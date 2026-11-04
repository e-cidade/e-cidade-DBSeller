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

	$clrotulo   = new rotulocampo();
	$db_opcao   = 1;
	
	$clrotulo->label("s110_i_codigo");
	$clrotulo->label("z01_nome");
	$clrotulo->label("sd63_c_procedimento");
	$clrotulo->label("sd63_c_nome");
	$tobserva = 18;
	?>
	<html>
	<head>
	<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
function js_abre(){
   var obj = document.form1;

	 // monta variáveis do período inicial    
	 var perini = new String();
	 perini = document.form1.perini.value;

	 // monta variáveis do período inicial 
	 var perfim = new String();
	 perfim = document.form1.perfim.value;

	 var query='';
   query += "&perini="+perini;
   query += "&perfim="+perfim;
   query += "&protocolados="+obj.protocolados.value;
   query += "&prestadora="+obj.s110_i_codigo.value;
   query += "&exame="+obj.sd63_c_procedimento.value;
   query += "&producao="+obj.producao.value;

   var jan = window.open('sau2_ag_exames002.php?'+query,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
   jan.moveTo(0,0);
}
</script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="document.form1.m40_codigo_ini.focus();" >
 
  <center>
    <form name="form1">
    <table style="margin-top: 50px;">
    <tr><td>
    <fieldset style="width:600px; top:50px">
    <legend><b>Relatório de Exames</b></legend>

      <table>   
	    <tr>
			  <td>
          <b> Período: </b>
				</td>
				<td>
          <?php 
	          db_inputdata('perini','','','',true,'text',1,"");
						echo " Até: ";   		          
	          db_inputdata('perfim','','','',true,'text',1,"");   		          
          ?>
			  </td>
      </tr>
			<tr>
				<td><b>Protocolados:</b></td>
				<td>
				<?php 
      		$aProtocolados = array(	"1"=>"SIM",
      										 				"2"=>"NÂO",
      													);
      		db_select('protocolados',$aProtocolados,false,1);
      	?>
				</td>
			</tr>
      	<tr>
				<td>
		    <?php
		       db_ancora('<b>Prestadoras:</b>',"js_pesquisa_prestadoras(true);",$db_opcao);
		    ?>
		    </td>
		    <td nowrap> 
		     <?php
		      db_input('s110_i_codigo',10,$Is110_i_codigo,true,'text',$db_opcao," onchange='js_pesquisa_prestadoras(false);'")
		     ?>
		     <?php
		      db_input('z01_nome',40,@$Iz01_nome,true,'text',3,'')
		     ?>
		    </td>
			</tr>
			<tr>
				<td>
		    <?php
		       db_ancora('<b>Exames:</b>',"js_pesquisa_exames(true);",$db_opcao);
		    ?>
		    </td>
		    <td nowrap> 
		     <?php
		      db_input('sd63_c_procedimento',10,$Isd63_c_procedimento,true,'text',$db_opcao," onchange='js_pesquisa_exames(false);'")
		     ?>
		     <?php
		      db_input('sd63_c_nome',40,@$Isd63_c_procedimento,true,'text',3,'')
		     ?>
		    </td>
			</tr>
				
			<tr>
				<td><b>Por Produção:</b></td>
				<td>
				<?php 
      		$aProducao = array("1"=>"Nao",
      						   "2"=>"Sim",
      									);
      		db_select('producao',$aProducao,false,1);
      	?>
				</td>
			</tr>
    </table>
		
    
    </fieldset>
    
    </td></tr>
    </table>
    <input name="pesquisar" align="center" type="button" value="Imprimir" onclick="js_abre();" /> 
    <input name="limpar" align="center" type="reset" value="Cancelar" /> 
    </form>

   </center>
    <?php 
      db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
    ?>
</body>
</html>
<script>
function js_pesquisa_prestadoras(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_sau_prestadoras','func_sau_prestadores.php?funcao_js=parent.js_mostraprestadores1|s110_i_codigo|z01_nome','Pesquisa',true);
  }else{
     if(document.form1.s110_i_codigo.value != ''){ 
        js_OpenJanelaIframe('','db_iframe_sau_prestadoras','func_sau_prestadores.php?pesquisa_chave='+document.form1.s110_i_codigo.value+'&funcao_js=parent.js_mostraprestadores','Pesquisa',false);
     }else{
       document.form1.s131_i_codigo.value = ''; 
     }
  }
}
function js_mostraprestadores(chave,erro){
  document.form1.z01_nome.value = chave; 
  if(erro=='true'){ 
    document.form1.s110_i_codigo.focus(); 
    document.form1.s110_i_codigo.value = ''; 
  }
}
function js_mostraprestadores1(chave1,chave2){
  document.form1.s110_i_codigo.value = chave1;
  document.form1.z01_nome.value = chave2;
  db_iframe_sau_prestadoras.hide();
}
function js_pesquisa_exames(mostra){
  if(mostra==true){
    js_OpenJanelaIframe(
      '',
      'db_iframe_sau_procedimento',
      'func_sau_procedimento.php?funcao_js=parent.js_mostra_exames1|sd63_c_procedimento|sd63_c_nome|sd63_c_procedimento'
        + '&lProcedimentosAgendamento&lProcedimentosVinculadosPrestadora', 
      'Pesquisa',
      true
    );
  }else{
     if(document.form1.sd63_c_procedimento.value != ''){ 
        js_OpenJanelaIframe('',
          'db_iframe_sau_procedimento',
          'func_sau_procedimento.php?pesquisa_chave=' + document.form1.sd63_c_procedimento.value
                                                + '&funcao_js=parent.mostraProcedimentos&lProcedimentosAgendamento'
                                                + '&lProcedimentosVinculadosPrestadora',
          'Pesquisa',
          false
        );
     }else{
       document.form1.sd63_c_nome.value = ''; 
     }
  }
}
function js_mostra_exames(chave,erro){
  document.form1.sd63_c_nome.value = chave; 
  if(erro=='true'){ 
    document.form1.sd63_c_procedimento.focus(); 
    document.form1.sd63_c_procedimento.value = ''; 
  }
}
function js_mostra_exames1(chave1,chave2){
  document.form1.sd63_c_procedimento.value = chave1;
  document.form1.sd63_c_nome.value = chave2;
  db_iframe_sau_procedimento.hide();
}

</script>