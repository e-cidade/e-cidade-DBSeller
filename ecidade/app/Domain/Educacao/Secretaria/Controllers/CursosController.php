<?php

namespace App\Domain\Educacao\Secretaria\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\CursoEdu;
use App\Domain\Educacao\Secretaria\Repositories\CursoRepository;
use App\Domain\Educacao\Secretaria\Resources\CursoResource;
use App\Domain\Educacao\Secretaria\Resources\DisciplinaResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CursosController extends Controller
{
    public function index()
    {
        $cursos = CursoEdu::orderBy('ed29_i_codigo')
            ->get()->map(function ($curso) {
                return CursoResource::toResponse($curso);
            });
        return new DBJsonResponse($cursos);
    }
    public function salvar(Request $request, CursoRepository $repository)
    {
        return new DBJsonResponse($repository->salvar($request->all()));
    }

    public function excluir($codigo, CursoRepository $repository)
    {
        try {
             return new DBJsonResponse($repository->excluir($codigo));
        } catch (\Exception $exception) {
            if ($exception->getCode() == 23503) {
                throw new \Exception(
                    "Não foi possível excluir pois este curso já está vinvulado a alguma base curricular!"
                );
            }
        }
    }

    public function getDisciplinasDoCurso($codigo, CursoRepository $repository)
    {
        $disciplinas = $repository->getDisciplinas($codigo)->map(function ($disciplina) {
            $disc = DisciplinaResource::toResponse($disciplina->disciplina);
            $disc->disciplinaEnsino = $disciplina->ed12_i_codigo;
            return $disc;
        });
        return new DBJsonResponse($disciplinas);
    }

    public function getDisciplinaDoCurso($codigo, $disciplina, CursoRepository $repository)
    {
        $disciplina = $repository->getDisciplinas($codigo)->filter(function ($disci) use ($disciplina) {
            return $disci->disciplina->ed232_i_codigo == $disciplina;
        })->first();
        $disciplinaEnsino = $disciplina->ed12_i_codigo;
        $disciplina = DisciplinaResource::toResponse($disciplina->disciplina);
        $disciplina->disciplinaEnsino = $disciplinaEnsino;
        return new DBJsonResponse($disciplina);
    }
}
