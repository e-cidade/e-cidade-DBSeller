<?php

namespace App\Domain\Tributario\ISSQN\Requests\SalaoParceiro;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalaoRequest extends FormRequest
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
            'q202_sequencial' => 'required|string',
            'q202_dtinicial' => 'required|string',
            'q202_dtfinal' => 'nullable|string',
            'q202_obs' => 'nullable|string|max:255'
            
        ];
    }
}
