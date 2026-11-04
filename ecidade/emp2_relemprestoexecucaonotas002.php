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
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("libs/db_liborcamento.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("fpdf151/assinatura.php");
require_once modification("libs/db_libcontabilidade.php");

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);

$clempresto = new cl_empresto;
$clorcprojativ = new cl_orcprojativ;
$clselorcdotacao = new cl_selorcdotacao();
$oClassinatura = new cl_assinatura();

$clselorcdotacao->setDados($filtra_despesa); // passa os parametros vindos da func_selorcdotacao_abas.php
$sql_filtro = $clselorcdotacao->getDadosComplemento(false);

$sParametros = null;
if ($filtra_despesa != "geral") {
    $sParametros = $clselorcdotacao->getParametros();
}

$xinstit = explode("-", $db_selinstit);
$listaInstituicoes = str_replace('-', ', ', $db_selinstit);
$resultinst = db_query("select codigo,nomeinstabrev from db_config where codigo in ($listaInstituicoes) ");
$descr_inst = '';
$xvirg = '';
for ($xins = 0; $xins < pg_num_rows($resultinst); $xins++) {
    db_fieldsmemory($resultinst, $xins);
    $descr_inst .= $xvirg . $nomeinstabrev;
    $xvirg = ', ';
}

$sele_work = " e60_instit in ($listaInstituicoes) ";
$sele_work1 = '';//tipo de recurso
$anoatual = db_getsession("DB_anousu");

$tipoEmpenho = "Todos";
if ($emptipo != 0) {
    $tipoEmpenho = $emptipo_descricao;
    $sele_work .= " and empempenho.e60_codtipo = {$emptipo}";
}

/*
 * Acrescentado restantes das opções de impressao
 */
if ($impressao == 0) {
    $sOpImpressao = 'Analítico';
} else {
    $sOpImpressao = 'Sintético';
}
if ($exercicio == 0) {
    $sExercicio = 'Todos';
} else {
    $sExercicio = $exercicio;
}

//filtro por posição
//$dtini = db_getsession("DB_anousu").'-01-01';
$dtini = $dtini_ano . "-" . $dtini_mes . "-" . $dtini_dia;
$dtfim = $dtfim_ano . "-" . $dtfim_mes . "-" . $dtfim_dia;

//filtro por agrupamento
$sql_order = "";
if ($tipo == "or") {// orgão - tabela orcdotacao
    $tipofiltro = " orgão";
    $sql_order = " order by o58_orgao,e60_anousu,e60_codemp::integer";
}

if ($tipo == "un") {// unidade - tabela orcdotacao
    $tipofiltro = "Unidade";
    $sql_order = " order by  o58_orgao,o58_unidade,e60_anousu,e60_codemp::integer ";
}

if ($tipo == "fu") {//Função  - tabela orcdotacao
    $tipofiltro = "Função";
    $sql_order = " order by o58_funcao,e60_anousu,e60_codemp::integer";
}

if ($tipo == "su") {//subfunção - tabela orcdotacao
    $tipofiltro = "Subfunção";
    $sql_order = " order by o58_subfuncao,e60_anousu,e60_codemp::integer";
}

if ($tipo == "pr") {//programa - tabela orcdotacao
    $tipofiltro = "Programa";
    $sql_order = " order by o58_programa,e60_anousu,e60_codemp::integer";
}

if ($tipo == "pa") {//projeto atividade - tabela orcdotacao
    $tipofiltro = "Projeto Atividade";
    $sql_order = " order by o58_projativ,e60_anousu,e60_codemp::integer";
}

if ($tipo == "el") {//elemento - tabela orcdotacao
    $tipofiltro = "Elemento";
    $sql_order = " order by o56_elemento, o58_codele, e60_anousu,e60_codemp::integer";
}

$sql_where_externo = ' ';

if ($tipo == 'de') {
    $tipofiltro = "Desdobramento";
    $listaDesdobramentos = explode(',', $desdobramentos);

    if ($listaDesdobramentos[0]) {
        $sql_where_externo = "AND e64_codele IN ({$desdobramentos})";
    }

    $sql_order = " order by e64_codele,e60_anousu,e60_codemp::integer";
}

if ($tipo == "re") {//recurso - tabela empresto
    $tipofiltro = "Recurso";
    $sql_order = " order by ";
    if (isNIteroi()) {
        $sql_order .= " gestao, ";
    } else {
        $sql_order .= " e91_recurso, ";
    }
    $sql_order .= " e60_anousu,e60_codemp::integer";
}

if ($tipo == "tr") {//resto - tabela empresto
    $tipofiltro = "Tipo de Resto";
    $sql_order = "order by e91_codtipo,e60_anousu,e60_codemp::integer";
}

if ($tipo == "cr") {//credor - tabela cgm
    $tipofiltro = "Credor";
    $sql_order = " order by z01_nome,e60_anousu,e60_codemp::integer ";
}

if ($tipo == "ex") {
    $tipofiltro = "Exercício";
    $sql_order = " order by e60_anousu, e60_codemp::integer ";
}

//filtro por restos a pagar

if ($commov == "0") {//geral
    $commovfiltro = "Todos";
    $sql_where_externo .= "  ";
}

if ($commov == "1") {//com movimento até a data
    $commovfiltro = "Com movimento até a data";
    $sql_where_externo = "and (round(vlranu,2) + round(vlrliq,2) + round(vlrpag,2)) > 0 and $sele_work";
}

if ($commov == "2") {//com saldo a pagar ok
    $commovfiltro = "Com saldo a pagar";
    $sql_where_externo .= "and (
                                 (
                                   (round(round(e91_vlremp, 2) - (round(e91_vlranu, 2) + round(vlranu, 2)), 2))
                                   - (round(e91_vlrpag, 2) + round(vlrpag, 2) + round(vlrpagnproc,2))
                                  ) > 0
                                )";
}

if ($commov == "3") {//liquidados
    $commovfiltro = "Liquidados";
    $sql_where_externo .= "and (round(vlrliq,2)) > 0 ";
}

if ($commov == "4") {//anulados
    $commovfiltro = "Anulados";
    $sql_where_externo .= " and (round(vlranu,2)) > 0";
}

if ($commov == "5") {//pagos
    $commovfiltro = "Pagos";
    $sql_where_externo .= "and (round(vlrpag,2) > 0 or round(vlrpagnproc,2)  > 0)";
}

if ($commov == "6") {//Não liquidados
    $commovfiltro = "Não liquidados";
    $sql_where_externo .= "and (
                                 (
                                   (round(round(e91_vlremp, 2) - (round(e91_vlranu, 2) + round(vlranu, 2)), 2))
                                   - (round(e91_vlrliq, 2) + round(vlrliq, 2))
                                  ) > 0
                                ) ";
}

//filtro por exercicio
if ($exercicio != 0) {
    $sql_where_externo .= ' and e60_anousu = ' . $exercicio;
}

if ($listacredor != "") {
    if (isset($vercredor) and $vercredor == "com") {
        $sql_where_externo .= " and e60_numcgm in  ($listacredor)";
    } else {
        $sql_where_externo .= " and e60_numcgm not in  ($listacredor)";
    }
}

$tipoInscricao = "Tipo de Inscricão: Todos";
if (!empty($_POST['tipoInscricao']) && $_POST['tipoInscricao'] == 2) {
    $sql_where_externo .= " and (round(e91_vlremp, 2) - round(e91_vlranu, 2) - round(e91_vlrliq, 2)) > 0 ";
    $tipoInscricao = 'Tipo de inscrisão: Não Processados';
}

if (!empty($_POST['tipoInscricao']) && $_POST['tipoInscricao'] == 3) {
    $sql_where_externo .= " and (round(e91_vlrliq,2)- round(e91_vlrpag,2)) > 0 ";
    $tipoInscricao = 'Tipo de inscrisão: Processados';
}
$sql_where_externo .= " and " . $sql_filtro;

isValenca()? $sqlempresto = $clempresto->sql_rp_novo_com_processo_administrativo(
    db_getsession("DB_anousu"),
    $sele_work,
    $dtini,
    $dtfim,
    $sele_work1,
    $sql_where_externo,
    "$sql_order "
) : $sqlempresto = $clempresto->sql_rp_novo(
    db_getsession("DB_anousu"),
    $sele_work,
    $dtini,
    $dtfim,
    $sele_work1,
    $sql_where_externo,
    "$sql_order "
) ;

$res = $clempresto->sql_record($sqlempresto);
$rows = $clempresto->numrows;

if ($clempresto->numrows == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Sem movimentação de restos a pagar.");
    exit;
}

$tam = "10";
$tam2 = "5";
$troca = 1;

//variaveis agrupamentos
$vnumcgm = null;
$vorgao = null;
$vunidade = null;
$vfuncao = null;
$vsubfuncao = null;
$vprojativ = null;
$velemento = null;
$vdesdobramento = null;
$vrecurso = null;
$vprograma = null;
$vtiporesto = null;
$vanousu = null;

$mValorAgrupamento = null;

$subtotal_rp_n_proc = 0;
$subtotal_rp_proc = 0;
$subtotal_anula_rp_n_proc = 0;
$subtotal_anula_rp_proc = 0;
$subtotal_mov_liquida = 0;
$subtotal_mov_pagmento = 0;
$subtotal_mov_pagnproc = 0;
$subtotal_aliquidar_finais = 0;
$subtotal_liquidados_finais = 0;
$subtotal_geral_finais = 0;


//total
$total_rp_n_proc = 0;
$total_rp_proc = 0;

$total_anula_rp_n_proc = 0;
$total_anula_rp_proc = 0;


$total_mov_liquida = 0;
$total_mov_pagmento = 0;
$total_mov_pagnproc = 0;

$total_aliquidar_finais = 0;
$total_liquidados_finais = 0;
$total_geral_finais = 0;
//

$verifica = true;
$estrutura = "";
$projativ = "";
$o55anousu = "";
$vprojativ = "";

$handle = null;
$pdf = null;

if (!isset($formato) || empty($formato)) {
    $formato = "pdf";
}

isValenca() ? $fontParam = 6 : $fontParam = 7;


if ($formato == "pdf") {
    $pdf = new \ECidade\Pdf\Pdf(); // abre a classe
    $pdf->init(false); // abre o relatorio
    $pdf->addTitulo("INSTITUIÇÃO(S): $descr_inst");
    $pdf->addTitulo("Posição: ".db_formatar($dtini, 'd')." até ".db_formatar($dtfim, 'd'));
    $pdf->addTitulo("Agrupado por: $tipofiltro ");
    $pdf->addTitulo("Restos a pagar: $commovfiltro");
    $pdf->addTitulo("Exercicio: {$sExercicio}");
    $pdf->addTitulo("Opção de Impressão: {$sOpImpressao}");
    $pdf->addTitulo("Tipo de Empenho: {$tipoEmpenho}");
    $pdf->addTitulo($tipoInscricao);

    $pdf->AliasNbPages(); // gera alias para as paginas
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(235);
    $pdf->setAutoPageBreak(false);
} else {
    $caminhoArquivo = "tmp/execucao_rp.csv";
    $nomeArquivo = "Relatério Execução de restos a pagar CSV";
    $handle = fopen($caminhoArquivo, "w+");

    $linhaCSV = "INSTITUIÇÃO(S): {$descr_inst} \n";
    $linhaCSV .= "Posição: ".db_formatar($dtini, 'd')." até ".db_formatar($dtfim, 'd')."\n";
    $linhaCSV .= "Agrupado por: {$tipofiltro} \n";
    $linhaCSV .= "Restos a pagar: {$commovfiltro} \n";
    $linhaCSV .= "Exercicio: {$sExercicio} \n";
    $linhaCSV .= "Opção de Impressão: {$sOpImpressao} \n";
    $linhaCSV .= "Tipo de Empenho: {$tipoEmpenho}";
    fwrite($handle, $linhaCSV."\n");

    $linhaCSV = "Dados cadastrais dos empenhos";
    $linhaCSV .= ";;;Saldos a pagar anteriores";
    $linhaCSV .= ";;Movimentação dos restos a pagar no período";
    $linhaCSV .= ";;;;;Saldo a pagar finais\n";

    $linhaCSV .= ";;;;;Anulação";
    $linhaCSV .= ";;Liquidação";
    $linhaCSV .= ";Pagamento\n";

    $linhaCSV .= "Empenho";
    $linhaCSV .= ";Emissão";
    $linhaCSV .= ";Credor";
    $linhaCSV .= ";RP não proc";
    $linhaCSV .= ";RP proc";
    $linhaCSV .= ";RP não proc";
    $linhaCSV .= ";RP proc";
    $linhaCSV .= ";;RP não proc";
    $linhaCSV .= ";RP proc";
    $linhaCSV .= ";A liquidar ";
    $linhaCSV .= ";Liquidados ";
    $linhaCSV .= ";Geral ";
    fwrite($handle, $linhaCSV."\n");
}

for ($x = 0; $x < $rows; $x++) {
    db_fieldsmemory($res, $x);

    cabecalho($pdf, $troca);
    $troca = 0;

    $oSubtotalizador = (object)array(
        'rp_n_proc' => $subtotal_rp_n_proc,
        'rp_proc' => $subtotal_rp_proc,
        'anula_rp_n_proc' => $subtotal_anula_rp_n_proc,
        'anula_rp_proc' => $subtotal_anula_rp_proc,
        'mov_liquida' => $subtotal_mov_liquida,
        'mov_pagnproc' => $subtotal_mov_pagnproc,
        'mov_pagmento' => $subtotal_mov_pagmento,
        'aliquidar_finais' => $subtotal_aliquidar_finais,
        'liquidados_finais' => $subtotal_liquidados_finais,
        'geral_finais' => $subtotal_geral_finais
    );

    $lZerarSubtotalizador = false;

    /*
     * Adicionada condição para a logica diferenciada de utilização da informação do recurso
     */
    $agrupadorRecurso = isNiteroi()?$gestao:$e91_recurso;
    if ($mValorAgrupamento != $o58_orgao && $tipo == "or") {
        if ($mValorAgrupamento !== null) {
            exibirSubtotal($pdf, $oSubtotalizador, $formato, $handle);
            $lZerarSubtotalizador = true;
        }

        $mValorAgrupamento = $o58_orgao;
    }

    if ($mValorAgrupamento != $o58_unidade.$o58_orgao && $tipo == "un") {
        if ($mValorAgrupamento !== null) {
            exibirSubtotal($pdf, $oSubtotalizador, $formato, $handle);
            $lZerarSubtotalizador = true;
        }

        $mValorAgrupamento = $o58_unidade;
    }

    if ($mValorAgrupamento != $o58_funcao && $tipo == "fu") {
        if ($mValorAgrupamento !== null) {
            exibirSubtotal($pdf, $oSubtotalizador, $formato, $handle);
            $lZerarSubtotalizador = true;
        }

        $mValorAgrupamento = $o58_funcao;
    }

    if ($mValorAgrupamento != $o58_subfuncao && $tipo == "su") {
        if ($mValorAgrupamento !== null) {
            exibirSubtotal($pdf, $oSubtotalizador, $formato, $handle);
            $lZerarSubtotalizador = true;
        }

        $mValorAgrupamento = $o58_subfuncao;
    }


    if ($mValorAgrupamento != $o58_programa && $tipo == "pr") {
        if ($mValorAgrupamento !== null) {
            exibirSubtotal($pdf, $oSubtotalizador, $formato, $handle);
            $lZerarSubtotalizador = true;
        }

        $mValorAgrupamento = $o58_programa;
    }


    if ($mValorAgrupamento != $o58_projativ && $tipo == "pa") {
        if ($mValorAgrupamento !== null) {
            exibirSubtotal($pdf, $oSubtotalizador, $formato, $handle);
            $lZerarSubtotalizador = true;
        }

        $mValorAgrupamento = $o58_projativ;
    }

    $elemento = substr($o56_elemento, 0, 7);
    if ($mValorAgrupamento != $elemento && $tipo == "el") {
        if ($mValorAgrupamento !== null) {
            exibirSubtotal($pdf, $oSubtotalizador, $formato, $handle);
            $lZerarSubtotalizador = true;
        }
        $mValorAgrupamento = $elemento;
    }


    if ($mValorAgrupamento != $e64_codele && $tipo == "de") {
        if ($mValorAgrupamento !== null) {
            exibirSubtotal($pdf, $oSubtotalizador, $formato, $handle);
            $lZerarSubtotalizador = true;
        }

        $mValorAgrupamento = $e64_codele;
    }

    if ($mValorAgrupamento != $agrupadorRecurso && $tipo == "re") {
        if ($mValorAgrupamento !== null) {
            exibirSubtotal($pdf, $oSubtotalizador, $formato, $handle);
            $lZerarSubtotalizador = true;
        }

        $mValorAgrupamento = $agrupadorRecurso;
    }

    if ($mValorAgrupamento != $e91_codtipo && $tipo == "tr") {
        if ($mValorAgrupamento !== null) {
            exibirSubtotal($pdf, $oSubtotalizador, $formato, $handle);
            $lZerarSubtotalizador = true;
        }

        $mValorAgrupamento = $e91_codtipo;
    }

    if ($mValorAgrupamento != $z01_numcgm && $tipo == "cr") {
        if ($mValorAgrupamento !== null) {
            exibirSubtotal($pdf, $oSubtotalizador, $formato, $handle);
            $lZerarSubtotalizador = true;
        }

        $mValorAgrupamento = $z01_numcgm;
    }

    if ($mValorAgrupamento != $e60_anousu && $tipo == "ex") {
        if ($mValorAgrupamento !== null) {
            exibirSubtotal($pdf, $oSubtotalizador, $formato, $handle);
            $lZerarSubtotalizador = true;
        }

        $mValorAgrupamento = $e60_anousu;
    }

    if ($lZerarSubtotalizador) {
        $subtotal_rp_n_proc = 0;
        $subtotal_rp_proc = 0;
        $subtotal_anula_rp_n_proc = 0;
        $subtotal_anula_rp_proc = 0;
        $subtotal_mov_liquida = 0;
        $subtotal_mov_pagmento = 0;
        $subtotal_mov_pagnproc = 0;
        $subtotal_aliquidar_finais = 0;
        $subtotal_liquidados_finais = 0;
        $subtotal_geral_finais = 0;
    }

    //filtro por orgão
    if ($tipo == "or" and $vorgao != $o58_orgao) {// orgão
        if (isset($quebradepagina) and $verifica == false) {
            $troca = 1;
            cabecalho($pdf, $troca);
        }

        if ($formato == "pdf") {
            $pdf->SetFont('Arial', 'B', $fontParam);
            $pdf->cell(0, 2, "", 0, 1, "", 0);
            $pdf->cell(0, 5, " Orgão: $o58_orgao $o40_descr ", 0, 1, "L", 0);
        } else {
            $linhaCSV = "\nOrgão: $o58_orgao $o40_descr\n";
            fwrite($handle, $linhaCSV."\n");
        }
        $vorgao = $o58_orgao;
        $verifica = false;
    }

    if ($tipo == "un" and $vunidade.$vorgao != $o58_unidade.$o58_orgao) {//unidade
        if (isset($quebradepagina) and $verifica == false) {
            $troca = 1;
            cabecalho($pdf, $troca);
        }

        if ($formato == "pdf") {
            $pdf->SetFont('Arial', 'B', $fontParam);
            $pdf->cell(0, 2, "", 0, 1, "", 0);
            $pdf->cell(0, 5, " orgão:$o58_orgao $o40_descr  ", 0, 1, "L", 0);
            $pdf->cell(0, 5, "Unidade:$o58_unidade $o41_descr  ", 0, 1, "L", 0);
        } else {
            $linhaCSV = "\n orgão:$o58_orgao $o40_descr  \n";
            $linhaCSV .= "Unidade:$o58_unidade $o41_descr ";
            fwrite($handle, $linhaCSV."\n");
        }
        $vunidade = $o58_unidade;
        $vorgao = $o58_orgao;
        $verifica = false;
    }

    if ($tipo == "fu" and $vfuncao != $o58_funcao) {//Função
        if (isset($quebradepagina) and $verifica == false) {
            $troca = 1;
            cabecalho($pdf, $troca);
        }

        if ($formato == "pdf") {
            $pdf->SetFont('Arial', 'B', $fontParam);
            $pdf->cell(0, 2, "", 0, 1, "", 0);
            $pdf->cell(0, 5, "Função:$o58_funcao $o52_descr", 0, 1, "L", 0);
        } else {
            $linhaCSV = "\nFunção:$o58_funcao $o52_descr";
            fwrite($handle, $linhaCSV."\n");
        }
        $vfuncao = $o58_funcao;
        $verifica = false;
    }

    if ($tipo == "su" and $vsubfuncao != $o58_subfuncao) {//subfuncao
        if (isset($quebradepagina) and $verifica == false) {
            $troca = 1;
            cabecalho($pdf, $troca);
        }
        if ($formato == "pdf") {
            $pdf->SetFont('Arial', 'B', $fontParam);
            $pdf->cell(0, 2, "", 0, 1, "", 0);
            $pdf->cell(0, 5, "Subfunção:$o58_subfuncao $o53_descr  ", 0, 1, "L", 0);
        } else {
            $linhaCSV = "\nSubfunção:$o58_subfuncao $o53_descr  ";
            fwrite($handle, $linhaCSV."\n");
        }
        $vsubfuncao = $o58_subfuncao;
        $verifica = false;
    }

    if ($tipo == "pr" and $vprograma != $o58_programa) {//programa
        if (isset($quebradepagina) and $verifica == false) {
            $troca = 1;
            cabecalho($pdf, $troca);
        }
        if ($formato == "pdf") {
            $pdf->SetFont('Arial', 'B', $fontParam);
            $pdf->cell(0, 2, "", 0, 1, "", 0);
            $pdf->cell(0, 5, "Programa:$o58_programa $o54_descr ", 0, 1, "L", 0);
        } else {
            $linhaCSV = "\nPrograma:$o58_programa $o54_descr ";
            fwrite($handle, $linhaCSV."\n");
        }
        $vprograma = $o58_programa;
        $verifica = false;
    }

    if ($tipo == "pa" and $vprojativ != $o58_projativ) {//projetto atividade
        if (isset($quebradepagina) and $verifica == false) {
            $troca = 1;
            cabecalho($pdf, $troca);
        }
        if ($vprojativ != $o58_projativ or $o55anousu != $e60_anousu) {
            if ($formato == "pdf") {
                $pdf->SetFont('Arial', 'B', $fontParam);
                $pdf->cell(0, 2, "", 0, 1, "", 0);
                $pdf->cell(0, 5, "Projeto/atividade:$o58_projativ $o55_descr", 0, 1, "L", 0);
            } else {
                $linhaCSV = "\nProjeto/atividade:$o58_projativ $o55_descr";
                fwrite($handle, $linhaCSV."\n");
            }
            $projativ = $o58_projativ;
            $vprojativ = $o58_projativ;
            $o55anousu = $e60_anousu;
        }

        $verifica = false;
    }

    $elemento = substr($o56_elemento, 0, 7);
    if ($tipo == "el" and $velemento != $elemento) {//elemento
        if (isset($quebradepagina) and $verifica == false) {
            $troca = 1;
            cabecalho($pdf, $troca);
        }
        $elementoCodigo = str_pad($elemento, 13, "0", STR_PAD_RIGHT);
        $elementoTitulo = getElementoSintetico($elementoCodigo, $e91_anousu);
        if ($formato == "pdf") {
            $pdf->SetFont('Arial', 'B', $fontParam);
            $pdf->cell(0, 2, "", 0, 1, "", 0);
            $pdf->cell(0, 5, "Elemento: {$elementoCodigo} {$elementoTitulo}  ", 0, 1, "L", 0);
        } else {
            $linhaCSV = "\nElemento: {$elementoCodigo} {$elementoTitulo}  ";
            fwrite($handle, $linhaCSV."\n");
        }
        $velemento = $elemento;
        $verifica = false;
    }

    if ($tipo == 'de') {
        $resdesdob = retorna_desdob(substr($o56_elemento, 0, 7), $e64_codele);
        $numrows = pg_num_rows($resdesdob);

        for ($i = 0; $i < $numrows; $i++) {
            db_fieldsmemory($resdesdob, $i);

            if ($estrutural != $estrutura) {
                if (isset($quebradepagina) && $verifica == false) {
                    $troca = 1;
                    cabecalho($pdf, $troca);
                }

                if ($formato == "pdf") {
                    $pdf->SetFont('Arial', 'B', $fontParam);
                    $pdf->cell(0, 3, '', 0, 1, 'L', 0);
                    $pdf->cell(0, 5, "Desdobramento: {$estrutural} {$descr}", 0, 1, 'L', 0);
                } else {
                    $linhaCSV = "\nDesdobramento: {$estrutural} {$descr}";
                    fwrite($handle, $linhaCSV."\n");
                }
                $estrutura = $estrutural;
                $verifica = false;
            }
        }
    }

    if ($tipo == "re" and $vrecurso != $agrupadorRecurso) {//recurso
        if (isset($quebradepagina) and $verifica == false) {
            $troca = 1;
            cabecalho($pdf, $troca);
        }
        if ($formato == "pdf") {
            $pdf->SetFont('Arial', 'B', $fontParam);
            $pdf->cell(0, 2, "", 0, 1, "", 0);
            $descricao = descricaoCompletaRecurso($e91_recurso, $anoatual, 10);

            $pdf->cell(0, 5, "Recurso: $descricao", 0, 1, "L", 0);
        } else {
            $linhaCSV = "\nRecurso: $descricao";
            fwrite($handle, $linhaCSV."\n");
        }
        $vrecurso = $agrupadorRecurso;
        $verifica = false;
    }

    if ($tipo == "tr" and $vtiporesto != $e91_codtipo) {//tipo resto
        if (isset($quebradepagina) and $verifica == false) {
            $troca = 1;
            cabecalho($pdf, $troca);
        }
        if ($formato == "pdf") {
            $pdf->SetFont('Arial', 'B', $fontParam);
            $pdf->cell(0, 2, "", 0, 1, "", 0);
            $pdf->cell(0, 5, "Tipo de resto: $e91_codtipo $e90_descr   ", 0, 1, "L", 0);
        } else {
            $linhaCSV = "\nTipo de resto: $e91_codtipo $e90_descr   ";
            fwrite($handle, $linhaCSV."\n");
        }
        $vtiporesto = $e91_codtipo;
        $verifica = false;
    }

    if ($tipo == "cr" and $vnumcgm != $z01_numcgm) {//credor
        if ((isset($quebradepagina) and $verifica == false)) {
            $troca = 1;
            cabecalho($pdf, $troca);
        }
        if ($formato == "pdf") {
            $pdf->SetFont('Arial', 'B', $fontParam);
            $pdf->cell(0, 2, "", 0, 1, "", 0);
            $pdf->cell(
                0,
                5,
                "Credor:" . $z01_numcgm . " CNPJ:" . db_formatar($z01_cgccpf, 'cnpj') . " " . substr($z01_nome, 0, 100),
                0,
                1,
                "L",
                0
            );
        } else {
            $linhaCSV = "\nCredor:" . $z01_numcgm . " CNPJ:" . db_formatar($z01_cgccpf, 'cnpj');
            $linhaCSV .= " ".substr($z01_nome, 0, 100);
            fwrite($handle, $linhaCSV."\n");
        }
        $vnumcgm = $z01_numcgm;
        $verifica = false;
    }

    if ($tipo == "ex" && $vanousu != $e60_anousu) {
        if ((isset($quebradepagina) && $verifica == false)) {
            $troca = 1;
            cabecalho($pdf, $troca);
        }

        if ($formato == "pdf") {
            $pdf->SetFont('Arial', 'B', $fontParam);
            $pdf->cell(0, 2, "", 0, 1, "", 0);
            $pdf->cell(0, 5, "Exercício: " . $e60_anousu, 0, 1, "L", 0);
        } else {
            $linhaCSV = "\nExercício: {$e60_anousu}";
            fwrite($handle, $linhaCSV."\n");
        }
        $vanousu = $e60_anousu;
        $verifica = false;
    }

    if ($formato == "pdf") {
        //dados do relatério
        $pdf->SetFont('Arial', '', $fontParam);
        $tam = "5";
    }

    $total_rp_n_proc += ($e91_vlremp - $e91_vlranu - $e91_vlrliq);
    $total_rp_proc += ($e91_vlrliq - $e91_vlrpag);
    $total_anula_rp_n_proc += $vlranuliqnaoproc;
    $total_anula_rp_proc += $vlranuliq;
    $total_mov_liquida += ($vlrliq);
    $total_mov_pagmento += $vlrpag;
    $total_mov_pagnproc += $vlrpagnproc;
    $liquidado_anterior = ($e91_vlremp - $e91_vlranu - $e91_vlrliq) + ($e91_vlrliq - $e91_vlrpag);
    $apagargeral = ($liquidado_anterior - $vlranu - $vlrpag - $vlrpagnproc);
    $aliquidargeral = $e91_vlremp - (($e91_vlranu + $vlranu) + ($vlrliq + $e91_vlrliq - $vlranuliq));
    $liquidados = ($apagargeral - $aliquidargeral);
    $total_aliquidar_finais = $total_aliquidar_finais + $aliquidargeral;
    $total_liquidados_finais = $total_liquidados_finais + abs($liquidados);
    $total_geral_finais = ($total_geral_finais + $apagargeral);

    if ($impressao == '0') {
        if ($formato == "pdf") {
            //dados cadastrais dos empenhos
            $larguraVariavel = 0;

            if(isValenca()){
                $larguraVariavel = 5;
                $pdf->Cell(3*$larguraVariavel, $tam, $e150_numeroprocesso, "TBR", 0, "R", 0);
            }
            $pdf->Cell(15, $tam, ($e60_codemp . "/" . $e60_anousu), "TBR", 0, "R", 0);//empenho
            $pdf->Cell(15, $tam, db_formatar($e60_emiss, 'd'), 1, 0, "R", 0);//emissao
            $pdf->Cell(50-(3*$larguraVariavel), $tam, substr($z01_nome, 0, 25), 1, 0, "L", 0);//credor

            //saldos a pagar anteriores
            $pdf->Cell(
                20,
                $tam,
                db_formatar(abs($e91_vlremp - $e91_vlranu - $e91_vlrliq), 'f'),
                1,
                0,
                "R",
                0
                );// rp nao proc

                $pdf->Cell(20, $tam, db_formatar(abs($e91_vlrliq - $e91_vlrpag), 'f'), 1, 0, "R", 0);//rp proc

                //Movimentação dos restos a pagar no período
                $pdf->Cell(20, $tam, db_formatar(abs($vlranuliqnaoproc), 'f'), 1, 0, "R", 0);//anulacao -> rp nao proc

                $pdf->Cell(20, $tam, db_formatar(abs($vlranuliq), 'f'), 1, 0, "R", 0);//anulacao -> rp proc

                if ($c70_anousu == $anoatual) {
                    $pdf->Cell(20, $tam, db_formatar(abs($vlrliq), 'f'), 1, 0, "R", 0);//liquidado=rpproc
                } else {
                    $pdf->Cell(20, $tam, db_formatar("0", 'f'), 1, 0, "R", 0);//liquidado=rpproc
                }

                $pdf->Cell(20, $tam, db_formatar(abs($vlrpagnproc), 'f'), 1, 0, "R", 0);//pagamento
                $pdf->Cell(20, $tam, db_formatar(abs($vlrpag), 'f'), 1, 0, "R", 0);//pagamento

                // a liquidar
                $pdf->Cell(20, $tam, db_formatar(abs($aliquidargeral), 'f'), 1, 0, "R", 0);

                // liquidados
                $pdf->Cell(20, $tam, db_formatar(abs($liquidados), 'f'), 1, 0, "R", 0);

                // a pagar
                $pdf->Cell(20, $tam, db_formatar(abs($apagargeral), 'f'), "TBL", 1, "R", 0);
        } else {
            $linhaCSV = ($e60_codemp . "/" . $e60_anousu);
            $linhaCSV .= ";".db_formatar($e60_emiss, 'd');
            $linhaCSV .= ";".$z01_nome;
            $linhaCSV .= ";".db_formatar(abs($e91_vlremp - $e91_vlranu - $e91_vlrliq), 'f');
            $linhaCSV .= ";".db_formatar(abs($e91_vlrliq - $e91_vlrpag), 'f');
            $linhaCSV .= ";".db_formatar(abs($vlranuliqnaoproc), 'f');
            $linhaCSV .= ";".db_formatar(abs($vlranuliq), 'f');
            if ($c70_anousu == $anoatual) {
                $linhaCSV .= ";".db_formatar(abs($vlrliq), 'f');
            } else {
                $linhaCSV .= ";".db_formatar("0", 'f');
            }
            $linhaCSV .= ";".db_formatar(abs($vlrpagnproc), 'f');
            $linhaCSV .= ";".db_formatar(abs($vlrpag), 'f');
            $linhaCSV .= ";".db_formatar(abs($aliquidargeral), 'f');
            $linhaCSV .= ";".db_formatar(abs($liquidados), 'f');
            $linhaCSV .= ";".db_formatar(abs($apagargeral), 'f');
            fwrite($handle, $linhaCSV."\n");
        }
    }
    //subtotal
    $subtotal_rp_n_proc += $e91_vlremp - $e91_vlranu - $e91_vlrliq;
    $subtotal_rp_proc += $e91_vlrliq - $e91_vlrpag;
    $subtotal_anula_rp_n_proc += $vlranuliqnaoproc;
    $subtotal_anula_rp_proc += $vlranuliq;
    $subtotal_mov_liquida += $vlrliq;
    $subtotal_mov_pagmento += $vlrpag;
    $subtotal_mov_pagnproc += $vlrpagnproc;
    $subtotal_aliquidar_finais += $aliquidargeral;
    $subtotal_liquidados_finais += abs($liquidados);
    $subtotal_geral_finais += $apagargeral;
}


if ($subtotal_rp_n_proc != 0 ||
    $subtotal_rp_proc != 0 ||
    $subtotal_anula_rp_n_proc != 0 ||
    $subtotal_anula_rp_proc != 0 ||
    $subtotal_mov_liquida != 0 ||
    $subtotal_mov_pagmento != 0 ||
    $subtotal_mov_pagnproc != 0 ||
    $subtotal_aliquidar_finais != 0 ||
    $subtotal_liquidados_finais != 0 ||
    $subtotal_geral_finais != 0) {
    if ($formato == "pdf") {
        $pdf->Cell(80, $tam, "Subtotal", "TBR", 0, "C", 1);
        $pdf->Cell(20, $tam, db_formatar(abs($subtotal_rp_n_proc), 'f'), 1, 0, "R", 1);
        $pdf->Cell(20, $tam, db_formatar(abs($subtotal_rp_proc), 'f'), 1, 0, "R", 1);
        $pdf->Cell(20, $tam, db_formatar(abs($subtotal_anula_rp_n_proc), 'f'), 1, 0, "R", 1);
        $pdf->Cell(20, $tam, db_formatar(abs($subtotal_anula_rp_proc), 'f'), 1, 0, "R", 1);
        $pdf->Cell(20, $tam, db_formatar(abs($subtotal_mov_liquida), 'f'), 1, 0, "R", 1);
        $pdf->Cell(20, $tam, db_formatar(abs($subtotal_mov_pagnproc), 'f'), 1, 0, "R", 1);
        $pdf->Cell(20, $tam, db_formatar(abs($subtotal_mov_pagmento), 'f'), 1, 0, "R", 1);
        $pdf->Cell(20, $tam, db_formatar(abs($subtotal_aliquidar_finais), 'f'), 1, 0, "R", 1);
        $pdf->Cell(20, $tam, db_formatar(abs($subtotal_liquidados_finais), 'f'), 1, 0, "R", 1);
        $pdf->Cell(20, $tam, db_formatar(abs($subtotal_geral_finais), 'f'), "TBL", 1, "R", 1);
    } else {
        $linhaCSV = "Subtotal";
        $linhaCSV .= ";;;".db_formatar(abs($subtotal_rp_n_proc), 'f');
        $linhaCSV .= ";".db_formatar(abs($subtotal_rp_proc), 'f');
        $linhaCSV .= ";".db_formatar(abs($subtotal_anula_rp_n_proc), 'f');
        $linhaCSV .= ";".db_formatar(abs($subtotal_anula_rp_proc), 'f');
        $linhaCSV .= ";".db_formatar(abs($subtotal_mov_liquida), 'f');
        $linhaCSV .= ";".db_formatar(abs($subtotal_mov_pagnproc), 'f');
        $linhaCSV .= ";".db_formatar(abs($subtotal_mov_pagmento), 'f');
        $linhaCSV .= ";".db_formatar(abs($subtotal_aliquidar_finais), 'f');
        $linhaCSV .= ";".db_formatar(abs($subtotal_liquidados_finais), 'f');
        $linhaCSV .= ";".db_formatar(abs($subtotal_geral_finais), 'f');

        fwrite($handle, $linhaCSV."\n");
    }
}

if ($formato == "pdf") {
    $pdf->ln(2);
    $pdf->Cell(80, $tam, "Total", "TBR", 0, "C", 1);
    $pdf->Cell(20, $tam, db_formatar(abs($total_rp_n_proc), 'f'), 1, 0, "R", 1);
    $pdf->Cell(20, $tam, db_formatar(abs($total_rp_proc), 'f'), 1, 0, "R", 1);
    $pdf->Cell(20, $tam, db_formatar(abs($total_anula_rp_n_proc), 'f'), 1, 0, "R", 1);
    $pdf->Cell(20, $tam, db_formatar(abs($total_anula_rp_proc), 'f'), 1, 0, "R", 1);
    $pdf->Cell(20, $tam, db_formatar(abs($total_mov_liquida), 'f'), 1, 0, "R", 1);
    $pdf->Cell(20, $tam, db_formatar(abs($total_mov_pagnproc), 'f'), 1, 0, "R", 1);
    $pdf->Cell(20, $tam, db_formatar(abs($total_mov_pagmento), 'f'), 1, 0, "R", 1);
    $pdf->Cell(20, $tam, db_formatar(abs($total_aliquidar_finais), 'f'), 1, 0, "R", 1);
    $pdf->Cell(20, $tam, db_formatar(abs($total_liquidados_finais), 'f'), 1, 0, "R", 1);
    $pdf->Cell(20, $tam, db_formatar(abs($total_geral_finais), 'f'), "TBL", 1, "R", 1);

    /*
     *Melhoria para imprecao dos filtros selecionados
     */
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->widths = array(200);
    //-- imprime parametros
    if (isset($imprimefiltros) && ($imprimefiltros == 'sim') && !empty($sParametros)) {
        $pdf->AddPage('L');
        $pdf->SetFont("Arial", "", 6);
        $pdf->Ln(10);
        $resto = $pdf->multicell(270, $tam, $sParametros, 1);
    }

    $pdf->Ln(17);
    assinaturas($pdf, $oClassinatura, 'LRF', true, false);

    $pdf->output('I');
} else {
    $linhaCSV = "Total";
    $linhaCSV .= ";;;".db_formatar(abs($total_rp_n_proc), 'f');
    $linhaCSV .= ";".db_formatar(abs($total_rp_proc), 'f');
    $linhaCSV .= ";".db_formatar(abs($total_anula_rp_n_proc), 'f');
    $linhaCSV .= ";".db_formatar(abs($total_anula_rp_proc), 'f');
    $linhaCSV .= ";".db_formatar(abs($total_mov_liquida), 'f');
    $linhaCSV .= ";".db_formatar(abs($total_mov_pagnproc), 'f');
    $linhaCSV .= ";".db_formatar(abs($total_mov_pagmento), 'f');
    $linhaCSV .= ";".db_formatar(abs($total_aliquidar_finais), 'f');
    $linhaCSV .= ";".db_formatar(abs($total_liquidados_finais), 'f');
    $linhaCSV .= ";".db_formatar(abs($total_geral_finais), 'f');
    fwrite($handle, $linhaCSV."\n");

    if (isset($imprimefiltros) && ($imprimefiltros == 'sim') && !empty($sParametros)) {
        $linhaCSV = "\n{$sParametros}";
        fwrite($handle, $linhaCSV."\n");
    }

    fclose($handle);

    echo "<script>
            window.opener.downloadArquivo('{$caminhoArquivo}', '{$nomeArquivo}');
            self.close();
          </script>";
}

function retorna_desdob($elemento, $e64_codele)
{
    $where = "o56_codele = $e64_codele and o56_elemento like '$elemento%'";

    $clorcelemento = new cl_orcelemento();
    $sql = $clorcelemento->sql_query_file(
        null,
        null,
        'o56_elemento AS estrutural, o56_descr AS descr',
        null,
        $where
        );

    return db_query($sql);
}

function getElementoSintetico($elemento, $anuUsu)
{
    $sql = "SELECT *
              FROM conplanoorcamento
             WHERE c60_estrut ilike '{$elemento}%'
               AND c60_anousu = {$anuUsu};";
    $rs =  db_query($sql);
    return  pg_fetch_object($rs)->c60_descr;
}

function cabecalho($pdf = null, $troca = 0)
{
    $larguraVariavel = 0;

    if(isValenca()){
        $larguraVariavel = 5;
        $fontParam = 6;
    }
    if (empty($pdf)) {
        return;
    }

    if ($pdf->getY() > $pdf->getH() - 35 || $troca != 0) {
        $tam = "10";
        $tam2 = "5";
        $pdf->addpage("L");
        $pdf->SetFont('Arial', 'B', $fontParam);
        $pdf->Cell(80, $tam, "Dados cadastrais dos empenhos", 1, 0, "C", 1);
        $pdf->Cell(40, $tam, "Saldos a pagar anteriores", 1, 0, "C", 1);
        $alturacabecalho = $pdf->gety();
        $distanciacabecalho = $pdf->getx();
        $pdf->Cell(100, $tam2, "Movimentação dos restos a pagar no período", 1, 1, "C", 1);
        $pdf->setxy($distanciacabecalho, $alturacabecalho + 5);
        $pdf->Cell(40, $tam2, "Anulação", 1, 0, "C", 1);
        $alturacabecalho2 = $pdf->gety();
        $distanciacabecalho2 = $pdf->getx();
        $pdf->Cell(20, $tam, "Liquidação", 1, "TLR", "C", 1);
        $pdf->setxy($distanciacabecalho2 + 20, $alturacabecalho2);
        $pdf->Cell(40, $tam2, "Pagamento", 1, "TLR", "C", 1);
        $pdf->setxy($distanciacabecalho + 100, $alturacabecalho);
        $pdf->Cell(60, $tam, "Saldo a pagar finais", 1, 1, "C", 1);



        if(isValenca()){

            $pdf->Cell(3*$larguraVariavel, $tam2, "PA", 1, 0, "C", 1);
        }


        $pdf->Cell(15, $tam2, "Empenho", 1, 0, "C", 1);
        $pdf->Cell(15, $tam2, " Emissão", 1, 0, "C", 1);
        $pdf->Cell(50-(3*$larguraVariavel), $tam2, "Credor", 1, 0, "C", 1);

        $pdf->Cell(20, $tam2, "RP não proc", 1, 0, "C", 1);
        $pdf->Cell(20, $tam2, "RP proc", 1, 0, "C", 1);

        $pdf->Cell(20, $tam2, "RP não proc", 1, 0, "C", 1);
        $pdf->Cell(20, $tam2, "RP proc", 1, 0, "C", 1);
        $pdf->setx($pdf->getx() + 20);
        $pdf->Cell(20, $tam2, "RP Não proc", 1, 0, "C", 1);
        $pdf->Cell(20, $tam2, "RP proc", 1, 0, "C", 1);

        $pdf->Cell(20, $tam2, "A liquidar ", 1, 0, "C", 1);
        $pdf->Cell(20, $tam2, "Liquidados ", 1, 0, "C", 1);
        $pdf->Cell(20, $tam2, "Geral ", 1, 1, "C", 1);

        $pdf->SetFont('Arial', '', 7);
        $troca = 0;
        $iYlinha = $pdf->getY();
    }
}

function exibirSubtotal($oPdf, $oSubtotalizador, $formato = "pdf", $handle = null)
{
    if ($formato == "pdf") {
        $oPdf->Cell(80, 5, "Subtotal", "TBR", 0, "C", 1);
        $oPdf->Cell(20, 5, db_formatar(abs($oSubtotalizador->rp_n_proc), 'f'), 1, 0, "R", 1);
        $oPdf->Cell(20, 5, db_formatar(abs($oSubtotalizador->rp_proc), 'f'), 1, 0, "R", 1);
        $oPdf->Cell(20, 5, db_formatar(abs($oSubtotalizador->anula_rp_n_proc), 'f'), 1, 0, "R", 1);
        $oPdf->Cell(20, 5, db_formatar(abs($oSubtotalizador->anula_rp_proc), 'f'), 1, 0, "R", 1);
        $oPdf->Cell(20, 5, db_formatar(abs($oSubtotalizador->mov_liquida), 'f'), 1, 0, "R", 1);
        $oPdf->Cell(20, 5, db_formatar(abs($oSubtotalizador->mov_pagnproc), 'f'), 1, 0, "R", 1);
        $oPdf->Cell(20, 5, db_formatar(abs($oSubtotalizador->mov_pagmento), 'f'), 1, 0, "R", 1);
        $oPdf->Cell(20, 5, db_formatar(abs($oSubtotalizador->aliquidar_finais), 'f'), 1, 0, "R", 1);
        $oPdf->Cell(20, 5, db_formatar(abs($oSubtotalizador->liquidados_finais), 'f'), 1, 0, "R", 1);
        $oPdf->Cell(20, 5, db_formatar(abs($oSubtotalizador->geral_finais), 'f'), "TBL", 1, "R", 1);
    } else {
        $linhaCSV = "Subtotal";
        $linhaCSV .= ";;";
        $linhaCSV .= ";".db_formatar(abs($oSubtotalizador->rp_n_proc), 'f');
        $linhaCSV .= ";".db_formatar(abs($oSubtotalizador->rp_proc), 'f');
        $linhaCSV .= ";".db_formatar(abs($oSubtotalizador->anula_rp_n_proc), 'f');
        $linhaCSV .= ";".db_formatar(abs($oSubtotalizador->anula_rp_proc), 'f');
        $linhaCSV .= ";".db_formatar(abs($oSubtotalizador->mov_liquida), 'f');
        $linhaCSV .= ";".db_formatar(abs($oSubtotalizador->mov_pagnproc), 'f');
        $linhaCSV .= ";".db_formatar(abs($oSubtotalizador->mov_pagmento), 'f');
        $linhaCSV .= ";".db_formatar(abs($oSubtotalizador->aliquidar_finais), 'f');
        $linhaCSV .= ";".db_formatar(abs($oSubtotalizador->liquidados_finais), 'f');
        $linhaCSV .= ";".db_formatar(abs($oSubtotalizador->geral_finais), 'f');
        fwrite($handle, $linhaCSV."\n");
    }
}
