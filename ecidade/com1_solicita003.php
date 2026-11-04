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
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("classes/db_pcparam_classe.php"));
$clpcparam = new cl_pcparam;
$clcriaabas = new cl_criaabas;
$db_opcao = 1;
$erro = false;
$result_tipo = $clpcparam->sql_record(
    $clpcparam->sql_query_file(null, "pc30_sugforn")
);
if ($clpcparam->numrows > 0) {
    db_fieldsmemory($result_tipo, 0);
} else {
    $erro = true;
}
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<table>
    <tr>
        <td>
            <?php
            if (isset($pc30_sugforn) && $pc30_sugforn == 't') {
                $clcriaabas->identifica = array(
                    "solicita" => "Solicitação",
                    "solicitem" => "Itens/Dotações",
                    "sugforn" => "Fornecedor sugerido"
                );
                $clcriaabas->src = array("solicita" => "com1_solicita006.php");
                $clcriaabas->title = array(
                    "solicita" => "Cadastro de solicitação de compras",
                    "solicitem" => "Cadastro de Itens e Dotações",
                    "sugforn" => "Cadastro de fornecedores sugeridos"
                );
                $clcriaabas->sizecampo = array(
                    "solicita" => "20",
                    "solicitem" => "20",
                    "sugforn" => "25"
                );
                $clcriaabas->disabled = array(
                    "solicitem" => "true",
                    "sugforn" => "true"
                );
            } else {
                $clcriaabas->identifica = array(
                    "solicita" => "Solicitação",
                    "solicitem" => "Itens/Dotações"
                );
                $clcriaabas->src = array("solicita" => "com1_solicita006.php");
                $clcriaabas->title = array(
                    "solicita" => "Cadastro de solicitação de compras",
                    "solicitem" => "Cadastro de Itens e Dotações"
                );
                $clcriaabas->sizecampo = array(
                    "solicita" => "20",
                    "solicitem" => "20"
                );
                $clcriaabas->disabled = array("solicitem" => "true");
            }
            $clcriaabas->cria_abas();
            ?>
        </td>
    </tr>
    <tr>
    </tr>
</table>
<?php
db_menu();
?>
</body>
</html>
<?php
if ($erro) {
    db_msgbox("Parâmetros do compras não configurados");
}
?>
