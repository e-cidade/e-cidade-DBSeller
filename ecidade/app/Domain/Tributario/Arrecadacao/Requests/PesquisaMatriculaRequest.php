<?php

namespace App\Domain\Tributario\Arrecadacao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PesquisaMatriculaRequest extends FormRequest
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
            'pagina' => [
                'integer',
            ],
            'porPagina' => [
                'integer',
            ],
            'matricula' => [
                'required',
                'integer',
                'exists:arrematric,k00_matric'
            ]
        ];
    }
}
