<?php

namespace ECidade\Lib\Request\EAuth;

use \GuzzleHttp\Client;
use ECidade\V3\Extension\Registry;

class EAuth
{

    private $urlApi;
    private $municipio;
    private $grant_type;
    private $client_id;
    private $client_secret;
    private $token;
    private $clientGuzzle;
    private $frontend_id_client;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
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
        return $this->getUrlApi() . "/oauth/token";
    }

    public function getRouteValidaExsite()
    {
        return $this->getUrlApi() . "/users/valida";
    }

    public function getRouteUserSave()
    {
        return $this->getUrlApi() . "/users/save";
    }

    public function getRouteEnviaPush()
    {
        return $this->getUrlApi() . "/api/push/save";
    }

    public function getRouteEmailMessage()
    {
        return $this->getUrlApi() . "/users/message";
    }

    public function getRouteUserCpf()
    {
        return $this->getUrlApi() . "/users/cpfcnpj";
    }

    public function getRouteNotificaAssinatura()
    {
        return $this->getUrlApi() . "/users/notifica-assinatura";
    }

    public function getRouteVerificaUserMunicipio()
    {
        return $this->getUrlApi() . "/users/verifica-user-municipio";
    }

    /**
     * @param mixed $urlApi
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
     * @param mixed $grant_type
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
     * @param mixed $client_id
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
     * @param mixed $client_secret
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
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }

    /**
     * @param mixed $municipio
     */
    public function setMunicipio($municipio)
    {
        $this->municipio = $municipio;
    }

    /**
     * @param mixed $frontend_id
     */
    public function setFrontend($frontend_id)
    {
        $this->frontend_id_client = $frontend_id;
    }

    /**
     * @return mixed
     */
    public function getFrontendId()
    {
        return $this->frontend_id_client;
    }

    private function validateConfig()
    {

        if (!Registry::get('app.config')->has('app.api')) {
            $msg = "Erro ao buscar as credencias das api's";
            $msg .= "\nVerifique o arquivo de configurao (application).";
            throw new \Exception($msg);
        }

        $configApi = (object)Registry::get('app.config')->get('app.api');

        if (empty($configApi) || empty($configApi->eauth)) {
            $msg = "Erro ao buscar as credencias do eauth";
            $msg .= "\nVerifique o arquivo de configurao (application).";
            throw new \Exception($msg);
        }

        $eauth = (object)$configApi->eauth;

        $this->setUrlApi($eauth->url);
        $this->setClientId($eauth->client_id);
        $this->setClientSecret($eauth->client_secret);
        $this->setGrantType($eauth->grant_type);
        $this->setMunicipio($eauth->municipio);
        $this->setFrontend($eauth->frontend_client_id);

        if (empty($this->getUrlApi())) {
            throw new \Exception("eauth url NO CONFIGURADO");
        }

        if (empty($this->getGrantType())) {
            throw new \Exception("eauth grant_type NO CONFIGURADO");
        }

        if (empty($this->getClientId())) {
            throw new \Exception("eauth client_id NO CONFIGURADO");
        }

        if (empty($this->getClientSecret())) {
            throw new \Exception("eauth client_secret NO CONFIGURADO");
        }

        if (empty($this->getMunicipio())) {
            throw new \Exception("eauth municipio NO CONFIGURADO");
        }

        $optionsGuzzle = [];

        if (!empty($eauth->disableVerifySSL) and (bool)$eauth->disableVerifySSL === true) {
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
                    'grant_type' => $this->getGrantType(),
                    'client_id' => $this->getClientId(),
                    'client_secret' => $this->getClientSecret(),
                ]
            ];

            $response = $this->clientGuzzle->request('POST', $this->getRouteToken(), $options);
            $obj = (object)json_decode($response->getBody(), true);

            if (empty($obj->access_token)) {
                throw new \Exception("Erro ao buscar token");
            }
            $this->token = $obj->access_token;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    private function getHeaders()
    {
        return array(
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        );
    }

    public function validarUsuarioExiste($cpfcnpj)
    {
        $options = array(
            'headers' => $this->getHeaders(),
            'form_params' => array(
                'cpfcnpj' => $cpfcnpj,
                'municipio' => $this->getMunicipio()
            )
        );

        return $this->clientGuzzle->request('POST', $this->getRouteValidaExsite(), $options);
    }

    public function salvarUsuarioEauth(
        $nome,
        $email,
        $cpfcnpj,
        $client_app_id = null,
        $processo = null,
        $notifica_assinatura = false
    ) {
        if ($client_app_id === null) {
            $client_app_id = $this->frontend_id_client;
        }

        $form_params = array(
            'name' => \DBString::utf8_encode_all($nome),
            'email' => \DBString::utf8_encode_all($email),
            'cpfcnpj' => \DBString::utf8_encode_all($cpfcnpj),
            'municipio' => $this->getMunicipio(),
            'client_id' => $client_app_id
        );

        if ($notifica_assinatura) {
            $form_params['processo'] = \DBString::utf8_encode_all($processo);
            $form_params['notifica_assinatura'] =  $notifica_assinatura;
        }

        $options = array(
            'headers' => $this->getHeaders(),
            'form_params' => $form_params
        );
        $response = $this->clientGuzzle->request('POST', $this->getRouteUserSave(), $options);
        return (object)json_decode($response->getBody(), true);
    }

    public function consultaUserCpf($cpfcnpj)
    {
        $options = array(
            'headers' => $this->getHeaders(),
        );
        $params = http_build_query(array(
            'cpfcnpj' => str_replace(array(".", "-", "/"), "", $cpfcnpj),
            'municipio' => $this->getMunicipio()
        ));
        $response = $this->clientGuzzle->get($this->getRouteUserCpf() . "?{$params}", $options);
        return (object)json_decode($response->getBody(), true);
    }

    public function sendMessage($cpfCnpj, $client_app_id, $message, $email = null, $anonimo = false)
    {
        $message = \DBString::utf8_encode_all($message);
        $options = array(
            'headers' => $this->getHeaders(),
            'form_params' => array(
                'cpfcnpj' => $cpfCnpj,
                'client_id' => $client_app_id,
                'message' => $message,
                'email' => $email,
                'anonimo' => $anonimo
            )
        );
        $response = $this->clientGuzzle->request('POST', $this->getRouteEmailMessage(), $options);
        return (object)json_decode($response->getBody(), true);
    }

    public function verificaUserMunicipio($cpfcnpj)
    {
        $options = array(
            'headers' => $this->getHeaders(),
            'form_params' => array(
                'cpfcnpj' => $cpfcnpj,
                'id_municipio' => $this->getMunicipio()
            )
        );
        $response = $this->clientGuzzle->request('POST', $this->getRouteVerificaUserMunicipio(), $options);
        return (object)json_decode($response->getBody(), true);
    }

    public function notificaAssinatura($mensagem, $cpfCnpj)
    {
        $options = array(
            'headers' => $this->getHeaders(),
            'form_params' => array(
                'message' => \DBString::utf8_encode_all($mensagem),
                'cpfcnpj' => \DBString::utf8_encode_all($cpfCnpj),
                'frontend_client_id' => $this->frontend_id_client
            )
        );
        $response = $this->clientGuzzle->request('POST', $this->getRouteNotificaAssinatura(), $options);
        return (object)json_decode($response->getBody(), true);
    }
}
