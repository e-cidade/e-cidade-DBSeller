<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Illuminate\Database\Capsule\Manager as DB;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Query\JoinClause;

class ArquivoLiquidacaoEmpenhoAdiantamento extends ArquivoBase
{
    protected $sNomeArquivo = 'LiquidacaoEmpenhoAdiantamento';

    /**
     * Busca os dados para gerar o Arquivo de Unidade Orçamentária
     */
    public function gerarDados()
    {
        $campos = [
            'e03_numeroprocesso',
            'e50_data', 'e50_anousu', 'e50_obs',
            'o58_orgao', 'o58_unidade',
            'e60_codemp', 'e60_anousu', 'e60_numemp', 'e60_numcgm', 'e69_codnota', 'e71_codord',
            'c70_codlan', 'c70_valor',
            'cgm_empenho.z01_cgccpf as cpf_responsavel',
            'cgm_empenho.z01_nome as nome_responsavel',
            'cgm_conta.z01_cgccpf as cpf_servidor',
            'cgm_conta.z01_nome as nome_servidor'
        ];

        $empenhos = DB::table('empempenho')
            ->select($campos)
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
            ->join('conlancamord', 'c80_codord', 'e50_codord')
            ->join('conlancamdoc', 'c71_codlan', 'c80_codlan')
            ->join('conlancam', 'c70_codlan', 'c71_codlan')
            ->join('conhistdoc', function ($join) {
                        $join->on('c53_coddoc', 'c71_coddoc')
                        ->where('c53_tipo', 20);
            })
            ->leftJoin('cgm as cgm_empenho', 'cgm_empenho.z01_numcgm', 'e60_numcgm')
            ->leftJoin('cgm as cgm_conta', 'cgm_conta.z01_numcgm', 'e49_numcgm')
            ->join('orcdotacao', function ($join) {
                $join->on('e60_anousu', 'o58_anousu')
                    ->on('e60_coddot', 'o58_coddot');
            })
            ->where('e60_anousu', $this->iAnoUsu)
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->where('e177_codigo', '5')
            ->whereBetween('e69_dtinclusao', [$this->dtDataInicial, $this->dtDataFinal])
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception('Não foi encontrardo nenhum empenho para o arquivo de Remessa');
        }

        $obj = new \stdClass();
        $obj->LiquidacoesEmpenhoAdiantamentos = [];

        foreach ($empenhos as $empenho) {
            $dadosLiquidacaoEmpenho = (object)[
            'Identificador' => $empenho->c70_codlan,
            'CodigoUnidadeGestora' => $this->sCodigoTribunal,
            'NumeroEmpenho' => $empenho->e60_codemp,
            'AnoEmpenho' => $empenho->e60_anousu,
            'NumeroLiquidacaoEmpenho' => $empenho->e71_codord,
            'Competencia' => $this->competencia,
            'CpfResponsavel' => $empenho->cpf_responsavel,
            'CpfServidor' => !empty($empenho->cpf_servidor) ? $empenho->cpf_servidor : $empenho->cpf_responsavel,
            'NomeServidor' => Helper::convertAndLimit(
                (!empty($empenho->nome_servidor) ?
                "<![CDATA[".$empenho->nome_servidor : "<![CDATA[".$empenho->nome_responsavel )
            ),
                'NumeroProcessoAdministrativo' =>
                Helper::convertAndLimit("<![CDATA[".$empenho->e03_numeroprocesso),
                'ValorConcedido' => $empenho->c70_valor,
                'DataConcessao' => $empenho->e50_data,
                'DataLimite' => $empenho->e50_data,
                'Finalidade' => Helper::convertAndLimit("<![CDATA[".$empenho->e50_obs),
                'AnoLiquidacaoDeEmpenho' => $empenho->e50_anousu,
                'CodigoOrgao' => $empenho->o58_orgao,
                'CodigoUnidadeOrcamentaria' => $empenho->o58_unidade
            ];

            $obj->LiquidacoesEmpenhoAdiantamentos[] = (object)[
            'LiquidacaoEmpenhoAdiantamento' => $dadosLiquidacaoEmpenho
            ];
        }

        $this->aDados = $obj;
    }
}
