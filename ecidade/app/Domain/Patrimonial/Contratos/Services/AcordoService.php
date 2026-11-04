<?php

namespace App\Domain\Patrimonial\Contratos\Services;

use App\Domain\Patrimonial\Contratos\Models\AcordoPosicao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Domain\Patrimonial\Contratos\Models\Acordo;

class AcordoService
{
    /**
     * @param Request $request
     * @return Builder[]|Collection
     */
    public function buscarAcordos(Request $request)
    {
        $acordo = Acordo::query();
        $acordo->with(['contratado', 'contratoPncp']);

        return $this->montarQueryAcordo($request, $acordo)->get();
    }

    /**
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    private function montarQueryAcordo(Request $request, Builder $query)
    {
        if (!empty((int)$request->ac16_sequencial)) {
            $query->where(['ac16_sequencial' => $request->ac16_sequencial]);
        }

        if (!empty((int)$request->ac16_anousu)) {
            $query->where(['ac16_anousu' => $request->ac16_anousu]);
        }

        if (!empty($request->ac16_numero)) {
            $query->where(['ac16_numero' => $request->ac16_numero]);
        }

        if (!empty((int)$request->ac16_acordosituacao)) {
            $query->where(['ac16_acordosituacao' => $request->ac16_acordosituacao]);
        }

        if (!empty((int)$request->ac16_coddepto)) {
            $query->where(function ($query) use ($request) {
                $query->where(['ac16_coddepto' => $request->ac16_coddepto])
                    ->orWhere(['ac16_deptoresponsavel' => $request->ac16_coddepto]);
            });
        }

        if (!empty((int)$request->ac16_instit)) {
            $query->where(['ac16_instit' => $request->ac16_instit]);
        }

        return $query;
    }

    /**
     * @param Request $request
     * @return Acordo|null
     */

    public function buscarAcordo(Request $request)
    {
        return Acordo::find($request->codigoAcordo);
    }

    /**
     * Função específica / não dinâmica, usar preferencialmente buscarAcordos
     * Adicionar parâmetros no montarQuery se necessário
     *
     * @param Request $request
     * @return Acordo[]|Collection
     */
    public function buscarTodosAcordos(Request $request)
    {
        $instit = db_getsession('DB_instit');
        return Acordo::select(
            'acordo.*',
            DB::raw("(ac16_numero || '/' || ac16_anousu) as ac16_numeroacordo"),
            'ac16_numero',
            'ac17_descricao',
            'z01_nome',
            'descrdepto',
            'ac28_descricao'
        )
            ->join('db_depart', 'coddepto', '=', 'ac16_coddepto')
            ->join('cgm', 'z01_numcgm', '=', 'ac16_contratado')
            ->join('acordogrupo', 'ac02_sequencial', '=', 'ac16_acordogrupo')
            ->join('acordosituacao', 'ac17_sequencial', '=', 'ac16_acordosituacao')
            ->join('acordoorigem', 'ac28_sequencial', '=', 'ac16_origem')
            ->where('ac16_instit', $instit)
            ->where('ac16_acordosituacao', '=', '4')
            ->where(function ($query) {
                $dept = db_getsession('DB_coddepto');
                $query->where('ac16_coddepto', '=', $dept)
                    ->orWhere('ac16_deptoresponsavel', '=', $dept);
            })
            ->orderBy('ac16_sequencial')
            ->get();
    }
}
