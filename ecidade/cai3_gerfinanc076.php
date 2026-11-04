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


require_once(modification("classes/db_issbase_classe.php"));
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("fpdf151/pdf.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("std/db_stdClass.php"));
$clrotulo = new rotulocampo;
$clrotulo->label('j40_refant');
$clrotulo->label('q02_inscmu');
parse_str($_SERVER["QUERY_STRING"]);

$oInstit = db_stdClass::getDadosInstit();
$head1     = "";
$head2     = "";
$sqlparag  = " select db02_texto ";
$sqlparag .= "   from db_documento ";
$sqlparag .= "        inner join db_docparag  on db03_docum   = db04_docum ";
$sqlparag .= "        inner join db_tipodoc   on db08_codigo  = db03_tipodoc ";
$sqlparag .= "        inner join db_paragrafo on db04_idparag = db02_idparag ";
$sqlparag .= " where db03_tipodoc = 1017 ";
$sqlparag .= "   and db03_instit = " . db_getsession("DB_instit") . " ";
$sqlparag .= " order by db04_ordem ";
$resparag = db_query($sqlparag);

if (pg_numrows($resparag) == 0) {
  $head3 = "SECRETARIA DA FAZENDA";
} else {
  db_fieldsmemory($resparag, 0);
  $head3 = $db02_texto;
}

$head4 = "Lançamento do crédito";


if (isset($numcgm)) {
  $head5 = "CGM " . $numcgm;
} else if (isset($matric)) {
  $head5 = "CGM " . $matric;
} else if (isset($inscr)) {
  $head5 = "CGM " . $inscr;
} else {
  $head5 = "Numpre " . $numpre;
}

if (!empty($data_lancamento_inicio)) {
  $datafim = empty($data_lancamento_final) ? date("d/m/Y") : $data_lancamento_final;
  $head6 = "Data inicial " . $data_lancamento_inicio . ' até ' . $datafim;
}

if (!empty($situacao)) {
  $head7 = "Status " . $situacao;
}

if (!empty($tipo)) {
  if ($tipo == 1) {
    $head8 = "Crédito Manual ";
  } else if ($tipo == 2) {
    $head8 = "Crédito Automático ";
  }
}


$aWherePagamento    = array();
$sWhereNumpreNormal = "";
$sWhereNumprePgto   = "";
$sInnerPagamento = "";


if (isset($numcgm)) {

  $sSqlEnderCgm  = " select *                    ";
  $sSqlEnderCgm .= "   from cgm                  ";
  $sSqlEnderCgm .= "  where z01_numcgm = $numcgm ";

  $oEnderCgm     =  db_utils::fieldsMemory(db_query($sSqlEnderCgm), 0);

  $nome  = 'CGM         : ' . $oEnderCgm->z01_numcgm . ' - ' . $oEnderCgm->z01_nome;
  $ender = $oEnderCgm->z01_ender . ', ' . $oEnderCgm->z01_numero . ' ' . $oEnderCgm->z01_compl . ' ' . $oEnderCgm->z01_bairro . ' - ' . $oEnderCgm->z01_cep . ' - ' . $oEnderCgm->z01_munic . '/' . $oEnderCgm->z01_uf;;

  $sInnerCredito = "inner join arrenumcgm on arrenumcgm.k00_numpre = abatimentorecibo.k127_numprerecibo ";
  $sWhereCredito = "and arrenumcgm.k00_numcgm = " . $numcgm;
} else if (isset($matric)) {

  $sSqlEnderMatric  = " select * ";
  $sSqlEnderMatric .= "   from proprietario         ";
  $sSqlEnderMatric .= "  where j01_matric = $matric ";

  $oEnderMatric     = db_utils::fieldsMemory(db_query($sSqlEnderMatric), 0);

  $sSqlEnvol        = " select rvnome                                                                   ";
  $sSqlEnvol       .= "   from fc_busca_envolvidos(true, {$oInstit->db21_regracgmiptu}, 'M', {$matric}) ";

  $oEnvolvidos      = db_utils::fieldsMemory(db_query($sSqlEnvol), 0);


  $nome  = 'Matrícula : ' . $matric . ' - '  . $oEnvolvidos->rvnome;
  $ender = $oEnderMatric->tipopri
    . ' '  . $oEnderMatric->nomepri
    . ', ' . $oEnderMatric->j39_numero
    . ' '  . $oEnderMatric->j39_compl
    . ' - ' . $oEnderMatric->j13_descr
    . ' - ' . $oEnderMatric->z01_ceppri
    . ' -  ZONA : ' . $oEnderMatric->j37_zona
    . '  SETOR : '  . $oEnderMatric->j34_setor
    . '  QUADRA : ' . $oEnderMatric->j34_quadra
    . '  LOTE : '   . $oEnderMatric->j34_lote
    . '  PQL : ' . $oEnderMatric->pql_localizacao;
  $labelrefant  = mb_strtoupper($GLOBALS['RLj40_refant']);
  $refant       = $oEnderMatric->j40_refant;

  $sInnerCredito = "inner join arrematric on arrematric.k00_numpre = abatimentorecibo.k127_numprerecibo ";
  $sWhereCredito = "and arrematric.k00_matric = " . $matric;
} else if (isset($inscr)) {


  $sSqlEnderInscr = " select *
                        from empresa
                       where q02_inscr = $inscr ";

  $oEnderInscr    = db_utils::fieldsMemory(db_query($sSqlEnderInscr), 0);

  $sSqlEnvol      = " select rvnome
                        from fc_busca_envolvidos(true, {$oInstit->db21_regracgmiss}, 'I', {$inscr}) ";

  $oEnvolvidos    = db_utils::fieldsMemory(db_query($sSqlEnvol), 0);


  $nome = "Inscrição : {$inscr} - {$oEnvolvidos->rvnome}";

  if (trim($oEnderInscr->z01_nomefanta) != "") {
    $nome .= " - Nome fantasia: " . trim($oEnderInscr->z01_nomefanta);
  }

  $ender = $oEnderInscr->j14_tipo   . ' '
    . $oEnderInscr->z01_ender  . ', '
    . $oEnderInscr->z01_numero . ' '
    . $oEnderInscr->z01_compl . ' '
    . ' - ' . $oEnderInscr->j13_descr
    . $oEnderInscr->z01_cep;
  $labelrefant  = mb_strtoupper($GLOBALS['RLq02_inscmu']);
  $refant       = $oEnderInscr->q02_inscmu;

  $sInnerCredito = "inner join arreinscr on arreinscr.k00_numpre = abatimentorecibo.k127_numprerecibo ";
  $sWhereCredito = "and arreinscr.k00_inscr = " . $inscr;
} else {

  $sSqlEnderNumpre  = " select cgm.*                                                                                 ";
  $sSqlEnderNumpre .= "   from ( select arrecad.k00_numcgm                                                           ";
  $sSqlEnderNumpre .= "            from arrecad                                                                      ";
  $sSqlEnderNumpre .= "                 inner join arreinstit  on arreinstit.k00_numpre = arrecad.k00_numpre         ";
  $sSqlEnderNumpre .= "                                       and arreinstit.k00_instit = " . db_getsession('DB_instit');
  $sSqlEnderNumpre .= "            where arrecad.k00_numpre = $numpre                                                ";
  $sSqlEnderNumpre .= "                                                                                              ";
  $sSqlEnderNumpre .= "          union all                                                                           ";
  $sSqlEnderNumpre .= "                                                                                              ";
  $sSqlEnderNumpre .= "          select arrecant.k00_numcgm                                                          ";
  $sSqlEnderNumpre .= "            from arrecant                                                                     ";
  $sSqlEnderNumpre .= "                 inner join arreinstit  on arreinstit.k00_numpre = arrecant.k00_numpre        ";
  $sSqlEnderNumpre .= "                                       and arreinstit.k00_instit = " . db_getsession('DB_instit');
  $sSqlEnderNumpre .= "            where arrecant.k00_numpre = $numpre                                               ";
  $sSqlEnderNumpre .= "                                                                                              ";
  $sSqlEnderNumpre .= "        ) as x                                                                                ";
  $sSqlEnderNumpre .= "        inner join cgm on z01_numcgm = x.k00_numcgm                                           ";

  $oEnderNumpre = db_utils::fieldsMemory(db_query($sSqlEnderNumpre), 0);

  $nome  = 'CGM         : ' . $oEnderNumpre->z01_numcgm . ' - ' . $oEnderNumpre->z01_nome . '       Cd. Arrecadao : ' . $numpre;
  $ender = $oEnderNumpre->z01_ender . ', ' . $oEnderNumpre->z01_numero . ' ' . $oEnderNumpre->z01_compl . ' ' . $oEnderNumpre->z01_bairro . ' - ' . $oEnderNumpre->z01_cep . ' - ' . $oEnderNumpre->z01_munic . '/' . $oEnderNumpre->z01_uf;

  $sInnerCredito = "";
  $sWhereCredito = "and abatimentorecibo.k127_numprerecibo = " . $numpre;
}

$where = '';
if (!empty($data_lancamento_inicio)) {
  $datafim = empty($data_lancamento_final) ? date("d/m/Y") : $data_lancamento_final;
  $where .= " and DATE( k125_datalanc ) between '$data_lancamento_inicio' and '$datafim' ";
}

if (!empty($situacao)) {
  $where .= " and status = '$situacao' ";
}

if (!empty($tipo)) {
  if ($tipo == 1) {
    $where .= " and k156_regracompensacao = 7 ";
  } else {
    $where .= " and k156_regracompensacao is null or k156_regracompensacao != 7";
  }
}
$iInstituicao = db_getsession("DB_instit");
$dDataSistema = date('Y-m-d', db_getsession('DB_datausu'));
$iAnoUsu  = db_getsession("DB_anousu");
$sSqlCreditosDisponiveis  = " select k125_sequencial,                                                                                                                           \n";
$sSqlCreditosDisponiveis .= "        k125_valordisponivel as abatimento_valordisponivel,                                                                                        \n";
$sSqlCreditosDisponiveis .= "        recibo.k00_numpre,                                                                                                                         \n";
$sSqlCreditosDisponiveis .= "        recibo.k00_receit,                                                                                                                         \n";
$sSqlCreditosDisponiveis .= "        recibo.k00_hist,                                                                                                                           \n";
$sSqlCreditosDisponiveis .= "        tabrec.k02_descr,                                                                                                                          \n";
$sSqlCreditosDisponiveis .= "        histcalc.k01_descr,                                                                                                                        \n";
$sSqlCreditosDisponiveis .= "        recibo.k00_valor,                                                                                                                                \n";
$sSqlCreditosDisponiveis .= "        case                                                                                                                                       \n";
$sSqlCreditosDisponiveis .= "          when (k125_datalanc + ((select coalesce(min(case when k155_tempovalidade = '' then null else k155_tempovalidade end::integer), 99999999) \n";
$sSqlCreditosDisponiveis .= "                                     from abatimentoregracompensacao                                                                               \n";
$sSqlCreditosDisponiveis .= "                                    inner join regracompensacao on k155_sequencial = k156_regracompensacao                                         \n";
$sSqlCreditosDisponiveis .= "                                   where k156_abatimento = abatimento.k125_sequencial)::integer||' days')::interval) >= '{$dDataSistema}'          \n";
$sSqlCreditosDisponiveis .= "           and k125_valordisponivel > 0                                                                                                            \n";
$sSqlCreditosDisponiveis .= "          then 'ATIVO'::varchar                                                                                                                    \n";
$sSqlCreditosDisponiveis .= "                                                                                                                                                   \n";
$sSqlCreditosDisponiveis .= "          else 'INATIVO'::varchar                                                                                                                  \n";
$sSqlCreditosDisponiveis .= "        end as status,                                                                                                                             \n";
$sSqlCreditosDisponiveis .= "        coalesce(
                                        (select sum(k157_valor) from abatimentoutilizacao where k157_abatimento = abatimento.k125_sequencial), 0
                                      ) as valor_utilizado,                                                                                                                      \n";
$sSqlCreditosDisponiveis .= "        coalesce(
                                        (select k167_data from abatimentocorrecao where k167_abatimento = k125_sequencial order by k167_data desc limit 1), k125_datalanc
                                      ) as data_correcao,                                                                                                                        \n";
$sSqlCreditosDisponiveis .= "       coalesce(
                                      (select sum(k167_valorcorrigido - k167_valorantigo) from abatimentocorrecao where k167_abatimento = abatimento.k125_sequencial), 0
                                    ) as valor_corrigido,                                                                                                                       \n";
$sSqlCreditosDisponiveis .= "       to_char(k125_datalanc,'DD/MM/YYYY') as k125_datalanc    ,                                                                                    \n";
$sSqlCreditosDisponiveis .= "coalesce(
    (select count(*) 
        from abatimentorecibo
      inner join recibo
          on k00_numpre = k127_numprerecibo
      where k127_abatimento = abatimento.k125_sequencial),
    0
) as qtd_registros
";
$sSqlCreditosDisponiveis .= "   from abatimentorecibo                                                                                                                           \n";
$sSqlCreditosDisponiveis .= "        inner join abatimento              on abatimento.k125_sequencial = abatimentorecibo.k127_abatimento                                        \n";
$sSqlCreditosDisponiveis .= "        inner join recibo                  on recibo.k00_numpre          = abatimentorecibo.k127_numprerecibo                                      \n";
$sSqlCreditosDisponiveis .= "        inner join arreinstit              on arreinstit.k00_numpre      = recibo.k00_numpre                                                       \n";
$sSqlCreditosDisponiveis .= "        inner join arretipo                on arretipo.k00_tipo          = recibo.k00_tipo                                                         \n";
$sSqlCreditosDisponiveis .= "        inner join tabrec                  on tabrec.k02_codigo          = recibo.k00_receit                                                       \n";
$sSqlCreditosDisponiveis .= "        inner join histcalc                on histcalc.k01_codigo        = recibo.k00_hist                                                         \n";
$sSqlCreditosDisponiveis .= "        left  join abatimentotransferencia on k158_abatimentoorigem      = k125_sequencial                                                         \n";
$sSqlCreditosDisponiveis .= "        left  join abatimentoutilizacao    on k157_sequencial            = k158_abatimentoutilizacao                                               \n";
$sSqlCreditosDisponiveis .= "        {$sInnerCredito}                                                                                                                           \n";
$sSqlCreditosDisponiveis .= "  where abatimento.k125_tipoabatimento = 3                                                                                                         \n";
$sSqlCreditosDisponiveis .= "    and arreinstit.k00_instit = {$iInstituicao}                                                                                                    \n";
$sSqlCreditosDisponiveis .= "        {$sWhereCredito}                                                                                                                           \n";
$sSqlCreditosDisponiveis .= "  group by k125_sequencial, recibo.k00_numpre, recibo.k00_valor, recibo.k00_receit, recibo.k00_hist, tabrec.k02_descr, histcalc.k01_descr,         \n";
$sSqlCreditosDisponiveis .= "           k125_datalanc                                                                                                                           \n";
$sSqlCreditosDisponiveis .= "  order by k125_sequencial desc                                                                                                                    \n";

$sSqlCreditosDisponiveis  = " select *, fc_corre( k00_receit, data_correcao, k00_valor, current_date, {$iAnoUsu}, data_correcao ) as valor_disponivel,
                                        fc_corre( k00_receit, data_correcao, abatimento_valordisponivel, current_date, {$iAnoUsu}, data_correcao ) as valor_disponivel_corrigido
                              from ({$sSqlCreditosDisponiveis}) as creditos;";

$sSqlCreditosDisponiveis .= "drop table if exists w_abatimento_credito; create temp table w_abatimento_credito as {$sSqlCreditosDisponiveis}                                    \n";
$sSqlCreditosDisponiveis .= "select k125_sequencial,
                                    abatimento_valordisponivel ,
                                    k00_numpre                 ,
                                    array_to_string(array_agg(k00_receit), ', ') codrecs         ,
                                    k00_hist                   , 
                                    array_to_string(array_agg(trim(k02_descr)), ', ') descr_receitas   , 
                                    k01_descr                  ,
                                    sum(k00_valor) total_recibo,
                                    status                     ,
                                    valor_utilizado            ,
                                    data_correcao              ,
                                    valor_corrigido            ,
                                    k125_datalanc              ,
                                    1 qtd_registros            ,
                                    sum(valor_disponivel) valor_disponivel,
                                    valor_disponivel_corrigido 
                                from w_abatimento_credito
                                left join  abatimentoregracompensacao on  k125_sequencial = k156_abatimento
                                where
                                    1=1
                                    $where
                            group by k125_sequencial            ,
                                    abatimento_valordisponivel ,
                                    k00_numpre                 ,
                                    k00_hist                   ,
                                    k01_descr                  ,
                                    status                     ,
                                    valor_utilizado            ,
                                    data_correcao              ,
                                    valor_corrigido            ,
                                    k125_datalanc              ,
                                    qtd_registros              ,
                                    valor_disponivel_corrigido";

//echo $sSqlCreditosDisponiveis; die;
$rsCreditosDisponiveis    = db_query($sSqlCreditosDisponiveis);
$iLinhasCreditos          = pg_num_rows($rsCreditosDisponiveis);
if ($iLinhasCreditos > 0) {
}

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage("P");
$pdf->SetFillColor(220);

$pdf->setxy(5, 35);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(5, 5, '', 0, 0, 'C');

$pdf->Cell(35, 5, "NOME",  0, 0, "R", 0);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(80, 5, ': ' . $nome,  0, 1, "L", 0);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(35, 5, "ENDEREÇO",  0, 0, "R", 0);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(80, 5, ': ' . $ender,  0, 1, "L", 0);
if (isset($refant)) {

  $pdf->SetFont('Arial', 'B', 8);
  $pdf->Cell(35, 5, $labelrefant,  0, 0, "R", 0);
  $pdf->SetFont('Arial', 'I', 8);
  $pdf->Cell(80, 5, ': ' . $refant,  0, 1, "L", 0);
}

$pdf->Cell(270, 2, '', "B", 1, "R", 0);

$pdf->setxy(5, 60);

$pdf->SetFont('Arial', 'B', 7);

$pdf->cell(15, 04, "Staus", 1, 0, "C", 0);
$pdf->cell(10, 04, "Abat", 1, 0, "C", 0);
$pdf->cell(15, 04, "Matric /Inscr", 1, 0, "C", 0);
$pdf->cell(15, 04, "DT.Lanc.", 1, 0, "C", 0);
$pdf->cell(15, 04, "Numpre", 1, 0, "C", 0);
$pdf->cell(20, 04, "Valor Original", 1, 0, "C", 0);
$pdf->cell(25, 04, "Valor Atualizado", 1, 0, "C", 0);
$pdf->cell(25, 04, "Valor Utilizado", 1, 0, "C", 0);
$pdf->cell(25, 04, "Valor Disponível", 1, 0, "C", 0);
$pdf->cell(35, 04, "Valor Disponível Corrigido", 1, 1, "C", 0);

if ($iLinhasCreditos > 0) {
  $totValorCredito    = 0;
  $totValorCorrigido  = 0;
  $totValorUtilizado  = 0;
  $totValorDisponivel = 0;

  $abatimentos = array();
  for ($iInd = 0; $iInd < $iLinhasCreditos; $iInd++) {
    $oCredito = db_utils::fieldsMemory($rsCreditosDisponiveis, $iInd);

    $oCredito->valor_corrigido = ($oCredito->valor_corrigido / $oCredito->qtd_registros);

    if (empty($abatimentos[$oCredito->k00_numpre])) {
      $abatimentos[$oCredito->k00_numpre]['valordisponivel'] = $oCredito->valor_utilizado;
    }

    if (!empty($oCredito->valor_corrigido)) {
      $nValorCorrigido = ($oCredito->total_recibo + $oCredito->valor_corrigido);
    } else {
      $nValorCorrigido = $oCredito->valor_disponivel;
    }

    if ($abatimentos[$oCredito->k00_numpre]['valordisponivel'] >= $nValorCorrigido) {
      $abatimentos[$oCredito->k00_numpre]['valordisponivel'] -= $nValorCorrigido;
      $oCredito->valor_utilizado  = $nValorCorrigido;
      $oCredito->valor_disponivel = 0;
    } elseif ($abatimentos[$oCredito->k00_numpre]['valordisponivel'] > 0) {
      $oCredito->valor_utilizado  = $abatimentos[$oCredito->k00_numpre]['valordisponivel'];
      $oCredito->valor_disponivel = $nValorCorrigido - $abatimentos[$oCredito->k00_numpre]['valordisponivel'];
      $abatimentos[$oCredito->k00_numpre]['valordisponivel'] -= $oCredito->valor_disponivel;
    }

    //Verifica origem de crédito do CGM
    $sqlOrigemCredito = "SELECT m.k00_matric, i.k00_inscr, c.k00_numcgm
                                FROM arrenumcgm as c
                                    LEFT JOIN arrematric as m on m.k00_numpre = c.k00_numpre
                                    LEFT JOIN arreinscr as i on i.k00_numpre = c.k00_numpre
                                WHERE c.k00_numpre = {$oCredito->k00_numpre}";
    $rsSqlOrigemCredito = db_query($sqlOrigemCredito);
    if ($rsSqlOrigemCredito) {
      $iLinhasOrigemCredito = pg_num_rows($rsSqlOrigemCredito);
      $aCreditos = array();
      for ($xInd = 0; $xInd < $iLinhasOrigemCredito; $xInd++) {
        $oOrigemCredito = db_utils::fieldsMemory($rsSqlOrigemCredito, $xInd);
        if (!empty($oOrigemCredito->k00_matric)) {
          $aCreditos[] = "M - " . $oOrigemCredito->k00_matric;
        }
        if (!empty($oOrigemCredito->k00_inscr)) {
          $aCreditos[] = "I - " . $oOrigemCredito->k00_inscr;
        }
        if (empty($oOrigemCredito->k00_inscr) && empty($oOrigemCredito->k00_matric)) {
          $aCreditos[] = "C - " . $oOrigemCredito->k00_numcgm;
        }
      }
    }
    $aCreditos = array_unique($aCreditos);
    $oCredito->origemCredito = implode(" / ", $aCreditos);

    $pdf->SetFont('arial', '', 6);

    $pdf->setx(5);
    $pdf->Cell(15, 04, $oCredito->status, 0, 0, "C", 0);
    $pdf->Cell(10, 04, $oCredito->k125_sequencial, 0, 0, "C", 0);
    $pdf->Cell(15, 04, $oCredito->origemCredito, 0, 0, "C", 0);
    $pdf->Cell(15, 04, $oCredito->k125_datalanc, 0, 0, "C", 0);
    $pdf->Cell(15, 04, $oCredito->k00_numpre, 0, 0, "C", 0);
    $pdf->Cell(20, 04, db_formatar($oCredito->total_recibo, 'f'), 0, 0, "C", 0);
    $pdf->Cell(25, 04, db_formatar($nValorCorrigido, 'f'), 0, 0, "C", 0);
    $pdf->Cell(25, 04, db_formatar($oCredito->valor_utilizado, 'f'), 0, 0, "C", 0);
    $pdf->Cell(25, 04, db_formatar($oCredito->abatimento_valordisponivel, 'f'), 0, 0, "C", 0);
    $pdf->Cell(35, 04, db_formatar($oCredito->valor_disponivel_corrigido, 'f'), 0, 1, "C", 0);

    $totValorCredito    += $oCredito->total_recibo;
    $totValorCorrigido  += $nValorCorrigido;
    $totValorUtilizado  += $oCredito->valor_utilizado;
    $totValorDisponivel += $oCredito->abatimento_valordisponivel;
    $totValorDisponivelCorrigido += $oCredito->valor_disponivel_corrigido;
  }
}

$pdf->setx(5);
$pdf->SetFont('arial', 'B', 6);
$pdf->cell(70, 4, 'TOTAL', 1, 0, "L", 0);
$pdf->cell(20, 04, db_formatar($totValorCredito, 'f'), 1, 0, "C", 0);
$pdf->cell(25, 04, db_formatar($totValorCorrigido, 'f'), 1, 0, "C", 0);
$pdf->cell(25, 04, db_formatar($totValorUtilizado, 'f'), 1, 0, "C", 0);
$pdf->cell(25, 04, db_formatar($totValorDisponivel, 'f'), 1, 0, "C", 0);
$pdf->cell(35, 04, db_formatar($totValorDisponivelCorrigido, 'f'), 1, 0, "C", 0);


$pdf->Output();
