<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25503AcertoLancamentoReceitas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop table if exists w_ajuste_lancamentos_recursos;

create table w_ajuste_lancamentos_recursos as
select * from (
    select distinct
           c70_codlan,
           c70_anousu,
           c70_data,
           c71_coddoc,
           c74_codrec,
           c74_codlan is not null as has_conlancamrec,
           c130_sequencial is not null as has_conlancamrecurso,
           o201_sequencial is not null as has_conlancamcomplementorecurso,
           o70_codigo as recurso_certo,
           f.gestao as gestao_receita,
           rec_receita.o15_recurso as subrec_receita,
           rec_receita.o15_complemento as compl_receita,
           -- conlancamrecurso
           f_cr.gestao as gestao_cr,
           rec_cr.o15_recurso as subrec_cr,
           rec_cr.o15_complemento as compl_cr,

           -- conlancamcomplementorecurso
           f_ccr.gestao as gestao_ccr,
           rec_ccr.o15_recurso as subrec_ccr,
           rec_ccr.o15_complemento as compl_ccr,
           o201_complemento as compl_lanc
      from conlancam
      join conlancamdoc on c71_codlan = c70_codlan
      join conhistdoc on c53_coddoc = c71_coddoc
      left join conlancamrec on c74_codlan = c70_codlan
      left join orcreceita on (o70_anousu, o70_codrec) = (c74_anousu, c74_codrec)

      -- recurso da receita
      left join orctiporec rec_receita on rec_receita.o15_codigo = o70_codigo
      left join fonterecurso f on f.orctiporec_id = rec_receita.o15_codigo
                and f.exercicio = c70_anousu

      -- recurso da conlancamrecurso
      left join conlancamrecurso on c130_conlancam = c70_codlan
      left join orctiporec rec_cr on rec_cr.o15_codigo = c130_orctiporec
      left join fonterecurso f_cr on f_cr.orctiporec_id = rec_cr.o15_codigo
                and f_cr.exercicio = c70_anousu

      -- recurso da conlancamcomplementorecurso
      left join conlancamcomplementorecurso on o201_codlan = c70_codlan
      left join orctiporec rec_ccr on rec_ccr.o15_codigo = o201_orctiporec
      left join fonterecurso f_ccr on f_ccr.orctiporec_id = rec_ccr.o15_codigo
                and f_ccr.exercicio = c70_anousu
     where c70_anousu = 2023
       and c53_tipo in (100, 101)
) as x
 where (    (gestao_receita != gestao_cr or gestao_receita != gestao_ccr)
         or (    (gestao_receita = gestao_cr and subrec_receita != subrec_cr)
              or (gestao_receita = gestao_ccr and subrec_receita != subrec_ccr)
            )
         or (has_conlancamrecurso is false or has_conlancamcomplementorecurso is false)
         or (gestao_receita = gestao_ccr and subrec_receita = subrec_ccr and compl_lanc != compl_ccr)
       )
 order by c71_coddoc, c70_codlan;

update conlancamrecurso set c130_orctiporec = recurso_certo
from w_ajuste_lancamentos_recursos
where c130_conlancam = c70_codlan
  and c130_orctiporec != recurso_certo;

update conlancamcomplementorecurso set  o201_orctiporec = recurso_certo, o201_complemento = compl_receita
from w_ajuste_lancamentos_recursos
where o201_codlan = c70_codlan
  and (o201_orctiporec != recurso_certo or o201_complemento != compl_receita);

insert into contabilidade.conlancamcomplementorecurso
select nextval('conlancamcomplementorecurso_o201_sequencial_seq'),
       c70_codlan,
       compl_receita,
       recurso_certo
  from w_ajuste_lancamentos_recursos
 where has_conlancamcomplementorecurso is false;

insert into conlancamrecurso
select nextval('conlancamrecurso_c130_sequencial_seq'),
       c70_codlan,
       recurso_certo,
       c69_credito,
       c70_anousu,
       'C'
  from w_ajuste_lancamentos_recursos
  join conlancamval on c69_codlan = c70_codlan
  where has_conlancamrecurso is false
union
select nextval('conlancamrecurso_c130_sequencial_seq'),
       c70_codlan,
       recurso_certo,
       c69_debito,
       c70_anousu,
       'D'
  from w_ajuste_lancamentos_recursos
  join conlancamval on c69_codlan = c70_codlan
  where has_conlancamrecurso is false;
SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
