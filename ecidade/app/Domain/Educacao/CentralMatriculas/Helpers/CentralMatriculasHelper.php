<?php

namespace App\Domain\Educacao\CentralMatriculas\Helpers;

use ECidade\Lib\Request\Storage\File as FileStorage;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;

class CentralMatriculasHelper
{
    protected $apiCentral;
    protected $client_secret;
    protected $client_id;
    protected $urlToken;

    public function __construct()
    {
        $this->apiCentral = env('CENTRAL_MATRICULAS_URL') . "/api";
        $this->client_secret = env('CENTRAL_MATRICULAS_CLIENT_SECRET');
        $this->client_id = env('CENTRAL_MATRICULAS_CLIENT_ID');
        $this->urlToken = env('CENTRAL_MATRICULAS_URL') . "/oauth/token";
    }

    /**
     * @return string
     */
    public function getUrlToken()
    {
        return $this->urlToken;
    }

    /**
     * @param string $urlToken
     */
    public function setUrlToken($urlToken)
    {
        $this->urlToken = $urlToken;
    }


    /**
     * @return string
     */
    public function getApiCentral()
    {
        return $this->apiCentral;
    }

    /**
     * @param string $apiCentral
     */
    public function setApiCentral($apiCentral)
    {
        $this->apiCentral = $apiCentral;
    }

    /**
     * @return bool|mixed|string|null
     */
    public function getClientSecret()
    {
        return $this->client_secret;
    }

    /**
     * @param bool|mixed|string|null $client_secret
     */
    public function setClientSecret($client_secret)
    {
        $this->client_secret = $client_secret;
    }

    /**
     * @return bool|mixed|string|null
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @param bool|mixed|string|null $client_id
     */
    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
    }

    public function getClient()
    {
        return new Client(['verify' => false ]);
    }

    public function getToken()
    {
        $options = [
            'form_params' => [
                'grant_type' => "client_credentials",
                'client_id' => $this->getClientId(),
                'client_secret' => $this->getClientSecret(),
            ]
        ];

        $response =  $this->getClient()->post($this->getUrlToken(), $options);
        $token = (object)json_decode($response->getBody(), true);
        return $token->access_token;
    }

    public function get($url)
    {
        $options = [
            "headers" => [
                "Authorization" => "Bearer {$this->getToken()}"
            ]
        ];

        return $this->getClient()->get($url, $options);
    }

    public function post($url, array $parametros, $multipart = false)
    {
        $options = [
            "headers" => [
                "Authorization" => "Bearer {$this->getToken()}"
            ],
            "form_params" => \DBString::utf8_encode_all($parametros)
        ];

        if ($multipart) {
            unset($options['form_params']);
            $options['multipart'] = $this->prepareMultipart(\DBString::utf8_encode_all($parametros));
        }

        return $this->getClient()->post($url, $options);
    }

    public function put($url, array $parametros)
    {
        $options = [
            "headers" => [
                "Authorization" => "Bearer {$this->getToken()}"
            ],
            "form_params" => \DBString::utf8_encode_all($parametros)
        ];

        return $this->getClient()->put($url, $options);
    }
    public function delete($url)
    {
        $options = [
            "headers" => [
                "Authorization" => "Bearer {$this->getToken()}"
            ]
        ];
        return $this->getClient()->delete($url, $options);
    }

    private function prepareMultipart($parametros)
    {
        $multipart = [];
        foreach ($parametros as $key => $parametro) {
            $param = [
                "name" => $key,
                "contents" => $parametro
            ];
            if ($parametro instanceof UploadedFile) {
                $param['contents'] = file_get_contents($parametro->getRealPath());
                $param['filename'] = $parametro->getClientOriginalName();
            }
            $multipart[] = $param;
        }
        return $multipart;
    }
}
