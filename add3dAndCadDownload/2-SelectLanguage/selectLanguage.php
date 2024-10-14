<?php
require_once "../../utils/RootApiUrl.php";
require_once "../../utils/checkToken.php";
require_once "../../utils/checkLogin.php";
?>

<?php
/**
 * @link https://developers.traceparts.com/v2/reference/get_v2-supportedlanguages
 */
function getTokenLanguageList(string $token)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::ROOT_API_URL . "v2/SupportedLanguages",
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
        return "cURL Error #: " . $err;
    } else {
        return $response;
    }
}

?>

<h1>Select a language</h1>
<?php
if (!empty($_GET["cultureInfo"])) {
    // session is already started in checkToken.php
    $_SESSION["cultureInfo"] = $_GET["cultureInfo"];
    echo("<p class='success-message'>Your language has been selected ! (" . $_GET["cultureInfo"] . ")</p>");
    echo("<a href='../3-PartDetails/partDetailsWithPartNumber.php'>Check a part details</a>");
} else {
    // session is already started in checkToken.php
    $apiReturn = getTokenLanguageList($_SESSION["token"]);
    $decodedApiReturn = json_decode($apiReturn, true);
}
?>
<?php if (!empty($decodedApiReturn)): ?>
    <form action="" method="get">
        <label for="cultureInfo"><u title="Language for your 3D visualisations' menus">Select a language:</u></label>
        <select name="cultureInfo" id="cultureInfo" required>
            <?php foreach ($decodedApiReturn as $languageKey => $languageValue): ?>
                <option value='<?= $languageKey ?>'><?= $languageValue ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Select language</button>
    </form>
<?php endif; ?>
