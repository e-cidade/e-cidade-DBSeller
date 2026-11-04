<?php
/**
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
require_once(modification("classes/db_lab_parametros_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

use ECidade\Saude\Laboratorio\Integracao\Luckmann\Enum\Parametros as ParametrosEnum;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Model\Parametros as ParametrosModel;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\Parametros as ParametrosService;

db_postmemory($_POST);

$parametrosService = new ParametrosService(
    ParametrosEnum::JSON_CONFIGURACOES,
    ParametrosEnum::JSON_CONFIGURACOES_INFINITY
);
$cllab_parametros = new cl_lab_parametros;

$db_opcao = 1;
$db_opcao1 = 1;
$db_botao = true;

if (isset($incluir)) {
    db_inicio_transacao();
    $cllab_parametros->incluir($la49_i_codigo);

    $parametrosModel = new ParametrosModel();

    $parametrosModel->setPastaPedidos($caminhoPedidos);
    $parametrosModel->setPastaResultados($caminhoResultados);
    $parametrosService->salvar($parametrosModel);

    $parametrosModel->setPastaPedidosInfinity($caminhoPedidosInfinity);
    $parametrosModel->setPastaResultadosInfinity($caminhoResultadosInfinity);
    $parametrosService->salvarJsonInfinity($parametrosModel);

    db_fim_transacao();
}

if (isset($alterar)) {
    db_inicio_transacao();
    $db_opcao1 = 3;
    $cllab_parametros->alterar($la49_i_codigo);

    $parametrosModel = new ParametrosModel();

    $parametrosModel->setPastaPedidos($caminhoPedidos);
    $parametrosModel->setPastaResultados($caminhoResultados);
    $parametrosService->salvar($parametrosModel);

    $parametrosModel->setPastaPedidosInfinity($caminhoPedidosInfinity);
    $parametrosModel->setPastaResultadosInfinity($caminhoResultadosInfinity);
    $parametrosService->salvarJsonInfinity($parametrosModel);

    db_fim_transacao();
}

$result = $cllab_parametros->sql_record($cllab_parametros->sql_query("", "*", "", ""));

if ($cllab_parametros->numrows == 0) {
    $db_opcao = 1;
} else {
    $db_opcao = 2;
    $db_opcao1 = 3;
    db_fieldsmemory($result, 0);
}

$parametrosModel = $parametrosService->getParametros();

$caminhoPedidos = $parametrosModel->getPastaPedidos();
$caminhoResultados = $parametrosModel->getPastaResultados();

$caminhoPedidosInfinity = $parametrosModel->getPastaPedidosInfinity();
$caminhoResultadosInfinity = $parametrosModel->getPastaResultadosInfinity();

?>
    <html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
        <link href="estilos.css" rel="stylesheet" type="text/css">
    </head>
    <body class="body-default">
    <?php
    include(modification("forms/db_frmlab_parametros.php"));
    db_menu();
    ?>
    </body>
    </html>
    <script>
        js_tabulacaoforms("form1", "la49_c_estrutural", true, 1, "la49_c_estrutural", true);
    </script>
<?php
if (isset($incluir) || isset($alterar)) {
    if ($cllab_parametros->erro_status == "0") {
        $cllab_parametros->erro(true, false);
        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>  ";

        if ($cllab_parametros->erro_campo != "") {
            echo "<script> document.form1.".$cllab_parametros->erro_campo.".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1.".$cllab_parametros->erro_campo.".focus();</script>";
        }
    } else {
        $cllab_parametros->erro(true, true);
    }
}
