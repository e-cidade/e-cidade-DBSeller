<?php

namespace App\Domain\Educacao\Secretaria\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\BaseAto;
use App\Domain\Educacao\Escola\Models\BaseAtoSerie;
use App\Domain\Educacao\Escola\Models\EscolaBase;
use App\Domain\Educacao\Escola\Models\Etapa;
use App\Domain\Educacao\Secretaria\Models\Base;
use App\Domain\Educacao\Secretaria\Models\BaseEtapa;
use App\Domain\Educacao\Secretaria\Models\BaseRegimeMatriculaDivisao;
use App\Domain\Educacao\Secretaria\Resources\BaseDisciplinaResource;
use App\Domain\Educacao\Secretaria\Resources\BaseResource;

class BaseCurricularRepository extends BaseRepository
{
    protected $modelClass = Base::class;

    public function index()
    {
        $bases = $this->newQuery()
            ->leftJoin('escolabase', 'ed77_i_base', '=', 'ed31_i_codigo')
            ->whereNull('ed77_i_codigo')->get();
        return $bases;
    }

    public function indexEscola($escola)
    {
        $bases = $this->newQuery()->join('escolabase', 'ed77_i_base', '=', 'ed31_i_codigo')
            ->where('ed77_i_escola', $escola)->get();
        return $bases;
    }

    public function salvar($parametros)
    {
        $dadosBase = BaseResource::toArrayModel((object)$parametros);
        if (isset($parametros['codigo']) && !empty($parametros['codigo'])) {
            $baseEditada = tap($this->newQuery()->find($parametros['codigo']))->update($dadosBase);
            $baseEditada->editada = true;
            return $this->extracted($baseEditada, $parametros);
        } else {
            $baseCriada = $this->newQuery()->create($dadosBase);
            $baseCriada->editada = false;
            return $this->extracted($baseCriada, $parametros);
        }
    }

    public function salvarEscola($escola, Base $baseCriada, $curso, $baseContinuacao = null)
    {
        $escolaBase = EscolaBase::updateOrCreate([
            'ed77_i_base' => $baseCriada->ed31_i_codigo,
            'ed77_i_escola' => $escola
        ], [
            'ed77_i_base' => $baseCriada->ed31_i_codigo,
            'ed77_i_escola' => $escola,
            'ed77_i_basecont' => $baseContinuacao
        ]);
        $sequenciaInicial = Etapa::find($baseCriada->etapaInicial)->ed11_i_sequencia;
        $sequenciaFinal = Etapa::find($baseCriada->etapaFinal)->ed11_i_sequencia;
        $cursoAtosSerie = $this->getCursosAtosSerie($curso, $escola, $sequenciaInicial, $sequenciaFinal);

        $iAto = -1;
        foreach ($cursoAtosSerie as $ato) {
            if ($iAto != $ato->ed215_i_atolegal) {
                $baseAto = $this->salvarBaseAtos($escolaBase->ed77_i_codigo, $ato->ed215_i_atolegal);
                $this->salvarBaseAtosSerie($baseAto, $ato->ed216_i_serie);
            }
            $iAto = $ato->ed215_i_atolegal;
        }
        return $escolaBase;
    }

    public function salvarBaseEtapa(Base $base, $etapaInicial, $etapaFinal)
    {
        BaseEtapa::updateOrCreate(['ed87_i_codigo' => $base->ed31_i_codigo], [
            'ed87_i_serieinicial' => $etapaInicial,
            'ed87_i_seriefinal' => $etapaFinal
        ]);
    }

    public function salvarBaseRegimeMatriculaDivisao(Base $base, $divisoes)
    {
        foreach ($divisoes as $divisao) {
            BaseRegimeMatriculaDivisao::updateOrCreate([
                'ed224_i_base' => $base->ed31_i_codigo,
                'ed224_i_regimematdiv' => $divisao['codigo']
            ], [
                'ed224_i_base' => $base->ed31_i_codigo,
                'ed224_i_regimematdiv' => $divisao['codigo']
            ]);
        }
    }

    public function salvarBaseAtos($baseEscola, $ato)
    {
        return BaseAto::updateOrCreate([
               'ed278_i_escolabase' => $baseEscola,
               'ed278_i_atolegal' => $ato
        ], [
            'ed278_i_escolabase' => $baseEscola,
            'ed278_i_atolegal' => $ato
        ]);
    }

    public function salvarBaseAtosSerie($baseAto, $serie)
    {
        return BaseAtoSerie::updateOrCreate([
            'ed279_i_baseato' => $baseAto->ed278_i_codigo,
            'ed279_i_serie' => $serie
        ], [
            'ed279_i_baseato' => $baseAto->ed278_i_codigo,
            'ed279_i_serie' => $serie
        ]);
    }

    public function getCursosAtosSerie($curso, $escola, $seqI, $seqF)
    {
        return \DB::table('cursoatoserie')->select(['ed215_i_atolegal', 'ed216_i_serie'])
            ->join('cursoato', 'ed215_i_codigo', '=', 'ed216_i_cursoato')
            ->join('serie', 'ed11_i_codigo', '=', 'ed216_i_serie')
            ->join('atolegal', 'ed05_i_codigo', '=', 'ed215_i_atolegal')
            ->join('cursoescola', 'ed71_i_codigo', '=', 'ed215_i_cursoescola')
            ->join('ensino', 'ed10_i_codigo', 'ed11_i_ensino')
            ->where('ed71_i_curso', $curso)
            ->where('ed71_i_escola', $escola)
            ->whereRaw("ed11_i_sequencia between {$seqI} and {$seqF}")
            ->distinct()
            ->get();
    }

    /**
     * @param $baseEditada
     * @param $parametros
     * @return mixed
     */
    public function extracted($baseEditada, $parametros)
    {
        $this->salvarBaseEtapa($baseEditada, $parametros['etapaInicial'], $parametros['etapaFinal']);
        $this->salvarBaseRegimeMatriculaDivisao($baseEditada, $parametros['divisoesRegimeMatricula']);
        $baseEditada->etapaInicial = $parametros['etapaInicial'];
        $baseEditada->etapaFinal = $parametros['etapaFinal'];
        if (isset($parametros['escola'])) {
            $baseEditada->baseEscola =
                $this->salvarEscola($parametros['escola'], $baseEditada, $baseEditada->ed31_i_curso, null);
        }

        if (isset($parametros['codigoBaseImportada']) && !empty($parametros['codigoBaseImportada'])) {
            $baseImportada = $this->newQuery()->find($parametros['codigoBaseImportada']);
            $baseImportada->disciplinasBase->each(function ($disciplina) use ($baseEditada) {
                $disciplina = BaseDisciplinaResource::toResponse($disciplina);
                $disciplina->codigo = null;
                $disciplina->base = $baseEditada->ed31_i_codigo;
                $disciplina->areaConhecimento = $disciplina->areaConhecimento->codigo;
                $disciplina->tipoBase = $disciplina->tipoBase->codigo;
                $disciplina->matricula = $disciplina->matricula->codigo;
                $disciplina->unidadeCurricular = is_null($disciplina->unidadeCurricular) ? null :
                    $disciplina->unidadeCurricular->codigo;
                $disciplina = BaseDisciplinaResource::toArrayModel($disciplina);
                $respository = new BaseDisciplinaRepository();
                $respository->salvar($disciplina);
            });
        }
        return $baseEditada;
    }


    public function excluir($base, $escola = null)
    {
        $this->excluirBaseEtapa($base);
        $this->excluirBaseRegimeMatriculaDivisao($base);
        if (!is_null($escola)) {
            $this->excluirEscolaBase($base);
        }
        $this->newQuery()->find($base)->delete();
    }

    public function excluirBaseEtapa($base)
    {
        BaseEtapa::where('ed87_i_codigo', $base)->delete();
    }

    public function excluirBaseRegimeMatriculaDivisao($base)
    {
        BaseRegimeMatriculaDivisao::where('ed224_i_base', $base)->delete();
    }

    public function excluirEscolaBase($base)
    {
        $codigos =  EscolaBase::where('ed77_i_base', $base)->get()->map(function ($escolabase) {
            return $escolabase->ed77_i_codigo;
        });
        $this->excluirBaseAtos($codigos);
        EscolaBase::where('ed77_i_base', $base)->delete();
    }

    public function excluirBaseAtos($escolabase)
    {

        $codigos = BaseAto::whereIn('ed278_i_escolabase', $escolabase)->get()
            ->map(function ($baseato) {
                return $baseato->ed278_i_codigo;
            });
        $this->excluirBaseAtosSerie($codigos);
        BaseAto::whereIn('ed278_i_escolabase', $escolabase)->delete();
    }

    public function excluirBaseAtosSerie($baseatos)
    {
        BaseAtoSerie::whereIn('ed279_i_baseato', $baseatos)->delete();
    }

    public function getDisciplinasCursoBase($base, $etapa)
    {
        $baseDisciplinaRepository = new BaseDisciplinaRepository();
        $disciplinasCurso = $this->newQuery()->find($base)->curso->ensino->disciplinas;
        $disciplinasBaseEtapa = $baseDisciplinaRepository->getPorBaseEtapa($base, $etapa)->map(function ($disciplina) {
            return $disciplina->disciplinaEnsino;
        });
        return $disciplinasCurso->diff($disciplinasBaseEtapa);
    }

    public function salvarBaseContinuacao($parametros)
    {
        $baseEscola = $parametros['baseEscola'];
        $baseContinuacao = $parametros['baseContinuacao'];
        return EscolaBase::find($baseEscola)->update(['ed77_i_basecont' => $baseContinuacao]);
    }

    public function excluirBaseAto($codigo)
    {
        BaseAtoSerie::where('ed279_i_baseato', $codigo)->delete();
        return BaseAto::find($codigo)->delete();
    }
}
