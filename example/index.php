<?php
include "config.php";

if(isset($_SESSION['tokens'])){

    $tokens = $_SESSION['tokens'];
    $accessToken = unserialize($tokens["access_token"]);

    $userDetails = $intuitClient->getUserInfo($accessToken);


    echo "<pre>".print_r($userDetails, true)."</pre><br><br>";

    echo "<a href='disconnect.php'>Disconnect</a>";
    
}else{

    $temporaryCredentials = $intuitClient->getTemporaryCredentials(); // Get Request Token
    $_SESSION['temporary_credentials'] = serialize($temporaryCredentials);
    $authurl = $intuitClient->getAuthorizationUrl($temporaryCredentials);
    echo "<a href='".$authurl."'>Connect to Quickbooks Account</a>";

}