<?php

namespace App\Domain\Saude\Farmacia\Validators;

class EntradaBnafarValidator extends BnafarValidator
{
    protected function validaCamposObrigatorios($movimentacao, $validaMovimentacao = true)
    {
        if ($movimentacao->cnpj_estabelecimento == '' && $movimentacao->cnes_estabelecimento == '') {
            yield ['id_estabelecimento' => 'É obrigatório informar o campo CNPJ ou CNES do estabelecimento origem.'];
        }

        // Executado pela rotina de transferência entra depósitos
        $isTransferencia = $movimentacao->movimentacao == '7' && $movimentacao->codigo_transferencia != '';
        if ($movimentacao->numero_documento == '' && !$isTransferencia) {
            yield ['numero_documento' => 'O campo nota fiscal é obrigatório.'];
        }

        foreach (parent::validaCamposObrigatorios($movimentacao, $validaMovimentacao) as $erro) {
            yield $erro;
        }
    }
}
