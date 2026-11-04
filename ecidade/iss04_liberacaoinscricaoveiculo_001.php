<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBseller Servicos de Informatica
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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));

$oRotulo = new rotulocampo();
$oRotulo->label("q120_issalvara");
$oRotulo->label("q123_inscr");
$oRotulo->label("z01_nome");

db_postmemory($HTTP_POST_VARS);
db_postmemory($HTTP_GET_VARS);

$oPost = db_utils::postMemory($_POST);
$oGet = db_utils::postMemory($_GET);
$clIssMovAlvara = new cl_issmovalvara;
$clIssAlvara = new cl_issalvara;

if (isset($liberar)) {

	try {
		
		db_inicio_transacao();

		if (empty($q120_issalvara)) {			
			$where   = "q123_inscr = {$q123_inscr}";
			$sql     = $clIssAlvara->sql_query_file("", "q123_sequencial", "", $where);

			$rs = db_query($sql);

			if (!$rs) {
				throw new ErrorException("Erro ao buscar o alvará do veículo.");
			}

			if (pg_num_rows($rs) == 0) {

				$usuario = new UsuarioSistema(db_getsession('DB_id_usuario'));

				$clIssAlvara->q123_isstipoalvara = $q123_isstipoalvara;
				$clIssAlvara->q123_inscr = $q123_inscr;
				$clIssAlvara->q123_dtinclusao = date("Y-m-d");
				$clIssAlvara->q123_situacao = 1;
				$clIssAlvara->q123_usuario = $usuario->getCodigo();
				$clIssAlvara->q123_geradoautomatico = 't';
				$clIssAlvara->incluir(null);

	            if ($clIssAlvara->erro_status == '0') {
	                throw new ErrorException("Erro ao salvar o alvará.");
	            }

				$q120_issalvara = $clIssAlvara->q123_sequencial;
			} else {
				$q120_issalvara = db_utils::fieldsMemory($rs, 0)->q123_sequencial;
			}
		}


		$oAlvara         = new Alvara($q120_issalvara);
		$oLiberarAlvara  = $oAlvara->incluirMovimentacao( MovimentacaoAlvara::TIPO_LIBERACAO );
		$aDocumentos     = Array();

	    $oLiberarAlvara->setDataMovimentacao( date("Y-m-d", db_getsession("DB_datausu")));
	    $oLiberarAlvara->setValidadeAlvara($oPost->q120_validadealvara);
	    $oLiberarAlvara->setCodigoProcesso($oPost->p58_codproc);
	    $oLiberarAlvara->setObservacao($oPost->q120_obs);
	    $oLiberarAlvara->setUsuario( new UsuarioSistema(db_getsession('DB_id_usuario')) );

	    if ($oPost->documentos != "" && !isset($oGet->aba)) {

	     	$aDocumentos = explode(",", $oPost->documentos);

	      	foreach($aDocumentos as $iIndice => $oValor){
	        	$oAlvara->addDocumento($oValor);
	      	}
	    }

    	$oAlvara->setTipoAlvara($oPost->q123_isstipoalvara);
    	$oLiberarAlvara->processar();

	    db_msgbox("Movimentação realizada com sucesso");

	    $q123_inscr          = "";
	    $z01_nome            = "";
	    $q120_issalvara      = "";
	    $q123_isstipoalvara  = "";
	    $q98_descricao       = "";
	    $q120_validadealvara = "";
	    $p58_codproc         = "";
	    $p58_requer          = "";
	    $q120_obs            = "";

	    db_fim_transacao(false);

  	} catch (ErrorException $erro) {

    	db_msgbox($erro->getMessage());
    	db_fim_transacao(true);
  	}
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php
  db_app::load("scripts.js, prototype.js, datagrid.widget.js, strings.js, grid.style.css, estilos.css");
  db_app::load("classes/dbViewAvaliacoes.classe.js, widgets/windowAux.widget.js, dbcomboBox.widget.js");
  db_app::load("DBViewAlvaraDocumentos.js");
?>
<style type="text/css">
  .field {
    border: 0px;
    border-top: 2px groove white;
  }
  fieldset.field table tr td:FIRST-CHILD {
    width: 150px;
    white-space: nowrap;
  }
 .link_botao {
    color: blue;
    cursor: pointer;
    text-decoration: underline;
  }
</style>
<script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBInputHora.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
<script type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>
<body onLoad="a=1;" class="body-default">
  <div class="container">
  <form name="form1" method="post" action="" onsubmit="jsMontaDocumentos(); return verifica();">
    <fieldset style="width: 700px;">
      <legend><strong>Liberação de Alvará</strong></legend>
      <table align="center" width="100%" cellpadding="" border="0">

        <tr>
            <td>
                <b>
                    <a id="ancoraVeiculo" href="#">
                        <label for="q123_inscr">Inscrição:</label>
                    </a>
                </b>
            </td>
            <td colspan="3">
                <input id="q172_sequencial" name="q172_sequencial" type="hidden" />
                <input id="q123_inscr" name="q123_inscr" type="text" data="q123_inscr" class="field-size2"/>
                <input id="descricaoInscricaoCGM" name="descricaoInscricaoCGM" type="text" data="z01_nome" class="field-size7 readonly" disabled="disabled"/>
            </td>
        </tr>

        <tr>
          <td><strong>Alvará:</strong>
          </td>
          <td>
  			   <?php
  			    	db_input("q120_issalvara", 8,"", true, 'text', 3);
  			   ?>
          </td>
        </tr>


        <tr>
          <td>
            <strong>
            <?php
              db_ancora("Tipo de Alvará:","js_pesquisaTipoAlvara(true);",1);
            ?>
            </strong>
          </td>
          <td>
           <?php
             db_input("q123_isstipoalvara", 8,"", true, 'text', 3);
             db_input("q98_descricao",     50,"", true, 'text', 3);
           ?>
          </td>
        </tr>

        <tr>
          <td><strong>Data da Movimentação:</strong>
          </td>
          <td>
  			   <?php
  			    echo date("d/m/Y",db_getsession("DB_datausu"));
  			   ?>
          </td>
        </tr>

        <tr>
          <td title="Validade em Dias"><strong>Validade do Alvará:</strong>
          </td>
          <td>
  			   <?php
  			    db_input("q120_validadealvara", 8,"", true, 'text', 1);
  			   ?>
          </td>
        </tr>

          <tr>
            <td nowrap><strong>
               <?php
                 db_ancora("Processo:","js_pesquisap58_codproc(true);",1);
               ?></strong>
            </td>
            <td>
              <?php
                db_input('p58_codproc',8,"",true,'text',1," onchange='js_pesquisap58_codproc(false);'");
                db_input('p58_requer',50,"",true,'text',3,'');
              ?>
            </td>
          </tr>

        <tr>
          <td ><strong>Observação:</strong>
          </td>
          <td>
  			   <?php
  			    db_textarea("q120_obs",5, 53,  "", true,null, 1)
  			   ?>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <div id='ctnDocumento' <?=isset($oGet->aba) ? "style=\"display: none\"":"" ?>></div>
          </td>
        </tr>

      </table>

      <input type='hidden' id='documentos' name='documentos'>

    </fieldset>
    <input type="submit" name="liberar"  id='liberar' value="Liberar Alvará" />
  </form>

  <div id='ficha'></div>

</div>
<?php
 if (!isset($oGet->aba)){
   db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
 }
?>
</body>
</html>

<script type="text/javascript">
	$('q123_isstipoalvara').classList.add("field-size2");
	$('q120_validadealvara').classList.add("field-size2");
	$('p58_codproc').classList.add("field-size2");
	$('q98_descricao').classList.add("field-size7");
	$('p58_requer').classList.add("field-size7");

    // grid documentos
    var oDocumentos = new DBViewAlvaraDocumentos("oDocumentos", "ctnDocumento");
    oDocumentos.setIsInscricaoVeiculo(true);
    oDocumentos.show();

    var lookupInscricaoVeiculo = new DBLookUp(
        $('ancoraVeiculo'),
        $('q123_inscr'),
        $('descricaoInscricaoCGM'),
        {
          'sArquivo': 'func_inscricaoveiculo.php',
          'sLabel': 'Pesquisar Inscrição de Veículo',
          'aCamposAdicionais': ['q172_sequencial', 'q172_issbase', 'db_issalvara']
        }
    );

    lookupInscricaoVeiculo.setCallBack('onClick', (arguments) => {
        $('descricaoInscricaoCGM').value = arguments[1];
        $('q172_sequencial').value = arguments[2];
        $('q123_inscr').value =  arguments[3];
        $('q120_issalvara').value =  arguments[4];

        oDocumentos.setCodigoAlvara($('q123_inscr').value);
        oDocumentos.carregaDados();
    });

    lookupInscricaoVeiculo.setCallBack('onChange', (error, arguments) => {
        if (error == true) {
            $('q123_inscr').value =  '';
            $('descricaoInscricaoCGM').value = '';
            $('q172_sequencial').value = '';
            $('q120_issalvara').value = '';
            return;
        }

        $('descricaoInscricaoCGM').value = arguments[0];
        $('q172_sequencial').value = arguments[2];
        $('q120_issalvara').value = arguments[3];

        oDocumentos.setCodigoAlvara($('q123_inscr').value);
        oDocumentos.carregaDados();
    });

function jsMontaDocumentos(){
  $('documentos').value = oDocumentos.getDocumentosSelecionados().toString();
}

// mostra processos
function js_pesquisap58_codproc(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_cgm','func_protprocesso.php?funcao_js=parent.js_mostraprotprocesso1|p58_codproc|z01_nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_cgm','func_protprocesso.php?pesquisa_chave='+document.form1.p58_codproc.value+'&funcao_js=parent.js_mostraprotprocesso','Pesquisa',false);
  }
}
function js_mostraprotprocesso(chave,chave1,erro){
  document.form1.p58_requer.value = chave1;
  if(erro==true){
    document.form1.p58_codproc.focus();
    document.form1.p58_codproc.value = '';
  }
}
function js_mostraprotprocesso1(chave1,chave2){
  document.form1.p58_codproc.value = chave1;
  document.form1.p58_requer.value = chave2;
  db_iframe_cgm.hide();
}


function verifica(){

  var iAlvara = $F('q123_inscr');

  if (iAlvara == null || iAlvara == "") {

    alert("Selecione um Alvara");
    return false;
	}
  if ($F('q123_isstipoalvara') == "") {

    alert("Escolha um tipo de alvará.");
    return false;
  }
}
function js_pesquisaTipoAlvara(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_isstipoalvara','func_isstipoalvara.php?funcao_js=parent.js_mostratipoalvara1|q98_sequencial|q98_descricao|q98_tipovalidade|q98_quantvalidade&alvara_veiculo=1','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_isstipoalvara','func_isstipoalvara.php?pesquisa_chave='+document.form1.p58_codproc.value+'&funcao_js=parent.js_mostratipoalvara|q98_sequencial|q98_descricao|q98_tipovalidade|q98_quantvalidade&alvara_veiculo=1','Pesquisa',false);
  }
}

/**
 * Função de pesqueisa de alvará
 */
function js_mostratipoalvara(chave1,chave2,chave3,chave4,erro){

  document.form1.q98_descricao.value = chave2;
  if(erro==true){
    document.form1.q123_isstipoalvara.focus();
    document.form1.q98_sequencial.value = '';
  }
  if(chave3 == 3){

    document.form1.q120_validadealvara.readOnly = 'readyonly';
    document.form1.q120_validadealvara.value    = 0;
  } else if(chave3 == 1) {

    document.form1.q120_validadealvara.readOnly = 'readyonly';
    document.form1.q120_validadealvara.value    = chave4;
  } else {
    document.form1.q120_validadealvara.readOnly = false;
    document.form1.q120_validadealvara.value    = '';
    document.form1.q120_validadealvara.focus();
  }
}
function js_mostratipoalvara1(chave1,chave2,chave3,chave4){
  document.form1.q123_isstipoalvara.value = chave1;
  document.form1.q98_descricao.value = chave2;
  if(chave3 == 3){

    document.form1.q120_validadealvara.readOnly = 'readyonly';
    document.form1.q120_validadealvara.value    = 0;
  } else if(chave3 == 1) {

    document.form1.q120_validadealvara.readOnly = 'readyonly';
    document.form1.q120_validadealvara.value    = chave4;
  } else {
    document.form1.q120_validadealvara.readOnly = false;
    document.form1.q120_validadealvara.value    = '';
    document.form1.q120_validadealvara.focus();
  }

  db_iframe_isstipoalvara.hide();
}
</script>