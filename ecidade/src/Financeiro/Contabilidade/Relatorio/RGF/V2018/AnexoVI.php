<?php

namespace ECidade\Financeiro\Contabilidade\Relatorio\RGF\V2018;

use App\Domain\Financeiro\Contabilidade\Factories\AnexoCincoRgfFactory;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoDoisRgfFactory;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoTresFactory;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoUmRgfFactory;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTresInRsService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTresMdfService;
use cl_assinatura;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\Factory\AnexoI as AnexoIFactory;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\Factory\AnexoIII as AnexoIIIFactory;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\Factory\AnexoIV as AnexoIVFactory;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\Factory\AnexoV as AnexoVFactory;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\Linha;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\V2018\AnexoI as AnexoI;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoIII as ReceitaCorrenteFactory;
use InstituicaoRepository;
use PDFDocument;
use relatorioContabil;
use stdClass;
use function ECidade\Financeiro\Contabilidade\Relatorio\RGF\Factory\AnexoV;

class AnexoVI extends AnexoAbstract
{
    const CODIGO_RELATORIO = 93;
    private $exibirDemonstrativoReceitaCorrenteLiquida;
    private $exibirDemonstrativoDespesaPessoal;
    private $exibirDemonstrativoDividaConsolidadaLiquida;
    private $exibirDemonstrativoGarantiasContraGarantias;
    private $exibirDemonstrativoOperacoesCredito;
    private $exibirDemonstrativoRestosPagar;

    protected $linhas = array();

    private $valorReceitaCorrenteLiquida;
    private $valorReceitaCorrenteLiquidaAjustada;
    private $valorReceitaCorrenteLiquidaEndividadamento;
    protected $simplificadoAnexoI;
    protected $simplificadoAnexoII;
    protected $simplificadoAnexoIII;
    protected $simplificadoAnexoIV;
    protected $simplificadoAnexoV;

    protected $dadosRcl = null;


    public function processar()
    {
        $linha = new Linha();
        $linha->addColuna(170, "LRF, art. 48 - Anexo 6", 0, 0, 'L', 0);
        $linha->addColuna(20, "R$ 1,00", 0, 1, 'R', 0);
        $this->linhas[] = $linha;

        if ($this->exibirDemonstrativoReceitaCorrenteLiquida) {
            $this->processaDemonstrativoReceitaCorrenteLiquida();
        }
        if ($this->exibirDemonstrativoDespesaPessoal) {
            $this->processaDemonstrativoDespesaPessoal();
        }
        if ($this->exibirDemonstrativoDividaConsolidadaLiquida) {
            $this->processaDemonstrativoDividaConsolidadaLiquida();
        }
        if ($this->exibirDemonstrativoGarantiasContraGarantias) {
            $this->processaDemonstrativoGarantiasContraGarantias();
        }
        if ($this->exibirDemonstrativoOperacoesCredito) {
            $this->processaDemonstrativoOperacoesCredito();
        }
        if ($this->exibirDemonstrativoRestosPagar) {
            $this->processaDemonstrativoRestosPagar();
        }

        $oLinha = new Linha();
        $oLinha->informaMetodo("notaExplicativaPdf");
        $this->linhas[] = $oLinha;
    }

    public function getDadosProcessados()
    {
        if (empty($this->linhas)) {
            $this->processar();
        }
        return $this->linhas;
    }

    public function definirParametros(stdClass $parametros)
    {
        $this->exibirDemonstrativoReceitaCorrenteLiquida = $parametros->rcl === 'true';
        $this->exibirDemonstrativoDespesaPessoal = $parametros->pessoal === 'true';
        $this->exibirDemonstrativoDividaConsolidadaLiquida = $parametros->divida === 'true';
        $this->exibirDemonstrativoGarantiasContraGarantias = $parametros->garantias === 'true';
        $this->exibirDemonstrativoOperacoesCredito = $parametros->operacoes === 'true';
        $this->exibirDemonstrativoRestosPagar = $parametros->restosapagar === 'true';
    }

    /**
     * Receita Corrente Liquida
     */
    private function processaDemonstrativoReceitaCorrenteLiquida()
    {
        $linha = new Linha();
        $linha->informaMetodo("cabecalhoDemonstrativoReceitaCorrenteLiquida");
        $this->linhas[] = $linha;

        $valorReceitaCorrenteLiquida = $this->getValorReceitaCorrenteLiquida();
        $valorReceitaCorrenteLiquidaAjustada = $this->getValorReceitaCorrenteLiquidaAjustada();

        $linha = new Linha();
        $linha->addColuna(105, "Receita Corrente líquida", "TBR", 0, 'L', 0);
        $linha->addColuna(85, $this->formatarValor($valorReceitaCorrenteLiquida), "TBL", 1, 'R', 0);
        $this->linhas[] = $linha;

        $textoRclAjustada = "Receita Corrente líquida Ajustada";
        if ($this->getAno() >= 2020) {
            $linha = new Linha();
            $valor = $this->formatarValor($this->getValorReceitaCorrenteLiquidaEndividamento());
            $texto = "Receita Corrente Líquida Ajustada para cálculo dos Limites de Endividamento";
            $linha->addColuna(105, $texto, "TBR", 0, 'L', 0);
            $linha->addColuna(85, $valor, "TBL", 1, 'R', 0);
            $this->linhas[] = $linha;
            $textoRclAjustada = "Receita Corrente Líquida Ajustada para cálculo dos Limites da Despesa com Pessoal";
        }
        $linha = new Linha();
        $linha->addColuna(105, $textoRclAjustada, "TBR", 0, 'L', 0);
        $linha->addColuna(
            85,
            $this->formatarValor($valorReceitaCorrenteLiquidaAjustada),
            "TBL",
            1,
            'R',
            0
        );
        $this->linhas[] = $linha;
    }

    /**
     * Demonstrativo Despesa com Pessoal
     */
    private function processaDemonstrativoDespesaPessoal()
    {
        $linha = new Linha();
        $linha->informaMetodo("cabecalhoDemonstrativoDespesaPessoal");
        $this->linhas[] = $linha;

        $dadosSimplificado = $this->simplificadoAnexoI();

        $linha = new Linha();
        $linha->addColuna(105, "Despesa Total com Pessoal - DTP", "TR", 0, 'L', 0);
        $linha->addColuna(40, $this->formatarValor($dadosSimplificado->total_despesa_pessoal), "TRL", 0, 'R', 0);
        $linha->addColuna(45, $this->formatarValor($dadosSimplificado->percentual_despesa_pessoal), "TL", 1, 'R', 0);
        $this->linhas[] = $linha;

        $linha = new Linha();

        $sPercentualMax = $this->formatarValor($dadosSimplificado->percentual_limite_maximo);
        $linha->addColuna(105, "Limite Máximo (incisos I, II e III, art. 20 da LRF) - $sPercentualMax", "R", 0, 'L', 0);
        $linha->addColuna(40, $this->formatarValor($dadosSimplificado->total_limite_maximo), 'LR', 0, 'R', 0);
        $linha->addColuna(45, $sPercentualMax, "L", 1, 'R', 0);
        $this->linhas[] = $linha;

        $linha = new Linha();
        $sPercPrudencial = $this->formatarValor($dadosSimplificado->percentual_limite_prudencial);
        $linha->addColuna(
            105,
            "Limite Prudencial (parágrafo único, art. 22 da LRF) - $sPercPrudencial",
            "R",
            0,
            'L',
            0
        );
        $linha->addColuna(40, $this->formatarValor($dadosSimplificado->total_limite_prudencial), 'LR', 0, 'R', 0);
        $linha->addColuna(45, $sPercPrudencial, "L", 1, 'R', 0);
        $this->linhas[] = $linha;

        $linha = new Linha();
        $sPercAlerta = $this->formatarValor($dadosSimplificado->percentual_limite_alerta);
        $linha->addColuna(105, "Limite de Alerta (inciso II do §1o do art. 59 da LRF) - $sPercAlerta", "BR", 0, 'L', 0);
        $linha->addColuna(40, $this->formatarValor($dadosSimplificado->total_limite_alerta), 'LRB', 0, 'R', 0);
        $linha->addColuna(45, $sPercAlerta, "BL", 1, 'R', 0);
        $this->linhas[] = $linha;
    }

    /**
     * Divida Consolidada Líquida
     */
    private function processaDemonstrativoDividaConsolidadaLiquida()
    {
        $linha = new Linha();
        $linha->informaMetodo("cabecalhoDemonstrativoDividaConsolidadaLiquida");
        $this->linhas[] = $linha;

        if ($this->ano >= 2022) {
            $this->simplificadoAnexoIINovo();
        } else {
            $dadosSimplificado = $this->simplificadoAnexoII();

            $percentualDividaRCL = ($dadosSimplificado->nTotalDividaII / $this->getValorReceitaCorrenteLiquida()) * 100;
            $percentualLimiteSenadoRCL = (
                    $dadosSimplificado->nLimiteSenadoAnexoII / $this->getValorReceitaCorrenteLiquida()
                ) * 100;

            $linha = new Linha();
            $linha->addColuna(105, "Dívida Consolidada Líquida", "TR", 0, 'L', 0);
            $linha->addColuna(40, $this->formatarValor($dadosSimplificado->nTotalDividaII), 'TLR', 0, 'R', 0);
            $linha->addColuna(45, $this->formatarValor($percentualDividaRCL), "TL", 1, 'R', 0);
            $this->linhas[] = $linha;

            $linha = new Linha();
            $linha->addColuna(105, "Limite Definido por Resolução do Senado Federal", "BR", 0, 'L', 0);
            $linha->addColuna(40, $this->formatarValor($dadosSimplificado->nLimiteSenadoAnexoII), 'LRB', 0, 'R', 0);
            $linha->addColuna(45, $this->formatarValor($percentualLimiteSenadoRCL), "BL", 1, 'R', 0);
            $this->linhas[] = $linha;
        }
    }

    /**
     * Garantias e Contra-Garantias
     * Garantia de Valores
     */
    private function processaDemonstrativoGarantiasContraGarantias()
    {
        $linha = new Linha();
        $linha->informaMetodo("cabecalhoDemonstrativoGarantiasContraGarantias");
        $this->linhas[] = $linha;

        $dadosSimplificado = $this->simplificadoAnexoIII();

        $percentualGarantiaConcedida = (
                $dadosSimplificado->total_garantia_concedida / $this->getValorReceitaCorrenteLiquida()
            ) * 100;
        $percentualLimiteSenado = (
                $dadosSimplificado->limite_definido_resolucao_senado / $this->getValorReceitaCorrenteLiquida()
            ) * 100;

        $linha = new Linha();
        $linha->addColuna(105, "Total das Garantias Concedidas", "TR", 0, 'L', 0);
        $linha->addColuna(40, $this->formatarValor($dadosSimplificado->total_garantia_concedida), 'TLR', 0, 'R', 0);
        $linha->addColuna(45, $this->formatarValor($percentualGarantiaConcedida), "TL", 1, 'R', 0);
        $this->linhas[] = $linha;

        $linha = new Linha();
        $linha->addColuna(105, "Limite Definido por Resolução do Senado Federal", "BR", 0, 'L', 0);
        $valor = $this->formatarValor($dadosSimplificado->limite_definido_resolucao_senado);
        $linha->addColuna(40, $valor, 'LRB', 0, 'R', 0);
        $linha->addColuna(45, $this->formatarValor($percentualLimiteSenado), "BL", 1, 'R', 0);
        $this->linhas[] = $linha;
    }

    /**
     * Operações de Credito
     */
    private function processaDemonstrativoOperacoesCredito()
    {
        $linha = new Linha();
        $linha->informaMetodo("cabecalhoDemonstrativoOperacoesCredito");
        $this->linhas[] = $linha;

        $dadosSimplificado = $this->simplificadoAnexoIV();

        $linha = new Linha();
        $linha->addColuna(105, "Operações de Crédito Internas e Externas", "TR", 0, 'L', 0);
        $linha->addColuna(40, $this->formatarValor($dadosSimplificado->total_operacoes_credito), 'TLR', 0, 'R', 0);
        $linha->addColuna(45, $this->formatarValor($dadosSimplificado->perc_operacoes_credito), "TL", 1, 'R', 0);
        $this->linhas[] = $linha;

        $linha = new Linha();
        $descricao = "Limite Definido pelo Senado Federal para Operações de Crédito Externas e Internas";
        $linha->addColuna(105, $descricao, "R", 0, 'L', 0);
        $valor = $this->formatarValor($dadosSimplificado->total_credito_interna_externa);
        $linha->addColuna(40, $valor, 'LR', 0, 'R', 0);
        $valor = $this->formatarValor($dadosSimplificado->perc_credito_interna_externa);
        $linha->addColuna(45, $valor, "L", 1, 'R', 0);
        $this->linhas[] = $linha;

        $linha = new Linha();
        $linha->addColuna(105, "Operações de Crédito por Antecipação da Receita", "R", 0, 'L', 0);

        $valor1 = $this->formatarValor($dadosSimplificado->total_antecipacao_receita_orcamentaria);
        $linha->addColuna(40, $valor1, 'LR', 0, 'R', 0);
        $valor2 = $this->formatarValor($dadosSimplificado->perc_antecipacao_receita_orcamentaria);
        $linha->addColuna(45, $valor2, "L", 1, 'R', 0);
        $this->linhas[] = $linha;

        $linha = new Linha();
        $descricao = "Limite Definido pelo Senado Federal para Operações de Crédito por Antecipação da Receita";
        $linha->addColuna(105, $descricao, "BR", 0, 'L', 0);
        $valor = $this->formatarValor($dadosSimplificado->total_credito_interna_receita_orcamentaria);
        $linha->addColuna(40, $valor, "BLR", 0, 'R', 0);
        $valor = $this->formatarValor($dadosSimplificado->perc_credito_interna_receita_orcamentaria);
        $linha->addColuna(45, $valor, "BL", 1, 'R', 0);
        $this->linhas[] = $linha;
    }

    /**
     * Restos a Pagar
     */
    private function processaDemonstrativoRestosPagar()
    {
        $linha = new Linha();
        $linha->informaMetodo("cabecalhoDemonstrativoRestosPagar");
        $this->linhas[] = $linha;

        $dadosSimplificado = $this->simplificadoAnexoV();

        $linha = new Linha();
        $linha->addColuna(80, "Valor Total", "TBR", 0, 'L', 0);
        $valor = $this->formatarValor($dadosSimplificado->rp_nao_processado);
        $linha->addColuna(50, $valor, 1, 0, 'R', 0);
        $valor = $this->formatarValor($dadosSimplificado->disponibilidade_caixa_liquida);
        $linha->addColuna(60, $valor, "TBL", 1, 'R', 0);
        $this->linhas[] = $linha;
    }

    public function cabecalhoDemonstrativoReceitaCorrenteLiquida(PDFDocument $pdf)
    {
        $pdf->SetFont("Arial", "B", 7);
        $pdf->Cell(105, 4, "RECEITA CORRENTE LÍQUIDA", "TBR", 0, 'C', 1);
        $pdf->Cell(85, 4, "VALOR ATÉ O QUADRIMESTRE/SEMESTRE", "TBL", 1, 'C', 1);
        $pdf->SetFont("Arial", "", 7);
    }

    public function cabecalhoDemonstrativoDespesaPessoal(PDFDocument $pdf)
    {
        $pdf->ln();
        $this->cabecalhoPadraoTresColunas($pdf, "DESPESA COM PESSOAL", 'VALOR', '% SOBRE A RCL AJUSTADA');
    }

    public function cabecalhoDemonstrativoDividaConsolidadaLiquida(PDFDocument $pdf)
    {
        $pdf->ln();
        $this->cabecalhoPadraoTresColunas($pdf, "DÍVIDA CONSOLIDADA");
    }

    public function cabecalhoDemonstrativoGarantiasContraGarantias(PDFDocument $pdf)
    {
        $pdf->ln();
        $this->cabecalhoPadraoTresColunas($pdf, "GARANTIAS DE VALORES");
    }

    public function cabecalhoDemonstrativoOperacoesCredito(PDFDocument $pdf)
    {
        $pdf->ln();
        $this->cabecalhoPadraoTresColunas($pdf, "OPERAÇÕES DE CRÉDITO");
    }

    private function cabecalhoPadraoTresColunas(
        $pdf,
        $labelPrimeira,
        $labelSegunda = 'VALOR',
        $labelTerceira = '% SOBRE A RCL'
    ) {
        $pdf->SetFont("Arial", "B", 7);
        $pdf->Cell(105, 4, "{$labelPrimeira}", "TBR", 0, 'C', 1);
        $pdf->Cell(40, 4, "{$labelSegunda} ", 1, 0, 'C', 1);
        $pdf->Cell(45, 4, "{$labelTerceira}", "TBL", 1, 'C', 1);
        $pdf->SetFont("Arial", "", 7);
    }

    public function cabecalhoDemonstrativoRestosPagar(PDFDocument $pdf)
    {
        $pdf->ln();
        $pdf->SetFont("Arial", "B", 7);

        $yAntes = $pdf->GetY();
        $pdf->Rect(10, $yAntes, 190, 12, 'F');

        $pdf->Cell(80, 12, "RESTO S A PAGAR", 0, 0, 'C', 0);
        $pdf->MultiCell(50, 4, "RESTOS A PAGAR EMPENHADOS E NÃO LIQUIDADOS DO EXERCÍCIO", 0, 'C', 0);
        $pdf->SetXY(140, $yAntes);
        $d = "DISPONIBILIDADE DE CAIXA LÍQUIDA (APÓS A INSCRIÇÃO EM RESTOS A PAGAR NÃO PROCESSADOS DO EXERCÍCIO)";
        $pdf->MultiCell(60, 4, $d, 0, 'C', 0);

        $pdf->SetFont("Arial", "", 7);
        $pdf->Line(10, $yAntes, 200, $yAntes);
        $pdf->Line(10, $yAntes + 12, 200, $yAntes + 12);
        $pdf->Line(90, $yAntes, 90, $yAntes + 12);
        $pdf->Line(140, $yAntes, 140, $yAntes + 12);
    }

    /**
     * @paran \PDFDocument $oPdf
     */
    public function notaExplicativaPdf(PDFDocument $oPdf)
    {
        $oPdf->line($oPdf->getX(), $oPdf->getY(), 200, $oPdf->getY());
        $oPdf->ln(1);
        $this->notaExplicativa($oPdf);

        $oPdf->ln($oPdf->getAvailHeight() - 10);
        $oDaoAssinatura = new cl_assinatura();
        assinaturas($oPdf, $oDaoAssinatura, 'GF');
    }

    protected function notaExplicativa(PDFDocument $oPdf)
    {
        $relatorioContabil = new relatorioContabil(self::CODIGO_RELATORIO, false);
        $iAltura = $relatorioContabil->notaExplicativa(
            $oPdf,
            $this->getPeriodo()->getCodigo(),
            $oPdf->getAvailWidth(),
            false
        );
        if ($oPdf->getAvailHeight() < ($iAltura + 10)) {
            $oPdf->AddPage();
        }
        $relatorioContabil->notaExplicativa($oPdf, $this->getPeriodo()->getCodigo(), $oPdf->getAvailWidth());
    }


    protected function simplificadoAnexoI()
    {
        if (empty($this->simplificadoAnexoI)) {
            if ($this->ano >= 2021) {
                $this->simplificadoAnexoI = $this->processaDadosSimplificadoAnexoUmApos2021();
            } else {
                $this->simplificadoAnexoI = $this->processaDadosSimplificadoAnexoUmAntes2021();
            }
        }

        return $this->simplificadoAnexoI;
    }

    private function processaDadosSimplificadoAnexoUmAntes2021()
    {
        $anexo = AnexoIFactory::getInstance(
            $this->ano,
            $this->periodo,
            $this->instituicoes,
            AnexoI::MODELO_DETALHAMENTO_MENSAL
        );
        return $anexo->getDadosSimplificado();
    }

    private function processaDadosSimplificadoAnexoUmApos2021()
    {
        $instituicoes = [];
        foreach ($this->instituicoes as $instituicao) {
            $instituicoes[] = $instituicao->getCodigo();
        }

        $filtros = [
            'codigo_relatorio' => AnexoUmRgfFactory::getCodigoRelatorio($this->ano),
            'periodo' => $this->periodo->getCodigo(),
            'DB_anousu' => $this->ano,
            'instituicoes' => $instituicoes,
            'DB_instit' => db_getsession('DB_instit')
        ];

        $service = AnexoUmRgfFactory::getService($this->ano, $filtros);

        return $service->processaLinhasSimplificado();
    }

    protected function simplificadoAnexoII()
    {


        if (empty($this->simplificadoAnexoII)) {
            $anexo = new AnexoII($this->ano, $this->periodo);
            $this->simplificadoAnexoII = $anexo->getDadosSimplificado();
        }

        return $this->simplificadoAnexoII;
    }

    /**
     * Emissão do Anexo III
     * @return stdClass
     */
    protected function simplificadoAnexoIII()
    {
        if (empty($this->simplificadoAnexoIII)) {
            $anexo = AnexoIIIFactory::getInstance($this->ano, $this->periodo);
            $this->simplificadoAnexoIII = $anexo->getDadosSimplificado();
        }
        return $this->simplificadoAnexoIII;
    }

    /**
     * Emissão do Anexo IV
     * @return stdClass
     * @throws \BusinessException
     */
    protected function simplificadoAnexoIV()
    {
        if (empty($this->simplificadoAnexoIV)) {
            $anexo = AnexoIVFactory::getInstance($this->ano, $this->periodo);
            $this->simplificadoAnexoIV = $anexo->getDadosSimplificado();
        }
        return $this->simplificadoAnexoIV;
    }

    protected function simplificadoAnexoV()
    {
        if ($this->ano < 2022) {
            if (empty($this->simplificadoAnexoV)) {
                $instituicoes = array_map(function ($instituicao) {
                    return $instituicao->getCodigo();
                }, $this->instituicoes);

                $anexo = AnexoVFactory::getInstance($this->ano, $this->periodo);
                $anexo->setInstituicoes(implode(',', $instituicoes));
                $this->simplificadoAnexoV = $anexo->getDadosSimplificado();
            }
        } else {
            $this->simplificadoAnexoV = $this->simplificadoAnexoVApos2021();
        }


        return $this->simplificadoAnexoV;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getValorReceitaCorrenteLiquida()
    {
        $dadosSimplificado = $this->getRcl();

        if ($this->getAno() == 2020) {
            $this->valorReceitaCorrenteLiquida = $dadosSimplificado->valor_rcl_mdf;
        } elseif ($this->getAno() >= 2021) {
            $this->valorReceitaCorrenteLiquida = $dadosSimplificado[0]->ate_bimestre;
        } else {
            $this->valorReceitaCorrenteLiquida = $dadosSimplificado->receita_corrente_liquida;
        }

        return $this->valorReceitaCorrenteLiquida;
    }

    /**
     * Valor da RCL Ajustada com folha
     * @return mixed
     */
    private function getValorReceitaCorrenteLiquidaAjustada()
    {
        $dadosSimplificado = $this->getRcl();
        if ($this->getAno() == 2020) {
            $this->valorReceitaCorrenteLiquidaAjustada = $dadosSimplificado->valor_rcl_pessoal;
        } elseif ($this->getAno() >= 2021) {
            $this->valorReceitaCorrenteLiquidaAjustada = $dadosSimplificado[2]->ate_bimestre;
        } else {
            $this->valorReceitaCorrenteLiquidaAjustada = $dadosSimplificado->receita_corrente_liquida_ajustada;
        }

        return $this->valorReceitaCorrenteLiquidaAjustada;
    }

    /**
     * Retorna o valor da RCl com endividademento.
     * @return mixed
     * @throws \Exception
     */
    private function getValorReceitaCorrenteLiquidaEndividamento()
    {
        $dadosSimplificado = $this->getRcl();
        if ($this->getAno() < 2021) {
            $this->valorReceitaCorrenteLiquidaEndividadamento = $dadosSimplificado->valor_rcl_endividamento;
        } elseif ($this->getAno() >= 2021) {
            $this->valorReceitaCorrenteLiquidaEndividadamento = $dadosSimplificado[1]->ate_bimestre;
        }
        return $this->valorReceitaCorrenteLiquidaEndividadamento;
    }

    /**
     * Formata o valor em moeda
     * @param $valor
     * @return string
     */
    private function formatarValor($valor)
    {
        $valor = number_format($valor, 2, ',', '.');
        return $valor;
    }

    /**
     * Retorna a RCl calculada corretamente
     * @return object|stdClass
     * @throws \Exception
     */
    public function getRcl()
    {

        if ($this->getAno() == 2020) {
            if (empty($this->dadosRcl)) {
                $dadosRcl = ReceitaCorrenteFactory::getInstance($this->getAno(), $this->getPeriodo()->getCodigo());
                $instituicoes = InstituicaoRepository::getInstituicoes();
                $codigoInstituicoes = implode(',', array_keys($instituicoes));
                $dadosRcl->setInstituicoes($codigoInstituicoes);
                $this->dadosRcl = $dadosRcl->getDadosSimplificado();
            }
        } elseif ($this->getAno() >= 2021) {
            $filtros = [
                'codigo_relatorio' => AnexoTresFactory::getCodigoRelatorio($this->getAno()),
                'periodo' => AnexoTresFactory::transformPeriodo($this->periodo->getCodigo()),
                'DB_anousu' => $this->getAno(),
                'DB_instit' => db_getsession('DB_instit')
            ];

            $service = AnexoTresFactory::getService($this->getAno(), $filtros);

            $this->dadosRcl = $service->processaLinhasSimplificado();
        } else {
            $this->dadosRcl = $this->simplificadoAnexoI();
        }

        return $this->dadosRcl;
    }

    /**
     * @return array
     * @throws \Exception
     * Retorna linhas com Layout para Gerar o Sigap Fiscal
     */
    public function getLinhasSigapFiscal()
    {
        $linhas = [];
        $linhas[1] = [
            'descricao' => 'Valor Receita Corrente Liquida',
            'valor' => $this->getValorReceitaCorrenteLiquida(),
        ];
        $linhas[2] = [
            'descricao' => 'Valor Receita Corrente Liquida Endividamento',
            'valor' => $this->getValorReceitaCorrenteLiquidaEndividamento(),
        ];
        $linhas[3] = [
            'descricao' => 'Valor Receita Corrente Liquida Ajustada',
            'valor' => $this->getValorReceitaCorrenteLiquidaAjustada(),
        ];
        $linhas[4] = [
            'descricao' => 'Despesa Total com Pessoal - DTP',
            'valor' => $this->simplificadoAnexoI->total_despesa_pessoal,
            'percentual' => $this->simplificadoAnexoI->percentual_despesa_pessoal,
        ];
        $linhas[5] = [
            'descricao' => 'Limite Maximo',
            'valor' => $this->simplificadoAnexoI->total_limite_maximo,
            'percentual' => $this->simplificadoAnexoI->percentual_limite_maximo,
        ];
        $linhas[6] = [
            'descricao' => 'Limite Preduncial',
            'valor' => $this->simplificadoAnexoI->total_limite_prudencial,
            'percentual' => $this->simplificadoAnexoI->percentual_limite_prudencial,
        ];
        $linhas[7] = [
            'descricao' => 'Limite de Alerta',
            'valor' => $this->simplificadoAnexoI->total_limite_alerta,
            'percentual' => $this->simplificadoAnexoI->percentual_limite_alerta,
        ];
        $linhas[8] = [
            'descricao' => 'Divida consolidada Liquida',
            'valor' => $this->simplificadoAnexoII->nTotalDividaII,
            'percentual' => $this->simplificadoAnexoII->nPercentualRCL,
        ];
        $linhas[9] = [
            'descricao' => 'Limite Senado Federal',
            'valor' => $this->simplificadoAnexoII->nLimiteSenadoAnexoII,
            'percentual' => (
                    $this->simplificadoAnexoII->nLimiteSenadoAnexoII / $this->getValorReceitaCorrenteLiquida()
                ) * 100,
        ];
        $linhas[10] = [
            'descricao' => 'Total de Garantias Concedidas',
            'valor' => $this->simplificadoAnexoIII->total_garantia_concedida,
            'percentual' => (
                    $this->simplificadoAnexoIII->total_garantia_concedida / $this->getValorReceitaCorrenteLiquida()
                ) * 100,
        ];
        $linhas[11] = [
            'descricao' => 'Limite definido por Resolução do Senado Federal',
            'valor' => $this->simplificadoAnexoIII->limite_definido_resolucao_senado,
            'percentual' => (
                    $this->simplificadoAnexoIII->limite_definido_resolucao_senado
                    / $this->getValorReceitaCorrenteLiquida()
                ) * 100,
        ];
        $linhas[12] = [
            'descricao' => 'Operações de Crédito Internas e Externas',
            'valor' => $this->simplificadoAnexoIV->total_operacoes_credito,
            'percentual' => $this->simplificadoAnexoIV->perc_operacoes_credito,
        ];
        $linhas[13] = [
            'descricao' => 'Limite definido pelo Senado Federal para Operações de Crédito Externas e Internas',
            'valor' => $this->simplificadoAnexoIV->total_credito_interna_externa,
            'percentual' => $this->simplificadoAnexoIV->perc_credito_interna_externa,
        ];
        $linhas[14] = [
            'descricao' => 'Operações de Crédito por Antecipação da Receita',
            'valor' => $this->simplificadoAnexoIV->total_antecipacao_receita_orcamentaria,
            'percentual' => $this->simplificadoAnexoIV->perc_antecipacao_receita_orcamentaria,
        ];
        $linhas[15] = [
            'descricao' => 'Limite Definido pelo Senado Federal para Operações de Crédito por Antecipação da Receita',
            'valor' => $this->simplificadoAnexoIV->total_credito_interna_receita_orcamentaria,
            'percentual' => $this->simplificadoAnexoIV->perc_credito_interna_receita_orcamentaria,
        ];
        $linhas[16] = [
            'descricao' => 'Restos a Pagar',
            'rp_nao_processado' => $this->simplificadoAnexoV->rp_nao_processado,
            'disponibilidade_caixa_liquida' => $this->simplificadoAnexoV->disponibilidade_caixa_liquida,
        ];

        return $linhas;
    }

    protected function transformPeriodoEmPeriodoMensal()
    {
        switch ($this->periodo->getCodigo()) {
            case 12: //1º SEMESTRE
                return 22;
            case 13: //2º SEMESTRE
            case 16: //3º QUADRIMESTRE
                return 28;
            case 14: //1º QUADRIMESTRE
                return 20;
            case 15: //2º QUADRIMESTRE
                return 24;
            default:
                throw new \Exception("Período não mapeado");
        }
    }

    private function simplificadoAnexoIINovo()
    {
        $instituicoes = array_map(function ($instituicao) {
            return $instituicao->getCodigo();
        }, $this->instituicoes);

        $filtros = [
            'codigo_relatorio' => AnexoDoisRgfFactory::getCodigoRelatorio($this->ano),
            'periodo' => $this->periodo->getCodigo(),
            'instituicoes' => $instituicoes,
            'DB_instit' => db_getsession('DB_instit'),
            'DB_anousu' => $this->ano
        ];

        $service = AnexoDoisRgfFactory::getService($this->ano, $filtros);
        $simplificado = $service->getSimplificado();

        foreach ($simplificado as $dado) {
            $linha = new Linha();
            $linha->addColuna(105, $dado->nome, "TR", 0, 'L', 0);
            $linha->addColuna(40, $this->formatarValor($dado->valor), 'TLR', 0, 'R', 0);
            $linha->addColuna(45, $this->formatarValor($dado->percentual), "TL", 1, 'R', 0);
            $this->linhas[] = $linha;
        }
    }


    protected function simplificadoAnexoVApos2021()
    {
        $instituicoes = [];
        foreach ($this->instituicoes as $instituicao) {
            $instituicoes[] = $instituicao->getCodigo();
        }

        $filtros = [
            'codigo_relatorio' => AnexoCincoRgfFactory::getCodigoRelatorio($this->ano),
            'periodo' => $this->periodo->getCodigo(),
            'DB_anousu' => $this->ano,
            'instituicoes' => $instituicoes,
            'DB_instit' => db_getsession('DB_instit')
        ];

        $service = AnexoCincoRgfFactory::getService($this->ano, $filtros);
        return $service->getSimplificado();
    }
}
