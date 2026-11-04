<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M17704MenusLevantamentoEmLote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upMenu();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downMenu();
    }

    public function downMenu()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            DELETE FROM db_menu WHERE id_item = 228179;
            DELETE FROM db_menu WHERE id_item_filho = 228179;
            DELETE FROM db_itensmenu WHERE id_item BETWEEN 228179 AND 228183 ;

SQL
        );
    }


    public function upMenu()
    {
        DB::connection()->getPdo()->exec(<<<SQL

            INSERT INTO db_itensmenu (id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente)
                              VALUES (228179, 'Processamento em Lote', 'Processamento em Lote', null,
                                      1, 1, 'Processamento em Lote', 'true')
                ON CONFLICT ON CONSTRAINT db_itensmenu_item_pk DO NOTHING;

            INSERT INTO db_menu (id_item, id_item_filho, menusequencia, modulo)
                         VALUES (1818, 228179, 133, 229038)
                ON CONFLICT ON CONSTRAINT db_menu_item_item_filho_mod_pk DO NOTHING;

            INSERT INTO db_itensmenu (id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente)
                              VALUES (228180, 'Levantamento em Lote', 'Levantamento em Lote',
                                      'fis4_fis_levantamentolote001.php', 1, 1, 'fis4_fis_levantamentolote001.php', 'true')
                ON CONFLICT ON CONSTRAINT db_itensmenu_item_pk DO NOTHING;

            INSERT INTO db_menu (id_item, id_item_filho, menusequencia, modulo)
                         VALUES (228179, 228180, 1, 229038)
                ON CONFLICT ON CONSTRAINT db_menu_item_item_filho_mod_pk DO NOTHING;

            INSERT INTO db_itensmenu (id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente)
                              VALUES (228181, 'Andamento em Lote', 'Andamento em Lote', 'fis4_fis_andamentolote001.php',
                                      1, 1, 'fis4_fis_andamentolote001.php', 'true')
                ON CONFLICT ON CONSTRAINT db_itensmenu_item_pk DO NOTHING;

            INSERT INTO db_menu (id_item, id_item_filho, menusequencia, modulo)
                         VALUES (228179, 228181, 2, 229038)
                ON CONFLICT ON CONSTRAINT db_menu_item_item_filho_mod_pk DO NOTHING;

            INSERT INTO db_itensmenu (id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente)
                              VALUES (228182, 'Emissão em Lote', 'Emissão em Lote', 'fis4_fis_emissaolote001.php',
                                      1, 1, 'fis4_fis_emissaolote001', 'true')
                ON CONFLICT ON CONSTRAINT db_itensmenu_item_pk DO NOTHING;

            INSERT INTO db_menu (id_item, id_item_filho, menusequencia, modulo)
                         VALUES (228179, 228182, 3, 229038)
                ON CONFLICT ON CONSTRAINT db_menu_item_item_filho_mod_pk DO NOTHING;

            INSERT INTO db_itensmenu (id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente)
                              VALUES (228183, 'Reemissão do Processamento em Lote',
                                      'Reemissão do Processamento em Lote', 'fis4_fis_reemissaolote001.php',
                                      1, 1, 'fis4_fis_reemissaolote001', 'true')
                ON CONFLICT ON CONSTRAINT db_itensmenu_item_pk DO NOTHING;

            INSERT INTO db_menu (id_item, id_item_filho, menusequencia, modulo)
                         VALUES (228179, 228183, 4, 229038)
                ON CONFLICT ON CONSTRAINT db_menu_item_item_filho_mod_pk DO NOTHING;

SQL
        );
    }
}
