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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor=#CCCCCC bgcolor="#CCCCCC" class="body-default" >

<div class="container">
  <form name="form1" method="post">
    <fieldset style="margin-top: 50px; width: 450px;">
    <legend>Relatório de empenhos por histórico</legend>
    <table border='0'>
      <tr>
         <td >
            <b> Período de:</b>
         </td>
         <td>
            <?php
               db_inputdata('data1',@$dia,@$mes,@$ano,true,'text',1,"");
            ?>
         </td>
         <td>
            <b>Até:</b>
         </td>
         <td>
            <?php
               db_inputdata('data2',@$dia2,@$mes2,@$ano2,true,'text',1,"");
            ?>
         </td>
      </tr>
      <tr>
         <td align = "left">
            <strong> Palavra Chave: </strong>
         </td>
         <td colspan = "3">
            <input type='text' name='palavra_chave' id='palavra_chave' size='15' class="PalavraChave">
         </td>
      </tr>
    </table>
    </fieldset>
  </form>

  <div style="margin-top: 10px;">
     <input type="button" id="emite" value="Emitir" onClick="js_emitir()">
  </div>
</div>

<?php db_menu();?>

</body>
</html>

<script>

function js_emitir() {

  let palavra_chave = $F('palavra_chave');
  let retorno = true;
  let data1 = js_formatar($F("data1"), 'd');
  let data2 = js_formatar($F("data2"), 'd');

  if (data1.valueOf() > data2.valueOf()) {
    
    alert( "Data inicial maior do que a final!" )
    return false;
  }

  let sQuery = "data_inicial=" + data1;
  sQuery += "&data_final=" + data2;;
  sQuery += "&palavra_chave=" + palavra_chave;

  oJanela = window.open('emp2_empenhohistorico002.php?'+sQuery,
  '',
  'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
  oJanela.moveTo(0,0);
  
}
</script>