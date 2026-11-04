<?php

namespace App\Domain\RecursosHumanos\Pessoal\Requests\Fundeb;

use App\Http\Requests\BaseFormRequest;

class ProcessamentoFundebRequest extends BaseFormRequest
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
            'ano'       => 'required|filled|integer',
            'mes'       => 'required|filled|integer',
            'rubrica'   => 'required|filled|string',
        ];
    }
}
