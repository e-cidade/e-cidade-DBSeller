<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M18884TabelaPublicacoespncp extends Migration
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
insert into db_sysarquivo values (1011003, 'compraspncp', 'Compras publicadas no PNCP', 'pn03', '2022-12-06', 'Compras PNCP', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (91,1011003);
insert into db_syscampo values(1014623,'pn03_codigo','int4','Sequencial Publicacoes PNCP','0', 'Sequencial Publicacoes PNCP',10,'f','f','f',1,'text','Sequencial Publicacoes PNCP');
insert into db_syscampo values(1014624,'pn03_liclicita','int4','Código licitacao da compra','0', 'Codigo licitacao da compra',1,'f','f','f',1,'text','Codigo licitacao da compra');
insert into db_syscampo values(1014626,'pn03_numero','int4','Codigo PNCP','0', 'Codigo PNCP',10,'f','f','f',1,'text','Codigo PNCP');
insert into db_syscampo values(1014721,'pn03_unidade','int4','Unidade Compradora','0', 'Unidade Compradora',10,'f','f','f',1,'text','Unidade Compradora');
insert into db_syscampo values(1014627,'pn03_ano','int4','Ano da compra','0', 'Ano da compra',10,'f','f','f',1,'text','Ano da compra');
insert into db_syscampo values(1014628,'pn03_instituicao','int4','Instituicao','0', 'Instituicao',10,'f','f','f',1,'text','Instituicao');
insert into db_syscampo values(1014686,'pn03_usuario','int4','Usuário que fez a compra','0', 'Usuário',10,'f','f','f',1,'text','Usuário');
insert into db_syscampo values(1014687,'pn03_datapublicacao','date','Data Publicacao PNCP','null', 'Data Publicacao PNCP',10,'f','f','f',1,'text','Data Publicacao PNCP');
insert into db_sysarqcamp values(1011003,1014628,1,0);
insert into db_sysarqcamp values(1011003,1014627,2,0);
insert into db_sysarqcamp values(1011003,1014626,3,0);
insert into db_sysarqcamp values(1011003,1014624,5,0);
insert into db_sysarqcamp values(1011003,1014623,6,0);
insert into db_sysarqcamp values(1011003,1014687,7,0);
insert into db_sysarqcamp values(1011003,1014686,8,0);
insert into db_sysarqcamp values(1011003,1014721,9,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011003,1014623,1,1014623);
insert into db_sysforkey values(1011003,1014624,1,1260,0);
insert into db_sysforkey values(1011003,1014686,1,109,0);
insert into db_sysforkey values(1011003,1014721,1,83,0);
insert into db_sysforkey values(1011003,1014628,1,83,0);
insert into db_syssequencia values(1001105, 'compraspncp_pn03_codigo_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001105 where codarq = 1011003 and codcam = 1014623;

SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_sysforkey where codarq = 1011003;
delete from db_syssequencia where codsequencia = 1001105;
delete from db_sysprikey where codarq = 1011003;
delete from db_sysarqcamp where codarq = 1011003;
delete from db_syscampo where codcam = 1014628;
delete from db_syscampo where codcam = 1014627;
delete from db_syscampo where codcam = 1014721;
delete from db_syscampo where codcam = 1014626;
delete from db_syscampo where codcam = 1014624;
delete from db_syscampo where codcam = 1014623;
delete from db_syscampo where codcam = 1014686;
delete from db_syscampo where codcam = 1014687;
delete from db_sysarqmod where codarq = 1011003;
delete from db_sysarquivo where codarq = 1011003;
SQL
        );
    }

    private function upEstrutura()
    {
        Schema::create('pncp.compraspncp', function (Blueprint $table) {
            $table->increments('pn03_codigo');
            $table->integer('pn03_liclicita')->unsigned();
            $table->integer('pn03_numero');
            $table->integer('pn03_unidade');
            $table->integer('pn03_ano');
            $table->integer('pn03_instituicao');
            $table->integer('pn03_usuario')->unsigned();
            $table->foreign('pn03_liclicita')->references('l20_codigo')->on('liclicita');
            $table->foreign('pn03_usuario')->references('id_usuario')->on('db_usuarios');
            $table->foreign('pn03_unidade')->references('pn02_unidade')->on('unidadespncp');
            $table->foreign('pn03_instituicao')->references('codigo')->on('db_config');
            $table->date('pn03_datapublicacao');
        });
        DB::statement("select public.fc_set_pg_search_path();");
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('pncp.compraspncp');");
    }

    private function downEstrutura()
    {
        Schema::drop('pncp.compraspncp');
    }
}
