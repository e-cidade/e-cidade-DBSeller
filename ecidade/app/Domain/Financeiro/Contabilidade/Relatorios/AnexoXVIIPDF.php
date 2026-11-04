<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios;

use cl_assinatura;

class AnexoXVIIPDF
{
    /**
     * @var integer
     */
    private $ano;

    private $font = "arial";

    private $fontSize = 8;

    public $pdf;

    public function __construct($ano)
    {
        $this->pdf = new \ECidade\Pdf\Pdf('L');
        $this->pdf->SetMargins(10, 8, 8);
        $this->pdf->init(false);
        $this->pdf->AliasNbPages();
        
        $this->ano = $ano;
    }

    /**
     * @param array $dados
     * @return string
     */
    public function emitir(array $dados)
    {
        $this->criaHeaders();
        $this->criaCabecalho();
        $this->criaRPs($dados);
        $this->criaLinhasBalanceteVerficacao($dados);
        $this->criaLinhasDepositos($dados);
        $this->assinatura();

        return $this->imprimir();
    }

    private function criaHeaders()
    {
        $head2 ="DEMONSTRATIVO DA DÍVIDA FLUTUANTE";
        $head4 = "EXERCÍCIO : {$this->ano}";
        $head6 = "ANEXO 17 DA LEI 4320/64";

        $this->pdf->addTitulo($head2);
        $this->pdf->addTitulo($head4);
        $this->pdf->addTitulo($head6);
    }

    private function criaCabecalho()
    {
        $this->pdf->AddPage("L");

        $this->pdf->SetFont($this->font, 'b', $this->fontSize);

        $this->pdf->Cell(105, $this->fontSize * 2, "TITULOS", "TBR", 0, "C", 0);
        $this->pdf->Cell(40, $this->fontSize, "SALDO DO EXERCICIO ", "TR", 0, "C", 0);
        $this->pdf->Cell(88, $this->fontSize, "MOVIMENTAÇÃO NO EXERCICIO", "BTR", 0, "C", 0);
        $this->pdf->Cell(45, $this->fontSize, "SALDO PARA ", "T", 1, "C", 0);

        $this->pdf->SetX(115);
        $this->pdf->Cell(40, $this->fontSize, "ANTERIOR R$", "BR", 0, "C", 0);
        $this->pdf->Cell(44, $this->fontSize, "INSCRIÇÃO", "BR", 0, "C", 0);
        $this->pdf->Cell(44, $this->fontSize, "BAIXA", "BR", 0, "C", 0);
        $this->pdf->Cell(45, $this->fontSize, "O EXERCICIO SEGUINTE R$", "B", 1, "C", 0);
    }

    private function criaRPs($dados)
    {
        $this->pdf->SetFont($this->font, 'b', $this->fontSize);
        $this->pdf->Cell(105, 6, "RESTOS A PAGAR", 'R', 0, "L", 0);
        $this->pdf->Cell(40, 6, "", "R", 0, "C", 0);
        $this->pdf->Cell(44, 6, "", "R", 0, "C", 0);
        $this->pdf->Cell(44, 6, "", "R", 0, "C", 0);
        $this->pdf->Cell(45, 6, "", "L", 1, "C", 0);

        $this->pdf->Cell(105, 6, "	 PROCESSSADOS", 'R', 0, "L", 0);

        $this->pdf->Cell(40, 6, \db_formatar($dados['totalizadorasaldoanterior'], "f"), "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, \db_formatar($dados['totalizadorainscricao'], "f"), "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, \db_formatar($dados['totalizadorabaixa'], "f"), "R", 0, "R", 0);
        $this->pdf->Cell(45, 6, \db_formatar($dados['saldofinalrp'], "f"), "L", 1, "R", 0);

        $this->pdf->SetFont($this->font, '', $this->fontSize);

        $linhas = (object) $dados['linhasrp'];

        foreach ($linhas as $exercicio => $linha) {
            $saldofinal = (($linha->saldoanterior + $linha->inscricao) - $linha->baixa);
            $this->pdf->Cell(105, 6, "	 EXERCÍCIO {$exercicio}", "R", 0, "L", 0);
            $this->pdf->Cell(40, 6, \db_formatar($linha->saldoanterior, "f"), "R", 0, "R", 0);
            $this->pdf->Cell(44, 6, \db_formatar($linha->inscricao, "f"), "R", 0, "R", 0);
            $this->pdf->Cell(44, 6, \db_formatar($linha->baixa, "f"), "R", 0, "R", 0);
            $this->pdf->Cell(45, 6, \db_formatar($saldofinal, "f"), "L", 1, "R", 0);
        }

        $this->pdf->SetFont($this->font, 'b', $this->fontSize);

        $this->pdf->Cell(105, 6, "	 NÃO PROCESSSADOS", 'R', 0, "L", 0);
        $this->pdf->Cell(40, 6, \db_formatar($dados['totalizadorasaldoanteriorrnp'], "f"), "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, \db_formatar($dados['totalizadorainscricaornp'], "f"), "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, \db_formatar($dados['totalizadorabaixarnp'], "f"), "R", 0, "R", 0);
        $this->pdf->Cell(45, 6, \db_formatar($dados['saldofinalrnp'], "f"), "L", 1, "R", 0);
        $this->pdf->SetFont($this->font, '', $this->fontSize);

        $linhas = (object) $dados['linhasrnp'];

        foreach ($linhas as $exercicio => $linha) {
            $saldofinal = (($linha->saldoanterior + $linha->inscricao) - $linha->baixa);
            $this->pdf->Cell(105, 6, "	 EXERCÍCIO {$exercicio}", "R", 0, "L", 0);
            $this->pdf->Cell(40, 6, \db_formatar($linha->saldoanterior, "f"), "R", 0, "R", 0);
            $this->pdf->Cell(44, 6, \db_formatar($linha->inscricao, "f"), "R", 0, "R", 0);
            $this->pdf->Cell(44, 6, \db_formatar($linha->baixa, "f"), "R", 0, "R", 0);
            $this->pdf->Cell(45, 6, \db_formatar($saldofinal, "f"), 0, 1, "R", 0);
        }

        $this->pdf->SetFont($this->font, 'b', $this->fontSize);

        $this->pdf->Cell(105, 6, "	 SUBTOTAL", "R", 0, "L", 0);
        $this->pdf->Cell(40, 6, \db_formatar(
            $dados['totalizadorasaldoanterior'] + $dados['totalizadorasaldoanteriorrnp'],
            "f"
        ), "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, \db_formatar(
            $dados['totalizadorainscricao'] + $dados['totalizadorainscricaornp'],
            "f"
        ), "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, \db_formatar(
            $dados['totalizadorabaixa'] + $dados['totalizadorabaixarnp'],
            "f"
        ), "R", 0, "R", 0);
        $this->pdf->Cell(45, 6, \db_formatar(
            $dados['saldofinalrp'] +$dados['saldofinalrnp'],
            "f"
        ), "L", 1, "R", 0);

        $this->pdf->Cell(105, 6, "	 SERVIÇO DA DÍVIDA A PAGAR", "R", 0, "L", 0);
        $this->pdf->Cell(40, 6, '', "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, '', "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, '', "R", 0, "R", 0);
        $this->pdf->Cell(45, 6, '', "L", 1, "R", 0);
    }

    private function criaLinhasBalanceteVerficacao($dados)
    {

        $totalDividasPagarAnterior = 0;
        $totalDividasPagarInscricao = 0;
        $totalDividasPagarBaixa = 0;
        $totalDividasPagarExercicioSeguinte = 0;

        $this->pdf->SetFont($this->font, '', $this->fontSize);

        $this->pdf->Cell(105, 6, "Interna em Contratos", 'R', 0, "L", 0);
        if (count($dados['linhasInternaContratos']) > 0) {
            foreach ($dados['linhasInternaContratos'] as $dado) {
                $this->pdf->Cell(40, 6, \db_formatar($dado->saldo_anterior, "f"), "R", 0, "R", 0);
                $this->pdf->Cell(44, 6, \db_formatar($dado->saldo_anterior_credito, "f"), "R", 0, "R", 0);
                $this->pdf->Cell(44, 6, \db_formatar($dado->saldo_anterior_debito, "f"), "R", 0, "R", 0);
                $this->pdf->Cell(45, 6, \db_formatar($dado->saldo_final, "f"), "L", 1, "R", 0);
            }
        } else {
            $this->pdf->Cell(40, 6, "0,00", "R", 0, "R", 0);
            $this->pdf->Cell(44, 6, "0,00", "R", 0, "R", 0);
            $this->pdf->Cell(44, 6, "0,00", "R", 0, "R", 0);
            $this->pdf->Cell(45, 6, "0,00", "L", 1, "R", 0);
        }

        $this->pdf->Cell(105, 6, "Externa em Contratos", 'R', 0, "L", 0);
        $this->pdf->Cell(40, 6, "0,00", "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, "0,00", "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, "0,00", "R", 0, "R", 0);
        $this->pdf->Cell(45, 6, "0,00", "L", 1, "R", 0);

        $this->pdf->Cell(105, 6, "Parcelamentos a Pagar", 'R', 0, "L", 0);
        if (count($dados['linhasParcelamentosPagar']) > 0) {
            foreach ($dados['linhasParcelamentosPagar'] as $dado) {
                $this->pdf->Cell(40, 6, \db_formatar($dado->saldo_anterior, "f"), "R", 0, "R", 0);
                $this->pdf->Cell(44, 6, \db_formatar($dado->saldo_anterior_credito, "f"), "R", 0, "R", 0);
                $this->pdf->Cell(44, 6, \db_formatar($dado->saldo_anterior_debito, "f"), "R", 0, "R", 0);
                $this->pdf->Cell(45, 6, \db_formatar($dado->saldo_final, "f"), "L", 1, "R", 0);
            }
        } else {
            $this->pdf->Cell(40, 6, "0,00", "R", 0, "R", 0);
            $this->pdf->Cell(44, 6, "0,00", "R", 0, "R", 0);
            $this->pdf->Cell(44, 6, "0,00", "R", 0, "R", 0);
            $this->pdf->Cell(45, 6, "0,00", "L", 1, "R", 0);
        }

        $this->pdf->SetFont($this->font, 'b', $this->fontSize);

        $this->pdf->Cell(105, 6, "	 SUBTOTAL", "R", 0, "L", 0);
        $this->pdf->Cell(40, 6, \db_formatar($dados['totalDividaPagarSaldoAnterior'], "f"), "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, \db_formatar($dados['totalDividaPagarSaldoAnteriorDebito'], "f"), "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, \db_formatar($dados['totalDividaPagarSaldoAnteriorCredito'], "f"), "R", 0, "R", 0);
        $this->pdf->Cell(45, 6, \db_formatar($dados['totalDividaPagarSaldoAnteriorSaldoFinal'], "f"), "L", 1, "R", 0);

        $linhas = (object) $dados['outraslinhas'];

        if (!empty($linhas)) {
            foreach ($linhas as $key => $dado) {
                $this->pdf->ln();

                $this->pdf->Cell(105, 6, $dado->c60_descr, 'R', 0, "L", 0);
                $this->pdf->Cell(40, 6, \db_formatar($dado->saldo_anterior, "f"), "R", 0, "R", 0);
                $this->pdf->Cell(44, 6, \db_formatar($dado->saldo_anterior_credito, "f"), "R", 0, "R", 0);
                $this->pdf->Cell(44, 6, \db_formatar($dado->saldo_anterior_debito, "f"), "R", 0, "R", 0);
                $this->pdf->Cell(45, 6, \db_formatar($dado->saldo_final, "f"), "L", 0, "R", 0);

                $this->pdf->SetFont($this->font, 'b', $this->fontSize);

                $this->pdf->ln();
                $this->pdf->Cell(105, 6, "	 SUBTOTAL", "R", 0, "L", 0);
                $this->pdf->Cell(40, 6, \db_formatar($dados['totlizadorasaldo'], "f"), "R", 0, "R", 0);
                $this->pdf->Cell(44, 6, \db_formatar($dados['totlizadoracredito'], "f"), "R", 0, "R", 0);
                $this->pdf->Cell(44, 6, \db_formatar($dados['totlizadoradebito'], "f"), "R", 0, "R", 0);
                $this->pdf->Cell(45, 6, \db_formatar($dados['totsaldofinaloutras'], "f"), "L", 1, "R", 0);
            }
        }
    }

    private function criaLinhasDepositos($dados)
    {

        $this->pdf->SetFont($this->font, 'B', $this->fontSize);
        $this->pdf->Cell(105, 6, "DEPÓSITOS", 'R', 0, "L", 0);
        $this->pdf->Cell(40, 6, "", "R", 0, "C", 0);
        $this->pdf->Cell(44, 6, "", "R", 0, "C", 0);
        $this->pdf->Cell(44, 6, "", "R", 0, "C", 0);

        $this->pdf->SetFont($this->font, '', $this->fontSize);

        $linhas = (object) $dados['deposito'];

        foreach ($linhas as $key => $dado) {
            $this->pdf->ln();
            $this->pdf->Cell(105, 6, $dado->c60_descr, 'R', 0, "L", 0);
            $this->pdf->Cell(40, 6, \db_formatar($dado->saldo_anterior, "f"), "R", 0, "R", 0);
            $this->pdf->Cell(44, 6, \db_formatar($dado->saldo_anterior_credito, "f"), "R", 0, "R", 0);
            $this->pdf->Cell(44, 6, \db_formatar($dado->saldo_anterior_debito, "f"), "R", 0, "R", 0);
            $this->pdf->Cell(45, 6, \db_formatar($dado->saldo_final, "f"), "L", 0, "R", 0);
        }

        $this->pdf->SetFont($this->font, 'b', $this->fontSize);


        $this->pdf->ln();
        $this->pdf->ln();
        $this->pdf->Cell(105, 6, "	 SUBTOTAL", "R", 0, "L", 0);
        $this->pdf->Cell(40, 6, \db_formatar($dados['totalizadorasaldo'], "f"), "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, \db_formatar($dados['totalizadoracredito'], "f"), "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, \db_formatar($dados['totalizadoradebito'], "f"), "R", 0, "R", 0);
        $this->pdf->Cell(45, 6, \db_formatar(
            $dados['totalizadorasaldo'] + $dados['totalizadoracredito'] - $dados['totalizadoradebito'],
            "f"
        ), "L", 1, "R", 0);

        $this->pdf->Cell(105, 6, "TOTAL", "R", 0, "L", 0);
        $this->pdf->Cell(40, 6, \db_formatar(
            $dados['totalizadorasaldo']
            + $dados['totalizadorasaldoanterior']
            + $dados['totalizadorasaldoanteriorrnp']
            + $dados['totalDividaPagarSaldoAnterior'],
            "f"
        ), "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, \db_formatar($dados['totalizadoracredito'] +
        $dados['totalizadorainscricao']
        + $dados['totalizadorainscricaornp']
        + $dados['totalDividaPagarSaldoAnteriorDebito'], "f"), "R", 0, "R", 0);
        $this->pdf->Cell(44, 6, \db_formatar(
            $dados['totalizadoradebito']
            + $dados['totalizadorabaixa']
            + $dados['totalizadorabaixarnp']
            + $dados['totalDividaPagarSaldoAnteriorCredito'],
            "f"
        ), "R", 0, "R", 0);
        $this->pdf->Cell(45, 6, \db_formatar(
            ($dados['totalizadorasaldo'] + $dados['totalizadoracredito'] - $dados['totalizadoradebito'])
            + ($dados['saldofinalrp'] + $dados['saldofinalrnp'])
            + $dados['totalDividaPagarSaldoAnteriorSaldoFinal'],
            "f"
        ), "L", 1, "R", 0);
    }

    public function assinatura()
    {
        $classinatura = new cl_assinatura;

        $ass_sec  = $classinatura->assinatura(1002, '0');
        $ass_cont = $classinatura->assinatura(1005, '0');

        $largura = ( $this->pdf->getW() ) / 2;
        $this->pdf->ln(30);
        $pos =  $this->pdf->getY();
        $this->pdf->multicell($largura, 3, $ass_sec, 0, "C", 0, 0);
        $this->pdf->ln();
        $this->pdf->setxy($largura, $pos);
        $this->pdf->multicell($largura, 3, $ass_cont, 0, "C", 0, 0);
        $this->pdf->ln();
    }

    protected function imprimir()
    {
        $fileName = 'tmp/demonstrativo_divida_flutuante' . time() . '.pdf';
        $this->pdf->Output("F", $fileName, false);
        return $fileName;
    }
}
