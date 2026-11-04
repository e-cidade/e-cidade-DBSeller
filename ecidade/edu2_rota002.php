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
include(modification("classes/db_aluno_classe.php"));
include(modification("classes/db_escola_classe.php"));
include(modification("classes/db_rota_classe.php"));
include(modification("classes/db_itinerario_classe.php"));
include(modification("classes/db_rotaaluno_classe.php"));
include(modification("classes/db_rotamov_classe.php"));
include(modification("classes/db_veicretirada_classe.php"));
$clrotamov = new cl_rotamov;
$clveicretirada = new cl_veicretirada;
$clescola = new cl_escola;
$clitinerario = new cl_itinerario;
$clrotaaluno = new cl_rotaaluno;
$claluno = new cl_aluno;
$clrota = new cl_rota;
$escola = db_getsession("DB_coddepto");
$result = $clrota->sql_record($clrota->sql_query("","*","","ed217_i_codigo = $chavepesquisa"));
if($clrota->numrows==0){?>
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
$head1 = "RELATÓRIO DE ROTAS";
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
for($x=0;$x<$clrota->numrows;$x++){
  db_fieldsmemory($result,$x);
//$pdf->cell(10,4,$ed217_i_codigo,0,0,"L",0);
$pdf->cell(65,4,$ed217_c_nome,0,0,"L",0);
$pdf->cell(55,4,$ed217_c_descr,0,0,"L",0);
$pdf->cell(45,4,db_formatar($ed217_d_datacad,'d'),0,0,"L",0);
$pdf->cell(25,4,$ed217_f_kmdia,"R",1,"L",0);
}
$pdf->setfont('arial','b',7);
$pdf->cell(190,4,"DADOS ITINERÁRIO",1,1,"C",1);
$result1 = $clitinerario->sql_record($clitinerario->sql_query("","*","","ed218_i_codigo = $chavepesquisa"));
$pdf->cell(190,4,"","LR",1,"C",0);
$pdf->setfont('arial','',7);
$pdf->cell(5,4,"","L",0,"C",0);
//$pdf->cell(45,4,"Código:",0,0,"L",0);
$pdf->cell(55,4,"Nome:",0,0,"L",0);
$pdf->cell(55,4,"Data Cadastro:",0,0,"L",0);
$pdf->cell(75,4,"Descrição:","R",1,"L",0);
$pdf->setfont('arial','b',7);
for($y=0;$y<$clitinerario->numrows;$y++){
  db_fieldsmemory($result1,$y);
//$pdf->cell(45,4,$ed218_i_codigo,0,0,"L",0);
$pdf->cell(55,4,$ed218_v_nome,0,0,"L",0);
$pdf->cell(65,4,db_formatar($ed218_d_datacad,'d'),0,0,"L",0);
$pdf->cell(70,4,$ed218_c_descr,"R",1,"L",0);
}
/////////////////////////////////////////////

$pdf->setfont('arial','b',7);
$pdf->cell(190,4,"ALUNOS POR ROTA",1,1,"C",1);
$result3 = $clrotaaluno->sql_record($clrotaaluno->sql_query("","*","","ed219_i_rota = $chavepesquisa"));
$pdf->cell(190,4,"","LR",1,"C",0);
$pdf->setfont('arial','',7);
$pdf->cell(5,24,"","L",0,"C",0);
//$pdf->cell(30,4,"Código:",0,0,"L",0);
$pdf->cell(80,4,"Nome:",0,0,"L",0);
$pdf->cell(40,4,"Data Início:",0,0,"L",0);
$pdf->cell(65,4,"Data Fim:","R",1,"L",0);
$pdf->setfont('arial','b',7);
for($i=0;$i<$clrotaaluno->numrows;$i++){
  db_fieldsmemory($result3,$i);
//$pdf->cell(30,4,$ed219_i_codigo,"R",0,"L",0);
$pdf->cell(80,4,$ed47_v_nome,0,0,"L",0);
$pdf->cell(40,4,db_formatar($ed219_d_datainicio,'d'),0,0,"L",0);
$pdf->cell(70,4,db_formatar($ed219_d_datafim,'d'),"R",1,"L",0);
}
/////////////////////////////////////////////////////
$pdf->setfont('arial','b',7);
$pdf->cell(190,4,"MOVIMENTAÇÃO DAS ROTAS",1,1,"C",1);
$result3 = $clrotamov->sql_record($clrotamov->sql_query("","*","","ed220_i_rota = $chavepesquisa"));
if($clrotamov->numrows>0){
$pdf->cell(190,4,"","LR",1,"C",0);
$pdf->setfont('arial','',7);
$pdf->cell(5,4,"","L",0,"C",0);
//$pdf->cell(30,4,"Código:",0,0,"L",0);
$pdf->cell(80,4,"Nome:",0,0,"L",0);
$pdf->cell(40,4,"Data Cadastro:",0,0,"L",0);
$pdf->cell(30,4,"Código Retirada:",0,0,"L",0);
$pdf->cell(35,4,"Hora Cadastro:","R",1,"L",0);
$pdf->setfont('arial','b',7);
for($i=0;$i<$clrotamov->numrows;$i++){
  db_fieldsmemory($result3,$i);
//$pdf->cell(30,4,$ed220_i_codigo,"R",0,"L",0);
$pdf->cell(5,4,"","L",0,"C",0);
$pdf->cell(80,4,$ed217_c_nome,0,0,"L",0);
$pdf->cell(40,4,db_formatar($ed220_d_datacad,'d'),0,0,"L",0);
$pdf->cell(30,4,$ve60_veiculo,0,0,"L",0);
$pdf->cell(35,4,$ed220_c_horacad,"R",1,"L",0);
}
}else{
$pdf->setfont('arial','',7);
$pdf->cell(190,4,"Nenhum registro.","LR",0,"C",0);
}
$pdf->cell(190,4,"","LRB",1,"C",0);
$pdf->Output();

?>