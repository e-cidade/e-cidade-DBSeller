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

require_once(modification("fpdf151/pdf.php"));
require_once(modification("fpdf151/assinatura.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));

$head2          = "RELATORIO DE EXAMES CONFIRMADOS";
$sWhere         = "";
$sCampoVerifica = "s110_i_codigo"; 
$sOrder         = "z01_nome,sd63_c_nome,z01_v_nome";

if (isset($perini) && trim($perini) != "" && isset($perfim) && trim($perfim) != ""){
  
  $head3 = "Período: {$perini} à {$perfim}";
  if ($sWhere != "") {
    $sWhere .= " and ";
  }
  $sWhere .= " s113_d_exame between '".implode("-", array_reverse(explode("/",$perini)))."'";
  $sWhere .= " and '".implode("-", array_reverse(explode("/",$perfim)))."'";
}
//Protocolados
if (isset($protocolados) && $protocolados == 1) {

  if ($sWhere != "") {
    $sWhere .= " and ";
  }
  $sWhere .= " s133_i_codigo is not null"; 
} else if (isset($protocolados) && $protocolados == 2) {
  
  if ($sWhere != "") {
    $sWhere .= " and ";
  }
  $sWhere .= " s133_i_codigo is null";
	
}
//Prestadora 
if (isset($prestadora) && trim($prestadora) != "") {

  if ($sWhere != "") {
    $sWhere .= " and ";
  }
  $sWhere .= "s110_i_codigo = {$prestadora}";	
}

//Exame
if(isset($exame) && trim($exame) != ""){
	
   if ($sWhere != "") {
    $sWhere .= " and ";
  }
  $sWhere .= "sd63_c_procedimento = '$exame'";
    
}

//Verifica qual layout utilizar
if(isset($producao) && $producao == 1){
	  
  $sSql   = "select s113_i_codigo,";
  $sSql  .= "        s133_c_protocolo,";
  $sSql  .= "        z01_v_nome, ";
  $sSql  .= "        s110_i_codigo,"; 
  $sSql  .= "        z01_nome, ";
  $sSql  .= "        z01_ender, ";
  $sSql  .= "        z01_bairro, ";
  $sSql  .= "        z01_munic, ";
  $sSql  .= "        z01_i_cgsund, ";
  $sSql  .= "        sd63_i_codigo,";
  $sSql  .= "        sd63_c_procedimento,";
  $sSql  .= "        sd63_c_nome, ";
  $sSql  .= "        s113_c_encaminhamento,";
  $sSql  .= "        s113_d_exame, ";
  $sSql  .= "        s113_c_hora,   ";
  $sSql  .= "        z01_d_nasc,  ";
  $sSql  .= "        z01_v_cgccpf,s133_i_codigo, ";
  $sSql  .= "        s133_c_observacoes";
  $sSql  .= "   from sau_agendaexames";
  $sSql  .= "        inner join sau_prestadorhorarios on s112_i_codigo = s113_i_prestadorhorarios";
  $sSql  .= "        inner join cgs_und on z01_i_cgsund = s113_i_numcgs  ";
  $sSql  .= "        left join sau_agendaexameconfirma on s113_i_codigo = s133_i_agendaexames ";
  $sSql  .= "        inner join sau_prestadorvinculos on s111_i_codigo = s112_i_prestadorvinc ";
  $sSql  .= "        inner join sau_prestadores on s110_i_codigo = s111_i_prestador ";
  $sSql  .= "        inner join cgm on s110_i_numcgm = z01_numcgm "; 
  $sSql  .= "        inner join sau_procedimento on s111_procedimento   = sd63_i_codigo "; 
  $sSql  .= "  where {$sWhere}";
  $sSql  .= "  order by {$sOrder}";  

  $rsRelatorio = db_query($sSql);
  $aRegistros  = db_utils::getCollectionByRecord($rsRelatorio);
  if (pg_num_rows($rsRelatorio) == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Não há dados a serem impressos");
  }
  $pdf = new PDF("L", "mm", "A4"); 
  $pdf->Open(); 
  $pdf->AliasNbPages(); 
  $pdf->SetAutoPageBreak(false);
  $pdf->setfillcolor(235);
  $pdf->setfont('arial','b',7);
  $alt            = 4;
  $pagina         = 1;
  $pdf->addpage();
  $pdf->setfont('arial','',10);
  $sValorVerificarAnterior = "";
  $verificarProcedimentoAnterior = "";  
  foreach ($aRegistros as $oLinhaRelatório) {
    
    $sValorVerificar = $oLinhaRelatório->s110_i_codigo;
    $verificarProcedimento = $oLinhaRelatório->sd63_c_procedimento;

    if ($sValorVerificarAnterior != $sValorVerificar) {    
      
      if ($pdf->GetY() > $pdf->h -25) {
        $pdf->AddPage();
      }

      $pdf->cell(25,$alt,'','',1,"R",0);

      $pdf->cell(25,$alt,'Prestadora:',0,0,"L",0);
      $pdf->cell(130,$alt,"{$oLinhaRelatório->s110_i_codigo} - {$oLinhaRelatório->z01_nome}",0,1,"L",0);

      $pdf->cell(25,$alt,'Endereço:',0,0,"L",0);
      $pdf->cell(110,$alt,$oLinhaRelatório->z01_ender,0,0,"L",0);
      $pdf->cell(10,$alt,'Bairro:','',0,"R",0);
      $pdf->cell(30,$alt, $oLinhaRelatório->z01_bairro,'',0,"L",0);      
      $pdf->cell(10,$alt,'Cidade:','',0,"R",0);
      $pdf->cell(30,$alt,$oLinhaRelatório->z01_munic,'',1,"L",0);


      $sValorVerificarAnterior = $sValorVerificar;
    }
        
    if($verificarProcedimentoAnterior != $verificarProcedimento){
      
      $pdf->ln();
      $pdf->cell(25,$alt,'Exame:','',0,"L",0);
      $pdf->cell(110,$alt,"{$oLinhaRelatório->sd63_c_procedimento} - {$oLinhaRelatório->sd63_c_nome}",'',1,"L",0);

      imprimeColunasRelatorio($pdf,$alt);
      $verificarProcedimentoAnterior = $verificarProcedimento;
    }

  	$pdf->cell(90,$alt,$oLinhaRelatório->z01_v_nome,'RTB',0,"L",0);
  	$pdf->cell(30,$alt,db_formatar($oLinhaRelatório->s113_d_exame,"d")." ".$oLinhaRelatório->s113_c_hora,'RTB',0,"C",0);
  	$pdf->cell(25,$alt,$oLinhaRelatório->s133_c_protocolo,'RTB',0,"C",0);
  	$pdf->cell(35,$alt,$oLinhaRelatório->s113_c_encaminhamento,'RTB',0,"C",0);
  	$pdf->cell(90,$alt,'','TB',1,"L",0);
  }
}else if (isset($producao) && $producao == 2){
    
  $sSql   = "select distinct";
  $sSql  .= "        sd63_i_codigo,";
  $sSql  .= "        sd63_c_procedimento,";
  $sSql  .= "        sd63_c_nome, ";
  $sSql  .= "        s113_d_exame ";
  $sSql  .= "   from sau_agendaexames";
  $sSql  .= "        inner join sau_prestadorhorarios on s112_i_codigo = s113_i_prestadorhorarios";
  $sSql  .= "        inner join cgs_und on z01_i_cgsund = s113_i_numcgs  ";
  $sSql  .= "        left  join sau_agendaexameconfirma on s113_i_codigo = s133_i_agendaexames ";
  $sSql  .= "        inner join sau_prestadorvinculos on s111_i_codigo = s112_i_prestadorvinc ";
  $sSql  .= "        inner join sau_prestadores on s110_i_codigo = s111_i_prestador ";
  $sSql  .= "        inner join cgm on s110_i_numcgm = z01_numcgm "; 
  $sSql  .= "        inner join sau_procedimento on s111_procedimento = sd63_i_codigo "; 
  $sSql  .= "  where {$sWhere}";
  $sSql  .= "  order by sd63_c_procedimento";
  
  $rsRelatorio = db_query($sSql);
  $aRegistros  = db_utils::getCollectionByRecord($rsRelatorio);
  if (pg_num_rows($rsRelatorio) == 0) {
   db_redireciona("db_erros.php?fechar=true&db_erro=Não há dados a serem impressos");
  }
  
  $pdf = new PDF("P", "mm", "A4"); 
  $pdf->Open(); 
  $pdf->AliasNbPages(); 
  $pdf->setfillcolor(235);
  $pdf->setfont('arial','b',7);
  $alt            = 4;
  $pagina         = 1;
  $pdf->addpage();
  $pdf->setfont('arial','',10);
  
  foreach ($aRegistros as $oLinhaRelatório) {
    
    $pdf->setfont('arial','',10);
    $pdf->cell(15,$alt,'Exame: ','',0,"L",0);
    $pdf->cell(110,$alt,"{$oLinhaRelatório->sd63_c_procedimento} - {$oLinhaRelatório->sd63_c_nome}",'',0,"L",0);
    $pdf->Ln();
    $pdf->cell(160,$alt,'Prestadora','RT',0,"C" ,1);
    $pdf->cell(30,$alt,'Quantidade','T',1,"C", 1);
    $sSql   = "select  z01_nome,";
    $sSql  .= "        count(*) as total";
    $sSql  .= " from sau_agendaexames";
    $sSql  .= "        inner join sau_prestadorhorarios on s112_i_codigo = s113_i_prestadorhorarios";
    $sSql  .= "        inner join cgs_und on z01_i_cgsund = s113_i_numcgs  ";
    $sSql  .= "        left  join sau_agendaexameconfirma on s113_i_codigo = s133_i_agendaexames ";
    $sSql  .= "        inner join sau_prestadorvinculos on s111_i_codigo = s112_i_prestadorvinc ";
    $sSql  .= "        inner join sau_prestadores on s110_i_codigo = s111_i_prestador ";
    $sSql  .= "        inner join cgm on s110_i_numcgm = z01_numcgm "; 
    $sSql  .= "        inner join sau_procedimento on s111_procedimento = sd63_i_codigo "; 
    $sSql  .= "  where {$sWhere}";
    $sSql  .= "    AND sd63_i_codigo = {$oLinhaRelatório->sd63_i_codigo}";
    $sSql  .= "  group by  z01_nome order by z01_nome";
    $rsTotais = db_query($sSql);
    $aTotais  = db_utils::getCollectionByRecord($rsTotais); 
    $pdf->setfont('arial','',8);
  	foreach ($aTotais as $oTotal) {
      
  	  $pdf->cell(160,$alt,$oTotal->z01_nome,'RBT',0,"L",0);
  	  $pdf->cell(30,$alt,$oTotal->total,'TB',1,"R",0);
  	  
     }
     $pdf->ln();
  }
}

$pdf->ln();
$pdf->Output();

function imprimeColunasRelatorio($pdf,$alt){

  $pdf->cell(90,$alt,'Pacientes','RT',0,"C",1);
  $pdf->cell(30,$alt,'Dia / Hora','RT',0,"C",1);
  $pdf->cell(25,$alt,'Protocolo','RT',0,"C",1);
  $pdf->cell(35,$alt,'Encaminhamento','RT',0,"C",1);
  $pdf->cell(90,$alt,'Profissional','T',1,"C",1);
}
?>