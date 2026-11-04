<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use App\Domain\RecursosHumanos\Pessoal\Repository\Helper\CompetenciaHelper;
use ECidade\RecursosHumanos\ESocial\Repository\ESocialRubricasRepository;
use ECidade\RecursosHumanos\ESocial\Service\RemuneracaoBeneficioService;
use Rubrica;
use RubricaRepository;
use stdClass;
use Exception;
use CgmRepository;
use ServidorRepository;
use ECidade\RecursosHumanos\ESocial\Service\DadosRRAService;

/**
 * Class RemuneracaoBeneficioEntePublico
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 */
class RemuneracaoBeneficioEntePublicoFormatter extends Formatter
{
    /**
     * @var null|RemuneracaoBeneficioEntePublico
     */
    private $remuneracao;

    /**
     * @var null|RemuneracaoService
     */
    private $remuneracaoService;

    private $inscricaoEmpregador;

    private $rubricas = array();

    private $anoCompetencia;

    private $mesCompetencia;

    private $codigoCategoria;

    private $isDecimoTerceiro = false;

    private $cgmNaoEnviado  = [];

    private $isUltimaParcelaDecimoTerceiro = false;

    private $rescisao = false;
    private $periodoAnterior = false;

    /**
     * @var ESocialRubricasRepository
     */
    private $rubricasRepository;

    /**
     * @var array
     */
    private $rubricasValidas;

    /**
     * @var boolean
     */
    private $possuiNaturezaSaude;

    /**
     * @var \Instituicao
     */
    private $instituicao;

    /**
     * @var CompetenciaHelper
     */
    private $competencia;

    /**
     * @var [] Servidor
     */
    private $servidores = [];

    /**
     * @return mixed
     */
    public function getServidores()
    {
        return $this->servidores;
    }

    /**
     * @param mixed $servidores
     */
    public function setServidores($servidores)
    {
        $this->servidores = $servidores;
    }

    /**
     * @var int
     */
    private $cgmAtual;

    /**
     * @var bool
     * informa se o cmg possui mais de 1 matricula simultaneamente
     * nos casos de varias matriculas do mesmo cgm
     */
    private $possuiVariasMatriculas = false;

    /**
     * @var bool
     * Variavel de controle do grupo dados complementares
     */
    private $enviaDadosComplementares = true;

    /**
     * Realiza a formatação dos dados para envio da API
     *
     * @param array $dados
     * @return array|stdClass[]
     * @throws \BusinessException
     * @throws \DBException
     */
    public function formatar($dados)
    {
        $dados = (object) $dados;
        $this->inscricaoEmpregador = $dados->inscricao_empregador;
        $this->anoCompetencia = $dados->anoCompetencia;
        $this->mesCompetencia = $dados->mesCompetencia;
        $this->rubricasRepository = new ESocialRubricasRepository();
        $this->rubricasValidas = $this->rubricasRepository->validarRubricas('1207');
        $this->instituicao = \InstituicaoRepository::getInstituicaoSessao();
        $this->possuiNaturezaSaude = false;
        $this->competencia = CompetenciaHelper::get($this->anoCompetencia, $this->mesCompetencia);
        $this->rubricaDiferenca = $dados->rubricaDiferenca;

        $dadosFormatados = array();

        foreach ($dados->cgms as $cgm) {
            $cgmServidor = CgmRepository::getByCodigo($cgm);
            if ($cgmServidor instanceof \CgmFisico) {
                $dado = $this->buscarDadosECidade($cgm);
                if ($dado) {
                    $dadosFormatados[] = $dado;
                }
            }
        }
        return $dadosFormatados;
    }

    /**
     * @param $cgm
     * @return stdClass
     * @throws \BusinessException
     * @throws \DBException
     */
    private function buscarDadosECidade($cgm)
    {
        $this->periodoAnterior = false;
        $this->cgmAtual = $cgm;
        $this->remuneracaoService = new RemuneracaoBeneficioService($this->anoCompetencia, $this->mesCompetencia);

        $dadoFormatado = new stdClass();
        $dadoFormatado->dmDev = array();
        $dadoFormatado->referencia = $cgm . '_' . $this->remuneracaoService->getAnoCompetencia()
            . str_pad($this->remuneracaoService->getMesCompetencia(), 2, '0', STR_PAD_LEFT)
            . '_';

        if ($this->isDecimoTerceiro) {
            $dadoFormatado->referencia .= '2';
            $dadoFormatado->indApuracao = 2;
            $dadoFormatado->perApur = $this->remuneracaoService->getAnoCompetencia();
        } else {
            $dadoFormatado->indApuracao = 1;
            $dadoFormatado->referencia .= '1';
            $dadoFormatado->perApur = $this->remuneracaoService->getAnoCompetencia() . "-"
                . $this->remuneracaoService->getMesCompetencia();
        }

        $dadoFormatado->inscricao_empregador = $this->inscricaoEmpregador;
        $cgmRemuneracao = CgmRepository::getByCodigo($cgm);

        $remuneracoes = false;
        if ($cgmRemuneracao instanceof \CgmFisico) {
            $remuneracoes = $this->remuneracaoService->buscarPorCGM($cgmRemuneracao);
        }
        // Caso nao tenha remuneracao no mes, retorna false
        if (empty($remuneracoes)) {
            $retorno = ["cgm" => $cgm, "motivo" => "Sem Remuneração"];
            $this->cgmNaoEnviado[] = $retorno;
            return false;
        }
        $dadoFormatado->ideBenef = new \stdClass();
        $dadoFormatado->ideBenef->cpfBenef = $remuneracoes[0]->getServidor()->getCgm()->getCpf();

        $indiceDmDev = 0;
        foreach ($remuneracoes as $index => $remuneracao) {
            $this->possuiVariasMatriculas = false;
            if ($remuneracao->qtdServidores > 1) {
                $this->possuiVariasMatriculas = true;
            }
            $pagamentos = $remuneracao->getPagamentos();
            foreach ($pagamentos as $indexFolha => $tipoFolha) {
                $this->remuneracao = $remuneracao;
                $this->rescisao = $tipoFolha[0]->periodoAnterior;

                $this->montarRemuneracaoPeriodoApuracao(
                    $dadoFormatado,
                    $dadoFormatado->indApuracao,
                    $indiceDmDev,
                    $tipoFolha[0]->nomePagamento,
                    $remuneracao->getServidor()->getMatricula()
                );
                $this->organizarDadosPagamentos($dadoFormatado, $tipoFolha, $indexFolha, $indiceDmDev);
                //verifica se existem informacoes de pagamentos
                $removeIndice = $this->validaRemocaoIndiceDmDev($dadoFormatado, $indiceDmDev);
                if ($removeIndice) {
                    unset($dadoFormatado->dmDev[$indiceDmDev]);
                } else {
                    /**
                     * Validamos se existe remuneracao dentro do demonstrativo, caso contrario deletamos o demonstrativo
                     * ou se a geracao do arquivo ANUAL é diferente de 12
                     */
                    $removeIndice = $this->validaRemocaoIndiceDmDev($dadoFormatado, $indiceDmDev);
                    if ($removeIndice || ($this->isDecimoTerceiro && !$this->isUltimaParcelaDecimoTerceiro)
                    ) {
                        unset($dadoFormatado->dmDev[$indiceDmDev]);
                    }
                }
                $indiceDmDev += 1;
            }
        }
        $dadoFormatado->dmDev = array_values($dadoFormatado->dmDev);
        //Validando se existe demonstrativo (dmDev), caso não exista ele não é enviado.
        if (empty($dadoFormatado->dmDev) && sizeof($dadoFormatado->dmDev) == 0) {
            $retorno = ["cgm" => $cgm, "motivo" => "Sem demonstrativo, possivel caso a se verificar."];
            $this->cgmNaoEnviado[] = $retorno;
            return false;
        }
        return $dadoFormatado;
    }

    /**
     * @param $dadoFormatado
     * @throws Exception
     */
    private function montarRemuneracaoPeriodoApuracao(
        &$dadoFormatado,
        $indApuracao = 1,
        $indice = 0,
        $tipoPagamento = '',
        $matricula = ''
    ) {
        if ($indApuracao == 1) {
            $indApuracao = "";
        }
        $nrBeneficio = $matricula;

        $adiantamento = false;
        // Montamos conforme o demonstrativo se e salario/adiantamento13/13/rescisao
        if (empty($indApuracao)) {
            $sigla = 'SAL';
            switch ($tipoPagamento) {
                case RemuneracaoBeneficioService::COMPLEMENTAR:
                    $sigla = 'COMP';
                    break;
                case RemuneracaoBeneficioService::RESCISAO:
                    $sigla = 'RESC';
                    break;
                case RemuneracaoBeneficioService::RESCISAOPOSTERIOR:
                    $sigla = 'RESPOS';
                    if ($this->rescisao) {
                        $this->periodoAnterior = $this->geraPerApurAnt($nrBeneficio);
                    }
                    break;
                case RemuneracaoBeneficioService::DECIMO:
                    $sigla = 'ADIANT13';
                    $adiantamento = true;
                    break;
            }
            //Validamos se é salario ou adiantamento do decimo pela primeira rubrica da remuneracao atual
            $identificadorDemonstrativo = $matricula . $sigla . $this->remuneracaoService->getAnoCompetencia()
                . $this->remuneracaoService->getMesCompetencia() . $indApuracao;
        } else {
            $identificadorDemonstrativo = $matricula . "DECIMO" . $this->remuneracaoService->getAnoCompetencia()
                . $this->remuneracaoService->getMesCompetencia() . $indApuracao;
        }

        $dmDev = new stdClass();
        $dmDev->ideDmDev = $identificadorDemonstrativo;
        $dmDev->nrBeneficio = $nrBeneficio;

        /**
         * Verificamos se o servidor possui processo RRA
         */
        $processoJudicial = DadosRRAService::getDados(
            $this->remuneracaoService->getAnoCompetencia(),
            $this->remuneracaoService->getMesCompetencia(),
            $this->remuneracao->getServidor()
        );

        if ($processoJudicial) {
            $dmDev->indRRA = 'S';
            $dmDev->infoRRA = $processoJudicial->infoRRA;
        }

        if ($this->remuneracao->getServidor()->getMatriculaAnterior() != "") {
            $dmDev->nrBeneficio = $this->remuneracao->getServidor()->getMatriculaAnterior();
        }
        if (count($this->remuneracao->getPagamentos()) > 0) {
            $ideEstab = new stdClass();
            $ideEstab->tpInsc = 1;
            $ideEstab->nrInsc = $this->inscricaoEmpregador;
            if (!empty($this->remuneracao->getServidor()->getLocalTrabalhoPrincial()) &&
                !empty($this->remuneracao->getServidor()->getLocalTrabalhoPrincial()->getNumeroInscricao())) {
                $ideEstab->nrInsc = $this->remuneracao
                    ->getServidor()
                    ->getLocalTrabalhoPrincial()
                    ->getNumeroInscricao();
            }
            $ideEstab->itensRemun = [];

            if (!$this->rescisao) {
                $dmDev->infoPerApur = new stdClass();
                $dmDev->infoPerApur->ideEstab = [];
                $dmDev->infoPerApur->ideEstab[0] = $ideEstab;
            } else {
                $dmDev->infoPerAnt = new \stdClass();
                $dmDev->infoPerAnt->idePeriodo = [];
                $idePeriodo = new \stdClass();
                $idePeriodo->perRef = $this->periodoAnterior;
                $idePeriodo->ideEstab[0] = $ideEstab;
                $dmDev->infoPerAnt->idePeriodo[] = $idePeriodo;
            }
        }
        if (empty($dadoFormatado->dmDev[$indice])) {
            $dadoFormatado->dmDev[$indice] = $dmDev;
        }
    }

    /**
     * @param $dadoFormatado
     * @param $index
     * @throws \BusinessException
     */
    private function organizarDadosPagamentos(&$dadoFormatado, $folha, $index = 0, $indexDmDev = 0)
    {
        $itensRemun = [];
        foreach ($folha as $pagamento) {
            $item = new stdClass();
            $item->codRubr = $pagamento->codigo;
            if (!array_key_exists($pagamento->codigo, $this->rubricasValidas)) {
                continue;
            }
            /**
             * Quando selecionado arquivo anual, sera enviado somente os dados do mes 12 e rubricas de 13
             * Quando for outro mes diferente do mes 12, as rubricas de 13 vao no mesmo arquivo do salario,
             * separadas pelo demonstrativo DmDev
             */
            if ($this->isUltimaParcelaDecimoTerceiro) {
                if ($this->isDecimoTerceiro) {
                    if (!$pagamento->decimoTerceiro) {
                        continue;
                    }
                } else {
                    if ($pagamento->decimoTerceiro) {
                        continue;
                    }
                }
            }
            $rubricaSistema = RubricaRepository::getInstanciaByCodigo(
                $pagamento->codigo,
                $this->instituicao->getCodigo()
            );
            if ($rubricaSistema->getTipo() == Rubrica::TIPO_BASE) {
                continue;
            }

            $rubrica = $this->rubricasValidas[$pagamento->codigo];

            if ($rubrica->natrubr == '9219') {
                $this->possuiNaturezaSaude = true;
            }
            $codigoRubrica = $pagamento->codigo;
            $tabrubrica = $rubrica->idetabrubr;

            $this->rubricas[$codigoRubrica] = $tabrubrica;

            $item->ideTabRubr = $this->rubricas[$codigoRubrica];
            $item->codRubr = $codigoRubrica;
            $item->qtdRubr = $this->truncar((float) $pagamento->quantidade);
            $item->vrRubr = $this->truncar((float) $pagamento->valor);
            if (($this->anoCompetencia >= 2021 && $this->mesCompetencia >= 7)
                or ($this->anoCompetencia >= 2021 && $dadoFormatado->indApuracao == 2)
                or ($this->anoCompetencia >= 2022)
            ) {
                $item->indApurIR = 0;
            }
            $recisaoFalecimento = $this->remuneracao->getServidor()->getDadosRescisao()->r59_motivoesocial;
            if ((int) $recisaoFalecimento == 10) {
                $item->indApurIR = 1;
            }

            if ($this->rubricaDiferenca == $codigoRubrica) {
                $itensRemunDiferenca[] = $item;
            } else {
                $itensRemun[] = $item;
            }
        }
        if (!$this->periodoAnterior) {
            $dadoFormatado->dmDev[$indexDmDev]->infoPerApur->ideEstab[0]->itensRemun = $itensRemun;
            if (!empty($itensRemunDiferenca)) {
                if (!isset($dadoFormatado
                            ->dmDev[$indexDmDev]
                            ->infoPerAnt)) {
                    $ideEstab = new stdClass();
                    $ideEstab->tpInsc = 1;
                    $ideEstab->nrInsc = $this->inscricaoEmpregador;
                    $ideEstab->itensRemun = [];
                    $dmDev = $dadoFormatado
                        ->dmDev[$indexDmDev];
                    $dmDev->infoPerAnt = new \stdClass();
                    $dmDev->infoPerAnt->idePeriodo = [];
                    $idePeriodo = new \stdClass();
                    $idePeriodo->perRef = $this->anoCompetencia . '-' . $this->mesCompetencia;
                    $idePeriodo->ideEstab[0] = $ideEstab;
                    $dmDev->infoPerAnt->idePeriodo[] = $idePeriodo;
                }
                $dadoFormatado
                    ->dmDev[$indexDmDev]
                    ->infoPerAnt
                    ->idePeriodo[0]
                    ->ideEstab[0]
                    ->itensRemun = $itensRemunDiferenca;
            }
        } else {
            $dadoFormatado->dmDev[$indexDmDev]->infoPerAnt->idePeriodo[0]->ideEstab[0]->itensRemun = $itensRemun;
        }
    }

    public function setDecimoTerceiro()
    {
        $this->isDecimoTerceiro = true;
    }

    public function setUltimaParcelaDecimoTerceiro()
    {
        $this->isUltimaParcelaDecimoTerceiro = true;
    }

    public function getCgmsNaoEnviados()
    {
        return $this->cgmNaoEnviado;
    }

    // Função responsavel por validar se o indice será removido dos dados
    private function validaRemocaoIndiceDmDev($dadoFormatado, $indiceDmDev)
    {
        $removeIndice = false;
        if (!$this->rescisao) {
            //Caso nao exista ideEstab ou ideEstab = 0, remove o indice
            if (!isset($dadoFormatado
                        ->dmDev[$indiceDmDev]
                        ->infoPerApur
                        ->ideEstab)
                || (isset($dadoFormatado
                            ->dmDev[$indiceDmDev]
                            ->infoPerApur
                            ->ideEstab) && sizeof($dadoFormatado
                        ->dmDev[$indiceDmDev]
                        ->infoPerApur
                        ->ideEstab) == 0)) {
                $removeIndice = true;
            } else {
                // caso não exista itensRemun ou itensRemun = 0, remove o indice
                if (!isset($dadoFormatado
                            ->dmDev[$indiceDmDev]
                            ->infoPerApur
                            ->ideEstab[0]->itensRemun)
                    || (isset($dadoFormatado
                                ->dmDev[$indiceDmDev]
                                ->infoPerApur
                                ->ideEstab[0]->itensRemun) && sizeof($dadoFormatado
                            ->dmDev[$indiceDmDev]
                            ->infoPerApur
                            ->ideEstab[0]->itensRemun) == 0)) {
                    $removeIndice = true;
                } else {
                    // Caso não exista itensRemun ou itensRemun = 0, remove o indice
                    //Varremos o itensRemun
                    $itensRemun = $dadoFormatado->dmDev[$indiceDmDev]->infoPerApur->ideEstab[0]->itensRemun;

                    if (!is_array($itensRemun) or sizeof($itensRemun) == 0) {
                        $removeIndice = true;
                    }
                }
            }
        } else {
            //Caso nao exista ideEstab ou ideEstab = 0, remove o indice
            if (!isset($dadoFormatado
                        ->dmDev[$indiceDmDev]
                        ->infoPerAnt
                        ->idePeriodo[0]
                        ->ideEstab)
                || (isset($dadoFormatado
                            ->dmDev[$indiceDmDev]
                            ->infoPerAnt
                            ->idePeriodo[0]
                            ->ideEstab) && sizeof($dadoFormatado
                        ->dmDev[$indiceDmDev]
                        ->infoPerAnt
                        ->idePeriodo[0]
                        ->ideEstab) == 0)) {
                $removeIndice = true;
            } else {
                // caso não exista remunPerApur ou remunPerApur = 0, remove o indice
                if (!isset($dadoFormatado
                            ->dmDev[$indiceDmDev]
                            ->infoPerAnt
                            ->idePeriodo[0]
                            ->ideEstab[0]->itensRemun)
                    || (isset($dadoFormatado
                                ->dmDev[$indiceDmDev]
                                ->infoPerAnt
                                ->idePeriodo[0]
                                ->ideEstab[0]->itensRemun) && sizeof($dadoFormatado
                            ->dmDev[$indiceDmDev]
                            ->infoPerAnt
                            ->idePeriodo[0]
                            ->ideEstab[0]->itensRemun) == 0)) {
                    $removeIndice = true;
                } else {
                    // Caso não exista itensRemun ou itensRemun = 0, remove o indice
                    //Varremos o itensRemun
                    $itensRemun = $dadoFormatado->dmDev[$indiceDmDev]->infoPerAnt->idePeriodo[0]->ideEstab[0]
                        ->itensRemun;
                    if (!is_array($itensRemun) or sizeof($itensRemun) == 0) {
                        $removeIndice = true;
                    }
                }
            }
        }

        return $removeIndice;
    }

    public function truncar($valor)
    {
        $valor = abs(round($valor, 6));
        $novoValor = (string) $valor;
        $novoValor = explode(".", $novoValor);
        if (sizeof($novoValor) > 1) {
            if (strlen($novoValor[1]) > 2) {
                $novoValor[1] = substr($novoValor[1], 0, 2);
                $valor = (float)($novoValor[0] . "." . $novoValor[1]);
            }
        }
        return $valor;
    }

    private function ajustarPeriodos(&$dadoFormatado)
    {
        foreach ($dadoFormatado->dmDev as &$dmDev) {
            $apurAnt = $this->geraPerApurAnt($dmDev->nrBeneficio);
            // teste do perAnt
            //$apurAnt = $this->geraPerApurAnt(746251);
            if ($apurAnt) {
                $dmDev->infoPerAnt = new \stdClass();
                $dmDev->infoPerAnt->idePeriodo = [];
                $idePeriodo = new \stdClass();
                $idePeriodo->perRef = $apurAnt;
                $this->ajusteItens($dmDev->infoPerApur->ideEstab);
                $idePeriodo->ideEstab = $dmDev->infoPerApur->ideEstab;
                $dmDev->infoPerAnt->idePeriodo[] = $idePeriodo;
                unset($dmDev->infoPerApur);
            } else {
                $this->ajusteItens($dmDev->infoPerApur->ideEstab);
            }
        }
        return $dadoFormatado;
    }

    private function ajusteItens(&$ideEstabs)
    {
        foreach ($ideEstabs as &$ideEstab) {
            $itensRemun = [];
            foreach ($ideEstab->remunPerApur as &$remunPerApur) {
                foreach ($remunPerApur->itensRemun as &$itenRemun) {
                    $itensRemun[] = $itenRemun;
                }
            }
            unset($ideEstab->remunPerApur);
            $ideEstab->itensRemun = $itensRemun;
        }
    }

    private function geraPerApurAnt($matricula)
    {
        $apurAnt = false;
        $servidor = ServidorRepository::getInstanciaByCodigo($matricula);
        if ($servidor->isRescindido()) {
            /**
             *  Validar com a analista o periodo de referencia
             *  enquanto isso esta sendo setado o periodo da propria rescisao
             */
            $dataRescisao = explode('-', $servidor->getDadosRescisao()->rh05_recis);
            $apurAnt = "{$dataRescisao[0]}-{$dataRescisao[1]}";
        }
        return $apurAnt;
    }


    /**
     * Validamos se o cgm possui + de 1 matricula
     */
    private function validaVinculo()
    {
        $cgm = CgmRepository::getByCodigo($this->cgmAtual);
        if (ServidorRepository::isServidorComESemVinculoByCgm($cgm, $this->competencia)) {
            $this->possuiVinculoNaoVinculo = true;
        } else {
            $this->possuiVinculoNaoVinculo = false;
        }
    }
}
