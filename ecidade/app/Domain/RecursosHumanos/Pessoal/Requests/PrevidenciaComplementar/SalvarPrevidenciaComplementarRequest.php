<?php

namespace App\Domain\RecursosHumanos\Pessoal\Requests\PrevidenciaComplementar;

use App\Http\Requests\DBFormRequest;

class SalvarPrevidenciaComplementarRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'matricula' => 'required|integer',
            'cnpj' => 'required|string',
            'tipoPrevidencia' => 'required|integer',
            'deducaoRelativa' => 'required|numeric',
            'contribuicaoPatrocinador' => 'nullable|numeric'
        ];
    }

    public function messages()
    {
        return [
            'matricula.required' => 'Matricula deve ser informada.',
            'cnpj.required' => 'CNPJ deve ser informado.',
            'tipoPrevidencia.required' => 'Tipo de Previdencia deve ser informado.',
            'deducaoRelativa.required' => 'Deducao Relativa deve ser informada.'
        ];
    }
}
