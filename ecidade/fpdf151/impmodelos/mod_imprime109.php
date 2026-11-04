<?php

use App\Domain\Tributario\Arrecadacao\Repositories\RecibobarpixRepository;

if (!defined('HEIGHT_CABECALHO')) {
    define("HEIGHT_CABECALHO", 9);
}

if (!defined('WIDTH_TOTAL_SCREEN')) {
    define("WIDTH_TOTAL_SCREEN", 208);
}

if (!defined('HEIGHT_SUBCABECALHO')) {
    define("HEIGHT_SUBCABECALHO", 5);
}

if (!defined('FONTE_TEXTO')) {
    define("FONTE_TEXTO", 7);
}

if (!defined('FONTE_TITULO_TEXTO')) {
    define("FONTE_TITULO_TEXTO", 4);
}

if (!defined('FONTE_AVISO')) {
    define("FONTE_AVISO", 5);
}

if (!defined('COLUNA1')) {
    define("COLUNA1", WIDTH_TOTAL_SCREEN / 12);
}

if (!defined('COLUNA6')) {
    define("COLUNA6", COLUNA1 * 6);
}

if (!defined('COLUNA2')) {
    define("COLUNA2", COLUNA1 * 2);
}

if (!defined('COLUNA3')) {
    define("COLUNA3", COLUNA1 * 3);
}

if (!defined('COLUNA4')) {
    define("COLUNA4", COLUNA1 * 4);
}

if (!defined('COLUNA5')) {
    define("COLUNA5", COLUNA1 * 5);
}

if (!defined('HEIGHT_LINHA_TIPO1')) {
    define("HEIGHT_LINHA_TIPO1", 6);
}

if (!defined('HEIGHT_LINHA_ENDERECO')) {
    define("HEIGHT_LINHA_ENDERECO", 16);
}

if (!defined('HEIGHT_LINHA_AVISO')) {
    define("HEIGHT_LINHA_AVISO", 26);
}

if (!defined('ESPACO_TEXTO')) {
    define("ESPACO_TEXTO", 5);
}

if (isset($this->jaimpresso) && $this->jaimpresso == 'f' && isset($this->capacarne) && $this->capacarne == 'true') {
    if (($this->qtdcarne % 4) == 0) {
        $this->objpdf->AddPage();
    }
} else {
        if (($this->qtdcarne % 8) == 0) {
        $this->objpdf->AddPage();
    }
}

$this->objpdf->SetLineWidth(0.05);
if ($this->atualizaquant) {
    $this->qtdcarne += 1;
}
$iAjusteColunaX = 0;

$top = $this->objpdf->GetY() - 3;
$topbkp = $this->objpdf->GetY() - 3;
if (isset($this->jaimpresso) && $this->jaimpresso == 'f' && isset($this->capacarne) && $this->capacarne == 'true') {
    $this->objpdf->SetTextColor(0, 0, 0);
    $this->objpdf->SetFillColor(250, 250, 250);
    $this->objpdf->SetLineWidth(0.1);

  /**
   * CABEÇALHO
   */
    $this->objpdf->Rect(1, 1, WIDTH_TOTAL_SCREEN, HEIGHT_CABECALHO);
    $this->objpdf->Image('imagens/files/' . $this->logo, 2, 2, 6);
    $this->objpdf->SetFont('Arial', '', 12);
    $this->objpdf->Text(80, 8, 'FICHA DE LANÇAMENTO - ' . $this->anoiptu);

    $y = HEIGHT_CABECALHO + 1;
  /**
   * IDENTIFICACAO DO IMOVEL TITULO
   *
   * -------------------------------------------------------------------------------------------
   */
    $this->objpdf->Rect(1, $y, COLUNA6, HEIGHT_SUBCABECALHO);
    $this->objpdf->SetFont('Arial', 'B', FONTE_TEXTO);
    $this->objpdf->Text(35, $y + 4, 'IDENTIFICAÇÃO DO IMÓVEL');

    $x = 1;
    $y = HEIGHT_CABECALHO + HEIGHT_SUBCABECALHO + 1;
    $width = COLUNA2;
    $height = HEIGHT_SUBCABECALHO + 2;

    $this->objpdf->Rect($x, $y, $width, $height);
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Inscrição');
    $this->objpdf->SetFont('Arial', 'B', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, substr($this->matricula_carne, 0, -1) . "-" . substr($this->matricula_carne, -1), 0, 0, "R");

    $x += $width;
    $width = COLUNA1;
    $this->objpdf->Rect($x, $y, $width, $height);
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Distrito');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, $this->d_carne, 0, 0, "R");

    $x += $width;
    $width =  COLUNA1;
    $this->objpdf->Rect($x, $y, $width, $height);
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Zona');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, $this->zn_carne, 0, 0, "R");

    $x += $width;
    $width = COLUNA1;
    $this->objpdf->Rect($x, $y, $width, $height);
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Quadra');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, substr($this->quadra_carne, 1), 0, 0, "R");

    $x += $width;
    $width = COLUNA1;
    $this->objpdf->Rect($x, $y, $width, $height);
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Lote');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, $this->lote_carne, 0, 0, "R");


    $y += $height;
    $x = 1;
    $width =  COLUNA6;
    $this->objpdf->Rect($x, $y, $width, $height);
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Nome');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $yText = $y + ESPACO_TEXTO;
    $this->objpdf->Text($x + 1, $yText, $this->nome_carne);

  /**
   * ENDEREÇO
   */
    $y += $height;
    $height =  HEIGHT_LINHA_ENDERECO;
    $x = 1;
    $width =  COLUNA6;
    $this->objpdf->Rect($x, $y, $width, $height);
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Endereço');
    $yText = $y + ESPACO_TEXTO - 2;

    $this->objpdf->setXY($x + 1, $yText);
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $endereco = $this->tiporua_carne . " " . $this->nomerua_carne . ", " . str_replace("-", "/", $this->num_end_carne);
    $condominio =  $this->nome_cond_carne . $this->nome_predio_carne;
    if (trim($condominio) != "") {
        $endereco .= "\n" . $condominio;
    }

    if (!empty($this->complemento_carne)) {
        $quadra_end = (strlen($this->quadra_end_carne) < 3) ? str_pad($this->quadra_end_carne, 3, "0", STR_PAD_LEFT) : $this->quadra_end_carne;
        $lote_end = (strlen($this->lote_end_carne) < 3) ? str_pad($this->lote_end_carne, 3, "0", STR_PAD_LEFT) : $this->lote_end_carne;

        $complemento .= (!empty($this->quadra_end_carne) && strlen(trim($this->quadra_end_carne)) > 0) && ($quadra_end != "000")  ? ", QD: " . $quadra_end  :  "";
        $complemento .= (!empty($this->lote_end_carne) && strlen(trim($this->lote_end_carne)) > 0) && ($lote_end != "000") ?  " LT: " . $lote_end  :  "";
    } else {
        if ($this->num_end_carne == "S/N") {
            $quadra_end = (strlen($this->quadra_end_carne) < 3) ? str_pad($this->quadra_end_carne, 3, "0", STR_PAD_LEFT) : $this->quadra_end_carne;
            $lote_end = (strlen($this->lote_end_carne) < 3) ? str_pad($this->lote_end_carne, 3, "0", STR_PAD_LEFT) : $this->lote_end_carne;

            $complemento = (!empty($this->quadra_end_carne) && strlen(trim($this->quadra_end_carne)) > 0) && ($quadra_end != "000")  ? ", QD: " . $quadra_end  :  "";
            $complemento .= (!empty($this->lote_end_carne) && strlen(trim($this->lote_end_carne)) > 0) && ($lote_end != "000") ?  " LT: " . $lote_end  :  "";
        }
    }

    if (!empty($complemento)) {
        $endereco .= $complemento;
    }
    $endereco .= "\nCEP: " . $this->cep_carne . " - " . $this->bairro_carne;

    $this->objpdf->MultiCell(100, 3, $endereco);



  /**
   * AVISO
   */
    $y += $height;
    $height = HEIGHT_LINHA_AVISO;
    $width =  COLUNA6;
    $yText = $y + 3;
    $this->objpdf->Rect($x, $y, $width, $height);
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Aviso');
    $this->objpdf->SetFont('Arial', '', FONTE_AVISO);
    $this->objpdf->setXY($x + 1, $yText);
    $this->objpdf->MultiCell(100, 2, $this->mensagemdebitosanosanteriores);
    $this->objpdf->setY($this->objpdf->getY() + 1);
    $this->objpdf->setX($x + 1);

    $percentual = round($this->percentual_bom_pagador, 2);
    if ($percentual > 0 &&  $percentual < 5) {
        $this->objpdf->MultiCell(100, 2, "DESCONTO DO BOM PAGADOR DE {$percentual}% FOI CONCEDIDO PROPORCIONALMENTE AOS PROPRIETÁRIOS APTOS AO BENEFÍCIO.");
    }

    $this->objpdf->SetFont('Arial', '', 5.5);
    $this->objpdf->Text($x + 1, 67.4, 'Base legal do lançamento: Artigos 10, 11, 13 e 16 da Lei 2597/08.', 0, 1, "L", 0);
    $this->objpdf->Text($x + 1, 69.4, 'Departamento de Lançamento e Fiscalização Tributária', 0, 1, "L", 0);



  /**
   * CARACTERÍSTICAS DO IMÓVEL
   *
   * --------------------------------------------------------------------------
   **/


  /**
   * IDENTIFICACAO DO IMOVEL TITULO
   **/
    $y = HEIGHT_CABECALHO + 1;
    $this->objpdf->Rect(COLUNA6 + 1, $y, COLUNA6, HEIGHT_SUBCABECALHO + 2);
    $this->objpdf->SetFont('Arial', 'B', FONTE_TEXTO);
    $this->objpdf->Text(COLUNA6 + 32, $y + 4, 'CARACTERÍSTICAS DO IMÓVEL');


    $x = COLUNA6 + 1;
    $y = HEIGHT_CABECALHO + HEIGHT_SUBCABECALHO + 4;
    $width = COLUNA1 + (COLUNA1 / 2);
    $height = 3;

    $this->objpdf->SetXY($x, $y);
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Cell(COLUNA6, $height, 'CARACTERISTICAS DO TERRENO', 'R');

    $y += $height;
    $height = HEIGHT_LINHA_TIPO1;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 2, $y + 2, 'Testada (m)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->testada_carne, 0, ",", "."), 0, 0, "R");


    $x += $width;
    $width =  COLUNA6 / 4;

    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Área (m²)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->area_carne, 0, ",", "."), 0, 0, "R");

    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Área do lote de vila (m²)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->arealote_carne, 2, ",", "."), 0, 0, "R");

    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Metro linear da testada -V0 (R$)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->lineartestada_carne, 2, ",", "."), 0, 0, "R");

    $y += $height;
    $x = COLUNA6 + 1;
    $height = 3;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Cell(COLUNA6, $height, 'CARACTERÍSTICAS DA EDIFICAÇÃO', 'R');

    $width = COLUNA6 / 3;
    $y += $height;
    $height = HEIGHT_LINHA_TIPO1;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 2, $y + 2, 'Tipo de imóvel');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $yText = $y + ESPACO_TEXTO;
    $this->objpdf->Text($x + 2, $yText, $this->tipoimovel_carne);

    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Característica');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $yText = $y + ESPACO_TEXTO;
    $this->objpdf->Text($x + 1, $yText, $this->caracteristica_carne);


    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Utilização');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $yText = $y + ESPACO_TEXTO;
    $this->objpdf->Text($x + 1, $yText, $this->utilizacao_carne);


    $x = COLUNA6 + 1;
    $width = COLUNA6 / 4;
    $y += $height;
    $height = HEIGHT_LINHA_TIPO1;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 2, $y + 2, 'Área privativa (m²)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->area_privativa, 2, ",", "."), 0, 0, "R");


    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Área comum (m²)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->area_comum, 2, ",", "."), 0, 0, "R");

    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Área garagem (m²)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->area_garagem, 2, ",", "."), 0, 0, "R");

    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Área do jirau - art 13,§ 6º (m²) ');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->area_jirau, 2, ",", "."), 0, 0, "R");

    $x = COLUNA6 + 1;
    $width = COLUNA6 / 3;
    $y += $height;
    $height = HEIGHT_LINHA_TIPO1;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 2, $y + 2, 'Área tributável da unidade (m²)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->area_tributavel_da_unidade, 0, ",", "."), 0, 0, "R");

    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Total construído no lote (m²)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->total_construido_lote, 0, ",", "."), 0, 0, "R");

    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Número de unidades no lote');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $yText = $y + ESPACO_TEXTO;
    $this->objpdf->Text($x + 1, $yText, $this->numunid_carne);

    $x = COLUNA6 + 1;
    $y += $height;
    $this->objpdf->Rect($x, $y, COLUNA6, HEIGHT_SUBCABECALHO);
    $this->objpdf->SetFont('Arial', 'B', FONTE_TEXTO);
    $this->objpdf->Text($x + 35, $y + 4, 'CÁLCULO DOS TRIBUTOS');

    $x = COLUNA6 + 1;
    $width = COLUNA6 / 3;
    $y += $height - 1;
    $height = HEIGHT_LINHA_TIPO1;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 2, $y + 2, 'Valor venal (R$)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->valorvenal_carne, 2, ",", "."), 0, 0, "R");

    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Aliquota (%)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->aliquota_carne, 2, ",", "."), 0, 0, "R");

    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'IPTU (R$)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->iptu_carne, 2, ",", "."), 0, 0, "R");

    $x = COLUNA6 + 1;
    $width = COLUNA6 / 3;
    $y += $height;
    $height = HEIGHT_LINHA_TIPO1;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'TLRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 2, $y + 2, 'Desconto NitNota (R$)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->valornota, 2, ",", "."), 0, 0, "R");

    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Desconto incentivo cultutral (R$)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->desconto_cultural, 2, ",", "."), 0, 0, "R");

    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Desconto bom pagador - até 5% (R$)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    if ($this->percentual_bom_pagador > 0) {
        $percentual = round($this->percentual_bom_pagador, 2);
        $texto_percentual =  " ({$percentual}%)";
    }
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->iptu_bompagador, 2, ',', '.') . $texto_percentual, 0, 0, "R");

    $x = COLUNA6 + 1;
    $width = COLUNA6 / 3;
    $y += $height;
    $height = HEIGHT_LINHA_TIPO1;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 2, $y + 2, 'IPTU devido (R$)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->valor_devido, 2, ',', '.'), 0, 0, "R");

    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'TCIL (R$)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->tcil_carne, 2, ',', '.'), 0, 0, "R");

    $x += $width;
    $this->objpdf->SetXY($x, $y);
    $this->objpdf->Cell($width, $height, '', 'LRB');
    $this->objpdf->SetFont('Arial', '', FONTE_TITULO_TEXTO);
    $this->objpdf->Text($x + 1, $y + 2, 'Total a pagar (R$)');
    $this->objpdf->SetFont('Arial', '', FONTE_TEXTO);
    $this->objpdf->SetXY($x, $y + 2);
    $this->objpdf->Cell($width, ESPACO_TEXTO, number_format($this->valor_total_a_pagar, 2, ',', '.'), 0, 0, "R");

    $this->jaimpresso = 't';
    $this->objpdf->Sety(85);
    $top = $this->objpdf->GetY() - 3;
    $this->qtdcarne += 1;
}

if (empty($endereco)) {
    $endereco = $this->endereco;
}

$this->qtdcarne += 1;

$this->objpdf->SetFont('Arial', 'B', 8);
$this->objpdf->SetTextColor(0, 0, 0);
$this->objpdf->SetFillColor(250, 250, 250);
$this->objpdf->SetX(17 - $iAjusteColunaX);
$this->objpdf->Text(17 - $iAjusteColunaX, $top, $this->prefeitura, 0, 0, "L", 0);
$this->objpdf->SetX(105 - $iAjusteColunaX);
$this->objpdf->Text(105 - $iAjusteColunaX, $top, $this->prefeitura, 0, 1, "L", 0);
$this->objpdf->SetX(170 - $iAjusteColunaX);
$this->objpdf->SetX(17 - $iAjusteColunaX);
$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->Text(17 - $iAjusteColunaX, $top + 2, $this->secretaria, 0, 0, "L", 0);
$this->objpdf->SetX(105 - $iAjusteColunaX);
$this->objpdf->Text(105 - $iAjusteColunaX, $top + 2, $this->secretaria, 0, 1, "L", 0);
$this->objpdf->Ln(2);
$this->objpdf->SetFont('Arial', 'B', 8);
$this->objpdf->SetX(10 - $iAjusteColunaX);
$this->objpdf->Cell(70 - $iAjusteColunaX, 4, $this->tipodebito, 0, 0, "C", 0);
$this->objpdf->SetFont('Arial', 'B', 6);


$this->objpdf->Cell(03 + 9, 4, '1ª Via Contribuinte', 0, 0, "R", 0);
$this->objpdf->SetFont('Arial', 'B', 8);
$this->objpdf->SetX(105 - $iAjusteColunaX);
$this->objpdf->Cell(80 - $iAjusteColunaX, 4, $this->tipodebito, 0, 0, "C", 0);
$this->objpdf->SetFont('Arial', 'B', 6);
$this->objpdf->Cell(05 + 9, 4, '2ª Via Prefeitura', 0, 1, "R", 0);

$y = $this->objpdf->GetY() - 1;
$this->objpdf->Image('imagens/files/' . $this->logo, 8, $y - 11, 8);
$this->objpdf->Image('imagens/files/' . $this->logo, COLUNA6 - 9, $y - 11, 8);
$this->objpdf->SetFont('Times', '', 5);
$this->objpdf->RoundedRect(10 - $iAjusteColunaX, $y + 1, 39, 6, 2, 'DF', '1234'); // matricula/ inscrição
$this->objpdf->RoundedRect(50 - $iAjusteColunaX, $y + 1, 20, 6, 2, 'DF', '1234'); // cod. de arrecadação
$this->objpdf->RoundedRect(71 - $iAjusteColunaX, $y + 1, 12, 6, 2, 'DF', '1234'); // parcela

$this->objpdf->SetFont('Arial', 'B', 6);
$this->objpdf->Text(165 - $iAjusteColunaX, $y - 3, "Data para pagamento : " . $this->dtparapag);
$this->objpdf->Text(58 - $iAjusteColunaX, $y - 3, "Data para pagamento : " . $this->dtparapag);
$this->objpdf->SetFont('Times', '', 5);

$this->objpdf->RoundedRect(10 - $iAjusteColunaX, $y + 8, 73, 12, 2, 'DF', '1234'); // nome / endereço

$this->objpdf->RoundedRect(10 - $iAjusteColunaX, $y + 21, 73, 14, 2, 'DF', '1234'); // instruçoes

$this->objpdf->RoundedRect(10 - $iAjusteColunaX, $y + 36, 39, 7, 2, 'DF', '1234'); // vencimento
$this->objpdf->RoundedRect(50 - $iAjusteColunaX, $y + 36, 33, 7, 2, 'DF', '1234'); // valor

$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->Text(13 - $iAjusteColunaX, $y + 3, $this->titulo1); // matricula/ inscrição
$this->objpdf->SetFont('Arial', 'B', 7);
$this->objpdf->Text(13 - $iAjusteColunaX, $y + 6, $this->descr1); // numero da matricula ou inscricao

$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->Text(52 - $iAjusteColunaX, $y + 3, $this->titulo2); // cod. de arrecadação
$this->objpdf->SetFont('Arial', 'B', 7);
$this->objpdf->Text(52 - $iAjusteColunaX, $y + 6, $this->descr2); // numpre

$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->Text(74 - $iAjusteColunaX, $y + 3, $this->titulo5); // Parcela
$this->objpdf->SetFont('Arial', 'B', 7);
$this->objpdf->Text(75 - $iAjusteColunaX, $y + 6, $this->descr5); // Parcela inicial e total de parcelas

$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->Text(13 - $iAjusteColunaX, $y + 10, $this->titulo3); // contribuinte/endereço
$this->objpdf->SetFont('Arial', 'B', 5);
$this->objpdf->setXY(13 - $iAjusteColunaX, $y + 11);
$this->objpdf->MultiCell(70, 2, $this->descr3_1);
$this->objpdf->setX(13 - $iAjusteColunaX);
$this->objpdf->MultiCell(70, 2,  (isset($endereco) && !empty($endereco)) ? $endereco : $this->descr3_2);
$this->objpdf->setXY(13 - $iAjusteColunaX, $y);

$this->objpdf->Text(13 - $iAjusteColunaX, $y + 23, $this->titulo4); // Instruções

$this->objpdf->SetFont('Arial', 'B', 7);
$xx = $this->objpdf->getx();
$yy = $this->objpdf->gety();

$this->objpdf->setleftmargin(10 - $iAjusteColunaX);
$this->objpdf->setrightmargin(120 - $iAjusteColunaX);
$this->objpdf->sety($y + 23);
$this->objpdf->SetFont('Arial', 'B', 5);
$this->objpdf->multicell(68 - $iAjusteColunaX, 2.5, $this->descr4_1); // Instruções 1 - linha 1
$this->objpdf->multicell(68 - $iAjusteColunaX, 2.5, $this->descr4_2); // Instruções 1 - linha 2
$this->objpdf->multicell(68 - $iAjusteColunaX, 2.5, $this->descr12_2); // Instruções 1 - linha 2
if ($this->valorTotalCustas > 0) {
    $this->objpdf->multicell(
        68 - $iAjusteColunaX,
        -3,
        str_pad($this->descrCustas, 49, ' ', STR_PAD_RIGHT) . 'R$ ' . db_formatar($this->valorTotalCustas, 'f')
    ); // Custas advogaticias
}
$this->objpdf->setxy($xx, $yy - 2);

$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->Text(13 - $iAjusteColunaX, $y + 38, $this->titulo6); // Vencimento
$this->objpdf->SetFont('Arial', 'B', 7);
$this->objpdf->Text(20 - $iAjusteColunaX, $y + 41, $this->descr6); // Data de Vencimento

$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->Text(53 - $iAjusteColunaX, $y + 38, $this->titulo7); // valor
$this->objpdf->SetFont('Arial', 'B', 7);
$this->objpdf->Text(56 - $iAjusteColunaX, $y + 41, $this->descr7); // qtd de URM ou valor


$this->objpdf->RoundedRect(95 - $iAjusteColunaX, $y + 1, 40, 6, 2, 'DF', '1234'); // matricula / inscricao
$this->objpdf->RoundedRect(136 - $iAjusteColunaX, $y + 1, 20, 6, 2, 'DF', '1234'); // cod. arrecadacao
$this->objpdf->RoundedRect(157 - $iAjusteColunaX, $y + 1, 20, 6, 2, 'DF', '1234'); // parcela
$this->objpdf->RoundedRect(178 - $iAjusteColunaX, $y + 1, 23, 6, 2, 'DF', '1234'); // vencimento


// QR Code
$pixRepository = new RecibobarpixRepository();
$pix = $pixRepository->getByCodBar($this->codigo_barras);

if ($pix) {
    $imagemPng = "tmp/pix_arrecadacao".time().".png";
    $url = $pix->k00_qrcode;
    \PHPQRCode\QRcode::png($url, $imagemPng, 'L', 12, 17);
    $this->objpdf->Image($imagemPng, 173 - $iAjusteColunaX, $y  + 8.5, 33);
}

$this->objpdf->RoundedRect(178 - $iAjusteColunaX, 12 + $y  + 3, 23, 20, 2, 'D', '1234');
// FIM QRC

$this->objpdf->RoundedRect(178 - $iAjusteColunaX, $y + 8, 23, 6, 2, 'DF', '1234'); // valor
$this->objpdf->RoundedRect(95 - $iAjusteColunaX, $y + 8, 82, 13, 2, 'DF', '1234'); // nome / endereco
$this->objpdf->RoundedRect(95 - $iAjusteColunaX, $y + 22, 82, 13, 2, 'DF', '1234'); // instrucoes



if ($this->linha_digitavel != null) {
    $this->objpdf->SetFont('Arial', '', 7);
    $this->objpdf->Text(10 - $iAjusteColunaX, $y + 54, $this->linha_digitavel);
    $this->objpdf->SetFont('Arial', 'B', 7);
}

$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->Text(97 - $iAjusteColunaX, $y + 3, $this->titulo1); // matricula / inscricao
$this->objpdf->SetFont('Arial', 'B', 7);
$this->objpdf->Text(97 - $iAjusteColunaX, $y + 6, $this->descr8); // numero da matricula ou inscricao

$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->Text(138 - $iAjusteColunaX, $y + 3, $this->titulo9); // cod. de arrecadação
$this->objpdf->SetFont('Arial', 'B', 7);
$this->objpdf->Text(138 - $iAjusteColunaX, $y + 6, $this->descr9); // numpre

$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->Text(161 - $iAjusteColunaX, $y + 3, $this->titulo10); // parcela
$this->objpdf->SetFont('Arial', 'B', 7);
$this->objpdf->Text(162 - $iAjusteColunaX, $y + 6, $this->descr10); // parcela e total das parcelas

$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->SetFont('Arial', 'B', 5);
$this->objpdf->setXY(97 - $iAjusteColunaX, $y + 10);
$this->objpdf->MultiCell(70, 2, $this->descr3_1);
$this->objpdf->setX(97 - $iAjusteColunaX);
$this->objpdf->MultiCell(70, 2,  (isset($endereco) && !empty($endereco)) ? $endereco : $this->descr3_2);
$this->objpdf->setXY(13 - $iAjusteColunaX, $y);

$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->Text(97 - $iAjusteColunaX, $y + 24, $this->titulo12); // instruções

$this->objpdf->SetFont('Arial', 'B', 7);

$xx = $this->objpdf->getx();
$yy = $this->objpdf->gety();
$this->objpdf->setleftmargin(97 - $iAjusteColunaX);
$this->objpdf->setrightmargin(2);
$this->objpdf->sety($y + 25);

// mensagem de instruções da guia prefeitura
$this->objpdf->SetFont('Arial', 'B', 5);
$this->objpdf->multicell(80 - $iAjusteColunaX, 2, substr($this->descr12_1, 0, 274)); // Instruções 2 - linha 1
$this->objpdf->multicell(80 - $iAjusteColunaX, 2, $this->descr12_2); // Instruções 2 - linha 2
$this->objpdf->setxy($xx, $yy);

$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->Text(180 - $iAjusteColunaX, $y + 3, $this->titulo14); // vencimento
$this->objpdf->SetFont('Arial', 'B', 7);
$this->objpdf->Text(180 - $iAjusteColunaX, $y + 6, $this->descr14); // data de vencimento

$this->objpdf->SetFont('Arial', '', 5);
$this->objpdf->Text(180 - $iAjusteColunaX, $y + 10, $this->titulo15); // valor
$this->objpdf->SetFont('Arial', 'B', 7);
$this->objpdf->Text(180 - $iAjusteColunaX, $y + 13, $this->descr15); // total de URM ou valor

$this->objpdf->SetLineWidth(0.05);
$this->objpdf->SetDash(1, 1);
$this->objpdf->Line(93 - $iAjusteColunaX, $y - 18, 93 - $iAjusteColunaX, $y + 55); // linha tracejada vertical
$this->objpdf->SetDash();
$this->objpdf->Ln(70);
$this->objpdf->SetFillColor(0, 0, 0);
$this->objpdf->SetFont('Arial', '', 10);

$this->objpdf->SetFont('Arial', '', 4);
$this->objpdf->TextWithDirection(2, $y + 53, $this->texto, 'U'); // texto no canhoto do carne
$this->objpdf->TextWithDirection(85 - $iAjusteColunaX, $y + 35, 'A U T E N T I C A  Ç Ã O   M E C Â N I C A', 'U'); // texto no canhoto do carne
$this->objpdf->TextWithDirection(203 - $iAjusteColunaX, $y + 35, 'A U T E N T I C A Ç Ã O   M E C Â N I C A', 'U'); // texto no canhoto do carne
$this->objpdf->SetFont('Arial', '', 7);

// mensagem do canto inferior esquerdo da guia do contribuinte
$this->objpdf->Text(10 - $iAjusteColunaX, $y + 46, $this->descr16_1); //
$this->objpdf->Text(10 - $iAjusteColunaX, $y + 48, $this->descr16_2); //
$this->objpdf->Text(10 - $iAjusteColunaX, $y + 50, $this->descr16_3); //
if ($this->linha_digitavel != null) {
    $this->objpdf->Text(105 - $iAjusteColunaX, $y + 38, $this->linha_digitavel);
}
if ($this->codigo_barras != null) {
    $this->objpdf->int25(95 - $iAjusteColunaX, $y + 39, $this->codigo_barras, 15, 0.33);
}

$this->objpdf->SetLineWidth(0.05);
$this->objpdf->SetDash(1, 1);
$this->objpdf->Line(0, $this->objpdf->gety() - 13, $this->objpdf->w - $iAjusteColunaX, $this->objpdf->gety() - 13); // linha tracejada vertical
$this->objpdf->SetDash();
