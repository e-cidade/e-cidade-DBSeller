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
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>

    <style>
        #fade {
            position: absolute;
            top: 0;
            display: none;
            height: 100vh;
            align-items: center;
            justify-content: center;
            width: 100%;
            z-index: 1;
            background: rgba(0, 0, 0, 0.7);
        }

        #ctnModal {
            width: 90%;
            top: 0;
            display: flex;
            flex-direction: column;
            background: white;
            border-radius: 2px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
        }

        #close {
            position: absolute;
            float: right;
            right: 5px;
            cursor: pointer;
        }

        .form-container input[type="text"] {
            min-height: 22px;
            margin-left: 2px;
        }

        .form-container select {
            min-height: 22px;
            width: 100%;
        }

    </style>
</head>
<body class="body-default">
<div class="alert alert-primary text-left" role="alert">
    Essa rotina realiza o vínculo das contas do PCASP com as contas do e-cidade.<br>
    - Para mapear as contas, selecione a "Conta PCASP Governo" e clique em <kbd>Adicionar
        <i class="fa fa-plus"></i></kbd> para selecionar as contas do e-Cidade.<br>
    - <kbd><i class="fas fa-print"></i></kbd> imprime as contas: não mapeadas com movimentação,
    não mapeadas sem movimentação e as contas mapeadas.
</div>
<div class="container">
    <table class="form-container" >
        <tr>
            <td><label for="plano">Plano de Contas:</label></td>
            <td>
                <select id="plano" name="plano" rel="ignore-css">
                    <option value="uniao">União / Federação</option>
                    <option value="UF">Estadual / Regional</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><a href="#" id="ancoraConta">Conta PCASP Governo:</a></td>
            <td>
                <input type="text" id="conta" name="conta" class="field-size4" rel="ignore-css">
                <input type="text" id="descricaoConta" name="descricaoConta" class="field-size7 readonly" readonly
                       rel="ignore-css">
            </td>
            <td style="text-align: left">
                <button type="button" id="btnClean" name="btnClean" class="btn btn-light">
                    <i class="fas fa-eraser"></i>
                </button>
            </td>
        </tr>
    </table>
</div>

<div id="fade">
    <div id="ctnModal">
        <div class="alert text-left" role="alert">
            <i id="close" class="fas fa-window-close"></i>
            Clique sobre a linha para selecionar a conta.<br>
            <div class="alert-success"> - Contas que já possuem vínculos, mas podem ser selecionadas para adicionar/remover novas contas.</div>
        </div>
        <fieldset id="ctnPesquisaPcasp">
            <legend>Plano de Contas - <span id="labelTipoPlano"></span></legend>
            <div>
                <table id="tablePcasp"
                       class="table table-sm"
                       data-height="400"
                       data-virtual-scroll="true"

                       style="width: 100%;">
                </table>
            </div>
        </fieldset>
    </div>
</div>

<div class="subcontainer" style="width: 1200px">
    <fieldset id="ctnPesquisaPcasp">
        <legend>Planos de Contas e-Cidade</legend>
        <table id="tableConplano"
               class="table table-sm"
               data-height="250"
               data-virtual-scroll="true"

               style="width: 100%;">
        </table>
    </fieldset>

    <button type="button" id="btnVincular" name="btnVincular" class="btn btn-light">
        <i class="fas fa-save"></i>
        Vincular
    </button>
    <button type="button" id="btnImprimir" name="btnImprimir" class="btn btn-light" title="Imprime Mapeamento">
        <i class="fas fa-print"></i>
    </button>
</div>

<?php db_menu() ?>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<script type="text/javascript" src='scripts/widgets/Collection.widget.js'></script>
<script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<script type="text/javascript">
    $.noConflict();
    var contaSelecionada = null;

    const collectionContasEcidade = new Collection().setId("c60_codigo");

    const cboTipoPlano = document.getElementById('plano');
    const labelTipoPlano = document.getElementById('labelTipoPlano');
    const ancoraConta = document.getElementById('ancoraConta');
    const inputConta = document.getElementById('conta');
    const inputDescricaoConta = document.getElementById('descricaoConta');
    const btnClean = document.getElementById('btnClean');
    const btnVincular = document.getElementById('btnVincular');

    const fade = document.getElementById('fade');
    const close = document.getElementById('close');
    close.onclick = () => {
        fade.style.display = "none"
    }

    const routs = {
        pcasp: 'financeiro/contabilidade/plano-contas/consulta/pcasp/padrao',
        ecidade: 'financeiro/contabilidade/plano-contas/consulta/pcasp/ecidade',
        vincular: 'financeiro/contabilidade/plano-contas/pcasp/vincular',
        vincularGeral: 'financeiro/contabilidade/plano-contas/pcasp/vincular-geral',
        mapeamento: 'financeiro/contabilidade/plano-contas/emitir/pcasp/mapeamento'
    }

    const abrirPesquisaContaPcasp = () => {
        contaSelecionada = null;
        btnClean.dispatchEvent(new Event('click'));
        tablePcasp.bootstrapTable('removeAll');
        labelTipoPlano.innerHTML = cboTipoPlano.options[cboTipoPlano.selectedIndex].innerHTML;
        fade.style.display = "flex"
        buscaPlanoPadrao();
    };

    ancoraConta.addEventListener('click', abrirPesquisaContaPcasp);
    inputConta.addEventListener('change', abrirPesquisaContaPcasp);

    const buscaPlanoPadrao = () => {
        const formData = new FormData();
        formData.append('tipoPlano', cboTipoPlano.value);
        formData.append('exercicio', PHPSession.getValueSession('DB_anousu'));
        formData.append('apenasAnaliticas', 1);
        formData.append('existeVinculo', 1);
        if (inputConta.value != '') {
            formData.append('conta', inputConta.value);
        }
        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.pcasp}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            tablePcasp.bootstrapTable('load', response.data);
        });
    };

    const linhaComVinculo = (row, index) => {
        if (row.vinculada_outra_selecionada) {
            return {classes: 'alert-danger', css: {'font-weight': 'bold'}};
        }

        if (row.tem_vinculo) {
            return {classes: 'alert-success', css: { 'font-weight': 'bold'}};
        }
        return {classes: ''}
    }

    const tablePcasp = jQuery('#tablePcasp');
    tablePcasp.bootstrapTable({
        locale: 'pt-BR',
        search: true,
        onClickRow: function (row, $element, field) {
            fade.style.display = "none";
            contaSelecionada = row;
            inputConta.value = row.conta;
            inputDescricaoConta.value = row.nome;

            buscarContasEcidade();
        },
        rowStyle: linhaComVinculo,
        columns: [
            {
                "title": "Conta",
                "field": 'conta',
                "align": 'center',
                "valign": 'middle',
                "width" : "150"
            },
            {
                "title": "Nome",
                "field": 'nome',
                "align": 'left',
                "valign": 'middle'
            }
        ]
    });

    const operateFormatterActions = (value, row, index) => {
        return [
            '<a class="excluir" href="javascript:void(0)" title="Excluir">',
            '  <i class="fas fa-trash-alt"></i>',
            '</a>'
        ].join('')
    }

    window.operateEvents = {
        'click .excluir': function (e, value, row, index) {

            collectionContasEcidade.remove(row.c60_codigo);
            tablePlanoEcidade.bootstrapTable('load', collectionContasEcidade.build());
        }
    };

    const tablePlanoEcidade = jQuery('#tableConplano');
    tablePlanoEcidade.bootstrapTable({
        locale: 'pt-BR',
        rowStyle: linhaComVinculo,
        buttons: function () {
            return {
                btnAdd: {
                    html:
                        '<div style="text-align: right; margin-right: 5px;">' +
                        '  <button class="adicionar btn btn-light"> Adicionar <i class="fa fa-plus"></i></button>' +
                        '  <i class="fas fa-question-circle" style="margin-left: 15px; margin-right: 5px"></i>' +
                        '</div>',
                }
            }
        },
        columns: [
            {
                "title": "Conta e-Cidade",
                "field": 'c60_estrut',
                "align": 'center',
                "valign": 'middle',
                "width" : "150"
            },
            {
                "title": "Nome",
                "field": 'c60_descr',
                "align": 'left',
                "valign": 'middle'
            },
            {
                "title": "Ações",
                "field": 'acoes',
                "align": 'center',
                "valign": 'middle',
                "width" : "50",
                events: window.operateEvents,
                formatter: operateFormatterActions
            }
        ]
    });

    /**
     * busca as contas do e-cidade vinculadas a conta do governo
     */
    const buscarContasEcidade = () => {
        const formData = new FormData();
        formData.append('exercicio', PHPSession.getValueSession('DB_anousu'));
        formData.append('idContaVinculada', contaSelecionada.id);
        formData.append('apenasAnaliticas', 1);
        formData.append('temVinculoTipoPlano', cboTipoPlano.value);
        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.ecidade}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            collectionContasEcidade.clear();
            loadContasEcidade(response.data)
        });
    };

    function buscarCodcon(estrutural) {
        db_iframe_conta.hide();
        const formData = new FormData();
        formData.append('exercicio', PHPSession.getValueSession('DB_anousu'));
        formData.append('estrutural', estrutural);
        formData.append('apenasAnaliticas', 1);
        formData.append('temVinculoTipoPlano', cboTipoPlano.value);
        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.ecidade}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            loadContasEcidade(response.data)
        });
    }

    document.querySelector('.adicionar').addEventListener('click', () => {
        if (contaSelecionada === null) {
            alert('Antes de buscar as contas do e-Cidade, você deve selecionar a conta do Plano de contas do Governo.');
            return;
        }
        var sUrl = 'func_conplanogeral.php?funcao_js=parent.buscarCodcon|c60_estrut';
        js_OpenJanelaIframe('', 'db_iframe_conta', sUrl, 'Pesquisa', true, '0');
    });

    /**
     * - Adiciona as contas do e-cidade (conplano) na collection
     * - Trata se a conta esta vinculada, e se esta vinculada a conta (Plano Governo) selecionada
     * - Carrega os dados na tabela
     * @param contas
     */
    const loadContasEcidade = contas => {
        contas.each(conta => {
            if (conta.conta_vinculada != null) {
                conta.tem_vinculo = true;
                conta.vinculada_outra_selecionada = conta.conta_vinculada !== contaSelecionada.conta;
            }
        });

        collectionContasEcidade.add(contas);
        tablePlanoEcidade.bootstrapTable('load', collectionContasEcidade.build());
    };

    btnClean.addEventListener('click', () => {
        contaSelecionada = null;
        inputConta.value = '';
        inputDescricaoConta.value = '';
        tablePlanoEcidade.bootstrapTable('removeAll');
    });

    cboTipoPlano.addEventListener('change', () => {
        btnClean.dispatchEvent(new Event('click'));
    });

    btnVincular.addEventListener('click', () => {
        const formData = new FormData();
        formData.append('exercicio', PHPSession.getValueSession('DB_anousu'));
        formData.append('pcasp_id', contaSelecionada.id);
        let contasVinculadasOutraConta = [];
        collectionContasEcidade.build().each(conta => {
            if (conta.vinculada_outra_selecionada) {
                contasVinculadasOutraConta.push(conta.c60_estrut);
                return
            }

            formData.append('contas_ecidade[]', conta.c60_codigo)
        });

        if (contasVinculadasOutraConta.length > 0) {
            let msg = `As contas do e-cidade grifadas em vermelho na tabela "Planos de Contas e-Cidade" nao seram `
            +`vinculadas a conta: ${contaSelecionada.conta}.\nContas do e-cidade que não serão vinculadas: `
            + contasVinculadasOutraConta.join(', ');
            alert(msg);
        }

        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.vincular}`, {body: formData}).then(response => {
            alert(response.message);
            if (response.error) {
                return;
            }

            btnClean.dispatchEvent(new Event('click'));
        });
    });

    btnImprimir.addEventListener('click', () => {
        const formData = new FormData();
        formData.append('exercicio', PHPSession.getValueSession('DB_anousu'));
        formData.append('tipoPlano', cboTipoPlano.value);
        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.mapeamento}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            const download = new DBDownload();
            download.addFile(response.data.csv, "Relatório do mapeamento");
            download.show();
        });
    })
</script>
</body>
</html>
