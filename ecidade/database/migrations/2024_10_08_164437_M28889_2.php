<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M288892 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $column = 'cadastro.ruascep.j29_lado';
        $json = '{
            "descricao":"Campo referente a descriчуo do lado da rua: 1 - PAR, 2 - IMPAR, 3 - AMBOS",
            "rotulo":"Lado",
            "rotulorel":"Lado",
            "maiusculo":true,
            "autocompl":false,
            "aceitatipo":0,
            "tipoobj":"text",
            "syscampodef":[
                {"defcampo":"1","defdescr":"PAR"},
                {"defcampo":"2","defdescr":"IMPAR"},
                {"defcampo":"3","defdescr":"Ambos"}
            ]
        }';

        DB::unprepared("
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            ALTER TABLE ruascep ADD COLUMN j29_lado INT;
            comment on column $column is '$json';

            select fc_gera_dicionario_apartir_tabela('cadastro','ruascep');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("
            ALTER TABLE ruascep DROP COLUMN j29_lado;
        ");
    }
}
