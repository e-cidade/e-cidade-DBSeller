<?php

namespace App\Domain\Saude\Farmacia\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property string data
 */
class RelatorioProtocoloBnafarRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'data' => ['required', 'string']
        ];
    }
}
