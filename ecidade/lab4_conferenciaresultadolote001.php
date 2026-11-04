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

require_once(modification('libs/db_utils.php'));
require_once(modification('libs/db_stdlib.php'));
require_once(modification('libs/db_conecta.php'));
require_once(modification('libs/db_sessoes.php'));
require_once(modification('libs/db_usuariosonline.php'));
require_once(modification('dbforms/db_funcoes.php'));
require_once(modification('libs/db_utils.php'));

$oRotulo = new rotulocampo;
$oRotulo->label("la09_i_exame");
$oRotulo->label("la24_i_setor");
$oRotulo->label("la02_i_codigo");
$oRotulo->label("la71_sequencial");

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <!--Bootstrap-table-->
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
    <script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>
    <!--Bootstrap-table-->

    <style>
        .th-inner {
            color: white;
        }

        .bootstrap-table {
            width: 95%;
            margin: 0 auto;
        }

        .fixed-table-pagination {
            display: flex;
            justify-content: space-around;
        }

        .active > a {
            background-color: #2c5676;
            text-decoration: none;
            color: white;
        }

        a:hover:not(.active) {
            background-color: #4a789c;
            text-decoration: none;
            color: white;
        }

        .dropdown-menu {
            background-color: white;
            height: 70px;
        }

        .dropdown-menu .dropdown-item.active {
            background-color: #2c5676;
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body bgcolor="#CCCCCC">
<form name='form1' class="container" style="margin-bottom: 15px;">
    <fieldset>
        <legend>Conferência de Exames - Lote</legend>
        <table class="form-container">
            <tr>
                <td class="bold">
                    Período:
                </td>
                <td>
                    <?php
                    db_inputdata('dData1', @$iDia1, @$iMes1, @$iAno1, true, 'text', 1, "");
                    ?>
                    <label>à</label>
                    <?php
                    db_inputdata('dData2', @$iDia2, @$iMes2, @$iAno2, true, 'text', 1, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td class="bold">
                    <?php
                    db_ancora('<b>Laboratório:</b>', "pesquisaLaboratorio(true);", "");
                    ?>
                </td>
                <td>
                    <?php
                    db_input(
                        'la02_i_codigo',
                        10,
                        $Ila02_i_codigo,
                        true,
                        'text',
                        "",
                        " onchange='pesquisaLaboratorio(false);' "
                    );
                    db_input('la02_c_descr', 50, @$Ila02_c_descr, true, 'text', 3, '');
                    ?>
                </td>
            </tr>
            <tr>
                <td class="bold" nowrap title="<?= @$Tla24_i_setor ?>">
                    <?php
                    db_ancora(@$Lla24_i_setor, "pesquisaSetor(true);", "");
                    ?>
                </td>
                <td>
                    <?php
                    db_input(
                        'la24_i_setor',
                        10,
                        $Ila24_i_setor,
                        true,
                        'text',
                        "",
                        " onchange='pesquisaSetor(false);'"
                    );
                    db_input('la24_i_codigo', 10, '', true, 'hidden', '', '');
                    db_input('la23_c_descr', 50, @$Ila23_c_descr, true, 'text', 3, '');
                    ?>
                </td>
            </tr>
            <tr>
                <td class="bold" nowrap title="<?= @$Tla09_i_exame ?>">
                    <?php
                    db_ancora(@$Lla09_i_exame, "pesquisaExame(true);", "");
                    ?>
                </td>
                <td>
                    <?php
                    db_input(
                        'la09_i_exame',
                        10,
                        @$Ila09_i_exame,
                        true,
                        'text',
                        "",
                        " onchange='pesquisaExame(false);'"
                    );
                    db_input('la08_c_descr', 50, @$Ila08_c_descr, true, 'text', 3, '');
                    ?>
                </td>
            </tr>
            <tr>
                <td class="bold">
                    <?php
                    db_ancora('<b>Grupo de Liberação:</b>', "pesquisaGrupoLiberacao(true);", "");
                    ?>
                </td>
                <td>
                    <?php
                    db_input(
                        'la71_sequencial',
                        10,
                        $Ila71_sequencial,
                        true,
                        'text',
                        "",
                        " onchange='pesquisaGrupoLiberacao(false);' "
                    );
                    db_input('la71_descricao', 50, @$Ila71_descricao, true, 'text', 3, '');
                    ?>
                </td>
            </tr>
            <tr>
        </table>
    </fieldset>
    <input name='pesquisar' type='button' value='Pesquisar' onclick="pesquisaRequisicoes()">
</form>

<div style="display: flex; flex-direction: column-reverse; align-items:center;" id="tabelaBox">
    <input id="botaoConferir" style="width: 75px; margin-top: 20px; display: none;" name='start' type='button'
           value='Conferir' onclick="confirma()">

    <table
        id="tabelaRequisicoes"
        data-side-pagination="server"
        data-page-size="30"
        data-page-list="[30, 50, 100]"
        data-pagination="true"
        data-height="450"
        data-ajax="getDadosTabela"
    </table>
</div>

<?php
db_menu();
?>
</body>
</html>

<script>
    "use strict";

    function mostralaboratorio(la02_c_descr, lErro) {
        document.form1.la02_c_descr.value = la02_c_descr;
        if (lErro == true) {
            document.form1.la02_i_codigo.focus();
            document.form1.la02_i_codigo.value = '';
        }
    }

    function mostralaboratorio1(la02_i_codigo, la02_c_descr) {
        document.form1.la02_i_codigo.value = la02_i_codigo;
        document.form1.la02_c_descr.value = la02_c_descr;
        db_iframe_lab_laboratorio.hide();
    }

    function pesquisaLaboratorio(lMostra) {
        if (lMostra == true) {
            js_OpenJanelaIframe('',
                'db_iframe_lab_laboratorio',
                'func_lab_laboratorio.php'
                + '&funcao_js=parent.mostralaboratorio1|la02_i_codigo|la02_c_descr',
                'Pesquisa',
                true
            );
        } else {
            if (document.form1.la02_i_codigo.value != '') {
                js_OpenJanelaIframe('',
                    'db_iframe_lab_laboratorio',
                    'func_lab_laboratorio.php?pesquisa_chave='
                    + document.form1.la02_i_codigo.value + '&funcao_js=parent.mostralaboratorio',
                    'Pesquisa',
                    false
                );
            } else {
                document.form1.la02_c_descr.value = '';
            }
        }
    }

    function pesquisaGrupoLiberacao(lMostra) {
        if (lMostra == true) {
            js_OpenJanelaIframe('',
                'db_iframe_gruposliberacao',
                'func_gruposliberacao.php'
                + '&funcao_js=parent.mostragrupoliberacao1|la71_sequencial|la71_descricao',
                'Pesquisa',
                true
            );
        } else {
            if (document.form1.la71_sequencial.value != '') {
                js_OpenJanelaIframe('',
                    'db_iframe_gruposliberacao',
                    'func_gruposliberacao.php?pesquisa_chave='
                    + document.form1.la71_sequencial.value + '&funcao_js=parent.mostragrupoliberacao',
                    'Pesquisa',
                    false
                );
            } else {
                document.form1.la71_descricao.value = '';
            }
        }
    }

    function mostragrupoliberacao1(la71_sequencial, la71_descricao) {
        document.form1.la71_sequencial.value = la71_sequencial;
        document.form1.la71_descricao.value = la71_descricao;
        db_iframe_gruposliberacao.hide();
    }

    function mostragrupoliberacao(la71_descricao, lErro) {
        document.form1.la71_descricao.value = la71_descricao;
        if (lErro == true) {
            document.form1.la71_sequencial.focus();
            document.form1.la71_sequencial.value = '';
        }
    }

    function pesquisaSetor(lMostra) {
        if (lMostra == true) {
            js_OpenJanelaIframe('',
                'db_iframe_lab_labsetor',
                'func_lab_labsetor.php?'
                + 'funcao_js=parent.mostralab_labsetor1|la24_i_setor|la23_c_descr|la24_i_codigo',
                'Pesquisa',
                true
            );
        } else {
            if (document.form1.la24_i_setor.value != '') {
                js_OpenJanelaIframe('',
                    'db_iframe_lab_labsetor',
                    'func_lab_labsetor.php?'
                    + 'pesquisa_chave=' + document.form1.la24_i_setor.value
                    + '&funcao_js=parent.mostralab_labsetor',
                    'Pesquisa',
                    false
                );
            } else {

                document.form1.la23_c_descr.value = '';
                document.form1.la24_i_codigo.value = '';
            }
        }
    }

    function mostralab_labsetor(la23_c_descr, lErro, la24_i_codigo) {
        document.form1.la23_c_descr.value = la23_c_descr;
        document.form1.la24_i_codigo.value = la24_i_codigo;
        if (lErro == true) {
            document.form1.la24_i_setor.focus();
            document.form1.la24_i_setor.value = '';
            document.form1.la24_i_codigo.value = '';
        }
    }

    function mostralab_labsetor1(la24_i_setor, la23_c_descr, la24_i_codigo) {
        document.form1.la24_i_setor.value = la24_i_setor;
        document.form1.la24_i_codigo.value = la24_i_codigo;
        document.form1.la23_c_descr.value = la23_c_descr;
        db_iframe_lab_labsetor.hide();
    }

    function pesquisaExame(lMostra) {
        let sUrl = 'func_lab_setorexame.php';
        let sFiltroSetor = '';
        if (document.form1.la24_i_setor.value != '') {
            sFiltroSetor = '&la24_i_codigo=' + document.form1.la24_i_codigo.value + '&';
        }

        if (lMostra == true) {
            sUrl += '?funcao_js=parent.mostralab_exame1|la08_i_codigo|la08_c_descr';
            sUrl += sFiltroSetor;
            js_OpenJanelaIframe('', 'db_iframe_lab_setorexame', sUrl, 'Pesquisa Exames', true);
        } else {
            if (document.form1.la09_i_exame.value != '') {
                sUrl += '?pesquisa_chave=' + document.getElementById('la09_i_exame').value;
                sUrl += '&funcao_js=parent.mostralab_exame';
                sUrl += sFiltroSetor;
                js_OpenJanelaIframe('', 'db_iframe_lab_setorexame', sUrl, 'Pesquisa Exames', false);
            } else {
                document.form1.la08_c_descr.value = '';
            }
        }
    }

    function mostralab_exame(la08_c_descr, lErro) {
        document.form1.la08_c_descr.value = la08_c_descr;
        if (lErro == true) {
            document.form1.la09_i_exame.focus();
            document.form1.la09_i_exame.value = '';
        }
    }

    function mostralab_exame1(la09_i_exame, la08_c_descr) {
        document.form1.la09_i_exame.value = la09_i_exame;
        document.form1.la08_c_descr.value = la08_c_descr;
        db_iframe_lab_setorexame.hide();
    }

    function confirma() {
        const params = validaDadosForm();

        if (!params) {
            return;
        }

        if (!confirm('Deseja confirmar a conferência de resultados?')) {
            return false;
        }

        var oAjaxRequest = new AjaxRequest('lab4_conferencia.RPC.php',
            {
                exec: 'salvarConferenciaLote',
                codigoLaboratorio: params.codigoLaboratorio,
                codigoSetor: params.codigoSetor,
                codigoExame: params.codigoExame,
                dataInicio: params.dataInicio,
                dataFim: params.dataFim,
                grupoLiberacao: params.grupoLiberacao
            },
            function (oRetorno, lErro) {
                alert(oRetorno.sMensagem.urlDecode());
                tabelaRequisicoes.bootstrapTable('destroy');
                document.getElementById('botaoConferir').style.display = 'none';
                return;
            });
        oAjaxRequest.setMessage('Conferindo...');
        oAjaxRequest.execute();
    }

    const tabelaRequisicoes = $('#tabelaRequisicoes');

    function getDadosTabela(parametrosPesquisa) {
        const params = validaDadosForm();

        if (!params) {
            return;
        }

        const oAjaxRequest = new AjaxRequest('lab4_conferencia.RPC.php',
            {
                exec: 'getRequisicoesExames',
                codigoLaboratorio: params.codigoLaboratorio,
                codigoSetor: params.codigoSetor,
                codigoExame: params.codigoExame,
                dataInicio: params.dataInicio,
                dataFim: params.dataFim,
                grupoLiberacao: params.grupoLiberacao,
                limit: parametrosPesquisa.data.limit,
                offset: parametrosPesquisa.data.offset
            },
            function (oRetorno, lErro) {
                document.getElementById('botaoConferir').style.display = 'none';

                if (oRetorno.iStatus == 2) {
                    alert(oRetorno.sMensagem.urlDecode());
                    tabelaRequisicoes.bootstrapTable('destroy');
                    return;
                }

                if (oRetorno.total > 0) {
                    document.getElementById('botaoConferir').style.display = 'block';
                }
                parametrosPesquisa.success(decodeDadosRequisicoes(oRetorno))
            });
        oAjaxRequest.setMessage('Buscando Requisições...');
        oAjaxRequest.execute();
    }

    function decodeDadosRequisicoes(dados) {
        let dadosDecodados = {
            total: dados.total,
            rows: []
        };

        for (const dado of dados.rows) {
            dadosDecodados.rows.push({
                requisicao: dado.requisicao,
                dataRequisicao: dado.dataRequisicao,
                nomePaciente: dado.nomePaciente.urlDecode(),
                nomeLaboratorio: dado.nomeLaboratorio.urlDecode(),
                listaExames: dado.listaExames.urlDecode()
            })
        }
        return dadosDecodados;
    }

    function validaDadosForm() {
        const codigoLaboratorio = document.getElementById('la02_i_codigo').value;
        const codigoSetor = document.getElementById('la24_i_setor').value;
        const codigoExame = document.getElementById('la09_i_exame').value;
        const dataInicio = document.getElementById('dData1').value;
        const dataFim = document.getElementById('dData2').value;
        const grupoLiberacao = document.getElementById('la71_sequencial').value;

        if (document.getElementById('dData1').value === '' || document.getElementById('dData2').value === '') {
            alert('Preencha o período.');
            return false;
        }

        const converteDataInicio = dataInicio.split('/');
        const converteDataFim = dataFim.split('/');
        const dInicio = new Date(converteDataInicio[2], converteDataInicio[1] - 1, converteDataInicio[0]);
        const dFim = new Date(converteDataFim[2], converteDataFim[1] - 1, converteDataFim[0]);

        if (dInicio > dFim) {
            alert('Data inicial é maior que a data final.');
            return false;
        }

        return {
            codigoLaboratorio: codigoLaboratorio,
            codigoSetor: codigoSetor,
            codigoExame: codigoExame,
            dataInicio: dataInicio,
            dataFim: dataFim,
            grupoLiberacao: grupoLiberacao
        }
    }

    function constroiTabelaRequisicoes(tabelaRequisicoes) {
        let tabela = {
            colunas: [
                {
                    field: 'requisicao',
                    title: 'Requisição',
                    width: 10,
                    widthUnit: '%',
                },
                {
                    field: 'nomePaciente',
                    title: 'Paciente',
                    width: 30,
                    widthUnit: '%'
                },
                {
                    field: 'nomeLaboratorio',
                    title: 'Laboratório',
                    width: 25,
                    widthUnit: '%'
                },
                {
                    field: 'dataRequisicao',
                    title: 'Data da Requisição',
                    width: 5,
                    widthUnit: '%'
                },
                {
                    field: 'listaExames',
                    title: 'Lista Exames',
                    width: 30,
                    widthUnit: '%'
                }
            ]
        }

        tabelaRequisicoes.bootstrapTable({
            columns: tabela.colunas
        })
    }

    function pesquisaRequisicoes() {
        if (!validaDadosForm()) {
            return;
        }
        tabelaRequisicoes.bootstrapTable('destroy');
        constroiTabelaRequisicoes(tabelaRequisicoes);
    }
</script>
