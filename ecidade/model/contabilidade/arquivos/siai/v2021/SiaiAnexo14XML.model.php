<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2013  DBselller Servicos de Informatica
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
require_once(modification("model/contabilidade/arquivos/siai/SiaiArquivoBase.model.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("std/DBString.php"));

class SiaiAnexo14XML extends SiaiArquivoBase
{

    public $sListaEmpenho = "";
    public $sListaLiquidacao = "";
    public $sListaPagamento = "";
  
    function __construct()
    {
      
        $this->oDocumento = new DOMDocument('1.0', 'utf-8');
        $this->oDocumento->formatOutput = true;
        $this->oDocumento->encoding = 'utf-8';
    }
    
  /**
  * Busca os dados para gerar o Arquivo de Empenhos
  */
    public function gerarDados()
    {
      
        /* Padrão Nome 598_012020_Anexo14.xml */
        $sNomeArquivo =  $this->codigoOrgaoTCEXml . "_" . str_pad($this->getMes(), 2, STR_PAD_LEFT) . $this->getAno() . "_Anexo14.xml";
        $this->setNomeArquivo($sNomeArquivo);
      
        $oNodeAnexo14 = $this->oDocumento->createElement("Anexo14");
      
        $oDadosAnexo14 = new stdClass();
        $oDadosAnexo14->CodigoUnidadeJurisdicionada = $this->codigoOrgaoTCEXml;
        $oDadosAnexo14->DataPeriodoInicial          = $this->getAno()."-".$this->getMes()."-01";
        $oDadosAnexo14->DataPeriodoFinal            = $this->getAno()."-".$this->getMes()."-".DBDate::getQuantidadeDiasMes($this->getMes(), $this->getAno());
      
        $oDadosAnexo14->SistemaGerador              = "SOFTWARE PUBLICO BRASILEIRO";
        $this->addElementXML($oDadosAnexo14, $oNodeAnexo14);

        $oNodeListaPessoa                    = $this->oDocumento->createElement("ListaPessoa");
        $oNodeListaEmpenho                   = $this->oDocumento->createElement("ListaEmpenho");
        $oNodeListaAnulacaoEmpenho           = $this->oDocumento->createElement("ListaAnulacaoEmpenho");
        $oNodeListaLiquidacao                = $this->oDocumento->createElement("ListaLiquidacao");
        $oNodeListaAnulacaoLiquidacao        = $this->oDocumento->createElement("ListaAnulacaoLiquidacao");
        $oNodeListaPagamento                 = $this->oDocumento->createElement("ListaPagamento");
        $oNodeListaAnulacaoPagamento         = $this->oDocumento->createElement("ListaAnulacaoPagamento");
        $oNodeListaRetencaoPagamento         = $this->oDocumento->createElement("ListaRetencaoPagamento");
        $oNodeListaAnulacaoRetencaoPagamento = $this->oDocumento->createElement("ListaAnulacaoRetencaoPagamento");

      
        /*
         * Pessoas
         */
        $aPessoaProcessadas = array();
        
        $aDadosPessoa = $this->getDadosPessoas();
        if (count($aDadosPessoa) == 0) {
            throw new Exception("Nenhum registro de pessoas encontrado");
        }
        
        foreach ($aDadosPessoa as $oDados) {
            if (!in_array($oDados->z01_cgccpf, $aPessoaProcessadas)) {
                $oNodePessoa = $this->oDocumento->createElement("Pessoa");
              
                $oDadosPessoa = new stdClass();
                $oDadosPessoa->TipoDocumentoPessoa = ((strlen($oDados->z01_cgccpf) == 11 )? "1" : "2");
                $oDadosPessoa->NumeroDocumentoPessoa = $oDados->z01_cgccpf;
                $oDadosPessoa->NomePessoa = DBString::removerAcentuacao(DBString::removerCaracteresEspeciais($oDados->z01_nome));
                $this->addElementXML($oDadosPessoa, $oNodePessoa);
              
                $oNodeListaPessoa->appendChild($oNodePessoa);
              
                $aPessoaProcessadas[] = $oDados->z01_cgccpf;
            }
        }
        
      
        /*
         * Empenhos
         */
        $aDadosEmpenho = array();
        if (!empty($this->sListaEmpenho) || (empty($this->sListaEmpenho) && empty($this->sListaLiquidacao) && empty($this->sListaPagamento))) {
            $aDadosEmpenho = $this->getDadosEmpenhos();
            if (count($aDadosEmpenho) == 0) {
      //          throw new Exception("Nenhum registro de Empenho encontrado");
            }
        }
      
        foreach ($aDadosEmpenho as $oDados) {
            $oNodeEmpenho = $this->oDocumento->createElement("Empenho");
        
            $sNumeroClassificacaoInstitucional = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
            if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                $sNumeroClassificacaoInstitucional .= "220";
            } else {
                $sNumeroClassificacaoInstitucional .= str_pad($oDados->o58_unidade, 3, "0", STR_PAD_LEFT);
            }
        
            $oDadosEmpenho = new stdClass();
            $oDadosEmpenho->NumeroClassificacaoInstitucional = $sNumeroClassificacaoInstitucional;
            $oDadosEmpenho->NumeroProcessoDespesa            = $oDados->processo_despesa;
            $oDadosEmpenho->NumeroDocumentoEmpenho           = $oDados->e60_codemp;
            $oDadosEmpenho->TipoProcedimentoLicitatorio      = $oDados->codigolicitacaotribunal;
            $oDadosEmpenho->NumeroReciboAnexo38              = ((empty($oDados->numeroprocessolicitatorio)||$oDados->numeroprocessolicitatorio=="0")?null:$oDados->numeroprocessolicitatorio);
            $lExisteAcordoReciboAnexo = PostgreSQLUtils::isTableExists("acordoreciboanexo");
            $oDadosEmpenho->NumeroReciboSiaiObras            = null;
            if (!!$lExisteAcordoReciboAnexo) {
                $oDadosEmpenho->NumeroReciboSiaiObras          = ((empty($oDados->anexo23)||$oDados->anexo23=="0")?null:$oDados->anexo23);
            }
            $oDadosEmpenho->TipoEmpenho                      = $oDados->e60_codtipo;
            $oDadosEmpenho->DataEmpenho                      = $oDados->e60_emiss;
            $oDadosEmpenho->ValorEmpenho                     = $oDados->e60_vlremp;
            $oDadosEmpenho->CodigoInstrumentoPrograma        = $oDados->o58_projativ;
            $this->addElementXML($oDadosEmpenho, $oNodeEmpenho);
        

      /*
      ALTERAÇÃO REALIZADA A PEDIDO DE RONALDO / EMENTARIO DE DESPESA DO SIAI NÃO TEM O ELEMENTO 319009 PARA 2021
      */
            if (substr($oDados->o56_elemento, 1, 6) == "319009") {
                  $oDados->o56_elemento = "3319011990000";
            }
    
            $oNodeEmpenhoNaturezaDespesa = $this->oDocumento->createElement("NaturezaDespesa");
            $oDadosEmpenhoNaturezaDespesa = new stdClass();
            $oDadosEmpenhoNaturezaDespesa->CategoriaEconomica   = substr($oDados->o56_elemento, 1, 1);
            $oDadosEmpenhoNaturezaDespesa->GrupoDespesa         = substr($oDados->o56_elemento, 2, 1);
            $oDadosEmpenhoNaturezaDespesa->ModalidadeAplicacao  = substr($oDados->o56_elemento, 3, 2);
            $oDadosEmpenhoNaturezaDespesa->ElementoDespesa      = substr($oDados->o56_elemento, 5, 2);
            $oDadosEmpenhoNaturezaDespesa->DesdobramentoDespesa = "00";//substr($oDados->o56_elemento,7,2);
            $this->addElementXML($oDadosEmpenhoNaturezaDespesa, $oNodeEmpenhoNaturezaDespesa);
          
            $oNodeEmpenho->appendChild($oNodeEmpenhoNaturezaDespesa);
        
            $sJustificativa = substr(DBString::removerAcentuacao(DBString::removerCaracteresEspeciais(empty($oDados->e60_resumo) ? "Justificativa nao informada" : str_replace(array("\r", "\n"), " ", $oDados->e60_resumo))), 0, 500);
        
            $oDadosEmpenho = new stdClass();
            $oDadosEmpenho->NumeroDocumentoOrdenador = $oDados->numerodocumentoordenador;
            $oDadosEmpenho->PrazoMaximoLiquidacao    = $oDados->diasliquidacao;
            $oDadosEmpenho->Justificativa = $sJustificativa;
            $this->addElementXML($oDadosEmpenho, $oNodeEmpenho);
            
            $oNodeEmpenhoClassificacaoFuncional = $this->oDocumento->createElement("ClassificacaoFuncional");
            $oDadosEmpenhoClassificacaoFuncional = new stdClass();
            $oDadosEmpenhoClassificacaoFuncional->CodigoFuncao    = str_pad($oDados->o58_funcao, 2, 0, STR_PAD_LEFT);
            $oDadosEmpenhoClassificacaoFuncional->CodigoSubFuncao = $oDados->o58_subfuncao;
            $this->addElementXML($oDadosEmpenhoClassificacaoFuncional, $oNodeEmpenhoClassificacaoFuncional);
            $oNodeEmpenho->appendChild($oNodeEmpenhoClassificacaoFuncional);
        
            $oNodeEmpenhoFonteRecurso = $this->oDocumento->createElement("FonteRecurso");
            $oDadosEmpenhoFonteRecurso = new stdClass();
          
            $oRecurso = $this->getRecurso($oDados->o58_codigo);

            $oDadosEmpenhoFonteRecurso->CodigoGrupoExercicioFonte = substr($oRecurso->o15_recurso, 0, 1);
            $oDadosEmpenhoFonteRecurso->CodigoClassificacaoFonte  = substr($oRecurso->o15_recurso, 1, 3);
            $oDadosEmpenhoFonteRecurso->CodigoDetalhamentoFonte   = "0000";//substr($oDados->o58_codigo,4,4);
            $this->addElementXML($oDadosEmpenhoFonteRecurso, $oNodeEmpenhoFonteRecurso);
          
            $oNodeEmpenho->appendChild($oNodeEmpenhoFonteRecurso);
      
            $oDadosEmpenho = new stdClass();
            $oDadosEmpenho->NumeroDocumentoCredorEmpenho = $oDados->z01_cgccpf;
            $this->addElementXML($oDadosEmpenho, $oNodeEmpenho);
        
            $oNodeListaEmpenho->appendChild($oNodeEmpenho);
        }
      
        /*
         * Lancamentos contabeis
         */
        $aLancamentosEmpenho = array();
      
        if ((!empty($this->sListaLiquidacao) || !empty($this->sListaPagamento)) || (empty($this->sListaEmpenho) && empty($this->sListaLiquidacao) && empty($this->sListaPagamento))) {
            $aLancamentosEmpenho = $this->getLancamentosEmpenho();
        }
      
        foreach ($aLancamentosEmpenho as $oDados) {
            $sNumeroClassificacaoInstitucional = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
            if ($oDados->e60_anousu <= 2019) {
                $sNumeroClassificacaoInstitucional .= str_pad($oDados->o58_unidade, 2, "0", STR_PAD_LEFT);
            } else {
                if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                    $sNumeroClassificacaoInstitucional .= "220";
                } else {
                    $sNumeroClassificacaoInstitucional .= str_pad($oDados->o58_unidade, 3, "0", STR_PAD_LEFT);
                }
            }
          
            switch ($oDados->c57_sequencial) {
              /*
               * Anulacao Empenho
               */
                case 11:
                    $oNodeAnulacaoEmpenho = $this->oDocumento->createElement("AnulacaoEmpenho");
            
                    $sMotivo = DBString::removerAcentuacao(DBString::removerCaracteresEspeciais($oDados->e94_motivo));
                    if (empty($sMotivo)) {
                        $sMotivo = "Motivo nao informado";
                    }
              
            
                    $oDadosAnulacaoEmpenho = new stdClass();
                    $oDadosAnulacaoEmpenho->NumeroClassificacaoInstitucional = $sNumeroClassificacaoInstitucional;
                    $oDadosAnulacaoEmpenho->NumeroDocumentoEmpenhoOriginal   = ($oDados->e60_anousu <= 2019 ? str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT) : $oDados->e60_codemp);
                    $oDadosAnulacaoEmpenho->AnoEmpenho                       = $oDados->e60_anousu;
                    $oDadosAnulacaoEmpenho->NumeroDocumentoAnulacaoEmpenho   = $oDados->c70_codlan;
                    $oDadosAnulacaoEmpenho->ValorAnulacaoEmpenho             = $oDados->c70_valor;
                    $oDadosAnulacaoEmpenho->DataAnulacaoEmpenho              = $oDados->c70_data;
                    $oDadosAnulacaoEmpenho->MotivoAnulacaoEmpenho            = substr($sMotivo, 0, 500);
                    $this->addElementXML($oDadosAnulacaoEmpenho, $oNodeAnulacaoEmpenho);
              
                    $oNodeListaAnulacaoEmpenho->appendChild($oNodeAnulacaoEmpenho);
            
                    break;
          
             /*
              * Liquidacao
              */
                case 20:
                    $oNodeLiquidacao = $this->oDocumento->createElement("Liquidacao");
             
                    $oDadosLiquidacao = new stdClass();
                    $oDadosLiquidacao->NumeroClassificacaoInstitucional = $sNumeroClassificacaoInstitucional;
                    $oDadosLiquidacao->NumeroDocumentoEmpenho           = ($oDados->e60_anousu <= 2019 ? str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT) : $oDados->e60_codemp);
                    $oDadosLiquidacao->AnoEmpenho                       = $oDados->e60_anousu;
                    $oDadosLiquidacao->NumeroProcessoPagamento          = $oDados->processo_despesa;
                    $oDadosLiquidacao->NumeroDocumentoLiquidacao        = $oDados->e69_codnota;
                    $oDadosLiquidacao->NumeroDocumentoResponsavelAtesto = $oDados->notafiscal_ordenadornumerodocumento;
                    $oDadosLiquidacao->DataLiquidacao                   = $oDados->c70_data;
                    $oDadosLiquidacao->ValorLiquidacao                  = $oDados->c70_valor;
                    $this->addElementXML($oDadosLiquidacao, $oNodeLiquidacao);
             
                    $oNodeListaNotaFiscal = $this->oDocumento->createElement("ListaNotaFiscal");
               
                    $oNodeNotaFiscal = $this->oDocumento->createElement("NotaFiscal");
                   
                    $oDadosNotaFiscal = new stdClass();
                    $oDadosNotaFiscal->NumeroDocumentoLiquidacao      = $oDados->c70_codlan;
                    $oDadosNotaFiscal->TipoDocumentoFiscal            = (empty($oDados->codigotce)?null:str_pad($oDados->codigotce, 3, "0", STR_PAD_LEFT));
                    $oDadosNotaFiscal->NumeroDocumentoFiscal          = substr($oDados->numeronota, 0, 9);
                    $oDadosNotaFiscal->NumeroSerieDocumentoFiscal     = substr($oDados->seriefiscal, 0, 3);
                    $oDadosNotaFiscal->DataEmissaoDocumentoFiscal     = $oDados->dtnota;
                    $oDadosNotaFiscal->ChaveAcessoNFE                 = '0';
                    $oDadosNotaFiscal->ValorDocumentoFiscal           = $oDados->valorfiscal;
                    $oDadosNotaFiscal->DataRecebimentoDocumentoFiscal = $oDados->dtrecebe;
                    $oDadosNotaFiscal->DataAtestoDocumentoFiscal      = $oDados->dtatesto;
                    $this->addElementXML($oDadosNotaFiscal, $oNodeNotaFiscal);
                   
                    $oNodeListaNotaFiscal->appendChild($oNodeNotaFiscal);
               
                    $oNodeLiquidacao->appendChild($oNodeListaNotaFiscal);
             
                    $oNodeListaLiquidacao->appendChild($oNodeLiquidacao);
             
                    break;
             
           /*
            * Anulacao Liquidacao
            */
                case 21:
                    $oNodeAnulacaoLiquidacao = $this->oDocumento->createElement("AnulacaoLiquidacao");
             
                      $oDadosAnulacaoLiquidacao = new stdClass();
                      $oDadosAnulacaoLiquidacao->NumeroClassificacaoInstitucional  = $sNumeroClassificacaoInstitucional;
                      $oDadosAnulacaoLiquidacao->NumeroDocumentoLiquidacao         = $oDados->e69_codnota;
                      $oDadosAnulacaoLiquidacao->AnoLiquidacao                     = $oDados->e69_anousu;
                      $oDadosAnulacaoLiquidacao->NumeroDocumentoAnulacaoLiquidacao = $oDados->c70_codlan;
                      $oDadosAnulacaoLiquidacao->ValorAnulacaoLiquidacao           = $oDados->c70_valor;
                      $oDadosAnulacaoLiquidacao->DataAnulacaoLiquidacao            = $oDados->c70_data;
                      $oDadosAnulacaoLiquidacao->MotivoAnulacaoLiquidacao          = substr(DBString::removerAcentuacao(DBString::removerCaracteresEspeciais($oDados->c72_complem)), 0, 500);
                      $this->addElementXML($oDadosAnulacaoLiquidacao, $oNodeAnulacaoLiquidacao);
             
                    $oNodeListaAnulacaoLiquidacao->appendChild($oNodeAnulacaoLiquidacao);
             
                    break;

           /*
            * Pagamento
            */
                case 30:
                    $oNodePagamento = $this->oDocumento->createElement("Pagamento");
             
                      $oDadosPagamento = new stdClass();
                      $oDadosPagamento->NumeroClassificacaoInstitucional = $sNumeroClassificacaoInstitucional;
                      $oDadosPagamento->NumeroDocumentoLiquidacao        = $oDados->e71_codnota;
                      $oDadosPagamento->AnoLiquidacao                    = $oDados->e50_anousu;
                      $oDadosPagamento->TipoDocumentoPagamento           = $oDados->formapagamento;
                      $oDadosPagamento->NumeroDocumentoPagamento         = $oDados->e71_codord;
                      $oDadosPagamento->DataPagamento                    = $oDados->c70_data;
                      $oDadosPagamento->ValorPagamento                   = $oDados->c70_valor;
                      $oDadosPagamento->DataEfetivaTransferenciaValor    = $oDados->c70_data;
                      $oDadosPagamento->JustificativaQuebraOrdem         = substr(DBString::removerAcentuacao(DBString::removerCaracteresEspeciais($oDados->e09_justificativa)), 0, 500);
                      $oDadosPagamento->NumeroDocumentoOrdenador         = $oDados->numerodocumentoordenador_pagamento;
                      $oDadosPagamento->NumeroDocumentoCredorPagamento   = $oDados->documento_credor;
                      $this->addElementXML($oDadosPagamento, $oNodePagamento);
             
                    $oNodeContaBancaria = $this->oDocumento->createElement("ContaBancaria");
                    $oDadosContaBancaria = new stdClass();
                    $oDadosContaBancaria->CodigoBanco   = $oDados->c63_banco;
                    $oDadosContaBancaria->NumeroAgencia = $oDados->c63_agencia.$oDados->c63_dvagencia;
                    $oDadosContaBancaria->NumeroConta   = $oDados->c63_conta.$oDados->c63_dvconta;
                    $this->addElementXML($oDadosContaBancaria, $oNodeContaBancaria);
             
                    $oNodePagamento->appendChild($oNodeContaBancaria);
                    $oNodeListaPagamento->appendChild($oNodePagamento);
             
                    break;

            /*
          * Anulacao Pagamento
          */
                case 31:
                    $oNodeAnulacaoPagamento = $this->oDocumento->createElement("AnulacaoPagamento");
             
                      $oDadosAnulacaoPagamento = new stdClass();
                      $oDadosAnulacaoPagamento->NumeroClassificacaoInstitucional = $sNumeroClassificacaoInstitucional;
                      $oDadosAnulacaoPagamento->NumeroDocumentoLiquidacao        = $oDados->e71_codnota;
                      $oDadosAnulacaoPagamento->NumeroDocumentoPagamentoOriginal = $oDados->e71_codord;
                      $oDadosAnulacaoPagamento->NumeroDocumentoAnulacaoPagamento = $oDados->c70_codlan;
                      $oDadosAnulacaoPagamento->ValorAnulacaoPagamento           = $oDados->c70_valor;
                      $oDadosAnulacaoPagamento->DataAnulacaoPagamento            = $oDados->c70_data;
                      $oDadosAnulacaoPagamento->MotivoAnulacaoPagamento          = substr(DBString::removerAcentuacao(DBString::removerCaracteresEspeciais($oDados->c72_complem)), 0, 500);
                      $this->addElementXML($oDadosAnulacaoPagamento, $oNodeAnulacaoPagamento);
             
                    $oNodeListaAnulacaoPagamento->appendChild($oNodeAnulacaoPagamento);
             
             
                    break;
            }
        }
      
      
        /*
         * Retencoes
         */
        $aLancamentosRetencoes = array();
      
        if (!empty($this->sListaPagamento) || (empty($this->sListaEmpenho) && empty($this->sListaLiquidacao) && empty($this->sListaPagamento))) {
            $aLancamentosRetencoes = $this->getLancamentosRetencoes();
        }
      
        foreach ($aLancamentosRetencoes as $oDados) {
            $sNumeroClassificacaoInstitucional = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
            if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                $sNumeroClassificacaoInstitucional .= "220";
            } else {
                $sNumeroClassificacaoInstitucional .= str_pad($oDados->o58_unidade, 3, "0", STR_PAD_LEFT);
            }
          
            /*
             * RetencaoPagamento
             */
            if (in_array($oDados->c53_tipo, array(100,160))) {
                $oNodeRetencaoPagamento = $this->oDocumento->createElement("RetencaoPagamento");
                $oDadosRetencaoPagamento = new stdClass();
                $oDadosRetencaoPagamento->NumeroClassificacaoInstitucional       = $sNumeroClassificacaoInstitucional;
                $oDadosRetencaoPagamento->NumeroDocumentoLiquidacao              = $oDados->e71_codnota;
                $oDadosRetencaoPagamento->NumeroDocumentoPagamento               = $oDados->e71_codord;
                $oDadosRetencaoPagamento->CodigoRetencao                         = $oDados->codigoretencao;
                $oDadosRetencaoPagamento->ValorRetencao                          = $oDados->c70_valor;
                $oDadosRetencaoPagamento->DataRetencao                           = $oDados->c70_data;
                $oDadosRetencaoPagamento->NumeroDocumentoPessoaRetencaoPagamento = $oDados->documentopessoaretencao;

                $this->addElementXML($oDadosRetencaoPagamento, $oNodeRetencaoPagamento);
             
                $oNodeListaRetencaoPagamento->appendChild($oNodeRetencaoPagamento);
            }
          
          
            /*
             * Anulacao Retencao Pagamento
             */
            if (in_array($oDados->c53_tipo, array(162))) {
                $oNodeAnulacaoRetencao = $this->oDocumento->createElement("AnulacaoRetencao");
                $oDadosAnulacaoRetencao = new stdClass();
                $oDadosAnulacaoRetencao->NumeroClassificacaoInstitucional = $sNumeroClassificacaoInstitucional;
                $oDadosAnulacaoRetencao->NumeroDocumentoLiquidacao        = $oDados->e71_codnota;
                $oDadosAnulacaoRetencao->NumeroDocumentoPagamento         = $oDados->e71_codord;
                $oDadosAnulacaoRetencao->CodigoRetencao                   = $oDados->codigoretencao;
                $oDadosAnulacaoRetencao->ValorAnulacaoRetencao            = $oDados->c70_valor;
                $oDadosAnulacaoRetencao->DataAnulacaoRetencao             = $oDados->c70_data;
                $oDadosAnulacaoRetencao->MotivoAnulacaoRetencao           = substr(DBString::removerAcentuacao(DBString::removerCaracteresEspeciais($oDados->c72_complem)), 0, 500);
                $this->addElementXML($oDadosAnulacaoRetencao, $oNodeAnulacaoRetencao);
             
                $oNodeListaAnulacaoRetencaoPagamento->appendChild($oNodeAnulacaoRetencao);
            }
        }
      
        /*
         * Montamos a estrutura do arquivo
         */
        if ($oNodeListaPessoa->hasChildNodes()) {
            $oNodeAnexo14->appendChild($oNodeListaPessoa);
        }
        if ($oNodeListaEmpenho->hasChildNodes()) {
            $oNodeAnexo14->appendChild($oNodeListaEmpenho);
        }
        if ($oNodeListaAnulacaoEmpenho->hasChildNodes()) {
            $oNodeAnexo14->appendChild($oNodeListaAnulacaoEmpenho);
        }
        if ($oNodeListaLiquidacao->hasChildNodes()) {
            $oNodeAnexo14->appendChild($oNodeListaLiquidacao);
        }
        if ($oNodeListaAnulacaoLiquidacao->hasChildNodes()) {
            $oNodeAnexo14->appendChild($oNodeListaAnulacaoLiquidacao);
        }
        if ($oNodeListaPagamento->hasChildNodes()) {
            $oNodeAnexo14->appendChild($oNodeListaPagamento);
        }
        if ($oNodeListaAnulacaoPagamento->hasChildNodes()) {
            $oNodeAnexo14->appendChild($oNodeListaAnulacaoPagamento);
        }
        if ($oNodeListaRetencaoPagamento->hasChildNodes()) {
            $oNodeAnexo14->appendChild($oNodeListaRetencaoPagamento);
        }
        if ($oNodeListaAnulacaoRetencaoPagamento->hasChildNodes()) {
            $oNodeAnexo14->appendChild($oNodeListaAnulacaoRetencaoPagamento);
        }

        $this->oDocumento->appendChild($oNodeAnexo14);
      
        $oRetornoValidacao = $this->validarXML($this->oDocumento, 'model/contabilidade/arquivos/siai/v2020/xsd/ValidadorAnexo14.xsd');
        if ($oRetornoValidacao->lErro == true) {
            $this->addLog($oRetornoValidacao->sMsg);
            //throw new Exception($oRetornoValidacao->sMsg);
        }
      
        $this->sArquivo = $this->oDocumento->saveXML();
        file_put_contents("tmp/{$this->getNomeArquivo()}", $this->getArquivo());
    }
  
    public function getDotacaoWhere()
    {
      
        $sOrcDotacaoWhere = " ((o58_orgao = {$this->codigoOrgao} and o58_unidade = {$this->codigoUnidade}) or ({$this->codigoOrgao} = 0 and o58_instit = {$this->codigoUnidade}))";
      
        if ($this->codigoOrgao == "29" && $this->codigoUnidade == "1") {
            $sOrcDotacaoWhere = " ((o58_orgao = 29 and o58_unidade = 1) or (o58_orgao = 29 and o58_unidade = 46) or (o58_orgao = 29 and o58_unidade = 47))";
        }
      
        if ($this->codigoOrgao == "20" && $this->codigoUnidade == "1") {
            $sOrcDotacaoWhere = " ((o58_orgao = 20 and o58_unidade = 1) or (o58_orgao = 20 and o58_unidade = 49))";
        }
      
        if ($this->codigoOrgao == "34" && $this->codigoUnidade == "1") {
            $sOrcDotacaoWhere = " ((o58_orgao = 34 and o58_unidade = 1) or (o58_orgao = 34 and o58_unidade = 49))";
        }

        if ($this->codigoOrgao == "37" && $this->codigoUnidade == "10") {
              $sOrcDotacaoWhere = " ((o58_orgao = 37 and o58_unidade = 10) or (o58_orgao = 37 and o58_unidade = 49))";
        }
     
        if ($this->codigoOrgao == "18" && $this->codigoUnidade == "1") {
            $sOrcDotacaoWhere = " ( (o58_orgao = 18 and o58_unidade = 1)
                                  or (o58_orgao = 18 and o58_unidade = 45)
                                  or (o58_orgao = 18 and o58_unidade = 46)
                                  or (o58_orgao = 18 and o58_unidade = 47)
                                  or (o58_orgao = 18 and o58_unidade = 48)
                                  or (o58_orgao = 18 and o58_unidade = 49))";
        }
      
        return $sOrcDotacaoWhere;
    }
  
    public function getDadosPessoas()
    {

      //correcao dos cgms dos ordenadores de despesa que podem ter sido alterados pelo processamento dos duploscgm.
        db_inicio_transacao();
        
        $sSql = "update plugins.assinaturaordenadordespesa set numcgm = z10_numcgm
                 from cgmerrado
                      inner join cgmcorreto on z10_codigo = z11_codigo
                where z11_numcgm = numcgm";
        db_query($sSql);
                                                          
        db_fim_transacao();
                                                                                  
           $sOrcDotacaoWhere = $this->getDotacaoWhere();
      
        $sSqlEmpenhos = "select distinct on (z01_cgccpf)
                                    z01_cgccpf,
                                    z01_nome,
                                    1 as tp
                               from cgm
                                    inner join empempenho on empempenho.e60_numcgm = cgm.z01_numcgm
                                    inner join orcdotacao on orcdotacao.o58_anousu = empempenho.e60_anousu
                                                         and orcdotacao.o58_coddot = empempenho.e60_coddot                                    
                              where cgm.z01_cgccpf not in ('0', '00000000000', '00000000000000')
                                and {$sOrcDotacaoWhere}";
      
        $sSqlEmpNota = "select distinct on (z01_cgccpf)
                                   z01_cgccpf,
                                   z01_nome,
                                   2 as tp
                              from cgm
			                	   inner join plugins.empnotagestor on empnotagestor.gestor = z01_numcgm
                                                   inner join empnota               on empnota.e69_codnota   = empnotagestor.empnota
                                                   inner join empempenho            on empempenho.e60_numemp = empnota.e69_numemp
                                                   inner join orcdotacao            on orcdotacao.o58_anousu = empempenho.e60_anousu
                                                                                   and orcdotacao.o58_coddot = empempenho.e60_coddot                                   
			                     where z01_cgccpf not in ('0', '00000000000', '00000000000000')                   
                                   and {$sOrcDotacaoWhere}";
      
        $sSqlAssinaturaOrdenador = "select distinct on (z01_cgccpf)
                                   z01_cgccpf,
                                   z01_nome,
                                   3 as tp
                              from plugins.assinaturaordenadordespesa
                                   inner join plugins.documentoassinaturaordenadordespesa on assinaturaordenadordespesa = plugins.assinaturaordenadordespesa.sequencial
                                   inner join cgm on z01_numcgm = plugins.assinaturaordenadordespesa.numcgm
					 where plugins.documentoassinaturaordenadordespesa.tipo = 1 and ativo = 't'";

        $sSqlPagOrdem = "select distinct on (z01_cgccpf) *
                         from ( select case when cgmconta.z01_numcgm is not null then cgmconta.z01_cgccpf else cgm.z01_cgccpf end as z01_cgccpf,
                                       case when cgmconta.z01_numcgm is not null then cgmconta.z01_nome   else cgm.z01_nome end as z01_nome,
                                       4 as tp
                                  from pagordem
                                        left join pagordemconta         on pagordem.e50_codord      = pagordemconta.e49_codord
                                        left join cgm cgmconta          on cgmconta.z01_numcgm      = pagordemconta.e49_numcgm
                                       inner join empempenho            on empempenho.e60_numemp    = pagordem.e50_numemp
                                       inner join cgm                   on cgm.z01_numcgm           = empempenho.e60_numcgm
                                       inner join orcdotacao            on orcdotacao.o58_anousu    = empempenho.e60_anousu
                                                                       and orcdotacao.o58_coddot    = empempenho.e60_coddot
                                 where case when cgmconta.z01_cgccpf is not null then cgmconta.z01_cgccpf not in ('0', '00000000000', '00000000000000') else true end
                                   and cgm.z01_cgccpf not in ('0', '00000000000', '00000000000000')
                                   and {$sOrcDotacaoWhere} ";
        if (!empty($this->sListaPagamento)) {
            $sSqlPagOrdem .= " and pagordem.e50_codord in ({$this->sListaPagamento})";
        } else {
            $sSqlPagOrdem .= " and exists (select 1 from conlancamord where c80_codord = e50_codord and extract(year from c80_data) = {$this->getAno()})";
        }
      
        $sSqlPagOrdem .= " ) as dados";
      
      
        $sSqlRetencao = "select distinct on (z01_cgccpf)
                              z01_cgccpf,
                              z01_nome,
                              5 as tp
                         from retencaopagordem
                              inner join retencaoreceitas on retencaoreceitas.e23_retencaopagordem = retencaopagordem.e20_sequencial
                              inner join retencaotiporec on retencaotiporec.e21_sequencial = retencaoreceitas.e23_retencaotiporec
                              inner join retencaotiporeccgm on retencaotiporeccgm.e48_retencaotiporec = retencaotiporec.e21_sequencial
                              inner join cgm as cgmretencao on retencaotiporeccgm.e48_cgm = cgmretencao.z01_numcgm
                              inner join pagordem on pagordem.e50_codord = retencaopagordem.e20_pagordem
                              inner join empempenho on empempenho.e60_numemp = pagordem.e50_numemp
                              inner join orcdotacao on orcdotacao.o58_coddot = empempenho.e60_coddot and orcdotacao.o58_anousu = empempenho.e60_anousu
                         where z01_cgccpf not in ('0', '00000000000', '00000000000000')
                           and exists (select 1 from conlancamord where c80_codord = e50_codord and extract(year from c80_data) = {$this->getAno()})
                           and {$sOrcDotacaoWhere} ";
      
        $aOrigemPessoas = array();
      
        $aOrigemPessoas[] = $sSqlEmpenhos. " and empempenho.e60_anousu = ".$this->getAno();
        $aOrigemPessoas[] = $sSqlEmpNota. " and extract(year from e69_dtinclusao) = ".$this->getAno();
        $aOrigemPessoas[] = $sSqlAssinaturaOrdenador;
        $aOrigemPessoas[] = $sSqlPagOrdem;
        $aOrigemPessoas[] = $sSqlRetencao;
      
        if (!empty($this->sListaEmpenho) || !empty($this->sListaLiquidacao) || !empty($this->sListaPagamento)) {
            $aOrigemPessoas = array();
          
            if (!empty($this->sListaEmpenho)) {
                $aOrigemPessoas[] = $sSqlEmpenhos." and e60_numemp in ({$this->sListaEmpenho})";
                $aOrigemPessoas[] = $sSqlAssinaturaOrdenador." and chave in ({$this->sListaEmpenho})";
            }
          
            if (!empty($this->sListaLiquidacao)) {
                $aOrigemPessoas[] = $sSqlEmpNota." and empnota.e69_codnota in ({$this->sListaLiquidacao})";
            }
          
            if (!empty($this->sListaPagamento)) {
                $aOrigemPessoas[] = $sSqlPagOrdem;
            }
        }

        $sqlPessoas = "select distinct on (z01_cgccpf) * from ( ".implode(" union ", $aOrigemPessoas)." ) as dados";
        $rsDados = db_query($sqlPessoas);
        return db_utils::getCollectionByRecord($rsDados);
    }
  
    public function getDadosEmpenhos()
    {
      
        $sOrcDotacaoWhere = $this->getDotacaoWhere();
      
        $sWhereBuscaEmpenhos  = " empempenho.e60_anousu = ".$this->getAno();
        $sWhereBuscaEmpenhos .= " and extract(month from empempenho.e60_emiss) = ".$this->getMes();
        $sWhereBuscaEmpenhos .= " and empempenho.e60_instit = ".db_getsession("DB_instit");
        $sWhereBuscaEmpenhos .= " and e60_codtipo <> 4 ";
        $sWhereBuscaEmpenhos .= " and {$sOrcDotacaoWhere} ";
        if (!empty($this->sListaEmpenho)) {
            $sWhereBuscaEmpenhos .= " and e60_numemp in ({$this->sListaEmpenho}) ";
        }
      
        $sCampos = 'empempenho.e60_numemp,
                  empempenho.e60_codemp,
                  empempenho.e60_anousu,
                  empempenho.e60_emiss,
                  empempenho.e60_vlremp,
                  empempenho.e60_resumo,
                  CASE                                            
                    WHEN empempenho.e60_codtipo = 1 THEN 1     
                    WHEN empempenho.e60_codtipo = 2 THEN 3     
                    WHEN empempenho.e60_codtipo = 3 THEN 2     
                    WHEN empempenho.e60_codtipo = 4 THEN 1
                    WHEN empempenho.e60_codtipo = 5 THEN 1
                  END AS e60_codtipo,
                  cgm.z01_numcgm, 
                  cgm.z01_cgccpf,
                  cgm.z01_nome,
                  orcdotacao.*,  
                  orcelemento.o56_elemento,
                  (select e150_numeroprocesso 
                     from empautorizaprocesso 
                          inner join empempaut on e150_empautoriza = e61_autori
                    where e61_numemp = e60_numemp limit 1) as processo_despesa,
                  (select numerorecibolicitacao 
                     from plugins.empempenhoprocessolicitatorio 
                    where empempenho = e60_numemp ) as numeroprocessolicitatorio,
                  (select l44_codigotribunal 
                     from empautoriza
                          inner join pctipocompra on pc50_codcom = e54_codcom
                          inner join pctipocompratribunal on l44_sequencial = pc50_pctipocompratribunal
                          inner join empempaut on e61_autori = e54_autori
		    where e61_numemp = e60_numemp) as codigolicitacaotribunal,';

        require_once(modification("libs/db_libpostgres.php"));
        $lExisteAcordoReciboAnexo = PostgreSQLUtils::isTableExists("acordoreciboanexo", "tabela");
        if (!!$lExisteAcordoReciboAnexo) {
            $sCampos .= "(SELECT anexo23
                        FROM plugins.acordoreciboanexo
                             INNER JOIN acordoempautoriza ON ac45_acordo = acordo
                             INNER JOIN empempaut ON e61_autori = ac45_empautoriza
                       WHERE e61_numemp = e60_numemp
                       union 
                      select anexo23 
                        from plugins.acordoreciboanexo 
                             inner join acordoempempenho on ac54_acordo = acordo 
                       where ac54_empempenho = e60_numemp 
                       limit 1 ) AS anexo23,";
        }

        $sCampos .= "(select T1.cpf from (select max(plugins.documentoassinaturaordenadordespesa.sequencial), z01_cgccpf as cpf
                    from plugins.documentoassinaturaordenadordespesa
                         inner join plugins.assinaturaordenadordespesa on assinaturaordenadordespesa.sequencial = documentoassinaturaordenadordespesa.assinaturaordenadordespesa
                         inner join cgm on z01_numcgm = plugins.assinaturaordenadordespesa.numcgm
                   where documentoassinaturaordenadordespesa.chave = e60_numemp
                     and documentoassinaturaordenadordespesa.tipo = 1
                   group by chave, z01_cgccpf) T1) as numerodocumentoordenador,";
        $sCampos .= "case
                     when classificacaocredores.cc30_codigo = 4
                       then 999
                      else classificacaocredoresdiasliquidacao.diasliquidacao
                   end as diasliquidacao";
        $sSqlEmpenhos = "select {$sCampos}
                         from empempenho
                              inner join db_config                      on db_config.codigo                             = empempenho.e60_instit
                              inner join orcdotacao                     on orcdotacao.o58_anousu                        = empempenho.e60_anousu
                                                                       and orcdotacao.o58_coddot                        = empempenho.e60_coddot
                              inner join cgm                            on cgm.z01_numcgm                               = empempenho.e60_numcgm
                              inner join orcprojativ                    on orcprojativ.o55_anousu                       = orcdotacao.o58_anousu
                                                                       and orcprojativ.o55_projativ                     = orcdotacao.o58_projativ
                              inner join empelemento                    on empelemento.e64_numemp                       = empempenho.e60_numemp  
                              inner join orcelemento                    on orcelemento.o56_codele                       = empelemento.e64_codele
                                                                       and orcelemento.o56_anousu                       = empempenho.e60_anousu
                              inner join classificacaocredoresempenho   on classificacaocredoresempenho.cc31_empempenho = empempenho.e60_numemp
                              inner join classificacaocredores          on classificacaocredores.cc30_codigo            = classificacaocredoresempenho.cc31_classificacaocredores
                               left join plugins.classificacaocredoresdiasliquidacao on classificacaocredores           = classificacaocredores.cc30_codigo
                        where {$sWhereBuscaEmpenhos}";
        $rsDados = db_query($sSqlEmpenhos);
        return db_utils::getCollectionByRecord($rsDados);
    }
  
    public function getLancamentosEmpenho()
    {
      
        $sOrcDotacaoWhere = $this->getDotacaoWhere();
      
        $sCampos = "       distinct on (c70_codlan)
                         orcdotacao.o58_orgao,
                         orcdotacao.o58_unidade,
                         empempenho.e60_numemp,
                         empempenho.e60_codemp,
                         empempenho.e60_anousu,

                         conplanoconta.c63_banco,
                         conplanoconta.c63_agencia,
                         conplanoconta.c63_dvagencia,
                         conplanoconta.c63_conta,
                         conplanoconta.c63_dvconta,

			 pagordem.e50_codord,
                         pagordem.e50_anousu,
			 empnota.e69_codnota,
                         empnota.e69_anousu,
                         pagordemnota.e71_codnota,
                         pagordemnota.e71_codord,
                         conlancam.c70_codlan,
                         conlancam.c70_data,
                         conlancam.c70_anousu,
                         round(conlancam.c70_valor,2) as c70_valor,
                         conlancamcompl.c72_complem,
                         conhistdoctipo.c57_sequencial,
                         corgrupocorrente.k105_corgrupotipo,
                         empanulado.e94_motivo,
                         
                         empnotafiscal.tiponotafiscal,
                         empnotafiscal.numeronota,
                         empnotafiscal.localrecebimento,
                         empnotafiscal.dtnota,
                         empnotafiscal.dtrecebe,
                         empnotafiscal.dtvencimento,
                         empnotafiscal.mes_competencia,
                         empnotafiscal.ano_competencia,
                         empnotafiscal.numeroprocesso,
                         empnotafiscal.seriefiscal,
                         round(empnotafiscal.valorfiscal,2) as valorfiscal,
                         tiponotafiscal.codigotce,
                         empnotagestor.empnota,
                         empnotagestor.gestor,
                         empnotagestor.dtatesto,
                         empnotagestor.dtvencimento,
                         
                         empnotagestor_cgm.z01_numcgm as notafiscal_ordenadorcgm,
                         empnotagestor_cgm.z01_nome as notafiscal_ordenadornome,
                         empnotagestor_cgm.z01_cgccpf as notafiscal_ordenadornumerodocumento,
                         
                         empagemovjustificativa.e09_justificativa,

                         pc63_banco, 
                         pc63_agencia, 
                         pc63_agencia_dig, 
                         pc63_conta, 
                         pc63_conta_dig,

                         case 
                            when empagemovforma.e97_codforma = 4 then '003'  
                            else '001'
                         end as formapagamento,
                         
                         (select T1.cpf
                            from (select max(plugins.documentoassinaturaordenadordespesa.sequencial),
                                         z01_cgccpf as cpf
                                    from plugins.documentoassinaturaordenadordespesa
                                         inner join plugins.assinaturaordenadordespesa on assinaturaordenadordespesa.sequencial = documentoassinaturaordenadordespesa.assinaturaordenadordespesa
                                         inner join cgm                                on cgm.z01_numcgm                        = plugins.assinaturaordenadordespesa.numcgm
                                   where documentoassinaturaordenadordespesa.chave = c75_numemp
                                     and documentoassinaturaordenadordespesa.tipo = 1
                                   group by chave, z01_cgccpf) T1) as numerodocumentoordenador,
                                   
                                 (select z01_cgccpf as cpf
                                    from plugins.documentoassinaturaordenadordespesa
                                         inner join plugins.assinaturaordenadordespesa on assinaturaordenadordespesa.sequencial = documentoassinaturaordenadordespesa.assinaturaordenadordespesa
                                         inner join cgm                                on cgm.z01_numcgm                        = plugins.assinaturaordenadordespesa.numcgm
                                   where documentoassinaturaordenadordespesa.chave in (select e82_codmov from empord where e82_codord = e50_codord)
                                     and documentoassinaturaordenadordespesa.tipo = 5
                                    order by documentoassinaturaordenadordespesa.sequencial desc limit 1) as numerodocumentoordenador_pagamento,
                                    
                         (select e150_numeroprocesso
                            from empautorizaprocesso
                                 inner join empempaut on e150_empautoriza = e61_autori
                           where e61_numemp = empempenho.e60_numemp limit 1) as processo_despesa,
                           
                         case
                           when pagordemconta.e49_codord is not null
                             then pagordemconta_cgm.z01_cgccpf
                           else cgm.z01_cgccpf
                         end as documento_credor,
                         
                         (select max(pg.e50_codord)
                            from pagordem pg
                           where pg.e50_numemp = empempenho.e60_numemp) as NumOB";
      
        $sSqlLancamentos = "select {$sCampos}
                           
                   from conlancam
                        inner join conlancamdoc                  on conlancamdoc.c71_codlan                  = conlancam.c70_codlan
                        inner join conhistdoc                    on conhistdoc.c53_coddoc                    = conlancamdoc.c71_coddoc
                        inner join conhistdoctipo                on conhistdoctipo.c57_sequencial            = conhistdoc.c53_tipo
                        inner join conlancamemp                  on conlancamemp.c75_codlan                  = conlancam.c70_codlan
                        inner join empempenho                    on empempenho.e60_numemp                    = conlancamemp.c75_numemp
                        inner join cgm                           on cgm.z01_numcgm                           = empempenho.e60_numcgm
                        inner join orcdotacao                    on orcdotacao.o58_coddot                    = empempenho.e60_coddot
                                                                and orcdotacao.o58_anousu                    = empempenho.e60_anousu
                         left join conlancamcorgrupocorrente     on conlancamcorgrupocorrente.c23_conlancam  = conlancam.c70_codlan
                         left join corgrupocorrente              on corgrupocorrente.k105_sequencial         = conlancamcorgrupocorrente.c23_corgrupocorrente
                         left join conlancamnota                 on conlancamnota.c66_codlan                 = conlancam.c70_codlan
                         left join conlancampag                  on conlancampag.c82_codlan                  = conlancam.c70_codlan
                         left join conlancamcompl                on conlancamcompl.c72_codlan                = conlancam.c70_codlan
                         left join conplanoreduz                 on conplanoreduz.c61_reduz                  = conlancampag.c82_reduz
                                                                and conplanoreduz.c61_anousu                 = conlancampag.c82_anousu
                         left join conplano                      on conplano.c60_codcon                      = conplanoreduz.c61_codcon
                                                                and conplano.c60_anousu                      = conplanoreduz.c61_anousu
                         left join conplanoconta                 on conplanoconta.c63_codcon                 = conplano.c60_codcon
                                                                and conplanoconta.c63_anousu                 = conplano.c60_anousu
                         left join empnota                       on empnota.e69_codnota                      = conlancamnota.c66_codnota
                         left join conlancamord                  on conlancamord.c80_codlan                  = conlancam.c70_codlan
                         left join pagordem                      on pagordem.e50_codord                      = conlancamord.c80_codord
                         left join empord                        on empord.e82_codord                        = pagordem.e50_codord
                         left join empagemov                     on empagemov.e81_codmov                     = empord.e82_codmov
                                                                and empagemov.e81_cancelado is null
                         left join empagemovforma                on empagemovforma.e97_codmov                = empord.e82_codmov
                         left join empagemovconta                on empagemovconta.e98_codmov                = empord.e82_codmov
                         left join pcfornecon                    on pcfornecon.pc63_contabanco               = empagemovconta.e98_contabanco
                         left join empagemovjustificativa        on empagemovjustificativa.e09_codmov        = empord.e82_codmov
                                                                and empagemovjustificativa.e09_codnota       = empnota.e69_codnota
                         left join pagordemnota                  on pagordemnota.e71_codord                  = conlancamord.c80_codord
                         left join pagordemconta                 on pagordemconta.e49_codord                 = pagordem.e50_codord
                         left join cgm as pagordemconta_cgm      on pagordemconta_cgm.z01_numcgm             = pagordemconta.e49_numcgm
                         left join empanulado                    on empanulado.e94_numemp                    = empempenho.e60_numemp
                         left join plugins.empnotagestor         on empnotagestor.empnota                    = (coalesce(empnota.e69_codnota,pagordemnota.e71_codnota))
                         left join cgm as empnotagestor_cgm      on empnotagestor_cgm.z01_numcgm             = empnotagestor.gestor
                         left join plugins.empnotafiscalempnota  on empnotafiscalempnota.empnota             = (coalesce(empnota.e69_codnota,pagordemnota.e71_codnota))
                         left join plugins.empnotafiscal         on empnotafiscal.sequencial                 = empnotafiscalempnota.empnotafiscal
                         left join plugins.tiponotafiscal        on tiponotafiscal.sequencial                = empnotafiscal.tiponotafiscal
                   where extract(month from conlancam.c70_data) = ".$this->getMes()."
                     and conhistdoctipo.c57_sequencial in (11,20,21,30,31)
                     and conlancam.c70_anousu = ".$this->getAno()."
                     and empempenho.e60_codtipo <> 4
                     and empempenho.e60_instit = ".db_getsession("DB_instit")."
                     and empempenho.e60_emiss >= '2016-01-01'
                     and {$sOrcDotacaoWhere}";
             /* and empempenho.e60_anousu = ".$this->getAno() COMENTADO PARA ENTRAR RESTOS A PAGAR */
        if (!empty($this->sListaLiquidacao)) {
            $sSqlLancamentos .= " and empnota.e69_codnota in ($this->sListaLiquidacao) ";
            $sSqlLancamentos .= " and conhistdoctipo.c57_sequencial in (20) ";
        }
                     
        if (!empty($this->sListaPagamento)) {
            $sSqlLancamentos .= " and pagordem.e50_codord in ($this->sListaPagamento) ";
            $sSqlLancamentos .= " and conhistdoctipo.c57_sequencial in (30) ";
        }
        
               $rsDados  =  db_query($sSqlLancamentos);
               return db_utils::getCollectionByRecord($rsDados);
    }
  
    public function getLancamentosRetencoes()
    {
      
        $sOrcDotacaoWhere = $this->getDotacaoWhere();
      
        /*
        1 | IRRF Pessoa Fisica
        2 | IRRF Pessoa Juridica
        3 | INSS Pessoa Fisica
        4 | INSS Pessoa Juridica
        5 | ISSQN
        7 | INSS s/ autonomos
        6 | Outros
        */
      
        $sSql = "select o58_orgao,
                      o58_unidade,
                      e71_codord,
                      e71_codnota,
                      e23_dtcalculo,
                      e23_valorretencao,
                      c70_codlan,
                      c70_valor,
                      c70_data,
                      c71_coddoc,
                      c53_descr,
                      c53_tipo,
		      c72_complem, 
                      cgmretencao.z01_cgccpf as documentopessoaretencao,
                      case 
                        when e21_retencaotipocalc in (1,2) then '001'  
                        when e21_retencaotipocalc in (3,4) then '003'
                        when e21_retencaotipocalc in (5) then '002'
                        when e21_retencaotipocalc in (7) then '003'
                        when e21_retencaotipocalc in (6) then '023'
                      end as codigoretencao
                 from retencaopagordem
                      inner join retencaoreceitas         on retencaoreceitas.e23_retencaopagordem        = retencaopagordem.e20_sequencial
                      inner join retencaotiporec          on retencaotiporec.e21_sequencial               = retencaoreceitas.e23_retencaotiporec
                      inner join retencaotiporeccgm       on retencaotiporeccgm.e48_retencaotiporec = retencaotiporec.e21_sequencial 
                      inner join cgm as cgmretencao       on retencaotiporeccgm.e48_cgm = cgmretencao.z01_numcgm
                      inner join retencaocorgrupocorrente on retencaocorgrupocorrente.e47_retencaoreceita = retencaoreceitas.e23_sequencial
                      inner join corgrupocorrente         on corgrupocorrente.k105_sequencial             = retencaocorgrupocorrente.e47_corgrupocorrente
                      inner join conlancamcorrente        on corgrupocorrente.k105_id                     = conlancamcorrente.c86_id
                                                         and corgrupocorrente.k105_data                   = conlancamcorrente.c86_data
                                                         and corgrupocorrente.k105_autent                 = conlancamcorrente.c86_autent
                      inner join conlancam                on conlancam.c70_codlan                         = conlancamcorrente.c86_conlancam
                      inner join conlancamdoc             on conlancam.c70_codlan                         = conlancamdoc.c71_codlan
                       left join conlancamcompl           on conlancamcompl.c72_codlan                    = conlancam.c70_codlan
                      inner join conhistdoc               on conhistdoc.c53_coddoc                        = conlancamdoc.c71_coddoc
                      inner join pagordem                 on pagordem.e50_codord                          = retencaopagordem.e20_pagordem
                      inner join empempenho               on empempenho.e60_numemp                        = pagordem.e50_numemp
                      inner join cgm                      on cgm.z01_numcgm                               = empempenho.e60_numcgm
                      inner join orcdotacao               on orcdotacao.o58_coddot                        = empempenho.e60_coddot
                                                         and orcdotacao.o58_anousu                        = empempenho.e60_anousu
                      inner join pagordemnota             on pagordemnota.e71_codord                      = pagordem.e50_codord 
                where extract(month from conlancam.c70_data) = ".$this->getMes()."
                  and conlancam.c70_anousu = ".$this->getAno()."
                  and empempenho.e60_codtipo <> 4
                  and empempenho.e60_instit = ".db_getsession("DB_instit")."
                  and empempenho.e60_emiss >= '2016-01-01'
                  and empempenho.e60_anousu = ".$this->getAno()."
                  and {$sOrcDotacaoWhere} ";
        if (!empty($this->sListaPagamento)) {
            $sSql .= " and pagordem.e50_codord in ($this->sListaPagamento) ";
        }
      
        $rsDados  =  db_query($sSql);
        return db_utils::getCollectionByRecord($rsDados);
    }

    public function getRecurso($CodigoRecurso)
    {

        $oOrcTipoRec = new cl_orctiporec();
        $rsRecurso = $oOrcTipoRec->sql_record($oOrcTipoRec->sql_query_file($CodigoRecurso));
        return db_utils::fieldsMemory($rsRecurso, 0);
    }
}
