<?php

namespace Saloodo\MailBundle\Event;

use Saloodo\MailBundle\Contract\MessageInterface;
use Symfony\Component\EventDispatcher\Event;

class EmailNotSentEvent extends Event
{
    const NAME = 'email.not_sent';

    private $email;
    private $errors;

    /**
     * EmailNotSentEvent constructor.
     * @param MessageInterface $email
     * @param array $errors
     */
    public function __construct(MessageInterface $email, array $errors = [])
    {
        $this->email = $email;
        $this->errors = $errors;
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
    public function getErrors(): array
    {
        return $this->errors;
    }
}
