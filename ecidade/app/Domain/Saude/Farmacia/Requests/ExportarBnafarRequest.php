<?php

namespace App\Domain\Saude\Farmacia\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property integer $DB_coddepto
 * @property integer $DB_id_usuario
 * @property string|null $periodoInicio
 * @property string|null $periodoFim
 * @property integer[]|null $procedimentos
 * @property integer|null $codigoMovimentacao
 * @property integer|null $procedimento
 */
class ExportarBnafarRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'DB_coddepto' => 'required|integer',
            'DB_id_usuario' => 'required|integer',
            'periodoInicio' => 'required_with:periodoFim,procedimentos|date',
            'periodoFim' => 'required_with:periodoInicio,procedimentos|date',
            'procedimentos' => 'required_with:periodoInicio,periodoFim|array',
            'procedimentos.*' => 'integer',
            'codigoMovimentacao' => 'required_with:procedimento|integer',
            'procedimento' => 'required_with:codigoMovimentacao|integer'
        ];
    }

    public function messages()
    {
        return [
            'DB_coddepto.*' => 'O campo DB_coddepto é obrigatório e deve ser um inteiro.',
            'DB_id_usuario.*' => 'O campo DB_id_usuario é obrigatório e deve ser um inteiro.',
            'procedimentos.*.integer' => 'Os itens do array de procedimentos devem ser do tipo inteiro.'
        ];
    }
}
