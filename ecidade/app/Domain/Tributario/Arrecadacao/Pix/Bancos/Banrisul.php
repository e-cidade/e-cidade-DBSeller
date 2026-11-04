<?php

namespace App\Domain\Tributario\Arrecadacao\Pix\Bancos;

use App\Domain\Tributario\Arrecadacao\Pix\Adapter\PixBanco;
use App\Domain\Tributario\Arrecadacao\Pix\PixInfo;
use GuzzleHttp\Client;

/**
 *
 */
class Banrisul extends PixInfo implements PixBanco
{
    /**
     * @var string
     */
    const BANK_CODE = "041";

    /**
     * @var array
     */
    const BASE_URL = ["production" => "https://api.banrisul.com.br", "sandbox" => "https://api-h.banrisul.com.br"];

    /**
     * @var string
     */
    const AUTH_ENDPOINT = "/auth/oauth/v2/token";

    /**
     * @var string
     */
    const GENERATE_PIX_ENDPOINT = "/pix/api/cobv/";

    /**
     * @var string
     */
    private static $accessToken;

    /**
     * @var string
     */
    private $txId;

    public function __construct()
    {
        parent::__construct(Banrisul::BANK_CODE, Banrisul::BASE_URL, Banrisul::BASE_URL);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function gerarPix()
    {
        if (!$this->canGeneratePix()) {
            return;
        }

        $this->retrieveAccessToken();

        $this->registerPix();
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function retrieveAccessToken()
    {
        try {
            $httpClient = new Client();
            $httpResponse = $httpClient->post(
                $this->getBaseAuthUrl().Banrisul::AUTH_ENDPOINT,
                [
                    "form_params" => ["grant_type" => "client_credentials"],
                    "headers" => [
                        "Content-Type" => "application/x-www-form-urlencoded",
                        "Accept" => "application/json",
                        "Authorization" => "Basic " . base64_encode(
                            "{$this->getAppUsername()}:{$this->getAppPassword()}"
                        )
                    ]
                ]
            );

            if ($httpResponse->getStatusCode() != 200) {
                throw new \Exception("Não foi possível obter o token de acesso a API Pix do Banrisul.");
            }

            $responseBody = $this->parseHttpResponseBody($httpResponse);
            
            Banrisul::$accessToken = $responseBody->access_token;
        } catch (\Exception $exception) {
            $this->logger(true, "Não foi possível obter o token de acesso a API Pix do Banrisul.");
            $this->logger(true, $exception->getMessage());
            $this->logger(true, $exception->getTraceAsString());

            throw new \Exception("Não foi possível obter o token de acesso a API Pix do Banrisul.");
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function registerPix()
    {
        if (!$this->canGeneratePix()) {
            return;
        }

        try {
            $this->buildTxId();

            $httpClient = new Client();
            $httpResponse = $httpClient->put(
                $this->getBaseAuthUrl().Banrisul::GENERATE_PIX_ENDPOINT.$this->txId,
                [
                    "json" => $this->buildRequestBody(),
                    "headers" => [
                        "Content-Type" => "application/json",
                        "Accept" => "application/json",
                        "Authorization" => "Bearer ".Banrisul::$accessToken
                    ]
                ]
            );

            if ($httpResponse->getStatusCode() != 201) {
                throw new \Exception("Não foi possível gerar o Pix na API do Banrisul.");
            }

            $responseBody = $this->parseHttpResponseBody($httpResponse);

            $this->savePixTransactionInfo(
                $responseBody->calendario->criacao,
                $responseBody->status,
                $this->txId,
                $responseBody->revisao,
                $responseBody->loc->location,
                $responseBody->pixCopiaECola,
                Banrisul::BANK_CODE
            );
        } catch (\Exception $exception) {
            $this->logger(false, "Não foi possível gerar o Pix na API do Banrisul.");
            $this->logger(false, $exception->getMessage());
            $this->logger(false, $exception->getTraceAsString());
        }
    }

    /**
     * @return array
     */
    private function buildRequestBody()
    {
        $body = [];
        $body["chave"] = $this->getPixKey();

        $body["valor"] = [
            "original" => round(floatval($this->getValor()), 2)
        ];

        $body["calendario"] = [
            "dataDeVencimento" => $this->getVencimento(),
            "validadeAposVencimento" => 0
        ];

        $body["devedor"] = [];
        $body["devedor"]["nome"] = utf8_encode($this->cgm->z01_nome);

        if (strlen($this->buildCpfCnpj()) < 13) {
            $body["devedor"]["cpf"] = $this->buildCpfCnpj();
        } else {
            $body["devedor"]["cnpj"] = $this->buildCpfCnpj();
        }

        $body["infoAdicionais"] = [
            [
                "nome" => "codigo_de_barras",
                "valor" => $this->getCodigoBarras()
            ],
            [
                "nome" => "descricao",
                "valor" => utf8_encode("Arrecadaï¿½ï¿½o Pix")
            ]
        ];

        return $body;
    }

    /**
     * @return string
     */
    private function buildCpfCnpj()
    {
        if ($this->isCountyCnpj() === true && empty($this->cgm->z01_cgccpf)) {
            return $this->getCnpj();
        }

        return $this->cgm->z01_cgccpf;
    }

    /**
     * @return void
     */
    private function buildTxId()
    {
        $txid = "N{$this->getcodigoArrecadacao()}";

        if ($this->getParcela()) {
            $txid .= "P{$this->getParcela()}";
        }

        $txid .= "R".substr(time(), -4);

        $this->txId = str_pad($txid, 35, "0", STR_PAD_LEFT);
    }

    /**
     * @param $httpResponse
     * @return object
     */
    private function parseHttpResponseBody($httpResponse)
    {
        return json_decode($httpResponse->getBody());
    }

    /**
     * @param $isAuth boolean
     * @param $message string
     * @return void
     */
    private function logger($isAuth, $message)
    {
        $path = ECIDADE_PATH."/tmp/";

        if ($isAuth) {
            $fileName = "pix-banrisul-auth-log.log";
        } else {
            $fileName = "pix-banrisul-generation-log.log";
        }

        $fileName = date("Y-m-d")."-{$fileName}";

        $fileOpen = fopen($path.$fileName, "a+");
        fwrite($fileOpen, $message."\n");
        fclose($fileOpen);
    }
}
