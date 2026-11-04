<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\AtividadeProfissional;

class AtividadeProfissionalResource
{
    public static function toResponse(AtividadeProfissional $atividade)
    {
        return (object) [
            'codigo' => $atividade->ed01_i_codigo,
            'nome' => $atividade->ed01_c_descr,
            'isRegente' => $atividade->ed01_c_regencia === 'S',
            'atualiza' => $atividade->ed01_c_atualiz === 'S',
            'isDocente' => $atividade->ed01_c_docencia === 'S',
            "efetividade" => trim($atividade->ed01_c_efetividade),
            'exigeAto' => $atividade->ed01_c_exigeato === 'S',
            'funcaoAdmin' => $atividade->ed01_i_funcaoadmin,
            'funcaoAtividade' => $atividade->ed01_funcaoatividade,
            'atividadeEscola' => $atividade->ed01_atividadeescolar,
            'permissaoDiario' => $atividade->ed01_permissao_diario
        ];
    }
}
