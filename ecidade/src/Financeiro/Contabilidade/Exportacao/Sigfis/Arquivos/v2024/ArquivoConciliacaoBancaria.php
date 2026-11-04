<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Illuminate\Database\Capsule\Manager as DB;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use stdClass;

class ArquivoConciliacaoBancaria extends ArquivoBase
{
    protected $sNomeArquivo  = 'ConciliacaoBancaria';
    protected $objetoConciliacao;
    protected $conciliacoesProcessadas;

    public function gerarDados()
    {
        $session = DB::select("select fc_putsession('DB_anousu','$this->iAnoUsu')");

        $camposConciliacao = [
        'db83_sequencial',
        'db90_codban',
        'db89_codagencia',
        'db83_conta',
        'db83_descricao',
        DB::raw("max(k68_sequencial) as ultima_conciliacao"),
        DB::raw("max(k68_data) as ultima_data_conciliacao")
        ];

        $groupBy = [
            'db83_sequencial',
            'db90_codban',
            'db89_codagencia',
            'db83_conta',
            'db83_descricao'
        ];

        /*
         * Quando o periodo for 0, buscar a ultima data anterior ao exercício atual e utilizar essa data
         * para buscar os registros de pendencia
         *
         * Nos outros períodos as datas inicial e final são respeitadas
         */
        if ($this->mesCompetencia == "0") {
            $wherePeriodo = " k68_data < '{$this->dtDataInicial}'";
        } else {
            $wherePeriodo = " k68_data between '$this->dtDataInicial' and '$this->dtDataFinal'";
        }

        $contasBancarias = db::table('contabancaria')
        ->select($camposConciliacao)
        ->join('bancoagencia', 'db89_sequencial', 'db83_bancoagencia')
        ->join('db_bancos', 'db90_codban', 'db89_db_bancos')
        ->join('concilia', 'k68_contabancaria', 'db83_sequencial')
        ->join('conplanocontabancaria', 'c56_contabancaria', 'db83_sequencial')
        ->WhereIn("db83_instit", $this->getListaInstituicoes())
        ->whereRaw($wherePeriodo)
        ->groupBy($groupBy)
        ->orderBy('db83_sequencial')
        ->get();

        if ($contasBancarias->isEmpty()) {
            throw new \Exception("Nenhum registro encontrado para o período");
        }
        
        $this->objetoConciliacao = new \stdClass();
        $this->objetoConciliacao->ConciliacoesBancarias = [];

        foreach ($contasBancarias as $contaBancaria) {
            $saldoExtrato = $this->getSaldoExtrato($contaBancaria);
                $pendencia = new stdClass;
                $pendencia->k68_sequencial = $contaBancaria->ultima_conciliacao;
                $pendencia->data = $contaBancaria->ultima_data_conciliacao;
                $pendencia->valor = $saldoExtrato;
                $this->montaObjetoConciliacao($contaBancaria, 3, $pendencia);

            $pendenciasExtrato = $this->getPendenciasExtrato($contaBancaria);
            foreach ($pendenciasExtrato as $pendenciaExtrato) {
                if ($pendenciaExtrato->tipo == 'D') {
                    $this->montaObjetoConciliacao($contaBancaria, 5, $pendenciaExtrato);
                } elseif ($pendenciaExtrato->tipo == 'C') {
                    $this->montaObjetoConciliacao($contaBancaria, 2, $pendenciaExtrato);
                }
            }

            $pendenciasTesouraria = $this->getPendenciasTesouraria($contaBancaria);
            foreach ($pendenciasTesouraria as $pendenciaTesouraria) {
                if ($pendenciaTesouraria->tipo == 'credito') {
                    $this->montaObjetoConciliacao($contaBancaria, 4, $pendenciaTesouraria);
                } elseif ($pendenciaTesouraria->tipo == 'debito') {
                    $this->montaObjetoConciliacao($contaBancaria, 1, $pendenciaTesouraria);
                }
            }

            $pendenciasExtratoExercicioAnterior = $this->getPendenciasExtratoExercicioAnterior($contaBancaria);
            foreach ($pendenciasExtratoExercicioAnterior as $pendenciaExtrato) {
                if ($pendenciaExtrato->tipo == 'D') {
                    $this->montaObjetoConciliacao($contaBancaria, 5, $pendenciaExtrato);
                } elseif ($pendenciaExtrato->tipo == 'C') {
                    $this->montaObjetoConciliacao($contaBancaria, 2, $pendenciaExtrato);
                }
            }

            $pendenciasTesourariaExercicioAnterior = $this->getPendenciasTesourariaExercicioAnterior($contaBancaria);
            foreach ($pendenciasTesourariaExercicioAnterior as $pendenciaTesouraria) {
                if ($pendenciaTesouraria->tipo == 'credito') {
                    $this->montaObjetoConciliacao($contaBancaria, 4, $pendenciaTesouraria);
                } elseif ($pendenciaTesouraria->tipo == 'debito') {
                    $this->montaObjetoConciliacao($contaBancaria, 1, $pendenciaTesouraria);
                }
            }
        }

        $this->processaDadosConciliacao();
        $this->objetoConciliacao->ConciliacoesBancarias = $this->conciliacoesProcessadas;
        $this->aDados =  $this->objetoConciliacao;
    }

    private function montaObjetoConciliacao(stdClass $contaBancaria, $tipo, $pendencia)
    {
        $dadosConciliacaoBancaria = (object)[
        'Identificador' => $pendencia->k68_sequencial.$tipo,
        'CodigoUnidadeGestora' => $this->sCodigoTribunal,
        'Exercicio' => date('Y', strtotime($pendencia->data)),
        'Competencia' => $this->competencia,
        'NumeroConciliacao' =>  $pendencia->k68_sequencial.$tipo,
        'DataConciliacao' => $pendencia->data,
        'Banco' => $contaBancaria->db90_codban,
        'Agencia' => $contaBancaria->db89_codagencia,
        'ContaBancaria' => $contaBancaria->db83_sequencial,
        'Tipo' => $tipo,
        'Descricao' => Helper::convertAndLimit("<![CDATA[".$contaBancaria->db83_descricao, 264),
        'Valor' => $pendencia->valor
        ];

        $this->objetoConciliacao->ConciliacoesBancarias[] = (object)[
        'ConciliacaoBancaria' => $dadosConciliacaoBancaria
        ];
    }

    private function getPendenciasTesouraria(stdClass $contaBancaria)
    {

        $filtroPeriodo = " and ridata between '{$this->dtDataInicial}' and '{$this->dtDataFinal}'";

        if ($this->mesCompetencia == 0) {
            $filtroPeriodo = null;
        }

        $chamadaExtratoCaixa = [];
        foreach ($this->getListaInstituicoes() as $instituicao) {
            $chamadaExtratoCaixa[] = " select *
                                         from fc_extratocaixa(
                                            {$instituicao},
                                            {$contaBancaria->db83_sequencial},
                                            null,
                                            null,
                                            false
                                        ) ";
        }
        $chamadaExtratoCaixa = implode(' union ', $chamadaExtratoCaixa);

        $pendencias = DB::select(<<<SQL
        with dados as
        (select max(case
                        when extrato.richeque is not null
                             and extrato.richeque <> 0
                             and extrato.rivalorcredito <> 0 then 'cheque'
                        when extrato.rnvalordebito is not null
                             and extrato.rnvalordebito <> 0
                             or extrato.richeque is not null
                             and extrato.richeque <> 0
                             and extrato.rnvalordebito <> 0 then 'debito'
                        when extrato.rivalorcredito is not null
                             and extrato.rivalorcredito <> 0 then 'credito'
                        end) as tipo,
                sum(case
                        when extrato.rnvalordebito is not null
                             and extrato.rnvalordebito <> 0 then extrato.rnvalordebito
                        else extrato.rivalorcredito
                    end) as valor,
                ridata,
                ricaixa,
                riautent
           from conciliapendcorrente
                inner join ( {$chamadaExtratoCaixa} ) as extrato
                        on extrato.ricaixa = k89_id
                       and extrato.riautent = k89_autent
                       and extrato.ridata = k89_data
          where k89_concilia = {$contaBancaria->ultima_conciliacao}
            and not exists (select 1
                              from corgrupocorrente
                             where k105_autent = k89_autent
                               and k105_id = k89_id
                               and k105_data = k89_data
                               and (
                                     (k105_corgrupotipo IN (2, 3, 5, 6)
                                      and extract(year from k105_data) <= 2012)
                                     or (k105_corgrupotipo IN (2, 3))
                                   )
                           )
            {$filtroPeriodo}
          group by ricaixa,
                   ridata,
                   riautent,
                   riempenho,
                   riordem,
                   riplanilha,
                   rislip,
                   richeque,
                   k89_concilia)
        select ridata as data,
               tipo,
               sum(valor) as valor,
               (select k68_sequencial
                  from concilia
                 where k68_contabancaria = {$contaBancaria->db83_sequencial}
                   and k68_data = ridata) as k68_sequencial
        from dados
       where valor > 0
       group by ridata, tipo
       order by ridata, tipo
SQL
        );

        return $pendencias;
    }

    private function getPendenciasTesourariaExercicioAnterior(stdClass $contaBancaria)
    {
        $anoAnterior = $this->iAnoUsu - 1;

        $filtroPeriodo = " and EXTRACT(YEAR FROM ridata)::integer = {$anoAnterior}";

        if ($this->mesCompetencia == 0) {
            $filtroPeriodo = null;
        }

        $chamadaExtratoCaixa = [];
        foreach ($this->getListaInstituicoes() as $instituicao) {
            $chamadaExtratoCaixa[] = " select *
                                         from fc_extratocaixa(
                                            {$instituicao},
                                            {$contaBancaria->db83_sequencial},
                                            null,
                                            null,
                                            false
                                        ) ";
        }
        $chamadaExtratoCaixa = implode(' union ', $chamadaExtratoCaixa);

        $pendencias = DB::select(<<<SQL
        with dados as
        (select max(case
                        when extrato.richeque is not null
                             and extrato.richeque <> 0
                             and extrato.rivalorcredito <> 0 then 'cheque'
                        when extrato.rnvalordebito is not null
                             and extrato.rnvalordebito <> 0
                             or extrato.richeque is not null
                             and extrato.richeque <> 0
                             and extrato.rnvalordebito <> 0 then 'debito'
                        when extrato.rivalorcredito is not null
                             and extrato.rivalorcredito <> 0 then 'credito'
                        end) as tipo,
                sum(case
                        when extrato.rnvalordebito is not null
                             and extrato.rnvalordebito <> 0 then extrato.rnvalordebito
                        else extrato.rivalorcredito
                    end) as valor,
                ridata,
                ricaixa,
                riautent
           from conciliapendcorrente
                inner join ( {$chamadaExtratoCaixa} ) as extrato
                        on extrato.ricaixa = k89_id
                       and extrato.riautent = k89_autent
                       and extrato.ridata = k89_data
          where k89_concilia = {$contaBancaria->ultima_conciliacao}
            and not exists (select 1
                              from corgrupocorrente
                             where k105_autent = k89_autent
                               and k105_id = k89_id
                               and k105_data = k89_data
                               and (
                                     (k105_corgrupotipo IN (2, 3, 5, 6)
                                      and extract(year from k105_data) <= 2012)
                                     or (k105_corgrupotipo IN (2, 3))
                                   )
                           )
            {$filtroPeriodo}
          group by ricaixa,
                   ridata,
                   riautent,
                   riempenho,
                   riordem,
                   riplanilha,
                   rislip,
                   richeque,
                   k89_concilia)
        select ridata as data,
               tipo,
               sum(valor) as valor,
               (select k68_sequencial
                  from concilia
                 where k68_contabancaria = {$contaBancaria->db83_sequencial}
                   and k68_data = ridata) as k68_sequencial
        from dados
       where valor > 0
       group by ridata, tipo
       order by ridata, tipo
SQL
        );

        return $pendencias;
    }

    private function getPendenciasExtrato(stdClass $contaBancaria)
    {

        $filtroPeriodo = " and k86_data between '{$this->dtDataInicial}' and '{$this->dtDataFinal}' ";

        if ($this->mesCompetencia == 0) {
            $filtroPeriodo = null;
        }

        return DB::select(<<<SQL
with pendencias_conta as 
     (select distinct 
             (select max(k68_sequencial) 
                from caixa.concilia 
               where concilia.k68_contabancaria = extratolinha.k86_contabancaria 
                 and concilia.k68_data = extratolinha.k86_data) as k68_sequencial,
              extratolinha.k86_sequencial as extratolinha,
              extratolinha.k86_data as data,
              extratolinha.k86_valor as valor,
              extratolinha.k86_tipo as tipo
         from caixa.conciliapendextrato
              join caixa.extratolinha on extratolinha.k86_sequencial = conciliapendextrato.k88_extratolinha
              join caixa.extrato      on extrato.k85_sequencial = extratolinha.k86_extrato
        where k86_contabancaria = {$contaBancaria->db83_sequencial}
          and k86_valor > 0
          and not exists (select true 
                            from caixa.conciliaextrato
                                 join caixa.conciliaitem 
                                   on conciliaitem.k83_sequencial = conciliaextrato.k87_conciliaitem
                                 join caixa.concilia on concilia.k68_sequencial = conciliaitem.k83_concilia
                           where concilia.k68_data <= '{$this->dtDataFinal}'
                             and conciliaextrato.k87_extratolinha = extratolinha.k86_sequencial)          
          {$filtroPeriodo}
     )
select case 
            when k68_sequencial is null then {$contaBancaria->ultima_conciliacao}
            else k68_sequencial 
       end as k68_sequencial,
       data,
       sum(valor) as valor,
       tipo 
  from pendencias_conta
 group by case 
            when k68_sequencial is null then {$contaBancaria->ultima_conciliacao}
            else k68_sequencial 
          end,
          data,
          tipo            
 order by k68_sequencial, data, tipo;

SQL
        );
    }

    private function getPendenciasExtratoExercicioAnterior(stdClass $contaBancaria)
    {
        $anoAnterior = $this->iAnoUsu - 1;

        $filtroPeriodo = " and EXTRACT(YEAR FROM k86_data)::integer = {$anoAnterior}";

        if ($this->mesCompetencia == 0) {
            $filtroPeriodo = null;
        }

        return DB::select(<<<SQL
with pendencias_conta as 
     (select distinct 
             (select max(k68_sequencial) 
                from caixa.concilia 
               where concilia.k68_contabancaria = extratolinha.k86_contabancaria 
                 and concilia.k68_data = extratolinha.k86_data) as k68_sequencial,
              extratolinha.k86_sequencial as extratolinha,
              extratolinha.k86_data as data,
              extratolinha.k86_valor as valor,
              extratolinha.k86_tipo as tipo
         from caixa.conciliapendextrato
              join caixa.extratolinha on extratolinha.k86_sequencial = conciliapendextrato.k88_extratolinha
              join caixa.extrato      on extrato.k85_sequencial = extratolinha.k86_extrato
        where k86_contabancaria = {$contaBancaria->db83_sequencial}
          and k86_valor > 0
          and not exists (select true 
                            from caixa.conciliaextrato
                                 join caixa.conciliaitem 
                                   on conciliaitem.k83_sequencial = conciliaextrato.k87_conciliaitem
                                 join caixa.concilia on concilia.k68_sequencial = conciliaitem.k83_concilia
                           where concilia.k68_data <= '{$this->dtDataFinal}'
                             and conciliaextrato.k87_extratolinha = extratolinha.k86_sequencial)
          {$filtroPeriodo}
     )
select case 
            when k68_sequencial is null then {$contaBancaria->ultima_conciliacao}
            else k68_sequencial 
       end as k68_sequencial,
       data,
       sum(valor) as valor,
       tipo 
  from pendencias_conta
 group by case 
            when k68_sequencial is null then {$contaBancaria->ultima_conciliacao}
            else k68_sequencial 
          end,
          data,
          tipo            
 order by k68_sequencial, data, tipo;

SQL
        );
    }

    private function getSaldoExtrato(stdClass $contaBancaria)
    {
        return DB::TABLE('extratosaldo')
        ->selectRaw("k97_saldofinal as valor")
        ->where('k97_contabancaria', $contaBancaria->db83_sequencial)
        ->whereRaw("
           k97_dtsaldofinal = (
                SELECT k97_dtsaldofinal
                  FROM extratosaldo a
                 WHERE a.k97_contabancaria = {$contaBancaria->db83_sequencial}
                   and '{$contaBancaria->ultima_data_conciliacao}' >= a.k97_dtsaldofinal
                ORDER BY a.k97_dtsaldofinal desc,a.k97_extrato desc LIMIT 1
            )
            ")
        ->orderBy("k97_extrato", 'DESC')
        ->first()->valor;
    }

    private function getSaldoTesouraria(stdClass $contaBancaria)
    {
        $saldoTotal = 0;

        $reduzidos = $this->getReduzidosContaBancaria($contaBancaria);
        foreach ($this->getListaInstituicoes() as $instituicao) {
            foreach ($reduzidos as $reduzido) {
                $saldoTesouraria = DB::select(<<<SQL
                select substr(
                    fc_saltessaldo({$reduzido},
                                   '{$contaBancaria->ultima_data_conciliacao}',
                                   '{$contaBancaria->ultima_data_conciliacao}',
                                   null,
                                   {$instituicao}), 41, 13)::float as saldocontacaixa
SQL
                );
                $saldoTotal += $saldoTesouraria[0]->saldocontacaixa;
            }
        }
        return $saldoTotal;
    }

    private function getReduzidosContaBancaria(stdClass $contaBancaria)
    {
        return DB::TABlE("conplanocontabancaria")
        ->select('c56_reduz')
        ->join("conplanoreduz", function ($join) {
            $join->on('c61_reduz', 'c56_reduz')
            ->on('c61_anousu', 'c56_anousu')
            ->whereIn('c61_instit', $this->getListaInstituicoes());
        })
        ->where('c56_anousu', $this->iAnoUsu)
        ->where("c56_contabancaria", $contaBancaria->db83_sequencial)
            ->get()
            ->map(function ($dados) {
                return $dados->c56_reduz;
            })->toArray();
    }

    private function processaDadosConciliacao()
    {

        $this->conciliacoesProcessadas = [];

        foreach ($this->objetoConciliacao->ConciliacoesBancarias as $conciliacao) {

            /**
             * Filtra todas as conciliacoes que tem o  mesmo tipo, ou seja, identificadores
             * iguais e somamos os valores
            */
            $identificadorConciliacao = $conciliacao->ConciliacaoBancaria->Identificador;

            if (!$this->temConciliacaoProcessada($identificadorConciliacao)) {
                $conciliacoesFiltradas = array_filter(
                    $this->objetoConciliacao->ConciliacoesBancarias,
                    function ($filtro) use ($identificadorConciliacao) {
                        return $filtro->ConciliacaoBancaria->Identificador  == $identificadorConciliacao;
                    }
                );

                $valor = 0;
                foreach ($conciliacoesFiltradas as $conciliacaoFiltrada) {
                    $valor += $conciliacaoFiltrada->ConciliacaoBancaria->Valor;
                }

                $conciliacao->ConciliacaoBancaria->Valor = $valor;
                $this->conciliacoesProcessadas[] = $conciliacao;
            }
        }
    }

    private function temConciliacaoProcessada($identificadorConciliacao)
    {

        if (count(array_filter($this->conciliacoesProcessadas, function ($filtro) use ($identificadorConciliacao) {
                return $filtro->ConciliacaoBancaria->Identificador  == $identificadorConciliacao;
        })) > 0) {
                return true;
        } else {
            return false;
        }
    }
}
