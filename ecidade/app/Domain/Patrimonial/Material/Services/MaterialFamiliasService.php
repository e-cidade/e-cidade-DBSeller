<?php

namespace App\Domain\Patrimonial\Material\Services;

use App\Domain\Patrimonial\Material\Models\MaterialFamilia;
use App\Domain\Patrimonial\Material\Requests\AlterarMaterialFamiliaRequest;
use App\Domain\Patrimonial\Material\Requests\ExcluirMaterialFamiliaRequest;
use App\Domain\Patrimonial\Material\Requests\IncluirMaterialFamiliaRequest;

class MaterialFamiliasService
{
    public function incluir(IncluirMaterialFamiliaRequest $request)
    {
        $familia = new MaterialFamilia();
        $familia->m105_descricao = $request->descricao;
        return $familia->save();
    }

    public function alterar(AlterarMaterialFamiliaRequest $request)
    {
        $familia = new MaterialFamilia();
        $familia = $familia->where('m105_sequencial', '=', $request->sequencial)->first();

        if (empty($familia)) {
            throw new \Exception('Família não encontrada.');
        }

        $familia->m105_descricao = $request->descricao;
        return $familia->save();
    }

    public function excluir(ExcluirMaterialFamiliaRequest $request)
    {
        $familia = new MaterialFamilia();
        $familia = $familia->where('m105_sequencial', $request->sequencial)->first();

        if (empty($familia)) {
            throw new \Exception('Família não encontrada.');
        }

        return $familia->delete();
    }
}
