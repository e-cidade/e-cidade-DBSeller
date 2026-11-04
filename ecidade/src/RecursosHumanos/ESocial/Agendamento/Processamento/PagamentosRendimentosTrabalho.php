<?php
/**
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
 *  junto com este programa; se nao, escreva para a Free Softwareb
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\RecursosHumanos\ESocial\Agendamento\Processamento;

use ECidade\RecursosHumanos\ESocial\Agendamento\Evento;
use ECidade\RecursosHumanos\ESocial\Integracao\FormatterFactory;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Integracao\Recurso;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocial;
use ECidade\RecursosHumanos\Pessoal\Service\DataPagamentoFolhaService;
use InstituicaoRepository;
use ECidade\V3\Extension\Registry;
use stdClass;
use CgmRepository;
use ServidorRepository;
use ParameterException;
use ParametrosPessoalRepository;
use Exception;

/**
 * Class PagamentosRendimentosTrabalho
 * @package ECidade\RecursosHumanos\ESocial\Agendamento\Processamento
 */
class PagamentosRendimentosTrabalho extends ProcessamentoAbstract implements ProcessamentoInterface
{
    private $cgm;
    private $mes;
    private $ano;
    private $processaPorCompetencia = false;

    /**
     * @param integer $mes
     * Seta o mes da competencia informada
     */
    public function setMes($mes)
    {
        $this->mes = $mes;
    }

    /**
     * @param integer $ano
     */
    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    public function __construct($cgm)
    {
        $this->cgm = $cgm;
    }

    /**
     * @return bool|mixed
     * @throws \Exception
     */
    public function processar()
    {
        // Adicionadas variaveis de controle de processamento do mes 12 e Decimo
        $processaMes12 = false;
        $processa13 = false;
        $this->ano = $this->getAnoCaixa();
        $this->mes = $this->getMesCaixa();

        ini_set("memory_limit", "1024M");
        $alteracao = false;
        $competencia =  $this->ano . $this->mes;
        $quantidadeCompetencia = -6;
        $servidores = [];

        if (empty($this->ano) || empty($this->mes)) {
            throw new ParameterException("Ano ou mês não informados.");
        }
        if ($this->isForcarMatricula() && sizeof($this->servidores) == 0) {
            throw new ParameterException("Nenhuma matrícula informada. Por Favor selecione as matrículas.");
        }

        $dataPagamentoService = new DataPagamentoFolhaService();
        $dataPagamentoService->setAnoCompetencia($this->ano);
        $dataPagamentoService->setMesCompetencia($this->mes);
        $stdParametros = new stdClass();
        $stdParametros->instituicao = InstituicaoRepository::getInstituicaoSessao()->getCodigo();

        $dataPagamentos = $dataPagamentoService->buscarDataPagamentoInstituicaoCompetenciaCaixa($stdParametros);
        // Controle de Competencias que serão processadas
        $competenciasProcessamento = [];
        // Caso seja o mes 12 na competencia de caixa, vindo da tela, adicionamos o processamento de 13
        if ($this->mes == 12) {
            $processa13 = true;
            $competenciasProcessamento[$this->ano] = [];
            $competenciasProcessamento[$this->ano][$this->mes] = 1;
        }
        foreach ($dataPagamentos as $dataPagamento) {
            if (empty($competenciasProcessamento[$dataPagamento->getAno()])) {
                $competenciasProcessamento[$dataPagamento->getAno()] = [];
            }
            // Caso o mes 12 tenha sido pago na competencia de caixa informada pela tela, adicionamos o processamento
            // do mes 12, pois não necessáriamente o mes 12 foi pago em dezembro, porem o 13 deve ir junto com os
            // pagamentos realizados em dezembro
            if ($dataPagamento->getMes() == 12) {
                $processaMes12 = true;
            }
            $mes = $dataPagamento->getMes();
            $competenciasProcessamento[$dataPagamento->getAno()][str_pad($mes, 2, '0', STR_PAD_LEFT)] = 1;
        }
        $dados = new stdClass();
        $body = new stdClass();
        $body->inscricaoEmpregador = CgmRepository::buscarCNPJEmpregador($this->cgm);
        $service = new ESocial(
            Registry::get('app.config'),
            Recurso::CONSULTA_REFERENCIA_PARA_PAGAMENTOS_RENDIMENTOS_TRABALHO
        );

        if (!empty($this->selecao)) {
            try {
                $instituicao = InstituicaoRepository::getInstituicaoByCodigo(db_getsession("DB_instit"));

                $this->servidores = ServidorRepository::getServidoresBySelecao(
                    $this->ano,
                    $this->mes,
                    $this->selecao,
                    $instituicao->getCodigo()
                );
                $this->servidores = array_values($this->servidores);
            } catch (Exception $e) {
                throw new \DBException("Ocorrêu um erro ao buscar as informações da seleção informada.");
            }
        }
        foreach ($competenciasProcessamento as $ano => $mesCompetencias) {
            foreach ($mesCompetencias as $mes => $value) {
                $retornoRequisicao = new stdClass();
                $competencia = $ano . $mes;
                if (!empty($this->servidores)) {
                    $body->referencias = [];
                    foreach ($this->servidores as $servidor) {
                        switch ($servidor->getTipoPagamentoEsocialAPI($ano, $mes)) {
                            case Tipo::S1202_API:
                                $body->referencias[] = $servidor->getCgm()->getCodigo() . '-' . $competencia . '-1';
                                $body->referencias[] = $servidor->getCgm()->getCodigo() . '-' . $competencia . '-2';
                                break;
                            case Tipo::S1207_API:
                                $body->referencias[] = $servidor->getCgm()->getCodigo() . '_' . $competencia . '_1';
                                $body->referencias[] = $servidor->getCgm()->getCodigo() . '_' . $competencia . '_2';
                                break;
                            default:
                                $body->referencias[] = $servidor->getCgm()->getCodigo() . $competencia . '1';
                                $body->referencias[] = $servidor->getCgm()->getCodigo() . $competencia . '2';
                                break;
                        }
                        if ($servidor->isRescindido()) {
                            $dadosRescisao = $servidor->getDadosRescisao();
                            $dataRescisao = new \DBDate($dadosRescisao->rh05_recis);

                            if (($dataPagamento->getAno() == $dataRescisao->getAno() &&
                                $dataPagamento->getMes() == $dataRescisao->getMes())
                                || ($this->ano == $dataRescisao->getAno() &&
                                $this->mes == $dataRescisao->getMes())
                            ) {
                                $body->referencias[] = $dadosRescisao->rh05_codigorescisao;
                            }
                        }
                    }
                    $service->setDados($body);
                    $retornoRequisicao = $this->buscaDadosApi(clone $service);
                } else {
                    $body->competencia = "%{$competencia}%" ;
                    $this->processaPorCompetencia = true;
                    $service->setDados($body);
                    $retornoRequisicao = $this->buscaDadosApi(clone $service);
                }
                if (!empty($retornoRequisicao)) {
                    if (empty($dados)) {
                        $dados = $retornoRequisicao;
                    } else {
                        if (isset($dados->eventos) && !empty($dados->eventos)) {
                            foreach ($retornoRequisicao->eventos as $evento) {
                                $dados->eventos[] = $evento;
                            }
                        } else {
                            $dados->eventos = $retornoRequisicao->eventos;
                        }
                    }
                }
            }
        }

        if (empty($dados)) {
            $dados = (object) $dados;
        }

        if ($this->isForcarMatricula()) {
            $servidores = $this->servidores;
        }

        $this->servidores = [];
        $dados->cgms = [];
        if (isset($dados->eventos)) {
            foreach ($dados->eventos as $dado) {
                $dadoCgm = new \stdClass();
                $dadoCgm->evento = $dado->tipo_evento_id;
                switch ($dado->tipo_evento_id) {
                    case TIPO::S2299_API:
                    case TIPO::S2399_API:
                        $dadoCgm->competencia = new stdClass();
                        $dadoCgm->competencia->ano = substr($dado->referencia, strlen($dado->referencia) - 6, -2);
                        $dadoCgm->competencia->mes = substr($dado->referencia, strlen($dado->referencia) - 2);
                        $dado->referencia = substr($dado->referencia, 0, $quantidadeCompetencia);
                        if (!\ServidorRepository::isMatriculaValida($dado->referencia)) {
                            continue;
                        }
                        $servidor = ServidorRepository::getInstanciaByCodigo(
                            $dado->referencia,
                            $this->ano,
                            $this->mes
                        );
                        $dadoCgm->cgm = $servidor->getCgm();
                        if ($servidor->isRpps()) {
                            $dadoCgm->evento = TIPO::S1202_API;
                        } else {
                            $dadoCgm->evento = TIPO::S1200_API;
                        }
                        unset($servidor);
                        break;
                    case TIPO::S1207_API:
                        $referencia = explode('_', $dado->referencia);
                        $dadoCgm->competencia = new stdClass();
                        $dadoCgm->competencia->ano = substr($referencia[1], 0, -2);
                        $dadoCgm->competencia->mes = substr($referencia[1], 4);
                        $dado->referencia = $referencia[0];
                        $dadoCgm->cgm = CgmRepository::getByCodigo($dado->referencia);
                        break;
                    case TIPO::S1202_API:
                        $referencia = explode('-', $dado->referencia);
                        $dadoCgm->competencia = new stdClass();
                        $dadoCgm->competencia->ano = substr($referencia[1], 0, -2);
                        $dadoCgm->competencia->mes = substr($referencia[1], 4);
                        $dado->referencia = $referencia[0];
                        $dadoCgm->cgm = CgmRepository::getByCodigo($dado->referencia);
                        break;
                    default:
                        $dadoCgm->competencia = new stdClass();
                        $dadoCgm->competencia->ano = substr($dado->referencia, strlen($dado->referencia) - 7, -3);
                        $dadoCgm->competencia->mes = substr($dado->referencia, strlen($dado->referencia) - 3, -1);
                        $dado->referencia = substr($dado->referencia, 0, -7);
                        $dadoCgm->cgm = CgmRepository::getByCodigo($dado->referencia);
                        break;
                }

                if (isset($dadoCgm->cgm)) {
                    if (empty($dados->cgms[$dadoCgm->cgm->getCodigo()])) {
                        $dados->cgms[$dadoCgm->cgm->getCodigo()] = new stdClass();
                        $dados->cgms[$dadoCgm->cgm->getCodigo()]->competencias = [];
                        //CRIA INSTÂNCIA EM MEMÓRIA
                        $dados->cgms[$dadoCgm->cgm->getCodigo()]->competenciaTmp = [];
                        $dados->cgms[$dadoCgm->cgm->getCodigo()]->cgm = null;
                    }
                    $dados->cgms[$dadoCgm->cgm->getCodigo()]->cgm = $dadoCgm->cgm;
                    $dados->cgms[$dadoCgm->cgm->getCodigo()]->competencias[] = $dadoCgm->competencia;
                    //VERIFICA SE A REFERENCIA ESTÁ VAZIO
                    if (empty($dados->cgms[$dadoCgm->cgm->getCodigo()]->competenciaTmp[$dadoCgm->competencia->ano])) {
                        $dados->cgms[$dadoCgm->cgm->getCodigo()]->competenciaTmp[$dadoCgm->competencia->ano] = [];
                    }
                    $codigo = $dadoCgm->cgm->getCodigo();
                    $mesCompetencia = $dadoCgm->competencia->mes;
                    $anoCompetencia = $dadoCgm->competencia->ano;
                    $dados->cgms[$codigo]->competenciaTmp[$anoCompetencia][$mesCompetencia] = $mesCompetencia;
                }
            }
        }
        //PEGANDO OS CGMS
        foreach ($dados->cgms as &$cgm) {
            $competencias = [];
            //BUSCANDO AS COMPETENCIAS
            foreach ($cgm->competenciaTmp as $ano => $competenciaTmp) {
                //BUSCAR POR MÊS E ANO
                foreach ($competenciaTmp as $mes) {
                    $competencia = new stdClass();
                    $competencia->mes = $mes;
                    $competencia->ano = $ano;
                    $competencias[] = $competencia;
                }
            }
            //APAGA A VARIÁVEL TEMPORARIA
            unset($cgm->competenciaTmp);
            $cgm->competencias = $competencias;
        }

        $dados->eventos = [];
        $dados->inscricao_empregador = $body->inscricaoEmpregador;
        $dados->anoCompetencia = $this->ano;
        $dados->mesCompetencia = $this->mes;

        $competencia = new \DBCompetencia($dados->anoCompetencia, $dados->mesCompetencia);
        $parametros = ParametrosPessoalRepository::getParametros($competencia);
        $formatter = FormatterFactory::get(Tipo::S1210);
        $formatter->setProcessaMes12($processaMes12);

        if (!empty($parametros->getMes13()) && $parametros->getMes13() == $this->mes && $this->mes != 1) {
            $processa13 = true;
        }

        $formatter->setProcessa13($processa13);

        if ($this->isForcarMatricula()) {
            $formatter->setServidores($servidores);
        }
        // Verifica se a competencia é a de pagamento do decimo terceiro
        if (!empty($parametros->getMes13()) && $parametros->getMes13() == $this->mes) {
            $formatter->setDecimoTerceiro();
        }
        $dadosPreenchimentoEmpregador = $formatter->formatar((array) $dados);
        $validaMd5 = true;

        if ($this->envioForcado) {
            $validaMd5 = false;
        }
        foreach ($dadosPreenchimentoEmpregador as $indice => $dados) {
            $evento = new Evento(TIPO::S1210, $this->cgm, $dados->referencia, $dados);
            $evento->iContador = $indice;

            if ($evento->adicionarFila(false, $validaMd5)) {
                $alteracao = true;
            }
        }

        return $alteracao;
    }

    private function buscaDadosApi($service)
    {
        $retorno = $service->request('GET');
        /**
         * Quando processamos por competencia, fazemos uma segunda requisicao na api devido aos desligamentos com
         * data de pagamento na competencia informada em tela (desconsiderando a competencia de caixa)
         */
        if ($this->processaPorCompetencia) {
            $dados = $service->getDados();
            $dados->competencia = "%{$this->ano}{$this->mes}%";
            $service->setDados($dados);
            $service->setRecurso(Recurso::CONSULTA_REFERENCIA_PARA_PAGAMENTOS_RENDIMENTOS_TRABALHO_DESLIGAMENTO);
            if (!empty($retorno)) {
                if (isset($retorno->eventos) && !empty($retorno->eventos)) {
                    $desligamentos = $service->request('GET');
                    if (!empty($desligamentos)) {
                        if (isset($desligamentos->eventos) && !empty($desligamentos->eventos)) {
                            foreach ($desligamentos->eventos as $evento) {
                                $retorno->eventos[] = $evento;
                            }
                        }
                    }
                }
            } else {
                $desligamentos = $service->request('GET');
            }
        }
        return $retorno;
    }
}
