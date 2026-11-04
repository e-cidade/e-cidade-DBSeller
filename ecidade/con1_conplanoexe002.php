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
parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);
$clconplanoexe = new cl_conplanoexe;
$db_opcao = 22;
$db_botao = false;
if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"]) == "Alterar") {
    db_inicio_transacao();
    $db_opcao = 2;

    $sql = "
    select c60_estrut
      from conplano
      join conplanoreduz  on  conplanoreduz.c61_codcon = conplano.c60_codcon and c61_anousu = c60_anousu
    where c61_anousu = {$_POST['c62_anousu']} and c61_reduz = {$_POST["c62_reduz"]}
    ";
    $rs = db_query($sql);
    $estrutural = db_utils::fieldsMemory($rs, 0)->c60_estrut;

    $clconplanoexe->alterar($_POST['c62_anousu'], $_POST["c62_reduz"]);

    $daoCC = new cl_conplanoexecontacorrente;
    $sql = $daoCC->sql_saldo_recurso($_POST["c62_reduz"], $_POST['c62_anousu'], $_POST['c62_codrec']);
    $rs = db_query($sql);

    $natureza = sinalContaBalanceteVerificacao(substr($estrutural, 0, 1), 0);

    $valor = 0;
    if ($_POST["c62_vlrcre"] != 0) {
        $valor = $_POST["c62_vlrcre"];
        $natureza = 'C';
    }

    if ($_POST["c62_vlrdeb"] != 0) {
        $valor = $_POST["c62_vlrdeb"];
        $natureza = 'D';
    }

    if ($rs && pg_num_rows($rs) === 0) {
        $daoCC->c143_conplanoreduz = $_POST["c62_reduz"];
        $daoCC->c143_exercicio = $_POST['c62_anousu'];
        $daoCC->c143_conplanosistema = 100;
        $daoCC->c143_saldo = $valor;
        $daoCC->c143_natureza = $natureza;
        $daoCC->incluir(null);

        if ($daoCC->erro_status == 0) {
            throw new DBException('Erro ao incluir saldo inicial por recurso.');
        }

        $daoCA = new cl_conplanoexecontacorrenteatributo();

        $daoCA->c144_conplanoexecontacorrente = $daoCC->id;
        $daoCA->c144_conplanoinfocomplementar = 100;
        $daoCA->c144_valor = $_POST['c62_codrec'];
        $daoCA->incluir(null);
        if ($daoCA->erro_status == 0) {
            throw new DBException('Erro ao incluir recurso do saldo inicial.');
        }
    } else {
        $dados = db_utils::fieldsMemory($rs, 0);
        $daoCC->id = $dados->id;
        $daoCC->c143_saldo = $valor;
        $daoCC->c143_natureza = $natureza;
        $daoCC->alterar($dados->id);
    }


    db_fim_transacao();
} else if (isset($chavepesquisa) && ($chavepesquisa != '')) {
    $db_opcao = 2;
    $anousu = db_getsession("DB_anousu");
    $result = $clconplanoexe->sql_record($clconplanoexe->sql_descr($anousu, $chavepesquisa));
    if ($clconplanoexe->numrows > 0) {
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
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
$login = db_getsession('DB_login');
if ($login != 'dbseller') {
    db_msgbox("Procedimento liberado apenas para login dbseller");
} else {
    require_once(modification("forms/db_frmconplanoexe.php"));
}

db_menu();
?>
</body>
</html>
<?php
if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"]) == "Alterar") {
    if ($clconplanoexe->erro_status == "0") {
        $clconplanoexe->erro(true, false);
        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
        if ($clconplanoexe->erro_campo != "") {
            echo "<script> document.form1." . $clconplanoexe->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clconplanoexe->erro_campo . ".focus();</script>";
        };
    } else {
        $clconplanoexe->erro(true, true);
    };
};
if ($db_opcao == 22) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
