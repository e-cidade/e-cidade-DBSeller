<?php

namespace App\Domain\Saude\Farmacia\Strategies;

use App\Domain\Saude\Farmacia\Builders\DispensacaoBnafarBuilder;
use Illuminate\Support\Collection;

class DispensacaoBnafarStrategy extends ProcedimentoBnafarStrategy
{
    /**
     * @return string
     */
    public function getProcedimento()
    {
        return 'dispensacao';
    }

    /**
     * @return string
     */
    public function getProcedimentoLote()
    {
        return 'dispensacao-lote';
    }

    /**
     * @return integer
     */
    public function getTipo()
    {
        return self::DISPENSACAO;
    }

    /**
     * @param Collection $dados
     * @return \Generator|void
     */
    protected function formatar(Collection $dados)
    {
        $builder = new DispensacaoBnafarBuilder();
        $builder->setCnes($this->cnesUnidade);
        $builder->setDados($dados);

        return $builder->build();
    }
}
