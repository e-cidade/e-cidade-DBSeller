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

require_once(modification("fpdf151/pdfwebseller.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("std/DBLargeObject.php"));
?>
<html>
<head>
    <title>DBSeller Informática Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link rel="stylesheet" type="text/css" href="assets/fontawesome/css/all.min.css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/arrays.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>
<body class="body-default">
<div class="container">
    <form id="frmDeparaDisciplina" method="post" action="">

        <fieldset>
            <legend>Definir Procedimento por Disciplina na Base Curricular</legend>
            <div class="alert alert-warning text-left" role="alert">
                Você deve escolher procedimentos com o mesmo número de elementos de avaliação para as disciplinas da etapa.
            </div>
            <table class="form-container">
                <tr>
                    <td><label for="baseCurricular">Base:</label></td>
                    <td>
                        <select id="baseCurricular" name="baseCurricular">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="etapa">Etapa:</label></td>
                    <td>
                        <select id="etapa" name="etapa">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
            </table>
            <fieldset>
                <legend>Disciplinas</legend>
                <div id="ctnGridDisciplinas" style="width: 700px;"></div>
            </fieldset>

        </fieldset>
        <button id="btnSalvar" type="button">
            <i class="fas fa-save"></i>
            Salvar
        </button>
    </form>
</div>
</body>

<script type="text/javascript">
    const baseCurricularSelect = document.getElementById('baseCurricular');
    const etapaSelect = document.getElementById('etapa');
    const btnSalvar = document.getElementById('btnSalvar');
    const containerGrig = document.getElementById('ctnGridDisciplinas');

    const selectProcedimento = document.createElement('select');
    const procedimentos = [];

    const collectionDisciplinas = new Collection();
    collectionDisciplinas.setId('codigo');

    var gridDisciplinas = new DatagridCollection(collectionDisciplinas).configure({
        order: false,
        height: 200,
     });

    gridDisciplinas.addColumn('disciplina', {'label': 'Disciplina'}).transform(function (item, linha) {
        return linha.disciplina.nome;
    });

    gridDisciplinas.addColumn('procedimentoAvaliacao', {'label': 'Procedimento de Avaliação', width: '50%'})
        .transform(function (item, linha) {
        selectProcedimento.setAttribute('id', `selectProcedimento_${linha.codigo}`);
        return selectProcedimento.outerHTML;
    });

    gridDisciplinas.show(containerGrig);

    gridDisciplinas.setEvent('onafterrenderrows', function() {
        collectionDisciplinas.get().map(function (linha)  {
            if (typeof linha.procedimento === 'object') {
                $(`selectProcedimento_${linha.codigo}`).value = linha.procedimento.codigo;
            }
        });
    });

    baseCurricularSelect.addEventListener('change', (e) => {
        etapaSelect.options.length = 0;
        etapaSelect.add(new Option('Selecione', ''));

        if (e.target.value == '') {
            etapaSelect.dispatchEvent(new Event('change'));
            return;
        }

        const formData = new FormData();
        formData.append('acao', 'buscarEtapas');
        formData.append('codigoBase', e.target.value);
        HttpClient.post('edu1_base_procedimento.RPC.php', {body: formData}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            response.etapas.map(function (etapa) {
                etapaSelect.add(new Option(etapa.descricao, etapa.codigo));
            });
        });
    });

    etapaSelect.addEventListener('change', (e) => {
        if (empty(e.target.value)) {
            gridDisciplinas.clear();
            gridDisciplinas.reload();
            return;
        }
        const formData = new FormData();
        formData.append('acao', 'buscarDisciplinas');
        formData.append('codigoBase', baseCurricularSelect.value);
        formData.append('codigoEtapa', e.target.value);
        HttpClient.post('edu1_base_procedimento.RPC.php', {body: formData}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            gridDisciplinas.clear();

            response.disciplinas.map(function (disciplina) {
                collectionDisciplinas.add(disciplina);
            });

            gridDisciplinas.reload();
        });
    });

    const criarSelect = (procedimentos) => {
        selectProcedimento.options.length = 0;
        selectProcedimento.add(new Option('Selecione', ''));

        procedimentos.map(function (procedimento) {
            selectProcedimento.add(new Option(procedimento.descricao, procedimento.codigo));
        });
    };

    (function () {
        const formData = new FormData();
        formData.append('acao', 'buscarBases');
        HttpClient.post('edu1_base_procedimento.RPC.php', {body: formData}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            response.bases.map(function (base) {
                baseCurricularSelect.add(new Option(base.descricao, base.codigo));
            });
        });

        const formData2 = new FormData();
        formData2.append('acao', 'buscarProcedimentos');
        HttpClient.post('edu1_base_procedimento.RPC.php', {body: formData2}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            response.procedimentos.map(function (procedimento) {
                procedimentos.push(procedimento);
            });
            criarSelect(procedimentos);
        });
    })();


    btnSalvar.addEventListener('click', function () {
        const disciplinas = collectionDisciplinas.build();

        disciplinas.map(function (disciplina) {
            disciplina.procedimentoEscolhido = $(`selectProcedimento_${disciplina.codigo}`).value
        });



        const formData = new FormData();
        formData.append('acao', 'atualizarProcedimentoDisciplina');
        formData.append('disciplinas', JSON.stringify(disciplinas));

        HttpClient.post('edu1_base_procedimento.RPC.php', {body: formData}).then(response => {
            alert(response.mensagem);
            if (response.erro) {
                return;
            }

        });
    });
</script>
</html>
