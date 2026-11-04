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
define('ALTERACAO_LOTE_TEMPLATES', 'scripts/classes/AlteracaoEmLote/templates/');

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));

$json = JSON::create();
$parametro = $json->parse(str_replace("\\","",$_POST["json"]));
$retorno = new stdClass();
$retorno->erro = false;
$retorno->mensagem = '';

$instituicaoSessao = db_getsession('DB_instit');
$anoSessao = db_getsession('DB_anousu');

try {
    db_inicio_transacao();
    switch ($parametro->exec) {
        case "getTemplate":
            if (empty($parametro->template)) {
                throw new Exception("Não foi informado o nome do template a ser carregado.");
            }

            $arquivo = ALTERACAO_LOTE_TEMPLATES . $parametro->template;
            if (!file_exists($arquivo)) {
                throw new Exception("Não foi encontrado o template desejado.");
            }
            $template = JSON::create()->parse(file_get_contents($arquivo));

            $novoTemplate = array();
            $totalTamanhoColunas = 100;
            foreach ($template as $coluna) {
                if (empty($coluna->dinamico)) {
                    $coluna->width = ((int)$coluna->width)."%";

                    if (isset($coluna->lista) && !is_array($coluna->lista) && strpos($coluna->lista, "::") !== false) {
                        $classe = explode('::', $coluna->lista);
                        if ($coluna->utilizaParametro && !empty($parametro->parametros)) {
                            $parametrosMetodo = $parametro->parametros;
                            $coluna->lista = call_user_func($classe, $parametrosMetodo);
                        } else {
                            $coluna->lista = call_user_func($classe);
                        }
                    }

                    $novoTemplate[] = $coluna;

                    $totalTamanhoColunas -= $coluna->width;
                    continue;
                }


                $classe = explode('::', $coluna->metodo);
                $colunasDinamicas = call_user_func($classe);
                $tamanhoColuna = $totalTamanhoColunas / count($parametro->filtros_adicionais);
                foreach ($colunasDinamicas as $colunaDinamica) {
                    if (!in_array($colunaDinamica->codigo, $parametro->filtros_adicionais)
                        && !empty($parametro->filtros_adicionais)) {
                        continue;
                    }

                    $novaColuna = new stdClass();
                    $novaColuna->label = $colunaDinamica->descricao;
                    $novaColuna->width = "{$tamanhoColuna}%";
                    $novaColuna->nome = str_replace('[codigo]', $colunaDinamica->codigo, $coluna->nome_coluna);
                    $novaColuna->tipo = "combo";
                    $novaColuna->editavel = true;
                    $novaColuna->valor = $novaColuna->nome;
                    $novaColuna->lista = $colunaDinamica->lista;
                    $novoTemplate[] = $novaColuna;
                }

            }
            $retorno->template = $novoTemplate;
            break;
    }
    db_fim_transacao(false);
} catch (Exception $e) {
    db_fim_transacao(true);
    $retorno->erro = true;
    $retorno->mensagem = $e->getMessage();
}
echo $json->stringify($retorno);
