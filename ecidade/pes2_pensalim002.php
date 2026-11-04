<?php
/**
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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sql.php");
require_once modification("classes/db_selecao_classe.php");

$oGet = db_utils::postMemory($_GET);

$iInstituicao = db_getsession('DB_instit');
$iMesFolha    = DBPessoal::getMesFolha();
$iAnoFolha    = DBPessoal::getAnoFolha();
$sWhere       = '';

/**
 * Define cabeçalho
 */
$head2 = "Resumo de Pensões Alimentícias";
$head4 = "Período: {$oGet->mes}/{$oGet->ano}";

switch ($oGet->tipo) {
  case 's':
    $head6      = 'Salário';
    $sValor     = 'r52_valor + r52_valfer';
    $iTipoFolha = FolhaPagamento::TIPO_FOLHA_SALARIO;
    break;

  case 'c':
    $head6      = 'Complementar';
    $sValor     = 'r52_valcom';
    $iTipoFolha = FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR;
    break;

  case '3':
    $head6  = '13º. Salário';
    $sValor = 'r52_val13';
    break;

  case 'r':
    $head6  = 'Rescisão';
    $sValor = 'r52_valres';
    break;

  case 'u':
    $head6      = 'Suplementar';
    $sValor     = 'r52_valor + r52_valfer';
    $iTipoFolha = FolhaPagamento::TIPO_FOLHA_SUPLEMENTAR;
    break;
}

$head7 = "";
if (!empty($oGet->selecao)) {

  $oDaoSelecao = new cl_selecao;

  $sSql     = $oDaoSelecao->sql_query_file($oGet->selecao, $iInstituicao);
  $rsResult = $oDaoSelecao->sql_record($sSql);

  if ($oDaoSelecao->numrows) {

    $oSelecao = db_utils::fieldsMemory($rsResult, 0);
    $sWhere   = "AND {$oSelecao->r44_where}";

    $head7    = "Seleção {$oGet->selecao} - {$oSelecao->r44_descr}";
  }
}

if ($oGet->ordem == 'n') {
  $sOrder = 'codigo_banco, rh01_regist';
} else {
  if ($oGet->func == 's') {
    $sOrder = 'codigo_banco, r70_estrut, z01_nome, codigo_banco, codigo_agencia';
  } else {
    $sOrder = 'codigo_banco, codigo_agencia, r70_estrut, nome_beneficiario';
  }
}

if (!empty($oGet->bancos)) {
    $sWhere .= " AND cast(trim(db90_codban) as integer) in ({$oGet->bancos})";
    $head8 = "Bancos selecionados: {$oGet->bancos}";
    $sOrder = "codigo_banco, r70_estrut, nome_beneficiario, z01_nome";
}

if (DBPessoal::utilizaFiltroLotacoesPorUsuario()) {
    $oLotacoesUsuario = DBPessoal::buscaLotacoesPorUsuario();
    $sWhere .= " and rhpessoalmov.rh02_lota in (".implode(",",$oLotacoesUsuario->aLotacoes).")";
}

$sGroup  = 'descricao_banco, codigo_banco, codigo_agencia, r52_dvagencia, conta, r52_dvconta, ';
$sGroup .= 'cgm_beneficiario, nome_beneficiario, cpf_beneficiario, r70_estrut, r70_descr, rh01_regist, ';
$sGroup .= 'x.z01_nome,    x.w01_work05,   cpf,     x.w02_work05,  r52_observacao, r52_cgmalimentado ';

// Verificar se existe rubrica de pensão alimenticia
$sSqlPensaoAlimenticia = "select r11_palime from cfpess where r11_anousu = {$iAnoFolha} and r11_mesusu = {$iMesFolha} and r11_instit = {$iInstituicao}";
$rsPensaoAlimenticia = db_query($sSqlPensaoAlimenticia);

if (!pg_num_rows($rsPensaoAlimenticia)) {
  db_redireciona("db_erros.php?fechar=true&db_erro= Não existe rubrica pensão alimenticia cadastrada");
}

$oPensaoAlimenticia = db_utils::fieldsMemory($rsPensaoAlimenticia,0);
$oRubricaPensaoAlimenticia = substr_replace($oPensaoAlimenticia->r11_palime, '4', 0, 1);

if (DBPessoal::verificarUtilizacaoEstruturaSuplementar() && isset($iTipoFolha)) {

  $sSql = "
    SELECT *
      FROM (
        SELECT CASE WHEN trim(r52_codbco) = '' OR r52_codbco IS NULL THEN '000'
                    ELSE r52_codbco
               END                     AS codigo_banco,
               CASE WHEN db90_descr IS NOT NULL THEN db90_descr
                    ELSE 'SEM BANCO'
               END                     AS descricao_banco,
               to_char(to_number(CASE WHEN trim(r52_codage) = '' THEN '0'
                    ELSE r52_codage
               END, '99999'), '99999') AS codigo_agencia,
               CASE WHEN r52_dvagencia IS NULL THEN ''
                    ELSE r52_dvagencia
               END                     AS r52_dvagencia,
               r52_conta               AS conta,
               CASE WHEN r52_dvconta IS NULL THEN ''
                    ELSE r52_dvconta
               END                     AS r52_dvconta,
               r52_numcgm              AS cgm_beneficiario,
               cgm.z01_nome            AS nome_beneficiario,
               cgm.z01_cgccpf          AS cpf_beneficiario,
               a.z01_nome,
               a.z01_cgccpf AS cpf,
               r70_estrut,
               r70_descr,
               rh01_regist,
               (
                 SELECT sum(rh145_valor)
                   FROM rhhistoricopensao
                        INNER JOIN rhfolhapagamento ON rh145_rhfolhapagamento = rh141_sequencial

                  WHERE rh141_anousu    = r52_anousu
                    AND rh141_mesusu    = r52_mesusu
                    AND rh141_instit    = rh02_instit
                    AND rh141_tipofolha = {$iTipoFolha}
                    AND rh145_pensao    = r52_sequencial
               )                       AS w01_work05,
               r52_observacao,
               r52_cgmalimentado
          FROM pensao
               INNER JOIN cgm          ON r52_numcgm              = z01_numcgm
               INNER JOIN rhpessoal    ON rh01_regist             = r52_regist
               INNER JOIN rhpessoalmov ON rh01_regist             = rh02_regist
                                      AND rh02_anousu             = {$iAnoFolha}
                                      AND rh02_mesusu             = {$iMesFolha}
                                      AND rh02_instit             = {$iInstituicao}
               LEFT JOIN gerfres       ON r20_regist              = rh02_regist
                                      AND r20_rubric              = '{$oRubricaPensaoAlimenticia}'
               INNER JOIN rhlota       ON r70_codigo              = rh02_lota
                                      AND r70_instit              = rh02_instit
               INNER JOIN cgm AS a     ON a.z01_numcgm            = rh01_numcgm
               LEFT  JOIN db_bancos    ON r52_codbco::varchar(10) = db90_codban
         WHERE r52_anousu = {$oGet->ano}
           AND r52_mesusu = {$oGet->mes}
               {$sWhere}
      ) AS x
     WHERE w01_work05 > 0
     GROUP BY {$sGroup}
     ORDER BY {$sOrder}
  ";
} else {

  $sSql = "
    SELECT *
      FROM (
        SELECT CASE WHEN trim(r52_codbco) = '' OR r52_codbco IS NULL THEN '000'
                    ELSE r52_codbco
               END                     AS codigo_banco,
               CASE WHEN db90_descr IS NOT NULL THEN db90_descr
                    ELSE 'SEM BANCO'
               END                     AS descricao_banco,
               to_char(to_number(CASE WHEN trim(r52_codage) = '' THEN '0'
                    ELSE r52_codage
               END, '99999'), '99999') AS codigo_agencia,
               CASE WHEN r52_dvagencia IS NULL THEN ''
                    ELSE r52_dvagencia
               END                     AS r52_dvagencia,
               r52_conta               AS conta,
               CASE WHEN r52_dvconta IS NULL THEN ''
                    ELSE r52_dvconta
               END                     AS r52_dvconta,
               r52_numcgm              AS cgm_beneficiario,
               cgm.z01_nome            AS nome_beneficiario,
               cgm.z01_cgccpf          AS cpf_beneficiario,
               a.z01_nome,
               a.z01_cgccpf AS cpf,
               rh01_regist,
               r70_estrut,
               r70_descr,
               {$sValor}               AS w01_work05,
               r20_valor               AS w02_work05,
               r52_observacao,
               r52_cgmalimentado
          FROM pensao
            INNER JOIN cgm          ON   r52_numcgm              =  z01_numcgm
            INNER JOIN rhpessoal    ON  rh01_regist              =  r52_regist
            INNER JOIN rhpessoalmov ON  rh01_regist              = rh02_regist
                                   AND  rh02_anousu              = {$iAnoFolha}
                                   AND  rh02_mesusu              = {$iMesFolha}
                                   AND  rh02_instit              = {$iInstituicao}
            LEFT JOIN gerfres       ON r20_regist                = rh02_regist
                                   AND r20_rubric                = '{$oRubricaPensaoAlimenticia}'
            INNER JOIN rhlota       ON   r70_codigo              = rh02_lota
                                   AND   r70_instit              = rh02_instit
            INNER JOIN cgm AS a     ON a.z01_numcgm              = rh01_numcgm
            LEFT  JOIN db_bancos    ON   r52_codbco::varchar(10) = db90_codban
         WHERE r52_anousu = {$oGet->ano}
           AND r52_mesusu = {$oGet->mes}
           AND {$sValor}  > 0
               {$sWhere}
      ) AS x
     GROUP BY {$sGroup}
     ORDER BY {$sOrder}
  ";
}

$rsResult = db_query($sSql);
if (!pg_num_rows($rsResult)) {
  db_redireciona("db_erros.php?fechar=true&db_erro=Não existem lançamentos no período de {$oGet->mes}/{$oGet->ano}");
}

$oPDF = new Pdf();
$oPDF->addTitulo($head2,2);
$oPDF->addTitulo($head4,4);
$oPDF->addTitulo($head6,6);
$oPDF->addTitulo($head7,7);
$oPDF->addTitulo($head8,8);
$oPDF->AliasNbPages();
$oPDF->setfillcolor(235);

$oPDF->init(false);

$oPDF->setfont('arial', 'b', 8);

$alt     = 5;
$total   = 0;
$total_g = 0;

$banco_atual = "";
$lotacao_atual = "";

db_fieldsmemory($rsResult,0);

if($oGet->tipoquebra == 'a'){
    $quebra = substr($codigo_banco,0,3).$codigo_agencia;
}else{
    $quebra = substr($codigo_banco,0,3);
}
$troca = 0;

if($oGet->func != 's'){

  for($x = 0; $x < pg_num_rows($rsResult);$x++){

     db_fieldsmemory($rsResult,$x);

     if ($quebra != substr($codigo_banco,0,3).$codigo_agencia && $oGet->tipoquebra == 'a') {

        $oPDF->setfont('arial','b',8);
        $oPDF->cell(122,$alt,'Total da Agência',"T",0,"C",0);
        $oPDF->cell(40,$alt,'',"T",0,"C",0);
        $oPDF->cell(30,$alt,db_formatar($total,'f'),"T",1,"R",0);
        $oPDF->sety(300);
        $total = 0;
        $quebra = substr($codigo_banco,0,3).$codigo_agencia;
     }

     if ($quebra != substr($codigo_banco,0,3) && $oGet->tipoquebra != 'a') {

        $oPDF->setfont('arial','b',8);
        $oPDF->cell(122,$alt,'Total do Banco',"T",0,"C",0);
        $oPDF->cell(40,$alt,'',"T",0,"C",0);
        $oPDF->cell(30,$alt,db_formatar($total,'f'),"T",1,"R",0);
        $oPDF->sety(300);
        $total = 0;
        $quebra = substr($codigo_banco,0,3);
     }

     if ($oPDF->getY() > $oPDF->getH() - 30 || $troca == 0) {

        $oPDF->addpage();
        $oPDF->setfont('arial','b',8);
        if ($oGet->tipoquebra == 'a') {
          $oPDF->cell(80,$alt,$descricao_banco.' - Agência: '.$codigo_agencia,0,1,"L",0);
        } else {
          $oPDF->cell(80,$alt,$descricao_banco,0,1,"L",0);
        }
        $oPDF->ln(3);
        $oPDF->cell(102,$alt,'Nome do Beneficiário',1,0,"C",1);
        $oPDF->cell(20,$alt,'CPF',1,0,"C",1);
        $oPDF->cell(20,$alt,'Agência',1,0,"C",1);
        $oPDF->cell(20,$alt,'Conta',1,0,"C",1);
        $oPDF->cell(30,$alt,'Valor',1,1,"C",1);
        $troca = 1;
     }

     $oPDF->setfont('arial','',7);
     $oPDF->cell(102, $alt, $nome_beneficiario,0,0,"l",0);
     $oPDF->cell(20, $alt, db_formatar($cpf_beneficiario, "CPF"),0,0,"l",0);
     $oPDF->cell(20, $alt, $codigo_agencia.$r52_dvagencia, 0, 0, "R", 0);
     $oPDF->cell(20, $alt, $conta.$r52_dvconta, 0, 0, "R", 0);
     $oPDF->cell(30, $alt, db_formatar($w01_work05,'f'), 0, 1, "R", 0);
     if ($oGet->mostraobservacao == "t" && !empty($r52_observacao)) {
         $oPDF->multicell(192,$alt,"OBSERVAÇÃO: ".$r52_observacao,0);
     }

     $total   += $w01_work05;
     $total_g += $w01_work05;
  }

  $oPDF->setfont('arial','b',8);
  if ($oGet->tipoquebra == 'a') {
    $oPDF->cell(122,$alt,'Total da Agência',"T",0,"C",0);
  } else {
    $oPDF->cell(122,$alt,'Total do Banco',"T",0,"C",0);
  }
  $oPDF->cell(40,$alt,'',"T",0,"C",0);
  $oPDF->cell(30,$alt,db_formatar($total,'f'),"T",1,"R",0);

  $oPDF->ln(5);
  $oPDF->cell(122,$alt,'Total do Geral',"T",0,"C",0);
  $oPDF->cell(40,$alt,'',"T",0,"C",0);
  $oPDF->cell(30,$alt,db_formatar($total_g,'f'),"T",1,"R",0);

} else {

  for ($x = 0; $x < pg_num_rows($rsResult);$x++) {

      db_fieldsmemory($rsResult,$x);

      if ($quebra != substr($codigo_banco,0,3).$codigo_agencia && $oGet->tipoquebra == 'a') {

          $oPDF->setfont('arial','b',8);
          $oPDF->cell(249,$alt,"Total da Agência {$codigo_agencia}","T",0,"C",0);
          $oPDF->cell(25,$alt,db_formatar($total,'f'),"T",1,"R",0);
          $total = 0;
          $quebra = substr($codigo_banco,0,3).$codigo_agencia;
      }

      if ($quebra != substr($codigo_banco,0,3) && $oGet->tipoquebra != 'a') {

          $oPDF->setfont('arial','b',8);
          $oPDF->cell(249,$alt,"Total do Banco {$banco_atual}","T",0,"R",0);
          $oPDF->cell(25,$alt,db_formatar($total,'f'),"T",1,"R",0);
          $total = 0;
          $quebra = substr($codigo_banco,0,3);
      }

     if ($oPDF->getY() > $oPDF->getH() - 30 || $troca == 0) {

        $oPDF->addpage('L');
        $oPDF->setfont('arial','b',8);

        if ($banco_atual != $descricao_banco) {
            if ($oGet->tipoquebra == 'a') {
                $oPDF->cell(80,$alt,"BANCO: ".$descricao_banco.' - Agência: '.$codigo_agencia,0,1,"L",0);
            } else {
                $oPDF->cell(80,$alt,"BANCO: ".$descricao_banco,0,1,"L",0);
            }

        }

        if ($lotacao_atual != $r70_estrut || $banco_atual != $descricao_banco) {
          $oPDF->cell(80,$alt,"LOTAÇÃO: {$r70_estrut} - {$r70_descr}",0,1,"L",0);
        }
        $lotacao_atual = $r70_estrut;
        $banco_atual = $descricao_banco;

        if($oGet->tipo == 'r'){
          $oPDF->cell(12,$alt,'Matr',1,0,"C",1);
          $oPDF->cell(70,$alt,'Nome do Funcionário',1,0,"C",1);
          $oPDF->cell(12,$alt,'CGM',1,0,"C",1);
          $oPDF->cell(70,$alt,'Nome do Beneficiário',1,0,"C",1);
          $oPDF->cell(25,$alt,'CPF',1,0,"C",1);
          $oPDF->cell(10,$alt,'Banco',1,0,"C",1);
          $oPDF->cell(20,$alt,'Agência',1,0,"C",1);
          $oPDF->cell(20,$alt,'Conta',1,0,"C",1);
          $oPDF->cell(20,$alt,'Valor',1,0,"C",1);
          $oPDF->cell(20,$alt,'Resc. 13º',1,1,"C",1);

        } else{
          $oPDF->cell(12,$alt,'Matr',1,0,"C",1);
          $oPDF->cell(75,$alt,'Nome do Funcionário',1,0,"C",1);
          $oPDF->cell(12,$alt,'CGM',1,0,"C",1);
          $oPDF->cell(75,$alt,'Nome do Beneficiário',1,0,"C",1);
          $oPDF->cell(25,$alt,'CPF',1,0,"C",1);
          $oPDF->cell(10,$alt,'Banco',1,0,"C",1);
          $oPDF->cell(20,$alt,'Agência',1,0,"C",1);
          $oPDF->cell(20,$alt,'Conta',1,0,"C",1);
          $oPDF->cell(25,$alt,'Valor',1,1,"C",1);
        }

        $troca = 1;
     }


     if ($banco_atual != $descricao_banco) {
         $oPDF->ln(2);
         $oPDF->setfont('arial','b',8);

         if ($oGet->tipoquebra == 'a') {
             $oPDF->cell(80,$alt,"BANCO: ".$descricao_banco.' - Agência: '.$codigo_agencia,0,1,"L",0);
         } else {
             $oPDF->cell(80,$alt,"BANCO: ".$descricao_banco,0,1,"L",0);
         }
         $oPDF->setfont('arial','',7);
     }


     if ($lotacao_atual != $r70_estrut || $banco_atual != $descricao_banco) {

         $oPDF->ln(2);
         $oPDF->setfont('arial','b',8);
         if($oGet->tipo == 'r'){
            $oPDF->cell(80,$alt,"LOTAÇÃO: {$r70_estrut} - {$r70_descr}",0,1,"L",0);
            $oPDF->cell(12,$alt,'Matr',1,0,"C",1);
            $oPDF->cell(70,$alt,'Nome do Funcionário',1,0,"C",1);
            $oPDF->cell(12,$alt,'CGM',1,0,"C",1);
            $oPDF->cell(70,$alt,'Nome do Beneficiário',1,0,"C",1);
            $oPDF->cell(25,$alt,'CPF',1,0,"C",1);
            $oPDF->cell(10,$alt,'Banco',1,0,"C",1);
            $oPDF->cell(20,$alt,'Agência',1,0,"C",1);
            $oPDF->cell(20,$alt,'Conta',1,0,"C",1);
            $oPDF->cell(20,$alt,'Valor',1,0,"C",1);
            $oPDF->cell(20,$alt,'Resc. 13º',1,1,"C",1);
         } else{
            $oPDF->cell(80,$alt,"LOTAÇÃO: {$r70_estrut} - {$r70_descr}",0,1,"L",0);
            $oPDF->cell(12,$alt,'Matr',1,0,"C",1);
            $oPDF->cell(75,$alt,'Nome do Funcionário',1,0,"C",1);
            $oPDF->cell(12,$alt,'CGM',1,0,"C",1);
            $oPDF->cell(75,$alt,'Nome do Beneficiário',1,0,"C",1);
            $oPDF->cell(25,$alt,'CPF',1,0,"C",1);
            $oPDF->cell(10,$alt,'Banco',1,0,"C",1);
            $oPDF->cell(20,$alt,'Agência',1,0,"C",1);
            $oPDF->cell(20,$alt,'Conta',1,0,"C",1);
            $oPDF->cell(25,$alt,'Valor',1,1,"C",1);

         }
         $oPDF->setfont('arial','',8);
     }
     $lotacao_atual = $r70_estrut;
     $banco_atual = $descricao_banco;

     if($oGet->tipo == 'r'){
        $oPDF->setfont('arial','',7);
        $oPDF->cell(12,$alt,$rh01_regist,0,0,"C",0);
        $oPDF->cell(70,$alt,$z01_nome,0,0,"L",0);
        $oPDF->cell(12,$alt,$cgm_beneficiario,0,0,"C",0);
        $oPDF->cell(70,$alt,$nome_beneficiario,0,0,"L",0);
        $oPDF->cell(25,$alt,$cpf_beneficiario,0,0,"C",0);
        $oPDF->cell(10,$alt,$codigo_banco,0,0,"C",0);
        $oPDF->cell(20,$alt,$codigo_agencia.$r52_dvagencia,0,0,"C",0);
        $oPDF->cell(20,$alt,$conta.$r52_dvconta,0,0,"C",0);
        $oPDF->cell(20,$alt,db_formatar($w01_work05,'f'),0,0,"C",0);
        $oPDF->cell(20,$alt,db_formatar($w02_work05,'f'),0,1,"C",0);

     } else {
        $oPDF->setfont('arial','',7);
        $oPDF->cell(12,$alt,$rh01_regist,0,0,"C",0);
        $oPDF->cell(75,$alt,$z01_nome,0,0,"L",0);
        $oPDF->cell(12,$alt,$cgm_beneficiario,0,0,"C",0);
        $oPDF->cell(75,$alt,$nome_beneficiario,0,0,"L",0);
        $oPDF->cell(25,$alt,$cpf_beneficiario,0,0,"C",0);
        $oPDF->cell(10,$alt,$codigo_banco,0,0,"C",0);
        $oPDF->cell(20,$alt,$codigo_agencia.$r52_dvagencia,0,0,"C",0);
        $oPDF->cell(20,$alt,$conta.$r52_dvconta,0,0,"C",0);
        $oPDF->cell(25,$alt,db_formatar($w01_work05,'f'),0,1,"C",0);
     }


     if ($oGet->mostraobservacao == "t" && !empty($r52_observacao)) {
        $oPDF->multicell(270,$alt,"OBSERVAÇÃO: ".$r52_observacao,0);
     }

     if ($oGet->tipo == 'r'){
      $total += $w01_work05 + $w02_work05;
      $total_g += $w01_work05 + $w02_work05;

     } else{
      $total += $w01_work05;
      $total_g += $w01_work05;

     }

  }

  $oPDF->setfont('arial','b',8);
  $oPDF->cell(249,$alt,"Total do Banco {$banco_atual}","T",0,"R",0);
  $oPDF->cell(25,$alt,db_formatar($total,'f'),"T",1,"R",0);

  $oPDF->setfont('arial','b',8);
  $oPDF->ln(5);
  $oPDF->cell(249,$alt,'TOTAL GERAL',"T",0,"R",0);
  $oPDF->cell(25,$alt,db_formatar($total_g,'f'),"T",1,"R",0);
}

$sName = 'tmp/pensaoAlimenticia' . date('YmdHms') . '.pdf';
$oPDF->Output("I",$sName, false);
