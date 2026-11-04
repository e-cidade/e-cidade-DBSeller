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

require_once(modification("fpdf151/pdf.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("classes/db_benshistoricocalculo_classe.php"));

$oGet = db_utils::postMemory($_GET);
if (empty($oGet->dtInicio)) {
    throw new Exception("É obrigatória a informação da data de início.");
}
if (empty($oGet->dtFim)) {
    throw new Exception("É obrigatória a informação da data de fim.");
}
if (empty($oGet->nomeCal)) {
    throw new Exception("É obrigatória a informação do calendário.");
}
$oDtInicio = new DBDate($oGet->dtInicio);
$oDtFim = new DBDate($oGet->dtFim);
$oNomeCal = str_replace(",", "','", $oGet->nomeCal);

if (mb_detect_encoding($oNomeCal . 'x', 'UTF-8', 'ISO-8859-1') == 'UTF-8') {
    $oNomeCal = utf8_decode($oGet->nomeCal);
    $oNomeCal = str_replace(",", "','", $oNomeCal); //wallace 2018-06-15

} else {

    $oNomeCal = str_replace(",", ',', $oNomeCal); //wallace 2018-06-15
}

$sFiltraAno = " ed52_i_ano = " . $oDtInicio->getAno();

$oDaoEscola = db_utils::getDao('escola');
$iAnoUsu = db_getsession("DB_anousu");

$sCampos = " ed18_i_codigo, ";
$sCampos .= " ed18_c_abrev, ";
$sCampos .= " ed18_c_nome, ";
$sCampos .= " ed18_codigoreferencia, ";
$sCampos .= " (SELECT count(*) ";
$sCampos .= "    FROM matricula ";
$sCampos .= "   INNER JOIN turma       ON ed57_i_codigo = ed60_i_turma ";
$sCampos .= "   INNER JOIN calendario  ON ed52_i_codigo = ed57_i_calendario ";
$sCampos .= "   WHERE ed60_c_situacao = 'MATRICULADO' ";
$sCampos .= "     AND ed60_d_datamatricula <= '" . $oDtFim->getDate() . "' ";
$sCampos .= "     AND extract(year FROM ed60_d_datamatricula) = " . $oDtFim->getAno();
$sCampos .= "     AND ( ed60_d_datasaida is null or ";
$sCampos .= "           ed60_d_datasaida not between '" . $oDtInicio->getDate() . "' and '" . $oDtFim->getDate() . "') ";
$sCampos .= "     AND ed57_i_escola = ed18_i_codigo ";
$sCampos .= "     AND ed52_c_descr  IN ('{$oNomeCal}') ";
$sCampos .= "     AND ed52_i_ano    = {$iAnoUsu}) AS total_alunos ";

$sSqlEscolas = $oDaoEscola->sql_query_file(null, $sCampos, "ed18_codigoreferencia", "ed18_i_funcionamento = 1 and ed18_i_tipoescola = 1");
$rsEscolas = $oDaoEscola->sql_record($sSqlEscolas);

if ($oDaoEscola->numrows == 0) {

    db_redireciona("db_erros.php?fechar=true&db_erro=Nenhuma escola cadastrada");
    exit;
}

$aEscolas = db_utils::getColectionByRecord($rsEscolas);
$lPrimeiroLaco = true;
$iAltura = 4;
$iTotalEscolas = 0;
$iTotalAlunos = 0;
$head1 = "Relatório de Escolas Ativas por Calendário";
$head2 = "Período: {$oGet->dtInicio} até {$oGet->dtFim}";
$nomecalhead = str_replace(",", ", ", $oGet->nomeCal);
$nomecalhead = utf8_decode($nomecalhead);
$head3 = "Calendário(s): {$nomecalhead}";
$oPdf = new PDF();
$oPdf->Open();
$oPdf->AliasNbPages();
$oPdf->setfillcolor(235);
/**
 * Iteramos sobre as escolas retornadas imprimindo os resultados
 */
foreach ($aEscolas as $oEscola) {
    $iTotalEscolas++;

    if ($oPdf->gety() > $oPdf->h - 30 || $lPrimeiroLaco) {
        $oPdf->addPage();
        imprimeCabecalho($oPdf, $iAltura);
        $lPrimeiroLaco = false;
    }
    $oPdf->setfont("arial", "", 8);
    $oPdf->cell(15, $iAltura, $iTotalEscolas, "BRLT", 0, "R");
    $oPdf->cell(15, $iAltura, $oEscola->ed18_i_codigo, "BRT", 0, "R");
    $oPdf->cell(15, $iAltura, $oEscola->ed18_codigoreferencia, "BRT", 0, "R");
    $oPdf->cell(130, $iAltura, empty($oEscola->ed18_c_abrev) ? $oEscola->ed18_c_nome : $oEscola->ed18_c_abrev, "BRT", 0, "L");
    $oPdf->cell(17, $iAltura, $oEscola->total_alunos, "BRT", 1, "R");
    $iTotalAlunos = $iTotalAlunos + $oEscola->total_alunos;
}
$oPdf->setfont("arial", "B", 8);
$oPdf->Ln(4);
$oPdf->cell(175, $iAltura, "TOTAL DE ESCOLAS: ".$iTotalEscolas, "", 1, "L");
$oPdf->cell(175, $iAltura, "TOTAL DE ALUNOS: " .$iTotalAlunos, "", 1, "L");

$oPdf->Output();

/**
 * Função para imprimir o cabecalho
 * @param FPDF $oPdf
 * @param integer $iAltura
 */
function imprimeCabecalho($oPdf, $iAltura)
{
    $oPdf->setfont("arial", "b", 8);
    $oPdf->cell(15, $iAltura, "ORDEM", "BRLT", 0, "C", 1);
    $oPdf->cell(15, $iAltura, "COD.", "BRT", 0, "C", 1);
    $oPdf->cell(15, $iAltura, "COD. REF.", 1, 0, "C", 1);
    $oPdf->cell(130, $iAltura, "ESCOLA", 1, 0, "C", 1);
    $oPdf->cell(17, $iAltura, "QTD. MAT", "BRT", 1, "C", 1);
}
