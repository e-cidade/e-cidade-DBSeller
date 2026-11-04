<?php
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

use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\Pessoal\Model\Sindicato;
use ECidade\RecursosHumanos\Pessoal\Repository\SindicatoRepository;
use ECidade\RecursosHumanos\RH\Efetividade\Repository\EscalaServidor;

/**
 * Classe para manipuação de servidores
 *
 * @author   Alberto Ferri Neto alberto@dbseller.com.br
 * @package  Pessoal
 * @revision $Author: dbandrio.costa $
 * @version  $Revision: 1.103 $
 */
class Servidor
{

    /**
     * Duplo vinculo do Servidor
     *
     * @var Servidor
     */
    private $oDuploVinculo;
    /**
     * Conta bancaria do Servidor
     * @var ContaBancaria
     */
    private $oContaBancaria;

    /**
     * Codigo do servidor na competencia
     */
    private $iCodigoMovimentacao;

    /**
     * Matrícula do servidor
     * @var integer
     */
    private $iMatricula;

    /**
     * Matrícula anterior do servidor
     * @var string
     */
    private $matriculaAnterior;

    /**
     * Fgts do servidor
     * @var integer
     */
    private $dataOptFgts;

    /**
     * Instância do objeto CgmBase Número do cgm do servidor
     * @var object
     */
    private $oCgm;

    /**
     * Código do cargo do servidor
     * @var inteiro
     */
    private $iCodigoCargo;

    /**
     * Data de admissão do servidoOptFgtsr
     * @var DBDate
     */
    private $oDataAdmissao;

    /**
     * Tipo de admissão do servidor
     * 1 - Admissao do 1o emprego
     * 2 - Admissao com emprego anterior
     * 3 - Transf de empreg sem onus para a cedente
     * 4 - Transf de empreg com onus para a cedente
     *
     * @var integer
     */
    private $iTipoAdmissao;

    /**
     * Data que foi/sera consedido o triênio
     * @var DBDate
     */
    private $oDataTrienio;

    /**
     * Data de progressão do servidor. (É a mudança de nível de capacitação do servidor para o nível subsequente)
     * @var DBDate
     */
    private $oDataProgressao;

    /**
     * Código da instituição da matrícula do servidor
     * @var integer
     */
    private $iCodigoInstituicao;

    /**
     * Número no relogio ponto
     * @var integer
     */
    private $iNumeroPonto;

    /**
     * Observações referentes ao servidor
     * @var string
     */
    private $sObservacaoServidor;

    /**
     * Ano de calculo atual da folha
     *
     * @var integer
     * @access private
     */
    private $iAnoCompetencia;

    /**
     * Ano de calculo atual da folha
     *
     * @var integer
     * @access private
     */
    private $iMesCompetencia;

    /**
     * Numero do CGM
     *
     * @var integer
     * @access private
     */
    private $iNumCgm;

    /**
     * Tabela previdencia
     *
     * @var integer
     * @access private
     */
    private $iTabelaPrevidencia;

    /**
     * Array com coleção de objetos Dependente
     * Referente ao servidor
     */
    private $aDependentes = array();

    /**
     * Objeto DBDate com a data de nascimento do servidor
     * @object DBDate
     * @access private
     */
    private $oDataNascimento;

    /**
     * Sexo do servidor
     * @var string
     */
    private $sSexo;

    /**
     * Tipo de exposicao a agentes nocivos
     * '' - Nunca esteve exposta
     * 01 - Não exposto no momento, mas já esteve
     * 02 - Exposta (aposentadoria esp. 15 anos)
     * 03 - Exposta (aposentadoria esp. 20 anos)
     * 04 - Exposta (aposentadoria esp. 25 anos)
     * 05 - Mais de um vínculo (ou fonte pagadora) - Não exposição a agente nocivo
     *
     * @var mixed
     * @access private
     */
    private $sTipoExposicaoAgentesNocivos;

    /**
     *
     * Código
     * @var mixed
     * @access private
     */
    private $iCodigoRegime;

    /**
     * Codigo da lotacao
     *
     * @var mixed
     * @access private
     */
    private $iCodigoLotacao;

    /**
     * Salario
     *
     * @var mixed
     * @access private
     */
    private $iSalario;

    /**
     * Define se o Servidor utiliza ou não o Abono de Permanência.
     * @var boolean
     */
    private $lAbonoPermanencia;

    /**
     * Número de dias de férias padrão do servidor
     *
     * @var Integer
     * @access private
     */
    private $iDiasGozoFerias;

    /**
     * Define se o servidor possui ou não moléstia grave
     *
     * @var Boolean
     */
    private $lMolestiaGrave;

    /**
     * Código do PIS/PASEP
     *
     * @var String
     */
    private $sPISPASEP;

    /**
     * @var bool
     */
    private $lRegistraPontoEletronico = true;

    /**
     * @var array
     */
    private $escalas;

    /**
     * @var ECidade\RecursosHumanos\RH\Efetividade\Model\EscalaServidor | null
     */
    private $escala;

    private $imigrante;

    const VARIAVEL_SALARIO_BASE_PROGRESSAO = 'F010';

    /**
     * @var \stdClass
     */
    private $documentos;

    /**
     * @var \LocalTrabalho[]
     */
    private $locaisTrabalho = array();

    /**
     * Data da rescisao do servidor
     * @var \DateTime
     */
    private $dataRescisao = null;

    /**
     * @var float
     */
    private $horasMensais = 0;

    /**
     * Dados da rescisao
     * @var \stdClass
     */
    private $dadosRescisao;

    /**
     * @var Rubrica[]
     */
    private $rubricasPonto = array();
    /**
     * Controle da pesquisa rescisao - cache
     * @var bool
     */
    private $carregouDadosRescisao = false;

    private $dataPagamentoRescisao;
    /**
     * @var Sindicato
     */
    private $sindicato;

    /**
     * @var int
     */
    private $racaCor;

    /**
     * @var int
     */
    private $grauInstrucao;

    /**
     * @var \DateTime
     */
    private $dataOptanteFgts;

    /**
     * @var integer
     */
    private $estadoCivil;

    /**
     * Descrição do Estado Civil
     *
     * @var String
     */
    private $sDescricaoEstadoCivil;

    /**
     * Código da Nacionalidade
     *
     * @var int
     */
    private $codigoNacionalidade;

    /**
     * Descrição da Nacionalidade
     *
     * @var String
     */
    private $sDescricaoNacionalidade;

    /**
     * @var string
     */
    private $naturalidade;

    /**
     * Código do Cargo
     *
     * @var int
     */
    private $cargo;

    /**
     * Servidor constructor.
     * @param int $iMatricula
     * @param int $iAnoCompetencia
     * @param int $iMesCompetencia
     * @param int $iInstituicao
     * @throws Exception
     */
    public function __construct(
        $iMatricula = null,
        $iAnoCompetencia = null,
        $iMesCompetencia = null,
        $iInstituicao = null,
        $usaInstituicao = true,
        $bSomenteCalculo = false
    ) {

        if (!empty($iAnoCompetencia)) {
            $this->iAnoCompetencia = $iAnoCompetencia;
        } else {
            $this->iAnoCompetencia = DBPessoal::getAnoFolha();
        }

        if (!empty($iMesCompetencia)) {
            $this->iMesCompetencia = $iMesCompetencia;
        } else {
            $this->iMesCompetencia = DBPessoal::getMesFolha();
        }

        if (!empty($iInstituicao)) {
            $this->iCodigoInstituicao = $iInstituicao;
        } else {
            $this->iCodigoInstituicao = db_getsession("DB_instit");
        }
        if (!empty($iMatricula)) {
            $iMatricula = $this->buscarMatriculaEcidade($iMatricula);

        }

        if (!empty($iMatricula) && !DBNumber::isInteger($iMatricula)) {
            throw new BusinessException("Formato de matrícula inválida.");
        }

        if (!empty($iMatricula)) {
            $oDaoPessoal = new cl_rhpessoal;
            $where = "rh01_regist = {$iMatricula} AND rh01_instit = {$this->iCodigoInstituicao}";
            if (!$usaInstituicao) {
                $where = "rh01_regist = {$iMatricula} ";
            }
            $sSqlPessoal = $oDaoPessoal->sql_query_file(
                null,
                'rh01_regist',
                null,
                $where
            );

            $rsPessoal = $oDaoPessoal->sql_record($sSqlPessoal);

            if (!$rsPessoal && $oDaoPessoal->erro_sql == "Erro ao selecionar os registros.") {
                throw new DBException("Erro ao Buscar Servidor.\n" . $oDaoPessoal->erro_msg);
            }

            if ($oDaoPessoal->numrows === 0) {
                throw new BusinessException("Matrícula {$iMatricula} não cadastrada no e-Cidade.");
            }

            if (!DBNumber::isInteger($iMatricula)) {
                throw new ParameterException("A Matrícula deve ser um Número Inteiro");
            }

            $this->setMatricula($iMatricula);

            if ($bSomenteCalculo) {
                return;
            }

            $sCampos = "rh01_numcgm, rh02_seqpes, rh02_lota, rh02_salari, rh02_funcao, rh01_tipadm, rh01_instit, rh01_ponto, ";
            $sCampos .= "rh01_observacao, rh02_abonopermanencia, rh02_tbprev, rh02_portadormolestia, rh02_ocorre, rh02_codreg, rh02_tpcont, ";
            $sCampos .= "rh01_admiss, rh01_trienio, rh01_progres, rh01_nasc, rh01_sexo, rh02_diasgozoferias, rh01_rhsindicato, ";
            $sCampos .= "rh01_nacion, rh01_estciv, rh02_hrsmen, rh01_raca, rh01_instru, rh01_registrapontoeletronico, rh01_matriculaanterior";
            $oDaoRhPessoalMov = new cl_rhpessoalmov();
            $sSqlRhPessoalMov = $oDaoRhPessoalMov->sql_queryDadosServidor(
                $this->iAnoCompetencia,
                $this->iMesCompetencia,
                $this->iCodigoInstituicao,
                $iMatricula,
                $sCampos
            );
            $rsRhPessoal = $oDaoRhPessoalMov->sql_record($sSqlRhPessoalMov);

            if (!$rsRhPessoal && $oDaoRhPessoalMov->erro_sql == "Erro ao selecionar os registros.") {
                throw new DBException("Erro ao Buscar Servidor.\n" . $oDaoRhPessoalMov->erro_msg);
            }

            if (pg_num_rows($rsRhPessoal) === 0) {
                throw new BusinessException("Servidor com a Matrícula: {$iMatricula} não está na competência: {$this->iMesCompetencia}/{$this->iAnoCompetencia}");
            }

            $oRhPessoal = db_utils::fieldsMemory($rsRhPessoal, 0);
            $this->iNumCgm = $oRhPessoal->rh01_numcgm;
            $this->iCodigoMovimentacao = $oRhPessoal->rh02_seqpes;
            $this->iCodigoLotacao = $oRhPessoal->rh02_lota;
            $this->iSalario = $oRhPessoal->rh02_salari;

            $this->setTpCont($oRhPessoal->rh02_tpcont);
            $this->setCodigoCargo($oRhPessoal->rh02_funcao);
            $this->setTipoAdmissao($oRhPessoal->rh01_tipadm);
            $this->setMatriculaAnterior($oRhPessoal->rh01_matriculaanterior);
            $this->setCodigoInstituicao($oRhPessoal->rh01_instit);
            $this->setNumeroPonto($oRhPessoal->rh01_ponto);
            $this->setObservacaoServidor($oRhPessoal->rh01_observacao);
            $this->setAbonoPermanencia($oRhPessoal->rh02_abonopermanencia);
            $this->setTabelaPrevidencia($oRhPessoal->rh02_tbprev);
            $this->setMolestiaGrave($oRhPessoal->rh02_portadormolestia == 't' ? true : false);
            $this->setFuncao($this->getInfoFuncao());

            if ($oRhPessoal->rh02_ocorre) {
                $this->setTipoExposicaoAgentesNocivos($oRhPessoal->rh02_ocorre);
            }

            if ($oRhPessoal->rh02_codreg) {
                $this->setCodigoRegime($oRhPessoal->rh02_codreg);
            }

            if (!empty($oRhPessoal->rh01_admiss)) {
                $this->setDataAdmissao(new DBDate($oRhPessoal->rh01_admiss));
            }

            if (!empty($oRhPessoal->rh01_trienio)) {
                $this->setDataTrienio(new DBDate($oRhPessoal->rh01_trienio));
            }

            if (!empty($oRhPessoal->rh01_progres)) {
                $this->setDataProgressao(new DBDate($oRhPessoal->rh01_progres));
            }

            if (!empty($oRhPessoal->rh01_nasc)) {
                $this->setDataNascimento(new DBDate($oRhPessoal->rh01_nasc));
            }

            $this->setSexo($oRhPessoal->rh01_sexo);

            if (!empty($oRhPessoal->rh02_diasgozoferias)) {
                $this->setDiasGozoFerias($oRhPessoal->rh02_diasgozoferias);
            }

            if (!empty($oRhPessoal->rh01_rhsindicato)) {
                $this->setSindicato(SindicatoRepository::find($oRhPessoal->rh01_rhsindicato));
            }

            if (!empty($oRhPessoal->rh01_nacion)) {
                $this->codigoNacionalidade = $oRhPessoal->rh01_nacion;
                $oDaoNacionalidade = new cl_rhnacionalidade;
                $sSqlNacionalidade = $oDaoNacionalidade->sql_query($this->codigoNacionalidade);
                $rsNacionalidade = $oDaoNacionalidade->sql_record($sSqlNacionalidade);

                if (!$rsNacionalidade && $oDaoNacionalidade->erro_sql == "Erro ao selecionar os registros.") {
                    throw new DBException("Erro ao buscar dados da nacionalidade do servidor ({$iMatricula}).\n" . $oDaoNacionalidade->erro_sql);
                }

                if (pg_num_rows($rsNacionalidade) > 0) {
                    $oNacionalidade = db_utils::fieldsMemory($rsNacionalidade, 0);
                    $this->sDescricaoNacionalidade = $oNacionalidade->rh06_descr;
                }
            }

            if (!empty($oRhPessoal->rh01_estciv)) {
                $this->estadoCivil = $oRhPessoal->rh01_estciv;
                $oDaoEstadoCivil = new cl_rhestcivil;
                $sSqlEstadoCivil = $oDaoEstadoCivil->sql_query($this->estadoCivil);
                $rsEstadoCivil = $oDaoEstadoCivil->sql_record($sSqlEstadoCivil);

                if (!$rsEstadoCivil && $oDaoEstadoCivil->erro_sql == "Erro ao selecionar os registros.") {
                    throw new DBException("Erro ao buscar dados do estado civil do servidor ({$iMatricula}).\n" . $oDaoEstadoCivil->erro_msg);
                }

                if (pg_num_rows($rsEstadoCivil) > 0) {
                    $oEstadoCivil = db_utils::fieldsMemory($rsEstadoCivil, 0);
                    $this->sDescricaoEstadoCivil = $oEstadoCivil->rh08_descr;
                }
            }

            if (!empty($oRhPessoal->rh02_hrsmen)) {
                $this->horasMensais = $oRhPessoal->rh02_hrsmen;
            }

            $oDocumentos = $this->getDocumentos();

            if (!empty($oDocumentos->sPIS)) {
                $this->setPISPASEP($oDocumentos->sPIS);
            }

            $this->racaCor = $oRhPessoal->rh01_raca;
            $this->grauInstrucao = $oRhPessoal->rh01_instru;
            $this->lRegistraPontoEletronico = $oRhPessoal->rh01_registrapontoeletronico == 't' ? true : false;

            $sOrderRegistrapontoeletronicohistorico = "rh215_data DESC";
            $sWhereRegistrapontoeletronicohistorico = "rh215_matricula = $iMatricula";
            $oDaoRegistrapontoeletronicohistorico   = new cl_registrapontoeletronicohistorico;
            $sSqlRegistrapontoeletronicohistorico   = $oDaoRegistrapontoeletronicohistorico->sql_query_file(
                null,
                '*',
                $sOrderRegistrapontoeletronicohistorico,
                $sWhereRegistrapontoeletronicohistorico
            );
            $rsRegistrapontoeletronicohistorico     = db_query($sSqlRegistrapontoeletronicohistorico);

            if (!$rsRegistrapontoeletronicohistorico) {
                throw new DBException("Erro ao buscar dados de registro de ponto eletrônico do Servidor ({$iMatricula})." . pg_last_error());
            }

            if (pg_num_rows($rsRegistrapontoeletronicohistorico) > 0) {
                $oRegistrapontoeletronicohistorico = db_utils::fieldsMemory($rsRegistrapontoeletronicohistorico, 0);
                $this->lRegistraPontoEletronico = $oRegistrapontoeletronicohistorico->rh215_registrapontoeletronico == 't' ? true : false;
            }
            unset($oRhPessoal);
        }
    }

    /**
     * Retorna a matrícula do servidor
     * @return integer
     */
    public function getMatricula()
    {
        return $this->iMatricula;
    }

    public function getMatriculaAnterior()
    {
        return $this->matriculaAnterior;
    }

    /**
     * Define a matrícula do servidor
     * @param integer $iMatricula
     */
    public function setMatricula($iMatricula)
    {
        $this->iMatricula = $iMatricula;
    }

    public function setMatriculaAnterior($matriculaAnterior)
    {
        $this->matriculaAnterior = $matriculaAnterior;
    }


    /**
     * Retorna um objeto DBDate contendo a data da opção do fgts
     * @var DBDate
     */
    public function getDataOptanteFgts()
    {
        $daoFgts = new cl_rhpesfgts();

        $sql = $daoFgts->sql_query_file($this->iMatricula);
        $rsFgts = db_query($sql);

        if (pg_num_rows($rsFgts) == 0) {
            return null;
        }

        return db_utils::makeFromRecord($rsFgts, function ($resultado) {
            $retorno = new stdClass();
            $retorno->rh15_data = $resultado->rh15_data;
            return $retorno;
        }, 0);
    }

    /**
     * Intancia um objeto DBDate, com informações sobre a data da opção do fgts
     *  @param object $dataOptFgts
     */
    public function setDataOptanteFgts($dataOptanteFgts)
    {
        $this->dataOptanteFgts = $dataOptanteFgts;
    }

    /**
     * Retorna o código do cgm do servidor
     * @return CgmFisico
     */
    public function hasCgm()
    {

        if (empty($this->oCgm) || !($this->oCgm instanceof CgmFisico) || !($this->oCgm instanceof CgmJuridico)) {
            return false;
        }
        return true;
    }

    /**
     * Retorna o objeto do cgm do servidor
     * @return CgmFisico
     */
    public function getCgm()
    {

        if (empty($this->oCgm)) {
            $this->setCgm(CgmFactory::getInstanceByCgm($this->iNumCgm));
        }
        return $this->oCgm;
    }

    /**
     * Define o código do cgm do servidor
     * @param object $oCgm
     */
    public function setCgm(CgmBase $oCgm = null)
    {
        $this->oCgm = $oCgm;
    }

    /**
     * Retorna o código do cargo do servidor
     * @return integer
     */
    public function getCodigoCargo()
    {
        return $this->iCodigoCargo;
    }

    /**
     * Define o código do cargo do servidor
     * @param integer $iCodigoCargo
     */
    public function setCodigoCargo($iCodigoCargo)
    {
        $this->iCodigoCargo = $iCodigoCargo;
    }

    /**
     * Retorna um objeto DBDate, contendo a data de admissão do servidor
     * @return DBDate
     */
    public function getDataAdmissao()
    {
        return $this->oDataAdmissao;
    }

    /**
     * Intancia um objeto DBDate, com informações sobre a data de admissão de um servidor
     * @param object $oDataAdmissao
     */
    public function setDataAdmissao(DBDate $oDataAdmissao)
    {
        $this->oDataAdmissao = $oDataAdmissao;
    }

    /**
     * Retorna o tipo de admissão do servidor
     * 1 - Admissao do 1o emprego
     * 2 - Admissao com emprego anterior
     * 3 - Transf de empreg sem onus para a cedente
     * 4 - Transf de empreg com onus para a cedente
     * @return integer
     */
    public function getTipoAdmissao()
    {
        return $this->iTipoAdmissao;
    }

    /**
     * Define o tipo de admissão do servidor
     * 1 - Admissao do 1o emprego
     * 2 - Admissao com emprego anterior
     * 3 - Transf de empreg sem onus para a cedente
     * 4 - Transf de empreg com onus para a cedente
     * @param integer
     */
    public function setTipoAdmissao($iTipoAdmissao)
    {
        $this->iTipoAdmissao = $iTipoAdmissao;
    }

    /**
     * Retorna um objeto DBDate contendo a data que foi/sera consedido o triênio
     * @return DBDate
     */
    public function getDataTrienio()
    {
        return $this->oDataTrienio;
    }

    /**
     * Instancía um objeto DBDate com as informações sobre a date que foi/sera consedido o triênio
     * @param object $sDataTrienio
     */
    public function setDataTrienio(DBDate $oDataTrienio)
    {
        $this->oDataTrienio = $oDataTrienio;
    }

    /**
     * Retorna um objeto DBDate contendo a data de progressão do servidor
     * @return DBDate
     */
    public function getDataProgressao()
    {
        return $this->oDataProgressao;
    }

    /**
     * Instancía um objeto DBDate com as informações sobre a date que foi/sera consedido o triênio
     * @param object $oDataTrienio
     */
    public function setDataProgressao(DBDate $oDataProgressao)
    {
        $this->oDataProgressao = $oDataProgressao;
    }

    /**
     * Retona o código da instituição da matrícula do servidor
     * @return integer
     */
    public function getCodigoInstituicao()
    {
        return $this->iCodigoInstituicao;
    }

    /**
     * Define o código da instituição da matrícula do servidor
     * @param integer $iCodigoInstituicao
     * @deprecated - Utilizar Servidor::getInstituicao();
     */
    public function setCodigoInstituicao($iCodigoInstituicao)
    {
        $this->iCodigoInstituicao = $iCodigoInstituicao;
    }

    /**
     * Retorna a instituicao do servidor
     *
     * @access public
     * @return Instituicao
     */
    public function getInstituicao()
    {

        require_once modification("model/configuracao/InstituicaoRepository.model.php");
        $oInstituicao = InstituicaoRepository::getInstituicaoByCodigo($this->iCodigoInstituicao);

        return $oInstituicao;
    }

    /**
     * Retorna o número do cartão ponto da matrícula do servidor
     * @return integer
     */
    public function getNumeroPonto()
    {
        return $this->iNumeroPonto;
    }

    /**
     * Define o número do cartão ponto da matrícula do servidor
     * @param integer $iNumeroPonto
     */
    public function setNumeroPonto($iNumeroPonto)
    {
        $this->iNumeroPonto = $iNumeroPonto;
    }

    /**
     * Retorna alguma observação sobre a matrícula do servidor
     * @return string
     */
    public function getObservacaoServidor()
    {
        return $this->sObservacaoServidor;
    }

    /**
     * Define alguma observação sobre a matrícula do servidor
     * @param string $sObservacaoServidor
     */
    public function setObservacaoServidor($sObservacaoServidor)
    {
        $this->sObservacaoServidor = $sObservacaoServidor;
    }

    /**
     * Define o codigo da tabela de previdencia
     *
     * @param integer $iTabelaPrevidencia
     * @access public
     * @return void
     */
    public function setTabelaPrevidencia($iTabelaPrevidencia)
    {
        $this->iTabelaPrevidencia = $iTabelaPrevidencia;
    }

    /**
     * Retorna codigo da tabela de previdencia
     *
     * @access public
     * @return integer
     */
    public function getTabelaPrevidencia()
    {
        return $this->iTabelaPrevidencia;
    }

    /**
     * Define a data de nascimento do servidor
     *
     * @param DBDate $oDataNascimento
     * @access public
     * @return void
     */
    public function setDataNascimento(DBDate $oDataNascimento)
    {
        $this->oDataNascimento = $oDataNascimento;
    }

    /**
     * Retorna a data de nascimento do servidor
     *
     * @access public
     * @return objeto DBDate
     */
    public function getDataNascimento()
    {
        return $this->oDataNascimento;
    }

    /**
     * Retorna idade do servidor
     *
     * @access public
     * @return integer
     */
    public function getIdade()
    {

        if (empty($this->oDataNascimento)) {
            return 0;
        }

        return DBDate::calculaIntervaloEntreDatas(new DBDate(date('Y-m-d'), db_getsession('DB_datausu')), $this->oDataNascimento, 'y');
    }

    /**
     * Define sexo do servidor
     *
     * @param string $sSexo
     * @access public
     * @return void
     */
    public function setSexo($sSexo)
    {
        $this->sSexo = $sSexo;
    }

    /**
     * Retorna o sexo do servidor
     *
     * @access public
     * @return string
     */
    public function getSexo()
    {
        return $this->sSexo;
    }

    /**
     * Define tipo de exposicao a agentes nocivos
     *
     * @param string $sTipoExposicaoAgentesNocivos
     * @access public
     * @return void
     */
    public function setTipoExposicaoAgentesNocivos($sTipoExposicaoAgentesNocivos)
    {
        $this->sTipoExposicaoAgentesNocivos = $sTipoExposicaoAgentesNocivos;
    }

    /**
     * Retorna tipo de exposicao a agentes nocivos
     *
     * @access public
     * @return string
     */
    public function getTipoExposicaoAgentesNocivos()
    {
        return $this->sTipoExposicaoAgentesNocivos;
    }

    /**
     * Define o codigo do regime do servidor
     *
     * @param integer $iCodigoRegime
     * @access public
     * @return void
     */
    public function setCodigoRegime($iCodigoRegime)
    {
        $this->iCodigoRegime = $iCodigoRegime;
    }

    /**
     * Retorna o ano da competencia da folha
     *
     * @access public
     * @return integer
     */
    public function getAnoCompetencia()
    {
        return $this->iAnoCompetencia;
    }

    /**
     * Retorna o mes da competencia da folha
     *
     * @access public
     * @return integer
     */
    public function getMesCompetencia()
    {
        return $this->iMesCompetencia;
    }

    /**
     * Retorna o código do regime
     *
     * @param integer $iCodigoRegime
     * @access public
     * @return integer
     */
    public function getCodigoRegime()
    {

        return $this->iCodigoRegime;
    }

    /**
     * Retorna o código do tipo de regime
     *
     * @access public
     * @return int
     */
    public function getTipoRegime()
    {
        $oDaoRhRegime = new cl_rhregime;
        $sSqlRhRegime = $oDaoRhRegime->sql_query_file($this->iCodigoRegime);
        $rsRhRegime = $oDaoRhRegime->sql_record($sSqlRhRegime);
        if ($oDaoRhRegime->numrows == 0) {
            return;
        }

        return db_utils::fieldsMemory($rsRhRegime, 0)->rh30_regime;
    }

    /**
     * Define se o servidor possui moléstia grave
     * @param Boolean
     */
    public function setMolestiaGrave($lMolestiaGrave)
    {
        $this->lMolestiaGrave = $lMolestiaGrave;
    }

    /**
     * Retorna se o servidor possui moléstia grave
     * @return Boolean
     */
    public function getMolestiaGrave()
    {
        return $this->lMolestiaGrave;
    }

    /**
     * Retorna o Vinculo do Servidor
     *
     * @access public
     * @return VinculoServidor
     */
    public function getVinculo()
    {
        return VinculoServidorRepository::getInstanciaPorCodigo($this->iCodigoRegime);
    }

    /**
     * Retorna os documentos do servidor
     *
     * @access public
     * @return stdClass
     */
    public function getDocumentos()
    {

        if (empty($this->documentos)) {
            $oDaoRHPesDoc = new cl_rhpesdoc;
            $sCampos = "rh16_ctps_s, rh16_titele, rh16_catres, rh16_ctps_n, rh16_ctps_uf, rh16_secaoe, rh16_zonael, rh16_carth_n, ";
            $sCampos .= "rh16_reserv, rh16_carth_val, rh16_ctps_d, r16_carth_cat, rh16_pis";
            $sSqlDocumentos = $oDaoRHPesDoc->sql_query_file($this->getMatricula(), $sCampos);
            $rsDocumentos = $oDaoRHPesDoc->sql_record($sSqlDocumentos);

            $oRetorno = new stdClass();
            $oRetorno->iSerieCTPS = '';
            $oRetorno->sNumeroTituloEleitor = '';
            $oRetorno->sCategoriaCertificadoReservista = '';
            $oRetorno->iNumeroCTPS = '';
            $oRetorno->sUfCTPS = '';
            $oRetorno->sSecaoEleitoral = '';
            $oRetorno->sZonaEleitoral = '';
            $oRetorno->iNumeroCarteiraHabilitacao = '';
            $oRetorno->sCertificadoReservista = '';
            $oRetorno->dValidadeHabilitacao = '';
            $oRetorno->iDigitoCTPS = '';
            $oRetorno->sCategoriaHabilitacao = '';
            $oRetorno->sPIS = '';

            if (!$rsDocumentos || pg_num_rows($rsDocumentos) == 0) {
                return $oRetorno;
            }

            $oDocumentos = db_utils::fieldsMemory($rsDocumentos, 0);

            $oRetorno->iSerieCTPS = $oDocumentos->rh16_ctps_s; // Série da CTPS                     int4
            $oRetorno->sNumeroTituloEleitor = $oDocumentos->rh16_titele; // Número do Título de Eleitor       varchar(12)
            $oRetorno->sCategoriaCertificadoReservista = $oDocumentos->rh16_catres; // Categoria do certificado de reservista.               varchar(4)
            $oRetorno->iNumeroCTPS = $oDocumentos->rh16_ctps_n; // Carteira de Trab.e Prev.social                        int4
            $oRetorno->sUfCTPS = $oDocumentos->rh16_ctps_uf; // Unidade Federativa da CTPS                            varchar(2)
            $oRetorno->sSecaoEleitoral = $oDocumentos->rh16_secaoe; // Seção eleitoral.                                      varchar(4)
            $oRetorno->sZonaEleitoral = $oDocumentos->rh16_zonael; // Zona eleitoral                                        varchar(3)
            $oRetorno->iNumeroCarteiraHabilitacao = $oDocumentos->rh16_carth_n; // Nro da Carteira de Habilitacao                        int8
            $oRetorno->sCertificadoReservista = $oDocumentos->rh16_reserv; // Certificado de Reservista.                            varchar(15)
            $oRetorno->dValidadeHabilitacao = $oDocumentos->rh16_carth_val; // Data de validade da carteira nacional de habilitação. date
            $oRetorno->iDigitoCTPS = $oDocumentos->rh16_ctps_d; // Dígito da CTPS                                        int4
            $oRetorno->sCategoriaHabilitacao = $oDocumentos->r16_carth_cat; // Categoria da carteira nacional de habilitação.        varchar(3)
            $oRetorno->sPIS = $oDocumentos->rh16_pis; // Código do PIS/PASEP/CI                                varchar(11)
            $this->documentos = $oRetorno;
            unset($oDocumentos);
        }
        return $this->documentos;
    }

    /**
     * Retorna CalculoFolha pelo nome da tabela
     *
     * @param string $sCalculo - nome da tabela de calculo
     * @access public
     * @return CalculoFolha
     */
    public function getCalculoFinanceiro($sCalculo)
    {

        require_once(modification('model/pessoal/CalculoFolha.model.php'));

        $oCalculoFinanceiro = null;

        switch ($sCalculo) {
            case CalculoFolha::CALCULO_SALARIO:
                require_once(modification("model/pessoal/CalculoFolhaSalario.model.php"));
                $oCalculoFinanceiro = new CalculoFolhaSalario($this);
                break;

            case CalculoFolha::CALCULO_SUPLEMENTAR:
                require_once(modification("model/pessoal/CalculoFolhaSalario.model.php"));
                $oCalculoFinanceiro = new CalculoFolhaSalario($this);
                break;

            case CalculoFolha::CALCULO_ADIANTAMENTO:
                require_once(modification("model/pessoal/CalculoFolhaAdiantamento.model.php"));
                $oCalculoFinanceiro = new CalculoFolhaAdiantamento($this);
                break;

            case CalculoFolha::CALCULO_COMPLEMENTAR:
                require_once(modification("model/pessoal/CalculoFolhaComplementar.model.php"));
                $oCalculoFinanceiro = new CalculoFolhaComplementar($this);
                break;

            case CalculoFolha::CALCULO_RESCISAO:
                require_once(modification("model/pessoal/CalculoFolhaRescisao.model.php"));
                $oCalculoFinanceiro = new CalculoFolhaRescisao($this);
                break;

            case CalculoFolha::CALCULO_13o:
                require_once(modification("model/pessoal/CalculoFolha13o.model.php"));
                $oCalculoFinanceiro = new CalculoFolha13o($this);
                break;

            case CalculoFolha::CALCULO_FERIAS:
                require_once(modification("model/pessoal/CalculoFolhaFerias.model.php"));
                $oCalculoFinanceiro = new CalculoFolhaFerias($this);
                break;

            case CalculoFolha::CALCULO_PROVISAO_FERIAS:
                require_once(modification("model/pessoal/CalculoFolhaProvisaoFerias.model.php"));
                $oCalculoFinanceiro = new CalculoFolhaProvisaoFerias($this);
                break;

            case CalculoFolha::CALCULO_PROVISAO_13o:
                require_once(modification("model/pessoal/CalculoFolhaProvisao13o.model.php"));
                $oCalculoFinanceiro = new CalculoFolhaProvisao13o($this);
                break;

            case CalculoFolha::CALCULO_PONTO_FIXO:
                require_once(modification("model/pessoal/CalculoFolhaProvisao13o.model.php"));
                $oCalculoFinanceiro = new CalculoFolhaFixo($this);
                break;

            default:
                throw new BusinessException("Calculo não implementado: " . $sCalculo);
                break;
        }

        return $oCalculoFinanceiro;
    }

    /**
     * Retorna o Ponto pelo tipo de ponto
     *
     * @param string $sPonto - tabela de ponto
     * @return \Ponto
     * @throws \BusinessException
     * @access public
     */
    public function getPonto($sPonto)
    {

        switch ($sPonto) {
            case Ponto::COMPLEMENTAR:
                require_once(modification("model/pessoal/PontoComplementar.model.php"));
                $oPonto = new PontoComplementar($this);
                break;

            case Ponto::FERIAS:
                require_once(modification("model/pessoal/PontoFerias.model.php"));
                $oPonto = new PontoFerias($this);
                break;

            case Ponto::FIXO:
                require_once(modification("model/pessoal/PontoFixo.model.php"));
                $oPonto = new PontoFixo($this);
                break;

            case Ponto::SALARIO:
                require_once(modification("model/pessoal/PontoSalario.model.php"));
                $oPonto = new PontoSalario($this);
                break;

            case Ponto::ADIANTAMENTO:
                require_once(modification("model/pessoal/PontoAdiantamento.model.php"));
                $oPonto = new PontoAdiantamento($this);
                break;

            case Ponto::PONTO_13o:
                require_once(modification("model/pessoal/Ponto13o.model.php"));
                $oPonto = new Ponto13o($this);
                break;

            case Ponto::RESCISAO:
                require_once(modification("model/pessoal/PontoRescisao.model.php"));
                $oPonto = new PontoRescisao($this);
                break;

            case Ponto::PROVISAO_13o:
                require_once(modification("model/pessoal/PontoProvisao13o.model.php"));
                $oPonto = new PontoProvisao13o($this);
                break;

            case Ponto::PROVISAO_FERIAS:
                require_once(modification("model/pessoal/PontoProvisaoFerias.model.php"));
                $oPonto = new PontoProvisaoFerias($this);
                break;

            default:
                throw new BusinessException("Ponto não implementado: " . $sPonto);
                break;
        }

        return $oPonto;
    }

    /**
     * Retorna uma coleção de objetos da classe dependente, relacionados ao servidor instânciado no objeto
     * @throws BusinessException Matrícula não informada
     * @return Dependente[]
     */
    public function getDependentes()
    {

        $this->aDependentes = [];
        require_once(modification('model/pessoal/Dependente.model.php'));

        if (empty($this->iMatricula)) {
            throw new BusinessException('Matrícula do servidor não informada para consulta dos dependentes.');
        }

        $oDaoRhDepend = new cl_rhdepend;
        $sSqlDependentes = $oDaoRhDepend->sql_query_file(
            null,
            "*",
            "rh31_codigo",
            "rh31_regist = {$this->getMatricula()} and rh31_irfcomplementar != true"
        );
        $rsDependentes = $oDaoRhDepend->sql_record($sSqlDependentes);

        if (!$rsDependentes || pg_num_rows($rsDependentes) == 0) {
            return array();
        }

        $aDependentes = db_utils::getCollectionByRecord($rsDependentes);
        foreach ($aDependentes as $oDependente) {
            $this->aDependentes[$oDependente->rh31_codigo] =  \Dependente::find($oDependente->rh31_codigo);
        }

        return $this->aDependentes;
    }

    /**
     * Retorna a variável da progressão do Salário Base do Servidor
     *
     * @param integer $iAnoCompetencia
     * @param integer $iMesCompetencia
     * @param integer $iMatricula
     * @param integer $iInstituicao
     * @param string $sVariavel
     * @return void|number
     */
    public function getValorVariaveisCalculo($iAnoCompetencia, $iMesCompetencia, $iMatricula, $iInstituicao, $sVariavel)
    {

        $oDaoRhPessoalMov = new cl_rhpessoalmov();
        $sSqlValorVariaveisCalculo = $oDaoRhPessoalMov->sql_queryValorVariaveisCalculo($iAnoCompetencia, $iMesCompetencia, $iMatricula, $iInstituicao);
        $rsValorVariaveisCalculo = $oDaoRhPessoalMov->sql_record($sSqlValorVariaveisCalculo);

        if (!$rsValorVariaveisCalculo || pg_num_rows($rsValorVariaveisCalculo) == 0) {
            return;
        }

        switch ($sVariavel) {
            case Servidor::VARIAVEL_SALARIO_BASE_PROGRESSAO:
                return db_utils::fieldsMemory($rsValorVariaveisCalculo, 0)->variavel_salario_base_progressao;
                break;

            default:
                return 0;
                break;
        }
    }

    /**
     * Retorna o servidor de origem da matricula, quando for um pensionista
     * @throws Exception Matricula deve ser informada
     * @return object Servidor
     */
    public function getServidorOrigem()
    {

        if (empty($this->iMatricula)) {
            throw new Exception('Matrícula do servidor não informada.');
        }

        $oDaoRhpesorigem = new cl_rhpesorigem;
        $sSqlServidorOrigem = $oDaoRhpesorigem->sql_queryServidorOrigem($this->getMatricula());
        $rsServidorOrigem = $oDaoRhpesorigem->sql_record($sSqlServidorOrigem);

        if (!$rsServidorOrigem || pg_num_rows($rsServidorOrigem) == 0) {
            return false;
        }

        $oServidor = db_utils::fieldsMemory($rsServidorOrigem, 0);

        return new Servidor($oServidor->rh01_regist, $this->iAnoCompetencia, $this->iMesCompetencia, $oServidor->rh01_instit);
    }

    /**
     * Retorna codigo da tarefa.
     *
     * @access public
     * @return integer
     */
    public function getCodigoLotacao()
    {
        return $this->iCodigoLotacao;
    }

    /**
     * Retorna o valor do salario.
     *
     * @access public
     * @return integer
     */
    /**
     * Retorna o valor do salario. Caso parâmetro $retornarValorPadrao seja passado como true, e o salário está como
     * zero, busca o salário padrão.
     *
     * @param bool $retornarValorPadrao
     * @return mixed
     * @throws DBException
     */

    public function setTpCont($tpCont)
    {
        $this->iTpCont = $tpCont;
    }

    public function getTpCont()
    {
        return $this->iTpCont;
    }

    public function getSalario($retornarValorPadrao = false)
    {
        $salario = $this->iSalario;

        if ($retornarValorPadrao === true && empty($salario)) {
            $daoRhPesPadrao = new cl_rhpespadrao();
            $sqlRhPesPadrao = $daoRhPesPadrao->sql_query_padroes($this->iCodigoMovimentacao, 'r02_valor');
            $rsRhPesPadrao = $daoRhPesPadrao->sql_record($sqlRhPesPadrao);

            if (!$rsRhPesPadrao && $daoRhPesPadrao->erro_sql == "Erro ao selecionar os registros.") {
                throw new DBException('Erro ao buscar o valor padrão do salário.\n' . $daoRhPesPadrao->erro_msg);
            }

            if ($daoRhPesPadrao->numrows === 0) {
                return $salario;
            }

            $salario = db_utils::fieldsMemory($rsRhPesPadrao, 0)->r02_valor;
        }

        return $salario;
    }

    public function getSalarioBase()
    {

        $oVariaveis = DBPessoal::getVariaveisCalculo($this);
        return $oVariaveis->f010;
    }

    /**
     * Retorna se o servidor esta ativo nesta competencia
     * @return bool
     * @throws \BusinessException
     */
    public function isAtivo($ignoraException = null)
    {

        if (empty($this->iMatricula)) {
            throw new BusinessException('Matrícula do servidor não informada para verificação de servidor ativo.');
        }

        $oDaoRhPessoal = new cl_rhpessoal;
        $rsSituacao = $oDaoRhPessoal->sql_record($oDaoRhPessoal->sql_verificaSituacaoServidor(
            $this->iMatricula,
            $this->iAnoCompetencia,
            $this->iMesCompetencia
        ));

        if ($rsSituacao && pg_num_rows($rsSituacao) > 0) {
            $oRhPessoal = db_utils::fieldsMemory($rsSituacao, 0);
            return ($oRhPessoal->rh30_vinculo == 'A');
        }

        if ($ignoraException) {
            return false;
        }

        throw new BusinessException('Não foi possivel verificar se o servidor de matrícula ' . $this->iMatricula . ' esta ativo. Favor revisar.');
    }

    /**
     * Retorna se o servidor é pensionista
     * @return bool
     * @throws \BusinessException
     */
    public function isPensionista($ignoraException = null)
    {

        if (empty($this->iMatricula)) {
            throw new BusinessException('Matrícula do servidor não informada para verificação de servidor pensionista.');
        }

        $oDaoRhPessoal = new cl_rhpessoal;
        $rsSituacao = $oDaoRhPessoal->sql_record($oDaoRhPessoal->sql_verificaSituacaoServidor(
            $this->iMatricula,
            $this->iAnoCompetencia,
            $this->iMesCompetencia
        ));

        if ($rsSituacao && pg_num_rows($rsSituacao) > 0) {
            $oRhPessoal = db_utils::fieldsMemory($rsSituacao, 0);
            return ($oRhPessoal->rh30_vinculo == 'P');
        }

        if ($ignoraException) {
            return false;
        }

        throw new BusinessException('Não foi possivel verificar se o servidor é pensionista.');
    }


    /**
     * Retorna se o servidor esta rescindido nesta competencia
     * @return bool
     * @throws \BusinessException
     */
    public function isRescindido()
    {
        $dadosRescisao = $this->getRescisao();
        if (!empty($dadosRescisao)) {
            return !empty($dadosRescisao->rh05_recis);
        }
        return false;
    }

    /**
     * Retorna se o servidor esta rescindido na competencia informada
     * @return bool
     * @throws \BusinessException
     */
    public function isRescindidoCompetencia()
    {
        $dadosRescisao = $this->getRescisaoNaCompetencia();
        if (!empty($dadosRescisao)) {
            return !empty($dadosRescisao->rh05_recis);
        }
        return false;
    }

    /**
     * Retorna se o Servidor está afastado na competencia
     * @return bool
     * @throws \BusinessException
     */
    public function isAfastado()
    {

        if (empty($this->iMatricula) || empty($this->iAnoCompetencia) || empty($this->iMesCompetencia) || empty($this->iCodigoInstituicao)) {
            throw new BusinessException("Ocorreu um erro ao consultar os afastamentos para o servidor.");
        }

        $rsAfastamentoServidor = db_query("select conta_dias_afasta({$this->iMatricula},
                                                                {$this->iAnoCompetencia},
                                                                {$this->iMesCompetencia},
                                                                ndias({$this->iAnoCompetencia}, {$this->iMesCompetencia}),
                                                                {$this->iCodigoInstituicao}) as afastamento");


        if (!$rsAfastamentoServidor) {
            throw new BusinessException("Ocorreu um erro ao consultar os afastamentos para o servidor.");
        }

        if (pg_num_rows($rsAfastamentoServidor) > 0) {
            $nAfastamento = db_utils::fieldsMemory($rsAfastamentoServidor, 0)->afastamento;
            return $nAfastamento > 0 ? $nAfastamento : false;
        }

        return false;
    }

    /**
     * Retorna se o servidor está afastado no RH
     * @param  \DBDate $dataAfastamento
     * @return bool
     */
    public function isAfastadoNoRH(\DBDate $dataAfastamento)
    {

        $assentamentos = AssentamentoRepository::getAssentamentosServidorPorTipoENatureza($this, 'A', $dataAfastamento);
        if (empty($assentamentos)) {
            return false;
        }

        return true;
    }

    /**
     * getContaBancaria
     *
     * @access public
     * @return \ContaBancaria
     * @throws \DBException
     */
    public function getContaBancaria()
    {

        if (is_null($this->oContaBancaria) && $this->iCodigoMovimentacao) {
            $oDaoRHPessoalMovContaBancaria = new cl_rhpessoalmovcontabancaria();
            $sSqlContaBancaria = $oDaoRHPessoalMovContaBancaria->sql_query_file(null, "rh138_contabancaria", null, "rh138_rhpessoalmov = {$this->iCodigoMovimentacao}");
            $rsContaServidor = db_query($sSqlContaBancaria);
            if (!$rsContaServidor) {
                throw new DBException("Erro ao buscar os dados da Conta Bancária.\n" .  $oDaoRHPessoalMovContaBancaria->erro_msg);
            }

            $iCodigo = null;

            if (pg_num_rows($rsContaServidor) > 0) {
                $iCodigo = db_utils::fieldsMemory($rsContaServidor, 0)->rh138_contabancaria;
                $this->oContaBancaria = new ContaBancaria($iCodigo);
            } else {
                $this->oContaBancaria = new ContaBancaria();
            }
        }
        return $this->oContaBancaria;
    }

    /**
     * Diz se o servidor pertence a um regime de rpps
     *
     * @return bool
     */
    public function isRpps($ano = null, $mes = null)
    {
        $tipoPrevidencia = null;
        if (!empty($ano) && !empty($mes)) {
            $tipoPrevidencia = $this->getTipoPrevidencia($ano, $mes);
        }
        if (empty($tipoPrevidencia)) {
            $tipoPrevidencia = $this->getTipoPrevidencia();
        }
        if (!empty($tipoPrevidencia)) {
            if (!$this->isRgps()) {
                return true;
            }
        } else {
            $cedencia = new \Cedencia($this->getMatricula());
            if (!empty($cedencia->getMatricula())) {
                if ($cedencia->getTipoRegimePrevidencia() == 2) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Diz se o servidor pertence a um regime de rgps
     * @return bool
     */
    public function isRgps($ano = null, $mes = null)
    {
        $tipoPrevidencia = null;
        if (!empty($ano) && !empty($mes)) {
            $tipoPrevidencia = $this->getTipoPrevidencia($ano, $mes);
        }
        if (empty($tipoPrevidencia)) {
            $tipoPrevidencia = $this->getTipoPrevidencia();
        }
        // 2 -> RGPS
        // outros -> RPPS
        switch ($tipoPrevidencia) {
            case 2:
                return true;
            default:
                return false;
        }
        return false;
    }

    public function getTipoPrevidencia($ano = null, $mes = null)
    {
        if (empty($ano)) {
            $ano = "fc_anofolha({$this->getCodigoInstituicao()})";
        }
        if (empty($mes)) {
            $mes = "fc_mesfolha({$this->getCodigoInstituicao()})";
        }
        /*
         * É somado + 2 pois o codigo 1 e 2 nao IRFF e na tela da movimentacao ele nao é exibido
         * e o codigo 1 que aparece é referente ao codigo 3
         * enfim, a regra é assim =/
         */
        $tabelaPrevidencia = $this->getTabelaPrevidencia() + 2;
        $sql = "
            select
                rh129_regimeprevidencia
            from
                 pessoal.inssirf
                 inner join pessoal.regimeprevidenciainssirf on rh129_codigo = r33_codigo and rh129_instit = r33_instit
            where
                r33_anousu = {$ano}
                and r33_mesusu = {$mes}
                and r33_instit = {$this->getCodigoInstituicao()}
                and r33_codtab = {$tabelaPrevidencia}";
        $rs = db_query($sql);

        if (!$rs) {
            $msg = "Houve um erro ao buscar a tabela de previdencia do servidor: {$this->getMatricula()}";
            throw new DBException($msg);
        }
        if (pg_num_rows($rs) > 0) {
            return db_utils::fieldsMemory($rs, 0)->rh129_regimeprevidencia;
        }
        return false;
    }

    public function setContaBancaria(ContaBancaria $oConta)
    {
        $this->oContaBancaria = $oConta;
    }

    public function getCodigoMovimentacao()
    {
        return $this->iCodigoMovimentacao;
    }

    /**
     * @return int
     */
    public function getEstadoCivil()
    {
        return $this->estadoCivil;
    }

    /**
     * @param int $estadoCivil
     */
    public function setEstadoCivil($estadoCivil)
    {
        $this->estadoCivil = $estadoCivil;
    }

    /**
     * @return string
     */
    public function getNaturalidade()
    {
        return $this->naturalidade;
    }

    /**
     * @param string $naturalidade
     */
    public function setNaturalidade($naturalidade)
    {
        $this->naturalidade = $naturalidade;
    }


    public function salvar()
    {

        if (is_null($this->oContaBancaria)) {
            return true;
        }

        $iCodigoContaBancaria = $this->oContaBancaria->salvar();
        $oDaoRHPessoalMovContaBancaria = new cl_rhpessoalmovcontabancaria();
        db_query("delete from rhpessoalmovcontabancaria where rh138_rhpessoalmov = $this->iCodigoMovimentacao;");
        $oDaoRHPessoalMovContaBancaria->rh138_rhpessoalmov = $this->iCodigoMovimentacao;
        $oDaoRHPessoalMovContaBancaria->rh138_contabancaria = $iCodigoContaBancaria;
        $oDaoRHPessoalMovContaBancaria->rh138_instit = db_getsession("DB_instit");
        $oDaoRHPessoalMovContaBancaria->incluir(null);
        return true;
    }

    /**
     * Retorna a Instancia de Servidor vinculada ao CGM do Servidor Atual
     * @return \Servidor
     * @throws \BusinessException
     * @throws \DBException
     */
    public function getServidorVinculado()
    {

        if ($this->oDuploVinculo !== null) {
            return $this->oDuploVinculo;
        }

        $oDaoRHPessoalMov = new cl_rhpessoalmov();
        $sSqlVinculo = $oDaoRHPessoalMov->sql_duplo_vinculo_matricula($this->getMatricula(), $this->getAnoCompetencia(), $this->getMesCompetencia());

        $rsQuery = db_query($sSqlVinculo);

        if (!$rsQuery) {
            throw new DBException("Erro ao buscar vinculo do Servidor.\n");
        }

        if (pg_num_rows($rsQuery) == 0) {
            return false;
        }
        $iMatricula = db_utils::fieldsMemory($rsQuery, 0)->rh01_regist;
        $this->oDuploVinculo = ServidorRepository::getInstanciaByCodigo($iMatricula, $this->getAnoCompetencia(), $this->getMesCompetencia());
        return true;
    }

    /**
     * Verifica se o servidor tem duplo vinculo
     */
    public function hasServidorVinculado()
    {

        $this->getServidorVinculado();
        return $this->oDuploVinculo !== null;
    }

    /**
     * Define se o servidor possui ou não Abono de Permanência.
     * @param boolean $lAbonoPermanencia
     */
    public function setAbonoPermanencia($lAbonoPermanencia)
    {
        $this->lAbonoPermanencia = (bool)($lAbonoPermanencia == 't');
    }

    /**
     * Verifica se o Servidor possui Abono de Permanência.
     * @return boolean - True Possuí abono de permanência.
     *                 - False Não Possuí Abono de Permanência.
     */
    public function hasAbonoPermanencia()
    {
        return $this->lAbonoPermanencia;
    }

    /**
     * Retorna o valor da margem consignável.
     *
     * @access public
     * @return Integer
     * @throws DBException
     */
    public function getMargemConsignavel($sRubrica = "R803")
    {

        /**
         * R803 é a rubrica da margem consignada.
         */
        $oDaoGerfsal = new cl_gerfsal();
        $sRubricaSqlGerfsal = $oDaoGerfsal->sql_query_file($this->iAnoCompetencia, $this->iMesCompetencia, $this->iMatricula, $sRubrica);
        $rsRubricaSqlGerfsal = $oDaoGerfsal->sql_record($sRubricaSqlGerfsal);

        if (!$rsRubricaSqlGerfsal && $oDaoGerfsal->erro_sql == "Erro ao selecionar os registros.") {
            $sMsg = "Ocorreu um erro ao buscar informações da rubrica {$sRubrica}, matricula {$this->iMatricula}\n";
            $sMsg .= "{$oDaoGerfsal->erro_msg}";
            throw new DBException($sMsg);
        }

        if (pg_num_rows($rsRubricaSqlGerfsal) > 0) {
            for ($i = 0; $i < pg_num_rows($rsRubricaSqlGerfsal); $i++) {
                $oBase = db_utils::fieldsMemory($rsRubricaSqlGerfsal, $i, false, false, true);

                if ($oBase->r14_rubric == $sRubrica) {
                    return $oBase->r14_valor;
                }
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de assentamentos de substituicao do servidor
     * @return \AssentamentoSubstituicao[]
     * @throws \BusinessException
     */
    public function getAssentamentosSubstituicao()
    {

        $aListaAssentamentos = array();
        $oDaoAssentamento = new cl_assenta();
        $sCamposAssentamento = "h16_codigo as assentamento,
                              assentaloteregistroponto.*,
                              loteregistroponto.*";

        $sWhereAssentamento = "h16_regist = {$this->iMatricula}                         ";
        $sWhereAssentamento .= "and h12_natureza = " . Assentamento::NATUREZA_SUBSTITUICAO;
        $sWhereAssentamento .= "and (rh160_assentamento is null                          ";
        $sWhereAssentamento .= "     or (rh155_ano     = {$this->iAnoCompetencia}        ";
        $sWhereAssentamento .= "         and rh155_mes = {$this->iMesCompetencia}))      ";

        $sSqlAssentamento = $oDaoAssentamento->sql_query_assentamento_com_substituicao(
            null,
            $sCamposAssentamento,
            "h16_regist, h16_dtconc desc",
            $sWhereAssentamento
        );

        $rsAssentamento = $oDaoAssentamento->sql_record($sSqlAssentamento);

        if (!$rsAssentamento && $oDaoAssentamento->erro_sql == "Erro ao selecionar os registros.") {
            throw new BusinessException("Erro ao buscar assentamentos para o servidor.\n" . $oDaoAssentamento->erro_msg);
        } else {
            if (pg_num_rows($rsAssentamento) > 0) {
                $aAssentamentos = db_utils::getCollectionByRecord($rsAssentamento);

                foreach ($aAssentamentos as $oStdAssentamento) {
                    $oAssentamento = AssentamentoRepository::getInstanceByCodigo($oStdAssentamento->assentamento);

                    if ($oAssentamento instanceof AssentamentoSubstituicao) {
                        $aListaAssentamentos[] = $oAssentamento;
                    }
                }
            }
        }

        return $aListaAssentamentos;
    }

    /**
     * Retorna o número de dias de férias padrão do servidor
     * @return Integer
     */
    public function getDiasGozoFerias()
    {
        return $this->iDiasGozoFerias;
    }

    /**
     * Define o número de dias de férias padrão do servidor
     * @param Integer $iDiasGozoFerias
     */
    public function setDiasGozoFerias($iDiasGozoFerias)
    {
        $this->iDiasGozoFerias = $iDiasGozoFerias;
    }

    /**
     * Verifica se possui vinculcado inativo ou pensionisa maior de 65 anos
     *
     * @return boolean [description]
     */
    public function hasVinculadoInativoPensionistaMaior65Anos()
    {

        $lVinculoServidorVinculadoInativo = false;
        $lVinculoServidorVinculadoPensionista = false;
        $lServidorVinculadoMaior65Anos = false;

        if ($this->hasServidorVinculado()) {
            $oServidorVinculado = $this->getServidorVinculado();

            $lVinculoServidorVinculadoInativo = $oServidorVinculado->getVinculo()->getTipo() == VinculoServidor::VINCULO_INATIVO;
            $lVinculoServidorVinculadoPensionista = $oServidorVinculado->getVinculo()->getTipo() == VinculoServidor::VINCULO_PENSIONISTA;

            if ($oServidorVinculado->getIdade() >= 65) {
                $lServidorVinculadoMaior65Anos = true;

                if ($lVinculoServidorVinculadoInativo || $lVinculoServidorVinculadoPensionista) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica se o servidor possui remuneracao no periodo *
     * Para os ervidor possuir remuneracao, ele nao deve ter seu pagamento suspenso nem estar afastado sem remuneracao
     * @return bool
     * @throws \BusinessException
     */
    public function temRemuneracaoNoPeriodo()
    {

        $oDaoAfastamento = new cl_afasta();
        $iUltimoDiaCompetencia = cal_days_in_month(CAL_GREGORIAN, $this->getMesCompetencia(), $this->getAnoCompetencia());
        $sDataFinal = "{$this->getAnoCompetencia()}-{$this->getMesCompetencia()}-{$iUltimoDiaCompetencia}";
        $aWhere[] = "r45_anousu = {$this->getAnoCompetencia()}";
        $aWhere[] = "r45_mesusu = {$this->getMesCompetencia()}";
        $aWhere[] = "r45_regist = {$this->getMatricula()}";
        $aWhere[] = "r45_situac IN (2, 3, 4, 6, 7)";
        $aWhere[] = "(r45_dtreto is null or r45_dtreto >= '{$sDataFinal}')";
        $sWhere = implode(" and ", $aWhere);
        $sSqlAfastamentosSemRemuneracao = $oDaoAfastamento->sql_query_file(null, "*", null, $sWhere);
        $rsAfastamentos = $oDaoAfastamento->sql_record($sSqlAfastamentosSemRemuneracao);
        if (!$rsAfastamentos && $oDaoAfastamento->erro_sql == "Erro ao selecionar os registros.") {
            throw new BusinessException("1 - Erro ao buscar afastamentos para o servidor\n$oDaoAfastamento->erro_msg");
        }

        if ($oDaoAfastamento->numrows > 0) {
            return false;
        }
        return true;
    }

    /**
     * @param \DBCompetencia $oCompetencia
     * @return array
     * @throws \BusinessException
     */
    public function getAfastamentosNoPeriodo(DBCompetencia $oCompetencia = null)
    {

        if (empty($oCompetencia)) {
            $oCompetencia = new DBCompetencia($this->getAnoCompetencia(), $this->getMesCompetencia());
        }

        $aAfastamentos = array();
        $oDaoAfastamento = new cl_afasta();
        $iUltimoDiaCompetencia = cal_days_in_month(CAL_GREGORIAN, $oCompetencia->getMes(), $oCompetencia->getAno());

        $sDataFinal = "{$oCompetencia->getAno()}-{$oCompetencia->getMes()}-{$iUltimoDiaCompetencia}";
        $sDataInicial = "{$oCompetencia->getAno()}-{$oCompetencia->getMes()}-01";
        $aWhere[] = "r45_anousu = {$oCompetencia->getAno()}";
        $aWhere[] = "r45_mesusu = {$oCompetencia->getMes()}";
        $aWhere[] = "r45_regist = {$this->getMatricula()}";

        $sWhereDatas = " ((r45_dtreto is null and r45_dtafas <= '{$sDataFinal}')";
        $sWhereDatas .= " or (r45_dtafas >= '{$sDataInicial}' and r45_dtafas <= '{$sDataFinal}')";
        $sWhereDatas .= " or (r45_dtafas <= '{$sDataInicial}' and r45_dtreto >= '{$sDataInicial}'))";

        $aWhere[] = $sWhereDatas;
        $sWhere = implode(" and ", $aWhere);
        $sSqlAfastamentosSemRemuneracao = $oDaoAfastamento->sql_query_file(null, "*", 'r45_dtafas, r45_dtreto', $sWhere);
        $rsAfastamentos = $oDaoAfastamento->sql_record($sSqlAfastamentosSemRemuneracao);
        if (!$rsAfastamentos && $oDaoAfastamento->erro_sql == "Erro ao selecionar os registros.") {
            throw new BusinessException("2 - Erro ao buscar afastamentos para o servidor\n$oDaoAfastamento->erro_msg");
        }

        if ($oDaoAfastamento->numrows > 0) {
            for ($iAfastamento = 0; $iAfastamento < $oDaoAfastamento->numrows; $iAfastamento++) {
                $oAfastamentoStd = db_utils::fieldsMemory($rsAfastamentos, $iAfastamento);
                $oAfastamento = AfastamentoRepository::getInstanciaPorCodigo($oAfastamentoStd->r45_codigo);

                $oAfastamentoStd->dias = $oAfastamento->getNumeroDeDiasNaCompetencia($oCompetencia);
                if (isset($aAfastamentos[$oAfastamentoStd->r45_situac])) {
                    $aAfastamentos[$oAfastamentoStd->r45_situac]->dias += $oAfastamentoStd->dias;
                    continue;
                }
                $aAfastamentos[$oAfastamentoStd->r45_situac] = $oAfastamentoStd;
            }
            $aAfastamentos[$oAfastamentoStd->r45_situac] = $oAfastamentoStd;
        }
        return $aAfastamentos;
    }

    /**
     * Retorna as escalas do servidor
     * @return \ECidade\RecursosHumanos\RH\Efetividade\Model\EscalaServidor[]
     */
    public function getEscalas($oDataPonto = null)
    {
        if (!empty($oDataPonto)) {
            return $this->getEscala($oDataPonto);
        }

        if (empty($this->escalas)) {
            $this->escalas = EscalaServidor::getEscalas($this, null);
        }

        return $this->escalas;
    }

    /**
     * Retorna a escala do servidor em uma data específica
     * @return \ECidade\RecursosHumanos\RH\Efetividade\Model\EscalaServidor
     */
    public function getEscala($oDataPonto, $forcar = false)
    {

        if ($forcar || empty($this->escala)) {
            $this->escala = EscalaServidor::getEscalas($this, $oDataPonto);
        }

        return $this->escala;
    }

    /**
     * Define a escala do servidor
     * @param \ECidade\RecursosHumanos\RH\Efetividade\Model\EscalaServidor
     */
    public function setEscala($escala)
    {

        if (!empty($escala)) {
            $this->escala = $escala;
        }

        return $this;
    }

    /**
     * Define as escala para o servidor
     * @param \ECidade\RecursosHumanos\RH\Efetividade\Model\EscalaServidor[]
     */
    public function setEscalas(array $escalas)
    {

        if (!empty($escalas)) {
            $this->escalas = $escalas;
        }

        return $this;
    }

    /**
     * Define o PIS/PASEP
     * @param string
     */
    public function setPISPASEP($sPISPASEP)
    {
        $this->sPISPASEP = $sPISPASEP;
    }

    /**
     * Retorna o PIS/PASEP
     * @return string
     */
    public function getPISPASEP()
    {
        return $this->sPISPASEP;
    }

    /**
     * Define se o servidor registra ponto eletrônico
     * @param boolean $lRegistraPontoEletronico
     */
    public function setDispensaLancamentoPonto($lRegistraPontoEletronico)
    {
        $this->lRegistraPontoEletronico = $lRegistraPontoEletronico;
    }

    /**
     * Retorna se o servidor registra ponto eletrônico
     * @return bool
     */
    public function registraPontoEletronico()
    {
        return $this->lRegistraPontoEletronico;
    }

    /**
     * @return stdClass
     * @throws DBException
     */
    public function getDadosCargo()
    {
        $oDaoRHPessoal = new cl_rhpessoal();
        $sSqlRHPessoal = $oDaoRHPessoal->sql_query_cargo($this->iMatricula, 'rhfuncao.*');
        $rsRHPessoal = $oDaoRHPessoal->sql_record($sSqlRHPessoal);

        if (!$rsRHPessoal && $oDaoRHPessoal->erro_sql == "Erro ao selecionar os registros.") {
            throw new DBException("Erro ao buscar os dados do Cargo do Servidor, matricula {$this->iMatricula}.\n" . $oDaoRHPessoal->erro_msg);
        }

        return db_utils::makeFromRecord($rsRHPessoal, function ($oRetorno) {
            return $oRetorno;
        }, 0);
    }

    /**
     * @return stdClass
     * @throws DBException
     */
    public function getPadrao()
    {

        $aWhere = array(
            'rh02_anousu =' . $this->getAnoCompetencia(),
            'rh02_mesusu =' . $this->getMesCompetencia(),
            'rh02_instit =' . $this->getInstituicao()->getSequencial(),
            'rh02_regist =' . $this->getMatricula()
        );

        $aWhere = array_filter($aWhere);

        $sSql = "SELECT  padroes.*, padroesfilho.r02_codigo as filho_codigo ,padroesfilho.r02_descr as filho_desc ,padroesfilho.r02_classe as
        filho_classe, padroesfilho.r02_nivel as  filho_nivel  FROM  rhpessoalmov                                                 \n";
        $sSql .= "       inner  join rhpespadrao          on rhpespadrao.rh03_seqpes       = rhpessoalmov.rh02_seqpes            \n";
        $sSql .= "                                      and rhpespadrao.rh03_anousu       = rhpessoalmov.rh02_anousu             \n";
        $sSql .= "                                      and rhpespadrao.rh03_mesusu       = rhpessoalmov.rh02_mesusu             \n";
        $sSql .= "       inner  join padroes              on padroes.r02_anousu            = rhpespadrao.rh03_anousu             \n";
        $sSql .= "                                      and padroes.r02_mesusu            = rhpespadrao.rh03_mesusu              \n";
        $sSql .= "                                      and padroes.r02_regime            = rhpespadrao.rh03_regime              \n";
        $sSql .= "                                      and padroes.r02_codigo            = rhpespadrao.rh03_padrao              \n";
        $sSql .= "                                      and padroes.r02_instit            = rhpessoalmov.rh02_instit             \n";
        $sSql .= "       inner  join padroes as padroesfilho   on padroesfilho.r02_padraopai_codigo  = padroes.r02_codigo        \n";
        $sSql .= "                                                AND  padroesfilho.r02_mesusu = padroes.r02_mesusu              \n";
        $sSql .= "                                                AND padroesfilho.r02_anousu = padroes.r02_anousu               \n";
        $sSql .= "                                                AND padroesfilho.r02_padraopai_regime = padroes.r02_regime     \n";

        $sSql .= " WHERE " . implode(" AND ", $aWhere);

        $rsPadrao = db_query($sSql);

        if (pg_num_rows($rsPadrao) > 1) {
            throw new DBException('Erro ao buscar o padrão , existe um mesmo padrão vinculado a outros padrões.');
        }

        return pg_fetch_object($rsPadrao);
    }

    /**
     * @param null|int $mes
     * @param null|int $ano
     * @return LocalTrabalho[]
     * @throws DBException
     */
    public function getLocaisTrabalho($mes = null, $ano = null)
    {
        if (empty($this->locaisTrabalho)) {
            $locaisTrabalhoRepository = \LocalTrabalhoRepository::getInstance();
            $this->locaisTrabalho = $locaisTrabalhoRepository->getLocalTrabalhoPorServidor($this, $ano, $mes);
        }

        return $this->locaisTrabalho;
    }

    /*
    * Retorna a identificação de lotacao/lotações no periodo de competência definido.
    */
    public function getLotacaoPorPeriodo(\Servidor $servidor,
        $ano = null,
        $mes = null,
        $anoCompetenciaAtual = null,
        $mesCompetenciaAtual = null)
    {

        if (empty($ano)) {
            $ano = \DBPessoal::getAnoFolha();
        }

        if (empty($mes)) {
            $mes = \DBPessoal::getMesFolha();
        }
        if (empty($anoCompetenciaAtual)) {
            $anoCompetenciaAtual = \DBPessoal::getAnoFolha();
        }

        if (empty($mesCompetenciaAtual)) {
            $mesCompetenciaAtual = \DBPessoal::getMesFolha();
        }

        $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
        $ano = str_pad($ano, 4, '0', STR_PAD_LEFT);
        $instituicaoServidor = $servidor->getCodigoInstituicao();
        $matricula = $servidor->getMatricula();

        $daoRhLocalTrab = new cl_rhlocaltrab();
        $where  = " rh55_instit = {$instituicaoServidor}";
        $where .= " and rh02_anousu = {$anoCompetenciaAtual}";
        $where .= " and rh02_mesusu = {$mesCompetenciaAtual}";
        $where .= " and rh02_regist = {$matricula}";
        $whereDataInicio = " and rh56_datainicio is not null";
        $order = "rh56_datainicio, rh56_datafim";

        $sql = $daoRhLocalTrab->sql_query_servidor(
            "distinct on (rh56_datainicio)
            rh55_lotacaotributaria as lotacaoTributaria,
            rh56_datainicio as datainicio,
            rh56_datafim as datafim,
            date_part('year', rh56_datainicio) as anoInicio,
            date_part('month', rh56_datainicio) as mesInicio,
            date_part('year', rh56_datafim) as anoFim,
            date_part('month', rh56_datafim) as mesFim,
            lag(rh56_datafim) over (order by rh56_datafim) as dataFimAnterior
            ",
            $where . $whereDataInicio,
            $order
        );

        $result = db_query($sql);

        // Validamos se não retorna nenhuma configuração de local de trabalho
        if (pg_num_rows($result) == 0) {
            // caso nao tenha, buscamos mais uma vez se existe pelo menos 1 local de trabalho, mesmo não preenchido os
            // dados de data inicio
            $sql = $daoRhLocalTrab->sql_query_servidor(
                "distinct on (rh56_datainicio)
                rh55_lotacaotributaria as lotacaoTributaria,
                rh56_datainicio as datainicio,
                rh56_datafim as datafim,
                date_part('year', rh56_datainicio) as anoInicio,
                date_part('month', rh56_datainicio) as mesInicio,
                date_part('year', rh56_datafim) as anoFim,
                date_part('month', rh56_datafim) as mesFim,
                lag(rh56_datafim) over (order by rh56_datafim) as dataFimAnterior
                ",
                $where,
                $order
            );
            $result = $daoRhLocalTrab->sql_record($sql);
            //Caso exista mais de 1 lotacao tributaria configurada, retornamos erro
            if ($daoRhLocalTrab->numrows > 1) {
                throw new DBException("Mais de uma lotação tributária para a matricula: {$matricula}.");
            }
            /**
             * Caso não exista Nenhuma lotação tributaria vinculada ao local de trabalho ainda
             *  Verificamos a quantidade de lotacoes configuradas para a instituicao
             *  Caso seja apenas 1, o servidor será vinculado a essa lotacao tributaria
             */
            if ($daoRhLocalTrab->numrows == 0) {
                $instituicao = \InstituicaoRepository::getInstituicaoByCodigo($instituicaoServidor);
                $codigoCgm = $instituicao->getCgm()->getCodigo();
                $sql = <<<SQL
                    select
                        rh268_codigolotacao as lotacaoTributaria,
                        null as datainicio,
                        null as datafim,
                        $ano as anoinicio,
                        $mes as mesinicio,
                        null as anoFim,
                        null as mesFim,
                        null as dataFimAnterior
                    from
                        recursoshumanos.rhlotacaotributaria
                    where
                        rh268_numcgm = $codigoCgm
                    ;
SQL;
                $result = \db_query($sql);
                //Caso exista mais de 1 lotacao tributaria configurada, retornamos erro
                if (pg_num_rows($result) != 1) {
                    throw new DBException("Erro ao buscar a lotação tributária da Matricula: {$matricula}.");
                }
            }
        }

        $lotacao = $this->montarLotacoes($result, $ano, $mes);

        if (sizeof($lotacao) == 0) {
            $instituicao = \InstituicaoRepository::getInstituicaoByCodigo($instituicaoServidor);
            $codigoCgm = $instituicao->getCgm()->getCodigo();
            $sql = <<<SQL
                select
                    rh268_codigolotacao as lotacaoTributaria,
                    null as datainicio,
                    null as datafim,
                    $ano as anoinicio,
                    $mes as mesinicio,
                    null as anoFim,
                    null as mesFim,
                    null as dataFimAnterior
                from
                    recursoshumanos.rhlotacaotributaria
                where
                    rh268_numcgm = $codigoCgm
                ;
SQL;
            $result = \db_query($sql);
            //Caso exista mais de 1 lotacao tributaria configurada, retornamos erro
            if (pg_num_rows($result) != 1) {
                throw new DBException("Erro ao buscar a lotação tributária da Matricula: {$matricula}.");
            }
            $lotacao = $this->montarLotacoes($result, $ano, $mes);
        }

        return $lotacao;
    }

    private function montarLotacoes($result, $ano, $mes) {
        $lotacao = [];
        for ($row = 0; $row < pg_num_rows($result); $row++) {
            $lotacao[] = \db_utils::fieldsMemory($result, $row);
        }

        foreach ($lotacao as $key => $value) {

            /**
             * Caso não possua Data Fim.
             */
            if (empty($value->datafim)) {
                if (($value->anoinicio == $ano) && $value->mesinicio == $mes) {
                    continue;
                }

            } else {

                /**
                 * Ano Menor que ano de Competência.
                 */
                if ($value->anofim < $ano) {
                    unset($lotacao[$key]);
                }

                /**
                 * Ano Igual e mês menor.
                 */
                if ($value->anofim == $ano && $value->mesfim < $mes) {
                    unset($lotacao[$key]);
                }

                /**
                 * Ano e mês Inferior a competência Atual.
                 */
                if (!empty($value->datafim) &&
                    ($value->anofim < $ano) &&
                    ($value->mesfim < $mes)) {
                    unset($lotacao[$key]);
                }
            }

            if ($value->anoinicio > $ano) {
                unset($lotacao[$key]);
            }

            if ($value->anoinicio == $ano && $value->mesinicio > $mes) {
                unset($lotacao[$key]);
            }
        }
        return $lotacao;
    }

    /**
     * @param null|int $mes
     * @param null|int $ano
     * @return LocalTrabalho
     * @throws DBException
     */
    public function getLocalTrabalhoPrincial($mes = null, $ano = null)
    {
        if (empty($this->locaisTrabalho)) {
            $locaisTrabalhoRepository = \LocalTrabalhoRepository::getInstance();
            $this->locaisTrabalho = $locaisTrabalhoRepository->getLocalTrabalhoPorServidor($this, $ano, $mes);
        }

        if (array_key_exists('principal', $this->locaisTrabalho)) {
            return $this->locaisTrabalho['principal'];
        }
        return null;
    }

    /**
     * Retorna a data das rescição do Servidor
     * @return DateTime
     * @throws BusinessException
     */
    public function getDataRescisao()
    {

        $dadosRescisao = $this->getRescisao();
        if (!empty($dadosRescisao) && $dadosRescisao->rh05_recis != '') {
            $this->dataRescisao = new \DateTime($dadosRescisao->rh05_recis);
            return $this->dataRescisao;
        }

        return null;
    }

    /**
     *
     * @throws BusinessException
     *
     */
    private function getRescisao()
    {

        if (!$this->carregouDadosRescisao) {
            if (empty($this->iMatricula)) {
                throw new BusinessException('Matrícula do servidor não informada para consulta da situacao do servidor.');
            }
            $oDaoRhPessoal = new cl_rhpessoal;
            $rsSituacao = $oDaoRhPessoal->sql_verificaSituacaoServidor(
                $this->iMatricula,
                DBPessoal::getAnoFolha(),
                DBPessoal::getMesFolha()
            );
            $rsSituacao = db_query($rsSituacao);
            if (!$rsSituacao == "Erro ao selecionar os registros.") {
                throw new BusinessException("1 - Não foi possivel verificar se o servidor esta rescindido.\n" . $oDaoRhPessoal->erro_msg);
            }
            $this->carregouDadosRescisao = true;

            $this->dadosRescisao = new stdClass();
            if (pg_num_rows($rsSituacao) > 0) {
                $this->dadosRescisao = db_utils::fieldsMemory($rsSituacao, 0);
            }
        }
        return $this->dadosRescisao;
    }

    /**
     *
     * @throws BusinessException
     *
     */
    private function getRescisaoNaCompetencia()
    {
        if (!$this->carregouDadosRescisao) {
            if (empty($this->iMatricula)) {
                throw new BusinessException('Matrícula do servidor não informada para consulta da situacao do servidor.');
            }
            $oDaoRhPessoal = db_utils::getDao('rhpessoal');
        }
        if (empty($this->iMatricula)) {
            throw new BusinessException('Matrícula do servidor não informada para consulta da situacao do servidor.');
        }
        $oDaoRhPessoal = new cl_rhpessoal;

        $rsSituacao = db_query($oDaoRhPessoal->sql_query_rescisao(
            null,
            "rh30_vinculo, rh05_recis, rh05_datapagamento",
            null,
            "rhpessoal.rh01_regist = {$this->iMatricula} and
            to_char(rh05_recis, 'YYYYMM') <= concat({$this->iAnoCompetencia}, lpad({$this->iMesCompetencia}, 2, 0)) "
        ));
        if (!$rsSituacao) {
            throw new BusinessException("2 - Não foi possivel verificar se o servidor esta rescindido.\n" . $oDaoRhPessoal->erro_msg);
        }
        $this->carregouDadosRescisao = false;

        $this->dadosRescisao = new stdClass();
        if (pg_num_rows($rsSituacao) > 0) {
            $this->dadosRescisao = db_utils::fieldsMemory($rsSituacao, 0);
        }

        return $this->dadosRescisao;
    }

    /**
     * @return bool
     * @throws DBException
     */
    public function temVinculoEmpregaticio()
    {
        $daoServidor = new cl_rhpessoal();
        $where = "rh01_regist = {$this->getMatricula()} and rh30_vinculoemprego is true";
        $sqlServidor = $daoServidor->sql_query_func_rhpessoal(null, "*", null, $where);
        $rsServidor = db_query($sqlServidor);

        if (!$rsServidor) {
            throw new DBException("Erro ao validar se o Trabalhador tem vínculo empregatício.");
        }

        if (pg_num_rows($rsServidor) == 0) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'matricula' => $this->getMatricula(),
            'cgm' => $this->getCgm() instanceof CgmBase ? $this->getCgm()->toArray() : null
        );
    }

    /**
     * Retorna a data de rescisão do servidor caso haja.
     * @return DateTime|null
     */
    public function getDataPagamentoRescisao()
    {
        $dadosRescisao = $this->getRescisao();
        if (!empty($dadosRescisao) && $dadosRescisao->rh05_datapagamento != '') {
            $this->dataPagamentoRescisao = new \DateTime($dadosRescisao->rh05_datapagamento);
            return $this->dataPagamentoRescisao;
        }

        return null;
    }

    /**
     *
     * @return CgmBase
     * @throws Exception
     */
    public function getEmpregador()
    {
        return LotacaoRepository::getInstanceByCodigo($this->getCodigoLotacao())->getCgm();
    }

    /**
     * @param Sindicato $sindicato
     */
    public function setSindicato(Sindicato $sindicato)
    {
        $this->sindicato = $sindicato;
    }

    /**
     * @return Sindicato
     */
    public function getSindicato()
    {
        return $this->sindicato;
    }

    /**
     * @return int
     */
    public function getRacaCor()
    {
        return $this->racaCor;
    }

    /**
     * @param int $racaCor
     */
    public function setRacaCor($racaCor)
    {
        $this->racaCor = $racaCor;
    }

    /**
     * @return int
     */
    public function getGrauInstrucao()
    {
        return $this->grauInstrucao;
    }

    /**
     * @param int $grauInstrucao
     */
    public function setGrauInstrucao($grauInstrucao)
    {
        $this->grauInstrucao = $grauInstrucao;
    }

    /**
     * @return float
     */
    public function getHorasMensais()
    {
        return $this->horasMensais;
    }

    /**
     * @param float $horasMensais
     */
    public function setHorasMensais($horasMensais)
    {
        $this->horasMensais = $horasMensais;
    }

    /**
     * @param float $porcentagem
     * @return float|int
     */
    public function getPorcentagemHorasMensais($porcentagem = 0.25)
    {
        return $this->horasMensais * $porcentagem;
    }

    /**
     * @param Rubrica[] $rubricasPonto
     */
    public function setRubricasPonto(array $rubricasPonto)
    {
        $this->rubricasPonto = $rubricasPonto;
    }

    public function getRubricasPonto()
    {
        return $this->rubricasPonto;
    }

    /**
     * @return Servidor
     * @throws BusinessException
     */
    public function withRubricasPonto()
    {
        if (empty($this->rubricasPonto)) {
            $this->rubricasPonto = RubricaRepository::buscaRubricasPontoServidor($this);
        }

        return $this;
    }
    /**
     * Diz se o servidor pode entrar no arquivo S1200 do eSocial
     * @return bool
     */
    public function is1200()
    {
        //Caso nao seja CLT, verificamos se e estagiario
        if (!$this->isClt()) {
            if ($this->isEstagiario()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     * @throws DBException
     * Verifica se a opcao de natureza do regime é estagio
     */
    public function isEstagiario()
    {
        // Validamos se o servidor e estagiario, pela natureza do codigo de regime do servidor na competencia
        $sql = "select
                    rhnaturezaregime.*
                from
                    rhnaturezaregime
                    inner join rhregime on rh71_sequencial = rh30_naturezaregime
                where
                    rh30_codreg = {$this->getCodigoRegime()}
                    and rh30_instit = {$this->getCodigoInstituicao()}
                    and rh30_naturezaregime in (6)";
        $rs = db_query($sql);
        if (!$rs) {
            $msg = "Erro ao buscar informações do regime da matrícula: {$this->getMatricula()}";
            throw new \DBException($msg);
        }
        // Caso tenha registro é estagiario
        if (pg_num_rows($rs) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Retorna o código do cgm do servidor
     * @return integer
     */
    public function getCodigoCgm()
    {
        return $this->iNumCgm;
    }

    public function getDescricaoEstadoCivil()
    {
        return $this->sDescricaoEstadoCivil;
    }

    public function getCodigoNacionalidade()
    {
        return $this->codigoNacionalidade;
    }

    public function getDescricaoNacionalidade()
    {
        return $this->sDescricaoNacionalidade;
    }

    public function isImigrante()
    {
        if (!empty($this->imigrante)) {
            if (!empty($this->imigrante->getCodigo())) {
                return true;
            } else {
                return false;
            }
        }
        $imigrante = new Imigrante($this->getMatricula(), $this->getCodigoInstituicao());
        $this->imigrante = $imigrante;
        if (!empty($imigrante->getCodigo())) {
            return true;
        }
        return false;
    }

    public function getDadosImigrante()
    {
        return $this->imigrante;
    }

    public function save()
    {
        /**
         * Verifica se existe alguma transação ativa
         */
        if (!db_utils::inTransaction()) {
            throw new Exception("nenhuma transação encontrada no cadastro de servidor!");
        }

        $rhpessoal = new  \cl_rhpessoal();
        $rhpessoal->rh01_raca = $this->getRacaCor();
        $rhpessoal->rh01_sexo = $this->getSexo();
        $rhpessoal->rh01_instru = $this->getGrauInstrucao();
        $rhpessoal->rh01_estciv = $this->getEstadoCivil();
        $rhpessoal->rh01_natura = $this->getNaturalidade();

        if (empty($this->getMatricula())) {
            $rhpessoal->incluir(null);
            if ($rhpessoal->erro_status != "1") {
                throw new \Exception($rhpessoal->erro_msg);
            }
        } else {
            $rhpessoal->rh01_regist = $this->getMatricula();
            $rhpessoal->alterar($this->getMatricula());
            if ($rhpessoal->erro_status != "1") {
                throw new \Exception($rhpessoal->erro_msg);
            }
        }
        return true;
    }

    /**
     * @return false|ServidorDocumento
     */
    public function documento()
    {
        return ServidorDocumento::findByMatricula($this->getMatricula());
    }

    /**
     * @return false|ServidorDeficiente
     */
    public function deficiente()
    {
        return ServidorDeficiente::findByMatricula($this->getMatricula());
    }

    /**
     * @param $cpf
     * @param $instituicao
     * @return false|Servidor
     * @throws ParameterException
     */
    public static function findCpfInstituicoes($cpf, $instituicao)
    {
        $rhpessoal = new  cl_rhpessoal();
        $sql = $rhpessoal->sql_query_cgm_instituicoes(
            null,
            "*",
            null,
            "z01_cgccpf ={$cpf}",
            $instituicao
        );

        $rs = $rhpessoal->sql_record($sql);
        $servidor = pg_fetch_object($rs);
        if (empty($servidor)) {
            return false;
        }

        return self::fromDao($servidor);
    }


    public static function findCpf($cpf)
    {
        $rhpessoal = new  cl_rhpessoal();
        $sql = $rhpessoal->sql_query_cgm_todas_instituicoes(
            null,
            "*",
            "rh01_admiss DESC",
            "z01_cgccpf ={$cpf}"
        );

        $rs = $rhpessoal->sql_record($sql);
        $servidor = pg_fetch_object($rs);
        if (empty($servidor)) {
            return false;
        }

        return self::fromDao($servidor);
    }

    /**
     * @param stdClass $oRhPessoal
     * @return Servidor
     * @throws ParameterException
     */
    public static function fromDao(stdClass $oRhPessoal)
    {

         $servidor = new self();
         $servidor->iNumCgm = $oRhPessoal->rh01_numcgm;
         $servidor->iCodigoMovimentacao = $oRhPessoal->rh02_seqpes;
         $servidor->iCodigoLotacao = $oRhPessoal->rh02_lota;
         $servidor->iSalario = $oRhPessoal->rh02_salari;

         $servidor->setMatricula($oRhPessoal->rh01_regist);
         $servidor->setCodigoCargo($oRhPessoal->rh02_funcao);
         $servidor->setTipoAdmissao($oRhPessoal->rh01_tipadm);
         $servidor->setCodigoInstituicao($oRhPessoal->rh01_instit);
         $servidor->setNumeroPonto($oRhPessoal->rh01_ponto);
         $servidor->setObservacaoServidor($oRhPessoal->rh01_observacao);
         $servidor->setAbonoPermanencia($oRhPessoal->rh02_abonopermanencia);
         $servidor->setTabelaPrevidencia($oRhPessoal->rh02_tbprev);
         $servidor->setMolestiaGrave($oRhPessoal->rh02_portadormolestia == 't' ? true : false);
         $servidor->setEstadoCivil($oRhPessoal->rh01_estciv);
         $servidor->setNaturalidade($oRhPessoal->rh01_natura);

        if ($oRhPessoal->rh02_ocorre) {
            $servidor->setTipoExposicaoAgentesNocivos($oRhPessoal->rh02_ocorre);
        }

        if ($oRhPessoal->rh02_codreg) {
            $servidor->setCodigoRegime($oRhPessoal->rh02_codreg);
        }

        if (!empty($oRhPessoal->rh01_admiss)) {
            $servidor->setDataAdmissao(new DBDate($oRhPessoal->rh01_admiss));
        }

        if (!empty($oRhPessoal->rh01_trienio)) {
            $servidor->setDataTrienio(new DBDate($oRhPessoal->rh01_trienio));
        }

        if (!empty($oRhPessoal->rh01_progres)) {
            $servidor->setDataProgressao(new DBDate($oRhPessoal->rh01_progres));
        }

        if (!empty($oRhPessoal->rh01_nasc)) {
            $servidor->setDataNascimento(new DBDate($oRhPessoal->rh01_nasc));
        }

        $servidor->setSexo($oRhPessoal->rh01_sexo);

        if (!empty($oRhPessoal->rh02_diasgozoferias)) {
            $servidor->setDiasGozoFerias($oRhPessoal->rh02_diasgozoferias);
        }

        if (!empty($oRhPessoal->rh01_rhsindicato)) {
            $servidor->setSindicato(SindicatoRepository::find($oRhPessoal->rh01_rhsindicato));
        }

        if (!empty($oRhPessoal->rh02_hrsmen)) {
            $servidor->horasMensais = $oRhPessoal->rh02_hrsmen;
        }

        return $servidor;
    }


    /**
     * @return bool
     * @throws DBException
     * Verifica se o servidor possui regime CLT
     */
    public function isClt()
    {
        // Validamos se o Regime é CLT
        $sql = "select
                    *
                from
                    rhregime
                where
                    rh30_codreg = {$this->getCodigoRegime()}
                    and rh30_instit = {$this->getCodigoInstituicao()}
                    and rh30_regime in (2)";
        $rs = db_query($sql);
        if (!$rs) {
            $msg = "Erro ao buscar informações do regime da matrícula: {$this->getMatricula()}";
            throw new \DBException($msg);
        }
        // Caso tenha registro é CLT
        if (pg_num_rows($rs) > 0) {
            return true;
        }

        return false;
    }

     /**
     * @return bool
     * @throws DBException
     *  1-Estatutário, 2-CLT e 3-Extra Quadro
     */
    private function isRegimeContratacao($tipo = null)
    {
        $sql = "select
                    *
                from
                    rhregime
                where
                    rh30_codreg = {$this->getCodigoRegime()}
                    and rh30_instit = {$this->getCodigoInstituicao()}
                    and rh30_regime = {$tipo}";
        $rs = db_query($sql);
        if (!$rs) {
            $msg = "Erro ao buscar informações do tipo de regime de contratação da matrícula: {$this->getMatricula()}";
            throw new \DBException($msg);
        }
        if (pg_num_rows($rs) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     * Verifica se o servidor possui regime Estatutário
     */
    public function isEstatutario()
    {
        if ($this->isRegimeContratacao(1)) {
            return true;
        }

        return false;
    }

        /**
     * @return bool
     * Verifica se o servidor possui regime Celetista
     */
    public function isCeletista()
    {
        if ($this->isRegimeContratacao(2)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     * Verifica se o servidor possui regime Extra Quadro
     */
    public function isExtraQuadro()
    {
        if ($this->isRegimeContratacao(3)) {
            return true;
        }

        return false;
    }

    public function getDadosRescisao()
    {
        return $this->getRescisao();
    }
   /**
     * @return O salário do servidor
     */
    public function getISalario()
    {
        return $this->iSalario;
    }

    public function setFuncao($cargo)
    {
        $this->cargo = $cargo;
    }

    public function getFuncao()
    {
        return $this->cargo;
    }

    /**
     * @return bool | Informações do cargo
     * Busca as informações do cargo do Servidor
    */
    private function getInfoFuncao()
    {
        if ($this->getSeqPes()) {
            $cl_rhpescargo =  new cl_rhpescargo;
            $result_cargo =  $cl_rhpescargo->sql_record($cl_rhpescargo->sql_query_descr($this->getSeqPes(), "rh20_cargo"));

            if (!$result_cargo) {
                return false;
            }

            if (pg_num_rows($result_cargo) === 0) {
                return false;
            }
            $cargo = db_utils::fieldsMemory($result_cargo, 0);
            return $cargo->rh20_cargo;
        }
        return false;
    }

    private function getSeqPes()
    {
        unset($seq_pes_result);
        $sql ="SELECT rh02_seqpes FROM rhpessoalmov
        WHERE  rh02_anousu = '{$this->iAnoCompetencia}' AND rh02_mesusu = '{$this->iMesCompetencia}'
        AND rh02_instit = '{$this->iCodigoInstituicao}' AND rh02_regist = '{$this->iMatricula}'";
        $cl_rhpescargo = new cl_rhpescargo;
        $seq_pes = $cl_rhpescargo->sql_record($sql);

        if (!$seq_pes) {
            return false;
        }

        if (pg_num_rows($seq_pes) === 0) {
            return false;
        }

        $seq_pes_result = db_utils::fieldsMemory($seq_pes, 0);
        return $seq_pes_result->rh02_seqpes;
    }

    public function getDataFim()
    {
        $clrhcontratoemergencialrenovacao    = new cl_rhcontratoemergencialrenovacao;
        $sWhereContratoEmergencialRenovacoes = " rh163_matricula = {$this->iMatricula}";
        $sSqlContratosEmergenciais           = $clrhcontratoemergencialrenovacao->sql_query(null, "rh164_datafim", null, $sWhereContratoEmergencialRenovacoes);
        $rsContratosEmergenciais             = $clrhcontratoemergencialrenovacao->sql_record($sSqlContratosEmergenciais);

        if (!$rsContratosEmergenciais) {
            return false;
        }

        if (pg_num_rows($rsContratosEmergenciais) === 0) {
            return false;
        }

        $contrato_emergencial = db_utils::fieldsMemory($rsContratosEmergenciais, 0);
        return $contrato_emergencial->rh164_datafim;
    }

    public function getRegistroJustificativa()
    {
        $sql="SELECT h07_justif  FROM admissao WHERE h07_regist ='{$this->iMatricula}'";
        $justificativa = db_query($sql);

        if (!$justificativa) {
            return false;
        }

        if (pg_num_rows($justificativa) === 0) {
            return false;
        }

        $justif = db_utils::fieldsMemory($justificativa, 0);
        return $justif->h07_justif;
    }

    public function getCompetenciasPagamentosRescisao()
    {
        $sql = "
            select r20_anousu as anousu, r20_mesusu as mesusu from pessoal.gerfres where r20_regist = {$this->getMatricula()} group by anousu, mesusu order by anousu asc, mesusu asc
        ";
        $rs = db_query($sql);
        if (!$rs) {
            throw new DBException("Erro ao buscar informações de pagamentos de rescisões.");
        }
        return \db_utils::makeCollectionFromRecord($rs, function ($retorno) {
            return $retorno;
        });
    }

    /**
     * @return bool
     * Verifica se o servidor possui pensao por morte de outro servidor
     */
    public function possuiPensaoPorMorte()
    {
        if ($this->isPensionista()) {
            $sql = "select rh21_regpri from rhpesorigem where rh21_regist = {$this->getMatricula()}";
            $rs = db_query($sql);
            if (!$rs) {
                $msg = "Houve um erro ao buscar informacoes de pensão por morte do servidor {$this->getMatricula()}";
                throw new DBException($msg);
            }
            if (pg_num_rows($rs) > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * @retrun bool
     * Verifica se o servidor possui pensao por morte de outro servidor
     */
    public function getMatriculaPensaoPorMorte()
    {
        if ($this->isPensionista()) {
            $sql = "select rh21_regpri from rhpesorigem where rh21_regist = {$this->getMatricula()}";
            $rs = db_query($sql);
            if (!$rs) {
                $msg = "Houve um erro ao buscar informacoes de pensão por morte do servidor {$this->getMatricula()}";
                throw new DBException($msg);
            }
            if (pg_num_rows($rs) > 0) {
                return db_utils::fieldsMemory($rs, 0)->rh21_regpri;
            }
        }
        return false;
    }

    /**
     * @return bool || int
     * retorna o servidor de origem
     */
    public function getOrigem()
    {
        $sql = "select rh21_regpri from rhpesorigem where rh21_regist = {$this->getMatricula()}";
        $rs = db_query($sql);
        if (!$rs) {
            $msg = "Houve um erro ao buscar informacoes de origem do servidor {$this->getMatricula()}";
            throw new DBException($msg);
        }
        if (pg_num_rows($rs) > 0) {
            return db_utils::fieldsMemory($rs, 0)->rh21_regpri;
        }
        return false;
    }




    /**
     * @return bool
     *
     * Metodo implementado com a finalidade de validar se o periodo aquisitivo do evento S-2230 deverá ser enviado
     * Sera retornado false quando o servidor nao estiver nas categorias indicadas e true caso contrario
     */
    public function validaCategoriaPeriodoAquisitivo()
    {
        $categorias = [301, 302, 303, 304, 306, 307, 309, 310, 312, 410];
        return in_array($this->getVinculo()->getCodigoCategoria(), $categorias);
    }



    /**
     * @return bool
     *
     * Metodo implementado com a finalidade de validar se a folha de pagaemnto do servidor rescindido deverá ser enviado
     * nos eventos S1200 e S1202 ao inves do S2399
     * Sera retornado false quando o servidor estiver nas categorias indicadas e true caso contrario
     */
    public function validaCategoriaRescisaoSemVinculo()
    {
        $categorias = [721];
        return !in_array($this->getVinculo()->getCodigoCategoria(), $categorias);
    }

     /**
     * @return string
     * Retorna a lotação tributária que deverá ser enviada ao evento S1200.
     */
    public function codigoLotacaoTributariaEsocial()
    {
        if (!empty($this->getLocalTrabalhoPrincial($this->iMesCompetencia, $this->iAnoCompetencia))) {
            return $this->getLocalTrabalhoPrincial()
                ->getLotacaoTributaria();
        } else {
            $daoLotacaoTributaria = new \cl_rhlotacaotributaria();
            $whereLotacaoTributaria = " db_config.codigo = {$this->iCodigoInstituicao}";
            $sqlLotacaoTributaria = $daoLotacaoTributaria->sql_query(null, "rh268_codigolotacao", null, $whereLotacaoTributaria);
            $rsLotacaoTributaria = $daoLotacaoTributaria->sql_record($sqlLotacaoTributaria);

            if (!$rsLotacaoTributaria) {
                return null;
            }

            if (pg_num_rows($rsLotacaoTributaria) === 0) {
                return null;
            }

            $lotacaoTributaria = db_utils::fieldsMemory($rsLotacaoTributaria, 0);
            return $lotacaoTributaria->rh268_codigolotacao;
        }
    }

    public function getTipoPagamentoEsocialAPI($ano = null, $mes = null)
    {
        if (!$this->isAtivo() || $this->isPensionista()) {
            return Tipo::S1207_API;
        }
        if ($this->isRgps($ano, $mes)) {
           return Tipo::S1200_API;
        }
        if ($this->isRpps($ano, $mes)) {
            return Tipo::S1202_API;
        }
        //Caso nao tenha tabela de previdencia, verificamos se o servidor é cedido
        if (!$this->getTipoPrevidencia()) {
            //Caso seja cedido, continuamos com o processamento como RPPS (S-1202)
            $cedencia = new \Cedencia($this->getMatricula());
            if (!empty($cedencia->getMatricula())) {
                return Tipo::S1202_API;
            }
        }

        return Tipo::S1200_API;
    }

    /**
     * Método responsavel por definir se as rubricas de rescisao seram enviadas no evento S-1200/S-1202 ou S-2299
     * retorno true = envia nos eventos S-1200/S-1202
     * retorno false = envia nos eventos S-2299
     */
    public function validacaoEnvioRescisaoEsocialComVinculo()
    {
        /**
         * COLOCAR UMA RESSALVA PARA PROCESSAR SOMENTE O VINCULO CADASTRADO COM O
         * REGIME = 3-CCE NO CAMPO: "CÓDIGO DE CATEGORIA"(RH30_CODIGOCATEGORIA)
         * ESTIVER O CÓDIGO: 901.
         */
        if ($this->getVinculo()->getCodigoCategoria() == 901
            && $this->getVinculo()->getRegime()->getCodigo() == 3) {
            return false;
        }

        /**
         * Campo: REGIME (RH30_REGIME), GERAR SOMENTE PARA QUEM POSSUI O CÓDIGO 2-CLT,
         * VINCULADO AO CADASTRO DO VINCULO.
         */
        if ($this->getVinculo()->getRegime()->getCodigo() == 2) {
            return false;
        }

        return true;
    }

    /**
     * Caso retorne true, significa que a data de rescisao é superior a obrigatoriedade
     * Caso contrario, o servidor foi desligado antes do inicio da obrigatoriedade
     */
    private function validaDataRescisaoObrigatoriedade()
    {
        $retorno = true;
        if (!empty($this->getDadosRescisao())) {
            if (isset($this->getDadosRescisao()->rh05_recis) && !empty($this->getDadosRescisao()->rh05_recis)) {
                $dataRescicao = new DBDate($this->getDadosRescisao()->rh05_recis);
                $dataObrigatoriedade = \DBPessoal::getDataFaseEsocial(3);
                if ($dataRescicao < $dataObrigatoriedade) {
                    return false;
                }
            }
        }
        return $retorno;
    }

    /**
     * Método responsavel por definir se as rubricas de rescisao seram enviadas no evento S-1200/S-1202 ou S-2399
     * retorno true = envia nos eventos S-1200/S-1202
     * retorno false = envia nos eventos S-2399
     */
    public function validacaoEnvioRescisaoEsocialSemVinculo()
    {
        /**
         * COLOCAR UMA RESSALVA PARA PROCESSAR SOMENTE O VINCULO CADASTRADO COM O
         * REGIME = 3-CCE NO CAMPO: "CÓDIGO DE CATEGORIA"(RH30_CODIGOCATEGORIA)
         * ESTIVER O CÓDIGO: 721.
         */
        if ($this->getVinculo()->getCodigoCategoria() == 721
            && $this->getVinculo()->getRegime()->getCodigo() == 3) {
            return false;
        }

        /**
         * Campo: REGIME (RH30_REGIME), GERAR SOMENTE PARA QUEM POSSUI O CÓDIGO 2-CLT,
         * VINCULADO AO CADASTRO DO VINCULO.
         */
        if ($this->getVinculo()->getRegime()->getCodigo() == 2) {
            return false;
        }

        return true;
    }


    public function getDadosReintegracao()
    {

        $iMatricula = $this->getMatricula();
        $iInstit    = $this->getInstituicao()->getSequencial();
        $sCampos    = "h25_tiporeintegracao, h25_numeroprocesso, h25_leianistia, h25_dataefetivoretorno, h25_datareintegracao";
        $sWhere     = "h25_regist = {$iMatricula} and h25_instit = {$iInstit}";

        $daoAdmissaoDado = new cl_rhadmissaodado;
        $rsAdmissaoDado  = $daoAdmissaoDado->sql_Record($daoAdmissaoDado->sql_Query(null, $sCampos, null, $sWhere));
        $oAdmissaoDado   = db_utils::fieldsMemory($rsAdmissaoDado, 0);

        return $oAdmissaoDado;

    }

    /**
     *  Valida se é obrigatorio preencher o campo PensAlim
     * no evento S2399
     */
    public function validarEnvioPensaoAlimenticia () {
        $categoriasEnvioPensao = [201,202,721];
        $dtTerm = $this->getDadosRescisao()->rh05_recis;
        $dtTermObrigatorio = '2019-04-21';
        if (in_array($this->getVinculo()->getCodigoCategoria(),$categoriasEnvioPensao)
            && $dtTerm > $dtTermObrigatorio) {
            return true;
        }
        return false;
    }

    /**
     * @param $matriculaAnterior
     * @return mixed
     * @throws BusinessException
     * @throws DBException
     * metodo para buscar rh01_regist atraves da matricula anterior
     */
    private function buscarMatriculaEcidade($matriculaAnterior) {
        $oDaoPessoal = new cl_rhpessoal;

        $where = "rh01_matriculaanterior = '{$matriculaAnterior}' ";
        $sSqlPessoal = $oDaoPessoal->sql_query_file(
            null,
            'rh01_regist',
            null,
            $where
        );

        $rsPessoal = $oDaoPessoal->sql_record($sSqlPessoal);

        if (!$rsPessoal && $oDaoPessoal->erro_sql == "Erro ao selecionar os registros.") {
            throw new DBException("Erro ao Buscar Servidor.\n" . $oDaoPessoal->erro_msg);
        }

        if ($oDaoPessoal->numrows > 0) {
            $rh01_regist = db_utils::fieldsMemory($rsPessoal,0)->rh01_regist;
            $this->setMatriculaAnterior($matriculaAnterior);
            return $rh01_regist;
        }

        return $matriculaAnterior;

    }

    /**
     * @return string
     * @throws DBException
     * Retorna o tipo de IR de acordo com o codigo da receita para o S-1210
     * caso o servidor for inativo ou pensionista, retornara 353301
     * caso contrario, se tiver vinculo de emprego, retornara 051607
     * se nao tiver retornara 056108
     *
     */
    public function getTipoIRCR($mesAtual = "", $anoAtual = "")
    {
        $daoPessoalMov = new cl_rhpessoalmov();

        if (empty($mesAtual)) {
            $mesAtual = $this->getMesCompetencia();
        }

        if (empty($anoAtual)) {
            $anoAtual = $this->getAnoCompetencia();
        }

        if (!$daoPessoalMov->temDescontoIRRF(
            $this->getMatricula(),
            $mesAtual,
            $anoAtual)
        ) {
            return null;
        }

        $regime = $this->getRegime();
        if ($regime->rh30_vinculo == "A") {
            if ($regime->rh30_vinculoemprego = "t") {
                return "056107";
            } else {
                return "058806";
            }
        } else {
               return "353301";
        }

    }

    private function getRegime() {

        $daoRhRegime = new cl_rhregime();
        $sqlRegime = $daoRhRegime->sql_query($this->getCodigoRegime());
        $rsRegime = $daoRhRegime->sql_record($sqlRegime);

        if (!$rsRegime && $daoRhRegime->erro_sql == "Erro ao selecionar os registros.") {
            throw new DBException("Erro ao buscar regime do servidor.\n" . $daoRhRegime->erro_msg);
        }

        $regime = db_utils::fieldsMemory($rsRegime,0);
        return $regime;
    }

    public function getTipoRegimeOrigem () {
      return (new \Cedencia())->getRegimeOrigem($this->getMatricula());
    }

    /**
     * Retorna a primeira competência de remuneração da rescisão da matrícula definida.
     * @param  integer   $matricula             //matrícula do servidor
     * @return stdClass
    **/
    public function getCompetenciaPagamentoRecisao($matricula)
    {

        if (empty($matricula)) {
            throw new BusinessException("Matrícula não informada. Favor revisar.");
        }

        $sql = "select
                    r20_anousu as anoCompetencia,
	                r20_mesusu as mesCompetencia,
                    coalesce(extract(year from rh05_datapagamento),extract(year from rh225_datapagamento)) as anoCompetenciaCaixa,
	                coalesce(extract(month from rh05_datapagamento),extract(MONTH from rh225_datapagamento)) as mesCompetenciaCaixa,
	                rh05_recis as dataRescisao,
                    rh05_datapagamento as datapagamento,
	                rh02_regist as matricula,
	                rh02_seqpes as sequencial
                from pessoal.rhpessoalmov
                join pessoal.gerfres on r20_regist = rh02_regist
                join pessoal.rhpesrescisao on
	            rh02_seqpes = rh05_seqpes
	            and rh02_regist = {$matricula}
                left join pessoal.rhdatapagamentofolha on rh225_instituicao=rh02_instit and
                r20_anousu = extract(year from rh225_datapagamento) and 
                r20_mesusu = extract(MONTH from rh225_datapagamento)
                order by rh05_datapagamento, rh225_datapagamento limit 1";
        $rs = db_query($sql);
        if (!$rs) {
            $msg = "Data de rescisão do servidor de número matrícula {$matricula} é inválida. Favor revisar.";
            throw new BusinessException($msg);
        }
        $retorno = db_utils::fieldsMemory($rs, 0);
        return $retorno;
    }
}
