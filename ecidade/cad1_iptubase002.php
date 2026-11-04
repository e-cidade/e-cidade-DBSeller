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
require_once(modification("classes/db_iptubase_classe.php"));
db_postmemory($_SERVER);
db_postmemory($_POST);
$cliptubase = new cl_iptubase;
$cliptubase->rotulo->label();
$cliptubase->rotulo->tlabel();

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
function js_pripromi(){
  alert("dsd");
}
function js_checa(){
  if(!js_verifica_campos_digitados()){
    return false;
  } 
  if(document.form1.j01_matric.value==""){
    alert("Informe a matrícula!");
    return false;
  }
  return true;
}
</script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="document.form1.j01_matric.focus();" >

<br />  <br />


<form name="form1" method="post" action="">
  <center>
  <fieldset style="width:600px;">
    <legend>Matricula do imóvel</legend>
    <table align="center" width="600" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td>     
            <input type="hidden" name="tipoImovel" value="<?=$tipoImovel?>"> 
            <input type="hidden" name="testaentra" value="true"> 
            <?php
              db_ancora($Lj01_matric,' js_matri(true); ',1);
            ?>
          </td>

          <td> 
          <?php
            db_input('j01_matric',10,0,true,'text',1,"onchange='js_matri(false)'");
            db_input('z01_nome',50,0,true,'text',3,"");
          ?>
          </td>
      </tr>
    </table>
    </fieldset>
    <input name="entrar" type="submit" id="pesquisa" value="Entrar" onclick="return js_checa()">
    <input name="navegamatricula" type="hidden" id="navegamatricula" value="">
  </center>
</form>

<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>

<?php
if (isset($_GET['alteracao'])) {
  $tipoImovel = $_GET['tipoImovel'];
  $testaentra = $_GET['testaentra'];
  $j01_matric = $_GET['j01_matric'];
  $z01_nome = $_GET['z01_nome'];
  $entrar = $_GET['entrar'];
  
  echo "
  <script>
  const obj = document.form1;
  obj.action = 'cad1_iptubase0021.php';
  obj.tipoImovel.value = {$tipoImovel};
  obj.testaentra.value = {$testaentra};
  obj.j01_matric.value = {$j01_matric};
  obj.z01_nome.value = '{$z01_nome}';
  obj.entrar.value = '{$entrar}';
  obj.navegamatricula.value = '1';
  obj.submit();
  </script>";
}
?>

<script>
function js_matri(mostra){
  var matri=document.form1.j01_matric.value;
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe','func_iptubase.php?tipoImovel=<?=$tipoImovel?>&funcao_js=parent.js_mostra|0|2','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe','func_iptubase.php?pesquisa_chave='+matri+'&funcao_js=parent.js_mostra1','Pesquisa',false);
  }
}
function js_mostra(chave1,chave2){
  document.form1.j01_matric.value = chave1;
  document.form1.z01_nome.value = chave2;
  db_iframe.hide();
}
function js_mostra1(chave,erro){
  document.form1.z01_nome.value = chave; 
  if(erro==true){ 
    document.form1.j01_matric.focus(); 
    document.form1.j01_matric.value = ''; 
  }
}

</script>
<?php

if (isset($_POST['tipoImovel'])) {
  $tipoImovel = $_POST['tipoImovel'];
  $j01_matric = $_POST['j01_matric'];
  $sql = "SELECT * FROM iptubase WHERE j01_matric = $j01_matric AND j01_tipoimovel = $tipoImovel";
  $result = db_query($sql);
  if (pg_num_rows($result) > 0) {
    $testaentra = $_POST['testaentra'];
    $z01_nome = $_POST['z01_nome'];
    $entrar = $_POST['entrar'];
    echo "<script>
      const obj = document.form1;
      obj.action = 'cad1_iptubase0021.php';
      obj.tipoImovel.value = $tipoImovel;
      obj.testaentra.value = $testaentra;
      obj.j01_matric.value = $j01_matric;
      obj.z01_nome.value = '$z01_nome';
      obj.entrar.value = '$entrar';
      obj.submit();
    </script>";
  } else {
    if ($tipoImovel == 1) {
      db_msgbox("Matrícula de Imóvel Rural!");
    } elseif ($tipoImovel == 2) {
      db_msgbox("Matrícula de Imóvel Urbano!");
    }
  }
}

if(isset($invalido)){
  echo "<script>alert('Numero de matrícula inválido!')</script>";
}
?>