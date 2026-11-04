<?php

namespace App\Domain\Patrimonial\Licitacoes\Services;

use App\Domain\Patrimonial\Licitacoes\Models\Licitacao;
use App\Domain\Patrimonial\Licitacoes\Clients\LicitaconObrasClient;
use App\Domain\Patrimonial\Licitacoes\Clients\LicitaconClient;

class LicitaconService
{
    private $version;

    /**
     * @param $instituicao
     * @return bool
     */
    public function verificarOrgaoFiscalizado($instituicao)
    {
        $client = new LicitaconObrasClient();
        $orgao = $client->buscarOrgaoFiscalizado($instituicao->cgc);

        return (bool)$orgao;
    }

    /**
     * @param $cgc
     * @return array|false
     */
    public function verificarOrgao($cgc)
    {
        $client = new LicitaconClient();
        $orgao = $client->buscarCodigoOrgao($cgc);

        return $orgao;
    }

    /**
     * @param $orgao
     * @param $modalidade
     * @param $numeroLicitacao
     * @param $anoLicitacao
     * @return array|false
     */
    public function verificarLicitacoes($orgao, $modalidade, $numeroLicitacao, $anoLicitacao)
    {
        $client = new LicitaconClient();
        $licitacoes = $client->buscarLicitaconLicitacoes(
            $orgao->CD_ORGAO,
            $modalidade,
            (int)$numeroLicitacao,
            (int)$anoLicitacao
        );

        return $licitacoes;
    }

    public function verificarContratos($cdOrgao, $numeroContrato, $anoContrato, $tipoInstrumento)
    {
        $client = new LicitaconClient();
        $contratos = $client->buscarLicitaconContratos(
            $cdOrgao,
            (int)$numeroContrato,
            (int)$anoContrato,
            $tipoInstrumento
        );

        return $contratos;
    }
}
