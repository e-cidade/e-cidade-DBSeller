<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23747AlteraNomeRotina extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_itensmenu set id_item = 228816 , descricao = 'Contratação/Edital/Aviso' , help = 'Contratação/Edital/Aviso' , itemativo = '1' , manutencao = '1' , desctec = 'Contratação/Edital/Aviso PNCP' , libcliente = 'true' where id_item = 228816;
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
update db_itensmenu set id_item = 228816 , descricao = 'Compra/Edital/Aviso' , help = 'Contratação/Edital/Aviso' , itemativo = '1' , manutencao = '1' , desctec = 'Contratação/Edital/Aviso PNCP' , libcliente = 'true' where id_item = 228816;
SQL
        );
    }
}
