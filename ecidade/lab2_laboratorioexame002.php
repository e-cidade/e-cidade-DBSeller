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
require_once(modification("classes/db_lab_laboratorio_classe.php"));

$cllab_laboratorio = new cl_lab_laboratorio;
$sCampos     =  " la02_i_codigo, ";
$sCampos    .= "la02_c_descr ";
$aWhere = [];
$sOrder      =  "";


if(isset($ordem) && $ordem == 0 ){
  if(isset($imprimirExames) && $imprimirExames){
    $sOrder = "la02_c_descr,la08_c_descr";
  } else {
    $sOrder = "la02_c_descr";
  }
} else if(isset($ordem) && $ordem == 1){
  if(isset($imprimirExames) && $imprimirExames){
    $sOrder = "la02_i_codigo,la08_i_codigo";
  } else {
    $sOrder = "la02_i_codigo";
  }
}

if (isset($imprimirExames) && $imprimirExames) {
  $sCampos .= " ,la08_i_codigo,";
  $sCampos .= " la08_c_sigla,";
  $sCampos .= " la08_c_descr,";
  $sCampos .= " la08_i_ativo,";
  $sCampos .= " la09_i_ativo";
} else {
  $sCampos = " distinct " . $sCampos;
}

if (isset($situacaoExames) && isset($imprimirExames) && $imprimirExames ) {
  if($situacaoExames == 1) {
    $aWhere[] = "(la08_i_ativo = 1 AND la09_i_ativo = 1)";
  } else if($situacaoExames == 2) {
    $aWhere[] = "(la08_i_ativo = 2 OR la09_i_ativo = 2)";
  }
}

if(isset($laboratorios) && !empty($laboratorios)){
  $aWhere[] .= " la02_i_codigo in ($laboratorios)";
}

$sWhere = implode(' AND ',$aWhere);
$result  =  db_query($cllab_laboratorio->sql_query_labexames(null, $sCampos, $sOrder, $sWhere));
if (pg_num_rows($result) == 0) {

  ?>
  <table width='100%'>
    <tr>
      <td align='center'>
        <font color='#FF0000' face='arial'>
          <b>Nenhum registro encontrado<br>
          <input type='button' value='Fechar' onclick='window.close()'></b>
        </font>
      </td>
    </tr>
  </table>
    <?php
  exit;

}
$ex = 0;
$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$head1 = "Relatório de Laboratórios";
$head3 = "LISTAR EXAMES VINCULADOS: ";
$head4 = "SITUAÇÃO EXAMES: ";
if (isset($imprimirExames) && $imprimirExames == "1") {
  $head3 .= ($imprimirExames == 1) ? "SIM" : "NÃO";
} else {
  $head3 .= "NÃO";
}
if(isset($situacaoExames)) {

  if ($situacaoExames == 1) {
    $head4 .= "ATIVOS";
  } else if( $situacaoExames == 2) {
  	$head4 .= "DESATIVADOS";
  } else {
  	$head4 .= "TODOS";
  }

}

$iTotal    = 0;
$iTotallab = 0;
$iCodlab   = -1;
$inicializa = 0; 
for ($s = 0; $s < pg_num_rows($result); $s++) {

  db_fieldsmemory($result, $s);

  if (($pdf->gety() > $pdf->getH() - 30) || $inicializa == 0) {
    $pdf->addpage('L');
  }

  if (
      (isset($imprimirExames) && $imprimirExames == 0)
      || $la02_i_codigo != $iCodlab
     ) {
    
    if ($la02_i_codigo != $iCodlab && $iCodlab != -1 && isset($imprimirExames) && $imprimirExames == 1) {

      $pdf->setfont('arial', 'B', 8);
      $pdf->Cell(280, 4, "TOTAL: ".$iTotallab,  1, 1, "R", 0);
      $iTotallab = 0;
    }

    if($la02_i_codigo != $iCodlab) {

      if($inicializa == 1 && isset($imprimirExames) && $imprimirExames == 1){
        $pdf->addpage('L');  
      }

      $pdf->setfont('arial','b',10);
  	  $pdf->SetFillColor(235);
      $sLab    = $la02_i_codigo." - ".$la02_c_descr;
      $iCodlab = $la02_i_codigo;
      $pdf->cell(280, 4, $sLab, 1, 1, "L", 1);
      $pdf->SetFillColor(255);

      if (isset($imprimirExames) && $imprimirExames == 1) {

        $pdf->cell(15,  4, "Código", 1, 0, "L", 0);
        $pdf->cell(20,  4, "Sigla",  1, 0, "L", 0);
        $pdf->Cell(225, 4, "Exame",  1, 0, "L", 0);
        $pdf->cell(20,  4, "Situação",  1, 1, "L", 0);
      }

    }

  }
  if (isset($imprimirExames) && $imprimirExames == 1) {

    $pdf->setfont('arial', '', 8);
    $pdf->cell(15,  4, $la08_i_codigo, 1, 0, "L", 0);
    $pdf->cell(20,  4, $la08_c_sigla,  1, 0, "L", 0);
    $pdf->Cell(225, 4, $la08_c_descr,  1, 0, "L", 0);
    if ($la08_i_ativo == 1 && $la09_i_ativo == 1) {
      $pdf->cell(20,  4, "ATIVO",  1, 1, "L", 0);
    } else {
      $pdf->cell(20,  4, "DESATIVADO",  1, 1, "L", 0);
    }
    $iTotal++;
    $iTotallab++;
    if ($s + 1 == pg_num_rows($result)) {

      $pdf->setfont('arial', 'B', 8);
      $pdf->Cell(280, 4, "TOTAL: ".$iTotallab,  1, 1, "R", 0);
      $pdf->setfont('arial', 'B', 10);
      $pdf->Cell(280, 4, "TOTAL: ".$iTotal,  1, 1, "L", 0);

    }
  }

  $inicializa = 1;
}
$pdf->Output();
?>
