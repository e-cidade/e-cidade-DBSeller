<?php

namespace App\Domain\RecursosHumanos\RH\Relatorios\Requests;

use App\Http\Requests\DBFormRequest;

class GerarCertidaoTempoContribuicaoRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'matricula' => 'required|integer',
            'ano' => 'required|integer',
            'numero' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'matricula.required' => 'Matricula deve ser informada.',
            'numero.required' => 'Numero deve ser informado.',
            'ano.required' => 'Ano deve ser informado.'
        ];
    }
}
