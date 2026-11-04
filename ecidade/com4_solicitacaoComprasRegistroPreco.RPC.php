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
require_once(modification("std/db_stdClass.php"));
require_once(modification("model/aberturaRegistroPreco.model.php"));
require_once(modification("model/estimativaRegistroPreco.model.php"));
require_once(modification("model/compilacaoRegistroPreco.model.php"));
require_once(modification("model/ItemEstimativa.model.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));

$oJson             = new services_json();
$oParam            = $oJson->decode(db_stdClass::db_stripTagsJson(str_replace("\\","",$_POST["json"])));
$oRetorno          = new stdClass();
$oRetorno->status  = 1;
$oRetorno->message = '';
$oRetorno->erro    = false;

switch ($oParam->exec) {
	/*
	 * case para gerar um arquivo CSV de uma estimativa de registro de preço
	 */
	case 'gerarestimativaCSV':

		$iAbertura      = $oParam->iAbertura;
		$sArquivo       = "tmp/abertura_{$iAbertura}.csv";
		$aDepartamentos = array();

		try{

			$oAbertura            = new aberturaRegistroPreco($iAbertura);
			$aItensAbertura       = $oAbertura->getItens();
      $aEstimativas         = $oAbertura->getEstimativas();
      $aCodigoDepartamentos = array();

      $aHeaderArquivo       = array(0 => "RESUMO DAS ESTIMATIVAS - ABERTURA REGISTRO DE PREÇO {$iAbertura} ");


			//Define objeto standart para representar a abertura com suas estimativas
			$oAbertura   						   						= new stdClass();
			$oAbertura->aItens				 						= array();
			$oAbertura->aDepartamentosEstimativas = array();

      // Busca estimativas anuladas
      $aIdsEstimativas = array();

      foreach ($aEstimativas as $oEstimativa) {
        $aIdsEstimativas[] = $oEstimativa->getCodigoSolicitacao();
      }

      $sIdsEstimativas = implode(',', $aIdsEstimativas);

      $sSqlAnuladas = "
        SELECT
          pc67_solicita
        FROM
          solicitaanulada
        WHERE
          pc67_solicita IN ({$sIdsEstimativas})
      ";

      $rsAnuladas = db_query($sSqlAnuladas);
      $aEstimativasAnuladas = array();

      while ($row = pg_fetch_assoc($rsAnuladas)) {
        $aEstimativasAnuladas[$row['pc67_solicita']] = true;
      }

      // percorre as estimativas de cada departamento
			foreach ($aEstimativas as $oEstimativa) {

        if (array_key_exists($oEstimativa->getCodigoSolicitacao(), $aEstimativasAnuladas)) {
          continue;
        }

				$aItensEstimativa                                     = $oEstimativa->getItens();
				$iDepartamento                                        = $oEstimativa->getCodigoDepartamento();
				$oDepartamento                                        = new DBDepartamento($iDepartamento);
				$sDepartamento                                        = $oDepartamento->getNomeDepartamento();
				$oAbertura->aDepartamentosEstimativas[$iDepartamento] = $sDepartamento;
				$aCodigoDepartamentos[$iDepartamento] 								= 0;
				$iQuantidadeItem = 0;

        //Totaliza as quantidades de cada item, por departamento
				foreach ($aItensEstimativa as $oItemEstimativa) {

          $sIndice = $oItemEstimativa->getOrdem()."|".$oItemEstimativa->getCodigoMaterial();
          if (empty($oAbertura->aItens[$sIndice])) {

            $oStdItemAbertura                  = new stdClass();
            $oStdItemAbertura->sDescricao      = urldecode($oItemEstimativa->getDescricaoMaterial());
            $oStdItemAbertura->sResumo         = urldecode($oItemEstimativa->getResumo());
            $oStdItemAbertura->aDepartamentos  = array();
            $oStdItemAbertura->iTotal          = 0;
            $oAbertura->aItens[$sIndice] =  $oStdItemAbertura;
          }

          $oAbertura->aItens[$sIndice]->aDepartamentos[$iDepartamento] = $oItemEstimativa->getQuantidade();
          $oAbertura->aItens[$sIndice]->iTotal                        += $oItemEstimativa->getQuantidade();
				}
			}

			/**
			 * Monta csv
			 */
			$fArquivo 				   = fopen($sArquivo, "w");
 			$aDepartamentos      = $oAbertura->aDepartamentosEstimativas;
            $aDepartamentos['0'] = "DESCRIÇÃO";

            if (!empty($oParam->exibeResumoItens)) {
                $aDepartamentos['1'] = "RESUMO";
            }

		    $aDepartamentos[] = "TOTAL";
			ksort($aDepartamentos);
			fputcsv($fArquivo,$aHeaderArquivo, ";");
			fputcsv($fArquivo, $aDepartamentos, ";");

      foreach ($oAbertura->aItens as $oItem) {

        $sDescricao            = $oItem->sDescricao;
        $sResumo               = $oItem->sResumo;
        $aQuantidades          = $oItem->aDepartamentos + $aCodigoDepartamentos;
        $aLinha                = array();
        $aLinha[]              = $sDescricao;

        if (!empty($oParam->exibeResumoItens)) {
            $aLinha[] = $sResumo;
        }

        ksort($aQuantidades);
        $aLinha               += $aQuantidades;
        $aLinha[]              = $oItem->iTotal;
        fputcsv($fArquivo, $aLinha, ";");
      }
			fclose($fArquivo);
			$oRetorno->sArquivo = $sArquivo;

		} catch (Exception $eErro) {

			db_fim_transacao(true);
			$oRetorno->message = urlencode($eErro->getMessage());
			$oRetorno->status  = 2;

		}

	break;


  case "salvarCompilacao":

    db_inicio_transacao();
    try {
      /**
       * Tem o código da Abertura 142374
       * Precisa encontrar a Estimativa (procurar na tabela solicitavinculo)
       * Pegar as quantidades dos itens das estimativas
       *  */

      $sql = "
        SELECT
          pc11_quant,
          pc67_sequencial
        FROM
          solicita
        INNER JOIN solicitavinculo
          ON pc53_solicitafilho = pc10_numero
        LEFT JOIN solicitaanulada
          ON pc67_solicita = pc10_numero
        INNER JOIN solicitem
          ON pc11_numero = pc53_solicitafilho
        WHERE
          pc53_solicitapai = {$oParam->iAbertura}
      ";

      $rs = db_query($sql);

      $quantidadeTotal = 0;
      $flagTodasEstimativasAnuladas = true;

      while ($row = pg_fetch_assoc($rs)) {
        $quantidadeTotal += (int) $row['pc11_quant'];

        if (empty($row['pc67_sequencial'])) {
          $flagTodasEstimativasAnuladas = false;
        }
      }

      if($quantidadeTotal == 0) {
        throw new Exception("Estimativa(s) lançada(s) sem quantidade.");
      }

      if($flagTodasEstimativasAnuladas) {
        throw new Exception("Todas estimativas encontram-se anuladas.");
      }

      if (isset($_SESSION["oSolicita"]) && $_SESSION["oSolicita"] instanceof compilacaoRegistroPreco) {
        $oSolicita = $_SESSION["oSolicita"];
      } else {
        $oSolicita = new compilacaoRegistroPreco();
      }

      $oSolicita->setLiberado($oParam->liberado);
      $oSolicita->setResumo(utf8_decode($oParam->resumo));
      $oSolicita->setDataInicio($oParam->datainicio);
      $oSolicita->setDataTermino($oParam->datatermino);
      $oSolicita->setCodigoAbertura($oParam->iAbertura);
      $oSolicita->save();

      $oRetorno->iCodigoSolicita = $oSolicita->getCodigoSolicitacao();
      $oRetorno->resumo = urlencode($oSolicita->getResumo());

      $_SESSION["oSolicita"] = $oSolicita;
      $aItens = $oSolicita->getItens();

      foreach ($aItens as $iIndice => $oItem) {

        $oItemRetono = new stdClass;
        $oItemRetono->codigoitem     = $oItem->getCodigoMaterial();
        $oItemRetono->descricaoitem  = $oItem->getDescricaoMaterial();
        $oItemRetono->quantidade     = $oItem->getQuantidade();
        $oItemRetono->quantidadeunid = $oItem->getQuantidadeUnidade();
        $oItemRetono->qtdemin        = $oItem->getQuantidadeMinima();
        $oItemRetono->qtdemax        = $oItem->getQuantidadeMaxima();
        $oItemRetono->automatico     = $oItem->isAutomatico();
        $oItemRetono->resumo         = urlencode($oItem->getResumo());
        $oItemRetono->justificativa  = urlencode($oItem->getJustificativa());
        $oItemRetono->prazo          = urlencode($oItem->getPrazos());
        $oItemRetono->pagamento      = urlencode($oItem->getPagamento());
        $oItemRetono->unidade        = $oItem->getUnidade();
        $oItemRetono->unidade_descr  = $oItem::getDescricaoUnidade($oItem->getUnidade());
        $oItemRetono->indice         = $iIndice;
        $oItemRetono->ativo          = $oItem->isAtivo();
        $oRetorno->itens[] = $oItemRetono;

      }
      db_fim_transacao(false);

    } catch (Exception $eErro) {

      db_fim_transacao(true);
      $oRetorno->message = urlencode($eErro->getMessage());
      $oRetorno->status  = 2;

    }
    break;

    break;
  case "salvarItensAbertura":
    try {

      db_inicio_transacao();
      $oSolicita = $_SESSION["oSolicita"];
      $oItens    = $oSolicita->getItens();
      foreach ($oParam->aItens as $oItemSolicitacao) {

        if (isset($oItens[$oItemSolicitacao->iIndice])) {
          $oItens[$oItemSolicitacao->iIndice]->setAtivo($oItemSolicitacao->lAtivo);
        }
      }
      $oSolicita->save();
      db_fim_transacao(false);
    } catch (Exception $eErro) {

      db_fim_transacao(true);
      $oRetorno->status  = 2;
      $oRetorno->message = urlencode($eErro->getMessage());
    }
    break;

  case "clearSession":

    unset($_SESSION["oSolicita"]);
    break;

  case "pesquisarAbertura":

    try {

      $oSolicita             = new compilacaoRegistroPreco($oParam->iSolicitacao);
      $_SESSION["oSolicita"] = $oSolicita;
      $aitens = $oSolicita->getItens();
      foreach ($aitens as $iIndice => $oItem) {

        $oItemRetono = new stdClass;
        $oItemRetono->codigoitem     = $oItem->getCodigoMaterial();
        $oItemRetono->descricaoitem  = urlencode(urldecode($oItem->getDescricaoMaterial()));
        $oItemRetono->quantidade     = $oItem->getQuantidade();
        $oItemRetono->quantidadeunid = $oItem->getQuantidadeUnidade();
        $oItemRetono->qtdemin        = $oItem->getQuantidadeMinima();
        $oItemRetono->qtdemax        = $oItem->getQuantidadeMaxima();
        $oItemRetono->automatico     = $oItem->isAutomatico();
        $oItemRetono->resumo         = $oItem->getResumo();
        $oItemRetono->justificativa  = $oItem->getJustificativa();
        $oItemRetono->prazo          = $oItem->getPrazos();
        $oItemRetono->valor_unitario = $oItem->getValorUnitario();
        $oItemRetono->valor_total    = $oItem->getValorTotal();
        $oItemRetono->pagamento      = $oItem->getPagamento();
        $oItemRetono->unidade        = $oItem->getUnidade();
        $oItemRetono->unidade_descr  = $oItem::getDescricaoUnidade($oItem->getUnidade());
        $oItemRetono->indice         = $iIndice;
        $oItemRetono->ativo          = $oItem->isAtivo();
        $oRetorno->itens[] = $oItemRetono;

      }
      $oRetorno->datainicio     = db_formatar($oSolicita->getDataInicio(),"d");
      $oRetorno->datatermino    = db_formatar($oSolicita->getDataTermino(),"d");
      $oRetorno->liberado       = $oSolicita->isLiberado();
      $oRetorno->resumo         = urlencode($oSolicita->getResumo());
      $oRetorno->formacontrole  = $oSolicita->getFormaDeControle();
      $oRetorno->codigoabertura = $oSolicita->getCodigoAbertura();
      $oRetorno->solicitacao    = $oSolicita->getCodigoSolicitacao();


    } catch (Exception $eErro) {

      $oRetorno->status  = 2;
      $oRetorno->message = urlencode($eErro->getMessage());

    }
    break;

     break;

   case "salvarItensValor" :

     try {

       db_inicio_transacao();
       $oSolicita = $_SESSION["oSolicita"];
       $aitens = $oSolicita->getItens();
       if (isset($aitens[$oParam->iIndice])) {
         if ($oParam->iTipo == 1) {
           $aitens[$oParam->iIndice]->setQuantidadeMinima($oParam->quantidade);
         } else {
           $aitens[$oParam->iIndice]->setQuantidadeMaxima($oParam->quantidade);
         }
       }
       db_fim_transacao(true);
     } catch (Exception $eErro) {
       db_fim_transacao(true);
     }
     break;

   case "alterarItem" :

    try {

      $oSolicita =  $_SESSION["oSolicita"];
      $aItens    = $oSolicita->getItens();
      $oItem     = $aItens[$oParam->iIndice];
      if (!$oItem->isAutomatico()) {

        $oItem->setResumo(utf8_decode(urldecode($oParam->sResumo)));
        $oItem->setJustificativa(utf8_decode(urldecode($oParam->sJustificativa)));
        $oItem->setPagamento(utf8_decode(urldecode($oParam->sPgto)));
        $oItem->setPrazos(utf8_decode(urldecode($oParam->sPrazo)));
        $oItem->setUnidade($oParam->iUnidade);
        $oItem->setQuantidadeUnidade($oParam->nQuantUnidade);
        $aitens = $oSolicita->getItens();

      }
      foreach ($aitens as $iIndice => $oItem) {

        $oItemRetono = new stdClass;
        $oItemRetono->codigoitem     = $oItem->getCodigoMaterial();
        $oItemRetono->descricaoitem  = $oItem->getDescricaoMaterial();
        $oItemRetono->quantidade     = $oItem->getQuantidade();
        $oItemRetono->quantidadeunid = $oItem->getQuantidadeUnidade();
        $oItemRetono->qtdemin        = $oItem->getQuantidadeMinima();
        $oItemRetono->qtdemax        = $oItem->getQuantidadeMaxima();
        $oItemRetono->automatico     = $oItem->isAutomatico();
        $oItemRetono->resumo         = $oItem->getResumo();
        $oItemRetono->justificativa  = $oItem->getJustificativa();
        $oItemRetono->prazo          = $oItem->getPrazos();
        $oItemRetono->pagamento      = $oItem->getPagamento();
        $oItemRetono->unidade        = $oItem->getUnidade();
        $oItemRetono->indice         = $iIndice;
        $oItemRetono->ativo          = $oItem->isAtivo();
        $oRetorno->itens[] = $oItemRetono;

      }
    } catch (Exception $eErro) {

      $oRetorno->status  = 2;
      $oRetorno->message = urlencode($eErro->getMessage());

    }

    break;

  case 'geraProcessoCompras' :

    $oSolicita =  $_SESSION["oSolicita"];
    try {

      db_inicio_transacao();
      $oSolicita->toProcessoCompra();
      $oRetorno->iProcessoCompras = $oSolicita->getProcessodeCompras();
      db_fim_transacao(false);

    } catch (Exception $eErro) {

      db_fim_transacao(true);
      $oRetorno->status  = 2;
      $oRetorno->message = urlencode($eErro->getMessage());

    }
    break;

  case 'cancelaProcessoCompras' :

    $oSolicita =  $_SESSION["oSolicita"];
    try {

      db_inicio_transacao();
      $oSolicita->cancelarProcessoCompras();
      db_fim_transacao(false);

    } catch (Exception $eErro) {

      db_fim_transacao(true);
      $oRetorno->status  = 2;
      $oRetorno->message = urlencode($eErro->getMessage());

    }
    break;

  case 'anularAberturaRegistroPreco' :

  	try {

  		db_inicio_transacao();

  	  $sMotivo   = db_stdClass::normalizeStringJsonEscapeString($oParam->sMotivo);

  		$oSolicita = new aberturaRegistroPreco($oParam->iCodigoSolicitacao);
  		$oSolicita->anular($sMotivo);
  		db_fim_transacao(false);

  	} catch (Exception $eErro) {

  		db_fim_transacao(true);
  		$oRetorno->status = 2;
  		$oRetorno->message = urlencode($eErro->getMessage());
  	}


  	break;

  case 'anularEstimativaRegistroPreco' :

    try {

      db_inicio_transacao();

      $sMotivo   = db_stdClass::normalizeStringJsonEscapeString($oParam->sMotivo);

      $oSolicita = new estimativaRegistroPreco($oParam->iCodigoSolicitacao);
      $oSolicita->anular($sMotivo);
      db_fim_transacao(false);

    } catch (Exception $eErro) {

      db_fim_transacao(true);
      $oRetorno->status = 2;
      $oRetorno->message = urlencode($eErro->getMessage());
    }


    break;

  case 'anularCompilacaoRegistroPreco' :

    try {

      db_inicio_transacao();

      $sMotivo   = db_stdClass::normalizeStringJsonEscapeString($oParam->sMotivo);

      $oSolicita = new compilacaoRegistroPreco($oParam->iCodigoSolicitacao);
      $oSolicita->anular($sMotivo);
      db_fim_transacao(false);

    } catch (Exception $eErro) {

      db_fim_transacao(true);
      $oRetorno->status  = 2;
      $oRetorno->message = urlencode($eErro->getMessage());
    }


    break;

  case 'consAberturaDetalhes':

  	$aDados = false;

  	$oRetorno->detalhe = $oParam->detalhe;
  	$adados  = array();
  	if ($oParam->detalhe == 'estimativa') {

  		$oConsulta = new aberturaRegistroPreco($oParam->pc10_numero);
  		$aDados    = $oConsulta->getEstimativas();
  		foreach ($aDados as $oEstimativa) {

        if (!empty($oParam->apenasativas) && $oParam->apenasativas) {
          if ($oEstimativa->isAnulada()) {
            continue;
          }
        }
  			$oEst = new stdClass();

  			$oEst->codigo        = $oEstimativa->getCodigoSolicitacao();
  			$oEst->emissao       = $oEstimativa->getDataSolicitacao();
  			$oEst->anulacao      = $oEstimativa->getDataAnulacao();
  			$oEst->departamento  = $oEstimativa->getDepartamento() ."-". urlencode($oEstimativa->getDescricaoDepartamento());
  			$oInstituicao        = new Instituicao($oEstimativa->getCodigoInstituicao());
  			$oEst->sInstituicao  = $oInstituicao->getSequencial()."-".urlencode($oInstituicao->getDescricao());
  			$adados[]            = $oEst;
  		}

  	} else if ($oParam->detalhe == 'compilacao') {

      $oConsulta = new aberturaRegistroPreco($oParam->pc10_numero);
      $aDados    = $oConsulta->getCompilacoes();
  	  foreach ($aDados as $oCompilacao) {

        $oComp = new stdClass();

        $oComp->codigo           = $oCompilacao->getCodigoSolicitacao();
        $oComp->emissao          = $oCompilacao->getDataSolicitacao();
        $oComp->datainicial      = $oCompilacao->getDataInicio();
        $oComp->datafinal        = $oCompilacao->getDataTermino();
        $oComp->datacancelamento = $oCompilacao->getDataAnulacao();
        $oComp->departamento     = urlencode($oCompilacao->getDescricaoDepartamento());
        $oComp->processocompra   = $oCompilacao->getProcessodeCompras();
        $oComp->processamento    = urlencode($oComp->processocompra == null ? 'Não' : 'Sim');

        $adados[]             = $oComp;
      }

  	} else if ($oParam->detalhe == 'itens') {

      $oConsulta               = new aberturaRegistroPreco($oParam->pc10_numero);
      $aDados                  = $oConsulta->getItens();
      $oRetorno->formacontrole = $oConsulta->getFormaDeControle();
      $oRetorno->resumo        = urlencode($oConsulta->getResumo());
      /**
       * Utilizamos este mesmo item para preencher os itens da compilação, quando a mesma é por valor.
       * O valor Unitário é o valor de referencia do registro de preço
       */
      foreach ($aDados as $oItemAbertura) {

        $oItem = new stdClass();

        $oItem->codigo         = $oItemAbertura->getCodigoMaterial();
        $oItem->material       = urlencode($oItemAbertura->getDescricaoMaterial());
        $oItem->resumo         = $oItemAbertura->getResumo();
        $oItem->ordem          = $oItemAbertura->getOrdem();
        $oItem->valor_unitario = $oItemAbertura->getValorUnitario();
        $oItem->unidade        = urlencode($oItemAbertura->getDadosUnidade()->m61_descr);//getUnidade();
        $oItem->quantidadeUnidade = $oItemAbertura->getQuantidadeUnidade();
        $adados[]             = $oItem;
      }

    } else if ($oParam->detalhe == "getItensEstimativa") {

      $oConsulta = new estimativaRegistroPreco($oParam->pc10_numero);
      $aDados    = $oConsulta->getItens();
      foreach ($aDados as $oIt) {

        $oItem = new stdClass();
        $oItem->iCodItemSol    = $oIt->getCodigoItemSolicitacao();
        $oItem->codigo         = $oIt->getCodigoMaterial();
        $oItem->material       = urlencode($oIt->getDescricaoMaterial());
        $oItem->resumo         = $oIt->getResumo();
        $oItem->unidade        = urlencode($oIt->getDadosUnidade()->m61_descr);
        $oItem->quantidadeUnidade = $oIt->getQuantidadeUnidade();
        $oItem->quantidade     = $oIt->getQuantidade();
        $oQtdDisponiveis       = $oIt->getMovimentacao();
        $oItem->iQtdSaldo      = $oQtdDisponiveis->saldo;
        $oItem->iQtdTotal      = $oQtdDisponiveis->quantidade;
        $oItem->iQtdCedida     = $oQtdDisponiveis->cedidas;
        $oItem->iQtdRecebida   = $oQtdDisponiveis->recebidas;
        $oItem->iQtdSolicitada = $oQtdDisponiveis->solicitada;
        $oItem->iQtdEmpenhada  = $oQtdDisponiveis->empenhada;
        $oItem->iQtdeExecedido = $oQtdDisponiveis->execedente;

        $adados[]             = $oItem;
      }
    }  else if ($oParam->detalhe == "getItensCompilacao") {

      $oCompilacao             = new compilacaoRegistroPreco($oParam->pc10_numero);
      $aDados                  = $oCompilacao->getItens();
      $oRetorno->formacontrole = $oCompilacao->getFormaDeControle();
      foreach ($aDados as $oIt) {

        $oItem = new stdClass();
        $oItem->codigo        = $oIt->getCodigoMaterial();
        $oItem->material      = urlencode($oIt->getDescricaoMaterial());
        $oItem->resumo        = $oIt->getResumo();
        $oItem->unidade       = urlencode($oIt->getDadosUnidade()->m61_descr);
        $oItem->quantidadeUnidade = $oIt->getQuantidadeUnidade();
        $oItem->quantmax      = $oIt->getQuantidadeMaxima();
        $oItem->quantmin      = $oIt->getQuantidadeMinima();
        $oItem->valortotal    = $oIt->getValorTotal();
        $oDadosItem           = $oCompilacao->getFornecedorItem($oIt->getCodigoMaterial(),$oIt->getCodigoItemSolicitacao());
        $oItem->fornecedor    = $oDadosItem->codigocgm."-".$oDadosItem->vencedor;
        $oItem->valorunitario = $oDadosItem->valorunitario;
        $oItem->percentual    = $oDadosItem->percentualdesconto;
        $oItem->ativo         = $oIt->isAtivo();
        $oItem->automatico    = $oIt->isAutomatico();
        $adados[]             = $oItem;
      }

    }
    $oRetorno->dados = $adados;
  	break;

  /**
   * Pega os dados dos itens cedidos a um departamento
   */
  case "getItensCedidos":

    $oItemEstimativa = new ItemEstimativa($oParam->iCodItemSol);
    $aDadosItemCede  = $oItemEstimativa->getCedenciasRealizadas();
    $oRetorno->aDados = $aDadosItemCede;

  break;

  /**
   * Pega os dados dos itens recebidos de um departamento
   */
  case "getItensRecebidos":

    $oItemEstimativa  = new ItemEstimativa($oParam->iCodItemSol);
    $aDadosItemRecebe = $oItemEstimativa->getCedenciasRecebidas();
    $oRetorno->aDados = $aDadosItemRecebe;

  break;

  /**
   * Pesquisa a quantidade restante do item da estimativa para o departamento logado
   */
    case "getQuantidadeRestanteItemEstimativa" :

        try {

            $oRetorno->iQuantidadeRestante = 0;

            $sSqlItemEstimativa  = "select pc11_codigo";
            $sSqlItemEstimativa .= "  from solicitemvinculo";
            $sSqlItemEstimativa .= "       inner join solicitem on pc55_solicitempai = pc11_codigo";
            $sSqlItemEstimativa .= "       inner join solicita  on pc10_numero       = pc11_numero";
            $sSqlItemEstimativa .= "       left  join solicitaanulada on pc10_numero = pc67_solicita";
            $sSqlItemEstimativa .= " where pc10_depto = ".db_getsession("DB_coddepto");
            $sSqlItemEstimativa .= "   and pc55_solicitemfilho = {$oParam->iItemOrigem}";
            $sSqlItemEstimativa .= "   and pc67_solicita is null";
            $rsItemEstimativa    = db_query($sSqlItemEstimativa);

            /**
             * Nao encontrou item
             */
            if (!$rsItemEstimativa || pg_num_rows($rsItemEstimativa) == 0) {
                break;
            }

            $iCodigoItemEstimativa = db_utils::fieldsMemory($rsItemEstimativa, 0)->pc11_codigo;

            $oItemEstimativa = new ItemEstimativa($iCodigoItemEstimativa);
            $oSaldosItemEstimativa = $oItemEstimativa->getMovimentacao();

            $oItemCompilacao = new itemCompilacao($oParam->iItemOrigem);
            $oSaldosItemCompilacao = $oItemCompilacao->getSaldo();

            if ($oSaldosItemEstimativa->saldo <= $oSaldosItemCompilacao || !$oItemCompilacao->temReservaDeCota()) {
                $oRetorno->iQuantidadeRestante = $oSaldosItemEstimativa->saldo;
            } else {
                $oRetorno->iQuantidadeRestante = $oSaldosItemCompilacao;
            }
        } catch (Exception $erro) {
            $oRetorno->status  = 2;
            $oRetorno->message = $erro->getMessage();
            $oRetorno->erro    = true;
        }

        break;
    case "buscarSolicitacoesVinculadas":
        if (!empty($oParam->estimativa)) {
            $sqlSolicitacoesVinculadas  = "
                SELECT solicitacao.*,
                       solicitacaoanulada.pc67_solicita AS solicitacaoanulada,
                       db_usuarios.nome AS pc10_usuario, usuario_solicitante.nome AS pc10_usuariosolicitante
                FROM solicita AS estimativa
                JOIN solicitavinculo AS vinculoaberturaestimativa
                    ON vinculoaberturaestimativa.pc53_solicitafilho = estimativa.pc10_numero
                JOIN solicitavinculo AS vinculooaberturacompilacao
                    ON vinculooaberturacompilacao.pc53_solicitapai = vinculoaberturaestimativa.pc53_solicitapai
                JOIN solicitavinculo  AS vinculocompilacaosolicitacao
                    ON vinculocompilacaosolicitacao.pc53_solicitapai = vinculooaberturacompilacao.pc53_solicitafilho
                JOIN solicita AS solicitacao
                    ON solicitacao.pc10_numero = vinculocompilacaosolicitacao.pc53_solicitafilho
                        AND solicitacao.pc10_depto = estimativa.pc10_depto
                JOIN db_usuarios ON estimativa.pc10_login = id_usuario
                LEFT JOIN solicitaanulada estimativaanulada ON estimativa.pc10_numero = estimativaanulada.pc67_solicita
                LEFT JOIN solicitaanulada solicitacaoanulada ON solicitacao.pc10_numero = solicitacaoanulada.pc67_solicita
                JOIN db_usuarios AS usuario_solicitante ON solicitacao.pc10_login = usuario_solicitante.id_usuario
              WHERE estimativa.pc10_numero = {$oParam->estimativa}
                AND estimativaanulada.pc67_sequencial IS NULL
            ";

            $solicitacoesVinculadas = db_query($sqlSolicitacoesVinculadas);
            if (empty(pg_num_rows($solicitacoesVinculadas))) {
                break;
            }

            $solicitacoesVinculadas = pg_fetch_all($solicitacoesVinculadas);
            $solicitacoes = [];
            foreach ($solicitacoesVinculadas as $solicitacaoVinculada) {
                $solicitacao = [];
                $solicitacao['pc10_numero'] = $solicitacaoVinculada['pc10_numero'];
                $solicitacao['pc10_data'] = $solicitacaoVinculada['pc10_data'];
                $solicitacao['pc10_resumo'] = urlencode($solicitacaoVinculada['pc10_resumo']);
                $solicitacao['pc10_usuariosolicitante'] = urlencode($solicitacaoVinculada['pc10_usuariosolicitante']);
                $solicitacao['anulada'] = (bool) $solicitacaoVinculada['solicitacaoanulada'];

                $solicitacoes[] = $solicitacao;
            }

            $oRetorno->dados = $solicitacoes;
        }
        break;
}
echo $oJson->encode($oRetorno);
