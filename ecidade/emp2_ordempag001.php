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
include(modification("dbforms/db_classesgenericas.php"));
db_postmemory($HTTP_POST_VARS);

$aux = new cl_arquivo_auxiliar;

$clrotulo = new rotulocampo;
$clrotulo->label('e50_numemp');
$clrotulo->label('e50_codord');


?>

<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>

<script>
function js_emite(){

  vir="";
  listacredor="";
  for(x=0;x<document.form1.credor.length;x++){
   listacredor+=vir+document.form1.credor.options[x].value;
   vir=",";
  }


  var param =  "data="+document.form1.data_ano.value+'-'+document.form1.data_mes.value+'-'+document.form1.data_dia.value;
      param += "&data1="+document.form1.data1_ano.value+'-'+document.form1.data1_mes.value+'-'+document.form1.data1_dia.value;
      param += "&codini="+document.form1.e50_codordINI.value;
      param += "&codfim="+document.form1.e50_codordFIM.value;
      param += "&numempini="+document.form1.e50_numempINI.value;
      param += "&numempfim="+document.form1.e50_numempFIM.value;
      param += "&ordem="+document.form1.sOrdem.value;
      param += "&listacredor="+listacredor;

  jan = window.open('emp2_ordempag002.php?'+param,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
  jan.moveTo(0,0);
}
function js_testa(campo,valor,nomecampo1,nomecampo2){
  msg = "Informe um intervalo de código válido!";
  erro = false;
  if(campo=="i"){
    if(eval("document.form1."+nomecampo2+".value")!="" && parseInt(valor)>=parseInt(eval("document.form1."+nomecampo2+".value"))){
      erro = true;
    }
  }else if(campo=="f"){
    if(eval("document.form1."+nomecampo1+".value")!="" && parseInt(valor)<=parseInt(eval("document.form1."+nomecampo1+".value"))){
      erro = true;
    }
  }
  if(erro == true){
    alert(msg);
    eval('document.form1.'+nomecampo1+'.value = ""');
    eval('document.form1.'+nomecampo2+'.value = ""');
    eval('document.form1.'+nomecampo1+'.focus()');
  }
}
</script>  
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
<table align="center" width="30%">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
      <form name="form1" method="post" action="">
      <fieldset class="fildset-principal">
        <legend>
          <b>Ordens De Pagamento</b>
        </legend>
        <table align="left" border="0" class="table-campos">
            <tr>
                <td nowrap align="left"><b>De:</b></td>
                <td  align="left" nowrap>
                 <?php
                   db_inputdata("data","","","","true","text",2);
                   echo " <b>Até:</b> ";
                   db_inputdata("data1","","","","true","text",2)
                 ?>
                </td>
            </tr>
            <tr>
                <td nowrap align="left"><b>Apartir da Ordem:</b></td>
                <td  align="left" nowrap>
                 <?php
		   db_input('e50_codord',16,$Ie50_codord,true,'text',1,"onChange=\"js_testa('i',this.value,'e50_codordINI','e50_codordFIM')\"","e50_codordINI","");
                   echo " <b>Até:</b> ";
		   db_input('e50_codord',16,$Ie50_codord,true,'text',1,"onChange=\"js_testa('f',this.value,'e50_codordINI','e50_codordFIM')\"","e50_codordFIM","");
                 ?>
                </td>
            </tr>
            <tr>
                <td nowrap align="left"><b>Apartir do Empenho:</b></td>
                <td  align="left" nowrap>
                 <?php
		   db_input('e50_numemp',16,$Ie50_numemp,true,'text',1,"onChange=\"js_testa('i',this.value,'e50_numempINI','e50_numempFIM')\"","e50_numempINI","");
                   echo " <b>Até:</b> ";
		   db_input('e50_numemp',16,$Ie50_numemp,true,'text',1,"onChange=\"js_testa('f',this.value,'e50_numempINI','e50_numempFIM')\"","e50_numempFIM","");
                 ?>
                </td>
            </tr>
            <tr>
              <td nowrap>
                <b>Ordem:</b>
              </td>
              <td>
                <?php
                  $op=array("e"=>"Empenho","d"=>"Data Empenho","l"=>"Data Lançamento","v"=>"Valor","c"=>"Credor");
                  db_select("sOrdem",$op,true,"text");
                ?>
              </td>
            </tr>

            <tr>
               <td nowrap width="50%">
                    <?php
                      // $aux = new cl_arquivo_auxiliar;
                      $aux->cabecalho = "<strong>Credores</strong>";
                      $aux->codigo = "e60_numcgm"; //chave de retorno da func
                      $aux->descr  = "z01_nome";   //chave de retorno
                      $aux->nomeobjeto = 'credor';
                      $aux->funcao_js = 'js_mostra';
                      $aux->funcao_js_hide = 'js_mostra1';
                      $aux->sql_exec  = "";
                      $aux->func_arquivo = "func_cgm_empenho.php";  //func a executar
                      $aux->nomeiframe = "db_iframe_cgm";
                      $aux->localjan = "";
                      $aux->onclick = "";
                      $aux->db_opcao = 2;
                      $aux->tipo = 2;
                      $aux->top = 1;
                      $aux->linhas = 4;
                      $aux->vwhidth = 400;
                      $aux->funcao_gera_formulario();
                   ?>
               </td>
            </tr>



      </fieldset>
      <table align="center">
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" align = "center">
            <input  name="emite2" id="emite2" type="button" value="Emitir Relatório" onclick="js_emite();" >
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>

<?php
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>

</html>
