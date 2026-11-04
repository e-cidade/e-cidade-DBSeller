<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23616AdicionandoCampoSituacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_motivoatendimento', function (Blueprint $table) {
            $table->boolean('s144_situacao')->default(true);
        });

        DB::connection()->getPdo()->exec(<<<SQL

            insert into db_syscampo values(1015036,'s144_situacao','bool','Campo para indicar se o motivo do atendimento está ativo.','t', 'Ativo',1,'f','f','f',5,'text','Ativo');
            insert into db_sysarqcamp values(2719,1015036,3,0);

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
        Schema::table('sau_motivoatendimento', function (Blueprint $table) {
            $table->dropColumn('s144_situacao');
        });

        DB::connection()->getPdo()->exec(<<<SQL
            delete from db_sysarqcamp where codcam = 1015036;
            delete from db_syscampo where codcam = 1015036;
SQL
        );
    }
}
