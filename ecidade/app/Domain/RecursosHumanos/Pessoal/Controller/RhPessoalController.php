<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Model\RhFuncao;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\RhPessoal as ModelsRhPessoal;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Providers\Rhpessoal;
use App\Http\Controllers\Controller;
use DBPessoal;
use Exception;
use Illuminate\Http\Request;

class RhPessoalController extends Controller
{

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function listMatriculas(Request $request)
    {
        $rescindido = false;
        if (isset($request->rescindidos)) {
            $rescindido = true;
        }

        try {
            $campos = ['rh01_regist', 'rh01_numcgm', 'z01_nome', 'rh01_admiss'];

            $where = [
                ['rh02_anousu', DBPessoal::getAnoFolha()],
                ['rh02_mesusu', DBPessoal::getMesFolha()],
                ['rh02_instit',  $request->DB_instit]
            ];

            $rhpessoal = ModelsRhPessoal::select($campos)
                ->join('rhpessoalmov', 'rh02_regist', '=', 'rh01_regist')
                ->join('cgm', 'cgm.z01_numcgm', '=', 'rhpessoal.rh01_numcgm')
                ->leftJoin('rhpesrescisao', 'rh02_seqpes', '=', 'rh05_seqpes')
                ->where($where)
                ->orderBy('z01_nome', 'asc');

            if (!$rescindido) {
                $rhpessoal->whereNull('rh05_seqpes');
            }

            if ($request->z01_nome) {
                $rhpessoal = $rhpessoal->where(
                    'z01_nome',
                    'like',
                    "%{$request->z01_nome}%"
                )->get();
            } elseif ($request->rh01_numcgm) {
                $rhpessoal = $rhpessoal->where(
                    'rh01_numcgm',
                    $request->rh01_numcgm
                )->get();
            } elseif ($request->rh01_regist) {
                $rhpessoal = $rhpessoal->where(
                    'rh01_regist',
                    $request->rh01_regist
                )->get();
            } else {
                $rhpessoal = $rhpessoal->get();
            }
            return new DBJsonResponse($rhpessoal);
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }
}
