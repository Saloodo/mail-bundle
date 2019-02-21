<?php

namespace Saloodo\MailBundle\Contract;


interface AdapterInterface
{
    /**
     * @param MessageInterface $email
     * @return bool
     */
    public function send(MessageInterface $email): bool;
}
