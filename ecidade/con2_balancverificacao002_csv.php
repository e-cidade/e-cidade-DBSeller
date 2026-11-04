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
header('Content-type: text/html; charset=ISO-8859-2');

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta" . ".php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_libcontabilidade.php"));

parse_str($_SERVER['QUERY_STRING']);

$sArquivo = "tmp/con2_balancverificacaocsv002.csv";
$fArquivo = fopen($sArquivo, "w+");

$agrupa_estrutural = ($agrupa_estrutural == '1' ? false : true);
$anousu = db_getsession("DB_anousu");

$xinstit = explode(",", $db_selinstit);

$sSqlInstituicao = "
 select codigo, nomeinst, nomeinstabrev, (select count(*) from db_config) as total_instituicao
   from db_config
  where codigo in ({$db_selinstit})";
$resultinst = db_query($sSqlInstituicao);
$numero_instit = pg_num_rows($resultinst);

$descr_inst = '';
$xvirg = '';
$flag_abrev = false;

$totalInstituicao = 0;
if ($numero_instit > 0) {
    $totalInstituicao = db_utils::fieldsMemory($resultinst, 0)->total_instituicao;
}
if ($totalInstituicao == $numero_instit) {
    $descr_inst = "CONSOLIDAÇÃO GERAL";
} else {
    for ($xins = 0; $xins < pg_num_rows($resultinst); $xins++) {
        db_fieldsmemory($resultinst, $xins);
        if (strlen(trim($nomeinstabrev)) > 0) {
            $descr_inst .= $xvirg . "($codigo)" . $nomeinstabrev;
            $flag_abrev = true;
        } else {
            $descr_inst .= $xvirg . "($codigo)" . $nomeinst;
        }
        $xvirg = ', ';
    }
}

if ($encerramento == 's') {
    $encerramento = 'true';
} else {
    $encerramento = 'false';
}

$aTextoSaida[] = "BALANCETE DE VERIFICAÇÃO";
$aTextoSaida[] = "EXERCÍCIO " . db_getsession("DB_anousu");
$aTextoSaida[] = "PERÍODO : " . db_formatar($perini, 'd') . " A " . db_formatar($perfin, 'd');

if ($movimento == "S") {
    $xmov = "Somente com Movimento";
} else {
    $xmov = "Todas";
}

if ($tipo == "S") {
    $aTextoSaida[] = "SINTÉTICO - " . $xmov;
} else {
    $aTextoSaida[] = "ANALÍTICO - " . $xmov;
}

if ($flag_abrev == false) {
    if (strlen($descr_inst) > 42) {
        $descr_inst = substr($descr_inst, 0, 100);
    }
}

$aTextoSaida[] = "INSTITUIÇÕES : " . $descr_inst;

$where = " c61_instit in ({$db_selinstit})";

if (!empty($recurso)) {
    $where .= " and c61_codigo in ({$recurso}) ";
}

if (USE_PCASP) {
    if ($sistema_contas !== "") {
        $where .= " and c60_consistemaconta = $sistema_contas";
    }
    if ($indicador_superavit !== "") {
        $where .= " and c60_identificadorfinanceiro = '$indicador_superavit'";
    }
} else {
    if ($sistema_contas != "T") {
        $where .= " and c52_descrred = '$sistema_contas'";
    }
}

if ($estrut_inicial != '') {
    $aEstrutural = explode(",", $estrut_inicial);
    $aWhereEstrutural = array();
    foreach ($aEstrutural as $sEstrutural) {
        $sEstrutural = trim($sEstrutural);
        if (empty($sEstrutural)) {
            continue;
        }
        $aWhereEstrutural[] = " p.c60_estrut like '{$sEstrutural}%' ";
    }
    if ($aWhereEstrutural) {
        $where .= " and (" . implode(" OR ", $aWhereEstrutural) . ") ";
    }
}

$consultaBalancete = db_planocontassaldo_matriz(db_getsession("DB_anousu"), $perini, $perfin, true, $where, '', $agrupa_estrutural, $encerramento);

if ($tipo == 'S') {
    $consultaBalancete = "select * from ({$consultaBalancete})  as x where fc_nivel_plano2005(estrutural) <= 6";
}
$result = db_query($consultaBalancete);

$pagina = 1;
$maislinha = 0;
$total_anterior = 0;
$total_debitos = 0;
$total_creditos = 0;
$total_final = 0;
$iAjustePcasp = 0;

if (USE_PCASP) {
    $iAjustePcasp = 8;
}

$sLinha = "";
$sLinha .= "CODCON|";
$sLinha .= "ESTRUTURAL|";
if ($numero_instit > 1 && $agrupa_estrutural == true) {
    $sLinha .= "|";
    $sLinha .= "|";
} else {
    $sLinha .= "REDUZ|";
    $sLinha .= "INST|";
}

$sLinha .= "DESCRIÇÃO DA CONTA|";
$sLinha .= "REC|";
$sLinha .= "SIS|";

if (USE_PCASP) {
    $sLinha .= "ISF|";
}

$sLinha .= "SALDO ANTERIOR|";
$sLinha .= "DÉBITOS|";
$sLinha .= "CRÉDITOS|";
$sLinha .= "SALDO";
$aTextoSaida[] = $sLinha;

$ultimoEstrutural = '';
for ($x = 0; $x < pg_num_rows($result); $x++) {
    db_fieldsmemory($result, $x);
    if (($tipo == "S") && ($c61_reduz != 0)) {
        continue;
    }

    if (USE_PCASP) {
    } else {
        if (substr($estrutural, 0, 1) == '3') {
            if (substr($estrutural, 2) + 0 > 0) {
                continue;
            }
        }
        if (substr($estrutural, 0, 1) == '4') {
            if (substr($estrutural, 2) + 0 > 0) {
                continue;
            }
        }
    }

    if (($movimento == "S") && (($saldo_anterior + $saldo_anterior_debito + $saldo_anterior_credito) == 0)) {
        continue;
    }

    $espaco = '';
    $maislinha = 0;

    if (substr($estrutural, 1, 14) == '00000000000000') {
        $espaco = "";
        $maislinha = 1;
        if ($sinal_anterior == "C") {
            $total_anterior -= $saldo_anterior;
        } else {
            $total_anterior += $saldo_anterior;
        }

        if ($sinal_final == "C") {
            $total_final -= $saldo_final;
        } else {
            $total_final += $saldo_final;
        }

        $total_debitos += $saldo_anterior_debito;
        $total_creditos += $saldo_anterior_credito;
    } elseif (substr($estrutural, 2, 13) == '0000000000000') {
        $espaco = "  ";
        $maislinha = 1;
    } elseif (substr($estrutural, 3, 12) == '000000000000') {
        $espaco = "    ";
        $maislinha = 1;
    } elseif (substr($estrutural, 4, 11) == '00000000000') {
        $espaco = "      ";
    } elseif (substr($estrutural, 5, 10) == '0000000000') {
        $espaco = "        ";
    } elseif (substr($estrutural, 7, 8) == '00000000') {
        $espaco = "          ";
    } elseif (substr($estrutural, 9, 6) == '000000') {
        $espaco = "            ";
    } elseif (substr($estrutural, 11, 4) == '0000') {
        $espaco = "              ";
    }

    if ($maislinha == 1) {
//        $pdf->ln(1);
    }

    $resconta = db_query("select conplanoconta.*
                          from conplanoconta
                         where c63_codcon = {$c61_codcon}
                           and c63_reduz = {$c61_reduz}
                           and c63_anousu = {$anousu} ");

    if (pg_num_rows($resconta) > 0) {
        db_fieldsmemory($resconta, 0);
    }

    $estruturalFormatado = db_formatar($estrutural, 'receita');
    if ($estrutural === $ultimoEstrutural) {
        $estruturalFormatado = '';
    }

    $sLinha = "$c61_codcon|";
    $sLinha .= "$estruturalFormatado|";
    if ($numero_instit > 1 && $agrupa_estrutural == true) {
        $sLinha .= "|";
        $sLinha .= "|";
    } else {
        $sLinha .= ($c61_reduz == 0 ? '' : $c61_reduz) . "|";
        $sLinha .= ($c61_reduz == 0 ? '' : $c61_instit) . "|";
    }

    if ($conta == 'S') {
        $sLinha .= (pg_num_rows($resconta) == 0 ? $espaco . $c60_descr : $espaco . $c60_descr . '   ( Bco: ' . $c63_banco . '  Ag: ' . $c63_agencia . '  Cta: ' . $c63_conta . ')') . "|";
    } else {
        $sLinha .= ($espaco . $c60_descr) . "|";
    }

    $recurso = descricaoCompletaRecurso($c61_codigo, $anousu, 3);

    $sLinha .= ($c61_reduz == 0 ? '' : $recurso) . "|";

    if ($c61_reduz != 0) {
        $sSis = "";
        if (USE_PCASP) {
            $sSis = $sis;
        } else {
            $resconta = db_query("
            select c52_descrred from conplano
             inner join consistema on c52_codsis = c60_codsis
             where c60_anousu=$anousu and c60_estrut = '$estrutural'");
            db_fieldsmemory($resconta, 0);
            $sSis = $c52_descrred;
        }

        $sLinha .= $sSis . "|";
    } else {
        $sLinha .= "|";
    }

    if (USE_PCASP) {
        $sLinha .= $isf . "|";
    }

    $sLinha .= db_formatar($saldo_anterior, 'f') . " {$sinal_anterior}|";
    $sLinha .= db_formatar($saldo_anterior_debito, 'f') . "|";
    $sLinha .= db_formatar($saldo_anterior_credito, 'f') . "|";
    $sLinha .= db_formatar($saldo_final, 'f') . " {$sinal_final}|";

    $aTextoSaida[] = $sLinha;

    if ($c61_reduz != 0) {
        if ($sinal_final == "C") {
            $saldo_final = $saldo_final * -1;
        }
    }

    if ($c61_reduz != 0) {
        if ($sinal_anterior == "D") {
            $saldo_anterior = $saldo_anterior * -1;
        }

        if ($sinal_final == "D") {
            $saldo_final = $saldo_final * -1;
        }
    }

    $ultimoEstrutural = $estrutural;
}

if ($total_anterior < 0) {
    $sinal = "C";
} else {
    $sinal = "D";
}

$sLinha = "||||||T O T A L   G E R A L|";
$sLinha .= db_formatar(($total_anterior * -1), 'f') . " {$sinal}|";
$sLinha .= db_formatar($total_debitos, 'f') . "|";
$sLinha .= db_formatar($total_creditos, 'f') . "|";

$total_final = $total_anterior + $total_debitos - $total_creditos;

if ($total_final < 0) {
    $sinal = "C";
    $total_final *= -1;
} else {
    $sinal = "D";
}
$sLinha .= db_formatar($total_final, 'f') . " {$sinal} |";

$aTextoSaida[] = $sLinha;

foreach ($aTextoSaida as $sLinhaSaida) {
    fputs($fArquivo, $sLinhaSaida . "\n");
}
fclose($fArquivo);

db_redireciona($sArquivo);
