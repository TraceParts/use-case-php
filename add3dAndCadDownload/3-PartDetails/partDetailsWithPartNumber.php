<?php
require_once "../../utils/RootApiUrl.php";
require_once "../../utils/checkToken.php";
require_once "../../utils/checkLogin.php";
?>

<?php
/**
 * @link https://developers.traceparts.com/v2/reference/get_v2-search-partnumber-availability
 */
function checkAvailabilityWithPartNumber(string $token, string $catalog, string $partNumber, ?bool $removeChar)
{
    $removeCharString = is_null($removeChar) ? "" : "&removeChar=" . ($removeChar ? "true" : "false");

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::ROOT_API_URL . "v2/Search/PartNumber/Availability?partNumber=" . $partNumber . "&catalog=" . $catalog . $removeCharString,
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

    <h1>Check a part availability with a part number.</h1>
    <form action="" method="get">
        <label for="partNumber"><u title="Part Number as you have in your own data.">Part number:</u></label>
        <input type="text" name="partNumber" id="partNumber" required><br>
        <label for="catalogLabel"><u title="Catalog label as you have in your own data.">Catalog label:</u></label>
        <input type="text" name="catalogLabel" id="catalogLabel" required><br>

        <p><u title='The following characters are not evaluating (" ", ".", "-", "/", "+")."'>Remove special characters
                (optional):</u></p>
        <input type="radio" name="removeChar" id="removeCharTrue" value="true"><label
                for="removeCharTrue">Yes</label><br>
        <input type="radio" name="removeChar" id="removeCharFalse" value="false"><label for="removeCharFalse">No</label><br>
        <input type="radio" name="removeChar" id="removeCharIgnore" value="" checked><label
                for="removeCharIgnore">Ignore</label><br>

        <button type="submit">Check with part number</button>
    </form>
    <p>You can also <a href="partDetailsWithYourOwnCode.php">check a part availability with your own code</a>.</p>


<?php
$apiReturn = null;
if (!empty($_GET["catalogLabel"]) && !empty($_GET["partNumber"])) {
    // Format removeChar to avoid human error
    $removeChar = null;
    if (!empty($_GET["removeChar"])) {
        $removeChar = $_GET["removeChar"] == "true" ? true : ($_GET["removeChar"] == "false" ? false : null);
    }
    // session is already started in checkToken.php
    $apiReturn = checkAvailabilityWithPartNumber($_SESSION["token"], $_GET["catalogLabel"], $_GET["partNumber"], $removeChar);
}
require_once "PartDetails.php";
PartDetails::showPartDetails($apiReturn);
?>