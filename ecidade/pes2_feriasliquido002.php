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
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("std/DBArray.php"));
require_once(modification("model/pessoal/relatorios/resumoFolha.model.php"));
require_once(modification("model/pessoal/ServidorRepository.model.php"));
require_once(modification("libs/JSON.php"));

$oGet = db_utils::postMemory($_GET);
$iInstituicao = db_getsession("DB_instit");

$iAno   =  $oGet->ano;
$iMes   =  $oGet->mes;
$exibeSemPeriodoGozo = $oGet->exibeSemPeriodoGozo;

$sOrdem = array(
    'n' => array("rh01_regist",  "Numérico") , 
    "a" => array("z01_nome" , "Alfabética")
);
$order  =  $sOrdem[$oGet->ordem];
$sOrder  = $order[0];

$head3 = "LÍQUIDO DE FÉRIAS";
$head5 = "Período: ".$iMes."/".$iAno;
$head7 = "Ordem: ".$order[1];

try {

   $whereExibeSemPeriodoGozo  = "and ((r30_per1i is not null and r30_per1f is not null)";
   $whereExibeSemPeriodoGozo .= "or (r30_per2i is not null and r30_per2f is not null))";

   $sSql = "select r30_regist,
       z01_nome,
       r30_perai,
       r30_peraf,
       case when r30_proc1 = '$iAno/$iMes' then r30_per1i else r30_per2i end as r30_per1i,
       case when r30_proc1 = '$iAno/$iMes' then r30_per1f else r30_per2f end as r30_per1f,
       case when r30_paga13 = 't' then 'sim' else 'nao' end as r30_paga13,
       (  
            select sum(r31_valor) from  gerffer  where 
            r31_pd = 1 and r31_regist = r30_regist     
            and r31_anousu = r30_anousu
            and r31_mesusu = r30_mesusu 
  
       ) as proventos,
               (  
            select sum(r31_valor) from  gerffer  where 
            r31_pd = 2 and r31_regist = r30_regist     
            and r31_anousu = r30_anousu
            and r31_mesusu = r30_mesusu 
  
        ) as descontos
       from cadferia
                inner join rhpessoal    on rh01_regist = r30_regist
                                inner join rhpessoalmov on rh02_anousu = r30_anousu
                                                       and rh02_mesusu = r30_mesusu
                                                                             and rh02_regist = r30_regist
                                                       and rh02_instit =  $iInstituicao
                inner join  cgm on z01_numcgm = rh01_numcgm
 


      where r30_anousu = $iAno
        and r30_mesusu = $iMes
        and (r30_proc1 = '$ano/$mes' or r30_proc2 = '$iAno/$iMes')";

   if($exibeSemPeriodoGozo == 0) {
       $sSql .= $whereExibeSemPeriodoGozo;
   }

   $sSql .= "order by ".$sOrder;

   $resource = db_query($sSql);  

   if (pg_num_rows($resource) == 0) {
       throw new BusinessException("Não há férias cadastradas para a competência informada.");
   }

   $data     = db_utils::getCollectionByRecord($resource);

   // Variáveis utilizadas no totalizador do relatório.
   $iValorTotalProventos = 0;
   $iValorTotalLiquido = 0;
   $iQuantidadeFuncionarios = 0;

   $pdf = new PDF();
   $pdf->Open();
   $pdf->AliasNbPages(); 
   $pdf->setfillcolor(235);
   $imprime_cabecalho = true;
   $alt = 4;
   foreach ($data as $servidor) {

      if ($pdf->gety() > $pdf->h - 30 || $imprime_cabecalho == true){
         $pdf->addpage();
         $pdf->setfont('arial','b',8);

         $pdf->cell(15,$alt,'MATRIC',1,0, \PDFDocument::ALIGN_CENTER,1);
         $pdf->cell(57,$alt,'NOME DO FUNCIONÁRIO',1,0,\PDFDocument::ALIGN_CENTER,1);
         $pdf->cell(32,$alt,'PERÍODO AQUISITIVO',1,0, \PDFDocument::ALIGN_CENTER,1);
         $pdf->cell(30,$alt,'PERÍODO DE GOZO',1,0, \PDFDocument::ALIGN_CENTER,1);
         $pdf->cell(30,$alt,'TOTAL PROVENTOS',1,0, \PDFDocument::ALIGN_CENTER,1);
         $pdf->cell(25,$alt,'TOTAL LÍQUIDO',1,1, \PDFDocument::ALIGN_CENTER,1);
         
         $pre = 1;
         $imprime_cabecalho = false;
      }
      
      if ($pre == 1) {
        $pre = 0;
      } else {
        $pre = 1;
      }

      $pdf->setfont('arial','',7);

      $iTotalLiquido = $servidor->proventos - $servidor->descontos;

      $pdf->cell(15,$alt,$servidor->r30_regist,0,0,"L",$pre);
      $pdf->cell(57,$alt,$servidor->z01_nome,0,0,"L",$pre);
      $pdf->cell(32,$alt,db_formatar($servidor->r30_perai,'d') .' - '. db_formatar($servidor->r30_peraf,'d'),0,0,"C",$pre);
      $pdf->cell(30,$alt,db_formatar($servidor->r30_per1i,'d'). ' - '. db_formatar($servidor->r30_per1f,'d'),0,0,"C",$pre);
      $pdf->cell(30,$alt,db_formatar($servidor->proventos,'f'),0,0, \PDFDocument::ALIGN_RIGHT, $pre);
      $pdf->cell(25,$alt,db_formatar($iTotalLiquido, 'f'),0,1, \PDFDocument::ALIGN_RIGHT, $pre);


      $iValorTotalProventos += $servidor->proventos;
      $iValorTotalLiquido += $iTotalLiquido;
      $iQuantidadeFuncionarios += 1;
   }

   // Impressão do totalizador do relatório.
   $pdf->ln(3);
   $pdf->setfont('arial', 'b', 8);
   $pdf->cell(50, 4, "QUANTIDADE DE FUNCIONÁRIOS:", 'T', 0, \PDFDocument::ALIGN_LEFT, 0);
   $pdf->cell(30, 4, $iQuantidadeFuncionarios, 'T', 0, \PDFDocument::ALIGN_LEFT, 0);
   $pdf->cell(54, 4, "TOTAL GERAL:", 'T', 0, \PDFDocument::ALIGN_RIGHT, 0);
   $pdf->cell(30, 4, db_formatar($iValorTotalProventos,'f'), 'T', 0, \PDFDocument::ALIGN_RIGHT, 0);
   $pdf->cell(25, 4, db_formatar($iValorTotalLiquido,'f'), 'T', 0, \PDFDocument::ALIGN_RIGHT, 0);
   
   $pdf->Output();

} catch(\Exception $eErro) {
    db_redireciona('db_erros.php?fechar=true&db_erro='. $eErro->getMessage());
    exit;   
}






