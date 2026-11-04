<?php

namespace App\Domain\RecursosHumanos\Pessoal\Requests\Fundeb;

use App\Http\Requests\BaseFormRequest;

class CargosFundebRequest extends BaseFormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|filled|integer|exists:rhcargosfundeb,rh283_codigo',
        ];
    }
}
