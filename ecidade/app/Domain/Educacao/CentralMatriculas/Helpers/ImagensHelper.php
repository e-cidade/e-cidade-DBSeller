<?php

namespace App\Domain\Educacao\CentralMatriculas\Helpers;

class ImagensHelper extends CentralMatriculasHelper
{
    public function __construct()
    {
        parent::__construct();
        $this->api = $this->getApiCentral() ."/configuracao/imagens";
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
        $response = $this->post($this->getApi() . "/" . $id, $params, true);
        $dados = (object)json_decode($response->getBody(), true);
        return $dados->data;
    }

    public function delet($id)
    {
        $response = $this->delete($this->getApi() . "/" . $id);
        $dados = (object)json_decode($response->getBody(), true);
        return $dados->data;
    }

    public function mapParams(array $params)
    {
        $dados = [];
        $dados['_method'] = 'PUT';
        if (isset($params['imagem'])) {
            $dados['imagem'] =  $params['imagem'];
        }
        return $dados;
    }
}
