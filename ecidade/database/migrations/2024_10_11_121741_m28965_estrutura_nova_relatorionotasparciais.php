<?php
/**
 * Nova migration para a tarefa de
 * relatório de notas parciais.
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28965EstruturaNovaRelatorionotasparciais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $this->upMenuRelatorioEspelhoAnual();
        $this->upMenuRelatorioEspelhoPeriodo();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $this->downMenuRelatorioEspelhoAnual();
        $this->downMenuRelatorioEspelhoPeriodo();
    }

    public function upMenuRelatorioEspelhoAnual()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu(id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente)
    values (3000315, 'Espelho Anual', 'Espelho Anual', 'edu2_espelhoanual.php', '1', '1', 'Relatório de Espelho Anual', 'true');
insert into db_menu(id_item, id_item_filho, menusequencia, modulo) values (9242, 3000315, 11, 1100747);
SQL
        );
    }

    public function upMenuRelatorioEspelhoPeriodo()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu(id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente)
        values (1985532, 'Espelho do Período', '#', 'edu2_espelhoperiodo001.php', '1', '1', 'Espelho do Período', 'true');
insert into db_menu(id_item, id_item_filho, menusequencia, modulo) values (9242, 1985532, 10, 1100747);
SQL
        );
    }

    public function downMenuRelatorioEspelhoAnual()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_menu where id_item_filho = 3000315;
delete from db_itensmenu where id_item = 3000315;
SQL
        );
    }

    public function downMenuRelatorioEspelhoPeriodo()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_itensmenu where id_item = 1985532;
delete from db_menu where id_item_filho = 1985532 AND modulo = 1100747;
SQL
        );
    }
}
