<?php

namespace App\Domain\Tributario\Cadastro\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImoveisRequest extends FormRequest
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
            'matricula' => ['integer'],
            'codCondominio' => ['integer'],
            'codLoteamento' => ['integer'],
            'codLogradouro' => ['integer'],
            'nome' => ['string'],
            'setor' => ['string'],
            'quadra' => ['string'],
            'lote' => ['string'],
            'setorLocalizacao' => ['string'],
            'quadraLocalizacao' => ['string'],
            'loteLocalizacao' => ['string'],
            'refAnterior' => ['string'],
            'regitroCartografico' => ['string'],
            'matriculasBaixadas' => ['boolean'],
        ];
    }
}
