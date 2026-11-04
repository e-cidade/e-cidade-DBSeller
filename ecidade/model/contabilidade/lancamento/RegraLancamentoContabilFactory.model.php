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

/**
 * Factory para decidir qual regra deve aplicar com base no documento contábil.
 * @author Andrio Costa
 * @package contabilidade
 * @subpackage lancamento
 * @version $Revision: 1.87 $
 */
class RegraLancamentoContabilFactory
{

    public function __construct()
    {
    }

    /**
     * Metodo para retornar o objeto da regra, pesquisando pelo codigo do documento
     * @param integer $iDocumento
     * @return IRegraLancamentoContabil
     * @throws Exception
     */
    private static function getInstanciaPorDocumento($iDocumento)
    {

        $aRegras = array(
            1 => "RegraLancamentoEmpenhoFinanceiro",
            2 => "RegraLancamentoEmpenhoFinanceiro",
            3 => "RegraLancamentoLiquidacaoEmpenho",
            4 => "RegraLancamentoLiquidacaoEmpenho",
            23 => "RegraLancamentoLiquidacaoEmpenho",
            24 => "RegraLancamentoLiquidacaoEmpenho",
            33 => "RegraLancamentoLiquidacaoEmpenho",
            333 => "RegraLancamentoLiquidacaoEmpenho",
            34 => "RegraLancamentoLiquidacaoEmpenho",
            334 => "RegraLancamentoLiquidacaoEmpenho",
            39 => "RegraLancamentoControle",
            40 => "RegraLancamentoControle",
            80 => "RegraInscricaoPassivoSemSuporteOrcamentario",
            81 => "RegraBaixaInscricaoPassivoSemSuporteOrcamentario",
            82 => "RegraEmpenhoPassivoSemSuporteOrcamentario",
            84 => "RegraLiquidacaoEmpenhoPassivoSemSuporteOrcamentario",
            85 => "RegraLiquidacaoEmpenhoPassivoSemSuporteOrcamentario",
            90 => "RegraLancamentoSuprimentoDeFundos",
            91 => "RegraLancamentoSuprimentoDeFundos",
            92 => "RegraLancamentoSuprimentoDeFundos",
            100 => "RegraArrecadacaoReceita",



            101 => "RegraArrecadacaoReceita",
            105 => "RegraReconhecimentoReceitaFatoGerador",
            106 => "RegraReconhecimentoReceitaFatoGerador",
            107 => "RegraArrecadacaoReceita",
            108 => "RegraArrecadacaoReceita",
            109 => "RegraArrecadacaoReceita",
            110 => "RegraArrecadacaoReceita",
            111 => "RegraArrecadacaoReceita",
            112 => "RegraArrecadacaoReceita",
            113 => "RegraArrecadacaoReceita",
            114 => "RegraArrecadacaoReceita",
            115 => "RegraArrecadacaoReceita",
            116 => "RegraArrecadacaoReceita",
            117 => "RegraArrecadacaoReceita",
            118 => "RegraArrecadacaoReceita",
            120 => "RegraPagamentoSlip",
            121 => "RegraAnulacaoSlip",
            130 => "RegraPagamentoSlip",
            131 => "RegraAnulacaoSlip",
            140 => "RegraPagamentoSlip",
            141 => "RegraAnulacaoSlip",
            142 => "RegraPagamentoSlip",
            143 => "RegraAnulacaoSlip",
            150 => "RegraPagamentoSlip",
            151 => "RegraPagamentoSlip",
            152 => "RegraAnulacaoSlip",
            153 => "RegraAnulacaoSlip",
            160 => "RegraPagamentoSlip",
            161 => "RegraPagamentoSlip",
            162 => "RegraAnulacaoSlip",
            163 => "RegraAnulacaoSlip",

            165 => "RegraArrecadacaoReceita",
            //166 => "RegraArrecadacaoReceita",  // ESTORNO do TEF

            167 => "RegraLancamentoTef",
            //168 => "RegraArrecadacaoReceita", // ESTORNO da Corrente do TEF

            169 => "RegraLancamentoTef",

            200 => "RegraEmLiquidacao",
            201 => "RegraEmLiquidacao",

            202 => "RegraLancamentoLiquidacaoEmpenho",
            203 => "RegraLancamentoLiquidacaoEmpenho",

            204 => "RegraLancamentoControle",
            205 => "RegraLancamentoControle",
            206 => "RegraLancamentoControle",
            207 => "RegraLancamentoControle",

            208 => "RegraLancamentoEmLiquidacaoMaterialPermanente",
            209 => "RegraLancamentoEmLiquidacaoMaterialPermanente",

            210 => "RegraLancamentoEmLiquidacaoMaterialConsumo",
            211 => "RegraLancamentoEmLiquidacaoMaterialConsumo",
            212 => "RegraLancamentoEmLiquidacaoMaterialConsumo",
            213 => "RegraLancamentoEmLiquidacaoMaterialConsumo",

            214 => "RegraLancamentoEmLiquidacaoMaterialPermanente",
            215 => "RegraLancamentoEmLiquidacaoMaterialPermanente",

            216 => "RegraLancamentoControleLiquidacao",
            217 => "RegraLancamentoControleLiquidacao",

            300 => "RegraLancamentoProvisaoFerias",
            301 => "RegraLancamentoProvisaoFerias",

            302 => "RegraLancamentoProvisaoDecimoTerceiro",
            303 => "RegraLancamentoProvisaoDecimoTerceiro",

            304 => "RegraLancamentoProvisaoFerias",
            305 => "RegraLancamentoProvisaoFerias",
            306 => "RegraLancamentoProvisaoFerias",
            307 => "RegraLancamentoProvisaoFerias",

            308 => "RegraLancamentoProvisaoDecimoTerceiro",
            309 => "RegraLancamentoProvisaoDecimoTerceiro",
            310 => "RegraLancamentoProvisaoDecimoTerceiro",
            311 => "RegraLancamentoProvisaoDecimoTerceiro",

            // apropriacao de decimo e ferias
            350 => "RegraApropriacaoDecimoFerias",
            352 => "RegraApropriacaoDecimoFerias",
            354 => "RegraApropriacaoDecimoFerias",
            356 => "RegraApropriacaoDecimoFerias",
            358 => "RegraApropriacaoDecimoFerias",
            360 => "RegraApropriacaoDecimoFerias",
            362 => "RegraApropriacaoDecimoFerias",
            364 => "RegraApropriacaoDecimoFerias",
            366 => "RegraApropriacaoDecimoFerias",
            368 => "RegraApropriacaoDecimoFerias",
            370 => "RegraApropriacaoDecimoFerias",
            372 => "RegraApropriacaoDecimoFerias",
            420 => "RegraApropriacaoDecimoFerias",
            422 => "RegraApropriacaoDecimoFerias",
            424 => "RegraApropriacaoDecimoFerias",
            426 => "RegraApropriacaoDecimoFerias",
            428 => "RegraApropriacaoDecimoFerias",
            430 => "RegraApropriacaoDecimoFerias",
            432 => "RegraApropriacaoDecimoFerias",
            434 => "RegraApropriacaoDecimoFerias",
            436 => "RegraApropriacaoDecimoFerias",
            438 => "RegraApropriacaoDecimoFerias",
            440 => "RegraApropriacaoDecimoFerias",
            442 => "RegraApropriacaoDecimoFerias",

            // extorno de apropriacao de décimo e ferias
            351 => "RegraApropriacaoDecimoFerias",
            353 => "RegraApropriacaoDecimoFerias",
            355 => "RegraApropriacaoDecimoFerias",
            357 => "RegraApropriacaoDecimoFerias",
            359 => "RegraApropriacaoDecimoFerias",
            361 => "RegraApropriacaoDecimoFerias",
            363 => "RegraApropriacaoDecimoFerias",
            365 => "RegraApropriacaoDecimoFerias",
            367 => "RegraApropriacaoDecimoFerias",
            369 => "RegraApropriacaoDecimoFerias",
            371 => "RegraApropriacaoDecimoFerias",
            373 => "RegraApropriacaoDecimoFerias",
            421 => "RegraApropriacaoDecimoFerias",
            423 => "RegraApropriacaoDecimoFerias",
            425 => "RegraApropriacaoDecimoFerias",
            427 => "RegraApropriacaoDecimoFerias",
            429 => "RegraApropriacaoDecimoFerias",
            431 => "RegraApropriacaoDecimoFerias",
            433 => "RegraApropriacaoDecimoFerias",
            435 => "RegraApropriacaoDecimoFerias",
            437 => "RegraApropriacaoDecimoFerias",
            439 => "RegraApropriacaoDecimoFerias",
            441 => "RegraApropriacaoDecimoFerias",
            443 => "RegraApropriacaoDecimoFerias",

            400 => "RegraMovimentacaoEstoqueSaida",
            401 => "RegraMovimentacaoEstoqueSaida",

            402 => "RegraLancamentoEntradaEstoque",
            403 => "RegraLancamentoEntradaEstoque",

            404 => "RegraMovimentacaoEstoqueSaida",

            410 => "RegraLancamentoEmpenhoFinanceiro",
            411 => "RegraLancamentoEmpenhoFinanceiro",

            412 => "RegraLancamentoDevolucaoAdiantamento",
            413 => "RegraLancamentoDevolucaoAdiantamento",
            414 => "RegraLancamentoDevolucaoAdiantamento",
            415 => "RegraLancamentoDevolucaoAdiantamento",
            416 => "RegraLancamentoDevolucaoAdiantamento",
            417 => "RegraLancamentoDevolucaoAdiantamento",

            418 => "RegraArrecadacaoReceita",
            419 => "RegraArrecadacaoReceita",

            500 => "RegraLancamentoEmpenhoFinanceiro",
            501 => "RegraLancamentoEmpenhoFinanceiro",
            502 => "RegraLancamentoLiquidacaoEmpenhoPrecatorio",
            503 => "RegraLancamentoLiquidacaoEmpenhoPrecatorio",

            504 => "RegraLancamentoEmpenhoFinanceiro",
            506 => "RegraLancamentoEmpenhoFinanceiro",
            507 => "RegraLancamentoEmpenhoFinanceiro",

            508 => "RegraLancamentoReconhecimentoContabil",
            509 => "RegraLancamentoReconhecimentoContabil",
            510 => "RegraLancamentoReconhecimentoContabil",
            511 => "RegraLancamentoReconhecimentoContabil",
            513 => "RegraLancamentoReconhecimentoContabil",
            514 => "RegraLancamentoReconhecimentoContabil",

            600 => "RegraLancamentoReavaliacaoBem",
            601 => "RegraLancamentoReavaliacaoBem",
            602 => "RegraLancamentoReavaliacaoBem",
            603 => "RegraLancamentoReavaliacaoBem",

            604 => "RegraLancamentoContaDepreciacao",
            605 => "RegraLancamentoContaDepreciacao",

            700 => "RegraLancamentoIncorporacaoBem",
            701 => "RegraLancamentoIncorporacaoBem",
            702 => "RegraLancamentoIncorporacaoBem",
            705 => "RegraLancamentoIncorporacaoBem",
            706 => "RegraLancamentoIncorporacaoBem",
            707 => "RegraLancamentoIncorporacaoBem",
            708 => "RegraLancamentoIncorporacaoBem",
            709 => "RegraLancamentoIncorporacaoBem",
            710 => "RegraLancamentoIncorporacaoBem",

            703 => "RegraLancamentoAjusteBaixaBem",

            900 => "RegraLancamentoAcordo",
            901 => "RegraLancamentoAcordo",
            903 => "RegraLancamentoAcordo",
            904 => "RegraLancamentoAcordo",

            2001 => "RegraLancamentoAberturaExercicio",
            2002 => "RegraLancamentoAberturaExercicio",
            2003 => "RegraLancamentoAberturaExercicio",
            2004 => "RegraLancamentoAberturaExercicio",

            2005 => "RegraLancamentoRestosAPagar",
            2006 => "RegraLancamentoRestosAPagar",
            2007 => "RegraLancamentoRestosAPagar",
            2008 => "RegraLancamentoRestosAPagar",
            2009 => "RegraLancamentoRestosAPagar",
            2010 => "RegraLancamentoRestosAPagar",
            2011 => "RegraLancamentoRestosAPagar",
            2012 => "RegraLancamentoRestosAPagar",

            1007 => "RegraLancamentoEncerramentoRP",
            1008 => "RegraLancamentoEncerramentoRP",
            1009 => "RegraLancamentoEncerramentoVariacaoPatrimonial",

            1010 => "RegraLancamentoEncerramentoOrcamentarioReceita",
            1020 => "RegraLancamentoEncerramentoOrcamentarioReceita",
            1021 => "RegraLancamentoEncerramentoOrcamentarioReceita",
            1022 => "RegraLancamentoEncerramentoOrcamentarioReceita",
            1023 => "RegraLancamentoEncerramentoOrcamentarioReceita",
            1011 => "RegraLancamentoEncerramentoResto",
            1024 => "RegraLancamentoEncerramentoResto",
            1012 => "RegraLancamentoEncerramentoResto",
            1025 => "RegraLancamentoEncerramentoResto",
            1026 => "RegraLancamentoEncerramentoResto",
            1013 => "RegraLancamentoEncerramentoResto",
            1014 => "RegraLancamentoEncerramentoResto",
            1015 => "RegraLancamentoEncerramentoResto",
            1016 => "RegraLancamentoEncerramentoResto",
            1017 => "RegraLancamentoEncerramentoResto",
            1018 => "RegraLancamentoEncerramentoResto",
            1019 => "RegraLancamentoEncerramentoOrcamentarioDespesa",

            2013 => "RegraLancamentoAberturaExercicio",
            2014 => "RegraLancamentoAberturaExercicio",
            2015 => "RegraLancamentoAberturaExercicio",
            2016 => "RegraLancamentoAberturaExercicio",
            2017 => "RegraLancamentoAberturaExercicio",
            2018 => "RegraLancamentoAberturaExercicio",
            2019 => "RegraLancamentoAberturaExercicio",
            2020 => "RegraLancamentoAberturaExercicio",

            2021 => "RegraLancamentoRecursosExercicioAnteriorControles",

            2030 => "RegraLancamentoAberturaResto",
            2031 => "RegraLancamentoAberturaResto",
            2032 => "RegraLancamentoAberturaResto",
            2033 => "RegraLancamentoAberturaResto",

            2034 => "RegraLancamentoTransferenciaSaldoRP",
            2035 => "RegraLancamentoTransferenciaSaldoRP",


            3000 => "RegraLancamentoRetificacao",
            3001 => "RegraLancamentoRetificacao",

            4000 => "RegraLancamentoReconhecimentoCompetencia",
            4001 => "RegraLancamentoReconhecimentoCompetencia",
            4002 => "RegraLancamentoReconhecimentoCompetencia",
            4003 => "RegraLancamentoReconhecimentoCompetencia",
            4010 => "RegraLancamentoReconhecimentoCompetencia",

            5000 => "RegraPagamentoSlip",
            5001 => "RegraPagamentoSlip",
            5002 => "RegraPagamentoSlip",
            5003 => "RegraPagamentoSlip",
            5004 => "RegraBaixaPagamentoLimiteSaque",
            5005 => "RegraBaixaPagamentoLimiteSaque",
            6000 => 'RegraLancamentoApropriacaoRetencao',
            6001 => 'RegraLancamentoApropriacaoRetencao',
            6002 => 'RegraLancamentoApropriacaoRetencao',
            6003 => 'RegraLancamentoApropriacaoRetencao',

            6004 => 'RegraLancamentoApropriacaoRetencao',
            6005 => 'RegraLancamentoApropriacaoRetencao',
            6006 => 'RegraLancamentoApropriacaoRetencao',
            6007 => 'RegraLancamentoApropriacaoRetencao',
            6008 => 'RegraLancamentoApropriacaoRetencao',
            6009 => 'RegraLancamentoApropriacaoRetencao',
            6010 => 'RegraLancamentoApropriacaoRetencao',
            6011 => 'RegraLancamentoApropriacaoRetencao',
            6012 => 'RegraLancamentoApropriacaoRetencao',
            6013 => 'RegraLancamentoApropriacaoRetencao',

        );

        if (!array_key_exists($iDocumento, $aRegras)) {
            $erro = "ERRO TÉCNICO : Não foi possivel localizar a regra para o documento {$iDocumento}.";
            throw new BusinessException($erro);
        }

        return new $aRegras[$iDocumento];
    }

    /**
     * Deve retornar a conta contabil para efetuar o lançamento
     * @param $iDocumento
     * @param $iLancamento
     * @param ILancamentoAuxiliar $oLancamentoAuxiliar
     * @return RegraLancamentoContabil
     * @throws Exception
     */
    public static function getRegraLancamento(
        $iDocumento,
        $iLancamento,
        ILancamentoAuxiliar $oLancamentoAuxiliar
    ) {

        $oRegra = self::getInstanciaPorDocumento($iDocumento);
        /**
         * @see mapearLancamentoAuxliarPorDocumento
         */
        self::mapearLancamentoAuxliarPorDocumento($iDocumento, $oLancamentoAuxiliar);
        return $oRegra->getRegraLancamento($iDocumento, $iLancamento, $oLancamentoAuxiliar);
    }

    /**
     * Método que guarda em um arquivo CSV o documento executado e qual lançamento auxiliar foi usado.
     * Esta rotina foi criada para conseguirmos mapear o documento e que lancamento auxiliar ele pode utilizar, depois
     * do mapeamento realizado, refatoraremos os lançamentos auxiliares limpando métodos e informações duplicadas.
     * @param $iCodigoDocumento - Código do Documento que está sendo executado
     * @param $oLancamentoAuxiliar - Lancamento auxiliar utilizado para executar o lançamento contábil
     * @return boolean - true
     */
    public static function mapearLancamentoAuxliarPorDocumento(
        $iCodigoDocumento,
        ILancamentoAuxiliar $oLancamentoAuxiliar
    ) {

        if (!file_exists("cache")) {
            mkdir("cache", 0777);
        }

        $sNomeArquivo = "cache/mapeamento_lancamentoauxiliar_documento.csv";
        $sLinhaArquivo = "{$iCodigoDocumento}," . get_class($oLancamentoAuxiliar) . "\n";
        $aDadosArquivo = array();
        if (file_exists($sNomeArquivo)) {
            $aDadosArquivo = file($sNomeArquivo);
        }
        if (!in_array($sLinhaArquivo, $aDadosArquivo)) {
            $hAbreArquivo = fopen($sNomeArquivo, "a");
            fwrite($hAbreArquivo, $sLinhaArquivo);
            fclose($hAbreArquivo);
        }
        return true;
    }
}
