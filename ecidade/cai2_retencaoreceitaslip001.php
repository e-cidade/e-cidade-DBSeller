<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2014  DBSeller Servicos de Informatica
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
require_once(modification("dbforms/db_funcoes.php"));

$clrotulo = new rotulocampo;
$clrotulo->label('e50_codord');

?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
<body class="body-default">
  <div class="container">
    <form name="form1">
      <fieldset>
        <legend> Slips gerados por retenção </legend>
        <table>
          <tr>
            <td> <?php db_ancora($Le50_codord,"js_pesquisaOrdemPagamento();",1);  ?> </td>
            <td> <?php db_input('e50_codord',10,$Ie50_codord,true,'text',1);?> </td>
          <tr>
         </table> 
      </fieldset>
      <input name="emite2" id="emite2" type="button" value="Emitir Relatório" onclick="js_emite();" >
    </form>
  </div>
  <?php db_menu(); ?>
</body>
</html>
<script>
function js_emite(){
  jan = window.open( 'cai2_retencaoreceitaslip002.php?pagordem='+document.form1.e50_codord.value,
                     '',
                     'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ' );
  jan.moveTo(0, 0);
}
  
function js_pesquisaOrdemPagamento(){
    js_OpenJanelaIframe('','db_iframe_pagordem','func_pagordem.php?funcao_js=parent.js_mostraOP|e50_codord','Pesquisa',true);
}

function js_mostraOP(chave,erro){
  document.form1.e50_codord.value = chave;  
  db_iframe_pagordem.hide();
}
</script>