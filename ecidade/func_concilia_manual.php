<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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


require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_saltes_classe.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$clsaltes = new cl_saltes;
$clsaltes->rotulo->label("k13_conta");
$clsaltes->rotulo->label("k13_descr");
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"
      onload='document.form2.chave_k13_conta.focus();'>
<table height="100%" border="0" align="center" cellspacing="0" bgcolor="#CCCCCC">
    <tr>
        <td height="63" align="center" valign="top">
            <table width="35%" border="0" align="center" cellspacing="0">
                <form name="form2" method="post" action="">
                    <tr>
                        <td width="4%" align="right" nowrap title="Reduzido"><strong>Reduzido:</strong></td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("k13_conta", 5, $Ik13_conta, true, "text", 4, "", "chave_k13_conta");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="4%" align="right" nowrap title="<?= $Tk13_descr ?>"><?= $Lk13_descr ?></td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("k13_descr", 40, $Ik13_descr, true, "text", 4, "", "chave_k13_descr");
                            ?>
                        </td>
                    </tr>
                    <?php
                    if (isset($c61_codigo) && trim($c61_codigo) != 1) {
                        ?>
                        <tr>
                            <td width="4%" align="right" nowrap title="Disponibilizar Recursos Livres"><b>Disponibilizar
                                    Recursos Livres:</b></td>
                            <td width="96%" align="left" nowrap>
                                <?php
                                $x = array("N" => "NÃO", "S" => "SIM");
                                db_select("disp_rec", $x, true, 4, "");
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="2" align="center">
                            <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                            <input name="limpar" type="reset" id="limpar" value="Limpar">
                            <input name="Fechar" type="button" id="fechar" value="Fechar"
                                   onClick="parent.db_iframe_saltes.hide();">
                        </td>
                    </tr>
                </form>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
            <?php

            $dbwhere = "";
            /** [Extensao FiltroDespesa] - modificacao 1 */

            if ( APROPRIACAO_RETENCAO ) {
                $dbwhere .= " and c60_codsis in (5,6) ";
            }

            if (isset($c61_codigo) && trim($c61_codigo) != "") {

                $dbwhere .= " and c61_codigo = {$c61_codigo} ";
                if (isset($disp_rec) && trim($disp_rec) == "S") {
                    $dbwhere .= " or c61_codigo = 1 ";
                }
            }

            if (isset($lFiltroContaBanco) && $lFiltroContaBanco == true) {
                $dbwhere .= " and c63_codcon is not null ";
            }
            if (isset($lFiltroContaPagadora) && $lFiltroContaPagadora == true) {
                $dbwhere .= " and e83_conta is null ";
            }

            if (isset($ver_datalimite) && trim(@$ver_datalimite) == "1") {
                $dbwhere .= " and (k13_limite is null or k13_limite >= '" . date("Y-m-d",
                        db_getsession("DB_datausu")) . "')";
            }

            if (!isset($pesquisa_chave)) {

                if (isset($campos) == false) {


                    $campos = "DISTINCT saltes.k13_conta,
                                        saltes.k13_reduz,
                                        saltes.k13_descr,
                                        c63_banco,
                                        c63_agencia,
                                        c63_dvagencia,
                                        c63_conta,
                                        c63_dvconta,
                                        c61_codcon,
                                        c61_reduz,
                                        c61_anousu,
                                        k68_sequencial as seq,
                                        k68_data,
                                        k68_conciliastatus
                                        ";
                }

/*
		$dbwhere .= " and k13_reduz in ( select distinct c56_reduz from concilia
  inner join conciliastatus on conciliastatus.k95_sequencial = concilia.k68_conciliastatus
  inner join contabancaria on contabancaria.db83_sequencial = concilia.k68_contabancaria
  inner join bancoagencia on bancoagencia.db89_sequencial = contabancaria.db83_bancoagencia
  inner join conplanocontabancaria on c56_contabancaria = db83_sequencial and c56_anousu = " . db_getsession("DB_anousu") . "
  inner join conplano on c60_codcon = c56_codcon and c60_anousu = c56_anousu
  inner join conplanoreduz on c61_codcon = c60_codcon )";
*/


                if (isset($chave_k13_conta) && (trim($chave_k13_conta) != "")) {
                    $sql = $clsaltes->sql_query_conciliacontabancaria($chave_k13_conta, $campos, "",
                        " k13_conta = {$chave_k13_conta } and c61_instit = " . db_getsession("DB_instit") . $dbwhere);
                } else {
                    if (isset($chave_k13_descr) && (trim($chave_k13_descr) != "")) {
                        $sql = $clsaltes->sql_query_conciliacontabancaria("", $campos, "k13_descr",
                            " k13_descr like '{$chave_k13_descr}%' and c61_instit = " . db_getsession("DB_instit") . $dbwhere);
                    } else {
                        $sql = $clsaltes->sql_query_conciliacontabancaria(null, $campos, "",
                            "c61_instit = " . db_getsession("DB_instit") . $dbwhere);
                    }
		}
        //echo $sql;

                $sql = "
                         select distinct
                         k13_conta as dl_reduzido,
                         k13_descr,
                         c63_banco,
                         c63_agencia,
                         c63_dvagencia,
                         c63_conta,
                         c63_dvconta,
                         max(k68_data)as k68_data,
                         'ABERTO' as status
                         from ({$sql}) as dd
                         group by k13_conta,
                                  k13_reduz,
                                  k13_descr,
                                  c63_banco,
                                  c63_agencia,
                                  c63_dvagencia,
                                  c63_conta,
                                  c63_dvconta,
                                  status
                        ";
                    //    echo $sql;
                db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", array(), false);
            } else {

                if ($pesquisa_chave != null && $pesquisa_chave != "") {

                    $result = $clsaltes->sql_record($clsaltes->sql_query_conciliacontabancaria(null, "*", "",
                        " k13_conta = {$pesquisa_chave} and c61_instit = " . db_getsession("DB_instit") . $dbwhere));
                    if ($clsaltes->numrows != 0) {

                        db_fieldsmemory($result, 0);
                        echo "<script>" . $funcao_js . "('$k13_descr',false);</script>";
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

