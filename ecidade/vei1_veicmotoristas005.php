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

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);

$clveicmotoristas = new cl_veicmotoristas;
$clveicparam      = new cl_veicparam;

$db_opcao = 22;
$db_botao = false;
if (isset($alterar)) {
  db_inicio_transacao();
  $db_opcao = 2;
  $clveicmotoristas->alterar($ve05_codigo);
  db_fim_transacao();
} else if (isset($chavepesquisa)) {
  $db_opcao = 2;
  $result = $clveicmotoristas->sql_record($clveicmotoristas->sql_query($chavepesquisa));
  db_fieldsmemory($result, 0);
  $db_botao = true;
?>
  <script>
    parent.document.formaba.veicmotoristascentral.disabled = false;
    (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_veicmotoristascentral.location.href = 'vei1_veiccentralmotoristas001.php?ve41_veicmotoristas=<?php echo (@$chavepesquisa) ?>';
    <?php
    if (isset($liberaaba) && $liberaaba == true) {
    ?>
      parent.mo_camada('veicmotoristascentral');
    <?php
    }
    ?>
  </script>
<?php
}

include(modification("forms/db_frmveicmotoristas.php"));

if (isset($alterar)) {
  if ($clveicmotoristas->erro_status == "0") {
    $clveicmotoristas->erro(true, false);
    $db_botao = true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if ($clveicmotoristas->erro_campo != "") {
      echo "<script> document.form1." . $clveicmotoristas->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1." . $clveicmotoristas->erro_campo . ".focus();</script>";
    }
  } else {
    $clveicmotoristas->erro(true, true);
  }
}
if ($db_opcao == 22) {
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
  js_tabulacaoforms("form1", "ve05_numcgm", true, 1, "ve05_numcgm", true);
</script>
