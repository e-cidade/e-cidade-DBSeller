<?php

namespace App\Domain\Patrimonial\PNCP\Resources;

class EditaisResource
{
    /**
     * @return object
     */
    public static function toResponse($edital)
    {
        return (object)[
            'editalCodigo' => $edital['l27_sequencial'],
            'editalArquivo' => $edital['l27_arquivo'],
            'nomeArquivo' => $edital['l27_arqnome'],
            'codigoLicitacao' => $edital['l27_liclicita'],
        ];
    }

    public static function toArray($editais)
    {
        $dados = [];

        foreach ($editais as $edital) {
            $dados[] = static::toResponse($edital);
        }

        return $dados;
    }
}
