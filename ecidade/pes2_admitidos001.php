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
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_rhregime_classe.php"));
include(modification("classes/db_rhcadregime_classe.php"));
include modification("dbforms/db_classesgenericas.php");

$clrhregime = new cl_rhregime;
$clrhcadregime = new cl_rhcadregime;
$rotulocampo = new rotulocampo;
$clrotulo = new rotulocampo;
$gform = new cl_formulario_rel_pes;

$rotulocampo->label("k11_id");
$rotulocampo->label("k13_conta");
$rotulocampo->label("DBtxt23");
$rotulocampo->label("DBtxt25");
$clrotulo->label('DBtxt23');
$clrotulo->label('DBtxt25');
$clrotulo->label('DBtxt27');
$clrotulo->label('DBtxt28');

db_postmemory($HTTP_POST_VARS);

if(!isset($datai_dia) && 
   !isset($datai_mes) &&
   !isset($datai_ano) ){
   $datai_dia = '01';
   $dataf_dia  = date('d',db_getsession("DB_datausu"));
   $datai_mes  = date('m',db_getsession("DB_datausu"));
   $dataf_mes  = date('m',db_getsession("DB_datausu"));
   $datai_ano  = date('Y',db_getsession("DB_datausu"));
   $dataf_ano  = date('Y',db_getsession("DB_datausu"));
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
      <center>
      <form name="form1" method="post" action="">
        <table>
          <tr>
            <td align="right" nowrap>
              <strong>Tipo :</strong>
            </td>
            <td align="left">
              <?php
              $arr_adm_dem = array("a"=>"Admitidos","d"=>"Demitidos");
              db_select('adm_dem',$arr_adm_dem,true,4,"");
              ?>
            </td>
          </tr>
          <tr> 
            <td align="right" nowrap>
              <strong>Admitidos entre:</strong>
            </td>
            <td align="left" nowrap>
              <?php
              db_inputdata("datai",$datai_dia,$datai_mes,$datai_ano, true,"text",1)
              ?>
            </td> 
            <td align="left" nowrap>
              &nbsp;<strong>e</strong>&nbsp;
              <?php
              db_inputdata("dataf",$dataf_dia,$dataf_mes,$dataf_ano, true,"text",1)
              ?>
            </td>
          </tr>
          <tr>
            <td align="right" nowrap title="Ordem para a emissão do relatório">
              <strong>Ordem:</strong>
            </td>
            <td align="left">
              <?php
              $arr_ordem = array("a"=>"Alfabética","n"=>"Numérica","d"=>"Admissão");
              if(!isset($ordem)){
                $ordem = "a";
              }
              db_select('ordem',$arr_ordem,true,4,"");
              if(isset($listaponto)){
                $chk_listaponto = "checked";
              }else{
                $chk_listaponto = "";
              }
              ?>
            </td>
            <td align="left">
    	      <input type="checkbox" name="listaponto" value="listaponto"<?=$chk_listaponto?>>Listar ponto fixo
           </td>
          </tr>
          <tr>
            <td align="right" nowrap>
              <strong>Quebrar por:</strong>
            </td>
            <td align="left">
              <?php
              $arr_quebra = array("n"=>"Sem quebra","l"=>"Por lotação","c"=>"Por cargo");
              if(!isset($lota)){
                $lota = "n";
              }
              db_select('lota',$arr_quebra,true,4,"");
              if(isset($listainativ)){
                $chk_listainativ = "checked";
              }else{
                $chk_listainativ = "";
              }
              ?>
            </td>
            <td align="left">
    	      <input type="checkbox" name="listainativ" value="listainativ"<?=$chk_listainativ?>>Listar inativos
            </td>
          </tr>
          <tr>
            <td align="right" nowrap>
              <strong>Quebra de página:</strong>
            </td>
            <td align="left">
              <?php
              $arr_SouN = array("s"=>"Sim","n"=>"Não");
              if(!isset($quebra)){
                $quebra = "n";
              }
              db_select('quebra',$arr_SouN,true,4,"");
              ?>
            </td>
            <td align="left">
              <?php
              $arr_SouN = array("s"=>"Sim","n"=>"Não");
              if(isset($listapens)){
                $chk_listapens = "checked";
              }else{
                $chk_listapens = "";
              }
              ?>
       	      <input type="checkbox" name="listapens" value="listapens"<?=$chk_listapens?>>Listar pensionistas
            </td>
          </tr>
          <tr>
            <td align="right" nowrap>
              <strong>Tipo :</strong>
            </td>
            <td align="left">
              <?php
              $arr_SouN = array("r"=>"Regime","v"=>"Vínculos");
              if(!isset($tipo)){
                $tipo = "r";
              }
              db_select('tipo',$arr_SouN,true,4, "onchange='js_opcao_selecionada();'");
              if(isset($listarescis)){
                $chk_listarescis = "checked";
              }else{
                $chk_listarescis = "";
              }
              ?>
            </td>
            <td align="rigth">
        	      <input type="checkbox" name="listarescis" value="listarescis"<?=$chk_listarescis?>>Listar rescindidos
            </td>
          </tr>
          <tr>
            <td align="right" nowrap><b>Formato de saída:</b></td>
            <td>
              <?php
              $aTiposSaida = array('pdf' => 'PDF', 'csv' => 'CSV');
              db_select('tipoSaida', $aTiposSaida, true, 1, "style='width:75px;'");
              ?>
            </td>
          </tr>
					<tr					>
					<td colspan=2 align=Left >
              <strong><?$arr_SouN[$tipo]?></strong>
					</td>
          <tr>
            <td align="right" nowrap colspan=3>
              <?php
              if($tipo == "r"){
	              $result_regime = $clrhcadregime->sql_record($clrhcadregime->sql_query_file(null,"rh52_regime,rh52_regime||' - '||rh52_descr as rh52_descr  "));
                db_multiploselect("rh52_regime", "rh52_descr", "nselecionados", "sselecionados", $result_regime, array(), 5, 250);
              }else{
  	            $result_regime = $clrhregime->sql_record($clrhregime->sql_query_file(null,"rh30_codreg,rh30_codreg||' - '||rh30_descr as rh30_descr  ", null, "rh30_instit = ".db_getsession('DB_instit')));
                db_multiploselect("rh30_codreg", "rh30_descr", "nselecionados", "sselecionados", $result_regime, array(), 5, 250);
              }
              ?>
            </td>
          </tr>
          <tr> 
            <td colspan="3" align="center"> 
              <input name="imprimir" type="button" id="imprimir" onClick="js_relatorio2()" value="Imprimir">
            </td>
          </tr>
        </table>
      </center>
      </form>
    </td>
  </tr>
</table>
<?php 
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
function js_relatorio2(){
  var F = document.form1;
  var datai = "";
  var dataf = "";
  if(F.datai_dia.value != "" && F.datai_mes.value != "" && F.datai_ano.value != ""){
    datai = F.datai_ano.value+'-'+F.datai_mes.value+'-'+F.datai_dia.value;
  }
  if(F.dataf_dia.value != "" && F.dataf_mes.value != "" && F.dataf_ano.value != ""){
    dataf = F.dataf_ano.value+'-'+F.dataf_mes.value+'-'+F.dataf_dia.value;
  }
  if(datai == "" && dataf == ""){
    alert("Informe o período de admissão.");
    F.datai_dia.focus();
  }else{
    qry = "?datai="+datai;
    qry+= "&dataf="+dataf;
    qry+= "&adm_dem="+F.adm_dem.value;
    qry+= "&ordem="+F.ordem.value;   
    if(F.listaponto.checked == true){
      qry+= "&fixo=s";
    }else{
      qry+= "&fixo=n";
    }
    if(F.listainativ.checked == true){
      qry+= "&listainativo=s";
    }
    if(F.listarescis.checked == true){
      qry+= "&listarescis=s";
    }
    if(F.listapens.checked == true){
      qry+= "&listapens=s";
    }
    qry+= "&tipo="+F.tipo.value;
    qry+= "&lota="+F.lota.value;
    qry+= "&quebrapagina="+F.quebra.value;
    qry+= "&tipoSaida="+F.tipoSaida.value;
    selecionados = "";
    virgula_ssel = "";
    for(var i=0; i<document.form1.sselecionados.length; i++){
      selecionados+= virgula_ssel + document.form1.sselecionados.options[i].value;
      virgula_ssel = ",";
    }
    qry+= "&regime="+selecionados;

    

    if (F.tipoSaida.value == 'csv') {
      var msgDiv = "Gerando arquivo CSV \n Aguarde ...";
      js_divCarregando(msgDiv, 'msgBox');
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_relfuncret', 'pes2_admitidos002.php'+qry, 'Gerando Arquivo', false);
    } else {
      jan = window.open('pes2_admitidos002.php'+qry,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
      jan.moveTo(0,0);
    }

  }
}

function js_opcao_selecionada(){
  document.form1.submit();
}

function js_detectaarquivo1(sArquivo) {
    js_removeObj('msgBox');
    var sListagem = sArquivo + "#Download arquivo CSV ";
    (window.CurrentWindow || parent.CurrentWindow).corpo.db_iframe_relfuncret.hide();
    js_montarlista(sListagem, "form1");
}
</script>