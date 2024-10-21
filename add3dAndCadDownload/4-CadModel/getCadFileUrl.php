<?php
require_once "../../utils/RootApiUrl.php";
require_once "../../utils/CurlResponse.php";
require_once "../../utils/checkToken.php";
require_once "../../utils/checkLogin.php";
?>

<?php
function getACadFileUrl(string $token, int $cadRequestId): CurlResponse
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::ROOT_API_URL . "v2/Product/cadFileUrl?cadRequestId=" . $cadRequestId,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer " . $token
        ],
    ]);

    $result = new CurlResponse($curl);

    curl_close($curl);

    return $result;
}

function loopGetACadFileUrlRequest(string $token, int $cadRequestId): string
{
    $timeout = 10; // in minutes
    $interval = 2; // in seconds

    $finalResult = "Timeout reached (" . $timeout . " minutes with " . $interval . " seconds interval). Your model couldn't be generated.";

    $nbrOfIterations = $timeout * 60 / $interval;
    for ($i = 0; $i < $nbrOfIterations; $i++) {
        $apiReturn = getACadFileUrl($token, $cadRequestId);

        // code 204 means the cad file is generating, and you must wait until it is fully generated or an error occurs
       if ($apiReturn->httpCode == 204) {    
           sleep($interval);} 
       else {    
           $finalResult = $apiReturn;    
           break;
       }
    }

    return $finalResult;
}

?>

<?php
$cadRequestId = 0;
if (!empty($_GET["cadRequestId"]) && is_numeric($_GET["cadRequestId"])) {
    $cadRequestId = intval($_GET["cadRequestId"]);
}
?>

<?php
$showLoopFormField = false;
if (!empty($cadRequestId)) {
    if (empty($_GET['loopRequest'])) {
        // session is already started in checkToken.php
        $apiReturn = getACadFileUrl($_SESSION['token'], $cadRequestId);
        if ($apiReturn->httpCode == 204) {
            $showLoopFormField = true;
        }
    } else {
        $apiReturn = loopGetACadFileUrlRequest($_SESSION['token'], $cadRequestId);
    }
} ?>

    <h1>Get CAD file url</h1>
    <form action="" method="get">
        <label for="cadRequestId"><u title="ID of the request provided by the cadRequest end point">CAD request ID</u>
            <input type="number" name="cadRequestId" id="cadRequestId" placeholder="CAD request ID" required>
        </label><br>
        <?php if ($showLoopFormField): ?>
            <p class="success-message">An HTTP code 204 means the file is generating, and you must wait until it is
                fully generated or an error occurs.</p>
            <p>To check if the file is ready you must repeat the request. You can loop it by checking the option
                below.</p>
            <label>
                <input type="checkbox" name="loopRequest">Loop this request to get a definitive answer ? (Can take a
                while)
            </label><br>
        <?php endif; ?>
        <button type="submit">Get url</button>
    </form>

<?php
if (!empty($apiReturn)) {
    if ($apiReturn instanceof CurlResponse) {
        echo $apiReturn->toJsonString();
    } else {
        echo $apiReturn;
    }
}
