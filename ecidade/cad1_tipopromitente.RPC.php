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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

use ECidade\Tributario\Cadastro\Model\Tipopromitente;
use ECidade\Tributario\Cadastro\Model\Tipoproprietariopromitente;
use ECidade\Tributario\Cadastro\Repository\TipoPromitenteRepository;
use ECidade\Tributario\Cadastro\Repository\TipoProprietariopromitenteRepository;

$post = db_utils::postMemory($_REQUEST);
$post->json = str_replace("\\", "", $post->json);
$parametro = JSON::create()->parse($post->json);
$retorno = (object)array('erro' => false, 'mensagem' => '');

try {
    db_inicio_transacao();

    switch ($parametro->executa) {
        case "salvar":
            $tipoPromitente = new Tipopromitente();

            $tipoPromitenteRepository = TipoPromitenteRepository::getInstance();

            $tipoPromitente->setTipopromitente($parametro->j164_tipopromitente);
            $tipoPromitente->setDescricao($parametro->j164_descricao);

            //Novo promitente
            if (empty($parametro->j164_tipopromitente)) {
                $siglaSugestao = strtoupper(substr($parametro->j164_descricao, 0, 1));
                //Pega primeiro char da string e verifica se existe
                if ($tipoPromitenteRepository->getExisteSigla($siglaSugestao)) {
                    //Se existir, gera uma sigla aleatoria
                    while (true) {
                        $parametro->j164_promitipo = strtoupper(chr(rand(97,122)));

                        $existe = $tipoPromitenteRepository->getExisteSigla($parametro->j164_promitipo);

                        if (!$existe) {
                            break;
                        }
                    }
                }
                //Se não existir, atribui o primeiro char como sigla
                else {
                    $parametro->j164_promitipo = $siglaSugestao;
                }
            }

            $tipoPromitente->setPromitipo($parametro->j164_promitipo);

            $tipoPromitente->setAbreviatura($parametro->j164_abreviatura);

            $tipoPromitenteRepository->persist($tipoPromitente);

            $retorno->mensagem = 'Tipo de promitente salvo com sucesso!';

            break;

        case "excluir":
            $tipoPromitente = new Tipopromitente();
            $tipoPromitente->setTipopromitente($parametro->j164_tipopromitente);

            $tipoPromitenteRepository = TipoPromitenteRepository::getInstance();
            $tipoProprietarioPromitenteRepository = TipoProprietariopromitenteRepository::getInstance();
            if ($tipoProprietarioPromitenteRepository->getVinculoByPromitente($parametro->j164_tipopromitente)) {
                $retorno->mensagem = 'Tipo de promitente com vínculo não pode ser excluído.';
            } else {
                $tipoPromitenteRepository->delete($tipoPromitente);
                $retorno->mensagem = 'Tipo de promitente removido com sucesso!';
            }

            break;

        case "buscar":
            $tipoPromitenteRepository = TipoPromitenteRepository::getInstance();
            $tipoPromitente = $tipoPromitenteRepository->getByTipoPromitente($parametro->j164_tipopromitente);

            $retorno->j164_tipopromitente = $tipoPromitente->getTipopromitente();
            $retorno->j164_descricao      = $tipoPromitente->getDescricao();
            $retorno->j164_promitipo      = $tipoPromitente->getPromitipo();
            $retorno->j164_abreviatura    = $tipoPromitente->getAbreviatura();

            break;

        case "buscarVinculo":
            $tipoPromitente = new Tipopromitente();

            $tipoPromitenteRepository = TipoPromitenteRepository::getInstance();

            $dados = $tipoPromitenteRepository->getByVinculo($parametro->j41_matric);

            $lista = array();

            foreach ($dados as $key => $dado) {
                $lista[] = array(
                    "j164_tipopromitente" => $dado->getTipopromitente(),
                    "j164_descricao" => $dado->getDescricao(),
                    "j164_promitipo" => $dado->getPromitipo(),
                    "j164_abreviatura" => $dado->getAbreviatura()
                );
            }

            $retorno->lista = $lista;

            break;

        case "lista":
            $sWhere = "";

            if (isset($parametro->j41_matric)) {
                $sql = "SELECT *
                          FROM tipoproprietariopromitente
                         INNER JOIN iptubase ON j01_tipoproprietario = j165_tipoproprietario
                         WHERE j01_matric = {$parametro->j41_matric};";

                $result = db_query($sql);



                $oPromitentes = db_utils::getColectionByRecord($result);

                $sPromitentes = implode(",", array_map(function ($elements){
                    return $elements->j165_tipopromitente;
                }, $oPromitentes));

                if (!$result) {
                    throw new Exception("Erro ao buscar os promitentes vinculados ao priprietário.");
                }

                if(pg_num_rows($result) == 0){
                    throw new Exception("Nenhum tipo de proprietário cadastrado.");
                }

                $sWhere = " j164_tipopromitente IN ({$sPromitentes}) ";
            }

            $tipoPromitente = new Tipopromitente();

            $tipoPromitenteRepository = TipoPromitenteRepository::getInstance();

            $dados = $tipoPromitenteRepository->getLista($sWhere);

            $lista = array();

            foreach ($dados as $key => $dado) {
                $lista[] = array(
                    "j164_tipopromitente" => $dado->getTipopromitente(),
                    "j164_descricao" => $dado->getDescricao(),
                    "j164_promitipo" => $dado->getPromitipo(),
                    "j164_abreviatura" => $dado->getAbreviatura()
                );
            }

            $retorno->lista = $lista;

            break;

        default:
            throw new Exception('Nenhuma ação encontrada.');
            break;
    }

    db_fim_transacao(false);
} catch (Exception $erro) {
    db_fim_transacao(true);

    $retorno->erro = true;
    $retorno->mensagem = $erro->getMessage();
}

echo JSON::create()->stringify($retorno);
