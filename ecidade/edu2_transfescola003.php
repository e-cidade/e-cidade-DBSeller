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

require_once(modification("fpdf151/pdfwebseller.php"));
require_once(modification("libs/db_utils.php"));
$oDaoTransfEscolaRede = db_utils::getdao('transfescolarede');
$oDaoTransfEscolaFora = db_utils::getdao('transfescolafora');
$oDaoMatricula        = db_utils::getdao('matricula');

if ($iEscola == 'TODAS') {
  $oDaoEscola     = db_utils::getdao('escola');              
  $sSqlEscola     = $oDaoEscola->sql_query_file("", "ed18_i_codigo,ed18_c_nome", "", "");                                                                      
  $rsResultEscola = $oDaoEscola->sql_record($sSqlEscola);            
  $iLinhas        = $oDaoEscola->numrows;
  $escolas = array(); 

  for ($iCont = 0; $iCont < $iLinhas; $iCont++) {                       
    $oDadosEscola = db_utils::fieldsmemory($rsResultEscola, $iCont);  
    $escolas[$iCont] = $oDadosEscola->ed18_i_codigo;                                 
  }
  $iEscola = implode(" ,", $escolas);
}

if ($sTipo == "R") {

  $sCamposTp  = " ed47_i_codigo, ed47_v_nome, ed60_d_datamatricula, 'TRANSFERÊNCIA REDE' as tipotransf,";
  $sCamposTp .= " ed57_i_calendario, ed60_i_codigo as codmatricula, ed57_c_descr  ";
  $sWhereTp   = " ed57_i_escola in ($iEscola) AND ed60_d_datamatricula BETWEEN '$dDataInicio' AND '$dDataFinal' ";
  $sWhereTp  .= " AND ed229_c_procedimento = 'MATRICULAR ALUNOS TRANSFERIDOS' ";
  $sOrderTp   = " to_ascii(ed47_v_nome) ";
  $sSql       = $oDaoMatricula->sql_query_tipotransf("", $sCamposTp, $sOrderTp, $sWhereTp);
  $rs         = $oDaoMatricula->sql_record($sSql);
  $iLinhas    = $oDaoMatricula->numrows;
  $head2      = "Tipo: TRANSFERÊNCIA REDE";

} else if ($sTipo == "F") {

  $sCamposTp  = " ed47_i_codigo,ed47_v_nome, ed60_d_datamatricula, 'TRANSFERÊNCIA FORA' as tipotransf,";
  $sCamposTp .= " ed57_i_calendario, ed60_i_codigo as codmatricula, ed57_c_descr";
  $sWhereTp   = " ed57_i_escola in ($iEscola) AND ed60_d_datamatricula BETWEEN '$dDataInicio' AND '$dDataFinal' ";
  $sWhereTp  .= " AND ed229_c_procedimento = 'MATRICULAR ALUNO' ";
  $sWhereTp  .= " AND ed229_t_descr like '% ANTERIOR: TRANSFERIDO FORA%'";
  $sOrderTp   = " to_ascii(ed47_v_nome) ";
  $sSql       = $oDaoMatricula->sql_query_tipotransf("", $sCamposTp, $sOrderTp, $sWhereTp);
  $rs         = $oDaoMatricula->sql_record($sSql);
  $iLinhas    = $oDaoMatricula->numrows;
  $head2   = "Tipo: TRANSFERÊNCIA FORA";

} else {

  $sCamposTp  = " ed47_i_codigo, to_ascii(ed47_v_nome) as ed47_v_nome,ed60_d_datamatricula,";
  $sCamposTp .= " 'TRANSFERÊNCIA REDE' as tipotransf, ed57_i_calendario, ed60_i_codigo as codmatricula, ed57_c_descr";
  $sWhereTp   = " ed57_i_escola in ($iEscola) AND ed60_d_datamatricula BETWEEN '$dDataInicio' AND '$dDataFinal' ";
  $sWhereTp  .= " AND ed229_c_procedimento = 'MATRICULAR ALUNOS TRANSFERIDOS' ";
  $sSqlTp     = $oDaoMatricula->sql_query_tipotransf("", $sCamposTp, "", $sWhereTp);

  $sCamposTp2  = " ed47_i_codigo, to_ascii(ed47_v_nome) as ed47_v_nome, ed60_d_datamatricula,";
  $sCamposTp2 .= " 'TRANSFERÊNCIA FORA' as tipotransf, ed57_i_calendario, ed60_i_codigo as codmatricula, ed57_c_descr";
  $sWhereTp2   = " ed57_i_escola in ($iEscola) ";
  $sWhereTp2  .= " AND ed60_d_datamatricula BETWEEN '$dDataInicio' AND '$dDataFinal' ";
  $sWhereTp2  .= " AND ed229_c_procedimento = 'MATRICULAR ALUNO' ";
  $sWhereTp2  .= " AND ed229_t_descr like '% ANTERIOR: TRANSFERIDO FORA%' ";
  $sOrderTp2   = " ed47_v_nome ";
  $sSqlTp2     = $oDaoMatricula->sql_query_tipotransf("", $sCamposTp2, $sOrderTp2, $sWhereTp2);

  $sSqlUnion  = $sSqlTp;
  $sSqlUnion .= " UNION ";
  $sSqlUnion .= $sSqlTp2;
  $rs         = $oDaoMatricula->sql_record($sSqlUnion);
  $iLinhas    = $oDaoMatricula->numrows;
  $head2      = "Tipo: TODAS";

}

if ($iLinhas == 0) {

  echo " <table width='100%'> ";
  echo "  <tr>";
  echo "   <td align='center'>";
  echo "    <font color='#FF0000' face='arial'>";
  echo "     <b>Nenhuma registro encontrado.<br>";
  echo "     <input type='button' value='Fechar' onclick='window.close()'></b>";
  echo "    </font>";
  echo "   </td>";
  echo "  </tr>";
  echo " </table>";
  exit;

}

$head1  = "RELATÓRIO DE ENTRADAS POR TRANSFERÊNCIA";
$head3  = "Período: ".db_formatar($dDataInicio, 'd')." até ".db_formatar($dDataFinal, 'd');
$oPdf   = new Pdf();
$oPdf->Open();
$oPdf->AliasNbPages();
$lTroca = true;
$lCor   = true;
for ($iCont = 0; $iCont < $iLinhas; $iCont++) {

  $oDadosTpTransf = db_utils::fieldsmemory($rs, $iCont);

  if ($oPdf->gety() > $oPdf->h - 30 || $lTroca != 0 ) {

    $oPdf->addpage('L');
    $oPdf->setfillcolor(215);
    $oPdf->setfont('arial', 'B', 8);
    $oPdf->cell(15, 5, "Código", 1, 0, "C", 1);
    $oPdf->cell(70, 5, "Aluno", 1, 0, "C", 1);
    $oPdf->cell(30, 5, "Turma Destino", 1, 0, "C", 1);
    $oPdf->cell(15, 5, "Data", 1, 0, "C", 1);
    $oPdf->cell(35, 5, "Tipo", 1, 0, "C", 1);
    $oPdf->cell(115, 5, "Código - Escola Origem", 1, 1, "C", 1);
    $lTroca = false;

 }

 if ($lCor == false) {
   $lCor = true;
 } else {
   $lCor = false;
 }
 
 if ($oDadosTpTransf->tipotransf == "TRANSFERÊNCIA REDE") {

   $sCamposTransf  = " escola.ed18_c_nome as nomeescola, ed18_i_codigo ";
   $sWhereTransf   = " ed60_i_aluno = $oDadosTpTransf->ed47_i_codigo ";
   $sWhereTransf  .= " AND ed102_i_escola in ($iEscola) AND ed102_i_calendario = $oDadosTpTransf->ed57_i_calendario ";
   $sSqlTransf     = $oDaoTransfEscolaRede->sql_query_tipotransferido("", $sCamposTransf, "", $sWhereTransf);
   $rsTransf       = $oDaoTransfEscolaRede->sql_record($sSqlTransf);
   $iLinhasTransf  = $oDaoTransfEscolaRede->numrows;

   if ($iLinhasTransf > 0) {
     $oDadosTransf = db_utils::fieldsmemory($rsTransf, 0);
   } else {
     $oDadosTransf->nomeescola = "";
   }

 } else {

   $sCamposTransf  = " escolaproc.ed82_c_nome as nomeescola, ed82_i_codigo ";
   $sInnerTransf   = " inner join escolaproc on ed82_i_codigo = ed104_i_escoladestino ";
   $sWhereTransf   = " ed104_i_aluno = $oDadosTpTransf->ed47_i_codigo ";
   $sSqlTransf     = $oDaoTransfEscolaFora->sql_query_file("", $sCamposTransf).$sInnerTransf.' where '.$sWhereTransf;

   $rsTransf       = $oDaoTransfEscolaFora->sql_record($sSqlTransf);
   $iLinhasTransf  = $oDaoTransfEscolaFora->numrows;

   if ($iLinhasTransf > 0) {
     $oDadosTransf = db_utils::fieldsmemory($rsTransf, 0);
   } else {
     $oDadosTransf->nomeescola = "";
   }

 }

 $codEscola = !empty($oDadosTransf->ed18_i_codigo) ? $oDadosTransf->ed18_i_codigo : $oDadosTransf->ed82_i_codigo;
 
 $oPdf->setfillcolor(230);
 $oPdf->setfont('arial', '', 7);
 $oPdf->cell(15, 5, $oDadosTpTransf->ed47_i_codigo, 0, 0, "C", $lCor);
 $oPdf->cell(70, 5, $oDadosTpTransf->ed47_v_nome, 0, 0, "L", $lCor);
 strlen($oDadosTpTransf->ed57_c_descr) > 21 ? $oPdf->setfont('arial', '', 5) : $oPdf->setfont('arial', '', 7);
 $oPdf->cell(30, 5, $oDadosTpTransf->ed57_c_descr, 0, 0, "L", $lCor);
 $oPdf->setfont('arial', '', 7);
 $oPdf->cell(15, 5, db_formatar($oDadosTpTransf->ed60_d_datamatricula, 'd'), 0, 0, "C", $lCor);
 $oPdf->cell(35, 5, $oDadosTpTransf->tipotransf, 0, 0, "C", $lCor);
 $escola = $codEscola. " - " . $oDadosTransf->nomeescola;
 $oPdf->cell(115, 5, $escola, 0, 1, "E", $lCor);
}
$oPdf->setfillcolor(215);
$oPdf->setfont('arial', 'B', 9);
$oPdf->cell(280, 5, "Quantidade no período de ".db_formatar($dDataInicio, 'd')." até ".db_formatar($dDataFinal, 'd').
            ": ".$iLinhas." entradas por transferência", 1, 1, "C", 1
           );
$oPdf->Output();
?>