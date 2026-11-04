<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24311CriandoNovoItemDeMenuENovaTabela extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upCriaTabela();
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downCriaTabela();
        $this->downDicionario();
    }

    public function upItemMenu()
    {
        DB::table('configuracoes.db_itensmenu')->insert([
            'id_item' => 228949,
            'descricao' => 'Tipos de Instrumentos Avaliativos',
            'help' =>'Tipos de Instrumentos Avaliativos',
            'funcao' => 'web/educacao/secretaria/cadastros/tipos-instrumentos-avaliativos/crud',
            'itemativo' => '1',
            'manutencao' => '1',
            'desctec' => 'Tipos de Instrumentos Avaliativos',
            'libcliente' => 'true'
        ]);

        DB::table('configuracoes.db_menu')->insert([
            'id_item' => 3470,
            'id_item_filho' => 228949,
            'menusequencia' => 48,
            'modulo' => 7159
        ]);
    }

    public function upCriaTabela()
    {
        Schema::create('secretariadeeducacao.tipo_instrumento_avaliativo', function (Blueprint $table) {
            $table->increments('ed201_id');
            $table->string('ed201_descricao', 70);
            $table->jsonb('ed201_ensinos');
            $table->boolean('ed201_ativo');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('secretariadeeducacao.tipo_instrumento_avaliativo');");
    }

    public function upCriaTabelaDicionario()
    {
        DB::table('configuracoes.db_sysarquivo')->insert([
            'codarq' => 1011104,
            'nomearq' => 'tipo_instrumento_avaliativo',
            'descricao' => 'Tipos de Instrumentos Avaliativos',
            'sigla' => 'ed195',
            'dataincl' => '2023-06-22',
            'rotulo' => 'Tipo de Instrumento Avaliativo',
            'tipotabela' => 0,
            'naolibclass' => 'f',
            'naolibfunc' => 'f',
            'naolibprog' => 'f',
            'naolibform' => 'f'
        ]);

        DB::table('configuracoes.db_sysarqmod')->insert([
            'codmod' => 61,
            'codarq' => 1011104
        ]);

        $dados = [
            [1015202,'ed201_id','int4','ID','0', 'ID',11,'f','f','f',1,'text','ID'],
            [1015203,'ed201_descricao','text','Descrição','', 'Descrição',70,'f','t','f',0,'text','Descrição'],
            [1015204,'ed201_ensinos','text','Ensinos','', 'Ensinos',500,'f','t','f',0,'text','Ensinos'],
            [1015205,'ed201_ativo','int4','Ativo','0', 'Ativo',11,'f','f','f',1,'text','Ativo']
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
            [1011104,1015202,1,0],
            [1011104,1015203,2,0],
            [1011104,1015204,3,0],
            [1011104,1015205,4,0]
        ];

        foreach ($dados as $linha) {
            DB::table('configuracoes.db_sysarqcamp')->insert([
                'codarq' => $linha[0],
                'codcam' => $linha[1],
                'seqarq' => $linha[2],
                'codsequencia' => $linha[3]
            ]);
        }

        DB::table('configuracoes.db_sysindices')->insert([
            'codind' => 1008879,
            'nomeind' => 'tipo_instrumento_avaliativo_indice',
            'codarq' => 1011104,
            'campounico' => '1'
        ]);

        DB::table('configuracoes.db_sysprikey')->insert([
            'codarq' => 1011104,
            'codcam' => 1015202,
            'sequen' => 1,
            'camiden' => 1015202
        ]);

        DB::statement('insert into db_syscadind values(1008879,1015202,1);');
    }

    public function upDicionario()
    {
        $this->upItemMenu();
        $this->upCriaTabelaDicionario();
    }

    public function downCriaTabela()
    {
        Schema::dropIfExists('secretariadeeducacao.tipo_instrumento_avaliativo');
    }

    public function downCriaTabelaDicionario()
    {

        $dados = [1015202, 1015203, 1015204, 1015205];
        DB::table('configuracoes.db_sysarqcamp')->whereIn('codcam', $dados)->delete();
        DB::table('configuracoes.db_sysindices')->where('codind', 1008879)->delete();
        DB::table('configuracoes.db_sysprikey')->where('codarq', 1011104)->delete();
        DB::table('configuracoes.db_syscampo')->whereIn('codcam', $dados)->delete();
        DB::table('configuracoes.db_sysarqmod')->where('codarq', 1011104)->delete();
        DB::table('configuracoes.db_sysarquivo')->where('codarq', 1011104)->delete();
        DB::table('configuracoes.db_syscadind')->where('codind', 1008879)->delete();
    }

    public function downItemMenu()
    {
        DB::table('configuracoes.db_itensmenu')->where('id_item', 228949)->delete();
        DB::table('configuracoes.db_menu')->where('id_item_filho', 228949)->delete();
    }

    public function downDicionario()
    {
        $this->downItemMenu();
        $this->downCriaTabelaDicionario();
    }
}
