<?php

namespace App\Domain\Tributario\Arrecadacao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PesquisaCgmRequest extends FormRequest
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
            'cgm' => [
                'required',
                'integer',
                'exists:arrenumcgm,k00_numcgm'
            ]
        ];
    }
}
