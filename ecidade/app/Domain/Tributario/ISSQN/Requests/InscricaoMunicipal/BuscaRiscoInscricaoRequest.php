<?php

namespace App\Domain\Tributario\ISSQN\Requests\InscricaoMunicipal;

use Illuminate\Foundation\Http\FormRequest;

class BuscaRiscoInscricaoRequest extends FormRequest
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
            'q201_inscr' => ['integer']
        ];
    }
}
