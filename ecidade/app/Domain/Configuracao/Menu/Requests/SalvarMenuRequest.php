<?php

namespace App\Domain\Configuracao\Menu\Requests;

use App\Http\Requests\DBFormRequest;

class SalvarMenuRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'descricao' => ['required', 'string'],
            'ajuda' => ['required', 'string'],
            'rota' => ['required', 'string'],
            'descricaoTecnica' => ['required', 'string'],
            'liberadoCliente' => ['required', 'boolean']
        ];
    }
}
