<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2020\BalanceteReceitaAnteriorBuilder2020;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2022\BalanceteReceitaAnterior;

class BalanceteReceitaAnteriorBuilder2023 extends BalanceteReceitaAnteriorBuilder2020
{
    /**
     * @var BalanceteReceitaAnterior
     */
    protected $layout;

    protected function create()
    {
        $this->layout = new BalanceteReceitaAnterior();
    }

    protected function processar()
    {
        parent::processar();
        $recursoSiconfi = substr($this->dados['codigo_siconfi'], 1);
        $complementoSiconfi = $this->dados['complemento'];

        // para dados anteriores a 2023 sempre zerar o complemento e recurso do siconfi
        if ($this->dados['exercicio'] <= 2023) {
            $recursoSiconfi = '0000';
            $complementoSiconfi = '0000';
        }

        $this->layout->setFonteRecursoSiconfi($this->formataNumerico($recursoSiconfi, 4));
        $this->layout->setComplementoSiconfi($this->formataNumerico($complementoSiconfi, 4));
    }
}
