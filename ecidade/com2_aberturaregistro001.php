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
include(modification("classes/db_pcparam_classe.php"));
$clpcparam = new cl_pcparam;
$clrotulo = new rotulocampo;
$clrotulo->label("pc10_numero");
$db_opcao = 1;
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script type="text/javascript" src="scripts/scripts.js"></script>
<script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
function js_abre(){
   obj = document.form1;
   query='';
   query += "&ini="+obj.pc10_numero_ini.value;
   query += "&fim="+obj.pc10_numero_fim.value;
   query += "&departamento=<?=db_getsession("DB_coddepto")?>";
   jan = window.open('com2_aberturaregistro002.php?'+query,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
}
</script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="document.form1.pc10_numero_ini.focus();" >
<table width="790" height='18'  border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr>
    <td width="360">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>

<table valign="top" marginwidth="0" width="300" border="0" cellspacing="0" cellpadding="0" style="margin-top: 15px;" align="center">
<tr align="center">
<td>
	<fieldset>
		<legend><b>Emite Abertura</b></legend>

	<table valign="top" marginwidth="0" width="300" border="0" cellspacing="0" cellpadding="0">
 <tr>
  <td  align="center" valign="top" bgcolor="#CCCCCC">
    <form name='form1'>
    <table>
      <tr>
	<td nowrap title="<?=@$Tpc10_numero?>" id="ancoraSolicitacaoInicio">
        <?php db_ancora('Solicitações de: ','js_pesquisaabertura()',$db_opcao); ?>
	</td>
	<td>
	   <?php db_input('pc10_numero',8,$Ipc10_numero,true,'text',$db_opcao,"","pc10_numero_ini")  ?>
	</td>
	<td id="ancoraSolicitacaoFim">
        <?php db_ancora('Até: ','js_pesquisaabertura(\'fim\')',$db_opcao); ?>
    </td>
	<td>
	   <?php db_input('pc10_numero',8,$Ipc10_numero,true,'text',$db_opcao,"","pc10_numero_fim")  ?>
	</td>
      </tr>
    </table>
    </form>
  </td>
 </tr>
</table>
</fieldset>
<input name='pesquisar' type='button' value='Gerar relatório' onclick='js_abre();' style="margin-top: 5px">
</td>
</tr>
</table>
    <?php
    db_menu();
    ?>
</body>
</html>
<script>
const solicitacaoInicio = document.getElementById('pc10_numero_ini');
const solicitacaoFim = document.getElementById('pc10_numero_fim');

solicitacaoInicio.onchange = () => js_copiacampo();

function js_copiacampo(){
    if(solicitacaoFim.value === '') {
        solicitacaoFim.value = solicitacaoInicio.value;
    }
}

function js_pesquisaabertura(campo){
    let funcaoJs = 'js_mostraaberturaini';

    if (campo === 'fim') {
        funcaoJs = 'js_mostraaberturafim'
    }

    return js_OpenJanelaIframe(
        'CurrentWindow.corpo',
        'db_iframe_abertura',
        `func_solicitaregistropreco.php?funcao_js=parent.${funcaoJs}|pc54_solicita`,
        'Pesquisa de aberturas',
        true
    );
}

function js_mostraaberturaini(solicitacao) {
    db_iframe_abertura.hide();
    solicitacaoInicio.value = solicitacao;

    js_copiacampo()
}

function js_mostraaberturafim(solicitacao) {
    db_iframe_abertura.hide();
    solicitacaoFim.value = solicitacao;
}
</script>
