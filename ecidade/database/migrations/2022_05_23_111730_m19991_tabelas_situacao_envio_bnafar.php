<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M19991TabelasSituacaoEnvioBnafar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        Schema::create('farmacia.bnafarenvios', function (Blueprint $table) {
            $table->bigIncrements('fa70_id');
            $table->integer('fa70_matestoqueini');
            $table->dateTime('fa70_data');
            $table->string('fa70_uri');
            $table->string('fa70_method', 10);
            $table->jsonb('fa70_body');
            $table->integer('fa70_codigobnafar')->nullable()->default(null);
            $table->integer('fa70_protocolo')->nullable()->default(null);

            $table->foreign('fa70_matestoqueini')->references('m80_codigo')->on('material.matestoqueini');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('farmacia.bnafarenvios');");

        Schema::create('farmacia.bnafarinconsistencias', function (Blueprint $table) {
            $table->bigIncrements('fa71_id');
            $table->bigInteger('fa71_bnafarenvio');
            $table->jsonb('fa71_content');

            $table->foreign('fa71_bnafarenvio')->references('fa70_id')->on('farmacia.bnafarenvios');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('farmacia.bnafarinconsistencias');");

        Schema::create('farmacia.bnafarconferencias', function (Blueprint $table) {
            $table->bigIncrements('fa72_id');
            $table->bigInteger('fa72_bnafarinconsistencia');
            $table->integer('fa72_usuario');
            $table->dateTime('fa72_data')->default(DB::raw('now()'));

            $table->foreign('fa72_bnafarinconsistencia')->references('fa71_id')->on('farmacia.bnafarinconsistencias');
            $table->foreign('fa72_usuario')->references('id_usuario')->on('configuracoes.db_usuarios');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('farmacia.bnafarconferencias');");

        Schema::create('farmacia.bnafarerros', function (Blueprint $table) {
            $table->integer('fa73_matestoqueini');
            $table->string('fa73_descricao');
            $table->string('fa73_campo')->nullable();
            $table->integer('fa73_matestoqueitem')->nullable();

            $table->foreign('fa73_matestoqueini')->references('m80_codigo')->on('material.matestoqueini');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('farmacia.bnafarerros');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('farmacia.bnafarerros');
        Schema::drop('farmacia.bnafarconferencias');
        Schema::drop('farmacia.bnafarinconsistencias');
        Schema::drop('farmacia.bnafarenvios');
        $this->downDicionario();
    }

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_sysarquivo values (1010927, 'bnafarenvios', 'Procedimentos enviados para o BNAFAR.', 'fa70', '2022-05-23', 'BNAFAR Envios', 0, 't', 't', 't', 't' );
            insert into db_sysarqmod values (52,1010927);

            insert into db_sysarquivo values (1010928, 'bnafarinconsistencias', 'Inconsistências retornadas pelo BNAFAR.', 'fa71', '2022-05-23', 'BNAFAR Inconsistências', 0, 't', 't', 't', 't' );
            insert into db_sysarqmod values (52,1010928);

            insert into db_sysarquivo values (1010985, 'bnafarconferencias', 'Tabela de relacionamento que indica que uma inconsistência foi conferida.', 'fa72', '2022-09-05', 'BNAFAR Conferências', 0, 't', 't', 't', 't' );
            insert into db_sysarqmod values (52,1010985);

            insert into db_sysarquivo values (1010991, 'bnafarerros', 'Erros a serem tradados.', 'fa73', '2022-10-17', 'BNAFAR Erros', 0, 't', 't', 't', 't' );
            insert into db_sysarqmod values (52,1010991);

            insert into db_syscampo values(1014142,'fa70_id','int8','Chave primária da tabela.','0', 'Código',10,'f','f','f',1,'text','Código');
            insert into db_syscampo values(1014143,'fa70_matestoqueini','int4','Chave estrangeira da tabela matestoqueini.','0', 'Estoque Movimentação',10,'f','f','f',1,'text','Estoque Movimentação');
            insert into db_syscampo values(1014144,'fa70_data','date','Data em que o arquivo foi enviado.','null', 'Data',10,'f','f','f',1,'text','Data');
            insert into db_syscampo values(1014145,'fa70_uri','varchar(200)','Endereço do procedimento realizado.','', 'Endereço',200,'f','f','f',0,'text','Endereço');
            insert into db_syscampo values(1014146,'fa70_method','varchar(10)','Método http realizado(POST ou PUT)','', 'Método',10,'f','t','f',0,'text','Método');
            insert into db_syscampo values(1014147,'fa70_body','text','Corpo da requisição, em JSON.','', 'Body',1,'f','f','f',0,'text','Body');
            insert into db_syscampo values(1014148,'fa70_codigobnafar','int8','Código retornado pelo sistema do hórus','0', 'Código BNAFAR',10,'f','f','f',1,'text','Código BNAFAR');
            insert into db_syscampo values(1014149,'fa70_protocolo','int8','Protocolo retornado pelo sistema do Hórus para operações em lote.','0', 'Protocolo',10,'f','f','f',1,'text','Protocolo');

            insert into db_syscampo values(1014150,'fa71_bnafarenvio','int8','Chave estrangeira da tabela bnafarenvios','0', 'BNAFAR Envio',10,'f','f','f',1,'text','BNAFAR Envio');
            insert into db_syscampo values(1014151,'fa71_content','text','Campo com as inconsistências, em JSON, retornadas pelo sistema do hórus.','', 'Content',1,'f','f','f',0,'text','Content');
            insert into db_syscampo values(1014472,'fa71_id','int8','Chave primária da tabela bnafarinconsistencias.','0', 'Código',10,'f','f','f',1,'text','Código');

            insert into db_syscampo values(1014473,'fa72_id','int8','Chave primária da tabela bnafarconferencias.','0', 'Código',10,'f','f','f',1,'text','Código');
            insert into db_syscampo values(1014474,'fa72_data','date','Data da conferência.','null', 'Data',10,'f','f','f',1,'text','Data');
            insert into db_syscampodef values(1014474,'now()','');
            insert into db_syscampo values(1014475,'fa72_usuario','int4','Usuário que realizou a conferência','0', 'Usuário',10,'f','f','f',1,'text','Usuário');
            insert into db_syscampo values(1014476,'fa72_bnafarinconsistencia','int8','Chave estrangeira da tabela bnafarinconsistencias.','0', 'Código Inconsistência',10,'f','f','f',1,'text','Código Inconsistência');

            insert into db_syscampo values(1014550,'fa73_matestoqueini','int4','Chave estrangeira da tabela matestoqueini.','0', 'Estoque Movimentação',10,'f','f','f',1,'text','Estoque Movimentação');
            insert into db_syscampo values(1014551,'fa73_descricao','varchar(200)','Descrição do erro.','', 'Descrição',200,'f','t','f',0,'text','Descrição');
            insert into db_syscampo values(1014552,'fa73_campo','varchar(200)','Campo que ocasionou o erro.','', 'Campo',200,'t','t','f',0,'text','Campo');
            insert into db_syscampodef values(1014552,'null','');
            insert into db_syscampo values(1014553,'fa73_matestoqueitem','int4','Quando existir, representa a tabela matestoqueitem.','0', 'Estoque Item',10,'t','f','f',1,'text','Estoque Item');
            insert into db_syscampodef values(1014553,'null','');

            insert into db_sysarqcamp values(1010927,1014142,1,0);
            insert into db_sysarqcamp values(1010927,1014143,2,0);
            insert into db_sysarqcamp values(1010927,1014144,3,0);
            insert into db_sysarqcamp values(1010927,1014145,4,0);
            insert into db_sysarqcamp values(1010927,1014146,5,0);
            insert into db_sysarqcamp values(1010927,1014147,6,0);
            insert into db_sysarqcamp values(1010927,1014148,7,0);
            insert into db_sysarqcamp values(1010927,1014149,8,0);
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1010927,1014142,1,1014142);
            insert into db_sysforkey values(1010927,1014143,1,1133,0);

            insert into db_sysarqcamp values(1010928,1014150,1,0);
            insert into db_sysarqcamp values(1010928,1014151,2,0);
            insert into db_sysarqcamp values(1010928,1014472,3,0);
            insert into db_sysforkey values(1010928,1014150,1,1010927,0);
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1010928,1014472,1,1014150);

            insert into db_sysarqcamp values(1010985,1014473,1,0);
            insert into db_sysarqcamp values(1010985,1014476,2,0);
            insert into db_sysarqcamp values(1010985,1014475,3,0);
            insert into db_sysarqcamp values(1010985,1014474,4,0);
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1010985,1014473,1,1014473);
            insert into db_sysforkey values(1010985,1014476,1,1010928,0);
            insert into db_sysforkey values(1010985,1014475,1,109,0);

            insert into db_sysarqcamp values(1010991,1014550,1,0);
            insert into db_sysarqcamp values(1010991,1014551,2,0);
            insert into db_sysarqcamp values(1010991,1014552,3,0);
            insert into db_sysarqcamp values(1010991,1014553,4,0);
            insert into db_sysforkey values(1010991,1014550,1,1133,0);
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            delete from db_sysforkey where codarq in (1010928, 1010927, 1010985, 1010991);
            delete from db_sysprikey where codarq in (1010928, 1010927, 1010985);
            delete from db_sysarqcamp where codarq in (1010928, 1010927, 1010985, 1010991);
            delete from db_syscampodef where codcam in (1014474, 1014552, 1014553);
            delete from db_syscampo where codcam in (1014142, 1014143, 1014144, 1014145, 1014146, 1014147, 1014148, 1014149, 1014150, 1014151, 1014472, 1014473,1014474,1014475,1014476,1014550,1014551,1014552,1014553,1014553);
            delete from db_sysarqmod where codarq in (1010928, 1010927, 1010985, 1010991);
            delete from db_sysarquivo where codarq in (1010928, 1010927, 1010985, 1010991);
SQL
        );
    }
}
