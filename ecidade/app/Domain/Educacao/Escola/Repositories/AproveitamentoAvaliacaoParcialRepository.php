<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AproveitamentoAvaliacaoParcial;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AvaliacaoParcial;

class AproveitamentoAvaliacaoParcialRepository extends BaseRepository
{
    protected $modelClass = AvaliacaoParcial::class;

    /**
     * @param $codigoDiario
     * @param AvaliacaoParcial $avalicaoParcial
     * @return AproveitamentoAvaliacaoParcial
     */
    public function getAproveitamentoAluno($codigoDiario, AvaliacaoParcial $avalicaoParcial)
    {
        $avalicaoParcial->refresh();
        $aproveitamentos = $avalicaoParcial->aproveitamentos;

        foreach ($aproveitamentos as $aproveitamentoAvaliacaoParcial) {
            if ($aproveitamentoAvaliacaoParcial->ed341_diario == $codigoDiario) {
                return $aproveitamentoAvaliacaoParcial;
            }
        }

        return AproveitamentoAvaliacaoParcial::create([
            "ed341_diario" => $codigoDiario,
            "ed341_procavaliacaoparcial" => $avalicaoParcial->ed340_codigo,
            "ed341_valornota" => null,
            "ed341_valornivel" => ''
        ]);
    }
}
