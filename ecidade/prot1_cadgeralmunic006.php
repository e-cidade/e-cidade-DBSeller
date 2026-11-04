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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("classes/db_cgm_classe.php"));
require_once(modification("classes/db_cgmtipoempresa_classe.php"));
require_once(modification("classes/db_tipoempresa_classe.php"));

$oPost = db_utils::postMemory($_POST);
$db_opcao = 33;
$db_botao = false;

$clcgm            = new cl_cgm;
$clcgmtipoempresa = new cl_cgmtipoempresa;
$cltipoempresa    = new cl_tipoempresa;

$clcgm->rotulo->label();
$cltipoempresa->rotulo->label();
$clcgmtipoempresa->rotulo->label();

$lPessoaFisica = true;

if (isset($chavepesquisa)) {

  $result = $clcgm->sql_record($clcgm->sql_query($chavepesquisa));
  if ($result !== false && $result != 0) {
    $db_opcao = 3;
    $db_botao = true;
    $oCgm = db_utils::fieldsMemory($result, 0);

    if (strlen($oCgm->z01_cgccpf) == 14) {
      $lPessoaFisica = false;
    }
  }
}

?>
<html>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">

  <div class="container">
    <?php
    require_once(modification("forms/db_frmcadgeralmunic.php"));
    ?>
  </div>

</body>

</html>
<?php

if (isset($chavepesquisa)) {
  echo "
  <script>
      function js_db_libera(){
         parent.document.formaba.documentos.disabled=false;
         (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_documentos.location.href='prot1_cadgeraldocumentos001.php?z01_numcgm=" . $chavepesquisa . "&opcao=3';
     ";
  if (isset($liberaaba)) {
    echo "  parent.mo_camada('documentos');";
  }
  echo "}\n
    js_db_libera();
    js_findCgm($chavepesquisa);
  </script>\n
 ";
}
if ($db_opcao == 22 || $db_opcao == 33) {
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
