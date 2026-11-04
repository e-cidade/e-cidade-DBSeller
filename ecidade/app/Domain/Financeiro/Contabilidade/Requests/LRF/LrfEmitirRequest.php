<?php

namespace App\Domain\Financeiro\Contabilidade\Requests\LRF;

use App\Http\Requests\DBFormRequest;

class LrfEmitirRequest extends DBFormRequest
{

    public function rules()
    {
        return [
            'tipo' => 'required|string',
            'anexo' => 'required|integer',
            'relatorio' => 'required|integer',
            'periodo' => 'required|integer',
            'instituicao' => 'nullable|array',
            'DB_anousu' => 'required|integer',
            'DB_instit' => 'required|integer',
            'DB_coddepto' => 'required|integer',
            'DB_datausu' => 'required|integer',
            'DB_login' => 'required|string',
            'DB_acessado' => 'required|integer',
            'DB_id_usuario' => 'required|integer'
        ];
    }
}
