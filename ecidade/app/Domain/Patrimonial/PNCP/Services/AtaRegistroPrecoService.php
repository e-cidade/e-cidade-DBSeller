<?php

namespace App\Domain\Patrimonial\PNCP\Services;

use App\Domain\Patrimonial\PNCP\Builders\AtaRegistroPrecoBuilder;
use App\Domain\Patrimonial\PNCP\Builders\RetificacaoAtaBuilder;
use App\Domain\Patrimonial\PNCP\Clients\AtaRegistroPrecoClient;
use App\Domain\Patrimonial\PNCP\Exceptions\CompraEditalAvisoExcpetion;
use App\Domain\Patrimonial\PNCP\Models\ComprasPncp;
use Exception;
use Illuminate\Http\Request;

class AtaRegistroPrecoService
{
    private $http;

    public function __construct()
    {
        $this->http = new AtaRegistroPrecoClient();
    }


    /**
     * @param Request $request
     * @return string[]
     * @throws Exception
     */
    public function incluir(Request $request)
    {
        $compra = ComprasPncp::where('pn03_codigo', $request->get('pn03_codigo'))->first();
        $builder = new AtaRegistroPrecoBuilder();
        $builder->setDados($request);
        $dados = $builder->build();
        $link = "https://pncp.gov.br/app/atas/{$request->get('cnpj')}/{$compra->pn03_ano}/$compra->pn03_numero/";


        if (!empty($compra)) {
            try {
                $response = $this->http->incluir(
                    $request->get('cnpj'),
                    $compra->pn03_ano,
                    $compra->pn03_numero,
                    $dados
                );
                $response = explode('/', $response);
                if ($request->tituloDocumento !== '') {
                    $this->enviarDocumento(
                        $request,
                        $request->get('cnpj'),
                        $compra->pn03_ano,
                        $compra->pn03_numero,
                        $response[11]
                    );
                }
                return [
                    'link' => $link . $response[11]
                ];
            } catch (CompraEditalAvisoExcpetion $e) {
                throw new Exception($e->getErros());
            }
        }
    }

    /**
     * @throws Exception
     */
    private function enviarDocumento($request, $documento, $ano, $sequencialCompra, $sequencialAta)
    {
        $anexoAta = [];
        $anexoAta['documento'] = $documento;
        $anexoAta['anoCompra'] = $ano;
        $anexoAta['sequencialCompra'] = $sequencialCompra;
        $anexoAta['sequencialAta'] = $sequencialAta;

        $header = [
            'Titulo-Documento' => stripslashes($request->tituloDocumento),
            'Tipo-Documento' => 11
        ];

        $multipart[0]['name'] = 'ata';
        $multipart[0]['contents'] = json_encode($anexoAta);
        $multipart[0]['headers'] = ['Content-Type' => 'application/json'];

        $multipart[1]['name'] = 'arquivo';
        $multipart[1]['contents'] = fopen($request->anexoDocumento->getPathName(), 'r');
        $multipart[1]['filename'] = urlencode(stripslashes($request->anexoDocumento->getClientOriginalName()));
        $multipart[1]['headers'] = ['Content-Type' => 'multipart/form-data'];

        $dados = [];
        $dados['headers'] = $header;
        $dados['multipart'] = $multipart;

        $linkContrato = $this->http->incluirAnexoAta($anexoAta, $dados);
        return [$linkContrato];
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function buscar(Request $request)
    {
        $comprasPncp = new ComprasPncp();
        $comprasPncp = $comprasPncp->query();

        if ($request->pn03_codigo) {
            $comprasPncp->where('pn03_codigo', $request->get('pn03_codigo'));
        }

        if ($request->licitacao) {
            $comprasPncp->where('pn03_liclicita', $request->get('licitacao'));
        }

        $compra = $comprasPncp->first();
        $response = [];
        if (!empty($compra)) {
            try {
                $response = $this->http->buscar(
                    $request->get('cnpj'),
                    $compra->pn03_ano,
                    $compra->pn03_numero
                );
            } catch (CompraEditalAvisoExcpetion $e) {
                throw new Exception($e->getErros());
            }
        }

        return $response;
    }

    public function excluir(Request $request)
    {
        $compra = ComprasPncp::where('pn03_codigo', $request->get('pn03_codigo'))->first();

        if (!empty($compra)) {
            try {
                $response = $this->http->excluir(
                    $request->get('cnpj'),
                    $compra->pn03_ano,
                    $compra->pn03_numero,
                    $request->get('sequencialAta')
                );
            } catch (CompraEditalAvisoExcpetion $e) {
                throw new Exception($e->getErros());
            }
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function retificar(Request $request)
    {
        $compra = ComprasPncp::where('pn03_liclicita', $request->get('licitacao'))->first();
        $builder = new RetificacaoAtaBuilder();
        $builder->setDados($request);
        $dados = $builder->build();
        if (!empty($compra)) {
            try {
                $this->http->retificar(
                    $request->get('cnpj'),
                    $compra->pn03_ano,
                    $compra->pn03_numero,
                    $request->get('sequencialAta'),
                    $dados
                );
            } catch (CompraEditalAvisoExcpetion $e) {
                throw new Exception($e->getErros());
            }
        }
    }
}
