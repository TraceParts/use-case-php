<?php
require_once "../../utils/checkToken.php";
require_once "../../utils/RootApiUrl.php";
?>

<?php
function decodeBoolValue(string $originalBool): ?bool
{
    $formatedBool = null;
    if (preg_match("/^true$/i", $originalBool)) {
        $formatedBool = true;
    } elseif (preg_match("/^false$/i", $originalBool)) {
        $formatedBool = false;
    }
    return $formatedBool;
}

?>
<?php
function createAUserAccount($token, $userEmail, $optionalParameters): CurlResponse
{
    $formatedParametersString = "";
    foreach ($optionalParameters as $parameterKey => $parameterValue) {
        if (!empty($parameterValue)) {
            $formatedParametersString .= ',';
            $formatedParametersString .= '"' . $parameterKey . '":';
            if (is_numeric($parameterValue)) {
                // parameterValue contains only numbers
                $formatedParametersString .= intval($parameterValue);
            } elseif (preg_match("/^true|false$/i", $parameterValue)) {
                $formatedParametersString .= decodeBoolValue($parameterValue);
            } else {
                $formatedParametersString .= '"' . $parameterValue . '"';
            }
        }
    }

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::ROOT_API_URL . "v2/Account/SignUp",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"userEmail\":\"" . $userEmail . "\"" . $formatedParametersString . "}",
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer " . $token,
            "content-type: application/*+json"
        ],
    ]);

    $result = new CurlResponse($curl);

    curl_close($curl);

    return $result;
}

?>

    <h1>Create a user account</h1>

    <form action="" method="post">
        <section>
            <h2>Required parameter :</h2>
            <label for="userEmail">User email :</label>
            <input type="email" name="userEmail" id="userEmail" title="Email address linked to the account."
                   placeholder="User email" required><br>
        </section>
        <section>
            <h2>Optional parameters :</h2>
            <label for='company'>Company :</label>
            <input type='text' name='company' id='company' title='User company.' placeholder='Company'/><br>
            <label for='country'>Country :</label>
            <input type='text' name='country' id='country' title='User country. ISO 3166-2 characters.'
                   placeholder='Country'/><br>
            <label for='name'>Name :</label>
            <input type='text' name='name' id='name' title='User last name.' placeholder='Name'/><br>
            <label for='fname'>First name :</label>
            <input type='text' name='fname' id='fname' title='User first name.' placeholder='First name'/><br>
            <label for='addr1'>Address 1 :</label>
            <input type='text' name='addr1' id='addr1' title='First field for the user address.'
                   placeholder='Address 1'/><br>
            <label for='addr2'>Address 2 :</label>
            <input type='text' name='addr2' id='addr2' title='Second field for the user address.'
                   placeholder='Address 2'/><br>
            <label for='addr3'>Address 3 :</label>
            <input type='text' name='addr3' id='addr3' title='Third field for the user address.'
                   placeholder='Address 3'/><br>
            <label for='city'>City :</label>
            <input type='text' name='city' id='city' title='User city.' placeholder='City'/><br>
            <label for='state'>State :</label>
            <input type='text' name='state' id='state' title='User state, for North America.' placeholder='State'/><br>
            <label for='zipCode'>Zip Code :</label>
            <input type='text' name='zipCode' id='zipCode' title='User zip code.' placeholder='Zip Code'/><br>
            <label for='phone'>Phone :</label>
            <input type='text' name='phone' id='phone' title='User phone number.' placeholder='Phone'/><br>
            <label for='fax'>Fax :</label>
            <input type='text' name='fax' id='fax' title='User FAX number.' placeholder='Fax'/><br>
            <p>TraceParts services information (Consent to receive information sent by TraceParts by email about
                TraceParts services.) :</p>
            <input type="radio" name="tpOptIn" id="tpOptInTrue" value="true">
            <label for="tpOptInTrue">Yes</label>
            <input type="radio" name="tpOptIn" id="tpOptInFalse" value="false" checked>
            <label for="tpOptInFalse">No</label><br>
            <p>Partners' services information (Consent to receive information sent by TraceParts by email about
                TraceParts’ partners’ services.) :</p>
            <input type="radio" name="partnersOptIn" id="partnersOptInTrue" value="true">
            <label for="partnersOptInTrue">Yes</label>
            <input type="radio" name="partnersOptIn" id="partnersOptInFalse" value="false" checked>
            <label for="partnersOptInFalse">No</label><br>
        </section>
        <button type="submit">Create account</button>
    </form>

<?php
if (!empty($_POST["userEmail"])) {
    $optionalParameters = [
        'company' => !empty($_POST["company"]) ? $_POST["company"] : null,
        'country' => !empty($_POST["country"]) ? $_POST["country"] : null,
        'name' => !empty($_POST["name"]) ? $_POST["name"] : null,
        'fname' => !empty($_POST["fname"]) ? $_POST["fname"] : null,
        'addr1' => !empty($_POST["addr1"]) ? $_POST["addr1"] : null,
        'addr2' => !empty($_POST["addr2"]) ? $_POST["addr2"] : null,
        'addr3' => !empty($_POST["addr3"]) ? $_POST["addr3"] : null,
        'city' => !empty($_POST["city"]) ? $_POST["city"] : null,
        'state' => !empty($_POST["state"]) ? $_POST["state"] : null,
        'zipCode' => !empty($_POST["zipCode"]) ? $_POST["zipCode"] : null,
        'phone' => !empty($_POST["phone"]) ? $_POST["phone"] : null,
        'fax' => !empty($_POST["fax"]) ? $_POST["fax"] : null,
        'tpOptIn' => !empty($_POST["tpOptIn"]),
        'partnersOptIn' => !empty($_POST["partnersOptIn"]),
    ];

    // session is already started in checkToken.php
    $apiReturn = createAUserAccount($_SESSION["token"], $_POST["userEmail"], $optionalParameters);

    //example of success return : 3fa85f64-5717-4562-b3fc-2c963f66afa6
    //todo check the possibles returns with someone who can delete users from DB
    if ($apiReturn->httpCode == 200) {
        echo("<p class='success-message'>Account " . $_POST["userEmail"] . " successfully created !</p>");
        echo $apiReturn->toJsonString();
    }
}
echo("<a href='generateToken.php'>Go back to generate token</a>");