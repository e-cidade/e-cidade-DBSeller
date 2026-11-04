<?php

namespace App\Domain\Educacao\CentralMatriculas\Services;

use App\Domain\Educacao\CentralMatriculas\Models\Escola;
use App\Domain\Educacao\CentralMatriculas\Models\VagaPcd;
use ECidade\Educacao\MatriculaOnline\Model\Fase;
use ECidade\Educacao\MatriculaOnline\Registry\EscolasRegistry;
use Etapa;

class VagasPcdService
{
    public function buscarVagasPcd(Fase $fase, $escola, Etapa $etapa, $turno)
    {
        $vagaPcd = VagaPcd::query()
            ->fase($fase)
            ->escola($escola)
            ->etapa($etapa)
            ->turno($turno)
            ->first();

        if (is_null($vagaPcd)) {
            $vagaPcd = new VagaPcd();
            $vagaPcd->mo64_fase = $fase->getCodigo();
            $vagaPcd->mo64_escola = $escola->getCodigo();
            $vagaPcd->mo64_etapa = $etapa->getCodigo();
            $vagaPcd->mo64_turno = $turno->getCodigoTurno();
            $vagaPcd->mo64_vagas = 0;
            $vagaPcd->save();
        }
        return $vagaPcd;
    }

    public function salvarVagasPcd(Fase $fase, Etapa $etapa, $dadosSalvar)
    {
        foreach ($dadosSalvar as $dado) {
            foreach ($dado->vagas as $vaga) {
                $escola = EscolasRegistry::get($dado->escola->mo53_codigo);
                $vagaSalvar = $this->buscarVagasPcd($fase, $escola, $etapa, new \Turno($vaga->turno));
                $vagaSalvar->mo64_vagas = $vaga->vagas;
                $vagaSalvar->save();
            }
        }
    }
}
