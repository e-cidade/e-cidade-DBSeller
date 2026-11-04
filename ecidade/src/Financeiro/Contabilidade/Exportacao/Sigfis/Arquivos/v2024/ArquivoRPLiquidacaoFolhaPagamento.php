<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Carbon\Carbon;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

class ArquivoRPLiquidacaoFolhaPagamento extends ArquivoBase
{

    protected $sNomeArquivo = 'LiquidacaoRestosPagarFolhaPagamento';

    public function gerarDados()
    {
        $campos = [
            'e60_numemp', 'e60_codemp', 'e60_anousu',
            'o58_orgao', 'o58_unidade',
            'e69_codnota', 'e69_anousu',
            'e50_obs', 'e50_codord',
            'e70_valor',
            'c70_data',
            'e164_data',
            'conta',
            'e177_decimo'
        ];
        $empenhos = DB::table('empresto')
            ->select($campos)
            ->join('empenho.empempenho', 'e60_numemp', 'e91_numemp')
            ->join('empnota', 'e69_numemp', 'e60_numemp')
            ->join('empnotaele', 'e70_codnota', 'e69_codnota')
            ->join('pagordem', 'e50_numemp', 'e60_numemp')
            ->join('pagordemnota', function (JoinClause $join) {
                $join->on('e71_codnota', 'e69_codnota')
                    ->on('e71_codord', 'e50_codord');
            })
            ->join('conlancamnota', 'c66_codnota', 'e69_codnota')
            ->join('conlancam', 'c70_codlan', 'c66_codlan')
            ->join('conlancamdoc', 'c71_codlan', 'c70_codlan')
            ->join('conhistdoc', function (JoinClause $join) {
                $join->on('c53_coddoc', 'c71_coddoc')
                ->where('c53_tipo', 20);
            })
            ->join('orcdotacao', function (JoinClause $join) {
                $join->on('o58_anousu', 'e60_anousu')
                    ->on('o58_coddot', 'e60_coddot');
            })
            ->join('empelemento', 'e64_numemp', 'e60_numemp')
            ->join('conplanoorcamento', function (JoinClause $join) {
                $join->on('c60_codcon', 'e64_codele')
                    ->whereRaw('c60_anousu = (case when e60_anousu < 2024 then 2024 else e60_anousu end)');
            })
            ->join('planodespesaconplanoorcamento', 'conplanoorcamento_codigo', 'c60_codigo')
            ->join('planodespesa', function (JoinClause $join) {
                $join->on('planodespesa.id', 'planodespesa_id')
                    ->whereRaw('c60_anousu = (case when e60_anousu < 2024 then 2024 else e60_anousu end)');
            })
            ->join('empnotasigfistipodocliquidacao', 'e178_empnota', 'e69_codnota')
            ->join('sigfistipodocliquidacao', 'e177_sequencial', 'e178_sigfistipodocliquidacao')
            ->leftJoin('empcompetencialiquidacao', 'e164_codord', 'e50_codord')
            ->where('e91_anousu', $this->iAnoUsu)
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->whereBetween('e69_dtinclusao', [$this->dtDataInicial, $this->dtDataFinal])
            ->where('uniao', '=', $this->getUtilizaPlanoUniao())
            ->where('e177_codigo', 2)
            ->orderBy('e60_numemp')
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception(sprintf(
                'Não foi encontrado nenhum registro para o arquivo da Remessa de %s',
                'Restos a Pagar - Liquidação - Folha Pagamento'
            ));
        }

        $obj = new stdClass();
        $obj->LiquidacoesRestosPagarFolhasPagamentos = [];

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
                "Identificador" => $empenho->e69_codnota,
                "CodigoUnidadeGestora" => $this->sCodigoTribunal,
                "NumeroEmpenho" => $empenho->e60_codemp,
                "AnoEmpenho" => $empenho->e60_anousu,
                "Competencia" => $this->competencia,
                "NumeroLiquidacaoRestosPagar" => $empenho->e50_codord,
                "NumeroFolhaPagamento" => str_pad($numeroFolha[$tipo], 2, '0', STR_PAD_LEFT),
                "AnoReferencia" => $anoFolha,
                "MesReferencia" => $mesFolha,
                "Valor" => $empenho->e70_valor,
                "Objeto" => Helper::convertAndLimit("<![CDATA[".$empenho->e50_obs, 209),
                "AnoLiquidacaoRestosPagar" => (new Carbon($empenho->c70_data))->format('Y'),
                "CodigoOrgao" => $empenho->o58_orgao,
                "CodigoUnidadeOrcamentaria" => $empenho->o58_unidade,
                "CodigoUnidadeGestoraResponsavel" => $this->getUgResponsavelFolha(),
                "CodigoTipoFolhaPagamento" => $tipoFolha,
            ];
            $obj->LiquidacoesRestosPagarFolhasPagamentos[] = (object)['LiquidacaoRestosPagarFolhaPagamento' => $data];
        }

        $this->aDados = $obj;
    }
}
