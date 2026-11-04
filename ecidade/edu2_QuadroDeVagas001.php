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

require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));

db_postmemory($HTTP_POST_VARS);

$db_opcao = 1;
$db_botao = true;
$sNomeEscola = db_getsession("DB_nomedepto");
$iEscola = db_getsession("DB_coddepto");
$iModulo = db_getsession("DB_modulo");

$oDaoEscola = new cl_escola();
$sSqlEscola = $oDaoEscola->sql_query_file(
    "",
    "ed18_i_codigo, ed18_c_nome",
    "ed18_i_codigo",
    ""
);

$rsResultEscola = $oDaoEscola->sql_record($sSqlEscola);
$iLinhas = $oDaoEscola->numrows;

$oDaoCalendario = new cl_calendarioescola();
$sSqlCalendario = $oDaoCalendario->sql_query(null, "distinct ed52_i_ano", "ed52_i_ano desc", null);
$rsResultCalendario = $oDaoCalendario->sql_record($sSqlCalendario);
$iCalendarioLinhas = $oDaoCalendario->numrows;
?>

<!doctype html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/webseller.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
<div class="container">
    <?php MsgAviso(db_getsession("DB_coddepto"), "escola"); ?>
    <a name="topo"></a>
    <form name="form1" method="post" action="">
        <fieldset style="padding-right: 16px">
            <legend>Quadro de Vagas Geral da Rede</legend>
            <table class="form-container">
                <tr>
                    <td>
                        <label for="ano">
                            Selecione o ano do calendário:
                        </label>
                    </td>
                    <td>
                        <select name="ano" id="ano"class="field-size6" style="margin-left: 10px;"
                            onChange="js_ano(this.value);">
                            <option value=""> Selecione o ano</option>
                            <?php for ($iCont = 0; $iCont < $iCalendarioLinhas; $iCont++) {
                                $oDadosCalendario = db_utils::fieldsMemory($rsResultCalendario, $iCont); ?>
                                <option value="<?= $oDadosCalendario->ed52_i_ano ?>">
                                    <?= "$oDadosCalendario->ed52_i_ano" ?>
                                </option>";
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="escola">
                            Selecione a escola:
                        </label>
                    </td>
                    <td>
                        <select name="escola" id="escola" class="field-size6" style="margin-left: 10px;"
                                onChange="js_escola(this.value);">
                            <option value=""> Selecione a escola</option>
                            <option value="0"> Todas</option>
                            <?php for ($iCont = 0; $iCont < $iLinhas; $iCont++) {
                                $oDadosEscola = db_utils::fieldsmemory($rsResultEscola, $iCont); ?>
                                <option value="<?= $oDadosEscola->ed18_i_codigo ?>">
                                    <?= "$oDadosEscola->ed18_i_codigo - $oDadosEscola->ed18_c_nome" ?>
                                </option>";
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="tipoRelatorio">
                            Selecione o tipo do relatório:
                        </label>
                    </td>
                    <td>
                        <select name="tipoRelatorio" id="tipoRelatorio" class="field-size6" disabled
                                style="margin-left: 10px">
                            <option value="1" selected> Agrupar por escola</option>
                            <option value="2"> Agrupar por etapa</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="select_calendario">
                            Selecione o Calendário:
                        </label>
                    </td>
                    <td>
                        <select name="calendario" id="select_calendario" multiple
                                style="font-size:9px; width:200px; height:180px; float: left; margin-left: 10px;"
                                onchange="js_calendario(this.value)">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button type="button" value="salvar" id="procurar" name="procurar"
                                onclick="js_procurar(document.form1.calendario)"
                                style="float: right;" disabled>
                            <i class="far fa-file-pdf"></i>
                            Processar
                        </button>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <?php
    db_menu(
        db_getsession("DB_id_usuario"),
        db_getsession("DB_modulo"),
        db_getsession("DB_anousu"),
        db_getsession("DB_instit")
    );
    ?>
</div>

<script type="text/javascript">
    const cboCalendario = document.getElementById('select_calendario');
    const cboTipoRelatorio = document.getElementById('tipoRelatorio');
    const cboEscolaSelecionada = document.getElementById('escola');
    var ano = '';

    function getValoresSelect(select) {
        let resultado = [];
        let tagOptions = select && select.options;
        let opt;

        for (let i = 1; i < tagOptions.length; i++) {
            opt = tagOptions[i];

            if (opt.selected) {
                resultado.push(opt.value || opt.text);
            }
        }

        return resultado;
    }

    function js_procurar(calendario) {
        calendario = getValoresSelect(calendario);

        if (calendario[0] !== undefined) {
            let jan;
            let tipoRelatorio = cboTipoRelatorio.value;
            jan = window.open(
                `edu2_QuadroDeVagas002.php?x&iCalendario=${calendario}&iEscola=${$('escola').value}` +
                `&iTipoRelatorio=${tipoRelatorio}`,
                '',
                `width=${screen.availWidth - 5}, height=${screen.availHeight - 40}, scrollbars=1, location=0`
            );

            jan.moveTo(0, 0);
        }
    }

    function js_ano(anoSelecionado) {
        ano = anoSelecionado;
        cboEscolaSelecionada.options[0].selected = true;
        js_limparCalendario();
    }

    function js_escola(escola) {
        let oParam = {};
        oParam.exec = "PesquisaCalendarioEspecialQuadroDeVagas";
        oParam.escola = escola;
        oParam.ano = ano;

        $('tipoRelatorio').value = 1;
        $('tipoRelatorio').disabled = true;

        if (escola === '') {
            $('select_calendario').innerHTML = '';
            $('procurar').disabled = true;

            return
        }

        if (escola === '0') {
            $('tipoRelatorio').disabled = false;
        }

        let url = 'edu4_escola.RPC.php';

        js_webajax(oParam, 'js_retornoPesquisaCalendario', url);

    }

    function js_retornoPesquisaCalendario(oRetorno) {
        let response = JSON.parse(oRetorno.responseText);

        if (response.iStatus !== 1) {
            alert(response.sMessage.urlDecode());
            js_limparCalendario();

            return false;
        }

        js_limparCalendario()

        response.aResult.map((calendario) => {
            cboCalendario.add(new Option(calendario.ed52_c_descr.urlDecode(), calendario.ed52_c_descr));
        });
    }

    function js_calendario(valor) {
        $('procurar').disabled = valor === '';
    }

    function js_limparCalendario() {
        cboCalendario.innerHTML = '';
        cboCalendario.add(new Option("Selecione o Calendário", ""));
        cboCalendario.options[0].selected = true;
    }
</script>
</body>
</html>
