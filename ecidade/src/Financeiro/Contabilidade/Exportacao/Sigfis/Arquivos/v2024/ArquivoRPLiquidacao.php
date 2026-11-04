<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

class ArquivoRPLiquidacao extends ArquivoBase
{
    protected $sNomeArquivo = 'LiquidacaoRestosPagar';

    public function gerarDados()
    {
        $campos = [
            'e60_numemp', 'e60_anousu', 'e60_codemp',
            'e91_anousu',
            'o58_orgao', 'o58_unidade',
            'c70_codlan', 'c70_data', 'c70_valor',
            'e50_codord',
            'e177_codigo',
            'planodespesa.conta',
            'z01_numcgm as cgm_responsaval', 'z01_cgccpf as cpf_responsaval'
        ];

        $empenhos = DB::table('empresto')
            ->select($campos)
            ->join('empenho.empempenho', 'e60_numemp', 'e91_numemp')
            ->join('conlancamemp', 'c75_numemp', 'e60_numemp')
            ->join('conlancam', 'c70_codlan', 'c75_codlan')
            ->join('conlancamdoc', 'c71_codlan', 'c70_codlan')
            ->join('conhistdoc', function (JoinClause $join) {
                $join->on('c53_coddoc', 'c71_coddoc')
                ->where('c53_tipo', 20);
            })
            ->join('conlancamnota', 'c66_codlan', 'c70_codlan')
            ->join('conlancamord', 'c80_codlan', 'c70_codlan')
            ->join('orcamento.orcdotacao', function (JoinClause $join) {
                $join->on('e60_anousu', 'o58_anousu')
                    ->on('e60_coddot', 'o58_coddot');
            })
            ->join('empnota', function (JoinClause $join) {
                $join->on('empnota.e69_numemp', 'empempenho.e60_numemp')
                        ->on('e69_codnota', 'c66_codnota');
            })
            ->join('empnotaele', 'e70_codnota', 'e69_codnota')
            ->join('pagordem', 'e50_codord', 'c80_codord')
            ->join('pagordemnota', function (JoinClause $join) {
                $join->on('e71_codnota', 'e69_codnota')
                    ->on('e71_codord', 'e50_codord');
            })
            ->join('db_usuacgm', 'id_usuario', 'e50_id_usuario')
            ->join('cgm', 'z01_numcgm', 'cgmlogin')
            ->join('empelemento', 'e64_numemp', 'e60_numemp')
            ->join('conplanoorcamento', function (JoinClause $join) {
                $join->on('c60_codcon', 'e64_codele')
                    ->whereRaw('c60_anousu = (case when e60_anousu < 2024 then 2024 else e60_anousu end)');
            })
            ->join('planodespesaconplanoorcamento', 'conplanoorcamento_codigo', 'c60_codigo')
            ->join('planodespesa', function (JoinClause $join) {
                $join->on('planodespesa.id', 'planodespesa_id')
                    ->on('exercicio', 'c60_anousu')
                    ->where('uniao', '=', $this->getUtilizaPlanoUniao());
            })
            ->leftJoin(
                'empnotasigfistipodocliquidacao',
                'empnotasigfistipodocliquidacao.e178_empnota',
                'empnota.e69_codnota'
            )
            ->leftJoin(
                'sigfistipodocliquidacao',
                'sigfistipodocliquidacao.e177_sequencial',
                'empnotasigfistipodocliquidacao.e178_sigfistipodocliquidacao'
            )
            ->where('e91_anousu', $this->iAnoUsu)
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->whereBetween('c70_data', [$this->dtDataInicial, $this->dtDataFinal])
            ->orderBy('e60_numemp')
            ->orderBy('e50_codord')
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception(sprintf(
                'Não foi encontrado nenhum registro para o arquivo da Remessa de %s',
                'Restos a Pagar - Inscrição'
            ));
        }

        $obj = new stdClass();
        $obj->LiquidacoesRestosPagar = [];
        foreach ($empenhos as $empenho) {
            $responsavel = (object)[
                'Identificador' => $empenho->cgm_responsaval,
                'CPFResponsavel' => $empenho->cpf_responsaval,
            ];

            $subelemento = (object)[
                'Identificador' => $empenho->e50_codord,
                'Valor' => $empenho->c70_valor,
                'NaturezaDespesa' => $empenho->conta,
            ];

            $data = (object)[
                'Identificador' => $empenho->c70_codlan,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'Competencia' => $this->competencia,
                'NumeroEmpenho' => $empenho->e60_codemp,
                'AnoEmpenho' => $empenho->e60_anousu,
                'NumeroNotaLiquidacao' => $empenho->e50_codord,
                'AnoLiquidacao' => $empenho->e91_anousu,
                'DataLiquidacao' => $empenho->c70_data,
                'ValorLiquidacao' => $empenho->c70_valor,
                'TipoDocumento' => $empenho->e177_codigo,
                'CodigoOrgao' => $empenho->o58_orgao,
                'CodigoUnidadeOrcamentaria' => $empenho->o58_unidade,
                'ResponsaveisLiquidacao' => [(object)['Responsavel' => $responsavel]],
                'SubElementos' => [(object)['LiquidacaoRestosPagarSubelemento' => $subelemento]],
                'ConsignacoesRetencoes' => null
            ];

            $obj->LiquidacoesRestosPagar[] = (object)['LiquidacaoRestosPagar' => $data];
        }

        $this->aDados = $obj;
    }
}
