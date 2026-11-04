<?php

namespace App\Domain\Educacao\Secretaria\Resources;

use App\Domain\Educacao\Escola\Enums\ControleFrequenciaEnum;
use App\Domain\Educacao\Escola\Enums\MedidaFrequenciaEnum;
use App\Domain\Educacao\Escola\Resources\RegimeMatriculaResource;
use App\Domain\Educacao\Secretaria\Models\Base;

class BaseResource
{
    public static function toResponse(Base $base)
    {
        return (object) [
            'codigo' => $base->ed31_i_codigo,
            'curso' => CursoResource::toResponse($base->curso),
            'descricao' => trim($base->ed31_c_descr),
            'turno' => trim($base->ed31_c_turno),
            'medidaFrequencia' => (new MedidaFrequenciaEnum($base->ed31_c_medfreq))->value(),
            'controleFrequencia' => (new ControleFrequenciaEnum($base->ed31_c_contrfreq))->value(),
            'observacao' => $base->ed31_t_obs,
            'conclusao' => $base->ed31_c_conclusao === 'S' ? true : false,
            'regimeMatricula' => (object)[
                'codigo' => $base->regimeMatricula->ed218_i_codigo,
                'nome' => trim($base->regimeMatricula->ed218_c_nome),
                'divisoes' => $base->regimeMatricula->divisoes->map(function ($divisao) {
                    return RegimeMatriculaResource::toResponseDivisao($divisao);
                })
            ],
            'isAtiva' => $base->ed31_c_ativo === 'S' ? true : false,
            'etapaInicial' =>  (object)[
                'codigo' => $base->baseEtapa->etapaInicial->ed11_i_codigo,
                'nome' => trim($base->baseEtapa->etapaInicial->ed11_c_descr)
            ],
            'etapaFinal' =>  (object)[
                'codigo' => $base->baseEtapa->etapaFinal->ed11_i_codigo,
                'nome' => trim($base->baseEtapa->etapaFinal->ed11_c_descr)
            ],
            'divisoesRegimeMatricula' => $base->divisoesRegimeMatricula->map(function ($baseDivisao) {
                return (object)[
                    'codigo' => $baseDivisao->regimeMatriculaDivisao->ed219_i_codigo,
                    'nome' => trim($baseDivisao->regimeMatriculaDivisao->ed219_c_nome)
                ];
            }),
            'editada' => $base->editada
        ];
    }

    public static function toArrayModel($parametros)
    {
        return [
            'ed31_i_curso' => $parametros->curso,
            'ed31_c_descr' => trim($parametros->nome),
            'ed31_c_turno' => trim($parametros->turno),
            'ed31_c_medfreq' => $parametros->frequencia,
            'ed31_c_contrfreq' => $parametros->controleFrequencia,
            'ed31_t_obs' => $parametros->observacao,
            'ed31_c_conclusao' => $parametros->concluiCurso ? 'S' : 'N',
            'ed31_i_regimemat' => $parametros->regimeMatricula,
            'ed31_c_ativo' => $parametros->isAtiva ? 'S' : 'N'
        ];
    }
}
