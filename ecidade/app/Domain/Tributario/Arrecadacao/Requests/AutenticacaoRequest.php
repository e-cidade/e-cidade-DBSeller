<?php

namespace App\Domain\Tributario\ITBI\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutenticacaoRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            "numeroGuia" => ["required", "integer"],
            "dataPagamento" => ["required", "date", "max:10"]
        ];

        return $rules;
    }
}
