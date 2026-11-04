<?php

namespace App\Domain\Tributario\Cadastro\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetIdPrincRequest extends FormRequest
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
            'j39_idcons' => ['integer'],
            'j39_matric' => ['integer'],
        ];
    }
}
