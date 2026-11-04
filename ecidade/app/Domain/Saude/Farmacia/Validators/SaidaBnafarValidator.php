<?php

namespace App\Domain\Saude\Farmacia\Validators;

class SaidaBnafarValidator extends BnafarValidator
{
    protected function validaCamposObrigatorios($movimentacao, $validaMovimentacao = true)
    {
        if ($movimentacao->cnes_estabelecimento == '' && $movimentacao->cnpj_estabelecimento == '') {
            yield ['id_estabelecimento' => 'É obrigatório informar o campo CNPJ ou CNES do estabelecimento destino.'];
        }

        foreach (parent::validaCamposObrigatorios($movimentacao, $validaMovimentacao) as $erro) {
            yield $erro;
        }
    }
}
