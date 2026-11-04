<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24577AlteraItemMenuGeradorRelatorio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("update db_itensmenu set descricao = 'Gerador de Relatórios (Descontinuado)' , help = 'Gerador de Relatórios (Descontinuado)' , desctec = 'Gerador de Relatórios (Descontinuado)' where id_item = 6925;");
        DB::statement("update db_itensmenu set funcao = 'web/configuracao/relatorios/gerador' where id_item = 1294;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("update db_itensmenu set descricao = 'Gerador de Relatórios (Novo)' , help = 'Gerador de Relatórios (Novo)' , desctec = 'Gerador de Relatórios (Novo)' where id_item = 6925;");
        DB::statement("update db_itensmenu set funcao = 'con2_gerelatorio001' where id_item = 1294;");
    }
}
