<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25371PlBalRec extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Essas funções executam o balancete de receita sem considerar o recurso / complemento da execução
         */
        $this->plBalanceteReceitaEcidade();
        $this->plBalanceteReceitaEmentarioPadrao();
        $this->plBalanceteReceitaUniao();
        $this->plBalanceteReceitaEstado();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.balancete_receita_exercicio;
drop function if exists contabilidade.balancete_receita_exercicio_emetario_padrao;
drop function if exists contabilidade.balancete_receita_exercicio_plano_uniao;
drop function if exists contabilidade.balancete_receita_exercicio_plano_estadual;
SQL
        );
    }

    private function plBalanceteReceitaEcidade()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.balancete_receita_exercicio;

create or replace function contabilidade.balancete_receita_exercicio (
    f_ano integer,
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
    recurso_receita integer,
    codigo_conplano integer,
    valor_inicial numeric(17,2),
    previsao_adicional_acumulado numeric(17,2),
    previsao_atualizada numeric(17,2),
    arrecadado_anterior numeric(17,2),
    arrecadado_periodo numeric(17,2),
    valor_a_arrecadar numeric(17,2),
    arrecadado_acumulado numeric(17,2),
    previsao_adicional numeric(17,2)
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
         c60_codigo as codigo_conplano,
         o70_valor as valor_inicial
    from orcamento.orcfontes
    join orcamento.orcreceita on o70_codfon = o57_codfon and o70_anousu = o57_anousu
    join contabilidade.conplanoorcamento on c60_codcon = o57_codfon and c60_anousu = o57_anousu
    join contabilidade.conplanoorcamentoanalitica on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
  where o57_anousu = f_ano
),  valores as (
  select codrec,
         c74_data,
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
), valores_anteriores_desdobramento_receita as (
   select dados_receita.codrec,
          coalesce (xxx.arrecadado, 0) as arrecadado_anterior,
          coalesce (xxx.previsao_adicional, 0) as previsao_adicional_anterior,
          0  as arrecadado,
          0  as previsao_adicional
    from dados_receita
    left join valores as xxx on xxx.codrec = dados_receita.codrec
              and xxx.c74_data between to_date(f_ano::text||'-01-01'::text, 'yyyy-mm-dd') and (f_dataInicio - 1)
), valores_desdobramento_receita as (
   select dados_receita.codrec,
          0 as arrecadado_anterior,
          0 as previsao_adicional_anterior,
          coalesce (xxx.arrecadado, 0) as arrecadado,
          coalesce (xxx.previsao_adicional, 0) as previsao_adicional
    from dados_receita
    left join valores as xxx on xxx.codrec = dados_receita.codrec
              and xxx.c74_data between f_dataInicio and f_dataFim
), soma_valores as (
  select x.codrec,
         sum(x.arrecadado_anterior) as arrecadado_anterior,
         sum(x.previsao_adicional_anterior) as previsao_adicional_anterior,
         sum(x.arrecadado) as arrecadado_periodo,
         sum(x.previsao_adicional) as previsao_adicional
    from (
              select * from valores_anteriores_desdobramento_receita
              union all
              select * from valores_desdobramento_receita
    ) as x
    group by x.codrec
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
       x.codigo_conplano,
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
              dados_receita.codigo_conplano,
              dados_receita.valor_inicial,
              soma_valores.arrecadado_anterior,
              soma_valores.previsao_adicional_anterior,
              soma_valores.arrecadado_periodo,
              soma_valores.previsao_adicional
         from dados_receita
         join soma_valores on soma_valores.codrec = dados_receita.codrec
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
        codigo_conplano := recordReceita.codigo_conplano;
        valor_inicial := recordReceita.valor_inicial;
        recurso_receita := recordReceita.recurso_receita;
        previsao_adicional_acumulado := recordReceita.previsao_adicional_acumulado;
        previsao_atualizada := recordReceita.previsao_atualizada;
        arrecadado_anterior := recordReceita.arrecadado_anterior;
        arrecadado_periodo := recordReceita.arrecadado_periodo;
        valor_a_arrecadar := recordReceita.valor_a_arrecadar;
        arrecadado_acumulado := recordReceita.arrecadado_acumulado;
        previsao_adicional := recordReceita.previsao_adicional;

        return next;
end loop;
end; $$
SQL
        );
    }

    private function plBalanceteReceitaUniao()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.balancete_receita_exercicio_plano_uniao;

create or replace function contabilidade.balancete_receita_exercicio_plano_uniao (
    f_ano integer,
    f_dataInicio date,
    f_dataFim date
)
returns table (
    ano integer,
    natureza varchar(13),
    descricao varchar,
    cp varchar(3),
    instituicao integer,
    orgao integer,
    unidade integer,
    esfera integer,
    recurso_receita integer,
    valor_inicial numeric(17,2),
    previsao_adicional_acumulado numeric(17,2),
    previsao_atualizada numeric(17,2),
    arrecadado_anterior numeric(17,2),
    arrecadado_periodo numeric(17,2),
    valor_a_arrecadar numeric(17,2),
    arrecadado_acumulado numeric(17,2),
    previsao_adicional numeric(17,2),
    reduzido integer,
    fonte integer,
    codigo_conplano integer
)
language plpgsql
as $$
declare

recordReceita record;

begin

for recordReceita in(
    select * from balancete_receita_exercicio_emetario_padrao(f_ano, true, f_dataInicio, f_dataFim)
) loop
        ano = recordReceita.ano;
        natureza = recordReceita.natureza;
        descricao = recordReceita.descricao;
        cp = recordReceita.cp;
        instituicao = recordReceita.instituicao;
        orgao = recordReceita.orgao;
        unidade = recordReceita.unidade;
        esfera = recordReceita.esfera;
        recurso_receita = recordReceita.recurso_receita;
        valor_inicial = recordReceita.valor_inicial;
        previsao_adicional_acumulado = recordReceita.previsao_adicional_acumulado;
        previsao_atualizada = recordReceita.previsao_atualizada;
        arrecadado_anterior = recordReceita.arrecadado_anterior;
        arrecadado_periodo = recordReceita.arrecadado_periodo;
        valor_a_arrecadar = recordReceita.valor_a_arrecadar;
        arrecadado_acumulado = recordReceita.arrecadado_acumulado;
        previsao_adicional = recordReceita.previsao_adicional;
        reduzido = recordReceita.reduzido;
        fonte = recordReceita.fonte;
        codigo_conplano = recordReceita.codigo_conplano;
        return next;
end loop;
end; $$
SQL
        );
    }

    private function plBalanceteReceitaEstado()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.balancete_receita_exercicio_plano_estadual;

create or replace function contabilidade.balancete_receita_exercicio_plano_estadual (
    f_ano integer,
    f_dataInicio date,
    f_dataFim date
)
returns table (
    ano integer,
    natureza varchar(13),
    descricao varchar,
    cp varchar(3),
    instituicao integer,
    orgao integer,
    unidade integer,
    esfera integer,
    recurso_receita integer,
    valor_inicial numeric(17,2),
    previsao_adicional_acumulado numeric(17,2),
    previsao_atualizada numeric(17,2),
    arrecadado_anterior numeric(17,2),
    arrecadado_periodo numeric(17,2),
    valor_a_arrecadar numeric(17,2),
    arrecadado_acumulado numeric(17,2),
    previsao_adicional numeric(17,2),
    reduzido integer,
    fonte integer,
    codigo_conplano integer
)
language plpgsql
as $$
declare

recordReceita record;

begin

for recordReceita in(
    select * from balancete_receita_exercicio_emetario_padrao(f_ano, false, f_dataInicio, f_dataFim)
) loop
        ano = recordReceita.ano;
        natureza = recordReceita.natureza;
        descricao = recordReceita.descricao;
        cp = recordReceita.cp;
        instituicao = recordReceita.instituicao;
        orgao = recordReceita.orgao;
        unidade = recordReceita.unidade;
        esfera = recordReceita.esfera;
        recurso_receita = recordReceita.recurso_receita;
        valor_inicial = recordReceita.valor_inicial;
        previsao_adicional_acumulado = recordReceita.previsao_adicional_acumulado;
        previsao_atualizada = recordReceita.previsao_atualizada;
        arrecadado_anterior = recordReceita.arrecadado_anterior;
        arrecadado_periodo = recordReceita.arrecadado_periodo;
        valor_a_arrecadar = recordReceita.valor_a_arrecadar;
        arrecadado_acumulado = recordReceita.arrecadado_acumulado;
        previsao_adicional = recordReceita.previsao_adicional;
        reduzido = recordReceita.reduzido;
        fonte = recordReceita.fonte;
        codigo_conplano = recordReceita.codigo_conplano;
        return next;
end loop;
end; $$
SQL
        );
    }

    private function plBalanceteReceitaEmentarioPadrao()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.balancete_receita_exercicio_emetario_padrao;

create or replace function contabilidade.balancete_receita_exercicio_emetario_padrao(
    f_ano integer,
    f_uniao boolean,
    f_dataInicio date,
    f_dataFim date
)
returns table (
    ano integer,
    natureza varchar(13),
    descricao varchar,
    cp varchar(3),
    instituicao integer,
    orgao integer,
    unidade integer,
    esfera integer,
    recurso_receita integer,
    valor_inicial numeric(17,2),
    previsao_adicional_acumulado numeric(17,2),
    previsao_atualizada numeric(17,2),
    arrecadado_anterior numeric(17,2),
    arrecadado_periodo numeric(17,2),
    valor_a_arrecadar numeric(17,2),
    arrecadado_acumulado numeric(17,2),
    previsao_adicional numeric(17,2),
    reduzido integer,
    fonte integer,
    codigo_conplano integer
)
language plpgsql
as $$
declare

recordReceita record;

begin

for recordReceita in(
    with plano_uniao as (
        select
               planoreceita.conta as natureza,
               planoreceita.nome as descricao,
               b.ano,
               b.cp,
               b.instituicao,
               b.orgao,
               b.unidade,
               b.esfera,
               b.recurso_receita,
               b.valor_inicial,
               b.previsao_adicional_acumulado,
               b.previsao_atualizada,
               b.arrecadado_anterior,
               b.arrecadado_periodo,
               b.valor_a_arrecadar,
               b.arrecadado_acumulado,
               b.previsao_adicional,
               b.reduzido,
               b.fonte,
               b.codigo_conplano
          from contabilidade.balancete_receita_exercicio(f_ano, f_dataInicio, f_dataFim) as b
          join contabilidade.planoreceitaconplanoorcamento pro on pro.conplanoorcamento_codigo = b.codigo_conplano
          join contabilidade.planoreceita on planoreceita.id = pro.planoreceita_id
        where planoreceita.uniao = f_uniao
          and planoreceita.exercicio = f_ano
    ) select * from plano_uniao
) loop
        ano = recordReceita.ano;
        natureza = recordReceita.natureza;
        descricao = recordReceita.descricao;
        cp = recordReceita.cp;
        instituicao = recordReceita.instituicao;
        orgao = recordReceita.orgao;
        unidade = recordReceita.unidade;
        esfera = recordReceita.esfera;
        recurso_receita = recordReceita.recurso_receita;
        valor_inicial = recordReceita.valor_inicial;
        previsao_adicional_acumulado = recordReceita.previsao_adicional_acumulado;
        previsao_atualizada = recordReceita.previsao_atualizada;
        arrecadado_anterior = recordReceita.arrecadado_anterior;
        arrecadado_periodo = recordReceita.arrecadado_periodo;
        valor_a_arrecadar = recordReceita.valor_a_arrecadar;
        arrecadado_acumulado = recordReceita.arrecadado_acumulado;
        previsao_adicional = recordReceita.previsao_adicional;
        reduzido = recordReceita.reduzido;
        fonte = recordReceita.fonte;
        codigo_conplano = recordReceita.codigo_conplano;
        return next;
end loop;
end; $$
SQL
        );
    }
}
