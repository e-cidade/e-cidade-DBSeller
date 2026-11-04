<?php 
include modification("fpdf151/pdf.php");
include modification("libs/db_sql.php");
include modification("classes/db_fis_auto_classe.php");
include modification("classes/db_fis_autousu_classe.php");

$clauto = new cl_fis_auto;
$clauto2 = new cl_fis_auto;
$autousu = new cl_fis_autousu;
$clrotulo = new rotulocampo;
$clrotulo->label('y50_codauto');
$clrotulo->label('y50_data');
$clrotulo->label('y50_prazorec');
$clrotulo->label('y50_dtvenc');
$clrotulo->label('y27_descr');

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$whereSetor = " and y50_instit = ".db_getsession('DB_instit') ;
if ($setorfiscal != 0) {
  $whereSetor .= " AND y50_setor = $setorfiscal";
}

if (($dt_prazo != "--")){
	$result = $clauto->sql_record($clauto->sql_query(null,"*",null,"y50_data between '$dt_ini' and '$dt_fin' and y50_prazorec <= '$dt_prazo'"."$whereSetor"));
} else {
	$result = $clauto->sql_record($clauto->sql_query(null,"*",null,"y50_data between '$dt_ini' and '$dt_fin'"."$whereSetor"));
}

if ($clauto->numrows == 0){
   db_redireciona('db_erros.php?fechar=true&db_erro=Não foram encontrado registros correspondentes.');
}

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$total = 0;

/*Definindo cabeçalhos*/
$head3 = "Relatório de Autos de Infração por Prazo";
if ($dt_ini != "--") $head7 = "Período = ".db_formatar($dt_ini,'d')." à ".db_formatar($dt_fin,'d')."";
if ($dt_prazo != "--") $head5 = "Prazo Recurso = ".db_formatar($dt_prazo,'d')."";

$pdf->setfillcolor(235);
$pdf->setfont('arial','b',6);
$troca = 1;
$alt = 4;

$numRowsResult = $clauto->numrows;

for($x = 0; $x < $numRowsResult;$x++){

   db_fieldsmemory($result,$x);
   $result2 = $clauto->sql_record($clauto->sql_query_busca2($y50_codauto,"dl_Auto = $y50_codauto and y50_instit = ".db_getsession('DB_instit')));
   $numRowsResult2 = $clauto->numrows;

   for ($j = 0; $j < $numRowsResult2;$j++){
      db_fieldsmemory($result2,$j);
   }

   /*Definindo cabeçalho 1*/
   if ($setorfiscal == 0) $head1 = "RELATÓRIO GERAL";

   /*Atribuinto valor do auto caso calculado*/
   $resultValor = db_query("SELECT  k00_valor
                                    FROM arrecad inner join fiscalizacao.fis_autonumpre
				    ON y17_numpre = k00_numpre and y17_codauto = $y50_codauto");


   $resultValor = db_query("SELECT coalesce(
( SELECT valortotal_levantamento
FROM fc_fis_autodeinfracao_getimpostosservicos(".$y50_codauto.", NULL)), 0) 
+ coalesce((SELECT valor_procedencia
FROM fc_fis_autodeinfracao_getprocedencias(".$y50_codauto." , NULL)), 0) AS total");
   $valorAuto = pg_fetch_array($resultValor);

   $resultusu = db_query( $autousu->sql_query($y50_codauto,null,'nome as nomeresponsavel') );
   db_fieldsmemory($resultusu,0);
   if ($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
      $pdf->addpage('L');
      $pdf->setfont('arial','b',6);
      $pdf->cell(15,$alt,"AUTO INF.",1,0,"C",1);
      $pdf->cell(25,$alt,"IDENTIFICAÇÃO",1,0,"C",1);
      $pdf->cell(15,$alt,"CÓDIGO",1,0,"C",1);
      $pdf->cell(60,$alt,"CONTRIBUINTE",1,0,"C",1);
      $pdf->cell(20,$alt,"DT AUTO",1,0,"C",1);
      $pdf->cell(20,$alt,"DT RECURSO",1,0,"C",1);
      $pdf->cell(20,$alt,"DT VENCTO",1,0,"C",1);
      $pdf->cell(50,$alt," FISCAL RESPONSÁVEL",1,0,"C",1);
      $pdf->cell(40,$alt,$RLy27_descr,1,0,"C",1);
      $pdf->cell(15,$alt,"VALOR",1,1,"C",1);
      $troca = 0;
   }

   if (!empty($valorAuto[0])) $valorAuto[0] = number_format("$valorAuto[0]",2,",",".");

   $pdf->setfont('arial','',6);
   $pdf->cell(15,$alt,@$y50_codauto,0,0,"C",0);
   $pdf->cell(25,$alt,@$dl_identifica,0,0,"C",0);
   $pdf->cell(15,$alt,@$dl_codigo,0,0,"C",0);
   $pdf->cell(60,$alt,@$z01_nome,0,0,"L",0);
   $pdf->cell(20,$alt,db_formatar($y50_data,'d'),0,0,"C",0);
   $pdf->cell(20,$alt,db_formatar($y50_prazorec,'d'),0,0,"C",0);
   $pdf->cell(20,$alt,db_formatar($y50_dtvenc,'d'),0,0,"C",0);
   $pdf->cell(50,$alt,$nomeresponsavel,0,0,"L",0);
   $pdf->cell(40,$alt,$y27_descr,0,0,"L",0);
   $pdf->cell(15,$alt,@$valorAuto[0],0,1,"R",0);
 }

$pdf->Output();

?>
