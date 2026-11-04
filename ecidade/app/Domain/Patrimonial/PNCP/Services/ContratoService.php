<?php

namespace App\Domain\Patrimonial\PNCP\Services;

use App\Domain\Patrimonial\Contratos\Models\AcordoDocumento;
use App\Domain\Patrimonial\Contratos\Models\AcordoDocumentoEvento;
use App\Domain\Patrimonial\Contratos\Models\AcordoEncerramentoLicitacon;
use App\Domain\Patrimonial\Licitacoes\Models\AcordoEvento;
use App\Domain\Patrimonial\PNCP\Clients\PNCPClient;
use App\Domain\Patrimonial\PNCP\Models\ContratoPNCP;
use App\Domain\Patrimonial\PNCP\Requests\ExclusaoContratoRequest;
use App\Domain\Patrimonial\PNCP\Requests\InclusaoDocumentoContratoRequest;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Exception;
use Illuminate\Http\Request;

class ContratoService
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
     * @return object
     * @throws Exception
     */
    public function enviarContrato(Request $request)
    {
        $contrato = $this->montarDadosContrato($request);

        $linkContratoApi = $this->http->incluirContrato($request->cnpjCompra, $contrato);
        $dadosContratoApi = $this->http->doRequest('GET', $linkContratoApi);

        $dadosContratoApi->acordo = $contrato['acordo'];

        $linkContrato =  "https://pncp.gov.br/app/contratos/";
        $linkContrato .=  "{$dadosContratoApi->orgaoEntidade->cnpj}/{$dadosContratoApi->anoContrato}/";
        $linkContrato .=  "{$dadosContratoApi->sequencialContrato}";
        $contrato['link'] = $linkContrato;

        $this->incluirEvento($contrato);
        $this->incluirContrato($dadosContratoApi);
        $this->excluirEncerramentoContrato($contrato);

        return $dadosContratoApi;
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    private function montarDadosContrato(Request $request)
    {
        $contrato = [];
        $contrato['acordo'] = $request->acordo;
        $contrato['cnpjCompra'] = $request->cnpjCompra;
        $contrato['anoCompra'] = intval($request->anoCompra);
        $contrato['sequencialCompra'] = intval($request->sequencialCompra);
        $contrato['tipoContratoId'] = intval($request->tipoContratoId);
        $contrato['numeroContratoEmpenho'] = intval($request->numeroContratoEmpenho);
        $contrato['anoContrato'] = intval($request->anoContrato);
        $contrato['processo'] = $request->processo;
        $contrato['categoriaProcessoId'] = intval($request->categoriaProcessoId);

        $contrato['nomeRazaoSocialFornecedor'] = $this->trataString($request->nomeRazaoSocialFornecedor);
        $contrato['tipoPessoaFornecedor'] = $this->retornaTipoPessoa(
            $request->codigoFornecedor,
            $request->niFornecedor
        );
        $contrato['niFornecedor'] = $request->niFornecedor;

        if ($contrato['tipoPessoaFornecedor'] === 'PE') {
            $contrato['niFornecedor'] = $this->buscaNiFornecedorEstrangeiro($request->codigoFornecedor);
        }

        $contrato['receita'] = $request->receita == '1';
        $contrato['codigoUnidade'] = intval($request->codigoUnidade);
        $contrato['objetoContrato'] = $this->trataString($request->objetoContrato);
        $contrato['valorInicial'] = floatval($request->valorInicial);
        $contrato['valorGlobal'] = floatval($request->valorGlobal);
        $contrato['valorParcela'] = floatval($request->valorParcela);
        $contrato['numeroParcelas'] = intval($request->numeroParcelas);

        $dataAssinatura = new \DateTime($request->dataAssinatura);
        $dataVigenciaInicio = new \DateTime($request->dataVigenciaInicio);
        $dataVigenciaFim = new \DateTime($request->dataVigenciaFim);

        $contrato['dataAssinatura'] = $dataAssinatura->format('Y-m-d');
        $contrato['dataVigenciaInicio'] = $dataVigenciaInicio->format('Y-m-d');
        $contrato['dataVigenciaFim'] = $dataVigenciaFim->format('Y-m-d');

        $niFornecedorSubContratado = $request->niFornecedorSubContratado;
        $razaoSocialSubContratado = $this->trataString($request->nomeRazaoSocialFornecedorSubContratado);

        if (!empty($niFornecedorSubContratado)) {
            $contrato['nomeRazaoSocialFornecedorSubContratado'] = $this->trataString($razaoSocialSubContratado);
            $contrato['tipoPessoaFornecedorSubContratado'] = $this->retornaTipoPessoa(
                $request->codigoSubContratado,
                $niFornecedorSubContratado
            );
            $contrato['niFornecedorSubContratado'] = $niFornecedorSubContratado;

            if ($contrato['tipoPessoaFornecedorSubContratado'] === 'PE') {
                $contrato['niFornecedorSubContratado'] = $this->buscaNiFornecedorEstrangeiro(
                    $request->codigoFornecedor
                );
            }
        }

        if (!empty($request->urlCipi)) {
            $contrato['urlCipi'] = $this->trataString($request->urlCipi);
            $contrato['identificadorCipi'] = $this->trataString($request->identificadorCipi);
        }

        if (!empty($request->informacaoComplementar)) {
            $contrato['informacaoComplementar'] = $this->trataString($request->informacaoComplementar);
        }

        return $contrato;
    }

    /**
     * @param string $string
     * @return string
     */
    private function trataString($string)
    {
        return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
    }

    /**
     * @param string $niFornecedor
     * @return string
     * @throws Exception
     */
    private function retornaTipoPessoa($numcgm, $niFornecedor)
    {
        $isCgmEstrangeiro = $this->verificaCgmEstrangeiro($numcgm);

        if ($isCgmEstrangeiro) {
            return 'PE';
        }

        $tipoPessoa = \LicitanteLicitaCon::getTipoPessoaPorCGM($numcgm);

        if ($tipoPessoa === 'J') {
            return 'PJ';
        }

        if ($tipoPessoa === 'F') {
            return 'PF';
        }

        throw new Exception(
            'Não foi possível identificar o tipo de pessoa, verifique o documento dos participantes do contrato.'
        );
    }

    /**
     * @param string $numcgm
     * @return bool
     * @throws Exception
     */
    private function verificaCgmEstrangeiro($numcgm)
    {
        if (empty($numcgm)) {
            throw new Exception("Não foi possível identificar o documento do fornecedor.");
        }

        $cgm = new Cgm();
        $isCgmEstrangeiro = $cgm->where(
            [
                ['z01_numcgm', '=', $numcgm],
                ['z01_nacion', '=', 2]
            ]
        )->count();

        return (bool)$isCgmEstrangeiro;
    }

    /**
     * @param $numcgm
     * @return string
     * @throws Exception
     */
    private function buscaNiFornecedorEstrangeiro($numcgm)
    {
        $cgm = new Cgm();
        $dadosCgm = $cgm->with('cgmEstrangeiro')->find($numcgm);
        $dadosCgmEstrangeiro = $dadosCgm->cgmEstrangeiro;

        if (empty($dadosCgmEstrangeiro->z09_documento)) {
            throw new Exception('Este CGM não possui documento estrangeiro informado, verifique seu cadastro.');
        }

        return $dadosCgmEstrangeiro->z09_documento;
    }

    /**
     * @param object $dados
     * @return void
     * @throws Exception
     */
    private function incluirContrato($dados)
    {
        $contrato = new ContratoPNCP();
        $contrato->pn04_acordo = $dados->acordo;
        $contrato->pn04_numero = $dados->sequencialContrato;
        $contrato->pn04_ano = $dados->anoContrato;
        $contrato->pn04_unidade = $dados->unidadeOrgao->codigoUnidade;
        $contrato->pn04_datapublicacao = date('Y-m-d');
        $contrato->pn04_usuario = db_getsession('DB_id_usuario');
        $contrato->pn04_instit = db_getsession('DB_instit');
        $contrato->save();
    }

    /**
     * @param array $dados
     * @return void
     * @throws Exception
     */
    private function incluirEvento($dados = [])
    {
        $acordo = new \Acordo($dados['acordo']);

        $evento = new \AcordoEvento();
        $evento->setAcordo($acordo);
        $evento->setTipoEvento($evento::TIPO_EVENTO_PUBLICACAO);
        $evento->setVeiculoComunicacao($evento::PUBLICACAO_CONTRATACOES_PUBLICAS);
        $evento->setDescricaoVeiculo(
            \db_stdClass::normalizeStringJsonEscapeString($dados['link'])
        );
        $evento->setData(\DBDate::create(date('Y-m-d')));
        $evento->salvar();
    }

    /**
     * @param array $dados
     * @return void
     * @throws Exception
     */
    private function excluirEncerramentoContrato($dados)
    {
        $acordoEncerramentoLicitacon = new AcordoEncerramentoLicitacon();
        $acordoEncerramentoLicitacon->where(['ac58_acordo' => $dados['acordo']])->delete();
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function enviarDocumento(InclusaoDocumentoContratoRequest $request)
    {
        $contrato = [];
        $contrato['cnpj'] = $request->cnpj;
        $contrato['ano'] = $request->ano;
        $contrato['sequencial'] = $request->sequencial;

        $header = [
            'Titulo-Documento' => stripslashes($request->tituloDocumento),
            'Tipo-Documento-Id' => $request->tipoDocumentoId
        ];

        $multipart[0]['name'] = 'contrato';
        $multipart[0]['contents'] = json_encode($contrato);
        $multipart[0]['headers'] = ['Content-Type' => 'application/json'];

        $multipart[1]['name'] = 'arquivo';
        $multipart[1]['contents'] = fopen($request->documento->getPathName(), 'r');
        $multipart[1]['filename'] = urlencode(stripslashes($request->documento->getClientOriginalName()));
        $multipart[1]['headers'] = ['Content-Type' => 'multipart/form-data'];

        $dados = [];
        $dados['headers'] = $header;
        $dados['multipart'] = $multipart;

        $linkContrato = $this->http->incluirContratoDocumento($contrato, $dados);
        return [$linkContrato];
    }

    public function buscarContratos(Request $request)
    {
        $condicoes = $this->montaCondicoesContrato($request);
        $contrato = new ContratoPNCP();

        return $contrato
            ->where($condicoes)
            ->with('unidadeCompradora')
            ->with('acordo')
            ->get();
    }

    private function montaCondicoesContrato(Request $request)
    {
        $condicoes = [];

        if (!empty($request->pn04_numero)) {
            $condicoes['pn04_numero'] = $request->pn04_numero;
        }

        if (!empty($request->pn04_ano)) {
            $condicoes['pn04_ano'] = $request->pn04_ano;
        }

        if (!empty($request->pn04_codigo)) {
            $condicoes['pn04_codigo'] = $request->pn04_codigo;
        }

        return $condicoes;
    }

    public function excluirDocumento(Request $request)
    {
        $this->http->excluirDocumentoContrato($request);
    }

    public function buscarDocumentosContrato($request)
    {
        return (array)$this->http->buscarDocumentosContrato($request);
    }

    /**
     * @param ExclusaoContratoRequest $request
     * @return void
     * @throws Exception
     */
    public function excluirContrato(ExclusaoContratoRequest $request)
    {
        $contrato = new ContratoPNCP();
        $dadosContrato = $contrato
            ->where(['pn04_codigo' => $request->pn04_codigo])
            ->firstOrFail();

        $dadosContrato->cnpj = $request->cnpj;
        $this->http->excluirContrato($dadosContrato);

        $acordoEvento = new \AcordoEvento();
        $veiculoComunicacao = $acordoEvento::PUBLICACAO_CONTRATACOES_PUBLICAS;
        $tipoEvento = $acordoEvento::TIPO_EVENTO_PUBLICACAO;
        $acordo = $dadosContrato->pn04_acordo;

        $evento = new AcordoEvento();
        $eventoPublicacao = $evento
            ->where([
                ['ac55_acordo', '=', $acordo],
                ['ac55_tipoevento', '=', $tipoEvento],
                ['ac55_veiculocomunicacao', '=', $veiculoComunicacao]
            ])->first();

        if (!empty($eventoPublicacao)) {
            $acordoDocumentoEvento = new AcordoDocumentoEvento();
            $relacaoDocumentoEvento = $acordoDocumentoEvento
                ->where(['ac57_acordoevento' => $eventoPublicacao->ac55_sequencial])
                ->get();

            if (!empty($relacaoDocumentoEvento)) {
                foreach ($relacaoDocumentoEvento as $documentoEvento) {
                    $acordoDocumento = new AcordoDocumento();
                    $documento = $acordoDocumento->where(
                        ['ac40_sequencial' => $documentoEvento->ac57_acordodocumento]
                    );

                    $documentoEvento->delete();
                    $documento->delete();
                }
            }

            $eventoPublicacao->delete();
        }

        $dadosContrato->delete();
    }
}
