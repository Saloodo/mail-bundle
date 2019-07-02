<?php

namespace Saloodo\MailBundle;


use Saloodo\MailBundle\Contract\MessageInterface;

abstract class AbstractEmail implements MessageInterface
{
    private $recipient;
    private $sender;
    protected $payload = [];

    /**
     * @return Party
     */
    public function getRecipient(): Party
    {
        return $this->recipient;
    }

    /**
     * @param string $email
     * @param string|null $name
     * @param string|null $uniqueId
     * @return $this
     */
    public function setRecipient(string $email, string $name = null, string $uniqueId = null)
    {
        $this->recipient = new Party($email, $name, $uniqueId);

        return $this;
    }

    /**
     * @return Party
     */
    public function getSender(): Party
    {
        return $this->sender;
    }

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setSender(string $email, string $name = null)
    {
        $this->sender = new Party($email, $name);

        return $this;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param $payload
     * @return $this
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function addToPayload($key, $value)
    {
        $this->payload[$key] = $value;
        return $this;
    }

    /**
     * @param array $array
     * @return $this
     */
    public function addArrayToPayload(array $array)
    {
        $this->payload = array_merge($this->payload, $array);
        return $this;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function getShortName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}
