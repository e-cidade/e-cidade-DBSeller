<?php

namespace App\Domain\Tributario\Arrecadacao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuscarGrupoTaxasRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'porPagina' => ['integer'],
            'page' => ['integer'],
            'sequencial' => [
                'integer',
                'exists:grupotaxas,ar55_sequencial'
            ],
            'descricao' => [
                'string'
            ],
            'datalimite' => [
                'string'
            ],
            'origem' => [
                'integer',
                'exists:grupotaxasorigem,ar56_sequencial'
            ]
        ];
    }
}
