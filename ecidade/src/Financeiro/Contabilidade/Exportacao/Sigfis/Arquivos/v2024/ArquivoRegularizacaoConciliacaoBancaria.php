<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Illuminate\Database\Capsule\Manager as DB;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Eloquent\Builder;
use \stdClass;

class ArquivoRegularizacaoConciliacaoBancaria extends ArquivoBase
{
    protected $sNomeArquivo  = 'RegularizacaoConciliacaoBancaria';
    /**
     *  Busca os dados para gerar o Arquivo de Unidade Orcamentaria
     */
    public function gerarDados()
    {


        $regularizacoesConciliacao = [];

        $camposRegularizacaoTesouraria = [
            DB::raw('min(conciliapendcorrente.k89_concilia) as conciliacao_pendencia'),
            DB::raw('db83_sequencial as conta_bancaria'),
            DB::raw("extract(year from k89_data) as exercicio_pendencia"),
            DB::raw("extract(year from k68_data) as ano_conciliacao"),
            DB::raw('k68_data as data_regularizacao'),
            DB::raw('k12_valor as valor_regularizacao'),
            DB::raw("
                case when k12_histcor is not null then k12_histcor
                     when e60_resumo is not null then e60_resumo
                     when k17_texto is not null then k17_texto
                     else '' END as justificativa_regularizacao
            "),
            DB::raw("
            case
                when slip.k17_codigo is not null
                    then
                        case
                            when (k17_credito = corrente.k12_conta
                                  and exists (select 1
                                                from conplanocontabancaria
                                               where k17_credito = c56_reduz
                                                 and c56_contabancaria = db83_sequencial ))
                                then
                                    case
                                        when k12_estorn is true
                                            then 1
                                        else 4
                                    end
                            when (k17_debito = corlanc.k12_conta
                                  and exists (select 1
                                                from conplanocontabancaria
                                               where k17_debito = c56_reduz
                                                 and c56_contabancaria = db83_sequencial ))
                                then
                                    case
                                        when k12_estorn is false
                                            then 1
                                        else 4
                                    end
                            else 0
                end
                when coremp.k12_empen is not null
                    then
                        case
                            when corrente.k12_estorn is true
                                then 1
                            else 4
                        end
                when corplacaixa.k82_seqpla is not null
                    then
                        case
                            when corrente.k12_estorn is false
                                then 1
                            else 4
                        end
            else
                case
                    when corrente.k12_estorn is false
                        then 1
                    else 4
                end
            end as tipo
            ")
        ];

        $groupBy = [
                'k84_sequencial',
                'k68_data',
                'k89_data',
                'k12_valor',
                'k12_histcor',
                'e60_resumo',
                'k17_texto',
                'slip.k17_codigo',
                'k12_estorn',
                'db83_sequencial',
                'corrente.k12_conta',
                'corlanc.k12_conta',
                'k82_seqpla',
                'k12_empen'

        ];

        $regularizacoesTesouraria = db::table('concilia')
        ->select($camposRegularizacaoTesouraria)
        ->join('contabancaria', 'db83_sequencial', 'k68_contabancaria')
        ->join('conplanocontabancaria', 'c56_contabancaria', 'k68_contabancaria')
        ->join('conciliaitem', 'k83_concilia', 'k68_sequencial')
        ->join('conciliacor', 'k84_conciliaitem', 'k83_sequencial')
        ->join('conciliapendcorrente', function ($join) {
            $join->on('conciliapendcorrente.k89_id', 'conciliacor.k84_id')
            ->on('conciliapendcorrente.k89_data', 'conciliacor.k84_data')
            ->on('conciliapendcorrente.k89_autent', 'conciliacor.k84_autent');
        })
        ->join('corrente', function ($join) {
            $join->on('corrente.k12_id', 'conciliacor.k84_id')
            ->on('corrente.k12_data', 'conciliacor.k84_data')
            ->on('corrente.k12_autent', 'conciliacor.k84_autent');
        })
        ->leftJoin('corhist', function ($join) {
            $join->on('corhist.k12_id', 'conciliacor.k84_id')
            ->on('corhist.k12_data', 'conciliacor.k84_data')
            ->on('corhist.k12_autent', 'conciliacor.k84_autent');
        })
        ->leftJoin('corlanc', function ($join) {
            $join->on('corlanc.k12_id', 'conciliacor.k84_id')
            ->on('corlanc.k12_data', 'conciliacor.k84_data')
            ->on('corlanc.k12_autent', 'conciliacor.k84_autent');
        })
        ->leftJoin('slip', 'slip.k17_codigo', 'corlanc.k12_codigo')
        ->leftJoin('coremp', function ($join) {
            $join->on('coremp.k12_id', 'conciliacor.k84_id')
            ->on('coremp.k12_data', 'conciliacor.k84_data')
            ->on('coremp.k12_autent', 'conciliacor.k84_autent');
        })
        ->leftJoin('empempenho', 'e60_numemp', 'coremp.k12_empen')
        ->leftJoin('corplacaixa', function ($join) {
            $join->on('corplacaixa.k82_id', 'conciliacor.k84_id')
            ->on('corplacaixa.k82_data', 'conciliacor.k84_data')
            ->on('corplacaixa.k82_autent', 'conciliacor.k84_autent');
        })
        ->where('k84_data', '<', $this->dtDataInicial)
        ->whereBetween('k68_data', [$this->dtDataInicial,$this->dtDataFinal])
        ->whereIn('db83_instit', $this->getListaInstituicoes())
        ->groupBy($groupBy);

        $camposRegularizacaoExtrato = [
            DB::raw('(select min(k68_sequencial) 
                        from concilia 
                       where k68_contabancaria = db83_sequencial 
                         and k68_data = k86_data ) as conciliacao_pendencia'),
            DB::raw('db83_sequencial as conta_bancaria'),
            DB::raw("extract(year from k86_data) as exercicio_pendencia"),
            DB::raw("extract(year from k68_data) as ano_conciliacao"),
            DB::raw('k68_data as data_regularizacao'),
            DB::raw('k86_valor as valor_regularizacao'),
            DB::raw('k86_observacao as justificativa_regularizacao'),
            DB::raw("case when k86_tipo = 'D' then 5 when k86_tipo = 'C' then 2 else null end as tipo")
        ];

        $groupBy = [
            'k86_sequencial',
            'k68_data',
            'k86_data',
            'k86_valor',
            'k86_observacao',
            'k86_tipo',
            'db83_sequencial'
        ];

        $regularizacoesExtrato = db::table('concilia')
        ->select($camposRegularizacaoExtrato)
        ->join('contabancaria', 'db83_sequencial', 'k68_contabancaria')
        ->join('conciliaitem', 'k83_concilia', 'k68_sequencial')
        ->join('conciliaextrato', 'k87_conciliaitem', 'k83_sequencial')
        ->join('extratolinha', 'k86_sequencial', 'k87_extratolinha')
        ->join('conciliapendextrato', 'k88_extratolinha', 'k86_sequencial')
        ->where('k86_data', '<', $this->dtDataInicial)
        ->where('k86_valor', '>', 0)
        ->whereBetween('k68_data', [$this->dtDataInicial,$this->dtDataFinal])
        ->whereIn('db83_instit', $this->getListaInstituicoes())
        ->groupBy($groupBy);

        $regularizacoesGerais = $regularizacoesTesouraria->unionAll($regularizacoesExtrato);

        $regularizacoesConciliacao = DB::table(DB::raw("({$regularizacoesGerais->toSql()}) as sub"))
        ->mergeBindings($regularizacoesGerais)
        ->selectRaw("
        conciliacao_pendencia,
        conta_bancaria,
        tipo,
        exercicio_pendencia,
        ano_conciliacao,
        data_regularizacao,
        sum(valor_regularizacao) as valor_regularizacao,
        array_to_string(array_accum(justificativa_regularizacao), ' ; ') as justificativa_regularizacao
        ")
        ->groupBy([
            "conciliacao_pendencia",
            "conta_bancaria",
            "tipo",
            "exercicio_pendencia",
            "ano_conciliacao",
            "data_regularizacao"
        ])
        ->orderBy('conciliacao_pendencia')
        ->orderBy('data_regularizacao')
        ->get();

        if ($regularizacoesConciliacao->isEmpty()) {
            throw new \Exception("Nenhum registro encontrado para o período");
        }
        
        $obj = new stdClass();
        $obj->RegularizacoesConciliacaoBancaria = [];

        foreach ($regularizacoesConciliacao as $regularizacaoConciliacao) {
            $numeroConciliacao = $regularizacaoConciliacao->conciliacao_pendencia.$regularizacaoConciliacao->tipo;

            $dadosRegularizacao = (object)[
                'Identificador' => $numeroConciliacao,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'Exercicio' => $regularizacaoConciliacao->exercicio_pendencia,
                'Competencia' => $this->competencia,
                'AnoConciliacaoBancaria' =>  $regularizacaoConciliacao->ano_conciliacao,
                'NumeroConciliacaoBancaria' => $numeroConciliacao,
                'DataRegularizacao' => $regularizacaoConciliacao->data_regularizacao,
                'ValorRegularização' => abs($regularizacaoConciliacao->valor_regularizacao),
                'Descricao' => Helper::convertAndLimit(
                    "<![CDATA[".$regularizacaoConciliacao->conta_bancaria
                    ." - ".trim($regularizacaoConciliacao->justificativa_regularizacao),
                    264
                )

            ];

            $obj->RegularizacoesConciliacaoBancaria[] = (object)[
                'RegularizacaoConciliacaoBancaria' => $dadosRegularizacao
            ];
        }

        $this->aDados =  $obj;
    }
}
