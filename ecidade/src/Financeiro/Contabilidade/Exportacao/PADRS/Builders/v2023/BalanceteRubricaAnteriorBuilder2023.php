<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2022\BalanceteRubricaAnteriorBuilder2022;

class BalanceteRubricaAnteriorBuilder2023 extends BalanceteRubricaAnteriorBuilder2022
{
    protected function processar()
    {
        parent::processar();

        $fonte = substr($this->dados['codigo_siconfi'], 1);
        $complementoSiconfi = $this->dados['complemento'];

        // para dados anteriores a 2023 sempre zerar o complemento e recurso do siconfi
        if ($this->dados['exercicio'] <= 2023) {
            $fonte = '0000';
            $complementoSiconfi = '0000';
        }

        $this->layout->setFonteRecursoSiconfi($this->formataNumerico($fonte, 4));
        $this->layout->setComplementoSiconfi($this->formataNumerico($complementoSiconfi, 4));
    }
}
