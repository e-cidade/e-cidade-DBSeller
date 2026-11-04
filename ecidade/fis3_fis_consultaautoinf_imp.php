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

$head5 = "DADOS DO AUTO DE INFRAÇÃO";

$clauto = new cl_fis_auto;
$clautolocal = new cl_fis_autolocal;
$clautoexec = new cl_fis_autoexec;
// Query add em 08-06-2016
$sql  = "SELECT fis_auto.*, db_depart.*, fis_tipofiscaliza.*, cgm.*, fis_datacienciaandamento.* FROM fiscalizacao.fis_auto ";
$sql .= "INNER JOIN db_config ON db_config.codigo = fis_auto.y50_instit ";
$sql .= "INNER JOIN db_depart ON db_depart.coddepto = fis_auto.y50_setor ";
$sql .= "INNER JOIN fiscalizacao.fis_tipofiscaliza ON fis_tipofiscaliza.y27_codtipo = fis_auto.y50_codtipo ";
$sql .= "INNER JOIN cgm ON  cgm.z01_numcgm = db_config.numcgm ";
$sql .= "LEFT JOIN fiscalizacao.fis_procfiscalauto ON y111_auto = y50_codauto ";
$sql .= "LEFT JOIN fiscalizacao.fis_procfiscal ON y111_procfiscal = y100_sequencial ";
$sql .= "LEFT JOIN fiscalizacao.fis_autoandam ON y58_codauto = y50_codauto ";
$sql .= "LEFT JOIN fiscalizacao.fis_fandam ON y58_codandam = y39_codandam ";
$sql .= "LEFT JOIN fiscalizacao.fis_datacienciaandamento ON fis_datacienciaandamento.fandam = y39_codandam ";
$sql .= "LEFT JOIN fiscalizacao.fis_tipoandam ON y39_codtipo = y41_codtipo ";
$sql .= "LEFT JOIN fiscalizacao.fis_grupotipoandamento_tipoandam ON fi30_tipoandam = y41_codtipo ";
$sql .= "LEFT JOIN fiscalizacao.fis_grupotipoandamento ON fi30_grupo = fis_grupotipoandamento.sequencial ";
$sql .= "WHERE y50_codauto = $codauto and y50_instit = ".db_getsession('DB_instit');
$sql .= " ";

$result = db_query($sql);
$num = pg_num_rows($result);

$sql2 = "select data_ciencia from fiscalizacao.fis_autoandam
LEFT JOIN fiscalizacao.fis_fandam ON y58_codandam = y39_codandam
LEFT JOIN fiscalizacao.fis_datacienciaandamento ON fis_datacienciaandamento.fandam = y39_codandam where y58_codauto = $codauto and data_ciencia is not null";
$result2 = db_query($sql2);

$result_busca= $clauto->sql_record($clauto->sql_query_busca($codauto));

$result_local=$clautolocal->sql_record($clautolocal->sql_query($codauto));
if ($clautolocal->numrows>0){
  db_fieldsmemory($result_local,0,true);
}else{
  $rs2EndPecas = db_query( "select end01_rua as j14_nome, end01_numero as y14_numero,end01_compl as  y14_compl,end01_bairro as j13_descr
                              from fiscalizacao.fis_enderecopecas where end01_codpeca = {$codauto} and end01_tipopeca = 'A' " );
 if( pg_num_rows( $rs2EndPecas ) > 0 ){
    db_fieldsmemory( $rs2EndPecas,0 );
 }
}

$result_exec=$clautoexec->sql_record($clautoexec->sql_query($codauto));
  
    
 


$sqlauto = "SELECT distinct y50_codauto,
                y50_data,
                pl09_paragrafo,
                pl09_descr,
                pl10_texto from fiscalizacao.fis_auto 
                LEFT JOIN fiscalizacao.fis_paragrafoauto on y50_codauto = pl10_auto 
                LEFT JOIN fiscalizacao.fis_paragrafo on pl10_paragrafo = pl09_paragrafo and y50_setor = pl09_coddepto 
                WHERE pl10_auto = {$codauto} and pl09_status = true order by 3;";
$resultauto = db_query($sqlauto);
$ln = pg_fetch_all($resultauto);

db_fieldsmemory($result,0,true);
db_fieldsmemory($result2,0,true);
db_fieldsmemory($result_busca,0);
db_fieldsmemory($result_exec,0,true);

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
    $pdf->cell(180,$alt,"DADOS DO AUTO DE INFRAÇÃO",0,0,"C",0);
    $pdf->ln();
    $pdf->ln();
     
    $pdf->setfont('arial','b',8);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"AUTO nº:    $y50_codauto",0,0,"L",0);
    $pdf->cell(90,$alt,"N° DO BLOCO:   $y50_numbloco",0,1,"L",0);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"IDENTIFICAÇÃO:   $dl_identificacao",0,0,"L",0);
    $pdf->cell(90,$alt,"Código Ident.:   $dl_codigo",0,1,"L",0);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"Tipo de Fiscalização:   $tipo",0,0,"L",0);
    $pdf->cell(100,$alt,"Data de ciência:   $data_ciencia",0,1,"L",0);
    $pdf->setX(30);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"Departamento:   $coddepto-$descrdepto",0,0,"L",0);
    $pdf->cell(90,$alt,"Hora:   $y50_hora",0,1,"L",0);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"Vencimento Atual:  $y50_dtvenc",0,0,"L",0);
    $pdf->cell(90,$alt,"Prazo p/ Recurso:   $y50_prazorec",0,1,"L",0);
    $pdf->setX(30);
    $pdf->cell(90,$alt,"Nome da Pessoa Autuada:   $y50_nome",0,0,"L",0);
    $pdf->ln();
    $pdf->ln();
    $pdf->setX(30);
    $pdf->MultiCell(180,$alt,"Obs:   $y50_obs",0,1,"L",0);
    $pdf->ln();
    $pdf->ln();
    $pdf->setfont('arial','b',10);
    $pdf->cell(180,$alt,"Endereço Registrado",0,0,"C",0);
    $pdf->ln();
    $pdf->ln();
    $pdf->setfont('arial','b',8);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"Rua:   $j14_nome",0,0,"L",0);
    $pdf->cell(90,$alt,"Nº:   $y14_numero",0,1,"L",0);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"Bairro:  $j13_descr",0,0,"L",0);
    $pdf->cell(90,$alt,"Complemento:   $y14_compl",0,1,"L",0);
    $pdf->ln();
    $pdf->setfont('arial','b',10);
    $pdf->cell(180,$alt,"Endereço Localizado",0,0,"C",0);
    $pdf->ln();
    $pdf->ln();
    $pdf->setfont('arial','b',8);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"Rua:   $j14_nome",0,0,"L",0);
    $pdf->cell(90,$alt,"Nº:   $y15_numero",0,1,"L",0);
    $pdf->setX(30);
    $pdf->cell(100,$alt,"Bairro:  $j13_descr",0,0,"L",0);
    $pdf->cell(90,$alt,"Complemento:   $y15_compl",0,1,"L",0);
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();
    $pdf->setfont('arial','b',10);
    $pdf->cell(180,$alt,"OUTROS DADOS DA PEÇA",0,0,"C",0);
    $pdf->ln();
    $pdf->ln();
    

foreach ($ln as $rln) {
    $pdf->setfont('arial','b',8);
    $pdf->ln();
    $pdf->MultiCell(190,$alt,$rln["pl09_descr"].": ",0,1,"L",0);
    $pdf->setfont('arial','',8);
    $pdf->MultiCell(190,$alt,$rln["pl10_texto"] ,0,1,"L",0);
}   
$pdf->Output();
?>