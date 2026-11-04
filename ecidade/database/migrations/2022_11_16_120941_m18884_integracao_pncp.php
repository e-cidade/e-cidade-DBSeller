<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M18884IntegracaoPncp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    /**
     * TODO criar a tabela no dicionario de dados
     */
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_sysarquivo values (1011001, 'integracaopncp', 'Integração com o Portal Nacional de Contratações Públicas', 'pn01', '2022-11-16', 'Integração PNCP', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (91,1011001);
insert into db_syscampo values(1014598,'pn01_codigo','int4','Codigo Integração PNCP','0', 'Codigo Integração PNCP',10,'f','f','f',1,'text','Codigo Integração PNCP');
insert into db_syscampo values(1014599,'pn01_habilitado','bool','Integracao Habilitado','f', 'Integracao Habilitado',1,'f','f','f',5,'text','Integracao Habilitado');
insert into db_syscampo values(1014600,'pn01_data','varchar(10)','Data Integração PNCP','', 'Data Integração PNCP',10,'f','f','f',0,'text','Data Integração PNCP');
insert into db_syscampo values(1014601,'pn01_instit','int4','Instituição Integração','0', 'Instituição Integração',10,'f','f','f',1,'text','Instituição Integração');
insert into db_syscampo values(1014706,'pn01_usuario','int4','Usuário que habilitou a integração','0', 'Usuário',10,'f','f','f',1,'text','Usuário');
insert into db_sysarqcamp values(1011001,1014706,6,0);
insert into db_sysarqcamp values(1011001,1014600,2,0);
insert into db_sysarqcamp values(1011001,1014599,3,0);
insert into db_sysarqcamp values(1011001,1014598,4,0);
insert into db_sysarqcamp values(1011001,1014601,1,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011001,1014598,1,1014598);
insert into db_syssequencia values(1001103, 'integracaopncp_pn01_codigo_seq', 1, 1, 9223372036854775807, 1, 1);
insert into db_sysforkey values(1011001,1014706,1,109,0);
insert into db_sysforkey values(1011001,1014601,1,83,0);
update db_sysarqcamp set codsequencia = 1001103 where codarq = 1011001 and codcam = 1014598;

SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_syssequencia where codsequencia = 1001103;
delete from db_sysprikey where codarq = 1011001;
delete from db_sysforkey where codarq = 1011001;
delete from db_sysarqcamp where codarq = 1011001;
delete from db_syscampo where codcam = 1014598;
delete from db_syscampo where codcam = 1014599;
delete from db_syscampo where codcam = 1014600;
delete from db_syscampo where codcam = 1014601;
delete from db_syscampo where codcam = 1014706;
delete from db_sysarqmod where codarq = 1011001;
delete from db_sysarquivo where codarq = 1011001;
SQL
        );
    }

    private function upEstrutura()
    {
        DB::statement("create schema pncp;");
        DB::statement("select public.fc_set_pg_search_path();");
        Schema::create('pncp.integracaopncp', function (Blueprint $table) {
            $table->increments('pn01_codigo');
            $table->boolean('pn01_habilitado');
            $table->string('pn01_data', 11);
            $table->integer('pn01_instit')->unique();
            $table->integer('pn01_usuario');
            $table->foreign('pn01_instit')->references('codigo')->on('db_config');
            $table->foreign('pn01_usuario')->references('id_usuario')->on('db_usuarios');
        });
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('pncp.integracaopncp');");
    }

    private function downEstrutura()
    {
        Schema::drop('pncp.integracaopncp');
        DB::statement("drop schema pncp cascade;");
    }
}
