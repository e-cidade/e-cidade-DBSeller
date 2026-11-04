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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("classes/db_matricobs_classe.php"));
require_once(modification("classes/db_iptuant_classe.php"));
require_once(modification("classes/db_iptubaseregimovel_classe.php"));
require_once(modification("classes/db_iptubasecondominio_classe.php"));
require_once(modification("classes/db_iptubasepredio_classe.php"));
require_once(modification("classes/db_cgm_classe.php"));

$codProc = [];
if (isset($_SESSION['PROCESSO_LOG'])) {

    $codProc = explode('/', $_SESSION['PROCESSO_LOG']);
    if (isset($codProc[0]) && isset($codProc[1])) {

        $processo = "
            select p58_codproc,
                   cast(p58_numero || '/' || p58_ano as varchar) as p58_numero,
                   z01_numcgm as DB_p58_numcgm,
                   p58_dtproc,
                   p51_descr,
                   p58_obs,
                   p58_requer
            from protprocesso
                 join cgm on cgm.z01_numcgm = protprocesso.p58_numcgm
                 join db_config on db_config.codigo = protprocesso.p58_instit
                 join db_usuarios on db_usuarios.id_usuario = protprocesso.p58_id_usuario
                 join db_depart on db_depart.coddepto = protprocesso.p58_coddepto
                 join tipoproc on tipoproc.p51_codigo = protprocesso.p58_codigo
                 join db_config as a on a.codigo = db_depart.instit
            where p58_instit = 1
              and p58_ano = $codProc[1]
              and p58_numero = '$codProc[0]'
            order by p58_codproc desc ;
            ";
        $result = db_query($processo);
        if (pg_num_rows($result) > 0) {
            db_fieldsmemory($result, 0);
            $y60_proces = db_getsession('PROCESSO_LOG');
        }
    }
}

$anousu = db_getsession("DB_anousu");
$anoRetroativoMatricula = db_getsession("DB_anoRetroativoMatricula", false);

$liberaCalculoRetroativo = false;

if (!$liberaCalculoRetroativo || empty($anoRetroativoMatricula)) {
    $anoRetroativoMatricula = $anousu;
}

db_postmemory($_GET);
db_postmemory($_POST);

/* Revisar a lógica abaixo e acima, está confusa */
if (isset($_SESSION['PROCESSO_LOG']) && !isset($y60_proces)) {
    $y60_proces = db_getsession('PROCESSO_LOG');
    db_query("select fc_putsession('PROCESSO_LOG', '" . db_getsession('PROCESSO_LOG') . "')");
} else {
    if (isset($y60_proces)) {
        $_SESSION['PROCESSO_LOG'] = $y60_proces;
        db_query("select fc_putsession('PROCESSO_LOG', '" . db_getsession('PROCESSO_LOG') . "')");
    } else {
        $_SESSION['PROCESSO_LOG'] = '0';
        db_query("select fc_putsession('PROCESSO_LOG', '" . db_getsession('PROCESSO_LOG') . "')");
    }
}

if (isset($_SESSION['PROCESSO_LOG'])) {
    $y60_proces = db_getsession('PROCESSO_LOG');
}

$db_botao = 1;
$db_opcao = 1;
$db_opcao_matric = 1;
$outros = false;

$clrotulo              = new rotulocampo;
$cliptubasecondominio  = new cl_iptubasecondominio;
$clcondominio          = new cl_condominio;
$cliptubasepredio      = new cl_iptubasepredio;
$cliptubase            = new cl_iptubase;
$cliptuant             = new cl_iptuant;
$clmatricobs           = new cl_matricobs;
$cliptubaseregimovel   = new cl_iptubaseregimovel;
$clpercposserural      = new cl_percposserural;
$clpromitente          = new cl_promitente;
$clpropri              = new cl_propri;
$clcfiptu              = new cl_cfiptu;
$cltipoproprietario    = new cl_tipoproprietario;
$cltipoproprietario->rotulo->label();


$parametrosNumeroCadastral = new App\Domain\Tributario\Cadastro\Models\ParametrosNumeroCadastral();
if ($parametrosNumeroCadastral->existeParametro(db_getsession("DB_instit"))) {
    $db_opcaorefant = 3;
}

$sql = $clcfiptu->sql_query_file(null, "*", null, "j18_anousu = {$anousu}");
$rCfiptu = $clcfiptu->sql_record($sql);

if (!$rCfiptu) {
    throw new DBException("Erro ao Buscar Parâmetros do Cadastro de Iptu.");
}

db_fieldsmemory($rCfiptu, 0);

$cliptubase->rotulo->label();

$clrotulo->label("z01_nome");
$clrotulo->label("j34_setor");
$clrotulo->label("j34_area");
$clrotulo->label("j26_obs");
$clrotulo->label("j40_refant");
$clrotulo->label("j40_registrocartografico");
$clrotulo->label("j18_utilizaareaprivativa");

$registroCartografico = filter_input(INPUT_POST, 'j40_registrocartografico');

$sqlerro = false;
$sErroMsg = "";

function validaPercentual($percentual, $matricula, $cgm)
{

    if (!empty($matricula) && !empty($cgm)) {
        $clpercposserural = new cl_percposserural;
        $where = "j166_matric = {$matricula} AND j166_numcgm <> {$cgm}";
        $sql = $clpercposserural->sql_query_file(null, "sum(j166_percentual) as soma", null, $where);
        $rs = $clpercposserural->sql_record($sql);

        if ($rs && pg_num_rows($rs) > 0) {
            $percentual += db_utils::fieldsMemory($rs, 0)->soma;
        }
    }

    if ($percentual > 100) {
        return false;
    }

    return true;
}

if (isset($incluir)) {

    $sql_pessoa = "select * from tipoproprietario where j163_tipoproprietario = $j01_tipoproprietario";
    $result_pessoa = db_query($sql_pessoa);
    db_fieldsmemory($result_pessoa, 0);
    $cgmInstance = \CgmFactory::getInstanceByCgm($j01_numcgm);
    if ($cgmInstance instanceof CgmFisico && $j163_pesfisjur == 2) {
        db_msgbox("CGM deve ser de Pessoa Jurídica.");
        $sqlerro = true;
    } elseif ($cgmInstance instanceof CgmJuridico && $j163_pesfisjur == 1) {
        db_msgbox("CGM deve ser de Pessoa Física.");
        $sqlerro = true;
    }

    db_inicio_transacao();

    if (isset($tipoImovel) && $tipoImovel == "2") {

        db_inicio_transacao();
        if (!validaPercentual($j166_percentual, $j01_matric, $j01_numcgm)) {
            $sqlerro = true;
            $cliptubase->erro_status = 0;
            $sErroMsg = "Percentual de posse não pode ser maior do que 100%.";
        }

        $cliptubase->j01_matric = $j01_matric;
        $cliptubase->j01_numcgm = $j01_numcgm;
        $cliptubase->j01_idbql  = 1000000000;
        $cliptubase->j01_codave = "1";
        $cliptubase->j01_fracao = 0;
        $cliptubase->j01_areaprivativa = 0;

        $cliptubase->j01_tipoimovel = 2;

        $cliptubase->j01_distrito   = $j01_distrito;
        $cliptubase->j01_hectare    = $j01_hectare;
        $cliptubase->j01_situcad    = $j01_situcad;
        $cliptubase->j01_datacad    = date('Y-m-d');
        $cliptubase->j01_processo   = $j01_processo;
        $cliptubase->j01_incra      = $j01_incra;
        $cliptubase->j01_descrlocal = $j01_descrlocal;

        if ($sqlerro == false) {
            $cliptubase->incluir($j01_matric);
            if ($cliptubase->erro_status == 0) {
                $sqlerro = true;
                $sErroMsg = $cliptubase->erro_msg;
            }
        }

        $clpercposserural->j166_matric = $cliptubase->j01_matric;
        $clpercposserural->j166_numcgm = $j01_numcgm;
        $clpercposserural->j166_percentual = !empty($j166_percentual) ? $j166_percentual : '0';

        if ($sqlerro == false) {
            $clpercposserural->incluir($j166_sequencial);
            if ($clpercposserural->erro_status == 0) {
                $sqlerro = true;
                $sErroMsg = "Não foi possível cadastrar percentual da posse rural.";
                $cliptubase->erro_status = 0;
            }
        }

        if ($sqlerro) {
            $cliptubase->erro_msg = $sErroMsg;
        }

        db_fim_transacao($sqlerro);
    } else {

        if ($liberaCalculoRetroativo) {
            $sequencesIptubase = array();
            $sequencesIptubasepredio = array();
            $sequencesIptubaseregimovel = array();
            $sequencesIptubasecondominio = array();

            for ($anoMatricula = $anoRetroativoMatricula; $anoMatricula <= $anousu; $anoMatricula++) {

                $schema = "";
                if ($anoMatricula < $anousu) {
                    $schema = "_{$anoMatricula}";
                }

                $resultIptubase = db_query("SELECT last_value AS sequence FROM cadastro{$schema}.iptubase_j01_matric_seq;");
                $sequencesIptubase[$anoMatricula] = \db_utils::fieldsMemory($resultIptubase, 0)->sequence;

                $resultIptubasepredio = db_query("SELECT last_value AS sequence FROM cadastro{$schema}.iptubasepredio_j109_sequencial_seq;");
                $sequencesIptubasepredio[$anoMatricula] = \db_utils::fieldsMemory($resultIptubasepredio, 0)->sequence;

                $resultIptubaseregimovel = db_query("SELECT last_value AS sequence FROM cadastro{$schema}.iptubaseregimovel_j04_sequencial_seq;");
                $sequencesIptubaseregimovel[$anoMatricula] = \db_utils::fieldsMemory($resultIptubaseregimovel, 0)->sequence;

                $resultIptubasecondominio = db_query("SELECT last_value AS sequence FROM cadastro{$schema}.iptubasecondominio_j108_sequencial_seq;");
                $sequencesIptubasecondominio[$anoMatricula] = \db_utils::fieldsMemory($resultIptubasecondominio, 0)->sequence;
            }

            $sequenceIptubase = intval(max($sequencesIptubase)) + 1;
            $sequenceIptubasepredio = intval(max($sequencesIptubasepredio)) + 1;
            $sequenceIptubaseregimovel = intval(max($sequencesIptubaseregimovel)) + 1;
            $sequenceIptubasecondominio = intval(max($sequencesIptubasecondominio)) + 1;
        }

        for ($anoMatricula = $anousu; $anoMatricula >= $anoRetroativoMatricula; $anoMatricula--) {

            db_inicio_transacao();
            if ($anoMatricula == $anousu) {
                db_query("select fc_putsession('DB_anoretroativo', '{$anoRetroativoMatricula} - {$anousu}')");
            }

            if ($liberaCalculoRetroativo) {
                $calculoRetroativoIptuRepository->setAnoRetroativoMatricula($anoMatricula);
                $calculoRetroativoIptuRepository->getAlteraSearchPath();

                db_query("SELECT setval('iptubase_j01_matric_seq', {$sequenceIptubase}, FALSE);");
                db_query("SELECT setval('iptubasepredio_j109_sequencial_seq', {$sequenceIptubasepredio}, FALSE);");
                db_query("SELECT setval('iptubaseregimovel_j04_sequencial_seq', {$sequenceIptubaseregimovel}, FALSE);");
                db_query("SELECT setval('iptubasecondominio_j108_sequencial_seq', {$sequenceIptubasecondominio}, FALSE);");
            }

            $cliptubase->j01_matric = $j01_matric;
            $cliptubase->j01_numcgm = $j01_numcgm;
            $cliptubase->j01_idbql = (int) filter_var($j01_idbql, FILTER_SANITIZE_NUMBER_INT);
            $cliptubase->j01_codave = "1";
            $cliptubase->j01_fracao = $j01_fracao;
            $cliptubase->j01_areaprivativa = $j01_areaprivativa;
            $cliptubase->j01_tipoimovel = 1;

            $cliptubase->j01_distrito   = null;
            $cliptubase->j01_hectare    = null;
            $cliptubase->j01_situcad    = null;
            $cliptubase->j01_datacad    = date('Y-m-d');
            $cliptubase->j01_processo   = null;
            $cliptubase->j01_incra      = null;
            $cliptubase->j01_descrlocal = null;

            if ($cliptubase->j01_fracao > 100) {
                $sqlerro = true;
                $sErroMsg = "Fração do Lote maior que 100%";
            }

            if ($sqlerro == false) {
                $cliptubase->incluir($j01_matric);
                if ($cliptubase->erro_status == 0) {
                    $sqlerro = true;
                    $sErroMsg = $cliptubase->erro_msg;
                }
            }

            $j01_matric = $cliptubase->j01_matric;

            if ($j26_obs != "" && $sqlerro == false) {
                $clmatricobs->j26_obs = $j26_obs;
                $clmatricobs->incluir($j01_matric);
                if ($clmatricobs->erro_status == 0) {
                    $sqlerro = true;
                    $sErroMsg = $clmatricobs->erro_msg;
                }
            }

            if (($j40_refant != "" || $registroCartografico != "") && $sqlerro == false) {
                $cliptuant->j40_matric = $j01_matric;
                $cliptuant->j40_refant = $j40_refant;
                $cliptuant->j40_registrocartografico = $registroCartografico;
                $cliptuant->incluir($j01_matric);
                if ($cliptuant->erro_status == 0) {
                    $sqlerro = true;
                    $sErroMsg = $cliptuant->erro_msg;
                }
            }

            // INCLUIR NA IPTUBASEREGIMOVEL ... SE TIVER REGISTROS
            if ($sqlerro == false) {
                if ($j04_setorregimovel != "") {
                    $cliptubaseregimovel->j04_setorregimovel = $j04_setorregimovel;
                    $cliptubaseregimovel->j04_matric = $j01_matric;
                    $cliptubaseregimovel->j04_matricregimo = $j04_matricregimo;
                    $cliptubaseregimovel->j04_quadraregimo = $j04_quadraregimo;
                    $cliptubaseregimovel->j04_loteregimo = $j04_loteregimo;
                    $cliptubaseregimovel->incluir(null);
                    if ($cliptubaseregimovel->erro_status == 0) {
                        $sqlerro = true;
                        $sErroMsg = $cliptubaseregimovel->erro_msg;
                    }
                }
            }

            if ($sqlerro == false) {

                if (isset($j107_sequencial) && trim($j107_sequencial) != "" && isset($predios) && $predios != 0) {
                    //insiro na iptubasepredio
                    $cliptubasepredio->j109_predio = $predios;
                    $cliptubasepredio->j109_matric = $j01_matric;
                    $cliptubasepredio->incluir(null);
                    if ($cliptubasepredio->erro_status == 0) {
                        $sqlerro = true;
                        $sErroMsg = $cliptubasepredio->erro_msg;
                    }
                } else if (isset($j107_sequencial) && trim($j107_sequencial) != "") {

                    $cliptubasecondominio->j108_condominio = $j107_sequencial;
                    $cliptubasecondominio->j108_matric = $j01_matric;
                    $cliptubasecondominio->incluir(null);

                    if ($cliptubasecondominio->erro_status == 0) {
                        $sqlerro = true;
                        $sErroMsg = $cliptubasecondominio->erro_msg;
                    }
                }
            }

            if ($sqlerro == false) {

                $numeroCadastral = $parametrosNumeroCadastral->montaNumero(db_getsession("DB_instit"), $j01_matric);
                if ($numeroCadastral) {

                    if (($j40_refant != "" || $registroCartografico != "") && $sqlerro == false) {

                        $cliptuant->j40_refant = $numeroCadastral;
                        $cliptuant->alterar($j01_matric);
                        if ($cliptuant->erro_status == 0) {
                            $sqlerro = true;
                            $sErroMsg = $cliptuant->erro_msg;
                        }
                    } else {

                        $cliptuant->j40_matric               = $j01_matric;
                        $cliptuant->j40_refant               = $numeroCadastral;
                        $cliptuant->j40_registrocartografico = $registroCartografico;

                        $sqlVerificaDuplicados = $cliptuant->sql_query_file(null, 'j40_matric', null, "j40_refant = '{$numeroCadastral}'");
                        $resultVerificaDuplicados = $cliptuant->sql_record($sqlVerificaDuplicados);

                        $erro = false;
                        if (isParaiba() && pg_num_rows($resultVerificaDuplicados) > 0) {
                            $erro = true;
                            $sqlerro = true;
                            $cliptubase->erro_status = 0;
                            $cliptubase->erro_msg = $cliptuant->erro_msg = "Unidade já cadastrada para essa inscrição imobiliária {$j40_refant}.";
                        }

                        if (!$erro) {
                            $cliptuant->incluir($j01_matric);
                        }

                        if ($cliptuant->erro_status == 0) {
                            $sqlerro = true;
                            $sErroMsg = $cliptuant->erro_msg;
                        }
                    }
                }
            }

            db_fim_transacao($sqlerro);
            if ($sqlerro) {
                $cliptubase->erro_msg = $sErroMsg;
            }
        }
    }
} else if (isset($alterar)) {
    $tipoImovel = isset($tipoImovel) ? $tipoImovel : $j01_tipoimovel;
    if ($tipoImovel == "2" || $j01_tipoimovel == "2") {

        db_inicio_transacao();
        if (!validaPercentual($j166_percentual, $j01_matric, $j01_numcgm)) {
            $sqlerro = true;
            $cliptubase->erro_status = 0;
            $sErroMsg = "Percentual de posse não pode ser maior do que 100%.";
        }

        $cliptubase->j01_matric = $j01_matric;
        $cliptubase->j01_numcgm = $j01_numcgm;
        $cliptubase->j01_idbql  = 1000000000;
        $cliptubase->j01_codave = "1";
        $cliptubase->j01_fracao = 0;
        $cliptubase->j01_areaprivativa = 0;
        $cliptubase->j01_tipoimovel = 2;

        $cliptubase->j01_tipoproprietario = $j01_tipoproprietario;
        $cliptubase->j01_distrito   = $j01_distrito;
        $cliptubase->j01_hectare    = $j01_hectare;
        $cliptubase->j01_situcad    = $j01_situcad;
        $cliptubase->j01_datacad    = date('Y-m-d', strtotime(str_replace('/', '-', $j01_datacad)));
        $cliptubase->j01_processo   = $j01_processo;
        $cliptubase->j01_incra      = $j01_incra;
        $cliptubase->j01_descrlocal = $j01_descrlocal;
        if ($sqlerro == false) {
            $cliptubase->alterar($j01_matric);
            $sErroMsg = $cliptubase->erro_msg;
            if ($cliptubase->erro_status == 0) {
                $sqlerro = true;
                $sErroMsg = $cliptubase->erro_msg;
            }
        }
        $clpercposserural->j166_matric = $j01_matric;
        $clpercposserural->j166_numcgm = $j01_numcgm;
        $clpercposserural->j166_percentual = !empty($j166_percentual) ? $j166_percentual : '0';
        if ($sqlerro == false) {
            $clpercposserural->alterar($j166_sequencial);
            $sErroMsg = $cliptubase->erro_msg;
            if ($clpercposserural->erro_status == 0) {
                $sqlerro = true;
                $sErroMsg = "Não foi possível cadastrar percentual da posse rural.";
            }
        }
    } else {


        $result = $cliptubase->sql_record($cliptubase->sql_query_file($j01_matric, "j01_unidade as j01_unidadeant"));
        db_fieldsmemory($result, 0);

        $cliptubase->j01_matric = $j01_matric;
        $cliptubase->j01_numcgm = $j01_numcgm;
        $cliptubase->j01_idbql = $j01_idbql;
        $cliptubase->j01_codave = "1";
        $cliptubase->j01_fracao = $j01_fracao;
        $cliptubase->j01_tipoproprietario = $j01_tipoproprietario;
        $cliptubase->j01_areaprivativa = $j01_areaprivativa;


        $sql_pessoa = "select * from tipoproprietario where j163_tipoproprietario = $j01_tipoproprietario";
        $result_pessoa = db_query($sql_pessoa);
        db_fieldsmemory($result_pessoa, 0);
        $cgmInstance = \CgmFactory::getInstanceByCgm($j01_numcgm);
        if ($cgmInstance instanceof CgmFisico && $j163_pesfisjur == 2) {
            db_msgbox("CGM deve ser de Pessoa Jurídica.");
            $sqlerro = true;
        } elseif ($cgmInstance instanceof CgmJuridico && $j163_pesfisjur == 1) {
            db_msgbox("CGM deve ser de Pessoa Física.");

            $sqlerro = true;
        }

        for ($anoMatricula = $anoRetroativoMatricula; $anoMatricula <= $anousu; $anoMatricula++) {

            if ($anoMatricula == $anoRetroativoMatricula) {
                if (empty($anoLimiteReplicaDados)) {
                    db_query("select fc_putsession('DB_anoretroativo', '{$anoRetroativoMatricula}')");
                } else {
                    db_query("select fc_putsession('DB_anoretroativo', '{$anoRetroativoMatricula} ? {$anoLimiteReplicaDados}')");
                }
            }

            if ($liberaCalculoRetroativo and $replicaDadosAnos == 1 and $anoMatricula <= $anoLimiteReplicaDados) {
                $calculoRetroativoIptuRepository->setAnoRetroativoMatricula($anoMatricula);
                $calculoRetroativoIptuRepository->getAlteraSearchPath();
            }
            db_inicio_transacao();

            //Verifica se existe Promitentes com o mesmo cgm nesta Matricula
            $resultPromitente = $clpromitente->sql_record("SELECT * FROM promitente WHERE j41_matric = {$j01_matric} and j41_numcgm = {$j01_numcgm};");
            //Verifica se existe Outro Propriet?rio com o mesmo cgm nesta Matricula
            $resultOutroProprietario = $clpropri->sql_record("SELECT * FROM propri WHERE j42_matric = {$j01_matric} and j42_numcgm = {$j01_numcgm};");

            if ($clpromitente->numrows > 0) {
                $sqlerro = true;
                $sErroMsg = "O cgm não pode ser o mesmo cadastrado como promitente nesta matricula.";
                db_msgbox($sErroMsg);
            } else if ($clpropri->numrows > 0) {
                $sqlerro = true;
                $sErroMsg = "O cgm não pode ser o mesmo cadastrado como proprietário secundário nesta matricula.";
                db_msgbox($sErroMsg);
            }

            $result = $cliptubase->sql_record($cliptubase->sql_query_file($j01_matric, "j01_unidade as j01_unidadeant"));
            db_fieldsmemory($result, 0);

            $cliptubase->j01_matric = $j01_matric;
            $cliptubase->j01_numcgm = $j01_numcgm;
            $cliptubase->j01_idbql = $j01_idbql;
            $cliptubase->j01_codave = "1";
            $cliptubase->j01_fracao = $j01_fracao;
            $cliptubase->j01_areaprivativa = $j01_areaprivativa;

            if ($cliptubase->j01_fracao > 100) {
                $sqlerro = true;
                $sErroMsg = "Fração do Lote maior que 100%";
            }

            if ($sqlerro == false) {
                $cliptubase->alterar($j01_matric);
                $sErroMsg = $cliptubase->erro_msg;
                if ($cliptubase->erro_status == 0) {
                    $sqlerro = true;
                    $sErroMsg = $cliptubase->erro_msg;
                }
            }

            if ($sqlerro == false) {
                $rMatricObs = db_query($clmatricobs->sql_query_file($j01_matric, 'j26_matric'));

                $clmatricobs->j26_matric = $j01_matric;
                $clmatricobs->j26_obs = $j26_obs;

                if (pg_num_rows($rMatricObs) > 0) {
                    $clmatricobs->alterar($j01_matric);
                } else {
                    if ($j26_obs != "" && $sqlerro == false) {
                        $clmatricobs->incluir($j01_matric);
                    }
                }

                if ($clmatricobs->erro_status === "0") {
                    $sqlerro = true;
                    $sErroMsg = $clmatricobs->erro_msg;
                }
            }

            if ($sqlerro == false) {
                $cliptuant->sql_record($cliptuant->sql_query_file($j01_matric, 'j40_matric'));
                $iRegistros = $cliptuant->numrows;

                $cliptuant->j40_refant = $j40_refant;
                $cliptuant->j40_registrocartografico = $registroCartografico;
                $cliptuant->j40_matric = $j01_matric;

                $sqlVerificaDuplicados = $cliptuant->sql_query_file(null, 'j40_matric', null, "j40_refant = '{$j40_refant}'");
                $resultVerificaDuplicados = $cliptuant->sql_record($sqlVerificaDuplicados);

                $erro = false;
                if (isParaiba() && pg_num_rows($resultVerificaDuplicados) > 0 && $j01_unidade != $j01_unidadeant) {
                    $erro = true;
                    $sqlerro = true;
                    $sErroMsg = "Unidade já cadastrada para essa inscrição imobiliária {$j40_refant}.";
                } else if ($iRegistros > 0) {
                    $cliptuant->alterar($j01_matric);

                    if (!$erro && $cliptuant->erro_status == 0) {
                        $sqlerro = true;
                        $sErroMsg = $cliptuant->erro_msg;
                    }
                } else {
                    if (($j40_refant != "" || $j40_registrocartografico != "") && $sqlerro == false) {
                        $cliptuant->incluir($j01_matric);

                        if (!$erro && $cliptuant->erro_status == 0) {
                            $sqlerro = true;
                            $sErroMsg = $cliptuant->erro_msg;
                        }
                    }
                }
            }

            // INCLUIR NA IPTUBASEREGIMOVEL ... SE TIVER REGISTROS
            if ($sqlerro == false) {
                $rIptubaseRegimovel = $cliptubaseregimovel->sql_record($cliptubaseregimovel->sql_query_file(null, "j04_sequencial", null, "j04_matric = $j01_matric"));

                $cliptubaseregimovel->j04_setorregimovel = $j04_setorregimovel;
                $cliptubaseregimovel->j04_matric = $j01_matric;
                $cliptubaseregimovel->j04_matricregimo = $j04_matricregimo;
                $cliptubaseregimovel->j04_quadraregimo = $j04_quadraregimo;
                $cliptubaseregimovel->j04_loteregimo = $j04_loteregimo;

                if ($cliptubaseregimovel->numrows > 0) {
                    $oIptubaseRegimovel = db_utils::fieldsMemory($rIptubaseRegimovel, 0);
                    $cliptubaseregimovel->j04_sequencial = $oIptubaseRegimovel->j04_sequencial;
                    $cliptubaseregimovel->alterar($oIptubaseRegimovel->j04_sequencial);
                } else {
                    if ($j04_setorregimovel != "") {
                        $cliptubaseregimovel->incluir(null);
                    }
                }

                if ($cliptubaseregimovel->erro_status === "0" && $j04_setorregimovel != "") {
                    $sqlerro = true;
                    $sErroMsg = $cliptubaseregimovel->erro_msg;
                }
            }

            //INCLUI NA IPTUBASEPREDIO OU IPTUBASECONDOMINIO
            if ($sqlerro == false) {
                if (isset($j107_sequencial) && trim($j107_sequencial) != "" && isset($predios) && $predios != 0) {
                    $rIptubasePredio = db_query($cliptubasepredio->sql_query_file(null, "j109_sequencial", null, " j109_matric = {$j01_matric} "));

                    $cliptubasepredio->j109_predio = $predios;
                    $cliptubasepredio->j109_matric = $j01_matric;

                    if (pg_num_rows($rIptubasePredio) > 0) {
                        $oIptunasePredio = db_utils::fieldsMemory($rIptubasePredio, 0);

                        $cliptubasepredio->alterar($oIptunasePredio->j109_sequencial);
                    } else {
                        $cliptubasepredio->incluir(null);
                    }

                    if ($cliptubasepredio->erro_status == 0) {
                        $sqlerro = true;
                        $sErroMsg = $cliptubasepredio->erro_msg;
                    }
                } else if (isset($j107_sequencial) && trim($j107_sequencial) != "") {
                    $rIptubaseCondominio = $cliptubasecondominio->sql_record($cliptubasecondominio->sql_query_file(null, "j108_sequencial", null, " j108_matric = {$j01_matric} "));

                    $cliptubasecondominio->j108_condominio = $j107_sequencial;
                    $cliptubasecondominio->j108_matric = $j01_matric;

                    if ($cliptubasecondominio->numrows > 0) {
                        $oIptubaseCondominio = db_utils::fieldsMemory($rIptubaseCondominio, 0);
                        $cliptubasecondominio->j108_sequencial = $oIptubaseCondominio->j108_sequencial;
                        $cliptubasecondominio->alterar($oIptubaseCondominio->j108_sequencial);
                    } else {
                        $cliptubasecondominio->incluir(null);
                    }

                    if ($cliptubasecondominio->erro_status == 0) {
                        $sqlerro = true;
                        $sErroMsg = $cliptubasecondominio->erro_msg;
                    }

                    //Deleta o vinculo do imovel com o condominio caso o usuario delete o codigo do condominio
                } else if (isset($j107_sequencial) && trim($j107_sequencial) == "") {
                    $sSqlBuscaCondominiosVinculados = $cliptubasecondominio->sql_query_file(null, "j108_sequencial", null, " j108_matric = {$j01_matric} ");
                    $rsBuscaCondominiosVinculados = $cliptubasecondominio->sql_record($sSqlBuscaCondominiosVinculados);
                    $aCondominiosVinculados = db_utils::getCollectionByRecord($rsBuscaCondominiosVinculados);

                    if (count($aCondominiosVinculados) > 0) {
                        foreach ($aCondominiosVinculados as $condominio) {
                            $cliptubasecondominio->excluir($condominio->j108_sequencial);
                        }

                        if ($cliptubasecondominio->erro_status == 0) {
                            $sqlerro = true;
                            $sErroMsg = $cliptubasecondominio->erro_msg;
                        }
                    }
                }
            }

            db_fim_transacao($sqlerro);
            $cliptubase->erro_msg = $sErroMsg;
            $cancela = $sqlerro;
            $db_opcao = 2;

            $sqlCondominio    = $cliptubasecondominio->sql_query_condominio(null, "*", null, "j108_matric = {$j01_matric}");
            $resultCondominio = $cliptubasecondominio->sql_record($sqlCondominio);
            $linhasreg        = $cliptubasecondominio->numrows;
            if ($linhasreg > 0) {
                db_fieldsmemory($resultCondominio, 0);
            }
        }
    }
} else if (isset($j01_matric) || isset($alterando)) {
    $wherepromitente = "promitente.j41_matric = {$j01_matric} and promitente.j41_tipopro = 't'";
    $sql = $clpromitente->sql_query($j01_matric, '', 'j41_promitipo#j41_numcgm#cgm.z01_nome as promitentenome', '', $wherepromitente);
    $resultpromitente = $clpromitente->sql_record($sql);
    if ($clpromitente->numrows != 0) {
        db_fieldsmemory($resultpromitente, 0);
    }

    $resultmatricobs = $clmatricobs->sql_record($clmatricobs->sql_query_file($j01_matric));
    if ($clmatricobs->numrows != 0) {
        db_fieldsmemory($resultmatricobs, 0);
    }
    $resultiptuant = $cliptuant->sql_record($cliptuant->sql_query_file($j01_matric));
    if ($cliptuant->numrows != 0) {
        db_fieldsmemory($resultiptuant, 0);
    }

    $camposIptubase = "j01_numcgm,j01_idbql,j01_codave,j01_fracao,j01_baixa,j01_unidade,z01_nome,j01_tipoproprietario,j01_areaprivativa,j34_area,ROUND(j01_fracaoproprietario, 4) as j01_fracaoproprietario";
    $result = $cliptubase->sql_record($cliptubase->sql_query($j01_matric, $camposIptubase, ""));
    @db_fieldsmemory($result, 0);
    $db_opcao = 2;
    $db_opcao_matric = 3;

    $sqlPercPosseRural = $clpercposserural->sql_query_file("", "*", "", "j166_matric = " . $j01_matric . " and j166_numcgm = " . $j01_numcgm);
    $rsPercPosseRural = $clpercposserural->sql_record($sqlPercPosseRural);
    if ($rsPercPosseRural && pg_num_rows($rsPercPosseRural) > 0) {
        db_fieldsmemory($rsPercPosseRural, 0);
    }

    $sqlreg = $cliptubaseregimovel->sql_query_file(null, "*", null, "j04_matric = {$j01_matric}");
    $resultreg = $cliptubaseregimovel->sql_record($sqlreg);
    $linhasreg = $cliptubaseregimovel->numrows;
    if ($linhasreg > 0) {
        db_fieldsmemory($resultreg, 0);
    }

    $sqlCondominio = $cliptubasecondominio->sql_query_condominio(null, "*", null, "j108_matric = {$j01_matric}");
    $resultCondominio = $cliptubasecondominio->sql_record($sqlCondominio);
    $linhasreg = $cliptubasecondominio->numrows;

    if ($linhasreg > 0) {
        db_fieldsmemory($resultCondominio, 0);
    }

    if ($linhasreg == 0) {
        $sCampos = "condominio.*, predio.*";
        $sqlPredio = $clcondominio->sql_query_predio(null, $sCampos, null, "j109_matric = {$j01_matric}");
        $resultPredio = $clcondominio->sql_record($sqlPredio);
        $linhasreg = $clcondominio->numrows;
        if ($linhasreg > 0) {
            db_fieldsmemory($resultPredio, 0);
        }
    }
}
?>

<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style type="text/css"></style>
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

    <form name="form1" method="post" onSubmit="return js_verifica_campos_digitados();" action="">
        <table height="430" align="center" width="790" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="center" valign="top" bgcolor="#CCCCCC">
                    <center>
                        <?php include(modification("forms/db_frmiptubasealt.php")); ?>
                    </center>
                </td>
            </tr>
        </table>
    </form>
</body>

<script>
    function recarregaPaginaMatricula(){
        this.location.reload()
    }
</script>

</html>
<?php
if (isset($incluir) || isset($alterar)) {

    if ($cliptubase->erro_status == 0 || $sqlerro == true) {

        $cliptubase->erro(true, false);
        if ($cliptubase->erro_campo != "") {
            echo "<script> document.form1." . $cliptubase->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $cliptubase->erro_campo . ".focus();</script>";
        }
    } else {
        if (!isset($cancela) || $cancela != true) {
            db_msgbox($cliptubase->erro_msg);
            echo " <script>
                 parent.document.form1.idmatricula.value=" . $cliptubase->j01_matric . "; \n           
                 parent.document.form1.nomematricula.value='$z01_nome'; \n          
                 parent.js_parentiframe('matricula',true);
                 if (document.formaba) {
                    document.formaba.ender.disabled = false;
                    mo_camada('ender',true,'Iframe8');
                    document.formaba.outros.disabled = false;
                    mo_camada('outros',true,'Iframe7');
                }
             </script> ";
            if (isset($incluir)) {
                $tipoimovel = isset($tipoImovel) ? $tipoImovel : $j01_tipoimovel;
                db_redireciona("cad1_iptubasealt.php?j01_matric=$cliptubase->j01_matric&tipoImovel=$tipoimovel");
            }
        }

        if (isset($alterar)) {
            $j01_matric = $_GET['j01_matric'];
            $tipoImovel = $_GET['j01_tipoimovel'];
            $testaentra = $_GET['testaentra'];
            $z01_nome = $_GET['z01_nome'];
            $entrar = $_GET['entrar'];

            echo "
            <script>
            parent.location.href='cad1_iptubase002.php?alteracao=true&tipoImovel={$tipoImovel}&testaentra={$testaentra}&j01_matric={$j01_matric}&z01_nome={$z01_nome}&entrar={$entrar}';
            </script>
            ";
        }
    }
}
