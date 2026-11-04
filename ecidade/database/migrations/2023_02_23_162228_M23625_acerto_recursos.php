<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23625AcertoRecursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop table if exists w_arrumar_recursos_empenhos;
create table w_arrumar_recursos_empenhos as
select e60_numemp,
       e60_instit,
       e60_emiss,
       rec_dot.o15_codigo,
       rec_dot.o15_complemento,
       rec_dot.o15_recurso as rec_dot_subrecurso,
       rec_emp.o15_recurso as rec_emp_subrecurso,
       o206_recurso,
       fonte_dot.gestao as fonte_dot_gestao,
       fonte_emp.gestao as fonte_emp_gestao
  from empempenho
  join orcdotacao on orcdotacao.o58_coddot = empempenho.e60_coddot
       and orcdotacao.o58_anousu = empempenho.e60_anousu
  join orcamento.orctiporec rec_dot on rec_dot.o15_codigo = orcdotacao.o58_codigo
  join fonterecurso fonte_dot on fonte_dot.orctiporec_id = rec_dot.o15_codigo
       and fonte_dot.exercicio = empempenho.e60_anousu

  join orcamento.origemcomplementorecurso on o206_numero = e60_numemp
       and o206_origem = 1
  join orcamento.orctiporec rec_emp on rec_emp.o15_codigo = o206_recurso
  join fonterecurso fonte_emp on fonte_emp.orctiporec_id = o206_recurso
       and fonte_emp.exercicio = empempenho.e60_anousu
  where e60_anousu = 2023
  and (    rec_dot.o15_recurso != rec_emp.o15_recurso
        or (rec_dot.o15_recurso = rec_emp.o15_recurso and fonte_emp.gestao != fonte_dot.gestao))
  order by 1;

create table bkp_treta_recursos_origemcomplementorecurso as
select origemcomplementorecurso.*
  from origemcomplementorecurso
  join w_arrumar_recursos_empenhos on e60_numemp = o206_numero
       and o206_origem = 1;

create table bkp_treta_recursos_conlancamcomplementorecurso as
select conlancamemp.*, conlancamcomplementorecurso.*
  from conlancamemp
  join w_arrumar_recursos_empenhos on e60_numemp = c75_numemp
  join conlancamcomplementorecurso on o201_codlan = c75_codlan;

update origemcomplementorecurso
   set o206_recurso = o15_codigo,
       o206_complementorecurso = o15_complemento
  from w_arrumar_recursos_empenhos
  where e60_numemp = o206_numero
       and o206_origem = 1;

update conlancamcomplementorecurso
   set o201_orctiporec = o15_codigo,
       o201_complemento = o15_complemento
  from w_arrumar_recursos_empenhos
  join conlancamemp on c75_numemp = e60_numemp
  where  o201_codlan = c75_codlan;

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
