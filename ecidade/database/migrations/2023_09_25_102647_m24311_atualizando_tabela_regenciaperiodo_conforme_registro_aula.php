<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24311AtualizandoTabelaRegenciaperiodoConformeRegistroAula extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        return;
        $conteudos = DB::table('escola.diario_classe_bncc')
            ->join('escola.regencia', 'regencia.ed59_i_codigo', '=', 'diario_classe_bncc.ed155_regencia')
            ->join('escola.turma', 'turma.ed57_i_codigo', '=', 'regencia.ed59_i_turma')
            ->join('escola.calendario', 'turma.ed57_i_calendario', '=', 'calendario.ed52_i_codigo')
            ->join('escola.periodocalendario', function ($join) {
                $join->on('calendario.ed52_i_codigo', '=', 'periodocalendario.ed53_i_calendario')
                    ->on('periodocalendario.ed53_d_inicio', '<=', 'diario_classe_bncc.ed155_data')
                    ->on('periodocalendario.ed53_d_fim', '>=', 'diario_classe_bncc.ed155_data');
            })
            ->join('escola.procedimento', 'regencia.ed59_procedimento', '=', 'procedimento.ed40_i_codigo')
            ->join('escola.procavaliacao', function ($join) {
                $join->on('procedimento.ed40_i_codigo', '=', 'procavaliacao.ed41_i_procedimento')
                    ->on('procavaliacao.ed41_i_periodoavaliacao', '=', 'periodocalendario.ed53_i_periodoavaliacao');
            })->get();

        if (count($conteudos) === 0) {
            return;
        }
        foreach ($conteudos as $conteudo) {
            $conteudos = DB::table('escola.diario_classe_bncc')
                            ->where("ed155_regencia", $conteudo->ed155_regencia)
                            ->where('ed155_data', '>=', $conteudo->ed53_d_inicio)
                            ->where('ed155_data', '<=', $conteudo->ed53_d_fim)->get();
            if (count($conteudos) === 0) {
                continue;
            }
            $auldasDadas = 0;
            if (trim($conteudo->ed57_c_medfreq) === 'DIAS LETIVOS') {
                $pordia = [];
                foreach ($conteudos as $cont) {
                    $pordia[$cont->ed155_data] = $cont;
                }
                $auldasDadas = count($pordia);
            } else {
              continue;
            }
            DB::table('escola.regenciaperiodo')
                ->where('ed78_i_regencia', $conteudo->ed155_regencia)
                ->where('ed78_i_procavaliacao', $conteudo->ed41_i_codigo)
                ->update(['ed78_i_aulasdadas' => $auldasDadas]);

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}


