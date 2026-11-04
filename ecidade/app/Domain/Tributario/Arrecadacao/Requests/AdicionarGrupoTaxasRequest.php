<?php

namespace App\Domain\Tributario\Arrecadacao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdicionarGrupoTaxasRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'descricao' => [
                'required',
                'string'
            ],
            'datalimite' => [
                'string'
            ],
            'procedenciaprinc' => [
                'required',
                'integer',
                'exists:procdiver,dv09_procdiver'
            ],
            'origem' => [
                'required',
                'integer',
                'exists:grupotaxasorigem,ar56_sequencial'
            ]
        ];
    }
}
