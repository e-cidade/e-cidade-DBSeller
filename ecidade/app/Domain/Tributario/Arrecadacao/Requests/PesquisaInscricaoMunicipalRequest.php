<?php

namespace App\Domain\Tributario\Arrecadacao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PesquisaInscricaoMunicipalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'page' => [
                'integer',
            ],
            'porPagina' => [
                'integer',
            ],
            'inscricao' => [
                'required',
                'integer',
                'exists:arreinscr,k00_inscr'
            ]
        ];
    }
}
