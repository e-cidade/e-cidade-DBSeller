<?php

namespace App\Domain\Tributario\Arrecadacao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeletarGrupoTaxasRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sequencial' => [
                'required',
                'integer',
                'exists:grupotaxas,ar55_sequencial'
            ]
        ];
    }
}
