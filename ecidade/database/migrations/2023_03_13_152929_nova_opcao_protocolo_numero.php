<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NovaOpcaoProtocoloNumero extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            INSERT INTO db_syscampodef  (codcam,defcampo,defdescr) VALUES (18213,4,'Sequencial do Ano Com Sigla');
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
            DELETE FROM  db_syscampodef WHERE codcam = 18213 AND defcampo = '4';
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
