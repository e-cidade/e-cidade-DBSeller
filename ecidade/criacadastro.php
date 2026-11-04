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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));

if(isset($HTTP_POST_VARS["criar"])) {
  $tabela = $HTTP_POST_VARS["nometab"];
  $arquivo = $HTTP_POST_VARS["arquivo"];
  $tam_vetor = sizeof($HTTP_POST_VARS);
  reset($HTTP_POST_VARS);
  next($HTTP_POST_VARS);
  next($HTTP_POST_VARS);
  next($HTTP_POST_VARS);
  $v = 0;
  for($i = 0;$i < $tam_vetor;$i += 2) {
    $campos[$v][0] =  $HTTP_POST_VARS[key($HTTP_POST_VARS)];
	next($HTTP_POST_VARS);
    $campos[$v][1] =  $HTTP_POST_VARS[key($HTTP_POST_VARS)];
    if(db_indexOf($HTTP_POST_VARS[key($HTTP_POST_VARS)],"varchar") > 0) {
	  next($HTTP_POST_VARS);
      $campos[$v][2] = $HTTP_POST_VARS[key($HTTP_POST_VARS)];
    }
	$v++;
    next($HTTP_POST_VARS);
  } 
  
  if($HTTP_POST_VARS["criartabela"] == "1") {
    $tam = sizeof($campos);
    $aux = "";
    for($i = 0;$i < $tam;$i++) {
      if($campos[$i][0] != "")
        $aux .= $campos[$i][0]." ".$campos[$i][1].($campos[$i][2] != ""?"(".$campos[$i][2]."),":",");
    }
    $aux[strlen($aux)-1] = ")";
    $aux = "create table $tabela( $aux";
    db_query($aux) or die("Erro(34) criando tabela $tabela: ".pg_errormessage());
  }
  ////////
  
$corpo = "
<?php
require(modification(\"libs/db_stdlib.php\"));
require(modification(\"libs/db_conecta.php\"));
parse_str(base64_decode(\$HTTP_SERVER_VARS['QUERY_STRING']));
if(isset(\$retorno)) {
  \$sql = \"select * from $tabela where ".$campos[0][0]." = \$retorno\";
  \$result = db_query(\$sql);
  db_fieldsmemory(\$result,0);
}

//////////INCLUIR/////////////
if(isset(\$HTTP_POST_VARS[\"incluir\"])) {
  db_postmemory(\$HTTP_POST_VARS);
  \$result = db_query(\"select max(".$campos[0][0].") + 1 from $tabela\");
  \$".$campos[0][0]." = pg_result(\$result,0,0);
  \$".$campos[0][0]." = \$".$campos[0][0]."==\"\"?\"1\":\$".$campos[0][0].";
  db_query(\"insert into $tabela values(";
  $tam = sizeof($campos);
  for($i = 0;$i < $tam;$i++) {
    if($campos[$i][0] != "")
	  $corpo .= "\n".($campos[$i][1] == 'float8' || $campos[$i][1] == 'int4'?"\$".$campos[$i][0].",":"'\$".$campos[$i][0]."',");
  }
  $corpo[strrpos($corpo,",")] = " ";
  $corpo .= "
	)\") or die(\"Erro(23) inserindo em $tabela: \".pg_errormessage());
  db_redireciona(\$HTTP_SERVER_VARS['PHP_SELF']);
  exit;		   
////////////////ALTERAR////////////////  
} else if(isset(\$HTTP_POST_VARS[\"alterar\"])) {
  db_postmemory(\$HTTP_POST_VARS);
  
  db_query(\"update $tabela set";
  $tam = sizeof($campos);
  for($i = 1;$i < $tam;$i++) {
    if($campos[$i][0] != "")
	  $corpo .= "\n".$campos[$i][0]." = ".($campos[$i][1] == 'float8' || $campos[$i][1] == 'int4'?"\$".$campos[$i][0].",":"'\$".$campos[$i][0]."',");
  }
  $corpo[strrpos($corpo,",")] = " ";  
  $corpo .= "
		   where ".$campos[0][0]." = $".$campos[0][0]."\") or die(\"Erro(38) alterando $tabela: \".pg_errormessage());
  db_redireciona(\$HTTP_SERVER_VARS['PHP_SELF']);
  exit;		     
////////////////EXCLUIR//////////////
} else if(isset(\$HTTP_POST_VARS[\"excluir\"])) {
  db_query(\"delete from $tabela where ".$campos[0][0]." = \".\$HTTP_POST_VARS[\"".$campos[0][0]."\"]) or die(\"Erro(43) excluindo $tabela: \".pg_errormessage());
  db_redireciona(\$HTTP_SERVER_VARS['PHP_SELF']);
  exit;  
}
?>\n";

$corpo .= "
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<meta http-equiv=\"Expires\" CONTENT=\"0\">
<script language=\"JavaScript\" type=\"text/javascript\" src=\"scripts/scripts.js\"></script>
<style type=\"text/css\">
<!--
.mod {
	color: black;
}
.mod1 {
	color: #999999;
}
-->
</style>
<link href=\"estilos.css\" rel=\"stylesheet\" type=\"text/css\">
</head>
<body bgcolor=#CCCCCC leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\" onLoad=\"js_iniciar()\" >
<table width=\"790\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#5786B2\">
  <tr> 
    <td width=\"360\">&nbsp;</td>
    <td width=\"263\">&nbsp;</td>
    <td width=\"25\">&nbsp;</td>
    <td width=\"140\">&nbsp;</td>
  </tr>
</table>
<table width=\"790\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  <tr> 
    <td height=\"430\" align=\"left\" valign=\"top\" bgcolor=\"#CCCCCC\"> <center>
	<?php
      if(isset(\$HTTP_POST_VARS[\"procurar\"]) || isset(\$HTTP_POST_VARS[\"priNoMe\"]) || isset(\$HTTP_POST_VARS[\"antNoMe\"]) || isset(\$HTTP_POST_VARS[\"proxNoMe\"]) || isset(\$HTTP_POST_VARS[\"ultNoMe\"])) {
        \$sql = \"SELECT ".$campos[0][0]." as db_codigo,*
              FROM $tabela
			  WHERE upper(".$campos[1][0].") like upper('\".\$HTTP_POST_VARS[\"".$campos[1][0]."\"].\"%')
			  ORDER BY ".$campos[1][0]."\";
        db_lov(\$sql,15,\"$arquivo\"); 
      } else {
    ?>
        <form name=\"form1\" method=\"post\">
		<input type=\"hidden\" name=\"id_item\" value=\"<?=@$id_item?>\">
          <table width=\"399\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	  $tam = sizeof($campos);
      for($i = 0;$i < $tam;$i++) {
        if($campos[$i][0] != "")
	      $corpo .= "
            <tr> 
              <td height=\"25\" nowrap><strong>".$campos[$i][0]."</strong></td>
              <td height=\"25\" nowrap><input name=\"".$campos[$i][0]."\" type=\"text\" value=\"<?=@\$".$campos[$i][0]."?>\" size=\"50\"></td>
            </tr>\n";
	  }
	  $corpo .= "
            <tr>
              <td height=\"25\" id=\"at4\" class=\"mod1\" nowrap>&nbsp;</td>
              <td height=\"25\" id=\"at5\" class=\"mod1\" nowrap> <input name=\"incluir\" accesskey=\"i\" type=\"submit\" id=\"incluir\" value=\"Incluir\" <?php echo isset(\$retorno)?\"disabled\":\"\" ?>> 
                &nbsp; <input name=\"alterar\" accesskey=\"a\" type=\"submit\" id=\"alterar\" value=\"Alterar\" <?php echo !isset(\$retorno)?\"disabled\":\"\" ?>> 
                &nbsp; <input name=\"excluir\" accesskey=\"e\" type=\"submit\" id=\"excluir\" value=\"Excluir\" onClick=\"return confirm('Quer realmente excluir este registro?')\" <?php echo !isset(\$retorno)?\"disabled\":\"\" ?>> 
                &nbsp; <input name=\"procurar\" accesskey=\"p\" type=\"submit\" id=\"procurar\" value=\"Procurar\"></td>
            </tr>
          </table>
        </form>
		<?php
		}
		?>
      </center></td>
  </tr>
</table>
<?php 
db_menu(db_getsession(\"DB_id_usuario\"),db_getsession(\"DB_modulo\"),db_getsession(\"DB_anousu\"),db_getsession(\"DB_instit\"));
?>
</body>
</html>\n";

$fp = popen("cadastros/$arquivo","w");
fputs($fp,$corpo,strlen($corpo));
fclose($fp);
  ////////
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<script language="JavaScript" type="text/javascript">
function cai() {
  var elem = window.event.srcElement.id;
  var prox = document.getElementById(elem).nextSibling.attributes('id').nodeValue; 
  var sel = document.getElementById(elem).options[document.getElementById(elem).selectedIndex].value;
  if(sel != 'varchar') {
    document.getElementById(prox).disabled = true;
	document.getElementById(prox).style.background = '#999999';
	document.getElementById("inserir").focus();
  } else {
    document.getElementById(prox).disabled = false;  
	document.getElementById(prox).style.background = 'white';
	document.getElementById(prox).focus();
  }
}
function js_remover() {
  var elem = document.getElementById('inp').lastChild;
  if(elem) {
    elem.parentNode.removeChild(elem);
    var elem = document.getElementById('inp').lastChild;
    elem.parentNode.removeChild(elem);
    var elem = document.getElementById('inp').lastChild;
    elem.parentNode.removeChild(elem);
    var elem = document.getElementById('inp').lastChild;
    elem.parentNode.removeChild(elem);      
 }
}
function js_inserir() {
  var nome = 'campo' + document.form1.length;
  var sele = 'sele' + document.form1.length;
  var idtamanho = 'idtamanho' + document.form1.length;  
  var idsele = 'idsele' +  document.form1.length;
  var idcampo = 'idcampo' + document.form1.length;
  var espaco = document.createElement("&nbsp;");
  var campo = document.createElement("INPUT");  
  var tipo = document.createElement("SELECT");  
  var int = document.createElement("OPTION");
  var car = document.createElement("OPTION");
  var dat = document.createElement("OPTION");    
  var tex = document.createElement("OPTION");    
  var flt = document.createElement("OPTION"); 
  var tam = document.createElement("INPUT");
   var br = document.createElement("BR");
  
  campo.setAttribute("type","text");
  campo.setAttribute("name",nome);  
  campo.setAttribute("id",idcampo);    
//  campo.setAttribute("value",nome);   
  tipo.setAttribute("name",sele);
  tipo.setAttribute("size","1");
  tipo.setAttribute("id",idsele);
  int.setAttribute("value","int4");      
  car.setAttribute("value","varchar");    
  dat.setAttribute("value","date");      
  tex.setAttribute("value","text");      
  flt.setAttribute("value","float8");
  tam.setAttribute("type","text");
  tam.setAttribute("name",idtamanho);  
  tam.setAttribute("id",idtamanho);    
  tam.setAttribute("size","3");
  tam.setAttribute("maxlength","3");
 
  document.getElementById('inp').appendChild(campo);
  document.getElementById('inp').appendChild(tipo);
  tipo.appendChild(car);
  car.appendChild(document.createTextNode("Caracter"));  
  tipo.appendChild(int);
  int.appendChild(document.createTextNode("Inteiro"));
  tipo.appendChild(dat);
  dat.appendChild(document.createTextNode("Data"));  
  tipo.appendChild(tex);
  tex.appendChild(document.createTextNode("Texto"));  
  tipo.appendChild(flt);
  flt.appendChild(document.createTextNode("Decimal"));
  document.getElementById('inp').appendChild(tam);
  document.getElementById('inp').appendChild(br);
  document.getElementById(idcampo).focus();  
  document.getElementById(idsele).onchange = cai;
  document.getElementById(idtamanho).onchange = function() {
    document.getElementById("inserir").focus();
  }
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> <center>
        <form name="form1" method="post">
          <table width="400" border="0" cellspacing="0" cellpadding="0">
            <tr> 
              <td> <table border="0" cellspacing="0" cellpadding="0">
                  <tr> 
                    <td>Nome da Tabela:</td>
                    <td><input type="text" name="nometab"></td>
                  </tr>
                  <tr> 
                    <td>Nome do Arquivo:</td>
                    <td><input type="text" name="arquivo"></td>
                  </tr>
                  <tr> 
                    <td>Criar Tabela:</td>
                    <td><input type="checkbox" name="criartabela" value="1"></td>
                  </tr>
                </table></td>
            </tr>
            <tr> 
              <td> <input name="inserir" type="button" id="inserir" value="Inserir" onClick="js_inserir()"> 
                <input name="remover" type="button" id="remover" value="Remover" onClick="js_remover()"> 
                <input name="criar" type="submit" value="Criar"> </td>
            </tr>
            <tr> 
              <td id="inp"></td>
            </tr>
          </table>
        </form>
      </center>
<?php 
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
    </td>
  </tr>
</table>
</body>
</html>