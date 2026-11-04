<?php

namespace App\Domain\Saude\Ambulatorial\Requests;

use App\Http\Requests\DBFormRequest;

class UnidadesEncaminhadorasRequest extends DBFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'ativo' => 'required|boolean',
            'descricao' => 'required|string'
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'ativo' => 'O campo ativo é obrigatório e deve ser um logico',
            'descricao' => 'O campo descrição é obrigatório e deve ser uma string'
        ];
    }
}
