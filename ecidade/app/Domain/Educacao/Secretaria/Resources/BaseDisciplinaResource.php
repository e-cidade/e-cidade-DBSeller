<?php

namespace App\Domain\Educacao\Secretaria\Resources;

use App\Domain\Educacao\Escola\Resources\AreaConhecimentoResource;
use App\Domain\Educacao\Secretaria\Models\BaseDisciplina;

class BaseDisciplinaResource
{
    public static function toResponse(BaseDisciplina $base)
    {
        return (object) [
            'codigo' => $base->ed34_i_codigo,
            'base' => $base->ed34_i_base,
            'serie' => $base->ed34_i_serie,
            'disciplina' => DisciplinaResource::toResponse($base->disciplinaEnsino->disciplina),
            'disciplinaEnsino' => $base->disciplinaEnsino->ed12_i_codigo,
            'horasAula' => $base->ed34_i_qtdperiodo,
            'cargaHoraria' => $base->ed34_i_chtotal,
            'matricula' => $base->ed34_c_condicao === 'OB' ?
                (object)['codigo' => 'OB', 'nome' =>'Obrigatória'] : (object)['codigo' => 'OP', 'nome' => "Opcional"],
            'ordenacao' => $base->ed34_i_ordenacao,
            'lancarHistorico' => $base->ed34_lancarhistorico,
            'isGlobalizada' => $base->ed34_disiciplinaglobalizada,
            'isCaraterReprobatorio' => $base->ed34_caracterreprobatorio,
            'isBaseComum' => $base->ed34_basecomum,
            'areaConhecimento' =>
                AreaConhecimentoResource::toResponse($base->disciplinaEnsino->disciplina->areaConhecimento),
            'procedimento' => $base->ed34_procedimento,
            'tipoBase' => TipoBaseResource::toResponse($base->tipoBase),
            'unidadeCurricular' => is_null($base->unidadeCurricular) ?
                    null :
                    UnidadeCurricularResource::toResponse($base->unidadeCurricular)
        ];
    }

    public static function toArrayModel($parametros)
    {
        $array = [
            'ed34_i_codigo' => isset($parametros->codigo) ? $parametros->codigo : null,
            'ed34_i_base' => intval($parametros->base),
            'ed34_i_serie' => intval($parametros->serie),
            'ed34_i_disciplina' => intval($parametros->disciplinaEnsino),
            'ed34_i_qtdperiodo' => intval($parametros->horasAula),
            'ed34_i_chtotal' => null,
            'ed34_c_condicao' => $parametros->matricula,
            'ed34_i_ordenacao' => isset($parametros->ordenacao) ? $parametros->ordenacao : null,
            'ed34_lancarhistorico' => $parametros->lancarHistorico == 1,
            'ed34_disiciplinaglobalizada' => $parametros->isGlobalizada == 1,
            'ed34_caracterreprobatorio' => $parametros->isCaraterReprobatorio == 1,
            'ed34_basecomum' => $parametros->tipoBase === 1,
            'ed34_areaconhecimento' => intval($parametros->areaConhecimento),
            'ed34_procedimento' => null,
            'ed34_tipobase' => intval($parametros->tipoBase),
            'ed34_unidade_curricular' =>
                $parametros->unidadeCurricular === '' || is_null($parametros->unidadeCurricular) ?
                null :
                intval($parametros->unidadeCurricular)
        ];
        return $array;
    }
}
