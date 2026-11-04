<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property string $sql
 * @property array $bindings
 */
class ExecutaSqlRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'sql' => ['required', 'string'],
            'bindings' => ['present', 'array']
        ];
    }
}
