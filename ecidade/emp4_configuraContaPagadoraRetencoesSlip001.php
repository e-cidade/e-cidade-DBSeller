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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

$oGet = db_utils::postMemory($_GET);
if (empty($oGet->json)) {
	db_redireciona('db_erros.php?fechar=true&db_erro=Movimentos do Empenho não Informados.');
}
$oJson    = new services_json();
$oParam   = $oJson->decode(str_replace("\\","",$oGet->json));

$oDaoRetencaoReceitas = new cl_retencaoreceitas();
$oAgendaPagamento = new agendaPagamento();

$sCredor = "";

$lPossuiRetencao = false;

?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <?
  db_app::load("scripts.js");
  db_app::load("prototype.js");
  db_app::load("datagrid.widget.js");
  db_app::load("strings.js");
  db_app::load("grid.style.css");
  db_app::load("estilos.css");
  ?>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
<div class="container">
  <form name='form1' id="form1">
   <fieldset style="width: 90%">
     <legend><b>Retenções Lançadas</b></legend>
       <table>
             <?
               $sHashCredor = "";

               foreach($oParam->MovimentosRetencoes as $oMovimentos) {

                /**
                 * validamos a taberec.k02_tipo = 'E'
                 * pois deve gerar slip somente das extras
                 */
               	  $sSqlRetencoes = "select e27_empagemov,
               	                           e20_pagordem,
               	                           e23_sequencial,
               	                           e23_retencaotiporec,
               	                           e21_descricao,
               	                           e23_valorbase,
               	                           e23_aliquota,
               	                           e23_valorretencao,
               	                           case
               	                             when cgm2.z01_numcgm is not null
               	                               then cgm2.z01_numcgm
               	                             else cgm1.z01_numcgm
               	                           end as z01_numcgm,
                                           case
                                             when trim(cgm2.z01_nome) is not null
                                               then cgm2.z01_nome
                                             else cgm1.z01_nome
                                           end as z01_nome,
                                           case
                                             when trim(cgm2.z01_cgccpf) is not null
                                               then cgm2.z01_cgccpf
                                             else cgm1.z01_cgccpf
                                           end as z01_cgccpf
               	  		              from retencaoreceitas
               	  		                   inner join retencaotiporec                 on retencaotiporec.e21_sequencial          = retencaoreceitas.e23_retencaotiporec
               	  		                   inner join retencaopagordem                on retencaopagordem.e20_sequencial         = retencaoreceitas.e23_retencaopagordem
               	  		                   inner join retencaoempagemov               on retencaoempagemov.e27_retencaoreceitas  = retencaoreceitas.e23_sequencial
               	  		                   inner join empagemov                       on empagemov.e81_codmov                    = retencaoempagemov.e27_empagemov
               	  		                   inner join empempenho                      on empempenho.e60_numemp                   = empagemov.e81_numemp
               	  		                   inner join cgm cgm1                        on cgm1.z01_numcgm                         = empempenho.e60_numcgm
               	  		                    left join pagordemconta                   on pagordemconta.e49_codord                = retencaopagordem.e20_pagordem
               	  		                    left join cgm cgm2                        on cgm2.z01_numcgm                         = pagordemconta.e49_numcgm
                                            join tabrec on e21_receita = k02_codigo
                                                       and k02_tipo = 'E'
               	  		             where retencaoempagemov.e27_empagemov = {$oMovimentos->iCodMov}
               	  		               and e23_ativo is true
               	  		               and e23_recolhido is false
               	  		               and e27_principal is true
               	  		             order by retencaoempagemov.e27_empagemov, retencaoreceitas.e23_sequencial";

               	  $rsDadosRetencoes = $oDaoRetencaoReceitas->sql_record($sSqlRetencoes);
               	  $iQtdretencoes =  $oDaoRetencaoReceitas->numrows;

               	  if ($iQtdretencoes > 0) {
               	  	$lPossuiRetencao = true;
               	  	echo "<script>parent.db_iframe_slipretencao.show();</script>";
               	  }

               	  for ($iRetencao = 0; $iRetencao < $iQtdretencoes; $iRetencao++) {

                  	$oDadosRetencao = db_utils::fieldsMemory($rsDadosRetencoes, $iRetencao);

                  	$sIdCredor = "{$oDadosRetencao->z01_numcgm}|{$oDadosRetencao->z01_nome}";
                  	if ($sHashCredor != $sIdCredor || $sHashCredor == "") {

                  		$sStrCredor = "{$oDadosRetencao->z01_numcgm} - ";
                  		$sStrCredor .= "{$oDadosRetencao->z01_nome} - ";
                  		$sStrCredor .= db_formatar($oDadosRetencao->z01_cgccpf, (strlen($oDadosRetencao->z01_cgccpf) > 11?"cnpj":"cpf"));

                  		echo "<tr>
                                <td colspan=\"7\"> <b>Credor:</b> {$sStrCredor} </td>
                              </tr>
                              <tr class=\"table_header\">
                                <td> OP. </td>
                                <td> Movimento </td>
                                <td> Cód. Retenção </td>
                                <td> Descr. Retenção </td>
                                <td> Base de Calculo </td>
                                <td> Aliquota </td>
                                <td> Valor Retido </td>
                                <td> Conta Pagadora </td>
                              </tr>";
                  	}
                  	$sHashCredor = $sIdCredor;

                    echo "<tr class=\"normal\">
                             <td align=\"center\" width=\"5%\"> {$oDadosRetencao->e20_pagordem} </td>
                             <td align=\"center\" width=\"5%\"> {$oDadosRetencao->e27_empagemov} </td>
                             <td align=\"center\" width=\"8%\"> {$oDadosRetencao->e23_sequencial} </td>
                             <td align=\"left\" width=\"30%\"> {$oDadosRetencao->e21_descricao} </td>
                             <td align=\"right\" width=\"10%\"> ".db_formatar($oDadosRetencao->e23_valorbase, 'f')." </td>
                             <td align=\"right\" width=\"5%\"> ".db_formatar($oDadosRetencao->e23_aliquota, 'f')." </td>
                             <td align=\"right\" width=\"10%\"> ".db_formatar($oDadosRetencao->e23_valorretencao, 'f')." </td>
                             <td align=\"center\" width=\"27%\"> ";




                    if (ParametroCaixa::getParametroMomentoSlipAutomatico(db_getsession("DB_instit"))  == 2 && !ParametroCaixa::getUtilizaContaExtraOrcamentaria(db_getsession("DB_instit"))) {

                      $aContas = $oAgendaPagamento->getContasRecurso($oMovimentos->iCodNota);

                    } else {

                        $campos = "distinct
                                   k109_contaextra as  e83_conta ,
                                   c60_descr as e83_descr ,
                                   '' as e83_codtipo ,
                                   gestao as c61_codigo ,
                                   o15_recurso ";
                        $where = " substr(gestao, 2, 3) in ('860', '861','862', '869') ";
                        $aContas = contaTesouraria::getListaContasExtras($campos, $where);
                    }


                    echo "<select name='ctapag-{$oMovimentos->iCodMov}-{$oDadosRetencao->e23_sequencial}' ";
                    echo "        id='ctapag-{$oMovimentos->iCodMov}-{$oDadosRetencao->e23_sequencial}' ";
                    echo "        style='width:100%'>";
                    foreach($aContas as $oConta) {

                        $sDescrConta = "{$oConta->e83_conta} - {$oConta->e83_descr} - {$oConta->c61_codigo}";

                        echo "<option ".(($oConta->e83_conta == $oMovimentos->iContaSaltes)?" selected ":" ");
                        echo "value ='{$oConta->e83_conta}'> $sDescrConta </option>";

                    }
                    echo "</select>";
                    echo "   </td>
                          </tr>";




                  }
               }
             ?>
       </table>
   </fieldset>
   <input type="button" value="Confirmar" name="confirmar" onclick="js_confirmaContasRetencoesSlip()">
   <input type="button" value="Cancelar" onclick="js_cancelar()">
   </form>
  </div>
</body>
</html>
<script>
  <?php
	if ($lPossuiRetencao == false) {
	  echo "parent.js_configurar();";
	}
  ?>
  parent.document.form1.dadosMovimentosRetencoesSlip.value = "";

  function js_confirmaContasRetencoesSlip() {

	  /* criamos um objeto json e enviamos para o parent. para que sejam gerados os slips */
	  var aRetorno = new Array();
	  var sHashMovimento = "";
	  /* precorremos o formulário */
 	  for (var i = 0; i < $("form1").length; i++) {

 		 /* ignnoramos os objetos do formulário que não forem do tipo select */
 		 if ($("form1")[i].type != "select-one") {
 	 	   continue;
 		 }

 		 var oDadosRetencoes = new Object();
 		 oDadosRetencoes.iRetencao      = "";
 		 oDadosRetencoes.iContaPagadora = "";

		 sId    = $("form1")[i].id;
		 iMovimento     = sId.split('-')[1].trim();
		 iRetencao      = sId.split('-')[2].trim();
		 iContaPagadora = $("form1")[i].value;

		 if (sHashMovimento != iMovimento) {

		   if (sHashMovimento!= "") {
		     aRetorno.push(oRetencoes);
		   }

		   var oRetencoes = new Object();
		   oRetencoes.iCodMov    = iMovimento;
		   oRetencoes.aRetencoes = new Array();

		 }
		 sHashMovimento = iMovimento;

		 oDadosRetencoes.iRetencao      = iRetencao;
		 oDadosRetencoes.iContaPagadora = iContaPagadora;

		 oRetencoes.aRetencoes.push(oDadosRetencoes);

	  }
 	 aRetorno.push(oRetencoes);

 	 parent.document.form1.dadosMovimentosRetencoesSlip.value = Object.toJSON(aRetorno);
	 parent.js_configurar();
	 parent.db_iframe_slipretencao.hide();
  }

  function js_cancelar() {

	  /* fechamos a janela e os movimentos não são atualziado */
	  parent.document.form1.dadosMovimentosRetencoesSlip.value = "";
	  parent.db_iframe_slipretencao.hide();

  }
</script>
