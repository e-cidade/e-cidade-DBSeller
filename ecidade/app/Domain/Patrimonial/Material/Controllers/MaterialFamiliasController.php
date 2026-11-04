<?php

namespace App\Domain\Patrimonial\Material\Controllers;

use App\Domain\Patrimonial\Material\Requests\AlterarMaterialFamiliaRequest;
use App\Domain\Patrimonial\Material\Requests\IncluirMaterialFamiliaRequest;
use App\Domain\Patrimonial\Material\Requests\ExcluirMaterialFamiliaRequest;
use \Exception;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Material\Services\MaterialFamiliasService;
use App\Http\Controllers\Controller;

class MaterialFamiliasController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param MaterialFamiliasService $service
     * @return DBJsonResponse
     */
    public function incluir(IncluirMaterialFamiliaRequest $request, MaterialFamiliasService $service)
    {
        try {
            $resposta = $service->incluir($request);
        } catch (Exception $e) {
            throw new Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return new DBJsonResponse($resposta, 'Família incluída com sucesso.', 201);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param MaterialFamiliasService $service
     * @return DBJsonResponse
     */
    public function alterar(AlterarMaterialFamiliaRequest $request, MaterialFamiliasService $service)
    {
        try {
            $resposta = $service->alterar($request);
        } catch (Exception $e) {
            throw new Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return new DBJsonResponse($resposta, 'Família alterada com sucesso.', 201);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param MaterialFamiliasService $service
     * @return DBJsonResponse
     */
    public function excluir(ExcluirMaterialFamiliaRequest $request, MaterialFamiliasService $service)
    {
        try {
            $resposta = $service->excluir($request);
        } catch (Exception $e) {
            throw new Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return new DBJsonResponse($resposta, 'Família excluída com sucesso.', 201);
    }
}
