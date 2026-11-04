<?php

namespace App\Domain\Educacao\Escola\Controllers;

use DBDate;
use db_utils;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Resources\AtendimentoEspecialResource;
use App\Domain\Educacao\Secretaria\Repositories\RecursosUtilizadosRepository;
use App\Domain\Educacao\Escola\Repositories\AlunoAtendimentoEspecialRepository;
use App\Domain\Educacao\Secretaria\Repositories\RecursosAtendimentosRepository;

class AlunoAtendimentoEspecialController extends Controller
{
    public function getAtendimentosByAluno($aluno, AlunoAtendimentoEspecialRepository $repository)
    {
        $dados = [];

        $dados = $repository->getAtendimentosEspecial($aluno)->map(function ($atendimento) {
            return AtendimentoEspecialResource::toResponse($atendimento);
        });

        return new DBJsonResponse($dados);
    }

    public function getDatasAtendimentosByAlunos(Request $request, AlunoAtendimentoEspecialRepository $repository)
    {
        $dados = [];
        foreach ($request['alunos'] as $aluno) {
            $dados = $repository->getAtendimentosEspecial($aluno['codigo'])->map(function ($atendimento) use ($dados) {
                $data = new DBDate(AtendimentoEspecialResource::toResponse($atendimento)->data);
                if (!in_array($data->getDate(DBdate::DATA_PTBR), $dados)) {
                    return $data->getDate(DBdate::DATA_PTBR);
                }
            });
        }
        return new DBJsonResponse($dados);
    }

    public function getProfissionaisEspecializadosPorTurma($turma, AlunoAtendimentoEspecialRepository $repository)
    {
        $retorno = $repository->getProfissionaisEspecializados($turma);
        return new DBJsonResponse($retorno);
    }

    public function getRecursosByAluno($aluno, RecursosAtendimentosRepository $repositoryRecursos)
    {
        $dadosRecursos = [];

        $dadosRecursos = $repositoryRecursos->getRecursosAtendimentos($aluno);
        
        return new DBJsonResponse($dadosRecursos);
    }

    public function deleteAtendimentosByCodigo(
        $codigo,
        AlunoAtendimentoEspecialRepository $repository,
        RecursosAtendimentosRepository $repositoryRecursos
    ) {

        if (!empty($codigo)) {
            $msgSucesso = "Atendimento Especial excluido com sucesso!";
            $repositoryRecursos->deleteRecursoAtendimentos($codigo);
            return new DBJsonResponse($repository->deleteAtendimentoEspecial($codigo), $msgSucesso, 200, false);
        }

        throw new \Exception("Não foi possível excluir!");
    }

    public function updateAtendimentos(
        Request $request,
        AlunoAtendimentoEspecialRepository $repository,
        RecursosAtendimentosRepository $repositoryRecursos
    ) {

        $atendimentos = $request->all();
        $repository->update($atendimentos);
        $repositoryRecursos->update($atendimentos);
        return new DBJsonResponse([], 'Atualização feita feita');
    }

    public function persistAtendimentos(
        Request $request,
        AlunoAtendimentoEspecialRepository $repository,
        RecursosAtendimentosRepository $repositoryRecursos
    ) {
        
        $atendimentos = $request->all();
        $atendimento = $repository->persist($atendimentos);
        $repositoryRecursos->persist($atendimento->ed197_codigo, $atendimentos);
        return new DBJsonResponse([], 'Inclusão feita');
    }



    public function getRecursos(RecursosUtilizadosRepository $repository)
    {
        $dados = [];

        $dados = $repository->getRecursosUtilizados($repository);
        
        return new DBJsonResponse($dados);
    }

    public function deleteRecursoByCodigo($codigo, RecursosUtilizadosRepository $repository)
    {

        if (!empty($codigo)) {
            $msgSucesso = "Atendimento Especial excluido com sucesso!";
    
            return new DBJsonResponse($repository->deleteRecursoUtilizado($codigo), $msgSucesso, 200, false);
        }

        throw new \Exception("Não foi possível excluir!");
    }

    public function persistRecurso(Request $request, RecursosUtilizadosRepository $repository)
    {

        $recursos = $request->all();

        return new DBJsonResponse($repository->persist($recursos));
    }

    public function updateRecurso(Request $request, RecursosUtilizadosRepository $repository)
    {
        
        $recursos = $request->all();

        return new DBJsonResponse($repository->update($recursos));
    }
}
