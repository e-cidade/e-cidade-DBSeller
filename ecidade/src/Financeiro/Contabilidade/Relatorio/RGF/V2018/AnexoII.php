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

namespace ECidade\Financeiro\Contabilidade\Relatorio\RGF\V2018;

use App\Domain\Financeiro\Contabilidade\Factories\AnexoTresFactory;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTres\AnexoTresService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTresInRsService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTresMdfService;
use cl_assinatura;
use DBDate;
use ECidade\Financeiro\Contabilidade\Calculo\ReceitaCorrenteLiquida;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\Linha;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\ProcessamentoRelatorioLegal;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoIII as ReceitaCorrenteFactory;
use Exception;
use InstituicaoRepository;
use PDFDocument;
use Periodo;
use stdClass;

/**
 * Class AnexoII.
 */
class AnexoII extends ProcessamentoRelatorioLegal
{
    const CODIGO_RELATORIO = 183;

    /**
     * @todo verificar se todos relatórios do RGF utilizam RCL e mover para ProcessamentoRelatorioLegal
     *
     * @var ReceitaCorrenteLiquida
     */
    protected $oRCL;

    /**
     * Váriaveis de controle do pdf.
     */
    private $iLarguraColunaSaldoExercicio = 84;
    private $iLarguraColunaSaldoPeriodo = 0;
    private $iLarguraColunaDescricao = 78;
    private $iLarguraColunaExercicioAnterior = 28;

    /**
     * @var array
     */
    private $aTipoBorda = [
        1 => ['R', 'LR', 'L'],
        2 => ['TBR', '1', 'TBL'],
    ];

    /**
     * @var array
     */
    protected $aColunaRecalcularPeriodo = [
        12 => 1,
        13 => 2,
        14 => 1,
        15 => 2,
        16 => 3,
    ];

    /**
     * Mapeia os períodos do RCL que devem ser calculado de acordo com o Período selecionado   *.
     */
    protected $aPeriodoCalcular = [
        12 => [12],
        13 => [12, 13],
        14 => [14],
        15 => [14, 15],
        16 => [14, 15, 16],
    ];

    protected $oDataInicio;

    /**
     * Número de colunas no saldo do exercício, Quadrimestre = 3 e Semestre = 2.
     *
     * @var int
     */
    private $iNumeroDePeriodos = 3;

    /**
     * AnexoII constructor.
     *
     * @param int $iAno
     *
     * @throws Exception
     */
    public function __construct($iAno, Periodo $oPeriodo)
    {
        $this->oDataInicio = new DBDate("{$iAno}-01-01");

        $aInstituicoes = InstituicaoRepository::getInstituicoes();

        /*
        nao deve mais tirar as instituicoes do tipo rpps
        $aInstituicoes = array_filter($aInstituicoes, function (Instituicao $oInstiuicao) {
            return ($oInstiuicao->getTipo() != 5 && $oInstiuicao->getTipo() != 6);
        });
        */

        parent::__construct($iAno, $oPeriodo, static::CODIGO_RELATORIO, $aInstituicoes);

//        $this->oRCL = new ReceitaCorrenteLiquida($iAno, null, 178);

        if (in_array($oPeriodo->getCodigo(), [12, 13])) {
            $this->iNumeroDePeriodos = 2;
        }

        $this->iLarguraColunaSaldoPeriodo = $this->iLarguraColunaSaldoExercicio / $this->iNumeroDePeriodos;
    }

    /**
     * @throws Exception
     */
    public function processar()
    {
        $this->aLinhas = [];
        if (empty($this->aLinhasConsistencia)) {
            $this->getDados();
        }

        $this->aLinhasConsistencia[26]->descricao = 'RECEITA CORRENTE LÍQUIDA - RCL (IV)';
        $this->aLinhasConsistencia[27]->descricao = '% da DC sobre a RCL AJUSTADA (I/VI)';
        $this->aLinhasConsistencia[28]->descricao = '% da DCL sobre a RCL AJUSTADA (III/VI)';
        $this->aLinhasConsistencia[32]->descricao = 'PRECATÓRIOS POSTERIORES A 05/05/2000 (Não incluídos na DC)2';
        $this->aLinhasConsistencia['26.1'] = $this->aLinhasConsistencia[40];
        $this->aLinhasConsistencia['26.2'] = $this->aLinhasConsistencia[41];

        $this->aLinhasConsistencia[39]->descricao = 'APROPRIAÇÃO DE DEPÓSITOS JUDICIAIS';

        foreach (array_keys($this->aLinhasConsistencia) as $iIndice) {
            $this->aLinhasConsistencia[$iIndice]->primeiro_periodo = 0;
            $this->aLinhasConsistencia[$iIndice]->segundo_periodo = 0;
            $this->aLinhasConsistencia[$iIndice]->terceiro_periodo = 0;
        }

        foreach ($this->aPeriodoCalcular[$this->oPeriodo->getCodigo()] as $iPeriodo) {
            $iColuna = $this->aColunaRecalcularPeriodo[$iPeriodo];
            $oDataFinal = Periodo::dataFinalPeriodo($iPeriodo, $this->iAno);
            $this->processarBalanceteVerificacaoParaColunaPorData($iColuna, $this->oDataInicio, $oDataFinal);
        }

        $this->processaRcl();

        if (in_array($this->getPeriodo()->getCodigo(), [14, 15, 16])) {
            $this->processaValorManualPorLinhaEColuna('26.1', 3);
        }

        $this->aLinhasConsistencia['26.2']->saldo_exercicio_anterior =
            $this->aLinhasConsistencia[26]->saldo_exercicio_anterior -
            $this->aLinhasConsistencia['26.1']->saldo_exercicio_anterior;

        $this->aLinhasConsistencia['26.2']->primeiro_periodo =
            $this->aLinhasConsistencia[26]->primeiro_periodo - $this->aLinhasConsistencia['26.1']->primeiro_periodo;

        $this->aLinhasConsistencia['26.2']->segundo_periodo =
            $this->aLinhasConsistencia[26]->segundo_periodo - $this->aLinhasConsistencia['26.1']->segundo_periodo;

        $this->aLinhasConsistencia['26.2']->terceiro_periodo =
            $this->aLinhasConsistencia[26]->terceiro_periodo - $this->aLinhasConsistencia['26.1']->terceiro_periodo;

        $aLinhaProcessarManual = [11, 8, 4, 3, 1, 21, 20, 25, 29, 30];

        foreach ($aLinhaProcessarManual as $aLinha) {
            $this->processarFormulaDaLinha($aLinha);
        }

        $this->aLinhasConsistencia['27']->saldo_exercicio_anterior = 0;
        $this->aLinhasConsistencia['27']->primeiro_periodo = 0;
        $this->aLinhasConsistencia['27']->segundo_periodo = 0;
        $this->aLinhasConsistencia['27']->terceiro_periodo = 0;
        $this->aLinhasConsistencia['28']->saldo_exercicio_anterior = 0;
        $this->aLinhasConsistencia['28']->primeiro_periodo = 0;
        $this->aLinhasConsistencia['28']->segundo_periodo = 0;
        $this->aLinhasConsistencia['28']->terceiro_periodo = 0;

        if ($this->aLinhasConsistencia['26.2']->saldo_exercicio_anterior != 0) {
            $this->aLinhasConsistencia['27']->saldo_exercicio_anterior =
                (($this->aLinhasConsistencia['1']->saldo_exercicio_anterior /
                        $this->aLinhasConsistencia['26.2']->saldo_exercicio_anterior) * 100);

            $this->aLinhasConsistencia['28']->saldo_exercicio_anterior =
                (($this->aLinhasConsistencia['25']->saldo_exercicio_anterior /
                        $this->aLinhasConsistencia['26.2']->saldo_exercicio_anterior) * 100);
        }

        if ($this->aLinhasConsistencia['26.2']->primeiro_periodo != 0) {
            $this->aLinhasConsistencia['27']->primeiro_periodo =
                (($this->aLinhasConsistencia['1']->primeiro_periodo /
                        $this->aLinhasConsistencia['26.2']->primeiro_periodo) * 100);

            $this->aLinhasConsistencia['28']->primeiro_periodo =
                (($this->aLinhasConsistencia['25']->primeiro_periodo /
                        $this->aLinhasConsistencia['26.2']->primeiro_periodo) * 100);
        }

        if ($this->aLinhasConsistencia['26.2']->segundo_periodo != 0) {
            $this->aLinhasConsistencia['27']->segundo_periodo =
                (($this->aLinhasConsistencia['1']->segundo_periodo /
                        $this->aLinhasConsistencia['26.2']->segundo_periodo) * 100);

            $this->aLinhasConsistencia['28']->segundo_periodo =
                (($this->aLinhasConsistencia['25']->segundo_periodo /
                        $this->aLinhasConsistencia['26.2']->segundo_periodo) * 100);
        }

        if ($this->aLinhasConsistencia['26.2']->terceiro_periodo != 0) {
            $this->aLinhasConsistencia['27']->terceiro_periodo =
                (($this->aLinhasConsistencia['1']->terceiro_periodo /
                        $this->aLinhasConsistencia['26.2']->terceiro_periodo) * 100);

            $this->aLinhasConsistencia['28']->terceiro_periodo =
                (($this->aLinhasConsistencia['25']->terceiro_periodo /
                        $this->aLinhasConsistencia['26.2']->terceiro_periodo) * 100);
        }

        unset($this->aLinhasConsistencia[40], $this->aLinhasConsistencia[41]);
        ksort($this->aLinhasConsistencia);

        $this->aLinhas = $this->aLinhasConsistencia;
    }

    /**
     * @param string $sTitulo
     * @param bool $lParagrafoOficial
     */
    public function cabecalhoQuadro1(PDFDocument $oPdf, $sTitulo = 'DÍVIDA CONSOLIDADA', $lParagrafoOficial = true)
    {
        if ($lParagrafoOficial) {
            $oPdf->SetFont('Arial', '', 6);
            $oPdf->Cell(180, 4, 'RGF - ANEXO 2 (LRF, art. 55, inciso I, alínea "b")');
            $oPdf->Cell(10, 4, 'R$ 1,00', 0, 1);
        }

        $oPdf->SetFont('Arial', 'b', 6);
        $oPdf->Cell($this->iLarguraColunaDescricao, 4, '', 'RT', 0, 'C', 1);
        $oPdf->Cell($this->iLarguraColunaExercicioAnterior, 4, 'SALDO DO', 'TLR', 0, 'C', 1);
        $oPdf->Cell($this->iLarguraColunaSaldoExercicio, 4, "SALDO DO EXERCÍCIO DE {$this->iAno}", 'TBL', 1, 'C', 1);

        $oPdf->SetFont('Arial', 'ub', 6);
        $oPdf->Cell($this->iLarguraColunaDescricao, 4, $sTitulo, 'RB', 0, 'C', 1);
        $oPdf->SetFont('Arial', 'b', 6);
        $oPdf->Cell($this->iLarguraColunaExercicioAnterior, 4, 'EXERCÍCIO ANTERIOR', 'LRB', 0, 'C', 1);

        $sPeriodo = $this->iNumeroDePeriodos == 2 ? 'Semestre' : 'Quadrimestre';
        for ($i = 1; $i <= $this->iNumeroDePeriodos; ++$i) {
            $sBorda = '1';
            $iLn = 0;
            if ($i == $this->iNumeroDePeriodos) {
                $sBorda = 'LTB';
                $iLn = 1;
            }
            $oPdf->Cell($this->iLarguraColunaSaldoPeriodo, 4, "Até o {$i}º {$sPeriodo}", $sBorda, $iLn, 'C', 1);
        }
        $oPdf->SetFont('Arial', '', 6);
    }

    public function cabecalhoQuadro2(PDFDocument $oPdf)
    {
        $sTitulo = 'OUTROS VALORES NÃO INTEGRANTES DA DC';
        $oPdf->ln();
        $this->cabecalhoQuadro1($oPdf, $sTitulo, false);
    }

    /**
     * @throws Exception
     */
    public function notaExplicativaPdf(PDFDocument $oPdf)
    {
        $oPdf->line($oPdf->getX(), $oPdf->getY(), 200, $oPdf->getY());
        $oPdf->ln(1);
        $this->notaExplicativa($oPdf, [$oPdf, 'addPage'], 20);

        $oPdf->ln($oPdf->getAvailHeight() - 10);
        $oDaoAssinatura = new cl_assinatura();
        assinaturas($oPdf, $oDaoAssinatura, 'GF');
    }

    /**
     * @return Linha[]
     *
     * @throws Exception
     */
    public function getDadosProcessados()
    {
        $this->processar();
        $oLinha = new Linha();
        $oLinha->informaMetodo('cabecalhoQuadro1');
        $this->aLinhasProcessadas[] = $oLinha;

        foreach ($this->aLinhas as $oLinhaRelatorio) {
            if ($oLinhaRelatorio->ordem <= 30) {
                $this->adicionalinhasQuadro1($oLinhaRelatorio);
            }

            if ($oLinhaRelatorio->ordem == 31) {
                $oLinha = new Linha();
                $oLinha->informaMetodo('cabecalhoQuadro2');
                $this->aLinhasProcessadas[] = $oLinha;
            }
            if ($oLinhaRelatorio->ordem >= 31) {
                $this->adicionalinhasQuadro1($oLinhaRelatorio);
            }
        }
        $oLinha = new Linha();
        $oLinha->informaMetodo('notaExplicativaPdf');
        $this->aLinhasProcessadas[] = $oLinha;

        return $this->aLinhasProcessadas;
    }

    /**
     * Identifica a linha e redireciona para função de calculo. Linhas tratadas
     *   25 - DÍVIDA CONSOLIDADA LÍQUIDA² (DCL) (III) = (I - II)
     *   26 - RECEITA CORRENTE LÍQUIDA - RCL
     *   27 - % da DC sobre a RCL (I/RCL)
     *   28 - % da DCL sobre a RCL (III/RCL)
     *   29 - LIMITE DEFINIDO POR RESOLUÇÃO DO SENADO FEDERAL - <%>
     *   30 - LIMITE DE ALERTA (inciso III do § 1º do art. 59 da LRF) - <%>.
     *
     * @param stdClass $oLinhaRelatorio
     */
    private function calculaLinhasQuadro1($oLinhaRelatorio)
    {
        $sNivel = str_repeat(' ', $oLinhaRelatorio->nivel * 2);
        $sDescricao = "{$sNivel} {$oLinhaRelatorio->descricao}";

        $nSaldoExercicioAnterior = $oLinhaRelatorio->saldo_exercicio_anterior;
        $aSaldoPeriodo = $this->getSaldoDoExercicio($oLinhaRelatorio);

        switch ($oLinhaRelatorio->ordem) {
            case 25:
            case 28:
                $this->adicionaLinha($sDescricao, $nSaldoExercicioAnterior, $aSaldoPeriodo, 1, 2);
                break;
            case 26:
            case 27:
            case 29:
            case 30:
            case 41:
                $this->adicionaLinha($sDescricao, $nSaldoExercicioAnterior, $aSaldoPeriodo, 0, 2);
                break;
        }
    }

    /**
     * Retorna um array com os valores do saldo do exercício de acordo com o pereíodo informado.
     *
     * @param stdClass $oLinha
     *
     * @return array
     */
    private function getSaldoDoExercicio($oLinha)
    {
        $aSaldoPeriodo = [];

        if (in_array($this->oPeriodo->getCodigo(), [12, 13])) {
            $aSaldoPeriodo[] = $oLinha->primeiro_periodo;
            $aSaldoPeriodo[] = $oLinha->segundo_periodo;
        } else {
            $aSaldoPeriodo[] = $oLinha->primeiro_periodo;
            $aSaldoPeriodo[] = $oLinha->segundo_periodo;
            $aSaldoPeriodo[] = $oLinha->terceiro_periodo;
        }

        return $aSaldoPeriodo;
    }

    /**
     * @param $oLinhaRelatorio
     *
     * @return bool
     *
     * @throws Exception
     */
    private function adicionalinhasQuadro1($oLinhaRelatorio)
    {
        /* As verificações abaixo são necessárias pois no relatório caso a linha 21 'Disponibilidade de Caixa¹'
         *  possui a seguinte regra: quando ela for negativa o valor dela vai para a linha
         *  34 'INSUFICIÊNCIA FINANCEIRA [3]'
         *  como valor positivo e o valor da linha 21 fica zerado. Porem a linha 21 possui outras linhas que utilizam
         *  ela para efetuar outros calculos. Temos entao que recalcular as seguintes linhas:
         *      20 'DEDUÇÕES (II)'
         *      25 'DÍVIDA CONSOLIDADA LÍQUIDA² (DCL) (III) = (I - II)'
         *      28 '% da DCL sobre a RCL (III/RCL)'
         */
        if ($oLinhaRelatorio->ordem == 25) {
            $this->processarFormulaDaLinha(25);
        }

        if ($oLinhaRelatorio->ordem == 28) {
            //ja aplicamos a formula correta no processar, aqui não precisa passar novamente.
            // $this->processarFormulaDaLinha(28);
        }

        if ($oLinhaRelatorio->ordem == 20) {
            if ($this->aLinhas[21]->saldo_exercicio_anterior < 0) {
                $this->aLinhas[34]->saldo_exercicio_anterior += abs($this->aLinhas[21]->saldo_exercicio_anterior);
                $this->aLinhas[20]->saldo_exercicio_anterior += abs($this->aLinhas[21]->saldo_exercicio_anterior);
                $this->aLinhas[21]->saldo_exercicio_anterior = 0;
            }

            if ($this->aLinhas[21]->primeiro_periodo < 0) {
                $this->aLinhas[34]->primeiro_periodo += abs($this->aLinhas[21]->primeiro_periodo);
                $this->aLinhas[20]->primeiro_periodo += abs($this->aLinhas[21]->primeiro_periodo);
                $this->aLinhas[21]->primeiro_periodo = 0;
            }

            if ($this->aLinhas[21]->segundo_periodo < 0) {
                $this->aLinhas[34]->segundo_periodo += abs($this->aLinhas[21]->segundo_periodo);
                $this->aLinhas[20]->segundo_periodo += abs($this->aLinhas[21]->segundo_periodo);
                $this->aLinhas[21]->segundo_periodo = 0;
            }

            if ($this->aLinhas[21]->terceiro_periodo < 0) {
                $this->aLinhas[34]->terceiro_periodo += abs($this->aLinhas[21]->terceiro_periodo);
                $this->aLinhas[20]->terceiro_periodo += abs($this->aLinhas[21]->terceiro_periodo);
                $this->aLinhas[21]->terceiro_periodo = 0;
            }
        }

        if ($oLinhaRelatorio->ordem >= 25 && $oLinhaRelatorio->ordem <= 30 || $oLinhaRelatorio->ordem == 41) {
            $this->calculaLinhasQuadro1($oLinhaRelatorio);

            return true;
        }
        $sNivel = str_repeat(' ', $oLinhaRelatorio->nivel * 2);

        $sDescricao = "{$sNivel} {$oLinhaRelatorio->descricao}";
        $nSaldoExercicioAnterior = $oLinhaRelatorio->saldo_exercicio_anterior;
        $aSaldoPeriodo = $this->getSaldoDoExercicio($oLinhaRelatorio);

        $this->adicionaLinha($sDescricao, $nSaldoExercicioAnterior, $aSaldoPeriodo, 0, 1);

        return true;
    }

    /**
     * [adicionaLinha description].
     *
     * @param [type] $sDescricao              [description]
     * @param float $nSaldoExercicioAnterior [description]
     * @param array $aSaldoPeriodo [description]
     * @param int $iFill [description]
     * @param int $iTipoBorda [description]
     */
    private function adicionaLinha(
        $sDescricao,
        $nSaldoExercicioAnterior,
        $aSaldoPeriodo,
        $iFill = 0,
        $iTipoBorda = 1
    ) {
        if (empty($nSaldoExercicioAnterior)) {
            $nSaldoExercicioAnterior = null;
        }
        $nSaldoExercicioAnterior = db_formatar($nSaldoExercicioAnterior, 'f');

        $oLinha = new Linha();
        $oLinha->addColuna(
            $this->iLarguraColunaDescricao,
            "{$sDescricao}",
            $this->aTipoBorda[$iTipoBorda][0],
            0,
            'L',
            $iFill
        );
        $oLinha->multicell(true);
        $oLinha->addColuna(
            $this->iLarguraColunaExercicioAnterior,
            "{$nSaldoExercicioAnterior}",
            $this->aTipoBorda[$iTipoBorda][1],
            0,
            'R',
            $iFill
        );

        foreach ($aSaldoPeriodo as $i => $nValor) {
            $sBorda = $this->aTipoBorda[$iTipoBorda][1];
            $iLn = 0;
            if ($i + 1 == $this->iNumeroDePeriodos) {
                $sBorda = $this->aTipoBorda[$iTipoBorda][2];
                $iLn = 1;
            }

            $nValor = db_formatar($nValor, 'f');
            $oLinha->addColuna($this->iLarguraColunaSaldoPeriodo, "$nValor", $sBorda, $iLn, 'R', $iFill);
        }
        $this->aLinhasProcessadas[] = $oLinha;
    }

    /**
     * @return stdClass
     *
     * @throws Exception
     */
    public function getDadosSimplificado()
    {
        /*
         * Carrega as informações que usaremos abaixo
         */
        $this->processar();

        $oStdDivida = new stdClass();
        $oStdDivida->nTotalDividaII = 0;
        $oStdDivida->nPercentualRCL = 0;
        $oStdDivida->nLimiteSenadoAnexoII = 0;

        switch ($this->oPeriodo->getCodigo()) {
            case Periodo::PRIMEIRO_SEMESTRE:
            case Periodo::PRIMEIRO_QUADRIMESTRE:
                $oStdDivida->nTotalDividaII = $this->aLinhasConsistencia[25]->primeiro_periodo;
                $oStdDivida->nPercentualRCL = $this->aLinhasConsistencia[28]->primeiro_periodo;
                $oStdDivida->nLimiteSenadoAnexoII = $this->aLinhasConsistencia[29]->primeiro_periodo;
                break;
            case Periodo::SEGUNDO_SEMESTRE:
            case Periodo::SEGUNDO_QUADRIMESTRE:
                $oStdDivida->nTotalDividaII = $this->aLinhasConsistencia[25]->segundo_periodo;
                $oStdDivida->nPercentualRCL = $this->aLinhasConsistencia[28]->segundo_periodo;
                $oStdDivida->nLimiteSenadoAnexoII = $this->aLinhasConsistencia[29]->segundo_periodo;
                break;
            case Periodo::TERCEIRO_QUADRIMESTRE:
                $oStdDivida->nTotalDividaII = $this->aLinhasConsistencia[25]->terceiro_periodo;
                $oStdDivida->nPercentualRCL = $this->aLinhasConsistencia[28]->terceiro_periodo;
                $oStdDivida->nLimiteSenadoAnexoII = $this->aLinhasConsistencia[29]->terceiro_periodo;
                break;
        }

        return $oStdDivida;
    }

    public function getLinhas()
    {
        return $this->aLinhas;
    }

    private function processaRcl()
    {

        if ($this->iAno <= 2020) {
            $this->processaRClAntiga();
        } else {
            $this->processaRClNova();
        }
    }

    private function processaRClAntiga()
    {
        $anoAnterior = ($this->iAno);

        $rcl = ReceitaCorrenteFactory::getInstance($anoAnterior, Periodo::DEZEMBRO);
        $instituicoes = \InstituicaoRepository::getInstituicoes();
        $codigoInstituicoes = implode(',', array_keys($instituicoes));
        $rcl->setInstituicoes($codigoInstituicoes);
        $stdDadosRCL = $rcl->getDadosSimplificado();
        $this->aLinhasConsistencia[26]->saldo_exercicio_anterior = $stdDadosRCL->valor_rcl_mdf;

        foreach ($this->aPeriodoCalcular[$this->oPeriodo->getCodigo()] as $iPeriodo) {
            $iColuna = $this->aColunaRecalcularPeriodo[$iPeriodo];
            /**
             * Calcula a RCL para o período.
             */
            $rcl = ReceitaCorrenteFactory::getInstance($this->iAno, $iPeriodo);

            $instituicoes = \InstituicaoRepository::getInstituicoes();
            $codigoInstituicoes = implode(',', array_keys($instituicoes));
            $rcl->setInstituicoes($codigoInstituicoes);
            $stdDadosRCL = $rcl->getDadosSimplificado();
            $nValorRCL = $stdDadosRCL->valor_rcl_mdf;
            $valorRCLEmendasIndividuais = $stdDadosRCL->valor_rcl_transferencia_individual;

            switch ($iColuna) {
                case 1:
                    $this->aLinhasConsistencia[26]->primeiro_periodo = $nValorRCL;
                    $this->aLinhasConsistencia['26.1']->primeiro_periodo = $valorRCLEmendasIndividuais;
                    break;
                case 2:
                    $this->aLinhasConsistencia[26]->segundo_periodo = $nValorRCL;
                    $this->aLinhasConsistencia['26.1']->segundo_periodo = $valorRCLEmendasIndividuais;
                    break;
                case 3:
                    $this->aLinhasConsistencia[26]->terceiro_periodo = $nValorRCL;
                    $this->aLinhasConsistencia['26.1']->terceiro_periodo = $valorRCLEmendasIndividuais;
                    break;
            }
        }

        $this->processaValorManualPorLinhaEColuna('26.1', 1);
        $this->processaValorManualPorLinhaEColuna('26.1', 2);
    }

    /**
     * @return void
     * @throws Exception
     */
    private function processaRClNova()
    {
        $exercicioAnterior = $this->getAno() - 1;
        $simplificado = $this->getDadosSimplificadoNovoRCL(Periodo::SEGUNDO_SEMESTRE, $exercicioAnterior);
        $this->aLinhasConsistencia[26]->saldo_exercicio_anterior = $simplificado[0]->ate_bimestre;

        foreach ($this->aPeriodoCalcular[$this->oPeriodo->getCodigo()] as $codigoPeriodo) {
            $coluna = $this->aColunaRecalcularPeriodo[$codigoPeriodo];
            $serviceRCL = $this->getServiceNovoRCL($codigoPeriodo, $this->getAno());
            $simplificado = $serviceRCL->processaLinhasSimplificado();
            $valorRCL = $simplificado[0]->ate_bimestre;
            $linhaEmendaIndividuais = $serviceRCL->getLinhaEmendaIndividuais();
            $valorRCLEmendasIndividuais = $linhaEmendaIndividuais->total_meses;

            switch ($coluna) {
                case 1:
                    $this->aLinhasConsistencia[26]->primeiro_periodo = $valorRCL;
                    $this->aLinhasConsistencia['26.1']->primeiro_periodo = $valorRCLEmendasIndividuais;
                    break;
                case 2:
                    $this->aLinhasConsistencia[26]->segundo_periodo = $valorRCL;
                    $this->aLinhasConsistencia['26.1']->segundo_periodo = $valorRCLEmendasIndividuais;
                    break;
                case 3:
                    $this->aLinhasConsistencia[26]->terceiro_periodo = $valorRCL;
                    $this->aLinhasConsistencia['26.1']->terceiro_periodo = $valorRCLEmendasIndividuais;
                    break;
            }
        }
    }

    /**
     * @param $codigoPeriodo
     * @return array
     * @throws Exception
     */
    private function getDadosSimplificadoNovoRCL($codigoPeriodo, $exercicio)
    {
        $service = $this->getServiceNovoRCL($codigoPeriodo, $exercicio);

        return $service->processaLinhasSimplificado();
    }

    /**
     * @param $codigoPeriodo
     * @return AnexoTresService
     * @throws Exception
     */
    private function getServiceNovoRCL($codigoPeriodo, $exercicio)
    {
        $filtros = [
            'codigo_relatorio' => AnexoTresFactory::getCodigoRelatorio($this->getAno()),
            'periodo' => AnexoTresFactory::transformPeriodo($codigoPeriodo),
            'DB_anousu' => $exercicio,
            'DB_instit' => db_getsession('DB_instit')
        ];

        return AnexoTresFactory::getService($this->getAno(), $filtros);
    }
}
