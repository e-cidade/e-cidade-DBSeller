<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25330FixTarefaProtelacoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("DELETE FROM migrations where migration in ('2023_11_03_090726_m25330_fix_tarefa_protelacoes', '2023_07_28_115008_M25070_novo_campo_instituicao_tabela_protelac');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
