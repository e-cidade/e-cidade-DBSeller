<?php

namespace App\Domain\Saude\Ambulatorial\Requests;

class UnificacaoCgsRequest
{
    public static function validaRequestSalvar($dadosRequest)
    {
        $rules =  [
            'cgs_correto' => ['required','integer'],
            'unificado' => ['required','boolean'],
            'errados' => ['required', 'array'],
            'errados.*.cgs_errado' => ['required', 'integer'],
            'errados.*.nome' => ['required', 'string'],
        ];
                
        validaRequest($dadosRequest, $rules);
    }

    public static function validaRequestDetalharUnificacao($dadosRequest)
    {
        $rules =  [
            'codigo_unificacao' => ['required','integer']
        ];
                
        validaRequest($dadosRequest, $rules);
    }

    public static function validaRequestProcessarUnificacao($dadosRequest)
    {
        $rules =  [
            'codigo_unificacao' => ['required','integer'],
            'cgs_correto' =>  ['required','integer']
        ];
                
        validaRequest($dadosRequest, $rules);
    }

    public static function validaRequestExcluir($dadosRequest)
    {
        
        $rules =  [
            'codigo_unificacao' => ['required','integer']
        ];
        validaRequest($dadosRequest, $rules);
    }
}
