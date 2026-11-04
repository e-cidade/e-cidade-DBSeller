<?php

namespace App\Domain\Saude\Farmacia\Strategies;

use App\Domain\Saude\Farmacia\Builders\EntradaBnafarBuilder;
use Illuminate\Support\Collection;

class EntradaBnafarStrategy extends ProcedimentoBnafarStrategy
{
    /**
     * @return string
     */
    public function getProcedimento()
    {
        return 'entrada';
    }

    /**
     * @return string
     */
    public function getProcedimentoLote()
    {
        return 'entrada-lote';
    }

    /**
     * @return integer
     */
    public function getTipo()
    {
        return self::ENTRADA;
    }

    /**
     * @param Collection $dados
     * @return \Generator
     */
    protected function formatar(Collection $dados)
    {
        $builder = new EntradaBnafarBuilder();
        $builder->setCnes($this->cnesUnidade);
        $builder->setTipoEstabelecimento('F');
        $builder->setDados($dados);

        return $builder->build();
    }
}
