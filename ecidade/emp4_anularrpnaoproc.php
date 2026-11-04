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
include(modification("classes/db_db_contatos_classe.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_orcdotacao_classe.php"));
include(modification("classes/db_orctiporec_classe.php"));
include(modification("classes/db_empempenho_classe.php"));
include(modification("classes/db_empelemento_classe.php"));
include(modification("classes/db_empanuladotipo_classe.php"));

$clempempenho      = new cl_empempenho;
$clempelemento     = new cl_empelemento;
$clorcdotacao      = new cl_orcdotacao;
$clorctiporec      = new cl_orctiporec;

//Checa parametro e mostra alerta de confirmacao de data
$clconparametro  = new cl_conparametro();
$rsconparametro  = $clconparametro->sql_record($clconparametro->sql_query_file(null, "c90_confirmadata"));
$conparametro    = db_utils::fieldsMemory($rsconparametro, 0);
if($conparametro->c90_confirmadata == 't'){
    $data = date('d/m/y', db_getsession('DB_datausu'));
    echo "<db-alertaconfirmadatafinanceiro data=" . $data . "></db-alertaconfirmadatafinanceiro>";
}

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/notaliquidacao.js"></script>
<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<script type="text/javascript" src="scripts/components/AlertaConfirmaDataFinanceiro.js"></script>
<link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
      rel="stylesheet"/>
<link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
      rel="stylesheet"/>
<link href="estilos.css" rel="stylesheet" type="text/css">
<link href="estilos.css" rel="stylesheet" type="text/css">
<link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table border="0" cellspacing="0" cellpadding="0">
  <tr height='25'>
    <td>&nbsp;</td>
      <center>
      </center>
    </td>
  </tr>
</table>
<center>
<?php
 include_once modification("forms/db_frmliquidarRPproc.php");
?>
</center>
</body>
</html>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),
        db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
