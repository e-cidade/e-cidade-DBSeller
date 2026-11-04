<?php

namespace App\Domain\Patrimonial\Licitacoes\Clients;

use App\Domain\Patrimonial\Licitacoes\Models\LicitaconUsuario;
use GuzzleHttp\Client;

class LicitaconClient extends Client
{
    public function __construct($config = [])
    {
        $config['headers']['Accept'] = '*/*';
        $config['headers']['Content-Type'] = 'application/json';
        $config['base_uri'] = empty($config['base_uri']) ? env('LICITACON_URL') . 'qonws/q/' : $config['base_uri'];

        parent::__construct($config);
    }

    /**
     * @param $cnpj
     * @return array|false
     */
    public function buscarCodigoOrgao($cnpj)
    {
        $posicaoOrgaoFiscalizado = false;
        $response = $this->get('licitacon.orgaos.json');
        $orgaosFiscalizados = json_decode($response->getBody()->getContents());

        foreach ($orgaosFiscalizados as $key => $orgao) {
            if ($orgao->CNPJ === $cnpj) {
                $posicaoOrgaoFiscalizado = $key;
            }
        }

        return $posicaoOrgaoFiscalizado ? $orgaosFiscalizados[$posicaoOrgaoFiscalizado] : false;
    }

    /**
     * @param $cdOrgao
     * @param $modalidade
     * @param $numeroLicitacao
     * @param $anoLicitacao
     * @return array|false
     */
    public function buscarLicitaconLicitacoes($cdOrgao, $modalidade, $numeroLicitacao, $anoLicitacao)
    {
        $response = $this->get("licitacon.licitacoes.json?cd_orgao={$cdOrgao}&tp_situacao=all&origem=all");
        $licitaconLicitacoes = json_decode($response->getBody()->getContents(), true);
        $licitacaoEncontrada = false;

        foreach ($licitaconLicitacoes as $itemLicitacon) {
            $modalidadeLicitacaoValida = $itemLicitacon['CD_TIPO_MODALIDADE'] === $modalidade;
            $numeroLicitacaoValido = $itemLicitacon['NR_LICITACAO'] === $numeroLicitacao;
            $anoLicitacaoValido = $itemLicitacon['ANO_LICITACAO'] === $anoLicitacao;

            if ($modalidadeLicitacaoValida && $numeroLicitacaoValido && $anoLicitacaoValido) {
                $licitacaoEncontrada = $itemLicitacon;
                break;
            }
        }

        return $licitacaoEncontrada;
    }

    public function buscarLicitaconContratos($cdOrgao, $numeroContrato, $anoContrato, $tipoInstrumento)
    {
        $response = $this->get("licitacon.contratos.json?cd_orgao={$cdOrgao}&tp_situacao=all&origem=all");
        $licitaconContratos = json_decode($response->getBody()->getContents(), true);
        $contratoEncontrado = false;

        foreach ($licitaconContratos as $itemLicitacon) {
            $numeroContratoValido = $itemLicitacon['NR_CONTRATO'] === $numeroContrato;
            $anoContratoValido = $itemLicitacon['ANO_CONTRATO'] === $anoContrato;
            $tipoInstrumentoValido = $itemLicitacon['TP_INSTRUMENTO'] === $tipoInstrumento;

            if ($numeroContratoValido && $anoContratoValido && $tipoInstrumentoValido) {
                $contratoEncontrado = $itemLicitacon;
                break;
            }
        }

        return $contratoEncontrado;
    }
}
