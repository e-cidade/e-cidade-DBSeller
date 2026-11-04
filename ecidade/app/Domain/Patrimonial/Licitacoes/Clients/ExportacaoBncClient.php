<?php

namespace App\Domain\Patrimonial\Licitacoes\Clients;

class ExportacaoBncClient extends BncClient
{
    /**
     * @param $options
     * @return string
     */
    public function salvarProcesso($options)
    {
        $response = $this->post($this->getUriSalvarProcesso(), $options);
        $processoId = $response->getBody()->getContents();

        return str_replace('"', '', $processoId);
    }

    /**
     * @return string
     */
    protected function getUriSalvarProcesso()
    {
        return 'Process/SaveProcess';
    }

    /**
     * @param $options
     * @return string
     */
    public function salvarLote($options)
    {
        $response = $this->post($this->getUriSalvarLote(), $options);
        return $response->getBody()->getContents();
    }

    /**
     * @return string
     */
    protected function getUriSalvarLote()
    {
        return 'Process/SaveBatches';
    }
}
