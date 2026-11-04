<?php

use Illuminate\Database\Migrations\Migration;

class M28711AlterandoCampaParaMista extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $column = 'cemiterio.quadracemit.cm22_c_tipo';
        $json = '{
            "descricao":"Tipo de quadra: O - Ossoario J - jazigo M - Mista S - Sepultura",
            "rotulo":"Tipo",
            "rotulorel":"Tipo",
            "maiusculo":true,
            "autocompl":false,
            "aceitatipo":0,
            "tipoobj":"text",
            "syscampodef":[
                {"defcampo":"M","defdescr":"Mista"},
                {"defcampo":"J","defdescr":"Jazigo"},
                {"defcampo":"O","defdescr":"Ossoario"},
                {"defcampo":"S","defdescr":"Sepultura"}
            ]
        }';

        DB::unprepared("
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            comment on column $column is '$json';
            select fc_atualiza_dicionario_apartir_comentario('table column', '$column', '$json');

            update cemiterio.quadracemit set cm22_c_tipo = 'M' where cm22_c_tipo = 'C';

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
        $column = 'cemiterio.quadracemit.cm22_c_tipo';
        $json = '{
            "descricao":"Tipo de quadra: O - Ossoario J - jazigo C - Campa S - Sepultura",
            "rotulo":"Tipo",
            "rotulorel":"Tipo",
            "maiusculo":true,
            "autocompl":false,
            "aceitatipo":0,
            "tipoobj":"text",
            "syscampodef":[
                {"defcampo":"C","defdescr":"Campa"},
                {"defcampo":"J","defdescr":"Jazigo"},
                {"defcampo":"O","defdescr":"Ossoario"},
                {"defcampo":"S","defdescr":"Sepultura"}
            ]
        }';

        DB::unprepared("
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            comment on column $column is '$json';
            select fc_atualiza_dicionario_apartir_comentario('table column', '$column', '$json');

            update cemiterio.quadracemit set cm22_c_tipo = 'C' where cm22_c_tipo = 'M';

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
        ");
    }
}
