<?php
require_once "checkToken.php";
require_once "RootApiUrl.php";
require_once "CurlResponse.php";

function checkLogin()
{
    // session is already started in checkToken.php
    if (empty($_SESSION["elsid"])) {
        echo("<p class='warning-message'>Your EasyLink Solutions ID (ELS ID) is not valid. Please enter a new one in the 'Generate token' page.</p>");
        exit;
    }
    // session is already started in checkToken.php
    if (empty($_SESSION["userEmail"])) {
        echo("<p class='warning-message'>Your User Email is not valid. Please enter a new one in the 'Generate token' page.</p>");
        exit;
    } else {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => RootApiUrl::ROOT_API_URL . "v2/Account/CheckLogin/" . $_SESSION["userEmail"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Bearer " . $_SESSION["token"]
            ],
        ]);

        $result = new CurlResponse($curl);

        curl_close($curl);

        if ($result->httpCode != 200) {
            echo("<p class='warning-message'>Your User Email is not valid. Please enter a new one in the 'Generate token' page.</p>");
            exit;
        }
    }
}

checkLogin();