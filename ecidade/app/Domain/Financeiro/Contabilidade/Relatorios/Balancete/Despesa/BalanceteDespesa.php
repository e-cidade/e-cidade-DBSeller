<?php


namespace App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Despesa;

use App\Domain\Financeiro\Contabilidade\Relatorios\Pdf;
use stdClass;

abstract class BalanceteDespesa extends Pdf
{
    /**
     * Dados para impressão
     * @var array
     */
    protected $dados;
    /**
     * @var stdClass
     */
    protected $totalizador;

    protected $modelo;

    /**
     * @var float
     */
    protected $wValor = 30.4;
    /**
     * @var string
     */
    private $assinaturaPrefeito;
    /**
     * @var string
     */
    private $assinaturaContador;
    /**
     * Valores válidos
     * @var string
     */
    private $apresentarRecurso;

    public function headers($periodo, $instituicoes)
    {
        $this->addTitulo('BALANCETE DA DESPESA POR COMPLEMENTO');
        $this->addTitulo('');
        $this->addTitulo("MODELO: {$this->modelo}");
        $this->addTitulo("PERÍODO: {$periodo}");
        $this->addTitulo("INSTITUIÇÕES: {$instituicoes}");
    }

    public function setDados($dados)
    {
        $this->dados = $dados;
    }

    /**
     * @param stdClass $totalizador
     */
    public function setTotalizador(stdClass $totalizador)
    {
        $this->totalizador = $totalizador;
    }

    abstract protected function imprimir();

    /**
     * @return array
     */
    public function emitir()
    {
        $this->imprimir();

        $filename = sprintf('tmp/balancete-despesa-%s.pdf', time());
        $this->Output('F', $filename);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    public function setApresentarRecurso($tipo)
    {
        $this->apresentarRecurso = $tipo;
    }

    /**
     * Imprime o cabeçalho do relatório
     */
    protected function imprimeCabecalho()
    {
        $this->AddPage();
        $this->bold();

        $this->SetTextColor(0, 0, 0);
        $this->Cell(20, 8, 'RECURSO', 0, 0, 'L', 1);
        $this->cellAdapt($this->fonte, 20, 8, 'COMPLEMENTO', 0, 0, 'L', 1);
        $this->Cell($this->wValor, $this->hLinha, 'EMPENHADO NO MÊS', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'ANULADO NO MÊS', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'EMP LIQUIDO NO MÊS', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'LIQUIDADO NO MÊS', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'PAGO NO MÊS', 0, 1, 'C', 1);

        $this->SetX(50);

        $this->Cell($this->wValor, $this->hLinha, 'EMPENHADO NO ANO', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'ANULADO NO ANO', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'EMP LIQUIDO NO ANO', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'LIQUIDADO NO ANO', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'PAGO NO ANO', 0, 1, 'C', 1);

        $this->SetTextColor(9, 84, 32);

        $this->Cell(40, 8, 'SALDOS', 0, 0, 'L', 1);
        $this->Cell($this->wValor, $this->hLinha, 'SALDO INICIAL', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'SUPLEMENTAÇÕES', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'CRED. ESPECIAIS', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'REDUÇÕES', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'TOTAL CRÉDITOS', 0, 1, 'C', 1);

        $this->SetX(50);
        $this->Cell(60.8, $this->hLinha, '', 0, 0, 'L', 1);

        $this->Cell($this->wValor, $this->hLinha, 'SALDO DISPONÍVEL', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'A LIQUIDAR', 0, 0, 'C', 1);
        $this->Cell($this->wValor, $this->hLinha, 'A PAGAR LIQUIDADO', 0, 1, 'C', 1);
        $this->SetTextColor(0, 0, 0);
        $this->Line(10, 35, 202, 35);
        $this->Line(10, $this->GetY(), 202, $this->GetY());
        $this->regular();
    }

    /**
     * @param $recursos
     */
    protected function imprimeRecursos($recursos)
    {
        $fill = true;
        foreach ($recursos as $recurso) {
            $fill = !$fill;
            $this->quebraPagina();

            $fonte = $this->apresentacaoRecurso($recurso);
            $this->Cell(20, 8, $fonte, '', 0, 'L', $fill);
            $this->Cell(20, 8, "{$recurso->complemento}", '', 0, 'L', $fill);
            $valores = $recurso->valores;
            $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($valores->empenhado), 0, 0, 'R', $fill);
            $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($valores->anulado), 0, 0, 'R', $fill);
            $empLiquidoFormatado = formataValorMonetario($valores->empenhado_liquido);
            $this->Cell($this->wValor, $this->hLinha, $empLiquidoFormatado, 0, 0, 'R', $fill);
            $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($valores->liquidado), 0, 0, 'R', $fill);
            $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($valores->pago), 0, 1, 'R', $fill);

            $this->SetX(50);
            $formatadoEmpAcumulado = formataValorMonetario($valores->empenhado_acumulado);
            $this->Cell($this->wValor, $this->hLinha, $formatadoEmpAcumulado, 0, 0, 'R', $fill);
            $formataAcumulado = formataValorMonetario($valores->anulado_acumulado);
            $this->Cell($this->wValor, $this->hLinha, $formataAcumulado, 0, 0, 'R', $fill);
            $formataEmpLiq = formataValorMonetario($valores->empenhado_liquido_acumulado);
            $this->Cell($this->wValor, $this->hLinha, $formataEmpLiq, 0, 0, 'R', $fill);
            $formataLiq = formataValorMonetario($valores->liquidado_acumulado);
            $this->Cell($this->wValor, $this->hLinha, $formataLiq, 0, 0, 'R', $fill);
            $formataPagoAcumulado = formataValorMonetario($valores->pago_acumulado);
            $this->Cell($this->wValor, $this->hLinha, $formataPagoAcumulado, 0, 1, 'R', $fill);
        }
    }

    /**
     * @param $dado
     */
    protected function imprimeSaldos($dado)
    {
        $this->Line(10, $this->GetY(), 202, $this->GetY());
        // imprime saldos
        $this->bold();
        $this->SetTextColor(9, 84, 32);
        $totalCredito = $dado->saldo_inicial + $dado->suplementado + $dado->suplementado_especial - $dado->reducoes;
        $this->Cell(20, $this->hLinha, '');
        $this->Cell(20, $this->hLinha, '');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($dado->saldo_inicial), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($dado->suplementado), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($dado->suplementado_especial), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($dado->reducoes), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($totalCredito), 0, 1, 'R');

        $this->Cell(100.8, 4, '');

        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($dado->saldo_disponivel), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($dado->a_liquidar), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($dado->a_pagar_liquidado), 0, 1, 'R');
        $this->SetTextColor(0, 0, 0);
        $this->regular();
    }

    /**
     * @param $totais
     * @param string $mensagem
     */
    protected function imprimeTotalizador($totais, $mensagem = 'TOTAIS GERAIS')
    {
        // 4 é o numero de linhas do totalizador
        $altura = 4 * $this->hLinha;
        if ($this->getAvailHeight() < ($this->hQuebraPagina + $altura)) {
            $this->imprimeCabecalho();
        }

        $this->Line(10, $this->GetY(), 202, $this->GetY());

        $this->bold();

        $this->Cell($this->wLinhaP, $this->hLinha, $mensagem, '', 1, 'L', 1);
        $this->SetX(50);
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($totais->empenhado), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($totais->anulado), 0, 0, 'R');
        $empLiquidoFormatado = formataValorMonetario($totais->empenhado_liquido);
        $this->Cell($this->wValor, $this->hLinha, $empLiquidoFormatado, 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($totais->liquidado), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($totais->pago), 0, 1, 'R');

        $this->SetX(50);
        $formatadoEmpAcumulado = formataValorMonetario($totais->empenhado_acumulado);
        $this->Cell($this->wValor, $this->hLinha, $formatadoEmpAcumulado, 0, 0, 'R');
        $formataAcumulado = formataValorMonetario($totais->anulado_acumulado);
        $this->Cell($this->wValor, $this->hLinha, $formataAcumulado, 0, 0, 'R');
        $formataEmpLiq = formataValorMonetario($totais->empenhado_liquido_acumulado);
        $this->Cell($this->wValor, $this->hLinha, $formataEmpLiq, 0, 0, 'R');
        $formataLiq = formataValorMonetario($totais->liquidado_acumulado);
        $this->Cell($this->wValor, $this->hLinha, $formataLiq, 0, 0, 'R');
        $formataPagoAcumulado = formataValorMonetario($totais->pago_acumulado);
        $this->Cell($this->wValor, $this->hLinha, $formataPagoAcumulado, 0, 1, 'R');

        $this->SetTextColor(9, 84, 32);
        $totalCredito = $totais->saldo_inicial + $totais->suplementado + $totais->suplementado_especial;
        $totalCredito -= $totais->reducoes;
        $this->SetX(50);
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($totais->saldo_inicial), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($totais->suplementado), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($totais->suplementado_especial), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($totais->reducoes), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($totalCredito), 0, 1, 'R');

        $this->Cell(100.8, 4, '');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($totais->saldo_disponivel), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($totais->a_liquidar), 0, 0, 'R');
        $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($totais->a_pagar_liquidado), 0, 1, 'R');
        $this->SetTextColor(0, 0, 0);
        $this->Line(10, $this->GetY(), 202, $this->GetY());
    }

    public function setAssinaturas($prefeito, $contador)
    {
        $this->assinaturaPrefeito = $prefeito;
        $this->assinaturaContador = $contador;
    }

    protected function imprimirAssinaturas()
    {
        $this->SetY($this->GetY() + 20);

        if ($this->validaQuebraPagina()) {
            $this->AddPage();
        }

        $this->bold();
        $y = $this->GetY();
        $w = ($this->wLinhaP / 2);
        $this->MultiCell($w, $this->hLinha, $this->assinaturaPrefeito, 0, 'C');
        $this->SetXY(10 + $w, $y);
        $this->MultiCell($w, $this->hLinha, $this->assinaturaContador, 0, 'C');
    }

    private function apresentacaoRecurso($recurso)
    {
        switch ($this->apresentarRecurso) {
            case 'fonteRecurso':
            default:
                return $recurso->recurso;
            case 'depara':
                return sprintf('%s - %s', $recurso->recurso, $recurso->subrecurso);
            case 'siconfi':
                return $recurso->siconfi;
        }
    }
}
