<?php

namespace App\Domain\Tributario\ISSQN\Requests\SalaoParceiro;

use Illuminate\Foundation\Http\FormRequest;

class SaveAtividadesSalaoRequest extends FormRequest
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
            'deducoes' => 'required',
        ];
    }
}
