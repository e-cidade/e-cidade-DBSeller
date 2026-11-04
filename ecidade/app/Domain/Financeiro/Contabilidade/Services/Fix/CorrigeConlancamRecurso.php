<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Fix;

use funcao;
use Illuminate\Support\Facades\DB;

class CorrigeConlancamRecurso
{
    /**
     * @var mixed
     */
    private $exercicio;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var string
     */
    private $tableProblemas;
    /**
     * @var string
     */
    private $tableInserir;

    public function arrumar($exercicio, $data)
    {
        $this->data = implode('-', array_reverse(explode('/', $data)));
        $this->exercicio = $exercicio;
        $this->tableProblemas = 'w_ajuste_conlancamrecurso_' . time();
        $this->tableInserir = 'w_ajuste_conlancamrecurso_recriados_' . time();
        $this->dropWorkTables();
        $this->buscaLancamentos();
        $this->limparRegistros();
        $this->recriarRegistros();
        $this->ajustarContasBancarias();
    }

    private function dropWorkTables()
    {
        DB::connection()->getPdo()->exec(
            "drop table if exists {$this->tableProblemas};
             drop table if exists {$this->tableInserir};

             drop table if exists w_ajuste_conlacamrecurso_empenho_contabancaria;
             drop table if exists w_ajuste_conlacamrecurso_empenho_pag_contabancaria;
             drop table if exists w_ajuste_conlacamrecurso_empenho_est_contabancaria;

             drop table if exists w_ajuste_conlancamrecurso_slip;
             drop table if exists w_ajuste_conlacamrecurso_receita;
             drop table if exists w_ajuste_conlacamrecurso_receita_estorno;"
        );
    }

    private function buscaLancamentos()
    {

        DB::statement(<<<SQL
create table $this->tableProblemas as
with debitos as (
	select c70_codlan, c70_data, c02_instit, c53_tipo, c53_coddoc
	from conlancam
	join conlancamdoc on c71_codlan = c70_codlan
	join conhistdoc on c71_coddoc = c53_coddoc
	left join conlancaminstit on c02_codlan = c70_codlan
	join conlancamval on c69_codlan = c70_codlan
	left join conlancamrecurso on c130_conlancam = c70_codlan
         and c130_conta = c69_debito
         and c130_anousu = c70_anousu
         and c130_natureza = 'D'
	where c70_data >= '$this->data'
	  and c53_coddoc not in (142, 143)
      and c130_sequencial is null
), creditos as (
  select c70_codlan, c70_data, c02_instit, c53_tipo, c53_coddoc
	from conlancam
	join conlancamdoc on c71_codlan = c70_codlan
	join conhistdoc on c71_coddoc = c53_coddoc
	join conlancamval on c69_codlan = c70_codlan
	left join conlancaminstit on c02_codlan = c70_codlan
	left join conlancamrecurso on c130_conlancam = c70_codlan
         and c130_conta = c69_credito
         and c130_anousu = c70_anousu
         and c130_natureza = 'C'
	where c70_data >= '$this->data'
	  and c53_coddoc not in (142, 143)
      and c130_sequencial is null
), divergencia_conlancamrecurso as (
	select c70_codlan, c70_data, c02_instit, c53_tipo, c53_coddoc
	  from (
	    select c70_codlan, c70_data, c02_instit, c53_tipo, c53_coddoc,
	           (select count(c69_sequen)
	              from conlancamval
	             where c69_codlan = c70_codlan
	           ) as count_conlancamval,
	           (select count(c130_sequencial)
	              from conlancamrecurso
	             where c130_conlancam = c70_codlan
	           ) as count_conlancamrecurso
	      from conlancam
	      join conlancamdoc on c71_codlan = c70_codlan
	      join conhistdoc on c71_coddoc = c53_coddoc
	      left join conlancaminstit on c02_codlan = c70_codlan
	    where c70_data >= '$this->data'
	) as xx
    where (count_conlancamval * 2) != count_conlancamrecurso
), unifica_dados as (
	select distinct *
	  from (
          select * from debitos
          union all
          select * from creditos
          union all
          select * from divergencia_conlancamrecurso
	) as x
)
select *,
       case
         when c53_tipo in (10, 11, 20, 21, 30, 31, 40, 41, 50, 51, 60, 61, 70, 71, 90, 91, 92, 100, 101, 110,
                           111, 112, 113, 200, 201, 414, 415, 900, 901, 1000, 1500, 2000, 2001)
         then (select o201_orctiporec from conlancamcomplementorecurso where o201_codlan = c70_codlan)
         else null
       end recurso
  from unifica_dados;
SQL
        );
    }

    private function limparRegistros()
    {
        DB::statement(<<<SQL
delete from conlancamrecurso
 using $this->tableProblemas
 where c130_conlancam = c70_codlan
SQL
        );
    }

    private function recriarRegistros()
    {
        DB::statement(<<<SQL
create table $this->tableInserir as
with problemas as (
   select * from $this->tableProblemas
), com_recurso as (
    select c70_codlan,
           recurso,
           c69_credito as conta,
           c69_anousu,
           'C' as natureza
      from problemas
      join conlancamval on c70_codlan = c69_codlan
    where recurso is not null

    union

    select c70_codlan,
           recurso,
           c69_debito as conta,
           c69_anousu,
           'D' as natureza
      from problemas
      join conlancamval on c70_codlan = c69_codlan
    where recurso is not null
), sem_recurso as (
    select c70_codlan,
           c61_codigo,
           c69_credito as conta,
           c69_anousu,
           'C' as natureza
      from problemas
      join conlancamval on c70_codlan = c69_codlan
      join conplanoreduz on c61_reduz = c69_credito
           and c61_anousu = c69_anousu
    where recurso is null
    union
    select c70_codlan,
           c61_codigo,
           c69_debito as conta,
           c69_anousu,
           'D' as natureza
      from problemas
      join conlancamval on c70_codlan = c69_codlan
      join conplanoreduz on c61_reduz = c69_debito
           and c61_anousu = c69_anousu
    where recurso is null
)
select * from com_recurso
union
select * from sem_recurso;
SQL
        );

        DB::statement(<<<SQL
insert into conlancamrecurso
select nextval('conlancamrecurso_c130_sequencial_seq'), c70_codlan, recurso, conta, c69_anousu, natureza
from $this->tableInserir
SQL
        );
    }

    private function ajustarContasBancarias()
    {
        $this->ajustarContasBancariasEmpenhos();
        $this->ajustarContasBancariasReceitas();
        $this->ajustarContasBancariasSlips();
    }

    private function ajustarContasBancariasEmpenhos()
    {
        DB::connection()->getPdo()->exec(<<<SQL
create table w_ajuste_conlacamrecurso_empenho_contabancaria as
select c70_codlan, o201_orctiporec, c71_coddoc
  from conlancam
  join conlancamdoc on c71_codlan = c70_codlan
  join conlancamcomplementorecurso on o201_codlan = c70_codlan
 where c70_data >= '$this->data'
   and c71_coddoc in (5, 35, 37, 6000, 6002, 6004, 6008, 6010, 6, 36, 38, 6001, 6003, 6005, 6009, 6011);

create table w_ajuste_conlacamrecurso_empenho_pag_contabancaria as
 select c70_codlan, c69_credito, c61_codigo, c71_coddoc
   from w_ajuste_conlacamrecurso_empenho_contabancaria
   join conlancamval on c69_codlan = c70_codlan
        and c69_ordem = 1
   join conplanoreduz on c61_reduz = c69_credito
        and c61_anousu = c69_anousu
  where c71_coddoc in (5, 35, 37, 6001, 6002, 6004, 6008, 6010);

create table w_ajuste_conlacamrecurso_empenho_est_contabancaria as
 select c70_codlan, c69_debito, c61_codigo, c71_coddoc
   from w_ajuste_conlacamrecurso_empenho_contabancaria
   join conlancamval on c69_codlan = c70_codlan
        and c69_ordem = 1
   join conplanoreduz on c61_reduz = c69_debito
        and c61_anousu = c69_anousu
  where c71_coddoc in (6, 36, 38, 6000, 6003, 6005, 6009, 6011);
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
update contabilidade.conlancamrecurso
   set c130_orctiporec = c61_codigo
  from w_ajuste_conlacamrecurso_empenho_pag_contabancaria
 where c130_conlancam = c70_codlan
   and c130_conta = c69_credito
   and c130_natureza = 'C';

update contabilidade.conlancamrecurso
   set c130_orctiporec = c61_codigo
  from w_ajuste_conlacamrecurso_empenho_est_contabancaria
 where c130_conlancam = c70_codlan
   and c130_conta = c69_debito
   and c130_natureza = 'D';
SQL
        );
    }

    private function ajustarContasBancariasReceitas()
    {
        DB::connection()->getPdo()->exec(<<<SQL
create table w_ajuste_conlacamrecurso_receita as
select c70_codlan,
       c70_anousu,
       o201_orctiporec as recurso_receita,
       c69_credito as reduzido_receita,
       c69_debito as conta_pag,
       rd.c61_codigo as recurso_conta_pag,
       c71_coddoc
  from conlancam
  join conlancamdoc on c71_codlan = c70_codlan
  join conlancamcomplementorecurso on o201_codlan = c70_codlan
  join conlancamval on c69_codlan = c70_codlan
       and c69_ordem = 1
  join conplanoreduz rd on rd.c61_reduz = c69_debito
       and rd.c61_anousu = c69_anousu
 where c70_data >= '$this->data'
   and c71_coddoc in (100, 107, 109, 111, 113, 115, 117, 165, 416, 419, 6000, 6006);

delete from conlancamrecurso
using w_ajuste_conlacamrecurso_receita
 where c130_conlancam = c70_codlan;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_receita, reduzido_receita, c70_anousu, 'C'
  from w_ajuste_conlacamrecurso_receita;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, conta_pag, c70_anousu, 'D'
  from w_ajuste_conlacamrecurso_receita;

-- segundo lancamento
insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_receita, c69_credito, c70_anousu, 'C'
  from w_ajuste_conlacamrecurso_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 2;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_receita, c69_debito, c70_anousu, 'D'
  from w_ajuste_conlacamrecurso_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 2;

-- terceiro lancamento
insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, c69_credito, c70_anousu, 'C'
  from w_ajuste_conlacamrecurso_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 3;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, c69_debito, c70_anousu, 'D'
  from w_ajuste_conlacamrecurso_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 3;

-- quarto lancamento
insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, c69_credito, c70_anousu, 'C'
  from w_ajuste_conlacamrecurso_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 4;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, c69_debito, c70_anousu, 'D'
  from w_ajuste_conlacamrecurso_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 4;
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
create table w_ajuste_conlacamrecurso_receita_estorno as
select c70_codlan,
       c70_anousu,
       o201_orctiporec as recurso_receita,
       c69_debito as reduzido_receita,
       c69_credito as conta_pag,
       rc.c61_codigo as recurso_conta_pag,
       c71_coddoc
  from conlancam
  join conlancamdoc on c71_codlan = c70_codlan
  join conlancamcomplementorecurso on o201_codlan = c70_codlan
  join conlancamval on c69_codlan = c70_codlan
       and c69_ordem = 1
  join conplanoreduz rc on rc.c61_reduz = c69_credito
       and rc.c61_anousu = c69_anousu
 where c70_data >= '$this->data'
   and c71_coddoc in (101, 108, 110, 112, 114, 116, 118, 166, 417, 6001, 6013, 418);

delete from conlancamrecurso
using w_ajuste_conlacamrecurso_receita_estorno
 where c130_conlancam = c70_codlan;

-- primeiro lancamento
insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_receita, reduzido_receita, c70_anousu, 'D'
  from w_ajuste_conlacamrecurso_receita_estorno;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, conta_pag, c70_anousu, 'C'
  from w_ajuste_conlacamrecurso_receita_estorno;

-- segundo lancamento
insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_receita, c69_credito, c70_anousu, 'C'
  from w_ajuste_conlacamrecurso_receita_estorno
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 2;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_receita, c69_debito, c70_anousu, 'D'
  from w_ajuste_conlacamrecurso_receita_estorno
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 2;

-- terceiro lancamento
insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, c69_credito, c70_anousu, 'C'
  from w_ajuste_conlacamrecurso_receita_estorno
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 3;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, c69_debito, c70_anousu, 'D'
  from w_ajuste_conlacamrecurso_receita_estorno
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 3;

SQL
        );
    }

    private function ajustarContasBancariasSlips()
    {
        DB::connection()->getPdo()->exec(<<<SQL
create table w_ajuste_conlancamrecurso_slip as
select c70_codlan,
       c69_debito,
       c69_credito,
       cd.c61_codigo as rec_deb,
       cc.c61_codigo as rec_cred,
       c71_coddoc,
       c69_ordem
  from conlancam
  join conlancamdoc on c71_codlan = c70_codlan
  join conlancamval on c69_codlan = c70_codlan
        and c69_ordem = 1
  join conplanoreduz cd on cd.c61_reduz = c69_debito
       and cd.c61_anousu = c69_anousu
  join conplanoreduz cc on cc.c61_reduz = c69_credito
       and cc.c61_anousu = c69_anousu
 where c70_data >= '$this->data'
   and c71_coddoc in (120, 121, 130, 131, 140, 160, 161, 141, 162, 163, 150, 151, 152, 153, 6012, 6007);
SQL
        );


        /**
         * Nos documentos abaixo, o segundo lançamento deve ser salvo conforme regra na planilha.
         * Um exemplo simplificado da regra é salvar o recurso da conta no primeiro lançamento conforme abaixo
         * Recurso da conta a Crédito para os docs. 160, 163, 120, 131
         * Recurso da conta a Débito para os docs. 161, 162, 121, 130
         *
         * Abaixo altera todos registros conforme acima e após altera apenas o registro do primeiro lançamento.
         */
        DB::connection()->getPdo()->exec(<<<SQL
update conlancamrecurso
   set c130_orctiporec = rec_deb
  from w_ajuste_conlancamrecurso_slip
 where c130_conlancam = c70_codlan
   and c71_coddoc in (161, 162, 121, 130);

update conlancamrecurso
   set c130_orctiporec = rec_cred
  from w_ajuste_conlancamrecurso_slip
 where c130_conlancam = c70_codlan
   and c71_coddoc in (160, 163, 120, 131);
SQL
        );

        /**
         * Arruma o primeiro lançamento de todos os documentos
         */
        DB::connection()->getPdo()->exec(<<<SQL
update contabilidade.conlancamrecurso
   set c130_orctiporec = rec_deb
  from w_ajuste_conlancamrecurso_slip
 where c130_conlancam = c70_codlan
   and c130_conta = c69_debito
   and c130_natureza = 'D';

update contabilidade.conlancamrecurso
   set c130_orctiporec = rec_cred
  from w_ajuste_conlancamrecurso_slip
 where c130_conlancam = c70_codlan
   and c130_conta = c69_credito
   and c130_natureza = 'C';
SQL
        );
    }
}
