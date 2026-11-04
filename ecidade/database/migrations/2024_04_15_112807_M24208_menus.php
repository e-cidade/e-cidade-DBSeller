<?php

use Illuminate\Database\Migrations\Migration;

class M24208Menus extends Migration
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
values (229256, 'Decêndio', 'Decêndio', '', '1', '1', 'Decêndio', 'true'),
       (229257, 'Receitas Configurar', 'Receitas Configurar', 'web/financeiro/tesouraria/decendio/receita', '1', '1', 'Receitas Configurar', 'true'),
       (229264, 'Receitas Configurar por Desdobramento', 'Receitas Configurar por Desdobramento', 'web/financeiro/tesouraria/decendio/receita-por-desdobramento', '1', '1', 'Receitas Configurar por Desdobramento', 'true'),
       (229258, 'Percentuais', 'Percentuais', 'web/financeiro/tesouraria/decendio/percentuais', '1', '1', 'Percentuais', 'true'),
       (229259, 'Decêndio', 'Decêndio', 'web/financeiro/tesouraria/decendio/gerar-slips', '1', '1', 'Decêndio', 'true');

insert into db_menu(id_item, id_item_filho, menusequencia, modulo)
values (5383, 229256, 9, 39),
       (229256, 229257, 1, 39),
       (229256, 229264, 2, 39),
       (229256, 229258, 3, 39),
       (7791, 229259, 6, 39);
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
delete from db_menu where id_item_filho in (229256, 229257, 229264, 229258, 229259);
delete from db_itensmenu where id_item in (229256, 229257, 229264, 229258, 229259);
SQL
        );
    }
}
