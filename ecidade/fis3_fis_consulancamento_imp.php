<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009 DBSeller Servicos de Informatica             
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
require_once(modification("libs/db_sql.php"));

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$head5 = "DADOS DA NOTIFICAÇÃO DE LANÇAMENTO";


$cllanc = new cl_fis_lancamento;

$clautoexec  = new cl_fis_autoexec;
$cllancexec  = new cl_fis_lancexec;
$cllanclocal = new cl_fis_lanclocal;

$sql2 = "select data_ciencia, y39_hora from fiscalizacao.fis_lancandam
LEFT JOIN fiscalizacao.fis_fandam ON nl19_codandam = y39_codandam
LEFT JOIN fiscalizacao.fis_datacienciaandamento ON fis_datacienciaandamento.fandam = y39_codandam where nl19_codlanc = $codlanc and data_ciencia is not null";
$oResulQuery2 = db_query($sql2);

$oResulQuery_busca= $cllanc->sql_record($cllanc->sql_query_busca($codlanc));

$oResulQuery_local=$cllanclocal->sql_record($cllanclocal->sql_query($codlanc));
  if ($cllanclocal->numrows>0){
    db_fieldsmemory($oResulQuery_local,0,true);
  }else{
    $rs2EndPecas = db_query( "select end01_rua as j14_nome, end01_numero as nl02_numero,end01_compl as  nl02_compl,end01_bairro as j13_descr
                                from fiscalizacao.fis_enderecopecas where end01_codpeca = {$codlanc} and end01_tipopeca = 'L' " );
   if( pg_num_rows( $rs2EndPecas ) > 0 ){
      db_fieldsmemory( $rs2EndPecas,0 );
   }
  }
  $oResulQuery_exec = $cllancexec->sql_record($cllancexec->sql_query($codlanc));

 
$sqllanc = "SELECT distinct nl01_codlanc,
                nl01_data,
                pl09_paragrafo,
                pl09_descr,
                pl30_texto from fiscalizacao.fis_lancamento 
                LEFT JOIN fiscalizacao.fis_paragrafolanc on nl01_codlanc = pl30_codlanc 
                LEFT JOIN fiscalizacao.fis_paragrafo on pl30_paragrafo = pl09_paragrafo and nl01_setor = pl09_coddepto 
                WHERE pl30_codlanc = {$codlanc} and pl09_status = true order by 3;";

$resultlanc = db_query($sqllanc);
$ln = pg_fetch_all($resultlanc);

 
db_fieldsmemory($oResulQuery_busca,0);
db_fieldsmemory($oResulQuery,0,true);
db_fieldsmemory($oResulQuery2,0,true);  
db_fieldsmemory($oResulQuery_exec,0,true);

$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial','',8);
$troca = 1;
$altH = 6;
$alt = 4;
$total = 0;
$id_usuarioaux = ""; 


   
    $pdf->addpage();
    $pdf->ln();
    $pdf->ln();
    $pdf->setfont('arial','b',10);
    $pdf->cell(180,$alt,"DADOS DA NOTIFICAÇÃO DE LANÇAMENTO",0,0,"C",0);
    $pdf->ln();
    $pdf->ln();
     
    $pdf->setfont('arial','b',8);
    
    $pdf->setX(30);
    $pdf->cell(100,$alt,"NOTIFICAÇÃO DE LANÇ. nº:    $nl01_codlanc",0,0,"L",0);
    $pdf->cell(90,$alt,"N° DO BLOCO:   $nl01_numbloco",0,1,"L",0);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"IDENTIFICAÇÃO:   $dl_identificacao",0,0,"L",0);
    $pdf->cell(90,$alt,"Código Ident.:   $dl_codigo",0,1,"L",0);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"Tipo de Fiscalização:   $tipo",0,0,"L",0);
    $pdf->cell(100,$alt,"Data de ciência:   $data_ciencia",0,1,"L",0);
    $pdf->setX(30);
     
    
    
    $pdf->cell(100,$alt,"Departamento:   $coddepto-$descrdepto",0,0,"L",0);
    $pdf->cell(90,$alt,"Hora:  $y39_hora",0,1,"L",0);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"Vencimento Atual:  $nl01_dtvenc",0,0,"L",0);
    $pdf->cell(90,$alt,"Prazo p/ Recurso:   $nl01_prazorec",0,1,"L",0);
    $pdf->setX(30);
    $pdf->cell(90,$alt,"Nome da Pessoa Autuada:   $nl01_nome",0,0,"L",0);
    $pdf->ln();
    $pdf->ln();
    $pdf->setX(30);
    $pdf->MultiCell(180,$alt,"Obs:   $y50_obs",0,1,"L",0);
    $pdf->ln();
    $pdf->setfont('arial','b',10);
    $pdf->cell(180,$alt,"Endereço Registrado",0,0,"C",0);
    $pdf->ln();
    $pdf->setfont('arial','b',8);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"Rua:   $j14_nome",0,0,"L",0);
    $pdf->cell(90,$alt,"Nº:   $nl02_numero",0,1,"L",0);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"Bairro:  $j13_descr",0,0,"L",0);
    $pdf->cell(90,$alt,"Complemento:   $nl02_compl",0,1,"L",0);
    $pdf->ln();
    $pdf->setfont('arial','b',10);
    $pdf->cell(180,$alt,"Endereço Localizado",0,0,"C",0);
    $pdf->ln();
    $pdf->ln();
    $pdf->setfont('arial','b',8);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"Rua:   $j14_nome",0,0,"L",0);
    $pdf->cell(90,$alt,"Nº:   $nl03_numero",0,1,"L",0);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"Bairro:  $j13_descr",0,0,"L",0);
    $pdf->cell(90,$alt,"Complemento:   $nl03_compl",0,1,"L",0);
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();
    $pdf->setfont('arial','b',10);
    $pdf->cell(180,$alt,"OUTROS DADOS DA PEÇA",0,0,"C",0);
    $pdf->ln();
    $pdf->ln();
    $pdf->setfont('arial','b',8);

foreach ($ln as $rln) {
    $pdf->setfont('arial','b',8);
    $pdf->ln();
    $pdf->MultiCell(190,$alt,$rln["pl09_descr"].": ",0,1,"L",0);
    $pdf->setfont('arial','',8);
    $pdf->MultiCell(190,$alt,$rln["pl30_texto"] ,0,1,"L",0);
}   
$pdf->Output();
?>