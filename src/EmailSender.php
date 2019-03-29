<?php

namespace Saloodo\MailBundle;


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
     * @return bool
     */
    public function send(MessageInterface $email): bool
    {
        if ($this->adapter->send($email)) {
            $this->eventDispatcher->dispatch(EmailSentEvent::NAME, new EmailSentEvent($email));

            return true;
        }
        
        $this->eventDispatcher->dispatch(EmailNotSentEvent::NAME, new EmailNotSentEvent($email, $this->adapter->getErrors()));

        return false;
    }
}
