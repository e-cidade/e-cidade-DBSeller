<?php

namespace App\Domain\RecursosHumanos\Pessoal\Requests\PrevidenciaComplementar;

use App\Http\Requests\DBFormRequest;

class BuscaPrevidenciaComplementarRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'matricula' => ['required', 'integer']
        ];
    }

    public function messages()
    {
        return [
            'matricula.required' => 'Matricula deve ser informada.'
        ];
    }
}
