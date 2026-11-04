<?php

namespace App\Domain\Educacao\Escola\Services;

use App\Domain\Educacao\Escola\Models\ExcecaoBloqueioNota;
use App\Domain\Educacao\Escola\Models\ParametrosBloqueioNota;
use DateTime;
use Exception;
use Illuminate\Support\Collection;
use PeriodoCalendario;
use Regencia;

class BloqueioNotasService
{
    const ACESSO_PARCIAIS = 1;
    const ACESSO_DIARIO = 2;

    private $tipoBloqueio;
    private $diasAposFinal;
    /**
     * @var ExcecaoBloqueioNota[]
     */
    private $excecoes = [];
    private $acessoOrigem;

    public function __construct($tipoBloqueio, $diasAposFinal, $acessoOrigem)
    {
        $this->tipoBloqueio = $tipoBloqueio;
        $this->diasAposFinal = $diasAposFinal;
        $this->acessoOrigem = $acessoOrigem;
    }

    public function adicionarExcecao($excessao)
    {
        if ($excessao instanceof Collection) {
            foreach ($excessao as $excessaoBloqueio) {
                $this->excecoes[] = $excessaoBloqueio;
            }
        } else {
            $this->excecoes[] = $excessao;
        }
    }

    /**
     * @param Regencia $regencia
     * @param PeriodoCalendario $periodoCalendario
     * @return bool
     * @throws Exception
     */
    public function podeLancarNota(Regencia $regencia, PeriodoCalendario $periodoCalendario)
    {
        if ($this->tipoBloqueio === ParametrosBloqueioNota::SEM_BLOQUEIO) {
            return true;
        }

        $dataAtual = new DateTime('NOW');
        $dataLimite = new DateTime($periodoCalendario->getDataTermino()->getDate());
        if ($this->diasAposFinal > 0) {
            $dataLimite->modify("+{$this->diasAposFinal} days");
        }
        $dataLimite = $this->getDataLimiteByExcessoes($regencia, $dataLimite, $periodoCalendario);

        if ($this->tipoBloqueio === ParametrosBloqueioNota::BLOQUEIO_TUDO) {
            return $dataAtual->format('Y-m-d') <= $dataLimite->format('Y-m-d');
        } elseif ($this->tipoBloqueio === ParametrosBloqueioNota::BLOQUEIO_NOTAS_PARCIAIS
            && $this->acessoOrigem === self::ACESSO_PARCIAIS) {
            return $dataAtual->format('Y-m-d') <= $dataLimite->format('Y-m-d');
        } elseif ($this->tipoBloqueio === ParametrosBloqueioNota::BLOQUEIO_NOTAS_DIARIO
            && $this->acessoOrigem === self::ACESSO_DIARIO) {
            return $dataAtual->format('Y-m-d') <= $dataLimite->format('Y-m-d');
        }

        return true;
    }

    /**
     * @throws Exception
     */
    private function getDataLimiteByExcessoes(
        Regencia $regencia,
        DateTime $dataLimite,
        PeriodoCalendario $periodoCalendario
    ) {
        $maiorDataLimite = $dataLimite->format('Y-m-d');

        foreach ($this->excecoes as $excecaoBloqueio) {
            if (!$excecaoBloqueio->ed363_ativo) {
                continue;
            }

            if ($excecaoBloqueio->ed363_turma != $regencia->getTurma()->getCodigo()) {
                continue;
            }

            if (!empty($excecaoBloqueio->ed363_regencia) &&
                $excecaoBloqueio->ed363_regencia != $regencia->getCodigo()
            ) {
                continue;
            }

            if ($periodoCalendario->getPeriodoAvaliacao()->getCodigo() != $excecaoBloqueio->ed363_periodoavaliacao) {
                continue;
            }

            if ($excecaoBloqueio->ed363_datalimite > $maiorDataLimite) {
                $maiorDataLimite = $excecaoBloqueio->ed363_datalimite;
            }
        }

        return new DateTime($maiorDataLimite);
    }
}
