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
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_libsys.php"));
require_once(modification("dbforms/db_funcoes.php"));

require_once(modification("classes/db_liclicitaata_classe.php"));
require_once(modification('dbagata/classes/core/AgataAPI.class'));
require_once(modification("model/documentoTemplate.model.php"));

ini_set("error_reporting","E_ALL & ~NOTICE");

$oGet = db_utils::postMemory($_GET);

if ( isset($oGet->lPosicaoInicial) && $oGet->lPosicaoInicial == "t" ) {
  
  $clliclicitaata    = new cl_liclicitaata;

  $sMsg  = "Licitação {$oGet->iLicitacao} julgada sem vinculo com modelo de ata, "; 
  $sMsg .= "para gerar ata escolher a opção posição ATUALIZADA.                  ";
  
  $sWhere            = "l39_liclicita = {$oGet->iLicitacao} and l39_posicaoinicial is true";
  $sSqlLicLicitaAta  = $clliclicitaata->sql_query_file(null, "*", null, $sWhere);
  $rsSqlLicLicitaAta = $clliclicitaata->sql_record($sSqlLicLicitaAta);
  if ($clliclicitaata->numrows > 0) {
  	
    $oLicLicitaAta = db_utils::fieldsMemory($rsSqlLicLicitaAta, 0);
    
	  db_inicio_transacao();
	   
	  $sCaminhoSalvoSxw = "tmp/salvo_julgamento_{$oLicLicitaAta->l39_arqnome}";
	  $oOpenFile        = pg_lo_open($conn, $oLicLicitaAta->l39_arquivo, "r");
	  if ($oOpenFile) {
	    $oDadosOid = pg_lo_read($oOpenFile, 999999);
	  } else {
	    
	    db_fim_transacao(true);
	    
	    db_redireciona("db_erros.php?fechar=true&db_erro={$sMsg}");
	  }
	    
	  $oFile = fopen($sCaminhoSalvoSxw, "w+");
	    
	  fwrite($oFile, $oDadosOid);
	  fclose($oFile);
	   
	  db_fim_transacao();
	  db_redireciona($sCaminhoSalvoSxw);
  } else {
   	db_redireciona("db_erros.php?fechar=true&db_erro={$sMsg}");
  }
} else {

	try {
		$oDocumentoTemplate = new documentoTemplate(5,$oGet->iCodDocumento,'',false,'docx'); 
	} catch (Exception $eException){
		$sErroMsg  = $eException->getMessage();
	  db_redireciona("db_erros.php?fechar=true&db_erro={$sErroMsg}");
	}

	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($oDocumentoTemplate->getArquivoTemplate());
	$sCaminhoSalvoArq  = "tmp/ata_licitacao_{$oGet->iLicitacao}.docx";

	$oLicitacao        = new licitacao($oGet->iLicitacao);
	$oInstituicao      = new Instituicao(db_getsession("DB_instit"));
	$oFornecedor       = new fornecedor($oGet->iFornecedor);
	
	$oDadosRepreLegal  = $oFornecedor->getRepresentanteLegal();
	$oDadosLic         = $oLicitacao->getDados();
	
	$aLicitacao[]      = $oGet->iLicitacao;
	
	$fValorTotal       = 0;
	$sNomePref         = db_stdClass::getDadosInstit(db_getsession("DB_instit"))->pref;
	$sDescrModalidade  = $oLicitacao->getDescricaoModalidade($oDadosLic->l20_codtipocom)->l03_descr;
	$sDataAtual        = date('d').' de '.db_mes(date('m')).' de '.date('Y');
	$iCpfRepresLegal   = "";
	$iIdentRepresLegal = "";
	$sNomeRepresentanteLegal = "";

	if($oDadosLic->l34_protprocesso != ""){
		$sProcessoAdm = $oDadosLic->p58_numero.'/'.$oDadosLic->p58_ano;	
	}else{
		$sProcessoAdm = $oDadosLic->l20_procadmin;
	}
	
	if($oDadosRepreLegal != null){
		$sNomeRepresentanteLegal = $oDadosRepreLegal->getNome();
		if($oDadosRepreLegal->isJuridico()){
			$iCpfRepresLegal = $oDadosRepreLegal->getCnpj();
			$iIdentRepresLegal = "";
		}else{
			$iCpfRepresLegal = $oDadosRepreLegal->getCpf();
			$iIdentRepresLegal = $oDadosRepreLegal->getIdentidade();
		}
	}
	
    $sVariaveisDoc = array(
							"munic_instituicao"       => $oInstituicao->getMunicipio(),
							"processo_adm"            => $sProcessoAdm,
							"descr_modalidade"        => $sDescrModalidade,
							"numero_licitacao"        => $oDadosLic->l20_numero,
							"ano_licitacao"           => $oDadosLic->l20_anousu,
							"nome_prefeito"           => $sNomePref,
							"endereco_instituicao"    => $oInstituicao->getLogradouro(),
							"numero_instituicao"      => $oInstituicao->getNumero(),
							"bairro_instituicao"      => $oInstituicao->getBairro(),
							"cep_instituicao"         => $oInstituicao->getCep(),
							"nome_fornecedor"         => $oFornecedor->getCgm()->getNome(),
							"cnpj_fornecedor"         => $oFornecedor->getCgm()->getCnpj(),
							"endereco_fornecedor"     => $oFornecedor->getCgm()->getLogradouro(),
							"numero_fornecedor"       => $oFornecedor->getCgm()->getNumero(),
							"bairro_fornecedor"       => $oFornecedor->getCgm()->getBairro(),
							"cep_fornecedor"          => $oFornecedor->getCgm()->getCep(),
							"cidade_fornecedor"       => $oFornecedor->getCgm()->getMunicipio(),
							"uf_fornecedor"           => $oFornecedor->getCgm()->getUf(),
							"representante_legal"     => $sNomeRepresentanteLegal,
							"cpf_representante_legal" => $iCpfRepresLegal,
							"rg_presentante_legal"    => $iIdentRepresLegal,
							"objeto"                  => $oDadosLic->l20_objeto,
							"elemento_despesa"        => "",
							"data_atual"              => $sDataAtual
	                      );
						  
	$templateProcessor->setValues($sVariaveisDoc,'');

	$aItensLicitacao = $oLicitacao::getItensPorFornecedor($aLicitacao,$oGet->iFornecedor,false,false);
	 
        $templateProcessor->cloneRow('item', count($aItensLicitacao));						  

	for($i=0;$i < count($aItensLicitacao);$i++){

		$iLinha = $i+1;
		
		$templateProcessor->setValue("item#{$iLinha}", $aItensLicitacao[$i]->ordem);
		$templateProcessor->setValue("und#{$iLinha}", $aItensLicitacao[$i]->unidade);
		$templateProcessor->setValue("quant#{$iLinha}", $aItensLicitacao[$i]->quantidade);
		$templateProcessor->setValue("desc#{$iLinha}", $aItensLicitacao[$i]->material);
		$templateProcessor->setValue("observacao#{$iLinha}", $aItensLicitacao[$i]->observacao);
		$templateProcessor->setValue("valor_unitario#{$iLinha}", db_formatar($aItensLicitacao[$i]->valorunitario,'f'));
		$templateProcessor->setValue("valor_total_item#{$iLinha}", db_formatar($aItensLicitacao[$i]->valortotal,'f'));

		$fValorTotal += $aItensLicitacao[$i]->valortotal;
		
	}
	
	$templateProcessor->setValue('valor_total_licitado', db_formatar($fValorTotal,'f'));
	$templateProcessor->setValue('valor_total', db_formatar($fValorTotal,'f'));

	$templateProcessor->saveAs($sCaminhoSalvoArq);
	db_redireciona($sCaminhoSalvoArq);
	
}
?>
