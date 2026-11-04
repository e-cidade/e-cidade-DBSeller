<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28902AtualizandoTriggerPensao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upPlPensao();
        $this->upPlPensaoContaBancaria();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downPlPensao();
        $this->downPlPensaoContaBancaria();
    }

    public function upPlPensao()
    {
        $sql = <<<SQL
create or replace function fc_pensao_trigger()
returns trigger
as $$
declare
  sCodigoBanco varchar default null;
  sAgencia     varchar default null;
  sConta       varchar default null;
  rOperacao    record;

  -- Utilizados em contaBancaria
  sNomeServidor             varchar default '';
  iSequencialAgencia        integer;
  iSequencialContaBancaria  integer;
  iSequencialContaServidor  integer;

  TIPO_CONTA_CORRENTE  constant integer := 1;
  TIPO_CONTA_POUPANCA  constant integer := 2;
  TIPO_CONTA_APLICACAO constant integer := 3;
  TIPO_CONTA_SALARIO   constant integer := 4;

  lTrigger   varchar default 'false';
begin

  select fc_getsession('DB_disable_trigger')
    into lTrigger;


  if ( TG_OP != 'DELETE' ) then

    rOperacao := new;
  else

    rOperacao := old;
  end if;

  if lTrigger = 'true' then
    return rOperacao;
  end if;

  --
  -- Quando alterar dados da conta bancaria do pensionista(pensao)
  -- Alterar tambem dados da conta bancaria do sistema (contabancaria e bancoagencia)
  --

  if TG_OP in ( 'INSERT', 'UPDATE' ) then

    perform fc_putsession('DB_disable_trigger', 'true');

    if rOperacao.r52_codbco is null or rOperacao.r52_codage is null or rOperacao.r52_conta   is null
       or rOperacao.r52_codbco = '' or rOperacao.r52_codage = ''    or rOperacao.r52_conta = '' then

      delete
        from pensaocontabancaria
       where rh139_regist   = rOperacao.r52_regist
         and rh139_anousu   = rOperacao.r52_anousu
         and rh139_mesusu   = rOperacao.r52_mesusu
         and rh139_numcgm   = rOperacao.r52_numcgm
         and rh139_cgmalimentado = rOperacao.r52_cgmalimentado;
      perform fc_putsession('DB_disable_trigger', 'false');
      return rOperacao;
    end if;
    --
    -- Verifica se existe Agencia com os dados informados
    --
    select z01_nome
      into sNomeServidor
      from cgm
     where z01_numcgm = rOperacao.r52_numcgm;

    select db89_sequencial
      into iSequencialAgencia
      from bancoagencia
     where db89_db_bancos  = rOperacao.r52_codbco
       and db89_codagencia = rOperacao.r52_codage
       and db89_digito     = rOperacao.r52_dvagencia;
    --
    -- Caso no encontre, cria uma nova com os dados informados.
    --
    if not found then

      select nextval('bancoagencia_db89_sequencial_seq')
        into iSequencialAgencia;

      insert into bancoagencia (db89_sequencial, db89_db_bancos, db89_codagencia, db89_digito)
           values ( iSequencialAgencia, rOperacao.r52_codbco, rOperacao.r52_codage, rOperacao.r52_dvagencia );
    end if;

    select db83_sequencial
      into iSequencialContaBancaria
      from contabancaria
     where db83_bancoagencia = iSequencialAgencia
       and db83_conta        = rOperacao.r52_conta
       and db83_dvconta      = rOperacao.r52_dvconta
     limit 1;

    if not found then

      select nextval('contabancaria_db83_sequencial_seq')
        into iSequencialContaBancaria;

      insert into contabancaria (
        db83_sequencial,
        db83_descricao,
        db83_bancoagencia,
        db83_conta,
        db83_dvconta,
        db83_identificador,
        db83_codigooperacao,
        db83_tipoconta,
        db83_contaplano
      ) values (
        iSequencialContaBancaria,
        sNomeServidor,
        iSequencialAgencia,
        rOperacao.r52_conta,
        rOperacao.r52_dvconta,
        '0',
        '',
        TIPO_CONTA_CORRENTE,
        false -- Conta do Plano de Contas da Prefeitura
    );
    end if;

     select rh139_sequencial
       into iSequencialContaServidor
       from pensaocontabancaria
      where rh139_regist   = rOperacao.r52_regist
        and rh139_anousu   = rOperacao.r52_anousu
        and rh139_mesusu   = rOperacao.r52_mesusu
        and rh139_numcgm   = rOperacao.r52_numcgm
        and rh139_cgmalimentado = rOperacao.r52_cgmalimentado;
    if found then

      update pensaocontabancaria
         set rh139_contabancaria = iSequencialContaBancaria
       where rh139_sequencial    = iSequencialContaServidor;
    else

      insert into pensaocontabancaria (
        rh139_sequencial,
        rh139_regist,
        rh139_numcgm,
        rh139_anousu,
        rh139_mesusu,
        rh139_contabancaria,
        rh139_cgmalimentado
      ) values (
        nextval('pensaocontabancaria_rh139_sequencial_seq'),
        rOperacao.r52_regist,
        rOperacao.r52_numcgm,
        rOperacao.r52_anousu,
        rOperacao.r52_mesusu,
        iSequencialContaBancaria,
        rOperacao.r52_cgmalimentado
      );
    end if;

    perform fc_putsession('DB_disable_trigger', 'false');
  else -- TG_OP = 'DELETE'

    perform fc_putsession('DB_disable_trigger', 'true');
    delete from pensaocontabancaria where rh139_regist    = rOperacao.r52_regist
                                      and rh139_anousu    = rOperacao.r52_anousu
                                      and rh139_mesusu    = rOperacao.r52_mesusu
                                      and rh139_numcgm    = rOperacao.r52_numcgm
                                      and rh139_cgmalimentado = rOperacao.r52_cgmalimentado;
    perform fc_putsession('DB_disable_trigger', 'false');
  end if;

  return rOperacao;
end;
$$ language 'plpgsql';
SQL;
        DB::connection()->getPdo()->exec($sql);

    }

    public function upPlPensaoContaBancaria()
    {
        $sql = <<<SQL
create or replace function fc_pensaocontabancaria_trigger()
returns trigger
as $$
declare
  rDados     record;
  sBanco     varchar;
  sAgencia   varchar;
  sDVAgencia varchar;
  sConta     varchar;
  sDVConta   varchar;
  lTrigger   varchar default 'false';
begin

select fc_getsession('DB_disable_trigger')
    into lTrigger;

  if lTrigger = 'true' then
    return rDados;
  end if;

  if TG_OP in ('INSERT', 'UPDATE') then
    rDados := new;
  else
    rDados := old;
  end if;


  if TG_OP in ('INSERT', 'UPDATE') then

    perform fc_putsession('DB_disable_trigger', 'true');

    select db89_db_bancos,
           db89_codagencia,
           db89_digito,
           db83_conta,
           db83_dvconta
      into sBanco,
           sAgencia,
           sDVAgencia,
           sConta,
           sDVConta
      from contabancaria
     inner join pensaocontabancaria       on rh139_contabancaria = db83_sequencial
     inner join bancoagencia              on db89_sequencial     = db83_bancoagencia
     inner join rhpessoalmov              on rh02_regist         = rDados.rh139_regist
                                         and rh02_anousu         = rDados.rh139_anousu
                                         and rh02_mesusu         = rDados.rh139_mesusu
     inner join pensao                    on r52_anousu          = rDados.rh139_anousu
                                         and r52_mesusu          = rDados.rh139_mesusu
                                         and r52_regist          = rDados.rh139_regist
                                         and r52_numcgm          = rDados.rh139_numcgm
                                         and r52_cgmalimentado   = rDados.rh139_cgmalimentado
     where rh139_sequencial = rDados.rh139_sequencial;


    if found then
      update pensao
         set r52_codbco    = sBanco,
             r52_codage    = sAgencia,
             r52_dvagencia = sDVAgencia,
             r52_conta     = sConta,
             r52_dvconta   = sDVConta
       where r52_regist    = rDados.rh139_regist
         and r52_anousu    = rDados.rh139_anousu
         and r52_mesusu    = rDados.rh139_mesusu
         and r52_numcgm    = rDados.rh139_numcgm
         and r52_cgmalimentado = rDados.rh139_cgmalimentado;
    end if;

    perform fc_putsession('DB_disable_trigger', 'false');
  else --TG_OP = 'DELETE'
    delete from pensao where r52_regist    = rDados.rh139_regist
                         and r52_anousu    = rDados.rh139_anousu
                         and r52_mesusu    = rDados.rh139_mesusu
                         and r52_numcgm    = rDados.rh139_numcgm
                         and r52_cgmalimentado = rDados.rh139_cgmalimentado;
  end if;

  return rDados;
end;
$$ language 'plpgsql';
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    public function downPlPensao()
    {
        $sql = <<<SQL
create or replace function fc_pensao_trigger()
returns trigger
as $$
declare
  sCodigoBanco varchar default null;
  sAgencia     varchar default null;
  sConta       varchar default null;
  rOperacao    record;

  -- Utilizados em contaBancaria
  sNomeServidor             varchar default '';
  iSequencialAgencia        integer;
  iSequencialContaBancaria  integer;
  iSequencialContaServidor  integer;

  TIPO_CONTA_CORRENTE  constant integer := 1;
  TIPO_CONTA_POUPANCA  constant integer := 2;
  TIPO_CONTA_APLICACAO constant integer := 3;
  TIPO_CONTA_SALARIO   constant integer := 4;

  lTrigger   varchar default 'false';
begin

  select fc_getsession('DB_disable_trigger')
    into lTrigger;


  if ( TG_OP != 'DELETE' ) then

    rOperacao := new;
  else

    rOperacao := old;
  end if;

  if lTrigger = 'true' then
    return rOperacao;
  end if;

  --
  -- Quando alterar dados da conta bancaria do pensionista(pensao)
  -- Alterar tambem dados da conta bancaria do sistema (contabancaria e bancoagencia)
  --

  if TG_OP in ( 'INSERT', 'UPDATE' ) then

    perform fc_putsession('DB_disable_trigger', 'true');

    if rOperacao.r52_codbco is null or rOperacao.r52_codage is null or rOperacao.r52_conta   is null
       or rOperacao.r52_codbco = '' or rOperacao.r52_codage = ''    or rOperacao.r52_conta = '' then

      delete
        from pensaocontabancaria
       where rh139_regist   = rOperacao.r52_regist
         and rh139_anousu   = rOperacao.r52_anousu
         and rh139_mesusu   = rOperacao.r52_mesusu
         and rh139_numcgm   = rOperacao.r52_numcgm;
      perform fc_putsession('DB_disable_trigger', 'false');
      return rOperacao;
    end if;
    --
    -- Verifica se existe Agencia com os dados informados
    --
    select z01_nome
      into sNomeServidor
      from cgm
     where z01_numcgm = rOperacao.r52_numcgm;

    select db89_sequencial
      into iSequencialAgencia
      from bancoagencia
     where db89_db_bancos  = rOperacao.r52_codbco
       and db89_codagencia = rOperacao.r52_codage
       and db89_digito     = rOperacao.r52_dvagencia;
    --
    -- Caso no encontre, cria uma nova com os dados informados.
    --
    if not found then

      select nextval('bancoagencia_db89_sequencial_seq')
        into iSequencialAgencia;

      insert into bancoagencia (db89_sequencial, db89_db_bancos, db89_codagencia, db89_digito)
           values ( iSequencialAgencia, rOperacao.r52_codbco, rOperacao.r52_codage, rOperacao.r52_dvagencia );
    end if;

    select db83_sequencial
      into iSequencialContaBancaria
      from contabancaria
     where db83_bancoagencia = iSequencialAgencia
       and db83_conta        = rOperacao.r52_conta
       and db83_dvconta      = rOperacao.r52_dvconta
     limit 1;

    if not found then

      select nextval('contabancaria_db83_sequencial_seq')
        into iSequencialContaBancaria;

      insert into contabancaria (
        db83_sequencial,
        db83_descricao,
        db83_bancoagencia,
        db83_conta,
        db83_dvconta,
        db83_identificador,
        db83_codigooperacao,
        db83_tipoconta,
        db83_contaplano
      ) values (
        iSequencialContaBancaria,
        sNomeServidor,
        iSequencialAgencia,
        rOperacao.r52_conta,
        rOperacao.r52_dvconta,
        '0',
        '',
        TIPO_CONTA_CORRENTE,
        false -- Conta do Plano de Contas da Prefeitura
    );
    end if;

     select rh139_sequencial
       into iSequencialContaServidor
       from pensaocontabancaria
      where rh139_regist   = rOperacao.r52_regist
        and rh139_anousu   = rOperacao.r52_anousu
        and rh139_mesusu   = rOperacao.r52_mesusu
        and rh139_numcgm   = rOperacao.r52_numcgm;
    if found then

      update pensaocontabancaria
         set rh139_contabancaria = iSequencialContaBancaria
       where rh139_sequencial    = iSequencialContaServidor;
    else

      insert into pensaocontabancaria (
        rh139_sequencial,
        rh139_regist,
        rh139_numcgm,
        rh139_anousu,
        rh139_mesusu,
        rh139_contabancaria
      ) values (
        nextval('pensaocontabancaria_rh139_sequencial_seq'),
        rOperacao.r52_regist,
        rOperacao.r52_numcgm,
        rOperacao.r52_anousu,
        rOperacao.r52_mesusu,
        iSequencialContaBancaria
      );
    end if;

    perform fc_putsession('DB_disable_trigger', 'false');
  else -- TG_OP = 'DELETE'

    perform fc_putsession('DB_disable_trigger', 'true');
    delete from pensaocontabancaria where rh139_regist    = rOperacao.r52_regist
                                      and rh139_anousu    = rOperacao.r52_anousu
                                      and rh139_mesusu    = rOperacao.r52_mesusu
                                      and rh139_numcgm    = rOperacao.r52_numcgm;
    perform fc_putsession('DB_disable_trigger', 'false');
  end if;

  return rOperacao;
end;
$$ language 'plpgsql';
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    public function downPlPensaoContaBancaria()
    {
        $sql = <<<SQL
create or replace function fc_pensaocontabancaria_trigger()
returns trigger
as $$
declare
  rDados     record;
  sBanco     varchar;
  sAgencia   varchar;
  sDVAgencia varchar;
  sConta     varchar;
  sDVConta   varchar;
  lTrigger   varchar default 'false';
begin

select fc_getsession('DB_disable_trigger')
    into lTrigger;

  if lTrigger = 'true' then
    return rDados;
  end if;

  if TG_OP in ('INSERT', 'UPDATE') then
    rDados := new;
  else
    rDados := old;
  end if;


  if TG_OP in ('INSERT', 'UPDATE') then

    perform fc_putsession('DB_disable_trigger', 'true');

    select db89_db_bancos,
           db89_codagencia,
           db89_digito,
           db83_conta,
           db83_dvconta
      into sBanco,
           sAgencia,
           sDVAgencia,
           sConta,
           sDVConta
      from contabancaria
     inner join pensaocontabancaria       on rh139_contabancaria = db83_sequencial
     inner join bancoagencia              on db89_sequencial     = db83_bancoagencia
     inner join rhpessoalmov              on rh02_regist         = rDados.rh139_regist
                                         and rh02_anousu         = rDados.rh139_anousu
                                         and rh02_mesusu         = rDados.rh139_mesusu
     inner join pensao                    on r52_anousu          = rDados.rh139_anousu
                                         and r52_mesusu          = rDados.rh139_mesusu
                                         and r52_regist          = rDados.rh139_regist
                                         and r52_numcgm          = rDados.rh139_numcgm
     where rh139_sequencial = rDados.rh139_sequencial;


    if found then
      update pensao
         set r52_codbco    = sBanco,
             r52_codage    = sAgencia,
             r52_dvagencia = sDVAgencia,
             r52_conta     = sConta,
             r52_dvconta   = sDVConta
       where r52_regist    = rDados.rh139_regist
         and r52_anousu    = rDados.rh139_anousu
         and r52_mesusu    = rDados.rh139_mesusu
         and r52_numcgm    = rDados.rh139_numcgm;
    end if;

    perform fc_putsession('DB_disable_trigger', 'false');
  else --TG_OP = 'DELETE'
    delete from pensao where r52_regist    = rDados.rh139_regist
                         and r52_anousu    = rDados.rh139_anousu
                         and r52_mesusu    = rDados.rh139_mesusu
                         and r52_numcgm    = rDados.rh139_numcgm;
  end if;

  return rDados;
end;
$$ language 'plpgsql';
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

}
