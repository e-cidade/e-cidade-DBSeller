<?php

namespace App\Domain\Educacao\CentralMatriculas\Helpers;

class DocumentosHelper extends CentralMatriculasHelper
{
    public function __construct()
    {
        parent::__construct();
        $this->api = $this->getApiCentral() ."/configuracao/documentos";
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

    public function update(array $params, $id)
    {
        $params = $this->mapParams($params);
        $response = $this->post($this->getApi() . "/" . $id, $params, true);
        $dados = (object)json_decode($response->getBody(), true);
        return $dados->data;
    }

    public function mapParams(array $params)
    {
        $dados = [];
        $dados['_method'] = 'PUT';
        $dados['documento'] =  $params['file'];
        return $dados;
    }
}
