<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AproveitamentoAvaliacaoParcial;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AvaliacaoParcial;
use App\Domain\Educacao\Escola\Models\PeriodoAvaliacao;
use Exception;
use \Regencia;

class AvaliacaoParcialRepository extends BaseRepository
{
    protected $modelClass = AvaliacaoParcial::class;

    /**
     * @param Regencia $regencia
     * @param PeriodoAvaliacao $periodoAvaliacao
     * @return AvaliacaoParcial[]
     * @throws Exception
     */
    public function getAvaliacoesByRegenciaPeriodo(Regencia $regencia, PeriodoAvaliacao $periodoAvaliacao)
    {
        $procavaliacao = null;
        foreach ($regencia->getProcedimentoAvaliacao()->getAvaliacoes() as $avaliacaoPeriodica) {
            if ($avaliacaoPeriodica->getPeriodoAvaliacao()->getCodigo() == $periodoAvaliacao->ed09_i_codigo) {
                $procavaliacao = $avaliacaoPeriodica;
            }
        }

        if (is_null($procavaliacao)) {
            throw new Exception("Erro ao buscar Avaliação Períodica no Procedimento da Disciplina informada.");
        }

        $avaliacoesParciais = AvaliacaoParcial::query()->regencia($regencia)->avaliacaoPeriodica($procavaliacao)->get();

        if ($avaliacoesParciais->count() == 0) {
            $avaliacaoParcial = AvaliacaoParcial::create([
                'ed340_regencia' => $regencia->getCodigo(),
                'ed340_procavaliacao' => $procavaliacao->getCodigo(),
                'ed340_ordem' => 1,
            ]);
            $avaliacaoParcial2 = AvaliacaoParcial::create([
                'ed340_regencia' => $regencia->getCodigo(),
                'ed340_procavaliacao' => $procavaliacao->getCodigo(),
                'ed340_ordem' => 2,
            ]);
            return [$avaliacaoParcial, $avaliacaoParcial2];
        }

        return $avaliacoesParciais;
    }
}
