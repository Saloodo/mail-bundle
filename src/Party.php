<?php

namespace Saloodo\MailBundle;

class Party
{
    private $email;
    private $name;

    public function __construct(string $email, string $name)
    {
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Party
     */
    public function setEmail(string $email): Party
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Party
     */
    public function setName(string $name): Party
    {
        $this->name = $name;
        return $this;
    }
}
