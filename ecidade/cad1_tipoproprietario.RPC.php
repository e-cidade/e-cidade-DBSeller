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

use ECidade\Tributario\Cadastro\Model\Tipoproprietario;
use ECidade\Tributario\Cadastro\Model\Tipoproprietariopromitente;
use ECidade\Tributario\Cadastro\Repository\TipoProprietarioRepository;
use ECidade\Tributario\Cadastro\Repository\TipoProprietariopromitenteRepository;

$post       = db_utils::postMemory($_REQUEST);
$post->json = str_replace("\\", "", $post->json);
$parametro  = JSON::create()->parse($post->json);
$retorno    = (object)array('erro' => false, 'mensagem' => '');

try {
    db_inicio_transacao();

    switch ($parametro->executa) {
        case "salvar":
            $tipoProprietario = new Tipoproprietario();
            $tipoProprietarioRepository = TipoProprietarioRepository::getInstance();

            $tipoProprietarioPromitente = new Tipoproprietariopromitente();
            $tipoProprietarioPromitenteRepository = TipoProprietariopromitenteRepository::getInstance();

            $tipoProprietario->setTipoproprietario($parametro->j163_tipoproprietario);
            $tipoProprietario->setDescricao($parametro->j163_descricao);
            $tipoProprietario->setAbreviatura($parametro->j163_abreviatura);
            $tipoProprietario->setPesfisjur($parametro->j163_pesfisjur);
            
            //Novo Proprietario sem vinculo
            if (empty($parametro->j163_tipoproprietario) && empty($parametro->j165_tipopromitente)) {
                $tipoProprietarioRepository->persist($tipoProprietario);
            }
            //Novo Proprietario com checkbox marcado
            else if (empty($parametro->j163_tipoproprietario)) {
                $tipoProprietarioRepository->persist($tipoProprietario);                  
                $oTipoProprietario = $tipoProprietarioRepository->persist($tipoProprietario);
                $tipoProprietarioPromitente->setTipoproprietario($parametro->j163_tipoproprietario);
                //Array com os valores do checkbox do Tipo de Promitente
                $arrayTipoPromitente = explode(",",$parametro->j165_tipopromitente, -1);

                foreach ($arrayTipoPromitente as $key => $value) {
                    $tipoProprietarioPromitente->setTipoproprietario($oTipoProprietario->getTipoproprietario());
                    $tipoProprietarioPromitente->setTipopromitente($value);
                    $tipoProprietarioPromitenteRepository->persist($tipoProprietarioPromitente);                    
                }
            } 
            //Proprietario existente
            else {
                //Checkbox vazio
                if (empty($parametro->j165_tipopromitente)) {
                    //Existe vinculo
                    if ($tipoProprietarioPromitenteRepository->getVinculoByProprietario($parametro->j163_tipoproprietario)) {
                        $tipoProprietarioPromitente->setTipoproprietario($parametro->j163_tipoproprietario);
                        $tipoProprietarioPromitenteRepository->delete($tipoProprietarioPromitente);
                        $tipoProprietarioRepository->persist($tipoProprietario);                  
                    }
                    //Sem vinculo
                    else {
                        $tipoProprietarioRepository->persist($tipoProprietario);                  
                    }
                } 
                //Checkbox marcado
                else {
                    //Existe vinculo
                    if ($tipoProprietarioPromitenteRepository->getVinculoByProprietario($parametro->j163_tipoproprietario)) {
                        $tipoProprietarioPromitente->setTipoproprietario($parametro->j163_tipoproprietario);
                        $tipoProprietarioPromitenteRepository->delete($tipoProprietarioPromitente);
                        $tipoProprietarioRepository->persist($tipoProprietario);                  
                        $oTipoProprietario = $tipoProprietarioRepository->persist($tipoProprietario);
                        $tipoProprietarioPromitente->setTipoproprietario($parametro->j163_tipoproprietario);
                        //Array com os valores do checkbox do Tipo de Promitente
                        $arrayTipoPromitente = explode(",",$parametro->j165_tipopromitente, -1);

                        foreach ($arrayTipoPromitente as $key => $value) {
                            $tipoProprietarioPromitente->setTipoproprietario($oTipoProprietario->getTipoproprietario());
                            $tipoProprietarioPromitente->setTipopromitente($value);
                            $tipoProprietarioPromitenteRepository->persist($tipoProprietarioPromitente);                    
                        }
                    }
                    //Sem vinculo
                    else {
                        $tipoProprietarioRepository->persist($tipoProprietario);                  
                        $oTipoProprietario = $tipoProprietarioRepository->persist($tipoProprietario);
                        $tipoProprietarioPromitente->setTipoproprietario($parametro->j163_tipoproprietario);
                        //Array com os valores do checkbox do Tipo de Promitente
                        $arrayTipoPromitente = explode(",",$parametro->j165_tipopromitente, -1);

                        foreach ($arrayTipoPromitente as $key => $value) {
                            $tipoProprietarioPromitente->setTipoproprietario($oTipoProprietario->getTipoproprietario());
                            $tipoProprietarioPromitente->setTipopromitente($value);
                            $tipoProprietarioPromitenteRepository->persist($tipoProprietarioPromitente);                    
                        }
                    }                    
                }    
            }
                                  
            $retorno->mensagem = 'Tipo de proprietario salvo com sucesso!';

            break;

        case "excluir":
            $tipoProprietario = new Tipoproprietario();
            $tipoProprietarioPromitente = new Tipoproprietariopromitente();
            $tipoProprietarioRepository = TipoProprietarioRepository::getInstance();
            $tipoProprietarioPromitenteRepository = TipoProprietariopromitenteRepository::getInstance();

            $tipoProprietario->setTipoproprietario($parametro->j163_tipoproprietario);
            $tipoProprietarioPromitente->setTipoproprietario($parametro->j163_tipoproprietario);
            $tipoProprietarioPromitenteRepository->delete($tipoProprietarioPromitente);
            $tipoProprietarioRepository->delete($tipoProprietario);
            $retorno->mensagem = 'Tipo de proprietario removido com sucesso!';
            
            break;

        case "buscarVinculo":
            $tipoProprietarioPromitenteRepository = TipoproprietariopromitenteRepository::getInstance();
            $dados = $tipoProprietarioPromitenteRepository->getVinculoByProprietario($parametro->j163_tipoproprietario);
            
            $buscarVinculos = array();

            foreach ($dados as $key => $value) {
                $buscarVinculos[] = array(
                    "j165_tipopromitente" => $value->getTipopromitente()
                );
            }

            $retorno->lista = $buscarVinculos;

            break;

        case "lista":
            $tipoProprietario = new Tipoproprietario();

            $tipoProprietarioRepository = TipoProprietarioRepository::getInstance();

            $dados = $tipoProprietarioRepository->getLista();

            $lista = array();

            foreach ($dados as $key => $dado) {
                $lista[] = array(
                    "j163_tipoproprietario" => $dado->getTipoproprietario(),
                    "j163_descricao" => $dado->getDescricao(),
                    "j163_abreviatura" => $dado->getAbreviatura(),
                    "j163_pesfisjur" => $dado->getPesfisjur()
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
