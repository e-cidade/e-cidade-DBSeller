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

parse_str(base64_decode($HTTP_SERVER_VARS['QUERY_STRING']), $queryString);

foreach ($queryString as $key => $value) {
    ${$key} = $value;
}

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Pesquisar de Campos</title>
</head>
<body>
<form method="post" name="consulta">
    <table width="100%">
        <tr>
            <td>
                <select name="sel_cliente" size="10" width="150">
                    <?php
                    $result = db_query($conn, "
                        select c.nomecam,c.codcam
                        from db_syscampo c
                        inner join db_sysarqcamp a
                        on a.codcam = c.codcam
                        where a.codarq = $tabela
                        order by a.seqarq
                    ");
                    $num_linhas = pg_numrows($result);
                    for ($i = 0; $i < $num_linhas; $i++) {
                        $nome_campo = pg_fieldname($result, 0);
                        $nome_campo = "#" . trim(pg_result($result, $i, $nome_campo)) . "#";
                        echo "<option value=\"{$nome_campo}\">{$nome_campo}</option>\n";
                    }
                    ?>
                </select>
                <br>
                <input type="submit" name="enviar" value="Enviar"
                       onclick="retorna(document.consulta.sel_cliente.options[document.consulta.sel_cliente.selectedIndex].value,'$camp','$tab')">
                <input type="button" name="cancelar" value="Cancelar" onclick="cancela()">
                <script>
                    function retorna(valor) {
                        if (!valor)
                            alert('Selecione algum campo');
                        else {
                            aux = String(parent.document.forms[0].alt_ind.value);
                            if (aux.indexOf(valor) != -1)
                                alert('Este valor ja existe');
                            else {
                                valor = valor + '\n';
                                parent.document.forms[0].alt_ind.value = parent.document.forms[0].alt_ind.value +
                                    valor;
                                parent.db_iframe_campo.hide();
                            }
                        }
                    }

                    function cancela() {
                        parent.db_iframe_campo.hide();
                    }
                </script>
            </td>
        </tr>
    </table>
</form>
</body>
</html>
