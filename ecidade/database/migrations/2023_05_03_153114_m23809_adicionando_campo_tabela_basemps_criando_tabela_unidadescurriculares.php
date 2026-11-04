<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23809AdicionandoCampoTabelaBasempsCriandoTabelaUnidadescurriculares extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        Schema::create('secretariadeeducacao.unidadescurriculares', function (Blueprint $table) {
            $table->increments('ed199_id');
            $table->text('ed199_descricao');
        });

        $dados = [
            "Eletivas",
            "Libras",
            "Língua Indígena",
            "Língua/Literatura estrangeira - Espanhol",
            "Língua/Literatura estrangeira - Francês",
            "Língua/Literatura estrangeira - Outra",
            "Projeto de Vida",
            "Trilhas de aprofundamento/aprendizagens"
        ];
        DB::statement("ALTER TABLE escola.basemps ALTER COLUMN ed34_i_codigo SET DEFAULT nextval('escola.basemps_ed34_i_codigo_seq')");

        foreach ($dados as $key => $dado) {
            DB::table('secretariadeeducacao.unidadescurriculares')->insert([
                'ed199_descricao' => $dado
            ]);
        }

        Schema::table('escola.basemps', function (Blueprint $table) {
            $table->integer('ed34_unidade_curricular')->nullable();

            $table->foreign('ed34_unidade_curricular', 'unidade_curricular_fk')
                ->references('ed199_id')->on('secretariadeeducacao.unidadescurriculares');
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
        Schema::table('escola.basemps', function (Blueprint $table) {
            $table->dropColumn('ed34_unidade_curricular');
        });
        Schema::dropIfExists('secretariadeeducacao.unidadescurriculares');
        DB::statement("ALTER TABLE escola.basemps ALTER COLUMN ed34_i_codigo SET DEFAULT 0");
    }

    public function upDicionario()
    {
        $dados = [
            [1014240,'ed34_unidade_curricular','int4','Unidade Curricular','null', 'Unidade Curricular',11,'t','f','f',0,'text','Unidade Curricular'],
            [1015043,'ed199_descricao','text','Descrição','', 'Descrição',500,'f','f','f',0,'text','Descrição'],
            [1015044,'ed199_id','int4','ID','0', 'ID',11,'f','f','f',1,'text','ID']
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

        DB::statement("insert into db_sysarquivo values (1011072, 'unidadescurriculares', 'Unidades Curriculares', 'ed199', '2023-05-08', 'Unidades Curriculares', 0, 't', 't', 'f', 't')");
        DB::statement("insert into db_sysarqmod values (61,1011072)");
        DB::statement("insert into db_sysindices values(1008854,'ed199_id_indice',1011072,'1')");
        DB::statement("insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011072,1015044,1,1015044)");
        DB::statement("insert into db_sysforkey values(1010061,1014240,1,1011072,0)");

        $dados = [
            [1010061,1014240,16,0],
            [1011072,1015044,1,0],
            [1011072,1015043,2,0]
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
        DB::table('configuracoes.db_sysarqcamp')->whereIn('codcam', [1014240, 1015043, 1015044])->delete();
        DB::statement("delete from db_sysarqmod where codarq = 1011072");
        DB::statement("delete from db_sysprikey where codarq = 1011072");
        DB::statement("delete from db_sysarquivo where codarq = 1011072");
        DB::statement("delete from db_sysindices where codind = 1008854");
        DB::statement("delete from db_sysforkey where referen = 1011072");
        DB::table('configuracoes.db_syscampo')->whereIn('codcam', [1014240, 1015043, 1015044])->delete();
    }
}
