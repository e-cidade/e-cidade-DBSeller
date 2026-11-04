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
require_once(modification("libs/db_stdlib.php"));

class SiaiArquivoLOAXML extends SiaiArquivoBase
{

    protected $oDocumento;
  
    function __construct()
    {
    
        $this->oDocumento = new DOMDocument('1.0', 'utf-8');
        $this->oDocumento->formatOutput = true;
        $this->oDocumento->encoding = 'utf-8';
    }
  
    function addElement($oAtributos, $oElementoPai = null)
    {
    
        foreach ($oAtributos as $sAtributo => $sValor) {
            if (!empty($oElementoPai)) {
                $oElementoPai->appendChild($this->oDocumento->createElement($sAtributo, $sValor));
            } else {
                $this->oDocumento->createElement($sAtributo, $sValor);
            }
        }
    
        return true;
    }
  
  /**
   * Gera o arquivo XML
   */
    public function processar()
    {
    
      /*
      ini_set ( 'display_errors', 1 );
      ini_set ( 'display_startup_erros', 1 );
      error_reporting ( E_ALL );
    */
        $aOrgaosGerados     = array();
        $aUnidadesGeradas   = array();
        $aFuncoesGeradas    = array();
        $aSubFuncoesGeradas = array();
        $aProgramasGerados  = array();
        $aAcoesGeradas      = array();
        $aAcoes             = array();
    
    
      /*
       * Criamos os nós do arquivo xml
       */
      //REMESSA
        $oRemessa = $this->oDocumento->createElement("remessa");
    
      //ANEXO01
        $oAnexo01 = $this->oDocumento->createElement("anexo01");
    
      //ANEXO02
        $oAnexo02 = $this->oDocumento->createElement("anexo02");
        $despesasA02 = $this->oDocumento->createElement("despesasA02");
        $receitasA02 = $this->oDocumento->createElement("receitasA02");
     
      //ANEXO06
        $oAnexo06  = $this->oDocumento->createElement("anexo06");
        $orgaosA06 = $this->oDocumento->createElement("orgaosA06");
    
      //ANEXO07
        $oAnexo07 = $this->oDocumento->createElement("anexo07");
        $funcoesA07 = $this->oDocumento->createElement("funcoesA07");
    
      //ANEXO08
        $oAnexo08 = $this->oDocumento->createElement("anexo08");
        $funcoesA08 = $this->oDocumento->createElement("funcoesA08");
     
      //ANEXO09
        $oAnexo09 = $this->oDocumento->createElement("anexo09");
        $orgaosA09 = $this->oDocumento->createElement("orgaosA09");
    
      /*
       * linha da remessa
       */
        $oDadosRemessa = new stdClass();
        $oDadosRemessa->codigoOrgao    = 422;//$this->getCodigoOrgao();
        $oDadosRemessa->cpfGestor      = "00000000000";
        $oDadosRemessa->tipoRemessa    = 3;
        $oDadosRemessa->ano            = $this->getAno();
        $oDadosRemessa->dataCriacao    = str_replace("/", "-", db_formatar($this->getDataGeracao(), "d"));
        $oDadosRemessa->sistemaGerador = "e-cidade";
        $this->addElement($oDadosRemessa, $oRemessa);
    
        $sWhere = "( o57_fonte ilike '33%'
                 or o57_fonte ilike '34%'
  			     or o57_fonte ilike '39%'
  			     or o57_fonte ilike '41%'
                 or o57_fonte ilike '42%'
  			     or o57_fonte ilike '47%'
  			     or o57_fonte ilike '9172%'
  			   )";
        $sSqlReceitaSaldo = db_receitasaldo(11, 1, 3, true, $sWhere, $this->getAno(), $this->dtDataInicial, $this->dtDataFinal, true);
        $sSqlValoresAnexo01 = "select abs(sum(case when o57_fonte = '411000000000000' then saldo_inicial else 0 end)) as receitaTributaria,             
                                  abs(sum(case when o57_fonte = '412000000000000' then saldo_inicial else 0 end)) as receitadeContribuicoes,        
                                  abs(sum(case when o57_fonte = '413000000000000' then saldo_inicial else 0 end)) as receitaPatrimonial,            
                                  abs(sum(case when o57_fonte = '416000000000000' then saldo_inicial else 0 end)) as receitadeServicos,             
                                  abs(sum(case when o57_fonte = '417000000000000' then saldo_inicial else 0 end)) as transferenciasCorrentes,       
                                  abs(sum(case when o57_fonte = '419000000000000' then saldo_inicial else 0 end)) as outrasReceitasCorrentes,       
                                  abs(sum(case when o57_fonte = '917200000000000'  then saldo_inicial else 0 end)) as deducaoTransferenciasFundeb,   
                                  abs(sum(case when o57_fonte = '331000000000000' then saldo_inicial else 0 end)) as despesaPessoalEncargosSociais, 
                                  abs(sum(case when o57_fonte = '332000000000000' then saldo_inicial else 0 end)) as despesaJurosEncargosDivida,    
                                  abs(sum(case when o57_fonte = '333000000000000' then saldo_inicial else 0 end)) as outrasDespesasCorrentes,       
                                  abs(sum(case when o57_fonte = '421000000000000' then saldo_inicial else 0 end)) as receitaOperacoesCredito,       
                                  abs(sum(case when o57_fonte = '422000000000000' then saldo_inicial else 0 end)) as receitaAlienacaoBens,          
                                  abs(sum(case when o57_fonte = '423000000000000' then saldo_inicial else 0 end)) as receitaAmortizacaoEmprestimos, 
                                  abs(sum(case when o57_fonte = '424000000000000' then saldo_inicial else 0 end)) as transferenciasCapital,         
                                  abs(sum(case when o57_fonte = '425000000000000' then saldo_inicial else 0 end)) as outrasReceitasCapital,         
                                  abs(sum(case when o57_fonte = '470000000000000' then saldo_inicial else 0 end)) as receitasIntraorcamentarias,    
                                  abs(sum(case when o57_fonte = '344000000000000' then saldo_inicial else 0 end)) as despesaInvestimentos,          
                                  abs(sum(case when o57_fonte = '345000000000000' then saldo_inicial else 0 end)) as despesaInversoesFinanceiras,   
                                  abs(sum(case when o57_fonte = '346000000000000' then saldo_inicial else 0 end)) as despesaAmortizacaoDivida,      
                                  abs(sum(case when o57_fonte = '399000000000000' then saldo_inicial else 0 end)) as reservaContingencia,
                                  0 as receitaAgropecuaria,           
                                  0 as receitaIndustrial,             
                                  0 as deducaoRestituicoes,           
                                  0 as outrasDeducoes
  	                         from ($sSqlReceitaSaldo) as receita_saldo";
        $rsValoresAnexo01 = db_query($sSqlValoresAnexo01);
        $oDadosValoresAnexo01 = db_utils::fieldsMemory($rsValoresAnexo01, 0);
    
        $oDadosAnexo01 = new stdClass();
        $oDadosAnexo01->receitaTributaria             = ($oDadosValoresAnexo01->receitatributaria             > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->receitatributaria, 2, '.', '')):"0");
        $oDadosAnexo01->receitadeContribuicoes        = ($oDadosValoresAnexo01->receitadecontribuicoes        > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->receitadecontribuicoes, 2, '.', '')):"0");
        $oDadosAnexo01->receitaPatrimonial            = ($oDadosValoresAnexo01->receitapatrimonial            > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->receitapatrimonial, 2, '.', '')):"0");
        $oDadosAnexo01->receitaAgropecuaria           = ($oDadosValoresAnexo01->receitaagropecuaria           > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->receitaagropecuaria, 2, '.', '')):"0");
        $oDadosAnexo01->receitaIndustrial             = ($oDadosValoresAnexo01->receitaindustrial             > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->receitaindustrial, 2, '.', '')):"0");
        $oDadosAnexo01->receitadeServicos             = ($oDadosValoresAnexo01->receitadeservicos             > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->receitadeservicos, 2, '.', '')):"0");
        $oDadosAnexo01->transferenciasCorrentes       = ($oDadosValoresAnexo01->transferenciascorrentes       > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->transferenciascorrentes, 2, '.', '')):"0");
        $oDadosAnexo01->outrasReceitasCorrentes       = ($oDadosValoresAnexo01->outrasreceitascorrentes       > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->outrasreceitascorrentes, 2, '.', '')):"0");
        $oDadosAnexo01->deducaoRestituicoes           = ($oDadosValoresAnexo01->deducaorestituicoes           > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->deducaorestituicoes, 2, '.', '')):"0");
        $oDadosAnexo01->deducaoTransferenciasFundeb   = ($oDadosValoresAnexo01->deducaotransferenciasfundeb   > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->deducaotransferenciasfundeb, 2, '.', '')):"0");
        $oDadosAnexo01->outrasDeducoes                = ($oDadosValoresAnexo01->outrasdeducoes                > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->outrasdeducoes, 2, '.', '')):"0");
        $oDadosAnexo01->despesaPessoalEncargosSociais = ($oDadosValoresAnexo01->despesapessoalencargossociais > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->despesapessoalencargossociais, 2, '.', '')):"0");
        $oDadosAnexo01->despesaJurosEncargosDivida    = ($oDadosValoresAnexo01->despesajurosencargosdivida    > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->despesajurosencargosdivida, 2, '.', '')):"0");
        $oDadosAnexo01->outrasDespesasCorrentes       = ($oDadosValoresAnexo01->outrasdespesascorrentes       > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->outrasdespesascorrentes, 2, '.', '')):"0");
        $oDadosAnexo01->receitaOperacoesCredito       = ($oDadosValoresAnexo01->receitaoperacoescredito       > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->receitaoperacoescredito, 2, '.', '')):"0");
        $oDadosAnexo01->receitaAlienacaoBens          = ($oDadosValoresAnexo01->receitaalienacaobens          > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->receitaalienacaobens, 2, '.', '')):"0");
        $oDadosAnexo01->receitaAmortizacaoEmprestimos = ($oDadosValoresAnexo01->receitaamortizacaoemprestimos > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->receitaamortizacaoemprestimos, 2, '.', '')):"0");
        $oDadosAnexo01->transferenciasCapital         = ($oDadosValoresAnexo01->transferenciascapital         > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->transferenciascapital, 2, '.', '')):"0");
        $oDadosAnexo01->outrasReceitasCapital         = ($oDadosValoresAnexo01->outrasreceitascapital         > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->outrasreceitascapital, 2, '.', '')):"0");
        $oDadosAnexo01->receitasIntraorcamentarias    = ($oDadosValoresAnexo01->receitasintraorcamentarias    > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->receitasintraorcamentarias, 2, '.', '')):"0");
        $oDadosAnexo01->despesaInvestimentos          = ($oDadosValoresAnexo01->despesainvestimentos          > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->despesainvestimentos, 2, '.', '')):"0");
        $oDadosAnexo01->despesaInversoesFinanceiras   = ($oDadosValoresAnexo01->despesainversoesfinanceiras   > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->despesainversoesfinanceiras, 2, '.', '')):"0");
        $oDadosAnexo01->despesaAmortizacaoDivida      = ($oDadosValoresAnexo01->despesaamortizacaodivida      > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->despesaamortizacaodivida, 2, '.', '')):"0");
        $oDadosAnexo01->reservaContingencia           = ($oDadosValoresAnexo01->reservacontingencia           > 0?str_replace(".", "", number_format($oDadosValoresAnexo01->reservacontingencia, 2, '.', '')):"0");
        ;
        $this->addElement($oDadosAnexo01, $oAnexo01);
    
        $sSqlDadosDespesas = "select orcelemento.o56_elemento   as elemento,
  			                     orcdotacao.o58_orgao       as orgao,
  			                     orcorgao.o40_descr         as orgao_nome,
  			                     orcdotacao.o58_unidade     as unidade,
  			                     orcunidade.o41_descr       as unidade_nome,
  			                     orcdotacao.o58_funcao      as funcao,
  			                     orcfuncao.o52_descr        as funcao_nome,
  			                     orcdotacao.o58_subfuncao   as subfuncao,
  			                     orcsubfuncao.o53_descr     as subfuncao_nome,
  			                     orcdotacao.o58_programa    as programa,
  			                     orcprograma.o54_descr      as programa_nome,
  			                     orcdotacao.o58_projativ    as acao,
  			                     orcprojativ.o55_descr      as acao_nome,
  			                     orcprojativ.o55_tipo       as acao_tipo,
  			                     sum(orcdotacao.o58_valor)  as valor
  			                from orcdotacao
  			                     inner join orcorgao       on orcorgao.o40_anousu                                       = orcdotacao.o58_anousu
  			                                              and orcorgao.o40_orgao                                        = orcdotacao.o58_orgao
                                 inner join orcunidade     on orcunidade.o41_anousu                                     = orcdotacao.o58_anousu
  			                                              and orcunidade.o41_orgao                                      = orcdotacao.o58_orgao
  			                                              and orcunidade.o41_unidade                                    = orcdotacao.o58_unidade 
                                 inner join orcprograma    on orcprograma.o54_anousu                                    = orcdotacao.o58_anousu
  			                                              and orcprograma.o54_programa                                  = orcdotacao.o58_programa
  	                             inner join orcprojativ    on orcprojativ.o55_anousu                                    = orcdotacao.o58_anousu
  			                                              and orcprojativ.o55_projativ                                  = orcdotacao.o58_projativ
  			                     inner join orcelemento    on orcelemento.o56_codele                                    = orcdotacao.o58_codele 
  			                                              and orcelemento.o56_anousu                                    = orcdotacao.o58_anousu
                                 inner join orctiporec     on orctiporec.o15_codigo                                     = orcdotacao.o58_codigo
  			                     inner join concarpeculiar on concarpeculiar.c58_sequencial                             = orcdotacao.o58_concarpeculiar
  			                     inner join orcfuncao      on orcfuncao.o52_funcao                                      = orcdotacao.o58_funcao
                                 inner join ppasubtitulolocalizadorgasto on ppasubtitulolocalizadorgasto.o11_sequencial = orcdotacao.o58_localizadorgastos 
                                 inner join orcsubfuncao   on orcsubfuncao.o53_subfuncao = orcdotacao.o58_subfuncao
  			               where o58_anousu = ".$this->getAno()."
  			                -- and o58_unidade = ".$this->getCodigoUnidade()."
  			                -- and o58_orgao = ".$this->getCodigoOrgao()."
  			               group by orcelemento.o56_elemento,
                                    orcdotacao.o58_orgao,    
                                    orcorgao.o40_descr,      
                                    orcdotacao.o58_unidade,  
                                    orcunidade.o41_descr,    
                                    orcdotacao.o58_funcao,   
                                    orcfuncao.o52_descr,     
                                    orcdotacao.o58_subfuncao,
                                    orcsubfuncao.o53_descr,  
                                    orcdotacao.o58_programa, 
                                    orcprograma.o54_descr,   
                                    orcdotacao.o58_projativ, 
                                    orcprojativ.o55_descr,   
                                    orcprojativ.o55_tipo
  			               order by orcdotacao.o58_orgao,
  			                 		orcdotacao.o58_unidade,
  			                 		orcdotacao.o58_funcao,
  			                 		orcdotacao.o58_subfuncao,
  			                 		orcdotacao.o58_programa,
  			                 		orcdotacao.o58_projativ,
  			                 		orcelemento.o56_elemento";
        $rsDadosDespesas = db_query($sSqlDadosDespesas);
        $iDespesasNumRoWs = pg_num_rows($rsDadosDespesas);
        if ($iDespesasNumRoWs > 0) {
            for ($iRow = 0; $iRow < $iDespesasNumRoWs; $iRow++) {
                $oDespesa = db_utils::fieldsMemory($rsDadosDespesas, $iRow);
        
                /*
                 * anexo02
                 * despesaA02
                 *
                 */
                $despesaA02 = $this->oDocumento->createElement("despesaA02");
                $oLinhaDespesa = new stdClass();
                $oLinhaDespesa->codigoDespesa = str_pad(substr($oDespesa->elemento, 1, 8), 8, "0", STR_PAD_LEFT);
                $oLinhaDespesa->valorDespesa  = ($oDespesa->valor > 0?str_replace(".", "", number_format($oDespesa->valor, 2, '.', '')):"0");
            
                $this->addElement($oLinhaDespesa, $despesaA02);
                $despesasA02->appendChild($despesaA02);
        
                /*
                 * anexo06
                 * orgaoA06
                 *
                 * anexo09
                 * orgaoA09
                 */
        
                if (!isset($orgaosA06)) {
                    $orgaosA06 = $this->oDocumento->createElement("orgaosA06");
                }
        
                if (!in_array($oDespesa->orgao, $aOrgaosGerados)) {
                    $unidadesA06 = $this->oDocumento->createElement("unidadesA06");
                    $unidadesA09 = $this->oDocumento->createElement("unidadesA09");
          
                    $orgaoA06  = $this->oDocumento->createElement("orgaoA06");
                    $orgaoA09  = $this->oDocumento->createElement("orgaoA09");
          
                    $oDadosOrgao = new stdClass();
                    $oDadosOrgao->codigoOrgao = $oDespesa->orgao;
                    $oDadosOrgao->nomeOrgao = db_removeAcentuacao($oDespesa->orgao_nome);
          
                    $this->addElement($oDadosOrgao, $orgaoA06);
                    $this->addElement($oDadosOrgao, $orgaoA09);
                    $orgaosA06->appendChild($orgaoA06);
                    $orgaosA09->appendChild($orgaoA09);
                }
                $aOrgaosGerados[] = $oDespesa->orgao;
        
                /*
                 * anexo06
                 * unidadesA06
                 *
                 * anexo09
                 * unidadesA09
                 */
                $sIdUnidade = $oDespesa->orgao."|".$oDespesa->unidade;
                if (!in_array($sIdUnidade, $aUnidadesGeradas)) {
                    $funcoesA06 = $this->oDocumento->createElement("funcoesA06");
                        
                    $unidadeA06 = $this->oDocumento->createElement("unidadeA06");
                    $unidadeA09 = $this->oDocumento->createElement("unidadeA09");
          
                    $oDadosUnidade = new stdClass();
                    $oDadosUnidade->codigoUnidadeGestora = $oDespesa->unidade;
                    $oDadosUnidade->nomeUnidadeGestora   = db_removeAcentuacao($oDespesa->unidade_nome);
                    $this->addElement($oDadosUnidade, $unidadeA06);
                    $unidadesA06->appendChild($unidadeA06);
          
                    $this->addElement($oDadosUnidade, $unidadeA09);
          
                    $sSqlValoresFuncoesUnidade = "select sum(case when o58_funcao = 1  then o58_valor else 0 end) as funcaolegislativa,       
                                               sum(case when o58_funcao = 4  then o58_valor else 0 end) as funcaoadministracao,     
                                               sum(case when o58_funcao = 6  then o58_valor else 0 end) as funcaosegurancapublica,  
                                               sum(case when o58_funcao = 8  then o58_valor else 0 end) as funcaoassistenciasocial, 
                                               sum(case when o58_funcao = 9  then o58_valor else 0 end) as funcaoprevidenciasocial,
                                               sum(case when o58_funcao = 10 then o58_valor else 0 end) as funcaosaude,             
                                               sum(case when o58_funcao = 11 then o58_valor else 0 end) as funcaotrabalho,          
                                               sum(case when o58_funcao = 12 then o58_valor else 0 end) as funcaoeducacao,          
                                               sum(case when o58_funcao = 13 then o58_valor else 0 end) as funcaocultura,           
                                               sum(case when o58_funcao = 14 then o58_valor else 0 end) as funcaodireitoscidadania, 
                                               sum(case when o58_funcao = 15 then o58_valor else 0 end) as funcaourbanismo,         
                                               sum(case when o58_funcao = 16 then o58_valor else 0 end) as funcaohabitacao,         
                                               sum(case when o58_funcao = 17 then o58_valor else 0 end) as funcaosaneamento,
                                               sum(case when o58_funcao = 18 then o58_valor else 0 end) as funcaogestaoambiental,   
                                               sum(case when o58_funcao = 19 then o58_valor else 0 end) as funcaocienciatecnologia, 
                                               sum(case when o58_funcao = 20 then o58_valor else 0 end) as funcaoagricultura,       
                                               sum(case when o58_funcao = 22 then o58_valor else 0 end) as funcaoindustria,         
                                               sum(case when o58_funcao = 23 then o58_valor else 0 end) as funcaocomercioservicos,  
                                               sum(case when o58_funcao = 24 then o58_valor else 0 end) as funcaocomunicacoes,      
                                               sum(case when o58_funcao = 25 then o58_valor else 0 end) as funcaoenergia,           
                                               sum(case when o58_funcao = 26 then o58_valor else 0 end) as funcaotransporte,        
                                               sum(case when o58_funcao = 27 then o58_valor else 0 end) as funcaodesportolazer,     
                                               sum(case when o58_funcao = 28 then o58_valor else 0 end) as funcaoencargosespeciais 
  	  	  		                          from orcdotacao
  	  	  		                         where o58_anousu  = ".$this->getAno()."
  	  	  		                           and o58_orgao   = {$oDespesa->orgao}
  	  	  		                           and o58_unidade = {$oDespesa->unidade}";
                    $rsValoresFuncoesUnidade = db_query($sSqlValoresFuncoesUnidade);
                    $oValoresFuncoesUnidade = db_utils::fieldsMemory($rsValoresFuncoesUnidade, 0);
          
                    $oDadosValoresFuncoesUnidade = new stdClass();
                    $oDadosValoresFuncoesUnidade->valorFuncaoLegislativa       = ($oValoresFuncoesUnidade->funcaolegislativa       >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaolegislativa, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoAdministracao     = ($oValoresFuncoesUnidade->funcaoadministracao     >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaoadministracao, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoSegurancaPublica  = ($oValoresFuncoesUnidade->funcaosegurancapublica  >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaosegurancapublica, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoAssistenciaSocial = ($oValoresFuncoesUnidade->funcaoassistenciasocial >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaoassistenciasocial, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoPrevidenciaSocial = ($oValoresFuncoesUnidade->funcaoprevidenciasocial >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaoprevidenciasocial, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoSaude             = ($oValoresFuncoesUnidade->funcaosaude             >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaosaude, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoTrabalho          = ($oValoresFuncoesUnidade->funcaotrabalho          >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaotrabalho, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoEducacao          = ($oValoresFuncoesUnidade->funcaoeducacao          >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaoeducacao, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoCultura           = ($oValoresFuncoesUnidade->funcaocultura           >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaocultura, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoDireitosCidadania = ($oValoresFuncoesUnidade->funcaodireitoscidadania >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaodireitoscidadania, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoUrbanismo         = ($oValoresFuncoesUnidade->funcaourbanismo         >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaourbanismo, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoHabitacao         = ($oValoresFuncoesUnidade->funcaohabitacao         >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaohabitacao, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoSaneamento        = ($oValoresFuncoesUnidade->funcaosaneamento        >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaosaneamento, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoGestaoAmbiental   = ($oValoresFuncoesUnidade->funcaogestaoambiental   >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaogestaoambiental, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoCienciaTecnologia = ($oValoresFuncoesUnidade->funcaocienciatecnologia >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaocienciatecnologia, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoAgricultura       = ($oValoresFuncoesUnidade->funcaoagricultura       >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaoagricultura, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoIndustria         = ($oValoresFuncoesUnidade->funcaoindustria         >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaoindustria, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoComercioServicos  = ($oValoresFuncoesUnidade->funcaocomercioservicos  >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaocomercioservicos, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoComunicacoes      = ($oValoresFuncoesUnidade->funcaocomunicacoes      >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaocomunicacoes, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoEnergia           = ($oValoresFuncoesUnidade->funcaoenergia           >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaoenergia, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoTransporte        = ($oValoresFuncoesUnidade->funcaotransporte        >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaotransporte, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoDesportoLazer     = ($oValoresFuncoesUnidade->funcaodesportolazer     >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaodesportolazer, 2, '.', '')):"0");
                    $oDadosValoresFuncoesUnidade->valorFuncaoEncargosEspeciais = ($oValoresFuncoesUnidade->funcaoencargosespeciais >0?str_replace(".", "", number_format($oValoresFuncoesUnidade->funcaoencargosespeciais, 2, '.', '')):"0");
                    $this->addElement($oDadosValoresFuncoesUnidade, $unidadeA09);
                    $unidadesA09->appendChild($unidadeA09);
                }
                $aUnidadesGeradas[] = $sIdUnidade;
        
        
                /*
                 * anexo06
                 * funcoesA06
                 *
                 * anexo07
                 * funcoesA07
                 *
                 * anexo08
                 * funcoesA08
                 */
                $sIdFuncao = $oDespesa->orgao."|".$oDespesa->unidade."|".$oDespesa->funcao;
                if (!in_array($sIdFuncao, $aFuncoesGeradas)) {
                    $subfuncoesA06  = $this->oDocumento->createElement("subfuncoesA06");
                    $subfuncoesA07  = $this->oDocumento->createElement("subfuncoesA07");
                    $subfuncoesA08  = $this->oDocumento->createElement("subfuncoesA08");
          
                    $funcaoA06  = $this->oDocumento->createElement("funcaoA06");
                    $funcaoA07  = $this->oDocumento->createElement("funcaoA07");
                    $funcaoA08  = $this->oDocumento->createElement("funcaoA08");
          
                    $oDadosFuncao = new stdClass();
                    if ($oDespesa->funcao == 99) {
                        $oDespesa->funcao = 28;
                    }
                    $oDadosFuncao->codigoFuncao = str_pad($oDespesa->funcao, 2, '0', STR_PAD_LEFT);
          
                    $this->addElement($oDadosFuncao, $funcaoA06);
                    $this->addElement($oDadosFuncao, $funcaoA07);
                    $this->addElement($oDadosFuncao, $funcaoA08);
          
                    $funcoesA06->appendChild($funcaoA06);
                    $funcoesA07->appendChild($funcaoA07);
                    $funcoesA08->appendChild($funcaoA08);
                }
                $aFuncoesGeradas[] = $sIdFuncao;
        
        
                /*
                 * anexo06
                 * subfuncoesA06
                 *
                 * anexo07
                 * subfuncoesA07
                 *
                 * anexo08
                 * subfuncoesA08
                 */
                $sIdSubFuncao = $oDespesa->orgao."|".$oDespesa->unidade."|".$oDespesa->funcao."|".$oDespesa->subfuncao;
                if (!in_array($sIdSubFuncao, $aSubFuncoesGeradas)) {
                    $subfuncaoA06   = $this->oDocumento->createElement("subfuncaoA06");
                    $subfuncaoA07   = $this->oDocumento->createElement("subfuncaoA07");
                    $subfuncaoA08   = $this->oDocumento->createElement("subfuncaoA08");
          
                    $programasA06 = $this->oDocumento->createElement("programasA06");
                    $programasA07 = $this->oDocumento->createElement("programasA07");
                    $programasA08 = $this->oDocumento->createElement("programasA08");
          
                    $oDadosSubFuncao = new stdClass();
                    if (in_array($oDespesa->subfuncao, array(999))) {
                        $oDespesa->subfuncao = 846;
                    }
                    $oDadosSubFuncao->codigoSubfuncao = str_pad($oDespesa->subfuncao, 3, '0', STR_PAD_LEFT);
          
                    $this->addElement($oDadosSubFuncao, $subfuncaoA06);
                    $this->addElement($oDadosSubFuncao, $subfuncaoA07);
                    $this->addElement($oDadosSubFuncao, $subfuncaoA08);
                    $subfuncoesA06->appendChild($subfuncaoA06);
                    $subfuncoesA07->appendChild($subfuncaoA07);
                    $subfuncoesA08->appendChild($subfuncaoA08);
                }
                $aSubFuncoesGeradas[] = $sIdSubFuncao;

        
                /*
                 * Anexo06
                 * programas
                 *
                 * Anexo06
                 * acoes
                 *
                 * Anexo07
                 * programas
                 *
                 * Anexo08
                 * programas
                 *
                 */
                $sIdPrograma = $oDespesa->orgao."|".$oDespesa->unidade."|".$oDespesa->funcao."|".$oDespesa->subfuncao."|".$oDespesa->programa;
                if (!in_array($sIdPrograma, $aProgramasGerados)) {
                    $acoesA06 = $this->oDocumento->createElement("acoesA06");
          
                    $programaA06 = $this->oDocumento->createElement("programaA06");
                    $oDadosProgramas = new stdClass();
                    $oDadosProgramas->codigoPrograma = str_pad($oDespesa->programa, 4, '0', STR_PAD_LEFT);
                    $oDadosProgramas->nomePrograma   = db_removeAcentuacao($oDespesa->programa_nome);
                    $this->addElement($oDadosProgramas, $programaA06);
                    $programasA06->appendChild($programaA06);
                      
                  /*
                   * Anexo07
                   * programas
                   */
                    $programaA07 = $this->oDocumento->createElement("programaA07");
          
                    $sSqlValoresPrograma = "select sum(case when o55_tipo = 1 then o58_valor else 0 end) as valor_projeto,
  	  	                                 sum(case when o55_tipo = 2 then o58_valor else 0 end) as valor_atividade
  	  	                            from orcdotacao
  	  	                                 inner join orcprojativ  on orcprojativ.o55_anousu   = orcdotacao.o58_anousu 
  	  	                                                        and orcprojativ.o55_projativ = orcdotacao.o58_projativ  
  	  	                           where orcdotacao.o58_orgao     = {$oDespesa->orgao}
  	  	                             and orcdotacao.o58_unidade   = {$oDespesa->unidade}
  	  	                             and orcdotacao.o58_funcao    = {$oDespesa->funcao}
  	  	                             and orcdotacao.o58_subfuncao = {$oDespesa->subfuncao}
  	  	                             and orcdotacao.o58_programa  = {$oDespesa->programa}";
                    $rsValoresPrograma = db_query($sSqlValoresPrograma);
                    $oValoresPrograma = db_utils::fieldsMemory($rsValoresPrograma, 0);
          
                    $oDadosValoresPrograma = new stdClass();
                    $oDadosValoresPrograma->valorProjeto   = ($oValoresPrograma->valor_projeto > 0?str_replace(".", "", number_format($oValoresPrograma->valor_projeto, 2, '.', '')):"0");
                    $oDadosValoresPrograma->valorAtividade = ($oValoresPrograma->valor_atividade > 0?str_replace(".", "", number_format($oValoresPrograma->valor_atividade, 2, '.', '')):"0");
                    $this->addElement($oDadosProgramas, $programaA07);
                    $this->addElement($oDadosValoresPrograma, $programaA07);
                    $programasA07->appendChild($programaA07);
            
          
                  /*
                   * Anexo08
                   * programas
                   */
                    $programaA08 = $this->oDocumento->createElement("programaA08");
          
                    $sSqlValoresPrograma = "select sum(case when o58_codigo = 100000  then o58_valor else 0 end) as valor_ordinario,
  	  	                                 sum(case when o58_codigo <> 100000 then o58_valor else 0 end) as valor_vinculado
  	  	                            from orcdotacao
					         inner join orctiporec on o58_codigo = o15_codigo
  	  	                            where orcdotacao.o58_orgao     = {$oDespesa->orgao}
  	  	                              and orcdotacao.o58_unidade   = {$oDespesa->unidade}
  	  	                              and orcdotacao.o58_funcao    = {$oDespesa->funcao}
  	  	                              and orcdotacao.o58_subfuncao = {$oDespesa->subfuncao}
  	  	                              and orcdotacao.o58_programa  = {$oDespesa->programa}";
                    $rsValoresPrograma = db_query($sSqlValoresPrograma);
                    $oValoresPrograma = db_utils::fieldsMemory($rsValoresPrograma, 0);
          
                    $oDadosValoresPrograma = new stdClass();
                    $oDadosValoresPrograma->valorOrdinario = ($oValoresPrograma->valor_ordinario > 0?str_replace(".", "", number_format($oValoresPrograma->valor_ordinario, 2, '.', '')):"0");
                    $oDadosValoresPrograma->valorVinculado = ($oValoresPrograma->valor_vinculado > 0?str_replace(".", "", number_format($oValoresPrograma->valor_vinculado, 2, '.', '')):"0");
                    $this->addElement($oDadosProgramas, $programaA08);
                    $this->addElement($oDadosValoresPrograma, $programaA08);
                    $programasA08->appendChild($programaA08);
          
          
                  /*
                   * Anexo06
                   * acoes
                   */
          
                  /*
                   * Buscamos os dados das acoes dos programas
                   */
                    $acaoA06 = $this->oDocumento->createElement("acaoA06");
          
                    $sSqlValoresAcao = "select sum(case when o55_tipo = 1 then o58_valor else 0 end) as valor_projeto,
  	  	                             sum(case when o55_tipo = 2 then o58_valor else 0 end) as valor_atividade
  	  	                        from orcdotacao
  	  	                             inner join orcprojativ  on orcprojativ.o55_anousu   = orcdotacao.o58_anousu 
  	  	                                                    and orcprojativ.o55_projativ = orcdotacao.o58_projativ
  	  	                       where o58_orgao     = {$oDespesa->orgao}
  	  	                         and o58_unidade   = {$oDespesa->unidade}
  	  	                         and o58_funcao    = {$oDespesa->funcao}
  	  	                         and o58_subfuncao = {$oDespesa->subfuncao}
  	  	                         and o58_programa  = {$oDespesa->programa}
  	  	                         and o58_projativ  = {$oDespesa->acao}";
                    $rsValoresAcao = db_query($sSqlValoresAcao);
                    $oValoresAcao = db_utils::fieldsMemory($rsValoresAcao, 0);
          
                    $oDadosAcao = new stdClass();
                    $oDadosAcao->codigoAcao     = str_pad($oDespesa->acao, 8, '0', STR_PAD_LEFT);
                    $oDadosAcao->nomeAcao       = db_removeAcentuacao($oDespesa->acao_nome);
                    $oDadosAcao->valorProjeto   = ($oValoresAcao->valor_projeto > 0?str_replace(".", "", number_format($oValoresAcao->valor_projeto, 2, '.', '')):"0");
                    $oDadosAcao->valorAtividade = ($oValoresAcao->valor_atividade > 0?str_replace(".", "", number_format($oValoresAcao->valor_atividade, 2, '.', '')):"0");
                    $this->addElement($oDadosAcao, $acaoA06);
                    $acoesA06->appendChild($acaoA06);
                }
                $aProgramasGerados[] = $sIdPrograma;
        
        
                /*
                 * anexo06
                 */
                $programaA06->appendChild($acoesA06);
                $subfuncaoA06->appendChild($programasA06);
                $funcaoA06->appendChild($subfuncoesA06);
                $unidadeA06->appendChild($funcoesA06);
                $orgaoA06->appendChild($unidadesA06);
        
                /*
                 * anexo07
                 */
                $subfuncaoA07->appendChild($programasA07);
                $funcaoA07->appendChild($subfuncoesA07);
        
                /*
                 * anexo08
                 */
                $subfuncaoA08->appendChild($programasA08);
                $funcaoA08->appendChild($subfuncoesA08);
        
                /*
                 * anexo09
                 */
                $orgaoA09->appendChild($unidadesA09);
            }
        }
    
        $oAnexo02->appendChild($despesasA02);
    
      /*
       * Anexo02
       * receita
       */
        $sSqlDadosReceita = "select substr(o57_fonte,1,10) as codigo, 
  	                            sum(o70_valor) as valor
  			               from orcreceita
  			                    inner join orcfontes  on o57_codfon = o70_codfon 
  			                                         and o57_anousu = o70_anousu
  			              where o70_anousu = {$this->getAno()}
  	                      group by substr(o57_fonte,1,10)";
        $rsDadosReceita = db_query($sSqlDadosReceita);
        $iNumRowsReceita = pg_num_rows($rsDadosReceita);
        for ($iRow = 0; $iRow < $iNumRowsReceita; $iRow++) {
            $oReceita = db_utils::fieldsMemory($rsDadosReceita, $iRow);
      
            if ($oReceita->valor < 0) {
                $oReceita->valor = 0;
            }
            $receitaA02 = $this->oDocumento->createElement("receitaA02");
            $oDadosReceita = new stdClass();
            $oDadosReceita->codigoReceita = $oReceita->codigo;
            $oDadosReceita->valorReceita  = ($oReceita->valor > 0?str_replace(".", "", number_format($oReceita->valor, 2, '.', '')):"0");
            $this->addElement($oDadosReceita, $receitaA02);
            $receitasA02->appendChild($receitaA02);
        }
    
    
        $oAnexo02->appendChild($receitasA02);
        $oAnexo06->appendChild($orgaosA06);
        $oAnexo07->appendChild($funcoesA07);
        $oAnexo08->appendChild($funcoesA08);
        $oAnexo09->appendChild($orgaosA09);
    
      /*
       * Adicionamos os dados na remessa
       */
        $oRemessa->appendChild($oAnexo01);
        $oRemessa->appendChild($oAnexo02);
        $oRemessa->appendChild($oAnexo06);
        $oRemessa->appendChild($oAnexo07);
        $oRemessa->appendChild($oAnexo08);
        $oRemessa->appendChild($oAnexo09);
    
      //adicionamos a remessa ao arquivo
        $this->oDocumento->appendChild($oRemessa);
    
        $oRetornoValidacao = $this->validarXML($this->oDocumento, 'model/contabilidade/arquivos/siai/v2018/xsd/XSD_LOA.txt');
        if ($oRetornoValidacao->lErro == true) {
            throw new Exception($oRetornoValidacao->sMsg);
        }
        $this->sArquivo = $this->oDocumento->saveXML();
    }
  
  
    public function getArquivo()
    {
        return $this->sArquivo;
    }
}
