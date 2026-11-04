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

$oDaoMatricula = new cl_matricula();
$oDaoCalendario = new cl_calendario();
$db_opcao = 1;
$db_botao = true;
$sNomeEscola = db_getsession("DB_nomedepto");
$iEscola = db_getsession("DB_coddepto");
$iModulo = db_getsession("DB_modulo");
?>
<!DOCTYPE html>
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
        <fieldset>
            <legend>Quadro de Vagas Geral da Rede por Bairro</legend>
            <table class="form-container">
                <tr>
                    <td style="width: 150px">
                        <span style=" margin-top: -90px; position: absolute">Selecione o(s) Bairro(s):</span>
                    </td>
                    <td>
                        <select name="bairro" id="select_bairro" multiple
                                style="font-size:9px; width:200px; height:180px;"
                                onchange="js_calendario();">
                        </select>
                    </td>
                    <td>
                        <div class="alert alert-primary text-left" role="alert"
                             style="padding: 10px; margin-left: 5px; font-size: 12px">
                            Para selecionar mais de um Bairro,<br>mantenha pressionada a tecla <kbd>CTRL</kbd><br>e
                            clique sobre os nomes.<br>Caso queira selecionar todos, clique na<br>primeira opção,
                            pressione <kbd>SHIFT</kbd> e<br>clique sobre a última opção da listagem.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Calendários:</b><br>
                    </td>
                    <td>
                        <select name="calendario" id="select_calendario" multiple
                                style="font-size:9px;width:200px;height:180px;"
                                onchange="js_etapa();">
                        </select>
                    </td>
                    <td>
                        <div class="alert alert-primary text-left" role="alert"
                             style="padding: 10px; margin-left: 5px; font-size: 12px">
                            Para selecionar mais de um Calendário,<br>mantenha pressionada a tecla <kbd>CTRL</kbd><br>e
                            clique sobre os nomes.<br>Caso queira selecionar todos, clique na<br>primeira opção,
                            pressione <kbd>SHIFT</kbd> e<br>clique sobre a última opção da listagem.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Etapas:</b><br>
                    </td>
                    <td>
                        <select name="etapa" id="select_etapa" multiple
                                style="font-size:9px;width:200px;height:180px;">
                        </select>
                    </td>
                    <td>
                        <div class="alert alert-primary text-left" role="alert"
                             style="padding: 10px; margin-left: 5px; font-size: 12px">
                            Para selecionar mais de uma Etapa,<br>mantenha pressionada a tecla <kbd>CTRL</kbd><br>e
                            clique sobre os nomes.<br>Caso queira selecionar todos, clique na<br>primeira opção,
                            pressione <kbd>SHIFT</kbd> e<br>clique sobre a última opção da listagem.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button type="button" value="Processar" id="procurar" name="procurar"
                                onclick="js_codigoCalendario()"
                                style="float: right; margin-top: 7px" disabled>
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
</body>
<script type="text/javascript">
    js_init();

    function js_init() {
        js_bairro();
    }

    function getValoresSelect(select) {
        let resultado = [];
        let tagOptions = select && select.options;
        let opt;

        for (let i = 1, iLen = tagOptions.length; i < iLen; i++) {
            opt = tagOptions[i];

            if (opt.selected) {
                resultado.push(opt.value || opt.text);
            }
        }

        return resultado;
    }

    function js_procurar(response) {
        let bairros = $F('select_bairro');
        let calendarios = response.aResult.map(Object => Object.ed52_i_codigo);
        let etapas = $F('select_etapa');

        let jan = window.open(
            `edu2_quadrodevagasbairro002.php?&iBairros=${bairros}&iCalendario=${calendarios}&iEtapas=${etapas}`,
            '',
            'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0'
        );

        jan.moveTo(0, 0);
    }

    function js_bairro() {
        let oParam = new Object();
        oParam.exec = "getBairrosQuadroVagas";
        let url = 'edu4_escola.RPC.php';
        js_webajax(oParam, 'js_retornoPesquisaBairro', url);
    }

    function js_limpaBairro() {
        $('select_bairro').innerHTML = '';
        $('select_bairro').add(new Option('Selecione o Bairro', '999999'));
        document.form1.select_bairro[0].selected = true;
    }

    function js_retornoPesquisaBairro(oRetorno) {
        let response = JSON.parse(oRetorno.responseText);

        if (response.iStatus !== 1) {
            alert(response.sMessage.urlDecode());
            js_limpaBairro();

            return false;
        }

        js_limpaBairro();

        response.aResult.map((bairro) => {
            $('select_bairro').add(new Option(bairro.j13_descr.urlDecode(), bairro.ed18_i_bairro));
        });
    }

    function js_calendario() {
        let oParam = new Object();
        oParam.exec = "getCalendariosBairrosQuadroVagas";
        oParam.bairros = $F('select_bairro');
        let url = 'edu4_escola.RPC.php';
        js_webajax(oParam, 'js_retornoPesquisaCalendarioBairro', url);
    }

    function js_retornoPesquisaCalendarioBairro(oRetorno) {
        let response = JSON.parse(oRetorno.responseText);
        $('select_etapa').innerHTML = '<option value="999999">Selecione a Etapa</option>';
        sHtml = '';
        if (response.iStatus != 1) {
            sHtml += '<option value="999999">Selecione o Calendário</option>';
            alert(response.sMessage.urlDecode());
            $('select_calendario').innerHTML = sHtml;
            document.form1.select_calendario[0].selected = true;
            return false;
        } else {
            sHtml += '<option value="999999">Selecione o Calendário</option>';
            for (var i = 0; i < response.aResult.length; i++) {
                with (response.aResult[i]) {
                    sHtml += '<option value="' + response.aResult[i].ed52_c_descr + '">';
                    sHtml += response.aResult[i].ed52_c_descr.urlDecode() + '</option>';
                }
            }
            $('select_calendario').innerHTML = sHtml;
            document.form1.select_calendario[0].selected = true;
        }
        $('select_calendario').disabled = false;
        $('procurar').disabled = false;
    }

    function js_etapa() {
        let oParam = new Object();
        oParam.exec = "getEtapasBairrosQuadroVagas";
        oParam.bairros = $F('select_bairro');
        oParam.calendarios = encodeURI($F('select_calendario'));
        let url = 'edu4_escola.RPC.php';
        js_webajax(oParam, 'js_retornoPesquisaEtapaBairro', url);
    }

    function js_retornoPesquisaEtapaBairro(oRetorno) {
        let response = JSON.parse(oRetorno.responseText);
        sHtml = '';
        if (response.iStatus != 1) {
            sHtml += '<option value="999999">Selecione a Etapa</option>';
            alert(response.sMessage.urlDecode());
            $('select_etapa').innerHTML = sHtml;
            document.form1.select_etapa[0].selected = true;
            return false;
        } else {
            sHtml += '<option value="999999">Selecione a Etapa</option>';
            for (var i = 0; i < response.aResult.length; i++) {
                with (response.aResult[i]) {
                    sHtml += '<option value="' + response.aResult[i].ed11_i_codigo + '">';
                    sHtml += response.aResult[i].ed11_c_descr.urlDecode() + '</option>';
                }
            }
            $('select_etapa').innerHTML = sHtml;
            document.form1.select_etapa[0].selected = true;
        }
        $('select_etapa').disabled = false;
        $('procurar').disabled = false;
    }

    function js_codigoCalendario() {
        let oParam = new Object();
        oParam.exec = "getCodigoCalendario";
        oParam.calendarios = $F('select_calendario');
        let url = 'edu4_escola.RPC.php';
        js_webajax(oParam, 'js_retornoCodigoCalendario', url);
    }

    function js_retornoCodigoCalendario(oRetorno) {
        let response = JSON.parse(oRetorno.responseText);
        js_procurar(response);
    }

</script>
<?php if ($iModulo != 7159) { ?>
    <script>
        js_escola(<?=$iEscola?>);
    </script>
<?php } ?>
</html>

