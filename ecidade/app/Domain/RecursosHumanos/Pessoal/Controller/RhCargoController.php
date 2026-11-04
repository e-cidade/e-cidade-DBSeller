<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Model\RhCargo;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class RhCargoController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function listFuncao(Request $request)
    {
        try {
            $campos = ['rh04_codigo', 'rh04_descr', 'rh04_instit'];
            $rhcargo = RhCargo::where(
                'rh04_instit',
                db_getsession('DB_instit')
            );
            if ($request->rh04_codigo) {
                $rhcargo = $rhcargo->where(
                    'rh04_codigo',
                    $request->rh04_codigo
                );
            }
            if ($request->rh04_descr) {
                $rhcargo = $rhcargo->where(
                    'rh04_descr',
                    'like',
                    "%{$request->rh04_descr}%"
                );
            }
            return new DBJsonResponse($rhcargo->get($campos));
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }
}
