<?php

namespace App\Domain\Financeiro\Contabilidade\Requests\Relatorios;

use App\Http\Requests\DBFormRequest;

class BalanceteRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'exercicio' => ['required', 'integer', 'filled'],
            'instituicoes' => ['required', 'array', 'filled'],
            'dataInicial' => ['required','date_format:Y-m-d'],
            'dataFinal' => ['required', 'date_format:Y-m-d', 'after_or_equal:dataInicial'],
        ];
    }

    public function messages()
    {
        return [
            'exercicio.required' => 'O Exercício deve ser informado.',
            'instituicoes.required' => 'Ao menos uma instituição deve ser informada.',
            'instituicoes.array' => 'O campo instituição deve ser um array com os códigos das instituições.',
            'dataInicial.required' => 'A Data Inicial deve ser informada',
            'dataFinal.required' => 'A Data Final deve ser informada.',
        ];
    }
}
