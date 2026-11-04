<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

class ArquivoLiquidacaoEmpenhoFolhaPagamento extends ArquivoBase
{
    protected $sNomeArquivo = 'LiquidacaoEmpenhoFolhaPagamento';

    public function gerarDados()
    {
        $campos = [
            'e60_numemp', 'e60_codemp', 'e60_anousu',
            'o58_orgao', 'o58_unidade',
            'e69_codnota', 'e69_anousu',
            'e71_codord',
            'e70_valor',
            'e50_obs',
            'e164_data',
            'conta',
            'e177_decimo'
        ];

        $empenhos = DB::table('empempenho')
            ->select($campos)
            ->join('empnota', 'e69_numemp', 'e60_numemp')
            ->join('empnotaele', 'e70_codnota', 'e69_codnota')
            ->join('pagordem', 'e50_numemp', 'e60_numemp')
            ->join('pagordemnota', function (JoinClause $join) {
                $join->on('e71_codnota', 'e69_codnota')
                    ->on('e71_codord', 'e50_codord');
            })
            ->join('orcdotacao', function (JoinClause $join) {
                $join->on('o58_anousu', 'e60_anousu')
                    ->on('o58_coddot', 'e60_coddot');
            })
            ->join('empelemento', 'e64_numemp', 'e60_numemp')
            ->join('conplanoorcamento', function (JoinClause $join) {
                $join->on('c60_codcon', 'e64_codele')
                    ->on('c60_anousu', 'e60_anousu');
            })
            ->join('planodespesaconplanoorcamento', 'conplanoorcamento_codigo', 'c60_codigo')
            ->join('planodespesa', function (JoinClause $join) {
                $join->on('planodespesa.id', 'planodespesa_id')
                    ->on('exercicio', 'e60_anousu')
                    ->where('uniao', '=', $this->getUtilizaPlanoUniao());
            })
            ->join('empnotasigfistipodocliquidacao', 'e178_empnota', 'e69_codnota')
            ->join('sigfistipodocliquidacao', 'e177_sequencial', 'e178_sigfistipodocliquidacao')
            ->leftJoin('empcompetencialiquidacao', 'e164_codord', 'e50_codord')
            ->where('e60_anousu', $this->iAnoUsu)
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->whereBetween('e69_dtinclusao', [$this->dtDataInicial, $this->dtDataFinal])
            ->where('e177_codigo', 2)
            ->orderBy('e60_numemp')
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception('Não foi encontrado nenhum empenho para o arquivo da Remessa.');
        }

        $obj = new stdClass();
        $obj->LiquidacoesEmpenhoFolhasPagamentos = [];

        // Controla o numero da folha
        $numeroFolha = ['normal' => 0, 'decimo' => 0];
        foreach ($empenhos as $empenho) {
            $parts = explode('-', $this->dtDataFinal);
            $mesFolha = $parts[1];
            $anoFolha = $empenho->e60_anousu;
            if (!empty($empenho->e164_data) && $empenho->e164_data !== '00/0000') {
                $parts = explode('/', $empenho->e164_data);
                $mesFolha = $parts[0];
                $anoFolha = $parts[1];
            }

            $tipoFolha = 1;
            if ($empenho->e177_decimo == 't') {
                $tipoFolha = 2;
            }

            $tipo = $tipoFolha === 1 ? 'normal' : 'decimo';
            $numeroFolha[$tipo] += 1;

            $data = (object)[
                "Identificador" => $empenho->e60_numemp,
                "CodigoUnidadeGestora" => $this->sCodigoTribunal,
                "NumeroEmpenho" => $empenho->e60_codemp,
                "AnoEmpenho" => $empenho->e60_anousu,
                "Competencia" => $this->competencia,
                "NumeroLiquidacaoEmpenho" => $empenho->e71_codord,
                "NumeroFolhaPagamento" => str_pad($numeroFolha[$tipo], 2, '0', STR_PAD_LEFT),
                "AnoReferencia" => $anoFolha,
                "MesReferencia" => $mesFolha,
                "Valor" => $empenho->e70_valor,
                "Objeto" => Helper::convertAndLimit("<![CDATA[".$empenho->e50_obs, 207),
                "AnoLiquidacaoDeEmpenho" => $empenho->e69_anousu,
                "CodigoOrgao" => $empenho->o58_orgao,
                "CodigoUnidadeOrcamentaria" => $empenho->o58_unidade,
                "CodigoUnidadeGestoraResponsavel" => $this->getUgResponsavelFolha(),
                "CodigoTipoFolhaPagamento" => $tipoFolha,
            ];
            $obj->LiquidacoesEmpenhoFolhasPagamentos[] = (object)['LiquidacaoEmpenhoFolhaPagamento' => $data];
        }

        $this->aDados = $obj;
    }
}
