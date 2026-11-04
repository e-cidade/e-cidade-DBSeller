<?php

namespace App\Domain\Educacao\Escola\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\MatriculaEscolaAluno;
use App\Http\Controllers\Controller;

class MatriculaEscolaAlunoController extends Controller
{
    public function buscar($escola = '', $aluno = '')
    {
        if (!$escola || !$aluno) {
            return new DBJsonResponse();
        }

        $item = MatriculaEscolaAluno::where('ed365_escola', $escola)
            ->where('ed365_aluno', $aluno)
            ->first();

        return new DBJsonResponse($item);
    }

    public function incluir($escola = '', $aluno = '', $matricula = '')
    {
        if (!$escola || !$aluno || !$matricula) {
            return new DBJsonResponse();
        }

        $item = MatriculaEscolaAluno::where('ed365_aluno', $aluno)
            ->where('ed365_escola', $escola)->first();

        if (!$item) {
            $item = new MatriculaEscolaAluno([
                'ed365_escola' => $escola,
                'ed365_aluno' => $aluno,
                'ed365_matricula' => $matricula,
            ]);
            $item->save();
        } else {
            $item->fill([
                'ed365_matricula' => $matricula,
            ]);
            $item->update();
        }

        return new DBJsonResponse([]);
    }

    public function incluirMatriculaInicial($escola = '', $aluno = '', $matriculainicial = '')
    {
        if (!$escola || !$aluno || !$matriculainicial) {
            return new DBJsonResponse();
        }

        $item = MatriculaEscolaAluno::where('ed365_aluno', $aluno)
            ->where('ed365_escola', $escola)->first();

        if (!$item) {
            $item = new MatriculaEscolaAluno([
                'ed365_escola' => $escola,
                'ed365_aluno' => $aluno,
                'ed365_datamatriculainicial' => $matriculainicial,
            ]);
            $item->save();
        } else {
            $item->fill([
                'ed365_datamatriculainicial' => $matriculainicial,
            ]);
            $item->update();
        }

        return new DBJsonResponse([]);
    }
}
