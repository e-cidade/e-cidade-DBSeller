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
require_once(modification("fpdf151/assinatura.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification('libs/db_utils.php'));

db_postmemory($_GET);
db_postmemory($HTTP_GET_VARS);

/**
 * Quando o relatório for uma reemissão, busca o código sequencial da tabela concilia.
 */
$processa_geral = false;

$clconcilia             = new cl_concilia();

$clextrato              = new cl_extrato();
$clsaltes               = new cl_saltes();

$classinatura           = new cl_assinatura;

db_sel_instit();

$sSqlCodConcilia  = "select * ";
$sSqlCodConcilia .= "  from concilia";
$sSqlCodConcilia .= "       inner join contabancaria on db83_sequencial = k68_contabancaria ";
$sSqlCodConcilia .= "       inner join conplanocontabancaria on c56_contabancaria = db83_sequencial and conplanocontabancaria.c56_anousu = " . db_getsession('DB_anousu');
$sSqlCodConcilia .= "       inner join bancoagencia on db83_bancoagencia = db89_sequencial ";
$sSqlCodConcilia .= " where k68_contabancaria = {$iConta} ";
$sSqlCodConcilia .= "   and k68_data = '{$sDataIniConciliacao}' ";
$sSqlCodConcilia .= " order by k68_data ";

$rsCodigoConcilia = db_query($sSqlCodConcilia);
if (pg_num_rows($rsCodigoConcilia) == 0){
    db_redireciona("db_erros.php?fechar=true&db_erro=Conta ($iConta) sem conciliação.");
}

db_fieldsmemory($rsCodigoConcilia,0);

$head3 =  "Relatório dos dados conciliados";
$head4 =  "Data Inicial: ".db_formatar($sDataIniConciliacao,'d');
$head5 =  "Data Final  : ".db_formatar($sDataFimConciliacao,'d');

//-------------------------------------------------------

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$alt                    = 4;
$fonte                  = 'arial';
$pagina = 0;
$pdf->addpage();

$pdf->setfont($fonte,'',8);

$pdf->cell(189,$alt,"DADOS DA CONTA BANCÁRIA ",0,1,"L",0);

$pdf->cell(189,$alt,"BANCO : $db89_db_bancos - Agência: $db89_codagencia - $db89_digito CONTA: $db83_conta - $db83_dvconta ",0,1,"L",0);

/* variaveis dos saldos  */
$saldo_tesouraria = 0;
$saldo_extrato = 0;
$quais_reduzidos = "(";

$sSqlReduz  = " select c61_reduz c61_reduz , (select case when k97_situacao = 'D' then k97_saldofinal * -1 else k97_saldofinal end as k97_saldofinal from extratosaldo where k97_contabancaria = $iConta and k97_dtsaldofinal = '$sDataFimConciliacao') as saldo_extrato";
$sSqlReduz .= "   from contabancaria ";
$sSqlReduz .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria = contabancaria.db83_sequencial and conplanocontabancaria.c56_anousu = " . db_getsession('DB_anousu');
$sSqlReduz .= "        inner join conplanoreduz         on conplanoreduz.c61_codcon = conplanocontabancaria.c56_codcon ";
$sSqlReduz .= "                                        and conplanoreduz.c61_anousu = conplanocontabancaria.c56_anousu ";
$sSqlReduz .= "                                        and conplanoreduz.c61_anousu = ".db_getsession('DB_anousu');
$sSqlReduz .= "                                        and conplanoreduz.c61_instit = ".db_getsession('DB_instit');
$sSqlReduz .= "  where contabancaria.db83_sequencial = {$iConta} ";
$rsReduz    = db_query($sSqlReduz);

if( $rsReduz && pg_num_rows($rsReduz) > 0 ) {

  for ($i = 0; $i <  pg_num_rows($rsReduz); $i++) {

    db_fieldsmemory($rsReduz,$i);

    $quais_reduzidos .= ($quais_reduzidos =='('?'':',').$c61_reduz;

    $sqlSaldoContaCaixa = "select substr(fc_saltessaldo(".$c61_reduz.",'".$sDataFimConciliacao."','".$sDataFimConciliacao."',null,".db_getsession('DB_instit')."),41,13)::float as saldocontacaixa";
    $rsSaldoContaCaixa  = db_query($sqlSaldoContaCaixa);

    if ( pg_num_rows($rsSaldoContaCaixa) > 0) {
      db_fieldsmemory($rsSaldoContaCaixa,0);
      $saldo_tesouraria += $saldocontacaixa;
    }
  }
}
$quais_reduzidos .= ')';
$sql = " select round(sum(k86_valor),2) as saldo_pendencias from (
           select case when k86_tipo = 'C' then k86_valor else k86_valor * -1 end as k86_valor
        from conciliapendextrato 
             inner join concilia on k88_concilia = k68_sequencial
             inner join extratolinha on k86_sequencial = k88_extratolinha 
             inner join extrato on k85_sequencial = k86_extrato 
	where k86_contabancaria = $iConta and k68_data = '$sDataFimConciliacao' and k86_bancohistmov = 1
       ) as x
       ";

$rsSaldoContaCaixa  = db_query($sql);

if ( pg_num_rows($rsSaldoContaCaixa) > 0) {
   db_fieldsmemory($rsSaldoContaCaixa,0);
}
 
$saldo_conciliacao = $saldo_extrato + ( $saldo_pendencias ) ;

$saldo_pendencias = 0;

$sql = "select round(sum(k86_valor),2) as saldo_pendencias from (

        select case when k86_tipo = 'C' then k86_valor * -1 else k86_valor end as k86_valor
	from conciliapendextrato 
             inner join concilia on k88_concilia = k68_sequencial
             inner join extratolinha on k86_sequencial = k88_extratolinha 
             inner join extrato on k85_sequencial = k86_extrato 
	where k86_contabancaria = $iConta and k68_data = '$sDataFimConciliacao' and k86_bancohistmov = 2

	union all

        --select round(sum(valor),2) as valor
        select round(sum(case when tipo = 'cheque' or tipo = 'credito' then valor else valor * -1 end),2) as valor
          from (
         select 
max( case when richeque is not null and richeque <> 0 and rivalorcredito <> 0 then 'cheque' 
          when rnvalordebito is not null and rnvalordebito <> 0 or richeque is not null and richeque <> 0 and rnvalordebito <> 0 then 'debito' 
	  when rivalorcredito is not null and rivalorcredito <> 0 then 'credito' 
     end) as tipo, 
ricaixa, 
riautent, 
ridata, 
(select e60_codemp||'/'||e60_anousu from empempenho where e60_numemp = riempenho ) as riempenho,
 riordem,
 riplanilha,
 rislip,
 richeque as cheque,
 max(case when rnvalordebito is not null and rnvalordebito <> 0 then 'D' else 'C' end) as tipomov, 
-- sum( rnvalordebito - rivalorcredito ) as valor, 
sum(case when rnvalordebito is not null and rnvalordebito <> 0 then rnvalordebito else rivalorcredito end) as valor, 

k89_justificativa 
from conciliapendcorrente 
     inner join concilia on k89_concilia = k68_sequencial
     inner join fc_extratocaixa(".db_getsession('DB_instit').",$iConta,null,null,false ) on ricaixa = k89_id 
                      and riautent = k89_autent and ridata = k89_data 
     where k68_contabancaria = $iConta  and k68_data = '$sDataFimConciliacao'
       and not exists (select 1 from corgrupocorrente where k105_autent = k89_autent and k105_id = k89_id and k105_data = k89_data and k105_corgrupotipo in (2,3,5,6) 
       and extract(year from k105_data) <= 2012 ) 
group by ricaixa, 
riautent, 
ridata, 
riempenho, 
riordem, 
riplanilha, 
rislip, 
richeque,
k89_justificativa

) as x
	) as x";

$rsSaldoContaCaixa  = db_query($sql);

if ( pg_num_rows($rsSaldoContaCaixa) > 0) {
   db_fieldsmemory($rsSaldoContaCaixa,0);
}
$saldo_conciliacao -=  ( $saldo_pendencias ) ;


$pdf->cell(189,$alt,"DATA FINAL : ".db_formatar($sDataFimConciliacao,'d'),0,1,"L",0);

$pdf->cell(30,$alt,"Saldo Tesouraria  : ",0,0,"L",0);
$pdf->cell(30,$alt,db_formatar($saldo_tesouraria,'f'),0,0,"L",0);
$pdf->cell(30,$alt,"Saldo Conciliação : ",0,0,"L",0);
$pdf->cell(30,$alt,db_formatar($saldo_conciliacao,'f'),0,0,"L",0);
$pdf->cell(30,$alt,"Diferença : ",0,0,"L",0);
$pdf->cell(30,$alt,db_formatar($saldo_tesouraria-$saldo_conciliacao,'f'),0,1,"L",0);

$sSqlCodConcilia = "
select k68_data,
       case when rtdetalhe is null then k86_historico  else translate(rtdetalhe,'#',' ') end as k86_historico,
       case when riempenho  > 0 then 'Empenho: '||riempenho
            when riordem    > 0 then 'Ordem: '||riordem 
	    when riplanilha > 0 then 'Planilha: '||riplanilha
	    when rislip > 0     then 'Slip: '||rislip
       else
         k86_documento	
       end as k86_documento,
       case when rnvalordebito > 0 then rnvalordebito
            when rivalorcredito > 0 then rivalorcredito * -1
       else 
          case when k86_tipo = 'D' then k86_valor else k86_valor * -1 end
       end as k86_valor,
       case when conciliacor.k84_id is not null then 'Tesour.'
	    when extratolinha.k86_bancohistmov = 2 then 'Extrato'
       else 
           'Implan.'
       end as tipo
from concilia 
     inner join conciliaitem on k83_concilia = k68_sequencial 

     left join conciliaextrato on k87_conciliaitem = k83_sequencial
     left join extratolinha on k87_extratolinha = k86_sequencial

     left join conciliacor on k84_conciliaitem = k83_sequencial 
     left join fc_extratocaixa(".db_getsession("DB_instit").",$db83_sequencial,'$sDataIniConciliacao'::date,'$sDataFimConciliacao'::date,false) on ricaixa  = conciliacor.k84_id 
	and riautent = conciliacor.k84_autent
        and ridata   = conciliacor.k84_data

where k68_contabancaria = $db83_sequencial
  and k68_data  between '$sDataIniConciliacao'  and '$sDataFimConciliacao';
";

//echo "$sSqlCodConcilia";exit;

$rsCodigoConcilia = db_query($sSqlCodConcilia);

if( !$rsCodigoConcilia ){
   db_redireciona("db_erros.php?fechar=true&db_erro=Dados da concilicao da conta ($iConta) $sSqlCodConcilia .");  
}

$data = "";

for( $lista = 0 ; $lista < pg_num_rows($rsCodigoConcilia); $lista ++ ){

   $concilia = db_utils::fieldsMemory($rsCodigoConcilia, $lista);

   if( $sMovimento == 2 and $concilia->tipo != 'Extrato' ) {
     continue;   
   }
   if( $sMovimento == 1 and $concilia->tipo == 'Extrato' ) {
     continue;   
   }


   if ($pdf->gety() > $pdf->h or $pagina ==0 ) {
     $pagina = 1;
     $pdf->cell(15,$alt,"Data",1,0,"C",0);
     $pdf->cell(15,$alt,"Tipo",1,0,"L",0);
     $pdf->cell(40,$alt,"Histórico",1,0,"L",0);
     $pdf->cell(100,$alt,"Complemento",1,0,"L",0);
     $pdf->cell(30,$alt,"Valor",1,1,"R",0);

   }

   if ( $data != $concilia->k68_data || $data == "" ){
      $data = $concilia->k68_data;
      $pdf->cell(15,$alt,db_formatar($concilia->k68_data,'d'),0,0,"C",0);
   }else{
      $pdf->cell(15,$alt,'',0,0,"C",0);
   }
   $pdf->cell(15,$alt,$concilia->tipo,0,0,"L",0);
   $pdf->cell(40,$alt,$concilia->k86_documento,0,0,"L",0);

   $x = $pdf->getx();
   $y = $pdf->gety();

   $pdf->multicell(95,$alt,$concilia->k86_historico,0,"L",0);

   $yy = $pdf->gety();
   $pdf->setxy($x+95,$y);

   $pdf->Cell(30,$alt,db_formatar($concilia->k86_valor,'f'),0,0,"R",0);
   $pdf->Cell(2,$alt,($concilia->k86_valor<0 ? '-' : '+' ),0,1,"R",0);

   $pdf->sety($yy);

}

$pdf->output();
