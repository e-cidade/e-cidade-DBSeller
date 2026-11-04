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
include(modification("classes/db_sau_agendaexterna_ext_classe.php"));
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
set_time_limit(0);
$clsau_agendaexterna = new cl_sau_agendaexterna_ext;
$clsau_agendaexterna->rotulo->label();

$ano           = substr( $pdia, 6, 4 );
$mes           = substr( $pdia, 3, 2 );
$dia           = substr( $pdia, 0, 2 );
$data = $ano."-".$mes."-".$dia;
$tipoagenda= $s118_c_tipoagenda;
if($tipoagenda=="C"){
 $where = "s118_d_marcada='$data' and s118_c_tipoagenda='C' ";
}else{
 $where = "s118_d_marcada='$data' and s118_c_tipoagenda='E'";
}

   $str_query            = $clsau_agendaexterna->sql_query_ext(null, "*", "","$where");
  $result_agendaexterna= $clsau_agendaexterna->sql_record( $str_query );


$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$head2 = "Indicação das Prestadoras para as Consultas ou Exames";
$pdf->ln(5);
$pdf->addpage('L');
$b=0;
$pri = true;

	for( $x=0; $x < $clsau_agendaexterna->numrows; $x++ ){
		$obj_agendaexterna  = db_utils::fieldsMemory( $result_agendaexterna, $x );
     if (  ($pdf->gety() > $pdf->h -30)  || $pri==true ){
     
          //$pdf->addpage();

          $pdf->setfont('arial','b',7);

          $pdf->setfillcolor(235);
          $pdf->setfont('arial','b',7);
          $pdf->cell(20,4,"Paciente",1,0,"C",1);
          $pdf->cell(70,4,"Nome",1,0,"C",1);
          $pdf->cell(30,4,"RG",1,0,"C",1);
          $pdf->cell(30,4,"CPF",1,0,"C",1);
          $pdf->cell(70,4,"Prestadora",1,0,"C",1);
          $pdf->cell(20,4,"Protocolo",1,0,"C",1);
          $pdf->cell(20,4,"Data",1,0,"C",1);
          $pdf->cell(20,4,"Hora",1,1,"C",1);          
          
          $pri = false;
          
     }
     $pdf->setfont('arial','',7);
     $pdf->cell(20,4,$obj_agendaexterna->z01_i_numcgs,1,0,"L",0);
     $pdf->cell(70,4,$obj_agendaexterna->z01_v_nome,1,0,"L",0);
     $pdf->cell(30,4,$obj_agendaexterna->z01_v_ident,1,0,"L",0);
     $pdf->cell(30,4,$obj_agendaexterna->z01_v_cgccpf,1,0,"L",0);
     $pdf->cell(70,4,$obj_agendaexterna->z01_nome,1,0,"L",0);
     $pdf->cell(20,4,$obj_agendaexterna->s118_v_protocolo,1,0,"L",0);
     $pdf->cell(20,4,db_formatar($obj_agendaexterna->s118_d_marcada,'d'),1,0,"L",0);
     $pdf->cell(20,4,$obj_agendaexterna->s118_c_horamarcada,1,1,"L",0);     

}
$pdf->Output();
?>