<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (c) 2019  DBSeller Servicos de Informatica
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

/**
 * Bens por Departamento
 * @author matheus.felini@dbseller.com.br
 */
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("fpdf151/pdf.php"));

$oDaoBens = new cl_bens();
$oDaoBensMater = new cl_bensmater();
$oDaoDbDepart = new cl_db_depart();
$oDaoDepartOrg = new cl_db_departorg();
$iAnoUsoSessao = db_getsession("DB_anousu");
$iInstituicaoSessao = db_getsession("DB_instit");
$oGet = db_utils::postMemory($_GET);
$oInstituicao = InstituicaoRepository::getInstituicaoByCodigo(db_getsession('DB_instit'));


ini_set("memory_limit", "-1");
$sWhereValor = '';
$rWhereValorDataAquisicao = '';
$sWhereValorDataAquisicao = '';

$sDataInicial = $oGet->dtInicial ? implode("-", array_reverse(explode("/", $oGet->dtInicial))) : '';
$sDataFinal = $oGet->dtFinal ? implode("-", array_reverse(explode("/", $oGet->dtFinal))) : '';


if ($sDataInicial && $sDataFinal) {
    if($tipoData == "1"){
        $sWhereValor = " t52_dtaqu between '{$sDataInicial}' and '{$sDataFinal}' ";
    } else {
        $sWhereValor = " t52_dtinclusao between '{$sDataInicial}' and '{$sDataFinal}' ";
    }
} else {
    if (!$sDataInicial) {
        $sDataFinal = date('Y-m-d');
    }
    if($tipoData == 1){
        $sWhereValor = $sDataInicial ? " t52_dtaqu >= '{$sDataInicial}' " : " t52_dtaqu <= '{$sDataFinal}' ";
    } else {
        $sWhereValor = $sDataInicial ? " t52_dtinclusao >= '{$sDataInicial}' " : " t52_dtinclusao <= '{$sDataFinal}' ";
    }
}

if ($oGet->lImprimeValorAtualAquisicao == 't') {
    $rWhereValorDataAquisicao = "and bhc.t57_datacalculo <= '{$sDataFinal}'";
    $sWhereValorDataAquisicao = "and bhcb2.t57_datacalculo <= '{$sDataFinal}'";
} else {
    $rWhereValorDataAquisicao = "";
    $sWhereValorDataAquisicao = "";
}

function getValores($sIdsBem, $sWhere,$rWhereValorDataAquisicao, $sWhereValorDataAquisicao)
{
    $date = new DateTime();
    $date->setTimestamp(db_getsession('DB_datausu'));

    $sWhereValor = $sWhere ? "t52_bem IN({$sIdsBem}) AND {$sWhere}" : "t52_bem IN({$sIdsBem})";

    $sSqlValor = "
        SELECT
            t52_bem,
            round(sum(round(t52_valaqu, 2)), 2) as valor,
            round(sum(
                CASE WHEN
                    bhcb.t58_bens IS NULL THEN t52_valaqu
                    ELSE bhcb.t58_valoratual
                END
            ), 2) AS valor_atual
        FROM
            bens
        LEFT JOIN (
            SELECT
                bhcb.t58_sequencial,
                bhcb.t58_bens,
                bhcb.t58_valoratual
            FROM
                benshistoricocalculobem bhcb
            INNER JOIN benshistoricocalculo bhc ON bhcb.t58_benshistoricocalculo = bhc.t57_sequencial
                    {$rWhereValorDataAquisicao}
                    AND bhc.t57_processado is true
                    AND bhc.t57_ativo is true
            ) bhcb ON bhcb.t58_bens = t52_bem
                AND t58_sequencial = (
                    SELECT
                        max(bhcb1.t58_sequencial)
                    FROM
                        benshistoricocalculobem bhcb1
                    INNER JOIN benshistoricocalculo bhcb2 ON bhcb1.t58_benshistoricocalculo = bhcb2.t57_sequencial
                    WHERE
                        bhcb1.t58_bens = bhcb.t58_bens
                        AND bhcb.t58_bens IN ({$sIdsBem})
                        AND bhcb2.t57_processado is true
                        AND bhcb2.t57_ativo is true
                        {$sWhereValorDataAquisicao}
                        AND bhcb2.t57_ano = (
                            select
                                max(bhc_inner.t57_ano)
                            from
                                benshistoricocalculo bhc_inner
                            inner join benshistoricocalculobem bhcb_inner on
                                bhc_inner.t57_sequencial = bhcb_inner.t58_benshistoricocalculo
                            where
                                bhcb_inner.t58_bens = bhcb1.t58_bens
                                and bhc_inner.t57_processado is true
                                and bhc_inner.t57_ativo is true
                        )
                )
            LEFT JOIN bensbaix ON t55_codbem = t52_bem
        WHERE
            t52_instit = " . db_getsession("DB_instit") . "
            AND {$sWhereValor}
        GROUP BY
            t52_bem
    ";

    $rsValor = db_query($sSqlValor);

    while($row = pg_fetch_assoc($rsValor)) {
        yield $row;
    }
}

/**
 * Variavel que armazena todos os filtros utilizados pelo usuário
 * Ao terminar de validar os filtro selecionados pelo usuário, é efetuado
 * um implode da string " and " no array.
 * @var array
 */
$aWhereParametros[] = "t52_instit = {$iInstituicaoSessao}";
$sWhereDivisao = "";
$oGet->lUsarDivisao = $oGet->lUsarDivisao == '1' ? true : false;

/*
 * Esconde ou não as colunas: Baixa e Estado.
 */
$lineBreak = 0;
$flagColunasVisiveis = true;
if ($oGet->lImprimeValorAquisicao == "t" && $oGet->filtroColunas == "1") {
    $lineBreak = 1;
    $flagColunasVisiveis = false;
}

/**
 * Filtra o SQL por departamentos divisões
 */
if (trim($oGet->sDepartamentos) != "") {
    $sWhereDivisao .= "and db_depart.instit = {$iInstituicaoSessao}";

    if (trim($oGet->sDivisoes) != "") {
        if ($oGet->lUsarDivisao) {
            $aWhereParametros[] = "t33_divisao in ({$oGet->sDivisoes})";
            $sWhereDivisao .= " and t30_codigo in ({$oGet->sDivisoes})";
        } else {
            $aWhereParametros[] = "(t33_divisao not in ({$oGet->sDivisoes}) or t33_divisao is null)";
            $sWhereDivisao .= " and t30_codigo not in ({$oGet->sDivisoes})";
        }
    }
    $sWhereDepartamentos = "coddepto in ({$oGet->sDepartamentos}) $sWhereDivisao";
    $sSqlDbDepartamentos = $oDaoDbDepart->sql_query_div(null, "*", null, $sWhereDepartamentos);
    $lTodosDepartamentos = false;
} else {
    $sSqlDbDepartamentos = $oDaoDbDepart->sql_query_div(null, "*", null, "o40_instit = {$iInstituicaoSessao}");
    $lTodosDepartamentos = true;
}

$rsDbDepartamentos = $oDaoDbDepart->sql_record($sSqlDbDepartamentos);
if ($oDaoDbDepart->numrows == 0) {
    $oParms = new stdClass();
    $oParms->sDepartamentos = $oGet->sDepartamentos;
    $sMsg = _M('patrimonial.patrimonio.pat2_benspordepartamento002.departamento_nao_encontrado', $oParms);
    db_redireciona("db_erros.php?fechar=true&db_erro=" . $sMsg);
}

/**
 * Classificação
 */
if (trim($oGet->sClabens) != "") {
    $aClabens = explode(',', $sClabens);
    $sClassificacao = '';

    foreach($aClabens as $classificacao) {
        $sClassificacao .=  $sClassificacao ? ", '{$classificacao}'" : "'{$classificacao}'";
    }

    $aWhereParametros[] = "t64_class IN ({$sClassificacao})";
}

if (trim($oGet->sOrgaos) != '') {
    $sJoinOrgao = "INNER JOIN db_departorg ON db01_coddepto = db_depart.coddepto AND db01_anousu = {$iAnoUsoSessao}";
    $aWhereParametros[] = "db01_orgao IN ({$oGet->sOrgaos}) AND db01_anousu = {$iAnoUsoSessao}";
}

/**
 * Bens
 */
if (trim($oGet->sBens) != "") {
    $aWhereParametros[] = "t52_bem in ({$oGet->sBens})";
}
/**
 * Situação dos Bens
 */
if (trim($oGet->sSituacaoBens) != "") {
    $sSqlSituaBens = "(select t56_situac ";
    $sSqlSituaBens .= "   from histbem ";
    $sSqlSituaBens .= "  where t56_codbem = t52_bem";
    $sSqlSituaBens .= "  order by t56_data desc ";
    $sSqlSituaBens .= "  limit 1 ) in ({$oGet->sSituacaoBens})";

    $aWhereParametros[] = $sSqlSituaBens;
}

/**
 * Contas Estruturais
 */
if (trim($oGet->sContasEstruturais) != "") {
    $aWhereParametros[] = "c60_codcon in ({$oGet->sContasEstruturais})";

    $oDaoConplano = new cl_conplano();
    $sWhereConplano = "c60_codcon in ({$oGet->sContasEstruturais}) and c60_anousu = {$iAnoUsoSessao}";
    $sSqlConplano = $oDaoConplano->sql_query_file(null, null, "*", null, $sWhereConplano);
    $rsConplano = $oDaoConplano->sql_record($sSqlConplano);
    /*
     * Configura um header para o relatório caso tenha somente um plano de conta escolhido
     */
    if ($oDaoConplano->numrows == 1) {
        $oConta = db_utils::fieldsMemory($rsConplano, 0);
        $head6 = "Conta: {$oConta->c60_estrut} - {$oConta->c60_descr}";
    }
}

/**
 * Configurações da data de aquisição
 */

if (trim($oGet->dtInicial) != "" && trim($oGet->dtFinal) == "") {
    $dtInicial = implode("-", array_reverse(explode("/", $oGet->dtInicial)));
    if($tipoData == "1"){
        $aWhereParametros[] = "t52_dtaqu >= cast('{$dtInicial}' as date)";
    } else {
        $aWhereParametros[] = "t52_dtinclusao >= cast('{$dtInicial}' as date)";
    }
} else {
    if (trim($oGet->dtInicial) == "" && trim($oGet->dtFinal) != "") {
        $dtFinal = implode("-", array_reverse(explode("/", $oGet->dtFinal)));
        if($tipoData == "1"){
            $aWhereParametros[] = "t52_dtaqu <= cast('{$dtFinal}' as date)";
        } else {
            $aWhereParametros[] = "t52_dtinclusao <= cast('{$dtFinal}' as date)";
        }
    } else {
        if (trim($oGet->dtInicial) != "" && trim($oGet->dtFinal) != "") {
            $dtInicial = implode("-", array_reverse(explode("/", $oGet->dtInicial)));
            $dtFinal = implode("-", array_reverse(explode("/", $oGet->dtFinal)));
            if($tipoData == "1"){
                $aWhereParametros[] = "t52_dtaqu between cast('{$dtInicial}' as date) and cast('{$dtFinal}' as date)";
            } else {
                $aWhereParametros[] = "t52_dtinclusao between cast('{$dtInicial}' as date) and cast('{$dtFinal}' as date)";
            }
        }
    }
}
/**
 * Descrição do Bem
 */
if (trim($oGet->sDescricaoBem) != "") {
    $aWhereParametros[] = "t52_descr ilike '%{$oGet->sDescricaoBem}%'";
}

/**
 * Configura variável que deverá ordenar os bens
 */
$sOrdemBuscaBens = null;
switch ($oGet->iOrdem) {
    /*
     * Placa
     */
    case "1":
        $sOrdemBuscaBens = "t64_class, t52_ident";
        break;

    /*
     * Descrição
     */
    case "3":
        $sOrdemBuscaBens = "t52_descr";
        break;

    /*
     * Ordem Default
     */
    default:
        $sOrdemBuscaBens = "t52_bem";
}

/**
 * Tratamento da propriedade iConvenio
 */
switch ($oGet->iConvenio) {
    /*
     * Ambos vinculados a convênio
     */
    case "2":
        if (!empty($oGet->sCedentes)) {
            $aWhereParametros[] = "t04_sequencial in({$oGet->sCedentes})";
        } else {
            $aWhereParametros[] = "t04_sequencial is not null";
        }
        break;

    /*
     * Apenas não vinculados a convênio
     */
    case "3":
        $aWhereParametros[] = "t04_sequencial is null";
        break;
}

/**
 * Configura Substuição de aquisição
 */
$substituirAquisicao = "";
if (!empty($oGet->substituirAquisicao)) {
    $substituirAquisicao = $oGet->substituirAquisicao;
}

/**
 * Configura as datas de baixa selecionado pelo usuário
 */
$dtBaixaInicial = "";
$dtBaixaFinal = "";
$baixaini = "";
if (trim($oGet->dtBaixaInicial) != "" && trim($oGet->dtBaixaFinal) != "") {
    $dtBaixaInicial = implode("-", array_reverse(explode("/", $oGet->dtBaixaInicial)));
    $dtBaixaFinal = implode("-", array_reverse(explode("/", $oGet->dtBaixaFinal)));
} else {
    if (trim($oGet->dtBaixaInicial) != "" && trim($oGet->dtBaixaFinal) == "") {
        $dtBaixaInicial = implode("-", array_reverse(explode("/", $oGet->dtBaixaInicial)));
        $dtBaixaFinal = date("Y-m-d", $iAnoUsoSessao);
    } else {
        if (trim($oGet->dtBaixaInicial) == "" && trim($oGet->dtBaixaFinal) != "") {
            $dtBaixaInicial = date("Y-m-d", $iAnoUsoSessao);
            $dtBaixaFinal = implode("-", array_reverse(explode("/", $oGet->dtBaixaFinal)));
        }
    }
}

/**
 * Adiciona a busca por data de baixa caso o usuário tenha setado as datas no formulário
 * Caso o usuário tenha filtrado por itens não baixados, o filtro de período de baixa será ignorado.
 * Caso o usuário tenha filtrado por todos os itens, busca os baixados no período e os não baixados independente do período de baixa.
 * Caso o usuário tenha filtrado por itens baixados, busca somente os baixados no período.
 */
$sWhereFiltroBaixa = '';
if (!empty($oGet->iListarBens)) {

    $lPeriodoBaixa = false;

    if (isset($dtBaixaInicial) && isset($dtBaixaFinal)) {
        if ($dtBaixaInicial != '' && $dtBaixaFinal != '') {
            $dtBaixaInicial = implode('-', array_reverse(explode('/', $dtBaixaInicial)));
            $dtBaixaFinal = implode('-', array_reverse(explode('/', $dtBaixaFinal)));
            $sWhereBaixados = "  t55_codbem is not null and t55_baixa between '$dtBaixaInicial' and '$dtBaixaFinal' ";
            $sWhereSemBaixa = "  (t55_codbem is null or t55_baixa not between '$dtBaixaInicial' and '$dtBaixaFinal') ";

            $lPeriodoBaixa = true;
        } else {
            if ($dtBaixaInicial != '' && $dtBaixaFinal == '') {
                $dtBaixaInicial = implode('-', array_reverse(explode('/', $dtBaixaInicial)));
                $sWhereBaixados = " and t55_codbem is not null and t55_baixa >= '$dtBaixaInicial' ";
                $sWhereSemBaixa = " and (t55_codbem is null or t55_baixa >= '$dtBaixaInicial' )";

                $lPeriodoBaixa = true;
            } else {
                if ($baixaini == '' && $dtBaixaFinal != '') {
                    $dtBaixaFinal = implode('-', array_reverse(explode('/', $dtBaixaFinal)));
                    $sWhereBaixados = " and t55_codbem is not null and t55_baixa <= '$dtBaixaFinal' ";
                    $sWhereSemBaixa = " and (t55_codbem is null or t55_baixa <= '$dtBaixaFinal' )";

                    $lPeriodoBaixa = true;
                }
            }
        }
    }

    if ($oGet->iListarBens == 3) {
        $sWhereFiltroBaixa .= ($lPeriodoBaixa) ? $sWhereBaixados : "t55_codbem is not null ";
    } else {
        if ($oGet->iListarBens == 2) {
            $sData = date("Y-m-d", db_getsession('DB_datausu'));

            if (isset($sDataFinal) && trim($sDataFinal) != '') {
                $sData = $sDataFinal;
            } else if (!empty($dtBaixaFinal)) {
                $sData = implode('-', array_reverse(explode('/', $dtBaixaFinal)));
            }

            $sWhereFiltroBaixa .= ($lPeriodoBaixa) ? $sWhereSemBaixa : "(t55_codbem is null or t55_baixa > '{$sData}') ";
        }
    }
} else {
    $sWhereFiltroBaixa .= "( t55_codbem  is null or t55_baixa < '" . date("Y-m-d", db_getsession('DB_datausu')) . "' ) ";
}

if($sWhereFiltroBaixa != "") {

  $aWhereParametros[] = $sWhereFiltroBaixa;
}

/**
 * Query que busca os bens que se encaixam nos filtros selecionados pelo usuário
 */
$sWhereParametros = implode(" and ", $aWhereParametros);

$sCamposBens = "case when t54_codbem is null ";
$sCamposBens .= "     then 'Material'";
$sCamposBens .= "     else 'Imóvel'";
$sCamposBens .= " end as tipobem,";
$sCamposBens .= "case when t55_codbem is null ";
$sCamposBens .= "     then 'Não' ";
$sCamposBens .= "     else 'Sim' ";
$sCamposBens .= " end as situacaobem,";

/**
 * t44_valoratual = valor depreciavel
 */

$sCamposBens .= "(
    select t58_sequencial from benshistoricocalculobem
    inner join benshistoricocalculo
      on benshistoricocalculo.t57_sequencial = benshistoricocalculobem.t58_benshistoricocalculo
    where t57_ativo is true and
      t57_processado is true and
      t58_bens = t52_bem
    limit 1
  ) as depreciado,
";

$sCamposBens .= "(t44_valoratual + t44_valorresidual) as valoratual,";
$sCamposBens .= "bens.*, ";
$sCamposBens .= "db_depart.*, ";
$sCamposBens .= "bensdiv.*, ";
$sCamposBens .= "clabens.*, ";
$sCamposBens .= "conplano.*, ";
$sCamposBens .= "departdiv.*, ";
$sCamposBens .= "benscedente.*, ";
$sCamposBens .= "benscadcedente.*, ";
$sCamposBens .= "bensmater.*, ";
$sCamposBens .= "bensimoveis.*, ";
$sCamposBens .= "bensbaix.*, ";
$sCamposBens .= "bensdepreciacao.*,";
$sCamposBens .= "(  select t70_descr ";
$sCamposBens .= "     from histbem ";
$sCamposBens .= "          inner join situabens on t70_situac = t56_situac ";
$sCamposBens .= "    where t56_codbem = t52_bem ";
$sCamposBens .= " order by t56_histbem desc limit 1 ) as estadobem";

/**
 * Monta o SQL de acordo validando se o usuário selecionou todos departamentos ou não
 */
if ($lTodosDepartamentos) {
    $sSqlBens = "   select {$sCamposBens} ";
    $sSqlBens .= "     from bens ";
    $sSqlBens .= "          inner join db_depart      on db_depart.coddepto 				= bens.t52_depart";
    $sSqlBens .= "          left  join bensdiv        on t52_bem 										= t33_bem";
    $sSqlBens .= "          left  join departdiv      on t33_divisao 								= t30_codigo";

    $sSqlBens .= "          inner join clabens        on clabens.t64_codcla 				= bens.t52_codcla";
    $sSqlBens .= "          inner join clabensconplano on clabensconplano.t86_clabens = clabens.t64_codcla ";
    $sSqlBens .= "                                   and clabensconplano.t86_anousu  = {$iAnoUsoSessao}";
    $sSqlBens .= "          inner join conplano        on conplano.c60_codcon = clabensconplano.t86_conplano ";
    $sSqlBens .= "                                   and conplano.c60_anousu 				= {$iAnoUsoSessao}";

    $sSqlBens .= "          inner join db_departorg   on db_departorg.db01_coddepto = db_depart.coddepto";
    $sSqlBens .= "                                   and db_departorg.db01_anousu   = {$iAnoUsoSessao}";
    $sSqlBens .= "          left join benscedente     on t09_bem                		= t52_bem";
    $sSqlBens .= "          left join benscadcedente  on t09_benscadcedente     		= t04_sequencial";
    $sSqlBens .= "  				left join bensmater       on t53_codbem                 = t52_bem";
    $sSqlBens .= "  				left join bensimoveis     on t54_codbem                 = t52_bem";
    $sSqlBens .= "  				left join bensbaix        on t55_codbem                 = t52_bem";
    $sSqlBens .= "  				left join bensdepreciacao on t44_bens                   = t52_bem";
    $sSqlBens .= "    where {$sWhereParametros}";
    $sSqlBens .= " order by db01_orgao, db01_coddepto, t52_depart, t30_codigo, {$sOrdemBuscaBens} ";
} else {
    $sSqlBens = "   select {$sCamposBens}";
    $sSqlBens .= "          from bens ";
    $sSqlBens .= "          inner join db_depart 			 on db_depart.coddepto  = bens.t52_depart ";
    $sSqlBens .= "          left  join bensdiv   			 on t52_bem 						 = t33_bem ";
    $sSqlBens .= "          left  join departdiv 			 on t33_divisao         = t30_codigo ";

    $sSqlBens .= "          inner join clabens   			 on clabens.t64_codcla  = bens.t52_codcla ";
    $sSqlBens .= "         inner join clabensconplano  on clabensconplano.t86_clabens = clabens.t64_codcla ";
    $sSqlBens .= "                                    and clabensconplano.t86_anousu  = {$iAnoUsoSessao}";
    $sSqlBens .= "         inner join conplano         on conplano.c60_codcon = clabensconplano.t86_conplano ";
    $sSqlBens .= " 	                             		  and conplano.c60_anousu = {$iAnoUsoSessao}";
    $sSqlBens .= $sJoinOrgao;

    $sSqlBens .= "  				left  join benscedente     on t09_bem            = t52_bem";
    $sSqlBens .= "  				left  join benscadcedente  on t09_benscadcedente = t04_sequencial";
    $sSqlBens .= "  				left  join bensmater       on t53_codbem         = t52_bem";
    $sSqlBens .= "  				left  join bensimoveis     on t54_codbem         = t52_bem";
    $sSqlBens .= "  				left  join bensbaix        on t55_codbem         = t52_bem";
    $sSqlBens .= "  				left  join bensdepreciacao on t44_bens           = t52_bem";
    $sSqlBens .= " 		where t52_depart in ({$oGet->sDepartamentos}) and {$sWhereParametros} ";
    $sSqlBens .= " order by t52_depart,t30_codigo, {$sOrdemBuscaBens} ";
}

/**
 * Executa a query que busca os bens que serão impressos no relatório
 */
// die($sSqlBens);
$rsBuscaBens = db_query($sSqlBens);
$iLinhasBuscaBens = pg_num_rows($rsBuscaBens);
if ($iLinhasBuscaBens == 0) {
    $sMsg = _M('patrimonial.patrimonio.pat2_benspordepartamento002.nao_existem_bens');
    db_redireciona("db_erros.php?fechar=true&db_erro=" . $sMsg);
    exit;
}

$sIdsBem = '';

while ($row = pg_fetch_assoc($rsBuscaBens)) {
    $sIdsBem .= $sIdsBem ? ", {$row['t52_bem']}" : $row['t52_bem'];
}

$arrayValores = array();
foreach (getValores($sIdsBem, $sWhereValor, $rWhereValorDataAquisicao, $sWhereValorDataAquisicao) as $bem) {
    $arrayValores[$bem['t52_bem']]['valor'] = $bem['valor'];
    $arrayValores[$bem['t52_bem']]['valor_atual'] = $bem['valor_atual'];
}

/**
 * Array que armazena os dados de órgão e unidade de um departamento
 * @var array
 */
$aOrgaosUnidade = array();

/**
 * Array que irá armazenar todos os dados já organizados obedecendo a seguinte estrutura
 * - Departamento
 * -- Divisao
 * --- Classificação
 * ---- Bem
 * @var array
 */
$aDepartamentos = array();

/**
 * Este for percorre o resultset retornado do banco de dados e cria um
 * novo array de dados com todos os bens já organizados por divisão e classificação.
 */
for ($iRow = 0; $iRow < $iLinhasBuscaBens; $iRow++) {
    $oBem = db_utils::fieldsMemory($rsBuscaBens, $iRow);

    $bemModel = new Bem($oBem->t52_bem);
    $cgm = new CgmFisico($oBem->t30_numcgm);
    $responsavelDivisao = $cgm->getNome();
    $valorAtual = $arrayValores[$oBem->t52_bem]['valor_atual'];

    /**
     * Busca o órgao
     */
    if (!array_key_exists($oBem->t52_depart, $aOrgaosUnidade)) {
        $sCamposDepartamentoOrgao = "db01_orgao, db01_unidade, o40_descr as orgao, o41_descr as unidade";
        $sWhereDepartamentoOrgao = "db01_coddepto in ({$oBem->t52_depart}) and db01_anousu = {$iAnoUsoSessao}";
        $sSqlDepartamentoOrgao = $oDaoDepartOrg->sql_query_orgunid(null, null, $sCamposDepartamentoOrgao, null,
          $sWhereDepartamentoOrgao);
        $rsDepartamentoOrgao = $oDaoDepartOrg->sql_record($sSqlDepartamentoOrgao);

        /**
         * Adicionamos o órgao/unidade para o departamento dentro de um array $aOrgaosUnidade
         * Para usar posteriormente durante a impressão das páginas do relatório
         */
        $oDadoDepartamentoOrgao = db_utils::fieldsMemory($rsDepartamentoOrgao, 0);
        $oDadoDepartamentoOrgao->codigodepartamento = $oBem->t52_depart;
        $oDadoDepartamentoOrgao->descricaodepartamento = $oBem->descrdepto;
        $aOrgaosUnidade[$oBem->t52_depart] = $oDadoDepartamentoOrgao;
    }

    /**
     * Configura a variável t30_codigo caso o bem não tenha divisao
     */
    $iCodigoDivisao = $oBem->t30_codigo;
    $sDescricaoDivisao = $oBem->t30_descr;
    if (empty($oBem->t30_codigo)) {
        $iCodigoDivisao = 0;
        $sDescricaoDivisao = "BENS SEM DIVISÃO";
    }

    $iDepartamento = $oBem->t52_depart;
    $sDepartamento = $oBem->descrdepto;

    $iClassificacao = $oBem->t64_codcla;
    $sClassificacao = $oBem->t64_class;
    $sClassificacaoDescricao = $oBem->t64_descr;

    $iEstrutural = $oBem->c60_codcon;
    $sEstrutural = $oBem->c60_estrut;
    $sEstruturalDescricao = $oBem->c60_descr;

    $before = memory_get_usage();
    $oStdBem = new stdClass();
    $oStdBem->t52_depart = $oBem->t52_depart;
    $oStdBem->t52_dtaqu = $oBem->t52_dtaqu;
    $oStdBem->t52_dtinclusao = $oBem->t52_dtinclusao;
    $oStdBem->t52_bem = $oBem->t52_bem;
    $oStdBem->t52_obs = $oBem->t52_obs;
    $oStdBem->t52_descr = substr($oBem->t52_descr, 0, 35);
    $oStdBem->t52_valaqu = $oBem->t52_valaqu;
    $oStdBem->t44_valorresidual = $oBem->t44_valorresidual;

    $valorUltimaAvaliacao = $bemModel->getValorUltimaReavaliacao();

    $oStdBem->valoratual = $valorAtual;
    $oStdBem->t44_valoratual = $valorAtual;

    $oStdBem->estadobem = $oBem->estadobem;
    $oStdBem->tipobem = $oBem->tipobem;
    $oStdBem->situacaobem = $oBem->situacaobem;
    $oStdBem->t52_depart = $oBem->t52_depart;
    $oStdBem->t52_ident = $oBem->t52_ident;
    $oStdBem->t44_benstipodepreciacao = $oBem->t44_benstipodepreciacao;

    // VALOR DEPRECIÁVEL
    if ($valorUltimaAvaliacao == 0) {
        $oStdBem->valorDepreciavel = $valorUltimaAvaliacao;
    } else {
        $oStdBem->valorDepreciavel = $valorUltimaAvaliacao - $bemModel->getValorResidual();
    }

    $oStdBem->valor_ultimaavaliacao = $valorUltimaAvaliacao;

    $after = memory_get_usage();
    $allocatedSize = ($after - $before);
    unset($oBem);


    /**
     * Criamos um novo array contendo os dados já organizados que serão impressos no relatório.
     *
     * Valida se o Departamento já existe no array que deve ser configurado.
     */
    if (empty($aDepartamentos[$iDepartamento])) {
        /*
         * Caso o departamento não exista, é criado uma estrutura dentro do array $aDepartamentos
         * com os dados que serão preenchidos no decorrer do FOR
         */
        $oDepartamento = new stdClass();
        $oDepartamento->iCodigoDepartamento = $iDepartamento;
        $oDepartamento->sDescricaoDepartamento = $sDepartamento;
        $oDepartamento->aDivisao = array();
        $oDepartamento->aDivisao[$iCodigoDivisao] = new stdClass();
        $oDepartamento->aDivisao[$iCodigoDivisao]->iCodigoDivisao = $iCodigoDivisao;
        $oDepartamento->aDivisao[$iCodigoDivisao]->responsavelDivisao = $responsavelDivisao;
        $oDepartamento->aDivisao[$iCodigoDivisao]->sDescricaoDivisao = $sDescricaoDivisao;
        $oDepartamento->aDivisao[$iCodigoDivisao]->aEstrutural = array();
        $oDepartamento->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural] = new stdClass();
        $oDepartamento->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->iCodigoEstrutural = $iEstrutural;
        $oDepartamento->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->sEstrutural = $sEstrutural;
        $oDepartamento->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->sDescricaoEstrutural = $sEstruturalDescricao;
        $oDepartamento->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao = array();
        $oDepartamento->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao] = new stdClass();
        $oDepartamento->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->iCodigoClassificacao = $iClassificacao;
        $oDepartamento->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->sClassificacao = $sClassificacao;
        $oDepartamento->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->sDescricaoClassificacao = $sClassificacaoDescricao;
        $oDepartamento->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->aBens = array();
        $oDepartamento->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->aBens[] = $oStdBem;

        /*
         * Adicionamos o objeto dentro do array de departamentos.
         */
        $aDepartamentos[$iDepartamento] = $oDepartamento;
    } else {
        /**
         * Caso o departamento exista no array, validamos se existe a divisão para o departamento em questão.
         */
        if (empty ($aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao])) {
            /*
             * Caso não exista criamos uma nova posição no array aDivisao com a classificação já setada para o bem
             */
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao] = new stdClass();
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->iCodigoDivisao = $iCodigoDivisao;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->responsavelDivisao = $responsavelDivisao;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->sDescricaoDivisao = $sDescricaoDivisao;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural = array();
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural] = new stdClass();
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->iCodigoEstrutural = $iEstrutural;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->sEstrutural = $sEstrutural;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->sDescricaoEstrutural = $sEstruturalDescricao;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao = array();
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao] = new stdClass();
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->iCodigoClassificacao = $iClassificacao;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->sClassificacao = $sClassificacao;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->sDescricaoClassificacao = $sClassificacaoDescricao;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->aBens = array();
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->aBens[] = $oStdBem;
        } else {
            if (!isset($aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao])) {
                $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao] = new stdClass();
            }
            /*
             * Caso a divisão já exista, apenas adicionamos uma nova posição no array aClassificacao com o bem em questão
             */
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->iCodigoEstrutural = $iEstrutural;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->sEstrutural = $sEstrutural;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->sDescricaoEstrutural = $sEstruturalDescricao;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->iCodigoClassificacao = $iClassificacao;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->sClassificacao = $sClassificacao;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->sDescricaoClassificacao = $sClassificacaoDescricao;
            $aDepartamentos[$iDepartamento]->aDivisao[$iCodigoDivisao]->aEstrutural[$iEstrutural]->aClassificacao[$iClassificacao]->aBens[] = $oStdBem;
        }
    }
}

unset($rsBuscaBens);

/*
 * Verifica quais bens foram reavaliados
 */
$daoCalculo = new \cl_benshistoricocalculobem();
$rsBensReavaliados = pg_fetch_all(db_query($daoCalculo->sql_query_busca_bens_reavaliados($sIdsBem)));

$bensReavaliados = array();
if ($rsBensReavaliados !== false) {
    foreach ($rsBensReavaliados as $bem) {
        $bensReavaliados[$bem['t58_bens']] = true;
    }
}

unset($rsBensReavaliados);

/**
 * Configura variável que valida se deve imprimir o valor ou não.
 * Facilita a forma de validação
 */
$oGet->lImprimeValorAquisicao = $oGet->lImprimeValorAquisicao == 't' ? true : false;
$oGet->lCaracteristicaAdicional = $oGet->lCaracteristicaAdicional == 't' ? true : false;

$head1 = "Relatório de Bens por Departamento";
$oPdf = new PDF();
$oPdf->Open();
$oPdf->AliasNbPages();
$oPdf->SetAutoPageBreak(false);

$iHeigth = 4;
$iWidth = 100;
$iTotalClassificacao = 0;
$lPrimeiroLaco = true;

/**
 * Variáveis configuradas para impressão de bens SEM VALORES
 */
$iWCodigoSemValor = 15;
$iWPlacaSemValor = 17;
$iWDescricaoSemValor = 50;
$iWAquisicaoSemValor = 17;
$iWEstadoSemValor = 14;
$iWSituacaoSemValor = 20;

/**
 * Percorremos o array de departamentos imprimindo as divisões, classificações e bens
 */
$iTamanhoWidth = 190;
if ($oGet->lImprimeValorAquisicao) {
    $iTamanhoWidth = 280;
}

$oTotal = new stdClass();
$oTotal->nValorAquisicao = 0;
$oTotal->nValorResidual = 0;
$oTotal->nValorDepreciado = 0;
$oTotal->nValorAtual = 0;
$oTotal->nValorDepreciavel = 0;
$oTotal->nValorReavaliado = 0;
$oTotal->nSaldoDepreciavel = 0;
$itotalRegistros = 0;

foreach ($aDepartamentos as $iIndiceDepartamento => $oDepartamento) {
    /**
     * Caso o usuário tenha escolhido a opção para imprimir os valores, o relatório será gerado em formato
     * paisagem. Do contrário será gerado em formato retrato.
     *
     * A variável $oGet->iQuebraPagina é referente a quebra de página por departamento
     */
    if ($oGet->iQuebraPagina == 2 || $oPdf->gety() > $oPdf->h - 30 || $lPrimeiroLaco) {
        setHeader(
          $oPdf,
          $iHeigth,
          $oGet->lImprimeValorAquisicao,
          $oGet->lCaracteristicaAdicional,
          $oDepartamento->iCodigoDepartamento,
          $aOrgaosUnidade,
          $flagColunasVisiveis,
          $substituirAquisicao
        );
        $lPrimeiroLaco = false;
    }

    foreach ($oDepartamento->aDivisao as $iIndiceDivisao => $oDivisao) {
        $iTotaBemPorDivisao = 0;

        $oTotalDivisao = new stdClass();
        $oTotalDivisao->nValorAquisicao = 0;
        $oTotalDivisao->nValorResidual = 0;
        $oTotalDivisao->nValorDepreciado = 0;
        $oTotalDivisao->nValorAtual = 0;
        $oTotalDivisao->nValorDepreciavel = 0;
        $oTotalDivisao->nValorReavaliado = 0;
        $oTotalDivisao->nSaldoDepreciavel = 0;

        $oPdf->SetFont("arial", "b", 8);
        $oPdf->Cell($iTamanhoWidth, $iHeigth, "Divisão: {$oDivisao->iCodigoDivisao} - {$oDivisao->sDescricaoDivisao} (Responsável: {$oDivisao->responsavelDivisao})",
          "T", 1);
        $iTotalBemNaClassificacao = 0;
        foreach ($oDivisao->aEstrutural as $iIndiceEstrutural => $oEstrutural) {
            $oPdf->SetFont("arial", "b", 7);
            $oPdf->Cell($iTamanhoWidth, 4,
                "Estrutural: {$oEstrutural->sEstrutural} - {$oEstrutural->sDescricaoEstrutural}", "T", 1);

            foreach ($oEstrutural->aClassificacao as $iIndiceClassificacao => $oClassificacao) {
                $iTotaBemPorClassificacao = 0;
                $oPdf->SetFont("arial", "b", 6);
                $oPdf->Cell($iTamanhoWidth, 4,
                    "Classificação: {$oClassificacao->sClassificacao} - {$oClassificacao->sDescricaoClassificacao}", "T", 1);

                /**
                 * Imprimimos os bens da classificação
                 */
                foreach ($oClassificacao->aBens as $iIndiceBem => $oBem) {
                    $valorAtual = $arrayValores[$oBem->t52_bem]['valor_atual'];

                    /**
                     * Configuramos variáveis
                     */
                    $dtAquisicaoBem = implode("/", array_reverse(explode("-", $oBem->t52_dtaqu)));
                    $dtInclusaoBem = implode('/', array_reverse(explode("-", $oBem->t52_dtinclusao)));

                    $sPlacaConfigurada = str_pad($oBem->t52_ident, 7, "0", STR_PAD_LEFT);

                    $oPdf->SetFont("arial", "", 6);
                    $oPdf->cell($iWCodigoSemValor, $iHeigth, $oBem->t52_bem, 0, 0, "C");
                    $oPdf->cell($iWPlacaSemValor, $iHeigth, $sPlacaConfigurada, 0, 0, "C");
                    $oPdf->cell($iWDescricaoSemValor, $iHeigth, $oBem->t52_descr, 0, 0, "L");
                    if ($tipoData == "1") {
                        $oPdf->cell($iWAquisicaoSemValor, $iHeigth, $dtAquisicaoBem, 0, 0, "C");
                    } else {
                        $oPdf->cell($iWAquisicaoSemValor, $iHeigth, $dtInclusaoBem, 0, 0, "C");
                    }

                    /**
                     * Caso o usuário tenha solicitado a impressão dos valores, os mesmos serão
                     * impressos neste bloco
                     */
                    if ($oGet->lImprimeValorAquisicao) {
                        $oDaoBensHistoricoCalculoBem = new cl_benshistoricocalculobem();
                        $sSqlBuscaHistoricoCalculoBem = $oDaoBensHistoricoCalculoBem->sql_query_calculo(null,
                            "t58_valoranterior",
                            "t58_sequencial asc limit 1",
                            "    t58_bens = {$oBem->t52_bem}
                                                                                           and t58_benstipodepreciacao <> 6");
                        $rsValorAnterior = db_query($sSqlBuscaHistoricoCalculoBem);
                        $nValorAnterior = $oBem->t52_valaqu;

                        if ($nValorAnterior == 0) {
                            $nValorAnterior = $valorAtual;
                        }

                        if (pg_num_rows($rsValorAnterior) == 1) {
                            $nValorAnterior = db_utils::fieldsMemory($rsValorAnterior, 0)->t58_valoranterior;
                        }

                        $nValorDepreciado = $nValorAnterior - $valorAtual;
                        if ($oBem->t44_benstipodepreciacao == 4 || pg_num_rows($rsValorAnterior) == 0) {
                            $nValorDepreciado = 0;
                        }

                        $valorReavaliado = 0;
                        $ultimaAvaliacao = 0;

                        $valorDepreciado = $oBem->t52_valaqu - $valorAtual;

                        if (
                            $oInstituicao->getMunicipio() === "CAPIVARI DO SUL" &&
                            $oInstituicao->getDescricao() === 'CAMARA MUNICIPAL DE VEREADORES'
                        ) {
                            if ($valorDepreciado <= 0) {
                                $valorDepreciado = 0;
                            }
                        }

                        $valorAtual = $valorAtual;
                        $valorAux = $oBem->t52_valaqu;

                        if ($valorAtual == ($oBem->t52_valaqu + $oBem->t44_valorresidual) && $valorDepreciado < 0) {
                            $valorDepreciado = 0;
                        }

                        if (isset($bensReavaliados[$oBem->t52_bem])) {

                            $sSql ="select
                                        sum(t58_valorcalculado) as soma
                                    from
                                        benshistoricocalculobem
                                    inner join benshistoricocalculo on
                                        benshistoricocalculo.t57_sequencial = benshistoricocalculobem.t58_benshistoricocalculo
                                    inner join bensdepreciacao on
                                        bensdepreciacao.t44_bens = benshistoricocalculobem.t58_bens
                                    where
                                        t58_bens = {$oBem->t52_bem}
                                        and benshistoricocalculo.t57_datacalculo >= (
                                        select
                                            max(benshistoricocalculo.t57_datacalculo)
                                        from
                                            benshistoricocalculobem
                                        join benshistoricocalculo on
                                            benshistoricocalculo.t57_sequencial = benshistoricocalculobem.t58_benshistoricocalculo
                                        where
                                            t58_bens = {$oBem->t52_bem}
                                            and benshistoricocalculobem.t58_benstipodepreciacao = 6)
                                        and t58_bens = {$oBem->t52_bem}
                                        and t57_processado is true;
                                    ";
                            $rsSoma = db_query($sSql);
                            $sSoma = db_utils::fieldsMemory($rsSoma, 0)->soma;
                            if ($sSoma < 0) {
                            $valorDepreciado = 0;
                            } else{
                                $valorDepreciado = $sSoma;
                            }
                            $valorReavaliado = $oBem->valor_ultimaavaliacao;
                            if ($valorDepreciado == $oBem->t44_valorresidual) {
                                $valorDepreciado = 0;
                            }

                            $valorAux = $valorReavaliado > $oBem->t52_valaqu ? $valorReavaliado : $oBem->t52_valaqu;
                        }

                        /*
                        * Necessário buscar direto, pois o calculo de Capivari antes de migrarem é diferente da DBseller, até 31/12/2024.
                        */
                        if (isCapivari() && $iInstituicaoSessao == 2) {
                            $sSql = "select
                                        t58_valoratual
                                    from
                                        benshistoricocalculobem
                                        inner join benshistoricocalculo on t57_sequencial = t58_benshistoricocalculo
                                    where
                                    t58_bens = {$oBem->t52_bem}
                                    and t57_ativo is true
                                    and t57_processado is true
                                    order by
                                        t57_ano desc,
                                        t57_mes desc
                                    limit 1";

                            $rsAux = db_query($sSql);
                            if ($rsAux && pg_num_rows($rsAux) > 0) {
                                $valorAux = db_utils::fieldsMemory($rsAux, 0);

                                // Porém, a partir de 2024, o calculo de Capivari será igual o da Dbseller.
                                if ($valorAux->t57_ano <= 2023) {
                                    $valorAtual = $valorAux->t58_valoratual;
                                }
                            }

                        } elseif ($valorAtual > $valorAux){
                            /*
                            * Necessário por causa de um bug ao desprocessar um bem reavalido
                            */
                            $valorAtual = $valorAux;
                        }

                        $saldoDepreciavel = $valorAtual - $oBem->t44_valorresidual;

                        if ($oGet->substituirAquisicao === '2') {
                            $ultimaAvaliacao = $valorReavaliado;

                            if ($valorReavaliado == 0) {
                                $ultimaAvaliacao = $oBem->t52_valaqu;
                            }
                                $oPdf->cell(17, $iHeigth, db_formatar($ultimaAvaliacao, "f"), 0, 0, "R");
                        } else {
                            $oPdf->cell(17, $iHeigth, db_formatar($oBem->t52_valaqu, "f"), 0, 0, "R");
                        }

                        $oPdf->cell(24, $iHeigth, db_formatar($valorReavaliado, "f"), 0, 0, "C");
                        $oPdf->cell(22, $iHeigth, db_formatar($oBem->t44_valorresidual, "f"), 0, 0, "C");
                        $oPdf->cell(24, $iHeigth, db_formatar($valorDepreciado, "f"), 0, 0, "C");
                        $oPdf->cell(20, $iHeigth, db_formatar($valorAtual, "f"), 0, 0, "C");
                        $oPdf->cell(24, $iHeigth, db_formatar($oBem->valorDepreciavel, "f"), 0, 0, "C");
                        $oPdf->cell(24, $iHeigth, db_formatar($saldoDepreciavel, "f"), 0, $lineBreak, "C");

                        if ($oGet->substituirAquisicao === '2') {
                            $oTotal->nValorAquisicao += $ultimaAvaliacao;
                        } else {
                            $oTotal->nValorAquisicao += $oBem->t52_valaqu;
                        }
                        $oTotal->nValorReavaliado += $valorReavaliado;
                        $oTotal->nValorResidual += $oBem->t44_valorresidual;
                        $oTotal->nValorDepreciado += $valorDepreciado;
                        $oTotal->nValorAtual += $valorAtual;
                        $oTotal->nValorDepreciavel += $oBem->valorDepreciavel;
                        $oTotal->nSaldoDepreciavel += $saldoDepreciavel;

                        if ($oGet->substituirAquisicao === '2') {
                            $oTotalDivisao->nValorAquisicao += $ultimaAvaliacao;
                        } else {
                            $oTotalDivisao->nValorAquisicao += $oBem->t52_valaqu;
                        }
                        $oTotalDivisao->nValorReavaliado += $valorReavaliado;
                        $oTotalDivisao->nValorResidual += $oBem->t44_valorresidual;
                        $oTotalDivisao->nValorDepreciado += $valorDepreciado;
                        $oTotalDivisao->nValorAtual += $valorAtual;
                        $oTotalDivisao->nValorDepreciavel += $oBem->valorDepreciavel;
                        $oTotalDivisao->nSaldoDepreciavel += $saldoDepreciavel;
                    }

                    if ($flagColunasVisiveis) {
                        $oPdf->cell($iWEstadoSemValor, $iHeigth, $oBem->estadobem, 0, 0, "C");
                        $oPdf->cell($iWEstadoSemValor, $iHeigth, $oBem->situacaobem, 0, 1, "C");
                    }

                    if ($oGet->lCaracteristicaAdicional) {
                        $oPdf->MultiCell($iTamanhoWidth, $iHeigth, $oBem->t52_obs, 1, "L", true);
                    }

                    /**
                     * Verifica se precisa adicionar uma nova página para imprimir os bens
                     */
                    if ($oPdf->gety() > $oPdf->h - 30) {
                        setHeader(
                            $oPdf,
                            $iHeigth,
                            $oGet->lImprimeValorAquisicao,
                            $oGet->lCaracteristicaAdicional,
                            $oBem->t52_depart,
                            $aOrgaosUnidade,
                            $flagColunasVisiveis,
                            $substituirAquisicao
                        );
                    }

                    $iTotalBemNaClassificacao++;
                    $iTotaBemPorClassificacao++;
                    $iTotaBemPorDivisao++;
                    $itotalRegistros++;
                }
                $oPdf->SetFont("arial", "b", 6);
                $oPdf->Cell($iTamanhoWidth, 4, "Total da Classificação: {$iTotaBemPorClassificacao}", "T", 1);
            }
        }
        $oPdf->Cell($iTamanhoWidth, 4, "Total da Divisão: {$iTotaBemPorDivisao}", "T", 1);

        if ($oGet->lImprimeValorAquisicao) {
            $oPdf->SetFont("arial", "b", 6);
            $oPdf->cell(90, $iHeigth, "Total:", 0, 0, "R");
            $oPdf->cell(24, $iHeigth, db_formatar($oTotalDivisao->nValorAquisicao, "f"), 0, 0, "R");
            $oPdf->cell(24, $iHeigth, db_formatar($oTotalDivisao->nValorReavaliado, "f"), 0, 0, "R");
            $oPdf->cell(22, $iHeigth, db_formatar($oTotalDivisao->nValorResidual, "f"), 0, 0, "R");
            $oPdf->cell(24, $iHeigth, db_formatar($oTotalDivisao->nValorDepreciado, "f"), 0, 0, "C");
            $oPdf->cell(20, $iHeigth, db_formatar($oTotalDivisao->nValorAtual, "f"), 0, 0, "C");
            $oPdf->cell(22, $iHeigth, db_formatar($oTotalDivisao->nValorDepreciavel, "f"), 0, 0, "C");
            $oPdf->cell(24, $iHeigth, db_formatar($oTotalDivisao->nSaldoDepreciavel, "f"), 0, 1, "C");
            $oPdf->SetFont("arial", "", 6);
        }

    }
}

$oPdf->SetFont("arial", "b", 7);
if ($oGet->lImprimeValorAquisicao) {
    $oPdf->SetFont("arial", "b", 8);
    $oPdf->cell(32, $iHeigth, "Total de Registros: {$itotalRegistros}", 0, 0, "R", 1);
    $oPdf->cell(60, $iHeigth, "Total:", 0, 0, "R", 1);
    $oPdf->cell(22, $iHeigth, db_formatar($oTotal->nValorAquisicao, "f"), 0, 0, "R", 1);
    $oPdf->cell(22, $iHeigth, db_formatar($oTotal->nValorReavaliado, "f"), 0, 0, "R", 1);
    $oPdf->cell(22, $iHeigth, db_formatar($oTotal->nValorResidual, "f"), 0, 0, "R", 1);
    $oPdf->cell(22, $iHeigth, db_formatar($oTotal->nValorDepreciado, "f"), 0, 0, "R", 1);
    $oPdf->cell(20, $iHeigth, db_formatar($oTotal->nValorAtual, "f"), 0, 0, "R", 1);
    $oPdf->cell(24, $iHeigth, db_formatar($oTotal->nValorDepreciavel, "f"), 0, 0, "C", 1);
    $oPdf->cell(24, $iHeigth, db_formatar($oTotal->nSaldoDepreciavel, "f"), 0, 0, "C", 1);
    $oPdf->cell(55, $iHeigth, "", 0, 1, "C", 1);
    $oPdf->SetFont("arial", "", 6);
} else {
    $oPdf->Cell($iTamanhoWidth, 4, "Total de Registros: {$itotalRegistros}", "T", 1);
}
$oPdf->SetFont("arial", "", 6);

/**
 * Função que imprime o cabeçalho do relatório
 * @param PDF $oPdf
 * @param integer $iHeigth
 * @param boolean $lImprimeValores
 * @param integer $iCodigoDepartamento
 * @param array $aOrgaosUnidade
 */


function setHeader(
  $oPdf,
  $iHeigth,
  $lImprimeValores,
  $lImprimeCaracteristicaAdicional,
  $iCodigoDepartamento,
  $aOrgaosUnidade,
  $flagColunasVisiveis,
  $substituirAquisicao
) {
    global $head3, $head4, $head5, $head6, $tipoData, $sDataInicial, $sDataFinal;
    $nomeData = $tipoData == "1" ? "Aquisição" : "Inclusão";
    $dataInicial = implode('/', array_reverse(explode('-', $sDataInicial)));
    $dataFinal = implode('/', array_reverse(explode('-', $sDataFinal)));



    /**
     * Verifica se os valores terão de ser impressos. Caso SIM
     * é adicionado uma página no formato PAISAGEM.
     */
    $head3 = "Órgão: {$aOrgaosUnidade[$iCodigoDepartamento]->db01_orgao} - {$aOrgaosUnidade[$iCodigoDepartamento]->orgao}";
    $head4 = "Unidade: {$aOrgaosUnidade[$iCodigoDepartamento]->db01_unidade} - {$aOrgaosUnidade[$iCodigoDepartamento]->unidade}";
    $head5 = "Departamento: {$aOrgaosUnidade[$iCodigoDepartamento]->codigodepartamento} - {$aOrgaosUnidade[$iCodigoDepartamento]->descricaodepartamento}";
    $head6 = "Período Selecionado: {$dataInicial} á {$dataFinal}";


    if ($lImprimeValores) {
        $oPdf->AddPage("L");
        $iWidthPagina = "280";
    } else {
        $oPdf->AddPage("P");
        $iWidthPagina = "190";
    }

    $lineBreak = 1;
    if ($flagColunasVisiveis) {
        $lineBreak = 0;
    }

    $oPdf->setfont('arial', 'b', 8);
    $oPdf->setfillcolor(235);
    $oPdf->cell(15, $iHeigth, "Código", 1, 0, "C", 1);
    $oPdf->cell(17, $iHeigth, "Placa", 1, 0, "C", 1);
    $oPdf->cell(50, $iHeigth, "Descrição", 1, 0, "C", 1);
    $oPdf->cell(15, $iHeigth, $nomeData, 1, 0, "C", 1);
    // 24 Vlr. Reavaliado
    if ($lImprimeValores) {
        if ($substituirAquisicao === '2') {
            $oPdf->cell(24, $iHeigth, "Última avaliação", 1, 0, "C", 1);
        } else {
            $oPdf->cell(22, $iHeigth, "Vlr. Aquisição", 1, 0, "C", 1);
        }
        $oPdf->cell(22, $iHeigth, "Vlr. Reavaliado", 1, 0, "C", 1);
        $oPdf->cell(22, $iHeigth, "Vlr. Residual", 1, 0, "C", 1);
        $oPdf->cell(22, $iHeigth, "Vlr. Depreciado", 1, 0, "C", 1);
        $oPdf->cell(20, $iHeigth, "Vlr. Atual", 1, 0, "C", 1);
        $oPdf->cell(24, $iHeigth, "Vlr. Depreciável", 1, 0, "C", 1);
        $oPdf->cell(24, $iHeigth, "Sal. Depreciável", 1, $lineBreak, "C", 1);
    }

    if ($flagColunasVisiveis) {
        $oPdf->cell(14, $iHeigth, "Estado", 1, 0, "C", 1);
        $oPdf->cell(13, $iHeigth, "Baixado", 1, 1, "C", 1);
    }

    if ($lImprimeCaracteristicaAdicional) {
        $oPdf->cell($iWidthPagina, $iHeigth, "Características Adicionais", 1, 1, "L", 1);
    }
}

$oPdf->Output();
