<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23669RemoveMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_menu where modulo = 398 and id_item_filho in (3935, 3936, 3937, 3934);
delete from db_itensfilho where id_item in (3935, 3936, 3937, 3934);
delete from db_itensmenu where id_item in (3935, 3936, 3937, 3934);
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
insert into db_itensmenu (id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente)
values  (3934, 'Cadastro de restos a pagar', 'Cadastro de restso a pagar', '', 1, 1, 'Cadastro de restos a pagar.', 'f'),
        (3937, 'Exclusão', 'Exclusão de Empresto', 'emp1_empresto003.php', 1, 1, 'Exclusão de Empresto', 'f'),
        (3935, 'Inclusão', 'Inclusão de Empresto', 'emp1_empresto001.php', 1, 1, 'Inclusão de Empresto', 'f'),
        (3936, 'Alteração', 'Alteração de Empresto', 'emp1_empresto002.php', 1, 1, 'Alteração de Empresto', 'f');

insert into db_menu
values (  29, 3934, 4, 398),
       (3934, 3935, 1, 398),
       (3934, 3936, 2, 398),
       (3934, 3937, 3, 398);
SQL
        );
    }
}
