<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M27174FcIssqnAtualizaIssbaseintegracaoexternaAdicionaCgm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
	$this->upEstrutura();
    }

    private function upDicionario() {

	$sqlUpDic = <<<SQL0

insert into db_syscampo values(1015616,'q60_integracaoexterna','bool','Integração externa do sistema de nota fiscal eletrônica. Quando ativo, registra a o código da inscrição econômica que teve alteração para ser enviado para o sistema de nota eletrônica externo. ','false', 'Integração Externa',1,'f','f','f',5,'text','Integração Externa') on conflict do nothing;

insert into db_syscampodef values(1015616,'Não','Utiliza o sistema do e-cidade de notas eletrônicas') on conflict do nothing;
insert into db_syscampodef values(1015616,'Sim','Utiliza sistema de notas eletrônicas externo') on conflict do nothing;

delete from db_sysarqcamp where codarq = 664 and codcam = 1015616;
insert into db_sysarqcamp values(664,1015616,40,0) on conflict do nothing;

SQL0;

	DB::connection()->getPdo()->exec($sqlUpDic);
    }

    private function upEstrutura() {

	    $sqlUpEst = <<<SQLE0
		ALTER TABLE parissqn ADD COLUMN IF NOT EXISTS q60_integracaoexterna BOOL NOT NULL DEFAULT FALSE;
SQLE0;

	DB::connection()->getPdo()->exec($sqlUpEst);

	DB::statement(<<<SQL1

--set check_function_bodies to off;
create or replace function fc_issqn_atualiza_issbaseintegracaoexterna() returns trigger as $$
declare

  sTabela          varchar default TG_TABLE_NAME;
  sOperacao        varchar default lower(TG_OP);
  sCampo           varchar;
  sSql             varchar;
  iCodigoInscricao integer;
  lIntegracaoExterna boolean default false;

begin


        select coalesce(q60_integracaoexterna, 'false')::boolean
          into lIntegracaoExterna
          from parissqn;

	if (lIntegracaoExterna is false and sOperacao in ('update', 'insert')) then
	     return new;
	elsif (lIntegracaoExterna is false and sOperacao not in ('update', 'insert')) then
	     return old;
        end if;

	/**
	 * Retorna o nome do campo alterado, incluido ou excluido, que tem relacao com a tabela issbase
	 */
	if    sTabela = 'isscalc'        then

	  sCampo := 'q01_inscr';

	elsif sTabela = 'issbase'        then

	  sCampo := 'q02_inscr';

	elsif sTabela = 'issruas'        then

	  sCampo := 'q02_inscr';

  elsif sTabela = 'tabativ'        then

    sCampo := 'q07_inscr';

  elsif sTabela = 'escrito'        then

    sCampo := 'q10_inscr';

  elsif sTabela = 'tabativtipcalc' then

    sCampo := 'q11_inscr';

  elsif sTabela = 'tabativbaixa'   then

    sCampo := 'q11_inscr';

  elsif sTabela = 'issbairro'      then

    sCampo := 'q13_inscr';

	elsif sTabela = 'issprocesso'    then

	  sCampo := 'q14_inscr';

  elsif sTabela = 'issquant'       then

    sCampo := 'q30_inscr';

  elsif sTabela = 'isszona'        then

    sCampo := 'q35_inscr';

  elsif sTabela = 'isscadsimples'  then

    sCampo := 'q38_inscr';

  elsif sTabela = 'issbaseporte'   then

    sCampo := 'q45_inscr';

  elsif sTabela = 'ativprinc'      then

    sCampo := 'q88_inscr';

  elsif sTabela = 'issalvara'      then

    sCampo := 'q123_inscr';

  elsif sTabela = 'aidof'          then

    sCampo := 'y08_inscr';

  elsif sTabela = 'varfix'         then

    sCampo := 'q33_inscr';

  elsif sTabela = 'isscadsimplesbaixa'  then

    -- Como nao possui chave estrangeira da inscricao sera atualizada outra tabela para executar a funcao
    if sOperacao in ('update', 'insert') then

      update isscadsimples set q38_sequencial = new.q39_isscadsimples where q38_sequencial = new.q39_isscadsimples;

      return new;

    else

      update isscadsimples set q38_sequencial = old.q39_isscadsimples where q38_sequencial = old.q39_isscadsimples;

      return old;

    end if;

  elsif sTabela = 'cgm'  then

    -- Como nao possui chave estrangeira da inscricao sera atualizada outra tabela para executar a funcao
    if sOperacao in ('update', 'insert') then

      update issbase set q02_numcgm = new.z01_numcgm where q02_numcgm = new.z01_numcgm and q02_dtbaix is null;

      return new;

    else

      update issbase set q02_numcgm = old.z01_numcgm where q02_numcgm = old.z01_numcgm and q02_dtbaix is null;

      return old;

    end if;

  else

    select trim(db_syscampo.nomecam)
	  into sCampo
      from db_sysarqcamp
	   inner join db_syscampo    on db_syscampo.codcam   = db_sysarqcamp.codcam
	   inner join db_sysarquivo  on db_sysarquivo.codarq = db_sysarqcamp.codarq
	   inner join db_sysforkey   on db_sysforkey.codarq  = db_sysarquivo.codarq
	                            and db_sysforkey.codcam  = db_syscampo.codcam
	   where db_sysarquivo.nomearq = sTabela
	     and db_sysforkey.referen  = 41; --41 = issbase

  end if;

  -- Trecho de codigo para executar no PG 9.2
  if current_setting('server_version_num')::integer >= 90200 then

    sSql := 'insert into issbaseintegracaoexterna 
             select '|| sCampo ||' 
               from (select $1.'||sCampo||') as w_inscricoes_issbaseintegracaoexterna_tg 
              where not exists (select 1  
                                  from issbaseintegracaoexterna 
                                 where q135_inscr = '|| sCampo ||')';

    if sOperacao in ('update', 'insert') then
      execute sSql using new;
    else
      execute sSql using old;
    end if;

  -- Trecho de codigo para executar no PG 8.2
  else

    if sOperacao in ('update', 'insert') then
      create temporary table w_inscricoes_issbaseintegracaoexterna_tg as select new.*;
    else
      create temporary table w_inscricoes_issbaseintegracaoexterna_tg as select old.*;
    end if;

    sSql := 'insert into issbaseintegracaoexterna 
             select '|| sCampo ||' 
               from w_inscricoes_issbaseintegracaoexterna_tg 
              where not exists (select 1  
                                  from issbaseintegracaoexterna 
                                 where q135_inscr = '|| sCampo ||')';
    execute (sSql);
    drop table w_inscricoes_issbaseintegracaoexterna_tg;

  end if;

  if sOperacao in ('update', 'insert') then
    return new;
  end if;

  return old;
end;

$$ language 'plpgsql';

SQL1
    );

	    $sqlUpTrigger = <<<SQLT0

--issbase
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issbase; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issbase for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--issbairro
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issbairro; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issbairro for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--issruas
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issruas; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issruas for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--isszona
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on isszona; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on isszona for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--issquant
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issquant; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issquant for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--issprocesso
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issprocesso; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issprocesso for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--issbaseporte
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issbaseporte; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issbaseporte for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--tabativ
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on tabativ; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on tabativ for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--tabativtipcalc
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on tabativtipcalc; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on tabativtipcalc for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--tabativbaixa
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on tabativbaixa; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on tabativbaixa for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--ativprinc
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on ativprinc; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on ativprinc for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--isscalc
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on isscalc; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on isscalc for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--isscadsimples
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on isscadsimples; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on isscadsimples for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--issalvara
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issalvara; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issalvara for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--escrito
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on escrito; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on escrito for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--aidof
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on aidof; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on aidof for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--varfix (estimativa)
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on varfix; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on varfix for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--isscadsimplesbaixa
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on isscadsimplesbaixa; 
create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on isscadsimplesbaixa for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--cgm
drop trigger if exists tg_atualiza_issbaseintegracaoexterna on cgm; 
create trigger tg_atualiza_issbaseintegracaoexterna after update or delete on cgm for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

SQLT0;

	DB::connection()->getPdo()->exec($sqlUpTrigger);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down() {
           $this->downDicionario(); 
	   $this->downEstrutura(); 
    }


    private function downDicionario() {

	    $sqlDownDic = <<<SQLD0

delete from db_sysarqcamp  where codarq = 664 and codcam = 1015616;
delete from db_syscampodef where codcam = 1015616;
delete from db_syscampo    where codcam = 1015616;

SQLD0;

	DB::connection()->getPdo()->exec($sqlDownDic);
    }

    private function downEstrutura()
    {

	    $sqlDownEst = <<<SQLDE0
                ALTER TABLE parissqn DROP COLUMN IF EXISTS q60_integracaoexterna;
SQLDE0;

	    DB::connection()->getPdo()->exec($sqlDownEst);

	    DB::statement(<<<SQLD1

--set check_function_bodies to off;
create or replace function fc_issqn_atualiza_issbaseintegracaoexterna() returns trigger as 
$$
declare

  sTabela          varchar default TG_TABLE_NAME;
  sOperacao        varchar default lower(TG_OP);
  sCampo           varchar;
  sSql             varchar;
  iCodigoInscricao integer;

begin

	/**
	 * Retorna o nome do campo alterado, incluido ou excluido, que tem relacao com a tabela issbase
	 */
	if    sTabela = 'isscalc'        then

	  sCampo := 'q01_inscr';

	elsif sTabela = 'issbase'        then

	  sCampo := 'q02_inscr';

	elsif sTabela = 'issruas'        then

	  sCampo := 'q02_inscr';

  elsif sTabela = 'tabativ'        then

    sCampo := 'q07_inscr';

  elsif sTabela = 'escrito'        then

    sCampo := 'q10_inscr';

  elsif sTabela = 'tabativtipcalc' then

    sCampo := 'q11_inscr';

  elsif sTabela = 'tabativbaixa'   then

    sCampo := 'q11_inscr';

  elsif sTabela = 'issbairro'      then

    sCampo := 'q13_inscr';

	elsif sTabela = 'issprocesso'    then

	  sCampo := 'q14_inscr';

  elsif sTabela = 'issquant'       then

    sCampo := 'q30_inscr';

  elsif sTabela = 'isszona'        then

    sCampo := 'q35_inscr';

  elsif sTabela = 'isscadsimples'  then

    sCampo := 'q38_inscr';

  elsif sTabela = 'issbaseporte'   then

    sCampo := 'q45_inscr';

  elsif sTabela = 'ativprinc'      then

    sCampo := 'q88_inscr';

  elsif sTabela = 'issalvara'      then

    sCampo := 'q123_inscr';

  elsif sTabela = 'aidof'          then

    sCampo := 'y08_inscr';

  elsif sTabela = 'varfix'         then

    sCampo := 'q33_inscr';

  elsif sTabela = 'isscadsimplesbaixa'  then

    -- Como nao possui chave estrangeira da inscricao sera atualizada outra tabela para executar a funcao
    if sOperacao in ('update', 'insert') then

      update isscadsimples set q38_sequencial = new.q39_isscadsimples where q38_sequencial = new.q39_isscadsimples;

      return new;

    else

      update isscadsimples set q38_sequencial = old.q39_isscadsimples where q38_sequencial = old.q39_isscadsimples;

      return old;

    end if;

  else

    select trim(db_syscampo.nomecam)
	  into sCampo
      from db_sysarqcamp
	   inner join db_syscampo    on db_syscampo.codcam   = db_sysarqcamp.codcam
	   inner join db_sysarquivo  on db_sysarquivo.codarq = db_sysarqcamp.codarq
	   inner join db_sysforkey   on db_sysforkey.codarq  = db_sysarquivo.codarq
	                            and db_sysforkey.codcam  = db_syscampo.codcam
	   where db_sysarquivo.nomearq = sTabela
	     and db_sysforkey.referen  = 41; --41 = issbase

  end if;

  -- Trecho de codigo para executar no PG 9.2
  if current_setting('server_version_num')::integer >= 90200 then

    sSql := 'insert into issbaseintegracaoexterna 
             select '|| sCampo ||' 
               from (select $1.'||sCampo||') as w_inscricoes_issbaseintegracaoexterna_tg 
              where not exists (select 1  
                                  from issbaseintegracaoexterna 
                                 where q135_inscr = '|| sCampo ||')';

    if sOperacao in ('update', 'insert') then
      execute sSql using new;
    else
      execute sSql using old;
    end if;

  -- Trecho de codigo para executar no PG 8.2
  else

    if sOperacao in ('update', 'insert') then
      create temporary table w_inscricoes_issbaseintegracaoexterna_tg as select new.*;
    else
      create temporary table w_inscricoes_issbaseintegracaoexterna_tg as select old.*;
    end if;

    sSql := 'insert into issbaseintegracaoexterna 
             select '|| sCampo ||' 
               from w_inscricoes_issbaseintegracaoexterna_tg 
              where not exists (select 1  
                                  from issbaseintegracaoexterna 
                                 where q135_inscr = '|| sCampo ||')';
    execute (sSql);
    drop table w_inscricoes_issbaseintegracaoexterna_tg;

  end if;

  if sOperacao in ('update', 'insert') then
    return new;
  end if;

  return old;
end;

--issbase
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issbase; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issbase for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--issbairro
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issbairro; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issbairro for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--issruas
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issruas; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issruas for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--isszona
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on isszona; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on isszona for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--issquant
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issquant; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issquant for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--issprocesso
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issprocesso; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issprocesso for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--issbaseporte
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issbaseporte; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issbaseporte for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--tabativ
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on tabativ; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on tabativ for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--tabativtipcalc
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on tabativtipcalc; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on tabativtipcalc for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--tabativbaixa
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on tabativbaixa; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on tabativbaixa for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--ativprinc
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on ativprinc; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on ativprinc for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--isscalc
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on isscalc; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on isscalc for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--isscadsimples
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on isscadsimples; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on isscadsimples for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--issalvara
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on issalvara; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on issalvara for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--escrito
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on escrito; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on escrito for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--aidof
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on aidof; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on aidof for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--varfix (estimativa)
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on varfix; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on varfix for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--isscadsimplesbaixa
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on isscadsimplesbaixa; 
--create trigger tg_atualiza_issbaseintegracaoexterna after insert or update or delete on isscadsimplesbaixa for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

--cgm
--drop trigger if exists tg_atualiza_issbaseintegracaoexterna on cgm; 
--create trigger tg_atualiza_issbaseintegracaoexterna after update or delete on cgm for each row execute procedure fc_issqn_atualiza_issbaseintegracaoexterna();

$$ language 'plpgsql';

SQLD1
    );
    }
}
