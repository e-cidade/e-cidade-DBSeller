<?php

namespace App\Domain\Saude\Farmacia\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property int $DB_coddepto
 * @property int $DB_id_usuario
 * @property int $protocolo
 * @property int $procedimento
 */
class ReprocessarProtocoloBnafarRequest extends DBFormRequest
{

    public function rules()
    {
        return [
            'DB_coddepto' => 'required|integer',
            'DB_id_usuario' => 'required|integer',
            'protocolo' => 'required|integer',
            'procedimento' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'DB_coddepto.*' => 'O campo DB_coddepto é obrigatório e deve ser um inteiro.',
            'DB_id_usuario.*' => 'O campo DB_id_usuario é obrigatório e deve ser um inteiro.',
            'protocolo.*' => 'O campo protocolo é obrigatório e deve ser um inteiro.',
            'procedimento.*' => 'O campo procedimento é obrigatório e deve ser um inteiro.'
        ];
    }
}
