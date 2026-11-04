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
include(modification("libs/JSON.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_matestoqueinimei_classe.php"));
include(modification("classes/db_matestoqueinimeiari_classe.php"));
include(modification("classes/db_db_depusu_classe.php"));
include(modification("classes/db_db_usuarios_classe.php"));
include(modification("classes/db_matrequi_classe.php"));
include(modification("classes/db_matrequiitem_classe.php"));
include(modification("classes/db_atendrequi_classe.php"));
include(modification("classes/db_atendrequiitem_classe.php"));
include(modification("classes/db_atendrequiitemmei_classe.php"));
include(modification("classes/db_matestoqueitem_classe.php"));
include(modification("classes/db_matestoqueini_classe.php"));
include(modification("classes/db_matestoque_classe.php"));
include(modification("classes/db_matparam_classe.php"));
include(modification("classes/db_db_almoxdepto_classe.php"));
include(modification("classes/db_db_departorg_classe.php"));
include(modification("dbforms/db_funcoes.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$clmatestoqueinimei = new cl_matestoqueinimei;
$clmatestoqueinimeiari = new cl_matestoqueinimeiari;
$cldb_almoxdepto = new cl_db_almoxdepto;
$clmatestoqueini = new cl_matestoqueini;
$clmatestoque = new cl_matestoque;
$cldb_depusu        = new cl_db_depusu;
$cldb_usuarios      = new cl_db_usuarios;
$clrotulo           = new rotulocampo;
$objJSON            = new Services_JSON();
$clmatrequi = new cl_matrequi;
$clmatrequiitem = new cl_matrequiitem;
$clatendrequi = new cl_atendrequi;
$clatendrequiitem = new cl_atendrequiitem;
$clatendrequiitemmei = new cl_atendrequiitemmei;
$clmatestoqueitem = new cl_matestoqueitem;

$clrotulo->label("m41_codmatmater");
$clrotulo->label("m60_descr");
$clrotulo->label("m40_login");

if (isset($confirma)) {
  $sqlerro=false;
  $dados    =explode("quant_","$valor");
  $arr_info = array();
  db_inicio_transacao();
  for ($w=1; $w<count($dados); $w++) {
    if ($dados[$w]=="") {
      continue;
    }
    $info =explode("_",$dados[$w]);
    $codlanc = $info[0];
    $pos = $info[1];
    $quant = $info[2];
    $usuario_inf = "usu_".$pos;
    $depto_inf = "depto_".$pos;
    $usuario = $$usuario_inf;
    $depto = $$depto_inf;
    if ($quant==0||$depto==""||$usuario==""){
      continue;
    }
    if (array_key_exists("$depto|$usuario",$arr_info)) {
      $codrequi = $arr_info["$depto|$usuario"]["requi"];
      $codatend = $arr_info["$depto|$usuario"]["atend"];
    } else {
      if ($sqlerro==false) {
        $result_almox = $cldb_almoxdepto->sql_record($cldb_almoxdepto->sql_query(null,$depto));
        if ($cldb_almoxdepto->numrows>0) {
          db_fieldsmemory($result_almox,0);
        }
        $clmatrequi->m40_auto  = 't';
        $clmatrequi->m40_depto = $depto;
        $clmatrequi->m40_login = $usuario;
        $clmatrequi->m40_almox = @$m92_codalmox ;
        $clmatrequi->m40_hora  = db_hora();
        $clmatrequi->m40_data  = date('Y-m-d',db_getsession("DB_datausu"));
        $clmatrequi->m40_obs  = "Saida automatica, atreves da entrada da ordem de compra.";

        $clmatrequi->incluir(@$m40_codigo);
        $codigorequi=$clmatrequi->m40_codigo;
        if ($clmatrequi->erro_status==0) {
          db_msgbox("teste");
          $sqlerro=true;
          $erro_msg=$clmatrequi->erro_msg;
          break;
        }
        $arr_info["$depto|$usuario"]["requi"]=$codigorequi;
        $codrequi = $codigorequi;
      }

      if ($sqlerro==false) {
        $clatendrequi->m42_login=$usuario;
        $clatendrequi->m42_depto=$depto;
        $clatendrequi->m42_data=date('Y-m-d',db_getsession("DB_datausu"));
        $clatendrequi->m42_hora=db_hora();
        $clatendrequi->incluir(null);
        $codigoatend=$clatendrequi->m42_codigo;
        if ($clatendrequi->erro_status==0) {
          $sqlerro=true;
          $erro_msg=$clatendrequi->erro_msg;
          break;
        }
        $arr_info["$depto|$usuario"]["atend"]=$codigoatend ;
        $codatend = $codigoatend;
      }

    }
    if ($sqlerro==false) {
      $result_matmater = $clmatestoqueitem->sql_record($clmatestoqueitem->sql_query($codlanc,"m70_codmatmater"));
      db_fieldsmemory($result_matmater,0);

      $clmatrequiitem->m41_codunid     = '1';
      $clmatrequiitem->m41_codmatrequi = $codrequi;
      $clmatrequiitem->m41_codmatmater = $m70_codmatmater;
      $clmatrequiitem->m41_quant = "$quant";
      $clmatrequiitem->incluir(null);
      if ($clmatrequiitem->erro_status==0) {
        $erro_msg = $clmatrequiitem->erro_msg;
        $sqlerro  = true;
        break;
      }
      $codmater   = $clmatrequiitem->m41_codmatmater;
      $codreqitem = $clmatrequiitem->m41_codigo;
      $tot_quant  = $clmatrequiitem->m41_quant;

      // Gera Array Com Itens do Atendimento
      $aItens = array();
      $aSubItens["codmatmater"]     = $codmater;
      $aSubItens["codmatrequiitem"] = $codreqitem;
      $aSubItens["quantatend"]      = $tot_quant;
      $aSubItens["codmatestoqueitem"]      = $codlanc;
      //se for retirar o estoque de algum lançamento em especifico

      $aItens[] = $aSubItens;

      //var_dump($aItens);
      //die();

      // Efetua atendimento da requisicao
      $sqlerro = $clmatrequi->atendrequi($codrequi, // Codigo da Requisicao
      $codatend, // Atendimento Existente
      $usuario,  // Usuario Atual
      date('Y-m-d', db_getsession("DB_datausu")),  // Data do Sistema
      db_hora(),                                   // Hora Atual
      db_getsession('DB_coddepto'),                // Depto do Usuario Atual logado
      $aItens,                                     // Itens do Atendimento
      $erro_msg);
    }

    if ($sqlerro==true) {
      break;
    }

  }
  db_fim_transacao($sqlerro);
  if ($sqlerro==true) {
    db_msgbox("Erro!!".$erro_msg);
  } else {
    db_msgbox("Processamento efetuado com sucesso!!");
    echo "<script>location.href='mat1_entraordcom001.php';</script>";
  }
}

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>


</script>

<style>
<?$cor="#999999"?>
.bordas{
         border: 2px solid #cccccc;
         border-top-color: <?=$cor?>;
         border-right-color: <?=$cor?>;
         border-bottom-color: <?=$cor?>;
         background-color: #999999;
}
<?$cor="999999"?>
.bordas_corp{
         border: 1px solid #cccccc;
         border-right-color: <?=$cor?>;
         border-bottom-color: <?=$cor?>;
       }
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#CCCCCC">
<table  border="0" cellspacing="0" cellpadding="0" width='100%'>
  <tr>
  <br>
  <br>
  <br>
    <td  align="left" valign="top" bgcolor="#CCCCCC">
    <form name='form1'>
    <center>

  <tr align = "center">
    <td  align = "center">
      <input name="voltar" type="button" value="Voltar" onclick="location.href='mat1_entraordcom001.php';" >
      <input name="confirma" type="submit"  value="Confirma" onclick='return js_buscaquant();'  >
      <br>
      <br>
    </td>
  </tr>
 <table border='1' cellspacing="0" cellpadding="0">
 <?php
db_input('valor',40,"",true,'hidden',3,'');
db_input("m80_codigo","10","",true,"hidden",3);
if (isset($m80_codigo) && $m80_codigo!= "") {

  $campos = "*";
  $result = $clmatestoqueinimei->sql_record($clmatestoqueinimei->sql_query_info(null,"$campos",null,"m82_matestoqueini=$m80_codigo"));
  $numrows = $clmatestoqueinimei->numrows;
  if ($numrows>0) {
    echo "
          <tr class='bordas'>
          <td class='bordas' align='center'><b><small>$RLm41_codmatmater</small></b></td>
          <td class='bordas' align='center'><b><small>$RLm60_descr</small></b></td>
          <td class='bordas' align='center'><b><small>Unid. Saída</small></b></td>
          <td class='bordas' align='center'><b><small><b>Quant. Disponível em Estoque<b></small></b></td>
          <td class='bordas' align='center'><b><small><b>Quant. Solicitada<b></small></b></td>
          <td class='bordas' align='center'><b><small><b>Usuário<b></small></b></td>
          <td class='bordas' align='center'><b><small><b>Departamento<b></small></b></td>
          ";
  } else {
    echo"<b>Nenhum registro encontrado...</b>";
  }
  echo " </tr>";
  for ($i=0; $i<$numrows; $i++) {
    db_fieldsmemory($result,$i);
    echo "<tr>
          <td	class='bordas_corp' align='center'><small>$m60_codmater </small></td>
          <td	class='bordas_corp' align='center'><small>$m60_descr </small></td>
          <td	class='bordas_corp' align='center'><small>$m61_descr </small></td>
          <td	class='bordas_corp' align='center'><small>$m71_quant</small></td>";
    $q="q_$i" ;
    $$q=$m71_quant;
    db_input("q_$i",6,0,true,'hidden',3,"");

    $op = 1;
    $quantidade = "quant_".$m71_codlanc."_"."$i";
    $$quantidade = "$m71_quant";

    echo "<td class='bordas_corp' align='center'><small>";
    db_input("quant_".$m71_codlanc."_"."$i",6,0,true,'text',1,"onchange='js_testaquant(this.value,$m71_quant,$i,$m71_codlanc)'");
    echo "</small></td><td>";

    //db_ancora(@$Lm40_login,"js_pesquisalogin(true);",1);
    db_input('login',10,@$Im40_login,true,'hidden',1);
    db_input('nome',40,@$Inome,true,'hidden',3,'');
      $result_usu = $cldb_usuarios->sql_record($cldb_usuarios->sql_query_depusu(null,"distinct db_usuarios.id_usuario,nome","nome","usuarioativo=1"));
      if ($cldb_usuarios->numrows>0) {
          echo " <select onchange='js_retdepto(this.value,$i,$m70_coddepto);'   name='usu_$i' >";
            echo "<option value=\"\" >Selecione um Usuário</option>\n";
        for ($x=0; $x<$cldb_usuarios->numrows; $x++) {
          db_fieldsmemory($result_usu,$x);
            echo "<option value=\"$id_usuario\" >$nome</option>\n";
        }
          echo " </select>";
      } else {
        echo "Nenhum usuário disponível!";
      }
    echo "</small></td><td>";
          echo " <select   name='depto_$i' >";
            echo "<option value=\"\" >Selecione um Usuário</option>\n";
          echo " </select>";

    echo"  </td>";
    echo"  </tr>";
  }
}
?>
 </table>
    </form>
    </center>
    </td>
  </tr>
</table>
<script>
function js_testaquant(valor,quant,pos,codlan)
{
  if (valor>quant) {
    alert("Informe uma quantidade valida!!");
    eval("document.form1.quant_"+codlan+"_"+pos+".value="+quant);
    eval("document.form1.quant_"+codlan+"_"+pos+".focus()");
  }
}
function js_retdepto(codusu,i,depto)
{
  if (codusu!="") {
    js_OpenJanelaIframe('','db_iframe_db_usuarios','mat4_buscadepto.php?i='+i+'&codusu='+codusu+'&deptoord='+depto+'&funcao_js=parent.js_procret','Pesquisa',false,0);
  }else{
  eval("document.form1.depto_"+i+".length=0;");
  eval("document.form1.depto_"+i+".options[0]=new Option();");
  eval("document.form1.depto_"+i+".options[0].value=''");
  eval("document.form1.depto_"+i+".options[0].text='Selecione um Usuário'");
  }
}
function js_procret(depto,pos)
{
  depto = new String(depto);
  eval("document.form1.depto_"+pos+".length=0;");
  eval("document.form1.depto_"+pos+".options[0]=new Option();");
  eval("document.form1.depto_"+pos+".options[0].value=''");
  eval("document.form1.depto_"+pos+".options[0].text='Selecione um Usuário'");
  if (depto=='') {
    alert("Usuário sem acesso a nenhum Depart.!!");
    eval("document.form1.usu_"+pos+".value=''");

  } else {
    eval("usu_pos=document.form1.usu_"+pos+".value");
    obj=document.form1;
    dados = depto.split(",");
    for (w=0; w<obj.elements.length; w++) {
      if (obj.elements[w].name.substr(0,4)=="usu_") {
        posinf = obj.elements[w].name.split("usu_");
        if (obj.elements[w].value==""||pos==posinf[1] ) {
          eval("document.form1.depto_"+posinf[1]+".length=0;");
          obj.elements[w].value = usu_pos;
          for (i=0; i<dados.length; i++) {
            info = dados[i].split("|");
            eval("document.form1.depto_"+posinf[1]+".options[i]=new Option();");
            eval("document.form1.depto_"+posinf[1]+".options[i].value=info[0]");
            eval("document.form1.depto_"+posinf[1]+".options[i].text=info[1]");
          }
        }
      }
    }

  }
}
function js_buscaquant()
{
  obj=document.form1;
  valor = "";
  arr_info = new Array();
  var ii = 0;
  for (i=0; i<obj.elements.length; i++) {
    if (obj.elements[i].name.substr(0,6)=="quant_") {

      valor += obj.elements[i].name+"_"+obj.elements[i].value;
    }
  }

  document.form1.valor.value = valor;
  return true;
}
</script>
<?php db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
</body>
</html>
