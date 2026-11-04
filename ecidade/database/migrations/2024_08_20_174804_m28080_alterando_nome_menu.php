<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M28080AlterandoNomeMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
DB::unprepared(<<<SQL
update db_itensmenu set descricao = 'Lista de CGSs Unificados' , help = 'Lista de CGSs Unificados',desctec = 'Lista de CGSs Unificados' where id_item = 8075;
update db_itensmenu set descricao = 'Geral CGSs' , help = 'Relatório geral CGSs',desctec = 'Relatório geral dos CGSs' where id_item = 8464;
update db_menu set menusequencia = 1 where id_item_filho = 8074 and id_item = 229297;
update db_menu set menusequencia = 2 where id_item_filho = 8464 and id_item = 229297;
update db_menu set menusequencia = 3 where id_item_filho = 8075 and id_item = 229297;
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
update db_itensmenu set descricao = 'CGS Corretos' , help = 'CGS Corretos',desctec = 'CGS Corretos' where id_item = 8075;
update db_itensmenu set descricao = 'Geral CGS' , help = 'Relatório geral CGS',desctec = 'Relatório geral dos CGS' where id_item = 8464;
update db_menu set menusequencia = 1 where id_item_filho = 8075 and id_item = 229297;
update db_menu set menusequencia = 2 where id_item_filho = 8074 and id_item = 229297;
update db_menu set menusequencia = 3 where id_item_filho = 8464 and id_item = 229297;
SQL
        );
    }
}
