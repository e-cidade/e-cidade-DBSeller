<?php

namespace App\Domain\Patrimonial\Licitacoes\Clients;

class ImportacaoBncClient extends BncClient
{
    /**
     * @param $processoId
     * @return mixed
     */
    public function buscarResultadoProcesso($processoId)
    {
        $options = [];
        $options['query']['processId'] = $processoId;

        $response = $this->get($this->getUriBuscarResultadoProcesso(), $options);
        $processo = $response->getBody()->getContents();

        return json_decode($processo);
    }

    /**
     * @return string
     */
    protected function getUriBuscarResultadoProcesso()
    {
        return 'Process/GetProcessResult';
    }
}
