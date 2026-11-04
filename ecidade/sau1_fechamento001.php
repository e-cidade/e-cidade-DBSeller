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
include(modification("classes/db_sau_fechamento_classe.php"));
include(modification("classes/db_sau_fechapront_classe.php"));
include(modification("classes/db_sau_arquivos_classe.php"));
include(modification("classes/db_prontproced_classe.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("dbforms/db_classesgenericas.php"));
require(modification("libs/db_utils.php"));
db_postmemory($HTTP_POST_VARS);
$clsau_fechamento = new cl_sau_fechamento;
$clsau_fechapront = new cl_sau_fechapront;
$clsau_arquivos = new cl_sau_arquivos;
$clprontproced = new cl_prontproced;
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$sd97_i_login   = DB_getsession("DB_id_usuario");
$sd97_d_data_dia    = date("d",db_getsession("DB_datausu"));
$sd97_d_data_mes    = date("m",db_getsession("DB_datausu"));
$sd97_d_data_ano    = date("Y",db_getsession("DB_datausu"));
$verifica=false;

function somarDias($sd97_d_datafim ,$dias){
  $sd97_d_datafim  = explode("/",$sd97_d_datafim );
  $dia = (int)$sd97_d_datafim [0];
  $mes = (int)$sd97_d_datafim [1];
  $ano = (int)$sd97_d_datafim [2];
  $sd97_d_datafim = date ("d-m-Y",mktime (0,0,0,$mes,$dia+$dias,$ano)); 
  return $sd97_d_datafim ;
  }


$db_opcao = 1;
$db_botao = true;
$db_opcao1=1;
if(isset($opcao)){
 $db_botao1 = true;
 if( $opcao == "alterar"){
 	$db_opcao = 2;
    $result1=$clsau_fechamento->sql_record($clsau_fechamento->sql_query(""," sd97_i_compmes||'/'||sd97_i_compano as sd97_i_compmes,sd97_d_dataini,sd97_d_datafim,sd97_c_descricao, sd97_c_tipo,sd97_i_codigo","sd97_i_codigo desc limit 3","" ));			   	 
	if($clsau_fechamento->numrows>0){					
	   $obj1 = db_utils::fieldsMemory( $result1, 0 ); ///tipo aberta
	   $obj2 = db_utils::fieldsMemory( $result1, 1 ); ///segundo codigo = codigo da alteracao
	   $obj3= db_utils::fieldsMemory( $result1,  2);	   
	   $verifica=true;  	        
   	   $result = $clsau_fechamento->sql_record($clsau_fechamento->sql_query($sd97_i_codigo));	 
	   db_fieldsmemory($result,0);    		   	   
	  if($obj3->sd97_c_tipo == "Fechada" && $sd97_i_codigo==$obj3->sd97_i_codigo){ 	
	  db_msgbox("Não pode alterar");
	   $verifica= false; 
	   $sd97_d_datafim=""; 		   	  
	   $sd97_d_datafim_dia="";
	   $sd97_d_datafim_mes=""; 
	   $sd97_d_datafim_ano="";  
	   }	   
    }
 }
}
//termina classe alterar excluir


if(isset($incluir)){
  db_inicio_transacao();
     //inclusao fechamento 
 if($sd97_i_codigo!=""){
  if(isset($sd97_d_datafim) && $sd97_d_datafim!=""){
  	//funcao alterar 
    $clsau_fechamento->sd97_c_tipo=$sd97_c_tipo;
    $clsau_fechamento->alterar($sd97_i_codigo);
	
    //funcao incluir fechapront
    $result2 = $clprontproced->sql_record($clprontproced->sql_query("","*","","sd29_d_data BETWEEN '$sd97_d_dataini' and '$sd97_d_datafim'"));
   if($clprontproced->numrows>0){
   	db_fieldsmemory($result2,0);
   } 

   for($a=0;$a<$clprontproced->numrows;$a++){
	  db_fieldsmemory($result2,$a);	 
	  $clsau_fechapront->sd98_i_prontproced=$sd29_i_codigo;
	  $clsau_fechapront->sd98_i_fechamento=$clsau_fechamento->sd97_i_codigo;
	  $clsau_fechapront->incluir(null);
   }  
   if(isset($sd97_d_datafim)&& $sd97_d_datafim!=""){
     $clsau_fechamento->sd97_d_dataini= somarDias($sd97_d_datafim,1);
   }
   if($sd97_i_compmes==12){
     $sd97_i_compmes =1;	
	 $sd97_i_compano++;			
   }else{
   	$sd97_i_compmes++;
   }   
  }
   $clsau_fechamento->sd97_c_descricao= " ";
   $clsau_fechamento->sd97_d_datafim= "null";
   $clsau_fechamento->sd97_c_tipo="Aberta";
   $clsau_fechamento->sd97_i_compmes=$sd97_i_compmes;
   $clsau_fechamento->sd97_i_compano=$sd97_i_compano;
   $clsau_fechamento->incluir(null);
 }else{ 
   if($sd97_c_tipo=="Aberta"){
   	  $clsau_fechamento->sd97_i_compmes=$sd97_i_compmes;
      $clsau_fechamento->sd97_i_compano=$sd97_i_compano;
      $clsau_fechamento->incluir(null);	  
   }else{
      $clsau_fechamento->sd97_i_compmes=$sd97_i_compmes;
      $clsau_fechamento->sd97_i_compano=$sd97_i_compano;
      $clsau_fechamento->incluir(null);
   if(isset($sd97_d_datafim)&& $sd97_d_datafim!=""){
      $clsau_fechamento->sd97_d_dataini= somarDias($sd97_d_datafim,1);
   }
   if($sd97_i_compmes==12){
     $sd97_i_compmes =1;	
	 $sd97_i_compano++;			
    }else{
   	 $sd97_i_compmes++;
    }   
    $clsau_fechamento->sd97_c_descricao= " ";
    $clsau_fechamento->sd97_d_datafim= "null";
    $clsau_fechamento->sd97_c_tipo="Aberta";
    $clsau_fechamento->sd97_i_compmes=$sd97_i_compmes;
    $clsau_fechamento->sd97_i_compano=$sd97_i_compano;
    $clsau_fechamento->incluir(null);
  }	
   }
  db_fim_transacao();
  
 }else if(isset($alterar)){
   db_inicio_transacao(); 
   if($sd97_c_tipo=="Aberta"){
   	$resultalt=$clsau_fechamento->sql_record($clsau_fechamento->sql_query("","sd97_d_datafim as datafim,sd97_c_tipo as tipoantes","sd97_i_codigo","sd97_i_codigo=$sd97_i_codigo" ));			   	 	
	db_fieldsmemory($resultalt,0); 
	if($tipoantes=="Fechada"){  
	   $result3=$clsau_fechamento->sql_record($clsau_fechamento->sql_query("","sd97_i_codigo as codigo","sd97_i_codigo","sd97_c_tipo='Aberta'" ));			   	 	
       db_fieldsmemory($result3,0);	   		  
	   $clsau_fechamento->excluir($codigo);
	 }		
	 $clsau_fechapront->excluir("","sd98_i_fechamento= $sd97_i_codigo");	  	  		  
	 $clsau_arquivos->excluir("","sd99_i_fechamento= $sd97_i_codigo");	
     $clsau_fechamento->alterar($sd97_i_codigo);	  
   }else{
 	$result2=$clsau_fechamento->sql_record($clsau_fechamento->sql_query("","sd97_d_datafim as datafim,sd97_c_tipo as tipoantes","sd97_i_codigo","sd97_i_codigo=$sd97_i_codigo" ));			   	 	
	db_fieldsmemory($result2,0);
	if($tipoantes=="Aberta"){		
	   $clsau_fechamento->alterar($sd97_i_codigo);
	  if($sd97_d_datafim!= ""){
       $clsau_fechamento->sd97_d_dataini= somarDias($sd97_d_datafim,1);
       if($sd97_i_compmes==12){
        $sd97_i_compmes =1;	
	    $sd97_i_compano++;			
       }else{
   	    $sd97_i_compmes++;
       } 
    	$clsau_fechamento->sd97_c_descricao= " ";
        $clsau_fechamento->sd97_d_datafim= "null";
        $clsau_fechamento->sd97_c_tipo="Aberta";
        $clsau_fechamento->sd97_i_compmes=$sd97_i_compmes;
        $clsau_fechamento->sd97_i_compano=$sd97_i_compano;
        $clsau_fechamento->incluir(null);
	  }  	
	}else{
	 if(db_formatar($datafim,'d') != $sd97_d_datafim){	
	   $result3=$clsau_fechamento->sql_record($clsau_fechamento->sql_query("","sd97_i_codigo as codigo","sd97_i_codigo","sd97_c_tipo='Aberta'" ));			   	 	
       db_fieldsmemory($result3,0);	  
	  $clsau_fechapront->excluir("","sd98_i_fechamento= $sd97_i_codigo");	  	  		  
	  $clsau_arquivos->excluir("","sd99_i_fechamento= $sd97_i_codigo");	 	 
	  $clsau_fechamento->excluir($codigo);	 	 
	  $clsau_fechamento->alterar($sd97_i_codigo);
	  if($sd97_d_datafim !=""){
	  if(isset($sd97_d_datafim)&& $sd97_d_datafim!=""){
       $clsau_fechamento->sd97_d_dataini= somarDias($sd97_d_datafim,1);
      }
      if($sd97_i_compmes==12){
         $sd97_i_compmes =1;	
	     $sd97_i_compano++;			
      }else{
   	    $sd97_i_compmes++;
      } 
	 $clsau_fechamento->sd97_c_descricao= " ";
     $clsau_fechamento->sd97_d_datafim= "null";
     $clsau_fechamento->sd97_c_tipo="Aberta";
     $clsau_fechamento->sd97_i_compmes=$sd97_i_compmes;
     $clsau_fechamento->sd97_i_compano=$sd97_i_compano;
     $clsau_fechamento->incluir(null);
	 db_msgbox("999");
	}
    }else{ 
      $clsau_fechamento->sd97_i_compmes=$sd97_i_compmes;
      $clsau_fechamento->sd97_i_compano=$sd97_i_compano;
      $clsau_fechamento->alterar($sd97_i_codigo);
   } 		
  }	
 }
 db_fim_transacao();
}  

//result fechamento
if($verifica==false){
$result = $clsau_fechamento->sql_record($clsau_fechamento->sql_query("","sd97_i_compmes,sd97_i_compano,sd97_d_dataini,sd97_c_descricao,sd97_c_tipo,sd97_i_codigo","sd97_i_codigo desc","sd97_c_tipo='Aberta'"));
  if($clsau_fechamento->numrows==0){
    $db_opcao1 = 1;
  }else{
    $db_opcao2 = 2;
    db_fieldsmemory($result,0);
  } 
 }



/////mensagem de inclusao efetuada com sucesso
if(isset($incluir)){
  if($clsau_fechamento->erro_status=="0"){
    $clsau_fechamento->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clsau_fechamento->erro_campo!=""){
      echo "<script> document.form1.".$clsau_fechamento->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clsau_fechamento->erro_campo.".focus();</script>";
    }
  }else{
    $clsau_fechamento->erro(true,true);
  }
}
if(isset($alterar)){
  if($clsau_fechamento->erro_status=="0"){
    $clsau_fechamento->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clsau_fechamento->erro_campo!=""){
      echo "<script> document.form1.".$clsau_fechamento->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clsau_fechamento->erro_campo.".focus();</script>";
    }
  }else{
    $clsau_fechamento->erro(true,true);
  }
}
if(isset($excluir)){
  if($clsau_fechamento->erro_status=="0"){
    $clsau_fechamento->erro(true,false);
  }else{
    $clsau_fechamento->erro(true,true);
  }
}
if($db_opcao==33){
  echo "<script>document.form1.pesquisar.click();</script>";
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
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
    <center>
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
	<br><br><br>
	<?php
	include(modification("forms/db_frmsau_fechamento.php"));
	?>
	</td>
  </tr>
</table>
</center>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
js_tabulacaoforms("form1","sd97_i_login",true,1,"sd97_i_login",true);
</script>