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
include(modification("classes/db_orcprojeto_classe.php"));
include(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_POST_VARS);
$clorcprojeto = new cl_orcprojeto;

$db_opcao = 1;
$db_botao = true;
if(isset($incluir)){
  db_inicio_transacao();
  $lErro = false;
  // inclui texto padrão
  $o39_texto = "Art 2. -  Para cobertura do Crédito aberto de acordo com o Art 1.,";
  $o39_texto.= " será usado como recurso as seguintes reduções orçamentárias:   ";

  if ($o39_usalimite == '0') {

    $clorcprojeto->erro_status = 0;
    $clorcprojeto->erro_campo  = 'o39_usalimite';
    $clorcprojeto->erro_msg    = "Usuário:\\nDecreto não incluido.\\nInforme se o projeto deve usar o limite da LOA";
    $lErro  = true;

  } else {
    if (preg_match('/\D/', trim($o39_numero)) || !(preg_replace('/\D/', '', trim($o39_numero)) > 0)) {
        $clorcprojeto->erro_msg = "Número do Decreto é de preenchimento obrigatório.";
        $clorcprojeto->erro_status = 0;
        $clorcprojeto->erro_campo  = 'o39_numero';
    } else if (!preg_match('/\d{1,2}\/\d{1,2}\/\d{2,4}/', trim($o39_data))) {
        $clorcprojeto->erro_msg = "Data do Decreto é de preenchimento obrigatório.";
        $clorcprojeto->erro_status = 0;
        $clorcprojeto->erro_campo  = 'o39_numero';
    } else {

        $clorcprojeto->o39_tipoproj = '1';
        $clorcprojeto->o39_texto = $o39_texto;
        $clorcprojeto->incluir($o39_codproj);
    }

  }

  db_fim_transacao($lErro);
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



    <center>
	<?php
	include(modification("forms/db_frmorcprojeto.php"));
	?>
    </center>


</body>
</html>
<?php
if(isset($incluir)){
  if($clorcprojeto->erro_status=="0"){
    $clorcprojeto->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clorcprojeto->erro_campo!=""){
      echo "<script> document.form1.".$clorcprojeto->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clorcprojeto->erro_campo.".focus();</script>";
    };
  }else{
    $clorcprojeto->erro(true,false);
    echo "<script>
           // parent.mo_camada('emissao');
           parent.location.href = 'orc1_orcsuplem001.php?chavepesquisa={$clorcprojeto->o39_codproj}';\n
          </script>
         ";

  };
};
?>
