<?php

namespace App\Domain\Financeiro\Contabilidade\Requests\Relatorios;

use Illuminate\Validation\Rule;

class BalanceteVerificacaoRequest extends BalanceteRequest
{
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                "tipoPlano" => ['required', 'string'],
                "estruturais" => ['nullable', 'array'],
                'indicadorSuperavit' => ['required', Rule::in(['T', 'N', 'F', 'P'])],
                'sistemaContas' => ['required', Rule::in([0, 1, 2, 3, 99])],
                'comEncerramento' => ['required', 'boolean'],
                'contasComMovimento' => ['required', 'boolean'],
                "subtitulo" => ['nullable', 'string'],
                'sintetico' => ['required', 'boolean'],
                'exibirContaBancaria' => ['required', 'boolean'],
                'consolidarPorReduzido' => ['required', 'boolean'],
                'recursos' =>  ['nullable', 'array'],
            ]
        );
    }
}
