<?php

namespace App\Domain\Patrimonial\PNCP\Services;

use App\Domain\Patrimonial\Compras\Services\SolicitacaoService;
use App\Domain\Patrimonial\Licitacoes\Models\Licitacao;
use App\Domain\Patrimonial\PNCP\Clients\PNCPClient;
use App\Domain\Patrimonial\PNCP\Exceptions\CompraEditalAvisoExcpetion;
use App\Domain\Patrimonial\PNCP\Models\ComprasPncp;
use App\Domain\Patrimonial\PNCP\Repositories\ItensLicitacaoRepository;
use App\Domain\Patrimonial\PNCP\Resources\CompraEditalAvisoResource;
use DBAttDinamicoValor;
use DBException;
use Exception;
use Illuminate\Http\Request;
use InstituicaoRepository;
use licitacao as oLicitacao;
use OrcamentoLicitacao;

class ItensLicitacaoService
{
    private $http;

    public function __construct()
    {
        $this->http = new PNCPClient();
    }

    /**
     * @param Request $request
     * @return object
     * @throws Exception
     */
    public function buscarLicitacao($request)
    {
        $licitacao = $request->licitacao;
        $resultadoItem = $request->resultadoItem;
        $instituicao = InstituicaoRepository::getInstituicaoByCodigo($request->DB_instit);
        $documento = $instituicao->getCNPJ();

        $dados = [];
        if (!empty($resultadoItem)) {
            $compra = ComprasPncp::where('pn03_liclicita', $licitacao)->first();
            if (is_null($compra)) {
                throw new Exception("Não foi encontrado compra para a licitação {$licitacao}");
            }
            try {
                $objeto = (object)[
                    'cnpj' => $documento,
                    'ano' => $compra->pn03_ano,
                    'numero' => $compra->pn03_numero
                ];

                $client = new PNCPClient(true);
                $resultado = $client->buscarCompra($objeto);
            } catch (CompraEditalAvisoExcpetion $e) {
                throw new Exception($e->getErros());
            }
            $dados = [
                'modalidadeCompra' => $resultado->modalidadeNome,
                'instrumentoConvocatorio' => $resultado->tipoInstrumentoConvocatorioNome,
                'numeroCompra' => "{$resultado->numeroCompra}/{$resultado->anoCompra}",
                'objetoCompra' => $resultado->objetoCompra,
            ];
        } else {
            $compra = ComprasPncp::where('pn03_liclicita', $licitacao)->first();
            if (!empty($compra)) {
                $dados['link'] = "https://pncp.gov.br/app/editais/";
                $dados['link'] .= "{$documento}/{$compra->pn03_ano}/{$compra->pn03_numero}";
                $dados['sequencialCompra'] = $compra->pn03_numero;
            }
        }

        if (empty($licitacao)) {
            throw new Exception('Codigo da licitação não pode ser vazio');
        }
        $model = Licitacao::where('l20_codigo', $licitacao)->first();
        $licitacao = new \licitacao($licitacao);
        $itens = $licitacao->getItens();

        $orcamentoSigiloso = 'f';
        if (!is_null($model->orcamentoSigiloso()->first())) {
            $valores = DBAttDinamicoValor::getValores($model->getOrcamentoSigiloso());
            $orcamentoSigiloso = '';
            foreach ($valores as $valor) {
                if ($valor->getAtributo()->getNome() === 'orcamentosigiloso') {
                    $orcamentoSigiloso = $valor->getValor();
                }
            }
        }
        return CompraEditalAvisoResource::toResponse($model, $orcamentoSigiloso, $itens, $dados);
    }

    /**
     * @param oLicitacao $oLicitacao
     * @return string
     * @throws DBException
     */
    private function getValorLicitacao(oLicitacao $oLicitacao)
    {
        $oOrcamentoLicitacao = new OrcamentoLicitacao($oLicitacao);
        return number_format($oOrcamentoLicitacao->getValorTotalEstimado(), 2, ',', '');
    }
}
