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

include(modification("fpdf151/pdf.php"));
include(modification("libs/db_sql.php"));
include(modification("classes/db_rhteutri_classe.php"));
include(modification("classes/db_rhtipovale_classe.php"));
include(modification("dbforms/db_funcoes.php"));

$clrhteutri   = new cl_rhteutri;
$clrhtipovale = new cl_rhtipovale;
$clrotulo     = new rotulocampo;

$clrotulo->label('rh67_codigo');

parse_str($_SERVER['QUERY_STRING'], $query);

$res_tipovale = $clrhtipovale->sql_record($clrhtipovale->sql_query($rh67_rhtipovale, "*"));

db_fieldsmemory($res_tipovale, 0);

$iAno = DBPessoal::getAnoFolha();
$iMes = DBPessoal::getMesFolha();

if ($iMes == 12) {
    $mesProximo = 1;        // Janeiro
    $anoProximo = $iAno + 1; // Incrementa o ano
} else {
    $mesProximo = $iMes + 1; // Próximo mês
    $anoProximo = $iAno;     // Mesmo ano
}

$head3 = "RELATÓRIO DE VALE TRANSPORTE";
$head5 = "TIPO : " . $rh68_sequencial . " - " . $rh68_descr;
$head6 = "COMPRA DO VALE TRANSPORTE DE: " . $mesProximo . " / " . $anoProximo;

if ($ordem == 'a') {
    $xordem = 'z01_nome';
} else {
    $xordem = 'rh67_regist';
}

$where  = " rh67_rhtipovale = $rh67_rhtipovale ";
$where .= " and rh67_anousu = $iAno ";
$where .= " and rh67_mesusu = $iMes ";
$where .= " and rh05_recis is null";
$where .= " and rh02_instit = " . db_getsession('DB_instit') . " ";

$xgrupo = '';
if (trim($grupo) != '' && trim($grupo) != 'todos') {
    $where .= " and rh67_grupo = $grupo ";
}

$result = $clrhteutri->sql_record($clrhteutri->sql_query(null, "*", $xordem, $where));
$xxnum = pg_num_rows($result);

if ($xxnum == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Não existem Vales Cadastrados para o tipo " . $rh68_sequencial . " - " . $rh68_descr . ". Verifique!");
}

if ($rh67_rhtipovale == 1) {

    $pdf = new PDF();
    $pdf->Open();
    $pdf->AliasNbPages();
    $total = 0;
    $totalValorFormatado = 0;
    $totalValorMensalDescontado = 0;
    $pdf->setfillcolor(235);
    $pdf->setfont('arial', 'b', 7);
    $troca = 1;
    $alt = 4;

    for ($x = 0; $x < pg_num_rows($result); $x++) {
        db_fieldsmemory($result, $x);
        if ($pdf->gety() > $pdf->h - 30 || $troca != 0) {
            $pdf->addpage();
            $pdf->setfont('arial', 'b', 7);
            $pdf->cell(20, $alt, 'MATRÍCULA', 1, 0, "C", 1);
            $pdf->cell(60, $alt, 'NOME', 1, 0, "C", 1);
            $pdf->cell(30, $alt, 'VALOR UNITÁRIO', 1, 0, "C", 1);
            $pdf->cell(20, $alt, 'QTD. DE DIAS', 1, 0, "C", 1);
            $pdf->cell(30, $alt, 'VALOR TOTAL MENSAL', 1, 0, "C", 1);
            $pdf->cell(30, $alt, 'VALOR DESCONTADO', 1, 1, "C", 1);
            $troca = 0;
            $pre = 1;
        }

        if ($pre == 1) {
            $pre = 0;
        } else {
            $pre = 1;
        }

        $iAno = DBPessoal::getAnoFolha();
        $iMes = DBPessoal::getMesFolha();

        $sSql  = "SELECT valor_mensal_descontado,                                                           ";
        $sSql .= "       valor,                             ";
        $sSql .= "        ROUND(SUM(valor_unitario), 2) AS valor_unitario                                   ";
        $sSql .= "    FROM (                                                                                ";                                          
        $sSql .= "        SELECT                                                                            ";
        $sSql .= "            r14_valor AS valor_mensal_descontado,                                         "; 
        $sSql .= "            rh67_valor AS valor,                                                 ";
        $sSql .= "            r16_valor * r63_quant AS valor_unitario                                       ";
        $sSql .= "        FROM vtfempr                                                                      ";
        $sSql .= "            INNER JOIN vtffunc                                                            ";
        $sSql .= "                ON vtffunc.r17_codigo = vtfempr.r16_codigo                                ";
        $sSql .= "                AND vtffunc.r17_anousu = vtfempr.r16_anousu                               ";
        $sSql .= "                AND vtffunc.r17_mesusu = vtfempr.r16_mesusu                               ";
        $sSql .= "            INNER JOIN vtfdias                                                            ";
        $sSql .= "                ON vtfdias.r63_vale = vtfempr.r16_codigo                                  ";
        $sSql .= "                AND vtfdias.r63_anousu = vtfempr.r16_anousu                               ";
        $sSql .= "                AND vtfdias.r63_mesusu = vtfempr.r16_mesusu                               ";
        $sSql .= "            LEFT JOIN gerfsal                                                             ";                   
        $sSql .= "                ON gerfsal.r14_anousu = vtffunc.r17_anousu                                ";
        $sSql .= "                AND gerfsal.r14_mesusu = vtffunc.r17_mesusu                               ";
        $sSql .= "                AND gerfsal.r14_regist = vtffunc.r17_regist                               ";
        $sSql .= "                AND gerfsal.r14_instit = vtfempr.r16_instit                               ";
        $sSql .= "            INNER JOIN rhteutri                                                           ";
        $sSql .= "                ON rhteutri.rh67_anousu = vtffunc.r17_anousu                              ";
        $sSql .= "                AND rhteutri.rh67_mesusu = vtffunc.r17_mesusu                             ";
        $sSql .= "                AND rhteutri.rh67_regist = vtffunc.r17_regist                             ";
        $sSql .= "        WHERE                                                                             ";
        $sSql .= "            rh67_anousu = $iAno                                                           ";
        $sSql .= "            AND rh67_mesusu = $iMes                                                       ";
        $sSql .= "            AND r17_regist = {$rh67_regist}                                               ";
        $sSql .= "            AND r63_regist = {$rh67_regist}                                               "; 
        $sSql .= "            AND r16_instit = " . db_getsession('DB_instit') . "                           ";
        $sSql .= "            AND r14_rubric = 'R916'                                                       ";
        $sSql .= "            AND r16_empres = '1'                                                          ";
        $sSql .= "        GROUP BY r14_valor, rh67_valor, r16_codigo, r16_valor, r63_quant                  ";
        $sSql .= "        ) AS subquery                                                                     ";
        $sSql .= "                GROUP BY valor, valor_mensal_descontado                                   ";

        $rsValorVtf = db_query($sSql);

        if (!$rsValorVtf || pg_num_rows($rsValorVtf) == 0) {
            continue;
        }

        $nValorUnitario = db_formatar(db_utils::fieldsMemory($rsValorVtf, 0)->valor_unitario, "f");
        $nDiasUteis = $rh67_dias;
        $valorFormatado = number_format($rh67_valor / 100, 2, ',', '.');
        $nValorMensalDescontado = db_formatar(db_utils::fieldsMemory($rsValorVtf, 0)->valor_mensal_descontado, "f");

        $pdf->cell(20, $alt, $rh67_regist, 0, 0, "C", $pre);
        $pdf->cell(60, $alt, $z01_nome, 0, 0, "L", $pre);
        $pdf->cell(30, $alt, $nValorUnitario, 0, 0, "C", $pre);
        $pdf->cell(20, $alt, $nDiasUteis, 0, 0, "C", $pre);
        $pdf->cell(30, $alt, $valorFormatado, 0, 0, "C", $pre);
        $pdf->cell(30, $alt, $nValorMensalDescontado, 0, 1, "C", $pre);

        $valorFormatadoFloat = floatval(str_replace(',', '.', str_replace('.', '', $valorFormatado)));
        $total += 1;
        $totalValorFormatado += $valorFormatadoFloat;
        $totalValorMensalDescontado += floatval(str_replace(',', '.', $nValorMensalDescontado));
        
    }

    $pdf->setfont('arial', 'b', 7);
    $pdf->cell(130, $alt, 'TOTAL :  ' . $total . ' FUNCIONÁRIOS', "T", 0, "L", 0);
    $pdf->cell(30, $alt, number_format($totalValorFormatado, 2, ',', '.'), "T", 0, "C", 0);
    $pdf->cell(35, $alt, number_format($totalValorMensalDescontado, 2, ',', '.'), "T", 1, "C", 0); 

    $pdf->Output();

} else if ($rh67_rhtipovale = 2) {

    $pdf = new PDF();
    $pdf->Open();
    $pdf->AliasNbPages();
    $total = 0;
    $pdf->setfillcolor(235);
    $pdf->setfont('arial', 'b', 7);
    $troca = 1;
    $alt = 4;

    for ($x = 0; $x < pg_num_rows($result); $x++) {
        db_fieldsmemory($result, $x);
       
        if ($pdf->gety() > $pdf->h - 30 || $troca != 0) {
            $pdf->addpage();
            $pdf->setfont('arial', 'b', 7);
            $pdf->cell(20, $alt, 'MATRÍCULA', 1, 0, "C", 1);
            $pdf->cell(60, $alt, 'NOME', 1, 0, "C", 1);
            $pdf->cell(30, $alt, 'VALOR UNITÁRIO', 1, 0, "C", 1);
            $pdf->cell(20, $alt, 'QTD. DE DIAS', 1, 0, "C", 1);
            $pdf->cell(30, $alt, 'VALOR TOTAL MENSAL', 1, 0, "C", 1);
            $pdf->cell(30, $alt, 'VALOR DESCONTADO', 1, 1, "C", 1);
            $troca = 0;
            $pre = 1;
        }

        if ($pre == 1) {
            $pre = 0;
        } else {
            $pre = 1;
        }

        $iAno = DBPessoal::getAnoFolha();
        $iMes = DBPessoal::getMesFolha();

        $sSql  = "SELECT valor_mensal_descontado,                                                           ";
        $sSql .= "       ROUND(SUM(valor_unitario), 2) AS valor_unitario                                    ";
        $sSql .= "    FROM (                                                                                ";                                          
        $sSql .= "        SELECT                                                                            ";
        $sSql .= "            r14_valor AS valor_mensal_descontado,                                         "; 
        $sSql .= "            r16_valor * r63_quant AS valor_unitario                                       ";
        $sSql .= "        FROM vtfempr                                                                      ";
        $sSql .= "            INNER JOIN vtffunc                                                            ";
        $sSql .= "                ON vtffunc.r17_codigo = vtfempr.r16_codigo                                ";
        $sSql .= "                AND vtffunc.r17_anousu = vtfempr.r16_anousu                               ";
        $sSql .= "                AND vtffunc.r17_mesusu = vtfempr.r16_mesusu                               ";
        $sSql .= "            INNER JOIN vtfdias                                                            ";
        $sSql .= "                ON vtfdias.r63_vale = vtfempr.r16_codigo                                  ";
        $sSql .= "                AND vtfdias.r63_anousu = vtfempr.r16_anousu                               ";
        $sSql .= "                AND vtfdias.r63_mesusu = vtfempr.r16_mesusu                               ";
        $sSql .= "            LEFT JOIN gerfsal                                                             ";                   
        $sSql .= "                ON gerfsal.r14_anousu = vtffunc.r17_anousu                                ";
        $sSql .= "                AND gerfsal.r14_mesusu = vtffunc.r17_mesusu                               ";
        $sSql .= "                AND gerfsal.r14_regist = vtffunc.r17_regist                               ";
        $sSql .= "                AND gerfsal.r14_instit = vtfempr.r16_instit                               ";
        $sSql .= "            INNER JOIN rhteutri                                                           ";
        $sSql .= "                ON rhteutri.rh67_anousu = vtffunc.r17_anousu                              ";
        $sSql .= "                AND rhteutri.rh67_mesusu = vtffunc.r17_mesusu                             ";
        $sSql .= "                AND rhteutri.rh67_regist = vtffunc.r17_regist                             ";
        $sSql .= "        WHERE rh67_anousu = $iAno                                                         ";
        $sSql .= "            AND rh67_mesusu = $iMes                                                       ";
        $sSql .= "         AND r17_regist = {$rh67_regist}                                                  ";
        $sSql .= "         AND r63_regist = {$rh67_regist}                                                  ";
        $sSql .= "            AND r16_instit = " . db_getsession('DB_instit') . "                           ";
        $sSql .= "            AND r14_rubric = 'R916'                                                       ";
        $sSql .= "            AND r16_empres = '2'                                                          ";
        $sSql .= "        GROUP BY r14_valor, r16_codigo, r16_valor, r63_quant                              ";
        $sSql .= "        ) AS subquery                                                                     ";
        $sSql .= "                GROUP BY valor_mensal_descontado                                          ";

        $rsValorVtf = db_query($sSql);
        
        if (!$rsValorVtf || pg_num_rows($rsValorVtf) == 0) {
            continue;
        }

        $nValorUnitario = db_formatar(db_utils::fieldsMemory($rsValorVtf, 0)->valor_unitario, "f");
        $nDiasUteis = $rh67_dias;
        $valorFormatado = number_format($rh67_valor / 100, 2, ',', '.');
        $nValorMensalDescontado = db_formatar(db_utils::fieldsMemory($rsValorVtf, 0)->valor_mensal_descontado, "f");

        $pdf->cell(20, $alt, $rh67_regist, 0, 0, "C", $pre);
        $pdf->cell(60, $alt, $z01_nome, 0, 0, "L", $pre);
        $pdf->cell(30, $alt, $nValorUnitario, 0, 0, "C", $pre);
        $pdf->cell(20, $alt, $nDiasUteis, 0, 0, "C", $pre);
        $pdf->cell(30, $alt, $valorFormatado, 0, 0, "C", $pre);
        $pdf->cell(30, $alt, $nValorMensalDescontado, 0, 1, "C", $pre);

        $valorFormatadoFloat = floatval(str_replace(',', '.', str_replace('.', '', $valorFormatado)));
        $total += 1;
        $totalValorFormatado += $valorFormatadoFloat;
        $totalValorMensalDescontado += floatval(str_replace(',', '.', $nValorMensalDescontado));

    }

    $pdf->setfont('arial', 'b', 7);
    $pdf->cell(130, $alt, 'TOTAL :  ' . $total . ' FUNCIONÁRIOS', "T", 0, "L", 0);
    $pdf->cell(30, $alt, number_format($totalValorFormatado, 2, ',', '.'), "T", 0, "C", 0);
    $pdf->cell(35, $alt, number_format($totalValorMensalDescontado, 2, ',', '.'), "T", 1, "C", 0);
    $pdf->Output();
}

