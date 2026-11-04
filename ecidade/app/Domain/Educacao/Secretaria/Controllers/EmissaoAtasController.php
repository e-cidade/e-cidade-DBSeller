<?php

namespace App\Domain\Educacao\Secretaria\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domain\Educacao\Escola\Models\CursoEdu;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Secretaria\Resources\CursoResource;
use App\Domain\Educacao\Secretaria\Repositories\CursoRepository;
use App\Domain\Educacao\Secretaria\Resources\DisciplinaResource;
use App\Domain\Educacao\Secretaria\Services\EmissaoAtasService;

class EmissaoAtasController extends Controller
{
    public function getAlunosClassificacao($calendario, $turma)
    {
        $alunos = (new EmissaoAtasService())->getAlunosClassificacao($calendario, $turma);
        return new DBJsonResponse($alunos);
    }

    public function getAlunosAvanco($calendario, $turma)
    {
        $alunos = (new EmissaoAtasService())->getAlunosAvanco($calendario, $turma);
        return new DBJsonResponse($alunos);
    }

    public function getAlunosReclassificacao($escola, $calendario, $turma)
    {
        $alunos = (new EmissaoAtasService())->getAlunosReclassificacao($escola, $calendario, $turma);
        return new DBJsonResponse($alunos);
    }
}
