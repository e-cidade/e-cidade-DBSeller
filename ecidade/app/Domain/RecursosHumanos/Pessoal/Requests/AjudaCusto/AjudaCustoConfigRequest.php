<?php

namespace App\Domain\RecursosHumanos\Pessoal\Requests\AjudaCusto;

use App\Http\Requests\BaseFormRequest;

class AjudaCustoConfigRequest extends BaseFormRequest
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
            'valor_limite' => 'required|numeric|min:1',
            'rubrica'      => 'required|string',
            'rubricaDependente'  => 'string|required_if:habilitaDependente,==,true',

        ];
    }

    /**
     *
     * @return array
     */
    public function messages()
    {
        return [
            'valor_limite.required.min' => 'Deve ser informado pelo menos um valor mínimo',
            'valor_limite.required' => 'O valor limite deve ser informado.',
            'rubrica.required' => 'A Rubrica deve ser informada.',
            'rubricaDependente.required' => 'A Rubrica de dependente deve ser informada.'
        ];
    }
}
