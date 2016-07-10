<?php 

include 'config.php';


if(isset($_SESSION['tokens'])){
	
	try {

	    $tokens = $_SESSION['tokens'];
	    $accessToken = unserialize($tokens["access_token"]);
	    $realmId = $tokens["realmId"];
		    
		$accounts = [];

	    $batchEntries = [
	        "BatchItemRequest" => [
	            [
	                "bId" => md5(rand(999, 99999)),
	                "Query" => "select * from Account"
	            ]
	        ]
	    ];
	    $apiUrl = "v3/company/" . $realmId . "/batch";
	    $batchEntries = json_encode($batchEntries);
	    $response = $intuitClient->api($accessToken, $apiUrl, "POST", $batchEntries, ["Content-Type"=>"application/json"]);
	    if (isset($response["BatchItemResponse"]["QueryResponse"]["Account"])) {
	        $accounts = $response["BatchItemResponse"]["QueryResponse"]["Account"];
        	echo "<ul>";
        	echo "<li>Account Name - Account Type" . "</li>";
	        foreach ($accounts as $account) {
	        	echo "<li>".$account["Name"] . " - " . $account["AccountType"] . "</li>";
	        }
        	echo "</ul>";
	    }
	} catch (\Exception $e) {
	    $message = $e->getMessage();
	    if (isset($e->responseBody)) {
	        $error = json_decode($e->responseBody, true);
	        $message = "Error: ";
	        if (isset($error["Fault"]["Error"])) {
	            foreach ($error["Fault"]["Error"] as $err) {
	                $message .= implode(", ", $err);
	            }
	        }
	    }
	    echo $message;
	}
}else{
	session_destroy();
	header("Location: index.php");  
}