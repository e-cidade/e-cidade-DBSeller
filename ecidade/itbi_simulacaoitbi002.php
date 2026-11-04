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
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

use ECidade\Tributario\Arrecadacao\Repository\TaxasLancadasRepository;

// dd($_GET);

$clitbitrasacao = new \cl_itbitransacao();

db_postmemory($_GET);

$rTipoTransacao = $clitbitrasacao->sql_record($clitbitrasacao->sql_query_file($it01_tipotransacao));
$oTipoTransacao = \db_utils::fieldsMemory($rTipoTransacao, 0);

$dadosDasTaxas = JSON::create()->parse(str_replace("\\", "", $taxa));
$dadosDePgto = JSON::create()->parse(str_replace("\\", "", $dadosPgto));


$iPreencheFundo = 1;
$alt="5";

$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$head2 = "RELATÓRIO DE SIMULAÇÃO DE ITBI";

$head3 = "Período: " . date("d/m/Y");

$pdf->AddPage("P");

$pdf->Rect(0.2, 0, 0, 300);
$pdf->Rect(209.5, 0, 0, 300);

// Transmitente
$pdf->SetFont('Arial', '', 9);
$pdf->Rect(0, 33, 210, 20);
$pdf->SetXY(3, 34);
$pdf->cell(15,3,'Transmitente',0,0,"C");
$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(7, 39);
$pdf->cell(46,4,"Nome do Transmitente: ".$z01_nome_transmitente,0,0,"L");
$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(7, 44);
$pdf->cell(37,4,'COD.Contribuinte: '.$it21_numcgm,0,0,"L");
$pdf->SetXY(7, 49);
$pdf->cell(50,4,'Endereço: '.$it03_endereco." ".$it03_numero." ".$it03_bairro." ".$it03_munic. " ".$it03_uf." ".$it03_cep,0,0,"L");

// ADQUIRENTE
$pdf->SetFont('Arial', '', 9);
$pdf->Rect(0, 33, 210, 20);
$pdf->SetXY(2, 57);
$pdf->cell(15,3,'Adquirente',0,0,"C");
$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(7, 61);
$pdf->cell(46,4,"Nome do Adquirente: ".$z01_nome_adquirentes,0,0,"L");
$pdf->SetFont('Arial', '', 8);

$pdf->SetXY(7, 66);
$pdf->cell(7,4,'Cod.Contribuinte: '.$it21_numcgm_adquirentes,0,0,"L");
$pdf->SetXY(7, 71);
$pdf->cell(50,4,'Endereço: '.$it03_endereco_adquirentes." ".$it03_numero_adquirentes." ".$it03_bairro_adquirentes." ".$it03_munic_adquirentes. " ".$it03_uf_adquirentes." ".$it03_cep_adquirentes,0,0,"L");

//Informações
$pdf->SetFont('Arial', '', 9);
$pdf->Rect(0, 53, 210, 24);
$pdf->SetXY(3, 79);
$pdf->cell(15,3,'Informações',0,0,"C");
$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(7, 83);
$pdf->cell(46,4,'Inscrição: ',0,0,"L");
$pdf->SetXY(22, 83);
$pdf->cell(18,4,$j01_matric,0,0,"R");


$pdf->SetXY(85, 83);
$pdf->cell(25,4,"Área Lote: ",0,0,"L");
$pdf->SetXY(100, 83);
$pdf->cell(25,4,$it01_areaterreno,0,0,"R");

$pdf->SetXY(7, 89);
$pdf->cell(25,4,"Área Unidade: ",0,0,"L");
$pdf->SetXY(26, 89);
$pdf->cell(14,4,$it01_areatrans,0,0,"R");
$pdf->SetFont('Arial', '', 8);


$pdf->SetXY(7, 95);
$pdf->cell(25,4,'Fração Ideal: ',0,0,"L");
$pdf->SetXY(26, 95);
$pdf->cell(14,4,'100',0,0,"R");


$pdf->SetXY(85, 89);
$pdf->cell(25,4,'Vlr.Venal:',0,0,"L");

$pdf->SetXY(85, 95);
$pdf->cell(25,4,'Setor: ',0,0,"L");
$pdf->SetXY(100, 95);
$pdf->cell(25,4,"$it22_setor",0,0,"R");

$pdf->SetXY(143, 83);
$pdf->cell(25,4,"Quadra: ",0,0,"L");
$pdf->SetXY(180, 83);
$pdf->cell(25,4,$it22_quadra,0,0,"R");

$pdf->SetXY(143, 89);
$pdf->cell(25,4,"Lote: ",0,0,"L");
$pdf->SetXY(180, 89);
$pdf->cell(25,4,$it22_lote,0,0,"R");

$pdf->SetXY(143, 95);
$pdf->cell(25,4,"Endereço: ",0,0,"L");
$pdf->SetXY(201.5, 95);
$pdf->cell(25,4,$it22_descrlograd,0,0,"R");

$pdf->SetXY(100, 89);
$pdf->cell(25,4,db_formatar($it01_valortransacao, 'f'),0,0,"R");


// DADOS TRANSAÇÕES

$pdf->SetFont('Arial', '', 9);
$pdf->Rect(0, 102, 210, 30);

$pdf->SetXY(7, 104);
$pdf->cell(19,3,'Dados de Transação',0,0,"C");
$pdf->SetFont('Arial', '', 8);

$pdf->SetXY(7, 108);
$pdf->cell(25,4,'Tipo de Transação: ',0,0,"L");

$pdf->SetXY(40, 108);
$pdf->cell(29,4,$it04_descr,0,0,"L");

$yFormaPgto = 113;
//+5

foreach($dadosDePgto as $formaPgto) {
    $formaPgtoValor = str_replace(".", "", $formaPgto->valor);
    $formaPgtoValor = str_replace(",", ".", $formaPgtoValor);
    $formaPgtoValor = db_formatar($formaPgtoValor, 'f');

    
    $pdf->SetXY(7, $yFormaPgto);
    $pdf->cell(37,4, "$formaPgto->descricao:                                 $formaPgtoValor",0,0,"L");
    $pdf->SetXY(85, $yFormaPgto);
    $pdf->cell(37,4,"Aliquota: $formaPgto->aliquota",0,0,"L");

    $yFormaPgto += 5;
}








// OBSERVAÇÕES **

// Linhas na vertical
$pdf->Rect(40, 132, 0, 118);
$pdf->Rect(170, 132, 0, 118);

//Linha Horizontal
$pdf->Rect(0, 143, 220, 0);
$pdf->Rect(0, 132, 220, 0);

// Código
$pdf->SetXY(0, 136);
$pdf->cell(40,4,'Código',0,0,"C");


// Tributo
$pdf->SetXY(40, 136);
$pdf->cell(130,4,'Tributo',0,0,"C");

// Valor
$pdf->SetXY(170, 136);
$pdf->cell(40,4,'Valor',0,0,"C");

//+4
$pdf->SetXY(30, 144);
$pdf->cell(10,4,"20",0,0,"R");


// Tributos
$pdf->SetXY(40, 144);
$pdf->cell(10,4,"ITBI",0,0,"L");



// Valores
$pdf->SetXY(197, 144);
$pdf->cell(13,4,$imposto_avalia,0,0,"R");


// // Código Taxas
$y = 140;

$imposto_avalia = str_replace(".", "", $imposto_avalia);
$imposto_avalia = str_replace(",", ".", $imposto_avalia);

$valorDesconto = $oTipoTransacao->it04_desconto * ($imposto_avalia / 100);

$subtotal = $imposto_avalia;
foreach($dadosDasTaxas as $taxas) {


    $taxasRepository = TaxasLancadasRepository::getInstance();
    $retorno = $taxasRepository->getTaxa($taxas->id);


    $y += 4;

    //+4
    $pdf->SetXY(30, $y +4);
    $pdf->cell(10,4,$retorno->ar44_receita,0,0,"R");


    // Tributos
    $pdf->SetXY(40, $y + 4);
    $pdf->cell(10,4,$taxas->nome,0,0,"L");



    // Valores
    $pdf->SetXY(197, $y + 4);
    $pdf->cell(13,4,$taxas->valor,0,0,"R");

    $valor = str_replace(".", "", $taxas->valor);
    $valor = str_replace(",", ".", $valor);

    $subtotal += (float)$valor;

}

$totalRecolher = $subtotal - $valorDesconto;




// OBSERVAÇÕES

// Linhas Verticais
$pdf->Rect(170, 250, 0, 20);
$pdf->Rect(104, 250, 0, 20);

// Linha horizontal
$pdf->Rect(0, 250, 220, 0);
$pdf->Rect(0, 270, 220, 0);


// Valores
$pdf->SetXY(197, 253);
$pdf->cell(13,4, db_formatar($subtotal, 'f'),0,0,"R");

$pdf->SetXY(197, 259);
$pdf->cell(13,4, db_formatar($valorDesconto, 'f'),0,0,"R");

$pdf->SetXY(197, 265);
$pdf->cell(13,4,db_formatar($totalRecolher, 'f'),0,0,"R");




// Descrições
$pdf->SetXY(104, 253);
$pdf->cell(35,4,'Sub Total................:',0,0,"L");

$pdf->SetXY(104, 259);
$pdf->cell(35,4,'Descontos................:',0,0,"L");

$pdf->SetXY(104, 265);
$pdf->cell(35,4,'Total a Recolher................:',0,0,"L");



// Comentário da Observação

$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(23, 254);
$pdf->cell(55,8,'Os tributos dessa simulação foram calculados com base',0,0,"C");
$pdf->SetXY(23, 258);
$pdf->cell(55,8,'nos valores e tipo de transação imobiliária declarada.',0,0,"C");

// // , para fins de simulação.





// Finaliza PDF
$pdf->Output();

