<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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

require_once(modification("fpdf151/pdf.php"));
require_once(modification("libs/db_utils.php"));

$oGet = db_utils::postMemory($_GET);
$ano = $oGet->iAno;
$inclusao = $oGet->iInclusao;

$head1 = "RELATÓRIO ALUNOS AEE";
$head2 = "Escola: Todas";
$head3 = "Ano: $ano";
$head4 = "Inclusão: $inclusao";

$textoAviso  = "Devido a possibilidade de alunos receberem atendimento em mais de uma turma na Sala de Recursos, podem ocorrer divergências entre o número de alunos atendidos e o número de turmas na Unidade. ";

/**
 * Busca todos os alunos alunos que possuem necessidades especiáis
 */


$sSql ="  SELECT
          DISTINCT ed268_i_escola,escola.ed18_codigoreferencia
          FROM   turmaac
          INNER JOIN turmaacmatricula  ON   turmaac.ed268_i_codigo          =  turmaacmatricula.ed269_i_turmaac
          INNER JOIN turno             ON   turno.ed15_i_codigo             =  turmaac.ed268_i_turno
          INNER JOIN calendario        ON   calendario.ed52_i_codigo        =  turmaac.ed268_i_calendario
          INNER JOIN aluno             ON   aluno.ed47_i_codigo             =  turmaacmatricula.ed269_aluno
          LEFT  JOIN alunonecessidade  ON   alunonecessidade.ed214_i_aluno  =  turmaacmatricula.ed269_aluno
          INNER JOIN escola            ON   escola.ed18_i_codigo            =  turmaac.ed268_i_escola
  ";

$where = " WHERE ed268_i_tipoatend = 5 AND ed52_i_ano IN ($ano) ";

if($inclusao == 'mec'){

    $where .= " AND alunonecessidade.ed214_i_necessidade IS NOT NULL ";

}else if($inclusao == 'municipio'){

    $where .= " AND alunonecessidade.ed214_i_necessidade IS NULL ";
}

$orderBy= " ORDER BY escola.ed18_codigoreferencia asc";

$sSql .= $where.$orderBy;


$rsAlunos = db_query($sSql);

$rsEscolas = db_query($sSql);

$iLinhasEscola = pg_num_rows($rsEscolas);


if ($iLinhasEscola == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum registro encontrado para os filtros selecionados.");
}

/**
 * Organiza os dados retornado pela query
 */
$total_geral_alunos_aee_mec = 0;
$total_geral_alunos_aee_municipio = 0;
$total_geral_alunos_aee = 0;
$total_geral_turmas_aee = 0;
$total_geral_salas_aee = 0;

$aDadosEscola = array();

for ($i = 0; $i < $iLinhasEscola; $i++) {

    $oDados = db_utils::fieldsMemory($rsAlunos, $i);

    $iEscola = $oDados->ed268_i_escola;

    $oDadosEscola = new stdClass();

    $oDadosEscola->ref_escola                       = EscolaRepository::getEscolaByCodigo($iEscola)->getCodigoReferencia();
    $oDadosEscola->nome_escola                      = EscolaRepository::getEscolaByCodigo($iEscola)->getNome();
    $oDadosEscola->total_alunos_aee_mec             = buscarAlunosMatriculadosIncluidosMEC($iEscola,$ano);
    $oDadosEscola->total_alunos_aee_municipio       = buscarAlunosMatriculadosIncluidosMunicipio($iEscola,$ano);
    $oDadosEscola->total_alunos_aee                 = buscaAlunosMatriculadosAEE($iEscola,$ano);
    $oDadosEscola->total_turmas_aee                 = buscaTurmasComAlunosMatriculadosAEE($iEscola,$ano);
    $oDadosEscola->total_salas_aee                  = buscaSalasAEE($iEscola,$ano);
    $aDadosEscola[] = $oDadosEscola;


    $total_geral_alunos_aee_mec                    += $oDadosEscola->total_alunos_aee_mec;
    $total_geral_alunos_aee_municipio              += $oDadosEscola->total_alunos_aee_municipio;
    $total_geral_alunos_aee                        += $oDadosEscola->total_alunos_aee;
    $total_geral_turmas_aee                        += $oDadosEscola->total_turmas_aee;
    $total_geral_salas_aee                         += $oDadosEscola->total_salas_aee;


}

$iHeight = 4;
$oPdf = new Pdf("P");
$oPdf->Open();
$oPdf->AliasNbPages();
$oPdf->SetAutoPageBreak(false);
$oPdf->SetFillColor(225, 225, 225);
$oPdf->AddPage();

$oPdf->Ln(5);
$oPdf->SetFont('arial', '', 10);
$oPdf->Cell($oPdf->getAvailWidth(), $iHeight, "ALUNOS ATENDIDOS EM SALAS DE RECURSOS - AEE " , 0, 1, "C");
$oPdf->Ln(5);

$iHeight = 4;

$ordenacao = 0;
foreach ($aDadosEscola as $oEscola){
    validaQuebraPagina($oPdf);
    $ordenacao++;

    $oPdf->SetFont('arial', '', 8);
    $oPdf->Cell(8, $iHeight, $ordenacao, 1, 0, "C",1);
    $oPdf->Cell(20, $iHeight, "CÓD: {$oEscola->ref_escola} ", 1, 0, "L",1);
    $oPdf->Cell($oPdf->getAvailWidth(), $iHeight, "ESCOLA: {$oEscola->nome_escola}", 1, 1, "L",1);

    if($inclusao == 'todas') {

        $oPdf->Cell(($oPdf->getAvailWidth()/4), $iHeight, "INCLUÍDOS PELO MEC: ", "B", 0, "L");
        $oPdf->Cell($oPdf->getAvailWidth(), $iHeight, $oEscola->total_alunos_aee_mec, "B", 1, "R");

        $oPdf->Cell(($oPdf->getAvailWidth()/4), $iHeight, "INCLUÍDOS PELO MUNICÍPIO: ", "B", 0, "L");
        $oPdf->Cell($oPdf->getAvailWidth(), $iHeight, $oEscola->total_alunos_aee_municipio, "B", 1, "R");

    }else if ($inclusao == 'mec'){

        $oPdf->Cell(($oPdf->getAvailWidth()/4), $iHeight, "INCLUÍDOS PELO MEC: ", "B", 0, "L");
        $oPdf->Cell($oPdf->getAvailWidth(), $iHeight, $oEscola->total_alunos_aee_mec, "B", 1, "R");

    }else if($inclusao == 'municipio'){

        $oPdf->Cell(($oPdf->getAvailWidth()/4), $iHeight, "INCLUÍDOS PELO MUNICÍPIO: ", "B", 0, "L");
        $oPdf->Cell(($oPdf->getAvailWidth()), $iHeight, "{$oEscola->total_alunos_aee_municipio}", 'B', 1, "R");


    }

    $oPdf->Cell($oPdf->getAvailWidth()/4, $iHeight, "TOTAL DE ALUNOS ATENDIDOS NA UNIDADE - MEC/MUNICÍPIO:", "B", 0, "L");
    $oPdf->Cell($oPdf->getAvailWidth(), $iHeight,$oEscola->total_alunos_aee, "B", 1, "R");

    $oPdf->Cell($oPdf->getAvailWidth()/4, $iHeight, "TOTAL DE TURMAS (DE ACORDO COM O INFORMADO PELO CENSO):", "B", 0, "L");
    $oPdf->Cell($oPdf->getAvailWidth(), $iHeight,$oEscola->total_turmas_aee, "B", 1, "R");

    $oPdf->Cell($oPdf->getAvailWidth()/4, $iHeight, "TOTAL DE SALAS DE RECURSO NA UNIDADE:", "B", 0, "L");
    $oPdf->Cell($oPdf->getAvailWidth(), $iHeight,$oEscola->total_salas_aee , "B", 1, "R");

    $oPdf->Ln(2);
    $oPdf->Cell($oPdf->getAvailWidth(), $iHeight, str_repeat("-",$oPdf->getAvailWidth()), 0, 1, "L");
    $oPdf->Ln(2);



}

$oPdf->AddPage();

$oPdf->SetY(60);
$oPdf->SetX($oPdf->w/5);

$oPdf->SetFont('arial', 'b', 8);

if($inclusao == 'todas') {

    $oPdf->Cell($oPdf->getAvailWidth() / 1.5, $iHeight, "TOTAL DE ALUNOS INCLUÍDOS PELO MEC:", 1, 0, "L");
    $oPdf->Cell($oPdf->getAvailWidth() / 4, $iHeight, empty($total_geral_alunos_aee_mec) ? '0' : $total_geral_alunos_aee_mec, 1, 1, "R");

    $oPdf->SetX($oPdf->w / 5);
    $oPdf->Cell($oPdf->getAvailWidth() / 1.5, $iHeight, "TOTAL DE ALUNOS INCLUÍDOS PELO MUNICÍPIO:", 1, 0, "L");
    $oPdf->Cell($oPdf->getAvailWidth() / 4, $iHeight, empty($total_geral_alunos_aee_municipio) ? '0' : $total_geral_alunos_aee_municipio, 1, 1, "R");

}else if ($inclusao == 'mec'){

    $oPdf->Cell($oPdf->getAvailWidth() / 1.5, $iHeight, "TOTAL DE ALUNOS INCLUÍDOS PELO MEC:", 1, 0, "L");
    $oPdf->Cell($oPdf->getAvailWidth() / 4, $iHeight, empty($total_geral_alunos_aee_mec) ? '0' : $total_geral_alunos_aee_mec, 1, 1, "R");

}else if($inclusao == 'municipio'){

    $oPdf->Cell($oPdf->getAvailWidth() / 1.5, $iHeight, "TOTAL DE ALUNOS INCLUÍDOS PELO MUNICÍPIO:", 1, 0, "L");
    $oPdf->Cell($oPdf->getAvailWidth() / 4, $iHeight, empty($total_geral_alunos_aee_municipio) ? '0' : $total_geral_alunos_aee_municipio, 1, 1, "R");
}


$oPdf->SetX($oPdf->w/5);
$oPdf->Cell($oPdf->getAvailWidth()/1.5, $iHeight, "TOTAL DE ALUNOS ATENDIDOS NA UNIDADE - MEC/MUNICÍPIO:", 1, 0, "L");
$oPdf->Cell($oPdf->getAvailWidth()/4, $iHeight, empty($total_geral_alunos_aee) ? '0' : $total_geral_alunos_aee, 1, 1, "R");

$oPdf->SetX($oPdf->w/5);
$oPdf->Cell($oPdf->getAvailWidth()/1.5, $iHeight, "TOTAL DE TURMAS:", 1, 0, "L");
$oPdf->Cell($oPdf->getAvailWidth()/4, $iHeight,empty($total_geral_turmas_aee) ? '0' : $total_geral_turmas_aee, 1, 1, "R");

$oPdf->SetX($oPdf->w/5);
$oPdf->Cell($oPdf->getAvailWidth()/1.5, $iHeight, "TOTAL DE SALAS DE RECURSO:", 1, 0, "L");
$oPdf->Cell($oPdf->getAvailWidth()/4, $iHeight,empty($total_geral_salas_aee) ? '0' : $total_geral_salas_aee , 1, 1, "R");

$oPdf->SetFont('arial', 'b', 10);
$oPdf->Ln(10);
$oPdf->MultiCell($oPdf->getAvailWidth(), $iHeight,$textoAviso, 0,  "C");




function buscaAlunosMatriculadosAEE($iEscola,$oNomeCal)
{

    $sWhere = "  ed268_i_escola = {$iEscola} ";
    $sWhere .= " and ed268_i_tipoatend = 5 ";
    $sWhere .= " and ed52_i_ano IN ('$oNomeCal') ";
    $oDaoAee = new cl_turmaacmatricula();
    $rsAee = db_query($oDaoAee->sql_query_turma(null, "count( DISTINCT aluno.ed47_i_codigo)", null, $sWhere));
    return db_utils::fieldsMemory($rsAee, 0)->count;

}

function buscaTurmasComAlunosMatriculadosAEE($iEscola,$oNomeCal)
{

    $sWhere = "  ed268_i_escola = {$iEscola} ";
    $sWhere .= " and ed268_i_tipoatend = 5 ";
    $sWhere .= " and ed52_i_ano IN ('$oNomeCal') ";
    $oDaoAee = new cl_turmaacmatricula();
    $rsAee = db_query($oDaoAee->sql_query_turma(null, "count(DISTINCT turmaac.ed268_i_codigo)", null, $sWhere));
    return db_utils::fieldsMemory($rsAee, 0)->count;
}



function buscaTurmasAEE($iEscola,$oNomeCal)
{

    $sWhere = "  ed268_i_escola = {$iEscola} ";
    $sWhere .= " and ed268_i_tipoatend = 5 ";
    $sWhere .= " and ed52_i_ano IN  ('$oNomeCal') ";
    $oDaoAee = new cl_turmaac();
    $rsAee = db_query($oDaoAee->sql_query(null, " count( DISTINCT ed268_i_codigo)", null, $sWhere));

    return db_utils::fieldsMemory($rsAee, 0)->count;
}

function buscaSalasAEE($iEscola,$oNomeCal)
{

    $turnoSql = "SELECT
                      COUNT ( DISTINCT
                      CASE
                      WHEN (SELECT count(*) FROM turno t  INNER JOIN turnoReferente tr on tr.ed231_i_turno = t.ed15_i_codigo WHERE t.ed15_i_codigo = turno.ed15_i_codigo AND tr.ed231_i_referencia = 1 )    >= 1 THEN 'MANHA'
                      WHEN (SELECT count(*) FROM turno t  INNER JOIN turnoReferente tr on tr.ed231_i_turno = t.ed15_i_codigo WHERE t.ed15_i_codigo = turno.ed15_i_codigo AND tr.ed231_i_referencia = 2 )     >= 1 THEN 'TARDE'
                      WHEN (SELECT count(*) FROM turno t  INNER JOIN turnoReferente tr on tr.ed231_i_turno = t.ed15_i_codigo WHERE t.ed15_i_codigo = turno.ed15_i_codigo AND tr.ed231_i_referencia = 3 )     >= 1 THEN 'NOITE'
                      END)
                FROM turmaac
                      INNER JOIN escola ON escola.ed18_i_codigo = turmaac.ed268_i_escola
                      INNER JOIN turno ON turno.ed15_i_codigo = turmaac.ed268_i_turno
                      INNER JOIN turnoReferente on turnoreferente.ed231_i_turno = turno.ed15_i_codigo
                      LEFT JOIN sala ON sala.ed16_i_codigo = turmaac.ed268_i_sala
                      INNER JOIN calendario ON calendario.ed52_i_codigo = turmaac.ed268_i_calendario
                      LEFT JOIN tiposala ON tiposala.ed14_i_codigo = sala.ed16_i_tiposala
                      INNER JOIN duracaocal ON duracaocal.ed55_i_codigo = calendario.ed52_i_duracaocal
                WHERE ed268_i_escola = {$iEscola} AND ed268_i_tipoatend = 5 AND ed52_i_ano IN ('$oNomeCal')";

    $rsTurnoAEE = db_query($turnoSql);
    return db_utils::fieldsMemory($rsTurnoAEE, 0)->count;
}

function buscarAlunosMatriculadosIncluidosMEC($iEscola,$oNomeCal){

    $sWhere = "  ed268_i_escola = {$iEscola} ";
    $sWhere .= " and ed268_i_tipoatend = 5 ";
    $sWhere .= " and ed52_i_ano IN  ('$oNomeCal') ";
    $sWhere .= " and exists(SELECT 1 FROM alunonecessidade WHERE turmaacmatricula.ed269_aluno = alunonecessidade.ed214_i_aluno) ";
    $oDaoAee = new cl_turmaacmatricula();
    $rsAee = db_query($oDaoAee->sql_query_turma(null, "count( DISTINCT aluno.ed47_i_codigo)", null, $sWhere));
    return db_utils::fieldsMemory($rsAee, 0)->count;


}

function buscarAlunosMatriculadosIncluidosMunicipio($iEscola,$oNomeCal){

    $sWhere = "  ed268_i_escola = {$iEscola} ";
    $sWhere .= " and ed268_i_tipoatend = 5 ";
    $sWhere .= " and ed52_i_ano IN ('$oNomeCal') ";
    $sWhere .= " and not exists(SELECT 1 FROM alunonecessidade WHERE turmaacmatricula.ed269_aluno = alunonecessidade.ed214_i_aluno) ";
    $oDaoAee = new cl_turmaacmatricula();
    $rsAee = db_query($oDaoAee->sql_query_turma(null, "count( DISTINCT aluno.ed47_i_codigo)", null, $sWhere));
    return db_utils::fieldsMemory($rsAee, 0)->count;

}

function ordenarAlfabeticamente($arrays)
{
    $arrayOrganizado = array();
    $tamanho = 0;
    $posicao = 0;

    for ($i = 0; $i < count($arrays); $i++) {

        if (strcasecmp(substr($arrays[$i], 0, 4),"fase")==0) {
            $arrays[$i] = substr($arrays[$i], 0, 4) . " " . romanoParaInteiro(substr($arrays[$i], 5));
        }

        if ($i == 0) {
            $arrayOrganizado[0] = $arrays[0];
            $tamanho = sizeof($arrayOrganizado);

        } else {
            for ($j = 0; $j < $tamanho; $j++) {
                if ($arrays[$i] > $arrayOrganizado[$j]) {
                    $posicao++;

                } else if (substr($arrays[$i], 3) < substr($arrayOrganizado[$j], 3)) {

                    //fazer um for que agrupe para a direita se existe valor a direita cria mais uma casa senão apenas troca de lugar
                    if (isset($arrayOrganizado[$posicao + 1])) {

                        for ($k = $tamanho; $k > $posicao; $k--) {
                            $guardarValor = $arrayOrganizado[$k - 1];
                            $arrayOrganizado[$k] = $guardarValor;
                        }
                        $j = $tamanho;
                    } else {
                        $guardarValor = $arrayOrganizado[$posicao];
                        $arrayOrganizado[$posicao + 1] = $guardarValor;
                    }

                } else {
                    //fazer um for que agrupe para a direita se existe valor a direita cria mais uma casa senão apenas troca de lugar
                    if (isset($arrayOrganizado[$posicao + 1])) {

                        for ($k = $tamanho; $k > $posicao; $k--) {
                            $guardarValor = $arrayOrganizado[$k - 1];
                            $arrayOrganizado[$k] = $guardarValor;
                        }
                        $j = $tamanho;
                    } else {

                    }
                }

            }
            $arrayOrganizado[$posicao] = $arrays[$i];

            $tamanho = count($arrayOrganizado);
            $posicao = 0;
        }


    }
    return ordenarPorEtapa($arrayOrganizado);
}

function ordenarPorEtapa($arrays)
{
    $etapas = array();
    $pos = 0;
    $existe = 0;
    for ($i = 0; $i < count($arrays); $i++) {
        if ($i == 0) {
            $etapas[$i] = substr($arrays[$i], 4);
        } else {
            for ($k = 0; $k < count($etapas); $k++) {
                if ($etapas[$k] == substr($arrays[$i], 4)) {
                    $existe++;
                }
            }

            if ($existe == 0) {
                $pos++;
                $etapas[$pos] = substr($arrays[$i], 4);

            }
            $existe = 0;
        }

    }
    $etapaOrdenada = array();

    for ($j = 0; $j < count($etapas); $j++) {
        for ($i = 0; $i < count($arrays); $i++) {

            if ($etapas[$j] == substr($arrays[$i], 4)) {
                if (strcasecmp(substr($arrays[$i], 0, 4),"fase")==0) {
                    $etapaOrdenada[$i] = substr($arrays[$i], 0, 4) . " " . inteiroParaRomano(substr($arrays[$i], 5));
                } else {
                    $etapaOrdenada[$i] = $arrays[$i];
                }
            }
        }
    }
    return $etapaOrdenada;
}

/**
 * Converter número romano para inteiro
 */

function romanoParaInteiro($numRoman, $debug = false)
{

    $nRoman = $numRoman;
    $default = array(
        'M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' => 9,
        'V' => 5,
        'IV' => 4,
        'I' => 1,
    );

    $int = 0;
    foreach ($default as $key => $value) {
        while (strpos($numRoman, $key) === 0) {
            $int += $value;
            $numRoman = substr($numRoman, strlen($key));
        }
    }

    if ($debug) {
        return sprintf('%s = %s', $nRoman, $int);
    }

    return $int;
}

/**
 * Converter número inteiro para romano
 */
function inteiroParaRomano($num, $debug = false)
{

    $n = intval($num);
    $nRoman = '';

    $default = array(
        'M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' => 9,
        'V' => 5,
        'IV' => 4,
        'I' => 1,
    );

    foreach ($default as $roman => $number) {
        $matches = intval($n / $number);
        $nRoman .= str_repeat($roman, $matches);
        $n = $n % $number;
    }

    if ($debug) {
        return sprintf('%s = %s', $num, $nRoman);
    }

    return $nRoman;
}
//----

/**
 * imprime cabeçalho
 * @param FPDF $oPdf
 * @param integer $iHeight
 */
function imprimeCabecalho(FPDF $oPdf, $iHeight)
{

    $oPdf->SetFont('arial', 'b', 8);
    $oPdf->cell(10, $iHeight, "Código", "TBR", 0, "C", 1);
    $oPdf->cell(80, $iHeight, "Aluno", 1, 0, "L", 1);
    $oPdf->cell(30, $iHeight, "Turma", 1, 0, "C", 1);
    $oPdf->cell(30, $iHeight, "Etapa", 1, 0, "C", 1);
    $oPdf->cell(30, $iHeight, "Código INEP", 1, 0, "C", 1);
    $oPdf->cell(10, $iHeight, "Escola", "TBL", 1, "C", 1);
}


/**
 * Valida se deve ser quebrado pagina
 * @param FPDF $oPdf
 */
function validaQuebraPagina(FPDF $oPdf)
{

    if ($oPdf->GetY() > $oPdf->h - 50) {
        $oPdf->AddPage();
    }
}

$oPdf->Output();
?>
