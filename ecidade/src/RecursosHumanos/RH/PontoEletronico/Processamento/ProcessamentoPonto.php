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

namespace ECidade\RecursosHumanos\RH\PontoEletronico\Processamento;

use ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Model\ProcessamentoPontoEletronico;
use ECidade\RecursosHumanos\RH\PontoEletronico\Manutencao\Repository\EspelhoPontoCache;
use \ECidade\V3\Extension\Request;
use \ECidade\V3\Extension\Registry;
use ECidade\Task\Base;

class ProcessamentoPonto extends Base
{

    private $parametros;

    public function setParametros($parametros)
    {

        $this->parametros = (object)$parametros;
        return;
    }

    public function getParametros($parametro)
    {

        $parametros = (array)$this->parametros;
        return $parametros[$parametro];
    }

    public function doRun()
    {

        ini_set('memory_limit', '-1');
        session_start();

        $parametros = $this->parametros;

        $_SESSION   = (array)$parametros->sessao;
        $HTTP_SERVER_VARS['HTTP_HOST'] = 'localhost';
        $HTTP_SERVER_VARS['PHP_SELF']  = 'src/RecursosHumanos/RH/PontoEletronico/Processamento/ProcessamentoPonto.php';
        $_SERVER["REQUEST_URI"]        = '';

        $fakeRequest = new Request();
        Registry::set('app.request', $fakeRequest);

        require_once modification("libs/db_stdlib.php");
        require_once modification("libs/db_utils.php");
        require_once modification("libs/db_app.utils.php");
        require_once modification("libs/db_conecta" . ".php");
        require_once modification("dbforms/db_funcoes.php");


        $timeZone = date_default_timezone_get();
        date_default_timezone_set('UTC');

        try {
            db_inicio_transacao();

            $servidor                         = \ServidorRepository::getInstanciaByCodigo($parametros->iMatricula);
            $aDatasProcessarCriacaoMarcacoes  = $parametros->aDatasProcessarCriacaoMarcacoes;
            $iMatricula                       = $parametros->iMatricula;
            $this->parametros->sNome          = $servidor->getCgm()->getNome();
            $oPeriodo                         = $parametros->oPeriodo;
            $aDatasProcessar                  = $parametros->aDatasProcessar;
            $flag                             = $parametros->flag;
            $matriculaErroFatal               = $iMatricula;
            $microtime                        = microtime(true);
            $filename                         = $parametros->arquivo;

            if ($parametros->inicializarMarcacoes) {
                ProcessamentoPontoEletronico::reinicializarMarcacoesNasDatas(
                    $servidor,
                    $aDatasProcessarCriacaoMarcacoes
                );
            }

            EspelhoPontoCache::init()->invalidarCacheNoPeriodo(
                $iMatricula,
                $oPeriodo->getDataInicio(),
                $oPeriodo->getDataFim()
            );

            ProcessamentoPontoEletronico::criarMarcacoesNasDatas($iMatricula, $aDatasProcessarCriacaoMarcacoes);
            ProcessamentoPontoEletronico::processarMatriculas(array($iMatricula), $oPeriodo, $aDatasProcessar, $flag);

            $status             = "Sucesso";
            $erroProcessamento  = "";
            $matriculaErroFatal = '';

            db_fim_transacao(false);
        } catch (\Exception $erro) {
            db_fim_transacao(true);

            $texto = '';

            if (!file_exists($filename)) {
                $texto = '-->Erros ao processar o ponto'.PHP_EOL;
            }

            $texto .='-->(matricula: '.$iMatricula.')' . $erro->getMessage() . PHP_EOL;
            // . print_r($erro->getTrace(), true) . PHP_EOL;

            $status            = 'Erro';
            $erroProcessamento = $texto;

            file_put_contents($filename, $texto, FILE_APPEND);
        }

        date_default_timezone_set($timeZone);

        $matricula = "Matricula: ". $iMatricula;
        $tempo = "Tempo: ". (microtime(true) - $microtime);

        file_put_contents('tmp/tempo_por_matricula.log', $matricula ." - " . $tempo . PHP_EOL, FILE_APPEND | LOCK_EX);

        unset($this->parametros->aDatasProcessarCriacaoMarcacoes);
        unset($this->parametros->oPeriodo);
        unset($this->parametros->aDatasProcessar);
        unset($this->parametros->flag);

        return (object)array(
            "matricula" => $iMatricula,
            "tempo"     => microtime(true) - $microtime,
            "nome"      => $this->parametros->sNome,
            "status"    => $status,
            "erro"      => $erroProcessamento
        );
    }
}
