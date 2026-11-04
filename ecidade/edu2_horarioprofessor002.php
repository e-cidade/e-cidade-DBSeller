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

use ECidade\Pdf\Pdf;

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_utils.php");
require_once modification("classes/db_regenciahorario_classe.php");
require_once modification("classes/db_periodoescola_classe.php");
require_once modification("classes/db_diasemana_classe.php");
require_once modification("classes/db_turmaachorario_classe.php");

function calculaAlturaY($horaInicioAtual, $alturaCelulaHora, $alturaHoras)
{
    $beginHour = substr($horaInicioAtual, 0, 2) . '00';
    if (strlen($beginHour) == 4 && substr($beginHour, 0, 1) == 0) {
        $beginHour = substr($beginHour, 1, 3);
    }
    $beginMinutes = substr($horaInicioAtual, 3, 2);
    return $alturaHoras->hora[$beginHour] + (($alturaCelulaHora * $beginMinutes) / 60);
}

function calculaAlturaRect($horaInicioAtual, $horaFimAtual, $alturaCelulaHora)
{
    $horaInicial = strtotime($horaInicioAtual); // Segundos
    $horaFinal = strtotime($horaFimAtual); // Segundos

    $periodoTotalSeg = $horaFinal - $horaInicial; // Segundos
    $periodoTotalHoras = $periodoTotalSeg / 3600; // Horas

    return $periodoTotalHoras * $alturaCelulaHora;
}

function atendimentoSimult($clrechumanoescola, $rSimult, $codeEscola) {
    for ($i = 0; $i < $clrechumanoescola->numrows; $i++) {
        $dadosSimult[$i] = db_utils::fieldsmemory($rSimult, $i);
    }
    foreach ($dadosSimult as $codEscola) {
        $textoImpressao = "";
        if ($codEscola->ed75_i_escola == $codeEscola) {
            $textoImpressao  = "* Atendimento Simultâneo *\n";
            return $textoImpressao;
        }
    }
}

$clrechumanoescola = new cl_rechumanoescola;
$clregenciahorario = new cl_regenciahorario;
$clperiodoescola = new cl_periodoescola;
$cldiasemana = new cl_diasemana;
$clturmaachorario = new cl_turmaachorario;
$oDaoRecHumanoHoraDisp = new cl_rechumanohoradisp();

$escola = db_getsession("DB_coddepto");
if ($escolahorario != "") {
    $condicao = "AND ed17_i_escola = $escolahorario";
} else {
    $condicao = "";
}

$sCampos = "DISTINCT case when ed20_i_tiposervidor = 1 then cgmrh.z01_nome else ";
$sCampos .= "cgmcgm.z01_nome end as z01_nome,ed18_c_nome,ed18_i_codigo,ed17_i_turno, ed15_c_nome as turno, z04_nomesocial";
$dbwhere = "ed58_i_rechumano = $rechumano and ed58_ativo is true and ed52_i_ano = $anohorario $condicao";
$sql = $clregenciahorario->sql_query("", $sCampos, "", $dbwhere);
$result0 = db_query($sql);

$escolas = array(); // Criando um array que irá armazenar as escolas de $result0
$objEscola = new stdClass(); // Criando um objeto para armazenar o código e nome das escolas dentro do array $escolas

for ($x = 0; $x < pg_num_rows($result0); $x++) {
    db_fieldsmemory($result0, $x);

    $objEscola = new stdClass();
    $objEscola->ed18_i_codigo = $ed18_i_codigo;
    $objEscola->ed18_c_nome = $ed18_c_nome;

    $escolas[] = $objEscola;
}

/***** O objetivo aqui é salvar todas as escolas          *
 *     diferentes dentro da variável $stringTodasEscolas  *
 *                                                   *****/
$todasEscolas = array();
$codigosEscolas = array();
sort($escolas);
foreach ($escolas as $dadoEscola) {
    $codigo = $dadoEscola->ed18_i_codigo;
    $nome = $dadoEscola->ed18_c_nome;
    $codigoENome = "$codigo - $nome";

    if(!in_array($codigoENome, $todasEscolas)) {
        $codigosEscolas[] = $codigo;
        $todasEscolas[] = $codigoENome;
    }
}
$stringTodasEscolas = implode("\n", $todasEscolas);
$stringCodigosEscolas = implode(", ", $codigosEscolas);

$pdf = new Pdf();
$pdf->setfillcolor(223);
$pdf->addTitulo("RELATÓRIO DOS HORÁRIOS DO PROFESSOR");
$pdf->addTitulo("Professor: " . $z01_nome);
$head5 = "";
if (!empty(@$z04_nomesocial)) {
    $pdf->addTitulo("Nome Social: " . $z04_nomesocial);
    $pdf->addTitulo("Ano: " . $anohorario);
    $pdf->addTitulo(count($todasEscolas) > 1 ? "Escolas:\n" . $stringTodasEscolas : "Escola: " . $stringTodasEscolas);
} else {
    $pdf->addTitulo("Ano: " . $anohorario);
    $pdf->addTitulo(count($todasEscolas) > 1 ? "Escolas:\n" . $stringTodasEscolas : "Escola: " . $stringTodasEscolas);
}

$pdf->init(false);
$pdf->exibeHeader(true, 1);
$pdf->addPage();

$sCampos = "min(ed17_h_inicio) as menorhorario, max(ed17_h_fim) as maiorhorario";
$result_per = $clregenciahorario->sql_record($clregenciahorario->sql_query("", $sCampos, "", $dbwhere));
db_fieldsmemory($result_per, 0);

$hora1 = (int)substr($menorhorario, 0, 2);
$hora2 = (int)substr($maiorhorario, 0, 2) + 1;
$horainicial = $hora1 * 100;
$horafinal = $hora2 * 100;
$tempo_ini = mktime($hora1, 0, 0, date("m"), date("d"), date("Y"));
$tempo_fim = mktime($hora2, 0, 0, date("m"), date("d"), date("Y"));
$difer_minutos = ($tempo_fim - $tempo_ini) / 60;
$alt_tab_hora = $difer_minutos / 3;
$qtd_hora = $difer_minutos / 60;
$alturaCelulaHora = $alt_tab_hora / $qtd_hora;
$larg_tabela = 195;
$larg_coluna1 = 20;
$tabela1_top = $pdf->getY();
$tabela1_left = floor($pdf->getX());
$inicioGrade = $tabela1_left + $larg_coluna1;

/*****     Grade dias da semana
 * (SEG / TER / QUA / QUI / SEX / SAB) *****/
$result = $cldiasemana->sql_record($cldiasemana->sql_query_rh(
    "",
    "ed32_i_codigo,ed32_c_abrev,ed32_c_descr",
    "ed32_i_codigo",
    "ed04_i_escola in ($stringCodigosEscolas) AND ed04_c_letivo = 'S'"
));

$pdf->settextcolor(75, 75, 75);
$larg_dia = floor(($larg_tabela - $larg_coluna1) / $cldiasemana->numrows);
$pdf->cell($larg_coluna1, 5, "", 0, 0, "C", 0);
$pdf->setY($tabela1_top);
$pdf->setX($inicioGrade);
$pdf->setfont('arial', 'b', 7);

$iNumDias = $cldiasemana->numrows;
for ($x = 0; $x < $cldiasemana->numrows; $x++) {
    db_fieldsmemory($result, $x);
    $pdf->cell($larg_dia, 5, $ed32_c_descr, 1, 0, "C", 1);
}

/***** Grade de fundo *****/
$pdf->setdrawcolor(100, 100, 100);
$pdf->setY($tabela1_top + 7);
$alturaCelula = $alt_tab_hora / $qtd_hora;
for ($x = 0; $x <= $qtd_hora; $x++) {
    $pdf->setX($inicioGrade);
    $pdf->cell($larg_dia * $cldiasemana->numrows, $alturaCelula, "", 1, 1, "C", 0);
}
$fim_tabelafundo = $pdf->getY();

/***** Linhas verticais da grade de fundo *****/
$left_ini = $inicioGrade + $larg_dia;
for ($x = 0; $x < $cldiasemana->numrows - 1; $x++) {
    $pdf->line($left_ini, $tabela1_top + 7, $left_ini, $fim_tabelafundo);
    $left_ini += $larg_dia;
}

/***** Grade dos horários *****/
$pdf->setfont('arial', 'b', 7);
$pdf->setfillcolor(105, 105, 105);
$pdf->settextcolor(249, 249, 249);
$pdf->setdrawcolor(249, 249, 249);
$pdf->setX($tabela1_left);
$top_ini = $tabela1_top + 7;
$tt = 0;
$alturaHoras = new stdClass();

for ($t = $horainicial; $t <= $horafinal; $t++) {
    $pdf->setY($top_ini);
    $hora = strlen($t) == 3 ? "0" . $t : $t;
    $hora = substr($hora, 0, 2) . ":" . substr($hora, 2, 2);
    if ($hora <= $horafinal) {
        if ($t != 2400) {
            if (($t % 100) == 0) {
                $lastHora = (substr($hora, 0, 2) + 1) . ':00';
                $lastHora = strlen($lastHora) == 4 ? "0" . $lastHora : $lastHora;
                $stringHora = $hora . ' - ' . $lastHora;
                $pdf->cell($larg_coluna1 - 2, 20, $stringHora, 'B', 0, "C", 1);
                $alturaHoras->hora[$t] = $pdf->getY(); // Armazenando em um objeto a altura das horas (getY())
            }
        }

        $tt++;
        if ($tt == 60) {
            $t += 40;
            $tt = 0;
        }
    }
    $top_ini += 0.3325;
}

$pdf->setfont('arial', '', 6);
$pdf->settextcolor(75, 75, 75);
$pdf->setfillcolor(223);
$pdf->setdrawcolor(100, 100, 100);

/***** Horário do Docente *****/
$var_left = $inicioGrade; // 10 + 10 = 20

$aSimult = array();
$iIndSimult = -1;

$sWhereRecHumanoHoraDisp = "ed75_i_rechumano = {$rechumano} AND ed33_ativo is true";
$sSqlRecHumanoHoraDisp = $oDaoRecHumanoHoraDisp->sql_query(null, "ed75_i_codigo", null, $sWhereRecHumanoHoraDisp);
$rsRecHumanoHoraDisp = db_query($sSqlRecHumanoHoraDisp);
$lRecHumanoHoraDisp = pg_num_rows($rsRecHumanoHoraDisp) > 0;

if ($lRecHumanoHoraDisp) {
    $ini_top = $tabela1_top + 7;
    db_fieldsmemory($result, $x); // null
    $sCampos = "ed232_c_abrev, ed15_c_nome, ed58_i_codigo, ed17_h_inicio, ed17_h_fim, ed17_i_escola,
    ed57_c_descr, ed232_c_descr, ed08_c_descr, ed58_i_diasemana, ed04_c_letivo";
    $sWhere = " ed58_i_rechumano = $rechumano";
    $sWhere .= " and ed52_i_ano = $anohorario and ed58_ativo is true and ed04_c_letivo = 'S'";
    if(isset($escolahorario) && !empty($escolahorario)) {
        $sWhere .= " AND ed17_i_escola = $escolahorario";
    }
    $sOrder = "ed58_i_diasemana,ed17_h_inicio asc,ed17_h_fim asc,ed17_i_turno,escola.ed18_c_nome";
    $result1 = $clregenciahorario->sql_record($clregenciahorario->sql_query_grade_horario_professor(
        $sCampos,
        $sWhere,
        $sOrder
    ));

    $sCampos = "ed268_c_descr,ed20_i_codigo,ed270_i_codigo,ed08_c_descr,turno.ed15_c_nome";
    $sCampos .= ",ed17_h_inicio,ed17_h_fim,ed17_i_escola,ed18_i_codigo,ed18_c_nome,ed08_c_descr";
    $sOrder = "ed270_i_diasemana,ed17_h_inicio asc,ed17_h_fim asc";
    $sWhere = " ed270_i_rechumano = $rechumano AND ed52_i_ano = $anohorario ";
    $result3 = $clturmaachorario->sql_record($clturmaachorario->sql_query(
        "",
        $sCampos,
        $sOrder,
        $sWhere
    ));

    $sCampos = "*";
    $sWhere = " ed75_i_rechumano = $rechumano AND ed75_c_simultaneo = 'S'";
    $rSimult = $clrechumanoescola->sql_record($clrechumanoescola->sql_query_atendimento_simultaneo(
        $sCampos,
        $sWhere
    ));
    $isAtendSimult = $clrechumanoescola->numrows > 0;

    /* Manipulando os dados */
    if ($clregenciahorario->numrows > 0) {
        $turmas = array();
        /* Aqui é armazenado todos os dados em $oDados */
        for ($ind = 0; $ind < $clregenciahorario->numrows; $ind++) {
            $oDados[$ind] = db_utils::fieldsmemory($result1, $ind);
        }
        foreach ($oDados as $dado) {
            /* Aqui é armazenada todas as turmas do professor */
            $diaSemana = $dado->ed58_i_diasemana;
            $escola = $dado->ed17_i_escola;
            $disciplina = $dado->ed232_c_descr;
            $turma = $dado->ed57_c_descr;
            $turno = $dado->ed15_c_nome;
            $periodo = $dado->ed08_c_descr;
            $hrInicio = $dado->ed17_h_inicio;
            $hrFim = $dado->ed17_h_fim;

            if (!isset($disciplinasPorDia[$diaSemana])) {
                $disciplinasPorDia[$diaSemana] = [];
            }

            $disciplinasPorDia[$diaSemana][] = [
                'escola' => $escola,
                'turma' => $turma,
                'disciplina' => $disciplina,
                'turno' => $turno,
                'periodo' => $periodo,
                'hrInicio' => $hrInicio,
                'hrFim' => $hrFim,
            ];
        }

        $multidisciplinas = [];
        foreach ($disciplinasPorDia as $diaSemana => $disciplinas) {
            $horarios = []; // Array para rastrear os horários de início

            foreach ($disciplinas as $disciplina) {
                $hrInicio = $disciplina['hrInicio'];

                // Verifica se o horário de início já foi registrado
                if (!isset($horarios[$hrInicio])) {
                    // Se já registrado, adiciona a disciplina ao array multidisciplina
                    $multidisciplinas[$diaSemana][$hrInicio][] = $disciplina;
                } else {
                    // Se não registrado, cria um novo registro para o horário de início
                    $horarios[$hrInicio] = true;
                }
            }
        }

        $isMultidisciplina = false;
        foreach ($multidisciplinas as $diaSemana => $horarios) {
            foreach ($horarios as $hora) {
                if (count($hora) > 1) {
                    $isMultidisciplina = true;
                }
            }
        }

        /* Imprimindo os dados */
        if ($isMultidisciplina) {
            $height = 3;
            foreach ($multidisciplinas as $indiceDiaSemana => $diaSemana) {
                foreach ($diaSemana as $indiceHora => $hora) {
                    $arrayDisciplinas = array();
                    foreach ($hora as $dado) {
                        if (!in_array($dado['disciplina'], $arrayDisciplinas)) {
                            $arrayDisciplinas[] = $dado['disciplina'];
                        }
                    }
                    sort($arrayDisciplinas);
                    $stringDisciplinas = implode("; ", $arrayDisciplinas);
                    unset($arrayDisciplinas);

                    if (strlen($stringDisciplinas) > 120 || $isAtendSimult) {
                        $height = 4;
                    }

                    $alturaImpressao = calculaAlturaY($dado['hrInicio'], $alturaCelulaHora, $alturaHoras);
                    $alturaRetangulo = calculaAlturaRect($dado['hrInicio'], $dado['hrFim'], $alturaCelulaHora);
                    $colunaImpressao = $inicioGrade + (($indiceDiaSemana - 2) * $larg_dia);
                    $pdf->setXY($colunaImpressao, $alturaImpressao);
                    $pdf->rect($colunaImpressao, $alturaImpressao, $larg_dia, $alturaRetangulo, 'DF');
                    $textoImpressao = "";
                    if ($isAtendSimult) {
                        $textoImpressao = atendimentoSimult($clrechumanoescola, $rSimult, $dado['escola']);
                    }
                    $textoImpressao .= "Escola: cod(" . $dado['escola'] . ")\n";
                    $textoImpressao .= "Turma: " . $dado['turma'] . "\n";
                    $textoImpressao .= "Disciplinas(s): " . $stringDisciplinas . "\n";
                    $textoImpressao .= "Turno: " . $dado['turno'] . "\n";
                    $textoImpressao .= "Horário: " . $dado['hrInicio'] . " - " . $dado['hrFim'] . "\n";
                    $pdf->multicell($larg_dia, $height, $textoImpressao, 0, "L", 1);

                    $height = 3;
                }
            }
        } else { // Se não for multidisciplina (Mais de uma disciplina no mesmo horário) - Nivelando branch
            $height = 2.2;
            foreach ($disciplinasPorDia as $indiceDiaSemana => $diaSemana) {
                foreach ($diaSemana as $item) {
                    $alturaImpressao = calculaAlturaY($item['hrInicio'], $alturaCelulaHora, $alturaHoras);
                    $alturaRetangulo = calculaAlturaRect($item['hrInicio'], $item['hrFim'], $alturaCelulaHora);
                    $colunaImpressao = $inicioGrade + (($indiceDiaSemana - 2) * $larg_dia);
                    $pdf->setXY($colunaImpressao, $alturaImpressao);
                    $pdf->rect($colunaImpressao, $alturaImpressao, $larg_dia, $alturaRetangulo, 'DF');
                    $textoImpressao  = "Escola: cod(" . $item['escola'] . ")\n";
                    $textoImpressao .= "Turma: " . $item['turma'] . "\n";
                    $textoImpressao .= $item['disciplina'] . "\n";
                    $textoImpressao .= "Horário: " . $item['hrInicio'] . " - " . $item['hrFim'] . "\n";
                    $pdf->multicell($larg_dia, $height, $textoImpressao, 0, "L", 1);
                }
            }
        }
    }
}

$pdf->Output();
