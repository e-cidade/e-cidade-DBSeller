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
$clrotulo = new rotulocampo;
$clrotulo->label('DBtxt23');
$clrotulo->label('DBtxt25');
$clrotulo->label('DBtxt27');
$clrotulo->label('DBtxt28');
db_postmemory($HTTP_POST_VARS);
?>

<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>

<script>
function js_emite(){
  jan = window.open('pes2_eldadiantamento002.php?&ano='+document.form1.DBtxt23.value+
                                           '&mes='+document.form1.DBtxt25.value
					   ,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
  jan.moveTo(0,0);
}
</script>  
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
  <table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr>
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>

  <table  align="center">
    <form name="form1" method="post" action="" >
      <tr>
         <td >&nbsp;</td>
         <td >&nbsp;</td>
      </tr>
      <tr >
        <td align="left" nowrap title="Digite o Ano / Mes de competência" >
        <strong>Ano / Mês :&nbsp;&nbsp;</strong>
        </td>
        <td>
          <?php
           $DBtxt23 = db_anofolha();
           db_input('DBtxt23',4,$IDBtxt23,true,'text',2,'')
          ?>
          &nbsp;/&nbsp;
          <?php
           $DBtxt25 = db_mesfolha();
           db_input('DBtxt25',2,$IDBtxt25,true,'text',2,'')
          ?>
        </td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" align = "center"> 
          <input  name="emite2" id="emite2" type="button" value="Relatório" onclick="js_emite();" >
          <input  name="proces" id="proces" type="submit" value="Lançar" onclick="js_emite1();" >
        </td>
      </tr>

  </form>
    </table>
<?php
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>

<?php
if(isset($proces)){
  echo "
  <script>
    if(confirm('Inicializando o Ponto de Adiantamento.\\nProcessar?')){
      obj=document.createElement('input');
      obj.setAttribute('name','confirma');
      obj.setAttribute('type','hidden');
      obj.setAttribute('value','confirma');
      document.form1.appendChild(obj);
      document.form1.DBtxt25.value = '$DBtxt25';
      document.form1.submit();
    }
  </script>
  ";

}
if(isset($confirma)){
  //echo ("select * from pontofs where r10_anousu = $DBtxt23 and r10_anousu = $DBtxt25 and r10_rubric = '0290'");
  $res_cons = db_query("select * from pontofa where r21_anousu = $DBtxt23 and r21_mesusu = $DBtxt25 and r21_instit = ".db_getsession('DB_instit'));
  
  
  if(pg_numrows($res_cons) > 0){
    db_query("delete from pontofa where  r21_anousu = $DBtxt23 and r21_mesusu = $DBtxt25 and r21_instit = ".db_getsession('DB_instit'));
  }
  
 $sql = "
select * from
(
select rh02_anousu,
       rh02_mesusu,
       rh02_instit,
       rh01_regist,
       rh27_rubric,
       rh02_lota,
       rh30_vinculo as vinculo,
       coalesce(conta_dias_afasta(rh02_regist,
                                  rh02_anousu,
                                  rh02_mesusu,
                                  ndias(rh02_anousu,rh02_mesusu),
                                  rh02_instit ),'0')::int as afasta,
       coalesce(dias_gozo_ferias( rh02_regist,
                                  rh02_anousu,
                                  rh02_mesusu,
                                  ndias(rh02_anousu, rh02_mesusu),
                                  rh02_instit),'0')::int as ferias
from rhpessoal
     inner join rhpessoalmov   on rh02_anousu = $DBtxt23
                              and rh02_mesusu = $DBtxt25
                              and rh02_regist = rh01_regist
                              and rh02_instit = ".db_getsession("DB_instit")."
     inner join rhregime       on rh30_codreg = rh02_codreg
                              and rh30_instit = rh02_instit
     left join rhpesrescisao   on rh05_seqpes = rh02_seqpes
     left join rhrubricas      on rh27_rubric = trim(rh01_clas1)
                              and rh27_instit = rh02_instit
where rh05_seqpes is null ) as x

where ferias <= 10
   and afasta = 0
   and vinculo = 'A'
   and rh27_rubric is not null

  ";
  $result = db_query($sql);
//  db_criatabela($result);
  db_query("begin");

  $msg_erro = 1;

  for($x = 0; $x < pg_numrows($result);$x++){
     db_fieldsmemory($result,$x);
     $sql_exec = "insert into pontofa values(
                               $rh02_anousu,
                               $rh02_mesusu,
				                       $rh01_regist,
				                      '$rh27_rubric',
				                       0,
				                       1,
				                      '$rh02_lota',
    				                  $rh02_instit)";
    $exec =  db_query($sql_exec);
    if($exec == false){
      echo "\n $rh01_regist ";
      echo "<script>
      alert('Erro ao inserir a matrícula $rh01_regist :  '+$sql);
      
      </script>";
      db_query("rollback");exit;
      $msg_erro = 0;
    }
  }
  if($msg_erro == 1){
      echo "<script> alert('Foram Processados $x Registros!'); </script>";
     db_query("commit");
  }
}
?>