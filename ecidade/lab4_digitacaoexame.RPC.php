<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2019  DBSeller Servicos de Informatica
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

use ECidade\Lib\Formula\Calculo;
use ECidade\Saude\Laboratorio\Atributo\Formula;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Enum\Parametros as ParametrosEnum;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Importacao\ImportacaoResultado;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Builder\ImportacaoInconsistencia;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\IntegraResultados;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\Parametros as ParametrosService;
use ECidade\Saude\Laboratorio\Repository\LaboratorioRepository;
use ECidade\Saude\Laboratorio\Service\ItemRequisicaoService as ItemRequisicaoService;
use ECidade\Saude\Laboratorio\Repository\ItemRequisicaoRepository as ItemRequisicaoRepository;
use ECidade\Saude\Laboratorio\Service\LaboratorioService;
use FormulaInterpreter\Exception\UnknownVariableException;
use FormulaInterpreter\Parser\ParserException;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("model/laboratorio/AtributoExame.model.php"));


define('MSG_LAB4DIGITACAOEXAME', 'saude.laboratorio.lab4_digitacaoexameRPC.');

$oJson = new services_json();
$oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));

$oRetorno = new stdClass();
$oRetorno->status = 1;
$oRetorno->message = '';

try {
    db_inicio_transacao();
    switch ($oParam->exec) {
        case 'getAtributosDoExame':
            // $oParam->requisicao não é o código da requisição e sim do item da requisicao
            $oRequisicao = new RequisicaoExame($oParam->requisicao);
            $oExame = $oRequisicao->getExame();
            $oResultadoExame = $oRequisicao->getResultado();
            $oRetorno->sObservacao = urlencode($oRequisicao->getObservacao());
            $oRetorno->sMotivoNovaColeta = urlencode($oRequisicao->getMotivoNovaColeta());
            $oRetorno->atributos = array();
            $oRetorno->dadosAtributos = array();

            foreach ($oResultadoExame->getResultadoCompleto() as $oResultadoAtributo) {
                $oAtributo = $oResultadoAtributo->getAtributo();
                $oAtributoStd = new stdClass();
                $oAtributoStd->codigo = urlencode($oAtributo->getCodigo());
                $oAtributoStd->codigo_estrutural = urlencode($oAtributo->getEstrutural());
                $oAtributoStd->descricao = urlencode($oAtributo->getNome());
                $oAtributoStd->tipo = urlencode($oAtributo->getTipo());
                $oAtributoStd->nivel = urlencode($oAtributo->getNivel());
                $oAtributoStd->tiporeferencia = $oAtributo->getTipoReferencia();
                $oAtributoStd->valorpercentual = '';
                $oAtributoStd->valorabsoluto = '';
                $oAtributoStd->codigoreferencia = '';
                $oAtributoStd->titulacao = '';
                $oAtributoStd->temFormula = false;
                $oAtributoStd->formula = '';

                if ($oAtributo->getFormula() !== '') {
                    $oAtributoStd->temFormula = true;
                    $oAtributoStd->formula = urlencode("Fórmula: {$oAtributo->getFormula()}");

                    $oDadosAtrStd = new stdClass();
                    $oDadosAtrStd->codigo_atributo = $oAtributo->getCodigo();
                    $oDadosAtrStd->atributosFormula = $oAtributo->getAtributosFormula($oExame);

                    $oRetorno->dadosAtributos[] = $oDadosAtrStd;
                }

                $oAtributoStd->titulacao = urlencode($oResultadoAtributo->getTitulacao());

                $oAtributoStd->valorabsoluto = urlencode($oResultadoAtributo->getValorAbsoluto());
                $oAtributoStd->valorpercentual = $oResultadoAtributo->getValorPercentual();
                $oReferenciaSalva = $oResultadoAtributo->getFaixaUtilizada();

                $oAtributoStd->referencia = '';
                if ($oAtributoStd->tiporeferencia != '') {
                    $oReferenciasAtributo = $oReferenciaSalva;

                    if (empty($oReferenciasAtributo) || $oReferenciasAtributo->getCodigo() == '') {
                        $oReferenciasAtributo = $oAtributo->getValoresDeReferenciaParaExame($oRequisicao);
                    }

                    $iCasasDecimais = null;

                    if ($oReferenciasAtributo instanceof AtributoValorReferenciaNumerico) {
                        $iCasasDecimais = $oReferenciasAtributo->getCasasDecimaisApresentacao();
                    }

                    $oReferenciaStd = new stdClass();
                    $oReferenciaStd->unidade = '';

                    if ($oAtributo->getUnidadeMedida() != null) {
                        $oReferenciaStd->unidade = urlencode($oAtributo->getUnidadeMedida()->getNome());
                    }

                    $oReferenciaStd->faixanormalminimo = '';
                    $oReferenciaStd->faixanormalmaximo = '';
                    $oReferenciaStd->faixaabsurdoinicio = '';
                    $oReferenciaStd->faixaasurdomaximo = '';
                    $oReferenciaStd->tipocalculo = '';
                    $oReferenciaStd->atributobase = '';
                    $oReferenciaStd->baseparacalculo = false;
                    $oReferenciaStd->fixo = '';
                    $oReferenciaStd->selecoes = array();
                    $oReferenciaStd->tipo = '';

                    switch ($oAtributo->getTipoReferencia()) {
                        case AtributoExame::REFERENCIA_NUMERICA:
                            if (empty($oReferenciasAtributo)) {
                                continue;
                            }

                            if ($oParam->lConferencia) {
                                $oAtributoStd->valorabsoluto = MascaraValorAtributoExame::mascarar(
                                    $iCasasDecimais,
                                    $oAtributoStd->valorabsoluto
                                );
                            }
                            $oReferenciaStd->faixanormalminimo = MascaraValorAtributoExame::mascarar(
                                $iCasasDecimais,
                                $oReferenciasAtributo->getValorMinimo()
                            );
                            $oAtributoStd->codigoreferencia = $oReferenciasAtributo->getCodigo();
                            $oReferenciaStd->faixanormalmaximo = MascaraValorAtributoExame::mascarar(
                                $iCasasDecimais,
                                $oReferenciasAtributo->getValorMaximo()
                            );
                            $oReferenciaStd->faixaabsurdoinicio = MascaraValorAtributoExame::mascarar(
                                $iCasasDecimais,
                                $oReferenciasAtributo->getValorAbsurdoMinimo()
                            );
                            $oReferenciaStd->faixaasurdomaximo = MascaraValorAtributoExame::mascarar(
                                $iCasasDecimais,
                                $oReferenciasAtributo->getValorAbsurdoMaximo()
                            );
                            $oReferenciaStd->tipocalculo = $oReferenciasAtributo->getTipoCalculo();
                            $oReferenciaStd->formula = $oReferenciasAtributo->getCalculavel();
                            $oReferenciaStd->casasdecimais = $oReferenciasAtributo->getCasasDecimaisApresentacao();

                            if ($oReferenciasAtributo->getAtributoBase() != '') {
                                $oReferenciaStd->atributobase = $oReferenciasAtributo->getAtributoBase()->getCodigo();
                            }

                            $oReferenciaStd->tipo = AtributoExame::REFERENCIA_NUMERICA;
                            $oAtributoStd->referencia = $oReferenciaStd;
                            break;

                        case AtributoExame::REFERENCIA_FIXA:
                            $oReferenciaStd->fixo = urlencode($oReferenciasAtributo->getTamanho());
                            $oReferenciaStd->tipo = AtributoExame::REFERENCIA_FIXA;
                            $oAtributoStd->referencia = $oReferenciaStd;
                            break;

                        case AtributoExame::REFERENCIA_SELECIONAVEL:
                            $oReferenciaStd->tipo = AtributoExame::REFERENCIA_SELECIONAVEL;

                            foreach ($oReferenciasAtributo->getReferenciasSelecionaveis() as $oReferencia) {
                                $oSelecao = new stdClass();
                                $oSelecao->codigo = $oReferencia->getCodigo();
                                $oSelecao->nome = urlencode($oReferencia->getDescricao());
                                $oReferenciaStd->selecoes[] = $oSelecao;
                            }

                            $oAtributoStd->referencia = $oReferenciaStd;

                            break;
                    }
                }

                $oRetorno->atributos[] = $oAtributoStd;
            }

            foreach ($oRetorno->atributos as $oAtributo) {
                if ($oAtributo->referencia == '') {
                    continue;
                }

                if ($oAtributo->referencia->tipo == AtributoExame::REFERENCIA_NUMERICA) {
                    foreach ($oRetorno->atributos as $oAtributoVerificar) {
                        if ($oAtributoVerificar->referencia == '') {
                            continue;
                        }

                        if ($oAtributoVerificar->referencia->atributobase != '' &&
                          $oAtributoVerificar->referencia->atributobase == $oAtributo->codigo) {

                            $oAtributo->referencia->baseparacalculo = true;
                            $oAtributo->valorpercentual = '100';
                            break;
                        }
                    }
                }
            }
            break;

        case 'salvarResultadoExame':
            $oRequisicao = new RequisicaoExame($oParam->iCodigoExame);
            $oExame = $oRequisicao->getExame();
            $oResultadoExame = $oRequisicao->getResultado();
            $inconsistencias = [];

            foreach ($oParam->aAtributos as $oAtributoLancado) {
                $oAtributo = AtributoExameRepository::getByCodigo($oAtributoLancado->iCodigoAtributo);
                $oResultadoLancado = $oResultadoExame->getValorDoAtributo($oAtributo);

                if (empty($oResultadoLancado)) {
                    $oResultadoLancado = new ResultadoExameAtributo();
                    $oResultadoLancado->setAtributo($oAtributo);
                    $oResultadoExame->adicionarResultadoParaAtributo($oResultadoLancado);
                }

                $nValorAbsoluto = pg_escape_string(utf8_decode(db_stdClass::db_stripTagsJsonSemEscape($oAtributoLancado->nValorAbsoluto)));
                $nValorPercentual = pg_escape_string(utf8_decode(db_stdClass::db_stripTagsJsonSemEscape($oAtributoLancado->nValorPercentual)));
                $oResultadoLancado->setValorPercentual($nValorPercentual);
                $oResultadoLancado->setValorAbsoluto($nValorAbsoluto);
                $oResultadoLancado->setTitulacao(db_stdClass::normalizeStringJsonEscapeString($oAtributoLancado->sTitulacao));


                if (!empty($oAtributoLancado->iCodigoReferencia)) {
                    $oResultadoLancado->setFaixaUtilizada(new AtributoValorReferenciaNumerico($oAtributoLancado->iCodigoReferencia));
                }

                if ($oAtributo->preenchimentoObrigatorio() == true) {
                    if ($nValorAbsoluto == "" && $nValorPercentual == "") {
                        $inconsistencias [] = $oAtributo->getNome();
                    }
                }
            }

            if (!empty($inconsistencias)) {
                $mensagem = "O preenchimento dos seguintes atributos são obrigatórios porém estão vazios: \n\n";
                foreach ($inconsistencias as $inconsistencia) {
                    $mensagem .= $inconsistencia . "\n";
                }
                throw new Exception($mensagem);
            }
            // Operation 0 - Plugin GeracaoArquivoBPA - Inicio comentario
            $oResultadoExame->salvar();
            $oRequisicao->setSituacao(RequisicaoExame::LANCADO);
            $oRequisicao->setObservacao(db_stdClass::normalizeStringJsonEscapeString($oParam->sObservacao));
            $oRequisicao->setMotivoNovaColeta(db_stdClass::normalizeStringJsonEscapeString($oParam->sMotivoNovaColeta));
            if($oParam->sMotivoNovaColeta){
                $oRequisicao->setSituacao(RequisicaoExame::NOVA_COLETA);
            }
            $oRequisicao->salvar();
            // Operation 1 - Plugin GeracaoArquivoBPA - Fim comentario e inicio do replace

            $oRetorno->message = _M(MSG_LAB4DIGITACAOEXAME . "resultados_salvos");

            break;

        case 'adicionarMedicamento':
            if (empty($oParam->iCodigoExame)) {
                throw new ParameterException(_M(MSG_LAB4DIGITACAOEXAME . "codigo_exame_nao_informado"));
            }

            if (!isset($oParam->aMedicamentos)) {
                throw new ParameterException(_M(MSG_LAB4DIGITACAOEXAME . "nao_foi_informado_medicamentos"));
            }

            $oItemRequisicao = new RequisicaoExame($oParam->iCodigoExame);
            foreach ($oParam->aMedicamentos as $oMedicamentoExame) {
                $oMedicamento = new MedicamentoLaboratorio($oMedicamentoExame->iCodigo);
                $oItemRequisicao->adicionarMedicamento($oMedicamento);
            }

            $oItemRequisicao->salvarMedicamento();

            $oRetorno->message = _M(MSG_LAB4DIGITACAOEXAME . "medicamentos_salvos");

            break;

        case 'removerMedicamento':
            if (empty($oParam->iCodigoExame)) {
                throw new ParameterException(_M(MSG_LAB4DIGITACAOEXAME . "codigo_exame_nao_informado"));
            }

            if (empty($oParam->iMedicamento)) {
                throw new ParameterException(_M(MSG_LAB4DIGITACAOEXAME . "nao_foi_informado_medicamentos"));
            }

            $oItemRequisicao = new RequisicaoExame($oParam->iCodigoExame);
            $oItemRequisicao->removerMedicamento(new MedicamentoLaboratorio($oParam->iMedicamento));

            $oRetorno->message = _M(MSG_LAB4DIGITACAOEXAME . "medicamento_removido");

            break;

        case 'buscarMedicamentos':
            if (empty($oParam->iCodigoExame)) {
                throw new ParameterException(_M(MSG_LAB4DIGITACAOEXAME . "codigo_exame_nao_informado"));
            }

            $oItemRequisicao = new RequisicaoExame($oParam->iCodigoExame);
            $aMedicamentos = array();

            foreach ($oItemRequisicao->getMedicamentos() as $oMedicamento) {
                $oDadosMedicamento = new stdClass();
                $oDadosMedicamento->iCodigo = $oMedicamento->getCodigo();
                $oDadosMedicamento->sNome = urlencode($oMedicamento->getNome());
                $oDadosMedicamento->sAbreviatura = urlencode($oMedicamento->getAbreviatura());

                $aMedicamentos[] = $oDadosMedicamento;
            }

            $oRetorno->aMedicamentos = $aMedicamentos;
            break;

        case 'importarResultados':
            $parametrosService = new ParametrosService(ParametrosEnum::JSON_CONFIGURACOES);
            $integraResultados = new IntegraResultados(
              $parametrosService->getParametros(),
              ParametrosEnum::RESULTADOS
            );

            $importacao = new ImportacaoResultado($integraResultados, ImportacaoInconsistencia::getInstance(),
              JSON::create());
            $requisicao = $importacao->executar();

            if ($requisicao === false) {
                $oRetorno->pastaLuckmanVazia = true;
                $oRetorno->temInconsistencias = false;
                break;
            }

            $oRetorno->temInconsistencias = $importacao->temInconsistencias();
            $oRetorno->pastaLuckmanVazia = false;
            $oRetorno->message = urlencode(_M(MSG_LAB4DIGITACAOEXAME . "resultados_importados"));

            if ($oRetorno->temInconsistencias === true) {
                $oRetorno->arquivo = $importacao->getArquivo();
                $oRetorno->requisicao = $requisicao;
                $oRetorno->message = urlencode(_M(MSG_LAB4DIGITACAOEXAME . "resultados_importados_inconsistencias"));
            }
            break;

        case 'calcularFormula':
            $requisicaoExame = new RequisicaoExame($oParam->requisicaoExame);
            $atributoExame = AtributoExameRepository::getByCodigo($oParam->atributoExame);

            try {
                $formula = new Formula($atributoExame->getFormula(), $requisicaoExame, $atributoExame);

                foreach ($oParam->atributosFormula as $atributoFormula) {
                    $formula->setVariavelValor($atributoFormula->sigla, $atributoFormula->valor);
                }

                $calculo = new Calculo($formula);
                $oRetorno->valor = $calculo->executar();
                if($oRetorno->valor == 'NAN' || $oRetorno->valor == 'INF' || $oRetorno->valor == ''){
                    $oRetorno->valor = "0";
                }
                $oRetorno->codigoAtributo = $oParam->atributoExame;
            } catch (Exception $erro) {
                $mensagem = $erro->getMessage();

                if ($erro instanceof ParserException) {
                    $mensagem = "Erro ao calcular o resultado do atributo. Verifique a fórmula do atributo";
                    $mensagem .= " '{$atributoExame->getNome()}'";
                }

                if ($erro instanceof UnknownVariableException) {
                    $mensagem = "Erro ao calcular o resultado do atributo. Verifique a fórmula do atributo";
                    $mensagem .= " '{$atributoExame->getNome()}'";
                }

                throw new Exception($mensagem);
            }

            break;
        case 'buscarProcedimento':
            $itemRequisicaoService = new ItemRequisicaoService(new ItemRequisicaoRepository(new cl_lab_requiitem()));
            $oRetorno->procedimento = $itemRequisicaoService->buscarProcedimentoExameItemRequisicao($oParam)[0]['la53_i_procedimento'];
            break;

        case 'importarResultadosLepac':
            $parametrosService = new ParametrosService(ParametrosEnum::JSON_CONFIGURACOES, ParametrosEnum::JSON_CONFIGURACOES_INFINITY);
            $integraResultados = new IntegraResultados(
                $parametrosService->getParametros(),
                ParametrosEnum::RESULTADOS_INIFINITY
            );

            $importacao = new ImportacaoResultado($integraResultados, ImportacaoInconsistencia::getInstance(),
                JSON::create());
            $requisicao = $importacao->executar(true);

            if ($requisicao === false) {
                $oRetorno->pastaInfinityVazia = true;
                $oRetorno->temInconsistencias = false;
                break;
            }

            $oRetorno->temInconsistencias = $importacao->temInconsistencias();
            $oRetorno->pastaInfinityVazia = false;

            if ($oRetorno->temInconsistencias === true) {
                $oRetorno->arquivo = $importacao->getArquivo();
                $oRetorno->requisicao = $requisicao;
                $oRetorno->message = urlencode('Importação concluída, porém inconsistências foram encontradas nos resultados. Deseja emitir o relatório?');
            }
            break;

        case 'verificaInterfaceDepartamentoLaboratorio':
            $codDepartamento = db_getsession('DB_coddepto');

            $campos = 'la02_interfaceado ';
            $where = "la03_i_departamento = {$codDepartamento}";

            $service = new LaboratorioService(new LaboratorioRepository(new cl_lab_laboratorio()));
            $labInterfaceado = $service->verificaLaboratorioInterfaceadoPorDepartamento($campos, $where);

            $oRetorno->departamentoPossuiInterface = $labInterfaceado;
            break;
    }

    db_fim_transacao(false);
} catch (Exception $eErro) {
    db_fim_transacao(true);

    $oRetorno->status = 2;
    $oRetorno->message = urlencode($eErro->getMessage());
}

$oRetorno->erro = $oRetorno->status == 2;
echo $oJson->encode($oRetorno);
