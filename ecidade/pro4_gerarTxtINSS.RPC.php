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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_layouttxt.php"));
require_once(modification("dbforms/db_funcoes.php"));

use ECidade\Tributario\Projetos\Obras\Sisobras\RegistroAreaPrincipal;
use ECidade\Tributario\Projetos\Obras\Sisobras\RegistroAreaComplementar;
use ECidade\Tributario\Projetos\Obras\Sisobras\RegistroAlvara;
use ECidade\Tributario\Projetos\Obras\Sisobras\RegistroHabitese;
use ECidade\Tributario\Projetos\Obras\Sisobras\Webservice\Arquivo\RecepcaoLote;
use ECidade\Tributario\Projetos\Obras\Sisobras\Webservice\Arquivo\ConsultarDocumento;
use ECidade\Tributario\Projetos\Obras\Sisobras\Webservice\Manutencao;

$oJson                  = new services_json();
$oSistemaExterno        = new cl_db_sistemaexterno();
$oObras                 = new cl_obras();
$oObrasEnvio            = new cl_obrasenvio();
$oObrasEnvioReg         = new cl_obrasenvioreg();
$oObrasEnvioRegHab      = new cl_obrasenvioreghab();
$oRetorno               = new stdClass();
$oDbConfig              = new cl_db_config();
$oObrasEnvioRegAlvara   = new cl_obrasenvioregalvara();
$cl_obrasoutrosprop     = new cl_obrasoutrosprop();

$oParametros            = $oJson->decode(str_replace("\\","",$_POST["json"]));
$oRetorno->erro         = false;
$oRetorno->sMensagem    = '';

$aDadosRetorno          = array();

try {

  switch ($oParametros->sExecucao) {

    case "gerarTXT" :

      unset($_SESSION['aInconsistencia']);
      db_putsession("aInconsistencia", array());

      $iMes            = str_pad($oParametros->iMes, 2, "0", STR_PAD_LEFT);
   	  $iAno            = $oParametros->iAno;
      $lAviso          = $oParametros->lAviso;

      $hNomeArquivo    = date("His");
      $dNomeArquivo    = date("Ymd");

      $sSufixo = "{$iMes}-{$iAno}-{$dNomeArquivo}-{$hNomeArquivo}";

      $aErros          = array();

      $iInconsistencia = 0;
      $lIncluir        = false;
      $iUltimoDiaMes   = date("d", strtotime("{$iAno}-{$iMes}-".cal_days_in_month(CAL_GREGORIAN, $iMes,$iAno)));

      $aInconsistencia = array();

      // Busca dados da tabela parprojetos
      $anousu = db_getsession('DB_anousu');
      $sqlBuscaParProjetos = "SELECT * FROM parprojetos WHERE ob21_anousu = $anousu;";
      $resultBuscaParProjetos = db_query($sqlBuscaParProjetos);
      $dadosBuscaParProjetos = db_utils::fieldsMemory($resultBuscaParProjetos, 0);

      // Atribui local e senha do certificado A1 cadastrados nos parametros dos projetos.
      $localA1 = $dadosBuscaParProjetos->ob21_localcertificadoa1;
      $senhaA1 = $dadosBuscaParProjetos->ob21_senhacertificadoa1;

      if (empty($localA1)) {
      	throw new Exception('Certificado A1 não encontrado.');
      }

      /**
       * Query com os dados principais
       */
      $aDadosSisobra   = array();
      $sSqlSisobra     = $oObras->sql_queryDadosSisobraWebservice($iMes, $iAno);
      $rsSisobra       = $oObras->sql_record($sSqlSisobra);
      if($rsSisobra){
        $aDadosSisobra   = db_utils::getCollectionByRecord($rsSisobra, false, false, false);
      }

      // Verifica se existem obras
      $iTotalObras = count($aDadosSisobra);
      if ($iTotalObras == 0 ) {
      	throw new Exception(_M('tributario.projetos.pro4_gerarTxtINSS.sem_obras'));
      }

      $arrayRegistroAlvara = [];
      $arrayRegistroHabitese = [];
      // Percorre dados para gerar XML SISOBRA
      foreach ($aDadosSisobra as $key => $value) {
        /********** ATRIBUIÇÃO DE DADOS HABITE-SE E ALVARÁ PARA O XML **********/

        $anoMesHabitese = date("Ym", strtotime($value->datahabitese));
        $anoMesParametro = $iAno.'-'.$iMes.'-01';
        $anoMesParametro = date("Ym", strtotime($anoMesParametro));

        // Se registro atual possui codigo habitese e é do mesmo periodo do parametro, gera XML leiaute habitese
        if (!empty($value->codigohabitese) && $anoMesHabitese == $anoMesParametro) {
          // Consulta dado atual na receita
          $oConsultarDocumento = new ConsultarDocumento('habitese', $value->codigohabitese, $iAno);
          $oConsultarDocumentoXml = $oConsultarDocumento->gerar()->saveXML();
          $client = new Manutencao($oConsultarDocumentoXml, $oConsultarDocumento->getOperacao(), $localA1, $senhaA1);
          $client->processarRequisicao();
          $getAlvaraHabiteseExistente = $client->getAlvaraHabiteseExistente();
          if (!empty($getAlvaraHabiteseExistente)) {
            // ER047
            $aInconsistencia[] = array(
              'tipo'=>'ERRO',
              'registro'=>utf8_encode('Habite-se: '.$value->codigohabitese),
              'detalhe'=>utf8_encode('O habite-se já está cadastrado. O número do habite-se náo poderá repetir no período do ano para uma mesma prefeitura.'),
              'tipoErro'=>'ER047'
            );
            // Caso Habite-se seja total
            if (!$value->tipohabitese) {
              // ER048
              $aInconsistencia[] = array(
                'tipo'=>'ERRO',
                'registro'=>utf8_encode('Habitese: '.$value->codigohabitese)." Obra:".$value->codigoobra,
                'detalhe'=>utf8_encode('Já existe um habite-se total cadastrado para o alvará '.$value->alvaraobra.'.'),
                'tipoErro'=>'ER048'
              );
            }
          } else {
            // $oConsultarDocumento = new ConsultarDocumento('alvara', $value->alvaraobra, $iAno);
            // $oConsultarDocumentoXml = $oConsultarDocumento->gerar()->saveXML();
            // $client = new Manutencao($oConsultarDocumentoXml, $oConsultarDocumento->getOperacao(), $localA1, $senhaA1);
            // $client->processarRequisicao();
            // $getAlvaraHabiteseExistente = $client->getAlvaraHabiteseExistente();
            // // Caso Alvará vinculado ao Habite-se atual não exista na receita, atribui no XML para envio
            // if (empty($getAlvaraHabiteseExistente)) {
            //   $alvaraInexistente = $value->alvaraobra;
            // }

            // Verifica se alvará atual já foi enviado
            // $sqlGetAlvaraInexistente = "SELECT * FROM obrasenvioregalvara WHERE ob31_codalvara = $value->alvaraobra";
            // $resultSqlGetAlvaraInexistente = db_query($sqlGetAlvaraInexistente);
            // if (pg_numrows($resultSqlGetAlvaraInexistente) == 0) {
            //   $alvaraInexistente = $value->alvaraobra;
            // }
          }

          // Inicia a busca de dados para montar XML do Habite-se
          $oRegistroHabitese = new RegistroHabitese();
          $resultObrashabite = db_query("select * from obrashabite where ob09_codhab = ".$value->codigohabitese);
          $dadosObrasHabite = db_utils::fieldsMemory($resultObrashabite, 0);

          $rsQtdTotalUnidadesBloco = db_query("select ob07_unidades from obrasender where ob07_codobra = ".$value->codigoobra);
          $qtdTotalUnidadesBloco = db_utils::fieldsMemory($rsQtdTotalUnidadesBloco, 0);

          $resultObrasConstrAreaComplementar = db_query(
            "select * from obrasconstrareacomplementar where ob27_construcao = ".$dadosObrasHabite->ob09_codconstr
          );
          $dadosObrasConstrAreaComplementar = db_utils::fieldsMemory($resultObrasConstrAreaComplementar, 0);

          $resultCaracterCategoriaAreaComplementar = db_query(
            "select j31_descr from caracter where j31_codigo = ".$dadosObrasConstrAreaComplementar->ob27_tipolancamento
          );
          $dadosCaracterCategoriaAreaComplementar = db_utils::fieldsMemory($resultCaracterCategoriaAreaComplementar, 0);

          $resultCaracterDestinacaoAreaComplementar = db_query(
            "select j31_descr from caracter where j31_codigo = ".$dadosObrasConstrAreaComplementar->ob27_ocupacao
          );
          $dadosCaracterDestinacaoAreaComplementar = db_utils::fieldsMemory($resultCaracterDestinacaoAreaComplementar, 0);

          // Atribui dados para as devidas tags
            // Formata valor do atributo Id conforme manual do sisobra
          if (strlen($value->codigohabitese) < 7) {
            $idHabitese = str_pad($value->codigohabitese, 7, 0, STR_PAD_RIGHT);
          } else if (strlen($value->codigohabitese) > 7) {
            $idHabitese = substr($value->codigohabitese, 0, 7);
          }
          $idHabitese = "id".$idHabitese;
          $oRegistroHabitese->setId($idHabitese);

          $oRegistroHabitese->setNumeroHabitese($value->codigohabitese);
          $oRegistroHabitese->setDataHabitese($value->datahabitese);
          $oRegistroHabitese->setDataFinalObra($value->datafimobra);

          if ($value->tipohabitese == "P") {
            $oRegistroHabitese->setTipoHabitese('parcial');
          } else {
            $oRegistroHabitese->setTipoHabitese('total');
          }

          $oRegistroHabitese->setObservacao($value->obshabite);

          $unidadeMedida = "M2";
          $oRegistroHabitese->setUnidadeMedida($unidadeMedida);
          if ($unidadeMedida != "M2") {
            // Só atribui se a unidade de medida for diferente de m2
            $oRegistroHabitese->setValorUnidadeMedida('');
          }

          $oRegistroHabitese->setNumeroAlvara($value->alvaraobra);
          $oRegistroHabitese->setDataAlvara($value->dataalvara);

          $oRegistroAreaPrincipal = new RegistroAreaPrincipal();

          if ($value->categoria == "NOVA") {
            $oRegistroAreaPrincipal->setCategoria("obra_nova");
          } else if ($value->categoria == "AMPLIAÇÃO") {
            $oRegistroAreaPrincipal->setCategoria("acrescimo");
          } else if ($value->categoria == "REGULARIZAÇÃO") {
            $oRegistroAreaPrincipal->setCategoria("obra_nova");
          } else {
            $oRegistroAreaPrincipal->setCategoria(strtolower($value->categoria));
          }

          $oRegistroAreaPrincipal->setDestinacao(strtolower($value->destinacao));
          $oRegistroAreaPrincipal->setTipoObra(strtolower($value->tipoobra));
          if ($value->destinacao == 'CONJUNTO_HABITACIONAL_POPULAR') {
            $oRegistroAreaPrincipal->setQtdTotalUnidadesBloco($qtdTotalUnidadesBloco->ob07_unidades);
          }

          if ($value->tipohabitese == "P") {
            $oRegistroAreaPrincipal->setArea($value->areaobrahabite);
          } else {
            $oRegistroAreaPrincipal->setArea($value->areaobra);
          }

          $oRegistroAreaComplementar = new RegistroAreaComplementar();

          if ($dadosCaracterCategoriaAreaComplementar->j31_descr == "NOVA") {
            $oRegistroAreaComplementar->setCategoria("obra_nova");
          } else if ($dadosCaracterCategoriaAreaComplementar->j31_descr == "AMPLIAÇÃO") {
            $oRegistroAreaComplementar->setCategoria("acrescimo");
          } else if ($dadosCaracterCategoriaAreaComplementar->j31_descr == "REGULARIZAÇÃO") {
            $oRegistroAreaComplementar->setCategoria("existente");
          } else {
            $oRegistroAreaComplementar->setCategoria(strtolower($dadosCaracterCategoriaAreaComplementar->j31_descr));
          }

          $oRegistroAreaComplementar->setDestinacao(strtolower($dadosCaracterDestinacaoAreaComplementar->j31_descr));
          $oRegistroAreaComplementar->setTipoObra(strtolower($dadosObrasConstrAreaComplementar->ob27_tipo));

          if($dadosObrasConstrAreaComplementar->ob27_tipo == 1) {
            $oRegistroAreaComplementar->setTipoAreaComplementar("quadra");
          } else if ($dadosObrasConstrAreaComplementar->ob27_tipo == 2) {
            $oRegistroAreaComplementar->setTipoAreaComplementar("estacionamento_terreo");
          } else if ($dadosObrasConstrAreaComplementar->ob27_tipo == 3) {
            $oRegistroAreaComplementar->setTipoAreaComplementar("piscina");
          } else if ($dadosObrasConstrAreaComplementar->ob27_tipo == 4) {
            $oRegistroAreaComplementar->setTipoAreaComplementar("area_posto_gasolina");
          }

          // $oRegistroAreaComplementar->setQtdTotalUnidadesBloco();

          $oRegistroAreaComplementar->setAreaCoberta($dadosObrasConstrAreaComplementar->ob27_medidaareacoberta);
          $oRegistroAreaComplementar->setAreaDescoberta($dadosObrasConstrAreaComplementar->ob27_medidaareadescoberta);

          $arrayRegistroHabitese[] = (object) [
            'oRegistroHabitese' => $oRegistroHabitese,
            'oRegistroAreaPrincipal' => $oRegistroAreaPrincipal,
            'oRegistroAreaComplementar' => $oRegistroAreaComplementar
          ];

          /********** Atribui erros de Habitese ao array de inconsistencias **********/
          // ER005
          if ($value->datafimobra > $value->datahabitese) {
            $aInconsistencia[] = array(
              'tipo'=>'ERRO',
              'registro'=>'Habite-se: '.$value->codigohabitese." Obra:".$value->codigoobra,
              'detalhe'=>'A Data do Final da Obra deve ser menor ou igual a Data do Habite-se.',
              'tipoErro'=>'ER005'
            );
          }
          // ER029
          if ($value->destinacao == 'CASA_POPULAR' ||
            $dadosCaracterDestinacaoAreaComplementar->j31_descr == 'CASA_POPULAR'
          ) {
            $totalArea = $value->areaobra +
              $dadosObrasConstrAreaComplementar->ob27_medidaareacoberta +
              $dadosObrasConstrAreaComplementar->ob27_medidaareadescoberta
            ;
            if ($totalArea > 70) {
              $aInconsistencia[] = array(
                'tipo'=>'ERRO',
                'registro'=>'Habite-se: '.$value->codigohabitese." Obra:".$value->codigoobra,
                'detalhe'=>utf8_encode('Para destinação "Casa Popular" a soma das áreas não pode ser maior que 70m².'),
                'tipoErro'=>'ER029'
              );
            }
          }
          // ER039
          if ($value->destinacao == 'CONJUNTO_HABITACIONAL_POPULAR') {
            $totalArea = $value->areaobra +
              $dadosObrasConstrAreaComplementar->ob27_medidaareacoberta +
              $dadosObrasConstrAreaComplementar->ob27_medidaareadescoberta
            ;
            if (($totalArea / $qtdTotalUnidadesBloco->ob07_unidades) > 70) {
              $aInconsistencia[] = array(
                'tipo'=>'ERRO',
                'registro'=>'Habite-se: '.$value->codigohabitese." Obra:".$value->codigoobra,
                  'detalhe'=>utf8_encode('Rejeição do documento Para destinação "Conjunto Habitacional Popular" a soma da área principal e complementar dividida pela quantidade total de unidades não pode ser maior que 70m². A destinação deve ser "Residencial Multifamiliar".'),
                'tipoErro'=>'ER039'
              );
            }
          }
          // ER066
          if ($value->datahabitese <= $value->dataalvara) {
            $aInconsistencia[] = array(
              'tipo'=>'ERRO',
              'registro'=>'Habite-se: '.$value->codigohabitese." Obra:".$value->codigoobra,
              'detalhe'=>utf8_encode('A Data do Habite-se deve ser posterior a Data do Alvará vinculado.'),
              'tipoErro'=>'ER066'
            );
          }

        }

        // Verifica se habitese atual não é do mesmo periodo do parametro, porem o alvará
        $anoMesAlvara = date("Ym", strtotime($value->dataalvara));

        if ($anoMesAlvara == $anoMesParametro) {
          $sqlGetAlvaraInexistente = "SELECT * FROM obrasenvioregalvara WHERE ob31_codalvara = $value->alvaraobra";
          $resultSqlGetAlvaraInexistente = db_query($sqlGetAlvaraInexistente);
          if (pg_numrows($resultSqlGetAlvaraInexistente) == 0) {
            $alvaraInexistente = $value->alvaraobra;
          }
        }

        // Se registro atual não tiver codigo habitese e tiver codigo alvara
        if (!empty($alvaraInexistente)) {
          // Consulta dado atual na receita
          $oConsultarDocumento = new ConsultarDocumento('alvara', $value->alvaraobra, $iAno);
          $oConsultarDocumentoXml = $oConsultarDocumento->gerar()->saveXML();
          $client = new Manutencao($oConsultarDocumentoXml, $oConsultarDocumento->getOperacao(), $localA1, $senhaA1);
          $client->processarRequisicao();
          $getAlvaraHabiteseExistente = $client->getAlvaraHabiteseExistente();
          if (!empty($getAlvaraHabiteseExistente)) {
            // ER042
            $aInconsistencia[] = array(
              'tipo'=>'ERRO',
              'registro'=>utf8_encode('Alvará: '.$value->alvaraobra),
                'detalhe'=>utf8_encode('O alvará já está cadastrado. O número do alvará não poderá repetir no período do ano para uma mesma prefeitura.'),
              'tipoErro'=>'ER042'
            );
          }

          // Inicia a busca de dados para montar XML do Alvará
          $oRegistroAlvara = new RegistroAlvara();

          $resultCgcCpf = db_query("select z01_cgccpf from cgm inner join obrasresp on ob10_numcgm = z01_numcgm where ob10_codobra = ".$value->codigoobra);
          $dadosCgcCpf = db_utils::fieldsMemory($resultCgcCpf, 0);

          // Busca dados Area Complementar
          $resultObrasConstrAreaComplementar = db_query(
            "select * from obrasconstrareacomplementar where ob27_construcao = ".$dadosObrasHabite->ob09_codconstr
          );
          $dadosObrasConstrAreaComplementar = db_utils::fieldsMemory($resultObrasConstrAreaComplementar, 0);

          $resultCaracterCategoriaAreaComplementar = db_query(
            "select j31_descr from caracter where j31_codigo = ".$dadosObrasConstrAreaComplementar->ob27_tipolancamento
          );
          $dadosCaracterCategoriaAreaComplementar = db_utils::fieldsMemory($resultCaracterCategoriaAreaComplementar, 0);

          $resultCaracterDestinacaoAreaComplementar = db_query(
            "select j31_descr from caracter where j31_codigo = ".$dadosObrasConstrAreaComplementar->ob27_ocupacao
          );
          $dadosCaracterDestinacaoAreaComplementar = db_utils::fieldsMemory($resultCaracterDestinacaoAreaComplementar, 0);

          // Busca dados infoAdicionais
          $resultNumeroProcesso = db_query("select * from obrasalvaraprotprocesso where ob26_obrasalvara = ".$value->codigoobra);
          $dadosNumeroProcesso = db_utils::fieldsMemory($resultNumeroProcesso, 0);

          $resultSituacao = db_query("select * from obrassituacao inner join obrassituacaolog on ob29_obrassituacao = ob28_sequencial where ob29_obras = ".$value->codigoobra);
          $dadosSituacao = db_utils::fieldsMemory($resultSituacao, 0);

          // Busca dados Responsável Técnico
          $resultResponsavelTecnico = db_query("select ob01_arquitetoobra, ob01_numeroarttecnico, ob01_numerorrttecnico, ob15_crea, z01_nome, ob15_profissao from obras inner join obrastec on ob01_arquitetoobra = ob15_sequencial inner join cgm on z01_numcgm = ob15_numcgm where ob01_codobra = ".$value->codigoobra);
          $dadosResponsavelTecnico = db_utils::fieldsMemory($resultResponsavelTecnico, 0);

          // Busca dados Responsável Projeto
          $resultResponsavelProjeto = db_query("select ob01_responsavelprojeto, ob01_numeroartprojeto, ob01_numerorrtprojeto, ob15_crea, z01_nome, ob15_profissao from obras inner join obrastec on ob01_responsavelprojeto = ob15_sequencial inner join cgm on z01_numcgm = ob15_numcgm where ob01_codobra = ".$value->codigoobra);
          $dadosResponsavelProjeto = db_utils::fieldsMemory($resultResponsavelProjeto, 0);

          $resultCgcCpf = db_query("select z01_cgccpf from cgm inner join obrasresp on ob10_numcgm = z01_numcgm where ob10_codobra = ".$value->codigoobra);
          $dadosCgcCpf = db_utils::fieldsMemory($resultCgcCpf, 0);

          // Atribui dados para as devidas tags
          // Formata valor do atributo Id conforme manual do sisobra

          if($value->idalvara == null){
            if (strlen($value->codigoobra) < 7) {
              $idAlvara = str_pad($value->codigoobra, 6, 0, STR_PAD_RIGHT);
            } else if (strlen($value->codigoobra) > 7) {
              $idAlvara = substr($value->codigoobra, 0, 6);
            }
            $idAlvara = "id"."9".$idAlvara;
            $oRegistroAlvara->setId($idAlvara);
          } else {
            $idAlvara = str_pad($value->idalvara, 7, 0, STR_PAD_LEFT);
            $idAlvara = "id".$idAlvara;
            $oRegistroAlvara->setId($idAlvara);
          }
          
          $oRegistroAlvara->setNumeroAlvara($value->alvaraobra);
          // $oRegistroAlvara->setNumeroProtocoloAnterior();
          $oRegistroAlvara->setNomeObra($value->nomeobra);
          $oRegistroAlvara->setDataAlvara($value->dataalvara);

          if (empty($value->datainicioobra)) {
            $aInconsistencia[] = array(
              'tipo'=>'ERRO',
              'registro'=>utf8_encode('Alvará: '.$value->alvaraobra)." Obra:".$value->codigoobra,
              'detalhe'=>utf8_encode('A Data do Início da Obra deve ser informada.'),
              'tipoErro'=>'ER089'
            );
          } else {
            $oRegistroAlvara->setDataInicioObra($value->datainicioobra);
          }
          $oRegistroAlvara->setDataFinalObra($value->datafimobra);
          $oRegistroAlvara->setTipoAlvara('inicial');

          // Filtra Responsável Execução Obra
          if ($value->respexecobra == 51) {
            $oRegistroAlvara->setProprietarioDoImovel(true);
          } else if ($value->respexecobra == 52) {
            if (strlen($dadosCgcCpf->z01_cgccpf) == 11) {
              $oRegistroAlvara->setDonoDaObraCpf($dadosCgcCpf->z01_cgccpf);
            } else if (strlen($dadosCgcCpf->z01_cgccpf)>11) {
              $oRegistroAlvara->setDonoDaObraCnpj($dadosCgcCpf->z01_cgccpf);
            }
          } else if ($value->respexecobra == 55) {
            if (strlen($dadosCgcCpf->z01_cgccpf) == 11) {
              $oRegistroAlvara->setIncorporadorConstrucaoCivilCpf($dadosCgcCpf->z01_cgccpf);
            } else if (strlen($dadosCgcCpf->z01_cgccpf)>11) {
              $oRegistroAlvara->setIncorporadorConstrucaoCivilCnpj($dadosCgcCpf->z01_cgccpf);
          }
          } else if ($value->respexecobra == 53) {
            $oRegistroAlvara->setEmpresaConstrutoraCnpj($dadosCgcCpf->z01_cgccpf);
          } else if ($value->respexecobra == 56) {

            $outrosPropri = $cl_obrasoutrosprop->sql_query_outrosprop(null, "z01_cgccpf", null, "ob32_codobra = {$value->codigoobra}");
            $rsOutrosPropri = $cl_obrasoutrosprop->sql_record($outrosPropri);
            
            $numeroOutrosProp = pg_num_rows($rsOutrosPropri);
            
            if (strlen($dadosCgcCpf->z01_cgccpf) == 11) {
              $oRegistroAlvara->setCpfResponsavelPrincipal($dadosCgcCpf->z01_cgccpf);

              if ($numeroOutrosProp > 0) {             
                for ($iNumero = 0; $iNumero < $numeroOutrosProp; $iNumero++) {
                  $oOutrosProprietarios = db_utils::fieldsMemory($rsOutrosPropri, $iNumero);

                  if (strlen($oOutrosProprietarios->z01_cgccpf) == 11) {
                    $oRegistroAlvara->addConstrucaoNomeColetivoCpf($oOutrosProprietarios->z01_cgccpf);
                  } else if (strlen($oOutrosProprietarios->z01_cgccpf)>11) {
                    $oRegistroAlvara->addConstrucaoNomeColetivoCnpj($oOutrosProprietarios->z01_cgccpf);
                  }
              }
                
              } else if (strlen($dadosCgcCpf->z01_cgccpf)>11) {
                $oRegistroAlvara->setCnpjResponsavelPrincipal($dadosCgcCpf->z01_cgccpf);
                
                if ($numeroOutrosProp > 0) {             
                  for ($iNumero = 0; $iNumero < $numeroOutrosProp; $iNumero++) {
                    $oOutrosProprietarios = db_utils::fieldsMemory($rsOutrosPropri, $iNumero);
  
                    if (strlen($oOutrosProprietarios->z01_cgccpf) == 11) {
                      $oRegistroAlvara->addConstrucaoNomeColetivoCpf($oOutrosProprietarios->z01_cgccpf);
                    } else if (strlen($oOutrosProprietarios->z01_cgccpf)>11) {
                      $oRegistroAlvara->addConstrucaoNomeColetivoCnpj($oOutrosProprietarios->z01_cgccpf);
                    }
                  }
                }
              }  
            }
          }

          /************************** FUTURA IMPLEMENTAÇÃO **************************/
          // $oRegistroAlvara->setCnpjConsorcio();
          // $oRegistroAlvara->setCnpjEmpresaLiderConsorcio();
          // $oRegistroAlvara->setCpfResponsavelPrincipal();
          // $oRegistroAlvara->setCnpjResponsavelPrincipal();

          if (empty($value->cepobra)) {
            $aInconsistencia[] = array(
              'tipo'=>'ERRO',
              'registro'=>utf8_encode('Alvará: '.$value->alvaraobra)." Obra:".$value->codigoobra,
              'detalhe'=>utf8_encode('CEP deve ser informado.'),
              'tipoErro'=>'ERRO'
            );
          } else if ((strlen($value->cepobra) < 8) || (strlen($value->cepobra) > 8)) {
            $aInconsistencia[] = array(
              'tipo'=>'ERRO',
              'registro'=>utf8_encode('Alvará: '.$value->alvaraobra)." Obra:".$value->codigoobra,
              'detalhe'=>utf8_encode('CEP está no formato inválido.'),
              'tipoErro'=>'ERRO'
            );
          } else {
            $oRegistroAlvara->setCep($value->cepobra);
          }
          $oRegistroAlvara->setTipoLogradouro($value->tipologradouro);
          $oRegistroAlvara->setLogradouro($value->logradouro);
          $oRegistroAlvara->setNumero($value->numlogradouro);
          $oRegistroAlvara->setComplemento($value->complogradouro);
          $oRegistroAlvara->setBairro($value->bairroobra);

          $unidadeMedida = "M2";
          $oRegistroAlvara->setUnidadeMedida($unidadeMedida);
          if ($unidadeMedida != "M2") {
            // Só atribui se a unidade de medida for diferente de m2
            $oRegistroAlvara->setValorUnidadeMedida('');
          }

          if (strlen($value->propriobracgccpf) == 11) {
            $oRegistroAlvara->setProprietarioObraCpf($value->propriobracgccpf);
          } else if (strlen($value->propriobracgccpf) > 11) {
            $oRegistroAlvara->setProprietarioObraCnpj($value->propriobracgccpf);
          }

          $oRegistroAlvara->setSituacao($dadosSituacao->ob28_descricao);
          // $oRegistroAlvara->setClasse();
          $oRegistroAlvara->setNumeroProcesso($dadosNumeroProcesso->ob26_protprocesso);

          // Dados Responsável Técnico
          if (pg_num_rows($resultResponsavelTecnico) > 0) {
            if (empty($dadosResponsavelTecnico->ob01_numeroarttecnico) && empty($dadosResponsavelTecnico->ob01_numerorrttecnico)) {
              $aInconsistencia[] = array(
                'tipo'=>'ERRO',
                'registro'=>utf8_encode('Alvará: '.$value->alvaraobra)." Obra:".$value->codigoobra,
                'detalhe'=>utf8_encode('ART ou RRT do Responsável Técnico deve ser informado.'),
                'tipoErro'=>'ERRO'
              );
            }
            if (empty($dadosResponsavelTecnico->ob15_profissao)) {
              $aInconsistencia[] = array(
                'tipo'=>'ERRO',
                'registro'=>utf8_encode('Alvará: '.$value->alvaraobra)." Obra:".$value->codigoobra,
                'detalhe'=>utf8_encode('Campo Profissão deve ser atribuído ao Responsável Técnico.'),
                'tipoErro'=>'ERRO'
              );
            }
            else {
              // Caso responsável técnico seja engenheiro
              if ($dadosResponsavelTecnico->ob15_profissao == 2) {
                $oRegistroAlvara->setEngenheiroNomeTecnico($dadosResponsavelTecnico->z01_nome);
                $oRegistroAlvara->setEngenheiroCreaTecnico($dadosResponsavelTecnico->ob15_crea);
                $oRegistroAlvara->setEngenheiroArtTecnico($dadosResponsavelTecnico->ob01_numeroarttecnico);
              // Caso responsável técnico seja arquiteto
              } else if ($dadosResponsavelTecnico->ob15_profissao == 1) {
                $oRegistroAlvara->setArquitetoNomeTecnico($dadosResponsavelTecnico->z01_nome);
                $oRegistroAlvara->setArquitetoCauTecnico($dadosResponsavelTecnico->ob15_crea);
                $oRegistroAlvara->setArquitetoRrtTecnico($dadosResponsavelTecnico->ob01_numerorrttecnico);
              }
            }
          }

          // Dados Responsável Projeto
          if (pg_num_rows($resultResponsavelProjeto) > 0) {
            if (empty($dadosResponsavelProjeto->ob01_numeroartprojeto) && empty($dadosResponsavelProjeto->ob01_numerorrtprojeto)) {
              $aInconsistencia[] = array(
                'tipo'=>'ERRO',
                'registro'=>utf8_encode('Alvará: '.$value->alvaraobra)." Obra:".$value->codigoobra,
                'detalhe'=>utf8_encode('ART ou RRT do Responsável Projeto deve ser informado.'),
                'tipoErro'=>'ERRO'
              );
            } if (empty($dadosResponsavelProjeto->ob15_profissao)) {
              $aInconsistencia[] = array(
                'tipo'=>'ERRO',
                'registro'=>utf8_encode('Alvará: '.$value->alvaraobra)." Obra:".$value->codigoobra,
                'detalhe'=>utf8_encode('Campo Profissão deve ser atribuído ao Responsável Projeto.'),
                'tipoErro'=>'ERRO'
              );
            }
            else {
              // Caso responsável projeto seja engenheiro
              if ($dadosResponsavelProjeto->ob15_profissao == 2) {
                $oRegistroAlvara->setEngenheiroNomeProjeto($dadosResponsavelProjeto->z01_nome);
                $oRegistroAlvara->setEngenheiroCreaProjeto($dadosResponsavelProjeto->ob15_crea);
                $oRegistroAlvara->setEngenheiroArtProjeto($dadosResponsavelProjeto->ob01_numeroartprojeto);
              // Caso responsável projeto seja arquiteto
              } else if ($dadosResponsavelProjeto->ob15_profissao == 1) {
                $oRegistroAlvara->setArquitetoNomeProjeto($dadosResponsavelProjeto->z01_nome);
                $oRegistroAlvara->setArquitetoCauProjeto($dadosResponsavelProjeto->ob15_crea);
                $oRegistroAlvara->setArquitetoRrtProjeto($dadosResponsavelProjeto->ob01_numerorrtprojeto);
              }
            }
          }

          // $oRegistroAlvara->setEspecificacao();
          $oRegistroAlvara->setObservacao($value->obsalvara);

          $oRegistroAreaPrincipal = new RegistroAreaPrincipal();

          if ($value->categoria == "NOVA") {
            $oRegistroAreaPrincipal->setCategoria("obra_nova");
          } else if ($value->categoria == "AMPLIAÇÃO") {
            $oRegistroAreaPrincipal->setCategoria("acrescimo");
          } else if ($value->categoria == "REGULARIZAÇÃO") {
            $oRegistroAreaPrincipal->setCategoria("existente");
          } else {
            $oRegistroAreaPrincipal->setCategoria(strtolower($value->categoria));
          }

          $oRegistroAreaPrincipal->setDestinacao($value->destinacao);
          $oRegistroAreaPrincipal->setTipoObra(strtolower($value->tipoobra));
          if ($value->destinacao == 'CONJUNTO_HABITACIONAL_POPULAR') {
            $oRegistroAreaPrincipal->setQtdTotalUnidadesBloco($qtdTotalUnidadesBloco->ob07_unidades);
          }
          $oRegistroAreaPrincipal->setArea($value->areaobra);

          $oRegistroAreaComplementar = new RegistroAreaComplementar();
          if ($dadosCaracterCategoriaAreaComplementar->j31_descr == "NOVA") {
            $oRegistroAreaComplementar->setCategoria("obra_nova");
          } else if ($dadosCaracterCategoriaAreaComplementar->j31_descr == "AMPLIAÇÃO") {
            $oRegistroAreaComplementar->setCategoria("acrescimo");
          } else if ($dadosCaracterCategoriaAreaComplementar->j31_descr == "REGULARIZAÇÃO") {
            $oRegistroAreaComplementar->setCategoria("existente");
          } else {
            $oRegistroAreaComplementar->setCategoria(strtolower($dadosCaracterCategoriaAreaComplementar->j31_descr));
          }

          $oRegistroAreaComplementar->setDestinacao(strtolower($dadosCaracterDestinacaoAreaComplementar->j31_descr));
          $oRegistroAreaComplementar->setTipoObra(strtolower($dadosObrasConstrAreaComplementar->ob27_tipo));

          if($dadosObrasConstrAreaComplementar->ob27_tipo == 1) {
            $oRegistroAreaComplementar->setTipoAreaComplementar("quadra");
          } else if ($dadosObrasConstrAreaComplementar->ob27_tipo == 2) {
            $oRegistroAreaComplementar->setTipoAreaComplementar("estacionamento_terreo");
          } else if ($dadosObrasConstrAreaComplementar->ob27_tipo == 3) {
            $oRegistroAreaComplementar->setTipoAreaComplementar("piscina");
          } else if ($dadosObrasConstrAreaComplementar->ob27_tipo == 4) {
            $oRegistroAreaComplementar->setTipoAreaComplementar("area_posto_gasolina");
          }

          // $oRegistroAreaComplementar->setQtdTotalUnidadesBloco();

          $oRegistroAreaComplementar->setAreaCoberta($dadosObrasConstrAreaComplementar->ob27_medidaareacoberta);
          $oRegistroAreaComplementar->setAreaDescoberta($dadosObrasConstrAreaComplementar->ob27_medidaareadescoberta);

          $arrayRegistroAlvara[] = (object) [
            'oRegistroAlvara' => $oRegistroAlvara,
            'oRegistroAreaPrincipal' => $oRegistroAreaPrincipal,
            'oRegistroAreaComplementar' => $oRegistroAreaComplementar
          ];

          /********** Atribui erros de Habitese ao array de inconsistencias **********/
          // ER008
          if (!empty($value->datafimobra) && $value->datafimobra <= $value->datainicioobra) {
            $aInconsistencia[] = array(
              'tipo'=>'ERRO',
              'registro'=>utf8_encode('Alvará: '.$value->alvaraobra)." Obra:".$value->codigoobra,
              'detalhe'=>utf8_encode('A Data do Final de Obra deve ser posterior á Data de Início da Obra.'),
              'tipoErro'=>'ER008'
            );
          }
          // ER029
          if ($value->destinacao == 'CASA_POPULAR' ||
            $dadosCaracterDestinacaoAreaComplementar->j31_descr == 'CASA_POPULAR'
          ) {
            $totalArea = $value->areaobra +
              $dadosObrasConstrAreaComplementar->ob27_medidaareacoberta +
              $dadosObrasConstrAreaComplementar->ob27_medidaareadescoberta
            ;
            if ($totalArea > 70) {
              $aInconsistencia[] = array(
                'tipo'=>'ERRO',
                'registro'=>utf8_encode('Alvará: '.$value->alvaraobra)." Obra:".$value->codigoobra,
                  'detalhe'=>utf8_encode('Para destinação "Casa Popular" a soma das áreas não pode ser maior que 70m².'),
                'tipoErro'=>'ER029'
              );
            }
          }
          // ER039
          if ($value->destinacao == 'CONJUNTO_HABITACIONAL_POPULAR') {
            $totalArea = $value->areaobra +
              $dadosObrasConstrAreaComplementar->ob27_medidaareacoberta +
              $dadosObrasConstrAreaComplementar->ob27_medidaareadescoberta
            ;
            if (($totalArea / $qtdTotalUnidadesBloco->ob07_unidades) > 70) {
              $aInconsistencia[] = array(
                'tipo'=>'ERRO',
                'registro'=>utf8_encode('Alvará: '.$value->alvaraobra)." Obra:".$value->codigoobra,
                  'detalhe'=>utf8_encode('Rejeição do documento Para destinação "Conjunto Habitacional Popular" a soma da área principal e complementar dividida pela quantidade total de unidades não pode ser maior que 70m². A destinação deve ser "Residencial Multifamiliar".'),
                'tipoErro'=>'ER039'
              );
            }
          }
          $alvaraInexistente = "";
        }
      }

      // Verifica se existe algum alvara ou habitese não enviados para receita
      $iTotalAlvara = count($arrayRegistroAlvara);
      $iTotalHabitese = count($arrayRegistroHabitese);
      if ($iTotalAlvara == 0 && $iTotalHabitese == 0) {
      	throw new Exception(_M('tributario.projetos.pro4_gerarTxtINSS.sem_obras'));
      }

      // Gera XML Alvara e Habitese
      $oRecepcaoLote = new RecepcaoLote($arrayRegistroAlvara, $arrayRegistroHabitese, $localA1, $senhaA1);

      $oRecepcaoLoteXml = $oRecepcaoLote->gerar()->saveXML();

      $oRecepcaoLoteXml = utf8_decode($oRecepcaoLoteXml);
      $oRecepcaoLoteXml = str_replace('&lt;', '<', $oRecepcaoLoteXml);
      $oRecepcaoLoteXml = str_replace('&gt;', '>', $oRecepcaoLoteXml);

      $filenameXml = loggerSis("loteobras-{$sSufixo}.xml", utf8_encode($oRecepcaoLoteXml));

      // Verifica se array de inconsistencias é vazio para retornar erros na tela
      if (!empty($aInconsistencia)) {
        $oRetorno->sArquivo         = $filenameXml;
        $oRetorno->aErros           = $aInconsistencia;
        $oRetorno->iInconsistencia  = 1;
        break;
      }
      // Conexão SOAP
      try {
        $aAlvarasValidos = [];
        $aHabitesesValidos = [];
        $client = new Manutencao($filenameXml, $oRecepcaoLote->getOperacao(), $localA1, $senhaA1);
        $client->processarRequisicao();
        $aRetornoEnvio = $client->getRespostaRecepcaoLote();

        $arrayRetornoRecepaoLote = $aRetornoEnvio["arrayRetornoRecepaoLote"];
        $retorno = $aRetornoEnvio["retorno"];

        loggerSis("retorno-recepcao-lote-{$sSufixo}.txt", $retorno.PHP_EOL.json_encode($arrayRetornoRecepaoLote));

        // Caso tenha retornado array de alvarás
        if (!empty($arrayRetornoRecepaoLote['Alvara'])) {
          // Caso seja registro único
          if (!is_array($arrayRetornoRecepaoLote['Alvara'][0])) {
            foreach ($arrayRetornoRecepaoLote['Alvara'] as $key => $value) {
              if ($key == 'codRetorno') {
                $codRetorno = $value;
              } if ($key == 'descricao') {
                $descricao = $value;
              } if ($key == 'numeroAlvara') {
                $numeroAlvara = $value;
              } if ($key == 'protocolo') {
                $protocolo = $value;
              }
            }
            if (($codRetorno == 'IN001' ||
                $codRetorno == 'IN003' ||
                $codRetorno == 'IN004' ||
                $codRetorno == 'IN006'
            ) && !empty($protocolo)) {
              $aAlvarasValidos[] = array(
                'numeroAlvara'=>$numeroAlvara,
                'protocolo'=>$protocolo
              );
              $aInconsistencia[] = array(
                'tipo'=>'SUCESSO',
                'registro'=>utf8_encode('Alvará: ').$numeroAlvara." Obra:".$value->codigoobra,
                'detalhe'=>$descricao,
                'tipoErro'=>$codRetorno
              );
            // Caso tenha retorno com erro
            } else {
              $aInconsistencia[] = array(
                'tipo'=>'ERRO',
                'registro'=>utf8_encode('Alvará: ').$numeroAlvara." Obra:".$value->codigoobra,
                'detalhe'=>$descricao,
                'tipoErro'=>$codRetorno
              );
            }
          }
          // Caso tenham múltiplos registros
          else {
            foreach ($arrayRetornoRecepaoLote['Alvara'] as $key => $value) {
              // Caso tenha retorno com sucesso
              if (($value['codRetorno'] == 'IN001' ||
                  $value['codRetorno'] == 'IN003' ||
                  $value['codRetorno'] == 'IN004' ||
                  $value['codRetorno'] == 'IN006'
              ) && !empty($value['protocolo'])) {
                  $aAlvarasValidos[] = array(
                    'numeroAlvara'=>$value['numeroAlvara'],
                    'protocolo'=>$value['protocolo']
                  );
                  $aInconsistencia[] = array(
                    'tipo'=>'SUCESSO',
                    'registro'=>utf8_encode('Alvará: ').$value['numeroAlvara']." Obra:".$value->codigoobra,
                    'detalhe'=>$value['descricao'],
                    'tipoErro'=>$value['codRetorno']
                  );
              } else {
                $aInconsistencia[] = array(
                  'tipo'=>'ERRO',
                  'registro'=>utf8_encode('Alvará: ').$value['numeroAlvara']." Obra:".$value->codigoobra,
                  'detalhe'=>$value['descricao'],
                  'tipoErro'=>$value['codRetorno']
                );
              }
            }
          }
        }

        // Caso tenha retornado array de habiteses
        if (!empty($arrayRetornoRecepaoLote['Habitese'])) {
          // Caso seja registro único
          if (!is_array($arrayRetornoRecepaoLote['Habitese'][0])) {
            foreach ($arrayRetornoRecepaoLote['Habitese'] as $key => $value) {
              if ($key == 'codRetorno') {
                $codRetorno = $value;
              } if ($key == 'descricao') {
                $descricao = $value;
              } if ($key == 'numeroHabitese') {
                $numeroHabitese = $value;
              } if ($key == 'protocolo') {
                $protocolo = $value;
              }
            }
            if (($codRetorno == 'IN001' ||
                $codRetorno == 'IN003' ||
                $codRetorno == 'IN004' ||
                $codRetorno == 'IN006'
            ) && !empty($protocolo)) {
              $aHabitesesValidos[] = array(
                'numeroHabitese'=>$numeroHabitese,
                'protocolo'=>$protocolo
              );
              $aInconsistencia[] = array(
                'tipo'=>'SUCESSO',
                'registro'=>utf8_encode('Habite-se: ').$numeroHabitese." Obra:".$value->codigoobra,
                'detalhe'=>$descricao,
                'tipoErro'=>$codRetorno
              );
            // Caso tenha retorno com erro
            } else {
              $aInconsistencia[] = array(
                'tipo'=>'ERRO',
                'registro'=>utf8_encode('Habite-se: ').$numeroHabitese." Obra:".$value->codigoobra,
                'detalhe'=>$descricao,
                'tipoErro'=>$codRetorno
              );
            }
          }
          // Caso sejam múltiplos registros
          else {
            foreach ($arrayRetornoRecepaoLote['Habitese'] as $key => $value) {
              // Caso tenha retorno com sucesso
              if (($value['codRetorno'] == 'IN001' ||
                  $value['codRetorno'] == 'IN003' ||
                  $value['codRetorno'] == 'IN004' ||
                  $value['codRetorno'] == 'IN006'
              ) && !empty($value['protocolo'])) {
                $aHabitesesValidos[] = array(
                  'numeroHabitese'=>$value['numeroHabitese'],
                  'protocolo'=>$value['protocolo']
                );
                $aInconsistencia[] = array(
                  'tipo'=>'SUCESSO',
                  'registro'=>utf8_encode('Habite-se: ').$value['numeroHabitese']." Obra:".$value->codigoobra,
                  'detalhe'=>$value['descricao'],
                  'tipoErro'=>$value['codRetorno']
                );
              // Caso tenha retorno com erro
              } else {
                $aInconsistencia[] = array(
                  'tipo'=>'ERRO',
                  'registro'=>utf8_encode('Habite-se: ').$value['numeroHabitese']." Obra:".$value->codigoobra,
                  'detalhe'=>$value['descricao'],
                  'tipoErro'=>$value['codRetorno']
                );
              }
            }
          }
        }

        // Caso não tenha retornado no array
        if (empty($arrayRetornoRecepaoLote['Alvara']) && empty($arrayRetornoRecepaoLote['Habitese'])) {
          foreach ($arrayRetornoRecepaoLote as $key => $value) {
            if ($key == 'codRetorno') {
              $codRetorno = $value;
            } if ($key == 'descricao') {
              $descricao = $value;
            }
          }
          $aInconsistencia[] = array(
            'tipo'=>'ERRO',
            'registro'=>'',
            'detalhe'=>$descricao,
            'tipoErro'=>$codRetorno
          );
        }

        // Caso tenham dados enviados com sucesso, libera inserção no banco
        if (!empty($aAlvarasValidos) || !empty($aHabitesesValidos)) {
          // $iInconsistencia = 0;
          $lIncluir        = true;
        }
        /*********** INÍCIO INCLUSÃO DE DADOS NO BANCO ***********/
        if ($lIncluir) {
          try {
            db_inicio_transacao();

            // salvamos o xml enviado com sucesso na transação
            $oObrasEnvio->ob16_data    = $dNomeArquivo;
            $oObrasEnvio->ob16_hora    = $hNomeArquivo;
            $oObrasEnvio->ob16_login   = db_getsession("DB_id_usuario");
            $oObrasEnvio->ob16_dtini   = "{$iAno}-{$iMes}-01";
            $oObrasEnvio->ob16_dtfim   = "{$iAno}-{$iMes}-{$iUltimoDiaMes}";
            $oObrasEnvio->ob16_nomearq = $filenameXml;
            $oObrasEnvio->ob16_arq     = $getXmlAlvaraHabitese;
            $oObrasEnvio->incluir(null);
            if ( (int)$oObrasEnvio->erro_status == 0 ) {
              throw new Exception ($oObrasEnvio->erro_msg);
            }

            // Caso existam alvarás enviados com sucesso
            if (!empty($aAlvarasValidos)) {
              // percorremos o array de alvarás enviados para as inclusoes
              foreach ($aAlvarasValidos as $value) {
                $sqlBuscaObra = "SELECT
                                    ob04_codobra
                                 FROM
                                    obrasalvara
                                 WHERE
                                    ob04_alvara = ".$value['numeroAlvara'];
                $resultBuscaObra = db_query($sqlBuscaObra);
                $oCodObra = db_utils::fieldsMemory($resultBuscaObra, 0);

                $oObrasEnvioReg->ob17_codobrasenvio = $oObrasEnvio->ob16_codobrasenvio;
                $oObrasEnvioReg->ob17_codobra       = $oCodObra->ob04_codobra;
                $oObrasEnvioReg->incluir(null);
                if ( (int)$oObrasEnvioReg->erro_status == 0 ) {
                  throw new Exception ($oObrasEnvioReg->erro_msg);
                }

                $oObrasEnvioRegAlvara->ob31_sequencial = null;
                $oObrasEnvioRegAlvara->ob31_obrasenvioreg = $oObrasEnvioReg->ob17_codobrasenvioreg;
                $oObrasEnvioRegAlvara->ob31_codalvara       = $value['numeroAlvara'];
                $oObrasEnvioRegAlvara->ob31_protocolo       = strval($value['protocolo']);
                $oObrasEnvioRegAlvara->incluir(null);
                if ( (int)$oObrasEnvioRegAlvara->erro_status == 0 ) {
                  throw new Exception ($oObrasEnvioRegAlvara->erro_msg);
                }
              }
            }
            // Caso existam habiteses enviados com sucesso
            if (!empty($aHabitesesValidos)) {
              // percorremos o array de habiteses enviados para as inclusoes
              foreach ($aHabitesesValidos as $value) {
                $sqlBuscaObra = "SELECT
                                    ob08_codobra
                                 FROM
                                    obrasconstr
                                    INNER JOIN obrashabite ON ob08_codconstr = ob09_codconstr
                                 WHERE
                                    ob09_codhab = ".$value['numeroHabitese'];
                $resultBuscaObra = db_query($sqlBuscaObra);
                $oCodObra = db_utils::fieldsMemory($resultBuscaObra, 0);

                $oObrasEnvioReg->ob17_codobrasenvio = $oObrasEnvio->ob16_codobrasenvio;
                $oObrasEnvioReg->ob17_codobra       = $oCodObra->ob08_codobra;
                $oObrasEnvioReg->incluir(null);
                if ( (int)$oObrasEnvioReg->erro_status == 0 ) {
                  throw new Exception ($oObrasEnvioReg->erro_msg);
                }

                $oObrasEnvioRegHab->ob18_codobraenvioreg = $oObrasEnvioReg->ob17_codobrasenvioreg;
                $oObrasEnvioRegHab->ob18_codhabite       = $value['numeroHabitese'];
                $oObrasEnvioRegHab->ob18_protocolo       = strval($value['protocolo']);
                $oObrasEnvioRegHab->incluir(null);
                if ( (int)$oObrasEnvioRegHab->erro_status == 0 ) {
                  throw new Exception ($oObrasEnvioRegHab->erro_msg);
                }
              }
            }

            db_fim_transacao(false);
          } catch (Exception $eErroBanco) {
            db_fim_transacao(true);
            $oParms = new stdClass();
            $oParms->sErro = $eErroBanco->getMessage();
            throw new Exception(_M('tributario.projetos.pro4_gerarTxtINSS.erro_processar_arquivo', $oParms));
          }
        }
        /*********** FIM INCLUSÃO DE DADOS NO BANCO ***********/

        // Cria um arquivo TXT com os dados salvos retornados da receita na pasta tmp
        if (!empty($aAlvarasValidos) || !empty($aHabitesesValidos)) {
          $sAlvarasValidos = json_encode($aAlvarasValidos);
          $sHabitesesValidos = json_encode($aHabitesesValidos);

          loggerSis("dados-salvos-receita-{$sSufixo}.txt", $sAlvarasValidos.PHP_EOL.$sHabitesesValidos);
        }

        $oRetorno->sArquivo         = $filenameXml;
        $oRetorno->aErros           = $aInconsistencia;
        $oRetorno->iInconsistencia  = 1;
        break;
      } catch (SoapFault $e) {
        dd($e);
      }

      // Declaração Sem Movimento
      // $oRecepcaoDSM  = new RecepcaoDSM($iMes,$iAno);
      // echo($oRecepcaoDSM->getRequestXml()->saveXML());

      $oRetorno->sArquivo         = $filenameXml;
      $oRetorno->aErros           = $aInconsistencia;
      $oRetorno->iInconsistencia  = $iInconsistencia;

    break;

    default:
      throw new Exception(_M('tributario.projetos.pro4_gerarTxtINSS.definia_opcao'));
    break;
  }

  $oRetorno->sMensagem = urlencode($oRetorno->sMensagem);
  echo $oJson->encode($oRetorno);

} catch (Exception $eErro){

  $oRetorno->erro      = true;
  $oRetorno->sMensagem = urlencode($eErro->getMessage());
  echo $oJson->encode($oRetorno);
}

/**
 * fuction para adicionar inconsistencias no array
 *
 * @param string  $sCampo        - Campo a ser validado
 * @param integer $iRegistroObra - codigo obra com problema
 * @param string  $sDetalhe      - tipo do registro de erro (alvara, cnpj etc. )
 * @param boolean $lObrigatorio  - Se o campo é obrigatário = true
 */
function validaDados( $mValor, $iRegistroObra, $sMensagem, $iTamanhoCampo, $lObrigatorio = true, $sAlinhamento = "R", $lValidaInteiroNulo = false ) {

  if ($sAlinhamento == "R") {

    $cStrPad  = STR_PAD_RIGHT;
    $sComplea = " ";
  }else if ($sAlinhamento == "LS") {

    $cStrPad  = STR_PAD_LEFT;
    $sComplea = " ";
  } else {

    $cStrPad  = STR_PAD_LEFT;
    $sComplea = "0";
  }

  $aErros = db_getsession("aInconsistencia");
  $oErros = new stdClass();

  $lValida = false;

  if ( empty($mValor) ) {
    $lValida = true;
  }

  /**
   * Validamos valores inteiro pois o empty nao pegara estes
   */
  if( $lValidaInteiroNulo ){

     if( (integer)$mValor <= 0 ){
       $lValida = true;
     }
  }

  if ( $lValida ) {

    if( $lObrigatorio == true ) {
      $oErros->tipo   = "ERRO";
    } else {
      $oErros->tipo   = "AVISO";
    }

    $oErros->registro = is_string($iRegistroObra) ? $iRegistroObra : "Obra: {$iRegistroObra} ";
    $oErros->detalhe  = urlencode($sMensagem);
    $aErros[]         = $oErros;
    db_putsession("aInconsistencia", $aErros);
  }

  return str_pad(trim($mValor), $iTamanhoCampo, $sComplea, $cStrPad);
}

/**
 * Verifica se é CPF ou CNPJ
 *
 * @param integer $iCgcCpf
 * @param integer $iRegistroObra
 * @return integer
 */
function getTipoIdentificacao($iCgcCpf, $iRegistroObra, $sDetalhe, $iNumCgm) {

  $aErros = db_getsession("aInconsistencia");

  // CNPJ
  if( strlen($iCgcCpf) > 11 ) {

    if( validaCNPJ($iCgcCpf) == true ) {
      return 1; // cnpj
    } else {

      $oErros = new stdClass();
      $oErros->tipo     = "ERRO";
      $oErros->registro = urlencode("Obra : {$iRegistroObra} ");
      $oErros->detalhe  = urlencode("Numcgm: {$iNumCgm} - CNPJ do {$sDetalhe} inválido: \"{$iCgcCpf}\"");
      $aErros[] = $oErros;
      //arsort($aErros);
      db_putsession("aInconsistencia", $aErros);
    }

  }
  // CPF
  else {

    if( validaCPF($iCgcCpf) == true ) {
      return 3;
    } else {

      $oErros = new stdClass();
      $oErros->tipo     = "ERRO";
      $oErros->registro = urlencode("Obra : {$iRegistroObra} ");
      $oErros->detalhe  = urlencode("Numcgm: {$iNumCgm} - CPF {$sDetalhe} inválido: \"{$iCgcCpf}\" ");
      $aErros[] = $oErros;

      db_putsession("aInconsistencia", $aErros);
    }
  }

  return 0;//erro
}

/**
 * Valida CPF
 *
 * @param string $cpf
 * @return boolean
 */
function validaCPF($cpf) {

  /**
   *  Verifiva se o número digitado contém todos os digitos
   */
  $cpf = str_pad(preg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);

  /**
   * Verifica se nenhuma das sequencias abaixo foi digitada, caso seja, retorna falso
   */
  if ( strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' ||
                             $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
    return false;

  } else  {

    /**
     * Calcula os números para verificar se o CPF é verdadeiro
     */
    for ($t = 9; $t < 11; $t++) {

      for ($d = 0, $c = 0; $c < $t; $c++) {
        $d += $cpf[$c] * (($t + 1) - $c);
      }

      $d = ((10 * $d) % 11) % 10;

      if ($cpf[$c] != $d) {
        return false;
      }
    }

    return true;
  }
}

/**
 * VERFICA CNPJ
 */
function validaCNPJ($cnpj) {

  if ((int)$cnpj == 0) {
    return false;
  }
  if (strlen($cnpj) != 14) {
    return false;
  }
  $soma = 0;
  $soma += ($cnpj[0] * 5);
  $soma += ($cnpj[1] * 4);
  $soma += ($cnpj[2] * 3);
  $soma += ($cnpj[3] * 2);
  $soma += ($cnpj[4] * 9);
  $soma += ($cnpj[5] * 8);
  $soma += ($cnpj[6] * 7);
  $soma += ($cnpj[7] * 6);
  $soma += ($cnpj[8] * 5);
  $soma += ($cnpj[9] * 4);
  $soma += ($cnpj[10] * 3);
  $soma += ($cnpj[11] * 2);

  $d1 = $soma % 11;
  $d1 = $d1 < 2 ? 0 : 11 - $d1;

  $soma = 0;
  $soma += ($cnpj[0] * 6);
  $soma += ($cnpj[1] * 5);
  $soma += ($cnpj[2] * 4);
  $soma += ($cnpj[3] * 3);
  $soma += ($cnpj[4] * 2);
  $soma += ($cnpj[5] * 9);
  $soma += ($cnpj[6] * 8);
  $soma += ($cnpj[7] * 7);
  $soma += ($cnpj[8] * 6);
  $soma += ($cnpj[9] * 5);
  $soma += ($cnpj[10] * 4);
  $soma += ($cnpj[11] * 3);
  $soma += ($cnpj[12] * 2);

  $d2 = $soma % 11;
  $d2 = $d2 < 2 ? 0 : 11 - $d2;

  if ($cnpj[12] == $d1 && $cnpj[13] == $d2) {
    return true;
  }
  else {
    return false;
  }
}

function loggerSis($sNomeArquivo, $conteudo)
{
    $sFileName = "tmp/sisobras-".$sNomeArquivo;

    $fileOpen = fopen($sFileName, "a+");
    fwrite($fileOpen, $conteudo);
    fclose($fileOpen);

    return $sFileName;
}
