<?php

/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBseller Servicos de Informatica
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

require_once(modification('libs/db_stdlib.php'));
require_once(modification('libs/db_' . 'conecta.php'));

?>

<html>

<head>
    <meta charset="utf-8">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link rel="stylesheet" type="text/css" href="estilos/grid.style.css">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/DBViewTipoFiltrosFolha.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
</head>

<body class="body-default">
    <div class="container">
        <form action="" method="post">
            <fieldset>
                <legend>Relatório Da Infraestrutura Da Escola</legend>
            </fieldset>

            <input name="processar" type="button" id="processar" value="Processar" onclick="emitir()">
        </form>
    </div>

    <script rel="script" type="text/javascript">
        function emitir() {

            js_divCarregando('Emitindo CSV', 'loading_message');

            fetch('edu2_relatorioinfradaescolaprocessamento001.php', {
                method: 'POST',
                credentials: 'include',
            }).then(response => response.json()).then(response => {
                if (response.erro) {
                    return alert(response.mensagem);
                }

                const download = new DBDownload();
                download.addFile(response.arquivo, response.nomeArquivo);
                download.show();
            }).finally(() => js_removeObj('loading_message'));
        }
    </script>
</body>

</html>
