<?php

include "config.php";


if(isset($_SESSION['tokens'])){

    $tokens = $_SESSION['tokens'];
    $accessToken = unserialize($tokens["access_token"]);

    $intuitClient->apiDisconnect($accessToken);
    
}

session_destroy();
header("Location: index.php");  