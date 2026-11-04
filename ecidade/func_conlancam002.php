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
require_once(modification("classes/db_conlancamval_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_conlancam_classe.php"));
require_once(modification("classes/db_conlancamcompl_classe.php"));
require_once(modification("classes/db_conlancamdig_classe.php"));
require_once(modification("classes/db_conplano_classe.php"));

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);
db_postmemory($_GET);

$clconplano = new cl_conplano;
$clconlancamval = new cl_conlancamval;
$clconlancamcompl = new cl_conlancamcompl;
$clconlancamdig = new cl_conlancamdig;
$clconlancam = new cl_conlancam;

$db_opcao = 33;
$db_botao = false;
$anousu = db_getsession("DB_anousu");

$sWhere = " where  c75_numemp = $chavepesquisa ";

if (isset($e69_codnota)) {
    $sWhere = " where  c66_codnota=$e69_codnota ";
}


$displayRetencoes = "none";
$campoRetencao = "";
$joinRetencao = '';

if (APROPRIACAO_RETENCAO) {
    if (!empty($combo_opcoes) && $combo_opcoes == 'n') {
        $sWhere .= " and c127_conlancam is null ";
        // tiramos os doc 140 de transferencia devido as transferencias da folha
        $sWhere .= " and c71_coddoc not in (140) ";
    }
    $displayRetencoes = "";
    $campoRetencao = "case
                         when c127_conlancam is not null then 'Sim'
                         else 'Não'
                     end as \"dl_Lançamento_Retenção\",";
    $joinRetencao = ' left join conlancamretencao  on c127_conlancam = c70_codlan ';
}
if (isset($chavepesquisa)) {
    $sql = "
    select c70_codlan,
           c70_data,
	       c53_descr,
	       {$campoRetencao}
	       c70_valor,
	       c82_reduz,
           c60_descr,
	       c72_complem,
           e69_numero as dl_Nota_Fiscal,
           e50_codord,
           e50_data,
           nomeinstabrev as dl_Ente
     from conlancamemp
          inner join conlancam          on c70_codlan = c75_codlan
          inner join empempenho         on c75_numemp = e60_numemp
          inner join conlancamordem     on conlancamordem.c03_codlan = conlancam.c70_codlan
          left  outer join conlancampag on c82_codlan = c70_codlan
          inner join conlancamdoc       on c71_codlan   = c70_codlan
          inner join conhistdoc         on c53_coddoc     = c71_coddoc
          left join conlancamcompl      on c72_codlan  =c70_codlan
          left join conlancamnota       on c66_codlan  =c70_codlan
          left join conlancamord        on c80_codlan  =c70_codlan
          left join empnota             on c66_codnota = e69_codnota
          left join conplanoreduz       on c61_reduz = conlancampag.c82_reduz and c61_anousu=c70_anousu
          left join conplano            on c60_codcon = conplanoreduz.c61_codcon and c60_anousu=c61_anousu
          left join pagordem            on e50_codord  = c80_codord
          inner join conlancaminstit    on c02_codlan  = c70_codlan
          inner join db_config on c02_instit = db_config.codigo
         {$joinRetencao}";
}
$sql .= $sWhere;
$sql .= " order by c75_data, c03_ordem, c75_codlan ";
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script  type="text/javascript" src="scripts/scripts.js"></script>
    <script  type="text/javascript" src="scripts/prototype.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color: #CCCCCC;">
<center>

    <script>
        function js_conlancam(codlan) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_conlancam003', 'func_conlancam003.php?chavepesquisa=' + codlan, 'Pesquisa');
        }
    </script>

    <table>
        <tr style="display: <?= $displayRetencoes; ?>">
            <td>
                <label for="combo_opcoes">
                    <b> Listar lançamentos de retenções:</b>
                </label>
                <?php
                $a = array('s' => 'Sim', 'n' => 'Não');
                db_select('combo_opcoes', $a, true, 1, "onChange=\"location.href='func_conlancam002.php?chavepesquisa={$chavepesquisa}&combo_opcoes='+\$F('combo_opcoes')\"");
                ?>
            </td>
        </tr>
        <tr>
            <td>
                  <?php
                if (isset($sql)) {
                    $js_funcao = "parent.js_infoLancamento|c70_codlan";
                    db_lovrot($sql, 15, "()", "", $js_funcao, "", "form1", array(), false, array());
                }
                ?>
            </td>
        </tr>
    </table>
    <input type="hidden" name="chavepesquisa" value="<?= $chavepesquisa ?>">
</center>
</body>
</html>
<script>
</script>
<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();

</script>
