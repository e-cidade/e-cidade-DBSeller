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

//use ECidade\Tributario\Cadastro\Iptu\CalculoRetroativo\Repository\CalculoRetroativoIptuRepository;

// phpcs:disable
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
// phpcs:enable

db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);

if (isset($y60_proces) && !empty($y60_proces)) {
    db_query("select fc_putsession('PROCESSO_LOG', '$y60_proces')");
    $_SESSION['PROCESSO_LOG'] = $y60_proces;
}

if (isset($_SESSION['PROCESSO_LOG'])) {
    db_query("select fc_putsession('PROCESSO_LOG', '" . db_getsession('PROCESSO_LOG') . "')");
} else {
    db_query("select fc_putsession('PROCESSO_LOG', '0')");
}

$anousu = db_getsession("DB_anousu");
$anoRetroativoMatricula = db_getsession("DB_anoRetroativoMatricula", false);

/*
Comentado este trecho de código, até fazermos o nivelamento de fontes com São Borja


$calculoRetroativoIptuRepository = CalculoRetroativoIptuRepository::getInstance();

$calculoRetroativoIptuRepository->setAnousu($anousu)
    ->setAnoRetroativoMatricula($anoRetroativoMatricula);

$anosSchemaCopiado = $calculoRetroativoIptuRepository->getAllAnosSchema(false);
$liberaCalculoRetroativo = $calculoRetroativoIptuRepository->getLiberaCalculoRetroativo();

$calculoRetroativoIptuRepository->getAlteraSearchPath();
*/

$liberaCalculoRetroativo = false;
if (!$liberaCalculoRetroativo || empty($anoRetroativoMatricula) || $anoRetroativoMatricula == null) {
    $anoRetroativoMatricula = $anousu;
}

$cllotedist        = new cl_lotedist;
$clface            = new cl_face;
$cllote            = new cl_lote;
$clloteloc         = new cl_loteloc;
$clloteam          = new cl_loteam;
$clloteloteam      = new cl_loteloteam;
$clcarlote         = new cl_carlote;
$cltestada         = new cl_testada;
$cltestpri         = new cl_testpri;
$cliptubase        = new cl_iptubase;
$clsetor           = new cl_setor;
$cltestadanumero   = new cl_testadanumero;
$cllotesetorfiscal = new cl_lotesetorfiscal;
$clcfiptu          = new cl_cfiptu;
$cltesinterlote    = new cl_tesinterlote;
$cltesinteroutros  = new cl_tesinteroutros;
$cltesinter        = new cl_tesinter;
$clcaracter        = new cl_caracter;
$cliptuant         = new cl_iptuant;

$parametrosNumeroCadastral = new App\Domain\Tributario\Cadastro\Models\ParametrosNumeroCadastral();
if ($parametrosNumeroCadastral->existeParametro(db_getsession("DB_instit"))) {
    $db_opcaorefant = 3;
}

$cllotedist->rotulo->label();
$clloteam->rotulo->tlabel();
$clcarlote->rotulo->tlabel();
$cllote->rotulo->label();
$cltestada->rotulo->tlabel();

$cltestadanumero->rotulo->tlabel();

$cllotedist->rotulo->tlabel();
$clrotulo = new rotulocampo;
$clrotulo->label("j30_descr");
$clrotulo->label("j13_descr");
$clrotulo->label("j14_nome");
$clrotulo->label("j34_loteam");
$clrotulo->label("j34_descr");
$cllotedist->rotulo->tlabel();
$cllotesetorfiscal->rotulo->label();
$clrotulo->label("j90_descr");
$clrotulo->label("j34_setor");
$clrotulo->label("j34_areapreservada");
$cllotedist->rotulo->tlabel();
$trans_erro = false;

$tipoImovel = isset($tipoImovel) ? $tipoImovel : 1;

if ($tipoImovel == "2") {
    $j34_idbql = 1000000000;
} else {
    if (empty($j34_idbql)) {
        $j34_idbql = null;
    }
}

// TO-DO: Ver como tratar include para poder retirar o phpcs
// phpcs:disable
/**
 * Atualiza o IPTUBASE da matrícula informada e atualiza a área total da construção do lote das demais matrículas
 */
function atualizarIptuBase($idmatricu, $j34_idbql, $cliptubase)
{
    $erro = false;
    //Busca todas as matrículas diferentes da atual que estão vinculadas ao lote
    $whereIptuBase = "j01_idbql in (select j01_idbql from iptubase where j01_matric = {$idmatricu})";
    $whereIptuBase .= "  and j01_matric <> {$idmatricu}";
    $sqlIptuBase = $cliptubase->sql_query_file(null, "j01_matric", null, $whereIptuBase);
    $rsIptuBase = db_query($sqlIptuBase);

    if (!$rsIptuBase) {
        db_msgbox("Erro ao buscar as matrículas vinculadas ao lote.");
        $erro = true;
    }

    $cliptubase->j01_idbql = $j34_idbql;
    $cliptubase->j01_matric = $idmatricu;
    $cliptubase->alterar($idmatricu);

    $totalIdbqlAnterior = pg_num_rows($rsIptuBase);
    // Update para executar a trigger que atualiza o campo j34_totcon da tabela lote
    $daoIptuconstr = new cl_iptuconstr();
    for ($index = 0; $index < $totalIdbqlAnterior; $index++) {
        $matricula = db_utils::fieldsMemory($rsIptuBase, $index)->j01_matric;
        $daoIptuconstr->j39_matric = $matricula;
        $daoIptuconstr->alterar($matricula);

        if ($daoIptuconstr->erro_status == 0) {
            db_msgbox("Erro ao atualizar o valor da área total de construção da matrícula {$matricula}.");
            $erro = true;
        }
    }
    return $erro;
}
// phpcs:enable

$anoUtilizatSetFiscal = db_getsession('DB_anousu');

if ($liberaCalculoRetroativo) {
    $anoUtilizatSetFiscal = $anoRetroativoMatricula;
}

$rsResultmostra = ($clcfiptu->sql_record($clcfiptu->sql_query_file($anoUtilizatSetFiscal, 'j18_utilizasetfisc, j18_testadanumero', "", "")));
if ($clcfiptu->numrows > 0) {
    db_fieldsmemory($rsResultmostra, 0);
    $mostrasetfiscal = $j18_utilizasetfisc;
    $numerotestada = $j18_testadanumero;
}

if (isset($incluquadra) && $incluquadra != "") {
    $resulta = $clsetor->sql_record($clsetor->sql_query($j34_setor, "j30_descr"));
    db_fieldsmemory($resulta, 0);
    $db_opcao = $incluquadra;
} else {
    $db_opcao = 1;
}
$db_botao = true;
$selface = false;
$testasetor = false;
$replote = false;

if (isset($j01_matric)) {
    $idmatricu = $j01_matric;
}
if (isset($incluir) || isset($alterar)) {
    $mesmo = false;
    $result = $cllote->sql_record(
        $cllote->sql_query(
            "",
            "j34_idbql as tidbql",
            "",
            "j34_setor= '$j34_setor' and j34_quadra='$j34_quadra' and j34_lote='$j34_lote'"
        )
    );
    $numrows = $cllote->numrows;
    if ($result != false && $numrows != 0) {
        if (isset($alterar)) {
            for ($xi = 0; $xi < $numrows; $xi++) {
                db_fieldsmemory($result, $xi);
                if ($j34_idbql == $tidbql) {
                    $mesmo = true;
                    break;
                }
            }
        }
        if ($mesmo == false) {
            $replote = true;
            if (isset($incluir)) {
                unset($incluir);
                $repete = "incluir";
                $db_opcao = 1;
            } else {
                unset($alterar);
                $repete = "alterar";
                $db_opcao = 2;
            }
        }
    }
}
if (isset($outrolote) && $outrolote != "") {
    $$outrolote = "ok";
}
if ($replote == true) {
} elseif (isset($j34_setor) && !isset($incluir) && !isset($alterar)) {
    $resultface = $clface->sql_record($clface->sql_query("", "distinct j37_quadra", "", "j37_setor='$j34_setor'"));
    $clface->numrows == 0;
    $selface = true;
} elseif (isset($incluir)) {
    if ($liberaCalculoRetroativo) {
        $sequences = array();

        for ($anoMatricula = $anoRetroativoMatricula; $anoMatricula <= $anousu; $anoMatricula++) {
            $schema = "";
            if ($anoMatricula < $anousu) {
                $schema = "_{$anoMatricula}";
            }

            $result = db_query("SELECT last_value AS sequence FROM cadastro{$schema}.lote_j34_idbql_seq;");
            $sequences[$anoMatricula] = \db_utils::fieldsMemory($result, 0)->sequence;
        }

        $sequence = intval(max($sequences)) + 1;
    }

    for ($anoMatricula = $anoRetroativoMatricula; $anoMatricula <= $anousu; $anoMatricula++) {
        if ($anoMatricula == $anoRetroativoMatricula) {
            db_query("select fc_putsession('DB_anoretroativo', '{$anoRetroativoMatricula} À {$anousu}')");
        }

        if ($liberaCalculoRetroativo) {
            $calculoRetroativoIptuRepository->setAnoRetroativoMatricula($anoMatricula);
            $calculoRetroativoIptuRepository->getAlteraSearchPath(true);
            db_query("SELECT setval('lote_j34_idbql_seq', {$sequence}, FALSE);");
        }

        db_inicio_transacao();
        $j34_lote = str_pad($j34_lote, 4, "0", STR_PAD_LEFT);
        $cllote->j34_lote = $j34_lote;
        $cllote->j34_areapreservada = $j34_areapreservada;
        $cllote->j34_areaprivativa = $j34_areaprivativa;
        $cllote->j34_areausocomum = $j34_areausocomum;
        $cllote->j34_zonadesurb = $j225_sequencial;
        if ($cllote->incluir(null) == true) {
            $j34_idbql = $cllote->j34_idbql;

            if ($idmatricu != "") {
                $trans_erro = atualizarIptuBase($idmatricu, $j34_idbql, $cliptubase);
            }

            if ($j34_loteam != "") {
                $result = $clloteam->sql_record($clloteam->sql_query($j34_loteam, "j34_loteam"));
                $numrows = $clloteam->numrows;
                if ($numrows >= 1) {
                    $clloteloteam->j34_idbql = $j34_idbql;
                    $clloteloteam->j34_loteam = $j34_loteam;
                    $clloteloteam->incluir($j34_idbql, $j34_loteam);
                }
            }
        }

        /*============ TESTADAS INTERNAS ============== */

        $matriztesinter = explode("X", $testadainter);

        foreach ($matriztesinter as $valor) {
            $dadosTestadaInterna = explode("-", $valor);
            $idbqlInterLote = $dadosTestadaInterna[0];
            $j39_idbql = $cllote->j34_idbql;
            $j39_orientacao = (isset($dadosTestadaInterna[1]) ? $dadosTestadaInterna[1] : "");
            $j39_testad = (isset($dadosTestadaInterna[2]) ? $dadosTestadaInterna[2] : "0");
            $j39_testle = (isset($dadosTestadaInterna[3]) ? $dadosTestadaInterna[3] : "0");
            $j84_tesintertipo = (isset($dadosTestadaInterna[4]) ? $dadosTestadaInterna[4] : "");
            $j84_observacao = (isset($dadosTestadaInterna[5]) ? $dadosTestadaInterna[5] : "");

            if (((isset($idbqlInterLote) && $idbqlInterLote != 0 && $idbqlInterLote != "") ||
                (isset($j84_tesintertipo) && $j84_tesintertipo != 0 && $j84_tesintertipo != ""))) {
                if (!isset($j39_orientacao) || $j39_orientacao == "") {
                    $j39_orientacao = 0;
                }

                $cltesinter->j39_idbql = $j39_idbql;
                $cltesinter->j39_orientacao = $j39_orientacao;
                $cltesinter->j39_testad = $j39_testad;
                $cltesinter->j39_testle = $j39_testle;
                $cltesinter->incluir(null);
                if ($cltesinter->erro_status == 0) {
                    db_msgbox("TESINTER INC1: " . $cltesinter->erro_msg);
                    $trans_erro = true;
                }

                if (isset($idbqlInterLote) && $idbqlInterLote != 0) {
                    $cltesinterlote->j69_tesinter = $cltesinter->j39_sequencial;
                    $cltesinterlote->j69_idbql = $idbqlInterLote;
                    $cltesinterlote->incluir($cltesinter->j39_sequencial);
                    if ($cltesinterlote->erro_status == 0) {
                        db_msgbox("TESINTERLOTE INC1:" . $cltesinterlote->erro_msg);
                        $trans_erro = true;
                    }
                } elseif (isset($j84_tesintertipo) && $j84_tesintertipo != '0') {
                    $cltesinteroutros->j84_tesintertipo = $j84_tesintertipo;
                    $cltesinteroutros->j84_tesinter = $cltesinter->j39_sequencial;
                    $cltesinteroutros->j84_observacao = (isset($j84_observacao))?$j84_observacao:'';
                    $cltesinteroutros->incluir();
                    if ($cltesinteroutros->erro_status == 0) {
                        db_msgbox("TESINTEROUTROS INC1:" . $cltesinteroutros->erro_msg);
                        $trans_erro = true;
                    }
                }
            }
        }

        //=============================================

        $resultado = db_query("select * from face where j37_face = $cartestpri");
        $j37_codigo = pg_result($resultado, 0, 3);
        $cltestpri->j49_face = $cartestpri;
        $cltestpri->j49_codigo = $j37_codigo;

        $cltestpri->incluir($cllote->j34_idbql, $cartestpri);
        $matriztesta = explode("x", $cartestada);
        for ($i = 0; $i < sizeof($matriztesta); $i++) {
            $dados = $matriztesta[$i];
            $matrizdados = explode("||", $dados);

            $j37_face = $matrizdados[0];
            $j14_codigo = $matrizdados[1];
            $j36_testad = $matrizdados[2];
            $j36_testle = $matrizdados[3];

            //==============================================================
            $j15_numero = $matrizdados[4];
            $j15_compl = $matrizdados[5];
            $j36_orientacao = $matrizdados[6];

            //==============================================================
            if ($j36_testad != "0" || $j36_testle != "0") {
                $cltestada->j36_idbql = $cllote->j34_idbql;
                $cltestada->j36_face = $j37_face;
                $cltestada->j36_codigo = $j14_codigo;
                $cltestada->j36_testad = $j36_testad;
                $cltestada->j36_testle = $j36_testle;
                $cltestada->j36_orientacao = $j36_orientacao;
                $cltestada->incluir($cllote->j34_idbql, $j37_face);
            }
            if ($cltestada->erro_status == "0") {
                $trans_erro = true;
                db_msgbox($cltestada->erro_msg);
            }

            //INCLUSAO NA TABELA TESTADANUMERO
            if ($trans_erro == false and isset($numerotestada) && $numerotestada == 't') {
                if ((isset($j15_numero) && $j15_numero != "") || (isset($j15_compl) && $j15_compl != "")) {
                    $cltestadanumero->j15_idbql = $cllote->j34_idbql;
                    $cltestadanumero->j15_face = $j37_face;
                    $cltestadanumero->j15_compl = $j15_compl;
                    $cltestadanumero->j15_numero = $j15_numero;
                    $cltestadanumero->incluir("");
                    if ($cltestadanumero->erro_status == "0") {
                        $trans_erro = true;
                        db_msgbox("testadanumero" . $cltestadanumero->erro_msg);
                    }
                }
            }
        }

        //INCLUSAO NA TABELA CARLOTE

        $j34_idbql = $cllote->j34_idbql;
        $clcarlote->j35_idbql = $j34_idbql;
        $matriz = explode("X", $caracteristica);
        for ($i = 0; $i < sizeof($matriz); $i++) {
            $j35_caract = $matriz[$i];
            if ($j35_caract != "") {
                $clcarlote->j35_dtlanc = date("Y-m-d", db_getsession("DB_datausu"));
                $clcarlote->incluir($j34_idbql, $j35_caract);
                if ($clcarlote->erro_status == "0") {
                    $trans_erro = true;
                    db_msgbox("carlote" . $clcarlote->erro_msg);
                }
            }
        }

        //INCLUSAO NA TABELA LOTEDIST

        if ($j54_codigo != "" && $j54_distan != "") {
            $cllotedist->j54_idbql = $cllote->j34_idbql;
            $cllotedist->j54_codigo = $j54_codigo;
            $cllotedist->j54_distan = str_replace(",", ".", $j54_distan);
            $cllotedist->j54_orientacao = $j54_orientacao;
            $cllotedist->incluir($j34_idbql);
            if ($cllotedist->erro_status == "0") {
                $trans_erro = true;
                db_msgbox("lotedist" . $cllotedist->erro_msg);
            }
        }

        //INCLUSAO NA TABELA LOTESETORFISCAL

        if (isset($j91_codigo) && $j91_codigo != "") {
            $cllotesetorfiscal->j91_idbql = $cllote->j34_idbql;
            $cllotesetorfiscal->j91_codigo = $j91_codigo;
            $cllotesetorfiscal->incluir($cllote->j34_idbql);
            if ($cllotesetorfiscal->erro_status == "0") {
                $trans_erro = true;
            }
        }

        //INCLUSAO NA TABELA LOTELOC

        if (isset($j06_setorloc) && $j06_setorloc != "") {
            $clloteloc->j06_idbql = $cllote->j34_idbql;
            $clloteloc->j06_setorloc = $j06_setorloc;
            $clloteloc->j06_quadraloc = $j06_quadraloc;
            $clloteloc->j06_lote = $j06_lote;
            $clloteloc->incluir($cllote->j34_idbql);
            if ($clloteloc->erro_status == "0") {
                $trans_erro = true;
            }
        }

        db_fim_transacao($trans_erro);

        if ($anoMatricula == $anoRetroativoMatricula) {
            db_query("select fc_delsession('DB_anoretroativo')");
        }
    } //Fim do For

    $_SESSION["lote"] = $j34_lote;

    $_SESSION['PROCESSO_LOG'] = $y60_proces;
} elseif (isset($alterar)) {
    $anoLimiteReplicaDados = (isset($anoLimiteReplicaDados) ? $anoLimiteReplicaDados : $anoRetroativoMatricula);

    if ($liberaCalculoRetroativo) {
        $sequences = array();

        for ($anoMatricula = $anoRetroativoMatricula; $anoMatricula <= $anousu; $anoMatricula++) {
            $schema = "";
            if ($anoMatricula < $anousu) {
                $schema = "_{$anoMatricula}";
            }

            $result = db_query("SELECT last_value AS sequence FROM cadastro{$schema}.tesinter_j39_sequencial_seq;");
            $sequences[$anoMatricula] = \db_utils::fieldsMemory($result, 0)->sequence;
        }

        $sequence = intval(max($sequences)) + 1;
    }

    for ($anoMatricula = intval($anoRetroativoMatricula); $anoMatricula <= $anoLimiteReplicaDados; $anoMatricula++) {
        if ($anoMatricula == $anoRetroativoMatricula) {
            if (empty($anoLimiteReplicaDados)) {
                db_query("select fc_putsession('DB_anoretroativo', '{$anoRetroativoMatricula}')");
            } else {
                db_query("select fc_putsession('DB_anoretroativo',
                    '{$anoRetroativoMatricula} À {$anoLimiteReplicaDados}')");
            }
        }

        if ($liberaCalculoRetroativo) {
            $calculoRetroativoIptuRepository->setAnoRetroativoMatricula($anoMatricula);
            $calculoRetroativoIptuRepository->getAlteraSearchPath(true);
            db_query("SELECT setval('tesinter_j39_sequencial_seq', {$sequence}, FALSE);");
        }

        $sqlerro = false;
        db_inicio_transacao();

        if ($j34_loteam != "") {
            $result = $clloteloteam->sql_record(
                $clloteloteam->sql_query_file(
                    "",
                    "",
                    "loteloteam.j34_loteam as loteam",
                    "",
                    "loteloteam.j34_idbql=$j34_idbql"
                )
            );
            $numrows = $clloteloteam->numrows;
            if ($numrows > 0) {
                db_fieldsmemory($result, 0);
                if ($j34_loteam != $loteam) {
                    $result = $clloteam->sql_record($clloteam->sql_query($j34_loteam, "j34_loteam"));
                    $numrows = $clloteam->numrows;
                    if ($numrows > 0) {
                        $clloteloteam->j34_idbql = $j34_idbql;
                        $clloteloteam->j34_loteam = $j34_loteam;
                        $clloteloteam->alterar($j34_idbql, $loteam);
                        if ($clloteloteam->erro_status == 0) {
                            $sqlerro = true;
                        }
                    }
                }
            } else {
                $result = $clloteam->sql_record($clloteam->sql_query($j34_loteam));
                $numrows = $clloteam->numrows;
                if ($numrows > 0) {
                    $clloteloteam->j34_idbql = $j34_idbql;
                    $clloteloteam->j34_loteam = $j34_loteam;
                    $clloteloteam->incluir($j34_idbql, $j34_loteam);
                    if ($clloteloteam->erro_status == 0) {
                        $sqlerro = true;
                    }
                }
            }
        } else {
            $result = $clloteloteam->sql_record(
                $clloteloteam->sql_query_file(
                    "",
                    "",
                    "loteloteam.j34_loteam as loteam",
                    "",
                    "loteloteam.j34_idbql=$j34_idbql"
                )
            );
            $numrows = $clloteloteam->numrows;
            if ($numrows > 0) {
                db_fieldsmemory($result, 0);
                $clloteloteam->j34_idbql = $j34_idbql;
                $clloteloteam->j34_loteam = $loteam;
                $clloteloteam->excluir($j34_idbql);
                if ($clloteloteam->erro_status == 0) {
                    $sqlerro = true;
                }
            }
        }

        if ($idmatricu != "") {
            $cliptubase->j01_idbql = $j34_idbql;
            $cliptubase->j01_matric = $idmatricu;
            $cliptubase->alterar($idmatricu);
        }

        $j34_lote = str_pad($j34_lote, 4, "0", STR_PAD_LEFT);
        $cllote->j34_lote = $j34_lote;
        $cllote->j34_areapreservada = $j34_areapreservada;
        $cllote->j34_areaprivativa = $j34_areaprivativa;
        $cllote->j34_areausocomum = $j34_areausocomum;
        $cllote->j34_zonadesurb = $j225_sequencial;
        $cllote->alterar($j34_idbql);
        if ($cllote->erro_status == 0) {
            db_msgbox($cllote->erro_msg);
            $sqlerro = true;
        }

        //==============================================================================================================
        if ($cllote->erro_status == 1) {
            $rsCarLote = $clcarlote->sql_record($clcarlote->sql_query_file($j34_idbql));
            $iClcarloteNumrows = $clcarlote->numrows;
            $xx = $clcarlote->numrows;

            /*========================================================================================================*/

            $matriztesinterdel = explode("X", $testadainter);
            $matriztesinterdel = array_unique($matriztesinterdel);

            $sListaTesinterDel = "";
            $virgula = "";

            if (count($matriztesinterdel) >= 1 && $testadainter != "") {
                foreach ($matriztesinterdel as $valor) {
                    $dadosTestadaInternaDel = explode("-", $valor);
                    if (!empty($dadosTestadaInternaDel[6])
                        && !empty($dadosTestadaInternaDel[7])) { //indicie 6 estado 1=excluído, indice 7 sequencial
                            $sListaTesinterDel .= $virgula . $dadosTestadaInternaDel[7];
                            $virgula = ",";
                    }
                }
            }

            $sWhereTesinter = "1=2"; // Nao excluir quem nao possuir sequencial

            if (!empty($sListaTesinterDel)) {
                $sWhereTesinter = "j39_sequencial in ({$sListaTesinterDel}) and j39_idbql = $j34_idbql";
            }

            if ($sqlerro == false) {
                $rsTesinterlote = $cltesinter->sql_record(
                    $cltesinter->sql_query_file(
                        null,
                        'j39_sequencial',
                        null,
                        "$sWhereTesinter"
                    )
                );
                $intNumrows = $cltesinter->numrows;
                for ($intTes = 0; $intTes < $intNumrows; $intTes++) {
                    db_fieldsmemory($rsTesinterlote, $intTes);

                    $cltesinteroutros->excluir("j84_tesinter = {$j39_sequencial}");
                    if ($cltesinteroutros->erro_status == 0) {
                        db_msgbox("TESINTEROUTROS EXCLUSAO : " . $cltesinteroutros->erro_msg);
                        $trans_erro = true;
                    }

                    $cltesinterlote->excluir($j39_sequencial);
                    if ($cltesinterlote->erro_status == 0) {
                        db_msgbox("TESINTERLOTE EXCLUSAO : " . $cltesinterlote->erro_msg);
                        $trans_erro = true;
                    }

                    $cltesinter->excluir($j39_sequencial);
                    if ($cltesinter->erro_status == 0) {
                        db_msgbox("TESINTER EXCLUSAO: " . $cltesinter->erro_msg);
                        $trans_erro = true;
                    }
                }
            }

            /*============ TESTADAS INTERNAS ============== */

            $matriztesinter = explode("X", $testadainter);
            $matriztesinter = array_unique($matriztesinter);

            if (count($matriztesinter) >= 1 && $testadainter != "") {
                foreach ($matriztesinter as $valor) {
                    $dadosTestadaInterna = explode("-", $valor);
                    $idbqlInterLote = $dadosTestadaInterna[0];
                    $j39_idbql = $cllote->j34_idbql;
                    $j39_orientacao = $dadosTestadaInterna[1];
                    $j39_testad = (isset($dadosTestadaInterna[2]) ? $dadosTestadaInterna[2] : "0");
                    $j39_testle = (isset($dadosTestadaInterna[3]) ? $dadosTestadaInterna[3] : "0");
                    $j84_tesintertipo = $dadosTestadaInterna[4];
                    $j84_observacao = (isset($dadosTestadaInterna[5]) ? $dadosTestadaInterna[5] : "");
                    $tesinter_excluida = (isset($dadosTestadaInterna[6]) ? $dadosTestadaInterna[6] : "0");
                    $j39_sequencial = $dadosTestadaInterna[7];

                    //
                    // Teste adicional para verificar se o seq existe em exercios de replicação
                    $tesinterExiste = false;
                    if ($j39_sequencial != "" && $j39_sequencial != "0" && $tesinter_excluida != "1") {
                        //se não foi excluída verifica alteracoes
                        $sCampos = "j39_idbql      as verifica_idbql,
                                    j39_orientacao as verifica_orientacao,
                                    j39_testad     as verifica_testad,
                                    j39_testle     as verifica_testle,
                                    j39_sequencial";
                        $sSqlTesinter = $cltesinter->sql_query_file($j39_sequencial, $sCampos);
                        $rsTesinter = $cltesinter->sql_record($sSqlTesinter);

                        if ($cltesinter->numrows > 0) {
                            db_fieldsmemory($rsTesinter, 0);
                            $tesinterExiste = true;
                        }
                    }

                    if ((isset($idbqlInterLote) && $idbqlInterLote != '0') ||
                        (isset($j84_tesintertipo) && $j84_tesintertipo != '0')) {
                        if ($j39_sequencial != "" && $j39_sequencial != "0" && $tesinterExiste == true
                            && $tesinter_excluida != "1") { //se não foi excluída verifica alteracoes
                            if ($verifica_idbql != $j39_idbql ||
                                $verifica_orientacao != $j39_orientacao ||
                                $verifica_testad != $j39_testad ||
                                $verifica_testle != $j39_testle
                            ) {
                                $cltesinter->j39_idbql = $j39_idbql;
                                $cltesinter->j39_orientacao = $j39_orientacao;
                                $cltesinter->j39_testad = $j39_testad;
                                $cltesinter->j39_testle = $j39_testle;
                                $cltesinter->j39_sequencial = $j39_sequencial;
                                $cltesinter->alterar($j39_sequencial);
                            }

                            $sqlTesinterlote = $cltesinterlote->sql_query_file($j39_sequencial, "j69_idbql as verifica_j69_idbql");
                            $rsTesinterlote = $cltesinterlote->sql_record($sqlTesinterlote);
                            if ($cltesinterlote->numrows > 0) {
                                db_fieldsmemory($rsTesinterlote, 0);

                                if ($verifica_j69_idbql != $idbqlInterLote) {
                                    $cltesinterlote->j69_tesinter = $j39_sequencial;
                                    $cltesinterlote->j69_idbql = $idbqlInterLote;
                                    $cltesinterlote->alterar($j39_sequencial);
                                }
                            }

                            $sCampos = "j84_tesintertipo as verifica_j84_tesintertipo,
                                        j84_observacao as verifica_j84_observacao";
                            $sqlTesinteroutros = $cltesinteroutros->sql_query_file($sCampos, null, "j84_tesinter = $j39_sequencial");
                            $rsTesinteroutros = $cltesinteroutros->sql_record($sqlTesinteroutros);
                            if ($cltesinteroutros->numrows > 0) {
                                db_fieldsmemory($rsTesinteroutros, 0);

                                $alterou_outros = false;
                                if ($verifica_j84_tesintertipo != $j84_tesintertipo) {
                                    $cltesinteroutros->j84_tesintertipo = $j84_tesintertipo;
                                    $alterou_outros = true;
                                }

                                if ($verifica_j84_observacao != $j84_observacao) {
                                    $cltesinteroutros->j84_observacao = $j84_observacao;
                                    $alterou_outros = true;
                                }

                                if ($alterou_outros) {
                                    $cltesinteroutros->j84_tesinter = $j39_sequencial;
                                    $cltesinteroutros->alterar($oid_tesinteroutros);
                                }
                            }
                        } else {
                            if (((isset($idbqlInterLote) && $idbqlInterLote != 0 && $idbqlInterLote != "") ||
                                (isset($j84_tesintertipo) && $j84_tesintertipo != 0 && $j84_tesintertipo != ""))
                                && $tesinterExiste == false && $tesinter_excluida != "1") {
                                $j39_idbql = $cllote->j34_idbql;
                                $j39_orientacao = $dadosTestadaInterna[1];
                                $j39_testad = (isset($dadosTestadaInterna[2]) ? $dadosTestadaInterna[2] : "0");
                                $j39_testle = (isset($dadosTestadaInterna[3]) ? $dadosTestadaInterna[3] : "0");
                                $j84_tesintertipo = $dadosTestadaInterna[4];
                                $j84_observacao = (isset($dadosTestadaInterna[5]) ? $dadosTestadaInterna[5] : "");
                                $tesinter_excluida = (isset($dadosTestadaInterna[6]) ? $dadosTestadaInterna[6] : "0");
                                $j39_sequencial = $dadosTestadaInterna[7];

                                if (!empty($j39_sequencial)) {
                                    //se não existe no exercício mas existe na matriz então replica
                                    if (!isset($j39_orientacao) || $j39_orientacao == "") {
                                        $j39_orientacao = 0;
                                    }

                                    $cltesinter->j39_idbql = $j39_idbql;
                                    $cltesinter->j39_orientacao = $j39_orientacao;
                                    $cltesinter->j39_testad = $j39_testad;
                                    $cltesinter->j39_testle = $j39_testle;
                                    $cltesinter->j39_sequencial = $j39_sequencial;
                                    $cltesinter->incluir($j39_sequencial);
                                    if ($cltesinter->erro_status == 0) {
                                        db_msgbox("TESINTER INC2: " . $cltesinter->erro_msg);
                                        $trans_erro = true;
                                    }

                                    if (isset($idbqlInterLote) && $idbqlInterLote != 0) {
                                        $cltesinterlote->j69_tesinter = $cltesinter->j39_sequencial;
                                        $cltesinterlote->j69_idbql = $idbqlInterLote;
                                        $cltesinterlote->incluir($cltesinter->j39_sequencial);
                                        if ($cltesinterlote->erro_status == 0) {
                                            db_msgbox("TESINTERLOTE INC2:" . $cltesinterlote->erro_msg);
                                            $trans_erro = true;
                                        }
                                    } elseif (isset($j84_tesintertipo) && $j84_tesintertipo != '0') {
                                        $cltesinteroutros->j84_tesintertipo = $j84_tesintertipo;
                                        $cltesinteroutros->j84_observacao = (isset($j84_observacao))?$j84_observacao:'';
                                        $cltesinteroutros->j84_tesinter = $cltesinter->j39_sequencial;
                                        $cltesinteroutros->incluir();
                                        if ($cltesinteroutros->erro_status == 0) {
                                            db_msgbox("TESINTEROUTROS INC2:" . $cltesinteroutros->erro_msg);
                                            $trans_erro = true;
                                        }
                                    }
                                } else {
                                    if (!isset($j39_orientacao) || $j39_orientacao == "") {
                                        $j39_orientacao = 0;
                                    }

                                    $cltesinter->j39_idbql = $j39_idbql;
                                    $cltesinter->j39_orientacao = $j39_orientacao;
                                    $cltesinter->j39_testad = $j39_testad;
                                    $cltesinter->j39_testle = $j39_testle;
                                    $cltesinter->j39_sequencial = null;
                                    $cltesinter->incluir(null);
                                    if ($cltesinter->erro_status == 0) {
                                        db_msgbox("TESINTER INC3: " . $cltesinter->erro_msg);
                                        $trans_erro = true;
                                    }

                                    if (isset($idbqlInterLote) && $idbqlInterLote != 0) {
                                        $cltesinterlote->j69_tesinter = $cltesinter->j39_sequencial;
                                        $cltesinterlote->j69_idbql = $idbqlInterLote;
                                        $cltesinterlote->incluir($cltesinter->j39_sequencial);
                                        if ($cltesinterlote->erro_status == 0) {
                                            db_msgbox("TESINTERLOTE INC3:" . $cltesinterlote->erro_msg);
                                            $trans_erro = true;
                                        }
                                    } elseif (isset($j84_tesintertipo) && $j84_tesintertipo != '0') {
                                        $cltesinteroutros->j84_tesintertipo = $j84_tesintertipo;
                                        $cltesinteroutros->j84_tesinter = $cltesinter->j39_sequencial;
                                        $cltesinteroutros->j84_observacao=(isset($j84_observacao))?$j84_observacao:'';
                                        $cltesinteroutros->incluir();
                                        if ($cltesinteroutros->erro_status == 0) {
                                            db_msgbox("TESINTEROUTROS INC3:" . $cltesinteroutros->erro_msg);
                                            $trans_erro = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //=============================================

            $result = $clface->sql_record($clface->sql_query_file("", "j37_codigo", "", "j37_face=$cartestpri"));
            $num = $clface->numrows;

            if ($num != 0) {
                db_fieldsmemory($result, 0);
                $cltestpri->j49_face = $cartestpri;
                $cltestpri->j49_codigo = $j37_codigo;
                $cltestpri->j49_idbql = $j34_idbql;

                $GLOBALS["HTTP_POST_VARS"]["j49_face"] = $cartestpri;
                $GLOBALS["HTTP_POST_VARS"]["j49_codigo"] = $j37_codigo;
                $GLOBALS["HTTP_POST_VARS"]["j49_idbql"] = $j34_idbql;

                $rsTestpri = $cltestpri->sql_record($cltestpri->sql_query_file($j34_idbql, null, "j49_face as tpface"));
                if ($cltestpri->numrows > 0) {
                    db_fieldsmemory($rsTestpri, 0);
                    $cltestpri->alterar($j34_idbql);
                } else {
                    $cltestpri->incluir($j34_idbql, $cartestpri);
                }
                if ($cltestpri->erro_status == 0) {
                    $sqlerro = true;
                }
            }

            $matriztesta = explode("x", $cartestada);
            $sobraTestada1 = array();
            $sobraTestada2 = array();

            for ($i = 0; $i < sizeof($matriztesta); $i++) {
                $dados = $matriztesta[$i];
                $matrizdados = explode("||", $dados);
                $j37_face = $matrizdados[0];
                $j14_codigo = $matrizdados[1];
                $j36_testad = $matrizdados[2];
                $j36_testle = $matrizdados[3];

                //==============================================================
                $j15_numero = $matrizdados[4];
                $j15_compl = $matrizdados[5];
                $j36_orientacao = $matrizdados[6];
                //==============================================================

                if ($j36_testad != "0" && $j36_testad != "") {
                    $sobraTestada1[] = $j37_face;

                    if ($sqlerro == false) {
                        $cltestada->j36_idbql = $cllote->j34_idbql;
                        $cltestada->j36_face = $j37_face;
                        $cltestada->j36_codigo = $j14_codigo;
                        $cltestada->j36_testad = $j36_testad;
                        $cltestada->j36_testle = $j36_testle;
                        $cltestada->j36_orientacao = $j36_orientacao;

                        $GLOBALS["HTTP_POST_VARS"]["j36_idbql"] = $cllote->j34_idbql; // Para account funcionar
                        $GLOBALS["HTTP_POST_VARS"]["j36_face"] = $j37_face; // Para account funcionar
                        $GLOBALS["HTTP_POST_VARS"]["j36_codigo"] = $j14_codigo; // Para account funcionar
                        $GLOBALS["HTTP_POST_VARS"]["j36_testad"] = $j36_testad; // Para account funcionar
                        $GLOBALS["HTTP_POST_VARS"]["j36_testle"] = $j36_testle; // Para account funcionar
                        $GLOBALS["HTTP_POST_VARS"]["j36_orientacao"] = $j36_testle; // Para account funcionar

                        $rsVt = $cltestada->sql_record($cltestada->sql_query_file($cllote->j34_idbql, $j37_face, "j36_codigo"));
                        if ($cltestada->numrows > 0) {
                            $cltestada->alterar($cllote->j34_idbql, $j37_face);
                        } else {
                            $cltestada->incluir($cllote->j34_idbql, $j37_face);
                        }

                        if ($cltestada->erro_status == 0) {
                            db_msgbox("Erro ao incluir Testada: " . $cltestada->erro_msg);
                            $sqlerro = true;
                        }
                    }

                    if (isset($numerotestada) && $numerotestada == 't') {
                        if ((isset($j15_numero) && $j15_numero != "") || (isset($j15_compl) && $j15_compl != "")) {
                            $sobraTestada2[] = $j37_face;

                            if ($sqlerro == false) {
                                $cltestadanumero->j15_idbql = $cllote->j34_idbql;
                                $cltestadanumero->j15_face = $j37_face;
                                $cltestadanumero->j15_compl = $j15_compl;
                                $cltestadanumero->j15_numero = $j15_numero;

                                $GLOBALS["HTTP_POST_VARS"]["j15_idbql"] = $cllote->j34_idbql;
                                $GLOBALS["HTTP_POST_VARS"]["j15_face"] = $j37_face;
                                $GLOBALS["HTTP_POST_VARS"]["j15_compl"] = $j15_compl;
                                $GLOBALS["HTTP_POST_VARS"]["j15_numero"] = $j15_numero;

                                $sqlTN = $cltestadanumero->sql_query(
                                    null,
                                    "j15_codigo",
                                    null,
                                    "j15_idbql = $cllote->j34_idbql AND testada.j36_face = $j37_face"
                                );
                                $rsTN = $cltestadanumero->sql_record($sqlTN);

                                if ($cltestadanumero->numrows > 0) {
                                    db_fieldsmemory($rsTN, 0);

                                    $cltestadanumero->j15_codigo = $j15_codigo;
                                    $cltestadanumero->alterar($j15_codigo);
                                    $cltestadanumero->j15_codigo = 0;

                                    if ($cltestadanumero->erro_status == 0) {
                                        $sqlerro = true;
                                        $erro_msg = $cltestadanumero->erro_msg;
                                    }
                                } else {
                                    $cltestadanumero->incluir("");
                                }

                                if ($cltestadanumero->erro_status == 0) {
                                    $sqlerro = true;
                                }
                            }
                        }
                    }
                }
            }

            $sobraTestada1 = implode(',', $sobraTestada1);
            $sobraTestada2 = implode(',', $sobraTestada2);

            if ($sobraTestada2 != '') {
                // Quando é alteração no mesmo setor/quadra existirá registros (sem alterar face de quadra)
                if ($sqlerro == false) {
                    $sqlTnD = $cltestadanumero->sql_query(
                        null,
                        "j15_codigo",
                        null,
                        "j15_idbql = " . $cllote->j34_idbql . " AND testada.j36_face not in (" . $sobraTestada2 . ")"
                    );
                    $result2 = $cltestadanumero->sql_record($sqlTnD);
                    $tnrows = $cltestadanumero->numrows;
                    for ($itn = 0; $itn < $tnrows; $itn++) {
                        db_fieldsmemory($result2, $itn);
                        $cltestadanumero->excluir($j15_codigo);
                        if ($cltestadanumero->erro_status == 0) {
                            $erro_msg = $cltestadanumero->erro_msg;
                            $sqlerro = true;
                            break;
                        }
                    }
                }
            } else { // Quando mudou setor/quadra (alterou a face de quadra)
                $resultt = $cltestadanumero->sql_record(
                    $cltestadanumero->sql_query(
                        null,
                        "*",
                        null,
                        " j15_idbql = $j34_idbql "
                    )
                );
                $xxx = $cltestadanumero->numrows;
                if ((isset($numerotestada) && $numerotestada == 't'
                    && (isset($matriztesta) && $matriztesta != "")) || $xxx > 0) {
                    $sqlerro = false;
                    for ($i = 0; $i < $xxx; $i++) {
                        db_fieldsmemory($resultt, $i);
                        $cltestadanumero->sql_record(
                            $cltestadanumero->sql_query(
                                null,
                                "*",
                                null,
                                "j15_idbql = $j36_idbql"
                            )
                        );
                        $numrowstestadanumero = $cltestadanumero->numrows;
                        if ($numrowstestadanumero > 0) {
                            $cltestadanumero->j15_idbql = $j15_idbql;
                            $cltestadanumero->j15_face = $j15_face;
                            $cltestadanumero->excluir("", " j15_idbql = $j36_idbql and j15_face = $j15_face ");
                            if ($cltestadanumero->erro_status == 0) {
                                $sqlerro = true;
                                $erro_msg = $cltestadanumero->erro_msg;
                                break;
                            }
                        }
                    }
                }
            }

            if ($sqlerro == false) {
                $sCampos = "j36_idbql as didbql, j36_face as dface";
                $sWhere = "j36_idbql = {$cllote->j34_idbql} and j36_face not in (" . $sobraTestada1 . ")";
                $result1 = $cltestada->sql_record($cltestada->sql_query_file(null, null, $sCampos, null, $sWhere));
                $txx = $cltestada->numrows;
                for ($it = 0; $it < $txx; $it++) {
                    db_fieldsmemory($result1, $it);
                    $cltestada->j36_idbql = $didbql;
                    $cltestada->j36_face = $dface;
                    $cltestada->excluir($didbql, $dface);
                    if ($cltestada->erro_status == 0) {
                        $erro_msg = $cltestada->erro_msg;
                        $sqlerro = true;
                        break;
                    }
                }
            }

            // altera caracteristicas de lote
            //============================================================================
            $j34_idbql = $cllote->j34_idbql;
            $clcarlote->j35_idbql = $j34_idbql;
            $matriz = explode("X", $caracteristica);

            for ($i = 1; $i < sizeof($matriz); $i++) {
                if ($matriz[$i] != '') {
                    $sqlCaracter = $clcaracter->sql_query_file($matriz[$i], "j31_grupo");
                    $rsCaracter = $clcaracter->sql_record($sqlCaracter);
                    db_fieldsmemory($rsCaracter, 0);

                    $sWhere = 'j35_idbql = ' . $j34_idbql . ' and j31_grupo = ' . $j31_grupo;
                    $sqlCarlote = $clcarlote->sql_query(null, null, "j35_caract as caract", null, $sWhere);
                    $rsCarlote = $clcarlote->sql_record($sqlCarlote);

                    $clcarlote->j35_idbql = $j34_idbql;
                    if ($clcarlote->numrows > 0) {
                        db_fieldsmemory($rsCarlote, 0);
                        $clcarlote->j35_caract = $matriz[$i];
                        $clcarlote->alterar($j34_idbql, $caract);
                        if ($clcarlote->erro_status == 0) {
                            $sqlerro = true;
                        }
                    } else {
                        $clcarlote->j35_caract = $matriz[$i];
                        $clcarlote->incluir($j34_idbql, $matriz[$i]);
                        if ($clcarlote->erro_status == 0) {
                            $sqlerro = true;
                        }
                    }
                }

                $j35_caract = $matriz[$i];

                if (isset($j54_codigo) && isset($j54_distan) && isset($j54_orientacao)) {
                    if ($j54_codigo != "" && $j54_distan != "") {
                        $rLoteDist = db_query($cllotedist->sql_query_file($j34_idbql));

                        $cllotedist->j54_idbql = $j34_idbql;
                        $cllotedist->j54_codigo = $j54_codigo;
                        $cllotedist->j54_distan = str_replace(",", ".", $j54_distan);
                        $cllotedist->j54_orientacao = $j54_orientacao;

                        if (pg_num_rows($rLoteDist) > 0) {
                            $cllotedist->alterar($j34_idbql);
                        } else {
                            $cllotedist->incluir($j34_idbql);
                        }

                        if ($cllotedist->erro_status == 0) {
                            db_msgbox("LOTEDIST : " . $cllotedist->erro_msg);
                            $sqlerro = true;
                        }
                    }
                }
            }
            $matriz = implode(',', $matriz);
            $matriz = substr($matriz, 1);
            $matriz = substr($matriz, 0, -1);
            if ($matriz) {
                $sWhere = 'j35_caract not in (' . $matriz . ') and j35_idbql = ' . $j34_idbql;
                $sqlExclusao = $clcarlote->sql_query(null, null, "j35_idbql, j35_caract", null, $sWhere);
                $rsExclusao = $clcarlote->sql_record($sqlExclusao);
                $numRowsExclusao = $clcarlote->numrows;
                if ($clcarlote->numrows > 0) {
                    for ($x = 0; $x < $numRowsExclusao; $x++) {
                        $result = db_utils::fieldsMemory($rsExclusao, $x);
                        $clcarlote->j35_idbql = $result->j35_idbql;
                        $clcarlote->j35_caract = $result->j35_caract;
                        $clcarlote->excluir($result->j35_idbql, $result->j35_caract);
                        if ($clcarlote->erro_status == 0) {
                            $sqlerro = true;
                        }
                    }

                }
            }

            //ALTERACAO  NA TABELA LOTESETORFISCAL
            if (isset($mostrasetfiscal) && $mostrasetfiscal == 't') {
                $sqlGetSetorFiscal = $cllotesetorfiscal->sql_query(
                    null,
                    "j91_codigo",
                    null,
                    "j91_idbql = " . $cllote->j34_idbql
                );
                $resultGetSetorFiscal = $cllotesetorfiscal->sql_record($sqlGetSetorFiscal);
                $numlinesGetSetorFiscal = pg_num_rows($resultGetSetorFiscal);
                if ((isset($j91_codigo) && $j91_codigo != "") && $numlinesGetSetorFiscal > 0) {
                    $cllotesetorfiscal->j91_idbql = $cllote->j34_idbql;
                    $cllotesetorfiscal->j91_codigo = $j91_codigo;
                    $cllotesetorfiscal->alterar($cllote->j34_idbql);
                } elseif (isset($j91_codigo) && $j91_codigo != "") {
                    $cllotesetorfiscal->j91_idbql = $cllote->j34_idbql;
                    $cllotesetorfiscal->j91_codigo = $j91_codigo;
                    $cllotesetorfiscal->incluir($cllote->j34_idbql);
                }
                if ($cllotesetorfiscal->erro_status == "0") {
                    $trans_erro = true;
                }
            }

            //ALTERACAO NA TABELA LOTELOC
            if (isset($j06_setorloc) && $j06_setorloc != "") {
                $clloteloc->j06_idbql = $cllote->j34_idbql;
                $result = $clloteloc->sql_record($clloteloc->sql_query($cllote->j34_idbql));
                if ($clloteloc->numrows > 0) {
                    $clloteloc->alterar($cllote->j34_idbql);
                } else {
                    $clloteloc->incluir($cllote->j34_idbql);
                }
            }
            //============================================
        }

        if (isParaiba() && isset($idmatricu)) {
            $resultFace = $clface->sql_record($clface->sql_query($cartestpri, '*'));
            if ($resultFace && $clface->numrows > 0) {
                db_fieldsmemory($resultFace, 0);
            }

            $result = $cliptuant->sql_record($cliptuant->sql_query($idmatricu));
            if ($result != false && $cliptuant->numrows > 0) {
                db_fieldsmemory($result, 0);
            }

            $numeroCadastral = $parametrosNumeroCadastral->montaNumero(db_getsession("DB_instit"), $idmatricu);

            if (isset($j37_sequencia)) {
                $numeroCadastral = substr_replace($numeroCadastral, $j37_sequencia, 13 - strlen($j37_sequencia), strlen($j37_sequencia));
            }

            $cliptuant->j40_refant = $numeroCadastral;
            $cliptuant->j40_registrocartografico = $j40_registrocartografico;
            $cliptuant->alterar($idmatricu);
        }

        db_fim_transacao($sqlerro);
        $db_opcao = 2;

        if ($anoMatricula == $anoRetroativoMatricula) {
            db_query("select fc_delsession('DB_anoretroativo')");
        }
    }
} elseif (isset($j34_idbql) || isset($alterando) || isset($chavepesquisa) && !isset($incluquadra)) {
    if (isset($chavepesquisa)) {
        $j34_idbql = $chavepesquisa;
    }
    if (isset($_SESSION['PROCESSO_LOG'])) {
        $y60_proces = $_SESSION['PROCESSO_LOG'];
    }
    if (isset($alterando)) {
        $result = $cliptubase->sql_record($cliptubase->sql_query("", "j01_idbql", "", "j01_matric=$j01_matric"));
        db_fieldsmemory($result, 0);

        $result = $cllote->sql_record($cllote->sql_query($j01_idbql, "j34_idbql", "", ""));
        if ($result && pg_num_rows($result) > 0) {
            db_fieldsmemory($result, 0);
        }
    }
    //setor fiscal =====================================================================================================
    $rsResultsetfis = db_query(
        $cllotesetorfiscal->sql_query(
            "",
            "j91_codigo,
            j90_descr",
            "",
            " j91_idbql = $j34_idbql"
        )
    );
    if (pg_num_rows($rsResultsetfis) != 0) {
        db_fieldsmemory($rsResultsetfis, 0);
        $j91_codigo = $j91_codigo;
    }
    //==================================================================================================================

    $testadainter = null;
    $carX = "";
    $sqlTesinter = " select coalesce(j69_idbql,0) as idbql, ";
    $sqlTesinter .= "				 j39_orientacao as orientacao, ";
    $sqlTesinter .= "				 coalesce(j84_tesintertipo, 0) as j84_tesintertipo, ";
    $sqlTesinter .= "				 j39_testad as testad, ";
    $sqlTesinter .= "				 j39_testle as testle, ";
    $sqlTesinter .= "				 coalesce(j84_observacao, '') as j84_observacao, ";
    $sqlTesinter .= "				 0 as tesinter_excluida, ";
    $sqlTesinter .= "				 j39_sequencial ";
    $sqlTesinter .= "		from tesinter ";
    $sqlTesinter .= " 	     left join tesinterlote   on j69_tesinter = j39_sequencial ";
    $sqlTesinter .= " 	     left join tesinteroutros on j84_tesinter = j39_sequencial ";
    $sqlTesinter .= " where j39_idbql = $j34_idbql ";

    $rsTestadaInter = $cltesinter->sql_record($sqlTesinter);

    for ($i = 0; $i < $cltesinter->numrows; $i++) {
        db_fieldsmemory($rsTestadaInter, $i);
        $testadainter .= $carX . $idbql . "-" . $orientacao . "-" . $testad . "-" . $testle . "-" . $j84_tesintertipo .
            "-" . $j84_observacao . "-" . $tesinter_excluida . "-" . $j39_sequencial;
        $carX = "X";
    }

    $result = $cllote->sql_record($cllote->sql_query($j34_idbql));
    db_fieldsmemory($result, 0);

    $testasetor = true;

    $result = $clloteloteam->sql_record(
        $clloteloteam->sql_query(
            "",
            "",
            "loteloteam.j34_loteam,loteam.j34_descr",
            "",
            "loteloteam.j34_idbql=$j34_idbql"
        )
    );
    $numrows = $clloteloteam->numrows;
    if ($result > 0) {
        db_fieldsmemory($result, 0);
    }

    $result = $cllotedist->sql_record($cllotedist->sql_query($j34_idbql));
    if ($cllotedist->numrows != 0) {
        db_fieldsmemory($result, 0);
    } else {
        $j54_codigo = "";
        $j54_distan = "";
        $j54_orientacao = "";
        $j14_nome = "";
    }
    $result = $cltestpri->sql_record($cltestpri->sql_query_file($j34_idbql));

    if ($result) {
        db_fieldsmemory($result, 0);
        $cartestpri = $j49_face;
    }
    $result = $cltestada->sql_record($cltestada->sql_query_file($j34_idbql));
    $cartestada = null;
    $cart = "";

    for ($i = 0; $i < $cltestada->numrows; $i++) {
        db_fieldsmemory($result, $i);

        $sqlTestadaNumero = " select coalesce(
            ( select j15_numero from testadanumero where j15_idbql=$j34_idbql and j15_face=$j36_face ),0) as j15_numero,
            case
              when (select j15_compl
                     from testadanumero where j15_idbql=$j34_idbql and j15_face=$j36_face ) is null then '0'
              else (select j15_compl
                      from testadanumero where j15_idbql=$j34_idbql and j15_face=$j36_face )
            end as j15_compl";

        $rsTestadanumero = db_query($sqlTestadaNumero);
        db_fieldsmemory($rsTestadanumero, 0);
        $cartestada .= $cart . $j36_face . "||" . $j36_codigo . "||" . $j36_testad . "||" . $j36_testle .
            "||" . $j15_numero . "||" . $j15_compl . "||" . $j36_orientacao;
        $cart = "x  ";
    }

    $result = $clcarlote->sql_record($clcarlote->sql_query($j34_idbql));
    $caracteristica = null;
    $car = "X";
    for ($i = 0; $i < $clcarlote->numrows; $i++) {
        db_fieldsmemory($result, $i);
        $caracteristica .= $car . $j35_caract;
        $car = "X";
    }
    $caracteristica .= $car;
    $db_opcao = 2;

    $db_botao = true;
}

if (isset($j34_areapreservada) && $j34_areapreservada == "") {
    $j34_areapreservada = 0;
}

if (isset($j34_setor) && $j34_setor == "") {
    $j30_descr = "ss";
}
?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>

    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style type="text/css">
      #j34_quadra, #j54_orientacao {
        width:92px;
      }

        #selectAnosMatricula {
            width: 95px;
            height: 10px;
            border: 1px solid #999999;
        }
    </style>
    <?php
      db_app::load("scripts.js");
      db_app::load("prototype.js");
      db_app::load("widgets/Input/DBInput.widget.js");
      db_app::load("widgets/Input/DBInputValor.widget.js");
    ?>
  </head>
  <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"
    onLoad="parent.document.getElementById('load').style.visibility='hidden'">
    <form id="Lote" name="form1" method="post" action="" onSubmit="return js_verifica_campos_digitados();">
       <table width="790" align="center" border="0" cellspacing="0" cellpadding="0">

            <tr>
            <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
                <input type="hidden" name="outrolote">
                <center>
              <?php  require_once(modification("forms/db_frmlotealt.php")); ?>
                </center>
            </td>
            </tr>

        </table>
    </form>
  </body>
</html>
<script>
new DBInputValor($('j54_distan'));
</script>
<?php

if ($replote == true) {
    echo "<script>";
    if ($repete == "incluir") {
        echo "var confirma=confirm('Este Lote já foi cadastrado!  Deseja cadastrar outro?');";
    } else {
        echo "var confirma=confirm('Este Lote já foi cadastrado!  Deseja continuar a alteração?');";
    }
    echo "if(confirma){\n
		         document.form1.outrolote.value='$repete'; \n
		         document.form1.submit(); \n
		       }\n
		      ";
    echo "</script>";
    exit;
}
if (isset($incluir) || isset($alterar)) {
    if ($cllote->erro_status == "0") {
        $cllote->erro(true, false);
        if ($cllote->erro_campo != "") {
            echo "<script> document.form1." . $cllote->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $cllote->erro_campo . ".focus();</script>";
        }
    } else {
        $cllote->erro(true, false);
        echo "<script>
				         parent.document.form1.idlote.value='" . $j34_idbql . "'; \n
				         parent.document.form1.idsetor.value='" . $j34_setor . "'; \n
				         parent.document.form1.idquadra.value='" . $j34_quadra . "'; \n
				         parent.js_parentiframe('lote',true);

				         </script>
				        ";
        if (isset($incluir)) {
            echo "<script>parent.js_novamatric(true);</script>";
        }
    }
}
if (isset($chavepesquisa) && !isset($incluquadra) || $tipoImovel == 2) {
    if (isset($idmatricu) && $idmatricu != "") {
        db_inicio_transacao();
        $erro = atualizarIptuBase($idmatricu, $j34_idbql, $cliptubase);
        db_fim_transacao($erro);
    }
    echo "<script>
		         parent.document.form1.idlote.value='" . $j34_idbql . "'; \n
		         parent.document.form1.idsetor.value='" . $j34_setor . "'; \n
		         parent.document.form1.idquadra.value='" . $j34_quadra . "'; \n
		         parent.js_parentiframe('lote',true);
		         </script>
		        ";
}
if (isset($alterando) || isset($novolote)) {
    echo "<script>
		         parent.document.form1.idsetor.value='" . $j34_setor . "'; \n
		         parent.document.form1.idquadra.value='" . $j34_quadra . "';";

    echo " parent.js_parentiframe('alterando',true);
		          </script>
		        ";
}
?>
