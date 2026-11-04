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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));


$oGet = db_utils::postMemory($_GET);

$oClretencaotipocalc     = new cl_retencaotipocalc();
$oClretencaotiporecgrupo = new cl_retencaotiporecgrupo();
?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/EmissaoRelatorio.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
<body>
    <div class="container">
        <form name='form1' action='' method="post">
            <fieldset>
                <legend>Relatório de Retenções cadastradas</legend>
                    <table>
                        <tr>
                            <td><strong>Tipo de Cálculo:</strong></td>
                            <td>
                              <?php
                                db_selectrecord(
                                    "e21_retencaotipocalc",
                                    $oClretencaotipocalc->sql_record(
                                        $oClretencaotipocalc->sql_query(
                                            "",
                                            "e32_sequencial ,e32_descricao",
                                            "e32_sequencial",
                                            ""
                                        )
                                    ),
                                    true,
                                    1,
                                    "",
                                    "",
                                    "",
                                    '0',
                                    ""
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Grupo:</strong></td>
                            <td>
                              <?php
                                db_selectrecord(
                                    "e21_retencaotiporecgrupo",
                                    $oClretencaotiporecgrupo->sql_record(
                                        $oClretencaotiporecgrupo->sql_query(
                                            "",
                                            "e01_sequencial, e01_descricao",
                                            "e01_sequencial",
                                            ""
                                        )
                                    ),
                                    true,
                                    1,
                                    "",
                                    "",
                                    "",
                                    '0',
                                    ""
                                );
                                ?>
                            </td>
                        </tr>  
                    </table>
            </fieldset>

            <input name="gerar_relatorio"  id="gerar_relatorio" type="button" value="Gerar Relatório">

        </form>
    </div>
</body>
<script type="text/javascript">

$('gerar_relatorio').addEventListener('click', function() {

  var parametros = new Object();
  parametros.e21_retencaotipocalc = $F('e21_retencaotipocalc');
  parametros.e21_retencaotiporecgrupo = $F('e21_retencaotiporecgrupo');

  var oRelatorio = new EmissaoRelatorio("emp2_relcadretencoes002.php", parametros);
  oRelatorio.open();
});
</script>
</html>

