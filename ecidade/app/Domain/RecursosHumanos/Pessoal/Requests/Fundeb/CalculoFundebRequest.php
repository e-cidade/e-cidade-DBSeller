<?php

namespace App\Domain\RecursosHumanos\Pessoal\Requests\Fundeb;

use App\Http\Requests\BaseFormRequest;

class CalculoFundebRequest extends BaseFormRequest
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
            'id_ano'      => 'required|filled|integer',
            'id_mes'      => 'required|filled|integer',
            'valor'       => 'required|filled|numeric',
        ];
    }
}
