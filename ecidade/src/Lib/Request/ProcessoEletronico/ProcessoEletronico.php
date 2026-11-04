<?php

namespace ECidade\Lib\Request\ProcessoEletronico;

use Carbon\Carbon;
use ECidade\V3\Extension\Registry;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Cache;

class ProcessoEletronico
{

    private $urlApi;
    private $grant_type;
    private $client_id;
    private $client_secret;
    private $token;
    private $clientGuzzle;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->iniciarLaravel();
        $this->getAuthorization();
    }

    /**
     * @return mixed
     */
    public function getUrlApi()
    {
        return $this->urlApi;
    }

    public function getRouteToken()
    {
        return $this->getUrlApi()."/oauth/token";
    }

    public function getRouteEnviarFolhaPagamento()
    {
        return $this->getUrlApi()."/api/servidor/ficha-financeira";
    }

    public function getRouteNotificaAssinatura()
    {
        return $this->getUrlApi()."/client/mensagem";
    }

    /**
     * @param  mixed  $urlApi
     */
    public function setUrlApi($urlApi)
    {
        $this->urlApi = $urlApi;
    }

    /**
     * @return mixed
     */
    public function getGrantType()
    {
        return $this->grant_type;
    }

    /**
     * @param  mixed  $grant_type
     */
    public function setGrantType($grant_type)
    {
        $this->grant_type = $grant_type;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @param  mixed  $client_id
     */
    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
    }

    /**
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->client_secret;
    }

    /**
     * @param  mixed  $client_secret
     */
    public function setClientSecret($client_secret)
    {
        $this->client_secret = $client_secret;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param  mixed  $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }


    private function validateConfig()
    {
        if (! Registry::get('app.config')->has('app.api')) {
            $msg = "Erro ao buscar as credencias das api's";
            $msg .= "\nVerifique o arquivo de configurao (application).";
            throw new \Exception($msg);
        }

        $configApi = (object)Registry::get('app.config')->get('app.api');

        if (empty($configApi) || empty($configApi->processoeletronico)) {
            $msg = "Erro ao buscar as credencias do processoeletronico";
            $msg .= "\nVerifique o arquivo de configurao (application).";
            throw new \Exception($msg);
        }

        $processoeletronico = (object)$configApi->processoeletronico;

        $this->setUrlApi($processoeletronico->url);
        $this->setClientId($processoeletronico->client_id);
        $this->setClientSecret($processoeletronico->client_secret);
        $this->setGrantType($processoeletronico->grant_type);


        if (empty($this->getUrlApi())) {
            throw new \Exception("processoeletronico url NÃO CONFIGURADO");
        }

        if (empty($this->getGrantType())) {
            throw new \Exception("processoeletronico grant_type NÃO CONFIGURADO");
        }

        if (empty($this->getClientId())) {
            throw new \Exception("processoeletronico client_id NÃO CONFIGURADO");
        }

        if (empty($this->getClientSecret())) {
            throw new \Exception("processoeletronico client_secret NÃO CONFIGURADO");
        }

        $optionsGuzzle = [];

        if (! empty($processoeletronico->disableVerifySSL)
             and (bool)$processoeletronico->disableVerifySSL === true
        ) {
            $optionsGuzzle["verify"] = false;
        }

        $this->clientGuzzle = new Client($optionsGuzzle);
    }

    private function getAuthorization()
    {
        try {
            $this->validateConfig();
            $options = [
                'form_params' => [
                    'grant_type'    => $this->getGrantType(),
                    'client_id'     => $this->getClientId(),
                    'client_secret' => $this->getClientSecret(),
                ],
            ];

            if (Cache::has('token_processo_eletronico')) {
                $this->setToken(Cache::get("token_processo_eletronico"));

                return;
            }

            $response = $this->clientGuzzle->request(
                'POST',
                $this->getRouteToken(),
                $options
            );
            $obj      = (object)json_decode($response->getBody(), true);

            if (empty($obj->access_token)) {
                throw new \Exception("Erro ao buscar token");
            }

            $expiresAt = Carbon::now()->addMinutes(10);
            Cache::put(
                'token_processo_eletronico',
                $obj->access_token,
                $expiresAt
            );
            $this->setToken($obj->access_token);
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    private function getHeaders()
    {
        return array(
            'Authorization' => 'Bearer '.$this->token,
            'Accept'        => 'application/json',
        );
    }


    private function iniciarLaravel()
    {
        $app = require_once ECIDADE_PATH.'bootstrap/app.php';
        // App já iniciado
        if ($app === true) {
            return;
        }
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
    }


    /**
     * @param  array  $folha
     *
     * @return object
     * @throws \Exception
     */
    public function enviarFolhaPagamento(
        array $folha
    ) {
        $form_params = \DBString::utf8_encode_all($folha);
        $options     = array(
            'headers'     => $this->getHeaders(),
            'form_params' => $form_params,
        );

        try {
            $response = $this->clientGuzzle->request(
                'POST',
                $this->getRouteEnviarFolhaPagamento(),
                $options
            );
        } catch (ServerException $ex) {
            throw new \Exception(
                $ex->getMessage(),
                $ex->getCode(),
                $ex->getPrevious()
            );
        } catch (ClientException $ex) {
            throw new \Exception(
                $ex->getMessage(),
                $ex->getCode(),
                $ex->getPrevious()
            );
        } catch (ConnectException $ex) {
            throw new \Exception(
                $ex->getMessage(),
                $ex->getCode(),
                $ex->getPrevious()
            );
        }

        return (object)json_decode($response->getBody(), true);
    }

    public function notificar($mensagem, $cpfCnpj)
    {
        $options  = array(
            'headers'     => $this->getHeaders(),
            'form_params' => array(
                'mensagem' => \DBString::utf8_encode_all($mensagem),
                'cpf_cnpj' => \DBString::utf8_encode_all($cpfCnpj),
            ),
        );
        $response = $this->clientGuzzle->request(
            'POST',
            $this->getRouteNotificaAssinatura(),
            $options
        );

        return (object)json_decode($response->getBody(), true);
    }
}
