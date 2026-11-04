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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_retencaotiporec_classe.php"));
require_once(modification("classes/empenho.php"));
require_once(modification("model/retencaoNota.model.php"));
require_once(modification("dbforms/db_funcoes.php"));

$oGet = db_utils::postMemory($_GET);
$clrotulo = new rotulocampo;
$clrotulo->label("e69_numero");
$clrotulo->label("e69_codnota");
$clrotulo->label("e50_codord");
$clrotulo->label("e60_numemp");
$clrotulo->label("e60_codemp");
$clrotulo->label("z01_nome");
$clrotulo->label("e70_valor");
$clrotulo->label("e70_vlrliq");
$clrotulo->label("e70_vlranu");
$clrotulo->label("e53_vlrpag");
$clrotulo->label("e21_sequencial");
$clrotulo->label("e21_descricao");
$clrotulo->label("e21_aliquota");
$clrotulo->label("e23_valorbase");
$clrotulo->label("e23_deducao");
$clrotulo->label("e23_valorretencao");
if (empty($oGet->iNumEmp)) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Número do Empenho não Informado.');
}
$oEmpenho = new empenho($oGet->iNumEmp);
$rsDadosNota = $oEmpenho->getNotas($oGet->iNumEmp, "e69_codnota = {$oGet->iNumNota}", false);
if ($oEmpenho->iNumRowsNotas == 0) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Nota não Encontrada.');
}
$oNota = db_utils::fieldsMemory($rsDadosNota, 0);
/**
 * Verificamos se o usuario está calculando a retencao para um cgm diferente do empenho
 * Caso relatado pelo Cliente GUAIBA
 *
 */
if (isset($oGet->iNumCgm) && $oGet->iNumCgm != "") {
    $sSqlDadosCgm = "select z01_nome,z01_cgccpf from cgm  where z01_numcgm = {$oGet->iNumCgm}";
    $rsDadosCgm = db_query($sSqlDadosCgm);
    if ($rsDadosCgm && pg_num_rows($rsDadosCgm) == 1) {
        $oCgm = db_utils::fieldsMemory($rsDadosCgm, 0);
        $oNota->z01_nome = $oCgm->z01_nome;
        $oNota->z01_cgccpf = $oCgm->z01_cgccpf;
    }
}

//Checa parametro e mostra alerta de confirmacao de data
$clconparametro = new cl_conparametro();
$rsconparametro = $clconparametro->sql_record($clconparametro->sql_query_file(null, "c90_confirmadata"));
$conparametro = db_utils::fieldsMemory($rsconparametro, 0);
if ($conparametro->c90_confirmadata == 't') {
    $data = date('d/m/y', db_getsession('DB_datausu'));
    echo "<db-alertaconfirmadatafinanceiro data=" . $data . "></db-alertaconfirmadatafinanceiro>";
}

$e69_codnota = $oNota->e69_codnota;
$e69_numero = $oNota->e69_numero;
$z01_nome = $oNota->z01_nome;
$z01_cgccpf = $oNota->z01_cgccpf;
$e70_valor = $oNota->e70_valor;
$e70_vlrliq = $oNota->e70_vlrliq;
$e70_vlranu = $oNota->e70_vlranu;
$e53_vlrpag = $oNota->e53_vlrpag;
$e50_codord = $oNota->e50_codord;
$e60_numemp = $oNota->e60_numemp;

$e23_valorbase = $oNota->e70_valor;
$e23_valorbase = $oNota->e70_liq;
if (isset($oGet->nValorBase) && $oGet->nValorBase != "") {
    $valorpagar = round($oGet->nValorBase, 2);
    $e23_valorbase = $valorpagar;
    $valormovimento = $valorpagar;
} else {
    $valorbase = $oNota->e70_vlrliq - $oNota->e70_vlranu - $oNota->e53_vlrpag;
    $valorpagar = $valorbase;
    $valormovimento = $valorpagar;
}

if (isset($oGet->iCodMov)) {
    $e81_codmov = $oGet->iCodMov;

    if (empty($oGet->iCodMov)) {
        $sSqlBuscaMovimento = " select e82_codmov from empord where e82_codord = {$oNota->e50_codord} ";
        $rsBuscaMovimento = db_query($sSqlBuscaMovimento);
        if ($rsBuscaMovimento) {
            $e81_codmov = db_utils::fieldsMemory($rsBuscaMovimento, 0)->e82_codmov;
        }
    }
}
if (isset($oGet->lSession) && $oGet->lSession == "false") {
    $oRetencaoNota = new retencaoNota($oNota->e69_codnota);
    $oRetencaoNota->unsetSession();
}

$uf = getEstadoInstituicao();
$isPB = $uf === "PB";
$e23_valorbase = $oNota->e70_liq;
?>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js");
    db_app::load("prototype.js");
    db_app::load("datagrid.widget.js");
    db_app::load("strings.js");
    db_app::load("grid.style.css");
    db_app::load("estilos.css");
    db_app::load("AjaxRequest.js");
    db_app::load("Input/DBInput.widget.js, Input/DBInputValor.widget.js");
    db_app::load("windowAux.widget.js, classes/financeiro/DadosAdicionaisRetencao.js");
    ?>
    <style type="text/css">
        #dadosAdicionaisRetencao fieldset>table tr td:nth-child(1) {
            width: 340px;
        }

        #dadosAdicionaisRetencao fieldset>table fieldset>table tr td:nth-child(1) {
            width: 327px;
        }
    </style>
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0"
    marginheight="0" onLoad="a=1">

    <div id="msgWarning" style="display: none" class="alert alert-warning text-left" role="alert">

    </div>
    <div class="container">
        <form name='form1'>
            <table>
                <tr>
                    <td>
                        <fieldset>
                            <legend><b>Dados da Nota</b></legend>
                            <table>
                                <tr>
                                    <td><b>Código da Nota:</b></td>
                                    <td>
                                        <?php
                                        db_input('e69_codnota', 13, $Ie69_codnota, true, 'text', 3);
                                        ?>
                                    </td>
                                    <td><b>Número:</b></td>
                                    <td>
                                        <?php
                                        db_input('e69_numero', 13, $Ie69_numero, true, 'text', 3);
                                        ?>
                                    </td>
                                    <td><b><?php echo $Le60_numemp ?>:</b></td>
                                    <td>
                                        <?php
                                        db_input('e60_numemp', 13, $Ie60_codemp, true, 'text', 3);
                                        ?>
                                    </td>
                                    <td><b>Ordem:</b></td>
                                    <td>
                                        <?php
                                        db_input('e50_codord', 13, $Ie50_codord, true, 'text', 3);
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b><?php echo $Lz01_nome ?></b></td>
                                    <td colspan='8'>
                                        <?php
                                        db_input('z01_nome', 70, $Lz01_nome, true, 'text', 3);
                                        db_input('z01_cgccpf', 20, $Lz01_nome, true, 'text', 3);
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Valor:</b></td>
                                    <td>
                                        <?php
                                        db_input('e70_valor', 13, $Ie70_valor, true, 'text', 3);
                                        ?>
                                    </td>
                                    <td><b>Valor Liquidado:</b></td>
                                    <td>
                                        <?php
                                        db_input('e70_vlrliq', 13, $Ie70_vlrliq, true, 'text', 3);
                                        ?>
                                    </td>
                                    <td><b>Valor Anulado: </b></td>
                                    <td>
                                        <?php
                                        db_input('e70_vlranu', 13, $Ie70_vlranu, true, 'text', 3);
                                        ?>
                                    </td>
                                    <td><b>Valor Pago:</b></td>
                                    <td>
                                        <?php
                                        db_input('e53_vlrpag', 13, $Ie53_vlrpag, true, 'text', 3);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        db_input('valorpagar', 13, $Ie53_vlrpag, true, 'hidden', 3);
                                        db_input('valormovimento', 13, 0, true, 'text', 3);
                                        db_input('e81_codmov', 14, '', true, 'hidden', 3);
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td>
                        <fieldset>
                            <legend><b>Dados das Retenções</b></legend>
                            <table>
                                <tr>
                                    <td>
                                        <strong><?php db_ancora('Retenção', "js_pesquisaRetencoes();", 2); ?></strong>
                                    </td>
                                    <td class="retencoes-info">
                                        <?php
                                        $rsInicio = db_query("select '' as codigo, 'Selecione' as descr");
                                        db_selectrecord(
                                            "e21_sequencial",
                                            $rsInicio,
                                            true,
                                            1,
                                            "",
                                            null,
                                            "",
                                            "",
                                            "
                                        js_setAliquota(\$('e21_sequencial').selectedIndex);
                                        js_validaReinf(\$('e21_sequencial').selectedIndex);
                                        js_enableSubcontratacao(\$('e21_sequencial').selectedIndex)
                                        checkIfPeriodEFDReinfIsClosed()
                                        "
                                        );
                                        ?>
                                    </td>
                                    <td><b>Aliquota:</b></td>
                                    <td>
                                        <?php
                                        db_input('e21_aliquota', 15, $Ie21_aliquota, true, 'text', 1, "onBlur='js_calculaRetencao()'");
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b><?php echo $Le23_deducao ?></b></td>
                                    <td>
                                        <?php
                                        db_input('e23_deducao', 10, $Ie23_deducao, true, 'text', 1, 'onblur="js_calculaRetencao()"');
                                        ?>
                                    </td>
                                    <td><b><?php echo $Le23_valorbase ?></b></td>
                                    <td>
                                        <?php
                                        // $e23_valorbase = ($e23_valorbase - $oNota->e70_vlranu);
                                        db_input('e23_valorbase', 15, $Ie23_valorbase, true, 'text', 3, '');
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b><?php echo $Le23_valorretencao ?></b></td>
                                    <td>
                                        <?php
                                        db_input('e23_valorretencao', 10, $Ie23_valorretencao, true, 'text', 1, '');
                                        ?>
                                    </td>
                                    <td>
                                        <b>Data Calculo</b>
                                    </td>
                                    <td>
                                        <?php
                                        if (isset($Get->dtPagamento) && $oGet->dtPagamento != "") {
                                            $dtpagamento = $oGet->dtPagamento;
                                        } else {
                                            $dtpagamento = date("d/m/Y", db_getsession("DB_datausu"));
                                        }
                                        $aDataBase = explode("/", $dtpagamento);
                                        $iMes = $aDataBase[1];
                                        $iAno = $aDataBase[2];
                                        db_input("dtpagamento", 15, '', true, "text");
                                        ?>
                                    </td>
                                </tr>
                                <tr id="dadosReinf" style="display: none;">
                                    <td colspan='4'>
                                        <fieldset>
                                            <legend><b>Dados EFD-Reinf</b></legend>
                                            <table id="R-4010-4020" class="form-container" style="display: none;">
                                                <tr>
                                                    <td><label id="ancoraNaturezaRendimento">Grupo / Natureza Rendimento: </label></td>
                                                    <td>
                                                        <input type="hidden" id="idNaturezaRendimento" name="idNaturezaRendimento" lang='db_codigo'>
                                                        <input type="text" id="codigoNaturezaRendimento" name="codigoNaturezaRendimento" lang='e167_codigo' class="field-size2">
                                                        <input type="text" id="naturezaRendimento" name="naturezaRendimento" lang='e167_descricao' class="field-size8">

                                                    </td>
                                                </tr>
                                            </table>

                                            <table id="R-2010" style="display: none;">
                                                <tr>
                                                    <td><b>Tipo de serviço da nota fiscal:</b></td>
                                                    <td class="retencoes-info" colspan="2">
                                                        <?php
                                                        $rsInicio = db_query("select '' as codigo, 'Selecione' as descr");
                                                        db_selectrecord("e18_sequencial", $rsInicio, true, 1, "", null, "", "", "js_validaTipoServico()");
                                                        ?>
                                                    </td>
                                                    <td>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Indicativo de Obra: </b></td>
                                                    <td>
                                                        <select name="indObra" id="indObra"></select>
                                                        <input type="text" maxlength="12" placeholder="CNO"
                                                            name="indObraCNO" id="indObraCNO" value="" width="180px"
                                                            disabled>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan='4' style='text-align: center'>
                                                        <input type='button' id='dadosadicionarefdreinf'
                                                            value='Dados Adicionais'>
                                                    </td>
                                                </tr>
                                            </table>
                                            <table id="R-2055" style="display: none;">
                                                <tr>
                                                    <td><b>Valor GILRAT: (0,1%)</b></td>
                                                    <td><input type="text" id="vlrrat"
                                                            onkeyup="js_ValidaCampos(this, 4,'(Valor GILRAT)','f','f',event);">
                                                    </td>
                                                    <td><b>Valor Senar: (0,2%)</b></td>
                                                    <td><input type="text" id="vlrsenar"
                                                            onkeyup="js_ValidaCampos(this, 4,'(Valor Senar)','f','f',event);">
                                                    </td>
                                                    <td><b>Valor CP: (1,2%)</b></td>
                                                    <td><input type="text" id="vlrcp"
                                                            onkeyup="js_ValidaCampos(this, 4,'(Valor CP)','f','f',event);">
                                                    </td>
                                                </tr>
                                            </table>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='4' style='text-align: center'>
                                        <input type='button' id='lancarretencao' value='Lançar' onclick='js_addRetencao()'>
                                        <input type='button' id='alterarretencao' value='Alterar'
                                            onclick='js_alterarRetencao()' disabled>
                                        <input type='button' id='apagarretencao' value='Excluir' onclick='js_apagar()'
                                            disabled>
                                        <input type='button' id='limparretencao' value='Limpar' onclick='js_limpar()'>
                                        <input type='button' id='recalcularretencao' value='Recalcular'
                                            onclick='js_calculaRetencao()'>
                                        <input type='button' id='mostraoutrosmov' value='Composição da Base'
                                            onclick='js_mostraInfo()'>
                                        <input type='button' id='tabelas        ' value='Tabelas' onclick='js_tabelas()'>
                                        <input type='button' id='subcontratacoesbtn' value='Subcontratações'
                                            onclick='js_openFrameSubcontratacao()' class="d-none">
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <td>
                        <fieldset>
                            <legend><b>Retenções lançadas</b></legend>
                            <div id='gridRetencoes'></div>
                        </fieldset>
                    </td>
                </tr>


                <tr>
                    <td align="right">
                        <div><strong> Total Retido
                                Lançado: </strong> <?php db_input('nTotalLancado', 15, "", true, 'text', 3, ""); ?> </div>
                    </td>
                </tr>


            </table>
            <?php
            if (isset($oGet->lSession) && $oGet->lSession == "false") {
                echo " <input type='button' id='confirmarretencoes'  value='Confirmar'  onclick='js_confirmar()'>";

                if (isset($oGet->iCodMov) && $oGet->iCodMov != "") {
                    $nomeBotao = "Configurar Pagamento";
                    if (APROPRIACAO_RETENCAO) {
                        $nomeBotao = "Apropriar Retenções";
                    }
                    echo " <input type='button' id='configurarretencoes'  value='{$nomeBotao}'  onclick='js_confirmarPagamento()'>";
                }
            }
            ?>
        </form>
    </div>
</body>

</html>
<script type="text/javascript" src="scripts/components/AlertaConfirmaDataFinanceiro.js"></script>
<script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>

<script>
    lSession = <?php echo isset($oGet->lSession) && $oGet->lSession == "false" ? "false" : "true"; ?>;
    lCallBack = <?php echo isset($oGet->callback) && $oGet->callback == "false" ? "false" : "true"; ?>;
    lApropriacaoRetencao = <?php echo APROPRIACAO_RETENCAO ? "true" : "false"; ?>;
    isRN = '<?php echo isRioGrandeDoNorte() ?>';

    const TIPO_CALCULO_IRRF_PESSOA_JURIDICA = 2;
    const TIPO_CALCULO_IRRF_PESSOA_FISICA = 1;
    const TIPO_CALCULO_INSS_PESSOA_JURIDICA = 4;
    const TIPO_CALCULO_INSS_PESSOA_FISICA = 3;

    const dadosAdicionaisRetencaoWindowAux = new windowAux('windowAux', 'Dados Adicionais EFD-Reinf', 600, 250);
    const dadosAdicionaisRetencao = new DadosAdicionaisRetencao();

    var tipoServicos = [];
    var produtorrural = 'f';

    $("nTotalLancado").value = "0.00";


    var lookupNaturezaRendimento = new DBLookUp($('ancoraNaturezaRendimento'), $('codigoNaturezaRendimento'), $('naturezaRendimento'), {
        "sArquivo": "func_naturezarendimento.php",
        "sObjetoLookUp": "db_iframe_naturezarendimento",
        "sLabel": "Pesquisar Natureza da Informação",
        "aCamposAdicionais": ['db_codigo']
    });
    lookupNaturezaRendimento.setCallBack('onClick', (dados) => {
        $('idNaturezaRendimento').value = dados[2];
    });
    lookupNaturezaRendimento.setCallBack('onChange', (erro, dados) => {
        $('idNaturezaRendimento').value = '';
        $('naturezaRendimento').value = dados[2];
        if (!erro) {
            $('idNaturezaRendimento').value = dados[3]

        }
    });

    function js_calcularTotalLancado() {

        $("nTotalLancado").value = "0.00";

        var aRegistros = gridRetencoes.getRows();
        var aValores = [];

        var nTotal = 0;
        aRegistros.each(function(oLinha, iInd) {

            var nValorLinha = oLinha.aCells[5].getValue();
            nValorLinha = nValorLinha.replace(/\./g, '');
            nValorLinha = nValorLinha.replace(",", ".");
            nValorLinha = new Number(nValorLinha);

            nTotal += nValorLinha;
        })
        $("nTotalLancado").value = js_formatar(nTotal, "f");
    }


    function js_pesquisaretencao(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('', 'db_iframe_retencaotiporec',
                'func_retencaotiporec.php?funcao_js=parent.js_mostraretencao1|e21_sequencial|e21_aliquota|e21_descricao',
                'Retenções Cadastradas', true, 0);
        } else {

            if (document.form1.e21_sequencial.value != '') {

                js_OpenJanelaIframe('', 'db_iframe_retencaotiporec',
                    'func_retencaotiporec.php?pesquisa_chave=' + $F('e21_sequencial') +
                    '&funcao_js=parent.js_mostraretencao',
                    '', false, 0);
            } else {

                document.form1.e21_descricao.value = '';
                document.form1.e21_aliquota.value = '';

            }
        }
    }

    function js_mostraretencao1(iCodigoRetencao, nAliquota, sDescricao) {

        $('e21_descricao').value = sDescricao;
        iCodigoRetencao;
        $('e21_aliquota').value = nAliquota;
        $('e21_descricao').value = sDescricao;
        db_iframe_retencaotiporec.hide();

    }

    function js_mostraretencao(sDescricao, lErro, nAliquota) {

        $('e21_descricao').value = sDescricao;
        if (lErro) {

            $('e21_sequencial').value = '';
            $('e21_descricao').focus();
            $('e21_aliquota').value = '';

        } else {

            $('e21_aliquota').value = nAliquota;
            $('e23_deducao').focus();

        }
    }

    function js_init() {

        gridRetencoes = new DBGrid("gridRetencoes");
        gridRetencoes.nameInstance = "gridRetencoes";
        gridRetencoes.setCellAlign(new Array("Right", "left", "right", "right", "right", "right", "right"));
        gridRetencoes.setHeader(new Array("Id",
            "Retenção",
            'Dedução',
            "Base de Calculo",
            "Aliquota",
            "Valor Retido",
            "Tipo",
            "Grupo",
            "Tipo de Serviço Sequencial",
            "Tipo de Serviço",
            "Subcontratacao"
        ));
        gridRetencoes.aHeaders[7].lDisplayed = false;
        gridRetencoes.aHeaders[8].lDisplayed = false;
        gridRetencoes.aHeaders[10].lDisplayed = false;
        gridRetencoes.show(document.getElementById('gridRetencoes'));
        closeOnSave = false;
        aBaseDeCalculo = new Array();

        var oParametros = {};

        // tipo de servico nota fiscal
        oParametros = {
            exec: "getTipoServicos"
        };
        new AjaxRequest(
            'emp4_retencaonotaRPC.php',
            oParametros,
            function(oRetorno, lErro) {
                tipoServicos = oRetorno.tiposServicosNotaFiscal || [];
                tipoServicos.forEach((tipoServico) => {
                    adicionaTipoServico(tipoServico.descricao, tipoServico.referencia, tipoServico.sequencial);
                });
            }
        ).execute();

        // verificar se o empenho e de aquisicao de producao rural
        oParametros = {
            exec: "getByEmpenho",
            numemp: $F('e60_numemp')
        };
        new AjaxRequest(
            'emp4_tipoaquisicaoproducaorural.RPC.php',
            oParametros,
            function(oRetorno, lErro) {
                produtorrural = (oRetorno.tipoaquisicaoproducaorural) ? 't' : 'f';
            }
        ).execute();

        // indicativo de obra
        oParametros = {
            exec: "getLabels"
        };
        new AjaxRequest(
            'emp4_tiposervicoobra.RPC.php',
            oParametros,
            function(oRetorno, lErro) {
                let option = '';
                let indObra = $('indObra');
                let data = oRetorno.labels;

                data.forEach((item, key) => {
                    option = document.createElement('option');
                    option.value = key;
                    option.innerHTML = item;
                    indObra.append(option);
                });
            }
        ).execute();

        dadosAdicionaisRetencao.setWindowAux(dadosAdicionaisRetencaoWindowAux);
        $('dadosadicionarefdreinf').addEventListener('click', () => {
            var oCmbTipoServico = $('e18_sequencial');
            if (oCmbTipoServico.selectedIndex == 0) {
                oCmbTipoServico.focus();
                return alert("É necessário selecionar o 'Tipo de serviço da nota fiscal' para preencher os dados adicionais");
            }
            dadosAdicionaisRetencao.exibe();
        });

        $('indObra').addEventListener('change', event => {
            switch (event.target.value) {
                case '1':
                    indObraCNO.disabled = false;
                    indObraCNO.placeholder = 'CNO do Prestador';
                    break;
                case '2':
                    indObraCNO.disabled = false;
                    indObraCNO.placeholder = 'CNO da Unidade';
                    break;
                case '0':
                    indObraCNO.disabled = true;
                    indObraCNO.value = '';
                    indObraCNO.placeholder = 'CNO';
                    break;
            }
        });

        js_clearSubcontratacao();
    }


    function js_setValorBase() {

        var nDeducao = new Number($F('e23_deducao')).toFixed(2); //new Number($F('e23_deducao'));
        // var nValorBase = new Number($F('e70_valor'));
        var nValorBase = new Number($F('e70_vlrliq'));
        nValorBase = (nValorBase - nDeducao);
        var nReducao = new Number();
        if (nValorBase < 0) {
            nValorBase = 0;
        } else {

            nReducao = nValorBase * (new Number($F('e21_aliquota')) / 100);
            $('e23_valorretencao').value = nReducao;
        }

        $('e23_valorbase').value = nValorBase;
    }

    function js_addRetencao() {

        /* Algumas regras:
         * - caso o usuario cadastrou uma retencao de tipo 1, ou 2 (Imposto de Renda)
         *   ele não pode mais cadastrar uma retencao do tipo 3 ou 4 (INSS), pois as
         *   retencoes do tipo 3, 4 deduzem da base de cálculo.
         * - Não Podemos lancar uma retencao duas vezes.
         * - Sempre devemos ver o calculo da retencao por CGM(cnpj/cpf (quando for pessoa fisica)) dentro do mes, nunca por nota.
         * - Valor da retencao nao pode ser maior que o valor da nota.
         */

        let cboRetencao = document.getElementById('e21_sequencial');

        var nValorRetencao = new Number($F('e23_valorretencao')).toFixed(2); //new Number($F('e23_valorretencao'));
        var nTotalRetido = getTotalRetido(null) + nValorRetencao;
        var nValorNota = new Number($F('e70_valor'));
        let nAliquota = Number($F('e21_aliquota'));
        let nValorBase = new Number($F('e23_valorbase')).toFixed(2); //Number($F('e23_valorbase'));

        if (nTotalRetido.toFixed(2) > nValorNota.toFixed(2)) {
            alert('Valor da retenção maior que o valor da nota!');
            return false;
        }

        var valorValorMovimento = new Number($F('valormovimento'));
        if (nTotalRetido.toFixed(2) > valorValorMovimento.toFixed(2)) {
            alert('Valor da retenção não pode ser maior do que o valor a pagar!');
            return false;
        }

        if ($F('e21_sequencial') == '') {
            alert('Retenção não informada!');
            $('e21_sequencial').focus();
            return false;
        }

        if ($F('e23_valorretencao') == '') {
            alert('valor da retenção não informado!');
            return false;
        }

        let optionSelecionado = cboRetencao.options[cboRetencao.selectedIndex]
        let tipoCalculo = optionSelecionado.tipocalc;

        // Validacoes EFD-REINF
        if ([TIPO_CALCULO_IRRF_PESSOA_FISICA, TIPO_CALCULO_IRRF_PESSOA_JURIDICA].includes(Number(tipoCalculo)) &&
            $F('idNaturezaRendimento') == '') {
            alert('A Natureza de Rendimento é obrigatória.')
            return;
        }

        if (tipoCalculo == TIPO_CALCULO_INSS_PESSOA_JURIDICA) {
            if (produtorrural == 'f' && $F('e18_sequencial') == '') {
                alert('Tipo de serviço da nota fiscal deve ser informado!');
                $('e18_sequencial').focus();
                return false;
            }

            if (produtorrural == 'f' && $F('indObra') > 0 && $F('indObraCNO') == '') {
                alert('Você deve informar o CNO do tipo de serviço.');
                return false;
            }

            if ($F('indObraCNO') != "" && $F('indObraCNO').length < 12) {
                alert('O CNO deve possuir 12 caracteres');
                return false;
            }

            if (nValorRetencao > (((nAliquota / 100) * nValorBase) + 0.01).toFixed(2)) {
                alert(`
                    Valor da retenção não pode ser maior que ${nAliquota}% da base de cálculo!
                    Revise os valores digitados.
                `);
                $('e23_valorretencao').focus();
                return false;
            }

            if (nValorRetencao < (((nAliquota / 100) * nValorBase)).toFixed(2)) {
                let valorRetencaoEsperado = ((nAliquota / 100) * nValorBase).toFixed(2);
                let warningRetencao = confirm(`
                    O valor esperado da retenção : ${valorRetencaoEsperado}.
                    Deseja prosseguir com o valor de: ${nValorRetencao}?
                `);

                if (!warningRetencao) {
                    return false;
                }
            }
        }

        isSave = true;
        var oRetencao = {};

        oRetencao.iCodigoRetencao = $F('e21_sequencial');
        oRetencao.nValorDeducao = new Number($F('e23_deducao')).toFixed(2);
        oRetencao.nValorNota = $F('e70_valor');
        oRetencao.tipoCalculo = tipoCalculo;
        oRetencao.iCodNota = $F('e69_codnota');
        oRetencao.nAliquota = $F('e21_aliquota');
        oRetencao.iCpfCnpj = $F('z01_cgccpf');
        oRetencao.nValorRetencao = new Number($F('e23_valorretencao')).toFixed(2); //$F('e23_valorretencao');
        oRetencao.nValorbase = new Number($F('e23_valorbase')).toFixed(2); //Number($F('e23_valorbase')).toFixed(2);
        oRetencao.codigoOrdem = $F('e50_codord');

        oRetencao.dadosReinf = js_validaReinf();
        oRetencao.subcontratacoes = js_getDadosSubcontratacao();

        oRetencao.aMovimentos = [];
        if (typeof(aBaseDeCalculo[$F('e21_sequencial')]) != "undefined") {
            oRetencao.aMovimentos = aBaseDeCalculo[$F('e21_sequencial')];
        }
        js_divCarregando("Aguarde, Calculando retenção", "msgBox");

        var oParametros = {
            "exec": "addRetencao",
            "params": [{
                "oRetencao": oRetencao,
                "inSession": true,
                "isUpdate": false
            }]
        };

        new AjaxRequest(
            'emp4_retencaonotaRPC.php',
            oParametros,
            function(oRetorno, lErro) {
                js_retornoaddRetencao(oRetorno);
            }
        ).setMessage('Aguarde, configurando retenção...').execute();
    }

    function js_retornoaddRetencao(oAjax) {

        js_removeObj("msgBox");
        var oRetencoes = oAjax;

        if (oRetencoes.status == 2) {
            js_validaReinf(null, true);
            alert(oRetencoes.message.urlDecode());
            return false;
        }

        gridRetencoes.clearAll(true);
        //preenchemos a grid com as retencoes;
        if (oRetencoes.aRetencoes.length > 0) {

            for (var iRet = 0; iRet < oRetencoes.aRetencoes.length; iRet++) {
                const oRetencao = oRetencoes.aRetencoes[iRet];
                const aLinha = new Array();

                aLinha[0] = oRetencao.e21_sequencial;
                aLinha[1] = oRetencao.e21_descricao.urlDecode();
                aLinha[2] = js_formatar(oRetencao.e23_deducao, 'f');
                aLinha[3] = js_formatar(oRetencao.e23_valorbase, 'f');
                aLinha[4] = js_formatar(oRetencao.e23_aliquota, 'f');
                aLinha[5] = js_formatar(oRetencao.e23_valorretencao, 'f');
                aLinha[6] = oRetencao.e21_retencaotipocalc;
                aLinha[7] = oRetencao.e21_retencaotiporecgrupo;
                aLinha[8] = oRetencao.dadosReinf ? JSON.stringify(oRetencao.dadosReinf) : JSON.stringify(false);
                aLinha[9] = oRetencao.dadosReinf ? (oRetencao.dadosReinf.e18_descricao ? oRetencao.dadosReinf.e18_descricao : '') : "";
                aLinha[10] = oRetencao.subcontratacoes ? JSON.stringify(oRetencao.subcontratacoes) : JSON.stringify(false);

                gridRetencoes.addRow(aLinha);
                gridRetencoes.aRows[iRet].sEvents = "onDBLClick='js_atualizaCampos(" + iRet + ")'";
                gridRetencoes.aRows[iRet].sValue = oRetencao.e21_sequencial;
                aBaseDeCalculo[oRetencao.e21_sequencial] = "";

                if (oRetencao.aMovimentos.length > 0) {
                    var aMov = new Array();
                    for (var iMov = 0; iMov < oRetencao.aMovimentos.length; iMov++) {
                        aMov.push(oRetencao.aMovimentos[iMov]);
                    }
                    aBaseDeCalculo[oRetencao.e21_sequencial] = aMov;
                }
            }

            gridRetencoes.renderRows();
            js_calcularTotalLancado();
        }

        $('valorpagar').value = $F('valormovimento');

        js_limpar();
    }

    function js_apagar() {

        isSave = true;
        js_divCarregando("Aguarde, Apagando retenções", "msgBox");

        var oParametro = {
            "exec": "apagarRetencao",
            "params": [{
                "iCodNota": $F('e69_codnota'),
                "iRetencao": $F('e21_sequencial'),
                "iNotaLiquidacao": $F('e50_codord')
            }]
        };

        new AjaxRequest(
            'emp4_retencaonotaRPC.php',
            oParametro,
            function(oRetorno) {
                js_retornoaddRetencao(oRetorno);
            }
        ).execute();
    }

    function js_consultaRetencoesNota() {

        js_divCarregando("Aguarde, Pesquisando retenções", "msgBox");
        var iCodOrd = $F('e50_codord');
        var iCodMov = $F('e81_codmov');
        var dtRetencao = $F('dtpagamento');

        var oParametro = {
            "exec": "getRetencoes",
            "numeroEmpenho": $F('e60_numemp'),
            "params": [{
                "iCodNota": $F('e69_codnota'),
                "iCodOrd": iCodOrd,
                "iCodMov": iCodMov,
                "dtCalculo": dtRetencao
            }]
        };

        new AjaxRequest(
            'emp4_retencaonotaRPC.php',
            oParametro,
            function(oRetorno, lErro) {
                js_retornoaddRetencao(oRetorno);
            }
        ).execute();

        alertaConfirmaData();
    }

    function alertaConfirmaData() {
        <?php
        $clconparametro = new cl_conparametro();
        $rsconparametro = $clconparametro->sql_record($clconparametro->sql_query_file(null, "c90_confirmadata"));
        $conparametro = db_utils::fieldsMemory($rsconparametro, 0);
        ?>
        let c90_confirmadata = '<?php echo $conparametro->c90_confirmadata ?>'
        if (c90_confirmadata == 't') {
            const msg = "Antes de realizar a operação confirme a data em que deseja incluir o movimento";
            alert(msg)
        }
    }


    /**
     * preenche os campos com os valores da retencao atual.
     * @param integer iNumRow linha selecionada da grid
     */
    function js_atualizaCampos(iNumRow) {

        gridRetencoes.aRows[iNumRow].isSelected = true;
        /*
         * Aqui pegamos todos os valores da linha selecionada,
         * onde cada indice da array é uma coluna da grid.
         * so permitimos o usuario alterar retencoes do grupo 1 (fornecedore)
         */

        var aSelecionados = gridRetencoes.getSelection();

        if (aSelecionados[0][7] == 1) {
            const dadosReinf = JSON.parse(aSelecionados[0][8]);
            const subcontratacoes = JSON.parse(aSelecionados[0][10]);

            $('e21_sequencial').value = aSelecionados[0][0]; //Codigo da retencao
            $('e21_sequencialdescr').value = aSelecionados[0][0]; //descricao da retencao
            $('e23_deducao').value = js_strToFloat(aSelecionados[0][2]); //valor da deducao
            $('e23_valorbase').value = js_strToFloat(aSelecionados[0][3]); // valor da base de cálculo
            $('e21_aliquota').value = js_strToFloat(aSelecionados[0][4]); //aliquota da retencao
            $('e23_valorretencao').value = js_strToFloat(aSelecionados[0][5]); //valor retido da retencao
            $('lancarretencao').disabled = true;
            if (isRN) {
                $('lancarretencao').disabled = false;
            }
            $('alterarretencao').disabled = false;
            $('apagarretencao').disabled = false;
            $('e21_sequencial').disabled = true;
            $('e21_sequencialdescr').disabled = true;
            $('e18_sequencial').value = dadosReinf.e19_tiposerviconotafiscal || "";
            $('e18_sequencialdescr').value = dadosReinf.e19_tiposerviconotafiscal || "";

            const temReinf = js_validaReinf();
            if (temReinf) {
                switch (dadosReinf.evento) {
                    case 'R-2010':
                        dadosAdicionaisRetencao.preencherDados({
                            valorNaoRetidoPrincipal: dadosReinf.e19_valornaoretidoprincipal,
                            valorServico15: dadosReinf.e19_valorservico15,
                            valorServico20: dadosReinf.e19_valorservico20,
                            valorServico25: dadosReinf.e19_valorservico25,
                            valorAdicional: dadosReinf.e19_valoradicional,
                            valorNaoRetidoAdicional: dadosReinf.e19_valornaoretidoadicional
                        }, true);

                        if (dadosReinf.e154_tipo > 0) {
                            $('indObraCNO').disabled = false;
                        }

                        $('indObra').value = dadosReinf.e154_tipo ?? 0;
                        $('indObraCNO').value = dadosReinf.e154_cno;
                        break;
                    case 'R-2055':
                        $('vlrrat').value = dadosReinf.e158_vlrrat;
                        $('vlrsenar').value = dadosReinf.e158_vlrsenar;
                        $('vlrcp').value = dadosReinf.e158_vlrcp;
                        break;
                }
            }

            if (subcontratacoes) {
                js_setSubcontratacoes(subcontratacoes);
                $('subcontratacoesbtn').removeClassName('d-none');
            }
        }
        gridRetencoes.aRows[iNumRow].isSelected = false;
    }

    function js_limpar() {

        $('lancarretencao').disabled = true;
        if (isRN) {
            $('lancarretencao').disabled = false;
        }
        $('alterarretencao').disabled = true;
        $('apagarretencao').disabled = true;
        $('e21_sequencial').disabled = false;
        $('e21_sequencialdescr').disabled = false;
        $('e21_sequencial').value = ''; //Codigo da retencao
        $('e21_sequencialdescr').value = ''; //descricao da retencao
        $('e23_deducao').value = ''; //valor da deducao
        $('e21_aliquota').value = ''; //aliquota da retencao
        $('e23_valorretencao').value = ''; //valor retido da retencao
        $('e23_valorbase').value = $F('e70_vlrliq');; // $F('e70_valor');
        $('dadosReinf').style.display = 'none';
        $('e18_sequencial').selectedIndex = 0;
        $('e18_sequencialdescr').selectedIndex = 0;
        $('e18_sequencial').disabled = false;
        $('e18_sequencialdescr').disabled = false;
        $('vlrrat').value = '';
        $('vlrsenar').value = '';
        $('vlrcp').value = '';
        $('indObra').value = 0;
        $('indObraCNO').value = '';

        dadosAdicionaisRetencao.remapear(true);
        js_clearSubcontratacao();
    }

    /**
     * Atualiza as informacoes da retenção.
     */
    function js_alterarRetencao() {

        var oRetencao = new Object();
        var nValorRetencao = new Number($F('e23_valorretencao')).toFixed(2); //new Number($F('e23_valorretencao'));
        var nTotalRetido = getTotalRetido($('e21_sequencial').value) + nValorRetencao;
        var nValorNota = new Number($F('e70_valor'));

        if (nTotalRetido.toFixed(2) > nValorNota.toFixed(2)) {
            alert('Valor da retenção maior que o valor da nota!');
            return false;
        }

        if ($F('e23_valorretencao') == '') {

            alert('valor da retenção não informado!');
            return false;
        }

        isSave = true;
        oRetencao.iCodigoRetencao = $F('e21_sequencial');
        oRetencao.nValorDeducao = new Number($F('e23_deducao')).toFixed(2); //new Number($F('e23_deducao')).toString();
        oRetencao.nValorNota = $F('e70_valor');
        oRetencao.iCodNota = $F('e69_codnota');
        oRetencao.nAliquota = $F('e21_aliquota');
        oRetencao.iCpfCnpj = $F('z01_cgccpf');
        oRetencao.nValorRetencao = new Number($F('e23_valorretencao')).toFixed(2); //$F('e23_valorretencao');
        oRetencao.nValorbase = new Number($F('e23_valorbase')).toFixed(2); //$F('e23_valorbase');
        oRetencao.aMovimentos = new Array();
        oRetencao.dadosReinf = js_validaReinf();
        oRetencao.subcontratacoes = js_getDadosSubcontratacao();

        if (aBaseDeCalculo[$F('e21_sequencial')] != "undefined") {
            oRetencao.aMovimentos = aBaseDeCalculo[$F('e21_sequencial')];

        }

        js_divCarregando("Aguarde, Calculando retenção", "msgBox");
        var oParametro = {
            "exec": "addRetencao",
            "params": [{
                "oRetencao": oRetencao,
                "inSession": true,
                "isUpdate": true
            }]
        };

        new AjaxRequest(
            'emp4_retencaonotaRPC.php',
            oParametro,
            function(oRetorno, lErro) {
                js_retornoaddRetencao(oRetorno);
            }
        ).execute();
    }

    /**
     * Confirmar as alterações realizadas nas retencoes.
     */
    function js_confirmar() {


        if (aBaseDeCalculo.length == 0) {
            alert("Antes de confirmar, é necessário lançar retenções.");
            return false;
        }

        js_divCarregando("Aguarde, salvando modificações", "msgBox");

        var oParametro = {
            "exec": "saveRetencoes",
            "params": [{
                "iCodNota": $F('e69_codnota'),
                "iCodOrd": $F('e50_codord'),
                "iCodMov": $F('e81_codmov'),
                "codigoOrdem": $F('e50_codord'),
                "aMovimentos": aBaseDeCalculo
            }]
        };

        new AjaxRequest(
            'emp4_retencaonotaRPC.php',
            oParametro,
            function(oRetorno, lErro) {
                js_retornoConfirma(oRetorno);
            }
        ).execute();

    }

    function js_retornoConfirma(oAjax) {

        isSave = false;
        js_removeObj("msgBox");
        //    var oRetorno = JSON.parse(oAjax.responseText);
        var oRetorno = oAjax;
        alert(oRetorno.message.urlDecode());

        return true;
        aBaseDeCalculo = new Array();
        if (oRetorno.status == 1) {

            if (lCallBack) {

                js_bloqueiaMenus(false);
                var nTotalRetencoes = gridRetencoes.sum(5, false);
                parent.js_atualizaValorRetencao($F('e81_codmov'),
                    nTotalRetencoes,
                    $F('e69_codnota'),
                    $F('e50_codord'),
                    oRetorno.lMesAnterior,
                    false
                );
            }
            if (closeOnSave) {
                js_bloqueiaMenus(false);
            }
        }
    }

    oBtnFechar = parent.$('fechardb_iframe_retencao');
    oBtnFechar.onclick = js_sair;
    oBtnMinimizar = parent.$('minimizardb_iframe_retencao');
    oBtnMinimizar.onclick = '';

    function js_sair() {

        if (isSave && !lSession) {

            if (confirm('Há Operações não salvas.\nSalvar antes de sair?')) {

                closeOnSave = true;
                js_confirmar();

            } else {

                js_bloqueiaMenus(false);
                parent.db_iframe_retencao.hide();

            }
        } else {

            js_bloqueiaMenus(false);
            if (lCallBack) {

                var nTotalRetencoes = gridRetencoes.sum(5, false);
                parent.js_atualizaValorRetencao($F('e81_codmov'), nTotalRetencoes, $F('e69_codnota'), $F('e50_codord'), null);

            }
            parent.db_iframe_retencao.hide();
        }

    }

    function adicionaRetencao(sValor, sChave, iTipoCalc, nAliquota, lLiberado) {
        var oCmbRetencao = $('e21_sequencial');
        var oCmbRetencaoDescr = $('e21_sequencialdescr');
        var oOptionKey = new Option(sChave, sChave);
        oOptionKey.tipocalc = iTipoCalc;
        oOptionKey.aliquota = nAliquota;
        oCmbRetencao.add(oOptionKey, null);
        var oOptionValue = new Option(sValor, sChave);
        oOptionValue.tipocalc = iTipoCalc;
        oOptionValue.aliquota = nAliquota;
        if (!lLiberado) {
            oOptionValue.disabled = true;
        }
        oCmbRetencaoDescr.add(oOptionValue, null);

    }

    function adicionaTipoServico(sValor, sChave, sequencial) {

        var oCmbTipoServico = $('e18_sequencial');
        var oCmbTipoServicoDescr = $('e18_sequencialdescr');
        var oOptionKey = new Option(sChave, sequencial);
        oOptionKey.descricao = sValor;
        oCmbTipoServico.add(oOptionKey, null);
        var oOptionValue = new Option(sValor, sequencial);
        oOptionValue.descricao = sValor;
        oCmbTipoServicoDescr.add(oOptionValue, null);
    }

    function js_setAliquota(selectedIndex) {

        $('e21_aliquota').value = $('e21_sequencial').options[selectedIndex].aliquota;
        $('valorpagar').value = $F('valormovimento');
        $('e23_valorbase').value = $F('e70_vlrliq'); //$F('e70_valor');
        js_calculaRetencao();

    }

    /**
     *  Valida EFDReinf
     *  Só deve aparecer o campo para selecionar o serviço caso seja uma pessoa júridica.
     *  Necessário que o tipo de cálculo seja 4, somente aplicados a contribuição previdenciária.
     *
     */
    function js_validaReinf(selectedIndex, focus = false) {
        if (!selectedIndex) {
            selectedIndex = $('e21_sequencial').selectedIndex;
        }

        var retencao = $('e21_sequencial').options[selectedIndex];
        var trReinf = $('dadosReinf');
        var r2010 = $('R-2010');
        var r2055 = $('R-2055');
        const r4010r4020 = $('R-4010-4020');

        // tipos de calculo que possuem campos a serem adicionados para o EFD-Reinf
        const tiposEFD = [
            TIPO_CALCULO_IRRF_PESSOA_JURIDICA,
            TIPO_CALCULO_IRRF_PESSOA_FISICA,
            TIPO_CALCULO_INSS_PESSOA_JURIDICA,
            TIPO_CALCULO_INSS_PESSOA_FISICA
        ];

        if (tiposEFD.includes(Number(retencao.tipocalc))) {
            trReinf.style.display = "table-row";
        } else {
            $('e18_sequencial').selectedIndex = 0;
            $('e18_sequencialdescr').selectedIndex = 0;
            trReinf.style.display = "none";
            return false;
        }

        if ([TIPO_CALCULO_IRRF_PESSOA_JURIDICA, TIPO_CALCULO_IRRF_PESSOA_FISICA].includes(Number(retencao.tipocalc))) {
            $('R-4010-4020').style.display = "table-row";

            let tipoPessoa = Number(retencao.tipocalc) === TIPO_CALCULO_IRRF_PESSOA_JURIDICA ? 'PJ' : 'PF';
            lookupNaturezaRendimento.setParametrosAdicionais([`declarante=${tipoPessoa}`, 'excluiGrupo10'])
            const objeto = {
                evento: 'R-4010-4020',
                id: $F('idNaturezaRendimento'),
                codigo: $F('codigoNaturezaRendimento'),
                descricao: $F('naturezaRendimento')
            }
            return objeto;
        }

        if ((tipoServicos.length > 0) && ($F('z01_cgccpf').length == 14) &&
            (retencao.tipocalc == TIPO_CALCULO_INSS_PESSOA_JURIDICA) ||
            retencao.tipocalc == TIPO_CALCULO_INSS_PESSOA_FISICA &&
            produtorrural == 't'
        ) {
            switch (produtorrural) {
                case 't':
                    r2055.style.display = "table-row";

                    var objeto = {
                        evento: 'R-2055',
                        vlrsenar: $F('vlrsenar'),
                        vlrrat: $F('vlrrat'),
                        vlrcp: $F('vlrcp'),
                        empnota: $F('e69_codnota')
                    }

                    return objeto;
                    break;

                case 'f':
                    r2010.style.display = "table-row";

                    const opcaoSelecionada = $('e18_sequencial').selectedIndex;
                    if (opcaoSelecionada == 0) {
                        $('e18_sequencialdescr').focus();
                        return false;
                    }

                    var objeto = {
                        evento: 'R-2010',
                        tipoServicoNotaFiscal: $('e18_sequencial').options[opcaoSelecionada].value,
                        tipoServicoNotaFiscal_descricao: $('e18_sequencial').options[opcaoSelecionada].descricao,
                        indObra: $F('indObra'),
                        indObraCNO: $F('indObraCNO'),
                        numemp: $F('e60_numemp')
                    }

                    var dadosAdicionais = dadosAdicionaisRetencao.pegaDados();
                    Object.assign(objeto, dadosAdicionais);

                    return objeto;
                    break;
            }
        }
    }

    function js_validaTipoServico() {
        const opcaoSelecionada = $('e18_sequencial').selectedIndex;
        if (opcaoSelecionada == 0) {
            dadosAdicionaisRetencao.remapear(true);
        }
    }

    function js_calculaRetencao() {

        var sCpfCnpj = $F('z01_cgccpf');
        var iTipoCalc = $('e21_sequencial').options[$('e21_sequencial').selectedIndex].tipocalc;
        var nAliquota = new Number($F('e21_aliquota'));
        var nDeducao = new Number($F('e23_deducao')).toFixed(2); //new Number($F('e23_deducao'));
        var nValorBase = new Number($F('e70_vlrliq'));; //new Number($F('e70_valor'));
        var nValorNota = new Number($F('e70_vlrliq'));; //new Number($F('e70_valor'));
        var dtPagamento = $F("dtpagamento");
        var aMovimentos = new Array();

        if (nDeducao < 0) {
            $('e23_deducao').value = "";
            alert('Valor da dedução não pode ser negativo.');
            return false;
        }

        if (nDeducao > nValorNota) {
            alert('Valor da dedução não pode ser maior que o valor da base de Cálculo.');
            return false;
        }

        if (aBaseDeCalculo[$F('e21_sequencial')] != null) {
            aMovimentos = aBaseDeCalculo[$F('e21_sequencial')];
        }

        if ($F('e21_sequencial') != '') {
            js_divCarregando("Aguarde, calculando..", "msgBox");
            var oParametro = {
                "exec": "calculaRetencao",
                "params": [{
                    "iTipoCalc": iTipoCalc,
                    "iCpfCnpj": sCpfCnpj,
                    "nAliquota": nAliquota,
                    "dtPagamento": dtPagamento,
                    "aMovimentos": aMovimentos,
                    "nValorDeducao": nDeducao,
                    "nValorBase": nValorBase,
                    "iCodNota": $F('e69_codnota'),
                    "nValorNota": nValorNota
                }]
            };
            new AjaxRequest(
                'emp4_retencaonotaRPC.php',
                oParametro,
                function(oRetorno, lErro) {
                    js_retornoCalculo(oRetorno);
                }
            ).execute();
        }
    }

    function js_retornoCalculo(oAjax) {

        js_removeObj("msgBox");
        var oRetorno = oAjax;
        if (oRetorno.status == 1) {
            $('e23_valorretencao').value = truncarDecimais(oRetorno.nValorRetencao);
            $('e21_aliquota').value = oRetorno.nAliquota;
            $('e23_valorbase').value = oRetorno.nValorBase;
            $('vlrrat').value = truncarDecimais(oRetorno.nValorGilrat);
            $('vlrsenar').value = truncarDecimais(oRetorno.nValorSenar);
            $('vlrcp').value = truncarDecimais(oRetorno.nValorCP);

            js_desabilitaBotoes(false);
        } else if (oRetorno.status == 2) {
            $('e23_valorretencao').value = '';
            js_desabilitaBotoes(true);
            if (isRN) {
                js_desabilitaBotoes(false);
            }
            alert(oRetorno.message.urlDecode());
        }
    }

    function js_confirmarPagamento() {
        if (aBaseDeCalculo.length == 0) {
            alert("Antes de confirmar, é necessário lançar retenções.");
            return false;
        }

        var sMensagemRetornoSave = 'Antes de pagar as retenções, confirme as modificações realizadas.';
        var sMensagemConfirmacao = 'Antes de pagar as retenções, revise se já salvou as últimas modificações.';
        if (lApropriacaoRetencao) {
            sMensagemRetornoSave = 'Antes de apropriar as retenções, é necessário confirmar as retenções lançadas. Para isso clique no botão confirmar.';
            sMensagemConfirmacao = 'Tem certeza que deseja apropriar as retenções? Clique em OK para confirmar.';
        }

        if (isSave) {
            alert(sMensagemRetornoSave);
            return false;
        }
        if (!confirm(sMensagemConfirmacao)) {
            return false;
        }

        //js_divCarregando("Aguarde, salvando modificações","msgBox");

        var oParametro = {
            "exec": "configurarRetencoes",
            "params": [{
                "iNumemp": $F('e60_numemp'),
                "iCodNota": $F('e69_codnota'),
                "iCodOrd": $F('e50_codord'),
                "iCodMov": $F('e81_codmov'),
                "aMovimentos": aBaseDeCalculo
            }]
        };

        new AjaxRequest(
            'emp4_retencaonotaRPC.php',
            oParametro,
            function(oRetorno) {
                js_retornoConfirmaPagamento(oRetorno);
            }
        ).execute();

    }

    function js_retornoConfirmaPagamento(oAjax) {

        isSave = false;
        //js_removeObj("msgBox");
        var oRetorno = oAjax;
        alert(oRetorno.message.urlDecode());

        if (oRetorno.sSlipsGeradoAutomatico != "" && oRetorno.status == 1) {
            if (confirm("Foram Gerados Slips para as Retenções, Deseja Imprimir ?")) {
                window.open('cai1_slip003.php?numslip=' + oRetorno.sSlipsGeradoAutomatico, '', 'location=0');
            }
        }

        if (oRetorno.status == 1) {

            js_bloqueiaMenus(false);
            parent.db_iframe_retencao.hide();
            parent.js_pesquisarOrdens();

        }


    }

    js_bloqueiaMenus(true);
    js_init();

    $('e21_sequencial').style.width = '50px';
    $('e21_sequencialdescr').style.width = '200px';
    <?php
    /*
     * Consultamos as retencoes Existes e adicionamos os tipos conforme o tipo de pessoa
     * do CGM.
     * se for fisica, apenas disponibilizamos as retencoes do tipo 1,3,5
     * se for juridica, apenas disponibilizamos as retencoes do tipo 2,4,5
     * Caso for pessoa juridica, devemos verificar se o movimento nao está incluido em outra
     * retencao.
     */
    $oDaoRetencao = new cl_retencaotiporec;
    $sSQLRetencao = $oDaoRetencao->sql_query(
        null,
        "e21_sequencial, e21_descricao, e21_retencaotipocalc, e21_aliquota",
        "e21_sequencial",
        "e21_instit=" . db_getsession("DB_instit") . "
         and e21_retencaotiporecgrupo = 1
         and e21_ativo is true "
    );
    $rsRetencao = $oDaoRetencao->sql_record($sSQLRetencao);
    if (strlen($oNota->z01_cgccpf) == 14) {
        $sPessoa = "J";
    } elseif (strlen($oNota->z01_cgccpf) == 11) {
        $sPessoa = "F";
    } else {
        $sPessoa = null;
    }
    if ($oDaoRetencao->numrows > 0) {
        $aRetencoes = db_utils::getCollectionByRecord($rsRetencao);
        foreach ($aRetencoes as $oRetencao) {
            $lAdiciona = true;
            switch ($oRetencao->e21_retencaotipocalc) {
                case 1:
                    if ($sPessoa != "F") {
                        $lAdiciona = false;
                    }
                    break;
                case 2:
                    if ($sPessoa != "J") {
                        $lAdiciona = false;
                    }
                    break;
                case 3:
                    if ($sPessoa != "F") {
                        $lAdiciona = false;
                    }
                    break;
                case 4:
                    if ($sPessoa != "J") {
                        $lAdiciona = false;
                    }
                    break;
                case 7:
                    if ($sPessoa != "F") {
                        $lAdiciona = false;
                    }
                    break;
                default:
                    $lAdiciona = true;
                    break;
            }

            if ($lAdiciona) {
                $lLiberado = "true";
                if ($oGet->iCodMov != "") {
                    $oRetencaoAtiva = $oRetencaoNota->getRetencoesByMovimento($oGet->iCodMov, $oRetencao->e21_sequencial);
                    if ($oRetencaoAtiva && $sPessoa == "J") {
                        if (isset($oRetencaoAtiva->e27_principal) && $oRetencaoAtiva->e27_principal == "f") {
                            $lLiberado = "false";
                        }
                    }
                }
                echo "adicionaRetencao('{$oRetencao->e21_descricao}','{$oRetencao->e21_sequencial}','{$oRetencao->e21_retencaotipocalc}',";
                echo "'$oRetencao->e21_aliquota', {$lLiberado});\n";
            }
        }
    }

    ?>

    function js_chkcpf(vcic) {

        expr = new RegExp("0{11}|1{11}|2{11}|3{11}|4{11}|5{11}|6{11}|7{11}|8{11}|9{11}");
        if (vcic.value.match(expr)) {

            vcic.value = "";
            vcic.focus();
            return false;
        }
        if (isNaN(vcic.value) || vcic.value.length != 11) {
            return false;
        }

        for (var vdigpos = 10; vdigpos < 12; vdigpos++) {

            var vdig = 0;
            var vpos = 0;
            for (var vfator = vdigpos; vfator >= 2; vfator--) {

                vdig = eval(vdig + vcic.value.substr(vpos, 1) * vfator);
                vpos++;

            }
            vdig = eval(11 - (vdig % 11)) < 10 ? eval(11 - vdig % 11) : 0;
            if (vdig != eval(vcic.value.substr(vdigpos - 1, 1))) {

                vcic.value = "";
                vcic.focus();
                return false;
            }
        }
        return true;
    }

    //validação de cnpj
    function js_chkcnpj(vcnpj) {

        if (isNaN(vcnpj.value) || vcnpj.value.length != 14) {
            return false;
        }
        for (var vdigpos = 13; vdigpos < 15; vdigpos++) {

            var vdig = 0;
            var vpos = 0;
            for (var vfator = vdigpos - 8; vfator >= 2; vfator--) {

                vdig = eval(vdig + vcnpj.value.substr(vpos, 1) * vfator);
                vpos++;

            }
            for (var vfator = 9; vfator >= 2; vfator--) {

                vdig = eval(vdig + vcnpj.value.substr(vpos, 1) * vfator);
                vpos++;

            }
            vdig = eval(11 - (vdig % 11)) < 10 ? eval(11 - vdig % 11) : 0;
            if (vdig != eval(vcnpj.value.substr(vdigpos - 1, 1))) {
                return false;
            }
        }
        return true;
    }

    /*
     * Validamos o cpf/cnpj do credor, caso esteja incorreto, não deixamos o usuário
     * cadastrar retenções.
     */
    sMsgCnpjIncorreto = "Não será possível incluir retenções para esta nota porque seu credor apresenta ";
    sMsgCnpjIncorreto += "código de CPF/CNPJ nulo ou inválido.\n";
    sMsgCnpjIncorreto += "Será necessário corrigir seu cadastro no CGM para realizar esta operação.";

    if ($F('z01_cgccpf').length == 14) {
        if (!js_chkcnpj($('z01_cgccpf'))) {
            alert(sMsgCnpjIncorreto);
            js_desabilitaBotoes(true);
        }
    } else if ($F('z01_cgccpf').length == 11) {
        if (!js_chkcpf($('z01_cgccpf'))) {
            alert(sMsgCnpjIncorreto);
            js_desabilitaBotoes(true);
        }
    } else if ($F('z01_cgccpf').length != 11 || $F('z01_cgccpf').length != 14) {
        alert(sMsgCnpjIncorreto);
        js_desabilitaBotoes(true);
    }
    js_consultaRetencoesNota();

    function js_mostraInfo() {

        var iCodigoRetencao = $F('e21_sequencial');
        var iCpfCGC = $F('z01_cgccpf');
        var iTipoCalculo = 0;
        var sValorFiltro = "";

        if (iCodigoRetencao == "") {
            alert('Informe a Retenção.');
            return false;
        }
        if (iCpfCGC.length == 14) {
            iTipoCalculo = 1; //apenas calculos sobre a nota de liquidacao
            sValorFiltro = $F('e50_codord');
        } else if (iCpfCGC.length == 11) {
            iTipoCalculo = 2; //calculos sobre valores pagos no mes
            sValorFiltro = iCpfCGC;
        } else {
            alert("CNPJ/CPF inválido.");
        }
        if (iTipoCalculo != 0) {

            js_OpenJanelaIframe('', 'db_iframe_inforetencao',
                'emp4_inforetencao.php?iTipoCalculo=' + iTipoCalculo + '&sValorFiltro=' + sValorFiltro +
                '&iNumEmp=' + $F('e60_numemp') + "&iCodMov=" + $F('e81_codmov') + "&iCodRetencao=" + iCodigoRetencao +
                '&iCodNota=' + $F('e69_codnota'),
                'Outras Informações (Base de Cálculo)',
                true, 10, 10, 750, 500);
        }
    }

    function js_tabelas() {

        js_OpenJanelaIframe('', 'db_iframe_tabelas',
            'emp4_tabelasretencao.php',
            'Tabelas de Cálculo Pessoa Fisica',
            true, 10, 10, 750, 500);
    }

    function setBaseDeCaculo(aBaseDeCalculo1, iCodigoRetencao) {
        aBaseDeCalculo[iCodigoRetencao] = aBaseDeCalculo1;
    }

    function js_desabilitaBotoes(lDesabilita) {
        $('lancarretencao').disabled = lDesabilita;
        $('alterarretencao').disabled = lDesabilita;
        $('apagarretencao').disabled = lDesabilita;
        $('mostraoutrosmov').disabled = lDesabilita;
        $('recalcularretencao').disabled = lDesabilita;
        $('limparretencao').disabled = lDesabilita;
    }

    //controle de tela, caso false, o usuário nao fez nenhuma modificação, caso true, a modificações.
    isSave = false;

    /*
     * passa para lookup todos os options da combobox de retençoes
     */
    function js_pesquisaRetencoes() {

        var aElemOpcao = $$("class='retencoes-info' select[name='e21_sequencial'] option");
        var sLista = new String(['']);
        var sListaNova = new String(['']);
        aElemOpcao.each(
            function(oElemOpcao, iInd) {
                sLista += oElemOpcao.value + ',';
            }
        );

        var tam = sLista.length - 1;
        sLista = sLista.slice(1, tam);

        var sUrl = 'func_retencaotiporec.php?somenteAtivo';
        sUrl += '&chave_pesquisa_in=+' + sLista;
        sUrl += '&funcao_js=parent.js_mostra|e21_sequencial';
        js_OpenJanelaIframe('', 'db_iframe_retencaotiporec', sUrl, 'Pesquisa', true, 5, 5, 1500, 500);
    }

    function js_mostra(chave1) {

        $('e21_sequencial').value = chave1;

        js_ProcCod_e21_sequencial('e21_sequencial', 'e21_sequencialdescr');
        js_setAliquota($('e21_sequencial').selectedIndex);
        js_validaReinf();
        checkIfPeriodEFDReinfIsClosed();

        db_iframe_retencaotiporec.hide();
    }

    /**
     * Cálcula o total retido na tabela de retenções lançadas.
     * @param {int} iRetencaoSelecionada Código da retenção selecionada, caso seja alteração.
     * @returns {Number} Total da tabela de retenções lançadas.
     */
    function getTotalRetido(iRetencaoSelecionada) {

        var nTotalRetido = new Number(0);

        var aRetencoes = gridRetencoes.getRows();
        for (var iIndice = 0; iIndice < aRetencoes.length; iIndice++) {

            if (aRetencoes[iIndice].aCells[0].getValue() == iRetencaoSelecionada) {
                continue;
            }
            nTotalRetido += js_strToFloat(aRetencoes[iIndice].aCells[5].getValue());
        }

        return nTotalRetido;
    }

    var isPB = '<?php echo $isPB ?>';

    //devido a PB necessitar de uma conta para apropriação, ela somente ocorrerá ao pagar movimento na agenda
    if (isPB) {

        $('configurarretencoes').disabled = 'disabled';
        $('configurarretencoes').setAttribute('title', 'Retenção será apropriada ao confirmar o pagamento do movimento na agenda.');
        $('configurarretencoes').setAttribute('onclick', '');
    }

    /**
     * Rotinas referentes a subcontratacoes
     */
    function js_openFrameSubcontratacao() {
        let sUrl = 'emp4_retencaoreceitassubcontracao.php';
        js_OpenJanelaIframe('this', 'db_iframe_subcontratacoes', sUrl, '', true, 0, 900, 900);
    }

    function js_enableSubcontratacao(selectedIndex) {
        tipoCalculo = $('e21_sequencial').options[selectedIndex].tipocalc;
        tipoCalculoIRRF = ['1', '2']
        if (tipoCalculoIRRF.indexOf(tipoCalculo) != -1) {
            $('subcontratacoesbtn').removeClassName('d-none');
        } else {
            $('subcontratacoesbtn').addClassName('d-none');
        }
    }

    function js_getDadosSubcontratacao() {
        if (sessionStorage.key('subcontratacoes')) {
            let subcontratacoes = sessionStorage.getItem('subcontratacoes');
            return JSON.parse(subcontratacoes);
        } else {
            return null;
        }
    }

    function js_clearSubcontratacao() {
        $('subcontratacoesbtn').addClassName('d-none');
        sessionStorage.removeItem('subcontratacoes');
    }

    function js_setSubcontratacoes(data) {
        sessionStorage.setItem('subcontratacoes', JSON.stringify(data));
    }

    /**
     * Validar apenas valores numéricos e limite para 12 caracteres no input CNO
     * */
    document.addEventListener('DOMContentLoaded', function() {
        const numericInput = document.getElementById('indObraCNO');

        // Adiciona um ouvinte de evento de entrada para validar a entrada
        numericInput.addEventListener('input', function() {
            const inputValue = numericInput.value;
            // Usa uma expressão regular para remover caracteres não numéricos
            const numericValue = inputValue.replace(/[^0-9]/g, '');
            // Limita o valor ao comprimento máximo
            numericInput.value = numericValue.substring(0, numericInput.maxLength);
        });
    });

    /**
     * Verificar status EFDREINF esta fechado (r-2099, r4099)
     */
    async function checkIfPeriodEFDReinfIsClosed() {
        const retencaoSelected = document.querySelector('#e21_sequencial');
        const tipoCalculo = retencaoSelected.selectedOptions[0].tipocalc;
        const currentDate = document.querySelector('#dtpagamento').value.split('/');
        let competence = `${currentDate[2]}-${currentDate[1].padStart(2, '0')}`;
        let event = null;

        switch (Number(tipoCalculo)) {
            case TIPO_CALCULO_INSS_PESSOA_FISICA:
            case TIPO_CALCULO_INSS_PESSOA_JURIDICA:
                event = 'R-2099';
                break;
            case TIPO_CALCULO_IRRF_PESSOA_FISICA:
            case TIPO_CALCULO_IRRF_PESSOA_JURIDICA:
                event = 'R-4099';
                break;
        }

        if (!event) return;

        try {
            const url = '/v4/api/integracoes/efd-reinf/events/checkPeriodIsClosed';
            const params = {
                event,
                competence
            }
            const req = await CurrentWindow.axios.get(url, {
                params
            });
            const {
                data: resp
            } = req;
            const {
                data: eventData
            } = resp;
            if (!eventData) return;
            if (eventData.closed === true) {
                alert(`
                    <b>Aviso</b>\n
                    Período <b>EFD-REINF(${event})</b> fechado para a competência <b>${competence}</b>.\n
                    Após o lançamento da retenção deve-se realizar a reabertura do período
                    e processar/enviar os eventos novamente.
                `);
            }
        } catch (error) {
            console.error(error);
        }
    }

    function truncarDecimais(valor, casas = 2) {
        console.log(valor);
        if (typeof valor !== 'number' || isNaN(valor)) {
            console.error('Valor inválido:', valor);
            return '';
        }
        const fator = 10 ** casas;
        return (Math.floor(valor * fator) / fator).toFixed(casas);
    }
</script>
