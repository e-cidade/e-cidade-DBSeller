<?php

namespace App\Domain\Patrimonial\PNCP\Services;

use App\Domain\Patrimonial\Licitacoes\Models\EventoLicitacao;
use App\Domain\Patrimonial\Licitacoes\Models\Licitacao;
use App\Domain\Patrimonial\Compras\Models\Solicitacao;
use App\Domain\Patrimonial\Licitacoes\Models\LiclicitaEncerramentoLicitacon;
use App\Domain\Patrimonial\PNCP\Clients\PNCPClient;
use App\Domain\Patrimonial\PNCP\Enum\ModalidadeCompraEnum;
use App\Domain\Patrimonial\PNCP\Exceptions\CompraEditalAvisoExcpetion;
use App\Domain\Patrimonial\PNCP\Models\ComprasPncp;
use App\Domain\Patrimonial\PNCP\Requests\ImportacaoCEARequest;
use App\Domain\Patrimonial\PNCP\Requests\InclusaoCEARequest;
use App\Domain\Patrimonial\PNCP\Resources\EditaisResource;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Patrimonial\Protocolo\Repository\CgmRepository;
use cl_pcorcamfornelic;
use cl_pcorcamitemlic;
use db_stdClass;
use db_utils;
use DBException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InstituicaoRepository;
use LicitanteLicitaCon;
use ParameterException;

class CompraEditalAvisoService
{
    private $http;
    const HOMOLOGADO = 2;
    const DESERTO = 4;
    const FRACASSADO = 5;

    public function __construct()
    {
        $this->http = new PNCPClient();
    }


    /**
     * @param Request $request
     * @return void
     */
    public function inserirDocumento(Request $request)
    {
        $cnpj = InstituicaoRepository::getInstituicaoByCodigo($request->DB_instit)->getCNPJ();

        $dadosDocumento = [];
        $dadosDocumento['cnpj'] = $cnpj;
        $dadosDocumento['ano'] = $request->ano;
        $dadosDocumento['sequencial'] = $request->sequencial;

        $header = [
            'Titulo-Documento' => stripslashes($request->tituloDocumento),
            'Tipo-Documento-Id' => $request->tipoDocumento
        ];

        $multipart[0]['name'] = 'documento';
        $multipart[0]['contents'] = json_encode($dadosDocumento);
        $multipart[0]['headers'] = ['Content-Type' => 'application/json'];

        $multipart[1]['name'] = 'arquivo';
        $multipart[1]['contents'] = fopen($request->documento->getPathName(), 'r');
        $multipart[1]['filename'] = urlencode(stripslashes($request->documento->getClientOriginalName()));
        $multipart[1]['headers'] = ['Content-Type' => 'multipart/form-data'];

        $dados = [];
        $dados['headers'] = $header;
        $dados['multipart'] = $multipart;

        $this->http->incluirDocumentoLicitacao($dadosDocumento, $dados);
    }

    /**
     * @param $licitacao
     * @param $item
     * @return array
     * @throws Exception
     */
    public static function getFornecedorJulgado($licitacao, $item)
    {
        $itemLicitacao = DB::table('liclicitem')
            ->join('pcorcamitemlic', 'pc26_liclicitem', 'l21_codigo')
            ->where('l21_codliclicita', $licitacao)
            ->where('l21_codigo', $item)
            ->select('pc26_orcamitem', 'l21_codigo')
            ->get();

        if ($itemLicitacao->count() === 0) {
            return [];
        }

        $fornecedor = DB::table('pcorcamjulg')
            ->join('pcorcamforne', 'pc21_orcamforne', 'pc24_orcamforne')
            ->join('pcorcamval', 'pcorcamval.pc23_orcamforne', 'pcorcamforne.pc21_orcamforne')
            ->where('pc24_orcamitem', $itemLicitacao[0]->pc26_orcamitem)
            ->where('pc23_orcamitem', $itemLicitacao[0]->pc26_orcamitem)
            ->select(
                'pc21_numcgm',
                'pc24_orcamitem',
                'pc21_orcamforne',
                'pc23_valor',
                'pc23_vlrun',
                'pc23_quant',
                'pc23_percentualdesconto'
            )
            ->first();
        if (is_null($fornecedor)) {
            return [];
        }
        $porteFornecedorId = DB::table('pcorcamfornelic')
            ->where('pc31_orcamforne', $fornecedor->pc21_orcamforne)
            ->select('pc31_liclicitatipoempresa')->first();

        $cgmRepository = new CgmRepository();
        $cgm = $cgmRepository->getByNumcgm($fornecedor->pc21_numcgm);
        $tipoPessoaId = "PE";
        if ($cgm->z01_nacion !== 2) {
            if (LicitanteLicitaCon::getTipoPessoaPorCGM($fornecedor->pc21_numcgm) === "F") {
                $tipoPessoaId = "PF";
            } else {
                $tipoPessoaId = "PJ";
            }
        }

        $dados = [
            "quantidadeHomologada" => $fornecedor->pc23_quant,
            'valorUnitarioHomologado' => $fornecedor->pc23_vlrun,
            'valorTotalHomologado' => $fornecedor->pc23_valor,
            'tipoPessoaId' => $tipoPessoaId,
            'niFornecedor' => $cgm->z01_cgccpf,
            'nomeRazaoSocialFornecedor' => $cgm->z01_nome,
            'percentualDesconto' => $fornecedor->pc23_percentualdesconto,
            'porteFornecedorId' => self::getFornecedorIdPNCP($porteFornecedorId->pc31_liclicitatipoempresa),
            'numcgm' => $cgm->z01_numcgm
        ];

        if ($dados['tipoPessoaId'] === 'PE') {
            $cgmFornecedor = $fornecedor->pc21_numcgm;
            $dados['niFornecedor'] = self::buscaNiFornecedorEstrangeiro($cgmFornecedor);
        }

        return $dados;
    }

    /**
     * @param $fornecedor
     * @return int
     */
    private static function getFornecedorIdPNCP($fornecedor)
    {
        switch ($fornecedor) {
            case 1:
                $porteFornecedor = 3;
                break;
            case 2:
                $porteFornecedor = 1;
                break;
            default:
                $porteFornecedor = 2;
                break;
        }

        return $porteFornecedor;
    }

    /**
     * @param $numcgm
     * @return mixed|string
     */
    private static function buscaNiFornecedorEstrangeiro($numcgm)
    {
        $cgm = new Cgm();
        $dadosCgm = $cgm->with('cgmEstrangeiro')->find($numcgm);
        $dadosCgmEstrangeiro = $dadosCgm->cgmEstrangeiro;
        if (!empty($dadosCgmEstrangeiro)) {
            return $dadosCgmEstrangeiro->z09_documento;
        }

        return 'Não informado';
    }


    /**
     * @param InclusaoCEARequest $request
     * @return string
     * @throws DBException
     * @throws ParameterException
     */
    public function incluirCompra(InclusaoCEARequest $request)
    {
        $this->verificaItens($request->itensCompra, $request);
        $compra = $this->montarDadosCompra($request);

        if (!isset($request->informacaoComplementar)) {
            unset($compra->informacaoComplementar);
        }

        $header = [
            'Titulo-Documento' => stripslashes($request->tituloDocumento),
            'Tipo-Documento-Id' => $request->tipoDocumento,
        ];

        $multipart = [
            [
                'name' => 'compra',
                'contents' => json_encode($compra),
                'headers' => ['Content-Type' => 'application/json']
            ],
            [
                'name' => 'documento',
                'contents' => fopen($request->anexoDocumento->getPathName(), 'r'),
                'filename' => $request->anexoDocumento->getClientOriginalName(),
                'headers' => [
                    'Content-Type' => 'multipart/form-data'
                ]
            ],
        ];

        try {
            $response = $this->http->incluirCompra($request->cnpj, null, $header, $multipart);
            $compra = (explode('/', explode("compras/", $response->compraUri)[1]));
            $linkCompra = "https://pncp.gov.br/app/editais/{$request->cnpj}/{$compra[0]}/$compra[1]";
            $dadosCompra = $this->http->doRequest('GET', $response->compraUri);

            try {
                $this->incluirDadosCompra($dadosCompra, $request);
            } catch (Exception $e) {
                throw new \Exception($e->getMessage());
            }

            if (empty($request->solicitacao)) {
                $this->incluirEvento($request->modalidade, $request, $linkCompra);
            }

            if (empty($request->solicitacao)) {
                $this->excluiEncerramentoLicitacon($request->licitacao);
            }
        } catch (CompraEditalAvisoExcpetion $e) {
            throw new Exception($e->getErros());
        }

        return $linkCompra;
    }

    public function alterarCompra(Request $request)
    {
        $compra = $this->montarDadosCompra($request);

        try {
            $this->http->alterarCompra($request->cnpj, $compra);
        } catch (CompraEditalAvisoExcpetion $e) {
            throw new Exception($e->getErros());
        }
    }

    public function montarDadosCompra($request)
    {
        if (!empty($request->sequencialCompra)) {
            $compra['sequencialCompra'] = intval($request->sequencialCompra);
        }

        if (!empty($request->dataAberturaProposta) && !empty($request->horaAberturaProposta)) {
            $dataAberturaProposta = new \DateTime("$request->dataAberturaProposta $request->horaAberturaProposta");
            $dataAberturaProposta = $dataAberturaProposta->format('Y-m-d\TH:i:s');
            $compra['dataAberturaProposta'] = $dataAberturaProposta;
        }

        if (!empty($request->dataEncerramentoProposta) && !empty($request->horaEncerramentoProposta)) {
            $dataEncerramentoProposta = new \DateTime(
                "$request->dataEncerramentoProposta $request->horaEncerramentoProposta"
            );
            $dataEncerramentoProposta = $dataEncerramentoProposta->format('Y-m-d\TH:i:s');
            $compra['dataEncerramentoProposta'] = $dataEncerramentoProposta;
        }

        if (!empty($request->anoCompra)) {
            $compra['anoCompra'] = intval($request->anoCompra);
        }

        if (!empty($request->numeroCompra)) {
            $compra['numeroCompra'] = $request->numeroCompra;
        }

        if (!empty($request->itensCompra)) {
            $compra['itensCompra'] = json_decode(stripslashes(utf8_encode($request->itensCompra)));
        }

        if (!empty($request->instrumentoConvocatorio)) {
            $compra['tipoInstrumentoConvocatorioId'] = $request->instrumentoConvocatorio;
        }

        if (!empty($request->modalidade)) {
            $compra['modalidadeId'] = $request->modalidade;
        }

        if (!empty($request->situacaoLicitacao)) {
            $compra['situacaoCompraId'] = $request->situacaoLicitacao;
        }

        if (!empty($request->numeroProcesso)) {
            $compra['numeroProcesso'] = $request->numeroProcesso;
        }

        if (!empty($request->objetoCompra)) {
            $compra['objetoCompra'] = stripslashes(utf8_encode($request->objetoCompra));
        }

        if (!empty($request->informacaoComplementar)) {
            $compra['informacaoComplementar'] = stripslashes(utf8_encode($request->informacaoComplementar));
        }

        if (!empty($request->amparoLegal)) {
            $compra['amparoLegalId'] = $request->amparoLegal;
        }

        if (!empty($request->srp)) {
            $compra['srp'] = $request->srp;
        }

        if (!empty($request->unidadeCompradora)) {
            $compra['codigoUnidadeCompradora'] = $request->unidadeCompradora;
        }

        if (!empty($request->justificativaPresencial)) {
            $compra['justificativaPresencial'] = $request->justificativaPresencial;
        }

        if (!empty($request->linkSistemaOrigem)) {
            $compra['linkSistemaOrigem'] = $request->linkSistemaOrigem;
        }

        if (!empty($request->modoDisputa)) {
            $compra['modoDisputaId'] = $request->modoDisputa;
        }

        return (object) $compra;
    }

    /**
     * @param ImportacaoCEARequest $request
     * @throws Exception
     * @return void
     */
    public function importarCompra(ImportacaoCEARequest $request)
    {
        $compra = $this->buscarCompra($request);
        if (empty($compra)) {
            throw new Exception('Compra não encontrada no PNCP.');
        }

        $this->validarDadosImportacao($compra, $request);
        $this->incluirDadosCompra($compra, $request);
    }

    /**
     * @param ComprasPncp $compra
     * @param ImportacaoCEARequest $request
     * @return void
     */
    private function validarDadosImportacao($compra, $request)
    {
        $compraExiste = ComprasPncp::where([
            'pn03_ano' => $request->ano,
            'pn03_numero' => $request->numero,
            'pn03_cnpj' => $compra->orgaoEntidade->cnpj,
        ])->exists();

        if ($compraExiste) {
            throw new Exception('Compra já importada!');
        }

        if (!empty($request->licitacao)) {
            $licitacaoExiste = Licitacao::where('l20_codigo', $request->licitacao)->exists();

            if ($licitacaoExiste === false) {
                throw new Exception('Licitação não encontrada!');
            }
        }

        if (!empty($request->solicitacao)) {
            $solicitacaoExiste = Solicitacao::where('pc10_numero', $request->solicitacao)->exists();

            if ($solicitacaoExiste === false) {
                throw new Exception('Solicitação não encontrada!');
            }
        }
    }

    /**
     * @param $request
     * @return object
     */
    public function buscarDocumentos($request)
    {
        $dados = [];
        $cnpj = InstituicaoRepository::getInstituicaoByCodigo($request->DB_instit)->getCNPJ();
        $dados['cnpj'] = $cnpj;
        $dados['sequencial'] = $request->sequencial;
        $dados['ano'] = $request->ano;
        return (array) $this->http->buscarDocumentos($dados);
    }

    /**
     * @param $request
     * @return object
     */
    public function excluirDocumentoCompra($request)
    {
        return $this->http->excluirDocumentoContratacao($request);
    }
    private function verificaItens($itens, $request)
    {
        $itens = (json_decode(stripslashes(utf8_encode($itens))));

        if (!empty($request->solicitacao)) {
            if (!empty($itens[0]) && empty($itens[0]->dadosFornecedor)) {
                throw new Exception('Itens não possuem julgamento.');
            }
        }

        foreach ($itens as $item) {
            if (!empty($item->aplicabilidadeMargemPreferenciaNormal)) {
                if (empty($item->percentualMargemPreferenciaNormal)) {
                    throw new Exception(
                        'Percentual de Margem Normal para o item '
                        . $item->numeroItem .
                        ' não informada.'
                    );
                }

                $percentualNormal = (float) $item->percentualMargemPreferenciaNormal;
                if (empty($percentualNormal)) {
                    throw new Exception('Margem Normal informada para o item ' . $item->numeroItem . ' é inválida.');
                }
            }

            if (!empty($item->aplicabilidadeMargemPreferenciaAdicional)) {
                if (empty($item->percentualMargemPreferenciaAdicional)) {
                    throw new Exception(
                        'Percentual de Margem Adicional para o item '
                        . $item->numeroItem .
                        ' não informada.'
                    );
                }

                $percentualAdicional = (float) $item->percentualMargemPreferenciaNormal;
                if (empty($percentualAdicional)) {
                    throw new Exception('Margem Adicional informada para o item ' . $item->numeroItem . ' é inválida.');
                }
            }

            if (empty($item->tipoBeneficioId)) {
                throw new Exception('Selecione o "Tipo Benefício" para o Item ' . $item->numeroItem . '.');
            }

            if (empty($item->incentivoProdutivoBasico)) {
                throw new Exception('Selecione o "Incentivo Fiscal PPB" para o Item ' . $item->numeroItem . '.');
            }

            if (empty($item->criterioJulgamentoId)) {
                throw new Exception('Selecione o "Critério Julgamento" para o Item ' . $item->numeroItem . '.');
            }

            if (!empty($item->dadosFornecedor)) {
                if ($item->indicadorSubcontratacao === '0') {
                    $mensagem = 'Selecione o "Indicador sub-contratação" para o Item ' . $item->numeroItem . '.';
                    throw new Exception($mensagem);
                }
            }

            if (empty($item->itemCategoriaId)) {
                throw new Exception('Selecione a "Categoria Item" para o item ' . $item->numeroItem . '.');
            }

            if (!isset($item->orcamentoSigiloso) || $item->orcamentoSigiloso === "0") {
                throw new Exception('Selecione o "Orcamento Sigiloso" para o Item ' . $item->numeroItem . '.');
            }

            if (isset($item->codigoRegistroImobiliario) && $item->codigoRegistroImobiliario === "") {
                throw new Exception(
                    'Informe o "Código de registro imobiliário" para o Item ' . $item->numeroItem . '.'
                );
            }
        }
    }

    /**
     * @param $modalidadeCompra
     * @param InclusaoCEARequest $request
     * @param $linkCompra
     * @return void
     * @throws DBException
     * @throws ParameterException
     */
    private function incluirEvento($modalidadeCompra, InclusaoCEARequest $request, $linkCompra)
    {
        $fase = \EventoLicitacao::FASE_EDITAL_PUBLICADO;
        $tipoEvento = \EventoLicitacao::TIPO_EVENTO_PUBLICACAO_EDITAL;
        $tipoPublicacao = \EventoLicitacao::TIPO_PUBLICACAO_PUBLICACAO_CONTRATACOES_PUBLICAS;
        $data = date('Y-m-d');
        $dataEvento = new \DBDate(urldecode($data));
        if ((int) $modalidadeCompra === ModalidadeCompraEnum::DISPENSA_DE_LICITACAO ||
            (int) $modalidadeCompra === ModalidadeCompraEnum::INEXIGIBILIDADE
        ) {
            $fase = \EventoLicitacao::FASE_PUBLICACAO;
            $tipoEvento = \EventoLicitacao::TIPO_EVENTO_PUBLICACAO;
        }

        $evento = new EventoLicitacao();
        $evento->l46_liclicita = $request->licitacao;
        $evento->l46_fase = $fase;
        $evento->l46_liclicitatipoevento = $tipoEvento;
        $evento->l46_dataevento = $dataEvento;
        $evento->l46_tipopublicacao = $tipoPublicacao;
        $evento->l46_descricaopublicacao = db_stdClass::normalizeStringJsonEscapeString($linkCompra);
        $evento->save();
    }

    /**
     * @param $licitacao
     * @return void
     * @throws Exception
     */
    private function excluiEncerramentoLicitacon($licitacao)
    {
        $liclicita = LiclicitaEncerramentoLicitacon::where('l18_liclicita', $licitacao)->first();
        if ($liclicita !== null) {
            $sql = "delete from liclicitaencerramentolicitacon where l18_liclicita = {$licitacao}";
            $rs = db_query($sql);
            if (!$rs) {
                throw new Exception("Erro ao excluir encerramento de licitacao no licitacon.");
            }
        }
    }


    /**
     * @param $dadosCompra
     * @return void
     * @throws Exception
     */
    private function incluirDadosCompra($dadosCompra, $request)
    {
        try {
            $publicacao = new ComprasPncp;
            $publicacao->pn03_liclicita = (int)$request->licitacao ?: null;
            $publicacao->pn03_solicita = (int)$request->solicitacao ?: null;
            $publicacao->pn03_numero = (int)$dadosCompra->sequencialCompra;
            $publicacao->pn03_unidade = $dadosCompra->unidadeOrgao->codigoUnidade;
            $publicacao->pn03_ano = $dadosCompra->anoCompra;
            $publicacao->pn03_instituicao = (int)$request->DB_instit;
            $publicacao->pn03_usuario = (int)$request->DB_id_usuario;
            $publicacao->pn03_datapublicacao = date('Y-m-d');
            $publicacao->pn03_cnpj = $dadosCompra->orgaoEntidade->cnpj;
            $publicacao->save();
        } catch (Exception $e) {
            throw new Exception('Erro ao salvar compra.');
        }
    }

    /**
     * @param $licitacao
     * @return array
     */
    public function buscarEditais($licitacao)
    {
        $editais = [];
        $licitacao = Licitacao::where('l20_codigo', $licitacao)->first();
        if (!is_null($licitacao->editais)) {
            $editais = $licitacao->editais->toArray();
            return EditaisResource::toArray($editais);
        }
        return $editais;
    }

    /**
     * @param $licitacao
     * @param $cnpj
     * @param $itens
     * @return bool|void
     * @throws Exception
     */
    public function incluirResultadoItem(Request $request, $cnpj, $itens)
    {
        $licitacao = $request->licitacao;
        $colunaCompra = 'pn03_liclicita';
        if (!empty($request->solicitacao)) {
            $licitacao = $request->solicitacao;
            $colunaCompra = 'pn03_solicita';
        }

        $itensCompra = (json_decode(stripslashes(utf8_encode($itens))));
        $publicacao = ComprasPncp::where($colunaCompra, $licitacao)->first();
        if (is_null($publicacao)) {
            return false;
        }

        $compra = (object) ComprasPncp::where($colunaCompra, $licitacao)->first()->toArray();
        foreach ($itensCompra as $item) {
            if ($item->situacao === "Homologada") {
                if ($item->indicadorSubcontratacao === '0') {
                    $mensagem = 'Selecione o "Indicador sub-contratação" para o Item ' . $item->numeroItem . '.';
                    throw new Exception($mensagem);
                }
            }
            $situacaoItem = ["situacaoCompraItemId" => self::DESERTO];
            if (!empty($item->dadosFornecedor)) {
                $situacaoFornecedor = $this->getFornecedor($licitacao, $item->dadosFornecedor->numcgm)->situacao;
                $situacaoItem = ["situacaoCompraItemId" => self::HOMOLOGADO];
                if ($situacaoFornecedor === '2') {
                    $situacaoItem = ["situacaoCompraItemId" => self::FRACASSADO];
                }
            }

            $resultadoItem = $this->http->buscarResultadoItem(
                $cnpj,
                $publicacao->pn03_ano,
                $publicacao->pn03_numero,
                $item->numeroItem
            );

            if (!empty((array) $resultadoItem)) {
                continue;
            }

            try {
                $this->http->retificarSituacaoItem(
                    $cnpj,
                    (int)$compra->pn03_ano,
                    (int)$compra->pn03_numero,
                    (int)$item->numeroItem,
                    $situacaoItem
                );
            } catch (CompraEditalAvisoExcpetion $e) {
                throw new Exception($e->getErros());
            }

            if ($item->situacao !== "Fracassada") {
                if ($item->situacao !== "Homologada" || $item->dataHomologacao === '') {
                    throw new Exception("Licitação não está Homologada!");
                }
            }


            if (!isset($item->dadosFornecedor->niFornecedor)) {
                continue;
            }

            $data = explode("/", $item->dataHomologacao);
            $dataHomologacao = $data[2] . '-' . $data[1] . '-' . $data[0];

            $usaMargemPreferencia = false;
            if (!empty($item->aplicacaoMargemPreferenciaNormal) || !empty($item->aplicacaoMargemPreferenciaAdicional)) {
                $usaMargemPreferencia = true;
            }

            $dados = [
                "aplicacaoMargemPreferencia" => $usaMargemPreferencia,
                "aplicacaoBeneficioMeEpp" => false,
                "aplicacaoCriterioDesempate" => false,
                "quantidadeHomologada" => $item->dadosFornecedor->quantidadeHomologada,
                "valorUnitarioHomologado" => $item->dadosFornecedor->valorUnitarioHomologado,
                "valorTotalHomologado" => $item->dadosFornecedor->valorTotalHomologado,
                "percentualDesconto" => $item->dadosFornecedor->percentualDesconto,
                "porteFornecedorId" => $item->dadosFornecedor->porteFornecedorId,
                "tipoPessoaId" => $item->dadosFornecedor->tipoPessoaId,
                "niFornecedor" => $item->dadosFornecedor->niFornecedor,
                "nomeRazaoSocialFornecedor" => $item->dadosFornecedor->nomeRazaoSocialFornecedor,
                "indicadorSubcontratacao" => $item->indicadorSubcontratacao,
                "naturezaJuridicaId" => '0000',
                "codigoPais" => "BRA",
                "ordemClassificacaoSrp" => 1,
                "dataResultado" => $dataHomologacao,
            ];

            try {
                $this->http->incluirResultadoItem(
                    $cnpj,
                    $compra->pn03_ano,
                    $compra->pn03_numero,
                    $item->numeroItem,
                    $dados
                );
            } catch (CompraEditalAvisoExcpetion $e) {
                throw new Exception($e->getErros());
            }
        }
    }

    /**
     * @param $iCodigoLicitacao
     * @param $numCgm
     * @return \_db_fields|\stdClass
     */
    private function getFornecedor($iCodigoLicitacao, $numCgm)
    {
        $sCampos = " pc20_codorc ";
        $sWhere  = " l20_codigo = {$iCodigoLicitacao} ";

        $oDaoOrcamItemLic = new cl_pcorcamitemlic();
        $sSqlOrcLicitacao = $oDaoOrcamItemLic->sql_query(null, $sCampos, null, $sWhere);

        $sCampos = " pc31_orcamforne as codigo, z01_nome as nome, l17_situacao as situacao ";
        $sWhere  = " pc21_codorc in ($sSqlOrcLicitacao) and z01_numcgm = $numCgm";

        $oDaoFornecedoresLicitacao = new cl_pcorcamfornelic();
        $sSqlFornecedoresLicitacao = $oDaoFornecedoresLicitacao->sql_query(null, $sCampos, null, $sWhere);
        $rsFornecedoresLicitacao   = db_query($sSqlFornecedoresLicitacao);
        return db_utils::fieldsMemory($rsFornecedoresLicitacao, 0);
    }

    /**
     * @param $cnpj
     * @param $codigoCompra
     * @return string
     * @throws DBException
     * @throws ParameterException
     * @throws Exception
     */
    public function excluirCompra($cnpj, $codigoCompra)
    {
        $comprasPncp = new ComprasPncp();
        $compra = $comprasPncp->where('pn03_codigo', $codigoCompra)->first();

        try {
            if (empty($compra)) {
                throw new Exception('Compra não encontrada');
            }

            $this->http->excluirCompra($cnpj, $compra->pn03_ano, $compra->pn03_numero);
            if ($compra->pn03_liclicita) {
                $this->excluirEventoLicitacao($compra->pn03_liclicita);
            }

            $publicacao = $comprasPncp->find($compra->pn03_codigo);
            $publicacao->delete();
        } catch (CompraEditalAvisoExcpetion $e) {
            throw new Exception($e->getErros());
        }

        return 'Contratação excluída com sucesso.';
    }

    /**
     * @throws DBException
     * @throws ParameterException
     */
    private function excluirEventoLicitacao($codigoLicitacao)
    {
        $eventoPublicacao = \EventoLicitacao::TIPO_EVENTO_PUBLICACAO;
        $eventoPublicacaoEdital = \EventoLicitacao::TIPO_EVENTO_PUBLICACAO_EDITAL;
        $eventoLicitacao = new EventoLicitacao();

        $dadosEvento = $eventoLicitacao
            ->where('l46_liclicita', $codigoLicitacao)
            ->where(function ($query) use ($eventoPublicacao, $eventoPublicacaoEdital) {
                $query->where('l46_liclicitatipoevento', $eventoPublicacao)
                    ->orWhere('l46_liclicitatipoevento', $eventoPublicacaoEdital);
            })
            ->first();

        $model = new \EventoLicitacao($dadosEvento->l46_sequencial);
        $model->excluir();
    }

    /**
     * @param Request $request
     * @return object
     * @throws Exception
     */
    public function buscarCompra(Request $request)
    {
        $client = new PNCPClient(true);
        return $client->buscarCompra($request);
    }
}
