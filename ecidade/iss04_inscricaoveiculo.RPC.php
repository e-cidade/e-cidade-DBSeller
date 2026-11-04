<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (c) 2018  DBSeller Servicos de Informatica
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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

$parametros = JSON::requestParameters();

$retorno = new stdClass();
$retorno->status = 1;
$retorno->mensagem = '';

$daoIssbase = new cl_issbase();

try {
    db_inicio_transacao();

    switch ($parametros->executa) {
        case "salvarInscricao":

            $daoIssbase->q02_inscr = $parametros->q02_inscr;
            $daoIssbase->q02_numcgm = $parametros->q02_numcgm;
            $daoIssbase->q02_tiplic = '0';
            $daoIssbase->q02_capit = '0';
            $daoIssbase->q02_ultalt = date("Y-m-d");
            $daoIssbase->q02_dtalt = date("Y-m-d");
            $daoIssbase->q02_dtalt = date("Y-m-d");
            $daoIssbase->q02_formalocalvara = $parametros->selectformalocalvara;

            if (empty($parametros->q02_inscr)) {
                $daoIssbase->q02_dtinic = date("Y-m-d");
                $daoIssbase->q02_dtcada = date("Y-m-d");
                $daoIssbase->incluirNumeracaoContinua(null);
            } else {
                $daoIssbase->alterar($parametros->q02_inscr);
            }

            if ($daoIssbase->erro_status == '0') {
                throw new Exception("Erro ao salvar a inscrição.");
            }

            salvarBairro($daoIssbase->q02_inscr, $parametros);
            salvarRuas($daoIssbase->q02_inscr, $parametros);

            $retorno->q02_inscr =  $daoIssbase->q02_inscr;
            $retorno->mensagem = 'Cadastro efetuado com sucesso!';
            break;

        case "excluirInscricao":

            validarVinculoCalculo($parametros->q02_inscr);
            validarVinculoAlvara($parametros->q02_inscr);
            excluirBairro($parametros->q02_inscr);
            excluirRuas($parametros->q02_inscr);
            excluirAtividadePorInscricao($parametros->q02_inscr);

            if (!empty($parametros->q172_sequencial)) {
                excluirCondutorAuxiliar($parametros->q172_sequencial);
            }

            excluirVeiculo($parametros->q02_inscr);

            $rs = db_query("DELETE FROM issbaseintegracaoexterna WHERE q135_inscr = {$parametros->q02_inscr};");

            if (!$rs) {
                throw new Exception("Erro ao excluir vínculo da inscrição com a integração externa.");
            }

            db_query("ALTER TABLE issbase DISABLE TRIGGER tg_atualiza_issbaseintegracaoexterna;");
            $daoIssbase->excluir($parametros->q02_inscr);
            db_query("ALTER TABLE issbase ENABLE TRIGGER tg_atualiza_issbaseintegracaoexterna;");

            if ($daoIssbase->erro_status == '0') {
                throw new Exception("Erro ao excluir a inscrição.");
            }

            $retorno->mensagem = 'Cadastro removido com sucesso!';
            break;

        case "carregarAtividades":

            $cltabativ = new cl_tabativ;
            $dados = array();
            $inscricao = $parametros->q02_inscr;
            $campos  = "q07_seq,q07_val_ativ_int,q88_inscr,q03_ativ,q03_descr,q07_datain,q07_horaini,q07_horafim,";
            $campos .= "q07_datafi,q07_databx,q07_perman,q07_quant, q81_descr,q11_tipcalc";

            $rs = db_query($cltabativ->sql_query_atividade_inscr($inscricao,$campos, ""));

            if(pg_num_rows($rs) > 0){
                $dados = db_utils::getCollectionByRecord($rs);
            }

            $retorno->data = $dados;

            break;

        case "excluirAtividade":

            $daoTabAtividadeTipoCalculo = new cl_tabativtipcalc();
            $daoTabAtividadePrincipal = new cl_ativprinc();
            $daoTabAtividade = new cl_tabativ();
            $q07_inscr = $parametros->q07_inscr;
            $q07_seq = $parametros->q07_seq;
            $q07_ativ = $parametros->q07_ativ;


            //Deleta da tabela tipcalc
            $daoTabAtividadeTipoCalculo->q11_inscr=$q07_inscr;
            $daoTabAtividadeTipoCalculo->q11_seq=$q07_seq;
            $daoTabAtividadeTipoCalculo->excluir($q07_inscr,$q07_seq);

            if ($daoTabAtividadeTipoCalculo->erro_status == 0){
                throw new Exception(
                    "Erro ao excluir atividade. Exclusão tabativtipcalc : {$daoTabAtividadeTipoCalculo->erro_msg}"
                );
            }

            //Deleta da tabela atividade principal
            $daoTabAtividadePrincipal->excluir(null, "q88_inscr = {$q07_inscr} and q88_seq  = {$q07_seq}");
            if ($daoTabAtividadePrincipal->erro_status == 0) {
                throw new Exception(
                    "Erro ao excluir atividade. Excluir ativprinc : {$daoTabAtividadePrincipal->erro_msg}"
                );
            }

            //Atualiza data inicial da empresa para data de atividade com menor data de inicio
            $campo = 'min(q07_datain) as datainicial';
            $rs = $daoTabAtividade->sql_record($daoTabAtividade->sql_query_file(
                '',
                '',
                $campo,
                '',
                'q07_inscr = '.$q07_inscr.' and q07_ativ <> '.$q07_ativ.'')
            );

            if (pg_num_rows($rs) > 0) {
                $dataInicial = db_utils::fieldsmemory($rs,0)->datainicial;
                $daoIssbase->q02_inscr = $q07_inscr;
                $daoIssbase->q02_dtinic = $dataInicial;
                $daoIssbase->alterar($q07_inscr);
                if ($daoIssbase->erro_status == 0) {
                    throw new Exception(
                        "Erro ao excluir atividade. Alteração issbase : {$daoIssbase->erro_msg}"
                    );
                }
            }

            //Exclui da atividade da tabativ
            $daoTabAtividade->q07_inscr = $q07_inscr;
            $daoTabAtividade->q07_seq = $q07_seq;
            $daoTabAtividade->excluir($q07_inscr,$q07_seq);
            if ($daoTabAtividade->erro_status == 0) {
                throw new Exception(
                    "Erro ao excluir atividade. Exclusão tabativ : {$daoTabAtividade->erro_msg}"
                );
            }

            //Verifica se todas atividades da empresa foram baixadas, se sim atualiza data de baixa da empresa
            $daoTabAtividade->sql_record($daoTabAtividade->sql_query_file(
                "",
                "",
                "q07_inscr",
                "",
                "q07_databx is null and q07_inscr=$q07_inscr")
            );

            if ($daoTabAtividade->numrows < 1) {
                //Pega maior data de baixa para atualizar data de baixa da empresa
                $rs = $daoTabAtividade->sql_record($daoTabAtividade->sql_query_file(
                    $q07_inscr,
                    "",
                    "max(q07_databx) as q07_databx")
                );

                if (pg_num_rows($rs) > 0) {
                    $daoIssbase->q02_dtbaix = db_utils::fieldsmemory($rs,0)->q07_databx;
                    $daoIssbase->q02_inscr=$q07_inscr;
                    $daoIssbase->alterar($q07_inscr);
                    if ($daoIssbase->erro_status == 0) {
                        throw new Exception(
                            "Erro ao excluir atividade. Alteração issbase baixa : {$daoIssbase->erro_msg}"
                        );
                    }
                }
            }

            break;

        case "salvarAtividade":

            $cltabativtipcalc = new cl_tabativtipcalc();
            $clativprinc = new cl_ativprinc();
            $cltabativ = new cl_tabativ();
            $clIssAlvara = new cl_issalvara;
            $clparissqn = new cl_parissqn;

            $incluir = (empty($q07_seq)) ? true : false;
            $q07_seq = $parametros->q07_seq;
            $q07_ativ = $parametros->q07_ativ;
            $q07_perman = $parametros->q07_perman;
            $q07_inscr = $parametros->q07_inscr;
            $princ = $parametros->princ;
            $q07_quant = $parametros->q07_quant;
            $q07_datain = $parametros->q07_datain;
            $q07_datafi = $parametros->q07_datafi;
            $q07_horaini = $parametros->q07_horaini;
            $q07_horafim = $parametros->q07_horafim;
            $q07_val_ativ_int = $parametros->q07_val_ativ_int;

            if (empty($q07_ativ)) {
                throw new Exception("Atividade não informada.");
            }

            /**
             * Veridica se ja tem atividade permanente cadastrada ou caso nao seja permantente, verifica se a data fim cadastrada é maior que a data de inicio passada
             */
            $aWhereValidacaoData = array("q07_inscr = {$q07_inscr}", "q07_databx is null", "q07_ativ = {$q07_ativ}");

            if ($q07_perman == 'f') {
                $aWhereValidacaoData[] = "q07_datafi >= '{$q07_datain}'::date";
            } else {
                $aWhereValidacaoData[] = "q07_perman is true";
            }

            $sSqlValidacaoData = $cltabativ->sql_query_file(
                null,
                null,
                '*',
                null,
                implode(" and ", $aWhereValidacaoData)
            );

            $rsValidacaoData = $cltabativ->sql_record($sSqlValidacaoData);

            if ($cltabativ->numrows > 0) {
                if (empty($q07_seq) || db_utils::fieldsMemory($rsValidacaoData, 0)->q07_seq != $q07_seq) {
                    if ($q07_perman == 'f') {
                        $oDataFinalAtividade = new DBDate( db_utils::fieldsMemory($rsValidacaoData, 0)->q07_datafi );
                        $data = $oDataFinalAtividade->getDate(DBDate::DATA_PTBR);
                        throw new Exception("A data de início da atividade deve ser maior que {$data}.");
                    }
                    throw new Exception("A Atividade {$q07_ativ} já foi cadastrada como uma atividade permanente.");
                }
            }

            /**
             * Verifica se esta tentando incluir uma atividade diferente do tipo de alvara permanente/provisorio
             */
            $tipoperman = ($q07_perman == 'f') ? 'true' : 'false';
            $query = " q07_perman is {$tipoperman} and q07_inscr = $q07_inscr and q07_databx is null";
            if (!empty($q07_seq)) {
                $query .= " and q07_seq <> {$q07_seq}";
            }

            $rsPesquisaAtivTipo = $cltabativ->sql_record($cltabativ->sql_query_file($q07_inscr, '', '*', null, $query));

            if ($cltabativ->numrows > 0) {
                throw new Exception("Não é permitido inclusão de atividade permanente e provisório para o mesmo alvará.");
            }

            if (empty($q07_seq)) {
                $q07_seq = '1';
                //Pega o seq da atividade para aquele inscricao
                $rs = $cltabativ->sql_record($cltabativ->sql_query_file($q07_inscr,'','max(q07_seq)+1 as seq'));
                if(pg_num_rows($rs) > 0 && db_utils::fieldsmemory($rs, 0)->seq != ''){
                    $q07_seq = db_utils::fieldsmemory($rs, 0)->seq;
                }
            }

            //Salva os dados na tabativ
            $cltabativ->q07_inscr        = $q07_inscr;
            $cltabativ->q07_seq          = $q07_seq;
            $cltabativ->q07_ativ         = $q07_ativ;
            $cltabativ->q07_quant        = $q07_quant;
            $cltabativ->q07_perman       = $q07_perman;
            $cltabativ->q07_datain       = $q07_datain;
            $cltabativ->q07_horaini      = $q07_horaini;
            $cltabativ->q07_horafim      = $q07_horafim;
            $cltabativ->q07_val_ativ_int = $q07_val_ativ_int;
            $cltabativ->q07_datafi = ($q07_datafi != "") ? $q07_datafi : null;
            $cltabativ->q07_tipbx = "0";

            $GLOBALS["HTTP_POST_VARS"]["q07_datafi_dia"] = '';

            if ($incluir){
                $cltabativ->incluir($q07_inscr,$q07_seq);
            } else {
                $cltabativ->alterar($q07_inscr,$q07_seq);
            }

            if ($cltabativ->erro_status == 0) {
                throw new Exception("Erro ao salvar atividade. Inclusão tabativ : {$cltabativ->erro_msg}");
            }

            if (!empty($q11_tipcalc)) {
                $cltabativtipcalc->q11_inscr   = $q07_inscr;
                $cltabativtipcalc->q11_seq     = $q07_seq;
                $cltabativtipcalc->q11_tipcalc = $q11_tipcalc;
                if ($incluir){
                    $cltabativtipcalc->incluir($q07_inscr,$q07_seq);
                } else {
                    $cltabativtipcalc->alterar($q07_inscr,$q07_seq);
                }
                if ($cltabativtipcalc->erro_status == 0) {
                    throw new Exception("Erro ao salvar atividade. Inclusão tabativtipcalc : {$cltabativtipcalc->erro_msg}");
                }
            }

            //Se for atividade principal
            if ($princ == 't') {
                //Exclui atividade principal para aquela inscricao
                $clativprinc->q88_inscr = $q07_inscr;
                $clativprinc->excluir($q07_inscr);
                if ($clativprinc->erro_status == 0) {
                    throw new Exception("Erro ao salvar atividade. Exclusão ativprinc : {$clativprinc->erro_msg}");
                }

                //Insere novo registro para esse seq
                $clativprinc->q88_inscr = $q07_inscr;
                $clativprinc->q88_seq   = $q07_seq;
                $clativprinc->incluir($q07_inscr);
                if ($clativprinc->erro_status == 0) {
                    throw new Exception("Erro ao salvar atividade. Inclusao ativprinc : {$clativprinc->erro_msg}");
                }

                //================================================  GERAÇÂO AUTOMATICA DO ALVARA ======================================
                // verificamos nas classes se alguma das atividades está como principal para verificar o q12_alvaraautomatico

                $sSqlAlvaraAuto  = "select   q03_ativ, ";
                $sSqlAlvaraAuto .= "                 q12_alvaraautomatico, ";
                $sSqlAlvaraAuto .= "                 q12_classe, ";
                $sSqlAlvaraAuto .= "                 case ";
                $sSqlAlvaraAuto .= "                  when q88_inscr is not null then ";
                $sSqlAlvaraAuto .= "                     'sim' else ";
                $sSqlAlvaraAuto .= "                     'nao' ";
                $sSqlAlvaraAuto .= "                 end as principal, ";
                $sSqlAlvaraAuto .= "                 case ";
                $sSqlAlvaraAuto .= "                  when q07_perman is true then ";
                $sSqlAlvaraAuto .= "                     'sim' else ";
                $sSqlAlvaraAuto .= "                     'nao' ";
                $sSqlAlvaraAuto .= "                 end as permanente ";
                $sSqlAlvaraAuto .= "    from tabativ ";
                $sSqlAlvaraAuto .= "               left  join ativprinc on q88_inscr = q07_inscr ";
                $sSqlAlvaraAuto .= "                             and q88_seq = q07_seq ";
                $sSqlAlvaraAuto .= "               inner join ativid on ativid.q03_ativ = tabativ.q07_ativ ";
                $sSqlAlvaraAuto .= "         inner join clasativ on q82_ativ = q03_ativ ";
                $sSqlAlvaraAuto .= "         inner join classe on q82_classe = q12_classe ";
                $sSqlAlvaraAuto .= "    where q07_inscr = {$q07_inscr} ";

                $rsAlvaraAuto      = db_query($sSqlAlvaraAuto);
                $iLinhasAlvaraAuto = pg_num_rows($rsAlvaraAuto);
                $aAlvaraAuto       = array();
                $lGeraAutomatico   = 'true';
                $iTipoalvara       = "";

                if ($iLinhasAlvaraAuto > 0){

                    $aAlvaraAuto = db_utils::getCollectionByRecord($rsAlvaraAuto);
                    foreach ($aAlvaraAuto as $iIndice => $oValor) {
                        if ($oValor->principal == 'sim' && $oValor->q12_alvaraautomatico == 'f') {
                            $lGeraAutomatico = 'false';
                        }
                    }
                }

                // issalvara
                $sSqlExisteAlvara = $clIssAlvara->sql_query(null, "q123_sequencial", null, "q123_inscr = {$q07_inscr} and q123_situacao in (1,2)");
                $rsExisteAlvara   = $clIssAlvara->sql_record($sSqlExisteAlvara);
                $lInserirAlvara   = "true";
                if($clIssAlvara->numrows > 0){
                    $lInserirAlvara = "false";
                }

                //=======  VERIFICAMOS NA PARISSQN o tipo de alvara
                $sSqlTipoAlvara = $clparissqn->sql_query_file(null,"q60_isstipoalvaraper,q60_isstipoalvaraprov", null , null);
                $rsTipoAlvara   = $clparissqn->sql_record($sSqlTipoAlvara);
                $oTipoAlvara    = db_utils::fieldsMemory($rsTipoAlvara,0);


                if ($q07_perman == 't') {
                    $iTipoalvara = $oTipoAlvara->q60_isstipoalvaraper;
                } else {
                    $iTipoalvara = $oTipoAlvara->q60_isstipoalvaraprov;
                }

                if ($lInserirAlvara == 'true') {
                    $sDtInclusao     = implode("-", array_reverse(explode("/", $q07_datain)));
                    $clIssAlvara->q123_isstipoalvara = $iTipoalvara;  // valor a partir da parissqn
                    $clIssAlvara->q123_inscr         = $q07_inscr;
                    $clIssAlvara->q123_dtinclusao    = $sDtInclusao;
                    $clIssAlvara->q123_situacao      = 1;
                    $clIssAlvara->q123_usuario       = db_getsession("DB_id_usuario");
                    if ($lGeraAutomatico == 'true') {
                        $clIssAlvara->q123_geradoautomatico = "true";
                    } else {
                        $clIssAlvara->q123_geradoautomatico = "false";
                    }
                    $clIssAlvara->incluir(null);
                    if($clIssAlvara->erro_status == '0'){
                        throw new Exception($clIssAlvara->erro_msg);
                    }

                    // se a for tru a geração automatica, criamos um movimento para o alvara
                    if ($lGeraAutomatico == 'true') {

                        $iValidadeAlvara = '';

                        if (!empty($q07_datafi)) {
                            $iValidadeAlvara = quantDias($q07_datain, $q07_datafi);
                        }
                        $clIssMovAlvara->q120_codproc          = "";
                        $clIssMovAlvara->q120_issalvara        = $clIssAlvara->q123_sequencial;
                        $clIssMovAlvara->q120_isstipomovalvara = 1 ;// liberação
                        $clIssMovAlvara->q120_dtmov            = $sDtInclusao;
                        $clIssMovAlvara->q120_validadealvara   = $iValidadeAlvara;
                        $clIssMovAlvara->q120_usuario          = db_getsession("DB_id_usuario");
                        $clIssMovAlvara->q120_obs              = "GERACAO AUTOMATICA";
                        $clIssMovAlvara->incluir(null);
                        if($clIssMovAlvara->erro_status == '0'){
                            throw new Exception($clIssMovAlvara->erro_msg);
                        }
                    }
                }

            //Se nao for atividade principal
            } else {
                if($incluir){
                    //Se nao tem nenhuma atividade cadastrada para inscrição, força essa como principal
                    $clativprinc->sql_record($clativprinc->sql_query_file($q07_inscr));
                    if (!$clativprinc->numrows > 0) {
                        $clativprinc->q88_inscr = $q07_inscr;
                        $clativprinc->q88_seq   = $q07_seq;
                        $clativprinc->incluir($q07_inscr);
                        if ($clativprinc->erro_status==0) {
                            throw new Exception("Erro ao salvar atividade. Inclusao ativprinc : {$clativprinc->erro_msg}");
                        }
                    }
                } else {
                $clativprinc->sql_record($clativprinc->sql_query_file($q07_inscr,"q88_seq","","q88_inscr=$q07_inscr and  q88_seq=$q07_seq"));
                if ($clativprinc->numrows > 0) {
                    $clativprinc->q88_inscr=$q07_inscr;
                    $clativprinc->excluir($q07_inscr);
                    if($clativprinc->erro_status==0){
                        $erromsg = "Exclusão ativprinc : ".$clativprinc->erro_msg;
                        $sqlerro=true;
                    }
                }
                }

            }

            //Atualiza data inicial do alvara para a data mais baixa de atividades cadastradas
            $rs = $cltabativ->sql_record($cltabativ->sql_query_file('','','min(q07_datain) as datainicial',''," q07_inscr = {$q07_inscr}"));
            $datainicial = db_utils::fieldsmemory($rs,0)->datainicial;

            $daoIssbase->q02_inscr  = $q07_inscr;
            $daoIssbase->q02_dtinic = $datainicial;
            $daoIssbase->alterar($q07_inscr);

            if ($daoIssbase->erro_status == 0) {
                throw new Exception("Erro ao salvar atividade. Inclusao issbase : {$daoIssbase->erro_msg}");
            }

            //Busca os dados para atualizar a tela
            $dados = array();
            $campos  = "q07_seq,q07_val_ativ_int,q88_inscr,q03_ativ,q03_descr,q07_datain,q07_horaini,q07_horafim,";
            $campos .= "q07_datafi,q07_databx,q07_perman,q07_quant, q81_descr,q11_tipcalc";

            $rs = db_query($cltabativ->sql_query_atividade_inscr("", $campos, null, " q07_seq = {$q07_seq} and q07_inscr = {$q07_inscr} and q07_ativ = {$q07_ativ}"));
            $dados = db_utils::getCollectionByRecord($rs);

            $retorno->data = $dados;
            break;


        case 'validarCGMdoMunicipio':

            $retorno->j14_codigo = '';
            $retorno->j14_nome = '';
            $retorno->z01_numero = '';
            $retorno->z01_compl = '';
            $retorno->z01_cep = '';
            $retorno->z01_cxpostal = '';
            $retorno->j13_codi = '';
            $retorno->j13_descr = '';

            $camposRuas = "ruas.j14_codigo, ruas.j14_nome, cgm.z01_numero, cgm.z01_compl, cgm.z01_cep, cgm.z01_cxpostal";
            $daoCgmruas = new cl_db_cgmruas();
            $sqlRuas = $daoCgmruas->sql_query($parametros->q02_numcgm, $camposRuas);
            $rsRuas = db_query($sqlRuas);

            if (!$rsRuas) {
                throw new Exception("Erro ao buscar o logadroudo do município do CGM.");
            }

            if (pg_num_rows($rsRuas) > 0) {
                $retornoRuas = db_utils::fieldsMemory($rsRuas, 0);

                $retorno->j14_codigo = $retornoRuas->j14_codigo;
                $retorno->j14_nome = $retornoRuas->j14_nome;
                $retorno->z01_numero = $retornoRuas->z01_numero;
                $retorno->z01_compl = $retornoRuas->z01_compl;
                $retorno->z01_cep = $retornoRuas->z01_cep;
                $retorno->z01_cxpostal = $retornoRuas->z01_cxpostal;
            }

            $camposBairros = "bairro.j13_codi, bairro.j13_descr";
            $daoCgmBairros = new cl_db_cgmbairro();
            $sqlBairros = $daoCgmBairros->sql_query($parametros->q02_numcgm, $camposBairros);
            $rsBairros = db_query($sqlBairros);

            if (!$rsBairros) {
                throw new Exception("Erro ao buscar o bairro do município do CGM.");
            }

            if (pg_num_rows($rsBairros)) {
                $retornoBairros = db_utils::fieldsMemory($rsBairros, 0);

                $retorno->j13_codi = $retornoBairros->j13_codi;
                $retorno->j13_descr = $retornoBairros->j13_descr;
            }
            break;

        case 'buscarTipoAlvara':

            if (empty($parametros->q02_inscr)) {
                throw new Exception("Inscrição não informada");
            }

            $retorno->q98_sequencial = '';
            $retorno->q98_descricao = '';
            $retorno->q120_issalvara = '';

            $daoIssAlvara = new cl_issmovalvara();

            // $sql = $daoIssAlvara->sql_query(null, 'q98_sequencial, q98_descricao', null, "q123_inscr = {$parametros->q02_inscr}");
            $sql = $daoIssAlvara->sql_AlvaraLiberado(null, $parametros->q02_inscr, 'q98_sequencial, q98_descricao, q120_issalvara');
            $rs = db_query($sql);

            if (!$rs) {
                throw new Exception("Erro ao buscar o tipo de alvará.");
            }


            if (pg_num_rows($rs) > 0) {
                $dados = db_utils::fieldsMemory($rs, 0);
                $retorno->q98_sequencial = $dados->q98_sequencial;
                $retorno->q98_descricao = $dados->q98_descricao;
                $retorno->q120_issalvara = $dados->q120_issalvara;
            }
            break;

        default:
            throw new Exception('Nenhuma ação encontrada.');
            break;
    }

    db_fim_transacao(false);

} catch (Exception $erro){

    db_fim_transacao(true);
    $retorno->status = 2;
    $retorno->mensagem = $erro->getMessage();
}

$retorno->erro = $retorno->status == 2;
echo JSON::create()->stringify($retorno);


function salvarBairro($inscricao, $parametros)
{
    $daoIssBairro = new cl_issbairro();
    $daoIssBairro->q13_inscr = $inscricao;
    $daoIssBairro->q13_bairro = $parametros->q13_bairro;

    $sql = $daoIssBairro->sql_query_file($inscricao);
    $rs = db_query($sql);

    if (!$rs) {
        throw new Exception("Erro ao buscar o bairro vinculado a inscrição.");
    }

    if (pg_num_rows($rs) == 0) {
        $daoIssBairro->incluir($inscricao);
    } else {
        $daoIssBairro->alterar($inscricao);
    }

    if ($daoIssBairro->erro_status == '0') {
        throw new Exception("Erro ao vincular o bairro a inscrição.");
    }
}

function salvarRuas($inscricao, $parametros)
{
    $daoIssRuas = new cl_issruas();
    $daoIssRuas->q02_inscr = $inscricao;
    $daoIssRuas->j14_codigo = $parametros->j14_codigo;
    $daoIssRuas->q02_numero = $parametros->q02_numero;
    $daoIssRuas->q02_compl = $parametros->q02_compl;
    $daoIssRuas->q02_cxpost = $parametros->q02_cxpost;
    $daoIssRuas->z01_cep = $parametros->z01_cep;


    $sql = $daoIssRuas->sql_query_file($inscricao);
    $rs = db_query($sql);

    if (!$rs) {
        throw new Exception("Erro ao buscar a rua vinculada a inscrição.");
    }

    if (pg_num_rows($rs) == 0) {
        $daoIssRuas->incluir($inscricao);
    } else {
        $daoIssRuas->alterar($inscricao);
    }

    if ($daoIssRuas->erro_status == '0') {
        throw new Exception("Erro ao vincular a rua com a inscrição.");
    }
}

function excluirBairro($inscricao)
{
    $daoIssBairro = new cl_issbairro();
    $daoIssBairro->excluir($inscricao);

    if ($daoIssBairro->erro_status == '0') {
        throw new Exception("Erro ao excluir o bairro vinculado a inscrição.");
    }
}

function excluirRuas($inscricao)
{
    $daoIssRuas = new cl_issruas();
    $daoIssRuas->excluir($inscricao);

    if ($daoIssRuas->erro_status == '0') {
        throw new Exception("Erro ao excluir a rua vinculada a inscrição.");
    }
}

function excluirCondutorAuxiliar($codigoInscricaoVeiculo)
{
    $daoVeiculoCondutorAuxiliar = new cl_issveiculocondutorauxiliar();
    $daoVeiculoCondutorAuxiliar->excluir(null, "q173_issveiculo = {$codigoInscricaoVeiculo}");

    if ($daoVeiculoCondutorAuxiliar->erro_status == '0') {
        throw new Exception("Erro ao excluir o(s) condutor(es) auxiliar(es).");
    }
}

function excluirVeiculo($inscricao)
{
    $daoIssVeiculo = new cl_issveiculo();
    $daoIssVeiculo->excluir(null, "q172_issbase = {$inscricao}");

    if ($daoIssVeiculo->erro_status == '0') {
        throw new Exception("Erro ao excluir a inscrição de veículo.");
    }
}

function excluirAtividadePorInscricao($inscricao)
{
    $daoTabativtipcalc = new cl_tabativtipcalc();
    $daoTabativtipcalc->excluir($inscricao);

    if ($daoTabativtipcalc->erro_status == '0') {
        throw new Exception("Erro ao excluir a inscrição de veículo.");
    }

    $daoAtivprinc = new cl_ativprinc();
    $daoAtivprinc->excluir($inscricao);

    if ($daoAtivprinc->erro_status == '0') {
        throw new Exception("Erro ao excluir a atividade principal.");
    }

    $daoTabativ = new cl_tabativ();
    $daoTabativ->excluir($inscricao);

    if ($daoTabativ->erro_status == '0') {
        throw new Exception("Erro ao excluir a atividade.");
    }
}

function validarVinculoCalculo($inscricao)
{
    $daoArreinscr = new cl_arreinscr();
    $sql = $daoArreinscr->sql_query_file(null, $inscricao);
    $rs = db_query($sql);

    if (!$rs) {
        throw new Exception("Erro ao verificar se a inscrição já foi calculada.");
    }

    if (pg_num_rows($rs) > 0) {
        throw new Exception("Não é possível excluir a inscrição pois a mesma já possui cálculo.");
    }
}

function validarVinculoAlvara($inscricao)
{
    $daoIssalvara = new cl_issalvara();
    $sql = $daoIssalvara->sql_query_file(null, '1', null, "q123_inscr = {$inscricao}");
    $rs = db_query($sql);

    if (!$rs) {
        throw new Exception("Erro ao verificar se a inscrição já possui alvará.");
    }

    if (pg_num_rows($rs) > 0) {
        throw new Exception("Não é possível excluir a inscrição pois a mesma já possui alvará.");
    }
}

// func para retornar os dias entre datas
function quantDias($data1, $data2) {
  $aVet1=explode("/",$data1);
  $aVet2=explode("/",$data2);
  round((mktime(0, 0, 0, 1,10,2020) -  mktime(0, 0, 0, 1, 10, 2020)) / (24 * 60 * 60), 0);

  return round((mktime(0,0,0,$aVet2[1],$aVet2[0],$aVet2[2])-
                mktime(0,0,0,$aVet1[1],$aVet1[0],$aVet1[2])) / (24 * 60 * 60), 0);
}