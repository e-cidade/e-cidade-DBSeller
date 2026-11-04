<?php

namespace App\Domain\Patrimonial\Protocolo\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CgmParamsRequest extends FormRequest
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
            'email' => ['string'],
            'page' => ['integer'],
            'porPagina' => ['integer'],
            'cgm' => ['integer'],
            'nome' => ['string'],
            'cgcpf' => ['string'],
            'email' => ['string']
        ];
    }
}
