<?php

namespace App\Domain\Saude\Farmacia\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property integer $DB_coddepto
 * @property string $periodoInicio
 * @property string $periodoFim
 * @property integer[] $procedimentos
 */
class ConsistirBnafarRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'DB_coddepto' => 'required|integer',
            'periodoInicio' => 'required|date',
            'periodoFim' => 'required|date',
            'procedimentos' => 'required|array',
            'procedimentos.*' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'DB_coddepto.*' => 'O campo DB_coddepto é obrigatório e deve ser um inteiro.',
            'procedimentos.*.required' => 'O array de procedimentos não pode estar vazio.',
            'procedimentos.*.integer' => 'Os itens do array de procedimentos devem ser do tipo inteiro.'
        ];
    }
}
