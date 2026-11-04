<?php

namespace App\Domain\Patrimonial\Ouvidoria\Services;

use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Patrimonial\Ouvidoria\Model\TipoProcessoFormaReclamacao;
use App\Domain\Patrimonial\Ouvidoria\Repository\Atendimento\AtendimentoRepository;
use App\Domain\Patrimonial\Ouvidoria\Repository\Cidadao\CidadaoRepository;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoAndamento;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoDocumento;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoOuvidoria;
use App\Domain\Patrimonial\Protocolo\Model\Processo\TipoProcesso;
use App\Domain\Patrimonial\Protocolo\Services\ProcessoService;
use ECidade\Lib\Session\DefaultSession;
use ECidade\Patrimonial\Protocolo\Processo\ProcessoEletronico\Filter\ListagemProcessos as FiltroListagemProcessos;
use ECidade\Patrimonial\Protocolo\Processo\ProcessoEletronico\Helper\ProcessoEletronicoHelper;
use Illuminate\Support\Facades\DB;

/**
 * Classe AtendimentoProcessoService
 * Gerencia as interações de um atendimento com um processo
 */
class AtendimentoProcessoService
{
    /**
     * Atributo que guarda o Processo gerado
     *
     * @property Processo $processo
     */
    private $processo;

    /**
     * Atributo que guarda o ProcessoAndamento gerado
     *
     * @property ProcessoAndamento $processoAndamento
     */
    private $processoAndamento;
    /**
     * @var AtendimentoRepository
     */
    private $atendimentoRepository;
    /**
     * @var CidadaoRepository
     */
    private $cidadaoRepository;
    /**
     * @var ProcessoService
     */
    private $processoService;

    /**
     * Construtor da classe
     *
     * @param AtendimentoRepository $atendimentoRepository
     * @param CidadaoRepository $cidadaoRepository
     * @param ProcessoService $processoService
     */
    public function __construct(
        AtendimentoRepository $atendimentoRepository,
        CidadaoRepository $cidadaoRepository,
        ProcessoService $processoService
    ) {
        $this->atendimentoRepository = $atendimentoRepository;
        $this->cidadaoRepository = $cidadaoRepository;
        $this->processoService = $processoService;
    }

    /**
     * Função que realiza a ação de aprovar um processo
     *
     * @param \Cgmbase $cgm
     * @param \stdClass $solicitacaoOuvidoria
     * @throws \Exception
     */
    public function aprovarProcesso(
        \CgmBase $cgm,
        $solicitacaoOuvidoria,
        \CgmBase $oCgmResponsavel,
        $observacao
    ) {
        $this->incluirProcesso($cgm, $solicitacaoOuvidoria->tipo_processo, $oCgmResponsavel, $observacao);
        $this->andamentoProcesso('Processo ' . $this->processo->getCodigoProcesso() . ' criado');
        $this->vincularProcessoAtendimento($solicitacaoOuvidoria->sequencial);
        $this->processoService->criarCapa($this->processo);
        $this->criarInformacoesProcessoPDF($solicitacaoOuvidoria->metadados);
        $this->anexarDocumentosProcesso($solicitacaoOuvidoria->metadados);
    }

    /**
     * Função que realiza a ação de aprovar um processo mas não gera capa e documentos nos e-Storage
     *
     * @param \Cgmbase $cgm
     * @param \stdClass $solicitacaoOuvidoria
     * @throws \Exception
     */
    public function aprovarProcessoSemCapa(
        \CgmBase $cgm,
        $solicitacaoOuvidoria,
        \CgmBase $oCgmResponsavel,
        $observacao
    ) {
        $this->incluirProcesso($cgm, $solicitacaoOuvidoria->tipo_processo, $oCgmResponsavel, $observacao);
        $this->andamentoProcesso('Processo ' . $this->processo->getCodigoProcesso() . ' criado');
        $this->vincularProcessoAtendimento($solicitacaoOuvidoria->sequencial);
    }

    /**
     * Função que realiza a ação de rejeitar um processo
     *
     * @param \Cgmbase $cgm
     * @param \stdClass $solicitacaoOuvidoria
     * @param string $motivo
     */
    public function rejeitarProcesso(\CgmBase $cgm, $solicitacaoOuvidoria, $motivo, $oCgmResponsavel)
    {
        $this->incluirProcesso($cgm, $solicitacaoOuvidoria->tipo_processo, $oCgmResponsavel, $motivo);
        $this->andamentoProcesso('Processo ' . $this->processo->getCodigoProcesso() . ' criado e arquivado.', true);
        $this->vincularProcessoAtendimento($solicitacaoOuvidoria->sequencial);
        $this->processoService->criarCapa($this->processo);
        $this->criarInformacoesProcessoPDF($solicitacaoOuvidoria->metadados, $motivo);
        $this->anexarDocumentosProcesso($solicitacaoOuvidoria->metadados);
        $this->baixarProcesso($motivo);
    }

    /**
     * Função que percorre os dados da solicitação e salva os documentos
     *
     * @param stdClass $document
     */
    protected function anexarDocumentosProcesso($dados)
    {
        if (!isset($dados->secoes)) {
            throw new \Exception("JSON inválido!");
        }

        $ordem = 3;
        foreach ($dados->secoes as $sessao) {
            if (strtoupper($sessao->tipo) != "ANEXO") {
                continue;
            }

            if (isset($sessao->resposta)) {
                foreach ($sessao->resposta as $documentoStorage) {
                    if ($documentoStorage->codigo == null) {
                        throw new \Exception("Documento inválido");
                    }

                    $this->salvarDocumento(
                        $documentoStorage,
                        $ordem
                    );

                    $ordem++;
                }
            }
        }
    }

    /**
     * Função que vincula um atendimento a um processo do protocolo
     *
     * @param int $processoOuvidoria
     */
    protected function vincularProcessoAtendimento($processoOuvidoria)
    {
        $model = new ProcessoOuvidoria();
        $model->setAtendimento($processoOuvidoria);
        $model->setProcesso($this->processo->getCodigoProcesso());
        $model->setPrincipal(true);

        $this->processoService->salvarProcessoOuvidoria($model);
    }

    /**
     * Função que busca uma solicitação junto com um cidadao
     *
     * @param AtendimentoRepository $atendimentoRepository
     * @param AtendimentoProcessoService $atendimentoProcessoService
     * @param FiltroListagemProcessos $filtroProcesso
     * @throws \Exception
     */
    public function buscarSolicitacaoOuvidoria(FiltroListagemProcessos $filtroProcesso, $useLegacy = false)
    {
        if ($useLegacy) {
            $solicitacao = $this->atendimentoRepository->buscarSolicitacaoOuvidoriaLegacy($filtroProcesso);
        } else {
            $solicitacao = $this->atendimentoRepository->buscarSolicitacaoOuvidoria($filtroProcesso);
        }

        if (!empty($solicitacao->ov02_sequencial)) {
            $solicitacao->setCidadao($this->cidadaoRepository->find(
                $solicitacao->ov02_sequencial
            ));

            unset($solicitacao->ov02_sequencial);
        }

        if (!empty($solicitacao->formareclamacao)
            and $solicitacao->formareclamacao == 9
            and !empty($solicitacao->metadados)
        ) {
            $metados = json_decode(utf8_decode($solicitacao->metadados));
            if (!empty($metados->secoes[0])) {
                if (!empty($metados->secoes[0]->campos[0])) {
                    if (!empty($metados->secoes[0]->campos[0]->nome)
                        and in_array(
                            $metados->secoes[0]->campos[0]->nome,
                            array("cpf", "cnpj")
                        )
                    ) {
                        $cpfcnpj = str_replace(array(".", "-", "/"), "", $metados->secoes[0]->campos[0]->resposta);
                        $cgm = Cgm::where("z01_cgccpf", "=", $cpfcnpj)->with('enderecoPrimario.endereco')->first();
                        $solicitacao->setCgm($cgm);
                    }
                }
            }
        }

        return $solicitacao;
    }

    /**
     * Função que inclui um novo processo
     *
     * @param \CgmBase $cgm
     * @param integer $codigoTipoProcesso
     * @param \CgmBase $oCgmResponsavel
     * @throws \Exception
     */
    public function incluirProcesso(\CgmBase $cgm, $codigoTipoProcesso, \CgmBase $oCgmResponsavel, $observacao)
    {

        $tipoProcesso = TipoProcessoFormaReclamacao::where("p43_tipoproc", "=", $codigoTipoProcesso)->first();
        if (!$tipoProcesso) {
            throw new \Exception("Tipo de Processo não encontrado!");
        }
        $this->setProcesso(new Processo());
        $this->processo->setCodigo($codigoTipoProcesso);
        $this->processo->setInterno(false);
        $this->processo->setPublico(true);
        $this->processo->setCgm($oCgmResponsavel->getCodigo());
        $this->processo->setDespacho('Criado Processo');
        $this->processo->setObservacao($observacao);
        $this->processo->setAno(DefaultSession::getInstance()->get(DefaultSession::DB_ANOUSU));
        $this->processo->setRequerente(substr($cgm->getNome(), 0, 79));
        $this->processo->setTipoProcesso(Processo::TIPO_PROCESSO_ELETRONICO);
        $oProcesso = $this->processoService->salvarProcesso($this->processo);

        $this->setProcesso($oProcesso);
    }

    /**
     * Função que salva um documento
     *
     * @param \stdClass $documento
     * @param int $ordem
     */
    protected function salvarDocumento(
        $documentoStorage,
        $ordem
    ) {
        $model = new ProcessoDocumento();

        $model->setProcesso($this->processo->getCodigoProcesso());
        $model->setDescricao($documentoStorage->descricao);
        $model->setDocumento($documentoStorage->codigo);
        $model->setNomeDocumento($documentoStorage->descricao);
        $model->setUsuario(DefaultSession::getInstance()->get(DefaultSession::DB_ID_USUARIO));
        $model->setData(date("Y-m-d"));
        $model->setStorage(true);
        $model->setOrdem($ordem);
        $this->processoService->salvarDocumento($model);
    }


    /**
     * Cria PDF com os dados enviados pela web
     *
     * @param \stdClass $dadosSolicitacao
     */
    private function criarInformacoesProcessoPDF($dadosSolicitacao, $motivoRejeicao = null)
    {
        $informacoesProcessoPDFService = new InformacoesProcessoPDFService();
        if (!empty($motivoRejeicao)) {
            $informacoesProcessoPDFService->setMotivoRejeicao($motivoRejeicao);
        }
        $caminho = $informacoesProcessoPDFService->gerar($this->processo, $dadosSolicitacao);

        $metadata = new \stdClass();
        $metadata->tipo_documento = "processo";
        $metadata->numero_do_processo = $this->processo->getNumero() . "/" . $this->processo->getAno();
        $metadata->requerente = $this->processo->getRequerente();
        $metadata->data_hora = $this->processo->getData() . " " . $this->processo->getHora();
        $rsProcessoOuvidoria = db_query("
                    SELECT
                    *
                    FROM
                          processoouvidoria
                    INNER JOIN ouvidoriaatendimento
                    ON processoouvidoria.ov09_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial
                    WHERE
                    ov09_protprocesso = {$this->processo->getCodigoProcesso()}
         ");

        $processoOuvidoria = pg_fetch_object($rsProcessoOuvidoria);

        if (!empty($processoOuvidoria)) {
            $numeroAtendimento = $processoOuvidoria->ov01_numero;
            $numeroAtendimento .= "/" . $processoOuvidoria->ov01_anousu;
            $metadata->numero_atendimento = $numeroAtendimento;
        }

        $metadata->codigo_usuario_aprovacao = $this->processo->getUsuario();
        $metadata->login_usuario_aprovacao = DefaultSession::getInstance()->get(DefaultSession::DB_LOGIN);

        $documentoStorage = StorageHelper::uploadArquivo($caminho, null, null, $metadata);
        $documentoStorage->codigo = $documentoStorage->id;
        $documentoStorage->descricao = $documentoStorage->name;
        $this->salvarDocumento($documentoStorage, 2);
    }

    /**
     * Função que realiza o andamento inicial do processo
     * @param string $despacho
     * @param boolean $efetuarRecebimento
     * @throws \Exception
     */
    protected function andamentoProcesso($despacho, $efetuarRecebimento = false)
    {
        $defaultSession = DefaultSession::getInstance();
        $usuario = $defaultSession->get(DefaultSession::DB_ID_USUARIO);
        $departamento = $defaultSession->get(DefaultSession::DB_CODDEPTO);
        $departamentoRecebimento = $this->processoService->getDepartamentoAndamentoPadrao($this->processo);

        $transferencia = $this->processoService->transferir(
            $this->processo,
            $departamentoRecebimento,
            $departamento,
            $usuario
        );

        if ($efetuarRecebimento) {
            $this->setProcessoAndamento($this->processoService->receber(
                $this->processo,
                $transferencia,
                $despacho
            ));
        } else {
            $this->setProcessoAndamento($this->processoService->andamentoSemReceber(
                $this->processo,
                $transferencia,
                $despacho
            ));
        }
    }

    /**
     * Função que realiza a baixa do processo
     *
     * @param string $motivo
     */
    public function baixarProcesso($motivo, $isLegacy = true)
    {
        $this->processoService->isLegacy = $isLegacy;
        $this->processoService->arquivar($this->processo, $motivo, null);
    }

    /**
     * Setter para propriedade $processoAndamento
     *
     * @param ProcessoAndamento $processoAndamento
     * @return $this
     */
    public function setProcessoAndamento(ProcessoAndamento $processoAndamento)
    {
        $this->processoAndamento = $processoAndamento;
        return $this;
    }

    /**
     * Setter para propriedade $processo
     *
     * @param Processo $processo
     * @return $this
     */
    public function setProcesso(Processo $processo)
    {
        $this->processo = $processo;
        return $this;
    }

    /**
     * Getter para propriedade $processo
     *
     * @return Processo $processo
     */
    public function getProcesso()
    {
        return $this->processo;
    }


    public function extrairInformacoesMetadadosEauth($metadados)
    {
        $dadosRequerente = array();
        $dadosApiEauth = array();

        foreach ($metadados->secoes as $secao) {
            if ($secao->nome == "requerente") {
                $dadosRequerente = $secao;
            }
        }

        if (empty($dadosRequerente)) {
            return false;
        }

        foreach ($dadosRequerente->campos as $campo) {
            if (empty($campo->resposta)) {
                $campo->resposta = null;
            }
            $dadosApiEauth[strtolower($campo->nome)] = $campo->resposta;
            if (is_object($campo->resposta)) {
                if ($campo->nome === "sexo") {
                    if ($campo->resposta->codigo === "N") {
                        $dadosApiEauth[strtolower($campo->nome)] = "";
                    } else {
                        $dadosApiEauth[strtolower($campo->nome)] = $campo->resposta->codigo;
                    }
                } else {
                    $dadosApiEauth[strtolower($campo->nome)] = $campo->resposta->descricao;
                }
            }
        }

        if (!empty($dadosApiEauth["data"])) {
            $dataAux = str_replace("/", "-", $dadosApiEauth["data"]);
            $parts = explode("-", $dataAux);
            if (strlen($parts[0]) > 2) {
                $dadosApiEauth["data"] = "{$parts[0]}-$parts[1]-$parts[2]";
            } else {
                $dadosApiEauth["data"] = "{$parts[2]}-$parts[1]-$parts[0]";
            }
        }

        if (!empty($dadosApiEauth["cpf"])) {
            $dadosApiEauth["cgccpf"] = trim(str_replace(array(".", "-", "/"), "", $dadosApiEauth["cpf"]));
        }

        if (!empty($dadosApiEauth["cnpj"])) {
            $dadosApiEauth["cgccpf"] = trim(str_replace(array(".", "-", "/"), "", $dadosApiEauth["cnpj"]));
        }

        if (!empty($dadosApiEauth["cep"])) {
            $dadosApiEauth["cep"] = trim(str_replace("-", "", $dadosApiEauth["cep"]));
        }
        return (object)$dadosApiEauth;
    }

    /**
     * @throws \Exception
     */
    public function getCgmResponsavelByMetadados($oMetadados)
    {
        $oResponsavel = null;
        $oEndereco = null;

        $this->buscaSecoes($oMetadados, $oResponsavel, $oEndereco); // Variáveis por referência

        if (empty($oResponsavel)) {
            return false;
        }

        $sCpfCnpj = null;
        $sNome = null;

        $this->ajustaDadosResponsavel($oResponsavel, $sCpfCnpj, $sNome); // Variáveis por referência

        if (empty($sCpfCnpj)) {
            return false;
        }

        $oCgm = \CgmFactory::getInstanceByCnpjCpf($sCpfCnpj);

        if (!$oCgm) {
            if (strlen(trim($sCpfCnpj)) == '11') {
                $oCgm = \CgmFactory::getInstanceByType(\CgmFactory::FISICO);
                $oCgm->setCpf($sCpfCnpj);
            } else {
                if (strlen(trim($sCpfCnpj)) == '14') {
                    $oCgm = \CgmFactory::getInstanceByType(\CgmFactory::JURIDICO);
                    $oCgm->setCnpj($sCpfCnpj);
                }
            }
            $oCgm->setNome(\DBString::upperCaseRemoveCaracteresEspeciais(substr($sNome, 0, 40)));
            $oCgm->setNomeCompleto(\DBString::upperCaseRemoveCaracteresEspeciais(
                substr($sNome, 0, 100)
            ));

            if (!empty($oEndereco)) {
                $oCgm = $this->ajustaEndereco($oCgm, $oEndereco);
            }

            $oCgm->save();
        }

        return $oCgm;
    }

    private function buscaSecoes($oMetadados, &$oResponsavel, &$oEndereco)
    {
        foreach ($oMetadados->secoes as $oSecao) {
            switch ($oSecao->nome) {
                case "responsavel":
                case "dados_responsavel":
                case "dados_empresa":
                    $oResponsavel = $oSecao;
                    break;
                case "endereco_municipio":
                    $oEndereco = $oSecao;
                    break;
                case "requerente_com_endereco":
                    $oResponsavel = $oSecao;
                    $oEndereco = $oSecao;
                    break;
            }
        }
    }

    private function ajustaDadosResponsavel($oResponsavel, &$sCpfCnpj, &$sNome)
    {
        foreach ($oResponsavel->campos as $oCampo) {
            switch ($oCampo->nome) {
                case "cpf":
                case "cnpj":
                case "cpf_cnpj":
                case "cpfCpj":
                    if (isset($oCampo->resposta)) {
                        $iResposta = preg_replace('/\D/', "", $oCampo->resposta);

                        if (!empty(trim($iResposta))) {
                            $sCpfCnpj = $iResposta;
                        }
                    }
                    break;
                case "razao_social":
                    if (isset($oCampo->resposta)) {
                        $sNome = $oCampo->resposta;
                    } elseif (isset($oResponsavel->resposta) && is_array($oResponsavel->resposta)) {
                        if (isset($oResponsavel->resposta[0]->razao_social)) {
                            $sNome = $oResponsavel->resposta[0]->razao_social;
                            $sCpfCnpj = $oResponsavel->resposta[0]->cpf;
                        } else {
                            throw new \Exception("Resposta para o campo razao_social não encontrada!");
                        }
                    } else {
                        throw new \Exception("Resposta para o campo razao_social não encontrada!");
                    }

                    break;
            }
        }
    }

    private function ajustaEndereco(\CgmBase $oCgm, $oEndereco)
    {
        $oInstituicao = (object)ProcessoEletronicoHelper::getDadosMunicipio("munic, uf");

        $oCgm->setUf(mb_strtoupper($oInstituicao->uf));
        $oCgm->setMunicipio(\DBString::upperCaseRemoveCaracteresEspeciais($oInstituicao->munic));

        foreach ($oEndereco->campos as $oCampo) {
            switch ($oCampo->nome) {
                case "cep":
                case "responsavel_cep":
                    if (isset($oCampo->resposta)) {
                        $oCgm->setCep(mb_strtoupper(trim(str_replace("-", "", $oCampo->resposta))));
                    }

                    break;
                case "bairro":
                    if (isset($oCampo->resposta->descricao)) {
                        $oCgm->setBairro(mb_strtoupper($oCampo->resposta->descricao));
                    }

                    break;
                case "numero":
                case "responsavel_numero":
                    if (isset($oCampo->resposta)) {
                        $oCgm->setNumero(mb_strtoupper($oCampo->resposta));
                    }

                    break;
                case "responsavel_logradouro":
                    $oCgm->setLogradouro(mb_strtoupper($oCampo->resposta));
                    break;
                case "logradouro":
                    if (isset($oCampo->resposta->descricao)) {
                        $oCgm->setLogradouro(mb_strtoupper($oCampo->resposta->descricao));
                    } else {
                        $oCgm->setLogradouro(" ");
                    }

                    break;
                case "complemento":
                case "responsavel_complemento":
                    if (isset($oCampo->resposta)) {
                        $oCgm->setComplemento(mb_strtoupper($oCampo->resposta));
                    }

                    break;
                case "responsavel_bairro":
                    $oCgm->setBairro(mb_strtoupper($oCampo->resposta));
                    break;
            }
        }

        return $oCgm;
    }

    /**
     * @throws Exception
     */
    public function alteraStatusAtendimento($solicitacaoOuvidoriaId, $idStatus)
    {
        $sql = "update ouvidoria.ouvidoriaatendimento set ov01_status = {$idStatus}
where ouvidoria.ouvidoriaatendimento.ov01_sequencial = {$solicitacaoOuvidoriaId}";
        $result = db_query($sql);

        if (pg_affected_rows($result) == 0) {
            throw new Exception("Falha ao atualizar status do atendimento!" . pg_last_error());
        }
    }

    public function consultaAtendimentos($parametros)
    {
        $atendimento = "ov01_numero || '/' || ov01_anousu";

        $query = DB::table('ouvidoriaatendimento')
            ->select(
                'db_depart.coddepto AS codigo_departamento_origem',
                'db_depart.descrdepto AS descricao_departamento_origem',
                'ouvidoria.ouvidoriaatendimento.ov01_sequencial AS sequencial',
                DB::raw("CONCAT(ov01_numero, '/', ov01_anousu) AS atendimento"),
                'ouvidoria.ouvidoriaatendimento.ov01_dataatend AS data',
                DB::raw("CONCAT(protprocesso.p58_numero, '/', protprocesso.p58_ano) AS processo_numero_ano"),
                'db_config.nomeinst',
                'ouvidoriaatendimento_status.ov35_descricao AS status',
                'ov02_nome AS solicitante',
                'p51_descr AS tipo_processo_descricao',
                'ov33_informacoesprocesso as metadados',
                'cgm.z01_numcgm as cgm',
                'db_config.codigo as codigo_instituicao'
            )
            ->leftJoin('processoouvidoria', 'ov09_ouvidoriaatendimento', '=', 'ov01_sequencial')
            ->leftJoin('protprocesso', 'protprocesso.p58_codproc', '=', 'processoouvidoria.ov09_protprocesso')
            ->join('db_config', 'db_config.codigo', '=', 'ouvidoria.ouvidoriaatendimento.ov01_instit')
            ->join('ouvidoriaatendimento_status', 'ouvidoriaatendimento_status.ov35_id', '=', 'ov01_status')
            ->leftJoin('ouvidoriaatendimentocidadao', 'ov10_ouvidoriaatendimento', '=', 'ov01_sequencial')
            ->leftJoin('cidadao', function ($join) {
                $join->on('cidadao.ov02_sequencial', '=', 'ouvidoria.ouvidoriaatendimentocidadao.ov10_cidadao')
                    ->on('cidadao.ov02_seq', '=', 'ouvidoria.ouvidoriaatendimentocidadao.ov10_seq');
            })
            ->join('ouvidoriaatendimentoprocessoeletronico', 'ov33_ouvidoriaatendimento', '=', 'ov01_sequencial')
            ->join('tipoproc', 'p51_codigo', '=', 'ouvidoria.ouvidoriaatendimento.ov01_tipoprocesso')
            ->join('db_depart', 'coddepto', '=', 'ouvidoria.ouvidoriaatendimento.ov01_depart')
            ->leftJoin('cgm', 'cgm.z01_cgccpf', '=', 'cidadao.ov02_cnpjcpf');

        if (isset($parametros->cgm)) {
            $query->where('cgm.z01_numcgm', '=', $parametros->cgm);
        }

        if (isset($parametros->atendimento)) {
            $query->whereRaw("{$atendimento} ILIKE ?", ['%'.$parametros->atendimento.'%']);
        }

        if (isset($parametros->descricao)) {
            $query->whereRaw("p51_descr ILIKE ?", ['%'.$parametros->descricao.'%']);
        }

        if (isset($parametros->dataDe) && isset($parametros->dataAte)) {
            $query->whereBetween('ov01_dataatend', [$parametros->dataDe, $parametros->dataAte]);
        }

        if (isset($parametros->filtroDepartamento)) {
            $query->where('ov01_depart', '=', db_getsession('DB_coddepto'));
        }

        if (isset($parametros->aprovarAtendimento)) {
            $query->where('ov01_status', '=', 1);
        }

        if (isset($parametros->filtrosStatus)) {
            $status = explode(', ', $parametros->filtrosStatus);
            $query->whereIn('ouvidoriaatendimento_status.ov35_descricao', $status);
        }

        $query->orderBy('ouvidoria.ouvidoriaatendimento.ov01_sequencial', 'desc');

        if (isset($parametros->perpage)) {
            return [
                'data' => $query->paginate($parametros->perpage),
                'instituicao_atual' => $this->getIdInstituicao()
            ];
        }

        return [
            'data' => $query->paginate(15),
            'instituicao_atual' => $this->getIdInstituicao()
        ];
    }

    public function getIdInstituicao()
    {
        $defaultSession = DefaultSession::getInstance();
        return $defaultSession->get(DefaultSession::DB_INSTIT);
    }
}
