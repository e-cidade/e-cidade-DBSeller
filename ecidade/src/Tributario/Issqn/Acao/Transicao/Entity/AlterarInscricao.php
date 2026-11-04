<?php

namespace ECidade\Tributario\Issqn\Acao\Transicao\Entity;

use App\Domain\Configuracao\Helpers\StorageHelper;
use cl_isscadsimples;
use DateTime;
use ECidade\Configuracao\Workflow\Interfaces\Acao as AcaoInterface;
use ECidade\Lib\Session\DefaultSession;
use ECidade\Tributario\Issqn\Enum\IssCategoriaEnum;
use ECidade\Tributario\Issqn\Model\IssCadastroSimples;
use ECidade\Tributario\Issqn\Repository\IssbaseRepository;
use ECidade\Tributario\Issqn\Inscricao\Service\AlvaraOnline;
use ECidade\Patrimonial\Protocolo\Servicos\InclusaoCgmLegacy;
use ECidade\Tributario\Issqn\ParametrosProcessoEletronicoBag;
use \ECidade\Tributario\Issqn\Repository\ProcessoEletronicoGrauRiscoRepository;
use ECidade\Patrimonial\Protocolo\Processo\ProcessoEletronico\Helper\ProcessoEletronicoHelper;
use ECidade\Tributario\Issqn\Inscricao\Atividades\Filter\ListagemAtividades as FiltroListagemAtividades;
use ECidade\Patrimonial\Protocolo\Processo\ProcessoEletronico\Filter\ListagemProcessos as FiltroListagemProcessos;
use ECidade\Tributario\Issqn\Repository\IssCadastroSimplesRepository;
use Exception;
use \DBDate;
use ECidade\Tributario\Issqn\Acao\Transicao\Entity\InscricaoInterface;
use Illuminate\Support\Facades\Log;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoDocumento;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoDocumentoRepository;

final class AlterarInscricao extends AcaoBase implements AcaoInterface, InscricaoInterface
{
    const SOCIO_RESPONSAVEL_MEI = 2;

    private $serviceProcessosAlvaraOnline;
    private $filtroProcesso;
    private $filtroAtividades;
    private $dados;
    private $cgms = array();
    private $clissbase;
    private $acao;
    private $grauRisco;
    private $camposAdicionais;
    private $inscricao;
    private $caminhoArquivoBIC;


    /**
     * AlterarInscricao constructor.
     * @param $processo
     * @param IssbaseRepository $issbaseRepository
     * @param AlvaraOnline $serviceProcessosAlvaraOnline
     * @param InclusaoCgmLegacy $inclusaoCgmService
     * @param ParametrosProcessoEletronicoBag $parameterBag
     * @param ProcessoEletronicoGrauRiscoRepository $processoEletronicoGrauRiscoRepository
     * @param FiltroListagemProcessos $filtroProcesso
     * @param FiltroListagemAtividades $filtroAtividades
     */
    public function __construct(
        $processo,
        IssbaseRepository $issbaseRepository,
        AlvaraOnline $serviceProcessosAlvaraOnline,
        InclusaoCgmLegacy $inclusaoCgmService,
        ParametrosProcessoEletronicoBag $parameterBag,
        ProcessoEletronicoGrauRiscoRepository $processoEletronicoGrauRiscoRepository,
        FiltroListagemProcessos $filtroProcesso,
        FiltroListagemAtividades $filtroAtividades
    ) {
        parent::__construct($processo, $issbaseRepository);
        $this->serviceProcessosAlvaraOnline = $serviceProcessosAlvaraOnline;
        $this->inclusaoCgmService  = $inclusaoCgmService;
        $this->parameterBag = $parameterBag;
        $this->processoEletronicoGrauRiscoRepository = $processoEletronicoGrauRiscoRepository;
        $this->filtroProcesso = $filtroProcesso;
        $this->filtroAtividades = $filtroAtividades;
    }


    /**
     * @param $inscricao
     * @return $this
     */
    public function setInscricao($inscricao)
    {
        $this->inscricao = $inscricao;
        return $this;
    }

    /**
     * @param mixed $camposAdicionais
     * @return AlterarInscricao
     */
    public function setCamposAdicionais($camposAdicionais)
    {
        $this->camposAdicionais = $camposAdicionais;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCaminhoArquivoBIC()
    {
        return $this->caminhoArquivoBIC;
    }

    public function validate()
    {
        $this->carregaAcao();
        $this->carregaGrauRisco();
        $this->buscarDadosByProcesso();
        $this->buscarCgmsByDados();
        if (empty($this->inscricao)) {
            throw new \BusinessException("Inscriçãoo não encontrada!");
        } elseif (empty($this->dados)) {
            throw new \BusinessException("Não há dados vinculados ao processo!");
        } elseif (empty($this->cgms)) {
            throw new \BusinessException("Cgms não criados!");
        }
    }

    public function run()
    {
        $this->salvarArquivoBIC();
        $this->alterarCGM();
        $this->alterarInscricao();
        $this->incluirSocios();
        $this->incluirAtividades();
        $this->alterarAlvara();
        $this->inserirDocumentos();
        $this->salvarDocumentoNoStorage();
    }

    private function buscarDadosByProcesso()
    {
        $this->filtroProcesso->setNumeroProcesso(null);
        $this->filtroProcesso->setAnoProcesso(null);
        $this->filtroProcesso->setCodigoProcessoProtocolo($this->processo->getCodProcesso());
        $this->filtroProcesso->setCodigoTipoProcesso($this->processo->getTipoProcesso());

        $this->dados = json_decode($this->serviceProcessosAlvaraOnline->retornarProcessoAlvara(
            $this->filtroProcesso,
            $this->filtroAtividades
        ));
    }

    private function buscarCgmsByDados()
    {
        $this->cgms = ProcessoEletronicoHelper::processaCgmsByDados(
            $this->inclusaoCgmService,
            $this->dados,
            $this->acao
        );
    }

    private function carregaAcao()
    {
        $this->acao = $this->parameterBag->getAcaoByTipoProcesso($this->processo->getTipoProcesso());
    }

    private function carregaGrauRisco()
    {
        $processoEletronicoGrauRisco = $this->processoEletronicoGrauRiscoRepository->findByProcesso(
            $this->processo->getCodProcesso()
        );
        $this->grauRisco = $processoEletronicoGrauRisco->getGrauRisco();
    }

    private function getAtribute($var)
    {
        return ProcessoEletronicoHelper::getValueJson(is_object($var) ? $var->value : $var);
    }

    private function alterarCGM()
    {
        Log::info("Alterando CGM" . serialize($this->cgms['cgmEmpresa']));
        $cgm = ProcessoEletronicoHelper::atualizaCgmsEmpresaByDados(
            $this->inclusaoCgmService,
            $this->dados,
            $this->acao
        );

        Log::info("Alterando CGM para" . serialize($cgm));
        if (empty($cgm)) {
            throw new \Exception("Não foi possivel atualizar dados do CGM");
        }
    }

    private function alterarInscricao()
    {

        Log::info("Inicializando alteraçãoo Inscrição");
        $data             = date("Y-m-d", \db_getsession('DB_datausu'));
        $ano              = date("Y", \db_getsession('DB_datausu'));
        $clissbase        = new \cl_issbase;
        $clissquant       = new \cl_issquant;
        $clisszona        = new \cl_isszona;
        $clescrito        = new \cl_escrito;
        $clissruas        = new \cl_issruas;
        $clissbairro      = new \cl_issbairro;
        $clissmatric      = new \cl_issmatric;
        $cliptuconstr     = new \cl_iptuconstr;
        $clissprocesso    = new \cl_issprocesso;
        $clissbaseporte   = new \cl_issbaseporte;
        $clcgmtipoempresa = new \cl_cgmtipoempresa;
        $tipoEmpresa      = null;

        $clissbase->q02_numcgm = $this->cgms['cgmEmpresa']->getCodigo();
        $clissbase->q02_memo = '';
        $clissbase->q02_tiplic = "0";
        $clissbase->q02_fantaold = "0";
        $clissbase->q02_inscmu = 0;
        $clissbase->q02_obs = '';
        $clissbase->q02_ultalt = $data;
        $clissbase->q02_dtalt = $data;
        $clissbase->q02_dtbaix = null;
        $clissbase->q02_capit = "0";
        $clissbase->q02_cep = $this->cgms['cgmEmpresa']->getCep();
        $clissbase->q02_formalocalvara = 1;

        $registroJunta = '';
        $dataJunta = null;

        if ($this->acao == ProcessoEletronicoHelper::ACAO_ALVARA_AUTONOMO) {
            $outrosDados = $this->dados->outros_dados;
            $this->dados->empresa = $outrosDados;
            $endereco_municipio = $this->dados->endereco_municipio;
        } else {
            $outrosDados = $this->dados->empresa->outros_dados;
            $endereco_municipio = $outrosDados;
        }

        if (array_key_exists('data_junta_comercial', $this->dados->empresa)
            && $this->getAtribute($this->dados->empresa->data_junta_comercial) != null
        ) {
            $dataJunta = $this->getAtribute($this->dados->empresa->data_junta_comercial);
        } elseif (array_key_exists('data_junta', $this->dados->empresa)
            && $this->getAtribute($this->dados->empresa->data_junta) != null
        ) {
            $dataJunta = $this->getAtribute($this->dados->empresa->data_junta);
        }

        if (array_key_exists('registro_junta', $this->dados->empresa)
            && $this->getAtribute($this->dados->empresa->registro_junta) != null
        ) {
            $registroJunta = $this->getAtribute($this->dados->empresa->registro_junta);
        } elseif (array_key_exists('registro', $this->dados->empresa)
            && $this->getAtribute($this->dados->empresa->registro) != null
        ) {
            $registroJunta = $this->getAtribute($this->dados->empresa->registro);
        }

        $clissbase->q02_dtjunta  = !empty($dataJunta) ? (new DBDate($dataJunta))->getDate() : $dataJunta;
        $clissbase->q02_regjuc   = $registroJunta;
        $clissbase->q02_inscr = $this->inscricao;
        $clissbase->alterar($clissbase->q02_inscr);

        if ($clissbase->erro_status == 0) {
            throw new Exception($clissbase->erro_msg);
        }

        $clissquant->q30_anousu = $ano;
        $clissquant->q30_inscr  = $clissbase->q02_inscr;

        $clissquant->q30_quant  = 1;
        $clissquant->q30_mult   = 1;
        $clissquant->q30_area   = 1;
        $clissquant->q30_graurisco = $this->verificaCamposAdicionais(
            "atividades",
            "grauRisco",
            ""
        );

        if ($this->acao != ProcessoEletronicoHelper::ACAO_ALVARA_AUTONOMO) {
            if (array_key_exists('empregados', $outrosDados)) {
                if (intval($this->getAtribute($outrosDados->empregados)) != 0) {
                    $clissquant->q30_quant  =  str_replace(
                        array(".", ","),
                        array("", "."),
                        $this->getAtribute($outrosDados->empregados)
                    );
                }
            }

            if (array_key_exists('area', $outrosDados)) {
                if (intval($this->getAtribute($outrosDados->area)) != 0) {
                    $outrosDados->area = preg_replace('/[^\d\.,]/', '', $this->getAtribute($outrosDados->area));
                    if (strpos($outrosDados->area, ",") == true) {
                        $clissquant->q30_area   = str_replace(array(".", ","), array("", "."), $outrosDados->area);
                    } else {
                        $clissquant->q30_area = $outrosDados->area;
                    }
                }
            }
        }

        if (array_key_exists('zona', $endereco_municipio)) {
            if (intval($this->getAtribute($endereco_municipio->zona)) != 0) {
                $clisszona->q35_zona  = $this->getAtribute($endereco_municipio->zona);
                $clisszona->q35_inscr = $clissbase->q02_inscr;
                $clisszona->alterar($clissbase->q02_inscr);

                if ($clisszona->erro_status == 0) {
                    throw new \Exception($clisszona->erro_msg);
                }
            }
        }

        $clissquant->q30_anousu = $ano;
        $clissquant->q30_inscr = $clissbase->q02_inscr;
        $clissquant->alterar($ano, $clissbase->q02_inscr);

        if ($clissquant->erro_status == 0) {
            throw new Exception($clissquant->erro_msg);
        }

        if ($this->acao == ProcessoEletronicoHelper::ACAO_ALVARA_AUTONOMO) {
            if (array_key_exists('porte', $this->dados->responsavel) &&
                !is_null($this->getAtribute($this->dados->responsavel->porte))
            ) {
                $clissbaseporte->q45_inscr    = $clissbase->q02_inscr;
                $clissbaseporte->q45_codporte = $this->getAtribute($this->dados->responsavel->porte);
                $clissbaseporte->alterar($clissbase->q02_inscr);

                if ($clissbaseporte->erro_status == 0) {
                    throw new Exception($clisszona->erro_msg);
                }
            }
        } elseif (array_key_exists('porte', $outrosDados)) {
            if (intval($this->getAtribute($outrosDados->porte)) != 0) {
                $clissbaseporte->q45_inscr    = $clissbase->q02_inscr;
                $clissbaseporte->q45_codporte = $this->getAtribute($outrosDados->porte);
                $clissbaseporte->alterar($clissbase->q02_inscr);

                if ($clissbaseporte->erro_status == 0) {
                    throw new Exception($clissbaseporte->erro_msg);
                }
            }
        }

        if (array_key_exists('cgmEscritorio', $this->cgms)) {
            $escritoriosAtivos = $this->verificaSeJaExisteEscritorioContabilAtivoDiferenteDoNovo(
                $clissbase->q02_inscr,
                $this->cgms['cgmEscritorio']->getCodigo()
            );

            if ($escritoriosAtivos) {
                foreach ($escritoriosAtivos as $escritoriosAtivo) {
                    $escritoriosAtivo = (object)$escritoriosAtivo;
                    $this->baixaEscritorioAnterior($escritoriosAtivo->q10_sequencial);
                }
            }

            if (!$this->verificarSeNovoEscritorioContabilEstaAtivo(
                $clissbase->q02_inscr,
                $this->cgms['cgmEscritorio']->getCodigo()
            )) {
                $this->incluirNovoEscritorio(
                    $clissbase->q02_inscr,
                    $this->cgms['cgmEscritorio']->getCodigo()
                );
            }
        }

        if ($this->acao == ProcessoEletronicoHelper::ACAO_ALVARA_AUTONOMO) {
            $endereco = $this->dados->endereco_municipio;
        } else {
            $endereco = $this->dados->empresa->endereco;
        }

        $numero = $this->getAtribute($endereco->numero);
        $complemento = $this->getAtribute($endereco->complemento);
        $cep = $this->getAtribute($endereco->cep);
        $bairro = $this->getAtribute($endereco->bairro);
        $logradouro = $this->getAtribute($endereco->logradouro);

        $clissruas->q02_inscr  = $clissbase->q02_inscr;
        $clissruas->j14_codigo = $logradouro;
        $clissruas->q02_numero = $numero;
        $clissruas->q02_compl  = substr(mb_strtoupper($complemento), 0, 40);
        $clissruas->q02_cxpost = '';
        $clissruas->z01_cep    = str_replace('-', '', $cep);
        $clissruas->alterar($clissbase->q02_inscr);

        if ($clissruas->erro_status == 0) {
            throw new Exception($clissruas->erro_msg);
        }

        $clissbairro->q13_inscr  = $clissbase->q02_inscr;
        $clissbairro->q13_bairro = $bairro;
        $clissbairro->alterar($clissbairro->q13_inscr);

        if ($clissbairro->erro_status == 0) {
            throw new Exception($clissbairro->erro_msg);
        }

        $matricula = null;
        if (array_key_exists('matricula', $endereco) && !is_null($this->getAtribute($endereco->matricula))) {
            $matricula = $this->getAtribute($endereco->matricula);

            if (is_object($matricula)) {
                $matricula = $matricula->codigo;
            }

            if (!empty($matricula) and is_numeric($matricula)) {
                $matricula = (int)$matricula;
                if (is_integer($matricula)) {
                    $rs = db_query(
                        $cliptuconstr->sql_query(
                            null,
                            null,
                            $campos = "j39_idcons",
                            null,
                            " iptuconstr.j39_matric = $matricula and j39_idprinc = 't' "
                        )
                    );

                    if (pg_num_rows($rs) > 0) {
                        $idconst = \db_utils::fieldsMemory($rs, 0)->j39_idcons;

                        if (empty($matricula)) {
                            throw new \Exception("Matricula não informada");
                        }

                        $clissmatric->q05_inscr  = $clissbase->q02_inscr;
                        $clissmatric->q05_matric = $matricula;
                        $clissmatric->q05_idcons = $idconst;
                        $clissmatric->alterar($clissmatric->q05_inscr, $clissmatric->q05_matric);

                        if ($clissmatric->erro_status == 0) {
                            throw new Exception($clissmatric->erro_msg);
                        }
                    }
                }
            }
        }

        if ($this->acao == ProcessoEletronicoHelper::ACAO_ALVARA_AUTONOMO) {
            if (array_key_exists('tipo_empresa', $this->dados->responsavel) &&
                !is_null($this->getAtribute($this->dados->responsavel->tipo_empresa))
            ) {
                $tipoEmpresa = $this->getAtribute($this->dados->responsavel->tipo_empresa);
            }
        } else {
            if (array_key_exists('tipo_empresa', $this->dados->empresa) &&
                !is_null($this->getAtribute($this->dados->empresa->tipo_empresa))
            ) {
                $tipoEmpresa = $this->getAtribute($this->dados->empresa->tipo_empresa);
            }
        }

        if (!is_null($tipoEmpresa)) {
            $clcgmtipoempresa->excluir(
                null,
                "z03_numcgm={$this->cgms['cgmEmpresa']->getCodigo()}"
            );

            if ($clcgmtipoempresa->erro_status == 0) {
                throw new Exception($clcgmtipoempresa->erro_msg);
            }

            $clcgmtipoempresa->z03_numcgm = $this->cgms['cgmEmpresa']->getCodigo();
            $clcgmtipoempresa->z03_tipoempresa = $tipoEmpresa;
            $clcgmtipoempresa->incluir(null);

            if ($clcgmtipoempresa->erro_status == 0) {
                throw new Exception($clcgmtipoempresa->erro_msg);
            }
        }

        $this->clissbase = $clissbase;
        Log::info("Finalizando alteração Inscrição");
    }

    private function incluirAtividades()
    {
        Log::info("Inicializando inclusão de atividades");
        $atividadesSecundarias = array();

        if ($this->acao == ProcessoEletronicoHelper::ACAO_ALVARA_AUTONOMO) {
            if (is_array($this->dados->atividades)) {
                $atividadePrincipal = array_filter($this->dados->atividades, function ($atividade) {
                    return $atividade->principal == "1";
                })[0];
            } else {
                $atividadePrincipal = $this->dados->atividades;
            }

            if (array_key_exists('atividades_secundarias', $this->dados->atividades)) {
                $atividadesSecundarias = $this->dados->atividades->atividades_secundarias;
            }
        } else {
            $atividadePrincipal = $this->dados->empresa->atividades;
            if (array_key_exists('atividades_secundarias', $this->dados->empresa->atividades)) {
                $atividadesSecundarias = $this->dados->empresa->atividades->atividades_secundarias;
            }
        }

        if (is_object($atividadePrincipal->atividade)) {
            $atividadeCodigo = $atividadePrincipal->atividade->id;
        } else {
            $atividadeCodigo = $atividadePrincipal->atividade;
        }

        if (is_object($atividadePrincipal->data_inicio)) {
            $data_inicio = $atividadePrincipal->data_inicio->value;
        } else {
            $data_inicio = $atividadePrincipal->data_inicio;
        }

        $data_inicio = date("Y-m-d", strtotime($data_inicio));

        $cltabativ = $this->incluirTabativ(
            $atividadeCodigo,
            $data_inicio,
            true
        );

        if (!empty($atividadesSecundarias)) {
            foreach ($atividadesSecundarias as $key => $atividade) {
                $data_inicio = date("Y-m-d", strtotime($atividade->data_inicio->value));

                $cltabativ = $this->incluirTabativ(
                    $atividade->atividade->id,
                    $data_inicio,
                    false
                );
            }
        }

//        $this->atualizaDataIniEmpresa();

        $this->verificaOptanteSimples();
        Log::info("Finalizando inclusão de atividades");
        return $cltabativ;
    }

    private function incluirTabativ($idAtividade, $dataInicio, $principal)
    {
        $dataInicio = !empty($dataInicio) ? (new DBDate($dataInicio))->getDate() : $dataInicio;

        $cltabativ = new \cl_tabativ;

        $cltabativ->q07_inscr  = $this->clissbase->q02_inscr;
        $cltabativ->q07_ativ   = $idAtividade;
        $cltabativ->q07_quant  = 1;
        $cltabativ->q07_perman = "true";
        $cltabativ->q07_tipbx  = "0";
        $cltabativ->q07_imprimealvara  = "Sim";
        // $cltabativ->q07_datafi = date('Y-m-d', strtotime($dataInicio . ' + 1 year'));
        $cltabativ->q07_datafi = null;

        $oSeq = $this->atividadeExiste($cltabativ->q07_ativ);
        if ($oSeq) {
            Log::info("Alterando atividade".json_encode($cltabativ));
            $cltabativ->q07_seq = $oSeq;
            $cltabativ->alterar($this->clissbase->q02_inscr, $cltabativ->q07_seq);
        } else {
            $result = $cltabativ->sql_record($cltabativ->sql_query_file(
                $this->clissbase->q02_inscr,
                '',
                'max(q07_seq)+1 as seq'
            ));
            $oSeq = \db_utils::fieldsMemory($result, 0);
            $cltabativ->q07_seq    = (is_null($oSeq) || $oSeq->seq == '') ? 1 : $oSeq->seq;
            Log::info(
                "incluindo atividade" . json_encode(
                    ['inscricao' => $this->clissbase->q02_inscr, 'atividade' => $cltabativ->q07_seq]
                )
            );
            $cltabativ->q07_datain = $dataInicio;
            $cltabativ->incluir($this->clissbase->q02_inscr, $cltabativ->q07_seq);
        }

        if ($cltabativ->erro_status == 0) {
            throw new \Exception($cltabativ->erro_msg);
        }

        if ($principal) {
            $this->incluirAtividadePrincipal($cltabativ->q07_seq);
        }

        return $cltabativ;
    }

    private function incluirAtividadePrincipal($q07_seq)
    {
        $clativprinc = new \cl_ativprinc;
        $rs = $clativprinc->sql_record($clativprinc->sql_query_file($this->clissbase->q02_inscr));
        Log::info("Excluindo atividade principal".json_encode(pg_fetch_object($rs)));
        if ($clativprinc->numrows > 0) {
            $clativprinc->q88_inscr = $this->clissbase->q02_inscr;
            $clativprinc->excluir($this->clissbase->q02_inscr);
            if ($clativprinc->erro_status == 0) {
                throw new Exception($clativprinc->erro_msg);
            }
        }

        $clativprinc->q88_inscr = $this->clissbase->q02_inscr;
        $clativprinc->q88_seq   = $q07_seq;
        $clativprinc->incluir($this->clissbase->q02_inscr);
        if ($clativprinc->erro_status == 0) {
            throw new Exception($clativprinc->erro_msg);
        }

        return $clativprinc;
    }

    private function atividadeExiste($q07_ativ)
    {
        $cltabativ = new \cl_tabativ();
        $sql = $cltabativ->sql_query_file(
            null,
            null,
            "*",
            null,
            "q07_ativ = {$q07_ativ} AND q07_inscr = {$this->clissbase->q02_inscr}"
        );
        $rs = db_query($sql);
        $obj = pg_fetch_object($rs);
        Log::info("Atividade [ existe =>".json_encode($obj)." sql=>{$sql}]");
        if (!empty($obj)) {
            return $obj->q07_seq;
        }
        return false;
    }

    private function atualizaDataIniEmpresa()
    {
        $cltabativ = new \cl_tabativ;
        $result = $cltabativ->sql_record($cltabativ->sql_query_file(
            '',
            '',
            'min(q07_datain) as datainicial',
            '',
            ' q07_inscr = ' . $this->clissbase->q02_inscr . ''
        ));

        if ($cltabativ->numrows > 0) {
            $datainicial = \db_utils::fieldsMemory($result, 0)->datainicial;
            $this->clissbase->q02_dtinic = $datainicial;
            $this->clissbase->q02_dtbaix = null;

            if ($this->clissbase->q02_dtjunta == 'null') {
                $this->clissbase->q02_dtjunta = null;
            }

            $this->clissbase->alterar($this->clissbase->q02_inscr);
            if ($this->clissbase->erro_status == 0) {
                throw new Exception($this->clissbase->erro_msg);
            }
        }
    }

    private function incluirSocios()
    {
        Log::info("Inicializando inclusão de socios");
        if ($this->acao == ProcessoEletronicoHelper::ACAO_ALVARA_EMPRESA) {
            foreach ($this->dados->empresa->socios as $key => $socio) {
                $valor_capital = preg_replace('/[^\d\.,]/', '', $this->getAtribute($socio->valor_capital));
                if (strpos($valor_capital, ",") == true) {
                    $valor_capital   = str_replace(array(".", ","), array("", "."), $valor_capital);
                }

                $this->incluirSocio(
                    $this->cgms['cgmEmpresa']->getCodigo(),
                    $this->cgms['cgmSocios'][$key]->getCodigo(),
                    $valor_capital ? $valor_capital : 0,
                    $this->getAtribute($socio->tipo_socio)
                );
            }
        } elseif ($this->acao == ProcessoEletronicoHelper::ACAO_ALVARA_MEI) {
            foreach ((array) $this->dados->empresa->responsavel_mei as $key => $responsavel) {
                $valor_capital = preg_replace('/[^\d\.,]/', '', $this->getAtribute($responsavel->valor_capital));
                if (strpos($valor_capital, ",") == true) {
                    $valor_capital   = str_replace(array(".", ","), array("", "."), $valor_capital);
                }

                $this->incluirSocio(
                    $this->cgms['cgmEmpresa']->getCodigo(),
                    $this->cgms['cgmResponsavel'][$key]->getCodigo(),
                    $valor_capital ? $valor_capital : 0,
                    $this->getAtribute($responsavel->tipo_socio)
                );
            }
        }
        Log::info("Finalizando inclusão de socios");
    }

    private function incluirSocio(
        $cgmEmpresa,
        $cgmSocio,
        $percentual,
        $tipo
    ) {
        $clsocios = new \cl_socios();

        $clsocios->q95_cgmpri = $cgmEmpresa;
        $clsocios->q95_numcgm = $cgmSocio;
        $clsocios->q95_perc   = str_replace(',', '.', (!is_null($percentual) ? $percentual : '0'));
        $clsocios->q95_tipo   = $tipo;

        if ($this->verificaSeJaExisteSocio($cgmEmpresa, $cgmSocio)) {
            $clsocios->alterar($cgmEmpresa, $cgmSocio);
        } else {
            $clsocios->incluir($cgmEmpresa, $cgmSocio);
            if ($clsocios->erro_status == 0) {
                throw new Exception($clsocios->erro_msg);
            }
        }
        return $clsocios;
    }

    private function alterarAlvara()
    {

        Log::info("Inicializando alteração de alvara");

        $clissalvara      = new \cl_issalvara;

        $sql = $clissalvara->sql_query_file(
            null,
            "*",
            null,
            "q123_inscr = {$this->clissbase->q02_inscr}"
        );
        $rsAlvara = pg_query($sql);
        $alvaraObj = pg_fetch_object($rsAlvara);
        if (empty($alvaraObj)) {
            throw new Exception("Alvara não encontrado!");
        }
        $classificacaoGrauRisco = ProcessoEletronicoHelper::getClassificacaoGrauRisco($this->grauRisco);

        $clissalvara->q123_isstipoalvara = ProcessoEletronicoHelper::getTipoAlvara(
            $this->parameterBag,
            $classificacaoGrauRisco
        );

        $clissalvara->q123_inscr         = $this->clissbase->q02_inscr;
        $clissalvara->q123_dtinclusao    = date("Y-m-d", \db_getsession('DB_datausu'));
        $clissalvara->q123_situacao      = 1;
        $clissalvara->q123_usuario       = db_getsession("DB_id_usuario");
        $clissalvara->q123_geradoautomatico = "true";
        $clissalvara->q123_sequencial = $alvaraObj->q123_sequencial;
        $clissalvara->alterar($clissalvara->q123_sequencial);
        Log::info("finalizando alteração de alvara");
        if ($clissalvara->erro_status == '0') {
            throw new Exception($clissalvara->erro_msg);
        }
        Log::info("setando alvara");
        $this->alvara = new \Alvara($clissalvara->q123_sequencial);
    }

    private function inserirDocumentos()
    {
        Log::info("incializando inclusão de documentos");
        if (!isset($this->dados->documentos)) {
            return;
        }

        $idAtividades = array();

        $oLiberarAlvara  = new \AlvaraMovimentacaoLiberacao($this->alvara->getCodigo());
        foreach ($this->dados->documentos as $documento) {
            if ($documento->tipo == 'atividades') {
                $oLiberarAlvara->addDocumento($documento->codigo_vinculo);
            }
        }

        $oLiberarAlvara->gravaDocumentos();
        Log::info("finalizando inclusão de documentos");
    }

    public function getInscricao()
    {
        return $this->clissbase->q02_inscr;
    }

    private function verificaOptanteSimples()
    {
        if ($this->acao == ProcessoEletronicoHelper::ACAO_ALVARA_EMPRESA) {
            if (array_key_exists('optante_simples', $this->dados->empresa->simples)
                && $this->getAtribute($this->dados->empresa->simples->optante_simples) != null
                && $this->getAtribute($this->dados->empresa->simples->optante_simples) == 1
            ) {
                if (array_key_exists('data_opcao_simples', $this->dados->empresa->simples)
                    && $this->getAtribute($this->dados->empresa->simples->data_opcao_simples) != null
                ) {
                    $dataOpcapSimples = $this->getAtribute($this->dados->empresa->simples->data_opcao_simples);
                    $dataSimples = new DateTime((new DBDate($dataOpcapSimples))->getDate());
                } else {
                    $dataSimples = new DateTime((new DBDate($this->clissbase->q02_dtinic))->getDate());
                }

                if (array_key_exists('categoria_simples', $this->dados->empresa->simples)
                    && $this->getAtribute($this->dados->empresa->simples->categoria_simples) != null
                ) {
                    $categoriaSimples = $this->getAtribute($this->dados->empresa->simples->categoria_simples);
                } else {
                    throw new Exception("Categoria do simples não informada");
                }

                $this->incluirEmpresaOptanteSimples($this->clissbase->q02_inscr, $categoriaSimples, $dataSimples);
            }
        } elseif ($this->acao == ProcessoEletronicoHelper::ACAO_ALVARA_MEI) {
            $this->incluirEmpresaOptanteSimples(
                $this->clissbase->q02_inscr,
                IssCategoriaEnum::MEI,
                new DateTime($this->clissbase->q02_dtinic)
            );
        }
    }

    /**
     * Insere uma empresa optante pelo simples quando é do tipo MEI
     * @param $inscricao
     * @param DateTime $dataInicial
     * @return IssCadastroSimples
     * @throws Exception
     */
    private function incluirEmpresaOptanteSimples($inscricao, $categoria, DateTime $dataInicial)
    {
        $dao = new cl_isscadsimples();
        $repo = IssCadastroSimplesRepository::getInstance($dao);

        $entity = new IssCadastroSimples();
        $entity->setDataInicial($dataInicial);
        $entity->setCategoria($categoria);
        $entity->setInscricao($inscricao);

        return $repo->save($entity);
    }

    public function verificaCamposAdicionais($sSecao, $sCampo, $sDefault = null)
    {
        if (isset($this->camposAdicionais->$sSecao) && isset($this->camposAdicionais->$sSecao->$sCampo)) {
            return $this->camposAdicionais->$sSecao->$sCampo;
        }

        return $sDefault;
    }

    private function verificaSeJaExisteSocio($cgmEmpresa, $cgmSocio)
    {
        $clsocios = new \cl_socios();
        $sql = $clsocios->sql_query_file($cgmEmpresa, $cgmSocio);
        $rs = db_query($sql);
        if (pg_num_rows($rs) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $inscricao
     * @return array|false
     */
    private function verificaSeJaExisteEscritorioContabilAtivoDiferenteDoNovo($inscricao, $cgmNovoEscritorioContabil)
    {
        $sql = "SELECT
                 *
                FROM
                    issqn.escrito
               WHERE
                     q10_inscr = {$inscricao}
               AND q10_numcgm <> {$cgmNovoEscritorioContabil}
               AND q10_dtfim IS NULL";
        $rs = db_query($sql);
        $escritorios = pg_fetch_all($rs);
        if (empty($escritorios)) {
            return false;
        }
        return $escritorios;
    }

    private function baixaEscritorioAnterior($q10_sequencial)
    {
        Log::info("Verficando se escritorio exsiste => ".json_encode($q10_sequencial));
        $clescrito =   new \cl_escrito();
        $clescrito->q10_dtfim  = date("Y-m-d", \db_getsession('DB_datausu'));
        $clescrito->q10_sequencial = $q10_sequencial;
        $clescrito->alterar($q10_sequencial);
        if ($clescrito->erro_status == 0) {
            throw new Exception($clescrito->erro_msg);
        }
    }

    private function verificarSeNovoEscritorioContabilEstaAtivo($inscricao, $cgmNovoEscritorioContabil)
    {

        $sql = "SELECT
                     *
                    FROM
                        issqn.escrito
                   WHERE
                         q10_inscr = {$inscricao}
                   AND q10_numcgm = {$cgmNovoEscritorioContabil}
                   AND q10_dtfim IS NULL";
        $rs = db_query($sql);
        $escritorio = pg_fetch_object($rs);
        if (empty($escritorio)) {
            return false;
        }
        Log::info("Verficando se escritorio exsiste => ".json_encode($escritorio));
        return $escritorio;
    }

    private function incluirNovoEscritorio($inscricao, $cgmNovoEscritorioContabil)
    {
        $clescrito =   new \cl_escrito();

        $clescrito->q10_inscr  = $inscricao;
        $clescrito->q10_numcgm = $cgmNovoEscritorioContabil;
        $clescrito->q10_dtini = date("Y-m-d", \db_getsession('DB_datausu'));
        $clescrito->incluir(null);

        if ($clescrito->erro_status == 0) {
            throw new Exception($clescrito->erro_msg);
        }
    }

    private function salvarArquivoBIC()
    {
        Log::info("Salvando a BIC");
        db_putsession('DB_itemmenu_acessado', 228068);
        db_putsession('DB_modulo', 604);
        $arquivo = new GerarArquivoBicPDF($this->inscricao);
        $arquivo->gerar();
        $this->caminhoArquivoBIC = $arquivo->getCaminho();
        Log::info("CAMINHO DA BIC {$this->caminhoArquivoBIC}");
    }

    private function salvarDocumentoNoStorage()
    {
        Log::info("Salvando arquivo no storage");
        $storageConfig = StorageHelper::getStorageConfig();
        $allowed = array();

        $metadata = new \stdClass();
        $metadata->tipo_documento = "processo";
        $metadata->numero_do_processo = $this->processo->getNumeroProcesso() . "/" . $this->processo->getAnoProcesso();
        $metadata->requerente = $this->processo->getRequerente();
        $metadata->data_hora = $this->processo->getDataProcesso() . " " . $this->processo->getHora();

        if (isset($storageConfig->client_id_ouvidoria) && !empty($storageConfig->client_id_ouvidoria)) {
            $allowed[] = $storageConfig->client_id_ouvidoria;
        }

        $processoDocumento = new ProcessoDocumento();
        $processoDocumento->setProcesso($this->processo->getCodProcesso());
        $processoDocumento->setDescricao('BIC');
        $processoDocumento->setData($this->processo->getDataProcesso());
        $processoDocumento->setUsuario(DefaultSession::getInstance()->get(DefaultSession::DB_ID_USUARIO));
        $processoDocumento->setStorage(true);
        $processoDocumento->setOrdem(1);
        $processoDocumento->setNomeDocumento(
            StorageHelper::uploadArquivo($this->getCaminhoArquivoBIC(), $allowed, true, $metadata)
        );

        $processoDocumentoRepository = new ProcessoDocumentoRepository(new \cl_protprocessodocumento());
        $processoDocumentoRepository->persist($processoDocumento);
        Log::info("Finalizando salvar arquivo no storage");
    }
}
