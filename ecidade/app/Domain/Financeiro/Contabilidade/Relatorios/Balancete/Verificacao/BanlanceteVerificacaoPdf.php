<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Verificacao;

use App\Domain\Financeiro\Contabilidade\Relatorios\Pdf;

class BanlanceteVerificacaoPdf extends Pdf
{
    protected $tipoPlano;
    protected $exibeContaBancaria = true;
    protected $porReduzido = true;
    protected $sintetico = false;

    /**
     * Dados a serem impresso
     * @var array
     */
    protected $dados = [];

    protected $wEstrutural = 30;
    protected $wReduzido = 15;
    protected $wInsttuicao = 8;
    protected $wDescricao = 100;
    protected $wValores = 24; // 96
    protected $wRecurso = 24;
    protected $wIsf = 6;
    protected $wTotal = 183;

    protected $totalSaldoAnterior = 0;
    protected $totalSaldoDebito = 0;
    protected $totalSaldoCredito = 0;
    protected $totalSaldoFinal = 0;


    protected $x = 0;

    public function __construct($orientation = 'L', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);
    }

    public function setTipoPlano($tipoPlano)
    {
        $this->tipoPlano = $tipoPlano;
        return $this;
    }

    public function setTipo($sintetico)
    {
        $this->sintetico = $sintetico;
        return $this;
    }

    public function setExibeContaBancaria($exibirContaBancaria)
    {
        $this->exibeContaBancaria = $exibirContaBancaria;
        return $this;
    }

    public function setPorReduzido($consolidarPorReduzido)
    {
        $this->porReduzido = $consolidarPorReduzido;
        return $this;
    }

    public function setDados($dados)
    {
        $this->dados = $dados;
        return $this;
    }

    public function headers($titulo, \stdClass $periodo, $instituicoes, $tipoPlano)
    {
        $this->addTitulo($titulo);
        $this->addTitulo('');
        $this->addTitulo("PERÍODO: " . headerPeriodo($periodo->dataInicio, $periodo->dataFim));
        $this->addTitulo("INSTITUIÇÕES: " . headerNomeInstituicoes($instituicoes));
        $this->addTitulo("PLANO: " . headerTipoPlano($tipoPlano));
        $this->addTitulo($this->sintetico ? 'SINTÉTICO' : 'ANALÍTICO');
    }

    public function emitir()
    {
        $this->calculaColunas();
        $this->imprimeCabecalho();
        $this->imprimirCorpo();
        $this->imprimirTotalizador();

        $filename = sprintf('tmp/balancete-verificacao-%s.pdf', time());
        $this->Output('F', $filename);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    protected function imprimeCabecalho()
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 7);
        $this->cell($this->wEstrutural, $this->hLinha, 'Estrutural', 'B', 0, 'C', 1);

        if ($this->porReduzido) {
            $this->cell($this->wReduzido, $this->hLinha, 'Reduz', 'B', 0, 'C', 1);
            $this->cell($this->wInsttuicao, $this->hLinha, 'Inst', 'B', 0, 'C', 1);
        }
        $this->cell($this->wDescricao, $this->hLinha, 'Descrição', 'B', 0, 'C', 1);
        if ($this->porReduzido) {
            $this->cell($this->wRecurso, $this->hLinha, 'Recurso', 'B', 0, 'C', 1);
        }
        $this->cell($this->wIsf, $this->hLinha, 'ISF', 'B', 0, 'C', 1);
        $this->cell($this->wValores, $this->hLinha, 'Saldo Anterior', 'B', 0, 'C', 1);
        $this->cell($this->wValores, $this->hLinha, 'Débitos', 'B', 0, 'C', 1);
        $this->cell($this->wValores, $this->hLinha, 'Créditos', 'B', 0, 'C', 1);
        $this->cell($this->wValores, $this->hLinha, 'Saldo', 'B', 1, 'C', 1);
    }

    protected function calculaColunas()
    {
        if (!$this->porReduzido) {
            $this->wDescricao += $this->wReduzido + $this->wInsttuicao + $this->wRecurso;
        }
    }

    protected function imprimirCorpo()
    {
        foreach ($this->dados as $dado) {
            $this->imprimeLinha($dado);
            $this->ln();
        }
    }

    protected function imprimeLinha($conta)
    {
        $this->quebraPagina();
        $conta->sintetica ? $this->bold() : $this->regular();

        $this->linha($this->wEstrutural, $conta->mascara, 'L');
        if ($this->porReduzido) {
            $reduzido = $conta->sintetica ? '' : $conta->reduzidos[0];
            $this->linha($this->wReduzido, $reduzido, 'C');
            $this->linha($this->wInsttuicao, $conta->instituicao, 'C');
        }
        $this->linha($this->wDescricao, $conta->nome, 'L');

        if ($this->porReduzido) {
            $complemento = str_pad($conta->complemento, 4, '0', STR_PAD_LEFT);
            $recurso = $conta->sintetica ? '' : "{$conta->siconfi} - $conta->subrecurso - {$complemento}";
            $this->linha($this->wRecurso, $recurso, 'C');
        }

        $isf = $conta->sintetica ? '' : $conta->indicador_superavit;
        $this->linha($this->wIsf, $isf, 'C');

        $this->imprimeValores($conta);
        $this->calculaTotalizadores($conta);
    }

    /**
     * @param $conta
     * @return void
     */
    public function imprimeValores($conta)
    {
        $sinalA = sinalContaBalanceteVerificacao($conta->classe, $conta->saldo_anterior);
        $sinalF = sinalContaBalanceteVerificacao($conta->classe, $conta->saldo_final);

        $saldoAnterior = $conta->saldo_anterior < 0 ? $conta->saldo_anterior*-1 : $conta->saldo_anterior;
        $saldoDebito = $conta->saldo_debito < 0 ? $conta->saldo_debito*-1 : $conta->saldo_debito;
        $saldoCredito = $conta->saldo_credito < 0 ? $conta->saldo_credito*-1 : $conta->saldo_credito;
        $saldoFinal = $conta->saldo_final < 0 ? $conta->saldo_final*-1 : $conta->saldo_final;
        $this->linha($this->wValores, formataValorMonetario($saldoAnterior) . " $sinalA", 'R');
        $this->linha($this->wValores, formataValorMonetario($saldoDebito), 'R');
        $this->linha($this->wValores, formataValorMonetario($saldoCredito), 'R');
        $this->linha($this->wValores, formataValorMonetario($saldoFinal) . " $sinalF", 'R');
    }

    /**
     * @return void
     */
    protected function imprimirTotalizador()
    {
        $this->quebraPagina();
        $this->SetFont('Arial', 'B', 7);
        $this->linha($this->wTotal, 'Total', 'R');

        $this->linha($this->wValores, formataValorMonetario($this->totalSaldoAnterior), 'R');
        $this->linha($this->wValores, formataValorMonetario($this->totalSaldoDebito), 'R');
        $this->linha($this->wValores, formataValorMonetario($this->totalSaldoCredito), 'R');
        $this->linha($this->wValores, formataValorMonetario($this->totalSaldoFinal), 'R');
    }

    protected function linha($w, $valor, $align = 'L', $fill = 0)
    {
        $this->cellAdapt($this->fonte, $w, $this->hLinha, $valor, 0, 0, $align, $fill);
    }

    /**
     * @param $conta
     * @return void
     */
    protected function calculaTotalizadores($conta)
    {
        if (!$conta->sintetica) {
            $this->totalSaldoAnterior += $conta->saldo_anterior;
            $this->totalSaldoDebito += $conta->saldo_debito;
            $this->totalSaldoCredito += $conta->saldo_credito;
            $this->totalSaldoFinal += $conta->saldo_final;
        }
    }
}
