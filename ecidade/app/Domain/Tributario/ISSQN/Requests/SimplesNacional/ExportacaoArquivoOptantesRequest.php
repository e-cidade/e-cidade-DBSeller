<?php

namespace App\Domain\Tributario\ISSQN\Requests\SimplesNacional;

use Illuminate\Foundation\Http\FormRequest;

class ExportacaoArquivoOptantesRequest extends FormRequest
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

        return [
            'nomeArquivo' => ['required', 'string'],
            'dataImportacao' => ['required', 'string'],
            'periodoApuracaoDe' => ['required', 'string'],
            'periodoApuracaoAte' => ['required', 'string'],
            'dataLimite' => ['required', 'string'],
            'registros' => ['required', 'array'],
        ];
    }
}
