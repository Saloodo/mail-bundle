<?php

namespace Saloodo\MailBundle\Contract;


use GuzzleHttp\Promise\PromiseInterface;

interface AdapterInterface
{
    /**
     * @param MessageInterface $email
     * @return PromiseInterface
     */
    public function send(MessageInterface $email): PromiseInterface;

    /**
     * @return array
     */
    public function getErrors(): array;
}