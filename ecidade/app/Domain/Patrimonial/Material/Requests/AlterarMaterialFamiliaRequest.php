<?php

namespace App\Domain\Patrimonial\Material\Requests;

use App\Http\Requests\DBFormRequest;

class AlterarMaterialFamiliaRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'sequencial' => 'required|string',
            'descricao' => 'required|string'
        ];
    }
}
