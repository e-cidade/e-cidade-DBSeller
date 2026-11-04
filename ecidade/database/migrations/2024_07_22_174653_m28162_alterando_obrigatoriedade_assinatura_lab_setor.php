<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M28162AlterandoObrigatoriedadeAssinaturaLabSetor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * Alterar a obrigatoriedade do campo la24_o_assinatura 
     */
    public function up()
    {
        DB::statement(<<<SQL
UPDATE db_syscampo set nulo = 't' where codcam = 15798;
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
        DB::statement(<<<SQL
UPDATE db_syscampo set nulo = 'f' where codcam = 15798;
SQL
);
    }
}
