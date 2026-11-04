<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M23483CorrecaoTce extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

--drop function fc_corre(integer, date, float8, date, int4, date);
create or replace function fc_corre(integer, date, float8, date, int4, date) returns float8 as $$
declare

  RECE_CORR alias for $1;
  DATA_VENC alias for $2;
  VALOR_B   alias for $3;
  DATA_HOJE alias for $4;
  SUBDIR    alias for $5;
  DTVENC    alias for $6;

  INFLATOR_CORRECAO varchar(5);
  VALOR_RETORNO     float8;
  V_TABREC          record;
  DT_VENC           date;
  INSTIT            integer;
  RECEITA_TCE       boolean default false;
  lRaise            boolean default false;

begin

  lRaise := (case when fc_getsession('DB_debugon') is null then false else true end);
  INSTIT := (case when fc_getsession('DB_instit') is not null then fc_getsession('DB_instit')::int else null end);

  if INSTIT is null then
     raise exception 'Instituição não informada.';
  end if;

  if lRaise is true then
    if trim(fc_getsession('db_debug')) <> '' then
      perform fc_debug('  <corre>  - INICIANDO PROCESSAMENTO... ',lRaise, false, false);
    else
      perform fc_debug('  <corre>  - INICIANDO PROCESSAMENTO... ',lRaise, true, false);
    end if;
  end if;

  VALOR_RETORNO := 0;

  if lRaise is true then
    perform fc_debug('  <corre>  - ',                                    lRaise, false, false);
    perform fc_debug('  <corre>  - Parametros  ',                        lRaise, false, false);
    perform fc_debug('  <corre>  - Receita ...............:'||RECE_CORR, lRaise, false, false);
    perform fc_debug('  <corre>  - Data de Vencimento 1...:'||DATA_VENC, lRaise, false, false);
    perform fc_debug('  <corre>  - Valor .................:'||VALOR_B,   lRaise, false, false);
    perform fc_debug('  <corre>  - Data Corrente .........:'||DATA_HOJE, lRaise, false, false);
    perform fc_debug('  <corre>  - Ano ...................:'||SUBDIR,    lRaise, false, false);
    perform fc_debug('  <corre>  - Data de Vencimento 2 ..:'||DTVENC,    lRaise, false, false);
  end if;

  if DATA_VENC::date > DATA_HOJE::date then

    if lRaise is true then
      perform fc_debug('  <corre>  - Data de Vencimento1 ('||DATA_VENC||') é maior que a data corrente ('||DATA_HOJE||')', lRaise, false, false);
      perform fc_debug('  <corre>  - Retornando valor de correcao o proprio valor da receita: '||round(VALOR_B,2),         lRaise, false, false);
    end if;

    return round(VALOR_B, 2);

  end if;

  if lRaise is true then
    perform fc_debug('  <corre>  - Verificando o cadastro da receita '||RECE_CORR, lRaise, false, false);
  end if;

  select *
    into V_TABREC
    from tabrec
         inner join tabrecjm on tabrec.k02_codjm = tabrecjm.k02_codjm
   where k02_codigo = RECE_CORR;
  if not found then

    if lRaise is true then
      perform fc_debug('  <corre>  - Nao encontrou cadastro para a receita '||RECE_CORR, lRaise, false, false);
      perform fc_debug('  <corre>  - Retornando valor de correcao -1',                   lRaise, false, false);
    end if;

    return -1;

  end if;

  if lRaise is true then
    perform fc_debug('  <corre>  - Verificando forma de correcao de acordo com o cadastro da receita (campo k02_corven)...', lRaise, false, false);
    perform fc_debug('  <corre>  - K02_corven = '||V_TABREC.K02_CORVEN,                                                      lRaise, false, false);
  end if;

  if V_TABREC.K02_CORVEN then
    DT_VENC := DTVENC;
  else
    DT_VENC := DATA_VENC;
  end if;

  DT_VENC := fc_proximo_dia_util(DT_VENC::date);

  if lRaise is true then
    perform fc_debug('  <corre>  - Data para vencimento que sera utilizada ..: '||DT_VENC,           lRaise, false, false);
    perform fc_debug('  <corre>  - Inflator para correcao ...................: '||V_TABREC.K02_CORR, lRaise, false, false);
  end if;

  INFLATOR_CORRECAO := trim(V_TABREC.K02_CORR);

  if INFLATOR_CORRECAO != 'REAL' then

    if not VALOR_B isnull and VALOR_B <> 0  then

      if lRaise is true then
        perform fc_debug('  <corre>  - Chamando a funcao fc_infla para calcular o valor da correcao...', lRaise, false, false);
        perform fc_debug('  <corre>  - ',                                                                lRaise, false, false);
      end if;

      /* Verifica se a procedência do débito é do TCE e o valor corrigido for menor que o histórico, considera o histórico */
      perform 1
      from proced
      where v03_receit = RECE_CORR
        and v03_instit = INSTIT
        and v03_tributaria = 3;

      if found then
         RECEITA_TCE = true;
      else
         perform 1
         from procdiver join proced on v03_codigo = dv09_proced
         where dv09_receit = RECE_CORR
           and dv09_instit = INSTIT
           and v03_tributaria = 3;

         if found then
            RECEITA_TCE = true;
         end if;
      end if;

      VALOR_RETORNO := fc_INFLA(INFLATOR_CORRECAO, round(VALOR_B, 2), DT_VENC, DATA_HOJE);

      if RECEITA_TCE is true and round(VALOR_RETORNO, 2) < round(VALOR_B, 2) then
         if lRaise is true then
           perform fc_debug('  <corre>  - ',                                                   lRaise, false, false);
           perform fc_debug('  <corre>  - Valor apos correcao: '||VALOR_RETORNO,               lRaise, false, false);
           perform fc_debug('  <corre>  - Valor historico: '||VALOR_B,                         lRaise, false, false);
           perform fc_debug('  <corre>  - Conforme regra do TCE, permanece o valor historico', lRaise, false, false);
           perform fc_debug('  <corre>  - ',                                                   lRaise, false, false);
         end if;

         VALOR_RETORNO := round(VALOR_B, 2);
      end if;

      if lRaise is true then
        perform fc_debug('  <corre>  - ',                                                                       lRaise, false, false);
        perform fc_debug('  <corre>  - Valor retornado da Funcao: '||VALOR_RETORNO,                             lRaise, false, false);
        perform fc_debug('  <corre>  - Fim da chamada da funcao fc_infla para calcular o valor da correcao...', lRaise, false, false);
        perform fc_debug('  <corre>  - ',                                                                       lRaise, false, false);
      end if;

    end if;

  else

    if lRaise is true then
      perform fc_debug('  <corre>  - Inflator REAL...',     lRaise, false, false);
      perform fc_debug('  <corre>  - Não calcula correcao', lRaise, false, false);
      perform fc_debug('  <corre>  - ',                     lRaise, false, false);
    end if;
    VALOR_RETORNO := VALOR_B;

  end if;

  if VALOR_RETORNO = 0 then
    VALOR_RETORNO := VALOR_B;
  end if;

  if lRaise is true then
    perform fc_debug('  <corre>  - Valor retornado de correcao ..:  '||VALOR_RETORNO||' = '||round(VALOR_RETORNO, 2), lRaise, false, false);
  end if;

  return round(VALOR_RETORNO, 2);
end;
$$ language 'plpgsql';

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

--drop function fc_corre(integer, date, float8, date, int4, date);
create or replace function fc_corre(integer, date, float8, date, int4, date) returns float8 as $$
declare
  RECE_CORR alias for $1;
  DATA_VENC alias for $2;
  VALOR_B   alias for $3;
  DATA_HOJE alias for $4;
  SUBDIR    alias for $5;
  DTVENC    alias for $6;

  INFLATOR_CORRECAO varchar(5);
  VALOR_RETORNO     float8;
  V_TABREC          record;
  DT_VENC           date;

  lRaise boolean default false;
begin

 	lRaise := (case when fc_getsession('DB_debugon') is null then false else true end);

  if lRaise is true then
    if trim(fc_getsession('db_debug')) <> '' then
      perform fc_debug('  <corre>  - INICIANDO PROCESSAMENTO... ',lRaise, false, false);
    else
      perform fc_debug('  <corre>  - INICIANDO PROCESSAMENTO... ',lRaise, true, false);
    end if;
  end if;

  VALOR_RETORNO := 0;

  if lRaise is true then
    perform fc_debug('  <corre>  - ',                                    lRaise, false, false);
    perform fc_debug('  <corre>  - Parametros  ',                        lRaise, false, false);
    perform fc_debug('  <corre>  - Receita ...............:'||RECE_CORR, lRaise, false, false);
    perform fc_debug('  <corre>  - Data de Vencimento 1...:'||DATA_VENC, lRaise, false, false);
    perform fc_debug('  <corre>  - Valor .................:'||VALOR_B,   lRaise, false, false);
    perform fc_debug('  <corre>  - Data Corrente .........:'||DATA_HOJE, lRaise, false, false);
    perform fc_debug('  <corre>  - Ano ...................:'||SUBDIR,    lRaise, false, false);
    perform fc_debug('  <corre>  - Data de Vencimento 2 ..:'||DTVENC,    lRaise, false, false);
  end if;

  if DATA_VENC::date > DATA_HOJE::date then

    if lRaise is true then
      perform fc_debug('  <corre>  - Data de Vencimento1 ('||DATA_VENC||') é maior que a data corrente ('||DATA_HOJE||')', lRaise, false, false);
      perform fc_debug('  <corre>  - Retornando valor de correcao o proprio valor da receita: '||round(VALOR_B,2),         lRaise, false, false);
    end if;

    return round(VALOR_B, 2);

  end if;

  if lRaise is true then
    perform fc_debug('  <corre>  - Verificando o cadastro da receita '||RECE_CORR, lRaise, false, false);
  end if;

  select *
    into V_TABREC
    from tabrec
         inner join tabrecjm on tabrec.k02_codjm = tabrecjm.k02_codjm
   where k02_codigo = RECE_CORR;
  if not found then

    if lRaise is true then
      perform fc_debug('  <corre>  - Nao encontrou cadastro para a receita '||RECE_CORR, lRaise, false, false);
      perform fc_debug('  <corre>  - Retornando valor de correcao -1',                   lRaise, false, false);
    end if;

    return -1;

  end if;

  if lRaise is true then
    perform fc_debug('  <corre>  - Verificando forma de correcao de acordo com o cadastro da receita (campo k02_corven)...', lRaise, false, false);
    perform fc_debug('  <corre>  - K02_corven = '||V_TABREC.K02_CORVEN,                                                      lRaise, false, false);
  end if;

  if V_TABREC.K02_CORVEN then
    DT_VENC := DTVENC;
  else
    DT_VENC := DATA_VENC;
  end if;

  DT_VENC := fc_proximo_dia_util(DT_VENC::date);

  if lRaise is true then
    perform fc_debug('  <corre>  - Data para vencimento que sera utilizada ..: '||DT_VENC,           lRaise, false, false);
    perform fc_debug('  <corre>  - Inflator para correcao ...................: '||V_TABREC.K02_CORR, lRaise, false, false);
  end if;

  INFLATOR_CORRECAO := trim(V_TABREC.K02_CORR);

  if INFLATOR_CORRECAO != 'REAL' then

    if not VALOR_B isnull and VALOR_B <> 0  then

      if lRaise is true then
        perform fc_debug('  <corre>  - Chamando a funcao fc_infla para calcular o valor da correcao...', lRaise, false, false);
        perform fc_debug('  <corre>  - ',                                                                lRaise, false, false);
      end if;

      VALOR_RETORNO := fc_INFLA(INFLATOR_CORRECAO, round(VALOR_B, 2), DT_VENC, DATA_HOJE);

      if lRaise is true then
        perform fc_debug('  <corre>  - ',                                                                       lRaise, false, false);
        perform fc_debug('  <corre>  - Valor retornado da Funcao: '||VALOR_RETORNO,                             lRaise, false, false);
        perform fc_debug('  <corre>  - Fim da chamada da funcao fc_infla para calcular o valor da correcao...', lRaise, false, false);
        perform fc_debug('  <corre>  - ',                                                                       lRaise, false, false);
      end if;

    end if;

  else

    if lRaise is true then
      perform fc_debug('  <corre>  - Inflator REAL...',     lRaise, false, false);
      perform fc_debug('  <corre>  - Não calcula correcao', lRaise, false, false);
      perform fc_debug('  <corre>  - ',                     lRaise, false, false);
    end if;
    VALOR_RETORNO := VALOR_B;

  end if;

  if VALOR_RETORNO = 0 then
    VALOR_RETORNO := VALOR_B;
  end if;

  if lRaise is true then
    perform fc_debug('  <corre>  - Valor retornado de correcao ..:  '||VALOR_RETORNO||' = '||round(VALOR_RETORNO, 2), lRaise, false, false);
  end if;

  return round(VALOR_RETORNO, 2);
end;
$$ language 'plpgsql';

SQL
        );
    }
}
