<?php

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_utils.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('libs/JSON.php');

use ECidade\Core\Helpers\StringHelper;
use ECidade\RecursosHumanos\ESocial\Agendamento\ProcessamentoStaticFactory;
use ECidade\RecursosHumanos\ESocial\DadosESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\Recurso;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\Integracao\Sped\API\Enum\ConsultaTipo;
use ECidade\V3\Extension\Registry;
use ECidade\RecursosHumanos\ESocial\Entity\ExclusaoEvento;
use ECidade\RecursosHumanos\ESocial\Repository\Referencia;
use ECidade\RecursosHumanos\ESocial\ESocialContextException;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\Error as FormatterError;


$parametros = JSON::create()->parse(str_replace('\\', '', $_POST['json']));

if (!empty($parametros->layout) && empty($parametros->tipo)) {
    $parametros->tipo = Tipo::getByLayout($parametros->layout);
}

$retorno = new stdClass();
$retorno->iStatus = 1;
$retorno->sMessage = '';
try {
    db_inicio_transacao();
    switch ($parametros->exec) {
        case 'getEmpregadores':
            $where = ' r70_ativo is true ';
            $codigoInstituicao = empty($parametros->instituicao)
                ? db_getsession('DB_instit') : $parametros->instituicao;
            $where .= " and r70_instit = {$codigoInstituicao}";
            $campos = ' distinct z01_numcgm as cgm, z01_cgccpf as documento, z01_nome as nome,'
                . ' r70_instit as instituicao';
            $dao = new cl_rhlota();
            $sql = $dao->sql_query_lota_cgm(null, $campos, 'z01_numcgm', $where);
            $rs = db_query($sql);

            if (!$rs) {
                $mensagem = "Ocorreu um erro ao consultar os CGM vinculados as lotações.\nContate o suporte.";
                throw new DBException($mensagem);
            }

            if (pg_num_rows($rs) == 0) {
                throw new Exception("Não existe empregadores cadastrados na base.");
            }

            $retorno->empregadores = db_utils::getCollectionByRecord($rs);
            break;

        case 'empregador':
            if (!file_exists($parametros->sPath)) {
                throw new Exception("Houve um erro ao realizar upload do arquivo. Tente novamente.");
            }

            $conteudoArquivo = file_get_contents($parametros->sPath);
            $certs = array();

            $senha = db_stdClass::normalizeStringJsonEscapeString($parametros->senha);
            $empregador = new \stdClass();
            $empregador->inscricao = $parametros->documento;
            $empregador->razao_social = StringHelper::normalizeEncode($parametros->razao_social);
            $empregador->tipo_inscricao = strlen($parametros->documento) == 11 ? 'cpf' : 'cnpj';
            $empregador->senha = $senha;
            $empregador->certificado = base64_encode($conteudoArquivo);
            $empregador->integracao = $parametros->integracao;
            $empregador->procuracao_inscricao = '';
            $empregador->procuracao_tipo_inscricao = '';
            if (!empty($parametros->procuracao_documento)) {
                $empregador->procuracao_inscricao = $parametros->procuracao_documento;
                $empregador->procuracao_tipo_inscricao = strlen($parametros->procuracao_documento) == 11
                    ? 'cpf' : 'cnpj';
            }

            $exportar = new ESocial(Registry::get('app.config'), Recurso::CADASTRO_EMPREGADOR);
            $exportar->setDados([$empregador]);
            $retorno = $exportar->request();
            $certificado = new ESocial(Registry::get('app.config'), Recurso::CERTIFICADO_VALIDADE);
            $empregador->cnpj = $empregador->inscricao;
            unset($empregador->inscricao);
            $certificado->setDados($empregador);
            $dados = $certificado->request('GET');
            
            $retorno->sMessage = "Certificado configurado com sucesso.";
            $retorno->dataValidadeCertificado = $dados;
            unlink($parametros->sPath);
            break;

        case 'getTipos':
            /* Retorna id dos tipos e seus titulos */
            $layouts = Tipo::getDescricoes();
            if (!empty($parametros->forcado)) {
                $layouts = Tipo::getDescricoesForcadas();
            }

            if (!empty($parametros->exclusaoLote)) {
                $layouts = Tipo::getDescricoesExclusaoLote();
            }

            /* Array de tipos */
            $retorno->tipos = array();

            if (sizeof($layouts) > 0) {
                foreach ($layouts as $layout => $titulo) {
                    if (isset($parametros->integracao)) {
                        if ($parametros->integracao == Tipo::EFD_REINF && substr($titulo, 0, 1) !== 'R') {
                            continue;
                        }

                        if ($parametros->integracao == Tipo::ESOCIAL && substr($titulo, 0, 1) !== 'S') {
                            continue;
                        }
                    }

                    $retorno->tipos[] = array(
                        'titulo' => $titulo,
                        'layout' => $layout
                    );
                }
            } else {
                throw new Exception("Não existe arquivos para envio.");
            }
            break;

        case 'getCertificado':
            $certificado = new ESocial(Registry::get('app.config'), Recurso::CERTIFICADO_VALIDADE);
            $where = " z01_numcgm = {$parametros->cgmEmpregador}";
            $campos = ' distinct  z01_cgccpf as empregador ';
            $dao = new cl_rhlota();
            $sql = $dao->sql_query_lota_cgm(null, $campos, null, $where);
            $rs = db_query($sql);
            $cnpjEmpregador = \db_utils::fieldsMemory($rs, 0)->empregador;
            $integracao = $parametros->integracao;
            if (isset($parametros->integracao)) {
                if ($parametros->integracao == 1) {
                    $cnpjEmpregador = CgmRepository::buscarCNPJEmpregador($parametros->cgmEmpregador);
                }
            }
            $body = ['cnpj' => $cnpjEmpregador, 'integracao' => $integracao];
            $certificado->setDados($body);
            $dados = $certificado->request('GET');
            if (!isset($dados->erro)) {
                $hoje = date('Y-m-d');
                $diferenca = strtotime($dados->dataValidadeCertificado) - strtotime($hoje);
                $dias = (int) floor($diferenca / (60 * 60 * 24));
                $dataMsg = date('d/m/Y',  strtotime($dados->dataValidadeCertificado));
                $retorno->msgCertificado = 'Data do Certificado Digital atual possui validade até <strong>' . $dataMsg . '</strong>.';
                $retorno->tipoAlerta = 'Verde';
                if ($dias < 31) {
                    $retorno->tipoAlerta = 'Laranja';
                    $retorno->msgCertificado = '<strong>Atenção!</strong> Faltam <strong>' . $dias . '</strong> para o Certificado Digital expirar. Data do Certificado Digital atual possui validade até <strong>' . $dataMsg . '</strong>.';
                }
                if ($dias < 1) {
                    $retorno->tipoAlerta = 'Vermelho';
                    $retorno->msgCertificado = '<strong>Atenção!</strong> Certificado Digital expirado. Data do Certificado Digital atual possui validade até <strong>' . $dataMsg . '</strong>.';
                }
            } else {
                $retorno->tipoAlerta = 'Vermelho';
                $retorno->msgCertificado = "<strong>Atenção! </strong>"
                    . mb_convert_encoding($dados->erro, 'ISO-8859-1', 'UTF-8');
            }
            break;

        case 'getTiposRetorno':
            $integracao = isset($parametros->integracao) ? $parametros->integracao : null;
            $tipos = ConsultaTipo::tipos(null, $integracao);
            /* Array de tipos */
            $retorno->tipos = array();

            if (sizeof($tipos) > 0) {
                foreach ($tipos as $tipo => $titulo) {
                    $retorno->tipos[] = array(
                        'tipo' => substr_replace($tipo, "-", 1, 0),
                        'titulo' => $titulo,
                        'layout' => $tipo
                    );
                }
            } else {
                throw new Exception("Não existe arquivos para envio.");
            }
            break;

        case 'getDeParaEventosRetorno':
            if (empty($parametros->strEvento)) {
                throw new ParameterException("É necessário escolher um evento.");
            }

            $strEvento = $parametros->strEvento;
            $dePara = ConsultaTipo::getDeParaEventosRetorno($strEvento);
            $retorno->eventos = $dePara;

            break;

        case 'enviarEventosParaApi':
            // finalizamos a transacao para evitar gerar inconsistencias com dados da api
            db_fim_transacao();
            if ((isset($parametros->anoCaixa) && !empty($parametros->anoCaixa)) &&
                (isset($parametros->mesCaixa) && !empty($parametros->mesCaixa))
            ) {

                $parametros->ano = $parametros->anoCaixa;
                $parametros->mes = $parametros->mesCaixa;
            }

            if (empty($parametros->cgm)) {
                $mensagem = "É necessário escolher um empregador para realizar o envio das informações.";
                throw new ParameterException($mensagem);
            }

            if (!empty($parametros->selecao)) {
                $servidores = ServidorRepository::getServidoresBySelecao(DBPessoal::getAnoFolha(), DBPessoal::getMesFolha(), $parametros->selecao);
                foreach ($servidores as $servidor) {
                    $parametros->matriculas[] = $servidor->getMatricula();
                }
            }

            if (isset($parametros->rubricas) && !empty($parametros->rubricas)) {
                $parametros->matriculas = $parametros->rubricas;
            }

            /**
             * Realiza um ping na API para verificar configurações e disponibilidade do serviço
             */
            $esocialApi = new ESocial(Registry::get('app.config'), "/ping");
            $response = $esocialApi->request("GET");

            $where = " rh213_situacao = 1 and rh213_empregador = " . $parametros->cgm;
            $join = '';

            if (!empty($parametros->layout)) {
                $where .= " AND rh213_evento =  '{$parametros->layout}'";
                switch ($parametros->layout) {

                    case '2230':
                        if (!empty($parametros->matriculas)) {
                            $where .= DadosESocial::buscaCondicaoResponsavelPreenchimento(
                                $parametros->tipo,
                                $parametros->matriculas,
                                (object)[
                                    "ano" => $parametros->ano,
                                    "mes" => $parametros->mes
                                ],
                                $parametros->indicativoPeriodoApuracao,
                                null,
                                null,
                                $parametros->cgm
                            );
                        } else {
                            $where .= DadosESocial::buscaCondicaoResponsavelPreenchimento(
                                $parametros->tipo,
                                [],
                                (object)[
                                    "ano" => $parametros->ano,
                                    "mes" => $parametros->mes
                                ],
                                null,
                                null,
                                null,
                                $parametros->cgm
                            );
                        }

                        break;

                    case '3000':

                        $join = DadosESocial::buscaCondicaoResponsavelPreenchimento(
                            $parametros->tipo,
                            null,
                            null,
                            null,
                            $parametros->dataPreenchidaInicio,
                            $parametros->dataPreenchidaFim
                        );

                    default:

                        if (!empty($parametros->matriculas)) {
                            $where .= DadosESocial::buscaCondicaoResponsavelPreenchimento(
                                $parametros->tipo,
                                $parametros->matriculas,
                                (object)[
                                    "ano" => $parametros->ano,
                                    "mes" => $parametros->mes
                                ],
                                $parametros->indicativoPeriodoApuracao
                            );
                        }
                        break;
                }
            }
            $daoEsocialEnvio = new cl_esocialenvio();
            $sqlEsocialEnvio = $daoEsocialEnvio->sql_query_file(
                null,
                "rh213_sequencial as id, rh213_evento as tipoevento, rh213_dados as evento",
                null,
                $where,
                $join
            );

            if ($parametros->layout == '1010') {
                $codigoInstituicao = empty($parametros->instituicao)
                    ? db_getsession('DB_instit') : $parametros->instituicao;
                $subQueryRubrica = explode('from', $sqlEsocialEnvio);
                $subQueryRubrica[1] = str_replace(
                    "esocialenvio",
                    "from esocial.esocialenvio
                     inner join pessoal.rhrubricas  on rh27_rubric = rh213_responsavelpreenchimento
                     and rh27_ativo
                     and rh27_instit = {$codigoInstituicao} ",
                    $subQueryRubrica[1]
                );
                $sqlEsocialEnvio = $subQueryRubrica[0] . $subQueryRubrica[1];
            }
            $rsEsocialEnvio = db_query($sqlEsocialEnvio);

            if (!$rsEsocialEnvio) {
                throw new Exception("Não foi possível buscar os dados dos preenchimentos.\nContate o suporte.");
            }

            if (pg_num_rows($rsEsocialEnvio) == 0) {
                $mensagem = "Nenhum preenchimento pendente foi encontrado. Realize as alterações necessárias e processe"
                    . " novamente os dados.";
                throw new Exception($mensagem);
            }

            $esocialEnvios = db_utils::getCollectionByRecord($rsEsocialEnvio);

            foreach ($esocialEnvios as $envio) {
                db_inicio_transacao();
                $sRecurso = Recurso::getRecursoByEvento($envio->tipoevento);

                $exportar = new ESocial(Registry::get('app.config'), $sRecurso);
                try {
                    $exportar->setDados(array(json_decode($envio->evento)));
                    $exportar->sendEvent();
                    $exportar->updateEvento($envio->id, 2);
                    $exportar->updateEventStatus($envio->id, true, "Enviado para a API");
                } catch (ESocialContextException $e) {

                    $message = sprintf("Erro ao enviar para API\n%s", DBString::utf8_decode_all($e->getMessage()));                    
                    $context = $e->getContext();
        
                    // evento recusado, formatado retorno para buscar descricao dos campos do layout
                    if ($e->getCode() == 422 && is_object($context)
                        && isset($context->errors) && is_array($context->errors)) {
        
                        $errorFormatter = new FormatterError($envio->tipoevento, true);
                        $message = $errorFormatter->formatErrors($context->errors);
                    }
        
                    $exportar->updateEvento($envio->id, 1, true);
                    $exportar->updateEventStatus($envio->id, false, $message);
                } catch (Exception $e) {
                    $exportar->updateEvento($envio->id, 1, true);
                    $exportar->updateEventStatus(
                        $envio->id, false, "Ocorreu um erro técnico, tente novamente mais tarde."
                    );
                }
                db_fim_transacao();
            }

            $mensagem = "Envio finalizado com sucesso.\nConsulte as informações em Consultas > Situação de"
                . " Eventos.";
            $retorno->sMessage = $mensagem;

            break;

        case 'agendarEnvioLote':
            ini_set("memory_limit", "-1");
            $instituicao = db_getsession("DB_instit");
            $processamentoInstance = ProcessamentoStaticFactory::factory(
                $parametros->tipo,
                $parametros->cgm,
                $instituicao,
                $parametros->layout,
                $parametros->ano,
                $parametros->mes
            );
            if (!empty($parametros->matriculas)) {
                $servidores = array_map(function ($matricula) {
                    return ServidorRepository::getInstanciaByCodigo($matricula);
                }, $parametros->matriculas);
                $processamentoInstance->setServidores($servidores);
            }

            if (!empty($parametros->indicativoPeriodoApuracao)) {
                $processamentoInstance->setIndicativoPeriodoApuracao($parametros->indicativoPeriodoApuracao);
            }

            if ($parametros->forcado) {
                $processamentoInstance->setEnvioForcado(true);
            }

            if (!empty($parametros->anocaixa)) {
                $processamentoInstance->setAnoCaixa($parametros->anocaixa);
            }

            if (!empty($parametros->mescaixa)) {
                $processamentoInstance->setMesCaixa($parametros->mescaixa);
            }

            if (!empty($parametros->ano)) {
                $processamentoInstance->setAnoCompetencia($parametros->ano);
            }

            if (!empty($parametros->mes)) {
                $processamentoInstance->setMesCompetencia($parametros->mes);
            }

            if (!empty($parametros->selecao)) {
                $processamentoInstance->setSelecao($parametros->selecao);
            }

            if (!empty($parametros->tipoDataPagamento)) {
                $processamentoInstance->setTipoDataPagamento($parametros->tipoDataPagamento);
            }

            if (isset($parametros->forcarMatricula) && !empty($parametros->forcarMatricula)) {
                $processamentoInstance->setForcarMatricula($parametros->forcarMatricula);
            }

            if (isset($parametros->dataInicio) && !empty($parametros->dataInicio)) {
                $processamentoInstance->setDataInicial($parametros->dataInicio);
            }

            if (isset($parametros->dataFim) && !empty($parametros->dataFim)) {
                $processamentoInstance->setDataFinal($parametros->dataFim);
            }

            if (isset($parametros->dataPreenchidaInicio) && !empty($parametros->dataPreenchidaInicio)) {
                $processamentoInstance->setDataPreenchidaInicial($parametros->dataPreenchidaInicio);
            }

            if (isset($parametros->dataPreenchidaFim) && !empty($parametros->dataPreenchidaFim)) {
                $processamentoInstance->setDataPreenchidaFinal($parametros->dataPreenchidaFim);
            }
            if (!empty($parametros->rubricas)) {
                $processamentoInstance->setlistaRubricas($parametros->rubricas);
            }

            if (isset($parametros->indFechReab)) {
                $processamentoInstance->setindFechReab($parametros->indFechReab);
            }

            $alteracao = $processamentoInstance->processar();
            $titulo = Tipo::getDescricoes($parametros->layout);
            $retorno->sMessage = "Processamento realizado para o arquivo {$titulo}.";

            if (!$alteracao) {
                $retorno->sMessage = "Não encontramos alterações para o arquivo {$titulo}.";
            }

            $limpaCache = new ESocial(Registry::get('app.config'), Recurso::LIMPA_CACHE);
            $limpaCache->request("GET");
            break;

        case "gerarCargaExclusao":
            if (empty($parametros->cgm)) {
                throw new \ParameterException("Empregador não informado.");
            }

            if (empty($parametros->layout)) {
                throw new \ParameterException("Evento não informado.");
            }

            $referencias = new Referencia();
            $filtros = new stdClass();

            $referencias->setLayout($parametros->layout);

            if (!empty($parametros->ano)) {
                $referencias->setAno($parametros->ano);
            }

            if (!empty($parametros->mes)) {
                $referencias->setMes($parametros->mes);
            }

            if (!empty($parametros->ano) && !empty($parametros->mes)) {
                $filtros->competencia = $parametros->ano . $parametros->mes;
            }

            if (!empty($parametros->anoCaixa)) {
                $referencias->setAnoCaixa($parametros->anoCaixa);
                $referencias->setAno($parametros->anoCaixa);
            }

            if (!empty($parametros->mesCaixa)) {
                $referencias->setMesCaixa($parametros->mesCaixa);
                $referencias->setMes($parametros->mesCaixa);
            }

            if (!empty($parametros->mesCaixa) && !empty($parametros->anoCaixa)) {
                $filtros->competencia = $parametros->anoCaixa . $parametros->mesCaixa;
            }

            if (!empty($parametros->matriculas)) {
                $referencias->setMatriculas($parametros->matriculas);
            }

            if (!empty($parametros->selecao)) {
                $referencias->setSelecao(($parametros->selecao));
            }

            if (!empty($referencias->getCompetencia())) {
                $filtros->competencia = $parametros->ano . $parametros->mes;
            }

            if (!empty($parametros->indicativoPeriodoApuracao)) {
                $referencias->setIndicativoApuracao(($parametros->indicativoPeriodoApuracao));
            }

            $referencias->setCgmEmpregador($parametros->cgm);

            $referencias->buscarDados();
            $eSocial = new ESocial(Registry::get('app.config'), Recurso::CONSULTA_RECIBO);
            $cgm = CgmFactory::getInstanceByCgm($parametros->cgm);

            $filtros->inscricaoEmpregador = $cgm->getCnpj();
            $filtros->naoExcluidos = true;

            if (!empty($referencias->getReferencias())) {
                $filtros->referencias = $referencias->getReferencias();
            }
            if (!empty($parametros->indicativoPeriodoApuracao)) {
                if (!empty($referencias->getCompetencia())) {
                    $filtros->competencia = $referencias->getCompetencia();
                }
            }
            $filtros->idEvento = $parametros->layout;
            $filtros->eventoExclusao = true;
            $eSocial->setDados($filtros);
            $dados = $eSocial->request('GET');
            /**
             * A partir daqui as informacoes foram copiadas do rpc de exclusao
             * Seram dados fixos pois nao temos tempo habio de deixar dinamico
             */
            foreach ($dados as $exclusao) {
                $dadoExcluir = JSON::create()->parse($exclusao->evento);
                $avaliacao = AvaliacaoRepository::getAvaliacaoByCodigo(ExclusaoEvento::AVALIACAO);
                $avaliacaoAdapter = new AvaliacaoEsocialAdapter($avaliacao);
                $formulario = $avaliacaoAdapter->getObject();
                $dadosParametros = [];
                $numeroRecibo = "";
                foreach ($formulario->grupos as $chave => $grupo) {
                    foreach ($grupo->perguntas as $key => $pergunta) {
                        switch ($pergunta->identificador_campo) {
                            case "tpEvento":
                                $pergunta->respostas[0]->valor = "S-{$parametros->layout}";
                                $dadosParametros["tpEvento"] = "S-{$parametros->layout}";
                                break;
                            case "nrRecEvt":
                                foreach ($exclusao->recibo as $recibo) {
                                    if ($recibo->ultimoRecibo && !$recibo->excluido) {
                                        $numeroRecibo = $recibo->numero;
                                        $pergunta->respostas[0]->valor = $recibo->numero;
                                        $dadosParametros["nrRecEvt"] = $recibo->numero;
                                    }
                                }
                                break;
                            case "cpfTrab":
                                switch ($parametros->layout) {
                                    case '1210':
                                        $pergunta->respostas[0]->valor = $dadoExcluir->ideBenef->cpfBenef;
                                        $dadosParametros["cpfTrab"] = $dadoExcluir->ideBenef->cpfBenef;
                                        break;
                                    case '2230':
                                        $pergunta->respostas[0]->valor = $dadoExcluir->ideVinculo->cpfTrab;
                                        $cpf = db_formatar($dadoExcluir->ideVinculo->cpfTrab, "CPF");
                                        $dadosParametros["cpfTrab"] = $cpf;
                                        break;
                                    case '1200':
                                    case '1202':
                                        $pergunta->respostas[0]->valor = $dadoExcluir->ideTrabalhador->cpfTrab;
                                        $cpf = db_formatar($dadoExcluir->ideTrabalhador->cpfTrab, "CPF");
                                        $dadosParametros["cpfTrab"] = $cpf;
                                        break;
                                }
                                break;
                            case "nisTrab":
                                switch ($parametros->layout) {
                                    case '2230':
                                        $pergunta->respostas[0]->valor = $dadoExcluir->ideVinculo->nisTrab;
                                        $dadosParametros["nisTrab"] = $dadoExcluir->ideVinculo->nisTrab;
                                        break;
                                }
                                break;
                            case "indApuracao":
                                switch ($parametros->layout) {
                                    case '1200':
                                    case '1202':
                                        // indice 0 = Mensal - 1 =  Anual
                                        $pergunta->respostas[0]->valor = 1;
                                        $pergunta->respostas[1]->valor = 0;
                                        if (!empty($parametros->indicativoPeriodoApuracao)) {
                                            // Anual
                                            if ($parametros->indicativoPeriodoApuracao == 2) {
                                                $pergunta->respostas[0]->valor = 0;
                                                $pergunta->respostas[1]->valor = 1;
                                            }
                                        }
                                        break;
                                }
                                break;
                            case "perApur":
                                switch ($parametros->layout) {
                                    case '1200':
                                    case '1202':
                                        $dadosParametros["perApur"] = "{$parametros->ano}-{$parametros->mes}";
                                        $pergunta->respostas[0]->valor = "{$parametros->ano}-{$parametros->mes}";
                                        if (!empty($parametros->indicativoPeriodoApuracao)) {
                                            // Anual
                                            if ($parametros->indicativoPeriodoApuracao == 2) {
                                                $dadosParametros["perApur"] = "{$parametros->ano}";
                                                $pergunta->respostas[0]->valor = "{$parametros->ano}";
                                            }
                                        }
                                        break;
                                    case '1210':
                                        $dadosParametros["perApur"] = "{$parametros->anoCaixa}-{$parametros->mesCaixa}";
                                        $pergunta->respostas[0]->valor = "{$parametros->anoCaixa}-{$parametros->mesCaixa}";
                                        break;
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }
                // Caso não tenha recibo, ignoramos o registro
                if (!isset($dadosParametros["nrRecEvt"]) || empty($dadosParametros["nrRecEvt"])) {
                    continue;
                }
                // Salvando os formularios
                $dao = new cl_avaliacaogruporespostaexclusaoeventos();
                $sql = $dao->sql_query_file(
                    null,
                    'eso14_avaliacaogruporesposta',
                    null,
                    "eso14_protocolo = '{$dadosParametros["nrRecEvt"]}'"
                );
                $rs = db_query($sql);

                if (!$rs) {
                    throw new Exception("Não foi possível verificar se há um preenchimento referente ao número de recibo {$parametros->nrRecEvt}.");
                }
                $preenchimento = pg_num_rows($rs) > 0 ? pg_fetch_object($rs)->eso14_avaliacaogruporesposta : null;
                $avaliacao = AvaliacaoRepository::getAvaliacaoByCodigo(ExclusaoEvento::AVALIACAO);
                $avaliacao->setAvaliacaoGrupo($preenchimento);
                $dadosParametros['iCodigoPreenchimento'] = $avaliacao->getAvaliacaoGrupo();
                $dadosParametros['empregador'] = $parametros->cgm;
                $avaliacaoESocial = new AvaliacaoESocial();
                $avaliacaoESocial->setAvaliacao($avaliacao);
                $avaliacaoESocial->setPerguntasRespostas($formulario);
                $avaliacaoESocial->salvar(null, Tipo::EXCLUSAO_EVENTOS, $dadosParametros);
                unset($avaliacaoESocial);
                unset($dadosParametros);
                unset($exclusao);
                unset($recibo);
            }
            $retorno->sMessage = "Processamento de Exclusão em lote realizada com sucesso.";

            break;
            // PLUGIN ENVIOESOCIAL - Adiciona os cases enviar e consultar
    }
} catch (Exception $eErro) {
    $retorno->iStatus = 2;
    $retorno->sMessage = $eErro->getMessage();
    $retorno->Code = $eErro->getCode();
    $retorno->msgCertificado = $eErro->getMessage();
}

$retorno->erro = $retorno->iStatus === 2;

db_fim_transacao($retorno->erro);
echo JSON::create()->stringify($retorno);
