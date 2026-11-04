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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="100%" align="center" valign="top" bgcolor="#CCCCCC">
      <center>

      <?php
        if (isset($lNovaConsulta) && !$lNovaConsulta) {
          echo '<input name="fechar" type="button" value="Fechar" onclick="parent.db_iframe_solicita.hide();">';
        }
         $sql  = "   select e60_numemp,                                                                                                  ";
         $sql .= "          pc11_numero,                                                                                                 ";
         $sql .= "          pc90_numeroprocesso                                                                                          ";
	       $sql .= "     from empautitem                                                                                                   ";
	       $sql .= "          inner join empautitempcprocitem on empautitempcprocitem.e73_autori    = empautitem.e55_autori                ";
	       $sql .= "                                         and empautitempcprocitem.e73_sequen    = empautitem.e55_sequen                ";
	       $sql .= "          inner join pcprocitem           on pcprocitem.pc81_codprocitem        = empautitempcprocitem.e73_pcprocitem  ";
	       $sql .= "          inner join solicitem            on solicitem.pc11_codigo              = pcprocitem.pc81_solicitem            ";
	       $sql .= "          left  join solicitaprotprocesso on solicitaprotprocesso.pc90_solicita = solicitem.pc11_numero            		 ";
		     $sql .= "          left  join empempaut            on empempaut.e61_autori               = empautitem.e55_autori                ";
		     $sql .= "          left  join empempenho           on empempenho.e60_numemp              = empempaut.e61_numemp                 ";
		     $sql .= "    where e55_autori = {$e55_autori}                                                                                   ";
         $sql .= " group by e60_numemp, pc11_numero, pc90_numeroprocesso                                                                 ";
		     $sql .= "   union                                                                                                               ";
         $sql .= "   select e60_numemp,                                                                                                  ";
         $sql .= "          pc11_numero,                                                                                                 ";
         $sql .= "          pc90_numeroprocesso                                                                                          ";
         $sql .= "     from empempenho                                                                                                   ";
         $sql .= "          inner join empempaut            on empempaut.e61_numemp               = empempenho.e60_numemp                ";
         $sql .= "          inner join acordoempautoriza on acordoempautoriza.ac45_empautoriza    = empempaut.e61_autori                 ";
         $sql .= "          inner join acordo on acordo.ac16_sequencial                           = acordoempautoriza.ac45_acordo        ";
         $sql .= "          inner join acordoposicao ON acordoposicao.ac26_acordo = acordo.ac16_sequencial                               ";
         $sql .= "          inner join acordoitem ON acordoitem.ac20_acordoposicao = acordoposicao.ac26_sequencial                       ";
         $sql .= "          inner join acordopcprocitem ON acordopcprocitem.ac23_acordoitem = acordoitem.ac20_sequencial                 ";
         $sql .= "          inner join pcprocitem ON pcprocitem.pc81_codprocitem = acordopcprocitem.ac23_pcprocitem                      ";
         $sql .= "          inner join solicitem            on solicitem.pc11_codigo              = pcprocitem.pc81_solicitem            ";
         $sql .= "          left  join solicitaprotprocesso on solicitaprotprocesso.pc90_solicita = solicitem.pc11_numero                ";
         $sql .= "    where e61_autori = {$e55_autori}                                                                                   ";
         $sql .= " group by e60_numemp, pc11_numero, pc90_numeroprocesso                                                                 ";
         $sql .= "   union                                                                                                               ";
         $sql .= "   select e60_numemp,                                                                                                  ";
         $sql .= "          pc11_numero,                                                                                                 ";
         $sql .= "          pc90_numeroprocesso                                                                                          ";
         $sql .= "     from empempenho                                                                                                   ";
         $sql .= "          inner join empempaut ON empempaut.e61_numemp = empempenho.e60_numemp                                         ";
         $sql .= "          inner join acordoempautoriza ON acordoempautoriza.ac45_empautoriza = empempaut.e61_autori                    ";
         $sql .= "          inner join acordo ON acordo.ac16_sequencial = acordoempautoriza.ac45_acordo                                  ";
         $sql .= "          inner join acordoposicao ON acordoposicao.ac26_acordo = acordo.ac16_sequencial                               ";
         $sql .= "          inner join acordoitem ON acordoitem.ac20_acordoposicao = acordoposicao.ac26_sequencial                       ";
         $sql .= "          inner join acordoliclicitem ON acordoliclicitem.ac24_acordoitem = acordoitem.ac20_sequencial                 ";
         $sql .= "          inner join liclicitem on liclicitem.l21_codigo = acordoliclicitem.ac24_liclicitem                            ";
         $sql .= "          inner join pcprocitem ON pcprocitem.pc81_codprocitem = liclicitem.l21_codpcprocitem                          ";
         $sql .= "          inner join solicitem ON solicitem.pc11_codigo = pcprocitem.pc81_solicitem                                    ";
         $sql .= "          LEFT  JOIN solicitaprotprocesso ON solicitaprotprocesso.pc90_solicita = solicitem.pc11_numero                ";
         $sql .= "    where e61_autori = {$e55_autori}                                                                                   ";
         $sql .= "   group by e60_numemp, pc11_numero, pc90_numeroprocesso                                                               ";
         db_lovrot($sql,15,"()","","");
      ?>
      </center>
    </td>
  </tr>
</table>
</body>
</html>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
