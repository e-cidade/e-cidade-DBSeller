<?php

namespace App\Domain\Tributario\ISSQN\RequestManagers\Redesim;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RedesimRequestManager
{
    const RETRIEVE_ESTABLISHMENTS_ENDPOINT = "/recuperaEstabelecimentos";

    const CONFIRM_RECEIPT_ENDPOINT = "/informaRecebimento";

    const CONFIRM_RESPONSE_ENDPOINT = "/confirmaRespostaOrgao";

    public $response = [];

    public $rawResponse;

    public $success;

    private $endpoint;

    private $headers;

    private $errorsLoggingEnabled = false;

    public function __construct($endpoint, $headers = [])
    {
        $this->endpoint = $endpoint;
        $this->headers = $headers;
    }

    public function post($requestBody)
    {
        try {
            $httpClient = new Client();
            $httpResponse = $httpClient->post(
                $this->buildUrl(),
                [
                    "json" => $requestBody,
                    "headers" => $this->buildHeaders()
                ]
            );

            if ($this->errorsLoggingEnabled && !$this->httpStatusCodeAcceptable($httpResponse->getStatusCode())) {
                Log::warning("Código HTTP diferente do esperado.");
            }

            $this->processHttpResponse($httpResponse);
            $this->success = true;
        } catch (\Exception $exception) {
            if ($this->errorsLoggingEnabled) {
                Log::error($exception);
            }

            $this->success = false;
        }
    }

    public function enableErrorsLogging($logFileName)
    {
        $this->errorsLoggingEnabled = true;
    }

    private function processHttpResponse($httpResponse)
    {
        $this->response = json_decode($httpResponse->getBody());
        $this->rawResponse = $httpResponse->getBody()->__toString();
    }

    private function buildUrl()
    {
        $url = env("REDESIM_BASE_URL");

        return "{$url}{$this->endpoint}";
    }

    private function buildHeaders()
    {
        $headers = [
            "Content-Type" => "application/json",
            "Accept" => "application/json"
        ];

        if ($this->headers) {
            $headers = array_merge($headers, $this->headers);
        }

        return $headers;
    }

    private function httpStatusCodeAcceptable($statusCode)
    {
        return in_array($statusCode, [Response::HTTP_OK]);
    }
}
