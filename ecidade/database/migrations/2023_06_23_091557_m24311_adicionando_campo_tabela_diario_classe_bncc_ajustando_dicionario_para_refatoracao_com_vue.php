<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24311AdicionandoCampoTabelaDiarioClasseBnccAjustandoDicionarioParaRefatoracaoComVue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upCriaCampo();
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downCriaCampo();
        $this->downDicionario();
    }

    public function upCriaCampo()
    {
        Schema::table('escola.diario_classe_bncc', function (Blueprint $table) {
            $table->integer('ed155_tipo_instrumento_avaliativo')->nullable();
            $table->foreign('ed155_tipo_instrumento_avaliativo', 'diario_classe_bncc_ed155_tipo_instrumento_avaliativo_fk')
                ->references('ed201_id')
                ->on('tipo_instrumento_avaliativo');
        });
        DB::statement("ALTER TABLE escola.diario_classe_bncc ALTER COLUMN ed155_codigo SET DEFAULT nextval('diario_classe_bncc_ed155_codigo_seq')");
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('escola.diario_classe_bncc');");
    }

    public function downCriaCampo()
    {
        Schema::table('escola.diario_classe_bncc', function (Blueprint $table) {
            $table->dropColumn('ed155_tipo_instrumento_avaliativo');
        });
        DB::statement("ALTER TABLE escola.diario_classe_bncc ALTER COLUMN ed155_codigo SET DEFAULT 0");
    }

    public function upCriaCampoDicionario()
    {
        $dados = [
            [1015206,'ed155_tipo_instrumento_avaliativo','int4','Tipo de Instrumento Avaliativo','0', 'Tipo de Instrumento Avaliativo',11,'f','f','f',1,'text','Tipo de Instrumento Avaliativo']
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
            [1010520,1015206,7,0]
        ];

        foreach ($dados as $linha) {
            DB::table('configuracoes.db_sysarqcamp')->insert([
                'codarq' => $linha[0],
                'codcam' => $linha[1],
                'seqarq' => $linha[2],
                'codsequencia' => $linha[3]
            ]);
        }

        $dados = [
            [1010520,1015206,1,1011104,0]
        ];

        foreach ($dados as $linha) {
            DB::table('configuracoes.db_sysforkey')->insert([
                'codarq' => $linha[0],
                'codcam' => $linha[1],
                'sequen' => $linha[2],
                'referen' => $linha[3],
                'tipoobjrel' => $linha[4]
            ]);
        }

    }

    public function downCriaCampoDicionario()
    {
        DB::table('configuracoes.db_sysarqcamp')->where('codcam', 1015206)->delete();
        DB::table('configuracoes.db_sysforkey')->where('codcam', 1015206)->delete();
        DB::table('configuracoes.db_syscampo')->where('codcam', 1015206)->delete();
    }

    public function upItemMenu()
    {
        DB::table('db_itensmenu')->where('id_item', 228234)->update(['funcao' => 'web/educacao/escola/procedimentos/diario-classe/registro-aula']);
    }

    public function downItemMenu()
    {
        DB::table('db_itensmenu')->where('id_item', 228234)->update(['funcao' => 'edu4_lancamento_conteudo.php']);
    }


    public function upDicionario()
    {
        $this->upCriaCampoDicionario();
        $this->upItemMenu();
    }

    public function downDicionario()
    {
        $this->downCriaCampoDicionario();
        $this->downItemMenu();
    }

}
