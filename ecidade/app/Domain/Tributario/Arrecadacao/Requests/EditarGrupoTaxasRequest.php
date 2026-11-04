<?php

namespace App\Domain\Tributario\Arrecadacao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarGrupoTaxasRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sequencial' => [
                'integer',
                'required',
                'exists:grupotaxas,ar55_sequencial'
            ],
            'descricao' => [
                'string'
            ],
            'datalimite' => [
                'string'
            ],
            'procedenciaprinc' => [
                'integer',
                'exists:procdiver,dv09_procdiver'
            ],
            'origem' => [
                'integer',
                'exists:grupotaxasorigem,ar56_sequencial'
            ]
        ];
    }
}
