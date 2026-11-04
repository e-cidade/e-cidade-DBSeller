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
$matriculas      = isset($_POST['matriculas']) ? $_POST['matriculas'] : '';
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
      $xordem = 'rh01_regist';
    }

    $where = " rh67_rhtipovale = $rh67_rhtipovale ";

    if (!empty($matriculas)) {
        $where .= " AND rh01_regist IN ($matriculas)";
    }

    $where .= " and rh67_anousu = $iAno ";
    $where .= " and rh67_mesusu = $iMes ";

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

    $constante = "CADUSU";
    $versao = "0402";
    $cnpj = $cgc;
    $dataAtual = date("Ymd");
    $horaAtual = date("Hi");

    $arq = "tmp/{$constante}_{$versao}_{$cnpj}_{$dataAtual}_{$horaAtual}.txt";

    $arquivo = fopen($arq, 'w');

    $result = $clrhteutri->sql_record($clrhteutri->sql_query(null,
                                                          "rh67_dias,
                                                          rh67_vales,
                                                          rh01_regist,
                                                          z01_nome,
                                                          z01_cgccpf,
                                                          z01_nasc,
                                                          z01_sexo,
                                                          z01_ident,
                                                          z01_identorgao,
                                                          z01_telef ",
                                                          $xordem,
                                                          $where));

    $xxnum = pg_num_rows($result);

    if ($rh67_rhtipovale == 1) {

            $iContador = 1;
            if ($lEmitePorValor) {

                //Header
                $sHeader  = str_pad($iContador, 5, '0', STR_PAD_LEFT);         // Nr_seq_reg
                $sHeader .= '01';                                              // Tp_registro
                $sHeader .= 'CADUSU';                                          // Nm_arquivo
                $sHeader .= '04.02';                                           // Nr_versão
                $sHeader .= db_formatar($cgc, 's', '0', 14, 'e', 0) . PHP_EOL; // Nr_doc_comprd (CPF/CNPJ/CEI)

                fputs($arquivo, $sHeader);

                $iAno = DBPessoal::getAnoFolha();
                $iMes = DBPessoal::getMesFolha();

                    for ($x = 0; $x < pg_num_rows($result); $x++) {
                        db_fieldsmemory($result, $x);

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

                        $nValor = str_replace(".", "", db_utils::fieldsMemory($rsValorVtf, 0)->valor);

                        $dataFormatada = DateTime::createFromFormat('Y-m-d', $z01_nasc)->format('dmY');

                        $iContador++;

                        $sLinha  = str_pad($iContador, 5, '0', STR_PAD_LEFT);
                        $sLinha .= db_formatar('02', 's', ' ', 2, 'd', 0);
                        $sLinha .= db_formatar($rh01_regist, 's', ' ', 15, 'd', 0);
                        $sLinha .= db_formatar($z01_nome, 's', ' ', 40, 'd', 0);
                        $sLinha .= db_formatar($z01_cgccpf, 's', ' ', 11, 'd', 0);
                        $sLinha .= str_pad($nValor, 6, "0", STR_PAD_LEFT);
                        $sLinha .= db_formatar('02', 's', ' ', 2, 'd', 0);
                        $sLinha .= db_formatar('01', 's', ' ', 2, 'd', 0);
                        $sLinha .= str_pad('', 13, ' ', STR_PAD_LEFT);
                        $sLinha .= db_formatar('04', 's', ' ', 2, 'd', 0);
                        $sLinha .= db_formatar($dataFormatada, 's', ' ', 8, 'd', 0);
                        $sLinha .= db_formatar($z01_sexo, 's', ' ', 1, 'd', 0);
                        $sLinha .= str_pad($z01_ident, 15, "0", STR_PAD_LEFT);
                        $sLinha .= db_formatar($z01_identorgao, 's', ' ', 8, 'd', 0);
                        $sLinha .= db_formatar('024', 's', ' ', 3, 'd', 0);
                        $sLinha .= db_formatar($z01_telef, 's', ' ', 10, 'd', 0);
                        $sLinha .= db_formatar('sme@epdvr.com.br', 's', ' ', 60, 'd', 0) . PHP_EOL;

                        fputs($arquivo, $sLinha);
                    }

                    $sTrailer = str_pad($iContador, 5, '0', STR_PAD_LEFT);
                    $sTrailer .= "99" . PHP_EOL;

                    fputs($arquivo, $sTrailer);
            }
    }
    fclose($arquivo);
}

$oRetorno = (object)array( 'erro' => false,
                           'mensagem' => '',
                           'arquivo' => $arq);

echo JSON::create()->stringify($oRetorno);

?>

