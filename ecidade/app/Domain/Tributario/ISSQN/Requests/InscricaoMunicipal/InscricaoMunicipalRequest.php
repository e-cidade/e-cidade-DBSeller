<?php

namespace App\Domain\Tributario\ISSQN\Requests\InscricaoMunicipal;

use Illuminate\Foundation\Http\FormRequest;

class InscricaoMunicipalRequest extends FormRequest
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
            'inscricao' => [
                'required',
                'integer',
                'exists:issbase,q02_inscr'
            ]
        ];
    }
}
