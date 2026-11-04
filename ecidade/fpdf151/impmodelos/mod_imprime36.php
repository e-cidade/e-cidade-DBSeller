<?php

$this->objpdf->line(2, 148.5, 208, 148.5);
$xlin = 25;
$xcol = 4;
$iNumRows = pg_num_rows($this->dados);
for ($j = 0; $j < $iNumRows; $j++) {
    if ($j > 0) {
        $this->objpdf->addPage();
        $xlin = 25;
        $xcol = 4;
    }
    for ($i = 0; $i < 2; $i ++) {
        $this->objpdf->setfillcolor(245);
        $this->objpdf->roundedrect($xcol -2, $xlin -18, 206, 139.5, 2, 'DF', '1234');
        $this->objpdf->setfillcolor(255, 255, 255);
        $this->objpdf->Setfont('Arial', 'B', 11);
        $this->objpdf->Image('imagens/files/'.$this->logo, 15, $xlin -14, 14);
        $this->objpdf->Setfont('Arial', 'B', 9);
        $this->objpdf->text(40, $xlin -11, $this->nomeinst);
        $this->objpdf->Setfont('Arial', '', 9);
        $this->objpdf->text(40, $xlin -7, $this->ender);
        $this->objpdf->text(40, $xlin -4, $this->munic);
        $this->objpdf->text(40, $xlin -1, $this->telef);
        $this->objpdf->text(40, $xlin +2, $this->email);
        $this->objpdf->settextcolor(190);
        $this->objpdf->Setfont('Arial', 'B', 30);
        $this->objpdf->text(175.5, $xlin -7.5, 'SLIP');
        $this->objpdf->settextcolor(0, 0, 0);
        $this->objpdf->Setfont('Arial', 'B', 30);
        $this->objpdf->text(175, $xlin -7, 'SLIP');
        $this->objpdf->Setfont('Arial', 'B', 12);
        $this->objpdf->Setfont('Arial', '', 9);
        $xlin += 10;
        $this->objpdf->Roundedrect($xcol +132, $xlin -12, 70, 12, 2, 'DF', '1234');
        $k17_codigo = pg_fetch_result($this->dados, $j, "k17_codigo");
        $this->objpdf->text($xcol +134, $xlin -9, 'Slip N'.chr(176).' '.db_formatar($k17_codigo, 's', '0', 6, 'e'));
        $k17_data = pg_fetch_result($this->dados, $j, "k17_data");
        $this->objpdf->text($xcol +134, $xlin -5, 'Emissao : '.db_formatar($k17_data, 'd'));
        $this->objpdf->Setfont('Arial', '', 7);
        $this->objpdf->text($xcol + 134, $xlin -1, 'Emissor: '.$this->nome_usuario);
        $this->objpdf->Setfont('Arial', '', 9);
        $this->objpdf->Roundedrect($xcol, $xlin +2, 202, 13, 2, 'DF', '1234');
        $this->objpdf->Roundedrect($xcol, $xlin +17, 202, 13, 2, 'DF', '1234');
        $this->objpdf->Setfont('Arial', '', 8);
        $this->objpdf->text($xcol +2, $xlin +6, 'Conta Débito (Recebe):');
        $this->objpdf->Setfont('Arial', 'B', 9);
        if (pg_fetch_result($this->dados, 0, "k17_debito") != 0) {
            $k17_debito = pg_fetch_result($this->dados, $j, "k17_debito");
            $descr_debito = pg_fetch_result($this->dados, $j, "descr_debito");
            $this->objpdf->text($xcol +10, $xlin +12, $k17_debito.'   -   '.$descr_debito);
        } else {
            $this->objpdf->text($xcol +10, $xlin +12, '______________________________');
        }
        $this->objpdf->Setfont('Arial', '', 8);
        $this->objpdf->text($xcol +2, $xlin +21, 'Conta Crédito (Paga):');
        $this->objpdf->Setfont('Arial', 'B', 9);

        if (pg_fetch_result($this->dados, 0, "k17_credito") != 0) {
            $k17_credito = pg_fetch_result($this->dados, $j, "k17_credito");
            $descr_credito = pg_fetch_result($this->dados, $j, "descr_credito");
            $this->objpdf->text($xcol +10, $xlin +27, $k17_credito.'   -   '.$descr_credito);
        } else {
            $this->objpdf->text($xcol +10, $xlin +27, '______________________________');
        }
        $this->objpdf->sety($xlin +27);
        $maiscol = 0;
        $this->objpdf->Roundedrect($xcol, $xlin +32, 202, 55, 2, 'DF', '1234');
        $this->objpdf->SetY($xlin +33);
        $z01_numcgm = pg_fetch_result($this->dados, $j, "z01_numcgm");
        $z01_nome = pg_fetch_result($this->dados, $j, "z01_nome");
        $this->objpdf->multicell(0, 5, 'Favorecido    :   '. $z01_numcgm .' - '.$z01_nome);

    /**
     * Dados bancarios do credor
     */
        if (!empty($this->oDadosBancarioCredor)) {
            $sTextoDadosBancariosCredor  = $this->oDadosBancarioCredor->iBanco;
//          $sTextoDadosBancariosCredor .= ' - '         . $this->oDadosBancarioCredor->sBanco;
            $sTextoDadosBancariosCredor .= '  -  Agência: ' . $this->oDadosBancarioCredor->iAgencia;
            $sTextoDadosBancariosCredor .= ' - '         . $this->oDadosBancarioCredor->iAgenciaDigito;
            $sTextoDadosBancariosCredor .= '  Conta: '   . $this->oDadosBancarioCredor->iConta;
            $sTextoDadosBancariosCredor .= ' - '         . $this->oDadosBancarioCredor->iContaDigito;

            $this->objpdf->multicell(0, 5, 'Banco            :   ' . $sTextoDadosBancariosCredor);
        }

        if (USE_PCASP && isset($this->sEvento)) {
            $k152_descricao = pg_fetch_result($this->dados, $j, "k152_descricao");
            $k152_sequencial = pg_fetch_result($this->dados, $j, "k152_sequencial");
            $sEvento = $k152_sequencial.'  -  '.$k152_descricao;
            $this->objpdf->multicell(0, 5, 'Evento           :   '.strtoupper($sEvento));
        }

        $k17_hist = pg_fetch_result($this->dados, $j, "k17_hist");
        $decr_hist = pg_fetch_result($this->dados, $j, "descr_hist");
        $this->objpdf->multicell(0, 5, 'Histórico        :   '.$k17_hist.'  -  '.$decr_hist);
        $textoFinalidade = 'Finalidade Pagamento:   '.
        pg_fetch_result($this->dados, $j, "e151_codigo").'  -  '.
        pg_fetch_result($this->dados, $j, "e151_descricao");

        if (!empty($this->recurso_slip)) {
            $recurso = sprintf(
                '%s-%s-%s',
                $this->recurso_slip->gestao,
                $this->recurso_slip->o15_recurso,
                str_pad($this->recurso_slip->o15_complemento, 4, "0", STR_PAD_LEFT)
            );
            $textoFonte = "Fonte              : ".$recurso;
            $this->objpdf->multicell(0, 5, $textoFonte);
        }
        $this->objpdf->multicell(0, 5, $textoFinalidade);


        $this->objpdf->cell(20, 5, 'Observações :   ', 0, 1, "L");
        $this->objpdf->Setfont('Arial', '', 8);
        $sTextoObservacao = pg_fetch_result($this->dados, $j, "k17_texto");
        $sTextoObservacao = str_replace('', "\n", $sTextoObservacao);

        if (strlen($sTextoObservacao) > 700) {
            $sTextoObservacao = substr($sTextoObservacao, 0, 250)." ...";
        }
        $this->objpdf->multicell(0, 4, $sTextoObservacao);
        //$this->objpdf->multicell(0, 4, pg_fetch_result($this->dados, $j, "k17_texto"). (pg_fetch_result($this->dados, $j, "k18_codigo") != 0 ? "\n"."Anulado em ".db_formatar(pg_fetch_result($this->dados, $j, "k17_dtanu"), 'd')."\n"."Motivo : ".pg_fetch_result($this->dados, $j, "k18_motivo") : ''));
        if (pg_fetch_result($this->dados, 0, "k17_situacao") == 3) {
            $this->objpdf->Setfont('Arial', 'b', 8);
            $k17_dtestorno = db_formatar(pg_fetch_result($this->dados, 0, "k17_dtestorno"), 'd');
            $this->objpdf->multicell(190, 3, "Estornado em ". $k17_dtestorno, 0, "L");
            $this->objpdf->Setfont('Arial', '', 8);
            $motivo = substr((pg_fetch_result($this->dados, 0, "k17_motivoestorno")), 0, 900);
            $this->objpdf->Setfont('Arial', '', 8);
            $this->objpdf->multicell(190, 3, "Motivo : ".$motivo, 0, "L");
        } elseif (pg_fetch_result($this->dados, 0, "k17_situacao") == 4) {
            $this->objpdf->Setfont('Arial', 'b', 8);
            $k17_dtanu = db_formatar(pg_fetch_result($this->dados, $j, "k17_dtanu"), 'd');
            $this->objpdf->multicell(190, 3, "Anulado em ".$k17_dtanu, 0, "L");
            $this->objpdf->Setfont('Arial', '', 8);
            $k18_motivo = substr(pg_fetch_result($this->dados, $j, "k18_motivo"), 0, 900);
            $this->objpdf->multicell(190, 3, "Motivo : ".$k18_motivo, 0, "L");
        }

        $this->objpdf->Setfont('Arial', '', 8);
        $this->objpdf->Roundedrect($xcol, $xlin +88, 202, 20, 2, 'DF', '1234');
        $this->objpdf->text($xcol +2, $xlin +91, 'Valor');
        $this->objpdf->Setfont('Arial', 'B', 10);
        $this->objpdf->SetY($xlin +93);
        $extenso = db_extenso(pg_fetch_result($this->dados, $j, "k17_valor"));
        $k17_valor = db_formatar(pg_fetch_result($this->dados, $j, "k17_valor"), 'f');
        $this->objpdf->multicell(0, 4, 'R$ '.$k17_valor.' ('.trim($extenso).')');

    //Alterado dia 12/01/2006
    //O emissor aparece ao lado da folha como foi solicitado.
        $this->objpdf->setfillcolor(0, 0, 0);
        $this->objpdf->SetFont('Arial', '', 4);
        $login = db_getsession('DB_login');
        $data = date("d/m/Y", db_getsession("DB_datausu"));
        $ano = db_getsession('DB_anousu');
        $db_base = db_getsession('DB_base');
        $this->objpdf->TextWithDirection(1.5, 80, $login.' - '.$data.' - '.date('H:m').' - '.$ano.' - '.$db_base, 'U');
        $this->objpdf->TextWithDirection(1.5, 225, $login.' -'.$data.' - '.date('H:m').' - '.$ano.' - '.$db_base, 'U');

        $xlin = 169;
    }
}
