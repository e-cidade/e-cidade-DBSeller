<?php

namespace App\Domain\Patrimonial\PNCP\Services;

use App\Domain\Patrimonial\PNCP\Clients\PNCPClient;
use App\Domain\Patrimonial\PNCP\Requests\ExclusaoDocumentoTermoRequest;
use App\Domain\Patrimonial\PNCP\Requests\ExclusaoTermoContratoRequest;
use App\Domain\Patrimonial\PNCP\Requests\InclusaoDocumentoTermoRequest;
use Exception;
use Illuminate\Http\Request;

class TermoService
{
    /**
     * @var PNCPClient
     */
    private $http;

    public function __construct()
    {
        $this->http = new PNCPClient();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function incluir(Request $request)
    {
        $dados = [];
        $dados['body'] = $this->montarDadosTermo($request);

        return $this->http->incluirTermo($request, $dados);
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    private function montarDadosTermo(Request $request)
    {
        $dadosTermo = [];

        if (!empty($request->tipoTermoContratoId)) {
            $dadosTermo['tipoTermoContratoId'] = intval($request->tipoTermoContratoId);
        }

        if (!empty($request->numeroTermoContrato)) {
            $dadosTermo['numeroTermoContrato'] = $this->tratarString($request->numeroTermoContrato);
        }

        if (!empty($request->objetoTermoContrato)) {
            $dadosTermo['objetoTermoContrato'] = $this->tratarString($request->objetoTermoContrato);
        }

        if (isset($request->qualificacaoAcrescimoSupressao)) {
            $dadosTermo['qualificacaoAcrescimoSupressao'] = boolval($request->qualificacaoAcrescimoSupressao);
        }

        if (isset($request->qualificacaoVigencia)) {
            $dadosTermo['qualificacaoVigencia'] = boolval($request->qualificacaoVigencia);
        }

        if (isset($request->qualificacaoFornecedor)) {
            $dadosTermo['qualificacaoFornecedor'] = boolval($request->qualificacaoFornecedor);
        }

        if (isset($request->qualificacaoInformativo)) {
            $dadosTermo['qualificacaoInformativo'] = boolval($request->qualificacaoInformativo);
        }

        if (isset($request->qualificacaoReajuste)) {
            $dadosTermo['qualificacaoReajuste'] = boolval($request->qualificacaoReajuste);
        }

        if (!empty($request->informativoObservacao)) {
            $dadosTermo['informativoObservacao'] = $this->tratarString($request->informativoObservacao);
        }

        if (!empty($request->dataAssinatura)) {
            $dadosTermo['dataAssinatura'] = $this->tratarData($request->dataAssinatura);
        }

        if (!empty($request->justificativa)) {
            $dadosTermo['justificativa'] = $this->tratarString($request->justificativa);
        }

        return $dadosTermo;
    }

    /**
     * @param $string
     * @return string
     */
    private function tratarString($string)
    {
        if (!empty($string)) {
            return urlencode(mb_convert_encoding(stripslashes($string), 'UTF-8', 'ISO-8859-1'));
        }

        return $string;
    }

    /**
     * @param $string
     * @return string
     * @throws Exception
     */
    private function tratarData($string)
    {
        $data = new \DateTime($string);
        return $data->format('Y-m-d');
    }

    /**
     * @param ExclusaoTermoContratoRequest $request
     * @return object
     */
    public function excluir(ExclusaoTermoContratoRequest $request)
    {
        return $this->http->excluirTermo($request);
    }

    /**
     * @param ExclusaoDocumentoTermoRequest $request
     * @return object
     * @throws Exception
     */
    public function excluirDocumento(ExclusaoDocumentoTermoRequest $request)
    {
        return $this->http->excluirDocumentoTermo($request);
    }

    /**
     * @param Request $request
     * @return object
     * @throws Exception
     */
    public function buscar(Request $request)
    {
        return $this->http->buscarTermos($request);
    }

    /**
     * @param ExclusaoTermoContratoRequest $request
     * @return object
     * @throws Exception
     */
    public function retificar(Request $request)
    {
        $dados = [];
        $dados['body'] = $this->montarDadosTermo($request);

        return $this->http->retificarTermo($request, $dados);
    }

    /**
     * @param InclusaoDocumentoTermoRequest $request
     * @return mixed
     */
    public function incluirDocumento(InclusaoDocumentoTermoRequest $request)
    {
        $header = [
            'Titulo-Documento' => stripslashes($request->tituloDocumento),
            'Tipo-Documento-Id' => $request->tipoDocumentoId
        ];

        $multipart[0]['name'] = 'arquivo';
        $multipart[0]['contents'] = fopen($request->documento->getPathName(), 'r');
        $multipart[0]['filename'] = urlencode(stripslashes($request->documento->getClientOriginalName()));
        $multipart[0]['headers'] = ['Content-Type' => 'multipart/form-data'];

        $dados = [];
        $dados['headers'] = $header;
        $dados['multipart'] = $multipart;

        return $this->http->incluirDocumento($request, $dados);
    }

    /**
     * @param Request $request
     * @return object
     * @throws Exception
     */
    public function buscarDocumentos(Request $request)
    {
        return $this->http->buscarTermoContratoDocumentos($request);
    }
}
