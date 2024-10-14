<?php

class CurlResponse
{
    public $responseBody;
    public int $httpCode;
    public string $curlError;

    /**
     * @param CurlHandle|resource $curl
     */
    public function __construct($curl)
    {
        $this->responseBody = curl_exec($curl);
        $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->curlError = curl_error($curl);
    }

    function toJsonString(): string
    {
        $object = [
            "responseBody" => json_decode($this->responseBody, true),
            "httpCode" => $this->httpCode,
            "curlError" => json_decode($this->curlError, true),
        ];
        return ("<pre>" . json_encode($object, JSON_PRETTY_PRINT) . "</pre>");
    }

    public function getJsonResponse()
    {
        return json_decode($this->responseBody, true);
    }


}