<?php

namespace App\Domain\Educacao\Escola\Services;

use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AproveitamentoAvaliacaoParcial;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AvaliacaoParcial;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\ProcedimentoResultadoParcial;
use App\Domain\Educacao\Escola\Models\PeriodoAvaliacao as PeriodoAvaliacaoLaravel;
use App\Domain\Educacao\Escola\Repositories\AproveitamentoAvaliacaoParcialRepository;
use App\Domain\Educacao\Escola\Repositories\AvaliacaoParcialRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use PeriodoAvaliacao;
use Regencia;

class AvaliacoesParciaisService
{
    /**
     * @var AvaliacaoParcialRepository
     */
    private $avaliacaoParcialRepository;

    /**
     * @var AproveitamentoAvaliacaoParcialRepository
     */
    private $aproveitamentoAvaliacaoParcialRepository;

    public function __construct()
    {
        $this->avaliacaoParcialRepository = new AvaliacaoParcialRepository();
        $this->aproveitamentoAvaliacaoParcialRepository = new AproveitamentoAvaliacaoParcialRepository();
    }
    /**
     * @param Regencia $regencia
     * @param PeriodoAvaliacao $periodoavaliacao
     * @return AvaliacaoParcial[]
     * @throws Exception
     */
    public function getAvaliacoesRegenciaPeriodo(Regencia $regencia, PeriodoAvaliacao $periodoavaliacao)
    {
        $periodoavaliacao = PeriodoAvaliacaoLaravel::find($periodoavaliacao->getCodigo());
        return $this->avaliacaoParcialRepository->getAvaliacoesByRegenciaPeriodo($regencia, $periodoavaliacao);
    }

    /**
     * @param Regencia $regencia
     * @param PeriodoAvaliacao $periodoavaliacao
     * @return AvaliacaoParcial
     * @throws Exception
     */
    public function adicionarAvaliacoesRegenciaPeriodo(Regencia $regencia, PeriodoAvaliacao $periodoavaliacao)
    {
        $procavaliacao = null;
        foreach ($regencia->getProcedimentoAvaliacao()->getAvaliacoes() as $avaliacaoPeriodica) {
            if ($avaliacaoPeriodica->getPeriodoAvaliacao()->getCodigo() === $periodoavaliacao->getCodigo()) {
                $procavaliacao = $avaliacaoPeriodica;
            }
        }

        if (is_null($procavaliacao)) {
            throw new Exception("Erro ao buscar Avaliação Períodica no Procedimento da Disciplina informada.");
        }

        $avaliacaoParcial = AvaliacaoParcial::query()->orderBy('ed340_ordem', 'desc')
            ->regencia($regencia)
            ->avaliacaoPeriodica($procavaliacao)
            ->first();

        return AvaliacaoParcial::create([
            'ed340_regencia' => $regencia->getCodigo(),
            'ed340_procavaliacao' => $procavaliacao->getCodigo(),
            'ed340_ordem' => $avaliacaoParcial->ed340_ordem + 1,
        ]);
    }

    /**
     * @param $codigoDiario
     * @param AvaliacaoParcial $avalicaoParcial
     * @return AproveitamentoAvaliacaoParcial
     */
    public function getAproveitamentoAluno($codigoDiario, AvaliacaoParcial $avalicaoParcial)
    {
        return $this->aproveitamentoAvaliacaoParcialRepository->getAproveitamentoAluno(
            $codigoDiario,
            $avalicaoParcial
        );
    }

    /**
     * @param Regencia $regencia
     * @param PeriodoAvaliacao $periodoavaliacao
     * @return ProcedimentoResultadoParcial|Model
     * @throws Exception
     */
    public function getResultadoRegenciaPeriodo(Regencia $regencia, PeriodoAvaliacao $periodoavaliacao)
    {
        $procavaliacao = null;
        foreach ($regencia->getProcedimentoAvaliacao()->getAvaliacoes() as $avaliacaoPeriodica) {
            if ($avaliacaoPeriodica->getPeriodoAvaliacao()->getCodigo() === $periodoavaliacao->getCodigo()) {
                $procavaliacao = $avaliacaoPeriodica;
            }
        }

        if (is_null($procavaliacao)) {
            throw new Exception("Erro ao buscar Avaliação Períodica no Procedimento da Disciplina informada.");
        }

        $resultadoParcial = ProcedimentoResultadoParcial::query()
            ->regencia($regencia)
            ->avaliacaoPeriodica($procavaliacao)
            ->first();

        if (is_null($resultadoParcial)) {
            return ProcedimentoResultadoParcial::create([
                'ed342_regencia' => $regencia->getCodigo(),
                'ed342_procavaliacao' => $procavaliacao->getCodigo(),
                'ed342_formaobtencao' => 'ME',
            ]);
        }

        return $resultadoParcial;
    }
}
