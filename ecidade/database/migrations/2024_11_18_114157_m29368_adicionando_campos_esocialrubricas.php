<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29368AdicionandoCamposEsocialrubricas extends Migration
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
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE');
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE');
        Schema::table('esocial.esocialrubricas', function (Blueprint $table) {
            $table->integer('eso26_tpproc')->nullable();
            $table->string('eso26_nrprocprocessocp', 21)->nullable();
            $table->integer('eso26_extdecisao')->nullable();
            $table->string('eso26_codsuspprocessocp', 14)->nullable();
            $table->string('eso26_nrprocirrf', 20)->nullable();
            $table->string('eso26_codsuspirrf', 14)->nullable();
            $table->string('eso26_nrprocfgts', 20)->nullable();
            $table->string('eso26_nrprocpispasep', 20)->nullable();
            $table->string('eso26_codsusppispasep', 14)->nullable();

        });
    }


    private function upDicionario()
    {
        $sql = <<<SQL
        SELECT fc_atualiza_dicionario_apartir_comentario('table column',
            'esocial.esocialrubricas.eso26_tpproc',
            '{
                "descricao": "Código correspondente ao tipo de processo",
                "rotulo": "Tipo de Processo",
                "rotulorel": "Tipo de Processo",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        );

        SELECT fc_atualiza_dicionario_apartir_comentario('table column',
            'esocial.esocialrubricas.eso26_nrprocprocessocp',
            '{
                "descricao": "Número de processo",
                "rotulo": "Número do Processo",
                "rotulorel": "Número do Processo",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 21,
                "tipoobj": "text"
            }'
        );

        SELECT fc_atualiza_dicionario_apartir_comentario('table column',
            'esocial.esocialrubricas.eso26_extdecisao',
            '{
                "descricao": "Extensão da decisão/sentença",
                "rotulo": "Extensão de Decisão",
                "rotulorel": "Extensão de Decisão",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        );

        SELECT fc_atualiza_dicionario_apartir_comentario('table column',
            'esocial.esocialrubricas.eso26_codsuspprocessocp',
            '{
                "descricao": "Código do indicativo da suspensão.",
                "rotulo": "Código do indicativo da suspensão",
                "rotulorel": "Código do indicativo da suspensão",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 14,
                "tipoobj": "text"
            }'
        );

        SELECT fc_atualiza_dicionario_apartir_comentario('table column',
            'esocial.esocialrubricas.eso26_nrprocirrf',
            '{
                "descricao": "Número do processo administrativo ou judicial no âmbito do IRRF",
                "rotulo": "Número do Processo IRRF",
                "rotulorel": "Número do Processo IRRF",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 20,
                "tipoobj": "text"
            }'
        );

        SELECT fc_atualiza_dicionario_apartir_comentario('table column',
            'esocial.esocialrubricas.eso26_codsuspirrf',
            '{
                "descricao": "Código da suspensão conforme processo IRRF",
                "rotulo": "Código de Suspensão IRRF",
                "rotulorel": "Código de Suspensão IRRF",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 14,
                "tipoobj": "text"
            }'
        );

        SELECT fc_atualiza_dicionario_apartir_comentario('table column',
            'esocial.esocialrubricas.eso26_nrprocfgts',
            '{
                "descricao": "Número do processo administrativo ou judicial no âmbito do FGTS",
                "rotulo": "Número do Processo FGTS",
                "rotulorel": "Número do Processo FGTS",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 20,
                "tipoobj": "text"
            }'
        );

        SELECT fc_atualiza_dicionario_apartir_comentario('table column',
            'esocial.esocialrubricas.eso26_nrprocpispasep',
            '{
                "descricao": "Número do processo administrativo ou judicial no âmbito do PIS/PASEP",
                "rotulo": "Número do Processo PIS/PASEP",
                "rotulorel": "Número do Processo PIS/PASEP",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 20,
                "tipoobj": "text"
            }'
        );

        SELECT fc_atualiza_dicionario_apartir_comentario('table column',
            'esocial.esocialrubricas.eso26_codsusppispasep',
            '{
                "descricao": "Código da suspensão conforme processo PIS/PASEP",
                "rotulo": "Código de Suspensão PIS/PASEP",
                "rotulorel": "Código de Suspensão PIS/PASEP",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 14,
                "tipoobj": "text"
            }'
        );

        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL;


        DB::connection()->getPdo()->exec($sql);
    }

    private function downTabela()
    {
        Schema::table('esocial.esocialrubricas', function (Blueprint $table) {
            $table->dropColumn('eso26_tpproc');
            $table->dropColumn('eso26_nrprocprocessocp');
            $table->dropColumn('eso26_extdecisao');
            $table->dropColumn('eso26_codsuspprocessocp');
            $table->dropColumn('eso26_nrprocirrf');
            $table->dropColumn('eso26_codsuspirrf');
            $table->dropColumn('eso26_nrprocfgts');
            $table->dropColumn('eso26_nrprocpispasep');
            $table->dropColumn('eso26_codsuspispasep');
        });
    }


}
