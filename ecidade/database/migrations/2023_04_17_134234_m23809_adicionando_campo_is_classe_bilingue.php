<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23809AdicionandoCampoIsClasseBilingue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        Schema::table('escola.turma', function (Blueprint $table) {
            $table->boolean('ed57_is_bilingue')->default(false);
        });

        Schema::table('escola.turmaac', function (Blueprint $table) {
            $table->boolean('ed268_is_bilingue')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        Schema::table('escola.turma', function (Blueprint $table) {
            $table->dropColumn('ed57_is_bilingue');
        });

        Schema::table('escola.turmaac', function (Blueprint $table) {
            $table->dropColumn('ed268_is_bilingue');
        });
    }

    public function upDicionario()
    {
        $dados = [
            [1015034,'ed57_is_bilingue','int4','Classe Bilíngue para Surdos','false', 'Classe Bilíngue para Surdos',11,'f','f','f',1,'text','Classe Bilíngue para Surdos'],
            [1015035,'ed268_is_bilingue','int4','Classe Bilíngue para Surdos','false', 'Classe Bilíngue para Surdos',11,'f','f','f',1,'text','Classe Bilíngue para Surdos']
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
            [1010083,1015034,22,0],
            [2416,1015035,15,0]
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

    public function downDicionario()
    {
        DB::table('configuracoes.db_sysarqcamp')->where('codcam',1015034)->delete();
        DB::table('configuracoes.db_syscampo')->where('codcam',1015034)->delete();

        DB::table('configuracoes.db_sysarqcamp')->where('codcam',1015035)->delete();
        DB::table('configuracoes.db_syscampo')->where('codcam',1015035)->delete();
    }




}
