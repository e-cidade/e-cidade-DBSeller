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
$clrotulo->label('rh01_regist');
$clrotulo->label('z01_nome');
$clrotulo->label('rh01_sexo');
$clrotulo->label('rh01_admiss');
$clrotulo->label('rh01_nasc');
$clrotulo->label('rh08_descr');
$clrotulo->label('rh31_dtnasc');

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$where = "";
if ($lista != "") {
	if (isset ($ver) and $ver == "com") {
		$where .= " and rh01_regist in  ($lista)";
	} else {
		$where .= " and rh01_regist not in  ($lista)";
	}
}
if(isset($selec) && $selec != ''){
  $where .= " and rh02_codreg in (".$selec.") ";
}
$head3 = "RELATÓRIO";
$head5 = "PERÍODO : ".$mes." / ".$ano;

$sql = "
select rh01_regist,
z01_nome,
rh01_sexo,
case when rh04_descr is null then 'CARGO: '||rh37_descr else 'FUNCAO :
'||rh04_descr end as cargo,
rh01_admiss,
rh01_nasc,
rh08_descr,
rh31_dtnasc
from rhpessoal
inner join cgm           on rh01_numcgm = z01_numcgm
left join rhfuncao       on rh37_funcao = rh01_funcao
and rh37_instit = 1
left join rhpessoalmov   on rh02_regist = rh01_regist
left join rhpescargo     on rh02_seqpes = rh20_seqpes
left join rhcargo        on rh20_cargo  = rh04_codigo
and rh04_instit = rh02_instit
inner join rhestcivil    on rh01_estciv = rh08_estciv
left join rhdepend       on rh31_regist = rh01_regist
and rh31_gparen = 'C'
left join rhpesrescisao  on rh05_seqpes = rh02_seqpes
inner join rhregime      on rh30_codreg = rh02_codreg
where rh02_anousu = $ano
and rh02_mesusu = $mes
and rh05_seqpes is null
	and rh02_instit = ".db_getsession("DB_instit")."
  and rh05_recis is null
  $where
       ";
//echo $sql ; exit;

$result = db_query($sql);
$xxnum = pg_numrows($result);
if ($xxnum == 0){
   db_redireciona('db_erros.php?fechar=true&db_erro=Não existem funcionários no período de '.$mes.' / '.$ano);

}

$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$total  = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);
$troca = 1;
$alt = 4;

for($x = 0; $x < pg_numrows($result);$x++){
   db_fieldsmemory($result,$x);
   if ($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
      $pdf->addpage("L");
      $pdf->setfont('arial','b',8);
      $pdf->cell(15,$alt,'MATRÍC.',1,0,"C",1);
      $pdf->cell(80,$alt,'NOME',1,0,"C",1);
      $pdf->cell(15,$alt,'SEXO',1,0,"C",1);
      $pdf->cell(80,$alt,'CARGO/FUNÇÃO',1,0,"C",1);
      $pdf->cell(20,$alt,'DT ADMISS.',1,0,"C",1);
      $pdf->cell(20,$alt,'DT NASC.',1,0,"C",1);
      $pdf->cell(20,$alt,'EST. CIVIL',1,0,"C",1);
      $pdf->cell(20,$alt,'NASC. CONJ.',1,1,"C",1);
      $troca = 0;
   }
   $pdf->setfont('arial','',7);
   $pdf->cell(15,$alt,$rh01_regist,0,0,"C",0);
   $pdf->cell(80,$alt,$z01_nome,0,0,"L",0);
   $pdf->cell(15,$alt,$rh01_sexo,0,0,"C",0);
   $pdf->cell(80,$alt,$cargo,0,0,"L",0);
   $pdf->cell(20,$alt,db_formatar($rh01_admiss,"d"),0,0,"C",0);
   $pdf->cell(20,$alt,db_formatar($rh01_nasc,"d"),0,0,"C",0);
   $pdf->cell(20,$alt,$rh08_descr,0,0,"L",0);
   $pdf->cell(20,$alt,db_formatar($rh31_dtnasc,"d"),0,1,"C",0);
   $total++;
}
$pdf->cell(270,$alt,'Total de Registros: '.$total,"T",0,"R",0);
$pdf->Output();
   
?>