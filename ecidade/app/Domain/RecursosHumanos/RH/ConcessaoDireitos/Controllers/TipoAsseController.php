<?php

namespace App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\TipoAsse;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class TipoAsseController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function listAssentamento(Request $request)
    {
        try {
            $campos = ['h12_codigo','h12_assent','h12_descr'];
            if ($request->h12_codigo) {
                $tipoasse = TipoAsse::where(
                    'h12_codigo',
                    $request->h12_codigo
                )->get($campos);
            } elseif ($request->h12_assent) {
                $tipoasse = TipoAsse::where(
                    'h12_assent',
                    'like',
                    "%{$request->h12_assent}%"
                )->get($campos);
            } elseif ($request->h12_descr) {
                $tipoasse = TipoAsse::where(
                    'h12_descr',
                    'like',
                    "%{$request->h12_descr}%"
                )->get($campos);
            } else {
                $tipoasse = TipoAsse::all($campos);
            }
            return new DBJsonResponse($tipoasse);
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }
}
