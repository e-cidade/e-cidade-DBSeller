<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28780CreateTableMacrozonas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            <<<SQL

        create sequence if not exists cadastro.macrozonas_j224_sequencial_seq;

        create table if not exists cadastro.macrozonas (
            j224_sequencial integer not null primary key default nextval('cadastro.macrozonas_j224_sequencial_seq'),
            j224_localizacao varchar(20) not null,
            j224_sigla varchar(10) not null,
            j224_descricao varchar(60)
        );
        
        select configuracoes.fc_auditoria_cria_funcao('cadastro.macrozonas');

        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

        SELECT fc_gera_dicionario_apartir_tabela('cadastro', 'macrozonas');

        COMMENT ON TABLE cadastro.macrozonas IS '{
          "descricao": "Macrozonas de desenvolvimento urbano",
          "sigla": "j224",
          "dataincl": "2024-11-11",
          "rotulo": "Macrozonas",
          "tipotabela": 0,
          "naolibclass": false,
          "naolibfunc": false,
          "naolibprog": false,
          "naolibform": false
        }';

        COMMENT ON COLUMN cadastro.macrozonas.j224_sequencial IS '{
          "descricao": "Sequencial",
          "rotulo": "Sequencial",
          "rotulorel": "Sequencial",
          "maiusculo": false,
          "autocompl": false,
          "aceitatipo": 1,
          "tamanho": 10,
          "tipoobj": "text"
        }';

        COMMENT ON COLUMN cadastro.macrozonas.j224_localizacao IS '{
          "descricao": "Localizaчуo",
          "rotulo": "Localizaчуo",
          "rotulorel": "Localizaчуo",
          "maiusculo": false,
          "autocompl": false,
          "aceitatipo": 3,
          "tamanho": 20,
          "tipoobj": "text"
        }';

        COMMENT ON COLUMN cadastro.macrozonas.j224_sigla IS '{
          "descricao": "Sigla da Macrozona",
          "rotulo": "Sigla",
          "rotulorel": "Sigla",
          "maiusculo": false,
          "autocompl": false,
          "aceitatipo": 3,
          "tamanho": 10,
          "tipoobj": "text"
        }';

        COMMENT ON COLUMN cadastro.macrozonas.j224_descricao IS '{
          "descricao": "Descriчуo da Macrozona",
          "rotulo": "Descriчуo",
          "rotulorel": "Descriчуo",
          "maiusculo": false,
          "autocompl": false,
          "aceitatipo": 3,
          "tamanho": 60,
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
        select configuracoes.fc_auditoria_remove_funcao('cadastro.macrozonas');
SQL
      );

        Schema::dropIfExists('cadastro.macrozonas');

        DB::unprepared(<<<SQL
        DROP SEQUENCE IF EXISTS cadastro.macrozonas_j224_sequencial_seq;
SQL
        );
    }
}
