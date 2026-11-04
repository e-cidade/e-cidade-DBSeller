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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_libcontabilidade.php"));
require_once(modification("classes/db_conparametro_classe.php"));

$clestrutura_sistema = new cl_estrutura_sistema;

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">

<?php
db_app::load("scripts.js, prototype.js, strings.js, dbautocomplete.widget.js");
db_app::load("estilos.css");
?>

<script>

function js_emite(){

  var exp = /\.|\-/g;
  var estrutural = document.form1.c90_estrutcontabil.value;
      estrutural = estrutural.replace(exp,"");
  jan = window.open('con2_planoorcamentonaovinculado002.php?estrutual='+estrutural,
                                                           '','width='+(screen.availWidth-5)+
                                                           ',height='+(screen.availHeight-40)+
                                                           ',scrollbars=1,location=0 ');
  jan.moveTo(0,0);
}
</script>  
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
  <div style='margin-top: 40px;'></div>
  <center>
  <fieldset style='width: 250px;'>
  <legend><b>Plano Orçamentário não Vínculado PCASP</b></legend>
  <table  align="center">
    <form name="form1" method="post" action="" >
      <tr>
      <?php 
        $clestrutura_sistema->autocompletar = false;
        $clestrutura_sistema->size          = 30;
        $clestrutura_sistema->botao         = false;
        $clestrutura_sistema->reload        = true ;
        $clestrutura_sistema->estrutura_sistema('c90_estrutcontabil');
      ?>
      </tr>
      <tr>
        <td colspan="2" align = "center"> 
          <input  name="emite" id="emite" type="button" value="Processar" onclick="js_emite();" >
        </td>
      </tr>
    </form>
  </table>
  </fieldset>
  </center>
  <?php
    db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
  ?>
</body>
</html>