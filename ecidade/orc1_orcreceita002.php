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

use ECidade\Enum\Financeiro\Orcamento\EsferaOrcamentariaEnum;
use ECidade\Financeiro\Orcamento\Model\Receita;
use ECidade\Financeiro\Orcamento\Service\AcompanhamentoDesembolsoReceitaService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_conplanoreduz_classe.php"));
require_once(modification("classes/db_conplanoorcamentoanalitica_classe.php"));
require_once(modification("classes/db_orcreceita_classe.php"));
require_once(modification("classes/db_orcparametro_classe.php"));
require_once(modification("classes/db_orcfontes_classe.php"));
parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);
$nameBotao = 'alterar';
$labelBotao = 'Alterar';

$clconplanoorcamentoanalitica = new cl_conplanoorcamentoanalitica;
$clorcreceita = new cl_orcreceita;
$clorcfontes = new cl_orcfontes;
$clorcparametro = new cl_orcparametro;
$clestrutura = new cl_estrutura;
if (isset($atualizar)) {
    $db_opcao = 2;
    $db_botao = true;
} else {
    $db_opcao = 22;
    $db_botao = false;
}
$ano = db_getsession("DB_anousu");

try {
    db_inicio_transacao();

    if (isset($alterar)) {
        if (empty($unidadeOrcamentaria)) {
            throw new Exception("A Unidade Orçamentária deve informada.");
        }

        $esferaOrcamentaria = isset($o70_esferaorcamentaria) ? $o70_esferaorcamentaria : null;
        if (empty($esferaOrcamentaria) || !EsferaOrcamentariaEnum::isValid((int) $esferaOrcamentaria)) {
            throw new Exception("Esfera Orçamentária inválida ou não iformada.");
        }

        db_inicio_transacao();
        $sqlerro = false;
        $db_opcao = 2;
        $where = "o57_fonte='" . str_replace(".", "", $o50_estrutreceita) . "' and o57_anousu = {$ano}";
        $result = $clorcfontes->sql_record($clorcfontes->sql_query_file(
            null,
            null,
            "o57_codfon as o70_codfon",
            "",
            $where
        ));
        if ($clorcfontes->numrows == 0) {
            throw new Exception("Verifique o código da fonte!");
        }

        db_fieldsmemory($result, 0);
        $result = $clorcreceita->sql_record($clorcreceita->sql_query_file(
            null,
            null,
            "o70_codrec as codrec",
            "",
            "o70_anousu={$ano} and o70_codfon = {$o70_codfon} and o70_concarpeculiar = '{$o70_concarpeculiar}'"
        ));
        db_fieldsmemory($result, 0);
        if ($clorcreceita->numrows > 0 && $codrec != $o70_codrec) {
            $msg = "O código da fonte já está cadastrado para o ano: {$ano}!\n Código Reduzido: {$codrec}";
            throw new Exception($msg);
        }
        $unidade = substr($unidadeOrcamentaria, 2, strlen($unidadeOrcamentaria) - 1);
        $orgao = substr($unidadeOrcamentaria, 0, 2);

        $clorcreceita->o70_orcunidade = $unidade;
        $clorcreceita->o70_orcorgao = $orgao;
        $clorcreceita->o70_esferaorcamentaria = $o70_esferaorcamentaria;
        $clorcreceita->o70_codfon = $o70_codfon;
        $clorcreceita->alterar($o70_anousu, $o70_codrec);
        if ($clorcreceita->erro_status == 0) {
            throw new Exception($clorcreceita->erro_msg);
        }
        $erro_msg = $clorcreceita->erro_msg;

        // Faz update no recurso da conta conforme previsao
        $rs_conplanoreduz = $clconplanoorcamentoanalitica->sql_record(
            $clconplanoorcamentoanalitica->sql_query_file(
                null,
                null, "c61_reduz,c61_anousu", "c61_codcon",
                "c61_codcon=$o70_codfon and c61_instit=" . db_getsession("DB_instit")
            )
        );
        $rows = $clconplanoorcamentoanalitica->numrows;
        if ($rows > 0) {
            for ($x = 0; $x < $rows; $x++) {
                db_fieldsmemory($rs_conplanoreduz, $x);
                $clconplanoorcamentoanalitica->c61_instit = db_getsession("DB_instit");
                $clconplanoorcamentoanalitica->c61_codigo = $o70_codigo;
                $clconplanoorcamentoanalitica->c61_anousu = $c61_anousu;
                $clconplanoorcamentoanalitica->c61_reduz = $c61_reduz;
                $clconplanoorcamentoanalitica->alterar($c61_reduz, $c61_anousu);
                if ($clconplanoorcamentoanalitica->erro_status == 0) {
                    throw new Exception($clconplanoorcamentoanalitica->erro_msg);
                }
            }
        }

        $receita = new Receita();
        $receita->setAno($clorcreceita->o70_anousu)
            ->setReduzido($clorcreceita->o70_codrec)
            ->setValor($clorcreceita->o70_valor);

        $service = new AcompanhamentoDesembolsoReceitaService();
        $service->criar($receita);

        $db_botao = true;
        $db_opcao = 2;
        db_fim_transacao($sqlerro);

    } else if (isset($chavepesquisa)) {
        $db_opcao = 2;
        $sql1 = $clorcreceita->sql_query2($chavepesquisa, $chavepesquisa1);
        $result = $clorcreceita->sql_record($sql1);
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
            "o57_codfon='$o70_codfon' and o57_anousu = {$ano}"
        );
        $result = $clorcfontes->sql_record($sql);
        db_fieldsmemory($result, 0);
        $db_botao = true;

        if ($o70_valor > 0) {
            $db_opcao = 3;
        }

        if ($o70_valor == 0) {
            $where = "c74_anousu = {$chavepesquisa} and c74_codrec = {$chavepesquisa1}";
            $dao = new cl_conlancamrec();
            $sql = $dao->sql_query_file(null, '1', null, $where);
            $rs = db_query($sql);
            if (pg_num_rows($rs) > 0) {
                $db_opcao = 3;
            }
        }

        $dao = new cl_db_usuarios();
        $rs = db_query($dao->sql_query_file($_SESSION['DB_id_usuario'], 'administrador'));

        if (pg_num_rows($rs) > 0 && db_utils::fieldsMemory($rs, 0)->administrador == 1) {
            $db_opcao = 2;
        }

        $db_botao = $db_opcao === 2;
    }
} catch (Exception $e) {
    $sqlerro = true;
    $errox_msg = $e->getMessage();
    $erro_msg = $errox_msg;;
    $clorcreceita->erro_status = 0;
    db_fim_transacao(true);
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
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
    <?php
    include_once(modification("forms/db_frmorcreceita.php"));
    ?>
</div>
<?php
db_menu();
?>
</body>
</html>
<?php
if (isset($alterar)) {
    if ($sqlerro == true) {
        db_msgbox($erro_msg);
        if ($clorcreceita->erro_campo != "") {
            echo "<script> document.form1." . $clorcreceita->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clorcreceita->erro_campo . ".focus();</script>";
        };
    } else {
        db_msgbox($erro_msg);
        db_redireciona("orc1_orcreceita002.php");
    };
};
if ($db_opcao == 22) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
