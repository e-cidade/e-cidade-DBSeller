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

include(modification("libs/db_stdlibwebseller.php"));
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_turmaachorario_classe.php"));
include(modification("dbforms/db_funcoes.php"));
    $escola = db_getsession("DB_coddepto");
    $clturmaachorario = new cl_turmaachorario;
?>
<html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <script type="text/javascript" src="scripts/scripts.js"></script>
        <script type="text/javascript" src="scripts/classes/http/http.js"></script>
        <link href="estilos.css" rel="stylesheet" type="text/css">
    </head>

    <body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
        <div class="container">
            <fieldset>
                <legend>Turmas AEE</legend>
                <div>
                    <label for="calendario"><b>Calendário: </b></label>
                    <input type="hidden" id="escola" value="<?=$escola?>">
                    <select name="calendario" id="calendario"class="field-size4">
                        <option value="">Selecione</option>
                    </select>
                    <label for="turma"><b>Turmas: </b></label>
                    <select name="turma" id="turma" class="field-size4">
                        <option value="">Selecione</option>
                    </select>
                </div>
            </fieldset>
            <button id="emitir">Emitir</button>
        </div>
    </body>
    <?php db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));?>
</html>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script>
    var apiUrl = "";
    window.addEventListener('load', async () =>{
        var apiUrl;
        await PHPSession.loadData().then(() => {
            apiUrl = PHPSession.requestApi;
        });

        const selectCalendario = document.querySelector('#calendario');
        const selectTurma = document.querySelector('#turma');
        const btnEmitir = document.querySelector('#emitir');
        const escola = document.querySelector('#escola').value;
        var calendarios = [];
        const routes = {
            calendario: `${apiUrl}/educacao/escola/${escola}/calendario`,
            turmas: `${apiUrl}/educacao/escola/turmasEspeciais/`,
            emitirRelatorioTurmasAee: `${apiUrl}/educacao/escola/relatorios/emitirRelatorioTurmasAee/`
        }

        HttpClient.get(routes.calendario).then(response => {
            calendarios = response.data;
            response.data.map(calendario => {
                selectCalendario.add(new Option(calendario.nome, calendario.codigo));
            })
        });

        selectCalendario.addEventListener('change', event => {
            selectTurma.innerHTML = '<option value="">Selecione</option>';
            var formData = new FormData();
            formData.append('calendario', selectCalendario.value);
            formData.append('escola', escola);
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.turmas, {body: formData}).then(response => {
                response.data.map(turma => {
                    selectTurma.add(new Option(turma.descricao, turma.id));
                })
            })
        })

        btnEmitir.addEventListener('click', event => {
            event.preventDefault();
            if (selectCalendario.value == "") {
                alert('Selecione um calendário!');
                return;
            }

            if (selectTurma.value == "") {
                alert('Selecione uma turma!');
                return;
            }

            var formData = new FormData();
            formData.append('turma', selectTurma.value);
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.emitirRelatorioTurmasAee, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                window.open(response.data);
            })
        })
    })
</script>
