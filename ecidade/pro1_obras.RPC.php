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

/**
 * @fileoverview Controla Ações no cadastro de contrução da obra
 * @version   $Revision: 1.6 $
 * @revision  $Author: dbjeferson.belmiro $
 */


require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));

$oJson                = new services_json();
$oParam               = $oJson->decode(str_replace("\\","",$_POST["json"]));
$oRetorno             = new stdClass();
$oRetorno->lErro      = false;
$oRetorno->sMessage   = '';

$clobras = new cl_obras;
$clobrasresp = new cl_obrasresp;
$clobrastec = new cl_obrastec;
$clobrastecnicos = new cl_obrastecnicos;
$clobraspropri = new cl_obraspropri;
$clobrastiporesp = new cl_obrastiporesp;
$clobraslote = new cl_obraslote;
$clobraslotei = new cl_obraslotei;
$clobrasender = new cl_obrasender;
$clobrasprotprocesso = new cl_obrasprotprocesso;
$clobrasiptubase = new cl_obrasiptubase;
$cl_obrasoutrosprop = new cl_obrasoutrosprop;
$cl_cgm = new cl_cgm;
$clobrasender = new cl_obrasender;
$clobrasalvara = new cl_obrasalvara;
$clobrashabite = new cl_obrashabite;
$clobrasconstr = new cl_obrasconstr;
$cl_obrasalvaraprotprocesso = new cl_obrasalvaraprotprocesso;
$cl_obrassituacaolog = new cl_obrassituacaolog;

$sqlerro = false;
/**
 * Camada de Tentativas do RPC
 */

try {
db_inicio_transacao();

switch ($oParam->acao) {
    case "buscaRegistros":

        $retorno = $cl_obrasoutrosprop->sql_query_outrosprop(null, "ob32_numcgm, z01_nome", null, "ob32_codobra = {$oParam->codigo_obra}");
        $rsOutrosProp = $cl_obrasoutrosprop->sql_record($retorno);

        $numeroProp = pg_num_rows($rsOutrosProp);

        if ($numeroProp > 0) {

            for ($iNumero = 0; $iNumero < $numeroProp; $iNumero++) {       
                $oOutrosProprietarios = db_utils::fieldsMemory($rsOutrosProp, $iNumero);
                
                $oStdUsuario                 = new stdClass();
                $oStdUsuario->iCodigoUsuario = $oOutrosProprietarios->ob32_numcgm;
                $oStdUsuario->sNomeUsuario   = $oOutrosProprietarios->z01_nome;
                $aDestinatarioRetorno[]      = $oStdUsuario;
            }
        }

        $oRetorno->aUsuarios = $aDestinatarioRetorno;

    break;
        
    case "salvarRegistros":

        if ($oParam->ob01_nomeobra == '') {
            throw new Exception('Campo Nome da obra nao informado.');
        } else {
            $clobras->ob01_nomeobra = trim(db_stdClass::normalizeStringJsonEscapeString($oParam->ob01_nomeobra));
        }
        
        if ($oParam->ob01_dtobra == '') {
            throw new Exception('Campo Data da obra nao informado.');
        } else {
            $clobras->ob01_dtobra = $oParam->ob01_dtobra;
        }

        $clobras->ob01_tiporesp = $oParam->ob01_tiporesp;
        $clobras->ob01_regular = $oParam->ob01_regular;
        $clobras->ob01_processo = $oParam->ob01_processo;
        $clobras->ob01_nometitularproc = trim(db_stdClass::normalizeStringJsonEscapeString($oParam->z01_nome));
        $clobras->ob01_obs = trim(db_stdClass::normalizeStringJsonEscapeString($oParam->ob01_obs));
        $clobras->ob01_responsavelprojeto = $oParam->ob15_sequencial;
        $clobras->ob01_arquitetoobra = $oParam->ob01_arquitetoobra;
        $clobras->ob01_numeroartprojeto = $oParam->ob01_numeroartprojeto;
        $clobras->ob01_numerorrtprojeto = $oParam->ob01_numerorrtprojeto;
        $clobras->ob01_numeroarttecnico = $oParam->ob01_numeroarttecnico;
        $clobras->ob01_numerorrttecnico = $oParam->ob01_numerorrttecnico;
        
        $clobras->incluir($oParam->ob01_codobra);

        if ($clobras->erro_status == "0") {
            throw new Exception('Obra não foi cadastrada');
        } else {
            $ok = $clobras->erro_msg;
        }

        $ob01_codobra = $clobras->ob01_codobra;
        $ob03_numcgm = $oParam->ob03_numcgm;
        $ob10_numcgm = $oParam->ob10_numcgm;

        if ($oParam->ob01_regular == 't') {
            if ($oParam->j01_matric == '') {
                throw new Exception('Campo Matrícula do imóvel não informado.');
            } else {
                $j01_matric = $oParam->j01_matric;
            }        
        } else {
            $j01_matric = "";

            if ($oParam->ob06_setor == '') {
                throw new Exception('Campo setor do imóvel não informado.');
            } else {
                $ob06_setor = $oParam->ob06_setor;
            }
            if ($oParam->ob06_quadra == '') {
                throw new Exception('Campo quadra do imóvel não informado.');
            } else {
                $ob06_quadra = $oParam->ob06_quadra;
            }
            if ($oParam->ob06_lote == '') {
                throw new Exception('Campo lote do imóvel não informado.');
            } else {
                $ob06_lote = $oParam->ob06_lote;
            }
        }
        
        $ob01_processo = $oParam->ob01_processo;
        
        if ($sqlerro == false) {

            $clobraspropri->ob03_numcgm = $ob03_numcgm;
            $clobraspropri->incluir($ob01_codobra);

            if ($clobraspropri->erro_status == "0") {
                throw new Exception('Campo proprietário da obra nao informado.');
            }
        }

        if ($sqlerro == false) {
                $clobrasresp->ob10_numcgm = $ob10_numcgm;
                $clobrasresp->ob10_codobra = $ob01_codobra;
                $clobrasresp->incluir($ob01_codobra);

            if ($clobrasresp->erro_status == "0") {
                throw new Exception('Campo responsável da obra não informado.');
            }
        }

        if ($j01_matric != "" && $sqlerro == false) {
            $clobrasiptubase->ob24_obras = $ob01_codobra;
            $clobrasiptubase->ob24_iptubase = $j01_matric;
            $clobrasiptubase->incluir(null);
        } else {
            if ($sqlerro == false) {
                $clobraslotei->ob06_codobra  = $ob01_codobra;
                $clobraslotei->ob06_setor  = $ob06_setor;
                $clobraslotei->ob06_quadra = $ob06_quadra;
                $clobraslotei->ob06_lote = $ob06_lote;
                $clobraslotei->incluir($ob01_codobra);
            }
        }

        if ($sqlerro == false) {
            $clobrastecnicos->ob20_obrastec = $oParam->ob15_sequencial;
            $clobrastecnicos->ob20_codobra = $ob01_codobra;
            $clobrastecnicos->incluir($oParam->ob20_sequencial);

            if ($clobrastecnicos->erro_status == "0") {
                throw new Exception('Campo Tecnico nao Informado.');
            }
        }

        if ($sqlerro == false && $oParam->ob01_processosistema == 'S' && !empty($ob01_processo)) {
            $clobrasprotprocesso->ob25_obras = $ob01_codobra;
            $clobrasprotprocesso->ob25_protprocesso = $ob01_processo;
            $clobrasprotprocesso->incluir(null);
            if ($clobrasprotprocesso->erro_status == "0") {
                throw new Exception('Campo Cód. Processo nao Informado.');
            }
        }

        if ($sqlerro == false) {
            if ($oParam->aUsuarios != '') {
                $aUsuarioTela      = $oParam->aUsuarios; //array(1,2,3,4);

                foreach ($aUsuarioTela as $iCodigoUsuario) {
                    $cl_obrasoutrosprop->ob32_codobra = $ob01_codobra;
                    $cl_obrasoutrosprop->ob32_numcgm = $iCodigoUsuario;
                    $cl_obrasoutrosprop->incluir(null);
                }
            }
        }

        $oRetorno->codobra = $ob01_codobra;
        $oRetorno->status  = 1;

    break;

    case "alterarRegistros":

        $ob01_codobra = $oParam->ob01_codobra;
        $ob15_sequencial = $oParam->ob15_sequencial;
        $ob20_sequencial = $oParam->ob20_sequencial;

        if ($oParam->ob01_regular == 't') {
            if ($oParam->j01_matric == '') {
                throw new Exception('Campo Matrícula do imóvel não informado.');
            } else {
                $j01_matric = $oParam->j01_matric;
            }        
        } else {
            $j01_matric = "";

            if ($oParam->ob06_setor == '') {
                throw new Exception('Campo setor do imóvel não informado.');
            } else {
                $ob06_setor = $oParam->ob06_setor;
            }
            if ($oParam->ob06_quadra == '') {
                throw new Exception('Campo quadra do imóvel não informado.');
            } else {
                $ob06_quadra = $oParam->ob06_quadra;
            }
            if ($oParam->ob06_lote == '') {
                throw new Exception('Campo lote do imóvel não informado.');
            } else {
                $ob06_lote = $oParam->ob06_lote;
            }
        }

        $ob01_processo = $oParam->ob01_processo;
        $ob01_regular = $oParam->ob01_regular;
        $ob01_processosistema = $oParam->ob01_processosistema;
        $ob03_numcgm = $oParam->ob03_numcgm;

        $clobrasresp->ob10_numcgm = $oParam->ob10_numcgm;
        $clobrasresp->ob10_codobra = $ob01_codobra;
        $clobrasresp->alterar($ob01_codobra);

        $clobraspropri->ob03_numcgm = $ob03_numcgm;
        $clobraspropri->ob10_codobra = $ob01_codobra;
        $clobraspropri->excluir($ob01_codobra);
        $clobraspropri->incluir($ob01_codobra);

        if (isset($ob05_idbql) and $ob05_idbql != "") {
            $clobraslote->excluir($ob01_codobra);
            $clobraslote->incluir($ob01_codobra);
        } else {
            $clobraslotei->ob06_codobra = $ob01_codobra;
            $clobraslotei->ob06_setor   = $ob06_setor;
            $clobraslotei->ob06_quadra  = $ob06_quadra;
            $clobraslotei->ob06_lote = $ob06_lote;
            $clobraslotei->excluir($ob01_codobra);
            $clobraslotei->incluir($ob01_codobra);
        }

        $rsTecnicos = $clobrastecnicos->sql_record($clobrastecnicos->sql_query_file(null, "ob20_sequencial", "",
        "ob20_codobra = $ob01_codobra"));

        if ($clobrastecnicos->numrows > 0) {
            db_fieldsmemory($rsTecnicos, 0);

            $clobrastecnicos->ob20_sequencial = $ob20_sequencial;
            $clobrastecnicos->ob20_codobra = $ob01_codobra;
            $clobrastecnicos->ob20_obrastec = $ob15_sequencial;
            $clobrastecnicos->alterar($ob20_sequencial);
        } else {
            if (isset($ob15_sequencial) && trim($ob15_sequencial) != "") {
                $clobrastecnicos->ob20_codobra = $ob01_codobra;
                $clobrastecnicos->ob20_obrastec = $ob15_sequencial;
                $clobrastecnicos->incluir(null);
            }
        }

        if ($ob01_regular) {
            $rsObrasiptubase = $clobrasiptubase->sql_record($clobrasiptubase->sql_query_file(null,
            "*",
            null,
            "ob24_obras = {$ob01_codobra}"));

            if ($clobrasiptubase->numrows > 0) {
                $oObrasIptubase = db_utils::fieldsMemory($rsObrasiptubase, 0);
                $clobrasiptubase->ob24_sequencial = $oObrasIptubase->ob24_sequencial;
                $clobrasiptubase->ob24_obras = $oObrasIptubase->ob24_obras;
                $clobrasiptubase->ob24_iptubase = $j01_matric;
                $clobrasiptubase->alterar($clobrasiptubase->ob24_sequencial);
            } else {
                $clobrasiptubase->ob24_obras = $ob01_codobra;
                $clobrasiptubase->ob24_iptubase = $j01_matric;
                $clobrasiptubase->incluir(null);
            }
        }

        //verifica se e um processo do sistema
        $rsObrasProtProcesso = $clobrasprotprocesso->sql_record($clobrasprotprocesso->sql_query(null,
        "*",
        null,
        "ob25_obras = {$ob01_codobra}"));

        if ($clobrasprotprocesso->numrows > 0) {
            db_fieldsmemory($rsObrasProtProcesso, 0);
           
            if ($ob01_processosistema == 'S' && !empty($ob01_processo)) {
                $clobrasprotprocesso->ob25_sequencial = $ob25_sequencial;
                $clobrasprotprocesso->ob25_obras = $ob25_obras;
                $clobrasprotprocesso->ob25_protprocesso = $oParam->ob01_processo;
                $clobrasprotprocesso->alterar($ob25_sequencial);
            } else {
                $clobrasprotprocesso->excluir($ob25_sequencial);
            }
        } else {
            if ($ob01_processosistema == 'S' && !empty($ob01_processo)) {
                $clobrasprotprocesso->ob25_obras = $ob01_codobra;
                $clobrasprotprocesso->ob25_protprocesso = $ob01_processo;
                $clobrasprotprocesso->incluir(null);
            }
        }

        if ($sqlerro == false) {

            $cl_obrasoutrosprop->excluir("", "ob32_codobra = {$ob01_codobra}");

            if ($oParam->aUsuarios != '') {
                $aUsuarioTela = $oParam->aUsuarios;

                foreach ($aUsuarioTela as $iCodigoUsuario) {
                    $cl_obrasoutrosprop->ob32_codobra = $ob01_codobra;
                    $cl_obrasoutrosprop->ob32_numcgm = $iCodigoUsuario;
                    $cl_obrasoutrosprop->incluir($ob01_codobra);
                }
            }
        }

        $clobras->ob01_nomeobra = db_stdClass::normalizeStringJsonEscapeString($oParam->ob01_nomeobra);
        $clobras->ob01_tiporesp = $oParam->ob01_tiporesp;
        $clobras->ob01_regular = $oParam->ob01_regular;
        $clobras->ob01_dtobra = $oParam->ob01_dtobra;
        $clobras->ob01_processo = $oParam->ob01_processo;
        $clobras->ob01_nometitularproc = db_stdClass::normalizeStringJsonEscapeString($oParam->z01_nome);
        $clobras->ob01_obs = db_stdClass::normalizeStringJsonEscapeString($oParam->ob01_obs);
        $clobras->ob01_responsavelprojeto = $oParam->ob15_sequencial;
        $clobras->ob01_arquitetoobra = $oParam->ob01_arquitetoobra;
        $clobras->ob01_numeroartprojeto = $oParam->ob01_numeroartprojeto;
        $clobras->ob01_numerorrtprojeto = $oParam->ob01_numerorrtprojeto;
        $clobras->ob01_numeroarttecnico = $oParam->ob01_numeroarttecnico;
        $clobras->ob01_numerorrttecnico = $oParam->ob01_numerorrttecnico;
        
        if ($sqlerro == false) {
            $clobras->alterar($ob01_codobra);        
            $oRetorno->status  = 2;
        }

    break;

    case "excluirRegistros":

        $ob01_codobra = $oParam->ob01_codobra;

        $clobrastecnicos->excluir("", "ob20_codobra = $ob01_codobra");
        $clobrasresp->excluir($ob01_codobra);
        $clobraspropri->excluir($ob01_codobra);
        $clobraslote->excluir($ob01_codobra);
        $clobraslotei->excluir($ob01_codobra);
        $clobrasender->excluir("", "ob07_codobra = $ob01_codobra");
        $clobrasiptubase->excluir("", "ob24_obras = {$ob01_codobra}");
        $clobrasprotprocesso->excluir("", "ob25_obras = {$ob01_codobra}");
        $cl_obrasoutrosprop->excluir("", "ob32_codobra = {$ob01_codobra}");

        $res = $clobrasalvara->sql_record(
        $clobrasalvara->sql_query_file("", "*", "", " ob04_codobra = $ob01_codobra")
        );

        $num = $clobrasalvara->numrows;
     
        if ($clobrasalvara->numrows > 0) {
            for ($i = 0; $i < $num; $i++) {
                db_fieldsmemory($res, $i);
                $cl_obrassituacaolog->excluir('', " ob29_obras = $ob01_codobra");
                $cl_obrasalvaraprotprocesso->excluir('', " ob26_obrasalvara = $ob01_codobra");
                $clobrasalvara->excluir($ob01_codobra);
            }
        }
        

        $res = $clobrasconstr->sql_record(
        $clobrasconstr->sql_query_file("", "*", "", " ob08_codobra = $ob01_codobra")
        );
        
        $num = $clobrasconstr->numrows;

        if ($clobrasconstr->numrows > 0) {
            for ($i = 0; $i < $num; $i++) {
                db_fieldsmemory($res, $i);

                $clobrasconstrcaracter = new cl_obrasconstrcaracter;

                $clobrasconstrcaracter->excluir(null, " ob34_obrasconstr = $ob08_codconstr");

                $re = $clobrashabite->sql_record(
                $clobrashabite->sql_query_file("", "*", "", " ob09_codconstr = $ob08_codconstr")
                );

                if ($clobrashabite->numrows > 0) {
                    db_fieldsmemory($re, 0);
                    $clobrashabite->excluir($ob09_codhab);
                }

                $clobrasconstr->excluir($ob08_codconstr);
            }
        }

        if ($sqlerro == false) {   
            $clobras->excluir($ob01_codobra);
            $oRetorno->status  = 3;
        }
    break;

    }
    
    db_fim_transacao($sqlerro);
} catch (Exception $eErro) {

db_fim_transacao(true);
$oRetorno->lErro    = true;
$oRetorno->sMessage = urlencode($eErro->getMessage());
}

echo $oJson->encode($oRetorno);
