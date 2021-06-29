<?php

namespace Saloodo\MailBundle;


use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\RejectedPromise;
use Saloodo\MailBundle\Contract\AdapterInterface;
use Saloodo\MailBundle\Contract\MessageInterface;
use Saloodo\MailBundle\Event\EmailNotSentEvent;
use Saloodo\MailBundle\Event\EmailSentEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EmailSender
{
    private $adapter;
    private $eventDispatcher;

    /**
     * Sender constructor.
     * @param AdapterInterface $adapter
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(AdapterInterface $adapter, EventDispatcherInterface $eventDispatcher)
    {
        $this->adapter = $adapter;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param MessageInterface $email
     * @return PromiseInterface
     */
    public function send(MessageInterface $email): PromiseInterface
    {
        $promise = $this->adapter->send($email);

        if ($promise) {
            $this->eventDispatcher->dispatch(EmailSentEvent::NAME, new EmailSentEvent($email));

            return $promise;
        }

        $this->eventDispatcher->dispatch(EmailNotSentEvent::NAME, new EmailNotSentEvent($email, $this->adapter->getErrors()));

        return new RejectedPromise('unknown error please check logs');
    }
}
