<?php

namespace App\Domain\Tributario\ISSQN\Requests\SimplesNacional;

use Illuminate\Foundation\Http\FormRequest;

class BuscaArquivosOptantesRequest extends FormRequest
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
            'nomeArquivo' => ['string'],
            'dataImportacao' => ['string'],
            'dataLimite' => ['string'],
        ];
    }
}
