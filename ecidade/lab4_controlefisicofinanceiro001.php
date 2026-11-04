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

use ECidade\Saude\Laboratorio\Helper\ControleFisicoFinanceiroHelper;

require_once(modification('libs/db_stdlib.php'));
require_once(modification('libs/db_conecta.php'));
require_once(modification('libs/db_sessoes.php'));
require_once(modification('libs/db_usuariosonline.php'));
require_once(modification('libs/db_utils.php'));
require_once(modification('dbforms/db_funcoes.php'));

$queryString = [];
parse_str($_SERVER['QUERY_STRING'], $queryString);
db_postmemory($queryString);
db_postmemory($_POST);

$oDaoLabControleFisicoFinanceiro = new cl_lab_controlefisicofinanceiro;
$db_opcao = 1;
$db_botao = false;

$iTipoControle = ControleFisicoFinanceiroHelper::getTipoControleAtual();
if ($iTipoControle !== ControleFisicoFinanceiroHelper::CONTROLE_NAO_INFORMADO) {
    $iSelectControle = $iTipoControle;
}
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body marginwidth="0" marginheight="0" onLoad="a=1">
<div class="container" style="margin-top: 20px; width: 900px; height: 430px;">
    <fieldset style='width: 100%;'>
        <legend><b>Controle Físico / Financeiro</b></legend>
        <?php
        require_once(modification('forms/db_frmlab_controlefisicofinanceiro.php'));
        ?>
    </fieldset>
</div>
<?php
db_menu();
?>
</body>
</html>
<script>
js_tabulacaoforms("form1","la56_i_laboratorio",true,1,"la56_i_laboratorio",true);
</script>
