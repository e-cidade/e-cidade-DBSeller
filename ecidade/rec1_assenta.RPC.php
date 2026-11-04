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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("dbforms/db_funcoes.php"));

use ECidade\RecursosHumanos\RH\Assentamento\Repository\Assentamento as AssentamentoRespository;

define('MENSAGENS', 'recursoshumanos.rh.rec1_assenta.');

$oJson                = new services_json();
$oParametros          = $oJson->decode(str_replace("\\", "", $_POST["json"]));
$oRetorno             = new stdClass();
$oRetorno->iStatus    = 1;
$oRetorno->erro       = false;
$oRetorno->sMensagem  = '';
$aRegistros           = array();

try {

  $iCodigoAssentamento = null;
  if (!empty($oParametros->iCodigoAssentamento)){
    $iCodigoAssentamento = $oParametros->iCodigoAssentamento;
  }

  switch ($oParametros->sExec) {

    case 'getSaldoDiasDireito' :

      if( !isset( $oParametros->iCodigoPeriodoAquisitivo ) || empty($oParametros->iCodigoPeriodoAquisitivo) ){
        throw new BusinessException( _M( MENSAGENS . 'periodoaquisitivo_nao_informado' ) );
      }

      $iCodigoPeriodoAquisitivo  = $oParametros->iCodigoPeriodoAquisitivo;      
      $oDiasDireito              = PeriodoAquisitivoAssentamento::getSaldoDiasDireito($iCodigoPeriodoAquisitivo, $iCodigoAssentamento);
      
      $oRetorno->iDiasDireito    = $oDiasDireito->saldodiasdireito;
     
    break;

    case 'validaSaldoDiasDireito' :
      
      if (!isset($oParametros->iTipoAssentamento)) {
        throw new BusinessException( _M( MENSAGENS . 'assentamento_nao_informado' ) );
      }

      if( !isset( $oParametros->iCodigoPeriodoAquisitivo ) || empty($oParametros->iCodigoPeriodoAquisitivo) ){
        throw new BusinessException( _M( MENSAGENS . 'periodoaquisitivo_nao_informado' ) );
      }

      $iTipoAssentamento         = $oParametros->iTipoAssentamento;
      $iDias                     = $oParametros->iDias;
      $iCodigoPeriodoAquisitivo  = $oParametros->iCodigoPeriodoAquisitivo;
      $iSequencialAssentamento   = $oParametros->iSequencialAssentamento;
      $iDiasDireito              = 0;
      $iDiasDireito              = PeriodoAquisitivoAssentamento::validaSaldoDiasDireito($iCodigoPeriodoAquisitivo, 
                                                                                         $iTipoAssentamento, 
                                                                                         $iDias,
                                                                                         $iCodigoAssentamento,
                                                                                         $iSequencialAssentamento);

      if( $iDiasDireito == 0 ){
        throw new BusinessException( _M( MENSAGENS . 'periodoaquisitivo_sem_dias_direito' ) );
      }

      $oRetorno->lSaldo       = true;
    break;

    case 'getPeriodoAquisitivo':

      if (!isset($oParametros->iCodigoAssenta)) {
        throw new BusinessException( _M( MENSAGENS . 'assentamento_nao_informado' ) );
      }

      $oPeriodoAquisitivoAssentamento = PeriodoAquisitivoAssentamento::getPeriodoAquisitivoAssentamento( 
                             new Assentamento( $oParametros->iCodigoAssenta ), $oParametros->iMatriculaServidor);

      if (!$oPeriodoAquisitivoAssentamento) {
        return null;
      }

      $oRetorno->iCodigoPeriodoAquisitivo = $oPeriodoAquisitivoAssentamento->getPeriodoAquisitivo()->getCodigo();

    break;

    case 'validaPeriodoDiasDireito':

      $lValidaPeriodoDireito = PeriodoAquisitivoAssentamento::validaPeriodoGozo( $oParametros->iServidor,   
                                                                                 $oParametros->iTipoAssentamento, 
                                                                                 $oParametros->sDataInicial, 
                                                                                 $oParametros->sDataFinal,
                                                                                 $oParametros->iSequencialAssentamento );


    break;
    case 'validarUltimoAfastamentoServidorApiEsocial':
      $codigoServidor = $oParametros->codigoServidor;
      $dataInicioAfastamento = $oParametros->dataInicioAfastamento;
      $tipoAssentamento = $oParametros->tipoAssentamento;
      $codigoAssentamento = $oParametros->codigoAssentamento;

      $assentamentoRepo = AssentamentoRespository::getInstance();

      $oRetorno->isDataInicialAfastamentoValido = $assentamentoRepo->validarUltimoProtocoloAfastamentoByServidor($codigoServidor, $dataInicioAfastamento, $tipoAssentamento, $codigoAssentamento);

    break;

      case 'getDadosRetificacao':

          $assensamento = AssentamentoRepository::getInstanceByCodigo($oParametros->codigo_assenamento);
          $retificacao = $assensamento->getRetificacao();
          $stdRetificacao = new stdClass();
          $stdRetificacao->sequencial = '';
          $stdRetificacao->origem = '';
          $stdRetificacao->tipo   = '';
          $stdRetificacao->numero = '';
          if ($retificacao) {
              $stdRetificacao = $retificacao;
          }
          $oRetorno->retificacao = $stdRetificacao;

          break;

      case 'validarDadosAssentamento':

          $oRetorno->obrigarRetificacao = false;
          $tipoAssentamentoOrigem  = TipoAssentamentoRepository::getInstance()->getInstanciaPorTipo($oParametros->dadosAssentamento->origem);
          $tipoAssentamentoDestino = TipoAssentamentoRepository::getInstance()->getInstanciaPorTipo($oParametros->dadosAssentamento->destino);

          $daoAfastamentoEsocial = new cl_afastamentosesocial();
          $codigoTipoAssentamentoOrigem = $tipoAssentamentoOrigem->getSequencial();
          $sqlAfastamentoEsocial = $daoAfastamentoEsocial->sql_query_file(null, "eso08_sequencial", null, "eso08_tipoasse = {$codigoTipoAssentamentoOrigem}");
          $rsAfastamentoEsocial  = db_query($sqlAfastamentoEsocial);

          if (!$rsAfastamentoEsocial) {
              throw new DBException("Erro ao buscar o vínculo do tipo do assentamento com o eSocial.");
          }

          if (pg_num_rows($rsAfastamentoEsocial) == 0) {
            break;
          }

          $atributosOrigem = $tipoAssentamentoOrigem->getAtributosDinamicos();
          $atributoHiddenOrigem = null;
          foreach ($atributosOrigem as $atributo) {

              if ($atributo->tipoAtributo === 7) {
                  $atributoHiddenOrigem = $atributo;
              }
          }

          $atributosDestino = $tipoAssentamentoDestino->getAtributosDinamicos();
          $atributoHiddenDestino = null;
          foreach ($atributosDestino as $atributo) {

              if ($atributo->tipoAtributo === 7) {
                  $atributoHiddenDestino = $atributo;
              }
          }

          $motivoOrigem = "Acidente/Doença não relacionada ao trabalho";
          $motivoDestino = "Acidente/Doença do trabalho";

          if((int)$atributoHiddenOrigem->valorDefault == 1 ) {
              $motivoOrigem = "Acidente/Doença do trabalho";
              $motivoDestino = "Acidente/Doença não relacionada ao trabalho";
          }

          $mensagem  = "O tipo de assentamento {$tipoAssentamentoOrigem->getDescricao()} possui vínculo com o motivo do eSocial {$motivoOrigem} ";
          $mensagem .= "e já possui protocolo de envio. Este motivo só pode ser alterado para o motivo {$motivoDestino}.\n";
          $mensagem .= "Consulte a rotina DB:RECURSOSHUMANOS > eSocial > Procedimentos > Configuração > Vínculo de Afastamento.";

          $tiposValidar = array(1, 3);
          if (in_array((int)$atributoHiddenOrigem->valorDefault, $tiposValidar)) {

              if (is_null($atributoHiddenDestino)) {
                  throw new BusinessException($mensagem);
              }
          
              if (!in_array((int)$atributoHiddenDestino->valorDefault, $tiposValidar)) {
                  throw new BusinessException($mensagem);
              }

              $oRetorno->obrigarRetificacao = true;
          } else if ($oParametros->possuiProcessoEsocial ) {
              $mensagem  = "Este assentamento já foi enviado ao eSocial. O tipo do assentamento não pode ser alterado.";
              throw new Exception($mensagem);
          }

          break;
      case 'possuiProtocoloEsocial':

          $possuiProtocolo = false;
          try {
              if (!empty($oParametros->codigo_assentamento)) {
                  $assentamentoEsocial = AssentamentoRespository::getInstance();
                  if ($assentamentoEsocial->possuiConfiguracaoApi()) {
                      $possuiProtocolo = $assentamentoEsocial->possuiProtocoloByAfastamento($oParametros->codigo_assentamento);
                  }
              }

              $oRetorno->possuiProtocoloEsocial = $possuiProtocolo;
          } catch (Exception $oErro) {
              $oRetorno->sMensagem  = "Erro ao acessar o sistema do eSocial.\nNão foi possível verificar se assentamento selecionado foi enviado para o eSocial.";
              $oRetorno->erro  = true;
          }
          break;
      case 'verificarExistenciaAssentamentoPeriodo':
          $oRetorno->existeAssentamento = false;
          
          $oRetorno->assentamentos = AssentamentoRespository::getInstance()->buscaAssentamentosPeriodo(
              $oParametros->codigoServidor,
              $oParametros->tipoAssentamento,
              $oParametros->dataInicioAfastamento
          );

          if (count($oRetorno->assentamentos) > 0) {
              $oRetorno->existeAssentamento = true;
          }

          break;

      case 'buscarAssentamentosCompetencia':

          $classenta = new cl_assenta;
          $dbwhere   = "h16_regist = {$oParametros->matricula}";
          $campos = [
            'h12_assent', 
            'h12_descr', 
            'h16_dtconc', 
            'h16_dtterm', 
            'h16_quant', 
            'h16_nrport', 
            'h16_anoato', 
            'h16_atofic'
          ];

          $sql = $classenta->sql_query_tipo(
              null,
              implode(",", $campos),
              "h16_dtconc desc limit 10",
              $dbwhere
          );

          $result                  = db_query($sql);
          $assentamentos           = db_utils::getCollectionByRecord($result, false, false, true);
          $oRetorno->assentamentos = $assentamentos;

          break;
  }

} catch (Exception $oErro) {
  $oRetorno->iStatus   = 2;
  $oRetorno->erro      = true;
  $oRetorno->sMensagem = $oErro->getMessage();
}

$oRetorno->sMensagem = urlencode($oRetorno->sMensagem);
echo $oJson->encode($oRetorno);
