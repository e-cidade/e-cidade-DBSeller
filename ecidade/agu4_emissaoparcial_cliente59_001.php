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
include(modification("classes/db_aguabase_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
$claguabase = new cl_aguabase;
$claguabase->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label('z01_nome');
$clrotulo->label('z01_numcgm');
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
function js_verificacalculo(){
   //if(document.form1.x01_matric.value == ""){
   //  alert('Informe uma Matrícula.');
	 //return false;
   //}
   return true;
}

</script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="document.form1.x01_matric.focus();" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="100%" align="center" valign="top" bgcolor="#CCCCCC">
   <form name="form1" action="" method="post" onSubmit="return js_verificacalculo();">
	    <table width="387" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td align="right" height="25" title="<?=$Tz01_nunmcgm?>"> 
              <?php
				db_ancora('<strong>Matricula:</strong>','js_mostranomes(true);',4)
				?>
            &nbsp;
            </td>
            <td width="360" height="25"> 
              <?php
				db_input("x01_matric",8,$Ix01_matric,true,'text',4," onchange='js_mostranomes(false);' ")
				?>
            </td>
          </tr>
          <tr>
            <td align="right" height="25">
              <?php
				db_ancora('<strong>Nome:</strong>','js_mostranomes(true);',4)
				?>
            &nbsp;
            </td>
            <td height="25">
              <?php
				db_input("z01_nome",40,$Iz01_nome,true,'text',3)
				?>
            </td>
          </tr>

          <tr>
            <td align="right" height="25">
				    <strong>Ano:&nbsp;</strong>
            </td>
            <td height="25">
              <?php
	            $result=db_query("select " . db_getsession("DB_anousu") . "as j18_anousu");
	            if(pg_numrows($result) > 0){
		          ?>
		          <select name="anousu">
		          <?php
  	          for($i=0;$i<pg_numrows($result);$i++){
		            db_fieldsmemory($result,$i);
	              ?>
	              <option value='<?=$j18_anousu?>'><?=$j18_anousu?></option>
	              <?php
	              }
		          ?>
		        </select>
		        <?php
	          }
	          ?>
          </td>
          </tr>

          <tr>
					<td align="right" height="25">
					<strong>Mes Inicial:&nbsp;</strong>
					</td>
					<td height="25">
					<?php
          if(!isset($mesini)){
            $mesini = db_subdata(db_getsession("DB_datausu"),"m","t");
          }
					$result=array("1"=>"Janeiro","2"=>"Feveireiro","3"=>"Março","4"=>"Abril","5"=>"Maio","6"=>"Junho","7"=>"Julho","8"=>"Agosto","9"=>"Setembro","10"=>"Outubro","11"=>"Novembro","12"=>"Dezembro");
					db_select("mesini",$result,true,1,"","","","","");
          ?>
          </td>
          </tr>

          <tr>
					<td align="right" height="25">
					<strong>Mes Final:&nbsp;</strong>
					</td>
					<td height="25">
					<?php
          if(!isset($mesfim)){
            $mesfim = db_subdata(db_getsession("DB_datausu"),"m","t");
          }
					$result=array("1"=>"Janeiro","2"=>"Feveireiro","3"=>"Março","4"=>"Abril","5"=>"Maio","6"=>"Junho","7"=>"Julho","8"=>"Agosto","9"=>"Setembro","10"=>"Outubro","11"=>"Novembro","12"=>"Dezembro");
					db_select("mesfim",$result,true,1,"","","","","");
          ?>
          </td>
          </tr>

		      <tr>
          <td align="right" height="25">
            <strong>Tipo de Emissao:&nbsp;</strong>
          </td>
  	      <td>
			   <?php 
           //$xy = array ("pdf" => "PDF - Pré-impresso", "txt" => "TXT - Arquivo Gráfica");
           $xy = array ("txt" => "TXT - Arquivo Gráfica");
           db_select('tipo_emissao', $xy, true, 1);
         ?>      
	       </td>
         </tr>
				
	      <tr>
          <td align="right" height="25">
            <strong>Qtd registros a processar:&nbsp;</strong>
          </td>
  	      <td>
			   <?php 
				  db_input("qtdreg",8,"",true,'text',4,"")
         ?>      
         <strong>&nbsp;* deixe em branco para processar todas</strong>
	       </td>
         </tr>
				
          <tr> 
            <td height="25">&nbsp;</td>
            <td height="25"> <input name="processar"  type="submit" id="calcular" value="Processar">
              <?php
			  if(isset($calcular)){
			    ?>
              <input name="Limpar"  type="button" id="limpr" value="Limpar" onClick="document.form1.x01_matric.value='';document.form1.z01_nome.value=''">
                <input name="ultimo"  type="button" id="ultimo" value="&Uacute;ltimo C&aacute;lculo" onClick="func_nome.show();  func_nome.focus();">
              <?php
			  }
			  ?>
            </td>
          </tr>
	  <tr>
	    <td colspan=3>
	      <textarea id="text_demo" name="text_demo" rows=20 cols=95 style="visibility:hidden"><?=$retorno_result?></textarea>
	    </td>
	  <tr>
        </table>
      </form>
     </td>
  </tr>
 <tr>
 </table>
</body>
</html>
<?php 
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
<script>
function js_mostranomes(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_aguabase','func_aguabase.php?funcao_js=parent.js_mostra1|x01_matric|z01_nome','Pesquisa',true,20);
  }else{
    if(document.form1.x01_matric.value != ''){ 
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_aguabase','func_aguabase.php?pesquisa_chave='+document.form1.x01_matric.value+'&funcao_js=parent.js_mostra','Pesquisa',false,0);
    }else{
      document.form1.z01_nome.value = ''; 
    }
  }
}
function js_mostra(chave,erro){
  document.form1.z01_nome.value = chave; 
  if(erro==true){ 
    document.form1.x01_matric.focus(); 
    document.form1.x01_matric.value = ''; 
  }
}
function js_mostra1(chave1,chave2){
  document.form1.x01_matric.value = chave1;
  document.form1.z01_nome.value = chave2;
  db_iframe_aguabase.hide();
}

</script>
<?php
/*
$func_nome = new janela('func_nome','');
$func_nome ->posX=1;
$func_nome ->posY=20;
$func_nome ->largura=770;
$func_nome ->altura=430;
$func_nome ->titulo="Emissao";
$func_nome ->iniciarVisivel = false;
$func_nome ->mostrar();

$claguabase->erro(true,false);
*/
if(isset($processar)){
  ?>
  <script>
    //js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe','agu4_emissaoparcial_cliente59_002.php?matricula=<?=$x01_matric?>&exercicio=<?=$anousu?>&parcela_ini=<?=$mesini?>&parcela_fim=<?=$mesfim?>&tipo_emissao=<?=$tipo_emissao?>&qtdreg=<?=$qtdreg?>','Emissao de Carnes',true,20);
    jan = window.open('agu4_emissaoparcial_cliente59_002.php?matricula=<?=$x01_matric?>&exercicio=<?=$anousu?>&parcela_ini=<?=$mesini?>&parcela_fim=<?=$mesfim?>&tipo_emissao=<?=$tipo_emissao?>&qtdreg=<?=$qtdreg?>', 'Emissao de Carnes', 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
    jan.moveTo(0, 0);
  
  </script>
  <?php
}

?>