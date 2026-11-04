<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                    www.dbseller.com.br
 *                 e-cidade@dbseller.com.br
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
namespace ECidade\RecursosHumanos\ESocial\Service;

use CalculoFolha;
use CgmBase;
use DBCompetencia;
use DBPessoal;
use ECidade\RecursosHumanos\ESocial\Entity\RemuneracaoRGPS;
use ECidade\RecursosHumanos\Pessoal\Service\ServidorOutrosVinculosService;
use ECidade\RecursosHumanos\Pessoal\Service\ServidorProcessosJudiciaisFolhaService;
use EventoFinanceiroFolha;
use FolhaPagamento;
use Servidor;
use ServidorRepository;
use stdClass;

/**
 * Class RemuneracaoRGPSService
 * @package ECidade\RecursosHumanos\ESocial\Service
 */
class RemuneracaoRGPSService
{
    private $remuneracaoRGPS;

    private $remuneracoesRGPS = [];

    const SALARIO = "SALARIO";
    const COMPLEMENTAR = "COMPLEMENTAR";
    const DECIMO = "DECIMO";
    const RESCISAO = "RESCISAO";
    const RESCISAOPOSTERIOR = "RESCISAOPOSTERIOR";
    // informacoes de rescisao que deveram ser enviadas nesse evento
    const RESCISAOENVIADA = "RESC";

    const TIPOSALARIO = 0;
    const TIPOCOMPLEMENTAR = 1;
    const TIPODECIMO = 2;
    const TIPORESCISAO = 3;
    const TIPORESCISAOPOSTERIOR = 4;
    const TIPORESCISAOENVIADA = 5;

    /**
     * @var int
     */
    private $anoCompetencia;

    /**
     * @var int
     */
    private $mesCompetencia;

    /**
     * @var int
     */
    private $anoCompetenciaAtual;

    /**
     * @var int
     */
    private $mesCompetenciaAtual;

    public function __construct($ano = '', $mes = '', $anoCompetenciaAtual = '', $mesCompetenciaAtual = '')
    {
        if (empty($ano)) {
            $this->anoCompetencia = DBPessoal::getAnoFolha();
        } else {
            $this->anoCompetencia = $ano;
        }

        if (empty($mes)) {
            $this->mesCompetencia = DBPessoal::getMesFolha();
        } else {
            $this->mesCompetencia = $mes;
        }

        if (empty($anoCompetenciaAtual)) {
            $this->anoCompetenciaAtual = DBPessoal::getAnoFolha();
        } else {
            $this->anoCompetenciaAtual = $anoCompetenciaAtual;
        }

        if (empty($mesCompetenciaAtual)) {
            $this->mesCompetenciaAtual = DBPessoal::getMesFolha();
        } else {
            $this->mesCompetenciaAtual = $mesCompetenciaAtual;
        }
    }

    /**
     * @param CgmBase $cgm
     * @return RemuneracaoRGPS
     * @throws \BusinessException
     * @throws \DBException
     */
    public function buscarPorCGM(CgmBase $cgm)
    {
        $servidores = ServidorRepository::getServidoresByCgm(
            $cgm,
            new DBCompetencia($this->anoCompetencia, $this->mesCompetencia)
        );
        $qtdServidores = 0;
        foreach ($servidores as $servidor) {
            $matricula = $servidor->getMatricula();
            $this->remuneracaoRGPS = new RemuneracaoRGPS();
            $this->remuneracaoRGPS->setServidor($servidor);
            $this->buscarOutrosVinculos($matricula);
            $this->buscarPagamento($servidor);
            $this->buscarDadosTrabalhador($servidores[0]);
            $this->buscarProcessosJudiciais($matricula);
            /**
             * Em análise, foi verificado que o evento S-2260 deixou de existir
             * Ele era utilizado para buscar essa informação, por esse motivo estou comentando o trecho abaixo.
             */
            //$this->buscarTrabalhoIntermitente($matricula);
            $possuiPagamento = false;

            foreach ($this->remuneracaoRGPS->getPagamentos() as $pagamento) {
                $remuneracao = clone $this->remuneracaoRGPS;
                $remuneracao->setPagamentos([$pagamento]);
                $this->remuneracoesRGPS[] = $remuneracao;
                $possuiPagamento = true;
            }
            if ($possuiPagamento) {
                $qtdServidores += 1;
            }
        }

        if (sizeof($this->remuneracoesRGPS) > 0) {
            foreach ($this->remuneracoesRGPS as &$remuneracao) {
                $remuneracao->qtdServidores = $qtdServidores;
            }
        }
        return $this->remuneracoesRGPS;
    }


    /**
     * @param CgmBase $cgm
     * @return boolean
     * @throws \BusinessException
     * @throws \DBException
     */
    public function validaRGPSPorCGM(CgmBase $cgm)
    {
        if ($cgm instanceof \CgmJuridico) {
            return false;
        }
        $servidores = ServidorRepository::getServidoresByCgm(
            $cgm,
            new DBCompetencia($this->anoCompetencia, $this->mesCompetencia)
        );
        foreach ($servidores as $servidor) {
            // Validamos se e RGPS
            if ($servidor->isRgps()) {
                return true;
            }
            if ($servidor->is1200()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $matricula
     * @throws \Exception
     */
    private function buscarOutrosVinculos($matricula)
    {
        $parametros = new stdClass();
        $parametros->matricula = $matricula;

        $serviceOutrosVinculos = new ServidorOutrosVinculosService();
        $serviceOutrosVinculos->setAnoCompetencia($this->anoCompetencia);
        $serviceOutrosVinculos->setMesCompetencia($this->mesCompetencia);
        $servidorOutrosVinculos = $serviceOutrosVinculos->buscarOutrosVinculosPorMatriculaCompetencia($parametros);
        $this->remuneracaoRGPS->setServidorOutrosVinculos($servidorOutrosVinculos);
    }

    /**
     * @param Servidor $servidor
     * @throws \BusinessException
     * @throws \DBException
     */
    private function buscarPagamento(Servidor $servidor)
    {
        $calculoFinanceiroSalario = $servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_SALARIO);
        $calculoFinanceiroComplementar = $servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_COMPLEMENTAR);
        $calculoFinanceiroDecimoTerceiro = $servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_13o);
        $calculoFinanceiroRescisao = $servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_RESCISAO);
        /**
         * Índice:
         * 0 - CalculoFolha::CALCULO_SALARIO
         * 1 - CalculoFolha::CALCULO_COMPLEMENTAR
         * 2 - CalculoFolha::CALCULO_13
         */
        $pagamentos = [];
        $processa = false;
        $calculoSalario = $calculoFinanceiroSalario->getEventosFinanceiros();
        if (sizeof($calculoSalario) > 0) {
            $processa = true;
        }
        if (DBPessoal::verificarUtilizacaoEstruturaSuplementar()) {
            $calculoComplementar = $calculoFinanceiroComplementar->getEventosFinanceirosHistorico(
                FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR
            );
        } else {
            $calculoComplementar = $calculoFinanceiroComplementar->getEventosFinanceiros();
        }
        if (sizeof($calculoComplementar) > 0) {
            $processa = true;
        }
        $calculoDecimoTerceiro = $calculoFinanceiroDecimoTerceiro->getEventosFinanceiros();
        if (sizeof($calculoDecimoTerceiro) > 0) {
            $processa = true;
        }
        $calculoRescisao = $calculoFinanceiroRescisao->getEventosFinanceiros();
        if (sizeof($calculoRescisao) > 0) {
            $processa = true;
        }

        if ($processa) {
            $calculoPorLotacao = $this->buscarLotacaoTributaria($servidor);
            $totalLotacao = count($calculoPorLotacao);

            if ($totalLotacao == 0) {
                foreach ($calculoSalario as $eventoFinanceiro) {
                    $rubrica = new stdClass();
                    $rubrica->codigo = $eventoFinanceiro->getRubrica()->getCodigo();
                    $rubrica->descricao = $eventoFinanceiro->getRubrica()->getDescricao();
                    $rubrica->quantidade = $eventoFinanceiro->getQuantidade();
                    $rubrica->valor = $eventoFinanceiro->getValor();
                    $rubrica->tipo = $eventoFinanceiro->getRubrica()->getTipo();
                    $tipoRubrica = $eventoFinanceiro->getRubrica()->getTipo() == EventoFinanceiroFolha::PROVENTO
                        ? "Provento" : "Desconto";
                    $rubrica->descricaoTipo = $tipoRubrica;
                    $rubrica->decimoTerceiro = false;
                    $rubrica->nomePagamento = self::SALARIO;
                    $pagamentos[self::TIPOSALARIO][] = $rubrica;
                }

                foreach ($calculoComplementar as $eventoFinanceiro) {
                    $rubrica = new stdClass();
                    $rubrica->codigo = $eventoFinanceiro->getRubrica()->getCodigo();
                    $rubrica->descricao = $eventoFinanceiro->getRubrica()->getDescricao();
                    $rubrica->quantidade = $eventoFinanceiro->getQuantidade();
                    $rubrica->valor = $eventoFinanceiro->getValor();
                    $rubrica->tipo = $eventoFinanceiro->getRubrica()->getTipo();
                    $tipoRubrica = $eventoFinanceiro->getRubrica()->getTipo() == EventoFinanceiroFolha::PROVENTO
                        ? "Provento" : "Desconto";
                    $rubrica->descricaoTipo = $tipoRubrica;
                    $rubrica->decimoTerceiro = false;
                    $rubrica->nomePagamento = self::COMPLEMENTAR;

                    $pagamentos[self::TIPOCOMPLEMENTAR][] = $rubrica;
                }

                foreach ($calculoDecimoTerceiro as $eventoFinanceiro) {
                    $rubrica = new stdClass();
                    $rubrica->codigo = $eventoFinanceiro->getRubrica()->getCodigo();
                    $rubrica->descricao = $eventoFinanceiro->getRubrica()->getDescricao();
                    $rubrica->quantidade = $eventoFinanceiro->getQuantidade();
                    $rubrica->valor = $eventoFinanceiro->getValor();
                    $rubrica->tipo = $eventoFinanceiro->getRubrica()->getTipo();
                    $rubrica->decimoTerceiro = true;
                    $tipoRubrica = $eventoFinanceiro->getRubrica()->getTipo() == EventoFinanceiroFolha::PROVENTO
                        ? "Provento" : "Desconto";
                    $rubrica->descricaoTipo = $tipoRubrica;
                    $rubrica->nomePagamento = self::DECIMO;
                    $pagamentos[self::TIPODECIMO][] = $rubrica;
                }

                // Verificamos se devemos enviar as rubricas de rescisao no 1200 ou 2299
                $validacao = false;
                if ($servidor->temVinculoEmpregaticio()) {
                    $validacao = $servidor->validacaoEnvioRescisaoEsocialComVinculo();
                } else {
                    $validacao = $servidor->validacaoEnvioRescisaoEsocialSemVinculo();
                }
                if ($validacao) {
                    foreach ($calculoRescisao as $eventoFinanceiro) {
                        $rubrica = new stdClass();
                        $rubrica->codigo = $eventoFinanceiro->getRubrica()->getCodigo();
                        $rubrica->descricao = $eventoFinanceiro->getRubrica()->getDescricao();
                        $rubrica->quantidade = $eventoFinanceiro->getQuantidade();
                        $rubrica->valor = $eventoFinanceiro->getValor();
                        $rubrica->tipo = $eventoFinanceiro->getRubrica()->getTipo();
                        $rubrica->decimoTerceiro = false;
                        $rubrica->descricaoTipo = $eventoFinanceiro->getRubrica()->getTipo() ==
                            EventoFinanceiroFolha::PROVENTO ? "Provento" : "Desconto";
                        $rubrica->nomePagamento = self::RESCISAO;

                        $compRescisao = $servidor->getCompetenciaPagamentoRecisao($servidor->getMatricula());
                       
                        $enviar = '';
                        //Havendo cálculo rescisão e sua competencia for diferente da primeira será RESCISAOPOSTERIOR
                        if (!empty($compRescisao) &&
                            (int) $compRescisao->anocompetencia != (int) $servidor->getAnoCompetencia() ||
                            (int) $compRescisao->mescompetencia != (int) $servidor->getMesCompetencia()
                            ) {
                            $rubrica->nomePagamento = self::RESCISAOPOSTERIOR;
                            $enviar = self::TIPORESCISAO;
                        } else {
                            // Verificamos se devemos enviar as rubricas de rescisao no 1200 ou 2299
                            $validacao = false;
                            if ($servidor->temVinculoEmpregaticio()) {
                                $validacao = $servidor->validacaoEnvioRescisaoEsocialComVinculo();
                            } else {
                                $validacao = $servidor->validacaoEnvioRescisaoEsocialSemVinculo();
                            }
                            if ($validacao) {
                                $rubrica->nomePagamento = self::RESCISAOENVIADA;
                                $enviar = self::TIPORESCISAOENVIADA;
                            }
                        }

                        if (!empty($enviar)) {
                            $pagamentos[$enviar][] = $rubrica;
                        }
                    }
                }
            } else {
                $pagamentos = [];
                $valorParcialFinanceiroSalario = [];
                $valorFinanceiroSalario = 0.0;
                $valorFinanceiroComplementar =0.0;
                $valorParcialFinanceiroComplementar = [];
                $valorFinanceiroDecimoTerceiro = 0.0;
                $valorParcialFinanceiroDecimoTerceiro = [];
                $valorFinanceiroRescisao = 0.0;
                $valorParcialFinanceiroRescisao = [];
                //Utiliza o mapa de trabalho
                foreach ($calculoPorLotacao as $key => $lotacao) {
                    foreach ($calculoSalario as $eventoFinanceiro) {
                        $rubrica = new stdClass();
                        $rubrica->codigo = $eventoFinanceiro->getRubrica()->getCodigo();
                        $rubrica->descricao = $eventoFinanceiro->getRubrica()->getDescricao();
                        $rubrica->quantidade = $eventoFinanceiro->getQuantidade();
                        $valorFinanceiroSalario = $eventoFinanceiro->getValor();
                        $rubrica->valor = $valorFinanceiroSalario;
                        if ($lotacao->dias > 0) {
                            $rubrica->valor = round((float) $valorFinanceiroSalario*($lotacao->dias/30), 2);
                            if ($key == $totalLotacao) {
                                $rubrica->valor = (float) $valorFinanceiroSalario
                                    -$valorParcialFinanceiroSalario[$rubrica->codigo][$key-1];
                            } else {
                                if (empty($valorParcialFinanceiroSalario[$rubrica->codigo])) {
                                    $valorParcialFinanceiroSalario[$rubrica->codigo] = [];
                                    $valorParcialFinanceiroSalario[$rubrica->codigo][] = 0.0;
                                }
                                $x = sizeof($valorParcialFinanceiroSalario[$rubrica->codigo])-1;
                                $y = $valorParcialFinanceiroSalario[$rubrica->codigo][$x];
                                $valorParcialFinanceiroSalario[$rubrica->codigo][] = $y + $rubrica->valor;
                            }
                        }
                        $rubrica->tipo = $eventoFinanceiro->getRubrica()->getTipo();
                        $rubrica->descricaoTipo = $eventoFinanceiro
                            ->getRubrica()
                            ->getTipo() == EventoFinanceiroFolha::PROVENTO
                            ? "Provento" : "Desconto";
                        $rubrica->decimoTerceiro = false;
                        $rubrica->nomePagamento = self::SALARIO;
                        $rubrica->lotacao = $lotacao->lotacaoTributaria;
                        $pagamentos[self::TIPOSALARIO][] = $rubrica;
                    }

                    foreach ($calculoComplementar as $eventoFinanceiro) {
                        $rubrica = new stdClass();
                        $rubrica->codigo = $eventoFinanceiro->getRubrica()->getCodigo();
                        $rubrica->descricao = $eventoFinanceiro->getRubrica()->getDescricao();
                        $rubrica->quantidade = $eventoFinanceiro->getQuantidade();
                        $rubrica->valor = $eventoFinanceiro->getValor();
                        $valorFinanceiroComplementar = $eventoFinanceiro->getValor();
                        $rubrica->valor = $valorFinanceiroComplementar;
                        if ($lotacao->dias > 0) {
                            $rubrica->valor = round((float) $valorFinanceiroComplementar*($lotacao->dias/30), 2);
                            if ($key == $totalLotacao) {
                                $rubrica->valor = (float) $valorFinanceiroComplementar
                                    -$valorParcialFinanceiroComplementar[$rubrica->codigo][$key-1];
                            } else {
                                if (empty($valorParcialFinanceiroComplementar[$rubrica->codigo])) {
                                    $valorParcialFinanceiroComplementar[$rubrica->codigo] = [];
                                    $valorParcialFinanceiroComplementar[$rubrica->codigo][] = 0.0;
                                }
                                $x = sizeof($valorParcialFinanceiroComplementar[$rubrica->codigo])-1;
                                $y = $valorParcialFinanceiroComplementar[$rubrica->codigo][$x];
                                $valorParcialFinanceiroComplementar[$rubrica->codigo][] = $y + $rubrica->valor;
                            }
                        }
                        $rubrica->tipo = $eventoFinanceiro->getRubrica()->getTipo();
                        $rubrica->descricaoTipo = $eventoFinanceiro
                            ->getRubrica()
                            ->getTipo() == EventoFinanceiroFolha::PROVENTO ? "Provento" : "Desconto";
                        $rubrica->decimoTerceiro = false;
                        $rubrica->lotacao = $lotacao->lotacaoTributaria;
                        $rubrica->nomePagamento = self::COMPLEMENTAR;

                        $pagamentos[self::TIPOCOMPLEMENTAR][] = $rubrica;
                    }

                    foreach ($calculoDecimoTerceiro as $eventoFinanceiro) {
                        $rubrica = new stdClass();
                        $rubrica->codigo = $eventoFinanceiro->getRubrica()->getCodigo();
                        $rubrica->descricao = $eventoFinanceiro->getRubrica()->getDescricao();
                        $rubrica->quantidade = $eventoFinanceiro->getQuantidade();
                        $valorFinanceiroDecimoTerceiro = $eventoFinanceiro->getValor();
                        $rubrica->valor = $valorFinanceiroDecimoTerceiro;
                        if ($lotacao->dias > 0) {
                            $rubrica->valor = round((float) $valorFinanceiroDecimoTerceiro*($lotacao->dias/30), 2);
                            if ($key == $totalLotacao) {
                                $rubrica->valor = (float) $valorFinanceiroDecimoTerceiro
                                    -$valorParcialFinanceiroDecimoTerceiro[$rubrica->codigo][$key-1];
                            } else {
                                if (empty($valorParcialFinanceiroDecimoTerceiro[$rubrica->codigo])) {
                                    $valorParcialFinanceiroDecimoTerceiro[$rubrica->codigo] = [];
                                    $valorParcialFinanceiroDecimoTerceiro[$rubrica->codigo][] = 0.0;
                                }
                                $x = sizeof($valorParcialFinanceiroDecimoTerceiro[$rubrica->codigo])-1;
                                $y = $valorParcialFinanceiroDecimoTerceiro[$rubrica->codigo][$x];
                                $valorParcialFinanceiroDecimoTerceiro[$rubrica->codigo][] = $y + $rubrica->valor;
                            }
                        }
                        $rubrica->tipo = $eventoFinanceiro->getRubrica()->getTipo();
                        $rubrica->decimoTerceiro = true;
                        $rubrica->descricaoTipo = $eventoFinanceiro
                            ->getRubrica()
                            ->getTipo() == EventoFinanceiroFolha::PROVENTO
                            ? "Provento" : "Desconto";
                        $rubrica->nomePagamento = self::DECIMO;
                        $rubrica->lotacao = $lotacao->lotacaoTributaria;
                        $pagamentos[self::TIPODECIMO][] = $rubrica;
                    }

                    foreach ($calculoRescisao as $eventoFinanceiro) {
                        $rubrica = new stdClass();
                        $rubrica->codigo = $eventoFinanceiro->getRubrica()->getCodigo();
                        $rubrica->descricao = $eventoFinanceiro->getRubrica()->getDescricao();
                        $rubrica->quantidade = $eventoFinanceiro->getQuantidade();
                        $valorFinanceiroRescisao = $eventoFinanceiro->getValor();
                        $rubrica->valor = $valorFinanceiroRescisao;
                        if ($lotacao->dias > 0) {
                            $rubrica->valor = round((float) $valorFinanceiroRescisao*($lotacao->dias/30), 2);
                            if (empty($valorParcialFinanceiroRescisao[$rubrica->codigo])) {
                                $valorParcialFinanceiroRescisao[$rubrica->codigo] = [];
                                $valorParcialFinanceiroRescisao[$rubrica->codigo][] = 0.0;
                            }

                            if ($key == $totalLotacao) {
                                $rubrica->valor = (float) $valorFinanceiroRescisao
                                    -$valorParcialFinanceiroRescisao[$rubrica->codigo][$key-1];
                            } else {
                                $x = sizeof($valorParcialFinanceiroRescisao[$rubrica->codigo])-1;
                                $y = $valorParcialFinanceiroRescisao[$rubrica->codigo][$x];
                                $valorParcialFinanceiroRescisao[$rubrica->codigo][] = $y + $rubrica->valor;
                            }
                        }
                        $rubrica->tipo = $eventoFinanceiro->getRubrica()->getTipo();
                        $rubrica->decimoTerceiro = false;
                        $rubrica->descricaoTipo = $eventoFinanceiro
                            ->getRubrica()
                            ->getTipo() == EventoFinanceiroFolha::PROVENTO
                            ? "Provento" : "Desconto";
                        $rubrica->nomePagamento = self::RESCISAO;

                        $compRescisao = $servidor->getCompetenciaPagamentoRecisao($servidor->getMatricula());

                        //Havendo cálculo rescisão e sua competencia for diferente da primeira será RESCISAOPOSTERIOR
                        if (!empty($compRescisao) &&
                            (int) $compRescisao->anocompetencia != (int) $servidor->getAnoCompetencia() ||
                            (int) $compRescisao->mescompetencia != (int) $servidor->getMesCompetencia()
                            ) {
                            $rubrica->nomePagamento = self::RESCISAOPOSTERIOR;
                            $enviar = self::TIPORESCISAO;
                        } else {
                            // Verificamos se devemos enviar as rubricas de rescisao no 1200 ou 2299
                            $validacao = false;
                            if ($servidor->temVinculoEmpregaticio()) {
                                $validacao = $servidor->validacaoEnvioRescisaoEsocialComVinculo();
                            } else {
                                $validacao = $servidor->validacaoEnvioRescisaoEsocialSemVinculo();
                            }
                            if ($validacao) {
                                $rubrica->nomePagamento = self::RESCISAOENVIADA;
                                $enviar = self::TIPORESCISAOENVIADA;
                            }
                        }

                        if (!empty($enviar)) {
                            $rubrica->lotacao = $lotacao->lotacaoTributaria;
                            $pagamentos[$enviar][] = $rubrica;
                        }
                    }
                }
                $this->ajustaPagamentos($pagamentos);
            }
        }
        $this->remuneracaoRGPS->setPagamentos($pagamentos);
    }

    /**
     * @param Servidor $servidor
     */
    private function buscarDadosTrabalhador(Servidor $servidor)
    {
        $cgm = $servidor->getCgm();
        $dadosTrabalhador = new stdClass();
        $dadosTrabalhador->nome = $cgm->getNomeCompleto();
        $dadosTrabalhador->cpf = $cgm->getCpf();
        $dadosTrabalhador->nis = $cgm->getPIS();
        $dadosTrabalhador->nascimento = $cgm->getDataNascimento();

        $this->remuneracaoRGPS->setDadosTrabalhador($dadosTrabalhador);
    }

    /**
     * @param int $matricula
     * @throws \Exception
     */
    private function buscarProcessosJudiciais($matricula)
    {
        $parametros = new stdClass();
        $parametros->matricula = $matricula;

        $processosJudiciaisService = new ServidorProcessosJudiciaisFolhaService();
        $processosJudiciaisService->setAnoCompetencia($this->anoCompetencia);
        $processosJudiciaisService->setMesCompetencia($this->mesCompetencia);
        $processosJudiciais = $processosJudiciaisService->buscarProcessosJudiciaisPorMatriculaCompetencia($parametros);
        $this->remuneracaoRGPS->setProcessosJudiciais($processosJudiciais);
    }

    /**
     * @param int $anoCompetencia
     */
    public function setAnoCompetencia($anoCompetencia)
    {
        $this->anoCompetencia = $anoCompetencia;
    }

    /**
     * @param int $mesCompetencia
     */
    public function setMesCompetencia($mesCompetencia)
    {
        $this->mesCompetencia = $mesCompetencia;
    }

    public function getAnoCompetencia()
    {
        return $this->anoCompetencia;
    }

    public function getMesCompetencia()
    {
        return $this->mesCompetencia;
    }

    private function buscarTrabalhoIntermitente($matricula)
    {
        $service = new TrabalhoIntermitenteService();
        $dadosTrabalhador = $service->buscarDadosApiPorMatricula(
            $matricula,
            $this->anoCompetencia,
            $this->mesCompetencia
        );
        $this->remuneracaoRGPS->setTrabalhadoresIntermitente($dadosTrabalhador);
    }

    public function buscarRemuneracaoPeriodoAnterior($matricula)
    {
        $reajuste = new stdClass();
        $reajuste->dtAcConv = "";
        $reajuste->tpAcConv = "";
        $reajuste->dsc = "";

        $reajusteSalarial  = new \cl_rhreajustesalarialesocial();
        $camposReajusteSalarial = "eso39_dataefeito, eso39_tipo, eso39_descricao";
        $whereReajuste = "eso39_matricula =  {$matricula} ";
        $whereReajuste .= "and extract(year from eso39_dataefeito) <= {$this->anoCompetencia}";
        $whereReajuste .= "and extract(month from eso39_dataefeito) <= {$this->mesCompetencia}";
        $whereReajuste .= "and (extract(year from eso39_dataefeito) != {$this->anoCompetencia}";
        $whereReajuste .= "or extract(month from eso39_dataefeito) != {$this->mesCompetencia})";
        $ordemReajuste = "eso39_sequencial desc";
        $sqlReajusteSalarial  = $reajusteSalarial->sql_query(
            null,
            $camposReajusteSalarial,
            $ordemReajuste,
            $whereReajuste
        );
        $rsReajusteSalarial = db_query($sqlReajusteSalarial);

        if (pg_num_rows($rsReajusteSalarial) > 0) {
            $dadosReajuste = \db_utils::fieldsMemory($rsReajusteSalarial, 0);
            $reajuste->dtAcConv = $dadosReajuste->eso39_dataefeito;
            $reajuste->tpAcConv = $dadosReajuste->eso39_tipo;
            $reajuste->dsc = $dadosReajuste->eso39_descricao;
        }
        return $reajuste;
    }

    public function buscarPeriodoAnterior($matricula)
    {
        $grupoIdePeriodo = new stdClass();
        $grupoIdePeriodo->perRef = '';

        $reajusteSalarial  = new \cl_rhreajustesalarialesocial();
        $camposReajusteSalarial = "eso39_dataefeito, eso39_tipo, eso39_descricao";
        $whereReajuste = "eso39_matricula =  {$matricula} ";
        $whereReajuste .= "and extract(year from eso39_dataefeito) <= {$this->anoCompetencia}";
        $whereReajuste .= "and extract(month from eso39_dataefeito) <= {$this->mesCompetencia}";
        $whereReajuste .= "and (extract(year from eso39_dataefeito) != {$this->anoCompetencia}";
        $whereReajuste .= "or extract(month from eso39_dataefeito) != {$this->mesCompetencia})";
        $ordemReajuste = "eso39_sequencial desc";
        $sqlReajusteSalarial  = $reajusteSalarial->sql_query(
            null,
            $camposReajusteSalarial,
            $ordemReajuste,
            $whereReajuste
        );

        $rsReajusteSalarial = db_query($sqlReajusteSalarial);

        if (pg_num_rows($rsReajusteSalarial) > 0) {
            $dadoCompetenciaPeriodo = \db_utils::fieldsMemory($rsReajusteSalarial, 0);
            $competenciaPeriodo = explode('-', $dadoCompetenciaPeriodo->eso39_dataefeito);
            if (!empty($competenciaPeriodo[1])) {
                $grupoIdePeriodo->perRef  = $competenciaPeriodo[0] . '-' . $competenciaPeriodo[1];
            }
        }
        return  $grupoIdePeriodo;
    }

    //Metodo sendo utilizado no RPPS
    public function buscarLotacaoTributaria($servidor)
    {
        //Criação do mapa de trabalho para saber os dias em cada lotacao tributária
        $periodos = $servidor->getLotacaoPorPeriodo(
            $servidor,
            $this->anoCompetencia,
            $this->mesCompetencia,
            $this->anoCompetenciaAtual,
            $this->mesCompetenciaAtual
        );

        // caso nao retorne nenhuma lotacao no periodo informado, buscamos na competencia atual
        if (empty($periodos)) {
            $periodos = $servidor->getLotacaoPorPeriodo(
                $servidor,
                $this->anoCompetenciaAtual,
                $this->mesCompetenciaAtual,
                $this->anoCompetenciaAtual,
                $this->mesCompetenciaAtual
            );
        }
        foreach ($periodos as $periodo) {
            $mapaTrabalho[] =
                $dadomapa = new stdClass();
                $dadomapa->dataInicio = $periodo->datainicio;
                $dadomapa->dataFim = $periodo->datafim;
                $dadomapa->lotacaoTributaria = $periodo->lotacaotributaria;
                $dadomapa->dias = 0;
                $dadomapa->ultimaLotacao =  false;
        }

        $lotacoes = [];
        if (!isset($mapaTrabalho)) {
            return [];
        }
        foreach ($mapaTrabalho as $key => $dadomapa) {
            if (!in_array($dadomapa->lotacaoTributaria, $lotacoes)) {
                $lotacoes[] = $dadomapa->lotacaoTributaria;
            }
        }

        if (count($lotacoes) == 1) {
            $mapaTrabalho[0]->dias = 30;
            $mapaTrabalho[0]->ultimaLotacao = true;
            return [$mapaTrabalho[0]];
        }
        $datasTrabalho = [];
        foreach ($lotacoes as $key => $lotacao) {
            foreach ($mapaTrabalho as $indice => $dataTrabalho) {
                if ($dataTrabalho->lotacaoTributaria == $lotacao) {
                    $datasTrabalho[$lotacao]['inicio'] = $dataTrabalho->dataInicio;
                    $datasTrabalho[$lotacao]['fim'] = $dataTrabalho->dataFim;
                }
            }
        }
        $diasTotais = 0;
        $primeiroDia = $this->getAnoCompetencia() . "-" . $this->getMesCompetencia() . "-01";
        $ultimoDia = date('Y-m-t', strtotime($primeiroDia));
        $dias = [];
        $numeroLotacoes = count($lotacoes);
        $y = $numeroLotacoes;
        foreach ($datasTrabalho as $lotacao => $diasTrabalhado) {
            $dataFimJornada = $diasTrabalhado['fim'];
            if (empty($dataFimJornada)) {
                $dataFimJonada = $ultimoDia;
            }
            $dataInicioJornada = $diasTrabalhado['inicio'];

            if (date('Y-m-d', strtotime($dataInicioJornada))< date('Y-m-d', strtotime($primeiroDia))) {
                $dataInicioJornada = $primeiroDia;
            }
            $diasJornada = $this->retornaDiasEntreDatas($dataInicioJornada, $dataFimJornada);
            if ($diasJornada > 30) {
                $diasJornada = (int) round($diasJornada / $numeroLotacoes, 0);
            }

            $y -= 1;
            $dias[$lotacao] = $diasJornada;
            if ($y == 0) {
                $dias[$lotacao] = 30 - $diasTotais;
            }
            $diasTotais += $diasJornada;
        }

        $z = $numeroLotacoes;
        foreach ($dias as $lotacao => $diasTrabalhado) {
            foreach ($mapaTrabalho as $key => $dadomapa) {
                if ($dadomapa->lotacaoTributaria == $lotacao) {
                    $mapaTrabalho[$key]->dias = $diasTrabalhado;
                }
            }
            $z -= 1;
            if ($z == 0) {
                $mapaTrabalho[$key]->ultimaLotacao = true;
            }
        }
        return $mapaTrabalho;
    }

    private function retornaDiasEntreDatas($data1, $data2)
    {
        $data_inicio = new \DateTime($data1);
        $data_fim = new \DateTime($data2);
        // Resgata diferença entre as datas
        $dias = $data_inicio->diff($data_fim);
        return $dias->days+1;
    }

    private function retornaInidiceData($data, $array)
    {
        foreach ($array as $key => $val) {
            if ($val->dataInicio === $data) {
                return $key;
            }
            if ($val->dataFim === $data) {
                return $key;
            }
        }
        return null;
    }

    public function buscarEstabelicimentoLotacao($matricula, $mesCompetencia, $anoCompetencia)
    {
        $grupoIdeEstabLot = new stdClass();
        $grupoIdeEstabLot->tpInsc = 1;
        $grupoIdeEstabLot->nrInsc = '';
        $grupoIdeEstabLot->codLotacao = '';

        $sql = "
        select
            b.cgc,
            c.rh268_codigolotacao
        from
            pessoal.rhpessoalmov a
        inner join configuracoes.db_config b on
            b.codigo = a.rh02_instit
        inner join recursoshumanos.rhlotacaotributaria c on
            c.rh268_numcgm = b.numcgm
        where
            a.rh02_regist = {$matricula}
        ";
        if (!empty($anoCompetencia)) {
            $sql .= " and a.rh02_anousu = {$anoCompetencia}";
        }
        if (!empty($mesCompetencia)) {
            $sql .= " and a.rh02_mesusu = {$mesCompetencia}";
        }

        $resultado = db_query($sql);

        $registro = pg_numrows($resultado);
        if ($registro > 0) {
            $grupoIdeEstabLot->nrInsc = \db_utils::fieldsMemory($resultado, 0)->cgc;
            $grupoIdeEstabLot->codLotacao = \db_utils::fieldsMemory($resultado, 0)->rh268_codigolotacao;
        }
        return $grupoIdeEstabLot;
    }

    //Metodo sendo utilizado no RPPS
    /**
     * @param $pagamentos
     * Soma os valores de remuneração por rubrica, lotação e tipo de pagamento (SALÁRIO, COMPLEMETAR, por exemplo)
    */
    public function ajustaPagamentos(&$pagamentos)
    {
        $rubricaUniValor = [];
        foreach ($pagamentos as $pagamento) {
            foreach ($pagamento as $y => $rubricaPagamento) {
                if (array_key_exists($rubricaPagamento->codigo . '-' .
                    $rubricaPagamento->lotacao . '-' .
                    $rubricaPagamento->nomePagamento, $rubricaUniValor)) {
                    $rubricaUniValor[$rubricaPagamento->codigo . '-' .
                        $rubricaPagamento->lotacao . '-' .
                        $rubricaPagamento->nomePagamento] +=
                        $rubricaPagamento->valor;
                } else {
                    $rubricaUniValor[$rubricaPagamento->codigo . '-' .
                        $rubricaPagamento->lotacao. '-' .
                        $rubricaPagamento->nomePagamento] =
                        $rubricaPagamento->valor;
                }
            }
        }
        $novoPagamento = [];
        foreach ($pagamentos as $i => $tipoPagamento) {
            $novoPagamento[$i] = [];
            foreach ($tipoPagamento as $rubrica) {
                $novoPagamento[$i][$rubrica->lotacao][$rubrica->codigo] = $rubrica;
            }
        }
        $pagamentos = [];
        $contador = 0;
        foreach ($novoPagamento as $pagamento) {
            $pagamentos[$contador] = [];
            foreach ($pagamento as $lotacao) {
                foreach ($lotacao as $rubrica) {
                    $valorUnificado = $rubricaUniValor[
                        $rubrica->codigo . '-' .
                        $rubrica->lotacao . '-' .
                        $rubrica->nomePagamento];
                    $rubrica->valor = $valorUnificado;
                    $pagamentos[$contador][] = $rubrica;
                }
            }
            $contador += 1;
        }
    }
}
