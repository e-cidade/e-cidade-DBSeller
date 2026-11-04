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
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_orctiporec_classe.php"));

$clrotulo = new rotulocampo;
$clrotulo->label('DBtxt21');

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script>
        function js_emite(opcao, origem) {
            sel_instit = document.form1.db_selinstit.value;
            data1 = document.form1.DBtxt21_ano.value + '-' + document.form1.DBtxt21_mes.value + '-' + document.form1.DBtxt21_dia.value;
            if (sel_instit == 0) {
                alert('Você não escolheu nenhuma Instituição. Verifique!');
                return false;
            }
            if (data1.length < 8) {
                alert('Data inválida !');
                return false;
            }
            recurso = document.form1.recurso.value;
            jan = window.open('con2_confresfin002.php?db_selinstit=' + sel_instit + '&data_limite=' + data1 + '&recurso=' + recurso, '', 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
            jan.moveTo(0, 0);
        }

    </script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">

    <form name="form1" method="post" action="">
        <fieldset>
            <legend>Filtros</legend>
            <table>
                <tr>
                    <td align="center" colspan="3">
                        <?php
                        db_selinstit('parent.js_limpa', 300, 100);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="right"><strong>Recurso:</strong></td>
                    <td>
                        <?php
                        $dbwhere = " o15_datalimite is null or o15_datalimite > '" . date('Y-m-d', db_getsession('DB_datausu')) . "'";
                        $clorctiporec = new cl_orctiporec;
                        $sCampos = "distinct o15_recurso , o15_recurso || ' - ' ||  o15_descr as o15_descr";
                        $sSql = $clorctiporec->sql_query(null, $sCampos, "o15_descr", $dbwhere);
                        $res = $clorctiporec->sql_record($sSql);
                        db_selectrecord("recurso", $res, true, 2, "", "", "", "0");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><b> Data Limite :</b></td>
                    <td>
                        <?php
                        db_inputdata('DBtxt21', @$DBtxt21_dia, @$DBtxt21_mes, @$DBtxt21_ano, true, 'text', 4);
                        ?>
                    </td>
                </tr>
            </table>
            <input type=button name=emite value=Emitir onClick="js_emite();">
        </fieldset>

    </form>
</div>
</body>
</html>
