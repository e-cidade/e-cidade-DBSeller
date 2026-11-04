<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25055AlterandoDescricaoItemMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(<<<SQL
UPDATE db_itensmenu set help = 'Prontuário Paciente',descricao = 'Prontuário Paciente',desctec= 'Prontuário Paciente' where id_item = 1045403;
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
UPDATE db_itensmenu set help = 'Prontuário Eletrônico',descricao = 'Prontuário Eletrônico',desctec= 'Prontuário Eletrônico' where id_item = 1045403;
SQL
);        
    }
}
