<?php

namespace App\Domain\Educacao\CentralMatriculas\Helpers;

class CamposOpcionaisHelper extends CentralMatriculasHelper
{
    public function __construct()
    {
        parent::__construct();
        $this->api = $this->getApiCentral() ."/configuracao/campos-opcionais";
    }

    /**
     * @return string
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @param string $api
     */
    public function setApi($api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $response = $this->get($this->getApi());
        $dados = (object)json_decode($response->getBody(), true);
        return $dados->data;
    }

    public function saveAll(array $params)
    {
        $params = $this->mapParams($params);
        $response = $this->post($this->getApi(), $params);
        $dados = (object)json_decode($response->getBody(), true);
        return $dados->data;
    }

    public function mapParams(array $params)
    {
        $dados = [];
        $dados['campos'] =  $params['campos'];
        foreach ($dados['campos'] as $key => $campo) {
            $dados['campos'][$key]['tipo'] = $campo['tipo']['value'];
        }
        return $dados;
    }
}
