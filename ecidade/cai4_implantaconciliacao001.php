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
require_once(modification("classes/db_saltes_classe.php"));
require_once(modification("classes/db_corrente_classe.php"));


$clsaltes = new cl_saltes;
$clcorrente = new cl_corrente;
db_postmemory($HTTP_POST_VARS);
db_postmemory($HTTP_GET_VARS);
$db_opcao = 1;
$db_botao = true;
$borda = 0;

?>

<!doctype html>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Expires" CONTENT="0">
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
</head>
<form id="formulario" method="post">
    <div class="subcontainer" style="width: 60%;">

        <fieldset>
            <legend> Implantação de Conciliação por Lote</legend>

            <table id="data-table"
                   class="table table-sm"
                   data-height="650"
                   data-virtual-scroll="true"
                   data-show-columns="true"
            >
            </table>

        </fieldset>
        <button type="button" id="btnProcessar" name="btnProcessar" class="btn btn-light">
            <i class="far fa-save"></i>
            Processar
        </button>
    </div>
</form>

<?php
db_menu();
?>
</body>
</html>


<script type="text/javascript" src="scripts/classes/http/http.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/widgets/DBFileUpload.widget.js"></script>
<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<!-- requires bootstrap table -->
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>


<script>

    const routs = {
        pendencias: "financeiro/tesouraria/contas-pendentes",
        processar: "financeiro/tesouraria/processar-implantacao"
    };

    const formData = new FormData();

    $.noConflict();
    jQuery(document).ready(function ($) {

        const estiloLinha = (data) => {

            if (data.dataImplantar.length <= 0) {
                return {
                    classes: 'form-error'
                };
            }
            return {};
        }


        const formatterSelectData = (v, data) => {

            let oCboDatas = document.createElement('select');
            oCboDatas.setAttribute('name', 'data_' + data.conta);
            oCboDatas.id = "data_" + data.conta;

            if (data.dataImplantar.length <= 0) {
                oCboDatas.add(new Option("Indisponível", ""));
            }

            data.dataImplantar.map((oData) => {
                oCboDatas.add(new Option(oData, oData));
            });

            return oCboDatas.outerHTML;
        }

        const colunas = [

            {
                field: 'checkbox',
                checkbox: true,
                align: 'center',
                valign: 'middle',
                width: '25px;'
            },
            {
                title: 'Conta',
                field: 'conta',
                halign: 'center',
                align: 'right',
                width: '70px;'
            },
            {
                title: 'Reduzidos',
                field: 'reduzido',
                halign: 'center',
                align: 'right',
                width: '70px;'
            },
            {
                title: 'Descricao',
                field: 'descricao',
                halign: 'center',
                align: 'left',
                width: '400px;'
            },
            {
                title: 'Datas',
                field: 'data',
                halign: 'center',
                align: 'center',
                formatter: formatterSelectData,
                width: '80px;'
            }
        ];


        var table = $('#data-table');
        table.bootstrapTable({
            columns: colunas,
            uniqueId: "id",
            locale: 'pt-BR',
            cache: false,
            //pagination: true,
            pageSize: 15,
            pageList: [10, 25, 50, 100, 200, 'All'],
            search: true,
            class: "table table-sm",
            // rowStyle: estiloLinha
        });
        // table.bootstrapTable('removeAll');


        buscaRegistros = () => {

            PHPSession.appendFormData(formData);
            HttpClient.post(`${PHPSession.requestApi}/${routs.pendencias}`, {body: formData}).then(response => {
                table.bootstrapTable('load', response.data);
            });
        }

        btnProcessar.addEventListener('click', () => {

            let aContas = [];
            table.bootstrapTable('getSelections').each(selecao => {

                data = document.getElementById("data_" + selecao.conta).value;

                if (data == "") {
                    alert("Deve ser selecionado uma data para a conta: " + selecao.conta + " " + selecao.descricao);
                    return false;
                }

                let oConta = {};
                oConta.conta = selecao.conta;
                oConta.data = data;
                aContas.push(oConta);
            });

            if (aContas.length <= 0) {

                alert("Selecionar uma Conta Para Implantacao");
                return false;
            }

            let confirmacao = "Deseja Implantar as Contas Selecionadas ?";
            if (!confirm(confirmacao)) {
                return false;
            }

            formData.append('linhasContas', JSON.stringify(aContas));
            PHPSession.appendFormData(formData);
            HttpClient.post(`${PHPSession.requestApi}/${routs.processar}`, {body: formData}).then(response => {

                alert(response.message);
                if (response.error) {
                    return;
                }
                buscaRegistros();
            });
        });

        PHPSession.loadData().then(() => {
            buscaRegistros();
        });
    });

</script>
