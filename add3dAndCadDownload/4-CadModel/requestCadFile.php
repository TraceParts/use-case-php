<?php
require_once "../../utils/RootApiUrl.php";
require_once "../../utils/CurlResponse.php";
?>

<?php
require_once "../../utils/checkToken.php";
require_once "../../utils/checkLogin.php";
?>

<?php
function requestACadFile(string $token, array $parameters)
{
    $formatedParametersString = "";
    foreach ($parameters as $parameterKey => $parameterValue) {
        if (!empty($parameterValue)) {
            $formatedParametersString .= '"' . $parameterKey . '":';
            if (is_numeric($parameterValue)) {
                // parameterValue contains only numbers
                $formatedParametersString .= intval($parameterValue);
            } else {
                $formatedParametersString .= '"' . $parameterValue . '"';
            }
            $formatedParametersString .= ',';
        }
    }

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::ROOT_API_URL . "v3/Product/cadRequest",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{" . $formatedParametersString . "}",
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer " . $token,
            "content-type: application/*+json"
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

<?php
// session is already started in checkToken.php
$userEmail = !empty($_SESSION['userEmail']) ? $_SESSION['userEmail'] : '';
$cultureInfo = !empty($_SESSION['cultureInfo']) ? $_SESSION['cultureInfo'] : '';
$cadFormatId = !empty($_POST['cadFormatId']) ? $_POST['cadFormatId'] : '';

$classificationCode = !empty($_POST['classificationCode']) ? $_POST['classificationCode'] : '';
$partNumber = !empty($_POST['partNumber']) ? $_POST['partNumber'] : '';
$partFamilyCode = !empty($_POST['partFamilyCode']) ? $_POST['partFamilyCode'] : '';
$selectionPath = !empty($_POST['selectionPath']) ? $_POST['selectionPath'] : '';

$cadDetailLevelId = !empty($_POST['cadDetailLevelId']) ? $_POST['cadDetailLevelId'] : '';
$languageId = !empty($_POST['languageId']) ? $_POST['languageId'] : '';
?>
    <h1>Request CAD file</h1>
    <form action="" method="post">
        <section>
            <p>Required parameters</p>
            <label for="userEmail"><u title="Email address associated to the CAD request event.">User email</u>
                <input readonly type="text" name="userEmail" id="userEmail"
                       value="<?= $userEmail ?>"><i
                        title="Read only parameter. Please go back to the associated page to modify it.">(readonly)</i>
            </label><br>
            <label for="cultureInfo"><u title="Language of the metadata related to the CAD file.">Culture info</u>
                <input readonly type="text" name="cultureInfo" id="cultureInfo"
                       value="<?= $cultureInfo ?>"><i
                        title="Read only parameter. Please go back to the associated page to modify it.">(readonly)</i>
            </label><br>
            <label for="cadFormatId"><u title="TraceParts ID of the CAD format.">CAD format ID</u>
                <input readonly type="text" name="cadFormatId" id="cadFormatId"
                       value="<?= $cadFormatId ?>"><i
                        title="Read only parameter. Please go back to the associated page to modify it.">(readonly)</i>
            </label><br>

            <label for="classificationCode"><u
                        title="TraceParts code of the classification (to use in combination with partNumber).">Classification
                    code (Supplier ID)</u>
                <input readonly type="text" name="classificationCode" id="classificationCode"
                       value="<?= $classificationCode ?>"><i
                        title="Read only parameter. Please go back to the associated page to modify it.">(readonly)</i>
            </label><br>
            <label for="partNumber"><u
                        title="Identifier of a product (to use in combination with classificationCode). Part number as stored in the TraceParts database.">Part
                    number</u>
                <input readonly type="text" name="partNumber" id="partNumber" value="<?= $partNumber ?>"><i
                        title="Read only parameter. Please go back to the associated page to modify it.">(readonly)</i>
            </label><br>
            <label for="partFamilyCode"><u title="TraceParts code of the product family.">Part family code (Product)</u>
                <input readonly type="text" name="partFamilyCode" id="partFamilyCode" value="<?= $partFamilyCode ?>"><i
                        title="Read only parameter. Please go back to the associated page to modify it.">(readonly)</i>
            </label><br>
            <label for="selectionPath"><u
                        title="Selected configuration (to use in combination with partFamilyCode. If not provided, the product is loaded with default configuration).">Selection
                    path</u>
                <input readonly type="text" name="selectionPath" id="selectionPath" value="<?= $selectionPath ?>"><i
                        title="Read only parameter. Please go back to the associated page to modify it.">(readonly)</i>
            </label><br>

            <label>
                <input type="checkbox" name="areInformationChecked" required>Information above are correct
            </label><br>
        </section>
        <section>
            <p>Optional parameters</p>
            <label for="cadDetailLevelId"><u
                        title="TraceParts ID of the optional detail level for the CAD model.">CAD detail level ID</u>
                <input type="text" name="cadDetailLevelId" id="cadDetailLevelId" value="<?= $cadDetailLevelId ?>">
            </label><br>
        </section>
        <section>
            <p>Deprecated parameters</p>
            <label for="languageId"><u
                        title="[DEPRECATED] TraceParts ID of the language (obsolete - please use cultureInfo).">Language
                    ID</u>
                <input type="text" name="languageId" id="languageId" value="<?= $languageId ?>">
            </label><br>
        </section>
        <button type="submit">Request CAD file</button>
    </form>

<?php
if (!empty($_POST['areInformationChecked'])) {
    $parameters = [
        'userEmail' => $userEmail,
        'cultureInfo' => $cultureInfo,
        'cadFormatId' => $cadFormatId,
        'classificationCode' => $classificationCode,
        'partNumber' => $partNumber,
        'partFamilyCode' => $partFamilyCode,
        'selectionPath' => $selectionPath,
        'cadDetailLevelId' => $cadDetailLevelId,
        'languageId' => $languageId,
    ];
    $response = requestACadFile($_SESSION['token'], $parameters);
}
?>
<?php if (isset($response) && $response->httpCode == 200):
    //response body should be the cadRequestId -> ID of the request provided by the cadRequest end point
    $cadRequestId = $response->responseBody; ?>
    <p class="success-message">Here is your CAD request ID : <?= $cadRequestId ?>. You will need it to get the CAD file
        url.</p>
    <p class="success-message">Your file is generating, you can now <a
                href="getCadFileUrl.php?cadRequestId=<?= $cadRequestId ?>">request the URL of this file to download
            it</a>.</p>
<?php endif;