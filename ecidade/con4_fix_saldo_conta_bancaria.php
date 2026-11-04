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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("fpdf151/pdf.php"));

$modulo = (int)db_getsession("DB_modulo");
$moduloEscola = 1100747;
$display = $moduloEscola === $modulo ? 'display: none' : 'display:';
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script src="scripts/scripts.js"></script>
    <script src="scripts/strings.js"></script>
    <script src="scripts/prototype.js"></script>

    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>

        table.table-exemplo {
            border-collapse: collapse;
            background-color: #FFF;
            color: #0a0a0a;
        }

        table.table-exemplo td {
            border: 1px solid black;
            padding: 0 2px;
        }

    </style>
</head>
<body bgcolor="#cccccc">
<div class="container">
    <div class="alert alert-primary text-left " role="alert">
        <b>ANTES DE IMPORTAR A PLANILHA, CONFIRME SE O SISTEMA ESTA NA INSTITUIÇÃO E DATA CORRETA.</b>><br>
        Os lançamentos serão realizados na instituição logada e na data atual:<br>

        <div style='font-size: 10pt'>
            Importe uma planilha no formato CSV.<br>
            Padrões da planilha:<br>
            <ul style="margin: 0;">
                <li><strong>Codificação: </strong>Europa Ocidental (ISO-8859-1) ou Latin1</li>
                <li><strong>Delimitador de campo:</strong> , (vírgula)</li>
                <li><strong>Delimitador de texto:</strong> " (aspas duplas)</li>
            </ul>
            <div>
                Exemplo de como deve ser a estrutura da planilha
                <table class="table-exemplo">
                    <tr>
                        <td class="bold">Conta Crédito</td>
                        <td class="bold">Conta Débito</td>
                        <td class="bold">Histórico</td>
                        <td class="bold">Observação</td>
                        <td class="bold">Recurso Crédito</td>
                        <td class="bold">Recurso Débito</td>
                        <td class="bold">Valor</td>
                        <td class="bold">Função</td>
                        <td class="bold">Subfunção</td>
                        <td class="bold">Elemento</td>
                        <td class="bold">Ai</td>
                        <td class="bold">NR</td>
                    </tr>
                </table>
            </div>
            <div>
                <b>Elemento:</b> Deve ser uma fonte de despesa presente na tabela <b>orcelemento</b> no exercício do lançamento. Deve conter os 13 digitos.<br />
                <b>NR:</b> Deve ser uma fonte de receita presente na tabela <b>orcfontes</b> no exercício do lançamento. Deve conter os 15 digitos.
            </div>
        </div>
    </div>
    <form id="form-upload" method="post" action="" enctype="multipart/form-data">

        <fieldset>
            <legend>Clique no botão "Arquivo" e selecione o arquivo</legend>
            <div id="ctnImportacao"></div>

            <input type="file" id="importar" name="importar" accept="text/csv">
        </fieldset>

        <input type="button" id="btnProcessar" value="Processar">
    </form>
</div>
<div class="container">
    <form id="formGerar">
        <fieldset>
            <legend>Gerar Template</legend>
            <table class="form-container">
                <tr>
                    <td>Data Inicial:</td>
                    <td><input id="dataInicio" name="dataInicio" type="text"/></td>
                    <td>Data Final:</td>
                    <td><input id="dataFinal" name="dataFinal" type="text"/></td>
                </tr>
                <tr>
                    <td>Estrutural:</td>
                    <td colspan="3">
                        <input type="text" name="estrutural" id="estrutural" class="field-size-max">
                    </td>
                </tr>
                <tr>
                    <td><a href="#" id="ancoraConta">Contrapartida:</a></td>
                    <td colspan="3">
                        <input type="text" id="reduzido" name="reduzido" lang="c61_reduz">
                        <input type="text" id="descricaoConta" name="descricaoConta" lang="c60_descr"
                               readonly class="readonly">
                    </td>
                </tr>

                <tr>
                    <td><a href="#" id="ancoraHistorico">Histórico:</a></td>
                    <td colspan="3">
                        <input type="text" id="historico" name="historico" lang="c50_codhist">
                        <input type="text" id="descricaoHistorico" name="descricaoHistorico" lang="c50_descr"
                               readonly class="readonly">
                    </td>
                </tr>
            </table>
        </fieldset>
        <input type="button" id="gerar_csv" value="Gerar CSV">
    </form>
</div>

<script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>

<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<?php db_menu(); ?>
</body>
<script type="text/javascript">
    const dataAtual = new Date();
    const formulario = $('form-upload');
    const btnProcessar = $('btnProcessar');

    const estrutural = $('estrutural');
    const ancoraConta = $('ancoraConta');
    const reduzido = $('reduzido');
    const descricaoConta = $('descricaoConta');

    const ancoraHistorico = $('ancoraHistorico');
    const historico = $('historico');
    const descricaoHistorico = $('descricaoHistorico');

    const btnGerar = $('gerar_csv');
    const inputDataInicio = new DBInputDate(document.getElementById('dataInicio'));
    const inputDataFinal = new DBInputDate(document.getElementById('dataFinal'));
    const dataHoje = new Date();

    inputDataInicio.setValue(`${dataHoje.getUTCFullYear()}-01-01`);
    inputDataFinal.setValue(dataHoje.toLocaleString());

    var lookupReduz = new DBLookUp(ancoraConta, reduzido, descricaoConta, {
        "sArquivo": "func_conplanoreduz.php",
        "sObjetoLookUp": "db_iframe_conplanoreduz",
        "sLabel": "Pesquisar Reduzidos"
    });

     var lookupHistorico = new DBLookUp(ancoraHistorico, historico, descricaoHistorico, {
        "sArquivo": "func_conhist.php",
        "sObjetoLookUp": "db_iframe_conhist",
        "sLabel": "Pesquisar Histórico"
    });

    btnProcessar.addEventListener('click', async function () {
        if (!confirm('Antes de prosseguir, confirme que esta na instituição e data do sistema correto!')) {
            return
        }

        try {
            js_divCarregando('Aguarde...', 'loading_message');
            const response = await CurrentWindow.axios.post(
                'v4/api/financeiro/contabilidade/fix/ajuste-saldo-contas-msc',
                new FormData(formulario)
            );
            alert(response.data.message);
            js_removeObj('loading_message')
        } catch (e) {
            alert(e.message)
            js_removeObj('loading_message')
        }
    });

    btnGerar.addEventListener('click', async function () {
        try {
            if (reduzido.value == '') {
                alert('Informe uma conta para contrapartida.')
                return false;
            }

            if (estrutural.value == '') {
                alert('Informe o estrutural.')
                return false;
            }

            const parameters = {
                dataInicial: inputDataInicio.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
                dataFinal: inputDataFinal.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
                contrapartida : reduzido.value,
                estrutural: estrutural.value,
                historico: historico.value,
            };

            js_divCarregando('Aguarde...', 'loading_message');
            const rota = 'v4/api/financeiro/contabilidade/fix/gera-csv-ajuste-saldo-contas';
            const response = await CurrentWindow.axios.post(rota, parameters);
            const download = new DBDownload();
            download.addFile(response.data.data.csv, "Template - CSV");
            download.show();

            js_removeObj('loading_message')
        } catch (e) {
            alert(e.message)
            js_removeObj('loading_message')
        }
    })

</script>

</html>
