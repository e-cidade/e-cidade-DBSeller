<?php

use Illuminate\Database\Migrations\Migration;

class M27275RenomeadoCampoDbConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo set descricao = 'IBGE do município', rotulo = 'IBGE do município', rotulorel = 'IBGE do município' where codcam = 15412;
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
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo set descricao = 'Código do município no estado', rotulo = 'Código do município no estado', rotulorel = 'Código do município no estado' where codcam = 15412;
SQL
        );
    }
}
