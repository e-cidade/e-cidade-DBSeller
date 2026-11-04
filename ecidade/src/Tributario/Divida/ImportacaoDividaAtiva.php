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

namespace ECidade\Tributario\Divida;

use cl_arrenumcgm;
use db_utils;
use ECidade\Tributario\Arrecadacao\CadTipo;
use ECidade\Tributario\Divida\Service\TermoInscricaoService;
use Exception;

/**
 * Class ImportacaoDividaAtiva
 *
 * @package ECidade\Tributario\Divida
 */
class ImportacaoDividaAtiva extends \ImportacaoDiversosCobrancaAdministrativa
{
    const TIPO_ORIGEM_PARCELAMENTO_COBRANCA_ADM = 14;
    /**
     * Tipos para buscar as Observacoes
     *
     * @var array
     */
    private $aTipos = array(3, 7, 4, 11, 16, 17, 19);

    /**
     * Codigo da Importacao
     *
     * @var integer
     */
    protected $codigoImportacao;

    /**
     * Define se deve vincular a matricula e inscricoes de debito
     *
     * @var bool
     */
    private $vincularMatriculasInscricoes = true;

    /**
     * @var \ProcedenciaDivida[]
     */
    protected $aReceitaProcedencia = array();

    /**
     * @var array
     */
    protected $aCertidaoDivida = array();

    /**
     * @var \processoProtocolo
     */
    protected $processo;

    /**
     * @var \stdClass
     */
    protected $stdProcesso;

    public function setDividasArrecad($aDividasArreca)
    {
        $this->aDividasArrecad = $aDividasArreca;
    }

    public function getDividasArrecad()
    {
        return $this->aDividasArrecad;
    }

    /**
     * @param $codigoReceita
     * @param \ProcedenciaDivida $procedenciaDivida
     */
    public function adicionarReceitaProcedencia($codigoReceita, \ProcedenciaDivida $procedenciaDivida)
    {
        $this->aReceitaProcedencia[$codigoReceita] = $procedenciaDivida;
        $this->aReceitaVencimento[$codigoReceita] = null;
    }

    /**
     * @param string $sWhere
     * @return boolean true
     * @throws \Exception
     * @throws \DBException
     */
    protected function importar($sWhere)
    {
        $this->onProgressBarSetMessageLog("Buscando débitos na base de dados (Etapa 3/11)");

        if ($this->getTipoDebitoOrigem() == self::TIPO_ORIGEM_PARCELAMENTO_COBRANCA_ADM) {
            $rsBuscaDebitos = $this->buscarDebitos($sWhere);
        } else {
            $rsBuscaDebitos = $this->buscarDebitosPorParcela($sWhere);
        }

        $iTotalRegistros = pg_num_rows($rsBuscaDebitos);

        if ($iTotalRegistros == 0) {
            throw new \Exception("Nenhum registro encontrado.");
        }

        $this->oDadosDebitos = \db_utils::getCollectionByRecord($rsBuscaDebitos);
        $this->adicionaObservacaoOrigemParcelamento();
        $this->validarOrigem();

        $daoDividaImportada = new \cl_divimporta();
        $daoDividaImportada->v02_divimporta = null;
        $daoDividaImportada->v02_usuario = db_getsession('DB_id_usuario');
        $daoDividaImportada->v02_data = date('Y-m-d', db_getsession('DB_datausu'));
        $daoDividaImportada->v02_hora = db_hora();
        $daoDividaImportada->v02_horafim = db_hora();
        $daoDividaImportada->v02_datafim = date('Y-m-d', db_getsession('DB_datausu'));
        $daoDividaImportada->v02_tipo = "1"; // MUDAR O TIPO
        $daoDividaImportada->v02_instit = \InstituicaoRepository::getInstituicaoSessao()->getCodigo();
        $daoDividaImportada->incluir($daoDividaImportada->v02_divimporta);
        if ($daoDividaImportada->erro_status === '0') {
            throw new \DBException("Erro ao incluir os dados da importação");
        }

        $this->codigoImportacao = $daoDividaImportada->v02_divimporta;

        $this->buscarLivroFolha();

        $this->criarHistoricoDebitos();

        $relacaoNumpreAntigoNovo = array();

        foreach ($this->oDadosDebitos as $stdDebitoOrigem) {
            $relacaoNumpreAntigoNovo[$stdDebitoOrigem->k00_numpre] = \cl_numpref::getNumpre();
        }

        $this->onProgressBarUpdateMaxProgress($iTotalRegistros - 1);

        if ($this->getTipoDebitoOrigem() == self::TIPO_ORIGEM_PARCELAMENTO_COBRANCA_ADM) {
            $this->processarDebitosParcelamento($relacaoNumpreAntigoNovo);
        } else {
            $this->processarDebitos($relacaoNumpreAntigoNovo);
        }

        unset($this->oDadosDebitos);
        unset($this->oDadosParcelamento);

        if ($this->getTipoDebitoOrigem() == self::TIPO_ORIGEM_PARCELAMENTO_COBRANCA_ADM) {
            $this->emitirCertidaoParcelamento();
        } else {
            $this->emitirCertidao();
        }

        $this->atualizaArreforo();

        unset($this->aDividasArrecad);
        unset($this->aDividasArrecadParcelamento);

        $this->emitirInicial();

        return true;
    }

    /**
     * Função que emite as certdões de acordo com as dívidas geradas
     */
    protected function emitirCertidao()
    {
        $this->onProgressBarSetMessageLog("Processa geração das certidões das dívidas (Etapa 10/11)");
        $this->onProgressBarUpdateMaxProgress(count($this->aDividasArrecad));

        $contadorRegistros = 0;
        $this->onProgressBarUpdatePercentual($contadorRegistros);

        $cda = new \cda(null);

        $this->reagruparDividas();

        foreach ($this->aDividasArrecad as $indiceDono => $aDividasArrecad) {
            $contadorRegistros++;

            foreach ($aDividasArrecad as $aDivida) {
                $indiceDonos = explode("-", $indiceDono);
                $dados = new \stdClass();
                $dados->cgm = $indiceDonos[0];
                $dados->dividas = $aDivida;
                $dados->certidao = $cda->geraLoteCertidao(CadTipo::DIVIDA_ATIVA, $aDivida);

                $this->aCertidaoDivida[] = $dados;
            }

            $this->onProgressBarUpdatePercentual($contadorRegistros);
        }
    }

    /**
     * Função que emite as certdões de acordo com as dívidas geradas de parcelamento
     */
    protected function emitirCertidaoParcelamento()
    {
        $this->onProgressBarSetMessageLog("Processa geração das certidões das dívidas (Etapa 10/11)");
        $this->onProgressBarUpdateMaxProgress(count($this->aDividasArrecad));

        $contadorRegistros = 0;
        $this->onProgressBarUpdatePercentual($contadorRegistros);

        $cda = new \cda(null);

        $this->reagruparDividasParcelamento();

        foreach ($this->aDividasArrecadParcelamento as $indiceDono => $aDividasArrecad) {
            $contadorRegistros++;

            foreach ($aDividasArrecad as $aDivida) {
                $indiceDonos = explode("-", $indiceDono);
                $dados = new \stdClass();
                $dados->cgm = $indiceDonos[0];
                $dados->dividas = $aDivida;
                $dados->certidao = $cda->geraLoteCertidao(CadTipo::DIVIDA_ATIVA, $aDivida);

                $this->aCertidaoDivida[] = $dados;
            }

            $this->onProgressBarUpdatePercentual($contadorRegistros);
        }
    }

    /**
     * Função adicinada para atualizar o tipo de débito do arreforo para divida ativa,
     * para caso cancelarem iniciais e cda o débito nao ficar como tipo 34 ou 14.
     * Feito no redmine 13467
     */
    protected function atualizaArreforo()
    {
        foreach ($this->aDividasArrecad as $indiceDono => $aDividasArrecad) {
            foreach ($aDividasArrecad as $aDivida) {
                foreach ($aDivida as $oDivida) {
                    db_query("update arreforo set k00_tipo = 5 where k00_numpre = $oDivida->iNumpre;");
                }
            }
        }
    }

    /**
     * @throws \DBException
     */
    protected function emitirInicial()
    {
        $this->onProgressBarSetMessageLog("Processa geração de iniciais do foro (Etapa 11/11)");
        $this->onProgressBarUpdateMaxProgress(count($this->aCertidaoDivida));

        $contadorRegistros = 0;
        $this->onProgressBarUpdatePercentual($contadorRegistros);

        $daoAdvogado = new \cl_parjuridico();
        $sqlAdvogado = $daoAdvogado->sql_query_advog(
            db_getsession('DB_anousu'),
            db_getsession('DB_instit'),
            'v19_advogadopadrao as cgm'
        );
        $rsAdvogado = db_query($sqlAdvogado);

        if (!$rsAdvogado) {
            throw new \DBException("Erro ao buscar os dados do advogado.");
        }

        if (pg_num_rows($rsAdvogado) == 0) {
            $mensagemAdvogado = "Não existe um advogado padrão cadastro.\n";
            $mensagemAdvogado .= "Para cadastrar, acesse a rotina:\n";
            $mensagemAdvogado .= "DB:TRIBUTÁRIO > Jurídico > Procedimentos > Manutenção de Parametros";
            throw new \DBException($mensagemAdvogado);
        }

        $advogado = pg_fetch_object($rsAdvogado);

        foreach ($this->aCertidaoDivida as $dadosCertidao) {
            $contadorRegistros++;

            $inicial = new \inicial();
            $inicial->setCodigoAdvogado($advogado->cgm);
            $inicial->setCodigoLocal("0");
            $inicial->setCodigoMovimentacao("0");
            $inicial->setInstituicao(db_getsession("DB_instit"));
            $inicial->setUsuario(db_getsession("DB_id_usuario"));
            $inicial->setData(date("Y-m-d", db_getsession("DB_datausu")));
            $inicial->setSituacao(1);
            $inicial->salvar();

            $numpresInceridos = array();

            foreach ($dadosCertidao->dividas as $divida) {
                if (in_array($divida->iNumpre, $numpresInceridos)) {
                    continue;
                }

                $daoInicialNumpre = new \cl_inicialnumpre();
                $daoInicialNumpre->v59_inicial = $inicial->getCodigoInicial();
                $daoInicialNumpre->v59_numpre = $divida->iNumpre;
                $resultadoNumpre = $daoInicialNumpre->incluir();

                if (!$resultadoNumpre) {
                    throw new \DBException("Erro ao incluir os débitos na inicial do foro. ");
                }

                $numpresInceridos[] = $divida->iNumpre;
            }

            $daoInicialNomes = new \cl_inicialnomes();
            $resultadoCgm = $daoInicialNomes->incluir($inicial->getCodigoInicial(), $dadosCertidao->cgm);

            if (!$resultadoCgm) {
                throw new \DBException("Erro ao vincular inicial ao CGM.");
            }

            $daoInicialCert = new \cl_inicialcert();
            $retornoCertidao = $daoInicialCert->incluir($inicial->getCodigoInicial(), $dadosCertidao->certidao);

            if (!$retornoCertidao) {
                throw new \DBException("Erro ao vincular a Certidão de Dívida na Inicial do Foro.");
            }

            $observacoes = "Movimentação gerada automaticamente pela importação para a dívida ativa.";
            $inicial->adicionarMovimentacao(1, $observacoes);
            $inicial->salvar();

            $daoParametros = new \cl_pardiv();
            $where = "v04_instit = " . db_getsession('DB_instit');
            $sqlParametro = $daoParametros->sql_query_file(null, "v04_tipoinicial", null, $where);
            $rsParametro = $daoParametros->sql_record($sqlParametro);

            if (!$rsParametro) {
                throw new \DBException("Erro ao consultar os parâmentros de configuração do Módulo Dívida Ativa.");
            }

            $parametros = \db_utils::fieldsMemory($rsParametro, 0);

            foreach ($dadosCertidao->dividas as $divida) {
                $daoArrecad = new \cl_arrecad();
                $daoArrecad->k00_tipo = $parametros->v04_tipoinicial;
                $resuladoArrecad = $daoArrecad->alterar_arrecad("k00_numpre = {$divida->iNumpre}");

                if (!$resuladoArrecad) {
                    throw new \DBException("Erro ao trocar o tipo de débito para inicial do foro.");
                }

                $this->geraArrenumcgm($dadosCertidao->cgm, $divida->iNumpre);
            }

            $this->onProgressBarUpdatePercentual($contadorRegistros);
        }
    }

    /**
     * Funcao para gerar arrenumcgm dos debitos caso nao exista
     * @param int $iNumcgm
     * @param int $iNumpre
     * @return void
     */
    private function geraArrenumcgm($iNumcgm, $iNumpre)
    {
        $oDaoArrenumCgm = new cl_arrenumcgm();
        $sSqlBuscaRegistros = $oDaoArrenumCgm->sql_query_file($iNumcgm, $iNumpre);
        $rsBuscaRegistros = db_query($sSqlBuscaRegistros);
        if (pg_num_rows($rsBuscaRegistros) == 0) {
            $oDaoArrenumCgm->incluir($iNumcgm, $iNumpre);
        }
    }

    /**
     * @param \stdClass $debito
     * @param  $indiceParcela
     * @return bool true
     * @throws \DBException|\ParameterException
     */
    protected function incluirDebito(\stdClass $debito, $indiceParcela, $numpreNovo)
    {
        $procedenciaDivida = $this->aReceitaProcedencia[$debito->k00_receit];
        $valoresParcelas = explode(',', $debito->colecao_valor);
        $datasVencimento = explode(',', $debito->data_vencimento);
        $historicos = explode(',', $debito->colecao_historico);
        $parcelas = explode(',', $debito->colecao_numpar);
        $dataOperacao = explode(',', $debito->colecao_dtoper);
        $daoArrecad = new \cl_arrecad();
        $daoArrecad->k00_numpre = $numpreNovo;
        $daoArrecad->k00_numpar = $this->lUnificarDebitos ? 1 : $parcelas[$indiceParcela];
        $daoArrecad->k00_numcgm = $debito->k00_numcgm;
        $daoArrecad->k00_dtoper = $dataOperacao[$indiceParcela];
        $daoArrecad->k00_receit = $procedenciaDivida->getReceitaDivida();
        $daoArrecad->k00_hist = $historicos[$indiceParcela];
        $daoArrecad->k00_valor = $this->lUnificarDebitos ? $debito->k00_valor : $valoresParcelas[$indiceParcela];

        $daoArrecad->k00_dtvenc = $datasVencimento[$indiceParcela];

        if ($this->lUnificarDebitos) {
            $daoArrecad->k00_dtvenc = $this->maiorDataVencimento() ?
                $debito->maior_data_vencimento :
                $debito->menor_data_vencimento;

            if ($this->escolhaDataVencimento()) {
                $daoArrecad->k00_dtvenc = $this->getDataVencimento()->getDate();
            }
        }

        $daoArrecad->k00_numtot = $this->lUnificarDebitos ? 1 : $debito->k00_numtot;
        $daoArrecad->k00_numdig = 1;
        $daoArrecad->k00_tipo = $procedenciaDivida->getTipoDebito();
        $daoArrecad->k00_tipojm = '0';

        $daoArrecad->incluir();
        if ($daoArrecad->erro_status === "0") {
            throw new \DBException("Não foi possível salvar os dados do novo débito.");
        }

        /**
         * Logica implementada para vincular somente uma vez as matriculas/inscricoes no debito novo criado
         */
        if ($this->vincularMatriculasInscricoes === true) {
            $this->vincularMatriculasInscricoes = false;

            if (!empty($debito->colecao_matricula)) {
                foreach (explode(',', $debito->colecao_matricula) as $indice => $matriculas) {
                    list($codigoMatricula, $percentualMatricula) = explode('|', $matriculas);
                    $daoArreMatricMatricula = new \cl_arrematric();
                    $daoArreMatricMatricula->k00_numpre = $numpreNovo;
                    $daoArreMatricMatricula->k00_matric = $codigoMatricula;
                    $daoArreMatricMatricula->k00_perc = $percentualMatricula;
                    $daoArreMatricMatricula->incluir(
                        $daoArreMatricMatricula->k00_numpre,
                        $daoArreMatricMatricula->k00_matric
                    );
                    if ($daoArreMatricMatricula->erro_status === '0') {
                        $mensagem = "Não foi possível vincular o débito {$numpreNovo} na matricula {$codigoMatricula}";
                        throw new \ParameterException($mensagem);
                    }
                }
            }

            if (!empty($debito->colecao_inscricao)) {
                foreach (explode(',', $debito->colecao_inscricao) as $indice => $inscricoes) {
                    list($codigoInscricao, $percentualInscricao) = explode('|', $inscricoes);
                    $daoArreInscricao = new \cl_arreinscr();
                    $daoArreInscricao->k00_numpre = $numpreNovo;
                    $daoArreInscricao->k00_inscr = $codigoInscricao;
                    $daoArreInscricao->k00_perc = $percentualInscricao;
                    $daoArreInscricao->incluir($daoArreInscricao->k00_numpre, $daoArreInscricao->k00_inscr);
                    if ($daoArreInscricao->erro_status === '0') {
                        $mensagem = "Não foi possível vincular o débito {$numpreNovo} na inscrição {$codigoInscricao}.";
                        throw new \ParameterException($mensagem);
                    }
                }
            }
        }
        return true;
    }

    /**
     * @param \stdClass $debito
     * @param integer $indiceParcela
     * @return integer
     * @throws \DBException
     */
    protected function incluirDivida(\stdClass $debito, $indiceParcela, $numpreNovo, $dataInscricao = null)
    {
        $daoDivida = new \cl_divida();

        $valor = explode(",", $debito->colecao_valor);
        $datasVencimento = explode(",", $debito->data_vencimento);
        $parcelas = explode(",", $debito->colecao_numpar);
        $dataOperacao = explode(",", $debito->colecao_numpar);
        $exercicioDivida = $debito->exercicio_divida;

        $valor_divida = $valor[$indiceParcela];
        $iNumpar = $parcelas[$indiceParcela];
        $dataVencimento = $datasVencimento[$indiceParcela];
        $numeroTotal = $debito->k00_numtot;

        if ($this->lUnificarDebitos) {
            $valor_divida = $debito->k00_valor;
            $iNumpar = 1;
            $numeroTotal = 1;
            $dataVencimento = $this->maiorDataVencimento() ?
                $debito->maior_data_vencimento :
                $debito->menor_data_vencimento;

            if ($this->escolhaDataVencimento()) {
                $dataVencimento = $this->getDataVencimento()->getDate();
            }
        }

        $daoDivida->v01_coddiv = null;
        $daoDivida->v01_numcgm = $debito->k00_numcgm;
        $daoDivida->v01_dtinsc = $dataInscricao;
        $daoDivida->v01_exerc = $exercicioDivida;
        $daoDivida->v01_numpre = $numpreNovo;
        $daoDivida->v01_numpar = $iNumpar;
        $daoDivida->v01_numtot = $numeroTotal;
        $daoDivida->v01_vlrhis = $valor_divida;
        $daoDivida->v01_proced = $this->aReceitaProcedencia[$debito->k00_receit]->getProcedenciaDivida();
        $daoDivida->v01_livro = $debito->livroExercicio;
        $daoDivida->v01_folha = $debito->folhaExercicio;
        $daoDivida->v01_dtvenc = $dataVencimento;
        $daoDivida->v01_dtoper = $dataOperacao[$indiceParcela];
        $daoDivida->v01_valor = $valor_divida;
        $daoDivida->v01_obs = str_replace("'", "\'", " - {$debito->obs} - {$this->sObservacoes}");
        $daoDivida->v01_numdig = "0";
        $daoDivida->v01_instit = db_getsession("DB_instit");
        $daoDivida->v01_dtinclusao = $dataInscricao;
        $daoDivida->v01_processo = '';
        $daoDivida->v01_dtprocesso = '';
        $daoDivida->v01_titular = '';

        if (!empty($this->stdProcesso)) {
            $daoDivida->v01_processo = $this->stdProcesso->codigo;
            $daoDivida->v01_dtprocesso = $this->stdProcesso->data;
            $daoDivida->v01_titular = $this->stdProcesso->titular;
        }

        $daoDivida->incluir($daoDivida->v01_coddiv);

        if ($daoDivida->erro_status === 0) {
            throw new \DBException("Nao incluiu dívida");
        }

        $codigoDivida = $daoDivida->v01_coddiv;

        if (!empty($this->processo)) {
            $daoProcessoDivida = new \cl_dividaprotprocesso();
            $daoProcessoDivida->v88_sequencial = null;
            $daoProcessoDivida->v88_divida = $codigoDivida;
            $daoProcessoDivida->v88_protprocesso = $this->processo->getCodProcesso();
            $daoProcessoDivida->incluir($daoProcessoDivida->v88_sequencial);
            if ($daoProcessoDivida->erro_status === '0') {
                throw new \DBException("Ocorreu um erro ao vincular a dívida com o processo do protocolo.");
            }
        }

        $indiceDivida = $numpreNovo . $iNumpar;
        $indiceDono = $debito->k00_numcgm . "-" . $debito->colecao_matricula . "-" . $debito->colecao_inscricao;

        if (!isset($this->aDividasArrecad[$indiceDono][$exercicioDivida][$indiceDivida])) {
            $oDivida = new \stdClass();
            $oDivida->iNumpre = $numpreNovo;
            $oDivida->iNumpar = $iNumpar;

            $this->aDividasArrecad[$indiceDono][$exercicioDivida][$indiceDivida] = $oDivida;
        }

        $daoDividaImportaReg = new \cl_divimportareg();
        $daoDividaImportaReg->v04_coddiv = $codigoDivida;
        $daoDividaImportaReg->v04_divimporta = $this->codigoImportacao;
        $daoDividaImportaReg->incluir();

        if ($daoDividaImportaReg->erro_status === '0') {
            throw new \DBException("Nao incluiu registro de importação da dívida");
        }

        $daoDividaOld = new \cl_divold();
        $daoDividaOld->k10_sequencial = null;
        $daoDividaOld->k10_coddiv = $codigoDivida;
        $daoDividaOld->k10_numpre = $debito->k00_numpre;
        $daoDividaOld->k10_numpar = $parcelas[$indiceParcela];
        $daoDividaOld->k10_receita = $debito->k00_receit;
        $daoDividaOld->incluir($daoDividaOld->k10_sequencial);
        if ($daoDividaOld->erro_status === '0') {
            throw new \DBException("Não foi possível incluir a dívida em divold. " . pg_last_error());
        }

        return $codigoDivida;
    }

    /**
     * @param  $iParcelamento
     * @return \stdClass[]
     * @throws \DBException
     * @throws \Exception
     */
    protected function buscarOrigemParcelamento($iParcelamento)
    {
        $oDaoTermo = new \cl_termo();

        $sSqlOrigem = $oDaoTermo->sql_query_origem_parcelamento($iParcelamento);

        $sCampos = implode(
            ",",
            array(
                "k00_numpre",
                "1 as k00_numpar",
                "k00_numcgm",
                "array_to_string(array_accum(k00_dtoper), ',') AS colecao_dtoper",
                "k00_receit",
                "array_to_string(array_accum(k00_hist), ',') as colecao_historico",
                "x.k00_tipo",
                "k03_tipo",
                "k00_tipojm",
                "k00_numtot",
                "array_to_string(array_accum(k00_numdig), ',') as k00_numdig",
                "sum(coalesce(k00_valor, 0)) as k00_valor",
                "min(k00_dtvenc) as menor_data_vencimento",
                "max(k00_dtvenc) as maior_data_vencimento",
                "array_to_string(array_accum(k00_numpar), ',') as colecao_numpar",
                "array_to_string(array_accum(coalesce(k00_valor, 0)), ',') as colecao_valor",

                "(select array_to_string(array_accum(k00_matric || '|' ||k00_perc), ',')
                from arrematric where arrematric.k00_numpre = x.k00_numpre) as colecao_matricula",

                "(select array_to_string(array_accum(k00_inscr || '|' ||k00_perc), ',')
                from arreinscr where arreinscr.k00_numpre = x.k00_numpre) as colecao_inscricao",

                "(select array_to_string(array_accum(distinct arrenumcgm.k00_numcgm), ',')
                from arrenumcgm where arrenumcgm.k00_numpre = x.k00_numpre) as colecao_cgm",

                "array_to_string(array_accum(k00_dtvenc), ',') as data_vencimento",
                "(select dv05_obs from diversos where dv05_numpre = x.k00_numpre) as obs",
                "(select dv05_exerc as exercicio from diversos where dv05_numpre = x.k00_numpre) AS exercicio_divida"
            )
        );

        $sGroupBy = implode(
            ",",
            array(
                "x.k00_numpre",
                "x.k00_numcgm",
                "x.k00_receit",
                "x.k00_tipo",
                "arretipo.k03_tipo",
                "x.k00_tipojm",
                "x.k00_numtot",
                "x.k00_numdig"
            )
        );

        $sSql = "select $sCampos           ";
        $sSql .= "  from ($sSqlOrigem) as x ";
        $sSql .= "      inner join arretipo on arretipo.k00_tipo = x.k00_tipo ";
        $sSql .= " where k00_valor > 0      ";
        $sSql .= " group by $sGroupBy       ";
        $sSql .= " order by k00_numpre      ";

        $rsOrigem = db_query($sSql);

        if (!$rsOrigem) {
            throw new \DBException("Erro ao consultar os dados de origem do parcelamento {$iParcelamento}.");
        }

        if (pg_num_rows($rsOrigem) == 0) {
            throw new \Exception("Não há débitos de origem para o parcelamento {$iParcelamento}.");
        }

        return \db_utils::getCollectionByRecord($rsOrigem);
    }

    /**
     * Função que anula o parcelamento e atualiza os dados dos débitos a serem importados
     *
     * @param  $iParcelamento
     * @return array|\stdClass[]
     * @throws \DBException
     */
    protected function anularParcelamento($iParcelamento)
    {
        $oOrigemParcelamento = $this->buscarOrigemParcelamento($iParcelamento);

        $oDaoTermo = new \cl_termo();
        $sSqlSimulacao = $oDaoTermo->sql_query_simular_anulacao($iParcelamento);
        $rsSimulacao = $oDaoTermo->sql_record($sSqlSimulacao);

        if (!$rsSimulacao) {
            throw new \DBException("Erro ao simular a anulação do parcelamento {$iParcelamento}.");
        }

        $oRetorno = \db_utils::fieldsMemory($rsSimulacao, 0);

        if (strpos($oRetorno->simulacao, 'OK') === false) {
            throw new \DBException($oRetorno->simulacao);
        }

        $sSqlSimulacao = $oDaoTermo->sql_query_simulacao(
            $iParcelamento,
            null,
            "max(v21_sequencial) as codigo_simulacao"
        );
        $rsSimulacao = $oDaoTermo->sql_record($sSqlSimulacao);

        if (!$rsSimulacao) {
            throw new \DBException("Erro ao buscar o código da simulação de anulação do parcelamento.");
        }

        $oSimulacao = \db_utils::fieldsMemory($rsSimulacao, 0);
        $iUsuario = db_getsession("DB_id_usuario");

        $sMotivo = "Anulado por inscrição em Dívida Ativa";

        $sSqlAnulacao = $oDaoTermo->sql_query_anular_parcelamento($oSimulacao->codigo_simulacao, $iUsuario, $sMotivo);
        $rsAnulacao = $oDaoTermo->sql_record($sSqlAnulacao);

        if (!$rsAnulacao) {
            throw new \DBException("Erro ao anular o parcelamento {$iParcelamento}.");
        }

        $oAnulacao = \db_utils::fieldsMemory($rsAnulacao, 0);

        if (strpos($oAnulacao->anulacao, 'OK') === false) {
            throw new \DBException($oAnulacao->anulacao);
        }

        $aNumpres = array();
        $retornoOrigem = array();
        foreach ($oOrigemParcelamento as $indice => $debito) {
            $aNumpres[$debito->k00_numpre] = $debito->k00_numpre;

            /**
             * Atualiza os valores das parcelas.
             * Se houver algum pagamento do parcelamento, os valores de parcelas serão atualizadas após a anulação
             * do parcelamento
             */
            $oDaoArrecad = new \cl_arrecad();
            $sCampos = "
                array_to_string(array_accum(k00_dtoper), ',') as colecao_dtoper,
                array_to_string(array_accum(k00_hist), ',') as colecao_historico,
                array_to_string(array_accum(k00_numdig), ',') as k00_numdig,
                array_to_string(array_accum(k00_numpar), ',') as colecao_numpar,
                array_to_string(array_accum(k00_valor), ',') as colecao_valor,
                array_to_string(array_accum(k00_dtvenc), ',') as data_vencimento,
                sum(coalesce(k00_valor, 0)) as k00_valor
            ";

            $sWhere = "k00_numpre = {$debito->k00_numpre}";

            $sSqlParcelaAtualizada = $oDaoArrecad->sql_query_file(null, $sCampos, null, $sWhere);
            $rsParcelaAtualizada = db_query($sSqlParcelaAtualizada);

            if (!$rsParcelaAtualizada) {
                throw new \DBException("Erro ao buscar os valores da parcela.");
            }

            if (pg_num_rows($rsParcelaAtualizada) > 0) {
                $colecao = \db_utils::fieldsMemory($rsParcelaAtualizada, 0);
                $oOrigemParcelamento[$indice]->colecao_dtoper = $colecao->colecao_dtoper;
                $oOrigemParcelamento[$indice]->colecao_historico = $colecao->colecao_historico;
                $oOrigemParcelamento[$indice]->k00_numdig = $colecao->k00_numdig;
                $oOrigemParcelamento[$indice]->colecao_numpar = $colecao->colecao_numpar;
                $oOrigemParcelamento[$indice]->colecao_valor = $colecao->colecao_valor;
                $oOrigemParcelamento[$indice]->data_vencimento = $colecao->data_vencimento;
                $oOrigemParcelamento[$indice]->k00_valor = $colecao->k00_valor;
            }
        }

        foreach ($aNumpres as $numpre) {
            $parcelamento = $this->verificarParcelamento($numpre);

            if (!empty($parcelamento)) {
                $retornoOrigem = array_merge($retornoOrigem, $this->anularParcelamento($parcelamento));
            }
        }

        if (!empty($retornoOrigem)) {
            $oOrigemParcelamento = $retornoOrigem;
        }

        return $oOrigemParcelamento;
    }

    /**
     * Função que verifica se o débito que está sendo importado é um parcelamento
     *
     * @return bool|integer
     * @throws \DBException
     */
    protected function verificarParcelamento($numpre)
    {
        $oDaoTermo = new \cl_termo();
        $sWhere = " v07_numpre = {$numpre} ";
        $sSqlTermo = $oDaoTermo->sql_query_file(
            null,
            "distinct v07_parcel as codigo_parcelamento",
            null,
            $sWhere
        );

        $rsTermo = db_query($sSqlTermo);

        if (!$rsTermo) {
            throw new \DBException("Erro ao consultar dados do parcelamento.");
        }

        if (pg_num_rows($rsTermo) > 0) {
            $oTermo = \db_utils::fieldsMemory($rsTermo, 0);
            return $oTermo->codigo_parcelamento;
        }

        return false;
    }

    protected function verificarParcelamentos(array $numpres)
    {
        $numpres = implode(', ', $numpres);
        $where = " v07_numpre IN ({$numpres}) ";

        $clTermo = new \cl_termo();
        $sql = $clTermo->sql_query_file(
            null,
            'DISTINCT v07_parcel AS codigo_parcelamento',
            null,
            $where
        );

        $resultado = db_query($sql);

        if (!$resultado) {
            throw new \DBException('Erro ao consultar dados do parcelamento.');
        }

        if (pg_num_rows($resultado)) {
            return \db_utils::getCollectionByRecord($resultado);
        }

        return false;
    }

    protected function validarOrigem()
    {
        $this->onProgressBarSetMessageLog("Buscando origens de parcelamentos (Etapa 5/11)");

        $aNumpres = array();

        foreach ($this->oDadosDebitos as $debito) {
            $aNumpres[] = $debito->k00_numpre;
        }

        $aOrigens = array();

        $parcelamentos = $this->verificarParcelamentos($aNumpres);

        $this->onProgressBarSetMessageLog("Processando anulação dos parcelamentos (Etapa 6/11)");
        $contadorRegistros = 0;
        $this->onProgressBarUpdateMaxProgress($contadorRegistros);
        $this->onProgressBarUpdatePercentual($contadorRegistros);

        if (!empty($parcelamentos)) {
            $this->onProgressBarUpdateMaxProgress(count($parcelamentos));

            $this->onProgressBarUpdatePercentual($contadorRegistros);

            foreach ($parcelamentos as $parcelamento) {
                $contadorRegistros++;

                $aOrigens = array_merge($aOrigens, $this->anularParcelamento($parcelamento->codigo_parcelamento));

                $this->onProgressBarUpdatePercentual($contadorRegistros);
                $this->oDadosParcelamento[$parcelamento->codigo_parcelamento] = $aOrigens;
            }
        }

        if (!empty($aOrigens)) {
            $this->oDadosDebitos = $aOrigens;
        }
    }

    /**
     * @param \processoProtocolo $processo
     */
    public function setProcessoProtocolo(\processoProtocolo $processo)
    {
        $this->processo = $processo;
    }

    /**
     * @param \stdClass $stdProcesso
     */
    public function setProcesso(\stdClass $stdProcesso)
    {
        $this->stdProcesso = $stdProcesso;
    }

    /**
     * @return bool
     * @throws \ParameterException
     */
    public function importacaoParcial()
    {
        if (empty($this->debitosProcessar)) {
            throw new \ParameterException("Informe os débitos que devem ser processados.");
        }

        $aWhere = array();

        foreach ($this->debitosProcessar['aDebitos'] as $stdDebito) {
            if (isset($stdDebito->iCodigoProcedencia)) {
                $this->adicionarReceitaProcedencia(
                    $stdDebito->iReceita,
                    new \ProcedenciaDivida($stdDebito->iCodigoProcedencia)
                );
            }

            $aWhere[] = "(arrecad.k00_numpre = {$stdDebito->iNumpre} and
                          arrecad.k00_numpar = {$stdDebito->iNumpar} and
                          arrecad.k00_receit = {$stdDebito->iReceita})";
        }

        if (isset($this->debitosProcessar['aProcedencias'])) {
            $aProcedencias = (array)$this->debitosProcessar['aProcedencias'];

            foreach ($aProcedencias as $receita => $procedencia) {
                $this->adicionarReceitaProcedencia($receita, new \ProcedenciaDivida($procedencia->iProcedencia));
            }
        }

        return $this->importar(implode(' or ', $aWhere));
    }

    /**
     * @return bool
     */
    public function importacaoGeral()
    {
        $this->onProgressBarSetMessageLog("Preparando consulta dos dados da importação (Etapa 2/11)");

        $sWhere = "arrecad.k00_tipo = {$this->getTipoDebitoOrigem()}";

        if (!empty($this->iListaDebitos)) {
            $sWhere = "arrecad.k00_numpre in (select distinct k61_numpre
                                              from listadeb where k61_codigo = {$this->iListaDebitos} )";
        } elseif (count($this->aReceitaProcedencia) > 0) {
            $aReceitas = array();

            foreach ($this->aReceitaProcedencia as $indice => $oReceita) {
                $aReceitas[] = $indice;
            }

            $sReceitas = implode(', ', $aReceitas);

            $sSqlReceitas = "
            select distinct
                   arrecad.k00_receit
              from arreold
                   inner join arreinstit on arreinstit.k00_numpre = arreold.k00_numpre
                   inner join diversos on diversos.dv05_numpre = arreold.k00_numpre
                   inner join termodiver on termodiver.dv10_coddiver = diversos.dv05_coddiver
                   inner join termo on termo.v07_parcel = termodiver.dv10_parcel
                   inner join arrecad on arrecad.k00_numpre = termo.v07_numpre
                   inner join tabrec on tabrec.k02_codigo = arrecad.k00_receit
             where arrecad.k00_tipo = 14
               and arreold.k00_receit in ({$sReceitas})
               and arreinstit.k00_instit = ".db_getsession("DB_instit");


            $resultado = db_query($sSqlReceitas);

            if (!$resultado) {
                throw new \Exception('Erro ao buscar receitas.');
            }

            $aReceitasArrecad = array();
            $collectionReceitas = \db_utils::getCollectionByRecord($resultado, false, false, true);

            foreach ($collectionReceitas as $oReceita) {
                $aReceitasArrecad[] = $oReceita->k00_receit;
            }

            $sReceitasArrecad = implode(', ', $aReceitasArrecad);

            $sWhere .= " AND arrecad.k00_receit in({$sReceitasArrecad})";
        }

        return $this->importar($sWhere);
    }

    private function buscarLivroFolha()
    {
        $anoSessao = date('Y', db_getsession("DB_datausu"));

        $query = "
            select v01_livro,
                   v01_folha,
                   count(*) as total_registros
              FROM divida
             WHERE v01_livro = (SELECT max(v01_livro)
                                  FROM divida
                                 WHERE extract(YEAR FROM v01_dtinclusao) = {$anoSessao}
                                 and v01_livro < 998 )
            group by v01_livro, v01_folha
            having v01_folha = max(v01_folha)
            order by 2 desc
            limit 1
        ";

        $this->onProgressBarSetMessageLog("Buscando dados de livros das dívidas (Etapa 7/11)");

        $buscaLivroExercicio = db_query($query);

        if (pg_num_rows($buscaLivroExercicio) == 0) {
            $daoDivida = new \cl_divida();
            $novoLivro = db_query($daoDivida->sql_query_file(null, "coalesce(max(v01_livro), 0) + 1 as v01_livro"));
            $livroExercicio = \db_utils::fieldsMemory($novoLivro, 0)->v01_livro;
            $folhaExercicio = 1;
            $totalRegistrosPorFolha = 0;
        } else {
            $livroExercicio = \db_utils::fieldsMemory($buscaLivroExercicio, 0)->v01_livro;
            $folhaExercicio = \db_utils::fieldsMemory($buscaLivroExercicio, 0)->v01_folha;
            $totalRegistrosPorFolha = \db_utils::fieldsMemory($buscaLivroExercicio, 0)->total_registros;
        }

        foreach ($this->oDadosDebitos as $debito) {
            $totalRegistrosPorFolha++;

            if ($totalRegistrosPorFolha > 30) {
                $totalRegistrosPorFolha = 1;
                $folhaExercicio++;
            }

            $debito->livroExercicio = $livroExercicio;
            $debito->folhaExercicio = $folhaExercicio;
        }
    }

    private function criarHistoricoDebitos()
    {
        $this->onProgressBarSetMessageLog("Criando histórico de débitos (Etapa 8/11)");
        $this->onProgressBarUpdateMaxProgress(count($this->oDadosDebitos));

        $contadorRegistros = 0;
        $this->onProgressBarUpdatePercentual($contadorRegistros);

        $debitos = $this->oDadosDebitos;

        foreach ($debitos as $debito) {
            $virgula = '';

            $contadorRegistros++;

            $sql = "INSERT INTO arreold VALUES";

            $parcelas = explode(',', $debito->colecao_numpar);
            $valores = explode(',', $debito->colecao_valor);
            $datasVencimento = explode(',', $debito->data_vencimento);
            $datasOperacao = explode(',', $debito->colecao_dtoper);
            $historicos = explode(',', $debito->colecao_historico);
            $digito = explode(',', $debito->k00_numdig);

            if (!empty($debito->colecao_numpar)) {
                foreach ($parcelas as $chaveParcela => $parcela) {
                    $sql .= "{$virgula}(
		            {$debito->k00_numpre},
		            {$parcela},
		            {$debito->k00_numcgm},
		            '{$datasOperacao[$chaveParcela]}',
		            {$debito->k00_receit},
		            {$historicos[$chaveParcela]},
		            {$valores[$chaveParcela]},
		            '{$datasVencimento[$chaveParcela]}',
		            {$debito->k00_numtot},
		            '{$digito[$chaveParcela]}',
		            {$debito->k00_tipo},
		            {$debito->k00_tipojm}
		        )";

                    $virgula = ',';
                }

                $resultado = db_query($sql);

                if (!$resultado) {
                    throw new \Exception('Erro ao incluir históricos de débitos em arreold.');
                }

                $this->onProgressBarUpdatePercentual($contadorRegistros);
            }
        }
    }

    private function processarDebitos(array $relacaoNumpreAntigoNovo)
    {
        $this->onProgressBarSetMessageLog("Inserindo débitos em dívida ativa (Etapa 9/11)");

        $dataInscricao = date('Y-m-d', db_getsession('DB_datausu'));
        $contadorRegistros = 0;

        $this->onProgressBarUpdatePercentual($contadorRegistros);

        /**
         * Agrupar debitos por procedencia, numpre e numpar
         */
        $debitos = [];
        foreach ($this->oDadosDebitos as $debito) {
            $procedenciaDivida = $this->aReceitaProcedencia[$debito->k00_receit];
            $procedencia = $procedenciaDivida->getProcedenciaDivida();

            if (!array_key_exists($procedencia, $debitos)) {
                $debitos[$procedencia] = [];
            }

            if (!array_key_exists($debito->k00_numpre, $debitos[$procedencia])) {
                $debitos[$procedencia][$debito->k00_numpre] = [];
            }

            if ($this->lUnificarDebitos) {
                if (!array_key_exists(1, $debitos[$procedencia][$debito->k00_numpre])) {
                    $debitos[$procedencia][$debito->k00_numpre][1] = [];
                }
                $debitos[$procedencia][$debito->k00_numpre][1][] = $debito;
                continue;
            }

            if (!array_key_exists($debito->k00_numpar, $debitos[$procedencia][$debito->k00_numpre])) {
                $debitos[$procedencia][$debito->k00_numpre][$debito->k00_numpar] = [];
            }

            $debitos[$procedencia][$debito->k00_numpre][$debito->k00_numpar][] = $debito;
        }

        /**
         * Processa debitos
         */
        foreach ($debitos as $proced => $numpre) {
            foreach ($numpre as $numpre => $parcelas) {
                foreach ($parcelas as $numpar => $parcela) {
                    $sql = '';
                    $contadorRegistros++;

                    // Cria divida agrupada por parcela
                    $dadosDivida = [
                        'numpre_novo' => $relacaoNumpreAntigoNovo[$numpre],
                        'procedencia' => $proced,
                        'numero_total' => $parcela[0]->k00_numtot,
                        'valor' => array_reduce($parcela, function ($total, $item) {
                            return bcadd($total, $item->k00_valor, 2);
                        }, "0"),
                        'data_vencimento' => $parcela[0]->data_vencimento
                    ];

                    // Data vencimento dos dados unificado
                    if ($this->lUnificarDebitos) {
                        $dataVencimento = array_map(function ($item) {
                            return $item->data_vencimento;
                        }, $parcela);
                        $dadosDivida['data_vencimento'] = $this->maiorDataVencimento()
                            ? max($dataVencimento)
                            : min($dataVencimento);

                        if ($this->escolhaDataVencimento()) {
                            $dadosDivida['data_vencimento'] = $this->getDataVencimento()->getDate();
                        }
                    }

                    $sql .= $this->criarDivida($parcela[0], $dadosDivida, $numpar, $dataInscricao, $proced);

                    // Criar debitos (arrecad) individuais
                    $receitaAtribuida = [];
                    foreach ($parcela as $debito) {
                        $sql .= $this->incluirDivOld($numpre, $debito->k00_numpar, $debito->k00_receit);

                        if ($this->lUnificarDebitos) {
                            // verifica se a receita ja foi atribuida
                            if (in_array($debito->k00_receit, $receitaAtribuida)) {
                                $this->excluirDebitoAntigo($debito, $debito->k00_numpar);
                                continue;
                            }

                            // pega os valores do somatorio da receita
                            $valorReceita = array_reduce($parcela, function ($total, $item) use ($debito) {
                                return $item->k00_receit == $debito->k00_receit
                                    ? bcadd($total, $item->k00_valor, 2)
                                    : $total;
                            }, 0);

                            // seta nas receoitas ja atribuidas
                            $receitaAtribuida[] = $debito->k00_receit;
                        }

                        // receita da procedencia do debito
                        $sqlProcedDebito = "
                            select proced.v03_receit, proced.k00_hist, procedarretipo.v06_arretipo
                            from divida.proced
                            inner join diversos.procdiver on procdiver.dv09_proced = proced.v03_codigo
                            left join caixa.procedarretipo on procedarretipo.v06_proced = proced.v03_codigo
                            where procdiver.dv09_receit = {$debito->k00_receit}
                        ";
                        $rsProcedDebito = db_query($sqlProcedDebito);
                        if (!$rsProcedDebito && pg_num_rows($rsProcedDebito) < 0) {
                            throw new Exception("Procedencia da receita {$debito->k00_receit} nao encontrada");
                        }
                        $procedenciaDebito = db_utils::fieldsMemory($rsProcedDebito, 0);

                        $dadosDebitos = [
                            'numpre_novo' => $relacaoNumpreAntigoNovo[$numpre],
                            'receita' => $procedenciaDebito->v03_receit,
                            'tipo' => $procedenciaDebito->v06_arretipo,
                            'historico' => $procedenciaDebito->k00_hist,
                            'numero_total' => ($this->lUnificarDebitos) ? "1" : $debito->k00_numtot,
                            'valor' => ($this->lUnificarDebitos) ? $valorReceita : $debito->k00_valor,
                            'data_vencimento' => ($this->lUnificarDebitos)
                                ? $dadosDivida['data_vencimento']
                                : $debito->data_vencimento
                        ];

                        $sql .= $this->criarDebito($debito, $dadosDebitos, $numpar, $debito->k00_numpar);
                    }

                    // cria arrematric
                    if (!empty($debito->colecao_matricula)) {
                        $matriculas = explode(',', $debito->colecao_matricula);

                        foreach ($matriculas as $matricula) {
                            list($codigoMatricula, $percentualMatricula) = explode('|', $matricula);

                            $sSqlMatric = "SELECT k00_numpre FROM arrematric
                                WHERE k00_numpre = {$dadosDebitos['numpre_novo']}
                                AND k00_matric = {$codigoMatricula} ;";

                            $rsMatric = db_query($sSqlMatric);

                            if (pg_num_rows($rsMatric) > 0) {
                                continue;
                            }

                            $sql .= "insert into arrematric (k00_numpre, k00_matric, k00_perc)
                                values ({$dadosDebitos['numpre_novo']}, {$codigoMatricula}, {$percentualMatricula});";
                        }
                    }

                    // cria arreinscr
                    if (!empty($debito->colecao_inscricao)) {
                        $inscricoes = explode(',', $debito->colecao_inscricao);

                        foreach ($inscricoes as $indice => $inscricao) {
                            list($codigoInscricao, $percentualInscricao) = explode('|', $inscricao);

                            $sSqlInscr = "SELECT  k00_numpre FROM  arreinscr
                                WHERE k00_numpre = {$dadosDebitos['numpre_novo']} AND k00_inscr = {$codigoInscricao} ;";
                            $rsInscr = db_query($sSqlInscr);

                            if (pg_num_rows($rsInscr) > 0) {
                                continue;
                            }

                            $sql .= "insert into arreinscr (k00_numpre, k00_inscr, k00_perc)
                                values ({$dadosDebitos['numpre_novo']}, {$codigoInscricao}, {$percentualInscricao});";
                        }
                    }

                    $resultado = db_query($sql);
                    if (!$resultado) {
                        throw new \Exception('Erro.' . pg_last_error());
                    }

                    // insere na termo
                    $sqlDivida = "select
                                    v01_coddiv,
                                    v01_dtvenc,
                                    v01_dtinsc,
                                    v01_vlrhis,
                                    v01_dtoper
                                from
                                    divida
                                where
                                    v01_numpre = $relacaoNumpreAntigoNovo[$numpre]
                                    and v01_numpar = $numpar
                                    and v01_proced = $proced";

                    $rs = db_query($sqlDivida);
                    if (pg_num_rows($rs) == 0) {
                        continue;
                    }

                    $oDivida = \db_utils::fieldsMemory($rs, 0);
                    $dataInsc = date("Y-m-d", db_getsession('DB_datausu'));
                    $usuario = db_getsession('DB_id_usuario');
                    $instit = db_getsession('DB_instit');
                    TermoInscricaoService::salvar(
                        $relacaoNumpreAntigoNovo[$numpre],
                        $oDivida,
                        $procedenciaDivida->getReceitaDivida(),
                        $dataInsc,
                        $usuario,
                        $instit
                    );

                    $this->onProgressBarUpdatePercentual($contadorRegistros);
                }
            }
        }
    }

    private function processarDebitosParcelamento(array $relacaoNumpreAntigoNovo)
    {
        $this->onProgressBarSetMessageLog("Inserindo débitos em dívida ativa (Etapa 9/11)");

        $parcelamentos = $this->oDadosParcelamento;
        $dataInscricao = date('Y-m-d', db_getsession('DB_datausu'));
        $this->onProgressBarUpdatePercentual($contadorRegistros);

        foreach ($parcelamentos as $parcelamento => $debitos) {
            foreach ($debitos as $debito) {
                $sql = "";
                $contadorRegistros++;

                $parcelas = explode(',', $debito->colecao_numpar);
                $valores = explode(',', $debito->colecao_valor);
                $datasVencimento = explode(',', $debito->data_vencimento);
                $historicos = explode(',', $debito->colecao_historico);
                $competencias = explode(',', $debito->colecao_competencia);

                $unificou = false;

                $procedenciaDivida = $this->aReceitaProcedencia[$debito->k00_receit];

                if (is_null($procedenciaDivida)) {
                    $msg  = "Erro: Sem procedencia configurada para a receita ";
                    $msg .= "{$debito->k00_receit} presente no parcelamento {$parcelamento}";
                    throw new \Exception($msg);
                }

                $dados = array(
                    'numpre_novo' => $relacaoNumpreAntigoNovo[$debito->k00_numpre],
                    'receita' => $procedenciaDivida->getReceitaDivida(),
                    'tipo' => $procedenciaDivida->getTipoDebito(),
                    'numero_total' => $debito->k00_numtot
                );

                asort($parcelas);

                if (!empty($debito->colecao_numpar)) {
                    foreach ($parcelas as $chaveParcela => $parcela) {
                        $dados['valor'] = $valores[$chaveParcela];
                        $dados['data_vencimento'] = $datasVencimento[$chaveParcela];
                        $dados['historico'] = $historicos[$chaveParcela];

                        $numpar = $parcela;

                        if ($this->lUnificarDebitos) {
                            $parcela = 1;
                            $dados['valor'] = $debito->k00_valor;
                            $dados['numero_total'] = 1;
                            $dados['data_vencimento'] = $this->maiorDataVencimento() ?
                                $debito->maior_data_vencimento :
                                $debito->menor_data_vencimento;

                            if ($this->escolhaDataVencimento()) {
                                $dados['data_vencimento'] = $this->getDataVencimento()->getDate();
                            }
                        }

                        $debito->competencia = $competencias[$chaveParcela];

                        if (!$unificou) {
                            $sql .= $this->criarDivida(
                                $debito,
                                $dados,
                                $parcela,
                                $dataInscricao,
                                $procedenciaDivida->getProcedenciaDivida(),
                                $parcelamento
                            );
                        }

                        $sql .= $this->incluirDivOld($debito->k00_numpre, $numpar, $debito->k00_receit);

                        if ($unificou) {
                            $sql .= $this->excluirDebitoAntigo($debito, $numpar);
                            continue;
                        }

                        $sql .= $this->criarDebito($debito, $dados, $parcela, $numpar);

                        if ($this->lUnificarDebitos) {
                            $unificou = true;
                        }
                    }
                }

                if (!empty($debito->colecao_matricula)) {
                    $matriculas = explode(',', $debito->colecao_matricula);

                    foreach ($matriculas as $indice => $matricula) {
                        list($codigoMatricula, $percentualMatricula) = explode('|', $matricula);

                        $sSqlMatric  = "SELECT  k00_numpre FROM  arrematric";
                        $sSqlMatric .= "                   WHERE k00_numpre = {$dados['numpre_novo']}";
                        $sSqlMatric .= "                     AND k00_matric = {$codigoMatricula} ;";

                        $rsMatric = db_query($sSqlMatric);

                        if (pg_num_rows($rsMatric) > 0) {
                            continue;
                        }

                        $sql .= "insert into arrematric (k00_numpre, k00_matric, k00_perc)
                                 values ({$dados['numpre_novo']}, {$codigoMatricula}, {$percentualMatricula});";
                    }
                }

                if (!empty($debito->colecao_inscricao)) {
                    $inscricoes = explode(',', $debito->colecao_inscricao);

                    foreach ($inscricoes as $indice => $inscricao) {
                        list($codigoInscricao, $percentualInscricao) = explode('|', $inscricao);

                        $sSqlInscr = "SELECT  k00_numpre FROM  arreinscr
                                      WHERE k00_numpre = {$dados['numpre_novo']} AND k00_inscr = {$codigoInscricao} ;";
                        $rsInscr = db_query($sSqlInscr);

                        if (pg_num_rows($rsInscr) > 0) {
                            continue;
                        }

                        $sql .= "insert into arreinscr (k00_numpre, k00_inscr, k00_perc)
                                 values ({$dados['numpre_novo']}, {$codigoInscricao}, {$percentualInscricao});";
                    }
                }

                if (!empty($debito->colecao_numpar)) {
                    $resultado = db_query($sql);
                    if (!$resultado) {
                        throw new \Exception('Erro.' . pg_last_error());
                    }
                }

                $this->onProgressBarUpdatePercentual($contadorRegistros);
            }
        }
    }

    private function criarDebito($debito, array $dados, $parcela, $numpar)
    {
        $sql = '';

        $sets = array(
            "k00_numpre = {$dados['numpre_novo']}",
            "k00_receit = {$dados['receita']}",
            "k00_numdig = 1",
            "k00_tipojm = '0'",
            "k00_tipo = {$dados['tipo']}"
        );

        $sets[] = "k00_numpar = {$parcela}";
        $sets[] = "k00_hist = {$dados['historico']}";
        $sets[] = "k00_valor = {$dados['valor']}";
        $sets[] = "k00_dtvenc = '{$dados['data_vencimento']}'";
        $sets[] = "k00_numtot = {$dados['numero_total']}";

        $sets = implode(', ', $sets);

        $sql .= "
            UPDATE arrecad
            SET {$sets}
            WHERE k00_numpre = {$debito->k00_numpre} AND
                  k00_numpar = {$numpar} AND
                  k00_receit = {$debito->k00_receit};
        ";

        return $sql;
    }

    private function criarDivida(
        $debito,
        array $dados,
        $parcela,
        $dataInscricao,
        $procedenciaDivida,
        $parcelamento = null
    ) {
        $sql = "";
        $observacao = str_replace("'", "\'", " - {$debito->obs} - {$this->sObservacoes} {$debito->competencia}");
        $instituicao = db_getsession('DB_instit');
        $processo = '';
        $dataProcesso = null;
        $titular = '';
        $seqprocesso = '';

        if (!empty($this->stdProcesso)) {
            $processo = $this->stdProcesso->codigo;
            $dataProcesso = $this->stdProcesso->data;
            $titular = $this->stdProcesso->titular;
        }

        $dataOperacao = explode(',', $debito->colecao_dtoper);

        if (empty($debito->exercicio_divida)) {
            $debito->exercicio_divida = substr($dataOperacao[0], 0, 4);
            ;
        }
        if (isset($this->processo) && $this->processo instanceof \processoProtocolo) {
            $processo = $this->processo->getNumeroProcesso()."/".$this->processo->getAnoProcesso();
            $dataProcesso = $this->processo->getDataProcesso();
            $seqprocesso = $this->processo->getCodProcesso();
        }

        $divida = new \stdClass();
        $divida->v01_coddiv = null;
        $divida->v01_numcgm = $debito->k00_numcgm;
        $divida->v01_dtinsc = $dataInscricao;
        $divida->v01_exerc = $debito->exercicio_divida;
        $divida->v01_numpre = $dados['numpre_novo'];
        $divida->v01_numpar = $parcela;
        $divida->v01_numtot = $dados['numero_total'];
        $divida->v01_vlrhis = $dados['valor'];
        $divida->v01_proced = $procedenciaDivida;
        $divida->v01_livro = $debito->livroExercicio;
        $divida->v01_folha = $debito->folhaExercicio;
        $divida->v01_dtvenc = $dados['data_vencimento'];
        $divida->v01_dtoper = $dataOperacao[0];
        $divida->v01_valor = $dados['valor'];
        $divida->v01_obs = $observacao;
        $divida->v01_numdig = 0;
        $divida->v01_instit = $instituicao;
        $divida->v01_dtinclusao = $dataInscricao;
        $divida->v01_processo = $processo;
        $divida->v01_dtprocesso = $dataProcesso;
        $divida->v01_titular = $titular;

        $sql .= "
            INSERT INTO divida (
                v01_coddiv,
                v01_numcgm,
                v01_dtinsc,
                v01_exerc,
                v01_numpre,
                v01_numpar,
                v01_numtot,
                v01_vlrhis,
                v01_proced,
                v01_livro,
                v01_folha,
                v01_dtvenc,
                v01_dtoper,
                v01_valor,
                v01_obs,
                v01_numdig,
                v01_instit,
                v01_dtinclusao,
                v01_processo,
                v01_dtprocesso,
                v01_titular
            ) VALUES (
                nextval('divida_v01_coddiv_seq'),
                {$divida->v01_numcgm},
                '{$divida->v01_dtinsc}',
                {$divida->v01_exerc},
                {$divida->v01_numpre},
                {$divida->v01_numpar},
                {$divida->v01_numtot},
                {$divida->v01_vlrhis},
                {$divida->v01_proced},
                {$divida->v01_livro},
                {$divida->v01_folha},
                '{$divida->v01_dtvenc}',
                '{$divida->v01_dtoper}',
                {$divida->v01_valor},
                '{$divida->v01_obs}',
                '{$divida->v01_numdig}',
                {$divida->v01_instit},
                '{$divida->v01_dtinclusao}',
                '{$divida->v01_processo}',
                ".(empty($divida->v01_dtprocesso) ? "NULL" : "'$divida->v01_dtprocesso'").",
                '{$divida->v01_titular}'
            );
        ";

        $clDivida = new \cl_divida();
        $sql .= $clDivida->sql_account($divida);

        if (!empty($seqprocesso)) {
            $sql .= "
                insert into dividaprotprocesso (v88_sequencial, v88_divida, v88_protprocesso) values (
                    nextval('dividaprotprocesso_v88_sequencial_seq'),
                    currval('divida_v01_coddiv_seq'),
                    {$seqprocesso}
                );
            ";
        }

        $sql .= "insert into divimportareg (v04_divimporta, v04_coddiv)
                 values ({$this->codigoImportacao}, currval('divida_v01_coddiv_seq'));";

        $indiceDivida = $dados['numpre_novo'] . $parcela;
        $indiceDono = $debito->k00_numcgm . "-" . $debito->colecao_matricula . "-" . $debito->colecao_inscricao;

        if (!isset($this->aDividasArrecad[$indiceDono][$debito->exercicio_divida][$indiceDivida])) {
            $oDivida = new \stdClass();
            $oDivida->iNumpre = $dados['numpre_novo'];
            $oDivida->iNumpar = $parcela;

            $this->aDividasArrecad[$indiceDono][$debito->exercicio_divida][$indiceDivida] = $oDivida;

            if ($parcelamento) {
                $this->aDividasArrecadParcelamento[$indiceDono][$parcelamento][$indiceDivida] = $oDivida;
            }
        }

        return $sql;
    }

    private function incluirDivOld($numpre, $parcela, $receita)
    {
        return "
            insert into divold (k10_sequencial, k10_coddiv, k10_numpre, k10_numpar, k10_receita) values (
                nextval('divold_k10_sequencial_seq'),
                currval('divida_v01_coddiv_seq'),
                {$numpre},
                {$parcela},
                {$receita}
            );
        ";
    }

    private function excluirDebitoAntigo($debito, $parcela)
    {
        return "
            DELETE FROM arrecad
            WHERE k00_numpre = {$debito->k00_numpre} AND
                  k00_numpar = {$parcela} AND
                  k00_receit = {$debito->k00_receit};
        ";
    }

    private function reagruparDividas()
    {
        $numpres = array();
        foreach ($this->aDividasArrecad as $indiceDono => $aDividasArrecad) {
            foreach ($aDividasArrecad as $indiceExercicio => $aDivida) {
                foreach ($aDivida as $indice => $divida) {
                    $numpres[] = $divida->iNumpre;
                }
            }
        }

        $numpres = array_unique($numpres);
        $numpres = implode(',', $numpres);


        $daoDivida = new \cl_divida();
        $sqlAutoNumpre = $daoDivida->sql_query_auto("distinct k00_auto, v01_numpre", "v01_numpre in ({$numpres})");
        $rsAutoNumpre = db_query($sqlAutoNumpre);

        if (empty($rsAutoNumpre)) {
            throw new \DBException("Erro ao validar auto de infração");
        }

        $autoNumpre = \db_utils::getCollectionByRecord($rsAutoNumpre);
        $autos = array();

        foreach ($autoNumpre as $auto) {
            $autos[$auto->v01_numpre] = $auto->k00_auto;
        }

        foreach ($this->aDividasArrecad as $indiceDono => $aDividasArrecad) {
            foreach ($aDividasArrecad as $indiceExercicio => $aDivida) {
                foreach ($aDivida as $indice => $divida) {
                    if (!isset($autos[$divida->iNumpre])) {
                        continue;
                    }

                    $auto = $autos[$divida->iNumpre];

                    $this->aDividasArrecad[$indiceDono][$auto][$indice] = $divida;
                    unset($this->aDividasArrecad[$indiceDono][$indiceExercicio][$indice]);
                }

                if (empty($this->aDividasArrecad[$indiceDono][$indiceExercicio])) {
                    unset($this->aDividasArrecad[$indiceDono][$indiceExercicio]);
                }
            }

            if (empty($this->aDividasArrecad[$indiceDono])) {
                unset($this->aDividasArrecad[$indiceDono]);
            }
        }
    }

    private function reagruparDividasParcelamento()
    {

        $numpres = array();
        foreach ($this->aDividasArrecadParcelamento as $indiceDono => $aDividasArrecadParcelamento) {
            foreach ($aDividasArrecadParcelamento as $indiceParcelamento => $aDivida) {
                foreach ($aDivida as $indice => $divida) {
                    $numpres[] = $divida->iNumpre;
                }
            }
        }

        $numpres = array_unique($numpres);
        $numpres = implode(',', $numpres);

        $daoDivida = new \cl_divida();
        $sqlAutoNumpre = $daoDivida->sql_query_auto("distinct k00_auto, v01_numpre", "v01_numpre in ({$numpres})");
        $rsAutoNumpre = db_query($sqlAutoNumpre);

        if (empty($rsAutoNumpre)) {
            throw new \DBException("Erro ao validar auto de infração");
        }

        $autoNumpre = \db_utils::getCollectionByRecord($rsAutoNumpre);
        $autos = array();

        foreach ($autoNumpre as $auto) {
            $autos[$auto->v01_numpre] = $auto->k00_auto;
        }

        foreach ($this->aDividasArrecadParcelamento as $indiceDono => $aDividasArrecadParcelamento) {
            foreach ($aDividasArrecadParcelamento as $indiceParcelamento => $aDivida) {
                foreach ($aDivida as $indice => $divida) {
                    if (!isset($autos[$divida->iNumpre])) {
                        continue;
                    }

                    $auto = $autos[$divida->iNumpre];

                    $this->aDividasArrecadParcelamento[$indiceDono][$auto][$indice] = $divida;
                    unset($this->aDividasArrecadParcelamento[$indiceDono][$indiceParcelamento][$indice]);
                }

                if (empty($this->aDividasArrecadParcelamento[$indiceDono][$indiceParcelamento])) {
                    unset($this->aDividasArrecadParcelamento[$indiceDono][$indiceParcelamento]);
                }
            }

            if (empty($this->aDividasArrecadParcelamento[$indiceDono])) {
                unset($this->aDividasArrecadParcelamento[$indiceDono]);
            }
        }
    }

    /**
     * Função que realiza a consulta dos débitos que serão importados
     * @param $sWhere
     * @return bool|\PgSql\Result
     * @throws DBException
     */
    protected function buscarDebitosPorParcela($sWhereArrecad)
    {
        $sSqlCaseValor  = " case                                                          ";
        $sSqlCaseValor .= "   when arretipo.k03_tipo = 3 and arrecad.k00_valor = 0 then ( ";
        $sSqlCaseValor .= "     select case                                               ";
        $sSqlCaseValor .= "              when issvar.q05_valor > 0 then issvar.q05_valor  ";
        $sSqlCaseValor .= "              when issvar.q05_valor = 0 then issvar.q05_vlrinf ";
        $sSqlCaseValor .= "            end                                                ";
        $sSqlCaseValor .= "       from issvar                                             ";
        $sSqlCaseValor .= "      where issvar.q05_numpre = arrecad.k00_numpre             ";
        $sSqlCaseValor .= "        and issvar.q05_numpar = arrecad.k00_numpar             ";
        $sSqlCaseValor .= "   )                                                           ";
        $sSqlCaseValor .= "   else arrecad.k00_valor                                      ";
        $sSqlCaseValor .= " end                                                           ";

        $campos = implode(", ", array(
            "arrecad.k00_numpre",
            "arrecad.k00_numpar",
            "arrecad.k00_numcgm",
            "array_to_string(array_accum(arrecad.k00_dtoper), ',') AS colecao_dtoper",
            "arrecad.k00_receit",
            "array_to_string(array_accum(k00_hist), ',') as colecao_historico",
            "arrecad.k00_tipo",
            "arretipo.k03_tipo",
            "arrecad.k00_tipojm",
            "arrecad.k00_numtot",
            "array_to_string(array_accum(arrecad.k00_numdig), ',') as k00_numdig",
            "sum({$sSqlCaseValor}) as k00_valor",
            "min(arrecad.k00_dtvenc) as menor_data_vencimento",
            "max(arrecad.k00_dtvenc) as maior_data_vencimento",
            "array_to_string(array_accum(arrecad.k00_numpar), ',') as colecao_numpar",
            "array_to_string(array_accum({$sSqlCaseValor}), ',') as colecao_valor",
            "(select array_to_string(array_accum(k00_matric || '|' ||k00_perc), ',')
                from arrematric where arrematric.k00_numpre = arrecad.k00_numpre) as colecao_matricula",
            "(select array_to_string(array_accum(k00_inscr || '|' ||k00_perc), ',')
                from arreinscr where arreinscr.k00_numpre = arrecad.k00_numpre) as colecao_inscricao",
            "(select array_to_string(array_accum(distinct arrenumcgm.k00_numcgm), ',')
                from arrenumcgm where arrenumcgm.k00_numpre = arrecad.k00_numpre) as colecao_cgm",
            "(select array_to_string(array_accum(q05_mes||'/'||q05_ano), ',')) as colecao_competencia",
            "array_to_string(array_accum(k00_dtvenc), ',') as data_vencimento",
            "(select dv05_obs from diversos where dv05_numpre = arrecad.k00_numpre) as obs",
            "(select dv05_exerc as exercicio from diversos where dv05_numpre = arrecad.k00_numpre) AS exercicio_divida"
        ));

        $subsql = "select ";

        $campos_sql = explode("#", $campos);
        $virgula = "";

        for ($i = 0; $i < sizeof($campos_sql); $i++) {
            $subsql .= $virgula.$campos_sql[$i];
            $virgula = ",";
        }

        $subsql .= " from arretipo ";
        $subsql .= "      inner join arrecad on arrecad.k00_tipo = arretipo.k00_tipo          ";
        $subsql .= "                        and ($sWhereArrecad) ";
        $subsql .= "      inner join arreinstit on arreinstit.k00_numpre = arrecad.k00_numpre ";
        $subsql .= "                           and arreinstit.k00_instit = ".db_getsession('DB_instit');
        $subsql .= "      left join diversos on arrecad.k00_numpre = diversos.dv05_numpre";

        $subsql .= " left join (
            select distinct dv13_diversos, dv13_numpar, dv13_numpre from diverimportaold) as diold
            on dv05_coddiver = dv13_diversos and dv13_numpar = arrecad.k00_numpar
        ";

        $subsql .= "      left join issvar on dv13_numpre = q05_numpre and q05_numpar = dv13_numpar";
        $subsql .= " group by arrecad.k00_numpre, arrecad.k00_numpar, arrecad.k00_numcgm, arrecad.k00_receit, ";
        $subsql .= " arrecad.k00_tipo, arrecad.k00_tipojm, arrecad.k00_numtot, arretipo.k03_tipo ";

        $sql  = " select k00_numpre,               ";
        $sql .= "        k00_numpar,               ";
        $sql .= "        k00_numcgm,               ";
        $sql .= "        colecao_dtoper,           ";
        $sql .= "        k00_receit,               ";
        $sql .= "        colecao_historico,        ";
        $sql .= "        k00_tipo,                 ";
        $sql .= "        k03_tipo,                 ";
        $sql .= "        k00_tipojm,               ";
        $sql .= "        k00_numtot,               ";
        $sql .= "        k00_numdig,               ";
        $sql .= "        k00_valor,                ";
        $sql .= "        menor_data_vencimento,    ";
        $sql .= "        maior_data_vencimento,    ";
        $sql .= "        colecao_numpar,           ";
        $sql .= "        colecao_valor,            ";
        $sql .= "        colecao_matricula,        ";
        $sql .= "        colecao_inscricao,        ";
        $sql .= "        colecao_cgm,              ";
        $sql .= "        colecao_competencia,      ";
        $sql .= "        data_vencimento,          ";
        $sql .= "        obs,                      ";
        $sql .= "        exercicio_divida          ";
        $sql .= "  from ({$subsql}) as x           ";
        $sql .= " where k00_valor > 0              ";
        $sql .= " order by k00_numpre              ";

        $rsBuscaDebitos = db_query($sql);

        if (!$rsBuscaDebitos) {
            throw new \DBException("Não foi possível consultar os dados a serem importados.");
        }

        return $rsBuscaDebitos;
    }
}
