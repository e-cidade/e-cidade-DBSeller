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
include(modification("libs/db_utils.php"));
include(modification("classes/db_sau_agendatransporte_ext_classe.php"));
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
set_time_limit(0);
$clsau_agendatransporte = new cl_sau_agendatransporte_ext;
$clsau_agendatransporte->rotulo->label();

$ano           = substr( $pdia, 6, 4 );
$mes           = substr( $pdia, 3, 2 );
$dia           = substr( $pdia, 0, 2 );
$data = $ano."-".$mes."-".$dia;

$where = "s124_d_saida='$data'";



   $str_query            = $clsau_agendatransporte->sql_query_ext(null, "*", "","$where");
  $result_agendatransporte= $clsau_agendatransporte->sql_record( $str_query );


$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$head2 = "Indicação dos Veículos";
$pdf->ln(5);
$pdf->addpage('L');
$b=0;
$pri = true;

	for( $x=0; $x < $clsau_agendatransporte->numrows; $x++ ){
		$obj_agendatransporte  = db_utils::fieldsMemory( $result_agendatransporte, $x );
     if (  ($pdf->gety() > $pdf->h -30)  || $pri==true ){
     
          //$pdf->addpage();

          $pdf->setfont('arial','b',7);

          $pdf->setfillcolor(235);
          $pdf->setfont('arial','b',7);
          $pdf->cell(30,4,"Paciente",1,0,"C",1);
          $pdf->cell(100,4,"Nome",1,0,"C",1);
          $pdf->cell(30,4,"RG",1,0,"C",1);
          $pdf->cell(30,4,"CPF",1,0,"C",1);
          $pdf->cell(30,4,"Placa",1,0,"C",1);
          $pdf->cell(30,4,"Data",1,0,"C",1);
          $pdf->cell(30,4,"Hora",1,1,"C",1);          
          $pri = false;
          
     }
     $pdf->setfont('arial','',7);
     $pdf->cell(30,4,$obj_agendatransporte->z01_i_numcgs,1,0,"L",0);
     $pdf->cell(100,4,$obj_agendatransporte->z01_v_nome,1,0,"L",0);
     $pdf->cell(30,4,$obj_agendatransporte->z01_v_ident,1,0,"L",0);
     $pdf->cell(30,4,$obj_agendatransporte->z01_v_cgccpf,1,0,"L",0);
     $pdf->cell(30,4,$obj_agendatransporte->ve01_placa,1,0,"L",0);
     $pdf->cell(30,4,db_formatar($obj_agendatransporte->s124_d_saida,'d'),1,0,"L",0);     
     $pdf->cell(30,4,$obj_agendatransporte->s124_c_hora,1,1,"L",0);

}
$pdf->Output();
?>