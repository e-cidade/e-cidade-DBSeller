<?php

namespace App\Domain\Saude\TFD\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property string $DB_id_usuario
 * @property int $codigoAgendamento
 * @property string $motivoCancelamento
 */
class CancelaVeiculoRequest extends DBFormRequest
{
    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            'DB_id_usuario' => ['integer', 'required', 'exists:db_usuarios,id_usuario'],
            'codigoAgendamento' => ['integer', 'required', 'exists:tfd_veiculodestino,tf18_i_codigo'],
            'motivoCancelamento' => ['string', 'max:120', 'required']
        ];
    }
}
