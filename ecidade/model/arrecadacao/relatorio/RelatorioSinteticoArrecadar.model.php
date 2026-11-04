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

class RelatorioSinteticoArrecadar {
  
  /** @var PDFDocument */
  private $oPdf;

  /** @var integer */
  private $iLarguraPagina;

  /** @var integer */
  private $iAlturaLinha;

  /* @var DBDate */
  private $oDataInicial;

  /* @var DBDate */
  private $oDataFinal;

  /* @var integer */
  private $iCgm;

  /* @var integer */
  private $iMatricula;

  /* @var integer */
  private $iInscricao;

  /* @var integer */
  private $iNumpre;

  /* @var integer */
  private $iCpf;

  /* @var integer */
  private $iCnpj;

  /* @var integer */
  private $iExercicioInicial;

  /* @var integer */
  private $iExercicioFinal;

  /* @var integer */
  private $sTiposDebito;
  
  /* @var String */
  private $sTipoBusca;
  
  /* @var String */
  private $sFuncaoDebitos;

  /*@var numeric*/
  private $nValorHistorico = 0;

  /*@var numeric*/
  private $nValorCorrigido = 0;

  /*@var numeric*/
  private $nValorJuros = 0;

  /*@var numeric*/
  private $nValorMulta = 0;

  /*@var numeric*/
  private $nValorDesconto = 0;

  /*@var numeric*/
  private $nValorTotal = 0;

  /*@var numeric*/
  private $sChavePesquisa;
  /**
   * @param DBDate $oDataInicial
   */
  public function setDataInicial(DBDate $oDataInicial) {
    $this->oDataInicial = $oDataInicial;
  }

  /**
   * @return DBDate
   */
  public function getDataInicial() {
    return $this->oDataInicial;
  }
  
  /**
   * @param DBDate $oDataFinal
   */
  public function setDataFinal(DBDate $oDataFinal) {
    $this->oDataFinal = $oDataFinal;
  }

  /**
   * @return DBDate
   */
  public function getDataFinal() {
    return $this->oDataFinal;
  }

  /**
   * @param String $sTiposDebito
   */
  public function setTiposDebito($sTiposDebito) {
    $this->sTiposDebito = $sTiposDebito;
  }

  /**
   * @return String
   */
  public function getTiposDebitos() {
    return $this->sTiposDebito;
  }

  /**
   * @param Integer $iExercicioInicial
   */
  public function setExercicioInicial($iExercicioInicial) {
    $this->iExercicioInicial = $iExercicioInicial;
  }

  /**
   * @return Integer
   */
  public function getExercicioInicial() {
    return $this->iExercicioInicial;
  }

  /**
   * @param Integer $iExercicioFinal
   */
  public function setExercicioFinal($iExercicioFinal) {
    $this->iExercicioFinal = $iExercicioFinal;
  }

  /**
   * @return Integer
   */
  public function getExercicioFinal() {
    return $this->iExercicioFinal;
  }
  
  /**
   * @param Integer $iCgm
   */
  public function setCgm($iCgm) {
    $this->iCgm = $iCgm;
  }

  /**
   * @return Integer
   */
  public function getCgm() {
    return $this->iCgm;
  }

  /**
   * @param Integer $iMatricula
   */
  public function setMatricula($iMatricula) {
    $this->iMatricula = $iMatricula;
  }

  /**
   * @return Integer
   */
  public function getMatricula() {
    return $this->iMatricula;
  }

  /**
   * @param Integer $iInscricao
   */
  public function setInscricao($iInscricao) {
    $this->iInscricao = $iInscricao;
  }

  /**
   * @return Integer
   */
  public function getInscricao() {
    return $this->iInscricao;
  }
  
   /**
   * @param Integer $iInscricao
   */
  public function setNumpre($iNumpre) {
    $this->iNumpre = $iNumpre;
  }

  /**
   * @return Integer
   */
  public function getNumpre() {
    return $this->iNumpre;
  }

  /**
   * @param Integer $iCpf
   */
  public function setCpf($iCpf) {
    $this->iCpf = $iCpf;
  }

  /**
   * @return Integer
   */
  public function getCpf() {
    return $this->iCpf;
  }

  /**
   * @param Integer $iCnpj
   */
  public function setCnpj($iCnpj) {
    $this->iCnpj = $iCnpj;
  }

  /**
   * @return Integer
   */
  public function getCnpj() {
    return $this->iCnpj;
  }

  /**
   * @param Integer $iCpfCnpj
   */
  public function setCpfCnpj($iCpfCnpj) {
    $tamanhoDoValor = strlen(strval($iCpfCnpj));
    if ($tamanhoDoValor <= 11) {
      $this->setCpf($iCpfCnpj);
    } else {
      $this->setCnpj($iCpfCnpj);
    }
    return;
  }

  /**
   * @return Array
   */
  public function getCpfCnpj() {
    $dado = [];
    if ($this->getCpf() != null) {
      $dado[] = "CPF";
      $dado[] = $this->getCpf();
    }
    if ($this->getCnpj() != null) {
      $dado[] = "CNPJ";
      $dado[] = $this->getCnpj();
    }
    return $dado;
  }

  private function processarDados(stdClass $oTipoDebito) {
    
    $oDataSessao  = new DBDate(date('Y-m-d', db_getsession("DB_datausu")));
    $DB_DATACALC  = $oDataSessao->getTimeStamp();
    $aWhere       = array();
    $where        = "";
    $and          = " and ";
       
    if ( isset($this->oDataInicial) && isset($this->oDataFinal)) {
    
    	$aWhere[]             = "k00_dtoper  between '{$this->oDataInicial->getDate()}' and '{$this->oDataFinal->getDate()}'";
    	$sDataInicio          = db_formatar($this->oDataInicial->getDate(), "d");
    	$sDataFim             = db_formatar($this->oDataFinal->getDate(), "d");
    	$sDadosPeriodo        = "De $sDataInicio até $sDataFim.";
    
    } else if (isset($this->oDataInicial)) {
    
    	$aWhere[]             = " k00_dtoper >= '{$this->oDataInicial->getDate()}'  ";
    	$sDataInicio          = db_formatar($this->oDataInicial->getDate(), "d");
      $sDataFim             = "";
    	$sDadosPeriodo        = "Apartir de $sDataInicio.";
    } else if (isset($this->oDataFinal)) {
    
      $aWhere[]             = " k00_dtoper <= '{$this->oDataFinal->getDate()}'   ";
      $sDataInicio          = "";
      $sDataFim             = db_formatar($this->oDataFinal->getDate(), "d");
      $sDadosPeriodo        = "Até $sDataFim.";
    }
    
    if ( !empty($this->iExercicioInicial) && !empty($this->iExercicioFinal) ) {
    
    	$aWhere[]              = "fc_arrecexerc(y.k00_numpre,y.k00_numpar)  between '{$this->iExercicioInicial}' and '{$this->iExercicioFinal}'  ";
    	$sDadosExercicio       = "Do exercício {$this->iExercicioInicial} até {$this->iExercicioFinal}.";
    } else if ( !empty($this->iExercicioInicial) ) {
    
    	$aWhere[]              = "fc_arrecexerc(y.k00_numpre,y.k00_numpar) >= '{$this->iExercicioInicial}'  ";
    	$sDadosExercicio       = "Apartir do exercício {$this->iExercicioInicial}.";
    } else if ( !empty($this->iExercicioFinal) ) {
    
    	$aWhere[]              = "fc_arrecexerc(y.k00_numpre,y.k00_numpar) <= '{$this->iExercicioFinal}'   ";
    	$sDadosExercicio       = "Até o exercício {$this->iExercicioFinal}.";
    }

    $oDadosRelatorio              = new stdClass();
    $oDadosRelatorio->aDebitos    = array();
    $aTiposDebitosDetalhe         = array();
    $oDadosRelatorio->aSuspensoes = array();
    $aValoresTipoDebito           = $oDadosRelatorio->aDebitos;

    
    /**
     * Parametros da Funcao
     */
    $aParametros     = array();
    $aParametros[]   = $this->sChavePesquisa;           // Valor do Tipo de Pesquisa Ex.: Numero do CGM, MATRICULA...
    $aParametros[]   = 0;                         // Limite de Registros
    $aParametros[]   = $oTipoDebito->k00_tipo;    // Tipo de Debito
    $aParametros[]   = $DB_DATACALC;              // Data Base para Calculo
    $aParametros[]   = db_getsession("DB_anousu");// Ano da Sessao
    
      /**
       * Adicionamos uma posicao a mais no array para quando por pesquisa por Numpre
       */
      if( $this->sTipoBusca == 'N' ){
        $aParametros[] = "";                        // Numpar
      }
      $aParametros[]   = "";                        // Totaliza
      $aParametros[]   = "";                        // Ordem Totalizacao
      $aParametros[]   = count($aWhere) > 0 ? "and " . implode(" and ", $aWhere) : ""; // Filtros para a Pesquisa
      $aParametros[]   = "";                        // Justific
      $aParametros[]   = "";                        // Instit
    
      /**
       * Chama a Função de débitos conforme o tipo de Origem selecionada
       * - debitos_matricula() - debitos_matricula($matricula, $limite, $tipo, $datausu, $anousu, $totaliza="", $totalizaordem="", $db_where="",      $justific=false, $instit=null )
       * - debitos_inscricao() - debitos_inscricao($inscricao, $limite, $tipo, $datausu, $anousu, $totaliza="", $totalizaordem="", $db_where="",      $justific=false, $instit=null)
       * - debitos_numcgm()    - debitos_numcgm   ($numcgm,    $limite, $tipo, $datausu, $anousu, $totaliza="", $totalizaordem="", $db_where="",      $justific=false, $instit=null )
       * - debitos_numpre()    - debitos_numpre   ($numpre,    $limite, $tipo, $datausu, $anousu, $numpar=0,    $totaliza="",      $totalizaordem="", $db_where="",    $justific=false, $instit=null )
       */
      $rsDebitos        = call_user_func_array($this->sFuncaoDebitos, $aParametros);
    
      if ( !is_resource($rsDebitos) ) {
        throw new DBException( "Não existem debitos({$oTipoDebito->k00_descr}) para o Exercicio/Periodo informado.");
      }
      $aNumpreDebito  = array();
      $oOrigem        = array();
      
      for ( $iIndiceDebitos = 0; $iIndiceDebitos < pg_num_rows($rsDebitos); $iIndiceDebitos++ ) {
      
        $oDebitos                                   = db_utils::fieldsMemory($rsDebitos, $iIndiceDebitos);
        
        $oValorBase                                 = new stdClass();
        $oValorBase->nValorHistorico                = 0;
        $oValorBase->nValorCorrigido                = 0;
        $oValorBase->nValorJuros                    = 0;
        $oValorBase->nValorMulta                    = 0;
        $oValorBase->nValorDesconto                 = 0;
        $oValorBase->nValorTotal                    = 0;
        if(isset($oDebitos->k00_origem)) {

          $oOrigem[$oDebitos->k00_origem]  =  $oDebitos->k00_origem; 
        }
        $sSqlExecicio = "select fc_arrecexerc($oDebitos->k00_numpre, $oDebitos->k00_numpar) as exercicio";
        $rsExercicio  = db_query($sSqlExecicio);
        if(!$rsExercicio) {
          
          throw new Exception("Não foi possível encontrar o exercício do numpre {$oDebitos->k00_numpre}");
        }
        $oExercicio = db_utils::fieldsMemory($rsExercicio, 0);
        $iExercicio = $oExercicio->exercicio;

        switch ($oTipoDebito->k03_tipo) {
    
            case 6:
            case 13:
            case 16:
            case 17:
              
              $daoTermo = db_utils::getDao("termo");

              $rsTermo  = $daoTermo->sql_record(
                          $daoTermo->sql_query_file(null, "v07_parcel, v07_dtlanc, (select min(k00_dtvenc) from arrecad where k00_numpre = v07_numpre) as v07_datpri", null, "v07_numpre={$oDebitos->k00_numpre}"));
    
              if ($daoTermo->numrows != 1) {
                
                throw new Exception("Tipo errado termo, numpre {$oDebitos->k00_numpre}");
              }
    
              $oTermo  = db_utils::fieldsMemory($rsTermo, 0);
              $indice  = $oTipoDebito->k00_tipo."-".$oTermo->v07_parcel."-".$oTermo->v07_dtlanc;
              $indice .= "-".$oDebitos->k00_numpre."-".$oTermo->v07_datpri;

              if ( isset($oDadosRelatorio->aDebitos[$indice]) ) {
                $oValorBase = $oDadosRelatorio->aDebitos[$indice];
              }   
              $oDebitosRelatorio                          = new stdClass();
              $oDebitosRelatorio->indice                  = $indice; 
              $oDebitosRelatorio->iTipoDebito             = $oTipoDebito->k00_tipo;
              $oDebitosRelatorio->parcel                  = $oTermo->v07_parcel;
              $oDebitosRelatorio->dtlanc                  = db_formatar($oTermo->v07_dtlanc, 'd');
              $oDebitosRelatorio->numpre                  = $oDebitos->k00_numpre;
              $oDebitosRelatorio->datpri                  = db_formatar($oTermo->v07_datpri,'d');
              $oDebitosRelatorio->sDescricaoTipoDebito    = $oTipoDebito->k00_descr;
              $oDebitosRelatorio->nValorHistorico         = $oValorBase->nValorHistorico  + $oDebitos->vlrhis;
              $oDebitosRelatorio->nValorCorrigido         = $oValorBase->nValorCorrigido  + $oDebitos->vlrcor;
              $oDebitosRelatorio->nValorJuros             = $oValorBase->nValorJuros      + $oDebitos->vlrjuros;
              $oDebitosRelatorio->nValorMulta             = $oValorBase->nValorMulta      + $oDebitos->vlrmulta;
              $oDebitosRelatorio->nValorDesconto          = $oValorBase->nValorDesconto   + $oDebitos->vlrdesconto;
              $oDebitosRelatorio->nValorTotal             = $oValorBase->nValorTotal      + $oDebitos->total;
              
              $oDebitosRelatorio->agrupador = ['espaco'     => '',
                            'parcel' => 'Parcel.', 
                            'numpre' => 'Numpre',
                            'dtlanc' => 'Dt Lanç.',
                            'datpri' => 'Dt Venc.'];
              $oDadosRelatorio->aDebitos[$indice] = $oDebitosRelatorio;
            break;
    
            case 18:
              
              $daoInicial = db_utils::getDao("inicial");
              
              $rsInicial  = $daoInicial->sql_record(
                            $daoInicial->sql_query_FiltrarInicialCdaPorNumpre($oDebitos->k00_numpre));
    
              if ($daoInicial->numrows != 1) {
                
                throw new Exception("Tipo errado");
              }
    
              $oInicial  = db_utils::fieldsMemory($rsInicial, 0);
              $indice  = $oDebitos->k00_origem.'-'.$oTipoDebito->k00_tipo."-".$oInicial->v59_inicial."-".$oInicial->v13_certid."-".$iExercicio;
              if ( isset($oDadosRelatorio->aDebitos[$indice]) ) {
                $oValorBase = $oDadosRelatorio->aDebitos[$indice];
              } 
              $oDebitosRelatorio                          = new stdClass();
              $oDebitosRelatorio->indice                  = $indice; 
              $oDebitosRelatorio->iTipoDebito             = $oTipoDebito->k00_tipo;
              $oDebitosRelatorio->origem                  = $oDebitos->k00_origem;
              $oDebitosRelatorio->inicial                 = $oInicial->v59_inicial;
              $oDebitosRelatorio->certidao                = $oInicial->v13_certid;
              $oDebitosRelatorio->iExercicio              = $iExercicio != null?$iExercicio:$oInicial->exercicio;
              $oDebitosRelatorio->sDescricaoTipoDebito    = $oTipoDebito->k00_descr;
              $oDebitosRelatorio->nValorHistorico         = $oValorBase->nValorHistorico  + $oDebitos->vlrhis;
              $oDebitosRelatorio->nValorCorrigido         = $oValorBase->nValorCorrigido  + $oDebitos->vlrcor;
              $oDebitosRelatorio->nValorJuros             = $oValorBase->nValorJuros      + $oDebitos->vlrjuros;
              $oDebitosRelatorio->nValorMulta             = $oValorBase->nValorMulta      + $oDebitos->vlrmulta;
              $oDebitosRelatorio->nValorDesconto          = $oValorBase->nValorDesconto   + $oDebitos->vlrdesconto;
              $oDebitosRelatorio->nValorTotal             = $oValorBase->nValorTotal      + $oDebitos->total;
              $oDebitosRelatorio->agrupador = ['espaco'     => '',
                                               'origem'    => 'Origem',
                                               'inicial'    => 'Inicial', 
                                               'certidao'   => 'Certidão',
                                               'iExercicio' => 'Exercício'
                                               ];
              $oDadosRelatorio->aDebitos[$indice] = $oDebitosRelatorio;
             
            break;
    
            case 15:

              $daoCertid = db_utils::getDao("certid");
              $rsCertid  = $daoCertid->sql_record(
                            $daoCertid->sql_query_cgm(null, 
                                                      "distinct v13_certid, coalesce(extract(year from v07_dtlanc), v01_exerc) as exercicio", 
                                                      null, 
                                                      "v07_numpre = $oDebitos->k00_numpre or v01_numpre = $oDebitos->k00_numpre"));    
              if ($daoCertid->numrows != 1) {
                
                throw new Exception("Tipo errado");
              }
    
              $oCertid = db_utils::fieldsMemory($rsCertid, 0);
              $indice  = $oTipoDebito->k00_tipo."-".$oCertid->v13_certid."-".$iExercicio;
              if ( isset($oDadosRelatorio->aDebitos[$indice]) ) {
                $oValorBase = $oDadosRelatorio->aDebitos[$indice];
              } 
              $oDebitosRelatorio                          = new stdClass();
              $oDebitosRelatorio->indice                  = $indice; 
              $oDebitosRelatorio->iTipoDebito             = $oTipoDebito->k00_tipo;
              $oDebitosRelatorio->certidao                = $oCertid->v13_certid;
              $oDebitosRelatorio->iExercicio              = empty($iExercicio)?$iExercicio:$oCertid->exercicio;
              $oDebitosRelatorio->sDescricaoTipoDebito    = $oTipoDebito->k00_descr;
              $oDebitosRelatorio->nValorHistorico         = $oValorBase->nValorHistorico  + $oDebitos->vlrhis;
              $oDebitosRelatorio->nValorCorrigido         = $oValorBase->nValorCorrigido  + $oDebitos->vlrcor;
              $oDebitosRelatorio->nValorJuros             = $oValorBase->nValorJuros      + $oDebitos->vlrjuros;
              $oDebitosRelatorio->nValorMulta             = $oValorBase->nValorMulta      + $oDebitos->vlrmulta;
              $oDebitosRelatorio->nValorDesconto          = $oValorBase->nValorDesconto   + $oDebitos->vlrdesconto;
              $oDebitosRelatorio->nValorTotal             = $oValorBase->nValorTotal      + $oDebitos->total;
              $oDebitosRelatorio->agrupador = ['espaco'     => '',
                                               'espaco1'     => '',
                                               'espaco2'     => '',
                                               'certidao' => 'Certidão',
                                               'iExercicio' => 'Exercício'];
              $oDadosRelatorio->aDebitos[$indice] = $oDebitosRelatorio;
            break;
    
            case 11:
              
              $daoAuto = db_utils::getDao("autonumpre");
              
              $rsAuto  = $daoAuto->sql_record(
                            $daoAuto->sql_query(null, "y17_codauto", null, "y17_numpre = $oDebitos->k00_numpre"));
    
              if ($daoAuto->numrows != 1) {
                
                throw new Exception("Tipo errado");
              }
    
              $oAuto  = db_utils::fieldsMemory($rsAuto, 0);
              $indice = $oTipoDebito->k00_tipo."-".$oAuto->y17_codauto."-".$iExercicio;
              $oDebitosRelatorio                          = new stdClass();
              $indice                                     = $oTipoDebito->k00_tipo."-".$oAuto->y17_codauto."-".$iExercicio;
              if ( isset($oDadosRelatorio->aDebitos[$indice]) ) {
                $oValorBase = $oDadosRelatorio->aDebitos[$indice];
              }

              $oDebitosRelatorio->indice                  = $indice; 
              $oDebitosRelatorio->iTipoDebito             = $oTipoDebito->k00_tipo; 
              $oDebitosRelatorio->codauto                 = $oAuto->y17_codauto;
              $oDebitosRelatorio->iExercicio              = $iExercicio;
              $oDebitosRelatorio->dtlanc                  = db_formatar($oDebitos->k00_dtoper, 'd');
              $oDebitosRelatorio->sDescricaoTipoDebito    = $oTipoDebito->k00_descr;
              $oDebitosRelatorio->nValorHistorico         = $oValorBase->nValorHistorico  + $oDebitos->vlrhis;
              $oDebitosRelatorio->nValorCorrigido         = $oValorBase->nValorCorrigido  + $oDebitos->vlrcor;
              $oDebitosRelatorio->nValorJuros             = $oValorBase->nValorJuros      + $oDebitos->vlrjuros;
              $oDebitosRelatorio->nValorMulta             = $oValorBase->nValorMulta      + $oDebitos->vlrmulta;
              $oDebitosRelatorio->nValorDesconto          = $oValorBase->nValorDesconto   + $oDebitos->vlrdesconto;
              $oDebitosRelatorio->nValorTotal             = $oValorBase->nValorTotal      + $oDebitos->total;
              $oDebitosRelatorio->agrupador = ['espaco'           => '',
                                               'espaco1'          => '',
                                               'codauto'          => 'Auto',
                                               'iExercicio'       => 'Exercício',
                                               'dtlanc'           => 'Dt. Lanç.',
                                               ];
                                                             
              
              
              $oDadosRelatorio->aDebitos[$indice] = $oDebitosRelatorio; 
            break;
               
            default:
              if($oTipoDebito->k03_tipo == 1 || $oTipoDebito->k03_tipo == 2 || $oTipoDebito->k03_tipo == 7 && $oDebitos->k00_origem){
                $oDebitosRelatorio                          = new stdClass();
                $indice                                     = $oDebitos->k00_origem.'-'.$oTipoDebito->k00_tipo."-".$iExercicio;
                if ( isset($oDadosRelatorio->aDebitos[$indice]) ) {
                  $oValorBase = $oDadosRelatorio->aDebitos[$indice];
                }                                                
                $oDebitosRelatorio->indice                  = $indice; 
                $oDebitosRelatorio->iTipoDebito             = $oTipoDebito->k00_tipo; 
                $oDebitosRelatorio->origem                  = $oDebitos->k00_origem;
                $oDebitosRelatorio->iExercicio              = $iExercicio;
                $oDebitosRelatorio->dtlanc                  = db_formatar($oDebitos->k00_dtoper, 'd');
                $oDebitosRelatorio->sDescricaoTipoDebito    = $oTipoDebito->k00_descr;
                $oDebitosRelatorio->nValorHistorico         = $oValorBase->nValorHistorico  + $oDebitos->vlrhis;
                $oDebitosRelatorio->nValorCorrigido         = $oValorBase->nValorCorrigido  + $oDebitos->vlrcor;
                $oDebitosRelatorio->nValorJuros             = $oValorBase->nValorJuros      + $oDebitos->vlrjuros;
                $oDebitosRelatorio->nValorMulta             = $oValorBase->nValorMulta      + $oDebitos->vlrmulta;
                $oDebitosRelatorio->nValorDesconto          = $oValorBase->nValorDesconto   + $oDebitos->vlrdesconto;
                $oDebitosRelatorio->nValorTotal             = $oValorBase->nValorTotal      + $oDebitos->total;
                $oDebitosRelatorio->agrupador = ['espaco'     => '',
                                                'espaco1'     => '',
                                                'origem'     => 'Origem',
                                                'iExercicio' => 'Exercício',
                                                'dtlanc' => 'Dt. Lanç.',
                                                ];
                $oDadosRelatorio->aDebitos[$indice] = $oDebitosRelatorio;
                break;
              }
            
              $oDebitosRelatorio                          = new stdClass();
              $indice                                     = $oTipoDebito->k00_tipo."-".$iExercicio;
              if ( isset($oDadosRelatorio->aDebitos[$indice]) ) {
                $oValorBase = $oDadosRelatorio->aDebitos[$indice];
              }                                                
              $oDebitosRelatorio->indice                  = $indice; 
              $oDebitosRelatorio->iTipoDebito             = $oTipoDebito->k00_tipo; 
              $oDebitosRelatorio->iExercicio              = $iExercicio;
              $oDebitosRelatorio->dtlanc                  = db_formatar($oDebitos->k00_dtoper, 'd');
              $oDebitosRelatorio->sDescricaoTipoDebito    = $oTipoDebito->k00_descr;
              $oDebitosRelatorio->nValorHistorico         = $oValorBase->nValorHistorico  + $oDebitos->vlrhis;
              $oDebitosRelatorio->nValorCorrigido         = $oValorBase->nValorCorrigido  + $oDebitos->vlrcor;
              $oDebitosRelatorio->nValorJuros             = $oValorBase->nValorJuros      + $oDebitos->vlrjuros;
              $oDebitosRelatorio->nValorMulta             = $oValorBase->nValorMulta      + $oDebitos->vlrmulta;
              $oDebitosRelatorio->nValorDesconto          = $oValorBase->nValorDesconto   + $oDebitos->vlrdesconto;
              $oDebitosRelatorio->nValorTotal             = $oValorBase->nValorTotal      + $oDebitos->total;
              $oDebitosRelatorio->agrupador = ['espaco'     => '',
                                               'espaco1'     => '',
                                               'espaco2'     => '',
                                               'iExercicio' => 'Exercício',
                                               'dtlanc' => 'Dt. Lanç.',
                                               ];
                                                             
              
              
              $oDadosRelatorio->aDebitos[$indice] = $oDebitosRelatorio;
            break;
          }
          
          
      }
   
      $aTotalDebito["nValorHistorico"] = 0;
      $aTotalDebito["nValorCorrigido"] = 0;
      $aTotalDebito["nValorJuros"]     = 0;
      $aTotalDebito["nValorMulta"]     = 0;
      $aTotalDebito["nValorDesconto"]  = 0;
      $aTotalDebito["nValorAcrescimo"] = 0;
      $aTotalDebito["nValorTotal"]     = 0;
  
      $cabecalhoAtual           = null;
      $imprimecabecalho         = true;
      $imprimeTotalizadorTipo   = false;
      $pageAtual                = $this->oPdf->getCurrentPage();
      foreach ( $oDadosRelatorio->aDebitos as $oValoresDebitos) {
        
        if($this->oPdf->y+$this->iAlturaLinha>$this->oPdf->PageBreakTrigger && !$this->oPdf->InFooter && $this->oPdf->AcceptPageBreak()) {
        
          $imprimecabecalho = true;
        }

        if($imprimecabecalho) {
          
          $this->oPdf->SetFont('Arial', 'B', 7); 
          $tamanhoDescricao = 0;
          foreach($oValoresDebitos->agrupador as $indice => $agrupa) {

            $borda         = 1;
            $preenchimento = 1;
            if(in_array($indice, ["espaco", "espaco1", "espaco2"])) {
              $borda = 0;
              $preenchimento = 0;
            }

            $this->oPdf->Cell(15, $this->iAlturaLinha, $agrupa        , $borda, 0, "C", $preenchimento);
            $tamanhoDescricao += 15;
          }   
          $this->oPdf->Cell(18, $this->iAlturaLinha, "Vlr Histórico"  , 1, 0, "C", 1);
          $this->oPdf->Cell(18, $this->iAlturaLinha, "Vlr Corrigido"  , 1, 0, "C", 1);
          $this->oPdf->Cell(18, $this->iAlturaLinha, "Vlr Juros"      , 1, 0, "C", 1);
          $this->oPdf->Cell(18, $this->iAlturaLinha, "Vlr Multa"      , 1, 0, "C", 1);
          $this->oPdf->Cell(18, $this->iAlturaLinha, "Descontos"      , 1, 0, "C", 1);
          $this->oPdf->Cell(18, $this->iAlturaLinha, "Total"          , 1, 1, "C", 1);
          $imprimecabecalho = false;
        }
        
       $this->oPdf->SetFont('Arial', '', 7);
       foreach ($oValoresDebitos->agrupador as $titulo => $nome) {
         
        $borda = 1;
        if(in_array($titulo, ["espaco", "espaco1", "espaco2"])) {
          $borda = 0;
          $this->oPdf->Cell(15, $this->iAlturaLinha, "", $borda, 0, "R", 0);
          continue;
        }
          
         $this->oPdf->Cell(15, $this->iAlturaLinha, $oValoresDebitos->$titulo, $borda, 0, "R", 0);
       } 
       $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($oValoresDebitos->nValorHistorico , 'f'), 1, 0, "R", 0);
       $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($oValoresDebitos->nValorCorrigido , 'f'), 1, 0, "R", 0);
       $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($oValoresDebitos->nValorJuros     , 'f'), 1, 0, "R", 0);
       $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($oValoresDebitos->nValorMulta     , 'f'), 1, 0, "R", 0);
       $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($this->oPdf->getCurrentPage()  , 'f'), 1, 0, "R", 0);
       $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($oValoresDebitos->nValorTotal     , 'f'), 1, 1, "R", 0);
       
       $aTotalDebito["nValorHistorico"] += $oValoresDebitos->nValorHistorico;
       $aTotalDebito["nValorCorrigido"] += $oValoresDebitos->nValorCorrigido;
       $aTotalDebito["nValorJuros"]     += $oValoresDebitos->nValorJuros;
       $aTotalDebito["nValorMulta"]     += $oValoresDebitos->nValorMulta;
       $aTotalDebito["nValorDesconto"]  += $oValoresDebitos->nValorDesconto;
       $aTotalDebito["nValorTotal"]     += $oValoresDebitos->nValorTotal;
       
      }
      
      $this->nValorHistorico += $aTotalDebito["nValorHistorico"];
      $this->nValorCorrigido += $aTotalDebito["nValorCorrigido"];
      $this->nValorJuros     += $aTotalDebito["nValorJuros"];
      $this->nValorMulta     += $aTotalDebito["nValorMulta"];
      $this->nValorDesconto  += $aTotalDebito["nValorDesconto"]; 
      $this->nValorTotal     += $aTotalDebito["nValorTotal"]; 
      $this->oPdf->SetFont('Arial', 'B', 7);
      $this->oPdf->Cell(7,  $this->iAlturaLinha, "", 0, 0, "L", 0);
      $this->oPdf->Cell($tamanhoDescricao - 7, $this->iAlturaLinha, $oValoresDebitos->sDescricaoTipoDebito,1,0,"L",0);
      $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($aTotalDebito["nValorHistorico"] , 'f'), 1, 0, "R", 0);
      $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($aTotalDebito["nValorCorrigido"] , 'f'), 1, 0, "R", 0);
      $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($aTotalDebito["nValorJuros"]     , 'f'), 1, 0, "R", 0);
      $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($aTotalDebito["nValorMulta"]     , 'f'), 1, 0, "R", 0);
      $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($aTotalDebito["nValorDesconto"]  , 'f'), 1, 0, "R", 0);
      $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($aTotalDebito["nValorTotal"]     , 'f'), 1, 1, "R", 0);
      if(count($oOrigem) > 0) {
        if($oTipoDebito->k03_tipo != 1 && $oTipoDebito->k03_tipo != 2 && $oTipoDebito->k03_tipo != 7 &&  $oTipoDebito->k03_tipo != 18){
          $sOrigens = implode(", ", $oOrigem);
          $this->oPdf->Cell(7,  $this->iAlturaLinha, "", 0, 0, "L", 0);
          $this->oPdf->Cell(20,  $this->iAlturaLinha, "Origem", 1, 0, "L", 0);
          $this->oPdf->SetFont('Arial', '', 7);
          $this->oPdf->MultiCell(156, $this->iAlturaLinha, $sOrigens,1,"L",0);
        }        
      }
      $this->oPdf->SetFont('Arial', '', 8);
      $this->oPdf->Ln();
  }
  

  /**
   * Configurar Emissão e Filtros no Cabeçalho do Relatório
   */
  private function configurar() {

    $this->oPdf->Open();
    $this->oPdf->addHeaderDescription("Relatório do Total dos Débitos Sintético Novo");
    $this->oPdf->addHeaderDescription("");
    if ($this->getDataInicial()) {
      $this->oPdf->addHeaderDescription("DATA INICIAL: {$this->getDataInicial()->getDate(DBDate::DATA_PTBR)}");
    } 

    if($this->getDataFinal()) {      
      $this->oPdf->addHeaderDescription("DATA FINAL {$this->getDataFinal()->getDate(DBDate::DATA_PTBR)}");
    }

    if ($this->getExercicioInicial()) {
      $this->oPdf->addHeaderDescription("ANO INICIAL: {$this->getExercicioInicial()}"); 
    } 
    
    if ($this->getExercicioFinal()) {
      $this->oPdf->addHeaderDescription("ANO FINAL {$this->getExercicioFinal()}");
    }

    $this->oPdf->setAutoNewLineMulticell(true);
    $this->oPdf->SetFillColor(235);
    $this->oPdf->setFontFamily("arial");
    $this->oPdf->SetFontSize(6);
    $this->oPdf->AddPage();
    $this->iAlturaLinha   = 4;
    $this->iLarguraPagina = $this->oPdf->getAvailWidth();
  }

  private function dadosContribuinte() {
    
    $this->sTipoBusca            = isset($this->iMatricula) ? "M" :
                                (isset($this->iInscricao)? "I" :
                                (isset($this->iCgm) ? "C" :
                                (isset($this->iNumpre) ? "N" : null)));
                                    
    $sCampos          = "db21_regracgmiss, db21_regracgmiptu";
    $oDaoInstituicao  = new cl_db_config();
    $sSqlConfiguracao = $oDaoInstituicao->sql_query_file(db_getsession("DB_instit"), $sCampos);
    $rsConfiguracao   = $oDaoInstituicao->sql_record($sSqlConfiguracao);
  
    $oDadoConfigTributario = null;
    if ($oDaoInstituicao->numrows == 0) {
      throw new Exception("Não foi encontrado regra para definir o cgm do Iptu.");
    }
    $oDadoConfigTributario = db_utils::fieldsMemory($rsConfiguracao, 0);  
    switch ($this->sTipoBusca) {
  
      /**
       * Matrícula
       */
      case "M":
        
        $this->sFuncaoDebitos        = "debitos_matricula";
        $this->sChavePesquisa        = $this->getMatricula();
        $sSqlDadosProprietario = "select * from proprietario where j01_matric = $this->sChavePesquisa limit 1";
        $rsDadosProprietario   = db_query($sSqlDadosProprietario);

        $sSqlPromitente  = "select promitente.j41_numcgm as rinumcgm, cgm.z01_cgccpf from promitente ";
        $sSqlPromitente .= "  join proprietario on proprietario.j41_numcgm = promitente.j41_numcgm ";
        $sSqlPromitente .= "  join cgm on cgm.z01_numcgm = promitente.j41_numcgm ";
        $sSqlPromitente .= "where j41_matric = $this->sChavePesquisa ";
        $rsPromitente = db_query($sSqlPromitente);
        $existePromitente = pg_num_rows($rsPromitente) > 0;

        if ($existePromitente) {
          $rsEnvolvidoRegra = $rsPromitente;
        } else {
          $sSqlEnvolvido  = "select * from fc_busca_envolvidos(false, {$oDadoConfigTributario->db21_regracgmiptu}, 'M', $this->sChavePesquisa) ";
          $sSqlEnvolvido .= "  join cgm on cgm.z01_numcgm = rinumcgm";
          $rsEnvolvido = db_query($sSqlEnvolvido);
          $rsEnvolvidoRegra = $rsEnvolvido;
        }
        $dadosEnvolvido = db_utils::fieldsMemory($rsEnvolvidoRegra, 0);
        $this->setCpfCnpj($dadosEnvolvido->z01_cgccpf);
        $oEnvolvidoRegra = CgmFactory::getInstanceByCgm($dadosEnvolvido->rinumcgm);

        $oDaoIptubase = new cl_iptubase();
        $sSqlTipoEnvolvido = $oDaoIptubase->sql_query_envolvidos_matricula($this->sChavePesquisa, $dadosEnvolvido->rinumcgm);
        $rsTipoEnvolvido = db_query($sSqlTipoEnvolvido);
        $oTipoEnvolvido = db_utils::getCollectionByRecord($rsTipoEnvolvido)[0];
  
        if ( !$rsDadosProprietario ) {
          throw new DBException("Erro ao Processar Pesquisa do Proprietário:".pg_last_error());
        }

        if ( pg_num_rows($rsDadosProprietario) == 0 ) {
          throw new BusinessException("Não Foi possivel emitir o Relatório pois não existe proprietário vinculado a Matrícula:".$this->sChavePesquisa);
        }
  
        $oDadosPesquisa = db_utils::fieldsMemory($rsDadosProprietario, 0);
  
        $sNome          = $oTipoEnvolvido->nomecomabreviatura;
        $sEndereco      = $oDadosPesquisa->tipopri    . ' '  .
                          $oDadosPesquisa->nomepri    . ', ' .
                          $oDadosPesquisa->j39_numero . ' '  .  
                          $oDadosPesquisa->j39_compl;
        
        
        $sOutrosDados1 = $GLOBALS['RLj40_refant'];
        $sOutrosDados2 = $oDadosPesquisa->j40_refant;
        $sOutrosDados3 = 'MATRÍCULA';
        $sOutrosDados4 = "Setor: "     . $oDadosPesquisa->j34_setor  .
                         " Quadra: " . $oDadosPesquisa->j34_quadra .
                         " Lote: "   . $oDadosPesquisa->j34_lote;
      break;
  
      /**
       * Inscrição
       */
      case "I":
        
        $this->sFuncaoDebitos        = "debitos_inscricao";
        $this->sChavePesquisa        = $this->getInscricao();
        $sSqlDadosProprietario =  "select * from empresa where q02_inscr = $this->sChavePesquisa";
        $rsDadosEmpresa        = db_query($sSqlDadosProprietario);  
        if ( !$rsDadosEmpresa ) {
          throw new DBException("Erro ao Buscar Pesquisa da Empresa:".pg_last_error());
        }
        if ( pg_num_rows($rsDadosEmpresa) == 0 ) {
          throw new BusinessException("Não Foi possivel emitir o Relatório pois a Empresa não existe:".$this->sChavePesquisa);
        }
  
        $oDadosPesquisa = db_utils::fieldsMemory($rsDadosEmpresa, 0);
        $sNome          = $oDadosPesquisa->z01_nome;
        $sEndereco      = $oDadosPesquisa->j14_tipo   . '  ' .
                          $oDadosPesquisa->z01_ender  . ', ' .
                          $oDadosPesquisa->z01_numero . '  ' .
                          $oDadosPesquisa->z01_compl;
  
        $sOutrosDados1  = 'ATIVIDADE';
        $sOutrosDados2  = $oDadosPesquisa->q03_descr;
        $sOutrosDados3  = 'INSCRIÇÃO';
        $sOutrosDados4  = "";
  
      break;
  
    /**
     * Cgm
     */
    case "C":
      
      $this->sFuncaoDebitos = "debitos_numcgm";
      $this->sChavePesquisa = $this->getCgm();
      $oCgm           = CgmFactory::getInstanceByCgm($this->getCgm());
      $sNome          = $oCgm->getNome();
      $sEndereco      = $oCgm->getLogradouro() . ', ' .
                        $oCgm->getNumero()     . ' '  .
                        $oCgm->getComplemento();
      $sOutrosDados1  = '';
      $sOutrosDados2  = '';
      $sOutrosDados3  = 'CGM';
      $sOutrosDados4  = "";
  
    break;
  
    /**
     * Numpre
     */
    case "N":

      $this->sFuncaoDebitos   = "debitos_numpre";
      $this->sChavePesquisa   = $this->getNumpre();
      $sSqlEnvolvido    = "select * ";
      $sSqlEnvolvido   .= "  from fc_socio_promitente( {$this->sChavePesquisa}, 'true',{$oDadoConfigTributario->db21_regracgmiptu}, {$oDadoConfigTributario->db21_regracgmiss} )";
      $rsEnvolvidoRegra = db_query($sSqlEnvolvido);
  
      if ( !$rsEnvolvidoRegra ) {
        throw new DBException( "Erro ao Buscar dados do Débito: ".pg_last_error() );
      }
  
      if ( pg_num_rows($rsEnvolvidoRegra) == 0 ) {
        throw new BusinessException( "Não Foi possivel emitir o Relatório pois o debito({$this->sChavePesquisa}) não existe" );
      }
  
      $oCgm  = CgmFactory::getInstanceByCgm(db_utils::fieldsMemory($rsEnvolvidoRegra, 0)->rinumcgm);
  
      $sNome          = $oCgm->getNome();
      $sEndereco      = $oCgm->getLogradouro() . ', ' .
                        $oCgm->getNumero()     . ' '  .
                        $oCgm->getComplemento();
  
      $sOutrosDados1  = '';
      $sOutrosDados2  = '';
      $sOutrosDados3  = 'NUMPRE';
      $sOutrosDados4  = "";
    break;
  
      /**
       * Erro de Parâmetro
       */
      default:
        throw new ParameterException("Tipo de Busca de Dados Não Informado ou Inválido");
      break;
    }
      
    $this->oPdf->SetFillColor(235);
    $this->oPdf->SetLineWidth(0.5);
    $this->oPdf->Ln(3);
    $this->oPdf->Cell(191, 2           , ''                                         ,"T", 1, "R", 0);
    $this->oPdf->SetFont('Arial', 'B', 8);
    $this->oPdf->Cell(30, $this->iAlturaLinha, $sOutrosDados3                             ,  0, 0, "L", 0);
    $this->oPdf->SetFont('Arial', 'I', 8);
    $this->oPdf->Cell(80, $this->iAlturaLinha, ': '.$this->sChavePesquisa. '  ' . $sOutrosDados4,  0, 1, "L", 0);
    $this->oPdf->SetFont('Arial', 'B', 8);
    $this->oPdf->Cell(30, $this->iAlturaLinha, "NOME"                                     ,  0, 0, "L", 0);
    $this->oPdf->SetFont('Arial', 'I', 8);
    $this->oPdf->Cell(80, $this->iAlturaLinha, ': ' . $sNome                              ,  0, 1, "L", 0);

    if (count($this->getCpfCnpj()) > 0) {
      $dados = $this->getCpfCnpj();
      $this->oPdf->SetFont('Arial', 'B', 8);
      $this->oPdf->Cell(30, $this->iAlturaLinha,        $dados[0] , 0, 0, "L", 0);
      $this->oPdf->SetFont('Arial', 'I', 8);
      $this->oPdf->Cell(80, $this->iAlturaLinha, ': ' . $dados[1] , 0, 1, "L", 0);
    }

    $this->oPdf->SetFont('Arial', 'B', 8);
    $this->oPdf->Cell(30, $this->iAlturaLinha, "ENDEREÇO"                                 ,  0, 0, "L", 0);
    $this->oPdf->SetFont('Arial', 'I', 8);
    $this->oPdf->Cell(80, $this->iAlturaLinha, ': ' . $sEndereco                          ,  0, 1, "L", 0);

    if ( $sOutrosDados1 != '' ) {
  
      $this->oPdf->SetFont('Arial', 'B', 8);
      $this->oPdf->Cell(30, $this->iAlturaLinha,        $sOutrosDados1, 0, 0, "L", 0);
      $this->oPdf->SetFont('Arial', 'I', 8);
      $this->oPdf->Cell(80, $this->iAlturaLinha, ': ' . $sOutrosDados2, 0, 1, "L", 0);
    }

    $this->oPdf->SetFont('Arial', 'BI', 12);
    $this->oPdf->Cell(191, 2, '', "B", 1, "R", 0);
    $this->oPdf->MultiCell(0, 20, "Valores Válidos Até a Data : " . db_formatar(date('Y-m-d'), 'd'), 0, "C", 0);
    $this->oPdf->SetLineWidth(0.2);
    $this->oPdf->SetFont('Arial', 'B', 8);    
  }   
  /**
   * Emitir Relatório
   */
  public function emitir() {

    $this->oPdf = new PDFDocument(PDFDocument::PRINT_PORTRAIT);
    $this->configurar();
    $this->dadosContribuinte();
    $aTiposDebitosSelecionados = explode(",", $this->getTiposDebitos());
    $aTipoDebitos = DBTributario::getTiposDebitoByOrigem($this->sTipoBusca, $this->sChavePesquisa);
    foreach ( $aTipoDebitos as $oTipoDebito ) {

      if (!in_array($oTipoDebito->k00_tipo, $aTiposDebitosSelecionados) ) {
        continue;
      }

      $this->processarDados($oTipoDebito);
    }     
    
    $this->oPdf->SetFont('Arial', 'B', 8);
    $this->oPdf->Cell(7,  $this->iAlturaLinha, "", 0, 0, "L", 0);
    $this->oPdf->Cell(68, $this->iAlturaLinha, "Total", 0, 0, "L", 0);
    $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($this->nValorHistorico, 'f'), 1, 0, "R", 0);
    $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($this->nValorCorrigido, 'f'), 1, 0, "R", 0);
    $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($this->nValorJuros    , 'f'), 1, 0, "R", 0);
    $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($this->nValorMulta    , 'f'), 1, 0, "R", 0);
    $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($this->nValorDesconto , 'f'), 1, 0, "R", 0);
    $this->oPdf->Cell(18, $this->iAlturaLinha, db_formatar($this->nValorTotal    , 'f'), 1, 1, "R", 0);

    $this->oPdf->Ln();
    if(count($aTipoDebitos) > count($aTiposDebitosSelecionados)) {

      $this->oPdf->SetFont('arial', 'B', 11);
      $this->oPdf->setx(17);
      $this->oPdf->Cell(195, 5, "*** EXISTEM MAIS DÉBITOS LANÇADOS QUE NÃO FORAM LISTADOS NESTE RELATÓRIO ***", 0, 1, "L", 1);
    }

    $this->oPdf->showPDF("RelatorioSinteticoArrecadar_" . time());
  } 
}