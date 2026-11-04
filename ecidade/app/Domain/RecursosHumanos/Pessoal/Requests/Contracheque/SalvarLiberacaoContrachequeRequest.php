<?php

namespace App\Domain\RecursosHumanos\Pessoal\Requests\Contracheque;

use App\Http\Requests\DBFormRequest;

class SalvarLiberacaoContrachequeRequest extends DBFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ['configuracoes' => 'required|array|min:1'];
    }

    public function messages()
    {
        return [
            'configuracoes.required' => 'Nenhuma configuração informada.',
            'configuracoes.array' => 'Deve ser enviado um conjunto de configuração (array).',
            'configuracoes.min' => 'Deve ser enviado pelo menos uma configuração.',
        ];
    }
}
