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

use ECidade\RecursosHumanos\RH\Efetividade\Repository\Periodo as PeriodoRepository;
use ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Model\ProcessamentoPontoEletronico;
use ECidade\Task\TaskInterface;
use ECidade\Task\Executor\Background as BackgroundProcess;
use ECidade\RecursosHumanos\RH\PontoEletronico\Processamento\ProcessamentoPonto as ProcessamentoPontoSegundoPlano;

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_conecta" . ".php");
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("libs/JSON.php");

$oPost       = db_utils::postMemory($_REQUEST);
$oPost->json = str_replace("\\","",$oPost->json);
$oParametro  = \JSON::create()->parse($oPost->json);
$oRetorno    = (object)array( 'erro' => false, 'mensagem'=> '');

$timeStamp          = time();
$filename           = 'tmp/log_importacao_arquivo_ponto_erros'. $timeStamp .'.log';
$limiteThreads      = 20;
$microtime          = microtime(true);

try {

    db_inicio_transacao();

    switch ($oParametro->exec) {

        case "processarPontoEletronico":
            if(empty($oParametro->dataInicio)) {
                throw new ParameterException('Informe a dataInicio.');
            }

        if (empty($oParametro->matriculasEnviar) && empty($oParametro->selecao)) {
            throw new ParameterException('Informe uma(s) matrícula(s) para processar ou informe uma seleção.');
        }
      
      $time = time();
      file_put_contents('tmp/log_importacao_arquivo_ponto.log', "Requisição recebida em ". date('d/m/Y H:i:s', $time) . PHP_EOL);
      file_put_contents('tmp/marcacoes_ajustadas.txt', '');
      file_put_contents('tmp/marcacoes_nao_ajustadas.txt', '');

      $aMatriculasProcessar = array();

      if(!empty($oParametro->matriculasEnviar)) {
        $aMatriculasProcessar = $oParametro->matriculasEnviar;
      }

      if(!empty($oParametro->selecao)) {
        $selecao = $oParametro->selecao;
      }

      if(empty($oParametro->dataFim)) {
        $oParametro->dataFim = $oParametro->dataInicio;
      }
      
      $oDataInicioParametro = new DBDate($oParametro->dataInicio);
      $oDataFinalParametro  = new DBDate($oParametro->dataFim);

      if(empty($aMatriculasProcessar)) {
        
        $aMatriculasProcessar = array_keys(ServidorRepository::getMatriculasBySelecao(
          \DBPessoal::getAnoFolha(),
          \DBPessoal::getMesFolha(),
          $selecao,
          $oDataFinalParametro
        ));

        if(empty($aMatriculasProcessar)) {
          throw new BusinessException('Não há matrículas para a seleção informada.');
        }
      }

      $oParametro->inicializarMarcacoes = (bool) $oParametro->inicializarMarcacoes;

      $oRetorno->servidores = array();
      $aServidoresSucesso = array();
      $aServidoresErro = array();
      $oPeriodoRepository   = new PeriodoRepository(null, null, true);
      $aPeriodos            = $oPeriodoRepository->getPeriodosEntreDatas($oDataInicioParametro, $oDataFinalParametro);

      $time      = time();
      $textoLog  = "Começou em ". date('d/m/Y H:i:s', $time) . PHP_EOL;
      $textoLog .= 'Quantidade de matriculas: '.count($aMatriculasProcessar).PHP_EOL;

      file_put_contents('tmp/log_importacao_arquivo_ponto.log', $textoLog, FILE_APPEND);    
      db_fim_transacao(false);
      
      $backgroundProcess = new BackgroundProcess();
      $backgroundProcess->limit($limiteThreads);
      
      foreach ($aPeriodos as $oPeriodo) {
        

        $_SESSION["DB_desativar_account"] = true;
        
        db_inicio_transacao();
        file_put_contents('tmp/log_importacao_arquivo_ponto.log', "Periodo: {$oPeriodo->getDataInicio()} ate {$oPeriodo->getDataFim()}" .PHP_EOL, FILE_APPEND);

        $aDatasProcessar                 = array();
        $aDatasProcessarCriacaoMarcacoes = array();

        foreach (\DBDate::getDatasNoIntervalo($oPeriodo->getDataInicio(), $oPeriodo->getDataFim()) as $oDataProcessar) {
          $aDatasProcessar[]                 = $oDataProcessar->getDate();
          $aDatasProcessarCriacaoMarcacoes[] = (object)array('data' => $oDataProcessar->getDate());
        }
        db_fim_transacao(false);

        foreach ($aMatriculasProcessar as $key => $iMatricula) {

          file_put_contents('tmp/log_importacao_arquivo_ponto.log', "Matricula: {$iMatricula}".PHP_EOL, FILE_APPEND);

          try {
          
            $parametros = array(
              'aDatasProcessarCriacaoMarcacoes' => $aDatasProcessarCriacaoMarcacoes,
              'iMatricula' => $iMatricula,
              'oPeriodo' => $oPeriodo,
              'aDatasProcessar' => $aDatasProcessar,
              'flag' => true,
              'inicializarMarcacoes' => $oParametro->inicializarMarcacoes,
              'sessao' => $_SESSION,
              'arquivo' => $filename
            );

            $processoSegundoPlano = new ProcessamentoPontoSegundoPlano();
            $processoSegundoPlano->setParametros($parametros);

            $backgroundProcess->add($processoSegundoPlano);
            
          } catch(Exception $erro) {

            $texto    = '';
            if(!file_exists($filename)) {
              $texto = '-->Erro ao start processo'.PHP_EOL;
            }
            $texto .='-->(matricula: '.$iMatricula.')'.$erro->getMessage().PHP_EOL.print_r($erro->getTrace() ,true).PHP_EOL;
            $status = $texto;
              
            file_put_contents($filename, $texto, FILE_APPEND);
          }
        }
      }

      $backgroundProcess->start();

      foreach ($backgroundProcess->complete() as $processoExecutadoSegundoPlano) {
        
        $retornoProcesso = $processoExecutadoSegundoPlano->result();
        
        if($processoExecutadoSegundoPlano->state() == TaskInterface::STATE_FAIL) {

          $errors = $processoExecutadoSegundoPlano->errors();
          $error  = array_pop($errors);

          $msg    = $error['message'] . PHP_EOL;
          $msg   .= $error['file']    . PHP_EOL;
          $msg   .= $error['line']    . PHP_EOL;
          
          $retornoProcesso = (object)array(
            'matricula' => $processoExecutadoSegundoPlano->getParametros('iMatricula'),
            'nome'      => $processoExecutadoSegundoPlano->getParametros('sNome'),
            'status'    => 'Erro',
            'erro'      => $msg
          );
        } else {
            if ($retornoProcesso->status == 'Erro') {
                $aServidoresErro[$processoExecutadoSegundoPlano->getParametros('sNome')] = $retornoProcesso;
            } else {
                $aServidoresSucesso[$processoExecutadoSegundoPlano->getParametros('sNome')] = $retornoProcesso;
            }
        }      
        
        $oRetorno->servidores[] = $retornoProcesso;
      }

      ksort($aServidoresSucesso);
      ksort($aServidoresErro);

      foreach ($aServidoresErro as $servidorE) {
            $oRetorno->servidores[] = $servidorE;
      }

      foreach ($aServidoresSucesso as $servidorS) {
            $oRetorno->servidores[] = $servidorS;
      }
        
      unset($_SESSION["DB_desativar_account"]);

      break;
    }
} catch (Exception $eErro) {
  
  unset($_SESSION["DB_desativar_account"]);
  db_fim_transacao(true);

  $oRetorno->erro     = true;
  $oRetorno->mensagem = $eErro->getMessage();
}

file_put_contents('tmp/log_importacao_arquivo_ponto.log', PHP_EOL . "Terminou de montar os processos as ". date('d/m/Y H:i:s', time()), FILE_APPEND);
file_put_contents('tmp/log_importacao_arquivo_ponto.log', PHP_EOL . "Demorou: ". (microtime(true) - $microtime), FILE_APPEND);

unset($_SESSION["DB_desativar_account"]);
$oRetorno->message = $oRetorno->mensagem;

echo JSON::create()->stringify($oRetorno);
