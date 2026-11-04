<?php

namespace App\Domain\Tributario\Arrecadacao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeletaDataLancamentoDebitoRequest extends FormRequest
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
            'numpre' => [
                'required',
                'integer',
                'exists:informacaodebito,k163_numpre'
            ]
        ];
    }
}
