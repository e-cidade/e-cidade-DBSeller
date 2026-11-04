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

$requisicaoExame = new RequisicaoExame();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
          name="viewport">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
</head>
<body class="body-default">
<div class="container">
    <form name="form1">

        <input type="hidden" id="cgsId">

        <fieldset class="container" style="border: 2px groove #FFF">
            <legend>Regredir Situação do Exame</legend>
            <table class="form-container">
                <tr>
                    <td colspan="5" id="linhaIdade" style="text-align: end; line-height: 0">
                    </td>
                </tr>
                <tr>
                    <td title="<?= (!empty($Tla22_i_codigo) ? $Tla22_i_codigo : null) ?>">
                        <label id="ancora" for="codigo">Requisição:</label>
                    </td>
                    <td id="inputCodigoRequisicao">
                        <input type="text" name="codigo" id="la22_i_codigo">
                        <input type="text" name="descricao" id="z01_v_nome" class="field-size10">
                    </td>
                </tr>
            </table>
            <div style="width: 580px; margin-top: 10px">
                <table id="data-table"
                       class="table table-responsive-md"
                       data-height="250"
                       data-virtual-scroll="true"
                       style="width: 100%;">
                </table>
            </div>
            <button type="button" id="btn-salvar" style="margin-top: 10px"><i class="fas fa-save"></i> Salvar</button>
            <button type="button" id="btn-limpar"><i class="fas fa-eraser"></i> Limpar</button>
        </fieldset>
    </form>
</div>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript">
    $.noConflict()
    jQuery(document).ready(function () {
        const inputCodigo = document.getElementById('la22_i_codigo');
        const inputDescricao = document.getElementById('z01_v_nome');
        const ancora = document.getElementById('ancora');
        const btnLimpar = document.getElementById('btn-limpar');
        const btnSalvar = document.getElementById('btn-salvar');

        const situacoes = <?php echo JSON::create()->stringify($requisicaoExame->getAllSituacoes()); ?>;

        new DBLookUp(ancora, inputCodigo, inputDescricao, {
            'sArquivo': 'func_lab_requisicao.php',
            'sLabel': 'Pesquisar Requisição',
            'sObjetoLookUp': "db_iframe_requisicao",
            'aCamposAdicionais': ['z01_i_cgsund'],
            'fCallBack': (codigo, descricao, cgs = null) => {
                buscarExames(cgs);
            }
        });

        btnLimpar.addEventListener('click', () => {
            inputCodigo.value = "";
            inputDescricao.value = "";
            table.bootstrapTable('load', []);
            apagaIdadeCompleta();
        });

        const salvar = () => {
            const formData = new FormData
            formData.append('acao', 'regredirSituacao')
            formData.append('codigoRequisicao', inputCodigo.value);
            formData.append('itensRequisicao', JSON.stringify(table.bootstrapTable('getData')));

            HttpClient.post('lab4_regredirsituacao001.RPC.php', {body: formData}).then(response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                btnLimpar.click();
                alert("Exames salvos com sucesso.");
            })
        }

        btnSalvar.addEventListener('click', salvar);

        window.selectEvents = {
            'change select': function (e, value, row, index) {
                row.situacao = e.target.value;
            }

        }

        var table = jQuery('#data-table');

        const formatterSelect = (value, row) => {
            let cboSituacoes = document.createElement('select');
            cboSituacoes.setAttribute('name', 'selectSituacao');
            cboSituacoes.id = row.codigoitemrequisicao;
            situacoes.map((situacao) => {
                if (situacao.codigo != "10 - Nao Digitado") {
                    cboSituacoes.add(new Option(situacao.descricao, situacao.codigo));
                }
            })
            return cboSituacoes.outerHTML;
        }

        table.bootstrapTable({
            locale: 'pt-BR',
            columns: [
                {
                    title: 'Exames',
                    field: 'exame',
                    align: 'left',
                    valign: 'middle'
                },
                {
                    title: 'Situação',
                    field: 'situacao',
                    align: 'center',
                    valign: 'middle',
                    formatter: formatterSelect,
                    events: selectEvents
                }
            ],
            onPostBody: (data) => {
                let selectSituacao = document.getElementsByName('selectSituacao');                
                selectSituacao.forEach((selectSituacao) => {
                    data.map((exame) => {
                        if (exame.codigoitemrequisicao == selectSituacao.getAttribute('id')) {
                            selectSituacao.value = exame.situacao;
                        }
                    })

                    selectSituacao.childNodes.forEach((elemento) => {
                        if (elemento.value > elemento.parentElement.value) {
                            elemento.setAttribute("disabled", "");
                        }
                    })
                })
            }
        })

        const buscarExames = (cgs = false) => {
            const formData = new FormData();
            formData.append('requisicao', inputCodigo.value);
            formData.append('acao', 'buscarExamesPorRequisicao');
            HttpClient.post('lab4_regredirsituacao001.RPC.php', {body: formData}).then((response) => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                table.bootstrapTable('load', response.exames);
            });

            if (cgs) {
                document.getElementById('cgsId').value = cgs;
                existeCgsUnd();
            }
        }
    });

    function existeCgsUnd() {
        let cgsUnd = document.getElementById('cgsId').value;

        if (cgsUnd !== '') {
            let parametros = {
                'sExecucao': 'buscarDadosCgs',
                'iCgs': cgsUnd
            };

            let dadosRequisicao = {};
            dadosRequisicao.method = 'post';
            dadosRequisicao.parameters = 'json=' + Object.toJSON(parametros);
            dadosRequisicao.onComplete = function (resposta) {

                let retorno = JSON.parse(resposta.responseText);

                retornaIdadePaciente(retorno);
            };

            const rpc = 'sau4_cgs.RPC.php';
            new Ajax.Request(rpc, dadosRequisicao);
            return;
        }

        return;
    }

    function retornaIdadePaciente(retorno) {
        let idadeCompleta = retorno.oCgs.sIdadeCompleta.urlDecode();

        $('linhaIdade').innerHTML = `<p id="idadeCompleta" style="font-weight: normal;" >Idade: <span style="color: red;">${idadeCompleta}</span></p>`;

        return;
    }

    function apagaIdadeCompleta() {
        let tagIdadeCompleta = document.querySelector("#idadeCompleta");

        if (tagIdadeCompleta) {
            tagIdadeCompleta.remove();
        }
    }
</script>
</body>
</html>
