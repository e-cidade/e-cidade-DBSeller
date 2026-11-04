<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28969Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu(id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente)
    values (3000335, 'Ata de Resultados Finais - SIGA', '#', 'edu2_ataresultadosfinaissiga.php', '1', '1', 'Ata de Resultados Finais Turmas de SIGA (Correção de Fluxo)', 'true');

insert into db_menu(id_item, id_item_filho, menusequencia, modulo) values (1101110, 3000335, 40, 1100747);
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
delete from db_menu where id_item_filho = 3000335 AND modulo = 1100747;
delete from db_itensmenu where id_item = 3000335;
SQL
        );
    }
}
