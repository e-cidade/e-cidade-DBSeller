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

include "fpdf151/pdfwebseller.php";

/**
 * @var $oPdf FPDF
 */
$oPdf = new PDF();
$oPdf->Open();
$oPdf->AliasNbPages();
if (!empty($titulo)) {
    $head1 = utf8_decode($titulo);
}
if (!empty($subtitulo)) {
    $head2 = utf8_decode($subtitulo);
}
$oPdf->ln(5);
$oPdf->addpage('L');
$oPdf->setfillcolor(235);
$oPdf->setfont('arial','b',7);

$aLinhas = json_decode(file_get_contents($sCaminhoArquivo), true);

foreach ($aLinhas as $cont => $linha) {
    $oPdf->multiCell(280, 4, utf8_decode(trim($linha)), 0, "J", ($cont % 2 ));

    if($cont == 0){
        $cabeçalho = trim($linha);
    }

    if($oPdf->gety() > $oPdf->h - 30){
        $oPdf->addpage('L');
        $oPdf->multicell(280,4,$cabeçalho,0,"J",1,0);
        $oPdf->multicell(280,4,"",0,"J",0,0);
    }
}



$oPdf->Output();
?>