<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28616CriandoTabelaExameToxicologico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upTabela();
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downTabela();
    }

    private function upTabela()
    {
        Schema::create('esocial.exametoxicologico', function (Blueprint $table) {
            $table->bigIncrements('eso41_sequencial');
            $table->string('eso41_cpftrab',11)->nullable();
            $table->integer('eso41_matricula');
            $table->date('eso41_dtexame')->nullable();
            $table->string('eso41_cnpjlab',14)->nullable();
            $table->string('eso41_codseqexame',11)->nullable();
            $table->string('eso41_nmmed',70)->nullable();
            $table->string('eso41_nrcrm',10)->nullable();
            $table->string('eso41_ufcrm',2)->nullable();
            $table->integer('eso41_instit');

            $table->foreign('eso41_matricula')
                ->references('rh01_regist')
                ->on('pessoal.rhpessoal');

            $table->foreign('eso41_instit')
                ->references('codigo')
                ->on('configuracoes.db_config');

        });
        DB::table('recursoshumanos.esocialformulariotipo')
            ->insert([
                'rh209_sequencial' => 57,
                'rh209_descricao' => 'S-2221 - Exame Toxicológico do Motorista Profissional Empregado'
                ]);

        DB::table('habitacao.avaliacao')
            ->insert([
                'db101_sequencial' => 4000126,
                'db101_avaliacaotipo' => 5,
                'db101_descricao' => 'S-2221 - Exame Toxicológico do Motorista Profissional Empregado',
                'db101_obs' => 'S-2221 - Exame Toxicológico do Motorista Profissional Empregado',
                'db101_ativo' => true,
                'db101_identificador' => 's2221_exame_toxicologico',
                'db101_permiteedicao' => false
            ]);

        DB::table('recursoshumanos.esocialversaoformulario')
            ->insert([
                'rh211_sequencial' => DB::raw("nextval('recursoshumanos.esocialversaoformulario_rh211_sequencial_seq')"),
                'rh211_versao' => 'S1.3',
                'rh211_avaliacao' => 4000126,
                'rh211_esocialformulariotipo' => 57
            ]);

    }

    private function upDicionario()
    {
        $sql = <<<SQL
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

        COMMENT ON TABLE esocial.exametoxicologico IS
            '{"descricao": "Tabela de cadastro dos Exames Toxicologicos dos motoristas profissionais",
              "sigla": "eso41",
              "dataincl": "2024-10-25",
              "rotulo": "exametoxicologico",
              "tipotabela": "0",
              "naolibclass": "false",
              "naolibfunc": "false",
              "naolibprog": "false",
              "naolibform": "false"
             }';

        COMMENT ON COLUMN esocial.exametoxicologico.eso41_sequencial IS
             '{"descricao": "Sequencial do exame toxicologico",
                "rotulo": "Sequencial do exame toxicologico",
                "rotulorel": "Sequencial do exame toxicologico",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 20,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN esocial.exametoxicologico.eso41_cpftrab IS
             '{"descricao": "CPF do trabalhador",
                "rotulo": "CPF do trabalhador",
                "rotulorel": "CPF do trabalhador",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 11,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN esocial.exametoxicologico.eso41_matricula IS
             '{"descricao": "Matrícula do trabalhador",
                "rotulo": "Matrícula do trabalhador",
                "rotulorel": "Matrícula do trabalhador",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN esocial.exametoxicologico.eso41_dtexame IS
             '{"descricao": "Data do exame",
                "rotulo": "Data do exame",
                "rotulorel": "Data do exame",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN esocial.exametoxicologico.eso41_cnpjlab IS
             '{"descricao": "CNPJ do laboratório",
                "rotulo": "CNPJ do laboratório",
                "rotulorel": "CNPJ do laboratório",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 14,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN esocial.exametoxicologico.eso41_codseqexame IS
             '{"descricao": "Código sequencial do exame",
                "rotulo": "Código sequencial do exame",
                "rotulorel": "Código sequencial do exame",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 11,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN esocial.exametoxicologico.eso41_nmmed IS
             '{"descricao": "Nome do médico",
                "rotulo": "Nome do médico",
                "rotulorel": "Nome do médico",
                "maiusculo": true,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 70,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN esocial.exametoxicologico.eso41_nrcrm IS
             '{"descricao": "Número do CRM do médico",
                "rotulo": "Número do CRM do médico",
                "rotulorel": "Número do CRM do médico",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN esocial.exametoxicologico.eso41_ufcrm IS
             '{"descricao": "UF do CRM do médico",
                "rotulo": "UF do CRM do médico",
                "rotulorel": "UF do CRM do médico",
                "maiusculo": true,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 2,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN esocial.exametoxicologico.eso41_instit IS
             '{"descricao": "Código da Instituição",
                "rotulo": "Código da Instituição",
                "rotulorel": "Código da Instituição",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

        SELECT fc_gera_dicionario_apartir_tabela('esocial', 'exametoxicologico');

        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

SQL;


        DB::connection()->getPdo()->exec($sql);
    }

    private function downTabela()
    {
        Schema::drop('esocial.exametoxicologico');
        DB::table('recursoshumanos.esocialversaoformulario')
            ->where('rh211_versao', 'S1.3')
            ->where('rh211_avaliacao', 4000126)
            ->where('rh211_esocialformulariotipo', 57)
            ->delete();


        DB::table('habitacao.avaliacao')
            ->where('db101_sequencial', 4000126)
            ->where('db101_avaliacaotipo', 5)
            ->delete();

        DB::table('recursoshumanos.esocialformulariotipo')
            ->where('rh209_sequencial', 57)
            ->delete();

    }
}
