<?php

use ECidade\Tributario\Arrecadacao\Model\TaxaEspecifica as TaxaEspecificaModel;
use ECidade\Tributario\Arrecadacao\Repository\TaxaEspecifica as TaxaEspecificaRepository;
use ECidade\Tributario\Arrecadacao\Service\TaxaEspecifica as TaxaEspecificaService;
use ECidade\Tributario\Caixa\Model\Arretipo;
use ECidade\Tributario\Caixa\Enum\ArretipoEnum;
use ECidade\Tributario\Caixa\Repository\RecibopagaRepository;
use ECidade\Tributario\Library\DataBase;

/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

/**
 * Classe responsável pela manutenção de recibos do sistema
 * @package Caixa
 */
class Recibo
{
    const TIPOEMISSAO_RECIBO_AVULSO = 1;
    const TIPOEMISSAO_RECIBO_CGF = 2;

    /**
     * Autenticação do recibo de retenção oriunda de outra instituição
     * @var int
     */
    const AUTENTICACAO_RETENCAO_OUTRA_INSTITUICAO = 7;

    /**
     * Estorno do recibo de retenção oriunda de outra instituição
     * @var int
     */
    const ESTORNO_RETENCAO_OUTRA_INSTITUICAO = 8;

    /**
     * Código da arrecadacao
     *
     * @var integer
     */
    private $iNumpre = null;

    /**
     * Array com o código das matriculas que devem ser vinculadas ao recibo (iptubase.j02_matric)
     *
     * @var array
     */
    private $aMatricula = array();

    /**
     * Array com os código das Inscrições municipais que devem ser vinculadas ao recibo (issbase.q02_inscr)
     *
     * @var array
     */
    private $aInscricao = array();

    /**
     * Array com os códigos dos cgms que devem ser vinculados ao recibo (cgm.z01_numcgm)
     *
     * @var array
     */
    private $aVinculoCgm = array();

    /**
     * Tipo de Recibo a ser Gerado
     *
     * @var integer
     */
    private $iTipoRecibo = null;

    /**
     * Receitas do recibo.
     *
     * @var array;
     */
    private $aReceitas = array();

    /**
     * Receitas de custas do recibo.
     *
     * @var array;
     */
    private $aReceitaCusta = array();

    /**
     * Receitas de custas de parcelamentos contidos no recibo.
     *
     * @var array;
     */
    private $aReceitaCustaParcelamento = array();

    /**
     * Tipo de recibo que sera emitido. 1 - recibo protocolo
     *
     * @var integer
     */
    private $iTipoEmissao = null;

    /**
     * Código do Cgm do Recibo
     *
     * @var integer
     */
    private $iNumCgm = null;

    /**
     * Data de emissao do Recibo
     *
     * @var string
     */
    private $dtRecibo = null;

    /**
     * exercicio
     *
     * @var integer
     */
    private $iAnoUsu = null;

    /**
     * Código da conta pagadora do recibo
     *
     * @var integer
     */
    private $iConta = null;

    /**
     * Código do Grupo da autenticação
     *
     * @var integer
     */
    private $iCodigoGrupoAutenticacao = 0;

    /**
     * Numpres de Debitos em aberto
     * que serao utilizados na confeção do recibo;
     * @var array;
     */
    private $aNumpres = array();

    /**
     * Recursos que o recibo possui.
     *
     * @var array de objetos do tipo stdclass
     */
    private $aRecursos = array();

    /**
     * Numero de banco gerado pelo sistema.
     *
     * @var string
     */
    private $sNumBco = "";

    /**
     * Código do histórico
     *
     * Definido 502 = RECIBO PROTOCOLO como padrão
     * @var integer
     */
    private $iCodigoHistorico = 502;

    /**
     * Historico do recibo usado na consulta geral financeira e na reemissão do recibo
     *
     * @var string
     */
    private $sHistorico = "";

    /**
     * Data de vencimento do recibo que será passado para a função fc_recibo
     *
     * @var string
     */
    private $dtVencRecibo = null;

    /**
     * Desconto utilizado
     *
     * @var array
     */
    private $aDescReciboWeb = array();

    /**
     * Caracteristica peculiar     *
     * @var integer
     */
    private $iCaracteristicaPeculiar;

    /**
     * Válida se a emissão esta sendo feita por Carnê Banco
     *
     * @var bool
     */
    private $emiteCarneBanco;

    /**
     * Convenio associado ao recibo
     *
     * @var convenio
     */
    private $convenio;

    /**
     * CGM que sera exibido no PDF do recibo
     * @var CgmBase
     */
    private $cgmExibicao;

    /**
     * $arretipo
     *
     * @var Arretipo
     */
    private $arretipo;

    /**
     * @var integer
     */
    private $codigoInstituicao;

    /**
     * @var integer
     */
    private $cadTipoMod;

    /**
     * @var integer
     */
    private $minNumpar;

    /**
     * @var integer
     */
    private $maxNumpar;

    /**
     * @var integer
     */
    private $quantidadeParcelas = 1;

    /**
     * @var integer
     */
    private $parcelaAtual = 1;

    /**
     * O objeto que foi usado para buscar os débitos do recibo.
     * Essencialmente, é ou o Cgm, ou a Inscrição, ou a Matrícula usada
     * na hora de pesquisar alguém na consulta geral financeira.
     *
     * @var CgmBase|\ECidade\Tributario\Issqn\Model\Issbase|\ECidade\Tributario\Cadastro\Model\Iptubase
     */
    private $identificacao;

    /**
     * Processos do foro das iniciais envolvidas no boleto
     *
     * @var int[]
     */
    private $processosForo;

    private $executaLancamentoContabil = true;

    private $lPartilhaPagaMigracao;
    /**
     * @var bool
     */
    private $geracaoTaxaExpediente = true;

    /**
     * Cria um novo recibo
     *
     * @param integer $iTipoEmissao tipo da emissao do recibo = 1 recibo avulso, 2 - recibo d CGF
     * @param integer $iNumCgm Código do Cgm para que está sendo emitido o Recibo
     * @param int $iTipo
     * @param null $iNumnov
     * @throws Exception
     */
    function __construct($iTipoEmissao = null, $iNumCgm = null, $iTipo = 1, $iNumnov = null)
    {
        if ($iNumnov != null) {
            $oDaoArrebanco = new cl_arrebanco();
            $oDaoReciboPaga = new cl_recibopaga();
            $sSqlArrebanco = $oDaoArrebanco->sql_queryRecibo($iNumnov);
            $rsArrebanco = $oDaoArrebanco->sql_record($sSqlArrebanco);

            if ($oDaoArrebanco->numrows > 0) {
                $oRecibo = db_utils::fieldsMemory($rsArrebanco, 0);
                $this->setNumBco($oRecibo->k00_numbco);
                $this->setHistorico($oRecibo->k00_histtxt);
                $this->setDataRecibo($oRecibo->k00_dtoper);
                $this->setNumnov($oRecibo->k00_numnov);
                $this->setCgm($oRecibo->k00_numcgm);
                $this->setDataVencimentoRecibo($oRecibo->k00_dtpaga);
                $this->setExercicioRecibo(date("Y", strtotime($oRecibo->k00_dtoper)));
                $this->setConta($oRecibo->k00_conta);
                $this->setTipoEmissao($oRecibo->tipo_emissao);
                $sSqlDebitosRecibo = $oDaoReciboPaga->sql_query_file(
                    null,
                    " distinct k00_numpre, k00_numpar ",
                    null,
                    " k00_numnov = {$iNumnov} "
                );
                $rsDebitosRecibo = $oDaoReciboPaga->sql_record($sSqlDebitosRecibo);
            }
        } else {
            $this->dtRecibo = date("Y-m-d", db_getsession("DB_datausu"));
            $this->dtVencRecibo = date("Y-m-d", db_getsession("DB_datausu"));
            $this->iAnoUsu = date("Y", db_getsession("DB_datausu"));
            $this->iTipoEmissao = $iTipoEmissao;
            $this->iNumCgm = $iNumCgm;
            $this->iTipoDBreciboWeb = $iTipo;
            $this->emiteCarneBanco = false;
            /*
             * Definimos o tipo de recibo conforme a instituição
             */
            $sSqlTiporecibo = "select k03_reciboprot         ";
            $sSqlTiporecibo .= "  from numpref                ";
            $sSqlTiporecibo .= " where k03_anousu =  " . db_getsession("DB_anousu");
            $sSqlTiporecibo .= "   and k03_instit =  " . db_getsession("DB_instit");
            $rsTipoRecibo = db_query($sSqlTiporecibo);
            if (pg_num_rows($rsTipoRecibo) == 0) {
                throw  new Exception("Erro [1] - Não há Configuração do tributário para o ano e instituição Correntes");
            }
            $this->iTipoRecibo = db_utils::fieldsMemory($rsTipoRecibo, 0)->k03_reciboprot;
        }
    }

    public function setCgm($cgm)
    {
        $this->iNumCgm = $cgm;
    }

    public function getCgm()
    {
        return $this->iNumCgm;
    }
    /**
     * Define o numero de banco gerado pelo sistema
     *
     * @param string $sNumBco
     */
    function setNumBco($sNumBco)
    {
        $this->sNumBco = $sNumBco;
    }

    /**
     * @return bool
     */
    public function executaLancamentoContabil()
    {
        return $this->executaLancamentoContabil;
    }

    /**
     * @param bool $executaLancamentoContabil
     */
    public function setExecutaLancamentoContabil($executaLancamentoContabil)
    {
        $this->executaLancamentoContabil = $executaLancamentoContabil;
    }

    /**
     * Define valor aDescReciboWeb
     *
     * @param integer $iNumpre
     * @param integer $iNumpar
     */

    function setDescontoReciboWeb($iNumpre, $iNumpar, $nValorDesconto)
    {
        $oDesconto = new stdClass();
        $oDesconto->iNumpre = $iNumpre;
        $oDesconto->iNumpar = $iNumpar;
        $oDesconto->nValorDesconto = $nValorDesconto;

        $this->aDescReciboWeb[] = $oDesconto;
    }

    /**
     * Retorna valor de desconto
     *
     * @param integer $iNumpre
     * @param integer $iNumpar
     * @return integer
     */
    function getDescontoReciboWeb($iNumpre, $iNumpar)
    {
        $nDesconto = 0;

        foreach ($this->aDescReciboWeb as $oDesconto) {
            if ($oDesconto->iNumpre == $iNumpre && $oDesconto->iNumpar == $iNumpar) {
                $nDesconto = $oDesconto->nValorDesconto;
            }
        }

        return $nDesconto;
    }

    /**
     * Retorna todos os descontos adicionados ao recibo
     * @return array
     */
    public function getTodosDescontosReciboWeb()
    {
        return $this->aDescReciboWeb;
    }

    /**
     * Define o código dos cgms envolvidos no recibo
     *
     * @param integer $iCgm
     */
    function setVinculoCgm($iCgm)
    {
        $this->aVinculoCgm[] = $iCgm;
    }

    /**
     * Retorna os cgms que devem ter vinculo com o numpre
     *
     * @return array
     */
    function getVinculoCgm()
    {
        return $this->aVinculoCgm;
    }

    /**
     * Define o código da matricula
     *
     * @param integer $iMatricula
     */
    function setMatricula($iMatricula)
    {
        $this->aMatricula[] = $iMatricula;
    }

    /**
     * Retorna o codigo da matricula definida para o recibo.
     *
     * @return array
     */
    function getMatricula()
    {
        return $this->aMatricula;
    }

    /**
     * Define o código da Inscricao
     *
     * @param integer $iInscricao
     */
    function setInscricao($iInscricao)
    {
        $this->aInscricao[] = $iInscricao;
    }

    /**
     * Retorna o codigo da Inscricao definida para o recibo.
     *
     * @return array
     */
    function getInscricao()
    {
        return $this->aInscricao;
    }

    /**
     * Define a conta pagadora
     *
     * @param integer $iConta
     */
    function setConta($iConta)
    {
        $this->iConta = $iConta;
    }

    /**
     * Retorna a conta definida pelo usuario
     *
     * @return integer
     */
    function getConta()
    {
        return $this->iConta;
    }

    /**
     * Define o Grupo de Autenticação (corgrupo.k104_sequencial);
     *
     * @param integer $iCorGrupo Código do Grupo (corgrupo.k104_sequencial)
     */
    function setGrupoAutenticacao($iCorGrupo)
    {
        $this->iCodigoGrupoAutenticacao = $iCorGrupo;
    }

    /**
     * Retorna o grupo de autenticação definido.
     *
     * @return integer
     */
    function getGrupoArrecadacao()
    {
        return $this->iCodigoGrupoAutenticacao;
    }

    /**
     * Define o Historico do Recibo;
     *
     * @param string $sHistorico Historico do pagamento
     */
    function setHistorico($sHistorico)
    {
        $this->sHistorico = addslashes($sHistorico);
    }

    /**
     * Retorna o Historico do recibo;
     *
     * @return string
     */
    function getHistorico($limiteLinhas = null)
    {
        if ($limiteLinhas === null) {
            return $this->sHistorico;
        }

        $linhas = explode("\n", $this->sHistorico);

        return implode("\n", array_slice($linhas, 0, $limiteLinhas));
    }

    /**
     * @return string
     */
    public function getDataRecibo()
    {
        return $this->dtRecibo;
    }

    /**
     * Define a data do recibo
     * @param string $dtRecibo
     */
    public function setDataRecibo($dtRecibo)
    {
        $this->dtRecibo = $dtRecibo;
    }

    /**
     * Define o Tipo de emissao;
     *
     * @param string $iTipoEmissao tipo de emissao
     */
    function setTipoEmissao($iTipoEmissao)
    {
        $this->iTipoEmissao = $iTipoEmissao;
    }

    /**
     * Retorna o tipo de emissao;
     *
     * @return string
     */
    function getTipoEmissao()
    {
        return $this->iTipoEmissao;
    }

    /**
     * Define o código do histórico
     * @param $iCodigoHistorico
     */
    public function setCodigoHistorico($iCodigoHistorico)
    {
        $this->iCodigoHistorico = $iCodigoHistorico;
    }

    /**
     * Retorna o código do histórico
     * @return integer
     */
    public function getCodigoHistorico()
    {
        return $this->iCodigoHistorico;
    }

    /**
     * Define o código do tipo
     * @param $iCodigoTipo
     */
    public function setCodigoTipo($iCodigoTipo)
    {
        $this->iTipoRecibo = $iCodigoTipo;
    }

    /**
     * Retorna o código do tipo
     * @return integer
     */
    public function getCodigoTipo()
    {
        return $this->iTipoRecibo;
    }

    /**
     * Define se estamos emitindo um Carnê Banco
     */
    public function setEmiteCarneBanco($emiteCarneBanco)
    {
        $this->emiteCarneBanco = $emiteCarneBanco;
    }

    /**
     * adiciona um numpre/numpar ao recibo;
     *
     * @param integer $iNumpre Código de arrecação
     * @param integer $iNumpar parcela
     * @throws Exception
     */
    function addNumpre($iNumpre, $iNumpar)
    {
        if (trim($iNumpre) == '') {
            throw new Exception(" Erro [1] Numpre não pode ser vazio");
        }

        if (trim($iNumpar) == '') {
            throw new Exception(" Erro [2] Numpar não pode ser vazio");
        }

        $sSqlArrecad = "select distinct k00_numpre,";
        if ($iNumpar <> 0) {
            $sSqlArrecad .= " k00_numpar, ";
        } else {
            $sSqlArrecad .= " 0 as k00_numpar, ";
        }
        $sSqlArrecad .= "       k00_tipo, k00_valor ";
        $sSqlArrecad .= "  from arrecad ";
        $sSqlArrecad .= " where k00_numpre = {$iNumpre} ";
        if ($iNumpar <> 0) {
            $sSqlArrecad .= "   and k00_numpar = {$iNumpar} ";
        }
        $rsArrecad = db_query($sSqlArrecad);
        if (pg_num_rows($rsArrecad) == 0) {

            throw new Exception(" Erro [3] Débito :{$iNumpre}  Parcela: {$iNumpar}  não encontrado!");
        }
        $oNumpre = db_utils::fieldsMemory($rsArrecad, 0);
        if (!in_array($oNumpre, $this->aNumpres)) {
            $this->aNumpres[] = $oNumpre;
        }
        unset($oNumpre);
    }

    /**
     * Adiciona uma receita ao recibo.
     *
     * @param integer $iCodRec
     * @param float $nValorReceita
     * @param integer $iCodSubReceita
     * @param null $iCaracteristicaPeculiar
     * @param null $iHistorico
     * @param null $sDataPaga
     * @return bool
     */
    function adicionarReceita($iCodRec, $nValorReceita, $iCodSubReceita = 0, $iCaracteristicaPeculiar = null, $iHistorico = null, $sDataPaga = null, $bIsento = false)
    {
        $oReceita = new stdClass();
        $oReceita->iCodRec = $iCodRec;
        $oReceita->nValorReceita = $nValorReceita;
        $oReceita->iCodSubReceita = $iCodSubReceita;
        $oReceita->iCaracteristicaPeculiar = $iCaracteristicaPeculiar;
        $oReceita->iHistorico = $this->getCodigoHistorico();
        $oReceita->sDataPaga = $sDataPaga;
        if (!empty($iHistorico)) {
            $oReceita->iHistorico = $iHistorico;
        }
        $oReceita->bIsento = $bIsento;
        $this->aReceitas[] = $oReceita;
        return true;
    }

    /**
     * Adiciona um recurso ao recibo.
     *
     * @param integer $iRecurso
     */
    function adicionarRecurso($iRecurso)
    {
        if (!in_array($iRecurso, $this->aRecursos)) {
            $this->aRecursos[] = $iRecurso;
        }
    }

    /**
     * Emite novo recibo
     * @throws Exception
     * @return boolean
     */
    function emiteRecibo($lCobrancaRegistrada = false, $lAlterarDataVencimento = true, $iCodigoConvenio = null)
    {
        if (!db_utils::inTransaction()) {
            throw new Exception("Erro [0] - Não existe Transação Ativa.");
        }

        if (!empty($this->dtVencRecibo) && $lCobrancaRegistrada) {
            if (!empty($iCodigoConvenio)) {
                $oDaoConvenio = new \cl_cadconvenio();
                $sSqConvenio  = $oDaoConvenio->sql_query_convenio_cobranca($iCodigoConvenio, "ar12_sequencial");
                $rsConvenio   = \db_query($sSqConvenio);

                if (empty($rsConvenio) || !pg_num_rows($rsConvenio)) {
                    throw new Exception("Erro ao buscar dados do convenio.");
                }

                $oDadosConvenio = db_utils::fieldsMemory($rsConvenio, 0);

                if (
                    $oDadosConvenio->ar12_sequencial == convenio::TIPO_CONVENIO_COBRANCA_REGISTRADA &&
                    date('Ymd', strtotime($this->dtVencRecibo)) == date('Ymd', strtotime($this->dtRecibo))
                ) {
                    $this->dtVencRecibo = date('Y-m-d', strtotime("+1 day", strtotime($this->dtVencRecibo)));
                }
            }

            $sSqlVencimento = "select fc_proximo_dia_util('{$this->dtVencRecibo}'::date) as vencimento";
            $rsVencimento = db_query($sSqlVencimento);

            if (!$rsVencimento) {
                throw new DBException("Erro ao buscar dia útil para o vencimento do recibo.");
            }

            $oVencimento = db_utils::fieldsMemory($rsVencimento, 0);

            if (!empty($this->aNumpres[0]->k00_tipo)) {
                $iTipoDebito = $this->aNumpres[0]->k00_tipo;
                $sSqlExercicioCarne = "select k00_exercicioscarne from arretipo where k00_tipo = {$iTipoDebito}";

                $oArreTipo = db_utils::fieldsmemory(db_query($sSqlExercicioCarne), 0);
            }

            // Verifica se o ano do vencimento é maior que o ano atual somado ao período de tolerância de emissão de carnês do tipo de débito.
            // Caso seja, pega o ultimo dia útil do ano
            $iExercicioCarne = (!empty($oArreTipo->k00_exercicioscarne) && $this->emiteCarneBanco ? $oArreTipo->k00_exercicioscarne : 0);
            $iAnoAtual = (int)date('Y', db_getsession('DB_datausu')) + $iExercicioCarne;
            $iAnoRecibo = (int)date('Y', strtotime($oVencimento->vencimento));

            if ($iAnoRecibo > $iAnoAtual && $lAlterarDataVencimento) {
                $sSqlVencimento = "select fc_ultimo_dia_util('{$iAnoAtual}-12-31'::date) as vencimento";
                $rsVencimento = db_query($sSqlVencimento);

                if (!$rsVencimento) {
                    throw new DBException("Erro ao buscar ultimo dia útil para o vencimento do recibo.");
                }

                $oVencimento = db_utils::fieldsMemory($rsVencimento, 0);
            }

            $this->dtVencRecibo = $oVencimento->vencimento;
        }

        if ($this->iTipoEmissao == 1) {
            if (count($this->aReceitas) == 0) {
                throw new Exception("Erro [1] - Recibo sem Receitas Configuradas!");
            }

            $rsNumpre = db_query("select nextval('numpref_k03_numpre_seq') as k03_numpre");
            $this->iNumpre = db_utils::fieldsMemory($rsNumpre, 0)->k03_numpre;
            $iDigitoNumpre = db_sqlformatar($this->iNumpre, 8, '0') . "001001";
            $iDigitoNumpre = db_CalculaDV($iDigitoNumpre);

            foreach ($this->aReceitas as $oReceita) {
                if ($oReceita->nValorReceita == 0 && !$oReceita->bIsento) {
                    throw new Exception("Erro [2] - Receita {$oReceita->iCodRec} com valor incorreto({$oReceita->nValorReceita})!");
                }

                $dtVencimento = $this->dtRecibo;
                if (isset($this->dtVencRecibo) && $this->dtVencRecibo != null) {
                    $dtVencimento = $this->dtVencRecibo;
                }

                $sSqlRec = "insert into recibo (k00_numcgm,
		  		                      			  k00_dtoper,
							                          k00_receit,
							                          k00_hist  ,
							                          k00_valor ,
							                          k00_dtvenc,
							                          k00_numpre,
							                          k00_numpar,
							                          k00_numtot,
							                          k00_numdig,
							                          k00_tipo  ,
							                          k00_numnov,
							                          k00_codsubrec)
					                      values ({$this->iNumCgm},
							                          '{$this->dtRecibo}',
							                          {$oReceita->iCodRec},
							                          {$oReceita->iHistorico},
							                          {$oReceita->nValorReceita},
							                          '{$dtVencimento}',
							                          {$this->iNumpre},
							                          1,
							                          1,
							                          {$iDigitoNumpre},
							                          {$this->iTipoRecibo},
							                          0,
							                          {$oReceita->iCodSubReceita})";


                $rsInclusaoReceita = db_query($sSqlRec);
                if (!$rsInclusaoReceita) {
                    throw new Exception("Erro [3] - Não foi possível incluir Receita.");
                }
                if (isset($oReceita->iCaracteristicaPeculiar) && $oReceita->iCaracteristicaPeculiar != "") {
                    $oDaoReciboConCarPeculiar = db_utils::getDao("reciboconcarpeculiar");
                    $oDaoReciboConCarPeculiar->k130_numpre = $this->iNumpre;
                    $oDaoReciboConCarPeculiar->k130_numpar = 1;
                    $oDaoReciboConCarPeculiar->k130_receit = $oReceita->iCodRec;
                    $oDaoReciboConCarPeculiar->k130_concarpeculiar = "{$oReceita->iCaracteristicaPeculiar}";
                    $oDaoReciboConCarPeculiar->incluir(null);
                    if ($oDaoReciboConCarPeculiar->erro_status == 0) {
                        throw new Exception("Erro [3] - Não foi possível incluir concarpeculiar.\n{$oDaoReciboConCarPeculiar->erro_msg}");
                    }
                }
            }

            $cl_reciboavulsoboleto = new \cl_reciboavulsoboleto();

            $cl_reciboavulsoboleto->k201_numpre = $this->iNumpre;
            $cl_reciboavulsoboleto->k201_data = date("Y-m-d H:i:s");
            $cl_reciboavulsoboleto->k201_usuario = db_getsession("DB_id_usuario");
            $cl_reciboavulsoboleto->k201_ip = db_getsession("DB_ip");

            $cl_reciboavulsoboleto->incluir();

            if ($cl_reciboavulsoboleto->erro_status == "0") {
                throw new \Exception($cl_reciboavulsoboleto->erro_msg);
            }

            if (count($this->aVinculoCgm) > 0) {
                $oDaoArreNumcgm = db_utils::getDao("arrenumcgm");

                for ($iIndCgm = 0; $iIndCgm < count($this->aVinculoCgm); $iIndCgm++) {
                    $oDaoArreNumcgm->incluir($this->aVinculoCgm[$iIndCgm], $this->iNumpre);
                    if ($oDaoArreNumcgm->erro_status == 0) {
                        throw new Exception("Erro [3.1] - Não foi possuivel realizar vinculo do recibo com o cgm.\n{$oDaoArreNumcgm->erro_msg}");
                    }
                }
            }

            if (count($this->aMatricula) > 0) {
                $oDaoArreMatric = db_utils::getDao("arrematric");
                for ($iIndMatricula = 0; $iIndMatricula < count($this->aMatricula); $iIndMatricula++) {
                    $oDaoArreMatric->k00_perc = round(100 / count($this->aMatricula), 2);
                    $oDaoArreMatric->incluir($this->iNumpre, $this->aMatricula[$iIndMatricula]);
                    if ($oDaoArreMatric->erro_status == 0) {
                        throw new Exception("Erro [3.2] - Não foi possível realizar vinculo do recibo com a matricula.\n{$oDaoArreMatric->erro_msg}");
                    }
                }
            }

            if (count($this->aInscricao) > 0) {
                $oDaoArreInscr = db_utils::getDao("arrematric");

                for ($iIndInscricao = 0; $iIndInscricao < count($this->aInscricao); $iIndInscricao++) {
                    $oDaoArreInscr->k00_perc = round(100 / count($this->aInscricao), 2);
                    $oDaoArreInscr->incluir($this->iNumpre, $this->aInscricao[$iIndInscricao]);
                    if ($oDaoArreInscr->erro_status == 0) {
                        throw new Exception("Erro [3.3] - Não foi possuivel realizar vinculo do recibo com a inscrição.\n{$oDaoArreInscr->erro_msg}");
                    }
                }
            }

            $sSqlhistorico = "insert into arrehist (k00_numpre,
                                              k00_numpar,
                         	                    k00_hist,
			                                        k00_dtoper,
			                                        k00_hora,
			                                        k00_id_usuario,
			                                        k00_histtxt,
			                                        k00_limithist,
			                                        k00_idhist)
			                                values ({$this->iNumpre},
			                                        0,
			                                        {$this->getCodigoHistorico()},
			                                        '{$this->dtRecibo}',
			      		                              '" . date("H:i") . "',
			      		                              " . db_getsession("DB_id_usuario") . ",
			                                        '" . $this->getHistorico() . "',
			                                        null,
			                                        nextval('arrehist_k00_idhist_seq'))";

            $rsHistorico = db_query($sSqlhistorico);
            if (!$rsHistorico) {
                throw new Exception("Erro [4] - Não foi possivel informar histórico do Recibo");
            }

            /**
             * Incluimos os recursos no recibo
             */
            for ($i = 0; $i < count($this->aRecursos); $i++) {
                $sInsertRecursos = "insert into reciborecurso (k00_sequen,
						   	                                      k00_numpre,
							                                        k00_recurso)
		  		                                    values (nextval('reciborecurso_k00_sequen_seq'),
				                                              {$this->iNumpre},
				                                              {$this->aRecursos[$i]})";
                $rsRecurso = db_query($sInsertRecursos);
                if (!$rsRecurso) {
                    throw new Exception("Erro [4] - Não foi possivel informar recursos do Recibo");
                }
            }
        } else if ($this->iTipoEmissao == 2) {
            if (count($this->aNumpres) == 0) {
                throw new Exception("Erro [5] - Não há debitos Adicionados no recibo");
            }

            /**
             * Pesquisamos a informação do banco para o tipo do debito.
             */
            if (empty($this->iNumpre)) {
                $rsNumpre = db_query("select nextval('numpref_k03_numpre_seq') as k03_numpre");
                $this->iNumpre = db_utils::fieldsMemory($rsNumpre, 0)->k03_numpre;
            }

            $iDigitoNumpre = db_sqlformatar($this->iNumpre, 8, '0') . "001001";
            $iDigitoNumpre = db_CalculaDV($iDigitoNumpre);

            /*
             * Adicionamos o historico
             */
            if ($this->getHistorico() != "") {
                $sSqlhistorico = "insert into arrehist (k00_numpre,
                                                k00_numpar,
  			                                        k00_hist,
  			                                        k00_dtoper,
  			                                        k00_hora,
  			                                        k00_id_usuario,
  			                                        k00_histtxt,
  			                                        k00_limithist,
  			                                        k00_idhist)
  			                                values ({$this->iNumpre},
  			                                        0,
  			                                        {$this->getCodigoHistorico()},
  			                                        '{$this->dtRecibo}',
  			      		                              '" . date("H:i") . "',
  			      		                              " . db_getsession("DB_id_usuario") . ",
  			                                        '" . $this->getHistorico() . "',
  			                                        null,
  			                                        nextval('arrehist_k00_idhist_seq'))";

                $rsHistorico = db_query($sSqlhistorico);
                if (!$rsHistorico) {
                    throw new Exception("Erro [4] - Não foi possivel informar histórico do Recibo");
                }
            }

            $aDebitosPorTipo = array();
            $aDadosBanco = array();

            foreach ($this->aNumpres as $oDebito) {
                $aDebitosPorTipo[$oDebito->k00_tipo] = $oDebito->k00_tipo;
            };


            foreach ($aDebitosPorTipo as $sTipo => $oDebito) {
                $sSqlBanco = "select k00_taxaadm,k00_codbco,k00_codage,k00_descr,k00_hist1, ";
                $sSqlBanco .= "       k00_hist2,k00_hist3,k00_hist4,k00_hist5,k00_hist6, ";
                $sSqlBanco .= "       k00_hist7,k00_hist8,k03_tipo,k00_tipoagrup, ";
                $sSqlBanco .= "       '' as fc_numbco, k00_taxaespecifica ";
                $sSqlBanco .= "  from arretipo where k00_tipo ={$sTipo}";
                $rsBanco = db_query($sSqlBanco);

                if (pg_num_rows($rsBanco) == 0) {
                    throw new Exception("O código do banco não esta cadastrado no arquivo arretipo para este tipo.");
                }

                $aDadosBanco[$sTipo] = db_utils::fieldsMemory($rsBanco, 0);
            }
            /**
             * Percorremos os debitos adicionados, e cr
             */
            $numpar = [];
            foreach ($this->aNumpres as $oDebito) {
                $oBanco = $aDadosBanco[$oDebito->k00_tipo];
                $nDescontoReciboWeb = $this->getDescontoReciboWeb($oDebito->k00_numpre, $oDebito->k00_numpar);

                $sSqlReciboWeb = "insert into db_reciboweb ";
                $sSqlReciboWeb .= "            (k99_numpre,";
                $sSqlReciboWeb .= "             k99_numpar,";
                $sSqlReciboWeb .= "             k99_numpre_n,";
                $sSqlReciboWeb .= "             k99_codbco,";
                $sSqlReciboWeb .= "             k99_codage,";
                $sSqlReciboWeb .= "             k99_numbco,";
                $sSqlReciboWeb .= "             k99_desconto,";
                $sSqlReciboWeb .= "             k99_tipo,";
                $sSqlReciboWeb .= "             k99_origem ";
                $sSqlReciboWeb .= "            ) ";
                $sSqlReciboWeb .= "            values ";
                $sSqlReciboWeb .= "            ({$oDebito->k00_numpre},   ";
                $sSqlReciboWeb .= "             {$oDebito->k00_numpar},   ";
                $sSqlReciboWeb .= "             {$this->iNumpre},         ";
                $sSqlReciboWeb .= "             {$oBanco->k00_codbco},    ";
                $sSqlReciboWeb .= "             '{$oBanco->k00_codage}',  ";

                if ($this->sNumBco != "") {
                    $sSqlReciboWeb .= "             '{$this->sNumBco}', ";
                } else {
                    $sSqlReciboWeb .= "             '{$oBanco->fc_numbco}', ";
                }
                $numpar[] = $oDebito->k00_numpar;
                $sSqlReciboWeb .= "             {$nDescontoReciboWeb},{$this->iTipoDBreciboWeb},1)";
                $rsReciboWeb = db_query($sSqlReciboWeb);

                if (!$rsReciboWeb) {
                    throw  new Exception("Erro [6] - Nao foi possivel emitir recibo!\n" . pg_last_error());
                }
            }

            /**
             * rodamos a funcao fc_recibo no numprenovo que criamos
             */
            $sFcRecibo = "select * from fc_recibo({$this->iNumpre}, '{$this->dtVencRecibo}'::date,'{$this->dtVencRecibo}'::date, {$this->iAnoUsu})";
            $rsFcRecibo = db_query($sFcRecibo);

            if (!$rsFcRecibo) {
                throw new Exception("Erro [7] Não foi possivel Emitir recibo.\n" . pg_last_error());
            } else {
                $oFcRecibo = db_utils::fieldsMemory($rsFcRecibo, 0);
                if (isset($oFcRecibo->rlerro) && $oFcRecibo->rlerro == 't') {
                    throw new Exception($oFcRecibo->rvmensagem);
                }
            }

            $dadosDebitoAtual = current($aDadosBanco);


            /**
             * Verifica se algum dos tipos de debitos estão configurados para ccobrar taxa adm
             */
            $gerarTaxaADM = false;
            foreach ($aDadosBanco as $dadoDebitoAtual) {
               if($dadoDebitoAtual->k00_taxaadm == 't'){
                    $gerarTaxaADM = true;
               }
            }

            if (($sTipo == 13 or $sTipo == 14) && $gerarTaxaADM) {
                $this->gerarTaxaADM($this->iNumpre, $sTipo, $numpar, $oDebito->k00_numpre);
            }

            $this->gerarTaxaExpediente($this->iNumpre, $dadosDebitoAtual->k00_taxaespecifica);

            /**
             * insere na tabela de Cabecalho da recibopaga
             */

            $oDaoReciboPagaBoleto = db_utils::getDao("recibopagaboleto");
            $oDaoReciboPagaBoleto->k138_numnov = $this->getNumpreRecibo();
            $oDaoReciboPagaBoleto->k138_data = date("Y-m-d");
            $oDaoReciboPagaBoleto->k138_hora = date("H:i:s");
            $oDaoReciboPagaBoleto->k138_usuario = db_getsession("DB_id_usuario");
            $oDaoReciboPagaBoleto->incluir("");

            if ((int)$oDaoReciboPagaBoleto->erro_status == 0) {
                throw new Exception("Gravar dados recibopagaboleto: \n" . $oDaoReciboPagaBoleto->erro_msg);
            }
        }
        return true;
    }

    /**
     * Adiciona uma receita de custas ao recibo.
     *
     * @param integer $iReceita
     * @param float $nValorReceita
     * @param integer $iHistorico
     * @param Taxa $taxa
     * @return bool
     */
    public function adicionarReceitaCusta($iReceita, $nValorReceita, $iHistorico, $taxa = null)
    {
        if (!empty($this->aReceitaCusta[$iReceita])) {
            $oReceita = $this->aReceitaCusta[$iReceita];
            $nValorReceita = $oReceita->nValorReceita + $nValorReceita;
        }


        $oReceita = new stdClass();
        $oReceita->iReceita = $iReceita;
        $oReceita->nValorReceita = $nValorReceita;
        $oReceita->iHistorico = $iHistorico;
        $oReceita->taxa = $taxa;

        $this->aReceitaCusta[$iReceita] = $oReceita;

        return true;
    }

    /**
     * Adiciona uma receita de custas de parcelamento ao recibo.
     *
     * @param integer $iNumpre
     * @param integer $iNumpar
     * @param Taxa $taxa
     * @param float $nValorReceita
     * @param integer $iHistorico
     * @return bool
     */
    public function adicionarReceitaCustaParcelamento($iNumpre, $iNumpar, $taxa, $nValorReceita, $iHistorico)
    {
        if (!empty($this->aReceitaCustaParcelamento[$iNumpre][$iNumpar][$taxa->getReceita()])) {
            $oReceita = $this->aReceitaCustaParcelamento[$iNumpre][$iNumpar][$taxa->getReceita()];
            $nValorReceita = $oReceita->nValorReceita + $nValorReceita;
        }

        $oReceita = new stdClass();
        $oReceita->iNumpre = $iNumpre;
        $oReceita->iNumpar = $iNumpar;
        $oReceita->iReceita = $taxa->getReceita();
        $oReceita->taxa = $taxa;
        $oReceita->nValorReceita = $nValorReceita;
        $oReceita->iHistorico = $iHistorico;

        $this->aReceitaCustaParcelamento[$iNumpre][$iNumpar][$taxa->getReceita()] = $oReceita;

        return true;
    }

    /**
     * Processa inserção de receita de custas ao recibo.
     * @return void
     * @throws Exception
     */
    public function processaReceitaCusta()
    {
        $this->processaReceitaCustaInicial();
        $this->processaReceitaCustaParcelamento();
    }

    /**
     * @throws Exception
     */
    private function processaReceitaCustaInicial()
    {
        if (count($this->aReceitaCusta) > 0) {
            $rsCgm = db_query("select k00_numcgm, k00_numpre, k00_numpar from recibopaga where k00_numnov = {$this->iNumpre} order by k00_numpre asc, k00_numpar asc limit 1");

            if (!$rsCgm) {
                throw new Exception("Erro ao obter o cgm do recibo.");
            }

            $iCgmRecibo = pg_fetch_result($rsCgm, 0, 'k00_numcgm');
            $iNumpreRecibo = pg_fetch_result($rsCgm, 0, 'k00_numpre');
            $iNumparRecibo = pg_fetch_result($rsCgm, 0, 'k00_numpar');

            foreach ($this->aReceitaCusta as $oReceita) {

                $sSqlCustasArrecad  = " select round(coalesce(a.k00_valor,0), 2) as vlr_honorarios         ";
                $sSqlCustasArrecad .= " from recibopaga                                                    ";
                $sSqlCustasArrecad .= "  inner join arrecad a on a.k00_numpre     = recibopaga.k00_numpre  ";
                $sSqlCustasArrecad .= "                      and a.k00_numpar     = recibopaga.k00_numpar  ";
                $sSqlCustasArrecad .= "                      and a.k00_receit     = recibopaga.k00_receit  ";
                $sSqlCustasArrecad .= " where recibopaga.k00_numnov = {$this->iNumpre}                     ";
                $sSqlCustasArrecad .= "   and recibopaga.k00_receit = {$oReceita->iReceita}                ";
                $rsCustasArrecad = db_query($sSqlCustasArrecad);
                if (!$rsCustasArrecad) {
                    throw new Exception("Erro ao verificar receita de custas");
                }

                if (pg_num_rows($rsCustasArrecad) > 0) {
                    continue;
                }

                $sSqlReciboPaga = "insert into recibopaga (k00_numcgm,
                                                           k00_dtoper,
                                                           k00_receit,
                                                           k00_hist,
                                                           k00_valor,
                                                           k00_dtvenc,
                                                           k00_numpre,
                                                           k00_numpar,
                                                           k00_numtot,
                                                           k00_numdig,
                                                           k00_conta,
                                                           k00_dtpaga,
                                                           k00_numnov) values ({$iCgmRecibo},
                                                                               '{$this->dtVencRecibo}',
                                                                                {$oReceita->iReceita},
                                                                                {$oReceita->iHistorico},
                                                                                {$oReceita->nValorReceita},
                                                                                '{$this->dtVencRecibo}',
                                                                                {$iNumpreRecibo},
                                                                                {$iNumparRecibo},
                                                                                1,
                                                                                0,
                                                                                0,
                                                                                '{$this->dtVencRecibo}',
                                                                                {$this->iNumpre})";

                $rsInsertReciboPaga = db_query($sSqlReciboPaga);

                if (!$rsInsertReciboPaga) {
                    throw new Exception("Não foi possível vincular receitas ao recibo.");
                }
            }
        }
    }

    /**
     * Processa inserção de receita de custas de parcelamento ao recibo.
     * @return void
     * @throws Exception
     */
    private function processaReceitaCustaParcelamento()
    {

        if (count((array)$this->aReceitaCustaParcelamento) > 0) {
            $rsCgm = db_query("select k00_numcgm from recibopaga where k00_numnov = {$this->iNumpre} order by k00_numpre asc, k00_numpar asc limit 1");

            if (!$rsCgm) {
                throw new Exception("Erro ao obter o cgm do recibo.");
            }

            $iCgmRecibo = pg_fetch_result($rsCgm, 0, 'k00_numcgm');

            foreach ($this->aReceitaCustaParcelamento as $iNumpre => $aNumpre) {
                $rsNumtot = db_query("select k00_numtot from recibopaga where k00_numnov = {$this->iNumpre} and k00_numpre = {$iNumpre} limit 1");

                if (!$rsNumtot) {
                    throw new Exception("Erro ao obter o numero total de parcelas do numpre {$iNumpre}.");
                }

                $iNumtot = pg_fetch_result($rsNumtot, 0, 'k00_numtot');

                foreach ($aNumpre as $iNumpar => $aNumpar) {
                    foreach ($aNumpar as $iReceit => $oReceita) {

                        $sSqlCustasArrecad  = " select round(coalesce(a.k00_valor,0), 2) as vlr_honorarios         ";
                        $sSqlCustasArrecad .= " from recibopaga                                                    ";
                        $sSqlCustasArrecad .= "  inner join arrecad a on a.k00_numpre     = recibopaga.k00_numpre  ";
                        $sSqlCustasArrecad .= "                      and a.k00_numpar     = recibopaga.k00_numpar  ";
                        $sSqlCustasArrecad .= "                      and a.k00_receit     = recibopaga.k00_receit  ";
                        $sSqlCustasArrecad .= " where recibopaga.k00_numnov = {$this->iNumpre}                     ";
                        $sSqlCustasArrecad .= "   and recibopaga.k00_receit = {$oReceita->iReceita}                ";
                        $rsCustasArrecad = db_query($sSqlCustasArrecad);
                        if (!$rsCustasArrecad) {
                            throw new Exception("Erro ao verificar receita de custas");
                        }

                        if (pg_num_rows($rsCustasArrecad) > 0) {
                            continue;
                        }

                        $sSqlReciboPaga = "insert into recibopaga (k00_numcgm,
                                                                   k00_dtoper,
                                                                   k00_receit,
                                                                   k00_hist,
                                                                   k00_valor,
                                                                   k00_dtvenc,
                                                                   k00_numpre,
                                                                   k00_numpar,
                                                                   k00_numtot,
                                                                   k00_numdig,
                                                                   k00_conta,
                                                                   k00_dtpaga,
                                                                   k00_numnov) values ({$iCgmRecibo},
                                                                                       '{$this->dtVencRecibo}',
                                                                                        {$oReceita->iReceita},
                                                                                        {$oReceita->iHistorico},
                                                                                        {$oReceita->nValorReceita},
                                                                                        '{$this->dtVencRecibo}',
                                                                                        {$iNumpre},
                                                                                        {$iNumpar},
                                                                                        {$iNumtot},
                                                                                        0,
                                                                                        0,
                                                                                        '{$this->dtVencRecibo}',
                                                                                        {$this->iNumpre})";

                        $rsInsertReciboPaga = db_query($sSqlReciboPaga);

                        if (!$rsInsertReciboPaga) {
                            throw new Exception("Não foi possível vincular receitas ao recibo." . pg_last_error());
                        }
                    }
                }
            }
        }
    }

    /**
     * @param      $dtAutenticacao
     * @param null $sCaracteristicaPeculiar
     * @param null $iSequencialEmpenho
     *
     * @return bool
     * @throws \BusinessException
     * @throws \Exception
     */
    function autenticarRecibo($dtAutenticacao, $sCaracteristicaPeculiar = null, $iSequencialEmpenho = null, $retencao = null, $tipo = 3)
    {
        if (!db_utils::inTransaction()) {
            throw new Exception("Erro [0] - Não existe Transação Ativa.");
        }
        if ($this->iNumpre == null) {
            throw new Exception("Erro [1] - Código da Arrecadação não informado.");
        }
        if ($this->getConta() == null) {
            throw new Exception("Erro [2] - Conta pagadora ou conta extra não cadastrada.");
        }

        $oAutenticacaoArrecadacao = new AutenticacaoArrecadacao(
            $this->iNumpre,
            "0",
            $this->getConta(),
            $this->getGrupoArrecadacao(),
            $dtAutenticacao,
            $sCaracteristicaPeculiar,
            $iSequencialEmpenho
        );
        if (count($this->aRecursos) == 1) {
            $oAutenticacaoArrecadacao->setCodigoRecurso($this->aRecursos[0]);
        }

        $oAutenticacaoArrecadacao->setRetencao($retencao);
        $oAutenticacaoArrecadacao->setExecutaLancamentoContabil($this->executaLancamentoContabil());
        $oAutenticacaoArrecadacao->autenticar($tipo);
        return true;
    }

    /**
     * Realiza o estorno de um recibo
     * @param $iNumpre
     * @param null $sCaracteristicaPeculiar
     * @param null $iSequencialEmpenho
     * @param null $retencao
     * @return bool
     * @throws BusinessException
     * @throws ParameterException
     * @throws Exception
     */
    function estornarRecibo($iNumpre, $sCaracteristicaPeculiar = null, $iSequencialEmpenho = null, $retencao = null, $tipo = 6)
    {
        if (!db_utils::inTransaction()) {
            throw new Exception("Erro [0] - Não existe Transação Ativa.");
        }
        if ($iNumpre == null) {
            throw new Exception("Erro [1] - Código da Arrecadação não informado.");
        }
        if ($this->getConta() == null) {
            throw new Exception("Erro [2] - Conta pagadora ou conta extra não cadastrada.");
        }

        $oAutenticacaoArrecadacao = new AutenticacaoArrecadacao($iNumpre, "0", $this->getConta(), $this->getGrupoArrecadacao(), null, $sCaracteristicaPeculiar, $iSequencialEmpenho);
        if (count($this->aRecursos) == 1) {
            $oAutenticacaoArrecadacao->setCodigoRecurso($this->aRecursos[0]);
        }
        $oAutenticacaoArrecadacao->setRetencao($retencao);
        $oAutenticacaoArrecadacao->setExecutaLancamentoContabil($this->executaLancamentoContabil());
        $oAutenticacaoArrecadacao->estornar($tipo);
        return true;
    }

    /**
     * Método que executa os lancamentos contabeis de uma receita extra-orcamentaria
     * @param integer $iId
     * @param date $dtLancamento
     * @param integer $iAutent
     * @param bool $lEstorno
     * @param null $sCaracteristicaPeculiar
     * @throws BusinessException
     */
    private function executarLancamentoContabil($iId, $dtLancamento, $iAutent, $lEstorno = false, $sCaracteristicaPeculiar = null)
    {
        $iCodigoDocumento = 160;
        $oDaoCorrente = db_utils::getDao('corrente');
        $sql = $oDaoCorrente->sql_query_arrecadacao_extra($iId, $dtLancamento, $iAutent);
        $sCampoValorEstorno = 'arrecada';
        if ($lEstorno) {
            $sql = $oDaoCorrente->sql_query_estorno_arrecadacao_extra($iId, $dtLancamento, $iAutent);
            $iCodigoDocumento = 162;
            $sCampoValorEstorno = 'estorna';
        }

        $rsBuscaAutenticacao = db_query($sql);
        $iTotalLinhas = pg_num_rows($rsBuscaAutenticacao);
        for ($iRowAutenticacao = 0; $iRowAutenticacao < $iTotalLinhas; $iRowAutenticacao++) {
            $oDadoAutenticacao = db_utils::fieldsMemory($rsBuscaAutenticacao, $iRowAutenticacao);

            $sObservacaoHistorico = "Arrecadação de Receita Extra-Orçamentária";
            if ($oDadoAutenticacao->k12_histcor != "") {
                $sObservacaoHistorico = $oDadoAutenticacao->k12_histcor;
            }

            $oContaCorrenteDetalhe = new ContaCorrenteDetalhe();

            $oLancamentoAuxiliar = new LancamentoAuxiliarArrecadacaoReceitaExtraOrcamentaria();
            $oLancamentoAuxiliar->setObservacaoHistorico($sObservacaoHistorico . "{$sCampoValorEstorno}:{$iRowAutenticacao}");
            $oLancamentoAuxiliar->setValorTotal($oDadoAutenticacao->$sCampoValorEstorno);
            $oLancamentoAuxiliar->setHistorico(9500);
            $oLancamentoAuxiliar->setContaCredito($oDadoAutenticacao->k02_reduz);
            $oLancamentoAuxiliar->setContaDebito($oDadoAutenticacao->k12_conta);
            $oLancamentoAuxiliar->setAutenticacao($iId);
            $oLancamentoAuxiliar->setDataAutenticacao($dtLancamento);
            $oLancamentoAuxiliar->setAutenticadora($iAutent);
            $oLancamentoAuxiliar->setContaCorrenteDetalhe($oContaCorrenteDetalhe);

            $oLancamentoAuxiliar->setEstorno($lEstorno);
            if (count($this->aRecursos) == 1) {
                $oLancamentoAuxiliar->setCodigoRecurso($this->aRecursos[0]);
            }
            if (!empty($sCaracteristicaPeculiar)) {
                $oLancamentoAuxiliar->setCaracteristicaPeculiar($sCaracteristicaPeculiar);
            }

            $oEventoContabil = new EventoContabil($iCodigoDocumento, db_getsession("DB_anousu"));
            $oEventoContabil->executaLancamento($oLancamentoAuxiliar, $dtLancamento);
        }
    }

    /**
     * Retorna Numpre do recibo
     * @return number
     */
    function getNumpreRecibo()
    {
        return $this->iNumpre;
    }

    /**
     * Define numpre novo do recibo
     * @param integer $iNumpre
     */
    function setNumnov($iNumpre)
    {
        $this->iNumpre = $iNumpre;
    }

    /**
     * Define a data de vencimento do recibo
     * @param integer $dtDataVenc
     */
    function setDataVencimentoRecibo($dtDataVenc)
    {
        $this->dtVencRecibo = $dtDataVenc;
    }

    /**
     * Retorna a data de vencimento do recibo
     * @return string
     */
    function getDataVencimentoRecibo()
    {
        return $this->dtVencRecibo;
    }

    function getDataVencimento()
    {
        return $this->dtVencRecibo;
    }

    /**
     * Retorna o array de débitos(numpre) do recibo gerado/a ser gerado
     * @return array;
     */
    function getDebitosRecibo()
    {
        return $this->aNumpres;
    }

    /**
     * Define o exercicio que o recibo vai ser gerado
     * @param integer $iAnoUsu
     */
    function setExercicioRecibo($iAnoUsu)
    {
        $this->iAnoUsu = $iAnoUsu;
    }

    /**
     * Buscamos o total do recibo conforme o tipo do mesmo
     * Caso o tipo seja 1, buscamos as informações na tabela 'recibo'
     * Caso o tipo seja 2, buscamos as informações na tabela 'recibopaga'
     * @return numeric
     */
    function getTotalRecibo()
    {
        switch ($this->iTipoEmissao) {
                /**
             * Se o recibo for do tipo 1 buscamos o valor do mesmo na tabela 'recibo'
             */
            case 1:
                $sNomeTabela = "recibo";
                $sWhereRecibo = "k00_numpre = {$this->getNumpreRecibo()}";
                break;

                /**
                 * Seo recibo for do tipo 2 buscamos o valor do mesmo na tabela 'recibopaga'
                 */
            case 2:
                $sNomeTabela = "recibopaga";
                $sWhereRecibo = "k00_numnov = {$this->getNumpreRecibo()}";
                break;
        }

        $oDaoRecibo = db_utils::getDao($sNomeTabela);
        $sSqlBuscaRecibo = $oDaoRecibo->sql_query_file(null, "coalesce(sum(k00_valor), 0) as soma_k00_valor", null, $sWhereRecibo);
        $rsBuscaRecibo = $oDaoRecibo->sql_record($sSqlBuscaRecibo);

        return db_utils::fieldsMemory($rsBuscaRecibo, 0)->soma_k00_valor;
    }

    /**
     * Cancela o Recibo
     * @param $sMotivoCancelamento
     * @return bool
     * @throws Exception
     */
    public function cancelar($sMotivoCancelamento)
    {
        /**
         * Valida a conexão
         */
        if (!db_utils::inTransaction()) {
            throw new Exception("Não existe Transação Ativa Para efetuar o Cancelamento." . pg_last_error());
        }
        /**
         * Valida se é recibo avulso
         */
        if ($this->getTipoEmissao() == 1) {
            throw new Exception("Não é possivel efetuar o cancelamento de um Recibo Avulso. ");
        }
        $oDaoCancReciboPaga = db_utils::getDao("cancrecibopaga");

        $oDaoCancReciboPaga->k134_numnov = $this->getNumpreRecibo();
        $oDaoCancReciboPaga->k134_motivo = $sMotivoCancelamento;
        $oDaoCancReciboPaga->k134_data = date("Y-m-d", db_getsession("DB_datausu"));
        $oDaoCancReciboPaga->k134_usuario = db_getsession("DB_id_usuario");
        $oDaoCancReciboPaga->incluir(null);

        if ($oDaoCancReciboPaga->erro_status == "0") {
            throw new Exception("Erro ao Cancelar Recibo:" . $oDaoCancReciboPaga->erro_msg);
        }
        return true;
    }

    /**
     * Verifica se o Recibo é valido.
     * @return boolean
     * @throws ParameterException
     */
    public function isValido()
    {
        $oHoje = new DBDate(date("y-m-d", db_getsession("DB_datausu")));
        $oDataRecibo = new DBDate($this->getDataVencimentoRecibo());

        if ($oHoje->getTimeStamp() <= $oDataRecibo->getTimeStamp()) {
            return true;
        }
        return false;
    }

    /**
     * Retorna instancias de Recibo apartir de um numpre de debito.
     *
     * @param integer $iNumpre
     * @throws DBException - Quando Houver erro de query
     * @throws Exception
     * @return Recibo[]
     */
    public static function getRecibosByNumpreDebito($iNumpre)
    {
        $oDaoRecibopaga = new cl_recibopaga();
        $sSqlRecibos = $oDaoRecibopaga->sql_query_file(null, 'k00_numnov', null, "k00_numpre = {$iNumpre}");
        $rsRecibos = db_query($sSqlRecibos);

        if (!$rsRecibos) {
            throw new DBException("Erro ao consultar recibos do numpre {$iNumpre}. Erro:\n" . pg_last_error());
        }

        $aRecibos = db_utils::getCollectionByRecord($rsRecibos);
        $aRecibosNumpre = array();

        foreach ($aRecibos as $oRecibo) {
            $aRecibosNumpre[] = new Recibo(null, null, null, $oRecibo->k00_numnov);
        }

        return $aRecibosNumpre;
    }

    /**
     * @return array
     */
    public function getReceitasCustas()
    {
        return $this->aReceitaCusta;
    }

    /**
     * @return array
     */
    public function getReceitaCustaParcelamento()
    {
        return $this->aReceitaCustaParcelamento;
    }

    /**
     * @return  convenio
     */
    public function getConvenio()
    {
        return $this->convenio;
    }

    /**
     * @param   convenio  $convenio  Convenio associado ao recibo
     */
    public function setConvenio(convenio $convenio)
    {
        $this->convenio = $convenio;
    }

    public function getTipoDebito()
    {
        return $this->aNumpres[0]->k00_tipo;
    }

    /**
     *
     * @param CgmBase $cgm
     * @return void
     */
    public function setCgmExibicao(CgmBase $cgm)
    {
        $this->cgmExibicao = $cgm;
    }

    /**
     *
     * @return CgmBase
     */
    public function getCgmExibicao()
    {
        return $this->cgmExibicao;
    }

    /**
     * @param Arretipo $arretipo
     * @return void
     */
    public function setArretipo(Arretipo $arretipo)
    {
        $this->arretipo = $arretipo;
    }

    /**
     * @return Arretipo
     */
    public function getArretipo()
    {
        return $this->arretipo;
    }

    /**
     * @return integer
     */
    public function getCodigoInstituicao()
    {
        return $this->codigoInstituicao;
    }

    /**
     * @param integer $codigoInstituicao
     * @return void
     */
    public function setCodigoInstituicao($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
    }

    /**
     * @param integer $cadTipoMod
     * @return void
     */
    public function setCadTipoMod($cadTipoMod)
    {
        $this->cadTipoMod = $cadTipoMod;
    }

    /**
     * @return integer
     */
    public function getCadTipoMod()
    {
        return $this->cadTipoMod;
    }

    /**
     * @param integer $minNumpar
     * @return void
     */
    public function setMinNumpar($minNumpar)
    {
        $this->minNumpar = $minNumpar;
    }

    /**
     * @return integer
     */
    public function getMinNumpar()
    {
        return $this->minNumpar;
    }

    /**
     * @param integer $maxNumpar
     * @return void
     */
    public function setMaxNumpar($maxNumpar)
    {
        $this->maxNumpar = $maxNumpar;
    }

    /**
     * @return integer
     */
    public function getMaxNumpar()
    {
        return $this->maxNumpar;
    }

    /**
     * @return float
     */
    public function getValorOrigem()
    {
        $valorOrigem = 0;
        foreach ($this->getDebitosRecibo() as $debito) {
            $valorOrigem += $debito->k00_valor;
        }
        return $valorOrigem;
    }

    /**
     * Filtra as linhas do histórico relacionadas ao tipo informado
     *
     * @param ArretipoEnum $arretipo
     * @return string
     * @throws Exception
     */
    public function filterHistorico($arretipo)
    {
        switch ($arretipo) {
            case ArretipoEnum::INICIAL_FORO:
                $prefixo = 'inicial';
                break;
            default:
                throw new Exception('Filtro ainda não implementado!');
        }

        $res = array();

        foreach (explode("\n", $this->getHistorico()) as $linha) {
            if ($this->stringStartsWith(mb_strtolower(trim($linha)), $prefixo)) {
                $res[] = trim($linha);
            }
        }

        return implode("\n", $res);
    }

    private function stringStartsWith($string, $start)
    {
        return substr($string, 0, strlen($start)) === $start;
    }

    /**
     * @return CgmBase|\ECidade\Tributario\Issqn\Model\Issbase|\ECidade\Tributario\Cadastro\Model\Iptubase
     */
    public function getIdentificacao()
    {
        return $this->identificacao;
    }

    /**
     * @param CgmBase|\ECidade\Tributario\Issqn\Model\Issbase|\ECidade\Tributario\Cadastro\Model\Iptubase $identificacao
     */
    public function setIdentificacao($identificacao)
    {
        $this->identificacao = $identificacao;
    }

    /**
     * @return integer
     */
    public function getQuantidadeParcelas()
    {
        return $this->quantidadeParcelas;
    }

    /**
     * @param integer $quantidadeParcelas
     * @return void
     */
    public function setQuantidadeParcelas($quantidadeParcelas)
    {
        $this->quantidadeParcelas = $quantidadeParcelas;
    }

    /**
     * @return integer
     */
    public function getParcelaAtual()
    {
        return $this->parcelaAtual;
    }

    /**
     * @param integer $parcelaAtual
     * @return void
     */
    public function setParcelaAtual($parcelaAtual)
    {
        $this->parcelaAtual = $parcelaAtual;
    }

    /**
     * @return  int[]
     */
    public function getProcessosForo()
    {
        return $this->processosForo;
    }

    /**
     * @param   int[]  $processosForo  Processos do foro das iniciais envolvidas no boleto
     */
    public function setProcessosForo($processosForo)
    {
        $this->processosForo = $processosForo;
    }

    /**
     * @param bool
     */
    public function setPartilhaPagaMigracao($lPartilhaPagaMigracao)
    {
        $this->lPartilhaPagaMigracao = $lPartilhaPagaMigracao;
    }

    /**
     * @return bool
     */
    public function getPartilhaPagaMigracao()
    {
        return $this->lPartilhaPagaMigracao;
    }

    /**
     * @param $numnov
     * @param $codigoTaxaEspecifica
     * @return bool
     * @throws Exception
     */
    protected function gerarTaxaExpediente($numnov, $codigoTaxaEspecifica)
    {
        if (empty($codigoTaxaEspecifica) || $this->geracaoTaxaExpediente === false) {
            return false;
        }

        $taxaService = new TaxaEspecificaService(TaxaEspecificaRepository::getInstance());
        $taxaModel = $taxaService->getByCodigoSubReceita($codigoTaxaEspecifica);
        $taxaModel->setValorCalculadoInflator($taxaService->calculaInflator($taxaModel));

        if (empty($taxaModel->getValorCalculadoInflator())) {
            return false;
        }

        $repository = new RecibopagaRepository(DataBase::getInstance(), new cl_recibopaga());
        $reciboPaga = $repository->findAllByNumnov($numnov)->get(0);

        $reciboPaga->setHist(TaxaEspecificaModel::CODIGO_HISTORICO);
        $reciboPaga->setReceit($taxaModel->getCodigoReceita());
        $reciboPaga->setValor($taxaModel->getValorCalculadoInflator());

        $repository->save($reciboPaga);

        return true;
    }

    /**
     * @param $numnov
     * @param $codigoTaxaADM
     * @return bool
     * @throws Exception
     */
    protected function gerarTaxaADM($numnov, $sTipo, $numpar, $numpre)
    {
        if ($sTipo == 14) {
            $sql = "select v07_valor,v07_totpar from termo where v07_numpre = " . $numpre;
            $rc = db_query($sql);
            $total = pg_fetch_all($rc)[0]['v07_valor'];
            $totpar = pg_fetch_all($rc)[0]['v07_totpar'];

            $sql = "select * from taxa where ar36_debitossemprocesso = true";
            $rTaxas = pg_fetch_all(db_query($sql));
            //loop taxas
            foreach ($rTaxas as $key => $taxa) {
                $pctm = $taxa['ar36_perc'];
                $sql = "select * from termotaxaparc where ar29_taxa = " . $taxa['ar36_sequencial'];
                $Rtermotaxaparc = db_query($sql);
                //verifica se taxa esta configurada em 1 parcela
                if (pg_num_rows($Rtermotaxaparc) > 0) {
                    $Rtermotaxaparc = pg_fetch_all(db_query($sql))[0];
                    $parcela = $Rtermotaxaparc['ar29_numpar'];
                    //verifica se a parcela configurada foi selecionada
                    foreach ($numpar as $key => $value) {
                        if ($value == $parcela && $pctm > 0) {
                            $valor_taxa = round(($total / 100 * $pctm), 2);
                            if ($valor_taxa < $taxa['ar36_valormin']) {
                                $valor_taxa = $taxa['ar36_valormin'];
                            }
                            if ($valor_taxa > $taxa['ar36_valormax']) {
                                $valor_taxa = $taxa['ar36_valormax'];
                            }
                        }
                    }
                } else {
                    $valor_taxa = round(($total / 100 * $pctm)/$totpar, 2);
                    if ($valor_taxa < $taxa['ar36_valormin']) {
                        $valor_taxa = $taxa['ar36_valormin'];
                    }
                    if ($valor_taxa > $taxa['ar36_valormax']) {
                        $valor_taxa = $taxa['ar36_valormax'];
                    }
                }
                if($valor_taxa != 0){
                    $repository = new RecibopagaRepository(DataBase::getInstance(), new cl_recibopaga());
                    $reciboPaga = $repository->findAllByNumnov($numnov)->get(0);
                    $reciboPaga->setHist(TaxaEspecificaModel::CODIGO_HISTORICO);
                    $reciboPaga->setReceit($taxa['ar36_receita']);
                    $reciboPaga->setValor($valor_taxa);
                    $repository->save($reciboPaga);
                }
            }
        } else {

            $oDaoRecibopaga = db_utils::getDao("recibopaga");
            $sqlRecibopaga = $oDaoRecibopaga->sql_query(null, 'sum(k00_valor)', null, "k00_numnov = $numnov");
            $rc = db_query($sqlRecibopaga);
            $total = pg_fetch_all($rc)[0]['sum'];

            $oDaotaxa = db_utils::getDao("taxa");
            $sqltaxa = $oDaotaxa->sql_query_file(
                null,
                "ar36_valor,ar36_valormin,ar36_valormax,ar36_perc,ar36_receita,ar36_descricao,ar36_receita",
                null,
                "ar36_debitossemprocesso is true"
            );
            $taxa = $oDaotaxa->sql_record($sqltaxa);

            if (pg_num_rows($taxa) == 0) {
                return false;
            }

            foreach (pg_fetch_all($taxa) as $key => $value) {
                $pctm = $value['ar36_perc'];
                if ($pctm > 0) {
                    $valor_taxa = round(($total / 100 * $pctm), 2);

                    if ($valor_taxa < $value['ar36_valormin']) {
                        $valor_taxa = $value['ar36_valormin'];
                    }

                    if ($valor_taxa > $value['ar36_valormax']) {
                        $valor_taxa = $value['ar36_valormax'];
                    }
                } else if ($value['ar36_valor'] > 0) {
                    $valor_taxa = $value['ar36_valor'];
                }
                $repository = new RecibopagaRepository(DataBase::getInstance(), new cl_recibopaga());
                $reciboPaga = $repository->findAllByNumnov($numnov)->get(0);
                $reciboPaga->setHist(TaxaEspecificaModel::CODIGO_HISTORICO);
                $reciboPaga->setReceit($value['ar36_receita']);
                $reciboPaga->setValor($valor_taxa);
                $repository->save($reciboPaga);
            }
            return true;
        }
    }

    /**
     * @param bool $geracaoTaxaExpediente
     */
    public function setGeracaoTaxaExpediente($geracaoTaxaExpediente)
    {
        $this->geracaoTaxaExpediente = $geracaoTaxaExpediente;
    }
}
