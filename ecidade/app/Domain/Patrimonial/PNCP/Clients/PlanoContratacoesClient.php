<?php

namespace App\Domain\Patrimonial\PNCP\Clients;

class PlanoContratacoesClient extends PNCPClient
{
    /**
     * @param $documento
     * @return string
     */
    private function getUriIncluir($documento)
    {
        return "v1/orgaos/{$documento}/pca";
    }

    /**
     * @param $documento
     * @param $dados
     * @return object
     * @throws \Exception
     */
    public function incluir($documento, $dados)
    {
        return $this->doRequest('POST', $this->getUriIncluir($documento), $dados, true);
    }
}
