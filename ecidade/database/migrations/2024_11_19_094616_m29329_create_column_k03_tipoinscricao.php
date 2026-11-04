<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29329CreateColumnK03Tipoinscricao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
            ALTER TABLE caixa.numpref ADD COLUMN k03_tipoinscricao smallint;
            COMMENT ON COLUMN caixa.numpref.k03_tipoinscricao IS
            '{
                "descricao": "Vinculo nas procedencias nas inscricoes em cob ou divida",
                "rotulo": "Tipo Inscriчуo",
                "rotulorel": "Tipo de Inscricao",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text",
                "syscampodef": [
                    {
                    "defcampo": "1",
                    "defdescr": "Cobranca Administrativa"
                    },
                    {
                    "defcampo": "2",
                    "defdescr": "Divida Ativa"
                    }
                ]
            }';

            insert into db_syscampo values(1015684,'k03_tipoinscricao','int4','Vinculo nas procedencias nas inscricoes em cob ou divida','0', 'Tipo Inscriчуo',10,'t','f','f',1,'text','Tipo Inscriчуo');
            insert into db_sysarqcamp values(318,1015684,84,0);
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
        DB::unprepared(<<<SQL
            ALTER TABLE caixa.numpref DROP COLUMN k03_tipoinscricao;
            DELETE FROM db_sysarqcamp WHERE codcam = 1015684;
            DELETE FROM db_syscampo WHERE codcam = 1015684;
SQL
        );
    }
}
