<?php

namespace App\Domain\Financeiro\Tesouraria\Relatorios\Tef\ExtratoContaBancaria;

use App\Domain\Financeiro\Tesouraria\Relatorios\Tef\ExtratoContaBancaria\ExtratoContaBancariaModel;
use ECidade\Pdf\Pdf;

class ExtratoContaBancariaPDF extends Pdf
{
    /**
     * @var ExtratoContaBancariaModel
     */
    private $dados;

    public $lQuebra_Historico;
    public $quebra_data;
    public $contas;
    public $lImprimeSaldo;

    public function setDadosRelatorio(ExtratoContaBancariaModel $dadosRelatorio)
    {
        $this->dados = $dadosRelatorio;
    }

    public function emitir()
    {
        $this->imprimir();

        $fileName = 'tmp/ExtratoContaBancaria' . time() . '.pdf';
        $this->output('F', $fileName);

        return [
            "name" => "Relatório extrato conta bancária PDF",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    public function imprimir()
    {

        $this->SetTextColor(0, 0, 0);
        $this->setfillcolor(235);
        $this->AddPage('L');

        $this->quebra_data = '';
        $this->lQuebra_Historico = false;
        $this->contas  = $this->dados->dados;

        if (count($this->contas) > 0) {
            foreach ($this->contas as $oConta) {
                $this->lImprimeSaldo = true;
                if ($this->GetY() > $this->geth() - 40) {
                    $this->AddPage("L");
                }

                $this->imprimeConta(
                    $oConta->k13_reduz,
                    $oConta->k13_descr,
                    $oConta->k13_dtimplantacao,
                    $oConta->debito,
                    $oConta->credito,
                    $this->lImprimeSaldo
                );

                $this->lImprimeSaldo = false;
                $this->imprimeCabecalho();
                $this->processaDadosConta($oConta);
            }
        }
    }

    public function processaDadosConta($conta)
    {

        foreach ($conta->data as $oData) {
            if (property_exists($oData, 'movimentacoes') && !empty($oData->movimentacoes)) {
                $this->processaMovimentosConta($conta, $oData);
            }

            if ($this->dados->totalizador_diario == 's') {
                $this->dados->saldo_dia_credito = $oData->saldo_dia_credito;
                $this->dados->saldo_dia_debito = $oData->saldo_dia_debito;
                $this->dados->saldo_dia_final = $oData->saldo_dia_final;
            }
        }

        $this->quebra_data = '';
        $this->imprimeTotalMovDia(
            $this->dados->saldo_dia_debito,
            $this->dados->saldo_dia_credito,
            $this->dados->saldo_dia_final
        );
        $this->dados->saldo_dia_credito = 0;
        $this->dados->saldo_dia_debito = 0;
        $this->dados->saldo_dia_final = 0;

        if ($this->GetY() > $this->geth() - 40) {
            $this->AddPage("L");
        }

        $this->imprimeTotalMovConta($conta->debitado, $conta->creditado, $conta->atual);
        $this->Ln(5);
    }

    public function processaMovimentosConta($oConta, $oData)
    {
        foreach ($oData->movimentacoes as $oMovimento) {
            if ($this->dados->totalizador_diario == 's' &&
                $this->quebra_data != '' &&
                $this->quebra_data != $oData->data
            ) {
                $this->imprimeTotalMovDia(
                    $this->dados->saldo_dia_debito,
                    $this->dados->saldo_dia_credito,
                    $this->dados->saldo_dia_final
                );
                $this->dados->saldo_dia_debito = 0;
                $this->dados->saldo_dia_credito = 0;
                $this->dados->saldo_dia_final = 0;
            }

            if ($this->GetY() > $this->geth() - 25) {
                $this->AddPage('L');
                $this->imprimeConta(
                    $oConta->k13_reduz,
                    $oConta->k13_descr,
                    $oConta->k13_dtimplantacao,
                    $oConta->debito,
                    $oConta->credito,
                    $this->lImprimeSaldo
                );
                $this->imprimeCabecalho();
            }

            if ($this->lQuebra_Historico) {
                $this->imprimeConta(
                    $oConta->k13_reduz,
                    $oConta->k13_descr,
                    $oConta->k13_dtimplantacao,
                    $oConta->debito,
                    $oConta->credito,
                    $this->lImprimeSaldo
                );
                $this->imprimeCabecalho();
                $this->lQuebra_Historico = false;
            }

            $this->imprimeDadosMovimento($oMovimento, $oData->data);

            if ($this->dados->imprime_analitico == 'a') {
                $this->imprimeAnalitico($oConta, $oMovimento);
            }

            $this->quebra_data = $oData->data;
        }
    }

    public function imprimeAnalitico($oConta, $oMovimento)
    {

        if (!isset($oMovimento->agrupado)) {
            if ($this->GetY() > $this->geth() - 25) {
                $this->AddPage("L");

                $this->imprimeConta(
                    $oConta->k13_reduz,
                    $oConta->k13_descr,
                    $oConta->k13_dtimplantacao,
                    $oConta->debito,
                    $oConta->credito,
                    $this->lImprimeSaldo
                );
                $this->imprimeCabecalho();
            }

            $this->Cell(20, 5, "", 0, 0, "C", 0);
            $this->Cell(30, 5, "Autenticação mecânica:", "", 0, "L", 0);
            $this->Cell(150, 5, trim($oMovimento->k12_codautent), "", 0, "L", 0);
            $this->Ln();
            if ($this->GetY() > $this->geth() - 25) {
                $this->AddPage("L");
                $this->imprimeConta(
                    $oConta->k13_reduz,
                    $oConta->k13_descr,
                    $oConta->k13_dtimplantacao,
                    $oConta->debito,
                    $oConta->credito,
                    $this->lImprimeSaldo
                );
                $this->imprimeCabecalho();
            }

            $this->Cell(20, 5, "", 0, 0, "C", 0);
            $this->Cell(65, 5, "Classificação de baixa bancária:", "", 0, "L", 0);
            $this->Cell(150, 5, $oMovimento->codigo, "", 0, "L", 0);
            $this->Ln();
            if ($this->GetY() > $this->geth() - 25) {
                $this->AddPage("L");

                $this->imprimeConta(
                    $oConta->k13_reduz,
                    $oConta->k13_descr,
                    $oConta->k13_dtimplantacao,
                    $oConta->debito,
                    $oConta->credito,
                    $this->lImprimeSaldo
                );
                $this->imprimeCabecalho();
            }

            $this->Cell(20, 5, "", 0, 0, "C", 0);
            $this->Cell(25, 5, "Nome/Razão Social:", "", 0, "L", 0);
            $this->Cell(150, 5, $oMovimento->credor, "", 0, "L", 0);
            $this->Ln();

            $this->lQuebra_Historico = false;
            if ($oMovimento->historico != '' && $this->dados->imprime_historico == 's') {
                if ($this->gety() > $this->geth() - 25) {
                    $this->addPage("L");
                    $this->lQuebra_Historico = true;
                }
                $this->Cell(20, 5, "", 0, 0, "C", 0);
                $oMovimento->historico = $this->MultiCell(0, 5, "Historico: {$oMovimento->historico}", 0, "L", 0);
            }
        }
    }

    public function headers()
    {

        $this->exibeHeader(true);
        $this->addTitulo('EXTRATO BANCÁRIO ' . ($this->dados->imprime_analitico == 'a' ? 'ANALÍTICO' : 'SINTÉTICO'));
        $this->addTitulo('PERÍODO : ' . $this->dados->data_inicial . ' À ' . $this->dados->data_final);

        if ($this->dados->somente_contas_bancarias == 's') {
            $this->addTitulo('SOMENTE CONTAS BANCÁRIAS');
        }

        if ($this->dados->agrupapor == 2) {
            $this->addTitulo('AGRUPAMENTO: PELA CONTA DE RECEITA');
        } elseif ($this->dados->agrupapor == 3) {
            $this->addTitulo('AGRUPAMENTO: PELOS CÓDIGOS DE EMPENHO E RECEITA');
        }

        if ($this->dados->receitaspor == 1) {
            $this->addTitulo('BAIXA BANCÁRIA: NÃO AGRUPADO PELA CLASSIFICAÇÃO');
        } elseif ($this->dados->receitaspor == 2) {
            $this->addTitulo('BAIXA BANCÁRIA: AGRUPADO PELA CLASSIFICAÇÃO');
        }
    }

    public function imprimeConta($codigo, $descricao, $dtimplantacao, $debito, $credito, $lImprimeSaldo)
    {
        $this->SetFont('Arial', 'b', 8);
        $this->Cell(12, 5, "CONTA:", 0, 0, "L", 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(90, 5, $codigo . " - " . $descricao, 0, 0, "L", 0);
        $this->SetFont('Arial', 'b', 8);

        $this->Cell(72, 5, "DATA IMPLANTAÇÃO DA CONTA NA TESOURARIA: ", 0, 0, "L", 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(10, 5, db_formatar($dtimplantacao, 'd'), 0, 0, "L", 0);
        $this->SetFont('Arial', 'b', 8);

        if ($lImprimeSaldo) {
            $this->Cell(40, 5, "SALDO ANTERIOR:", 0, 0, "R", 0);
            $this->Cell(25, 5, $debito == 0 ? "" : formataValorMonetario($debito), 0, 0, "R", 0);
            $this->Cell(25, 5, $credito == 0 ? "" : formataValorMonetario($credito), 0, 0, "R", 0);
        }

        $this->Ln();
        $this->SetFont('Arial', '', 7);
    }

    public function imprimeCabecalho()
    {
        $this->SetFont('Arial', 'b', 8);
        $this->Cell(20, 5, "DATA", "T", 0, "C", 1);
        $this->Cell(85, 5, "CONTRAPARTIDA", "TL", 0, "C", 1);
        $this->Cell(25, 5, "PLANILHA", "TL", 0, "C", 1);
        $this->Cell(25, 5, "EMPENHO", "TL", 0, "C", 1);
        $this->Cell(25, 5, "ORDEM", "TL", 0, "C", 1);
        $this->Cell(25, 5, "CHEQUE", "TL", 0, "C", 1);
        $this->Cell(25, 5, "SLIP", "TL", 0, "C", 1);
        $this->Cell(25, 5, "DÉBITO", "TL", 0, "C", 1);
        $this->Cell(25, 5, "CRÉDITO", "TL", 0, "C", 1);
        $this->Ln();
        $this->Cell(20, 5, "", "TB", 0, "R", 1);
        $this->Cell(210, 5, "INFORMAÇÕES COMPLEMENTARES", "TLB", 0, "C", 1);
        $this->Cell(25, 5, "", "TLB", 0, "R", 1);
        $this->Cell(25, 5, "", "TB", 0, "R", 1);
        $this->Ln();
        $this->SetFont('Arial', '', 7);
    }

    public function imprimeTotalMovDia($saldoDiaDebito, $saldoDiaCredito, $saldoDiaFinal)
    {
        $this->SetFont('Arial', 'b', 8);
        $this->Cell(20, 5, "", "TB", 0, "R", 1);
        $this->Cell(210, 5, "TOTAIS DA MOVIMENTAÇÃO NO DIA:", "TB", 0, "R", 1);
        $this->Cell(25, 5, $saldoDiaDebito == 0 ? "" : formataValorMonetario($saldoDiaDebito), "TLB", 0, "R", 1);
        $this->Cell(25, 5, $saldoDiaCredito == 0 ? "" : formataValorMonetario($saldoDiaCredito), "TLB", 0, "R", 1);
        $this->Ln();
        $this->Cell(20, 5, "", "TB", 0, "R", 1);
        $this->Cell(210, 5, "SALDO NO DIA:", "TB", 0, "R", 1);
        $this->Cell(50, 5, $saldoDiaFinal == 0 ? "" : formataValorMonetario($saldoDiaFinal), "TLB", 0, "R", 1);
        $this->Ln();
        $this->SetFont('Arial', '', 7);
    }

    public function imprimeTotalMovConta($saldoDebitado, $saldoCreditado, $saldoAtual)
    {

        $this->SetFont('Arial', 'b', 8);
        $this->Cell(20, 5, "", "TB", 0, "R", 1);
        $this->Cell(210, 5, "TOTAIS DA MOVIMENTAÇÃO 1:", "TB", 0, "R", 1);
        $this->Cell(25, 5, $saldoDebitado == 0 ? "" : formataValorMonetario($saldoDebitado), "TLB", 0, "R", 1);
        $this->Cell(25, 5, $saldoCreditado == 0 ? "" : formataValorMonetario($saldoCreditado), "TB", 0, "R", 1);
        $this->Ln();
        $this->Cell(20, 5, "", "TB", 0, "R", 1);
        $this->Cell(210, 5, "SALDO FINAL:", "TB", 0, "R", 1);
        $this->Cell(50, 5, $saldoAtual == 0 ? "" : formataValorMonetario($saldoAtual), "TLB", 0, "R", 1);
        $this->Ln();
        $this->SetFont('Arial', '', 7);
    }

    public function imprimeDadosMovimento($oMovimento, $data)
    {
        $this->Cell(20, 5, db_formatar($data, 'd'), "T", 0, "C", 0);
        $this->Cell(85, 5, $oMovimento->contrapartida, "T", 0, "L", 0);
        $this->Cell(25, 5, $oMovimento->planilha, "T", 0, "C", 0);
        $this->Cell(25, 5, $oMovimento->empenho, "T", 0, "C", 0);
        $this->Cell(25, 5, $oMovimento->ordem == "0" ? "" : $oMovimento->ordem, "T", 0, "C", 0);
        $this->Cell(25, 5, $oMovimento->cheque == "0" ? "" : $oMovimento->cheque, "T", 0, "C", 0);
        $this->Cell(25, 5, $oMovimento->slip, "T", 0, "C", 0);
        $this->Cell(
            25,
            5,
            $oMovimento->valor_debito == 0 ? "" : formataValorMonetario($oMovimento->valor_debito),
            "T",
            0,
            "R",
            0
        );
        $this->Cell(
            25,
            5,
            $oMovimento->valor_credito == 0 ? "" : formataValorMonetario($oMovimento->valor_credito),
            "T",
            0,
            "R",
            0
        );
        $this->Ln();
    }
}
