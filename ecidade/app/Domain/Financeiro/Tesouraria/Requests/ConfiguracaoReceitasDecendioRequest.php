<?php

namespace App\Domain\Financeiro\Tesouraria\Requests;

use App\Http\Requests\DBFormRequest;

class ConfiguracaoReceitasDecendioRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'exercicio' => 'required|digits:4',
            'receitas'  => 'required|array|min:1',
            'receitas.*'  => 'required|integer',
            'desdobramentos'  => 'required|array|min:1',
        ];
    }

    public function messages()
    {
        return [
            'receitas.*' => 'Você deve informar as receitas.',
            'desdobramentos.*' => 'Você deve informar os desdobramentos.',
            'exercicio.*' => 'Você deve informar o exercício.',
        ];
    }
}
