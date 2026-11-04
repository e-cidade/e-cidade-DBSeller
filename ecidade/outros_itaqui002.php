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
//include(modification("libs/db_stdlib.php"));

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
//db_postmemory($HTTP_SERVER_VARS,2);exit;

//$ano = 2009;
//$mes = 6;
//$aniversario = 5;


$head2 = "RELACAO DE ANIVERSARIANTES ";
$head4 = "MÊS : ".strtoupper(db_mes($aniversario));
$head6 = "ORDEM : ALFABÉTICA";


$sql = "
select rh01_regist,
       z01_nome,
       substr(rh01_nasc,6,2),
       rh01_nasc,
       rh30_vinculo,
       rh30_regime ,
       substr(db_fxxx(rh01_regist,rh02_anousu,rh02_mesusu,rh02_instit),111,11) as f010
from   rhpessoal
       inner join cgm          on z01_numcgm  = rh01_numcgm
       inner join rhpessoalmov on rh01_regist = rh02_regist
                              and rh02_anousu = $ano
                              and rh02_mesusu = $mes
       left join rhpesrescisao on rh02_seqpes = rh05_seqpes
       inner join rhregime     on rh30_codreg = rh02_codreg
                              and rh30_regime = 1
where rh05_seqpes is null
  and rh30_vinculo = 'A'
  and substr(rh01_nasc,6,2) = lpad($aniversario,2,'0')
order by substr(rh01_nasc,6,2) , z01_nome;

"; 

//echo $sql;exit;
$result = db_query($sql);
//db_criatabela($result);exit;
$xxnum = pg_numrows($result);
if($xxnum == 0){
  db_redireciona('db_erros.php?fechar=true&db_erro=Não nenhum registro encontrado no período de '.$mes.' / '.$ano);
}

$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$pdf->setfont('arial','b',8);
$pdf->setfillcolor(235);

$total_g   =  0;
$troca     =  1;
$total     =  0;
$tot_valor =  0;
$alt       =  4;
$xsec      = '';

for($x = 0; $x < pg_numrows($result);$x++){
   db_fieldsmemory($result,$x);
   if ($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
      $pdf->addpage('L');
      $pdf->setfont('arial','b',8);
      $pdf->cell(15,$alt,'MATRIC',1,0,"C",1);
      $pdf->cell(80,$alt,'NOME',1,0,"C",1);
      $pdf->cell(25,$alt,'NASCIMENTO',1,0  ,"C",1);
      $pdf->cell(25,$alt,'PADRAO',1,1,"C",1);
			$pdf->ln(3);
      $troca = 0;
      $pre = 1;
   }
   if($pre == 1){
     $pre = 0;
   }else{
     $pre = 1;
   }  
   $pdf->setfont('arial','',7);
   $pdf->cell(15,$alt,$rh01_regist,0,0,"R",$pre);
   $pdf->cell(80,$alt,$z01_nome,0,0,"L",$pre);
   $pdf->cell(25,$alt,db_formatar($rh01_nasc, 'd'),0,0,"C",$pre);
   $pdf->cell(25,$alt,db_formatar($f010, 'f') ,0,1,"R",$pre);
   $total++;
   $tot_valor += $f010;
}
$pdf->setfont('arial','b',8);
$pdf->cell(120,$alt,'TOTAL DE FUNCIONARIOS  :  '.$total,"T",0,"L",0);
$pdf->cell(25,$alt,db_formatar($tot_valor,'f'),"T",1,"R",0);
$pdf->Output();
?>