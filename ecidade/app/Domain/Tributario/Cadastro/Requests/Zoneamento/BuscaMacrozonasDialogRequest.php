<?php

namespace App\Domain\Tributario\Cadastro\Requests\Zoneamento;

use Illuminate\Foundation\Http\FormRequest;

class BuscaMacrozonasDialogRequest extends FormRequest
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
            'page' => ['integer'],
            'porPagina' => ['integer'],
            'sequencial' => ['integer'],
            'sigla' => ['string'],
            'localizacao' => ['string'],
            'descricao' => ['string'],
        ];
    }
}
