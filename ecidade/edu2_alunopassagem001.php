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
include(modification("classes/db_linha_classe.php"));
include(modification("classes/db_alunopassagem_classe.php"));
include(modification("classes/db_alunopassagemqtd_classe.php"));
include(modification("classes/db_valorpassagem_classe.php"));
$clescola = new cl_escola;
$claluno = new cl_aluno;
$cllinha = new cl_linha;
$clalunopassagem = new cl_alunopassagem;
$clalunopassagemqtd = new cl_alunopassagemqtd;
$clvalorpassagem = new cl_valorpassagem;
$escola = db_getsession("DB_coddepto");
$result1 = $clalunopassagem->sql_record($clalunopassagem->sql_query("","*","","ed215_i_codigo = $chavepesquisa"));
if($clalunopassagem->numrows==0){?>
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
db_fieldsmemory($result1,0);
$pdf->setfillcolor(223);
$head1 = "RELATÓRIO DE ALUNOS POR PASSAGENS";
$head2 = "Aluno:  $ed47_i_codigo - $ed47_v_nome";
$pdf->addpage('P');
$pdf->ln(5);

/////////////////////////////////////////////
$pdf->setfont('arial','b',7);
$pdf->cell(190,4,"DADOS ALUNOS COM PASSAGENS",1,1,"C",1);
$pdf->cell(190,4,"","LR",1,"C",0);

$pdf->setfont('arial','',7);
$pdf->cell(5,8,"","L",0,"C",0);
//$pdf->cell(10,4,"Código:",0,0,"L",0);
$pdf->cell(40,4,"Nome:",0,0,"L",0);
$pdf->cell(25,4,"Data Cadastro:",0,0,"L",0);
$pdf->cell(40,4,"Escola:",0,0,"L",0);
$pdf->cell(40,4,"Origem:",0,0,"L",0);
$pdf->cell(40,4,"Destino:","R",1,"L",0);
$pdf->setfont('arial','b',7);
for($x=0;$x<$clalunopassagem->numrows;$x++){
  db_fieldsmemory($result1,$x);
//$pdf->cell(10,4,$ed227_i_codigo,0,0,"L",0);
$pdf->cell(40,4,$ed47_v_nome,0,0,"L",0);
$pdf->cell(30,4,db_formatar($ed215_d_datacad,'d'),0,0,"L",0);
$pdf->cell(40,4,$ed18_c_nome,0,0,"L",0);
$pdf->cell(40,4,$ed217_c_origem,0,0,"L",0);
$pdf->cell(40,4,$ed217_c_destino,"R",1,"L",0);
}
$pdf->cell(190,4,"","LRB",1,"C",0);
$pdf->Output();
?>