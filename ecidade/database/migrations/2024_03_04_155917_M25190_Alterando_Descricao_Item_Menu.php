<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25190AlterandoDescricaoItemMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
DB::statement(<<<SQL
UPDATE db_itensmenu set help = 'Produtividade Profissional',descricao = 'Produtividade Profissional',desctec= 'Produtividade Profissional' where id_item = 1101038;
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
UPDATE db_itensmenu set help = 'Produtividade Médica',descricao = 'Produtividade Médica',desctec= 'Produtividade Médica' where id_item = 1101038;
SQL
);             
    }
}
