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

$clrotulo = new rotulocampo;
$clrotulo->label('r14_rubric');
$clrotulo->label('z01_nome');
$clrotulo->label('r01_regist');
$clrotulo->label('r14_quant');
$clrotulo->label('r14_valor');

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
//db_postmemory($HTTP_SERVER_VARS,2);exit;

$head2 = "RELATÓRIO DE MARGEM CONSIGNÁVEL";
$head4 = "PERÍODO : ".$mes." / ".$ano;
$head6 = "PERCENTUAL CONSIGNÁVEL : $perc %";

$ponto = 's';

if($ponto == 's'){
  $arquivo = 'gerfsal';
  $sigla   = 'r14_';
  $head5   = 'PONTO : SALÁRIO';
}elseif($ponto == 'c'){
  $arquivo = 'gerfcom';
  $sigla   = 'r48_';
  $head5   = 'PONTO : COMPLEMENTAR';
}elseif($ponto == 'a'){
  $arquivo = 'gerfadi';
  $sigla   = 'r22_';
  $head5   = 'PONTO : ADIANTAMENTO';
}elseif($ponto == 'r'){
  $arquivo = 'gerfres';
  $sigla   = 'r20_';
  $head5   = 'PONTO : RESCISÃO';
}elseif($ponto == 'd'){
  $arquivo = 'gerfs13';
  $sigla   = 'r35_';
  $head5   = 'PONTO : 13o. SALÁRIO';
}

if($filtro == 't'){
  $where = '';
}else{
  $where = "where ssmpo-perc_ssmpo > 0 or afmo-perc_afmo > 0 or cons-perc_cons > 0 "; 
}


$sql = "
select regist,
       z01_nome,
       liquido,
       perc_liq,
       ssmpo,
       perc_ssmpo,
       ssmpo-perc_ssmpo as dif_ssmpo,
       afmo,
       perc_afmo,
       afmo-perc_afmo as dif_afmo,
       cons,
       perc_cons, 
       cons-perc_cons as dif_consig
from
(
select rh02_regist as regist,
       z01_nome,
       liquido,
       round(liquido/100*$perc1,2) as perc_liq,
       ssmpo,
       round((liquido/100*$perc1)/100*$perc2,2) as perc_ssmpo,
       afmo,
       round((liquido/100*$perc1)/100*$perc3,2) as perc_afmo,
       cons,
       round((liquido/100*$perc1)/100*$perc4,2) as perc_cons

      	from rhpessoalmov
             inner join (select ".$sigla."regist as regist,
                                round(sum(case when ".$sigla."pd = 1 then ".$sigla."valor else ".$sigla."valor*(-1) end),2) as liquido
                         from ".$arquivo."
                         where ".$sigla."anousu = $ano
                           and ".$sigla."mesusu = $mes
                           and ".$sigla."rubric in (select r09_rubric
                                              from basesr
                                              where r09_base   = '$base1'
                                                and r09_anousu = ".db_anofolha()."
                                                and r09_mesusu = ".db_mesfolha()."
                                                and r09_instit = ".db_getsession('DB_instit').")
                                              group by  ".$sigla."regist) liq  on liq.regist = rh02_regist
             inner join (select ".$sigla."regist as regist,
                                round(sum(case when ".$sigla."pd = 2 then ".$sigla."valor else ".$sigla."valor*(-1) end),2) as ssmpo
                         from ".$arquivo."
                         where ".$sigla."anousu = $ano
                           and ".$sigla."mesusu = $mes
                           and ".$sigla."rubric in (select r09_rubric
                                              from basesr
                                              where r09_base   = '$base2'
                                                and r09_anousu = ".db_anofolha()."
                                                and r09_mesusu = ".db_mesfolha()."
                                                and r09_instit = ".db_getsession('DB_instit').")
                                              group by  ".$sigla."regist) ssmpo  on ssmpo.regist = rh02_regist
             inner join (select ".$sigla."regist as regist,
                                round(sum(case when ".$sigla."pd = 2 then ".$sigla."valor else ".$sigla."valor*(-1) end),2) as afmo
                         from ".$arquivo."
                         where ".$sigla."anousu = $ano
                           and ".$sigla."mesusu = $mes
                           and ".$sigla."rubric in (select r09_rubric
                                              from basesr
                                              where r09_base   = '$base3'
                                                and r09_anousu = ".db_anofolha()."
                                                and r09_mesusu = ".db_mesfolha()."
                                                and r09_instit = ".db_getsession('DB_instit').")
                                              group by  ".$sigla."regist) afmo  on afmo.regist = rh02_regist
             inner join (select ".$sigla."regist as regist,
                                round(sum(case when ".$sigla."pd = 2 then ".$sigla."valor else ".$sigla."valor*(-1) end),2) as cons
                         from ".$arquivo."
                         where ".$sigla."anousu = $ano
                           and ".$sigla."mesusu = $mes
                           and ".$sigla."rubric in (select r09_rubric
                                              from basesr
                                              where r09_base   = '$base4'
                                                and r09_anousu = ".db_anofolha()."
                                                and r09_mesusu = ".db_mesfolha()."
                                                and r09_instit = ".db_getsession('DB_instit').")
                                              group by  ".$sigla."regist) cons  on cons.regist = rh02_regist
	     inner join rhpessoal   on  rh01_regist = rh02_regist											
             inner join cgm on z01_numcgm = rh01_numcgm 
       	where rh02_anousu = $ano 
       	  and rh02_mesusu = $mes
  	  and rh02_instit = ".db_getsession("DB_instit")."
          and (ssmpo > 0 or afmo > 0 or cons > 0)
        order by z01_nome
)as xx
$where
       ";

$result = db_query($sql);
//echo $sql; db_criatabela($result);exit;
$xxnum = pg_numrows($result);
if ($xxnum == 0){
   //db_msgbox('Não existem Cálculo no período de '.$mes.' / '.$ano);
   db_redireciona('db_erros.php?fechar=true&db_erro=Não existem Cálculo no período de '.$mes.' / '.$ano);

}


$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);

$troca   = 1;
$alt     = 4;
$xvalor  = 0;
$xquant  = 0;
$total   = 0;
$quebra  = '';
$t_quant = 0;
$t_valor = 0;
$t_func  = 0;
for($x = 0; $x < pg_numrows($result);$x++){
   db_fieldsmemory($result,$x);
   if ($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
        $pdf->addpage('L');
        $pdf->setfont('arial','b',8);
        $pdf->cell(15,$alt,'MATRIC.',1,0,"C",1);
        $pdf->cell(60,$alt,'NOME',1,0,"C",1);
        $pdf->cell(17,$alt,'LIQUIDO',1,0,"C",1);
        $pdf->cell(17,$alt,'50% LIQ',1,0,"C",1);
        $pdf->cell(17,$alt,'SSMPO',1,0,"C",1);
        $pdf->cell(17,$alt,'15% SSMPO',1,0,"C",1);
        $pdf->cell(17,$alt,'DIF.SSMPO',1,0,"C",1);
        $pdf->cell(17,$alt,'AFMO',1,0,"C",1);
        $pdf->cell(17,$alt,'15% AFMO',1,0,"C",1);
        $pdf->cell(17,$alt,'DIF.AFMO',1,0,"C",1);
        $pdf->cell(17,$alt,'CONSIG',1,0,"C",1);
        $pdf->cell(17,$alt,'15% CONSIG',1,0,"C",1);
        $pdf->cell(17,$alt,'DIF.CONSIG',1,1,"C",1);
      $troca = 0;
      $pre = 1;
   }
   if($pre == 1)
     $pre = 0;
   else
     $pre = 1;
   $pdf->setfont('arial','',7);
   $pdf->cell(15,$alt,$regist,0,0,"C",$pre);
   $pdf->cell(60,$alt,$z01_nome,0,0,"L",$pre);
   $pdf->cell(17,$alt,db_formatar($liquido,'f'),0,0,"R",$pre);
   $pdf->cell(17,$alt,db_formatar($perc_liq,'f'),0,0,"R",$pre);
   $pdf->cell(17,$alt,db_formatar($ssmpo,'f'),0,0,"R",$pre);
   $pdf->cell(17,$alt,db_formatar($perc_ssmpo,'f'),0,0,"R",$pre);
   $pdf->setfont('arial','b',8);
   $pdf->cell(17,$alt,db_formatar(($dif_ssmpo < 0?'':$dif_ssmpo),'f'),0,0,"R",$pre);
   $pdf->setfont('arial','',7);
   $pdf->cell(17,$alt,db_formatar($afmo,'f'),0,0,"R",$pre);
   $pdf->cell(17,$alt,db_formatar($perc_afmo,'f'),0,0,"R",$pre);
   $pdf->setfont('arial','b',8);
   $pdf->cell(17,$alt,db_formatar(($dif_afmo < 0?'':$dif_afmo),'f'),0,0,"R",$pre);
   $pdf->setfont('arial','',7);
   $pdf->cell(17,$alt,db_formatar($cons,'f'),0,0,"R",$pre);
   $pdf->cell(17,$alt,db_formatar($perc_cons,'f'),0,0,"R",$pre);
   $pdf->setfont('arial','b',8);
   $pdf->cell(17,$alt,db_formatar(($dif_consig < 0?'':$dif_consig),'f'),0,1,"R",$pre);
   $pdf->setfont('arial','',7);
   $t_valor += $valor;
   $t_quant += $quant;
   $t_func  += 1;
   $xvalor  += $valor;
   $xquant  += $quant;
   $total   += 1;
}
$pdf->setfont('arial','b',8);
$pdf->cell($tot_espaco,$alt,'TOTAL  :  '.$total.'  FUNCIONÁRIOS',"T",0,"C",0);
//$pdf->cell(15,$alt,db_formatar($xquant,'f'),"T",0,"R",0);
//$pdf->cell(25,$alt,db_formatar($xvalor,'f'),"T",1,"R",0);

$pdf->Output();

?>