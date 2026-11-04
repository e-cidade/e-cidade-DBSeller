<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26318CriaEstruturaControleJob extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
insert into db_sysarquivo values(1011162, 'lancamentocontabilrequisicaobatch', 'Lotes de envio para processamento de lançamentos contábeis de requisição de material', 'm106', '2023-11-14', 'Lote envio processamento lançamentos contabeis ', 0, 't', 't', 't', 't');
insert into db_sysarqmod values(13, 1011162);
insert into db_syscampo values(1015542, 'm106_sequencial', 'int4', 'Sequencial', '0', 'Sequencial', 10, 'f', 'f', 'f', 1, 'text', 'Sequencial');
insert into db_syscampo values(1015543, 'm106_batch', 'int8', 'Sequencial Batch', '0', 'Sequencial Batch', 10, 'f', 'f', 'f', 1, 'text', 'Sequencial Batch');
insert into db_syscampo values(1015544, 'm106_parametros', 'text', 'Parâmetros enviados', '', 'Parâmetros', 100, 'f', 't', 'f', 0, 'text', 'Parâmetros');
insert into db_syscampo values(1015545, 'm106_status', 'varchar(20)', 'Status: PROCESSANDO, SUCESSO, ERRO', '', 'Status', 20, 'f', 't', 'f', 0, 'text', 'Status');
insert into db_syscampo values(1015546, 'm106_exception', 'text', 'Msg Erro', '', 'Msg Erro', 100, 't', 't', 'f', 0, 'text', 'Msg Erro');
insert into db_sysarqcamp values(1011162, 1015542, 1, 0);
insert into db_sysarqcamp values(1011162, 1015543, 2, 0);
insert into db_sysarqcamp values(1011162, 1015544, 3, 0);
insert into db_sysarqcamp values(1011162, 1015545, 4, 0);
insert into db_sysarqcamp values(1011162, 1015546, 5, 0);
insert into db_sysprikey(codarq, codcam, sequen, camiden) values(1011162, 1015542, 1, 1015543);
SQL;
        DB::connection()->getPdo()->exec($sql);
         
        Schema::create("material.lancamentocontabilrequisicaobatch", function (Blueprint $table) {
            $table->bigIncrements("m106_sequencial");
            $table->bigInteger("m106_batch");
            $table->jsonb("m106_parametros");
            $table->enum("m106_status", ['PROCESSANDO', 'SUCESSO', 'ERRO'])->default('PROCESSANDO');
            $table->text("m106_exception")->nullable();
            $table->timestamps();

            $table->foreign(
                "m106_batch",
                "lancamentocontabilrequisicaobatch_batch_jobs_fk"
            )->references("id")->on("public.batch_jobs");
        });

        DB::statement("select configuracoes.fc_auditoria_cria_funcao('material.lancamentocontabilrequisicaobatch');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
delete from db_sysprikey where codarq = 1011162;
delete from db_syscampo where codcam in (1015542,1015543,1015544,1015545,1015546); 
delete from db_sysarqcamp where codarq = 1011162;  
delete from db_sysarqmod where codarq = 1011162;
delete from db_sysarquivo where codarq = 1011162;
SQL;
        DB::connection()->getPdo()->exec($sql);
        Schema::dropIfExists("material.lancamentocontabilrequisicaobatch");
    }
}
