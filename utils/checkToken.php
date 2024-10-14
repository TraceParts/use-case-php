<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
function checkToken()
{
    if (empty($_SESSION["token"]) || !preg_match("/^[A-Za-z0-9_-]{2,}(?:\.[A-Za-z0-9_-]{2,}){2}$/", $_SESSION["token"])) {
        echo("<p class='warning-message'>Your token is not valid. Please generate a new one.</p>");
        exit;
    }
}

checkToken();
