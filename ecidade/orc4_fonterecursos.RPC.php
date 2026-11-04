<?php
use \ECidade\Financeiro\Orcamento\Repository\RecursoRepository as Repository;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));

$parametros = JSON::requestParameters();
$retorno = (object)['erro' => false, 'mensagem' => ''];

try {
    db_inicio_transacao();

    switch ($parametros->acao) {
        case 'infoRecurso':
            $recurso = Repository::getByCodigo($parametros->idRecurso);
            $fonte = $recurso->getFonteRecurso(db_getsession('DB_anousu'));
            $retorno->codigo = $recurso->getCodigo();
            $retorno->recurso = $recurso->getRecurso();
            $retorno->descricao = $recurso->getDescricao();
            $retorno->complemento = $recurso->getComplemento();
            $retorno->fonte = $fonte->toArray();
            $dataLimite = date('Y-m-d', db_getsession('DB_datausu'));

            $retorno->complementos =  Repository::getComplementosByGestao(
                $fonte->gestao,
                $recurso->getRecurso(),
                $fonte->exercicio,
                $dataLimite
            );
            break;

        case 'buscarComplementos':
            $subRecurso = '';

            if (!empty($parametros->idRecurso)) {
                $recurso = Repository::getByCodigo($parametros->idRecurso);
                $subRecurso = $recurso->getRecurso();
            }

            if (!empty($parametros->subrecurso)) {
                $subRecurso = $parametros->subrecurso;
            }

            if (empty($subRecurso)) {
                throw new Exception("Fonte de recurso não informada");
            }
            $dataLimite = date('Y-m-d', db_getsession('DB_datausu'));

            $retorno->complementos = Repository::getComplementosByGestao(
                $parametros->fonteRecurso,
                $subRecurso,
                db_getsession('DB_anousu')
            );
            break;
    }
} catch (Exception $erro) {
    $retorno->mensagem = $erro->getMessage();
    $retorno->erro = true;
}

db_fim_transacao($retorno->erro);
echo JSON::create()->stringify($retorno);
