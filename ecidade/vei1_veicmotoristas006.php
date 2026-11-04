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
require_once(modification("classes/db_veicmotoristas_classe.php"));
require_once(modification("classes/db_veicparam_classe.php"));
require_once(modification("classes/db_veicmotoristascentral_classe.php"));

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);

$clveicmotoristas        = new cl_veicmotoristas;
$clveicparam             = new cl_veicparam;
$clveicmotoristascentral = new cl_veicmotoristascentral;

$db_botao = false;
$db_opcao = 33;
if (isset($excluir)) {
  $sqlerro  = false;
  $erro_msg = "";

  db_inicio_transacao();

  $res_veicmotoristascentral = $clveicmotoristascentral->sql_record($clveicmotoristascentral->sql_query(null, "ve41_veicmotoristas", null, "ve41_veicmotoristas = $ve05_codigo"));
  if ($clveicmotoristascentral->numrows > 0) {
    $erro_msg = "Motorista vinculado a Central de Abastecimento. Verifique.";
    $sqlerro  = true;
  }

  if ($sqlerro == false) {
    $db_opcao = 3;
    $clveicmotoristas->excluir($ve05_codigo);
  }

  db_fim_transacao($sqlerro);
} else if (isset($chavepesquisa)) {
  $db_opcao = 3;
  $result = $clveicmotoristas->sql_record($clveicmotoristas->sql_query($chavepesquisa));
  db_fieldsmemory($result, 0);
  $db_botao = true;
}

include(modification("forms/db_frmveicmotoristas.php"));

if (isset($excluir)) {
  if ($clveicmotoristas->erro_status == "0" || $sqlerro == true) {
    if (trim($erro_msg) != "") {
      db_msgbox($erro_msg);
    }
    $clveicmotoristas->erro(true, false);
  } else {
    $clveicmotoristas->erro(true, true);
  }
}
if ($db_opcao == 33) {
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
  js_tabulacaoforms("form1", "excluir", true, 1, "excluir", true);
</script>
