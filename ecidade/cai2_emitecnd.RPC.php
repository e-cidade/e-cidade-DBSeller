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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("classes/db_certidao_classe.php"));
require_once(modification("classes/db_db_certidaoweb_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_app.utils.php"));

define('MENSAGENS', 'tributario.arrecadacao.cai2_emitecnd.');

$oJson               = new services_json();
$oParametros         = $oJson->decode(str_replace("\\","",$_POST["json"]));

$oRetorno            = new stdClass();
$oRetorno->iStatus   = 1;
$oRetorno->sMensagem = '';

$lErro               = false;
$sMsg                = '';
$dtHoje              = date("Y-m-d", db_getsession("DB_datausu"));

db_app::import('exceptions.*');

try {
	
  switch ($oParametros->sExec) {
    
  	/**
  	 * Retorna certidões do cgm
  	 */
    case 'getCertidoes' :
      
    	if( !isset( $oParametros->sOrigem ) ){
    		throw new BusinessException(_M( MENSAGENS . 'origem_nao_informada'));
    	}
    	 
    	$oDaoCertidao     = new cl_certidao();
    	$sSqlCertidao  = $oDaoCertidao->sql_query_certidao_prazos( $oParametros->sOrigem, $oParametros->iCodigoOrigem );
    	$rsDAOCertidao = db_query( $sSqlCertidao );

    	if(!$rsDAOCertidao){
    		 
    		$oMensagem = (object)array('sErro'=>pg_last_error());
    		throw new DBException( _M( MENSAGENS . 'erro_buscar_dados_certidao') );
    	}
    	 
    	$oRetorno->aCertidoes = db_utils::getCollectionByRecord($rsDAOCertidao, false, false, true);
    	
    break;
      
    /**
     * Retorna o arquivo da certidão
     */
    case 'getCertidao' :
  
    	db_inicio_transacao();
    	
    	if( !db_utils::inTransaction() ){
    		throw new DBException(_M( MENSAGENS . 'sem_transacao_ativa'));
    	}
    	
    	if( !isset( $oParametros->iCertidaoSequencial ) ){
    		throw new BusinessException(_M( MENSAGENS . 'codigo_certidao_nao_informada'));
    	}
    	
    	$oDaoCertidao  = new cl_certidao();
    	$sSqlCertidao  = $oDaoCertidao->sql_query_file( $oParametros->iCertidaoSequencial,'p50_arquivo' );
    	$rsDAOCertidao = db_query( $sSqlCertidao );
    	
    	if(!$rsDAOCertidao){
    	
    		$oMensagem = (object)array('sErro'=>pg_last_error());
    		throw new DBException( _M( MENSAGENS . 'erro_buscar_dados_certidao') );
    	}
    	
    	$aRegistros = db_utils::getCollectionByRecord($rsDAOCertidao, false, false, true);
    	
    	foreach ($aRegistros as $aRegistro){
    		
    		if( $aRegistro->p50_arquivo == '0' ){
    		  throw new BusinessException(_M( MENSAGENS . 'arquivo_certidao_nao_encontrado'));
    		}
    	}
    	
    	$sArquivo 				= "tmp/certidao_" .  $oParametros->iCertidaoSequencial . '.pdf';
    	$lReemitiuArquivo = DBLargeObject::leitura( $aRegistro->p50_arquivo, $sArquivo );
    	
    	if( !$lReemitiuArquivo ){
    		throw new BusinessException(_M( MENSAGENS . 'erro_buscar_arquivo_certidao'));
    	}
    	
    	db_fim_transacao();
    	$oRetorno->sArquivo  = $sArquivo; 
    	echo $oJson->encode($oRetorno);
    	exit;
    	
    break;
      /**
       * Valida se existe template configurado
       */
    case 'getValidaTemplate' :

        /**
         * Seta a coluna de acordo com o tipo de certidão
         * 0 - Regular
         * 1 - Positiva
         * Qualquer diferente - Negativa
        */

        if ($oParametros->iTipo == 1) {
            if ($oParametros->sTitulo == "CGM") {
                $sCampo = "k03_templatecertidaopositiva_cgm";
            } else {
                if ($oParametros->sTitulo == "MATRICULA") {
                    $sCampo = "k03_templatecertidaopositiva_matric";
                } else {
                    $sCampo = "k03_templatecertidaopositiva_inscr";
                }
            }
        } else {
            if ($oParametros->iTipo == 0) {
                if ($oParametros->sTitulo == "CGM") {
                    $sCampo = "k03_templatecertidao_cgm";
                } else {
                    if ($oParametros->sTitulo == "MATRICULA") {
                        $sCampo = "k03_templatecertidao_matric";
                    } else {
                        $sCampo = "k03_templatecertidao_inscr";
                    }
                }
            } else {
                if ($oParametros->sTitulo == "CGM") {
                    $sCampo = "k03_templatecertidaonegativa_cgm";
                } else {
                    if ($oParametros->sTitulo == "MATRICULA") {
                        $sCampo = "k03_templatecertidaonegativa_matric";
                    } else {
                        $sCampo = "k03_templatecertidaonegativa_inscr";
                    }
                }
            }
        }

        $sSql = "SELECT 1 
                   FROM numpref
                  WHERE numpref.k03_anousu = " . db_getsession("DB_anousu") . "
                    AND numpref.k03_instit = " . db_getsession("DB_instit") . "
                    AND {$sCampo} IS NOT NULL
                    AND (SELECT COUNT(*)
                           FROM db_documentotemplate dt
                          WHERE dt.db82_sequencial   = numpref.{$sCampo}
                            AND dt.db82_templatetipo = 57) > 0;";

        $oDaoTemplate = new cl_numpref();
        $sSqlTemplate = $oDaoTemplate->sql_record($sSql);

        $oRetorno->bValidaTemplate = true;
        $oRetorno->erro            = "";

        if (!$sSqlTemplate) {
            $oRetorno->bValidaTemplate = false;
            $oRetorno->erro            = 2;
        }

    break;
  }
    
} catch (Exception $oErro) {

  $oRetorno->iStatus  = 2;
  $oRetorno->sMensagem = $oErro->getMessage();
}
  
  $oRetorno->sMensagem = urlencode( $oRetorno->sMensagem );
  echo $oJson->encode( $oRetorno );
