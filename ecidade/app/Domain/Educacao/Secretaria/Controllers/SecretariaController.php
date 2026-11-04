<?php

namespace App\Domain\Educacao\Secretaria\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Secretaria\Repositories\BaseCurricularRepository;
use App\Domain\Educacao\Secretaria\Repositories\DIsciplinaRepository;
use App\Domain\Educacao\Escola\Repositories\AtividadeProfissionalRepository;
use App\Domain\Educacao\Secretaria\Resources\BaseResource;
use App\Domain\Educacao\Secretaria\Resources\DisciplinaResource;
use App\Domain\Educacao\Escola\Resources\AtividadeProfissionalResource;
use App\Http\Controllers\Controller;

class SecretariaController extends Controller
{
    /**
     * @param BaseCurricularRepository $repository
     * @return DBJsonResponse
     *
     * @api {get} educacao/secretaria/bases 01 - Listar Bases Curriculares
     * @apiName ListarBasesCurriculares
     * @apiGroup Educacao-Secretaria
     * @apiPermission usuario
     * @apiVersion 1.0.0
     *
     * @apiDescription Retorna um array de objetos com suas respectivas propriedades
     *
     * @apiSuccess {Object[]} data Array de bases curriculares
     * @apiSuccess {Number} data.codigo C�digo da base curricular
     * @apiSuccess {String} data.descricao Descri��o da base curricular
     * @apiSuccess {false} error Indica que n�o ocorreu erro
     *
     * @apiSuccessExample {json} Exemplo:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [
     *         {
     *            "codigo": 1,
     *            "descricao": "Base Curricular 1"
     *         },
     *         {
     *            "codigo": 2,
     *            "descricao": "Base Curricular 2"
     *         },
     *       ],
     *       "error": false,
     *       "message": ""
     *     }
     */
    public function getBases(BaseCurricularRepository $repository)
    {
        $bases = $repository->index()->map(function ($base) {
            return BaseResource::toResponse($base);
        });
        return new DBJsonResponse($bases);
    }

    
    public function getDisciplinas(DIsciplinaRepository $repository)
    {
        $disciplinas = $repository->index()->map(function ($disciplina) {
            return DisciplinaResource::toResponse($disciplina);
        });
        return new DBJsonResponse($disciplinas);
    }

    /**
     * @param BaseCurricularRepository $repository
     * @return DBJsonResponse
     *
     * @api {get} educacao/secretaria/disciplinas/{base}/{etapa} 02 - Listar Disciplinas de um Curso Base
     * @apiName ListarDisciplinasCursoBase
     * @apiGroup Educacao-Secretaria
     * @apiPermission usuario
     * @apiVersion 1.0.0
     *
     * @apiDescription Retorna um array de objetos com suas respectivas propriedades
     *
     * @apiParam {Number} base C�digo da base curricular
     * @apiParam {Number} etapa Etapa do curso
     *
     * @apiSuccess {Object[]} data Array de disciplinas de um curso base
     * @apiSuccess {Number} data.codigo C�digo da disciplina
     * @apiSuccess {String} data.descricao Descri��o da disciplina
     * @apiSuccess {false} error Indica que n�o ocorreu erro
     *
     * @apiSuccessExample {json} Exemplo:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [
     *         {
     *            "codigo": 1,
     *            "descricao": "Disciplina 1"
     *         },
     *         {
     *            "codigo": 2,
     *            "descricao": "Disciplina 2"
     *         },
     *       ],
     *       "error": false,
     *       "message": ""
     *     }
     */
    public function getDisciplinasCursoBase($base, $etapa, BaseCurricularRepository $repository)
    {
        $disciplinas = $repository->getDisciplinasCursoBase($base, $etapa)->map(function ($disciplina) {
            $disc = DisciplinaResource::toResponse($disciplina->disciplina);
            $disc->disciplinaEnsino = $disciplina->ed12_i_codigo;
            return $disc;
        });
        return new DBJsonResponse($disciplinas);
    }

    /**
     * @param AtividadeProfissionalRepository $repository
     * @return DBJsonResponse
     *
     * @api {get} educacao/secretaria/atividades 03 - Listar Atividades Profissionais
     * @apiName ListarAtividadesProfissionais
     * @apiGroup Educacao-Secretaria
     * @apiPermission usuario
     * @apiVersion 1.0.0
     *
     * @apiDescription Retorna um array de objetos com suas respectivas propriedades
     *
     * @apiSuccess {Object[]} data Array de atividades profissionais
     * @apiSuccess {Number} data.codigo C�digo da atividade profissional
     * @apiSuccess {String} data.descricao Descri��o da atividade profissional
     * @apiSuccess {false} error Indica que n�o ocorreu erro
     *
     * @apiSuccessExample {json} Exemplo:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [
     *         {
     *            "codigo": 1,
     *            "descricao": "Atividade Profissional 1"
     *         },
     *         {
     *            "codigo": 2,
     *            "descricao": "Atividade Profissional 2"
     *         },
     *       ],
     *       "error": false,
     *       "message": ""
     *     }
     */
    public function getAtividades(AtividadeProfissionalRepository $repository)
    {
        $atividades = $repository->index()->map(function ($atividade) {
            return AtividadeProfissionalResource::toResponse($atividade);
        });

        return new DBJsonResponse($atividades);
    }
}
