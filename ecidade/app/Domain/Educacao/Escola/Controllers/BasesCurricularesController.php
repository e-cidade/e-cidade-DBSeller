<?php

namespace App\Domain\Educacao\Escola\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\EscolaBase;
use App\Domain\Educacao\Escola\Resources\AtoLegalResource;
use App\Domain\Educacao\Secretaria\Repositories\BaseCurricularRepository;
use App\Domain\Educacao\Secretaria\Repositories\BaseDisciplinaRepository;
use App\Domain\Educacao\Secretaria\Resources\BaseDisciplinaResource;
use App\Domain\Educacao\Secretaria\Resources\BaseResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BasesCurricularesController extends Controller
{
    public function index($escola, BaseCurricularRepository $repository)
    {
        $bases = $repository->indexEscola($escola)->map(function ($base) {
            $retorno = BaseResource::toResponse($base);
            $retorno->baseContinuacao = $base->ed77_i_basecont;
            $retorno->baseEscola = $base->ed77_i_codigo;
            return $retorno;
        });
        return new DBJsonResponse($bases);
    }

    public function salvar(Request $request, BaseCurricularRepository $repository)
    {
        $base = $repository->salvar($request->all());
        $resposta = BaseResource::toResponse($base);
        if (!is_null($base->baseEscola)) {
            $resposta->baseContinuacao = $base->baseEscola->ed77_i_basecont;
            $resposta->baseEscola = $base->baseEscola->ed77_i_codigo;
        }

        return new DBJsonResponse($resposta);
    }

    public function salvarBaseContinuacao(Request $request, BaseCurricularRepository $repository)
    {
        return new DBJsonResponse($repository->salvarBaseContinuacao($request->all()));
    }

    public function excluir($escola, $codigo, BaseCurricularRepository $repository)
    {
        try {
            $retorno = $repository->excluir($codigo, $escola);
        } catch (\Exception $e) {
            if ($e->getCode() == 23503) {
                throw new \Exception(
                    "Não foi possível excluir pois esta base já está vinvulada a alguma turma!"
                );
            }
        }
        return new DBJsonResponse($retorno);
    }

    public function getDisciplinasPorBaseEtapa($base, $etapa, BaseDisciplinaRepository $repository)
    {
        $disciplinas = $repository->getPorBaseEtapa($base, $etapa)->map(function ($baseDisciplina) {
            return BaseDisciplinaResource::toResponse($baseDisciplina);
        });

        return new DBJsonResponse($disciplinas);
    }

    public function salvarDisciplinaBase(Request $request, BaseDisciplinaRepository $repository)
    {
        $parametros = $request->all();
        if (isset($parametros['lote'])) {
            foreach ($parametros['lote'] as $disciplinas) {
                $resposta = BaseDisciplinaResource::toResponse(
                    $repository->salvar(BaseDisciplinaResource::toArrayModel((object)$disciplinas))
                );
            }
        } else {
            $resposta = BaseDisciplinaResource::toResponse(
                $repository->salvar(BaseDisciplinaResource::toArrayModel((object)$parametros))
            );
        }
        return new DBJsonResponse($resposta);
    }

    public function excluirDisciplinaBase($codigo, BaseDisciplinaRepository $repository)
    {
        $resposta = $repository->excluir($codigo);
        return new DBJsonResponse($resposta);
    }

    public function getBaseAtos($baseEscola)
    {
        $atos = EscolaBase::find($baseEscola)->atos->map(function ($ato) {
            $atoLegal = AtoLegalResource::toResponse($ato->ato);
            $atoLegal->baseAto = $ato->ed278_i_codigo;
            return $atoLegal;
        });
        return new DBJsonResponse($atos);
    }

    public function excluirBaseAto($codigo, BaseCurricularRepository $repository)
    {
        return new DBJsonResponse($repository->excluirBaseAto($codigo));
    }

    public function salvarBaseAto(Request $request, BaseCurricularRepository $repository)
    {
        try {
            return new DBJsonResponse(
                $repository->salvarBaseAtos($request->get('baseEscola'), $request->get('ato'))
            );
        } catch (\Exception $exception) {
            if ($exception->getCode() == 23505) {
                throw new \Exception(
                    "Ato Já vinculado nesta base!"
                );
            }
        }
    }
}
