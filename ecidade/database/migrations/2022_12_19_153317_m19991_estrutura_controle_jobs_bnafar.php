<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M19991EstruturaControleJobsBnafar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        Schema::create('farmacia.bnafarbatch', function (Blueprint $table) {
            $table->bigIncrements('fa75_id');
            $table->bigInteger('fa75_batch');
            $table->integer('fa75_tipo');
            $table->boolean('fa75_concluido')->default(false);

            $table->foreign('fa75_batch', 'bnafarbatch_batch_jobs_fk')->references('id')->on('public.batch_jobs');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('farmacia.bnafarbatch');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        Schema::dropIfExists('farmacia.bnafarbatch');
    }

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_sysarquivo values (1011011, 'bnafarbatch', 'Batch dos jobs do BNAFAR.', 'fa75', '2022-12-26', 'Batch Bnafar', 0, 't', 't', 't', 't' );
            insert into db_sysarqmod values (52,1011011);

            insert into db_syscampo values(1014667,'fa75_id','int8','Chave primária da tabela bnafarbatch.','0', 'Código',10,'f','f','f',1,'text','Código');
            insert into db_syscampo values(1014668,'fa75_batch','int8','Chave estrangeira da tabela batch_jobs','0', 'Batch',10,'f','f','f',1,'text','Batch');
            insert into db_syscampo values(1014669,'fa75_tipo','int4','Tipo do job, conforme enum: 1: ENTRADA 2: SAÌDA 3: DISPENSAÇÃO','0', 'Tipo',10,'f','f','f',1,'text','Tipo');
            insert into db_syscampo values(1014670,'fa75_concluido','bool','Indica se o job já terminou.','f', 'Concluído',1,'f','f','f',5,'text','Concluído');

            insert into db_sysarqcamp values(1011011,1014667,1,0);
            insert into db_sysarqcamp values(1011011,1014668,2,0);
            insert into db_sysarqcamp values(1011011,1014669,3,0);
            insert into db_sysarqcamp values(1011011,1014670,4,0);

            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011011,1014667,1,1014667);
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            delete from db_sysprikey where codarq = 1011011;
            delete from db_sysarqcamp where codarq = 1011011;
            delete from db_syscampo where codcam in (1014667,1014668,1014669,1014670);
            delete from db_sysarqmod where codarq = 1011011;
            delete from db_sysarquivo where codarq = 1011011;
SQL
        );
    }
}
