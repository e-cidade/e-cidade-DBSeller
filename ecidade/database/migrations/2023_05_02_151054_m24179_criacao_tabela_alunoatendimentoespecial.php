<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M24179CriacaoTabelaAlunoatendimentoespecial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('escola.alunoatendimentoespecial', function(Blueprint $table) {
            $table->increments('ed197_codigo');
            $table->integer('ed197_aluno');
            $table->jsonb('ed197_atendimentos');
            $table->date('ed197_data_emissao');
            $table->text('ed197_parecer');

            $table->foreign('ed197_aluno', 'alunoatendimentoespecial_aluno_fk')
                ->references('ed47_i_codigo')
                ->on('aluno');            
        });

        Schema::create('escola.recursosutilizados', function(Blueprint $table) {
            $table->increments('ed198_codigo');
            $table->text('ed198_descricao');  
        });

        Schema::create('escola.recursosatendimentos', function(Blueprint $table) {
            $table->increments('ed199_codigo');
            $table->integer('ed199_alunoatendimentos'); 
            $table->integer('ed199_recursosutilizados');  
            

            $table->foreign('ed199_alunoatendimentos', 'recursosatendimentos_alunoatendimentoespecial_fk')
                ->references('ed197_codigo')
                ->on('alunoatendimentoespecial');
            $table->foreign('ed199_recursosutilizados', 'recursosatendimentos_recursosutilizados_fk')
                ->references('ed198_codigo')
                ->on('recursosutilizados');
        });

        $dados = [
            ['Teclado com colméia'],
            ['Acionador de pressão'],
            ['Mouse com entrada para acionador'],
            ['Lupa eletrônica'],
            ['Soroban'],
            ['Guia de Assinatura'],
            ['Kit Desenho Geométrico'],
            ['Calculadora Sonora'],
            ['Material Dourado'],
            ['Esquema Corporal'],
            ['Bandinha Rítmica'],
            ['Memória de Numerais l'],
            ['Tapete Alfabético Encaixado'],
            ['Software Comunicação Alternativa'],
            ['Sacolão Criativo Monta Tudo'],
            ['Quebra Cabeças - sequência lógica'],
            ['Dominó de Associação de Ideias'],
            ['Dominó de Frases'],
            ['Dominó de Animais em Libras'],
            ['Dominó de Frutas em Libras'],
            ['Dominó tátil'],
            ['Alfabeto Braille'],
            ['Lupas manuais'],
            ['Plano inclinado - suporte para leitura'],
            ['Memória Tátil']
        ];

        foreach ($dados as $key=>$dado) {
            $id = "nextval('recursosutilizados_ed198_codigo_seq') as id";
            $id = DB::table('escola.recursosutilizados')->selectRaw($id)->value('id');
            DB::table('escola.recursosutilizados')->insert([
                'ed198_codigo' => intval($id),
                'ed198_descricao' => $dado[0]
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
        Schema::dropIfExists('escola.recursosatendimentos');
        Schema::dropIfExists('escola.recursosutilizados');
        Schema::dropIfExists('escola.alunoatendimentoespecial');

        DB::table('escola.recursosutilizados')->whereBetween('codcam', [0, 24]);
        
    }

//sera que tem q criar no dicionario de dados?
}
