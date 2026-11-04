<?php

namespace App\Domain\Financeiro\Tesouraria\Requests;

use App\Http\Requests\DBFormRequest;

class PeriodoSlipRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'dataInicial' => ['required', 'filled', 'date_format:Y-m-d'],
            'dataFinal' => 'sometimes|date_format:Y-m-d|after_or_equal:dataInicial',
            'tipo' => 'sometimes|integer',
            'situacao' => 'sometimes|integer',
        ];
    }

    public function messages()
    {
        return [
            'dataInicial.required' => 'Deve ser informada.',
            'dataInicial.filled' => 'Deve ser informada.',
            'dataInicial.date_format' => 'O Formato da data inicial deve ser Y-m-d.',
            'dataFinal.date_format' => 'O Formato da data final deve ser Y-m-d.',
        ];
    }
}
