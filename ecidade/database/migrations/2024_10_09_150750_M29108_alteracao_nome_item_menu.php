<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M29108AlteracaoNomeItemMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("update db_itensmenu set id_item = 229279 , descricao = 'Monitoramento de jobs' , help = 'Monitoramento de jobs' , funcao = 'web/configuracao/jobs' , itemativo = '1' , manutencao = '1' , desctec = 'Monitoramento e gerenciamento de jobs em fila e jobs falhados.' , libcliente = 'true' , api = 'false' where id_item = 229279;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("update db_itensmenu set id_item = 229279 , descricao = 'Manutenção de falhas de jobs' , help = 'Manutenção de falhas de jobs' , funcao = 'web/configuracao/jobs/failed' , itemativo = '1' , manutencao = '1' , desctec = 'Consulta de jobs falhado, reprocessamento e exclusão' , libcliente = 'true' , api = 'false' where id_item = 229279;");
    }
}
