<?php
/*
 * E-cidade Software Publico para Gestao Municipal
 * Copyright (C) 2009 DBSeller Servicos de Informatica
 * www.dbseller.com.br
 * e-cidade@dbseller.com.br
 *
 * Este programa e software livre; voce pode redistribui-lo e/ou
 * modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 * publicada pela Free Software Foundation; tanto a versao 2 da
 * Licenca como (a seu criterio) qualquer versao mais nova.
 *
 * Este programa e distribuido na expectativa de ser util, mas SEM
 * QUALQUER GARANTIA; sem mesmo a garantia implicita de
 * COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 * PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 * detalhes.
 *
 * Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 * junto com este programa; se nao, escreva para a Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307, USA.
 *
 * Copia da licenca no diretorio licenca/licenca_en.txt
 * licenca/licenca_pt.txt
 */
use ECidade\Pdf\Pdf;

require_once (modification("libs/db_stdlib.php"));
require_once (modification("libs/db_conecta.php"));
require_once (modification("libs/db_sql.php"));
require_once (modification("libs/db_utils.php"));
require_once (modification("libs/JSON.php"));

$oGet = db_utils::postMemory($_GET);

$oJson = new services_json();

$oParametros = $oJson->decode(str_replace("\\", "", $oGet->sParametros));

$clrotulo = new rotulocampo();
$clrotulo->label('r14_rubric');
$clrotulo->label('z01_nome');
$clrotulo->label('r01_regist');
$clrotulo->label('r14_quant');
$clrotulo->label('r14_valor');

$iInstituicao = db_getsession("DB_instit");

$sRubrica = $oParametros->iRubrica;
$iMes = $oParametros->iMes;
$iAno = $oParametros->iAno;
$sPonto = $oParametros->sPonto;
$lPeriodoAtual = $oParametros->sDadosCadastrais == "a" ? true : false;
$iCodigoSelecao = $oParametros->iSelecao;
$sTotalizacao = $oParametros->sTotalizacao;
$sPagina = $oParametros->sPagina;
$sTipo = $oParametros->sTipo;
$sOrdem = $oParametros->sOrdem;
$sTipoOrdem = $oParametros->sTipoOrdem;
$sLocalTrab = $oParametros->sLocalTrab;

$sDescricaoRubrica = '';

$head2 = "FINANCEIRA POR RUBRICA";
$head3 = "RUBRICA : " . $sRubrica . " - " . $sDescricaoRubrica;
$head4 = "PERÍODO : " . $iMes . " / " . $iAno;

/*
 * Se o tipo da folha for Salário ou Suplementar, os valores seram
 * retirados do histórico cálculo invéz da gerfsal.
 */
if ($sPonto == 's') {
    $sTabela = 'gerfsal';
    $sSigla = 'r14_';
    $head5 = 'PONTO : SALÁRIO';
    if (DBPessoal::verificarUtilizacaoEstruturaSuplementar()) {
        $sTabela = sql_gerfsal_ficticio(FolhaPagamento::TIPO_FOLHA_SALARIO, $sSigla);
    }
} elseif ($sPonto == 'c') {
    $sTabela = 'gerfcom';
    $sSigla = 'r48_';
    $head5 = 'PONTO : COMPLEMENTAR';
    if (DBPessoal::verificarUtilizacaoEstruturaSuplementar()) {
        $sTabela = sql_gerfsal_ficticio(FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR, $sSigla);
    }
} elseif ($sPonto == 'a') {
    $sTabela = 'gerfadi';
    $sSigla = 'r22_';
    $head5 = 'PONTO : ADIANTAMENTO';
} elseif ($sPonto == 'r') {
    $sTabela = 'gerfres';
    $sSigla = 'r20_';
    $head5 = 'PONTO : RESCISÃO';
} elseif ($sPonto == 'd') {
    $sTabela = 'gerfs13';
    $sSigla = 'r35_';
    $head5 = 'PONTO : 13o. SALÁRIO';
} elseif ($sPonto == 'u') {
    $sSigla = 'r14_';
    $head5 = 'PONTO : SUPLEMENTAR';
    $sTabela = sql_gerfsal_ficticio(FolhaPagamento::TIPO_FOLHA_SUPLEMENTAR, $sSigla);
}

$sWhere = "";

if (! empty($sRubrica)) {
    $oDaoRhRubricas = new cl_rhrubricas();
    $sSqlRhRubricas = $oDaoRhRubricas->sql_query_file($sRubrica, $iInstituicao, 'rh27_rubric, rh27_descr');
    $rsRhRubricas = $oDaoRhRubricas->sql_record($sSqlRhRubricas);
    if ($oDaoRhRubricas->numrows == 0) {
        db_redireciona("db_erros.php?fechar=true&db_erro=Rubrica não cadastrada no período de {$iMes} / {$iAno}");
    }
    $oRhRubrica = db_utils::fieldsMemory($rsRhRubricas, 0);
    $sWhere .= "and {$sSigla}rubric = '{$sRubrica}'";
}

if (! empty($iCodigoSelecao)) {
    $oDaoSelecao = new cl_selecao();
    $sSqlSelecao = $oDaoSelecao->sql_query_file($iCodigoSelecao, $iInstituicao);
    $rsSelecao = $oDaoSelecao->sql_record($sSqlSelecao);
    if ($oDaoSelecao->numrows > 0) {
        $sWhereSelecao = db_utils::fieldsMemory($rsSelecao, 0)->r44_where;
        $sWhere .= " and {$sWhereSelecao}";
    }
}

if ($sOrdem == 'a') {
    $head6 = 'ORDEM : ALFABÉTICA ' . strtoupper($sTipoOrdem);
    $sOrderBy = 'order by ' . $sSigla . 'rubric, z01_nome ' . $sTipoOrdem;
    if ($sLocalTrab == "S") {
        $sOrderByLocalTrab = 'order by rubric, rh55_estrut, z01_nome ' . $sTipoOrdem;
    }
} elseif ($sOrdem == 'n') {
    $head6 = 'ORDEM : NUMÉRICA ' . strtoupper($sTipoOrdem);
    $sOrderBy = 'order by ' . $sSigla . 'rubric, regist ' . $sTipoOrdem;
    if ($sLocalTrab == "S") {
        $sOrderByLocalTrab = 'order by rubric, rh55_estrut, regist ' . $sTipoOrdem;
    }
} elseif ($sOrdem == 'l') {
    $head6 = 'ORDEM : LOTAÇÃO ' . strtoupper($sTipoOrdem);
    $sOrderBy = 'order by ' . $sSigla . 'rubric, lotacao ' . $sTipoOrdem . ',z01_nome ';
    if ($sLocalTrab == "S") {
        $sOrderByLocalTrab = 'order by rubric, rh55_estrut, lotacao ' . $sTipoOrdem . ',z01_nome ';
    }
} elseif ($sOrdem == 'v') {
    $head6 = 'ORDEM : VALOR ' . strtoupper($sTipoOrdem);
    $sOrderBy = 'order by ' . $sSigla . 'rubric, valor ' . $sTipoOrdem;
    if ($sLocalTrab == "S") {
        $sOrderByLocalTrab = 'order by rubric, rh55_estrut, valor ' . $sTipoOrdem;
    }
} elseif ($sOrdem == 'q') {
    $head6 = 'ORDEM : QUANTIDADE ' . strtoupper($sTipoOrdem);
    $sOrderBy = 'order by ' . $sSigla . 'rubric, quant ' . $sTipoOrdem;
    if ($sLocalTrab == "S") {
        $sOrderByLocalTrab = 'order by rubric, rh55_estrut, quant ' . $sTipoOrdem;
    }
} elseif ($sOrdem == 'r') {
    $head6 = 'ORDEM : RECURSO ' . strtoupper($sTipoOrdem);
    $sOrderBy = 'order by ' . $sSigla . 'rubric, rh25_recurso ' . $sTipoOrdem . ', z01_nome ';
    if ($sLocalTrab == "S") {
        $sOrderByLocalTrab = 'order by rubric, rh55_estrut, rh25_recurso ' . $sTipoOrdem . ', z01_nome ';
    }
}

$oDaoRhPessoalMov = new cl_rhpessoalmov();
$oRhPesLocalTrab = new cl_rhpeslocaltrab();

if (DBPessoal::utilizaFiltroLotacoesPorUsuario()) {
    $oLotacoesUsuario = DBPessoal::buscaLotacoesPorUsuario();
    $sWhere .= " and rhpessoalmov.rh02_lota in (".implode(",",$oLotacoesUsuario->aLotacoes).")";
}

$sSqlFinanceiro = $oDaoRhPessoalMov->sql_queryFinanceiroPeloCodigo($iAno, $iMes, $sTabela, $sSigla, $sWhere, $sOrderBy, $lPeriodoAtual);
/*
 * Caso seja marcada a opção de mostrar os dados dos locais de trabalho
 * Manipulamos a query adicionando a busca nas tabelas rhpeslocaltrab e rhlocaltrab
 */
if ($sLocalTrab == "S") {
    $sSqlFinanceiro = " select dados.*,
                               rhlocaltrab.rh55_estrut,
                               rhlocaltrab.rh55_descr
                          from ({$sSqlFinanceiro}) as dados
                         inner join rhpessoalmov    on rhpessoalmov.rh02_regist = dados.regist
                                                   and rhpessoalmov.rh02_mesusu = {$iMes}
                                                   and rhpessoalmov.rh02_anousu = {$iAno}
                          left join rhpeslocaltrab  on rhpeslocaltrab.rh56_seqpes = rhpessoalmov.rh02_seqpes
                          left join rhlocaltrab     on rhlocaltrab.rh55_codigo = rhpeslocaltrab.rh56_localtrab
                                                   and rhlocaltrab.rh55_instit = rhpessoalmov.rh02_instit
                         {$sOrderByLocalTrab}";
}

$rsFinanceiro = $oDaoRhPessoalMov->sql_record($sSqlFinanceiro);
if ($oDaoRhPessoalMov->numrows == 0) {
    if ($sTipo == 'r') {
        db_redireciona('db_erros.php?fechar=true&db_erro=Não existem Cálculo no período de ' . $iMes . ' / ' . $iAno);
    } else {
        db_msgbox("Não existem Cálculo no período de {$iMes} / {$iAno}");
        exit;
    }
}

db_fieldsmemory($rsFinanceiro, 0);

$xxrubrica = $rubric;
$xxlocaltrab = null;

if ($sTipo == 'r') {

    $pdf = new Pdf();
    $pdf->addTitulo($head2,2);
    $pdf->addTitulo($head3,3);
    $pdf->addTitulo($head4,4);
    $pdf->addTitulo($head5,5);
    $pdf->addTitulo($head6,6);
    $pdf->AliasNbPages();
    $pdf->setfillcolor(235);
    $pdf->init(false);
    $pdf->setfont('arial', 'b', 8);
    $pdf->setExibeBrasao(true);
    $total = 0;
    $troca = 1;
    $alt = 4;
    $xvalor = 0;
    $xquant = 0;
    $total = 0;
    $quebra = '';
    $t_quant = 0;
    $t_valor = 0;
    $t_func = 0;
    $tr_quant = 0;
    $tr_valor = 0;
    $tr_func = 0;

    if ($sPagina == 'p') {
        $tot_espaco = 229;
    } else {
        $tot_espaco = 152;
    }

    for ($x = 0; $x < pg_numrows($rsFinanceiro); $x ++) {

        db_fieldsmemory($rsFinanceiro, $x);

        /**
         * pd = 2 = registro de desconto
         * Caso seja desconto é abatido o valor do somatório
         */
        if ($pd == '2') { // desconto
            $valor *= - 1;
        }


        if ($quebra != $rh25_recurso && $sOrdem == 'r') {


            $iProximoRecurso = db_utils::fieldsMemory($rsFinanceiro, $x + 1)->rh25_recurso;

            $pdf->setfont('arial', 'b', 8);

            if ($x != 0) {

                $pdf->cell($tot_espaco, $alt, 'TOTAL  :  ' . $t_func . '  FUNCIONÁRIO(S)', 'T', 0, "L", 0);
                $pdf->cell(15, $alt, db_formatar($t_quant, 'f'), 'T', 0, "R", 0);
                $pdf->cell(25, $alt, db_formatar(abs($t_valor), 'f'), 'T', 1, "R", 0);
                $pdf->addPage("L");

                if ($iProximoRecurso == $rh25_recurso) {

                    $pdf->cell(20, $alt, 'RECURSO: ', 0, 0, "L", 0);
                    $pdf->cell(10, $alt, $rh25_recurso, 0, 0, "R", 0);
                    $pdf->cell(60, $alt, $o15_descr, 0, 1, "L", 0);
                }

            }

            $quebra = $rh25_recurso;
            $pdf->ln(3);

            $t_quant = 0;
            $t_valor = 0;
            $t_func = 0;
        }

        if ($xxrubrica != $rubric) {
            $troca = 1;
            $pdf->cell($tot_espaco, $alt, 'TOTAL  :  ' . $tr_func . '  FUNCIONÁRIO(S)', 'T', 0, "L", 0);
            $pdf->cell(15, $alt, db_formatar($tr_quant, 'f'), 'T', 0, "R", 0);
            $pdf->cell(25, $alt, db_formatar(abs($tr_valor), 'f'), 'T', 1, "R", 0);

            $pdf->addPage("L");
            $tr_quant = 0;
            $tr_valor = 0;
            $tr_func = 0;
            $xxrubrica = $rubric;
        }

        if ($pdf->getY() > $pdf->getH() - 30 || $troca != 0) {

            if ($sPagina == 'p') {
                $pdf->addpage("L");
            } else {
                $pdf->addpage();
            }

            if ($sTotalizacao == 'a') {

                $pdf->setfont('arial', 'b', 8);
                $pdf->cell(15, $alt, "Matrícula", 1, 0, "C", 1);
                $pdf->cell(60, $alt, $RLz01_nome, 1, 0, "C", 1);
                $pdf->cell(15, $alt, 'LOTAÇÃO', 1, 0, "C", 1);
                $pdf->cell(62, $alt, 'DESCRICAO', 1, 0, "C", 1);

                if ($sPagina == 'p') {

                    $pdf->cell(15, $alt, 'CARGO', 1, 0, "C", 1);
                    $pdf->cell(62, $alt, 'DESCRICAO', 1, 0, "C", 1);
                }

                $pdf->cell(15, $alt, 'QUANT', 1, 0, "C", 1);
                $pdf->cell(25, $alt, 'VALOR', 1, 1, "C", 1);
            }

            $pdf->setfont('arial', 'b', 9);
            $pdf->cell(20, $alt, 'RUBRICA : ', 0, 0, "L", 0);
            $pdf->cell(10, $alt, $rubric, 0, 0, "R", 0);
            $pdf->cell(60, $alt, $rh27_descr, 0, 1, "L", 0);

            /*
             * Mostramos os dados do local de trabalho do servidor
             */
            if ($sLocalTrab == "S") {
                $pdf->setfont('arial', 'b', 9);
                $pdf->cell(43, $alt, 'LOCAL DE TRABALHO : ', 0, 0, "L", 0);
                $sLocalTrabDescricao = $rh55_estrut." - ".$rh55_descr;
                if (empty($rh55_estrut)) {
                  $sLocalTrabDescricao = "Não Informado";
                }
                $pdf->cell(140, $alt, $sLocalTrabDescricao, 0, 1, "L", 0);

                $xxlocaltrab = $rh55_estrut;
            }

            if ($sOrdem == 'r') {
                $pdf->setfont('arial', 'b', 9);
                $pdf->cell(20, $alt, 'RECURSO : ', 0, 0, "L", 0);
                $pdf->cell(10, $alt, $rh25_recurso, 0, 0, "R", 0);
                $pdf->cell(60, $alt, $o15_descr, 0, 1, "L", 0);
            }
            $troca = 0;
            $pre = 1;
        }

        /*
         * Mostramos os dados do local de trabalho do servidor
         */
        if ($sLocalTrab == "S" && $xxlocaltrab != $rh55_estrut) {
            $pdf->setfont('arial', 'b', 9);
            $pdf->cell(43, $alt, 'LOCAL DE TRABALHO : ', 0, 0, "L", 0);
            $sLocalTrabDescricao = $rh55_estrut." - ".$rh55_descr;
            if (empty($rh55_estrut)) {
                $sLocalTrabDescricao = "Não Informado";
            }
            $pdf->cell(140, $alt, $sLocalTrabDescricao, 0, 1, "L", 0);

            $xxlocaltrab = $rh55_estrut;
        }

        if ($sTotalizacao == 'a') {

            if ($pre == 1) {
              $pre = 0;
            } else {
                $pre = 1;
            }

            $pdf->setfont('arial', '', 7);
            $pdf->cell(15, $alt, $regist, 0, 0, "C", $pre);
            $pdf->cell(60, $alt, $z01_nome, 0, 0, "L", $pre);
            $pdf->cell(15, $alt, $lotacao, 0, 0, "C", $pre);
            $pdf->cell(62, $alt, substr(trim($descricao), 0, 40), 0, 0, "L", $pre);

            if ($sPagina == 'p') {
                $pdf->cell(15, $alt, $cargo, 0, 0, "C", $pre);
                $pdf->cell(62, $alt, substr(trim($desc_cargo), 0, 40), 0, 0, "L", $pre);
            }

            $pdf->cell(15, $alt, db_formatar($quant, 'f'), 0, 0, "R", $pre);
            $pdf->cell(25, $alt, db_formatar(abs($valor), 'f'), 0, 1, "R", $pre);
        }

        $t_valor += $valor;
        $t_quant += $quant;
        $t_func += 1;
        $tr_valor += $valor;
        $tr_quant += $quant;
        $tr_func += 1;
        $xvalor += $valor;
        $xquant += $quant;
        $total += 1;
    }

    if ($sOrdem == 'r' && $x != 0) {

        $pdf->setfont('arial', 'b', 8);
        $quebra = $rh25_recurso;
        $pdf->cell($tot_espaco, $alt, 'TOTAL  :  ' . $t_func . '  FUNCIONÁRIO(S)', 'T', 0, "L", 0);
        $pdf->cell(15, $alt, db_formatar($t_quant, 'f'), 'T', 0, "R", 0);
        $pdf->cell(25, $alt, db_formatar(abs($t_valor), 'f'), 'T', 1, "R", 0);
        $pdf->ln(3);
        $t_quant = 0;
        $t_valor = 0;
        $t_func = 0;
    }

    $pdf->cell($tot_espaco, $alt, 'TOTAL  :  ' . $tr_func . '  FUNCIONÁRIO(S)', 'T', 0, "L", 0);
    $pdf->cell(15, $alt, db_formatar($tr_quant, 'f'), 'T', 0, "R", 0);
    $pdf->cell(25, $alt, db_formatar(abs($tr_valor), 'f'), 'T', 1, "R", 0);
    $pdf->ln(5);
    $pdf->setfont('arial', 'b', 8);
    $pdf->cell($tot_espaco, $alt, 'TOTAL  :  ' . $total . '  FUNCIONÁRIO(S)', "T", 0, "C", 0);
    $pdf->cell(15, $alt, db_formatar($xquant, 'f'), "T", 0, "R", 0);
    $pdf->cell(25, $alt, db_formatar(abs($xvalor), 'f'), "T", 1, "R", 0);

    $pdf->Output();
    db_msgbox('Relatório Gerado com Sucesso');

} elseif ($sTipo == 'a') {

    $str_arquivo = "tmp/{$sRubrica}_{$iAno}{$iMes}.txt";
    $nomearq = $str_arquivo;
    $fl_arquivo = fopen($str_arquivo, "w");

    $str_dados = chr(15) . $head2 . "\n" . $head3 . "\n" . $head4 . "\n";
    fputs($fl_arquivo, $str_dados);

    $str_dados = str_pad(" Matrícula", 13)
                 . " " . str_pad($RLz01_nome, 45)
                 . " " . str_pad("QUANTIDADE", 15)
                 . " " . str_pad("VALOR", 10)
                 . " " . str_pad("LOTACAO", 10)
                 . " " . str_pad("DESCRICAO", 40)
                 . " " . str_pad("CARGO", 9)
                 . " " . str_pad("DESCRICAO", 35);
    if ($sLocalTrab == "S") {
       $str_dados .= " " . str_pad("LOCAL DE TRABALHO", 40);
    }

    $sLinhaSeparadora  = "+".str_repeat("-",11)."+".str_repeat("-",44)."+".str_repeat("-",14)."+".str_repeat("-",11);
    $sLinhaSeparadora .= "+".str_repeat("-",10)."+".str_repeat("-",40)."+".str_repeat("-",8)."+".str_repeat("-",34);
    if ($sLocalTrab == "S") {
       $sLinhaSeparadora .= "+".str_repeat("-",39);
    }
    $sLinhaSeparadora .= "\n";
    fputs($fl_arquivo, $sLinhaSeparadora);
    fputs($fl_arquivo, $str_dados . "\n");
    fputs($fl_arquivo, $sLinhaSeparadora);

    $troca = 1;
    $alt = 4;
    $xvalor = 0;
    $xquant = 0;
    $total = 0;
    for ($x = 0; $x < pg_numrows($rsFinanceiro); $x ++) {
        db_fieldsmemory($rsFinanceiro, $x);

        if ($pd == '2') { // desconto
            $valor *= - 1;
            $quant *= - 1;
        }

        $str_dados = str_pad($regist . "-" . modulo11($regist), 12, " ", STR_PAD_LEFT)
                     . " " . str_pad($z01_nome, 42, " ")
                     . " " . str_pad(trim(db_formatar($quant, 'f')), 15, " ", STR_PAD_LEFT)
                     . " " . str_pad(trim(db_formatar($valor, 'f')), 12, " ", STR_PAD_LEFT)
                     . " " . str_pad($lotacao, 10, "0", STR_PAD_LEFT)
                     . " " . str_pad(substr(trim($descricao), 0, 29), 40)
                     . " " . str_pad($cargo, 8, "0", STR_PAD_LEFT)
                     . " " . str_pad(substr(trim($desc_cargo), 0, 30), 34," ", STR_PAD_LEFT);
        if ($sLocalTrab == "S") {
           $str_dados .= " " . (empty($rh55_estrut)?"Não Informado":$rh55_estrut." - ".$rh55_descr);
        }
        fputs($fl_arquivo, $str_dados . "\n");

        $xvalor += $valor;
        $xquant += $quant;
        $total += 1;
    }
    fputs($fl_arquivo, $sLinhaSeparadora);
    $str_dados = str_pad("TOTAL : " . $total . "  FUNCIONÁRIO(S)", 50)
                 . " " . str_pad(trim(db_formatar($xquant, 'f')), 13, " ", STR_PAD_LEFT)
                 . " " . str_pad(trim(db_formatar($xvalor, 'f')), 11, " ", STR_PAD_LEFT);
    fputs($fl_arquivo, $str_dados . chr(18) . "\n");

    echo "
     <script>
      parent.js_detectaarquivo('$nomearq');
     </script>
       ";
} elseif ($sTipo == 'p') {
    $str_arquivo = "tmp/{$sRubrica}_{$iAno}{$iMes}.csv";
    $nomearq = $str_arquivo;
    $fl_arquivo = fopen($str_arquivo, "w+");

    $str_dados = "RUBRICA ;" . $sRubrica . "-" . $sDescricaoRubrica;
    fputs($fl_arquivo, $str_dados . "\n\n");

    $str_dados = "MATRICULA" . ";"
               . "NOME" . ";"
               . "QUANTIDADE" . ";"
               . "VALOR" . ";"
               . "LOTACAO" . ";"
               . "DESCRICAO" . ";"
               . "CARGO" . ";"
               . "DESCRICAO" . ";";
    if ($sLocalTrab == "S") {
       $str_dados .= "LOCAL DE TRABALHO";
    }
    fputs($fl_arquivo, $str_dados . "\n");

    $troca = 1;
    $alt = 4;
    $xvalor = 0;
    $xquant = 0;
    $total = 0;
    for ($x = 0; $x < pg_numrows($rsFinanceiro); $x ++) {
        db_fieldsmemory($rsFinanceiro, $x);

        if ($pd == '2') { // desconto
            $valor *= - 1;
            $quant *= - 1;
        }

        $str_dados = $regist . ";"
                   . $z01_nome . ";"
                   . trim(db_formatar($quant, 'f')) . ";"
                   . trim(db_formatar($valor, 'f')) . ";"
                   . $lotacao . ";"
                   . substr(trim($descricao), 0, 29) . ";"
                   . $cargo . "; "
                   . substr(trim($desc_cargo), 0, 29). ";";
        if ($sLocalTrab == "S") {
           $str_dados .= (empty($rh55_estrut)?"Não Informado":$rh55_estrut." - ".$rh55_descr);
        }
        fputs($fl_arquivo, $str_dados . "\n");

        $xvalor += $valor;
        $xquant += $quant;
        $total += 1;
    }
    fclose($fl_arquivo);

    echo "
     <script>
      parent.js_detectaarquivo1('$nomearq');
     </script>
       ";
}

// modulo 11
function modulo11($num, $base = 9, $r = 0)
{
    $soma = 0;
    $fator = 2;
    $xx = strlen($num);
    /* Separacao dos numeros */
    for ($i = strlen($num); $i > 0; $i --) {
        // pega cada numero isoladamente
        $numeros[$i] = substr($num, $i - 1, 1);
        // Efetua multiplicacao do numero pelo falor
        $parcial[$i] = $numeros[$i] * $fator;
        // Soma dos digitos
        $soma += $parcial[$i];
        if ($fator == $base) {
            // restaura fator de multiplicacao para 1
            $fator = 1;
        }
        $fator ++;
    }
    if ($r == 0) {
        $soma *= 10;
        $digito = $soma % 11;
        if ($digito == 10) {
            $digito = 0;
        }
        return $digito;
    } elseif ($r == 1) {
        $resto = $soma % 11;
        return $resto;
    }
}

/**
 * Constrói uma query onde irá retornar a gersfal utilizando os valores da tabela rhhistoricocalculo
 *
 * @param Integer $iTipoFolha
 * @param String $sSigla
 * @return String
 */
function sql_gerfsal_ficticio($iTipoFolha, $sSigla)
{
    $sSql = "(select rh143_rubrica          as {$sSigla}rubric,                                                      \n";
    $sSql .= "        rh143_regist           as {$sSigla}regist,                                                      \n";
    $sSql .= "        sum(rh143_valor)       as {$sSigla}valor,                                                       \n";
    $sSql .= "        sum(rh143_quantidade)  as {$sSigla}quant,                                                       \n";
    $sSql .= "        rh141_anousu           as {$sSigla}anousu,                                                      \n";
    $sSql .= "        rh141_mesusu           as {$sSigla}mesusu,                                                      \n";
    $sSql .= "        rh141_instit           as {$sSigla}instit,                                                      \n";
    $sSql .= "        rh143_tipoevento       as {$sSigla}pd                                                           \n";
    $sSql .= "  from rhhistoricocalculo                                                                               \n";
    $sSql .= "    inner join rhfolhapagamento                                                                         \n";
    $sSql .= "      on rhfolhapagamento.rh141_sequencial = rhhistoricocalculo.rh143_folhapagamento                    \n";
    $sSql .= "    inner join rhtipofolha                                                                              \n";
    $sSql .= "      on rhtipofolha.rh142_sequencial = rhfolhapagamento.rh141_tipofolha                                \n";
    $sSql .= "  where rh142_sequencial = {$iTipoFolha}                                                                \n";
    $sSql .= "      group by rh143_rubrica, rh143_regist, rh141_anousu, rh141_mesusu, rh141_instit, rh143_tipoevento) \n";
    $sSql .= "as gerfsal                                                                                              \n";

    return $sSql;
}
