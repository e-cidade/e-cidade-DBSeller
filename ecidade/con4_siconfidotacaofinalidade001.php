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
 *  Voce deve ter recebido uma copia d  a Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_utils.php"));
require(modification("libs/db_app.utils.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
$lReadOnly = "false";
$lUsuario = "false";
$oGet = db_utils::postMemory($_GET);
if (isset($oGet->readonly)) {
    $lReadOnly = $oGet->readonly;
}
if (isset($oGet->usuario)) {
    $lUsuario = $oGet->usuario;
}
?>
<html>
<head>
    <?php
    db_app::load("scripts.js, prototype.js, widgets/windowAux.widget.js, strings.js");
    db_app::load("widgets/dbtextField.widget.js, datagrid.widget.js, widgets/dbcomboBox.widget.js, AjaxRequest.js");
    db_app::load("estilos.css, grid.style.css");
    ?>
    <style type="text/css">
        input[type="checkbox"] {
            margin: 0px;
            vertical-align: middle;
        }
    </style>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div class="container" style="width: 60%">
    <form method='post' id='form1'>
        <table style="width: 100%" >
            <tr>
                <td valign="top">
                    <fieldset>
                        <legend>
                            <b>Configuração do atributo ES</b>
                        </legend>
                        <table style="width: 100%">
                            <tr>
                                <td>
                                    <b>Tipo:</b>
                                    <select id='tipo' style="" onchange="init()">
                                        <option value='1'>MDE</option>
                                        <option value='2'>ASPS</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 100%">
                                    <fieldset>
                                        <legend>
                                            <b>Filtro para vinculação das dotações</b>
                                        </legend>
                                        <table style="width: 100%">
                                            <tr>
                                                <td>
                                                    <b>Órgão:</b>
                                                </td>
                                                <td>
                                                    <select id='cboOperadorOrgao'>
                                                        <option value='in'>Contendo</option>
                                                        <option value='notin'>Não Contendo</option>
                                                    </select>
                                                    <input id="orgaos" type="text"
                                                           onKeyPress="return js_mask(event,'0-9|,')">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>Unidade:</b>
                                                </td>
                                                <td>
                                                    <select id='cboOperadorUnidade'>
                                                        <option value='in'>Contendo</option>
                                                        <option value='notin'>Não Contendo</option>
                                                    </select>
                                                    <input id="unidade" type="text"
                                                           onKeyPress="return js_mask(event,'0-9|,')">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>Função:</b>
                                                </td>
                                                <td>
                                                    <select id='cboOperadorFuncao'>
                                                        <option value='in'>Contendo</option>
                                                        <option value='notin'>Não Contendo</option>
                                                    </select>
                                                    <input id="funcao" type="text"
                                                           onKeyPress="return js_mask(event,'0-9|,')">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>Subfunção:</b>
                                                </td>
                                                <td>
                                                    <select id='cboOperadorSubFuncao'>
                                                        <option value='in'>Contendo</option>
                                                        <option value='notin'>Não Contendo</option>
                                                    </select>
                                                    <input id="subfuncao" type="text"
                                                           onKeyPress="return js_mask(event,'0-9|,')">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>Programa:</b>
                                                </td>
                                                <td>
                                                    <select id='cboOperadorPrograma'>
                                                        <option value='in'>Contendo</option>
                                                        <option value='notin'>Não Contendo</option>
                                                    </select>
                                                    <input id="programa" type="text"
                                                           onKeyPress="return js_mask(event,'0-9|,')">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>Projeto/Atividade:</b>
                                                </td>
                                                <td>
                                                    <select id='cboOperadorProjAtiv'>
                                                        <option value='in'>Contendo</option>
                                                        <option value='notin'>Não Contendo</option>
                                                    </select>
                                                    <input id="projetoatividade" type="text"
                                                           onKeyPress="return js_mask(event,'0-9|,')">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>Recurso:</b>
                                                </td>
                                                <td>
                                                    <select id='cboOperadorRecurso'>
                                                        <option value='in'>Contendo</option>
                                                        <option value='notin'>Não Contendo</option>
                                                    </select>
                                                    <input id="recurso" type="text"
                                                           onKeyPress="return js_mask(event,'0-9|,')">
                                                </td>
                                            </tr>
                                            <tr style="display: none;">
                                                <td>
                                                    <b>Car. Peculiar:</b>
                                                </td>
                                                <td>
                                                    <select id='cboCaracteristica'>
                                                        <option value='in'>Contendo</option>
                                                        <option value='notin'>Não Contendo</option>
                                                    </select>
                                                    <input id="caracteristicapeculiar" type="text">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="3" style="width:100%">
                                                    <fieldset id='ctnGridDotacoes'>
                                                    </fieldset>
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <input type='button' id='btnSalvarFiltros' value='Salvar'>
                                    <input type='button' id='btnExcluirSelecionados' value='Excluir Selecionados'>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
        </table>
    </form>
</body>
</div>
</html>
<script>

    var sUrlRPC = "con4_siconfidotacaofinalidade.RPC.php";

    function init() {

        oGridDotacoes = new DBGrid('gridcontas');
        oGridDotacoes.nameInstance = 'oGridDotacoes';
        oGridDotacoes.setCheckbox(0);
        oGridDotacoes.setHeader(new Array("id", "Conta", "Descrição", "Tipo"));
        oGridDotacoes.setCellWidth(new Array("2%", "20%", "60%", "20%"));
        oGridDotacoes.setCellAlign(new Array("center", "center", "left", "center"));
        oGridDotacoes.aHeaders[1].lDisplayed = false;
        oGridDotacoes.show($('ctnGridDotacoes'));
        oGridDotacoes.clearAll(true);
        carregarDadosGrid();

    }

    function carregarDadosGrid() {

        var oParam = new Object();
        oParam.exec = 'getDotacoesConfiguradas';

        new AjaxRequest(sUrlRPC, oParam,
            function (oRetorno, lErro) {

                oGridDotacoes.clearAll(true);
                oRetorno.aDotacoesConfiguradas.each(function (oDotacao, id) {

                    var aDotacao = new Array();
                    aDotacao[0] = oDotacao.sequencial;
                    aDotacao[1] = oDotacao.dotacao;
                    aDotacao[2] = oDotacao.descricao;
                    aDotacao[3] = (oDotacao.tipo == 1 ? 'MDE' : 'ASPS');
                    oGridDotacoes.addRow(aDotacao);
                });
                oGridDotacoes.renderRows();
            }
        ).setMessage('Aguarde, buscando dotações ja configuradas para o tipo.').execute();

    }


    function salvar() {

        var oParam = {

            exec: 'salvar',
            tipo: $('tipo').value,
            orgao: $('orgaos').value,
            contendo_orgao: $('cboOperadorOrgao').value,
            unidade: $('unidade').value,
            contendo_unidade: $('cboOperadorUnidade').value,
            funcao: $('funcao').value,
            contendo_funcao: $('cboOperadorFuncao').value,
            subfuncao: $('subfuncao').value,
            contendo_subfuncao: $('cboOperadorSubFuncao').value,
            programa: $('programa').value,
            contendo_programa: $('cboOperadorPrograma').value,
            projeto_atividade: $('projetoatividade').value,
            contendo_projeto_atividade: $('cboOperadorProjAtiv').value,
            recurso: $('recurso').value,
            contendo_recurso: $('cboOperadorRecurso').value
        };

        new AjaxRequest(sUrlRPC, oParam,

            function (oRetorno, lErro) {

                if (lErro) {
                    alert(oRetorno.mensagem);
                    return false;
                }
                carregarDadosGrid();
            }
        ).setMessage('Aguarde, buscando e salvando as dotações de acordo com o filtro selecionado.').execute();

    }

    function excluirSelecionados() {

        var sSequenciais = "";

        var qtdSelecionados = oGridDotacoes.getSelection("object").length
        if (qtdSelecionados === 0) {
            alert("Selecione pelo menos um registro registro para excluir.");
            return false;
        }

        oGridDotacoes.getSelection("object").each(function (linha) {
            sSequenciais += linha.aCells[1].getValue() + ",";
        });
        sSequenciais = sSequenciais.substring(0, (sSequenciais.length - 1));

        var oParam = {
            exec: 'excluir',
            sequenciais: sSequenciais
        };

        new AjaxRequest(sUrlRPC, oParam,

            function (oRetorno, lErro) {

                if (lErro) {
                    alert(oRetorno.mensagem.urlDecode());
                    return false;
                }
                carregarDadosGrid();
            }
        ).setMessage('Aguarde, buscando e salvando as dotações de acordo com o filtro selecionado.').execute();

    }

    init();

    $('btnSalvarFiltros').observe("click", salvar);
    $('btnExcluirSelecionados').observe("click", excluirSelecionados);

</script>


