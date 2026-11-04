<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25897NivelamentoCriacaoMenuProcessamentoProtelacoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
INSERT INTO db_itensmenu(id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente)
SELECT 228974, 'Processamento Protelações', 'Processamento Protelações', 'rec1_processa_protelacoes001.php', '1', '1', 'Processa as Protelações por Competência', 'true'
WHERE NOT EXISTS (
    SELECT 1 FROM db_itensmenu WHERE id_item = 228974
);

INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo)
SELECT 5567, 228974, 4, 2323
WHERE NOT EXISTS (
    SELECT 1 FROM db_menu WHERE id_item = 5567 AND id_item_filho = 228974
);
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
DELETE FROM db_menu where id_item_filho = 228974 AND modulo = 2323;
DELETE FROM db_itensmenu where id_item = 228974;
SQL
        );
    }
}
