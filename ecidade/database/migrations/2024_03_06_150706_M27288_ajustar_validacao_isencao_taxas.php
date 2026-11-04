<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M27288AjustarValidacaoIsencaoTaxas extends Migration
{
    public function up()
    {       
    $sql = <<<SQL
drop function if exists fc_iptu_verificaisencoes(integer, integer, boolean, boolean);

drop type if exists tp_iptu_verificaisencoes;

create or replace function fc_iptu_verificaisencoes(
  iMatricula integer,
  iAnousu integer,
  bMostrademo boolean,
  lRaise boolean,
  OUT riCodisen   integer,
  OUT riTipois    integer,
  OUT rnIsenaliq  numeric,
  OUT rnArealo    numeric,
  OUT rbIsentaxas boolean
) returns record as
$$
begin

    perform fc_debug(' <iptu_verificaisencoes> VERIFICANDO ISENCOES...', lRaise);

    riCodisen   := 0;
    riTipois    := 0;
    rnIsenaliq  := 0;
    rbIsentaxas := 'f';
    rnArealo    := 0;

    perform *
       from db_plugin
      where db145_nome = 'calculo-de-iptu-proporcional'
        and db145_situacao is true;

    if found then

      select round(sum((aliquota * total_dias)  / ((iAnousu||'-12-31')::date - (iAnousu||'-01-01')::date + 1)), 2) as aliquota,
             max(area_lote) as area,
             (count(case when isenta_taxas is true then 1 else null end) > 1)::boolean as isenta_taxas
        into rnIsenaliq,
             rnArealo,
             rbIsentaxas
        from fc_iptu_verifica_isencao_competencia(iMatricula, iAnousu, lRaise);

    else

      select j46_codigo,
             coalesce(j45_tipis,0),
             j46_perc::numeric,
             j45_taxas as j45_taxas,
             case
               when j46_arealo is null then 0::numeric
               else j46_arealo::numeric
             end as j46_arealo
        into riCodisen,
             riTipois,
             rnIsenaliq,
             rbIsentaxas,
             rnArealo
        from iptuisen
             inner join isenexe  on j46_codigo = j47_codigo
             inner join tipoisen on j46_tipo   = j45_tipo
      where j46_matric = iMatricula
        and j47_anousu = iAnousu;

      if rnIsenaliq is null then
        rnIsenaliq = 0;
      end if;

      if rbIsentaxas is null then
        rbIsentaxas = 'false'::boolean;
      end if;

    end if;

    perform fc_debug(' <iptu_verificaisencoes> riCodisen:   ' || coalesce( riCodisen, 0 ),   lRaise);
    perform fc_debug(' <iptu_verificaisencoes> rbIsentaxas: ' || rbIsentaxas,                lRaise);
    perform fc_debug(' <iptu_verificaisencoes> rnIsenaliq:  ' || coalesce( rnIsenaliq, 0 ),  lRaise);

    return;
end;
$$  language 'plpgsql';

SQL;
        $this->executeQuery($sql);
    }

    public function down()
    {
        $sql = <<<SQL
drop function if exists fc_iptu_verificaisencoes(integer, integer, boolean, boolean);

drop type if exists tp_iptu_verificaisencoes;

create or replace function fc_iptu_verificaisencoes(
  iMatricula integer,
  iAnousu integer,
  bMostrademo boolean,
  lRaise boolean,
  OUT riCodisen   integer,
  OUT riTipois    integer,
  OUT rnIsenaliq  numeric,
  OUT rnArealo    numeric,
  OUT rbIsentaxas boolean
) returns record as
$$
begin

    perform fc_debug(' <iptu_verificaisencoes> VERIFICANDO ISENCOES...', lRaise);

    riCodisen   := 0;
    riTipois    := 0;
    rnIsenaliq  := 0;
    rbIsentaxas := 'f';
    rnArealo    := 0;

    perform *
       from db_plugin
      where db145_nome = 'calculo-de-iptu-proporcional'
        and db145_situacao is true;

    if found then

      select round(sum((aliquota * total_dias)  / ((iAnousu||'-12-31')::date - (iAnousu||'-01-01')::date + 1)), 2) as aliquota,
             max(area_lote) as area,
             (count(case when isenta_taxas is true then 1 else null end) > 1)::boolean as isenta_taxas
        into rnIsenaliq,
             rnArealo,
             rbIsentaxas
        from fc_iptu_verifica_isencao_competencia(iMatricula, iAnousu, lRaise);

    else

      select j46_codigo,
             coalesce(j45_tipis,0),
             j46_perc::numeric,
             j45_taxas as j45_taxas,
             case
               when j46_arealo is null then 0::numeric
               else j46_arealo::numeric
             end as j46_arealo
        into riCodisen,
             riTipois,
             rnIsenaliq,
             rbIsentaxas,
             rnArealo
        from iptuisen
             inner join isenexe  on j46_codigo = j47_codigo
             inner join tipoisen on j46_tipo   = j45_tipo
      where j46_matric = iMatricula
        and j47_anousu = iAnousu;

      if rnIsenaliq is null then
        rnIsenaliq = 0;
      end if;

      if rbIsentaxas is null then
        rbIsentaxas = 'true'::boolean;
      else
        rbIsentaxas = 'false'::boolean;
      end if;

    end if;

    perform fc_debug(' <iptu_verificaisencoes> riCodisen:   ' || coalesce( riCodisen, 0 ),   lRaise);
    perform fc_debug(' <iptu_verificaisencoes> rbIsentaxas: ' || rbIsentaxas,                lRaise);
    perform fc_debug(' <iptu_verificaisencoes> rnIsenaliq:  ' || coalesce( rnIsenaliq, 0 ),  lRaise);

    return;
end;
$$  language 'plpgsql';

SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
