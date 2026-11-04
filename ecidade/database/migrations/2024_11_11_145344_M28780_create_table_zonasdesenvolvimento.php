<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28780CreateTableZonasdesenvolvimento extends Migration
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

        create sequence if not exists cadastro.zonasdesenvolvimento_j225_sequencial_seq;
        create table if not exists cadastro.zonasdesenvolvimento (
            j225_sequencial integer not null primary key default nextval('cadastro.zonasdesenvolvimento_j225_sequencial_seq'),
            j224_sequencial integer not null,
            j225_sigla varchar(10) not null,
            j225_descricao varchar(60),
            foreign key (j224_sequencial) references cadastro.macrozonas (j224_sequencial)
        );
        
        select configuracoes.fc_auditoria_cria_funcao('cadastro.zonasdesenvolvimento');

        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

        SELECT fc_gera_dicionario_apartir_tabela('cadastro', 'zonasdesenvolvimento');

        COMMENT ON TABLE cadastro.zonasdesenvolvimento IS '{
          "descricao": "Zona de desenvolvimento urbano",
          "sigla": "j225",
          "dataincl": "2024-11-11",
          "rotulo": "Zona",
          "tipotabela": 0,
          "naolibclass": false,
          "naolibfunc": false,
          "naolibprog": false,
          "naolibform": false
        }';

        COMMENT ON COLUMN cadastro.zonasdesenvolvimento.j225_sequencial IS '{
          "descricao": "Sequencial",
          "rotulo": "Sequencial",
          "rotulorel": "Sequencial",
          "maiusculo": false,
          "autocompl": false,
          "aceitatipo": 1,
          "tamanho": 10,
          "tipoobj": "text"
        }';

        COMMENT ON COLUMN cadastro.zonasdesenvolvimento.j224_sequencial IS '{
          "descricao": "Sequencial Macrozona",
          "rotulo": "Sequencial Macrozona",
          "rotulorel": "Sequencial Macrozona",
          "maiusculo": false,
          "autocompl": false,
          "aceitatipo": 1,
          "tamanho": 10,
          "tipoobj": "text"
        }';

        COMMENT ON COLUMN cadastro.zonasdesenvolvimento.j225_sigla IS '{
          "descricao": "Sigla da Zona",
          "rotulo": "Sigla",
          "rotulorel": "Sigla",
          "maiusculo": false,
          "autocompl": false,
          "aceitatipo": 3,
          "tamanho": 10,
          "tipoobj": "text"
        }';

        COMMENT ON COLUMN cadastro.zonasdesenvolvimento.j225_descricao IS '{
          "descricao": "Descriчуo da Zona",
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
        select configuracoes.fc_auditoria_remove_funcao('cadastro.zonasdesenvolvimento');
SQL
    );

        Schema::dropIfExists('cadastro.zonasdesenvolvimento');

        DB::unprepared(<<<SQL
        DROP SEQUENCE IF EXISTS cadastro.zonasdesenvolvimento_j225_sequencial_seq;
SQL
        );
    }
}
