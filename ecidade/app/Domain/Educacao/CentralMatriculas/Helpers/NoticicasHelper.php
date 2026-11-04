<?php

namespace App\Domain\Educacao\CentralMatriculas\Helpers;

use Carbon\Carbon;

class NoticicasHelper extends CentralMatriculasHelper
{
    protected $api;
    public function __construct()
    {

        parent::__construct();
        $this->api = $this->getApiCentral() ."/configuracao/noticias";
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
    public function create(array $params)
    {
        $response = $this->post($this->getApi(), $this->mapParams($params), true);
        $dados = (object)json_decode($response->getBody(), true);
        return $dados->data;
    }

    public function update(array $params, $id)
    {
        $params = $this->mapParams($params);
        $params['_method'] = 'PUT';
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
        $dados['data'] = (new Carbon($params['data']))->format('Y-m-d');
        $dados['ativa'] =  $params['ativa'] === 'true' ? 1 : 0;
        $dados['texto'] =  $params['texto'];
        $dados['titulo'] =  $params['titulo'];

        if (isset($params['imagem'])) {
            $dados['imagem'] =  $params['imagem'];
        }

        if (isset($params['ordem'])) {
            $dados['ordem'] =  $params['ordem'];
        }
        return $dados;
    }
}
