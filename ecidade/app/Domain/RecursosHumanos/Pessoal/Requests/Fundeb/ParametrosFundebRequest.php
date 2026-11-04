<?php

namespace App\Domain\RecursosHumanos\Pessoal\Requests\Fundeb;

use App\Http\Requests\BaseFormRequest;

class ParametrosFundebRequest extends BaseFormRequest
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
            'tipo' => 'required|filled|integer',
        ];
    }
}
