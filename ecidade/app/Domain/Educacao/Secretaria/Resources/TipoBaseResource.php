<?php

namespace App\Domain\Educacao\Secretaria\Resources;

use App\Domain\Educacao\Secretaria\Models\TipoBase;
use ECidade\Enum\Educacao\Secretaria\EstruturaCurricularEnum;

class TipoBaseResource
{
    public static function toResponse(TipoBase $tipoBase)
    {
        return (object) [
            'codigo' => $tipoBase->ed182_id,
            'nome' => trim($tipoBase->ed182_descricao),
            'estruturaCurricular' => new EstruturaCurricularEnum($tipoBase->ed182_estrutura_curricular),
            'tipoItinerarioFormativo' => $tipoBase->ed182_tipo_itinerario_informativo,
            'composicaoItinerarioIntegrado' => $tipoBase->ed182_compos_itinerario_integrado,
            'tipoCurstoItinerarioTecnicoProfissional' => $tipoBase->ed182_tipo_curso_itinerario_tec_prof,
            'isItinerarioConcomitante' => $tipoBase->ed182_itinerario_concomitante,
            'isAtivo' => $tipoBase->ed182_ativo,
            'ordemHistorico' => $tipoBase->ed182_ordem_historico
        ];
    }
}
