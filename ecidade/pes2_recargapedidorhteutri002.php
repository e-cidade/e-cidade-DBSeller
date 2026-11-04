<?php

/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidadedbseller.com.br
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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_rhteutri_classe.php"));
require_once(modification("classes/db_db_config_classe.php"));

$clrhteutri = new cl_rhteutri;
$cldbconfig = new cl_db_config;
$clrotulo   = new rotulocampo;

db_postmemory($_POST);

$rh67_rhtipovale = isset($_POST['rh67_rhtipovale']) ? $_POST['rh67_rhtipovale'] : '';
$rh68_descr      = $_POST['rh68_descr'];
$tipo_emissao    = $_POST['tipo_emissao'];
$ordem           = $_POST['ordem'];
$gera            = $_POST['gera'];

$iAno = DBPessoal::getAnoFolha();
$iMes = DBPessoal::getMesFolha();

if (isset($gera)) {
    $lEmitePorValor = ($tipo_emissao == "valor" ? true : false);

    if ($ordem == 'a') {
        $xordem = 'z01_nome';
    } else {
        $xordem = 'rh67_regist';
    }

    $where = " rh67_rhtipovale = $rh67_rhtipovale ";

    if ($tipo == 'a') {
        $where .= " and rh67_ativo = 't'";
    } elseif ($tipo == 'i') {
        $where .= " and rh67_ativo = 'f'";
    }

    $where .= " and rh67_anousu = $iAno ";
    $where .= " and rh67_mesusu = $iMes ";
    $where .= " and rh05_recis is null";
    $where .= " and rh02_instit = " . db_getsession('DB_instit') . " ";

    $instituicao = db_getsession('DB_instit');
    $sql = $cldbconfig->sql_query(null, "cgc", null, "codigo = {$instituicao}");
    $resultCnpj = pg_query($conn, $sql);

    if (!$resultCnpj) {
        throw new Exception("Erro na consulta: " . pg_last_error());
    }

    if (pg_num_rows($resultCnpj) > 0) {
        $row = pg_fetch_assoc($resultCnpj);
        $cgc = $row['cgc'];
    } else {
        echo "Nenhum registro encontrado.";
    }

    $result = $clrhteutri->sql_record($clrhteutri->sql_query(null,
                                                        "rh67_regist,
                                                        z01_nome,
                                                        z01_cgccpf",
                                                        $xordem,
                                                        $where
    ));

    $totalRegistros = pg_num_rows($result);

    $constante = "PEDIDO";
    $versao    = "0100";
    $cnpj      = $cgc;
    $dataAtual = date("Ymd");
    $horaAtual = date("Hi");

    $arq = "tmp/{$constante}_{$versao}_{$cnpj}_{$dataAtual}_{$horaAtual}.txt";

    $arquivo = fopen($arq, 'w');

    if ($rh67_rhtipovale == 1) {
        for ($x = 0; $x < $totalRegistros; $x++) {
            $row = pg_fetch_assoc($result, $x);

            $rh67_regist = $row['rh67_regist'];
            $z01_nome   = $row['z01_nome'];
            $z01_cgccpf = $row['z01_cgccpf'];

            $iContador = 1;

            //Cabecalho
            $sHeader  = str_pad($iContador, 5, '0', STR_PAD_LEFT);         // Nr_seq_reg
            $sHeader .= '01';                                              // Tp_registro
            $sHeader .= 'PEDIDO';                                          // Nm_arquivo
            $sHeader .= '01.00';                                           // Nr_versão
            $sHeader .= db_formatar($cgc, 's', '0', 14, 'e', 0) . PHP_EOL; // Nr_doc_comprd (CPF/CNPJ/CEI)

            fputs($arquivo, $sHeader);

            $oServidor = ServidorRepository::getInstanciaByCodigo(trim($rh67_regist));

            $F032 = DBPessoal::getVariaveisCalculo($oServidor, 'f032');
            $iDiasUteis = (int)$F032;

            for ($x = 0; $x < $totalRegistros; $x++) {
                db_fieldsmemory($result, $x);

                $iAno = DBPessoal::getAnoFolha();
                $iMes = DBPessoal::getMesFolha();

                $sSql  = "SELECT ROUND(SUM(valor), 2) AS valor ";
                $sSql .= "  FROM ( ";
                $sSql .= "       SELECT r16_valor * r63_quant AS valor ";
                $sSql .= "       FROM vtfempr ";
                $sSql .= "       INNER JOIN vtffunc ON vtffunc.r17_codigo = vtfempr.r16_codigo ";
                $sSql .= "                         AND vtffunc.r17_anousu = vtfempr.r16_anousu ";
                $sSql .= "                         AND vtffunc.r17_mesusu = vtfempr.r16_mesusu ";
                $sSql .= "       INNER JOIN vtfdias ON vtfdias.r63_vale   = vtfempr.r16_codigo ";
                $sSql .= "                         AND vtfdias.r63_anousu = vtfempr.r16_anousu ";
                $sSql .= "                         AND vtfdias.r63_mesusu = vtfempr.r16_mesusu "; 
                $sSql .= "       INNER JOIN rhteutri ON rhteutri.rh67_anousu = vtffunc.r17_anousu ";
                $sSql .= "                         AND rhteutri.rh67_mesusu = vtffunc.r17_mesusu ";
                $sSql .= "                         AND rhteutri.rh67_regist = vtffunc.r17_regist ";
                $sSql .= "       INNER JOIN rhpessoalmov ON rhpessoalmov.rh02_anousu = vtffunc.r17_anousu ";
                $sSql .= "                         AND rhpessoalmov.rh02_mesusu = vtffunc.r17_mesusu "; 
                $sSql .= "                         AND rhpessoalmov.rh02_regist = vtffunc.r17_regist ";
                $sSql .= "       LEFT JOIN rhpesrescisao ON rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes ";
                $sSql .= "       WHERE r16_anousu = $iAno ";
                $sSql .= "         AND r16_mesusu = $iMes ";
                $sSql .= "         AND r17_regist = {$rh67_regist} ";
                $sSql .= "         AND r63_regist = {$rh67_regist} ";
                $sSql .= "         AND r16_empres = '1' ";
                $sSql .= "         AND rh05_recis is null";
                $sSql .= "         AND r16_instit = " . db_getsession('DB_instit') . " ";
                $sSql .= "   GROUP BY r16_codigo, r16_valor, r63_quant ";               
                $sSql .= "  ) AS subquery ";

                $rsValorVtf = db_query($sSql);

                if (!$rsValorVtf || pg_num_rows($rsValorVtf) == 0) {
                    continue;
                }

                $sSqlDiasAfastados = "select * from fc_valetransporte_dias_afasta_vr({$rh67_regist}, $iAno, $iMes)";

                $rsDiasAfastados = db_query($sSqlDiasAfastados);

                $oCalculoAfastamento = db_utils::fieldsMemory($rsDiasAfastados, 0);

                $iDiasAfastados = $oCalculoAfastamento->ridiasmes + $oCalculoAfastamento->ridiasmesant;

                $dataInicioFerias = $oCalculoAfastamento->ridtafas;
                $dataFimFerias = $oCalculoAfastamento->ridtreto;

                $anoAtual = $iAno;
                $mesAtual = $iMes;

                if ($iMes == 12) {
                    $iAno = $iAno + 1;
                    $iMes = 1;
                } else {
                    $iMes = $iMes + 1;
                }

                $dataInicioMes = "{$iAno}-" . str_pad($iMes, 2, '0', STR_PAD_LEFT) . "-01";
                $dataFimMes = "{$iAno}-" . str_pad($iMes, 2, '0', STR_PAD_LEFT) . "-" . (new DateTime("$iAno-$iMes-01"))->modify('last day of this month')->format('d');

                $diasATrabalhar = 0;

                if ($dataInicioFerias && $dataFimFerias) {
                    if ($dataInicioMes <= $dataInicioFerias) {

                        $sSqlDiasUteisAntesFerias =
                            "SELECT COUNT(*) AS dias_uteis
                                FROM (
                                    SELECT generate_series('$dataInicioMes'::date, '$dataInicioFerias'::date - interval '1 day', '1 day'::interval) AS data_mes
                                ) AS datas
                                WHERE
                                    EXTRACT(DOW FROM data_mes) NOT IN (0, 6) 
                                    AND data_mes NOT IN (                    
                                        SELECT r62_data 
                                            FROM calendf 
                                        WHERE r62_calend = (
                                            SELECT rh53_calend 
                                                FROM rhcadcalend 
                                                    INNER JOIN rhlotacalend ON rh64_calend = rh53_calend 
                                            WHERE rh53_instit = " . db_getsession('DB_instit') . "
                                    )
                                )";

                        $rsDiasUteisAntesFerias = db_query($sSqlDiasUteisAntesFerias);
                        $diasATrabalhar = (int) db_utils::fieldsMemory($rsDiasUteisAntesFerias, 0)->dias_uteis;

                    } elseif ($dataFimFerias <= $dataFimMes) {
                        $dataInicioAposFerias = date('Y-m-d', strtotime($dataFimFerias . ' +1 day'));
                        $dataFimMes = "{$iAno}-" . str_pad($iMes, 2, '0', STR_PAD_LEFT) . "-" . date('t', strtotime($dataInicioAposFerias));

                        $sSqlDiasUteisAposFerias =
                            "SELECT COUNT(*) AS dias_uteis
                                    FROM (
                                        SELECT generate_series('$dataInicioAposFerias'::date, '$dataFimMes'::date, '1 day'::interval) AS datas_mes
                                        ) AS datas
                                WHERE
                                EXTRACT(DOW FROM datas_mes) NOT IN (0, 6)
                                AND datas_mes NOT IN (
                                    SELECT r62_data 
                                        FROM calendf 
                                    WHERE r62_calend = (
                                        SELECT rh53_calend 
                                            FROM rhcadcalend 
                                                INNER JOIN rhlotacalend ON rh64_calend = rh53_calend 
                                        WHERE rh53_instit = " . db_getsession('DB_instit') .
                            ")
                            )";

                        $rsDiasUteisAposFerias = db_query($sSqlDiasUteisAposFerias);
                        $diasATrabalhar = (int) db_utils::fieldsMemory($rsDiasUteisAposFerias, 0)->dias_uteis;
                    }
                } else {
                    $diasATrabalhar = $iDiasUteis;
                }

                $diasUteisTrabalhados = $diasATrabalhar - $iDiasAfastados;
                $nValor = str_replace(".", "", db_utils::fieldsMemory($rsValorVtf, 0)->valor);
                $nValor *= $diasUteisTrabalhados;

                if ($nValor <= 0) {
                    $nValor = 0;
                    continue;
                }

                $totalNValor += $nValor;
                $iContador++;

                $sLinha  = str_pad($iContador, 5, '0', STR_PAD_LEFT);
                $sLinha .= db_formatar('02', 's', ' ', 2, 'd', 0);
                $sLinha .= db_formatar($rh67_regist, 's', ' ', 15, 'd', 0);
                $sLinha .= str_pad($nValor, 8, "0", STR_PAD_LEFT) . PHP_EOL;

                fputs($arquivo, $sLinha);

                $sSqlUpdate  = "UPDATE rhteutri SET rh67_dias = {$diasUteisTrabalhados}, ";
                $sSqlUpdate .= "rh67_valor = {$nValor} ";
                $sSqlUpdate .= "WHERE rh67_regist = {$rh67_regist} ";
                $sSqlUpdate .= "AND rh67_anousu = {$anoAtual} ";
                $sSqlUpdate .= "AND rh67_mesusu = {$mesAtual} ";
                $sSqlUpdate .= "AND rh67_rhtipovale = 1 ";

                if (!db_query($sSqlUpdate)) {
                    throw new Exception("Erro ao atualizar dados na tabela rhteutri: " . pg_last_error());
                }

            }
            $sTrailer = str_pad($iContador, 5, '0', STR_PAD_LEFT);
            $sTrailer .= "99";
            $sTrailer .= str_pad($totalNValor, 10, '0', STR_PAD_LEFT) . PHP_EOL;
            fputs($arquivo, $sTrailer);
        }
    }
    fclose($arquivo);
}

$oRetorno = (object)array(
    'erro' => false,
    'mensagem' => '',
    'arquivo' => $arq
);

echo JSON::create()->stringify($oRetorno);
