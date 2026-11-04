<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24585EstruturaUnidadesOrigem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upEstrutura();
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downEstrutura();
        $this->downDicionario();
    }

    public function upEstrutura(){
DB::unprepared(<<<SQL
/**
=================================================================================
Tabela UnidadesOrigem
 */
CREATE SEQUENCE IF NOT EXISTS ambulatorial.unidadesorigem_sd112_sequencial_seq 
INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE ambulatorial.unidadesorigem (
  sd112_sequencial INT NOT NULL DEFAULT nextval('unidadesorigem_sd112_sequencial_seq'),
  sd112_descricao VARCHAR(255),
  sd112_ativo  BOOLEAN DEFAULT TRUE,
  CONSTRAINT unidadesorigem_pk PRIMARY KEY (sd112_sequencial)
);
select configuracoes.fc_auditoria_cria_funcao('ambulatorial.unidadesorigem');

/**
=================================================================================
Tabela Prontuarios
 */
ALTER TABLE ambulatorial.prontuarios ADD COLUMN sd24_unidadeorigem INT;
ALTER TABLE ambulatorial.prontuarios ADD CONSTRAINT prontuarios_unidadeorigem_fk FOREIGN KEY (sd24_unidadeorigem)
REFERENCES ambulatorial.unidadesorigem(sd112_sequencial);
SQL
        );    
    }

    public function downEstrutura(){
DB::unprepared(<<<SQL
/**
=================================================================================
Tabela Prontuarios
 */
ALTER TABLE ambulatorial.prontuarios DROP COLUMN sd24_unidadeorigem;
/**
=================================================================================
Tabela UnidadesOrigem
 */
DROP TABLE ambulatorial.unidadesorigem;
DROP SEQUENCE ambulatorial.unidadesorigem_sd112_sequencial_seq;
SQL
        ); 
    }
    
    public function upDicionario(){
DB::unprepared(<<<SQL
/**
=================================================================================
Tabela UnidadesOrigem
 */
insert into db_syscampo values(1015657,'sd112_sequencial','int4','Sequencial da unidade de origem','0', 'Sequencial da unidade de origem',10,'f','f','f',1,'text','Sequencial da unidad
e de origem');
insert into db_syscampo values(1015658,'sd112_descricao','varchar(50)','Descrição da unidade de origem','', 'Descrição',50,'f','f','f',0,'text','Descrição');
insert into db_syscampo values(1015659,'sd112_ativo','bool','Verifica se a unidade de origem está ativa.','true', 'Ativo',1,'f','f','f',5,'text','Ativo');
insert into db_sysarquivo values (1011177, 'unidadesorigem', 'Unidades de Origem do ambulatorial', 'sd112', '2024-04-29', 'Unidades de Origem', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (1000004,1011177);
insert into db_sysarqcamp values(1011177,1015657,1,0);
insert into db_sysarqcamp values(1011177,1015658,2,0);
insert into db_sysarqcamp values(1011177,1015659,3,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011177,1015657,1,1015657);
insert into db_syssequencia values(1001183, 'unidadesorigem_sd112_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001183 where codarq = 1011177 and codcam = 1015657;
/**
=================================================================================
Tabela Prontuarios
 */
insert into db_syscampo values(1015662,'sd24_unidadeorigem','int4','Unidade de origem','0', 'Unidade Origem',10,'t','f','f',1,'text','Unidade Origem');
insert into db_sysarqcamp values(1010134,1015662,24,0);
insert into db_sysforkey values(1010134,1015662,1,1011177,0);
/**
=================================================================================
Itens de Menu
 */
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229262 ,'Unidade Origem' ,'Unidade Origem' ,'web/saude/ambulatorial/cadastros/unidade-origem' ,'1' ,'1' ,'Unidade de origem' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 3470 ,229262 ,49 ,1000004 );
SQL
        );         
    }

    public function downDicionario(){
DB::unprepared(<<<SQL
/**
=================================================================================
Tabela UnidadesOrigem
 */
delete from db_sysarqcamp where codarq = 1011177;
delete from db_sysprikey where codarq = 1011177;
delete from db_sysarqcamp where codarq = 1011177;
delete from db_sysprikey where codarq = 1011177;
delete from db_syssequencia where codsequencia = 1001183;
delete from db_syscampo where codcam in (1015657,1015658,1015659);
delete from db_sysarqmod where codmod = 1000004 and codarq = 1011177;
delete from db_sysarquivo where codarq = 1011177;
/**
=================================================================================
Tabela Prontuarios
 */
delete from db_sysforkey where codarq = 1010134 and codcam = 1015662;
delete from db_sysarqcamp where codarq = 1010134 and codcam = 1015662;
delete from db_syscampo where codcam  = 1015662;
/**
=================================================================================
Itens de Menu
 */
delete from db_menu where id_item_filho = 229262 AND modulo = 1000004;
delete from db_itensmenu where id_item = 229262;
SQL
        );
    }
}
