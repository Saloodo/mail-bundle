<?php

namespace Saloodo\MailBundle;


use GuzzleHttp\Promise\PromisorInterface;
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
     * @return PromisorInterface|null
     */
    public function send(MessageInterface $email): ?PromisorInterface
    {
        $promise = $this->adapter->send($email);
        if ($promise instanceof PromisorInterface) {
            $this->eventDispatcher->dispatch(EmailSentEvent::NAME, new EmailSentEvent($email));

            return $promise;
        }

        $this->eventDispatcher->dispatch(EmailNotSentEvent::NAME, new EmailNotSentEvent($email, $this->adapter->getErrors()));

        return null;
    }
}
