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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <title>DBSeller Informática Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link rel="stylesheet" type="text/css"
          href="./extension/package/Desktop/assets/vendors/select2/css/select2.min.css"/>
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
</head>
<body>
<div class="container">
    <fieldset>
        <legend>Cancelar Designação - Transposição</legend>
        <table class="form-container">
            <tr>
                <td>
                    <?php
                    db_ancora("<b>Aluno:</b>", "js_pesquisaAluno(true)", 1);
                    ?>
                </td>
                <td>
                    <?php
                    db_input("ed47_i_codigo", 10, $Ied47_i_codigo, "text", true, 1, "onchange='js_pesquisaAluno(false)'");
                    db_input("ed47_v_nome", 30, $Ied47_v_nome, "text", true, 3);
                    ?>
                </td>
            </tr>
            <tr>
                <td><label for="ano">Ano transposição: </label></td>
                <td>
                    <input type="text" id="ano" class="field-size2" value="" disabled />
                </td>
            </tr>
            <tr>
                <td><b>Escola de origem:</b></td>
                <td><input type="text" id="escolaOrigem" class="field-size7" value="" disabled /></td>
            </tr>
            <tr>
                <td><b>Etapa de Origem:</b></td>
                <td><input type="text" id="etapaOrigem" class="field-size7" value="" disabled /></td>
            </tr>
            <tr>
                <td><b>Escola de designação: &nbsp;</b> </td>
                <td><input type="text" id="escolaDesignacao" class="field-size7" value="" disabled /></td>
            </tr>
            <tr>
                <td><b>Etapa designada:</b></td>
                <td><input type="text" id="etapaDesignada" class="field-size7" value="" disabled /></td>
            </tr>
        </table>
    </fieldset>
    <button type="button" class="btn btn-light" id="btnCancelar" disabled>
        <i class="fa fa-ban"></i>
        Cancelar
    </button>
</div>

<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    var urlApi;
    window.addEventListener('load', () => {
        PHPSession.loadData().then(() => {
            urlApi = PHPSession.requestApi;
        });
    });

    const anoTransposicao = document.getElementById('ano');
    const escolaOrigem = document.getElementById('escolaOrigem');
    const etapaOrigem = document.getElementById('etapaOrigem');
    const escolaDesignacao = document.getElementById('escolaDesignacao');
    const etapaDesignada = document.getElementById('etapaDesignada');

    const ancoraAluno = document.getElementById('ancoraAluno');
    const codigoAluno = document.getElementById('ed47_i_codigo');
    const labelAluno = document.getElementById('ed47_v_nome');

    function js_pesquisaAluno(mostra) {
        if (mostra) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_aluno',
                'func_aluno.php?funcao_js=parent.js_mostraaluno1|ed47_i_codigo|ed47_v_nome',
                'Pesquisar Alunos',
                true
            );
        } else {
            if (codigoAluno.value != "") {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_aluno',
                    'func_aluno.php?funcao_js=parent.js_mostraaluno2&pesquisa_chave2='+$F('ed47_i_codigo'),
                    'Pesquisar Alunos',
                    false
                );
            } else {
                limpaTela();
            }
        }
    }

    function js_mostraaluno1(chave1,chave2) {
        codigoAluno.value = chave1;
        labelAluno.value   = chave2;
        db_iframe_aluno.hide();
        buscarAlunoDesignado();
    }

    function js_mostraaluno2(chave1, lErro) {
        labelAluno.value   = chave1;
        if (lErro) {
            labelAluno.value  = "Chave ("+codigoAluno.value+") Não encontrada";
            codigoAluno.value = '';
            return;
        }
        buscarAlunoDesignado();
    }

    const buscarAlunoDesignado = () => {
        const formData = new FormData();
        formData.append('codigoAluno', codigoAluno.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/educacao/escola/transposicao/aluno-designado`, { body: formData })
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                anoTransposicao.value = response.data.anoTransposicao;
                escolaOrigem.value = response.data.escolaOrigem;
                etapaOrigem.value = response.data.etapaOrigem;
                escolaDesignacao.value = response.data.escolaDesignacao;
                etapaDesignada.value = response.data.etapaDesignada;
                btnCancelar.disabled = false;
            });
    }

    const limpaTela = () => {
        codigoAluno.value = "";
        labelAluno.value = "";
        anoTransposicao.value = "";
        escolaOrigem.value = "";
        etapaOrigem.value = "";
        escolaDesignacao.value = "";
        etapaDesignada.value = "";
        btnCancelar.disabled = true;
    }

    btnCancelar.addEventListener('click', () => {
        const formData = new FormData();
        formData.append('codigoAluno', codigoAluno.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/educacao/escola/transposicao/cancelar-designacao`, { body: formData })
            .then((response) => {
                alert(response.message);
                if (response.error) {
                    return;
                }
                limpaTela();
            });
    });
</script>
</body>
</html>

