<?php

namespace App\Domain\Tributario\Diversos\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcedenciaParamsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'porPagina' => ['integer'],
            'page' => ['integer'],
            'sequencial' => ['integer'],
            'receita' => ['integer'],
            'descricaoabreviada' => ['string'],
            'filtraTipoCobrancaFalso' => ['boolean'],
            'descricao' => ['string']
        ];
    }
}
