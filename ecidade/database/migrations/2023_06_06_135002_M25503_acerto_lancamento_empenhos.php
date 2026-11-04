<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25503AcertoLancamentoEmpenhos extends Migration
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
select x.*,
       case when comple_execucao = compl_dotacao
         then cod_rec_dotacao
       else
       (select o15_codigo
          from orctiporec
          join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
               and fonterecurso.exercicio = x.c70_anousu
        where orctiporec.o15_complemento = comple_execucao
          and orctiporec.o15_recurso = x.subrec_dotacao
          and fonterecurso.gestao = x.gestao_dotacao
          limit 1
        )
        end as recurso_certo
    from (
    select distinct
           c70_codlan,
           c70_anousu,
           c70_data,
           c71_coddoc,
           c75_numemp,
           c73_codlan is not null as has_conlancamdot,
           c130_sequencial is not null as has_conlancamrecurso,
           o201_sequencial is not null as has_conlancamcomplementorecurso,

           case
             when e60_anousu < c70_anousu
               then (select o206_complementorecurso from origemcomplementorecurso where o206_origem = 10 and o206_numero = e60_numemp)
            else (select o206_complementorecurso from origemcomplementorecurso where o206_origem = 1 and o206_numero = e60_numemp)
           end as comple_execucao,
           -- recurso da dotacao
           rec_dot.o15_codigo as cod_rec_dotacao,
           f.gestao as gestao_dotacao,
           rec_dot.o15_recurso as subrec_dotacao,
           rec_dot.o15_complemento as compl_dotacao,
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
      left join conlancamemp on c75_codlan = c70_codlan
      left join empempenho on e60_numemp = c75_numemp
      -- left join origemcomplementorecurso on o206_origem = 1 and o206_numero = e60_numemp
      left join conlancamdot on c73_codlan = c70_codlan
      left join orcdotacao on (o58_anousu, o58_coddot) = (e60_anousu, e60_coddot)

      -- recurso da dotacao
      left join orctiporec rec_dot on rec_dot.o15_codigo = o58_codigo
      left join fonterecurso f on f.orctiporec_id = rec_dot.o15_codigo
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
       and c53_tipo in (10, 11, 20, 21, 30, 31)
) as x
 where (    (gestao_dotacao != gestao_cr or gestao_dotacao != gestao_ccr)
         or (    (gestao_dotacao = gestao_cr and subrec_dotacao != subrec_cr)
              or (gestao_dotacao = gestao_ccr and subrec_dotacao != subrec_ccr)
            )
         or (has_conlancamrecurso is false or has_conlancamcomplementorecurso is false)
       )

 order by c71_coddoc, c70_codlan;

update conlancamrecurso set c130_orctiporec = recurso_certo
from w_ajuste_lancamentos_recursos
where c130_conlancam = c70_codlan
  and c130_orctiporec != recurso_certo;

update conlancamcomplementorecurso set  o201_orctiporec = o15_codigo, o201_complemento = o15_complemento
from w_ajuste_lancamentos_recursos
join orctiporec on o15_codigo = recurso_certo
where o201_codlan = c70_codlan
  and (o201_orctiporec != o15_codigo or o201_complemento != o15_complemento);

insert into contabilidade.conlancamcomplementorecurso
select nextval('conlancamcomplementorecurso_o201_sequencial_seq'),
       c70_codlan,
       o15_complemento,
       o15_codigo
  from w_ajuste_lancamentos_recursos
  join orctiporec on o15_codigo = recurso_certo
 where has_conlancamcomplementorecurso is false;


insert into conlancamrecurso
select nextval('conlancamrecurso_c130_sequencial_seq'),
       c70_codlan,
       o15_codigo,
       c69_credito,
       c70_anousu,
       'C'
  from w_ajuste_lancamentos_recursos
  join orctiporec on o15_codigo = recurso_certo
  join conlancamval on c69_codlan = c70_codlan
  where has_conlancamrecurso is false
union
select nextval('conlancamrecurso_c130_sequencial_seq'),
       c70_codlan,
       o15_codigo,
       c69_debito,
       c70_anousu,
       'D'
  from w_ajuste_lancamentos_recursos
  join orctiporec on o15_codigo = recurso_certo
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
