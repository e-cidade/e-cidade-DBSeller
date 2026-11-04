<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23990AcertoRecursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->empenho();
        $this->autorizacaoEmpenho();
        $this->recibos();
        $this->planilhas();
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


    /**
     * Arruma os recursos dos empenhos
     */
    private function empenho()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop table if exists w_ajuste_recursos_empenho;
create table w_ajuste_recursos_empenho as
select *,
        (select o15_codigo
           from orctiporec
           join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                and fonterecurso.exercicio = x.e60_anousu
           where orctiporec.o15_recurso = x.subrec
             and orctiporec.o15_complemento = x.o206_complementorecurso
             and fonterecurso.gestao = x.gestao) as recurso_certo,
        (recuso_salvo && recursos_validos ) as xy
from (
    select e60_numemp,
           e60_anousu,
           fv.gestao,
           o206_recurso,
           o206_complementorecurso,
           rec_dot.o15_codigo as recurso_despesa,
           rec_dot.o15_complemento as compl_recurso_despesa,
           rec_validos.o15_recurso as subrec,
           array_agg(rec_validos.o15_codigo) as recursos_validos,
           array_agg(o206_recurso) as recuso_salvo
        from empempenho
        join orcdotacao on (o58_anousu, o58_coddot) = (e60_anousu, e60_coddot)
        join origemcomplementorecurso on o206_origem = 1 and o206_numero = e60_numemp
        join orctiporec rec_dot on o15_codigo = o58_codigo
        join fonterecurso f on f.orctiporec_id = rec_dot.o15_codigo and f.exercicio = e60_anousu
        join orctiporec rec_validos on rec_validos.o15_recurso = rec_dot.o15_recurso
        join fonterecurso fv on fv.orctiporec_id = rec_validos.o15_codigo and fv.exercicio = e60_anousu
       where e60_anousu = 2023
         and f.gestao = fv.gestao
       group by 1, 2, 3, 4, 5, 6, 7, 8
) as x
where (recuso_salvo && recursos_validos ) is false;

update origemcomplementorecurso set o206_recurso = recurso_certo
   from w_ajuste_recursos_empenho
  where o206_origem = 1
   and o206_numero = e60_numemp
   and recurso_certo is not null;

update conlancamcomplementorecurso set o201_orctiporec = recurso_certo
   from w_ajuste_recursos_empenho
   join conlancamemp on c75_numemp = e60_numemp
 where o201_codlan = c75_codlan
   and o201_orctiporec != recurso_certo
   and recurso_certo is not null;

update origemcomplementorecurso
    set o206_recurso = recurso_despesa,
    o206_complementorecurso = compl_recurso_despesa
   from w_ajuste_recursos_empenho
  where o206_origem = 1
   and o206_numero = e60_numemp
   and recurso_certo is null;

update conlancamcomplementorecurso
    set o201_orctiporec = recurso_despesa,
        o201_complemento = compl_recurso_despesa
   from w_ajuste_recursos_empenho
   join conlancamemp on c75_numemp = e60_numemp
 where o201_codlan = c75_codlan
   and recurso_certo is null;
SQL
        );
    }

    private function autorizacaoEmpenho()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop table if exists w_ajuste_recursos_autorizacoes;

create table w_ajuste_recursos_autorizacoes as
select *,
        (select o15_codigo
           from orctiporec
           join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                and fonterecurso.exercicio = x.e54_anousu
           where orctiporec.o15_recurso = x.subrec
             and orctiporec.o15_complemento = x.o206_complementorecurso
             and fonterecurso.gestao = x.gestao) as recurso_certo,
        (recuso_salvo && recursos_validos ) as xy
from (
    select e54_autori,
           e54_anousu,
           fv.gestao,
           o206_recurso,
           o206_complementorecurso,
           rec_validos.o15_recurso as subrec,
           rec_dot.o15_codigo as recurso_despesa,
           rec_dot.o15_complemento as compl_recurso_despesa,
           array_agg(rec_validos.o15_codigo) as recursos_validos,
           array_agg(o206_recurso) as recuso_salvo
        from empautoriza
        join empautidot on e54_autori  = e56_autori
        join orcdotacao on (o58_anousu, o58_coddot) = (e56_anousu, e56_coddot)
        join origemcomplementorecurso on o206_origem = 100 and o206_numero = e54_autori
        join orctiporec rec_dot on o15_codigo = o58_codigo
        join fonterecurso f on f.orctiporec_id = rec_dot.o15_codigo and f.exercicio = e54_anousu
        join orctiporec rec_validos on rec_validos.o15_recurso = rec_dot.o15_recurso
        join fonterecurso fv on fv.orctiporec_id = rec_validos.o15_codigo and fv.exercicio = e54_anousu
       where e54_anousu = 2023
         and f.gestao = fv.gestao
       group by 1, 2, 3, 4, 5, 6, 7, 8
) as x
where (recuso_salvo && recursos_validos ) is false;

update origemcomplementorecurso set o206_recurso = recurso_certo
   from w_ajuste_recursos_autorizacoes
  where o206_origem = 100
   and o206_numero = e54_autori
   and recurso_certo is not null;

update origemcomplementorecurso
   set o206_recurso = recurso_despesa,
       o206_complementorecurso = compl_recurso_despesa
  from w_ajuste_recursos_autorizacoes
 where o206_origem = 1
   and o206_numero = e54_autori
   and recurso_certo is null;
SQL
        );
    }

    private function planilhas()
    {
        DB::connection()->getPdo()->exec(<<<SQL

drop table if exists w_planilhas_arrumar_recursos;

create table w_planilhas_arrumar_recursos as
with planilhas as (
    select k81_seqpla,
           k81_receita,
           extract(year from k80_data) as ano,
           k82_id,
           k82_data,
           k82_autent
    from placaixarec
    join placaixa on k80_codpla = k81_codpla
    join corplacaixa on corplacaixa.k82_seqpla = placaixarec.k81_seqpla
    where extract(year from k80_data) = 2023
), planilha_receita as (
  select planilhas.*,
         o206_recurso as rec_salvo,
         o206_complementorecurso as comple_salvo
    from planilhas
    join origemcomplementorecurso on o206_origem = 200
         and o206_numero = k81_seqpla
), recurso_origem_receita as (
    select r.*,
           'O' as tipo,
           o70_codigo as recurso_receita,
           o15_complemento as compl_recurso_receita
      from planilha_receita r
      join taborc on taborc.k02_codigo = r.k81_receita
           and taborc.k02_anousu = r.ano
      join orcreceita on orcreceita.o70_codrec = taborc.k02_codrec
           and orcreceita.o70_anousu = taborc.k02_anousu
      join orctiporec on o15_codigo = o70_codigo
    union

    select r.*,
           'E' as tipo,
           c61_codigo,
           o15_complemento
      from planilha_receita r
      join tabplan on tabplan.k02_codigo = r.k81_receita
           and tabplan.k02_anousu = r.ano
      join conplanoreduz on conplanoreduz.c61_anousu = tabplan.k02_anousu
           and conplanoreduz.c61_reduz = tabplan.k02_reduz
      join orctiporec on o15_codigo = c61_codigo
), recursos as (
    select r.*,
           fv.gestao,
           rec_validos.o15_recurso as subrec,
           array_agg(rec_validos.o15_codigo) as recursos_validos,
           array_agg(rec_salvo) as recuso_salvo
      from recurso_origem_receita r
      join orctiporec rec_ on o15_codigo = recurso_receita
      join fonterecurso f on f.orctiporec_id = rec_.o15_codigo and f.exercicio = ano
      join orctiporec rec_validos on rec_validos.o15_recurso = rec_.o15_recurso
      join fonterecurso fv on fv.orctiporec_id = rec_validos.o15_codigo
           and fv.gestao = f.gestao
           and fv.exercicio = ano
     group by k81_seqpla, k81_receita, ano, k82_id, k82_data, k82_autent, rec_salvo, comple_salvo, tipo, recurso_receita, compl_recurso_receita, fv.gestao, rec_validos.o15_recurso
), arrumar as (
    select recursos.*,
            (recuso_salvo && recursos_validos ),
           (select o15_codigo
              from orctiporec
              join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                   and fonterecurso.exercicio = recursos.ano
             where orctiporec.o15_recurso = recursos.subrec
               and orctiporec.o15_complemento = recursos.comple_salvo
               and fonterecurso.gestao = recursos.gestao) as recurso_certo
      from recursos
    where (recuso_salvo && recursos_validos ) is false
) select * from arrumar ;

update origemcomplementorecurso set o206_recurso = recurso_certo
   from w_planilhas_arrumar_recursos
  where o206_origem = 200
    and o206_numero = k81_seqpla
    and recurso_certo is not null;

update origemcomplementorecurso
   set o206_recurso = recurso_receita,
       o206_complementorecurso = compl_recurso_receita
   from w_planilhas_arrumar_recursos
  where o206_origem = 200
    and o206_numero = k81_seqpla
    and recurso_certo is null;

update conlancamcomplementorecurso set o201_orctiporec = recurso_certo
   from w_planilhas_arrumar_recursos
   join conlancamcorrente on c86_id = k82_id
        and c86_data = k82_data
        and c86_autent = k82_autent
 where o201_codlan = c86_conlancam
   and o201_orctiporec != recurso_certo
   and recurso_certo is not null;

update conlancamcomplementorecurso
    set  o201_orctiporec = recurso_receita,
       o201_complemento = compl_recurso_receita
   from w_planilhas_arrumar_recursos
   join conlancamcorrente on c86_id = k82_id
        and c86_data = k82_data
        and c86_autent = k82_autent
 where o201_codlan = c86_conlancam
   and recurso_certo is null;
SQL
        );
    }

    private function recibos()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop table if exists w_recibos_arrumar_recursos;

create table w_recibos_arrumar_recursos as
with recibos_ as (
  select k00_numpre,
         k00_receit,
         extract (year from k00_dtpaga) as ano,
         o206_recurso as rec_salvo,
         o206_complementorecurso as comple_salvo,
         k12_id,
         k12_data,
         k12_autent
    from arrepaga
    join origemcomplementorecurso on o206_origem = 300
         and o206_numero = k00_numpre
    left join cornump on k12_numpre = k00_numpre
         and k12_numpar = k00_numpar
         and k12_receit = k00_receit
    where extract (year from k00_dtpaga) = 2023
), recurso_origem_receita as (
    select r.*,
           'O' as tipo,
           o70_codigo as recurso_receita,
           o15_complemento as compl_recurso_receita
      from recibos_ r
      join taborc on taborc.k02_codigo = r.k00_receit
           and taborc.k02_anousu = r.ano
      join orcreceita on orcreceita.o70_codrec = taborc.k02_codrec
           and orcreceita.o70_anousu = taborc.k02_anousu
      join orctiporec on o15_codigo = o70_codigo
    union

    select r.*,
           'E' as tipo,
           c61_codigo,
           o15_complemento
      from recibos_ r
      join tabplan on tabplan.k02_codigo = r.k00_receit
           and tabplan.k02_anousu = r.ano
      join conplanoreduz on conplanoreduz.c61_anousu = tabplan.k02_anousu
           and conplanoreduz.c61_reduz = tabplan.k02_reduz
      join orctiporec on o15_codigo = c61_codigo
), recursos as (
    select r.*,
           fv.gestao,
           rec_validos.o15_recurso as subrec,
           array_agg(rec_validos.o15_codigo) as recursos_validos,
           array_agg(rec_salvo) as recuso_salvo
      from recurso_origem_receita r
      join orctiporec rec_ on o15_codigo = recurso_receita
      join fonterecurso f on f.orctiporec_id = rec_.o15_codigo and f.exercicio = ano
      join orctiporec rec_validos on rec_validos.o15_recurso = rec_.o15_recurso
      join fonterecurso fv on fv.orctiporec_id = rec_validos.o15_codigo
           and fv.gestao = f.gestao
           and fv.exercicio = ano
     group by k00_numpre, k00_receit, ano, rec_salvo, comple_salvo, k12_id, k12_data, k12_autent, tipo, recurso_receita, compl_recurso_receita, fv.gestao, rec_validos.o15_recurso
), arrumar as (
    select recursos.*,
           (select o15_codigo
              from orctiporec
              join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                   and fonterecurso.exercicio = recursos.ano
             where orctiporec.o15_recurso = recursos.subrec
               and orctiporec.o15_complemento = recursos.comple_salvo
               and fonterecurso.gestao = recursos.gestao) as recurso_certo
      from recursos
    where (recuso_salvo && recursos_validos ) is false
) select * from arrumar;

update origemcomplementorecurso set o206_recurso = recurso_certo
   from w_recibos_arrumar_recursos
  where o206_origem = 300
    and o206_numero = k00_numpre
    and recurso_certo is not null;

update conlancamcomplementorecurso set o201_orctiporec = recurso_certo
   from w_recibos_arrumar_recursos
   join conlancamcorrente on c86_id = k12_id
        and c86_data = k12_data
        and c86_autent = k12_autent
 where o201_codlan = c86_conlancam
   and o201_orctiporec != recurso_certo
   and recurso_certo is not null;

update origemcomplementorecurso set o206_recurso = recurso_certo
   from w_recibos_arrumar_recursos
  where o206_origem = 300
    and o206_numero = k00_numpre
    and recurso_certo is null;

update conlancamcomplementorecurso set o201_orctiporec = recurso_certo
   from w_recibos_arrumar_recursos
   join conlancamcorrente on c86_id = k12_id
        and c86_data = k12_data
        and c86_autent = k12_autent
 where o201_codlan = c86_conlancam
   and recurso_certo is null;
SQL
        );
    }
}
