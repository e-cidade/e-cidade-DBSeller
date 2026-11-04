<?php

namespace App\Domain\Saude\Farmacia\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property string $competencia
 * @property integer $pagina
 * @property integer $tamanho
 * @property integer $DB_coddepto
 */
class ConsultarProtocoloBnafarRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'competencia' => ['required', 'string'],
            'pagina' => ['required', 'integer'],
            'tamanho' => ['required', 'integer'],
            'DB_coddepto' => ['required', 'integer']
        ];
    }
}
