<?php

namespace Saloodo\MailBundle;


use GuzzleHttp\Promise\PromiseInterface;
use Saloodo\MailBundle\Contract\AdapterInterface;
use Saloodo\MailBundle\Contract\MessageInterface;

class EmailSender
{
    private $adapter;

    /**
     * Sender constructor.
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param MessageInterface $email
     * @return PromiseInterface
     */
    public function send(MessageInterface $email): PromiseInterface
    {
        return $this->adapter->send($email);
    }
}
