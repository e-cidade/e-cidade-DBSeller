<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28224CriacaoItensMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu values (
    229268, 
'Demonstrativo Dívida Flutuante(Anexo 17) - Novo', 
'Demonstrativo Dívida Flutuante(Anexo 17) - Novo', 
'con2_anexoXVII.php', 
1, 
1, 
'Demonstrativo Dívida Flutuante(Anexo 17) - Novo', 't');

insert into db_menu values (3392, 229268, 12, 209);                
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
delete from db_menu where id_item_filho = 229268;
delete from db_itensmenu where id_item = 229268;
SQL
        );
    }
}
