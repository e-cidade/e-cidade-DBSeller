<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M18884UnidadesPncp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("select public.fc_set_pg_search_path();");
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
insert into db_sysarquivo values (1011002, 'unidadespncp', 'Unidades do Portal Nacional de Contratações Públicas', 'up01', '2022-11-16', 'Unidades PNCP', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (91,1011002);
insert into db_syscampo values(1014603,'pn02_codigo','int4','Sequencial da tabela unidadespncp','0', 'Sequencial Unidade PNCP',10,'f','f','f',1,'text','Sequencial Unidade PNCP');
insert into db_syscampo values(1014604,'pn02_unidade','int4','Codigo da Unidade','0', 'Codigo da Unidade',10,'f','f','f',1,'text','Codigo da Unidade');
insert into db_syscampo values(1014605,'pn02_nome','varchar(100)','Nome da Unidade','', 'Nome da Unidade',100,'f','f','f',0,'text','Nome da Unidade');
insert into db_syscampo values(1014606,'pn02_ativo','bool','Unidade Ativa','f', 'Unidade Ativa',1,'f','f','f',5,'text','Unidade Ativa');
insert into db_syscampo values(1014607,'pn02_instit','int4','Instituição da Unidade','0', 'Instituição da Unidade',10,'f','f','f',1,'text','Instituição da Unidade');
insert into db_syscampo values(1014608,'pn02_data','varchar(10)','Data da Inclusão','0', 'Data da Inclusão',10,'f','f','f',1,'text','Data da Inclusão');
insert into db_sysarqcamp values(1011002,1014608,6,0);
insert into db_sysarqcamp values(1011002,1014607,1,0);
insert into db_sysarqcamp values(1011002,1014606,2,0);
insert into db_sysarqcamp values(1011002,1014605,3,0);
insert into db_sysarqcamp values(1011002,1014604,4,0);
insert into db_sysarqcamp values(1011002,1014603,5,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011002,1014604,1,1014604);
insert into db_syssequencia values(1001104, 'unidadespncp_pn02_codigo_seq', 1, 1, 9223372036854775807, 1, 1);
insert into db_sysforkey values(1011002,1014607,1,83,0);
update db_sysarqcamp set codsequencia = 1001104 where codarq = 1011002 and codcam = 1014604;
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_syssequencia where codsequencia = 1001104;
delete from db_sysprikey where codarq = 1011002;
delete from db_sysarqcamp where codarq = 1011002;
delete from db_syscampo where codcam = 1014603;
delete from db_syscampo where codcam = 1014604;
delete from db_syscampo where codcam = 1014605;
delete from db_syscampo where codcam = 1014606;
delete from db_syscampo where codcam = 1014607;
delete from db_syscampo where codcam = 1014608;
delete from db_sysarqmod where codarq = 1011002;
delete from db_sysarquivo where codarq = 1011002;
delete from db_sysforkey where codarq = 1011002 and referen = 0;
SQL
        );
    }

    private function upEstrutura()
    {
        Schema::create('pncp.unidadespncp', function (Blueprint $table) {
            $table->increments('pn02_codigo');
            $table->integer('pn02_unidade')->unique();
            $table->string('pn02_nome', 100);
            $table->boolean('pn02_ativo');
            $table->integer('pn02_instit');
            $table->string('pn02_data');
            $table->foreign('pn02_instit')->references('codigo')->on('db_config');
        });
        DB::statement("select public.fc_set_pg_search_path();");
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('pncp.unidadespncp');");
    }

    private function downEstrutura()
    {
        Schema::drop('pncp.unidadespncp');
    }
}
