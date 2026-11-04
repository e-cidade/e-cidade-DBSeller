<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\AlunoAtendimentoEspecial;

class AtendimentoEspecialResource
{
    /**
     * @param array $atendimento
     * @return array
     */
    public static function toArrayModel($atendimento)
    {
        $atendimento = (object) $atendimento;
        return [
            'ed197_aluno' => $atendimento->aluno,
            'ed197_atendimentos' => json_encode($atendimento->atendimentos),
            'ed197_data_emissao' => $atendimento->data,
            'ed197_parecer' => trim($atendimento->parecer)
        ];
    }

    public static function toResponse(AlunoAtendimentoEspecial $atendimento)
    {
 
        return (object) [
            'codigo' => $atendimento->ed197_codigo,
            'aluno' => $atendimento->ed197_aluno,
            'atendimentos' => json_decode($atendimento->ed197_atendimentos),
            'data' => $atendimento->ed197_data_emissao,
            'parecer' => trim($atendimento->ed197_parecer)
        ];
    }
}
