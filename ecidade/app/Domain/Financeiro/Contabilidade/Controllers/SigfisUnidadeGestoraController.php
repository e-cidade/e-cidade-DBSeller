<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Services\SigfisUnidadeGestoraService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SigfisUnidadeGestoraController extends Controller
{
    private $service;

    public function __construct(SigfisUnidadeGestoraService $service)
    {
        $this->service = $service;
    }

    /**
     * Lista Unidades Gestoras
     *
     * @return DBJsonResponse
     *
     * @api {get} financeiro/contabilidade/tce/rj/sigfis/unidadegestora 01 - Lista Unidades Gestoras
     * @apiName indexSigfisUnidadeGestora
     * @apiGroup Contabilidade-TCE-RJ-Sigfis
     *
     * @apiSuccess {Object[]} data Unidades gestoras cadastradas
     * @apiSuccess {Integer} data.codigo Codigo da unidade gestora
     * @apiSuccess {Integer} data.instit Instit da unidade gestora
     * @apiSuccess {String} data.codnomeinstit Nome da instituicao
     *
     * @apiUse Error
     */
    public function index()
    {
        try {
            $unidadesGestoras = $this->service->all();
            return new DBJsonResponse($unidadesGestoras);
        } catch (\Throwable $th) {
            return new DBJsonResponse(null, $th->getMessage(), 500);
        }
    }

    /**
     * Busca Unidade Gestora por codigo
     *
     * @param Request $request
     * @param int $codigo Codigo da unidade gestora
     *
     * @return DBJsonResponse
     *
     * @api {get} financeiro/contabilidade/tce/rj/sigfis/unidadegestora/:id 02 - Busca Unidade Gestora por codigo
     * @apiName showSigfisUnidadeGestora
     * @apiGroup Contabilidade-TCE-RJ-Sigfis
     *
     * @apiParam {Number} id Codigo da unidade gestora
     *
     * @apiSuccess {Object} data Unidades gestoras cadastradas
     * @apiSuccess {Integer} data.codigo Codigo da unidade gestora
     * @apiSuccess {Integer} data.instit Instit da unidade gestora
     * @apiSuccess {String} data.codnomeinstit Nome da instituicao
     *
     * @apiUse Error
     */
    public function show(Request $request, $codigo)
    {
        try {
            $unidadeGestora = $this->service->findBy('c179_codigo', $codigo);
            return new DBJsonResponse($unidadeGestora);
        } catch (\Throwable $th) {
            return new DBJsonResponse(null, $th->getMessage(), 500);
        }
    }


    /**
     * Persiste dados da unidade gestora
     *
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {post} financeiro/contabilidade/tce/rj/sigfis/unidadegestora 03 - Salva Unidade Gestora
     * @apiName storeSigfisUnidadeGestora
     * @apiGroup Contabilidade-TCE-RJ-Sigfis
     *
     * @apiQuery {Number} codigo Codigo da unidade gestora
     * @apiQuery {Number} instit Instit da unidade gestora
     * @apiQuery {Number} [sequencial] Sequencial da unidade gestora
     * @apiQuery {Boolean} responsavelfolha Se unidade gestora e responsavel pela folha
     * @apiQuery {Number} [codigofolha] Codigo UG da Folha
     * @apiQuery {Number} cgmordenadordespesa Cgm do Ordenador de Despesa
     *
     * @apiSuccess (200) Salvo com sucesso
     *
     * @apiUse Error
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'codigo' => 'required|int',
            'instit' => 'required|int',
            'sequencial' => 'int',
            'responsavelfolha' => 'required|bool',
            'codigofolha' => 'required_if:responsavelfolha,false',
            'cgmordenadordespesa' => 'required|int'
        ]);

        try {
            $this->service->store((object) $request->all());
            return new DBJsonResponse(null, 'Salvo com sucesso');
        } catch (\Throwable $th) {
            return new DBJsonResponse(null, $th->getMessage(), 500);
        }
    }
}
