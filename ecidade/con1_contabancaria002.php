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

$clcontabancaria = new cl_contabancaria;

$db_opcao = 22;
$db_botao = false;

if (isset($alterar)) {
    db_inicio_transacao();
    try {
        $db_opcao = 2;
        $daoBancoAgencia = new cl_bancoagencia();
        $sqlDadosBancos = $daoBancoAgencia->sql_query_file($db83_bancoagencia);
        $rsDadosBanco = db_query($sqlDadosBancos);
        if (pg_num_rows($rsDadosBanco) == 0) {
            throw new \Exception('Agência informada não cadastrada no sistema.');
        }
        $dadosAgencia = db_utils::fieldsMemory($rsDadosBanco, 0);
        if ($dadosAgencia->db89_db_bancos == '104' && empty($db83_codigooperacao)) {
            throw new \Exception('Campo Código da Operação deve ser informado.');
        }

        $clcontabancaria->alterar($db83_sequencial);

        db_fim_transacao();
    } catch (Exception $exception) {
        db_fim_transacao(true);
        $clcontabancaria->erro_campo = "db83_codigooperacao";
        $clcontabancaria->erro_status = "0";
        $clcontabancaria->erro_msg = $exception->getMessage();
    }
} elseif (isset($chavepesquisa)) {
    $db_opcao = 2;
    $result = $clcontabancaria->sql_record($clcontabancaria->sql_query($chavepesquisa));
    db_fieldsmemory($result, 0);
    $db_botao = true;
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
<body>
<?php
  include(modification("forms/db_frmcontabancaria.php"));
  db_menu();
?>
</body>
</html>
<?php
if (isset($alterar)) {
    if ($clcontabancaria->erro_status == "0") {
        $clcontabancaria->erro(true, false);
        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>  ";

        if ($clcontabancaria->erro_campo != "") {
            echo "<script> document.form1." . $clcontabancaria->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clcontabancaria->erro_campo . ".focus();</script>";
        }
    } else {
        $clcontabancaria->erro(true, true);
    }
}
if ($db_opcao == 22) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
    js_tabulacaoforms("form1", "db83_descricao", true, 1, "db83_descricao", true);
</script>
