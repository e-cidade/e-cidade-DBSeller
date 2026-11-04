<?php

/**
 * Certidão de Isenção IPTU
 */
$this->objpdf->SetTextColor(0,0,0);
$this->objpdf->setfont('Arial','B',18);

$url_autenticidade = env('URL_AUTENTICIDADE_DOCUMENTO')."/consulta/isencao/{$this->codisen}";

if (!empty($this->codisen) && !empty($url_autenticidade)) {
  $fileQRCode = "tmp/qrcode_{$this->codisen}.png";
  \PHPQRCode\QRcode::png("{$url_autenticidade}", $fileQRCode, 'L', 5, 1);
  $this->objpdf->Image($fileQRCode, 160, 50, 20);
  $this->objpdf->Setfont('Arial', 'B', 18);
}

$linha  =  $this->objpdf->gety()+20;
$coluna = $this->objpdf->getx();
$borda  = "0";

$linha += 0;
$this->objpdf->sety($linha);
$this->objpdf->setx($coluna);

$this->objpdf->setfont('Arial','B',26);
$this->objpdf->cell(30,7,"",$borda,1,"L",0);
$this->objpdf->Multicell(180,7,$this->isenmsg1,$borda,"C",0);
$this->objpdf->cell(180,7,"",$borda,1,"C",0);

$this->objpdf->setfont('Arial','B',10);
$this->objpdf->cell(30,7,"",$borda,0,"L",0);
$this->objpdf->cell(200,7,"IDENTIFICAÇÃO DO CONTRIBUINTE",$borda,1,"L",0);
$this->objpdf->cell(30,7,"",$borda,0,"L",0);
$this->objpdf->cell(33,7,"Nome: ",$borda,0,"L",0);
$this->objpdf->setfont('Arial','',10);
$this->objpdf->cell(60,7,$this->isennome,$borda,1,"L",0);

$this->objpdf->cell(30,7,"",$borda,0,"L",0);
$this->objpdf->setfont('Arial','B',10);
$this->objpdf->cell(33,7,"CPF/CNPJ: ",$borda,0,"L",0);
$this->objpdf->setfont('Arial','',10);
$this->objpdf->cell(60,7,$this->isencgc,$borda,1,"L",0);

$this->objpdf->cell(30,10,"",$borda,1,"L",0);
$this->objpdf->cell(30,7,"",$borda,0,"L",0);
$this->objpdf->setfont('Arial','B',10);
$this->objpdf->cell(200,7,"IDENTIFICAÇÃO DO IMÓVEL",$borda,1,"L",0);
$this->objpdf->cell(30,7,"",$borda,0,"L",0);
$this->objpdf->cell(33,7,"Matrícula: ",$borda,0,"L",0);
$this->objpdf->setfont('Arial','',10);
$this->objpdf->cell(50,7,$this->isenmatric,$borda,1,"L",0);

$this->objpdf->cell(30,7,"",$borda,0,"L",0);
$this->objpdf->setfont('Arial','B',10);
$this->objpdf->cell(33,7,"Endereço: ",$borda,0,"L",0);
$this->objpdf->setfont('Arial','',10);
$this->objpdf->cell(140,7,$this->isenender,$borda,1,"L",0);

$this->objpdf->cell(30,7,"",$borda,0,"L",0);
$this->objpdf->setfont('Arial','B',10);
$this->objpdf->cell(33,7,"Bairro: ",$borda,0,"L",0);
$this->objpdf->setfont('Arial','',10);
$this->objpdf->cell(140,7,$this->isenbairro,$borda,1,"L",0);

$this->objpdf->cell(30,7,"",$borda,0,"L",0);
$this->objpdf->setfont('Arial','B',10);
$this->objpdf->cell(33,7,"Setor/Quadra/Lote:",$borda,0,"L",0);
$this->objpdf->setfont('Arial','',10);
$this->objpdf->cell(50,7,"{$this->isensetor}/{$this->isenquadra}/{$this->isenlote}",$borda, 1,"L",0);

$this->objpdf->cell(30,7,"",$borda,0,"L",0);
$this->objpdf->setfont('Arial','B',10);
$this->objpdf->cell(33,7,"Ano Inicial:",$borda,0,"L",0);
$this->objpdf->setfont('Arial','',10);
$this->objpdf->cell(20,7,$this->isenanoini,$borda,1,"L",0);

$this->objpdf->cell(30,7,"",$borda,0,"L",0);
$this->objpdf->setfont('Arial','B',10);
$this->objpdf->cell(33,7,"Ano Final:",$borda,0,"L",0);
$this->objpdf->setfont('Arial','',10);
$this->objpdf->cell(20,7,$this->isenanofim,$borda,1,"L",0);

$linha  += 40;

$this->objpdf->sety($linha+50);
$this->objpdf->setx($coluna);

$this->objpdf->cell(15,7,"",$borda,0,"L",0);
$this->objpdf->Multicell(180,7,$this->isenmsg2,$borda,"J",0);
$this->objpdf->cell(180,7,"",$borda,1,"C",0);
$this->objpdf->setfont('Arial','',10);

$this->objpdf->cell(15,7,"",$borda,0,"L",0);
$this->objpdf->Multicell(150,7, $this->isenmsg4,$borda,"J",0);
$this->objpdf->Ln(2);
$linha = $this->objpdf->gety();
if(isset($this->isenmsg3) && $this->isenmsg3 != ""){

 $this->objpdf->sety($linha+5);
 $this->objpdf->cell(15,7,"",$borda,0,"L",0);
 $this->objpdf->Multicell(150,7,$this->isenmsg3,$borda,"J",0);
 $linha = $this->objpdf->gety();
 $this->objpdf->sety($linha);
}

$alturatotal =  $this->objpdf->getH();

$this->objpdf->sety($alturatotal-60);

$this->objpdf->cell(115,7,"",$borda,0,"L",0);
$this->objpdf->cell(40,7, "{$this->munic}, {$this->data_extenso}.",$borda,1,"L",0);

try{ 
  if (isset($this->isenassinatura2) && $this->isenassinatura2 != ""){
    eval($this->isenassinatura2);
  } else {
    $this->objpdf->cell(90,7,"",0,1,"C",0);
  }
} catch(Exception $error){
  $this->objpdf->cell(90,7,"",0,1,"C",0);
}

$this->objpdf->setxy(14,@$y+$linha-@$y+5);