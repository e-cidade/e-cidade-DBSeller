<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23123AlteraTabelaMovimentacaoprontuario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movimentacaoprontuario', function (Blueprint $table) {
            $table->integer('sd102_profissionalencaminhado')->nullable();
            $table->integer('sd102_profissionalatendimento')->nullable();

            $table->foreign('sd102_profissionalencaminhado')->references('sd27_i_codigo')->on('especmedico');
            $table->foreign('sd102_profissionalatendimento')->references('sd27_i_codigo')->on('especmedico');
        });

        DB::connection()->getPdo()->exec(<<<SQL
        insert into db_syscampo values(1014765,'sd102_profissionalencaminhado','int4','Profissional indicado para realizar o atendimento do paciente.','0', 'Profissional Encaminhado',10,'t','f','f',1,'text','Profissional Encaminhado');
        insert into db_syscampo values(1014766,'sd102_profissionalatendimento','int4','Profissional que está atendendo o(a) paciente.','0', 'Profissional Atendimento',10,'t','f','f',1,'text','Profissional Atendimento');
        insert into db_sysarqcamp values(3773,1014766,9,0);
        insert into db_sysarqcamp values(3773,1014765,10,0);
        insert into db_sysforkey values(3773,1014766,1,100026,0);
        insert into db_sysforkey values(3773,1014765,2,100026,0);
SQL
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movimentacaoprontuario', function (Blueprint $table) {
            $table->dropColumn('sd102_profissionalencaminhado');
            $table->dropColumn('sd102_profissionalatendimento');
        });

        DB::connection()->getPdo()->exec(<<<SQL
            delete from db_sysforkey where codcam in (1014765, 1014766);
            delete from db_sysarqcamp where codcam in (1014765, 1014766);
            delete from db_syscampo where codcam in (1014765, 1014766);
SQL
        );
    }
}
