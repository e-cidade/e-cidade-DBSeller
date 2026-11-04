<?php

namespace App\Domain\Financeiro\Tesouraria\Services;

use App\Domain\Financeiro\Tesouraria\Models\TipoDesdobramento;
use Illuminate\Support\Facades\DB;

class ValoresDecendioService
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private $contasTesouraria;

    private $descricoesReceitas = [];

    public function getValoresReceita($exercicio, $instituicao, $dataInicial, $dataFinal)
    {
        $sql = "
        with lancamentos as (
            select substring (o57_fonte, 2) as fonte,
                   substring (o57_fonte, 1, 1) as classe,
                   c70_anousu,
                   c70_codlan,
                   c70_data,
                   c74_codrec,
                   c53_coddoc,
                   c53_coddoc,
                   c53_tipo,
                   o70_instit,
                   k222_codigo,
                   k222_descricao,
                   k222_percentual
               from caixa.receitadescendio
               join caixa.tipodesdobramento on k222_codigo = k224_tipodesdobramento
               join orcamento.orcreceita on (o70_anousu, o70_codrec) = (k224_anousu, k224_reduz)
               join orcamento.orcfontes on (o57_codfon, o57_anousu) = (o70_codfon, o70_anousu)
               join contabilidade.conlancamrec on (c74_anousu, c74_codrec) = (k224_anousu, k224_reduz)
               join contabilidade.conlancam on c70_codlan = c74_codlan
               join conlancamdoc ON c71_codlan = c70_codlan
               join conhistdoc ON c53_coddoc = c71_coddoc
               where k224_anousu = {$exercicio}
                 and o70_instit = {$instituicao}
                 and c70_data >= '{$dataInicial}'
                 and c70_data <= '{$dataFinal}'
                 and c53_tipo in (100, 101)
                 and c53_coddoc not in (6000, 6001, 6006, 6007)
                 and not exists(
                    select 1
                      from decendiolancamento
                     where k231_lancamento = c70_codlan and k231_desdobramento = k222_codigo
                 )
        ), debitos as (
            select lancamentos.*,
                   c69_valor::numeric as valor,
                   c60_estrut,
                   c60_descr,
                   c61_reduz,
                   c61_codigo
              from lancamentos
              join contabilidade.conlancamval on c69_codlan = c70_codlan
                   and c69_ordem = 1
              join contabilidade.conplanoreduz on c61_reduz = c69_debito
                   and c61_anousu = c70_anousu
              join contabilidade.conplano on (c60_codcon, c60_anousu) = (c61_codcon, c61_anousu)
              join orcamento.orctiporec o on o.o15_codigo = c61_codigo
              join orcamento.fonterecurso fr on fr.orctiporec_id = o.o15_codigo
                and fr.exercicio = c70_anousu
           where c53_tipo = 100
             and codigo_siconfi in ('1500', '1501', '1502', '2500', '2501', '2502')
             and o15_complemento = 0
        ), creditos as (
            select lancamentos.*,
                   (c69_valor *-1)::numeric as valor,
                   c60_estrut,
                   c60_descr,
                   c61_reduz,
                   c61_codigo
              from lancamentos
              join contabilidade.conlancamval on c69_codlan = c70_codlan
                   and c69_ordem = 1
              join contabilidade.conplanoreduz on c61_reduz = c69_credito
                   and c61_anousu = c70_anousu
              join contabilidade.conplano on (c60_codcon, c60_anousu) = (c61_codcon, c61_anousu)
              join orcamento.orctiporec o on o.o15_codigo = c61_codigo
              join orcamento.fonterecurso fr on fr.orctiporec_id = o.o15_codigo
                   and fr.exercicio = c70_anousu
            where c53_tipo = 101
              and codigo_siconfi in ('1500', '1501', '1502', '2500', '2501', '2502')
              and o15_complemento = 0
        ), unifica as (
            select * from debitos
            union
            select * from creditos
        ), totaliza as (
            select c70_anousu as exercicio,
                   k222_codigo as codigo_desdobramento,
                   k222_descricao as descricao_desdobramento,
                   k222_percentual as percentual_desdobramento,
                   o70_instit as instituicao,
                   fonte as estrutural_receita,
                   c60_estrut as estrutural_conta_bancaria,
                   c60_descr as descricao_conta_bancaria,
                   c61_reduz reduzido_conta_bancaria,
                   c61_codigo,
                   array_to_string(array_agg(distinct c74_codrec), ',') as receitas,
                   array_to_string(array_agg(distinct classe), ',') as classes,
                   sum(valor) as valor,
                   array_to_string(array_agg(c70_codlan), ',') as lancamentos
              from unifica
            group by c70_anousu, k222_codigo, k222_descricao, k222_percentual, o70_instit, fonte,
                     c60_estrut, c60_descr, c61_reduz, c61_codigo
        )
        select * from totaliza
         order by estrutural_receita, codigo_desdobramento;
        ";

        $valores = DB::select($sql);
        $desdobramentos = TipoDesdobramento::with('recursos')->get();

        $retornar = [];
        foreach ($valores as $valor) {
            if ($valor->valor == 0) {
                continue;
            }

            $retornar[] = $this->montaObjetoRetorno($valor, $desdobramentos);
        }

        return $retornar;
    }

    public function processarValoresDescendio($exercicio, $instituicao, $dataInicial, $dataFinal)
    {
        $this->getContas($exercicio, $instituicao);
        $this->processaDescricoesReceitas($exercicio);
        return $this->getValoresReceita($exercicio, $instituicao, $dataInicial, $dataFinal);
    }

    private function montaObjetoRetorno($valor, $desdobramentos)
    {
        $copia = [
            "receita" => (object)[
                "reduzidos" => $valor->receitas,
                "estrutural" => $valor->estrutural_receita,
                "classes" => $valor->classes,
                "descricao" => $this->descricaoReceita($valor->estrutural_receita, $valor),
            ],
            "conta_pagadora" => (object)[
                "reduzido" => $valor->reduzido_conta_bancaria,
                "estrutural" => $valor->estrutural_conta_bancaria,
                "descricao" => $valor->descricao_conta_bancaria,
                "recurso" => $valor->c61_codigo,
            ],
            'valor' => $valor->valor,
            'instituicao' => $valor->instituicao,
            'lancamentos' => explode(',', $valor->lancamentos)
        ];

        $recursos = $desdobramentos->filter(function (TipoDesdobramento $desdobramento) use ($valor) {
            return $valor->codigo_desdobramento === $desdobramento->k222_codigo;
        })->map(function (TipoDesdobramento $desdobramento) {
            return $desdobramento->recursos->map(function ($recurso) {
                return $recurso->o15_codigo;
            });
        })->shift()->toArray();

        $copia['desdobramento'] = (object)[
            'codigo' => $valor->codigo_desdobramento,
            'percentual' => $valor->percentual_desdobramento,
            'descricao' => $valor->descricao_desdobramento,
            'valor' => round($valor->valor * ($valor->percentual_desdobramento / 100), 2),
            'recursos' => $recursos,
            'contasBancarias' => $this->contasCompativeis($recursos)
        ];

        return (object)$copia;
    }

    /**
     * Retorna as contas da tesouraria
     * @param $exercicio
     * @param $instituicao
     * @return void
     */
    private function getContas($exercicio, $instituicao)
    {
        $filtros = [
            'comReduzidos' => 1,
            'DB_anousu' => $exercicio,
            'DB_instit' => $instituicao,
        ];

        $service = new ContasService();
        $this->contasTesouraria = $service->get($filtros);
    }

    /**
     * Retorna as contas da tesouraria
     * @param array $recursos lista de recursos
     * @return array
     */
    private function contasCompativeis($recursos)
    {
        return array_values($this->contasTesouraria->filter(function ($conta) use ($recursos) {
            return in_array($conta->o15_codigo, $recursos);
        })->map(function ($conta) {
            return (object)[
                "conta" => $conta->k13_conta,
                "nome" => $conta->k13_descr,
            ];
        })->toArray());
    }


    private function getDescricoesReceitas($exercicio, $filtrarClasse = true, $reduzidos = null)
    {
        $filtros = ["k224_anousu = {$exercicio}"];
        if ($filtrarClasse) {
            $filtros[] = "substring(o57_fonte, 1, 1) = '4'";
        }
        if (!is_null($reduzidos)) {
            $filtros[] = "k224_reduz in ($reduzidos)";
        }

        $sql = "
          select o57_fonte, o57_descr
            from caixa.receitadescendio
            join caixa.tipodesdobramento on k222_codigo = k224_tipodesdobramento
            join orcamento.orcreceita on (o70_anousu, o70_codrec) = (k224_anousu, k224_reduz)
            join orcamento.orcfontes on (o57_codfon, o57_anousu) = (o70_codfon, o70_anousu)
          where
        ";
        $sql .= implode(' and ', $filtros);

        return DB::select($sql);
    }

    private function processaDescricoesReceitas($exercicio)
    {
        $receitas = $this->getDescricoesReceitas($exercicio);
        foreach ($receitas as $receita) {
            $this->descricoesReceitas[$receita->o57_fonte] = $receita->o57_descr;
        }
    }

    private function descricaoReceita($estrutural, $dados)
    {
        $key = "4$estrutural";
        if (array_key_exists("{$key}", $this->descricoesReceitas)) {
            return $this->descricoesReceitas[$key];
        }

        $receitas = $this->getDescricoesReceitas($dados->exercicio, false, $dados->receitas);

        return array_values($receitas)[0]->o57_descr;
    }

    public function buscaDetalhamentoReceitaDecendio(array $filtros)
    {
        $sql = "
         with lancamentos as (
            select o57_fonte as fonte,
                   c70_anousu,
                   c70_codlan,
                   c70_data,
                   c74_codrec,
                   c53_coddoc,
                   c53_coddoc,
                   c53_tipo,
                   o70_instit,
                   k222_codigo,
                   k222_descricao,
                   k222_percentual
               from caixa.receitadescendio
               join caixa.tipodesdobramento on k222_codigo = k224_tipodesdobramento
               join orcamento.orcreceita on (o70_anousu, o70_codrec) = (k224_anousu, k224_reduz)
               join orcamento.orcfontes on (o57_codfon, o57_anousu) = (o70_codfon, o70_anousu)
               join contabilidade.conlancamrec on (c74_anousu, c74_codrec) = (k224_anousu, k224_reduz)
               join contabilidade.conlancam on c70_codlan = c74_codlan
               join conlancamdoc ON c71_codlan = c70_codlan
               join conhistdoc ON c53_coddoc = c71_coddoc
               where k224_anousu = {$filtros['exercicio']}
                 and o70_instit = {$filtros['instituicao']}
                 and c70_data >= '{$filtros['dataInicial']}'
                 and c70_data <= '{$filtros['dataFinal']}'
                 and c53_tipo in (100, 101)
                 and c53_coddoc not in (6000, 6001, 6006, 6007)
                 and k222_codigo = {$filtros['desdobramento']}
                 and c74_codrec in ({$filtros['reduzidos']})
                 and not exists(
                    select 1
                      from decendiolancamento
                     where k231_lancamento = c70_codlan and k231_desdobramento = k222_codigo
                 )
        ), debitos as (
            select lancamentos.*,
                   c69_valor::numeric as valor,
                   c60_estrut,
                   c60_descr,
                   c61_reduz,
                   c61_codigo
              from lancamentos
              join contabilidade.conlancamval on c69_codlan = c70_codlan
                   and c69_ordem = 1
              join contabilidade.conplanoreduz on c61_reduz = c69_debito
                   and c61_anousu = c70_anousu
              join contabilidade.conplano on (c60_codcon, c60_anousu) = (c61_codcon, c61_anousu)
           where c53_tipo = 100
             and c61_reduz = {$filtros['conta_pagadora']}
        ), creditos as (
            select lancamentos.*,
                   (c69_valor *-1)::numeric as valor,
                   c60_estrut,
                   c60_descr,
                   c61_reduz,
                   c61_codigo
              from lancamentos
              join contabilidade.conlancamval on c69_codlan = c70_codlan
                   and c69_ordem = 1
              join contabilidade.conplanoreduz on c61_reduz = c69_credito
                   and c61_anousu = c70_anousu
              join contabilidade.conplano on (c60_codcon, c60_anousu) = (c61_codcon, c61_anousu)
            where c53_tipo = 101
              and c61_reduz = {$filtros['conta_pagadora']}
        ), unifica as (
            select * from debitos
            union
            select * from creditos
        ), totaliza as (
            select c70_anousu as exercicio,
                   k222_descricao as descricao_desdobramento,
                   k222_percentual as percentual_desdobramento,
                   o70_instit as instituicao,
                   fonte as estrutural_receita,
                   c60_estrut as estrutural_conta_bancaria,
                   c60_descr as descricao_conta_bancaria,
                   c61_reduz reduzido_conta_bancaria,
                   sum(valor) as valor,
                   array_to_string(array_agg(c70_codlan), ',') as lancamentos
              from unifica
            group by c70_anousu, k222_descricao, k222_percentual, o70_instit, fonte,
                     c60_estrut, c60_descr, c61_reduz
        )
        select * from totaliza
         order by estrutural_receita;
        ";

        $dados = DB::select($sql);
        $retornar = [];
        foreach ($dados as $dado) {
            $retornar[] = (object)[
                'desdobramento' => $dado->descricao_desdobramento,
                'estrutural' => $dado->estrutural_receita,
                'conta_bancaria' => (object)[
                    'estrutural' => $dado->estrutural_receita,
                    'nome' => $dado->descricao_conta_bancaria,
                    'reduzido' => $dado->reduzido_conta_bancaria,
                ],
                'valor' => $dado->valor,
                'valor_desdobramento' => round($dado->valor * ($dado->percentual_desdobramento / 100), 2),
            ];
        }
        return $retornar;
    }
}
