<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Carbon\Carbon;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

class ArquivoRPLiquidacaoAdiantamento extends ArquivoBase
{
    protected $sNomeArquivo = 'LiquidacaoRestosPagarAdiantamento';

    public function gerarDados()
    {
        $campos = [
            'e03_numeroprocesso',
            'e50_data', 'e50_obs', 'e50_anousu', 'e50_codord',
            'o58_orgao', 'o58_unidade',
            'e60_codemp', 'e60_anousu', 'e60_numemp', 'e60_numcgm',
            'e69_codnota',
            'e70_valor',
            'c70_data',
            'cgm_empenho.z01_cgccpf as cpf_responsavel',
            'cgm_empenho.z01_nome as nome_responsavel',
            'cgm_conta.z01_cgccpf as cpf_servidor',
            'cgm_conta.z01_nome as nome_servidor'
        ];

        $empenhos = DB::table('empresto')
            ->select($campos)
            ->join('empempenho', 'e60_numemp', 'e91_numemp')
            ->join('empnota', 'empnota.e69_numemp', 'empempenho.e60_numemp')
            ->join('empnotasigfistipodocliquidacao', 'e178_empnota', 'e69_codnota')
            ->join('sigfistipodocliquidacao', 'e177_sequencial', 'e178_sigfistipodocliquidacao')
            ->join('empnotaele', 'e70_codnota', 'e69_codnota')
            ->join('pagordem', 'e50_numemp', 'e69_numemp')
            ->leftJoin('pagordemprocesso', 'e03_pagordem', 'e50_codord')
            ->leftJoin('pagordemconta', 'e49_codord', 'e50_codord')
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
            ->leftJoin('cgm AS cgm_empenho', 'cgm_empenho.z01_numcgm', 'e60_numcgm')
            ->leftJoin('cgm AS cgm_conta', 'cgm_conta.z01_numcgm', 'e49_numcgm')
            ->join('orcdotacao', function ($join) {
                $join->on('e60_anousu', 'o58_anousu')
                    ->on('e60_coddot', 'o58_coddot');
            })
            ->where('e91_anousu', $this->iAnoUsu)
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->where('e177_codigo', '5')
            ->whereBetween('e69_dtinclusao', [$this->dtDataInicial, $this->dtDataFinal])
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception(sprintf(
                'Não foi encontrado nenhum registro para o arquivo da Remessa de %s',
                'Restos a Pagar - Liquidação - Adiantamento'
            ));
        }

        $obj = new stdClass();
        $obj->LiquidacoesRestosPagarAdiantamentos = [];

        foreach ($empenhos as $empenho) {
            $nomeServidor = !empty($empenho->nome_servidor) ? $empenho->nome_servidor : $empenho->nome_responsavel;
            $dadosLiquidacaoEmpenho = (object)[
                'Identificador' => $empenho->e69_codnota,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'NumeroEmpenho' => $empenho->e60_codemp,
                'AnoEmpenho' => $empenho->e60_anousu,
                'NumeroLiquidacaoRestosPagar' => $empenho->e50_codord,
                'Competencia' => $this->competencia,
                'CpfResponsavel' => $empenho->cpf_responsavel,
                'CpfServidor' => !empty($empenho->cpf_servidor) ? $empenho->cpf_servidor :
                    $empenho->cpf_responsavel,
                'NomeServidor' => Helper::convertAndLimit("<![CDATA[".$nomeServidor, 59),
                'Valor' => $empenho->e70_valor,
                'DataConcessao' => $empenho->e50_data,
                'DataLimite' => $empenho->e50_data,
                'Finalidade' => Helper::convertAndLimit("<![CDATA[".$empenho->e50_obs, 264),
                'AnoLiquidacaoRestosPagar' =>  (new Carbon($empenho->c70_data))->format('Y'),
                'CodigoOrgao' => $empenho->o58_orgao,
                'CodigoUnidadeOrcamentaria' => $empenho->o58_unidade,
                'ProcessoAdministrativo' => $empenho->e03_numeroprocesso,
            ];

            $obj->LiquidacoesRestosPagarAdiantamentos[] = (object)[
                'LiquidacaoRestosPagarAdiantamento' => $dadosLiquidacaoEmpenho
            ];
        }

        $this->aDados = $obj;
    }
}
