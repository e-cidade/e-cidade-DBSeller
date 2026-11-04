<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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

use ECidade\RecursosHumanos\ESocial\Repository\ServidorAlteracao;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Transformer\S2205;
use ECidade\RecursosHumanos\Pessoal\Repository\MenorAprendizRepository;

require modification("libs/db_stdlib.php");
require modification("libs/db_conecta.php");
include modification("libs/db_sessoes.php");
include modification("libs/db_usuariosonline.php");
include modification("libs/db_utils.php");
include modification("classes/db_admissao_classe.php");
include modification("classes/db_rhpessoal_classe.php");
include modification("classes/db_rhparam_classe.php");
include modification("dbforms/db_funcoes.php");
include modification("classes/db_rhpesrescisao_classe.php");
include modification("classes/db_protprocesso_classe.php");

db_postmemory($_POST);

$cl_rhpesrescisao = new cl_rhpesrescisao;
$clprotprocesso = new cl_protprocesso;
$cladmissao  = new cl_admissao;
$clrhparam   = new cl_rhparam;
$clrhpessoal = new cl_rhpessoal;
$codigoInstituicao = db_getsession('DB_instit');

/** Código para extensão */
$clDadosAdmissionais = new cl_rhadmissaodado;
$clDeficiente = new cl_rhdeficiente;
$clEstagioVinculo = new cl_rhestagiovinculo;
$clVinculoAprendiz = new cl_rhvinculoaprendiz;
/** Fim do Código para extensão */

$db_opcao = 2;
$db_botao = false;
$sqlerro = false;

$iInstituicaoSessao = db_getsession('DB_instit');
$iAnoSessao         = db_getsession('DB_anousu');
$iMesSessao         = date('m', db_getsession("DB_datausu"));

$sWhereResc = "rh02_regist = " . $h07_regist . " and rh02_anousu = " . $iAnoSessao . " and rh02_mesusu = " . $iMesSessao . " and rh02_instit = " . $iInstituicaoSessao;
$rsRhPesRescisao = $cl_rhpesrescisao->sql_Record($cl_rhpesrescisao->sql_query_rescisao_data("", "rh05_recis", null, $sWhereResc));
$sDataResc = $rsRhPesRescisao == false ? null : (new DateTime(db_utils::fieldsMemory($rsRhPesRescisao, 0)->rh05_recis))->format('d/m/Y');

if(isset($incluir)){
  db_inicio_transacao();
  $cladmissao->incluir($h07_regist);
  if($cladmissao->erro_status==0){
      $sqlerro=true;
  }
  $erro_msg = $cladmissao->erro_msg;

  if($sqlerro == false){
    $resultDadosAdmissionais = $clDadosAdmissionais->sql_Record($clDadosAdmissionais->sql_Query(null, 'h25_sequencial', null, "h25_regist=$h07_regist and h25_instit = $codigoInstituicao"));
    // Processo Aposentadoria
    if ($h25_processoaposentadoria == 0) {
      $clDadosAdmissionais->h25_processoaposentadoria = '';
      $clDadosAdmissionais->h25_nrprocessoaposentadoria = '';
    } else {
      if ($h25_processoaposentadoria != "") {
        $clDadosAdmissionais->h25_processoaposentadoria = $h25_processoaposentadoria;
        $clDadosAdmissionais->h25_nrprocessoaposentadoria = $h25_nrprocessoaposentadoria;
      } else {
        $clDadosAdmissionais->h25_processoaposentadoria = '';
        $clDadosAdmissionais->h25_nrprocessoaposentadoria = $h25_nrprocessoaposentadoria;
      }
    }

    //Processo Exoneracao
    if ($h25_processoexoneracao == 0) {
        $clDadosAdmissionais->h25_processoexoneracao = '';
        $clDadosAdmissionais->h25_nrprocessoexoneracao = '';
    } else {
        if ($h25_processoexoneracao != '') {
            $clDadosAdmissionais->h25_processoexoneracao = $h25_processoexoneracao;
            $clDadosAdmissionais->h25_nrprocessoexoneracao = $h25_nrprocessoexoneracao;
        } else {
            $clDadosAdmissionais->h25_processoexoneracao = '';
            $clDadosAdmissionais->h25_nrprocessoexoneracao = $h25_nrprocessoexoneracao;
        }
    }

    // Processo Reintegracao
    if ($h25_processoreintegracao == 0) {
      $clDadosAdmissionais->h25_processoreintegracao = 0;
      $clDadosAdmissionais->h25_nrprocessoreintegracao = $h25_nrprocessoreintegracao;
    } else {
      if ($h25_processoreintegracao != '') {
        $clDadosAdmissionais->h25_processoreintegracao = $h25_processoreintegracao;
        $clDadosAdmissionais->h25_nrprocessoreintegracao = $h25_nrprocessoreintegracao;
      } else {
          $clDadosAdmissionais->h25_processoreintegracao = 0;
          $clDadosAdmissionais->h25_nrprocessoreintegracao = $h25_nrreintegracao;
      }
    }
    $clDadosAdmissionais->h25_irfonte = $h25_irfonte;
    $clDadosAdmissionais->h25_referenciair = '';
    if($h25_irfonte == 1){
      $clDadosAdmissionais->h25_referenciair = $h25_referenciair;
    }

    $clDadosAdmissionais->h25_regist = $h07_regist;
    $clDadosAdmissionais->h25_instit = db_getsession('DB_instit');

    $clDadosAdmissionais->h25_nomeacao = null;
    if (isset($h25_nomeacao) && !empty($h25_nomeacao)) {
      $clDadosAdmissionais->h25_nomeacao = "{$h25_nomeacao_ano}-{$h25_nomeacao_mes}-{$h25_nomeacao_dia}";
    }

    $clDadosAdmissionais->h25_portariaaposentadoria = $h25_portariaaposentadoria;
    $clDadosAdmissionais->h25_dataaposentadoria = null;
    if (isset($h25_dataaposentadoria) && !empty($h25_dataaposentadoria)) {
      $clDadosAdmissionais->h25_dataaposentadoria = "{$h25_dataaposentadoria_ano}-{$h25_dataaposentadoria_mes}-{$h25_dataaposentadoria_dia}";
    }
    $clDadosAdmissionais->h25_contaraposentadoria = null;
    if (isset($h25_contaraposentadoria) && !empty($h25_contaraposentadoria)) {
      $clDadosAdmissionais->h25_contaraposentadoria = "{$h25_contaraposentadoria_ano}-{$h25_contaraposentadoria_mes}-{$h25_contaraposentadoria_dia}";
    }

    $clDadosAdmissionais->h25_portariaexoneracao = $h25_portariaexoneracao;
    $clDadosAdmissionais->h25_dataexoneracao = null;
    if (isset($h25_dataexoneracao) && !empty($h25_dataexoneracao)) {
      $clDadosAdmissionais->h25_dataexoneracao = "{$h25_dataexoneracao_ano}-{$h25_dataexoneracao_mes}-{$h25_dataexoneracao_dia}";
    }
    if (isset($h25_contarexoneracao) && !empty($h25_contarexoneracao)) {
      $clDadosAdmissionais->h25_contarexoneracao = "{$h25_contarexoneracao_ano}-{$h25_contarexoneracao_mes}-{$h25_contarexoneracao_dia}";
    }

    $clDadosAdmissionais->h25_datareintegracao = null;
    if (isset($h25_datareintegracao) && !empty($h25_datareintegracao)) {
      $clDadosAdmissionais->h25_datareintegracao = "{$h25_datareintegracao_ano}-{$h25_datareintegracao_mes}-{$h25_datareintegracao_dia}";
    }

    $clDadosAdmissionais->h25_dtbase = null;
    if (isset($h25_dtbase) && !empty($h25_dtbase)) {
      $clDadosAdmissionais->h25_dtbase = $h25_dtbase;
    }

    $clDadosAdmissionais->h25_tiporeintegracao = null;
    if (isset($h25_tiporeintegracao) && !empty($h25_tiporeintegracao)) {
      $clDadosAdmissionais->h25_tiporeintegracao = $h25_tiporeintegracao;
    }

    $clDadosAdmissionais->h25_dataefetivoretorno = null;
    if (isset($h25_dataefetivoretorno) && !empty($h25_dataefetivoretorno)) {
      $clDadosAdmissionais->h25_dataefetivoretorno = $h25_dataefetivoretorno;
    }

    $clDadosAdmissionais->h25_numeroprocesso = null;
    if (isset($h25_numeroprocesso) && !empty($h25_numeroprocesso)) {
      $clDadosAdmissionais->h25_numeroprocesso = $h25_numeroprocesso;
    }

    $clDadosAdmissionais->h25_leianistia = null;
    if (isset($h25_leianistia) && !empty($h25_leianistia)) {
      $clDadosAdmissionais->h25_leianistia = $h25_leianistia;
    }

    if ($h07_tempor == 'f') {
      $h25_hipleg = null;
      $_POST['h25_hipleg'] = $h25_hipleg;
      $clDadosAdmissionais->h25_hipleg = $h25_hipleg;
    }else{
      if (isset($h25_hipleg) && !empty($h25_hipleg)) {
        $clDadosAdmissionais->h25_hipleg = $h25_hipleg;
      }
    }

    $clDadosAdmissionais->h25_nrdispositivo = $h25_nrdispositivo;
    $clDadosAdmissionais->h25_portariareintegracao = $h25_portariareintegracao;


    if ($clDadosAdmissionais->numrows > 0) {
      db_fieldsmemory($resultDadosAdmissionais, 0);
      $clDadosAdmissionais->alterar($h25_sequencial);
    } else {
      $clDadosAdmissionais->incluir(null);
    }

    if ($clDadosAdmissionais->erro_status==0) {
      $sqlerro=true;
    }
    $erro_msg = $clDadosAdmissionais->erro_msg;

    $clrhpessoal->rh01_regist = $h07_regist;
    $clrhpessoal->rh01_matriculaanterior = $rh01_matriculaanterior;
    $clrhpessoal->alterar($h07_regist);
    if($clrhpessoal->erro_status == 0) {
      $sqlerro=true;
    }
    $erro_msg = $clrhpessoal->erro_msg;
  }
  /** Fim Código para extensão */

  $sqlDeficiente = $clDeficiente->sql_query(null, 'rh253_sequencial', null, "rh253_matricula=$h07_regist and rh253_instit = $codigoInstituicao");
  $resultadoDeficiente = $clDeficiente->sql_record($sqlDeficiente);

  $clDeficiente->rh253_matricula = $h07_regist;
  $clDeficiente->rh253_fisica = $rh253_fisica;
  $clDeficiente->rh253_instit	= $codigoInstituicao;
  $clDeficiente->rh253_visual = $rh253_visual;
  $clDeficiente->rh253_auditiva = $rh253_auditiva;
  $clDeficiente->rh253_mental = $rh253_mental;
  $clDeficiente->rh253_intelectual = $rh253_intelectual;
  $clDeficiente->rh253_reabilitado = $rh253_reabilitado;
  $clDeficiente->rh253_cota = $rh253_cota;
  $clDeficiente->rh253_observacao	= $rh253_observacao	;

  if ($clDeficiente->numrows > 0) {
    db_fieldsmemory($resultadoDeficiente, 0);
    $clDeficiente->alterar($rh253_sequencial);
  } else {
    $clDeficiente->incluir(null);
  }

  if($clDeficiente->erro_status==0){
    $sqlerro=true;
  }
  $erro_msg = $clDeficiente->erro_msg;

  /*
   Preenche dados Estagiário
  */

  $sqlEstagio = $clEstagioVinculo->sql_query(null, 'rh260_sequencial', null, "rh260_matricula = $h07_regist");
  $resultadoEstagio = $clEstagioVinculo->sql_record($sqlEstagio);

  $clEstagioVinculo->rh260_matricula = $h07_regist;
  $clEstagioVinculo->rh260_naturezaestagio = $rh260_naturezaestagio;
  $clEstagioVinculo->rh260_nivelestagio = $rh260_nivelestagio;
  $clEstagioVinculo->rh260_dataterminoestagio = null;
  if(!isset($rh260_dataterminoestagio)) {
    $clEstagioVinculo->rh260_dataterminoestagio = $rh260_dataterminoestagio;
  }
  $clEstagioVinculo->rh260_cnpjinstensino = $rh260_cnpjinstensino;
  $clEstagioVinculo->rh260_cnpjagentintegracao = $rh260_cnpjagentintegracao;

  if ($clEstagioVinculo->numrows > 0) {
    db_fieldsmemory($resultadoEstagio, 0);
    $clEstagioVinculo->alterar($rh260_sequencial);
  } else {
    $clEstagioVinculo->incluir(null);
  }

  if($clEstagioVinculo->erro_status==0){
    $sqlerro=true;
  }
  $erro_msg = $clEstagioVinculo->erro_msg;

  db_fim_transacao($sqlerro);

}else if(isset($alterar)){
  db_inicio_transacao();
  $db_opcao = 2;
  $r = $cladmissao->sql_query_dados($h07_regist);
  $result = db_query("select * from admissao inner join rhpessoal on rhpessoal.rh01_regist = admissao.h07_regist inner join cgm on cgm.z01_numcgm = rhpessoal.rh01_numcgm where admissao.h07_regist =".$h07_regist);
  $linhas = pg_num_rows($result);

  $oServidor = ServidorRepository::getInstanciaByCodigo($h07_regist, DBPessoal::getAnoFolha(), DBPessoal::getMesFolha());
  if ($h07_justif !== $oServidor->getRegistroJustificativa()) {
    $dataAlteracaoESocial = date('Y-m-d');
    $servidorAlteracao = ServidorAlteracao::findMatriculaByLayout($h07_regist, Tipo::S2206);
    $servidorAlteracao->setDataS2206(new DBDate($dataAlteracaoESocial));
    $servidorAlteracao->save();
  }

    $menorAprendizAlteracao = MenorAprendizRepository::findByMatricula($h07_regist);
    // Caso ja seja menor aprendiz
    if (!empty($menorAprendiz)) {
        if (isset($rh312_modalidade) && !empty($rh312_modalidade)) {
            //Comparamos os dados
            if (($menorAprendizAlteracao->getModalidade() != $rh312_modalidade)
                || ($menorAprendizAlteracao->getCnpjDireta() != $rh312_cnpjqualificadora)
                || ($menorAprendizAlteracao->getCnpjIndireta() != $rh312_cnpjefetivada)
                || ($menorAprendizAlteracao->getCnpjPratica() != $rh312_pratica)
            ) {
                $dataAlteracaoESocial = date('Y-m-d');
                $servidorAlteracao = ServidorAlteracao::findMatriculaByLayout($h07_regist, Tipo::S2206);
                $servidorAlteracao->setDataS2206(new DBDate($dataAlteracaoESocial));
                $servidorAlteracao->save();
            }
        }
    } else {
        // caso esteja virando menor aprendiz
        if (isset($rh312_modalidade) && !empty($rh312_modalidade)) {
            $dataAlteracaoESocial = date('Y-m-d');
            $servidorAlteracao = ServidorAlteracao::findMatriculaByLayout($h07_regist, Tipo::S2206);
            $servidorAlteracao->setDataS2206(new DBDate($dataAlteracaoESocial));
            $servidorAlteracao->save();
        }
    }

  if ($linhas > 0){
    $cladmissao->alterar($h07_regist);
  } else {
    $cladmissao->incluir($h07_regist);
  }
  if ($cladmissao->erro_status==0) {
    $sqlerro=true;
  }
  $erro_msg = $cladmissao->erro_msg;

  /** Código para extensão */
  if($sqlerro == false){
    $resultDadosAdmissionais = $clDadosAdmissionais->sql_Record($clDadosAdmissionais->sql_Query(null, 'h25_sequencial', null, "h25_regist=$h07_regist and h25_instit = $codigoInstituicao"));
    // Processo Aposentadoria
    if ($h25_processoaposentadoria == 0) {
      $clDadosAdmissionais->h25_processoaposentadoria = '';
      $clDadosAdmissionais->h25_nrprocessoaposentadoria = '';
    } else {
      if ($h25_processoaposentadoria != "") {
        $clDadosAdmissionais->h25_processoaposentadoria = $h25_processoaposentadoria;
        $clDadosAdmissionais->h25_nrprocessoaregistsentadoria = '';
        $clDadosAdmissionais->h25_nrprocessoaposentadoria = $h25_nrprocessoaposentadoria;
      }
    }

    //Processo Exoneracao
    if ($h25_processoexoneracao == 0) {
        $clDadosAdmissionais->h25_processoexoneracao = '';
        $clDadosAdmissionais->h25_nrprocessoexoneracao = '';
    } else {
        if ($h25_processoexoneracao != '') {
            $clDadosAdmissionais->h25_processoexoneracao = $h25_processoexoneracao;
            $clDadosAdmissionais->h25_nrprocessoexoneracao = $h25_nrprocessoexoneracao;
        } else {
            $clDadosAdmissionais->h25_processoexoneracao = '';
            $clDadosAdmissionais->h25_nrprocessoexoneracao = $h25_nrprocessoexoneracao;
        }
    }

    // Processo Reintegracao
    if ($h25_processoreintegracao == 0) {
      $clDadosAdmissionais->h25_processoreintegracao = 0;
      $clDadosAdmissionais->h25_nrprocessoreintegracao = $h25_nrprocessoreintegracao;
    } else {
      if ($h25_processoreintegracao != '') {
        $clDadosAdmissionais->h25_processoreintegracao = $h25_processoreintegracao;
        $clDadosAdmissionais->h25_nrprocessoreintegracao = $h25_nrprocessoreintegracao;
      } else {
          $clDadosAdmissionais->h25_processoreintegracao = 0;
          $clDadosAdmissionais->h25_nrprocessoreintegracao = $h25_nrreintegracao;
      }
    }
    $clDadosAdmissionais->h25_irfonte = $h25_irfonte;
    $clDadosAdmissionais->h25_referenciair = '';
    if($h25_irfonte == 1){
      $clDadosAdmissionais->h25_referenciair = $h25_referenciair;
    }

    $clDadosAdmissionais->h25_regist = $h07_regist;
    $clDadosAdmissionais->h25_instit = db_getsession('DB_instit');

    $clDadosAdmissionais->h25_nomeacao = null;
    if (isset($h25_nomeacao) && !empty($h25_nomeacao)) {
      $clDadosAdmissionais->h25_nomeacao = "{$h25_nomeacao_ano}-{$h25_nomeacao_mes}-{$h25_nomeacao_dia}";
    }

    $clDadosAdmissionais->h25_portariaaposentadoria = $h25_portariaaposentadoria;
    $clDadosAdmissionais->h25_dataaposentadoria = null;
    if (isset($h25_dataaposentadoria) && !empty($h25_dataaposentadoria)) {
      $clDadosAdmissionais->h25_dataaposentadoria = "{$h25_dataaposentadoria_ano}-{$h25_dataaposentadoria_mes}-{$h25_dataaposentadoria_dia}";
    }
    $clDadosAdmissionais->h25_contaraposentadoria = null;
    if (isset($h25_contaraposentadoria) && !empty($h25_contaraposentadoria)) {
      $clDadosAdmissionais->h25_contaraposentadoria = "{$h25_contaraposentadoria_ano}-{$h25_contaraposentadoria_mes}-{$h25_contaraposentadoria_dia}";
    }

    $clDadosAdmissionais->h25_portariaexoneracao = $h25_portariaexoneracao;
    $clDadosAdmissionais->h25_dataexoneracao = null;
    if (isset($h25_dataexoneracao) && !empty($h25_dataexoneracao)) {
      $clDadosAdmissionais->h25_dataexoneracao = "{$h25_dataexoneracao_ano}-{$h25_dataexoneracao_mes}-{$h25_dataexoneracao_dia}";
    }
    if (isset($h25_contarexoneracao) && !empty($h25_contarexoneracao)) {
      $clDadosAdmissionais->h25_contarexoneracao = "{$h25_contarexoneracao_ano}-{$h25_contarexoneracao_mes}-{$h25_contarexoneracao_dia}";
    }

    $clDadosAdmissionais->h25_datareintegracao = null;
    if (isset($h25_datareintegracao) && !empty($h25_datareintegracao)) {
      $clDadosAdmissionais->h25_datareintegracao = "{$h25_datareintegracao_ano}-{$h25_datareintegracao_mes}-{$h25_datareintegracao_dia}";
    }

    $clDadosAdmissionais->h25_dtbase = null;
    if (isset($h25_dtbase) && !empty($h25_dtbase)) {
      $clDadosAdmissionais->h25_dtbase = $h25_dtbase;
    }

    $clDadosAdmissionais->h25_tiporeintegracao = null;
    if (isset($h25_tiporeintegracao) && !empty($h25_tiporeintegracao)) {
      $clDadosAdmissionais->h25_tiporeintegracao = $h25_tiporeintegracao;
    }

    $clDadosAdmissionais->h25_dataefetivoretorno = null;
    if (isset($h25_dataefetivoretorno) && !empty($h25_dataefetivoretorno)) {
      $clDadosAdmissionais->h25_dataefetivoretorno = $h25_dataefetivoretorno;
    }

    $clDadosAdmissionais->h25_numeroprocesso = null;
    if (isset($h25_numeroprocesso) && !empty($h25_numeroprocesso) && $clDadosAdmissionais->h25_tiporeintegracao == "1") {
      $clDadosAdmissionais->h25_numeroprocesso = $h25_numeroprocesso;
    }
    
    $clDadosAdmissionais->h25_leianistia = null;
    if (isset($h25_leianistia) && !empty($h25_leianistia) && $clDadosAdmissionais->h25_tiporeintegracao == "2") {
      $clDadosAdmissionais->h25_leianistia = $h25_leianistia;
    }
    
    $numeroProcesso = $clDadosAdmissionais->h25_numeroprocesso ? $clDadosAdmissionais->h25_numeroprocesso : null;
    $anistia = $clDadosAdmissionais->h25_leianistia ? $clDadosAdmissionais->h25_leianistia : null;
    
    if ($h07_tempor == 'f') {
      $h25_hipleg = null;
      $_POST['h25_hipleg'] = $h25_hipleg;
      $clDadosAdmissionais->h25_hipleg = $h25_hipleg;
    }else{
      if (isset($h25_hipleg) && !empty($h25_hipleg)) {
        $clDadosAdmissionais->h25_hipleg = $h25_hipleg;
      }
    }


    $clDadosAdmissionais->h25_nrdispositivo = $h25_nrdispositivo;
    $clDadosAdmissionais->h25_portariareintegracao = $h25_portariareintegracao;


    if ($clDadosAdmissionais->numrows > 0) {
      db_fieldsmemory($resultDadosAdmissionais, 0);
      $clDadosAdmissionais->alterar($h25_sequencial);
      alteraRhAdmissao($h25_sequencial, $numeroProcesso, $anistia);
    } else {
        $clDadosAdmissionais->incluir(null);
    }
    if($clDadosAdmissionais->erro_status==0){
      $sqlerro=true;
    }
    $erro_msg = $clDadosAdmissionais->erro_msg;

    $sqlDeficiente = $clDeficiente->sql_query(null, '*', null, "rh253_matricula=$h07_regist and rh253_instit = $codigoInstituicao");
    $resultadoDeficiente = $clDeficiente->sql_record($sqlDeficiente);

      $dadosAtuais = pg_fetch_object($resultadoDeficiente,0);

      // Registra alteração para envio do formulário S2205
      foreach(S2205::getCamposControleAlteracao() as $campo){
          if(isset($$campo)){
              if(isset($dadosAtuais->$campo)){
                  if( $dadosAtuais->$campo != $$campo){
                      $servidorAlteracao = ServidorAlteracao::findMatriculaByLayout($h07_regist, Tipo::S2205);
                      $servidorAlteracao->setDataS2205(new DBDate(date('Y-m-d')));
                      $servidorAlteracao->save();

                      break;
                  }
              }
          }
      }

    $clDeficiente->rh253_matricula = $h07_regist;
    $clDeficiente->rh253_fisica = $rh253_fisica;
    $clDeficiente->rh253_instit	= $codigoInstituicao;
    $clDeficiente->rh253_visual = $rh253_visual;
    $clDeficiente->rh253_auditiva = $rh253_auditiva;
    $clDeficiente->rh253_mental = $rh253_mental;
    $clDeficiente->rh253_intelectual = $rh253_intelectual;
    $clDeficiente->rh253_reabilitado = $rh253_reabilitado;
    $clDeficiente->rh253_cota = $rh253_cota;
    $clDeficiente->rh253_observacao = $rh253_observacao;

    if ($clDeficiente->numrows > 0) {
      $clDeficiente->alterar($dadosAtuais->rh253_sequencial);
    } else {
      $clDeficiente->incluir(null);
    }

    if($clDeficiente->erro_status==0){
      $sqlerro=true;
    }
    $erro_msg = $clDeficiente->erro_msg;

    /**
     * Altera dados do Estagiário
     */

    // Remover o (*)
    $sqlEstagio = $clEstagioVinculo->sql_query(null, 'rh260_sequencial', null, "rh260_matricula=$h07_regist");
    $resultadoEstagio = $clEstagioVinculo->sql_record($sqlEstagio);

    $clEstagioVinculo->rh260_matricula = $h07_regist;
    $clEstagioVinculo->rh260_naturezaestagio = $rh260_naturezaestagio;
    $clEstagioVinculo->rh260_nivelestagio = $rh260_nivelestagio;
    $clEstagioVinculo->rh260_dataterminoestagio = null;
    if (!isset($rh260_dataterminoestagio)) {
      $clEstagioVinculo->rh260_dataterminoestagio = $rh260_dataterminoestagio;
    }
    $clEstagioVinculo->rh260_cnpjinstensino = $rh260_cnpjinstensino;
    $clEstagioVinculo->rh260_cnpjagentintegracao = $rh260_cnpjagentintegracao;
    $clEstagioVinculo->rh260_areaatuacao = $rh260_areaatuacao;
    $clEstagioVinculo->rh260_apoliceseguro = $rh260_apoliceseguro;
    $clEstagioVinculo->rh260_cpfsupervisor = $rh260_cpfsupervisor;
    if ($clEstagioVinculo->numrows > 0) {
      db_fieldsmemory($resultadoEstagio, 0);
      validaAlterarEstagiarioS2306($rh260_sequencial,
                                   $rh260_naturezaestagio,
                                   $rh260_nivelestagio,
                                   $rh260_cnpjinstensino,
                                   $rh260_cnpjagentintegracao,
                                   $rh260_dataterminoestagio,
                                   $rh260_areaatuacao,
                                   $rh260_apoliceseguro,
                                   $rh260_cpfsupervisor);
      $clEstagioVinculo->alterar($rh260_sequencial);
    } else {
      $clEstagioVinculo->incluir(null);
    }

    if($clEstagioVinculo->erro_status==0){
      $sqlerro=true;
    }
    $erro_msg = $clEstagioVinculo->erro_msg;

  }
  $clrhpessoal->rh01_regist = $h07_regist;
  $clrhpessoal->rh01_matriculaanterior = $rh01_matriculaanterior;
  $clrhpessoal->alterar($h07_regist);
  if($clrhpessoal->erro_status == 0) {
      $sqlerro=true;
  }
  $erro_msg = $clrhpessoal->erro_msg;
  /** Fim Código para extensão */
   db_fim_transacao($sqlerro);

} /** Código para extensão */
else if(isset($h07_regist) && trim($h07_regist) != ""){
  $db_botao = true;
  db_inicio_transacao();

  $resultAdmissao = $cladmissao->sql_record($cladmissao->sqlQueryAdmissao($h07_regist));

  if ($cladmissao->numrows > 0) {
    db_fieldsmemory($resultAdmissao,0);
  }else{
    $db_opcao = 1;
  }
  $resultDadosAdmissionais = $clDadosAdmissionais->sql_record($clDadosAdmissionais->sql_query(null, '*', null, "h25_regist=$h07_regist and h25_instit = $codigoInstituicao"));
    $processoreintegracao = 0;
    $processoexoneracao = 0;
    $processoaposentadoria = 0;
  if ($clDadosAdmissionais->numrows > 0) {
    db_fieldsmemory($resultDadosAdmissionais, 0);

    // Processos das Exoneracoes do sistema (Sim/Nao)
    if (!empty($h25_processoexoneracao)) {
        $processoexoneracao = 1;
      $where  = " p58_codproc = ".$h25_processoexoneracao;
      $where .= " and p58_instit  = ".db_getsession('DB_instit');
      $result = $clprotprocesso->sql_record($clprotprocesso->sql_query("","p58_codproc,cast(p58_numero||'/'||p58_ano as varchar) as h25_nrprocessoexoneracao,z01_nome as p58_requer_h25_processoexoneracao","p58_dtproc desc",$where));
      db_fieldsmemory($result,0);
    } else if ($h25_nrprocessoexoneracao != '') {
      $processoexoneracao = 1;
    }

    //Processos das Aposentadorias do sistema (Sim/Nao)
    if (!empty($h25_processoaposentadoria)) {
      $processoaposentadoria = 1;
      $where  = " p58_codproc = ".$h25_processoaposentadoria;
      $where .= " and p58_instit  = ".db_getsession('DB_instit');
      $slq = $clprotprocesso->sql_query("","p58_codproc,cast(p58_numero||'/'||p58_ano as varchar) as h25_numeroaposentadoria,z01_nome as p58_requer_h25_processoaposentadoria","p58_dtproc desc",$where);
      $result2 = $clprotprocesso->sql_record($slq);
      db_fieldsmemory($result2,0);
    } else if ($h25_nrprocessoaposentadoria != '') {
      $processoaposentadoria   = 1;
    }
        //Processos das reintegracao do sistema (Sim/Nao)
        if (!empty($h25_processoreintegracao)) {
            $where = " p58_codproc = ".$h25_processoreintegracao;
            $where .= " and p58_instit  = ".db_getsession('DB_instit');

            $sql = $clprotprocesso->sql_query("","p58_codproc,cast(p58_numero||'/'||p58_ano as varchar) as h25_nrreintegracao,z01_nome as p58_requer_h25_processoreintegracao","p58_dtproc desc",$where);
            $result3 = $clprotprocesso->sql_record($sql);

            db_fieldsmemory($result3,0);
            $processoreintegracao = 1;
        } else if($h25_nrprocessoreintegracao != '') {
            $processoreintegracao = 1;
        }
  } else {
      $h25_nrdispositivo = '';
      $h25_nomeacao = null;
      $h25_irfonte = 0;
      $h25_referenciair = '';
      $h25_portariaaposentadoria ='';
      $h25_dataaposentadoria = null;
      $h25_contaraposentadoria = null;
      $h25_processoaposentadoria = 0;
      $h25_nrprocessoaposentadoria = '';
      $h25_anoprocessoaposentadoria = 0;
      $h25_portariaexoneracao = '';
      $h25_dataexoneracao = null;
      $h25_contarexoneracao = null;
      $h25_processoexoneracao = 0;
      $h25_nrprocessoexoneracao = '';
      $h25_anoprocessoexoneracao = 0;
      $h25_portariareintegracao = '';
      $h25_datareintegracao = null;
      $h25_processoreintegracao = 0;
      $h25_nrprocessoreintegracao = '';
      $h25_anoprocessoreintegracao = 0;
      $h25_regist = 0;
      $h25_instit = 0;
      $h25_publicacaoexoneracao = null;
      $h25_nomeacao_dia = '';
      $h25_nomeacao_mes = '';
      $h25_nomeacao_ano = '';
      $h25_dataexoneracao_dia = '';
      $h25_dataexoneracao_mes = '';
      $h25_dataexoneracao_ano = '';
      $h25_contarexoneracao_dia = '';
      $h25_contarexoneracao_mes = '';
      $h25_contarexoneracao_ano = '';
      $h25_datareintegracao_dia = '';
      $h25_datareintegracao_mes = '';
      $h25_datareintegracao_ano = '';
      $h25_dtbase = '';
      $h25_hipleg = '';
  }

  $sqlDeficiente = $clDeficiente->sql_query(null, '*', null, "rh253_matricula=$h07_regist and rh253_instit = $codigoInstituicao");
  $resultadoDeficiente = $clDeficiente->sql_record($sqlDeficiente);

  if ($clDeficiente->numrows > 0) {
    db_fieldsmemory($resultadoDeficiente, 0);
  } else {
    $rh253_visual	= 'f';
    $rh253_auditiva	= 'f';
    $rh253_mental	= 'f';
    $rh253_intelectual= 'f';
    $rh253_reabilitado= 'f';
    $rh253_cota		   = 'f';
    $rh253_observacao	= '';
    $rh253_matricula = $h07_regist;
    $rh253_instit = $codigoInstituicao;
  }

  /*
  Preenche dados Caso Servidor seja Estagiário
  */

  $sqlEstagio = $clEstagioVinculo->sql_query(null, '*', null, "rh260_matricula=$h07_regist");
  $resultadoEstagio = $clEstagioVinculo->sql_record($sqlEstagio);

  if($clEstagioVinculo->numrows > 0) {
    db_fieldsmemory($resultadoEstagio, 0);
  } else {
    $rh260_naturezaestagio = '';
    $rh260_nivelestagio = '';
    $rh260_dataterminoestagio = null;
    $rh260_cnpjinstensino = '';
    $rh260_cnpjagentintegracao = '';
    $rh260_matricula = $h07_regist;
    $rh260_dataterminoestagio_dia = '';
    $rh260_dataterminoestagio_mes = '';
    $rh260_dataterminoestagio_ano = '';
    $rh260_areaatuacao = '';
    $rh260_apoliceseguro = '';
    $rh260_cpfsupervisor = '';
  }

    /**
     * Preenche dados Caso Servidor seja Menor Aprendiz
     */

    $sqlMenorAprendiz = $clVinculoAprendiz->sql_query(null, '*', null, "rh312_matricula=$h07_regist");
    $rsMenorAprendiz = $clVinculoAprendiz->sql_record($sqlMenorAprendiz);

    if ($clVinculoAprendiz->numrows > 0) {
        db_fieldsmemory($rsMenorAprendiz, 0);
    }



  $result_funcao = $clrhpessoal->sql_record($clrhpessoal->sql_query_cargo($h07_regist, " *, rh37_descr as rh37_descr2 "));

  if($clrhpessoal->numrows > 0){
    db_fieldsmemory($result_funcao, 0);
  }
  db_fim_transacao();
} /** Fim Código para extensão */
else if (isset($chavepesquisa)) {
  $db_opcao = 2;
  $db_botao = true;
  $result = $cladmissao->sql_record($cladmissao->sql_query_dados($chavepesquisa));
  if($cladmissao->numrows > 0){
    db_fieldsmemory($result,0);
    $result_funcao = $clrhpessoal->sql_record($clrhpessoal->sql_query_cargo($h07_regist, " rh37_funcao, rh37_descr as rh37_descr2 "));
    if($clrhpessoal->numrows > 0){
      db_fieldsmemory($result_funcao, 0);
    }
  }
}

 $rsConsultaModelo = $clrhparam->sql_record($clrhparam->sql_query_file(null,"h36_modtermoposse",null,"h36_instit = ".db_getsession("DB_instit")));

 if($clrhparam->numrows > 0){
 	$oParam  = db_utils::fieldsMemory($rsConsultaModelo,0);
 	$modeloposse = $oParam->h36_modtermoposse;
 }

 /**
 * @param $seqpes
 * @param $naturezaestagio
 * @param $nivelestagio
 *
 * @param $cnpjinstensino
 * @param $cnpjagentintegracao
 * Valida se se houve alteração contratual do estágio. S2306.
 */
function validaAlterarEstagiarioS2306($seqpes = null,
                                      $naturezaestagio = null,
                                      $nivelestagio = null,
                                      $cnpjinstensino = null,
                                      $cnpjagentintegracao = null,
                                      $dataterminoestagio = null,
                                      $rh260_areaatuacao = null,
                                      $rh260_apoliceseguro = null,
                                      $rh260_cpfsupervisor = null) {
  $alteraS2306 = false;
  $daoEstagioVinculo = new cl_rhestagiovinculo;

  //REGISTRO DO ESTAGIÁRIO
  $sqlEstagioVinculo = $daoEstagioVinculo->sql_query($seqpes,
                                              'rh260_naturezaestagio,
                                              rh260_nivelestagio,
                                              rh260_cnpjinstensino,
                                              rh260_cnpjagentintegracao,
                                              rh260_matricula,
                                              rh260_dataterminoestagio,
                                              rh260_areaatuacao,
                                              rh260_apoliceseguro,
                                              rh260_cpfsupervisor');
  $resultadoEstagioVinculo = $daoEstagioVinculo->sql_record($sqlEstagioVinculo);
  if ($daoEstagioVinculo->numrows > 0) {
    $daoEstagioVinculo = db_utils::fieldsMemory($resultadoEstagioVinculo, 0);
  }

  if (!$alteraS2306) {
    if ($daoEstagioVinculo->rh260_naturezaestagio != $naturezaestagio) {
      $alteraS2306 =true;
    }
  }
  if (!$alteraS2306) {
    if ($daoEstagioVinculo->rh260_nivelestagio != $nivelestagio) {
      $alteraS2306 =true;
    }
  }
  if (!$alteraS2306) {
    if ($daoEstagioVinculo->rh260_cnpjinstensino != $cnpjinstensino) {
      $alteraS2306 =true;
    }
  }
  if (!$alteraS2306) {
    if ($daoEstagioVinculo->rh260_cnpjagentintegracao != $cnpjagentintegracao) {
      $alteraS2306 =true;
    }
  }
  if (!$alteraS2306) {
    if ($daoEstagioVinculo->rh260_areaatuacao != $rh260_areaatuacao) {
      $alteraS2306 =true;
    }
  }
  if (!$alteraS2306) {
    if ($daoEstagioVinculo->rh260_apoliceseguro != $rh260_apoliceseguro) {
      $alteraS2306 =true;
    }
  }
  if (!$alteraS2306) {
    if ($daoEstagioVinculo->rh260_cpfsupervisor != $rh260_cpfsupervisor) {
      $alteraS2306 =true;
    }
  }
  if (!$alteraS2306) {
    if (!empty($dataterminoestagio)) {
        //fazemos a conversao do input
        $dataterminoestagio = explode('/', $dataterminoestagio);
        $dataterminoestagio = "{$dataterminoestagio[2]}-{$dataterminoestagio[1]}-{$dataterminoestagio[0]}";
    }
    if ($daoEstagioVinculo->rh260_dataterminoestagio != $dataterminoestagio) {
      $alteraS2306 =true;
    }
  }
  //INCLUSÃO
  if (empty($daoEstagioVinculo->rh260_naturezaestagio)
    && empty($daoEstagioVinculo->rh260_nivelestagio)
    && empty($daoEstagioVinculo->rh260_cnpjinstensino)
    && empty($daoEstagioVinculo->rh260_cnpjagentintegracao)
    && empty($daoEstagioVinculo->rh260_areaatuacao)
    && empty($daoEstagioVinculo->rh260_apoliceseguro)
    && empty($daoEstagioVinculo->rh260_cpfsupervisor)){
      $alteraS2306 =false;
  }

  if ($alteraS2306) {
    $dataAlteracaoESocial = date('Y-m-d');
    $servidorAlteracao = ServidorAlteracao::findMatriculaByLayout($daoEstagioVinculo->rh260_matricula, Tipo::S2306);
    $servidorAlteracao->setDataS2306(new DBDate($dataAlteracaoESocial));
    $servidorAlteracao->save();
  }
  return $alteraS2306;
}

  function alteraRhAdmissao($h25_sequencial, $numeroProcesso, $Anistia){
    $sSql = "UPDATE rhadmissaodado 
    SET h25_numeroprocesso = '$numeroProcesso', h25_leianistia = '$Anistia' 
    WHERE h25_sequencial = $h25_sequencial";
    db_query($sSql);
  }

    if (isset($incluir) || isset($alterar)) {
        $menorAprendiz = MenorAprendizRepository::findByMatricula($h07_regist);
        if (isset($rh312_modalidade) && !empty($rh312_modalidade)) {
            //inclui ou altera
            $repositorio = new MenorAprendizRepository();
            if (!$menorAprendiz) {
                $menorAprendiz = $repositorio::menorAprendiz(); 
            }
            $menorAprendiz->setModalidade($rh312_modalidade);
            $menorAprendiz->setCodigoInstituicao($codigoInstituicao);
            $menorAprendiz->setMatricula($h07_regist);
            $menorAprendiz->setCnpjDireta($rh312_cnpjqualificadora);
            $menorAprendiz->setCnpjIndireta($rh312_cnpjefetivada);
            $menorAprendiz->setCnpjPratica($rh312_pratica);
            $repositorio->save($menorAprendiz);
        } else {
            if (!empty($menorAprendiz)) {
                $repositorio = new MenorAprendizRepository();
                $repositorio->delete($menorAprendiz);
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
<script language="JavaScript" type="text/javascript" src="scripts/geradorrelatorios.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/libJsonJs.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="js_pesquisah07_cant(false); js_pesquisah07_fundam(false); js_pesquisah07_refe(false); js_pesquisah07_area(false);" >
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">

<!-- Código para extensão -->

  <!-- <?php if (db_getsession('DB_nome_modulo') != 'Pessoal'): ?>< -->

  <!-- Fim código para extensão -->

<!--   <tr>
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
 -->
  <!-- Código para extensão -->

  <!-- <?php endif; ?> -->

<!-- Fim código para extensão -->

</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
                <?php include modification("forms/db_frmadmissao.php");?>
    </center>
	</td>
  </tr>
</table>
<?php

/** Código para extensão */

// if (db_getsession('DB_nome_modulo') != 'Pessoal') {
//   db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
// }
/** Fim Código para extensão */

?>
</body>
</html>
<?php
if(isset($alterar) || isset($incluir)){
  if($cladmissao->erro_status=="0"){
    $cladmissao->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($cladmissao->erro_campo!=""){
      echo "<script> document.form1.".$cladmissao->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$cladmissao->erro_campo.".focus();</script>";
    }
  }else{
    $cladmissao->erro(true,false);
    echo "<script> location.href=\"?h07_regist={$h07_regist}&opcao=alterar\";";
    echo "parent.mo_camada('rhpesdoc');";
    echo " js_db_libera (); </script>";

  }
}
// if($db_opcao==22){
//   echo "<script>document.form1.pesquisar.click();</script>";
// }
?>
<script>
js_tabulacaoforms("form1","h07_tipadm",true,1,"h07_tipadm",true);
</script>
