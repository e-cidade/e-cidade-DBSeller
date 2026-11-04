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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));

$oPost = db_utils::postMemory($_POST);
$oGet  = db_utils::postMemory($_GET);

$clPlanilhaDepartamento = new cl_placaixausu();

$db_opcao = 22;
$db_botao = false;

if (isset($oPost->salvar)) {

    try {
      db_inicio_transacao();
      
      $clPlanilhaDepartamento->excluir(null, "k214_placaixa = {$oPost->k214_placaixa}");

      $clPlanilhaDepartamento->k214_placaixa = $oPost->k214_placaixa;
      $clPlanilhaDepartamento->k214_db_depart = $oPost->k211_depart;
      $clPlanilhaDepartamento->k214_db_usuario = db_getsession('DB_id_usuario');

      $clPlanilhaDepartamento->incluir(null);
      if ($clPlanilhaDepartamento->erro_status == "0") {
        throw new Exception($clPlanilhaDepartamento->erro_msg);
      }
      
      db_fim_transacao();
      
      db_msgbox("Procedimento realizado com sucesso");
      
    } catch (Exception $oErro) {
        db_fim_transacao(true);
        db_msgbox($oErro->getMessage()); 
    }
} 

if (isset($oGet->k214_placaixa)) {
    $wherePlanilha = "k214_placaixa = {$oGet->k214_placaixa}";
    $sqlPlanilhaDepartamento = $clPlanilhaDepartamento->sql_query(null, "*", null, $wherePlanilha);
    $rsDados = $clPlanilhaDepartamento->sql_record($sqlPlanilhaDepartamento);
    if ($clPlanilhaDepartamento->numrows > 0) {
      db_fieldsmemory($rsDados,0);
    }
    
    $db_opcao = 2;
    $db_botao = true;    
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script type="text/javascript" src="scripts/scripts.js" ></script>
<script type="text/javascript" src="scripts/prototype.js" ></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
  <form name="form1" method="post" >
   <fieldset>
	<legend>Manutenção vínculo Planilha/Departamento</legend>
	  <table class="form-container">
        <tr>
          <td>Planilha:</td>
          <td>
            <?php
              db_input('k214_placaixa',8,'Planilha',true,'text',3);
            ?>
          </td>
        </tr>
        <tr>
          <td>
            <?php 
              db_ancora('Departamento:',"pesquisaDepartamento(true);", $db_opcao); 
            ?>
          </td>
          <td>
            <?php
              db_input('k211_depart',8,$Ik211_depart,true,'text',$db_opcao,"onchange='pesquisaDepartamento(false);'");
              db_input('descrdepto',50,0,true,'text',3);
            ?>
          </td>
        </tr>
      </table>
   </fieldset>
   <input name="salvar" type="submit" value="Salvar" <?php echo ($db_botao==false?"disabled":"")?> >
   <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="pesquisaPlanilha();">
  </form>
</div>
<?php
  db_menu();
?>
</body>
</html>
<script>
function pesquisaPlanilha() {
  js_OpenJanelaIframe('CurrentWindow.corpo',
              'db_iframe_planilha',
              'func_placaixa.php?lAltera=1&funcao_js=parent.preenchePesquisa|k80_codpla',
              'Pesquisar Planilha',
              true);
}

function preenchePesquisa(chave) {
  db_iframe_planilha.hide();
  location.href = 'cai4_manutencaoplanilhadepartamento001.php?k214_placaixa='+chave;
}

function pesquisaDepartamento(mostra) {
  if (mostra==true) {
    var sUrl = 'func_departamento.php?lAltera=1&funcao_js=parent.mostraDepartamento1|coddepto|descrdepto';
      js_OpenJanelaIframe('CurrentWindow.corpo',
                          'db_iframe_departamento',
                          sUrl,
                          'Pesquisar Departamento',
                          true);
    } else {
    if ($F('k211_depart') != '') {
      js_OpenJanelaIframe('CurrentWindow.corpo',
                          'db_iframe_departamento',
                          'func_departamento.php?lAltera=1&pesquisa_chave='+$F('k211_depart')+
                          '&funcao_js=parent.mostraDepartamento',
                          'Pesquisar Departamento',
                          false);
    }
  }
}

function mostraDepartamento(chave,erro) {
  $('descrdepto').value = chave;
  if (erro == true) {
    $('k211_depart').focus();
    $('k211_depart').value = '';
  } 
}

function mostraDepartamento1(chave1, chave2) {
  $('k211_depart').value = chave1;
  $('descrdepto').value = chave2;
  db_iframe_departamento.hide();
}

if ($F("k214_placaixa") == "") {
  pesquisaPlanilha();	
}
</script>