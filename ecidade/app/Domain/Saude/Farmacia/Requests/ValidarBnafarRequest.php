<?php

namespace App\Domain\Saude\Farmacia\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property integer $DB_coddepto
 * @property string $periodoInicio
 * @property string $periodoFim
 */
class ValidarBnafarRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'DB_coddepto' => 'required|integer',
            'periodoInicio' => 'required|date',
            'periodoFim' => 'required|date'
        ];
    }

    public function message()
    {
        return [
            'DB_coddepto.*' => 'O campo DB_coddepto é obrigatório e deve ser um inteiro.'
        ];
    }
}
