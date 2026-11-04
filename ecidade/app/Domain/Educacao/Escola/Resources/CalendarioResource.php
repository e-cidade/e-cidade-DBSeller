<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\Calendario;

class CalendarioResource
{

    public static function toResponse(Calendario $calendario, $relations = [])
    {
        $objModel = [
            'codigo' => $calendario->ed52_i_codigo,
            'nome' => trim($calendario->ed52_c_descr),
            'duaracao' => $calendario->ed52_i_duracaocal,
            'ano' => $calendario->ed52_i_ano,
            'periodo' => $calendario->ed52_i_periodo,
            'inicio' => $calendario->ed52_d_inicio,
            'fim' => $calendario->ed52_d_fim,
            'resultadoFinal' => $calendario->ed52_d_resultfinal,
            'temAulasSabado' => $calendario->ed52_c_aulasabado,
            'diasLetivos' => $calendario->ed52_i_diasletivos,
            'semLetivas' => $calendario->ed52_i_semletivas,
            'calendarioAnterior' => $calendario->ed52_i_calendant,
            'isAtivo' => $calendario->ed52_c_passivo,
            'observacao' => $calendario->ed52_t_obs,
            ];

        if (in_array('turmas', $relations)) {
            $objModel['turmas'] = $calendario->turmas->map(function ($turma) {
                return TurmaResource::toResponse($turma);
            });
        }

        return (object) $objModel;
    }

    /**
     * @param array $calendarios
     * @return array
     */
    public static function toArray(array $calendarios)
    {
        $data = array();
        foreach ($calendarios as $calendario) {
            $data[] = (object) array(
                'id' => $calendario->ed52_i_codigo,
                'descricao' => trim($calendario->ed52_c_descr),
                'ano' => $calendario->ed52_i_ano
            );
        }
        return $data;
    }
}
