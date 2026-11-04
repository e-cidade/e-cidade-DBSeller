<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24884BloqueioContracheque extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec($this->dicionario());
        DB::connection()->getPdo()->exec($this->menu());
        DB::connection()->getPdo()->exec($this->estrutura());
        DB::connection()->getPdo()->exec($this->migracao());    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec($this->dicionario(true));
        DB::connection()->getPdo()->exec($this->menu(true));
        DB::connection()->getPdo()->exec($this->estrutura(true));
    }


    public function dicionario($rollback = false)
    {
        $sql = <<<SQL
            insert into configuracoes.db_sysarquivo values (1011130, 'rhliberacontracheque', 'Controla a liberação de contracheque no portal transparência e portal do servidor.', 'rh301', '2023-08-18', 'Controle de Liberacao de Contracheque', 0, 'f', 'f', 'f', 'f' );
            insert into configuracoes.db_sysarqmod values (28,1011130);
            insert into configuracoes.db_syscampo values(1015322,'rh301_sequencial','int4','Código sequencial da configuracao','0', 'rh301_sequencial',10,'f','f','f',1,'text','rh301_sequencial');
            insert into configuracoes.db_syscampo values(1015323,'rh301_ano','int4','Ano da configuracao','0', 'rh301_ano',10,'f','f','f',1,'text','rh301_ano');
            insert into configuracoes.db_syscampo values(1015324,'rh301_mes','int4','mes da configuracao','0', 'rh301_mes',10,'f','f','f',1,'text','rh301_mes');
            insert into configuracoes.db_syscampo values(1015325,'rh301_instituicao','int4','codigo da instuicao da configuracao','0', 'rh301_instituicao',10,'f','f','f',1,'text','rh301_instituicao');
            insert into configuracoes.db_syscampo values(1015326,'rh301_salario','bool','Configuracao de liberacao de salario','f', 'rh301_salario',1,'f','f','f',5,'text','rh301_salario');
            insert into configuracoes.db_syscampodef values(1015326,'f','');
            insert into configuracoes.db_syscampo values(1015327,'rh301_rescisao','bool','configuracao de exibicao de rescisao','f', 'rh301_rescisao',1,'f','f','f',5,'text','rh301_rescisao');
            insert into configuracoes.db_syscampodef values(1015327,'f','');
            insert into configuracoes.db_syscampo values(1015328,'rh301_complementar','bool','configuracao de exibicao de folha complementar','f', 'rh301_complementar',1,'f','f','f',5,'text','rh301_complementar');
            insert into configuracoes.db_syscampodef values(1015328,'f','');
            insert into configuracoes.db_syscampo values(1015329,'rh301_decimo','bool','configuracao de exibicao de 13','f', 'rh301_decimo',1,'f','f','f',5,'text','rh301_decimo');
            insert into configuracoes.db_syscampodef values(1015329,'f','');
            insert into configuracoes.db_syscampo values(1015330,'rh301_adiantamento','bool','configuracao de exibicao de adiantamento','f', 'rh301_adiantamento',1,'f','f','f',5,'text','rh301_adiantamento');
            insert into configuracoes.db_syscampodef values(1015330,'f','');
            insert into configuracoes.db_syscampo values(1015331,'rh301_suplementar','bool','configuracao de exibicao de folha suplementar','f', 'rh301_suplementar',1,'f','f','f',5,'text','rh301_suplementar');
            insert into configuracoes.db_syscampodef values(1015331,'f','');
            delete from configuracoes.db_sysarqcamp where codarq = 1011130;
            insert into configuracoes.db_sysarqcamp values(1011130,1015322,1,0);
            insert into configuracoes.db_sysarqcamp values(1011130,1015323,2,0);
            insert into configuracoes.db_sysarqcamp values(1011130,1015324,3,0);
            insert into configuracoes.db_sysarqcamp values(1011130,1015325,4,0);
            insert into configuracoes.db_sysarqcamp values(1011130,1015326,5,0);
            insert into configuracoes.db_sysarqcamp values(1011130,1015327,6,0);
            insert into configuracoes.db_sysarqcamp values(1011130,1015328,7,0);
            insert into configuracoes.db_sysarqcamp values(1011130,1015329,8,0);
            insert into configuracoes.db_sysarqcamp values(1011130,1015330,9,0);
            insert into configuracoes.db_sysarqcamp values(1011130,1015331,10,0);
            delete from configuracoes.db_sysprikey where codarq = 1011130;
            insert into configuracoes.db_sysprikey (codarq,codcam,sequen,camiden) values(1011130,1015322,1,1015322);
            insert into configuracoes.db_sysindices values(1008891,'rh301_ano_rh301_mes_rh301_instituicao',1011130,'1');
            insert into configuracoes.db_syscadind values(1008891,1015323,1);
            insert into configuracoes.db_syscadind values(1008891,1015324,2);
            insert into configuracoes.db_syscadind values(1008891,1015325,3);
            insert into configuracoes.db_syssequencia values(1001155, 'rhliberacontracheque_rh301_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
            update configuracoes.db_sysarqcamp set codsequencia = 1001155 where codarq = 1011130 and codcam = 1015322;
SQL;
        if ($rollback) {
            $sql = <<<SQL
                delete from configuracoes.db_syssequencia where codsequencia = 1001155;
                delete from configuracoes.db_syscadind where codcam in (1015325, 1015324, 1015323);
                delete from configuracoes.db_sysindices where codarq in(1011130);
                delete from configuracoes.db_sysprikey where codarq = 1011130;
                delete from configuracoes.db_sysarqcamp where codarq = 1011130;
                delete from configuracoes.db_syscampodef where codcam in(1015326, 1015327, 1015328, 1015329, 1015330, 1015331);
                delete from configuracoes.db_syscampo where codcam in (1015322, 1015323, 1015324, 1015325, 1015326, 1015327, 1015328, 1015329, 1015330, 1015331);
                delete from configuracoes.db_sysarqmod where codarq = 1011130;
                delete from configuracoes.db_sysarquivo where codarq = 1011130;        
SQL;
        }
        return $sql;
    }

    public function menu($rollback = false)
    {
        $sql = <<<SQL
            insert into configuracoes.db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228961 ,'Liberação de Contracheques Online' ,'Liberação de Contracheques Online no Portal Transparência e Portal do Servidor' ,'' ,'1' ,'1' ,'Rotina de configuração para liberação de exibição dos contracheques no portal transparência e no portal do servidor.' ,'true' );
            update configuracoes.db_itensmenu set id_item = 228961 , descricao = 'Liberação de Contracheques Online' , help = 'Liberação de Contracheques Online no Portal Transparência e Portal do Servidor' , funcao = 'web/recursos-humanos/pessoal/rotinas_mensais/liberacao_contracheque_online' , itemativo = '1' , manutencao = '1' , desctec = 'Rotina de configuração para liberação de exibição dos contracheques no portal transparência e no portal do servidor.' , libcliente = 'true' where id_item = 228961;
            delete from configuracoes.db_menu where id_item_filho = 228961 AND modulo = 952;
            insert into configuracoes.db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 5110 ,228961 ,10 ,952 );

SQL;
        if ($rollback) {
            $sql = <<<SQL
                delete from configuracoes.db_menu where id_item_filho = 228961 AND modulo = 952;
                delete from configuracoes.db_itensmenu where id_item = 228961;
SQL;
        }
        return $sql;
    }

    public function estrutura($rollback = false)
    {
        $sql = <<<SQL
            CREATE TABLE pessoal.rhliberacontracheque(
                rh301_sequencial serial,
                rh301_ano int4 NOT NULL,
                rh301_mes int4 NOT NULL,
                rh301_instituicao int4 NOT NULL,
                rh301_salario boolean not null default false,
                rh301_rescisao boolean not null default false,
                rh301_complementar boolean not null default false,
                rh301_decimo boolean not null default false,
                rh301_adiantamento boolean not null default false,
                rh301_suplementar boolean not null default false,
                CONSTRAINT rhliberacontracheque_sequ_pk PRIMARY KEY (rh301_sequencial));
            CREATE UNIQUE INDEX rh301_ano_rh301_mes_rh301_instituicao ON pessoal.rhliberacontracheque(rh301_ano, rh301_mes, rh301_instituicao);

            SELECT configuracoes.fc_auditoria_cria_funcao('pessoal.rhliberacontracheque');
SQL;
        if ($rollback) {
            $sql = <<<SQL
                DROP INDEX rh301_ano_rh301_mes_rh301_instituicao;
                DROP TABLE pessoal.rhliberacontracheque;
                DROP SEQUENCE IF EXISTS pessoal.rhliberacontracheque_rh301_sequencial_seq;

                SELECT configuracoes.fc_auditoria_remove_funcao('pessoal.rhliberacontracheque');

SQL;
        }
        return $sql;
    }

    public function migracao()
    {
        $sql = <<<SQL
            insert into pessoal.rhliberacontracheque (
                rh301_ano,
                rh301_mes,
                rh301_instituicao,
                rh301_salario,
                rh301_rescisao,
                rh301_complementar,
                rh301_decimo,
                rh301_adiantamento,
                rh301_suplementar
            )         
            select distinct 
                rh02_anousu,
                rh02_mesusu,
                rh02_instit,
                true,
                true,
                true,
                true,
                true,
                true
            from 
                pessoal.rhpessoalmov 
            order by 
                rh02_instit asc, 
                rh02_anousu asc,
                rh02_mesusu asc;

SQL;
        return $sql;
    }
}