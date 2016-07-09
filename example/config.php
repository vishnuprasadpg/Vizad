<?php
session_start();

include "vendor/autoload.php";


define('QB_CUSTOMER_ID', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('QB_CUSTOMER_SECRET', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');


$intuitClient = new Vizad\Intuit([
            'identifier'   => QB_CUSTOMER_ID,
            'secret'       => QB_CUSTOMER_SECRET,
            'callback_uri' => "http://localhost/oauthcallback.php",
            'sandboxMode' => true
        ]);