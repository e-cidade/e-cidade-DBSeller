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

include(modification("fpdf151/pdfwebseller.php"));
include(modification("classes/db_matricula_classe.php"));
include(modification("classes/db_linha_classe.php"));
include(modification("classes/db_rotamov_classe.php"));
include(modification("classes/db_veicmanut_classe.php"));
$clrotamov = new cl_rotamov;
$clveicmanut = new cl_veicmanut;
$cllinha = new cl_linha;
$escola = db_getsession("DB_coddepto");
$result = $cllinha->sql_record($cllinha->sql_query("","*","","ed217_i_codigo = $chavepesquisa"));
if($cllinha->numrows==0){?>
 <table width='100%'>
  <tr>
   <td align='center'>
    <font color='#FF0000' face='arial'>
     <b>Nenhum registro encontrado.<br>
     <input type='button' value='Fechar' onclick='window.close()'></b>
    </font>
   </td>
  </tr>
 </table>
 <?php
 exit;
}
$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
db_fieldsmemory($result,0);
$pdf->setfillcolor(223);
$head1 = "RELATÓRIO DE CUSTO ROTAS";
$head2 = "Rota:  $ed217_i_codigo - $ed217_c_nome";
$pdf->addpage('P');
$pdf->ln(5);

/////////////////////////////////////////////

$pdf->setfont('arial','b',7);
$pdf->cell(190,4,"DADOS ROTAS",1,1,"C",1);
$pdf->cell(190,4,"","LR",1,"C",0);
$pdf->setfont('arial','',7);
$pdf->cell(5,24,"","L",0,"C",0);
//$pdf->cell(10,4,"Código:",0,0,"L",0);
$pdf->cell(65,4,"Nome:",0,0,"L",0);
$pdf->cell(55,4,"Descição:",0,0,"L",0);
$pdf->cell(45,4,"Data Cadastro:",0,0,"L",0);
$pdf->cell(20,4,"KM dia:","R",1,"L",0);
$pdf->setfont('arial','b',7);
for($x=0;$x<$cllinha->numrows;$x++){
db_fieldsmemory($result,$x);
//$pdf->cell(10,4,$ed217_i_codigo,0,0,"L",0);
$pdf->cell(65,4,$ed217_c_origem,0,0,"L",0);
$pdf->cell(55,4,$ed217_c_destino,0,0,"L",0);
$pdf->cell(45,4,db_formatar($ed217_d_datacad,'d'),0,0,"L",0);
$pdf->cell(25,4,$ed217_f_kmdia,"R",1,"L",0);
}
////////////////////////////////////////////
$pdf->setfont('arial','b',7);
$pdf->cell(190,4,"DADOS GASTOS MANUTENÇÃO",1,1,"C",1);
$sql= "select ve01_placa,
              ve60_medidasaida,
              ve60_horasaida,
              ve60_datasaida
             from veicmanut
             inner join veicmanutretirada on ve65_veicmanut= ve62_codigo
             inner join veicretirada on ve60_codigo= ve65_veicretirada
             inner join veiculos on ve01_codigo = ve60_veiculo
             inner join rotamov on ed220_i_veicretirada = ve60_codigo
             inner join rota on ed217_i_codigo = ed220_i_rota
             where ed220_i_rota=$chavepesquisa
             ";
$result = db_query($sql);
$linhas= pg_num_rows($result);
 if($linhas>0){
 $pdf->cell(190,4,"","LR",1,"C",0);
 $pdf->setfont('arial','',7);
 $pdf->cell(5,24,"","L",0,"C",0);
 //$pdf->cell(45,4,"Código:",0,0,"L",0);
 $pdf->cell(30,4,"Placa:",0,0,"L",0);
 $pdf->cell(65,4,"Medida Saída:",0,0,"L",0);
 $pdf->cell(50,4,"Hora Saída:",0,0,"L",0);
 $pdf->cell(40,4,"Data Saída:","R",1,"L",0);
 $pdf->setfont('arial','b',7);
 for($y=0;$y<$linhas;$y++){
 db_fieldsmemory($result,$y);
 //$pdf->cell(45,4,$ed218_i_codigo,0,0,"L",0);
 $pdf->cell(35,4,$ve01_placa,0,0,"L",0);
 $pdf->cell(65,4,$ve60_medidasaida,0,0,"L",0);
 $pdf->cell(50,4,$ve60_horasaida,0,0,"L",0);
 $pdf->cell(40,4,db_formatar($ve60_datasaida,'d'),"R",1,"L",0);
 }
}else{
$pdf->setfont('arial','',7);
$pdf->cell(190,4,"Nenhum registro.","LR",0,"C",0);
}
/////////////////////////////////////////////

$pdf->setfont('arial','b',7);
$pdf->cell(190,4,"DADOS RETIRADA VEÍCULO",1,1,"C",1); //por periodo
$sql1= "select ve01_placa,
               ve61_medidadevol,
               ve61_horadevol,
               ve61_datadevol,
               ve60_medidasaida,
               ve60_horasaida,
               ve60_datasaida,
               ve60_destino
             from veicretirada
             inner join veicdevolucao on ve61_veicretirada= ve60_codigo
             inner join rotamov on ed220_i_veicretirada = ve60_codigo
             inner join rota on ed217_i_codigo = ed220_i_rota
             inner join veiculos on ve01_codigo = ve60_veiculo
             where ed220_i_rota=$chavepesquisa
             ";
$result1 = db_query($sql1);
$linhas1= pg_numrows($result1);
 if($linhas1>0){
 $pdf->cell(190,4,"","LR",1,"C",0);
 $pdf->setfont('arial','',7);
 $pdf->cell(5,24,"","L",0,"C",0);
 $pdf->cell(35,4,"Placa:",0,0,"L",0);
 $pdf->cell(40,4,"Data Retirada:",0,0,"L",0);
 $pdf->cell(65,4,"Data Devolução:",0,0,"L",0);
 $pdf->cell(45,4,"Destino:","R",1,"L",0);
 $pdf->setfont('arial','b',7);
 for($i=0;$i<$linhas1;$i++){
 db_fieldsmemory($result1,$i);
 $pdf->cell(40,4,$ve01_placa,0,0,"L",0);
 $pdf->cell(40,4,db_formatar($ve60_datasaida,'d'),0,0,"L",0);
 $pdf->cell(65,4,db_formatar($ve61_datadevol,'d'),0,0,"L",0);
 $pdf->cell(45,4,$ve60_destino,"R",1,"L",0);
 }
}else{
 $pdf->setfont('arial','',7);
 $pdf->cell(190,4,"Nenhum registro.","LR",0,"C",0);
 }
/////////////////////////////////////////////////////
$pdf->setfont('arial','b',7);
$pdf->cell(190,4,"GASTOS COM ABASTECIMENTO",1,1,"C",1);
$sql2 ="select ve70_litros,
               ve70_data,
               ve70_hora,
               ve70_medida,
               ve01_placa
               from veicabast
              inner join veicabastretirada on ve73_veicabast= ve70_codigo
              inner join veicretirada on ve60_codigo= ve73_veicretirada
              inner join veiculos on ve01_codigo = ve60_veiculo
              inner join rotamov on ed220_i_veicretirada = ve60_codigo
              inner join rota on ed217_i_codigo = ed220_i_rota
              where ed220_i_rota=$chavepesquisa
              ";
$result2 = db_query($sql2);
$linhas2= pg_numrows($result2);
 if($linhas1>0){
 $pdf->cell(190,4,"","LR",1,"C",0);
 $pdf->setfont('arial','',7);
 $pdf->cell(5,4,"","L",0,"C",0);
 $pdf->cell(30,4,"Placa:",0,0,"L",0);
 $pdf->cell(40,4,"Data:",0,0,"L",0);
 $pdf->cell(20,4,"Hora:",0,0,"L",0);
 $pdf->cell(30,4,"Litros:",0,0,"L",0);
 $pdf->cell(30,4,"Medida:",0,0,"L",0);
 //$pdf->cell(35,4,"Total valor:",0,0,"L",0);
 $pdf->cell(35,4,"Total KMs Retirada:","R",1,"L",0);
 $pdf->setfont('arial','b',7);
 for($i=0;$i<$linhas;$i++){
 db_fieldsmemory($result2,$i);
 $pdf->cell(5,4,"","L",0,"C",0);
 $pdf->cell(30,4,$ve01_placa,0,0,"L",0);
 $pdf->cell(40,4,db_formatar($ve70_data,'d'),0,0,"L",0);
 $pdf->cell(20,4,$ve70_hora,0,0,"L",0);
 $pdf->cell(30,4,$ve70_litros,0,0,"L",0);
 $pdf->cell(30,4,$ve70_medida,0,0,"L",0);
 //$pdf->cell(35,4,( $ve62_vlrmobra + $ve62_vlrpecas  ),0,0,"L",0);
 $pdf->cell(35,4,( $ve61_medidadevol - $ve60_medidasaida ),"R",1,"L",0);
 }
}else{
 $pdf->setfont('arial','',7);
 $pdf->cell(190,4,"Nenhum registro.","LR",0,"C",0);
 }
$pdf->cell(190,4,"","LRB",1,"C",0);
$pdf->Output();

?>