<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26038AlteracaoLabelEd217Exibecargahoraria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo set rotulo = 'Exibir Porcentagem da Frequência', rotulorel = 'Exibir Porcentagem da Frequência' where codcam = 20293;
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
update db_syscampo set rotulo = 'Exibe Carga Horária', rotulorel = 'Exibe Carga Horária' where codcam = 20293;
SQL
        );
    }
}
