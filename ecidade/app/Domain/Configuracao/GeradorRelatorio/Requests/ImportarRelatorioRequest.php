<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Requests;

use App\Http\Requests\DBFormRequest;

class ImportarRelatorioRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'arquivo' => ['required', 'file', 'mimetypes:text/xml,application/xml'],
            'grupo' => ['required', 'integer'],
            'tipo' => ['required', 'integer']
        ];
    }

    public function messages()
    {
        return [
            'arquivo.mimetypes' => 'O arquivo deve ser um xml.',
            'arquivo.*' => 'É obrigatório informar um arquivo.',
            'grupo.required' => 'É obrigatório informar um grupo para o relatório.',
            'grupo.integer' => 'O campo grupo deve ser do tipo inteiro.',
            'tipo.required' => 'É obrigatório informar um tipo para o relatório.',
            'tipo.integer' => 'O campo tipo deve ser do tipo inteiro.'
        ];
    }
}
