<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23050AtualizacaoProcedureBalanceteReceitaComplemento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists balancete_receita_complemento;

create or replace function balancete_receita_complemento (
    f_ano integer,
    f_codfon int,
    f_cp varchar,
    f_dataInicio date,
    f_dataFim date
)
returns table (
    reduzido integer,
    fonte integer,
    ano integer,
    natureza varchar(13),
    descricao varchar,
    cp varchar(3),
    instituicao integer,
    orgao integer,
    unidade integer,
    esfera integer,
    fonte_recurso varchar(5),
    gestao varchar(10),
    siconfi varchar(4),
    recurso_lancamento integer,
    complemento_lancamento integer,
    valor_inicial numeric(17,2),
    previsao_adicional_acumulado numeric(17,2),
    previsao_atualizada numeric(17,2),
    arrecadado_anterior numeric(17,2),
    arrecadado_periodo numeric(17,2),
    valor_a_arrecadar numeric(17,2),
    arrecadado_acumulado numeric(17,2),
    previsao_adicional numeric(17,2),
    ordem integer
)
language plpgsql
as $$
declare

recordReceita record;

begin

for recordReceita in(

with dados_receita as (
  select o70_codrec as codrec,
         o57_codfon as fonte,
         o57_anousu as ano,
         o57_fonte as natureza,
         o57_descr as descricao,
         o70_instit as instituicao,
         o70_concarpeculiar as cp,
         o70_orcorgao as orgao,
         o70_orcunidade as unidade,
         o70_esferaorcamentaria as esfera,
         o70_codigo as recurso_receita,
         o15_complemento as complemento,
         o70_valor as valor_inicial
    from orcfontes
   join orcreceita on o57_codfon = o70_codfon
                  and o57_anousu = o70_anousu
   join orctiporec on o15_codigo = o70_codigo
  where o57_codfon = f_codfon
    and o57_anousu = f_ano
    and o70_concarpeculiar = f_cp
),  valores as (
  select codrec,
         c74_data,
         case
            when o201_sequencial is null
                then dados_receita.recurso_receita
            else o201_orctiporec
         end as recurso_lancamento,
         case
            when o201_sequencial is null
                then dados_receita.complemento
            else o201_complemento
         end as complemento_lancamento,
         case c53_tipo
                when 100 then round(c70_valor,2)::float8
                when 101 then round(c70_valor*-1,2)::float8
                else 0::float8
         end as arrecadado,
         case c53_tipo
                when 110 then round(c70_valor,2)::float8
                when 111 then round(c70_valor*-1,2)::float8
                else 0::float8
         end as previsao_adicional
  from dados_receita
  join conlancamrec on conlancamrec.c74_anousu = dados_receita.ano
                   and conlancamrec.c74_codrec = dados_receita.codrec
  join conlancam    on conlancam.c70_codlan = conlancamrec.c74_codlan
  join conlancamdoc on conlancamdoc.c71_codlan = conlancamrec.c74_codlan
  join conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
  left join contabilidade.conlancamcomplementorecurso on conlancamcomplementorecurso.o201_codlan = conlancamrec.c74_codlan
), valores_anteriores_desdobramento_receita as (
   select dados_receita.codrec,
          1 as ordem,
          coalesce(xxx.recurso_lancamento, dados_receita.recurso_receita) as recurso_lancamento,
          coalesce(xxx.complemento_lancamento, dados_receita.complemento) as complemento_lancamento,
          coalesce (xxx.arrecadado, 0) as arrecadado_anterior,
          coalesce (xxx.previsao_adicional, 0) as previsao_adicional_anterior,
          0  as arrecadado,
          0  as previsao_adicional
    from dados_receita
    left join valores as xxx on xxx.codrec = dados_receita.codrec
              and xxx.recurso_lancamento = dados_receita.recurso_receita
              and xxx.c74_data between to_date(f_ano::text||'-01-01'::text, 'yyyy-mm-dd') and (f_dataInicio - 1)
)
,  valores_anteriores_outros_desdobramentos as (
   select dados_receita.codrec,
          2 as ordem,
          xxx.recurso_lancamento as recurso_lancamento,
          xxx.complemento_lancamento as complemento_lancamento,
          xxx.arrecadado as arrecadado_anterior,
          xxx.previsao_adicional as previsao_adicional_anterior,
          0  as arrecadado,
          0  as previsao_adicional
    from dados_receita
    join valores as xxx on xxx.codrec = dados_receita.codrec
   where xxx.c74_data between to_date(f_ano::text||'-01-01'::text, 'yyyy-mm-dd') and (f_dataInicio - 1)
     and xxx.recurso_lancamento != dados_receita.recurso_receita

), valores_desdobramento_receita as (
   select dados_receita.codrec,
          1 as ordem,
          coalesce(xxx.recurso_lancamento, dados_receita.recurso_receita) as recurso_lancamento,
          coalesce(xxx.complemento_lancamento, dados_receita.complemento) as complemento_lancamento,
          0 as arrecadado_anterior,
          0 as previsao_adicional_anterior,
          coalesce (xxx.arrecadado, 0) as arrecadado,
          coalesce (xxx.previsao_adicional, 0) as previsao_adicional
    from dados_receita
    left join valores as xxx on xxx.codrec = dados_receita.codrec
              and xxx.recurso_lancamento = dados_receita.recurso_receita
              and xxx.c74_data between f_dataInicio and f_dataFim
),  valores_outros_desdobramentos as (
   select dados_receita.codrec,
          2 as ordem,
          xxx.recurso_lancamento as recurso_lancamento,
          xxx.complemento_lancamento as complemento_lancamento,
          0 as arrecadado_anterior,
          0 as previsao_adicional_anterior,
          coalesce (xxx.arrecadado, 0) as arrecadado,
          coalesce (xxx.previsao_adicional, 0)  as previsao_adicional
    from dados_receita
    join valores as xxx on xxx.codrec = dados_receita.codrec
   where xxx.c74_data  between f_dataInicio and f_dataFim
     and xxx.recurso_lancamento != dados_receita.recurso_receita
), soma_valores as (
  select x.codrec,
         x.ordem,
         x.recurso_lancamento,
         x.complemento_lancamento,
         sum(x.arrecadado_anterior) as arrecadado_anterior,
         sum(x.previsao_adicional_anterior) as previsao_adicional_anterior,
         sum(x.arrecadado) as arrecadado_periodo,
         sum(x.previsao_adicional) as previsao_adicional
    from (
              select * from valores_anteriores_desdobramento_receita
              union all
              select * from valores_anteriores_outros_desdobramentos
              union all
              select * from valores_desdobramento_receita
              union all
              select * from valores_outros_desdobramentos
    ) as x
    group by x.codrec,
             x.ordem,
             x.recurso_lancamento,
             x.complemento_lancamento
), balancete as (

select
       x.codrec,
       x.fonte,
       x.ano,
       x.natureza,
       x.descricao,
       x.instituicao,
       x.cp,
       x.orgao,
       x.unidade,
       x.esfera,
       x.recurso_receita,
       x.o15_recurso as fonte_recurso,
       x.gestao,
       x.codigo_siconfi,
       x.recurso_lancamento,
       x.complemento_lancamento,
       x.ordem,
       round(x.valor_inicial, 2) as valor_inicial,
       round(x.previsao_adicional + x.previsao_adicional_anterior, 2) as previsao_adicional_acumulado,
       round(x.valor_inicial + x.previsao_adicional + x.previsao_adicional_anterior, 2) as previsao_atualizada,
       round(x.arrecadado_anterior, 2) as arrecadado_anterior,
       round(x.arrecadado_periodo, 2) as arrecadado_periodo,
       round(
         ( (x.valor_inicial + x.previsao_adicional + x.previsao_adicional_anterior) -
           (x.arrecadado_anterior + x.arrecadado_periodo)
         ),
       2) as valor_a_arrecadar,
       round((x.arrecadado_anterior + x.arrecadado_periodo), 2) as arrecadado_acumulado,
       round(x.previsao_adicional, 2) as previsao_adicional
 from (
       select
              dados_receita.codrec,
              dados_receita.fonte,
              dados_receita.ano,
              dados_receita.natureza,
              dados_receita.descricao,
              dados_receita.instituicao,
              dados_receita.cp,
              dados_receita.orgao,
              dados_receita.unidade,
              dados_receita.esfera,
              dados_receita.recurso_receita,
              case
                 when dados_receita.recurso_receita = soma_valores.recurso_lancamento
                 then dados_receita.valor_inicial
                 else 0
              end as valor_inicial,
              orctiporec.o15_recurso,
              fonterecurso.gestao,
              fonterecurso.codigo_siconfi,
              soma_valores.recurso_lancamento,
              soma_valores.complemento_lancamento,
              soma_valores.arrecadado_anterior,
              soma_valores.previsao_adicional_anterior,
              soma_valores.arrecadado_periodo,
              soma_valores.previsao_adicional,
              soma_valores.ordem
         from dados_receita
         join soma_valores on soma_valores.codrec = dados_receita.codrec
         join orctiporec on orctiporec.o15_codigo = soma_valores.recurso_lancamento
         join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
              and fonterecurso.exercicio = dados_receita.ano
         order by soma_valores.ordem
  ) as x
)
select * from balancete
    ) loop

        reduzido := recordReceita.codrec;
        fonte := recordReceita.fonte;
        ano := recordReceita.ano;
        natureza := recordReceita.natureza;
        descricao := recordReceita.descricao;
        cp := recordReceita.cp;
        instituicao := recordReceita.instituicao;
        orgao := recordReceita.orgao;
        unidade := recordReceita.unidade;
        esfera := recordReceita.esfera;
        valor_inicial := recordReceita.valor_inicial;
        fonte_recurso := recordReceita.fonte_recurso;
        gestao := recordReceita.gestao;
        siconfi := recordReceita.codigo_siconfi;
        recurso_lancamento := recordReceita.recurso_lancamento;
        complemento_lancamento := recordReceita.complemento_lancamento;
        previsao_adicional_acumulado := recordReceita.previsao_adicional_acumulado;
        previsao_atualizada := recordReceita.previsao_atualizada;
        arrecadado_anterior := recordReceita.arrecadado_anterior;
        arrecadado_periodo := recordReceita.arrecadado_periodo;
        valor_a_arrecadar := recordReceita.valor_a_arrecadar;
        arrecadado_acumulado := recordReceita.arrecadado_acumulado;
        previsao_adicional := recordReceita.previsao_adicional;
        ordem := recordReceita.ordem;

        return next;
end loop;
end; $$
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
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists balancete_receita_complemento;

create or replace function balancete_receita_complemento (
    f_ano integer,
    f_codfon int,
    f_cp varchar,
    f_dataInicio date,
    f_dataFim date
)
returns table (
    reduzido integer,
    fonte integer,
    ano integer,
    natureza varchar(13),
    descricao varchar,
    cp varchar(3),
    instituicao integer,
    orgao integer,
    unidade integer,
    esfera integer,
    fonte_recurso varchar(5),
    recurso_lancamento integer,
    complemento_lancamento integer,
    valor_inicial numeric(17,2),
    previsao_adicional_acumulado numeric(17,2),
    previsao_atualizada numeric(17,2),
    arrecadado_anterior numeric(17,2),
    arrecadado_periodo numeric(17,2),
    valor_a_arrecadar numeric(17,2),
    arrecadado_acumulado numeric(17,2),
    previsao_adicional numeric(17,2),
    ordem integer
)
language plpgsql
as $$
declare

recordReceita record;

begin

for recordReceita in(

with dados_receita as (
  select o70_codrec as codrec,
         o57_codfon as fonte,
         o57_anousu as ano,
         o57_fonte as natureza,
         o57_descr as descricao,
         o70_instit as instituicao,
         o70_concarpeculiar as cp,
         o70_orcorgao as orgao,
         o70_orcunidade as unidade,
         o70_esferaorcamentaria as esfera,
         o70_codigo as recurso_receita,
         o15_complemento as complemento,
         o70_valor as valor_inicial
    from orcfontes
   join orcreceita on o57_codfon = o70_codfon
                  and o57_anousu = o70_anousu
   join orctiporec on o15_codigo = o70_codigo
  where o57_codfon = f_codfon
    and o57_anousu = f_ano
    and o70_concarpeculiar = f_cp
),  valores as (
  select codrec,
         c74_data,
         case
            when o201_sequencial is null
                then dados_receita.recurso_receita
            else o201_orctiporec
         end as recurso_lancamento,
         case
            when o201_sequencial is null
                then dados_receita.complemento
            else o201_complemento
         end as complemento_lancamento,
         case c53_tipo
                when 100 then round(c70_valor,2)::float8
                when 101 then round(c70_valor*-1,2)::float8
                else 0::float8
         end as arrecadado,
         case c53_tipo
                when 110 then round(c70_valor,2)::float8
                when 111 then round(c70_valor*-1,2)::float8
                else 0::float8
         end as previsao_adicional
  from dados_receita
  join conlancamrec on conlancamrec.c74_anousu = dados_receita.ano
                   and conlancamrec.c74_codrec = dados_receita.codrec
  join conlancam    on conlancam.c70_codlan = conlancamrec.c74_codlan
  join conlancamdoc on conlancamdoc.c71_codlan = conlancamrec.c74_codlan
  join conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
  left join contabilidade.conlancamcomplementorecurso on conlancamcomplementorecurso.o201_codlan = conlancamrec.c74_codlan
), valores_anteriores_desdobramento_receita as (
   select dados_receita.codrec,
          1 as ordem,
          coalesce(xxx.recurso_lancamento, dados_receita.recurso_receita) as recurso_lancamento,
          coalesce(xxx.complemento_lancamento, dados_receita.complemento) as complemento_lancamento,
          coalesce (xxx.arrecadado, 0) as arrecadado_anterior,
          coalesce (xxx.previsao_adicional, 0) as previsao_adicional_anterior,
          0  as arrecadado,
          0  as previsao_adicional
    from dados_receita
    left join valores as xxx on xxx.codrec = dados_receita.codrec
              and xxx.recurso_lancamento = dados_receita.recurso_receita
              and xxx.c74_data between to_date(f_ano::text||'-01-01'::text, 'yyyy-mm-dd') and (f_dataInicio - 1)
)
,  valores_anteriores_outros_desdobramentos as (
   select dados_receita.codrec,
          2 as ordem,
          xxx.recurso_lancamento as recurso_lancamento,
          xxx.complemento_lancamento as complemento_lancamento,
          xxx.arrecadado as arrecadado_anterior,
          xxx.previsao_adicional as previsao_adicional_anterior,
          0  as arrecadado,
          0  as previsao_adicional
    from dados_receita
    join valores as xxx on xxx.codrec = dados_receita.codrec
   where xxx.c74_data between to_date(f_ano::text||'-01-01'::text, 'yyyy-mm-dd') and (f_dataInicio - 1)
     and xxx.recurso_lancamento != dados_receita.recurso_receita

), valores_desdobramento_receita as (
   select dados_receita.codrec,
          1 as ordem,
          coalesce(xxx.recurso_lancamento, dados_receita.recurso_receita) as recurso_lancamento,
          coalesce(xxx.complemento_lancamento, dados_receita.complemento) as complemento_lancamento,
          0 as arrecadado_anterior,
          0 as previsao_adicional_anterior,
          coalesce (xxx.arrecadado, 0) as arrecadado,
          coalesce (xxx.previsao_adicional, 0) as previsao_adicional
    from dados_receita
    left join valores as xxx on xxx.codrec = dados_receita.codrec
              and xxx.recurso_lancamento = dados_receita.recurso_receita
              and xxx.c74_data between f_dataInicio and f_dataFim
),  valores_outros_desdobramentos as (
   select dados_receita.codrec,
          2 as ordem,
          xxx.recurso_lancamento as recurso_lancamento,
          xxx.complemento_lancamento as complemento_lancamento,
          0 as arrecadado_anterior,
          0 as previsao_adicional_anterior,
          coalesce (xxx.arrecadado, 0) as arrecadado,
          coalesce (xxx.previsao_adicional, 0)  as previsao_adicional
    from dados_receita
    join valores as xxx on xxx.codrec = dados_receita.codrec
   where xxx.c74_data  between f_dataInicio and f_dataFim
     and xxx.recurso_lancamento != dados_receita.recurso_receita
), soma_valores as (
  select x.codrec,
         x.ordem,
         x.recurso_lancamento,
         x.complemento_lancamento,
         sum(x.arrecadado_anterior) as arrecadado_anterior,
         sum(x.previsao_adicional_anterior) as previsao_adicional_anterior,
         sum(x.arrecadado) as arrecadado_periodo,
         sum(x.previsao_adicional) as previsao_adicional
    from (
              select * from valores_anteriores_desdobramento_receita
              union all
              select * from valores_anteriores_outros_desdobramentos
              union all
              select * from valores_desdobramento_receita
              union all
              select * from valores_outros_desdobramentos
    ) as x
    group by x.codrec,
             x.ordem,
             x.recurso_lancamento,
             x.complemento_lancamento
), balancete as (

select
       x.codrec,
       x.fonte,
       x.ano,
       x.natureza,
       x.descricao,
       x.instituicao,
       x.cp,
       x.orgao,
       x.unidade,
       x.esfera,
       x.recurso_receita,
       x.o15_recurso as fonte_recurso,
       x.recurso_lancamento,
       x.complemento_lancamento,
       x.ordem,
       round(x.valor_inicial, 2) as valor_inicial,
       round(x.previsao_adicional + x.previsao_adicional_anterior, 2) as previsao_adicional_acumulado,
       round(x.valor_inicial + x.previsao_adicional + x.previsao_adicional_anterior, 2) as previsao_atualizada,
       round(x.arrecadado_anterior, 2) as arrecadado_anterior,
       round(x.arrecadado_periodo, 2) as arrecadado_periodo,
       round(
         ( (x.valor_inicial + x.previsao_adicional + x.previsao_adicional_anterior) -
           (x.arrecadado_anterior + x.arrecadado_periodo)
         ),
       2) as valor_a_arrecadar,
       round((x.arrecadado_anterior + x.arrecadado_periodo), 2) as arrecadado_acumulado,
       round(x.previsao_adicional, 2) as previsao_adicional
 from (
       select
              dados_receita.codrec,
              dados_receita.fonte,
              dados_receita.ano,
              dados_receita.natureza,
              dados_receita.descricao,
              dados_receita.instituicao,
              dados_receita.cp,
              dados_receita.orgao,
              dados_receita.unidade,
              dados_receita.esfera,
              dados_receita.recurso_receita,
              case
                 when dados_receita.recurso_receita = soma_valores.recurso_lancamento
                 then dados_receita.valor_inicial
                 else 0
              end as valor_inicial,
              orctiporec.o15_recurso,
              soma_valores.recurso_lancamento,
              soma_valores.complemento_lancamento,
              soma_valores.arrecadado_anterior,
              soma_valores.previsao_adicional_anterior,
              soma_valores.arrecadado_periodo,
              soma_valores.previsao_adicional,
              soma_valores.ordem
         from dados_receita
         join soma_valores on soma_valores.codrec = dados_receita.codrec
         join orctiporec on orctiporec.o15_codigo = soma_valores.recurso_lancamento
         order by soma_valores.ordem
  ) as x
)
select * from balancete
    ) loop

        reduzido := recordReceita.codrec;
        fonte := recordReceita.fonte;
        ano := recordReceita.ano;
        natureza := recordReceita.natureza;
        descricao := recordReceita.descricao;
        cp := recordReceita.cp;
        instituicao := recordReceita.instituicao;
        orgao := recordReceita.orgao;
        unidade := recordReceita.unidade;
        esfera := recordReceita.esfera;
        valor_inicial := recordReceita.valor_inicial;
        fonte_recurso := recordReceita.fonte_recurso;
        recurso_lancamento := recordReceita.recurso_lancamento;
        complemento_lancamento := recordReceita.complemento_lancamento;
        previsao_adicional_acumulado := recordReceita.previsao_adicional_acumulado;
        previsao_atualizada := recordReceita.previsao_atualizada;
        arrecadado_anterior := recordReceita.arrecadado_anterior;
        arrecadado_periodo := recordReceita.arrecadado_periodo;
        valor_a_arrecadar := recordReceita.valor_a_arrecadar;
        arrecadado_acumulado := recordReceita.arrecadado_acumulado;
        previsao_adicional := recordReceita.previsao_adicional;
        ordem := recordReceita.ordem;

        return next;
end loop;
end; $$
SQL
        );
    }
}
