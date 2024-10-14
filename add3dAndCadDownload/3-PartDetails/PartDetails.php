<?php

class PartDetails
{
    public static function showPartDetails($apiReturn)
    {
        if (!empty($apiReturn)) {
            $decodedApiReturn = json_decode($apiReturn, true);
            echo("<pre>" . json_encode($decodedApiReturn, JSON_PRETTY_PRINT) . "</pre>");
        }

        if (!empty($decodedApiReturn) && gettype($decodedApiReturn) == "array") {
            $parameters = "";
            // parameters gathered here https://developers.traceparts.com/v2/reference/3d-viewer-implementation#required-parameters
            if (array_key_exists("classificationCode", $decodedApiReturn) && array_key_exists("partNumber", $decodedApiReturn)) {
                // ⚠️ 'classificationCode' is 'supplierID' here
                $parameters .= "supplierID=" . $decodedApiReturn["classificationCode"] . "&partNumber=" . $decodedApiReturn["partNumber"];
            } elseif (array_key_exists("partFamilyCode", $decodedApiReturn) && array_key_exists("selectionPath", $decodedApiReturn)) {
                // ⚠️ 'partFamilyCode' is 'product' here
                $parameters .= "product=" . $decodedApiReturn["partFamilyCode"] . "&selectionPath=" . $decodedApiReturn["selectionPath"];
            }

            if (!empty($parameters)) {
                $viewerUrl = "https://www.traceparts.com/els";
                // session is already started in checkToken.php
                $viewerUrl .= "/" . $_SESSION["elsid"];
                $viewerUrl .= "/" . $_SESSION["cultureInfo"];
                $viewerUrl .= "/api/viewer/3d?";
                $viewerUrl .= $parameters;

                echo("<iframe src='" . $viewerUrl . "'></iframe>");
                echo("<p class='success-message'><a href='../4-CadModel/showCadModel.php?" . $parameters . "'>Show and download this model</a></p>");
            }
        }
    }
}