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
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container">
    <fieldset>
        <legend>Parâmetros de Bloqueio de Notas</legend>
        <table class="form-container">
            <tr>
                <td><label for="prazo-dias">Prazo em Dias para Bloqueio: </label></td>
                <td><input type="text" name="prazo-dias" id="prazo-dias" /></td>
            </tr>
            <tr>
                <td><label for="tipo-bloqueio">Tipo de Bloqueio: </label></td>
                <td>
                    <select name="tipo-bloqueio" id="tipo-bloqueio">
                        <option value="1">Sem bloqueio</option>
                        <option value="2">Notas parciais</option>
                        <option value="3">Notas do Diário</option>
                        <option value="4">Todos</option>
                    </select>
                </td>
            </tr>
        </table>
        <br/>
        <button type="button" class="btn btn-light" id="btnSalvar">
            <i class="fas fa-save"></i>
            Salvar
        </button>
    </fieldset>
</div>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    let urlApi = "";
    let codigoParamentros = null;
    PHPSession.loadData().then(() => {
        urlApi = PHPSession.requestApi;
        buscarParametros();
    });

    const inputPrazoDias = document.getElementById('prazo-dias');
    const selectTipoBloqueio = document.getElementById('tipo-bloqueio');
    const btnSalvar = document.getElementById('btnSalvar');

    const buscarParametros = () => {
        HttpClient.get(`${urlApi}/educacao/secretaria/parametros/bloqueio-notas/buscar`)
            .then((response) => {
                if (response.error) {
                    alert('Erro ao buscar parâmetros');
                    return;
                }

                codigoParamentros = response.data.ed362_codigo;
                inputPrazoDias.value = response.data.ed362_prazodias;
                selectTipoBloqueio.value = response.data.ed362_tipobloqueio;
            });
    }

    btnSalvar.addEventListener('click', () => {
        const formData = new FormData();
        formData.append('mo632_codigo', codigoParamentros);
        formData.append('mo632_prazodias', inputPrazoDias.value);
        formData.append('mo632_tipobloqueio', selectTipoBloqueio.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/educacao/secretaria/parametros/bloqueio-notas/salvar`, {body: formData})
            .then((response) => {
                alert(response.message);
                if (response.error) {
                    return;
                }
                buscarParametros();
            });
    });
</script>
</body>
</html>
