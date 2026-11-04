<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M22797AdicionarCamposTabelaInformacaoDebito extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    DB::connection()->getPdo()->exec(
      <<<SQL
        ALTER TABLE informacaodebito ADD k163_observacao varchar(500);
        ALTER TABLE informacaodebito ADD k163_manual boolean;
SQL
    );

    DB::connection()->getPdo()->exec(
      <<<SQL2
drop trigger  if exists tg_arrecad_informacaodebito on issqn.issvar;
drop trigger  if exists tg_arrecad_informacaodebito on caixa.arrecad;
drop trigger  if exists tg_arrecad_informacaodebito on issqn.issvarlev;
drop function if exists fc_arrecad_informacaodebito();

CREATE OR REPLACE FUNCTION public.fc_arrecad_informacaodebito()
RETURNS trigger
AS $$
declare 

  dDataOperacao date;
  sObservacao   varchar;
  bManual       boolean;
  iTipoDebito   integer;
  sTabela       varchar;
  iNumpre       integer;
  iNumpar       integer;
  
  crLevanta     refcursor;
  rLevanta      record;
  
begin
  
  
  sTabela       := lower(TG_TABLE_NAME);
  
  /**
   * Quando for um débito de issqn 
   * Levantamento, complementar ou variável
   */
  if sTabela = 'issvar' then 

      dDataOperacao := fc_getsession('DB_datausu');
      sObservacao   := 'Encerramento de competência';
      bManual       := false;

    /**
     * Verifica se a data do numpre ja se encontra registrada
     */
    perform * from informacaodebito where informacaodebito.k163_numpre = new.q05_numpre and informacaodebito.k163_numpar = new.q05_numpar;
    
    /**
     * Se sim, atualiza para a data informada
     */
    if found then 

      if lower(TG_OP) <> 'insert' then
        
        /**
         * Verifica se houve alguma alteração nos valores. Se sim, atualiza.
         */
        if ((new.q05_vlrinf <> old.q05_vlrinf) or (new.q05_valor <> old.q05_valor)) then
        
         update informacaodebito set k163_data = dDataOperacao, k163_observacao = sObservacao, k163_manual = bManual where k163_numpre = new.q05_numpre and k163_numpar = new.q05_numpar;
         
        end if;
      else 
        update informacaodebito set k163_data = dDataOperacao, k163_observacao = sObservacao, k163_manual = bManual where k163_numpre = new.q05_numpre and k163_numpar = new.q05_numpar;
      end if;
      
    /**
     * Se não, insere um novo registro armazenando a data de origem do numpre
     */  
    else
    
      insert into informacaodebito (k163_sequencial, k163_numpre, k163_numpar, k163_data, k163_observacao, k163_manual) values (nextval('informacaodebito_k163_sequencial_seq'), new.q05_numpre, new.q05_numpar, dDataOperacao, sObservacao, bManual);
      
    end if;
    
  /**
   * Movimentos na tabela arrecad
   */  
  elsif sTabela = 'arrecad' then
  
    /**
     * Verifica o tipo de débito
     */
    select k03_tipo into iTipoDebito from arretipo where k00_tipo = new.k00_tipo;
  
    /**
     * Divida ativa
     */
    if iTipoDebito = 5 then 
      
      /**
       * Verifica o numpre de origem da divida
       */   
      select informacaodebito.k163_data, informacaodebito.k163_observacao, informacaodebito.k163_manual
        into dDataOperacao, sObservacao, bManual
        from divida 
             inner join divold   
                     on divold.k10_coddiv  = divida.v01_coddiv
             inner join informacaodebito 
                     on divold.k10_numpre = informacaodebito.k163_numpre
                    and divold.k10_numpar = informacaodebito.k163_numpar
       where divida.v01_numpre = new.k00_numpre
         and divida.v01_numpar = new.k00_numpar;
   
      /**
       * Não permite inclusao de informação duplicada.
       */ 
      perform * 
         from informacaodebito
        where k163_numpre = new.k00_numpre
          and k163_numpar = new.k00_numpar;

      if not found and dDataOperacao is not null then
        insert into informacaodebito (k163_sequencial, k163_numpre, k163_numpar, k163_data, k163_observacao, k163_manual) values (nextval('informacaodebito_k163_sequencial_seq'), new.k00_numpre, new.k00_numpar, dDataOperacao, sObservacao, bManual);
      end if;

    elsif iTipoDebito = 13 then 
      
      /**
       * Verifica o numpre de origem cob. adm.
       */   
      select informacaodebito.k163_data, informacaodebito.k163_observacao, informacaodebito.k163_manual
        into dDataOperacao, sObservacao, bManual
        from diversos 
             inner join diverimportaold 
                     on diversos.dv05_coddiver      = diverimportaold.dv13_diversos
             inner join informacaodebito  
                     on diverimportaold.dv13_numpre = informacaodebito.k163_numpre
                    and diverimportaold.dv13_numpar = informacaodebito.k163_numpar
       where diverimportaold.dv13_numpre = new.k00_numpre
         and diverimportaold.dv13_numpar = new.k00_numpar;
   
      /**
       * Não permite inclusao de informação duplicada.
       */ 
      perform * 
         from informacaodebito
        where k163_numpre = new.k00_numpre
          and k163_numpar = new.k00_numpar;

      if not found and dDataOperacao is not null then
        insert into informacaodebito (k163_sequencial, k163_numpre, k163_numpar, k163_data, k163_observacao, k163_manual) values (nextval('informacaodebito_k163_sequencial_seq'), new.k00_numpre, new.k00_numpar, dDataOperacao, sObservacao, bManual);
      end if;

    end if;
  
  elsif sTabela = 'issvarlev' then

    /**
     * Verifica se é um registro de levantamento fiscal
     */
    select distinct y60_data, issvar.q05_numpre, issvar.q05_numpar
          into dDataOperacao, iNumpre, iNumpar
      from levanta
          inner join issvarlev on issvarlev.q18_codlev = levanta.y60_codlev
          inner join issvar    on issvar.q05_codigo    = issvarlev.q18_codigo
    where issvar.q05_codigo = new.q18_codigo limit 1;
    
    /**
     * Se nao for issqn variavel por levamentamento fiscal e complementar ou fixado 
     * é setada a data da sessao no momento em que usuario gera valor. 
     */ 
    if found then 

      sObservacao := 'Levantamento Fiscal';

      update informacaodebito set k163_data = dDataOperacao, k163_observacao = sObservacao where k163_numpre = iNumpre and k163_numpar = iNumpar;

    end if;

  end if;
  
  return new;
     
end;
$$
LANGUAGE 'plpgsql';

create trigger tg_arrecad_informacaodebito after insert on caixa.arrecad for each row execute             procedure fc_arrecad_informacaodebito();
create trigger tg_arrecad_informacaodebito after insert or update on issqn.issvar for each row execute    procedure fc_arrecad_informacaodebito();
create trigger tg_arrecad_informacaodebito after insert or update on issqn.issvarlev for each row execute procedure fc_arrecad_informacaodebito();

SQL2
    );

    DB::connection()->getPdo()->exec(
      <<<SQL3
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228894 ,'Data de Lançamento' ,'Data de Lançamento' ,'web/tributario/arrecadacao/data-de-lancamento' ,'1' ,'1' ,'Rotina para registrar manualmente a data de lançamento de débitos.' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 32 ,228894 ,1016 ,1985522 );
SQL3
    );
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    DB::connection()->getPdo()->exec(
      <<<SQLD
        ALTER TABLE informacaodebito DROP k163_observacao;
        ALTER TABLE informacaodebito DROP k163_manual;
SQLD
    );

    DB::connection()->getPdo()->exec(
      <<<SQLD2
drop trigger  if exists tg_arrecad_informacaodebito on issqn.issvar;
drop trigger  if exists tg_arrecad_informacaodebito on caixa.arrecad;
drop trigger  if exists tg_arrecad_informacaodebito on issqn.issvarlev;
drop function if exists fc_arrecad_informacaodebito();

create or replace function fc_arrecad_informacaodebito() returns trigger as  
$$
declare 

  dDataOperacao date;
  iTipoDebito   integer;
  sTabela       varchar;
  iNumpre       integer;
  iNumpar       integer;
  
  crLevanta     refcursor;
  rLevanta      record;
  
begin
  
  
  sTabela       := lower(TG_TABLE_NAME);
  
  /**
   * Quando for um débito de issqn 
   * Levantamento, complementar ou variável
   */
  if sTabela = 'issvar' then 
  
    /**
     * Verifica se é um registro de levantamento fiscal
     */
    select distinct y60_data
           into dDataOperacao
      from levanta
           inner join issvarlev on issvarlev.q18_codlev = levanta.y60_codlev
           inner join issvar    on issvar.q05_codigo    = issvarlev.q18_codigo
     where issvar.q05_numpre = new.q05_numpre
       and issvar.q05_numpar = new.q05_numpar;
     
    /**
     * Se nao for issqn variavel por levamentamento fiscal e complementar ou fixado 
     * é setada a data da sessao no momento em que usuario gera valor. 
     */ 
    if not found then 
    
      dDataOperacao := fc_getsession('DB_datausu');
      
    end if;
    
    
    /**
     * Verifica se a data do numpre ja se encontra registrada
     */
    perform * from informacaodebito where informacaodebito.k163_numpre = new.q05_numpre and informacaodebito.k163_numpar = new.q05_numpar;
    
    /**
     * Se sim, atualiza para a data informada
     */
    if found then 

      if lower(TG_OP) <> 'insert' then
        
        /**
         * Verifica se houve alguma alteração nos valores. Se sim, atualiza.
         */
        if ((new.q05_vlrinf <> old.q05_vlrinf) or (new.q05_valor <> old.q05_valor)) then
        
         update informacaodebito set k163_data = dDataOperacao where k163_numpre = new.q05_numpre and k163_numpar = new.q05_numpar;
         
        end if;
      else 
        update informacaodebito set k163_data = dDataOperacao where k163_numpre = new.q05_numpre and k163_numpar = new.q05_numpar;
      end if;
      
    /**
     * Se não, insere um novo registro armazenando a data de origem do numpre
     */  
    else
    
      insert into informacaodebito (k163_sequencial, k163_numpre, k163_numpar, k163_data) values (nextval('informacaodebito_k163_sequencial_seq'), new.q05_numpre, new.q05_numpar, dDataOperacao);
      
    end if;
    
  /**
   * Movimentos na tabela arrecad
   */  
  elsif sTabela = 'arrecad' then
  
    /**
     * Verifica o tipo de débito
     */
    select k03_tipo into iTipoDebito from arretipo where k00_tipo = new.k00_tipo;
  
    /**
     * Divida ativa
     */
    if iTipoDebito = 5 then 
      
      /**
       * Verifica o numpre de origem da divida
       */   
      select arreold.k00_dtoper 
        into dDataOperacao
        from divida 
             inner join divold   on divold.k10_coddiv  = divida.v01_coddiv
             inner join arreold  on arreold.k00_numpre = divold. k10_numpre
             inner join arretipo on arretipo.k00_tipo  = arreold.k00_tipo
             inner join cadtipo  on cadtipo.k03_tipo   = arretipo.k03_tipo
       where cadtipo.k03_tipo  = 3
         and divida.v01_numpre = new.k00_numpre
         and divida.v01_numpar = new.k00_numpar;
         
         
      if not found then 
        dDataOperacao := fc_getsession('DB_datausu');
      end if;
   
      /**
       * Não permite inclusao de informação duplicada.
       */ 
      perform * 
         from informacaodebito
        where k163_numpre = new.k00_numpre
          and k163_numpar = new.k00_numpar;

      if not found then
        insert into informacaodebito (k163_sequencial, k163_numpre, k163_numpar, k163_data) values (nextval('informacaodebito_k163_sequencial_seq'), new.k00_numpre, new.k00_numpar, dDataOperacao);
      end if;

    end if;
  
  end if;
  
  return new;
     
end;
$$ 
language 'plpgsql';

create trigger tg_arrecad_informacaodebito after insert on caixa.arrecad for each row execute          procedure fc_arrecad_informacaodebito();
create trigger tg_arrecad_informacaodebito after insert or update on issqn.issvar for each row execute procedure fc_arrecad_informacaodebito();
create trigger tg_arrecad_informacaodebito after insert or update on issqn.issvarlev for each row execute procedure fc_arrecad_informacaodebito();

SQLD2
    );

    DB::connection()->getPdo()->exec(
      <<<SQL3
    delete from db_menu where id_item = 32 and id_item_filho = 228894 and menusequencia = 1016 and modulo = 1985522;
    delete from db_itensmenu where id_item = 228894 and descricao = 'Data de Lançamento' and help = 'Data de Lançamento' and funcao = 'web/tributario/arrecadacao/data-de-lancamento';
SQL3
    );
  }
}
