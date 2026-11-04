<?php

namespace App\Domain\Patrimonial\PNCP\Requests;

use App\Http\Requests\DBFormRequest;

/**
 *@property $anoCompra
 *@property $tituloDocumento
 *@property $tipoDocumento
 *@property $unidadeCompradora
 *@property $instrumentoConvocatorio
 *@property $modalidade
 *@property $modoDisputa
 *@property $numeroCompra
 *@property $numeroProcesso
 *@property $objetoCompra
 *@property $informacaoComplementar
 *@property $amparoLegal
 *@property $cnpj
 */
class InclusaoCEARequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'cnpj' => 'string|required',
            'anoCompra' => 'integer|required',
            'tituloDocumento' => 'string|required',
            'unidadeCompradora' => 'integer|required',
            'instrumentoConvocatorio' => 'integer|required',
            'modalidade' => 'integer|required',
            'modoDisputa' => 'integer|required',
            'numeroCompra' => 'integer|required',
            'numeroProcesso' => 'string|required',
            'objetoCompra' => 'string|required',
            'informacaoComplementar' => 'string',
            'amparoLegal' => 'integer|required',
        ];
    }
}
