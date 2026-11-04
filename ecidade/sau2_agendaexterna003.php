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
$clsau_agendatransporte = new cl_sau_agendatransporte_ext;
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
set_time_limit(0);




$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$head2 = "Relação dos Passageiros";
$dat=explode("/",$data);
$head3="Data: $dat[2]/$dat[1]/$dat[0]";
$data="$dat[0]-$dat[1]-$dat[2]";
$pdf->ln(5);
$pdf->addpage('P');
$b=0;
$pri = true;


          $pdf->setfillcolor(235);
          $pdf->setfont('arial','b',7);
          $pdf->cell(194,6,"Relação de Passageiros",1,1,"C",1);

          $pdf->setfillcolor(235);
          $pdf->setfont('arial','b',7);
          $pdf->cell(10,6,"Nº",1,0,"C",0);
          $pdf->cell(60,6,"Nome do Passageiro",1,0,"C",0);
          $pdf->cell(22,6,"Nº da Identidade",1,0,"C",0);
          $pdf->cell(10,6,"",1,0,"C",0);
          $pdf->cell(10,6,"Nº",1,0,"C",0);
          $pdf->cell(60,6,"Nome do Passageiro",1,0,"C",0);
          $pdf->cell(22,6,"Nº da Identidade",1,1,"C",0);          
          $pri = false;          
       //$sql=$clsau_agendatransporte->sql_query_ext ( "", "*", "s124_i_codigo","" );
       //die($sql);
       $sql=$clsau_agendatransporte->sql_query_ext ( "", "*", "s124_i_codigo","s124_d_saida = '$data' and s121_i_veiculo = $veiculo" ); 
       $result=$clsau_agendatransporte->sql_record($sql);
       $i=0;
       $ctr=26;
       $linhas=pg_num_rows($result);
       $e=1;
       $d=27;
       for($r=0;$r<26;$r++){
         $z01_v_nome="";
         $z01_v_ident=""; 
       	 if(($i<$linhas)&&($i<26)){
       	     db_fieldsmemory($result,$i);
             $i++;
          }
       	  $pdf->setfillcolor(235);
          $pdf->setfont('arial','b',7);
          $pdf->cell(10,7,"$e",1,0,"C",0);
          $pdf->cell(60,7,"$z01_v_nome",1,0,"L",0);
          $pdf->cell(22,7,"$z01_v_ident",1,0,"L",0);
          $pdf->cell(10,7,"",1,0,"C",0);
          
          $z01_v_nome="";
          $z01_v_ident="";
          if(($ctr<$linhas)&&($ctr<52)){
             db_fieldsmemory($result,$i);
             $crt++;
          }
          $pdf->cell(10,7,"$d",1,0,"C",0);
          $pdf->cell(60,7,"$z01_v_nome",1,0,"L",0);
          $pdf->cell(22,7,"$z01_v_ident",1,1,"L",0);
        	
          $e++;$d++;
       }
    



$pdf->rect( 10, 230, 194, 49, "D");
$pdf->setY(235);
$pdf->setX(15);
$pdf->cell(100,4,"Nome:_______________________________________________________________________",0,0,"C",0);
$pdf->setY(235);
$pdf->setX(110);
$pdf->cell(100,4,"n.º do RECEFI:_______________________________________",0,1,"C",0);

$pdf->setY(240);
$pdf->setX(20);
$pdf->cell(34,4,"Placas:______________________________",0,0,"C",0);
$pdf->setY(240);
$pdf->setX(69);
$pdf->cell(50,4,"Ano Fabricação:__________________________",0,0,"C",0);
$pdf->setY(240);
$pdf->setX(120);
$pdf->cell(80,4,"Nota Fiscal n.º:_______________________________________",0,1,"C",0);

$pdf->setY(245);
$pdf->setX(18);
$pdf->cell(42,4,"Data início da viagem:___________________",0,0,"C",0);
$pdf->setY(245);
$pdf->setX(69);
$pdf->cell(40,4,"Hora:_______________________",0,0,"C",0);
$pdf->setY(245);
$pdf->setX(105);
$pdf->cell(60,4,"Data do retorno:_____________________",0,0,"C",0);
$pdf->setY(245);
$pdf->setX(127);
$pdf->cell(100,4,"Hora :_______________________",0,1,"C",0);

$pdf->setY(250);
$pdf->setX(36);
$pdf->cell(40,4,"Cidade de Origem:________________________________________________",0,0,"C",0);
$pdf->setY(250);
$pdf->setX(110);
$pdf->cell(80,4,"Cidade de Destino :____________________________________________________",0,1,"C",0);

$pdf->setY(255);
$pdf->setX(75);
$pdf->cell(60,4,"Endereço da Saída :___________________________________________________________________________________________________________________",0,1,"C",0);

$pdf->setY(260);
$pdf->setX(10);
$pdf->cell(63,4,"Distância da Viagem:_____________________Km",0,0,"C",0);
$pdf->setY(260);
$pdf->setX(65);
$pdf->cell(80,4,"Nome do Guia de Turismo:_____________________",0,0,"C",0);
$pdf->setY(260);
$pdf->setX(120);
$pdf->cell(92,4,"Cadastro no MTUR:___________________________",0,1,"C",0);

$pdf->setY(265);
$pdf->setX(10);
$pdf->cell(100,4,"Local:_________________________________________________________________",0,0,"C",0);
$pdf->setY(265);
$pdf->setX(85);
$pdf->cell(92,4,"Data:___________________________",0,1,"C",0);
$pdf->setY(272);
$pdf->setX(10);
$pdf->cell(77,4,"* Em caso de substituição de veículo comunicar ao DAER ",0,0,"C",0);
$pdf->setY(272);
$pdf->setX(50);
$pdf->cell(150,4,"Carimbo e Assinatura Fiscalização do DAER",0,1,"C",0);

$pdf->Output();
?>