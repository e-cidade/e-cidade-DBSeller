<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\ConteudoDesenvolvido;
use App\Domain\Educacao\Secretaria\Resources\DisciplinaResource;
use App\Domain\Educacao\Secretaria\Resources\TiposInstrumentosAvaliativosResource;

class ConteudoDesenvolvidoResource
{
    public static function toArrayModel($parametros)
    {
        $conteudo = (object) $parametros;
        return [
            'ed155_codigo' => isset($conteudo->codigo) ? $conteudo->codigo : null,
            'ed155_regencia' => $conteudo->regencia,
            'ed155_db_usuarios' => $conteudo->usuario,
            'ed155_data' => $conteudo->data,
            'ed155_conteudo' => trim($conteudo->conteudo),
            'ed155_turmaturnoreferente' => $conteudo->turmaTurnoReferente,
            'ed155_tipo_instrumento_avaliativo' =>
                !empty($conteudo->tipoInstrumento) ? $conteudo->tipoInstrumento : null,
            'ed155_aulas_dadas' => $conteudo->aulasDadas
        ];
    }

    public static function toResponse(ConteudoDesenvolvido $conteudo)
    {
        $aConteudo = [
            'codigo' => $conteudo->ed155_codigo,
            'regencia' => RegenciaResource::toResponse($conteudo->regencia),
            'disciplina' => DisciplinaResource::toResponse($conteudo->regencia->disciplinaEnsino->disciplina),
            'turma' => TurmaResource::toResponse($conteudo->regencia->turma, ['etapa', 'turnoReferente']),
            'usuarios' => $conteudo->ed155_db_usuarios,
            'data' => $conteudo->ed155_data,
            'conteudo' => trim($conteudo->ed155_conteudo),
            'turno' => TurnoResource::toResponse($conteudo->turmaTurnoReferente->turma->turno),
            'turnoReferente' => $conteudo->turmaTurnoReferente->turnoReferente->referencia->toOject(),
            'tipoInstrumento' => is_null($conteudo->tipoInstrumentoAvaliativo) ? null :
                TiposInstrumentosAvaliativosResource::toResponse($conteudo->tipoInstrumentoAvaliativo),
            'aulasDadas' => $conteudo->ed155_aulas_dadas
        ];

        if (isset($conteudo->totalRegistros)) {
            $aConteudo['totalRegistros'] = $conteudo->totalRegistros;
        }

        return (object) $aConteudo;
    }
}
