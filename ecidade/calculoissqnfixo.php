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


// criar dentro do dbportal rotina chamada: calculo geral de issqn em procedimentos
// deve perguntar o ano atraves de uma lista/select assim como no calculo geral do iptu
// deve perguntar o tipo: 2-fixo 3-variavel (lista)
// deve perguntar os vencimentos da unica (somente qdo escolher 2-fixo), 
// permitindo ate 3 vencimentos, onde o usuario vai informar:
// 1-vencimento
// 2-percentual
// a rotina depois de calcular, deve procurar na isscalc, a inscricao e o tipo (cadcalc), pegar o numpre
// e inserir na recibounica, com o tipo "G", e no dtoper lancar a data atual
// nao esquecer de que nao sao obrigatorios todos os vencimentos, e pode ocorrer do usuario nao digitar nenhum
// vencimento
// dentro do select abaixo tem um in (2,3), que deve ser substituido pelo conteudo da variavel tipo de calculo,
// no caso, fixo ou variavel

// criar log de controle do calculo
// tabelas:
// 1-isscadlogcalc=iptucadlogcalc
// codigo integer/pk/sequence
// descr  varchar(40)
// erro   boolean
// 2-isscalclog=iptucalclog
// codigo integer/pk/sequence
// anousu integer
// data date
// hora char(5)
// usuario integer (fk->db_usuarios)
// parcial boolean
// quantaproc integer
// 3-isscalclogmat=iptucalclogmat
// codigo integer/pk/sequence
// inscr (campo principal q02_inscr) fk->issbase
// isscadlog (campo principal codigo da isscadlogcalc) integer fk->isscadlogcalc
// obs text

require(modification("libs/db_conn.php"));

$DB_BASE="sapiranga";
echo "\nbase de dados: $DB_BASE\n";
sleep(2);

if(!($conn = @pg_connect("host=$DB_SERVIDOR dbname=$DB_BASE port=$DB_PORTA user=$DB_USUARIO password=$DB_SENHA"))) {
  echo "Erro ao conectar com a base ".$DB_BASE;
  exit;
}

//$sql1 = "select tabativ.* from ativtipo inner join tipcalc on q80_tipcal = q81_codigo inner join tabativ on q07_ativ = q80_ativ inner join issbase on q07_inscr = q02_inscr where q02_dtbaix is null and q81_cadcalc in (2,3) and q07_databx is null and q07_datafi is null and q07_datain <= current_date order by q07_inscr";
$sql1 = "select distinct q07_inscr from tabativ left outer join tabativtipcalc on q11_inscr = q07_inscr and q11_seq = q07_seq inner join ativtipo on q07_ativ = q80_ativ inner join tipcalc on q80_tipcal = q81_codigo inner join ativid on q07_ativ = q03_ativ inner join cadcalc on q81_cadcalc = q85_codigo inner join issbase on q02_inscr = q07_inscr where q02_dtbaix is null and q81_cadcalc in (2,3) and q07_datain <= current_date and (q07_datafi is null or q07_datafi >= current_date) and (q07_databx is null or q07_databx >= current_date)";

$result1 = db_query($sql1);

for ($x = 0;$x < pg_numrows($result1);$x++) {
  db_fieldsmemory($result1,$x);

  echo "processando inscricao $q07_inscr - $x/" . pg_numrows($result1) . " - $q81_cadcalc\n";

  db_query("begin");

//  $sql2 = "create table ativs as select distinct q07_inscr, q07_perman, q07_seq, '*'::char(1) as q07_calcula, q07_ativ, q03_descr, q07_datain, q07_datafi, q07_databx, q07_quant, q11_tipcalc from tabativ left outer join tabativtipcalc on q11_inscr = q07_inscr and q11_seq = q07_seq inner join ativtipo on q07_ativ = q80_ativ inner join tipcalc on q80_tipcal = q81_codigo inner join ativid on q07_ativ = q03_ativ inner join cadcalc on q81_cadcalc = q85_codigo where q07_inscr =  $q07_inscr";
  $sql2 = "create temporary table ativs as select distinct q07_inscr, q07_perman, q07_seq, '*'::char(1) as q07_calcula, q07_ativ, q03_descr, q07_datain, q07_datafi, q07_databx, q07_quant, q11_tipcalc from tabativ left outer join tabativtipcalc on q11_inscr = q07_inscr and q11_seq = q07_seq inner join ativtipo on q07_ativ = q80_ativ inner join tipcalc on q80_tipcal = q81_codigo inner join ativid on q07_ativ = q03_ativ inner join cadcalc on q81_cadcalc = q85_codigo where q07_inscr = $q07_inscr and q07_datain <= current_date and (q07_datafi is null or q07_datafi >= current_date) and (q07_databx is null or q07_databx >= current_date)";
  $result2 = db_query($sql2);
  if ($result2 == false) {
    echo $sql2 . "\n";
    break;
  }

  $data=date('Y-m-d');
  $ano=date('Y');
  $instit=1;

  $idUsuario = db_getsession("DB_id_usuario");
  $sql3 = "select fc_issqn($idUsuario,$q07_inscr,'".$data."',".$ano.",null,'true','true',".$instit.") as retorno";
//  echo "<br>" . $sql3 . "<br>";exit;
  $result3=db_query($sql3);  
  if ($result3 == false) {
    echo "erro: \n$sql3\n";
    break;
  }
  db_fieldsmemory($result3,0);

  if($retorno == "ok"){
    echo "...ok\n";
    db_query("drop table ativs");
    db_query("commit");
    pg_close($conn);
    $conn = @pg_connect("host=$DB_SERVIDOR dbname=$DB_BASE port=$DB_PORTA user=$DB_USUARIO password=$DB_SENHA");
  }else{
    echo "\n$sql3\n";
    db_query("rollback");
    break;
  }

}

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

?>