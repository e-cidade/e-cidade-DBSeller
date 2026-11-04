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

include(modification("fpdf151/scpdf.php"));
include(modification("classes/db_contlot_classe.php"));
include(modification("classes/db_contrib_classe.php"));
include(modification("classes/db_contlotv_classe.php"));
include(modification("classes/db_contricalc_classe.php"));
include(modification("classes/db_editalserv_classe.php"));
include(modification("classes/db_editalrua_classe.php"));
include(modification("libs/db_sql.php"));
$clcontlot = new cl_contlot;
$clcontrib = new cl_contrib;
$clcontricalc = new cl_contricalc;
$clcontlotv = new cl_contlotv;
$cleditalserv = new cl_editalserv;
$cleditalrua = new cl_editalrua;
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$sSqlEditalRua = $cleditalrua->sql_query("","d01_numero,d02_contri,j14_nome,d01_data","j14_nome","d02_codedi=$edital");
$rsEditalRua   = $cleditalrua->sql_record($sSqlEditalRua);




$result = $cleditalrua->sql_record($sSqlEditalRua);
$num =   $cleditalrua->numrows;




$iAlturalinha = 4;
$iFonte       = 6;

for ( $iEditalRua = 0; $iEditalRua < $cleditalrua->numrows; $iEditalRua++ ) {


  $oPdf = new pdf();
  $oPdf->SetFillColor( 235 );
  $oPdf->Open();
  $oPdf->AliasNbPages();
  
  
  
  $oDadosEditalRua = db_utils::fieldsMemory($rsEditalRua, $iEditalRua);

  switch($iOrdena) {
    case  0 :
      $cSqlOrdena = 'j01_matric';
      break; 
    case  1 :
      $cSqlOrdena = 'z01_nome';
      break;   
    case  2 :
      $cSqlOrdena = 'z01_nome,j01_matric';
      break;      
    }
 
  $sSqlContribuicao = "
                          SELECT DISTINCT 
                          d07_valor,
                          d06_valor,
                          d07_venal,
                          d09_numpre,
                          d40_codigo, 
                          d41_eixo,
                          j14_nome,
                          d41_testada,
                          (
                              select coalesce(sum(arrepaga.k00_valor),0) as valor_pago 
                                from (
                                           select k00_valor ,
                                                  k00_numpre,
                                                  k00_numpar,
                                                  k00_receit
                                             from arrecad 
                                            where k00_numpre = d09_numpre
                                         union all
                                           select k00_valor ,
                                                  k00_numpre,
                                                  k00_numpar,
                                                  k00_receit
                                             from arrecant 
                                            where k00_numpre = d09_numpre
                                         
                                      ) as x 
                                       left join arrepaga on x.k00_numpre = arrepaga.k00_numpre
                                                         and x.k00_numpar = arrepaga.k00_numpar
                                                         and x.k00_receit = arrepaga.k00_receit 
                                           where x.k00_numpre = d09_numpre                       
                          ) + coalesce((select round(sum(k00_valor*(k00_perc/100)),2) from contricalc inner join termocontrib on d09_sequencial = contricalc inner join termo on parcel = v07_parcel inner join arrepaga on arrepaga.k00_numpre = v07_numpre inner join arrematric on arrepaga.k00_numpre = arrematric.k00_numpre and arrematric.k00_matric = j01_matric where d09_contri = {$oDadosEditalRua->d02_contri} and d09_matric = j01_matric),0) as valor_pago,

(select coalesce(sum(x.k00_valor),0) as valor_a_pagar
                                from (
                                           select k00_valor ,
                                                  k00_numpre,
                                                  k00_numpar,
                                                  k00_receit
                                             from arrecad 
                                            where k00_numpre = d09_numpre
                          ) as x) 
                     + coalesce((select round(sum(k00_valor*(k00_perc/100)),2) from contricalc inner join termocontrib on d09_sequencial = contricalc inner join termo on parcel = v07_parcel inner join arrecad on arrecad.k00_numpre = v07_numpre inner join arrematric on arrecad.k00_numpre = arrematric.k00_numpre and arrematric.k00_matric = j01_matric where d09_contri = {$oDadosEditalRua->d02_contri} and d09_matric = j01_matric),0) as valor_a_pagar,

                          (d41_testada + d41_eixo) as total_testada,
                          j34_area,
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
                          j36_testad,
                          j34_idbql
                     FROM edital
               INNER JOIN editalrua ON d02_codedi =d01_codedi
               INNER JOIN editalproj ON d10_codedi = d01_codedi
               INNER JOIN editalruaproj ON d11_contri = d02_contri
               INNER JOIN projmelhorias ON d10_codigo = d40_codigo
               inner join ruas on d02_codigo = j14_codigo
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
               inner join contricalc on d09_contri = d02_contri
                          and d09_matric = j01_matric
               inner join contrib on  d07_contri = d02_contri
                          and d07_matric  = j01_matric
                    where d02_contri = {$oDadosEditalRua->d02_contri}
                    order by {$cSqlOrdena}
                          
            ";

//echo $sSqlContribuicao; die();

    $rsContribuicao = db_query($sSqlContribuicao);         
    
    if ( pg_numrows($rsContribuicao) > 0  ) {

      $head2 = "Relatório de Valores Da Contribuição";
      $head3 = "Contribuição: $oDadosEditalRua->d02_contri ";
      $head4 = "Rua: $oDadosEditalRua->j14_nome";
      $head5 = "Registros da Contribuição: " . pg_numrows($rsContribuicao);

      $oPdf->AddPage("L");
      imprimirCabecalho($oPdf, $iAlturalinha, true);

      $nTotalVenal   = 0;
      $nTotalContrib = 0;
      $nTotalPago    = 0;
      $nTotalSaldo   = 0;
      $nDesconto     = 0;


      for ( $iContribuicao = 0; $iContribuicao < pg_numrows($rsContribuicao); $iContribuicao++ ) {

        $oDadosContribuicao = db_utils::fieldsMemory($rsContribuicao, $iContribuicao);

        $iFill = 1;
        if ( ($iContribuicao % 2) == 0 ) {
          $iFill = 0;
        }

        $oPdf->setfont('arial','',$iFonte);

        $nSaldo    = $oDadosContribuicao->d07_valor - $oDadosContribuicao->valor_pago;
        $nDesconto = $oDadosContribuicao->d06_valor - $oDadosContribuicao->d07_valor;

        //echo "<br>d06:$oDadosContribuicao->d06_valor | d07:  $oDadosContribuicao->d07_valor =  $nDesconto";

        $oPdf->cell(15,  $iAlturalinha, $oDadosContribuicao->j01_matric                    ,  "", 0, "R", $iFill);
        $oPdf->cell(70,  $iAlturalinha, $oDadosContribuicao->z01_nome                      ,  "", 0, "L", $iFill);
        $oPdf->cell(20,  $iAlturalinha, $oDadosContribuicao->j34_setor                     ,  "", 0, "C", $iFill);
        $oPdf->cell(20,  $iAlturalinha, $oDadosContribuicao->j34_quadra                    ,  "", 0, "C", $iFill);
        $oPdf->cell(20,  $iAlturalinha, $oDadosContribuicao->j34_lote                      ,  "", 0, "C", $iFill);
        $oPdf->cell(20,  $iAlturalinha, $oDadosContribuicao->j34_zona                      ,  "", 0, "C", $iFill);
        $oPdf->cell(20,  $iAlturalinha, $oDadosContribuicao->total_testada                 ,  "", 0, "R", $iFill);
        $oPdf->cell(20,  $iAlturalinha, db_formatar($oDadosContribuicao->d07_venal, "f")   ,  "", 0, "R", $iFill);
        $oPdf->cell(25,  $iAlturalinha, db_formatar($oDadosContribuicao->d07_valor, "f")   ,  "", 0, "R", $iFill);
        $oPdf->cell(25,  $iAlturalinha, db_formatar($oDadosContribuicao->valor_pago, "f")  ,  "", 0, "R", $iFill);
        $oPdf->cell(25,  $iAlturalinha, db_formatar($oDadosContribuicao->valor_a_pagar , "f")                         ,  "", 1, "R", $iFill);

        $nTotalVenal   += $oDadosContribuicao->d07_venal;
        $nTotalContrib += $oDadosContribuicao->d07_valor;
        $nTotalPago    += $oDadosContribuicao->valor_pago;
        $nTotalSaldo   += $oDadosContribuicao->valor_a_pagar;

        imprimirCabecalho($oPdf, $iAlturalinha, false);

      }

      $oPdf->setfont('arial','b',$iFonte);
      $oPdf->cell(185,  $iAlturalinha, "Total: "                         ,  "", 0, "R", 1 );
      $oPdf->cell(20,  $iAlturalinha, db_formatar( $nTotalVenal  , "f")  ,  "", 0, "R", 1 );
      $oPdf->cell(25,  $iAlturalinha, db_formatar( $nTotalContrib, "f")  ,  "", 0, "R", 1 );
      $oPdf->cell(25,  $iAlturalinha, db_formatar( $nTotalPago   , "f")  ,  "", 0, "R", 1 );
      $oPdf->cell(25,  $iAlturalinha, db_formatar( $nTotalSaldo  , "f")  ,  "", 1, "R", 1 );

    }



}


$oPdf->Output();

function imprimirCabecalho($oPdf, $iAlturalinha, $lImprime) {

  if ( $oPdf->GetY() > $oPdf->h - 25 || $lImprime ) {

      $oPdf->SetFont('arial', 'b', 6);

      if ( !$lImprime ) {

          $oPdf->AddPage("P");
      }

      $oPdf->setfont('arial','b',6);
      $oPdf->cell(15,  $iAlturalinha, "MATRÍCULA"    ,  "", 0, "C", 1);
      $oPdf->cell(70,  $iAlturalinha, "PROPRIETÁRIO" ,  "", 0, "C", 1);
      $oPdf->cell(20,  $iAlturalinha, "SETOR"        ,  "", 0, "C", 1);
      $oPdf->cell(20,  $iAlturalinha, "QUADRA"       ,  "", 0, "C", 1);
      $oPdf->cell(20,  $iAlturalinha, "LOTE"         ,  "", 0, "C", 1);
      $oPdf->cell(20,  $iAlturalinha, "ZONA"         ,  "", 0, "C", 1);
      $oPdf->cell(20,  $iAlturalinha, "TESTADA"      ,  "", 0, "C", 1);
      $oPdf->cell(20,  $iAlturalinha, "VLR. VENAL"   ,  "", 0, "C", 1);
      $oPdf->cell(25,  $iAlturalinha, "VLR. COTRIB." ,  "", 0, "C", 1);
      $oPdf->cell(25,  $iAlturalinha, "VLR. PAGO"    ,  "", 0, "C", 1);
      $oPdf->cell(25,  $iAlturalinha, "SALDO"        ,  "", 1, "C", 1);

  }
}



die();

















$lin=0;
$pri=false;
$pripag="true";
$pdf = new SCPDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(220);
$pdf->SetFont('Arial','',11);
db_fieldsmemory($result,0);
$head1 = "EDITAL:$d01_numero  DATA:$d01_data";

$nTotalContribuicao = 0;

$totpago   = "";
$totdevido = "";
$cont      = 0;
$contot    = 0;

for($i=0;$i<$num;$i++) {
  
  db_fieldsmemory($result,$i);



  $sSqlContriCalc = $clcontricalc->sql_query_file(null,"d09_contri",null,"d09_contri = $d02_contri");
  $result03       = $clcontricalc->sql_record($sSqlContriCalc);

  //echo $sSqlContriCalc; die();



   if($clcontricalc->numrows>0){
      $existe_calculado="sim"; 
   }
   if($clcontricalc->numrows>0){
     $cabec="";
     $pri01="false";
     $propag="true";


     $sSqlContLot = "
                 select distinct	
                                  j01_matric,
                                  z01_nome,
                                  lote.j34_idbql,
                                  lote.j34_area,
                                  j34_setor,
                                  j34_quadra,
                                  j34_lote,
                                  j34_zona,
                                  d05_testad
                             from contlot
                       inner join lote on j34_idbql = d05_idbql
                       inner join iptubase on j34_idbql = j01_idbql
                       inner join cgm on j01_numcgm = z01_numcgm
                            where d05_contri = $d02_contri 
                         order by j34_idbql
    ";

     $result01 = pg_query($sSqlContLot);
     $numrows01 = pg_numrows($result01);


     $linha = 60;
     if($pri01=="false"){// testa quando e uma nova contribucao
  
        $pri01="true";	
        $y=$pdf->GetY();
        if($y>160 || $pripag=="true"){
  	  $pripag="false";
          $pdf->AddPage("L");
    	  $propag="false";
        } 
        $cabec="1";
        $pdf->SetFont('Arial','B',7);
        $reso= $cleditalrua->sql_record($cleditalrua->sql_query($d02_contri,"d02_codedi,j14_nome,d02_profun"));
        db_fieldsmemory($reso,0);
        $pdf->ln();
        $pdf->Cell("60",6,"CONTRIBUIÇÃO:".$d02_contri,1,0,"L",1);
        $pdf->Cell("205",6,"RUA:".$j14_nome,1,1,"L",1);
        $pdf->Cell(60,4,"PROPRIETÁRIO",1,0,"C",1);
        $pdf->Cell(16,4,"MATRICULA",1,0,"C",1);
        $pdf->Cell(10,4,"SETOR",1,0,"C",1);
        $pdf->Cell(12,4,"QUADRA",1,0,"C",1);
        $pdf->Cell(8,4,"LOTE",1,0,"C",1);
        $pdf->Cell(8,4,"ZONA",1,0,"C",1);
        $pdf->Cell(16,4,"TESTADA",1,0,"C",1);
        $pdf->Cell(12,4,"AREA",1,0,"C",1);
        $pdf->Cell(17,4,"VALOR",1,0,"C",1);
        $pdf->Cell(18,4,"VALOR",1,0,"C",1);
        $pdf->Cell(28,4,"VALOR DA ",1,0,"C",1);
        $pdf->Cell(30,4,"VALOR DEVIDO",1,0,"C",1);
        $pdf->Cell(30,4,"VALOR PAGO",1,1,"C",1);
        $pdf->Cell(60,4,"","LRB",0,"C",1);
        $pdf->Cell(16,4,"","LRB",0,"C",1);
        $pdf->Cell(10,4,"","LRB",0,"C",1);
        $pdf->Cell(12,4,"","LRB",0,"C",1);
        $pdf->Cell(8,4,"","LRB",0,"C",1);
        $pdf->Cell(8,4,"","LRB",0,"C",1);
        $pdf->Cell(16,4,"EM METROS","LRB",0,"C",1);
        $pdf->Cell(12,4,"EM M2","LRB",0,"C",1);
        $pdf->Cell(17,4,"VENAL EM R$","LRB",0,"C",1);
        $pdf->Cell(18,4,"POR M2 EM R$","LRB",0,"C",1);
        $pdf->Cell(28,4,"CONTRIBUIÇÃO EM R$","LRB",0,"C",1);
        $pdf->Cell(30,4,"","LRB",0,"C",1);
        $pdf->Cell(30,4,"","LRB",1,"C",1);
     }   
     $pri02="false";   
     for($b=0; $b<$numrows01; $b++){
      $y02=$pdf->getY();
      $Letra = 'Times';
      if($y02>180){
        $pdf->AddPage("L");
	$sql = "select nomeinst,bairro,cgc,ender,upper(munic) as munic,uf,telef,email,url,logo,db12_extenso
			from db_config 
			inner join db_uf on db12_uf=uf
			where codigo = ".db_getsession("DB_instit");
	$result05 = db_query($sql);
	global $nomeinst;
	global $ender;
	global $munic;
	global $cgc;
	global $bairro;
	global $uf;
	global $logo;
	//echo $sql;
	db_fieldsmemory($result05,0);
	/// seta a margem esquerda que veio do relatorio
	$S = $pdf->lMargin;
	$pdf->SetLeftMargin(10);
	$posini = ($pdf->w/2)-12;
	$pdf->Image("imagens/files/".$logo,$posini,8,24);
	//$pdf->Image('imagens/files/'.$logo,2,3,30);
	$pdf->Ln(35);
	$pdf->SetFont($Letra,'',10);
	$pdf->MultiCell(0,4,$db12_extenso,0,"C",0);
	$pdf->SetFont($Letra,'B',13);
	$pdf->MultiCell(0,6,$nomeinst,0,"C",0);
	$pdf->SetFont($Letra,'B',12);
	$pdf->MultiCell(0,4,@$GLOBALS["head1"],0,"C",0);
	$pdf->Ln(10);
	$pdf->SetLeftMargin($S);
        $pri02="false";
        $cabec="";
        $propag="true";
      }  
    $pdf->SetFont($Letra,'',7);
    $pdf->line(10,$pdf->h-12,290,$pdf->h-12);
    $pdf->text(10,$pdf->h-8,'Base: '.@$GLOBALS["DB_NBASE"]);
//    $pdf->text(90,$pdf->h-8,$nome.'     Emissor: '.@$GLOBALS["DB_login"].'     Exercício: '.db_getsession("DB_anousu").'    Data: '.date("d-m-Y",db_getsession("DB_datausu"))." - ".date("H:i:s"));
    $pdf->text(270,$pdf->h-8,'Página '.$pdf->PageNo().' de {nb}',0,1,'R');
    $pdf->SetFont($Letra,'B',12);
      if($pri02=="false" && $propag=="true" && $cabec!="1"){  
        $pri02="true";
        $pdf->SetFont('Arial','B',7);
        $reso= $cleditalrua->sql_record($cleditalrua->sql_query($d02_contri,"d02_codedi,j14_nome,d02_profun"));
        db_fieldsmemory($reso,0);
        $pdf->Cell("60",6,"CONTRIBUIÇÃO:".$d02_contri,1,0,"L",1);
        $pdf->Cell("205",6,"RUA:".$j14_nome,1,1,"L",1);
        $pdf->Cell(60,4,"PROPRIETÁRIO",1,0,"C",1);
        $pdf->Cell(16,4,"MATRICULA",1,0,"C",1);
        $pdf->Cell(10,4,"SETOR",1,0,"C",1);
        $pdf->Cell(12,4,"QUADRA",1,0,"C",1);
        $pdf->Cell(8,4,"LOTE",1,0,"C",1);
        $pdf->Cell(8,4,"ZONA",1,0,"C",1);
        $pdf->Cell(16,4,"TESTADA",1,0,"C",1);
        $pdf->Cell(12,4,"AREA",1,0,"C",1);
        $pdf->Cell(17,4,"VALOR",1,0,"C",1);
        $pdf->Cell(18,4,"VALOR",1,0,"C",1);
        $pdf->Cell(28,4,"VALOR DA ",1,0,"C",1);
        $pdf->Cell(30,4,"VALOR DEVIDO",1,0,"C",1);
        $pdf->Cell(30,4,"VALOR PAGO",1,1,"C",1);
        $pdf->Cell(60,4,"","LRB",0,"C",1);
        $pdf->Cell(16,4,"","LRB",0,"C",1);
        $pdf->Cell(10,4,"","LRB",0,"C",1);
        $pdf->Cell(12,4,"","LRB",0,"C",1);
        $pdf->Cell(8,4,"","LRB",0,"C",1);
        $pdf->Cell(8,4,"","LRB",0,"C",1);
        $pdf->Cell(16,4,"EM METROS","LRB",0,"C",1);
        $pdf->Cell(12,4,"EM M2","LRB",0,"C",1);
        $pdf->Cell(17,4,"VENAL EM R$","LRB",0,"C",1);
        $pdf->Cell(18,4,"POR M2 EM R$","LRB",0,"C",1);
        $pdf->Cell(28,4,"CONTRIBUIÇÃO EM R$","LRB",0,"C",1);
        $pdf->Cell(30,4,"","LRB",0,"C",1);
        $pdf->Cell(30,4,"","LRB",1,"C",1);
      }

      db_fieldsmemory($result01,$b);
      
      
      $sSqlContriCalc = $clcontricalc->sql_query_file(null, "d09_contri,d09_numpre", "d09_contri = $d02_contri and d09_matric = $j01_matric");
      $result04=$clcontricalc->sql_record($sSqlContriCalc);
      //echo  $sSqlContriCalc; die();


      if($clcontricalc->numrows>0){
	          $cont++;
            $result02= $clcontrib->sql_record($clcontrib->sql_query_file($d02_contri,$j01_matric,"d07_valor,d07_venal"));
	          db_fieldsmemory($result02,0);
	          $m2 = ($d02_profun * $d05_testad); 
	
            $result07= $cleditalserv->sql_record($cleditalserv->sql_query($d02_contri,"","d04_vlrcal,d04_mult"));
            $numrows07=$cleditalserv->numrows;
           	$valmetro="";
           	for($u=0; $u<$numrows07; $u++){
           	  db_fieldsmemory($result07,$u);
           	  $valmetro+=$d04_vlrcal;
           	}
        $pdf->SetFont('Times','',6);
        $pdf->Cell(60,4,substr($z01_nome,0,35),1,0,"L",0);
        $pdf->Cell(16,4,$j01_matric,1,0,"C",0);
        $pdf->Cell(10,4,$j34_setor,1,0,"C",0);
        $pdf->Cell(12,4,$j34_quadra,1,0,"C",0);
        $pdf->Cell(8,4,$j34_lote,1,0,"C",0);
        $pdf->Cell(8,4,$j34_zona,1,0,"C",0);
        $pdf->Cell(16,4,db_formatar($d05_testad,'p'),1,0,"C",0);
        $pdf->Cell(12,4,db_formatar($m2,'p'),1,0,"C",0);
        $pdf->Cell(17,4,db_formatar($d07_venal,'f'),1,0,"C",0);
        $pdf->Cell(18,4,db_formatar($valmetro,'f'),1,0,"C",0);
        $pdf->Cell(28,4,db_formatar($d07_valor,'f') ,1,0,"C",0);


        $nTotalContribuicao += $d07_valor;


	     db_fieldsmemory($result04,0);
       $result09  = debitos_numpre($d09_numpre,0,0,db_getsession("DB_datausu"),db_getsession("DB_anousu"),0,"1") or die($d09_numpre);
  
        db_fieldsmemory($result09,0);
        $result08= pg_query("select sum(k00_valor) from arrepaga where k00_numpre = $d09_numpre");

        db_fieldsmemory($result08,0);

        $pdf->Cell(30,4,db_formatar($total,'f') ,1,0,"C",0);
        $pdf->Cell(30,4,db_formatar($sum,'f'),1,0,"C",0);
        $pdf->ln();
	      if($numrows01==($b+1)){
            $pdf->SetFont('Arial','B',6);
            $pdf->Cell(60,4,"REGISTROS DA CONTRIBUIÇÃO:".($cont),0,0,"L",0);
	          $contot += $cont;
	          $cont=0;
	      }
	$totpago+=$sum;
	$totdevido+=$total;
      }
     }   
   }  










}
if(empty($existe_calculado)){
  echo "
    <script>
     window.close();
     opener.alert('Não existe contribuição calculada para este edital.');
    </script>
  ";
}
$pdf->SetFont('Arial','B',7);
$pdf->Cell(16,4,"",0,0,"C",0);
$pdf->Cell(10,4,"",0,0,"C",0);
$pdf->Cell(12,4,"",0,0,"C",0);
$pdf->Cell(8,4,"",0,0,"C",0);
$pdf->Cell(8,4,"",0,0,"C",0);
$pdf->Cell(16,4,"",0,0,"C",0);
$pdf->Cell(12,4,"",0,0,"C",0);
$pdf->Cell(17,4,"",0,0,"C",0);
$pdf->Cell(18,4,"TOTAL",1,0,"C",1);
$pdf->Cell(28,4, db_formatar($nTotalContribuicao, "f")  ,1,0,"C",1);
$pdf->Cell(30,4,db_formatar($totdevido,'f'),1,0,"C",1);
$pdf->Cell(30,4,db_formatar($sum,'f'),1,1,"C",1);
$pdf->Ln();
$pdf->Cell(60,6,"Total de registros do edital:$contot",0,1,"L",0);
$pdf->Ln(5);
$pdf->Output();

?>
