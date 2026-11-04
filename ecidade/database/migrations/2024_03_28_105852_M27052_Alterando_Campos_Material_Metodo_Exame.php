<?php

use Illuminate\Database\Migrations\Migration;

class M27052AlterandoCamposMaterialMetodoExame extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
DB::unprepared(<<<SQL
update db_syscampo set maiusculo = 'f' where nomecam = 'la15_c_descr';
update db_syscampo set maiusculo = 'f' where nomecam = 'la11_c_descr';
update db_syscampo set maiusculo = 'f' where nomecam = 'la08_c_descr';
update db_syscampo set maiusculo = 'f' where nomecam = 'la08_observacao';
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
DB::unprepared(<<<SQL
update db_syscampo set maiusculo = 't' where nomecam = 'la15_c_descr';
update db_syscampo set maiusculo = 't' where nomecam = 'la11_c_descr';
update db_syscampo set maiusculo = 't' where nomecam = 'la08_c_descr';
update db_syscampo set maiusculo = 't' where nomecam = 'la08_observacao';
SQL
        );
    }
}
