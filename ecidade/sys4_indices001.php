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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');

db_postmemory($_GET);
db_postmemory($_POST);

parse_str(base64_decode($HTTP_SERVER_VARS['QUERY_STRING']), $queryString);

foreach ($queryString as $key => $value) {
    ${$key} = $value;
}

$tabela = isset($HTTP_POST_VARS["tabela"]) ? $HTTP_POST_VARS["tabela"] : $tabela;
$nomearq = db_query("select nomearq from db_sysarquivo where codarq = $tabela");
$nomearq = pg_result($nomearq, 0, 0);

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>DBSeller Informática Ltda - Página Inicial</title>
    <link rel="stylesheet" href="estilos.css">
    <script src="scripts/scripts.js"></script>
    <script>
        Botao = '';

        function js_submeter(obj) {
            if (Botao == 'atualizar') {
                if (obj.nome_ind.value == '') {
                    alert('Campo nome do indice é obrigatório');
                    obj.nome_ind.focus();
                    return false;
                }
                if (obj.alt_ind.value == '') {
                    alert('Campo \'campos\' não pode ser vazio.');
                    obj.alt_ind.focus();
                    return false;
                }
            }
            return true;
        }

        function js_campos_mostra() {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_campo',
                'sys4_chaveprim002.php?<?php echo base64_encode("tabela=$tabela") ?>', 'Pesquisa campo', true, '20', '160',
                '300', '300');
        }
    </script>
</head>
<body>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="padding-top: 5%">
    <tr>
        <td align="center" valign="top" bgcolor="#CCCCCC">
            <?php
            if (!isset($HTTP_POST_VARS["b_campo_ind"]) && !isset($HTTP_POST_VARS["excluir"])) {
                if (isset($ind)) {
                    $result = db_query("select i.campounico,i.nomeind as nome_indice,c.nomecam as nome_campo
			           from db_sysindices i
		               inner join db_syscadind ci
			           on ci.codind = i.codind
			           inner join db_syscampo c
			           on c.codcam = ci.codcam
			           where i.codind = $ind
			           order by ci.sequen");
                    $num_linhas = pg_numrows($result);
                    $nome_ind = pg_result($result, 0, "nome_indice");
                } ?>
                <form method="post" name="f_campo" onSubmit="return js_submeter(this)">
                    <fieldset style="width:500px">
                        <legend><b>Tabela: <?= @$nomearq ?></b></legend>
                        <table>
                            <tr>
                                <td>
                                    <strong>
                                        <label for="nome">Nome:</label>
                                    </strong>
                                </td>
                                <td>
                                    <input type="text" size="40" name="nome_ind" value="<?= @$nome_ind ?>" id="nome">
                                </td>
                            </tr>
                            <tr>
                                <td valign="top">
                                    <strong>
                                        <label for="campos">Campos:</label>
                                    </strong>
                                </td>
                                <td>
            <textarea rows="7" cols="37" name="alt_ind" id="campos">
                <?php

                if (isset($ind)) {
                    for ($i = 0; $i < $num_linhas; $i++) {
                        echo trim(pg_result($result, $i, "nome_campo")) . "\n";
                    }
                } ?>
            </textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Unico : </b>
                                </td>
                                <td>
                                    <input name="campounico" type="checkbox" id="campounico"
                                           value="unico"
                                        <?php

                                        echo @pg_result($result, 0, "campounico") == "0" ||
                                        @pg_result($result, 0, "campounico") == ""
                                            ? ""
                                            : "checked"; ?>
                                    >
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <input type="submit" onClick="Botao='atualizar'" name="b_campo_ind" value="Atualizar">
                    <input type="submit" name="excluir" value="Excluir"
                           OnClick="return confirm('Voce quer realmente excluir este registro?')">
                    <input type="button" value="Procurar" onClick="js_campos_mostra();" name="button">
                    <input type="button" name="voltar" value="Voltar"
                           onclick="location.href='sys3_campos001.php?<?= base64_encode("tabela=" . $GLOBALS["tabela"]) ?>'">
                    <input type="hidden" name="tabela" value="<?= @$tabela ?>">
                    <input type="hidden" name="ind"
                           value="<?= @(isset($HTTP_POST_VARS["ind"]) ? $HTTP_POST_VARS["ind"] : $ind) ?>">
                </form>
                <?php
            } else {
                if (!isset($HTTP_POST_VARS["excluir"])) {
                    db_postmemory($HTTP_POST_VARS);
                    if (isset($campounico)) {
                        $campounico = $campounico == "" ? "0" : "1";
                    } else {
                        $campounico = "0";
                    }
                    if ($ind == "") {
                        db_query("BEGIN");
                        $rsCodigoIndice = db_query("select nextval('db_sysindices_codind_seq') as codigo");
                        $iCodigoIndice = db_utils::fieldsMemory($rsCodigoIndice, 0)->codigo;
                        db_query("insert into db_sysindices values({$iCodigoIndice},'{$nome_ind}',{$tabela},'{$campounico}')") or die("Erro(94) inserindo em db_sysindices");
                        $result = db_query("select codind
                           from db_sysindices
                           where nomeind = '$nome_ind'") or die("Erro(97) selecionando db_sysindices");
                        $ind = pg_result($result, 0, 0);
                        $alt_ind =explode("\r\n", $alt_ind);
                        for ($i = 0; $i < sizeof($alt_ind) - 1; $i++) {
                            if ($alt_ind[$i] != "" && $alt_ind[$i] != " " && $alt_ind[$i] != "  ") {
                                $alt_ind[$i] = trim(str_replace("#", "", $alt_ind[$i]));
                                $alt_ind[$i] = trim(str_replace("#", "", $alt_ind[$i]));
                                $result = db_query("select codcam from db_syscampo where nomecam = '$alt_ind[$i]'") or die("Erro(102) selecionando db_syscampo");
                                $s = $i + 1;
                                db_query("insert into db_syscadind values($ind," . pg_result(
                                    $result,
                                    0,
                                    "codcam"
                                ) . ",$s)") or die("Erro(104) inserindo em db_syscadind");
                            }
                        }
                        db_query("END");
                        db_redireciona("sys3_campos001.php?" . base64_encode("tabela=$tabela"));
                    } else {
                        db_query("BEGIN");
                        db_query("update db_sysindices set nomeind = '$nome_ind',campounico = '$campounico' where codind = $ind") or die("Erro(111) atualizando db_sysindices");
                        db_query("delete from db_syscadind where codind = $ind") or die("Erro(112) excluindo db_syscadind");
                        $alt_ind =explode("\r\n", $alt_ind);
                        for ($i = 0; $i < sizeof($alt_ind) - 1; $i++) {
                            if ($alt_ind[$i] != "" && $alt_ind[$i] != " " && $alt_ind[$i] != "  ") {
                                $result = db_query("
					select codcam
					from db_syscampo
					where nomecam = '$alt_ind[$i]'");
                                $s = $i + 1;
                                $result = db_query($conn, "
					insert into db_syscadind
					values($ind," . pg_result(
                                    $result,
                                    0,
                                    "codcam"
                                    ) . ",$s)") or die("Erro(156) inserindo em db_syscadind");
                            }
                        }
                        db_query("END");
                        db_redireciona("sys3_campos001.php?" . base64_encode("tabela=$tabela"));
                    }
                } else {
                    if (isset($HTTP_POST_VARS["excluir"])) {
                        db_query("BEGIN");
                        db_query("delete from db_syscadind	where codind = $ind") or die("Erro(131) excluindo db_syscadind");
                        db_query("delete from db_sysindices where codind = $ind") or die("Erro(132) excluindo db_sysindices");
                        db_query("END");
                        db_redireciona("sys3_campos001.php?" . base64_encode("tabela=$tabela"));
                    }
                }
            }
            ?>
        </td>
    </tr>
</table>
<?php

db_menu(
    db_getsession("DB_id_usuario"),
    db_getsession("DB_modulo"),
    db_getsession("DB_anousu"),
    db_getsession("DB_instit")
);

?>
</body>
</html>
