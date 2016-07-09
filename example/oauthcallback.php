<?php
include "config.php";

try{
    if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) && isset($_GET['realmId']) && isset($_GET['dataSource']) && $_SESSION['temporary_credentials']) {
        // Retrieve the temporary credentials we saved before
        $temporaryCredentials = unserialize($_SESSION['temporary_credentials']);
        // We will now retrieve token credentials from the server
        $tokenCredentials = $intuitClient->getTokenCredentials($temporaryCredentials, $_GET['oauth_token'],
            $_GET['oauth_verifier']);

        $qbData = [
            "access_token" => serialize($tokenCredentials),
            "realmId" => $_GET['realmId'],
        ];
        $_SESSION['tokens'] = $qbData;

        header("Location: index.php");
    }
}catch (\Exception $e){
    echo $e->getMessage();
}
