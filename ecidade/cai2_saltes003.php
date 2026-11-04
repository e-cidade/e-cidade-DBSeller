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

use ECidade\Financeiro\Tesouraria\SaldoTesourariaHelper;
use \ECidade\Pdf\Pdf;

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");


parse_str($_SERVER["QUERY_STRING"]);

$anousu = db_getsession("DB_anousu");
$instit = db_getsession("DB_instit");

$pdf = new Pdf('L'); // abre a classe
$pdf->addTitulo('Relatorio de Saldo da Tesouraria');
$pdf->addTitulo('');
$pdf->addTitulo("DATA: {$datai_dia}/{$datai_mes}/{$datai_ano}");
$pdf->addTitulo("TIPO:  $tipo");
$pdf->init(false);
$pdf->SetAutoPageBreak(false, 15);
$pdf->setFillColor(220);

$exercicio = db_getsession("DB_anousu");
$instituicao = db_getsession("DB_instit");
$dataSessao = date('Y-m-d', db_getsession("DB_datausu"));
$dataFiltroSaldo = $dataSessao;

if (!empty($_GET['datai_dia'])) {
    $dataFiltroSaldo = sprintf('%s-%s-%s', $_GET['datai_ano'], $_GET['datai_mes'], $_GET['datai_dia']);
}

$helper = new SaldoTesourariaHelper($instituicao, $exercicio, $dataSessao, $dataFiltroSaldo);
$helper->totalizaPorConta('k13_descr');
switch ($tipo) {
    case 'conta':
        $totais = imprimeTabelaConta($pdf, $helper->totalizaPorConta());
        break;
    case 'recurso':
        $totais = imprimeTabelaRecurso($pdf, $helper->totalizaContasPorRecurso());
        break;
    case 'recurso_conta':
        $totais = imprimeTabelaRecurso($pdf, $helper->totalizaContasPorRecurso(), true);
        break;
    case 'instituicao':
        $totais = imprimeTabelaBancos($pdf, $helper->totalizaPorBanco());
        break;
    case 'domicilio_bancario':
        $totais = imprimeTabelaDomicilioBancario($pdf, $helper->totalizaPorDomiciolioBancario());
        break;
}

$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', ''); // seta a fonte do relatorio
$pdf->Cell(179, 5, "Totais", "LRTB", 0, "R", 0);
$pdf->Cell(25, 5, db_formatar($totais->saldo_anterior, 'f'), "LRTB", 0, "R", 0);
$pdf->Cell(25, 5, db_formatar($totais->debitado, 'f'), "LRTB", 0, "R", 0);
$pdf->Cell(25, 5, db_formatar($totais->creditado, 'f'), "LRTB", 0, "R", 0);
$pdf->Cell(25, 5, db_formatar($totais->saldo_atual, 'f'), "LRTB", 0, "R", 0);

$pdf->Output();

function imprimeTabelaConta(Pdf $pdf, $porConta)
{
    $totais = (object)[
        "saldo_anterior" => 0,
        "debitado" => 0,
        "creditado" => 0,
        "saldo_atual" => 0,
    ];

    headerPorConta($pdf);
    foreach ($porConta as $conta) {
        if ($pdf->getAvailableHeight() < 5) {
            headerPorConta($pdf);
        }
        $pdf->cell(13, 5, $conta->k13_conta, 1, 0, 'C');
        $pdf->cellAdapt(8, 106, 5, $conta->k13_descr, 1, 0, 'L');
        $pdf->cell(20, 5, $conta->gestao, 1, 0, 'C');
        $pdf->cell(20, 5, $conta->o15_recurso, 1, 0, 'C');
        $pdf->cell(20, 5, $conta->o15_complemento, 1, 0, 'C');
        $pdf->cell(25, 5, db_formatar($conta->saldo_anterior, 'f'), 1, 0, 'R');
        $pdf->cell(25, 5, db_formatar($conta->debitado, 'f'), 1, 0, 'R');
        $pdf->cell(25, 5, db_formatar($conta->creditado, 'f'), 1, 0, 'R');
        $pdf->cell(25, 5, db_formatar($conta->saldo_atual, 'f'), 1, 1, 'R');

        $totais->saldo_anterior += $conta->saldo_anterior;
        $totais->debitado += $conta->debitado;
        $totais->creditado += $conta->creditado;
        $totais->saldo_atual += $conta->saldo_atual;
    }

    return $totais;
}

/**
 * @param Pdf $pdf
 * @return void
 */
function headerPorConta(Pdf $pdf)
{
    $pdf->addPage();
    $pdf->setFont('arial', 'b', '9');
    $pdf->cell(13, 5, 'Código', 1, 0, 'C', 1);
    $pdf->cell(106, 5, 'Descrição', 1, 0, 'C', 1);
    $pdf->cell(20, 5, 'Recurso', 1, 0, 'C', 1);
    $pdf->cell(20, 5, 'Subrecurso', 1, 0, 'C', 1);
    $pdf->cell(20, 5, 'Compl.', 1, 0, 'C', 1);
    $pdf->cell(25, 5, 'Saldo Ant.', 1, 0, 'C', 1);
    $pdf->cell(25, 5, 'Vlr. Debit.', 1, 0, 'C', 1);
    $pdf->cell(25, 5, 'Vlr. Cred.', 1, 0, 'C', 1);
    $pdf->cell(25, 5, 'Saldo Atual', 1, 1, 'C', 1);
    $pdf->setFont('arial', '', '8');
}

function imprimeTabelaRecurso(Pdf $pdf, $porRecursos, $imprimirContas = false)
{
    $totais = (object)[
        "saldo_anterior" => 0,
        "debitado" => 0,
        "creditado" => 0,
        "saldo_atual" => 0,
    ];

    headerPorRecurso($pdf);
    foreach ($porRecursos as $recurso) {
        if ($pdf->getAvailableHeight() < 8) {
            headerPorRecurso($pdf);
        }

        $y = $pdf->getY();
        $linhas = $pdf->nbLines(119, $recurso->descricao);
        $h = 5 * $linhas;


        $pdf->cell(20, $h, $recurso->gestao, 1, 0, 'C');
        $pdf->cell(20, $h, $recurso->o15_recurso, 1, 0, 'C');
        $pdf->cell(20, $h, $recurso->o15_complemento, 1, 0, 'C');
        $pdf->multiCell(119, 5, $recurso->descricao, 1, 'L');
        $pdf->setXY(189, $y);
        $pdf->cell(25, $h, db_formatar($recurso->saldo_anterior, 'f'), 1, 0, 'R');
        $pdf->cell(25, $h, db_formatar($recurso->debitado, 'f'), 1, 0, 'R');
        $pdf->cell(25, $h, db_formatar($recurso->creditado, 'f'), 1, 0, 'R');
        $pdf->cell(25, $h, db_formatar($recurso->saldo_atual, 'f'), 1, 1, 'R');

        $pinta = true;
        if ($imprimirContas) {
            foreach ($recurso->contas as $conta) {
                if ($pdf->getAvailableHeight() < 4) {
                    headerPorRecurso($pdf);
                }
                $pdf->setFontSize(6.5);
                $pdf->cell(20, 4, '', 1, 0, '', $pinta);
                $pdf->cell(159, 4, "({$conta->k13_conta}) - {$conta->k13_descr}", 1, 0, 'L', $pinta);
                $pdf->cell(25, 4, db_formatar($conta->saldo_anterior, 'f'), 1, 0, 'R', $pinta);
                $pdf->cell(25, 4, db_formatar($conta->debitado, 'f'), 1, 0, 'R', $pinta);
                $pdf->cell(25, 4, db_formatar($conta->creditado, 'f'), 1, 0, 'R', $pinta);
                $pdf->cell(25, 4, db_formatar($conta->saldo_atual, 'f'), 1, 1, 'R', $pinta);
                $pinta = !$pinta;
            }
            $pdf->setFontSize('8');
            $pdf->ln(2);
        }
        $totais->saldo_anterior += $recurso->saldo_anterior;
        $totais->debitado += $recurso->debitado;
        $totais->creditado += $recurso->creditado;
        $totais->saldo_atual += $recurso->saldo_atual;
    }

    return $totais;
}

function headerPorRecurso(Pdf $pdf)
{
    $pdf->setFillColor(220);
    $pdf->addPage();
    $pdf->setFont('arial', 'b', '9');
    $pdf->cell(20, 5, 'Recurso', 1, 0, 'C', 1);
    $pdf->cell(20, 5, 'Subrecurso', 1, 0, 'C', 1);
    $pdf->cell(20, 5, 'Compl.', 1, 0, 'C', 1);
    $pdf->cell(119, 5, 'Descrição', 1, 0, 'C', 1);
    $pdf->cell(25, 5, 'Saldo Ant.', 1, 0, 'C', 1);
    $pdf->cell(25, 5, 'Vlr. Debit.', 1, 0, 'C', 1);
    $pdf->cell(25, 5, 'Vlr. Cred.', 1, 0, 'C', 1);
    $pdf->cell(25, 5, 'Saldo Atual', 1, 1, 'C', 1);
    $pdf->setFont('arial', '', '8');
    $pdf->setFillColor(240);
}


function imprimeTabelaBancos(Pdf $pdf, $porBanco)
{
    $totais = (object)[
        "saldo_anterior" => 0,
        "debitado" => 0,
        "creditado" => 0,
        "saldo_atual" => 0,
    ];

    headerPorBancos($pdf);
    foreach ($porBanco as $banco) {
        if ($pdf->getAvailableHeight() < 5) {
            headerPorBancos($pdf);
        }

        $pdf->cell(13, 5, $banco->db90_codban, 1, 0, 'C');
        $pdf->cell(166, 5, $banco->db90_descr, 1, 0, 'L');
        $pdf->cell(25, 5, db_formatar($banco->saldo_anterior, 'f'), 1, 0, 'R');
        $pdf->cell(25, 5, db_formatar($banco->debitado, 'f'), 1, 0, 'R');
        $pdf->cell(25, 5, db_formatar($banco->creditado, 'f'), 1, 0, 'R');
        $pdf->cell(25, 5, db_formatar($banco->saldo_atual, 'f'), 1, 1, 'R');

        $pinta = true;
        foreach ($banco->contas as $conta) {
            if ($pdf->getAvailableHeight() < 5) {
                headerPorBancos($pdf);
            }

            $pdf->setFontSize(6.5);
            $pdf->cell(13, 5, '', 1, 0, 'C', $pinta);
            $pdf->cell(166, 5, "({$conta->k13_conta}) -  {$conta->k13_descr}", 1, 0, 'L', $pinta);
            $pdf->cell(25, 5, db_formatar($conta->saldo_anterior, 'f'), 1, 0, 'R', $pinta);
            $pdf->cell(25, 5, db_formatar($conta->debitado, 'f'), 1, 0, 'R', $pinta);
            $pdf->cell(25, 5, db_formatar($conta->creditado, 'f'), 1, 0, 'R', $pinta);
            $pdf->cell(25, 5, db_formatar($conta->saldo_atual, 'f'), 1, 1, 'R', $pinta);

            $pinta = !$pinta;
        }
        $pdf->setFontSize('8');
        $pdf->ln(2);
        $totais->saldo_anterior += $banco->saldo_anterior;
        $totais->debitado += $banco->debitado;
        $totais->creditado += $banco->creditado;
        $totais->saldo_atual += $banco->saldo_atual;
    }

    return $totais;
}

function headerPorBancos(Pdf $pdf, $domicilio = false)
{
    $pdf->setFillColor(220);

    $pdf->addPage();
    $pdf->setFont('arial', 'b', '9');
    if ($domicilio) {
        $pdf->cell(66, 5, 'Código', 1, 0, 'C', 1);
        $pdf->cell(113, 5, 'Banco', 1, 0, 'C', 1);
    } else {
        $pdf->cell(13, 5, 'Código', 1, 0, 'C', 1);
        $pdf->cell(166, 5, 'Banco', 1, 0, 'C', 1);
    }
    $pdf->cell(25, 5, 'Saldo Ant.', 1, 0, 'C', 1);
    $pdf->cell(25, 5, 'Vlr. Debit.', 1, 0, 'C', 1);
    $pdf->cell(25, 5, 'Vlr. Cred.', 1, 0, 'C', 1);
    $pdf->cell(25, 5, 'Saldo Atual', 1, 1, 'C', 1);
    $pdf->setFont('arial', '', '8');
    $pdf->setFillColor(240);
}

function imprimeTabelaDomicilioBancario($pdf, $porDomicilio)
{
    $totais = (object)[
        "saldo_anterior" => 0,
        "debitado" => 0,
        "creditado" => 0,
        "saldo_atual" => 0,
    ];

    headerPorBancos($pdf, true);

    foreach ($porDomicilio as $domicilio) {
        if ($pdf->getAvailableHeight() < 5) {
            headerPorBancos($pdf, true);
        }
        $pdf->cell(66, 5, $domicilio->bancoagencia, 1, 0, 'C');
        $pdf->cell(113, 5, '', 1, 0, 'C');
        $pdf->cell(25, 5, db_formatar($domicilio->saldo_anterior, 'f'), 1, 0, 'R');
        $pdf->cell(25, 5, db_formatar($domicilio->debitado, 'f'), 1, 0, 'R');
        $pdf->cell(25, 5, db_formatar($domicilio->creditado, 'f'), 1, 0, 'R');
        $pdf->cell(25, 5, db_formatar($domicilio->saldo_atual, 'f'), 1, 1, 'R');

        $pinta = true;
        foreach ($domicilio->contas as $conta) {
            if ($pdf->getAvailableHeight() < 5) {
                headerPorBancos($pdf);
            }

            $nome = sprintf(
                '(%s) - %s - %s - %s - %s',
                ($conta->k13_conta),
                $conta->k13_descr,
                $conta->gestao,
                $conta->o15_recurso,
                $conta->o15_complemento
            );
            $pdf->setFontSize(6.5);
            $pdf->cell(179, 5, $nome, 1, 0, 'L', $pinta);
            $pdf->cell(25, 5, db_formatar($conta->saldo_anterior, 'f'), 1, 0, 'R', $pinta);
            $pdf->cell(25, 5, db_formatar($conta->debitado, 'f'), 1, 0, 'R', $pinta);
            $pdf->cell(25, 5, db_formatar($conta->creditado, 'f'), 1, 0, 'R', $pinta);
            $pdf->cell(25, 5, db_formatar($conta->saldo_atual, 'f'), 1, 1, 'R', $pinta);

            $pinta = !$pinta;
        }
        $pdf->setFontSize('8');
        $pdf->ln(2);

        $totais->saldo_anterior += $domicilio->saldo_anterior;
        $totais->debitado += $domicilio->debitado;
        $totais->creditado += $domicilio->creditado;
        $totais->saldo_atual += $domicilio->saldo_atual;
    }

    return $totais;
}
