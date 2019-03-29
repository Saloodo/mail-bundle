<?php

namespace Saloodo\MailBundle\Adapters;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Saloodo\MailBundle\Contract\AdapterInterface;
use Saloodo\MailBundle\Contract\MessageInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface as Cache;

class SalesForceAdapter implements AdapterInterface
{
    private $client;
    private $id;
    private $secret;
    private $apiUrl;
    private $authApiUrl;
    private $cache;
    private $errors = [];

    private $async = false;

    /**
     * SalesForceAdapter constructor.
     * @param string $id
     * @param string $secret
     * @param string $tenant_subdomain
     * @param Cache $cache
     */
    public function __construct(string $id = '', string $secret = '', string $tenant_subdomain = '', Cache $cache)
    {
        $this->client = new Client();
        $this->id = $id;
        $this->secret = $secret;
        $this->apiUrl = sprintf('https://%s.rest.marketingcloudapis.com', $tenant_subdomain);
        $this->authApiUrl = sprintf('https:/%s.auth.marketingcloudapis.com', $tenant_subdomain);
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function send(MessageInterface $email): bool
    {
        $endpoint = sprintf('%s/messaging/v1/messageDefinitionSends/key:%s/send', $this->apiUrl, $email->getTemplateKey());

        $accessToken = $this->fetchAccessToken();
        if ($accessToken === '') return false;

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->fetchAccessToken(),
            ],
            'timeout' => 5, // in seconds
            'json' => $this->getFullPayload($email, $email->getPayload()),
        ];

        try {
            $response = json_decode($this->client->post($endpoint, $options)->getBody()->getContents(), true);
        } catch (GuzzleException $exception) {
            $this->errors[] = __METHOD__ . ' -- GuzzleException:: ' . $exception->getMessage();
            return false;
        }

        if ($response['responses'][0]['hasErrors'] === true) {
            $errors = $response['responses'][0]['messageErrors'];
            foreach ($errors as $error) {
                $this->errors[] = sprintf('%s -- SalesforceError:: Error Code: %s Error Message: %s', 
                    __METHOD__, 
                    $error['messageErrorCode'], 
                    $error['messageErrorStatus']
                );
            }
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    private function fetchAccessToken(): string
    {
        try {
            $tokenCache = $this->cache->getItem('salesforce_token');
        } catch (\Psr\Cache\InvalidArgumentException $exception) {
            $this->errors[] = __METHOD__ . ' -- InvalidArgumentException:: ' . $exception->getMessage();
            return '';
        }

        if ($tokenCache->isHit()) {
            return $tokenCache->get();
        }

        $endpoint = sprintf('%s/v1/requestToken', $this->authApiUrl);

        $options = [
            'timeout' => 5, // in seconds
            'json' => [
                'clientId' => $this->id,
                'clientSecret' => $this->secret
            ],
        ];

        try {
            $response = json_decode($this->client->post($endpoint, $options)->getBody()->getContents(), true);
        } catch (GuzzleException $exception) {
            $this->errors[] = __METHOD__ . ' -- GuzzleException:: ' . $exception->getMessage();
            return '';
        }

        if (!array_key_exists("accessToken", $response)) {
            $this->errors[] = __METHOD__ . ' -- No accessToken returned from Salesforce';
            return '';
        }

        $tokenCache->set($response['accessToken']);

        // expire time will be received in the response, subtract 5 to avoid latencies issues
        $tokenCache->expiresAfter($response['expiresIn'] - 5);

        return $response['accessToken'];
    }

    /**
     * Returns the payload to be passed to salesforce call.
     * @param MessageInterface $message
     * @param array $emailData The data that will populate templates on salesforce side
     * @return array
     */
    private function getFullPayload(MessageInterface $message, array $emailData)
    {
        $payload =
            [
                'To' => [
                    'Address' => $message->getRecipient()->getEmail(),
                    'SubscriberKey' => $message->getRecipient()->getEmail(),
                    'ContactAttributes' => [
                        'SubscriberAttributes' => $emailData
                    ]
                ],
                'From' => [
                    'Address' => $message->getSender()->getEmail(),
                    'Name' => $message->getSender()->getName(),
                ],
            ];

        if (!$this->async) {
            $payload['OPTIONS'] = ["RequestType" => "SYNC"];
        }

        return $payload;
    }

    public function getErrors() : array
    {
        return $this->errors;
    }
}
