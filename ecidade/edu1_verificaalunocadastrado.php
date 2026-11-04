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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="ISO-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
          name="viewport">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputCpf.widget.js"></script>
</head>
<body class="body-default">
<div class="container">
    <fieldset>
        <legend>Inclusão de Aluno</legend>
        <table class="form-container" style="border-collapse: separate;">
            <tr>
                <td><label for="">CPF: &nbsp;</label></td>
                <td>
                    <input type="text" id="cpf" class="field-size3" />
                </td>
            </tr>
        </table>
    </fieldset>

    <button type="button" class="btn btn-light" id="btnPesquisar">
        <i class="fa fa-search"></i>
        Buscar Cadastro
    </button>
</div>
<div class="container" style="max-width: 500px; text-align: justify;">
    <fieldset>
        <p><b>Busca por CPF</b></p>
        <p>Prezado Cliente,<br/><br/> A fim de evitar o cadastro duplo no sistema, informe o CPF do aluno no campo.
            O sistema irá verificar se este já possui o cadastro.<br/><br/>
            Caso possua, será redirecionado para a tela de edição do cadastro existente.<br/><br/>
            Se o CPF informado não possuir nenhum cadastro anterior,
            o sistema permitirá realizar a inclusão de um novo cadastro para novos alunos.
        </p>
    </fieldset>
</div>

<script type="text/javascript">
    const inputCpf = document.getElementById("cpf");
    const btnPesquisar = document.getElementById("btnPesquisar");

    var cpfMask = new DBInputCpf(inputCpf);

    const pesquisarCpf = () => {
        if (!cpfMask.isValid() || empty(cpfMask.getValue())) {
            alert('CPF Inválido!');
            return;
        }

        var oParametro = {};
        oParametro.exec = 'buscarAlunoCpf';
        oParametro.iCpf = cpfMask.getValue();

        var oDadosRequest = {};
        oDadosRequest.methot = 'post';
        oDadosRequest.parameters = 'json='+Object.toJSON(oParametro);
        oDadosRequest.onComplete = (oResponse) => {
            js_removeObj("msgBox");
            const response = JSON.parse(oResponse.responseText);
            if (response.status === 2) {
                alert(response.message.urlDecode());
                return;
            }

            if (!response.aluno) {
                window.location.href = 'edu1_alunoabas001.php';
                return;
            }

            if (confirm('CPF já cadastrado, deseja alterar o cadastro do aluno?')) {
                window.location.href = 'edu1_alunoabas002.php?editarAluno=' + response.aluno;
            }
        };

        js_divCarregando("Aguarde, buscando os alunos de acordo com os campos informados.", "msgBox");
        new Ajax.Request('edu4_aluno.RPC.php', oDadosRequest );
    }

    btnPesquisar.addEventListener('click', () => {
        pesquisarCpf();
    });
</script>
</body>
</html>
