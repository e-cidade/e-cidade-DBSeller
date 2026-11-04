<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24096AjusteLancamentoExtraFinanceiro extends Migration
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
           c130_sequencial is not null as has_conlancamrecurso,
           o201_sequencial is not null as has_conlancamcomplementorecurso,
           c61_codigo as recurso_certo,
           f.gestao as gestao_reduzido,
           rec_reduzido.o15_recurso as subrec_reduzido,
           rec_reduzido.o15_complemento as compl_reduzido,
           -- conlancamrecurso
           f_cr.gestao as gestao_cr,
           rec_cr.o15_recurso as subrec_cr,
           rec_cr.o15_complemento as compl_cr,

           -- conlancamcomplementorecurso
           f_ccr.gestao as gestao_ccr,
           rec_ccr.o15_recurso as subrec_ccr,
           rec_ccr.o15_complemento as compl_ccr
      from conlancam
      join conlancamdoc on c71_codlan = c70_codlan
      join conhistdoc on c53_coddoc = c71_coddoc
      join conlancamval ON c69_codlan = c70_codlan and c69_ordem = 1
      join conplanoreduz ON c61_reduz = c69_debito
           and c61_anousu = c69_anousu

      -- recurso do reduzido
      left join orctiporec rec_reduzido on rec_reduzido.o15_codigo = c61_codigo
      left join fonterecurso f on f.orctiporec_id = rec_reduzido.o15_codigo
                and f.exercicio = c70_anousu

      -- recurso da conlancamrecurso
      left join contabilidade.conlancamrecurso ON c130_conlancam = c69_codlan
           and c130_conta = c69_debito
           and c130_natureza = 'D'
      left join orctiporec rec_cr on rec_cr.o15_codigo = c130_orctiporec
      left join fonterecurso f_cr on f_cr.orctiporec_id = rec_cr.o15_codigo
                and f_cr.exercicio = c70_anousu

      -- recurso da conlancamcomplementorecurso
      left join conlancamcomplementorecurso on o201_codlan = c70_codlan
      left join orctiporec rec_ccr on rec_ccr.o15_codigo = o201_orctiporec
      left join fonterecurso f_ccr on f_ccr.orctiporec_id = rec_ccr.o15_codigo
                and f_ccr.exercicio = c70_anousu
     where c70_anousu = 2023
       and c53_tipo in (151, 161, 120, 153, 163, 131)

    union

    select distinct
           c70_codlan,
           c70_anousu,
           c70_data,
           c71_coddoc,
           c130_sequencial is not null as has_conlancamrecurso,
           o201_sequencial is not null as has_conlancamcomplementorecurso,
           c61_codigo as recurso_certo,
           f.gestao as gestao_reduzido,
           rec_reduzido.o15_recurso as subrec_reduzido,
           rec_reduzido.o15_complemento as compl_reduzido,
           -- conlancamrecurso
           f_cr.gestao as gestao_cr,
           rec_cr.o15_recurso as subrec_cr,
           rec_cr.o15_complemento as compl_cr,

           -- conlancamcomplementorecurso
           f_ccr.gestao as gestao_ccr,
           rec_ccr.o15_recurso as subrec_ccr,
           rec_ccr.o15_complemento as compl_ccr
      from conlancam
      join conlancamdoc on c71_codlan = c70_codlan
      join conhistdoc on c53_coddoc = c71_coddoc
      join conlancamval ON c69_codlan = c70_codlan and c69_ordem = 1
      join conplanoreduz ON c61_reduz = c69_credito
           and c61_anousu = c69_anousu

      -- recurso do reduzido
      left join orctiporec rec_reduzido on rec_reduzido.o15_codigo = c61_codigo
      left join fonterecurso f on f.orctiporec_id = rec_reduzido.o15_codigo
                and f.exercicio = c70_anousu

      -- recurso da conlancamrecurso
      left join contabilidade.conlancamrecurso ON c130_conlancam = c69_codlan
           and c130_conta = c69_credito
           and c130_natureza = 'C'
      left join orctiporec rec_cr on rec_cr.o15_codigo = c130_orctiporec
      left join fonterecurso f_cr on f_cr.orctiporec_id = rec_cr.o15_codigo
                and f_cr.exercicio = c70_anousu

      -- recurso da conlancamcomplementorecurso
      left join conlancamcomplementorecurso on o201_codlan = c70_codlan
      left join orctiporec rec_ccr on rec_ccr.o15_codigo = o201_orctiporec
      left join fonterecurso f_ccr on f_ccr.orctiporec_id = rec_ccr.o15_codigo
                and f_ccr.exercicio = c70_anousu
     where c70_anousu = 2023
       and c53_tipo in (152, 162, 121, 150, 160, 130)
) as x
 order by c71_coddoc, c70_codlan;

update conlancamrecurso set c130_orctiporec = recurso_certo
from w_ajuste_lancamentos_recursos
where c130_conlancam = c70_codlan
  and c130_orctiporec != recurso_certo;

update conlancamcomplementorecurso set  o201_orctiporec = recurso_certo, o201_complemento = compl_reduzido
from w_ajuste_lancamentos_recursos
where o201_codlan = c70_codlan
  and (o201_orctiporec != recurso_certo or o201_complemento != compl_reduzido);

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
