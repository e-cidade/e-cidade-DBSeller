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
include_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_liborcamento.php"));

$clrotulo = new rotulocampo;
$clrotulo->label('DBtxt21');
$clrotulo->label('DBtxt22');
?>
<html>
<head>

    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">

    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBHint.widget.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>

    <script type="text/javascript" src="scripts/widgets/DBViewInstituicao.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>

    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>

<body>
<script type="text/javascript" src="scripts/session.js"></script>
<div class="container">
    <form name="frmBalanceteVerificacao" method="post" action="">
        <fieldset>

            <legend>Balancete de Verificação</legend>

            <!-- Filtros de Pesquisa -->
            <fieldset class="separator">

                <legend>Filtros de Pesquisa</legend>

                <table class="form-container">
                    <tr>
                        <td>
                            <label class="bold" id="lbl_data_inicial" for="DBtxt21">Data Inicial:</label>
                        </td>
                        <td>
                            <?php
                            $DBtxt21_ano = db_getsession("DB_anousu");
                            $DBtxt21_mes = '01';
                            $DBtxt21_dia = '01';
                            db_inputdata('DBtxt21', $DBtxt21_dia, $DBtxt21_mes, $DBtxt21_ano, true, 'text', 4);
                            ?>
                        </td>

                        <td>
                            <label class="bold" id="lbl_data_final" for="DBtxt22">Data Final:</label>
                        </td>
                        <td>
                            <?php
                            $DBtxt22_ano = db_getsession("DB_anousu");
                            $DBtxt22_mes = date("m", db_getsession("DB_datausu"));
                            $DBtxt22_dia = date("d", db_getsession("DB_datausu"));
                            db_inputdata('DBtxt22', $DBtxt22_dia, $DBtxt22_mes, $DBtxt22_ano, true, 'text', 4);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td style="width: 170px;">
                            <label id="lbl_sistema_contas" for="sistema_contas">Sistema de Contas:</label>
                        </td>
                        <td colspan="3">
                            <?php
                            if (!USE_PCASP) {
                                $aOpcoesSistema = [
                                    'T' => 'Todos',
                                    'F' => 'Financeiro',
                                    'C' => 'Compensado',
                                    'P' => 'Patrimonial',
                                    'O' => 'Orçamentário',
                                ];
                            } else {
                                $aOpcoesSistema = [
                                    '' => 'Todos',
                                    '0' => 'Não aplicável',
                                    '1' => 'Subsistema de Informações Orçamentárias',
                                    '2' => 'Subsistema de informações Patrimoniais',
                                    '3' => 'Subsistema de Custos',
                                    '4' => 'Subsistema de Compensação',
                                ];
                            }
                            $sistema_contas = 'T';
                            db_select("sistema_contas", $aOpcoesSistema, true, 2, 'style="width: 100%;"')
                            ?>
                        </td>
                    </tr>

                    <?php if (USE_PCASP) : ?>
                        <tr>
                            <td>
                                <label id="lbl_indicador" for="indicador_superavit">Indicador de Superávit:</label>
                            </td>
                            <td colspan="3">
                                <?php
                                $aIndicadores = [
                                    '' => 'Todos',
                                    'N' => 'N - Não se aplica',
                                    'F' => 'F - Financeiro',
                                    'P' => 'P - Permanente',
                                ];
                                db_select('indicador_superavit', $aIndicadores, true, 1, 'style="width: 50%;"');
                                ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <tr>
                        <td>
                            <label id="lbl_encerramento" for="encerramento">Encerramento de Exercício:</label>
                        </td>
                        <td colspan="3">
                            <?php
                            db_select('encerramento', ['n' => 'Não', 's' => 'Sim'], true, 1, 'style="width: 50%;"');
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label class="bold" id="lbl_estruturais" for="estrut_inicial">Estruturais:</label>
                        </td>
                        <td colspan="3">
                            <?php
                            $Testrut_inicial = 'Informe os estruturais separados por vírgula';
                            db_input('estrut_inicial', '', '', false, '', '', 'style="width: 100%;"')
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="4" >
                            <fieldset id="containerRecursos">
                                <legend >Recursos</legend>
                                <?php require_once modification('forms/db_frmfiltrorecursos.php') ?>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div id="lista-instituicoes">&nbsp;</div>
                        </td>
                    </tr>

                </table>
            </fieldset>

            <!-- Opções de visualização -->
            <fieldset class="separator">

                <legend>Opções de Visualização</legend>

                <table class="form-container">
                    <tr>
                        <td style="width: 195px;">
                            <label class="bold" for="emissao">Emissão:</label>
                        </td>
                        <td>
                            <?php
                            $aOpcoesEmissao = [
                                'pdf' => 'PDF',
                                'csv' => 'CSV',
                            ];
                            db_select("emissao", $aOpcoesEmissao, true, 2, 'style="width: 50%;"');
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td style="width: 195px;">
                            <label class="bold" id="lbl_tipo" for="tipo">Tipo:</label>
                        </td>
                        <td>
                            <?php
                            $aOpcoesTipo = [
                                'A' => 'Analítico',
                                'S' => 'Sintético até o 7º nível',
                            ];
                            db_select("tipo", $aOpcoesTipo, true, 2, 'style="width: 50%;"');
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label class="bold" id="lbl_movimento" for="movimento">Somente Contas com
                                Movimento:</label>
                        </td>
                        <td>
                            <?php
                            $aOpcoesContaMovimento = ['S' => 'Sim', 'N' => 'Não'];
                            db_select("movimento", $aOpcoesContaMovimento, true, 2, 'style="width: 50%;"');
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label class="bold" id="lbl_conta" for="conta">Exibir Conta Bancária:</label>
                        </td>
                        <td>
                            <?php
                            $aOpcoesExibirConta = ['S' => 'Sim', 'N' => 'Não'];
                            db_select("conta", $aOpcoesExibirConta, true, 2, 'style="width: 50%;"');
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label id="lbl_agrupa_estrutural" for="agrupa_estrutural">Consolidado por Estrutural:</label>
                        </td>
                        <td>
                            <?php
                            $aOpcoesConsolidadoEstrutural = ['0' => 'Sim', '1' => 'Não'];
                            db_select("agrupa_estrutural", $aOpcoesConsolidadoEstrutural, true, 2, 'style="width: 50%;"');
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </fieldset>

        <input style="margin-top: 20px;" name="emite" id="emite" type="button" value="Processar"
               onclick="js_emite();">

        <input type="hidden" name="use_pcasp" value="<?php echo USE_PCASP ? '1' : '0'; ?>">
    </form>
</div> <!-- container -->

<script type="text/javascript" src="scripts/widgets/datagrid/plugins/DBHint.plugin.js"></script>
<script>
    const URL_RELATORIO = "con2_balancverificacao002.php";
    const URL_RELATORIOCSV = "con2_balancverificacao002_csv.php";
    var USE_PCASP;
    var oViewInstituicao;

    document.observe('dom:loaded', function () {
        oViewInstituicao = new DBViewInstituicao('oViewInstituicao', $('lista-instituicoes'));
        oViewInstituicao.show();

        new DBToogle($('containerRecursos'), false);

        USE_PCASP = document.frmBalanceteVerificacao.use_pcasp.value;

        if (USE_PCASP == '1') {
            $('sistema_contas').value = '';
        }
    });

    function js_emite() {

        var oInstituicoes = oViewInstituicao.getInstituicoesSelecionadas();
        var sInstituicoes = '';
        var oForm = document.frmBalanceteVerificacao;

        var oDataInicial = new Date(oForm.DBtxt21_ano.value, oForm.DBtxt21_mes.value, oForm.DBtxt21_dia.value, 0, 0, 0);
        var oDataFinal = new Date(oForm.DBtxt22_ano.value, oForm.DBtxt22_mes.value, oForm.DBtxt22_dia.value, 0, 0, 0);

        var sPeriodoInicial = oForm.DBtxt21_ano.value + '-' + oForm.DBtxt21_mes.value + '-' + oForm.DBtxt21_dia.value;
        var sPeriodoFinal = oForm.DBtxt22_ano.value + '-' + oForm.DBtxt22_mes.value + '-' + oForm.DBtxt22_dia.value;

        /**
         * Validações
         */
        if (oInstituicoes.length == 0) {
            alert('Selecione ao menos uma Instituição.');
            return;
        }
        if (empty(oForm.DBtxt21.value)) {
            alert('O campo Data Inicial deve ser informado.');
            return;
        }
        if (empty(oForm.DBtxt22.value)) {
            alert('O campo Data Final deve ser informado.');
            return;
        }
        if (oDataInicial.valueOf() > oDataFinal.valueOf()) {
            alert('Data Final deve ser maior ou igual a Data Inicial.');
            return false;
        }

        /**
         * Prepara os dados para enviar ao relatório
         */

        // Converte o objeto retornado pelo DBViewInstituicao para uma string
        // de códigos separados por vírgula
        for (var i = 0; i < oInstituicoes.length; i++) {

            sInstituicoes += oInstituicoes[i].codigo;
            // Não coloca vírgula se for o último
            if (i + 1 != oInstituicoes.length) {
                sInstituicoes += ',';
            }
        }

        let recursos = collectionRecurso.build();
        let sRecurso = "";
        if (recursos.length > 0) {
            sRecurso = recursos.map((recurso) => {
                return recurso.codigo
            }).join(',');
        }

        var sQuery = '?';
        sQuery += 'encerramento=' + oForm.encerramento.value;
        sQuery += '&sistema_contas=' + oForm.sistema_contas.value;
        sQuery += '&agrupa_estrutural=' + oForm.agrupa_estrutural.value;
        sQuery += '&conta=' + oForm.conta.value;
        sQuery += '&estrut_inicial=' + oForm.estrut_inicial.value;
        sQuery += '&db_selinstit=' + sInstituicoes;
        sQuery += '&movimento=' + oForm.movimento.value;
        sQuery += '&emissao=' + oForm.emissao.value;
        sQuery += '&tipo=' + oForm.tipo.value;
        sQuery += '&recurso=' + sRecurso;
        sQuery += '&perini=' + sPeriodoInicial;
        sQuery += '&perfin=' + sPeriodoFinal;

        if (USE_PCASP == '1') {
            sQuery += '&indicador_superavit=' + oForm.indicador_superavit.value;
        }

        var iHeight = (screen.availHeight - 40);
        var iWidth = (screen.availWidth - 5);
        var sOpcoes = 'width=' + iWidth + ',height=' + iHeight + ',scrollbars=1,location=0';
        if (oForm.emissao.value == "csv") {
            var oJanela = window.open(URL_RELATORIOCSV + sQuery, '', sOpcoes);
        } else {
            var oJanela = window.open(URL_RELATORIO + sQuery, '', sOpcoes);
        }
        oJanela.moveTo(0, 0);
    }
</script>

<?php db_menu() ?>
</body>
</html>
