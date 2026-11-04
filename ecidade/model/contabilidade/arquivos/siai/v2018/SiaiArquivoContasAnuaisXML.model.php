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
require_once(modification("interfaces/iPadArquivoTxtBase.interface.php"));
require_once(modification("model/contabilidade/arquivos/siai/SiaiArquivoBase.model.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("libs/db_stdlib.php"));

class SiaiArquivoContasAnuaisXML extends SiaiArquivoBase
{

    protected $lGeraAnexo01;
    protected $lGeraAnexo02;
    protected $lGeraAnexo06;
    protected $lGeraAnexo07;
    protected $lGeraAnexo08;
    protected $lGeraAnexo09;
    protected $lGeraAnexo10;
    protected $lGeraAnexo11;
    protected $lGeraAnexo12;
    protected $lGeraQuadro01;
    protected $lGeraQuadro02;
    protected $lValidaXML;
  
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
  
  /*
   * Seta se os anexos serÃ£o gerados
   */
    public function setGeraAnexo01($lGeraAnexo01)
    {
        $this->lGeraAnexo01 = $lGeraAnexo01;
    }

    public function setGeraAnexo02($lGeraAnexo02)
    {
        $this->lGeraAnexo02 = $lGeraAnexo02;
    }

    public function setGeraAnexo06($lGeraAnexo06)
    {
        $this->lGeraAnexo06 = $lGeraAnexo06;
    }

    public function setGeraAnexo07($lGeraAnexo07)
    {
        $this->lGeraAnexo07 = $lGeraAnexo07;
    }

    public function setGeraAnexo08($lGeraAnexo08)
    {
        $this->lGeraAnexo08 = $lGeraAnexo08;
    }

    public function setGeraAnexo09($lGeraAnexo09)
    {
        $this->lGeraAnexo09 = $lGeraAnexo09;
    }

    public function setGeraAnexo10($lGeraAnexo10)
    {
        $this->lGeraAnexo10 = $lGeraAnexo10;
    }

    public function setGeraAnexo11($lGeraAnexo11)
    {
        $this->lGeraAnexo11 = $lGeraAnexo11;
    }

    public function setGeraAnexo12($lGeraAnexo12)
    {
        $this->lGeraAnexo12 = $lGeraAnexo12;
    }
  
    public function setGeraQuadro01($lGeraQuadro01)
    {
        $this->lGeraQuadro01 = $lGeraQuadro01;
    }

    public function setGeraQuadro02($lGeraQuadro02)
    {
        $this->lGeraQuadro02 = $lGeraQuadro02;
    }

  /*
   * Retorna se os anexos serão gerados
   */
    public function getGeraAnexo01()
    {
        return $this->lGeraAnexo01;
    }

    public function getGeraAnexo02()
    {
        return $this->lGeraAnexo02;
    }

    public function getGeraAnexo06()
    {
        return $this->lGeraAnexo06;
    }

    public function getGeraAnexo07()
    {
        return $this->lGeraAnexo07;
    }

    public function getGeraAnexo08()
    {
        return $this->lGeraAnexo08;
    }

    public function getGeraAnexo09()
    {
        return $this->lGeraAnexo09;
    }

    public function getGeraAnexo10()
    {
        return $this->lGeraAnexo10;
    }

    public function getGeraAnexo11()
    {
        return $this->lGeraAnexo11;
    }

    public function getGeraAnexo12()
    {
        return $this->lGeraAnexo12;
    }
  
    public function getGeraQuadro01()
    {
        return $this->lGeraQuadro01;
    }
  
    public function getGeraQuadro02()
    {
        return $this->lGeraQuadro02;
    }

  /**
   * Gera o arquivo XML
   */
    public function processar()
    {
    
        $oNodeRemessa = $this->oDocumento->createElement("remessa");
    
        $sSqlCPFGestor = "select z01_cgccpf as cpf
                       from plugins.assinaturaordenadordespesa
                            inner join db_departorg on db_departorg.db01_coddepto = assinaturaordenadordespesa.departamento
                            inner join cgm          on cgm.z01_numcgm             = assinaturaordenadordespesa.numcgm
                      where db01_orgao   = 25
                        and db01_unidade = 1
                        and db01_anousu  = {$this->iAno}
                        and principal    = 't'
                      limit 1";
        $sCPFGestor    = db_utils::fieldsMemory(db_query($sSqlCPFGestor), 0)->cpf;
    
      /*
       * linha da remessa
       */
        $oDadosRemessa = new stdClass();
        $oDadosRemessa->codigoOrgao    = $this->getCodigoOrgaoTCE();
        $oDadosRemessa->cpfGestor      = $sCPFGestor;
        $oDadosRemessa->tipoRemessa    = 7;
        $oDadosRemessa->ano            = $this->getAno();
        $oDadosRemessa->dataCriacao    = str_replace("/", "-", db_formatar($this->getDataGeracao(), "d"));
        $oDadosRemessa->sistemaGerador = "ECIDADE";
        $this->addElement($oDadosRemessa, $oNodeRemessa);
    
    
    
        if ($this->getGeraAnexo01()) {
          /*
           * criamos os nodes XML
           */
            $oNodeAnexo01 = $this->oDocumento->createElement("anexo01");
            $oNodeAnexoDados01 = $this->oDocumento->createElement("anexoDados01");
      
          /*
           * Objeto com as colunas que irao compor o Node AnexoDados01
           */
            $oDadosAnexoDados01 = new stdClass();
            $oDadosAnexoDados01->receitaTributaria             = 0;
            $oDadosAnexoDados01->receitadeContribuicoes        = 0;
            $oDadosAnexoDados01->receitaPatrimonial            = 0;
            $oDadosAnexoDados01->receitaAgropecuaria           = 0;
            $oDadosAnexoDados01->receitaIndustrial             = 0;
            $oDadosAnexoDados01->receitadeServicos             = 0;
            $oDadosAnexoDados01->transferenciasCorrentes       = 0;
            $oDadosAnexoDados01->outrasReceitasCorrentes       = 0;
            $oDadosAnexoDados01->receitasIntraorcamentarias    = 0;
            $oDadosAnexoDados01->deducaoRestituicoes           = 0;
            $oDadosAnexoDados01->deducaoTransferenciasFundeb   = 0;
            $oDadosAnexoDados01->outrasDeducoes                = 0;
            $oDadosAnexoDados01->despesaPessoalEncargosSociais = 0;
            $oDadosAnexoDados01->despesaJurosEncargosDivida    = 0;
            $oDadosAnexoDados01->outrasDespesasCorrentes       = 0;
            $oDadosAnexoDados01->despesasIntraorcamentarias    = 0;
            $oDadosAnexoDados01->receitaOperacoesCredito       = 0;
            $oDadosAnexoDados01->receitaAlienacaoBens          = 0;
            $oDadosAnexoDados01->receitaAmortizacaoEmprestimos = 0;
            $oDadosAnexoDados01->transferenciasCapital         = 0;
            $oDadosAnexoDados01->outrasReceitasCapital         = 0;
            $oDadosAnexoDados01->despesaInvestimentos          = 0;
            $oDadosAnexoDados01->despesaInversoesFinanceiras   = 0;
            $oDadosAnexoDados01->despesaAmortizacaoDivida      = 0;
      
      
          /*
           * Buscamos as informacoes
           */
            $sSqlWork1  = " create temp table work1 as                ";
            $sSqlWork1 .= " select o56_elemento||'00' as elemento,    ";
            $sSqlWork1 .= "        o56_descr                 as descr, ";
            $sSqlWork1 .= "        0::float8                 as valor  ";
            $sSqlWork1 .= "   from orcelemento                         ";
            $sSqlWork1 .= "  where o56_anousu = {$this->iAno}       ";
            $sSqlWork1 .= "  union                                     ";
            $sSqlWork1 .= " select o57_fonte,                         ";
            $sSqlWork1 .= "        o57_descr,                         ";
            $sSqlWork1 .= "        0::float8 as valor                 ";
            $sSqlWork1 .= "   from orcfontes                          ";
            $sSqlWork1 .= "  where o57_anousu = {$this->iAno}      ";
            $rsSqlWork1 = db_query($sSqlWork1);

            $result_rec = db_receitasaldo(11, 1, 3, true, "", $this->iAno, $this->dtDataInicial, $this->dtDataFinal);

            $valor = 0;
            for ($i = 0; $i < pg_numrows($result_rec); $i++) {
                $oRec = db_utils::fieldsMemory($result_rec, $i);
                $valor = $oRec->saldo_arrecadado;
        
                $sSqlwork1Update  = "update work1 set valor = valor+{$valor} where work1.elemento = '{$oRec->o57_fonte}'";
                $rsSqlwork1Update = db_query($sSqlwork1Update);
                $executa          = true;
                $conta            = 0;
            }


            $sWhere = "o58_instit in (1, 2, 3, 4, 5, 6, 7, 8, 9) and 
    ((o58_orgao = 11 and o58_unidade = 3) or 
     (o58_orgao = 11 and o58_unidade = 29) or 
     (o58_orgao = 11 and o58_unidade = 49) or 
     (o58_orgao = 11 and o58_unidade = 1) or 
     (o58_orgao = 11 and o58_unidade = 2) or 
     (o58_orgao = 11 and o58_unidade = 4) or 
     (o58_orgao = 11 and o58_unidade = 10) or 
     (o58_orgao = 11 and o58_unidade = 20) or 
     (o58_orgao = 11 and o58_unidade = 50) or 
     (o58_orgao = 11 and o58_unidade = 47) or 
     (o58_orgao = 12 and o58_unidade = 1) or 
     (o58_orgao = 13 and o58_unidade = 1) or 
     (o58_orgao = 13 and o58_unidade = 49) or 
     (o58_orgao = 14 and o58_unidade = 28) or 
     (o58_orgao = 14 and o58_unidade = 20) or 
     (o58_orgao = 14 and o58_unidade = 10) or 
     (o58_orgao = 14 and o58_unidade = 2) or 
     (o58_orgao = 14 and o58_unidade = 48) or 
     (o58_orgao = 14 and o58_unidade = 1) or 
     (o58_orgao = 14 and o58_unidade = 30) or 
     (o58_orgao = 14 and o58_unidade = 49) or 
     (o58_orgao = 14 and o58_unidade = 50) or 
     (o58_orgao = 14 and o58_unidade = 3) or 
     (o58_orgao = 14 and o58_unidade = 47) or 
     (o58_orgao = 15 and o58_unidade = 48) or 
     (o58_orgao = 15 and o58_unidade = 2) or 
     (o58_orgao = 15 and o58_unidade = 49) or 
     (o58_orgao = 15 and o58_unidade = 50) or 
     (o58_orgao = 15 and o58_unidade = 1) or 
     (o58_orgao = 16 and o58_unidade = 1) or 
     (o58_orgao = 17 and o58_unidade = 1) or 
     (o58_orgao = 17 and o58_unidade = 40) or 
     (o58_orgao = 17 and o58_unidade = 30) or 
     (o58_orgao = 17 and o58_unidade = 10) or 
     (o58_orgao = 17 and o58_unidade = 50) or 
     (o58_orgao = 17 and o58_unidade = 20) or 
     (o58_orgao = 18 and o58_unidade = 48) or 
     (o58_orgao = 18 and o58_unidade = 2) or 
     (o58_orgao = 18 and o58_unidade = 49) or 
     (o58_orgao = 18 and o58_unidade = 45) or 
     (o58_orgao = 18 and o58_unidade = 1) or 
     (o58_orgao = 18 and o58_unidade = 47) or 
     (o58_orgao = 18 and o58_unidade = 46) or 
     (o58_orgao = 19 and o58_unidade = 49) or 
     (o58_orgao = 19 and o58_unidade = 1) or 
     (o58_orgao = 20 and o58_unidade = 1) or 
     (o58_orgao = 20 and o58_unidade = 49) or 
     (o58_orgao = 21 and o58_unidade = 1) or 
     (o58_orgao = 22 and o58_unidade = 1) or 
     (o58_orgao = 23 and o58_unidade = 1) or 
     (o58_orgao = 23 and o58_unidade = 2) or 
     (o58_orgao = 24 and o58_unidade = 49) or 
     (o58_orgao = 24 and o58_unidade = 3) or 
     (o58_orgao = 24 and o58_unidade = 47) or 
     (o58_orgao = 24 and o58_unidade = 2) or 
     (o58_orgao = 24 and o58_unidade = 30) or 
     (o58_orgao = 24 and o58_unidade = 45) or 
     (o58_orgao = 24 and o58_unidade = 1) or 
     (o58_orgao = 24 and o58_unidade = 46) or 
     (o58_orgao = 24 and o58_unidade = 48) or 
     (o58_orgao = 24 and o58_unidade = 20) or 
     (o58_orgao = 25 and o58_unidade = 1) or 
     (o58_orgao = 25 and o58_unidade = 10) or 
     (o58_orgao = 25 and o58_unidade = 49) or 
     (o58_orgao = 26 and o58_unidade = 1) or 
     (o58_orgao = 27 and o58_unidade = 1) or 
     (o58_orgao = 28 and o58_unidade = 1) or 
     (o58_orgao = 28 and o58_unidade = 10) or 
     (o58_orgao = 29 and o58_unidade = 47) or 
     (o58_orgao = 29 and o58_unidade = 48) or 
     (o58_orgao = 29 and o58_unidade = 1) or 
     (o58_orgao = 29 and o58_unidade = 46) or 
     (o58_orgao = 29 and o58_unidade = 30) or 
     (o58_orgao = 30 and o58_unidade = 1) or 
     (o58_orgao = 31 and o58_unidade = 1) or 
     (o58_orgao = 31 and o58_unidade = 48) or 
     (o58_orgao = 32 and o58_unidade = 1) or 
     (o58_orgao = 33 and o58_unidade = 1) or 
     (o58_orgao = 34 and o58_unidade = 49) or 
     (o58_orgao = 34 and o58_unidade = 1) or 
     (o58_orgao = 35 and o58_unidade = 49) or 
     (o58_orgao = 35 and o58_unidade = 1) or 
     (o58_orgao = 35 and o58_unidade = 20) or 
     (o58_orgao = 36 and o58_unidade = 1) or 
     (o58_orgao = 77 and o58_unidade = 0) or 
     (o58_orgao = 99 and o58_unidade = 0))";

            $rsDespesa = db_dotacaosaldo(7, 3, 3, true, $sWhere, $this->iAno, $this->dtDataInicial, $this->dtDataFinal, null, null, null, 2);
            $valor      = 0;

            for ($i = 0; $i < pg_numrows($rsDespesa); $i++) {
                $oDespesa = db_utils::fieldsMemory($rsDespesa, $i);
                $valor = $oDespesa->empenhado-$oDespesa->anulado;
                $sElemento        = $oDespesa->o58_elemento;
        
                $sSqlwork1Update  = "update work1 set valor = valor+{$valor} where work1.elemento = '{$sElemento}00'";
                $rsSqlwork1Update = db_query($sSqlwork1Update);
        
                $conta            = 0;
                $executa          = true;

                while ($executa == true) {
                    $sElemento     = db_le_mae($sElemento, false);
                    $sSqlwork1Update  = "update work1 set valor = valor+{$valor} where work1.elemento = '{$sElemento}00'";
                    $rsSqlwork1Update = db_query($sSqlwork1Update);
          
                    if (substr($sElemento, 2, 13) == "0000000000000") {
                        $executa = false;
                    }
          
                    $conta ++;
                    if ($conta > 10) {
                        $executa = false;
                    }
                }
            }

            $total  = 0;
            $troca  = 1;
            $alt    = 4;
            $a      = 0;
            $b      = 0;
            $valora = 0;
            $valorb = 0;

            $descra = array();
            $vlra   = array();
            $descrb = array();
            $vlrb   = array();

            $sSqlWork1   = "   select *                                      ";
            $sSqlWork1  .= "     from work1                                  ";
            $sSqlWork1  .= "    where elemento::bigint in(411000000000000,   ";
            $sSqlWork1  .= "                              412000000000000,   ";
            $sSqlWork1  .= "                              413000000000000,   ";
            $sSqlWork1  .= "                              414000000000000,   ";
            $sSqlWork1  .= "                              415000000000000,   ";
            $sSqlWork1  .= "                              416000000000000,   ";
            $sSqlWork1  .= "                              417000000000000,   ";
            $sSqlWork1  .= "                              419000000000000,   ";
            $sSqlWork1  .= "                              470000000000000,   ";
            $sSqlWork1  .= "                              910000000000000,   ";
            $sSqlWork1  .= "                              920000000000000,   ";
            $sSqlWork1  .= "                              970000000000000,   ";
            $sSqlWork1  .= "                              980000000000000    ";
            $sSqlWork1  .= "                             )                   ";
            $sSqlWork1  .= " order by elemento                               ";

            $rsSqlWork1    = db_query($sSqlWork1);
            $iNumRowsWork1 = pg_numrows($rsSqlWork1);


            for ($i = 0; $i < $iNumRowsWork1; $i++) {
                $oElemento = db_utils::fieldsMemory($rsSqlWork1, $i);
        
                if ($oElemento->elemento != "920000000000000" && $oElemento->elemento != "980000000000000") {
                    $descra[$a] = $oElemento->descr;
                    $vlra[$a]   = $oElemento->valor;
                    $a         += 1;
                    $valora    += $oElemento->valor;
                }
            }

            $sSqlWork1     = "select * from work1 where substr(elemento,1,1) = '3' order by elemento";
            $rsSqlWork1    = db_query($sSqlWork1);
            $iNumRowsWork1 = pg_numrows($rsSqlWork1);

            for ($i = 0; $i < $iNumRowsWork1; $i++) {
                $oElemento = db_utils::fieldsMemory($rsSqlWork1, $i);
                if (substr($oElemento->elemento, 3, 12) == "000000000000" && substr($oElemento->elemento, 2, 1) != "0") {
                    if (substr($oElemento->elemento, 1, 1) == "3") {
                          $descrb[$b] = $oElemento->descr;
                          $vlrb[$b]   = $oElemento->valor;
                          $b         += 1;
                          $valorb     = ($valorb + $oElemento->valor);
                    }
                }
            }

            $numreg = (sizeof($descra)>sizeof($descrb)?sizeof($descra):sizeof($descrb));
            for ($i = 0; $i < $numreg; $i++) {
                if (isset($descra[$i])) {
                    if ($vlra[$i] == 0 || empty($vlra[$i])) {
                          $sValorA = "0.00";
                    } else {
                        $sValorA = number_format($vlra[$i], 2, '.', '');
                    }

               /*
                * 1 - Montamos os dados da stdClass com as propriedados do Node e seus valores
                */
                    if (trim($descra[$i]) == "RECEITA TRIBUTARIA") {
                        $oDadosAnexoDados01->receitaTributaria = $sValorA;
                    }
          
                    if (trim($descra[$i]) == "RECEITAS DE CONTRIBUIÇÕES") {
                        $oDadosAnexoDados01->receitadeContribuicoes = $sValorA;
                    }
          
                    if (trim($descra[$i]) == "RECEITA PATRIMONIAL") {
                        $oDadosAnexoDados01->receitaPatrimonial  = $sValorA;
                        $oDadosAnexoDados01->receitaAgropecuaria = 0.00;
                        $oDadosAnexoDados01->receitaIndustrial   = 0.00;
                    }
          
                    if (trim($descra[$i]) == "RECEITA DE SERVICOS") {
                        $oDadosAnexoDados01->receitadeServicos = $sValorA;
                    }
          
                    if (trim($descra[$i]) == "TRANSFERENCIAS CORRENTES") {
                        $oDadosAnexoDados01->transferenciasCorrentes = $sValorA;
                    }
          
                    if (trim($descra[$i]) == "OUTRAS RECEITAS CORRENTES") {
                        $oDadosAnexoDados01->outrasReceitasCorrentes = $sValorA;
                    }
          
                    if (trim($descra[$i]) == "RECEITAS INTRA-ORÇAMENTÁRIAS CORRENTES") {
                        $oDadosAnexoDados01->receitasIntraorcamentarias = $sValorA;
                    }
          
                    if (trim($descra[$i]) == "(R) DEDUCOES DA RECEITA CORRENTE") {
                        $oDadosAnexoDados01->deducaoRestituicoes         = "0.00";
                        $oDadosAnexoDados01->deducaoTransferenciasFundeb = abs($sValorA);
                        $oDadosAnexoDados01->outrasDeducoes              = "0.00";
                    }
                }

                if (isset($descrb[$i])) {
                    if ($vlrb[$i] == 0 || empty($vlrb[$i])) {
                        $sValorB = "0.00";
                    } else {
                        $sValorB = number_format($vlrb[$i], 2, '.', '');
                    }
          
              /*
               * 2 - Montamos os dados da stdClass com as propriedados do Node e seus valores
               */
                    if (trim($descrb[$i]) == "PESSOAL E ENCARGOS SOCIAIS") {
                        $oDadosAnexoDados01->despesaPessoalEncargosSociais = $sValorB;
                    }
          
                    if (trim($descrb[$i]) == "JUROS E ENCARGOS DA DÍVIDA") {
                        $oDadosAnexoDados01->despesaJurosEncargosDivida = $sValorB;
                    }
          
                    if (trim($descrb[$i]) == "OUTRAS DESPESAS CORRENTES") {
                        $oDadosAnexoDados01->outrasDespesasCorrentes    = $sValorB;
                        $oDadosAnexoDados01->despesasIntraorcamentarias = 0.00;
                    }
                }
            }

            $valord = 0;
            $valore = 0;
            $d      = 0;
            $e      = 0 ;
            $descrd = array();
            $vlrd   = array();
            $descre = array();
            $vlre   = array();

            $sSqlWork1     = "select * from work1 where substr(elemento,2,1) = '2' or substr(elemento,2,1) = '8' order by elemento";
            $rsSqlWork1    = db_query($sSqlWork1);
            $iNumRowsWork1 = pg_numrows($rsSqlWork1);

            for ($i = 0; $i < $iNumRowsWork1; $i++) {
                $oElementos = db_utils::fieldsMemory($rsSqlWork1, $i);
        
                if ((substr($oElementos->elemento, 3, 12) == "000000000000"
                 && substr($oElementos->elemento, 2, 1) != "0" && substr($oElementos->elemento, 0, 1) != "9")
                || ($oElementos->elemento == "920000000000000" || $oElementos->elemento == "980000000000000" )) {
                    if (substr($oElementos->elemento, 1, 1) == "2" || substr($oElementos->elemento, 1, 1) == "8") {
                        $descrd[$d] = $oElementos->descr;
                        $vlrd[$d]   = $oElementos->valor;
                        $d         += 1;
                        $valord     = ($valord + $oElementos->valor);
                    }
                }
            }

            $sSqlWork1     = "select * from work1 where substr(elemento,1,1) = '3' order by elemento";
            $rsSqlWork1    = db_query($sSqlWork1);
            $iNumRowsWork1 = pg_numrows($rsSqlWork1);

            for ($i = 0; $i < $iNumRowsWork1; $i++) {
                $oElementos = db_utils::fieldsMemory($rsSqlWork1, $i);
                if (substr($oElementos->elemento, 3, 12) == "000000000000" && substr($oElementos->elemento, 2, 1) != "0") {
                    if (substr($oElementos->elemento, 1, 1) == "4") {
                          $descre[$e] = $oElementos->descr;
                          $vlre[$e]   = $oElementos->valor;
                          $e         += 1;
                          $valore     = ($valore + $oElementos->valor) ;
                    }
                }
            }

            $numreg = (sizeof($descrd) > sizeof($descre)? sizeof($descrd) : sizeof($descre));
            for ($i = 0; $i < $numreg; $i++) {
                if (isset($descrd[$i])) {
                    if ($vlrd[$i] == 0 || empty($vlrd[$i])) {
                          $sValorD = "0.00";
                    } else {
                        $sValorD = number_format($vlrd[$i], 2, '.', '');
                    }

                /*
                 * 3 - Montamos os dados da stdClass com as propriedados do Node e seus valores
                 */
                    if ($descrd[$i] == "OPERACOES DE CREDITO") {
                        $oDadosAnexoDados01->receitaOperacoesCredito = $sValorD;
                    }
          
                    if ($descrd[$i] == "ALIENAÇÃO DE BENS") {
                        $oDadosAnexoDados01->receitaAlienacaoBens = $sValorD;
                        $oDadosAnexoDados01->receitaAmortizacaoEmprestimos = 0.00;
                    }
          
                    if ($descrd[$i] == "TRANSFERÊNCIAS DE CAPITAL") {
                        $oDadosAnexoDados01->transferenciasCapital = $sValorD;
                    }
          
                    if ($descrd[$i] == "OUTRAS RECEITAS DE CAPITAL") {
                        $oDadosAnexoDados01->outrasReceitasCapital = $sValorD;
                    }
                }
        
                if (isset($descre[$i])) {
                    if ($vlre[$i] == 0 || empty($vlre[$i])) {
                        $sValorE = "0.00";
                    } else {
                        $sValorE = number_format($vlre[$i], 2, '.', '');
                    }


              /*
               * 4 - Montamos os dados da stdClass com as propriedados do Node e seus valores
               */
                    if ($descre[$i] == "DESPESA DE INVESTIMENTOS") {
                        $oDadosAnexoDados01->despesaInvestimentos = $sValorE;
                    }
          
                    if ($descre[$i] == "INVERSÕES FINANCEIRAS") {
                        $oDadosAnexoDados01->despesaInversoesFinanceiras = $sValorE;
                    }
          
                    if ($descre[$i] == "AMORTIZAÇÃO DA DÍVIDA") {
                        $oDadosAnexoDados01->despesaAmortizacaoDivida = $sValorE;
                    }
                }
            }

        /*
         * Adicionamos o objeto ao nome
         * criamos assim as propriedades no xml com os valores
         */
            $this->addElement($oDadosAnexoDados01, $oNodeAnexoDados01);
            $oNodeAnexo01->appendChild($oNodeAnexoDados01);
            $oNodeRemessa->appendChild($oNodeAnexo01);
        }
    
    
    
        if ($this->getGeraAnexo02()) {
          /*
           * criamos os nodes XML
           */
            $oNodeAnexo02 = $this->oDocumento->createElement("anexo02");
            $oNodeAnexoDados02 = $this->oDocumento->createElement("anexoDados02");
            $oNodeDespesasA02 = $this->oDocumento->createElement("despesasA02");

            $sSqlDotacaoSaldo = db_dotacaosaldo(7, 3, 3, true, "substr(o56_elemento, 2, 1) in ('3', '4')", $this->iAno, $this->dtDataInicial, $this->dtDataFinal, null, null, true, 2);
            $sSqlDespesas = "select rpad(substr(o58_elemento, 2, 1)::TEXT, 8, '0') as o56_elemento, 
                              sum(empenhado-anulado) as totaldespesa 
                         from ( {$sSqlDotacaoSaldo} ) as dados 
                        group by rpad(substr(o58_elemento, 2, 1)::TEXT, 8, '0')";
            $rsDespesas = db_query($sSqlDespesas);
            for ($i=0; $i < pg_num_rows($rsDespesas); $i++) {
                $oDadosDespesa = db_utils::fieldsMemory($rsDespesas, $i);

                $oNodeDespesaA02 = $this->oDocumento->createElement("despesaA02");
        
                $oDadosDespesaA02 = new stdClass();
                $oDadosDespesaA02->codigoDespesa = $oDadosDespesa->o56_elemento;
                $oDadosDespesaA02->valorDespesa = number_format($oDadosDespesa->totaldespesa, 2, '.', '');
                $this->addElement($oDadosDespesaA02, $oNodeDespesaA02);
        
                $oNodeDespesasA02->appendChild($oNodeDespesaA02);
            }
      
            $oNodeAnexoDados02->appendChild($oNodeDespesasA02);
            $oNodeAnexo02->appendChild($oNodeAnexoDados02);
            $oNodeRemessa->appendChild($oNodeAnexo02);
        }
    
    

        if ($this->getGeraAnexo06()) {
          /*
           * Criação Nodes XML
           */
            $oNodeAnexo06      = $this->oDocumento->createElement("anexo06");
            $oNodeAnexoDados06 = $this->oDocumento->createElement("anexoDados06");
            $oNodeOrgaosA06    = $this->oDocumento->createElement("orgaosA06");
      
          /*
           * Buscamos os dados dos Orgaos
           */
            $sSqlOrgaos = "select distinct 
                            o40_orgao, 
                            o40_descr 
                       from orcorgao 
                            inner join orcdotacao on orcdotacao.o58_orgao   = orcorgao.o40_orgao
                                                 and orcdotacao.o58_anousu  = orcorgao.o40_anousu
                                                 and orcdotacao.o58_instit  = orcorgao.o40_instit
                      where orcdotacao.o58_anousu = {$this->iAno}";
            $rsOrgaos = db_query($sSqlOrgaos);
            $iLinhasOrgaos = pg_num_rows($rsOrgaos);
            for ($iIndOrgao = 0; $iIndOrgao < $iLinhasOrgaos; $iIndOrgao++) {
                $oDadosOrgao = db_utils::fieldsMemory($rsOrgaos, $iIndOrgao);
                if ($oDadosOrgao->o40_orgao == "99") {
                    continue;
                }
        
                $oNodeOrgaoA06     = $this->oDocumento->createElement("orgaoA06");
        
                $oDadosOrgaoA06 = new stdClass();
                $oDadosOrgaoA06->codigoOrgao = $oDadosOrgao->o40_orgao;
                $oDadosOrgaoA06->nomeOrgao   = db_removeAcentuacao($oDadosOrgao->o40_descr);
                $this->addElement($oDadosOrgaoA06, $oNodeOrgaoA06);
        
              /*
               * Buscamos dados das Unidades do Orgao
               */
                $oNodeUnidadesA06  = $this->oDocumento->createElement("unidadesA06");
                $sSqlUnidades = "select distinct 
                                o41_unidade, 
                                o41_descr, 
                                o41_instit 
                           from orcunidade
                                inner join orcdotacao on o58_unidade = o41_unidade
                                                     and o58_orgao   = o41_orgao
                                                     and o58_anousu  = o41_anousu
                                                     and o58_instit  = o41_instit
                          where o58_anousu = {$this->iAno}
                            and o58_orgao  = {$oDadosOrgao->o40_orgao}";
                $rsUnidades = db_query($sSqlUnidades);
                $iLinhasUnidades = pg_num_rows($rsUnidades);
                for ($iIndUnidade = 0; $iIndUnidade < $iLinhasUnidades; $iIndUnidade++) {
                    $oDadosUnidade = db_utils::fieldsMemory($rsUnidades, $iIndUnidade);
          
                    $oNodeUnidadeA06 = $this->oDocumento->createElement("unidadeA06");

                    $oDadosUnidadeA06 = new stdClass();
                    $oDadosUnidadeA06->codigoUnidadeGestora = $oDadosUnidade->o41_unidade;
                    $oDadosUnidadeA06->nomeUnidadeGestora   = db_removeAcentuacao($oDadosUnidade->o41_descr);
                    $this->addElement($oDadosUnidadeA06, $oNodeUnidadeA06);
          
                  /*
                   * Buscamos dados das Funcoes
                   */
                    $oNodeFuncoesA06 = $this->oDocumento->createElement("funcoesA06");
                    $sSqlFuncoes = "select distinct o52_funcao 
                            from orcfuncao 
                                 inner join orcdotacao on o52_funcao = o58_funcao
                           where o58_orgao   = {$oDadosOrgao->o40_orgao}
                             and o58_unidade = {$oDadosUnidade->o41_unidade}
                             and o58_anousu  = {$this->iAno}
                             and o58_instit  = {$oDadosUnidade->o41_instit}
                             and o58_orgao   = {$oDadosOrgao->o40_orgao}
                             and o58_unidade = {$oDadosUnidade->o41_unidade}";
                    $rsFuncoes = db_query($sSqlFuncoes);
                    $iLinhasFuncoes = pg_num_rows($rsFuncoes);
          
                    $aFuncoes = array();

                    for ($iIndFuncao = 0; $iIndFuncao < $iLinhasFuncoes; $iIndFuncao++) {
                        $oDadosFuncao = db_utils::fieldsMemory($rsFuncoes, $iIndFuncao);
                        if (in_array($oDadosFuncao->o52_funcao, $aFuncoes)) {
                            continue;
                        }
                        $aFuncoes[] = $oDadosFuncao->o52_funcao;
            
                        $oNodeFuncaoA06 = $this->oDocumento->createElement("funcaoA06");
            
                        $oDadosFuncaoA06 = new stdClass();
                        $oDadosFuncaoA06->codigoFuncao = str_pad($oDadosFuncao->o52_funcao, 2, "0", STR_PAD_LEFT);
                        $this->addElement($oDadosFuncaoA06, $oNodeFuncaoA06);
            
                        /*
                         * Buscamos dados das SubFuncoes
                         */
                        $oNodeSubFuncoesA06 = $this->oDocumento->createElement("subfuncoesA06");
                        $sSqlSubFuncoes = "select distinct 
                                      o53_subfuncao 
                                 from orcsubfuncao
                                      inner join orcdotacao on o53_subfuncao = o58_subfuncao
                                where o58_funcao  = $oDadosFuncao->o52_funcao
                                  and o58_anousu  = {$this->iAno}
                                  and o58_orgao   = {$oDadosOrgao->o40_orgao}
                                  and o58_unidade = {$oDadosUnidade->o41_unidade}";
                        $rsSubFuncoes = db_query($sSqlSubFuncoes);
                        $iLinhasSubFuncoes = pg_num_rows($rsSubFuncoes);
            
                        $aSubFuncoes = array();

                        for ($iIndSubFuncao = 0; $iIndSubFuncao < $iLinhasSubFuncoes; $iIndSubFuncao++) {
                            $oDadosSubFuncao = db_utils::fieldsMemory($rsSubFuncoes, $iIndSubFuncao);
                            if (in_array($oDadosSubFuncao->o53_subfuncao, $aSubFuncoes)) {
                                continue;
                            }
                            $aSubFuncoes[] = $oDadosSubFuncao->o53_subfuncao;
             
                            $oNodeSubFuncaoA06 = $this->oDocumento->createElement("subfuncaoA06");
              
                            $oDadosSubFuncaoA06 = new stdClass();
                            $oDadosSubFuncaoA06->codigoSubfuncao = str_pad($oDadosSubFuncao->o53_subfuncao, 3, "0", STR_PAD_LEFT);
                            $this->addElement($oDadosSubFuncaoA06, $oNodeSubFuncaoA06);
              
                      /*
                       * Buscamos dados dos programas
                       */
                            $oNodeProgramasA06 = $this->oDocumento->createElement("programasA06");
                            $sSqlProgramas = "select distinct 
                                       o54_programa, 
                                       o54_descr 
                                  from orcprograma 
                                       inner join orcdotacao on o58_programa = o54_programa
                                                            and o58_anousu   = o54_anousu
                                 where o58_anousu    = {$this->iAno}
                                   and o58_funcao    = {$oDadosFuncao->o52_funcao}
                                   and o58_subfuncao = {$oDadosSubFuncao->o53_subfuncao}
                                   and o58_orgao     = {$oDadosOrgao->o40_orgao}
                                   and o58_unidade   = {$oDadosUnidade->o41_unidade}";
                            $rsProgramas = db_query($sSqlProgramas);
                            $iLinhasProgramas = pg_num_rows($rsProgramas);
              
                            $aProgramas = array();

                            for ($iIndPrograma = 0; $iIndPrograma < $iLinhasProgramas; $iIndPrograma++) {
                                $oDadosPrograma = db_utils::fieldsMemory($rsProgramas, $iIndPrograma);
                                if (in_array($oDadosPrograma->o54_programa, $aProgramas)) {
                                    continue;
                                }
                                $aProgramas[] = $oDadosPrograma->o54_programa;
                
                                $oNodeProgramaA06 = $this->oDocumento->createElement("programaA06");
                
                                $oDadosProgramaA06 = new stdClass();
                                $oDadosProgramaA06->codigoPrograma = str_pad($oDadosPrograma->o54_programa, 4, "0", STR_PAD_LEFT);
                                $oDadosProgramaA06->nomePrograma   = db_removeAcentuacao($oDadosPrograma->o54_descr);
                                $this->addElement($oDadosProgramaA06, $oNodeProgramaA06);
                
                          /*
                           * Buscamos dados das acoes
                           */
                                $oNodeAcoesA06 = $this->oDocumento->createElement("acoesA06");
                                $sSqlAcoes = "select o55_projativ, 
                                     o55_descr  
                                from orcprojativ 
                                     inner join orcdotacao on o58_projativ = o55_projativ
                                                          and o58_anousu   = o55_anousu
                               where o58_anousu    = {$this->iAno}
                                 and o58_funcao    = {$oDadosFuncao->o52_funcao}
                                 and o58_subfuncao = {$oDadosSubFuncao->o53_subfuncao}
                                 and o58_programa  = {$oDadosPrograma->o54_programa}
                                 and o58_orgao     = {$oDadosOrgao->o40_orgao}
                                 and o58_unidade   = {$oDadosUnidade->o41_unidade}
                               group by o55_projativ, 
                                        o55_descr";
                                $rsAcoes = db_query($sSqlAcoes);
                                $iLinhasAcoes = pg_num_rows($rsAcoes);
                
                                $aAcoes = array();
                
                                for ($iIndAcao = 0; $iIndAcao < $iLinhasAcoes; $iIndAcao++) {
                                    $oDadosAcao = db_utils::fieldsMemory($rsAcoes, $iIndAcao);
                                    if (in_array($oDadosAcao->o55_projativ, $aAcoes)) {
                                        continue;
                                    }
                                    $aAcoes[] = $oDadosAcao->o55_projativ;
                  
                                    $oNodeAcaoA06 = $this->oDocumento->createElement("acaoA06");
                  
                                    $oDadosAcaoA06 = new stdClass();
                                    $oDadosAcaoA06->codigoAcao = $oDadosAcao->o55_projativ;
                                    $oDadosAcaoA06->nomeAcao   = db_removeAcentuacao($oDadosAcao->o55_descr);
                  
                                    $sWhereDotacao  = "o58_orgao     = {$oDadosOrgao->o40_orgao}         and ";
                                    $sWhereDotacao .= "o58_unidade   = {$oDadosUnidade->o41_unidade}     and ";
                                    $sWhereDotacao .= "o58_funcao    = {$oDadosFuncao->o52_funcao}       and ";
                                    $sWhereDotacao .= "o58_subfuncao = {$oDadosSubFuncao->o53_subfuncao} and ";
                                    $sWhereDotacao .= "o58_programa  = {$oDadosPrograma->o54_programa}   and ";
                                    $sWhereDotacao .= "o58_projativ  = {$oDadosAcao->o55_projativ}";
                                    $rsDadosDespesa = db_dotacaosaldo(6, 3, 4, true, $sWhereDotacao, $this->iAno, $this->dtDataInicial, $this->dtDataFinal);
                                    $oDespesa = db_utils::fieldsMemory($rsDadosDespesa, 0);
                              //$nValor = $oDespesa->dot_ini + $oDespesa->suplementado - $oDespesa->reduzido;
                                    $nValor = $oDespesa->empenhado - $oDespesa->anulado;
                  
                                    if (substr($oDadosAcao->o55_projativ, 0, 1) == "1") {
                                        $oDadosAcaoA06->valorProjeto   = number_format($nValor, 2, '.', '');
                                        $oDadosAcaoA06->valorAtividade = 0.00;
                                    } else {
                                        $oDadosAcaoA06->valorProjeto = 0.00;
                                        $oDadosAcaoA06->valorAtividade = number_format($nValor, 2, '.', '');
                                    }
                                    $this->addElement($oDadosAcaoA06, $oNodeAcaoA06);
                  
                                    $oNodeAcoesA06->appendChild($oNodeAcaoA06);
                                }
                
                                $oNodeProgramaA06->appendChild($oNodeAcoesA06);
                                $oNodeProgramasA06->appendChild($oNodeProgramaA06);
                            }
              
                            $oNodeSubFuncaoA06->appendChild($oNodeProgramasA06);
                            $oNodeSubFuncoesA06->appendChild($oNodeSubFuncaoA06);
                        }
            
                        $oNodeFuncaoA06->appendChild($oNodeSubFuncoesA06);
                        $oNodeFuncoesA06->appendChild($oNodeFuncaoA06);
                    }
          
                    $oNodeUnidadeA06->appendChild($oNodeFuncoesA06);
                    $oNodeUnidadesA06->appendChild($oNodeUnidadeA06);
                }
        
                $oNodeOrgaoA06->appendChild($oNodeUnidadesA06);
                $oNodeOrgaosA06->appendChild($oNodeOrgaoA06);
            }
      
            $oNodeAnexoDados06->appendChild($oNodeOrgaosA06);
            $oNodeAnexo06->appendChild($oNodeAnexoDados06);
            $oNodeRemessa->appendChild($oNodeAnexo06);
        }
    
    

        if ($this->getGeraAnexo07()) {
            $oNodeAnexo07      = $this->oDocumento->createElement("anexo07");
            $oNodeAnexoDados07 = $this->oDocumento->createElement("anexoDados07");
            $oNodeFuncoesA07   = $this->oDocumento->createElement("funcoesA07");
      
          /*
           * Buscamos os dados das funcoes
           */
            $sSqlFuncoes = "select distinct 
                             o52_funcao 
                        from orcfuncao 
                             inner join orcdotacao on o52_funcao = o58_funcao
		       where o58_anousu = {$this->iAno}
                       order by o52_funcao";
            $rsFuncoes = db_query($sSqlFuncoes);
            $iLinhasFuncoes = pg_num_rows($rsFuncoes);
            for ($iIndFuncao = 0; $iIndFuncao < $iLinhasFuncoes; $iIndFuncao++) {
                $oDadosFuncao = db_utils::fieldsMemory($rsFuncoes, $iIndFuncao);

                if ($oDadosFuncao->o52_funcao == "99") {
                    continue;
                }

                $oNodeFuncaoA07 = $this->oDocumento->createElement("funcaoA07");
        
                $oDadosFuncaoA07 = new stdClass();
                $oDadosFuncaoA07->codigoFuncao = str_pad($oDadosFuncao->o52_funcao, 2, "0", STR_PAD_LEFT);
                $this->addElement($oDadosFuncaoA07, $oNodeFuncaoA07);
        
              /*
               * Buscamos os dados das subfuncoes
               */
                $oNodeSubFuncoesA07 = $this->oDocumento->createElement("subfuncoesA07");
                $sSqlSubFuncoes = "select distinct 
                                  o53_subfuncao 
                             from orcsubfuncao
                                  inner join orcdotacao on o53_subfuncao = o58_subfuncao
                            where o58_anousu = {$this->iAno}
			      and o58_funcao = {$oDadosFuncao->o52_funcao}
                            order by o53_subfuncao";
                $rsSubFuncoes = db_query($sSqlSubFuncoes);
                $iLinhasSubFuncoes = pg_num_rows($rsSubFuncoes);
        
                for ($iIndSubFuncao = 0; $iIndSubFuncao < $iLinhasSubFuncoes; $iIndSubFuncao++) {
                    $oDadosSubFuncao = db_utils::fieldsMemory($rsSubFuncoes, $iIndSubFuncao);
          
                    $oNodeSubFuncaoA07 = $this->oDocumento->createElement("subfuncaoA07");
          
                    $oDadosSubFuncaoA07 =  new stdClass();
                    $oDadosSubFuncaoA07->codigoSubfuncao = str_pad($oDadosSubFuncao->o53_subfuncao, 3, "0", STR_PAD_LEFT);
                    $this->addElement($oDadosSubFuncaoA07, $oNodeSubFuncaoA07);
          
                  /*
                   * Buscaos os dados dos programas
                   */
                    $oNodeProgramasA07 = $this->oDocumento->createElement("programasA07");
                    $sSqlProgramas = "select distinct 
                                   o54_programa, 
                                   o54_descr 
                              from orcprograma 
                                   inner join orcdotacao on o58_programa = o54_programa
                                                        and o58_anousu = o54_anousu
                             where o58_anousu = {$this->iAno}
                               and o58_funcao = {$oDadosFuncao->o52_funcao}
                               and o58_subfuncao = {$oDadosSubFuncao->o53_subfuncao}
                             order by o54_programa";
                    $rsProgramas = db_query($sSqlProgramas);
                    $iLinhasProgramas = pg_num_rows($rsProgramas);
          
                    for ($iIndPrograma = 0; $iIndPrograma < $iLinhasProgramas; $iIndPrograma++) {
                        $oDadosPrograma = db_utils::fieldsMemory($rsProgramas, $iIndPrograma);

                        $oNodeProgramaA07 = $this->oDocumento->createElement("programaA07");
            
                        $oDadosProgramaA07 = new stdClass();
                        $oDadosProgramaA07->codigoPrograma = str_pad($oDadosPrograma->o54_programa, 4, "0", STR_PAD_LEFT);
                        $oDadosProgramaA07->nomePrograma   = db_removeAcentuacao($oDadosPrograma->o54_descr);
            
                        $sWhereDotacao  = "o58_funcao    = {$oDadosFuncao->o52_funcao}       and ";
                        $sWhereDotacao .= "o58_subfuncao = {$oDadosSubFuncao->o53_subfuncao} and ";
                        $sWhereDotacao .= "o58_programa  = {$oDadosPrograma->o54_programa} ";
            
                        /*
                         * Buscamos o valor do Projeto
                         */
                        $sWhereDotacaoProjeto = "{$sWhereDotacao} and substr(o58_projativ, 1, 1) <> '1' ";
                        $rsDadosDespesa = db_dotacaosaldo(3, 3, 4, true, $sWhereDotacaoProjeto, $this->iAno, $this->dtDataInicial, $this->dtDataFinal);
                        $oDespesa = db_utils::fieldsMemory($rsDadosDespesa, 0);
                        //$nValorProjeto = $oDespesa->dot_ini + $oDespesa->suplementado - $oDespesa->reduzido;
                        $nValorProjeto = $oDespesa->empenhado - $oDespesa->anulado;
            
                        /*
                         * Buscamos o valor da Atividade
                         */
                        $sWhereDotacaoAtividade = "{$sWhereDotacao} and substr(o58_projativ, 1, 1) = '1' ";
                        $rsDadosDespesa = db_dotacaosaldo(3, 3, 4, true, $sWhereDotacaoAtividade, $this->iAno, $this->dtDataInicial, $this->dtDataFinal);
                        $oDespesa = db_utils::fieldsMemory($rsDadosDespesa, 0);
                        //$nValorAtividade = $oDespesa->dot_ini + $oDespesa->suplementado - $oDespesa->reduzido;
                        $nValorAtividade = $oDespesa->empenhado - $oDespesa->anulado;
            
                        //setamos os valores
                        $oDadosProgramaA07->valorProjeto = number_format($nValorProjeto, 2, '.', '');
                        $oDadosProgramaA07->valorAtividade = number_format($nValorAtividade, 2, '.', '');
                        
                        $this->addElement($oDadosProgramaA07, $oNodeProgramaA07);
                        $oNodeProgramasA07->appendChild($oNodeProgramaA07);
                    }
                    $oNodeSubFuncaoA07->appendChild($oNodeProgramasA07);
                    $oNodeSubFuncoesA07->appendChild($oNodeSubFuncaoA07);
                }
                $oNodeFuncaoA07->appendChild($oNodeSubFuncoesA07);
                $oNodeFuncoesA07->appendChild($oNodeFuncaoA07);
            }
      
            $oNodeAnexoDados07->appendChild($oNodeFuncoesA07);
            $oNodeAnexo07->appendChild($oNodeAnexoDados07);
            $oNodeRemessa->appendChild($oNodeAnexo07);
        }
    
    

        if ($this->getGeraAnexo08()) {
            $oNodeAnexo08 = $this->oDocumento->createElement("anexo08");
            $oNodeAnexoDados08 = $this->oDocumento->createElement("anexoDados08");
            $oNodeFuncoesA08 = $this->oDocumento->createElement("funcoesA08");

            $sSqlFuncoes = "select distinct o52_funcao 
                        from orcfuncao 
                             inner join orcdotacao on o52_funcao = o58_funcao
                       where o58_anousu  = {$this->iAno}";
            $rsFuncoes = db_query($sSqlFuncoes);
            $iLinhasFuncoes = pg_num_rows($rsFuncoes);
            for ($iIndFuncao = 0; $iIndFuncao < $iLinhasFuncoes; $iIndFuncao++) {
                $oDadosFuncao = db_utils::fieldsMemory($rsFuncoes, $iIndFuncao);

                if ($oDadosFuncao->o52_funcao == "99") {
                    continue;
                }

                $oNodeFuncaoA08 = $this->oDocumento->createElement("funcaoA08");
        
                $oDadosFuncaoA08 = new stdClass();
                $oDadosFuncaoA08->codigoFuncao = str_pad($oDadosFuncao->o52_funcao, 2, "0", STR_PAD_LEFT);
                $this->addElement($oDadosFuncaoA08, $oNodeFuncaoA08);
        
                $oNodeSubFuncoesA08 = $this->oDocumento->createElement("subfuncoesA08");
        
                $sSqlSubFuncoes = "select distinct o53_subfuncao 
                             from orcsubfuncao
                                  inner join orcdotacao on o53_subfuncao = o58_subfuncao
                            where o58_anousu = {$this->iAno}
                              and o58_funcao = {$oDadosFuncao->o52_funcao}";
                $rsSubFuncoes = db_query($sSqlSubFuncoes);
                $iLinhasSubFuncoes = pg_num_rows($rsSubFuncoes);
        
                for ($iIndSubFuncao = 0; $iIndSubFuncao < $iLinhasSubFuncoes; $iIndSubFuncao++) {
                    $oDadosSubFuncao = db_utils::fieldsMemory($rsSubFuncoes, $iIndSubFuncao);
          
                    $oNodeSubFuncaoA08 = $this->oDocumento->createElement("subfuncaoA08");
          
                    $oDadosSubFuncaoA08 = new stdClass();
                    $oDadosSubFuncaoA08->codigoSubfuncao = str_pad($oDadosSubFuncao->o53_subfuncao, 3, "0", STR_PAD_LEFT);
                    $this->addElement($oDadosSubFuncaoA08, $oNodeSubFuncaoA08);
          
                  /*
                   * Buscamos os dados dos programas
                   */
                    $oNodeProgramasA08 = $this->oDocumento->createElement("programasA08");
                    $sSqlProgramas = "select o54_programa, 
                                   o54_descr 
                              from orcprograma 
                                   inner join orcdotacao on o58_programa = o54_programa
                                                        and o58_anousu = o54_anousu
                             where o58_anousu    = {$this->iAno}
                               and o58_funcao    = {$oDadosFuncao->o52_funcao}
                               and o58_subfuncao = {$oDadosSubFuncao->o53_subfuncao}
                             group by o54_programa, o54_descr";

                    $rsProgramas = db_query($sSqlProgramas);
                    $iLinhasProgramas = pg_num_rows($rsProgramas);

                    for ($iIndPrograma = 0; $iIndPrograma < $iLinhasProgramas; $iIndPrograma++) {
                        $oDadosPrograma = db_utils::fieldsMemory($rsProgramas, $iIndPrograma);

                        $oNodeProgramaA08 = $this->oDocumento->createElement("programaA08");
            
                        $oDadosProgramaA08 = new stdClass();
                        $oDadosProgramaA08->codigoPrograma = str_pad($oDadosPrograma->o54_programa, 4, "0", STR_PAD_LEFT);
                        $oDadosProgramaA08->nomePrograma = db_removeAcentuacao($oDadosPrograma->o54_descr);
            
                        $sWhereDotacao  = "o58_funcao    = {$oDadosFuncao->o52_funcao}       and ";
                        $sWhereDotacao .= "o58_subfuncao = {$oDadosSubFuncao->o53_subfuncao} and ";
                        $sWhereDotacao .= "o58_programa  = {$oDadosPrograma->o54_programa} ";
            
                        $sWhereDotacaoRecurso1  = "{$sWhereDotacao} and o58_codigo    = 0100000 ";
                        $rsDadosDespesa1 = db_dotacaosaldo(5, 3, 4, true, $sWhereDotacaoRecurso1, $this->iAno, $this->dtDataInicial, $this->dtDataFinal);
                        $oDespesa1 = db_utils::fieldsMemory($rsDadosDespesa1, 0);
                        $nValor1 = $oDespesa1->empenhado - $oDespesa1->anulado;

                        $oDadosProgramaA08->valorOrdinario = number_format($nValor1, 2, '.', '');
                        //$oDadosProgramaA08->valorVinculado = 0.00;


                        $sWhereDotacaoRecurso2  = "{$sWhereDotacao} and o58_codigo    <> 0100000 ";
                        $rsDadosDespesa2 = db_dotacaosaldo(5, 3, 4, true, $sWhereDotacaoRecurso2, $this->iAno, $this->dtDataInicial, $this->dtDataFinal);
                        $oDespesa2 = db_utils::fieldsMemory($rsDadosDespesa2, 0);
                        $nValor2 = $oDespesa2->empenhado - $oDespesa2->anulado;

                        //$oDadosProgramaA08->valorOrdinario = 0.00;
                        $oDadosProgramaA08->valorVinculado = number_format($nValor2, 2, '.', '');

                        $this->addElement($oDadosProgramaA08, $oNodeProgramaA08);
                        $oNodeProgramasA08->appendChild($oNodeProgramaA08);
                    }
                    $oNodeSubFuncaoA08->appendChild($oNodeProgramasA08);
                    $oNodeSubFuncoesA08->appendChild($oNodeSubFuncaoA08);
                }
                $oNodeFuncaoA08->appendChild($oNodeSubFuncoesA08);
                $oNodeFuncoesA08->appendChild($oNodeFuncaoA08);
            }

            $oNodeAnexoDados08->appendChild($oNodeFuncoesA08);
            $oNodeAnexo08->appendChild($oNodeAnexoDados08);
            $oNodeRemessa->appendChild($oNodeAnexo08);
        }

    
    
        if ($this->getGeraAnexo09()) {
            $oNodeAnexo09      = $this->oDocumento->createElement("anexo09");
            $oNodeAnexoDados09 = $this->oDocumento->createElement("anexoDados09");
            $oNodeOrgaosA09    = $this->oDocumento->createElement("orgaosA09");
      
            $sSqlOrgaos = "select distinct 
                            o40_orgao, 
                            o40_descr 
                       from orcorgao 
                            inner join orcdotacao on o58_orgao   = o40_orgao
                                                 and o58_anousu  = o40_anousu
                                               where o58_anousu = {$this->iAno}
		     order by o40_orgao";
            $rsOrgaos = db_query($sSqlOrgaos);
            $iLinhasOrgaos = pg_num_rows($rsOrgaos);
      
            for ($iIndOrgao = 0; $iIndOrgao < $iLinhasOrgaos; $iIndOrgao++) {
                $oDadosOrgao = db_utils::fieldsMemory($rsOrgaos, $iIndOrgao);
        
                $oNodeOrgaoA09 = $this->oDocumento->createElement("orgaoA09");
        
                $oDadosOrgaoA09 = new stdClass();
                $oDadosOrgaoA09->codigoOrgao = $oDadosOrgao->o40_orgao;
                $oDadosOrgaoA09->nomeOrgao   = db_removeAcentuacao($oDadosOrgao->o40_descr);
                $this->addElement($oDadosOrgaoA09, $oNodeOrgaoA09);
        
              /*
               * Buscamos os dados das Unidades
               */
                $oNodeUnidadesA09 = $this->oDocumento->createElement("unidadesA09");
                $sSqlUnidades = "select distinct 
                                o41_unidade, 
                                o41_descr, 
                                o41_instit 
                           from orcunidade
                                inner join orcdotacao on o58_unidade = o41_unidade
                                                     and o58_orgao   = o41_orgao
                                                     and o58_anousu  = o41_anousu
                                                     and o58_instit  = o41_instit
                          where o58_anousu = {$this->iAno}
                            and o58_orgao  = {$oDadosOrgao->o40_orgao}
			order by o41_unidade";
                $rsUnidades = db_query($sSqlUnidades);
                $iLinhasUnidades = pg_num_rows($rsUnidades);
        
                for ($iIndUnidade = 0; $iIndUnidade < $iLinhasUnidades; $iIndUnidade++) {
                    $oDadosUnidade = db_utils::fieldsMemory($rsUnidades, $iIndUnidade);

                    $oNodeUnidadeA09 = $this->oDocumento->createElement("unidadeA09");

                    /*
                     * Objeto com as colunas que irao compor o Node UnidadeA09
                     */
                    $oDadosUnidadeA09 = new stdClass();
                    $oDadosUnidadeA09->codigoUnidadeGestora         = $oDadosUnidade->o41_unidade;
                    $oDadosUnidadeA09->nomeUnidadeGestora           = db_removeAcentuacao($oDadosUnidade->o41_descr);
                    $oDadosUnidadeA09->valorFuncaoLegislativa       = 0;
                    $oDadosUnidadeA09->valorFuncaoAdministracao     = 0;
                    $oDadosUnidadeA09->valorFuncaoSegurancaPublica  = 0;
                    $oDadosUnidadeA09->valorFuncaoAssistenciaSocial = 0;
                    $oDadosUnidadeA09->valorFuncaoPrevidenciaSocial = 0;
                    $oDadosUnidadeA09->valorFuncaoSaude             = 0;
                    $oDadosUnidadeA09->valorFuncaoTrabalho          = 0;
                    $oDadosUnidadeA09->valorFuncaoEducacao          = 0;
                    $oDadosUnidadeA09->valorFuncaoCultura           = 0;
                    $oDadosUnidadeA09->valorFuncaoDireitosCidadania = 0;
                    $oDadosUnidadeA09->valorFuncaoUrbanismo         = 0;
                    $oDadosUnidadeA09->valorFuncaoHabitacao         = 0;
                    $oDadosUnidadeA09->valorFuncaoSaneamento        = 0;
                    $oDadosUnidadeA09->valorFuncaoGestaoAmbiental   = 0;
                    $oDadosUnidadeA09->valorFuncaoCienciaTecnologia = 0;
                    $oDadosUnidadeA09->valorFuncaoAgricultura       = 0;
                    $oDadosUnidadeA09->valorFuncaoIndustria         = 0;
                    $oDadosUnidadeA09->valorFuncaoComercioServicos  = 0;
                    $oDadosUnidadeA09->valorFuncaoComunicacoes      = 0;
                    $oDadosUnidadeA09->valorFuncaoEnergia           = 0;
                    $oDadosUnidadeA09->valorFuncaoTransporte        = 0;
                    $oDadosUnidadeA09->valorFuncaoDesportoLazer     = 0;
                    $oDadosUnidadeA09->valorFuncaoEncargosEspeciais = 0;
                        
                    $sSqlFuncoes = "select o52_funcao
                              from orcfuncao 
                             where o52_funcao in (1,4,6,8,9,10,11,12,13,14,15,16,17,18,19,20,22,23,24,25,26,27,28)
				";
                    $rsFuncoes = db_query($sSqlFuncoes);
                    $iLinhasFuncoes = pg_num_rows($rsFuncoes);
                    for ($iIndFuncao = 0; $iIndFuncao < $iLinhasFuncoes; $iIndFuncao++) {
                        $oDadosFuncao = db_utils::fieldsMemory($rsFuncoes, $iIndFuncao);

                        $sWhereDotacao  = "o58_orgao     = {$oDadosOrgao->o40_orgao}         and ";
                        $sWhereDotacao .= "o58_unidade   = {$oDadosUnidade->o41_unidade}     and ";
                        $sWhereDotacao .= "o58_funcao    = {$oDadosFuncao->o52_funcao}       and ";
                        $sWhereDotacao .= "o58_instit    = {$oDadosUnidade->o41_instit}        ";
                        $rsDadosDespesa = db_dotacaosaldo(3, 3, 4, true, $sWhereDotacao, $this->iAno, $this->dtDataInicial, $this->dtDataFinal);
                        $oDespesa = db_utils::fieldsMemory($rsDadosDespesa, 0);
                    //$nValor = $oDespesa->dot_ini + $oDespesa->suplementado - $oDespesa->reduzido;
                        $nValor = $oDespesa->empenhado - $oDespesa->anulado;

              
                        if ($oDadosFuncao->o52_funcao == 1) {
                              $oDadosUnidadeA09->valorFuncaoLegislativa = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 4) {
                            $oDadosUnidadeA09->valorFuncaoAdministracao = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 6) {
                            $oDadosUnidadeA09->valorFuncaoSegurancaPublica = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 8) {
                            $oDadosUnidadeA09->valorFuncaoAssistenciaSocial = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 9) {
                            $oDadosUnidadeA09->valorFuncaoPrevidenciaSocial = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 10) {
                            $oDadosUnidadeA09->valorFuncaoSaude = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 11) {
                            $oDadosUnidadeA09->valorFuncaoTrabalho = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 12) {
                            $oDadosUnidadeA09->valorFuncaoEducacao = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 13) {
                            $oDadosUnidadeA09->valorFuncaoCultura = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 14) {
                            $oDadosUnidadeA09->valorFuncaoDireitosCidadania = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 15) {
                            $oDadosUnidadeA09->valorFuncaoUrbanismo = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 16) {
                            $oDadosUnidadeA09->valorFuncaoHabitacao = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 17) {
                            $oDadosUnidadeA09->valorFuncaoSaneamento = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 18) {
                            $oDadosUnidadeA09->valorFuncaoGestaoAmbiental = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 19) {
                            $oDadosUnidadeA09->valorFuncaoCienciaTecnologia = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 20) {
                            $oDadosUnidadeA09->valorFuncaoAgricultura = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 22) {
                            $oDadosUnidadeA09->valorFuncaoIndustria = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 23) {
                            $oDadosUnidadeA09->valorFuncaoComercioServicos = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 24) {
                            $oDadosUnidadeA09->valorFuncaoComunicacoes = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 25) {
                            $oDadosUnidadeA09->valorFuncaoEnergia = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 26) {
                            $oDadosUnidadeA09->valorFuncaoTransporte = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 27) {
                            $oDadosUnidadeA09->valorFuncaoDesportoLazer = number_format($nValor, 2, '.', '');
                        }
                        if ($oDadosFuncao->o52_funcao == 28) {
                            $oDadosUnidadeA09->valorFuncaoEncargosEspeciais = number_format($nValor, 2, '.', '');
                        }
                    }
                    $this->addElement($oDadosUnidadeA09, $oNodeUnidadeA09);
                    $oNodeUnidadesA09->appendChild($oNodeUnidadeA09);
                }
                $oNodeOrgaoA09->appendChild($oNodeUnidadesA09);
                $oNodeOrgaosA09->appendChild($oNodeOrgaoA09);
            }
            $oNodeAnexoDados09->appendChild($oNodeOrgaosA09);
            $oNodeAnexo09->appendChild($oNodeAnexoDados09);
            $oNodeRemessa->appendChild($oNodeAnexo09);
        }

    
    
        if ($this->getGeraAnexo10()) {
            $oNodeAnexo10      = $this->oDocumento->createElement("anexo10");
            $oNodeAnexoDados10 = $this->oDocumento->createElement("anexoDados10");
            $oNodeReceitasA10  = $this->oDocumento->createElement("receitasA10");
      
            $rsReceitas = db_receitasaldo(11, 1, 3, true, "", $this->iAno, $this->dtDataInicial, $this->dtDataFinal, false);
            $iLinhasReceitas = pg_numrows($rsReceitas);
      
            for ($iInd = 0; $iInd < $iLinhasReceitas; $iInd++) {
                $oReceita = db_utils::fieldsMemory($rsReceitas, $iInd);
        
                $oNodeReceitaA10 = $this->oDocumento->createElement("receitaA10");
        
                $oDadosReceitaA10 = new stdClass();
                $oDadosReceitaA10->codigoReceita   = substr($oReceita->o57_fonte, 0, 10);
                $oDadosReceitaA10->valorOrcado     = number_format(abs($oReceita->saldo_inicial), 2, '.', '');
                $oDadosReceitaA10->valorArrecadado = number_format(abs($oReceita->saldo_arrecadado), 2, '.', '');
                $this->addElement($oDadosReceitaA10, $oNodeReceitaA10);
        
                $oNodeReceitasA10->appendChild($oNodeReceitaA10);
            }
            $oNodeAnexoDados10->appendChild($oNodeReceitasA10);
            $oNodeAnexo10->appendChild($oNodeAnexoDados10);
            $oNodeRemessa->appendChild($oNodeAnexo10);
        }

    
    
        if ($this->getGeraAnexo11()) {
            $oNodeAnexo11             = $this->oDocumento->createElement("anexo11");
            $oNodeAnexoDados11        = $this->oDocumento->createElement("anexoDados11");
            $oNodeUnidadesDespesasA11 = $this->oDocumento->createElement("unidadesDespesasA11");
      
          /*
           * Buscamos os dados do Orgao/Unidade
           */
            $sqlUnidade = "select distinct
                            o41_orgao as orgao, 
                            o41_unidade as unidade, 
                            o41_descr as descr_unidade 
                       from orcunidade
                            inner join orcdotacao on o41_orgao   = o58_orgao
                                                 and o41_unidade = o58_unidade
                                                 and o41_anousu  = o58_anousu 
                      where o41_anousu = {$this->iAno}
                      order by o41_orgao, 
                               o41_unidade";
            $rsUnidade = db_query($sqlUnidade);
            $iLinhasUnidades = pg_numrows($rsUnidade);
            for ($iIndUnidade = 0; $iIndUnidade < $iLinhasUnidades; $iIndUnidade++) {
                $oDadosUnidade = db_utils::fieldsMemory($rsUnidade, $iIndUnidade);
         
                if ($oDadosUnidade->orgao == "99") {
                    continue;
                }
         
                $oNodeUnidadeDespesasA11 = $this->oDocumento->createElement("unidadeDespesasA11");
         
                $oDadosUnidadeDespesasA11 = new stdClass();
                $oDadosUnidadeDespesasA11->codigoUnidadeOrcamentaria = str_pad($oDadosUnidade->orgao, 2, '0', STR_PAD_LEFT).".".str_pad($oDadosUnidade->unidade, 2, '0', STR_PAD_LEFT);
                $oDadosUnidadeDespesasA11->nomeUnidadeOrcamentaria   = db_removeAcentuacao($oDadosUnidade->descr_unidade);
                $this->addElement($oDadosUnidadeDespesasA11, $oNodeUnidadeDespesasA11);
         
               /*
              * Buscamos os dados das despesas
              */
                $oNodeDespesasA11 = $this->oDocumento->createElement("despesasA11");
                $sSqlFuncaoProgramatica = "select distinct 
                                           o58_funcao    as funcao, 
                                           o58_subfuncao as subfuncao, 
                                           o58_programa  as programa, 
                                           o58_projativ  as acao, 
                                           o55_descr     as descr_acao 
                                      from orcdotacao 
                                           inner join orcprojativ on o58_projativ = o55_projativ 
                                                                 and o58_anousu = o55_anousu 
                                     where o58_anousu  = {$this->iAno} 
                                       and o58_orgao   = {$oDadosUnidade->orgao} 
                                       and o58_unidade = {$oDadosUnidade->unidade}
                                     order by o58_subfuncao,o58_programa,o58_projativ,o55_descr";
                $rsFuncaoProgramatica = db_query($sSqlFuncaoProgramatica);
                $iLinhasFuncaoProgramatica = pg_numrows($rsFuncaoProgramatica);
         
                for ($iIndFuncaoProgramatica = 0; $iIndFuncaoProgramatica < $iLinhasFuncaoProgramatica; $iIndFuncaoProgramatica++) {
                    $oDotacao = db_utils::fieldsMemory($rsFuncaoProgramatica, $iIndFuncaoProgramatica);
            
                    $sWhereDotacao = "o58_orgao     = {$oDadosUnidade->orgao}   and
                              o58_unidade   = {$oDadosUnidade->unidade} and
                              o58_funcao    = {$oDotacao->funcao}       and
                              o58_subfuncao = {$oDotacao->subfuncao}    and
                              o58_programa  = {$oDotacao->programa}     and
                              o58_projativ  = {$oDotacao->acao}";

                    $rsDadosDespesa = db_dotacaosaldo(8, 1, 2, true, $sWhereDotacao, $this->iAno, $this->dtDataInicial, $this->dtDataFinal);
                    $iLinhasDespesa = pg_numrows($rsDadosDespesa);
                    for ($iIndDespesa = 0; $iIndDespesa < $iLinhasDespesa; $iIndDespesa++) {
                        $oDespesa = db_utils::fieldsMemory($rsDadosDespesa, $iIndDespesa);

                        $nVlrTotalSuplementacao = ($oDespesa->dot_ini + $oDespesa->suplementado_acumulado - $oDespesa->reduzido_acumulado);
                        $nVlrTotalEspecial      = $oDespesa->especial_acumulado;
                        $nVlrTotalRealizado     = $oDespesa->empenhado - $oDespesa->anulado;

                        if ($oDespesa->o58_codigo != 0 && ($nVlrTotalSuplementacao + $nVlrTotalEspecial + $nVlrTotalRealizado) > 0) {
                            $oNodeDespesaA11 = $this->oDocumento->createElement("despesaA11");
                  
                            $oDadosDespesaA11 = new stdClass();
                            $oDadosDespesaA11->codigoDespesa                           = substr($oDespesa->o58_elemento, 1, 8);
                            $oDadosDespesaA11->valorCreditosOrcamentariosSuplementares = $nVlrTotalSuplementacao;
                            $oDadosDespesaA11->valorCreditosEspeciaisExtraordinarios   = $nVlrTotalEspecial;
                            $oDadosDespesaA11->valorRealizada                          = $nVlrTotalRealizado;
               
                            $this->addElement($oDadosDespesaA11, $oNodeDespesaA11);
                            $oNodeDespesasA11->appendChild($oNodeDespesaA11);
                        }
                    }
                    $oNodeUnidadeDespesasA11->appendChild($oNodeDespesasA11);
                }
                $oNodeUnidadesDespesasA11->appendChild($oNodeUnidadeDespesasA11);
            }
            $oNodeAnexoDados11->appendChild($oNodeUnidadesDespesasA11);
            $oNodeAnexo11->appendChild($oNodeAnexoDados11);
            $oNodeRemessa->appendChild($oNodeAnexo11);
        }

    
    
        if ($this->getGeraAnexo12()) {
            $oNodeAnexo12               = $this->oDocumento->createElement("anexo12");
            $oNodeAnexoDados12          = $this->oDocumento->createElement("anexoDados12");
            $oNodeBlocoAReceitasA12     = $this->oDocumento->createElement("blocoAReceitasA12");
            $oNodeBlocoADespesasA12     = $this->oDocumento->createElement("blocoADespesasA12");
            $oNodeBlocoBA12             = $this->oDocumento->createElement("blocoBA12");
            $oNodeBlocoCA12             = $this->oDocumento->createElement("blocoCA12");
      
            $oDadosBlocoAReceitasA12 = new stdClass();
            $oDadosBlocoAReceitasA12->valorReceitaTributariaPI                  = "0";
            $oDadosBlocoAReceitasA12->valorReceitaTributariaPA                  = "0";
            $oDadosBlocoAReceitasA12->valorReceitaTributariaRR                  = "0";
            $oDadosBlocoAReceitasA12->valorReceitaContribuicaoPI                = "0";
            $oDadosBlocoAReceitasA12->valorReceitaContribuicaoPA                = "0";
            $oDadosBlocoAReceitasA12->valorReceitaContribuicaoRR                = "0";
            $oDadosBlocoAReceitasA12->valorReceitaPatrimonialPI                 = "0";
            $oDadosBlocoAReceitasA12->valorReceitaPatrimonialPA                 = "0";
            $oDadosBlocoAReceitasA12->valorReceitaPatrimonialRR                 = "0";
            $oDadosBlocoAReceitasA12->valorReceitaAgropecuariaPI                = "0";
            $oDadosBlocoAReceitasA12->valorReceitaAgropecuariaPA                = "0";
            $oDadosBlocoAReceitasA12->valorReceitaAgropecuariaRR                = "0";
            $oDadosBlocoAReceitasA12->valorReceitaIndustrialPI                  = "0";
            $oDadosBlocoAReceitasA12->valorReceitaIndustrialPA                  = "0";
            $oDadosBlocoAReceitasA12->valorReceitaIndustrialRR                  = "0";
            $oDadosBlocoAReceitasA12->valorReceitaServicosPI                    = "0";
            $oDadosBlocoAReceitasA12->valorReceitaServicosPA                    = "0";
            $oDadosBlocoAReceitasA12->valorReceitaServicosRR                    = "0";
            $oDadosBlocoAReceitasA12->valorTransfereciasCorrentesPI             = "0";
            $oDadosBlocoAReceitasA12->valorTransfereciasCorrentesPA             = "0";
            $oDadosBlocoAReceitasA12->valorTransfereciasCorrentesRR             = "0";
            $oDadosBlocoAReceitasA12->valorOutrasReceitasCorrentesPI            = "0";
            $oDadosBlocoAReceitasA12->valorOutrasReceitasCorrentesPA            = "0";
            $oDadosBlocoAReceitasA12->valorOutrasReceitasCorrentesRR            = "0";
            $oDadosBlocoAReceitasA12->valorReceitaOperacaoCreditoPI             = "0";
            $oDadosBlocoAReceitasA12->valorReceitaOperacaoCreditoPA             = "0";
            $oDadosBlocoAReceitasA12->valorReceitaOperacaoCreditoRR             = "0";
            $oDadosBlocoAReceitasA12->valorReceitaAlienacaoBensPI               = "0";
            $oDadosBlocoAReceitasA12->valorReceitaAlienacaoBensPA               = "0";
            $oDadosBlocoAReceitasA12->valorReceitaAlienacaoBensRR               = "0";
            $oDadosBlocoAReceitasA12->valorReceitaAmortizacoesEmprestimosPI     = "0";
            $oDadosBlocoAReceitasA12->valorReceitaAmortizacoesEmprestimosPA     = "0";
            $oDadosBlocoAReceitasA12->valorReceitaAmortizacoesEmprestimosRR     = "0";
            $oDadosBlocoAReceitasA12->valorTransfereciasCapitalPI               = "0";
            $oDadosBlocoAReceitasA12->valorTransfereciasCapitalPA               = "0";
            $oDadosBlocoAReceitasA12->valorTransfereciasCapitalRR               = "0";
            $oDadosBlocoAReceitasA12->valorOutrasReceitasCapitalPI              = "0";
            $oDadosBlocoAReceitasA12->valorOutrasReceitasCapitalPA              = "0";
            $oDadosBlocoAReceitasA12->valorOutrasReceitasCapitalRR              = "0";
            $oDadosBlocoAReceitasA12->valorArrecadadosExerciciosAnterioresPI    = "0";
            $oDadosBlocoAReceitasA12->valorArrecadadosExerciciosAnterioresPA    = "0";
            $oDadosBlocoAReceitasA12->valorArrecadadosExerciciosAnterioresRR    = "0";
            $oDadosBlocoAReceitasA12->valorOperacoesCreditoInternasMobiliariaPI = "0";
            $oDadosBlocoAReceitasA12->valorOperacoesCreditoInternasMobiliariaPA = "0";
            $oDadosBlocoAReceitasA12->valorOperacoesCreditoInternasMobiliariaRR = "0";
            $oDadosBlocoAReceitasA12->valorOperacoesCreditoInternasContratualPI = "0";
            $oDadosBlocoAReceitasA12->valorOperacoesCreditoInternasContratualPA = "0";
            $oDadosBlocoAReceitasA12->valorOperacoesCreditoInternasContratualRR = "0";
            $oDadosBlocoAReceitasA12->valorOperacoesCreditoExternasMobiliariaPI = "0";
            $oDadosBlocoAReceitasA12->valorOperacoesCreditoExternasMobiliariaPA = "0";
            $oDadosBlocoAReceitasA12->valorOperacoesCreditoExternasMobiliariaRR = "0";
            $oDadosBlocoAReceitasA12->valorOperacoesCreditoExternasContratualPI = "0";
            $oDadosBlocoAReceitasA12->valorOperacoesCreditoExternasContratualPA = "0";
            $oDadosBlocoAReceitasA12->valorOperacoesCreditoExternasContratualRR = "0";
            $oDadosBlocoAReceitasA12->valorSuperavitFinanceiroPA                = "0";
            $oDadosBlocoAReceitasA12->valorSuperavitFinanceiroRR                = "0";
            $oDadosBlocoAReceitasA12->valorreaberturaCreditosAdicionaisPA       = "0";
            $oDadosBlocoAReceitasA12->valorreaberturaCreditosAdicionaisRR       = "0";
            $this->addElement($oDadosBlocoAReceitasA12, $oNodeBlocoAReceitasA12);
      
            $oDadosBlocoADespesasA12 = new stdClass();
            $oDadosBlocoADespesasA12->valorPessoalEncargosSociaisDI                = "0";
            $oDadosBlocoADespesasA12->valorPessoalEncargosSociaisDA                = "0";
            $oDadosBlocoADespesasA12->valorPessoalEncargosSociaisDE                = "0";
            $oDadosBlocoADespesasA12->valorPessoalEncargosSociaisDL                = "0";
            $oDadosBlocoADespesasA12->valorPessoalEncargosSociaisDP                = "0";
            $oDadosBlocoADespesasA12->valorJurosEncargosDividaDI                   = "0";
            $oDadosBlocoADespesasA12->valorJurosEncargosDividaDA                   = "0";
            $oDadosBlocoADespesasA12->valorJurosEncargosDividaDE                   = "0";
            $oDadosBlocoADespesasA12->valorJurosEncargosDividaDL                   = "0";
            $oDadosBlocoADespesasA12->valorJurosEncargosDividaDP                   = "0";
            $oDadosBlocoADespesasA12->valorOutrasDespesasCorrentesDI               = "0";
            $oDadosBlocoADespesasA12->valorOutrasDespesasCorrentesDA               = "0";
            $oDadosBlocoADespesasA12->valorOutrasDespesasCorrentesDE               = "0";
            $oDadosBlocoADespesasA12->valorOutrasDespesasCorrentesDL               = "0";
            $oDadosBlocoADespesasA12->valorOutrasDespesasCorrentesDP               = "0";
            $oDadosBlocoADespesasA12->valorInvestimentosDI                         = "0";
            $oDadosBlocoADespesasA12->valorInvestimentosDA                         = "0";
            $oDadosBlocoADespesasA12->valorInvestimentosDE                         = "0";
            $oDadosBlocoADespesasA12->valorInvestimentosDL                         = "0";
            $oDadosBlocoADespesasA12->valorInvestimentosDP                         = "0";
            $oDadosBlocoADespesasA12->valorInversoesFinanceirasDI                  = "0";
            $oDadosBlocoADespesasA12->valorInversoesFinanceirasDA                  = "0";
            $oDadosBlocoADespesasA12->valorInversoesFinanceirasDE                  = "0";
            $oDadosBlocoADespesasA12->valorInversoesFinanceirasDL                  = "0";
            $oDadosBlocoADespesasA12->valorInversoesFinanceirasDP                  = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaDI                     = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaDA                     = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaDE                     = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaDL                     = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaDP                     = "0";
            $oDadosBlocoADespesasA12->valorReservaContingenciaDI                   = "0";
            $oDadosBlocoADespesasA12->valorReservaContingenciaDA                   = "0";
            $oDadosBlocoADespesasA12->valorReservaContingenciaDE                   = "0";
            $oDadosBlocoADespesasA12->valorReservaContingenciaDL                   = "0";
            $oDadosBlocoADespesasA12->valorReservaContingenciaDP                   = "0";
            $oDadosBlocoADespesasA12->valorReservaRPPSDI                           = "0";
            $oDadosBlocoADespesasA12->valorReservaRPPSDA                           = "0";
            $oDadosBlocoADespesasA12->valorReservaRPPSDE                           = "0";
            $oDadosBlocoADespesasA12->valorReservaRPPSDL                           = "0";
            $oDadosBlocoADespesasA12->valorReservaRPPSDP                           = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaInternaMobiliariaDI    = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaInternaMobiliariaDA    = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaInternaMobiliariaDE    = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaInternaMobiliariaDL    = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaInternaMobiliariaDP    = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaInternaOutrasDividasDI = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaInternaOutrasDividasDA = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaInternaOutrasDividasDE = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaInternaOutrasDividasDL = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaInternaOutrasDividasDP = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaExternaMobiliariaDI    = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaExternaMobiliariaDA    = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaExternaMobiliariaDE    = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaExternaMobiliariaDL    = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaExternaMobiliariaDP    = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaExternaOutrasDividasDI = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaExternaOutrasDividasDA = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaExternaOutrasDividasDE = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaExternaOutrasDividasDL = "0";
            $oDadosBlocoADespesasA12->valorAmortizacaoDividaExternaOutrasDividasDP = "0";
            $this->addElement($oDadosBlocoADespesasA12, $oNodeBlocoADespesasA12);
      
            $oDadosBlocoBA12 = new stdClass();
            $oDadosBlocoBA12->valorPessoalEncargosSociaisNPIEA   = "0";
            $oDadosBlocoBA12->valorPessoalEncargosSociaisNPIDEA  = "0";
            $oDadosBlocoBA12->valorPessoalEncargosSociaisNPL     = "0";
            $oDadosBlocoBA12->valorPessoalEncargosSociaisNPP     = "0";
            $oDadosBlocoBA12->valorPessoalEncargosSociaisNPC     = "0";
            $oDadosBlocoBA12->valorJurosEncargosDividaNPIEA      = "0";
            $oDadosBlocoBA12->valorJurosEncargosDividaNPIDEA     = "0";
            $oDadosBlocoBA12->valorJurosEncargosDividaNPL        = "0";
            $oDadosBlocoBA12->valorJurosEncargosDividaNPP        = "0";
            $oDadosBlocoBA12->valorJurosEncargosDividaNPC        = "0";
            $oDadosBlocoBA12->valorOutrasDespesasCorrentesNPIEA  = "0";
            $oDadosBlocoBA12->valorOutrasDespesasCorrentesNPIDEA = "0";
            $oDadosBlocoBA12->valorOutrasDespesasCorrentesNPL    = "0";
            $oDadosBlocoBA12->valorOutrasDespesasCorrentesNPP    = "0";
            $oDadosBlocoBA12->valorOutrasDespesasCorrentesNPC    = "0";
            $oDadosBlocoBA12->valorInvestimentosNPIEA            = "0";
            $oDadosBlocoBA12->valorInvestimentosNPIDEA           = "0";
            $oDadosBlocoBA12->valorInvestimentosNPL              = "0";
            $oDadosBlocoBA12->valorInvestimentosNPP              = "0";
            $oDadosBlocoBA12->valorInvestimentosNPC              = "0";
            $oDadosBlocoBA12->valorInversoesFinanceirasNPIEA     = "0";
            $oDadosBlocoBA12->valorInversoesFinanceirasNPIDEA    = "0";
            $oDadosBlocoBA12->valorInversoesFinanceirasNPL       = "0";
            $oDadosBlocoBA12->valorInversoesFinanceirasNPP       = "0";
            $oDadosBlocoBA12->valorInversoesFinanceirasNPC       = "0";
            $oDadosBlocoBA12->valorAmortizacaoDividaNPIEA        = "0";
            $oDadosBlocoBA12->valorAmortizacaoDividaNPIDEA       = "0";
            $oDadosBlocoBA12->valorAmortizacaoDividaNPL          = "0";
            $oDadosBlocoBA12->valorAmortizacaoDividaNPP          = "0";
            $oDadosBlocoBA12->valorAmortizacaoDividaNPC          = "0";
            $this->addElement($oDadosBlocoBA12, $oNodeBlocoBA12);
   
            $oDadosBlocoCA12 = new stdClass();
            $oDadosBlocoCA12->valorPessoalEncargosSociaisPIEA   = "0";
            $oDadosBlocoCA12->valorPessoalEncargosSociaisPIDEA  = "0";
            $oDadosBlocoCA12->valorPessoalEncargosSociaisPP     = "0";
            $oDadosBlocoCA12->valorPessoalEncargosSociaisPC     = "0";
            $oDadosBlocoCA12->valorJurosEncargosDividaPIEA      = "0";
            $oDadosBlocoCA12->valorJurosEncargosDividaPIDEA     = "0";
            $oDadosBlocoCA12->valorJurosEncargosDividaPP        = "0";
            $oDadosBlocoCA12->valorJurosEncargosDividaPC        = "0";
            $oDadosBlocoCA12->valorOutrasDespesasCorrentesPIEA  = "0";
            $oDadosBlocoCA12->valorOutrasDespesasCorrentesPIDEA = "0";
            $oDadosBlocoCA12->valorOutrasDespesasCorrentesPP    = "0";
            $oDadosBlocoCA12->valorOutrasDespesasCorrentesPC    = "0";
            $oDadosBlocoCA12->valorInvestimentosPIEA            = "0";
            $oDadosBlocoCA12->valorInvestimentosPIDEA           = "0";
            $oDadosBlocoCA12->valorInvestimentosPP              = "0";
            $oDadosBlocoCA12->valorInvestimentosPC              = "0";
            $oDadosBlocoCA12->valorInversoesFinanceirasPIEA     = "0";
            $oDadosBlocoCA12->valorInversoesFinanceirasPIDEA    = "0";
            $oDadosBlocoCA12->valorInversoesFinanceirasPP       = "0";
            $oDadosBlocoCA12->valorInversoesFinanceirasPC       = "0";
            $oDadosBlocoCA12->valorAmortizacaoDividaPIEA        = "0";
            $oDadosBlocoCA12->valorAmortizacaoDividaPIDEA       = "0";
            $oDadosBlocoCA12->valorAmortizacaoDividaPP          = "0";
            $oDadosBlocoCA12->valorAmortizacaoDividaPC          = "0";
            $this->addElement($oDadosBlocoCA12, $oNodeBlocoCA12);
      
            $oNodeAnexoDados12->appendChild($oNodeBlocoAReceitasA12);
            $oNodeAnexoDados12->appendChild($oNodeBlocoADespesasA12);
            $oNodeAnexoDados12->appendChild($oNodeBlocoBA12);
            $oNodeAnexoDados12->appendChild($oNodeBlocoCA12);
            $oNodeAnexo12->appendChild($oNodeAnexoDados12);
            $oNodeRemessa->appendChild($oNodeAnexo12);
        }
    
        if ($this->getGeraQuadro01()) {
            /*
             * Incluimos o relatório DEMONSTRATIVO DOS RESTOS A PAGAR (Anexo VII)
             * Ele sera processado para disponibilizar as variaveis para geracao do arquivo
             * Com o processamento do arquivo sera criada a variavel $aDadosLRFDemonstrativoRP um array dos dados
             */
            $arqinclude                  = true;
            $lGeracaoArquivoContasAnuais = true;
            $dt_ini                      = $this->dtDataInicial;
            $dt_fin                      = $this->dtDataFinal;
            $anousu                      = $this->iAno;
            $sInstituicoes = db_utils::fieldsMemory(db_query("select array_to_string(array_accum(codigo),',') as instit from db_config"), 0)->instit;
            $db_filtro     = "e60_instit in ({$sInstituicoes})";
            require_once("con2_lrfdemonstrativorp002_natal.php");
            if (!isset($aDadosLRFDemonstrativoRP) || count($aDadosLRFDemonstrativoRP) == 0) {
                throw new Exception("Erro ao buscar dados dos RPS para geração dos dados do Quadro01");
            }
        
            $oQuadro01      = $this->oDocumento->createElement("quadro01");
            $oQuadroDados01 = $this->oDocumento->createElement("quadroDados01");
            $oOrgaosQ01     = $this->oDocumento->createElement("orgaosQ01");
        
            $sHashOrgao = "";
        
            foreach ($aDadosLRFDemonstrativoRP as $oDadosRP) {
                $sIdOrgao = $oDadosRP->Orgao;
                if ($sHashOrgao != $sIdOrgao) {
                    $oOrgaoQ01      = $this->oDocumento->createElement("orgaoQ01");
                    $oDadosOrgao01 = new stdClass();
                    $oDadosOrgao01->codigoOrgao = $oDadosRP->Orgao;
                    $oDadosOrgao01->nomeOrgao   = db_removeAcentuacao($oDadosRP->OrgaoDescricao);
                    $this->addElement($oDadosOrgao01, $oOrgaoQ01);
              
                    $oUnidadesQ01 = $this->oDocumento->createElement("unidadesQ01");
                }
                $sHashOrgao = $sIdOrgao;
            
                $oUnidadeQ01  = $this->oDocumento->createElement("unidadeQ01");
                $oDadosUnidadeQ01 = new stdClass();
                $oDadosUnidadeQ01->codigoUnidade = $oDadosRP->Unidade;
                $oDadosUnidadeQ01->nomeUnidade   = db_removeAcentuacao($oDadosRP->UnidadeDescricao);
                $oDadosUnidadeQ01->fonteRecurso  = str_pad($oDadosRP->FonteRecurso, 5, "0", STR_PAD_LEFT);
                $this->addElement($oDadosUnidadeQ01, $oUnidadeQ01);
            
                $oNodeRestoPagarProcessadoQ01  = $this->oDocumento->createElement("restoPagarProcessadoQ01");
                $oDadosRestoPagarProcessadoQ01 = new stdClass();
                $oDadosRestoPagarProcessadoQ01->inscritosExerciciosAnteriores = number_format($oDadosRP->ProcessadoInscritoExerciciosAnteriores, 2, '.', '');
                $oDadosRestoPagarProcessadoQ01->inscritosDezembro             = number_format($oDadosRP->ProcessadoInscritoDezembro, 2, '.', '');
                $oDadosRestoPagarProcessadoQ01->pagos                         = number_format($oDadosRP->ProcessadoPago, 2, '.', '');
                $oDadosRestoPagarProcessadoQ01->cancelados                    = number_format($oDadosRP->ProcessadoCancelado, 2, '.', '');
                $this->addElement($oDadosRestoPagarProcessadoQ01, $oNodeRestoPagarProcessadoQ01);
                $oUnidadeQ01->appendChild($oNodeRestoPagarProcessadoQ01);
            
                $oNodeRestoPagarNaoProcessadoQ01 = $this->oDocumento->createElement("restoPagarNaoProcessadoQ01");
                $oDadosRestoPagarNaoProcessadoQ01 = new stdClass();
                $oDadosRestoPagarNaoProcessadoQ01->inscritosExerciciosAnteriores = number_format($oDadosRP->NaoProcessadoInscritoExerciciosAnteriores, 2, '.', '');
                $oDadosRestoPagarNaoProcessadoQ01->inscritosDezembro             = number_format($oDadosRP->NaoProcessadoInscritoDezembro, 2, '.', '');
                $oDadosRestoPagarNaoProcessadoQ01->pagos                         = number_format($oDadosRP->NaoProcessadoPago, 2, '.', '');
                $oDadosRestoPagarNaoProcessadoQ01->cancelados                    = number_format($oDadosRP->NaoProcessadoCancelado, 2, '.', '');
                $this->addElement($oDadosRestoPagarNaoProcessadoQ01, $oNodeRestoPagarNaoProcessadoQ01);
                $oUnidadeQ01->appendChild($oNodeRestoPagarNaoProcessadoQ01);
            
                $oUnidadesQ01->appendChild($oUnidadeQ01);
                $oOrgaoQ01->appendChild($oUnidadesQ01);
                $oOrgaosQ01->appendChild($oOrgaoQ01);
            }
        
            $oQuadroDados01->appendChild($oOrgaosQ01);
            $oQuadro01->appendChild($oQuadroDados01);
            $oNodeRemessa->appendChild($oQuadro01);
        }
    
    
        if ($this->getGeraQuadro02()) {
            $oNodeQuadro02      = $this->oDocumento->createElement("quadro02");
            $oNodeQuadroDados02 = $this->oDocumento->createElement("quadroDados02");
      
            $oNodeInscritosQ02         = $this->oDocumento->createElement("inscritosQ02");
            $oNodeInscritosUnidadesQ02 = $this->oDocumento->createElement("unidadesQ02");
      
            $oNodePagosQ02         = $this->oDocumento->createElement("pagosQ02");
            $oNodePagosUnidadesQ02 = $this->oDocumento->createElement("unidadesQ02");
      
            $oNodeCanceladosQ02         = $this->oDocumento->createElement("canceladosQ02");
            $oNodeCanceladosUnidadesQ02 = $this->oDocumento->createElement("unidadesQ02");
      
            $oEmpResto = new cl_empresto();
      
            $sWhereRP1  = " true ";
            $sWhereRP2  = " ";
            $sWhereRP3  = " ";
            $sOrderRPBy = " order by o58_orgao, o58_unidade, o58_codigo, e60_anousu, e60_codemp::int";
            $sSqlEmpResto = $oEmpResto->sql_rp_novo(
                $this->iAno,
                $sWhereRP1,
                $this->dtDataInicial,
                $this->dtDataInicial,
                $sWhereRP2,
                $sWhereRP3,
                "$sOrderRPBy "
            );
            $rsDadosRP = db_query($sSqlEmpResto);
            $iLinhasRP = pg_num_rows($rsDadosRP);
            for ($iInd = 0; $iInd < $iLinhasRP; $iInd++) {
                $oDadosRP = db_utils::fieldsMemory($rsDadosRP, $iInd);
        
                $sSqlCaracteristicaPeculiar = "select o58_concarpeculiar 
      			                         from orcdotacao
      			                              inner join empempenho on e60_coddot = o58_coddot
      			                                                   and e60_anousu = o58_anousu
      			                                                   and e60_instit = o58_instit
      			                        where e60_numemp = {$oDadosRP->e60_numemp}";
                $rsCaracteristicaPeculiar = db_query($sSqlCaracteristicaPeculiar);
                $sCaracteristicaPeculiar = db_utils::fieldsMemory($rsCaracteristicaPeculiar, 0)->o58_concarpeculiar;
        
                $sClassificacaoFuncional   = substr($sCaracteristicaPeculiar, 1, 2).
                                     str_pad($oDadosRP->o58_orgao, 2, '0', STR_PAD_LEFT).str_pad($oDadosRP->o58_unidade, 3, '0', STR_PAD_LEFT).
                                     str_pad($oDadosRP->o58_funcao, 2, '0', STR_PAD_LEFT).
                                     str_pad($oDadosRP->o58_subfuncao, 3, '0', STR_PAD_LEFT).
                                     str_pad($oDadosRP->o58_programa, 4, '0', STR_PAD_LEFT).
                                     str_pad($oDadosRP->o58_projativ, 4, '0', STR_PAD_LEFT).
                                     str_pad($oDadosRP->o58_codigo, 10, '0', STR_PAD_LEFT).
                                     substr($oDadosRP->o56_elemento, 2, 8);
                                     
                $nVlrInscritoProcessado    = number_format(abs(round($oDadosRP->e91_vlrliq - $oDadosRP->e91_vlrpag, 2)), 2, '.', '');
                $nVlrInscritoNaoProcessado = number_format(abs(round($oDadosRP->e91_vlremp - $oDadosRP->e91_vlranu - $oDadosRP->e91_vlrliq, 2)), 2, '.', '');
                $nVlrAnuladoProcessado     = number_format(abs($oDadosRP->vlranuliq), 2, '.', '');
                $nVlrAnuladoNaoProcessado  = number_format(abs($oDadosRP->vlranuliqnaoproc), 2, '.', '');
                $nVlrPagoProcessado        = number_format(abs($oDadosRP->vlrpag), 2, '.', '');
                $nVlrPagoNaoProcessado     = number_format(abs($oDadosRP->vlrpagnproc), 2, '.', '');
        
                $sNomeCredor               = htmlentities(db_removeAcentuacao($oDadosRP->z01_nome));
        
                /*
                 * RP inscritos
                 */
                  $oNodeInscritosUnidadeQ02   = $this->oDocumento->createElement("unidadeQ02");
          
                  $oDadosUnidadeQ02 = new stdClass();
                  $oDadosUnidadeQ02->codigoUnidade                      = str_pad($oDadosRP->o58_orgao, 2, '0', STR_PAD_LEFT).str_pad($oDadosRP->o58_unidade, 3, '0', STR_PAD_LEFT);
                  $oDadosUnidadeQ02->nomeUnidade                        = db_removeAcentuacao($oDadosRP->o41_descr);
                  $oDadosUnidadeQ02->fonteRecurso                       = str_pad($oDadosRP->o58_codigo, 5, '0', STR_PAD_LEFT);
                  $oDadosUnidadeQ02->numeroEmpenho                      = $oDadosRP->e60_numemp;
                  $oDadosUnidadeQ02->data                               = $oDadosRP->e60_emiss;
                  $oDadosUnidadeQ02->classificacaoFuncionalProgramatica = $sClassificacaoFuncional;
                  $oDadosUnidadeQ02->credor                             = $sNomeCredor;
                  $oDadosUnidadeQ02->valorProcessado                    = $nVlrInscritoProcessado;
                  $oDadosUnidadeQ02->valorNaoProcessado                 = $nVlrInscritoNaoProcessado;
                  $this->addElement($oDadosUnidadeQ02, $oNodeInscritosUnidadeQ02);

                  $oNodeInscritosUnidadesQ02->appendChild($oNodeInscritosUnidadeQ02);
          
                  /*
                   * RP pagos
                   */
                  $oNodePagosUnidadeQ02   = $this->oDocumento->createElement("unidadeQ02");
          
                  $oDadosUnidadeQ02 = new stdClass();
                  $oDadosUnidadeQ02->codigoUnidade                      = str_pad($oDadosRP->o58_orgao, 2, '0', STR_PAD_LEFT).str_pad($oDadosRP->o58_unidade, 3, '0', STR_PAD_LEFT);
                  $oDadosUnidadeQ02->nomeUnidade                        = db_removeAcentuacao($oDadosRP->o41_descr);
                  $oDadosUnidadeQ02->fonteRecurso                       = str_pad($oDadosRP->o58_codigo, 5, '0', STR_PAD_LEFT);
                  $oDadosUnidadeQ02->numeroEmpenho                      = $oDadosRP->e60_numemp;
                  $oDadosUnidadeQ02->data                               = $oDadosRP->e60_emiss;
                  $oDadosUnidadeQ02->classificacaoFuncionalProgramatica = $sClassificacaoFuncional;
                  $oDadosUnidadeQ02->credor                             = $sNomeCredor;
                  $oDadosUnidadeQ02->valorProcessado                    = $nVlrPagoProcessado;
                  $oDadosUnidadeQ02->valorNaoProcessado                 = $nVlrPagoNaoProcessado;
                  $this->addElement($oDadosUnidadeQ02, $oNodePagosUnidadeQ02);
          
                  $oNodePagosUnidadesQ02->appendChild($oNodePagosUnidadeQ02);
          
                  /*
                   * RP anulados
                   */
                  $oNodeCanceladosUnidadeQ02   = $this->oDocumento->createElement("unidadeQ02");
          
                  $oDadosUnidadeQ02 = new stdClass();
                  $oDadosUnidadeQ02->codigoUnidade                      = str_pad($oDadosRP->o58_orgao, 2, '0', STR_PAD_LEFT).".".str_pad($oDadosRP->o58_unidade, 2, '0', STR_PAD_LEFT);
                  $oDadosUnidadeQ02->nomeUnidade                        = db_removeAcentuacao($oDadosRP->o41_descr);
                  $oDadosUnidadeQ02->fonteRecurso                       = str_pad($oDadosRP->o58_codigo, 5, '0', STR_PAD_LEFT);
                  $oDadosUnidadeQ02->numeroEmpenho                      = $oDadosRP->e60_numemp;
                  $oDadosUnidadeQ02->data                               = $oDadosRP->e60_emiss;
                  $oDadosUnidadeQ02->classificacaoFuncionalProgramatica = $sClassificacaoFuncional;
                  $oDadosUnidadeQ02->credor                             = $sNomeCredor;
                  $oDadosUnidadeQ02->valorProcessado                    = $nVlrAnuladoProcessado;
                  $oDadosUnidadeQ02->valorNaoProcessado                 = $nVlrAnuladoNaoProcessado;
                  $this->addElement($oDadosUnidadeQ02, $oNodeCanceladosUnidadeQ02);
          
                  $oNodeCanceladosUnidadesQ02->appendChild($oNodeCanceladosUnidadeQ02);
            }
      
            $oNodeInscritosQ02->appendChild($oNodeInscritosUnidadesQ02);
            $oNodePagosQ02->appendChild($oNodePagosUnidadesQ02);
            $oNodeCanceladosQ02->appendChild($oNodeCanceladosUnidadesQ02);
      
      
            $oNodeQuadroDados02->appendChild($oNodeInscritosQ02);
            $oNodeQuadroDados02->appendChild($oNodePagosQ02);
            $oNodeQuadroDados02->appendChild($oNodeCanceladosQ02);
            $oNodeQuadro02->appendChild($oNodeQuadroDados02);
            $oNodeRemessa->appendChild($oNodeQuadro02);
        }
    
        $this->oDocumento->appendChild($oNodeRemessa);
      
        $oRetornoValidacao = $this->validarXML($this->oDocumento, 'model/contabilidade/arquivos/siai/v2018/xsd/XSD_ContasAnuais_ecidade.txt');
        if ($oRetornoValidacao->lErro == true) {
            $this->addLog($oRetornoValidacao->sMsg);
            //throw new Exception($oRetornoValidacao->sMsg);
        }
    
        $this->sArquivo = $this->oDocumento->saveXML();
    }
  
  
    public function getArquivo()
    {
        return $this->sArquivo;
    }
}
