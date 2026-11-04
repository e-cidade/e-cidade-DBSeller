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

include(modification("libs/db_conecta.php"));
include(modification("libs/db_stdlib.php"));
db_postmemory($HTTP_POST_VARS);
db_postmemory($HTTP_SERVER_VARS);
if(!isset($munic)){
  echo "<script>alert('Municipio Inválido.');";
  echo "location.href='index.php';</script>";
}

?>
<html>
<head>
<title>Documento sem t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<style type="text/css">
.tabcols {
  font-size:10px;
  color:black;
}
.tabcolscab {
  font-size:10px;
  color:blue;
}
.tabcolsrod {
  font-size:10px;
  color:red;
}
.tabcolsnome {
  font-size:9px;
  color:blue;
}
</style>
</head>


<body>
<p><font size="2" face="Arial, Helvetica, sans-serif"><a href="relatorios.php?munic=<?=$munic?>">Retornar 
  aos Relat&oacute;rios</a></font></p>
<?php
if(!isset($emite)){
?>
<form name="form1" method="post" action="">
  <table width="100%" border="0" cellspacing="0">
    <tr> 
      <td width="42%">Ordem:</td>
      <td width="56%">Filtro:</td>
    </tr>
    <tr> 
      <td height="102"> <p> 
          <input name="ordem" type="radio" value="cgcte" checked>
          Codigo CGCTE <br>
          <input type="radio" name="ordem" value="razao">
          Razão Social <br>
          <input type="radio" name="ordem" value="fatur">
          Faturamento <br>
          <input type="radio" name="ordem" value="origem">
          Tipo<br>
          <input type="radio" name="ordem" value="ref001">
          Funcionario <br>
          <input type="radio" name="ordem" value="ref002">
          &Aacute;rea<br>
          <input type="radio" name="ordem" value="ref003">
          Kwa <br>
          <input type="radio" name="ordem" value="ref007">
          Sa&iacute;das <br>
          <input type="radio" name="ordem" value="ref011">
          Entradas<br>
          <input type="radio" name="ordem" value="ref012">
          Total</p></td>
      <td valign="top"><table width="100%" border="0" cellspacing="0">
          <tr> 
            <td>N&uacute;mero a Listar:&nbsp; <input name="numerolista" type="text" id="numerolista" size="10"></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td>Tipo de Apresenta&ccedil;&atilde;o:</td>
          </tr>
          <tr>
            <td><input type="radio" name="tipoapresenta" value="relatorio" checked>
              Relat&oacute;rio&nbsp;&nbsp; 
              <input type="radio" name="tipoapresenta" value="grafico" >
              Gr&aacute;fico</td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td><input type="radio" name="ordemtipo" value="asc" checked>
        Ascendente&nbsp;&nbsp; 
        <input type="radio" name="ordemtipo" value="desc">
        Descendente</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><input name="emite" type="submit" id="emite" value="Processar"></td>
      <td><input name="munic" type="hidden" value="<?=$munic?>"></td>
      <td width="2%">&nbsp;</td>
    </tr>
  </table>
</form>
<?php
exit;
}

if($tipoapresenta == 'relatorio'){

?>

<p><font size="2" face="Arial, Helvetica, sans-serif"></font></p>
<p> 
  <?php
$sql="select * from (select guiabc.cgcte, 
             razao,
			 fatur,
			 catego,
			 origem,
             sum(case when guiabv.ref = '001' then guiabv.valor else 0 end ) as ref001,
			 sum(case when guiabv.ref = '002' then guiabv.valor else 0 end ) as ref002,
			 sum(case when guiabv.ref = '003' then guiabv.valor else 0 end ) as ref003,
			 sum(case when guiabv.ref = '007' then guiabv.valor else 0 end ) as ref007,
			 sum(case when guiabv.ref = '011' then guiabv.valor else 0 end ) as ref011,
			 sum(case when guiabv.ref = '012' then guiabv.valor else 0 end ) as ref012
      from guiabc
	       inner join cadastro on guiabc.cgcte = cadastro.cgcte
	       inner join guiabv on guiabv.cgcte = guiabc.cgcte
	  where substr(guiabc.cgcte,1,3) = '$munic'
	  group by guiabc.cgcte, razao, fatur, catego ,origem) as x
	  order by $ordem $ordemtipo";
$numerolista = $numerolista + 0;
if($numerolista != 0)
  $sql = $sql . " limit $numerolista ";
?>
<font size='2'> 
<table style="size:'1'; font:'Arial, Helvetica, sans-serif'" width="100%" border="1" cellspacing="0">
  <tr> 
    <td class='tabcolscab' width="11%" height="23">Inscri&ccedil;&atilde;o</td>
    <td nowrap class='tabcolsnome' width="25%">Nome</td>
    <td class='tabcolscab'  align="right" width="8%">Faturamento</td>
    <td class='tabcolscab' width="6%">Tipo</td>
    <td class='tabcolscab'  align="right" width="7%">Func.</td>
    <td class='tabcolscab'  align="right" width="7%">&Aacute;rea</td>
    <td class='tabcolscab'  align="right" width="7%">Kwh</td>
    <td class='tabcolscab'  align="right" width="9%">Sa&iacute;das</td>
    <td class='tabcolscab'  align="right" width="10%">Entradas</td>
    <td class='tabcolscab' align="right"  width="10%">Total</td>
  </tr>
<?php
$result = db_query($sql);
if(pg_numrows($result)!=0){
  $tfatur = 0;
  $tcatego= 0;
  $tref001= 0;
  $tref002= 0;
  $tref003= 0;
  $tref007= 0;
  $tref011= 0;
  $tref012= 0;

  for($i=0;$i<pg_numrows($result);$i++){
     db_fieldsmemory($result,$i);
?>
  <tr> 
    <td class='tabcols' ><?=$cgcte?>&nbsp;</td>
    <td nowrap class='tabcolsnome' ><?=$razao?>&nbsp;</td>
    <td class='tabcols' align="right" ><?=number_format($fatur,2,",",".")?>&nbsp;</td>
    <td  class='tabcols' align="center"><?=number_format($catego,2,",",".")?>&nbsp;</td>
    <td class='tabcols' align="right" ><?=number_format($ref001,2,",",".")?>&nbsp;</td>
    <td class='tabcols'  align="right"><?=number_format($ref002,2,",",".")?>&nbsp;</td>
    <td class='tabcols'  align="right"><?=number_format($ref003,2,",",".")?>&nbsp;</td>
    <td class='tabcols'  align="right"><?=number_format($ref007,2,",",".")?>&nbsp;</td>
    <td class='tabcols'  align="right"><?=number_format($ref011,2,",",".")?>&nbsp;</td>
    <td class='tabcols'  align="right"><?=number_format($ref012,2,",",".")?>&nbsp;</td>
  </tr>
<?php

   $tfatur += $fatur;
   $tcatego+= $catego;
   $tref001+= $ref001;
   $tref002+= $ref002;
   $tref003+= $ref003;
   $tref007+= $ref007;
   $tref011+= $ref011;
   $tref012+= $ref012;

  }
  ?>
  <tr> 
    <td colspan="2" nowrap class='tabcolsrod' >Total ....</td>
    <td class='tabcolsrod' align="right" ><?=number_format($tfatur,2,",",".")?>&nbsp;</td>
    <td class='tabcolsrod' align="center"><?=number_format($tcatego,2,",",".")?>&nbsp;</td>
    <td class='tabcolsrod' align="right" ><?=number_format($tref001,2,",",".")?>&nbsp;</td>
    <td class='tabcolsrod'  align="right"><?=number_format($tref002,2,",",".")?>&nbsp;</td>
    <td class='tabcolsrod'  align="right"><?=number_format($tref003,2,",",".")?>&nbsp;</td>
    <td class='tabcolsrod'  align="right"><?=number_format($tref007,2,",",".")?>&nbsp;</td>
    <td class='tabcolsrod'  align="right"><?=number_format($tref011,2,",",".")?>&nbsp;</td>
    <td class='tabcolsrod'  align="right"><?=number_format($tref012,2,",",".")?>&nbsp;</td>
  </tr>
  <?php
}

echo "Total de Empresas:".pg_numrows($result);
?>
</table>
<?php
}else{
// grafico

}

?>
</font> 
</body>
</html>