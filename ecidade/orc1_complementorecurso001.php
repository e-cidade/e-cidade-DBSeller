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
require_once(modification("libs/db" . "_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));

$iInstituicao = db_getsession("DB_instit");
$clorcfontes = new cl_orcfontes;
$clorcfontes->rotulo->label();

$db_opcao = 1;

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/arrays.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
    <form id="formComplementos" name="formComplementos">
        <fieldset>
            <legend>Cadastro de Complemento de Fonte de Recurso</legend>
            <table class="form-container">
                <tr>
                    <td><label for="codigo">Complemento:</label></td>
                    <td><input type="text" id="codigo" name="codigo" class="field-size2"></td>
                </tr>
                <tr>
                    <td><label for="descricao">Descrição:</label></td>
                    <td><input type="text" id="descricao" name="descricao" class="field-size8"/></td>
                </tr>
                <tr>
                    <td><label for="msc">Msc:</label></td>
                    <td>
                        <select id="msc" name="msc">
                            <option value="false">Não</option>
                            <option value="true">Sim</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><label for="tribunal">Tribunal de Contas:</label></td>
                    <td>
                        <select id="tribunal" name="tribunal">
                            <option value="false">Não</option>
                            <option value="true">Sim</option>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>
        <button type="button" id="salvar" name="salvar">
            <i class="far fa-save"></i>
            Salvar
        </button>

    </form>
</div>

<fieldset class="subcontainer" style="width: 1000px;">
    <legend>Complementos adicionados</legend>
    <div id="ctnComplementos"></div>
</fieldset>
</body>
</html>

<?php
db_menu();
?>
<script language="JavaScript" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">

    const sUrl = 'orc4_manutencaoRecurso.RPC.php';
    const rota = '/financeiro/orcamento/cadastro/complemento';

    const formulario = document.getElementById('formComplementos');
    const inputCodigo = document.getElementById('codigo');
    const inputDescricao = document.getElementById('descricao');
    const cboMsc = document.getElementById('msc');
    const cboTribunal = document.getElementById('tribunal');
    const btnSalvar = document.getElementById('salvar');

    const ctnComplementos = document.getElementById('ctnComplementos');

    const collection = new Collection();
    collection.setId('codigo');

    const grid = new DatagridCollection(collection).configure({
        order: false,
        height: 200
    });

    grid.addColumn('codigo', {"label": "Código", "width": '20%'});
    grid.addColumn('descricao', {"label": "Complemento", "width": '40%'});
    grid.addColumn('msc', {"label": "MSC", "width": '15%'}).transform((item, linha) => {
        return linha.msc ? 'Sim' : 'Não';
    });
    grid.addColumn('tribunal', {"label": "Tribunal de Contas", "width": '15%'}).transform((item, linha) => {
        return linha.tribunal ? 'Sim' : 'Não';
    });

    grid.addAction('Editar', 'Editar', (event, linha) => {
        inputCodigo.value = linha.codigo;
        inputDescricao.value = linha.descricao;
        cboMsc.value = linha.msc;
        cboTribunal.value = linha.tribunal;
    }, true, 'fa-edit');

    grid.addAction('Excluir', 'Excluir', (event, linha) => {
        const formData = new FormData();
        formData.append('codigo', linha.codigo);
        PHPSession.appendFormData(formData);

        HttpClient.post(PHPSession.requestApi + rota + '/excluir', {body: formData}).then(response => {
            alert(response.message);
            if (response.error) {
                return;
            }
            collection.remove(linha.codigo);
            grid.reload();
        });

    }, true, 'fa-trash');

    grid.show(ctnComplementos)

    window.addEventListener('load', () => {
        PHPSession.loadData().then(() => {
            HttpClient.get(PHPSession.requestApi + rota).then(response => {
                if (response.error) {
                    alert(response.message)
                    return;
                }

                grid.clear();
                collection.add(response.data);
                grid.reload();
            });
        });
    });

    btnSalvar.addEventListener('click', () => {

        if (inputCodigo.value === '') {
            alert('Você deve informar o código');
            return
        }

        const formData = new FormData(formulario);
        formData.set('msc', cboMsc.value == 'true' ? 1: 0);
        formData.set('tribunal', cboTribunal.value == 'true' ? 1: 0);

        PHPSession.appendFormData(formData);

        HttpClient.post(PHPSession.requestApi + rota + '/salvar', {body: formData}).then(response => {

            alert(response.message);
            if (response.error) {
                return;
            }
            collection.add(response.data);
            grid.reload();
            limparFormulario();
        });
    });



    const limparFormulario = () => {
        formulario.reset();
    };
</script>
