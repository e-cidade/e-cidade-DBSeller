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
include(modification("classes/db_fis_tipoandam_classe.php"));

$cltipoandam = new cl_fis_tipoandam;

$clrotulo = new rotulocampo;
$clrotulo->label('y41_codtipo');
$clrotulo->label('y41_descr');
$clrotulo->label('y41_obs');


parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
//db_postmemory($HTTP_SERVER_VARS,2);exit;

if($ordem == "a") {
   $desc_ordem = "ALFABÉTICA";
   $order_by = "y41_descr";
}
else {
   $desc_ordem = "NUMÉRICA";
   $order_by = "y41_codtipo";
}
 
$head3 = "RELATÓRIO DE TIPOS DE ANDAMENTO";
$head5 = "ORDEM $desc_ordem";

/**
 * Query antiga:
 * $cltipoandam->sql_query("","*",$order_by," y41_instit = ".db_getsession('DB_instit') )
 */

$sql = "SELECT y41_codtipo, y41_descr, y41_obs, fis_grupotipoandamento.descricao as grupo FROM fiscalizacao.fis_tipoandam
         INNER JOIN fiscalizacao.fis_grupotipoandamento_tipoandam ON fi30_tipoandam = y41_codtipo  
         INNER JOIN fiscalizacao.fis_grupotipoandamento ON fi30_grupo = fis_grupotipoandamento.sequencial
         WHERE y41_instit = ".db_getsession('DB_instit')." /* AND fis_grupotipoandamento.sequencial <> 999 */ ORDER BY ".$order_by;

$result = $cltipoandam->sql_record($sql);

if ($cltipoandam->numrows == 0){
   db_redireciona('db_erros.php?fechar=true&db_erro=Não existem tipos de andamento cadastrados.');

}
      
$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);
$troca = 1;
$alt = 4;

for($x = 0; $x < $cltipoandam->numrows;$x++){
   db_fieldsmemory($result,$x);
   if ($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
      $pdf->addpage();
      $pdf->setfont('arial','b',8);
      $pdf->cell(12,$alt,$RLy41_codtipo,1,0,"C",1);
      $pdf->cell(65,$alt,$RLy41_descr,1,0,"C",1);
      $pdf->cell(55,$alt,'Grupo',1,0,"L",1);
      $pdf->cell(60,$alt,$RLy41_obs,1,1,"C",1); 
     
      $troca = 0;
   }
   $pdf->setfont('arial','',7);
   $pdf->cell(12,$alt,$y41_codtipo,0,0,"C",0);
   $pdf->cell(65,$alt,$y41_descr,0,0,"L",0);  
   $pdf->cell(55,$alt,$grupo,0,0,"L",0);  
   $pdf->multicell(60,$alt,$y41_obs,0,"L",0);
   $total++;
}

$pdf->setfont('arial','b',8);
$pdf->cell(150,$alt,'TOTAL DE TIPOS DE ANDAMENTO  :  '.$total,"T",0,"L",0);

$pdf->Output();
   
?>