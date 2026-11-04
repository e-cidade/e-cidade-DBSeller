<?php

namespace App\Domain\Patrimonial\Material\Requests;

use App\Http\Requests\DBFormRequest;

class IncluirMaterialFamiliaRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'descricao' => 'required|string'
        ];
    }
}
