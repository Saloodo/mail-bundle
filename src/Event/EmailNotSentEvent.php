<?php

namespace Saloodo\MailBundle\Event;

use Saloodo\MailBundle\Contract\MessageInterface;
use Symfony\Component\EventDispatcher\Event;

class EmailNotSentEvent extends Event
{
    const NAME = 'email.not_sent';

    private $email;
    private $payload;

    /**
     * EmailNotSentEvent constructor.
     * @param MessageInterface $email
     * @param array $payload
     */
    public function __construct(MessageInterface $email, array $payload = [])
    {
        $this->email = $email;
        $this->payload = $payload;
    }

    /**
     * @return MessageInterface
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
