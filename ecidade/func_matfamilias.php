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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_pcdotac_classe.php"));
?>
<html>

<head>
    <title>Busca Famílias</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <style>
        td {
            padding: 0;
        }
    </style>
</head>

<body>
<div class="container">
    <form name="form2" method="post">
        <table class="form-container">
            <tr>
                <td>
                    <label for="m105_sequencial">Sequencial:</label>
                </td>
                <td>
                    <?php db_input("m105_sequencial", 20, '', false); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="m105_descricao">Descrição:</label>
                </td>
                <td>
                    <?php db_input("m105_descricao", 20, '', false); ?>
                </td>
        </table>

        <div class="subcontainer" style="margin-top:5px">
            <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
            <input name="limpar" type="reset" id="limpar" value="Limpar">
            <input name="Fechar" type="button" id="fechar" value="Fechar"
                   onClick="parent.db_iframe_matfamilias.hide();">
        </div>
    </form>
</div>
<div class="container">

    <?php

    $condicoes = [];
    if (empty($pesquisa_chave)) {
        if (!empty($m105_sequencial)) {
            $condicoes[] = "m105_sequencial = $m105_sequencial";
        }

        if (!empty($m105_descricao)) {
            $condicoes[] = "m105_descricao = '$m105_descricao'";
        }

        $sql = "SELECT * FROM matfamilias";
        if (!empty($condicoes)) {
            $sql .= 'WHERE ' . implode(' AND ', $condicoes);
        }

        db_lovrot($sql, 15, '()', '', $funcao_js, null, 'NoMe', [], false);
    }
    ?>
</div>

<script>
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
</body>
</html>
