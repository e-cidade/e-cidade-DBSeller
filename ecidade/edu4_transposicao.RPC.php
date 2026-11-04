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

use App\Domain\Educacao\Escola\Models\Transposicao;

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_conecta" . ".php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/JSON.php");
require_once modification("dbforms/db_funcoes.php");

$parametros = JSON::requestParameters();
$retorno = (object)array('erro' => false, 'mensagem' => '');

$codigoEscola = db_getsession('DB_coddepto');

try {
    db_inicio_transacao();
    switch ($parametros->acao) {
        case 'buscarProximaEtapa':
            if (empty($parametros->turma)) {
                throw new Exception('Informe a Turma');
            }

            $deParaEtapas = [
                19 => 20,
                20 => 21,
                21 => 22,
                22 => 23,
                23 => 1,
                1 => 2,
                2 => 3,
                3 => 4,
                4 => 5,
                5 => 9,
                9 => 6,
                6 => 7,
                7 => 8,
            ];

            $turma = new Turma($parametros->turma);
            $retorno->etapasDestino = [];

            foreach ($turma->getEtapas() as $etapa) {
                $etapaAtual = $etapa->getEtapa();
                if (array_key_exists($etapaAtual->getCodigo(), $deParaEtapas)) {
                    $retorno->etapaOrigem = $etapaAtual->getCodigo();
                    $etapaDestino = new Etapa($deParaEtapas[$etapaAtual->getCodigo()]);
                    $retorno->etapasDestino[] = (object)[
                        "codigo" => $etapaDestino->getCodigo(),
                        "descricao" => $etapaDestino->getNome()
                    ];
                }
            }
            $retorno->anoDestino = $turma->getCalendario()->getAnoExecucao()+1;
            $transposicao = Transposicao::getTransposicaoAtual();
            if (!is_null($transposicao)) {
                $retorno->transposicao = $transposicao->toArray();
            } else {
                $retorno->transposicao = null;
            }
            break;
        case 'buscarCandidatos':

            break;
    }
} catch (Exception $erro) {
    $retorno->mensagem = $erro->getMessage();
    $retorno->erro = true;
}

db_fim_transacao($retorno->erro);
echo JSON::create()->stringify($retorno);

