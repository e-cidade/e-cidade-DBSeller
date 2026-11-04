<?php

namespace App\Domain\Tributario\ISSQN\Requests\SalaoParceiro;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParceiroRequest extends FormRequest
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
            'q203_sequencial' => 'required|integer',
            'q203_dtinicial' => 'required|string',
            'q203_dtfinal' => 'nullable|string',
            'q203_obs' => 'nullable|string|max:255'
            
        ];
    }
}
