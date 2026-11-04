<?php
require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_utils.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('libs/JSON.php');
require_once modification('fpdf151/pdf.php');

use ECidade\RecursosHumanos\ESocial\Integracao\ESocial;
use ECidade\RecursosHumanos\ESocial\Repository\ESocialEnvioRepository;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\Error as FormatterError;
use ECidade\RecursosHumanos\ESocial\Integracao\Recurso;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Service\AfastamentoTemporarioService;
use ECidade\V3\Extension\Registry;

$oJson = new services_json();
$oParam = JSON::create()->parse(str_replace('\\', "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->iStatus = 1;
$oRetorno->sMessage = '';

try {
    db_inicio_transacao();
    ini_set('memory_limit', '-1');

    switch ($oParam->exec) {
        case "consultaDados":
            $filtros = $oParam->aFiltros;

            $esocialEnvioRepository = new ESocialEnvioRepository();

            $inscricaoEmpregador = $filtros->inscricaoEmpregador;
            $idEvento = $filtros->idEvento;
            $dataInicio = $filtros->dataInicio;
            $dataFinal = $filtros->dataFinal;
            $statusErro = ($filtros->statusErro == "true" ? true : false);
            $statusRecibo = ($filtros->statusRecibo == "true" ? true : false);
            $statusOcorrencia = ($filtros->statusOcorrencia == "true" ? true : false);
            $statusAdvertencia = ($filtros->statusAdvertencia == "true" ? true : false);
            $tipoEvento = $filtros->tipoEvento;

            $esocialSituacoes = $esocialEnvioRepository->buscaSituacoes(
                $inscricaoEmpregador,
                $idEvento,
                $dataInicio,
                $dataFinal,
                $statusErro,
                $statusRecibo,
                $statusOcorrencia,
                $tipoEvento,
                $statusAdvertencia
            );

            $dados = array();
            foreach ($esocialSituacoes as $situacao) {
                $dados[] = JSON::create()->parse($situacao->serialize());
            }

            $oRetorno->dados = $dados;

            break;
        case "imprimirRelatorioConsulta":
            $filtros = $oParam->filtros;

            $esocialEnvioRepository = new ESocialEnvioRepository();

            $inscricaoEmpregador = $filtros->inscricaoEmpregador;
            $idEvento = $filtros->idEvento;
            $dataInicio = $filtros->dataInicio;
            $dataFinal = $filtros->dataFinal;
            $statusErro = $filtros->statusErro == "true" ? true : false;
            $statusRecibo = $filtros->statusRecibo == "true" ? true : false;
            $statusOcorrencia = $filtros->statusOcorrencia == "true" ? true : false;
            $statusAdvertencia = $filtros->statusAdvertencia == "true" ? true : false;
            $tipoEvento = $filtros->tipoEvento;

            $dados = $esocialEnvioRepository->buscaSituacoes(
                $inscricaoEmpregador,
                $idEvento,
                $dataInicio,
                $dataFinal,
                $statusErro,
                $statusRecibo,
                $statusOcorrencia,
                $tipoEvento,
                $statusAdvertencia,
                true
            );
            $caminhoPdf = "tmp/relatorio_esocial.pdf";
            if ($oParam->tipo == "csv") {
                $caminhoPdf = "tmp/consulta_esocial.csv";
            }

            switch ($oParam->tipo) {
                case 'csv':
                    $indices = [
                        'evento' => 0,
                        'referencia' => 1,
                        'status' => 2,
                        'recibo' => 3,
                        'ocorrencia' => 4
                    ];

                    $retornos = [];

                    $titulos = [];
                    $titulos[$indices['evento']] = 'Evento';
                    $titulos[$indices['referencia']] = 'Referência';
                    $titulos[$indices['status']] = 'Erros/Status';
                    $titulos[$indices['recibo']] = 'Recibos';
                    $titulos[$indices['ocorrencia']] = 'Ocorrências';
                    $retornos[] = $titulos;

                    foreach ($dados as $dado) {
                        $retorno = [];
                        $retorno[$indices['evento']] = $dado->getEvento();
                        $retorno[$indices['referencia']] = $dado->getDescricao();
                        $retorno[$indices['status']] = $dado->getSituacao();

                        $recibos = $dado->getRecibos();
                        $textoRecibo = "";
                        if (!empty($recibos)) {
                            foreach ($recibos as $recibo) {
                                $textoRecibo .=  $recibo->numero . ", ";
                            }
                            $textoRecibo = substr($textoRecibo, 0, -2);
                        } else {
                            $textoRecibo = "Nenhum recibo encontrado.";
                        }
                        $retorno[$indices['recibo']] = $textoRecibo;

                        $ocorrencias = $dado->getOcorrencias();
                        $textoOcorrencia = "";
                        if (!empty($ocorrencias)) {
                            foreach ($ocorrencias as $ocorrencia) {
                                $textoOcorrencia .= "Código: " . $ocorrencia->codigo . ", ";
                                $textoOcorrencia .= "Descrição: " . $ocorrencia->descricao;
                                if (!empty($ocorrencia->localizacao)) {
                                    $textoOcorrencia .= ",Localização: " . $ocorrencia->localizacao . ", ";
                                }
                            }
                            $textoOcorrencia = substr($textoOcorrencia, 0, -2);
                        } else {
                            $textoOcorrencia = "Nenhuma ocorrência encontrada.";
                        }
                        $retorno[$indices['ocorrencia']] = $textoOcorrencia;

                        $retornos[] = $retorno;
                    }
                    $arquivo = fopen($caminhoPdf, 'w');
                    foreach ($retornos as $retorno) {
                        fputcsv($arquivo, $retorno,  ";");
                    }

                    fclose($arquivo);
                    break;
                default:
                    $head1 = "Data início: " . substr($filtros->dataInicio, 0, 10);
                    $head1 .= "\nData final: " . substr($filtros->dataFinal, 0, 10);
                    $head1 .= "\nFiltrar envios com erro: " . ($filtros->statusErro == "true" ? "Sim" : "Não");
                    $head1 .= "\nFiltrar envios com sucesso: " . ($filtros->statusRecibo == "true" ? "Sim" : "Não");
                    $head1 .= "\nFiltrar envios com ocorrências: " . ($filtros->statusOcorrencia == "true" ? "Sim" : "Não");
                    $head1 .= "\nQuantidade de registros: " . (count($dados));

                    $pdf = new PDF();
                    $pdf->Open();
                    $pdf->AliasNbPages();
                    $pdf->addpage();
                    $pdf->setfillcolor(235);
                    $pdf->SetAutoPageBreak(true, 15);

                    foreach ($dados as $dado) {

                        $pdf->setfont('arial', 'B', 8);
                        $pdf->cell(22, 5, "Evento", 1, 0, "C", 1);
                        $pdf->cell(170, 5, "Referência", 1, 1, "C", 1);

                        $pdf->setfont('arial', '', 8);
                        $pdf->cell(22, 5, $dado->getEvento(), 1, 0, "C");

                        $pdf->cell(170, 5, $dado->getDescricao(), 1, 1, "L");

                        $pdf->setfont('arial', 'B', 8);
                        $pdf->cell(192, 5, "Erros / Status", 1, 1, "L");
                        $pdf->setfont('arial', '', 8);
                        $pdf->MultiCell(192, 4, str_replace(array("<br />", "</b>", "<b>"), array("\n", "", ""), $dado->getSituacao()), 1);

                        $ocorrencias = $dado->getOcorrencias();
                        if (!empty($ocorrencias)) {
                            $pdf->setfont('arial', 'B', 8);
                            $pdf->cell(192, 5, "Ocorrências", 1, 1, "L");
                            $pdf->setfont('arial', '', 8);

                            $pdf->cell(22, 5, "Código", 1, 0, "L");
                            $pdf->cell(120, 5, "Descrição", 1, 0, "L");
                            $pdf->cell(50, 5, "Localização", 1, 1, "L");

                            foreach ($ocorrencias as $ocorrencia) {

                                $linhas = $pdf->NbLines(120, $ocorrencia->descricao);
                                $linhasLocalizacao = $pdf->NbLines(50, $ocorrencia->localizacao);

                                if ($linhas < $linhasLocalizacao) {
                                    $linhas = $linhasLocalizacao;
                                }

                                $alturaLinha = ($linhas * 5) ;
                                if (($pdf->h - 20) < ($pdf->getY() + $alturaLinha)  ) {
                                    $pdf->AddPage();
                                }

                                $y = $pdf->GetY();
                                $x = $pdf->GetX();

                                $pdf->MultiCell(22, 5, $ocorrencia->codigo, 0, 'C');
                                $x += 22;
                                $pdf->SetXY($x, $y);
                                $pdf->MultiCell(120, 5, $ocorrencia->descricao, 0);

                                $pdf->SetXY($x + 120, $y);

                                $localizacao = "";

                                if (!empty($ocorrencia->localizacao)) {
                                    $localizacao = $ocorrencia->localizacao;
                                }
                                $pdf->MultiCell(50, 5, $localizacao, 0);

                                $pdf->SetY($y + $alturaLinha);

                                $pdf->Line(10, $y, 202, $y);
                                $pdf->Line(10, $pdf->GetY(), 202, $pdf->GetY());
                                $pdf->Line(10, $y, 10, $pdf->GetY());
                                $pdf->Line(32, $y, 32, $pdf->GetY());
                                $pdf->Line(152, $y, 152, $pdf->GetY());
                                $pdf->Line(202, $y, 202, $pdf->GetY());
                            }
                        }

                        $recibos = $dado->getRecibos();
                        if (!empty($recibos)) {
                            $pdf->setfont('arial', 'B', 8);
                            $pdf->cell(192, 5, "Recibos", 1, 1, "L");
                            $pdf->setfont('arial', '', 8);
                            foreach ($recibos as $recibo) {
                                $pdf->MultiCell(192, 4, $recibo->numero, 1);
                            }
                        }
                    }


                    $pdf->Output($caminhoPdf, false, true);
                break;
            }
            if (!file_exists($caminhoPdf)) {
                throw new Exception("Erro ao gerar o relatório.\nContate o suporte.");
            }

            $oRetorno->nomeArquivo = $caminhoPdf;

            break;

        case "consultaRecibos":

            $oESocial = new ESocial(Registry::get('app.config'), Recurso::CONSULTA_RECIBO);

            if (!empty($oParam->aFiltros->inscricaoEmpregador)) {
                $oCgm = CgmFactory::getInstanceByCgm($oParam->aFiltros->inscricaoEmpregador);
                $oParam->aFiltros->inscricaoEmpregador = $oCgm->getCnpj();
            }

            if ($oParam->aFiltros->tipoEvento == Tipo::ESOCIAL) {
                $oParam->aFiltros->idEvento = 'S-' . $oParam->aFiltros->idEvento;
            } 
            
            if (!empty($oParam->aFiltros->idEvento)) {
                if ($oParam->aFiltros->idEvento == "1000") {
                    unset($oParam->aFiltros->idReferencia);
                } else if(isset($oParam->aFiltros->idReferencia)) {
                    $aReferencias = explode(" - ", $oParam->aFiltros->idReferencia);
                    $oParam->aFiltros->idReferencia = $aReferencias[0];
                }
            }

            $oESocial->setDados($oParam->aFiltros);
            $dados = $oESocial->request("GET");
            $oRetorno->recibos = array();
            foreach ($dados as $dado) {
                foreach ($dado->recibo as $recibo) {
                    $item = new stdClass();
                    $item->protocolo = $recibo->protocolo;
                    $item->numero = '<span title="Válido" style="color:#008000"><strong>' . $recibo->numero . "</strong></span>";
                    if (!empty($recibo->excluido)) {
                        $item->numero = '<span title="Excluído" style="color:#ff0000;"><strong>' . $recibo->numero . '</strong></span>';
                    } else {
                        if (empty($recibo->ultimoRecibo)) {
                            $item->numero = '<span title="Alterado" style="color:#ffa500;"><strong>' . $recibo->numero . '</strong></span>';
                        }
                    }

                    $item->evento = $dado->tipo;

                    $oData = new DateTime($recibo->updated_at);
                    $item->data = $oData->format("d/m/Y H:i:s");
                    $oRetorno->recibos[] = $item;
                }
            }

            if (sizeof($oRetorno->recibos) <= 0) {
                throw new \Exception("Nenhum recibo encontrado para este evento.");
            }

            break;

        case "consultaOcorrencias":
            $oESocial = new ESocial(Registry::get('app.config'), "/evento/ocorrencia");
            $oESocial->setDados($oParam->aFiltros);

            $oParam->aFiltros->inscricaoEmpregador = CgmFactory::getInstanceByCgm($oParam->aFiltros->inscricaoEmpregador)->getCnpj();
            $dados = $oESocial->request("GET");
            $bOcorrencia = false;
            $oRetorno->idStatusEnvio = $oParam->idStatusEnvio;

            foreach ($dados as $evento) {

                if (empty($evento->ocorrencias)) {
                    continue;
                }

                $bOcorrencia = true;

                foreach ($evento->ocorrencias as $dado) {

                    $localizacao = '';
                    if ($dado->localizacao) {
                        if ($oParam->aFiltros->idEvento == 'R-2055') {
                           $localizacao = $dado->localizacao;
                        } else {
                            $errorFormatter = new FormatterError($oParam->aFiltros->idEvento);
                            $labels = $errorFormatter->extractLabels($dado->localizacao);
                            $localizacao = $errorFormatter->formatLabels($labels, "%s");
                        }
                    }
                    $dataOcorrencia = '';
                    if (!empty($dado->updated_at)) {
                        $oData = new DateTime($dado->updated_at);
                        $dataOcorrencia = $oData->format("d/m/Y");
                    }
                    $item = (object)array(
                        'codigo' => $dado->codigo,
                        'evento' => $dado->tipo,
                        'descricao' => $dado->descricao,
                        'data' => $dataOcorrencia,
                        'localizacao' => $localizacao,
                    );

                    $oRetorno->ocorrencias[] = $item;
                }
            }

            if (!$bOcorrencia) {
                $oRetorno->sMessage = "Nenhuma ocorrência encontrada para o evento selecionado";
                $oRetorno->iStatus = 2;
            } else {
                $daoeSocialEnvioStatus = new cl_esocialenviostatus();
                $daoeSocialEnvioStatus->rh214_sequencial = $oParam->idStatusEnvio;
                $daoeSocialEnvioStatus->rh214_situacao = 'false';
                $daoeSocialEnvioStatus->alterar($oParam->idStatusEnvio);

                if ($daoeSocialEnvioStatus->erro_status == "0") {
                    throw new \Exception("Erro ao atualizar a situação do envio. \n{$daoeSocialEnvioStatus->erro_msg}");
                }
            }

            break;

        case "consultaStatus":
            // SQL para veirifcar se existe status na base do e-cidade
            $sSql = " SELECT * FROM esocialenvio LEFT JOIN esocialenviostatus on esocialenvio.rh213_sequencial"
                . " = esocialenviostatus.rh214_esocialenvio ";
            $sSql .= " WHERE ";
            $sSql .= " rh213_empregador = {$oParam->aFiltros->inscricaoEmpregador} ";
            $sSql .= " AND rh213_evento = '{$oParam->aFiltros->idEvento}' ";
            $sSql .= " AND rh213_responsavelpreenchimento = '{$oParam->aFiltros->idReferencia}'";
            $rsSqlEsocialEnvio = pg_query($sSql);
            if (!$rsSqlEsocialEnvio) {
                throw new \Exception("Nenhum registro foi encontrado com os filtros informados.");
            }
            // Array de retorno
            $oRetorno->dados = array();
            // Se existir informacao
            $oDado =  new stdClass();
            if (pg_num_rows($rsSqlEsocialEnvio) > 0) {
                $aDados = db_utils::makeCollectionFromRecord(
                    $rsSqlEsocialEnvio,
                    function ($dado) use ($oRetorno, $oDado) {
                    $item = new stdClass();
                    // Se existir informacao na tabela esocialenviostatus seta a descricao
                    if (!empty($dado->rh214_sequencial)) {
                        $oData = new DateTime($dado->rh214_data);
                        $item->data = $oData->format("d/m/Y H:i:s");
                        $item->situacao = $dado->rh214_descricao;
                    } else {
                        // Caso contrario, ainda nao foi enviado, nesse caso a situação é setada manualmente
                        $oData = new DateTime($dado->rh213_data);
                        $item->data = $oData->format("d/m/Y H:i:s");
                        $item->situacao = "Aguardando envio na rotina eSocial > "
                            . "Procedimentos > Envio de eventos para o eSocial.";
                    }
                    // $dado->rh214_situacao == 'f'-> Quando o evento ainda não foi enviado para a api
                    if (empty($dado->rh214_situacao) || $dado->rh214_situacao == 'f') {
                        $oRetorno->dados[] = $item;
                        $oDado->dado = $dado;
                    }
                });
            }
            // Se o array estiver vazio ou estiver com o status de enviado para a API
            if ((isset($oRetorno->dados) && sizeof($oRetorno->dados) == 0)
                || $oRetorno->dados[0]->situacao == 'Enviado para a API'
            ) {
                $oESocial = new ESocial(Registry::get('app.config'), "/evento/status");
                if (!empty($oParam->aFiltros->inscricaoEmpregador)) {
                    $oCgm = CgmFactory::getInstanceByCgm($oParam->aFiltros->inscricaoEmpregador);
                    $oParam->aFiltros->inscricaoEmpregador = $oCgm->getCnpj();
                }

                $oESocial->setDados($oParam->aFiltros);
                $dados = $oESocial->request("GET");

                // Caso tenha retorno da api e no ecidade esteja como enviado para a api, atualiza no ecidade o status
                if (sizeof($dados) > 0 && sizeof($oRetorno->dados) > 0 && $oRetorno->dados[0]->situacao == 'Enviado para a API') {
                    $daoeSocialEnvioStatus = new cl_esocialenviostatus();
                    $daoeSocialEnvioStatus->rh214_sequencial = $oDado->dado->rh214_sequencial;
                    $daoeSocialEnvioStatus->rh214_situacao = 'true';
                    $daoeSocialEnvioStatus->alterar($oDado->dado->rh214_sequencial);

                    if ($daoeSocialEnvioStatus->erro_status == 0) {
                        throw new \Exception("Não foi possível atualizar o status do evento.");
                    }
                    $oRetorno->dados = [];
                }

                foreach ($dados as $dado) {
                    $item = new stdClass();
                    $item->codigo = $dado->codigo;
                    $item->evento = $dado->tipo;

                    if ($dado->tipo == "S-1000") {
                        $item->identificacao = $dado->referencia . " - " . $dado->empregador->razao_social;
                    } else {
                        $item->identificacao = $dado->referencia;
                    }

                    $oData = new DateTime($dado->updated_at);
                    $item->data = $oData->format("d/m/Y H:i:s");
                    $item->situacao = $dado->status;

                    $oRetorno->dados[] = $item;
                }
            }
            break;
        case 'atualizaRegistro':

            if (empty($oParam->status_id)) {
                throw new \ParameterException("Status do evento não informado.");
            }

            $oDaoeSocialEnvioStatus = new cl_esocialenviostatus();
            $where = "rh214_sequencial = {$oParam->status_id} and rh214_situacao is false";
            $sql = $oDaoeSocialEnvioStatus->sql_query(null, "*", null, $where);
            $rs = db_query($sql);
            if (!$rs || pg_num_rows($rs) == 0) {
                throw new \DBException("Erro ao buscar os dados do registro.");
            }

            $dados = db_utils::fieldsMemory($rs, 0);
            $dados->dadosJson = json_decode($dados->rh213_dados);

            switch ($oParam->evento) {
                case Tipo::S2230:
                    if (empty($dados->dadosJson->ideVinculo)) {
                        throw new \BusinessException("Dados do vínculo não encontrados.");
                    }

                    if (empty($dados->dadosJson->ideVinculo->matricula)) {
                        throw new \BusinessException("Matrícula não encontrada.");
                    }

                    $matricula = $dados->dadosJson->ideVinculo->matricula;
                    $codigoAssentamento = $dados->rh213_responsavelpreenchimento;
                    $afastamento = new AfastamentoTemporarioService($matricula, $codigoAssentamento);
                    $afastamento->preencherFormulario();

                    $daoeSocialEnvioStatus = new cl_esocialenviostatus();
                    $daoeSocialEnvioStatus->rh214_sequencial = $oParam->status_id;
                    $daoeSocialEnvioStatus->rh214_situacao = 'true';
                    $daoeSocialEnvioStatus->alterar($oParam->status_id);

                    if ($daoeSocialEnvioStatus->erro_status == "0") {
                        throw new \Exception("Erro ao atualizar a situaçao do envio. \n{$daoeSocialEnvioStatus->erro_msg}");
                    }

                    break;
                default:
                    $nomeEvento = Tipo::getTitulos(Tipo::getByLayout($oParam->evento));
                    throw new \BusinessException("Não é possível atualizar o evento {$nomeEvento}");
                    break;
            }

            $oRetorno->idStatusEnvio = $oParam->status_id;
            $oRetorno->sMessage = "Registro atualizado com sucesso.";
            break;
    }

    db_fim_transacao(false);
} catch (Exception $eErro) {
    db_fim_transacao(true);
    $oRetorno->iStatus = 2;
    $oRetorno->sMessage = $eErro->getMessage();
}

$oRetorno->erro = $oRetorno->iStatus == 2;
echo JSON::create()->stringify($oRetorno);
