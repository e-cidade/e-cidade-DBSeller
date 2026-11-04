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
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("model/relatorioContabil.model.php"));
require_once(modification("libs/db_utils.php"));

$iAnoUsu = db_getsession("DB_anousu");
$oGet = db_utils::postMemory($_GET);
$codigoRelatorio = $oGet->codigoRelatorio;

$oRelatorio = new relatorioContabil($codigoRelatorio);
$clrotulo = new rotulocampo;
$clrotulo->label('DBtxt21');
$clrotulo->label('DBtxt22');

$aPeriodos = $oRelatorio->getPeriodos();
$aListaPeriodos = array();
$aListaPeriodos[0] = "Selecione";
foreach ($aPeriodos as $oPeriodo) {
    $aListaPeriodos[$oPeriodo->o114_sequencial] = $oPeriodo->o114_descricao;
}

/**
 * Verifica se instituicao atual é prefeitura
 */
$iInstituicao = db_getsession('DB_instit');
$oInstituicao = new Instituicao($iInstituicao);
$isPrefeitura = $oInstituicao->isPrefeitura() === 't';

$sStyleDisplay = '';
if (!$isPrefeitura) {
    $sStyleDisplay = 'display:none;';
}

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/arrays.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">

    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBViewInstituicao.widget.js"></script>
    <script language="JavaScript" type="text/javascript"
            src="scripts/widgets/datagrid/plugins/DBHint.plugin.js"></script>
</head>
<body style="background-color: #CCCCCC; margin-top: 30px;">
<div class="container">

    <table style="width: 445px;">
        <tr>
            <td class='table_header'>
                <?php echo $oRelatorio->getDescricao(); ?>
            </td>
        </tr>
    </table>

    <fieldset style="width: 400px;">

        <legend><strong>Filtros</strong></legend>

        <div id="ctnGridInstituicao" style="<?= $sStyleDisplay; ?>"></div>
        <table>
            <tr>
                <td><label for="o116_periodo"><b>Período:</b></label></td>
                <td><?php db_select("o116_periodo", $aListaPeriodos, true, 1); ?></td>
            </tr>
            <tr>
                <td><label class="bold" for="quadroAuxiliar">Quadro Auxiliar:</label></td>
                <td>
                    <select id="quadroAuxiliar">
                        <option value="1">Imprimir</option>
                        <option value="0">Não Imprimir</option>
                    </select>
                </td>
            </tr>
        </table>

        <fieldset style="border-bottom: 0; border-left: 0; border-right: 0; margin-top:10px;">
            <legend class="bold">Opções de Impressão</legend>
            <table style="width: 100%;" border="0">
                <tr>
                    <td style="width:50%" nowrap><label for="imprimirValorExercicioAnterior"><b>Imprimir Valores do
                                Exercício Anterior:</b></label></td>
                    <td><?php db_select('imprimirValorExercicioAnterior', array(true => 'Sim', false => 'Não'), true, 1); ?></td>
                </tr>
                <tr>
                    <td nowrap><label for="tipoImpressao"><b>Tipo de Impressão:</b></label></td>
                    <td>
                        <?php
                        $aTipoImpressao = ["S" => "Sintético", "A" => "Analítico"];
                        db_select('tipoImpressao', $aTipoImpressao, true, 1);
                        ?>
                    </td>
                </tr>
                <tr id='linhaApresentarRecurso' style="display:none">
                    <td class="bold">Apresentar :</td>
                    <td>
                        <select id="apresentarRecurso" name="apresentarRecurso">
                            <option value="depara">Depara Subrecurso</option>
                            <option selected value="siconfi">Código Siconfi</option>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>
    </fieldset>

    <input name="emite" id="emite" type="button" value="Gerar" onclick="validarConfiguracao();">
</div>
</body>
</html>
<script>
    var oComboboxExercicioAnterior = $('imprimirValorExercicioAnterior');
    var oComboBoxTipoImpressao = $('tipoImpressao');
    var oComboBoxPeriodo = $('o116_periodo');
    oComboboxExercicioAnterior.style.width = '100%';
    oComboBoxPeriodo.style.width = '100%';
    oComboBoxTipoImpressao.style.width = '100%';

    var iAnoUsu = '<?php echo $iAnoUsu; ?>';
    var iCodigoRelatorio = '<?php echo $codigoRelatorio; ?>';
    var isPrefeitura = <?php echo $isPrefeitura ? 'true' : 'false'; ?>;
    var iInstituicao = <?php echo $iInstituicao; ?>;

    /**
     * Instituicao logada é prefeitura
     * - exibe componente com todas as instituições
     */

    oViewInstituicao = new DBViewInstituicao('oViewInstituicao', $('ctnGridInstituicao'));
    oViewInstituicao.setWidth(400);
    oViewInstituicao.setHeight(130);
    oViewInstituicao.show();

    /**
     * Emite o relatório
     * @returns {boolean}
     */
    function emitir() {

        var iCodigoPeriodo = $F('o116_periodo');
        var sTipoImpressao = $F('tipoImpressao');
        var lConsolidado = false;
        var sInstituicao = iInstituicao;

        /**
         * Busca as instituicoes selecionadas
         * - caso exista o componente com as instituicoes, exibido somente na prefeitura
         */
        if (oViewInstituicao != null) {

            var aInstituicoesSelecionadas = oViewInstituicao.getInstituicoesSelecionadas(true);

            if (aInstituicoesSelecionadas.length == 0) {

                alert("Selecione ao menos uma Instituição.");
                return false;
            }

            if (oViewInstituicao.getTotalInstituicoes() == aInstituicoesSelecionadas.length) {
                lConsolidado = true;
            }

            sInstituicao = aInstituicoesSelecionadas.implode("-");
        }

         var sProgramaRelatorio = "con2_balancofinanceiro_2015.php?db_selinstit=" + sInstituicao;
        sProgramaRelatorio += "&periodo=" + iCodigoPeriodo;
        sProgramaRelatorio += "&consolidado=" + (lConsolidado ? 'true' : 'false');
        sProgramaRelatorio += "&codrel=" + iCodigoRelatorio;
        sProgramaRelatorio += "&tipoImpressao=" + sTipoImpressao;
        sProgramaRelatorio += "&quadroAuxiliar=" + $F('quadroAuxiliar');
        sProgramaRelatorio += "&apresentarRecurso=" + $F('apresentarRecurso');
        sProgramaRelatorio += "&imprimirValorExercicioAnterior=" + (oComboboxExercicioAnterior.value == 1 ? 'true' : 'false');

        var jan = window.open(sProgramaRelatorio, '', 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
        jan.moveTo(0, 0);
    }


    function validarConfiguracao() {
        var oGet = js_urlToObject();
        if (oGet.codigoRelatorio == 277) {
            emitir();
            return ;
        }

        var iCodigoPeriodo = $F('o116_periodo');
        var sTipoImpressao = $F('tipoImpressao');
        var lConsolidado = false;
        var sInstituicao = iInstituicao;

        /**
         * Busca as instituicoes selecionadas
         * - caso exista o componente com as instituicoes, exibido somente na prefeitura
         */
        if (oViewInstituicao != null) {

            var aInstituicoesSelecionadas = oViewInstituicao.getInstituicoesSelecionadas(true);

            if (aInstituicoesSelecionadas.length == 0) {

                alert("Selecione ao menos uma Instituição.");
                return false;
            }

            if (oViewInstituicao.getTotalInstituicoes() == aInstituicoesSelecionadas.length) {
                lConsolidado = true;
            }

            sInstituicao = aInstituicoesSelecionadas.implode("-");
        }

        if (iCodigoPeriodo == "0") {

            alert("O campo Período é de preenchimento obrigatório.");
            return false;
        }


        var oParametro = {
            exec: 'getRecursosNaoConfigurados',
            iCodigoRelatorio: oGet.codigoRelatorio,
            imprimirValorExercicioAnterior: (oComboboxExercicioAnterior.value == 1 ? 't' : 'f'),
            iCodigoPeriodo: $F('o116_periodo'),
            aCodigosInstituicao: oViewInstituicao.getInstituicoesSelecionadas(true)
        };
        var lPossuiRecursoDesconfigurado = false;

        js_divCarregando('Aguarde, validando configuração...', 'msgbox');
        obj = new Ajax.Request(
                              'con2_relatoriosdcasp.RPC.php',
                              {
                               method: 'POST',
                               parameters: 'json='+Object.toJSON(oParametro),
                               onSuccess : js_retornoRecursosNaoConfigurados
                              }
                             );
        }

        function js_retornoRecursosNaoConfigurados(oAjax) {

    	  var oRetorno = JSON.parse(oAjax.responseText);
          js_removeObj('msgbox');

          if (oRetorno.sArquivo.trim() != '') {

            lPossuiRecursoDesconfigurado = true;
            var oDownloadWindow = new DBDownload();
            oDownloadWindow.addFile(oRetorno.sArquivo.urlDecode(), "Recursos Não Configurados");
            oDownloadWindow.show();
            var sMensagem = "Foram identificados recursos com movimentação financeira que não estão configurados \n";
            sMensagem += "nos Parâmetros do relatório e isso ocasionará em discrepância entre os valores.\n\nConfirma a emissão do relatório?";
            if (!confirm(sMensagem)) {
                return false;
            }
          }
          emitir();
        }

    document.getElementById('tipoImpressao').addEventListener('change', function (){
        document.getElementById('linhaApresentarRecurso').style.display = 'none';
        if (document.getElementById('tipoImpressao').value === 'A') {
            document.getElementById('linhaApresentarRecurso').style.display = 'table-row';
        }
    });
</script>
