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

use App\Domain\RecursosHumanos\RH\Relatorios\Services\ConfigAssentPeriodoService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");

require_once(modification("libs/db_sql.php"));

parse_str($_SERVER['QUERY_STRING'], $query);

/**
 * Configuracao do relatorio
 *
 * - filtro adicionais (Selecao, Lotacao...)
 * - filtro assentamentos do departamento (Apenas assentamento do departamento)
 */
$configAssentPeriodoService = new ConfigAssentPeriodoService;
$configAssentPeriodo = $configAssentPeriodoService->getByInstit(db_getsession("DB_instit"));

$filtrosadicionais = false;
$assentferias      = false;

if ($configAssentPeriodo) {
    if ($configAssentPeriodo->rh512_filtroadicionais) {
        $filtrosadicionais = true;
    }

    if ($configAssentPeriodo->rh512_assentferias) {
        $assentferias = $configAssentPeriodo->rh512_assentferias;
    }
}

// verifica se possui campo dinanmico ano relativo de ferias
$sqlFeriasSnoRelativo = "
    select db109_sequencial from db_cadattdinamicoatributos
    where db109_descricao ilike 'Relativas ao Ano de'
";
$rsFeriasAnoRelativo  = db_query($sqlFeriasSnoRelativo);
$anoFeriasRelativo    = (pg_num_rows($rsFeriasAnoRelativo) > 0) ? true : false;

$where = 'where rh05_recis is null';
if ($regist != '') {
    $where .= " and rh01_regist = $regist";
}

$sql_ano = "select fc_anofolha(" . db_getsession('DB_instit') . ") as anousu, fc_mesfolha(" . db_getsession('DB_instit') . ") as mesusu";
$res_ano = db_query($sql_ano);
db_fieldsmemory($res_ano, 0);

if ($perinicial != '--') {
    if ($perfinal == '--') {
        $final = date("Y-m-d");
    } else {
        $final = $perfinal;
    }

    $where .= " and h16_dtconc between '{$perinicial}' and '{$final}'";
}

if ($tipos != '') {
    $where .= " and h12_codigo in ($tipos)";
}

$join = "
    left join assentamentofuncional on rh193_assentamento_funcional = h16_codigo
    inner join rhpessoal on h16_regist  = rh01_regist
    inner join rhpessoalmov  on rh02_regist  = rh01_regist
        and rh02_anousu  = $anousu
        and rh02_mesusu  = $mesusu
        and rh02_instit  = " . db_getsession('DB_instit') . "
    inner join rhfuncao  on rh37_funcao  = rh02_funcao
        and rh37_instit = " . db_getsession('DB_instit') . "
    inner join cgm on rh01_numcgm = z01_numcgm
    inner join tipoasse on h16_assent = h12_codigo
    left join assentadb_cadattdinamicovalorgrupo on h80_assenta = h16_codigo
    left join rhpesrescisao on rh05_seqpes = rh02_seqpes
";

$xordem = 'order by ';

if ($filtrosadicionais) {
    /* Busca Seleção */
    $whereSelecao = '';
    if (!empty($selecao)) {
        $oDaoSelecao   = new cl_selecao();
        $whereSelecao = $oDaoSelecao->getCondicaoSelecao($selecao);
    }

    /** Caso seja selecionada selação sera o unico criterio do where */
    if (!empty($whereSelecao)) {
        $where .= ' and ' . $whereSelecao;
    }

    switch ($tipo) {
        case 't':
            $join .= "
                inner join rhpeslocaltrab on rhpeslocaltrab.rh56_seqpes = rhpessoalmov.rh02_seqpes
                inner join rhlocaltrab  on rhlocaltrab.rh55_codigo  = rhpeslocaltrab.rh56_localtrab
                and rhlocaltrab.rh55_instit  = rhpessoalmov.rh02_instit
            ";

            if (isset($loc1) && trim($loc1) != "" && isset($loc2) && trim($loc2) != "") {
                // Se for por intervalos e vier local de trabalho inicial e final
                $where .= " and rh55_estrut between '" . $loc1 . "' and '" . $loc2 . "' ";
            } else if (isset($loc1) && trim($loc1) != "") {
                // Se for por intervalos e vier somente local de trabalho inicial
                $where .= " and rh55_estrut >= '" . $loc1 . "' ";
            } else if (isset($loc2) && trim($loc2) != "") {
                // Se for por intervalos e vier somente local de trabalho final
                $where .= " and rh55_estrut <= '" . $loc2 . "' ";
            } else if (isset($loc) && trim($loc) != "") {
                // Se for por selecionados
                $where .= " and rh55_estrut in ('" . str_replace(",", "','", $loc) . "') ";
            }

            if ($ordemresumo == 's') {
                $xordem .= " rh55_estrut, ";
            }
            break;

        case 'm':
            if (isset($reg1) && trim($reg1) != "" && isset($reg2) && trim($reg2) != "") {
                // Se for por intervalos e vier Matricula inicial e final
                $where .= " and rh01_regist between '" . $reg1 . "' and '" . $reg2 . "' ";
            } else if (isset($reg1) && trim($reg1) != "") {
                // Se for por intervalos e vier somente Matricula inicial
                $where .= " and rh01_regist >= '" . $reg1 . "' ";
            } else if (isset($reg2) && trim($reg2) != "") {
                // Se for por intervalos e vier somente Matricula final
                $where .= " and rh01_regist <= '" . $reg2 . "' ";
            } else if (isset($reg) && trim($reg) != "") {
                // Se for por selecionados
                $where .= " and rh01_regist in ('" . str_replace(",", "','", $reg) . "') ";
            }

            if ($ordemresumo == 's') {
                $xordem .= " rh01_regist, ";
            }
            break;

        case 'c':
            if (isset($cai) && trim($cai) != "" && isset($caf) && trim($caf) != "") {
                // Se for por intervalos e vier lotação inicial e final
                $where     .= " and rh37_funcao between '" . $cai . "' and '" . $caf . "' ";
            } else if (isset($cai) && trim($cai) != "") {
                // Se for por intervalos e vier somente lotação inicial
                $where    .= " and rh37_funcao >= '" . $cai . "' ";
            } else if (isset($caf) && trim($caf) != "") {
                // Se for por intervalos e vier somente lotação final
                $where    .= " and rh37_funcao <= '" . $caf . "' ";
            } else if (isset($fca) && trim($fca) != "") {
                // Se for por selecionados
                $where  .= " and rh37_funcao in ('" . str_replace(",", "','", $fca) . "') ";
            }

            if ($ordemresumo == 's') {
                $xordem .= " rh37_funcao, ";
            }
            break;

        case 'l':
            $join .= " left join rhlota on rhlota.r70_codigo = rhpessoalmov.rh02_lota";

            if (isset($lti) && trim($lti) != "" && isset($ltf) && trim($ltf) != "") {
                // Se for por intervalos e vier local inicial e final
                $where .= " and r70_estrut between '" . $lti . "' and '" . $ltf . "' ";
            } else if (isset($lti) && trim($lti) != "") {
                // Se for por intervalos e vier somente local inicial
                $where .= " and r70_estrut >= '" . $lti . "' ";
            } else if (isset($ltf) && trim($ltf) != "") {
                // Se for por intervalos e vier somente local final
                $where .= " and r70_estrut <= '" . $ltf . "' ";
            } else if (isset($flt) && trim($flt) != "") {
                // Se for por selecionados
                $where .= " and r70_estrut in ('" . str_replace(",", "','", $flt) . "') ";
            }

            if ($ordemresumo == 's') {
                $xordem .= " r70_estrut, ";
            }
            break;

        default:
            $join .= "
                left join rhlota on rhlota.r70_codigo = rhpessoalmov.rh02_lota
                left join rhlotaexe on rhlotaexe.rh26_codigo = rhlota.r70_codigo
                    and rhlotaexe.rh26_anousu = rhpessoalmov.rh02_anousu
                left join orcorgao on orcorgao.o40_orgao = rhlotaexe.rh26_orgao
                    and orcorgao.o40_anousu = rhpessoalmov.rh02_anousu
                    and orcorgao.o40_instit = rhpessoalmov.rh02_instit
            ";

            if (isset($ori) && trim($ori) != "" && isset($orf) && trim($orf) != "") {
                // Se for por intervalos e vier órgão inicial e final
                $where    .= " and o40_orgao between " . $ori . " and " . $orf;
            } else if (isset($ori) && trim($ori) != "") {
                // Se for por intervalos e vier somente órgão inicial
                $where .= " and o40_orgao >= " . $ori;
            } else if (isset($orf) && trim($orf) != "") {
                // Se for por intervalos e vier somente órgão final
                $where    .= " and o40_orgao <= " . $orf;
            } else if (isset($for) && trim($for) != "") {
                // Se for por selecionados
                $where  .= " and o40_orgao in (" . $for . ") ";
            }

            if ($ordemresumo == 's') {
                $xordem .= " o40_orgao, ";
            }
            break;
    }
}

switch ($ordem) {
    case 'a':
        $xordem .= " z01_nome, rh37_descr, h12_descr, h16_dtconc ";
        break;

    case 'n':
        $xordem .= " h16_regist, h16_dtconc ";
        break;

    case 'c':
        $xordem .= " rh37_descr, z01_nome, h16_dtconc ";
        break;

    case 'd':
        $xordem .= " h16_dtconc ";
        break;
}

if (!$filtrosadicionais) {
    $where .= "
        and h16_regist in (select distinct rh02_regist from rhpessoalmov
        where rh02_anousu = " . DBPessoal::getAnoFolha() . "
        and rh02_mesusu = " . DBPessoal::getMesFolha() . "
        and rh02_lota in (select distinct rh157_lotacao
        from db_usuariosrhlota
        where rh157_usuario = " . db_getsession("DB_id_usuario") . "))
    ";
}

$sql = "select h16_regist,";
if ($filtrosadicionais) {
    $sql .= "SUBSTR(z01_nome,0,33) as z01_nome, SUBSTR(h12_descr,0,30) as h12_descr,";

    $tipos_array = explode(",", $tipos);
    $tipo_ferias = false;

    if (is_array($assentferias) && $assentferias) {
        $tipo_ferias = array_intersect($assentferias, $tipos_array);
        $tipo_ferias = count($tipo_ferias) > 0 ? true : false;
    }

    if ($anoFeriasRelativo && $tipo_ferias) {
        $sql .= "
        (SELECT db110_valor
        FROM db_cadattdinamicovalorgrupo
        LEFT JOIN db_cadattdinamicoatributosvalor ON db_cadattdinamicoatributosvalor.db110_cadattdinamicovalorgrupo = db_cadattdinamicovalorgrupo.db120_sequencial
        LEFT JOIN db_cadattdinamicoatributos ON db_cadattdinamicoatributos.db109_sequencial = db_cadattdinamicoatributosvalor.db110_db_cadattdinamicoatributos
        WHERE
        db120_sequencial = h80_db_cadattdinamicovalorgrupo
        AND db109_descricao ilike 'Relativas ao Ano de'
        AND db110_valor <> '') AS ano_relativo,
    ";
    } else {
        $sql .= " z01_cgccpf,";
    }
} else {
    $sql .= " z01_nome, h12_descr,";
}

$sql .= "
    h12_assent,
    h16_dtconc,
    h16_dtterm,
    h16_quant,
    h16_histor,
    h16_hist2,
    h16_dtlanc,
    rh37_descr
    from assenta
    $join
    $where
    $xordem
";

// kill_sql($sql);

$result = db_query($sql);
//db_criatabela($result);exit;
$xxnum = pg_num_rows($result);
if ($xxnum == 0) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Não existem assentamentos cadastrados para o período informado');
}

/**
 * Exportação em csv
 */
if (isset($export) && $export == 'csv') {
    $servidores   = db_utils::getCollectionByRecord($result);
    $ano_relativo = ($filtrosadicionais && $tipo_ferias && $anoFeriasRelativo) ? true : false;
    return exportCsv($servidores, $ano_relativo);
}

/**
 * Exportação em Xlsx (Excel)
 */
if (isset($export) && $export == 'xlsx') {
    $servidores   = db_utils::getCollectionByRecord($result);
    $ano_relativo = ($filtrosadicionais && $tipo_ferias && $anoFeriasRelativo) ? true : false;
    return exportXlsx($servidores, $ano_relativo);
}


/**
 * Exportacao em PDF
 */
$pdf = new ECidade\Pdf\Pdf();
$pdf->init(false);
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$pdf->setfont('arial', 'b', 8);
$troca      = 1;
$alt        = 4;
$total      = 0;
$total_dias = 0;

$head3 = "RELATORIO DE ASSENTAMENTOS";
$head5 = "PERIODO : " . db_formatar($perinicial, 'd') . " a " . db_formatar($final, 'd');

$pdf->addTitulo($head3);
$pdf->addTitulo($head5);

for ($x = 0; $x < pg_num_rows($result); $x++) {
    db_fieldsmemory($result, $x);

    if ($pdf->gety() > $pdf->getH() - 30 || $troca != 0) {
        $pdf->addpage('L');
        $pdf->setfont('arial', 'b', 8);
        $pdf->cell(15, $alt, 'MATRIC.', 1, 0, "C", 1);

        if ($filtrosadicionais) {
            $pdf->cell(50, $alt, 'NOME', 1, 0, "C", 1);

            if ($anoFeriasRelativo && $tipo_ferias) {
                $pdf->cell(30, $alt, 'RELATIVOS AO ANO', 1, 0, "C", 1);
            } else {
                $pdf->cell(30, $alt, 'CPF', 1, 0, "C", 1);
            }

            $pdf->cell(50, $alt, 'CARGO', 1, 0, "C", 1);
            $pdf->cell(50, $alt, 'ASSENTAMENTO', 1, 0, "C", 1);
        } else {
            $pdf->cell(60, $alt, 'NOME', 1, 0, "C", 1);
            $pdf->cell(60, $alt, 'CARGO', 1, 0, "C", 1);
            $pdf->cell(60, $alt, 'ASSENTAMENTO', 1, 0, "C", 1);
        }

        $pdf->cell(20, $alt, 'INICIAL', 1, 0, "C", 1);
        $pdf->cell(20, $alt, 'FINAL', 1, 0, "C", 1);
        $pdf->cell(24, $alt, 'LANCAMENTO', 1, 0, "C", 1);
        $pdf->cell(15, $alt, 'DIAS', 1, 1, "C", 1);

        if ($descr == 's') {
            $pdf->cell(250, $alt, 'OBSERVACAO', 1, 1, "C", 1);
        }

        $troca = 0;
        $pre = 1;
    }

    $pre = ($pre == 1) ? 0 : 1;

    $pdf->setfont('arial', '', 7);
    $pdf->cell(15, $alt, $h16_regist, 0, 0, "C", $pre);

    if ($filtrosadicionais) {
        $pdf->cell(50, $alt, $z01_nome, 0, 0, "L", $pre);

        if ($anoFeriasRelativo && $tipo_ferias) {
            $pdf->cell(30, $alt, $ano_relativo, 0, 0, "L", $pre);
        } else {
            $pdf->cell(30, $alt, $z01_cgccpf, 0, 0, "L", $pre);
        }

        $pdf->cell(50, $alt, $rh37_descr, 0, 0, "L", $pre);
        $pdf->cell(50, $alt, $h12_assent . '-' . $h12_descr, 0, 0, "L", $pre);
    } else {
        $pdf->cell(60, $alt, $z01_nome, 0, 0, "L", $pre);
        $pdf->cell(60, $alt, $rh37_descr, 0, 0, "L", $pre);
        $pdf->cell(60, $alt, $h12_assent . '-' . $h12_descr, 0, 0, "L", $pre);
    }

    $pdf->cell(20, $alt, db_formatar($h16_dtconc, 'd'), 0, 0, "C", $pre);
    $pdf->cell(20, $alt, db_formatar($h16_dtterm, 'd'), 0, 0, "C", $pre);
    $pdf->cell(24, $alt, db_formatar($h16_dtlanc, 'd'), 0, 0, "C", $pre);
    $pdf->cell(15, $alt, $h16_quant, 0, 1, "C", $pre);

    if ($descr == 's') {
        $pdf->multicell(250, $alt, $h16_histor . ' ' . $h16_hist2, 0, "L", $pre);
    }

    $total      += 1;
    $total_dias += $h16_quant;
    //   $pdf->SetXY($pdf->lMargin,$pdf->gety() + $alt);
}

$pdf->setfont('arial', 'b', 8);
$pdf->cell(239, $alt, 'TOTAL DE LANCAMENTOS :  ' . $total, "T", 0, "C", 0);
$pdf->cell(15, $alt, $total_dias, "T", 0, "C", 0);
//$pdf->cell(20,$alt,'',"T",0,"C",0);
//$pdf->cell(30,$alt,db_formatar($total,'f'),"T",1,"R",0);

$pdf->Output();


/**
 * Cria arquivo do relatorio em formato csv
 *
 * @param object $dados Dados do relatorio
 * @param bool $anoRelativo Quando o cliente possui o campo ano relativo para ferias
 *
 * @return string json
 */
function exportCsv($dados, $anoRelativo = false)
{
    $fileName = 'tmp/assentamento_periodo' . time() . '.csv';
    $csvFile  = fopen($fileName, 'w');

    // header
    $csvHeaders = [
        'MATRIC',
        'NOME',
        'CARGO',
        'ASSENTAMENTO',
        'INICIAL',
        'FINAL',
        'LANCAMENTO',
        'DIAS'
    ];

    if ($anoRelativo) {
        $csvHeaders[] = 'ANO_RELATIVO';
    }

    fputcsv($csvFile, $csvHeaders);

    foreach ($dados as $servidor) {
        $linha = [];
        $linha['MATRIC']       = $servidor->h16_regist;
        $linha['NOME']         = $servidor->z01_nome;
        $linha['CARGO']        = $servidor->rh37_descr;
        $linha['ASSENTAMENTO'] = $servidor->h12_assent . ' - ' . $servidor->h12_descr;
        $linha['INICIAL']      = db_formatar($servidor->h16_dtconc, 'd');
        $linha['FINAL']        = db_formatar($servidor->h16_dtterm, 'd');
        $linha['LANCAMENTO']   = db_formatar($servidor->h16_dtlanc, 'd');
        $linha['DIAS']         = $servidor->h16_quant;

        if ($anoRelativo) {
            $linha['ANO_RELATIVO'] = $servidor->ano_relativo;
        }

        fputcsv($csvFile, $linha);
    }

    // retorno do arquivo de download
    fclose($csvFile);

    if (file_exists($fileName)) {
        $retorno = ['erro' => false, 'file' => $fileName];
    } else {
        $retorno = ['erro' => true, 'msg' => 'Erro ao gerar o arquivo csv'];
    }

    echo json_encode($retorno);
}

/**
 * Cria arquivo do relatorio em formato xlsx - excel
 *
 * @param object $dados Dados do relatorio
 * @param bool $anoRelativo Quando o cliente possui o campo ano relativo para ferias
 *
 * @return string json
 */
function exportXlsx($dados, $ano_relativo)
{
    // arquivo
    $fileName = 'tmp/assentamento_periodo' . time() . '.xlsx';
    $spreadsheet = new Spreadsheet();
    $activeWorksheet = $spreadsheet->getActiveSheet();
    $cells = [];

    // header
    $header = [
        'MATRIC',
        'NOME',
        'CARGO',
        'ASSENTAMENTO',
        'INICIAL',
        'FINAL',
        'LANCAMENTO',
        'DIAS'
    ];

    if ($ano_relativo) {
        $header[] = 'ANO_RELATIVO';
    }

    $cells[] = $header;

    // dados
    foreach ($dados as $servidor) {
        $cell = [
            $servidor->h16_regist,
            mb_convert_encoding($servidor->z01_nome, 'UTF-8', 'ISO 8859-1'),
            mb_convert_encoding($servidor->rh37_descr, 'UTF-8', 'ISO 8859-1'),
            $servidor->h12_assent . ' - ' . mb_convert_encoding($servidor->h12_descr, 'UTF-8', 'ISO 8859-1'),
            db_formatar($servidor->h16_dtconc, 'd'),
            db_formatar($servidor->h16_dtterm, 'd'),
            db_formatar($servidor->h16_dtlanc, 'd'),
            $servidor->h16_quant
        ];

        if ($ano_relativo) {
            $cell[] = $servidor->ano_relativo ?: '';
        }

        $cells[] = $cell;
    }

    // export
    $activeWorksheet->fromArray($cells);
    $writer = new Xlsx($spreadsheet);
    $writer->save($fileName);

    if (file_exists($fileName)) {
        $retorno = ['erro' => false, 'file' => $fileName];
    } else {
        $retorno = ['erro' => true, 'msg' => 'Erro ao gerar o arquivo excel'];
    }

    echo json_encode($retorno);
}
