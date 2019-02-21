<?php

namespace Saloodo\MailBundle\Contract;


interface MessageInterface
{
    /**
     * @return array
     */
    public function getFrom(): array;

    /**
     * @return array
     */
    public function getTo(): array;

    /**
     * @return int
     */
    public function getExternalKey(): int;

    /**
     * @return string
     */
    public function getShortName(): string;
}
