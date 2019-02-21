<?php

namespace Saloodo\MailBundle;


use Saloodo\MailBundle\Contract\MessageInterface;

abstract class AbstractEmail implements MessageInterface
{
    const EXTERNAL_KEY = 0;

    private $to = [];
    private $from = [];
    protected $payload;

    /**
     * @return int
     */
    public function getExternalKey(): int
    {
        return static::EXTERNAL_KEY;
    }

    /**
     * @return array
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setTo(string $email, string $name = null)
    {
        $this->to[0] = $email;

        if ($name) {
            $this->to[1] = $name;
        } else {
            $this->to[1] = $email;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFrom(): array
    {
        return $this->from;
    }

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setFrom(string $email, string $name = null)
    {
        $this->from[0] = $email;

        if ($name) {
            $this->from[1] = $name;
        } else {
            $this->from[1] = $email;
        }

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
     * @param mixed $payload
     * @return GenericEmail
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @param mixed $payload
     * @return GenericEmail
     */
    public function addToPayload($key, $value)
    {
        $this->payload[$key] = $value;
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
