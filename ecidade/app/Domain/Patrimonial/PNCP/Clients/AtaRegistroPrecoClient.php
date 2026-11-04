<?php

namespace App\Domain\Patrimonial\PNCP\Clients;

class AtaRegistroPrecoClient extends PNCPClient
{
    /**
     * @param $documento
     * @param $anoCompra
     * @param $sequencialCompra
     * @param $dados
     * @return object
     * @throws \Exception
     */
    public function incluir($documento, $anoCompra, $sequencialCompra, $dados)
    {
        return $this->doRequest(
            'POST',
            $this->getUriIncluirOuBuscar($documento, $anoCompra, $sequencialCompra),
            $dados,
            true
        );
    }

    /**
     * @throws \Exception
     */
    public function incluirAnexoAta($anexoAta, $dados)
    {
        return $this->doRequestIncluirDocumentoContrato(
            $this->getUriIncluirAnexoCompra($anexoAta),
            $dados
        );
    }

    /**
     * @param $anexoAta
     * @return string
     */
    public function getUriIncluirAnexoCompra($anexoAta)
    {
        return "v1/orgaos/{$anexoAta['documento']}/compras/{$anexoAta['anoCompra']}/"
        . "{$anexoAta['sequencialCompra']}/atas/{$anexoAta['sequencialAta']}/arquivos";
    }

    /**:
     * @param $documento
     * @param $anoCompra
     * @param $sequencialCompra
     * @return string
     */
    private function getUriIncluirOuBuscar($documento, $anoCompra, $sequencialCompra)
    {
        return "v1/orgaos/{$documento}/compras/{$anoCompra}/{$sequencialCompra}/atas";
    }

    /**
     * @param $documento
     * @param $anoCompra
     * @param $sequencialCompra
     * @return object
     */
    public function buscar($documento, $anoCompra, $sequencialCompra)
    {
        return $this->doRequest('GET', $this->getUriIncluirOuBuscar($documento, $anoCompra, $sequencialCompra));
    }

    /**
     * @param $documento
     * @param $anoCompra
     * @param $sequencialCompra
     * @param $sequencialAta
     * @return object
     */
    public function excluir($documento, $anoCompra, $sequencialCompra, $sequencialAta)
    {
        return $this->doRequest(
            'DELETE',
            $this->getUriExlcuirOuRetificar($documento, $anoCompra, $sequencialCompra, $sequencialAta)
        );
    }


    /**
     * @param $documento
     * @param $anoCompra
     * @param $sequencialCompra
     * @param $sequencialAta
     * @param $dados
     * @return object
     */
    public function retificar($documento, $anoCompra, $sequencialCompra, $sequencialAta, $dados)
    {
        return $this->doRequest(
            'PUT',
            $this->getUriExlcuirOuRetificar($documento, $anoCompra, $sequencialCompra, $sequencialAta),
            $dados
        );
    }

    /**
     * @param $documento
     * @param $anoCompra
     * @param $sequencialCompra
     * @param $sequencialAta
     * @return string
     */
    public function getUriExlcuirOuRetificar($documento, $anoCompra, $sequencialCompra, $sequencialAta)
    {
        return "v1/orgaos/{$documento}/compras/{$anoCompra}/{$sequencialCompra}/atas/{$sequencialAta}";
    }
}
