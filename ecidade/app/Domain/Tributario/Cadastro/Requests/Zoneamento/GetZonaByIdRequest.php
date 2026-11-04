<?php

namespace App\Domain\Tributario\Cadastro\Requests\Zoneamento;

use Illuminate\Foundation\Http\FormRequest;

class GetZonaByIdRequest extends FormRequest
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
            'sequencial'=> 'integer|required',
        ];
    }
}
