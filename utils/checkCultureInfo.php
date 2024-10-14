<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
function checkCultureInfo()
{
    if (empty($_SESSION["cultureInfo"]) || !preg_match("/^[a-zA-Z]{2,4}(?:-[a-zA-Z]{2,4})*$/", $_SESSION["cultureInfo"])) {
        echo("<p class='warning-message'>Your cultureInfo is not valid. Please select a new one.</p>");
        exit;
    }
}

checkCultureInfo();