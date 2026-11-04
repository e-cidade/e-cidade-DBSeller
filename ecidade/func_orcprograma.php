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

use ECidade\Enum\Financeiro\Orcamento\TipologiaProgramaEnum;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_orcprograma_classe.php"));

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);

$clorcprograma = new cl_orcprograma;
$clorcprograma->rotulo->label();

$chave_o54_descr = isset($chave_o54_descr) ? stripslashes($chave_o54_descr) : '';

$get = (object)filter_input_array(INPUT_GET);
$iAnoSessao = isset($get->previsao) ? $get->ano : db_getsession('DB_anousu');
$sWhereAdicional = "o54_anousu = {$iAnoSessao}";

if (!empty($iAno)) {
    $iAno = isset($get->previsao) ? $get->ano : db_getsession('DB_anousu');
    $sWhereAdicional = "o54_anousu = {$iAno}";
}

if (!empty($iTipo) && $iTipo != 0) {
    $sWhereAdicional .= " and o54_tipoprograma = {$iTipo} ";
}

if (isset($programasTematicos)) {
    $tipologia = [TipologiaProgramaEnum::PROGRAMAS_FINALISTICOS, TipologiaProgramaEnum::PROGRAMAS_TEMATICOS];
    $tipos = implode(', ', $tipologia);
    $sWhereAdicional .= " and o54_tipoprograma in ($tipos) ";
}

if (isset($programasGestao)) {
    $tipologia = [TipologiaProgramaEnum::PROGRAMAS_APOIO_POLITICAS, TipologiaProgramaEnum::PROGRAMAS_GESTAO];
    $tipos = implode(', ', $tipologia);
    $sWhereAdicional .= " and o54_tipoprograma in ($tipos) ";
}

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0" align="center" cellspacing="0" bgcolor="#CCCCCC">
    <tr>
        <td height="63" align="center" valign="top">
            <table width="35%" border="0" align="center" cellspacing="0">
                <form name="form2" method="post" action="">
                    <tr>
                        <td width="4%" align="right" nowrap title="<?= $To54_programa ?>">
                            <?= $Lo54_programa ?>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("o54_programa", 4, $Io54_programa, true, "text", 4, "", "chave_o54_programa");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="4%" align="right" nowrap title="<?= $To54_descr ?>">
                            <?= $Lo54_descr ?>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("o54_descr", 40, $Io54_descr, true, "text", 4, "", "chave_o54_descr");
                            ?>
                        </td>
                    </tr>
                    <tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                            <input name="limpar" type="reset" id="limpar" value="Limpar">
                            <input name="Fechar" type="button" id="fechar" value="Fechar"
                                   onClick="parent.db_iframe_orcprograma.hide();">
                        </td>
                    </tr>
                </form>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
            <?php

            $chave_o54_descr = addslashes($chave_o54_descr);

            $campos = "
lpad(orcprograma.o54_programa::varchar ,4,'0'::varchar)::varchar as o54_programa ,
orcprograma.o54_descr,
orcprograma.o54_codtri,
orcprograma.o54_finali,
orcprograma.o54_anousu as db_o54_anousu,
case
when o54_tipoprograma in (0) then 'Revise a tipologia do programa'
 when o54_tipoprograma in (1,3) then 'Programa Temático'
 else 'Programas de Gestão, Manutenção e Serviços ao Estado'
 end::text db_tipologia
";

            if (!isset($pesquisa_chave)) {
                if (isset($chave_o54_programa)) {
                    if (!DBNumber::isInteger($chave_o54_programa)) {
                        $chave_o54_programa = '';
                    }
                }

                if (isset($chave_o54_programa) && (trim($chave_o54_programa) != "")) {
                    $sql = $clorcprograma->sql_query(null, null, $campos,
                        "o54_programa",
                        "{$sWhereAdicional} and o54_programa = $chave_o54_programa");

                } elseif (isset($chave_o54_descr) && (trim($chave_o54_descr) != "")) {
                    $sql = $clorcprograma->sql_query(null, null, $campos, "o54_programa",
                        "{$sWhereAdicional}   and o54_descr like '$chave_o54_descr%'
	                                   ");
                } else {
                    $sql = $clorcprograma->sql_query(null, "", $campos,
                        "o54_programa",
                        "{$sWhereAdicional}"
                    );
                }

                if (isset($chave_o54_descr)) {
                    $chave_o54_descr = str_replace("\\", "", $chave_o54_descr);
                }
                db_lovrot($sql, 15, "()", "", $funcao_js);
            } else {
                if ($pesquisa_chave != null && $pesquisa_chave != "") {
                    $result = $clorcprograma->sql_record($clorcprograma->sql_query_file(
                        null,
                        null,
                        $campos,
                        '',
                        " {$sWhereAdicional}  and o54_programa = $pesquisa_chave")
                    );
                    if ($clorcprograma->numrows != 0) {
                        db_fieldsmemory($result, 0);
                        echo "<script>" . $funcao_js . "('$o54_descr',false, '$db_tipologia');</script>";
                    } else {
                        echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
                    }
                } else {
                    echo "<script>" . $funcao_js . "('',false);</script>";
                }
            }
            ?>
        </td>
    </tr>
</table>
</body>
</html>
<?php
if (!isset($pesquisa_chave)) {
    ?>
    <script>
        (function () {

            if (document.getElementById('chave_o54_programa').value != '') {
                var oRegex = /^[0-9]+$/;
                if (!oRegex.test(document.getElementById('chave_o54_programa').value)) {
                    alert('Programa deve ser preenchido somente com números!');
                    document.getElementById('chave_o54_programa').value = '';
                    return false;
                }
            }

        })();
    </script>
    <?php
}
?>
<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
