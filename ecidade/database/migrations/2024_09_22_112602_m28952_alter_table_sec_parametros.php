<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28952AlterTableSecParametros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('secretariadeeducacao.sec_parametros', function (Blueprint $table) {
            $table->boolean('ed290_habilitarcamposmatriculainicial')->default('false');
        });

        // Habilitando triggers
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        // Adicionando comentário na coluna criada
        DB::statement("COMMENT ON COLUMN secretariadeeducacao.sec_parametros.ed290_habilitarcamposmatriculainicial IS '{
                \"descricao\": \"Armazena a variável que habilita ou desabilita os inputs de matricula inicial na aba cursos do cadastro do aluno.\",
                \"rotulo\": \"ed290_habilitarcamposmatriculainicial\",
                \"rotulorel\": \"ed290_habilitarcamposmatriculainicial\",
                \"maiusculo\": false,
                \"autocompl\": false,
                \"aceitatipo\": 5,
                \"tamanho\": 1,
                \"tipoobj\": \"text\"
            }'");

        DB::select("SELECT fc_gera_dicionario_apartir_tabela(?, ?)", ['secretariadeeducacao', 'sec_parametros']);

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('secretariadeeducacao.sec_parametros', function (Blueprint $table) {
            $table->dropColumn('ed290_habilitarcamposmatriculainicial');
        });
    }
}
