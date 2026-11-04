<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Services\MapeamentoEmpenhoRPContaService;
use App\Http\Controllers\Controller;
use BusinessException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MapeamentoEmpenhoRPContaController extends Controller
{
    protected $service;

    /**
     * Contrutor
     *
     * @param MapeamentoEmpenhoRPContaService $service
     */
    public function __construct(MapeamentoEmpenhoRPContaService $service)
    {
        $this->service = $service;
    }

    /**
     * Contas Mapeadas
     *
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {get} financeiro/contabilidade/procedimento/mapeamento-empenho-rp-conta 01 - Contas Mapeadas
     * @apiName ContasMapeadas
     * @apiGroup Contabilidade-MapeamentoEmpenhoRPSaldo
     * @apiQuery {Number} instituicao Codigo da instituicao
     * @apiQuery {Number} exercicio Ano do exercicio
     * @apiSuccess {Object[]} data Contas mapeadas
     * @apiUse Error
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'instituicao'  => 'required|numeric',
            'exercicio' => 'required|digits:4',
        ]);

        try {
            $mapeamentos = $this->service->getContasMapeadas((object) $request->all());
            return new DBJsonResponse($mapeamentos);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 406);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro interno. Consulte os logs.', 406);
        }
    }

    /**
     * Contas para mapear
     *
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {get} financeiro/contabilidade/procedimento/mapeamento-empenho-rp-conta/contas 02 - Contas para mapear
     * @apiName ContasMapear
     * @apiGroup Contabilidade-MapeamentoEmpenhoRPSaldo
     * @apiSuccess {Object[]} data Contas para mapear
     * @apiUse Error
     */
    public function contas(Request $request)
    {
        $instit = session('DB_instit');
        $exercicio = session('DB_anousu');

        try {
            $contas = $this->service->getContasRP($exercicio, $instit);
            return new DBJsonResponse($contas);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 406);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro interno. Consulte os logs.', 406);
        }
    }

    /**
     * Empenhos a mapear
     *
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {get} financeiro/contabilidade/procedimento/mapeamento-empenho-rp-conta/empenhos 03 - Empenhos a mapear
     * @apiName EmpenhosMapear
     * @apiGroup Contabilidade-MapeamentoEmpenhoRPSaldo
     * @apiQuery {Number} estrutural Codigo da conta estrutural
     * @apiQuery {Number} exercicio Ano do exercicio
     * @apiQuery {Number} tipoLiquidacao Tipo liquidacao
     * @apiSuccess {Object[]} data Empenhos
     * @apiUse Error
     */
    public function empenhos(Request $request)
    {
        $this->validate($request, [
            'reduzido'  => 'required|numeric',
            'exercicio' => 'required|digits:4',
            'tipoLiquidacao' => 'required|numeric',
            'tipoBusca' => 'required|numeric',
        ]);

        try {
            $empenhos = $this->service->getEmpenhos((object) $request->all());
            return new DBJsonResponse($empenhos);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 406);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro interno. Consulte os logs.', 406);
        }
    }

    /**
     * Empenhos da conta
     *
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {get} financeiro/contabilidade/procedimento/mapeamento-empenho-rp-conta/empenhos-conta 04 -Empenhos da conta
     * @apiName EmpenhosConta
     * @apiGroup Contabilidade-MapeamentoEmpenhoRPSaldo
     * @apiQuery {Number} reduzido Codigo da conta reduzida
     * @apiQuery {Number} exercicio Ano do exercicio
     * @apiQuery {String} empenho Filtros do empenho
     * @apiSuccess {Object[]} data Empenhos
     * @apiUse Error
     */
    public function empenhosConta(Request $request)
    {
        $this->validate($request, [
            'reduzido'  => 'required|numeric',
            'exercicio' => 'required|digits:4',
            'empenho'   => 'string'
        ]);

        try {
            $empenhos = $this->service->getEmpenhosConta((object) $request->all());
            return new DBJsonResponse($empenhos);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 406);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro interno. Consulte os logs.', 406);
        }
    }

    /**
     * Salvar mapeamento
     *
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {post} financeiro/contabilidade/procedimento/mapeamento-empenho-rp-conta/save 05 - Salvar mapeamento
     * @apiName SaveMapeamento
     * @apiGroup Contabilidade-MapeamentoEmpenhoRPSaldo
     * @apiQuery {Number} reduzido Codigo da conta reduzida
     * @apiQuery {Number} exercicio Ano do exercicio
     * @apiQuery {Object[]} empenhos Lista de empenhos
     * @apiSuccess {Object[]} data
     * @apiUse Error
     */
    public function save(Request $request)
    {
        $this->validate($request, [
            'reduzido'  => 'required|numeric',
            'exercicio' => 'required|digits:4',
            'empenhos'  => 'required|array'
        ]);

        try {
            $this->service->saveEmpenhos((object) $request->all());
            return new DBJsonResponse(null, 'Salvo com sucesso');
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 406);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro interno. Consulte os logs.', 406);
        }
    }

    /**
     * Deletar Mapeamento
     *
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {delete} financeiro/contabilidade/procedimento/mapeamento-empenho-rp-conta/delete 06 - Deletar Mapeamento
     * @apiName DeleteMapeamento
     * @apiGroup Contabilidade-MapeamentoEmpenhoRPSaldo
     * @apiQuery {Number} codigo do mapeamento
     * @apiSuccess {Object[]} data
     * @apiUse Error
     */
    public function delete(Request $request)
    {
        $this->validate($request, ['codigo' => 'required|numeric']);

        try {
            $this->service->deleteEmpenho($request->codigo);
            return new DBJsonResponse(null, 'Excluido com sucesso');
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 406);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro interno. Consulte os logs.', 406);
        }
    }

    /**
     * Deletar Mapeamento
     *
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {delete} financeiro/contabilidade/procedimento/mapeamento-empenho-rp-conta/delete 07 - Deletar Mapeamento
     * @apiName DeleteMapeamento
     * @apiGroup Contabilidade-MapeamentoEmpenhoRPSaldo
     * @apiQuery {Number} reduzido Reduzido
     * @apiQuery {Number} exercicio Ano do exercicio
     * @apiSuccess {Object[]} data
     * @apiUse Error
     */
    public function deleteAll(Request $request)
    {
        $this->validate($request, [
            'reduzido'  => 'required|numeric',
            'exercicio' => 'required|digits:4',
        ]);

        try {
            $this->service->deleteAllEmpenhos($request->reduzido, $request->exercicio);
            return new DBJsonResponse(null, 'Excluido com sucesso');
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 406);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro interno. Consulte os logs.', 406);
        }
    }
}
