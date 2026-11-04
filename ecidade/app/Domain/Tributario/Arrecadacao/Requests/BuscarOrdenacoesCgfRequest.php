<?php

namespace App\Domain\Tributario\Arrecadacao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuscarOrdenacoesCgfRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cadtipo' => [
                'required',
                'integer'
            ]
        ];
    }
}
