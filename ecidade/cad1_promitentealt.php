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
require_once(modification("classes/db_promitente_classe.php"));
require_once(modification("classes/db_iptubase_classe.php"));

db_postmemory($_SERVER);
db_postmemory($_POST);

$db_botao  = 1;
$db_opcao  = 1;
$numrows00 = 0;

$cliptubase         = new cl_iptubase;
$clpropri           = new cl_propri;
$clpromitente       = new cl_promitente;
$cltipopromitente   = new cl_tipopromitente;
$cltipoproprietario = new cl_tipoproprietario;
$clrotulo           = new rotulocampo;
$rotulocampo        = new rotulocampo;

$cliptubase->rotulo->label();
$clpromitente->rotulo->label();
$cltipopromitente->rotulo->tlabel();
$rotulocampo->label("z01_nome");
$clrotulo->label("j01_numcgm");

$db_op        = $db_opcao;
$db_op02      = $db_opcao;
$outros       = false;

if(isset($alterando)){
  $j41_matric = $j01_matric;
}

if(isset($incluir)){

   db_inicio_transacao();

   $resultPrincipal = $clpromitente->sql_record($clpromitente->sql_query_file($j41_matric, "", "*", "", "j41_matric = {$j41_matric}"));
        if ((pg_num_rows($resultPrincipal) == 0) && $j41_tipopro == 'f') {
            $j41_tipopro = 't';
        }
        //Verifica se possui o mesmo cgm como proprietario pra mesma matricula.
        $result = $cliptubase->sql_record($cliptubase->sql_query_file($j41_matric, "*", "", "j01_matric = {$j41_matric} AND j01_numcgm = {$j41_numcgm}"));
        //Verifica se possui o mesmo cgm como proprietario pra mesma matricula.
        $result = $clpropri->sql_record($clpropri->sql_query_file($j41_matric, $j41_numcgm));
        //Verifica se a Matricula possui responsável principal
        $result = $clpromitente->sql_record($clpromitente->sql_query_file($j41_matric, "", "*", "", "j41_matric = {$j41_matric} AND j41_tipopro = 't' AND j41_numcgm <> {$j41_numcgm}"));
        $hasPromitentePrincipalCadastrado = $clpromitente->numrows > 0;

        if ($cliptubase->numrows > 0) {
            db_msgbox("O cgm não pode ser o mesmo cadastrado como proprietário principal nesta matricula.");
        } else if ($clpropri->numrows > 0) {
            db_msgbox("O cgm não pode ser o mesmo cadastrado como proprietário secundário nesta matricula.");
        } else if (($hasPromitentePrincipalCadastrado) && ($j41_tipopro == 't')) {
            db_msgbox("Deve haver apenas um promitente principal.");
        }else {
            $getPromitipo = db_query('select j164_promitipo from tipopromitente where j164_tipopromitente = ' . $j164_tipopromitente);
            db_fieldsmemory($getPromitipo, 0);
            $clpromitente->j41_numcgm = $j41_numcgm;
            $clpromitente->j41_tipopro = $j41_tipopro;
            $clpromitente->j41_promitipo = $j164_promitipo;
            $clpromitente->j41_tipopromitente = $j164_tipopromitente;
            $clpromitente->incluir($j41_matric, $j41_numcgm);
        }
        if ($clpromitente->erro_status == "0") {
            db_msgbox($clpromitente->erro_msg);
            $outros = true;
        }

   db_fim_transacao();

}else if(isset($alterar)){

   db_inicio_transacao();

   if ($anoMatricula == $anoRetroativoMatricula) {
            db_query("select fc_putsession('DB_anoretroativo', '{$anoRetroativoMatricula}')");
        }

        if ($liberaCalculoRetroativo AND $replicaDadosAnos == 1) {
            $calculoRetroativoIptuRepository->setAnoRetroativoMatricula($anoMatricula);
            $calculoRetroativoIptuRepository->getAlteraSearchPath();
        }

        db_inicio_transacao();
        //Verifica se possui o mesmo cgm como proprietario pra mesma matricula.
        $result = $cliptubase->sql_record($cliptubase->sql_query_file($j41_matric, "*", "", "j01_matric = {$j41_matric} AND j01_numcgm = {$j41_numcgm}"));
        //Verifica se possui o mesmo cgm como proprietario pra mesma matricula.
        $result = $clpropri->sql_record($clpropri->sql_query_file($j41_matric, $j41_numcgm));
        //Verifica se a Matricula possui responsável principal
        $result = $clpromitente->sql_record($clpromitente->sql_query_file($j41_matric, "", "*", "", "j41_matric = {$j41_matric} AND j41_tipopro = 't' AND j41_numcgm <> {$j41_numcgm}"));
        $hasPromitentePrincipalCadastrado = $clpromitente->numrows > 0;

        if ($cliptubase->numrows > 0) {
            db_msgbox("O cgm não pode ser o mesmo cadastrado como proprietário principal nesta matricula.");
        } else if ($clpropri->numrows > 0) {
            db_msgbox("O cgm não pode ser o mesmo cadastrado como proprietário secundário nesta matricula.");
        } else if (($hasPromitentePrincipalCadastrado) && ($j41_tipopro == 't')) {
            db_msgbox("Deve haver apenas um promitente principal.");
        } else {
            $getPromitipo = db_query('select j164_promitipo from tipopromitente where j164_tipopromitente = ' . $j164_tipopromitente);
            db_fieldsmemory($getPromitipo, 0);
            $clpromitente->j41_numcgm = $j41_numcgm;
            $clpromitente->j41_tipopro = $j41_tipopro;
            $clpromitente->j41_promitipo = $j164_promitipo;
            $clpromitente->j41_tipopromitente = $j164_tipopromitente;
            $clpromitente->alterar($j41_matric, $j41_numcgm);
        }

        if ($clpromitente->erro_status == "0") {
            db_msgbox($clpromitente->erro_msg);
            $outros = true;
        }
   db_fim_transacao();
   
}else if(isset($excluir)){
    $clpromitente->excluir($j41_matric,$cgmexclusao);
}else if(isset($j41_matric) && isset($j41_numcgm)){  

   $result = $clpromitente->sql_record($clpromitente->sql_query($j41_matric,"","promitente.*#cgm.z01_nome#a.z01_nome as z01_nomematri","","j41_numcgm=$j41_numcgm and j41_matric = $j41_matric "));
   db_fieldsmemory($result,0);

   if($j41_tipopro=='t'){

      $db_op='3';
      $clpromitente->sql_record($clpromitente->sql_query($j41_matric,"","promitente.*#cgm.z01_nome#a.z01_nome as z01_nomematri","","j41_matric = $j41_matric "));
      if($clpromitente->numrows>1){    
       $db_op02='3';
      } 
   }
   $db_opcao=2;
   $result = $clpromitente->sql_record($clpromitente->sql_query($j41_matric,"","cgm.z01_nome"));
   $numcgm=$j41_numcgm;

   if($clpromitente->numrows > 0){

     $outros=true;
     $recol="ok";
   }
}else if(isset($j41_matric) && !isset($j41_numcgm)){  

  $result = $clpromitente->sql_record($clpromitente->sql_query($j41_matric,"","j41_promitipo#j41_tipopro#a.z01_nome as z01_nomematri","",""));
  $numrows00=$clpromitente->numrows;     
   if($clpromitente->numrows>0){
      @db_fieldsmemory($result,0);
	$db_opcao=1;
      $outros=true;
   }else{

      $result = $cliptubase->sql_record($cliptubase->sql_query($j41_matric,"z01_nome as z01_nomematri",""));
      @db_fieldsmemory($result,0);
        $db_opcao=1;
   }
}  
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">

  </head>
  <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="js_trocacordeselect()">

  <br /><br />

  <table height="430" align="center" width="790" border="0" cellspacing="0" cellpadding="0">
  <form id='form1' name="form1" method="post" onSubmit="return js_verifica_campos_digitados();" action="">
    <tr>
      <td align="left" valign="top" bgcolor="#CCCCCC">
       <center> 
       <?php
         include(modification("forms/db_frmpromitentealt.php"));
       ?> 
      </center>
      </td>
    </tr>
</form>
</table>
</body>
</html>
<?php
if(isset($incluir) || isset($alterar)){
  if($clpromitente->erro_status=="0"){
    $clpromitente->erro(true,false);
    if($clpromitente->erro_campo!=""){
      echo "<script> document.form1.".$clpromitente->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clpromitente->erro_campo.".focus();</script>";
    }
  }else{
    $clpromitente->erro(true,false);
    db_redireciona("cad1_promitentealt.php?j41_matric=$j41_matric&z01_nomematri=$z01_nomematri");
  }
}
?>