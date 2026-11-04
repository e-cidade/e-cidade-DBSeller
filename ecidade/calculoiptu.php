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

require(modification("libs/db_conn.php"));

$DB_BASE="simula";
echo "\nbase de dados: $DB_BASE\n";
sleep(5);

if(!($conn = @pg_connect("host=$DB_SERVIDOR dbname=$DB_BASE port=$DB_PORTA user=$DB_USUARIO password=$DB_SENHA"))) {
  echo "Erro ao conectar com a base ".$DB_BASE;
  exit;
}

$sql1 = "select	* 
		from iptubase
		where j01_baixa is null";
$result1 = db_query($sql1) or die("sql: " . pg_ErrorMessage());
db_query("select fc_putsession('DB_Gerafinanc','0')");
for ($record1=0;$record1 < pg_numrows($result1);$record1++) {
  db_fieldsmemory($result1,$record1);
  echo "processando matricula $j01_matric...\n";

  $sql2 = "select fc_calculoiptu($j01_matric,2005,'t','t','t','t','f')";
  echo $sql2."\n";
  sleep(999999999);
  $result2 = db_query($sql2) or die("sql: " . pg_ErrorMessage());

  echo "\n";

}

echo "ok...";

$result = db_query("commit;");

function db_fieldsmemory($recordset,$indice,$formatar="",$mostravar=false){
  $fm_numfields = pg_numfields($recordset);
  for ($i = 0;$i < $fm_numfields;$i++){
    $matriz[$i] = pg_fieldname($recordset,$i);
	$aux = trim(pg_result($recordset,$indice,$matriz[$i]));
	if(!empty($formatar)) {
  	  switch(pg_fieldtype($recordset,$i)) {
	    case "float8":
	    case "float4":
	    case "float":
          ${$matriz[$i]} = number_format($aux,2,".","");
          if($mostravar==true) echo $matriz[$i]."->".${$matriz[$i]}."<br>";
		  break;
		case "date":
          if($aux!=""){
		    $data = explode("-",$aux);
		    ${$matriz[$i]} = $data[2]."/".$data[1]."/".$data[0];
		  }else{
		    ${$matriz[$i]} = "";
		  }
          if($mostravar==true) echo $matriz[$i]."->".${$matriz[$i]}."<br>";
		  break;
		default:
          ${$matriz[$i]} = $aux;		  		
          if($mostravar==true) echo $matriz[$i]."->".${$matriz[$i]}."<br>";
		  break;
	  }
	} else
  	  switch(pg_fieldtype($recordset,$i)) {
		case "date":
		  $datav = explode("-",$aux);
          $split_data = $matriz[$i]."_dia";
          global ${$split_data};
          $$split_data =  @$datav[2];	
          if($mostravar==true) echo $split_data."->".$$split_data."<br";
          $split_data = $matriz[$i]."_mes";
          global ${$split_data};
          $$split_data =  @$datav[1];	
          if($mostravar==true) echo $split_data."->".$$split_data."<br>";
          $split_data = $matriz[$i]."_ano";
          global ${$split_data};
          $$split_data =  @$datav[0];	 
          if($mostravar==true) echo $split_data."->".$$split_data."<br>";
          ${$matriz[$i]} = $aux;		  		
          if($mostravar==true) echo $matriz[$i]."->".${$matriz[$i]}."<br>";
		  break;
		default:
          ${$matriz[$i]} = $aux;		  		
          if($mostravar==true) echo $matriz[$i]."->".${$matriz[$i]}."<br>";
		  break;
	  }
  }
}