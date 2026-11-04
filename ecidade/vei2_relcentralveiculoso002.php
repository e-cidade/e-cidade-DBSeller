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
include(modification("classes/db_veiccadcentral_classe.php"));
$clrotulo = new rotulocampo;
$clveiccadcentral = new cl_veiccadcentral;
//$clrotulo->label('m61_abrev');

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
//db_postmemory($HTTP_SERVER_VARS,2);exit;

if($quebra == 's'){
    $head5 = "Quebra de página : SIM";
  }
  else{
    $head5 = "Quebra de página : NÃO";
  }
$head3 = "CADASTRO DE MATERIAIS ";

if ($codcentral=="0"){
$where="";
}else{
$where="ve36_coddepto=$codcentral";
}

$xordem = 'codigo_central';
$sql="ve36_sequencial as codigo_central,
       descrdepto      as central,
       ve01_codigo     as codigo_veiculo,
       ve01_placanum   as placa,
       ve20_descr      as descr_tipo,
       ve22_descr      as descr_modelo,
       ve21_descr      as descr_marca,
       ve01_anofab     as ano_fabricacao,
       case 
         when (select ve04_veiculo from veicbaixa where ve04_veiculo = veiccentral.ve40_veiculos) is not null
           then 'SIM'
         else 'NAO'
       end as baixado";

$result_central =  $clveiccadcentral->sql_record($clveiccadcentral->sql_query_veiculoscentral(null,$sql,$xordem,$where));
//db_criatabela($result_central);exit; 

$sql="veicmotoristas.ve05_codigo,
       z01_nome,
       ve30_descr,
       ve05_dtvenc,
       null as null";

$xordem = "veicmotoristas.ve05_codigo";

$result_motoristas =  $clveiccadcentral->sql_record($clveiccadcentral->sql_query_veiculosmotoristas(null,"$sql",$xordem));
//db_criatabela($result_motoristas);
//exit;



$xxnum = pg_numrows($result);
if ($xxnum == 0){
   db_redireciona('db_erros.php?fechar=true&db_erro=Não existem centrais .');
}
$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);
$troca = 1;
$alt = 4;

$passa=false;
$codigocentral="";
for($x = 0; $x < pg_numrows($result_central);$x++){
db_fieldsmemory($result_central,$x);
   
   if ($pdf->gety() > $pdf->h - 30 || $troca != 0 )
   {
      $pdf->addpage();

      $pdf->setfont('arial','b',8);
      $pdf->cell(13,$alt,"Codveic",1,0,"L",1);
      $pdf->cell(13,$alt,"Placa",1,0,"L",1);
      $pdf->cell(43,$alt,"Descr. Tipo",1,0,"L",1);
      $pdf->cell(43,$alt,"Descr. Modelo",1,0,"L",1);
      $pdf->cell(40,$alt,"Descr. Marca",1,0,"L",1);
      $pdf->cell(23,$alt,"Ano Fabricação",1,0,"L",1);
      $pdf->cell(20,$alt,"Baixado",1,1,"L",1);

      $troca = 0;
   }
 //  $pdf->setfont('arial','',7);


if ($passa==false && $codcentral=="0"){
  $codigocentral=$codigo_central;
  $pdf->cell(13,$alt,"muda Central",1,1,"L",1);
  $passa=true;
};







   if ($codigocentral==$codigo_central){
      $pdf->setfont('arial','b',8);
      $pdf->cell(13,$alt,$codigo_veiculo,1,0,"L",1);
      $pdf->cell(13,$alt,$placa,1,0,"L",1);
      $pdf->cell(43,$alt,$descr_tipo,1,0,"L",1);
      $pdf->cell(43,$alt,$descr_modelo,1,0,"L",1);
      $pdf->cell(40,$alt,$descr_marca,1,0,"L",1);
      $pdf->cell(23,$alt,$ano_fabricacao,1,0,"L",1);
      $pdf->cell(20,$alt,$baixado,1,1,"L",1);
   }else{

      $pdf->setfont('arial','b',8);
      $pdf->cell(13,$alt,"muda Central",1,1,"L",1);
      $pdf->cell(13,$alt,$codigo_veiculo,1,0,"L",1);
      $pdf->cell(13,$alt,$placa,1,0,"L",1);
      $pdf->cell(43,$alt,$descr_tipo,1,0,"L",1);
      $pdf->cell(43,$alt,$descr_modelo,1,0,"L",1);
      $pdf->cell(40,$alt,$descr_marca,1,0,"L",1);
      $pdf->cell(23,$alt,$ano_fabricacao,1,0,"L",1);
      $pdf->cell(20,$alt,$baixado,1,1,"L",1);



}


   $codigocentral=$codigo_central;


$total ++;
}
$pdf->setfont('arial','b',8);
$pdf->cell(0,$alt,"TOTAL DE REGISTROS  :  $total",'T',0,"L",0);
$pdf->output();
?>