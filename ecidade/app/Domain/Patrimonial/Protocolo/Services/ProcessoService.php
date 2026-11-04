<?php

namespace App\Domain\Patrimonial\Protocolo\Services;

use App\Domain\Configuracao\Configuracao\Repositories\MenuPermissionRepository;
use App\Domain\Configuracao\Usuario\Repository\UsuarioRepository;
use App\Domain\Patrimonial\Protocolo\Model\Processo\AndamentoProcessoInterno;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoAndamentoInternoRepository;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoTransferenciaAndamentoRepository;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoTransferenciaRepository;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ArquivamentoAndamentoRepository;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ArquivamentoProcessoRepository;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoAndamentoRepository;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoOuvidoriaRepository;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoDocumentoRepository;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\AndamentoPadraoRepository;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\TransferenciaRepository;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ArquivamentoRepository;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoRepository;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoTransferenciaAndamento;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoTransferencia;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ArquivamentoAndamento;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ArquivamentoProcesso;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoAndamento;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoDocumento;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoOuvidoria;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Transferencia;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Arquivamento;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use ECidade\Lib\Session\DefaultSession;
use App\Domain\Configuracao\Helpers\StorageHelper;
use ECidade\Patrimonial\Protocolo\Repositorio\TransferenciaRepositorio;
use ECidade\Patrimonial\Protocolo\Servicos\AndamentoProcessoService;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \processoProtocolo;
use ProcessoDocumento as ProcessoDocumentoLegacy;

/**
 * Classe ProcessoService
 * Faz os tramites referentes ao processo
 */
class ProcessoService
{
    /**
     * @var ProcessoRepository
     */
    private $processoRepository;
    /**
     * @var ArquivamentoRepository
     */
    private $arquivamentoRepository;
    /**
     * @var TransferenciaRepository
     */
    private $transferenciaRepository;
    /**
     * @var AndamentoPadraoRepository
     */
    private $andamentoPadraoRepository;
    /**
     * @var ProcessoDocumentoRepository
     */
    private $processoDocumentoRepository;
    /**
     * @var ProcessoAndamentoRepository
     */
    private $processoAndamentoRepository;
    /**
     * @var ProcessoOuvidoriaRepository
     */
    private $processoOuvidoriaRepository;
    /**
     * @var ArquivamentoProcessoRepository
     */
    private $arquivamentoProcessoRepository;
    /**
     * @var ArquivamentoAndamentoRepository
     */
    private $arquivamentoAndamentoRepository;
    /**
     * @var ProcessoTransferenciaRepository
     */
    private $processoTransferenciaRepository;
    /**
     * @var ProcessoTransferenciaAndamentoRepository
     */
    private $processoTransferenciaAndamentoRepository;

    public $isLegacy = true;
    /**
     * @var ProcessoAndamentoInternoRepository
     */
    private $processoAndamentoInternoRepository;
    /**
     * @var AndamentoProcessoService
     */
    private $andamentoProcesoService;

    /**
     * Construtor da classe
     */
    public function __construct()
    {
        $this->processoRepository = new ProcessoRepository();
        $this->arquivamentoRepository
                                  = new ArquivamentoRepository();
        $this->transferenciaRepository
                                  = new TransferenciaRepository();
        $this->andamentoPadraoRepository
                                  = new AndamentoPadraoRepository();
        $this->processoDocumentoRepository
                                  = new ProcessoDocumentoRepository();
        $this->processoAndamentoRepository
                                  = new ProcessoAndamentoRepository();
        $this->processoOuvidoriaRepository
                                  = new ProcessoOuvidoriaRepository();
        $this->arquivamentoProcessoRepository
                                  = new ArquivamentoProcessoRepository();
        $this->arquivamentoAndamentoRepository
                                  = new ArquivamentoAndamentoRepository();
        $this->processoTransferenciaRepository
                                  = new ProcessoTransferenciaRepository();
        $this->processoTransferenciaAndamentoRepository
                                  = new ProcessoTransferenciaAndamentoRepository();
        $this->processoAndamentoInternoRepository
                                  = new ProcessoAndamentoInternoRepository();
        $this->andamentoProcesoService = new AndamentoProcessoService(new \stdClass());
    }

    /**
     * @return AndamentoProcessoService
     */
    public function getAndamentoProcesoService()
    {
        return $this->andamentoProcesoService;
    }

    /**
     * Função que salva um documento
     *
     * @param  ProcessoDocumento  $processoDocumento
     *
     * @throws Exception
     */
    public function salvarDocumento(ProcessoDocumento $processoDocumento)
    {
        return $this->processoDocumentoRepository->persist($processoDocumento);
    }

    /**
     * Função que vincula um processo a um atendimento da ouvidoria
     *
     * @param  ProcessoOuvidoria  $processoOuvidoria
     *
     * @throws Exception
     */
    public function salvarProcessoOuvidoria(ProcessoOuvidoria $processoOuvidoria)
    {
        $this->processoOuvidoriaRepository->persist($processoOuvidoria);
    }

    /**
     * Função que salva um processo
     *
     * @param  Processo  $processo
     *
     * @return Processo
     * @throws Exception
     */
    public function salvarProcesso(
        Processo $processo,
        $siglaDoDocumentoNaNumeracao = false
    ) {
        return $this->processoRepository->persist(
            $processo,
            $siglaDoDocumentoNaNumeracao
        );
    }

    /**
     * Retorna o departamento do andamento padrao
     *
     * @param  Processo  $processo
     * @param  boolean  $proximoDepartamento
     *
     * @return integer $departamento
     * @throws Exception
     */
    public function getDepartamentoAndamentoPadrao(
        Processo $processo,
        $proximoDepartamento = true
    ) {
        $posicaoAtual
                 = $this->processoTransferenciaRepository->getPosicaoAtualProcesso(
                     $processo
                 );
        $posicao = ($proximoDepartamento) ? $posicaoAtual + 1 : $posicaoAtual;
        $departamento
                 = $this->andamentoPadraoRepository->getDepartamentoAndamentoPadrao(
                     $processo->getCodigo(),
                     $posicao
                 );

        if (! $departamento) {
            throw new Exception('Departamento padrão não encontrado!');
        }

        return $departamento;
    }

    /**
     * Função que transfere um processo
     *
     * @param  Processo  $processo
     * @param  int  $departamentoRecebimento
     * @param  int  $departamento
     * @param  int  $usuario
     * @param  int  $usuarioRecebimento
     *
     * @return Transferencia $transferencia
     * @throws Exception
     */
    public function transferir(
        Processo $processo,
        $departamentoRecebimento,
        $departamento,
        $usuario,
        $usuarioRecebimento = 0
    ) {
        $transferencia         = new Transferencia();
        $processoTransferencia = new ProcessoTransferencia();
        $data                  = DefaultSession::getInstance()->get(
            DefaultSession::DB_DATAUSU
        );

        $transferencia->setUsuario($usuario);
        $transferencia->setDepartamento($departamento);
        $transferencia->setUsuarioRecebimento($usuarioRecebimento);
        $transferencia->setDepartamentoRecebimento($departamentoRecebimento);
        $transferencia->setHora(date("H:i"));
        $transferencia->setData(date('Y-m-d', $data));

        $this->transferenciaRepository->persist($transferencia);

        $processoTransferencia->setCodigoTransferencia(
            $transferencia->getCodigo()
        );
        $processoTransferencia->setCodigoProcesso(
            $processo->getCodigoProcesso()
        );

        $this->processoTransferenciaRepository->persist($processoTransferencia);

        return $transferencia;
    }

    /**
     * Função que recebe um processo
     *
     * @param  Processo  $processo
     * @param  Transferencia  $transferencia
     * @param  string  $despacho
     *
     * @return ProcessoAndamento
     * @throws Exception
     */
    public function receber(
        Processo $processo,
        Transferencia $transferencia,
        $despacho
    ) {
        $processoAndamento              = new ProcessoAndamento();
        $usuarioRecebimento             = $transferencia->getUsuarioRecebimento();
        $processoTransferenciaAndamento = new ProcessoTransferenciaAndamento();

        $processoAndamento->setProcesso($processo->getCodigoProcesso());
        $processoAndamento->setUsuario(
            $usuarioRecebimento != 0
                ? $usuarioRecebimento
                : db_getsession(
                    "DB_id_usuario"
                )
        );
        $processoAndamento->setData($transferencia->getData());
        $processoAndamento->setDespacho($despacho);
        $processoAndamento->setDepartamento(
            $transferencia->getDepartamentoRecebimento()
        );
        $processoAndamento->setPublico($processo->getPublico());
        $processoAndamento->setHora($transferencia->getHora());

        $this->processoAndamentoRepository->persist($processoAndamento);

        $processoTransferenciaAndamento->setCodigoAndamento(
            $processoAndamento->getCodigo()
        );
        $processoTransferenciaAndamento->setCodigoTransferencia(
            $transferencia->getCodigo()
        );

        $this->processoTransferenciaAndamentoRepository->persist(
            $processoTransferenciaAndamento
        );

        $processo->setCodigoAndamento($processoAndamento->getCodigo());

        if ($this->isLegacy) {
            $this->salvarProcesso($processo);
        } else {
            $processo->saveOrFail();
        }

        return $processoAndamento;
    }

    /**
     * @param  Processo  $processo
     * @param  Transferencia  $transferencia
     * @param $despacho
     *
     * @return ProcessoAndamento
     * @throws Exception
     */
    public function andamentoSemReceber(
        Processo $processo,
        Transferencia $transferencia,
        $despacho
    ) {
        $processoAndamento  = new ProcessoAndamento();
        $usuarioRecebimento = $transferencia->getUsuarioRecebimento();
        $processoAndamento->setProcesso($processo->getCodigoProcesso());
        $processoAndamento->setUsuario(
            $usuarioRecebimento != 0 ? $usuarioRecebimento
                : $transferencia->getUsuario()
        );
        $processoAndamento->setData($transferencia->getData());
        $processoAndamento->setDespacho($despacho);
        $processoAndamento->setDepartamento(
            $transferencia->getDepartamentoRecebimento()
        );
        $processoAndamento->setPublico($processo->getPublico());
        $processoAndamento->setHora($transferencia->getHora());
        $processoAndamento = $this->processoAndamentoRepository->persist(
            $processoAndamento
        );
        $processo->setCodigoAndamento($processoAndamento->getCodigo());
        $this->salvarProcesso($processo);

        return $processoAndamento;
    }


    /**
     * @param  ProcessoAndamento  $andamento
     * @param $publico
     * @param $tipoDespacho
     *
     * @return AndamentoProcessoInterno
     * @throws Exception
     */
    public function despachar(
        ProcessoAndamento $andamento,
        $despacho,
        $publico = false,
        $tipoDespacho = 1
    ) {
        $processoAndamentoInterno = new AndamentoProcessoInterno();
        $processoAndamentoInterno->setCodigoAndamento($andamento->getCodigo());
        $processoAndamentoInterno->setCodigoUsuario(
            db_getsession("DB_id_usuario")
        );
        $processoAndamentoInterno->setData(date("Y-m-d"));
        $processoAndamentoInterno->setHora(date("H:i"));
        $processoAndamentoInterno->setPublico($publico);
        $processoAndamentoInterno->setTransint(false);
        $processoAndamentoInterno->setTipoDespacho($tipoDespacho);
        $processoAndamentoInterno->setDespacho($despacho);

        return $this->processoAndamentoInternoRepository->persist(
            $processoAndamentoInterno
        );
    }


    /**
     * @throws \DBException
     * @throws \ParameterException
     * @throws Exception
     */
    public function salvarPdfDespacho(
        Processo $processo,
        AndamentoProcessoInterno $andamentoProcessoInterno
    ) {
        $mostrarPDF              = true;
        $codproc                 = $processo->getCodigoProcesso();
        $iCodigoAndamentoDepacho = $processo->getCodigoAndamento();
        $codprocandamint         = $andamentoProcessoInterno->getSequencial();
        $caminho                 = "";

        require_once("pro2_despachointer002.php");

        $storageConfig = StorageHelper::getStorageConfig();
        $allowed       = array();

        if (! empty($storageConfig->client_id_ouvidoria)) {
            $allowed[] = $storageConfig->client_id_ouvidoria;
        }

        $metadata                     = new \stdClass();
        $metadata->tipo_documento     = "processo";
        $metadata->numero_do_processo = $processo->getNumero()."/"
                                        .$processo->getAno();
        $metadata->requerente         = $processo->getRequerente();
        $rsProcessoOuvidoria          = db_query(
            "
                    SELECT
                    *
                    FROM processoouvidoria
                    INNER JOIN ouvidoriaatendimento
                    ON processoouvidoria.ov09_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial
                    WHERE
                    ov09_protprocesso = {$processo->getCodigoProcesso()}
         "
        );

        $atendimentoOuvidoria = pg_fetch_object($rsProcessoOuvidoria);

        if (! empty($atendimentoOuvidoria)) {
            $numeroAtendimento            = $atendimentoOuvidoria->ov01_numero;
            $numeroAtendimento            .= "/"
                                             .$atendimentoOuvidoria->ov01_anousu;
            $metadata->numero_atendimento = $numeroAtendimento;
            $metadata->data_hora          = $andamentoProcessoInterno->getData()
                                            ." "
                                            .$andamentoProcessoInterno->getHora(
                                            );
        }

        $usuarioSistema                     = new \UsuarioSistema(
            db_getsession("DB_id_usuario")
        );
        $metadata->codigo_usuario_aprovacao = $usuarioSistema->getCodigo();
        $metadata->login_usuario_aprovacao  = $usuarioSistema->getLogin();
        $metadata->codigoDespacho
                                            = $andamentoProcessoInterno->getSequencial(
                                            );
        $processoProtocolo                  = new processoProtocolo(
            $processo->getCodigoProcesso()
        );

        $ordem             = ProcessoDocumentoLegacy::getLastOrdemProcesso(
            $codproc
        ) + 1;
        $processoDocumento = new ProcessoDocumentoLegacy(null);

        $processoDocumento->setProcandamint(
            $andamentoProcessoInterno->getSequencial()
        );
        $processoDocumento->setDescricao("Despacho do processo {$codproc}");
        $processoDocumento->setProcessoProtocolo($processoProtocolo);
        $processoDocumento->setUsuario($usuarioSistema);
        $processoDocumento->setCaminhoArquivo($caminho);
        $processoDocumento->setStorage(true);
        $processoDocumento->setOrdem($ordem);
        $processoDocumento->setNomeDocumento(
            "Despacho do processo {$codproc}.pdf"
        );
        $processoDocumento->setOID(
            StorageHelper::uploadArquivo($caminho, $allowed, true, $metadata)
        );

        $processoDocumento->salvar();
        return true;
    }


    /**
     * Função que arquiva um processo
     *
     * @param  Processo  $processo
     * @param $motivo
     * @param  ProcessoAndamento|null  $processoAndamento
     *
     * @throws Exception
     */
    public function arquivar(
        Processo $processo,
        $motivo,
        ProcessoAndamento $processoAndamento = null
    ) {
        $defaultSession = DefaultSession::getInstance();
        $data           = $defaultSession->get(DefaultSession::DB_DATAUSU);
        $usuario        = $defaultSession->get(DefaultSession::DB_ID_USUARIO);
        $departamento   = $defaultSession->get(DefaultSession::DB_CODDEPTO);
        $codigoProcesso = $processo->getCodigoProcesso();

        $arquivamento          = new Arquivamento();
        $arquivamentoProcesso  = new ArquivamentoProcesso();
        $arquivamentoAndamento = new ArquivamentoAndamento();

        $arquivamento->setProcesso($codigoProcesso);
        $arquivamento->setData(date('Y-m-d', $data));
        $arquivamento->setHistorico($motivo);
        $arquivamento->setUsuario($usuario);
        $arquivamento->setDepartamento($departamento);

        $this->arquivamentoRepository->persist($arquivamento);

        $arquivamentoProcesso->setArquivamento($arquivamento->getCodigo());
        $arquivamentoProcesso->setProcesso($codigoProcesso);

        $this->arquivamentoProcessoRepository->persist($arquivamentoProcesso);

        if (empty($processoAndamento)) {
            $transferencia     = $this->transferir(
                $processo,
                $departamento,
                $departamento,
                $usuario
            );
            $processoAndamento = $this->receber(
                $processo,
                $transferencia,
                $motivo
            );
        }

        $arquivamentoAndamento->setArquivamento($arquivamento->getCodigo());
        $arquivamentoAndamento->setAndamento($processoAndamento->getCodigo());
        $arquivamentoAndamento->setArquivado(true);

        $this->arquivamentoAndamentoRepository->persist($arquivamentoAndamento);
    }

    /**
     * @param  Processo  $processo
     * @param  Transferencia|null  $transferencia
     *
     * @return ProcessoDocumento
     * @throws \ParameterException
     */
    public function criarCapa(
        Processo $processo,
        Transferencia $transferencia = null
    ) {
        $this->iniciarLaravel();
        db_putsession('DB_itemmenu_acessado', 6865);
        db_putsession('DB_acessado', 6865);
        $nomeArquivo     = "capa_processo_{$processo->getCodigoProcesso()}.pdf";
        $capaDoDocumento = new CapaDoDocumento(
            $processo,
            $transferencia,
            $nomeArquivo
        );
        $caminho         = $capaDoDocumento->saveFile();
        $storageConfig   = StorageHelper::getStorageConfig();
        $allowed         = array();

        if (isset($storageConfig->client_id_ouvidoria)
            && ! empty($storageConfig->client_id_ouvidoria)
        ) {
            $allowed[] = $storageConfig->client_id_ouvidoria;
        }

        $metadata                     = new \stdClass();
        $metadata->tipo_documento     = "processo";
        $metadata->numero_do_processo = $processo->getNumero()."/"
                                        .$processo->getAno();
        $metadata->requerente         = $processo->getRequerente();
        $metadata->data_hora          = $processo->getData()." "
                                        .$processo->getHora();
        $rsProcessoOuvidoria          = db_query(
            "
                    SELECT
                    *
                    FROM
                          processoouvidoria
                    INNER JOIN ouvidoriaatendimento
                    ON processoouvidoria.ov09_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial
                    WHERE
                    ov09_protprocesso = {$processo->getCodigoProcesso()}
         "
        );

        $processoOuvidoria = pg_fetch_object($rsProcessoOuvidoria);

        if (! empty($processoOuvidoria)) {
            $numeroAtendimento            = $processoOuvidoria->ov01_numero;
            $numeroAtendimento            .= "/"
                                             .$processoOuvidoria->ov01_anousu;
            $metadata->numero_atendimento = $numeroAtendimento;
        }

        $metadata->codigo_usuario_aprovacao = $processo->getUsuario();
        $metadata->login_usuario_aprovacao  = DefaultSession::getInstance()
                                                            ->get(
                                                                DefaultSession::DB_LOGIN
                                                            );

        $processoDocumento = new ProcessoDocumento();
        $processoDocumento->setProcesso($processo->getCodigoProcesso());
        $processoDocumento->setDescricao('Capa do Processo');
        $processoDocumento->setData($processo->getData());
        $processoDocumento->setUsuario(
            DefaultSession::getInstance()->get(DefaultSession::DB_ID_USUARIO)
        );
        $processoDocumento->setStorage(true);
        $processoDocumento->setOrdem(1);
        $processoDocumento->setNomeDocumento(
            'Capa do Processo.pdf'
        );
        $processoDocumento->setDocumento(
            StorageHelper::uploadArquivo($caminho, $allowed, true, $metadata)
        );

        return $this->salvarDocumento($processoDocumento);
    }

    private function iniciarLaravel()
    {
        $app = require_once ECIDADE_PATH.'bootstrap/app.php';
        // App já iniciado
        if ($app === true) {
            return;
        }
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
    }

    public function buscarProcessos($perPage, $request)
    {
        $idUser = db_getsession('DB_id_usuario');

        $filtro = "";
        if ($request->get('codProcesso') !== null && $request->get('codProcesso') !== "") {
            $filtro = $filtro . " AND p58_codproc = {$request->get('codProcesso')}";
        }

        if ($request->get('processo') !== null && $request->get('processo') !== "") {
            $filtro = $filtro . " AND (p58_numero || '/' || p58_ano) ilike "."'%{$request->get('processo')}%'";
        }

        if ($request->get('requerente') !== null && $request->get('requerente') !== "") {
            $filtro = $filtro . " AND p58_requer ilike "."'%{$request->get('requerente')}%'";
        }

        if ($request->get('descricao') !== null && $request->get('descricao') !== "") {
            $filtro = $filtro . " AND p51_descr ilike "."'%{$request->get('descricao')}%'";
        }

        $filtroDataQuery1 = "";
        $filtroDataQuery2 = "";

        if ($request->get('tipoData') !== null &&
            $request->get('tipoData') !== "" &&
            $request->get('tipoData') === 'inclusao'
        ) {
            $filtro = $filtro . " AND p58_dtproc >= '{$request->get('dataDe')}'
            and  p58_dtproc <= '{$request->get('dataAte')}'";
        } else {
            if (!empty($request->get('dataDe')) &&  !empty($request->get('dataAte'))) {
                $filtroDataQuery1 = " AND p62_dttran >= '{$request->get('dataDe')}'
            and  p62_dttran <= '{$request->get('dataAte')}'";
                $filtroDataQuery2 = " AND p88_data >= '{$request->get('dataDe')}'
            and  p88_data <= '{$request->get('dataAte')}'";
            }
        }


        if ($request->get('checkboxFiltroTipoProcesso') !== null &&
            $request->get('checkboxFiltroTipoProcesso')!== ""
        ) {
            $checkboxFiltroTipoProcesso = explode(",", $request->get('checkboxFiltroTipoProcesso'));
            $whereTipoProcesso = '';
            if (in_array('interno', $checkboxFiltroTipoProcesso)) {
                $internos = "(p58_tipoprocesso = 2 AND ov09_ouvidoriaatendimento isnull)";
                if ($whereTipoProcesso === '') {
                    $whereTipoProcesso = " AND(".$internos;
                }
            }
            if (in_array('externo', $checkboxFiltroTipoProcesso)) {
                $externos = "(p58_tipoprocesso = 2 AND ov09_ouvidoriaatendimento notnull)";
                if ($whereTipoProcesso === '') {
                    $whereTipoProcesso = " AND(".$externos;
                } else {
                    $whereTipoProcesso = $whereTipoProcesso." OR ".$externos;
                }
            }
            if (in_array('manual', $checkboxFiltroTipoProcesso)) {
                $manual = "(p58_tipoprocesso = 1)";
                if ($whereTipoProcesso === '') {
                    $whereTipoProcesso = " AND(".$manual;
                } else {
                    $whereTipoProcesso = $whereTipoProcesso." OR ".$manual;
                }
            }
            $whereTipoProcesso = $whereTipoProcesso.")";
            $filtro = $filtro . $whereTipoProcesso;
        }

        $filtroResponsavelQuery1 = "";
        $filtroResponsavelQuery2 = "";

        if ($request->get('checkboxFiltroResponsavel') !== null &&
            $request->get('checkboxFiltroResponsavel') !== "" &&
            $request->get('checkboxFiltroResponsavel') !== "undefined"
        ) {
            $checkboxFiltroResponsavel = explode(",", $request->get('checkboxFiltroResponsavel'));
            if (in_array('meu', $checkboxFiltroResponsavel)) {
                $filtroResponsavelQuery1 = " ((CASE p62_id_usorec WHEN {$idUser} THEN 1 WHEN 0 THEN 0 ELSE 2 END) = 1)";
                $filtroResponsavelQuery2 = " ((CASE p89_usuario WHEN {$idUser} THEN 1 WHEN 0 THEN 0 ELSE 2 END) = 1)";
            }

            if (in_array('outros', $checkboxFiltroResponsavel)) {
                if (($filtroResponsavelQuery1 !== "") && ($filtroResponsavelQuery2  !== "")) {
                    $filtroResponsavelQuery1 =
                        $filtroResponsavelQuery1.
                        " OR ((CASE p62_id_usorec WHEN {$idUser} THEN 1 WHEN 0 THEN 0 ELSE 2 END)=2)";
                    $filtroResponsavelQuery2 =
                        $filtroResponsavelQuery2.
                        " OR ((CASE p89_usuario WHEN {$idUser} THEN 1 WHEN 0 THEN 0 ELSE 2 END) = 2)";
                } else {
                    $filtroResponsavelQuery1 =
                        " ((CASE p62_id_usorec WHEN {$idUser} THEN 1 WHEN 0 THEN 0 ELSE 2 END) = 2)";
                    $filtroResponsavelQuery2 =
                        " ((CASE p89_usuario WHEN {$idUser} THEN 1 WHEN 0 THEN 0 ELSE 2 END) = 2)";
                }
            }

            if (in_array('departamento', $checkboxFiltroResponsavel)) {
                if (($filtroResponsavelQuery1 !== "") && ($filtroResponsavelQuery2 !== "")) {
                    $filtroResponsavelQuery1=
                        $filtroResponsavelQuery1.
                        " OR ((CASE p62_id_usorec WHEN {$idUser} THEN 1 WHEN 0 THEN 0 ELSE 2 END)=0)";
                    $filtroResponsavelQuery2=
                        $filtroResponsavelQuery2.
                        " OR ((CASE p89_usuario WHEN {$idUser} THEN 1 WHEN 0 THEN 0 ELSE 2 END) = 0)";
                } else {
                    $filtroResponsavelQuery1 =
                        " ((CASE p62_id_usorec WHEN {$idUser} THEN 1 WHEN 0 THEN 0 ELSE 2 END) = 0)";
                    $filtroResponsavelQuery2 =
                        " ((CASE p89_usuario WHEN {$idUser} THEN 1 WHEN 0 THEN 0 ELSE 2 END) = 0)";
                }
            }

            $filtroResponsavelQuery1 = "AND (". $filtroResponsavelQuery1. ")";
            $filtroResponsavelQuery2 = "AND (". $filtroResponsavelQuery2. ")";
        }


        $filtroCheckBox = null;
        if ($request->get('filtrosCheckBox') !== null &&
            $request->get('filtrosCheckBox') !== "undefined" &&
            $request->get('filtrosCheckBox') !== ""
        ) {
            //$filtroCheckBox = $this->setFiltroCheckBox($request->get('filtrosCheckBox'));
            $filtroCheckBox = $request->get('filtrosCheckBox');
        }

        $sql = $this->queryProcesso(
            $filtro,
            $filtroDataQuery1,
            $filtroDataQuery2,
            $filtroResponsavelQuery1,
            $filtroResponsavelQuery2,
            $filtroCheckBox
        );


        $results = DB::select($sql);
        $results = $this->prepararProcesso($results);

        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            array_slice(
                $results,
                ($page - 1) * $perPage,
                $perPage
            ),
            count($results),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    public function querybuscarProcessosAReceber($filtro, $filtroDataP62, $filtroResponsavelQueryP62)
    {
        $idUser = db_getsession('DB_id_usuario');
        $codDept = db_getsession('DB_coddepto');

        $departamento = \DBDepartamentoRepository::getPorCodigo(db_getsession('DB_coddepto'));
        $whereRestringeVisualizarReceberP62 = " AND ( p62_id_usorec = {$idUser} OR p62_id_usorec = 0 )";

        if ($departamento->getCodigoResponsavel() == $idUser
            or
            UsuarioRepository::hasPermission(
                $idUser,
                MenuPermissionRepository::PERMISSION_RECEBER_PROCESSOS_TERCEIROS
            )
        ) {
            $whereRestringeVisualizarReceberP62 = "";
        }

        return "(SELECT DISTINCT
    p58_codproc AS codigo,
    p51_codigo AS codigoTipoProcesso,
    z01_numcgm AS numcgm,
    z01_nome AS titular,
    p58_codandam AS codigoAndamento,
    nomeinst AS instituicao,
    descrdepto AS departamento,
    p62_codtran AS transferencia,
    p58_numero || '/' || p58_ano AS processo,
    p58_requer AS requerente,
    p58_tipoprocesso AS tipoProcesso,
    p51_descr AS descricao,
    TO_CHAR(p62_dttran, 'dd/mm/YYYY') AS data,
    p58_dtproc as dataProcesso,
    p58_obs AS observacao,
    (
        SELECT COUNT(p123_id)
        FROM protocolo.mensageria
        WHERE p123_processo = p58_codproc
        AND p123_data_visualizada IS NULL
        AND p123_interna = false
    ) AS mensagens_novas,
    1 AS codigoStatus,
    p58_codproc || '/' || p58_ano AS codigoProcesso,
    CASE p62_id_usorec
        WHEN {$idUser} THEN 1
        WHEN 0 THEN 0
        ELSE 2
    END AS parausuariologado,
    false AS processoInterno,
    CASE
        WHEN p58_tipoprocesso = 2 THEN 1
        ELSE 0
    END AS flag_processo_eletronico,
    (
        SELECT
            SUM(CASE
                WHEN p113_data_registro IS NULL THEN 1
                ELSE 0
            END) AS mensagens_nao_lidas
        FROM
            procandam
        INNER JOIN processosvinculados ON processosvinculados.p92_processofilho = procandam.p61_codproc
        INNER JOIN procandamint ON procandamint.p78_codandam = procandam.p61_codandam
        LEFT JOIN historicovisualizacaoprocandam ON
        historicovisualizacaoprocandam.p113_procandamint_id = procandamint.p78_sequencial
        WHERE
            p92_processopai = p58_codproc
    )  AS mensagens_nao_lidas,
    ov09_ouvidoriaatendimento
    FROM
    protprocesso
    INNER JOIN cgm ON cgm.z01_numcgm = protprocesso.p58_numcgm
    INNER JOIN db_config ON codigo = p58_instit
    INNER JOIN configuracoes.db_depart ON db_depart.coddepto = protprocesso.p58_coddepto
    INNER JOIN proctransferproc ON p58_codproc = p63_codproc
    INNER JOIN proctransfer ON p62_codtran = p63_codtran
    INNER JOIN tipoproc ON p58_codigo = p51_codigo
    LEFT JOIN arqproc ON p58_codproc = p68_codproc
    LEFT JOIN processoouvidoria ON ov09_protprocesso = protprocesso.p58_codproc
    LEFT JOIN protocolo.mensageria ON protocolo.mensageria.p123_processo = protocolo.protprocesso.p58_codproc
    WHERE
    p68_codproc IS NULL
    AND p62_coddeptorec = {$codDept}
    AND NOT EXISTS ( SELECT 1 FROM proctransand WHERE p64_codtran = p62_codtran )
    AND p51_prottipodocumentoprocesso NOT IN (6, 7)
    AND NOT EXISTS (
        SELECT 1 FROM processosapensados
          WHERE processosapensados.p30_procapensado = protprocesso.p58_codproc
    )
            {$whereRestringeVisualizarReceberP62} {$filtro} {$filtroDataP62} {$filtroResponsavelQueryP62}
        ORDER BY p62_codtran DESC)";
    }

    public function buscarProcessosAReceberInternos($filtro, $filtroDataP88, $filtroResponsavelQueryP89)
    {
        $idUser = db_getsession('DB_id_usuario');
        $codDept = db_getsession('DB_coddepto');

        $departamento = \DBDepartamentoRepository::getPorCodigo(db_getsession('DB_coddepto'));
        $whereRestringeVisualizarReceberP89 = " AND (p89_usuario = {$idUser})";

        if ($departamento->getCodigoResponsavel() == $idUser
            or
            UsuarioRepository::hasPermission(
                $idUser,
                MenuPermissionRepository::PERMISSION_RECEBER_PROCESSOS_TERCEIROS
            )
        ) {
            $whereRestringeVisualizarReceberP89 = "";
        }

        return "(SELECT DISTINCT
    p58_codproc AS codigo,
    p51_codigo AS codigoTipoProcesso,
    z01_numcgm AS numcgm,
    z01_nome AS titular,
    p58_codandam AS codigoAndamento,
    nomeinst AS instituicao,
    descrdepto AS departamento,
    p88_codigo AS transferencia,
    p58_numero || '/' || p58_ano AS processo,
    p58_requer AS requerente,
    p58_tipoprocesso AS tipoProcesso,
    p51_descr AS descricao,
    TO_CHAR(p88_data, 'dd/mm/YYYY') AS data,
    p58_dtproc as dataProcesso,
    p58_obs AS observacao,
    (
        SELECT COUNT(p123_id)
        FROM protocolo.mensageria
        WHERE p123_processo = p58_codproc
        AND p123_data_visualizada IS NULL
        AND p123_interna = false
    ) AS mensagens_novas,
    1 AS codigoStatus,
    p58_codproc || '/' || p58_ano AS codigoProcesso,
    CASE p89_usuario
        WHEN {$idUser} THEN 1
        WHEN 0 THEN 0
        ELSE 2
    END AS parausuariologado,
    true AS processoInterno,
    CASE
        WHEN p58_tipoprocesso = 2  THEN 1
        ELSE 0
    END AS flag_processo_eletronico,
    (
        SELECT
            SUM(CASE  WHEN p113_data_registro IS NULL THEN 1 ELSE 0 END) AS mensagens_nao_lidas
        FROM
            procandam
        INNER JOIN processosvinculados ON
            processosvinculados.p92_processofilho = procandam.p61_codproc
        INNER JOIN procandamint ON
            procandamint.p78_codandam = procandam.p61_codandam
        LEFT JOIN historicovisualizacaoprocandam
            ON historicovisualizacaoprocandam.p113_procandamint_id = procandamint.p78_sequencial
        WHERE
            p92_processopai = p58_codproc
    ) AS mensagens_nao_lidas,
    ov09_ouvidoriaatendimento
FROM
    protprocesso
INNER JOIN cgm ON cgm.z01_numcgm = protprocesso.p58_numcgm
INNER JOIN db_config ON codigo = p58_instit
INNER JOIN configuracoes.db_depart ON db_depart.coddepto = protprocesso.p58_coddepto
INNER JOIN proctransferintand ON p58_codandam = p87_codandam
INNER JOIN proctransferint ON p88_codigo = p87_codtransferint
INNER JOIN proctransferintusu ON p89_codtransferint = p88_codigo
INNER JOIN tipoproc ON p58_codigo = p51_codigo
INNER JOIN procandam ON p61_codandam = p87_codandam
LEFT JOIN arqproc ON p58_codproc = p68_codproc
LEFT JOIN processoouvidoria ON ov09_protprocesso = protprocesso.p58_codproc
LEFT JOIN protocolo.mensageria ON protocolo.mensageria.p123_processo = protocolo.protprocesso.p58_codproc
WHERE
    p68_codproc IS NULL
    AND p61_coddepto = {$codDept}
    AND NOT EXISTS(SELECT 1 FROM procandamintand WHERE p86_codtrans = p88_codigo)
    AND NOT EXISTS (
        SELECT 1 FROM processosapensados
        WHERE processosapensados.p30_procapensado = protprocesso.p58_codproc
    )
    AND p51_prottipodocumentoprocesso NOT IN (6, 7)
            {$whereRestringeVisualizarReceberP89} {$filtro} {$filtroDataP88} {$filtroResponsavelQueryP89}
        ORDER BY
            p88_codigo DESC)";
    }

    public function buscarProcessosRecebidos($filtro, $filtroDataP62, $filtroResponsavelQueryP62)
    {
        $idUser = db_getsession('DB_id_usuario');
        $codDept = db_getsession('DB_coddepto');

        //$aux = "select count(p78_codandam) from procandamint where p78_codandam = p58_codandam limit 1";

        return "(SELECT DISTINCT
            p58_codproc AS codigo,
            p51_codigo AS codigoTipoProcesso,
            z01_numcgm AS numcgm,
            z01_nome AS titular,
            p58_codandam AS codigoAndamento,
            nomeinst AS instituicao,
            descrdepto AS departamento,
            p62_codtran AS transferencia,
            p58_numero || '/' || p58_ano AS processo,
            p58_requer AS requerente,
            p58_tipoprocesso AS tipoProcesso,
            p51_descr AS descricao,
            TO_CHAR(p62_dttran, 'dd/mm/YYYY') AS data,
            p58_dtproc as dataProcesso,
            p58_obs AS observacao,
            (
                SELECT COUNT(p123_id)
                FROM protocolo.mensageria
                WHERE p123_processo = p58_codproc
                AND p123_data_visualizada IS NULL
                AND p123_interna = false
            ) AS mensagens_novas,
            CASE
                WHEN (
                    SELECT
                        count(p78_codandam)
                    FROM
                        procandamint
                    WHERE
                        p78_codandam = p58_codandam
                    LIMIT 1
                ) > 0 THEN 3
                ELSE 2
            END as codigoStatus,
            p58_codproc || '/' || p58_ano as codigoProcesso,
            CASE p62_id_usorec
                WHEN {$idUser} THEN 1
                WHEN 0 THEN 0
                ELSE 2
            END as parausuariologado,
            false as processoInterno,
            CASE
                WHEN p58_tipoprocesso = 2 THEN 1
                ELSE 0
            END flag_processo_eletronico,
            (
                SELECT
                    SUM(CASE
                        WHEN p113_data_registro IS NULL THEN 1
                        ELSE 0
                    END) AS mensagens_nao_lidas
                FROM
                    procandam
                    INNER JOIN processosvinculados ON processosvinculados.p92_processofilho = procandam.p61_codproc
                    INNER JOIN procandamint ON procandamint.p78_codandam = procandam.p61_codandam
                    LEFT JOIN historicovisualizacaoprocandam ON
                    historicovisualizacaoprocandam.p113_procandamint_id = procandamint.p78_sequencial
                WHERE
                    p92_processopai = p58_codproc
            )  AS mensagens_nao_lidas,
            ov09_ouvidoriaatendimento
        FROM
            protprocesso
            INNER JOIN cgm ON cgm.z01_numcgm = protprocesso.p58_numcgm
            INNER JOIN db_config ON codigo = p58_instit
            INNER JOIN configuracoes.db_depart ON db_depart.coddepto = protprocesso.p58_coddepto
            INNER JOIN procandam ON p58_codandam = p61_codandam
            INNER JOIN proctransfer ON p62_codtran = (
                SELECT
                    p63_codtran
                FROM
                    proctransferproc
                WHERE
                    p63_codproc = p58_codproc
                ORDER BY
                    p63_codtran DESC
                LIMIT 1
            )
            INNER JOIN tipoproc ON p58_codigo = p51_codigo
            INNER JOIN proctransand ON p64_codandam = p58_codandam
            LEFT JOIN arqproc ON arqproc.p68_codproc = protprocesso.p58_codproc
            LEFT JOIN processoouvidoria ON ov09_protprocesso = protprocesso.p58_codproc
            LEFT JOIN protocolo.mensageria ON protocolo.mensageria.p123_processo = protocolo.protprocesso.p58_codproc
        WHERE
            p68_codproc IS NULL
            AND p61_coddepto = {$codDept}
            AND NOT EXISTS(
                SELECT
                    p62_codtran AS codtran
                FROM
                    proctransfer
                    LEFT JOIN proctransand ON p64_codtran = p62_codtran
                    LEFT JOIN proctransferproc ON p63_codtran = p62_codtran
                WHERE
                    p64_codtran IS NULL AND p63_codproc = p58_codproc
            )
            AND NOT EXISTS (
                SELECT
                    1
                FROM
                    processosapensados
                WHERE
                    processosapensados.p30_procapensado = protprocesso.p58_codproc
            )
            AND p51_prottipodocumentoprocesso NOT IN (6, 7) {$filtro} {$filtroDataP62} {$filtroResponsavelQueryP62}
        ORDER BY
            p62_codtran DESC)";
    }

    public function buscarProcessosRecebidosInterno($filtro, $filtroDataP88, $filtroResponsavelQueryP89)
    {
        $idUser = db_getsession('DB_id_usuario');
        $codDept = db_getsession('DB_coddepto');

        $departamento = \DBDepartamentoRepository::getPorCodigo(db_getsession('DB_coddepto'));
        $whereRestringeVisualizarReceberP89 = "AND (p89_usuario = {$idUser})";

        if ($departamento->getCodigoResponsavel() == $idUser
            or
            UsuarioRepository::hasPermission(
                $idUser,
                MenuPermissionRepository::PERMISSION_RECEBER_PROCESSOS_TERCEIROS
            )
        ) {
            $whereRestringeVisualizarReceberP89 = "";
        }

        return "(SELECT DISTINCT
    p58_codproc AS codigo,
    p51_codigo AS codigoTipoProcesso,
    z01_numcgm AS numcgm,
    z01_nome AS titular,
    p58_codandam AS codigoAndamento,
    nomeinst AS instituicao,
    descrdepto AS departamento,
    p88_codigo AS transferencia,
    p58_numero || '/' || p58_ano AS processo,
    p58_requer AS requerente,
    p58_tipoprocesso AS tipoProcesso,
    p51_descr AS descricao,
    TO_CHAR(p88_data, 'dd/mm/YYYY') AS data,
    p58_dtproc as dataProcesso,
    p58_obs AS observacao,
    (
        SELECT COUNT(p123_id)
        FROM protocolo.mensageria
        WHERE p123_processo = p58_codproc
        AND p123_data_visualizada IS NULL
        AND p123_interna = false
    ) AS mensagens_novas,
    CASE
        WHEN (
            SELECT COUNT(p78_sequencial)
            FROM procandamint
            WHERE p78_codandam = p58_codandam
            AND p78_transint IS false
            LIMIT 1
        ) > 0 THEN 3
        ELSE 2
    END AS codigoStatus,
    p58_codproc || '/' || p58_ano AS codigoProcesso,
    CASE p89_usuario
        WHEN {$idUser} THEN 1
        WHEN 0 THEN 0
        ELSE 2
    END AS parausuariologado,
    true AS processoInterno,
    CASE
        WHEN p58_tipoprocesso = 2 THEN 1
        ELSE 0
    END AS flag_processo_eletronico,
    (
        SELECT
            SUM(
                CASE
                    WHEN p113_data_registro IS NULL THEN 1
                    ELSE 0
                END
            ) AS mensagens_nao_lidas
        FROM
            procandam
        INNER JOIN processosvinculados ON processosvinculados.p92_processofilho = procandam.p61_codproc
        INNER JOIN procandamint ON procandamint.p78_codandam = procandam.p61_codandam
        LEFT JOIN historicovisualizacaoprocandam ON
        historicovisualizacaoprocandam.p113_procandamint_id = procandamint.p78_sequencial
        WHERE
            p92_processopai = p58_codproc
    ) AS mensagens_nao_lidas,
    ov09_ouvidoriaatendimento
FROM
    protprocesso
INNER JOIN cgm ON cgm.z01_numcgm = protprocesso.p58_numcgm
INNER JOIN db_config ON codigo = p58_instit
INNER JOIN configuracoes.db_depart ON db_depart.coddepto = protprocesso.p58_coddepto
INNER JOIN proctransferintand ON p58_codandam = p87_codandam
INNER JOIN proctransferint ON p88_codigo = p87_codtransferint
INNER JOIN proctransferintusu ON p89_codtransferint = p88_codigo
INNER JOIN tipoproc ON p58_codigo = p51_codigo
INNER JOIN procandam ON p61_codandam = p87_codandam
LEFT JOIN arqproc ON p58_codproc = p68_codproc
LEFT JOIN processoouvidoria ON ov09_protprocesso = protprocesso.p58_codproc
LEFT JOIN protocolo.mensageria ON protocolo.mensageria.p123_processo = protocolo.protprocesso.p58_codproc
WHERE
    p68_codproc IS NULL
    AND p61_coddepto = {$codDept}
    AND EXISTS(
        SELECT 1
        FROM procandamintand
        WHERE p86_codtrans = p88_codigo
    )
    AND NOT EXISTS (
        SELECT 1
        FROM processosapensados
        WHERE processosapensados.p30_procapensado = protprocesso.p58_codproc
    )
    AND p51_prottipodocumentoprocesso NOT IN (6, 7)
    {$whereRestringeVisualizarReceberP89} {$filtro} {$filtroDataP88} {$filtroResponsavelQueryP89}
ORDER BY
    p88_codigo)";
    }

    public function queryProcesso(
        $filtro,
        $filtroDataP62 = "",
        $filtroDataP88 = "",
        $filtroResponsavelQueryP62 = "",
        $filtroResponsavelQueryP89 = "",
        $filtrosCheckBox = null
    ) {
        $where = "";
        $filtrosCheckBoxArray = explode(",", $filtrosCheckBox);
        $filtrosCheckBoxArray = array_map('intval', $filtrosCheckBoxArray);

        if ($filtrosCheckBox !== null) {
            if (count($filtrosCheckBoxArray) === 1 && in_array(1, $filtrosCheckBoxArray)) {
                $sqlFinal = $this->querybuscarProcessosAReceber($filtro, $filtroDataP62, $filtroResponsavelQueryP62) .
                    " UNION ALL " .
                    $this->buscarProcessosAReceberInternos($filtro, $filtroDataP88, $filtroResponsavelQueryP89) .
                    " ORDER BY transferencia DESC";

                return $sqlFinal;
            } else {
                $filtrosCheckBoxArray = array_filter($filtrosCheckBoxArray, function ($value) {
                    return $value !== 4;
                });
                $filtrosCheckBoxArray = array_values($filtrosCheckBoxArray);

                if (count($filtrosCheckBoxArray) > 0) {
                    $inTipos = implode(",", $filtrosCheckBoxArray);
                    $where = " WHERE codigostatus IN ({$inTipos}) ";
                }
            }

            $filtrosCheckBoxArray = explode(",", $filtrosCheckBox);
            $filtrosCheckBoxArray = array_map('intval', $filtrosCheckBoxArray);

            if ($where === "" && in_array(4, $filtrosCheckBoxArray)) {
                $where = " WHERE mensagens_novas > 0 ";
            } else {
                if ($where != "" && in_array(4, $filtrosCheckBoxArray)) {
                    $where = $where. " AND mensagens_novas > 0 ";
                }
            }
        }

        $sqlFinal = "SELECT * FROM (" .
            $this->querybuscarProcessosAReceber($filtro, $filtroDataP62, $filtroResponsavelQueryP62) . " UNION ALL " .
            $this->buscarProcessosAReceberInternos($filtro, $filtroDataP88, $filtroResponsavelQueryP89) .
            " UNION ALL " .
            $this->buscarProcessosRecebidos($filtro, $filtroDataP62, $filtroResponsavelQueryP62) . " UNION ALL " .
            $this->buscarProcessosRecebidosInterno($filtro, $filtroDataP88, $filtroResponsavelQueryP89) .
            " ) AS  view_processo {$where} ORDER BY transferencia DESC";

        return $sqlFinal;
    }

    public function prepararProcesso($processos)
    {
        foreach ($processos as &$processo) {
            $processo->hash = md5(serialize($processo));
            $processo->isProcessoRedesim = $this->andamentoProcesoService->isProcessoRedesim($processo);
            $processo->isProcessoRedesimInclusaoInscricao = $this
                ->andamentoProcesoService
                ->isProcessoRedesimInclusaoInscricao($processo);
        }

        return $processos;
    }

    public function apenasReceber($hash, $codigoProcesso, $codigoTransferencia)
    {
        $validacao = $this->validarStatusProcesso($hash, $codigoProcesso);
        if ($validacao->houveAlteracao) {
            return $validacao;
        }

        return $this->receberProcesso(intval($codigoTransferencia));
    }

    public function validarStatusProcesso($hash, $codProcesso)
    {
        $resposta = (object)array(
            'houveAlteracao' => false,
            'processo' => null,
            'mensagem' => null
        );

        $where = "AND (p58_codproc = {$codProcesso})";
        $processos = $this->prepararProcesso(
            DB::select(
                $this->queryProcesso($where)
            )
        );

        if (count($processos) == 0) {
            $resposta->mensagem = 'O processo acessado foi transferido por outro usuário, ';
            $resposta->mensagem .= 'ele não está mais presente neste departamento.';
            return $resposta;
        }

//        $processoAtual = array_pop($processos);
//
//        if ($hash != $processoAtual->hash) {
//            $resposta->mensagem = 'O processo acessado foi alterado por outro usuário, ';
//            $resposta->mensagem .= 'verifique o estado atual do processo com as informações atualizadas.';
//            $resposta->processo = $processoAtual;
//            $resposta->houveAlteracao = true;
//        }

        return $resposta;
    }

    /**
     * @throws \ParameterException
     */
    public function receberProcesso($codigoTransferencia)
    {
        $daoAndamento = new \cl_procandam();
        $daoProcesso = new \cl_protprocesso();
        $daoTransicao = new \cl_proctransand();

        if (empty($codigoTransferencia)) {
            throw new \ParameterException(
                'É necessário informar o código da transferência para realizar o recebimento.'
            );
        }

        $processos = TransferenciaRepositorio::buscaProcessosPorTransferencia($codigoTransferencia);

        if (empty($processos)) {
            return false;
        }

        foreach ($processos as $processo) {
            $codProcesso = $processo->getCodProcesso();
            $sqlProcesso = $daoProcesso->sql_query_file($codProcesso);
            $rsProcesso = db_query($sqlProcesso);

            if (!$rsProcesso) {
                throw new \Exception(
                    "Não foi possível buscar o andamento do processo {$codProcesso}.\nContate o suporte."
                );
            }

            if (pg_num_rows($rsProcesso) == 0) {
                throw new \Exception(
                    "Não foi possível buscar o andamento do processo {$codProcesso}.\nContate o suporte."
                );
            }

            $statusProcesso = pg_fetch_object($rsProcesso, 0);

            $sqlAndamento = $daoAndamento->sql_query_file(null, "*", null, "p61_codproc={$codProcesso} limit 1");
            $rsAndamento = db_query($sqlAndamento);

            if (!$rsAndamento) {
                throw new \Exception(
                    "Não foi possível buscar o andamento do processo {$codProcesso}.\nContate o suporte."
                );
            }

            $textoDespacho = '';
            if (pg_num_rows($rsAndamento) == 0) {
                $textoDespacho = 'Tramite Inicial';
            }

            $daoAndamento->p61_despacho = $textoDespacho;
            $daoAndamento->p61_publico = ($statusProcesso->p58_publico == 'f' ? 'false' : 'true');
            $daoAndamento->p61_codproc = $codProcesso;
            $daoAndamento->p61_dtandam = date('Y-m-d');
            $daoAndamento->p61_hora = db_hora();
            $daoAndamento->p61_id_usuario = db_getsession("DB_id_usuario");
            $daoAndamento->p61_coddepto = db_getsession("DB_coddepto");
            $daoAndamento->incluir(null);

            if ($daoAndamento->erro_status == "0") {
                $mensagem = "Não foi possível realizar o recebimento da transferência ";
                $mensagem .= "{$codigoTransferencia}.\nContate o suporte.";
                throw new \Exception($mensagem);
            }

            $daoTransicao->p64_codtran = $codigoTransferencia;
            $daoTransicao->p64_codandam = $daoAndamento->p61_codandam;
            $daoTransicao->incluir(null);

            if ($daoTransicao->erro_status == "0") {
                $mensagem = "Não foi possível realizar o recebimento da transferência ";
                $mensagem .= "{$codigoTransferencia}.\nContate o suporte.";
                throw new \Exception($mensagem);
            }

            $daoProcesso->p58_codproc = $processo->getCodProcesso();
            $daoProcesso->p58_codandam = $daoAndamento->p61_codandam;
            $daoProcesso->p58_despacho = " ";
            $daoProcesso->p58_instit = db_getsession("DB_instit");
            $daoProcesso->alterar($processo->getCodProcesso());
        }

        return true;
    }

    public function receberMassa($processos)
    {
        $processosSuccess = array();
        $processosError = array();
        foreach ($processos as $processo) {
            try {
                $this->apenasReceber(
                    $processo["hash"],
                    $processo["codigoProcesso"],
                    $processo["codigoTransferencia"]
                );

                $processosSuccess[] = $processo;
            } catch (\Exception $e) {
                $processoErro = new \stdClass();
                $processoErro->processo = $processo;
                $processoErro->mensagem = $e->getMessage();

                $processosError[] = $processoErro;
                Log::error($e->getMessage(), $e->getTrace());
            }
        }

        $result = new \stdClass();
        $result->aprovados = $processosSuccess;
        $result->erros = $processosError;

        return $result;
    }
}
