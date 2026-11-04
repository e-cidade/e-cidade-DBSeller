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
require_once modification('libs/db_conecta.php');
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>

</head>
<body>
<script type="text/javascript" src="scripts/session.js"></script>
<div class="container">
    <fieldset>
        <legend>Planejamento por recurso</legend>
        <table class="form-container">
            <tr class="text-left">
                <td><label class="bold" for="planejamento">Planejamento:</label></td>
                <td>
                    <select id="planejamento" class="field-size8">
                        <option value="">Selecione um plano</option>
                    </select>
                </td>
            </tr>
        </table>
        <?php require_once modification('forms/db_frmfiltrorecursos.php') ?>

    </fieldset>
    <button type="button" name="imprime" id="imprime" class="btn btn-light">
        <i class="fas fa-print"></i>
        Emitir Relatório
    </button>
</div>


</body>
<script rel="script" type="text/javascript" src="scripts/classes/planejamento/planejamento.js"></script>
<script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<script>
    const planejamento = new Planejamento(document.getElementById('planejamento'));
    planejamento.setCallbackChange((e) => {
        if (e.target.value === '') {
            return;
        }

        buscaRecursos(planejamento.getPlano().pl2_ano_inicial);
    });

    PHPSession.loadData().then(() => {
        planejamento.load();
    });


    const validar = () => {
        try {
            if (planejamento.getValue() === '') {
                throw 'Selecione o planejamento.';
            }

            if (collectionRecurso.build().length === 0) {
                throw 'Selecione ao menos um recurso.';
            }
        } catch (e) {
            alert(e)
            return false;
        }
        return true;
    }
    document.getElementById('imprime').addEventListener('click', () => {
        const formData = new FormData();
        formData.append('planejamento_id', planejamento.getValue());
        PHPSession.appendFormData(formData);

        collectionRecurso.build().each((recurso) => {
            formData.append('orctiporec_id[]', recurso.codigo);
        });

        let rotaRelatorio = 'financeiro/planejamento/relatorios/planejamento-por-recurso';
        HttpClient.post(`${PHPSession.requestApi}/${rotaRelatorio}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            const download = new DBDownload();
            download.addFile(response.data.pdf, response.message);
            download.show();
        });
    });
</script>
</html>
