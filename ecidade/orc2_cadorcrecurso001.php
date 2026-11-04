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
?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>

    <script>
        function js_emite() {
            let sParam = document.getElementById('imprec').value;
            let finalidade = document.getElementById('finalidade').value;
            let url = `orc2_cadorcrecurso002.php?recurso=${sParam}&finalidade=${finalidade}`
            jan = window.open(url, '', 'scrollbars=1,location=0 ');
            jan.moveTo(0, 0);
        }
    </script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="container">


<form name="form1" method="post" action="">
    <fieldset>
        <legend>Relatório Cadastral de Recursos</legend>

        <table class="form-container">
            <tr>
                <td>Impressão por Recurso:</td>
                <td title="Forma de impressão">
                    <?php
                    $x = array("t" => "Todos", "sa" => "Somente Ativos", "si" => "Somente Inativos");
                    db_select('imprec', $x, true, 4, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td>Imprimir Finalidade:</td>
                <td>
                    <select id="finalidade" name="finalidade">
                        <option value="N">Não</option>
                        <option value="S">Sim</option>
                    </select>
                </td>
            </tr>
        </table>
    </fieldset>
    <button type="button" name="emite2" id="emite2" onclick="js_emite();" class="btn btn-light">
        <i class="fas fa-print"></i>
        Emitir Relatório
    </button>

</form>
<?php db_menu(); ?>
</body>
</html>
