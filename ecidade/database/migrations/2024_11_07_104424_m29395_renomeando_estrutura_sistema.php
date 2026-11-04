<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M29395RenomeandoEstruturaSistema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upItensMenu();
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
        $this->downItensMenu();
        $this->downEstrutura();
        $this->downDicionario();
    }

    public function upItensMenu()
    {
        DB::statement(<<<SQL
update db_itensmenu set descricao = 'Unidade Encaminhadora',help='Unidade Encaminhadora',desctec='Unidade Encaminhadora',funcao='web/saude/ambulatorial/cadastros/unidade-encaminhadora' where id_item = 229262;
SQL
        );    
    }

    public function upEstrutura()
    {
        DB::unprepared(<<<SQL

CREATE TABLE ambulatorial.unidadesencaminhadoras (
  sd112_sequencial SERIAL NOT NULL ,
  sd112_descricao VARCHAR(255),
  sd112_ativo  BOOLEAN DEFAULT TRUE,
  CONSTRAINT unidadesencaminhadoras_pk PRIMARY KEY (sd112_sequencial)
);
select configuracoes.fc_auditoria_cria_funcao('ambulatorial.unidadesencaminhadoras');

INSERT INTO ambulatorial.unidadesencaminhadoras select * from ambulatorial.unidadesorigem;
SELECT setval('ambulatorial.unidadesencaminhadoras_sd112_sequencial_seq', 
              (SELECT MAX(sd112_sequencial) FROM ambulatorial.unidadesencaminhadoras));
              
ALTER TABLE ambulatorial.prontuarios ADD COLUMN sd24_unidadeencaminhadora INT;
ALTER TABLE ambulatorial.prontuarios ADD CONSTRAINT prontuarios_unidadeencaminhadora_fk FOREIGN KEY (sd24_unidadeencaminhadora)
REFERENCES ambulatorial.unidadesencaminhadoras(sd112_sequencial);

UPDATE ambulatorial.prontuarios set sd24_unidadeencaminhadora = sd24_unidadeorigem where sd24_unidadeorigem is not null;

ALTER TABLE ambulatorial.prontuarios DROP COLUMN sd24_unidadeorigem;
DROP TABLE ambulatorial.unidadesorigem;
SQL
        );
    }

    public function upDicionario()
    {
        DB::unprepared(<<<SQL
UPDATE db_syscampo set descricao = 'Sequencial da unidade encaminhadora',rotulo = 'Sequencial da unidade encaminhadora',rotulorel='Sequencial da unidade encaminhadora' where nomecam = 'sd112_sequencial';
UPDATE db_syscampo set descricao = 'Descrição da unidade encaminhadora' where nomecam = 'sd112_descricao';
UPDATE db_syscampo set descricao = 'Verifica se a unidade encaminhadora está ativa.' where nomecam = 'sd112_ativo';
UPDATE db_sysarquivo set nomearq = 'unidadesencaminhadoras',descricao ='Unidades Encaminhadoras do ambulatorial',rotulo='Unidades Encaminhadoras' where codarq = 1011177;
UPDATE db_syssequencia set nomesequencia = 'unidadesencaminhadoras_sd112_sequencial_seq' where codsequencia = 1001183;
UPDATE db_syscampo set nomecam = 'sd24_unidadeencaminhadora',descricao='Unidade Encaminhadora',rotulo='Unidade Encaminhadora',rotulorel='Unidade Encaminhadora' where codcam = 1015662;
SQL
        );  
    }

    public function downItensMenu()
    {
        DB::statement(<<<SQL
update db_itensmenu set descricao = 'Unidade Origem',help='Unidade Origem',desctec='Unidade de Origem',funcao='web/saude/ambulatorial/cadastros/unidade-origem' where id_item = 229262;
SQL
        );   
    }

    public function downEstrutura()
    {
        DB::unprepared(<<<SQL

CREATE TABLE ambulatorial.unidadesorigem (
  sd112_sequencial SERIAL NOT NULL,
  sd112_descricao VARCHAR(255),
  sd112_ativo  BOOLEAN DEFAULT TRUE,
  CONSTRAINT unidadesorigem_pk PRIMARY KEY (sd112_sequencial)
);
select configuracoes.fc_auditoria_cria_funcao('ambulatorial.unidadesorigem');

INSERT INTO ambulatorial.unidadesorigem select * from ambulatorial.unidadesencaminhadoras;
SELECT setval('ambulatorial.unidadesorigem_sd112_sequencial_seq', 
              (SELECT MAX(sd112_sequencial) FROM ambulatorial.unidadesorigem));


ALTER TABLE ambulatorial.prontuarios ADD COLUMN sd24_unidadeorigem INT;
ALTER TABLE ambulatorial.prontuarios ADD CONSTRAINT prontuarios_unidadeorigem_fk FOREIGN KEY (sd24_unidadeorigem)
REFERENCES ambulatorial.unidadesorigem(sd112_sequencial);

UPDATE ambulatorial.prontuarios set sd24_unidadeorigem = sd24_unidadeencaminhadora where sd24_unidadeencaminhadora is not null;

ALTER TABLE ambulatorial.prontuarios DROP COLUMN sd24_unidadeencaminhadora;
DROP TABLE ambulatorial.unidadesencaminhadoras;
SQL
        );
    }

    public function downDicionario()
    {
        DB::unprepared(<<<SQL
UPDATE db_syscampo set descricao = 'Sequencial da unidade de origem',rotulo = 'Sequencial da unidade de origem',rotulorel='Sequencial da unidade de origem' where nomecam = 'sd112_sequencial';
UPDATE db_syscampo set descricao = 'Descrição da unidade de origem' where nomecam = 'sd112_descricao';
UPDATE db_syscampo set descricao = 'Verifica se a unidade de origem está ativa.' where nomecam = 'sd112_ativo';
UPDATE db_sysarquivo set nomearq = 'unidadesorigem',descricao ='Unidades de Origem do ambulatorial',rotulo='Unidades de Origem' where codarq = 1011177;
UPDATE db_syssequencia set nomesequencia = 'unidadesorigem_sd112_sequencial_seq' where codsequencia = 1001183;
UPDATE db_syscampo set nomecam = 'sd24_unidadeorigem',descricao='Unidade de origem',rotulo='Unidade de origem',rotulorel='Unidade de origem' where codcam = 1015662;
SQL
        ); 
    }
}
