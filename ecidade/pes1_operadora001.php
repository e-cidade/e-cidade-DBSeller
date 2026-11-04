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
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInputCNPJ.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInputInteger.widget.js"></script>
</head>
<body>
<form class="container" id="formulario">
    <fieldset>
        <legend>Operadora de Plano de Saúde</legend>
        <table class="form-container">
            <tbody>
            <tr>
                <td>
                    <label for="cgm"><a id="ancoraOperadora" href="#">Operadora:</a></label>
                </td>
                <td>
                    <input type="text" id="cgm" name="cgm" class="field-size2" lang="z01_numcgm">
                    <input type="text" id="nome" name="nome" class="field-size8" lang="z01_nome" disabled>
                    <input type="hidden" id="sequencial" name="sequencial">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="cnpj">CNPJ:</label>
                </td>
                <td>
                    <input type="text" id="cnpj" name="cnpj" class="field-size4 readonly" lang="z01_cgccpf" disabled>
                </td>
            </tr>
            <tr title="Código de registro ANS">
                <td>
                    <label for="ans">ANS:</label>
                </td>
                <td>
                    <input type="text" id="ans" name="ans" class="field-size2" lang="ans" maxlength="6">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="ativo">Situação:</label>
                </td>
                <td>
                    <select id="ativo" name="ativo">
                        <option value="true">Ativa</option>
                        <option value="false">Inativa</option>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
    </fieldset>

    <input type="button" value="Salvar" id="salvar">
    <input type="reset" value="Novo" id="limpar">
</form>
<div class="container" style="width: 720px;">
    <fieldset>
        <legend>Operadoras Cadastradas</legend>
        <div id="cntGrid"></div>
    </fieldset>
</div>
<script rel="script" type="text/javascript">
    const formulario = document.querySelector('#formulario');
    const salvarButton = document.querySelector('#salvar');

    const cgm = document.querySelector('#cgm');
    const nome = document.querySelector('#nome');
    const sequencial = document.querySelector('#sequencial');
    const ativo = document.querySelector('#ativo');
    const inputCNPJ = new DBInputCNPJ($('cnpj'));
    const inputAns = new DBInputInteger($('ans'));
    const collection = new Collection().setId('sequencial');
    const gridOperadoras = DatagridCollection.create(collection).configure('order', false);

    const ancoraOperadora = new DBLookUp($('ancoraOperadora'), cgm, nome, {
        'sArquivo': 'func_cgmjuridico.php',
        'sObjetoLookUp': 'func_nome',
        'sLabel': 'Pesquisar Empresas',
        'aCamposAdicionais': ['z01_cgccpf']
    });

    ancoraOperadora.setCallBack('onClick', campos => {
        inputCNPJ.setValue(campos[2]);
    });

    ancoraOperadora.setCallBack('onChange', (erro, campos) => {

        inputCNPJ.setValue('');
        if (!erro) {
            inputCNPJ.setValue(campos[2].toString());
        }
    });

    const popularGrid = operadora => {
        collection.add({
            sequencial: operadora.sequencial,
            nome: operadora.cgm.nome,
            cnpj: operadora.cgm.cnpj,
            ans: operadora.ans,
            ativo: operadora.ativo,
            objeto: operadora
        });
    };

    const carregar = () => {
        const parametros = new FormData();
        parametros.append('acao', 'carregar');

        HttpClient.post('pes1_operadora.RPC.php', {body: parametros}).then(response => {
            if (response.erro) {
                return alert(response.mensagem);
            }

            response.operadoras.map(popularGrid);
            gridOperadoras.reload();
        });
    };

    const valido = () => {
        if (cgm.value.trim() === '') {
            return alert('O campo "Operadora" é obrigatório.');
        }

        if (document.querySelector('#ativo').value === '') {
            return alert('O campo "Situação" é obrigatório.');
        }

        return true;
    };

    const salvar = () => {
        if (valido() !== true) {
            return;
        }

        const parametros = new FormData(formulario);
        parametros.append('acao', 'salvar');
        parametros.append('ans', inputAns.getValue());

        HttpClient.post('pes1_operadora.RPC.php', {body: parametros}).then(response => {
            alert(response.mensagem);

            if (response.erro) {
                return;
            }

            popularGrid(response.operadora);

            gridOperadoras.reload();
            formulario.reset();
        });
    };

    gridOperadoras.addColumn('nome', {label: 'Nome', align: 'left', width: '300px'});
    gridOperadoras.addColumn('cnpj', {label: 'CNPJ', align: 'center', width: '140px'}).transform('cnpj');
    gridOperadoras.addColumn('ans', {label: 'ANS', align: 'center', width: '90px'});
    gridOperadoras.addColumn('ativo', {label: 'Situação', align: 'center', width: '80px'}).
        transform((valor, item) => {
            return item.ativo ? 'Ativa' : 'Inativa';
        });

    gridOperadoras.addAction('A', 'Alterar', (event, linha) => {
        cgm.value = linha.objeto.cgm.codigo;
        nome.value = linha.objeto.cgm.nome;
        sequencial.value = linha.sequencial;
        ativo.value = linha.ativo;
        inputCNPJ.setValue(linha.objeto.cgm.cnpj);
        inputAns.setValue(linha.ans);
    });

    gridOperadoras.show($('cntGrid'));
    salvarButton.addEventListener('click', salvar);
    carregar();

    document.querySelector('#formulario').addEventListener('reset', () => {
        Array.from(document.querySelector('#formulario').querySelectorAll('input[type="hidden"]')).forEach(elemento => {
            elemento.value = '';
        });
    });
</script>
<?php db_menu(); ?>
</body>
</html>
