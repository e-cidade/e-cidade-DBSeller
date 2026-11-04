<?php

namespace App\Domain\Tributario\ISSQN\Requests\InscricaoMunicipal;

use Illuminate\Foundation\Http\FormRequest;

class BuscarInscricoesMunicipaisRequest extends FormRequest
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
            'inscricao' => ['integer'],
            'nome' => ['string'],
            'inscricaoAnterior' => ['string'],
            'cgcpf' => ['string'],
            'setorFiscal' => ['integer'],
        ];
    }
}
