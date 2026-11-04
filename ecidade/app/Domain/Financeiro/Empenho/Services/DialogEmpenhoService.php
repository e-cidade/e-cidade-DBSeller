<?php

namespace App\Domain\Financeiro\Empenho\Services;

use App\Domain\Financeiro\Empenho\Repositories\EmpenhoRepository;

class DialogEmpenhoService
{
    public function get(array $filtros)
    {
        $mapaSort = [
            "numemp" => "e60_numemp",
            "numero" => "e60_codemp",
            "dataEmissao" => "e60_emiss",
            "credor" => "z01_nome",
        ];

        $sort = "e60_numemp";
        $sortOrder = "asc";
        if (!empty($filtros['sortField'])) {
            $sort = $mapaSort[$filtros['sortField']];
            $sortOrder = $filtros['sortOrder'];
        }

        $filtros['sortField'] = $sort;
        $filtros['sortOrder'] = $sortOrder;

        return (new EmpenhoRepository())->getDataForDialogPesquisa($filtros);
    }
}
