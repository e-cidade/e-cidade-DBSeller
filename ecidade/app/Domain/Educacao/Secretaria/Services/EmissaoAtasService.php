<?php

namespace App\Domain\Educacao\Secretaria\Services;

use Illuminate\Support\Facades\DB;

class EmissaoAtasService
{
    public function getAlunosClassificacao($calendario, $turma)
    {
        return DB::table('escola.trocaserie')
        ->distinct()
        ->select(
            'ed47_i_codigo',
            'ed47_v_nome',
            'turmaorig.ed57_c_descr as turmaorigem',
            'turmadest.ed57_c_descr as turmadestino',
            'trocaserie.ed101_d_data',
            'trocaserie.ed101_c_tipo'
        )
        ->join('escola.aluno', 'ed47_i_codigo', '=', 'ed101_i_aluno')
        ->join('escola.turma as turmaorig', 'turmaorig.ed57_i_codigo', '=', 'ed101_i_turmaorig')
        ->join('escola.turma as turmadest', 'turmadest.ed57_i_codigo', '=', 'ed101_i_turmadest')
        ->where('turmaorig.ed57_i_calendario', '=', $calendario)
        ->where('turmaorig.ed57_i_codigo', '=', $turma)
        ->where('trocaserie.ed101_c_tipo', '=', 'C')
        ->orderBy('ed47_v_nome')
        ->get()->map(function ($aluno) {
            return (object) [
                'nome' => trim($aluno->ed47_v_nome),
                'codigo' => $aluno->ed47_i_codigo,
                'tipo' => $aluno->ed101_c_tipo
            ];
        });

        return (object) $alunosClassificacao;
    }

    public function getAlunosAvanco($calendario, $turma)
    {
        return DB::table('escola.trocaserie')
        ->distinct()
        ->select(
            'ed47_i_codigo',
            'ed47_v_nome',
            'turmaorig.ed57_c_descr as turmaorigem',
            'turmadest.ed57_c_descr as turmadestino',
            'trocaserie.ed101_d_data',
            'trocaserie.ed101_c_tipo'
        )
        ->join('escola.aluno', 'ed47_i_codigo', '=', 'ed101_i_aluno')
        ->join('escola.turma as turmaorig', 'turmaorig.ed57_i_codigo', '=', 'ed101_i_turmaorig')
        ->join('escola.turma as turmadest', 'turmadest.ed57_i_codigo', '=', 'ed101_i_turmadest')
        ->where('turmaorig.ed57_i_calendario', '=', $calendario)
        ->where('turmaorig.ed57_i_codigo', '=', $turma)
        ->where('trocaserie.ed101_c_tipo', '=', 'A')
        ->orderBy('ed47_v_nome')
        ->get()->map(function ($aluno) {
            return (object) [
                'nome' => trim($aluno->ed47_v_nome),
                'codigo' => $aluno->ed47_i_codigo,
                'tipo' => $aluno->ed101_c_tipo
            ];
        });

        return (object) $alunosAvanco;
    }

    public function getAlunosReclassificacao($escola, $calendario, $turma)
    {
        return DB::table('escola.trocaserie')
        ->distinct()
        ->select(
            'ed47_i_codigo',
            'ed47_v_nome'
        )
        ->join('escola.aluno', 'ed47_i_codigo', '=', 'ed101_i_aluno')
        ->join('escola.turma as turmaorig', 'turmaorig.ed57_i_codigo', '=', 'ed101_i_turmaorig')
        ->join('escola.escola as escolaorig', 'escolaorig.ed18_i_codigo', '=', 'turmaorig.ed57_i_escola')
        ->join('escola.turno as turnoorig', 'turnoorig.ed15_i_codigo', '=', 'turmaorig.ed57_i_turno')
        ->join('escola.sala as salaorig', 'salaorig.ed16_i_codigo', '=', 'turmaorig.ed57_i_sala')
        ->join(
            'escola.calendario as calendarioorig',
            'calendarioorig.ed52_i_codigo',
            '=',
            'turmaorig.ed57_i_calendario'
        )
        ->join('escola.base as baseorig', 'baseorig.ed31_i_codigo', '=', 'turmaorig.ed57_i_base')
        ->join('escola.turma as turmadest', 'turmadest.ed57_i_codigo', '=', 'trocaserie.ed101_i_turmadest')
        ->join('escola.escola as escoladest', 'escoladest.ed18_i_codigo', '=', 'turmadest.ed57_i_escola')
        ->join('escola.turno as turnodest', 'turnodest.ed15_i_codigo', '=', 'turmadest.ed57_i_turno')
        ->join('escola.sala as saladest', 'saladest.ed16_i_codigo', '=', 'turmadest.ed57_i_sala')
        ->join(
            'escola.calendario as calendariodest',
            'calendariodest.ed52_i_codigo',
            '=',
            'turmadest.ed57_i_calendario'
        )
        ->join('escola.base as basedest', 'basedest.ed31_i_codigo', '=', 'turmadest.ed57_i_base')
        ->where('escolaorig.ed18_i_codigo', '=', $escola)
        ->where('calendarioorig.ed52_i_codigo', '=', $calendario)
        ->where('turmaorig.ed57_i_codigo', '=', $turma)
        ->where('ed101_c_tipo', '=', 'R')
        ->orderBy('ed47_v_nome')
        ->get()->map(function ($aluno) {
            return (object) [
                'nome' => trim($aluno->ed47_v_nome),
                'codigo' => $aluno->ed47_i_codigo
            ];
        });
    }
}
