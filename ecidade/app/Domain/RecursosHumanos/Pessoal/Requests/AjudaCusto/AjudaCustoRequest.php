<?php

namespace App\Domain\RecursosHumanos\Pessoal\Requests\AjudaCusto;

use App\Http\Requests\BaseFormRequest;

class AjudaCustoRequest extends BaseFormRequest
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
            'competencia'        => 'required|string',
            'especializacao'     => 'required|string',
            'graduacao'          => 'required|string',
            'local'              => 'required|string',
            'valor'              => 'numeric|min:1',
            'quantidade'         => 'required|numeric|min:1',
            'matricula'          => 'required|integer',
            'unidade_ensino'     => 'required|integer',
            'codigo_dependente'  => 'integer|required_if:habilitaDependente,==,true',
        ];
    }

    /**
     *
     * @return array
     */
    public function messages()
    {
        return [
            'competencia.required'       => 'Competência deve ser informada.',
            'valor.min'                  => 'O valor limite deve ser informado.',
            'codigo_dependente.required_if' => 'O dependente é obrigatório.',
            'graduacao.required'         => 'Campo graduação é obrigatório',
            'local.required'             => 'Campo Local é obrigatório',
            'matricula.required'         => 'A Matricula do servidor deve ser informada',
            'quantidade.required'        => 'Campo quantidade é obrigatório',
            'quantidade.min'             => 'O campo quantidade deve ter um valor mínimo informado.',
            'especializacao.required'    => 'Campo Especialização é obrigatório',
            'unidade_ensino.required'    => 'Campo Unidade de Ensino é obrigatório',
        ];
    }
}
