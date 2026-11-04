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
require_once(modification("classes/db_pcfornereprlegal_classe.php"));
require_once(modification("classes/db_pcforne_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
db_postmemory($_POST);
db_postmemory($_GET);

$clpcfornereprlegal = new cl_pcfornereprlegal;
$clpcforne = new cl_pcforne;
$db_opcao = 1;
$db_botao = true;

if (isset($incluir)) {
  db_inicio_transacao();
  $clpcfornereprlegal->incluir($pc81_sequencia);
  db_fim_transacao();
} else if (isset($alterar)) {
  $db_opcao = 2;
  db_inicio_transacao();
  $clpcfornereprlegal->alterar($pc81_sequencia);
  db_fim_transacao();
} else if (isset($excluir)) {
  $db_opcao = 3;
  db_inicio_transacao();
  $clpcfornereprlegal->excluir($pc81_sequencia);
  db_fim_transacao();
} else if (isset($opcao)) {
  if ($opcao == "alterar") {
    $db_opcao = 2;
  } else {
    $db_opcao = 3;
  }

  $result = $clpcfornereprlegal->sql_record($clpcfornereprlegal->sql_query(null, "pc81_sequencia, pc81_cgmforn, pc81_cgmresp, pc81_datini, pc81_datfin, pc81_obs, a.z01_nome as z01_nome, b.z01_nome as z01_nome1", "", "pc81_sequencia = " . $pc81_sequencia));

  if ($clpcfornereprlegal->numrows > 0) {
    db_fieldsmemory($result, 0);
  }
} else if (isset($pc81_cgmforn)) {
  $result = $clpcforne->sql_record($clpcforne->sql_query(null, "pc60_numcgm as pc81_cgmforn, z01_nome", "", "pc60_numcgm = $pc81_cgmforn"));
  if ($clpcforne->numrows > 0) {
    db_fieldsmemory($result, 0);
  }
}
?>

<html>
<?php
include(modification("forms/db_frmpcfornereprlegal.php"));
?>

<script>
  <?php if ($db_opcao != 3) { ?>
    js_tabulacaoforms("form1", "pc81_cgmresp", true, 1, "pc81_cgmresp", true);
  <?php } else { ?>
    js_tabulacaoforms("form1", "excluir", true, 1, "excluir", true);
  <?php } ?>
</script>

<?php
if (isset($incluir) || isset($alterar) || isset($excluir)) {
  db_msgbox($clpcfornereprlegal->erro_msg);
  if ($clpcfornereprlegal->erro_status == "0") {
    $db_botao = true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if ($clpcfornereprlegal->erro_campo != "") {
      echo "<script> document.form1." . $clpcfornereprlegal->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1." . $clpcfornereprlegal->erro_campo . ".focus();</script>";
    }
  }
}
?>

</html>
