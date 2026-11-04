<?php

namespace ECidade\Tributario\Issqn\Acao\Transicao\Entity;

use Alvara;
use AlvaraMovimentacaoLiberacao;
use BusinessException;
use cl_ativprinc;
use cl_cgmtipoempresa;
use cl_escrito;
use cl_iptuconstr;
use cl_issalvara;
use cl_issbairro;
use cl_issbase;
use cl_issbaseporte;
use cl_isscadsimples;
use cl_issmatric;
use cl_issprocesso;
use cl_issquant;
use cl_issruas;
use cl_isszona;
use cl_socios;
use cl_tabativ;
use DateTime;
use db_utils;
use ECidade\Configuracao\Workflow\Interfaces\Acao as AcaoInterface;
use ECidade\Tributario\Issqn\Enum\IssCategoriaEnum;
use ECidade\Tributario\Issqn\Model\IssCadastroSimples;
use ECidade\Tributario\Issqn\Repository\IssbaseRepository;
use ECidade\Tributario\Issqn\Inscricao\Service\AlvaraOnline;
use ECidade\Patrimonial\Protocolo\Servicos\InclusaoCgmLegacy;
use ECidade\Tributario\Issqn\ParametrosProcessoEletronicoBag;
use ECidade\Tributario\Issqn\Repository\ProcessoEletronicoGrauRiscoRepository;
use ECidade\Patrimonial\Protocolo\Processo\ProcessoEletronico\Helper\ProcessoEletronicoHelper;
use ECidade\Tributario\Issqn\Inscricao\Atividades\Filter\ListagemAtividades as FiltroListagemAtividades;
use ECidade\Patrimonial\Protocolo\Processo\ProcessoEletronico\Filter\ListagemProcessos as FiltroListagemProcessos;
use ECidade\Tributario\Issqn\Repository\IssCadastroSimplesRepository;
use Exception;
use DBDate;
use ParameterException;

final class GerarInscricao extends AcaoBase implements AcaoInterface, InscricaoInterface
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
    /**
     * @var ProcessoEletronicoGrauRiscoRepository
     */
    private $processoEletronicoGrauRiscoRepository;
    /**
     * @var ParametrosProcessoEletronicoBag
     */
    private $parameterBag;
    /**
     * @var InclusaoCgmLegacy
     */
    private $inclusaoCgmService;
    /**
     * @var Alvara
     */
    private $alvara;


    /**
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
        $this->inclusaoCgmService = $inclusaoCgmService;
        $this->parameterBag = $parameterBag;
        $this->processoEletronicoGrauRiscoRepository = $processoEletronicoGrauRiscoRepository;
        $this->filtroProcesso = $filtroProcesso;
        $this->filtroAtividades = $filtroAtividades;
    }

    /**
     * @param mixed $camposAdicionais
     * @return GerarInscricao
     */
    public function setCamposAdicionais($camposAdicionais)
    {
        $this->camposAdicionais = $camposAdicionais;
        return $this;
    }

    /**
     * @throws BusinessException
     * @throws Exception
     */
    public function validate()
    {
        $this->carregaAcao();
        $this->carregaGrauRisco();
        $this->buscarDadosByProcesso();
        $this->buscarCgmsByDados();

        if (empty($this->dados)) {
            throw new BusinessException("Não há dados vinculados ao processo!");
        } elseif (empty($this->cgms)) {
            throw new BusinessException("Cgms não criados!");
        }
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $this->criarInscricao();
        $this->incluirSocios();
        $this->incluirAtividades();
        $this->incluirAlvara();
        $this->inserirDocumentos();
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

    /**
     * @throws Exception
     */
    private function carregaAcao()
    {
        $this->acao = $this->parameterBag->getAcaoByTipoProcesso($this->processo->getTipoProcesso());
    }

    /**
     * @throws Exception
     */
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

    /**
     * @throws ParameterException
     * @throws Exception
     */
    private function criarInscricao()
    {

        $data = date("Y-m-d", db_getsession('DB_datausu'));
        $ano = date("Y", db_getsession('DB_datausu'));
        $clissbase = new cl_issbase;
        $clissquant = new cl_issquant;
        $clisszona = new cl_isszona;
        $clescrito = new cl_escrito;
        $clissruas = new cl_issruas;
        $clissbairro = new cl_issbairro;
        $clissmatric = new cl_issmatric;
        $cliptuconstr = new cl_iptuconstr;
        $clissprocesso = new cl_issprocesso;
        $clissbaseporte = new cl_issbaseporte;
        $clcgmtipoempresa = new cl_cgmtipoempresa;
        $tipoEmpresa = null;

        $sDataInicio = $data;
        $oDataInicio = $this->getDataIfPossible($this->dados, "empresa.atividades.data_inicio");

        if (!empty($this->getAtribute($oDataInicio))) {
            $sDataInicio = $this->getAtribute($oDataInicio);
        }

        $sProtocoloJunta = "";
        $oProtocoloJunta = $this->getDataIfPossible($this->dados, "empresa.protocolo_junta");
        if (!empty($this->getAtribute($oProtocoloJunta))) {
            $sProtocoloJunta = $this->getAtribute($oProtocoloJunta);
        }

        $clissbase->q02_numcgm = $this->cgms['cgmEmpresa']->getCodigo();
        $clissbase->q02_memo = '';
        $clissbase->q02_tiplic = "0";
        $clissbase->q02_inscmu = 0;
        $clissbase->q02_obs = '';
        $clissbase->q02_dtcada = $data;
        $clissbase->q02_dtinic = $sDataInicio;
        $clissbase->q02_ultalt = $data;
        $clissbase->q02_dtalt = $data;
        $clissbase->q02_dtbaix = null;
        $clissbase->q02_capit = "0";
        $clissbase->q02_cep = $this->cgms['cgmEmpresa']->getCep();
        $clissbase->q02_formalocalvara = 1;
        $clissbase->q02_protocolojuntacomercial = $sProtocoloJunta;

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

        $clissbase->q02_dtjunta = !empty($dataJunta) ? (new DBDate($dataJunta))->getDate() : $dataJunta;
        $clissbase->q02_regjuc = $registroJunta;


        $clissbase->incluirNumeracaoContinua(null);

        if ($clissbase->erro_status == 0) {
            throw new Exception($clissbase->erro_msg);
        }

        $clissquant->q30_anousu = $ano;
        $clissquant->q30_inscr = $clissbase->q02_inscr;

        $clissquant->q30_quant = 1;
        $clissquant->q30_mult = 1;
        $clissquant->q30_area = 1;
        $clissquant->q30_graurisco = $this->verificaCamposAdicionais(
            "atividades",
            "grauRisco",
            ""
        );

        if ($this->acao != ProcessoEletronicoHelper::ACAO_ALVARA_AUTONOMO) {
            if (array_key_exists('empregados', $outrosDados)) {
                if (intval($this->getAtribute($outrosDados->empregados)) != 0) {
                    $clissquant->q30_quant = str_replace(
                        array(".", ","),
                        array("", "."),
                        $this->getAtribute($outrosDados->empregados)
                    );
                }
            }

            if (array_key_exists('area', $outrosDados)) {
                if (intval($this->getAtribute($outrosDados->area)) != 0) {
                    $outrosDados->area = preg_replace('/[^\d\.,]/', '', $this->getAtribute($outrosDados->area));
                    if (strpos($outrosDados->area, ",")) {
                        $clissquant->q30_area = str_replace(array(".", ","), array("", "."), $outrosDados->area);
                    } else {
                        $clissquant->q30_area = $outrosDados->area;
                    }
                }
            }
        }

        if (array_key_exists('zona', $endereco_municipio)) {
            if (intval($this->getAtribute($endereco_municipio->zona)) != 0) {
                $clisszona->q35_zona = $this->getAtribute($endereco_municipio->zona);
                $clisszona->q35_inscr = $clissbase->q02_inscr;
                $clisszona->incluir($clissbase->q02_inscr);

                if ($clisszona->erro_status == 0) {
                    throw new Exception($clisszona->erro_msg);
                }
            }
        }

        $clissquant->incluir($ano, $clissbase->q02_inscr);

        if ($clissquant->erro_status == 0) {
            throw new Exception($clissquant->erro_msg);
        }

        $clissprocesso->q14_inscr = $clissbase->q02_inscr;
        $clissprocesso->q14_proces = $this->processo->getCodProcesso();
        $clissprocesso->incluir($clissbase->q02_inscr);

        if ($clissprocesso->erro_status == 0) {
            throw new Exception($clissprocesso->erro_msg);
        }

        if ($this->acao == ProcessoEletronicoHelper::ACAO_ALVARA_AUTONOMO) {
            if (array_key_exists('porte', $this->dados->responsavel) &&
                !is_null($this->getAtribute($this->dados->responsavel->porte))
            ) {
                $clissbaseporte->q45_inscr = $clissbase->q02_inscr;
                $clissbaseporte->q45_codporte = $this->getAtribute($this->dados->responsavel->porte);
                $clissbaseporte->incluir($clissbase->q02_inscr);

                if ($clissbaseporte->erro_status == 0) {
                    throw new Exception($clisszona->erro_msg);
                }
            }
        } elseif (array_key_exists('porte', $outrosDados)) {
            if (intval($this->getAtribute($outrosDados->porte)) != 0) {
                $clissbaseporte->q45_inscr = $clissbase->q02_inscr;
                $clissbaseporte->q45_codporte = $this->getAtribute($outrosDados->porte);
                $clissbaseporte->incluir($clissbase->q02_inscr);

                if ($clissbaseporte->erro_status == 0) {
                    throw new Exception($clissbaseporte->erro_msg);
                }
            }
        }

        if (array_key_exists('cgmEscritorio', $this->cgms)) {
            $clescrito->q10_inscr = $clissbase->q02_inscr;
            $clescrito->q10_numcgm = $this->cgms['cgmEscritorio']->getCodigo();
            $clescrito->incluir(null);

            if ($clescrito->erro_status == 0) {
                throw new Exception($clescrito->erro_msg);
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

        $clissruas->q02_inscr = $clissbase->q02_inscr;
        $clissruas->j14_codigo = $logradouro;
        $clissruas->q02_numero = $numero;
        $clissruas->q02_compl = substr(mb_strtoupper($complemento), 0, 40);
        $clissruas->q02_cxpost = '';
        $clissruas->z01_cep = $cep;
        $clissruas->incluir($clissbase->q02_inscr);

        if ($clissruas->erro_status == 0) {
            throw new Exception($clissruas->erro_msg);
        }

        $clissbairro->q13_inscr = $clissbase->q02_inscr;
        $clissbairro->q13_bairro = $bairro;
        $clissbairro->incluir($clissbase->q02_inscr);

        if ($clissbairro->erro_status == 0) {
            throw new Exception($clissbairro->erro_msg);
        }

        $matricula = null;
        if (array_key_exists('matricula', $endereco)) {
            $matricula = $this->getAtribute($endereco->matricula);
            if (is_object($matricula)) {
                $matricula = $matricula->codigo;
            }
        }

        if (!empty($matricula) and is_numeric($matricula)) {
            $matricula = (int)$matricula;
            $rs = db_query(
                $cliptuconstr->sql_query(
                    null,
                    null,
                    "j39_idcons",
                    null,
                    " iptuconstr.j39_matric = $matricula and j39_idprinc = 't' "
                )
            );

            if (pg_num_rows($rs) > 0) {
                $idconst = db_utils::fieldsMemory($rs, 0)->j39_idcons;

                if (empty($matricula)) {
                    throw new Exception("Matricula não informada");
                }

                $clissmatric->q05_inscr = $clissbase->q02_inscr;
                $clissmatric->q05_matric = $matricula;
                $clissmatric->q05_idcons = $idconst;
                $clissmatric->incluir($clissmatric->q05_inscr, $clissmatric->q05_matric);

                if ($clissmatric->erro_status == 0) {
                    throw new Exception($clissmatric->erro_msg);
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
            $numCgm = (int) $this->cgms['cgmEmpresa']->getCodigo();
            $clcgmtipoempresa->sql_record(
                "select *  from cgmtipoempresa
                    where z03_numcgm = {$numCgm}"
            );

            if ($clcgmtipoempresa->numrows === 0) {
                $clcgmtipoempresa->z03_numcgm = $this->cgms['cgmEmpresa']->getCodigo();
                $clcgmtipoempresa->z03_tipoempresa = $tipoEmpresa;
                $clcgmtipoempresa->incluir(null);

                if ($clcgmtipoempresa->erro_status == 0) {
                    throw new Exception($clcgmtipoempresa->erro_msg);
                }
            }
        }

        $this->clissbase = $clissbase;
    }

    /**
     * @throws ParameterException
     */
    private function incluirAtividades()
    {
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
            foreach ($atividadesSecundarias as $atividade) {
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

        return $cltabativ;
    }

    /**
     * @throws ParameterException
     * @throws Exception
     * @throws Exception
     * @throws Exception
     */
    private function incluirTabativ($idAtividade, $dataInicio, $principal)
    {
        $dataInicio = !empty($dataInicio) ? (new DBDate($dataInicio))->getDate() : $dataInicio;

        $cltabativ = new cl_tabativ;

        $result = $cltabativ->sql_record($cltabativ->sql_query_file(
            $this->clissbase->q02_inscr,
            '',
            'max(q07_seq)+1 as seq'
        ));
        $oSeq = db_utils::fieldsMemory($result, 0);

        $cltabativ->q07_inscr = $this->clissbase->q02_inscr;
        $cltabativ->q07_seq = (is_null($oSeq) || $oSeq->seq == '') ? 1 : $oSeq->seq;
        $cltabativ->q07_ativ = $idAtividade;
        $cltabativ->q07_quant = 1;
        $cltabativ->q07_perman = "true";
        $cltabativ->q07_datain = $dataInicio;
        $cltabativ->q07_tipbx = "0";
        $cltabativ->q07_imprimealvara = "Sim";
        // $cltabativ->q07_datafi = date('Y-m-d', strtotime($dataInicio . ' + 1 year'));
        $cltabativ->q07_datafi = null;
        $cltabativ->incluir($this->clissbase->q02_inscr, $cltabativ->q07_seq);

        if ($cltabativ->erro_status == 0) {
            throw new Exception($cltabativ->erro_msg);
        }

        if ($principal) {
            $this->incluirAtividadePrincipal($cltabativ->q07_seq);
        }

        return $cltabativ;
    }

    /**
     * @throws Exception
     */
    private function incluirAtividadePrincipal($q07_seq)
    {
        $clativprinc = new cl_ativprinc;
        $clativprinc->sql_record($clativprinc->sql_query_file($this->clissbase->q02_inscr));
        if ($clativprinc->numrows > 0) {
            $clativprinc->q88_inscr = $this->clissbase->q02_inscr;
            $clativprinc->excluir($this->clissbase->q02_inscr);
            if ($clativprinc->erro_status == 0) {
                throw new Exception($clativprinc->erro_msg);
            }
        }

        $clativprinc->q88_inscr = $this->clissbase->q02_inscr;
        $clativprinc->q88_seq = $q07_seq;
        $clativprinc->incluir($this->clissbase->q02_inscr);
        if ($clativprinc->erro_status == 0) {
            throw new Exception($clativprinc->erro_msg);
        }

        return $clativprinc;
    }

    /**
     * @throws Exception
     */
    private function atualizaDataIniEmpresa()
    {
        $cltabativ = new cl_tabativ;
        $result = $cltabativ->sql_record($cltabativ->sql_query_file(
            '',
            '',
            'min(q07_datain) as datainicial',
            '',
            ' q07_inscr = ' . $this->clissbase->q02_inscr
        ));

        if ($cltabativ->numrows > 0) {
            $datainicial = db_utils::fieldsMemory($result, 0)->datainicial;
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

    /**
     * @throws Exception
     */
    private function incluirSocios()
    {
        if ($this->acao == ProcessoEletronicoHelper::ACAO_ALVARA_EMPRESA) {
            foreach ($this->dados->empresa->socios as $key => $socio) {
                $valor_capital = preg_replace('/[^\d\.,]/', '', $this->getAtribute($socio->valor_capital));
                if (strpos($valor_capital, ",")) {
                    $valor_capital = str_replace(array(".", ","), array("", "."), $valor_capital);
                }

                $iQualificacao = isset($socio->qualificacao) ? $this->getAtribute($socio->qualificacao) : null;

                $this->incluirSocio(
                    $this->cgms['cgmEmpresa']->getCodigo(),
                    $this->cgms['cgmSocios'][$key]->getCodigo(),
                    $valor_capital ?: 0,
                    $this->getAtribute($socio->tipo_socio),
                    $iQualificacao
                );
            }
        } elseif ($this->acao == ProcessoEletronicoHelper::ACAO_ALVARA_MEI) {
            foreach ((array)$this->dados->empresa->responsavel_mei as $key => $responsavel) {
                $valor_capital = preg_replace('/[^\d\.,]/', '', $this->getAtribute($responsavel->valor_capital));
                if (strpos($valor_capital, ",")) {
                    $valor_capital = str_replace(array(".", ","), array("", "."), $valor_capital);
                }

                $iQualificacao = isset($socio->qualificacao) ? $this->getAtribute($socio->qualificacao) : null;

                $this->incluirSocio(
                    $this->cgms['cgmEmpresa']->getCodigo(),
                    $this->cgms['cgmResponsavel'][$key]->getCodigo(),
                    $valor_capital ?: 0,
                    $this->getAtribute($responsavel->tipo_socio),
                    $iQualificacao
                );
            }
        }
    }

    /**
     * @param $cgmEmpresa
     * @param $cgmSocio
     * @param $percentual
     * @param $tipo
     * @param $qualificacao
     * @return cl_socios
     * @throws Exception
     */
    private function incluirSocio(
        $cgmEmpresa,
        $cgmSocio,
        $percentual,
        $tipo,
        $qualificacao
    ) {
        $clsocios = new cl_socios();

        $clsocios->q95_cgmpri = $cgmEmpresa;
        $clsocios->q95_numcgm = $cgmSocio;
        $clsocios->q95_perc = str_replace(',', '.', (!is_null($percentual) ? $percentual : '0'));
        $clsocios->q95_tipo = $tipo;
        $clsocios->q95_qualificacaosocio = $qualificacao;

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

    /**
     * @throws Exception
     */
    private function incluirAlvara()
    {
        $clissalvara = new cl_issalvara;

        $classificacaoGrauRisco = ProcessoEletronicoHelper::getClassificacaoGrauRisco($this->grauRisco);

        $clissalvara->q123_isstipoalvara = ProcessoEletronicoHelper::getTipoAlvara(
            $this->parameterBag,
            $classificacaoGrauRisco
        );

        $clissalvara->q123_inscr = $this->clissbase->q02_inscr;
        $clissalvara->q123_dtinclusao = date("Y-m-d", db_getsession('DB_datausu'));
        $clissalvara->q123_situacao = 1;
        $clissalvara->q123_usuario = db_getsession("DB_id_usuario");
        $clissalvara->q123_geradoautomatico = "true";
        $clissalvara->incluir(null);

        if ($clissalvara->erro_status == '0') {
            throw new Exception($clissalvara->erro_msg);
        }

        $this->alvara = new Alvara($clissalvara->q123_sequencial);
    }

    private function inserirDocumentos()
    {
        if (!isset($this->dados->documentos)) {
            return;
        }

        $oLiberarAlvara = new AlvaraMovimentacaoLiberacao($this->alvara->getCodigo());
        foreach ($this->dados->documentos as $documento) {
            if ($documento->tipo == 'atividades') {
                $oLiberarAlvara->addDocumento($documento->codigo_vinculo);
            }
        }

        $oLiberarAlvara->gravaDocumentos();
    }

    public function getInscricao()
    {
        return $this->clissbase->q02_inscr;
    }

    /**
     * @throws ParameterException
     * @throws Exception
     */
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
     * @param $categoria
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
        if (isset($this->camposAdicionais->$sSecao->$sCampo)) {
            return $this->camposAdicionais->$sSecao->$sCampo;
        }

        return $sDefault;
    }

    private function verificaSeJaExisteSocio($cgmEmpresa, $cgmSocio)
    {
        $clsocios = new cl_socios();
        $sql = $clsocios->sql_query_file($cgmEmpresa, $cgmSocio);
        $rs = db_query($sql);
        if (pg_num_rows($rs) > 0) {
            return true;
        }
        return false;
    }

    private function getDataIfPossible($object, $attribute)
    {
        if (empty($object)) {
            return null;
        }

        $aAttribute = explode(".", $attribute);
        $findCount = 0;

        foreach ($aAttribute as $sAttribute) {
            if (isset($object->$sAttribute)) {
                $object = $object->$sAttribute;
                $findCount++;
            } else {
                break;
            }
        }

        return $findCount == count($aAttribute) ? $object : null;
    }
}
