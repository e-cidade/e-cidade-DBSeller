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

use ECidade\Financeiro\Contabilidade\ContaCorrente\Consulta\Formatter\Json;
use ECidade\Financeiro\Contabilidade\ContaCorrente\Model\ContaCorrente;
use ECidade\Financeiro\Contabilidade\ContaCorrente\Repository\ContaCorrente as ContaCorrenteRepository;

require_once(modification("fpdf151/pdf.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("fpdf151/assinatura.php"));
require_once(modification("libs/db_libcontabilidade.php"));

$classinatura = new cl_assinatura;

$tipo = "A";
$agrupa_estrutural = 1;
$conta = 'S';
$movimento = 'S';
parse_str($_SERVER['QUERY_STRING']);

$stringFiltro = str_replace("\\", "", $_GET["filtros_conta_corrente"]);
$filtros = json_decode($stringFiltro);
$filtroPadrao = clone $filtros;
$agrupa_estrutural = ($agrupa_estrutural == '1' ? false : true);
$anousu = db_getsession("DB_anousu");

$xinstit = explode(",", $db_selinstit);
$resultinst = db_query("
  select codigo, nomeinst, nomeinstabrev, (select count(*) from db_config) as total_instituicao
    from db_config where codigo in ({$db_selinstit})
");
$descr_inst = '';
$xvirg = '';
$flag_abrev = false;
$numero_instit = pg_num_rows($resultinst);

$totalInstituicao = 0;
if ($numero_instit > 0) {
    $totalInstituicao = db_utils::fieldsMemory($resultinst, 0)->total_instituicao;
}
$rsDesabilitarAuditoria = db_query("SELECT fc_putsession('__disable_audit__', 'on');");
if ($totalInstituicao == $numero_instit) {
    $descr_inst = "CONSOLIDAÇÃO GERAL";
} else {
    for ($xins = 0; $xins < pg_num_rows($resultinst); $xins++) {
        db_fieldsmemory($resultinst, $xins);
        if (strlen(trim($nomeinstabrev)) > 0) {
            $descr_inst .= $xvirg . "($codigo)" . $nomeinstabrev;
            $flag_abrev = true;
        } else {
            $descr_inst .= $xvirg . "($codigo)" . $nomeinst;
        }
        $xvirg = ', ';
    }
}

if ($encerramento == 's')
    $encerramento = 'true';
else
    $encerramento = 'false';


$head2 = "BALANCETE DE VERIFICAÇÃO";
$head3 = "EXERCÍCIO " . db_getsession("DB_anousu");
$head4 = "PERÍODO : " . db_formatar($perini, 'd') . " A " . db_formatar($perfin, 'd');

if ($movimento == "S")
    $xmov = "Somente com Movimento";
else
    $xmov = "Todas";

if ($tipo == "S")
    $head5 = "SINTÉTICO - " . $xmov;
else
    $head5 = "ANALÍTICO - " . $xmov;

if ($flag_abrev == false) {
    if (strlen($descr_inst) > 42) {
        $descr_inst = substr($descr_inst, 0, 100);
    }
}

$head6 = "INSTITUIÇÕES : " . $descr_inst;

$where = " c61_instit in ({$db_selinstit})";

if (!empty($recurso)) {

    $recurso = preg_replace("/[^0-9\,]/", '', $recurso);
    $where .= " and c61_codigo in ({$recurso}) ";
}

if (USE_PCASP) {

    if ($sistema_contas !== "") {
        $where .= " and c60_consistemaconta = $sistema_contas";
    }
    if ($indicador_superavit !== "") {
        $where .= " and c60_identificadorfinanceiro = '$indicador_superavit'";
    }
} else {
    if ($sistema_contas != "T") {
        $where .= " and c52_descrred = '$sistema_contas'";
    }
}
if (!empty($filtroPadrao->conta_corrente)) {

    $sqlContasCorrentes = " and exists (select c120_conplanosistema";
    $sqlContasCorrentes .= "  from contabilidade.conplanoatributos ";
    $sqlContasCorrentes .= "       inner join contabilidade.conplanoreduz cp on cp.c61_codcon = c120_conplano ";
    $sqlContasCorrentes .= "                                               and  cp.c61_anousu = c120_anousu ";
    $sqlContasCorrentes .= "       inner join contabilidade.conplanosistema on c120_conplanosistema = c122_sequencial ";
    $sqlContasCorrentes .= " where cp.c61_reduz = r.c61_reduz and cp.c61_anousu = r.c61_anousu";
    $sqlContasCorrentes .= "     and c122_tipo = 2 ";
    $sqlContasCorrentes .= "     and c122_sequencial = {$filtroPadrao->conta_corrente}) ";

    $where .= $sqlContasCorrentes;
}

if ($estrut_inicial != '') {

    $aEstrutural = explode(",", $estrut_inicial);
    $aWhereEstrutural = array();
    foreach ($aEstrutural as $sEstrutural) {
        $sEstrutural = trim($sEstrutural);
        if (empty($sEstrutural)) {
            continue;
        }
        $aWhereEstrutural[] = " p.c60_estrut like '{$sEstrutural}%' ";
    }
    if ($aWhereEstrutural) {
        $where .= " and (" . implode(" OR ", $aWhereEstrutural) . ") ";
    }
}


$consultaBalancete = db_planocontassaldo_matriz(db_getsession("DB_anousu"), $perini, $perfin, true, $where, '', $agrupa_estrutural, $encerramento);


if ($tipo == 'S') {
    $consultaBalancete = "select * from ({$consultaBalancete})  as x where fc_nivel_plano2005(estrutural) <= 6";
}
$result = db_query($consultaBalancete);
$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$pdf->setfont('arial', 'b', 6);
$alt = 4;
$pagina = 1;
$maislinha = 0;
$total_anterior = 0;
$total_debitos = 0;
$total_creditos = 0;
$total_final = 0;
$iAjustePcasp = 0;

if (USE_PCASP) {
    $iAjustePcasp = 7;
}

$ultimoEstrutural = '';
for ($x = 0; $x < pg_num_rows($result); $x++) {
    db_fieldsmemory($result, $x);
    if (($tipo == "S") && ($c61_reduz != 0)) {
        continue;
    }

    if (USE_PCASP) {
    } else {
        if (substr($estrutural, 0, 1) == '3') {
            if (substr($estrutural, 2) + 0 > 0)
                continue;
        }
        if (substr($estrutural, 0, 1) == '4') {
            if (substr($estrutural, 2) + 0 > 0)
                continue;
        }
    }

    if (($movimento == "S") && (($saldo_anterior + $saldo_anterior_debito + $saldo_anterior_credito) == 0)) {
        continue;
    }

    if (($pdf->gety() > $pdf->h - 32) || $pagina == 1) {
        $pagina = 0;
        $pdf->addpage('L');
        $pdf->setfont('arial', 'b', 6);
        $pdf->cell(28, $alt, 'ESTRUTURAL', "B", 0, "C", 0);
        if ($numero_instit > 1 && $agrupa_estrutural == true) {
            $pdf->cell(10, $alt, '', "B", 0, "C", 0);
            $pdf->cell(10, $alt, '', "B", 0, "C", 0);
        } else {
            $pdf->cell(10, $alt, 'REDUZ', "B", 0, "C", 0);
            $pdf->cell(10, $alt, 'INST', "B", 0, "C", 0);
        }
        $pdf->cell((125 - $iAjustePcasp), $alt, 'DESCRIÇÃO DA CONTA', "B", 0, "C", 0);
        $pdf->cell(19, $alt, 'REC', "B", 0, "C", 0);

        if (USE_PCASP) {
            $pdf->cell($iAjustePcasp, $alt, 'ISF', "B", 0, "C", 0);
        }
        $pdf->cell(24, $alt, 'SALDO ANTERIOR', "B", 0, "R", 0);
        $pdf->cell(22, $alt, 'DÉBITOS', "B", 0, "R", 0);
        $pdf->cell(22, $alt, 'CRÉDITOS', "B", 0, "R", 0);
        $pdf->cell(24, $alt, 'SALDO', "B", 1, "R", 0);
        $pdf->ln(3);
    }
    $espaco = '';
    $maislinha = 0;
    if (substr($estrutural, 1, 14) == '00000000000000') {
        $espaco = "";
        $maislinha = 1;
        if ($sinal_anterior == "C")
            $total_anterior -= $saldo_anterior;
        else
            $total_anterior += $saldo_anterior;

        if ($sinal_final == "C")
            $total_final -= $saldo_final;
        else
            $total_final += $saldo_final;

        $total_debitos += $saldo_anterior_debito;
        $total_creditos += $saldo_anterior_credito;
    } elseif (substr($estrutural, 2, 13) == '0000000000000') {
        $espaco = "  ";
        $maislinha = 1;
    } elseif (substr($estrutural, 3, 12) == '000000000000') {
        $espaco = "    ";
        $maislinha = 1;
    } elseif (substr($estrutural, 4, 11) == '00000000000') {
        $espaco = "      ";
    } elseif (substr($estrutural, 5, 10) == '0000000000') {
        $espaco = "        ";
    } elseif (substr($estrutural, 7, 8) == '00000000') {
        $espaco = "          ";
    } elseif (substr($estrutural, 9, 6) == '000000') {
        $espaco = "            ";
    } elseif (substr($estrutural, 11, 4) == '0000') {
        $espaco = "              ";
    }
    if ($maislinha == 1) {
        $pdf->ln(1);
    }

    $resconta = db_query("select conplanoconta.*
                          from conplanoconta
                         where c63_codcon = {$c61_codcon}
                           and c63_reduz = {$c61_reduz}
                           and c63_anousu = {$anousu} ");

    if (pg_num_rows($resconta) > 0)
        db_fieldsmemory($resconta, 0);
    if ($c61_reduz != 0) {
        $pdf->setfont('arial', '', 6);
    } else {
        $pdf->setfont('arial', 'B', 6);
    }

    $estruturalFormatado = db_formatar($estrutural, 'receita');
    if ($estrutural === $ultimoEstrutural) {
        $estruturalFormatado = '';
    }
    $pdf->cell(28, $alt, $estruturalFormatado, 0, 0, "L", 0, 0);
    if ($numero_instit > 1 && $agrupa_estrutural == true) {
        $pdf->cell(10, $alt, "", 0, 0, "C", 0, 0);
        $pdf->cell(10, $alt, "", 0, 0, "C", 0, 0);
    } else {
        $pdf->cell(10, $alt, ($c61_reduz == 0 ? '' : $c61_reduz), 0, 0, "C", 0, 0);
        $pdf->cell(10, $alt, ($c61_reduz == 0 ? '' : $c61_instit), 0, 0, "C", 0, 0);
    }
    if ($conta == 'S') {
        $pdf->cell((125 - $iAjustePcasp), $alt, (pg_num_rows($resconta) == 0 ? $espaco . $c60_descr : $espaco . $c60_descr . '   ( Bco: ' . $c63_banco . '  Ag: ' . $c63_agencia . '  Cta: ' . $c63_conta . ')'), 0, 0, "L", 0, 0, '.');
    } else {
        $pdf->cell((125 - $iAjustePcasp), $alt, $espaco . $c60_descr, 0, 0, "L", 0, 0, '.');
    }

    $recurso = '';
    if ($c61_codigo != 0) {
        $recurso = descricaoCompletaRecurso($c61_codigo, $anousu, 4);
    }
    $pdf->cell(19, $alt, $recurso, 0, 0, "C", 0);
    if (USE_PCASP) {
        $pdf->cell($iAjustePcasp, $alt, $isf, 0, 0, "C", 0);
    }

    $pdf->SetFontSize(5.5);

    $pdf->cell(22, $alt, db_formatar($saldo_anterior, 'f'), 0, 0, "R", 0);
    $pdf->cell(2, $alt, $sinal_anterior, 0, 0, "R", 0);
    $pdf->cell(22, $alt, db_formatar($saldo_anterior_debito, 'f'), 0, 0, "R", 0);
    $pdf->cell(22, $alt, db_formatar($saldo_anterior_credito, 'f'), 0, 0, "R", 0);
    $pdf->cell(22, $alt, db_formatar($saldo_final, 'f'), 0, 0, "R", 0);
    $pdf->cell(2, $alt, $sinal_final, 0, 1, "R", 0);

    if ($c61_reduz != 0) {

        if ($sinal_final == "C") {
            $saldo_final = $saldo_final * -1;
        }
    }

    if ($c61_reduz != 0) {

        if ($sinal_anterior == "D") {
            $saldo_anterior = $saldo_anterior * -1;
        }

        if ($sinal_final == "D") {
            $saldo_final = $saldo_final * -1;
        }
    }

    $pdf->SetFontSize(7);
    $filtrosColunas = $filtroPadrao->colunas;
    if (!empty($c61_reduz)) {

        $contasCorrente = getContasCorrentesDaConta($c61_reduz, $anousu);
        foreach ($contasCorrente as $contaCorrente) {

            if (!empty($filtroPadrao->conta_corrente) && $filtroPadrao->conta_corrente !== $contaCorrente->getCodigo()) {
                continue;
            }


            $estruturalFormatado = '';
            $pdf->setfont('arial', 'b', 10);
            $pdf->cell(30, $alt, '', 0, 0, "L");
            $pdf->cell(253, $alt, $contaCorrente->getNome(), "B", 1, "L");
            $colunas = $filtrosColunas;
            if (empty($filtrosColunas)) {

                foreach ($contaCorrente->getAtributos() as $atributo) {
                    $colunas[] = $atributo->getSigla();
                }
            }
            $filtros->colunas = $colunas;
            $dadosContaCorrente = getDadosContaCorrente($contaCorrente, $perini, $perfin, $c61_reduz, $filtros, $c61_instit);
            if (empty($dadosContaCorrente)) {
                continue;
            }

            foreach ($dadosContaCorrente->registros as $registro) {

                $totalSaldoInicial = 0;
                $totalDebito = 0;
                $totalCredito = 0;
                $totalSaldoFinal = 0;
                $movimentacoes = $registro->movimentacoes;
                $pdf->setfont('arial', '', 7);

                foreach ($movimentacoes as $movimentacao) {
                    if (($pdf->gety() > $pdf->h - 32) || $pagina == 1) {
                        $pagina = 0;
                        $pdf->addpage('L');
                        $pdf->setfont('arial', 'b', 7);
                        $pdf->cell(28, $alt, 'ESTRUTURAL', "B", 0, "C", 0);
                        if ($numero_instit > 1 && $agrupa_estrutural == true) {
                            $pdf->cell(10, $alt, '', "B", 0, "C", 0);
                            $pdf->cell(10, $alt, '', "B", 0, "C", 0);
                        } else {
                            $pdf->cell(10, $alt, 'REDUZ', "B", 0, "C", 0);
                            $pdf->cell(10, $alt, 'INST', "B", 0, "C", 0);
                        }
                        $pdf->cell((125 - $iAjustePcasp), $alt, 'DESCRIÇÃO DA CONTA', "B", 0, "C", 0);
                        $pdf->cell(10, $alt, 'REC', "B", 0, "C", 0);
                        $pdf->cell(8, $alt, 'SIS', "B", 0, "C", 0);
                        if (USE_PCASP) {
                            $pdf->cell($iAjustePcasp, $alt, 'ISF', "B", 0, "C", 0);
                        }
                        $pdf->cell(24, $alt, 'SALDO ANTERIOR', "B", 0, "R", 0);
                        $pdf->cell(22, $alt, 'DÉBITOS', "B", 0, "R", 0);
                        $pdf->cell(22, $alt, 'CRÉDITOS', "B", 0, "R", 0);
                        $pdf->cell(24, $alt, 'SALDO', "B", 1, "R", 0);
                        $pdf->ln(3);
                    }

                    $pdf->cell(30, $alt, '', 0, 0, "L");
                    $pdf->setfont('arial', '', 7);
                    $pdf->cell(161, $alt, $movimentacao->conta_corrente, 0, 0, "L", 0, 0, '.');
                    $pdf->SetFontSize(5.5);
                    $pdf->cell(22, $alt, db_formatar($movimentacao->saldo_anterior, 'f'), 0, 0, "R", 0);
                    $pdf->cell(2, $alt, $movimentacao->natureza_anterior, 0, 0, "R", 0);
                    $pdf->cell(22, $alt, db_formatar($movimentacao->valor_debito, 'f'), 0, 0, "R", 0);
                    $pdf->cell(22, $alt, db_formatar($movimentacao->valor_credito, 'f'), 0, 0, "R", 0);
                    $pdf->cell(22, $alt, db_formatar($movimentacao->saldo_final, 'f'), 0, 0, "R", 0);
                    $pdf->cell(2, $alt, $movimentacao->natureza_final, 0, 1, "R", 0);

                    $totalSaldoInicial += ($movimentacao->natureza_anterior == 'D' ? $movimentacao->saldo_anterior * -1 : $movimentacao->saldo_anterior);
                    $totalSaldoFinal += ($movimentacao->natureza_final == 'D' ? $movimentacao->saldo_final * -1 : $movimentacao->saldo_final);
                    $totalDebito += $movimentacao->valor_debito;
                    $totalCredito += $movimentacao->valor_credito;

                }
                $pdf->setfont('arial', 'B', 10);
                $pdf->cell(30, $alt, "", 0, 0, "L");
                $pdf->cell(161, $alt, "Total {$contaCorrente->getNome()}", 0, 0, "L");
                $pdf->SetFontSize(5.5);
                $pdf->cell(22, $alt, db_formatar(abs($totalSaldoInicial), 'f'), 0, 0, "R", 0);
                $pdf->cell(2, $alt, ($totalSaldoInicial < 0 ? "D" : "C"), 0, 0, "R", 0);
                $pdf->cell(22, $alt, db_formatar($totalDebito, 'f'), 0, 0, "R", 0);
                $pdf->cell(22, $alt, db_formatar($totalCredito, 'f'), 0, 0, "R", 0);
                $pdf->cell(22, $alt, db_formatar(abs($totalSaldoFinal), 'f'), 0, 0, "R", 0);
                $pdf->cell(2, $alt, ($totalSaldoFinal < 0 ? "D" : "C"), 0, 1, "R", 0);

            }
        }
    }
    $pdf->SetFontSize(7);
    $ultimoEstrutural = $estrutural;
}

$pdf->setfont('arial', 'b', 7);
$pdf->ln(2);
$pdf->cell(25, $alt, '', 0, 0, "L", 0, 0, '.');
$pdf->cell(10, $alt, '', 0, 0, "L", 0, 0, '.');
$pdf->cell(130, $alt, 'T O T A L   G E R A L ', 0, 0, "L", 0, 0, '.');
$pdf->cell(26, $alt, '', 0, 0, "R", 0);
//echo $total_anterior."<br>";
if ($total_anterior < 0) {
    $sinal = "C";
    //$total_anterior *= -1;
} else {
    $sinal = "D";
}
$pdf->SetFontSize(5.5);
$pdf->cell(22, $alt, db_formatar(($total_anterior * -1), 'f'), 0, 0, "R", 0);
$pdf->cell(2, $alt, $sinal, 0, 0, "R", 0);
$pdf->cell(22, $alt, db_formatar($total_debitos, 'f'), 0, 0, "R", 0);
$pdf->cell(22, $alt, db_formatar($total_creditos, 'f'), 0, 0, "R", 0);
$total_final = $total_anterior + $total_debitos - $total_creditos;
if ($total_final < 0) {
    $sinal = "C";
    $total_final *= -1;
} else {
    $sinal = "D";
}
$pdf->cell(22, $alt, db_formatar($total_final, 'f'), 0, 0, "R", 0);
$pdf->SetFontSize(7);
$pdf->cell(2, $alt, $sinal, 0, 1, "R", 0);


if ($pdf->gety() > ($pdf->h - 40))
    $pdf->addpage("L");

$tes = "______________________________" . "\n" . "Tesoureiro";
$sec = "______________________________" . "\n" . "Secretaria da Fazenda";
$cont = "______________________________" . "\n" . "Contador";
$pref = "______________________________" . "\n" . "Prefeito";
$ass_pref = $classinatura->assinatura(1000, $pref);
$ass_sec = $classinatura->assinatura(1002, $sec);
$ass_tes = $classinatura->assinatura(1004, $tes);
$ass_cont = $classinatura->assinatura(1005, $cont);

//echo $ass_pref;
$largura = ($pdf->w) / 2;
$pdf->ln(10);
$pos = $pdf->gety();
$pdf->multicell($largura, 3, $ass_pref, 0, "C", 0, 0);
$pdf->setxy($largura, $pos);
$pdf->multicell($largura, 3, $ass_cont, 0, "C", 0, 0);

$pdf->Output();

/**
 * Retorna todas as contas correntes do reduzido no ano
 * @param $reduzido
 * @param $ano
 * @return ContaCorrente[]
 */
function getContasCorrentesDaConta($reduzido, $ano)
{


    $sqlContasCorrentes = "select distinct c120_conplanosistema";
    $sqlContasCorrentes .= "  from contabilidade.conplanoatributos ";
    $sqlContasCorrentes .= "       inner join  contabilidade.conplanoreduz on c61_codcon = c120_conplano ";
    $sqlContasCorrentes .= "                                              and c61_anousu = c120_anousu ";
    $sqlContasCorrentes .= "       inner join  contabilidade.conplanosistema on c120_conplanosistema = c122_sequencial ";
    $sqlContasCorrentes .= " where c61_reduz = {$reduzido} and c61_anousu = {$ano}";
    $sqlContasCorrentes .= "     and c122_tipo = 2";
    $rsContaCorrentes = db_query($sqlContasCorrentes);
    $contasCorrentes = db_utils::makeCollectionFromRecord($rsContaCorrentes, function ($dados) {

        return ContaCorrenteRepository::getByCodigo($dados->c120_conplanosistema);
    });
    return $contasCorrentes;
}


/**
 * @param ContaCorrente $contaCorrente
 * @param $dataInicial
 * @param $dataFinal
 * @param $reduzido
 * @param $filtros
 * @param $instituicao
 * @return false|mixed
 * @throws Exception
 */
function getDadosContaCorrente(ContaCorrente $contaCorrente, $dataInicial, $dataFinal, $reduzido, $filtros, $instituicao)
{
    $datainicial = new \DateTime($dataInicial);
    $dataFinal = new \DateTime($dataFinal);
    $param = new \stdClass();
    $param->filtros = new \stdClass();
    $param->filtros->estrutural = '';
    $param->filtros->contas = array($reduzido);
    $param->filtros->atributos = $filtros->atributos;
    $param->filtros->documentos = array();
    $colunas = $filtros->colunas;
    $formatter = new Json();
    $instituicao = InstituicaoRepository::getInstituicaoByCodigo($instituicao);
    $processamento = new ECidade\Financeiro\Contabilidade\ContaCorrente\Consulta\Processamento\Processamento($instituicao, $datainicial, $dataFinal, $contaCorrente);
    $processamento->setAgruparPorDocumentoContabil(count($param->filtros->documentos) > 0);
    $consulta = new ECidade\Financeiro\Contabilidade\ContaCorrente\Consulta\Consulta($formatter, $processamento);
    $consulta->setFiltros($param->filtros);
    $consulta->setColunas($colunas);
    try {
        $dados = $consulta->emitir();
    } catch (\Exception $e) {
        $dados = false;
    }
    return $dados;
}
