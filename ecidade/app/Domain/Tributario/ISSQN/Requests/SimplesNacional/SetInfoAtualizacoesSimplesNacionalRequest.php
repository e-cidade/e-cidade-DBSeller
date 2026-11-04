<?php

namespace App\Domain\Tributario\ISSQN\Requests\SimplesNacional;

use App\Http\Requests\BaseFormRequest;

class SetInfoAtualizacoesSimplesNacionalRequest extends BaseFormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'frequenciaAtualizacoes' => [
                'required',
                'integer'
            ],
            'diaDoMes' => [
                'integer'
            ],
            'diaDaSemana' => [
                'integer'
            ],
            'horario' => [
                'required',
                'string'
            ],
            'idUsuario' => ['integer', 'required'],
        ];
    }
}
