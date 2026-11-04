<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                    www.dbseller.com.br
 *                 e-cidade@dbseller.com.br
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
require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_utils.php');
require_once modification('libs/JSON.php');
require_once modification('dbforms/db_funcoes.php');

use ECidade\Educacao\Escola\Censo\Censo;
use ECidade\Educacao\Escola\Censo\Identificacao\Importar;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\ImportarFactory;
use ECidade\Educacao\Escola\Censo\Registry\LogCensoRegistry;

$retorno = new stdClass();
$retorno->erro = false;
$retorno->mensagem = '';

$parametros = JSON::requestParameters();

$modulo = (int)db_getsession("DB_modulo");
$moduloEscola = 1100747;

db_inicio_transacao();
try {
    switch ($parametros->acao) {
        case 'gerarCenso':
            if (empty($parametros->ano)) {
                throw new ParameterException('É necessário informar o ano do censo.');
            }
            $escola = EscolaRepository::getEscolaByCodigo(db_getsession('DB_coddepto'));
            $censo = new Censo($parametros->ano);
            $censo->setEscola($escola);
            $retorno->arquivo_censo = '';
            $retorno->arquivo_log = '';
            $retorno->erroCPF = '';
            if ($censo->exportarMatriculaInicial()) {
                $retorno->arquivo_censo = $censo->getArquivoCenso();
            } else {
                $logs = LogCensoRegistry::get(LogCensoRegistry::MATRICULA_INICIAL)->getLogs();
                if (array_keys($logs) === [30]) {
                    foreach ($logs[30] as $log) {
                        if (str_contains($log, 'CPF ALUNO')) {
                            $somenteCPF = true;
                        }
                    }
                    foreach ($logs[30] as $log) {
                        if (!str_contains($log, 'CPF ALUNO')) {
                            $somenteCPF = false;
                        }
                    }
                    if ($somenteCPF) {
                        $retorno->erroCPF = $somenteCPF;
                        $retorno->arquivo_censo = $censo->getArquivoCenso();
                    }
                }
                $retorno->arquivo_log = $censo->getArquivoLog();
            }
            break;
        case 'importarInep':
            $censo = new Censo($parametros->ano);
            $importar = ImportarFactory::factory((int)$parametros->ano);
            $importar->setCenso($censo);

            if ($modulo === $moduloEscola) {
                $escola = EscolaRepository::getEscolaByCodigo(db_getsession('DB_coddepto'));
                $importar->addEscola($escola);
            }

            if (!empty($parametros->escolas)) {
                foreach ($parametros->escolas as $codigoEscola) {
                    $importar->addEscola(EscolaRepository::getEscolaByCodigo($codigoEscola));
                }
            }

            $importar->importarINEP(JSON::create()->parse($parametros->file));

            $retorno->arquivo_log = $importar->getLogImportacao();
            $retorno->mensagem = "Importação do INEP processada.";
            break;
        case 'gerarArquivoIdentificacao':
            $censo = new Censo($parametros->ano);
            $escolas = array_map(function ($codigo) {
                return EscolaRepository::getEscolaByCodigo($codigo);
            }, $parametros->escolas);

            $retorno->arquivo_censo = $censo->exportarArquivoIdentificacao($escolas, $parametros->data_censo);
            break;
        case 'importarArquivoIdentificacao':
            $importar = new Importar();
            $importar->importar(JSON::create()->parse($parametros->file));
            $retorno->mensagem = "Importação realizada com sucesso.";
            break;
    }
} catch (Exception $exception) {
    $retorno->erro = true;
    $retorno->mensagem = $exception->getMessage();
}

db_fim_transacao($retorno->erro);

echo JSON::create()->stringify($retorno);
