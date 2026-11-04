<?php

namespace App\Domain\Educacao\CentralMatriculas\Helpers;

class CoresHelper extends CentralMatriculasHelper
{
    public function __construct()
    {
        parent::__construct();
        $this->api = $this->getApiCentral() ."/configuracao/cores";
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

    public function update(array $params, $id)
    {
        $params = $this->mapParams($params);
        $response = $this->put($this->getApi() . "/" . $id, $params, true);
        $dados = (object)json_decode($response->getBody(), true);
        return $dados->data;
    }

    public function mapParams(array $params)
    {
        $dados = [];
        $dados['cor'] =  $params['cor'];
        return $dados;
    }
}
