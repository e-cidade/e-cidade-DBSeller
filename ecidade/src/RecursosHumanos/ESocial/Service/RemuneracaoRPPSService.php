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

use ECidade\RecursosHumanos\ESocial\Entity\RemuneracaoRPPS;
use ECidade\RecursosHumanos\Pessoal\Service\ServidorOutrosVinculosService;
use ECidade\RecursosHumanos\Pessoal\Service\ServidorOperadoraSaudeService;
use ECidade\RecursosHumanos\Pessoal\Service\ServidorProcessosJudiciaisFolhaService;
use ECidade\RecursosHumanos\ESocial\Service\TrabalhoIntermitenteService;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorOperadoraSaudeRepository;
use ServidorRepository;
use Servidor;
use stdClass;
use CgmBase;
use CalculoFolha;
use EventoFinanceiroFolha;
use DBCompetencia;
use DBPessoal;
use FolhaPagamento;
use ECidade\RecursosHumanos\ESocial\Service\RemuneracaoRGPSService;
use ECidade\RecursosHumanos\ESocial\Entity\Remuneracao;

/**
 * Class remuneracaoRPPSService
 * @package ECidade\RecursosHumanos\ESocial\Service
 */
class RemuneracaoRPPSService
{
    private $remuneracaoRPPS;

    private $remuneracoesRPPS = [];

    private $remuneracao;

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

    public function __construct($ano = '', $mes = '')
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
    }

    /**
     * @param CgmBase $cgm
     * @return remuneracaoRPPS
     * @throws \BusinessException
     * @throws \DBException
     */
    public function buscarPorCGM(CgmBase $cgm)
    {
        $servidores = ServidorRepository::getServidoresByCgm(
            $cgm,
            new DBCompetencia($this->anoCompetencia, $this->mesCompetencia)
        );
        $quantidadeServidores = 0;
        foreach ($servidores as $servidor) {
            if (!$servidor->isRpps($this->anoCompetencia, $this->mesCompetencia)) {
                continue;
            }
            $matricula = $servidor->getMatricula();

            $this->remuneracaoRPPS = new remuneracaoRPPS();
            $this->remuneracaoRPPS->setServidor($servidor);
            $this->buscarOutrosVinculos($matricula);
            $this->buscarPagamento($servidor);
            $this->buscarDadosTrabalhador($servidores[0]);
            $possuiPagamento = false;
            foreach ($this->remuneracaoRPPS->getPagamentos() as $pagamento) {
                $remuneracao = clone $this->remuneracaoRPPS;
                $remuneracao->setPagamentos([$pagamento]);
                $this->remuneracoesRPPS[] = $remuneracao;
                $possuiPagamento = true;
            }

            if ($possuiPagamento) {
                $quantidadeServidores += 1;
            }
        }

        if (sizeof($this->remuneracaoRPPS) > 0) {
            foreach ($this->remuneracaoRPPS as &$remuneracao) {
                $remuneracao->quantidadeServidor = $quantidadeServidores;
            }
        }

        return $this->remuneracoesRPPS;
    }


    /**
     * @param CgmBase $cgm
     * @return boolean
     * @throws \BusinessException
     * @throws \DBException
     */
    public function validaRPPSPorCGM(CgmBase $cgm)
    {
        if ($cgm instanceof \CgmJuridico) {
            return false;
        }
        $servidores = ServidorRepository::getServidoresByCgm(
            $cgm,
            new DBCompetencia($this->anoCompetencia, $this->mesCompetencia)
        );
        foreach ($servidores as $servidor) {
            // Validamos se e RPPS
            if (!$servidor->isRgps()) {
                return true;
            }
            if (!$servidor->is1200()) {
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
        $this->remuneracaoRPPS->setServidorOutrosVinculos($servidorOutrosVinculos);
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
         * 3 - CalculoFolha::CALCULO_RESCISAO
         */

        if (DBPessoal::verificarUtilizacaoEstruturaSuplementar()) {
            $calculoComplementar = $calculoFinanceiroComplementar->getEventosFinanceirosHistorico(
                FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR
            );
        } else {
            $calculoComplementar = $calculoFinanceiroComplementar->getEventosFinanceiros();
        }
 
        $pagamentos = [];

        //Otimizando o processamento
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
            //Definindo as lotações tributárias
            $remunercaoRGPSService = new RemuneracaoRGPSService();
            $calculoPorLotacao = $remunercaoRGPSService->buscarLotacaoTributaria($servidor);
            $totalLotacao = count($calculoPorLotacao);
            if ($totalLotacao == 0) {
                foreach ($calculoFinanceiroSalario->getEventosFinanceiros() as $eventoFinanceiro) {
                    $rubrica = new stdClass();
                    $rubrica->codigo = $eventoFinanceiro->getRubrica()->getCodigo();
                    $rubrica->descricao = $eventoFinanceiro->getRubrica()->getDescricao();
                    $rubrica->quantidade = $eventoFinanceiro->getQuantidade();
                    $rubrica->valor = $eventoFinanceiro->getValor();
                    $rubrica->tipo = $eventoFinanceiro->getRubrica()->getTipo();
                    $rubrica->descricaoTipo = $eventoFinanceiro->getRubrica()->getTipo() ==
                        EventoFinanceiroFolha::PROVENTO ? "Provento" : "Desconto";
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
                    $rubrica->descricaoTipo = $eventoFinanceiro->getRubrica()->getTipo() ==
                        EventoFinanceiroFolha::PROVENTO ? "Provento" : "Desconto";
                    $rubrica->decimoTerceiro = false;
                    $rubrica->nomePagamento = self::COMPLEMENTAR;
        
                    $pagamentos[self::TIPOCOMPLEMENTAR][] = $rubrica;
                }
        
                foreach ($calculoFinanceiroDecimoTerceiro->getEventosFinanceiros() as $eventoFinanceiro) {
                    $rubrica = new stdClass();
                    $rubrica->codigo = $eventoFinanceiro->getRubrica()->getCodigo();
                    $rubrica->descricao = $eventoFinanceiro->getRubrica()->getDescricao();
                    $rubrica->quantidade = $eventoFinanceiro->getQuantidade();
                    $rubrica->valor = $eventoFinanceiro->getValor();
                    $rubrica->tipo = $eventoFinanceiro->getRubrica()->getTipo();
                    $rubrica->decimoTerceiro = true;
                    $rubrica->descricaoTipo = $eventoFinanceiro->getRubrica()->getTipo() ==
                        EventoFinanceiroFolha::PROVENTO ? "Provento" : "Desconto";
                    $rubrica->nomePagamento = self::DECIMO;
                    $pagamentos[self::TIPODECIMO][] = $rubrica;
                }
        
                $validacao = false;
                if ($servidor->temVinculoEmpregaticio()) {
                    $validacao = $servidor->validacaoEnvioRescisaoEsocialComVinculo();
                } else {
                    $validacao = $servidor->validacaoEnvioRescisaoEsocialSemVinculo();
                }
                if ($validacao) {
                    foreach ($calculoFinanceiroRescisao->getEventosFinanceiros() as $eventoFinanceiro) {
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
                        $enviar = '';
        
                        $competenciasRescisao = $servidor->getCompetenciasPagamentosRescisao();
                        if (sizeof($competenciasRescisao) > 0) {
                            foreach ($competenciasRescisao as $key => $value) {
                                if ($key > 0) {
                                    if ($servidor->getAnoCompetencia() == (int) $value->anousu
                                        && $servidor->getMesCompetencia() == (int) $value->mesusu) {
                                        $dadosRescisao = $servidor->getDadosRescisao();
                                        if (!empty($dadosRescisao)) {
                                            $anoRescisao = substr($dadosRescisao->rh05_datapagamento, 0, 4);
                                            $mesRescisao = substr($dadosRescisao->rh05_datapagamento, 5, 2);
        
                                            if ($servidor->getAnoCompetencia() == (int) $anoRescisao
                                                && $servidor->getMesCompetencia() == (int) $mesRescisao) {
                                                    $rubrica->nomePagamento = self::RESCISAOENVIADA;
                                                    $enviar = self::TIPORESCISAOENVIADA;
                                            } else {
                                                $rubrica->nomePagamento = self::RESCISAOPOSTERIOR;
                                                $enviar = self::TIPORESCISAOPOSTERIOR;
                                            }
                                        } else {
                                            $rubrica->nomePagamento = self::RESCISAOPOSTERIOR;
                                            $enviar = self::TIPORESCISAOPOSTERIOR;
                                        }
                                        break;
                                    }
                                } else {
                                    // Caso a rescisao deva ser enviada no S1200 ao inves do S2299/S2399
                                    if (!$servidor->temVinculoEmpregaticio()) {
                                        if ($servidor->validacaoEnvioRescisaoEsocialComVinculo()) {
                                            $rubrica->nomePagamento = self::RESCISAOENVIADA;
                                            $enviar = self::TIPORESCISAOENVIADA;
                                        }
                                    } else {
                                        // Caso a rescisao deva ser enviada no S1202 ao inves do S2299/S2399
                                        if ($servidor->validacaoEnvioRescisaoEsocialSemVinculo()) {
                                            $rubrica->nomePagamento = self::RESCISAOENVIADA;
                                            $enviar = self::TIPORESCISAOENVIADA;
                                        }
                                    }
                                }
                            }
                        }
        
                        if (!empty($enviar)) {
                            $pagamentos[$enviar][] = $rubrica;
                        }
                    }
                }
            }
            if ($totalLotacao > 0) {
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
                foreach ($calculoPorLotacao as $keyLotacao => $lotacao) {
                    foreach ($calculoSalario as $eventoFinanceiro) {
                        $rubrica = new stdClass();
                        $rubrica->codigo = $eventoFinanceiro->getRubrica()->getCodigo();
                        $rubrica->descricao = $eventoFinanceiro->getRubrica()->getDescricao();
                        $rubrica->quantidade = $eventoFinanceiro->getQuantidade();
                        $valorFinanceiroSalario = $eventoFinanceiro->getValor();
                        $rubrica->valor = $valorFinanceiroSalario;
                        if ($lotacao->dias > 0) {
                            $rubrica->valor = round((float) $valorFinanceiroSalario*($lotacao->dias/30), 2);
                            if ($keyLotacao == $totalLotacao) {
                                $rubrica->valor = (float) $valorFinanceiroSalario
                                    -$valorParcialFinanceiroSalario[$rubrica->codigo][$keyLotacao-1];
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
                        $rubrica->descricaoTipo = $eventoFinanceiro->getRubrica()->getTipo() ==
                            EventoFinanceiroFolha::PROVENTO ? "Provento" : "Desconto";
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
                        $valorFinanceiroComplementar = $eventoFinanceiro->getValor();
                        $rubrica->valor = $valorFinanceiroComplementar;
                        if ($lotacao->dias > 0) {
                            $rubrica->valor = round((float) $valorFinanceiroComplementar*($lotacao->dias/30), 2);
                            if ($keyLotacao == $totalLotacao) {
                                $rubrica->valor = (float) $valorFinanceiroComplementar
                                    -$valorParcialFinanceiroComplementar[$rubrica->codigo][$keyLotacao-1];
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
                        $rubrica->descricaoTipo = $eventoFinanceiro->getRubrica()->getTipo() ==
                            EventoFinanceiroFolha::PROVENTO ? "Provento" : "Desconto";
                        $rubrica->decimoTerceiro = false;
                        $rubrica->nomePagamento = self::COMPLEMENTAR;
                        $rubrica->lotacao = $lotacao->lotacaoTributaria;
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
                            if ($keyLotacao == $totalLotacao) {
                                $rubrica->valor = (float) $valorFinanceiroDecimoTerceiro
                                    -$valorParcialFinanceiroDecimoTerceiro[$rubrica->codigo][$keyLotacao-1];
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
                        $rubrica->descricaoTipo = $eventoFinanceiro->getRubrica()->getTipo() ==
                            EventoFinanceiroFolha::PROVENTO ? "Provento" : "Desconto";
                        $rubrica->nomePagamento = self::DECIMO;
                        $rubrica->lotacao = $lotacao->lotacaoTributaria;
                        $pagamentos[self::TIPODECIMO][] = $rubrica;
                    }

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
                            $valorFinanceiroRescisao = $eventoFinanceiro->getValor();
                            $rubrica->valor = $valorFinanceiroRescisao;
                            if ($lotacao->dias > 0) {
                                $rubrica->valor = round((float) $valorFinanceiroRescisao*($lotacao->dias/30), 2);
                                if (empty($valorParcialFinanceiroRescisao[$rubrica->codigo])) {
                                    $valorParcialFinanceiroRescisao[$rubrica->codigo] = [];
                                    $valorParcialFinanceiroRescisao[$rubrica->codigo][] = 0.0;
                                }
    
                                if ($keyLotacao == $totalLotacao) {
                                    $rubrica->valor = (float) $valorFinanceiroRescisao
                                        -$valorParcialFinanceiroRescisao[$rubrica->codigo][$keyLotacao-1];
                                } else {
                                    $x = sizeof($valorParcialFinanceiroRescisao[$rubrica->codigo])-1;
                                    $y = $valorParcialFinanceiroRescisao[$rubrica->codigo][$x];
                                    $valorParcialFinanceiroRescisao[$rubrica->codigo][] = $y + $rubrica->valor;
                                }
                            }
                            $rubrica->tipo = $eventoFinanceiro->getRubrica()->getTipo();
                            $rubrica->decimoTerceiro = false;
                            $rubrica->descricaoTipo = $eventoFinanceiro->getRubrica()->getTipo() ==
                                EventoFinanceiroFolha::PROVENTO ? "Provento" : "Desconto";
                            $rubrica->nomePagamento = self::RESCISAO;
                            $enviar = '';
            
                            $compRescisao = $servidor->getCompetenciaPagamentoRecisao($servidor->getMatricula());
    
                            //Havendo cálculo rescisão e sua competencia
                            //for diferente da primeira será RESCISAOPOSTERIOR
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
                }
                $remuneracao = new Remuneracao();
                $pagamentos = $remuneracao->ajustaPagamentos($pagamentos);
            }
        }
        $this->remuneracaoRPPS->setPagamentos($pagamentos);
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
        $dadosTrabalhador->nascimento = $cgm->getDataNascimento();

        $this->remuneracaoRPPS->setDadosTrabalhador($dadosTrabalhador);
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

    public function retornaQuantidadeMatricula($matricula = null)
    {
        if (!empty($matricula)) {
            $sql = "
            with registro_cgm as (
                select
                    rh01_numcgm
                from
                    pessoal.rhpessoal
                where
                    rh01_regist = {$matricula} )
                select
                    count(*) as qtdServidores
                from
                    pessoal.rhpessoal
                where
                    rh01_numcgm in (
                    select
                        rh01_numcgm
                    from
                        registro_cgm ) ";
            $resultado = db_query($sql);
            if (pg_numrows($resultado) == 1) {
                return (int) \db_utils::fieldsMemory($resultado, 0)->qtdservidores;
            }
        }
        return 1;
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
}
