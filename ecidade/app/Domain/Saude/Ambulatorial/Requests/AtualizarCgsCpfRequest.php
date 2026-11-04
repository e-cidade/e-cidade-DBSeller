<?php

namespace App\Domain\Saude\Ambulatorial\Requests;

use App\Http\Requests\DBFormRequest;

/**
 * @property int $cgsPaciente
 * @property int|null $cpf
 * @property int|null $cns
 */
class AtualizarCgsCpfRequest extends DBFormRequest
{
    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            'cgsPaciente' => ['required', 'integer', 'exists:cgs_und,z01_i_cgsund'],
            'idCartaoSus' => ['string', 'exists:cgs_cartaosus,s115_i_codigo'],
            'cpf' => ['string'],
            'cns' => ['string', 'required_without:cpf'],
        ];
    }
}
