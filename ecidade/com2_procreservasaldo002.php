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

include(modification("fpdf151/pdf.php"));
include(modification("libs/db_sql.php"));


parse_str($HTTP_SERVER_VARS['QUERY_STRING']);


$sql="
select distinct o80_codres,
       o40_orgao,
       o40_descr,
       o41_unidade,
       o41_descr,
       o58_coddot,
       o56_descr,
       o56_elemento,
       o80_descr,
       o80_dtini,
       o80_dtfim,
       o80_anousu,
       o80_valor,
       nomeinst,
       o58_funcao,
       o52_descr,
       o58_subfuncao,
       o53_descr,
       o58_programa,
       o54_descr,
       o58_projativ,
       o55_descr,
       o58_codigo,
       o15_descr,
       pc01_descrmater,
       pc01_codmater,
       pc11_resum,
       pc90_numeroprocesso,
       fc_estruturaldotacao(o58_anousu,o58_coddot) as estrut,
       pc11_seq,
       gestao,
       descricao
from orcreserva
     inner join orcdotacao   on o58_coddot  = o80_coddot
                            and o58_anousu  = o80_anousu
     inner join orcorgao     on o40_orgao   = o58_orgao
                            and o40_anousu  = o58_anousu
                            and o40_instit  = o58_instit
     inner join orcunidade   on o41_unidade = o58_unidade
                            and o41_orgao   = o58_orgao
                            and o41_anousu  = o58_anousu
                            and o41_instit  = o58_instit
     inner join orcelemento  on o56_codele  = o58_codele
                            and o56_anousu  = o58_anousu
     inner join db_config    on codigo      = o58_instit
     inner join orcfuncao    on o58_funcao  = o52_funcao
     inner join orcsubfuncao on o58_subfuncao  = o53_subfuncao
     inner join orcprograma  on o58_programa= o54_programa and o80_anousu = o54_anousu
     left join orcprojativ  on o58_projativ= o55_projativ and o80_anousu = o55_anousu and o55_instit = o58_instit
     left join orctiporec   on o58_codigo  = o15_codigo
     inner join fonterecurso on orctiporec.o15_codigo = fonterecurso.orctiporec_id and fonterecurso.exercicio = orcdotacao.o58_anousu
     inner join orcreservasol on o82_codres = o80_codres
     inner join solicitem            on solicitem.pc11_codigo               = o82_solicitem
     inner join solicita ON pc11_numero = pc10_numero
     left join solicitaprotprocesso on pc90_solicita = pc11_numero
     INNER JOIN pcprocitem ON pc81_solicitem = pc11_codigo
     inner join pcorcamitemproc          on pcprocitem.pc81_codprocitem         = pcorcamitemproc.pc31_pcprocitem
      inner join pcorcamitem          on pcorcamitem.pc22_orcamitem          = pcorcamitemproc.pc31_orcamitem
       left  join solicitempcmater on solicitempcmater.pc16_solicitem = solicitem.pc11_codigo
      left  join pcmater          on pcmater.pc01_codmater     = solicitempcmater.pc16_codmater
  where pc22_codorc = $pc20_codorc  AND pc10_depto = ".db_getsession("DB_coddepto")." and pc10_instit = ".db_getsession("DB_instit")."  and  o58_instit = ".db_getsession("DB_instit")." order by pc11_seq";

  //db_getsession("DB_coddepto")

// echo($sql); die;
$result = pg_exec($sql);
$xxnum = pg_numrows($result);
if ($xxnum == 0){
   db_redireciona('db_erros.php?fechar=true&db_erro=Não existe Cadastrada  Contate suporte.');

}

$aDadosReserva = array();
$aReservas     = array();
for($x = 0; $x < pg_numrows($result);$x++){

  db_fieldsmemory($result,$x);

  $aDadosReserva[$estrut]['iRecurso']   = $gestao.' - '.$descricao;
  $aDadosReserva[$estrut]['iMotivo']    = $o80_descr;
  $aDadosReserva[$estrut]['iDotacao']   = $o58_coddot;
  $aDadosReserva[$estrut]['iValor']    += $o80_valor;
  $aDadosReserva[$estrut]['iProcesso']  = $pc90_numeroprocesso;

  $aReservas[$estrut][] =  $o80_codres."/".$o80_anousu;

}


db_fieldsmemory($result,0);
$head3 = "RELATÓRIO DE RESERVA DE SALDO";
$head4 = $nomeinst;

$pdf = new PDF();
$pdf->Open();
$pdf->Addpage();
$pdf->AliasNbPages();
$total = 0;
$pdf->setfillcolor(235);
$troca = 1;
$alt = 6;

$pdf->setfont('arial','b',10);
$pdf->setX(85);
$pdf->cell(40,5,'Reserva de Saldo ',0,1,"C",0);
$pdf->setX(90);
$pdf->setfont('arial','',8);
$pdf->cell(30,$alt,'Código do Orçamento : '.$pc20_codorc,0,1,"C",0);

$pdf->setfont('arial','b',8);
$oTotal = 0;
$pdf->cell(195,$alt,'',"B",1,"C",0);
foreach ($aDadosReserva as $oDotacao => $oDadosReserva) {

  $pdf->ln(2);

  $oReservas = implode(",", $aReservas[$oDotacao]);

  $pdf->setfont('arial','b',8);
  $pdf->cell(35,$alt,'Reservas: ',"",0,"L",0);
  $pdf->setfont('arial','',8);
  $pdf->cell(45,$alt,$oReservas,"",1,"L",0);

  $pdf->setfont('arial','b',8);
  $pdf->cell(35,$alt,'Dotação Orçamentária: ',"",0,"L",0);
  $pdf->cell(8,$alt,$oDadosReserva['iDotacao'],"",0,"L",0);
  $pdf->setfont('arial','',8);
  $pdf->cell(45,$alt," -  ".$oDotacao,"",1,"L",0);

  $pdf->setfont('arial','b',8);
  $pdf->cell(35,$alt,'Recurso: ',"",0,"L",0);
  $pdf->setfont('arial','',8);
  $pdf->cell(40,$alt,$oDadosReserva['iRecurso'],"",1,"L",0);

  $pdf->setfont('arial','b',8);
  $pdf->cell(35,$alt,'Processo: ',"",0,"L",0);
  $pdf->setfont('arial','',8);
  $pdf->cell(40,$alt,$oDadosReserva['iProcesso'],"",1,"L",0);


  $pdf->setfont('arial','b',8);
  $pdf->cell(35,$alt,'Valor: ',"",0,"L",0);
  $pdf->setfont('arial','',8);
  $pdf->cell(8,$alt,"R$ ".db_formatar($oDadosReserva['iValor'],"f"),"",1,"L",0);

  $pdf->cell(195,4,'',"B",1,"C",0);

  $oTotal += $oDadosReserva['iValor'];

}

$pdf->cell(125,4,'TOTAL DA RESERVA',"",0,"L",0);
$pdf->cell(30,4,'R$ '.db_formatar($oTotal,'f'),"",0,"R",0);
$pdf->cell(40,4,'',"",0,"C",0);

$pdf->Output();

?>
