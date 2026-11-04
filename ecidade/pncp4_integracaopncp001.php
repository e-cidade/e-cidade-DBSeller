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
$dados = InstituicaoRepository::getInstituicaoByCodigo(db_getsession('DB_instit'));
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
        <legend>Configuração PNCP</legend>
        <input id="id" hidden>
        <table class="form-container">
            <tr>
                <td>
                    <label for="habilitar_pncp">Habilitar Integração com o PNCP:</label>
                </td>
                <td>
                    <select id="habilitar_pncp" name='habilitar_pncp'>
                        <option value="habilitar">SIM</option>
                        <option value="desabilitar" selected>NÃO</option>
                    </select>
                </td>
            </tr>
            <tr>
                <input type="hidden" id="documento" value="<?= $dados->getCNPJ() ?>">
            </tr>
        </table>
    </fieldset>
    <button type="button" id="btn_salvar">
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

        const btnSalvar = document.getElementById('btn_salvar');
        const habilitarPncp = document.getElementById('habilitar_pncp');
        const documento = document.getElementById('documento');
        const formData = new FormData();
        const routes = {
            habilitar: `${apiUrl}/patrimonial/pncp/integracao/habilitar/`,
            verificaIntegracao: `${apiUrl}/patrimonial/pncp/integracao/verificaIntegracao/`
        }

        function verificaEnteAutorizado() {
            formData.append('documento', documento.value);

            PHPSession.appendFormData(formData);

            HttpClient.post(routes.verificaIntegracao, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }

                if (response.data !== null) {
                    habilitarPncp.value = "habilitar";
                    habilitarPncp.disabled = true;
                    btnSalvar.disabled = true;
                }
            })
        }

        verificaEnteAutorizado();
        btnSalvar.addEventListener('click', () => {
            formData.append('habilitar_pncp', habilitarPncp.value);
            formData.append('documento', documento.value);

            PHPSession.appendFormData(formData);
            HttpClient.post(routes.habilitar, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                verificaEnteAutorizado();
                alert(response.message);
            })
        })
    })
</script>
</body>
