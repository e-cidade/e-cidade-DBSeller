<?php

namespace App\Domain\Financeiro\Tesouraria\Requests;

use App\Http\Requests\DBFormRequest;

class GetDetalhamentoDecendioRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'exercicio' => 'required|digits:4',
            'instituicao' => 'required|integer',
            'dataInicial' => 'required|date',
            'dataFinal' => 'required|date',
            'reduzidos' => 'required',
            'desdobramento' => 'required',
            'conta_pagadora' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'exercicio' => 'Você deve informar o exercício.',
            'instituicao' => 'Você deve informar a instituição.',
            'dataInicial' => 'Você deve informar a data inicial.',
            'dataFinal' => 'Você deve informar a data final.',
            'reduzidos' => 'Você deve informar o(s) reduzido(s).',
            'desdobramento' => 'Você deve informar o desdobramento.',
            'conta_pagadora' => 'Você deve informar a conta pagadora.',
        ];
    }
}
