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
include(modification("classes/db_lotacao_classe.php"));
include(modification("classes/db_rhlota_classe.php"));
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
db_postmemory($HTTP_SERVER_VARS);

$clrhlota  = new cl_rhlota;

if(!isset($ano)){
  $ano = db_anofolha();
}
if(!isset($mes)){
  $mes = db_mesfolha();
}

$sql_lotacao = $clrhlota->sql_query_orgao(null,"
   r70_codigo,
   r70_estrut,
   r70_descr,           
   o40_codtri as o40_orgao,
   o40_descr
  ",
  "r70_codigo",
  "r70_codigo = '$lotacao' and o40_anousu = $ano and r70_instit = ".db_getsession('DB_instit'));
//die($sql_lotacao);
$result_lotacao = $clrhlota->sql_record($sql_lotacao);
if($clrhlota->numrows == 0){
  db_redireciona("db_erros.php?fechar=true&db_erro=Lotação não encontrada");
}

db_fieldsmemory($result_lotacao,0);

$sql_funcionarios = $clrhlota->sql_query_cgm(null,"rh01_regist as r01_regist,
                                                       z01_nome,
                                                       case when rh30_vinculo='A' 
                                                            then 'ATIVO' 
                                                            else case when rh30_vinculo='I' 
                                                                      then 'INATIVO' 
                                                                      else 'PENSIONISTA' 
                                                                 end 
                                                       end as vinculo,
                                                       rh37_funcao,
                                                       rh37_descr",
                                                      "z01_nome",
                                                      "    rh02_anousu  = $ano
                                                       and rh02_mesusu  = $mes
                                                       and rh02_instit  = ".db_getsession("DB_instit")."
                                                       and r70_instit  = ".db_getsession("DB_instit")."
                                                       and r70_codigo  = '$lotacao'
                                                       and rh05_seqpes is null
                                                      ");

// die($sql_funcionarios);

$result_funcionarios = $clrhlota->sql_record($sql_funcionarios);

if($clrhlota->numrows == 0){
  db_redireciona("db_erros.php?fechar=true&db_erro=Nenhuma lotação não encontrada");
}


$head4 = "LOTAÇÕES";

$head6 = $r70_codigo." - ".$r70_descr;
$head7 = $o40_orgao." - ".$o40_descr;

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$totalt = 0;

$troca = 1;
$p = 1;
$alt = 4;

for($x = 0; $x < pg_numrows($result_funcionarios); $x ++) {
  db_fieldsmemory($result_funcionarios, $x);
  if($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
    $pdf->addpage();
    $pdf->setfont('arial','b',8);

    $pdf->cell(15,$alt,"Registro",1,0,"C",1);
    $pdf->cell(80,$alt,"Nome",1,0,"C",1);
    $pdf->cell(15,$alt,"Cargo",1,0,"C",1);
    $pdf->cell(60,$alt,"Descrição",1,0,"C",1);
    $pdf->cell(15,$alt,"Vínculo",1,1,"C",1);

    $troca = 0;
  }

  $totalt++;
    
  $pdf->setfont('arial','',7);
  $pdf->cell(15,$alt,$r01_regist,"T",0,"C",0);
  $pdf->cell(80,$alt,$z01_nome,"T",0,"L",0);
  $pdf->cell(15,$alt,$rh37_funcao,"T",0,"C",0);
  $pdf->cell(60,$alt,$rh37_descr,"T",0,"L",0);
  $pdf->cell(15,$alt,$vinculo,"T",1,"L",0);
}

$pdf->setfont('arial','b',7);
$pdf->cell(185,$alt,"TOTAL DE REGISTROS  ".$totalt,"T",0,"L",0);
$pdf->Output();
?>