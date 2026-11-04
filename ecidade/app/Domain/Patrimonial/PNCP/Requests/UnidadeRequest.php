<?php

namespace App\Domain\Patrimonial\PNCP\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property $codigoIbge
 * @property $unidadeCodigo
 * @property $unidadeNome
 * @property $documento
 * @property $ativo
 * @property $DB_instit
 * @property $alteracao
 */
class UnidadeRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'codigoIbge' => 'required|string',
            'unidadeCodigo' => 'required|integer',
            'unidadeNome' => 'required|string',
            'documento' => 'required|string',
            'ativo' => 'required|string',
        ];
    }
}
