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

use ECidade\Pdf\Pdf;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("classes/db_atividaderh_classe.php"));

$clatividaderh = new cl_atividaderh;
$escola = db_getsession("DB_coddepto");
$sep = "";
$aAtividades = explode(",", $atividades);
$semAtividade = in_array(" ", $aAtividades);
$somenteSemAtividade = $atividades == " ";
$atividades = $semAtividade ? str_replace(" ,", "", $atividades) : $atividades;

$whereSemAtividade = $semAtividade ? " or  ed01_i_codigo is null" : "";
$whereAtividades = $somenteSemAtividade ? "(ed01_i_codigo is null)" : "(ed01_i_codigo in ($atividades) {$whereSemAtividade})";
$result3 = $clatividaderh->sql_record($clatividaderh->sql_query("","ed01_c_descr","ed01_c_descr"," ed01_i_codigo in ($atividades)"));
$rsTotAtiv = pg_query("select ed01_i_codigo from atividaderh");
$instit = db_getsession("DB_instit");
$ano = db_anofolha();
$mes = db_mesfolha();



if($saida == "N"){
  $where = " ed75_i_escola = {$escola}  AND {$whereAtividades} AND ed75_i_saidaescola is null ";
} else {
  $where = " ed75_i_escola = $escola  AND {$whereAtividades}";
}

$sql = "SELECT DISTINCT case when ed20_i_tiposervidor = 1 then rechumanopessoal.ed284_i_rhpessoal else rechumanocgm.ed285_i_cgm end as ed20_i_codigo,
               case when ed20_i_tiposervidor = 1 then cgmrh.z01_nome else cgmcgm.z01_nome end as z01_nome,
               case when ed20_i_tiposervidor = 1 then cgmrh.z01_cgccpf else cgmcgm.z01_cgccpf end  as z01_cgccpf,
               ed01_c_descr,
               case when ed20_i_tiposervidor = 1
                then regimerh.rh30_descr
                else regimecgm.rh30_descr
               end as rh30_descr,
               ed75_i_saidaescola,
               z04_nomesocial
        FROM rechumano
         inner join rechumanoescola on ed75_i_rechumano = ed20_i_codigo
         left join rechumanoativ on ed22_i_rechumanoescola = ed75_i_codigo
         left join atividaderh on ed01_i_codigo = ed22_i_atividade
         left join rechumanopessoal  on  rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo
         left join rhpessoal  on  rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
         left join rhpessoalmov on rhpessoalmov.rh02_anousu  = $ano
                               and rhpessoalmov.rh02_mesusu  = $mes
                               and rhpessoalmov.rh02_regist  = rhpessoal.rh01_regist
                               and rhpessoalmov.rh02_instit  = $instit
         left join rhregime as regimerh on  regimerh.rh30_codreg = rhpessoalmov.rh02_codreg
         left join cgm as cgmrh on  cgmrh.z01_numcgm = rhpessoal.rh01_numcgm
         left join rechumanocgm  on  rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo
         left join cgm as cgmcgm on  cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm
         left join rhregime as regimecgm on  regimecgm.rh30_codreg = rechumano.ed20_i_rhregime
         left join cgmfisico on cgmfisico.z04_numcgm = rhpessoal.rh01_numcgm
        WHERE $where
        ORDER BY $ordem
       ";

$result = pg_query($sql);
$linhas = pg_num_rows($result);

if ($linhas==0) {
  db_redireciona('db_erros.php?fechar=true&db_erro=Nenhum registro encontrado.');
  exit;
}

$pdf = new Pdf();
$pdf->init(false);
$pdf->addTitulo("RELATÓRIO RECURSOS HUMANOS POR ATIVIDADE");
$atividadeCabecalho002 = "Atividade(s): ";

if($clatividaderh->numrows == pg_num_rows($rsTotAtiv)) {
  $atividadeCabecalho002 .= "TODAS AS ATIVIDADES";
} else {
  for ($c=0;$c<$clatividaderh->numrows;$c++) {
    db_fieldsmemory($result3,$c);
    $atividadeCabecalho002 .= $sep.$ed01_c_descr;
    $sep = ", ";
  }
}

if (strlen($atividadeCabecalho002) > 320) {
  $pdf->addTitulo(substr($atividadeCabecalho002,0,320)."...");
} else {
  $pdf->addTitulo($atividadeCabecalho002);
}

$pdf->ln(5);
$troca = 1;
$cor1 = "0";
$cor2 = "1";
$cor = "";
$cont = 0;
$contat = 0;
$continat = 0;

for($c=0;$c<$linhas;$c++){
  db_fieldsmemory($result,$c);

  if ($pdf->gety() > $pdf->getH() - 30 || $troca != 0 ) {
    $pdf->addpage('L');
    $pdf->setfillcolor(215);
    $pdf->setfont('arial','b',8);
  if ($saida == "N") {
    $pdf->cell(25,4,"Matrícula/CGM",1,0,"C",1);
    $pdf->cell(80,4,"Nome",1,0,"C",1);
    $pdf->cell(80,4,"Nome Social",1,0,"C",1);
    $pdf->cell(20,4,"CPF",1,0,"C",1);
    $pdf->cell(40,4,"Atividade",1,0,"C",1);
    $pdf->cell(35,4,"Regime",1,1,"C",1);
  } else {
    $pdf->cell(22,4,"Matrícula/CGM",1,0,"C",1);
    $pdf->cell(73.5,4,"Nome",1,0,"C",1);
    $pdf->cell(73.5,4,"Nome Social",1,0,"C",1);
    $pdf->cell(20,4,"CPF",1,0,"C",1);
    $pdf->cell(40,4,"Atividade",1,0,"C",1);
    $pdf->cell(35,4,"Regime",1,0,"C",1);
    $pdf->cell(13,4,"Dt. Saída",1,1,"C",1);
  }
  $troca = 0;
 }

  if ($cor==$cor1) {
    $cor = $cor2;
  } else {
    $cor = $cor1;
  }

  $pdf->setfillcolor(240);
  $pdf->setfont('arial','',7);

  if ($saida == "N") {
   $pdf->cell(25,4,$ed20_i_codigo,'B',0,"C",$cor);
   $pdf->cell(80,4,$z01_nome,'B',0,"L",$cor);
   $pdf->cell(80,4,mb_strimwidth($z04_nomesocial, 0, 51),'B',0,"L",$cor);
   $pdf->cell(20,4,$z01_cgccpf,'B',0,"C",$cor);
   $pdf->cell(40,4,$ed01_c_descr==""?"Não informado":$ed01_c_descr,'B',0,"C",$cor);
   $pdf->cell(35,4,$rh30_descr,'B',1,"C",$cor);
  } else {
   $pdf->cell(22,4,$ed20_i_codigo,'B',0,"C",$cor);
   $pdf->cell(73.5,4,$z01_nome,'B',0,"L",$cor); 
   $pdf->cell(73.5,4,mb_strimwidth($z04_nomesocial, 0, 42),'B',0,"L",$cor);
   $pdf->cell(20,4,$z01_cgccpf,'B',0,"C",$cor);
   $pdf->cell(40,4,$ed01_c_descr==""?"Não informado":$ed01_c_descr,'B',0,"C",$cor);
   $pdf->cell(35,4,$rh30_descr,'B',0,"C",$cor);
   if (!empty($ed75_i_saidaescola)) {
     $dtsaida = date('d/m/Y', strtotime($ed75_i_saidaescola));
     $pdf->cell(13,4,$dtsaida,'B',1,"C",$cor);
     $continat++;
  } else {
     $pdf->cell(13,4,'---','B',1,"C",$cor);
     $contat++;
   }
 }
 $cont++;
}

$pdf->setfont('arial','b',7);

if ($saida == "S") {
  $pdf->cell(280,5,"Total de Recursos Humanos Ativos: $contat",1,1,"L",0);
  $pdf->cell(280,5,"Total de Recursos Humanos Inativos: $continat",1,1,"L",0);
}

$pdf->cell(280,5,"Total de Recursos Humanos: $cont",1,1,"L",0);
$pdf->Output();

?>
