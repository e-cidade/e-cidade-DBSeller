<?php

namespace App\Domain\Patrimonial\Licitacoes\Clients;

use GuzzleHttp\Client;

class BncClient extends Client
{
    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $config['headers']['Accept'] = '*/*';
        $config['headers']['Content-Type'] = 'application/json';
        $config['headers']['Authorization'] = $this->getAccessToken();
        $config['base_uri'] = env('BNC_URL');

        parent::__construct($config);
    }

    /**
     * @return string
     */
    private function getAccessToken()
    {
        return 'Bearer ' . env('BNC_ACCESS_TOKEN');
    }

    /**
     * @param $processoId
     * @return bool
     */
    public function excluirProcesso($processoId)
    {
        $options = [];
        $options['body'] = json_encode($processoId);
        $this->post($this->getUriExcluirProcesso(), $options);

        return true;
    }

    /**
     * @return string
     */
    protected function getUriExcluirProcesso()
    {
        return 'Process/RemoveProcess';
    }

    /**
     * @param $processoId
     * @return mixed
     */
    public function buscarDadosProcesso($processoId)
    {
        $options = [];
        $options['query']['processId'] = $processoId;

        $response = $this->get($this->getUriBuscarDadosProcesso(), $options);
        $processo = $response->getBody()->getContents();

        return json_decode($processo);
    }

    /**
     * @return string
     */
    protected function getUriBuscarDadosProcesso()
    {
        return 'Process/GetProcessData';
    }

    /**
     * @param $licitacaoId
     * @param array $options
     * @return array|string|string[]
     */
    public function buscarIdProcesso($licitacaoId, $options = [])
    {
        $options['query']['param'] = $licitacaoId;
        $processoId = '';

        $response = $this->get($this->getUriBuscarIdProcesso(), $options);
        if ($response->getStatusCode() === 200) {
            $processoId = $response->getBody()->getContents();
        }

        return str_replace('"', "", $processoId);
    }

    /**
     * @return string
     */
    protected function getUriBuscarIdProcesso()
    {
        return 'Process/GetProcessId';
    }
}
