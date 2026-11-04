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
    <title>Busca de publicações pncp</title>
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
                    <label for="pn03_liclicita">Código licitação:</label>
                </td>
                <td>
                    <?php db_input("pn03_liclicita", 20, '', false); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="pn03_solicita">Código solicitação:</label>
                </td>
                <td>
                    <?php db_input("pn03_solicita", 20, '', false); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="l20_numero">Número:</label>
                </td>
                <td>
                    <?php db_input("l20_numero", 20, '', false); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="pn03_ano">Ano:</label>
                </td>
                <td>
                    <?php db_input("pn03_ano", 20, '', false); ?>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="l03_codcom">Tipo Compra:</label>
                </td>
                <td>
                    <?php
                    $pcTipoCompra = new \App\Domain\Patrimonial\Compras\Models\TipoCompra();
                    $tiposCompraLicitacao = $pcTipoCompra
                        ->orderBy('pc50_descr')
                        ->pluck('pc50_descr', 'pc50_codcom')
                        ->all();

                    $listaTiposCompra = ['Todos'];
                    $listaTiposCompra += $tiposCompraLicitacao;

                    db_select('l03_codcom', $listaTiposCompra, true, 2);
                    ?>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="pn02_unidade">Unidade Compradora:</label>
                </td>
                <td>
                    <?php
                    $unidadesPncp = new \App\Domain\Patrimonial\PNCP\Models\UnidadesPNCP();
                    $resultadoUnidadesPncp = $unidadesPncp
                        ->orderBy('pn02_nome')
                        ->pluck('pn02_nome', 'pn02_unidade')
                        ->all();

                    $listaUnidadesPncp = ['Todas'];
                    $listaUnidadesPncp += $resultadoUnidadesPncp;
                    db_select('pn03_unidade', $listaUnidadesPncp, true, 2);
                    ?>
                </td>
            </tr>
        </table>

        <div class="subcontainer" style="margin-top:5px">
            <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
            <input name="limpar" type="reset" id="limpar" value="Limpar">
            <input name="Fechar" type="button" id="fechar" value="Fechar"
                   onClick="parent.db_iframe_publicacoespncp.hide();">
        </div>
    </form>
</div>
<div class="container">

    <?php

    $condicoes = [];
    $condicoes[] = 'pn03_instituicao = ' . db_getsession('DB_instit');

    if (empty($pesquisa_chave)) {
        if (!empty($pn03_unidade)) {
            $condicoes[] = "pn03_unidade = '$pn03_unidade'";
        }

        if (!empty($pn03_solicita)) {
            $condicoes[] = "pn03_solicita = '$pn03_solicita'";
        }

        if (!empty($pn03_liclicita)) {
            $condicoes[] = "pn03_liclicita = '$pn03_liclicita'";
        }

        if (!empty($l20_numero)) {
            $condicoes[] = "l20_numero = '$l20_numero'";
        }

        if (!empty($pn03_ano)) {
            $condicoes[] = "pn03_ano = '$pn03_ano'";
        }

        if (!empty($l03_codcom)) {
            $condicoes[] = "l03_codcom = '$l03_codcom'";
        }

        $sql = "
            SELECT
                pn03_codigo, pn03_liclicita, pn03_solicita, l20_numero, pn03_ano, pc50_descr,
                l08_descr as l20_licsituacao, pn03_numero, pn02_nome, pn03_datapublicacao
            FROM compraspncp
            JOIN unidadespncp ON pn02_unidade = pn03_unidade
            LEFT JOIN liclicita ON l20_codigo = pn03_liclicita
            LEFT JOIN cflicita ON l20_codtipocom = l03_codigo
            LEFT JOIN licsituacao ON l08_sequencial = l20_licsituacao
            LEFT JOIN pctipocompra ON pc50_codcom = l03_codcom
        ";

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
