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
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
db_postmemory($_POST);

$oRotuloCampo = new rotulocampo();
$oRotuloCampo->label("ed52_d_inicio");
$oRotuloCampo->label("ed52_d_fim");

$oDaoCalendarioEscola = new cl_calendarioescola;
$db_opcao = 1;
$iEscola = db_getsession("DB_coddepto");

if (empty($ed52_i_ano)) {
    $ed52_i_ano = date("Y");
}

$censo = new ECidade\Educacao\Escola\Censo\Censo($ed52_i_ano);
$dataCenso = $censo->getDataCenso();
$dataCensoBaseCalculada = $censo->getDataCenso()->getDate(DBDate::DATA_PTBR);

if (!is_null($data_censo) && $data_censo != $dataCensoBaseCalculada) {
    $dataPartes = explode("/", $data_censo);
    $data_censo_dia = $dataPartes[0];
    $data_censo_mes = $dataPartes[1];
    $data_censo_ano = $dataPartes[2];
} else {
    $data_censo_dia = $dataCenso->getDia();
    $data_censo_mes = $dataCenso->getMes();
    $data_censo_ano = $dataCenso->getAno();
}

$sEscolaOrder = " ed52_d_inicio asc, ed52_d_fim desc ";
$sCampos = " ed52_d_inicio , ed52_d_fim ";
$sWhere = " ed52_i_ano = $ed52_i_ano AND ed38_i_escola = $iEscola ";

$sSqlAnoCenso = $oDaoCalendarioEscola->sql_query("", $sCampos, $sEscolaOrder, "$sWhere");
$rsAnoCenso = $oDaoCalendarioEscola->sql_record($sSqlAnoCenso);
$oDadosInicioFim = db_utils::fieldsmemory($rsAnoCenso, 0);

$bVerif = false;

$ed52_d_inicio = db_formatar($oDadosInicioFim->ed52_d_inicio, 'd');

$aDataIni = explode('/', $ed52_d_inicio);
$ed52_d_inicio_dia = $aDataIni[0];
$ed52_d_inicio_mes = $aDataIni[1];
$ed52_d_inicio_ano = $aDataIni[2];

$ed52_d_fim = db_formatar($oDadosInicioFim->ed52_d_fim, 'd');
$aDataFim = explode('/', $ed52_d_fim);
$ed52_d_fim_dia = $aDataFim[0];
$ed52_d_fim_mes = $aDataFim[1];
$ed52_d_fim_ano = $aDataFim[2];

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <script type="text/javascript" src="scripts/arrays.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        .interno {
            border: 0px;
            border-top: 2px groove white;
        }

        div.formulario table tr td:FIRST-CHILD {
            width: 100px;
        }
    </style>
</head>
<body bgcolor="#CCCCCC" style='margin-top: 25px'>
<?php MsgAviso(db_getsession("DB_coddepto"), "escola"); ?>
<form name="form1" method="post" action="" align="center">
    <div class='container'>
        <fieldset>
            <legend>Gerar Arquivo de Identificação</legend>
            <fieldset class="separator">
                <legend>Dados do Censo</legend>
                <table class="form-container">
                    <tr>
                        <td class="field-size3">Data do Censo:</td>
                        <td>
                            <?php
                            db_inputdata('data_censo', $data_censo_dia, $data_censo_mes, $data_censo_ano, true, 'text', 1, "onchange=\"js_ano();\"", "", "", "parent.js_ano();")
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Ano do Censo:</td>
                        <td><?php db_input("ed52_i_ano", 10, 1, true, 'text'); ?></td>
                    </tr>
                </table>
            </fieldset>
            <fieldset class='interno'>
                <legend><b>Calendário</b></legend>
                <table class="form-container">
                    <tr>
                        <td class="field-size3"><?= $Led52_d_inicio ?></td>
                        <td>
                            <?php db_inputdata(
                                'ed52_d_inicio',
                                $ed52_d_inicio_dia,
                                $ed52_d_inicio_mes,
                                $ed52_d_inicio_ano,
                                true,
                                'text',
                                $db_opcao,
                                ""
                            ); ?>
                        </td>
                        <td>
                            <?= $Led52_d_fim ?>
                        </td>
                        <td>
                            <?php db_inputdata(
                                'ed52_d_fim',
                                $ed52_d_fim_dia,
                                $ed52_d_fim_mes,
                                $ed52_d_fim_ano,
                                true,
                                'text',
                                $db_opcao,
                                ""
                            ); ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset class='interno'>
                <legend><b>Outras Opções</b></legend>
                <table class="form-container">
                    <tr>
                        <td class="field-size3">
                            <b>Formato de Arquivo:</b>
                        </td>
                        <td>
                            <?php
                            $aOptions = array("1" => "TXT", "2" => "PDF");
                            db_select("formatoarquivo", $aOptions, "", 1);
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </fieldset>
        <div class="subcontainer" style='width: 500px'>
            <fieldset>
                <legend><b>Escolas</b></legend>
                <div id="ctnEscolas">
                </div>
            </fieldset>
        </div>
        <input id='gerarArquivo' name="gerararquivo" type="button" id="arquivo" value="Gerar Arquivo" >
    </div>
</form>
</body>
</html>
<?php db_menu(); ?>
<script type="text/javascript">

    sUrlRPC = 'edu4_censoalunosseminep.RPC.php';

    function js_ano() {

        datacenso = document.form1.data_censo.value;

        if (datacenso != "" && datacenso.length == 10) {

            datacenso = datacenso.split("/");
            document.form1.ed52_i_ano.value = datacenso[2];

            document.form1.submit();
        } else {

            document.form1.ed52_i_ano.value = "";
            document.form1.ed52_d_inicio.value = "";
            document.form1.ed52_d_fim.value = "";

        }

    }

    function js_valida() {

        if (   document.form1.data_censo.value == ""
            || document.form1.ed52_i_ano.value == ""
            || document.form1.ed52_d_inicio.value == ""
            || document.form1.ed52_d_fim.value == "") {

            alert("Preencha todos os  campos do formulário!");
            return false;
        }

        if (document.form1.ed52_i_ano.value < document.form1.ed52_d_inicio_ano.value
            || document.form1.ed52_i_ano.value > document.form1.ed52_d_fim_ano.value) {

            alert("Data Inicial e Final do Calendário deve conter o Ano do Censo!");
            return false;
        }

        dataini = document.form1.ed52_d_inicio_ano.value + document.form1.ed52_d_inicio_mes.value;
        dataini += document.form1.ed52_d_inicio_dia.value;
        datafim = document.form1.ed52_d_fim_ano.value + document.form1.ed52_d_fim_mes.value;
        datafim += document.form1.ed52_d_fim_dia.value;

        if (parseInt(dataini) >= parseInt(datafim)) {
            alert("Data Final do Calendário deve ser maior que a Data Inicial!");
            return false;
        }

        if (oDataGridEscola.getSelection("object").length == 0) {
            alert('Nenhuma escola selecionada.');
            return false;
        }

        return true;
    }

    $('ed52_i_ano').style.width = '100%';
    $('formatoarquivo').style.width = '100%';

    $('gerarArquivo').addEventListener('click', function() {
        if (!js_valida()) {
            return false;
        }

        if (document.form1.formatoarquivo.value == 1) {
            gerarArquivo()
        } else {
            gerarPdf();
        }
    });

    gerarPdf = () => {
        const linhas = oDataGridEscola.getSelection("object");
        const escolas = [];
        linhas.each(function (linha) {
            escolas.push(linha.aCells[0].getValue());
        });

        const filtros = {
            "ano" : $F('ed52_i_ano'),
            "escolas": escolas,
            "data_censo": $F('data_censo')
        }

        window.open('edu2_alunosdocentesseminep002.php?filtros=' + btoa(JSON.stringify(filtros)), '', 'scrollbars=1,location=0');
    };

    gerarArquivo = () => {
        const aLinhas = oDataGridEscola.getSelection("object");

        const data = new FormData();
        data.append('acao', 'gerarArquivoIdentificacao');
        data.append('ano', $F('ed52_i_ano'));
        data.append('data_censo', $F('data_censo'));

        aLinhas.each(function (linha) {
            data.append('escolas[]', linha.aCells[0].getValue());
        });
        HttpClient.post('edu4_novoCenso.RPC.php', {body: data}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            if (response.arquivo_censo != '') {
                dowloadArquivoCenso(response.arquivo_censo, "Arquivo de Exportação.");
            }
        });
    }

    dowloadArquivoCenso = (arquivo, label) => {
        var oDownload = new DBDownload();
        oDownload.addFile(arquivo, label);
        oDownload.show();
    };

    function js_pesquisaEscola() {
        var oParametro = {};
        oParametro.exec = 'getEscolas';
        oParametro.filtraModulo = true;
        oParametro.ordemAlfabetica = true;

        var oAjax = new Ajax.Request(
            sUrlRPC,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametro),
                onComplete: js_retornaPesquisaEscola
            }
        );
    }

    function js_retornaPesquisaEscola(oResponse) {

        var oRetorno = JSON.parse(oResponse.responseText);
        oDataGridEscola.clearAll(true);
        oRetorno.aDados.each(function (oLinha, iContador) {
            var aLinha = [];

            aLinha[0] = oLinha.codigo_escola;
            aLinha[1] = oLinha.nome_escola.urlDecode();
            if (oRetorno.iTotalLinhas == 1) {
                oDataGridEscola.addRow(aLinha, false, false, true);
            } else {
                oDataGridEscola.addRow(aLinha);
            }
        });

        oDataGridEscola.renderRows();
    }

    function js_gridEscola() {
        oDataGridEscola = new DBGrid("gridEscola");
        oDataGridEscola.nameInstance = 'oDataGridEscola';
        oDataGridEscola.setCheckbox(0);
        oDataGridEscola.setCellAlign(new Array("center", "left"));
        oDataGridEscola.setHeader(new Array("Código", "Nome"));
        oDataGridEscola.setCellWidth(new Array("20%", "80%"));
        oDataGridEscola.show($('ctnEscolas'));
    }

    js_gridEscola();
    js_pesquisaEscola();
</script>
