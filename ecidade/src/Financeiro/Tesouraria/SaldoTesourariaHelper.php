<?php

namespace ECidade\Financeiro\Tesouraria;

class SaldoTesourariaHelper
{

    private $instituicao;
    private $exercicio;
    private $dataSessao;
    private $dataFiltrarSaldo;

    public function __construct($instituicao, $exercicio, $dataSessao, $dataFiltrarSaldo)
    {
        $this->instituicao = $instituicao;
        $this->exercicio = $exercicio;
        $this->dataSessao = $dataSessao;
        $this->dataFiltrarSaldo = $dataFiltrarSaldo;
    }

    /**
     * @param string $ordem
     * @return array
     */
    public function totalizaPorConta($ordem = 'k13_descr')
    {
        $sql = $this->getSqlValoresConta($ordem);
        $rs = db_query($sql);
        $porConta = [];
        while ($dado = pg_fetch_object($rs)) {
            $porConta [] = (object)[
                "c60_estrut" => $dado->c60_estrut,
                "k13_conta" => $dado->k13_conta,
                "k13_descr" => $dado->k13_descr,
                "tipo" => $dado->tipo,
                "descricao" => $dado->descricao,
                "gestao" => $dado->gestao,
                "o15_recurso" => $dado->o15_recurso,
                "o15_complemento" => $dado->o15_complemento,
                "saldo_anterior" => $dado->anterior,
                "debitado" => $dado->debitado,
                "creditado" => $dado->creditado,
                "saldo_atual" => $dado->atual
            ];
        }

        return $porConta;
    }

    /**
     * @return array
     */
    public function totalizaContasPorRecurso()
    {
        $contas = $this->totalizaPorConta("o15_recurso, gestao, o15_complemento, k13_descr");
        $porRecursos = [];
        foreach ($contas as $conta) {
            $hash = sprintf('%s#%s#%s', $conta->gestao, $conta->o15_recurso, $conta->o15_complemento);

            if (!array_key_exists($hash, $porRecursos)) {
                $porRecursos[$hash] = (object)[
                    'gestao' => $conta->gestao,
                    'o15_recurso' => $conta->o15_recurso,
                    'o15_complemento' => $conta->o15_complemento,
                    'descricao' => $conta->descricao,
                    'contas' => [],
                    'saldo_anterior' => 0,
                    'debitado' => 0,
                    'creditado' => 0,
                    'saldo_atual' => 0,
                ];
            }
            $porRecursos[$hash]->contas[] = $conta;
            $porRecursos[$hash]->saldo_anterior += $conta->saldo_anterior;
            $porRecursos[$hash]->debitado += $conta->debitado;
            $porRecursos[$hash]->creditado += $conta->creditado;
            $porRecursos[$hash]->saldo_atual += $conta->saldo_atual;
        }
        return $porRecursos;
    }

    /**
     * @return array
     */
    public function totalizaPorBanco()
    {
        $sql = <<<SQL
 select x.*,
           substr(saldos,1,1)::int as tipo,
           coalesce(substr(saldos,2,13)::float8, 0) as anterior,
           coalesce(substr(saldos,15,13)::float8, 0) as debitado ,
           coalesce(substr(saldos,28,13)::float8, 0) as creditado,
           coalesce(substr(saldos,41,13)::float8, 0) as atual
      from (
      select db90_codban,db90_descr, k13_conta, k13_descr,
 fc_saltessaldo(k13_conta,'{$this->dataFiltrarSaldo}','{$this->dataFiltrarSaldo}',null, $this->instituicao) as saldos
          from saltes
          join conplanoexe on c62_anousu = {$this->exercicio}  and c62_reduz = k13_conta
          join conplanoreduz on c61_anousu = c62_anousu
               and c61_reduz = c62_reduz
               and c61_instit = {$this->instituicao}
          join conplano on c61_codcon = c60_codcon and c61_anousu = c60_anousu
          join orctiporec on o15_codigo = c61_codigo
		  join conplanoconta on c63_anousu=c60_anousu and c63_reduz = c61_reduz
		  join db_bancos on db90_codban = conplanoconta.c63_banco
		  where c60_codsis in (5,6)
            and (k13_limite is null or k13_limite >= '{$this->dataSessao}')
        ) as x
        order by db90_codban
SQL;

        $rs = db_query($sql);
        $porBanco = [];
        while ($dado = pg_fetch_object($rs)) {
            if (!array_key_exists($dado->db90_codban, $porBanco)) {
                $porBanco[$dado->db90_codban] = (object)[
                    'db90_codban' => $dado->db90_codban,
                    'db90_descr' => $dado->db90_descr,
                    'contas' => [],
                    'saldo_anterior' => 0,
                    'debitado' => 0,
                    'creditado' => 0,
                    'saldo_atual' => 0,
                ];
            }

            $conta = (object)[
                'k13_conta' => $dado->k13_conta,
                'k13_descr' => $dado->k13_descr,
                'saldo_anterior' => $dado->anterior,
                'debitado' => $dado->debitado,
                'creditado' => $dado->creditado,
                'saldo_atual' => $dado->atual,
            ];
            $porBanco[$dado->db90_codban]->contas[] = $conta;
            $porBanco[$dado->db90_codban]->saldo_anterior += $conta->saldo_anterior;
            $porBanco[$dado->db90_codban]->debitado += $conta->debitado;
            $porBanco[$dado->db90_codban]->creditado += $conta->creditado;
            $porBanco[$dado->db90_codban]->saldo_atual += $conta->saldo_atual;
        }

        return $porBanco;
    }

    /**
     * @return array
     */
    public function totalizaPorDomiciolioBancario()
    {
        $data = $this->dataFiltrarSaldo;
        $sql = <<<SQL
select x.*,
       substr(saldos,1,1)::int as tipo,
       coalesce(substr(saldos,2,13)::float8, 0) as anterior,
       coalesce(substr(saldos,15,13)::float8, 0) as debitado ,
       coalesce(substr(saldos,28,13)::float8, 0) as creditado,
       coalesce(substr(saldos,41,13)::float8, 0) as atual
  from (
  select k13_conta,
         k13_descr,
         db89_db_bancos || ' / ' || db89_codagencia || ' / ' ||  db83_conta || ' - ' || db83_dvconta as bancoagencia,
         gestao,
         o15_recurso,
         db83_conta,
         db83_dvconta,
         db89_db_bancos,
         o15_complemento,
         c61_reduz,
         fc_saltessaldo(k13_conta,'{$data}','{$data}',null, $this->instituicao) as saldos
  from saltes
  join conplanoexe on c62_anousu = {$this->exercicio}  and c62_reduz = k13_conta
  join conplanoreduz on c61_anousu = c62_anousu
       and c61_reduz = c62_reduz
       and c61_instit = {$this->instituicao}
  join conplano on c61_codcon = c60_codcon and c61_anousu = c60_anousu
  join orctiporec on o15_codigo = c61_codigo
  join fonterecurso on orctiporec_id = o15_codigo and exercicio = c62_anousu
  join conplanocontabancaria on c56_codcon = c61_codcon
       and c56_anousu = c61_anousu
       and c56_reduz = c61_reduz
  join contabancaria on (c56_contabancaria) = (db83_sequencial)
  join bancoagencia on (db83_bancoagencia) = (db89_sequencial)
  where c60_codsis in (5,6)
    and (k13_limite is null or k13_limite >= '{$this->dataSessao}')
) as x
order by bancoagencia, o15_recurso, gestao, o15_complemento
SQL;

        $rs = db_query($sql);
        $porDomicilio = [];
        while ($dado = pg_fetch_object($rs)) {
            if (!array_key_exists($dado->bancoagencia, $porDomicilio)) {
                $porDomicilio[$dado->bancoagencia] = (object)[
                    'bancoagencia' => $dado->bancoagencia,
                    'contas' => [],
                    'saldo_anterior' => 0,
                    'debitado' => 0,
                    'creditado' => 0,
                    'saldo_atual' => 0,
                ];
            }

            $conta = (object)[
                'k13_conta' => $dado->k13_conta,
                'k13_descr' => $dado->k13_descr,
                'gestao' => $dado->gestao,
                'o15_recurso' => $dado->o15_recurso,
                'o15_complemento' => $dado->o15_complemento,
                'saldo_anterior' => $dado->anterior,
                'debitado' => $dado->debitado,
                'creditado' => $dado->creditado,
                'saldo_atual' => $dado->atual,
            ];
            $porDomicilio[$dado->bancoagencia]->contas[] = $conta;
            $porDomicilio[$dado->bancoagencia]->saldo_anterior += $conta->saldo_anterior;
            $porDomicilio[$dado->bancoagencia]->debitado += $conta->debitado;
            $porDomicilio[$dado->bancoagencia]->creditado += $conta->creditado;
            $porDomicilio[$dado->bancoagencia]->saldo_atual += $conta->saldo_atual;
        }

        return $porDomicilio;
    }

    /**
     * @param string $ordem
     * @return string
     */
    public function getSqlValoresConta($ordem = 'k13_descr')
    {
        return <<<SQL
select x.*,
       substr(saldos,1,1)::int as tipo,
       coalesce(substr(saldos,2,13)::float8, 0) as anterior,
       coalesce(substr(saldos,15,13)::float8, 0) as debitado ,
       coalesce(substr(saldos,28,13)::float8, 0) as creditado,
       coalesce(substr(saldos,41,13)::float8, 0) as atual
  from (
    select k13_conta, k13_descr, c60_estrut, gestao, o15_recurso, descricao, o15_complemento,
   fc_saltessaldo(k13_conta,'{$this->dataFiltrarSaldo}','{$this->dataFiltrarSaldo}',null, $this->instituicao) as saldos
      from saltes
      join conplanoexe on c62_anousu = {$this->exercicio}  and c62_reduz = k13_conta
      join conplanoreduz on c61_anousu = c62_anousu
            and c61_reduz = c62_reduz
            and c61_instit = {$this->instituicao}
      join conplano on c61_codcon = c60_codcon and c61_anousu = c60_anousu
      join orctiporec on o15_codigo = c61_codigo
      join fonterecurso on orctiporec_id = o15_codigo and exercicio = c62_anousu
     where c60_codsis in (5,6)
       and (k13_limite is null or k13_limite >= '{$this->dataSessao}')
   ) as x
 order by {$ordem}
SQL;
    }
}
