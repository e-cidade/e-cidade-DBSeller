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
include(modification("classes/db_solicita_classe.php"));

$clsolicita = new cl_solicita;
$clsolicita->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label('');

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
//db_postmemory($HTTP_SERVER_VARS,2);exit;

$head3 = "Desreserva de saldo das solicitações";
$result = $clsolicita->sql_record($clsolicita->sql_query_reserv(null,"distinct pc10_numero,pc10_data,pc10_resumo,pc10_depto,descrdepto,pc10_instit,nomeinst,pc10_login,nome",null,"EXTRACT (YEAR FROM pc10_data)= ".db_getsession("DB_anousu")."  and o82_codres is not null and pc81_solicitem is null and (current_date-pc10_data)>=30 and pc10_depto in (select coddepto from db_depusu where id_usuario = ".db_getsession("DB_id_usuario").") "));
if ($clsolicita->numrows == 0){
   db_redireciona('db_erros.php?fechar=true&db_erro=Não existem registros cadastrados.');
}
$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);
$troca = 1;
$alt = 4;
$total = 0;
$p=0;
for($x = 0; $x < $clsolicita->numrows;$x++){
   db_fieldsmemory($result,$x);
   if ($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
      $pdf->addpage("L");
      $pdf->setfont('arial','b',8);
      $pdf->cell(20,$alt,"Solicitação",1,0,"C",1);
      $pdf->cell(25,$alt,"Data",1,0,"C",1); 
      $pdf->cell(80,$alt,"Departamento",1,0,"C",1); 
      $pdf->cell(65,$alt,"Usuário",1,0,"C",1); 
      $pdf->cell(0,$alt,"Resumo",1,1,"C",1); 
      $p=0;
      $troca = 0;
   }
   $pdf->setfont('arial','',7);
   $pdf->cell(20,$alt,$pc10_numero,0,0,"C",$p);
   $pdf->cell(25,$alt,$pc10_data,0,0,"C",$p);
   $pdf->cell(80,$alt,$descrdepto,0,0,"L",$p);
   $pdf->cell(65,$alt,$nome,0,0,"L",$p);
   $pdf->multicell(0,$alt,$pc10_resumo,0,"L",$p);
   if ($p==0){
     $p=1;
   }else{
     $p=0;
   }
   $total++;
}
$pdf->setfont('arial','b',8);
$pdf->cell(0,$alt,'TOTAL DE REGISTROS  :  '.$total,"T",0,"L",0);
$pdf->Output();
?>