<?php

namespace App\Domain\Educacao\Escola\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\AtoLegal;
use App\Domain\Educacao\Escola\Resources\AtoLegalResource;
use App\Domain\Educacao\Secretaria\Resources\CursoResource;
use App\Domain\Educacao\Secretaria\Resources\EnsinoResource;
use App\Http\Controllers\Controller;
use App\Domain\Educacao\Escola\Models\CursoEdu;

class CursoController extends Controller
{
    public function getCursosByEscola($escola)
    {
        $cursos = CursoEdu
            ::join('cursoescola', 'cursoescola.ed71_i_curso', '=', 'cursoedu.ed29_i_codigo')
            ->where("cursoescola.ed71_i_escola", $escola)->get()->map(function ($curso) {
                $curso = CursoResource::toResponse($curso);
                $cursosAtos = \DB::table('cursoato')->select('ed215_i_atolegal')
                    ->where('ed215_i_cursoescola', $curso->cursoEscola)->get()->map(function ($cursoAto) {
                        return $cursoAto->ed215_i_atolegal;
                    });
                $curso->atos = \DB::table('atolegal')
                    ->whereIn('ed05_i_codigo', $cursosAtos)->get();
                return $curso;
            });
        return new DBJsonResponse($cursos);
    }

    public function getCurso($escola, $codigo)
    {
        $curso = CursoEdu
            ::join('cursoescola', 'cursoescola.ed71_i_curso', '=', 'cursoedu.ed29_i_codigo')
            ->where("cursoescola.ed71_i_escola", $escola)
            ->where('ed29_i_codigo', $codigo)->get()->map(function ($curso) {
                $curso->nomeEnsino = trim($curso->ensino->ed10_c_descr);
                $curso->ensinos = EnsinoResource::toResponse($curso->ensino);
                $cursosAtos = \DB::table('cursoato')->select('ed215_i_atolegal')
                    ->where('ed215_i_cursoescola', $curso->ed71_i_codigo)->get()->map(function ($cursoAto) {
                        return $cursoAto->ed215_i_atolegal;
                    });
                $curso->atos = AtoLegal
                    ::whereIn('ed05_i_codigo', $cursosAtos->toArray())->get()->map(function ($ato) {
                        return AtoLegalResource::toResponse($ato);
                    });
                return $curso;
            })->first();
        return new DBJsonResponse($curso);
    }
}
