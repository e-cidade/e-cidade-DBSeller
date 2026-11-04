<?php

namespace App\Domain\Saude\Farmacia\Strategies;

use App\Domain\Saude\Farmacia\Builders\SaidaBnafarBuilder;
use Illuminate\Support\Collection;

class SaidaBnafarStrategy extends ProcedimentoBnafarStrategy
{
    /**
     * @return string
     */
    public function getProcedimento()
    {
        return 'saida';
    }

    /**
     * @return string
     */
    public function getProcedimentoLote()
    {
        return 'saida-lote';
    }

    /**
     * @return integer
     */
    public function getTipo()
    {
        return self::SAIDA;
    }

    /**
     * @param Collection $dados
     * @return \Generator|void
     */
    protected function formatar(Collection $dados)
    {
        $builder = new SaidaBnafarBuilder();
        $builder->setCnes($this->cnesUnidade);
        $builder->setTipoEstabelecimento('F');
        $builder->setDados($dados);

        return $builder->build();
    }
}
