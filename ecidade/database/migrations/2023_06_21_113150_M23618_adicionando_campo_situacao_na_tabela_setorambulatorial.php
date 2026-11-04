<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23618AdicionandoCampoSituacaoNaTabelaSetorambulatorial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setorambulatorial', function (Blueprint $table) {
            $table->boolean('sd91_situacao')->default(true);
        });

        DB::connection()->getPdo()->exec(<<<SQL

            insert into db_syscampo values(1015199,'sd91_situacao','bool','Campo para indicar se o setor ambulatorial está ativo.','f', 'Ativo',1,'f','f','f',5,'text','Ativo');
            insert into db_sysarqcamp values(3772,1015199,5,0);


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
        Schema::table('setorambulatorial', function (Blueprint $table) {
            $table->dropColumn('sd91_situacao');
        });

        DB::connection()->getPdo()->exec(<<<SQL

            delete from db_sysarqcamp where codcam = 1015199;
            delete from db_syscampo where codcam = 1015199;

SQL
        );
    }
}
