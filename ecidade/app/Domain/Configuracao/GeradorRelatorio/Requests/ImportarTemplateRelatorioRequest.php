<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Requests;

use App\Http\Requests\DBFormRequest;

class ImportarTemplateRelatorioRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'template' => [
                'required',
                'file',
                'mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ]
        ];
    }
}
