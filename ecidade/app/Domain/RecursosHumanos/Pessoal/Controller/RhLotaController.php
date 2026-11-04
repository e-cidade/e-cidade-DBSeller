<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Model\RhLota;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class RhLotaController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function listLotacao(Request $request)
    {
        try {
            $campos = ['r70_codigo', 'r70_estrut', 'r70_descr', 'r70_instit'];
            $rhlota = RhLota::where(
                'r70_instit',
                db_getsession('DB_instit')
            );
            if ($request->r70_codigo) {
                $rhlota = $rhlota->where(
                    'r70_codigo',
                    $request->r70_codigo
                );
            }
            if ($request->r70_estrut) {
                $rhlota = $rhlota->where(
                    'r70_estrut',
                    'like',
                    "%{$request->r70_estrut}%"
                );
            }
            if ($request->r70_descr) {
                $rhlota = $rhlota->where(
                    'r70_descr',
                    'like',
                    "%{$request->r70_descr}%"
                );
            }
            return new DBJsonResponse($rhlota->get($campos));
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }
}
