<?php

namespace App\Domain\Financeiro\Contabilidade\Requests\LRF;

use App\Http\Requests\DBFormRequest;

class LrfNotaExplicativaRequest extends DBFormRequest
{

    public function rules()
    {
        return [
            'relatorio' => 'required|integer',
            'exercicio' => 'required|integer',
            'instituicao' => 'required|integer',
            'nota' => 'required|string',
            'fonte' => 'nullable|string',
            'periodo' => 'required|integer',
            'notaSize' => 'required|integer',
            'fonteSize' => 'required|integer',
        ];
    }
}
