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
    <script type='text/javascript' src='scripts/prototype.js'></script>
    <script type='text/javascript' src='scripts/widgets/DBToggleList.widget.js'></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>

</head>
<body>
<div class="container">
    <fieldset>
        <legend>Ata de Resultados Finais - SIGA</legend>
        <table class="form-container">
            <tr>
                <td style="width: 75px"><label for="calendario">Calendário: </label></td>
                <td class="field-size5">
                    <select id="calendario" name="calendario">
                        <option value="">Selecione</option>
                    </select>
                </td>
            </tr>
        </table>
        <div id="ctnTurmas">
            <fieldset class="separator">
                <legend>Turmas</legend>
                <div id="listaTurmas"></div>
            </fieldset>
        </div>
        <br>
        <button id="btnGerar" class="btn btn-light">
            <i class="fa fa-print"></i>
            Imprimir
        </button>
    </fieldset>
</div>

<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    const escola = <?=db_getsession('DB_coddepto')?>;
    let urlApiEscola = '';

    const cboCalendario = document.getElementById('calendario');
    const ctnTurmas = document.getElementById('ctnTurmas');
    const btnGerar = document.getElementById('btnGerar');
    const listaTurmas = document.getElementById('listaTurmas');
    const oToggleTurmas = new DBToggleList([{'sId': "sTurma", 'sLabel': "Turma"}]);
    oToggleTurmas.closeOrderButtons();
    oToggleTurmas.show(listaTurmas);

    PHPSession.loadData().then(() => {
        urlApiEscola = PHPSession.requestApi + '/educacao/escola';
        const formData = new FormData();
        PHPSession.appendFormData(formData);
        HttpClient.get(`${urlApiEscola}/calendario/${escola}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            const calendarios = response.data;
            calendarios.forEach(calendario => {
                cboCalendario.add(new Option(calendario.nome, calendario.codigo));
            });
        });
    });

    cboCalendario.addEventListener('change', () => {
        const calendario = cboCalendario.value;
        if (calendario === '') {
            oToggleTurmas.clearAll();
            return;
        }
        HttpClient.get(`${urlApiEscola}/turmas?calendario=${calendario}&tipoTurma=7`).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            const turmas = response.data;
            oToggleTurmas.clearAll();
            turmas.forEach(turma => {
                oToggleTurmas.addSelect({
                    'iTurmaEtapa': turma.turmaEtapaCodigo,
                    'sTurma': `${turma.turmaDescricao} - ${turma.etapaDescricao}`,
                });
            });
            oToggleTurmas.renderRows();
        });
    });

    btnGerar.addEventListener('click', function () {
        if (cboCalendario.value === '') {
            alert('Selecione um calendário');
            return;
        }
        if (oToggleTurmas.getSelected().length === 0) {
            alert('Selecione uma turma');
            return;
        }

        const formData = new FormData();
        PHPSession.appendFormData(formData);
        let turmasEtapas = [];
        oToggleTurmas.getSelected().forEach(turma => {
            turmasEtapas.push(turma.iTurmaEtapa);
        });

        formData.append('turmasEtapas', turmasEtapas.join(','));
        HttpClient.post(`${urlApiEscola}/relatorios/ata-resultados-finais-siga`, {body: formData}).then((response) => {
            if (response.error) {
                alert(response.message);
                return;
            }
            window.open(response.data.path);
        });
    })
</script>
</body>
</html>
