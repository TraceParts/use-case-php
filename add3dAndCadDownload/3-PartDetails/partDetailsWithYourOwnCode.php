<?php
require_once "../../utils/RootApiUrl.php";
require_once "../../utils/checkToken.php";
require_once "../../utils/checkLogin.php";
?>

<?php
/**
 * @link https://developers.traceparts.com/v2/reference/get_v2-search-yourowncode-availability
 */
function checkAvailabilityWithYourOwnCode(string $token, string $catalog, string $yourOwnCode)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::ROOT_API_URL . "v2/Search/YourOwnCode/Availability?yourOwnCode=" . $yourOwnCode . "&catalog=" . $catalog,
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

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return $response;
    }
}

?>

    <h1>Check a part availability with your own code.</h1>
    <form action="" method="get">
        <label for="yourOwnCode"><u
                    title="Non public string to call a configuration in the TraceParts database (i.e.: SKU, internal_code, Part_ID).">Your
                own code:</u></label>
        <input type="text" name="yourOwnCode" id="yourOwnCode" required><br>
        <label for="catalogLabel"><u title="Catalog label as you have in your own data.">Catalog label:</u></label>
        <input type="text" name="catalogLabel" id="catalogLabel" required><br>

        <button type="submit">Check with your own code</button>
    </form>
    <p>You can also <a href="partDetailsWithPartNumber.php">check a part availability with a part number</a>.</p>

<?php
$apiReturn = null;
if (!empty($_GET["catalogLabel"]) && !empty($_GET["yourOwnCode"])) {
    // session is already started in checkToken.php
    $apiReturn = checkAvailabilityWithYourOwnCode($_SESSION["token"], $_GET["catalogLabel"], $_GET["yourOwnCode"]);
}
require_once "PartDetails.php";
PartDetails::showPartDetails($apiReturn);
?>