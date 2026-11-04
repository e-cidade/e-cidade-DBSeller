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

require_once(modification("libs/db_sql.php"));
require_once(modification("fpdf151/pdf1.php"));
require_once(modification("libs/db_libsys.php"));
require_once(modification("dbagata/classes/core/AgataAPI.class"));
require_once(modification("model/documentoTemplate.model.php"));

$cldb_docparag = db_utils::getDao('db_docparag');
$cllancamento  = db_utils::getDao("fis_lancamento");
$cllancfiscal  = db_utils::getDao("fis_lancfiscal");
$cllancusu     = db_utils::getDao("fis_lancusu");
$cllanctipo    = db_utils::getDao("fis_lanctipo");
$cllancnumpre  = db_utils::getDao("fis_lancnumpre");

$clfiscal      = db_utils::getDao("fis_fiscal");
$oDaoParfiscal = db_utils::getDao("fis_parfiscal");

$clrotulo      = new rotulocampo;

$clrotulo->label('nl01_codlanc');
$clrotulo->label('nl01_nome');

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

/**
 * Verifica se existe documento template setado para utilizar o agata
 */
$iCodigoNotiLancamento = $codlanc;
$sSqlParfiscal         = $oDaoParfiscal->sql_query_file(db_getsession("DB_instit"), "y32_templateautoinfracao");

$rsParfiscal           = $oDaoParfiscal->sql_record($sSqlParfiscal);

$iCodigoProcessoFiscal = $codprocfiscal;

$sSqlFis   =  "select * from fiscalizacao.fis_lancamento ";
$sSqlFis  .=  " inner join fiscalizacao.fis_tipofiscaliza on fis_tipofiscaliza.y27_codtipo = fis_lancamento.nl01_codtipo "; 
$sSqlFis  .=  " inner join fiscalizacao.fis_fisdoc on fd01_codtipo = fis_tipofiscaliza.y27_codtipo"; 

$andWhere = '';
if (isset($iCodigoProcessoFiscal) and $iCodigoProcessoFiscal != '') {
  $sSqlFis .= " inner join fiscalizacao.fis_procfiscallanc on nl01_codlanc = nl09_lanc ";
  $sSqlFis .= " inner join fiscalizacao.fis_procfiscal on nl09_procfiscal = y100_sequencial ";
  $andWhere = " and y100_sequencial = {$iCodigoProcessoFiscal} ";
}

$sSqlFis  .=  " where nl01_codlanc = ".$iCodigoNotiLancamento." and fd01_instit = ".db_getsession("DB_instit").$andWhere;

$ResulFis  =  db_query($sSqlFis);

if(pg_num_rows($ResulFis) > 0){
  db_fieldsmemory($ResulFis,0);  
  $iTemplateNotiLancamento = $fd01_doc;
}else{
  $iTemplateNotiLancamento = db_utils::fieldsMemory($rsParfiscal, 0)->y32_templateautoinfracao;
}

if ($iTemplateNotiLancamento != "") {


  $sDescrDoc        = date("YmdHis").db_getsession("DB_id_usuario");
  $sNomeRelatorio   = "tmp/CertidaoIsencao{$iCodigoNotiLancamento}.pdf";
  $sCaminhoSalvoSxw = "tmp/CertidaoIsencao_{$sDescrDoc}_{$iCodigoNotiLancamento}.sxw";
  $sAgt = "fiscal/fis_notificacao_de_lancamento.agt";

  if (!file_exists('dbagata/reports/'.$sAgt)){
     db_redireciona('db_erros.php?fechar=true&db_erro=Não encontrou o arquivo de configuração fis_notificacao_de_lancamento.agt.');
  }

  $aParam = array();
  $aParam['$nl01_codlanc'] = $iCodigoNotiLancamento;

  db_stdClass::oo2pdf(6000, $iTemplateNotiLancamento, $sAgt, $aParam, $sCaminhoSalvoSxw, $sNomeRelatorio);
  exit;
}

/**
 * Executa o padrão da rotina
 */
$pdf = new PDF1();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$pdf->Addpage();
$pdf->setfont('arial','b',8);

//die($cllancamento->sql_query_info($codlanc));
//--------------Retorna dados do Notificação de Lançamento---------------------------
$result_infolanc=$cllancamento->record($cllancamento->sql_query_info($codlanc));
if ($cllancamentoows>0){

  db_fieldsmemory($result_infolanc,0);
  $data = explode('-',$data);
  $dia  = $data[2];
  $mes  = $data[1];
  $ano  = $data[0];
  $mes  = db_mes($mes);
  $data = " $dia de $mes de $ano ";
  $prazo_recurso=db_formatar($prazo_recurso,'d');
}
$result_lancfiscal=$cllancfiscal->sql_record($cllancfiscal->sql_query($codlanc));
if ($cllancfiscal->numrows > 0) {

  db_fieldsmemory($result_lancfiscal, 0);
  $result_fiscal=$clfiscal->sql_record($clfiscal->sql_query_busca($y51_codnoti));
  if ($clfiscal->numrows > 0) {

    db_fieldsmemory($result_fiscal, 0);
    $bql = $j34_setor . "/" . $j34_quadra . "/" . $j34_lote;
    $nome = $z01_nome;
  }
}

//----------------------Traz valor da Notificação----------------------------------
$sql          = $cllancnumpre->sql_query_paga($codlanc);
$result_valor = $cllancnumpre->sql_record($sql);

if ($cllancnumpre->numrows > 0){

  db_fieldsmemory($result_valor,0,true);
  $extenso = db_extenso($valor,false);
  $valor   = trim(db_formatar($valor,'f'));
}else{

  db_msgbox('Notificação de Lançamento não Calculado');
  exit;
}
//----------------------Busca Fiscais---------------------------------------
$result_fiscal=$cllancusu->sql_record($cllancusu->sql_query($codlanc,null,"nl14_obs as obsfiscal, nome as fisc"));
$numrows_fiscal=$cllancusu->numrows;
$vir="";
$fiscal="";
for($f=0;$f<$numrows_fiscal;$f++){

  db_fieldsmemory($result_fiscal,$f,true);
  $fiscal .= $vir." $fisc";
  $vir     = ",";
}

//----------------------Busca Procedências----------------------------------
$result_proc=$cllanctipo->sql_record($cllanctipo->sql_query(null,"y29_descr, y29_descr_obs",null,"nl18_codlanc=$codlanc"));
$numrows_proc=$cllanctipo->numrows;
$vir="";
$procedencia="";
$obsprocedenciacomquebra="";
for($p=0;$p<$numrows_proc;$p++){

  db_fieldsmemory($result_proc,$p,true);
  $procedencia.=$vir." $y29_descr";
  $obsprocedenciacomquebra.="$y29_descr_obs\n";
  $vir=",";
}

$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(220);
$result = $cldb_docparag->sql_record($cldb_docparag->sql_query(null,null,"db_docparag.*,db02_texto,db02_espaca,db02_alinha,db02_inicia","db04_ordem"," db_documento.db03_tipodoc = 1100"));
$numrows = $cldb_docparag->numrows;
$pdf->SetXY('10','60');
$pdf->SetFont('Arial','b',14);
$pdf->cell(0,10,"NOTIFICAÇÂO DE LANÇAMENTO E IMPOSIÇÃO DE MULTA N° $codlanc",0,1,"C",0);
$pdf->cell(0,10,"",0,1,"R",0);

for($i=0; $i<$numrows; $i++){

 db_fieldsmemory($result,$i);
 $pdf->SetFont('Arial','',12);
 $pdf->SetX($db02_alinha);
 $texto=db_geratexto($db02_texto);
 $pdf->SetFont('Arial','',12);
 $pdf->MultiCell("0",4+$db02_espaca,$texto,"0","J",0,$db02_inicia+0);
 $pdf->cell(0,6,"",0,1,"R",0);
}

$pdf->cell(0,10,"",0,1,"R",0);
$pdf->SetFont('Arial','b',12);
$pdf->cell(90,4,"___________________________",0,0,"C",0);
$pdf->cell(90,4,"___________________________",0,1,"C",0);
$pdf->cell(90,4,"Fiscal",0,0,"C",0);
$pdf->cell(90,4,"Autuado",0,1,"C",0);
$pdf->cell(0,10,"",0,1,"R",0);
$pdf->cell(0,4,"Data:___/___/_____ ",0,1,"R",0);
$pdf->Output();
