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

//use ECidade\Tributario\Cadastro\Iptu\CalculoRetroativo\Repository\CalculoRetroativoIptuRepository;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("classes/db_iptuconstr_ext_classe.php"));
require_once(modification("classes/db_iptuconstr_classe.php"));
require_once(modification("classes/db_carconstr_classe.php"));
require_once(modification("classes/db_constrcar_classe.php"));
require_once(modification("classes/db_iptucale_classe.php"));
require_once(modification("classes/db_constrescr_classe.php"));
require_once(modification("classes/db_issmatric_classe.php"));
require_once(modification("classes/db_iptuconstrdemo_classe.php"));
require_once(modification("classes/db_iptuconstrpontos_classe.php"));
require_once(modification("classes/db_iptuconstrobrasconstr_classe.php"));
require_once(modification("classes/db_protprocesso_classe.php"));
require_once(modification("classes/db_cfiptu_classe.php"));

if (!isset($replicaDadosAnos)) {
    $replicaDadosAnos = 0;
}
$anousu = db_getsession("DB_anousu");
$anoRetroativoMatricula = db_getsession("DB_anoRetroativoMatricula", false);
$iAnousu = (($replicaDadosAnos == 0 and isset($alterando)) ? $anoRetroativoMatricula : $anousu);

/*
$calculoRetroativoIptuRepository = CalculoRetroativoIptuRepository::getInstance();

$calculoRetroativoIptuRepository->setAnousu($anousu)
                                ->setAnoRetroativoMatricula($anoRetroativoMatricula);

$liberaCalculoRetroativo = $calculoRetroativoIptuRepository->getLiberaCalculoRetroativo();
*/

$liberaCalculoRetroativo = false;
if (!$liberaCalculoRetroativo || empty($anoRetroativoMatricula)) {
    $anoRetroativoMatricula = $anousu;
}

if (!isset($anoMatricula) or empty($anoMatricula)) {
    $anoMatricula = $anousu;
}

//$calculoRetroativoIptuRepository->getAlteraSearchPath();

db_postmemory($HTTP_POST_VARS);

parse_str($HTTP_SERVER_VARS['QUERY_STRING'], $queryString);

$oDaoIPTUConstr = new cl_iptuconstr_extends;
$oDaoIPTUBase = new cl_iptubase;
$oDaoCarConstr = new cl_carconstr;
$oDaoCfIPTU = new cl_cfiptu;
$oDaoIPTUConstrPontos = new cl_iptuconstrpontos;
$oDaoProtProcesso = new cl_protprocesso;
$oDaoIPTUConstrObrasConstr = new cl_iptuconstrobrasconstr;
$oDaoCaracter = new cl_caracter;

$oDaoIPTUConstr->rotulo->label();
$oDaoIPTUConstr->rotulo->tlabel();

$oRotulo = new rotulocampo;
$oRotulo->label("j14_nome");
$oRotulo->label("z01_nome");
$oRotulo->label("j83_pontos");
$oRotulo->label("p58_requer");
$oRotulo->label("j107_areacomumedificada");
$oRotulo->label("j107_areagaragem");

$db_botao = 1;
$db_opcaoid = 1;
$db_opcao = 1;
$testasel = false;
$lSqlErro = false;

/**
 * Variável utilizada no formulario para validar o limite de area da coinstrução se estiver vinculada a uma obra
 */
$sOnChangeArea = "";

$oPost = db_utils::postMemory($_POST);
$oGet = db_utils::postMemory($_GET);

/**
 * Modificadas variaveis da data
 * Originais preservadas abaixo
 *
 * $j39_dtlan_dia = date("d");
 * $j39_dtlan_mes = date("m");
 * $j39_dtlan_ano = date("Y");
 */

$j39_dtlan_dia = "";
$j39_dtlan_mes = "";
$j39_dtlan_ano = "";

$lProcesso = "S";

if (isset($alterando)) {

    $j39_matric = $j01_matric;
    $result = $oDaoIPTUBase->sql_record($oDaoIPTUBase->sql_query($j39_matric, "z01_nome", ""));
    db_fieldsmemory($result, 0);
}

/**
 * Nova construção.....
 */
if (isset($j39_idcons) && $j39_idcons == "nova" && empty($incluir)) {

    $result = $oDaoIPTUBase->sql_record($oDaoIPTUBase->sql_query($j39_matric, "z01_nome"));
    db_fieldsmemory($result, 0);
    $j39_idcons = "";

    /**
     * Rotina de Inclusão
     */
} elseif (isset($incluir)) {
    if (empty($j39_idcons)) {
        if ($liberaCalculoRetroativo) {
            $arrayIdCons = array();

            for ($anoMatricula = $anoRetroativoMatricula; $anoMatricula <= $iAnousu; $anoMatricula++) {
                $calculoRetroativoIptuRepository->setAnoRetroativoMatricula($anoMatricula);
                $calculoRetroativoIptuRepository->getAlteraSearchPath();

                $result = $oDaoIPTUConstr->sql_record($oDaoIPTUConstr->sql_query_file($j39_matric, "", 'max(j39_idcons) as j39_idcons'));
                $arrayIdCons[$anoMatricula] = \db_utils::fieldsMemory($result, 0)->j39_idcons;
            }

            $j39_idcons = intval(max($arrayIdCons)) + 1;
        }
    }

    $sAnosQuePossuemMesmaConstrucao = "";
    $iAnousu = ($replicaDadosAnos == 0 ? $anoRetroativoMatricula : (isset($anoLimiteReplicaDados) ? $anoLimiteReplicaDados : $anousu));
    for ($anoMatricula = intval($anoRetroativoMatricula); $anoMatricula <= $iAnousu; $anoMatricula++) {
        //Aqui executa a validação se existe a mesma construção nos anos posteriores, que é para não tentar incluir a mesma novamente.
        $esquema = "cadastro";
        if ($anoMatricula < $anousu) {
            $esquema = $esquema . '_' . $anoMatricula;
        }
        $sSqlGetConstrucao = "Select * from $esquema.iptuconstr where j39_matric = $j39_matric and j39_idcons = $j39_idcons";
        $resultGetConstrucao = db_query($sSqlGetConstrucao);
        $numLinhasGetConstrucao = pg_num_rows($resultGetConstrucao);

        if ($numLinhasGetConstrucao > 0) {
            if ($sAnosQuePossuemMesmaConstrucao == "") {
                $sAnosQuePossuemMesmaConstrucao = $anoMatricula;
            } else {
                $sAnosQuePossuemMesmaConstrucao .= ', ' . $anoMatricula;
            }
            continue;
        }
    }

    if ($sAnosQuePossuemMesmaConstrucao != "") {
        $sMensagemErro = "Não é possivel replicar esta construção para os anos selecionados pois a mesma ja existe nos anos: $sAnosQuePossuemMesmaConstrucao";
        $lSqlErro = true;
    }

    if ($lSqlErro == false) {
        for ($anoMatricula = intval($anoRetroativoMatricula); $anoMatricula <= $iAnousu; $anoMatricula++) {
            if ($anoMatricula == $anoRetroativoMatricula) {
                db_query("select fc_putsession('DB_anoretroativo', '{$anoRetroativoMatricula} - {$anoLimiteReplicaDados}')");
            }

            if ($liberaCalculoRetroativo) {
                $calculoRetroativoIptuRepository->setAnoRetroativoMatricula($anoMatricula);
                $calculoRetroativoIptuRepository->getAlteraSearchPath();
            }


            $lSqlErro = false;
            db_inicio_transacao();

            if ($j39_idcons == "" || $j39_idcons == 0) {

                $result = $oDaoIPTUConstr->sql_record($oDaoIPTUConstr->sql_query_file($j39_matric, "", 'max(j39_idcons) as j39_idcons'));
                if ($oDaoIPTUConstr->numrows > 0) {
                    db_fieldsmemory($result, 0);
                } else {
                    $j39_idcons = 0;
                }
                $j39_idcons = $j39_idcons + 1;
            }

            if ($j39_idprinc == "t") {
                $oDaoIPTUConstr->alterar_idprinc($j39_matric, null, 'false');
            }

            $oDaoIPTUConstr->j39_matric = $j39_matric;
            $oDaoIPTUConstr->j39_idprinc = $j39_idprinc;
            $oDaoIPTUConstr->incluir($j39_matric, $j39_idcons, false);
            $sMensagemErro = "[ 1 ] - " . $oDaoIPTUConstr->erro_msg;

            if ($oDaoIPTUConstr->erro_status == 0) {
                $lSqlErro = true;
            }

            $matriz = explode("X", $caracteristica);

            for ($i = 0; $i < sizeof($matriz); $i++) {

                $j48_caract = $matriz[$i];
                if ($j48_caract != "" && $j48_caract != 0) {

                    $oDaoCarConstr->incluir($j39_matric, $j39_idcons, $j48_caract);
                    if ($oDaoCarConstr->erro_status == 0) {
                        $lSqlErro = true;
                    }
                }
            }

            if ($lSqlErro == false) {

                if (isset($j83_pontos) && $j83_pontos != "") {

                    $oDaoIPTUConstrPontos->j83_matric = $j39_matric;
                    $oDaoIPTUConstrPontos->j83_idcons = $j39_idcons;
                    $oDaoIPTUConstrPontos->j83_pontos = $j83_pontos;
                    $oDaoIPTUConstrPontos->incluir(null);

                    if ($oDaoIPTUConstrPontos->erro_status == 0) {
                        $lSqlErro = true;
                        $sMensagemErro = "[ 2 ] - " . $oDaoIPTUConstrPontos->erro_msg;
                    }
                }
            }

            if ($lSqlErro == false) {

                $oDaoIPTUConstr_copia = $oDaoIPTUConstr;
                $sWhere = "rtrim(j39_compl)='$j39_compl' and j39_codigo=$j39_codigo and j39_numero=$j39_numero";
                $result_mesmo = $oDaoIPTUConstr->sql_record($oDaoIPTUConstr->sql_query(null, null, "j39_matric", null, $sWhere));

                if ($oDaoIPTUConstr->numrows > 1) {
                    $menserro = "Já existe uma matrícula com o mesmo logradouro,numero e complemento!";
                }
                $oDaoIPTUConstr = $oDaoIPTUConstr_copia;
            }

            /**
             * Caso a construção seja baseada em uma construção do módulo projetos
             * Inclui na tabela de vinculação
             */

            if (isset($oPost->modulo_projetos) && $oPost->modulo_projetos == "true") {

                $oDaoIPTUConstrObrasConstr->j132_obrasconstr = $oPost->j132_obrasconstr;
                $oDaoIPTUConstrObrasConstr->j132_matric = $oDaoIPTUConstr->j39_matric;
                $oDaoIPTUConstrObrasConstr->j132_idconstr = $oDaoIPTUConstr->j39_idcons;
                $oDaoIPTUConstrObrasConstr->incluir(null);

                if ((int)$oDaoIPTUConstrObrasConstr->erro_status == 0) {
                    $lSqlErro = true;
                    $sMensagemErro = "[ 3 ] - " . $oDaoIPTUConstrObrasConstr->erro_msg;
                }
            }

            db_fim_transacao($lSqlErro);
            $db_botao = 1;

            if ($anoMatricula == $anoRetroativoMatricula) {
                db_query("select fc_delsession('DB_anoretroativo')");
            }
        }
    }
    /**
     *
     * Rotina de Alteração
     *
     */
/**
 * 
 *  "} elseif ( isset($alterar) ) {" abaixo 
 *  é condição para adicionar código do pluign Cálculo Iptu Proporcional
 *  Não indentar
 *  
 */
} elseif ( isset($alterar) ) {
    $lSqlErro == false;
    $sAnosConstrucaoInexistente = "";
    $iAnousu = ($replicaDadosAnos == 0 ? $anoRetroativoMatricula : (isset($anoLimiteReplicaDados) ? $anoLimiteReplicaDados : $anousu));
    for ($anoMatricula = intval($anoRetroativoMatricula); $anoMatricula <= $iAnousu; $anoMatricula++) {
        //Aqui executa a validação se existe a mesma construção nos anos posteriores, que é para não tentar incluir a mesma novamente.
        $esquema = "cadastro";
        if ($anoMatricula < $anousu) {
            $esquema = $esquema . '_' . $anoMatricula;
        }
        $sSqlGetConstrucao = "Select * from $esquema.iptuconstr where j39_matric = $j39_matric and j39_idcons = $j39_idcons";
        $resultGetConstrucao = db_query($sSqlGetConstrucao);
        $numLinhasGetConstrucao = pg_num_rows($resultGetConstrucao);
        if ($numLinhasGetConstrucao == 0) {
            if ($sAnosConstrucaoInexistente == "") {
                $sAnosConstrucaoInexistente = $anoMatricula;
            } else {
                $sAnosConstrucaoInexistente .= ', ' . $anoMatricula;
            }
        }
    }

    if ($sAnosConstrucaoInexistente != "") {
        $sMensagemErro = "Não é possivel replicar esta alteração para os anos selecionados pois a mesma não existe nos anos: $sAnosConstrucaoInexistente";
        $lSqlErro = true;
    }

    if ($lSqlErro == false) {
        $iAnousu = ($replicaDadosAnos == 0 ? $anoRetroativoMatricula : (isset($anoLimiteReplicaDados) ? $anoLimiteReplicaDados : $anousu));
        for ($anoMatricula = intval($anoRetroativoMatricula); $anoMatricula <= $iAnousu; $anoMatricula++) {
            if ($anoMatricula == $anoRetroativoMatricula) {
                if (empty($anoLimiteReplicaDados)) {
                    db_query("select fc_putsession('DB_anoretroativo', '{$anoRetroativoMatricula}')");
                } else {
                    db_query("select fc_putsession('DB_anoretroativo', '{$anoRetroativoMatricula} ? {$anoLimiteReplicaDados}')");
                }
            }

            if ($liberaCalculoRetroativo and ($replicaDadosAnos == 1 or !isset($alterando)) and $anoMatricula <= $anoLimiteReplicaDados) {
                $calculoRetroativoIptuRepository->setAnoRetroativoMatricula($anoMatricula);
                $calculoRetroativoIptuRepository->getAlteraSearchPath();
            }

            $lSqlErro = false;
            db_inicio_transacao();
            $dtdemo = @$j39_dtdemo_ano . "-" . @$j39_dtdemo_mes . "-" . @$j39_dtdemo_dia;
            
            // if ($dtdemo != "--") {
            //     $sWhere = "j39_matric=$j39_matric and j39_idcons=$j39_idcons and j39_idprinc = 't' ";
            //     $result_princ = $oDaoIPTUConstr->sql_record($oDaoIPTUConstr->sql_query(null, null, "j39_idprinc", null, $sWhere));
            //     if ($oDaoIPTUConstr->numrows > 0) {
            //         $sWhere = "j39_matric=$j39_matric and j39_idcons<>$j39_idcons and j39_idprinc = 'f' and j39_dtdemo is null";
            //         $result_princ = $oDaoIPTUConstr->sql_record($oDaoIPTUConstr->sql_query(null, null, "j39_matric as matric,j39_idcons as idcons", null, $sWhere));
                    
            //         if ($oDaoIPTUConstr->numrows > 0) {
            //             $lSqlErro = true;
            //             $sMensagemErro = "Alteração Abortada!!É necessário informar outra construção como principal!!";
            //         }
            //     }
            // }

            if ($lSqlErro == false) {
                if (isset($j83_pontos)) {
                    $sWhere = "j83_matric = $j39_matric and j83_idcons = $j39_idcons ";
                    $result_pontos = $oDaoIPTUConstrPontos->sql_record($oDaoIPTUConstrPontos->sql_query(null, "j83_codigo", null, $sWhere));
                    if ($oDaoIPTUConstrPontos->numrows > 0) {
                        db_fieldsmemory($result_pontos, 0);
                        if (isset($j83_pontos) && $j83_pontos != "") {
                            $oDaoIPTUConstrPontos->j83_pontos = $j83_pontos;
                            $oDaoIPTUConstrPontos->j83_codigo = $j83_codigo;
                            $oDaoIPTUConstrPontos->alterar($j83_codigo);
                            if ($oDaoIPTUConstrPontos->erro_status == 0) {
                                $lSqlErro = true;
                                $sMensagemErro = "[ 4 ] - " . $oDaoIPTUConstrPontos->erro_msg;
                            }
                        } else {
                            $oDaoIPTUConstrPontos->excluir(null, "j83_matric = $j39_matric and j83_idcons = $j39_idcons ");
                            if ($oDaoIPTUConstrPontos->erro_status == 0) {
                                $lSqlErro = true;
                                $sMensagemErro = "[ 5 ] - " . $oDaoIPTUConstrPontos->erro_msg;
                            }
                        }
                    } else {
                        if (isset($j83_pontos) && $j83_pontos != "") {
                            $oDaoIPTUConstrPontos->j83_matric = $j39_matric;
                            $oDaoIPTUConstrPontos->j83_idcons = $j39_idcons;
                            $oDaoIPTUConstrPontos->j83_pontos = $j83_pontos;
                            $oDaoIPTUConstrPontos->incluir(null);

                            if ($oDaoIPTUConstrPontos->erro_status == 0) {
                                $lSqlErro = true;
                                $sMensagemErro = "[ 6 ] - " . $oDaoIPTUConstrPontos->erro_msg;
                            }
                        }
                    }
                }

                $resultPrincipal = db_query(
                    "select j39_idcons, j39_matric ,j39_idprinc j39_dtdemo from iptuconstr
                    where iptuconstr.j39_matric = {$j39_matric}
                    and j39_idcons != {$j39_idcons}
                    and j39_idprinc = 't'"
                );
                $resultDisponiveis = db_query(
                    "select j39_idcons, j39_matric ,j39_idprinc j39_dtdemo from iptuconstr
                    where iptuconstr.j39_matric = {$j39_matric}
                    and j39_idcons != {$j39_idcons}
                    and j39_idprinc = 'f'
                    and j39_dtdemo is null"
                );

                if(pg_num_rows($resultPrincipal) > 0){
                    $idcons = pg_fetch_result($resultPrincipal, 0, 'j39_idcons');
                    $matric = pg_fetch_result($resultPrincipal, 0, 'j39_matric');
                    if($testaprinc == 't'){
                        $update = db_query("update cadastro.iptuconstr set j39_idprinc = false where j39_matric = {$matric}");
                    }else if($testaprinc == 'f' && $j39_dtdemo == null && $j39_idprinc == 't'){
                        $update = db_query("update cadastro.iptuconstr set j39_idprinc = false where j39_matric = {$matric}");
                    }else if($testaprinc == 'f' && $j39_dtdemo != null && $j39_idprinc == 't'){
                        $lSqlErro = true;
                        $sMensagemErro = "Para definir uma nova construção principal, a data de demolição não deve ser preenchida!";
                        break;
                    }
                    $oDaoIPTUConstr->j39_idprinc = $j39_idprinc;
                    $oDaoIPTUConstr->alterar($j39_matric, $j39_idcons, false);
                    $sMensagemErro = "[ 7 ] - " . $oDaoIPTUConstr->erro_msg;
                }else if(pg_num_rows($resultDisponiveis) > 0 && $testaprinc == 'f'){
                    $idcons = $selectPrincipal;
                    $matric = $j39_matric;
                    $update = db_query("update cadastro.iptuconstr set j39_idprinc = true where	j39_idcons = {$idcons} and j39_dtdemo is null and j39_matric = {$matric}");
                    @$GLOBALS["HTTP_POST_VARS"]["j39_idprinc"] = 'f';
                    $oDaoIPTUConstr->j39_idprinc = 'f';
                    $oDaoIPTUConstr->alterar($j39_matric, $j39_idcons);
                    $lSqlErro = false;
                    $sMensagemErro = "Construção {$idcons} Foi alterada para Principal";
                }else{
                    $oDaoIPTUConstr->j39_idprinc = $j39_idprinc;
                    $oDaoIPTUConstr->alterar($j39_matric, $j39_idcons, false);
                    $sMensagemErro = "[ 7 ] - " . $oDaoIPTUConstr->erro_msg;
                }
                if ($oDaoIPTUConstr->erro_status == 0) {
                    $lSqlErro = true;
                }

                $matriz = explode("X", $caracteristica);
                $oDaoCarConstr->j48_matric = $j39_matric;
                $oDaoCarConstr->j48_idcons = $j39_idcons;

                $GLOBALS["HTTP_POST_VARS"]["j48_matric"] = $j39_matric; // para o acount funcionar
                $GLOBALS["HTTP_POST_VARS"]["j48_idcons"] = $j39_idcons; // para o acount funcionar

                $sCaracteristicas = implode(',', $matriz);
                if ($matriz[0] != "") {
                    $sSqlCaracter = $oDaoCaracter->sql_query_carconstr($j39_matric, $j39_idcons, $sCaracteristicas);
                    $rsDaoCaracter = $oDaoCaracter->sql_record($sSqlCaracter);
                }
                if ($oDaoCaracter->numrows > 0) {
                    while ($aRetornoCaracter = pg_fetch_object($rsDaoCaracter)) {
                        if ($aRetornoCaracter->caract <> $aRetornoCaracter->caractmatric) {
                            $GLOBALS["HTTP_POST_VARS"]["j48_caract"] = $aRetornoCaracter->caract; // para o acount funcionar
                            $oDaoCarConstr->j48_caract = $aRetornoCaracter->caract;
                            $oDaoCarConstr->alterar($j39_matric, $j39_idcons, $aRetornoCaracter->caractmatric);
                            if ($oDaoCarConstr->erro_status == 0) {
                                $lSqlErro = true;
                                $sMensagemErro = "[ 12 ] - " . $oDaoCarConstr->erro_msg;
                            }
                        }

                        if (!empty($aRetornoCaracter->caract) && empty($aRetornoCaracter->caractmatric)) {
                            $oDaoCarConstr->j48_caract = $aRetornoCaracter->caract;
                            $oDaoCarConstr->incluir($j39_matric, $j39_idcons, $aRetornoCaracter->caract);
                            if ($oDaoCarConstr->erro_status == 0) {
                                $lSqlErro = true;
                                $sMensagemErro = "[ 13 ] - " . $oDaoCarConstr->erro_msg;
                            }
                        }
                        $j48_caract = $aRetornoCaracter->caract;
                    }
                }

                $matrizE = implode(',', $matriz);
                $notin = '';
                if ($matrizE != false) {
                    $notin = 'j48_caract not in (' . $matrizE . ') and';
                }

                $sWhere = $notin . " j48_idcons = " . $j39_idcons . " and j48_matric = " . $j39_matric;
                $sqlExclusao = $oDaoCarConstr->sql_query_file(null, null, null, "j48_caract", null, $sWhere);
                $rsExclusao = $oDaoCarConstr->sql_record($sqlExclusao);

                if ($oDaoCarConstr->numrows > 0) {
                    for ($ie = 0; $ie < $oDaoCarConstr->numrows; $ie++) {
                        db_fieldsmemory($rsExclusao, $ie);
                        $oDaoCarConstr->j48_idcons = $j39_idcons;
                        $oDaoCarConstr->j48_caract = $j48_caract;
                        $GLOBALS["HTTP_POST_VARS"]["j48_caract"] = $matrizE[$ie]; // para o acount funcionar
                        $CarConstrWhere = 'j48_matric = ' . $j39_matric . ' and j48_idcons = ' . $j39_idcons . ' and j48_caract = ' . $j48_caract;
                        $oDaoCarConstr->excluir($j39_matric, $j39_idcons, $j48_caract, $CarConstrWhere);
                        if ($oDaoCarConstr->erro_status == 0) {
                            $lSqlErro = true;
                            $sMensagemErro = "[ 14 ] - " . $oDaoCarConstr->erro_msg;
                        }
                    }
                }
            }
            db_fim_transacao($lSqlErro);
            $db_botao = 2;

            if ($anoMatricula == $anoRetroativoMatricula) {
                db_query("select fc_delsession('DB_anoretroativo')");
            }

            $alterando = true;
        }
    }

    if ($j39_dtdemo != null || !empty($j39_dtdemo)) {
        
        db_fim_transacao();
        $iAnousu = $anousu;

        for ($anoMatricula = $anoRetroativoMatricula; $anoMatricula <= $iAnousu; $anoMatricula++) {
            if ($anoMatricula == $anoRetroativoMatricula) {
                if (empty($anoLimiteReplicaDados)) {
                    db_query("select fc_putsession('DB_anoretroativo', '{$anoRetroativoMatricula}')");
                } else {
                    db_query("select fc_putsession('DB_anoretroativo', '{$anoRetroativoMatricula} Á {$anoLimiteReplicaDados}')");
                }
            }

            if ($liberaCalculoRetroativo) {
                $calculoRetroativoIptuRepository->setAnoRetroativoMatricula($anoMatricula);
                $calculoRetroativoIptuRepository->getAlteraSearchPath();
            }

            $lSqlErro = false;
            if (isset($j39_dtdemo_ano) && isset($j39_dtdemo_mes) && isset($j39_dtdemo_dia)) {
                $dtdemo = $j39_dtdemo_ano . "-" . $j39_dtdemo_mes . "-" . $j39_dtdemo_dia;
            }

            $sWhere = "j39_matric=$j39_matric and j39_idcons=$j39_idcons and j39_idprinc = 't' ";
            $result_princ = $oDaoIPTUConstr->sql_record($oDaoIPTUConstr->sql_query(null, null, "j39_idprinc", null, $sWhere));
            if ($oDaoIPTUConstr->numrows > 0) {
                $sWhere = "j39_matric = {$j39_matric} and j39_idcons <> {$j39_idcons} and j39_idprinc = 'f' and j39_dtdemo is null";

                $result_princ = $oDaoIPTUConstr->sql_record($oDaoIPTUConstr->sql_query(null, null, "j39_matric as matric,j39_idcons as idcons", null, $sWhere));
                if ($oDaoIPTUConstr->numrows == 1 && pg_fetch_result($result_princ, 0, 'idcons') != null) {
                    @$GLOBALS["HTTP_POST_VARS"]["j39_idprinc"] = 'f';
                    $oDaoIPTUConstr->j39_idprinc = 'f';
                    $oDaoIPTUConstr->alterar($j39_matric, $j39_idcons);
                    $idcons = pg_fetch_result($result_princ, 0, 'idcons');
                    $matric = pg_fetch_result($result_princ, 0, 'matric');
                    $update = "update cadastro.iptuconstr set j39_idprinc = true where	j39_idcons = {$idcons} and j39_dtdemo is null and j39_matric = {$matric};";
                    $oDaoIPTUConstr->sql_record($update);
                    $lSqlErro = false;
                    $sMensagemErro = "Construção {$idcons} Foi alterada para Principal";
                    
                }else if($oDaoIPTUConstr->numrows > 1){
                    @$GLOBALS["HTTP_POST_VARS"]["j39_idprinc"] = 'f';
                    $oDaoIPTUConstr->j39_idprinc = 'f';
                    $oDaoIPTUConstr->alterar($j39_matric, $j39_idcons);
                    $idcons = $selectPrincipal;
                    $matric = pg_fetch_result($result_princ, 0, 'matric');
                    $update = "update cadastro.iptuconstr set j39_idprinc = true where	j39_idcons = {$idcons} and j39_dtdemo is null and j39_matric = {$matric};";
                    $oDaoIPTUConstr->sql_record($update);
                    $lSqlErro = false;
                    $sMensagemErro = "Construção {$idcons} Foi alterada para Principal";
                }
                
            }
            
        }
        
        db_fim_transacao($lSqlErro);
    }

    /**
     *
     * Rotina de Exclusão
     *
     */
} elseif (isset($excluir)) {
    for ($anoMatricula = $anoRetroativoMatricula; $anoMatricula <= $iAnousu; $anoMatricula++) {

        if ($anoMatricula == $anousu) {
            db_query("select fc_putsession('DB_anoretroativo', '{$anoRetroativoMatricula} - {$anousu}')");
        }

        if ($liberaCalculoRetroativo and $replicaDadosAnos == 1) {
            $calculoRetroativoIptuRepository->setAnoRetroativoMatricula($anoMatricula);
            $calculoRetroativoIptuRepository->getAlteraSearchPath();
        }

        $lSqlErro = false;
        // verifica se é  principal
        $sWhere = "j39_idprinc is true and j39_dtdemo is null";
        $sqlprinc = $oDaoIPTUConstr->sql_query_file($j39_matric, $j39_idcons, "j39_matric", null, $sWhere);
        $resultprinc = $oDaoIPTUConstr->sql_record($sqlprinc);
        $linhasprinc = $oDaoIPTUConstr->numrows;

        if ($linhasprinc > 0) {

            // verifica se tem construções secundarias
            $sWhere = "j39_idprinc is false and j39_dtdemo is null and j39_idcons <> {$j39_idcons}";
            $sqlsec = $oDaoIPTUConstr->sql_query_file($j39_matric, null, "j39_matric", null, $sWhere);
            $resultsec = $oDaoIPTUConstr->sql_record($sqlsec);
            $linhassec = $oDaoIPTUConstr->numrows;

            if ($linhassec > 0) {
                $lSqlErro = true;
                $sMensagemErro = "Exclusão Abortada!!É necessário informar outra construção como principal!!";
            }
        }

        db_inicio_transacao();

        /**
         * Caso a construção seja baseada em uma construções do módulo projetos
         * Inclui na tabela de vinculação
         */


        $oDaoIPTUConstrObrasConstr->excluir(null, "j132_matric = {$j39_matric}  and j132_idconstr = {$j39_idcons}");

        if ((int)$oDaoIPTUConstrObrasConstr->erro_status == 0) {

            $lSqlErro = true;
            $sMensagemErro = "[ 8 ] - " . $oDaoIPTUConstrObrasConstr->erro_msg;
        }

        $lSqlErro = false;
        /*
        * verifica se tem registro na tabelas iptucacpadraoconstr
        */
        $sSqlcalcPadrao = "select *
                            from iptucalcpadraoconstr
                            where j11_matric = $j39_matric
                            and j11_idcons = $j39_idcons";

        $rsResultCalcPadrao = db_query($sSqlcalcPadrao);
        $iLinhasCalcPadrao = pg_num_rows($rsResultCalcPadrao);

        if ($iLinhasCalcPadrao > 0) {

            $sSqlcalcPadraoExcluir = " delete from iptucalcpadraoconstr where j11_matric = $j39_matric and j11_idcons = $j39_idcons ";
            db_query($sSqlcalcPadraoExcluir);
        }

        $rsParamentroExcluiConstr = $oDaoCfIPTU->sql_record($oDaoCfIPTU->sql_query_file(db_getsession('DB_anousu'), "j18_excconscalc"));

        if ($oDaoCfIPTU->numrows > 0) {

            db_fieldsmemory($rsParamentroExcluiConstr, 0);
            if ($j18_excconscalc != "" && $j18_excconscalc == 't') {
                $excconscalc = true;
            } else {
                $excconscalc = false;
            }
        }


        if ($lSqlErro == false) {

            $sqlcalculo = " select *  from iptuconstr ";
            $sqlcalculo .= "          inner join iptucale on j22_matric= j39_matric ";
            $sqlcalculo .= "                             and j22_idcons=j39_idcons  ";
            $sqlcalculo .= "  where j39_matric = $j39_matric ";
            $sqlcalculo .= "    and j39_idcons = $j39_idcons";
            $resultcalculo = db_query($sqlcalculo);
            $linhascalculo = pg_num_rows($resultcalculo);


            if ($linhascalculo > 0 && !$excconscalc) {
                $lSqlErro = true;
                $sMensagemErro = "Exclusão Abortada!!Não é possivel excluir uma construção que ja possui cálculo gerado!!";
            }
        }


        if ($lSqlErro == false) {

            $oDaoIPTUConstr->excluirGeral($j39_matric, $j39_idcons, true);
            $sMensagemErro = "[ 9 ] - " . $oDaoIPTUConstr->erro_msg;

            if ($oDaoIPTUConstr->erro_status == 0) {

                $lSqlErro = true;
                $sMensagemErro = "[ 10 ] - " . $oDaoIPTUConstr->erro_msg;
            }

            $result01 = $oDaoIPTUConstr->sql_record($oDaoIPTUConstr->sql_query_file($j39_matric, "", "j39_idprinc as strpri,j39_idcons as strcons", "j39_idcons"));
            $numrows01 = $oDaoIPTUConstr->numrows;

            if ($numrows01 > 0) {

                $blz = "";
                for ($g = 0; $g < $numrows01; $g++) {

                    db_fieldsmemory($result01, $g);

                    if ($strpri == 't') {

                        $blz = "ok";
                        break;
                    }
                }

                if ($blz == "") {

                    db_fieldsmemory($result01, 0);
                    $oDaoIPTUConstr->alterar_idprinc($j39_matric, $j39_idcons, 'false');
                    $oDaoIPTUConstr->j39_idcons = $strcons;
                    $oDaoIPTUConstr->j39_idprinc = 't';
                    $oDaoIPTUConstr->alterar($j39_matric, $strcons);
                    
                    if ($oDaoIPTUConstr->erro_status == 0) {

                        $lSqlErro = true;
                        $sMensagemErro = "[ 11 ] - " . $oDaoIPTUConstr->erro_msg;
                    }
                }
            }
            db_fim_transacao($lSqlErro);
        }
        $db_botao = 1;

        if ($anoMatricula == $anoRetroativoMatricula) {
            db_query("select fc_delsession('DB_anoretroativo')");
        }

    }

    /**
     * preencher campos do formulário
     */
} elseif (isset($j39_idcons) && $j39_idcons != "") {

    $sSqlDadosFormulario = $oDaoIPTUConstr->sql_query($j39_matric, $j39_idcons, "*", "", "");
    $rsDadosFormulario = $oDaoIPTUConstr->sql_record($sSqlDadosFormulario);

    if ($oDaoIPTUConstr->numrows != 0) {

        $db_opcaoid = 3;
        $db_botao = 2;

        db_fieldsmemory($rsDadosFormulario, 0);
        $oIPTUConstr = db_utils::fieldsMemory($rsDadosFormulario, 0);

        $sSqlCarConstr = $oDaoCarConstr->sql_query($j39_matric, $j39_idcons, "", "*");
        $rsCarConstr = $oDaoCarConstr->sql_record($sSqlCarConstr);
        $caracteristica = null;
        $car = "X";

        for ($i = 0; $i < $oDaoCarConstr->numrows; $i++) {

            db_fieldsmemory($rsCarConstr, $i);
            $caracteristica .= $car . $j48_caract;
            $car = "X";
        }

        $caracteristica .= $car;
        $result_pontos = $oDaoIPTUConstrPontos->sql_record($oDaoIPTUConstrPontos->sql_query(null, "*", null, "j83_matric = $j39_matric and j83_idcons = $j39_idcons "));

        if ($oDaoIPTUConstrPontos->numrows > 0) {
            db_fieldsmemory($result_pontos, 0);
        }


        if (!empty($j39_codprotdemo)) {
            $rsProcesso = $oDaoProtProcesso->sql_record($oDaoProtProcesso->sql_query($j39_codprotdemo, "cgm.z01_nome as p58_requer", null));

            if ($oDaoProtProcesso->numrows > 0) {
                db_fieldsmemory($rsProcesso, 0);
            } else {
                $lProcesso = "N";
            }
        }

        /**
         * Valida se a construção está vinculada a uma obra do modulo projetos
         */
        $sSqlIPTUObrasConstr = " select a.j132_obrasconstr,                                                           \n";
        $sSqlIPTUObrasConstr .= "        coalesce( sum(j39_area) , 0 ) as j39_area,                                    \n";
        $sSqlIPTUObrasConstr .= "        coalesce( min(ob08_area), 0 ) as ob08_area                                    \n";
        $sSqlIPTUObrasConstr .= "   from iptuconstrobrasconstr a                                                       \n";
        $sSqlIPTUObrasConstr .= "        inner join obrasconstr             on ob08_codconstr     = a.j132_obrasconstr \n";
        $sSqlIPTUObrasConstr .= "        inner join iptuconstrobrasconstr b on b.j132_obrasconstr = a.j132_obrasconstr \n";
        $sSqlIPTUObrasConstr .= "        inner join iptuconstr              on j39_matric = b.j132_matric              \n";
        $sSqlIPTUObrasConstr .= "                                          and j39_idcons = b.j132_idconstr            \n";
        $sSqlIPTUObrasConstr .= "  where a.j132_matric   = {$oIPTUConstr->j39_matric}                                  \n";
        $sSqlIPTUObrasConstr .= "    and a.j132_idconstr = {$oIPTUConstr->j39_idcons}                                  \n";
        $sSqlIPTUObrasConstr .= "  group by a.j132_obrasconstr                                                         \n";

        $rsIPTUObrasConstr = db_query($sSqlIPTUObrasConstr);
        if ($rsIPTUObrasConstr && pg_num_rows($rsIPTUObrasConstr) > 0) {

            $oIPTUConstrObrasConstr = db_utils::fieldsMemory($rsIPTUObrasConstr, 0);
            $j132_obrasconstr = $oIPTUConstrObrasConstr->j132_obrasconstr;
            $nLimiteArea = $oIPTUConstr->j39_area +
                $oIPTUConstrObrasConstr->ob08_area -
                $oIPTUConstrObrasConstr->j39_area;

            $sOnChangeArea = "onChange=\"js_validaArea({$nLimiteArea});\"";
        }
    } else {

        $result = $oDaoIPTUBase->sql_record($oDaoIPTUBase->sql_query($j39_matric, "z01_nome", ""));
        db_fieldsmemory($result, 0);

    }

    $sWhere = "j39_matric = {$j39_matric} and j39_idcons <> {$j39_idcons} and j39_idprinc = 'f' and j39_dtdemo is null";
    $verificaDisponivel = $oDaoIPTUConstr->sql_record($oDaoIPTUConstr->sql_query(null, null, "j39_matric as matric,j39_idcons as idcons", null, $sWhere));
    $verificaResult = $oDaoIPTUConstr->numrows;
}


?>
    <html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" content="0">

        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBHint.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/classes/DBViewCaracteristicasConstrucao.classe.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
        <script type="text/javascript" src="scripts/classes/http/http.js"></script>

        <link href="estilos.css" rel="stylesheet" type="text/css">
        <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">

    </head>
    <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"
          onLoad="js_trocacordeselect(); js_montaCampoProcesso();">
    <table align="center" width="790" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" valign="top" bgcolor="#CCCCCC">
                <center>
                    <?php include(modification("forms/db_frmiptuconstralt.php")); ?>
                </center>
            </td>
        </tr>
    </table>
    </body>
    </html>

    <script>
        function js_colocaid() {
            document.form1.id_setor2.value = parent.document.form1.idsetor.value;
            document.form1.id_quadra2.value = parent.document.form1.idquadra.value;
        }

        js_colocaid();
    </script>

<?php

if (isset($incluir) || (isset($alterar)) || (isset($excluir))) {

    if ($lSqlErro == true) {

        db_msgbox($sMensagemErro);

        if ($oDaoIPTUConstr->erro_campo != "") {
            echo "<script>js_removeObj('msgbox');</script>";
            echo "<script> document.form1." . $oDaoIPTUConstr->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $oDaoIPTUConstr->erro_campo . ".focus();</script>";
        }

    } else {


        if (isset($menserro)) {
            db_msgbox($menserro);
        }

        db_msgbox($sMensagemErro);

        $alterando2 = "";
        $j01_matric = "";

        if (isset($alterando)) {
            $alterando2 = "alterando=true";
            $j01_matric = "&j01_matric={$j39_matric}";
        }

        db_redireciona("cad1_iptuconstralt.php?id_setor2=$id_setor2&id_quadra2=$id_quadra2&j39_matric=$j39_matric&j39_idcons=nova$j01_matric&$alterando2");

    }
}
