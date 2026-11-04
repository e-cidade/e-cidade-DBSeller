<?php
/*
 *  E-cidade Software Publico para Gestao Municipal
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

$clrotulo = new rotulocampo;
$clrotulo->label('DBtxt23');
$clrotulo->label('DBtxt25');
$clrotulo->label('DBtxt27');
$clrotulo->label('DBtxt28');
db_postmemory($_POST);

/**
 * Menu DB:FINANCEIRO
 *    > Orçamento > Relatórios > Demonstrativos do Orçamento
 *    > Proposta Orçamentária > Programa de Trabalho
 *
 * O relatório "Programa de Trabalho" foi criado especificamente para a
 * cidade de Natal/RN.
 *
 */

?>

<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script>
function js_verifica()
{
  var anoi = new Number(document.form1.datai_ano.value);
  var anof = new Number(document.form1.dataf_ano.value);
  if(anoi.valueOf() > anof.valueOf()){
    alert('Intervalo de data invalido. Velirique !.');
    return false;
  }
  return true;
}

function js_emite()
{

  jan = window.open('orc2_programatrabalho002.php?ano='+document.form1.ano.value
    +'&mostra_rodape='+document.form1.mostra_rodape.value+'&orgao='+document.form1.orgao.value
    +'&unidade='+document.form1.unidade.value,'','width='+(screen.availWidth-5)
    +',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
  jan.moveTo(0,0);
}
</script>  
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
<br><br>
<div class="container">
<fieldset><legend><b>Programa de trabalho </legend>
  <table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
    <tr>
      <td width="360" height="18">&nbsp;</td>
      <td width="263">&nbsp;</td>
      <td width="25">&nbsp;</td>
      <td width="140">&nbsp;</td>
    </tr>
  </table>

  <table  align="center">
    <form name="form1" method="post" action="" onsubmit="return js_verifica();">
      <tr>
         <td >&nbsp;</td>
         <td >&nbsp;</td>
      </tr>
      <tr >
        <td align="left" nowrap title="Digite o Ano do exercicio" >
          <strong>Ano :&nbsp;&nbsp;</strong>
        </td>
        <td>
          <?php
            $ano = db_getsession('DB_anousu');
            db_input('ano', 4, $ano, true, 'text', 2, '');
            ?>
        </td>
      </tr>
      <tr >
        <td align="left" nowrap title="Digite o Ano do exercicio" >
          <strong>Órgão / Unidade :&nbsp;&nbsp;</strong>
        </td>
        <td>
          <?php
            $orgao = '';
            db_input('orgao', 2, $orgao, true, 'text', 2, '');
            ?>
        </td>
        &nbsp;&nbsp;
        <td>
          <?php
            $unidade = '';
            db_input('unidade', 2, $unidade, true, 'text', 2, '');
            ?>
        </td>
      </tr>
      <tr>
       <td>
         <strong>Emite Rodapé:</strong> 
       </td>
       <td>
       <?php $aOpcoesRodape = array ('S' => 'SIM', 'N' => 'NÃO');
            db_select('mostra_rodape', $aOpcoesRodape, true, 2, "");
        ?>
       </td>
      </tr>      
      <tr>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" align = "center"> 
          <input  name="emite2" id="emite2" type="button" value="Processar" onclick="js_emite();" >
        </td>
      </tr>
    </form>
  </table>
</fieldset>
</div>
<?php
  db_menu(
      db_getsession("DB_id_usuario"),
      db_getsession("DB_modulo"),
      db_getsession("DB_anousu"),
      db_getsession("DB_instit")
  );
    ?>
</body>
</html>
