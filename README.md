# Vizad
OAuth 1.0 Client for Intuit using The PHP League OAuth1-Client


define('QB_CUSTOMER_ID', 'qyprdRYZP6xkgNyBje3oieXX1XdsqF');
define('QB_CUSTOMER_SECRET', 'yZZl2YnMa84kiVg1VO8PVilrWmP75OGikwz0uyYT');


$intuitClient = new Vizad\Intuit([
            'identifier'   => QB_CUSTOMER_ID,
            'secret'       => QB_CUSTOMER_SECRET,
            'callback_uri' => "http://localhost/tests/intuitComponent/oauthcallback.php",
            'sandboxMode' => true
        ]);

$temporaryCredentials = $intuitClient->getTemporaryCredentials();
$_SESSION['temporary_credentials'] = serialize($temporaryCredentials);
$authurl = $intuitClient->getAuthorizationUrl($temporaryCredentials);