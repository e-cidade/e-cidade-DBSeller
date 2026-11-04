<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23809NovaTabelaCensonecessidaderecurso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('escola.censo_necessidade_recurso', function (Blueprint $table) {
            $table->increments('ed200_id');
            $table->integer('ed200_necessidade');
            $table->text('ed200_recurso');

            $table->foreign('ed200_necessidade', 'necessidade_fk')
                ->references('ed48_i_codigo')->on('escola.necessidade');
        });

        $dados = [
            [
                'ed200_necessidade' => 102,
                'ed200_recurso' => 'Auxílio ledor'
            ],
            [
                'ed200_necessidade' => 102,
                'ed200_recurso' => 'Auxílio transcrição'
            ],
            [
                'ed200_necessidade' => 102,
                'ed200_recurso' => 'CD com áudio para deficiente visual'
            ],
            [
                'ed200_necessidade' => 102,
                'ed200_recurso' => 'Nenhum'
            ],
            [
                'ed200_necessidade' => 102,
                'ed200_recurso' => 'Prova ampliada (Fonte 18)'
            ],
            [
                'ed200_necessidade' => 102,
                'ed200_recurso' => 'Prova superampliada (Fonte 24)'
            ],
            [
                'ed200_necessidade' => 101,
                'ed200_recurso' => 'Auxílio ledor'
            ],
            [
                'ed200_necessidade' => 101,
                'ed200_recurso' => 'Auxílio transcrição'
            ],
            [
                'ed200_necessidade' => 101,
                'ed200_recurso' => 'CD com áudio para deficiente visual'
            ],
            [
                'ed200_necessidade' => 101,
                'ed200_recurso' => 'Material didático e prova em Braille'
            ],
            [
                'ed200_necessidade' => 104,
                'ed200_recurso' => 'Leitura labial'
            ],
            [
                'ed200_necessidade' => 104,
                'ed200_recurso' => 'Nenhum'
            ],
            [
                'ed200_necessidade' => 104,
                'ed200_recurso' => 'Prova de Língua Portuguesa como Segunda Língua para surdos e deficientes auditivos'
            ],
            [
                'ed200_necessidade' => 104,
                'ed200_recurso' => 'Prova em Vídeo em Libras'
            ],
            [
                'ed200_necessidade' => 104,
                'ed200_recurso' => 'Tradutor-intérprete de Libras'
            ],
            [
                'ed200_necessidade' => 106,
                'ed200_recurso' => 'Auxílio ledor'
            ],
            [
                'ed200_necessidade' => 106,
                'ed200_recurso' => 'Auxílio transcrição'
            ],
            [
                'ed200_necessidade' => 106,
                'ed200_recurso' => 'CD com áudio para deficiente visual'
            ],
            [
                'ed200_necessidade' => 106,
                'ed200_recurso' => 'Nenhum'
            ],
            [
                'ed200_necessidade' => 107,
                'ed200_recurso' => 'Auxílio ledor'
            ],
            [
                'ed200_necessidade' => 107,
                'ed200_recurso' => 'Auxílio transcrição'
            ],
            [
                'ed200_necessidade' => 107,
                'ed200_recurso' => 'CD com áudio para deficiente visual'
            ],
            [
                'ed200_necessidade' => 107,
                'ed200_recurso' => 'Nenhum'
            ],
            [
                'ed200_necessidade' => 103,
                'ed200_recurso' => 'Leitura labial'
            ],
            [
                'ed200_necessidade' => 103,
                'ed200_recurso' => 'Nenhum'
            ],
            [
                'ed200_necessidade' => 103,
                'ed200_recurso' => 'Prova de Língua Portuguesa como Segunda Língua para surdos e deficientes auditivos'
            ],
            [
                'ed200_necessidade' => 103,
                'ed200_recurso' => 'Prova em Vídeo em Libras'
            ],
            [
                'ed200_necessidade' => 103,
                'ed200_recurso' => 'Tradutor-intérprete de Libras'
            ],
            [
                'ed200_necessidade' => 105,
                'ed200_recurso' => 'Auxílio ledor'
            ],
            [
                'ed200_necessidade' => 105,
                'ed200_recurso' => 'Auxílio transcrição'
            ],
            [
                'ed200_necessidade' => 105,
                'ed200_recurso' => 'CD com áudio para deficiente visual'
            ],
            [
                'ed200_necessidade' => 105,
                'ed200_recurso' => 'Guia-intérprete'
            ],
            [
                'ed200_necessidade' => 105,
                'ed200_recurso' => 'Leitura labial'
            ],
            [
                'ed200_necessidade' => 105,
                'ed200_recurso' => 'Material didático e prova em Braille'
            ],
            [
                'ed200_necessidade' => 105,
                'ed200_recurso' => 'Prova ampliada (Fonte 18)'
            ],
            [
                'ed200_necessidade' => 105,
                'ed200_recurso' => 'Prova de Língua Portuguesa como Segunda Língua para surdos e deficientes auditivos'
            ],
            [
                'ed200_necessidade' => 105,
                'ed200_recurso' => 'Prova em Vídeo em Libras'
            ],
            [
                'ed200_necessidade' => 105,
                'ed200_recurso' => 'Prova superampliada (Fonte 24)'
            ],
            [
                'ed200_necessidade' => 105,
                'ed200_recurso' => 'Tradutor-intérprete de Libras'
            ],
            [
                'ed200_necessidade' => 109,
                'ed200_recurso' => 'Auxílio ledor'
            ],
            [
                'ed200_necessidade' => 109,
                'ed200_recurso' => 'Auxílio transcrição'
            ],
            [
                'ed200_necessidade' => 109,
                'ed200_recurso' => 'CD com áudio para deficiente visual'
            ],
            [
                'ed200_necessidade' => 109,
                'ed200_recurso' => 'Nenhum'
            ],
            [
                'ed200_necessidade' => 114,
                'ed200_recurso' => 'Auxilio ledor'
            ],
            [
                'ed200_necessidade' => 114,
                'ed200_recurso' => 'Auxílio transcrição'
            ],
            [
                'ed200_necessidade' => 114,
                'ed200_recurso' => 'Prova superampliada (Fonte 24)'
            ],
            [
                'ed200_necessidade' => 114,
                'ed200_recurso' => 'CD com áudio para deficiente visual'
            ],
            [
                'ed200_necessidade' => 114,
                'ed200_recurso' => 'Prova ampliada (Fonte 18)'
            ],
            [
                'ed200_necessidade' => 114,
                'ed200_recurso' => 'Nenhum'
            ]
        ];

        foreach ($dados as $dado) {
            DB::table('censo_necessidade_recurso')->insert($dado);
        }
    }

    public function upDicionario()
    {
        DB::statement("insert into db_sysarquivo values (1011075, 'censo_necessidade_recurso', 'Necessidade Especial Recurso', 'ed200', '2023-05-10', 'Necessidade Especial Recurso', 0, 't', 't', 't', 't' )");
        DB::statement("insert into db_sysarqmod values (1008004,1011075)");

        $dados = [
            [1015056,'ed200_id','int4','ID','0', 'ID',11,'f','f','f',1,'text','ID'],
            [1015057,'ed200_necessidade','int4','Necessidade','0', 'Necessidade',11,'f','f','f',1,'text','Necessidade'],
            [1015058,'ed200_recurso','text','Recurso','', 'Recurso',500,'f','f','f',0,'text','Recurso']
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

        DB::statement("insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011075,1015056,1,1015056)");
        DB::statement("insert into db_sysforkey values(1011075,1015057,1,1010050,0)");
        DB::statement("insert into db_sysindices values(1008855,'ed200_id',1011075,'1')");
        DB::statement("insert into db_syscadind values(1008855,1015056,1)");

        $dados = [
            [1011075,1015056,1,0],
            [1011075,1015057,2,0],
            [1011075,1015058,3,0]
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
        Schema::dropIfExists('escola.censo_necessidade_recurso');
    }

    public function downDicionario()
    {
        DB::table('configuracoes.db_sysarqcamp')->whereIn('codcam', [1015056, 1015057, 1015058])->delete();
        DB::statement("delete from db_sysarqmod where codarq = 1011075");
        DB::statement("delete from db_sysprikey where codarq = 1011075");
        DB::statement("delete from db_sysarquivo where codarq = 1011075");
        DB::statement("delete from db_sysindices where codind = 1008855");
        DB::statement("delete from db_sysforkey where referen = 1011075");
        DB::table('configuracoes.db_syscampo')->whereIn('codcam', [1015056, 1015057, 10150568])->delete();

        DB::table("configuracoes.db_sysarquivo")->where('codarq', 1011075)->delete();
        DB::table("configuracoes.db_sysarqmod")->where('codarq', 1011075)->delete();
    }
}
