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
require_once(modification("classes/db_cnae_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clcnae = new cl_cnae;
$clisscnaeanexos = new cl_isscnaeanexos;

$db_opcao = 22;
$db_botao = false;
if($db_opcao != 1){
}
if(isset($alterar)){
  db_inicio_transacao();
  $db_opcao = 2;
  $clcnae->alterar($q71_sequencial);

    if (!empty($q178_issgscadanexos)) {
        // // // // // // // // // // // // //
        // Inclusão e Alteração de Vínculos //
        // // // // // // // // // // // // //
        $clisscnaeanexos->q178_sequencial = $q178_sequencial;
        $clisscnaeanexos->q178_cnae = $q71_sequencial;
        $clisscnaeanexos->q178_issgscadanexos = $q178_issgscadanexos;

        // Se $q178_data_fim vier como vazio, atribui vinculo ativo (data vazia) para isscnaeanexos
        if (!empty($q178_data_fim)) {
            $clisscnaeanexos->q178_data_fim = date("Y-m-d", strtotime(str_replace("/", "-", $q178_data_fim)));
        } else if (empty($q178_data_fim)) {
            $clisscnaeanexos->q178_data_fim = 'null';
        }

        // Se sequencial for vazio, inclusão de vinculos
        if (empty($q178_sequencial)) {
            // Se data for vazia (inclusão de vínculo ativo)
            if (empty($q178_data_fim)) {
                $sql = "SELECT * FROM issqn.isscnaeanexos 
                        INNER JOIN cnae on q178_cnae = $q71_sequencial and q178_data_fim IS NULL;
                ";
                $result = db_query($sql);
                db_fieldsmemory($result,0);

                // Se existir vínculo com data 'null'(ativo) altera o último registro com data 'null' para receber a data atual(hoje)
                if (pg_numrows($result) > 0) {
                    $dataAtual = db_getsession('DB_datausu');
                    $dataAtualFormatada = str_replace('/', '-', date('Y/m/d',$dataAtual));
                    $clisscnaeanexos->q178_data_fim = $dataAtualFormatada;

                    // Altera vínculo ativo(com data 'null') existente
                    $clisscnaeanexos->q178_sequencial = $q178_sequencial;
                    $clisscnaeanexos->q178_issgscadanexos = $q178_issgscadanexos;
                    $clisscnaeanexos->alterar($q178_sequencial);

                    // Depois de alterar atribui 'null' novamente na data para inclusão de um vínculo ativo(com data 'null')
                    $clisscnaeanexos->q178_sequencial = '';
                    $clisscnaeanexos->q178_issgscadanexos = $selectanexo;
                    $clisscnaeanexos->q178_data_fim = 'null';
                    $clisscnaeanexos->incluir(null);
                    // Se não existir vínculo com data 'null'(ativo), insere novo vínculo ativo
                } else {
                    $clisscnaeanexos->incluir(null);
                }

            // Se data não for vazia (inclusão de vínculo não ativo)
            } else {
                $sql = "SELECT * FROM issqn.isscnaeanexos WHERE q178_data_fim = '$clisscnaeanexos->q178_data_fim'";
                $result = db_query($sql);

                if (pg_numrows($result) > 0 ) {
                    db_msgbox("Vínculo já existente");
                } else {
                    $clisscnaeanexos->incluir(null);
                }
            }

            // Se sequencial não for vazio, alteração de vinculos
        } else if (!empty($q178_sequencial)) {
            // Se a data for vazio (vinculo ativo)
            if (empty($q178_data_fim)) {
                $sql = "SELECT * FROM issqn.isscnaeanexos WHERE q178_sequencial = $q178_sequencial AND q178_data_fim IS NULL";
            } else {
                $sql = "SELECT * FROM issqn.isscnaeanexos WHERE q178_sequencial = $q178_sequencial AND q178_data_fim = '$clisscnaeanexos->q178_data_fim'";
            }

            $result = db_query($sql);

            if (pg_numrows($result) >0 ) {
                db_msgbox("Vínculo já existente");
            } else {
                $clisscnaeanexos->alterar($q178_sequencial);
            }
        }

        if ($clisscnaeanexos->erro_status == "0") {
            throw new \Exception($clisscnaeanexos->erro_msg);
        }
    }

    db_fim_transacao();
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $result = $clcnae->sql_record($clcnae->sql_query($chavepesquisa));
   db_fieldsmemory($result,0);

   $db_botao = true;
}

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
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
<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
	<?php
	include(modification("forms/db_frmcnae.php"));
	?>
    </center>
	</td>
  </tr>
</table>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if(isset($alterar)){
  if($clcnae->erro_status=="0"){
    $clcnae->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clcnae->erro_campo!=""){
      echo "<script> document.form1.".$clcnae->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clcnae->erro_campo.".focus();</script>";
    }
  }else{
    $clcnae->erro(true,true);
  }
}
if($db_opcao==22){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
js_tabulacaoforms("form1","q71_estrutural",true,1,"q71_estrutural",true);

function setaTipo(tipo){
  if(tipo == 'A'){
     document.getElementById('Tipo').value = 'A';
     document.getElementById('trVinculo').show();
     document.getElementById('fieldsetListaVinculo').show();
  } else {
    document.getElementById('Tipo').value = 'S';
  }
 document.getElementById('Tipo').disabled = true;
}
</script>

<?php
  if($chave2 == '' ){
    echo "<script>setaTipo('S')</script>";
  } else {
  	echo "<script>setaTipo('A')</script>";
  }
?>
