<?php

namespace App\Domain\Saude\Farmacia\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Ambulatorial\Requests\AtualizarCgsCpfRequest;
use App\Domain\Saude\Ambulatorial\Services\CgsUnidadeService;

class CgsUnidadeController
{
    /**
     * @var CgsUnidadeService $cgsUnidadeService
     */
    private $cgsUnidadeService;

    public function __construct(CgsUnidadeService $cgsUnidadeService)
    {
        $this->cgsUnidadeService = $cgsUnidadeService;
    }

    /**
     * @api {post} saude/farmacia/procedimento/atualizar/paciente/cgs-cpf 01 - Atualizar CPF e/ou CNS do CGS
     * @apiDescription Atualiza ou cria o CPF e/ou CNS do CGS informado
     * @apiName AtualizarCgsCpf
     * @apiGroup Saúde-Ambulatorial
     * @apiParam {Integer} cgsPaciente Número do cgs do paciente.
     * @apiParam {Integer} [idCartaoSus] código id do CNS do paciente.
     * @apiParam {String} [cpf] Novo Valor do CPF do paciente.
     * @apiParam {String} [cns] Novo Valor do CNS do paciente.
     *
     * @apiSuccess {Object} data Objeto contendo a situação da operação.
     * @apiSuccess {Boolean} data.sucesso Indica se a requisição foi processada com sucesso.
     * @apiSuccess {Boolean} error Indica se ocorreu um erro.
     * @apiSuccess {String} message Mensagem da requisição.
     *
     * @apiError (500) {Object} data Objeto contendo a situação da operação.
     * @apiError (500) {Boolean} data.sucesso Indica se a requisição foi processada com sucesso.
     * @apiError (500) {Boolean} error Indica se ocorreu um erro.
     * @apiError (500) {String} message Mensagem da requisição.
     */
    public function atualizarCgsCpf(AtualizarCgsCpfRequest $request)
    {
        $salvou = $this->cgsUnidadeService->atualizarCgsCpf((object)$request->all());
        $resposta = (object)[
            'sucesso' => $salvou->sucesso
        ];

        return new DBJsonResponse($resposta, $salvou->mensagem, $salvou->statusCode);
    }
}
