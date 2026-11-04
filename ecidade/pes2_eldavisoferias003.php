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

include(modification("fpdf151/pdf2.php"));
include(modification("libs/db_sql.php"));

$clrotulo = new rotulocampo;
$clrotulo->label('rh27_rubric');
$clrotulo->label('rh27_descr');
$clrotulo->label('rh27_elemen');
$clrotulo->label('rh27_pd');

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
//db_postmemory($HTTP_SERVER_VARS,2);
//db_postmemory($HTTP_POST_VARS,2);exit;

//$ano = 2005;
//$mes = 9;
//$matric = 182;

$sqlpref = "select * from db_config where codigo = ".db_getsession("DB_instit");
$resultpref = db_query($sqlpref);
db_fieldsmemory($resultpref,0);

$sql = "
select r30_per1i-30 as atual,
       cadferia.*,
       o15_codigo,
       rh01_admiss,
       o15_descr as recurso,
                  case when r30_per2i is null 
                       then r30_per1i - 30
		  else r30_per2i - 30 end as data, 
                  case when r30_per2i is null 
                       then r30_per1i
		  else r30_per2i end as gozoi, 
                  case when r30_per2f is null 
                       then r30_per1f
		  else r30_per2f end as gozof, 
                  case when r30_per2f+1 is null 
                       then r30_per1f+1
		  else r30_per2f end as trabalho, 
                  case when r30_per2f+r30_abono::int is null 
                       then r30_per1f+r30_abono::int
		  else r30_per2f end as fim_abono, 
		  z01_nome, 
                  rh37_descr as r37_descr 
from cadferia 
     inner join rhpessoalmov on rh02_regist = r30_regist
                            and rh02_anousu = r30_anousu
             		            and rh02_mesusu = r30_mesusu
											      and rh02_instit = ".db_getsession("DB_instit")."
     inner join rhpessoal    on rh01_regist = r30_regist                       
     inner join cgm     on rh01_numcgm = z01_numcgm
     inner join rhfuncao on rh01_funcao = rh37_funcao
                         and rh02_instit = rh37_instit 
     inner join rhlota   on r70_codigo = rh02_lota
		                    and r70_instit = rh02_instit 
     left join (select distinct rh25_codigo, rh25_recurso from rhlotavinc) as rhlotavinc on rh25_codigo = r70_codigo
     left join orctiporec on o15_codigo = rh25_recurso 
where r30_anousu = ".db_anofolha()."
  and r30_mesusu = ".db_mesfolha()."
  and r30_regist = $matric
order by r30_regist, 
         R30_PERAI desc limit 1;
       ";
//echo $sql ; exit;

$result = db_query($sql);
//db_criatabela($result); exit;
$xxnum = pg_numrows($result);
if ($xxnum == 0){
   db_redireciona('db_erros.php?fechar=true&db_erro=Não existem dados no período de '.$mes.' / '.$ano);

}

$pdf = new PDF2(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$func   = 0;
$func_c = 0;
$tot_c  = 0;
$total  = 0;
$pdf->setleftmargin(20);
$pdf->setrightmargin(20);
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);
$troca = 1;
$alt = 4;
$head1 = 'DEPARTAMENTO DE PESSOAL';


for($x = 0; $x < pg_numrows($result);$x++){
   db_fieldsmemory($result,$x);
   if ($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
      $pdf->addpage();
      $parag1 = $z01_nome.', abaixo assinado, servidor desta Prefeitura Municipal, exercendo o cargo de '.$r37_descr.' vem mui respeitosamente, requerer a V. Sa., as férias referentes ao período de '.substr($r30_perai,8,2).' de '.db_mes(substr($r30_perai,5,2)).' de '.substr($r30_perai,0,4).' à '.substr($r30_peraf,8,2).' de '.db_mes(substr($r30_peraf,5,2)).' de '.substr($r30_peraf,0,4).' a serem gozadas a partir de '.substr($gozoi,8,2).' de '.db_mes(substr($gozoi,5,2)).' de '.substr($gozoi,0,4).' à '.substr($gozof,8,2).' de '.db_mes(substr($gozof,5,2)).' de '.substr($gozof,0,4).'.';
      $pdf->setfont('arial','',10);
      $pdf->cell(0,5,'A V I S O  D E  F É R I A S',0,1,"C",0);
      $pdf->ln(10);
      $pdf->cell(0,5,'Ao Funcionário(a)',0,1,"L",0);
      $pdf->ln(10);
      $pdf->cell(40,5,'Sr.(a) '.$z01_nome.'      MATRÍCULA :  '.$r30_regist,0,1,"L",0);
      $pdf->ln(10);
      $pdf->multicell(0,5,'Tendo V. Sa. direito a férias, com o presente, levamos a seu conhecimento que resolvemos concedê-las no período abaixo relacionado:',0,"J",0);
      $pdf->ln(5);
      $pdf->cell(50,5,'Data de admissão ',0,0,"L",0);
      $pdf->cell(40,5,': '.db_formatar($rh01_admiss,'d'),0,1,"L",0);
      $pdf->ln(5);
      $pdf->cell(50,5,'Período aquisitivo',0,0,"L",0);
      $pdf->cell(40,5,': '.db_formatar($r30_perai,'d').' a '.db_formatar($r30_peraf,'d'),0,1,"L",0);
      $pdf->ln(5);
      $pdf->cell(50,5,'Período de gozo',0,0,"L",0);
      $pdf->cell(40,5,': '.db_formatar($gozoi,'d').' a '.db_formatar($gozof,'d'),0,1,"L",0);
      if($r30_abono > 0){
        $pdf->ln(5);
        $pdf->cell(50,5,'Dias de abono',0,0,"L",0);
        $pdf->cell(40,5,': '.$r30_abono,0,1,"L",0);
        $pdf->ln(5);
        $pdf->cell(50,5,'Período de abono',0,0,"L",0);
        $pdf->cell(40,5,': '.db_formatar($trabalho,'d').' a '.db_formatar($fim_abono,'d'),0,1,"L",0);
      }
      $pdf->ln(5);
      $pdf->cell(50,5,'Retornar ao trabalho dia',0,0,"L",0);
      $pdf->cell(40,5,': '.db_formatar($trabalho,'d'),0,1,"L",0);
//       "PROVENIENTE : de '.$r30_dias1.' de férias, referente ao período de '.substr($r30_perai,8,2).' de '.db_mes(substr($r30_perai,5,2)).' de '.substr($r30_perai,0,4).' à '.substr($r30_peraf,8,2).' de '.db_mes(substr($r30_peraf,5,2)).' de '.substr($r30_peraf,0,4).' a serem gozadas a partir de '.substr($gozoi,8,2).' de '.db_mes(substr($gozoi,5,2)).' de '.substr($gozoi,0,4).' à '.substr($gozof,8,2).' de '.db_mes(substr($gozof,5,2)).' de '.substr($gozof,0,4).'.',0,"J",0);
      $pdf->ln(10);
      $pdf->cell(0,5,strtoupper($munic).'-'.strtoupper($uf).', '.substr($atual,8,2).' de '.db_mes(substr($atual,5,2)).' de '.substr($atual,0,4).'.',0,1,"R",0);
      $pdf->ln(30);
      $pdf->line(30,220,90,220);
      $pdf->text(35,225,'CIENTE: empregado');
      $pdf->line(120,220,180,220);

   }
}
$pdf->Output();
   
?>