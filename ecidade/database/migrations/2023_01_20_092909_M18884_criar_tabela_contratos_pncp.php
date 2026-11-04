<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class M18884CriarTabelaContratosPncp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("SELECT public.fc_set_pg_search_path();");
        $this->upDicionario();
        $this->upEstrutura();
    }

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_sysarquivo values (1011018, 'contratospncp', 'Tabela com contratos enviados ao PNCP', 'pn04', '2023-01-20', 'Contratos do PNCP', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (91,1011018);

            insert into db_syscampo values(1014707,'pn04_codigo','int4','Código do contrato no PNCP','0', 'Código Contrato',10,'f','f','f',1,'text','Código Contrato');
            insert into db_syscampo values(1014708,'pn04_acordo','int4','Referência ao acordo utilizado na geração do contrato','0', 'Acordo',10,'f','f','f',1,'text','Acordo');
            insert into db_syscampo values(1014710,'pn04_numero','int4','Número do contrato no PNCP','0', 'Número Contrato',10,'f','f','f',1,'text','Número Contrato');
            insert into db_syscampo values(1014711,'pn04_ano','int4','Ano do contrato no PNCP','0', 'Ano Contrato',10,'f','f','f',1,'text','Ano Contrato');
            insert into db_syscampo values(1014712,'pn04_usuario','int4','Usuário que gerou o contrato','0', 'Usuário',10,'f','f','f',1,'text','Usuário');
            insert into db_syscampo values(1014713,'pn04_instit','int4','Instituição da qual o contrato foi gerado','0', 'Instituição',10,'f','f','f',1,'text','Instituição');
            insert into db_syscampo values(1014719,'pn04_unidade','int4','Unidade que realizou a contratação','0', 'Unidade Contrato',10,'f','f','f',1,'text','Unidade Contrato');
            insert into db_syscampo values(1014720,'pn04_datapublicacao','date','Data da publicação do contrato no PNCP','null', 'Data Publicação',10,'f','f','f',1,'text','Data Publicação');

            delete from db_sysarqcamp where codarq = 1011018;
            insert into db_sysarqcamp values(1011018,1014707,1,0);
            insert into db_sysarqcamp values(1011018,1014710,2,0);
            insert into db_sysarqcamp values(1011018,1014711,3,0);
            insert into db_sysarqcamp values(1011018,1014708,4,0);
            insert into db_sysarqcamp values(1011018,1014713,6,0);
            insert into db_sysarqcamp values(1011018,1014712,7,0);
            update db_sysarqcamp set codsequencia = 1001108 where codarq = 1011018 and codcam = 1014707;
            delete from db_sysarqcamp where codarq = 1011018;
            insert into db_sysarqcamp values(1011018,1014707,1,1001108);
            insert into db_sysarqcamp values(1011018,1014708,2,0);
            insert into db_sysarqcamp values(1011018,1014719,3,0);
            insert into db_sysarqcamp values(1011018,1014710,4,0);
            insert into db_sysarqcamp values(1011018,1014711,5,0);
            insert into db_sysarqcamp values(1011018,1014720,6,0);
            insert into db_sysarqcamp values(1011018,1014713,7,0);
            insert into db_sysarqcamp values(1011018,1014712,8,0);

            insert into db_syssequencia values(1001108, 'contratospncp_pn04_codigo_seq', 1, 1, 9223372036854775807, 1, 1);

            delete from db_sysforkey where codarq = 1011018 and referen = 0;
            insert into db_sysforkey values(1011018,1014708,1,2828,0);
            delete from db_sysforkey where codarq = 1011018 and referen = 0;
            insert into db_sysforkey values(1011018,1014719,1,1011002,0);
            delete from db_sysforkey where codarq = 1011018 and referen = 0;
            insert into db_sysforkey values(1011018,1014712,1,109,0);
            delete from db_sysforkey where codarq = 1011018 and referen = 0;
            insert into db_sysforkey values(1011018,1014713,1,83,0);

            delete from db_sysprikey where codarq = 1011018;
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011018,1014707,1,1014707);
SQL
        );
    }

    private function upEstrutura()
    {
        Schema::create('pncp.contratospncp', function (Blueprint $table) {
            $table->increments('pn04_codigo');
            $table->integer('pn04_acordo');
            $table->integer('pn04_unidade');
            $table->integer('pn04_numero');
            $table->integer('pn04_ano');
            $table->date('pn04_datapublicacao');
            $table->integer('pn04_usuario');
            $table->integer('pn04_instit');

            $table->foreign('pn04_acordo')->references('ac16_sequencial')->on('acordo');
            $table->foreign('pn04_unidade')->references('pn02_unidade')->on('unidadespncp');
            $table->foreign('pn04_usuario')->references('id_usuario')->on('db_usuarios');
            $table->foreign('pn04_instit')->references('codigo')->on('db_config');
        });

        DB::statement("SELECT public.fc_set_pg_search_path();");
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('pncp.contratospncp');");
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

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            delete from db_syssequencia where codsequencia = 1001108;

            delete from db_sysforkey where codarq = 1011018;
            delete from db_sysprikey where codarq = 1011018;

            delete from db_sysarqcamp where codarq = 1011018;

            delete from db_syscampo where codcam = 1014707;
            delete from db_syscampo where codcam = 1014708;
            delete from db_syscampo where codcam = 1014710;
            delete from db_syscampo where codcam = 1014711;
            delete from db_syscampo where codcam = 1014712;
            delete from db_syscampo where codcam = 1014713;
            delete from db_syscampo where codcam = 1014719;
            delete from db_syscampo where codcam = 1014720;

            delete from db_sysarqmod where codarq = 1011018;
            delete from db_sysarquivo where codarq = 1011018;
SQL
        );
    }

    private function downEstrutura()
    {
        Schema::drop('pncp.contratospncp');
    }
}
