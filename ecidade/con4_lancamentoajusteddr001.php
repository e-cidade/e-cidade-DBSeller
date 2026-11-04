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
        <legend>Lançamento(s) por fonte de Recursos</legend>

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
            <tr>
                <td id="ctnInstituicao" colspan="2" style="font-weight: normal">
                    <input type="hidden" name="db_selinstit" id="db_selinstit" value="">
                </td>
            </tr>
        </table>
    </fieldset>
    <br/>
    <button id="btnCarregar" type="button">
        <i class="fas fa-search"></i>
        Carregar Dados
    </button>
</div>
<div class="container">
    <div id="div_grid_conferenciarecurso" style="width: 800px; margin-bottom:10px;"></div>
    <button id="btnProcessar" type="button" disabled>
        <i class="fas fa-save"></i>
        Processar
    </button>
</div>
</body>
<?php db_menu() ?>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/datagrid.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
<script src="scripts/widgets/dbtextField.widget.js" type="text/javascript"></script>
<script>
    const collectionRec = new Collection().setId('o15_codigo');
    gridRecursos = new DatagridCollection(collectionRec).configure({
        order: false,
        height: 300
    });

    gridRecursos.addColumn('gestao', {label: 'Gestao', 'width': '15%', 'align': 'center'});
    gridRecursos.addColumn('subrecurso', {label: 'SubRecurso', 'width': '15%', 'align': 'center'});
    gridRecursos.addColumn('complemento', {label: 'Complemento', 'width': '15%', 'align': 'center'});
    gridRecursos.addColumn('descricao', {label: 'Descricao','width': '35%', });
    gridRecursos.addColumn('diferenca', {'label': 'Diferença', 'width': '20%', 'align': 'right'}).transform('dinheiro');
    gridRecursos.show($('div_grid_conferenciarecurso'));

    const routs = {
        processar: "financeiro/contabilidade/obter-dados-conferencia-por-recurso"
    };

    const rpcFile = 'con4_lancamentoajusteddr.RPC.php';

    const dataInicial = $("txtDataInicial");
    const dataFinal = $("txtDataFinal");

    PHPSession.loadData().then(() => {
        btnCarregar.addEventListener('click', () => {
            const formData = new FormData();

            formData.append('dataInicial', js_formatar(dataInicial.value, 'd'));
            formData.append('dataFinal', js_formatar(dataFinal.value, 'd'));

            if (dataInicial.value == "" || dataFinal.value == "") {

                alert("Selecione um Intervalo de Datas.");
                return false;
            }

            gridRecursos.clear();

            formData.append('instituicoes[]', PHPSession.getValueSession("DB_instit"));

            PHPSession.appendFormData(formData);
            HttpClient.post(`${PHPSession.requestApi}/${routs.processar}`, {body: formData}).then(response => {
                if (response.data.error) {
                    alert(response.data.message);
                    return;
                }

                collectionRec.add(response.data);
                gridRecursos.reload();
                document.querySelector('#btnProcessar').disabled = false;
            });
        });
    });

    btnProcessar.addEventListener('click', () => {
        if (!confirm('Deseja realmente fazer o(s) lançamento(s) ?')) {
            return false;
        }

        let linhasProcessar = collectionRec.build()


        const parametros = new FormData();
        parametros.append('exec', 'salvarLancamentos');

        for (let linha of linhasProcessar){
            parametros.append('recursos[]', JSON.stringify(linha));
        }
        PHPSession.appendFormData(parametros);

        HttpClient.post(rpcFile, {body: parametros}).then((response) => {

            if (response.erro) {
                if (response.mensagem) {
                    alert(response.mensagem.urlDecode());
                }
                return false;
            }

            alert(response.mensagem);
            btnCarregar.click();
            collectionRec.clear()
            document.querySelector('#btnProcessar').disabled = true;

        });

        return false;
    });

    document.querySelector('#btnProcessar').disabled = true;

</script>
