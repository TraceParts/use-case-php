<?php
require_once "../../utils/RootApiUrl.php";
session_start();
?>

<?php
/**
 * ⚠️This token gives direct access to our API with the associated credentials. Never let someone other that the owner of this credentials get this token.⚠️
 * @link https://developers.traceparts.com/v2/reference/post_v2-requesttoken
 */
function generateToken(string $tenantUid, string $apiKey)
{

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::ROOT_API_URL . "v2/RequestToken",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"tenantUid\":\"" . $tenantUid . "\",\"apiKey\":\"" . $apiKey . "\"}",
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "content-type: application/*+json"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #: " . $err;
    } else {
        return $response;
    }
}

?>

<?php
function checkTenantUidFormat(string $tenantUid)
{
    return preg_match("/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/", $tenantUid);
}

function checkApiKeyFormat(string $apiKey)
{
    return preg_match("/^\w{4,50}$/", $apiKey);
}

?>

    <h1>Generate a token.</h1>
    <form action="" method="post">
        <p>You need to enter your Tenant Uid and your API key to generate the token.</p>

        <?php
        if (!empty($_SESSION["token"])):?>
            <p class='warning-message'><strong>You already have a token ! You can still generate a new one if you want by filling the form below.</strong></p>
        <p>You can also simply <a href="../2-SelectLanguage/selectLanguage.php">go to the next step (Select language)</a>.</p>
        <?php endif; ?>

        <label for="tenantUid"><u title="Given in the email that gives you access to the API">Tenant Unique
                ID:</u></label>
        <input type="text" name="tenantUid" id="tenantUid" required placeholder="Tenant Uid"
               pattern="^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$"
               title="Your Tenant Uid should look like this : '00000000-0000-0000-0000-000000000000'"><br>
        <?php
        if (!empty($_POST["tenantUid"])) {
            // check if the Tenant Uid has a valid format
            if (!checkTenantUidFormat($_POST["tenantUid"])) {
                // the Tenant Uid HAS NOT a valid format
                echo("<p class='warning-message'>The Tenant Uid format is not valid. Reminder, it should look like this : '00000000-0000-0000-0000-000000000000'.</p><br>");
            } else {
                // the Tenant Uid has a valid format
                $tenantUid = $_POST["tenantUid"];
            }
        }
        ?>

        <label for="apiKey"><u title="Given in the email that gives you access to the API">API key:</u></label>
        <input type="text" name="apiKey" id="apiKey" required placeholder="API key" minlength="4" maxlength="50"
               title="Your API key should have a length between 4 and 50 characters"><br>
        <?php
        if (!empty($_POST["apiKey"])) {
            // check if the API key has a valid format
            if (!checkApiKeyFormat($_POST["apiKey"])) {
                // the API key HAS NOT a valid format
                echo("<p class='warning-message'>The API Key format is not valid. Reminder, it should have a length between 4 and 50 characters.</p><br>");
            } else {
                // the API key has a valid format
                $apiKey = $_POST["apiKey"];
            }
        }
        ?>

        <p>The EasyLink Solutions ID (ELS ID) will be used later for the 3D visualisation.</p>
        <label for="elsid"><u title="Given in the email that gives you access to the API">EasyLink Solutions ID (ELS
                ID):</u></label>
        <input type="text" name="elsid" id="elsid" required placeholder="ELS ID"><br>
        <?php
        if (!empty($_POST["elsid"])) {
            $elsid = $_POST["elsid"];
        }
        ?>

        <p>This User email will be used to request the CAD files. Make sure it is valid and have the right to request
            CAD files.</p>
        <label for="userEmail"><u title="Email address associated to the CAD request event.">User email
                address:</u></label>
        <input type="email" name="userEmail" id="userEmail" required placeholder="User email"><br>
        <p>If you don't have one, you can <a href="createAUserAccount.php">create a new account here</a>.</p>
        <?php
        if (!empty($_POST["userEmail"])) {
            $userEmail = $_POST["userEmail"];
        }
        ?>

        <button type="submit">Generate token</button>
    </form>

<?php if (!empty($tenantUid) && !empty($apiKey) && !empty($elsid) && !empty($userEmail)) {
    $apiReturn = generateToken($tenantUid, $apiKey);
    $decodedReturnedJson = json_decode($apiReturn);
    // check if the JSON string contains the token
    if (!empty($decodedReturnedJson) && property_exists($decodedReturnedJson, "token")) {
        // the JSON string contains the token
        $_SESSION["token"] = $decodedReturnedJson->token;
        require_once "../../utils/checkToken.php";
        echo("<p class='success-message'>Token successfully generated !</p>");
        $_SESSION["elsid"] = $elsid;
        $_SESSION["userEmail"] = $userEmail;
        require_once "../../utils/checkLogin.php";
        echo("<p class='success-message'>ELSID saved successfully.</p>");
        echo("<p class='success-message'>User email saved successfully.</p>");

        echo("<a href='../2-SelectLanguage/selectLanguage.php'>Select a language for this token</a>");
    } else {
        echo("<pre class='error-message'>" . json_encode($decodedReturnedJson, JSON_PRETTY_PRINT) . "</pre>");
    }
}
?>