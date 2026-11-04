<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28898AlterandoDescricaoCategoria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('recursoshumanos.rhcodigocategoria')
            ->where('rh255_codigo',313)
            ->update(['rh255_descricao' => 'Servidor público exercente de atividade de instrutoria, curso ou concurso, convocado para pareceres técnicos, depoimentos ou aditância no exterior.']);
        DB::table('recursoshumanos.rhcodigocategoria')
            ->where('rh255_codigo',902)
            ->update(['rh255_descricao' => 'Médico residente, residente em área profissional de saúde ou médico em curso de formação.']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('recursoshumanos.rhcodigocategoria')
            ->where('rh255_codigo',313)
            ->update(['rh255_descricao' => 'Servidor público exercente de atividade de instrutoria, capacitação, treinamento, curso ou concurso, ou convocado para pareceres técnicos ou depoimentos (AGENTE PÚBLICO)']);
        DB::table('recursoshumanos.rhcodigocategoria')
            ->where('rh255_codigo',902)
            ->update(['rh255_descricao' => 'Médico residente ou residente em área profissional de saúde (BOLSISTA)']);
    }
}
