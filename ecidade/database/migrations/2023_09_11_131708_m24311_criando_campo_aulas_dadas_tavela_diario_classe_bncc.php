<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24311CriandoCampoAulasDadasTavelaDiarioClasseBncc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upCriaCampo();
        $this->upCriaCampoDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downCriaCampo();
        $this->downCriaCampoDicionario();
    }


    public function upCriaCampo()
    {
        Schema::table('escola.diario_classe_bncc', function (Blueprint $table) {
            $table->integer('ed155_aulas_dadas')->default(1);
        });
    }

    public function downCriaCampo()
    {
        Schema::table('escola.diario_classe_bncc', function (Blueprint $table) {
            $table->dropColumn('ed155_aulas_dadas');
        });
    }

    public function upCriaCampoDicionario()
    {
        $dados = [
            [1015385,'ed155_aulas_dadas','int4','Aulas Dadas','0', 'Aulas Dadas',11,'t','f','f',1,'text','Aulas Dadas']
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
            [1010520,1015385,8,0]
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

    public function downCriaCampoDicionario()
    {
        DB::table('configuracoes.db_sysarqcamp')->where('codcam', 1015385)->delete();
        DB::table('configuracoes.db_syscampo')->where('codcam', 1015385)->delete();
    }

}
