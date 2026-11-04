<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriaColunaSolicitacaoComprasPncp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec("
            ALTER TABLE compraspncp ALTER COLUMN pn03_liclicita DROP NOT NULL;
            ALTER TABLE compraspncp ADD COLUMN pn03_solicita INTEGER;
            ALTER TABLE compraspncp ADD FOREIGN KEY (pn03_solicita) REFERENCES solicita(pc10_numero);
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec("
            ALTER TABLE compraspncp ALTER COLUMN pn03_liclicita SET NOT NULL;
            ALTER TABLE compraspncp DROP COLUMN pn03_solicita;
            ALTER TABLE compraspncp DROP CONSTRAINT pn03_solicita;
        ");
    }
}
