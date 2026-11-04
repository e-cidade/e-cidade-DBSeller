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
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));

$clclabens = new cl_clabens;

$clrotulo = new rotulocampo;
$clrotulo->label('t64_class');
$clrotulo->label('t64_descr');
$clrotulo->label('c60_estrut');
$clrotulo->label('c60_descr');

parse_str($_SERVER['QUERY_STRING']);

$sCampos = "";
$sJoin = " inner join clabens         on clabens.t64_codcla          = bens.t52_codcla ";
$sJoin .= " inner join clabensconplano on clabensconplano.t86_clabens = clabens.t64_codcla ";
$sJoin .= "                              and clabensconplano.t86_anousu = " . db_getsession("DB_anousu");
$sJoin .= " inner join conplano        on conplano.c60_codcon         = clabensconplano.t86_conplano ";
$sJoin .= "                              and conplano.c60_anousu        = " . db_getsession("DB_anousu");
$sJoin .= " left join cgm              on cgm.z01_numcgm              = bens.t52_numcgm ";
$sJoin .= " inner join db_depart       on db_depart.coddepto          = bens.t52_depart ";
$sJoin .= " left join bensbaix         on bensbaix.t55_codbem         = bens.t52_bem ";
$sJoin .= " left join bensmater        on bensmater.t53_codbem        = bens.t52_bem ";
$sJoin .= " left join bensimoveis      on bensimoveis.t54_codbem      = bens.t52_bem ";
$sJoin .= " left join bensdiv          on bensdiv.t33_bem             = bens.t52_bem ";
$sJoin .= " left join departdiv        on bensdiv.t33_divisao         = departdiv.t30_codigo ";
$sJoin .= " left join db_departorg     on db01_coddepto               = coddepto ";
$sJoin .= "                              and db_departorg.db01_anousu = " . db_getsession("DB_anousu");
$sJoin .= " left join orcorgao         on db01_orgao                  = o40_orgao   ";
$sJoin .= "                              and db01_anousu                = o40_anousu  ";

$sJoin .= " inner join orcunidade on orcunidade.o41_unidade = db_departorg.db01_unidade ";
$sJoin .= "                        and orcunidade.o41_orgao   = db_departorg.db01_orgao   ";
$sJoin .= "                        and orcunidade.o41_anousu  = db_departorg.db01_anousu  ";
$sJoin .= "                        and orcunidade.o41_instit  = db_depart.instit ";

$sJoin .= "  left join benscedente     on t09_bem            = t52_bem";
$sJoin .= "  left join benscadcedente  on t09_benscadcedente = t04_sequencial";

$sOrderBy = "";
$sQuebrarApos = "";
$aCamposRelatorio = array();
$tamanhoValorAquisicaoAtual = 30;
$tamanhoValorTotal = 220;

switch ($tipoagrupa) {
    case 0:
        $sCampos = "t64_class,t64_descr,c60_estrut,c60_descr";
        $desc_tipo = "Classificação";
        $sOrderBy = "t64_class";
        $sGroupBy = $sCampos;

        $oCampo1 = new stdClass();
        $oCampo1->nome = "t64_class";
        $oCampo1->tamanho = 30;
        $oCampo1->descricao = $RLt64_class;
        $aCamposRelatorio[] = $oCampo1;

        $oCampo2 = new stdClass();
        $oCampo2->nome = "t64_descr";
        $oCampo2->tamanho = 80;
        $oCampo2->descricao = $RLt64_descr;
        $aCamposRelatorio[] = $oCampo2;

        $oCampo3 = new stdClass();
        $oCampo3->nome = "c60_estrut";
        $oCampo3->tamanho = 30;
        $oCampo3->descricao = $RLc60_estrut;
        $aCamposRelatorio[] = $oCampo3;

        $oCampo4 = new stdClass();
        $oCampo4->nome = "c60_descr";
        $oCampo4->tamanho = 80;
        $oCampo4->descricao = $RLc60_descr;
        $aCamposRelatorio[] = $oCampo4;
        break;

    case 1:
        $desc_tipo = "Plano de Contas";
        $sCampos = "c60_estrut,c60_descr";
        $sOrderBy = "c60_estrut";
        $sGroupBy = $sCampos;
        $oCampo1 = new stdClass();

        $oCampo3 = new stdClass();
        $oCampo3->nome = "c60_estrut";
        $oCampo3->tamanho = 60;
        $oCampo3->descricao = $RLc60_estrut;
        $aCamposRelatorio[] = $oCampo3;

        $oCampo4 = new stdClass();
        $oCampo4->nome = "c60_descr";
        $oCampo4->tamanho = 160;
        $oCampo4->descricao = $RLc60_descr;
        $aCamposRelatorio[] = $oCampo4;
        break;

    case 2:
        $desc_tipo = "Plano de Contas / Classificação";
        $sCampos = "c60_estrut,c60_descr,t64_class,t64_descr";
        $sOrderBy = "c60_estrut,t64_class";
        $sGroupBy = $sCampos;

        $oCampo1 = new stdClass();
        $oCampo1->nome = "t64_class";
        $oCampo1->tamanho = 30;
        $oCampo1->descricao = $RLt64_class;
        $aCamposRelatorio[] = $oCampo1;

        $oCampo2 = new stdClass();
        $oCampo2->nome = "t64_descr";
        $oCampo2->tamanho = 80;
        $oCampo2->descricao = $RLt64_descr;
        $aCamposRelatorio[] = $oCampo2;

        $oCampo3 = new stdClass();
        $oCampo3->nome = "c60_estrut";
        $oCampo3->tamanho = 30;
        $oCampo3->descricao = $RLc60_estrut;
        $aCamposRelatorio[] = $oCampo3;

        $oCampo4 = new stdClass();
        $oCampo4->nome = "c60_descr";
        $oCampo4->tamanho = 80;
        $oCampo4->descricao = $RLc60_descr;
        $aCamposRelatorio[] = $oCampo4;
        break;

    case 3:
        $desc_tipo = "localizacao (Orgão)";
        $sCampos = "o40_orgao,o40_descr";
        $sGroupBy = $sCampos;
        $sOrderBy = $sCampos;
        $oCampo3 = new stdClass();
        $oCampo3->nome = "o40_orgao";
        $oCampo3->tamanho = 40;
        $oCampo3->descricao = "Orgão";
        $aCamposRelatorio[] = $oCampo3;

        $oCampo4 = new stdClass();
        $oCampo4->nome = "o40_descr";
        $oCampo4->tamanho = 180;
        $oCampo4->descricao = "Descrição";
        $aCamposRelatorio[] = $oCampo4;
        break;

    case 4:
        $desc_tipo = "localizacao (Orgão/Unidade)";
        $sCampos = "o40_orgao,o40_descr, o41_unidade, o41_descr";
        $sGroupBy = $sCampos;
        $sOrderBy = $sCampos;
        $oCampo1 = new stdClass();
        $oCampo1->nome = "o40_orgao";
        $oCampo1->tamanho = 10;
        $oCampo1->descricao = "Orgão";
        $aCamposRelatorio[] = $oCampo1;

        $oCampo2 = new stdClass();
        $oCampo2->nome = "o40_descr";
        $oCampo2->tamanho = 99;
        $oCampo2->descricao = "Nome Orgão";
        $aCamposRelatorio[] = $oCampo2;

        $oCampo3 = new stdClass();
        $oCampo3->nome = "o41_unidade";
        $oCampo3->tamanho = 12;
        $oCampo3->descricao = "Unidade";
        $aCamposRelatorio[] = $oCampo3;

        $oCampo4 = new stdClass();
        $oCampo4->nome = "o41_descr";
        $oCampo4->tamanho = 99;
        $oCampo4->descricao = "Nome Unidade";
        $aCamposRelatorio[] = $oCampo4;
        break;

    case 5:
        $desc_tipo = "localizacao (Orgão/Unidade/Departamento)";
        $sCampos = "o40_orgao,o40_descr, o41_unidade, o41_descr,coddepto, descrdepto";
        $sGroupBy = $sCampos;
        $sOrderBy = $sCampos;

        $oCampo1 = new stdClass();
        $oCampo1->nome = "o40_orgao";
        $oCampo1->tamanho = 12;
        $oCampo1->descricao = "Orgão";
        $aCamposRelatorio[] = $oCampo1;

        $oCampo2 = new stdClass();
        $oCampo2->nome = "o40_descr";
        $oCampo2->tamanho = 60;
        $oCampo2->descricao = "Nome Orgão";
        $aCamposRelatorio[] = $oCampo2;

        $oCampo3 = new stdClass();
        $oCampo3->nome = "o41_unidade";
        $oCampo3->tamanho = 15;
        $oCampo3->descricao = "Unidade";
        $aCamposRelatorio[] = $oCampo3;

        $oCampo4 = new stdClass();
        $oCampo4->nome = "o41_descr";
        $oCampo4->tamanho = 60;
        $oCampo4->descricao = "Nome Unidade";
        $aCamposRelatorio[] = $oCampo4;

        $oCampo5 = new stdClass();
        $oCampo5->nome = "coddepto";
        $oCampo5->tamanho = 13;
        $oCampo5->descricao = "Depto";
        $aCamposRelatorio[] = $oCampo5;

        $oCampo6 = new stdClass();
        $oCampo6->nome = "descrdepto";
        $oCampo6->tamanho = 60;
        $oCampo6->descricao = "Nome depto";
        $aCamposRelatorio[] = $oCampo6;

        break;

    case 6:
        $desc_tipo = "localizacao (Orgão/Unidade/Departamento/Divisão)";
        $sCampos = "distinct o40_orgao || ' - ' || o40_descr as orgao";
        $sCampos .= ", o41_unidade || ' - ' || o41_descr as unidade";
        $sCampos .= ", coddepto || ' - ' || descrdepto as departamento";
        $sCampos .= ", t30_codigo || ' - ' || t30_descr as divisao";
        $sCampos .= ", o40_orgao, o40_descr, o41_unidade, o41_descr, coddepto, descrdepto, t30_codigo, t30_descr";
        $sOrderBy = "o40_orgao, o40_descr, o41_unidade, o41_descr, coddepto, descrdepto, t30_codigo, t30_descr";
        $sGroupBy = "o40_orgao, o40_descr, o41_unidade, o41_descr, coddepto, descrdepto, t30_codigo, t30_descr";

        $oCampo2 = new stdClass();
        $oCampo2->nome = "orgao";
        $oCampo2->tamanho = 48;
        $oCampo2->descricao = "Orgão";
        $aCamposRelatorio[] = $oCampo2;

        $oCampo4 = new stdClass();
        $oCampo4->nome = "unidade";
        $oCampo4->tamanho = 63;
        $oCampo4->descricao = "Unidade";
        $aCamposRelatorio[] = $oCampo4;

        $oCampo6 = new stdClass();
        $oCampo6->nome = "departamento";
        $oCampo6->tamanho = 59;
        $oCampo6->descricao = "Departamento";
        $aCamposRelatorio[] = $oCampo6;

        $oCampo6 = new stdClass();
        $oCampo6->nome = "divisao";
        $oCampo6->tamanho = 55;
        $oCampo6->descricao = "Divisão";
        $aCamposRelatorio[] = $oCampo6;

        $tamanhoValorAquisicaoAtual = 26;
        $tamanhoValorTotal = 225;
        break;
}

$sWhere = " and (db_depart.limite is null or db_depart.limite >= '" . date("Y-m-d", db_getsession('DB_datausu')) . "')";
if ($listaorgaos != "") {
    if ($opcoesorgaos == "comorgao") {
        $sWhere .= " and o40_orgao in({$listaorgaos}) ";
    } else {
        $sWhere .= " and o40_orgao not in({$listaorgaos}) ";
    }
}

if ($listaunidades != "") {
    if ($opcoesunidades == "comunidade") {
        $sWhere .= " and o41_unidade in({$listaunidades}) ";
    } else {
        $sWhere .= " and o41_unidade not in({$listaunidades}) ";
    }
}

if ($listadepto != "") {
    if ($opcoesdepto == "comdepartamento") {
        $sWhere .= " and coddepto in({$listadepto}) ";
    } else {
        $sWhere .= " and coddepto not in({$listadepto}) ";
    }
}

if ($listadivisoes != "") {
    if ($opcoesdivisoes == "comdivisao") {
        $sWhere .= " and t30_codigo in({$listadivisoes}) ";
    } else {
        $sWhere .= " and ( t30_codigo not in ({$listadivisoes}) or t30_codigo is null ) ";
    }
}

switch ($opcoescedentes) {
    case 2 :
        if ($listacedentes != "") {
            $sWhere .= " and t04_sequencial in({$listacedentes})";
        } else {
            $sWhere .= " and t04_sequencial is not null";
        }
        break;

    case 3:
        $sWhere .= " and t04_sequencial is null";
        break;
}

if (isset($data_base) && isset($data_base_fim)) {
    if ($data_base != "" && $data_base_fim == "") {
        $sdtIni = implode("-", array_reverse(explode("/", $data_base)));
        if($tipoData == 1){
            $sWhere .= " and t52_dtaqu >= '{$sdtIni}' ";
        } else {
            $sWhere .= " and t52_dtinclusao >= '{$sdtIni}' ";
        }
    } else {
        if ($data_base != "" && $data_base_fim != "") {
            $sdtIni = implode("-", array_reverse(explode("/", $data_base)));
            $sdtFim = implode("-", array_reverse(explode("/", $data_base_fim)));
            if($tipoData == 1) {
                $sWhere .= " and t52_dtaqu between '{$sdtIni}' and '{$sdtFim}' ";
            } else {
                $sWhere .= " and t52_dtinclusao between '{$sdtIni}' and '{$sdtFim}' ";
            }
        } else {
            if ($data_base = "" && $data_base_fim != "") {
                $sdtFim = implode("-", array_reverse(explode("/", $data_base_fim)));
                if ($tipoData == 1){
                    $sWhere .= " and t52_dtaqu <= '{$sdtFim}' ";
                } else {
                    $sWhere .= " and t52_dtinclusao <= '{$sdtFim}' ";
                }
            }
        }
    }
}

$sJoin .= "
    LEFT JOIN (
        SELECT
            t58_sequencial,
            t58_bens,
            t58_valoratual
        FROM   benshistoricocalculobem bhcb
        INNER JOIN benshistoricocalculo bhc
            ON   bhcb.t58_benshistoricocalculo = bhc.t57_sequencial
                AND bhc.t57_datacalculo <= '{$sdtFim}'
                AND bhc.t57_processado is true
                AND bhc.t57_ativo is true
        ) historicocalculobem ON historicocalculobem.t58_bens = t52_bem
        and t58_sequencial in (
            select t58_sequencial seq_bhc
                from benshistoricocalculobem bhcb1
                inner join benshistoricocalculo bhcb2 on bhcb1.t58_benshistoricocalculo = bhcb2.t57_sequencial
                where bhcb1.t58_bens = historicocalculobem.t58_bens
                  and bhcb2.t57_processado is true
                  and bhcb2.t57_ativo is true
                  and bhcb2.t57_datacalculo <= '{$sdtFim}'

                order by bhcb2.t57_sequencial desc limit 1
        )
";

$sHeadBaixa = '';
if (isset($listabens) && $listabens != "") {
    $lPeriodoBaixa = false;

    if (isset($baixaini) && isset($baixafim)) {
        if ($baixaini != '' && $baixafim != '') {
            $dBaixaIni = implode('-', array_reverse(explode('/', $baixaini)));
            $dBaixaFim = implode('-', array_reverse(explode('/', $baixafim)));
            $sWhereBaixados = " and t55_codbem is not null and t55_baixa between '$dBaixaIni' and '$dBaixaFim' ";
            $sWhereSemBaixa = " and (t55_codbem is null or t55_baixa not between '$dBaixaIni' and '$dBaixaFim') ";
            $sHeadBaixa = "PERÍODO DE BAIXA: {$baixaini} a {$baixafim}";

            $lPeriodoBaixa = true;
        } else {
            if ($baixaini != '' && $baixafim == '') {
                $dBaixaIni = implode('-', array_reverse(explode('/', $baixaini)));
                $sWhereBaixados = " and t55_codbem is not null and t55_baixa >= '$dBaixaIni' ";
                $sWhereSemBaixa = " and (t55_codbem is null or t55_baixa >= '$dBaixaIni' )";
                $sHeadBaixa = "PERÍODO DE BAIXA A PARTIR DE $baixaini";

                $lPeriodoBaixa = true;
            } else {
                if ($baixaini == '' && $baixafim != '') {
                    $dBaixaFim = implode('-', array_reverse(explode('/', $baixafim)));
                    $sWhereBaixados = " and t55_codbem is not null and t55_baixa <= '$dBaixaFim' ";
                    $sWhereSemBaixa = " and (t55_codbem is null or t55_baixa <= '$dBaixaFim' )";
                    $sHeadBaixa = "PERÍODO DE BAIXA ATÉ $baixafim";

                    $lPeriodoBaixa = true;
                }
            }
        }
    }

    if ($listabens == 'b') {
        $sWhere .= ($lPeriodoBaixa) ? $sWhereBaixados : " and t55_codbem is not null ";
    } else {
        if ($listabens == "n") {
            $sData = date("Y-m-d", db_getsession('DB_datausu'));
            if (isset($data_base_fim) && trim($data_base_fim) != '') {
                $sData = implode('-', array_reverse(explode('/', $data_base_fim)));
            }
            if (isset($$sqldBaixaFim) && trim($dBaixaFim) != '') {
                $sData = implode('-', array_reverse(explode('/', $dBaixaFim)));
            }
            $sWhere .= ($lPeriodoBaixa) ? $sWhereSemBaixa : " and (t55_codbem is null or t55_baixa > '{$sData}') ";
        }
    }
} else {
    $sWhere .= " and ( t55_codbem  is null or t55_baixa < '" . date("Y-m-d", db_getsession('DB_datausu')) . "' ) ";
}

if (isset($bemtipo) && ($bemtipo != '')) {
    $sWhere .= " and t64_bemtipos = $bemtipo ";
}

if (isset($listacontas) && ($listacontas != '')) {
    if ($opcoescontas == 's') {
        $sWhere .= " and c60_codcon in($listacontas) ";
    } else {
        $sWhere .= " and c60_codcon not in ($listacontas)";
    }
}
$sql = "select {$sCampos},
                  Round(sum(round(t52_valaqu, 2)), 2) as valor,
                  Round(Sum(
                    CASE WHEN
                        historicocalculobem.t58_bens IS NULL
                        THEN
                            t52_valaqu
                        ELSE
                            historicocalculobem.t58_valoratual
                    END
                   ), 2) AS valor_atual
                  from bens
                 {$sJoin}
          where  t52_instit = " . db_getsession("DB_instit") . "

             {$sWhere}
          group by {$sGroupBy}
          order by {$sOrderBy}";

$head3 = "Relatório Financeiro Patrimonial";
$head5 = "TIPO : $desc_tipo";
if($tipoData == 1) {
    $head6 = "DATA AQUISIÇÃO: {$data_base} a {$data_base_fim}";
} else {
    $head6 = "DATA INCLUSÃO: {$data_base} a {$data_base_fim}";
}
$head7 = $sHeadBaixa;

$result = db_query($sql);
$totalLinhas = pg_num_rows($result);

if ($totalLinhas == 0) {
    $sMsg = _M('patrimonial.patrimonio.pat2_bensfinanc002.nao_existem_bens');
    db_redireciona('db_erros.php?fechar=true&db_erro=' . $sMsg);
}

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial', 'b', 8);
$troca = 1;
$alt = 4;
$totvalor = 0;
$totvalor_aquisicao = 0;
$iCell = 0;

if ($tipoagrupa != 6) {
    for ($x = 0; $x < $totalLinhas; $x++) {
        $oLinha = db_utils::fieldsmemory($result, $x);

        if ($pdf->gety() > $pdf->h - 30 || $troca != 0) {
            $pdf->addpage("L");
            $pdf->setfont('arial', 'b', 8);

            foreach ($aCamposRelatorio as $oCampo) {
                $pdf->cell($oCampo->tamanho, $alt, $oCampo->descricao, 1, 0, "C", 1);
                $iCell += $oCampo->tamanho;
            }

            $pdf->cell($tamanhoValorAquisicaoAtual, $alt, 'Vlr. Aquisição', 1, 0, "C", 1);
            $pdf->cell($tamanhoValorAquisicaoAtual, $alt, 'Vlr. Atual', 1, 1, "C", 1);
            $troca = 0;
        }
        $pdf->setfont('arial', '', 6);
        foreach ($aCamposRelatorio as $oCampo) {
            $pdf->cell($oCampo->tamanho, $alt, $oLinha->{$oCampo->nome}, 1, 0, "L", 0);
        }
        $pdf->setfont('arial', '', 8);
        $pdf->cell($tamanhoValorAquisicaoAtual, $alt, db_formatar($oLinha->valor, 'f'), 1, 0, "R");
        $pdf->cell($tamanhoValorAquisicaoAtual, $alt, db_formatar($oLinha->valor_atual, 'f'), 1, 1, "R");
        $totvalor += $oLinha->valor;
        $totvalor_aquisicao += $oLinha->valor_atual;
    }
} else {
    for ($x = 0; $x < $totalLinhas; $x++) {
        $oLinha = db_utils::fieldsmemory($result, $x);

        if ($pdf->gety() > $pdf->h - 30 || $troca != 0) {
            $pdf->addpage("L");
            $pdf->setfont('arial', 'b', 8);

            foreach ($aCamposRelatorio as $oCampo) {
                $pdf->cell($oCampo->tamanho, $alt, $oCampo->descricao, 1, 0, "C", 1);
                $iCell += $oCampo->tamanho;
            }

            $pdf->cell($tamanhoValorAquisicaoAtual, $alt, 'Vlr. Aquisição', 1, 0, "C", 1);
            $pdf->cell($tamanhoValorAquisicaoAtual, $alt, 'Vlr. Atual', 1, 1, "C", 1);
            $troca = 0;
        }

        $pdf->setfont('arial', '', 6);
        $maiorColunaMulticell = 1;
        $somaColunas = 0;
        $posicaoXInicial = $pdf->GetX();
        $posicaoYInicial = $pdf->GetY();

        foreach ($aCamposRelatorio as $oCampo) {
            $posicaoY = $pdf->GetY();
            $posicaoX = $pdf->GetX();
            $tamanhoColunaMulticell = $pdf->NbLines($oCampo->tamanho, $oLinha->{$oCampo->nome});

            if($tamanhoColunaMulticell > $maiorColunaMulticell) {
                $maiorColunaMulticell = $tamanhoColunaMulticell;
            }

            $pdf->MultiCell($oCampo->tamanho, $alt, $oLinha->{$oCampo->nome}, 0, "L", 0);
            $pdf->SetXY($posicaoX + $oCampo->tamanho, $posicaoY);

            $somaColunas += $oCampo->tamanho;
        }

        $pdf->setfont('arial', '', 8);
        $pdf->cell($tamanhoValorAquisicaoAtual, $alt * $maiorColunaMulticell, db_formatar($oLinha->valor, 'f'), 0, 0, "R");
        $pdf->cell($tamanhoValorAquisicaoAtual, $alt * $maiorColunaMulticell, db_formatar($oLinha->valor_atual, 'f'), 0, 1, "R");

        $somaColunas += ($tamanhoValorAquisicaoAtual * 2);
        $linhaY = $posicaoYInicial + ($alt * $maiorColunaMulticell);

        $pdf->Line($posicaoXInicial, $linhaY, $posicaoXInicial + $somaColunas, $linhaY);

        $totvalor += $oLinha->valor;
        $totvalor_aquisicao += $oLinha->valor_atual;
    }
}

$pdf->setfont('arial', 'b', 8);
$pdf->cell($tamanhoValorTotal, $alt, "Valor Total ", 1, 0, "R");
$pdf->cell($tamanhoValorAquisicaoAtual, $alt, db_formatar($totvalor, "f"), 1, 0, "R");
$pdf->cell($tamanhoValorAquisicaoAtual, $alt, db_formatar($totvalor_aquisicao, "f"), 1, 1, "R");

$pdf->cell(15, $alt, "Total Registros: {$clclabens->numrows}", 0, 1);

$pdf->Output();
