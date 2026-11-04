<?php

namespace App\Domain\Tributario\ISSQN\Requests\SalaoParceiro;

use Illuminate\Foundation\Http\FormRequest;

class SaveParceirosRequest extends FormRequest
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
            'q202_inscr' => 'required|integer',
            'q202_dtinicial' => 'string',
            'q202_obs' => 'nullable|string|max:255',
            'q203_inscrparc' => 'required|integer',
            'q203_numcgm' => 'required|integer',
            'q203_dtinicial' => 'required|string',
            'q203_obs' => 'nullable|string|max:255',
        ];
    }
}
