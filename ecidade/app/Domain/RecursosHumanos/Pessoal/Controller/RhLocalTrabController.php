<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Model\RhLocalTrab;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class RhLocalTrabController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function listLocal(Request $request)
    {
        try {
            $campos = ['rh55_codigo', 'rh55_estrut', 'rh55_descr', 'rh55_instit'];
            $rhlocaltrab = RhLocalTrab::where(
                'rh55_instit',
                db_getsession('DB_instit')
            );
            if ($request->rh55_codigo) {
                $rhlocaltrab = $rhlocaltrab->where(
                    'rh55_codigo',
                    $request->rh55_codigo
                );
            }
            if ($request->rh55_estrut) {
                $rhlocaltrab = $rhlocaltrab->where(
                    'rh55_estrut',
                    $request->rh55_estrut
                );
            }
            if ($request->rh55_descr) {
                $rhlocaltrab = $rhlocaltrab->where(
                    'rh55_descr',
                    'like',
                    "%{$request->rh55_descr}%"
                );
            }
            return new DBJsonResponse($rhlocaltrab->get($campos));
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }
}
