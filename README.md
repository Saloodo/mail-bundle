# Saloodo Mail Bundle
Saloodo Mail Bundle is a small symfony bundle that provides a simple interface for e-mail sending.

It currently supports only salesforce.

## Instalation

### Require the package

``` bash
composer require saloodo/mail-bundle
```

### Add the Bundle to AppKernel

```
    new Saloodo\MailBundle\SaloodoMailBundle(),
```

### Define the configuration

```yaml
saloodo_mail:
    cache_driver: 'app_general_cache' # will be used to cache access token
    adapter: 'salesforce'
    salesforce:
        id: 'salesforce_id'
        secret: 'salesforce_secret'
        tenant_subdomain: 'salesforce_tenant_subdomain'
```

## Sending an e-mail



```php

<?php

//AppBundle/Mail/AccountApprovedEmail.php
namespace AppBundle\Mail;

use Saloodo\MailBundle\AbstractEmail;

class AccountApprovedEmail extends AbstractEmail
{
    const EXTERNAL_KEY = 11378;

    public function setConfimationLink($confirmationLink): void
    {
        $this->addToPayload("confirmation_link", $confirmationLink);
    }
}


//AppBundle/Controller/SomeController.php
namespace AppBundle\Controller;

use Saloodo\MailBundle\Sender;

class SomeController
{
    protected $sender;
    
    public function __construct(Sender $sender) 
    {
        $this->sender = $sender;   
    }
    
    protected function doAction(UserInterface $user)
    {
        $email = new AccountApprovedEmail();
        
        $email->setTo($user->getEmail(), $user->getName());
        $email->setConfimationLink("https://www.google.com");

        $emailSender->send($email);
    }
}
```


## Listen to the events

Saloodo Mail Bundle dispatches events out of the box. You can listen or subscribe to these events.

```
email.not_sent
email.sent
```


