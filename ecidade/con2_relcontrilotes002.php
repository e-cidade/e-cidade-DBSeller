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

require_once modification(("fpdf151/pdf.php"));
include(modification("classes/db_contlot_classe.php"));
include(modification("classes/db_contlotv_classe.php"));
include(modification("classes/db_editalserv_classe.php"));
include(modification("classes/db_editalrua_classe.php"));
$clcontlot = new cl_contlot;
$clcontlotv = new cl_contlotv;
$cleditalserv = new cl_editalserv;
$cleditalrua = new cl_editalrua;
$clprojmelhoriasmatric = new cl_projmelhoriasmatric;
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);


$sSqlServico = $cleditalserv->sql_query($contri,"","d04_quant,d04_vlrobra,d04_vlrcal,d04_vlrval,d03_descr");
$rsServico   = $cleditalserv->sql_record($sSqlServico);
$oDadosServico = db_utils::fieldsMemory($rsServico, 0);


$sSqlTotalTestada = "

select sum(d41_testada + d41_eixo) as total_testada 
  from projmelhoriasmatric 
 where projmelhoriasmatric.d41_codigo  in (

                                          select d10_codigo 
                                            from editalrua 
                                      inner join editalproj on d02_codedi = d10_codedi
                                           where d02_contri = {$contri}
                                        )


";

$rsTotalTestada = db_query($sSqlTotalTestada);

$total_testada = 0;
if (  pg_numrows($rsTotalTestada) > 0 ) {

    
  $total_testada = db_utils::fieldsMemory($rsTotalTestada, 0)->total_testada;
}




$reso = $cleditalrua->sql_record($cleditalrua->sql_query($contri,"d02_codedi,d01_numero,j14_nome"));
db_fieldsmemory($reso, 0);



$sSql = "

SELECT DISTINCT 
               d41_eixo,
               d41_testada,
               (d41_testada + d41_eixo) as total_testada,
               d04_quant,
               d04_vlrobra,
               d02_valorizacao,
               contlotv.*,
               d02_profun,
               d10_codigo,
               d01_perc,
               j01_matric,
               j40_refant,
               z01_nome,
               j34_setor,
               j34_quadra,
               j34_lote,
               j34_zona,
               j36_testad AS d05_testad,
               j34_idbql,
               CASE
                   WHEN j36_testle IS NULL
                        OR j36_testle = 0 THEN (select j36_testad from testada where j36_codigo = d40_codlog and j36_idbql = j34_idbql)
                   ELSE (select j36_testad from testada where j36_codigo = d40_codlog and j36_idbql = j34_idbql)
               END AS j36_testad,
               (d41_testada+d41_eixo) AS d41_testada_comeixo
          FROM edital
    INNER JOIN editalrua ON d02_codedi =d01_codedi
    INNER JOIN editalproj ON d10_codedi = d01_codedi
    INNER JOIN editalruaproj ON d11_contri = d02_contri
    INNER JOIN projmelhorias ON d10_codigo = d40_codigo
    INNER JOIN projmelhoriasmatric ON d41_codigo = d40_codigo
    INNER JOIN iptubase ON j01_matric = d41_matric
    INNER JOIN lote ON j01_idbql = j34_idbql
    INNER JOIN testpri ON j49_idbql = j34_idbql
    INNER JOIN testada ON j49_idbql = j36_idbql
                      AND j49_face = j36_face
                      AND j49_codigo = j36_codigo
    INNER JOIN cgm ON j01_numcgm = z01_numcgm
     LEFT JOIN iptuant ON j40_matric = j01_matric
    INNER JOIN contlotv on d06_idbql = j34_idbql
                       and d06_contri = d02_contri
    INNER JOIN editalserv on d04_contri = d02_contri
               where d02_contri = {$contri}
               order by j34_idbql , 
                        d10_codigo, 
                        j01_matric

";

$result = $clcontlot->sql_record($sSql);

if (  pg_numrows( $result ) <= 0  ) {

  db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum Registro Encontrado.");
  exit;
}  

$sServico      = "Serviço: " . $oDadosServico->d03_descr;
$sServicoQuant = "Quant: " . $oDadosServico->d04_quant ;
$sServicoVal   = "Valor: " . trim(db_formatar($oDadosServico ->d04_vlrobra, "f"));

$head2 = "Relatório Lotes por Contribuição";
$head3 = "CONTRIBUIÇÃO: $contri";
$head4 = "EDITAL: $d01_numero";
$head5 = "RUA: $j14_nome";
$head6 = $sServico;
$head7 = $sServicoQuant;
$head8 = $sServicoVal;

$pdf = new pdf();
$pdf->SetFillColor( 235 );
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage("P");

$iAlturalinha = 4;
$iFonte       = 6;


if (  pg_numrows( $result ) > 0  ) {

  $aDados = array();

  for ( $iRegistro = 0; $iRegistro < pg_numrows( $result ); $iRegistro++  ) {

    $oDadosRegistro = db_utils::fieldsMemory($result, $iRegistro );

    (float)$nAreaRealTotal = $oDadosRegistro->d04_quant * $oDadosRegistro->d02_profun;
    (float)$nAreaTotal     = ( $total_testada * $oDadosRegistro->d02_profun ); 
    (float)$nValorM2       = ( $oDadosRegistro->d04_vlrobra / $nAreaRealTotal );
    (float)$nAreaParcial   = ( ($oDadosRegistro->d41_testada + $oDadosRegistro->d41_eixo ) * $oDadosRegistro->d02_profun ); 
    (float)$nAreaCorrigida = ( $nAreaParcial / $nAreaTotal * $nAreaRealTotal );
    (float)$nCusto         = ( ( $nAreaCorrigida * $nValorM2 ) / 100 ) * (100 - $oDadosRegistro->d01_perc);

    $oDados = new stdClass();
    $oDados->idbql         = $oDadosRegistro->d06_idbql;
    $oDados->setor         = $oDadosRegistro->j34_setor;
    $oDados->quadra        = $oDadosRegistro->j34_quadra;
    $oDados->lote          = $oDadosRegistro->j34_lote;
    $oDados->valor_calc    = $nCusto;
    $oDados->valor_contrib = $oDadosRegistro->d06_valor;

    $aDados[$oDadosRegistro->d06_idbql]['dados'] = $oDados;
    $aDados[$oDadosRegistro->d06_idbql]['valor'] += $nCusto;
  } 
}




imprimirCabecalho($pdf, $iAlturalinha, true);

$nTotalCalc = 0;
$nTotalContrib = 0;

$iContador = 0;

foreach($aDados as $aValores) {
  
  foreach($aValores  as $iIndice => $oDadosRegistro) {
    
    $nValorCalc = db_formatar($aValores['valor'], "f");
    
    if ($iIndice == 'dados') {
      
      
      $lFil = 0;
      if ( ($iContador % 2) == 1 ) {

        $lFil = 1;
      } 


      $pdf->SetFont('arial', '', 6);
      $pdf->cell(25,  $iAlturalinha, $oDadosRegistro->idbql         ,  "", 0, "R", $lFil);
      $pdf->cell(25,  $iAlturalinha, $oDadosRegistro->setor         ,  "", 0, "C", $lFil);
      $pdf->cell(25,  $iAlturalinha, $oDadosRegistro->quadra        ,  "", 0, "C", $lFil);
      $pdf->cell(25,  $iAlturalinha, $oDadosRegistro->lote          ,  "", 0, "C", $lFil);
      $pdf->cell(30,  $iAlturalinha, $nValorCalc                    ,  "", 0, "R",  $lFil);
      $pdf->cell(30,  $iAlturalinha, db_formatar($oDadosRegistro->valor_contrib, "f") ,  "", 1, "R",  $lFil);
      imprimirCabecalho($pdf, $iAlturalinha, false);
      
      $nTotalCalc += $aValores['valor'];
      $nTotalContrib += $oDadosRegistro->valor_contrib;
      
      $iContador ++;
    }
  }
}

$pdf->cell(100,  $iAlturalinha, "Total:"        ,  "", 0, "R", 1);
$pdf->cell(30,  $iAlturalinha, db_formatar($nTotalCalc, "f")    ,  "", 0, "R", 1);
$pdf->cell(30,  $iAlturalinha, db_formatar($nTotalContrib, "f") ,  "", 1, "R", 1);

$pdf->Output();

function imprimirCabecalho($oPdf, $iAlturalinha, $lImprime) {

  if ( $oPdf->GetY() > $oPdf->h - 25 || $lImprime ) {

      $oPdf->SetFont('arial', 'b', 6);

      if ( !$lImprime ) {

          $oPdf->AddPage("P");
      }

      $oPdf->setfont('arial','b',6);
      $oPdf->cell(25,  $iAlturalinha, "IDBQL"              ,  "", 0, "C", 1);
      $oPdf->cell(25,  $iAlturalinha, "SETOR"              ,  "", 0, "C", 1);
      $oPdf->cell(25,  $iAlturalinha, "QUADRA"             ,  "", 0, "C", 1);
      $oPdf->cell(25,  $iAlturalinha, "LOTE"               ,  "", 0, "C", 1);
      $oPdf->cell(30,  $iAlturalinha, "VLR CALC."    ,  "", 0, "C", 1);
      $oPdf->cell(30,  $iAlturalinha, "VLR CONTRIB." ,  "", 1, "C", 1);
  }
}

?>
