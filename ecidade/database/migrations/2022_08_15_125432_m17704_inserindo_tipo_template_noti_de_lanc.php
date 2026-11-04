<?php

use Illuminate\Database\Migrations\Migration;

class M17704InserindoTipoTemplateNotiDeLanc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            insert into configuracoes.db_documentotemplatetipo values (6000, 'Notificação De Lançamento');
SQL;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
            delete from configuracoes.db_documentotemplatetipo where db80_sequencial = 6000;
SQL;
        DB::statement($sql);
    }
}
