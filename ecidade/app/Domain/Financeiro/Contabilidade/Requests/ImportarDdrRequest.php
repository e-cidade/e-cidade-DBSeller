<?php

namespace App\Domain\Financeiro\Contabilidade\Requests;

use App\Http\Requests\DBFormRequest;
use Illuminate\Validation\Rule;

class ImportarDdrRequest extends DBFormRequest
{
    private $mimes = [
        'text/csv',
        'text/plain',
        'application/csv',
        'text/comma-separated-values',
        'text/anytext',
        'application/octet-stream',
        'application/txt',
    ];
    public function rules()
    {
        return [
            'file' => [
                'required',
                'file',
                'mimetypes:' . implode(',', $this->mimes)
            ],
            'exercicio' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'O arquivo deve ser informado.',
            'file.mimetypes' => "O arquivo deve ser um csv"
        ];
    }
}
