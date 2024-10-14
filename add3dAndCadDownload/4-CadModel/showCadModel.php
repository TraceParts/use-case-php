<?php
require_once "../../utils/RootApiUrl.php";
require_once "../../utils/CurlResponse.php";
?>

<?php
require_once "../../utils/checkToken.php";
require_once "../../utils/checkLogin.php";
?>

<?php
function getCadFormatsList(string $token, string $cultureInfo, string $formatedParametersString): CurlResponse
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::ROOT_API_URL . "v3/Product/CadDataAvailability?cultureInfo=" . $cultureInfo . "&" . $formatedParametersString,
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

?>
<h1>CAD model</h1>
<?php
$parameters = "";
if (!empty($_GET["supplierID"]) && !empty($_GET["partNumber"])) {
    $parameters .= "supplierID=" . $_GET["supplierID"] . "&partNumber=" . $_GET["partNumber"];
} elseif (!empty($_GET["product"]) && !empty($_GET["selectionPath"])) {
    $parameters .= "product=" . $_GET["product"] . "&selectionPath=" . $_GET["selectionPath"];
}
if (!empty($parameters)) {
    $viewerUrl = "https://www.traceparts.com/els";
    // session is already started in checkToken.php
    $viewerUrl .= "/" . $_SESSION["elsid"];
    $viewerUrl .= "/" . $_SESSION["cultureInfo"];
    $viewerUrl .= "/api/viewer/3d?";
    $viewerUrl .= $parameters;

    echo "<p><a href='" . $viewerUrl . "' target='_blank'>Viewer URL (New tab)</a></p>";
    echo("<iframe src='" . $viewerUrl . "'></iframe>");
}
?>
<?php
$formatedParametersString = "";
if (!empty($_GET["supplierID"]) && !empty($_GET["partNumber"])) {
    // ⚠️ 'supplierID' is 'classificationCode' here
    $formatedParametersString .= "classificationCode=" . $_GET["supplierID"] . "&partNumber=" . $_GET["partNumber"];
} elseif (!empty($_GET["product"]) && !empty($_GET["selectionPath"])) {
    // ⚠️ 'product' is 'partFamilyCode' here
    $formatedParametersString .= "partFamilyCode=" . $_GET["product"] . "&selectionPath=" . $_GET["selectionPath"];
}

$response = getCadFormatsList($_SESSION["token"], $_SESSION["cultureInfo"], $formatedParametersString);
?>
<?php if ($response->httpCode == 200 && array_key_exists("cadFormatList", $response->getJsonResponse())): ?>
    <form action="requestCadFile.php" method="post">
        <?php if (!empty($_GET["supplierID"]) && !empty($_GET["partNumber"])): // ⚠️ 'supplierID' is 'classificationCode' here ?>
            <label for="classificationCode">Classification code (Supplier ID)
                <input readonly type="text" name="classificationCode" id="classificationCode"
                       value="<?= $_GET["supplierID"] // ⚠️ 'supplierID' is 'classificationCode' here  ?>"><i
                        title="Read only parameter. Please go back to the associated page to modify it.">(readonly)</i>
            </label><br>
            <label for="partNumber">Part number
                <input readonly type="text" name="partNumber" id="partNumber"
                       value="<?= $_GET["partNumber"] ?>"><i
                        title="Read only parameter. Please go back to the associated page to modify it.">(readonly)</i>
            </label><br>
        <?php elseif (!empty($_GET["product"]) && !empty($_GET["selectionPath"])): ?>
            <label for="partFamilyCode">Part family code (Product)
                <input readonly type="text" name="partFamilyCode" id="partFamilyCode"
                       value="<?= $_GET["product"] // ⚠️ 'product' is 'partFamilyCode' here  ?>"><i
                        title="Read only parameter. Please go back to the associated page to modify it.">(readonly)</i>
            </label><br>
            <label for="selectionPath">Selection path
                <input readonly type="text" name="selectionPath" id="selectionPath"
                       value="<?= $_GET["selectionPath"] ?>"><i
                        title="Read only parameter. Please go back to the associated page to modify it.">(readonly)</i>
            </label><br>
        <?php endif; ?>

        <label for="cadFormatId"><u title="TraceParts ID of the CAD format.">Select a CAD format:</u>
            <select name="cadFormatId" id="cadFormatId" required>
                <?php foreach ($response->getJsonResponse()["cadFormatList"] as $cadFormat): ?>
                    <option value='<?= $cadFormat["cadFormatId"] ?>'><?= $cadFormat["cadFormatName"] ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Get this CAD file</button>
    </form>
<?php endif; ?>

