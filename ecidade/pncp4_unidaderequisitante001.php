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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="estilos.css" rel="stylesheet">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>
<body>
<div id="alert" class="alert-info" role="alert" style="text-align: center;" hidden></div>
<div class="container">
    <fieldset>
        <legend>Configuração Parametro</legend>
        <table class="form-container">
            <tr>
                <td>
                    <label for="bloquear">Bloquear Unidade Requisitante</label>
                </td>
                <td>
                    <select id="bloquear" name='bloquear'>
                        <option value="t">SIM</option>
                        <option value="f" selected>NÃO</option>
                    </select>
                </td>
            </tr>
        </table>
    </fieldset>
    <button type="button" id="btnSalvar">
        <i class="fas fa-save"></i>
        Salvar
    </button>
</div>
</div>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script>
    var apiUrl = "";

    window.addEventListener('load', async () => {
        var apiUrl;
        await PHPSession.loadData().then(() => {
            apiUrl = PHPSession.requestApi;
        });

        const btnSalvar = document.getElementById('btnSalvar');
        const habilitarPncp = document.getElementById('bloquear');
        const routes = {
            habilitar: `${apiUrl}/patrimonial/pncp/integracao/bloquear-unidade-requisitante/`,
        }

        function verificaEnteAutorizado() {
            const formData = new FormData();
            formData.append('verificar', true);
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.habilitar, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }

                if (response.data[0].pn07_habilitado) {
                    habilitarPncp.value = "t";
                    habilitarPncp.disabled = true;
                    btnSalvar.disabled = true;
                }
            })
        }

        verificaEnteAutorizado();
        btnSalvar.addEventListener('click', () => {
            const formData = new FormData();
            formData.append('bloquear', habilitarPncp.value);
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.habilitar, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                verificaEnteAutorizado();
                alert("Unidade Requisitante bloqueada por departamento");
            })
        })
    })
</script>
</body>
