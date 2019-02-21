<?php

namespace Saloodo\MailBundle\Contract;


use Saloodo\MailBundle\Party;

interface MessageInterface
{
    /**
     * @return Party
     */
    public function getSender(): Party;

    /**
     * @return Party
     */
    public function getRecipient(): Party;

    /**
     * @return string
     */
    public function getShortName(): string;

    /**
     * @return string
     */
    public function getTemplateKey(): string;

    /**
     * @return array
     */
    public function getPayload(): array;
}
