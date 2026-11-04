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
require_once(modification("libs/JSON.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("model/Acordo.model.php"));
require_once(modification("model/CgmBase.model.php"));
require_once(modification("model/CgmFactory.model.php"));
require_once(modification("model/CgmFisico.model.php"));
require_once(modification("model/CgmJuridico.model.php"));
require_once(modification("model/AcordoHomologacao.model.php"));
require_once(modification("model/AcordoComissao.model.php"));
require_once(modification("model/AcordoComissaoMembro.model.php"));
require_once(modification("model/AcordoAssinatura.model.php"));
require_once(modification("model/AcordoRescisao.model.php"));
require_once(modification("model/AcordoAnulacao.model.php"));
require_once(modification("model/AcordoPosicao.model.php"));
require_once(modification("model/AcordoItem.model.php"));
require_once(modification("model/Dotacao.model.php"));
require_once(modification("model/MaterialCompras.model.php"));

$oJson    = new services_json();
$oRetorno = new stdClass();
$oParam   = $oJson->decode(db_stdClass::db_stripTagsJson(str_replace("\\", "", $_POST["json"])));

$oRetorno->status  = 1;

if (isset($oParam->observacao)) {
	$sObservacao = utf8_decode($oParam->observacao);
}

switch($oParam->exec) {
    
  /*
   * Pesquisa homologaчуo para o contrato
   */
  case "getDadosHomologacao":
      
      try {
        
        $oHomologacao        = new AcordoHomologacao($oParam->codigo);
        $oAcordo             = new Acordo($oHomologacao->getAcordo());
        $oRetorno->codigo    = $oHomologacao->getCodigo();
        $oRetorno->acordo    = $oAcordo->getCodigoAcordo();
        $oRetorno->descricao = urlencode($oAcordo->getResumoObjeto());

      } catch (Exception $eExeption){
        
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n", "\n", $eExeption->getMessage()));
      }
      
      break;
	
  /*
   * Incluir homologaчуo para o contrato
   */
  case "homologarContrato":
      
      try {
        
        db_inicio_transacao();
  
        $oHomologacao = new AcordoHomologacao();
        $oHomologacao->setAcordo($oParam->acordo);
        $oHomologacao->setObservacao(db_stdClass::normalizeStringJsonEscapeString($sObservacao));
        $oHomologacao->save();
        
        db_fim_transacao(false);
      } catch (Exception $eExeption){
        
        db_fim_transacao(true);
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n", "\n", $eExeption->getMessage()));
      }
      
      break;  
      
  /*
   * Cancelar homologaчуo para o contrato
   */
  case "cancelarHomologacao":
      
      try {
        
        db_inicio_transacao();
 
        $oHomologacao = new AcordoHomologacao($oParam->codigo);
        $oHomologacao->setObservacao(db_stdClass::normalizeStringJsonEscapeString($sObservacao));
        $oHomologacao->cancelar();
        
        db_fim_transacao(false);
      } catch (Exception $eExeption){
        
        db_fim_transacao(true);
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n", "\n", $eExeption->getMessage()));
      }
      
      break;
      
  /*
   * Pesquisa dados da assinatura
   */
  case "getDadosAssinatura":
      
      try {

        $oAssinatura             = new AcordoAssinatura($oParam->codigo);
        $oAcordo                 = new Acordo($oAssinatura->getAcordo());
        $oRetorno->codigo        = $oAssinatura->getCodigo();
        $oRetorno->acordo        = $oAcordo->getCodigoAcordo();
        $oRetorno->datamovimento = date("Y-m-d",db_getsession("DB_datausu"));
        $oRetorno->descricao     = urlencode($oAcordo->getResumoObjeto());
        
      } catch (Exception $eExeption){
        
        db_fim_transacao(true);
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n", "\n", $eExeption->getMessage()));
      }
      
      break;

  /*
   * Incluir assinatura para o contrato
   */
  case "assinarContrato":
      
      try {
        
        db_inicio_transacao();

      	$oAssinatura = new AcordoAssinatura();
      	$oAssinatura->setAcordo($oParam->acordo);
      	$dtMovimento = implode("-", array_reverse(explode("/", $oParam->dtmovimentacao)));
      	$oAssinatura->setDataMovimento($dtMovimento);
      	$oAssinatura->setObservacao(db_stdClass::normalizeStringJsonEscapeString($sObservacao));
      	$oAssinatura->save();
      	
        db_fim_transacao(false);
        
      } catch (Exception $eExeption){
        
        db_fim_transacao(true);
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n","\n",$eExeption->getMessage()));
      }
      
      break;
      
  /*
   * Cancelamento da assinatura para o contrato
   */
  case "cancelarAssinatura":
      
      try {
        
        db_inicio_transacao();
        
        $oAssinatura = new AcordoAssinatura($oParam->codigo);
        $oAssinatura->setDataMovimento();
        $oAssinatura->setObservacao(db_stdClass::normalizeStringJsonEscapeString($sObservacao));
        $oAssinatura->cancelar();
        
        db_fim_transacao(false);
      } catch (Exception $eExeption){
        
        db_fim_transacao(true);
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n", "\n", $eExeption->getMessage()));
      }
      
      break;
      
  /*
   * Pesquisa recisуo para o contrato
   */
  case "getDadosRescisao":
      
      try {

        $oRecisao                = new AcordoRescisao($oParam->codigo);
        $oAcordo                 = new Acordo($oRecisao->getAcordo());
        $oRetorno->codigo        = $oRecisao->getCodigo();
        $oRetorno->acordo        = $oAcordo->getCodigoAcordo();
        $oRetorno->datamovimento = date("Y-m-d",db_getsession("DB_datausu"));
        $oRetorno->descricao     = urlencode($oAcordo->getResumoObjeto());
        
      } catch (Exception $eExeption){
        
        db_fim_transacao(true);
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n", "\n", $eExeption->getMessage()));
      }
      
      break;
      
  /*
   * Incluir recisуo para o contrato
   */
  case "rescindirContrato":
      
      try {
        
        db_inicio_transacao();

        $oRecisao = new AcordoRescisao();
        $oRecisao->setAcordo($oParam->acordo);
        $dtMovimento = implode("-", array_reverse(explode("/", $oParam->dtmovimentacao)));
        $oRecisao->setDataMovimento($dtMovimento);
        $oRecisao->setObservacao(db_stdClass::normalizeStringJsonEscapeString($sObservacao));
        $oRecisao->save();
        
        db_fim_transacao(false);
        
      } catch (Exception $eExeption){
        
        db_fim_transacao(true);
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n","\n",$eExeption->getMessage()));
      }
      
      break;

  /*
   * Cancelamento de recisуo para o contrato
   */
  case "cancelarRescisao":
      
      try {
        
        db_inicio_transacao();
        
        $oRecisao = new AcordoRescisao($oParam->codigo);
        $oRecisao->setDataMovimento();
        $oRecisao->setObservacao(db_stdClass::normalizeStringJsonEscapeString($sObservacao));
        $oRecisao->cancelar();
        
        db_fim_transacao(false);
      } catch (Exception $eExeption){
        
        db_fim_transacao(true);
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n", "\n", $eExeption->getMessage()));
      }
      
      break;
      
  /*
   * Cancela cancelamento de recisуo para o contrato
   */
  case "desfazerCancelarRecisao":
      
      try {
        
        db_inicio_transacao();
        
        $oRecisao = new AcordoRescisao($oParam->codigo);
        $oRecisao->setObservacao(db_stdClass::normalizeStringJsonEscapeString($sObservacao));
        $oRecisao->setDataMovimento();
        $oRecisao->desfazerCancelamento();
        
        db_fim_transacao(false);
      } catch (Exception $eExeption){
        
        db_fim_transacao(true);
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n", "\n", $eExeption->getMessage()));
      }
      
      break;
      
  /*
   * Pesquisa anulaчуo de contrato
   */
  case "getDadosAnulacao":
      
      try {
        
        $oAnulacao = new AcordoAnulacao($oParam->codigo);
      	$oAcordo   = new Acordo($oAnulacao->getAcordo());
        $oRetorno->codigo        = $oAnulacao->getCodigo();
        $oRetorno->acordo        = $oAcordo->getCodigoAcordo();
        $oRetorno->datamovimento = date("Y-m-d",db_getsession("DB_datausu"));
        $oRetorno->descricao     = urlencode($oAcordo->getResumoObjeto());
        
      } catch (Exception $eExeption){
        
        db_fim_transacao(true);
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n", "\n", $eExeption->getMessage()));
      }
      
      break;
      
  /*
   * Incluir anulaчуo de contrato
   */
  case "anularContrato":
      
      try {
        
        db_inicio_transacao();
        
        $oAnulacao = new AcordoAnulacao();
        $oAnulacao->setAcordo($oParam->acordo);
        $dtMovimento = implode("-", array_reverse(explode("/", $oParam->dtmovimentacao)));
        $oAnulacao->setDataMovimento($dtMovimento);
        $oAnulacao->setObservacao(db_stdClass::normalizeStringJsonEscapeString($sObservacao));
        $oAnulacao->save();
        
        db_fim_transacao(false);
      } catch (Exception $eExeption){
        
        db_fim_transacao(true);
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n", "\n", $eExeption->getMessage()));
      }
      
      break;
      
  /*
   * Cancelamento de anulaчуo de contrato
   */
  case "cancelarAnulacao":
      
      try {
        
        db_inicio_transacao();
        
        $oAnulacao = new AcordoAnulacao($oParam->codigo);
        $oAnulacao->setDataMovimento();
        $oAnulacao->setObservacao(db_stdClass::normalizeStringJsonEscapeString($sObservacao));
        $oAnulacao->cancelar();
        
        db_fim_transacao(false);
      } catch (Exception $eExeption){
        
        db_fim_transacao(true);
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n", "\n", $eExeption->getMessage()));
      }
      
      break;
      
  /*
   * Cancela cancelamento de anulaчуo de contrato
   */
  case "desfazerCancelarAnulacao":
      
      try {
        
        db_inicio_transacao();
        
        $oAnulacao = new AcordoAnulacao($oParam->codigo);
        $oAnulacao->setObservacao(db_stdClass::normalizeStringJsonEscapeString($sObservacao));
        $oAnulacao->setDataMovimento();
        $oAnulacao->desfazerCancelamento();
        
        db_fim_transacao(false);
      } catch (Exception $eExeption){
        
        db_fim_transacao(true);
        $oRetorno->status = 2;
        $oRetorno->erro   = urlencode(str_replace("\\n", "\n", $eExeption->getMessage()));
      }
      
      break;
}

echo $oJson->encode($oRetorno);   
?>