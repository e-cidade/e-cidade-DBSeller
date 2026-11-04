<?php

namespace App\Domain\Patrimonial\Protocolo\Controller\Processo;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;
use DBDepartamentoRepository;
use ECidade\Patrimonial\Protocolo\Servicos\AndamentoProcessoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AndamentoProcessoController extends Controller
{
    public function __construct(AndamentoProcessoService $andamentoProcessoService)
    {
        $this->andamentoProcessoService = $andamentoProcessoService;
    }

    public function despachosAnteriores($codProc, Request $request)
    {
        if (empty($codProc)) {
            return new DBJsonResponse([], "Erro ao buscar despachos anteriores, código do processo não informado", 400);
        }

        $perPage = 5;
        if ($request->get('perPage') !== null) {
            $perPage = $request->get('perPage');
        }

        try {
            $result = $this->andamentoProcessoService->despachosAnteriores($codProc, $perPage);
            return new DBJsonResponse(
                array(
                    'userId' => $request->user('api')->id_usuario,
                    'data' => $result
                )
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return new DBJsonResponse([], "Erro ao buscar despachos anteriores", 400);
        }
    }

    public function buscarDepartamentosByInstituicao()
    {
        $instituicoes = \InstituicaoRepository::getInstituicoes();
        $rs = array();

        foreach ($instituicoes as $instituicao) {
            try {
                $departamento = \DBDepartamento::findByInstituicao(
                    $instituicao->getCodigo(),
                    !empty($this->parametros->filtraDepartamentosPorDataLimite) ? true : false
                );
            } catch (\Exception $e) {
                Log::error($e->getMessage(), $e->getTrace());
                return new DBJsonResponse([], "Erro ao buscar instituições", 400);
            }

            $rs[] = (object)array(
                'codigo' => $instituicao->getCodigo(),
                'descricao' => $instituicao->getDescricao(),
                'departamentos' => $departamento
            );
        }
        return new DBJsonResponse($rs);
    }

    public function buscarUsuariosPorDepartamento($codigoDepartamento)
    {
        if (empty($codigoDepartamento)) {
            return new DBJsonResponse([], "Erro ao buscar usurários do departamento", 400);
        }

        $usuariosPorDepartamento = null;
        try {
            $departamento = DBDepartamentoRepository::getPorCodigo($codigoDepartamento);
            $usuariosPorDepartamento = $departamento->getUsuariosParaSelect();
            return new DBJsonResponse($usuariosPorDepartamento, "Usuários encontradas com sucesso", 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return new DBJsonResponse([], "Erro ao buscar usuário pelo departamento", 400);
        }
    }

    public function buscarInstituicoes()
    {
        try {
            $resposta = $this->andamentoProcessoService->buscarInstituicoes();
            return new DBJsonResponse($resposta, "Insituições encontradas com sucesso", 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return new DBJsonResponse([], "Erro ao buscar instituições", 400);
        }
    }

    public function transferir(Request $request)
    {

        $this->validate($request, [
            'departamentoDestino' => 'required',
            'codigoProcesso' => 'required',
        ]);


        $parametros = (object)[
            'departamentoDestino' => intval($request->get('departamentoDestino')),
            'recebimentoDestino' => intval($request->get('recebimentoDestino'))
        ];

        $this->andamentoProcessoService->setParametros($parametros);

        try {
            $codigoTransferencia = null;
            if (is_array($request->get('codigoProcesso'))) {
                 $resposta = $this->andamentoProcessoService->transferirEmMassa($request->get('codigoProcesso'));
                 $codigoTransferencia = $resposta->codigoTransferencia;
            } else {
                $resposta = $this->andamentoProcessoService->transferir(
                    $request->get('codigoProcesso')
                );
                $codigoTransferencia = $resposta["codigoTransferencia"];
            }

            $geraRecebimentoTransferencia = $this->andamentoProcessoService->getParametrosProtocolo()
                ->p90_gera_termo_transferencia;
            if ($geraRecebimentoTransferencia === "f") {
                $geraRecebimentoTransferencia = false;
            } elseif ($geraRecebimentoTransferencia === "t") {
                $geraRecebimentoTransferencia = true;
            }

            return new DBJsonResponse(
                [
                    $resposta,
                    "geraDocTransferencia" => $geraRecebimentoTransferencia,
                    "codigoTransferencia" => $codigoTransferencia
                ],
                "Processo Transferido",
                200
            );
        } catch (\Exception $e) {
            return new DBJsonResponse([], "Erro ao transferir processo: {$e->getMessage()}", 400);
        }
    }
}
