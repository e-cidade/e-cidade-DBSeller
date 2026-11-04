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

use ECidade\RecursosHumanos\Pessoal\Service\DecimoServidorService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_libpessoal.php"));
require_once(modification("dbforms/db_funcoes.php"));

global $cfpess, $subpes, $d08_carnes, $db21_codcli, $rhrubricasadiantamento;

$subpes = db_anofolha() . '/' . db_mesfolha();

db_selectmax("cfpess", " select * from cfpess " . bb_condicaosubpes("r11_"));

//db_selectmax("rhrubricasadiantamento", " select * from rhrubricasadiantamento " . bb_condicaosubpes("rh262_"));

db_inicio_transacao();

global $opcao, $r110_lotaci, $r110_lotacf, $r110_regisi, $r110_regisf, $opcao_gml, $opcao_geral, $faixa_lotac, $faixa_regis;
global $lotacao_faixa, $instituicao, $ano, $data_decimo_13;
db_postmemory($HTTP_POST_VARS);
if (isset($selecao) && !empty($selecao)) {

    //verificamos se foi informada selecao, buscamos a condicao e aplicamos na consulta
    $oDaoRhPessoalMov = new cl_rhpessoalmov();
    $oSelecao = new Selecao($selecao);
    $sWhereServidores  = "     rh02_anousu = " . DBPessoal::getAnoFolha();
    $sWhereServidores .= " and rh02_mesusu = " . DBPessoal::getMesFolha();
    $sWhereServidores .= " and rh02_instit = " . db_getsession('DB_instit');
    $sWhereServidores .= " 
    and rh02_regist in (select rh02_regist 
        from pessoal.rhpessoal
            inner join cgm on rh01_numcgm = z01_numcgm
            inner join pessoal.rhpessoalmov on rh01_regist = rh02_regist 
                                    and rh02_anousu = " . DBPessoal::getAnoFolha(). "
                                    and rh02_mesusu = " . DBPessoal::getMesFolha() . "
                                    and rh02_instit = " . db_getsession("DB_instit") . "
            left join pessoal.rhlota on rhlota.r70_codigo = rhpessoalmov.rh02_lota
                            and rhlota.r70_instit = rhpessoalmov.rh02_instit
            left join pessoal.rhregime on rhregime.rh30_codreg = rhpessoalmov.rh02_codreg
            left join pessoal.rhpescargo on rhpescargo.rh20_seqpes = rhpessoalmov.rh02_seqpes
            left join pessoal.rhpespadrao on rhpespadrao.rh03_seqpes = rhpessoalmov.rh02_seqpes             
                                and rhpespadrao.rh03_anousu = rhpessoalmov.rh02_anousu             
                                and rhpespadrao.rh03_mesusu = rhpessoalmov.rh02_mesusu
            left join pessoal.rhpesrescisao on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes
    where " . $oSelecao->getWhere() . ")";
    $sSqlServidores = $oDaoRhPessoalMov->sql_query_file(null, null, 'rh02_regist', null, $sWhereServidores);
    $rsServidores  = db_query($sSqlServidores);

    if (!$rsServidores) {
        throw new DBException("Ocorreu um erro ao buscar as matr�culas da sele��o informada.");
    }
    $aMatriculas = db_utils::makeCollectionFromRecord($rsServidores, function($oResultado) {
        return $oResultado->rh02_regist;
    });

    if (sizeof($aMatriculas) > 0) {
        rsort($aMatriculas);
        if (empty($faixa_regis)) {
            $faixa_regis = implode(",", $aMatriculas);
        } else {
            $faixa_regis .= "," . implode(",", $aMatriculas);
        }
    } else {
        throw new DBException("Nenhuma matr�cula encontrada na sele��o informada.");
    }
    $opcao_gml = "m";
}

if (!isset($r110_lotaci)) {
    $r110_lotaci = '    ';
}

if (!isset($r110_lotacf)) {
    $r110_lotacf = '    ';
}
if (!isset($r110_regisi)) {
    $r110_regisi = $faixa_regis;
}

if (!isset($r110_regisf)) {
    $r110_regisf = $faixa_regis;
}

if (!isset($opcao_filtro)) {
    $opcao_filtro = "0";
}


if ($faixa_lotac != " ") {
    $lotacao_faixa = $faixa_lotac;
}

if (!isset($instituicao)) {
    $instituicao = "";
}

if (!isset($ano)) {
    $ano = "";
}

if (!isset($data_decimo_13)) {
    $data_decimo_13 = "";
}

//echo "faixa_lotac-->$faixa_lotac  r110_lotaci-->$r110_lotaci  r110_lotacf-->$r110_lotacf ";
//
//$opcao_gml = 'g';
//$opcao_geral = 1;
//$r110_regisi = 1;
//$r110_regisf = 1000;


?>
  <html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
<br><br><br>
<center>
    <?php
    db_criatermometro('calculo_folha', 'Concluido...', 'blue', 1, 'Efetuando Calculo ...');
    ?>
</center>
<?php
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"),
  db_getsession("DB_instit"));
?>
</body>
<?php
global $db_config;
db_selectmax("db_config",
  "select lower(trim(munic)) as d08_carnes , db21_codcli as codcli, cgc from db_config where codigo = " . db_getsession("DB_instit"));

$db21_codcli = $db_config[0]["codcli"];

if (trim($db_config[0]["cgc"]) == "90940172000138") {
    $d08_carnes = "daeb";
} else {
    $d08_carnes = $db_config[0]["d08_carnes"];
}


$db_erro = false;


if ($opcao == '1' || $opcao == '2') {
//echo "<BR> passou aqui! d08_carnes --> $d08_carnes";
    gera_ponto();
} else {
    diferenca_163();
}

//echo "<BR> antes do fim db_fim_transacao()";
//flush();
//exit;
db_fim_transacao();
//flush();
db_redireciona("pes4_manut13o001.php");

function diferenca_163()
{

    global $opcao, $opcao_filtro, $opcao_gml, $r110_regisi, $r110_regisf, $r110_lotaci,
           $r110_lotacf, $faixa_regis, $faixa_lotac, $d08_carnes, $subpes, $db21_codcli;

    $m_rubr = array();
    $m_valor = array();
    $m_quant = array();

    $condicaoaux = db_condicaoaux($opcao_filtro, $opcao_gml, "rh02_", $r110_regisi, $r110_regisf, $r110_lotaci,
      $r110_lotacf, $faixa_regis, $faixa_lotac);

    if ($opcao_gml == "l") {
        if ($opcao_filtro == "i") {
            $condicaoaux = " and r70_estrut between " . db_sqlformat($r110_lotaci);
            $condicaoaux .= " and " . db_sqlformat($r110_lotacf);
        } else {
            $condicaoaux = " and r70_estrut in (" . $faixa_lotac . ")";
        }
    }

    global $pessoal;
    $sql = "select rh01_regist as r01_regist,
                 rh02_hrsmen as r01_hrsmen, 
                 trim(TO_CHAR(RH02_LOTA,'9999')) as r01_lotac,  
                 rh01_numcgm as r01_numcgm, 
                 rh03_regime as r01_regime, 
                     rh02_tbprev as r01_tbprev,
                     rh01_admiss as r01_admiss,
                     rh05_recis  as r01_recis
                 from rhpessoal
                      inner join rhpessoalmov  on  rhpessoalmov.rh02_regist  = rhpessoal.rh01_regist
                      inner join rhlota        on  r70_codigo                = rh02_lota
                                              and  r70_instit                = rh02_instit
                      left  join rhpespadrao   on  rhpespadrao.rh03_seqpes   = rhpessoalmov.rh02_seqpes 
                          left  join rhpesrescisao on  rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes 
                       " . bb_condicaosubpes("rh02_") . $condicaoaux;

    if (!db_selectmax("pessoal", $sql)) {
        return;
    }
    $imax = count($pessoal);

    $matriz1 = array();
    $matriz2 = array();

    $matriz1[1] = "r10_regist";
    $matriz1[2] = "r10_rubric";
    $matriz1[3] = "r10_valor";
    $matriz1[4] = "r10_quant";
    $matriz1[5] = "r10_lotac";
    $matriz1[6] = "r10_datlim";
    $matriz1[7] = "r10_anousu";
    $matriz1[8] = "r10_mesusu";
    $matriz1[9] = "r10_instit";

    for ($Ipessoal = 0; $Ipessoal < count($pessoal); $Ipessoal++) {
//echo "<BR> 1 passou aqui r01_regist --> ".$pessoal[$Ipessoal]["r01_regist"];
        db_atutermometro($Ipessoal, count($pessoal), 'calculo_folha', 1);

        $condicaoaux = " and r10_regist = " . db_sqlformat($pessoal[$Ipessoal]["r01_regist"]);
        $condicaoaux .= " and r10_rubric >= '4000' and r10_rubric < '6000' ";
        db_delete("pontofs", bb_condicaosubpes("r10_") . $condicaoaux);
        $condicaoaux = " and r10_regist = " . db_sqlformat($pessoal[$Ipessoal]["r01_regist"]);

        global $pontofs;
        if (db_selectmax("pontofs", "select * from pontofs " . bb_condicaosubpes("r10_") . $condicaoaux)) {

            $imax_rubr = 0;
            for ($Ipontofs = 0; $Ipontofs < count($pontofs); $Ipontofs++) {
//echo "<BR> 2 passou aqui r10_rubric --> ".$pontofs[$Ipontofs]["r10_rubric"]." r01_regime --> ". $pessoal[$Ipessoal]["r01_regime"];

                // para carazinho a rubricas de hora extra nao deve ser lida no complemento;
                // pois e lida apenas por 6 meses ( junho a novembro );
                if ($db21_codcli == "18" && $pessoal[$Ipessoal]["r01_regime"] != 2
                  && (db_at($pontofs[$Ipontofs]["r10_rubric"], "0004-0005") > 0)) {
                    continue;
                }
                $condicaoaux = " where rh27_instit = " . db_getsession("DB_instit") . " and rh27_rubric = " . db_sqlformat($pontofs[$Ipontofs]["r10_rubric"]);
                global $rubricas;
                if (db_selectmax("rubricas", "select * from rhrubricas " . $condicaoaux)) {
//echo "<BR> 1 rh27_calc2 --> ".$rubricas[0]["rh27_calc2"] . " digito --> ".db_substr($pontofs[$Ipontofs]["r10_rubric"],1,1);
//echo "<BR> rh27_calc2 --> ".(db_at($rubricas[0]["rh27_calc2"],"3-4-5-6-8-9") > 0?"sim":"nao");
//echo "<BR> rh27_rubric  --> ".(db_substr($pontofs[$Ipontofs]["r10_rubric"],1,1) == "0" || db_substr($pontofs[$Ipontofs]["r10_rubric"],1,1) == "1"?"sim":"nao");
//echo "<BR> rh27_calc2 2 -->".(db_val($rubricas[0]["rh27_calc2"]) != 0?"sim":"nao");
                    if (db_at(db_str($rubricas[0]["rh27_calc2"], 1), "3-4-5-6-8-9") > 0
                      && (db_substr($pontofs[$Ipontofs]["r10_rubric"], 1,
                          1) == "0" || db_substr($pontofs[$Ipontofs]["r10_rubric"], 1, 1) == "1")
                    ) {
//echo "<BR> 2 rh27_calc2 --> ".$rubricas[0]["rh27_calc2"] . " digito --> ".db_substr($pontofs[$Ipontofs]["r10_rubric"],1,1);
                        $quant = 0;
                        $valor = 0;

                        if (db_at(db_str($rubricas[0]["rh27_calc2"], 1), "3-4-5-6-8-9") > 0) {
//echo "<BR> 3 passou aqui !!";
                            if (db_empty($rubricas[0]["rh27_form"])) {
//echo "<BR> 4 passou aqui !!";
                                $valor = $pontofs[$Ipontofs]["r10_valor"] / 12;
                            } else {
//echo "<BR> 5 passou aqui !!";
                                $quant = $pontofs[$Ipontofs]["r10_quant"] / 12;
                            }
                        }
                        $rubrica = db_str(db_val($pontofs[$Ipontofs]["r10_rubric"]) + 4000, 4);

                        $imax_rubr++;
                        $m_rubr[$imax_rubr] = $rubrica;
                        $m_valor[$imax_rubr] = $valor;
                        $m_quant[$imax_rubr] = $quant;
                    }
                }
            }
            for ($iind = 1; $iind <= $imax_rubr; $iind++) {

//echo "<BR> for($iind = 1 ; $iind <= $imax_rubr;$iind++){";
//echo "<BR rubric --> ".$m_rubr[$iind];
                $matriz2[1] = $pessoal[$Ipessoal]["r01_regist"];
                $matriz2[2] = $m_rubr[$iind];
                $matriz2[3] = round($m_valor[$iind], 2);
                $matriz2[4] = round($m_quant[$iind], 2);
                $matriz2[5] = $pessoal[$Ipessoal]["r01_lotac"];
                $matriz2[6] = bb_space(7);
                $matriz2[7] = db_val(db_substr($subpes, 1, 4));
                $matriz2[8] = db_val(db_substr($subpes, -2));
                $matriz2[9] = db_getsession("DB_instit");

                db_insert("pontofs", $matriz1, $matriz2);


            }
        }
    }
}

// Gera o ponto de 13 ou o adiantamento do 13 salario
// tambem gera o complemento do adiantamento do 13 salario , por exemplo uma pessoal que ganho 50 reais no mes de junho
// como adiantamento de 13 e no mes de novembro for feito o adiantamento de 13 salario para todos o sistema permite que voce
// paga so o complemento do adiantamento (no caso a diferenca entre o que ele tem a receber por explo 200 reais , que seria
// os 200 - 50 que ja foi adiantado , entao receberia 150 reais como complemento de adiantamento, ou nao daria complemento de
// adiantamento para esta pessoal e so abateria os 50 reais quando ele recebe o 13 salario propriamente dito no final do ano

function gera_ponto()
{
    require_once 'model/configuracao/DBLog.model.php';

    global $pagaradiantamentonovamente, $fracao_certa, $mesana, $d08_carnes, $db21_codcli;

    global $subpes, $cfpess, $pessoal, $Ipessoal, $dias_pagamento, $db_config, $instituicao, $ano, $data_decimo_13;;

    global $opcao, $opcao_filtro, $opcao_gml, $r110_regisi, $r110_regisf, $r110_lotaci,
           $r110_lotacf, $faixa_regis, $faixa_lotac;

    $dbLog = new DBLog('TXT', 'tmp/manutencao_13');

    $dbLog->escreverLog("----- Filtros In�cio -----");
    $dbLog->escreverLog("=> Op��o: {$opcao} (1 - Adiantamento 13� / 2 - Saldo 13� / 3 - Complemento 13�)");
    $dbLog->escreverLog("=> Pagar complemento de adiantamento: {$pagaradiantamentonovamente}");
    $dbLog->escreverLog("=> Fra��o para pagamento: {$fracao_certa}");
    $dbLog->escreverLog("=> Sele��o: {$opcao_gml} (g - Geral / m - Matr�cula / l - Lota��o)");
    $dbLog->escreverLog("----- Filtros Fim -----");
    $dbLog->escreverLog("");

    $subpes = db_anofolha() . '/' . db_mesfolha();

    $dbLog->escreverLog("=> Ano/M�s folha: {$subpes}");

    $r11_sald13 = ($opcao == 1 ? '0' : '1');
    $matriz1 = array();
    $matriz2 = array();
    $matriz1[1] = "r11_sald13";
    $matriz2[1] = (db_boolean($r11_sald13) == true ? 't' : 'f');

    $dbLog->escreverLog("##### Atualizando cfpess - r11_sald13: {$r11_sald13} (0 - false / 1 - true) #####");

    db_update("cfpess", $matriz1, $matriz2, bb_condicaosubpes("r11_"));

    $subpes_processa = db_strtran($cfpess[0]["r11_altfer"], "/", "");
    $faixa_lotac = str_replace("\\", "", $faixa_lotac);

    $condicaoaux = db_condicaoaux($opcao_filtro, $opcao_gml, "rh02_", $r110_regisi, $r110_regisf, $r110_lotaci,
      $r110_lotacf, $faixa_regis, str_replace("\\", "", $faixa_lotac));
    if ($opcao_gml == "l") {
        if ($opcao_filtro == "i") {
            $condicaoaux = " and r70_estrut between " . db_sqlformat($r110_lotaci);
            $condicaoaux .= " and " . db_sqlformat($r110_lotacf);
        } else {
            $condicaoaux = " and r70_estrut in (" . $faixa_lotac . ")";
        }
    }

    $dbLog->escreverLog("##### condicaoaux: {$condicaoaux} #####");

    $sql = "select rh01_regist as r01_regist, ";
    $sql .= "       rh02_hrsmen as r01_hrsmen, ";
    $sql .= "       trim(TO_CHAR(RH02_LOTA,'9999')) as r01_lotac, ";
    $sql .= "       rh01_numcgm as r01_numcgm, ";
    $sql .= "       rh03_regime as r01_regime, ";
    $sql .= "            rh02_tbprev as r01_tbprev, ";
    $sql .= "            rh01_admiss as r01_admiss, ";
    $sql .= "            rh05_recis  as r01_recis ";
    $sql .= "       from rhpessoal ";
    $sql .= "            inner join rhpessoalmov  on  rhpessoalmov.rh02_regist  = rhpessoal.rh01_regist ";
    $sql .= "            inner join rhlota        on  r70_codigo                = rh02_lota ";
    $sql .= "                                    and  r70_instit                = rh02_instit ";
    $sql .= "            left  join rhpespadrao   on  rhpespadrao.rh03_seqpes   = rhpessoalmov.rh02_seqpes ";
    $sql .= "            left  join rhpesrescisao on  rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes ";
    $sql .= "             " . bb_condicaosubpes("rh02_") . $condicaoaux;

    $dbLog->escreverLog("");
    $dbLog->escreverLog("##### [1] SQL In�cio - rhpessoal/rhpessoalmov/rhlota/rhpespadrao/rhpesrescisao #####");
    $dbLog->escreverLog($sql);
    $dbLog->escreverLog("##### [1] SQL FIM #####");
    $dbLog->escreverLog("");

    db_selectmax("pessoal", $sql);

    $dbLog->escreverLog("##### Come�ando a percorrer os registros [1] #####");
    for ($Ipessoal = 0; $Ipessoal < count($pessoal); $Ipessoal++) {
        $dbLog->escreverLog("----- Matr�cula: {$pessoal[$Ipessoal]["r01_regist"]} -----");

        $m_rubric = array();
        $m_tipo = array();
        $m_media = array();
        $m_valor = array();
        $m_quant = array();
        $m_form = array();
        $qten = array();
        $vlrn = array();

        db_atutermometro($Ipessoal, count($pessoal), 'calculo_folha', 1);
        flush();

        global $cadferia;

        $condicaoaux = " and r30_regist = " . db_sqlformat($pessoal[$Ipessoal]["r01_regist"]);
        db_selectmax("cadferia", "select r30_paga13 from cadferia " . bb_condicaosubpes("r30_") . $condicaoaux . " order by r30_perai desc limit 1");

        $matric = $pessoal[$Ipessoal]["r01_regist"];
        $condicaoaux = " and r34_regist = " . db_sqlformat($matric);

        if (db_delete("pontof13", bb_condicaosubpes("r34_") . $condicaoaux)) {
            $dbLog->escreverLog("");
            $dbLog->escreverLog("  *** [1] if in�cio ***");
            $dbLog->escreverLog("      => deletado da tabela pontof13... deletando da tabela gerfs13");

            db_delete("gerfs13", bb_condicaosubpes("r35_") . " and r35_regist = " . db_sqlformat($matric));
            db_delete("gerfsad13", bb_condicaosubpes("r95_") . " and r95_regist = " . db_sqlformat($matric));

            $dbLog->escreverLog("  *** [1] if fim ***");
            $dbLog->escreverLog("");
        }

        //  demitidos nao calcular;
        //  nao calcular adiantamento para quem esta todo o mes afastado;

        $situa_func = situacao_funcionario($pessoal[$Ipessoal]["r01_regist"]);

        $dbLog->escreverLog("  => Situa��o Funcion�rio: {$situa_func}");
        $dbLog->escreverLog("  => Rescis�o(r01_recis): {$pessoal[$Ipessoal]["r01_recis"]}");
        $dbLog->escreverLog("  => Dias de pagamento: {$dias_pagamento}");

        if (!db_empty($pessoal[$Ipessoal]["r01_recis"])) {
            $dbLog->escreverLog("");
            $dbLog->escreverLog("  *** [2] if in�cio ***");
            $dbLog->escreverLog("      => continue...");
            $dbLog->escreverLog("  *** [2] if fim ***");
            $dbLog->escreverLog("");

            continue;
        }

        if ($opcao == 1 && $situa_func != 1 && $dias_pagamento == 0) {
            $dbLog->escreverLog("");
            $dbLog->escreverLog("  *** [3] if in�cio ***");

            if ($db21_codcli == "4" || $db21_codcli == "20") {
                $dbLog->escreverLog("");
                $dbLog->escreverLog("  *** [4] if in�cio ***");

                if ($pessoal[$Ipessoal]["r01_regime"] == 1 && ($situa_func != 6 && $situa_func != 5 && $situa_func != 3) && $pessoal[$Ipessoal]["r01_tbprev"] != $cfpess[0]["r11_tbprev"]) {
                    $dbLog->escreverLog("");
                    $dbLog->escreverLog("  *** [5] if in�cio ***");
                    $dbLog->escreverLog("      => continue...");
                    $dbLog->escreverLog("  *** [5] if fim ***");
                    $dbLog->escreverLog("");

                    continue;
                }

                $dbLog->escreverLog("  *** [4] if fim ***");
                $dbLog->escreverLog("");
            } else {
                $dbLog->escreverLog("");
                $dbLog->escreverLog("  *** [4] else in�cio ***");
                $dbLog->escreverLog("      => continue...");
                $dbLog->escreverLog("  *** [4] else fim ***");
                $dbLog->escreverLog("");
                continue;
            }

            $dbLog->escreverLog("  *** [3] if fim ***");
            $dbLog->escreverLog("");
        }

        $datp13dias = ndias(db_substr($subpes, -2) . "/" . db_substr($subpes, 1, 4));
        $datp13 = db_ctod(db_str($datp13dias, 2, 0, " 0") . "/" . db_substr($subpes, -2) . "/" . db_substr($subpes, 1, 4));
        $fracao = $fracao_certa;
        $meses_admissao = 0;

        $dbLog->escreverLog("  => datp13dias: {$datp13dias}");
        $dbLog->escreverLog("  => datp13: {$datp13}");
        $dbLog->escreverLog("  => fracao: {$fracao}");
        $dbLog->escreverLog("  => Admiss�o: {$pessoal[$Ipessoal]["r01_admiss"]}");
        $dbLog->escreverLog("  => db_val(db_substr(subpes, 1, 4)): " . db_val(db_substr($subpes, 1, 4)));

        if (db_year($pessoal[$Ipessoal]["r01_admiss"]) == db_val(db_substr($subpes, 1, 4))) {
            $dbLog->escreverLog("");
            $dbLog->escreverLog("  *** [6] if in�cio ***");

            $xdat = $pessoal[$Ipessoal]["r01_admiss"];
            $nm13 = db_month($datp13);

            if (db_day($xdat) > 16 && db_year($pessoal[$Ipessoal]["r01_admiss"]) == db_year($datp13)) {
                $dbLog->escreverLog("");
                $dbLog->escreverLog("  *** [7] if in�cio ***");

                $meses_admissao = db_month($pessoal[$Ipessoal]["r01_admiss"]);

                $dbLog->escreverLog("      => meses_admissao: {$meses_admissao}");
                $dbLog->escreverLog("  *** [7] if fim ***");
                $dbLog->escreverLog("");
            } else {
                $dbLog->escreverLog("");
                $dbLog->escreverLog("  *** [7] else in�cio ***");

                $meses_admissao = db_month($pessoal[$Ipessoal]["r01_admiss"]) - 1;

                $dbLog->escreverLog("      => meses_admissao: {$meses_admissao}");
                $dbLog->escreverLog("  *** [7] else fim ***");
                $dbLog->escreverLog("");
            }

            $dbLog->escreverLog("  *** [6] if fim ***");
            $dbLog->escreverLog("");
        } else {
            $dbLog->escreverLog("");
            $dbLog->escreverLog("  *** [6] else in�cio ***");

            $xdat = db_ctod("01/01/" . db_substr(db_dtoc($datp13), 7, 4));
            $nm13 = db_month($datp13);

            $dbLog->escreverLog("      => xdat: {$xdat}");
            $dbLog->escreverLog("      => nm13: {$nm13}");

            $dbLog->escreverLog("  *** [6] else fim ***");
            $dbLog->escreverLog("");
        }

        $meses_afastado = calcula_afastamentos();
        $dbLog->escreverLog("  => meses_afastado: {$meses_afastado}");

        if ($opcao == 1
          && ($db21_codcli == "4" || $db21_codcli == "20")
          && $pessoal[$Ipessoal]["r01_regime"] == 1
          && $pessoal[$Ipessoal]["r01_tbprev"] != $cfpess[0]["r11_tbprev"]
          && (
            (
              $dias_pagamento == 0
              && ($situa_func == 6 || $situa_func == 3 || $situa_func == 5)
            )
            ||
            (
              $dias_pagamento == 30
              && $situa_func == 1
            )
          )
        ) {
            $meses_afastado = 0;
        }

        $imax = 0;
        $sal13 = 0;
        $pensao_alim_13 = 0;
        $m_rubric[1] = "";
        $m_quant[1] = 0;
        $m_valor[1] = 0;
        $m_cont[1] = 0;
        $qten[1] = 0;
        $vlrn[1] = 0;


        $matriz1 = array();
        $matriz2 = array();
        $matriz1[1] = "r34_regist";
        $matriz1[2] = "r34_rubric";
        $matriz1[3] = "r34_valor";
        $matriz1[4] = "r34_quant";
        $matriz1[5] = "r34_lotac";
        $matriz1[6] = "r34_media";
        $matriz1[7] = "r34_calc";
        $matriz1[8] = "r34_anousu";
        $matriz1[9] = "r34_mesusu";
        $matriz1[10] = "r34_instit";

        if ($nm13 == 12 && db_val(db_substr($subpes, 6, 2)) >= 12) {
            $mesana = $mesana;
        } else {
            $mesana = db_val(db_substr($subpes, 6, 2)); // mes da folha
        }

        // faz levantamento e avalia mes a mes
        for ($cont = 1; $cont <= $mesana; $cont++) {

            $mes_rubric = array();
            $mes_valor = array();
            $mes_quant = array();
            $qten_mes = array();
            $vlrn_mes = array();
            $maxmes = 0;
            $tem_no_mes_tipo_9 = true;

            $indmes = 0;

            $mes = db_str($cont, 2, 0, "0");
            $ano = db_substr($subpes, 1, 4);

            if (!($cont < db_month($xdat) || ($cont == db_month($xdat) && db_day($xdat) > 16) || $cont > db_val(db_substr($subpes, 6, 2)))) {

                // para a leitura apos a conversao de ferias.;
                $subpes_cont = $ano . "/" . $mes;
                $tem_no_mes_tipo9 = false;
                $funcionario_em_ferias = "n";
                $dias_de_ferias = 0;
                $dias_de_abono = 0;

                $condicaoaux = " and r14_regist = " . db_sqlformat($matric);
                global $gerfsal;

                db_selectmax("gerfsal", "select * from gerfsal " . bb_condicaosubpesproc("r14_", $subpes_cont) . $condicaoaux);
                for ($Igerfsal = 0; $Igerfsal < count($gerfsal); $Igerfsal++) {

                    if (db_val($gerfsal[$Igerfsal]["r14_rubric"]) > 0 && db_val($gerfsal[$Igerfsal]["r14_rubric"]) < 2000) {

                        // para carazinho a rubricas de hora extra nao deve ser lida no complemento;
                        // pois e lida apenas por 6 meses ( junho a novembro );
                        // colocado este if para, em Carazinho, se for pago o 13o com a folha de dezembro fechada, pegar de julho em diante
                        // senao pegar de junho
                        if ($mesana == 12) {
                            $meses_carazinho = 7;
                        } else {
                            $meses_carazinho = 6;
                        }
                        if ($db21_codcli == "18" && $pessoal[$Ipessoal]["r01_regime"] != 2
                          && ($gerfsal[$Igerfsal]["r14_rubric"] == "0004" || $gerfsal[$Igerfsal]["r14_rubric"] == "0005")
                          && db_val($mes) < $meses_carazinho) {
                            continue;
                        }
                        if (!db_empty($cfpess[0]["r11_rubdec"])) {
                            if ($gerfsal[$Igerfsal]["r14_rubric"] == $cfpess[0]["r11_rubdec"]) {
                                $sal13 += $gerfsal[$Igerfsal]["r14_valor"];
                            }
                        }

                        if (!db_empty($cfpess[0]["r11_palime"])) {
                            if ($gerfsal[$Igerfsal]["r14_rubric"] == db_str(db_val($cfpess[0]["r11_palime"]) + 4000, 4,
                                0)) {
                                $pensao_alim_13 += $gerfsal[$Igerfsal]["r14_valor"];
                            }
                        }

                        // verifica se funcionario tem a rubrica de ferias do cfpess;
                        if ($gerfsal[$Igerfsal]["r14_rubric"] == $cfpess[0]["r11_ferias"]) {
                            if ($db21_codcli != "11") {
                                $funcionario_em_ferias = "s";
                            } else {
                                if ($db21_codcli == "11" && "f" == $cadferia[0]["r30_paga13"]) {
                                    $funcionario_em_ferias = "s";
                                }
                            }
                            $dias_de_ferias = $gerfsal[$Igerfsal]["r14_quant"];
                        } else {
                            $dias_de_abono = $gerfsal[$Igerfsal]["r14_quant"];
                        }

                        global $rubricas;
                        $condicaoaux = " where rh27_instit = " . db_getsession("DB_instit") . " and rh27_rubric = " . db_sqlformat($gerfsal[$Igerfsal]["r14_rubric"]);
                        
                        //dd($condicaoaux);

                        if (db_selectmax("rubricas", "select * from rhrubricas " . $condicaoaux) && $rubricas[0]["rh27_calc2"] != 0) {
                            $indmes = db_ascan($mes_rubric, $gerfsal[$Igerfsal]["r14_rubric"]);
                            if (db_empty($indmes)) {
                                $maxmes += 1;
                                $indmes = $maxmes;
                                $qten_mes[$maxmes] = 0;
                                $vlrn_mes[$maxmes] = 0;
                                $mes_rubric[$maxmes] = $gerfsal[$Igerfsal]["r14_rubric"];
                                $mes_valor[$maxmes] = 0;
                                $mes_quant[$maxmes] = 0;
                            }
                            if ($rubricas[0]["rh27_calc2"] == 9) {
                                $tem_no_mes_tipo9 = true;
                                $mes_valor[$indmes] = $gerfsal[$Igerfsal]["r14_valor"];
                                $mes_quant[$indmes] = $gerfsal[$Igerfsal]["r14_quant"];
                            } else {
                                $mes_valor[$indmes] += $gerfsal[$Igerfsal]["r14_valor"];
                                $mes_quant[$indmes] += $gerfsal[$Igerfsal]["r14_quant"];
                                if (db_at(db_str($rubricas[0]["rh27_calc2"], 1), "1-2-7") > 0) {
                                    $vlrn_mes[$indmes] = $gerfsal[$Igerfsal]["r14_valor"];
                                    $qten_mes[$indmes] = $gerfsal[$Igerfsal]["r14_quant"];
                                }
                            }
                        }
                    }
                }
                $condicaoaux = " and r48_regist = " . db_sqlformat($matric);
                global $gerfcom;

                db_selectmax("gerfcom", "select * from gerfcom " . bb_condicaosubpesproc("r48_", $subpes_cont) . $condicaoaux);

                for ($Igerfcom = 0; $Igerfcom < count($gerfcom); $Igerfcom++) {
                    if (db_substr($gerfcom[$Igerfcom]["r48_rubric"], 1,
                        1) != "R" && db_val($gerfcom[$Igerfcom]["r48_rubric"]) > 0 && db_val($gerfcom[$Igerfcom]["r48_rubric"]) < 2000) {

                        // para carazinho a rubricas de hora extra nao deve ser lida no complemento;
                        // pois e lida apenas por 6 meses ( junho a novembro );
                        if ($db21_codcli == "18" && $pessoal[$Ipessoal]["r01_regime"] != 2
                          && ($gerfcom[$Igerfcom]["r48_rubric"] == "0004" || $gerfcom[$Igerfcom]["r48_rubric"] == "0005")
                          && db_val($mes) < 6) {
                            continue;
                        }

                        if (!db_empty($cfpess[0]["r11_rubdec"])) {
                            if ($gerfcom[$Igerfcom]["r48_rubric"] == $cfpess[0]["r11_rubdec"]) {
                                $sal13 += $gerfcom[$Igerfcom]["r48_valor"];
                            }
                        }

                        // verifica se funcionario tem a rubrica de ferias do cfpess;
                        if ($gerfcom[$Igerfcom]["r48_rubric"] == $cfpess[0]["r11_ferias"]) {
                            if ($db21_codcli != "11") {
                                $funcionario_em_ferias = "c";
                            } else {
                                if ($db21_codcli == "11" && "f" == $cadferia[0]["r30_paga13"]) {
                                    $funcionario_em_ferias = "c";
                                }
                            }
                            $dias_de_ferias = $gerfcom[$Igerfcom]["r48_quant"];
                        } else {
                            $dias_de_abono = $gerfcom[$Igerfcom]["r48_quant"];
                        }

                        $condicaoaux = "where rh27_instit = " . db_getsession("DB_instit") . " and rh27_rubric = " . db_sqlformat($gerfcom[$Igerfcom]["r48_rubric"]);
                        global $rubricas;

                        if (db_selectmax("rubricas", "select * from rhrubricas " . $condicaoaux) && $rubricas[0]["rh27_calc2"] != 0) {
                            $indmes = db_ascan($mes_rubric, $gerfcom[$Igerfcom]["r48_rubric"]);
                            if (db_empty($indmes)) {
                                $maxmes += 1;
                                $indmes = $maxmes;
                                $qten_mes[$maxmes] = 0;
                                $vlrn_mes[$maxmes] = 0;

                                $mes_rubric[$maxmes] = $gerfcom[$Igerfcom]["r48_rubric"];
                                $mes_valor[$maxmes] = 0;
                                $mes_quant[$maxmes] = 0;
                            }

                            if ($rubricas[0]["rh27_calc2"] == 9) {
                                if (!$tem_no_mes_tipo9) {
                                    $mes_valor[$indmes] = $gerfcom[$Igerfcom]["r48_valor"];
                                    $mes_quant[$indmes] = $gerfcom[$Igerfcom]["r48_quant"];
                                }
                            } else {
                                $mes_valor[$indmes] += $gerfcom[$Igerfcom]["r48_valor"];
                                $mes_quant[$indmes] += $gerfcom[$Igerfcom]["r48_quant"];
                                if (db_at(db_str($rubricas[0]["rh27_calc2"], 1), "1-2-7") > 0) {
                                    $vlrn_mes[$indmes] = $gerfcom[$Igerfcom]["r48_valor"];
                                    $qten_mes[$indmes] = $gerfcom[$Igerfcom]["r48_quant"];
                                }
                            }
                        }
                    }
                }

                // assim avaliara os dados antigos tambem por somatoria de arquivos;
                $condicaoaux = " and r31_regist = " . db_sqlformat($matric);

                if (db_selectmax("gerffer", "select * from gerffer " . bb_condicaosubpesproc("r31_", $subpes_cont) . $condicaoaux)) {
                    if (($subpes_processa > ($ano . $mes) || db_empty($subpes_processa))) {
                        if ($db21_codcli != "11") {
                            $funcionario_em_ferias = "a";
                        } else {
                            if ($db21_codcli == "11" && "f" == $cadferia[0]["r30_paga13"]) {
                                $funcionario_em_ferias = "a";
                            }
                        }
                    }
                }

                // se proporcio;

                if ($funcionario_em_ferias != "n") {

                    $condicaoaux = " and r90_regist = " . db_sqlformat($matric);
                    global $pontofx;
                    db_selectmax("pontofx", "select * from pontofx " . bb_condicaosubpesproc("r90_", $subpes_cont) . $condicaoaux);

                    for ($Ipontofx = 0; $Ipontofx < count($pontofx); $Ipontofx++) {
                        if (db_val($pontofx[$Ipontofx]["r90_rubric"]) > 0 && db_val($pontofx[$Ipontofx]["r90_rubric"]) < 2000) {
                            $condicaoaux = "where rh27_instit = " . db_getsession("DB_instit") . " and rh27_rubric = " . db_sqlformat($pontofx[$Ipontofx]["r90_rubric"]);
                            if (db_selectmax("rubricas", "select * from rhrubricas " . $condicaoaux)
                              && $rubricas[0]["rh27_calc2"] != 0) {
                                $indmes = db_ascan($mes_rubric, $pontofx[$Ipontofx]["r90_rubric"]);

                                if (db_empty($indmes)) {
                                    $maxmes += 1;
                                    $indmes = $maxmes;
                                    $qten_mes[$maxmes] = 0;
                                    $vlrn_mes[$maxmes] = 0;

                                    $mes_rubric[$maxmes] = $pontofx[$Ipontofx]["r90_rubric"];
                                    $mes_valor[$maxmes] = 0;
                                    $mes_quant[$maxmes] = 0;
                                }

                                if ($rubricas[0]["rh27_calc2"] == 9) {
                                    if (!$tem_no_mes_tipo9) {
                                        $mes_valor[$indmes] = $pontofx[$Ipontofx]["r90_valor"];
                                        $mes_quant[$indmes] = $pontofx[$Ipontofx]["r90_quant"];
                                    }
                                } else {
                                    if ($funcionario_em_ferias == "a") {
                                        // vai pegar o mes inteiro (=30dias ferias);
                                        $mes_valor[$indmes] += ($pontofx[$Ipontofx]["r90_valor"]);
                                        $mes_quant[$indmes] += ($pontofx[$Ipontofx]["r90_quant"]);
                                    } else {
                                        $mes_valor[$indmes] += ($pontofx[$Ipontofx]["r90_valor"] / 30 * $dias_de_ferias);
                                        $mes_quant[$indmes] += ($pontofx[$Ipontofx]["r90_quant"] / 30 * $dias_de_ferias);
                                    }
                                    if (db_at(db_str($rubricas[0]["rh27_calc2"], 1), "1-2-7") > 0) {
                                        $vlrn_mes[$indmes] = $pontofx[$Ipontofx]["r90_valor"];
                                        $qten_mes[$indmes] = $pontofx[$Ipontofx]["r90_quant"];
                                    }
                                }
                            }
                        }
                    }
                }
                // avalia a rubrica considerando a soma dos tres arquivos;
                for ($ix = 1; $ix <= $maxmes; $ix++) {

                    // posicionado na rubrica;
                    $condicaoaux = " where rh27_instit = " . db_getsession("DB_instit") . " and rh27_rubric = " . db_sqlformat($mes_rubric[$ix]);
                    db_selectmax("rubricas", "select * from rhrubricas " . $condicaoaux);

                    $iind = db_ascan($m_rubric, $mes_rubric[$ix]);
                    if (db_empty($iind)) {
                        $imax += 1;
                        $iind = $imax;
                        $m_rubric[$imax] = $mes_rubric[$ix];
                        $m_valor[$imax] = 0;
                        $m_quant[$imax] = 0;
                        $m_cont[$imax] = 0;
                        $qten[$imax] = 0;
                        $vlrn[$imax] = 0;
                    }

                    if ($rubricas[0]["rh27_calc2"] == 9) {
                        $m_cont[$iind] += 1;
                        $m_valor[$iind] = $mes_valor[$ix];
                        $m_quant[$iind] = $mes_quant[$ix];
                    } else {

                        if (($mes_quant[$ix] >= (($rubricas[0]["rh27_quant"] > 999 ? $pessoal[$Ipessoal]["r01_hrsmen"] : $rubricas[0]["rh27_quant"]) / 2))) {
                            $m_cont[$iind] += 1;
                        }

                        if (db_at(db_str($rubricas[0]["rh27_calc2"], 1), "3-4-7") > 0) {
                            $quant_restomenos = ($rubricas[0]["rh27_quant"] > 999 ? $pessoal[$Ipessoal]["r01_hrsmen"] : $rubricas[0]["rh27_quant"]);

                            if (!db_empty($quant_restomenos)) {
                                $quant_resto = $mes_quant[$ix] - ($rubricas[0]["rh27_quant"] > 999 ? $pessoal[$Ipessoal]["r01_hrsmen"] : $rubricas[0]["rh27_quant"]);
                                while ($quant_resto > 0) {
                                    if ($quant_resto >= (($rubricas[0]["rh27_quant"] > 999 ? $pessoal[$Ipessoal]["r01_hrsmen"] : $rubricas[0]["rh27_quant"]) / 2)) {
                                        $m_cont[$iind] += 1;
                                        $quant_resto = $quant_resto - ($rubricas[0]["rh27_quant"] > 999 ? $pessoal[$Ipessoal]["r01_hrsmen"] : $rubricas[0]["rh27_quant"]);
                                    } else {
                                        break;
                                    }
                                }
                            } else {
                                $m_cont[$iind] += 1;
                            }
                        }

                        $m_valor[$iind] += $mes_valor[$ix];
                        $m_quant[$iind] += $mes_quant[$ix];
                        if (db_at(db_str($rubricas[0]["rh27_calc2"], 1), "1-2-7") > 0) {
                            $vlrn[$iind] = $vlrn_mes[$ix];
                            $qten[$iind] = $qten_mes[$ix];
                        }
                    }
                }

                $condicaoaux = " and r35_regist = " . db_sqlformat($matric);
                global $gerfs13;

                db_selectmax("gerfs13", "select * from gerfs13 " . bb_condicaosubpesproc("r35_", $subpes_cont) . $condicaoaux);

                for ($Igerfs13 = 0; $Igerfs13 < count($gerfs13); $Igerfs13++) {

                    // verificar se as rubricas de "salario" nao devem ser lidas aqui pois neste;
                    // arquivo pode ser lancado a rubrica r11_rubdec de adiantamento ;

                    if (db_substr($gerfs13[$Igerfs13]["r35_rubric"], 1,
                        1) != "R" && (db_val($gerfs13[$Igerfs13]["r35_rubric"]) > 0 && db_val($gerfs13[$Igerfs13]["r35_rubric"]) > 4000
                        && db_val($gerfs13[$Igerfs13]["r35_rubric"]) < 6000)) {
                        if ($gerfs13[$Igerfs13]["r35_pd"] == 1) {
                            $sal13 += $gerfs13[$Igerfs13]["r35_valor"];
                        }

                        if (!db_empty($cfpess[0]["r11_palime"])) {
                            if ($gerfs13[$Igerfs13]["r35_rubric"] == db_str(db_val($cfpess[0]["r11_palime"]) + 4000, 4, 0)) {
                                $pensao_alim_13 += $gerfs13[$Igerfs13]["r35_valor"];
                            }
                        }
                    }

                    if (!db_empty($cfpess[0]["r11_rubdec"])) {
                        if ($gerfs13[$Igerfs13]["r35_rubric"] == $cfpess[0]["r11_rubdec"]) {
                            $sal13 += $gerfs13[$Igerfs13]["r35_valor"];
                        }
                    }

                    if ($gerfs13[$Igerfs13]["r35_rubric"] == 'R934') {
                        $sal13 -= $gerfs13[$Igerfs13]["r35_valor"];
                    }
                }
            }
        }

        // item inserido em 12/2002 pois em santarosa funcionarios com 12 meses de ;
        // afastamento estao recebendo 1/12 de rbricas tipo 4;
        $considerar_pontofx = true;
        if ($meses_afastado == 12) {
            if ($m_valor[1] + $m_quant[1] + $vlrn[1] + $qten[1] == 0) {
                $considerar_pontofx = false;
            }
        }

        if ($considerar_pontofx) {

            $condicaoaux = " and r90_regist = " . db_sqlformat($pessoal[$Ipessoal]["r01_regist"]);
            global $pontofx;
            db_selectmax("pontofx", "select * from pontofx " . bb_condicaosubpes("r90_") . $condicaoaux);

            for ($Ipontofx = 0; $Ipontofx < count($pontofx); $Ipontofx++) {
                $condicaoaux = " where rh27_instit = " . db_getsession("DB_instit") . " and rh27_rubric = " . db_sqlformat($pontofx[$Ipontofx]["r90_rubric"]);

                if (db_selectmax("rubricas", "select * from rhrubricas " . $condicaoaux) && $rubricas[0]["rh27_calc2"] != 0) {
                    $iind = db_ascan($m_rubric, $pontofx[$Ipontofx]["r90_rubric"]);

                    if (db_empty($iind)) {
                        $imax += 1;
                        $iind = $imax;
                        $m_rubric[$iind] = $pontofx[$Ipontofx]["r90_rubric"];
                        $qten[$iind] = 0;
                        $vlrn[$iind] = 0;
                        $m_cont[$iind] = 1;
                        $m_valor[$iind] = $pontofx[$Ipontofx]["r90_valor"];
                        $m_quant[$iind] = $pontofx[$Ipontofx]["r90_quant"];

                        if (db_at(db_str($rubricas[0]["rh27_calc2"], 1), "1-2-7") > 0) {
                            $qten[$iind] = $pontofx[$Ipontofx]["r90_quant"];
                            $vlrn[$iind] = $pontofx[$Ipontofx]["r90_valor"];
                        }
                    } else {
                        if (db_at(db_str($rubricas[0]["rh27_calc2"], 1), "1-2-7") > 0) {
                            $qten[$iind] = $pontofx[$Ipontofx]["r90_quant"];
                            $vlrn[$iind] = $pontofx[$Ipontofx]["r90_valor"];
                        }
                    }
                }
            }
        }

        $fracao_old = $fracao;

        $dbLog->escreverLog("  => sal13: {$sal13}");

        if (!db_empty($sal13) && $pagaradiantamentonovamente == 't') {
            $matriz2[1] = $matric;
            $matriz2[2] = "R934";
            $matriz2[3] = round($sal13, 2);
            $matriz2[4] = 0;
            $matriz2[5] = $pessoal[$Ipessoal]["r01_lotac"];
            $matriz2[6] = 0;
            $matriz2[7] = 0;
            $matriz2[8] = db_val(db_substr($subpes, 1, 4));
            $matriz2[9] = db_val(db_substr($subpes, -2));
            $matriz2[10] = db_getsession("DB_instit");

            db_insert("pontof13", $matriz1, $matriz2);
        }

        if (!db_empty($pensao_alim_13)) {
            $matriz2[1] = $matric;
            $matriz2[2] = "R980";
            $matriz2[3] = round($pensao_alim_13, 2);
            $matriz2[4] = 0;
            $matriz2[5] = $pessoal[$Ipessoal]["r01_lotac"];
            $matriz2[6] = 0;
            $matriz2[7] = 0;
            $matriz2[8] = db_val(db_substr($subpes, 1, 4));
            $matriz2[9] = db_val(db_substr($subpes, -2));
            $matriz2[10] = db_getsession("DB_instit");

            db_insert("pontof13", $matriz1, $matriz2);
        }

        if (db_empty($sal13) || (!db_empty($sal13) && $pagaradiantamentonovamente == 't')) {
            for ($iind = 1; $iind <= $imax; $iind++) {
                $calc_meses_afas = 0;
                $calcula_fracao_nm13 = false;

                $quant = 0;
                $valor = 0;
                $condicaoaux = " where rh27_instit = " . db_getsession("DB_instit") . " and rh27_rubric = " . db_sqlformat($m_rubric[$iind]);
                if (db_selectmax("rubricas", "select * from rhrubricas " . $condicaoaux)) {
                    $vtipo = db_str($rubricas[0]["rh27_calc2"], 1);
                    $condicaoaux = " and r90_regist = " . db_sqlformat($matric);
                    $condicaoaux .= " and r90_rubric = " . db_sqlformat($m_rubric[$iind]);
                    global $pontofx;

                    if (db_selectmax("pontofx", "select * from pontofx " . bb_condicaosubpes("r90_") . $condicaoaux)) {
                        // rubrica com pgto integral;
                        if (db_at($vtipo, "1-2-7") > 0) {
                            // por exemplo: para pagar inativos que tem a rubrica lancada em valor..;
                            if (!db_empty($rubricas[0]["rh27_form"]) && $pontofx[0]["r90_valor"] > 0) {
                                $valor = $vlrn[$iind];
                                if (db_boolean($rubricas[0]["rh27_calcp"])) {
                                    $calc_meses_afas = $meses_afastado + $meses_admissao;
                                } else {
                                    $fracao = 100;
                                }
                            } else {
                                if (!db_empty($rubricas[0]["rh27_form"])) {
                                    $quant = $qten[$iind];
                                    if (db_boolean($rubricas[0]["rh27_propq"])) {
                                        $calc_meses_afas = $meses_afastado + $meses_admissao;
                                    } else {
                                        $fracao = 100;
                                    }
                                } else {
                                    $valor = $vlrn[$iind];
                                    if (db_boolean($rubricas[0]["rh27_calcp"])) {
                                        $calc_meses_afas = $meses_afastado + $meses_admissao;
                                    } else {
                                        $fracao = 100;
                                    }
                                }
                            }
                        } else {
                            if (db_at($vtipo, "3-4") > 0 && $m_quant[$iind] != 0) {
                                // rubrica com media ao  numero de meses  ;
                                if ($opcao == 2) {
                                    if (!db_empty($rubricas[0]["rh27_form"])) {
                                        $quant = ($rubricas[0]["rh27_quant"] / 12) * $m_cont[$iind];
                                    } else {
                                        $valor = ($rubricas[0]["rh27_quant"] / 12) * $m_cont[$iind];
                                    }
                                } else {
                                    if (!db_empty($rubricas[0]["rh27_form"])) {
                                        $quant = ($rubricas[0]["rh27_quant"] / $nm13) * $m_cont[$iind];
                                    } else {
                                        $valor = ($rubricas[0]["rh27_quant"] / $nm13) * $m_cont[$iind];
                                    }
                                }
                            } else {
                                if (db_at($vtipo, "5-6") > 0) {
                                    // rubrica com pgto pela media encontrada ;
                                    if (!db_empty($rubricas[0]["rh27_form"])) {
                                        $quant += $m_quant[$iind] / 12;
                                    } else {
                                        $valor += $m_valor[$iind] / 12;
                                    }
                                    $calcula_fracao_nm13 = true;
                                } else {
                                    if ($vtipo == "8") {
                                        // rubrica com pgto pela media apenas de valores ;
                                        $valor += $m_valor[$iind] / 12;
                                        $calcula_fracao_nm13 = true;
                                    } else {
                                        if ($vtipo == "9" && $m_quant[$iind] != 0) {
                                            // rubrica com media ao  numero de ocorrencia;
                                            if (!db_empty($rubricas[0]["rh27_form"])) {
                                                $quant = (($pontofx[0]["r90_quant"]) / 12) * $m_cont[$iind];
                                            } else {
                                                $valor = (($pontofx[0]["r90_valor"]) / 12) * $m_cont[$iind];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if ($vtipo == "2") {
                            // rubrica com pgto integral;
                            if (!db_empty($rubricas[0]["rh27_form"])) {
                                $quant = $qten[$iind];
                                if (db_boolean($rubricas[0]["rh27_propq"])) {
                                    $calc_meses_afas = $meses_afastado + $meses_admissao;
                                } else {
                                    $fracao = 100;
                                }
                            } else {
                                $valor = $vlrn[$iind];
                                if (db_boolean($rubricas[0]["rh27_calcp"])) {
                                    $calc_meses_afas = $meses_afastado + $meses_admissao;
                                } else {
                                    $fracao = 100;
                                }
                            }
                        } else {
                            if (db_at($vtipo, "4-7") > 0 && $m_quant[$iind] != 0) {
                                // rubrica com media ao  numero de meses ;
                                if ($opcao == 2) {
                                    if (!db_empty($rubricas[0]["rh27_form"])) {
                                        $quant += (!db_empty($rubricas[0]["rh27_quant"]) ? ($rubricas[0]["rh27_quant"] > 999 ? $pessoal[$Ipessoal]["r01_hrsmen"] : $rubricas[0]["rh27_quant"]) / 12 : ($m_quant[$iind] / $m_cont[$iind]) / 12) * $m_cont[$iind];
                                    } else {
                                        $valor += (($m_valor[$iind] / $m_cont[$iind]) / 12) * $m_cont[$iind];
                                    }
                                } else {
                                    if (!db_empty($rubricas[0]["rh27_form"])) {
                                        $quant += (!db_empty($rubricas[0]["rh27_quant"]) ? ($rubricas[0]["rh27_quant"] > 999 ? $pessoal[$Ipessoal]["r01_hrsmen"] : $rubricas[0]["rh27_quant"]) / $nm13 : ($m_quant[$iind] / $m_cont[$iind]) / $nm13) * $m_cont[$iind];
                                    } else {
                                        $valor += (($m_valor[$iind] / $m_cont[$iind]) / $nm13) * $m_cont[$iind];
                                    }
                                }
                            } else {
                                if ($vtipo == "6") {
                                    // condicao especial para carazinho no que se refere a horas extras;
                                    if ($db21_codcli == "18" && $pessoal[$Ipessoal]["r01_regime"] != 2
                                      && (db_at($rubricas[0]["rh27_rubric"], "0004-0005") > 0)) {
                                        $dividir_por = 6;
                                    } else {
                                        $dividir_por = 12;
                                    }

                                    // rubrica com pgto pela media encontrada ;
                                    if (!db_empty($rubricas[0]["rh27_form"])) {
                                        $quant += $m_quant[$iind] / $dividir_por;
                                    } else {
                                        $valor += $m_valor[$iind] / $dividir_por;
                                    }
                                    $calcula_fracao_nm13 = true;
                                } else {
                                    if ($vtipo == "8") {
                                        // rubrica com pgto pela media apenas de valores;
                                        $valor += $m_valor[$iind] / 12;
                                        $calcula_fracao_nm13 = true;
                                    } else {
                                        if ($vtipo == "9" && $m_quant[$iind] != 0) {
                                            // rubrica com media ao  numero de ocorrencia;
                                            if (!db_empty($rubricas[0]["rh27_form"])) {
                                                $quant = (($m_quant[$iind]) / 12) * $m_cont[$iind];
                                            } else {
                                                $valor = (($m_valor[$iind]) / 12) * $m_cont[$iind];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // fim avaliacao;

                if ($quant > 0 || $valor > 0) {
                    $matriz2[1] = $matric;
                    $matriz2[2] = db_str(db_val($m_rubric[$iind]) + 4000, 4);

                    if ($valor != 0) {
                        if ($calcula_fracao_nm13) {
                            $matriz2[3] = round($valor * ($fracao / 100), 2);
                        } else {
                            if ($opcao == 2) { // saldo
                                $matriz2[3] = round((($valor * (12 - $calc_meses_afas)) / 12) * ($fracao / 100), 2);
                            } else { // Adiantamento
                                $matriz2[3] = round((($valor * ($nm13 - $calc_meses_afas)) / $nm13) * ($fracao / 100),
                                  2);
                            }
                        }
                        $matriz2[4] = 0;
                    } else {
                        $matriz2[3] = 0;
                        if ($calcula_fracao_nm13) {
                            $matriz2[4] = round($quant * ($fracao / 100), 2);
                        } else {
                            if ($opcao == 2) { // saldo 13
                                $matriz2[4] = round((($quant * (12 - $calc_meses_afas)) / 12) * ($fracao / 100), 2);
                            } else { // Adiantamento
                                $matriz2[4] = round((($quant * ($nm13 - $calc_meses_afas)) / $nm13) * ($fracao / 100), 2);
                            }
                        }
                    }

                    $matriz2[5] = $pessoal[$Ipessoal]["r01_lotac"];
                    $matriz2[6] = 0;
                    $matriz2[7] = 0;
                    $matriz2[8] = db_val(db_substr($subpes, 1, 4));
                    $matriz2[9] = db_val(db_substr($subpes, -2));
                    $matriz2[10] = db_getsession("DB_instit");

                    if ($matriz2[4] + $matriz2[3] > 0) {
                        db_insert("pontof13", $matriz1, $matriz2);
                    }
                }
                $fracao = $fracao_old;
            }
            if ($opcao == 2) {
                $dados[] = [
                        'instituicao' => $instituicao,
                        'data_decimo_13' => $data_decimo_13,
                        'ano' => $ano,
                        'matriculas' => $pessoal[$Ipessoal]["r01_regist"]
                    ];
                DecimoServidorService::inserir($dados);
            }
        }

        $dbLog->escreverLog('');
    }

    $dbLog->escreverLog('');
    $dbLog->escreverLog('### FIM ###');
}

function calc_13($data)
{

    global $pessoal, $Ipessoal;

    if (db_year($pessoal[$Ipessoal]["r01_admiss"]) < db_year($data)) {
        $nro_meses = 12;
    } else {

        if (db_day($pessoal[$Ipessoal]["r01_admiss"]) > 15) {
            $nro_meses = db_month($data) - db_month($pessoal[$Ipessoal]["r01_admiss"]);
        } else {
            $nro_meses = (db_month($data) + 1) - db_month($pessoal[$Ipessoal]["r01_admiss"]);
        }
    }

    return $nro_meses;
}

function calcula_afastamentos()
{
    global $afasta, $pessoal, $subpes, $cfpess, $Ipessoal;

//echo "<BR> subpes --> $subpes";

    $retorno = 0;

    $condicaoaux = " and r45_regist =" . db_sqlformat($pessoal[$Ipessoal]["r01_regist"]) . " order by r45_regist, r45_dtafas desc";
    if (db_selectmax("afasta", "select * from afasta " . bb_condicaosubpes("r45_") . $condicaoaux)) {

        for ($Iafasta = 0; $Iafasta < count($afasta); $Iafasta++) {
            if (db_str($afasta[$Iafasta]["r45_situac"],
                1) == "3" && $pessoal[$Ipessoal]["r01_tbprev"] != $cfpess[0]["r11_tbprev"]) {
//echo "<BR> 1 passou aqui !!";
                continue;
            }

            if ((!db_empty($afasta[$Iafasta]["r45_dtreto"])) && db_year($afasta[$Iafasta]["r45_dtreto"]) <= db_val(db_substr($subpes,
                1, 4))) {
                if (db_year($afasta[$Iafasta]["r45_dtreto"]) < db_val(db_substr($subpes, 1, 4))) {
//echo "<BR> 2 passou aqui !!";
                    break;
                }
            } else {
                $mes = 12;
//echo "<BR> 3 passou aqui !! mes --> $mes";
            }
            if (db_year($afasta[$Iafasta]["r45_dtreto"]) > db_val(db_substr($subpes, 1, 4))) {
                $mes = 12;
//echo "<BR> 4 passou aqui !! mes --> $mes";
            } else {
                if (!db_empty($afasta[$Iafasta]["r45_dtreto"])) {
                    $mes = db_month($afasta[$Iafasta]["r45_dtreto"]);
//echo "<BR> 5 passou aqui !! mes --> $mes";
                }
            }
            if (db_val(db_substr($subpes, -2)) < $mes) {
                $mes = db_val(db_substr($subpes, -2));
                $mes++;
                $ano = (db_empty($afasta[$Iafasta]["r45_dtreto"]) ? db_val(db_substr($subpes, 1,
                  4)) : db_year($afasta[$Iafasta]["r45_dtreto"]));
//echo "<BR> 6 passou aqui !!  mes --> $mes ano --> $ano";
                if ($mes > 12) {
                    $mes = 1;
                    $ano++;
//echo "<BR> 7 passou aqui !!  mes --> $mes ano --> $ano";
                }

                // ultimo dia do mes anterior
                $dias_mes = db_day(date("Y-m-d",
                  db_mktime(db_ctod("01/" . db_str($mes, 2, 0, "0") . "/" . db_str($ano, 4, 0, "0"))) - 86400));
                $mes--;

//echo "<BR> 8 passou aqui !!  mes --> $mes dias_mes --> $dias_mes";
                $diasmes = ndias(db_str($mes, 2, 0, "0") . "/" . db_str($ano, 4, 0, "0"));

//echo "<BR> 9 passou aqui !!  mes --> $mes dias_mes --> $dias_mes";
            } else {
                $ano = ((db_empty($afasta[$Iafasta]["r45_dtreto"]) || db_year($afasta[$Iafasta]["r45_dtreto"]) > db_val(db_substr($subpes,
                    1, 4))) ? db_val(db_substr($subpes, 1, 4))
                  : db_year($afasta[$Iafasta]["r45_dtreto"]));
                $datafinal = ((db_empty($afasta[$Iafasta]["r45_dtreto"]) || db_year($afasta[$Iafasta]["r45_dtreto"]) > db_val(db_substr($subpes,
                    1, 4)))
                  ? db_ctod("31/12/" . db_substr($subpes, 1, 4)) : $afasta[$Iafasta]["r45_dtreto"]);

//echo "<BR> 10 passou aqui !!  ano --> $ano datafinal --> $datafinal";
                // retorna o nr de dias trabalhados no mes????
                // $dias_mes = ceil(((db_mktime($datafinal)+ 86400) - db_mktime(db_ctod("01/".db_str($mes,2,0,"0")."/".db_str($ano,4,0,"0")))/86400));
                $dias_mes = db_datedif($datafinal,
                  db_ctod("01/" . db_str($mes, 2, 0, "0") . "/" . db_str($ano, 4, 0, "0")));

//echo "<BR> 11 passou aqui !!  dias_mes --> $dias_mes";
                $diasmes = ndias(db_str(db_month($datafinal), 2, 0, "0") . "/" . db_str(db_year($datafinal), 4, 0,
                    "0"));
//echo "<BR> 12 passou aqui !!  dias_mes --> $diasmes";

            }

            $metade_trabalhado = bcdiv($diasmes, 2, 0);

//echo "<BR> 13 passou aqui !!  metade_trabalhado --> $metade_trabalhado";
//echo "<BR> 13.1 passou aqui  retorno --> $retorno";
//echo "<BR> if( $diasmes - $dias_mes < $metade_trabalhado){";

            if ($diasmes - $dias_mes < $metade_trabalhado) {
                $retorno += 1;
//echo "<BR> 13.2 passou aqui  retorno --> $retorno";
            }

            if (db_month($afasta[$Iafasta]["r45_dtafas"]) == $mes && db_year($afasta[$Iafasta]["r45_dtafas"]) == $ano) {

                if ($diasmes - $dias_mes < $metade_trabalhado) {
                    $dias_mes = $dias_mes - (db_day($afasta[$Iafasta]["r45_dtafas"]) - 1);

//echo "<BR> 15 passou aqui !!  dias_mes --> $dias_mes";
                    if ($diasmes - $dias_mes >= $metade_trabalhado) {
                        $retorno--;
//echo "<BR> 16 passou aqui !! retorno --> $retorno";
                    }
                }
            } else {
                $mes--;

//echo "<BR> 17 passou aqui mes--> $mes ";
                for ($Imes = $mes; $Imes >= 1; $Imes--) {
                    if (db_mktime($afasta[$Iafasta]["r45_dtafas"]) < db_mktime(db_ctod("01/" . db_str($Imes, 2, 0,
                          "0") . "/" . db_substr($subpes, 1, 4)))) {
                        $retorno += 1;
//echo "<BR> 18 passou aqui !! retorno --> $retorno";
                    } else {
                        $dias_mes = db_datedif(db_ctod("01/" . db_str($Imes, 2, 0, "0") . "/" . db_str($ano, 4)),
                            $afasta[$Iafasta]["r45_dtafas"]) - 1;
                        //// timestamp da primeira data
                        $data_time1 = strtotime($afasta[$Iafasta]["r45_dtafas"]);
                        $data_time2 = strtotime(db_ctod("01/" . db_str($Imes, 2, 0, "0") . "/" . db_str($ano, 4)));
                        $dif_entre_time1_time2 = int((strtotime($afasta[$Iafasta]["r45_dtafas"]) - strtotime(db_ctod("01/" . db_str($Imes,
                                2, 0, "0") . "/" . db_str($ano, 4)))) / (60 * 60 * 24));
                        $dias_mes = $dif_entre_time1_time2 + 1;

//echo "<br> 19.1 stamp afasta ".  date('Y-m-d',strtotime($afasta[$Iafasta]["r45_dtafas"]));
//echo "<br> 19.1 stamp 01     ".  date('Y-m-d',strtotime(db_ctod("01/".db_str($Imes,2,0,"0")."/".db_str($ano,4))))."<br>";
//echo "<br> 19.1 diferenca    ".  int((strtotime($afasta[$Iafasta]["r45_dtafas"]) - strtotime(db_ctod("01/".db_str($Imes,2,0,"0")."/".db_str($ano,4)))) / (60 * 60 * 24)) ;

                        $diasmes = ndias(db_str($Imes, 2, 0, "0") . "/" . db_str($ano, 4, 0, "0"));
//echo "<BR> 19 passou aqui !!  Imes --> $Imes  ano --> $ano    diasmes($diasmes) - dias_mes($dias_mes) > metade_trabalhado($metade_trabalhado)      dias_mes --> $dias_mes";
                        if ($diasmes - $dias_mes > $metade_trabalhado) {
                            $retorno += 1;
//echo "<BR> 20 passou aqui !!  retorno --> $retorno";
                        }
                        break;
                    }
                }
            }
        }

        if ($retorno < 0) {
            $retorno = 0;
        }
    }

    return $retorno;
}

?>
