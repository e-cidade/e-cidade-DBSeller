<?php
$this->objpdf->AliasNbPages();
$this->objpdf->settopmargin(1);
$this->objpdf->line(2, 151.8, 208, 151.8);

if ($this->seq == 0) {
    $xlin  = 24;
    $this->objpdf->AddPage();
} else {
    $xlin = 171;
}

$xcol  = 4;
$cinza = 225;

$sql = "select r70_estrut, rh55_descr, senha, cgmpen.z01_cgccpf as cpfpen, cgmina.z01_nome as nomeinstituidor,
               rh30_vinculo, rh37_descr as cargo, rh04_descr as funcao 
        from rhpessoal pespen
             inner join cgm cgmpen      on pespen.rh01_numcgm = cgmpen.z01_numcgm
             inner join rhpessoalmov    on pespen.rh01_regist = rh02_regist
             LEFT  JOIN  rhfuncao       ON rh37_funcao =       rh02_funcao
                                       AND rh37_instit =       rh02_instit
             LEFT  JOIN  rhpescargo     ON rh20_seqpes =       rh02_seqpes
                                       AND rh20_instit =       rh02_instit
             LEFT  JOIN  rhcargo        ON rh04_codigo =       rh20_cargo
                                       AND rh04_instit =       rh02_instit

             inner join rhlota          on r70_codigo  = rh02_lota
                                       and r70_instit  = rh02_instit
             inner join rhregime        on rh30_codreg = rh02_codreg
                                       and rh30_instit = rh02_instit
             left join rhpeslocaltrab   on rh56_seqpes = rh02_seqpes
                                       and rh56_princ  = 't'
             left join rhlocaltrab      on rh56_localtrab = rh55_codigo
             left join natal.margem_zetra     on pespen.rh01_regist   = regist
             left join rhpesorigem      on pespen.rh01_regist = rh21_regist
             left join rhpessoal pesina on rh21_regpri = pesina.rh01_regist
             left join cgm cgmina       on pesina.rh01_numcgm = cgmina.z01_numcgm 
        where pespen.rh01_regist = {$this->registro}
          and rh02_anousu = {$this->ano}
          and rh02_mesusu = {$this->mes}
                ";
$result = db_query($sql);
$oDados = DB_utils::fieldsMemory($result, 0);

$this->objpdf->setfillcolor(225);
$this->objpdf->roundedrect($xcol-2, $xlin-18, 206, 144.5, 2, 'DF', '1234');
$this->objpdf->setfillcolor(255, 255, 255);
$this->objpdf->Setfont('Arial', 'B', 11);
$this->objpdf->text(130, $xlin-13, 'RECIBO DE PAGAMENTO');
$this->objpdf->text(130, $xlin-8, 'REF. AO MÊS '.db_formatar($this->mes, 's', '0', 2, 'e', 0).'/'.$this->ano);
$this->objpdf->text(130, $xlin-3, $this->qualarquivo);

$this->objpdf->Image('imagens/files/'.$this->logo, 15, $xlin-17, 12); //.$this->logo
$this->objpdf->Setfont('Arial', 'B', 9);
if (substr($oDados->r70_estrut, 0, 2) != '50') {
    $this->objpdf->text(30, $xlin-15, $this->prefeitura);
    $this->objpdf->Setfont('Arial', '', 7);
    $this->objpdf->text(30, $xlin-12, $this->enderpref);
    $this->objpdf->text(30, $xlin- 9, $this->municpref);
    $this->objpdf->text(30, $xlin- 6, $this->telefpref);
    $this->objpdf->text(30, $xlin- 3, db_formatar($this->cgcpref, 'cnpj'));
} else {
    $this->objpdf->text(30, $xlin-15, 'COMPANHIA DE SERVIÇOS URBANOS DO NATAL');
    $this->objpdf->Setfont('Arial', '', 7);
    $this->objpdf->text(30, $xlin-12, 'AV. DR. MARIO NEGOCIO');
    $this->objpdf->text(30, $xlin- 9, 'NATAL');
    $this->objpdf->text(30, $xlin- 6, '(84)3232.8769');
    $this->objpdf->text(30, $xlin- 3, '08.498.701/0001-04');
}
///retangulo da assinatura
$this->objpdf->Roundedrect($xcol+178, $xlin+14, $xcol+20, 110, 2, 'DF', '1234');

//retangulo onde fica no nome do funcionario
$this->objpdf->Roundedrect($xcol, $xlin, $xcol+198, 12, 2, 'DF', '1234');

$this->objpdf->Roundedrect($xcol, $xlin+14, $xcol+172, 82, 2, 'DF', '1234');
$this->objpdf->Roundedrect($xcol, $xlin+96, $xcol+172, 28, 2, 'DF', '1234');
$this->objpdf->line($xcol, $xlin+22, $xcol+176, $xlin+22);
$this->objpdf->line($xcol, $xlin+115, $xcol+176, $xlin+115);
$this->objpdf->line($xcol+130, $xlin+105, $xcol+176, $xlin+105);

$this->objpdf->line($xcol+153, $xlin+14, $xcol+153, $xlin+115);
$this->objpdf->line($xcol+130, $xlin+14, $xcol+130, $xlin+115);
$this->objpdf->line($xcol+115, $xlin+14, $xcol+115, $xlin+96);
$this->objpdf->line($xcol+15, $xlin+14, $xcol+15, $xlin+96);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+2, $xlin+3, 'Matrícula:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+12, $xlin+3, $this->registro);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+21, $xlin+3, 'Nome:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+31, $xlin+3, $this->nome);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+100, $xlin+3, 'Cargo:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+110, $xlin+3, $oDados->cargo);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+160, $xlin+3, 'Padrão:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+172, $xlin+3, $this->padrao);
if ($oDados->rh30_vinculo != 'P') {
    $this->objpdf->Setfont('Arial', '', 6);
    $this->objpdf->text($xcol+2, $xlin+7, 'L.Trabalho :');
    $this->objpdf->Setfont('Arial', 'B', 7);
    $this->objpdf->text($xcol+15, $xlin+7, $oDados->rh55_descr);
} else {
    $this->objpdf->Setfont('Arial', '', 6);
    $this->objpdf->text($xcol+2, $xlin+7, 'Instituidor :');
    $this->objpdf->Setfont('Arial', 'B', 7);
    $this->objpdf->text($xcol+15, $xlin+7, $oDados->nomeinstituidor);
}

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+100, $xlin+7, 'Função:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+110, $xlin+7, $oDados->funcao);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+100, $xlin+10, 'Bco/Ag/Cta:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+112, $xlin+10, $this->banco.' / '.$this->agencia.' / '.$this->conta);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+160, $xlin+7, 'Admissão:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+172, $xlin+7, $this->admissao);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+2, $xlin+11, 'Lotação:');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+12, $xlin+11, $this->descr_lota);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->Setfont('Arial', 'B', 7);

$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+160, $xlin+11, 'C P F  :');
$this->objpdf->Setfont('Arial', 'B', 7);
$this->objpdf->text($xcol+172, $xlin+11, db_formatar($oDados->cpfpen, 'cpf'));


$this->objpdf->Setfont('Arial', '', 8);
$this->objpdf->text($xcol+ 5, $xlin+18, 'Cód.');
$this->objpdf->text($xcol+ 55, $xlin+18, 'Descrição');
$this->objpdf->text($xcol+116, $xlin+18, 'Referência');
$this->objpdf->text($xcol+135, $xlin+18, 'Proventos');
$this->objpdf->text($xcol+157, $xlin+18, 'Descontos');
$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->text($xcol+155, $xlin+98, 'Total dos Descontos');
$this->objpdf->text($xcol+131, $xlin+98, 'Total dos Vencimentos');
$this->objpdf->text($xcol+133, $xlin+111, 'Líquido a Receber');
$this->objpdf->setfillcolor(225);
$this->objpdf->rect($xcol+153, $xlin+105, 23, 10, 'DF');
$this->objpdf->setfillcolor(255, 255, 255);

$this->objpdf->text($xcol+5, $xlin+117, 'Margem Consignavel');
$this->objpdf->text($xcol+40, $xlin+117, 'Sal. Base');
$this->objpdf->text($xcol+70, $xlin+117, 'Base Previdência');
$this->objpdf->text($xcol+100, $xlin+117, 'Base FGTS');
if ($this->registro == 49654) {
    $this->objpdf->text($xcol+127, $xlin+117, 'Contribuição Patronal');
} else {
    $this->objpdf->text($xcol+130, $xlin+117, 'FGTS do Mês');
}
$this->objpdf->text($xcol+160, $xlin+117, 'Base IRRF');


$this->objpdf->sety($xlin+24);
$maiscol = 0;
$yy = $this->objpdf->gety();
$provento = 0;
$margem_deduz  = 0;
$margem_consignada = 0;
$desconto     = 0;
$baseprev     = 0;
$basefgts     = 0;
$baseirrf     = 0;
$valor_margem = 0;

$this->objpdf->Setfont('Arial', '', 7);
for ($ii = 0; $ii < $this->linhasenvelope; $ii++) {
    if (pg_fetch_result($this->recordenvelope, $ii, $this->tipo)  == 'P') {
        $this->objpdf->cell(5, 3, trim(pg_fetch_result($this->recordenvelope, $ii, $this->rubrica)), 0, 0, "R", 0);
        $this->objpdf->cell(5, 3, "", 0, 0, "L", 0);
        $this->objpdf->cell(93, 3, pg_fetch_result($this->recordenvelope, $ii, $this->descr_rub), 0, 0, "L", 0);
        $this->objpdf->cell(20, 3, db_formatar(pg_fetch_result($this->recordenvelope, $ii, $this->quantidade), 'f'), 0, 0, "R", 0);
        $this->objpdf->cell(22, 3, db_formatar(pg_fetch_result($this->recordenvelope, $ii, $this->valor), 'f'), 0, 0, "R", 0);
        $this->objpdf->cell(22, 3, '', 0, 1, "R", 0);
        $provento += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
        $rubrica = trim(pg_fetch_result($this->recordenvelope, $ii, $this->rubrica));
        if (db_getsession("DB_instit") == 1 && strtoupper($this->municpref == 'GUAIBA') && ($rubrica == '0102' || $rubrica == '0109' || $rubrica == '0111' || $rubrica == '0195'  || $rubrica == '0196' || $rubrica == '0197' || $rubrica == '0198' )) {
            $margem_consignada += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
        } elseif (db_getsession("DB_instit") == 1 && strtoupper($this->municpref) == 'ARAPIRACA' &&
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
        $this->objpdf->cell(93, 3, pg_fetch_result($this->recordenvelope, $ii, $this->descr_rub), 0, 0, "L", 0);
        $this->objpdf->cell(20, 3, db_formatar(pg_fetch_result($this->recordenvelope, $ii, $this->quantidade), 'f'), 0, 0, "R", 0);
        $this->objpdf->cell(22, 3, '', 0, 0, "R", 0);
        $this->objpdf->cell(22, 3, db_formatar(pg_fetch_result($this->recordenvelope, $ii, $this->valor), 'f'), 0, 1, "R", 0);
        $desconto += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
        $rubrica = trim(pg_fetch_result($this->recordenvelope, $ii, $this->rubrica));
        if (db_getsession("DB_instit") == 1 && strtoupper($this->municpref) == 'ARAPIRACA') {
            if ($rubrica == 'R901' || $rubrica == 'R904' || $rubrica == 'R913' || $rubrica == '0333') {
                $margem_consignada -= pg_fetch_result($this->recordenvelope, $ii, $this->valor);
            } elseif ($rubrica == '0330' ||
                $rubrica == '0334' ||
                $rubrica == '0335' ||
                $rubrica == '0336' ||
                $rubrica == '0337' ||
                $rubrica == '0338' ||
                $rubrica == '0340' ||
                $rubrica == '0341' ||
                $rubrica == '0342' ||
                $rubrica == '0343' ||
                $rubrica == '0344' ||
                $rubrica == '0345'
            ) {
                $margem_deduz += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
            }
        }
    } else {
        if (pg_fetch_result($this->recordenvelope, $ii, $this->rubrica) == 'R981' ||
            pg_fetch_result($this->recordenvelope, $ii, $this->rubrica) == 'R982') {
            $baseirrf += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
        } elseif (pg_fetch_result($this->recordenvelope, $ii, $this->rubrica) == 'R992') {
            $baseprev += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
        } elseif (pg_fetch_result($this->recordenvelope, $ii, $this->rubrica) == 'R991') {
            $basefgts += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
        } elseif (pg_fetch_result($this->recordenvelope, $ii, $this->rubrica) == 'R803') {
            $valor_margem += pg_fetch_result($this->recordenvelope, $ii, $this->valor);
        }
        continue;
    }
}

$this->objpdf->text($xcol+134, $xlin+102, db_formatar($provento, 'f'));
$this->objpdf->text($xcol+157, $xlin+102, db_formatar($desconto, 'f'));
$this->objpdf->Setfont('Arial', 'B', 9);
$this->objpdf->text($xcol+157, $xlin+111, db_formatar(( $provento - $desconto ), 'f'));
$this->objpdf->Setfont('Arial', '', 8);

if (strtoupper($this->municpref == 'GUAIBA') || strtoupper($this->municpref) == 'ARAPIRACA') {
    if (db_getsession("DB_instit") == 1) {
        $this->objpdf->text($xcol+5, $xlin+121, db_formatar((  (( $margem_consignada*30/100 ) - $margem_deduz ) < 0?0:(($margem_consignada*30/100 ) - $margem_deduz) ), 'f'));
    } else {
        $this->objpdf->text($xcol+5, $xlin+121, db_formatar($provento*30/100, 'f'));
    }
} else {
    $this->objpdf->text($xcol+5, $xlin+121, db_formatar($valor_margem, 'f'));
}

$this->objpdf->text($xcol+33, $xlin+121, db_formatar($this->f010, 'f'));
$this->objpdf->text($xcol+70, $xlin+121, db_formatar($baseprev, 'f'));
$this->objpdf->text($xcol+95, $xlin+121, db_formatar($basefgts, 'f'));

if ($this->registro == 49654) {
    $this->objpdf->text($xcol+127, $xlin+121, db_formatar(($baseprev*22/100), 'f'));
} else {
    $this->objpdf->text($xcol+127, $xlin+121, db_formatar(($basefgts*8/100), 'f'));
}

$this->objpdf->text($xcol+155, $xlin+121, db_formatar($baseirrf, 'f'));

$this->objpdf->SetY($xlin+97);
$this->objpdf->SetX($xcol+3);

/*
* Valida msg aposentadoria
*/
$sSqlVerificaAposentadoria = "select rh01_regist 
                              from rhpessoal 
                                   inner join rhpessoalmov on rh02_regist = rh01_regist 
                                   inner join rhregime on rh02_codreg = rh30_codreg 
                                   left join rhpesrescisao on rh05_seqpes = rh02_seqpes 
                              where rhpessoalmov.rh02_anousu = {$this->ano} 
                                and rhpessoalmov.rh02_mesusu = {$this->mes}
                                and rhpessoalmov.rh02_regist = {$this->registro} 
                                and extract(month from rhpessoal.rh01_clas2) in (9,10);";
$rsVerificaAposentadoria = db_query($sSqlVerificaAposentadoria);
$iLinhasVerificaAposentadoria = pg_num_rows($rsVerificaAposentadoria);
if ($iLinhasVerificaAposentadoria > 0) {
    $this->mensagem .= "\nSr(a) Aposentado(a) admitido pela Prefeitura Municipal do Natal antes de Janeiro/1998,";
    $this->mensagem .= "apresente sua Carteira de Trabalho no NATALPREV ou envie cópia via e-mail: ";
    $this->mensagem .= "recadastramento.natalprev@natal.rn.gov.br. Mais informações ou esclarecimentos, ligue: 3232-8841";
}
$this->objpdf->Setfont('Arial', '', 7);
$this->objpdf->multicell(125, 4, 'MENSAGEM :   '.$this->mensagem, 0, "J");
$this->objpdf->Setfont('Arial', '', 8);
$this->objpdf->SetX($xcol+3);
$this->objpdf->multicell(0, 4, $this->histparcel);
$this->objpdf->Setfont('Arial', '', 6);
$this->objpdf->setx(15);
$this->objpdf->setfillcolor(0);
$this->objpdf->Setfont('Arial', '', 5);
$this->objpdf->TextWithDirection(185, $xlin+120, 'DECLARO TER RECEBIDO A IMPORTÂNCIA LÍQUIDA DISCRIMIDA NESTE RECIBO.', 'U'); // texto no canhoto do carne
$this->objpdf->line($xcol+193, $xlin+25, $xcol+193, $xlin+70);
$this->objpdf->line($xcol+193, $xlin+75, $xcol+193, $xlin+115);
$this->objpdf->TextWithDirection(200, $xlin+97, 'DATA', 'U'); // texto no canhoto do carne
$this->objpdf->TextWithDirection(200, $xlin+60, 'ASSINATURA DO FUNCIONÁRIO', 'U'); // texto no canhoto do carne
$this->objpdf->TextWithDirection(209.7, $xlin, $this->total.' / '.$this->numero, 'U'); // numero do contra-cheque
$this->objpdf->TextWithDirection(205, $xlin+120, "Para Verificar Autenticidade Acesse: ".$this->url, 'U');
$this->objpdf->TextWithDirection(205, $xlin+60, "Código da Autenticação: ", 'U');
$this->objpdf->Setfont('Arial', 'B', 5);
$this->objpdf->TextWithDirection(205, $xlin+40, $this->codautent, 'U');
