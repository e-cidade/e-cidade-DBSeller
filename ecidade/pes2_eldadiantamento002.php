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

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
//db_postmemory($HTTP_SERVER_VARS,2);exit;

$head3 = "FUNCIONÁRIOS SEM PONTO DE ADIANTAMENTO";
$head5 = "PERÍODO : ".$mes." / ".$ano;


 $sql = "
select * from
(
select rh02_anousu,
       rh02_mesusu,
       rh02_instit,
       rh01_regist,
       z01_nome,
       rh02_lota,
       rh30_vinculo as vinculo,
       rh27_rubric,
       rh01_clas1,
       coalesce(conta_dias_afasta(rh02_regist,
                                  rh02_anousu,
                                  rh02_mesusu,
                                  ndias(rh02_anousu,rh02_mesusu),
                                  rh02_instit ),'0')::int as afasta,
       coalesce(dias_gozo_ferias( rh02_regist,
                                  rh02_anousu,
                                  rh02_mesusu,
                                  ndias(rh02_anousu, rh02_mesusu),
                                  rh02_instit),'0')::int as ferias
from rhpessoal
     inner join cgm            on rh01_numcgm = z01_numcgm 
     inner join rhpessoalmov   on rh02_anousu = $ano
                              and rh02_mesusu = $mes
                              and rh02_regist = rh01_regist
                              and rh02_instit = ".db_getsession("DB_instit")."
     inner join rhregime       on rh30_codreg = rh02_codreg
                              and rh30_instit = rh02_instit
     left join rhpesrescisao   on rh05_seqpes = rh02_seqpes
     left join rhrubricas      on rh27_rubric = trim(rh01_clas1)
                              and rh27_instit = rh02_instit
where rh05_seqpes is null ) as x

where (ferias > 10
   or afasta > 0
   or vinculo <> 'A')
   or rh27_rubric is null
order by ferias,afasta,vinculo,rh27_rubric, z01_nome
  ";

//echo $sql ; exit;

$result = db_query($sql);
$xxnum = pg_numrows($result);
if ($xxnum == 0){
   db_redireciona('db_erros.php?fechar=true&db_erro=Não existem Cálculo no período de '.$mes.' / '.$ano);

}

$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);
$troca = 1;
$alt = 4;
$pre = 1;
$total = 0;
for($x = 0; $x < pg_numrows($result);$x++){
   db_fieldsmemory($result,$x);
   if ($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
      $pdf->addpage();
      $pdf->setfont('arial','b',8);
      $pdf->cell(15,$alt,'MATRIC',1,0,"C",1);
      $pdf->cell(60,$alt,'NOME DO FUNCIONÁRIO',1,0,"C",1);
      $pdf->cell(20,$alt,'VINCULO',1,0,"C",1);
      $pdf->cell(20,$alt,'AFASTA' ,1,0,"C",1);
      $pdf->cell(20,$alt,'FÉRIAS' ,1,0,"C",1);
      $pdf->cell(20,$alt,'RUBRICA' ,1,1,"C",1);
      $troca = 0;
      $pre = 1;
   }
   if($pre == 1){
     $pre = 0;
   }else{
     $pre = 1;
   }
   $pdf->setfont('arial','',7);
   $pdf->cell(15,$alt,$rh01_regist,0,0,"C",$pre);
   $pdf->cell(60,$alt,$z01_nome,0,0,"L",$pre);
   $pdf->cell(20,$alt,$vinculo,0,0,"C",$pre);
   $pdf->cell(20,$alt,$afasta,0,0,"C",$pre);
   $pdf->cell(20,$alt,$ferias,0,0,"C",$pre);
   $pdf->cell(20,$alt,$rh01_clas1,0,1,"C",$pre);
   $total ++;
}
if($pre == 1){
  $pre = 0;
}else{
  $pre = 1;
}
$pdf->setfont('arial','b',8);
$pdf->cell(155,$alt,'TOTAL DE REGISTROS :  '.$total,"T",0,"C",$pre);
//$pdf->cell(20,$alt,'',"T",0,"C",0);
//$pdf->cell(30,$alt,db_formatar($total,'f'),"T",1,"R",0);

$pdf->Output();
   
?>