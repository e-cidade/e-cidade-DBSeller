<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26238NovoParametroAlteraProcedimentoAvaliacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;');
        DB::statement(' ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;');

        Schema::table('secretariadeeducacao.sec_parametros', function (Blueprint $table) {
            $table->boolean('ed290_permite_alteracao_proc_avaliacao')->default(false);
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('secretariadeeducacao.sec_parametros');");

        $dicionario = [
            "descricao" => "Permite alteracao proavaliacao.",
            "rotulo" => "Permite alteracao proc avaliacao.",
            "rotulorel" => "Permite alteracao proc avaliacao.",
            "maiusculo" => false,
            "autocompl" => false,
            "aceitatipo" => 1,
            "tamanho" => 10,
            "tipoobj" => "text"
        ];
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('secretariadeeducacao', 'sec_parametros')");
        $cm = mb_convert_encoding(json_encode($dicionario), 'ISO-8859-1');
        $sql = "COMMENT ON COLUMN secretariadeeducacao.sec_parametros.ed290_permite_alteracao_proc_avaliacao IS '{$cm}'";
        DB::statement($sql);
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;');
        DB::statement(' ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("SELECT fc_remove_dicionario_tabela('secretariadeeducacao', 'sec_parametros');");
        Schema::table('secretariadeeducacao.sec_parametros', function (Blueprint $table) {
            $table->dropColumn('ed290_permite_alteracao_proc_avaliacao');
        });
    }
}
