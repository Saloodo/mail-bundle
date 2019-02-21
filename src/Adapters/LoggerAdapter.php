<?php

namespace Saloodo\MailBundle\Adapters;


use Psr\Log\LoggerInterface;
use Saloodo\MailBundle\Contract\AdapterInterface;
use Saloodo\MailBundle\Contract\MessageInterface;

class LoggerAdapter implements AdapterInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function send(MessageInterface $email): bool
    {
        $this->logger->info("email logged!");

        return true;
    }
}
