<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Fix;

use Illuminate\Support\Facades\DB;

class CorrigeContasBancarias
{

    private $data;

    public function arrumar($data)
    {
        $this->data = implode('-', array_reverse(explode('/', $data)));
        $this->arrumarEmpenhos();
        $this->arrumarReceitas();
        $this->arrumarSlips();
    }

    private function arrumarEmpenhos()
    {
        DB::connection()->getPdo()->exec("drop table if exists lancamentos_pag_empenho");
        DB::connection()->getPdo()->exec("drop table if exists lancamentos_emp_conta_banco");
        DB::connection()->getPdo()->exec("drop table if exists lancamentos_extorno_emp_conta_banco");
        DB::connection()->getPdo()->exec(<<<SQL
create table lancamentos_pag_empenho as
select c70_codlan, o201_orctiporec, c71_coddoc
  from conlancam
  join conlancamdoc on c71_codlan = c70_codlan
  join conlancamcomplementorecurso on o201_codlan = c70_codlan
 where c70_data >= '$this->data'
   and c71_coddoc in (5, 35, 37, 6002, 6004, 6008, 6010, 6, 36, 38, 6003, 6005, 6009, 6011);


create table lancamentos_emp_conta_banco as
 select c70_codlan, c69_credito, c61_codigo, c71_coddoc
   from lancamentos_pag_empenho
   join conlancamval on c69_codlan = c70_codlan
        and c69_ordem = 1
   join conplanoreduz on c61_reduz = c69_credito
        and c61_anousu = c69_anousu
  where c71_coddoc in (5, 35, 37, 6002, 6004, 6008, 6010);

create table lancamentos_extorno_emp_conta_banco as
 select c70_codlan, c69_debito, c61_codigo, c71_coddoc
   from lancamentos_pag_empenho
   join conlancamval on c69_codlan = c70_codlan
        and c69_ordem = 1
   join conplanoreduz on c61_reduz = c69_debito
        and c61_anousu = c69_anousu
  where c71_coddoc in (6, 36, 38, 6003, 6005, 6009, 6011);
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
update conlancamrecurso
   set c130_orctiporec = o201_orctiporec
  from lancamentos_pag_empenho
 where c130_conlancam = c70_codlan;

update contabilidade.conlancamrecurso
   set c130_orctiporec = c61_codigo
  from lancamentos_emp_conta_banco
 where c130_conlancam = c70_codlan
   and c130_conta = c69_credito
   and c130_natureza = 'C';

update contabilidade.conlancamrecurso
   set c130_orctiporec = c61_codigo
  from lancamentos_extorno_emp_conta_banco
 where c130_conlancam = c70_codlan
   and c130_conta = c69_debito
   and c130_natureza = 'D';
SQL
        );
    }

    private function arrumarReceitas()
    {
        DB::connection()->getPdo()->exec("drop table if exists lancamentos_receita");
        DB::connection()->getPdo()->exec("drop table if exists lancamentos_estorno_receita");

        DB::connection()->getPdo()->exec(<<<SQL
create table lancamentos_receita as
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
using lancamentos_receita
 where c130_conlancam = c70_codlan;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_receita, reduzido_receita, c70_anousu, 'C'
  from lancamentos_receita;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, conta_pag, c70_anousu, 'D'
  from lancamentos_receita;

-- segundo lancamento
insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_receita, c69_credito, c70_anousu, 'C'
  from lancamentos_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 2;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_receita, c69_debito, c70_anousu, 'D'
  from lancamentos_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 2;

-- terceiro lancamento
insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, c69_credito, c70_anousu, 'C'
  from lancamentos_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 3;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, c69_debito, c70_anousu, 'D'
  from lancamentos_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 3;
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
create table lancamentos_estorno_receita as
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
using lancamentos_estorno_receita
 where c130_conlancam = c70_codlan;

-- primeiro lancamento
insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_receita, reduzido_receita, c70_anousu, 'D'
  from lancamentos_estorno_receita;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, conta_pag, c70_anousu, 'C'
  from lancamentos_estorno_receita;

-- segundo lancamento
insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_receita, c69_credito, c70_anousu, 'C'
  from lancamentos_estorno_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 2;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_receita, c69_debito, c70_anousu, 'D'
  from lancamentos_estorno_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 2;

-- terceiro lancamento
insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, c69_credito, c70_anousu, 'C'
  from lancamentos_estorno_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 3;

insert into contabilidade.conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
select c70_codlan, recurso_conta_pag, c69_debito, c70_anousu, 'D'
  from lancamentos_estorno_receita
  join conlancamval on c69_codlan = c70_codlan and c69_ordem = 3;

SQL
        );
    }

    private function arrumarSlips()
    {
        DB::connection()->getPdo()->exec("drop table if exists demais_lancamentos");
        DB::connection()->getPdo()->exec(<<<SQL
create table demais_lancamentos as
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
   and c71_coddoc in (140, 160, 161, 141, 162, 163, 150, 151, 152, 153, 6012, 6007);
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
  from demais_lancamentos
 where c130_conlancam = c70_codlan
   and c71_coddoc in (161, 162, 121, 130);

update conlancamrecurso
   set c130_orctiporec = rec_cred
  from demais_lancamentos
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
  from demais_lancamentos
 where c130_conlancam = c70_codlan
   and c130_conta = c69_debito
   and c130_natureza = 'D';

update contabilidade.conlancamrecurso
   set c130_orctiporec = rec_cred
  from demais_lancamentos
 where c130_conlancam = c70_codlan
   and c130_conta = c69_credito
   and c130_natureza = 'C';
SQL
        );
    }
}
