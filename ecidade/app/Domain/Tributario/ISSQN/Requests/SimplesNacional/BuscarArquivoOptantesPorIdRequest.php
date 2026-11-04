<?php

namespace App\Domain\Tributario\ISSQN\Requests\SimplesNacional;

use Illuminate\Foundation\Http\FormRequest;

class BuscarArquivoOptantesPorIdRequest extends FormRequest
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
            'id' => ['string', 'required'],
        ];
    }
}
