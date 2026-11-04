<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28685AjusteItemMenuMensageriaAcordo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
            update  configuracoes.db_itensmenu set  funcao = 'web/patrimonial/contratos/mensageria-acordos-config' where id_item = 9947;
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
        DB::connection()->getPdo()->exec(
            <<<SQL
            update  configuracoes.db_itensmenu set funcao = 'con4_acordovencer001.php' where id_item = 9947;
SQL
        );
    }
}
