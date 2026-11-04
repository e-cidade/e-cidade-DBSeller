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
        <legend>Orçamento por recurso</legend>

        <?php require_once modification('forms/db_frmfiltrorecursos.php') ?>

    </fieldset>
    <button type="button" name="imprime" id="imprime" class="btn btn-light">
        <i class="fas fa-print"></i>
        Emitir Relatório
    </button>
</div>
</body>

<script>

    PHPSession.loadData().then(() => {
        buscaRecursos(PHPSession.getValueSession('DB_anousu'));
    });

    document.getElementById('imprime').addEventListener('click', () => {
        let recursos = collectionRecurso.build();
        if (recursos.lenght === 0) {
            alert('Selecione ao menos um recurso.')
            return;
        }

        let ids = recursos.map((recurso) => {
            return recurso.codigo
        }).join(',');

        let url = `orc2_orcamentoporecurso002.php?recursos=${ids}`
        window.open(url, '', 'scrollbars=1,location=0');
    });
</script>
</html>
