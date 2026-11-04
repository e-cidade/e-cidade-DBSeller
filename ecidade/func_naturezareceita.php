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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);

$oRotulo = new rotulocampo();
$oRotulo->label('o57_fonte');

$ano = !empty($_GET['anoFuturo']) ? $_GET['anoFuturo'] : db_getsession('DB_anousu');
$where = ["o57_anousu = $ano", "fonterecurso.exercicio = $ano"];
if (!empty($_GET['natureza'])) {
    $where[] = "o57_fonte like '{$_GET['natureza']}%'";
}

if (!empty($_GET['anoFuturo'])) {
    $where[] = "
     exists( select 1 from orcamento.orcfontes as natureza
       where natureza.o57_fonte = orcfontes.o57_fonte
         and natureza.o57_anousu = {$_GET['anoFuturo']})
     ";
}

if (isset($_GET['semReceita'])) {
    $where[] = "o70_codrec is null";
}

$campos = "
    o57_codfon as db_codigo,
    o57_fonte,
    o57_descr,
    fonterecurso.gestao,
    fonterecurso.descricao,
    o200_descricao,
    nomeinst,
    c61_instit as db_instit,
    o15_codigo as db_idrecurso,
    o70_codrec as db_reduz
";

$sql = "
select {$campos}
  from orcfontes
  join conplanoorcamentoanalitica on (c61_codcon, c61_anousu) = (o57_codfon, o57_anousu)
  join orctiporec on orctiporec.o15_codigo = c61_codigo
  join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                   and fonterecurso.exercicio = orcfontes.o57_anousu
  join db_config on db_config.codigo = conplanoorcamentoanalitica.c61_instit
  join complementofonterecurso on complementofonterecurso.o200_sequencial = orctiporec.o15_complemento
  left join orcreceita on (o70_codfon, o70_anousu) = (o57_codfon, o57_anousu)
";

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
</head>
<body class='body-default'>
<div class="subcontainer">
    <form name="form2" method="post" action="" class="container">
        <fieldset>
            <legend>Dados para Pesquisa</legend>
            <table class="form-container">
                <tr>
                    <td title="<?= $To57_fonte ?>">
                        <label for="chave_o57_fonte"><?= $Lo57_fonte ?></label>
                    </td>
                    <td>
                        <?php db_input("o57_fonte", 20, $Io57_fonte, true, "text", 1, "", "chave_o57_fonte"); ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
        <input name="limpar" type="reset" id="limpar" value="Limpar">
        <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_natureza.hide();">
    </form>
</div>
<?php

if (!isset($pesquisa_chave)) {
    if (isset($chave_o57_fonte) && (trim($chave_o57_fonte) != "")) {
        $where[] = " o57_fonte like '{$chave_o57_fonte}%' ";
    }

    $sWhere = implode(" and ", $where);

    if (!empty($where)) {
        $sql .= " where " . implode(' and ', $where);
    }

    $sql .= " order by o57_fonte ";
    $repassa = array();
    if (isset($chave_o57_fonte)) {
        $repassa = array("chave_o57_fonte" => $chave_o57_fonte);
    }
    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
    echo '  </fieldset>';
    echo '</div>';
}
?>

</body>
</html>
<script>
    $('limpar').onclick = function () {
        $('chave_pl26_codigo').value = '';
        $('chave_pl26_descricao').value = '';
        $('pesquisar2').click();
    }
</script>
<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
