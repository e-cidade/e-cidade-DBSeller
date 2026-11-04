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

require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("fpdf151/scpdf.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_libdocumento.php"));
require_once(modification("libs/db_libparagrafo.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("model/educacao/ArredondamentoNota.model.php"));
require_once(modification("std/DBDate.php"));

$datas = explode("/",$dtAtual);

$dDia = $datas[0];
$dMes = $datas[1];
$dAno = $datas[2];
if($dMes == "01") { $nMes = "Janeiro"; }
if($dMes == "02") { $nMes = "Fevereiro"; }
if($dMes == "03") { $nMes = "Março"; }
if($dMes == "04") { $nMes = "Abril"; }
if($dMes == "05") { $nMes = "Maio"; }
if($dMes == "06") { $nMes = "Junho"; }
if($dMes == "07") { $nMes = "Julho"; }
if($dMes == "08") { $nMes = "Agosto"; }
if($dMes == "09") { $nMes = "Setembro"; }
if($dMes == "10") { $nMes = "Outubro"; }
if($dMes == "11") { $nMes = "Novembro"; }
if($dMes == "12") { $nMes = "Dezembro"; }

$oDaoEduRelatModel      = new cl_edu_relatmodel;

$sCamposRelatModel = "ed217_brasao, ed217_t_cabecalho";
$sSqlEduRelatModel  = $oDaoEduRelatModel->sql_query("", $sCamposRelatModel, "", "ed217_i_codigo = {$tipoRelatorio}");
$rsEduRelatModel    = $oDaoEduRelatModel->sql_record($sSqlEduRelatModel);

if ($oDaoEduRelatModel->numrows > 0) {
  $oDadosRelatModel = db_utils::fieldsmemory($rsEduRelatModel, 0);
}

$iInstituicao = db_getsession( "DB_instit" );
$sImagem      = RelatorioHistoricoEscolar::getBrasao( $oDadosRelatModel->ed217_brasao, new Instituicao( $iInstituicao ) );

$arr_turma = explode('.', $turma);
$etapa     = $arr_turma[1];

$sSqlAluno  = "SELECT ed11_c_descr as fase, ";
$sSqlAluno .= "ed52_i_ano as ano, ";
$sSqlAluno .= "ed29_c_descr as etapa, ";
$sSqlAluno .= "ed47_v_nome AS nomealuno, ";
$sSqlAluno .= "ed47_d_nasc AS datanasc, ";
$sSqlAluno .= "ed47_v_mae AS nomemae, ";
$sSqlAluno .= "ed47_v_pai AS nomepai, ";
$sSqlAluno .= "ed18_c_nome AS escola ";
$sSqlAluno .= "FROM matricula ";
$sSqlAluno .= "INNER JOIN turma ON (ed60_i_turma = ed57_i_codigo) ";
$sSqlAluno .= "INNER JOIN turmaserieregimemat ON (ed220_i_turma = ed57_i_codigo) ";
$sSqlAluno .= "INNER JOIN serieregimemat ON (ed223_i_codigo = ed220_i_serieregimemat) ";
$sSqlAluno .= "INNER JOIN serie ON (ed11_i_codigo = ed223_i_serie) ";
$sSqlAluno .= "INNER JOIN escola ON (ed57_i_escola = ed18_i_codigo) ";
$sSqlAluno .= "INNER JOIN aluno ON (ed60_i_aluno = ed47_i_codigo) ";
$sSqlAluno .= "INNER JOIN calendario ON (ed57_i_calendario = ed52_i_codigo) ";
$sSqlAluno .= "INNER JOIN base ON (ed57_i_base = ed31_i_codigo) ";
$sSqlAluno .= "INNER JOIN cursoedu ON (ed31_i_curso = ed29_i_codigo) ";
$sSqlAluno .= "WHERE ed60_i_codigo IN ($alunos) ";
$sSqlAluno .= "AND ed11_i_codigo = $etapa ";

$rsAluno = db_query($sSqlAluno);
$iLinhasAluno = pg_num_rows($rsAluno);
$sSqlMunic = "SELECT munic FROM db_config WHERE codigo = $iInstituicao ";
$rsMunic = db_query($sSqlMunic);
$Munic = ucfirst(mb_strtolower(pg_result($rsMunic, 0)));

$oPdf = new FPDF();
$oPdf->Open();
$oPdf->AliasNbPages();
$oPdf->setfillcolor(223);
$oPdf->SetAutoPageBreak(false, 10);

for($cont = 0; $cont < $iLinhasAluno; $cont++){

  db_fieldsmemory($rsAluno, $cont);

  $oPdf->AddPage('L');
  $sMoldura = ECIDADE_PATH."imagens/moldura_certificado_conclusao.jpg";
  $oPdf->Image($sMoldura, 1, 1, 293, 210);
  $oPdf->Image($sImagem, 136, 20, 25, 25);
  $oPdf->SetFont('Arial', 'b', 16);
  $oPdf->SetXY(0,48);
  $oPdf->MultiCell(297,6,$oDadosRelatModel->ed217_t_cabecalho, 0, "C", 0, 0);
//$oPdf->Cell(85, 4, "PREFEITURA DE PETRÓPOLIS", 0, 0, "L", 0);
//  $oPdf->SetXY(107,55);
//  $oPdf->Cell(83, 4, "SECRETARIA DE EDUCAÇÃO", 0, 0, "L", 0);

  $oPdf->SetXY(0,90 );
  $oPdf->Cell(297, 4,$escola, 0, 0, "C", 0);
  $oPdf->SetXY(117.5,80);
  $oPdf->SetFont('Arial', 'b', 25);
  $oPdf->Cell(62, 4, "CERTIFICADO", 0, 0, "L", 0);
  $nascimento = date('d/m/Y', strtotime($datanasc));
  $oPdf->SetXY(30,100);
  $oPdf->SetFont('Arial', '', 16);
  $oPdf->MultiCell(237, 8, "Certificamos, para os devidos fins, que o(a) aluno(a) ".$nomealuno.", nascido(a) em ".$nascimento.", filho(a) de ".$nomemae." e ".$nomepai.", concluiu o(a) ".$fase." do(a) ".$etapa." no ano de ".$ano.".", 0, "J", 0, 0);

  $oPdf->SetXY(0,140);
  $oPdf->Cell(297, 4, "$Munic, $dDia de $nMes de $dAno.", 0, 0, "C", 0);

  $oPdf->SetXY(44,170);
  $oPdf->Cell(90, 8, "Diretor(a)", "T", 0, "C", 0);

  $oPdf->SetXY(162,170);
  $oPdf->Cell(90, 8, "Secretário(a)", "T", 0, "C", 0);
}

$oPdf->Output();
?>
