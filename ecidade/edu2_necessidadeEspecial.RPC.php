<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2013  DBselller Servicos de Informatica             
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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));

$oJson       = new services_json();
$oParam      = $oJson->decode(str_replace("\\","",$_POST["json"]));
$oRetorno    = new stdClass();
$oRetorno->status = 1;
$oRetorno->erro = false;
$oRetorno->message = '';

switch($oParam->exec) {
  case 'pesquisaTipoAtendimentoAluno':
    $aTipoAtendimentoAluno = array();
    $aWhere = array();
    $sCampos = "*";
    $sOrder = (isset($oParam->order) && !empty($oParam->order))?$oParam->order:'ed188_sequencial';

    if(!empty($oParam->aluno)) {
      $aWhere[] = 'ed188_aluno = '.$oParam->aluno;
    }

    $oTipoAtendimentoAluno = new cl_necessidadetipoatendimentoaluno;
    $sWhere = implode(' and ', $aWhere);
    $sSql = $oTipoAtendimentoAluno->sql_query('',$sCampos, $sOrder, $sWhere);
    $rTipoAtendimentoAluno = $oTipoAtendimentoAluno->sql_record($sSql);

    if($oTipoAtendimentoAluno->numrows > 0) {
      for($i = 0; $i < $oTipoAtendimentoAluno->numrows; $i++) {
        $tipoAtendimentoAluno = db_utils::fieldsMemory($rTipoAtendimentoAluno, $i);
        $newTipoAtendimentoAluno = new stdClass();
        $newTipoAtendimentoAluno->sequencial = $tipoAtendimentoAluno->ed188_sequencial;
        $newTipoAtendimentoAluno->tipoatendimento = $tipoAtendimentoAluno->ed188_necessidadetipoatendimento;
        $newTipoAtendimentoAluno->aluno = $tipoAtendimentoAluno->ed188_aluno;
        $aTipoAtendimentoAluno[] = $newTipoAtendimentoAluno;
      }

      $oRetorno->tiposAtendimentos = $aTipoAtendimentoAluno;
    } else {
      $oRetorno->status  = 0;
      $oRetorno->message = urlencode('Nenhum Tipo de Atendimento Encontrado.');
      $oRetorno->erro = true;
    }

    echo $oJson->encode($oRetorno);
  break;

  case 'pesquisaTipoAtendimento':

    $aTipoAtendimento = array();
    $aWhere = array();
    $sCampos = "ed186_sequencial,ed186_descricao";
    $sOrder = (isset($oParam->order) && !empty($oParam->order))?$oParam->order:'ed186_sequencial';

    $oTipoAtendimento = new cl_necessidadetipoatendimento;
    $sWhere = implode(' and ', $aWhere);
    $sSql = $oTipoAtendimento->sql_query('',$sCampos, $sOrder, $sWhere);
    $rTipoAtendimento = $oTipoAtendimento->sql_record($sSql);

    if($oTipoAtendimento->numrows > 0) {
      for($i = 0; $i < $oTipoAtendimento->numrows; $i++) {
        $tipoAtendimento = db_utils::fieldsMemory($rTipoAtendimento, $i);
        $newTipoAtendimento = new stdClass();
        $newTipoAtendimento->sequencial = $tipoAtendimento->ed186_sequencial;
        $newTipoAtendimento->descricao = urlencode($tipoAtendimento->ed186_descricao);
        $aTipoAtendimento[] = $newTipoAtendimento;
      }

      $oRetorno->tiposAtendimentos = $aTipoAtendimento;
    } else {
      $oRetorno->status  = 0;
      $oRetorno->message = urlencode('Nenhum Tipo de Atendimento Encontrado.');
      $oRetorno->erro = true;
    }

    echo $oJson->encode($oRetorno);
  break;

    case 'pesquisaSubdivisao':

    $aSubdivisoes = array();
    $aWhere = array();
    
    $sCampos = "*";
    $aWhere[] = "ed48_i_codigo = {$oParam->necessidade}";
    $sOrder = (isset($oParam->order) && !empty($oParam->order))?$oParam->order:'ed185_sequencial';
    
    $oNecessidadeSubdivisao = new cl_necessidadesubdivisao;
    $sWhere = implode(' and ', $aWhere);
    $sSql = $oNecessidadeSubdivisao->sql_query('',$sCampos, $sOrder, $sWhere);
    $rSubdivisao = $oNecessidadeSubdivisao->sql_record($sSql);

    if($oNecessidadeSubdivisao->numrows > 0) {
      for($i = 0; $i < $oNecessidadeSubdivisao->numrows; $i++) {
        $subdivisao = db_utils::fieldsMemory($rSubdivisao, $i);
        $newSubdivisao = new stdClass();
        $newSubdivisao->sequencial = $subdivisao->ed185_sequencial;
        $newSubdivisao->necessidade = $subdivisao->ed185_necessidade;
        $newSubdivisao->descricao = urlencode($subdivisao->ed185_descricao);
        $aSubdivisoes[] = $newSubdivisao;
      }

      $oRetorno->subdivisoes = $aSubdivisoes;
    } else {
      $oRetorno->status  = 0;
      $oRetorno->message = urlencode('Nenhuma Subdivisão Encontrado.');
      $oRetorno->erro = true;
    }

    echo $oJson->encode($oRetorno);
  break;

    case 'pesquisaSubdivisaoAluno':

    $aSubdivisoesAluno = array();
    $aWhere = array();
    $sCampos = "*";
    $sOrder = (isset($oParam->order) && !empty($oParam->order))?$oParam->order:'ed187_aluno';
    
    if(!empty($oParam->where)) {
      $aWhere[] = $oParam->where;
    }

    if(!empty($oParam->necessidade)) {
      $aWhere[] = 'necessidadesubdivisao.ed185_necessidade = '.$oParam->necessidade;
    }

    if(!empty($oParam->aluno)) {
      $aWhere[] = 'ed187_aluno = '.$oParam->aluno;
    }

    if(!empty($oParam->order)) {
      $sOrder = $oParam->order;
    }

    $oNecessidadeSubdivisaoaluno = new cl_necessidadesubdivisaoaluno;
    $sWhere = implode(' and ', $aWhere);
    $sSql = $oNecessidadeSubdivisaoaluno->sql_query('',$sCampos, $sOrder, $sWhere);
    $rSubdivisao = $oNecessidadeSubdivisaoaluno->sql_record($sSql);

    if($oNecessidadeSubdivisaoaluno->numrows > 0) {
      for($i = 0; $i < $oNecessidadeSubdivisaoaluno->numrows; $i++) {
        $oSubdivisaoAluno = db_utils::fieldsMemory($rSubdivisao, $i);
        $newSubdivisaoAluno = new stdClass();
        $newSubdivisaoAluno->sequencial = $oSubdivisaoAluno->ed187_sequencial;
        $newSubdivisaoAluno->necessidade = $oSubdivisaoAluno->ed185_necessidade;
        $newSubdivisaoAluno->aluno = $oSubdivisaoAluno->ed187_aluno;
        $newSubdivisaoAluno->subdivisao = $oSubdivisaoAluno->ed185_sequencial;
        $newSubdivisaoAluno->descricao = urlencode($oSubdivisaoAluno->ed185_descricao);
        $aSubdivisoesAluno[] = $newSubdivisaoAluno;
      }

      $oRetorno->subdivisoes = $aSubdivisoesAluno;
    } else {
      $oRetorno->status  = 0;
      $oRetorno->message = urlencode('Nenhuma Registro Encontrado.');
      $oRetorno->erro = true;
    }

    echo $oJson->encode($oRetorno);
  break;

}
