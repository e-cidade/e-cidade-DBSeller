<?php
/*
 * Essa migration adiciona a rotina de 'Tipo de Ausencia' para
 * o cliente Nova Friburgo, caso rode essa Migration em uma
 * base que já possui ela (que é o caso de Nova Friburgo),
 * recomendo ignorar o up() com um return;
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29003AlteracaoDeRotinaTipoAusencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Cria o novo menu, sem apagar o menu antigo, mas
         * alterando o id do acesso para o cliente
         */
        DB::connection()->getPdo()->exec(<<<SQL
        insert into db_itensmenu(id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente)
        values (3000401, 'Inclusão', '#', 'edu1_af_tipoausencia_inclusao.php', '1', '1', 'Cadastro de Tipo de Ausências, Substituições e Extras', 'true'),
            (3000402, 'Alteração / Exclusão', '#', 'edu1_af_tipoausencia.php', '1', '1', 'Edição e Consultas do Tipo de Ausências, Substituições e Extras', 'true');

        delete from db_menu where id_item_filho in (9618, 9619, 9620);

        insert into db_menu(id_item, id_item_filho, menusequencia, modulo)
        values (9617, 3000401, 1, 7159),
            (9617, 3000402, 2, 7159);
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
        /**
         * Devolve o id da Página anterior, e deleta a conexão
         * com as rotinas novas
         */
        DB::connection()->getPdo()->exec(<<<SQL
        insert into db_menu(id_item, id_item_filho, menusequencia, modulo)
        values (9617, 9618, 1, 7159),
            (9617, 9619, 2, 7159),
            (9617, 9620, 3, 7159);

        delete from db_menu where id_item_filho in (3000401, 3000402);
        delete from db_itensmenu where id_item in (3000401, 3000402);
SQL
        );
    }
}
