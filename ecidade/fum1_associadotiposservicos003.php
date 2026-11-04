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

parse_str($_SERVER['QUERY_STRING'], $queryString);
db_postmemory($HTTP_POST_VARS);

$oDaoAssociadotiposservicos = new cl_associadotiposservicos;
$db_botao    = false;
$db_opcao    = 33;
$sPosScripts = "";

if (isset($excluir)) {

  db_inicio_transacao();
  $db_opcao = 3;
  $oDaoAssociadotiposservicos->excluir($fm09_codigo);
  db_fim_transacao();

  $sPosScripts .= 'alert("' . $oDaoAssociadotiposservicos->erro_msg . '");' . "\n";

  if ($oDaoAssociadotiposservicos->erro_status != "0") {
    $sPosScripts .= "location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "';\n";
  }

} else if(isset($chavepesquisa)) {

  $db_opcao = 3;
  $db_botao = true;
  $result   = $oDaoAssociadotiposservicos->sql_record( $oDaoAssociadotiposservicos->sql_query($chavepesquisa) );
  db_fieldsmemory($result, 0);
}

if ($db_opcao == 33) {
  $sPosScripts .= "document.form1.pesquisar.click();";
}

$sPosScripts .=  'js_tabulacaoforms("form1", "fm09_descricao", true, 1, "fm09_descricao", true);';

include(modification("forms/db_frmassociadotiposservicos.php"));
