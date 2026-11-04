<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

class ArquivoAnulacaoDeLiquidacaoDeEmpenho extends ArquivoBase
{
    protected $sNomeArquivo = 'AnulacaoDeLiquidacaoDeEmpenho';

    public function gerarDados()
    {
        $campos = [
            'e60_numemp',
            'e60_codemp',
            'e60_anousu',
            'o58_orgao',
            'o58_unidade',
            'e69_codnota',
            'e71_codord',
            'c70_codlan',
            'c70_data',
            'c70_valor',
            'c72_complem',
            'e69_anousu',
            'z01_nome',
            'z01_cgccpf',
        ];
        $empenhos = DB::table('empempenho')
            ->select($campos)
            ->join('empnota', 'e69_numemp', 'e60_numemp')
            ->join('orcdotacao', function (JoinClause $join) {
                $join->on('o58_anousu', 'e60_anousu')
                    ->on('o58_coddot', 'e60_coddot');
            })
            ->join('conlancamemp', 'c75_numemp', 'e60_numemp')
            ->join('conlancam', 'c75_codlan', 'c70_codlan')
            ->join('conlancamdoc', 'c71_codlan', 'c70_codlan')
            ->join('conlancamcompl', 'c72_codlan', 'c70_codlan')
            ->join('conlancamnota', function (JoinClause $join) {
                $join->on('c66_codlan', 'c70_codlan')
                    ->on('c66_codnota', 'e69_codnota');
            })
            ->join('pagordemnota', 'e71_codnota', 'e69_codnota')
            ->join('lancamentoscontabeislog AS log', function (JoinClause $join) {
                $join->on('codlan', 'c70_codlan')
                    ->where('tipo_movimento', 1);
            })
            ->join('db_usuacgm', 'db_usuacgm.id_usuario', 'log.id_usuario')
            ->join('cgm AS cgmusu', 'cgmusu.z01_numcgm', 'cgmlogin')
            ->whereIn('c71_coddoc', [4, 24, 413])
            ->where('e60_anousu', $this->iAnoUsu)
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->whereBetween('c70_data', [$this->dtDataInicial, $this->dtDataFinal])
            ->orderBy('e60_numemp')
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception(sprintf(
                'Não foi encontrado nenhum registro para o arquivo da Remessa de %s',
                'Anulação de Liquidação de Empenho'
            ));
        }

        $obj = new stdClass();
        $obj->AnulacoesDeLiquidacaoDeEmpenho = [];
        foreach ($empenhos as $empenho) {
            $data = (object)[
                "Identificador" => $empenho->c70_codlan,
                "CodigoUnidadeGestora" => $this->sCodigoTribunal,
                "NumeroEmpenho" => $empenho->e60_codemp,
                "AnoEmpenho" => $empenho->e60_anousu,
                "NumeroLiquidacaoEmpenho" => $empenho->e71_codord,
                "AnoLiquidacao" => $empenho->e69_anousu,
                "NumeroAnulacao" => $empenho->e71_codord,
                "Competencia" => $this->competencia,
                "Data" => $empenho->c70_data,
                "CPFResponsavel" => $empenho->z01_cgccpf,
                "Valor" => $empenho->c70_valor,
                "Justificativa" => Helper::convertAndLimit("<![CDATA[".$empenho->c72_complem),
                "CodigoOrgao" => $empenho->o58_orgao,
                "CodigoUnidadeOrcamentaria" => $empenho->o58_unidade,
            ];

            $obj->AnulacoesDeLiquidacaoDeEmpenho[] = (object)['AnulacaoDeLiquidacaoDeEmpenho' => $data];
        }
        $this->aDados = $obj;
    }
}
