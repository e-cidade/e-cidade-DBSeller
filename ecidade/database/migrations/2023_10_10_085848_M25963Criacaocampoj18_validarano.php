<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25963Criacaocampoj18Validarano extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql =<<<SQL
            ALTER TABLE cadastro.cfiptu ADD j18_validarano bool NOT NULL DEFAULT false;
            COMMENT ON COLUMN cadastro.cfiptu.j18_validarano IS '{"descricao":"Validação do campo j39_ano","rotulo":"Validação do campo j39_ano","rotulorel":"Validação do campo j39_ano","maiusculo":false,"autocompl":false,"aceitatipo":5,"tipoobj":"text"}';
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
        DB::connection()->getPdo()->exec("ALTER TABLE cadastro.cfiptu DROP COLUMN j18_validarano;");
    }
}
