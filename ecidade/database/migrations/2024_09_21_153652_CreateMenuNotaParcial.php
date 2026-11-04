<?php

use Illuminate\Database\Migrations\Migration;

use App\Domain\Configuracao\Models\DBMenu;
use App\Domain\Configuracao\Models\DBItensmenu;
use Illuminate\Support\Facades\DB;

class CreateMenuNotaParcial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $itemNotas = DBItensmenu::where('id_item', 1985531)->first();

        if (!$itemNotas) {
            DB::connection()->getPdo()->exec(<<<SQL
                INSERT INTO configuracoes.db_itensmenu 
                    (
                        id_item,
                        descricao,
                        help,
                        funcao,
                        itemativo,
                        manutencao,
                        desctec,
                        libcliente,
                        api
                    ) VALUES (
                        1985531,
                        'Notas Parciais',
                        '#',
                        'edu4_notasparciais001.php',
                        1,
                        '1',
                        'Tela inicial para lançamento de notas parciais',
                        true,
                        false
                    );
SQL
);
        }

        $itemMenu = DBMenu::where('id_item_filho', 1985531)->get();

        if ($itemMenu->count() <= 0) {
            DB::connection()->getPdo()->exec(<<<SQL
                INSERT INTO configuracoes.db_menu
                    (
                        id_item,
                        id_item_filho,
                        menusequencia,
                        modulo
                    ) VALUES (
                        1100930,
                        1985531,
                        8,
                        1100747
                    );
SQL
);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
