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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_utils.php");
require_once modification("dbforms/db_funcoes.php");

db_postmemory($_POST);

$aCGM = array();
$sMsg = null;

try {

	$sSqlCGM  = '     select distinct z01_numcgm as cgm,                 ';
	$sSqlCGM .= '            z01_cgccpf||\' - \'||z01_nome as empregador ';
	$sSqlCGM .= '       from rhlota                                      ';
	$sSqlCGM .= ' inner join cgm                                         ';
	$sSqlCGM .= '         on rhlota.r70_numcgm = cgm.z01_numcgm          ';
  $sSqlCGM .= '      where r70_instit = '. db_getsession("DB_instit")   ;
	$sSqlCGM .= '   order by z01_numcgm '                                 ;

	$rsSqlCGM = db_query($sSqlCGM);

	if(!$rsSqlCGM) {
		throw new DBException("Ocorreu um erro ao consultar os CGM vinculados as lotações.\nContate o suporte.");
	}

	if(pg_num_rows($rsSqlCGM) > 0) {
		$aCGM = db_utils::makeCollectionFromRecord($rsSqlCGM, function ($oItemCGM) {
			return (object)array('cgm'=>$oItemCGM->cgm,'empregador'=>$oItemCGM->empregador);
		});
	}

} catch (Exception $e) {
	$sMsg = $e->getMessage();
}
?>
<html>
<head>
  <title>DBSeller Informática Ltda</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="estilos/grid.style.css">
    <link rel="stylesheet" href="estilos/avaliacao.css">
    <link rel="stylesheet" href="estilos/awesomplete.css">
    <script src="scripts/scripts.js"></script>
    <script src="scripts/prototype.js"></script>
    <script src="scripts/object.js"></script>
    <script src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script src="scripts/widgets/DBInputHora.widget.js"></script>
    <script src="scripts/widgets/Input/DBInputCep.widget.js"></script>
    <script src="scripts/widgets/Input/DBInputCNPJ.js"></script>
    <script src="scripts/widgets/Input/DBInputCpf.widget.js"></script>
    <script src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <script src="scripts/widgets/Input/DBInputInteger.widget.js"></script>
    <script src="scripts/widgets/Input/DBInputTelefone.widget.js"></script>
    <script src="scripts/widgets/Input/DBInputValor.widget.js"></script>
    <script src="scripts/widgets/Input/DBInputCheckboxRadio.widget.js"></script>
    <script src="scripts/widgets/Input/DBCheckBox.widget.js"></script>
    <script src="scripts/widgets/Input/DBRadio.widget.js"></script>
    <script src="scripts/widgets/Collection.widget.js"></script>
    <script src="scripts/classes/avaliacao/DBViewFormulario.classe.js"></script>
    <script src="scripts/classes/avaliacao/DBViewGrupoPerguntas.classe.js"></script>
    <script src="scripts/classes/avaliacao/DBViewPergunta.classe.js"></script>
    <script src="scripts/classes/avaliacao/DBViewResposta.classe.js"></script>
    <script src="scripts/awesomplete.js"></script>
    <script src="scripts/classes/avaliacao/DBAutoComplete.js"></script>
    <script src="scripts/classes/eSocial/DBAutoCompleteEsocial.js"></script>
    <script src="scripts/classes/avaliacao/DBViewRespostaNula.classe.js"></script>
    <script src="scripts/AjaxRequest.js"></script>
  <style>
    .controle {
      width: 80px;
    }

    #anterior {
      margin-left: 2px;
      float: left;
    }

    #proximo {
      margin-right: 2px;
      float: right;
    }

    .db-tooltip {
      display: none;
    }
  </style>
</head>
<body>
  <form class="container" style="width: 800px;">
    <?php if (!empty($aCGM)): ?>
	    <fieldset>
	      <legend><label for="cgm">Escolha o Empregador</label></legend>
	      <select id = 'cgm' style="width:100%" onchange="buscarAvaliacao(event)">
	      	<?php foreach($aCGM as $oCGM):?>
	      		<option value="<?php echo $oCGM->cgm; ?>"><?php echo $oCGM->empregador; ?></option>
	      	<?php endforeach;?>
	      </select>
	    </fieldset>
    <?php endif; ?>
    <fieldset>
    <legend>Formulário de Cadastro para o eSocial</legend>
      <div id="questionario"></div>
    </fieldset>
    <input type="button" id="anterior" name="anterior" value="Anterior" class="controle" />
    <input type="button" id="salvar"   name="salvar"   value="Salvar"   class="controle" />
    <input type="button" id="proximo"  name="proximo"  value="Próximo"  class="controle" />
  <form>
  <script type="text/javascript">
  	var viewAvaliacao      = '';
    var iCGMAnterior = '';

    (function(){
      try {
        buscarAvaliacao();
      } catch (e) {
        alert(e);
      }
    })();

    function buscarAvaliacao(event) {

      if(event) {
        if(!confirmaSaida("Se você trocar de empregador os dados que não foram salvos serão perdidos.\nTem certeza que deseja trocar?")) {
          $('cgm').value = iCGMAnterior;
          return false;
        }
      }

      removeEventoBotoes();
      iCGMAnterior = $F('cgm');
      $('questionario').innerHTML = '';

      var iCGM   = $F('cgm');
      var oDados = {
      	exec : 'buscarAvaliacao'
      };

      if (!empty(iCGM)) {
        oDados.iCGM = iCGM;
      }

      AjaxRequest.create('eso01_preenchimentoempregador.RPC.php', oDados, montarAvaliacao)
        .setMessage('Buscando dados...')
        .execute();
    }

    function montarAvaliacao(oResponse, lErro) {

      if (lErro) {
        alert(oResponse.mensagem);
      }

      viewAvaliacao = DBViewFormulario.makeFromObject(oResponse.oFormulario)
        .setEvent('changeStep', controlarBotoes)
        .show($('questionario'));

      DBViewRespostaNula.adicionaRespostaNula(viewAvaliacao);

      $('proximo').observe('click', function() {
        this.blur();
        viewAvaliacao.avancarGrupo();
      });

      $('anterior').observe('click', function() {
        viewAvaliacao.recurarGrupo();
      });

      $('salvar').observe('click', function() {
        salvarQuestionario(viewAvaliacao);
      });
    }

    function salvarQuestionario(viewAvaliacao, iCodigoGrupo) {

      if(! viewAvaliacao.getStatus().grupoAtual.isValido()) {
        alert("Há informações obrigatórias inconsistentes.\nVerifique.");
        return false;
      }
      AjaxRequest.create(
        'eso01_preenchimentoempregador.RPC.php',
        {
          exec                  : 'salvarAvaliacao',
          iCGM                  : iCGMAnterior,
          iCodigoAvaliacao      : viewAvaliacao.codigo,
          iCodigoGrupoPerguntas : iCodigoGrupo,
          aPerguntasRespostas   : viewAvaliacao.getDados(iCodigoGrupo)
        },
        function(oResponse, lErro){

          if (!iCodigoGrupo || lErro) {
            alert(oResponse.mensagem);
          }
          if (lErro) {
            return ;
          }
          viewAvaliacao.avancarGrupo();
        }
      ).setMessage('Salvando dados...').execute();

      return true;
    }

    function removeEventoBotoes() {

      $('salvar').stopObserving('click');
      $('proximo').stopObserving('click');
      $('anterior').stopObserving('click');
    }

    function confirmaSaida (sMensagem) {

      if(typeof sMensagem == 'undefined' || sMensagem == null || sMensagem == false) {
        sMensagem = 'Você está saindo do cadastro do e-social.\nAntes de sair, salve seus dados.';
      }

      if (!confirm(sMensagem)) {
        return false;
      }
      return true;
    }

    function aplicarMascaraAliquotas(event) {
        const regex_start = "^(\\,|\\.)(\\d*)$|(?:\\,|\\.)?(\\d*)(\\,|\\.)?(\\d{0,";
        const decimals = 4;
        const regex_end = '})(\\d*)(?:\\,|\\.)?';
        const regex = new RegExp(regex_start + decimals + regex_end);
        event.target.value = event.target.value.replace('.', ',');
        event.target.value = event.target.value.replace(/[^0-9\,\.]/g, '').replace(regex, '$1$2$3$4$5');
        event.target.placeholder = "0,0000";
    }

    function formataAliquotas() {
        const fap = document.querySelector("input[identificador=fap]");
        const aliqRatAjust = document.querySelector("input[identificador=aliqRatAjust]");

        if (fap && !fap.getAttribute("decimals")) {
            fap.setAttribute("decimals", "4");
            fap.addEventListener("input", aplicarMascaraAliquotas);
        }

        if (aliqRatAjust && !aliqRatAjust.getAttribute("decimals")) {
            aliqRatAjust.setAttribute("decimals", "4");
            aliqRatAjust.addEventListener("input", aplicarMascaraAliquotas);
        }

    }

    var controlarBotoes = function(event) {
      DBAutoCompleteEsocial.gerarAutoComplete();
      DBViewRespostaNula.adicionaRespostaNula(viewAvaliacao);
      formataAliquotas();
      var status = this.getStatus();

      $('proximo').disabled  = true;
      $('anterior').disabled = true;
      $('salvar').disabled   = true;

      if (status.grupoPosterior) {
        $('proximo').disabled = false;
      }

      if (status.grupoAnterior) {
        $('anterior').disabled = false;
      }

      if (status.grupoAtual) {
        $('salvar').disabled = false;
      }
    };
  </script>
  <?php
    db_menu();
    if(!empty($sMsg)) {
      db_msgbox($sMsg);
    }
  ?>
</body>
