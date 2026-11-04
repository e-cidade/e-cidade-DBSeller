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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_POST_VARS);

$oDaoAssociadotiposservicos = new cl_associadotiposservicos;
$db_opcao    = 1;
$db_botao    = true;
$sPosScripts = "";

if (isset($incluir)) {

  $fm09_valor = preg_replace("/[^0-9]/", "", $fm09_valor);
  $fm09_valor = ($fm09_valor / 100);

  if (!isset($fm09_copart_financeiro)) {
     $fm09_copart_financeiro = 'false';
  }

  if (!isset($fm09_copart_percentual)) {
     $fm09_copart_percentual = 'false';
  }

  db_inicio_transacao();
  $oDaoAssociadotiposservicos->fm09_descricao = $fm09_descricao;
  $oDaoAssociadotiposservicos->fm09_copart_percentual = $fm09_copart_percentual;
  $oDaoAssociadotiposservicos->fm09_copart_financeiro = $fm09_copart_financeiro;
  $oDaoAssociadotiposservicos->fm09_valor = $fm09_valor;
  $oDaoAssociadotiposservicos->incluir($fm09_codigo);
  db_fim_transacao();

  $sPosScripts .= 'alert("' . $oDaoAssociadotiposservicos->erro_msg . '");' . "\n";

  if ($oDaoAssociadotiposservicos->erro_status == '0') {

    $db_botao = true;
    $sPosScripts .= "document.form1.db_opcao.disabled = false;\n";
    $sPosScripts .= "document.form1.fm09_valor.value = '';\n";

    if ($oDaoAssociadotiposservicos->erro_campo != "") {
      $sPosScripts .= "document.form1.{$oDaoAssociadotiposservicos->erro_campo}.classList.add('form-error');\n";
      $sPosScripts .= "document.form1.{$oDaoAssociadotiposservicos->erro_campo}.focus();\n";
    }
  } else {
    $sPosScripts .= "location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "';\n";
  }
}

$sPosScripts .=  'js_tabulacaoforms("form1", "fm09_descricao", true, 1, "fm09_descricao", true);';

include(modification("forms/db_frmassociadotiposservicos.php"));
