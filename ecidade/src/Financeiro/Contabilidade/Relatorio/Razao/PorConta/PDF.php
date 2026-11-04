<?php

namespace ECidade\Financeiro\Contabilidade\Relatorio\Razao\PorConta;

class PDF extends Relatorio
{
    private $pdf;
    const TAMANHO = '4';

    public function __construct()
    {
        require_once modification(ECIDADE_PATH . "libs/db_libcontabilidade.php");
        require_once modification(ECIDADE_PATH . "fpdf151/PDFDocument.php");
        $this->pdf = new \PDFDocument();
    }

    private function writeHeader()
    {
        $head2 = "RAZÃO POR CONTA";

        $head5 = sprintf(
            'PERÍODO : %s à %s',
            db_formatar($this->getDataInicial(), 'd'),
            db_formatar($this->getDataFinal(), 'd')
        );

        $this->pdf->addHeaderDescription("\n" . $head2);
        $this->pdf->addHeaderDescription("\n\n" . $head5);
        $this->pdf->Open();
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage('L');
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetFillColor(235);
        $imprime_header = true;
        $this->pdf->SetFont('Arial', '', 7);
    }

    private function writeMovimentacaoDoDia($total_dia_debito, $total_dia_credito, $quebraLinha = false)
    {
        if ($quebraLinha) {
            $this->pdf->ln();
        }
        $this->pdf->setX(203);
        $this->pdf->Cell(40, self::TAMANHO, "MOVIMENTO DO DIA: ", "T", 0, "R");
        $this->pdf->Cell(20, self::TAMANHO, db_formatar($total_dia_debito, 'f'), "T", 0, "R");
        $this->pdf->Cell(20, self::TAMANHO, db_formatar($total_dia_credito, 'f'), "T", 0, "R");
    }

    private function writeSaldoDoDia($saldo, $sinal, $quebraLinha = false)
    {
        if ($quebraLinha) {
            $this->pdf->ln();
        }
        $this->pdf->setX(203);
        $this->pdf->Cell(40, self::TAMANHO, "SALDO DIA:", '0', 0, "R");
        $this->writeSaldo($saldo, $sinal);
        $this->pdf->setX(203);
        $this->pdf->Cell(80, self::TAMANHO, " ", "T", 0, "R");
        $this->pdf->ln();
    }

    public function writeSaldo($saldo, $sinal)
    {
        if ($sinal == 'D') {
            $this->pdf->Cell(20, self::TAMANHO, db_formatar(abs($saldo), 'f'), '0', 0, "R");
            $this->pdf->Cell(20, self::TAMANHO, '', '0', 1, "R");
        } else {
            $this->pdf->Cell(20, self::TAMANHO, '', '0', 0, "R");
            $this->pdf->Cell(20, self::TAMANHO, db_formatar(abs($saldo), 'f'), '0', 1, "R");
        }
    }

    private function writeTotalMovimentacoes($tot_mov_debito, $tot_mov_credito)
    {
        //$this->pdf->setX(203);
        $this->pdf->setX(200);
        $this->pdf->Cell(40, self::TAMANHO, "TOTAIS DA MOVIMENTAÇÃO:", 'T', 0, "R");
        $this->pdf->Cell(20, self::TAMANHO, db_formatar($tot_mov_debito, 'f'), 'T', 0, "R");
        $this->pdf->Cell(20, self::TAMANHO, db_formatar($tot_mov_credito, 'f'), 'T', 0, "R");
        $this->pdf->ln();
    }

    private function writeSaldoFinal($total_saldo_final, $sinal_final)
    {
        $this->pdf->setX(228);
        $this->pdf->Cell(30, self::TAMANHO, "SALDO FINAL:", '0', 0, "R");
        $this->pdf->Cell(5, self::TAMANHO, $sinal_final, '0', 0, "C");
        $this->pdf->Cell(
            20,
            self::TAMANHO,
            db_formatar(($total_saldo_final < 0 ? $total_saldo_final * -1 : $total_saldo_final), 'f'),
            '0',
            0,
            "R"
        );
        $this->pdf->ln();
    }

    private function writeSaldoFinalLancamento($saldo, $sinal)
    {
        $this->pdf->setX(203);
        $this->pdf->Cell(40, self::TAMANHO, "SALDO FINAL:", '0', 0, "R");
        $this->writeSaldo($saldo, $sinal);
        $this->pdf->ln();
    }

    private function writeSaldoAnterior($sinal_anterior, $saldo_anterior, $showBorder = false)
    {
        $border = $showBorder ? 'B' : '0';
        $this->pdf->setX(228);
        $this->pdf->Cell(30, self::TAMANHO, "SALDO ANTERIOR:", $border, 0, "R");
        $this->pdf->Cell(5, self::TAMANHO, $sinal_anterior, $border, 0, "C");
        $this->pdf->Cell(20, self::TAMANHO, db_formatar($saldo_anterior, 'f'), $border, 0, "R");
        $this->pdf->ln();
    }

    private function writeColunas()
    {
        $this->pdf->Cell(20, self::TAMANHO, "LAN", '1', 0, "L");
        $this->pdf->Cell(20, self::TAMANHO, "SEQ", '1', 0, "L");
        $this->pdf->Cell(20, self::TAMANHO, "DATA", '1', 0, "L");
        $this->pdf->Cell(20, self::TAMANHO, "RECEITA", '1', 0, "L");
        $this->pdf->Cell(20, self::TAMANHO, "DOTAÇÂO", '1', 0, "L");
        $this->pdf->Cell(20, self::TAMANHO, "EMPENHO", '1', 0, "L");
        $this->pdf->Cell(23, self::TAMANHO, "SUPLEMENTAÇÂO", '1', 0, "L");
        $this->pdf->Cell(90, self::TAMANHO, "DOCUMENTO", '1', 0, "L");
        $this->pdf->Cell(20, self::TAMANHO, "DEBITO", '1', 0, "R");
        $this->pdf->Cell(20, self::TAMANHO, "CREDITO", '1', 1, "R");
    }


    private function writeCabecalhoConta($reduzido, $estrutural, $descricao, $instituicao)
    {
        $this->pdf->Ln();
        $this->pdf->Cell(20, self::TAMANHO, "REDUZIDO:", 0, 0, "L");
        $this->pdf->Cell(20, self::TAMANHO, "$reduzido", 0, 1, "L");
        $this->pdf->Cell(20, self::TAMANHO, "ESTRUTURAL:", 0, 0, "L");
        $this->pdf->Cell(20, self::TAMANHO, "$estrutural", 0, 1, "L");
        $this->pdf->Cell(20, self::TAMANHO, "DESCRIÇÃO:", 0, 0, "L");
        $this->pdf->Cell(20, self::TAMANHO, "$descricao", 0, 1, "L");
        $this->pdf->Cell(20, self::TAMANHO, "INSTITUIÇÃO:", 0, 0, "L");
        $this->pdf->Cell(20, self::TAMANHO, "$instituicao", 0, 1, "L");
        $this->pdf->Ln();
    }

    private function writeBody()
    {
        $conta_atual = null;

        $dados = $this->getComplanoAnalitico();
        foreach ($dados as $dado) {
            $dado = (object)$dado;

            if (($conta_atual != $dado->c61_reduz) && !empty($conta_atual) && $this->getQuebraPaginaPorConta()) {
                $this->pdf->addpage("L");
            }

            $conta_atual = $dado->c61_reduz;

            $contas = $this->getDadosGerais($dado->c61_reduz);

            if (!empty($contas)) {
                $sinal_dia = '';
                $saldo_dia = 0;
                $total_dia_debito = 0;
                $total_dia_credito = 0;
                $tot_mov_debito = 0;
                $tot_mov_credito = 0;
                $saldo_anterior = "";
                $sinal_anterior = "";
                $repete = "";
                $repete_colunas = false;


                $datasaldo = $contas[0]['c69_data'];
                $iCor = 1;
                foreach ($contas as $conta) {
                    $conta = (object)$conta;
                    if ($datasaldo != $conta->c69_data && $this->getSaldoPorDia()) {
                        $this->writeMovimentacaoDoDia($total_dia_debito, $total_dia_credito);

                        if ($sinal_dia == "D") {
                            $total_dia_debito += $saldo_dia;
                        } else {
                            $total_dia_credito += $saldo_dia;
                        }

                        $sinal_dia = $this->retornaSinal($total_dia_debito, $total_dia_credito);
                        $saldo_dia = $this->calculaSaldo($total_dia_debito, $total_dia_credito, true);
                        $this->writeSaldoDoDia($saldo_dia, $sinal_dia, true);
                        $total_dia_debito = 0;
                        $total_dia_credito = 0;
                    }

                    $datasaldo = $conta->c69_data;

                    if ($repete != $conta->c61_codcon) {
                        // --- imprime movimentação da conta anterior, se houver conta anterior
                        if ($repete != "") {
                            $this->writeTotalMovimentacoes($tot_mov_debito, $tot_mov_credito);
                            $sinal_final = $this->retornaSinal($tot_mov_debito, $tot_mov_credito);

                            if ($saldo_anterior != "") {
                                if ($sinal_anterior == "D") {
                                    $tot_mov_debito += $saldo_anterior;
                                } else {
                                    $tot_mov_credito += $saldo_anterior;
                                }
                            }

                            $this->writeSaldoFinal(
                                $this->calculaSaldo($tot_mov_debito, $tot_mov_credito),
                                $sinal_final
                            );
                        }
                        //------------------ //  ------------------
                        $repete = $conta->c61_codcon;
                        $repete_colunas = true;
                        $this->writeCabecalhoConta(
                            $conta->c61_reduz,
                            $conta->c60_estrut,
                            $conta->conta_descr,
                            "{$dado->codigo} - {$dado->nomeinst}"
                        );
                        //--- saldo anterior

                        db_inicio_transacao();
                        $r_anterior = db_planocontassaldo_matriz(
                            $this->getAnoUsu(),
                            $this->getDataInicial(),
                            $this->getDataInicial(),
                            false,
                            "c61_reduz = {$conta->c61_reduz} and c61_instit in ({$this->getInstituicao()})"
                        );
                        db_fim_transacao(true);
                        @$saldo_anterior = pg_result($r_anterior, 0, "saldo_anterior");
                        @$sinal_anterior = pg_result($r_anterior, 0, "sinal_anterior");
                        $this->writeSaldoAnterior($sinal_anterior, $saldo_anterior);
                        //-----------------------------
                        //---- totalizadores do movimento
                        $tot_mov_debito = 0;
                        $tot_mov_credito = 0;

                        $sinal_dia = $sinal_anterior;
                        $saldo_dia = $saldo_anterior;
                    }


                    if ($repete_colunas == true) {
                        $repete_colunas = false;
                        $this->writeColunas();
                    }
                    $iCor = $iCor == 0 ? 1 : 0;
                    $this->pdf->Cell(20, self::TAMANHO, "$conta->c69_codlan", 0, 0, "L", $iCor);
                    $this->pdf->Cell(20, self::TAMANHO, "$conta->c69_sequen", 0, 0, "L", $iCor);
                    $this->pdf->Cell(20, self::TAMANHO, db_formatar($conta->c69_data, 'd'), 0, 0, "C", $iCor);
                    $this->pdf->Cell(20, self::TAMANHO, "$conta->c74_codrec", '0', 0, "L", $iCor);
                    $this->pdf->Cell(20, self::TAMANHO, "$conta->c73_coddot", '0', 0, "L", $iCor);

                    $sNumeroEmpenho = "{$conta->e60_codemp} / {$conta->e60_anousu}";

                    if (empty($conta->e60_codemp)) {
                        $sNumeroEmpenho = "";
                    }

                    $this->pdf->Cell(20, self::TAMANHO, $sNumeroEmpenho, '0', 0, "L", $iCor);
                    $this->pdf->Cell(23, self::TAMANHO, "{$conta->c79_codsup}", '0', 0, "L", $iCor);
                    $this->pdf->Cell(90, self::TAMANHO, "{$conta->c53_coddoc}-{$conta->c53_descr}", 0, 0, "L", $iCor);

                    if ($conta->tipo == "C") {
                        $this->pdf->Cell(
                            20,
                            self::TAMANHO,
                            "",
                            0,
                            0,
                            "R",
                            $iCor
                        ); // imprime esse espação no lugar do debito
                        $this->pdf->Cell(20, self::TAMANHO, db_formatar($conta->c69_valor, 'f'), 0, 0, "R", $iCor);
                    } else {
                        $this->pdf->Cell(20, self::TAMANHO, db_formatar($conta->c69_valor, 'f'), 0, 0, "R", $iCor);
                        $this->pdf->Cell(
                            20,
                            self::TAMANHO,
                            "",
                            0,
                            0,
                            "R",
                            $iCor
                        );
                        // imprime esse espação no lugar do credito
                    }

                    // -- totalizadores do movimento -------------
                    if ($conta->tipo == "D") {
                        $tot_mov_debito += $conta->c69_valor;
                        $total_dia_debito += $conta->c69_valor;
                    } else {
                        $tot_mov_credito += $conta->c69_valor;
                        $total_dia_credito += $conta->c69_valor;
                    }
                    //--------------   //   ----------------------


                    $this->pdf->ln();
                    $this->pdf->Cell(40, self::TAMANHO, "", 0, 0, "L", $iCor);
                    $this->pdf->Cell(
                        30,
                        self::TAMANHO,
                        "CONTRAPARTIDA :",
                        0,
                        0,
                        "L",
                        $iCor
                    ); // imprime esse espação no lugar do debito
                    if ($conta->c61_reduz == $conta->c69_debito) {
                        $this->pdf->Cell(
                            203,
                            self::TAMANHO,
                            "{$conta->credito_estrut} -  {$conta->credito_descr}  ($conta->c69_credito)",
                            0,
                            1,
                            "L",
                            $iCor
                        );
                    } else {
                        $this->pdf->Cell(
                            203,
                            self::TAMANHO,
                            "{$conta->debito_estrut} -  {$conta->debito_descr} ($conta->c69_debito)",
                            0,
                            1,
                            "L",
                            $iCor
                        );
                    }

                    if ($this->getRelatorio() == "a") {
                        $txt = "";
                        if ($conta->c75_numemp != "") {
                            $txt = $conta->e60_resumo;
                        }

                        if (!empty($conta->z01_numcgm)) {
                            $txt = " CGM: $conta->z01_numcgm : $conta->z01_nome, " . $txt;
                        }

                        if (!empty($conta->slip)) {
                            $this->pdf->Cell(40, self::TAMANHO, "", 0, 0, "L", $iCor);
                            $this->pdf->Cell(
                                233,
                                self::TAMANHO,
                                "SLIP:  {$conta->slip}",
                                0,
                                1,
                                "L",
                                $iCor
                            );
                        }

                        if (!empty($conta->codigo_movimento)) {
                            $this->pdf->Cell(40, self::TAMANHO, "", 0, 0, "L", $iCor);
                            $this->pdf->Cell(233, self::TAMANHO, "OP:  {$conta->codigo_movimento}", 0, 1, "L", $iCor);
                        }

                        $contaBuscar = $conta->c69_credito;
                        $naturezaContaBuscar = 'C';
                        if ($conta_atual == $conta->c69_debito) {
                            $contaBuscar = $conta->c69_debito;
                            $naturezaContaBuscar = 'D';
                        }

                        $sqlRecurso = "
                        select *
                          from contabilidade.recurso_exercicio
                         where o15_codigo = contabilidade.fc_recurso_conta_lancamento(
                                $conta->c69_codlan, $contaBuscar, '$naturezaContaBuscar'
                            )
                           and exercicio = {$this->getAnoUsu()}
                        ";

                        $rs = db_query($sqlRecurso);
                        if ($rs && pg_num_rows($rs) == 1) {
                            $recurso = \db_utils::fieldsMemory($rs, 0);
                            $msg = sprintf(
                                'RECURSO: Siconfi %s, Gestão %s, Subrecurso %s, Complemento %s',
                                $recurso->codigo_siconfi,
                                $recurso->gestao,
                                $recurso->o15_recurso,
                                $recurso->o15_complemento
                            );
                            $this->pdf->Cell(40, self::TAMANHO, "", 0, 0, "L", $iCor);
                            $this->pdf->Cell(233, self::TAMANHO, $msg, 0, 1, "L", $iCor);
                        }

                        $sHistorico = "HISTORICO: {$conta->c50_descr} {$conta->c72_complem} {$txt}";
                        $nMulticellHeight = $this->pdf->getMultiCellHeight(233, self::TAMANHO, $sHistorico);
                        if (!empty($conta->planilha)) {
                            $this->pdf->Cell(40, self::TAMANHO, "", 0, 0, "L", $iCor);
                            $this->pdf->Cell(233, self::TAMANHO, "PLANILHA: {$conta->planilha}", 0, 1, "L", $iCor);
                        }

                        $this->pdf->Cell(40, $nMulticellHeight, "", 0, 0, "L", $iCor);
                        $this->pdf->multicell(233, self::TAMANHO, $sHistorico, 0, 1, $iCor);
                    }
                }

                if ($this->getSaldoPorDia()) {
                    $this->writeMovimentacaoDoDia($total_dia_debito, $total_dia_credito, true);
                }
                // --- calcula saldo final ---

                if ($sinal_dia == "D") {
                    $total_dia_debito += $saldo_dia;
                } else {
                    $total_dia_credito += $saldo_dia;
                }

                $sinal_dia = $this->retornaSinal($total_dia_debito, $total_dia_credito);
                if ($this->getSaldoPorDia()) {
                    $this->writeSaldoDoDia($total_dia_debito, $total_dia_credito, $sinal_dia);
                }

                $this->writeTotalMovimentacoes($tot_mov_debito, $tot_mov_credito);
                // --- calcula saldo final ---
                if ($saldo_anterior != "") {
                    if ($sinal_anterior == "D") {
                        $tot_mov_debito += $saldo_anterior;
                    } else {
                        $tot_mov_credito += $saldo_anterior;
                    }
                }

                $sinal_final = $this->retornaSinal($tot_mov_debito, $tot_mov_credito);
                $this->writeSaldoFinalLancamento(
                    $this->calculaSaldo($tot_mov_debito, $tot_mov_credito),
                    $sinal_final
                );
            } else {
                db_inicio_transacao();
                $r_anterior = db_planocontassaldo_matriz(
                    $this->getAnoUsu(),
                    $this->getDataInicial(),
                    $this->getDataFinal(),
                    false,
                    "c61_reduz = {$dado->c61_reduz} and c61_instit in ({$this->getInstituicao()})"
                );
                db_fim_transacao(true);
                if ($this->getContasSemMovimento()) {
                    $saldo_anterior = @pg_result($r_anterior, 0, "saldo_anterior");
                    $sinal_anterior = @pg_result($r_anterior, 0, "sinal_anterior");
                    $this->writeCabecalhoConta(
                        $dado->c61_reduz,
                        $dado->c60_estrut,
                        $dado->c60_descr,
                        "{$dado->codigo} - {$dado->nomeinst}"
                    );
                    $this->writeSaldoAnterior($sinal_anterior, $saldo_anterior, true);
                    $this->writeColunas();
                    $this->pdf->Ln();
                    // saldo final
                    $this->writeSaldoFinal($saldo_anterior, $sinal_anterior);
                }
            }
        }
    }

    public function run()
    {
        $this->writeHeader();
        $this->writeBody();
        $this->pdf->OutPut("tmp/razao_por_conta_" . date('dmYHis') . ".pdf");
    }
}
