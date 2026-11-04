<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26936SaudeParametroUsuarioApi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            ALTER TABLE ambulatorial.sau_config
                ADD COLUMN s103_usuarioapi INTEGER NOT NULL DEFAULT 1,
                ADD CONSTRAINT db_usuarios_sau_config_fk FOREIGN KEY (s103_usuarioapi) REFERENCES configuracoes.db_usuarios (id_usuario);

            SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                   'ambulatorial.sau_config.s103_usuarioapi',
                   '{ "descricao": "Usuário responsável pela API do CGS.",
                      "rotulo": "Usuário da API",
                      "rotulorel": "Usuário da API",
                      "maiusculo": false,
                      "autocompl": false,
                      "aceitatipo": 1,
                      "tamanho": 1,
                      "tipoobj": "text"
                    }') ;


            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

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
        DB::connection()->getPdo()->exec(<<<SQL

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            ALTER TABLE ambulatorial.sau_config DROP COLUMN s103_usuarioapi;

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

SQL
        );
    }
}
