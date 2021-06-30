<?php

namespace Saloodo\MailBundle\Adapters;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\RejectedPromise;
use Saloodo\MailBundle\Contract\AdapterInterface;
use Saloodo\MailBundle\Contract\MessageInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface as Cache;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

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
    public function send(MessageInterface $email): PromiseInterface
    {
        try {
            $tokenCache = $this->cache->getItem('salesforce_token');
        } catch (\Psr\Cache\InvalidArgumentException $exception) {
            $message = __METHOD__ . ' -- InvalidArgumentException:: ' . $exception->getMessage();
            $this->errors[] = $message;
            return new RejectedPromise($message);
        }

        if ($tokenCache->isHit()) {
            return $this->sendEmail($tokenCache->get(), $email);
        }

        $endpoint = sprintf('%s/v1/requestToken', $this->authApiUrl);

        $options = [
            'timeout' => 60, // in seconds
            'json' => [
                'clientId' => $this->id,
                'clientSecret' => $this->secret
            ],
        ];

        try {
            return $this->client->postAsync($endpoint, $options)->then(
                function (ResponseInterface $response) use ($tokenCache, $email) {
                    $response = json_decode($response->getBody()->getContents(), true);
                    if (!array_key_exists("accessToken", $response)) {
                        $message = __METHOD__ . ' -- No accessToken returned from Salesforce';
                        $this->errors[] = $message;
                        return new RejectedPromise($message);
                    }

                    $tokenCache->set($response['accessToken']);

                    // expire time will be received in the response, subtract 5 to avoid latencies issues
                    $tokenCache->expiresAfter($response['expiresIn'] - 5);

                    $accessToken =  $response['accessToken'];
                    return $this->sendEmail($accessToken, $email);
                }
            );
        } catch (GuzzleException $exception) {
            $message = __METHOD__ . ' -- GuzzleException:: ' . $exception->getMessage();
            $this->errors[] = $message;
            return new RejectedPromise($message);
        }
    }

    /**
     * @param string $accessToken
     * @param MessageInterface $email
     * @return PromiseInterface|null : ?PromiseInterface
     */
    private function sendEmail(string $accessToken, MessageInterface $email): PromiseInterface
    {
        $endpoint = sprintf('%s/messaging/v1/messageDefinitionSends/key:%s/send', $this->apiUrl, $email->getTemplateKey());

        if ($accessToken === '') return new RejectedPromise('Access Token missing');

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
            'timeout' => 60, // in seconds
            'json' => $this->getFullPayload($email, $email->getPayload()),
        ];

        try {
            return $this->client->postAsync($endpoint, $options)->then(
                function (ResponseInterface $response) {
                    $response = json_decode($response->getBody()->getContents(), true);
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
                },
                function (RequestException $exception) {
                    $message = __METHOD__ . ' -- GuzzleException:: ' . $exception->getMessage();
                    $this->errors[] = $message;
                    return new RejectedPromise($message);
                }
            );
        } catch (GuzzleException $exception) {
            $message = __METHOD__ . ' -- GuzzleException:: ' . $exception->getMessage();
            $this->errors[] = $message;
            return new RejectedPromise($message);
        }
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
                    'SubscriberKey' => $message->getRecipient()->getUniqueId() ?? $message->getRecipient()->getEmail(),
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

    public function getErrors(): array
    {
        return $this->errors;
    }
}
