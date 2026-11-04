<?php

namespace App\Domain\Tributario\Cemiterio\Requests\Cemiterio\Parametros;

use Illuminate\Foundation\Http\FormRequest;

class BuscaParametrosCemiterioRequest extends FormRequest
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
            'ano' => [
                'integer'
            ],
        ];
    }
}
