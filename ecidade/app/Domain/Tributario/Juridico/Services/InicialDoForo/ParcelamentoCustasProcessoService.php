<?php

namespace App\Domain\Tributario\Juridico\Services\InicialDoForo;

use ECidade\Tributario\Juridico\ProcessoForo\Repository\ProcessoForo as ProcessoForoRepository;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo as ProcessoForoEntity;
use ECidade\Tributario\Divida\Termo\Repository\Termo as TermoRepository;
use ECidade\Tributario\Arrecadacao\Custas\Service\Relatorio\Factory;
use ECidade\Tributario\Juridico\Inicial\Inicial as InicialEntity;
use cl_processoforoinicial;
use cl_custasparcelamento;
use cl_custasparceladas;
use cl_termotaxaparc;
use cl_arrecad;
use cl_arreold;
use cl_processoforopartilhacusta;
use cl_taxa;
use cl_termo;
use db_utils;
use stdClass;
use DateTime;
use Exception;

/**
 * Service para visualizacao de custas e honorarios vinculados a um processo
 */
class ParcelamentoCustasProcessoService
{
    /**
     * custas vinculadas ao processo
     * @var object[]
     */
    private $aCustas = array();

    /**
     * custas partilha
     * @var object[]
     */
    private $aCustasPartilha = array();

    /**
     * @return ParcelamentoCustasProcessoService
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     * Busca de custas do termo lancadas no arrecad
     * @param integer $iNumpre
     * @param boolean $lAgrupaPorParcela
     * @return array
     */
    public function buscaCustasProcessoPorNumpre($iNumpre, $lAgrupaPorParcela = false)
    {
        $daoArrecad = new cl_arrecad();
        $sqlBuscaCustas = $daoArrecad->sql_busca_custas($iNumpre, $lAgrupaPorParcela);
        $rsBuscaCustas = db_query($sqlBuscaCustas);
        $custas = db_utils::getCollectionByRecord($rsBuscaCustas);
        return $custas;
    }

    /**
     * @param array $taxas
     * @param int $cgm
     * @param int $quantidadeParcelas
     * @param int $sequencialTermo
     * @return void
     */
    public function inserirTaxasArrecad($taxas, $cgm, $quantidadeParcelas, $sequencialTermo)
    {
        $numpre = $this->buscarNumpreTermo($sequencialTermo);
        $datasVencimento = $this->buscarDatasVencimentoParcelasTermo($sequencialTermo);

        $custasAgrupadas = [];

        foreach ($taxas as $oTaxa) {
            if ($oTaxa->permiteParcelamento) {
                $numeroMaximoParcelasDivisao = $oTaxa->numeroMaximoParcelas;
                $naoConsideraNumeroMaximoParcelas = !$numeroMaximoParcelasDivisao
                    || empty(strval($numeroMaximoParcelasDivisao))
                    || (int)$numeroMaximoParcelasDivisao >= (int)$quantidadeParcelas;

                if ($naoConsideraNumeroMaximoParcelas) {
                    for ($index = 1; $index <= $quantidadeParcelas; $index++) {
                        $novaCusta = new stdClass();
                        $novaCusta->k00_dtoper = (new DateTime())->format('Y-m-d');
                        $novaCusta->k00_dtvenc = $datasVencimento[$index]->k00_dtvenc;
                        $novaCusta->k00_receit = $oTaxa->taxa->getReceita();
                        $novaCusta->k00_numtot = $quantidadeParcelas;
                        $novaCusta->k00_valor  = $oTaxa->valor / $quantidadeParcelas;
                        $novaCusta->k00_numpre = $numpre;
                        $novaCusta->k00_numpar = $index;
                        $novaCusta->k00_numcgm = $cgm;
                        $novaCusta->k00_tipo   = 30;
                        $novaCusta->k00_numdig = '0';
                        $novaCusta->k00_tipojm = '0';
                        $novaCusta->k00_hist   = 11304;

                        $indexCustaAgrupada = $novaCusta->k00_receit . $novaCusta->k00_numpre . $novaCusta->k00_numpar;
                        if ($custasAgrupadas[$indexCustaAgrupada]) {
                            $novaCusta->k00_valor = (float)$novaCusta->k00_valor
                                + (float)$custasAgrupadas[$indexCustaAgrupada]->k00_valor;
                        }
                        $custasAgrupadas[$indexCustaAgrupada] = $novaCusta;
                    }
                } else {
                    for ($index = 1; $index <= $numeroMaximoParcelasDivisao; $index++) {
                        $novaCusta = new stdClass();
                        $novaCusta->k00_dtoper = (new DateTime())->format('Y-m-d');
                        $novaCusta->k00_dtvenc = $datasVencimento[$index]->k00_dtvenc;
                        $novaCusta->k00_numtot = $numeroMaximoParcelasDivisao;
                        $novaCusta->k00_receit = $oTaxa->taxa->getReceita();
                        $novaCusta->k00_valor  = $oTaxa->valor / (int)$numeroMaximoParcelasDivisao;
                        $novaCusta->k00_numpre = $numpre;
                        $novaCusta->k00_numpar = $index;
                        $novaCusta->k00_numcgm = $cgm;
                        $novaCusta->k00_tipo   = 30;
                        $novaCusta->k00_numdig = '0';
                        $novaCusta->k00_tipojm = '0';
                        $novaCusta->k00_hist   = 11304;

                        $indexCustaAgrupada = $novaCusta->k00_receit . $novaCusta->k00_numpre . $novaCusta->k00_numpar;
                        if ($custasAgrupadas[$indexCustaAgrupada]) {
                            $novaCusta->k00_valor = (float)$novaCusta->k00_valor
                                + (float)$custasAgrupadas[$indexCustaAgrupada]->k00_valor;
                        }
                        $custasAgrupadas[$indexCustaAgrupada] = $novaCusta;
                    }
                }
            } else {
                $oDaoArrecad = new cl_arrecad();
                $oDaoArrecad->k00_dtoper = (new DateTime())->format('Y-m-d');
                $oDaoArrecad->k00_dtvenc = $datasVencimento[1]->k00_dtvenc;
                $oDaoArrecad->k00_receit = $oTaxa->taxa->getReceita();
                $oDaoArrecad->k00_valor  = $oTaxa->valor;
                $oDaoArrecad->k00_numpre = $numpre;
                $oDaoArrecad->k00_numpar = $this->buscarParcelaVinculadaTaxa(
                    $oTaxa->taxa->getCodigoTaxa()
                );
                $oDaoArrecad->k00_numcgm = $cgm;
                $oDaoArrecad->k00_tipo   = 30;
                $oDaoArrecad->k00_numtot = 1;
                $oDaoArrecad->k00_numdig = '0';
                $oDaoArrecad->k00_tipojm = '0';
                $oDaoArrecad->k00_hist   = 11304;
                $oDaoArrecad->incluir();
            }
        }

        foreach ($custasAgrupadas as $custa) {
            $oDaoArrecad = new cl_arrecad();
            $oDaoArrecad->k00_dtoper = $custa->k00_dtoper;
            $oDaoArrecad->k00_dtvenc = $custa->k00_dtvenc;
            $oDaoArrecad->k00_receit = $custa->k00_receit;
            $oDaoArrecad->k00_numtot = $custa->k00_numtot;
            $oDaoArrecad->k00_valor  = $custa->k00_valor;
            $oDaoArrecad->k00_numpre = $custa->k00_numpre;
            $oDaoArrecad->k00_numpar = $custa->k00_numpar;
            $oDaoArrecad->k00_numcgm = $custa->k00_numcgm;
            $oDaoArrecad->k00_tipo   = $custa->k00_tipo;
            $oDaoArrecad->k00_numdig = $custa->k00_numdig;
            $oDaoArrecad->k00_tipojm = $custa->k00_tipojm;
            $oDaoArrecad->k00_hist   = $custa->k00_hist;
            $oDaoArrecad->incluir();
        }
    }

    /**
     * @param array $taxas
     * @param int $sequencialTermo
     */
    public function gravarCustasParceladas($taxas, $sequencialTermo)
    {
        $processos = $this->buscaProcesso($sequencialTermo);

        foreach ($processos as $processo) {
            foreach ($taxas as $oTaxa) {
                $oDaoCustasParceladas = new cl_custasparceladas();
                $oDaoCustasParceladas->ar58_parcelamento = $sequencialTermo;
                $oDaoCustasParceladas->ar58_processoforo = $processo->v71_processoforo;
                $oDaoCustasParceladas->ar58_taxa = $oTaxa->taxa->getCodigoTaxa();
                $oDaoCustasParceladas->ar58_valor = $oTaxa->valor;
                $oDaoCustasParceladas->incluir();
            }
        }
    }

    /**
     * @param integer $sequencialTermo
     * @return void
     */
    public function deletarPartilhaCustas($sequencialTermo)
    {
        $processos = $this->buscaProcesso($sequencialTermo);
        foreach ($processos as $processo) {
            $partilhaCustas = $this->buscaPartilhaCustas($processo->v71_processoforo);
            foreach ($partilhaCustas as $partilhaCusta) {
                $this->deletarCustasPartilha($partilhaCusta->v77_sequencial);
            }
        }
    }

    /**
     * @param integer $sequencialTermo
     * @return array
     */
    private function buscaProcesso($sequencialTermo)
    {
        $oDaoProcessoForoInicial = new cl_processoforoinicial();
        $sqlProcessos = $oDaoProcessoForoInicial->sql_buscaProcessosForo($sequencialTermo);
        $rsProcessos = db_query($sqlProcessos);
        return db_utils::getCollectionByRecord($rsProcessos);
    }

    /**
     * @param integer $sequencialProcessoForo
     * @return array
     */
    private function buscaPartilhaCustas($sequencialProcessoForo)
    {
        $oDaoPartilhaCusta = new cl_processoforopartilhacusta();
        $sqlPartilhas = $oDaoPartilhaCusta->sql_buscaPartilhasPorProcessoForo($sequencialProcessoForo);
        $rsPartilhas = db_query($sqlPartilhas);

        return db_utils::getCollectionByRecord($rsPartilhas);
    }

    /**
     * @param integer $sequencialPartilhaCusta
     * @return void
     */
    private function deletarCustasPartilha($sequencialPartilhaCusta)
    {
        $oDaoPartilhaCusta = new cl_processoforopartilhacusta();
        $oDaoPartilhaCusta->excluir($sequencialPartilhaCusta, "v77_sequencial = $sequencialPartilhaCusta");
    }

    /**
     * @param integer $sequencialTermo
     * @return array
     */
    private function buscarDatasVencimentoParcelasTermo($sequencialTermo)
    {
        $oDaoTermo = new cl_termo();
        $sqlBuscaDatas = $oDaoTermo->sql_buscar_datas_vencimento_parcelas_termo($sequencialTermo);
        $rsBuscaDatas = db_query($sqlBuscaDatas);
        $DatasEncontradas = db_utils::getCollectionByRecord($rsBuscaDatas);

        $datas = [];
        foreach ($DatasEncontradas as $data) {
            if (!isset($datas[$data->k00_numpar])) {
                $datas[$data->k00_numpar] = $data;
            }
        }

        return $datas;
    }

    /**
     * @param integer $sequencialTermo
     * @return integer
     */
    private function buscarNumpreTermo($sequencialTermo)
    {
        $oDaoTermo = new cl_termo();
        $sqlBuscaTermo = $oDaoTermo->sql_query_file(
            $sequencialTermo,
            'v07_numpre',
            null,
            "v07_parcel = $sequencialTermo"
        );
        $rsBuscaTermo = db_query($sqlBuscaTermo);
        $termoEncontrado = db_utils::getCollectionByRecord($rsBuscaTermo);
        $numpre = $termoEncontrado[0]->v07_numpre;
        return (int)$numpre;
    }

    /**
     * @param integer $sequencialTermo
     * @return void
     */
    public function inserirTaxasArreold($sequencialTermo)
    {
        $numpre = $this->buscarNumpreTermo($sequencialTermo);
        $custasArrecad = $this->buscarCustasArrecad($numpre);

        foreach ($custasArrecad as $custa) {
            $oDaoArreold = new cl_arreold();
            $oDaoArreold->k00_numpre = $custa->k00_numpre;
            $oDaoArreold->k00_numpar = $custa->k00_numpar;
            $oDaoArreold->k00_numcgm = $custa->k00_numcgm;
            $oDaoArreold->k00_dtoper = $custa->k00_dtoper;
            $oDaoArreold->k00_receit = $custa->k00_receit;
            $oDaoArreold->k00_hist   = $custa->k00_hist;
            $oDaoArreold->k00_valor  = $custa->k00_valor;
            $oDaoArreold->k00_dtvenc = $custa->k00_dtvenc;
            $oDaoArreold->k00_numtot = $custa->k00_numtot;
            $oDaoArreold->k00_numdig = $custa->k00_numdig;
            $oDaoArreold->k00_tipo   = $custa->k00_tipo;
            $oDaoArreold->k00_tipojm = $custa->k00_tipojm;
            $oDaoArreold->incluir();

            $oDaoArrecad = new cl_arrecad();
            $oDaoArrecad->excluir(null, "
                k00_numpre = $custa->k00_numpre and
                k00_numpar = $custa->k00_numpar and
                k00_numcgm = $custa->k00_numcgm and
                k00_receit = $custa->k00_receit and
                k00_hist = $custa->k00_hist
            ");
        }
    }

    /**
     * @param integer $numpre
     * @return array
     */
    public function buscarCustasArrecad($numpre)
    {
        $oDaoArrecad = new cl_arrecad();
        $sqlBuscaCustas = $oDaoArrecad->sql_query_file(
            null,
            '*',
            null,
            "k00_numpre = $numpre and k00_hist in (11303, 11304)"
        );
        $rsBuscaCustas = db_query($sqlBuscaCustas);
        return db_utils::getCollectionByRecord($rsBuscaCustas);
    }

    /**
     * @param integer $sequencialTaxa
     * @return integer
     */
    private function buscarParcelaVinculadaTaxa($sequencialTaxa)
    {
        try {
            $oDaoTermoTaxaParc = new cl_termotaxaparc();
            $sqlTermoTaxaParc = $oDaoTermoTaxaParc->sql_query_file(
                null,
                'ar29_numpar',
                'ar29_numpar',
                "ar29_taxa = $sequencialTaxa"
            );
            $rsTermoTaxaParc = db_query($sqlTermoTaxaParc);
            $parcelaEncontrada = db_utils::getCollectionByRecord($rsTermoTaxaParc);
            $numpar = $parcelaEncontrada[0]->ar29_numpar;
            if ($numpar && !empty(strval($numpar))) {
                return (int)$numpar;
            }
        } catch (Exception $error) {
        }
        return 1;
    }

    /**
     * @param integer $sequencialTermo
     * @return void
     */
    public function vincularParcelamento($sequencialTermo)
    {
        $custasTermo = $this->buscaProcessosEIniciaisTermo(
            $sequencialTermo
        );

        $custas = $this->buscaCustasProcessosEIniciais(
            $custasTermo->sProcessos,
            $custasTermo->sIniciais
        );

        foreach ($custas as $custa) {
            $this->vinculaCustaParcelamentoATermo(
                $custa->ar53_sequencial,
                $sequencialTermo
            );
        }
    }

    /**
     * @param integer $sequencialTermo
     * @return object
     */
    private function buscaProcessosEIniciaisTermo($sequencialTermo)
    {
        $oCustasTermo = new stdClass();
        $oCustasTermo->sIniciais = '';
        $oCustasTermo->sProcessos = '';

        $termoRepository = TermoRepository::getInstance()->setReturnFullItem(true);
        $termo           = $termoRepository->getByCode($sequencialTermo);

        $termoIniciais = $termo->getTermoIniciais();

        if (isset($termoIniciais)) {
            foreach ($termoIniciais as $termoInicial) {
                $inicial                = $termoInicial->getInicial()->getCodigo();
                $processoForoRepository = ProcessoForoRepository::getInstance();
                $processo               = $processoForoRepository->getByInicial($inicial);

                if (!empty($inicial)) {
                    $oCustasTermo->sIniciais .= ", $inicial";
                }

                if (!empty($processo)) {
                    $codigoProcesso = $processo->getCodigo();

                    if (!empty($codigoProcesso)) {
                        $oCustasTermo->sProcessos .= ", $codigoProcesso";
                    }
                }
            }
        }

        $oCustasTermo->sIniciais = trim($oCustasTermo->sIniciais, ',');
        $oCustasTermo->sProcessos = trim($oCustasTermo->sProcessos, ',');

        return $oCustasTermo;
    }

    /**
     * @param string $sProcessos
     * @param string $sIniciais
     * @return object
     */
    private function buscaCustasProcessosEIniciais(
        $sProcessos,
        $sIniciais
    ) {
        $oDaoCustasParcelamento = new cl_custasparcelamento();
        $sWhere = "1 = 2";

        if (!empty($sProcessos)) {
            $sWhere .= " or ar53_processoforo in ($sProcessos)";
        }

        if (!empty($sIniciais)) {
            $sWhere .= " or ar53_inicial in ($sIniciais)";
        }

        $sSqlCustas = $oDaoCustasParcelamento->sql_query_file(
            null,
            'ar53_sequencial',
            'ar53_sequencial',
            $sWhere
        );

        $rsCustas = db_query($sSqlCustas);

        return db_utils::getCollectionByRecord($rsCustas);
    }

    /**
     * @param string $sequencialCustaParcelamento
     * @param string $sequencialTermo
     * @return void
     */
    private function vinculaCustaParcelamentoATermo(
        $sequencialCustaParcelamento,
        $sequencialTermo
    ) {
        $oDaoCustasParcelamento = new cl_custasparcelamento();

        $oDaoCustasParcelamento->ar53_sequencial = $sequencialCustaParcelamento;
        $oDaoCustasParcelamento->ar53_parcelamento = $sequencialTermo;

        $oDaoCustasParcelamento->alterar(
            $oDaoCustasParcelamento->ar53_sequencial
        );
    }

    /**
     * @param integer $numeroProcesso
     * @param integer $numeroInicial
     * @return void
     */
    public function setCustasProcesso(
        $numeroProcessoForo = null,
        $numeroInicial = null,
        $sWhere = null
    ) {
        $custas = $this->buscaCustas(
            $numeroProcessoForo,
            $numeroInicial,
            $sWhere
        );

        $this->aCustas = $custas;
    }

    /**
     * @param string[]/object[] $debitos
     * @param integer/string $tipoDebito
     * @param integer/string $cadTipo
     * @return void
     */
    public function setCustasPartilhas(
        $debitos,
        $tipoDebito,
        $cadTipo
    ) {
        $arrayDebitos = array();
        $arrayIniciais = array();

        foreach ($debitos as $debito) {
            if (isset($debito->inicial)) {
                $arrayDebitos[] = $debito->inicial;
                $arrayIniciais[] = $debito->inicial;
                continue;
            }

            $arrayDebitos[] = $debito;
        }

        $service = Factory::create(
            $tipoDebito,
            $cadTipo,
            $arrayDebitos
        );
        $debitos = $service->processar();

        $processos = array();
        $iniciais = array();

        foreach ($debitos as $debito) {
            if ($debito instanceof ProcessoForoEntity) {
                $processos[] = $debito;
            }

            if ($debito instanceof InicialEntity) {
                $iniciais[] = $debito;
            }
        }

        foreach ($processos as $processo) {
            $codigoProcesso = $processo->getCodigo();
            $custasProcessoForo = $this->buscaCustas($codigoProcesso);

            foreach ($processo->getProcessoForoPartilhas() as $partilha) {
                foreach ($partilha->getCustas() as $custaPartilha) {
                    $informacoesCusta = new stdClass();
                    $codigoTaxa = $custaPartilha->getTaxa()->getCodigoTaxa();

                    $custaParcelamento = $custasProcessoForo;
                    $custaParcelamento = array_values(
                        array_filter(
                            $custaParcelamento,
                            function ($item) use ($codigoTaxa) {
                                return $item->ar36_sequencial == $codigoTaxa;
                            }
                        )
                    );

                    if (count($custaParcelamento) == 0) {
                        continue;
                    }

                    $custaParcelamento   = $custaParcelamento[0];
                    $petmiteParcelamento = $this->validaPermiteParcelamento(
                        $custaParcelamento
                    );

                    $isHonorario = $this->validaHonorario($custaParcelamento);

                    $informacoesCusta->permiteParcelamento = $petmiteParcelamento;
                    $informacoesCusta->numeroMaximoParcelas = $custaParcelamento->numeromaximoparcelas;
                    $informacoesCusta->taxa = $custaPartilha->getTaxa();
                    $informacoesCusta->valor = $custaPartilha->getValor();
                    $informacoesCusta->ishonorario = $isHonorario;
                    $informacoesCusta->codigoforo = $processo->getCodigoForo();

                    $this->aCustasPartilha[] = $informacoesCusta;
                }
            }
        }

        foreach ($iniciais as $inicial) {
            $codigoInicial = $inicial->getCodigo();
            $custasInicial = $this->buscaCustas(null, $codigoInicial);

            foreach ($inicial->getInicialPartilhas() as $partilha) {
                foreach ($partilha->getCustas() as $custaPartilha) {
                    $informacoesCusta = new stdClass();
                    $codigoTaxa = $custaPartilha->getTaxa()->getCodigoTaxa();

                    $custaParcelamento = $custasInicial;
                    $custaParcelamento = array_values(
                        array_filter(
                            $custaParcelamento,
                            function ($item) use ($codigoTaxa) {
                                return $item->ar36_sequencial == $codigoTaxa;
                            }
                        )
                    );

                    if (count($custaParcelamento) == 0) {
                        continue;
                    }

                    $custaParcelamento   = $custaParcelamento[0];
                    $petmiteParcelamento = $this->validaPermiteParcelamento(
                        $custaParcelamento
                    );

                    $isHonorario = $this->validaHonorario($custaParcelamento);

                    $informacoesCusta->permiteParcelamento = $petmiteParcelamento;
                    $informacoesCusta->numeroMaximoParcelas = $custaParcelamento->numeromaximoparcelas;
                    $informacoesCusta->taxa = $custaPartilha->getTaxa();
                    $informacoesCusta->valor = $custaPartilha->getValor();
                    $informacoesCusta->ishonorario = $isHonorario;
                    $informacoesCusta->codigoforo = $inicial->getCodigoForo();

                    $this->aCustasPartilha[] = $informacoesCusta;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getCustasPartilhas()
    {
        return $this->aCustasPartilha;
    }

    /**
     * @return array
     */
    public function getCustasHonorariosPartilhas()
    {
        return array_filter(
            $this->aCustasPartilha,
            function ($item) {
                return $item->ishonorario;
            }
        );
    }

    /**
     * @return array
     */
    public function getCustasProcesso()
    {
        return $this->aCustas;
    }

    /**
     * @return array
     */
    public function getCustasProcessoParceladas()
    {
        $custasParceladas = array_filter(
            $this->aCustas,
            function ($custa) {
                return $this->validaPermiteParcelamento($custa);
            }
        );

        return $custasParceladas;
    }

    private function buscaCustas($numeroProcessoForo = null, $numeroInicial = null, $sWhere = null)
    {
        $oDaoTaxa = new cl_taxa();
        $sSqlCustas =  $oDaoTaxa->sql_taxasProcesso(
            $numeroProcessoForo,
            $numeroInicial,
            $sWhere
        );

        return db_utils::getCollectionByRecord(db_query($sSqlCustas));
    }

    /**
     * @param integer $sequencialTaxa
     * @return boolean
     */
    public function validaPermissaoParcelamentoCusta($sequencialTaxa)
    {
        $custasEncontradas = $this->buscaCustasComParcelamentoPorCodigo(
            $sequencialTaxa
        );

        return count($custasEncontradas) > 0;
    }

    /**
     * @param integer $sequencialTaxa
     * @return array
     */
    private function buscaCustasComParcelamentoPorCodigo($sequencialTaxa)
    {
        return array_filter(
            $this->getCustasComParcelamento(),
            function ($custa) use ($sequencialTaxa) {
                return (int)$custa->ar36_sequencial == $sequencialTaxa;
            }
        );
    }

    /**
     * @param integer $sequencialTaxa
     * @return integer
     */
    public function getNumeroMaximoParcelasCusta($sequencialTaxa)
    {

        $custaEncontrada = (array_filter(
            $this->getCustasComParcelamento(),
            function ($custa) use ($sequencialTaxa) {
                return (int)$custa->ar36_sequencial
                    == $sequencialTaxa;
            }
        ));

        if (count($custaEncontrada) > 0) {
            $numeroMaximoParcelas = array_shift($custaEncontrada)
                ->numeromaximoparcelas;

            $numeroMaximoParcelas = (isset($numeroMaximoParcelas)
                && !empty((string)($numeroMaximoParcelas)))
                ? $numeroMaximoParcelas
                : '99999999999';

            return $numeroMaximoParcelas;
        }

        return 0;
    }

    /**
     * @return array
     */
    public function getCustasComParcelamento()
    {
        $custasFiltradas = $this->aCustas;

        $custasFiltradas = array_filter(
            $custasFiltradas,
            function ($custa) {
                return $this->validaPermiteParcelamento($custa);
            }
        );

        return $custasFiltradas;
    }

    /**
     * @return array
     */
    public function getCustasSemParcelamento()
    {
        $custasFiltradas = $this->aCustas;

        $custasFiltradas = array_filter(
            $custasFiltradas,
            function ($custa) {
                return !$this->validaPermiteParcelamento($custa);
            }
        );

        return $custasFiltradas;
    }

    /**
     * @return float
     */
    public function getTotalCustasPartilhaParcelado()
    {
        return $this->getTotalHonorariosPartilhaParcelado()
            + $this->getTotalTaxasPartilhaParcelado();
    }

    /**
     * @return float
     */
    public function getTotalCustasPartilhaNaoParcelado()
    {
        return $this->getTotalHonorariosPartilhaNaoParcelado()
            + $this->getTotalTaxasPartilhaNaoParcelado();
    }

    /**
     * @return float
     */
    public function getTotalHonorariosPartilhaParcelado()
    {
        return $this->getTotalCustasPartilha(true, true);
    }

    /**
     * @return float
     */
    public function getTotalHonorariosPartilhaNaoParcelado()
    {
        return $this->getTotalCustasPartilha(false, true);
    }

    /**
     * @return float
     */
    public function getTotalTaxasPartilhaParcelado()
    {
        return $this->getTotalCustasPartilha(true, false);
    }

    /**
     * @return float
     */
    public function getTotalTaxasPartilhaNaoParcelado()
    {
        return $this->getTotalCustasPartilha(false, false);
    }

    /**
     * @param boolean $comParcelamento
     * @param boolean $apenasHonorario
     * @return float
     */
    private function getTotalCustasPartilha($comParcelamento, $apenasHonorario)
    {
        $custas = array_filter(
            $this->aCustasPartilha,
            function ($item) use (
                $comParcelamento,
                $apenasHonorario
            ) {

                $permiteParcelamento = $comParcelamento
                    ? $item->permiteParcelamento
                    : !$item->permiteParcelamento;

                $apenasHonorario = $apenasHonorario
                    ? $item->ishonorario
                    : !$item->ishonorario;


                return $permiteParcelamento && $apenasHonorario;
            }
        );

        $valorTotalCustas = 0;

        foreach ($custas as $custa) {
            $valorTotalCustas = $valorTotalCustas + $custa->valor;
        }

        return (float)$valorTotalCustas;
    }

    /**
     * @param object custa
     * @return boolean
     */
    private function validaPermiteParcelamento($custa)
    {
        return ($custa->permiteparcelamento == 't');
    }

    /**
     * @param object custa
     * @return boolean
     */
    private function validaHonorario($custa)
    {
        return ($custa->ar36_honorario == 't');
    }
}
