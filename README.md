
﻿
# vimexx-php-sdk
Een (onofficiële) Vimexx API client voor php.

## Installatie
Via composer
> composer require mrjoachim/vimexx-php-sdk

##  Configuratie
Om te beginnen heb je de volgende gegevens nodig vanuit jouw vimexx klantenpaneel:

- Client ID
- Client Key
- E-mailadres
- Wachtwoord

De client id en key kun je hier vinden: https://my.vimexx.nl/api.
Initialiseer de SDK zoals hieronder:
```php
<?php
use MrJoachim\VimexxPhpSdk\VimexxSDK;    

require('vendor/autoload.php');

$vimexx = new  VimexxSDK(000, "client_key", "jouw@e-mailadres.nl", "jouwwachtwoord");
```
De testmodus schakel je zo in:
```php
$vimexx->enableTestMode();
```
## API
### Domein registreren
Let op: jouw online vimexx wallet moet wel genoeg saldo hebben.
Een domein registreer je zo:
```php
$vimexx->registerDomain("test", "nl");
```

### Domein informatie ophalen

```php <?php
use MrJoachim\VimexxPhpSdk\VimexxSDK;    

require('vendor/autoload.php');

$vimexx = new  VimexxSDK(000, "client_key", "jouw@e-mailadres.nl", "jouwwachtwoord");
$domein = $vimexx->getDomain("test", "nl");
$domein->getName(); //Je krijgt "test.nl" terug.
$domein->getExpirationDate();
$domein->hasDNSManagement();
$domein->isTransferredAway();
$domein->hasAutoRenewEnabled();

$domein->getNameservers();

foreach($domein->getDNS() as $dnsRecord){
    $dnsRecord->getType();
    $dnsRecord->getHostname();
    $dnsRecord->getContent();
    $dnsRecord->getPrio();
}
```

### Domein updaten

```php <?php
use MrJoachim\VimexxPhpSdk\Entities\DNSRecord;
use MrJoachim\VimexxPhpSdk\VimexxSDK;    

require('vendor/autoload.php');

$vimexx = new  VimexxSDK(000, "client_key", "jouw@e-mailadres.nl", "jouwwachtwoord");
$domein = $vimexx->getDomain("test", "nl");

$domein->setNameservers("ns.zxcs.nl", "ns.zxcs.be", "ns.zxcs.eu");

$records = [];
$records[] = DNSRecord::createARecord("test", "waarde");
$records[] = DNSRecord::createAAAARecord("test", "waarde");
$records[] = DNSRecord::createCNameRecord("test", "waarde");
$records[] = DNSRecord::createMXRecord("test", 10, "waarde");
$records[] = DNSRecord::createTXTRecord("test", "waarde");
$domein->setDNS($records);
```

