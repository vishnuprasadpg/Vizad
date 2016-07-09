# OAuth 1.0 Client for Intuit using The PHP League OAuth1-Client

## Install Via Composer 

```bash

composer require vizad/intuit

```

## Usage

```php

define('QB_CUSTOMER_ID', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('QB_CUSTOMER_SECRET', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');


$intuitClient = new Vizad\Intuit([
            'identifier'   => QB_CUSTOMER_ID,
            'secret'       => QB_CUSTOMER_SECRET,
            'callback_uri' => "CALLBACK_URI",
            'sandboxMode' => true
        ]);

$temporaryCredentials = $intuitClient->getTemporaryCredentials();
$_SESSION['temporary_credentials'] = serialize($temporaryCredentials);
$authurl = $intuitClient->getAuthorizationUrl($temporaryCredentials);

echo "<a href='".$authurl."'>Connect to Quickbooks Account</a>";

```

#### Check out example directory for sample implementation