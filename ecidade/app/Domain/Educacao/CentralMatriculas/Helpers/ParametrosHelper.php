<?php

namespace App\Domain\Educacao\CentralMatriculas\Helpers;

class ParametrosHelper extends CentralMatriculasHelper
{
    public function __construct()
    {
        parent::__construct();
        $this->api = $this->getApiCentral() ."/configuracao/parametros";
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

    public function first()
    {
        $response = $this->get($this->getApi());
        $dados = (object)json_decode($response->getBody(), true);
        return $dados->data;
    }

    public function save(array $params)
    {
        $params = $this->mapParams($params);
        $response = $this->post($this->getApi(), $params);
        $dados = (object)json_decode($response->getBody(), true);
        return $dados->data;
    }

    public function mapParams(array $params)
    {
        $dados = [];
        foreach ($params['parametros'] as $key => $campo) {
            $campo = utf8_encode_all($campo);
            $dados[$campo['campo']] = $campo['data'];
            if ($campo['componente'] === 'Dropdown') {
                $dados[$campo['campo']] = $campo['data']['value'];
            }
            if ($campo['componente'] === 'Checkbox') {
                $dados[$campo['campo']] = $campo['data'] ? 1 : 0;
            }
        }
        return $dados;
    }
}
