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
require_once(modification("classes/db_solicitem_classe.php"));
require_once(modification("classes/db_liclicita_classe.php"));
require_once(modification("classes/db_pcproc_classe.php"));
require_once(modification("classes/db_pcparam_classe.php"));
require_once(modification("classes/db_liclicitem_classe.php"));
db_postmemory($HTTP_GET_VARS);
db_postmemory($HTTP_POST_VARS);
$clsolicitem = new cl_solicitem;
$clpcproc = new cl_pcproc;
$clpcparam = new cl_pcparam;
$clliclicitem = new cl_liclicitem;
$clliclicita = new cl_liclicita;
$clacordo = new cl_acordo;
$clsolicita = new cl_solicita;
$clpcorcam = new cl_pcorcam;
$clrotulo = new rotulocampo;
$clrotulo->label("pc10_numero");
$clrotulo->label("pc10_data");
$clrotulo->label("pc10_resumo");
$clrotulo->label("pc80_codproc");
$clrotulo->label("pc80_resumo");
$clrotulo->label("descrdepto");
$clrotulo->label("nome");
$clrotulo->label("l20_codigo");
$lRegistroPreco = false;

if (empty($licitacao)) {
    db_redireciona("db_erros.php?db_erro=" . urlencode("Código da Licitação não informado."));
    die;
}

$result = $clliclicita->sql_record($clliclicita->sql_query($licitacao, "l08_altera, l20_usaregistropreco,  l20_formacontroleregistropreco"));
if ($clliclicita->numrows > 0) {
    db_fieldsmemory($result, 0);
    if ($l20_usaregistropreco == "t") {
        $lRegistroPreco = true;
    }
}
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script>
        function js_submit() {
            parent.itens.js_submit_form();
            parent.itens.document.form1.codproc.value = document.form1.codproc.value;

            parent.itens.document.form1.submit();
            document.form1.submit();
        }
    </script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="form1">
    <center>
        <table border="0" cellspacing="1" cellpadding="0" height='10%'>
            <tr>
                <td align="right" nowrap title="<?= @$Tl20_codigo ?>">
                    <strong>Licitação : </strong>
                </td>
                <td nowrap>
                    <?php
                    db_input('licitacao', 10, $Il20_codigo, true, 'text', 3);
                    ?>
                </td>
                <td><b>Processos de Compras:</b></td>
                <td>
                    <?php
                    $vir = "";

                    $result_liclicitem = $clliclicitem->sql_record($clliclicitem->sql_query(
                        null,
                        "distinct pc80_codproc",
                        null,
                        "l21_codliclicita = $licitacao"
                    ));
                    if ($clliclicitem->numrows > 0) {
                        for ($w = 0; $w < $clliclicitem->numrows; $w++) {
                            db_fieldsmemory($result_liclicitem, $w);
                            echo $vir . " $pc80_codproc";
                            $vir = ",";
                        }
                    } else {
                        echo "Nenhum Processo de Compra incluído.";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="pc80_codproc" id="ancoraProcessoCompras">Processo de Compra:</label>
                </td>
                <td>
                    <?php

                    $codprocinputvalue = "";

                    if (!empty($codproc)) {
                        $codprocinputvalue = $codproc;

                        $rsPcparam = $clpcparam->sql_record($clpcparam->sql_query_file(db_getsession("DB_instit"), "pc30_contrandsol"));
                        db_fieldsmemory($rsPcparam, 0);

                        $sWhere = " and pc10_solicitacaotipo in(1,2,5,6)";
                        $sWhere .= " and pc80_codproc = {$codproc} ";
                        $sWhere .= " and not exists (select 1 ";
                        $sWhere .= "                   from acordopcprocitem ";
                        $sWhere .= "                        inner join acordoitem    on ac23_acordoitem    = ac20_sequencial";
                        $sWhere .= "                        inner join acordoposicao on ac20_acordoposicao = ac26_sequencial";
                        $sWhere .= "                        inner join acordo        on ac26_acordo        = ac16_sequencial";
                        $sWhere .= "                  where ac23_pcprocitem = pc81_codprocitem ";
                        $sWhere .= "                    and (ac16_acordosituacao  not in (2,3)))";

                        if (isset($pc30_contrandsol) && $pc30_contrandsol == 't') {
                            $sSqlProc = $clpcproc->sql_query_soland(
                                null,
                                "*",
                                "pc80_codproc",
                                "(e55_sequen is null or (e55_sequen is not null and e54_anulad is not null))
    	                                             and pc43_depto = " . db_getsession('DB_coddepto') . " {$sWhere}"
                            );
                        } else {
                            $sSqlProc = $clpcproc->sql_query_aut(
                                null,
                                "*",
                                "pc80_codproc",
                                "(e55_sequen is null or (e55_sequen is not null and e54_anulad is not null))
                                                  and pc10_instit = " . db_getsession("DB_instit") . " {$sWhere}"
                            );
                        }

                        $nome = "";
                        $descrdepto = "";
                        $pc80_resumo = "";
                        $pc80_data_dia = "";
                        $pc80_data_mes = "";
                        $pc80_data_ano = "";

                        $result_pcproc = $clpcproc->sql_record($sSqlProc);
                        $fieldSQL = db_fieldsmemory($result_pcproc, 0);

                        if (isset($codproc) && $codproc != "") {
                            $couni = "codproc";
                            $$couni = $codproc;
                        }

                        $sSqlAcordo = $clacordo->sql_queryProcessosVinculados(null, "ac16_sequencial", null, "and pc81_codproc = $codproc");
                        $rsAcordo= $clacordo->sql_record($sSqlAcordo);
                        db_fieldsmemory($rsAcordo, 0);

                        $sSqlSolicita = $clsolicita->sql_query_solicitaanulada(null, "pc67_sequencial", null, "pc81_codproc = $codproc");
                        $clsolicita->sql_record($sSqlSolicita);

                        $sSqlProcAutorizacaoEmp = $clpcproc->sql_query_proc($codproc, "pc80_codproc", null, "pc80_codproc = $codproc and e54_autori is not null");
                        $clpcproc->sql_record($sSqlProcAutorizacaoEmp);

                        $sSqlOrcamentoSol = "select sum(pc23_vlrun) as soma_pc23_vlrun
                                                from pcprocitem
                                                left join pcorcamitemproc
                                                    on pcprocitem.pc81_codprocitem = pcorcamitemproc.pc31_pcprocitem
                                                left join pcorcamval
                                                    on pcorcamitemproc.pc31_orcamitem = pcorcamval.pc23_orcamitem
                                                where pc81_codproc = {$codproc}";

                        $rsOrcamentoSol = $clpcorcam->sql_record($sSqlOrcamentoSol);
                        $isOrcNulo = false;
                        $fieldOrc = db_utils::fieldsMemory($rsOrcamentoSol, 0)->soma_pc23_vlrun;
                        if (!$fieldOrc || $fieldOrc == 0) {
                            $isOrcNulo = true;
                        }

                        if ($clacordo->numrows != 0) {
                            db_msgbox('O processo de compras está vinculado a um contrato.');
                        } elseif ($pc67_sequencial) {
                            db_msgbox('O processo de compras selecionado está vinculado a uma solicitação de compras anulada.');
                        } elseif ($l20_codigo && $l20_codigo != $licitacao) {
                            db_msgbox('O processo de compras selecionado está vinculado a outra licitação.');
                        } elseif ($pc10_solicitacaotipo == 6 && $lRegistroPreco == false) {
                            db_msgbox('O processo de compras trata-se de um registro de preço, altere a informação "Usa registro de preço" para SIM.');
                        } elseif ($pc10_solicitacaotipo == 1 && $lRegistroPreco) {
                            db_msgbox('O processo de compras não se trata de um registro de preço, altere a informação  "Usa registro de preço" para NÃO.');
                        } elseif ($pc10_solicitacaotipo == 5) {
                            db_msgbox('O processo de compras selecionado é automático, verifique.');
                        } elseif ($pc80_situacao && $pc80_situacao != ProcessoCompras::AUTORIZADO) {
                            db_msgbox('O processo de compras não está autorizado.');
                        } elseif ($clpcproc->numrows != 0) {
                            db_msgbox('O processo de compras possui autorização de empenho.');
                        } elseif ($isOrcNulo && $lRegistroPreco) {
                            db_msgbox('A licitação requer um orçamento.\n Verifique se existe orçamento para o processo de compras!');
                        }
                    }

                    ?>
                    <input type="text" id="pc80_codproc" name="codproc" value="<?php echo $codprocinputvalue; ?>"/>
                    <input type="hidden" id="descrdepto"/>
                </td>
                <td align="right" nowrap title="<?= @$Tnome ?>">
                    <strong>Usuário:</strong>
                </td>
                <td align="left" nowrap>
                    <?php
                    db_input('nome', 41, $Inome, true, 'text', 3);
                    ?>
                </td>

            </tr>
            <tr>
                <td align="right" nowrap title="<?= @$Tpc80_data ?>">
                    <strong>Data: </strong>
                </td>
                <td align="left" nowrap>
                    <?php
                    db_input('pc80_data_dia', 2, 0, true, 'text', 3);
                    db_input('pc80_data_mes', 2, 0, true, 'text', 3);
                    db_input('pc80_data_ano', 4, 0, true, 'text', 3);
                    ?>
                </td>
                <td align="right" nowrap title="<?= @$Tdescrdepto ?>">
                    <strong>Departamento: </strong>
                </td>
                <td align="left" nowrap>
                    <?php
                    db_input('descrdepto', 41, $Idescrdepto, true, 'text', 3);
                    ?>
                </td>
            </tr>
            <tr>
                <td align="right" nowrap title="<?= @$Tpc80_resumo ?>">
                    <strong>Resumo: </strong>
                </td>
                <td colspan="3" nowrap>
                    <?php
                    db_textarea('pc80_resumo', 2, 73, $Ipc80_resumo, true, 'text', 3, "")
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" align="center" nowrap>
                    <br/>
                    <input name='incluir' type='button' value='Pesquisar' onclick='js_submit();'></td>
                </td>
            </tr>
        </table>
    </center>
</form>
</body>
</html>
<script>
    const ancoraProcessoCompras = document.getElementById('ancoraProcessoCompras');
    const codproc = document.getElementById('pc80_codproc');
    const descrdepto = document.getElementById('descrdepto');

    new DBLookUp(ancoraProcessoCompras, codproc, descrdepto, {
        'arquivo': 'func_pcproc.php',
        'label': 'Pesquisa Processo de Compras',
        'objetoLookUp': 'db_iframe_codproc',
        'sDestinoLookUp': 'CurrentWindow.corpo',
        'aCamposAdicionais': ['pc80_codproc'],
        'fCallBack': (codigoprocesso) => {
            codproc.value = codigoprocesso;
        }
    });
</script>
