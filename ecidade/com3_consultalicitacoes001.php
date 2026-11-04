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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));

$oGet = db_utils::postMemory($_GET, false);

if (!isset($oGet->pc10_numero) || trim($oGet->pc10_numero) == "") {

    $sMsgErro = urlencode("Pesquisa sem parâmetros.");
    db_redireciona('db_erros.php?fechar=true&db_erro=' . $sMsgErro);
}

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#cccccc" onload="">
<div id="gridContainer"></
</div>
</body>
</html>
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/datagrid/plugins/DBHint.plugin.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/messageboard.widget.js"></script>

<?php
$clliclicitem = new cl_liclicitem();
$clliclicita = new cl_liclicita();
$res_liclicitem = $clliclicitem->sql_record($clliclicitem->sql_query(null, "distinct l20_codigo", "l20_codigo", "pc11_numero=$oGet->pc10_numero"));
$dados = new stdClass();
$dados->erro = 1;
if ($clliclicitem->numrows > 0) {
    db_fieldsmemory($res_liclicitem, 0);
    $res_liclicita = $clliclicita->sql_record($clliclicita->sql_query($l20_codigo));
    if ($clliclicita->numrows > 0) {
        $dados->erro = 0;
        $dados->licitacoes = db_utils::getCollectionByRecord($res_liclicita);

    }
}
$dados = JSON::create()->stringify($dados);

?>
<script>
    let dados = <?php echo $dados ?>;

    oGridLicitacoes = new DBGrid('licitacoes');
    oGridLicitacoes.nameInstance = 'oGridLicitacoes';
    oGridLicitacoes.setCellWidth(new Array('20%', "20%", "20%", '20%', '20%'));
    oGridLicitacoes.setCellAlign(new Array('center', "left", "center", 'center', 'center'));
    oGridLicitacoes.setHeader(new Array('Licitação', "Objeto", "Local", 'Data de Criação', 'Modalidade'));
    oGridLicitacoes.setHeight(230);
    oGridLicitacoes.show($('gridContainer'));

    oGridLicitacoes.setStatus("Coloque o cursor sob a linha para obter mais informações.");

    preencheGrid(dados);


    function preencheGrid(dados) {
        if (dados.licitacoes !== null && dados.erro == 0) {
            oGridLicitacoes.clearAll(true);
            dados.licitacoes.each(function (dado, ind) {
                    let rowLicitacao = [];
                    rowLicitacao[0] = "<b><a href='#' onclick='js_consultaLicitacao(" + dado.l20_codigo + ")' title='Consultar Licitacao'>" + dado.l20_codigo + "</a></b>";
                    rowLicitacao[1] = dado.l20_objeto;
                    rowLicitacao[2] = dado.l20_local;
                    rowLicitacao[3] = js_formatar(dado.l20_datacria, "d");
                    rowLicitacao[4] = dado.l03_codigo + " - " + dado.l03_descr
                    oGridLicitacoes.addRow(rowLicitacao);
                }
            )
            oGridLicitacoes.renderRows();

            dados.licitacoes.each(function(dado,ind) {
                oGridLicitacoes.setHint(ind,1,dado.l20_objeto);
                oGridLicitacoes.setHint(ind,2,dado.l20_local);
                oGridLicitacoes.setHint(ind,4,dado.l03_codigo + " - " + dado.l03_descr)
                }
            )
        }


    }

    function js_consultaLicitacao(iCodigoLicitacao) {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_infolic', 'lic3_licitacao002.php?l20_codigo=' + iCodigoLicitacao, 'Pesquisa Licitação', true);
    }
</script>
