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

require_once  modification("fpdf151/pdf.php");
require_once  modification("libs/db_sql.php");
require_once  modification("dbforms/db_funcoes.php");

require_once  modification('classes/db_fis_levanta_estendida_classe.php');

$cllevanta      = new cl_fis_levanta_estendida;
$clparfiscal    = new cl_fis_parfiscal;
$cllevvalor     = new cl_fis_levvalor;
$cllevantanotas = new cl_fis_levantanotas;
$cllevinscr     = new cl_fis_levinscr;
$clativprinc    = new cl_ativprinc;
$cllevusu       = new cl_fis_levusu;
$cllevcgm       = new cl_fis_levcgm;

$clrotulo = new rotulocampo;
$clrotulo->label("q02_inscr");
$clrotulo->label("y63_aliquota");
$cllevantanotas->rotulo->label();

db_postmemory($HTTP_POST_VARS);

class NPDF extends PDF{
  function Header(){

    global $conn;
    global $result;
    global $url;
    global $db21_compl;

    //Dados da instituição
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
    $this->SetXY(1,1);
    $this->Image('imagens/files/'.pg_result($dados,0,"logo"),7,3,20);

    $nome = pg_result($dados,0,"nomeinst");
    global $nomeinst;
    $nomeinst = pg_result($dados,0,"nomeinst");

    if(strlen($nome) > 42)
      $TamFonteNome = 8;
    else
      $TamFonteNome = 9;

    $fazenda = db_query($conn,'select
                z01_nome,
                z01_ender,
                z01_numero,
                z01_bairro,
                z01_munic,
                z01_uf,
                z01_cep,
                z01_telef
              from cgm
              where
                z01_numcgm = 3547');

    $this->SetFont('Arial','BI',$TamFonteNome);
    $this->Text(33,9,pg_result($fazenda,0,'z01_nome'));
    $this->SetFont('Arial','I',8);
    $this->Text(33,14,'RUA '.trim(pg_result($fazenda,0,"z01_ender")).", Nº ".trim(pg_result($fazenda,0,"z01_numero").', '.trim(pg_result($fazenda,0,"z01_bairro"))));
    $this->Text(33,18,trim(pg_result($fazenda,0,"z01_munic"))." - ".pg_result($fazenda,0,"z01_uf").' - CEP: '.pg_result($fazenda,0,"z01_cep"));
    $this->Text(33,22,trim(pg_result($fazenda,0,"z01_telef")));

    $comprim = ($this->w - $this->rMargin - $this->lMargin);
    $Espaco = $this->w - 80 ;
    $this->SetFont('Arial','',7);
    $margemesquerda = $this->lMargin;
    $this->setleftmargin($Espaco);
    $this->sety(6);
    $this->setfillcolor(235);
    $this->roundedrect($Espaco - 3,5,75,28,2,'DF','123');
    $this->line(10,33,$comprim,33);
    $this->setfillcolor(255);
    $this->multicell(0,3,@$GLOBALS["head1"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head2"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head3"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head5"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head6"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head7"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head8"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head9"],0,"J",0);
    $this->setleftmargin($margemesquerda);
    $this->SetY(35);
  }

  function Footer() {
//#00#//footer
//#10#//Este método é usado para criar o rodapé da página. Ele é automaticamente chamado por |addPage|
//#10#//e |close| e não deve ser chamado diretamente pela aplicação. A  implementação  em  FPDF  está
//#10#//vazia, então você  deve  criar  uma  subclasse  e  sobrepor  o  método  se  você  quiser   um
//#10#//processamento específico.
//#15#//footer()
//#99#//Exemplo:
//#99#//class PDF extends FPDF
//#99#//{
//#99#//  function Footer()
//#99#//  {
//#99#//    Vai para 1.5 cm da borda inferior
//#99#//      $this->SetY(-15);
//#99#//    Seleciona Arial itálico 8
//#99#//      $this->SetFont('Arial','I',8);
//#99#//    Imprime o número da página centralizado
//#99#//      $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
//#99#//  }
//#99#//}
  global $conn;
  global $result;
  global $url;
    if($this->imprime_rodape == true) {

    /*
     * Modificação para exibir o caminho do menu
     * na base do relatório
     */
      $sSqlMenuAcess = " select trim(modulo.descricao)||'>'||trim(menu.descricao)||'>'||trim(item.descricao) as menu
                           from db_menu
                          inner join db_itensmenu as modulo on modulo.id_item = db_menu.modulo
                          inner join db_itensmenu as menu on menu.id_item = db_menu.id_item
                          inner join db_itensmenu as item on item.id_item = db_menu.id_item_filho
                          where id_item_filho = ".db_getsession("DB_itemmenu_acessado")."
                            and modulo = ".db_getsession("DB_modulo");

      $rsMenuAcess   = db_query($conn,$sSqlMenuAcess);
      $sMenuAcess    = substr(pg_result($rsMenuAcess, 0, "menu"), 0, 50);

      //Position at 1.5 cm from bottom
      $this->SetFont('Arial','',5);
      $this->text(10,$this->h-8,'Base: '.@$GLOBALS["DB_NBASE"]);
      $this->SetFont('Arial','I',6);
      $this->SetY(-10);
      $nome = @$GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"];
      $nome = substr($nome,strrpos($nome,"/")+1);
      $result_nomeusu = db_query($conn, "select nome as nomeusu from db_usuarios where id_usuario =".db_getsession("DB_id_usuario"));
      if (pg_numrows($result_nomeusu)>0){
        $nomeusu = pg_result($result_nomeusu,0,0);
      }
      if (isset($nomeusu)&&$nomeusu!=""){
        $emissor = $nomeusu;
      }else{
        $emissor = @$GLOBALS["DB_login"];
      }
      $this->Cell(0,10,$sMenuAcess. "  ". $nome.'   Emissor: '.substr(ucwords(strtolower($emissor)),0,30).'  Exerc: '.db_getsession("DB_anousu").
                                                '   Data: '.date("d-m-Y",db_getsession("DB_datausu"))." - ".date("H:i:s"),"T",0,'L');

    }
  }
}

$pdf = new NPDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$total = 0;
$alt = 6;

$vtot_bruto   = 0;
$vtot_imposto = 0;
$vtot_pago    = 0;
$vtot_saldo   = 0;
$total_consi = 0;

//valor a pagar
$vtot_correcao = 0;
$vtot_multa    = 0;
$vtot_juro     = 0;
$vtot_total    = 0;
//-------------------
$result_nomeusu = db_query($conn, "select nome as nomeusu from db_usuarios where id_usuario =".db_getsession("DB_id_usuario"));
if (pg_numrows($result_nomeusu)>0){
  $nomeusu = pg_result($result_nomeusu,0,0);
}
if (isset($nomeusu)&&$nomeusu!=""){
  $emissor = $nomeusu;
}else{
  $emissor = @$GLOBALS["DB_login"];
}

if($pecafiscal == 1){
  $sSqlFis  =  "SELECT y100_sequencial AS codprocfiscal, ";
  $sSqlFis .=  "       y117_levanta AS codlev ";
  $sSqlFis .=  " FROM fiscalizacao.fis_auto ";
  $sSqlFis .=  " INNER JOIN fiscalizacao.fis_tipofiscaliza ON y27_codtipo = y50_codtipo ";
  $sSqlFis .=  " INNER JOIN fiscalizacao.fis_procfiscalauto ON y50_codauto = y111_auto ";
  $sSqlFis .=  " INNER JOIN fiscalizacao.fis_autolevanta ON y50_codauto = y117_auto ";
  $sSqlFis .=  " INNER JOIN fiscalizacao.fis_procfiscal ON y111_procfiscal = y100_sequencial ";
  $sSqlFis .=  " INNER JOIN fiscalizacao.fis_autoinscr ON y50_codauto = y52_codauto ";
  $sSqlFis .=  " WHERE y100_sequencial IN ($procfiscalini) order by y50_codauto ";
  $sTipo     = "Auto de Infração: ";
} else if($pecafiscal == 2){ //NOTIF LANCAMENTO
  $sSqlFis  =  "select y100_sequencial as codprocfiscal, nl15_levanta as codlev from fiscalizacao.fis_lancamento ";
  $sSqlFis .=  " inner join fiscalizacao.fis_tipofiscaliza on fis_tipofiscaliza.y27_codtipo = fis_lancamento.nl01_codtipo ";
  $sSqlFis .=  " inner join fiscalizacao.fis_fisdoc on fd01_codtipo = fis_tipofiscaliza.y27_codtipo";
  $sSqlFis .= " inner join fiscalizacao.fis_procfiscallanc on nl01_codlanc = nl09_lanc ";
  $sSqlFis .= " inner join fiscalizacao.fis_lanclevanta on nl01_codlanc = nl15_lancamento ";
  $sSqlFis .= " inner join fiscalizacao.fis_procfiscal on nl09_procfiscal = y100_sequencial ";
  $sSqlFis .= " inner JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = nl01_codlanc ";
  $sSqlFis .= " where fd01_instit = ".db_getsession("DB_instit")." and y100_sequencial IN ($procfiscalini) order by nl01_codlanc";
  $sTipo     = "Notificação de Lancamento: ";
}

$ResulFis  =  db_query($sSqlFis);

for ($iProcs = 0; $iProcs  <  pg_num_rows($ResulFis); $iProcs ++) {
  db_fieldsmemory($ResulFis,$iProcs);
//-------------------

$joins = '';
$where = '';
if ((isset($codlev) and $codlev != '') and (isset($codprocfiscal) and $codprocfiscal != '')) {
  $joins .= " INNER JOIN fiscalizacao.fis_procfiscallevanta ON y112_levanta = y60_codlev INNER JOIN fiscalizacao.fis_procfiscal ON y112_procfiscal = y100_sequencial ";
  $where .= " fis_levanta.y60_codlev = $codlev and fis_procfiscal.y100_sequencial = $codprocfiscal ";
} elseif (isset($codlev) and $codlev != '') {
  $where .= " fis_levanta.y60_codlev = $codlev ";
} elseif (isset($codprocfiscal) and $codprocfiscal != '') {
  $joins .= " INNER JOIN fiscalizacao.fis_procfiscallevanta ON y112_levanta = y60_codlev INNER JOIN fiscalizacao.fis_procfiscal ON y112_procfiscal = y100_sequencial ";
  $where .= " fis_procfiscal.y100_sequencial = $codprocfiscal ";
}

$result = $cllevanta->sql_record($cllevanta->getLevantamentos('*', $joins, $where));
$numrows = $cllevanta->numrows;
if ($numrows>0) {
  db_fieldsmemory($result,0,true);
}
if ($numrows==0) {
  db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum registro encontrado.");
}

$result  = $cllevinscr->sql_record($cllevinscr->sql_query($codlev,"q02_inscr,z01_nome,z01_ender,z01_numero,z01_compl,z01_bairro"));
$numrows = $cllevinscr->numrows;

if ($numrows>0) {

  db_fieldsmemory($result,0);

  $result  = $clativprinc->sql_record($clativprinc->sql_query_compl($q02_inscr,"q03_descr"));
  $numrows = $clativprinc->numrows;
  if ($numrows>0) {
    db_fieldsmemory($result,0);
  }
  $head1 = "Inscrição: $q02_inscr";
  $head2 = "Nome: $z01_nome";
  $sSqlEndInscrLevant = "SELECT j14_nome||', '||nl02_numero||', '||nl02_compl||', '||j13_descr as endereco from fiscalizacao.fis_lanclocal inner join fiscalizacao.fis_lancamento ON nl02_codlanc = nl01_codlanc inner join fiscalizacao.fis_lanclevanta on nl15_lancamento = nl01_codlanc LEFT JOIN ruas ON j14_codigo = nl02_codigo LEFT JOIN bairro ON j13_codi = nl02_codi WHERE nl15_levanta = $codlev
UNION ALL
SELECT j14_nome||', '||y14_numero||', '||y14_compl||', '||j13_descr as endereco from fiscalizacao.fis_autolocal inner join fiscalizacao.fis_auto ON y14_codauto = y50_codauto inner join fiscalizacao.fis_autolevanta on y117_auto = y50_codauto LEFT JOIN ruas ON j14_codigo = y14_codigo LEFT JOIN bairro ON j13_codi = y14_codi WHERE y117_levanta = $codlev";
  $rsSqlEndLevant = db_query($sSqlEndInscrLevant);
  db_fieldsmemory($rsSqlEndLevant,0);

  $head3 = "Endereço: ".$endereco;

} else {

  $result  = $cllevcgm->sql_record($cllevcgm->sql_query($codlev,"z01_nome,z01_ender"));
  db_fieldsmemory($result,0);

  $head2 = "Nome: $z01_nome";
  $head3 = "Endereço: $z01_ender";
}

$head5 = "Levantamento fiscal: $codlev Data:$y60_data";
$head6 = "Período: $y60_dtini a $y60_dtfim";
//-------------------
$result_auto = db_query($conn, "select nl15_lancamento from fiscalizacao.fis_lanclevanta where nl15_levanta = $codlev
 union all select y117_auto from fiscalizacao.fis_autolevanta where y117_levanta = $codlev ");

if (pg_numrows($result_auto)>0){
  $auto = pg_result($result_auto,0,0);
}
$head7 = $sTipo.$auto;
//-----------------

$dtCiencia = null;
$sqlAndamento = "select data_ciencia from fiscalizacao.fis_fandam inner join fiscalizacao.fis_datacienciaandamento on fis_datacienciaandamento.fandam = y39_codandam";

if (pg_numrows($result_auto) > 0) {
    $auto = pg_result($result_auto, 0, 0);
    $head7 = "Notificação: $auto";
    $sqlAndamento .= " inner join fiscalizacao.fis_lancandam on nl19_codandam  =  y39_codandam where  nl19_codlanc = {$auto}";
    $rsAndamento = db_query($sqlAndamento);
    if( pg_num_rows($rsAndamento) > 0 ) {
        db_fieldsmemory($rsAndamento , 0);
        $dtCiencia = $data_ciencia;
    }
}

$result_auto = db_query($conn, "select y117_auto from fiscalizacao.fis_autolevanta where y117_levanta = $codlev");
if (pg_numrows($result_auto) > 0) {
    $auto = pg_result($result_auto, 0, 0);
    $head7 = "Auto: $auto";
    $sqlAndamento .= " inner join fiscalizacao.fis_autoandam on y58_codandam  =  y39_codandam where   y58_codauto = {$auto}";
    $rsAndamento = db_query($sqlAndamento);
    if( pg_num_rows($rsAndamento) > 0 ) {
        db_fieldsmemory($rsAndamento , 0);
        $dtCiencia = $data_ciencia;
    }
}

$pdf->addpage("L");

$result01  =  $cllevvalor->sql_record($cllevvalor->sql_query_file(null,"distinct y63_sequencia,y63_mes,y63_ano","y63_ano asc, y63_mes asc","y63_codlev=$codlev"));
$numrows01 =  $cllevvalor->numrows;
$sPrenche1 = "B";
$sPrenche2 = "BR";
$sPrenche3 = "RB";
$sPrenche  = "TRLB";

  $vtot_bruto    = 0;
  $vtot_imposto  = 0;
  $vtot_pago     = 0;
  $vtot_saldo    = 0;
  $total_consi   = 0;
  $vtot_correcao = 0;
  $vtot_multa    = 0;
  $vtot_juro     = 0;
  $vtot_total    = 0;

for ($x=0; $x<$numrows01; $x++) {



  db_fieldsmemory($result01,$x);
  if ($pdf->gety() < $pdf->h - 50) {
    $competencia = $y63_mes."/".$y63_ano;
  }

  $sql =  $cllevvalor->sql_query_file("","y63_sequencia,y63_mes,y63_ano,y63_codlev,y63_sequencia,y63_bruto,y63_aliquota,(select sum(yl63_pagoriginal) from fiscalizacao.fis_valordefla where yl63_sequencia =y63_sequencia )as y63_pago, y63_pago as y68_valor,y63_saldo,y63_dtvenc, y63_histor,(y63_pago+y63_saldo) as y63_apagar
                ,(select case when count(y68_pgto)> 1 then null  else (select y68_pgto from fiscalizacao.fis_levvalorpgtos where y68_sequencia = y63_sequencia limit 1) end as teste from fiscalizacao.fis_levvalorpgtos  where y68_sequencia = y63_sequencia) as y68_pgto"," y63_ano,y63_mes","y63_sequencia=$y63_sequencia  and y63_codlev =$codlev");
  $result  = $cllevvalor->sql_record($sql);
  $numrows = $cllevvalor->numrows;
  if ($numrows < 1) {
    continue;
  }
  $tot_valor   = 0;
  $tot_imposto = 0;


  for ($i=0; $i<$numrows; $i++) {

    db_fieldsmemory($result,$i);
    if ($y79_codigo=="") {
      $y79_valor = $y63_bruto;
    }

    $imposto = ($y63_aliquota*$y79_valor)/100;
    $pdf->setfont('arial','b',8);
    if ($pdf->gety() > $pdf->h - 50 || $i==0) {

      if ($pdf->gety() > $pdf->h - 50) {
        //-----------------
        $pdf->ln(2);
        $pdf->cell(139,5, "Recebido em: ____/____/_______", 0,0,"C",0);
        $pdf->cell(139,5, "", 0,1,"C",0);
        $pdf->ln(1);
        $pdf->cell(139,5, "______________________________________________________________", 0,0,"C",0);
        $pdf->cell(139,5, "______________________________________________________________", 0,1,"C",0);
        $pdf->cell(139,2, "Assinatura", 0,0,"C",0);
        $pdf->cell(139,2, $emissor, 0,1,"C",0);
        //-----------------
        $pdf->addpage("L");
        $pdf->setfont('arial','',7);
        $competencia = $y63_mes."/".$y63_ano;
        $pdf->setfont('arial','b',8);

        $pdf->cell(25,$alt,"Competência"     ,"BTL",0,"C",1);
        $pdf->cell(25,$alt,"Valor bruto",     "BT",0,"C",1);
        $pdf->cell(5 ,$alt,"Alí", "BT",0,"C",1);
        $pdf->cell(25,$alt,"Imposto",         "BT",0,"C",1);
        $pdf->cell(20,$alt,"Vencimento",      "BT",0,"C",1);
        $pdf->cell(20,$alt,"Valor pago",      "BT",0,"C",1);
        $pdf->cell(25,$alt,"Dt. do Pagamento","BT",0,"C",1);
        $pdf->cell(25,$alt,"Vlr. Pgt Desc.",  "BT",0,"C",1);
        $pdf->cell(25,$alt,"Valor à pagar",   "BT",0,"C",1);
        $pdf->cell(25,$alt,"Valor corrigido", "BT",0,"C",1);
        $pdf->cell(20,$alt,"Multa",           "BT",0,"C",1);
        $pdf->cell(20,$alt,"Juros",           "BT",0,"C",1);
        $pdf->cell(25,$alt,"Valor total",     "BTR",1,"C",1);
      }

      // Imprimir cabeçalho na primeira folha
      if($x == 0){
        $pdf->cell(25,$alt,"Competência"     ,"BTL",0,"C",1);
        $pdf->cell(25,$alt,"Valor bruto",     "BT",0,"C",1);
        $pdf->cell(5 ,$alt,"Alí", "BT",0,"C",1);
        $pdf->cell(25,$alt,"Imposto",         "BT",0,"C",1);
        $pdf->cell(20,$alt,"Vencimento",      "BT",0,"C",1);
        $pdf->cell(20,$alt,"Valor pago",      "BT",0,"C",1);
        $pdf->cell(25,$alt,"Dt. do Pagamento","BT",0,"C",1);
        $pdf->cell(25,$alt,"Vlr. Pgt Desc.",  "BT",0,"C",1);
        $pdf->cell(25,$alt,"Valor à pagar",   "BT",0,"C",1);
        $pdf->cell(25,$alt,"Valor corrigido", "BT",0,"C",1);
        $pdf->cell(20,$alt,"Multa",           "BT",0,"C",1);
        $pdf->cell(20,$alt,"Juros",           "BT",0,"C",1);
        $pdf->cell(25,$alt,"Valor total",     "BTR",1,"C",1);
      }
    }

    $tot_imposto +=$imposto;
    $tot_valor   +=$y79_valor;
  }

  //correções
  if ($numrows > 0) {
    $result66 = $clparfiscal->sql_record($clparfiscal->sql_query_file(db_getsession('DB_instit'), "*"));
    db_fieldsmemory($result66, 0);

    $dtoper = date("Y-m-d", db_getsession("DB_datausu"));
    if (!empty($dtCiencia)) {
        $dtoper = $dtCiencia;
    }
    $aData = explode("-", $y63_dtvenc);
    $dDtOperData = $aData[0] . "-" . $aData[1] . "-01";

    $result = db_query("select round(fc_corre(" . ($y60_espontaneo == 't' ? $y32_receitexp : $y32_receit) . ",'" . $y63_dtvenc . "'," . $y63_saldo . ",'" . $dtoper . "'," . db_getsession("DB_anousu") . ",'$y63_dtvenc'),2) as correcao");
    db_fieldsmemory($result, 0);


    $result = db_query("select round(fc_juros(" . ($y60_espontaneo == 't' ? $y32_receitexp : $y32_receit) . ",'" . $y63_dtvenc . "','" . $dtoper . "','" . $dDtOperData . "','f'," . db_getsession("DB_anousu") . " , 0),2) as juro");
    db_fieldsmemory($result, 0);
    $juro = round($correcao * $juro, 4);

    $result = db_query("select round(fc_multa(" . ($y60_espontaneo == 't' ? $y32_receitexp : $y32_receit) . ",'" . $y63_dtvenc . "','" . $dtoper . "','" . $dDtOperData . "'," . db_getsession("DB_anousu") . "),2) as multa");
    db_fieldsmemory($result, 0);

    $multa = round($correcao * $multa, 4);

    $total = round($correcao + $juro + $multa, 4);
  } else {
    $multa = '0.00';
    $correcao = '0.00';
    $juro = '0.00';
    $total = '0.00';
    $y63_saldo = '0.00';
  }

  $pdf->setfont('arial','',7);
  $pdf->cell(25,$alt,$competencia,                            "$sPrenche",0,"C",1);
  $pdf->cell(25,$alt,'R$ '.trim(db_formatar($y79_valor,"f")), "$sPrenche1",0,"C",0);
  $pdf->cell(5 ,$alt,"$y63_aliquota",                         "$sPrenche1",0,"C",0);
  $pdf->cell(25,$alt,'R$ '.trim(db_formatar($imposto,"f")),   "$sPrenche1",0,"R",0);

  $pdf->cell(20,$alt,trim(db_formatar($y63_dtvenc, "d")),     "$sPrenche1",0,"R",0);
  $pdf->cell(20,$alt,'R$ '.trim(db_formatar($y63_pago,"f")),  "$sPrenche1",0,"R",0);
  $pdf->cell(22,$alt,trim(db_formatar($y68_pgto,"d")),        "$sPrenche1",0,"R",0);
  $pdf->cell(25,$alt,'R$ '.trim(db_formatar($y68_valor,"f")), "$sPrenche1",0,"R",0);
  $pdf->cell(25,$alt,'R$ '.trim(db_formatar($y63_saldo, "f")),"$sPrenche1",0,"R",0);
  $pdf->cell(25,$alt,'R$ '.trim(db_formatar($correcao,"f")),  "$sPrenche1",0,"R",0);
  $pdf->cell(20,$alt,'R$ '.trim(db_formatar($multa,"f")),     "$sPrenche1",0,"R",0);
  $pdf->cell(20,$alt,'R$ '.trim(db_formatar($juro,"f")),      "$sPrenche1",0,"R",0);
  $pdf->cell(28,$alt,'R$ '.trim(db_formatar($total,"f")),     "$sPrenche3",1,"R",0);

  if($troca == 1){
    $troca = 2;
    $sPrenche1 = "B";
    $sPrenche2 = "BR";
    $sPrenche3 = "RB";
    $sPrenche  = "TRLB";
  }
  $result2 = db_query("select y68_seq as seq ,y68_valor as vlrpag, yl63_pagoriginal as vlrpagor, y68_pgto as dtpaga from fiscalizacao.fis_levvalor inner join fiscalizacao.fis_levvalorpgtos on y63_sequencia = y68_sequencia inner join fiscalizacao.fis_valordefla on yl63_sequencia = y68_sequencia and y68_seq = yl63_seq where y63_sequencia=$y63_sequencia  and y63_codlev =$codlev");
  $numrows2 = pg_num_rows($result2);

  if($numrows2 > 1){
      $pdf->setfont('arial','',7);
      for ($y=0; $y < $numrows2 ; $y++) {
        $pdf->SetX(90);
        db_fieldsmemory($result2,$y);
        $pdf->cell(20,$alt,"Pagamentos",                           "TRLB",0,"C",1);
        $pdf->cell(20,$alt,'R$ '.trim(db_formatar($vlrpagor,"f")) ,"B",0,"R",0);
        $pdf->cell(22,$alt,trim(db_formatar($dtpaga,"d")),  "B",0,"R",0);
        $pdf->cell(25,$alt,'R$ '.trim(db_formatar($vlrpag ,"f")) ,"BR",1,"R",0);

      }
      $sPrenche1 = "BT";
      $sPrenche2 = "RLB";
      $sPrenche3 = "BRT";
      $troca = 1;
  }
  if($y63_histor != ""){
      $pdf->setfont('arial','',7);
      $pdf->SetX(35);
      $pdf->cell(25,$alt,"Descrição: ",                           "TRLB",0,"C",1);
      $pdf->MultiCell(235,$alt,$y63_histor ,"TRLB","L");

      $sPrenche1 = "BT";
      $sPrenche2 = "RLB";
      $sPrenche3 = "BRT";
      $troca = 1;
  }
  $pdf->setfont('arial','',7);

  $vtot_bruto   += $tot_valor;
  $vtot_imposto += $tot_imposto;
  $vtot_pago    += $y63_pago;
  $vtot_saldo   += $y63_saldo;
  $total_consi  += $y68_valor;
  //valor a pagar
  $vtot_correcao += $correcao;
  $vtot_multa    += $multa;
  $vtot_juro     += $juro;
  $vtot_total    += $total;
}

$pdf->Ln(5);

// imprime o total geral
$pdf->setfont('arial','b',7);
$pdf->cell(30,$alt,"TOTAL ",                              "T",0,"C",0);
$pdf->cell(20,$alt,'R$ '.trim(db_formatar($vtot_bruto,"f")),   "T",0,"C",0);
$pdf->cell(10,$alt,"",                                         "T",0,"C",0);
$pdf->cell(25,$alt,'R$ '.trim(db_formatar($vtot_imposto,"f")), "T",0,"C",0);
$pdf->cell(19,$alt,"",                                         "T",0,"C",0);
$pdf->cell(20,$alt,'R$ '.trim(db_formatar($vtot_pago,"f")),    "T",0,"C",0);
$pdf->cell(25,$alt,"",                                         "T",0,"C",0);
$pdf->cell(22,$alt,'R$ '.trim(db_formatar($total_consi,"f")),  "T",0,"C",0);

$pdf->cell(25,$alt,'R$ '.trim(db_formatar($vtot_saldo,"f")),   "T",0,"C",0);
$pdf->cell(25,$alt,'R$ '.trim(db_formatar($vtot_correcao,"f")),"T",0,"C",0);
$pdf->cell(20,$alt,'R$ '.trim(db_formatar($vtot_multa,"f")),   "T",0,"C",0);
$pdf->cell(25,$alt,'R$ '.trim(db_formatar($vtot_juro,"f")),    "T",0,"C",0);
$pdf->cell(25,$alt,'R$ '.trim(db_formatar($vtot_total,"f")),   "T",1,"C",0);

$pdf->ln(3);

$result11  = $cllevusu->sql_record($cllevusu->sql_query($codlev,null,"nome"));

$numrows11 = $cllevusu->numrows;
if ($numrows11>0) {

  $pdf->setfont('arial','b',8);
  $pdf->cell(100,$alt,"FISCAIS:",1,1,"L",1);
  $pdf->setfont('arial','',8);

  $pdf->cell(100,$alt,"ALEXANDRE CORREA GRASSI BISSACOT",1,1,"L",0);
}

//-----------------
$pdf->SetY(177);
$pdf->cell(139,5, "Recebido em: ____/____/_______", 0,0,"C",0);
$pdf->cell(139,5, "", 0,1,"C",0);
$pdf->ln(1);
$pdf->cell(139,4, "______________________________________________________________", 0,0,"C",0);
$pdf->cell(139,4, "______________________________________________________________", 0,1,"C",0);
$pdf->cell(139,2, "Assinatura", 0,0,"C",0);
$pdf->cell(139,2, $emissor, 0,1,"C",0);
//-----------------

}
$pdf->Output();
