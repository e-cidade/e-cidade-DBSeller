<?php

namespace App\Domain\Financeiro\Contabilidade\Requests\LRF;

use App\Http\Requests\DBFormRequest;

class GetValoresLinhaLrf extends DBFormRequest
{

    public function rules()
    {
        return [
            'relatorio' => 'required|integer',
            'instituicao' => 'required|integer',
            'linha' => 'required|integer',
            'coluna' =>'required|string'
        ];
    }
}
