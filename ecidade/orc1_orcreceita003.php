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

use ECidade\Financeiro\Orcamento\Service\AcompanhamentoDesembolsoReceitaService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_orcreceita_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("classes/db_orcparametro_classe.php"));
require_once(modification("classes/db_orcfontes_classe.php"));
$nameBotao = 'excluir';
$labelBotao = 'Excluir';

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);
$clorcreceita = new cl_orcreceita;
$clorcfontes = new cl_orcfontes;
$clorcparametro = new cl_orcparametro;
$clestrutura = new cl_estrutura;
$db_botao = false;
if (isset($atualizar)) {
    $db_opcao = 3;
    $db_botao = true;
} else {
    $db_opcao = 33;
    $db_botao = false;
}
if (isset($excluir)) {
    $sqlerro = false;
    $cltaborc = new cl_taborc();
    $where = "k02_codrec = {$o70_codrec} and k02_anousu = {$o70_anousu} ";
    $sqlReceita = $cltaborc->sql_query_file(null, null, "count(*) as total ", null, $where);
    $rsReceita = db_query($sqlReceita);
    if (!$rsReceita) {
        $sqlerro = true;
        $erro_msg = "Não foi possível verificar uso da receita";
    }
    $quantidadeReceitasTesouraria = (int)db_utils::fieldsMemory($rsReceita, 0)->total;
    if ($quantidadeReceitasTesouraria > 0) {
        $sqlerro = true;
        $erro_msg = "Não é possivel excluir o cadastro da receita, pois a mesma ja está em uso na tesouraria.";
    }
    db_inicio_transacao();

    if (!$sqlerro) {
        $db_opcao = 3;
        $service = new AcompanhamentoDesembolsoReceitaService();
        $service->excluir($o70_anousu, $o70_codrec);

        $clorcreceita->excluir($o70_anousu, $o70_codrec);
        if ($clorcreceita->erro_status == 0) {
            $sqlerro = true;
        }
        $erro_msg = $clorcreceita->erro_msg;
    }
    db_fim_transacao($sqlerro);
} else {
    if (isset($chavepesquisa)) {
        $db_opcao = 3;
        $result = $clorcreceita->sql_record($clorcreceita->sql_query2($chavepesquisa, $chavepesquisa1));
        db_fieldsmemory($result, 0);
        if (!empty($o70_orcorgao)) {

            $unidadeOrcamentaria = str_pad($o70_orcorgao, 2, "0", STR_PAD_LEFT);
            $unidadeOrcamentaria .= str_pad($o70_orcunidade, 2, "0", STR_PAD_LEFT);
            $unidadeOrcamentariaDescricao = $o41_descr;
        }
        $sql = $clorcfontes->sql_query_file(
            null,
            null,
            "o57_fonte as o50_estrutreceita",
            '',
            "o57_codfon='$o70_codfon' and o57_anousu = " . db_getsession("DB_anousu")
        );
        $result = $clorcfontes->sql_record($sql);
        db_fieldsmemory($result, 0);
        $db_botao = true;
    }
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
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
    <?php
    include(modification("forms/db_frmorcreceita.php"));
    ?>
</div>
<?php
db_menu();
?>
</body>
</html>
<?php
if (isset($excluir)) {
    db_msgbox($erro_msg);
    if ($sqlerro == false) {
        db_redireciona("orc1_orcreceita003.php");
    }
};
if ($db_opcao == 33) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
