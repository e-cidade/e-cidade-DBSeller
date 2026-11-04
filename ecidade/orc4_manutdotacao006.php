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

use ECidade\Financeiro\Orcamento\Service\AcompanhamentoDesembolsoDespesaService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_orcdotacao_classe.php"));
require_once(modification("classes/db_orctiporec_classe.php"));
require_once(modification("classes/db_orcdotacaocontr_classe.php"));
require_once(modification("classes/db_orcelemento_classe.php"));
require_once(modification("classes/db_orcparametro_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_liborcamento.php"));

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);
$labelBotao = 'Excluir';

$clorcdotacao = new cl_orcdotacao;
$clorctiporec = new cl_orctiporec;
$clorcdotacaocontr = new cl_orcdotacaocontr;
$clorcelemento = new cl_orcelemento;
$clorcparametro = new cl_orcparametro;
$db_opcao = 33;
$db_botao = false;
if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"]) == "Alterar") {
    db_inicio_transacao();
    $erro_trans = false;

    if ($erro_trans != true) {
        $rsDot = $clorcdotacaocontr->sql_record($clorcdotacaocontr->sql_query_file($o58_anousu, $o58_coddot));
        if ($clorcdotacaocontr->numrows > 0) {
            $clorcdotacaocontr->excluir(null, "o61_coddot={$o58_coddot} and o61_anousu = {$o58_anousu}");
            if ($clorcdotacaocontr->erro_status == 0) {
                $erro_trans = true;
                $clorcdotacao->erro_msg = $clorcdotacaocontr->erro_msg;
            }
        }
    }

    $acompanhamento = new AcompanhamentoDesembolsoDespesaService();
    $acompanhamento->excluir($o58_anousu, $o58_coddot);

    $clorcdotacao->excluir($o58_anousu, $o58_coddot);
    if ($clorcdotacao->erro_status == 0) {
        $erro_trans = true;
    }

    db_fim_transacao($erro_trans);
    $db_opcao = 3;
} elseif (isset($chavepesquisa)) {
    $db_opcao = 3;
    $result = $clorcdotacao->sql_record($clorcdotacao->sql_query2($chavepesquisa, $chavepesquisa1));
    db_fieldsmemory($result, 0);
    $db_botao = true;
}
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script>
        function js_dotacao(obj) {
            document.form1.o58_orgao.value = obj.value.substr(0, 2);
            document.form1.o58_unidade.value = obj.value.substr(2, 2);
            document.form1.o58_funcao.value = obj.value.substr(4, 2);
            document.form1.o58_subfuncao.value = obj.value.substr(6, 3);
            document.form1.o58_programa.value = obj.value.substr(9, 4);
            document.form1.o58_projativ.value = obj.value.substr(13, 4);
            document.form1.o56_elemento.value = obj.value.substr(17, 13);

            js_pesquisao58_orgao(false);
            js_pesquisao58_unidade(false);
            js_pesquisao58_funcao(false);
            js_pesquisao58_subfuncao(false);
            js_pesquisao58_programa(false);
            js_pesquisao58_projativ(false);
            js_pesquisao58_codele(false);

        }
    </script>
</head>
<body>
<div class="container">
    <?php
    include(modification("forms/db_frmorcdotacao001.php"));
    ?>
</div>
</body>
</html>
<?php
if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"]) == "Incluir") {
    if ($clorcdotacao->erro_status == "0") {
        $clorcdotacao->erro(true, false);
        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
        if ($clorcdotacao->erro_campo != "") {
            echo "<script> document.form1." . $clorcdotacao->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clorcdotacao->erro_campo . ".focus();</script>";
        };
    } else {
        $clorcdotacao->erro(true, true);
    };
}
if ($db_opcao == 33) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
