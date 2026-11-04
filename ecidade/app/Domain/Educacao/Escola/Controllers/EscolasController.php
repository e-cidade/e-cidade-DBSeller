<?php

namespace App\Domain\Educacao\Escola\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\AtividadeProfissionalEscola;
use App\Domain\Educacao\Escola\Models\Disciplina;
use App\Domain\Educacao\Escola\Models\Ensino;
use App\Domain\Educacao\Escola\Models\Escola;
use App\Domain\Educacao\Escola\Models\EtapaRegimeMatricula;
use App\Domain\Educacao\Escola\Models\RegimeMatricula;
use App\Domain\Educacao\Escola\Resources\RegimeMatriculaResource;
use App\Domain\Educacao\Secretaria\Resources\DisciplinaResource;
use App\Http\Controllers\Controller;
use App\Domain\Educacao\Escola\Models\EscolaDiretor;

class EscolasController extends Controller
{
    public function getEscolas()
    {
        return new DBJsonResponse(Escola::select(['ed18_i_codigo', 'ed18_c_nome'])
            ->orderBy('ed18_c_nome')->get());
    }

    public function getEscola($escola)
    {
        $campos = ['ed18_i_codigo', 'ed18_c_nome'];
        $escola = Escola::select($campos)->find($escola);
        return new DBJsonResponse($escola);
    }

    public function getDiretores($escola)
    {
        $diretores = EscolaDiretor::selectRaw("distinct
            ed20_i_codigo as codigo_rechumano,
            case when ed20_i_tiposervidor = 1 then trim(cgmrh.z01_nome) else trim(cgmcgm.z01_nome) end as nome,
            trim(ed83_c_descr) as descricao_tipo_ato_legal,
            ed05_c_numero as numero_ato_legal
        ")
        ->leftJoin('atolegal', 'atolegal.ed05_i_codigo', '=', 'escoladiretor.ed254_i_atolegal')
        ->leftJoin('tipoato', 'tipoato.ed83_i_codigo', '=', 'atolegal.ed05_i_tipoato')
        ->leftJoin('turno', 'turno.ed15_i_codigo', '=', 'escoladiretor.ed254_i_turno')
        ->join('rechumano', 'rechumano.ed20_i_codigo', '=', 'escoladiretor.ed254_i_rechumano')
        ->leftJoin('rechumanoescola', 'rechumanoescola.ed75_i_rechumano', '=', 'rechumano.ed20_i_codigo')
        ->leftJoin('rechumanoativ', 'rechumanoativ.ed22_i_rechumanoescola', '=', 'rechumanoescola.ed75_i_codigo')
        ->leftJoin('atividaderh', 'atividaderh.ed01_i_codigo', '=', 'rechumanoativ.ed22_i_atividade')
        ->leftJoin('rechumanopessoal', 'rechumanopessoal.ed284_i_rechumano', '=', 'rechumano.ed20_i_codigo')
        ->leftJoin('rhpessoal', 'rhpessoal.rh01_regist', '=', 'rechumanopessoal.ed284_i_rhpessoal')
        ->leftJoin('rhpessoalmov', function ($join) {
            $join->on('rhpessoalmov.rh02_regist', '=', 'rhpessoal.rh01_regist')
                ->where('rhpessoalmov.rh02_anousu', '=', date('y'))
                ->where('rhpessoalmov.rh02_mesusu', '=', date('m'))
                ->where('rhpessoalmov.rh02_instit', '=', intval(db_getsession("DB_instit")));
        })
        ->leftJoin('rhfuncao', function ($join) {
            $join->on('rhfuncao.rh37_funcao', '=', 'rhpessoal.rh01_funcao')
                ->on('rhfuncao.rh37_instit', '=', 'rhpessoalmov.rh02_instit');
        })
        ->leftJoin('cgm as cgmrh', 'cgmrh.z01_numcgm', '=', 'rhpessoal.rh01_numcgm')
        ->leftJoin('rechumanocgm', 'rechumanocgm.ed285_i_rechumano', '=', 'rechumano.ed20_i_codigo')
        ->leftJoin('cgm as cgmcgm', 'cgmcgm.z01_numcgm', '=', 'rechumanocgm.ed285_i_cgm')
        ->where('escoladiretor.ed254_i_escola', $escola)
        ->where('escoladiretor.ed254_c_tipo', 'A')
        ->where('atividaderh.ed01_i_funcaoadmin', 2)->get();

        return new DBJsonResponse($diretores);
    }

    public function getSecretarios($escola)
    {
        $dataAtual = date("Y-m-d");
        $secretarios = AtividadeProfissionalEscola::selectRaw("distinct
            ed20_i_codigo as codigo_rechumano,
            case when ed20_i_tiposervidor = 1 then trim(cgmrh.z01_nome) else trim(cgmcgm.z01_nome) end as nome,
            trim(ed83_c_descr) as descricao_tipo_ato_legal,
            ed05_c_numero as numero_ato_legal
        ")
        ->join('rechumanoescola', 'rechumanoescola.ed75_i_codigo', '=', 'rechumanoativ.ed22_i_rechumanoescola')
        ->join('rechumano', 'rechumano.ed20_i_codigo', '=', 'rechumanoescola.ed75_i_rechumano')
        ->leftJoin('atividaderh', 'atividaderh.ed01_i_codigo', '=', 'rechumanoativ.ed22_i_atividade')
        ->leftJoin('escoladiretor', 'escoladiretor.ed254_i_rechumano', '=', 'rechumano.ed20_i_codigo')
        ->leftJoin('turno', 'turno.ed15_i_codigo', '=', 'escoladiretor.ed254_i_turno')
        ->leftJoin('atolegal', 'atolegal.ed05_i_codigo', '=', 'rechumanoativ.ed22_i_atolegal')
        ->leftJoin('tipoato', 'tipoato.ed83_i_codigo', '=', 'atolegal.ed05_i_tipoato')
        ->leftJoin('rechumanopessoal', 'rechumanopessoal.ed284_i_rechumano', '=', 'rechumano.ed20_i_codigo')
        ->leftJoin('rhpessoal', 'rhpessoal.rh01_regist', '=', 'rechumanopessoal.ed284_i_rhpessoal')
        ->leftJoin('cgm as cgmrh', 'cgmrh.z01_numcgm', '=', 'rhpessoal.rh01_numcgm')
        ->leftJoin('rhpessoalmov', function ($join) {
            $join->on('rhpessoalmov.rh02_regist', '=', 'rhpessoal.rh01_regist')
                ->where('rhpessoalmov.rh02_anousu', '=', date('y'))
                ->where('rhpessoalmov.rh02_mesusu', '=', date('m'))
                ->where('rhpessoalmov.rh02_instit', '=', intval(db_getsession("DB_instit")));
        })
        ->leftJoin('rhfuncao', function ($join) {
            $join->on('rhfuncao.rh37_funcao', '=', 'rhpessoal.rh01_funcao')
                ->on('rhfuncao.rh37_instit', '=', 'rhpessoalmov.rh02_instit');
        })
        ->leftJoin('rechumanocgm', 'rechumanocgm.ed285_i_rechumano', '=', 'rechumano.ed20_i_codigo')
        ->leftJoin('cgm as cgmcgm', 'cgmcgm.z01_numcgm', '=', 'rechumanocgm.ed285_i_cgm')
        ->where('rechumanoescola.ed75_i_escola', $escola)
        ->where('atividaderh.ed01_i_funcaoadmin', 3)
        ->whereRaw("(rechumanoativ.ed22_datafim is null OR rechumanoativ.ed22_datafim >= '{$dataAtual}')")->get();

        return new DBJsonResponse($secretarios);
    }

    public function getRegimesMatriculaByEnsino($escola, $ensino)
    {
        $etapas = Ensino::find($ensino)->etapas;
        $regimes = [];
        foreach ($etapas as $etapa) {
            foreach ($etapa->serieRegimeMatricula as $serieRegime) {
                $regimes[] = RegimeMatriculaResource::toResponse($serieRegime->regimeMatricula);
            }
        }
        $regimes = array_unique($regimes, SORT_REGULAR);
        return new DBJsonResponse(array_values($regimes));
    }

    public function getRegimeMatriculaByCodigo($escola, $codigo)
    {
        $regime = RegimeMatriculaResource::toResponse(RegimeMatricula::find($codigo));
        return new DBJsonResponse($regime);
    }
}
