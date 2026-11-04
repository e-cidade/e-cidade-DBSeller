<?php

namespace App\Domain\Tributario\Cadastro\Requests\Zoneamento;

use Illuminate\Foundation\Http\FormRequest;

class SaveMacrozonaRequest extends FormRequest
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
            'sigla' => 'string|required:max:10',
            'localizacao' => 'string|required',
            'descricao' => 'string:max:60',
        ];
    }
}
