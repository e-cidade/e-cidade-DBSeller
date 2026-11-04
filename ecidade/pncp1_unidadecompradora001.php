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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('libs/db_utils.php');
require_once modification('libs/db_app.utils.php');
require_once modification('dbforms/db_funcoes.php');
$instituicao = InstituicaoRepository::getInstituicaoByCodigo(db_getsession('DB_instit'));
$cnpj = $instituicao->getCNPJ();
$nomeInstituicao = InstituicaoRepository::getInstituicaoPrefeitura()->getDescricao();
$codigoIBGE = $instituicao->getDadosPrefeitura()->getCodigoIbge();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link type="text/css" href="estilos.css" rel="stylesheet">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <style>
        select:disabled,input:disabled {
            color: dimgrey;
        }
    </style>
</head>
<body>
<div class="alert alert-primary text-left" role="alert">
    Não é permitido alteração de informações como Código, Nome e Munícipio da Unidade.
    Caso necessário utilize o recurso "Ativo".
</div>
<div class="container">
    <fieldset>
        <legend>Unidades para PNCP</legend>
        <table class="form-container">
            <tr>
                <input type="hidden" id="unidade_documento" value="<?= $cnpj;?>">
            </tr>
            <tr>
                <td>
                    <label for="orgao_descricao">Orgão/Entidade: </label>
                </td>
                <td>
                    <input type="text" id="orgao_descricao" class="field-size9" disabled>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="unidade_codigo">Código da Unidade:</label>
                </td>
                <td>
                    <input type="text" id="unidade_codigo" class="field-size9" maxlength="30"
                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="unidade_nome">Nome da Unidade:</label>
                </td>
                <td>
                    <input type="text" id="unidade_nome" class="field-size9" maxlength="100">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="unidade_municipio">Município:</label>
                </td>
                <td>
                    <select name="unidade_municipio" id="unidade_municipio" disabled style="color: dimgrey">
                        <option value="<?= $codigoIBGE ?>"><?= $instituicao->getMunicipio()?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="unidade_ativo">Ativo:</label>
                </td>
                <td>
                    <select name="unidade_ativo" id="unidade_ativo">
                        <option value="true">Sim</option>
                        <option value="false">Não</option>
                    </select>
                </td>
            </tr>
        </table>
    </fieldset>
    <button type="button" id="btn_salvar">
        <i class="fas fa-save"></i>
        Salvar
    </button>
    <button type="button" id="btn_limpar">
        <i class="fas fa-eraser"></i>
        Limpar
    </button>
</div>
</div>
<div class="subcontainer" style="width: 950px;">
    <fieldset>
        <legend>Unidades</legend>
        <table id="data-table" class="table table-sm">
        </table>
    </fieldset>
</div>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script>
    $.noConflict();

    window.addEventListener('load', async () => {
        var apiUrl;
        await PHPSession.loadData().then(() => {
            apiUrl = PHPSession.requestApi;
        });

        const orgaoDescricao = document.getElementById("orgao_descricao");
        const unidadeCodigo = document.getElementById("unidade_codigo");
        const unidadeNome = document.getElementById("unidade_nome");
        const unidadeMunicipio = document.getElementById("unidade_municipio");
        const unidadeAtivo = document.getElementById("unidade_ativo");
        const unidadeDocumento = document.getElementById("unidade_documento");
        const btnSalvar = document.getElementById("btn_salvar");
        const btnLimpar = document.getElementById("btn_limpar");
        const formData = new FormData();
        const tabelaUnidades = jQuery('#data-table');
        let toggleAlteracao = false;
        const routes = {
            inclusaoUnidades: `${apiUrl}/patrimonial/pncp/unidades/incluir/`,
            buscarEntidade: `${apiUrl}/patrimonial/pncp/unidades/buscarEntidade/`,
            buscarUnidades: `${apiUrl}/patrimonial/pncp/unidades/buscarUnidades/`,
            verificaIntegracao: `${apiUrl}/patrimonial/pncp/integracao/verificaIntegracao/`
        }

        function limpaCampos() {
            unidadeCodigo.value = '';
            unidadeNome.value = '';
        }

        function verificaCampos() {
            if (unidadeCodigo.value === '') {
                alert("Código da unidade não pode ser vazio");
                return false;
            }
            if (unidadeNome.value === '') {
                alert("Nome da unidade não pode ser vazio");
                return false;
            }
            return true;
        }

        function verificaIntegracao() {
            const formData = new FormData();
            formData.append('documento', unidadeDocumento.value);
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.verificaIntegracao, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                if (response.data === null) {
                    btnSalvar.disabled = true;
                    alert("Por favor, verifique a configuração de integração com o PNCP!");
                } else {
                    buscarEntidade();
                    buscarUnidades();
                }
            })
        }
        verificaIntegracao();

        function buscarEntidade() {
            formData.append('documento', unidadeDocumento.value);
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.buscarEntidade, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                orgaoDescricao.value = response.data.razaoSocial;
            })
        }

        btnLimpar.addEventListener('click', () => {
            unidadeCodigo.value = '';
            unidadeNome.value = '';
            unidadeAtivo.value = 'true';
            unidadeNome.disabled = false;
            unidadeCodigo.disabled = false;
        })

        btnSalvar.addEventListener('click', () => {
            if (!verificaCampos()) {
                return false;
            }
            verificaCampos();
            formData.append('codigoIbge', unidadeMunicipio.value);
            formData.append('unidadeCodigo', unidadeCodigo.value.replaceAll(' ', ''));
            formData.append('unidadeNome', unidadeNome.value);
            formData.append('documento', unidadeDocumento.value);
            formData.append('ativo', unidadeAtivo.value);
            formData.append('alteracao', toggleAlteracao);
            HttpClient.post(routes.inclusaoUnidades, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                toggleAlteracao = false;
                alert(response.message);
                limpaCampos();
                buscarUnidades();
                unidadeCodigo.disabled = false;
                unidadeNome.disabled = false;
                unidadeAtivo.value = 'true';
            })
        })

        function buscarUnidades() {
            formData.append('documento', unidadeDocumento.value);
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.buscarUnidades, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message)
                }
                tabelaUnidades.bootstrapTable('load', response.data);
            })
        }

        jQuery(document).ready(jQuery => {
            window.operateEvents = {
                'click .alterar': (e, d, data) => {
                    unidadeCodigo.value = data.codigoUnidade;
                    unidadeNome.value = data.nomeUnidade;
                    unidadeAtivo.value = data.ativo;
                    unidadeNome.disabled = true;
                    unidadeCodigo.disabled = true;
                    toggleAlteracao = true;
                }
            };
            const colunas = [
                {
                    field: 'ativo',
                    title: 'Ativo',
                    halign: 'center',
                    align: 'center',
                    formatter: (value) => {
                        if (value) {
                            return `<i class="fas fa-check-circle fa-lg" style="color: #449d44"></i>`;
                        }
                        return `<i class="fas fa-times-circle fa-lg" style="color: #c9302c"></i>`;
                    },
                },
                {
                    field: 'codigoOrgao',
                    title: 'Código Órgão',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'orgao',
                    title: 'Órgão',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'codigoUnidade',
                    title: 'Código Unidade',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'nomeUnidade',
                    title: 'Nome da Unidade',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'data',
                    title: 'Data de Inclusão',
                    halign: 'center',
                    align: 'center',
                    formatter:(value) => {
                        let data = value.substring(0, 10);
                        return data.split('-').reverse().join('/');
                    }
                },
                {
                    field: 'acao',
                    title: 'Ações',
                    halign: 'center',
                    align: 'center',
                    formatter: () => {
                        return ['<a class="alterar" href="javascript:void(0)" title="Alterar">',
                            '  <i class="fas fa-edit"></i>',
                            '</a>'].join('')
                    },
                    events:  window.operateEvents
                }
            ];

            tabelaUnidades.bootstrapTable({
                locale: 'pt-BR',
                height: 400,
                search: false,
                class: "table table-sm",
                columns: colunas,
                showButtonText: false,
                detailView: false,
                cache: false,
                useRowAttrFunc: true,
                reorderableRows: true,
            });
        });
    });
</script>
</body>
