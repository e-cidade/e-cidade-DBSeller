<?php

namespace App\Domain\Tributario\ISSQN\Requests\InscricaoMunicipal;

use Illuminate\Foundation\Http\FormRequest;

class BuscarInfoSalaoParceiroRequest extends FormRequest
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
            'inscricao' => ['integer'],
            'cgm' => ['integer'],
        ];
    }
}
