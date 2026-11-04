<?php

namespace App\Domain\Financeiro\Orcamento\Relatorios;

use App\Domain\Financeiro\Planejamento\Relatorios\MetasVersusCotasPdf;

class AcompanhamentoMetasVersusCotasPdf extends MetasVersusCotasPdf
{
    public function headers($titulo)
    {
        $this->addTitulo($titulo);

        $recurso = "Agrupado por: Recurso";
        if ($this->tipoAgrupador === 'geral') {
            $recurso = "Agrupado por: Total Geral";
        }
        $this->addTitulo($recurso);

        $periodicidade = "Periodicidade: Mensal";
        if ($this->porBimestre) {
            $periodicidade = "Periodicidade: Bimestral";
        }
        $this->addTitulo($periodicidade);

        if ($this->dados['filtros']['filtrouRecurso']) {
            $this->addTitulo("Filtrou recurso");
        }

        $this->wValores = $this->wValor + $this->wPercentual;
    }

    protected function getTituloPlano()
    {
        return '';
    }
}
