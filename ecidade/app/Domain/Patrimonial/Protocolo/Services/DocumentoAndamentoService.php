<?php

namespace App\Domain\Patrimonial\Protocolo\Services;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Protocolo\Model\AtividadeExecucao;
use App\Domain\Patrimonial\Protocolo\Model\DocumentoAndamento;
use App\Domain\Patrimonial\Protocolo\Model\DocumentoMovimentacao;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoAndamento;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoDocumento;
use App\Domain\Patrimonial\Protocolo\Model\ProcessoAtividadeExecucao;
use App\Domain\Patrimonial\Protocolo\Model\TipoProcesso;
use App\Domain\Patrimonial\Protocolo\Repository\DocumentosAndamentoRepository;
use App\Domain\Patrimonial\Protocolo\Repository\DocumentosMovimentacaoRepository;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoDocumentoRepository;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoRepository;
use App\Domain\Patrimonial\Protocolo\Repository\ProcessoAtividadeExecucaoRepository;
use ECidade\Lib\Session\DefaultSession;
use ECidade\Patrimonial\Protocolo\Modelo\AndamentoProcessoInterno;
use ECidade\Patrimonial\Protocolo\Repositorio\AndamentoProcessoInternoRepository;
use Exception;
use Instituicao;

abstract class DocumentoAndamentoService
{
    /**
     * @var ProcessoService
     */
    private $processoService;

    /**
     * @var Processo
     */
    protected $processo;
    /**
     * @var ProcessoAtividadeExecucao[]
     */
    private $atividadesExecucao = [];

    /**
     * @var DocumentoAndamento
     */
    protected $documento;
    /**
     * @var DocumentosAndamentoRepository
     */
    private $repository;

    public function __construct()
    {
        $this->processoService = new ProcessoService();
        $this->repository = new DocumentosAndamentoRepository();
    }

    /**
     * @param $codigoTipoDocumento
     * @param $codigoOrigem
     * @return TipoProcesso|null
     * @throws Exception
     */
    public static function getTipoProcesso($codigoTipoDocumento, $codigoOrigem)
    {
        if (empty($codigoTipoDocumento) || empty($codigoOrigem)) {
            return null;
        }
        // PL que deve retornar o código do tipo de documento correto para o documento
        $rs = db_query(
            "select fc_tipoprocesso_documentoandamento({$codigoTipoDocumento}, {$codigoOrigem}) as codigo"
        );
        if (!$rs) {
            $error = pg_last_error();
            $msg = substr($error, 7, (strpos($error, 'CONTEXT:') - 7));
            throw new Exception("Erro ao buscar Tipo de Processo para Andamento do Documento.\n\n{$msg}");
        }
        if (pg_num_rows($rs) == 0) {
            return null;
        }
        $tipoProcesso = pg_fetch_object($rs);
        return TipoProcesso::find($tipoProcesso->codigo);
    }

    abstract public function gerar($arquivo, $uuid);

    abstract public function montarObjetoTela();

    /**
     * @param TipoProcesso $tipoProcesso
     * @param $despacho
     * @return Processo
     * @throws Exception
     */
    public function gerarProcessoDocumento(TipoProcesso $tipoProcesso, $despacho)
    {
        $this->processo = $this->incluirNovoProcesso($tipoProcesso);
        $this->executarAndamentoProcesso($this->processo, $despacho);
        $this->processoService->criarCapa($this->processo);
        $this->criarFluxoAtividadesExecucao($this->processo);

        return $this->processo;
    }

    /**
     * @param TipoProcesso $tipoProcesso
     * @return Processo
     * @throws Exception
     */
    public function incluirNovoProcesso(TipoProcesso $tipoProcesso)
    {
        $instituicao = new Instituicao(db_getsession('DB_instit'));
        $numeroCgm = $instituicao->getCgm()->getCodigo();
        $nomeCgm = $instituicao->getCgm()->getNome();

        $processo = new Processo();
        $processo->setCodigo($tipoProcesso->p51_codigo);
        $processo->setCgm($numeroCgm);
        $processo->setRequerente($nomeCgm);
        $processo->setObservacao($tipoProcesso->p51_descr);
        $processo->setInterno('false');
        $processo->setPublico('false');
        $processo->setAno(db_getsession("DB_anousu"));
        $processo->setTipoProcesso(Processo::TIPO_PROCESSO_ELETRONICO);
        $processoRepository = new ProcessoRepository();

        return $processoRepository->persist($processo);
    }

    /**.
     * @param Processo $processo
     * @param $despacho
     * @return ProcessoAndamento
     * @throws Exception
     */
    public function executarAndamentoProcesso(Processo $processo, $despacho)
    {
        DefaultSession::getInstance()->set(DefaultSession::DB_DATAUSU, db_getsession('DB_datausu'));
        $transferencia = $this->processoService->transferir(
            $processo,
            db_getsession('DB_coddepto'),
            db_getsession('DB_coddepto'),
            db_getsession('DB_id_usuario')
        );

        return $this->processoService->receber(
            $processo,
            $transferencia,
            $despacho
        );
    }

    /**
     * @param $arquivo
     * @param $status
     * @return void
     * @throws Exception
     */
    final public function vincularDocumento($arquivo, $status)
    {
        // Criar andamento interno
        $andamentoInterno = new AndamentoProcessoInterno();
        $andamentoInterno->setIdAndamento($this->processo->getCodigoAndamento());
        $andamentoInterno->setDespacho($status);
        $andamentoInterno->setPublico(false);
        $andamentoInterno->setTransitoInterno(false);
        $andamentoInterno->setData(date('Y-m-d'));
        $andamentoInterno->setHora(db_hora());
        $andamentoInterno->setIdUsuario(db_getsession("DB_id_usuario"));
        $andamentoInterno->setIdTipoDespacho(1);

        $andamentoInternoRepository = new AndamentoProcessoInternoRepository(new \cl_procandamint());
        $andamentoInternoRepository->save($andamentoInterno);

        // Vincular Documento
        $processoDocumento = new ProcessoDocumento();
        $processoDocumento->setDescricao($arquivo->name);
        $processoDocumento->setProcesso($this->processo->getCodigoProcesso());
        $processoDocumento->setDocumento($arquivo->id);
        $processoDocumento->setStorage(true);
        $processoDocumento->setNomeDocumento($arquivo->name);
        $processoDocumento->setData(date('Y-m-d'));
        $processoDocumento->setAndamento($andamentoInterno->getId());
        $processoDocumento->setUsuario(db_getsession('DB_id_usuario'));
        $processoDocumento->setOrdem(2);
        if (isset($arquivo->lancContabilID)) {
            $processoDocumento->setC70codlan($arquivo->lancContabilID);
        }
        if (isset($arquivo->nomeUpload)) {
            $nomeSemAspas = str_replace("'", "", $arquivo->nomeUpload);
            $processoDocumento->setNomeUpload($nomeSemAspas);
        }

        $processoDocumentoRepository = new ProcessoDocumentoRepository();
        $processoDocumento = $processoDocumentoRepository->persist($processoDocumento);

        $this->documento->p116_protprocessodocumento = $processoDocumento->getCodigo();
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function criarFluxoAtividadesExecucao(Processo $processo)
    {
        $atividadesTipoProcesso =  TipoProcesso::find($processo->getCodigo())->atividades()->get();
        $processoAtividadeExecucaoRepository = new ProcessoAtividadeExecucaoRepository();
        foreach ($atividadesTipoProcesso as $atividadeTipoProcesso) {
            $processoAtividadeExecucao = new ProcessoAtividadeExecucao();
            $processoAtividadeExecucao->p118_protprocesso = $processo->getCodigoProcesso();
            $processoAtividadeExecucao->p118_atividadesexecucao = $atividadeTipoProcesso->p114_codigo;
            $processoAtividadeExecucao->p118_ordem = $atividadeTipoProcesso->pivot->p115_ordem;

            $processoAtividadeExecucaoRepository->salvar($processoAtividadeExecucao);
            $this->atividadesExecucao[] = $processoAtividadeExecucao;
        }
    }

    /**
     * @param Usuario $usuarioPermitido
     * @param AtividadeExecucao $atividadeexecucao
     * @return void
     * @throws Exception
     */
    protected function adicionarUsuario(Usuario $usuarioPermitido, AtividadeExecucao $atividadeexecucao)
    {
        $daoProcessoUsuario = new \cl_processo_usuarios();
        $daoProcessoUsuario->p119_protprocesso = $this->processo->p58_codproc;
        $daoProcessoUsuario->p119_id_usuario = $usuarioPermitido->getCodigo();
        $daoProcessoUsuario->p119_atividadeexecucao = $atividadeexecucao->p114_codigo;

        // Verifica se já existe as permissões
        $sql = $daoProcessoUsuario->sql_query_file(
            null,
            '*',
            null,
            "p119_protprocesso = {$this->processo->p58_codproc} AND
            p119_id_usuario = {$usuarioPermitido->getCodigo()} AND
            p119_atividadeexecucao = {$atividadeexecucao->p114_codigo}"
        );
        $rs = db_query($sql);
        if (pg_num_rows($rs) > 0) {
            return;
        }

        $daoProcessoUsuario->incluir(null);
        if ($daoProcessoUsuario->erro_status == 0) {
            throw new Exception("Erro ao vincular permissão de usuário ao processo");
        }
    }

    /**
     * @throws Exception
     * @retrun ProcessoAtividadeExecucao|null
     */
    protected function buscarPrimeiraAtividade()
    {
        return (new ProcessoAtividadeExecucaoRepository())->scopeProcesso($this->processo)->first();
    }

    /**
     * @param $ordem
     * @return ProcessoAtividadeExecucao|null
     * @throws Exception
     */
    protected function buscarProximaAtividade($ordem)
    {
        return (new ProcessoAtividadeExecucaoRepository())
            ->scopeOrdem(++$ordem)
            ->scopeProcesso($this->processo)
            ->first();
    }

    /**
     * @param $ordem
     * @return ProcessoAtividadeExecucao|null
     * @throws Exception
     */
    private function buscarAtividadeAnderior($ordem)
    {
        return (new ProcessoAtividadeExecucaoRepository())
            ->scopeOrdem(--$ordem)
            ->scopeProcesso($this->processo)
            ->first();
    }

    public function setDocumento(DocumentoAndamento $documentoAndamento)
    {
        $this->documento = $documentoAndamento;
        $this->processo = $this->documento->processo;
        return $this;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function conferir()
    {
        if ($this->documento->proximaAtividade->atividade->p114_codigo !== AtividadeExecucao::CONFERIR) {
            throw new Exception("Documento não pode ser conferido");
        }

        $this->executarProximaAtividade();
        $this->buscarUsuariosPermissoes();
        $this->documento = $this->repository->salvar($this->documento);
        $this->salvarMovimentacao();
        $this->executarAndamentoProcesso($this->processo, 'Conferido');

        $this->verificarUltimoEvento();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function devolverAtividade(ProcessoAtividadeExecucao $atividadeDevolver)
    {
        $this->voltarAtividade($atividadeDevolver);
        $this->buscarUsuariosPermissoes();

        $documentosMovimentacaoRepository = new DocumentosMovimentacaoRepository();
        $ultimaMovimentacao = $documentosMovimentacaoRepository
            ->scopeDocumento($this->documento)
            ->scopeAtividade($this->documento->p116_atividade_atual)
            ->last();

        if (!is_null($ultimaMovimentacao)) {
            $this->documento->p116_protprocessodocumento = $ultimaMovimentacao->p117_protprocessodocumento;
        }

        $documentoAndamentoRepository = new DocumentosAndamentoRepository();
        $this->documento = $documentoAndamentoRepository->salvar($this->documento);
        $this->salvarMovimentacao(true);
        $this->executarAndamentoProcesso($this->processo, 'Devolvido');
    }

    /**
     * @throws Exception
     */
    protected function salvarMovimentacao($devolucao = false)
    {
        $usuario = Usuario::find(db_getsession('DB_id_usuario'));

        $movimentacao = new DocumentoMovimentacao();
        $movimentacao->p117_documento_andamento = $this->documento->p116_codigo;
        $movimentacao->p117_id_usuario = $usuario->getCodigo();
        $movimentacao->p117_protprocessodocumento = $this->documento->p116_protprocessodocumento;
        $movimentacao->p117_processo_atividadesexecucao = $this->documento->p116_atividade_atual;
        $movimentacao->p117_devolucao = $devolucao;

        $documentosMovimentacaoRepository = new DocumentosMovimentacaoRepository();
        $movimentacaoAtual = $documentosMovimentacaoRepository->salvar($movimentacao);

        if ($devolucao) {
            $movimentacoesInativar = $documentosMovimentacaoRepository
                ->scopeDocumento($this->documento)
                ->scopeDataMenor($movimentacaoAtual->p117_data)
                ->scopeAtividadeExecucaoMaior($movimentacao->p117_processo_atividadesexecucao)
                ->scopeDevolucao('false')
                ->get();

            foreach ($movimentacoesInativar as $movimentacaoInativar) {
                $movimentacaoInativar->p117_invalida = true;
                $documentosMovimentacaoRepository->salvar($movimentacaoInativar);
            }
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function executarProximaAtividade()
    {
        $this->documento->p116_atividade_atual = $this->documento->p116_proxima_atividade;
        $proximaAtividade = $this->buscarProximaAtividade($this->documento->proximaAtividade->p118_ordem);
        $this->documento->p116_proxima_atividade = 'null';
        if (!is_null($proximaAtividade)) {
            $this->documento->p116_proxima_atividade = $proximaAtividade->p118_codigo;
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function voltarAtividade(ProcessoAtividadeExecucao $atividadeDevolver)
    {
        $this->documento->p116_atividade_atual = $atividadeDevolver->p118_codigo;
        $proximaAtividade = $this->buscarProximaAtividade($atividadeDevolver->p118_ordem);
        $this->documento->p116_proxima_atividade = $proximaAtividade->p118_codigo;
    }

    /**
     * @throws Exception
     */
    protected function isDevolvido()
    {
        $documentosMovimentacaoRepository = new DocumentosMovimentacaoRepository();
        $movimentacoes = $documentosMovimentacaoRepository->scopeDocumento($this->documento)->get();
        $ultimaMovimentacao = array_pop($movimentacoes);
        $penultimaMovimentacao = array_pop($movimentacoes);
        if (is_null($ultimaMovimentacao) || is_null($penultimaMovimentacao)) {
            return false;
        }
        $ordemUltimaMovimentacao = $ultimaMovimentacao->processoAtividadeExecucao->p118_ordem;
        $ordemPenultimaMovimentacao = $penultimaMovimentacao->processoAtividadeExecucao->p118_ordem;
        return $ordemUltimaMovimentacao < $ordemPenultimaMovimentacao;
    }

    /**
     * @param $arquivoAssinado
     * @return void
     * @throws Exception
     */
    public function assinar($arquivoAssinado)
    {
        if ($this->documento->proximaAtividade->atividade->p114_codigo !== AtividadeExecucao::ASSINAR) {
            throw new Exception("Documento não pode ser Assinado");
        }

        $this->executarProximaAtividade();
        $this->buscarUsuariosPermissoes();
        $this->executarAndamentoProcesso($this->processo, "Assinar");
        $this->vincularDocumento($arquivoAssinado, "Assinado");
        $this->salvarDocumentoAndamento();
        $this->salvarMovimentacao();
        $this->verificarUltimoEvento();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function arquivar()
    {
        $proximaAtividade = ProcessoAtividadeExecucao::find($this->documento->p116_proxima_atividade);
        if ($proximaAtividade->p118_atividadesexecucao !== AtividadeExecucao::ARQUIVAR) {
            throw new Exception("Documento não pode ser arquivado");
        }
        $this->executarProximaAtividade();
        $this->documento = $this->repository->salvar($this->documento);
        $this->salvarMovimentacao();
        $this->executarAndamentoProcesso($this->processo, 'Arquivado');

        $this->processoService->arquivar($this->getProcesso(), 'Fim do Fluxo de Andamento do Documento');
    }

    /**
     * @param $codigoOrigem
     * @param $descricao
     * @return void
     * @throws Exception
     */
    protected function criarNovoDocumentoAndamento($codigoOrigem, $descricao, $qrcode)
    {
        $documentoAndamento = new DocumentoAndamento();
        $documentoAndamento->p116_descricao = $descricao;
        $documentoAndamento->p116_protprocesso = $this->getProcesso()->getCodigoProcesso();
        $primeiraAtividade = $this->buscarPrimeiraAtividade();
        $documentoAndamento->p116_atividade_atual = $primeiraAtividade->p118_codigo;
        $proximaAtividade = $this->buscarProximaAtividade($primeiraAtividade->p118_ordem);
        $documentoAndamento->p116_proxima_atividade = $proximaAtividade->p118_codigo;
        $documentoAndamento->p116_codigo_origem = $codigoOrigem;
        $documentoAndamento->p116_qrcode = $qrcode;

        $this->documento = $documentoAndamento;
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function salvarDocumentoAndamento()
    {
        $this->documento = $this->repository->salvar($this->documento);
    }

    /**
     * @return ProcessoAtividadeExecucao[]
     * @throws Exception
     */
    public function buscarAtividadesExecutadas()
    {
        $ordem = $this->documento->atividadeAtual->p118_ordem;
        $processoAtividadeRepository = new ProcessoAtividadeExecucaoRepository();
        return $processoAtividadeRepository->scopeExecutadas($ordem)
            ->scopeProcesso($this->processo)
            ->get();
    }

    public function getProcesso()
    {
        return $this->processo;
    }

    /**
     * @param $arquivo
     * @return void
     * @throws Exception
     */
    public function reemitirDocumento($arquivo)
    {
        $primeiraAtividade = $this->buscarPrimeiraAtividade();
        $this->documento->p116_atividade_atual = $primeiraAtividade->p118_codigo;
        $proximaAtividade = $this->buscarProximaAtividade($primeiraAtividade->p118_ordem);
        $this->documento->p116_proxima_atividade = $proximaAtividade->p118_codigo;

        $this->buscarUsuariosPermissoes();
        $this->executarAndamentoProcesso($this->processo, "Alterado");
        $this->vincularDocumento($arquivo, "Alterado");
        $this->salvarDocumentoAndamento();
        $this->salvarMovimentacao(true);
    }

    /**
     * @return void
     * @throws Exception
     */
    private function verificarUltimoEvento()
    {
        if ($this->documento->p116_proxima_atividade == 'null') {
            return;
        }
        $proximaAtividade = ProcessoAtividadeExecucao::find($this->documento->p116_proxima_atividade);
        if ($proximaAtividade->p118_atividadesexecucao === AtividadeExecucao::ARQUIVAR) {
            $this->arquivar();
        }
    }
}
