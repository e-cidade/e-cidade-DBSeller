<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Fix;

use Illuminate\Support\Facades\DB;

/**
 * Corrige os lançamentos dos documentos: 120, 121, 130, 131, 140, 141, 150, 151, 152, 153, 160, 161, 162, 163
 */
class CorrigeLancamentoSlips
{
    /**
     * @var mixed
     */
    private $exercicio;
    /**
     * @var string
     */
    private $tableProblemas;
    /**
     * @var string
     */
    private $tableInserir;
    public function arrumar($exercicio)
    {
        $this->exercicio = $exercicio;
        $this->tableProblemas = 'w_lancamentos_arrumar_' . time();
        $this->tableInserir = 'w_lancamentos_inserir_' . time();
        $this->bucaLancamento();

        $this->deleta();
        $this->recriar();
    }

    private function bucaLancamento()
    {
        DB::connection()->getPdo()->exec("drop table if exists {$this->tableProblemas}");
        DB::connection()->getPdo()->exec(<<<SQL
create table $this->tableProblemas as
select c70_codlan
  from conlancam
  join conlancamdoc on c71_codlan = c70_codlan
 where c70_anousu = 2023
   and c71_coddoc in (120, 121, 130, 131, 140, 141, 150, 151, 152, 153, 160, 161, 162, 163)
SQL
        );
    }
    private function deleta()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from conlancamrecurso
using $this->tableProblemas
 where c130_conlancam = c70_codlan
SQL
        );
    }

    private function recriar()
    {
        DB::connection()->getPdo()->exec("drop table if exists {$this->tableInserir}");

        DB::connection()->getPdo()->exec(<<<SQL
create table $this->tableInserir as
with problemas as (
   select * from $this->tableProblemas
), sem_recurso as (
    select c70_codlan,
           c61_codigo as recurso,
           c69_credito as conta,
           c69_anousu,
           'C' as natureza
      from problemas
      join conlancamval on c70_codlan = c69_codlan
      join conplanoreduz on c61_reduz = c69_credito
           and c61_anousu = c69_anousu

    union all

    select c70_codlan,
           c61_codigo as recurso,
           c69_debito as conta,
           c69_anousu,
           'D' as natureza
      from problemas
      join conlancamval on c70_codlan = c69_codlan
      join conplanoreduz on c61_reduz = c69_debito
           and c61_anousu = c69_anousu
) select * from sem_recurso;
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
insert into conlancamrecurso
select nextval('conlancamrecurso_c130_sequencial_seq'), c70_codlan, recurso, conta, c69_anousu, natureza
from $this->tableInserir
SQL
        );
    }
}
