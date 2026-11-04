<?php

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_utils.php');
require_once modification("dbforms/db_funcoes.php");

use ECidade\RecursosHumanos\ESocial\Entity\ExclusaoEvento;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocialEnvio;
use ECidade\RecursosHumanos\ESocial\Integracao\Recurso;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\V3\Extension\Registry;

$parametros = JSON::requestParameters();

try {
    db_inicio_transacao();

    $retorno = new stdClass();
    $retorno->erro = false;

    switch ($parametros->acao) {
        case 'buscar':
            $avaliacao = AvaliacaoRepository::getAvaliacaoByCodigo(ExclusaoEvento::AVALIACAO);
            $avaliacaoAdapter = new AvaliacaoEsocialAdapter($avaliacao);
            $retorno->formulario = $avaliacaoAdapter->getObject();
            break;
        case 'salvar':
            $dao = new cl_avaliacaogruporespostaexclusaoeventos();
            $sql = $dao->sql_query_file(null, 'eso14_avaliacaogruporesposta', null,
                "eso14_protocolo = '{$parametros->nrRecEvt}'");
            $rs = db_query($sql);

            if (!$rs) {
                throw new Exception("Não foi possível verificar se há um preenchimento referente ao número de recibo {$parametros->nrRecEvt}.");
            }

            $preenchimento = null;

            $perguntasRespostas = JSON::create()->parse($parametros->perguntasRespostas);
            $avaliacao = AvaliacaoRepository::getAvaliacaoByCodigo(ExclusaoEvento::AVALIACAO);
            $avaliacao->setAvaliacaoGrupo($preenchimento);

            $parametrosAvaliacao = (array)$parametros;
            $parametrosAvaliacao['iCodigoPreenchimento'] = $avaliacao->getAvaliacaoGrupo();

            $avaliacaoESocial = new AvaliacaoESocial();
            $avaliacaoESocial->setAvaliacao($avaliacao);
            $avaliacaoESocial->setPerguntasRespostas($perguntasRespostas);
            $avaliacaoESocial->salvar(null, Tipo::EXCLUSAO_EVENTOS, $parametrosAvaliacao);

            $retorno->mensagem = 'Formulário salvo com sucesso!';
            break;
        case 'buscarEmpregador':
            $codigoInstituicao = db_getsession('DB_instit');

            $sqlCgm = "
                SELECT DISTINCT
                  z01_numcgm                      AS cgm,
                  z01_cgccpf || ' - ' || z01_nome AS empregador
                FROM rhlota
                  INNER JOIN cgm ON rhlota.r70_numcgm = cgm.z01_numcgm
                WHERE r70_instit = {$codigoInstituicao}
                ORDER BY z01_numcgm
            ";

            $resultadoSqlCgm = db_query($sqlCgm);

            if (!$resultadoSqlCgm) {
                throw new DBException("Não foi possível buscar os empregadores da instituição {$codigoInstituicao}.");
            }

            if (pg_num_rows($resultadoSqlCgm) === 0) {
                throw new DBException("Não há empregadores cadastrados para a instituição {$codigoInstituicao}.");
            }

            $retorno->empregadores = db_utils::getCollectionByRecord($resultadoSqlCgm);
            break;
        case 'consultarRecibos':
            $parametros->aFiltros = JSON::create()->parse($parametros->aFiltros);

            if (empty($parametros->aFiltros->inscricaoEmpregador)) {
                throw new \ParameterException("Empregador não informado.");
            }

            if (empty($parametros->aFiltros->idEvento)) {
                throw new \ParameterException("Evento não informado.");
            }

            $eSocial = new ESocial(Registry::get('app.config'), Recurso::CONSULTA_RECIBO);
            $cgm = CgmFactory::getInstanceByCgm($parametros->aFiltros->inscricaoEmpregador);
            $parametros->aFiltros->inscricaoEmpregador = $cgm->getCnpj();
            $parametros->aFiltros->naoExcluidos = true;
            if ($parametros->aFiltros->cpf) {
                switch ($parametros->aFiltros->idEvento) {
                    case Tipo::S1207:
                    case Tipo::S1210:
                    case Tipo::S2400:
                    case Tipo::S2410:
                        $parametros->aFiltros->eventojson = json_encode(array('cpfBenef' => $parametros->aFiltros->cpf));
                        break;
                    default:
                        $parametros->aFiltros->eventojson = json_encode(array('cpfTrab' => $parametros->aFiltros->cpf));
                        break;
                }
            }
            $eSocial->setDados($parametros->aFiltros);
            $dados = $eSocial->request('GET');
            if (empty($dados)) {
                throw new \BusinessException("Não há nenhum recibo cadastrado para o empregador {$cgm->getCnpj()}.");
            }
            $retorno->recibos = array();
            foreach ($dados as $dado) {
                $eSocialEnvio = new ESocialEnvio();
                $eSocialEnvio->setEvento(preg_replace('/[^0-9]/', '', $dado->tipo));
                $eSocialEnvio->setEmpregador($dado->empregador->inscricao);
                $eSocialEnvio->setDados($dado->evento);
                $eSocialEnvio->setResponsavelPreenchimento($dado->referencia);

                foreach ($dado->recibo as $recibo) {
                    $item = new stdClass();
                    $item->cpftrab = '';

                    switch ($parametros->aFiltros->idEvento) {
                        case Tipo::S1207:
                        case Tipo::S1210:
                            $item->cpftrab = JSON::hasKey('cpfBenef', $dado->evento)
                                ? db_formatar(JSON::search('cpfBenef', $dado->evento), 'CPF')
                                : '';
                            break;
                        default:
                            $item->cpftrab = JSON::hasKey('cpfTrab', $dado->evento)
                                ? db_formatar(JSON::search('cpfTrab', $dado->evento), 'CPF')
                                : '';
                            break;
                    }
                    $item->tipo_evento = $dado->tipo;
                    $data = '';
                    if (!empty($recibo->updated_at)) {
                        $data = date('d/m/Y H:i', strtotime($recibo->updated_at));
                    }
                    $item->data = $data;
                    $item->numero = $recibo->numero;
                    $item->descricao = $eSocialEnvio->getDescricaoIdentificacao();

                    $retorno->recibos[] = $item;
                }
            }

            break;
    }
} catch (Exception $exception) {
    $retorno->mensagem = $exception->getMessage();
    $retorno->erro = true;
}

db_fim_transacao($retorno->erro);

echo JSON::create()->stringify($retorno);
