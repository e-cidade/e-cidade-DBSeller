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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("libs/db_liborcamento.php"));

$clrotulo = new rotulocampo;
$clrotulo->label('DBtxt21');
$clrotulo->label('DBtxt22');
$daoContaCorrente = new cl_conplanosistema();
$where = "c122_tipo = 2";
$campos = "c122_sequencial as codigo, c122_descricao as nome";
$sqlContaCorrente = $daoContaCorrente->sql_query_file(null, $campos, "c122_sequencial", $where);
$rsContaCorrentes = db_query($sqlContaCorrente);
$totalLinhas = pg_num_rows($rsContaCorrentes);
$contascorrentes = array("0" => "Selecione");
for ($i = 0; $i < $totalLinhas; $i++) {

    $dado = db_utils::fieldsMemory($rsContaCorrentes, $i);
    $contascorrentes[$dado->codigo] = $dado->nome;

}
?>
<html>
<head>

    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">

    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBHint.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript"
            src="scripts/widgets/datagrid/plugins/DBHint.plugin.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBViewInstituicao.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>

    <link href="estilos.css" rel="stylesheet" type="text/css">

</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#cccccc">

<div class="container">

    <form name="frmBalanceteVerificacao" method="post" action="">
        <table>
            <tr>
                <td align="center" colspan="3">
                    <fieldset>

                        <legend>Balancete de Verificação</legend>

                        <!-- Filtros de Pesquisa -->
                        <fieldset class="separator">

                            <legend>Filtros de Pesquisa</legend>

                            <table style="margin-top: 10px; width: 100%;">

                                <tr>
                                    <td>
                                        <label class="bold" id="lbl_data_inicial" for="DBtxt21">Data Inicial:</label>
                                    </td>
                                    <td>
                                        <?php
                                        $DBtxt21_ano = db_getsession("DB_anousu");
                                        $DBtxt21_mes = '01';
                                        $DBtxt21_dia = '01';
                                        db_inputdata('DBtxt21', $DBtxt21_dia, $DBtxt21_mes, $DBtxt21_ano, true, 'text',
                                            4);
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
                                        db_inputdata('DBtxt22', $DBtxt22_dia, $DBtxt22_mes, $DBtxt22_ano, true, 'text',
                                            4);
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="width: 170px;">
                                        <label class="bold" id="lbl_sistema_contas" for="sistema_contas">Sistema de
                                            Contas:</label>
                                    </td>
                                    <td colspan="3">
                                        <?php
                                        if (!USE_PCASP) {
                                            $aOpcoesSistema = array(
                                                'T' => 'Todos',
                                                'F' => 'Financeiro',
                                                'C' => 'Compensado',
                                                'P' => 'Patrimonial',
                                                'O' => 'Orçamentário',
                                            );
                                        } else {
                                            $aOpcoesSistema = array(
                                                '' => 'Todos',
                                                '0' => 'Não aplicável',
                                                '1' => 'Subsistema de Informações Orçamentárias',
                                                '2' => 'Subsistema de informações Patrimoniais',
                                                '3' => 'Subsistema de Custos',
                                                '4' => 'Subsistema de Compensação',
                                            );
                                        }
                                        $sistema_contas = 'T';
                                        db_select("sistema_contas", $aOpcoesSistema, true, 2, 'style="width: 100%;"')
                                        ?>
                                    </td>
                                </tr>

                                <?php if (USE_PCASP) : ?>
                                    <tr>
                                        <td>
                                            <label class="bold" id="lbl_indicador" for="indicador_superavit">Indicador
                                                de Superávit:</label>
                                        </td>
                                        <td colspan="3">
                                            <?php
                                            $aIndicadores = array(
                                                '' => 'Todos',
                                                'N' => 'N - Não se aplica',
                                                'F' => 'F - Financeiro',
                                                'P' => 'P - Permanente',
                                            );
                                            db_select('indicador_superavit', $aIndicadores, true, 1,
                                                'style="width: 50%;"');
                                            ?>
                                        </td>
                                    </tr>
                                <?php endif ?>

                                <tr>
                                    <td>
                                        <label class="bold" id="lbl_encerramento" for="encerramento">Encerramento de
                                            Exercício:</label>
                                    </td>
                                    <td colspan="3">
                                        <?php
                                        $aOpcoesEncerramento = array(
                                            'n' => 'Não',
                                            's' => 'Sim',
                                        );
                                        db_select('encerramento', $aOpcoesEncerramento, true, 1, 'style="width: 50%;"');
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <label class="bold" id="lbl_estruturais"
                                               for="estrut_inicial">Estruturais:</label>
                                    </td>
                                    <td colspan="3">
                                        <?php
                                        $Testrut_inicial = 'Informe os estruturais separados por vírgula';
                                        db_input('estrut_inicial', '', '', false, '', '', 'style="width: 100%;"')
                                        ?>
                                    </td>
                                </tr>
                                <tr>

                                    <td>
                                        <b><label for="contacorrente">Conta Corrente:</label></b>
                                    </td>
                                    <td>
                                        <?php
                                        db_select("contacorrente", $contascorrentes, true, 1);
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <fieldset class="separator campos_consulta"
                                                  id="mostrarColunasContaCorrente"
                                                  >
                                            <legend>Opções de Visualização</legend>
                                            <div style="text-align: left"><b>Selecione quais atributos
                                                    deseja
                                                    visualizar:</b>
                                            </div>
                                            <table id="atributosColunas">
                                            </table>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>

                                    <td colspan="4">
                                        <div id='ctnLancadorAtributos'
                                             style="margin-top: 10px; width: 600px">
                                            <div>
                                                <fieldset class="separator campos_consulta"
                                                          id="mostrarAtributos">
                                                    <legend>Atributos Selecionados</legend>
                                                    <div id="gridAtributos">
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <div id="lista-instituicoes" style="width: 100%">&nbsp;</div>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>

                    </fieldset>

                    <input style="margin-top: 20px;" name="emite" id="emite" type="button" value="Processar"
                           onclick="js_emite();">

                </td>
            </tr>

        </table>
        <input type="hidden" name="use_pcasp" value="<?php echo USE_PCASP ? '1' : '0'; ?>">
    </form>

</div> <!-- container -->

<script>
    const URL_RELATORIO = "con2_balanceteverificacaocontacorrente002.php";
    var USE_PCASP;
    var oViewInstituicao;
    var oGridRecursos;
    var rpc = "cons2_consultacontacorrente.RPC.php";
    document.observe('dom:loaded', function () {


        oViewInstituicao = new DBViewInstituicao('oViewInstituicao', $('lista-instituicoes'));
        oViewInstituicao.show();

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

        var filtros = new Object();
        filtroAtributos = filtroAtributo();
        colunas = colunaAtributos();

        filtros.conta_corrente = $F('contacorrente');
        filtros.atributos = filtroAtributos;
        filtros.colunas = colunas;

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


        var sQuery = '?';
        sQuery += 'encerramento=' + oForm.encerramento.value;
        sQuery += '&sistema_contas=' + oForm.sistema_contas.value;
        sQuery += '&estrut_inicial=' + oForm.estrut_inicial.value;
        sQuery += '&db_selinstit=' + sInstituicoes;
        sQuery += '&perini=' + sPeriodoInicial;
        sQuery += '&perfin=' + sPeriodoFinal;
        sQuery += '&filtros_conta_corrente=' + JSON.stringify(filtros);

        if (USE_PCASP == '1') {
            sQuery += '&indicador_superavit=' + oForm.indicador_superavit.value;
        }

        var iHeight = (screen.availHeight - 40);
        var iWidth = (screen.availWidth - 5);
        var sOpcoes = 'width=' + iWidth + ',height=' + iHeight + ',scrollbars=1,location=0';
        var oJanela = window.open(URL_RELATORIO + sQuery, '', sOpcoes);

        oJanela.moveTo(0, 0);
    }

    function getAtributosDoContaCorrente(colunas, valorPadraoAtributos) {

        if ($F('contacorrente') == '0') {

            gridContaCorrenteAtributos.clearAll(true);
            $('atributosColunas').innerHTML = '';
            return;

        }
        var request = {
            exec: "getAtributos",
            conta_corrente: $F('contacorrente')
        };
        new AjaxRequest(rpc, request, function (response, erro) {

            if (erro) {
                return;
            }

            colunas = colunas || [];
            valorPadraoAtributos = valorPadraoAtributos || [];

            gridContaCorrenteAtributos.clearAll(true);
            $('atributosColunas').innerHTML = '';
            for (var dados of response.atributos) {
                var valorAtributo = '';
                for (var valorInformado of valorPadraoAtributos) {
                    if (valorInformado.sigla == dados.sigla) {
                        valorAtributo = valorInformado.valor;
                        break;
                    }
                }

                var linha = [
                    dados.sigla,
                    dados.descricao,
                    "<input type='text' value= '" + valorAtributo + "' style='width: 100%;' id='valor" + dados.sigla + "'>"
                ];
                gridContaCorrenteAtributos.addRow(linha);
            }
            gridContaCorrenteAtributos.renderRows();
            /* adiciona o hint nas linhas */
            response.atributos.each(
                function (atributo, linha) {

                    gridContaCorrenteAtributos.setHint(linha, 1, atributo.ajuda);
                }
            );

            /**
             * Mostra as colunas do relatorio
             */
            var itensImpressos = 0;
            for (var atributo of response.atributos) {

                if (itensImpressos == 0) {
                    var linha = document.createElement("tr");
                    $('atributosColunas').appendChild(linha);
                }
                var checked = ' checked ';
                if (!empty(colunas)) {
                    checked = colunas.in_array(atributo.sigla) ? ' checked ' : '';
                }
                var coluna = document.createElement("td");
                coluna.style.whiteSpace = "nowrap";
                coluna.noWrap = true;
                var checkbox = document.createElement("input");
                checkbox.id = 'atributo_coluna_' + atributo.sigla;
                checkbox.type = 'checkbox';
                checkbox.checked = checked;
                checkbox.className = 'coluna_atributo';
                checkbox.value = atributo.sigla;
                var label = document.createElement("label");
                label.htmlFor = checkbox.id;

                label.innerHTML = atributo.sigla;
                coluna.appendChild(checkbox);
                coluna.appendChild(label);
                linha.appendChild(coluna);
                itensImpressos++;
                if (itensImpressos > 4) {
                    itensImpressos = 0;
                }
            }
        }).setMessage('Aguarde, pesquisando atributos.').execute();
    }

    $('contacorrente').observe('change', function () {
        getAtributosDoContaCorrente();
    });

    var gridContaCorrenteAtributos = new DBGrid('gridAtributos');
    gridContaCorrenteAtributos.nameInstance = 'gridContaCorrenteAtributos';
    var headers = ["Sigla", "Descrição", "valor"];

    gridContaCorrenteAtributos.setCellWidth(["20%", "60%", "20%"]);
    gridContaCorrenteAtributos.setHeader(headers);
    gridContaCorrenteAtributos.setHeight(100);
    gridContaCorrenteAtributos.show($('gridAtributos'));

    function filtroAtributo() {
        var retorno = [];
        var linhas = gridContaCorrenteAtributos.aRows;
        if (linhas.length > 0) {
            for (var elemento of linhas) {

                var sigla = elemento.aCells[0].getValue()
                var valor = elemento.aCells[2].getValue();
                if (valor != '') {
                    retorno.push({atributo: sigla, valor: valor});
                }
            }
        }
        return retorno;
    }

    function colunaAtributos()
    {
        var colunas = [];
        var listaCheckboxes = $$('.coluna_atributo');
        for (checkbox of listaCheckboxes) {

            if (!checkbox.checked) {
                continue;
            }
            colunas.push(checkbox.value);
        }
        return colunas;
    }
</script>

<?php db_menu() ?>
</body>
</html>
