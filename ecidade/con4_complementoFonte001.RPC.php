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

use ECidade\Financeiro\Contabilidade\LancamentoContabil\Retificacao\AlteracaoLancamento;
use ECidade\Financeiro\Contabilidade\LancamentoContabil\Retificacao\InclusaoLancamento;
use ECidade\Financeiro\Orcamento\Repository\RecursoRepository as RecursoRepository;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_libcontabilidade.php"));

$parametros = JSON::create()->parse(str_replace('\\', "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->mensagem = '';
$oRetorno->erro = false;

$instituicaoSessao = db_getsession('DB_instit');
$anoSessao = db_getsession('DB_anousu');
$dataLimite = date('Y-m-d', db_getsession('DB_datausu'));
try {
    switch ($parametros->exec) {
        /**
         * @deprecated usar o case getComplementosPorFonteRecurso
         */
        case 'getComplementosPorRecurso':
            if (empty($parametros->codigo_recurso)) {
                throw new Exception('Código do recurso não informado.');
            }

            $complementosEncontrados = RecursoRepository::getComplementos($parametros->codigo_recurso, $dataLimite);
            $oRetorno->complementos = [];
            if (!empty($complementosEncontrados)) {
                $oRetorno->complementos = $complementosEncontrados;
            }

            break;
        case 'getComplementoPorDotacao':
            if (empty($parametros->codigo_dotacao)) {
                throw new Exception('Código da Dotação não informado.');
            }

            $dotacao = DotacaoRepository::getDotacaoPorCodigoAno($parametros->codigo_dotacao, $anoSessao);
            $recurso = $dotacao->getDadosRecurso();
            $fonte = $recurso->getFonteRecurso($anoSessao);

            $oRetorno->complementos = RecursoRepository::getComplementosByGestao(
                $fonte->gestao,
                $recurso->getRecurso(),
                $fonte->exercicio,
                $dataLimite
            );
            break;
        case 'getComplementosPorFonteRecurso':
            if (empty($parametros->gestao)) {
                throw new Exception('Fonte de recurso não informado.');
            }

            $complementosEncontrados = RecursoRepository::getComplementosByGestao(
                $parametros->gestao,
                $parametros->subrecurso,
                $anoSessao,
                $dataLimite
            );
            $oRetorno->complementos = [];
            if (!empty($complementosEncontrados)) {
                $oRetorno->complementos = $complementosEncontrados;
            }

            break;
        default:
            throw new \Exception('Metodo ' . $parametros->exec . ' não existe;');
            break;
    }
    db_fim_transacao(false);
} catch (Exception $oErro) {
    db_fim_transacao(true);
    $oRetorno->erro = true;
    $oRetorno->mensagem = $oErro->getMessage();
}
echo JSON::create()->stringify($oRetorno);


