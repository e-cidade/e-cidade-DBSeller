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

namespace ECidade\Tributario\Issqn\Simples\Service;

use BusinessException;
use cancelamentoDebitos;
use cl_arrecad;
use cl_arrehist;
use cl_arreinscr;
use cl_disarq;
use cl_disbanco;
use cl_issarqsimples;
use cl_issarqsimplesdisarq;
use cl_issarqsimplesregdisbanco;
use cl_issarqsimplesregissbase;
use cl_issarqsimplesregissvar;
use cl_isscadsimples;
use cl_isscadsimplesbaixa;
use cl_isscalc;
use cl_issvar;
use cl_parissqn;
use db_utils;
use ECidade\Tributario\Issqn\Simples\Repository\ISSArquivoSimplesRegistroRepository;

class ProcessaArquivoSimplesService
{
    public $sequencialArquivo;
    public $nomeArquivo;
    public $banco;
    public $agencia;
    public $conta;

    /**
     * Processamento do Arquivo Simples
     *
     * @param numeric $sequencialArquivo
     * @param string $nomeArquivo
     * @param numeric $banco
     * @param nuneric $agencia
     * @param nuneric $conta
     */
    public function __construct($sequencialArquivo, $nomeArquivo, $banco, $agencia, $conta)
    {
        $this->sequencialArquivo = $sequencialArquivo;
        $this->nomeArquivo = $nomeArquivo;
        $this->banco = $banco;
        $this->agencia = $agencia;
        $this->conta = $conta;
    }

    public function execute()
    {
        if (!db_utils::inTransaction()) {
            throw new BusinessException("Sem Transação Ativa");
        }

        $oDaoParISSQN = new cl_parissqn;
        $oParametrosISSQN = $oDaoParISSQN->getParametrosIssqn();
        $oDaoIssarqsimples = new cl_issarqsimples;

        $oDaoDisArq = new cl_disarq;
        $oDaoDisArq->id_usuario = db_getsession("DB_id_usuario");
        $oDaoDisArq->k15_codbco = $this->banco;
        $oDaoDisArq->k15_codage = $this->agencia;
        $oDaoDisArq->arqret = $this->nomeArquivo;
        $oDaoDisArq->textoret = "Object oid";
        $oDaoDisArq->dtretorno = date("Y-m-d", db_getsession("DB_datausu"));
        $oDaoDisArq->dtarquivo = date("Y-m-d", db_getsession("DB_datausu"));
        $oDaoDisArq->k00_conta = $this->conta;
        $oDaoDisArq->autent = "false";
        $oDaoDisArq->instit = db_getsession('DB_instit');
        $oDaoDisArq->md5 = 'null';
        $oDaoDisArq->incluir(null);

        if ($oDaoDisArq->erro_status == 0) {
            throw new BusinessException("Erro Disarq: " . $oDaoDisArq->erro_msg);
        }

        $oDaoIssArqSimplesDisArq = new cl_issarqsimplesdisarq;
        $oDaoIssArqSimplesDisArq->q43_issarqsimples = $this->sequencialArquivo;
        $oDaoIssArqSimplesDisArq->q43_disarq = $oDaoDisArq->codret;
        $oDaoIssArqSimplesDisArq->incluir(null);

        if ($oDaoIssArqSimplesDisArq->erro_status == 0) {
            throw new BusinessException("Erro IssArqSimplesDisArq: " . $oDaoIssArqSimplesDisArq->erro_msg);
        }

        $issArqSimplesRegRepository = new ISSArquivoSimplesRegistroRepository;
        $issArqSimplesRegRepository->limparErrosExistentesDoArquivo($this->sequencialArquivo);

        $dataArquivoSimples = $issArqSimplesRegRepository->getDataArquivoSimples($this->sequencialArquivo);
        if (empty($dataArquivoSimples)) {
            throw new BusinessException("Não Foi Encontrado o Arquivo Simples {$this->sequencialArquivo}");
        }

        $registros = $issArqSimplesRegRepository->getRegistrosByArquivo($this->sequencialArquivo);
        if (empty($registros)) {
            throw new BusinessException("Nenhum Registro Encontrado");
        }

        foreach ($registros as $registro) {
            $dVlrReg = ($registro->q23_vlrprinc + $registro->q23_vlrjur + $registro->q23_vlrmul);
            $iNumpre = null;
            $iNumpar = null;
            $cgmVinculados = $registro->getCgmVinculos();
            $inscricoesVinculadas = $registro->getInscricaoVinculos();
            $dadosParaPagamento = $registro->getDadosParaPagamento();
            $competenciaRegistro = "{$registro->q23_mesusu}/{$registro->q23_anousu}";
            $sequencialRegistro = $registro->q23_sequencial;

            if (empty($dadosParaPagamento->cgm)) {
                $message = "Não existe no cadastro do CGM";
                $this->lancarErro($message, $dadosParaPagamento, $sequencialRegistro);
            }

            if (count($cgmVinculados) > 1) {
                $cgmDuplicados = implode(',', $cgmVinculados);
                $message = "Cadastro nos cgms ({$cgmDuplicados})";
                $this->lancarErro($message, $dadosParaPagamento, $sequencialRegistro);
            }

            if (!empty($dadosParaPagamento->inscricao)) {
                $oDaoissarqsimplesregissbase = new cl_issarqsimplesregissbase;
                $oDaoissarqsimplesregissbase->excluir(null, "q134_issarqsimplesreg = {$sequencialRegistro}");
                $oDaoissarqsimplesregissbase->q134_inscr = $dadosParaPagamento->inscricao;
                $oDaoissarqsimplesregissbase->q134_issarqsimplesreg = $registro->q23_sequencial;
                $oDaoissarqsimplesregissbase->incluir(null);
                if ($oDaoissarqsimplesregissbase->erro_status == "0") {
                    throw new BusinessException($oDaoissarqsimplesregissbase->erro_msg);
                }
            }

            /**
             * Lancamento Complementar
             */
            if ($registro->q23_acao == 1) {
                $rsNumpre = db_query("select nextval('numpref_k03_numpre_seq') as k03_numpre");
                $oNumpre = db_utils::fieldsmemory($rsNumpre, 0);
                $iNumpre = $oNumpre->k03_numpre;
                $iNumpar = $registro->q23_mesusu;

                $oDaoIssVar = new cl_issvar;
                $oDaoIssVar->q05_numpre = $oNumpre->k03_numpre;
                $oDaoIssVar->q05_numpar = $iNumpar;
                $oDaoIssVar->q05_ano = $registro->q23_anousu;
                $oDaoIssVar->q05_mes = $registro->q23_mesusu;
                $oDaoIssVar->q05_histor = "Referente a Baixa Simples nacional competencia {$competenciaRegistro}";
                $oDaoIssVar->q05_aliq = $oParametrosISSQN->q60_aliq;
                $oDaoIssVar->q05_valor = "0";
                $oDaoIssVar->q05_bruto = "0";
                $oDaoIssVar->q05_vlrinf = $dVlrReg;
                $oDaoIssVar->incluir(null);

                if ($oDaoIssVar->erro_status == "0") {
                    throw new BusinessException("Iss Complementar" . $oDaoIssVar->erro_msg);
                }

                $q05_codigo = $oDaoIssVar->q05_codigo;
                $oDaoArrecad = new cl_arrecad;
                $oDaoArrecad->k00_numpre = $oNumpre->k03_numpre;
                $oDaoArrecad->k00_numpar = $iNumpar;
                $oDaoArrecad->k00_numcgm = $dadosParaPagamento->cgm;
                $oDaoArrecad->k00_dtoper = $registro->q23_dtvenc;
                $oDaoArrecad->k00_receit = $oParametrosISSQN->q60_receit;
                $oDaoArrecad->k00_hist   = $oParametrosISSQN->q60_histsemmov;
                $oDaoArrecad->k00_valor  = $dVlrReg;
                $oDaoArrecad->k00_dtvenc = $oDaoIssarqsimples->getDatavenc(
                    $registro->q23_anousu,
                    $registro->q23_mesusu
                );
                $oDaoArrecad->k00_numtot = 1;
                $oDaoArrecad->k00_numdig = "0";
                $oDaoArrecad->k00_tipo   = $oParametrosISSQN->q60_tipo;
                $oDaoArrecad->k00_tipojm = "0";
                $oDaoArrecad->incluir(null);

                if ($oDaoArrecad->erro_status == 0) {
                    throw new BusinessException("ARRECAD :  ". $oDaoArrecad->erro_msg);
                }

                if (!empty($dadosParaPagamento->inscricao)) {
                    $oDaoArreinscr = new cl_arreinscr;
                    $oDaoArreinscr->k00_inscr = $dadosParaPagamento->inscricao;
                    $oDaoArreinscr->k00_numpre = $iNumpre;
                    $oDaoArreinscr->k00_perc = 100;
                    $oDaoArreinscr->incluir($iNumpre, $dadosParaPagamento->inscricao);

                    if ($oDaoArreinscr->erro_status == 0) {
                        throw new BusinessException("Arreinscr : ". $oDaoArreinscr->erro_msg);
                    }
                }
            }

            /**
             * Lancamento Normal
             */
            if ($registro->q23_acao == 0) {
                if (empty($dadosParaPagamento->inscricao)) {
                    $this->lancarErro("Sem Inscrição Ativa", $dadosParaPagamento, $sequencialRegistro);
                }

                if (count($inscricoesVinculadas) > 1) {
                    $inscricoesDuplicadas = implode(", ", $inscricoesVinculadas);
                    $message = "Cadastro com Mais de um alvará {$inscricoesDuplicadas}";
                    $this->lancarErro($message, $dadosParaPagamento, $sequencialRegistro);
                }

                $oDaoIssCalc = new cl_isscalc;
                $oDaoIssVar = new cl_issvar;

                $rsIssCalc = db_query($oDaoIssCalc->sql_query(
                    $registro->q23_anousu,
                    $dadosParaPagamento->inscricao,
                    3
                ));

                $lMEI = false;
                $oDaoIsscadsimples = new cl_isscadsimples();
                $sWhere = " cgm.z01_cgccpf = '{$dadosParaPagamento->cnpj}' ";
                $sSqlMEI = $oDaoIsscadsimples->sql_query(
                    null,
                    'q02_inscr, q38_sequencial, q38_categoria',
                    null,
                    $sWhere
                );

                $rsMEI = db_query($sSqlMEI);
                if (!$rsMEI) {
                    $message = "Erro verificar Inscrição MEI. ".pg_last_error();
                    $this->lancarErro($message, $dadosParaPagamento, $sequencialRegistro);
                }

                if (pg_num_rows($rsMEI) > 0) {
                    $sCategoria = db_utils::fieldsMemory($rsMEI, 0)->q38_categoria;
                    $iIsscadsimples = (int) db_utils::fieldsMemory($rsMEI, 0)->q38_sequencial;
                    $oDaoIsscadsimplesbaixa = new cl_isscadsimplesbaixa;
                    $sWhere = " q39_isscadsimples = {$iIsscadsimples} ";
                    $sSqlBaixaMEI = $oDaoIsscadsimplesbaixa->sql_query(
                        null,
                        'q39_sequencial, q39_dtbaixa',
                        null,
                        $sWhere
                    );

                    $rsBaixaMEI = db_query($sSqlBaixaMEI);
                    if (!$rsBaixaMEI) {
                        $message = "Erro verificar baixa Inscrição MEI. ".pg_last_error();
                        $this->lancarErro($message, $dadosParaPagamento, $sequencialRegistro);
                    }
                }

                $anoPag = substr($registro->q23_dtarrec, 0, 4);
                $mesPag = substr($registro->q23_dtarrec, 5, 2);
                $lcomplementar = false;

                if ($registro->q23_anousu == $anoPag && $registro->q23_mesusu == $mesPag && (!$lMEI)) {
                    $lcomplementar = true;
                    $rsNumpre = db_query("select nextval('numpref_k03_numpre_seq') as k03_numpre");
                    $iNumpre = db_utils::fieldsmemory($rsNumpre, 0)->k03_numpre;
                    $iNumpar = $registro->q23_mesusu;

                    $oDaoIssVar = new cl_issvar;
                    $oDaoIssVar->q05_numpre = $iNumpre;
                    $oDaoIssVar->q05_numpar = $iNumpar;
                    $oDaoIssVar->q05_ano = $registro->q23_anousu;
                    $oDaoIssVar->q05_mes = $registro->q23_mesusu;
                    $oDaoIssVar->q05_histor = "Referente a Baixa Simples nacional competencia {$competenciaRegistro}";
                    $oDaoIssVar->q05_aliq = $oParametrosISSQN->q60_aliq;
                    $oDaoIssVar->q05_valor = "0";
                    $oDaoIssVar->q05_bruto = "0";
                    $oDaoIssVar->q05_vlrinf = $dVlrReg;
                    $oDaoIssVar->incluir(null);

                    if ($oDaoIssVar->erro_status == 0) {
                        throw new BusinessException("Iss Complementar : ".$oDaoIssVar->erro_msg);
                    }

                    $q05_codigo = $oDaoIssVar->q05_codigo;
                    $oDaoArrecad = new cl_arrecad;
                    $oDaoArrecad->k00_numpre = $iNumpre;
                    $oDaoArrecad->k00_numpar = $iNumpar;
                    $oDaoArrecad->k00_numcgm = $dadosParaPagamento->cgm;
                    $oDaoArrecad->k00_dtoper = $registro->q23_dtvenc;
                    $oDaoArrecad->k00_receit = $oParametrosISSQN->q60_receit;
                    $oDaoArrecad->k00_hist = $oParametrosISSQN->q60_histsemmov;
                    $oDaoArrecad->k00_valor = $dVlrReg;
                    $oDaoArrecad->k00_dtvenc = $oDaoIssarqsimples->getDatavenc(
                        $registro->q23_anousu,
                        $registro->q23_mesusu
                    );
                    $oDaoArrecad->k00_numtot = 1;
                    $oDaoArrecad->k00_numdig = "0";
                    $oDaoArrecad->k00_tipo = $oParametrosISSQN->q60_tipo;
                    $oDaoArrecad->k00_tipojm = "0";
                    $oDaoArrecad->incluir(null);

                    if ($oDaoArrecad->erro_status == 0) {
                        throw new BusinessException("ARRECAD : ". $oDaoArrecad->erro_msg);
                    }

                    $oDaoArreinscr = new cl_arreinscr;
                    $oDaoArreinscr->k00_inscr = $dadosParaPagamento->inscricao;
                    $oDaoArreinscr->k00_numpre = $iNumpre;
                    $oDaoArreinscr->k00_perc = 100;
                    $oDaoArreinscr->incluir($iNumpre, $dadosParaPagamento->inscricao);

                    if ($oDaoArreinscr->erro_status == 0) {
                        throw new BusinessException("Arreinscr : ". $oDaoArreinscr->erro_msg);
                    }
                }

                $rsVar = db_query($oDaoIssVar->sql_issvar_isscalc_inscr_comp(
                    $dadosParaPagamento->inscricao,
                    $registro->q23_anousu,
                    $registro->q23_mesusu
                ));

                if (pg_num_rows($rsVar) < 1) {
                    $message = "Sem lançamento ISS variavel para {$competenciaRegistro}";
                    $this->lancarErro($message, $dadosParaPagamento, $sequencialRegistro);
                }

                $oDaoArrecad = new cl_arrecad;
                $oCalc = db_utils::fieldsMemory($rsIssCalc, 0);
                $oIssVar = db_utils::fieldsMemory($rsVar, 0);
                $q05_codigo = $oIssVar->q05_codigo;
                $sCamposArrecad = " k00_numpre, k00_numpar, k00_hist, k00_dtoper ";
                $sWhereArrecad = " k00_numpre = {$oIssVar->q05_numpre} ";
                $sWhereArrecad .= " and k00_numpar = {$oIssVar->q05_numpar} ";
                $sWhereArrecad .= " and exists (select 1 ";
                $sWhereArrecad .= " from isscalc ";
                $sWhereArrecad .= " where q01_numpre = {$oIssVar->q05_numpre} ";
                $sWhereArrecad .= " and q01_inscr  = {$dadosParaPagamento->inscricao}) ";

                $sSqlArrecad = $oDaoArrecad->sql_query_file(null, $sCamposArrecad, null, $sWhereArrecad);
                $rsArrecad = db_query($sSqlArrecad);

                if (!$rsArrecad) {
                    throw new BusinessException("Erro ao buscar dados do Arrecad:".pg_last_error());
                }

                if (pg_num_rows($rsArrecad) != 1 && !$lcomplementar) {
                    $rsNumpre = db_query("select nextval('numpref_k03_numpre_seq') as k03_numpre");
                    $iNumpre = db_utils::fieldsmemory($rsNumpre, 0)->k03_numpre;
                    $iNumpar = $registro->q23_mesusu;

                    $oDaoIssVar = new cl_issvar;
                    $oDaoIssVar->q05_numpre = $iNumpre;
                    $oDaoIssVar->q05_numpar = $iNumpar;
                    $oDaoIssVar->q05_ano = $registro->q23_anousu;
                    $oDaoIssVar->q05_mes = $registro->q23_mesusu;
                    $oDaoIssVar->q05_histor = "Referente a Baixa Simples nacional competencia {$competenciaRegistro}";
                    $oDaoIssVar->q05_aliq = $oParametrosISSQN->q60_aliq;
                    $oDaoIssVar->q05_valor = "0";
                    $oDaoIssVar->q05_bruto = "0";
                    $oDaoIssVar->q05_vlrinf = $dVlrReg;
                    $oDaoIssVar->incluir(null);

                    if ($oDaoIssVar->erro_status == 0) {
                        throw new BusinessException("Iss Complementar : ".$oDaoIssVar->erro_msg);
                    }

                    $q05_codigo = $oDaoIssVar->q05_codigo;
                    $oDaoArrecad = new cl_arrecad;
                    $oDaoArrecad->k00_numpre = $iNumpre;
                    $oDaoArrecad->k00_numpar = $iNumpar;
                    $oDaoArrecad->k00_numcgm = $dadosParaPagamento->cgm;
                    $oDaoArrecad->k00_dtoper = $registro->q23_dtvenc;
                    $oDaoArrecad->k00_receit = $oParametrosISSQN->q60_receit;
                    $oDaoArrecad->k00_hist = $oParametrosISSQN->q60_histsemmov;
                    $oDaoArrecad->k00_valor = $dVlrReg;
                    $oDaoArrecad->k00_dtvenc = $oDaoIssarqsimples->getDatavenc(
                        $registro->q23_anousu,
                        $registro->q23_mesusu
                    );
                    $oDaoArrecad->k00_numtot = 1;
                    $oDaoArrecad->k00_numdig = "0";
                    $oDaoArrecad->k00_tipo = $oParametrosISSQN->q60_tipo;
                    $oDaoArrecad->k00_tipojm = "0";
                    $oDaoArrecad->incluir(null);

                    if ($oDaoArrecad->erro_status == 0) {
                        throw new BusinessException("ARRECAD : ". $oDaoArrecad->erro_msg);
                    }

                    $oDaoArreinscr = new cl_arreinscr;
                    $oDaoArreinscr->k00_inscr = $dadosParaPagamento->inscricao;
                    $oDaoArreinscr->k00_numpre = $iNumpre;
                    $oDaoArreinscr->k00_perc = 100;
                    $oDaoArreinscr->incluir($iNumpre, $dadosParaPagamento->inscricao);

                    if ($oDaoArreinscr->erro_status == 0) {
                        throw new BusinessException("Arreinscr : ". $oDaoArreinscr->erro_msg);
                    }
                }

                if ($iNumpre == null) {
                    $iNumpre = $oIssVar->q05_numpre;
                    $iNumpar = $oIssVar->q05_numpar;
                }

                // gerar cacelamento dos demais debitos
                if (!empty($registro->getInscricoesParaCancelamento())) {
                    foreach ($registro->getInscricoesParaCancelamento() as $dadosParaCancelamento) {
                        $this->gerarCancelamentoDebito($registro, $dadosParaCancelamento, $dadosParaPagamento);
                    }
                }
            }

            //Cadastramos na disbanco e issarqsimplesregdisbanco
            $oDaoDisbanco = new cl_disbanco;
            $oDaoDisbanco->codret = $oDaoDisArq->codret;
            $oDaoDisbanco->k15_codbco = $this->banco;
            $oDaoDisbanco->k15_codage = $this->agencia;
            $oDaoDisbanco->dtarq = $dataArquivoSimples;
            $oDaoDisbanco->dtpago = $registro->q23_dtarrec;
            $oDaoDisbanco->vlrpago = $dVlrReg;
            $oDaoDisbanco->vlrjuros = "0";
            $oDaoDisbanco->vlrmulta = "0";
            $oDaoDisbanco->vlrdesco = "0";
            $oDaoDisbanco->cedente = null;
            $oDaoDisbanco->vlrtot = $dVlrReg;
            $oDaoDisbanco->vlrcalc = "0";
            $oDaoDisbanco->classi = 'false';
            $oDaoDisbanco->k00_numpre = $iNumpre;
            $oDaoDisbanco->k00_numpar = $iNumpar;
            $oDaoDisbanco->instit = db_getsession('DB_instit');
            $oDaoDisbanco->convenio = null;
            $oDaoDisbanco->dtcredito = $registro->q23_dtarrec;
            $oDaoDisbanco->incluir(null);

            if ($oDaoDisbanco->erro_status == 0) {
                $sErroMsg = "erro disbanco ".$oDaoDisbanco->erro_msg;
                throw new BusinessException($sErroMsg);
            }

            $oDaoIssArqSimplesRegDisbanco = new cl_issarqsimplesregdisbanco;
            $oDaoIssArqSimplesRegDisbanco->q44_issarqsimplesreg = $registro->q23_sequencial;
            $oDaoIssArqSimplesRegDisbanco->q44_disbanco = $oDaoDisbanco->idret;
            $oDaoIssArqSimplesRegDisbanco->incluir(null);

            if ($oDaoIssArqSimplesRegDisbanco->erro_status == 0) {
                $sErroMsg  = " ".$oDaoIssArqSimplesRegDisbanco->erro_msg;
                throw new BusinessException($sErroMsg);
            }

            $oDaoIssArqSimplesRegIssVar = new cl_issarqsimplesregissvar;
            $oDaoIssArqSimplesRegIssVar->q68_issvar = $q05_codigo;
            $oDaoIssArqSimplesRegIssVar->q68_issarqsimplesreg = $registro->q23_sequencial;
            $oDaoIssArqSimplesRegIssVar->incluir(null);

            if ($oDaoIssArqSimplesRegIssVar->erro_status == 0) {
                $sErroMsg = " Erro ao incluir issarqsimplesregissvar. Contate o suporte ";
                $sErroMsg .= $oDaoIssArqSimplesRegIssVar->erro_msg;
                throw new BusinessException($sErroMsg);
            }

            if (!$lcomplementar) {
                $oDaoIssvar = new cl_issvar;
                $sSqlIssvar = $oDaoIssvar->sql_update_vlrinf_if_null($q05_codigo, $dVlrReg);
                $rsIssvar = db_query($sSqlIssvar);
                if (!$rsIssvar) {
                    throw new BusinessException("Erro ao atualizar Issvar:".pg_last_error());
                }
            }

            $oDaoArrecad = new cl_arrecad;
            $oDaoArrecad->k00_dtvenc = $oDaoIssarqsimples->getDatavenc(
                $registro->q23_anousu,
                $registro->q23_mesusu
            );
            $oDaoArrecad->alterar(null, "k00_numpre = {$iNumpre} and k00_numpar = {$iNumpar}");

            if ($oDaoArrecad->erro_status == 0) {
                throw new BusinessException($oDaoArrecad->erro_msg);
            }

            $oDaoArrehist = new cl_arrehist;
            $oDaoArrehist->k00_numpre = $iNumpre;
            $oDaoArrehist->k00_numpar = $iNumpar;
            $oDaoArrehist->k00_hist = $oParametrosISSQN->q60_histsemmov;
            $oDaoArrehist->k00_dtoper = $registro->q23_dtvenc;
            $oDaoArrehist->k00_hora = db_hora();
            $oDaoArrehist->k00_id_usuario = db_getsession("DB_id_usuario");
            $oDaoArrehist->k00_histtxt = "BAIXA SIMPLES NACIONAL COMPETÊNCIA {$competenciaRegistro}";
            $oDaoArrehist->k00_limithist = date("Y-m-d", db_getsession("DB_datausu"));
            $oDaoArrehist->incluir(null);

            if ($oDaoArrehist->erro_status == 0) {
                throw new BusinessException("Arrehist:".$oDaoArrehist->erro_msg);
            }
        }
    }

    /**
     * Lanca erro e finaliza o processamento
     *
     * @param string $detalhe Descricao do erro
     * @param object $dados Objeto com os dados do registro (cnpj, cgm, inscricao)
     * @param numeric $sequencialRegistro Sequencial do registro
     * @throws BusinessException
     * @return never
     */
    private function lancarErro($detalhe, $dados, $sequencialRegistro)
    {
        $message = "";
        if (!empty($dados->cnpj)) {
            $message .= "CNPJ - {$dados->cnpj}";
        }

        if (!empty($dados->cgm)) {
            $message .= ", CGM - {$dados->cgm}";
        }

        if (!empty($dados->inscricao)) {
            $message .= ", Álvara - {$dados->inscricao}";
        }

        $message .= ": {$detalhe}, Registro: {$sequencialRegistro}";
        throw new BusinessException(urldecode($message));
    }

    private function gerarCancelamentoDebito($registro, $dadosParaCancelamento, $dadosParaPagamento)
    {
        $issVar = new cl_issvar;
        $rsIssVar = db_query($issVar->sql_issvar_isscalc_inscr_comp(
            $dadosParaCancelamento->inscr,
            $registro->q23_anousu,
            $registro->q23_mesusu
        ));

        if (!$rsIssVar) {
            throw new BusinessException('Erro ao buscar issvar para cancelamento: '.pg_last_error());
        }

        if (pg_num_rows($rsIssVar) < 1) {
            return;
        }

        $dadosIssVar = db_utils::fieldsMemory($rsIssVar, 0);
        $arrecad = new cl_arrecad;
        $camposArrecad = "k00_numpre, k00_numpar, k00_receit";
        $whereArrecad = "
            k00_numpre = {$dadosIssVar->k00_numpre}
            and k00_numpar = {$dadosIssVar->q05_numpar}
            and exists (
                select 1 from isscalc where k00_numpre = {$dadosIssVar->k00_numpre}
                and q01_inscr = {$dadosParaCancelamento->inscr}
            )
        ";
        $rsArrecad = db_query($arrecad->sql_query_file(null, $camposArrecad, null, $whereArrecad));
        if (!$rsArrecad) {
            throw new BusinessException("[cancelamento debitos] Erro ao buscar dados do Arrecad:".pg_last_error());
        }

        if (pg_num_rows($rsArrecad) < 1) {
            return;
        }

        $debitos = db_utils::getCollectionByRecord($rsArrecad, 0);
        $debitosParaCancelar = [];
        foreach ($debitos as $debito) {
            $debitosParaCancelar[] = [
                'Numpre' => $debito->k00_numpre,
                'Numpar' => $debito->k00_numpar,
                'Receita' => $debito->k00_receit
            ];
        }

        $obsCancelamento = "PAGO NO ARQ. SIMPLES NA INSCR: {$dadosParaPagamento->inscricao}";
        $cancelaDebitos = new cancelamentoDebitos;
        $cancelaDebitos->setArreHistTXT($obsCancelamento);
        $cancelaDebitos->setHistoricoProcessamento($obsCancelamento);
        $cancelaDebitos->geraCancelamento($debitosParaCancelar);
    }
}
