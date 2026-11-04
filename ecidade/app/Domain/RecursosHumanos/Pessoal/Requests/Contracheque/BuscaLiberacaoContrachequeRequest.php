<?php

namespace App\Domain\RecursosHumanos\Pessoal\Requests\Contracheque;

use Illuminate\Foundation\Http\FormRequest;

class BuscaLiberacaoContrachequeRequest extends FormRequest
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
            'DB_instit' => 'required|integer'
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'DB_instit.required' => 'Instituição não informada',
        ];
    }
}
