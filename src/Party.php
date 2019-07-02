<?php

namespace Saloodo\MailBundle;

class Party
{
    /** @var string */
    private $email;

    /** @var string */
    private $name;

    /** @var string|null */
    private $uniqueId;

    public function __construct(string $email, string $name = null, string $uniqueId = null)
    {
        $this->email = $email;
        $this->name = $name;
        $this->uniqueId = $uniqueId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getUniqueId(): ?string
    {
        return $this->uniqueId;
    }
}
