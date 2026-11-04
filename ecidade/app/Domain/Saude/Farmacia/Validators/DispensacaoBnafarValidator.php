<?php

namespace App\Domain\Saude\Farmacia\Validators;

class DispensacaoBnafarValidator extends BnafarValidator
{
    protected function validaCamposObrigatorios($movimentacao, $validaMovimentacao = true)
    {
        $cnsInvalido = $movimentacao->cns_paciente == '' || strlen($movimentacao->cns_paciente) != 15;
        $cpfInvalido = $movimentacao->cpf_paciente == '' || strlen($movimentacao->cpf_paciente) != 11;
        if ($cnsInvalido && $cpfInvalido) {
            yield [
                'cns_paciente' => "Usuário {$movimentacao->nome_paciente} com CNS inválido.",
                'cpf_paciente' => "Usuário {$movimentacao->nome_paciente} com CPF inválido."
            ];
        }

        foreach (parent::validaCamposObrigatorios($movimentacao, false) as $erro) {
            yield $erro;
        }
    }
}
