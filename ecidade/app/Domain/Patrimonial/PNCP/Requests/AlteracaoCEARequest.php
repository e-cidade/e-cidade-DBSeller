<?php

namespace App\Domain\Patrimonial\PNCP\Requests;

use App\Http\Requests\DBFormRequest;

/**
 *@property $cnpj
 *@property $anoCompra
 *@property $numeroCompra
 */
class AlteracaoCEARequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'cnpj' => 'string|required',
            'anoCompra' => 'integer|required',
            'numeroCompra' => 'integer|required',
        ];
    }
}
