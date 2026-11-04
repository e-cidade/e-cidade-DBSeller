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

require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("std/DBDate.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("classes/db_edu_parametros_classe.php"));
require_once(modification("libs/db_libdocumento.php"));
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_libparagrafo.php"));
require_once(modification("model/educacao/avaliacao/iFormaObtencao.interface.php"));
require_once(modification("model/educacao/avaliacao/iElementoAvaliacao.interface.php"));
require_once(modification("model/CgmFactory.model.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("fpdf151/pdfwebseller.php"));


require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));


$oJson = new Services_JSON();
$oGet = db_utils::postMemory($_GET);

//$codigo_turma = ;//$oGet->iTurma;
$codigo_escola = $oGet->iEscola;
$ano =  $oGet->iAno;
$tamanho_fonte = 10;


// WALLACE (2019-01-01) MONTA CABEÇALHO

$oPdf = new PDF();
$oPdf->Open();
$oPdf->AliasNbPages();
$oPdf->SetAutoPageBreak(false);

$head1 = "ALUNOS AVALIADOS POR PARECER";
$head2 = "ESCOLA :" . strtoupper(EscolaRepository::getEscolaByCodigo($codigo_escola)->getNome());
$head3 = "ANO: " . $ano;

$oPdf->AddPage();

$oPdf->Ln(3);
$oPdf->SetFont('Arial', 'B', 12);
$oPdf->Cell($oPdf->getAvailWidth(), 4, "ALUNOS AVALIADOS POR PARECER", 0, 1, 'C', 0);
$oPdf->Ln(10);
$oPdf->SetFillColor(200, 200, 200);


// WALLACE (2019-01-31) BUSCA TURMA E SEU RESPECTIVO TURNO
$sql_turma =
    "
    SELECT DISTINCT ed57_i_codigo, ed57_c_descr
    FROM turma
      INNER JOIN escola ON ed18_i_codigo = ed57_i_escola
      INNER JOIN calendario ON ed52_i_codigo = ed57_i_calendario
      INNER JOIN matricula ON ed60_i_turma = ed57_i_codigo
      INNER JOIN aluno ON ed60_i_aluno =  ed47_i_codigo
      INNER JOIN diario ON ed95_i_aluno = ed47_i_codigo
      INNER JOIN diariofinal ON ed74_i_diario = ed95_i_codigo
    WHERE ed18_i_codigo = $codigo_escola AND ed52_i_ano = $ano AND ed74_c_valoraprov = 'Parecer' ORDER BY 1,2; 
    ";


$rs_turma = db_query($sql_turma);
$linhas_turma = pg_num_rows($rs_turma);


for ($x = 0; $x < $linhas_turma; $x++) {
    db_fieldsmemory($rs_turma, $x);
    quebraPagina($oPdf);

    $sql_ano =
        "
            SELECT DISTINCT
              ed11_i_codigo,
              ed11_c_descr
            FROM turma
              INNER JOIN matricula ON ed60_i_turma = ed57_i_codigo
              INNER JOIN aluno ON ed60_i_aluno = ed47_i_codigo
              INNER JOIN matriculaserie ON ed221_i_matricula = ed60_i_codigo
              INNER JOIN serie ON ed11_i_codigo = ed221_i_serie
            WHERE ed57_i_codigo = $ed57_i_codigo
            ORDER BY 1, 2;
    ";


    $rs_ano = db_query($sql_ano);
    $linhas_ano = pg_num_rows($rs_ano);


    for ($y = 0; $y < $linhas_ano; $y++) {
        db_fieldsmemory($rs_ano, $y);
        quebraPagina($oPdf);

        $oPdf->SetFont('Arial', 'B', $tamanho_fonte+1 );
        $oPdf->Cell($oPdf->getAvailWidth(), 4, "$ed57_c_descr ($ed11_c_descr) ", 1, 1, 'B',1);


        $sql_aluno = "
            SELECT DISTINCT
              ed47_i_codigo,
              ed47_v_nome
            FROM turma
              INNER JOIN matricula      ON ed60_i_turma = ed57_i_codigo
              INNER JOIN aluno          ON ed60_i_aluno = ed47_i_codigo
              INNER JOIN matriculaserie ON ed221_i_matricula = ed60_i_codigo
              INNER JOIN serie          ON ed11_i_codigo = ed221_i_serie
            WHERE ed57_i_codigo = $ed57_i_codigo AND ed11_i_codigo = $ed11_i_codigo
            ORDER BY 1, 2;
            ";

        $rs_aluno = db_query($sql_aluno);
        $linhas_aluno = pg_num_rows($rs_aluno);

        $oPdf->SetFont('Arial', '', $tamanho_fonte-2 );

        for ($z = 0; $z < $linhas_aluno; $z++) {
            db_fieldsmemory($rs_aluno, $z);
            quebraPagina($oPdf);

            $oPdf->Cell($oPdf->getAvailWidth(), 4, "$ed47_v_nome ", 1, 1, 'B');
        }
        $oPdf->Ln(2);

    }


 }





//
//
//// WALLACE (2019-01-31) ITERA O RESULTADO DAS TURMAS OBTIDAS PELA QUERY E TRANSFORMA OS CAMPOS DA CONSULTA EM VARÍAVEL PARA SEREM USADAS PELO FPDF
//
//$total_geral = 0;
//for ($x = 0; $x < $linhas_turma; $x++) {
//    db_fieldsmemory($rs_turma, $x);
//
//    quebraPagina($oPdf);
//
//
//    $oPdf->SetFont('Arial', 'B', $tamanho_fonte + 2);
//    $oPdf->Cell($oPdf->getAvailWidth(), 4, "TURMA: $ed57_c_descr - $ed15_c_nome", 'B', 1, 'B');
//
//// WALLACE (2019-01-31) BUSCA O ANOS ESCOLAR DE UMA TURMA ESPECÍFICA
//    $sql_anos =
//        "  SELECT DISTINCT
//        ed11_i_codigo, ed11_c_descr
//
//        FROM matricula
//        INNER JOIN matriculaserie       ON  matriculaserie.ed221_i_matricula = matricula.ed60_i_codigo
//        INNER JOIN serie                ON  serie.ed11_i_codigo = matriculaserie.ed221_i_serie
//        WHERE ed60_i_turma=$ed57_i_codigo AND  matriculaserie.ed221_c_origem = 'S' ORDER BY 1;
//    ";
//
//    $rs_anos = db_query($sql_anos);
//    $linhas_anos = pg_num_rows($rs_anos);
//
//// WALLACE (2019-01-31) ITERA O RESULTADO DOS ANOS ESCOLARES OBTIDOS PELA QUERY E TRANSFORMA OS CAMPOS DA CONSULTA EM VARÍAVEL PARA SEREM USADAS PELO FPDF
//    for ($y = 0; $y < $linhas_anos; $y++) {
//        db_fieldsmemory($rs_anos, $y);
//
//        quebraPagina($oPdf);
//
//        $oPdf->SetFont('Arial', 'B', $tamanho_fonte);
//        $oPdf->Cell($oPdf->getAvailWidth(), 4, "ANO: " . $ed11_c_descr, 1, 1, 'L', 1);
//        $oPdf->Cell($oPdf->getAvailWidth() / 6, 4, 'CÓDIGO', 1, 0, 'B');
//        $oPdf->Cell($oPdf->getAvailWidth(), 4, 'NOME', 1, 1, 'B');
//        $oPdf->Ln(1);
//
//
//        // WALLACE (2019-01-31) BUSCA OS ALUNOS  DE UM ANO ESCOLAR ESPECÍFICO
//        $sSqlAlunos =
//            "
//            SELECT ed47_i_codigo,ed60_i_aluno, ed47_v_nome,ed57_c_descr
//            FROM aluno
//            INNER JOIN  matricula ON matricula.ed60_i_aluno = aluno.ed47_i_codigo
//            INNER JOIN matriculaserie  ON  matriculaserie.ed221_i_matricula = matricula.ed60_i_codigo
//            INNER JOIN turma  on  turma.ed57_i_codigo = matricula.ed60_i_turma
//            INNER JOIN turno                ON  turno.ed15_i_codigo = turma.ed57_i_turno
//            INNER JOIN serie  on  serie.ed11_i_codigo = matriculaserie.ed221_i_serie
//            WHERE ed11_i_codigo = $ed11_i_codigo AND ed60_i_turma=$ed57_i_codigo  AND matriculaserie.ed221_c_origem = 'S';
//            ";
//
//        $rs_alunos = db_query($sSqlAlunos);
//        $linhas_alunos = pg_num_rows($rs_alunos);
//
//        // WALLACE (2019-01-31) ITERA O RESULTADO DOS ALUNOS OBTIDOS PELA QUERY E TRANSFORMA OS CAMPOS DA CONSULTA EM VARÍAVEL PARA SEREM USADAS PELO FPDF
//        for ($z = 0; $z < $linhas_alunos; $z++) {
//
//            db_fieldsmemory($rs_alunos, $z);
//            $alunos_anoescolar++;
//
//            $oPdf->SetFont('Arial', '', $tamanho_fonte);
//
//            $oPdf->Cell($oPdf->getAvailWidth() / 6, 4, $ed47_i_codigo, 'B', 0, 'B');
//            $oPdf->Cell($oPdf->getAvailWidth(), 4, $ed47_v_nome, 'B', 1, 'B');
//
//        }
//
//        // WALLACE (2019-01-31) GERA O TOTAL DE CADA ANO ESCOLAR
//        $oPdf->SetFont('Arial', 'B', $tamanho_fonte);
//        $oPdf->Cell($oPdf->getAvailWidth(), 4, "TOTAL: " . $alunos_anoescolar, 1, 1, "B");
//        $oPdf->Ln(5);
//        $total_alunos_turma +=$alunos_anoescolar;
//        $alunos_anoescolar =0;
//
//    }
//
//    // WALLACE (2019-01-31) GERA O TOTAL DE CADA TURMA
//    $oPdf->Cell($oPdf->getAvailWidth(), 4, "TOTAL POR TURMA: " . $total_alunos_turma, 'B', 1, "R");
//    $total_geral += $total_alunos_turma;
//    $total_alunos_turma = 0;
//    $oPdf->Ln(5);
//
//}
//
//// WALLACE (2019-01-31) GERA O TOTAL DE TODAS AS TURMAS
//$oPdf->Ln(5);
//$oPdf->Cell($oPdf->getAvailWidth(), 4, "TOTAL GERAL: " . $total_geral, 0, 1, "R");


function quebraPagina($oPdf){

    if ($oPdf->GetY() > ($oPdf->h - 50)) {
        $oPdf->AddPage();
    }
}

$oPdf->Output();








