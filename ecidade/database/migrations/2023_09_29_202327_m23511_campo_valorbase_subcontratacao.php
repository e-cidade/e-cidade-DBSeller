<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23511CampoValorbaseSubcontratacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            ALTER TABLE empenho.retencaoreceitassubcontratacao ADD e163_valorbase numeric(8, 2) NOT NULL DEFAULT 0;
            COMMENT ON COLUMN empenho.retencaoreceitassubcontratacao.e163_valorbase IS '{"descricao":"valor base","rotulo":"valor base","rotulorel":"valor base","maiusculo":false,"autocompl":false,"aceitatipo":0,"tipoobj":"text"}';

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

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
        DB::statement('ALTER TABLE retencaoreceitassubcontratacao DROP COLUMN e163_valorbase');
    }
}
