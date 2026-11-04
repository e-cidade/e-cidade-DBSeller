<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29260CreateAnexositbi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::unprepared(<<<SQL
            CREATE TABLE itbi.anexositbi (
                it39_sequencial SERIAL NOT NULL,
                it39_itbi integer,
                it39_usuario integer,
                it39_arquivo varchar(300),
                it39_idstorage integer,
                it39_descricao varchar(300),
                it39_observacao text,
                it39_data timestamp,
                it39_usuarioexclusao integer,
                it39_dataexclusao timestamp,
                
                PRIMARY KEY (it39_sequencial),
                FOREIGN KEY (it39_itbi) REFERENCES itbi.itbi (it01_guia),
                FOREIGN KEY (it39_usuario) REFERENCES configuracoes.db_usuarios(id_usuario),
                FOREIGN KEY (it39_usuarioexclusao) REFERENCES configuracoes.db_usuarios(id_usuario)
            );
SQL
        );
        DB::unprepared(<<<SQL

            select configuracoes.fc_auditoria_cria_funcao('itbi.anexositbi');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            SELECT fc_gera_dicionario_apartir_tabela('itbi', 'anexositbi');

            COMMENT ON TABLE itbi.anexositbi IS
            '{
                "descricao": "Anexos ITBI",
                "sigla": "it39",
                "dataincl": "2024-11-28",
                "rotulo": "Anexos ITBI",
                "tipotabela": 2,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }';

            COMMENT ON COLUMN itbi.anexositbi.it39_sequencial IS
            '{
                "descricao": "Sequencial da tabela",
                "rotulo": "Sequencial Anexo ITBI",
                "rotulorel": "Sequencial Anexo ITBI",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';



            COMMENT ON COLUMN itbi.anexositbi.it39_arquivo IS 
            '{
              "descricao": "Arquivo Importado",
              "rotulo": "Arquivo Importado",
              "rotulorel": "Arquivo Importado",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 0,
              "tamanho": 300,
              "tipoobj": "text"
            }';

            COMMENT ON COLUMN itbi.anexositbi.it39_idstorage IS
            '{
                "descricao": "ID Storage",
                "rotulo": "ID Storage",
                "rotulorel": "ID Storage",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN itbi.anexositbi.it39_descricao IS 
            '{
              "descricao": "Descriчуo do Arquivo",
              "rotulo": "Descriчуo do Arquivo",
              "rotulorel": "Descriчуo do Arquivo",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 0,
              "tamanho": 300,
              "tipoobj": "text"
            }';

            COMMENT ON COLUMN itbi.anexositbi.it39_observacao IS 
            '{
              "descricao": "Observaчуo Arquivo",
              "rotulo": "Observaчуo Arquivo",
              "rotulorel": "Observaчуo Arquivo",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 0,
              "tamanho": 300,
              "tipoobj": "text"
            }';

            COMMENT ON COLUMN itbi.anexositbi.it39_data IS 
            '{
              "descricao": "Data Upload",
              "rotulo": "Data Upload",
              "rotulorel": "Data Upload",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 0,
              "tamanho": 10,
              "tipoobj": "text"
            }';

            COMMENT ON COLUMN itbi.anexositbi.it39_usuario IS
            '{
                "descricao": "Usuсrio",
                "rotulo": "Usuсrio upload",
                "rotulorel": "Usuсrio upload",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN itbi.anexositbi.it39_itbi IS
            '{
                "descricao": "Guia ITBI",
                "rotulo": "Guia ITBI",
                "rotulorel": "Guia ITBI",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN itbi.anexositbi.it39_dataexclusao IS 
            '{
              "descricao": "Data Exclusуo",
              "rotulo": "Data Exclusуo",
              "rotulorel": "Data Exclusуo",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 0,
              "tamanho": 10,
              "tipoobj": "text"
            }';

            COMMENT ON COLUMN itbi.anexositbi.it39_usuarioexclusao IS
            '{
                "descricao": "Usuсrio Exclusуo",
                "rotulo": "Usuсrio Exclusуo upload",
                "rotulorel": "Usuсrio Exclusуo upload",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

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
        DB::unprepared(<<<SQL
        select configuracoes.fc_auditoria_remove_funcao('itbi.anexositbi');
SQL
    );
        Schema::dropIfExists('itbi.anexositbi');
    }
}
