<?php

namespace Saloodo\MailBundle\Adapters;


use Psr\Log\LoggerInterface;
use Saloodo\MailBundle\Contract\AdapterInterface;
use Saloodo\MailBundle\Contract\MessageInterface;

class LoggerAdapter implements AdapterInterface
{
    private $logger;
    private $errors = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function send(MessageInterface $email): bool
    {
        $this->logger->info("Email sent", $email);

        return true;
    }

    public function getErrors() : array
    {
        return $this->errors;
    }
}
