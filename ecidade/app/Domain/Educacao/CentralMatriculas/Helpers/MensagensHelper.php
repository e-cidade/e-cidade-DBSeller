<?php

namespace App\Domain\Educacao\CentralMatriculas\Helpers;

use Carbon\Carbon;

class MensagensHelper extends CentralMatriculasHelper
{
    public function __construct()
    {
        parent::__construct();
        $this->api = $this->getApiCentral() ."/configuracao/mensagens";
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
        $response = $this->put($this->getApi() . "/" . $id, $params);
        $dados = (object)json_decode($response->getBody(), true);
        return $dados->data;
    }

    public function mapParams(array $params)
    {
        $dados = [];
        $dados['conteudo'] =  $params['conteudo'];
        return $dados;
    }
}
