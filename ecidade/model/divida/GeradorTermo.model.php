<?php
/**
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

/**
 * @todo - retirar os arrobas
 */

/**
 * Classe esta em processo de refatoracao
 */

class GeradorTermo
{

    /**
     * @var DBDate
     */
    private $oDataRecalculoJurosMulta = null;

    /**
     * @var string
     */
    private $textoCreditosTrib = "C R É D I T O    T R I B U T Á R I O ";

    /**
     * @var string
     */
    private $textoCreditosNaoTrib = "C R É D I T O  N Ã O  T R I B U T Á R I O";

    public function setDataRecalculoJurosMulta(DBDate $oDataRecalculo)
    {
        $this->oDataRecalculoJurosMulta = $oDataRecalculo;
    }

    private $pdf = null;

    public function __construct($pdf = null)
    {
        $this->oDocumento = new libdocumento(5029);
        if (!empty($pdf)) {
            $this->pdf = $pdf;
        }

        $this->verificaAlteraTexto();
    }

    /**
     * Função que verifica se deve ou não mudar o texto de crédtio tributário
     * @throws Exception
     */
    private function verificaAlteraTexto ()
    {
        $cl_db_config = new cl_db_config();
        $rDbConfig = $cl_db_config->sql_record($cl_db_config->sql_query_file(1, "cgc"));

        if (!$rDbConfig) {
            throw new Exception("Erro ao buscar os dados da prefeitura.");
        }

        $oDbConfig = db_utils::fieldsMemory($rDbConfig, 0);

        switch ($oDbConfig->cgc) {
            case "05903125000145" :
                $this->textoCreditosTrib = "DETALHAMENTO DO CRÉDITO";
                $this->textoCreditosNaoTrib = "DETALHAMENTO DO CRÉDITO";
                break;
        }
    }

    /**
     * Método responsavel pela geração da termo
     *
     * @param  integer $certid Código Termo (inicial)
     * @param  integer $certid1 Código Termo (final)
     * @param  boolean $reemissao Controla se recalcula os debitos usando a data de geracao (True) || recalcula usando data de emissao da termo (False)
     * @param  boolean $totexe controla se exibe totalizador por exercicio
     *
     * @return void
     */
    public function gerar($certid, $certid1, $proced, $dataInicial, $dataFinal, $anoexerc, $origemtipo, $origem, $totexe, $reemissao)
    {
        $iInstituicao = db_getsession("DB_instit");
        $oDaoParDiv = new cl_pardiv();
        $sSqlPardiv = $oDaoParDiv->sql_query_file($iInstituicao);
        $rsPardiv = $oDaoParDiv->sql_record($sSqlPardiv);

        if ($oDaoParDiv->numrows > 0) {

            $oPardiv = db_utils::fieldsMemory($rsPardiv, 0);
            /* $lImpFolha = $oPardiv->v04_implivrofolha == "t" ? true : false; */
        }

        $clcfiptu = new cl_cfiptu;
        $this->oDocumento->getParagrafos();
        $this->oDocumento->nro_parcelamento = 5;

        if ($this->oDocumento->lErro) {
            throw new BusinessException("{$this->oDocumento->sMsgErro}");
        }

        if (count($this->oDocumento->aParagrafos) == 0) {
            throw new BusinessException("Configure o Documento 5029.");
        }

        $classinatura = new cl_assinatura;
        $clpropri = new cl_propri;

        /**
         * Buscamos o documento da que agrupa a TERMO
         */
        $exercicio = db_getsession("DB_anousu");
        $borda = 1;
        $bordat = 1;
        $preenc = 0;

        $TPagina = 57;

        $instit = db_getsession('DB_instit');
        $count_certid = 0;

        if (!isset($certid) || empty($certid)) {
            $sql = $this->getSqlListaCertidoes($certid, $certid1, $count_certid, $instit, $proced, $dataInicial, $dataFinal, $anoexerc, $origemtipo, $origem);
            $rsCertidao = db_query($sql);
            $numero = pg_num_rows($rsCertidao);
        } else {
            $numero = ($certid1 - $certid) + 1;
        }

        if (empty($this->pdf)) {
            $this->pdf = new pdfTermo(); // abre a classe
            $pdf = $this->pdf;
            $pdf->open();
        } else {
            $pdf = $this->pdf;
        }
        //$this->pdf = new pdfCertidao(); // abre a classe
        $pdf = $this->pdf;
        //$pdf->open();
        $pdf->lPrintFooter = false;
        // abre o relatorio
        $pdf->aliasnbpages();     // gera alias para as paginas
        $pdf->SetAutoPageBreak('on', 15);

        global $head5;
        $head5 = "";

        for ($numcertid = 0; $numcertid < $numero; $numcertid++) {

            $instit = db_getsession('DB_instit');

            $sql = $this->getSqlListaCertidoes($certid, $certid1, $count_certid, $instit, $proced, $dataInicial, $dataFinal, $anoexerc, $origemtipo, $origem);

            $rsCertidao = db_query($sql);
            if (pg_num_rows($rsCertidao) == 0) {
                continue;
            }
            $oCertid = db_utils::fieldsMemory($rsCertidao, 0);
            $oTermo = new termoinscr($oCertid->v92_termo);
            $aProcedencias = $oTermo->getProcedencias();
            $count_certid .= "," . $oCertid->v92_termo;

            /* $head2 = $oTermo->getLivro(); */
            /* $head3 = $oTermo->getFolha(); */
            $head4 = $oTermo->getDataLivro();
            $head5 = $oCertid->v92_termo . "/" . $oTermo->getAno();

            $sqlparag = $this->getSqlParg($instit);

            $resparag = db_query($sqlparag);
            $head1 = 'SECRETARIA DE FINANÇAS';
            if ($resparag && pg_num_rows($resparag) > 0) {
                $head1 = db_utils::fieldsMemory($resparag, 0)->db02_texto;
            }

            $sSqlDadosInicial = $this->getSqlDadosInicial($oCertid->v92_termo);

            $rsDadosInicial = db_query($sSqlDadosInicial);

            if (pg_numrows($rsDadosInicial) > 0) {

                $oDadosInicial = db_utils::fieldsMemory($rsDadosInicial, 0);
                $this->oDocumento->processoforo = $oDadosInicial->processoforo;
                $this->oDocumento->numeroinicial = $oDadosInicial->numeroinicial;
            }

            $pdf->addPage();
            $pdf->settextcolor(0, 0, 0);
            $pdf->setfillcolor(220);
            $pdf->setfont('arial', '', 11);


            $this->drawDevedores($pdf, $oTermo, 'o');

            $this->drawOrigens($pdf, $oTermo, 'o');

            $sParagrafo = '';

            if (array_key_exists('1', $this->oDocumento->aParagrafos)) {
                $sParagrafo = $this->oDocumento->aParagrafos[1];
            }

            $this->drawCertifico($pdf, $oTermo, $sParagrafo);

            $this->drawFundamentoLegal($pdf, $oTermo, $aProcedencias);

            if ($reemissao == "true") {
                $this->drawDebitos($pdf, $oTermo, $oPardiv, $totexe == "t" ? true : false,
                $reemissao = true);
            }
            else {
                $this->drawDebitos($pdf, $oTermo, $oPardiv, $totexe == "t" ? true : false,
                $reemissao = false);
            }

            $this->drawMetodologia($pdf, $oTermo, $aProcedencias);

            $this->drawTextoPadrao($pdf, $oTermo, @$this->oDocumento->aParagrafos[3]->db02_texto);

            $this->drawData($pdf, $oTermo, $oCertid->v92_dtinsc, $reemissao == "t" ? true : false);

            $this->drawAssinaturas($pdf, $oTermo, $this->oDocumento->aParagrafos);

        }
    }

    public function escreverArquivo($sNomeArquivo)
    {

        if (empty($sNomeArquivo)) {
            throw new ParameterException("Parâmetro sNomeArquivo inválido");
        }

        $this->pdf->Output($sNomeArquivo, false, true);
        if (!file_exists($sNomeArquivo)) {
            throw new FileException("Erro ao gerar arquivo da Certidão.");
        }

        return $sNomeArquivo;
    }

    public function exibirArquivo()
    {

        //@todo - tratar
        $this->pdf->Output('', true);
    }

    /**
     * Desenha o quadro dos devedores
     *
     * @param pdf3 $pdf
     * @param termoinscr $oCertidao
     */
    private function drawDevedores($pdf, termoinscr $oCertidao, $lTipoOrdem)
    {

        $pdf->setfont('arial', 'B', 10);
        $pdf->cell(190, 5, 'DEVEDOR(ES)', 0, 1, "C", 0);
        $pdf->cell(190, 0.7, '', "TB", 1, "L", 0);
        $pdf->Ln(5);

        $pdf->setfont('arial', 'B', 10);

        $pdf->cell(30, 5, 'TIPO', "TB", 0, "L", 0);
        $pdf->cell(110, 5, 'NOME', 1, 0, "L", 0);
        $pdf->cell(20, 5, 'CGM ', 1, 0, "L", 0);
        $pdf->cell(30, 5, 'CPF/CNPJ', "TB", 1, "L", 0);

        $pdf->setfont('arial', '', 10);

        $aCgcCpf = array();
        $aDadosDevedor = $oCertidao->getDevedoresEnvolvidos($lTipoOrdem);

        foreach ($aDadosDevedor->aDevedores as $oDevedor) {

            $aCgcCpf[] = $oDevedor->cgcCpf;

            $pdf->setfont('arial', '', 8);
            $pdf->Ln(1);
            $pdf->cell(30, 3, substr($oDevedor->tipo, 0, 15), 0, 0, "L", 0);
            $pdf->Cell(110, 3, $oDevedor->nome, 0, 0, "L", 0);
            $pdf->Cell(20, 3, $oDevedor->numcgm, 0, 0, "L", 0);
            $pdf->Cell(30, 3, $oDevedor->cgcCpf, 0, 1, "L", 0);

            $fone = $oDevedor->telefone != "" ? " Fone: " . $oDevedor->telefone : "";
            $celular = $oDevedor->celular != "" ? " Cel: " . $oDevedor->celular : "";

            $pdf->setfont('arial', '', 8);
            $pdf->MultiCell(190, 3, $oDevedor->endereco . $fone . $celular, "B", "L", 0);
            $pdf->setfont('arial', '', 10);


        }
    }

    /**
     * [drawOrigens description]
     * @param  [type] $pdf           [description]
     * @param  termoinscr $oCertidao [description]
     * @param  [type] $lTipoOrdem    [description]
     * @return [type]                [description]
     */
    private function drawOrigens($pdf, termoinscr $oCertidao, $lTipoOrdem)
    {
        $aImoveis = $oCertidao->getDevedoresEnvolvidos($lTipoOrdem);

        if (count($aImoveis->aImoveis) > 0) {
            $pdf->setfont('', '', 10);
            $pdf->Ln(3);
            $pdf->setfont('arial', 'B', 10);
            $pdf->cell(190, 5, 'DADOS DO IMÓVEL', 0, 1, "C", 0);
            $pdf->cell(190, 0.7, '', "TB", 1, "L", 0);
        }

        $clcfiptu = new cl_cfiptu;
        $rsCfiptu = $clcfiptu->sql_record($clcfiptu->sql_query_file("", "j18_utilizaloc", "",
            "j18_anousu = " . db_getsession("DB_anousu")));
        if ($clcfiptu->numrows > 0) {
            $oCfiptu = db_utils::fieldsMemory($rsCfiptu, 0);
        } else {
            $oCfiptu->j18_utilizaloc = 'f';
        }

        foreach ($aImoveis->aImoveis as $oOrigem) {

            $pdf->Ln(3);

            $pdf->setfont('arial', '', 8);
            $pdf->cell(120, 3, 'ENDEREÇO: ' . (isset($oOrigem->endereco) ? $oOrigem->endereco : ""), 0, 0, "l", 0);
            $pdf->cell(40, 3, 'BAIRRO : ' . (isset($oOrigem->bairro) ? $oOrigem->bairro : ""), 0, 1, "l", 0);
            $pdf->cell(120, 3, 'CIDADE : ' . (isset($oOrigem->cidade) ? $oOrigem->cidade : ""), 0, 0, "l", 0);
            $pdf->cell(40, 3, 'CEP : ' . (isset($oOrigem->cep) ? $oOrigem->cep : ""), 0, 1, "l", 0);
            $pdf->cell(30, 3, 'SETOR  : ' . (isset($oOrigem->setor) ? $oOrigem->setor : ""), 0, 0, "l", 0);
            $pdf->cell(30, 3, 'QUADRA : ' . (isset($oOrigem->quadra) ? $oOrigem->quadra : ""), 0, 0, "l", 0);
            $pdf->cell(30, 3, 'LOTE : ' . (isset($oOrigem->lote) ? $oOrigem->lote : ""), 0, 0, "l", 0);
            $pdf->cell(30, 3, 'MATRÍCULA : ' . (isset($oOrigem->matricula) ? $oOrigem->matricula : ""), 0, 0, "l", 0);
            $pdf->cell(30, 3, 'MATRÍCULA REG.IMÓVEL: ' . (isset($oOrigem->matricula_ri) ? $oOrigem->matricula_ri : ""), 0, 1, "l",
            0);


            if ($oCfiptu->j18_utilizaloc == 't') {
                $pdf->cell(60, 5,
                    'DADOS DE LOCALIZACAO: SETOR  : ' . (isset($oOrigem->setorloc) ? $oOrigem->setorloc : "") . '-'
                    . (isset($oOrigem->descrsetorloc) ? $oOrigem->descrsetorloc : "") .
                    ' QUADRA : ' . (isset($oOrigem->quadraloc) ? $oOrigem->quadraloc : "") .
                    ' - LOTE : ' . (isset($oOrigem->loteloc) ? $oOrigem->loteloc : ""), 0, 0, "l", 0);
                $pdf->ln();
            }

            $pdf->Ln(3);
            $pdf->cell(190, 0.7, '', "TB", 1, "L", 0);
            $pdf->Ln(3);

        }

        if (count($aImoveis->aEmpresas) > 0) {

            $pdf->Ln(3);
            $pdf->setfont('arial', 'B', 10);
            $pdf->cell(190, 7, 'DADOS DA INSCRIÇÃO', 0, 1, "C", 0);
            $pdf->setfont('arial', '', 10);
            $pdf->cell(190, 0.7, '', "TB", 1, "L", 0);
            $pdf->Ln(3);
            foreach ($aImoveis->aEmpresas as $oOrigem) {

                if ($pdf->gety() > $pdf->h - 68) {

                    $pdf->addPage();
                    $pdf->SetFont('ARIAL', 'B', 11);
                    $pdf->multicell(0, 5,
                        "TERMO DE INSCRIÇÃO EM DÍVIDA ATIVA N" . CHR(176) . " " . $oCertidao->getCodigo() . "/{$oCertidao->getAno()}",
                        0, "C", 0, 0);
                    $pdf->setfont('', 'B', 9);
                    $pdf->ln(8);

                }
                $pdf->cell(35, 5, 'INSCRIÇÃO: ', 0, 0, "L", 0);
                $pdf->cell(100, 5, (isset($oOrigem->inscricao) ? $oOrigem->inscricao : ""), 0, 0, "L", 0);
                $pdf->ln();
                $pdf->cell(35, 5, 'REF. AO ALVARÁ : ', 0, 0, "L", 0);
                $pdf->cell(100, 5, (isset($oOrigem->endereco) ? $oOrigem->endereco : ""), 0, 1, "L", 0);
                $pdf->cell(35, 5, 'BAIRRO : ', 0, 0, "l", 0);
                $pdf->cell(100, 5, (isset($oOrigem->bairro) ? $oOrigem->bairro : ""), 0, 1, "l", 0);
                $pdf->cell(35, 5, 'CIDADE : ', 0, 0, "l", 0);
                $pdf->cell(100, 5, (isset($oOrigem->cidade) ? $oOrigem->cidade : ""), 0, 0, "l", 0);
                $pdf->cell(15, 5, 'CEP : ', 0, 0, "l", 0);
                $pdf->cell(100, 5, (isset($oOrigem->cep) ? $oOrigem->cep : ""), 0, 1, "l", 0);
                $pdf->cell(190, 0.7, '', "TB", 1, "L", 0);
                $pdf->Ln(3);
            }
        }
    }

    /**
     * [drawCertifico description]
     * @param  [type] $pdf       [description]
     * @param  [type] $oCertidao [description]
     * @param  [type] $sTexto    [description]
     * @return [type]            [description]
     */
    private function drawCertifico($pdf, $oCertidao, $oParagrafo)
    {

        if (!empty($oParagrafo)) {
            $sTexto = $oParagrafo->db02_texto;
        }
        $pdf->setfont('', 'B', 10);
        $pdf->MultiCell(0, 5, $this->oDocumento->replaceText($sTexto), 0, "L", 0);
        $pdf->setfont('arial', '', 11);
    }

    /**
     * [drawFundamentoLegal description]
     * @param  [type] $pdf           [description]
     * @param  [type] $oCertidao     [description]
     * @param  [type] $aProcedencias [description]
     * @return [type]                [description]
     */
    private function drawFundamentoLegal($pdf, $oCertidao, $aProcedencias)
    {

        $lGerarFundamentacao = true;

        if (count($aProcedencias) > 0) {

            $sSqlFundamentacao = "select distinct                                                                  ";
            $sSqlFundamentacao .= "       db02_texto                                                                ";
            $sSqlFundamentacao .= "  from db_documento                                                              ";
            $sSqlFundamentacao .= "       inner join procedparag on procedparag.v80_docum = db_documento.db03_docum ";
            $sSqlFundamentacao .= "       inner join db_docparag  on db03_docum   = db04_docum                      ";
            $sSqlFundamentacao .= "       inner join db_tipodoc   on db08_codigo  = db03_tipodoc                    ";
            $sSqlFundamentacao .= "       inner join db_paragrafo on db04_idparag = db02_idparag                    ";
            $sSqlFundamentacao .= " where                                                                           ";

            $sSqlFundamentacao .= "  v80_proced in ";
            $sSqlFundamentacao .= " (" . implode(",", $aProcedencias) . ") ";

            if ($lGerarFundamentacao) {

                $rsFundamentacao = db_query($sSqlFundamentacao);
                $iTotalFundamentacao = pg_num_rows($rsFundamentacao);

                if ($iTotalFundamentacao > 0) {

                    for ($i = 0; $i < $iTotalFundamentacao; $i++) {

                        $oFundamentacao = db_utils::fieldsmemory($rsFundamentacao, $i);
                        $pdf->Ln(2);
                        $pdf->MultiCell(0, 5, db_geratexto($oFundamentacao->db02_texto), 0, "L", 0);
                    }
                }
            }
        }
    }

    /**
     * [drawMetodologia description]
     * @param  [type] $pdf           [description]
     * @param  termoinscr $oCertidao [description]
     * @param  [type] $aProcedencias [description]
     * @return [type]                [description]
     */
    private function drawMetodologia($pdf, termoinscr $oCertidao, $aProcedencias)
    {

        $lGerarMetodologia = true;
        $sMetCalculo = "select distinct v80_docmetcalculo, ";
        $sMetCalculo .= "       db02_texto,  ";
        $sMetCalculo .= "       db02_alinhamento ";
        $sMetCalculo .= "  from db_documento  ";
        $sMetCalculo .= "       inner join procedparag on procedparag.v80_docmetcalculo = db_documento.db03_docum ";
        $sMetCalculo .= "       inner join db_docparag  on db03_docum   = db04_docum ";
        $sMetCalculo .= "       inner join db_tipodoc   on db08_codigo  = db03_tipodoc ";
        $sMetCalculo .= "       inner join db_paragrafo on db04_idparag = db02_idparag ";
        $sMetCalculo .= " where db03_tipodoc = 1050  ";


        $sMetCalculo .= " and v80_proced in (" . implode(",", $aProcedencias) . ") ";

        if ($lGerarMetodologia) {
            $pdf->setfont('', 'B', 6);
            $pdf->MultiCell(0, 4, "FORMA DE CALCULAR OS JUROS, MULTA E ATUALIZAÇÃO MONETÁRIA", 1, "C", 1);
            $pdf->setfont('arial', 'B', 8);

            $resMetCalculo = db_query($sMetCalculo);
            if ($resMetCalculo) {

                $iNumRows = pg_num_rows($resMetCalculo);
                for ($v = 0; $v < $iNumRows; $v++) {

                    $oMetodologia = db_utils::fieldsmemory($resMetCalculo, $v);
                    $pdf->MultiCell(0, 5, db_geratexto($oMetodologia->db02_texto), 1, $oMetodologia->db02_alinhamento,
                        0);
                }
            }
        }
    }

    /**
     * Escreve o quadro de Divida
     *
     * @param pdf3 $pdf
     * @param termoinscr $oCertidao
     */
    private function drawDebitos(pdf3 $pdf, termoinscr $oCertidao, $oPardiv, $lTotaliza = false, $lReemissao)
    {

        $aDebitos = $oCertidao->getDebitos($lReemissao);

        $aDebitosOrdenado = array();
        $aTotaisAno = array();
        $oTotalGeral = array();

        foreach ($aDebitos as $oDebito) {

            $aDebitosOrdenado[$oDebito->procedenciatributaria][] = $oDebito;
            if (!isset($aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio])) {

                $aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio] = new stdClass();
                $aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio]->vlrhis = $oDebito->valorhistorico;
                $aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio]->vlrcor = $oDebito->valorcorrigido;
                $aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio]->vlrmul = $oDebito->valormulta;
                $aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio]->vlrjur = $oDebito->valorjuros;
                $aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio]->vlrtot = $oDebito->valortotal;
                if ($oDebito->certidmassa != 0) {
                    $aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio]->vlrtot = $oDebito->valorcorrigido;
                }

            } else {

                $aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio]->vlrhis += $oDebito->valorhistorico;
                $aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio]->vlrcor += $oDebito->valorcorrigido;
                $aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio]->vlrmul += $oDebito->valormulta;
                $aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio]->vlrjur += $oDebito->valorjuros;

                if ($oDebito->certidmassa != 0) {
                    $aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio]->vlrtot += $oDebito->valorcorrigido;
                } else {
                    $aTotaisAno[$oDebito->procedenciatributaria][$oDebito->exercicio]->vlrtot += $oDebito->valortotal;
                }
            }
            if (!isset($oTotalGeral[$oDebito->procedenciatributaria])) {

                $oTotalGeral[$oDebito->procedenciatributaria] = new stdClass();
                $oTotalGeral[$oDebito->procedenciatributaria]->valorhistorico = $oDebito->valorhistorico;
                $oTotalGeral[$oDebito->procedenciatributaria]->valorcorrigido = $oDebito->valorcorrigido;
                $oTotalGeral[$oDebito->procedenciatributaria]->valormulta = $oDebito->valormulta;
                $oTotalGeral[$oDebito->procedenciatributaria]->valorjuros = $oDebito->valorjuros;
                $oTotalGeral[$oDebito->procedenciatributaria]->valortotal = $oDebito->valortotal;
                if ($oDebito->certidmassa != 0) {
                    $oTotalGeral[$oDebito->procedenciatributaria]->valortotal = $oDebito->valorcorrigido;
                }
            } else {

                $oTotalGeral[$oDebito->procedenciatributaria]->valorhistorico += $oDebito->valorhistorico;
                $oTotalGeral[$oDebito->procedenciatributaria]->valorcorrigido += $oDebito->valorcorrigido;
                $oTotalGeral[$oDebito->procedenciatributaria]->valormulta += $oDebito->valormulta;
                $oTotalGeral[$oDebito->procedenciatributaria]->valorjuros += $oDebito->valorjuros;
                if ($oDebito->certidmassa != 0) {
                    $oTotalGeral[$oDebito->procedenciatributaria]->valortotal += $oDebito->valorcorrigido;
                } else {
                    $oTotalGeral[$oDebito->procedenciatributaria]->valortotal += $oDebito->valortotal;

                }
            }
        }

        /**
         * Escrevemos o quadro dos creditos
         */
        foreach ($aDebitosOrdenado as $iTipo => $aTipo) {

            $pdf->ln(3);
            if ($iTipo == 1) {

                $pdf->MultiCell(0, 5, $this->textoCreditosTrib, 0, "C", 0);
            } else {

                $pdf->setfont('', 'B', 9);
                $pdf->MultiCell(0, 5, $this->textoCreditosNaoTrib, 0, "C", 0);
            }
            $pdf->SetFont('', 'B', 6);
            $pdf->Cell(11, 5, "DÍVIDA", 1, 0, "C", 1);
            $pdf->Cell(11, 5, "NUMPRE", 1, 0, "C", 1);
            $pdf->Cell(12, 5, "DT LANC.", 1, 0, "C", 1);
            $pdf->Cell(10, 5, "EXERC.", 1, 0, "C", 1);
            $pdf->Cell(7, 5, "PARC", 1, 0, "C", 1);
            $pdf->Cell(15, 5, "LIV/FOL", 1, 0, "C", 1);
            $pdf->Cell(13, 5, "ORIG.", 1, 0, "C", 1);
            $pdf->Cell(25, 5, "PROCEDÊNIA", 1, 0, "C", 1);
            /* $pdf->Cell(18, 5, "ORIGEM DÉBITO", 1, 0, "C", 1); */
            $pdf->Cell(15, 5, "DATA INSCR.", 1, 0, "C", 1);
            $pdf->Cell(15, 5, "DATA VENC.", 1, 0, "C", 1);
            $pdf->Cell(13, 5, "VLR HIST.", 1, 0, "C", 1);
            $pdf->Cell(13, 5, "CORRIGIDO", 1, 0, "C", 1);
            $pdf->Cell(11, 5, "MULTA", 1, 0, "C", 1);
            $pdf->Cell(10, 5, "JUROS", 1, 0, "C", 1);
            $pdf->Cell(11, 5, "TOTAL", 1, 1, "C", 1);
            $lEscreveTotal = false;
            $iExercicioAnterior = null;
            $pagina = 0;
            $iY = 0;

            foreach ($aTipo as $oDebito) {

                if ($oDebito->exercicio != $iExercicioAnterior && $lEscreveTotal && $lTotaliza) {

                    $pdf->SetFont('', 'B', 6);
                    $pdf->Cell(134, 5, "TOTAL EXERCICIO - {$iExercicioAnterior}", 1, 0, "C", 0);
                    $pdf->Cell(13, 5, db_formatar($aTotaisAno[$iTipo][$iExercicioAnterior]->vlrhis, 'f'), 1, 0,
                        "R", 0);
                    $pdf->Cell(13, 5, db_formatar($aTotaisAno[$iTipo][$iExercicioAnterior]->vlrcor, 'f'), 1, 0,
                        "R", 0);
                    $pdf->Cell(11, 5, db_formatar($aTotaisAno[$iTipo][$iExercicioAnterior]->vlrmul, 'f'), 1, 0,
                        "R", 0);
                    $pdf->Cell(10, 5, db_formatar($aTotaisAno[$iTipo][$iExercicioAnterior]->vlrjur, 'f'), 1, 0,
                        "R", 0);
                    $pdf->Cell(11, 5, db_formatar($aTotaisAno[$iTipo][$iExercicioAnterior]->vlrtot, 'f'), 1, 1,
                        "R", 0);
                    $pdf->setfont('', 'B', 9);

                }
                $lEscreveTotal = true;
                if ($iY > 272) {

                    $pdf->AddPage();
                    $pdf->SetFont('', 'B', 6);

                    $pdf->Cell(11, 5, "DÍVIDA", 1, 0, "C", 1);
                    $pdf->Cell(11, 5, "NUMPRE", 1, 0, "C", 1);
                    $pdf->Cell(12, 5, "DT LANC.", 1, 0, "C", 1);
                    $pdf->Cell(10, 5, "EXERC.", 1, 0, "C", 1);
                    $pdf->Cell(7, 5, "PARC", 1, 0, "C", 1);
                    $pdf->Cell(15, 5, "LIV/FOL", 1, 0, "C", 1);
                    $pdf->Cell(13, 5, "ORIG.", 1, 0, "C", 1);
                    $pdf->Cell(25, 5, "PROCEDÊNIA", 1, 0, "C", 1);
                    /* $pdf->Cell(18, 5, "ORIGEM DÉBITO", 1, 0, "C", 1); */
                    $pdf->Cell(15, 5, "DATA INSCR.", 1, 0, "C", 1);
                    $pdf->Cell(15, 5, "DATA VENC.", 1, 0, "C", 1);
                    $pdf->Cell(13, 5, "VLR HIST.", 1, 0, "C", 1);
                    $pdf->Cell(13, 5, "CORRIGIDO", 1, 0, "C", 1);
                    $pdf->Cell(11, 5, "MULTA", 1, 0, "C", 1);
                    $pdf->Cell(10, 5, "JUROS", 1, 0, "C", 1);
                    $pdf->Cell(11, 5, "TOTAL", 1, 1, "C", 1);
                    $pagina = $pdf->PageNo();

                }

                $pdf->SetFont('', '', 6);
                $pdf->Cell(11, 5, $oDebito->codigodivida, 1, 0, "C", 0);
                $pdf->Cell(11, 5, $oDebito->numpre, 1, 0, "C", 0);
                $pdf->Cell(12, 5, db_formatar($oDebito->datalancamento, 'd'), 1, 0, "C", 0);
                $pdf->Cell(10, 5, $oDebito->exercicio, 1, 0, "C", 0);
                $pdf->Cell(7, 5, $oDebito->numpar, 1, 0, "C", 0);
                $pdf->Cell(15, 5, $oDebito->livro . "/" . $oDebito->folha, 1, 0, "C", 0);
                $pdf->Cell(13, 5, ucfirst($oDebito->origem) . "/{$oDebito->codigoorigem}", 1, 0, "C", 0);
                if (strlen($oDebito->procedencia) == 20) {
                    $procedencia = substr($oDebito->procedencia, 0, 20);
                } else {
                    $procedencia = $oDebito->procedencia;
                }
                $pdf->Cell(25, 5, $procedencia, 1, 0, "L", 0);
                /* $pdf->Cell(18, 5, $oDebito->origemdebito, 1, 0, "C", 0); */
                $pdf->Cell(15, 5, db_formatar($oDebito->datainscricao, 'd'), 1, 0, "C", 0);
                $pdf->Cell(15, 5, db_formatar($oDebito->datavencimento, 'd'), 1, 0, "C", 0);
                $pdf->Cell(13, 5, db_formatar($oDebito->valorhistorico, 'f'), 1, 0, "R", 0);
                $pdf->Cell(13, 5, db_formatar($oDebito->valorcorrigido, 'f'), 1, 0, "R", 0);
                if ($oDebito->certidmassa == 0) {

                    $pdf->Cell(11, 5, db_formatar($oDebito->valormulta, 'f'), 1, 0, "R", 0);
                    $pdf->Cell(10, 5, db_formatar($oDebito->valorjuros, 'f'), 1, 0, "R", 0);
                    $pdf->Cell(11, 5, db_formatar($oDebito->valortotal, 'f'), 1, 1, "R", 0);

                } else {

                    $pdf->Cell(14, 5, db_formatar(0, 'f'), 1, 0, "R", 0);
                    $pdf->Cell(14, 5, db_formatar(0, 'f'), 1, 0, "R", 0);
                    $pdf->Cell(15, 5, db_formatar($oDebito->valorcorrigido, 'f'), 1, 1, "R", 0);

                }
                if ($oPardiv->v04_imphistcda == "t" && isset($oDebito->observacao)) {

                    $pdf->SetFont('', 'I', 5);
                    $pdf->setX(10);

                    $pdf->SetAligns(array('J'));
                    $pdf->SetWidths(array(195));
                    $pdf->Row_multicell(array("Observação: {$oDebito->observacao}"), 4, true, 4, 0, true, true,
                        3, 3);

                    $pdf->SetFont('', '', 6);

                }

                $iExercicioAnterior = $oDebito->exercicio;
                $iY = $pdf->GetY();

            }

            /**
             * Escreve o total do ultimo ano
             */
            if (($lEscreveTotal && $lTotaliza)) {

                $pdf->SetFont('', 'B', 6);
                $pdf->Cell(134, 5, "TOTAL EXERCICIO - {$iExercicioAnterior}", 1, 0, "C", 0);
                $pdf->Cell(13, 5, db_formatar($aTotaisAno[$iTipo][$iExercicioAnterior]->vlrhis, 'f'), 1, 0, "R",
                    0);
                $pdf->Cell(13, 5, db_formatar($aTotaisAno[$iTipo][$iExercicioAnterior]->vlrcor, 'f'), 1, 0, "R",
                    0);
                $pdf->Cell(11, 5, db_formatar($aTotaisAno[$iTipo][$iExercicioAnterior]->vlrmul, 'f'), 1, 0, "R",
                    0);
                $pdf->Cell(10, 5, db_formatar($aTotaisAno[$iTipo][$iExercicioAnterior]->vlrjur, 'f'), 1, 0, "R",
                    0);
                $pdf->Cell(11, 5, db_formatar($aTotaisAno[$iTipo][$iExercicioAnterior]->vlrtot, 'f'), 1, 1, "R",
                    0);
                $pdf->setfont('', 'B', 9);

            }
            $pdf->SetFont('', 'B', 6);
            $pdf->Cell(134, 5, "TOTAL", 1, 0, "C", 0);
            $pdf->Cell(13, 5, db_formatar($oTotalGeral[$iTipo]->valorhistorico, 'f'), 1, 0, "R", 0);
            $pdf->Cell(13, 5, db_formatar($oTotalGeral[$iTipo]->valorcorrigido, 'f'), 1, 0, "R", 0);
            $pdf->Cell(11, 5, db_formatar($oTotalGeral[$iTipo]->valormulta, 'f'), 1, 0, "R", 0);
            $pdf->Cell(10, 5, db_formatar($oTotalGeral[$iTipo]->valorjuros, 'f'), 1, 0, "R", 0);
            $pdf->Cell(11, 5, db_formatar($oTotalGeral[$iTipo]->valortotal, 'f'), 1, 1, "R", 0);
            $pdf->setfont('', 'B', 9);

            $pdf->Ln(5);
        }
    }

    /**
     * [drawTextoPadrao description]
     * @param  [type] $pdf       [description]
     * @param  [type] $oCertidao [description]
     * @param  [type] $sTexto    [description]
     * @return [type]            [description]
     */
    private function drawTextoPadrao($pdf, $oCertidao, $sTexto)
    {
        if (empty($sTexto)) {
            return;
        }

        $pdf->setfont('', 'B', 9);
        $pdf->Ln(5);
        $pdf->MultiCell(0, 5, $sTexto, 0, "L", 0);
    }

    /**
     * [drawData description]
     * @param  [type] $pdf       [description]
     * @param  [type] $oCertidao [description]
     * @param  [type] $sData     [description]
     * @param  [type] $lCorrigir [description]
     * @return [type]            [description]
     */
    private function drawData($pdf, $oCertidao, $sData, $lCorrigir)
    {
        $pdf->setfont('', 'B', 7);
        $sMunic = db_stdClass::getDadosInstit()->munic;
        if ($lCorrigir) {

            $dataemis = db_getsession("DB_datausu");
            $anoemis = db_getsession("DB_anousu");
            $xdia = substr(date("Y-m-d", db_getsession("DB_datausu")), 8, 2);
            $xmes = substr(date("Y-m-d", db_getsession("DB_datausu")), 5, 2);
            $xano = substr(date("Y-m-d", db_getsession("DB_datausu")), 0, 4);

        } else {

            $dataemis = mktime(0, 0, 0, substr($sData, 5, 2),
                substr($sData, 8, 2),
                substr($sData, 0, 4)
            );
            $anoemis = substr($sData, 0, 4);
            $xmes = substr($sData, 5, 2);
            $xdia = substr($sData, 8, 2);
            $xano = substr($sData, 0, 4);

        }

        $pdf->MultiCell(0, 4, $sMunic . ', ' . $xdia . " de " . db_mes($xmes) . " de " . $xano . '.', 0, "R", 0);
    }

    /**
     * [drawAssinaturas description]
     * @param  [type] $pdf          [description]
     * @param  [type] $oCertidao    [description]
     * @param  [type] $aAssinaturas [description]
     * @return [type]               [description]
     */
    private function drawAssinaturas($pdf, $oCertidao, $aAssinaturas)
    {
        $asssec = null;
        $asscoord = null;

        foreach ($aAssinaturas as $oAssinaturas) {

            if ($oAssinaturas->db02_descr == "ASSINATURAS_CODIGOPHP") {
                $assinaturas_php = trim($oAssinaturas->db02_texto);
            }
            if ($oAssinaturas->db04_ordem == '4') {
                $asssec = $oAssinaturas->db02_texto;
            }
            if ($oAssinaturas->db04_ordem == '5') {
                $asscoord = $oAssinaturas->db02_texto;
            }
        }

        if (empty($asssec) && empty($asscoord) && !isset($assinaturas_php)) {
            return;
        }

        if ($pdf->gety() > $pdf->h - 66) {

            $pdf->addPage();
        }

        $pdf->setfont('', '', 1);
        $pdf->MultiCell(0, 2, "", 0, "R", 0);
        $pdf->setfont('', '', 10);

        if (!empty($asssec)) {
            $sec = "______________________________" . "\n" . $asssec;
        } else {
            $sec = "";
        }
        if (!empty($asscoord)) {
            $coor = "______________________________" . "\n" . $asscoord;
        } else {
            $coor = "";
        }

        $pdf->SetFont('', 'B', 10);

        $largura = ($pdf->w) / 2;
        $posy = $pdf->gety();
        $alt = 5;
        $dbinstit = db_getsession('DB_instit');

        if (isset($assinaturas_php) && $assinaturas_php != "") {

            eval($assinaturas_php);
        } else {

            if ($coor != "") {
                $pdf->multicell($largura - 20, 4, $coor, 0, "C", 0, 0);
            } else {
                $pdf->Cell(1, 3, "", 0, 0, "C", 0);
            }

            if ($sec != "") {

                $pdf->Cell($largura - 10, 3, "", 0, 0, "C", 0);
                $pdf->multicell($largura, 4, $sec, 0, "C", 0, 0);
            } else {
                $pdf->Cell(100, 3, "", 0, 0, "C", 0);
            }
        }
    }

    /**
     * Monta SQL para consultar a lista de Certidoes
     * @param  integer $certid Codigo da Certidao
     * @param  integer $certid1 Codigo Final da Certidao
     * @param  integer $count_certid Contador
     * @param  integer $instit Instituicao
     * @return string                SQL Query
     */
    private function getSqlListaCertidoes($certid, $certid1, $count_certid, $instit, $proced, $dataInicial, $dataFinal, $anoexerc, $origemtipo, $origem)
    {
        if (!isset($certid) || empty($certid)) {

            $sql = "select v92_termo,v92_dtinsc
                    from  termoinscr
                        inner join termoinscrreg on v93_termo = v92_termo
                        inner join divida on v01_coddiv = v93_coddiv
                                                and v01_instit = {$instit}
                        left  join cgm       on v01_numcgm = z01_numcgm
                        left join arrematric on v01_numpre = arrematric.k00_numpre
                        left join arreinscr on v01_numpre = arreinscr.k00_numpre
                    where v92_termo not in ( {$count_certid} )
                    and ({$anoexerc} = 0 or v01_exerc = {$anoexerc})
                    and ({$anoexerc} = 0 or v01_exerc = {$anoexerc})
                    and v01_dtinsc between '{$dataInicial}' and '{$dataFinal}'
                    and (({$origemtipo} = 0 and {$origem} = 0) or ({$origemtipo} = 1 and v01_numcgm = {$origem}) or ({$origemtipo} = 2 and k00_matric = {$origem}) or ({$origemtipo} = 3 and k00_inscr = {$origem}))
                    and ({$proced} = 0 or v01_proced = {$proced})";

        } else {

            $sql = "select v92_termo,v92_dtinsc
                        from  termoinscr
                            inner join termoinscrreg on v93_termo = v92_termo
                            inner join divida on v01_coddiv = v93_coddiv
                                                    and v01_instit = {$instit}
                            left  join cgm       on v01_numcgm = z01_numcgm
                            left join arrematric on v01_numpre = arrematric.k00_numpre
                            left join arreinscr on v01_numpre = arreinscr.k00_numpre
                        where v92_termo BETWEEN {$certid} AND {$certid1}
                        and v92_termo not in ( {$count_certid} )
                        and ({$anoexerc} = 0 or v01_exerc = {$anoexerc})
                        and ({$anoexerc} = 0 or v01_exerc = {$anoexerc})
                        and v01_dtinsc between '{$dataInicial}' and '{$dataFinal}'
                        and (({$origemtipo} = 0 and {$origem} = 0) or ({$origemtipo} = 1 and v01_numcgm = {$origem}) or ({$origemtipo} = 2 and k00_matric = {$origem}) or ({$origemtipo} = 3 and k00_inscr = {$origem}))
                        and ({$proced} = 0 or v01_proced = {$proced})
                        order by v92_termo limit 1";
        }

        return $sql;
    }

    /**
     * Monta SQL do Paragrafo do Termo
     * @param  integer $instit Instituicao
     * @return string          SQL Query
     */
    private function getSqlParg($instit)
    {

        $sqlparag = "select db02_texto
                   from db_documento
                        inner join db_docparag on db03_docum = db04_docum
                        inner join db_tipodoc on db08_codigo  = db03_tipodoc
                        inner join db_paragrafo on db04_idparag = db02_idparag
                        where db03_tipodoc = 1017 and db03_instit = {$instit} order by db04_ordem ";

        return $sqlparag;
    }

    /**
     * Monta SQL para consultar os dados da Inicial da Certidao
     * @param  integer $certid certid
     * @return string          SQL Query
     */
    private function getSqlDadosInicial($certid)
    {

        $sSqlDadosInicial = " select v51_inicial as numeroinicial,                                                                      ";
        $sSqlDadosInicial .= "        v70_codforo as processoforo                                                                        ";
        $sSqlDadosInicial .= "   from inicialcert                                                                                        ";
        $sSqlDadosInicial .= "        left join processoforoinicial on processoforoinicial.v71_inicial = inicialcert.v51_inicial              ";
        $sSqlDadosInicial .= "        left join processoforo        on processoforo.v70_sequencial     = processoforoinicial.v71_processoforo ";
        $sSqlDadosInicial .= "  where inicialcert.v51_certidao = {$certid}";

        return $sSqlDadosInicial;
    }

}
