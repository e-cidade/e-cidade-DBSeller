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
include(modification("libs/db_utils.php"));

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

/*
 * VERIFICA SE VEIO A VARIAVEL COMPLEMENTAR, SE VEIO, INCLUI NA CLAUSULA
 */
$sRh72 = "";
$sRh79 = "";

$sSiglas = "('" . $ponts . "')";
$sSiglas = str_replace(",", "','", $sSiglas);

if ($ponts == 'r48') {
    $sRh72 = " AND rh72_seqcompl <>  '0'";
    $sRh79 = " AND rh79_seqcompl <>  '0'";

    if (isset($semestre) && $semestre != "") { // && $complementar !== ""
        $sRh72 = " AND rh72_seqcompl = '$semestre'";
        $sRh79 = " AND rh79_seqcompl = '$semestre'";
    }
}

if ($consig == 0) {
    $sWhere = " ((rh73_tiporubrica = 2 and rh72_tipoempenho = 1) or (rh73_tiporubrica = 2 and rh79_tipoempenho = 1)) ";
    //and
    $sWhere .= "
        and ((rh72_anousu = {$ano} and rh72_mesusu = {$mes}) or (rh79_anousu = {$ano} and rh79_mesusu = {$mes}))
    ";

    if (isset($ponts) && trim($ponts) != "") {
        $sWhere .= " and ( (rh72_siglaarq in $sSiglas $sRh72 ) or (rh79_siglaarq in $sSiglas $sRh79 )) ";
    }
} else if ($consig == 1) {
    $sWhere = " (rh73_tiporubrica = 2 and rh72_tipoempenho = 1) and (rh72_anousu = {$ano} and rh72_mesusu = {$mes})";
    if (isset($ponts) && trim($ponts) != "") {
        $sWhere .= " and (rh72_siglaarq in $sSiglas $sRh72) ";
    }
} else if ($consig == 2) {
    $sWhere = "(rh73_tiporubrica = 2 and rh79_tipoempenho = 1) and (rh79_anousu = {$ano} and rh79_mesusu = {$mes}) ";
    if (isset($ponts) && trim($ponts) != "") {
        $sWhere .= " and (rh79_siglaarq in $sSiglas $sRh79) ";
    }
}

if ($sWhere != "") {
    $sWhere .= " and ";
}

$sWhere .= " rh73_instit = " . db_getsession("DB_instit");
$sWhere1 = "";

if (isset($rubrs) && trim($rubrs) != "") {
    $sRubricas = explode(',', $rubrs);
    $sWhere .= " AND rh73_rubric in " . "('" . implode("','", $sRubricas) . "')";
}

if (isset($recrs) && trim($recrs) != "") {
    $sRecursos = explode(',', $recrs);
    $sWhere1 .= " WHERE o15_codigo in ('" . implode("','", $sRecursos) . "')";
}

$sQuery = "
    select rh73_rubric,
           rh27_descr,
           gestao as recurso,
           o15_recurso as subrecurso,
           o15_complemento as complemento,
           descricao,
           round(sum(rh73_valor),2) as valor
    from (SELECT  rh73_rubric,
                  rh27_descr,
                  rh73_valor,
                  case when rh72_recurso is null then rh79_recurso else rh72_recurso end as codigo
             from rhempenhofolharubrica
            inner join rhrubricas on rh73_rubric = rh27_rubric and rh73_instit = rh27_instit
             left join rhempenhofolharhemprubrica on rh81_rhempenhofolharubrica = rh73_sequencial
             left join rhempenhofolha on rh81_rhempenhofolha = rh72_sequencial
             left join rhslipfolharhemprubrica on rh80_rhempenhofolharubrica = rh73_sequencial
             left join rhslipfolha on rh80_rhslipfolha = rh79_sequencial
            where $sWhere
        ) as x
    join orctiporec on codigo = o15_codigo
    join fonterecurso on orctiporec_id = o15_codigo and exercicio = {$ano}
   $sWhere1 group by rh73_rubric,rh27_descr,gestao,o15_recurso,descricao,o15_complemento
  order by rh73_rubric,gestao,o15_recurso,o15_complemento
";

$resQuery = db_query($sQuery);
$iNumRows = pg_num_rows($resQuery);

$sValorCompararQuebra = "";
$sCampoQuebrar = "rh73_rubric";
$total_ger = 0;
$aTotalRecurso = array();
if ($iNumRows == 0) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Não existem dados no período de ' . $mes . ' / ' . $ano);
} else {
    for ($i = 0; $i < $iNumRows; $i++) {
        $oDado = db_utils::fieldsMemory($resQuery, $i);

        if ($sValorCompararQuebra == $oDado->rh73_rubric) {
            $dados[$oDado->$sCampoQuebrar]->itens[] = $oDado;
            $dados[$oDado->$sCampoQuebrar]->total += $oDado->valor;
        } else {
            $dados[$oDado->$sCampoQuebrar]->itens[] = $oDado;
            $dados[$oDado->$sCampoQuebrar]->total = $oDado->valor;
        }

        $hash = sprintf('%s#%s#%s', $oDado->recurso, $oDado->subrecurso, $oDado->complemento);
        if (!array_key_exists($hash, $aTotalRecurso)) {
            $aTotalRecurso[$hash] = (object)[
                "total" => 0,
                "descricao" => substr($oDado->descricao, 0, 80),
                "cod" => "{$oDado->recurso} - {$oDado->subrecurso} - {$oDado->complemento}",
            ];
        }

        $aTotalRecurso[$hash]->total += $oDado->valor;

        $sValorCompararQuebra = $oDado->$sCampoQuebrar;
        $total_ger += $oDado->valor;
    }
}

$head3 = "Consignações da Folha (novo)";
$head5 = "Período : " . $mes . " / " . $ano;
$headPontos = "";
if (isset($ponts) && trim($ponts) != "") {
    $aPontos = explode(',', $ponts);

    if (in_array('r14', $aPontos)) {
        $headPontos .= "Salário";
    }
    if (in_array('r20', $aPontos)) {
        if ($headPontos != "") {
            $headPontos .= ", Rescisão";
        } else {
            $headPontos .= "Rescisão";
        }
    }
    if (in_array('r22', $aPontos)) {
        if ($headPontos != "") {
            $headPontos .= ", Adiantamento";
        } else {
            $headPontos .= "Adiantamento";
        }
    }
    if (in_array('r35', $aPontos)) {
        if ($headPontos != "") {
            $headPontos .= ", 13º Salário";
        } else {
            $headPontos .= "13º Salário";
        }
    }
    if (in_array('r48', $aPontos)) {
        if ($headPontos != "") {
            $headPontos .= ", Complementar";
        } else {
            $headPontos .= "Complementar";
        }
    }
}
$head7 = "Pontos: " . $headPontos;

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$pdf->setfont('arial', 'b', 8);
$troca = 1;
$alt = 4;
$cor = 1;

$pdf->addpage();

$lEscreverHeader = true;

foreach ($dados as $oQuebra) {
    $cor = 0;

    $lLinhaCompleta = true;

    foreach ($oQuebra->itens as $oImprime) {
        if ($pdf->Gety() > $pdf->h - 25 || $lEscreverHeader) {
            if ($pdf->Gety() > $pdf->h - 25) {
                $pdf->AddPage();
            }
            $pdf->setfont('arial', 'b', 8);
            $pdf->cell(15, $alt, 'RUBRICA', 1, 0, "C", 1);
            $pdf->cell(75, $alt, 'DESCRIÇÃO', 1, 0, "C", 1);
            $pdf->cell(75, $alt, 'RECURSO', 1, 0, "C", 1);
            $pdf->cell(25, $alt, 'DESCONTO', 1, 1, "C", 1);
            $lEscreverHeader = false;
        }

        $recurso = substr(sprintf(
            '%s - %s - %s',
            $oImprime->recurso,
            $oImprime->subrecurso,
            $oImprime->descricao,
            $oImprime->complemento
        ), 0, 50);
        if ($lLinhaCompleta) {
            $lLinhaCompleta = false;
            $pdf->setfont('arial', '', 8);
            $pdf->cell(15, $alt, $oImprime->rh73_rubric, 0, 0, "C", $cor);
            $pdf->cell(75, $alt, $oImprime->rh27_descr, 0, 0, "L", $cor);
            $pdf->cell(75, $alt, $recurso, 0, 0, "L", $cor);
            $pdf->cell(25, $alt, db_formatar($oImprime->valor, "f"), 0, 1, "R", $cor);
        } else {
            $pdf->setfont('arial', '', 8);
            $pdf->cell(15, $alt, "", 0, 0, "C", $cor);
            $pdf->cell(75, $alt, "", 0, 0, "L", $cor);
            $pdf->cell(75, $alt, $recurso, 0, 0, "L", $cor);
            $pdf->cell(25, $alt, db_formatar($oImprime->valor, "f"), 0, 1, "R", $cor);
        }
    }

    $cor = 1;
    $pdf->setfont('arial', 'b', 7);
    $pdf->cell(165, $alt, "Total da rubrica ", 0, 0, "R", 1);
    $pdf->cell(25, $alt, db_formatar($oQuebra->total, "f"), 0, 1, "R", 1);
    $pdf->ln(2);
}

$pdf->setfont('arial', 'B', 8);
$pdf->cell(165, $alt, "Total geral ", "T", 0, "R", 1);
$pdf->cell(25, $alt, db_formatar($total_ger, "f"), "T", 1, "R", 1);

if ($totaliza == 's') {
    $pdf->setfont('arial', 'B', 9);

    if ($pdf->gety() > $pdf->h - 30) {
        $pdf->addpage();
    }
    $pdf->cell(0, $alt, 'Total dos Recursos ', 0, 1, "L", 0);
    $pdf->setfont('arial', '', 9);
    $cor = 0;

    foreach ($aTotalRecurso as $oRecurso) {
        if ($pdf->gety() > $pdf->h - 30) {
            $pdf->addpage();
        }

        $pdf->cell(170, $alt, $oRecurso->cod . " - " . $oRecurso->descricao, 0, 0, "L", $cor, '', '.');
        $pdf->cell(20, $alt, db_formatar($oRecurso->total, "f"), 0, 1, "R", $cor);
    }
}

$pdf->Output();
