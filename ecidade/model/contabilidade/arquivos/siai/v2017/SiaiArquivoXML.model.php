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
require_once(modification("model/contabilidade/arquivos/siai/SiaiArquivoBaseXML.model.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("libs/db_stdlib.php"));

class SiaiArquivoXML extends SiaiArquivoBaseXML
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
    protected $iAnoUsu = 2016;
  /*
   * Seta se os anexos serão gerados
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

  /**
   * Gera o arquivo XML
   */
    public function gerarDados()
    {
    
        $iAnoSessao         = db_getsession('DB_anousu');
        $iInstituicaoSessao = db_getsession('DB_instit');

        $sNomeArquivo = 'contas_anuais.xml';
        $this->setNomeArquivo($sNomeArquivo);
    
        $arquivoXML = fopen("tmp/".$this->getNomeArquivo(), 'w+');

      //Strings para concatenar a fim de fazer o aninhamento
        $sFilhoNivel1  = "  ";
        $sFilhoNivel2  = "    ";
        $sFilhoNivel3  = "      ";
        $sFilhoNivel4  = "        ";
        $sFilhoNivel5  = "          ";
        $sFilhoNivel6  = "            ";
        $sFilhoNivel7  = "              ";
        $sFilhoNivel8  = "                ";
        $sFilhoNivel9  = "                  ";
        $sFilhoNivel10 = "                    ";

      /******* CABEÇALHO *******/

        $sSqlCPFGestor = "SELECT z01_cgccpf as cpf 
                      FROM plugins.assinaturaordenadordespesa 
                        INNER JOIN db_departorg on db01_coddepto = departamento 
                        INNER JOIN cgm on z01_numcgm = assinaturaordenadordespesa.numcgm 
                      WHERE db01_orgao = 25 
                        AND db01_unidade = 1 
                        AND db01_anousu = 2017 
                        AND principal = 't' 
                      LIMIT 1";
        $sCPFGestor    = db_utils::fieldsMemory(db_query($sSqlCPFGestor), 0)->cpf;

        $sLinhaHeader  = "<remessa>";
        $sLinhaTrailer = "</remessa>";
      //Elementos filho de <remessa>
        $sLinhaCodOrgao    = $sFilhoNivel1."<codigoOrgao>{$this->getCodigoOrgaoTCE()}</codigoOrgao>"; // CODIGO IDENTIFICADOR DO ORGAO
        $sLinhaCPFGestor   = $sFilhoNivel1."<cpfGestor>{$sCPFGestor}</cpfGestor>"; //CPF DO ORDENADOR DE DESPESA
        $sLinhaTipoRemessa = $sFilhoNivel1."<tipoRemessa>7</tipoRemessa>"; // FIXO 7
        $sLinhaAno         = $sFilhoNivel1."<ano>{$iAnoSessao}</ano>";  // ANO DE REFERENCIA
        $sLinhaDataCriacao = $sFilhoNivel1."<dataCriacao>".date('Y-m-d')."</dataCriacao>";//
        $sLinhaSistemaGera = $sFilhoNivel1."<sistemaGerador>ECIDADE</sistemaGerador>";// -- ECIDADE

      //Escreve no arquivo
        fputs($arquivoXML, $sLinhaHeader      ."\r\n"
                      .$sLinhaCodOrgao    ."\r\n"
                      .$sLinhaCPFGestor   ."\r\n"
                      .$sLinhaTipoRemessa ."\r\n"
                      .$sLinhaAno         ."\r\n"
                      .$sLinhaDataCriacao ."\r\n"
                      .$sLinhaSistemaGera ."\r\n");

        if ($this->getGeraAnexo01()) {
            $sLinhaAnexo01RecTributaria = "";
            $sLinhaAnexo01RecContribuicoes = "";
            $sLinhaAnexo01RecPatrimonial = "";
            $sLinhaAnexo01RecAgropecuaria = "";
            $sLinhaAnexo01RecIndustrial = "";
            $sLinhaAnexo01RecServicos = "";
            $sLinhaAnexo01TransfCorrentes = "";
            $sLinhaAnexo01RecCorrentes = "";
            $sLinhaAnexo01RecIntraOrc = "";
            $sLinhaAnexo01DedRestituicoes = "";
            $sLinhaAnexo01DedTransfFundeb = "";
            $sLinhaAnexo01DedOutras = "";
            $sLinhaAnexo01DespEncargosSociais = "";
            $sLinhaAnexo01DespJurosEncargos = "";
            $sLinhaAnexo01DespCorrentes = "";
            $sLinhaAnexo01DespIntraOrc = "";
            $sLinhaAnexo01RecOpCredito = "";
            $sLinhaAnexo01RecAlienacao = "";
            $sLinhaAnexo01RecAmortiEmp = "";
            $sLinhaAnexo01TransfCapital = "";
            $sLinhaAnexo01RecOutrasCapital = "";
            $sLinhaAnexo01DespInvestimentos = "";
            $sLinhaAnexo01DespInversoesFinanc = "";
            $sLinhaAnexo01DespAmortiDiv = "";
            $sLinhaAnexo01RecAlienacao = "";
            $sLinhaAnexo01RecAmortiEmp = "";
            $sLinhaAnexo01TransfCapital = "";
            $sLinhaAnexo01RecOutrasCapital = "";
            $sLinhaAnexo01DespInvestimentos = "";
            $sLinhaAnexo01DespInversoesFinanc = "";
            $sLinhaAnexo01DespAmortiDiv = "";

            $sSqlWork1  = " create temp table work1 as                ";
            $sSqlWork1 .= " select o56_elemento||'00' as elemento,    ";
            $sSqlWork1 .= "       o56_descr                 as descr, ";
            $sSqlWork1 .= "       0::float8                 as valor  ";
            $sSqlWork1 .= "  from orcelemento                         ";
            $sSqlWork1 .= " where o56_anousu = 2016                   ";
            $sSqlWork1 .= " union                                     ";
            $sSqlWork1 .= " select o57_fonte,                         ";
            $sSqlWork1 .= "        o57_descr,                         ";
            $sSqlWork1 .= "        0::float8 as valor                 ";
            $sSqlWork1 .= "   from orcfontes                          ";
            $sSqlWork1 .= "  where o57_anousu = 2016                  ";
            $rsSqlWork1 = db_query($sSqlWork1);

            $dataini    = '2016-01-01';
            $datafin    = '2016-12-31';
            $result_rec = db_receitasaldo(11, 1, 3, true, "", 2016, $dataini, $datafin);

            $valor = 0;
            for ($i = 0; $i < pg_numrows($result_rec); $i++) {
                $oRec = db_utils::fieldsMemory($result_rec, $i);
                $valor = $oRec->saldo_arrecadado;
        
                $sSqlwork1Update  = "update work1 set valor = valor+{$valor} where work1.elemento = '{$oRec->o57_fonte}'";
                $rsSqlwork1Update = db_query($sSqlwork1Update);
                $executa          = true;
                $conta            = 0;
            }

            $result_rec = db_dotacaosaldo(7, 3, 3, true, "", 2016, $dataini, $datafin, null, null, null, 2);
            $valor      = 0;

            for ($i = 0; $i < pg_numrows($result_rec); $i++) {
                $oRec = db_utils::fieldsMemory($result_rec, $i);
                $valor = $oRec->empenhado-$oRec->anulado;
        
                $sSqlwork1Update  = "update work1 set valor = valor+{$valor} where work1.elemento = '{$oRec->o58_elemento}00'";
                $rsSqlwork1Update = db_query($sSqlwork1Update);
        
                $conta            = 0;
                $executa          = true;
                while ($executa == true) {
                    $o58_elemento     = db_le_mae($oRec->o58_elemento, false);
                    $sSqlwork1Update  = "update work1 set valor = valor+{$valor} where work1.elemento = '{$o58_elemento}00'";
                    $rsSqlwork1Update = db_query($sSqlwork1Update);
          
                    if (substr($o58_elemento, 2, 13) == "0000000000000") {
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

            $sLinhaAnexo01 = "";

            $sLinhaAnexo01 .= "<anexo01>\r\n";
            $sLinhaAnexo01 .= "<anexoDados01>"."\r\n";

            $sFilhoNivel1 = " ";

            $numreg = (sizeof($descra)>sizeof($descrb)?sizeof($descra):sizeof($descrb));
            for ($i = 0; $i < $numreg; $i++) {
                if (isset($descra[$i])) {
                    if ($vlra[$i] == 0 || empty($vlra[$i])) {
                        $sValorA = "0.00";
                    } else {
                        $sValorA = number_format($vlra[$i], 2, '.', '');
                    }


                    if (trim($descra[$i]) == "RECEITA TRIBUTARIA") {
                        $sLinhaAnexo01RecTributaria .= $sFilhoNivel1."<receitaTributaria>".$sValorA."</receitaTributaria>\r\n";
                    }
                    if (trim($descra[$i]) == "RECEITAS DE CONTRIBUIÇÕES") {
                        $sLinhaAnexo01RecContribuicoes .= $sFilhoNivel1."<receitadeContribuicoes>".$sValorA."</receitadeContribuicoes>\r\n";
                    }
                    if (trim($descra[$i]) == "RECEITA PATRIMONIAL") {
                        $sLinhaAnexo01RecPatrimonial  .= $sFilhoNivel1."<receitaPatrimonial>".$sValorA."</receitaPatrimonial>\r\n";
                        $sLinhaAnexo01RecAgropecuaria .= $sFilhoNivel1."<receitaAgropecuaria>0.00</receitaAgropecuaria>"."\r\n";
                        $sLinhaAnexo01RecIndustrial   .= $sFilhoNivel1."<receitaIndustrial>0.00</receitaIndustrial>"."\r\n";
                    }
                    if (trim($descra[$i]) == "RECEITA DE SERVICOS") {
                        $sLinhaAnexo01RecServicos .= $sFilhoNivel1."<receitadeServicos>".$sValorA."</receitadeServicos>\r\n";
                    }
                    if (trim($descra[$i]) == "TRANSFERENCIAS CORRENTES") {
                        $sLinhaAnexo01TransfCorrentes .= $sFilhoNivel1."<transferenciasCorrentes>".$sValorA."</transferenciasCorrentes>\r\n";
                    }
                    if (trim($descra[$i]) == "OUTRAS RECEITAS CORRENTES") {
                        $sLinhaAnexo01RecCorrentes .= $sFilhoNivel1."<outrasReceitasCorrentes>".$sValorA."</outrasReceitasCorrentes>\r\n";
                    }
                    if (trim($descra[$i]) == "RECEITAS INTRA-ORÇAMENTÁRIAS CORRENTES") {
                        $sLinhaAnexo01RecIntraOrc .= $sFilhoNivel1."<receitasIntraorcamentarias>".$sValorA."</receitasIntraorcamentarias>\r\n";
                    }
                    if (trim($descra[$i]) == "(R) DEDUCOES DA RECEITA CORRENTE") {
                        $sLinhaAnexo01DedRestituicoes .= $sFilhoNivel1."<deducaoRestituicoes>0.00</deducaoRestituicoes>"."\r\n";
                        $sLinhaAnexo01DedTransfFundeb .= $sFilhoNivel1."<deducaoTransferenciasFundeb>".abs($sValorA)."</deducaoTransferenciasFundeb>\r\n";
                        $sLinhaAnexo01DedOutras .= $sFilhoNivel1."<outrasDeducoes>0.00</outrasDeducoes>"."\r\n";
                    }
                }

                if (isset($descrb[$i])) {
                    if ($vlrb[$i] == 0 || empty($vlrb[$i])) {
                        $sValorB = "0.00";
                    } else {
                        $sValorB = number_format($vlrb[$i], 2, '.', '');
                    }
          

                    if (trim($descrb[$i]) == "PESSOAL E ENCARGOS SOCIAIS") {
                        $sLinhaAnexo01DespEncargosSociais .= $sFilhoNivel1."<despesaPessoalEncargosSociais>".$sValorB."</despesaPessoalEncargosSociais>\r\n";
                    }
                    if (trim($descrb[$i]) == "JUROS E ENCARGOS DA DÍVIDA") {
                        $sLinhaAnexo01DespJurosEncargos .= $sFilhoNivel1."<despesaJurosEncargosDivida>".$sValorB."</despesaJurosEncargosDivida>\r\n";
                    }
                    if (trim($descrb[$i]) == "OUTRAS DESPESAS CORRENTES") {
                        $sLinhaAnexo01DespCorrentes .= $sFilhoNivel1."<outrasDespesasCorrentes>".$sValorB."</outrasDespesasCorrentes>\r\n";
                        $sLinhaAnexo01DespIntraOrc  .= $sFilhoNivel1."<despesasIntraorcamentarias>0.00</despesasIntraorcamentarias>"."\r\n";
                    }
                }
            }

            $sLinhaAnexo01 .= $sLinhaAnexo01RecTributaria.
                        $sLinhaAnexo01RecContribuicoes.
                        $sLinhaAnexo01RecPatrimonial.
                        $sLinhaAnexo01RecAgropecuaria.
                        $sLinhaAnexo01RecIndustrial.
                        $sLinhaAnexo01RecServicos.
                        $sLinhaAnexo01TransfCorrentes.
                        $sLinhaAnexo01RecCorrentes.
                        $sLinhaAnexo01RecIntraOrc.
                        $sLinhaAnexo01DedRestituicoes.
                        $sLinhaAnexo01DedTransfFundeb.
                        $sLinhaAnexo01DedOutras.
                        $sLinhaAnexo01DespEncargosSociais.
                        $sLinhaAnexo01DespJurosEncargos.
                        $sLinhaAnexo01DespCorrentes.
                        $sLinhaAnexo01DespIntraOrc;

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


                    if ($descrd[$i] == "OPERACOES DE CREDITO") {
                        $sLinhaAnexo01RecOpCredito .= $sFilhoNivel1."<receitaOperacoesCredito>".$sValorD."</receitaOperacoesCredito>\r\n";
                    }
                    if ($descrd[$i] == "ALIENAÇÃO DE BENS") {
                        $sLinhaAnexo01RecAlienacao .= $sFilhoNivel1."<receitaAlienacaoBens>".$sValorD."</receitaAlienacaoBens>\r\n";
                        $sLinhaAnexo01RecAmortiEmp .= $sFilhoNivel1."<receitaAmortizacaoEmprestimos>0.00</receitaAmortizacaoEmprestimos>"."\r\n";
                    }
                    if ($descrd[$i] == "TRANSFERÊNCIAS DE CAPITAL") {
                        $sLinhaAnexo01TransfCapital .= $sFilhoNivel1."<transferenciasCapital>".$sValorD."</transferenciasCapital>\r\n";
                    }
                    if ($descrd[$i] == "OUTRAS RECEITAS DE CAPITAL") {
                        $sLinhaAnexo01RecOutrasCapital .= $sFilhoNivel1."<outrasReceitasCapital>".$sValorD."</outrasReceitasCapital>\r\n";
                    }
                }
        
                if (isset($descre[$i])) {
                    if ($vlre[$i] == 0 || empty($vlre[$i])) {
                        $sValorE = "0.00";
                    } else {
                        $sValorE = number_format($vlre[$i], 2, '.', '');
                    }


                    if ($descre[$i] == "DESPESA DE INVESTIMENTOS") {
                        $sLinhaAnexo01DespInvestimentos .= $sFilhoNivel1."<despesaInvestimentos>".$sValorE."</despesaInvestimentos>\r\n";
                    }
                    if ($descre[$i] == "INVERSÕES FINANCEIRAS") {
                        $sLinhaAnexo01DespInversoesFinanc .= $sFilhoNivel1."<despesaInversoesFinanceiras>".$sValorE."</despesaInversoesFinanceiras>\r\n";
                    }
                    if ($descre[$i] == "AMORTIZAÇÃO DA DÍVIDA") {
                        $sLinhaAnexo01DespAmortiDiv .= $sFilhoNivel1."<despesaAmortizacaoDivida>".$sValorE."</despesaAmortizacaoDivida>\r\n";
                    }
                }
            }

            $sLinhaAnexo01 .= $sLinhaAnexo01RecOpCredito.
                        $sLinhaAnexo01RecAlienacao.
                        $sLinhaAnexo01RecAmortiEmp.
                        $sLinhaAnexo01TransfCapital.
                        $sLinhaAnexo01RecOutrasCapital.
                        $sLinhaAnexo01DespInvestimentos.
                        $sLinhaAnexo01DespInversoesFinanc.
                        $sLinhaAnexo01DespAmortiDiv;

            $sLinhaAnexo01 .= "</anexoDados01>"."\r\n";
            $sLinhaAnexo01 .= "</anexo01>\r\n";
      
          //Para escrever o anexo no arquivo
            fputs($arquivoXML, $sLinhaAnexo01);
        }

        if ($this->getGeraAnexo02()) {
            $sLinhaAnexo02  = "<anexo02>\r\n";
            $sLinhaAnexo02 .= "<anexoDados02>\r\n";
            $sLinhaAnexo02 .= $sFilhoNivel1."<despesasA02>\r\n";

            $sSqlDespesas = "select RPAD(substr(o56_elemento, 2, 1)::TEXT, 8, '0') as o56_elemento, 
                  sum(o58_valor) as totaldespesa 
               from orcelemento 
                inner join orcdotacao on o56_codele = o58_codele and o56_anousu = o58_anousu 
               where o56_anousu = 2016 and substr(o56_elemento, 2, 1) in ('3', '4') 
               group by substr(o56_elemento, 2, 1)";
            $rsDespesas = db_query($sSqlDespesas);
            for ($i=0; $i < pg_num_rows($rsDespesas); $i++) {
                $oDespesa = db_utils::fieldsMemory($rsDespesas, $i);

                $sLinhaAnexo02 .= $sFilhoNivel2."<despesaA02>\r\n";
                $sLinhaAnexo02 .= $sFilhoNivel3."<codigoDespesa>$oDespesa->o56_elemento</codigoDespesa>\r\n";
                $sLinhaAnexo02 .= $sFilhoNivel3."<valorDespesa>".number_format($oDespesa->totaldespesa, 2, '.', '')."</valorDespesa>\r\n";
                $sLinhaAnexo02 .= $sFilhoNivel2."</despesaA02>\r\n";
            }
            $sLinhaAnexo02 .= $sFilhoNivel1."</despesasA02>\r\n";
            $sLinhaAnexo02 .= "</anexoDados02>\r\n";
            $sLinhaAnexo02 .= "</anexo02>\r\n";
            fputs($arquivoXML, $sLinhaAnexo02."\r\n");
        }

        if ($this->getGeraAnexo06()) {
            $sLinhaAnexo06  = "<anexo06>\r\n";
            $sLinhaAnexo06 .= $sFilhoNivel1."<anexoDados06>\r\n";
            $sLinhaAnexo06 .= $sFilhoNivel1."<orgaosA06>\r\n";

            $sSqlOrgaos = "select distinct o40_orgao, o40_descr from orcorgao inner join orcdotacao on o58_orgao   = o40_orgao
                                                                                   and o58_anousu  = o40_anousu
                                                                                   and o58_instit  = o40_instit
                                                                                  where o58_anousu = $this->iAnoUsu";
            $rsOrgaos = db_query($sSqlOrgaos);

            for ($i = 0; $i < pg_num_rows($rsOrgaos); $i++) {
                $oOrgao = db_utils::fieldsMemory($rsOrgaos, $i);
                if ($oOrgao->o40_orgao == "99") {
                    continue;
                }
                $sLinhaAnexo06 .= $sFilhoNivel2."<orgaoA06>\r\n";
                $sLinhaAnexo06 .= $sFilhoNivel2."<codigoOrgao>$oOrgao->o40_orgao</codigoOrgao>\r\n";
                $sLinhaAnexo06 .= $sFilhoNivel2."<nomeOrgao>$oOrgao->o40_descr</nomeOrgao>\r\n";
                $sLinhaAnexo06 .= $sFilhoNivel2."<unidadesA06>\r\n";
        
                $sSqlUnidades = "select distinct o41_unidade, o41_descr, o41_instit from orcunidade
                                                              inner join orcdotacao on o58_unidade = o41_unidade
                                                                                   and o58_orgao   = o41_orgao
                                                                                   and o58_anousu  = o41_anousu
                                                                                   and o58_instit  = o41_instit
                                                                                  where o58_anousu = $this->iAnoUsu
                                                                                    and o58_orgao  = $oOrgao->o40_orgao";
                $rsUnidades = db_query($sSqlUnidades);
        
                for ($j = 0; $j < pg_num_rows($rsUnidades); $j++) {
                    $oUnidade = db_utils::fieldsMemory($rsUnidades, $j);
                    $sLinhaAnexo06 .= $sFilhoNivel3."<unidadeA06>\r\n";
                    $sLinhaAnexo06 .= $sFilhoNivel4."<codigoUnidadeGestora>$oUnidade->o41_unidade</codigoUnidadeGestora>\r\n";
                    $sLinhaAnexo06 .= $sFilhoNivel4."<nomeUnidadeGestora>$oUnidade->o41_descr</nomeUnidadeGestora>\r\n";
                    $sLinhaAnexo06 .= $sFilhoNivel4."<funcoesA06>\r\n";
                    $sSqlFuncoes = "select distinct o52_funcao from orcfuncao 
                                                        inner join orcdotacao on o52_funcao = o58_funcao
                                                                   where o58_orgao   = $oOrgao->o40_orgao
                                                                     and o58_unidade = $oUnidade->o41_unidade
                                                                     and o58_anousu  = $this->iAnoUsu
                                                                     and o58_instit  = $oUnidade->o41_instit
                                                                     and o58_orgao   = $oOrgao->o40_orgao
                                                                     and o58_unidade  = $oUnidade->o41_unidade";
                    $rsFuncoes = db_query($sSqlFuncoes);
          
                    $aFuncoes = array();

                    for ($k = 0; $k < pg_num_rows($rsFuncoes); $k++) {
                        $oFuncao = db_utils::fieldsMemory($rsFuncoes, $k);
                        if (in_array($oFuncao->o52_funcao, $aFuncoes)) {
                            continue;
                        }
                        $aFuncoes[] = $oFuncao->o52_funcao;
                        $sLinhaAnexo06 .= $sFilhoNivel5."<funcaoA06>\r\n";
                        $sLinhaAnexo06 .= $sFilhoNivel5."<codigoFuncao>".str_pad($oFuncao->o52_funcao, 2, "0", STR_PAD_LEFT)."</codigoFuncao>\r\n";
                        $sLinhaAnexo06 .= $sFilhoNivel5."<subfuncoesA06>\r\n";
                        $sSqlSubFuncoes = "select distinct o53_subfuncao from orcsubfuncao
                                              inner join orcdotacao on o53_subfuncao = o58_subfuncao
                                                    where o58_funcao = $oFuncao->o52_funcao
                                                      and o58_anousu = $this->iAnoUsu
                                                                     and o58_orgao   = $oOrgao->o40_orgao
                                                                     and o58_unidade  = $oUnidade->o41_unidade";
                        $rsSubFuncoes = db_query($sSqlSubFuncoes);

                        $aSubFuncoes = array();

                        for ($l = 0; $l < pg_num_rows($rsSubFuncoes); $l++) {
                            $oSubFuncao = db_utils::fieldsMemory($rsSubFuncoes, $l);
                            if (in_array($oSubFuncao->o53_subfuncao, $aSubFuncoes)) {
                                continue;
                            }
                            $aSubFuncoes[] = $oSubFuncao->o53_subfuncao;
                            $sLinhaAnexo06 .= $sFilhoNivel6."<subfuncaoA06>\r\n";
                            $sLinhaAnexo06 .= $sFilhoNivel6."<codigoSubfuncao>".str_pad($oSubFuncao->o53_subfuncao, 3, "0", STR_PAD_LEFT)."</codigoSubfuncao>\r\n";
                            $sLinhaAnexo06 .= $sFilhoNivel6."<programasA06>\r\n";
                            $sSqlProgramas = "select distinct o54_programa, o54_descr from orcprograma 
                                                         inner join orcdotacao on o58_programa = o54_programa
                                                                              and o58_anousu = o54_anousu
                                                               where o58_anousu = $this->iAnoUsu
                                                                 and o58_funcao = $oFuncao->o52_funcao
                                                                 and o58_subfuncao = $oSubFuncao->o53_subfuncao
                                                                     and o58_orgao   = $oOrgao->o40_orgao
                                                                     and o58_unidade  = $oUnidade->o41_unidade";
                            $rsProgramas = db_query($sSqlProgramas);

                            $aProgramas = array();

                            for ($m = 0; $m < pg_num_rows($rsProgramas); $m++) {
                                $oPrograma = db_utils::fieldsMemory($rsProgramas, $m);
                                if (in_array($oPrograma->o54_programa, $aProgramas)) {
                                    continue;
                                }
                                $aProgramas[] = $oPrograma->o54_programa;
                                $sLinhaAnexo06 .= $sFilhoNivel7."<programaA06>\r\n";
                                $sLinhaAnexo06 .= $sFilhoNivel8."<codigoPrograma>".str_pad($oPrograma->o54_programa, 4, "0", STR_PAD_LEFT)."</codigoPrograma>\r\n";
                                $sLinhaAnexo06 .= $sFilhoNivel8."<nomePrograma>$oPrograma->o54_descr</nomePrograma>\r\n";
                                $sLinhaAnexo06 .= $sFilhoNivel8."<acoesA06>\r\n";
                                $sSqlAcoes = "select o55_projativ, o55_descr, sum(o58_valor) as o58_valor from orcprojativ 
                                                                  inner join orcdotacao on o58_projativ = o55_projativ
                                                                                       and o58_anousu = o55_anousu
                                                                        where o58_anousu = $this->iAnoUsu
                                                                          and o58_funcao = $oFuncao->o52_funcao
                                                                          and o58_subfuncao = $oSubFuncao->o53_subfuncao
                                                                          and o58_programa = $oPrograma->o54_programa
                                                                     and o58_orgao   = $oOrgao->o40_orgao
                                                                     and o58_unidade  = $oUnidade->o41_unidade
				group by o55_projativ, o55_descr";
                                $rsAcoes = db_query($sSqlAcoes);
                
                                $aAcoes = array();
                
                                for ($n = 0; $n < pg_num_rows($rsAcoes); $n++) {
                                    $oAcao = db_utils::fieldsMemory($rsAcoes, $n);
                                    if (in_array($oAcao->o55_projativ, $aAcoes)) {
                                        continue;
                                    }
                                    $aAcoes[] = $oAcao->o55_projativ;
                                    $sLinhaAnexo06 .= $sFilhoNivel9."<acaoA06>\r\n";
                                    $sLinhaAnexo06 .= $sFilhoNivel10."<codigoAcao>$oAcao->o55_projativ</codigoAcao>\r\n";
                                    $sLinhaAnexo06 .= $sFilhoNivel10."<nomeAcao>$oAcao->o55_descr</nomeAcao>\r\n";
                                    if (substr($oAcao->o55_projativ, 0, 1) == "1") {
                                        $sLinhaAnexo06 .= $sFilhoNivel10."<valorProjeto>".number_format($oAcao->o58_valor, 2, '.', '')."</valorProjeto>\r\n";
                                        $sLinhaAnexo06 .= $sFilhoNivel10."<valorAtividade>0.00</valorAtividade>\r\n";
                                    } else {
                                        $sLinhaAnexo06 .= $sFilhoNivel10."<valorProjeto>0.00</valorProjeto>\r\n";
                                        $sLinhaAnexo06 .= $sFilhoNivel10."<valorAtividade>".number_format($oAcao->o58_valor, 2, '.', '')."</valorAtividade>\r\n";
                                    }
                                    $sLinhaAnexo06 .= $sFilhoNivel9."</acaoA06>\r\n";
                                }
                                $sLinhaAnexo06 .= $sFilhoNivel8."</acoesA06>\r\n";
                                $sLinhaAnexo06 .= $sFilhoNivel7."</programaA06>\r\n";
                            }
                            $sLinhaAnexo06 .= $sFilhoNivel6."</programasA06>\r\n";
                            $sLinhaAnexo06 .= $sFilhoNivel6."</subfuncaoA06>\r\n";
                        }
                        $sLinhaAnexo06 .= $sFilhoNivel5."</subfuncoesA06>\r\n";
                        $sLinhaAnexo06 .= $sFilhoNivel5."</funcaoA06>\r\n";
                    }
                    $sLinhaAnexo06 .= $sFilhoNivel4."</funcoesA06>\r\n";
                    $sLinhaAnexo06 .= $sFilhoNivel3."</unidadeA06>\r\n";
                }
                $sLinhaAnexo06 .= $sFilhoNivel2."</unidadesA06>\r\n";
                $sLinhaAnexo06 .= $sFilhoNivel2."</orgaoA06>\r\n";
            }
            $sLinhaAnexo06 .= $sFilhoNivel1."</orgaosA06>\r\n";
            $sLinhaAnexo06 .= $sFilhoNivel1."</anexoDados06>\r\n";
            $sLinhaAnexo06 .= "</anexo06>\r\n";
            fputs($arquivoXML, $sLinhaAnexo06."\r\n");
        }

        if ($this->getGeraAnexo07()) {
            $sLinhaAnexo07  = "<anexo07>\r\n";
            $sLinhaAnexo07 .= "<anexoDados07>\r\n";
            $sLinhaAnexo07 .= $sFilhoNivel1."<funcoesA07>\r\n";

            $sSqlFuncoes = "select distinct o52_funcao from orcfuncao 
                          inner join orcdotacao on o52_funcao = o58_funcao
                              where o58_anousu  = $this->iAnoUsu";
            $rsFuncoes = db_query($sSqlFuncoes);

            for ($i = 0; $i < pg_num_rows($rsFuncoes); $i++) {
                $oFuncao = db_utils::fieldsMemory($rsFuncoes, $i);

                if ($oFuncao->o52_funcao == "99") {
                    continue;
                }

                $sLinhaAnexo07 .= $sFilhoNivel2."<funcaoA07>\r\n";
                $sLinhaAnexo07 .= $sFilhoNivel3."<codigoFuncao>".str_pad($oFuncao->o52_funcao, 2, "0", STR_PAD_LEFT)."</codigoFuncao>\r\n";
                $sLinhaAnexo07 .= $sFilhoNivel3."<subfuncoesA07>\r\n";
                $sSqlSubFuncoes = "select distinct o53_subfuncao from orcsubfuncao
                                                   inner join orcdotacao on o53_subfuncao = o58_subfuncao
                                                         where o58_anousu = $this->iAnoUsu
                                                          and o58_funcao = $oFuncao->o52_funcao";
                                                                      //where o58_coddot = $oFuncao->o58_coddot
                                                                        //and o58_anousu = $this->iAnoUsu";
                $rsSubFuncoes = db_query($sSqlSubFuncoes);
                for ($j = 0; $j < pg_num_rows($rsSubFuncoes); $j++) {
                    $oSubFuncao = db_utils::fieldsMemory($rsSubFuncoes, $j);
          
                    $sLinhaAnexo07 .= $sFilhoNivel4."<subfuncaoA07>\r\n";
                    $sLinhaAnexo07 .= $sFilhoNivel5."<codigoSubfuncao>".str_pad($oSubFuncao->o53_subfuncao, 3, "0", STR_PAD_LEFT)."</codigoSubfuncao>\r\n";
                    $sLinhaAnexo07 .= $sFilhoNivel5."<programasA07>\r\n";
          
                    $sSqlProgramas = "select distinct o54_programa, o54_descr from orcprograma 
                                                              inner join orcdotacao on o58_programa = o54_programa
                                                                                   and o58_anousu = o54_anousu
                                                                    where o58_anousu = $this->iAnoUsu
                                                                      and o58_funcao = $oFuncao->o52_funcao
                                                                      and o58_subfuncao = $oSubFuncao->o53_subfuncao";
                                         //where o58_coddot = $oSubFuncao->o58_coddot
                                           //and o58_anousu = $this->iAnoUsu";

                    $rsProgramas = db_query($sSqlProgramas);

                    for ($l = 0; $l < pg_num_rows($rsProgramas); $l++) {
                        $oPrograma = db_utils::fieldsMemory($rsProgramas, $l);

                        $sLinhaAnexo07 .= $sFilhoNivel6."<programaA07>\r\n";
                        $sLinhaAnexo07 .= $sFilhoNivel7."<codigoPrograma>".str_pad($oPrograma->o54_programa, 4, "0", STR_PAD_LEFT)."</codigoPrograma>\r\n";
                        $sLinhaAnexo07 .= $sFilhoNivel7."<nomePrograma>$oPrograma->o54_descr</nomePrograma>\r\n";
            
                        $sSqlValorProjeto = "select coalesce(sum(o58_valor), 0) as valorProjeto from orcprojativ 
                                                                 inner join orcdotacao on o58_anousu = o55_anousu 
                                                                                      and o58_projativ = o55_projativ 
                                                                       where o58_programa = $oPrograma->o54_programa 
                                                                         and o58_funcao = $oFuncao->o52_funcao
                                                                         and o58_subfuncao = $oSubFuncao->o53_subfuncao
                                                                         and o58_anousu = $this->iAnoUsu
                                                                         and substr(o58_projativ, 1, 1) = '1'";
                        $nValorProjeto = db_utils::fieldsMemory(db_query($sSqlValorProjeto), 0)->valorprojeto;
                        $sLinhaAnexo07 .= $sFilhoNivel7."<valorProjeto>".number_format($nValorProjeto, 2, '.', '')."</valorProjeto>\r\n";
            
                        $sSqlValorAtividade = "select coalesce(sum(o58_valor), 0) as valorAtividade from orcprojativ 
                                                                  inner join orcdotacao on o58_anousu = o55_anousu 
                                                                                       and o58_projativ = o55_projativ 
                                                                        where o58_programa = $oPrograma->o54_programa 
                                                                          and o58_funcao = $oFuncao->o52_funcao
                                                                          and o58_subfuncao = $oSubFuncao->o53_subfuncao
                                                                          and o58_anousu = $this->iAnoUsu
                                                                          and substr(o58_projativ, 1, 1) <> '1'";
                        $nValorAtividade = db_utils::fieldsMemory(db_query($sSqlValorAtividade), 0)->valoratividade;
                        $sLinhaAnexo07 .= $sFilhoNivel7."<valorAtividade>".number_format($nValorAtividade, 2, '.', '')."</valorAtividade>\r\n";
                        
                        $sLinhaAnexo07 .= $sFilhoNivel6."</programaA07>\r\n";
                    }
                    $sLinhaAnexo07 .= $sFilhoNivel5."</programasA07>\r\n";
                    $sLinhaAnexo07 .= $sFilhoNivel4."</subfuncaoA07>\r\n";
                }
                $sLinhaAnexo07 .= $sFilhoNivel3."</subfuncoesA07>\r\n";
                $sLinhaAnexo07 .= $sFilhoNivel2."</funcaoA07>\r\n";
            }

            $sLinhaAnexo07 .= $sFilhoNivel1."</funcoesA07>\r\n";
            $sLinhaAnexo07 .= "</anexoDados07>\r\n";
            $sLinhaAnexo07 .= "</anexo07>\r\n";
            fputs($arquivoXML, $sLinhaAnexo07."\r\n");
        }

        if ($this->getGeraAnexo08()) {
            $sLinhaAnexo08  = "<anexo08>\r\n";
            $sLinhaAnexo08 .= "<anexoDados08>\r\n";
            $sLinhaAnexo08 .= $sFilhoNivel1."<funcoesA08>\r\n";

            $sSqlFuncoes = "select distinct o52_funcao from orcfuncao 
                          inner join orcdotacao on o52_funcao = o58_funcao
                              where o58_anousu  = $this->iAnoUsu";
            $rsFuncoes = db_query($sSqlFuncoes);

            for ($i = 0; $i < pg_num_rows($rsFuncoes); $i++) {
                $oFuncao = db_utils::fieldsMemory($rsFuncoes, $i);

                if ($oFuncao->o52_funcao == "99") {
                    continue;
                }

                $sLinhaAnexo08 .= $sFilhoNivel2."<funcaoA08>\r\n";
                $sLinhaAnexo08 .= $sFilhoNivel3."<codigoFuncao>".str_pad($oFuncao->o52_funcao, 2, "0", STR_PAD_LEFT)."</codigoFuncao>\r\n";
                $sLinhaAnexo08 .= $sFilhoNivel3."<subfuncoesA08>\r\n";
                $sSqlSubFuncoes = "select distinct o53_subfuncao from orcsubfuncao
                                                   inner join orcdotacao on o53_subfuncao = o58_subfuncao
                                                         where o58_anousu = $this->iAnoUsu
                                                          and o58_funcao = $oFuncao->o52_funcao";
                $rsSubFuncoes = db_query($sSqlSubFuncoes);
                for ($j = 0; $j < pg_num_rows($rsSubFuncoes); $j++) {
                    $oSubFuncao = db_utils::fieldsMemory($rsSubFuncoes, $j);
          
                    $sLinhaAnexo08 .= $sFilhoNivel4."<subfuncaoA08>\r\n";
                    $sLinhaAnexo08 .= $sFilhoNivel5."<codigoSubfuncao>".str_pad($oSubFuncao->o53_subfuncao, 3, "0", STR_PAD_LEFT)."</codigoSubfuncao>\r\n";
                    $sLinhaAnexo08 .= $sFilhoNivel5."<programasA08>\r\n";
          
                    $sSqlProgramas = "select o54_programa, o54_descr, o58_codigo, sum(o58_valor) as o58_valor from orcprograma 
                                       inner join orcdotacao on o58_programa = o54_programa
                                                and o58_anousu = o54_anousu
                                         where o58_anousu = $this->iAnoUsu
                                           and o58_funcao = $oFuncao->o52_funcao
                                           and o58_subfuncao = $oSubFuncao->o53_subfuncao
                                         group by o54_programa, o58_codigo, o54_descr";
                    $rsProgramas = db_query($sSqlProgramas);

                    for ($l = 0; $l < pg_num_rows($rsProgramas); $l++) {
                        $oPrograma = db_utils::fieldsMemory($rsProgramas, $l);

                        $sLinhaAnexo08 .= $sFilhoNivel6."<programaA08>\r\n";
                        $sLinhaAnexo08 .= $sFilhoNivel7."<codigoPrograma>".str_pad($oPrograma->o54_programa, 4, "0", STR_PAD_LEFT)."</codigoPrograma>\r\n";
                        $sLinhaAnexo08 .= $sFilhoNivel7."<nomePrograma>$oPrograma->o54_descr</nomePrograma>\r\n";
                        if ($oPrograma->o58_codigo == "111") {
                            $sLinhaAnexo08 .= $sFilhoNivel7."<valorOrdinario>".number_format($oPrograma->o58_valor, 2, '.', '')."</valorOrdinario>\r\n";
                            $sLinhaAnexo08 .= $sFilhoNivel7."<valorVinculado>0.00</valorVinculado>\r\n";
                        } else {
                            $sLinhaAnexo08 .= $sFilhoNivel7."<valorOrdinario>0.00</valorOrdinario>\r\n";
                            $sLinhaAnexo08 .= $sFilhoNivel7."<valorVinculado>".number_format($oPrograma->o58_valor, 2, '.', '')."</valorVinculado>\r\n";
                        }
            
                        $sLinhaAnexo08 .= $sFilhoNivel6."</programaA08>\r\n";
                    }
                    $sLinhaAnexo08 .= $sFilhoNivel5."</programasA08>\r\n";
                    $sLinhaAnexo08 .= $sFilhoNivel4."</subfuncaoA08>\r\n";
                }
                $sLinhaAnexo08 .= $sFilhoNivel3."</subfuncoesA08>\r\n";
                $sLinhaAnexo08 .= $sFilhoNivel2."</funcaoA08>\r\n";
            }

            $sLinhaAnexo08 .= $sFilhoNivel1."</funcoesA08>\r\n";
            $sLinhaAnexo08 .= "</anexoDados08>\r\n";
            $sLinhaAnexo08 .= "</anexo08>\r\n";
            fputs($arquivoXML, $sLinhaAnexo08."\r\n");
        }

        if ($this->getGeraAnexo09()) {
            $sLinhaAnexo09  = "<anexo09>\r\n";
            $sLinhaAnexo09 .= "<anexoDados09>\r\n";
            $sLinhaAnexo09 .= $sFilhoNivel1."<orgaosA09>\r\n";

            $sSqlOrgaos = "select distinct o40_orgao, o40_descr from orcorgao 
                              inner join orcdotacao on o58_orgao   = o40_orgao
                                                                     and o58_anousu  = o40_anousu
                                                                     and o58_instit  = o40_instit
                                                          where o58_anousu = $this->iAnoUsu";
            $rsOrgaos = db_query($sSqlOrgaos);

            for ($i = 0; $i < pg_num_rows($rsOrgaos); $i++) {
                $oOrgao = db_utils::fieldsMemory($rsOrgaos, $i);
        
                $sLinhaAnexo09 .= $sFilhoNivel2."<orgaoA09>\r\n";
                $sLinhaAnexo09 .= $sFilhoNivel2."<codigoOrgao>$oOrgao->o40_orgao</codigoOrgao>\r\n";
                $sLinhaAnexo09 .= $sFilhoNivel2."<nomeOrgao>$oOrgao->o40_descr</nomeOrgao>\r\n";
                $sLinhaAnexo09 .= $sFilhoNivel2."<unidadesA09>\r\n";

                $sSqlUnidades = "select distinct o41_unidade, o41_descr, o41_instit from orcunidade
                                                                    inner join orcdotacao on o58_unidade = o41_unidade
                                                                                           and o58_orgao   = o41_orgao
                                                                                           and o58_anousu  = o41_anousu
                                                                                           and o58_instit  = o41_instit
                                                                            where o58_anousu = $this->iAnoUsu
                                                                              and o58_orgao  = $oOrgao->o40_orgao";
                $rsUnidades = db_query($sSqlUnidades);

                for ($j = 0; $j < pg_num_rows($rsUnidades); $j++) {
                    $sLinhaAnexo09FuncaoLegislativa = "";
                    $sLinhaAnexo09FuncaoAdministracao = "";
                    $sLinhaAnexo09FuncaoSegurancaPub = "";
                    $sLinhaAnexo09FuncaoAssistenciaSocial = "";
                    $sLinhaAnexo09FuncaoPrevidencia = "";
                    $sLinhaAnexo09FuncaoSaude = "";
                    $sLinhaAnexo09FuncaoTrabalho = "";
                    $sLinhaAnexo09FuncaoEducacao = "";
                    $sLinhaAnexo09FuncaoCultura = "";
                    $sLinhaAnexo09FuncaoDireitos = "";
                    $sLinhaAnexo09FuncaoUrbanismo = "";
                    $sLinhaAnexo09FuncaoHabitacao = "";
                    $sLinhaAnexo09FuncaoSaneamento = "";
                    $sLinhaAnexo09FuncaoGestaoAmbiental = "";
                    $sLinhaAnexo09FuncaoCienciaTec = "";
                    $sLinhaAnexo09FuncaoAgricultura = "";
                    $sLinhaAnexo09FuncaoIndustria = "";
                    $sLinhaAnexo09FuncaoComercio = "";
                    $sLinhaAnexo09FuncaoComunicacoes = "";
                    $sLinhaAnexo09FuncaoEnergia = "";
                    $sLinhaAnexo09FuncaoTransporte = "";
                    $sLinhaAnexo09FuncaoDesporto = "";
                    $sLinhaAnexo09FuncaoEncargos = "";

                    $oUnidade = db_utils::fieldsMemory($rsUnidades, $j);

                    $sLinhaAnexo09 .= $sFilhoNivel3."<unidadeA09>\r\n";
                    $sLinhaAnexo09 .= $sFilhoNivel4."<codigoUnidadeGestora>$oUnidade->o41_unidade</codigoUnidadeGestora>\r\n";
                    $sLinhaAnexo09 .= $sFilhoNivel4."<nomeUnidadeGestora>$oUnidade->o41_descr</nomeUnidadeGestora>\r\n";
                    $sSqlFuncoes = "select o52_funcao
                  from orcfuncao 
                  where o52_funcao in (1,4,6,8,9,10,11,12,13,14,15,16,17,18,19,20,22,23,24,25,26,27,28)";
                    $rsFuncoes = db_query($sSqlFuncoes);

                    for ($k = 0; $k < pg_num_rows($rsFuncoes); $k++) {
                        $oFuncao = db_utils::fieldsMemory($rsFuncoes, $k);
                        $nValorFuncao = db_utils::fieldsMemory(db_query("select coalesce(sum(o58_valor), 0) as valor from orcfuncao 
                                                                                    left join orcdotacao on o52_funcao  = o58_funcao                                                                
                                                                                                      and o58_orgao   = $oOrgao->o40_orgao
                                                                                                      and o58_unidade = $oUnidade->o41_unidade
                                                                                                      and o58_anousu  = $this->iAnoUsu
                                                                                                      and o58_instit  = $oUnidade->o41_instit
                                                                                                      and o58_orgao   = $oOrgao->o40_orgao
                                                                                                      and o58_unidade = $oUnidade->o41_unidade
                                                                                   where o52_funcao = $oFuncao->o52_funcao
                                                                                   group by o52_funcao"), 0)->valor;
                        if ($oFuncao->o52_funcao == 1) {
                                $sLinhaAnexo09FuncaoLegislativa .= $sFilhoNivel4."<valorFuncaoLegislativa>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoLegislativa>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 4) {
                              $sLinhaAnexo09FuncaoAdministracao .= $sFilhoNivel4."<valorFuncaoAdministracao>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoAdministracao>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 6) {
                            $sLinhaAnexo09FuncaoSegurancaPub .= $sFilhoNivel4."<valorFuncaoSegurancaPublica>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoSegurancaPublica>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 8) {
                            $sLinhaAnexo09FuncaoAssistenciaSocial .= $sFilhoNivel4."<valorFuncaoAssistenciaSocial>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoAssistenciaSocial>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 9) {
                            $sLinhaAnexo09FuncaoPrevidencia .= $sFilhoNivel4."<valorFuncaoPrevidenciaSocial>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoPrevidenciaSocial>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 10) {
                            $sLinhaAnexo09FuncaoSaude .= $sFilhoNivel4."<valorFuncaoSaude>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoSaude>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 11) {
                            $sLinhaAnexo09FuncaoTrabalho .= $sFilhoNivel4."<valorFuncaoTrabalho>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoTrabalho>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 12) {
                            $sLinhaAnexo09FuncaoEducacao .= $sFilhoNivel4."<valorFuncaoEducacao>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoEducacao>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 13) {
                            $sLinhaAnexo09FuncaoCultura .= $sFilhoNivel4."<valorFuncaoCultura>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoCultura>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 14) {
                            $sLinhaAnexo09FuncaoDireitos .= $sFilhoNivel4."<valorFuncaoDireitosCidadania>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoDireitosCidadania>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 15) {
                            $sLinhaAnexo09FuncaoUrbanismo .= $sFilhoNivel4."<valorFuncaoUrbanismo>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoUrbanismo>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 16) {
                            $sLinhaAnexo09FuncaoHabitacao .= $sFilhoNivel4."<valorFuncaoHabitacao>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoHabitacao>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 17) {
                            $sLinhaAnexo09FuncaoSaneamento .= $sFilhoNivel4."<valorFuncaoSaneamento>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoSaneamento>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 18) {
                            $sLinhaAnexo09FuncaoGestaoAmbiental .= $sFilhoNivel4."<valorFuncaoGestaoAmbiental>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoGestaoAmbiental>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 19) {
                            $sLinhaAnexo09FuncaoCienciaTec .= $sFilhoNivel4."<valorFuncaoCienciaTecnologia>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoCienciaTecnologia>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 20) {
                            $sLinhaAnexo09FuncaoAgricultura .= $sFilhoNivel4."<valorFuncaoAgricultura>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoAgricultura>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 22) {
                            $sLinhaAnexo09FuncaoIndustria .= $sFilhoNivel4."<valorFuncaoIndustria>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoIndustria>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 23) {
                            $sLinhaAnexo09FuncaoComercio .= $sFilhoNivel4."<valorFuncaoComercioServicos>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoComercioServicos>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 24) {
                            $sLinhaAnexo09FuncaoComunicacoes .= $sFilhoNivel4."<valorFuncaoComunicacoes>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoComunicacoes>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 25) {
                            $sLinhaAnexo09FuncaoEnergia .= $sFilhoNivel4."<valorFuncaoEnergia>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoEnergia>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 26) {
                            $sLinhaAnexo09FuncaoTransporte .= $sFilhoNivel4."<valorFuncaoTransporte>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoTransporte>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 27) {
                            $sLinhaAnexo09FuncaoDesporto .= $sFilhoNivel4."<valorFuncaoDesportoLazer>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoDesportoLazer>\r\n";
                        }
                        if ($oFuncao->o52_funcao == 28) {
                            $sLinhaAnexo09FuncaoEncargos .= $sFilhoNivel4."<valorFuncaoEncargosEspeciais>".number_format($nValorFuncao, 2, '.', '')."</valorFuncaoEncargosEspeciais>\r\n";
                        }
                    }
                    $sLinhaAnexo09 .= $sLinhaAnexo09FuncaoLegislativa
                             .$sLinhaAnexo09FuncaoAdministracao
                             .$sLinhaAnexo09FuncaoSegurancaPub
                             .$sLinhaAnexo09FuncaoAssistenciaSocial
                             .$sLinhaAnexo09FuncaoPrevidencia
                             .$sLinhaAnexo09FuncaoSaude
                             .$sLinhaAnexo09FuncaoTrabalho
                             .$sLinhaAnexo09FuncaoEducacao
                             .$sLinhaAnexo09FuncaoCultura
                             .$sLinhaAnexo09FuncaoDireitos
                             .$sLinhaAnexo09FuncaoUrbanismo
                             .$sLinhaAnexo09FuncaoHabitacao
                             .$sLinhaAnexo09FuncaoSaneamento
                             .$sLinhaAnexo09FuncaoGestaoAmbiental
                             .$sLinhaAnexo09FuncaoCienciaTec
                             .$sLinhaAnexo09FuncaoAgricultura
                             .$sLinhaAnexo09FuncaoIndustria
                             .$sLinhaAnexo09FuncaoComercio
                             .$sLinhaAnexo09FuncaoComunicacoes
                             .$sLinhaAnexo09FuncaoEnergia
                             .$sLinhaAnexo09FuncaoTransporte
                             .$sLinhaAnexo09FuncaoDesporto
                             .$sLinhaAnexo09FuncaoEncargos;

                    $sLinhaAnexo09 .= $sFilhoNivel3."</unidadeA09>\r\n";
                }
                $sLinhaAnexo09 .= $sFilhoNivel2."</unidadesA09>\r\n";
                $sLinhaAnexo09 .= $sFilhoNivel2."</orgaoA09>\r\n";
            }
            $sLinhaAnexo09 .= $sFilhoNivel1."</orgaosA09>\r\n";
            $sLinhaAnexo09 .= "</anexoDados09>\r\n";
            $sLinhaAnexo09 .= "</anexo09>\r\n";
            fputs($arquivoXML, $sLinhaAnexo09."\r\n");
        }

        if ($this->getGeraAnexo10()) {
            $sLinhaAnexo10  = "<anexo10>\r\n";
            $sLinhaAnexo10 .= "<anexoDados10>\r\n";
            $sLinhaAnexo10 .= $sFilhoNivel1."<receitasA10>\r\n";

            $result1 = db_receitasaldo(11, 1, 3, true, "", 2016, '2016-01-01', '2016-12-31', false);

            for ($x1 = 0; $x1 < pg_numrows($result1); $x1++) {
                $oReceita = db_utils::fieldsMemory($result1, $x1);
                $sLinhaAnexo10 .= $sFilhoNivel2."<receitaA10>\r\n";
                $sLinhaAnexo10 .= $sFilhoNivel3."<codigoReceita>".substr($oReceita->o57_fonte, 0, 10)."</codigoReceita>\r\n";
                $sLinhaAnexo10 .= $sFilhoNivel3."<valorOrcado>".number_format(abs($oReceita->saldo_inicial), 2, '.', '')."</valorOrcado>\r\n";
                $sLinhaAnexo10 .= $sFilhoNivel3."<valorArrecadado>".number_format(abs($oReceita->saldo_arrecadado), 2, '.', '')."</valorArrecadado>\r\n";
                $sLinhaAnexo10 .= $sFilhoNivel2."</receitaA10>\r\n";
            }

            $sLinhaAnexo10 .= $sFilhoNivel1."</receitasA10>\r\n";
            $sLinhaAnexo10 .= "</anexoDados10>\r\n";
            $sLinhaAnexo10 .= "</anexo10>\r\n";
            fputs($arquivoXML, $sLinhaAnexo10."\r\n");
        }

        if ($this->getGeraAnexo11()) {
            $sqlUnidade = "
                select distinct
                        o41_orgao as orgao, 
                        o41_unidade as unidade, 
                        o41_descr as descr_unidade 
                from orcunidade
          inner join orcdotacao on o41_orgao = o58_orgao
                               and o41_unidade = o58_unidade
                               and o41_anousu = o58_anousu 
                where o41_anousu = 2016
                order by o41_orgao, 
                         o41_unidade, 
                         o41_descr";
            $rsUnidade = db_query($sqlUnidade);

            $sLinhaAnexo11  = "<anexo11>\r\n";
            $sLinhaAnexo11 .= "<anexoDados11>\r\n";
            $sLinhaAnexo11 .= "<unidadesDespesasA11>\r\n";

            for ($i = 0; $i < pg_numrows($rsUnidade); $i++) {
                $oUnidade = db_utils::fieldsMemory($rsUnidade, $i);
         
                $sLinhaAnexo11 .= $sFilhoNivel1."<unidadeDespesasA11>\r\n";
                $sLinhaAnexo11 .= $sFilhoNivel2."<codigoUnidadeOcamentaria>".str_pad($oUnidade->orgao, 2, '0', STR_PAD_LEFT).".".str_pad($oUnidade->unidade, 2, '0', STR_PAD_LEFT)."</codigoUnidadeOcamentaria>\r\n";
                $sLinhaAnexo11 .= $sFilhoNivel2."<nomeUnidadeOcamentaria>$oUnidade->descr_unidade</nomeUnidadeOcamentaria>\r\n";
                $sLinhaAnexo11 .= $sFilhoNivel2."<despesasA11>\r\n";

                $sql_func = "
                     select 
                 distinct o58_funcao as funcao, 
                          o58_subfuncao as subfuncao, 
                          o58_programa as programa, 
                          o58_projativ as acao, 
                          o55_descr as descr_acao 
                     from 
                          orcdotacao 
                          inner join orcprojativ on o58_projativ = o55_projativ and o58_anousu = o55_anousu 
                     where 
                          o58_anousu = 2016 and o58_orgao = $oUnidade->orgao and o58_unidade = $oUnidade->unidade
                     order by
                          o58_subfuncao,o58_programa,o58_projativ,o55_descr
                     ";
                $rsFunc = db_query($sql_func);

                for ($w = 0; $w < pg_numrows($rsFunc); $w++) {
                    $oDotacao = db_utils::fieldsMemory($rsFunc, $w);

                    $sele_work = "o58_orgao = $oUnidade->orgao and 
                       o58_unidade = $oUnidade->unidade and 
                       o58_funcao = $oDotacao->funcao and 
                       o58_subfuncao = $oDotacao->subfuncao and
                       o58_programa = $oDotacao->programa and
                       o58_projativ = $oDotacao->acao";

                    $rsDadosDespesa = db_dotacaosaldo(8, 1, 2, true, $sele_work, 2016, '2016-01-01', '2016-12-31');

                    for ($j = 0; $j < pg_numrows($rsDadosDespesa); $j++) {
                        $oDespesa = db_utils::fieldsMemory($rsDadosDespesa, $j);

                        $total_suplem = ($oDespesa->dot_ini + $oDespesa->suplementado_acumulado - $oDespesa->reduzido_acumulado);
                        $total_esp = $oDespesa->especial_acumulado;
                        $total_realizada = $oDespesa->empenhado - $oDespesa->anulado;

                        if ($oDespesa->o58_codigo != 0 && ($total_suplem + $total_esp + $total_realizada) > 0) {
                             $sLinhaAnexo11 .= $sFilhoNivel3."<despesaA11>\r\n";
                             $sLinhaAnexo11 .= $sFilhoNivel4."<codigoDespesa>".substr($oDespesa->o58_elemento, 1, 8)."</codigoDespesa>\r\n";
                             $sLinhaAnexo11 .= $sFilhoNivel4."<valorCreditosOrcamentariosSuplementares>".$total_suplem."</valorCreditosOrcamentariosSuplementares>\r\n";
                             $sLinhaAnexo11 .= $sFilhoNivel4."<valorCreditosEspeciaisExtraordinarios>".$total_esp."</valorCreditosEspeciaisExtraordinarios>\r\n";
                             $sLinhaAnexo11 .= $sFilhoNivel4."<valorRealizada>".$total_realizada."</valorRealizada>\r\n";
                             $sLinhaAnexo11 .= $sFilhoNivel3."</despesaA11>\r\n";
                        }
                    }
                }
                $sLinhaAnexo11 .= $sFilhoNivel2."</despesasA11>\r\n";
                $sLinhaAnexo11 .= $sFilhoNivel1."</unidadeDespesasA11>\r\n";
            }
            $sLinhaAnexo11 .= "</unidadesDespesasA11>\r\n";
            $sLinhaAnexo11 .= "</anexoDados11>\r\n";
            $sLinhaAnexo11 .= "</anexo11>\r\n";

            fputs($arquivoXML, $sLinhaAnexo11."\r\n");
        }

        if ($this->getGeraAnexo12()) {
            $sLinhaAnexo12 = "<anexo12>
<anexoDados12>
   <blocoAReceitasA12>
     <valorReceitaTributariaPI>614477000.00</valorReceitaTributariaPI>
     <valorReceitaTributariaPA>614477000.00</valorReceitaTributariaPA>
     <valorReceitaTributariaRR>143584988.29</valorReceitaTributariaRR>
     <valorReceitaContribuicaoPI>202178000.00</valorReceitaContribuicaoPI>
     <valorReceitaContribuicaoPA>202178000.00</valorReceitaContribuicaoPA>
     <valorReceitaContribuicaoRR>14594243.24</valorReceitaContribuicaoRR>
     <valorReceitaPatrimonialPI>62517000.00</valorReceitaPatrimonialPI>
     <valorReceitaPatrimonialPA>62517000.00</valorReceitaPatrimonialPA>
     <valorReceitaPatrimonialRR>11172951.20</valorReceitaPatrimonialRR>
     <valorReceitaAgropecuariaPI>0.00</valorReceitaAgropecuariaPI>
     <valorReceitaAgropecuariaPA>0.00</valorReceitaAgropecuariaPA>
     <valorReceitaAgropecuariaRR>0.00</valorReceitaAgropecuariaRR>
     <valorReceitaIndustrialPI>0.00</valorReceitaIndustrialPI>
     <valorReceitaIndustrialPA>0.00</valorReceitaIndustrialPA>
     <valorReceitaIndustrialRR>0.00</valorReceitaIndustrialRR>
     <valorReceitaServicosPI>10318000.00</valorReceitaServicosPI>
     <valorReceitaServicosPA>10318000.00</valorReceitaServicosPA>
     <valorReceitaServicosRR>10255.32</valorReceitaServicosRR>
     <valorTransfereciasCorrentesPI>1355921000.00</valorTransfereciasCorrentesPI>
     <valorTransfereciasCorrentesPA>1357609385.67</valorTransfereciasCorrentesPA>
     <valorTransfereciasCorrentesRR>187386824.95</valorTransfereciasCorrentesRR>
     <valorOutrasReceitasCorrentesPI>207819570.79</valorOutrasReceitasCorrentesPI>
     <valorOutrasReceitasCorrentesPA>207819570.79</valorOutrasReceitasCorrentesPA>
     <valorOutrasReceitasCorrentesRR>23476620.32</valorOutrasReceitasCorrentesRR>
     <valorReceitaOperacaoCreditoPI>11051000.00</valorReceitaOperacaoCreditoPI>
     <valorReceitaOperacaoCreditoPA>11051000.00</valorReceitaOperacaoCreditoPA>
     <valorReceitaOperacaoCreditoRR>0.00</valorReceitaOperacaoCreditoRR>
     <valorReceitaAlienacaoBensPI>2000.00</valorReceitaAlienacaoBensPI>
     <valorReceitaAlienacaoBensPA>2000.00</valorReceitaAlienacaoBensPA>
     <valorReceitaAlienacaoBensRR>0.00</valorReceitaAlienacaoBensRR>
     <valorReceitaAmortizacoesEmprestimosPI>0.00</valorReceitaAmortizacoesEmprestimosPI>
     <valorReceitaAmortizacoesEmprestimosPA>0.00</valorReceitaAmortizacoesEmprestimosPA>
     <valorReceitaAmortizacoesEmprestimosRR>0.00</valorReceitaAmortizacoesEmprestimosRR>
     <valorTransfereciasCapitalPI>6619000.00</valorTransfereciasCapitalPI>
     <valorTransfereciasCapitalPA>6619000.00</valorTransfereciasCapitalPA>
     <valorTransfereciasCapitalRR>0.00</valorTransfereciasCapitalRR>
     <valorOutrasReceitasCapitalPI>3000000.00</valorOutrasReceitasCapitalPI>
     <valorOutrasReceitasCapitalPA>3000000.00</valorOutrasReceitasCapitalPA>
     <valorOutrasReceitasCapitalRR>0.00</valorOutrasReceitasCapitalRR>
     <valorArrecadadosExerciciosAnterioresPI>0.00</valorArrecadadosExerciciosAnterioresPI>
     <valorArrecadadosExerciciosAnterioresPA>0.00</valorArrecadadosExerciciosAnterioresPA>
     <valorArrecadadosExerciciosAnterioresRR>0.00</valorArrecadadosExerciciosAnterioresRR>
     <valorOperacoesCreditoInternasMobiliariaPI>0.00</valorOperacoesCreditoInternasMobiliariaPI>
     <valorOperacoesCreditoInternasMobiliariaPA>0.00</valorOperacoesCreditoInternasMobiliariaPA>
     <valorOperacoesCreditoInternasMobiliariaRR>0.00</valorOperacoesCreditoInternasMobiliariaRR>
     <valorOperacoesCreditoInternasContratualPI>0.00</valorOperacoesCreditoInternasContratualPI>
     <valorOperacoesCreditoInternasContratualPA>0.00</valorOperacoesCreditoInternasContratualPA>
     <valorOperacoesCreditoInternasContratualRR>0.00</valorOperacoesCreditoInternasContratualRR>
     <valorOperacoesCreditoExternasMobiliariaPI>0.00</valorOperacoesCreditoExternasMobiliariaPI>
     <valorOperacoesCreditoExternasMobiliariaPA>0.00</valorOperacoesCreditoExternasMobiliariaPA>
     <valorOperacoesCreditoExternasMobiliariaRR>0.00</valorOperacoesCreditoExternasMobiliariaRR>
     <valorOperacoesCreditoExternasContratualPI>0.00</valorOperacoesCreditoExternasContratualPI>
     <valorOperacoesCreditoExternasContratualPA>0.00</valorOperacoesCreditoExternasContratualPA>
     <valorOperacoesCreditoExternasContratualRR>0.00</valorOperacoesCreditoExternasContratualRR>
     <valorSuperavitFinanceiroPA>0.00</valorSuperavitFinanceiroPA>
     <valorSuperavitFinanceiroRR>0.00</valorSuperavitFinanceiroRR>
     <valorreaberturaCreditosAdicionaisPA>0.00</valorreaberturaCreditosAdicionaisPA>
     <valorreaberturaCreditosAdicionaisRR>0.00</valorreaberturaCreditosAdicionaisRR>
   </blocoAReceitasA12>
   <blocoADespesasA12>
     <valorPessoalEncargosSociaisDI>1242479734.00</valorPessoalEncargosSociaisDI>
     <valorPessoalEncargosSociaisDA>1242479734.00</valorPessoalEncargosSociaisDA>
     <valorPessoalEncargosSociaisDE>1098906498.26</valorPessoalEncargosSociaisDE>
     <valorPessoalEncargosSociaisDL>236992163.87</valorPessoalEncargosSociaisDL>
     <valorPessoalEncargosSociaisDP>162420484.88</valorPessoalEncargosSociaisDP>
     <valorJurosEncargosDividaDI>24526000.00</valorJurosEncargosDividaDI>
     <valorJurosEncargosDividaDA>24526000.00</valorJurosEncargosDividaDA>
     <valorJurosEncargosDividaDE>22742576.00</valorJurosEncargosDividaDE>
     <valorJurosEncargosDividaDL>323212.73</valorJurosEncargosDividaDL>
     <valorJurosEncargosDividaDP>0.00</valorJurosEncargosDividaDP>
     <valorOutrasDespesasCorrentesDI>936633213.38</valorOutrasDespesasCorrentesDI>
     <valorOutrasDespesasCorrentesDA>936080852.97</valorOutrasDespesasCorrentesDA>
     <valorOutrasDespesasCorrentesDE>618299009.08</valorOutrasDespesasCorrentesDE>
     <valorOutrasDespesasCorrentesDL>89350075.27</valorOutrasDespesasCorrentesDL>
     <valorOutrasDespesasCorrentesDP>47310823.31</valorOutrasDespesasCorrentesDP>
     <valorInvestimentosDI>246596623.41</valorInvestimentosDI>
     <valorInvestimentosDA>248837369.49</valorInvestimentosDA>
     <valorInvestimentosDE>42173853.53</valorInvestimentosDE>
     <valorInvestimentosDL>3471236.87</valorInvestimentosDL>
     <valorInvestimentosDP>1556120.70</valorInvestimentosDP>
     <valorInversoesFinanceirasDI>3000.00</valorInversoesFinanceirasDI>
     <valorInversoesFinanceirasDA>3000.00</valorInversoesFinanceirasDA>
     <valorInversoesFinanceirasDE>0.00</valorInversoesFinanceirasDE>
     <valorInversoesFinanceirasDL>0.00</valorInversoesFinanceirasDL>
     <valorInversoesFinanceirasDP>0.00</valorInversoesFinanceirasDP>
     <valorAmortizacaoDividaDI>20164000.00</valorAmortizacaoDividaDI>
     <valorAmortizacaoDividaDA>20164000.00</valorAmortizacaoDividaDA>
     <valorAmortizacaoDividaDE>19855910.00</valorAmortizacaoDividaDE>
     <valorAmortizacaoDividaDL>1242535.28</valorAmortizacaoDividaDL>
     <valorAmortizacaoDividaDP>0.00</valorAmortizacaoDividaDP>
     <valorReservaContingenciaDI>2000000.00</valorReservaContingenciaDI>
     <valorReservaContingenciaDA>2000000.00</valorReservaContingenciaDA>
     <valorReservaContingenciaDE>0.00</valorReservaContingenciaDE>
     <valorReservaContingenciaDL>0.00</valorReservaContingenciaDL>
     <valorReservaContingenciaDP>0.00</valorReservaContingenciaDP>
     <valorReservaRPPSDI>0.00</valorReservaRPPSDI>
     <valorReservaRPPSDA>0.00</valorReservaRPPSDA>
     <valorReservaRPPSDE>0.00</valorReservaRPPSDE>
     <valorReservaRPPSDL>0.00</valorReservaRPPSDL>
     <valorReservaRPPSDP>0.00</valorReservaRPPSDP>
     <valorAmortizacaoDividaInternaMobiliariaDI>0.00</valorAmortizacaoDividaInternaMobiliariaDI>
     <valorAmortizacaoDividaInternaMobiliariaDA>0.00</valorAmortizacaoDividaInternaMobiliariaDA>
     <valorAmortizacaoDividaInternaMobiliariaDE>0.00</valorAmortizacaoDividaInternaMobiliariaDE>
     <valorAmortizacaoDividaInternaMobiliariaDL>0.00</valorAmortizacaoDividaInternaMobiliariaDL>
     <valorAmortizacaoDividaInternaMobiliariaDP>0.00</valorAmortizacaoDividaInternaMobiliariaDP>
     <valorAmortizacaoDividaInternaOutrasDividasDI>1500000.00</valorAmortizacaoDividaInternaOutrasDividasDI>
     <valorAmortizacaoDividaInternaOutrasDividasDA>1500000.00</valorAmortizacaoDividaInternaOutrasDividasDA>
     <valorAmortizacaoDividaInternaOutrasDividasDE>233000.00</valorAmortizacaoDividaInternaOutrasDividasDE>
     <valorAmortizacaoDividaInternaOutrasDividasDL>0.00</valorAmortizacaoDividaInternaOutrasDividasDL>
     <valorAmortizacaoDividaInternaOutrasDividasDP>0.00</valorAmortizacaoDividaInternaOutrasDividasDP>
     <valorAmortizacaoDividaExternaMobiliariaDI>0.00</valorAmortizacaoDividaExternaMobiliariaDI>
     <valorAmortizacaoDividaExternaMobiliariaDA>0.00</valorAmortizacaoDividaExternaMobiliariaDA>
     <valorAmortizacaoDividaExternaMobiliariaDE>0.00</valorAmortizacaoDividaExternaMobiliariaDE>
     <valorAmortizacaoDividaExternaMobiliariaDL>0.00</valorAmortizacaoDividaExternaMobiliariaDL>
     <valorAmortizacaoDividaExternaMobiliariaDP>0.00</valorAmortizacaoDividaExternaMobiliariaDP>
     <valorAmortizacaoDividaExternaOutrasDividasDI>0.00</valorAmortizacaoDividaExternaOutrasDividasDI>
     <valorAmortizacaoDividaExternaOutrasDividasDA>0.00</valorAmortizacaoDividaExternaOutrasDividasDA>
     <valorAmortizacaoDividaExternaOutrasDividasDE>0.00</valorAmortizacaoDividaExternaOutrasDividasDE>
     <valorAmortizacaoDividaExternaOutrasDividasDL>0.00</valorAmortizacaoDividaExternaOutrasDividasDL>
     <valorAmortizacaoDividaExternaOutrasDividasDP>0.00</valorAmortizacaoDividaExternaOutrasDividasDP>
   </blocoADespesasA12>
   <blocoBA12>
     <valorPessoalEncargosSociaisNPIEA>2922723.84</valorPessoalEncargosSociaisNPIEA>
     <valorPessoalEncargosSociaisNPIDEA>4906222.62</valorPessoalEncargosSociaisNPIDEA>
     <valorPessoalEncargosSociaisNPL>496186.99</valorPessoalEncargosSociaisNPL>
     <valorPessoalEncargosSociaisNPP>9356.54</valorPessoalEncargosSociaisNPP>
     <valorPessoalEncargosSociaisNPC>402176.59</valorPessoalEncargosSociaisNPC>
     <valorJurosEncargosDividaNPIEA>0.00</valorJurosEncargosDividaNPIEA>
     <valorJurosEncargosDividaNPIDEA>1663400.46</valorJurosEncargosDividaNPIDEA>
     <valorJurosEncargosDividaNPL>0.00</valorJurosEncargosDividaNPL>
     <valorJurosEncargosDividaNPP>0.00</valorJurosEncargosDividaNPP>
     <valorJurosEncargosDividaNPC>0.00</valorJurosEncargosDividaNPC>
     <valorOutrasDespesasCorrentesNPIEA>30323121.64</valorOutrasDespesasCorrentesNPIEA>
     <valorOutrasDespesasCorrentesNPIDEA>65300218.06</valorOutrasDespesasCorrentesNPIDEA>
     <valorOutrasDespesasCorrentesNPL>46348429.03</valorOutrasDespesasCorrentesNPL>
     <valorOutrasDespesasCorrentesNPP>27847164.78</valorOutrasDespesasCorrentesNPP>
     <valorOutrasDespesasCorrentesNPC>2905992.08</valorOutrasDespesasCorrentesNPC>
     <valorInvestimentosNPIEA>155629694.27</valorInvestimentosNPIEA>
     <valorInvestimentosNPIDEA>48404982.79</valorInvestimentosNPIDEA>
     <valorInvestimentosNPL>8694502.63</valorInvestimentosNPL>
     <valorInvestimentosNPP>3792214.71</valorInvestimentosNPP>
     <valorInvestimentosNPC>1262683.39</valorInvestimentosNPC>
     <valorInversoesFinanceirasNPIEA>0.00</valorInversoesFinanceirasNPIEA>
     <valorInversoesFinanceirasNPIDEA>0.00</valorInversoesFinanceirasNPIDEA>
     <valorInversoesFinanceirasNPL>0.00</valorInversoesFinanceirasNPL>
     <valorInversoesFinanceirasNPP>0.00</valorInversoesFinanceirasNPP>
     <valorInversoesFinanceirasNPC>0.00</valorInversoesFinanceirasNPC>
     <valorAmortizacaoDividaNPIEA>0.00</valorAmortizacaoDividaNPIEA>
     <valorAmortizacaoDividaNPIDEA>0.00</valorAmortizacaoDividaNPIDEA>
     <valorAmortizacaoDividaNPL>0.00</valorAmortizacaoDividaNPL>
     <valorAmortizacaoDividaNPP>0.00</valorAmortizacaoDividaNPP>
     <valorAmortizacaoDividaNPC>0.00</valorAmortizacaoDividaNPC>
   </blocoBA12>
   <blocoCA12>
     <valorPessoalEncargosSociaisPIEA>139018742.44</valorPessoalEncargosSociaisPIEA>
     <valorPessoalEncargosSociaisPIDEA>119586684.47</valorPessoalEncargosSociaisPIDEA>
     <valorPessoalEncargosSociaisPP>6769886.71</valorPessoalEncargosSociaisPP>
     <valorPessoalEncargosSociaisPC>1377921.16</valorPessoalEncargosSociaisPC>
     <valorJurosEncargosDividaPIEA>241.42</valorJurosEncargosDividaPIEA>
     <valorJurosEncargosDividaPIDEA>0.00</valorJurosEncargosDividaPIDEA>
     <valorJurosEncargosDividaPP>0.00</valorJurosEncargosDividaPP>
     <valorJurosEncargosDividaPC>0.00</valorJurosEncargosDividaPC>
     <valorOutrasDespesasCorrentesPIEA>55823240.64</valorOutrasDespesasCorrentesPIEA>
     <valorOutrasDespesasCorrentesPIDEA>106525496.23</valorOutrasDespesasCorrentesPIDEA>
     <valorOutrasDespesasCorrentesPP>56458512.19</valorOutrasDespesasCorrentesPP>
     <valorOutrasDespesasCorrentesPC>719983.78</valorOutrasDespesasCorrentesPC>
     <valorInvestimentosPIEA>26511101.85</valorInvestimentosPIEA>
     <valorInvestimentosPIDEA>6313123.94</valorInvestimentosPIDEA>
     <valorInvestimentosPP>7114025.90</valorInvestimentosPP>
     <valorInvestimentosPC>127721.37</valorInvestimentosPC>
     <valorInversoesFinanceirasPIEA>0.00</valorInversoesFinanceirasPIEA>
     <valorInversoesFinanceirasPIDEA>0.00</valorInversoesFinanceirasPIDEA>
     <valorInversoesFinanceirasPP>0.00</valorInversoesFinanceirasPP>
     <valorInversoesFinanceirasPC>0.00</valorInversoesFinanceirasPC>
     <valorAmortizacaoDividaPIEA>0.00</valorAmortizacaoDividaPIEA>
     <valorAmortizacaoDividaPIDEA>546360.19</valorAmortizacaoDividaPIDEA>
     <valorAmortizacaoDividaPP>0.00</valorAmortizacaoDividaPP>
     <valorAmortizacaoDividaPC>0.00</valorAmortizacaoDividaPC>
   </blocoCA12>
</anexoDados12>
</anexo12>";

            fputs($arquivoXML, $sLinhaAnexo12."\r\n");
        }

        fputs($arquivoXML, $sLinhaTrailer);
        fclose($arquivoXML);
    }
}
