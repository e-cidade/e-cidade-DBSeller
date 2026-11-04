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
 *@property $ano
 *@property $numero
 *@property $licitacao
 *@property $solicitacao
 */
class ImportacaoCEARequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'cnpj' => 'string|required',
            'ano' => 'integer|required',
            'numero' => 'integer|required',
            'licitacao' => 'integer|sometimes',
            'solicitacao' => 'integer|sometimes',
        ];
    }
}
