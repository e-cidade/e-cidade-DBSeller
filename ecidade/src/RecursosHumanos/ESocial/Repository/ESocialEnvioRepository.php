<?php

namespace ECidade\RecursosHumanos\ESocial\Repository;

use BusinessException;
use CgmFactory;
use DateTime;
use DBException;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocialEnvio;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocialEnvioStatus;
use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\Error as FormatterError;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\V3\Extension\Registry;
use Exception;
use JSON;
use ParameterException;

/**
 * Class ESocialEnvio
 * @package ECidade\RecursosHumanos\ESocial\Integracao
 */
class ESocialEnvioRepository
{

    /**
     * @var bool
     */
    private $exportacaoArquivo = false;

    /**
     * @var array
     */
    private $scopes = array();

    /**
     * Tamanho maximo de consulta na api por particao
     * @var integer
     */
    private $comprimentoParticao = 200;

    /**
     * @param $id
     * @param array $columns
     * @return bool|ESocialEnvio
     */
    public static function find($id, $columns = array('*'))
    {
        $dao = new \cl_esocialenvio();
        $sql = $dao->sql_query_status($id, implode(', ', $columns));
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Não foi possível buscar o envio do esocial.\nContate o suporte.");
        }

        if (pg_num_rows($rs) === 0) {
            return false;
        }

        $resultado = pg_fetch_array($rs);
        return ESocialEnvio::fromState($resultado);
    }


    /**
     * @param $id
     * @param array $columns
     * @return bool|ESocialEnvio
     */
    public static function getDados($id, $columns = array('*'))
    {
        $dao = new \cl_esocialenvio();
        $sql = $dao->sql_query_status($id, implode(', ', $columns));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar o envio do esocial.\nContate o suporte.");
        }

        if (pg_num_rows($rs) === 0) {
            return false;
        }

        $resultado = pg_fetch_array($rs);
        db_fim_transacao();
        return ESocialEnvio::fromState($resultado);
    }

    /**
     * @param $inscricaoEmpregador
     * @param $idEvento
     * @param $dataInicio
     * @param $dataFinal
     * @param bool $statusErro
     * @param bool $statusRecibo
     * @param bool $statusOcorrencia
     * @return ESocialEnvio[]
     * @throws BusinessException
     * @throws DBException
     * @throws ParameterException
     */
    public function buscaSituacoes(
        $inscricaoEmpregador,
        $idEvento,
        $dataInicio,
        $dataFinal,
        $statusErro = false,
        $statusRecibo = false,
        $statusOcorrencia = false,
        $tipoEvento = Tipo::ESOCIAL,
        $statusAdvertencia = false,
        $exportacaoArquivo = false
    ) {
        $this->exportacaoArquivo = $exportacaoArquivo;
        $consultaSituacao = new \stdClass();
        // Adicionado array de referencias e idEventos, pois iremos particionar a consulta
        $consultaSituacao->referencias = [];
        $consultaSituacao->idEventos = [];

        $whereArray = array();
        // adicionada variavel de campos para ecomonizar recurso de memoria em grande quantidade de registros
        $campos = "rh213_sequencial, rh213_evento, rh213_responsavelpreenchimento, rh213_data, rh213_situacao,"
            . "rh214_sequencial, rh214_descricao, rh214_situacao, rh214_data";

        if (!empty($inscricaoEmpregador)) {
            $whereArray[] = "rh213_empregador = {$inscricaoEmpregador}";
            $oCgm = CgmFactory::getInstanceByCgm($inscricaoEmpregador);
            $consultaSituacao->inscricaoEmpregador = $oCgm->getCnpj();
        }

        if (!empty($idEvento)) {
            $whereArray[] = "rh213_evento = '{$idEvento}'";
        }

        if (empty($idEvento)) {
            if ($tipoEvento == Tipo::EFD_REINF) {
                $whereArray[] = "rh213_evento ilike 'R%'";
            }
            if ($tipoEvento == Tipo::ESOCIAL) {
                $str = "rh213_evento NOT IN (SELECT rh213_evento FROM esocialenvio WHERE rh213_evento ILIKE 'R%')";
                $whereArray[] = $str;
            }
        }

        if (!empty($dataInicio)) {
            $whereArray[] = "rh213_data >= '{$dataInicio}'";
            $dataInicio = new \DBDate(substr($dataInicio, 0, 10));
            $consultaSituacao->dataInicio = ($dataInicio->convertTo("Y-m-d")). ' 00:00';
        }

        if (!empty($dataFinal)) {
            $whereArray[] = "rh213_data <= '{$dataFinal}'";
            $dataFinal = new \DBDate(substr($dataFinal, 0, 10));
            $consultaSituacao->dataFinal = ($dataFinal->convertTo("Y-m-d")). ' 23:59';
        }

        $consultaSituacao->incluiErrosLayout = false;
        if ($statusErro) {
            $consultaSituacao->incluiErrosLayout = true;

            if (!$statusRecibo && !$statusOcorrencia) {
                $whereArray[] = "rh213_situacao = 1";
            }
        }
        $sqlEsocialEnvio = "
            SELECT
                DISTINCT {$campos}
            FROM
                 esocialenvio
            LEFT JOIN esocialenviostatus on
                esocialenvio.rh213_sequencial = esocialenviostatus.rh214_esocialenvio ";

        if (count($whereArray) > 0) {
            $where = implode(" AND ", $whereArray);
            $sqlEsocialEnvio .= "WHERE {$where}";
        }
        $sqlEsocialEnvio .= " ORDER BY rh213_data DESC, rh213_evento";
        $rsEsocialEnvio = db_query($sqlEsocialEnvio);

        $totalRegistros = pg_num_rows($rsEsocialEnvio);

        if (!$rsEsocialEnvio) {
            throw new \Exception("Nenhum registro foi encontrado com os filtros informados.");
        }
        $esocialEnvios = [];
        for ($i = 0; $i < $totalRegistros; $i++) {
            $esocialEnvios[] = pg_fetch_object($rsEsocialEnvio, $i);
        }
        unset($rsEsocialEnvio);


        if (empty($esocialEnvios)) {
            throw new \Exception("Nenhum registro foi encontrado com os filtros informados.");
        }


        // array de armazenagem de dados
        $dados = array();
        // variavel de controle de quantidade dentro da particao
        $contador = 0;
        // numero da particao
        $particao = 0;
        foreach ($esocialEnvios as $esocialEnvio) {
            $esocialSituacao = new ESocialEnvio();
            $esocialSituacao->setEvento($esocialEnvio->rh213_evento);
            $esocialSituacao->setResponsavelPreenchimento($esocialEnvio->rh213_responsavelpreenchimento);
            $esocialSituacao->setEmpregador($inscricaoEmpregador);
            $esocialSituacao->setData(new \DateTime($esocialEnvio->rh213_data));
            $esocialSituacao->setSituacaoSalva($esocialEnvio->rh213_situacao);


            if (!empty($esocialEnvio->rh214_sequencial)) {
                $eventoStatus = new ESocialEnvioStatus();
                $eventoStatus->setSequencial($esocialEnvio->rh214_sequencial);
                $eventoStatus->setData($esocialEnvio->rh214_data);
                $eventoStatus->setDescricao($esocialEnvio->rh214_descricao);
                $eventoStatus->setSituacao($esocialEnvio->rh214_situacao);
                $eventoStatus->setEsocialenvio($esocialEnvio->rh213_sequencial);
    
                $esocialSituacao->setEnvioStatus($eventoStatus);
                $esocialSituacao->setCodigo($esocialEnvio->rh214_sequencial);
                $esocialSituacao->setCodigoEnvio($esocialEnvio->rh213_sequencial);
                $esocialSituacao->setSituacao($esocialEnvio->rh214_descricao);
                $esocialSituacao->setPermiteAtualizar(false);
                $esocialSituacao->setProcessadoSucesso(($esocialEnvio->rh214_situacao == "f" ? false : true));
                $esocialSituacao->setAguardandoProcessamento(false);

                if ($esocialEnvio->rh213_evento == '2230' && $esocialEnvio->rh214_situacao == 'f') {
                    $esocialSituacao->setPermiteAtualizar(true);
                }
            } else {
                $esocialSituacao->setCodigo($esocialEnvio->rh213_sequencial);
                $esocialSituacao->setCodigoEnvio($esocialEnvio->rh213_sequencial);

                $msg = ($tipoEvento == Tipo::ESOCIAL)
                    ? "Aguardando envio na teste rotina eSocial > Procedimentos > Envio de eventos para o eSocial."
                    : "Aguardando envio na rotina EFD-Reinf > Procedimentos > Envio de eventos para o EFD-Reinf.";

                $esocialSituacao->setSituacao($msg);
                $esocialSituacao->setPermiteAtualizar(false);
                $esocialSituacao->setProcessadoSucesso(true);
                $esocialSituacao->setAguardandoProcessamento(true);
            }

            if ($contador == $this->comprimentoParticao) {
                $particao += 1;
                $contador = 0;
            }

            $consultaSituacao->referencias[$particao][$esocialEnvio->rh213_responsavelpreenchimento] = $esocialEnvio
                ->rh213_responsavelpreenchimento;

            $consultaSituacao->idEventos[$particao][$esocialEnvio->rh213_evento] = ($tipoEvento == Tipo::ESOCIAL)
                ? "S-{$esocialEnvio->rh213_evento}"
                : "{$esocialEnvio->rh213_evento}";

            $contador += 1;
            $dados[$esocialSituacao->getEvento()][$esocialSituacao->getResponsavelPreenchimento()] = $esocialSituacao;
        }
        unset($esocialEnvios);
        $dadosFiltrados = array();

        $situacoes = [];
        $consultaSituacao->statusRecibo = $statusRecibo;
        $consultaSituacao->statusOcorrencia = $statusOcorrencia;
        $consultaSituacao->statusAdvertencia = $statusAdvertencia;
        $particao = count($consultaSituacao->referencias);

        for ($i = 0; $i < $particao; $i++) {
            $consulta = clone $consultaSituacao;
            $consulta->referencias = $consultaSituacao->referencias[$i];
            $consulta->referencias = array_values($consulta->referencias);
            $consulta->idEventos = $consultaSituacao->idEventos[$i];
            $consulta->idEventos = array_values($consulta->idEventos);
            $situacoes = null;
            try {
                $oESocial = new ESocial(Registry::get('app.config'), "/evento/situacao_eventos");
                $oESocial->setDados($consulta);
                $situacoes = $oESocial->request("GET");
            } catch (Exception $e) {
                $situacoes = null;
            }
            if (!empty($situacoes)) {
                foreach ($situacoes as $situacao) {
                    $layout = str_replace("S-", "", $situacao->layout);
                    if (empty($dados[$layout][$situacao->referencia])) {
                        continue;
                    }
                    $dado = $dados[$layout][$situacao->referencia];
                    $dado->situacaoApi = $situacao->status;
                    if ($layout == $dado->getEvento()
                        && $situacao->referencia == $dado->getResponsavelPreenchimento()
                        || ($consultaSituacao->incluiErrosLayout == true && !$dado->isProcessadoSucesso())
                    ) {
                        if ($statusRecibo && empty($situacao->recibos)) {
                            continue;
                        }
                        if ($statusOcorrencia && empty($situacao->ocorrencias)) {
                            continue;
                        }
                        if ($layout == $dado->getEvento()
                            && $situacao->referencia == $dado->getResponsavelPreenchimento()
                        ) {
                            if (!empty($situacao->ocorrencias)) {
                                $ocorrencias = JSON::create()->parse($situacao->ocorrencias);
                                foreach ($ocorrencias as $ocorrencia) {
                                    if ($ocorrencia->localizacao) {
                                        if ($dado->getEvento() != 'R-2055') {
                                            $errorFormatter = new FormatterError($dado->getEvento());
                                            $labels = $errorFormatter->extractLabels($ocorrencia->localizacao);
                                            $ocorrencia->localizacao = str_replace(
                                                array("<br />", "</b>", "<b>"),
                                                array("\n", "", ""),
                                                $errorFormatter->formatLabels($labels, "%s")
                                            );
                                        }
                                    }
                                }
                                $dado->setOcorrencias($ocorrencias);
                            }
                            $dado->setRecibos(JSON::create()->parse($situacao->recibos));
                        }

                        // Verifica a situação da dataRecibo com a EnvioStatus para que informe se já estar processado.
                        if (isset($situacao->data_recibo_recente)
                            && !empty($situacao->data_recibo_recente)
                            && $dado->getSituacaoSalva() == 1
                            ) {
                            $dataRecibo = new DateTime($situacao->data_recibo_recente);
                            if (!empty($dado->getEnvioStatus())) {
                                if ($dataRecibo > $dado->getEnvioStatus()->getData()) {
                                    $eventoStatus = $dado->getEnvioStatus();
                                    $eventoStatus->updateEventStatus(
                                        $dado->getCodigoEnvio(),
                                        true,
                                        'Enviado para a API'
                                    );
                                    $this->atualizarEvento($dado->getCodigoEnvio(), 2);
                                    $dado->setProcessadoSucesso(true);
                                }
                            }
                        }

                        if ($dado->isProcessadoSucesso()) {
                            $dado->setSituacao(ESocialEnvioStatus::getStatusDescricao($situacao->status));
                        }
                        $this->ajustaDados($dado);
                        $dadosFiltrados[] = $dado;
                        unset($dados[$layout][$situacao->referencia]);
                    }
                }
            }
        }
        // caso nao tenha sido enviado para api
        if ($statusErro || (!$statusRecibo && !$statusOcorrencia && !$statusAdvertencia)) {
            foreach ($dados as $layout) {
                foreach ($layout as $dado) {
                    $this->ajustaDados($dado);
                    $dadosFiltrados[] = $dado;
                    unset($dado);
                }
                unset($layout);
            }
        }

        if (sizeof($dadosFiltrados) == 0) {
            throw new \Exception("Nenhum registro foi encontrado com os filtros informados.");
        }

        return $dadosFiltrados;
    }

    public function scopeSequencial($sequencial, $operator = '=')
    {
        $this->scopes['sequencial'] = "rh213_sequencial {$operator} {$sequencial}";
        return $this;
    }

    public function scopeEvento($evento, $operator = '=')
    {
        $this->scopes['evento'] = "rh213_evento {$operator} '{$evento}'";
        return $this;
    }


    public function scopeEmpregador($empregador, $operator = '=')
    {
        $this->scopes['empregador'] = "rh213_empregador {$operator} {$empregador}";
        return $this;
    }


    public function scopeResponsavelPreenchimento($responsavelPreenchimento, $operator = '=')
    {
        $this->scopes['responsavelPreenchimento'] = "rh213_responsavelpreenchimento {$operator}"
            . " '{$responsavelPreenchimento}'";
        return $this;
    }

    public function scopeSituacao($situacao, $operator = '=')
    {
        $this->scopes['situacao'] = "rh213_situacao {$operator} {$situacao}";
        return $this;
    }


    public function scopeData($data, $operator = '=')
    {
        $this->scopes["data{$operator}"] = "rh213_data {$operator} '{$data}'";
        return $this;
    }

    public function get()
    {
        $dao = new \cl_esocialenvio();
        $sql = $dao->sql_query_status(null, '*', null, implode(' AND ', $this->scopes));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível os envios do esocial.\nContate o suporte.");
        }

        $esocialEnvios = array();

        if (pg_num_rows($rs) === 0) {
            return $esocialEnvios;
        }

        while ($esocialEnvio = pg_fetch_array($rs)) {
            $esocialEnvios[] = ESocialEnvio::fromState($esocialEnvio);
        }

        return $esocialEnvios;
    }

    public function atualizarEvento($sequencial, $situacao, $invalid = false)
    {
        $dao = new \cl_esocialenvio();
        $dao->rh213_situacao = $situacao;
        $dao->rh213_sequencial = $sequencial;

        // altera md5 do evento para pode ser enviado
        if ($invalid) {
            $dao->rh213_md5 = 'invalid';
        }
        $dao->alterar($sequencial);

        if ($dao->erro_status == 0) {
            throw new \Exception("Não foi possível alterar situação da fila.");
        }
    }

    /**
     * @param ESocialEnvio $dado
     * @throws BusinessException
     * @throws DBException
     * @throws ParameterException
     * Função criada com a finalidade de utilizar menor quantidade de memoria, pois
     * nem sempre todas as informações retornadas do sql são enviadas para o front-end
     * devido aos filtros de consultas na API.
     * o campo rh213_dados, por ser do tipo texto, acaba utilizando muito recurso de memoria
     * em grande quantidades de registros
     */
    public function ajustaDados(ESocialEnvio &$dado)
    {
        $codigo = $dado->getCodigoEnvio();
        $esocialEnvioTemporario = self::find($codigo, ['rh213_dados', 'rh213_situacao']);
        $dado->setDados($esocialEnvioTemporario->getDados());
        $dado->setSituacaoSalva($esocialEnvioTemporario->getSituacaoSalva());
        $dado->adicionaDescricaoPadrao();

        /**
         * Situacoes mapeadas pelos codigos da api
         * PROCESSADO_SUCESSO = 4
         * PROCESSADO_ERRO = 5
         * PROCESSADO_ADVERTENCIA = 12
        */
        if (!empty($dado->situacaoApi)) {
            switch ($dado->situacaoApi) {
                case 4:
                    if (!$this->exportacaoArquivo) {
                        $dado->setDescricao("<span style='color: green'>" . $dado->getDescricao() . "</span>");
                        $texto = "<span style='color: green'>" . $dado->getData()->format('d/m/Y H:i:s') . "</span>";
                        $dado->formataExibicaoData($texto);
                    }
                    break;
                case 7:
                case 5:
                    if ($dado->getSituacaoSalva() != 1) {
                        if (!$this->exportacaoArquivo) {
                            $dado->setDescricao("<span style='color: red'>" . $dado->getDescricao() . "</span>");
                            $texto = "<span style='color: red'>" . $dado->getData()->format('d/m/Y H:i:s') . "</span>";
                            $dado->formataExibicaoData($texto);
                            $this->atualizarEvento($codigo, 5, true);
                        }
                    } else {
                        $dado->setDescricao($dado->getDescricao());
                        $texto = $dado->getData()->format('d/m/Y H:i:s');
                        $dado->formataExibicaoData($texto);
                    }
                    break;
                case 12:
                    if (!$this->exportacaoArquivo) {
                        $dado->setDescricao("<span style='color: #8f8f0b'>" . $dado->getDescricao() . "</span>");
                        $texto = "<span style='color: #8f8f0b'>" . $dado->getData()->format('d/m/Y H:i:s') . "</span>";
                        $dado->formataExibicaoData($texto);
                    }
                    break;
            }
        }
        unset($esocialEnvioTemporario);
    }
}
