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

use App\Domain\Tributario\Arrecadacao\Repositories\NumprefRepository;
use ECidade\Tributario\Arrecadacao\Convenio as RegistroConvenio;

/**
 * Class convenio
 */
class convenio {

  const MODALIDADE_COBRANCA     = 1;
  const MODALIDADE_ARRECADACAO  = 2;
  const MODALIDADE_CAIXA_PADRAO = 3;

  const TIPO_CONVENIO_COMPENSACAO_BDL     = 1;
  const TIPO_CONVENIO_COMPENSACAO_BSJ     = 2;
  const TIPO_CONVENIO_ARRECADACAO         = 3;
  const TIPO_CONVENIO_CAIXA_PADRAO        = 4;
  const TIPO_CONVENIO_COMPENSACAO_SICOB   = 5;
  const TIPO_CONVENIO_COMPENSACAO_SIGCB   = 6;
  const TIPO_CONVENIO_COBRANCA_REGISTRADA = 7;

  private $iModalidadeConvenio  = null;
  private $iCodConvenio		      = null;
  private $iFormatoVenc 	      = null;
  private $sLinhaDigitavel 	    = "";
  private $sSegmento  		      = "";
  private $sCodigoBarra	 	      = "";
  private $iCodBanco	 	        = "";
  private $iCodAgencia	 	      = "";
  private $iDigAgencia          = "";
  private $sCarteira	 	        = "";
  private $sVariacao	 	        = "";
  private $sConvenioArrecadacao	= "";
  private $sConvenioCobranca	  = "";
  private $sCedente  		        = "";
  private $sNossoNumero			    = "";
  private $sCampoLivre			    = "";
  private $sTipoConvenio        = "";
  private $iCadTipoConvenio     = "";
  private $sDigitoCedente       = "";
  private $sOperacao            = "";
  private $sEspecie             = "";
  private $iCodigoAgencia       = null;
  private $oBanco               = null;
  private $custasHonorarios     = false;
  private $vencimento     = null;


  public function __construct($iCodConvenio="",$iNumpre,$iNumpar,$sValor,$sVlrbar,$dDataVenc,$iTercDig, $dataVencimento = null) {
    $this->custasHonorarios = (boolean) (new NumprefRepository())->buscaParametroCustasHonorarios();
    $oConvenio = new RegistroConvenio($iCodConvenio);

	  $this->iModalidadeConvenio  = $oConvenio->getModalidadeConvenio();
	  $this->iCodConvenio  		    = $oConvenio->getCodConvenio();
	  $this->sSegmento    		    = $oConvenio->getSegmento();
	  $this->iFormatoVenc 		    = $oConvenio->getFormatoVenc();
	  $this->sConvenioArrecadacao = $oConvenio->getConvenioArrecadacao();
   	$this->iCodBanco	 	        = $oConvenio->getCodBanco();
   	$this->sCarteira	 	        = $oConvenio->getCarteira();
   	$this->sVariacao	 	        = $oConvenio->getVariacao();
	  $this->sConvenioCobranca    = $oConvenio->getConvenioCobranca();
   	$this->sCedente  		        = $oConvenio->getCedente();
   	$this->sOperacao            = $oConvenio->getOperacao();
   	$this->iCadTipoConvenio     = $oConvenio->getTipoConvenio();
   	$this->iDigAgencia          = $oConvenio->getDigAgencia();
   	$this->sEspecie             = $oConvenio->getEspecie();
    $this->iCodigoAgencia       = $oConvenio->getCodigoAgencia();

	  $this->sTipoConvenio  = $oConvenio->getSiglaTipoConvenio();
 		$this->iCodAgencia    = $oConvenio->getCodAgencia();
    $this->sDigitoCedente = $oConvenio->getDigitoCedente();

		$this->geraLinhaBarra($iNumpre,$iNumpar,$sValor,$sVlrbar,$dDataVenc,$iTercDig, $dataVencimento);
  }


  private function geraLinhaBarra($iNumpre,$iNumpar,$sValor,$sVlrbar,$dDataVenc,$iTercDig,$dataVencimento = null) {
    if ($dataVencimento != null) {
      $dataSelecionada = new \DBDate($dDataVenc);
      $dataSelecionada = $dataSelecionada->convertTo(\DBDate::DATA_EN);
      $dataVencimento = new \DBDate($dataVencimento);
      $dataVencimento = $dataVencimento->convertTo(\DBDate::DATA_EN);
      $data = $dataVencimento >= $dataSelecionada ? $dataVencimento : $dataSelecionada;

      $dataVencimento = new \DBDate($data);
      $dDataVenc = $dataVencimento->convertTo(\DBDate::DATA_PTBR);
    }else{
      $data = $dDataVenc;
      $data = str_replace("/","",$data);
    }
    $sDataVencimento = str_replace("-","",$data);

    if ((strpos($dDataVenc, '-') === false) && (strpos($dDataVenc, '/') === false)) {
       $dDataVenc = substr($dDataVenc, 0, 4) . '-' . substr($dDataVenc, 4, 2) . '-' .substr($dDataVenc, 6, 2);
    }

    $this->vencimento = new \DBDate($dDataVenc);

    if ($this->iModalidadeConvenio == 1) {

      $sSqlFichaCompensacao  = "select * 																	                ";
      $sSqlFichaCompensacao .= "  from fc_fichacompensacao( {$this->iCodConvenio},			  ";
      $sSqlFichaCompensacao .= " 				             		    {$iNumpre},										";
      $sSqlFichaCompensacao .= " 					            	    {$iNumpar},										";
      $sSqlFichaCompensacao .= " 						               '{$dDataVenc}',					  		";
      $sSqlFichaCompensacao .= " 						                {$sValor})										";

      $rsFichaCompensacao    = db_query($sSqlFichaCompensacao);
      $oFichaCompensacao     = db_utils::fieldsMemory($rsFichaCompensacao,0);

      if ($oFichaCompensacao->erro == 'f') {

        $this->sCodigoBarra    = $oFichaCompensacao->codigobarras;
        $this->sLinhaDigitavel = $oFichaCompensacao->linhadigitavel;
        $this->sNossoNumero    = $oFichaCompensacao->nossonumero;
        $this->sCampoLivre	   = $oFichaCompensacao->campolivre;

      } else {
	      throw new Exception("Ficha Compensação: ".$oFichaCompensacao->mensagem);
      }
    } else if ($this->iModalidadeConvenio == 2) {

      if ($this->iFormatoVenc == 1) {
        $sVencBar	       = $sDataVencimento.'000000';
      } else if ($this->iFormatoVenc == 2) {

        $sDataVencimento = substr($sDataVencimento, 6, 2).substr($sDataVencimento, 4, 2).substr($sDataVencimento, 2, 2);
        $sVencBar  	     = $sDataVencimento.'00000000';
      }

      $sInibar          = "8".$this->sSegmento.$iTercDig;

      if ($this->custasHonorarios) {
        $iNumpreFormatado = db_numpre($iNumpre,0).db_formatar(0, 's', "0", 3, "e");
      } else {
        $iNumpreFormatado = db_numpre($iNumpre,0).db_formatar($iNumpar, 's', "0", 3, "e");
      }

      $sSqlFebraban     = " select fc_febraban('$sInibar'||'$sVlrbar'||'".$this->sConvenioArrecadacao."'||'".$sVencBar."'||'$iNumpreFormatado')";

      $rsFebraban   = db_query($sSqlFebraban);
      $oFebraban    = db_utils::fieldsMemory($rsFebraban,0);

      if ($oFebraban->fc_febraban == "") {
        throw new Exception("Erro ao gerar código de barras(2)");
      }

      $this->sCodigoBarra     = substr($oFebraban->fc_febraban,0,strpos($oFebraban->fc_febraban, ','));
      $this->sLinhaDigitavel  = substr($oFebraban->fc_febraban, strpos($oFebraban->fc_febraban, ',') + 1);
    } else {

      $this->sCodigoBarra     = str_pad($iNumpre, 8, '0', STR_PAD_LEFT).str_pad($iNumpar, 3, '0', STR_PAD_LEFT);
      $this->sLinhaDigitavel  = str_pad($iNumpre, 8, '0', STR_PAD_LEFT).str_pad($iNumpar, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Verificamos se o numpre é um numpre de recibo
     * Se a condição for verdadeira, inserimos registro do numpre, código de barra e linha digitavel na tabela recibocodbar
     */
    $sSqlRecibo  = "select 1                       ";
    $sSqlRecibo .= "  from recibopaga              ";
    $sSqlRecibo .= " where k00_numnov = {$iNumpre} ";
    $sSqlRecibo .= " union                         ";
    $sSqlRecibo .= "select 1                       ";
    $sSqlRecibo .= "  from recibo                  ";
    $sSqlRecibo .= " where k00_numpre = {$iNumpre} ";
    $rsRecibo    = db_query($sSqlRecibo);

    if (pg_num_rows($rsRecibo) > 0) {
      /**
       * Valida se existe a tabela
       */
      $rsVerificaExistenciaTabela = db_query("select * from pg_class where relname = 'recibocodbar' and relkind = 'r'");
      if (pg_num_rows($rsVerificaExistenciaTabela)>0) {

        $oDaoReciboCodBar         = db_utils::getDao("recibocodbar");
        /**
         * Valida se existem registros na tabela recibocodbar
         */
        $sSqlVerificaReciboCodBar = $oDaoReciboCodBar->sql_query_file( null,"1",null,"k00_numpre = {$iNumpre} or k00_codbar = '{$this->sCodigoBarra}' ");
        $rsVerificaReciboCodBar   = db_query($sSqlVerificaReciboCodBar);

        if (!$rsVerificaReciboCodBar){
          throw new Exception("Codigo de Barras: ".$oDaoReciboCodBar->erro_banco);
        }
        /**
         * Caso não exista tenta incluir
         */
        if (pg_num_rows($rsVerificaReciboCodBar) == 0) {

          $oDaoReciboCodBar->k00_numpre          = $iNumpre;
          $oDaoReciboCodBar->k00_codbar          = $this->sCodigoBarra;
          $oDaoReciboCodBar->k00_linhadigitavel  = $this->sLinhaDigitavel;
          $oDaoReciboCodBar->k00_nossonumero     = $this->sNossoNumero;
          $oDaoReciboCodBar->incluir($iNumpre);
          /**
           * Não conseguindo incluir dispara erro
           */
          if ($oDaoReciboCodBar->erro_status == "0") {

            $sMsgErro  = "Erro ao incluir registros do numpre na tabela recibocodbar.\\n\\n";
            $sMsgErro .= "Erro da Classe:\\n";
            $sMsgErro .= "{$oDaoReciboCodBar->erro_msg}";
            throw new Exception($sMsgErro);
          }
        }
      }
    }
  }


  public function getImagemBanco(){

    $sSqlBanco  = " select  * 						 			                  ";
    $sSqlBanco .= "   from db_bancos							                ";
	  $sSqlBanco .= "  where db90_codban = '{$this->getCodBanco()}' ";

    $rsBanco    	   = db_query($sSqlBanco);
    $iNroLinhasBanco = pg_num_rows($rsBanco);

    if($iNroLinhasBanco > 0 ){

      $oBanco = db_utils::fieldsMemory($rsBanco,0);

      $this->oBanco = $oBanco;

      if($oBanco->db90_digban=="" || $oBanco->db90_abrev=="" || $oBanco->db90_logo=="")	{
     	  throw new Exception("Configure o banco no Cadastro de Bancos!");
   	  }

      global $conn;

      //Ajustado transações para poder gerar recibo a partir da API do Laravel
      if (is_null($conn)) {
          \DB::beginTransaction();
      } else {
          db_query($conn, "begin");
      }


  	  $sCaminho = "tmp/".$this->getCodBanco().".jpg";
	    pg_lo_export  ( "$oBanco->db90_logo",$sCaminho);

        if (is_null($conn)) {
            \DB::commit();
        } else {
            db_query($conn, "commit");
        }

	    return $sCaminho;

  	} else {
  	  throw new Exception("Não existe Banco cadastrado para o código {$this->getCodBanco()}!");
    }

  }

  public function getImagemBancoRecibo(){

    $sSqlBanco  = " select  * 						 			                  ";
    $sSqlBanco .= "   from db_bancos							                ";
	  $sSqlBanco .= "  where db90_codban = '{$this->getCodBanco()}' ";

    $rsBanco    	   = db_query($sSqlBanco);
    $iNroLinhasBanco = pg_num_rows($rsBanco);

    if($iNroLinhasBanco > 0 ){

      $oBanco = db_utils::fieldsMemory($rsBanco,0);

      $this->oBanco = $oBanco;

      if($oBanco->db90_digban=="" || $oBanco->db90_abrev=="" || $oBanco->db90_logo=="")	{
     	  throw new Exception("Configure o banco no Cadastro de Bancos!");
   	  }

  	  $sCaminho = "tmp/".$this->getCodBanco().".jpg";
	    pg_lo_export  ( "$oBanco->db90_logo",$sCaminho);

	    return $sCaminho;

  	} else {
  	  throw new Exception("Não existe Banco cadastrado para o código {$this->getCodBanco()}!");
    }
  }

  public function getDigitoAgencia(){
  	return $this->iDigAgencia;
  }

  public function getDigitoCedente(){
    return $this->sDigitoCedente;
  }

  public function getOperacao(){
  	return $this->sOperacao;
  }

  public function getLinhaDigitavel(){
    return $this->sLinhaDigitavel;
  }

  public function getCodigoBarra(){
  	return $this->sCodigoBarra;
  }

  public function getCodBanco(){
  	return $this->iCodBanco;
  }

  public function getCodAgencia(){
  	return $this->iCodAgencia;
  }

  public function getCarteira(){
	  return $this->sCarteira."-".str_pad($this->sVariacao.db_CalculaDV($this->sVariacao, 11),4,"0",STR_PAD_LEFT);
  }

  public function getCodigoCarteira(){
    return $this->sCarteira;
  }

  public function getCedente(){
  	return $this->sCedente;
  }

  public function getConvenioCobranca(){
  	return $this->sConvenioCobranca;
  }

  public function getConvenioArrecadacao(){
  	return $this->sConvenioArrecadacao;
  }

  public function getNossoNumero(){
  	return $this->sNossoNumero;
  }

  public function getCampoLivre(){
  	return $this->sCampoLivre;
  }

  public function getTipoConvenio(){
  	return $this->sTipoConvenio;
  }

  public function getiCadTipoConvenio(){
  	return $this->iCadTipoConvenio;
  }

  public function getAgenciaCedente(){
    switch ($this->iCadTipoConvenio) {
    	case 5:
    	  $sCedente        = $this->getOperacao().$this->getCedente()."-".$this->getDigitoCedente();
        $sAgenciaCedente = $this->getCodAgencia()."/".$sCedente;
    	break;
      case 6:
      case regraEmissao::getConveioCustaBoleto():
        $sCedente        = $this->getCedente()."-".$this->getDigitoCedente();
        $sAgenciaCedente = $this->getCodAgencia()."/".$sCedente;
      break;
    	default:
        $sCedente        = substr($this->getCedente(),0,strlen($this->getCedente())-1)."-". substr($this->getCedente(),strlen($this->getCedente())-1,1);
        $sAgenciaCedente = $this->getCodAgencia()."/".$sCedente;
    	break;
    }

    return $sAgenciaCedente;

  }

  /**
   * Retorna Espécie do documento gerado.
   * @return string - Especie do documento
   */
  public function getEspecieDocumento() {
    return $this->sEspecie;
  }

  /**
   * Retorna o código da agência sem o digito verificador
   * @return integer
   */
  public function getCodigoAgencia() {
    return $this->iCodigoAgencia;
  }

  /**
   * Retorna os dados do banco gerados na consulta do método getImagemBanco
   * @return stdClass
   */
  public function getBanco()
  {
      return $this->oBanco;
  }


    /**
     * @return null|DBDate
     */
    public function getVencimento()
    {
        return $this->vencimento;
    }
}
