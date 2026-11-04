<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\PadBuilder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023\RDExtra;

class RDExtra2023Builder extends PadBuilder
{
    protected function create()
    {
        $this->layout = new RDExtra();
    }

    protected function processar()
    {
        $this->layout->setEstrutural($this->formatar($this->dados['estrutural'], 20, '0', STR_PAD_LEFT))
            ->setOrgaoUnidade($this->formataNumerico($this->dados['orgao_unidade'], 4))
            ->setValor($this->formataValor($this->dados['valor'], 13, false))
            ->setIdentidicador($this->dados['identificador'])
            ->setClassificacao($this->dados['classificacao']);
    }
}
