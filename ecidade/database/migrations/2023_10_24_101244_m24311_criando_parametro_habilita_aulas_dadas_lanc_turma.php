<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24311CriandoParametroHabilitaAulasDadasLancTurma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("escola.edu_parametros", function ($table) {
            $table->boolean("ed233_campo_aulas_dadas_lanc_turma")->default(true);
        });

        $dados = [
            [1015515,'ed233_campo_aulas_dadas_lanc_turma','bool','Habilita Aulas Dadas Lançamento por Turma','f', 'Habilita Aulas Dadas Lançamento por Turma',1,'f','f','f',5,'text','Habilita Aulas Dadas Lançament por Turma']
        ];

        foreach ($dados as $linha) {
            DB::table('configuracoes.db_syscampo')->insert([
                'codcam' => $linha[0],
                'nomecam' => $linha[1],
                'conteudo' => $linha[2],
                'descricao' => $linha[3],
                'valorinicial' => $linha[4],
                'rotulo' => $linha[5],
                'tamanho' => $linha[6],
                'nulo' => $linha[7],
                'maiusculo' => $linha[8],
                'autocompl' => $linha[9],
                'aceitatipo' => $linha[10],
                'tipoobj' => $linha[11],
                'rotulorel' => $linha[12]
            ]);
        }

        $dados = [
          [2019,1015515,17,0]
        ];

        foreach ($dados as $linha) {
            DB::table('configuracoes.db_sysarqcamp')->insert([
                'codarq' => $linha[0],
                'codcam' => $linha[1],
                'seqarq' => $linha[2],
                'codsequencia' => $linha[3]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("escola.edu_parametros", function ($table) {
            $table->dropColumn("ed233_campo_aulas_dadas_lanc_turma");
        });

        DB::table('configuracoes.db_sysarqcamp')->where('codcam', 1015515)->delete();
        DB::table('configuracoes.db_syscampo')->where('codcam', 1015515)->delete();
    }
}
