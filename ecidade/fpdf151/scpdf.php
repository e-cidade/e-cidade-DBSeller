<?php
global $HTTP_POST_VARS;
global $HTTP_SERVER_VARS;

if (session_id() == null)
   session_start();

if(!defined('DB_BIBLIOT')){

  require_once(modification("libs/db_stdlib.php"));
    require_once(modification("libs/db_conecta.php"));
    require_once(modification("libs/db_sessoes.php"));
    if (!db_getsession("DB_processamento_automatico", false)) {
        require_once(modification("libs/db_usuariosonline.php"));
    }
    db_postmemory($HTTP_POST_VARS);
  db_postmemory($HTTP_SERVER_VARS);

  if(!defined('FPDF_FONTPATH')){
    define('FPDF_FONTPATH', 'fpdf151/font/');
  }
  require_once(modification('fpdf151/fpdf.php'));
}

class scpdf extends fpdf {
//|00|//scpdf
//|10|//Esta classe é uma extensão da classe |fpdf|, não possui cabeçalho ou rodapé, é classe utilizada
//|10|//na geração de formularios tais como: carnês de parcelamento, recibos, alvarás, etc
  function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {

    $h = $this->h;
    $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1*$this->k, ($h-$y1)*$this->k,
    $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
  }

   function VCell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false) {
     //Output a cell
     $k=$this->k;
     if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
     {
       //Automatic page break
       $x=$this->x;
       $ws=$this->ws;
       if($ws>0)
       {
         $this->ws=0;
         $this->_out('0 Tw');
       }
       $this->AddPage($this->CurOrientation,$this->CurPageFormat);
       $this->x=$x;
       if($ws>0)
       {
         $this->ws=$ws;
         $this->_out(sprintf('%.3F Tw',$ws*$k));
       }
     }
     if($w==0)
       $w=$this->w-$this->rMargin-$this->x;
     $s='';
     // begin change Cell function
     if($fill || $border>0)
     {
       if($fill)
         $op=($border>0) ? 'B' : 'f';
       else
         $op='S';
       if ($border>1) {
         $s=sprintf('q %.2F w %.2F %.2F %.2F %.2F re %s Q ',$border,
             $this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
       }
       else
         $s=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
     }
     if(is_string($border))
     {
       $x=$this->x;
       $y=$this->y;
       if(is_int(strpos($border,'L')))
         $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
       else if(is_int(strpos($border,'l')))
         $s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);

       if(is_int(strpos($border,'T')))
         $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
       else if(is_int(strpos($border,'t')))
         $s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);

       if(is_int(strpos($border,'R')))
         $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
       else if(is_int(strpos($border,'r')))
         $s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);

       if(is_int(strpos($border,'B')))
         $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
       else if(is_int(strpos($border,'b')))
         $s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
     }
     if(trim($txt)!='')
     {
       $cr=substr_count($txt,"\n");
       if ($cr>0) { // Multi line
         $txts = explode("\n", $txt);
         $lines = count($txts);
         for($l=0;$l<$lines;$l++) {
           $txt=$txts[$l];
           $w_txt=$this->GetStringWidth($txt);
           if ($align=='U')
             $dy=$this->cMargin+$w_txt;
           elseif($align=='D')
           $dy=$h-$this->cMargin;
           else
             $dy=($h+$w_txt)/2;
           $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
           if($this->ColorFlag)
             $s.='q '.$this->TextColor.' ';
           $s.=sprintf('BT 0 1 -1 0 %.2F %.2F Tm (%s) Tj ET ',
               ($this->x+.5*$w+(.7+$l-$lines/2)*$this->FontSize)*$k,
               ($this->h-($this->y+$dy))*$k,$txt);
           if($this->ColorFlag)
             $s.=' Q ';
         }
       }
       else { // Single line
         $w_txt=$this->GetStringWidth($txt);
         $Tz=100;
         if ($w_txt>$h-2*$this->cMargin) {
           $Tz=($h-2*$this->cMargin)/$w_txt*100;
           $w_txt=$h-2*$this->cMargin;
         }
         if ($align=='U')
           $dy=$this->cMargin+$w_txt;
         elseif($align=='D')
         $dy=$h-$this->cMargin;
         else
           $dy=($h+$w_txt)/2;
         $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
         if($this->ColorFlag)
           $s.='q '.$this->TextColor.' ';
         $s.=sprintf('q BT 0 1 -1 0 %.2F %.2F Tm %.2F Tz (%s) Tj ET Q ',
             ($this->x+.5*$w+.3*$this->FontSize)*$k,
             ($this->h-($this->y+$dy))*$k,$Tz,$txt);
         if($this->ColorFlag)
           $s.=' Q ';
       }
     }
     // end change Cell function
     if($s)
       $this->_out($s);
     $this->lasth=$h;
     if($ln>0)
     {
       //Go to next line
       $this->y+=$h;
       if($ln==1)
         $this->x=$this->lMargin;
     }
     else
       $this->x+=$w;
   }

   function TextWithRotation($x,$y,$txt,$txt_angle,$font_angle=0)
    {
        $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));

        $font_angle+=90+$txt_angle;
        $txt_angle*=M_PI/180;
        $font_angle*=M_PI/180;

        $txt_dx=cos($txt_angle);
        $txt_dy=sin($txt_angle);
        $font_dx=cos($font_angle);
        $font_dy=sin($font_angle);

        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',
                 $txt_dx,$txt_dy,$font_dx,$font_dy,
                 $x*$this->k,($this->h-$y)*$this->k,$txt);
        $this->_out($s);


    }

    //Page headerMovel
    function headerMovel($deslocamentoVertical) {
        //#00#//header
        //#10#//Este método é usado gerar o cabeçalho da página. É chamado automaticamente por |addPage| e não
        //#10#//deve ser chamado diretamente pela aplicação. A implementação em FPDF está  vazia,  então  você
        //#10#//precisa criar uma subclasse dele para  sobrepor o  método  se  você  quiser  um  processamento
        //#10#//específico para o cabeçalho.
        //#15#//header()
        //#99#//Exemplo:
        //#99#//class PDF extends FPDF
        //#99#//{
        //#99#//  function Header()
        //#99#//  {
        //#99#//    Seleciona fonte Arial bold 15
        //#99#//      $this->SetFont('Arial','B',15);
        //#99#//    Move para a direita
        //#99#//      $this->Cell(80);
        //#99#//    Titulo dentro de uma caixa
        //#99#//      $this->Cell(30,10,'Title',1,0,'C');
        //#99#//    Quebra de linha
        //#99#//      $this->Ln(20);
        //#99#//  }
        //#99#//}

        global $conn;
        global $result;
        global $url;
        global $iEscola;
        //Dados da instituição

        //   echo ("select nomeinst,ender,munic,uf,telef,email,url,logo from db_config where codigo = ".db_getsession("DB_instit"));
        //   $dados = db_query("select nomeinst,ender,munic,uf,telef,email,url,logo from db_config where codigo = ".db_getsession("DB_instit"));

        $dados = db_query($conn,"select nomeinst,trim(ender)||','||trim(cast(numero as text)) as ender,munic,uf,telef,email,url,logo from db_config where codigo = ".db_getsession("DB_instit"));
        $url = @pg_result($dados,0,"url");
        $this->SetXY(1,1 + $deslocamentoVertical);
        $this->Image('imagens/files/'.pg_result($dados,0,"logo"),7,3 + $deslocamentoVertical,20);
        if ($_SESSION["DB_modulo"] == 1100747) {
            if (!isset($iEscola)){
                $iEscola = 	db_getsession("DB_coddepto");
            }

            //$this->Cell(100,32,"",1);
            $dados1 = db_query($conn,"select ed18_c_nome,
                                       j14_nome,
                                       ed18_i_numero,
                                       j13_descr,
                                       ed261_c_nome,
                                       ed260_c_sigla,
                                       ed18_c_email,
                                       ed18_c_logo,
                                       ed18_codigoreferencia
                                 from escola
                                  inner join bairro  on  bairro.j13_codi = escola.ed18_i_bairro
                                  inner join ruas  on  ruas.j14_codigo = escola.ed18_i_rua
                                  inner join db_depart  on  db_depart.coddepto = escola.ed18_i_codigo
                                  inner join censouf  on  censouf.ed260_i_codigo = escola.ed18_i_censouf
                                  inner join censomunic  on  censomunic.ed261_i_codigo = escola.ed18_i_censomunic
                                  left join ruascep on ruascep.j29_codigo = ruas.j14_codigo
                                  left join logradcep on logradcep.j65_lograd = ruas.j14_codigo
                                  left join ceplogradouros on ceplogradouros.cp06_codlogradouro = logradcep.j65_ceplog
                                  left join ceplocalidades on ceplocalidades.cp05_codlocalidades = ceplogradouros.cp06_codlocalidade
                                 where ed18_i_codigo = ".$iEscola);
            $nome = pg_result($dados,0,"nomeinst");
            $nomeescola = pg_result($dados1,0,"ed18_c_nome");
            global $nomeinst;
            $nomeinst = pg_result($dados,0,"nomeinst");
            if(strlen($nome) > 42 || strlen($nomeescola) > 42)
                $TamFonteNome = 8;
            else
                $TamFonteNome = 9;
            if(trim(pg_result($dados1,0,"ed18_c_logo"))!=""){
                $this->Image('imagens/'.trim(pg_result($dados1,0,"ed18_c_logo")), 105, 4 + $deslocamentoVertical, 20);
            }
            $ruaescola = trim(pg_result($dados1,0,"j14_nome"));
            $numescola = trim(pg_result($dados1,0,"ed18_i_numero"));
            $bairroescola = trim(pg_result($dados1,0,"j13_descr"));
            $cidadeescola = trim(pg_result($dados1,0,"ed261_c_nome"));
            $estadoescola = trim(pg_result($dados1,0,"ed260_c_sigla"));
            $emailescola = trim(pg_result($dados1,0,"ed18_c_email"));
            $dados2 = db_query($conn,"select ed26_i_numero from telefoneescola where ed26_i_escola = ".db_getsession("DB_coddepto")." LIMIT 1");
            if(pg_num_rows($dados2)>0){
                $telefoneescola = trim(pg_result($dados2,0,"ed26_i_numero"));
            }else{
                $telefoneescola = "";
            }

            /**
             * Valida se a escola possui um código referente cadastrado e o adiciona antes do nome da escola
             */
            $iCodigoReferencia = trim(pg_result($dados1,0,"ed18_codigoreferencia"));

            if ( $iCodigoReferencia != null ) {
                $nomeescola = "{$iCodigoReferencia} - {$nomeescola}";
            }

            $this->SetFont('Arial','BI',$TamFonteNome);
            $this->Text(33,9 + $deslocamentoVertical,$nome);

            // Ajusta o tamanho da fonte do nome da escola dinamicamente
            $content = "{$nomeescola} ";
            $w = 93;
            $tamanhoString = $this->GetStringWidth($content);

            if ($tamanhoString > $w) {
                // Deixa a fonte EXATAMENTE no tamanho para caber na célula
                $tamanhoFonte = 8 * $w / $tamanhoString;

                $this->SetFontSize($tamanhoFonte);
            }

            $this->Text(33,14 + $deslocamentoVertical,$nomeescola);
            $this->SetFont('Arial','I',8);
            $this->Text(33,18 + $deslocamentoVertical,$ruaescola.", ".$numescola." - ".$bairroescola);
            $this->Text(33,22 + $deslocamentoVertical,$cidadeescola." - ".$estadoescola);
            $this->Text(33,26 + $deslocamentoVertical,$telefoneescola);
            $comprim = ($this->w - $this->rMargin - $this->lMargin);
            /*    $this->Text(33,30,($emailescola!=""?$emailescola." - ":"").$url); FHSYS - Removido a URL conforme chamado 030 por Flavio Henrique em 13/06/2017 */
            $this->Text(33,30 + $deslocamentoVertical,($emailescola!=""?$emailescola:""));
            $Espaco = $this->w - 80 ;
            $this->SetFont('Arial','',7);
            $margemesquerda = $this->lMargin;
            $this->setleftmargin($Espaco);
            $this->sety(6 + $deslocamentoVertical);
            $this->setfillcolor(235);
            $this->roundedrect($Espaco - 3,5 + $deslocamentoVertical,75,28,2,'DF','123');
            $this->line(10,33 + $deslocamentoVertical,$comprim,33 + $deslocamentoVertical);
            $this->setfillcolor(255);
            $this->multicell(0,3,@$GLOBALS["head1"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head2"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head3"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head4"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head5"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head6"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head7"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head8"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head9"],0,1,"J",0);
            $this->setleftmargin($margemesquerda);
            $this->SetY(35 + $deslocamentoVertical);
        } else {

            $dados = db_query($conn,"select nomeinst,
                                       db21_compl,
                                       trim(ender)||',
                                       '||trim(cast(numero as text)) as ender,
                                       trim(ender) as rua,
                                       munic,
                                       numero,
                                       uf,
                                       cgc,
                                       telef,
                                       email,
                                       url,
                                       logo
                                from db_config where codigo = ".db_getsession("DB_instit"));
            $url = @pg_result($dados,0,"url");
            $this->SetXY(1,1 + $deslocamentoVertical);
            $this->Image('imagens/files/'.pg_result($dados,0,"logo"),7,3 + $deslocamentoVertical,20);

            //$this->Cell(100,32,"",1);
            $nome = pg_result($dados,0,"nomeinst");
            global $nomeinst;
            $nomeinst = pg_result($dados,0,"nomeinst");

            if(strlen($nome) > 42)
                $TamFonteNome = 8;
            else
                $TamFonteNome = 9;

            $this->SetFont('Arial','BI',$TamFonteNome);
            $this->Text(33,9 + $deslocamentoVertical,$nome);

            $this->SetFont('Arial','I',8);
            $sComplento = substr(trim(pg_result($dados,0,"db21_compl") ),0,20 );
            if ($sComplento != '' || $sComplento != null ) {
                $sComplento = ", ".substr(trim(pg_result($dados,0,"db21_compl") ),0,20 );
            }
            $this->Text(33,14 + $deslocamentoVertical,trim(pg_result($dados,0,"rua")).", ".trim(pg_result($dados,0,"numero")).$sComplento );
            $this->Text(33,18 + $deslocamentoVertical,trim(pg_result($dados,0,"munic"))." - ".pg_result($dados,0,"uf"));
            $this->Text(33,22 + $deslocamentoVertical,trim(pg_result($dados,0,"telef"))."   -    CNPJ : ".db_formatar(pg_result($dados,0,"cgc"),"cnpj"));
            $this->Text(33,26 + $deslocamentoVertical,trim(pg_result($dados,0,"email")));
            $comprim = ($this->w - $this->rMargin - $this->lMargin);
            $this->Text(33,30 + $deslocamentoVertical,$url);
            $Espaco = $this->w - 80 ;
            $this->SetFont('Arial','',7);
            $margemesquerda = $this->lMargin;
            $this->setleftmargin($Espaco);
            $this->sety(6 + $deslocamentoVertical);
            $this->setfillcolor(235);
            $this->roundedrect($Espaco - 3,5 + $deslocamentoVertical,75,28,2,'DF','123');
            $this->line(10,33 + $deslocamentoVertical,$comprim,33 + $deslocamentoVertical);
            $this->setfillcolor(255);
            $this->multicell(0,3,@$GLOBALS["head1"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head2"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head3"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head4"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head5"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head6"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head7"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head8"],0,1,"J",0);
            $this->multicell(0,3,@$GLOBALS["head9"],0,1,"J",0);
            $this->setleftmargin($margemesquerda);
            $this->SetY(35 + $deslocamentoVertical);
        }
    }

    function AddPageFicai($orientation='')
//#00#//addpage
//#10#//Adiciona uma página nova ao documento. Se uma página já existir, o método de Footer() é chamado antes para saída
//#10#//do rodapé. Então a página é adicionada, a posição atual é ajustada ao  canto  superior-esquerdo de acordo com as
//#10#//margens esquerdas e superiores, e Header() é chamado para montar o cabeçalho.
//#10#//A fonte que foi ajustada antes de chamar é restaurada  automaticamente.  Não há nenhuma necessidade chamar outra
//#10#//vez |setfont()| se você quiser continuar com a mesma fonte. O mesmo é verdadeiro para cores e largura da linha.
//#10#//A origem do sistema de coordenadas está no de canto superior-esquerdo e as ordenadas cescem para baixo.
//#15#//addpage($orientation='')
//#20#//orientation  : Orientação da página. Os valores possíveis são (diferenciando maiúsculas e minúsculas):
//#20#//                  - P para relrato
//#20#//                  - L para paisagem
//#20#//               O valor padrão é o que foi passado ao construtor. |fpdf|


    {
        //Start a new page
        $family=$this->FontFamily;
        $style=$this->FontStyle.($this->underline ? 'U' : '');
        $size=$this->FontSizePt;
        $lw=$this->LineWidth;
        $dc=$this->DrawColor;
        $fc=$this->FillColor;
        $tc=$this->TextColor;
        $cf=$this->ColorFlag;
        if($this->page>0)
        {
            //Page footer
            $this->InFooter=true;
            $this->Footer();
            $this->InFooter=false;
            //Close page
            $this->_endpage();
        }
        //Start new page
        $this->_beginpage($orientation);
        //Set line cap style to square
        $this->_out('2 J');
        //Set line width
        $this->LineWidth=$lw;
        $this->_out(sprintf('%.2f w',$lw*$this->k));
        //Set font
        if($family)
            $this->SetFont($family,$style,$size);
        //Set colors
        $this->DrawColor=$dc;
        if($dc!='0 G')
            $this->_out($dc);
        $this->FillColor=$fc;
        if($fc!='0 g')
            $this->_out($fc);
        $this->TextColor=$tc;
        $this->ColorFlag=$cf;
        //Page header
        $this->headerFicai();
        //Restore line width
        if($this->LineWidth!=$lw)
        {
            $this->LineWidth=$lw;
            $this->_out(sprintf('%.2f w',$lw*$this->k));
        }
        //Restore font
        if($family)
            $this->SetFont($family,$style,$size);
        //Restore colors
        if($this->DrawColor!=$dc)
        {
            $this->DrawColor=$dc;
            $this->_out($dc);
        }
        if($this->FillColor!=$fc)
        {
            $this->FillColor=$fc;
            $this->_out($fc);
        }
        $this->TextColor=$tc;
        $this->ColorFlag=$cf;
    }

    function headerFicai()
    {
        if (!$this->lExibeHeader) {
            return false;
        }
        //#00#//header
        //#10#//Este método é usado gerar o cabeçalho da página. É chamado automaticamente por |addPage| e não
        //#10#//deve ser chamado diretamente pela aplicação. A implementação em FPDF está  vazia,  então  você
        //#10#//precisa criar uma subclasse dele para  sobrepor o  método  se  você  quiser  um  processamento
        //#10#//específico para o cabeçalho.
        //#15#//header()
        //#99#//Exemplo:
        //#99#//class PDF extends FPDF
        //#99#//{
        //#99#//  function Header()
        //#99#//  {
        //#99#//    Seleciona fonte Arial bold 15
        //#99#//      $this->SetFont('Arial','B',15);
        //#99#//    Move para a direita
        //#99#//      $this->Cell(80);
        //#99#//    Titulo dentro de uma caixa
        //#99#//      $this->Cell(30,10,'Title',1,0,'C');
        //#99#//    Quebra de linha
        //#99#//      $this->Ln(20);
        //#99#//  }
        //#99#//}

        global $result;
        global $url;
        global $iEscola;
        //Dados da instituição

        //   echo ("select nomeinst,ender,munic,uf,telef,email,url,logo from db_config where codigo = ".db_getsession("DB_instit"));
        //   $dados = db_query("select nomeinst,ender,munic,uf,telef,email,url,logo from db_config where codigo = ".db_getsession("DB_instit"));

        $dados = db_query("select nomeinst,trim(ender)||','||trim(cast(numero as text)) as ender,munic,uf,telef,email,url,logo from db_config where codigo = " . db_getsession("DB_instit"));
        $url = @pg_result($dados, 0, "url");
        $this->SetXY(1, 1);
        if ($this->lExibeBrasao) {
            $this->Image('imagens/files/' . pg_result($dados, 0, "logo"), 7, 3, 20);
        }
        if ($_SESSION["DB_modulo"] == 1100747) {
            if (!isset($iEscola)) {
                $iEscola = db_getsession("DB_coddepto");
            }

            //$this->Cell(100,32,"",1);
            $dados1 = db_query("select ed18_c_nome,
                                   ed18_codigoreferencia,
                                   j14_nome,
                                   ed18_i_numero,
                                   j13_descr,
                                   ed261_c_nome,
                                   ed260_c_sigla,
                                   ed18_c_email,
                                   ed18_c_logo
                             from escola
                              inner join bairro  on  bairro.j13_codi = escola.ed18_i_bairro
                              inner join ruas  on  ruas.j14_codigo = escola.ed18_i_rua
                              inner join db_depart  on  db_depart.coddepto = escola.ed18_i_codigo
                              inner join censouf  on  censouf.ed260_i_codigo = escola.ed18_i_censouf
                              inner join censomunic  on  censomunic.ed261_i_codigo = escola.ed18_i_censomunic
                              left join ruascep on ruascep.j29_codigo = ruas.j14_codigo
                              left join logradcep on logradcep.j65_lograd = ruas.j14_codigo
                              left join ceplogradouros on ceplogradouros.cp06_codlogradouro = logradcep.j65_ceplog
                              left join ceplocalidades on ceplocalidades.cp05_codlocalidades = ceplogradouros.cp06_codlocalidade
                             where ed18_i_codigo = " . $iEscola);
            $nome = pg_result($dados, 0, "nomeinst");

            $nomeescola = pg_result($dados1, 0, "ed18_c_nome");
            $iCodigoReferencia = pg_result($dados1, 0, "ed18_codigoreferencia");

            if ($iCodigoReferencia != null) {
                $nomeescola = "{$iCodigoReferencia} - {$nomeescola}";
            }

            global $nomeinst;
            $nomeinst = pg_result($dados, 0, "nomeinst");
            if (strlen($nome) > 42 || strlen($nomeescola) > 42) {
                $TamFonteNome = 8;
            } else {
                $TamFonteNome = 9;
            }
            if (trim(pg_result($dados1, 0, "ed18_c_logo")) != "") {
                if ($this->lExibeBrasao) {
                    $this->Image('imagens/' . trim(pg_result($dados1, 0, "ed18_c_logo")), 170, 4, 20);
                }
            }
            $ruaescola = trim(pg_result($dados1, 0, "j14_nome"));
            $numescola = trim(pg_result($dados1, 0, "ed18_i_numero"));
            $bairroescola = trim(pg_result($dados1, 0, "j13_descr"));
            $cidadeescola = trim(pg_result($dados1, 0, "ed261_c_nome"));
            $estadoescola = trim(pg_result($dados1, 0, "ed260_c_sigla"));
            $emailescola = trim(pg_result($dados1, 0, "ed18_c_email"));
            $dados2 = db_query("select ed26_i_numero from telefoneescola where ed26_i_escola = " . db_getsession("DB_coddepto") . " LIMIT 1");
            if (pg_num_rows($dados2) > 0) {
                $telefoneescola = trim(pg_result($dados2, 0, "ed26_i_numero"));
            } else {
                $telefoneescola = "";
            }
            $this->SetFont('Arial', 'BI', $TamFonteNome);
            $this->Text(33, 9, $nome);
            $this->Text(33, 14, $nomeescola);
            $this->SetFont('Arial', 'I', 8);
            $this->Text(33, 18, $ruaescola . ", " . $numescola . " - " . $bairroescola);
            $this->Text(33, 22, $cidadeescola . " - " . $estadoescola);
            $this->Text(33, 26, $telefoneescola);
            $comprim = ($this->w - $this->rMargin - $this->lMargin);
            $this->Text(33, 30, ($emailescola != "" ? $emailescola . " - " : "") . $url);
            $Espaco = $this->w - 80;
            $this->SetFont('Arial', '', 7);
            $margemesquerda = $this->lMargin;
            $this->setleftmargin($Espaco);
            $this->sety(6);
            $this->setfillcolor(235);
//            $this->roundedrect($Espaco - 3, 5, 75, 28, 2, 'DF', '123');
            $this->line(10, 33, $comprim, 33);
            $this->setfillcolor(255);
            $this->multicell(0, 3, @$GLOBALS["head1"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head2"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head3"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head4"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head5"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head6"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head7"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head8"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head9"], 0, 1, "J", 0);
            $this->setleftmargin($margemesquerda);
            $this->SetY(35);
        } else {
            $dados = db_query("select nomeinst,
                                   db21_compl,
                                   trim(ender)||',
                                   '||trim(cast(numero as text)) as ender,
                                   trim(ender) as rua,
                                   munic,
                                   numero,
                                   uf,
                                   cgc,
                                   telef,
                                   email,
                                   url,
                                   logo
                            from db_config where codigo = " . db_getsession("DB_instit"));
            $url = @pg_result($dados, 0, "url");
            $this->SetXY(1, 1);
            $this->Image('imagens/files/' . pg_result($dados, 0, "logo"), 7, 3, 20);

            //$this->Cell(100,32,"",1);
            $nome = pg_result($dados, 0, "nomeinst");
            global $nomeinst;
            $nomeinst = pg_result($dados, 0, "nomeinst");

            if (strlen($nome) > 42) {
                $TamFonteNome = 8;
            } else {
                $TamFonteNome = 9;
            }

            $this->SetFont('Arial', 'BI', $TamFonteNome);
            $this->Text(33, 9, $nome);
            $this->SetFont('Arial', 'I', 8);
            $sComplento = substr(trim(pg_result($dados, 0, "db21_compl")), 0, 20);
            if ($sComplento != '' || $sComplento != null) {
                $sComplento = ", " . substr(trim(pg_result($dados, 0, "db21_compl")), 0, 20);
            }
            $this->Text(33, 14, trim(pg_result($dados, 0, "rua")) . ", " . trim(pg_result($dados, 0, "numero")) . $sComplento);
            $this->Text(33, 18, trim(pg_result($dados, 0, "munic")) . " - " . pg_result($dados, 0, "uf"));
            $this->Text(33, 22, trim(pg_result($dados, 0, "telef")) . "   -    CNPJ : " . db_formatar(pg_result($dados, 0, "cgc"), "cnpj"));
            $this->Text(33, 26, trim(pg_result($dados, 0, "email")));
            $comprim = ($this->w - $this->rMargin - $this->lMargin);
            $this->Text(33, 30, $url);
            $Espaco = $this->w - 80;
            $this->SetFont('Arial', '', 7);
            $margemesquerda = $this->lMargin;
            $this->setleftmargin($Espaco);
            $this->sety(6);
            $this->setfillcolor(235);
            $this->roundedrect($Espaco - 3, 5, 75, 28, 2, 'DF', '123');
            $this->line(10, 33, $comprim, 33);
            $this->setfillcolor(255);
            $this->multicell(0, 3, @$GLOBALS["head1"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head2"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head3"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head4"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head5"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head6"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head7"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head8"], 0, 1, "J", 0);
            $this->multicell(0, 3, @$GLOBALS["head9"], 0, 1, "J", 0);
            $this->setleftmargin($margemesquerda);
            $this->SetY(35);
        }
    }

}
