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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);

$oNecessidadeTipoAtendimento = new cl_necessidadetipoatendimento;
$oNecessidadeTipoAtendimento->rotulo->label();

$db_botao = false;
$db_opcao = $opcao;

try {

  if(isset($incluir)) {

    db_inicio_transacao();
      $oNecessidadeTipoAtendimento->incluir(null);
      if ($oNecessidadeTipoAtendimento->erro_status == '0') {
        throw new Exception($oNecessidadeTipoAtendimento->erro_msg);
      }
    db_fim_transacao();

  } else if (isset($alterar)) {

    db_inicio_transacao();
      $oNecessidadeTipoAtendimento->alterar($ed186_sequencial);
      if ($oNecessidadeTipoAtendimento->erro_status == '0') {
       throw new Exception($oNecessidadeTipoAtendimento->erro_msg);
      }
    db_fim_transacao();

  } else if (isset($excluir)) {

    db_inicio_transacao();
      $oNecessidadeTipoAtendimento->excluir($ed186_sequencial);
      if ($oNecessidadeTipoAtendimento->erro_status == '0') {
       throw new Exception($oNecessidadeTipoAtendimento->erro_msg);
      }
    db_fim_transacao();

  }

} catch (Exception $oErro) {

  db_fim_transacao(true);
  $oNecessidadeTipoAtendimento->erro_msg = $oErro->getMessage();
}
?>
<html>
<head>
<title>DBSeller Informática Ltda - Página Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
</head>
<body>
<div class="container">
<form name="form1" method="post" action="">
  <inpu type="hidden" name="opcao" id="opcao" value=<?=$opcao?> >
  <fieldset>
    <legend><?= ($db_opcao==1)?"Inclusão":(($db_opcao==2)?"Alteração":"Exclusão")?> Tipo de Atendimento</legend>
    <table class="form-container">

      <tr>
        <td title="Sequencial">
          <?php db_ancora("Sequencial:", 'js_pesquisaNecessidadeTipoAtendimento(true)', ($db_opcao!=1)?1:3); ?>
        </td>
        <td>
          <?php
          db_input('ed186_sequencial',10,1,true,'text',($db_opcao!=1)?1:3,"onchange='js_pesquisaNecessidadeTipoAtendimento(false);'");
          ?>
        </td>
      </tr>

      <tr>
        <td>
          Descrição:
        </td>
        <td>
          <?php
          db_input('ed186_descricao', 61, 0, true, 'text', ($db_opcao==3)?3:1, "");
          ?>
        </td>
      </tr>

      <tr>
        <td colspan="2">
         <fieldset id="ctTipoAtendimento" style="width: 470px; display: none">
           <legend>Tipo de Atendimento Cadastrado</legend>
           <div id="container-tipoatendimento"></div>
         </fieldset>
      </tr>

    </table>
  </fieldset>
  <input name="<?=($db_opcao==1?"incluir":($db_opcao==2?"alterar":"excluir"))?>" 
         type="submit" 
         id="db_opcao" 
         value="<?=($db_opcao==1?"Incluir":($db_opcao==2?"Alterar":"Excluir"))?>" <?=($db_botao==false&&$db_opcao!=1?"disabled":"")?> >
  <?php if($db_opcao!=1) { ?>
  <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
  <?php } ?>
</form>
</div>

<script>

oGridDados = new DBGrid('container-tipoatendimento');
oGridDados.nameInstance = 'oGridDados';
oGridDados.allowSelectColumns(false);

oGridDados.setCellWidth(new Array('15%','85%'));
oGridDados.setCellAlign(new Array('center','left'));
oGridDados.setHeader(new Array('Sequencial','Descrição'));
oGridDados.setHeight(250);
oGridDados.show($('container-tipoatendimento'));

  function js_pesquisaNecessidadeTipoAtendimento( lMostra ) {
    if ( lMostra ) {
      js_OpenJanelaIframe("",'db_iframe_necessidadetipoatendimento','func_necessidadetipoatendimento.php?funcao_js=parent.js_preencheTipoAtendimento|ed186_sequencial|ed186_descricao','Pesquisa',true);
    } else {
      js_OpenJanelaIframe("",'db_iframe_necessidadetipoatendimento','func_necessidadetipoatendimento.php?pesquisa_chave='+document.form1.ed186_sequencial.value+'&funcao_js=parent.js_preencheTipoAtendimento1','Pesquisa',false);
    }
  }

  function js_preencheTipoAtendimento( ed186_sequencial, sDescricao ) {
    document.form1.ed186_sequencial.value = ed186_sequencial;
    document.form1.ed186_descricao.value  = sDescricao;
    document.form1.db_opcao.disabled = false;
    db_iframe_necessidadetipoatendimento.hide();
  }

  function js_preencheTipoAtendimento1( lErro, sDescricao ) {
    if ( !lErro ) {
      document.form1.ed186_descricao.value = sDescricao;
      document.form1.db_opcao.disabled = false;
    } else {
      document.form1.ed186_sequencial.value = "";
      document.form1.ed186_descricao.value = "";
      document.form1.db_opcao.disabled = true;
      alert(sDescricao);
    }
  }

function js_retornaPesquisaSubdivisao(oResponse) {

	  var oRetorno = JSON.parse(oResponse.responseText);
	  var ctSubdivisoes = document.querySelector('#ctSubdivisoes');
	      ctSubdivisoes.setAttribute('style','display: none;');
	      oGridDados.clearAll(true);
	  if (oRetorno.status == 1) {
	    ctSubdivisoes.setAttribute('style','')
	    oRetorno.subdivisoes.each(function(oSubdivisao, iSeq) {
	    	var aRow = new Array();
	        aRow[0] = oSubdivisao.sequencial;
	        aRow[1] = oSubdivisao.descricao.urlDecode();
	    	oGridDados.addRow(aRow);
	    })

	    oGridDados.renderRows();
	  }
	}

  function js_pesquisa(){
    js_pesquisaNecessidadeTipoAtendimento(true);
  }
</script>

<?php
db_menu();
?>
</body>
</html>
<?php
if(isset($incluir) || isset($alterar)) {

  if ($oNecessidadeTipoAtendimento->erro_status=="0") {
    db_msgbox($oNecessidadeTipoAtendimento->erro_msg);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
  } else {
    db_msgbox($oNecessidadeTipoAtendimento->erro_msg);
    db_redireciona("edu1_necessidadetipoatendimento001.php?opcao={$opcao}");
  }

} else if(isset($excluir)) {
  db_msgbox($oNecessidadeTipoAtendimento->erro_msg);
  db_redireciona("edu1_necessidadetipoatendimento001.php?opcao=3");
}

if ($db_opcao==2 || $db_opcao==3) {
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
