<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Configuracao\Usuario\Models\UsuarioCgm;
use App\Domain\Educacao\Escola\Models\AtividadeProfissional;
use App\Domain\Educacao\Escola\Models\ProfissionalEscola;
use App\Domain\Educacao\Escola\Models\Views\ProfissionaisEscolasView;

class ProfissionaisEscolaResource
{
    public static function toResponse(ProfissionaisEscolasView $profissionalEscola)
    {
        $retorno = [
            'cod_rechumanoescola' => $profissionalEscola->cod_rechumano_escola,
            'escola' => EscolaResource::toResponse($profissionalEscola->escola),
            'cod_rechumano' =>  $profissionalEscola->cod_rechumano,
            'profissional' => ProfissionalResource::toResponse($profissionalEscola->profissional),
            'dataIngresso' => $profissionalEscola->profissionalEscola->ed75_d_ingresso,
            'simultaneo' => $profissionalEscola->profissionalEscola->ed75_c_simultaneo === 'S',
            'dataSaida' => $profissionalEscola->profissionalEscola->ed75_i_saidaescola,
            'atividades' => $profissionalEscola->profissionalEscola->atividades->map(function ($atividade) {
                return AtividadeProfissionalResource::toResponse($atividade->atividade);
            }),
            'nome' => $profissionalEscola->cgm->z01_nome,
            'cgm' => $profissionalEscola->cod_cgm,
            'dataNascimento' => $profissionalEscola->cgm->z01_nasc,
            'cpf' => $profissionalEscola->cgm->z01_cgccpf,
            'matricula' => $profissionalEscola->matricula,
            'usuarioInterno' => $profissionalEscola->usuarioInterno
        ];

        if ($profissionalEscola->total !== null) {
            $retorno['total'] = $profissionalEscola->total;
        }
         return $retorno;
    }
}
