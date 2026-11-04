<?php
$this->objpdf->AliasNbPages();
$this->objpdf->settopmargin(1);
$totalRubricasFiltradas = 0;
for ($ii = 0; $ii < $this->linhasenvelope; $ii++) {
    $validaTipo = (
        (pg_fetch_result($this->recordenvelope, $ii, $this->tipo) == 'P')
        ||
        (pg_fetch_result($this->recordenvelope, $ii, $this->tipo) == 'D')
    );
    if (!$validaTipo) {
        continue;
    }
    $totalRubricasFiltradas++;
}

$xcol  = 7;
$cinza = 225;

$excessoRubricas = ($totalRubricasFiltradas > 20);

if ($this->seq == 0 || ($this->seq == 1 && $excessoRubricas)) {
    $xlin  = 24;
    $this->objpdf->AddPage();
} else {
    if ($this->lastExcessoRubricas) {
        $this->objpdf->AddPage();
        $xlin = 24;
    } else {
        $this->objpdf->line(2, 149.5, 208, 149.5);
        $xlin = 173.5;
    }
}

$this->lastExcessoRubricas = $excessoRubricas;

$espacoExtra1 = $excessoRubricas ? 35.01 : 0;
$espacoExtra2 = $excessoRubricas ? ($espacoExtra1*2) : 0;

$this->objpdf->setfillcolor(225);
//Quadro geral
$this->objpdf->roundedrect($xcol-2, $xlin-18, 199, 134.5+$espacoExtra1, 2, 'DF', '1234');
$this->objpdf->setfillcolor(255, 255, 255);
$this->objpdf->Setfont('Arial', 'B', 11);
$this->objpdf->text($xcol+126, $xlin-13, 'RECIBO DE PAGAMENTO');
$this->objpdf->text($xcol+126, $xlin-8, 'REF. AO MÊS '.db_formatar($this->mes, 's', '0', 2, 'e', 0).'/'.$this->ano);
$this->objpdf->text($xcol+126, $xlin-3, $this->qualarquivo);

$this->objpdf->Image('imagens/files/'.$this->logo, 15, $xlin-17, 12);
$this->objpdf->Setfont('Arial', 'B', 9);
$this->objpdf->text($xcol+26, $xlin-15, $this->prefeitura);
$this->objpdf->Setfont('Arial', '', 7);
$this->objpdf->text($xcol+26, $xlin-12, $this->enderpref);
$this->objpdf->text($xcol+26, $xlin- 9, $this->municpref);
$this->objpdf->text($xcol+26, $xlin- 6, $this->telefpref);
$this->objpdf->text($xcol+26, $xlin- 3, db_formatar($this->cgcpref, 'cnpj'));

//retangulo da assinatura
$this->objpdf->Roundedrect($xcol+171, $xlin+14, $xcol+17, 100, 2, 'DF', '1234');

//retangulo onde fica no nome do funcionario
$this->objpdf->Roundedrect($xcol, $xlin, $xcol+188, 12, 2, 'DF', '1234');

//retangulo das linhasenvelope
$this->objpdf->Roundedrect($xcol, $xlin+14, $xcol+163, 72, 2, 'DF', '1234');
$this->objpdf->Roundedrect($xcol, $xlin+14, $xcol+163, 72+$espacoExtra1, 2, 'DF', '1234');

//retangulo da mensagem
$this->objpdf->Roundedrect($xcol, $xlin+87+$espacoExtra1, $xcol+163, 27, 2, 'DF', '1234');

//Linha do cabeçalho das linhasevelope
$this->objpdf->line($xcol, $xlin+22, $xcol+170, $xlin+22);

//Linha de baixo do Liquido a Receber e que separa os impostos
$this->objpdf->line($xcol, $xlin+105+$espacoExtra1, $xcol+170, $xlin+105+$espacoExtra1);
//Linha de cima do Liquido a Receber
$this->objpdf->line($xcol+123, $xlin+96+$espacoExtra1, $xcol+170, $xlin+96+$espacoExtra1);
//Linha do meio do Total dos Vencimentos e Total dos Descontos
$this->objpdf->line($xcol+146, $xlin+87+$espacoExtra1, $xcol+146, $xlin+105+$espacoExtra1);
//Linha da esquerda do Total dos Vencimentos e Liquido a Receber
$this->objpdf->line($xcol+123, $xlin+87+$espacoExtra1, $xcol+123, $xlin+105+$espacoExtra1);


//Linha entre Proventos e Descontos
$this->objpdf->line($xcol+146, $xlin+14, $xcol+146, $xlin+86+$espacoExtra1);
//Linha entre Referência e Proventos
$this->objpdf->line($xcol+123, $xlin+14, $xcol+123, $xlin+86+$espacoExtra1);
//Linha entre Descrição e Referência
$this->objpdf->line($xcol+108, $xlin+14, $xcol+108, $xlin+86+$espacoExtra1);
//Linha entre Cód. e Descrição
$this->objpdf->line($xcol+15, $xlin+14, $xcol+15, $xlin+86+$espacoExtra1);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+2, $xlin+3, 'Matrícula:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+12, $xlin+3, $this->registro);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+23, $xlin+3, 'Nome:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+32, $xlin+3, $this->nome);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+100, $xlin+3, 'Função:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+112, $xlin+3, $this->descr_funcao);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+170, $xlin+3, 'Admissão:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+180, $xlin+3, $this->admissao);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+2, $xlin+7, 'Lotação:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+12, $xlin+7, $this->descr_lota);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+100, $xlin+7, 'Padrão:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+112, $xlin+7, $this->padrao);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+100, $xlin+11, 'Bco/Ag/Cta:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+112, $xlin+11, $this->banco.' / '.$this->agencia.' / '.$this->conta);

$this->objpdf->Setfont('Arial', '', 8);
$this->objpdf->text($xcol+ 5, $xlin+18, 'Cód.');
$this->objpdf->text($xcol+ 56, $xlin+18, 'Descrição');
$this->objpdf->text($xcol+109, $xlin+18, 'Referência');
$this->objpdf->text($xcol+129, $xlin+18, 'Proventos');
$this->objpdf->text($xcol+153, $xlin+18, 'Descontos');
$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+149, $xlin+89+$espacoExtra1, 'Total dos Descontos');
$this->objpdf->text($xcol+125, $xlin+89+$espacoExtra1, 'Total dos Vencimentos');
$this->objpdf->text($xcol+127, $xlin+101+$espacoExtra1, 'Líquido a Receber');
//Caixa do Valor Total
$this->objpdf->setfillcolor(255, 255, 255);

$this->objpdf->text($xcol+4, $xlin+107+$espacoExtra1, 'Margem Consignavel');
$this->objpdf->text($xcol+37, $xlin+107+$espacoExtra1, 'Sal. Base');
$this->objpdf->text($xcol+67, $xlin+107+$espacoExtra1, 'Base Previdência');
$this->objpdf->text($xcol+99, $xlin+107+$espacoExtra1, 'Base FGTS');
$this->objpdf->text($xcol+127, $xlin+107+$espacoExtra1, 'FGTS do Mês');
$this->objpdf->text($xcol+155, $xlin+107+$espacoExtra1, 'Base IRRF');

$this->objpdf->sety($xlin+24);
$maiscol           = 0;
$yy                = $this->objpdf->gety();
$provento          = 0;
$margem_deduz      = 0;
$margem_consignada = 0;
$desconto          = 0;
$baseprev          = 0;
$basefgts          = 0;
$baseirrf          = 0;
$valor_margem      = 0;
$this->objpdf->Setfont('Arial', '', 7);
for ($ii = 0; $ii < $this->linhasenvelope; $ii++) {
    $this->objpdf->setx($xcol+6.5);
    if (pg_fetch_result($this->recordenvelope, $ii, $this->tipo) == 'P') {
        $this->objpdf->cell(5, 3, trim(pg_fetch_result($this->recordenvelope, $ii, $this->rubrica)), 0, 0, "R", 0);
        $this->objpdf->cell(5, 3, "", 0, 0, "L", 0);
        $this->objpdf->cell(86, 3, pg_fetch_result($this->recordenvelope, $ii, $this->descr_rub), 0, 0, "L", 0);
        $this->objpdf->cell(20, 3, db_formatar(pg_fetch_result($this->recordenvelope, $ii, $this->quantidade), 'f'), 0, 0, "R", 0);
        $this->objpdf->cell(22, 3, db_formatar(pg_fetch_result($this->recordenvelope, $ii, $this->valor), 'f'), 0, 0, "R", 0);
        $this->objpdf->cell(22, 3, '', 0, 1, "R", 0);
        $provento += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
        $rubrica = trim(pg_fetch_result($this->recordenvelope, $ii, $this->rubrica));
        if (db_getsession("DB_instit") == 1 && strtoupper($this->municpref) == 'ARAPIRACA' &&
            ($rubrica == '0005' || $rubrica == '0006' || $rubrica == '0007' || $rubrica == '0008' ||
             $rubrica == '0011' || $rubrica == '0014' || $rubrica == '0017' || $rubrica == '0018' ||
             $rubrica == '0020' || $rubrica == '0021' || $rubrica == '0023' || $rubrica == '0055' ||
             $rubrica == '0060' || $rubrica == '0061' || $rubrica == '0062' || $rubrica == '0063' ||
             $rubrica == '0064' || $rubrica == '0065' || $rubrica == '0098' || $rubrica == '0099' ||
             $rubrica == '0101' || $rubrica == '0104' || $rubrica == '0105' || $rubrica == '0107' ||
             $rubrica == '0108' || $rubrica == '0112' || $rubrica == '0116' || $rubrica == '0117' ||
             $rubrica == '0118' || $rubrica == '0121' || $rubrica == '0122' || $rubrica == '0126' ||
             $rubrica == '0129' || $rubrica == '0131' || $rubrica == '0132' || $rubrica == '0133' ||
             $rubrica == '0134' || $rubrica == '0135' || $rubrica == '0136' || $rubrica == '0137' ||
             $rubrica == '0138' || $rubrica == '0150' || $rubrica == '0151' || $rubrica == '0160' ||
             $rubrica == '0170' || $rubrica == '0190'
            )) {
            $margem_consignada += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
        }
    } elseif (pg_fetch_result($this->recordenvelope, $ii, $this->tipo) == 'D') {
        $this->objpdf->cell(5, 3, trim(pg_fetch_result($this->recordenvelope, $ii, $this->rubrica)), 0, 0, "R", 0);
        $this->objpdf->cell(5, 3, "", 0, 0, "L", 0);
        $this->objpdf->cell(86, 3, pg_fetch_result($this->recordenvelope, $ii, $this->descr_rub), 0, 0, "L", 0);
        $this->objpdf->cell(20, 3, db_formatar(pg_fetch_result($this->recordenvelope, $ii, $this->quantidade), 'f'), 0, 0, "R", 0);
        $this->objpdf->cell(22, 3, '', 0, 0, "R", 0);
        $this->objpdf->cell(22, 3, db_formatar(pg_fetch_result($this->recordenvelope, $ii, $this->valor), 'f'), 0, 1, "R", 0);
        $desconto += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
        $rubrica = trim(pg_fetch_result($this->recordenvelope, $ii, $this->rubrica));
        if (db_getsession("DB_instit") == 1 && strtoupper($this->municpref) == 'ARAPIRACA') {
            if ($rubrica == 'R901' || $rubrica == 'R904' || $rubrica == 'R913' || $rubrica == '0333') {
                $margem_consignada -= pg_fetch_result($this->recordenvelope, $ii, $this->valor);
            } elseif ($rubrica == '0330' || $rubrica == '0334' || $rubrica == '0335' || $rubrica == '0336' ||
                $rubrica == '0337' || $rubrica == '0338' || $rubrica == '0340' || $rubrica == '0341' ||
                $rubrica == '0342' || $rubrica == '0343' || $rubrica == '0344' || $rubrica == '0345') {
                $margem_deduz += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
            }
        }
    } else {
        if (pg_fetch_result($this->recordenvelope, $ii, $this->rubrica) == 'R981' ||
            pg_fetch_result($this->recordenvelope, $ii, $this->rubrica) == 'R982') {
            $baseirrf += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
        } elseif (pg_fetch_result($this->recordenvelope, $ii, $this->rubrica) == 'R985' ||
            pg_fetch_result($this->recordenvelope, $ii, $this->rubrica) == 'R986' ||
            pg_fetch_result($this->recordenvelope, $ii, $this->rubrica) == 'R987'
        ) {
            $baseprev += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
        } elseif (pg_fetch_result($this->recordenvelope, $ii, $this->rubrica) == 'R991') {
            $basefgts += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
        } elseif (pg_fetch_result($this->recordenvelope, $ii, $this->rubrica) == 'R803') {
            $valor_margem += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
            $valor_margem = 0;
        }
    }
}

//Valor = Total dos Vencimentos
$this->objpdf->text($xcol+127, $xlin+93+$espacoExtra1, db_formatar($provento, 'f'));
//Valor = Total dos Descontos
$this->objpdf->text($xcol+150, $xlin+93+$espacoExtra1, db_formatar($desconto, 'f'));
$this->objpdf->Setfont('Arial', 'B', 9);
//Valor = Total dos Totais
$this->objpdf->text($xcol+150, $xlin+102+$espacoExtra1, db_formatar(( $provento - $desconto ), 'f'));
$this->objpdf->Setfont('Arial', '', 8);
//Margem Consignável
$xlinImpostos = $xlin + 111;
if (strtoupper($this->municpref) == 'ARAPIRACA') {
    if (db_getsession("DB_instit") == 1) {
        $this->objpdf->text($xcol+7, $xlinImpostos+$espacoExtra1, db_formatar((  (( $margem_consignada*30/100 ) - $margem_deduz ) < 0?0:(($margem_consignada*30/100 ) - $margem_deduz) ), 'f'));
    } else {
        $this->objpdf->text($xcol+7, $xlinImpostos+$espacoExtra1, db_formatar($provento*30/100, 'f'));
    }
} else {
    $this->objpdf->text($xcol+7, $xlinImpostos+$espacoExtra1, db_formatar($valor_margem, 'f'));
}
//Sal Base
$this->objpdf->text($xcol+30, $xlinImpostos+$espacoExtra1, db_formatar($this->f010, 'f'));
//Base Previdência
$this->objpdf->text($xcol+67, $xlinImpostos+$espacoExtra1, db_formatar($baseprev, 'f'));
//Base FGTS
$this->objpdf->text($xcol+94, $xlinImpostos+$espacoExtra1, db_formatar($basefgts, 'f'));
//FGTS do Mês
$this->objpdf->text($xcol+124, $xlinImpostos+$espacoExtra1, db_formatar(($basefgts*8/100), 'f'));
//Base IRRF
$this->objpdf->text($xcol+149, $xlinImpostos+$espacoExtra1, db_formatar($baseirrf, 'f'));

$this->objpdf->SetY($xlin+87);
$this->objpdf->SetX($xcol+1);
$this->objpdf->multicell(120, 4+$espacoExtra2, 'MENSAGEM :   '.$this->mensagem, 0, "J");
$this->objpdf->SetX($xcol+1);
$this->objpdf->multicell(0, 4, $this->histparcel);
$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->setfillcolor(0);
$this->objpdf->Setfont('Arial', '', 5);

// texto no canhoto do carne
$declaro = 'DECLARO TER RECEBIDO A IMPORTÂNCIA LÍQUIDA DISCRIMIDA NESTE RECIBO.';
if (isset($this->imprimirQRCode) && $this->imprimirQRCode) {
    $this->objpdf->TextWithDirection($xcol + 175, $xlin + 85, $declaro, 'U');
    $this->objpdf->line($xcol + 185, $xlin + 18, $xcol + 185, $xlin + 58);
    $this->objpdf->line($xcol + 185, $xlin + 62, $xcol + 185, $xlin + 83);
    $this->objpdf->TextWithDirection($xcol + 188, $xlin + 74, 'DATA', 'U');
    $this->objpdf->TextWithDirection($xcol + 188, $xlin + 52, 'ASSINATURA DO FUNCIONÁRIO', 'U');
// numero do contra-cheque
    $this->objpdf->TextWithDirection($xcol + 198.7, $xlin, $this->total . ' / ' . $this->numero, 'U');
    $this->objpdf->TextWithDirection($xcol + 194, $xlin + 110, "Para Verificar Autenticidade Acesse: " . $this->url, 'U');
    $this->objpdf->TextWithDirection($xcol + 192, $xlin + 90, "Código da Autenticação: ", 'U');
    $this->objpdf->Setfont('Arial', 'B', 5);
    $this->objpdf->TextWithDirection($xcol + 192, $xlin + 70, $this->codautent, 'U'); // código da autenticação

    $url_autenticidade = env('BASE_URL_AUTENTICIDADE');
    if (!empty($url_autenticidade)) {
        $fileQRCode = "tmp/autenticacao_contracheque_{$this->codautent}.png";
        \PHPQRCode\QRcode::png("{$url_autenticidade}/contracheque/{$this->codautent}", $fileQRCode, 'L', 5, 1);
        $this->objpdf->Image($fileQRCode, $xcol + 172, $xlin + 91, 20);
    }
} else {
    $this->objpdf->TextWithDirection($xcol + 175, $xlin + 110, $declaro, 'U'); // texto no canhoto do carne
    $this->objpdf->line($xcol + 186, $xlin + 20, $xcol + 186, $xlin + 65);
    $this->objpdf->line($xcol + 186, $xlin + 70, $xcol + 186, $xlin + 110);
    $this->objpdf->TextWithDirection($xcol + 189, $xlin + 92, 'DATA', 'U'); // texto no canhoto do carne
    $this->objpdf->TextWithDirection($xcol + 189, $xlin + 55, 'ASSINATURA DO FUNCIONÁRIO', 'U');
    $this->objpdf->TextWithDirection($xcol + 198.7, $xlin, $this->total . ' / ' . $this->numero, 'U');
    $this->objpdf->TextWithDirection($xcol + 194, $xlin + 110, "Para Verificar Autenticidade Acesse: {$this->url}", 'U');
    $this->objpdf->TextWithDirection($xcol + 194, $xlin + 55, "Código da Autenticação: ", 'U');
    $this->objpdf->Setfont('Arial', 'B', 5);
    $this->objpdf->TextWithDirection($xcol + 194, $xlin + 35, $this->codautent, 'U'); // código da autenticação
}
