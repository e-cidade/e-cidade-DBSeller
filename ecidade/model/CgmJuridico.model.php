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

/**
 * model para cgm
 *@package Protocolo
 */
class CgmJuridico  extends CgmBase {

	/**
	 * CNPJ do CGM
	 *
	 * @var string
	 */
	protected $iCnpj;

  /**
   * Nome Fantasia do CGM Jurídico
   *
   * @var string
   */
  protected $sNomeFantasia;

  /**
   * Tipo de Credor do CGM Jurídico
   *
   * @var integer
   */
  protected $iTipoCredor;

  /**
   * Contato do CGM Jurídico
   *
   * @var string
   */
  protected $sContato;

  /**
   * Código Nire
   *
   * @var string
   */

  protected  $sNire;

  /**
     * Nacionalidade do CGM
     *
     * @var integer
     */
    protected $iNacionalidade;

     /**
     * @type string
     */
    protected $sDocumentoEstrangeiro;

    /**
     * @var string
    */
    protected $sPaisEstrangeiro;

    /**
     * @var string
     */
    protected $sCidadeEstrangeiro;

  function __construct( $iCgm = null ) {

    if ( !empty($iCgm) ) {

      parent::__construct($iCgm);

      $oDaoCgm = db_utils::getDao("cgm");
      $sSqlCgm = $oDaoCgm->sql_query_file($iCgm);
      $rsCgm   = $oDaoCgm->sql_record($sSqlCgm);

      if ($oDaoCgm->numrows > 0) {

        $oDadosCgm = db_utils::fieldsMemory($rsCgm,0);

        $this->setCnpj        ($oDadosCgm->z01_cgccpf);
        $this->setContato     ($oDadosCgm->z01_contato);
        $this->setNomeFantasia($oDadosCgm->z01_nomefanta);
        $this->setTipoCredor  ($oDadosCgm->z01_tipcre);
        $this->setObs         ($oDadosCgm->z01_obs);
        $this->setNacionalidade ($oDadosCgm->z01_nacion);

      }
    }



  }

  /**
   * @param string
   */
  public function setNire($sNire) {
  	$this->sNire = $sNire;
  }

  public function getNire() {

  		$sNire = "";
  	 	$iNumCgm = $this->getCodigo();
	  	if ($iNumCgm != "") {

	  		$oDaoNire = db_utils::getDao("cgmjuridico");
	  		$sCampos = " z08_nire ";
	  		$sWhere  = " z08_numcgm = ".$iNumCgm;

	  		$sQueryNire   = $oDaoNire->sql_query_file(null,$sCampos,null,$sWhere);
	      $rsQueryNire  = $oDaoNire->sql_record($sQueryNire);
	      if ($rsQueryNire !== false) {
	      	$oNire = db_utils::fieldsMemory($rsQueryNire,0);
	      	$sNire = $oNire->z08_nire;
	      }
	  	}

  	return $sNire;
  }

  /**
   * @return string
   */
  public function getCnpj() {
    return $this->iCnpj;
  }

  /**
   * @param string $iCnpj
   */
  public function setCnpj($iCnpj) {
    $this->iCnpj = $iCnpj;
  }


  /**
   * @return integer
   */
  public function getTipoCredor() {
    return $this->iTipoCredor;
  }

  /**
   * @param integer $iTipoCredor
   */
  public function setTipoCredor($iTipoCredor) {
    $this->iTipoCredor = $iTipoCredor;
  }

  /**
   * @return string
   */
  public function getContato() {
    return $this->sContato;
  }

  /**
   * @param string $sContato
   */
  public function setContato($sContato) {
    $this->sContato = $sContato;
  }

  /**
   * @return string
   */
  public function getNomeFantasia() {
    return $this->sNomeFantasia;
  }

  /**
   * @param string $sNomeFantasia
   */
  public function setNomeFantasia($sNomeFantasia) {
    $this->sNomeFantasia = $sNomeFantasia;
  }
   /**
     * @return integer
     */
    public function getNacionalidade()
    {
        return $this->iNacionalidade;
    }

     /**
     * Documento estrangeiro
     * @return string
     * @throws Exception
     */
    public function getDocumentoEstrangeiro()
    {

        $this->carregarInformacoesEstrangeiros();
        return $this->sDocumentoEstrangeiro;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getPaisEstrangeiro()
    {

        $this->carregarInformacoesEstrangeiros();
        return $this->sPaisEstrangeiro;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCidadeEstrangeiro()
    {

        $this->carregarInformacoesEstrangeiros();
        return $this->sCidadeEstrangeiro;
    }


    /**
     * @param integer $iNacionalidade
     */
    public function setNacionalidade($iNacionalidade)
    {
        $this->iNacionalidade = $iNacionalidade;
    }

    /**
     * Documento estrangeiro
     * @param $sDocumento
     */
    public function setDocumentoEstrangeiro($sDocumento)
    {
        $this->sDocumentoEstrangeiro = $sDocumento;
    }

     /**
     * @param $sPais
     */
    public function setPaisEstrangeiro($sPais)
    {
        $this->sPaisEstrangeiro = $sPais;
    }

    /**
     * @param $sCidade
     */
    public function setCidadeEstrangeiro($sCidade)
    {
        $this->sCidadeEstrangeiro = $sCidade;
    }

  /**
   * Salva os dados informados do CGM, caso o CGM já exista então
   * é alterado o registro apartir do código (numcgm) informado
   */
  public function save() {
    $sMsgErro = 'Falha ao salvar CGM Jurídico';

    /**
     * Verifica se existe alguma transação ativa
     */
    if ( !db_utils::inTransaction() ){
      throw new Exception("{$sMsgErro}, nenhuma transação encontrada!");
    }

    /**
     * Chama o método save da classe CGM
     */

    $oDaoCgm         = db_utils::getDao('cgm');
    $oDaoCgmCgc      = db_utils::getDao('db_cgmcgc');
    $oDaoCgmJuridico = db_utils::getDao('cgmjuridico');

    $oDaoCgm->z01_numcgm        = $this->getCodigo();
    $oDaoCgm->z01_cgccpf        = $this->getCnpj();
    $oDaoCgm->z01_ultalt        = date('Y-m-d',db_getsession('DB_datausu'));
    $oDaoCgm->z01_obs           = $this->getObs();
    $oDaoCgm->z01_ender         = addslashes($this->getLogradouro());

    $oDaoCgm->z01_numero     = $this->getNumero();
    $oDaoCgm->z01_compl      = addslashes($this->getComplemento());
    $oDaoCgm->z01_bairro     = addslashes($this->getBairro());
    $oDaoCgm->z01_munic      = addslashes($this->getMunicipio());
    $oDaoCgm->z01_uf         = $this->getUf();
    $oDaoCgm->z01_cep        = $this->getCep();

    $oDaoCgm->z01_nomefanta  = addslashes($this->getNomeFantasia());
    $oDaoCgm->z01_tipcre     = $this->getTipoCredor();
    $oDaoCgm->z01_contato    = addslashes($this->getContato());
    $oDaoCgm->z01_ultalt     = date('Y-m-d',db_getsession('DB_datausu'));
    $oDaoCgm->z01_obs        = $this->getObs();
    $oDaoCgm->z01_incest     = $this->getInscricaoEstadual();
    $oDaoCgm->z01_fax        = $this->getFax();
    $oDaoCgm->z01_telef      = $this->getTelefone();
    $oDaoCgm->z01_telcel     = $this->getCelular();
    $oDaoCgm->z01_email      = addslashes($this->getEmail());
    $oDaoCgm->z01_cxpostal   = $this->getCaixaPostal();
    $oDaoCgm->z01_celcon     = $this->getCelularComercial();
    $oDaoCgm->z01_nome       = addslashes(substr($this->getNome(), 0, 40));
    $oDaoCgm->z01_nomecomple = addslashes(substr($this->getNomeCompleto(), 0, 100));
    $oDaoCgm->z01_emailc     = addslashes($this->getEmailComercial());
    $oDaoCgm->z01_numcon     = $this->getNumeroComercial();
    $oDaoCgm->z01_telcon     = $this->getTelefoneComercial();
    $oDaoCgm->z01_nacion     = $this->getNacionalidade();

    if (!empty($oDaoCgm->z01_numcgm)) {
      $oDaoCgm->alterar($this->getCodigo());
    } else {
      $oDaoCgm->z01_cadast = !empty($this->getCadastro()) ? $this->getCadastro() : date("Y-m-d");
      $oDaoCgm->z01_login = db_getsession('DB_id_usuario');

      $oDaoCgm->incluir(null);
    }

    if ( $oDaoCgm->erro_status == 0 ) {
    	throw new Exception("{$sMsgErro}, {$oDaoCgm->erro_msg}");
    }

    /**
     *  Seta o valor da propriedade $iCodigo com o número do CGM gerado
     */
    $this->setCodigo($oDaoCgm->z01_numcgm);

    if (!empty($this->getEnderecoPrimario())) {
        $this->salvaCgmEndereco();
    }
    /**
     * Caso o CGM informado seja do município da instituição então é verificado se
     * exite a rua e bairro informado nos respectivos cadastros do sistema
     *
     */
    try {
      $this->salvaBairroRua();
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }

   /**
    * inseri registro na cgmendereco {Primério}
    */
    if ($this->getEnderecoPrimario() != "" && $this->getEnderecoPrimario() != null) {
      try {
        $this->salvaCgmEndereco();
      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }
    }

    /**
    * inseri registro na cgmendereco {Secundério}
    */
    $oDaoCgmEndereco = new \cl_cgmendereco();

    if ($this->getEnderecoSecundario() != "" && $this->getEnderecoSecundario() != null) {
      $this->salvaCgmEnderecoSecundario();
    } else if ($this->getEnderecoSecundario() == "") {
      $sWhereCgmEnderSecundario = "z07_numcgm=".$this->getCodigo()." and z07_tipo = 'S' ";
      $oDaoCgmEndereco->excluir(null,$sWhereCgmEnderSecundario);

      if ( $oDaoCgmEndereco->erro_status == '0' ) {
        throw new Exception("{$oDaoCgmEndereco->erro_msg}");
      }
    }

    $rsCgmCgm = $oDaoCgmCgc->sql_record($oDaoCgmCgc->sql_query_file($this->getCodigo()));

    $oDaoCgmCgc->z01_numcgm = $this->getCodigo();
    $oDaoCgmCgc->z01_cgc    = $this->getCnpj();

    if ($oDaoCgmCgc->numrows > 0) {
	    $oDaoCgmCgc->alterar($this->getCodigo());
    } else {
    	$oDaoCgmCgc->incluir($this->getCodigo());
    }

    if ( $oDaoCgmCgc->erro_status == 0 ) {
      throw new Exception("{$sMsgErro}, {$oDaoCgmCgc->erro_msg}");
    }

    /**
     * Insere na cgmjuridico
     */

    $sCgmJuridico  = $oDaoCgmJuridico->sql_query_file(null, 'z08_sequencial', null, "z08_numcgm = ".$this->getCodigo());
    $rsCgmJuridico = $oDaoCgmJuridico->sql_record($sCgmJuridico);

    $oDaoCgmJuridico->z08_numcgm = $this->getCodigo();
    if ($this->sNire != "" && $this->sNire != null) {
      $oDaoCgmJuridico->z08_nire   = $this->sNire;
    }

    if ($rsCgmJuridico !== false) {
    	$oDaoCgmJuridico->z08_sequencial = db_utils::fieldsMemory($rsCgmJuridico,0)->z08_sequencial;
      $oDaoCgmJuridico->alterar(db_utils::fieldsMemory($rsCgmJuridico,0)->z08_sequencial);
    } else {
      $oDaoCgmJuridico->incluir(null);
    }

    if ( $oDaoCgmJuridico->erro_status == '0' ) {
      throw new Exception("{$sMsgErro}, {$oDaoCgmJuridico->erro_msg}");
    }

    if($this->iNacionalidade == 2){
        $this->vincularDocumentoEstrangeiroEmpresa();
    }

    $this->salvaCgmTipoEmpresa();
  }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), array(
            'cnpj' => $this->getCnpj()
        ));
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function vincularDocumentoEstrangeiroEmpresa()
    {

        $oDaoDocumento = new cl_cgmestrangeiro();
        $oDaoDocumento->excluir(null, "z09_numcgm = {$this->iCodigo}");

        if ( empty($this->iNacionalidade) || empty($this->sDocumentoEstrangeiro)) {
            return true;
        }

        $oDaoDocumento->z09_sequencial = null;
        $oDaoDocumento->z09_numcgm = $this->iCodigo;
        $oDaoDocumento->z09_documento = $this->sDocumentoEstrangeiro;
        $oDaoDocumento->z09_pais = $this->sPaisEstrangeiro;
        $oDaoDocumento->z09_cidade = $this->sCidadeEstrangeiro;
        $oDaoDocumento->incluir($oDaoDocumento->z09_sequencial);
        if ($oDaoDocumento->erro_status == "0") {
            throw new Exception("Não foi possível vincular o documento do CGM estrangeiro.");
        }
        return true;
    }

    /**
     * Carrega as informações do CGM Estrangeiro
     * @throws Exception
     */
    private function carregarInformacoesEstrangeiros()
    {

        if ($this->iNacionalidade != 0) {
            $oDaoDocumento = new cl_cgmestrangeiro();
            $sSqlDocumento = $oDaoDocumento->sql_query_file(null, "*", null, "z09_numcgm = {$this->iCodigo}");
            $rsDocumento = db_query($sSqlDocumento);
            if (!$rsDocumento) {
                throw new Exception("Ocorreu um erro ao buscar o número do documento estrangeiro.");
            }

            if (pg_num_rows($rsDocumento) > 0) {
                $stdDadosEstrangeiro = db_utils::fieldsMemory($rsDocumento, 0);
                $this->setDocumentoEstrangeiro($stdDadosEstrangeiro->z09_documento);
                $this->setCidadeEstrangeiro($stdDadosEstrangeiro->z09_cidade);
                $this->setPaisEstrangeiro($stdDadosEstrangeiro->z09_pais);
            }
        }
    }
}
