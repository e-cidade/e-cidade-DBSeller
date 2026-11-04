<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\PadBuilder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2022\BalanceteRubricaAnterior;

class BalanceteRubricaAnterior2024Builder extends PadBuilder
{
    protected function create()
    {
        $this->layout = new BalanceteRubricaAnterior();
    }

    protected function processar()
    {
        $elemento = substr($this->dados['o56_elemento'], 1);

        $this->layout->setOrgao($this->formataNumerico($this->dados['o58_orgao'], 2));
        $this->layout->setUnidade($this->formataNumerico($this->dados['o58_unidade'], 2));
        $this->layout->setFuncao($this->formataNumerico($this->dados['o58_funcao'], 2));
        $this->layout->setSubFuncao($this->formataNumerico($this->dados['o58_subfuncao'], 3));
        $this->layout->setPrograma($this->formataNumerico($this->dados['o58_programa'], 4));
        $this->layout->setProjetoAtividade($this->formataNumerico($this->dados['o58_projativ'], 5));
        $this->layout->setElemento($this->formatEstrutural($elemento, 15));
        $this->layout->setFonteRecurso($this->formataNumerico(0, 4));
        $this->layout->setPrimeiroBimestreEmpenhado($this->formataValor($this->dados['bi1_empenhado'], 11));
        $this->layout->setPrimeiroBimestreLiquidado($this->formataValor($this->dados['bi1_liquidado'], 11));
        $this->layout->setPrimeiroBimestrePagamento($this->formataValor($this->dados['bi1_pagamento'], 11));

        $this->layout->setSegundoBimestreEmpenhado($this->formataValor($this->dados['bi2_empenhado'], 11));
        $this->layout->setSegundoBimestreLiquidado($this->formataValor($this->dados['bi2_liquidado'], 11));
        $this->layout->setSegundoBimestrePagamento($this->formataValor($this->dados['bi2_pagamento'], 11));
        $this->layout->setTerceiroBimestreEmpenhado($this->formataValor($this->dados['bi3_empenhado'], 11));
        $this->layout->setTerceiroBimestreLiquidado($this->formataValor($this->dados['bi3_liquidado'], 11));
        $this->layout->setTerceiroBimestrePagamento($this->formataValor($this->dados['bi3_pagamento'], 11));
        $this->layout->setQuartoBimestreEmpenhado($this->formataValor($this->dados['bi4_empenhado'], 11));
        $this->layout->setQuartoBimestreLiquidado($this->formataValor($this->dados['bi4_liquidado'], 11));
        $this->layout->setQuartoBimestrePagamento($this->formataValor($this->dados['bi4_pagamento'], 11));
        $this->layout->setQuintoBimestreEmpenhado($this->formataValor($this->dados['bi5_empenhado'], 11));
        $this->layout->setQuintoBimestreLiquidado($this->formataValor($this->dados['bi5_liquidado'], 11));
        $this->layout->setQuintoBimestrePagamento($this->formataValor($this->dados['bi5_pagamento'], 11));
        $this->layout->setSextoBimestreEmpenhado($this->formataValor($this->dados['bi6_empenhado'], 11));
        $this->layout->setSextoBimestreLiquidado($this->formataValor($this->dados['bi6_liquidado'], 11));
        $this->layout->setSextoBimestrePagamento($this->formataValor($this->dados['bi6_pagamento'], 11));
        $this->layout->setComplemento($this->formataNumerico(0, 4));

        $complementoSiconfi = $this->dados['complemento'];

        $this->layout->setFonteRecursoSiconfi($this->formataNumerico($this->dados['siconfi'], 4));
        $this->layout->setComplementoSiconfi($this->formataNumerico($complementoSiconfi, 4));
    }
}
