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

$clrotulo = new rotulocampo;
$clrotulo->label('r06_codigo');
$clrotulo->label('r06_descr');
$clrotulo->label('r06_elemen');
$clrotulo->label('r06_pd');

$clgerasql = new cl_gera_sql_folha;
$clgerasql->inicio_rh = false;

$get = db_utils::postMemory($_GET);
parse_str($_SERVER['QUERY_STRING']);
$ponts = $get->pontos;

$get->rubricas = json_decode(str_replace('\"', '"', $get->rubricas));
$get->recursos = json_decode(str_replace('\"', '"', $get->recursos));

$dbwhere = " rh23_rubric is null and 1=1 ";
$dbwhererubs = "";

if (!empty($get->recursos)) {
    $recursos = implode("', '", $get->recursos);
    $dbwhere = " o15_recurso in ('{$recursos}')";
}

if (!empty($get->rubricas)) {
    $rubricas = implode("', '", $get->rubricas);
    $dbwhererubs = " and #s#_rubric in ('{$rubricas}')";
}

$arr_pontos =explode(",",$ponts);
$varSQL = "";

$headPontos = "";

for($i=0; $i<6; $i++){
  $valor = "";
  if( isset($arr_pontos[$i]) && trim($arr_pontos[$i]) != "" ){
    $valor = $arr_pontos[$i];
  }else if( count($arr_pontos) == 1 && trim($arr_pontos[0]) == "" ){
    $valor = "$i";
  }
  switch ( $valor ) {
    case "0" :
               $headPontos .= (trim($varSQL) != "" ? ", " : "") . "Salário";
               $varSQL .= (trim($varSQL) != "" ? " union " : "") . " ( " . $clgerasql->gerador_sql(
                                                                                                   "r14", $ano, $mes, null, null,
                                                                                                   "#s#_rubric, #s#_valor, #s#_regist, #s#_pd, #s#_anousu, #s#_mesusu",
                                                                                                   "#s#_rubric",
                                                                                                   "#s#_pd <> 3 " . $dbwhererubs
                                                                                                  ) . " ) ";
               $sigla = (isset($sigla) ? $sigla : "r14");
               break;
    case "1" :
               $headPontos .= (trim($varSQL) != "" ? ", " : "") . "Adiantamento";
               $varSQL .= (trim($varSQL) != "" ? " union " : "") . " ( " . $clgerasql->gerador_sql(
                                                                                                   "r22", $ano, $mes, null, null,
                                                                                                   "#s#_rubric, #s#_valor, #s#_regist, #s#_pd, #s#_anousu, #s#_mesusu",
                                                                                                   "#s#_rubric",
                                                                                                   "#s#_pd <> 3" . $dbwhererubs
                                                                                                  ) . " ) ";
               $sigla = (isset($sigla) ? $sigla : "r22");
               break;
    case "2" :
               $headPontos .= (trim($varSQL) != "" ? ", " : "") . "Férias";
               $varSQL .= (trim($varSQL) != "" ? " union " : "") . " ( " . $clgerasql->gerador_sql(
                                                                                                   "r31", $ano, $mes, null, null,
                                                                                                   "#s#_rubric, #s#_valor, #s#_regist, #s#_pd, #s#_anousu, #s#_mesusu",
                                                                                                   "#s#_rubric",
                                                                                                   "#s#_pd <> 3 " . $dbwhererubs
                                                                                                  ) . " ) ";
               $sigla = (isset($sigla) ? $sigla : "r31");
               break;
    case "3" :
               $headPontos .= (trim($varSQL) != "" ? ", " : "") . "Rescisão";
               $varSQL .= (trim($varSQL) != "" ? " union " : "") . " ( " . $clgerasql->gerador_sql(
                                                                                                   "r20", $ano, $mes, null, null,
                                                                                                   "#s#_rubric, #s#_valor, #s#_regist, #s#_pd, #s#_anousu, #s#_mesusu",
                                                                                                   "#s#_rubric",
                                                                                                   "#s#_pd <> 3 " . $dbwhererubs
                                                                                                  ) . " ) ";
               $sigla = (isset($sigla) ? $sigla : "r20");
               break;
    case "4" :
               $headPontos .= (trim($varSQL) != "" ? ", " : "") . "Saldo do 13o.";
               $varSQL .= (trim($varSQL) != "" ? " union " : "") . " ( " . $clgerasql->gerador_sql(
                                                                                                   "r35", $ano, $mes, null, null,
                                                                                                   "#s#_rubric, #s#_valor, #s#_regist, #s#_pd, #s#_anousu, #s#_mesusu",
                                                                                                   "#s#_rubric",
                                                                                                   "#s#_pd <> 3 " . $dbwhererubs
                                                                                                  ) . " ) ";
               $sigla = (isset($sigla) ? $sigla : "r35");
               break;
    case "5" :
               $headPontos .= (trim($varSQL) != "" ? ", " : "") . "Complementar";
               $varSQL .= (trim($varSQL) != "" ? " union " : "") . " ( " . $clgerasql->gerador_sql(
                                                                                                   "r48", $ano, $mes, null, null,
                                                                                                   "#s#_rubric, #s#_valor, #s#_regist, #s#_pd, #s#_anousu, #s#_mesusu",
                                                                                                   "#s#_rubric",
                                                                                                   "#s#_pd <> 3 " . $dbwhererubs
                                                                                                  ) . " ) ";
               $sigla = (isset($sigla) ? $sigla : "r48");
               break;
  }
}

if(count($arr_pontos) == 1 && trim($arr_pontos[0]) == ""){
  $headPontos = "Todos os pontos";
}

function getReduzidoPorRubrica($rubrica, $instit, $anousu)
{

  $sql = "
            select rh75_rubric,
                   rh75_retencaotiporec,
                   e21_receita,
                   c61_reduz
              from rhrubretencao
        inner join retencaotiporec  on rh75_retencaotiporec = e21_sequencial
        inner join tabrec on e21_receita = k02_codigo

        inner join tabplan on tabrec.k02_codigo = tabplan.k02_codigo
                          and tabplan.k02_anousu = {$anousu}
        inner join conplanoreduz on conplanoreduz.c61_anousu = tabplan.k02_anousu
                                and conplanoreduz.c61_reduz = tabplan.k02_reduz
                                and conplanoreduz.c61_instit = {$instit}
        inner join conplano on conplanoreduz.c61_anousu = conplano.c60_anousu
                           and conplanoreduz.c61_codcon = conplano.c60_codcon
             where rh75_instit = {$instit}
               and rh75_rubric = '{$rubrica}'
  union
            select rh75_rubric,
                   rh75_retencaotiporec,
                   e21_receita,
                   c61_reduz
              from rhrubretencao
        inner join retencaotiporec  on rh75_retencaotiporec = e21_sequencial
        inner join tabrec on e21_receita = k02_codigo

        inner join taborc on tabrec.k02_codigo = taborc.k02_codigo
                 and taborc.k02_anousu = {$anousu}
        inner join orcreceita on taborc.k02_codrec = orcreceita.o70_codrec
                     and orcreceita.o70_anousu = {$anousu}
                     and orcreceita.o70_instit = {$instit}
        inner join conplanoorcamento on orcreceita.o70_anousu = conplanoorcamento.c60_anousu
                                    and orcreceita.o70_codfon = conplanoorcamento.c60_codcon
        inner join conplanoorcamentoanalitica on  c61_codcon = c60_codcon
                                             and c61_anousu = c60_anousu
             where rh75_instit = {$instit}
               and rh75_rubric = '{$rubrica}'
  ";
  $iReduzido = "";

  $rsReduz = db_query($sql);

  if (pg_numrows($rsReduz) > 0) {
    $iReduzido = db_utils::fieldsMemory($rsReduz ,0)->c61_reduz;
  }
  return  $iReduzido;
}




$clgerasql->inicio_rh = true;
$clgerasql->inner_rel = false;
$clgerasql->inner_exe = true;
$clgerasql->inner_org = true;
$clgerasql->inner_vin = true;
$clgerasql->inner_pro = true;
$clgerasql->inner_rec = true;
$clgerasql->usar_rub  = true;
$clgerasql->usar_rel  = true;
$clgerasql->usar_lot  = true;
$clgerasql->usar_exe  = true;
$clgerasql->usar_org  = true;
$clgerasql->usar_vin  = true;
$clgerasql->usar_pro  = true;
$clgerasql->usar_rec  = true;
$clgerasql->subsql    = $varSQL;
$clgerasql->subsqlano = $sigla."_anousu";
$clgerasql->subsqlmes = $sigla."_mesusu";
$clgerasql->subsqlreg = $sigla."_regist";
$clgerasql->subsqlrub = $sigla."_rubric";
$clgerasql->trancaGer = true;
$sqlFinal = $clgerasql->gerador_sql(
    "",
    $ano, $mes, null, null,
    "o15_recurso as rh25_recurso,
     o15_descr,
     rh27_descr, ".$sigla."_pd as tipo, ".$sigla."_rubric as rubrica,
    rhpessoalmov.rh02_instit,rh02_anousu,
    round(sum(".$sigla."_valor),2) as valor",
    $sigla."_rubric, rh25_recurso",
    $dbwhere .
    "group by o15_recurso,
              o15_descr, ".$sigla."_pd,
              ".$sigla."_rubric,
              rh27_descr,
              rhpessoalmov.rh02_instit,
              rhpessoalmov.rh02_anousu
              "
);



$result = $clgerasql->sql_record($sqlFinal);
if($result === false || ($result !== false && $clgerasql->numrows_exec == 0)){
   db_redireciona('db_erros.php?fechar=true&db_erro=Não existem dados no período de '.$mes.' / '.$ano);
}

$head3 = "Retenções e Consignações da Folha";
$head5 = "Período : " . $mes . " / " . $ano;
$head7 = "Pontos: " . $headPontos;

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);
$troca = 1;
$alt = 4;

$rubri_ant = "";
$total_rub = 0;
$total_ger = 0;
$cor = 1;
$proxpag = true;

for($x = 0; $x < pg_numrows($result);$x++){
  db_fieldsmemory($result,$x);

  if($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
    $pdf->addpage();
    $pdf->setfont('arial','b',8);
    $pdf->cell(15,$alt,'RUBRICA',1,0,"C",1);
    $pdf->cell(15,$alt,'REDUZ',1,0,"C",1);
    $pdf->cell(70,$alt,'DESCRIÇÃO',1,0,"C",1);
    $pdf->cell(70,$alt,'RECURSO',1,0,"C",1);
    $pdf->cell(20,$alt,'DESCONTO',1,1,"C",1);
    $troca = 0;
    $cor = 1;
    $proxpag = true;
  }

  // $cor = ($cor == 0 ? 1 : 0);
  $cor = 0;

  if($rubri_ant != $rubrica || $proxpag == true){
    if($rubri_ant != $rubrica && $rubri_ant != ""){
      $pdf->setfont('arial','b',7);
      $pdf->cell(165,$alt,"Total da rubrica ",0,0,"R",1);
      $pdf->cell( 25,$alt,db_formatar($total_rub, "f"),0,1,"R",1);
      $pdf->ln(2);
      $total_rub = 0;
      $cor = 0;
    }

    $iReduzidoRubrica = getReduzidoPorRubrica($rubrica, $rh02_instit, $rh02_anousu);

    $pdf->setfont('arial','',7);
    $pdf->cell(15,$alt,$rubrica ,0,0,"C",$cor);
    $pdf->cell(15,$alt,$iReduzidoRubrica ,0,0,"C",$cor);
    $pdf->cell(70,$alt,$rh27_descr,0,0,"L",$cor);
  }else{
    $pdf->setfont('arial','',7);
    $pdf->cell(30,$alt,"",0,0,"C",$cor);
    $pdf->cell(70,$alt,"",0,0,"L",$cor);
  }

  $pdf->cell(70,$alt,$rh25_recurso . " - " .$o15_descr,0,0,"L",$cor);
  $pdf->cell(20,$alt,db_formatar($valor,"f"),0,1,"R",$cor);
  $total_rub += $valor;
  if($tipo == 1){
    $total_ger -= $valor;
  }else if ($tipo == 2){
    $total_ger += $valor;
  }
  $rubri_ant = $rubrica;
  $proxpag = false;
}
$pdf->ln(3);
$pdf->setfont('arial','B',7);
$pdf->cell(165,$alt,"Total da rubrica ",0,0,"R",1);
$pdf->cell( 25,$alt,db_formatar($total_rub, "f"),0,1,"R",1);
$pdf->ln(1);
$pdf->setfont('arial','B',8);
$pdf->cell(165,$alt,"Total geral ","T",0,"R",1);
$pdf->cell( 25,$alt,db_formatar($total_ger, "f"),"T",1,"R",1);

if($totaliza == 's') {
  $sqlFinal = $clgerasql->gerador_sql(
    "",
    $ano, $mes, null, null,
    "o15_recurso as rh25_recurso, o15_descr,
    round(sum(case when ".$sigla."_pd = 2 then ".$sigla."_valor
        when ".$sigla."_pd = 1 then ".$sigla."_valor *(-1) end ),2) as valor",
    "rh25_recurso , o15_descr",
    $dbwhere .
    "group by o15_recurso,o15_descr"
);
  $result = $clgerasql->sql_record($sqlFinal);
  $pdf->setfont('arial','B',9);
  if($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
    $pdf->addpage();
  }
  $pdf->cell(0,$alt,'Total dos Recursos ',0,1,"L",0);
  $pdf->setfont('arial','',9);
  for($x = 0; $x < pg_numrows($result);$x++){
    db_fieldsmemory($result,$x);

    if($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
      $pdf->addpage();
    }
    $pdf->cell(75,$alt,$rh25_recurso . " - " .$o15_descr,0,0,"L",$cor,'','.');
    $pdf->cell(25,$alt,db_formatar($valor,"f"),0,1,"R",$cor);
  }
}



$pdf->Output();
?>
