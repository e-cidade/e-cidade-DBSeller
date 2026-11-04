<?php

namespace App\Domain\Financeiro\Tesouraria\Requests;

use App\Http\Requests\DBFormRequest;

class GetLancamentosDecendioRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'exercicio' => 'required|digits:4',
            'instituicao'  => 'required|integer',
            'dataInicial'  => 'required|date',
            'dataFinal'  => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'exercicio' => 'Você deve informar o exercício.',
            'instituicao' => 'Você deve informar a instituição.',
            'dataInicial' => 'Você deve informar a data inicial.',
            'dataFinal' => 'Você deve informar a data final.',
        ];
    }
}
