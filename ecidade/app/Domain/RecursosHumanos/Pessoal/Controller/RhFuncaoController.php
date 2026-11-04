<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Model\RegimeRecursosHumanos;
use App\Domain\RecursosHumanos\Pessoal\Model\RhFuncao;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class RhFuncaoController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function listCargo(Request $request)
    {
        try {
            $campos = ['rh37_funcao', 'rh37_descr', 'rh37_instit'];
            $rhfuncao = RhFuncao::where(
                'rh37_instit',
                db_getsession('DB_instit')
            );
            if ($request->rh37_funcao) {
                $rhfuncao = $rhfuncao->where(
                    'rh37_funcao',
                    $request->rh37_funcao
                );
            }
            if ($request->rh37_descr) {
                $rhfuncao = $rhfuncao->where(
                    'rh37_descr',
                    'like',
                    "%{$request->rh37_descr}%"
                );
            }
            return new DBJsonResponse($rhfuncao->get($campos));
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function listRegime(Request $request)
    {
        try {
            $campos = ['rh30_codreg', 'rh30_descr', 'rh30_instit'];
            $rhfuncao = RegimeRecursosHumanos::where(
                'rh30_instit',
                db_getsession('DB_instit')
            );
            if ($request->rh30_descr) {
                $rhfuncao = $rhfuncao->where(
                    'rh30_descr',
                    $request->rh30_descr
                );
            }
            if ($request->rh30_descr) {
                $rhfuncao = $rhfuncao->where(
                    'rh30_descr',
                    'like',
                    "%{$request->rh30_descr}%"
                );
            }
    
            return new DBJsonResponse($rhfuncao->get($campos));
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }
}
