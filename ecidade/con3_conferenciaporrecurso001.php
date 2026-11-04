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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification('libs/db_conecta.php');
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>
<body>

<div class="container">
    <fieldset>
        <legend>Conferência por Recurso</legend>

        <table class="form-container">
            <tr class="text-left">
                <td><label class="bold" for="dataInicial">Data Inicial:</label></td>
                <td>
                    <?php db_inputdata("txtDataInicial", null, null, null, true, null, 1) ?>
                </td>
            </tr>
            <tr class="text-left">
                <td><label class="bold" for="dataFinal">Data Final:</label></td>
                <td>
                    <?php db_inputdata("txtDataFinal", null, null, null, true, null, 1) ?>
                </td>
            </tr>
            <tr class="text-left">
                <td><label class="bold" for="modeloEmissao">Template Emissão:</label></td>
                <td>
                    <select id="modeloEmissao" >
                        <option value="1" selected>Utiliza Subrecurso</option>
                        <option value="2">Siconfi</option>
                        <option value="3">Siconfi + CO</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td id="ctnInstituicao" colspan="2" style="font-weight: normal">
                    <input type="hidden" name="db_selinstit" id="db_selinstit" value="">
                </td>
            </tr>
        </table>
    </fieldset>
    <button id="btnEmitir" type="button">
        <i class="fas fa-print"></i> Emitir
    </button>

</div>
</body>
<?php db_menu() ?>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/datagrid.widget.js"></script>
<script rel="script" type="text/javascript" src="scripts/widgets/DBViewInstituicao.widget.js"></script>
<script rel="script" type="text/javascript" src="scripts/classes/planejamento/planejamento.js"></script>
<script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<script>

    const sApiUrl = "<?= ECIDADE_REQUEST_PATH ?>v4/api/financeiro/contabilidade/";
    const routs = {
        processar: sApiUrl + "relatorio-conferencia-por-recurso"
    };

    const dataInicial = $("txtDataInicial");//new Planejamento(document.getElementById('txtDataInicial'));
    const dataFinal = $("txtDataFinal");//new Planejamento(document.getElementById('txtDataInicial'));

    var viewInstituicao = new DBViewInstituicao('viewInstituicao', $('ctnInstituicao'));
    viewInstituicao.show();

    btnEmitir.addEventListener('click', () => {
        const formData = new FormData();

        formData.append('dataInicial', js_formatar(dataInicial.value, 'd'));
        formData.append('dataFinal', js_formatar(dataFinal.value, 'd'));
        formData.append('modeloEmissao', $('modeloEmissao').value);

        if (dataInicial.value == "" || dataFinal.value == "") {

            alert("Selecione um Intervalo de Datas.");
            return false;
        }

        for (let codigo of viewInstituicao.getInstituicoesSelecionadas(true)) {
            formData.append('instituicoes[]', codigo);
        }

        PHPSession.appendFormData(formData);
        HttpClient.post(`${routs.processar}`, {body: formData}).then(response => {

            if (response.error) {
                alert(response.message);
                return;
            }

            const download = new DBDownload();
            download.addFile(response.data.arquivos.pdf, "Conferência por Recursos - PDF");
            download.addFile(response.data.arquivos.csv, "Conferência por Recursos - CSV");
            download.show();

        });
    });
</script>
