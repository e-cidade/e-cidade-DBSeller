<?php

namespace App\Domain\Configuracao\Usuario\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property string $refresh_token
 * @property string $password
 */
class UnblockRequest extends DBFormRequest
{
    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            'refresh_token' => ['required', 'string'],
            'password' => ['present', 'string'],
        ];
    }
}
