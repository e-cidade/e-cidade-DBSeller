<?php

namespace App\Domain\Educacao\Censo\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Censo\Enums\TipoCursoProfissionalizanteEnum;
use App\Domain\Educacao\Escola\Enums\UnidadesCurricularesEnum;
use App\Domain\Educacao\Escola\Models\CensoAreasPos;
use App\Domain\Educacao\Escola\Models\CensoCursoProfissionalizante;
use App\Domain\Educacao\Secretaria\Models\UnidadeCurricular;
use App\Domain\Educacao\Secretaria\Resources\UnidadeCurricularResource;
use App\Http\Controllers\Controller;
use ECidade\Enum\Educacao\Censo\AreasPosGraduacaoEnum;
use ECidade\Enum\Educacao\Censo\TiposPosGraduacaoEnum;
use Illuminate\Http\Request;

/**
 * Class CalendarioController
 * @package App\Domain\Educacao\Escola\Controllers
 */
class TabelasCensoController extends Controller
{
    public function getTiposPosGraduacao()
    {
        $tipos = TiposPosGraduacaoEnum::getAll();
        return new DBJsonResponse($tipos);
    }

    public function getAreasPosGraduacao()
    {
        $areas = [];
        $areasPos = CensoAreasPos::all()->toArray();
        foreach ($areasPos as $area) {
            $areas[$area['ed184_id']] = $area['ed184_descricao'];
        }
        return new DBJsonResponse($areas);
    }

    public function getCursosProfissionalizantes()
    {
        $cursos = CensoCursoProfissionalizante::all()->map(function ($curso) {
            $curso->tipo = (new TipoCursoProfissionalizanteEnum($curso->ed247_i_tipo))->descricao();
            return $curso;
        });
        return new DBJsonResponse($cursos);
    }

    public function getCursoProfissionalizantesByCodigo($codigo)
    {
        $curso = CensoCursoProfissionalizante::find($codigo);
        $curso->tipo = (new TipoCursoProfissionalizanteEnum($curso->ed247_i_tipo))->descricao();
        return new DBJsonResponse($curso);
    }

    public function getUnidadesCurriculares()
    {
        $unidades = UnidadeCurricular::all()->map(function ($unidade) {
            return UnidadeCurricularResource::toResponse($unidade);
        });
        return new DBJsonResponse($unidades);
    }

    public function salvarUnidadeCurricular(Request $request)
    {
        $parametros = $request->all();
        if (isset($parametros['codigo'])) {
            $retorno = UnidadeCurricular::find($parametros['codigo'])
                ->update(['ed199_descricao' => $parametros['nome']]);
        } else {
            $retorno = UnidadeCurricular::create(['ed199_descricao' => $parametros['nome']]);
        }
        return new DBJsonResponse($retorno);
    }

    public function excluirUnidadeCurricular($codigo)
    {
        return new DBJsonResponse(UnidadeCurricular::find($codigo)->delete());
    }
}
