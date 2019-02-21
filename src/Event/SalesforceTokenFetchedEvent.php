<?php

namespace Saloodo\MailBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class TokenFetchedEvent extends Event
{
    const NAME = 'token.fetched';

}
