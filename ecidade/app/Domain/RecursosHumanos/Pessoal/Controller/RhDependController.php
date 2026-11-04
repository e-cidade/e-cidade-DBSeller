<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Model\RhDepend;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class RhDependController extends Controller
{
    public function listDependente(Request $request)
    {
        try {
            $campos = ['rh31_codigo', 'rh31_nome', 'rh31_dtnasc', 'rh01_regist', 'z01_nome'];

            $where = [
                ['rh01_instit',  $request->DB_instit]
            ];

            $rhdepend = RhDepend::select($campos)
                ->join('rhpessoal', 'rh01_regist', '=', 'rh31_regist')
                ->join('cgm', 'z01_numcgm', '=', 'rh01_numcgm')
                ->where($where);

            if ($request->rh31_nome) {
                $rhdepend = $rhdepend->where(
                    'rh31_nome',
                    'like',
                    "%{$request->rh31_nome}%"
                )->get();
            } elseif ($request->z01_nome) {
                $rhdepend = $rhdepend->where(
                    'z01_nome',
                    'like',
                    "%{$request->z01_nome}%"
                )->get();
            } elseif ($request->rh01_regist) {
                $rhdepend = $rhdepend->where(
                    'rh01_regist',
                    '=',
                    "{$request->rh01_regist}"
                )->get();
            } else {
                $rhdepend = $rhdepend->get();
            }
            return new DBJsonResponse($rhdepend);
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }

    public function getDependenteByServidor($servidor)
    {
        $campos = [
            'rh31_codigo',
            'rh31_nome',
            'rh31_dtnasc',
            'dp01_cpf',
            'rh31_gparen'
        ];

        $listaDependentes = RhDepend::select($campos)
            ->leftJoin('rhdependeplug', 'dp01_rhdepend', '=', 'rh31_codigo')
            ->where('rh31_regist', '=', $servidor)
            ->get()
            ->toArray();

        $dependentes = array_map(function ($dependente) {
            $dependente = (object) $dependente;
            $dependente->idade = Carbon::parse($dependente->rh31_dtnasc)->age;
            return $dependente;
        }, $listaDependentes);

        return new DBJsonResponse($dependentes);
    }
}
