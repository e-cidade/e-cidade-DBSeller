<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use App\Domain\RecursosHumanos\Pessoal\Repository\Helper\CompetenciaHelper;
use CgmFisico;
use CgmJuridico;
use ECidade\RecursosHumanos\ESocial\Entity\RemuneracaoRPPS;
use ECidade\RecursosHumanos\ESocial\Repository\ESocialRubricasRepository;
use ECidade\RecursosHumanos\ESocial\Service\RemuneracaoRPPSService;
use ECidade\RecursosHumanos\Pessoal\Service\DataPagamentoFolhaService;
use Rubrica;
use RubricaRepository;
use stdClass;
use Exception;
use CgmRepository;
use ECidade\RecursosHumanos\ESocial\Service\DadosRRAService;
use ServidorRepository;
use DBDate;

/**
 * Class remuneracaoRPPSFormatter
 *
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 */
class RemuneracaoRPPSFormatter extends Formatter
{
    /**
     * @var null|remuneracaoRPPS
     */
    private $remuneracaoRPPS;

    /**
     * @var null|RemuneracaoRPPSService
     */
    private $remuneracaoRPPSService;

    private $inscricaoEmpregador;

    private $rubricas = [];

    private $anoCompetencia;

    private $mesCompetencia;

    private $codigoCategoria;

    private $isDecimoTerceiro = false;

    private $cgmNaoEnviado  = [];

    private $isUltimaParcelaDecimoTerceiro = false;

    private $matriculaAtual = null;

    /**
     * @var ESocialRubricasRepository
     */
    private $rubricasRepository;

    /**
     * @var array
     */
    private $rubricasValidas;

    /**
     * @var \Instituicao
     */
    private $instituicao;

    /**
     * @var CompetenciaHelper
     */
    private $competencia;

    /**
     * @var boolean
     */
    private $dadosComplementares = true;

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
     * informa se o cmg possui matricula com e sem vinculo simultaneamente
     * nos casos de varias matriculas do mesmo cgm
     */
    private $possuiVinculoNaoVinculo = false;

    /**
     * @var bool
     * Variavel de controle do grupo dados complementares
     */
    private $enviaDadosComplementares = true;

    /**
     * @var bool
     * Variável que define é rescisão.
     */
    private $isRescisao = false;

     /**
      * @var bool
      * Variável que retorna dados da remuneração
      */
    private $possuiMatriculas = false;


    /**
     * Realiza a formatação dos dados para envio da API
     *
     * @param  array $dados
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
        $this->rubricasValidas = $this->rubricasRepository->validarRubricas('1202');
        $this->instituicao = \InstituicaoRepository::getInstituicaoSessao();
        $this->possuiNaturezaSaude = false;
        $this->competencia = CompetenciaHelper::get($this->anoCompetencia, $this->mesCompetencia);
        $this->rubricaDiferenca = $dados->rubricaDiferenca;
        $dadosFormatados = [];

        foreach ($dados->cgms as $cgm) {
            $this->dadosComplementares = true;
            $cgmServidor = CgmRepository::getByCodigo($cgm);
            if ($cgmServidor instanceof CgmFisico) {
                $this->matriculaAtual = null;
                $dado = $this->buscarDadosECidade($cgm);
                if ($dado) {
                    $dadosFormatados[] = $dado;
                } else {
                    $this->cgmNaoEnviado[] = $cgm;
                }
            } else {
                $this->cgmNaoEnviado[] = $cgm;
            }
        }
        return $dadosFormatados;
    }

    /**
     * @param  $cgm
     * @return stdClass
     * @throws \BusinessException
     * @throws \DBException
     */
    private function buscarDadosECidade($cgm)
    {
        $anoCompetencia = $this->anoCompetencia;
        $mesCompetencia = $this->mesCompetencia;

        $this->cgmAtual = $cgm;
        $this->remuneracaoRPPSService = new RemuneracaoRPPSService($this->anoCompetencia, $this->mesCompetencia);
        $this->remuneracaoRPPSService->setAnoCompetencia($this->anoCompetencia);
        $this->remuneracaoRPPSService->setMesCompetencia($this->mesCompetencia);
        $this->validaVinculo();

        $dadoFormatado = new stdClass();
        $dadoFormatado->dmDev = array();
        $dadoFormatado->referencia = $cgm . '-' . $this->remuneracaoRPPSService->getAnoCompetencia()
        . str_pad($this->remuneracaoRPPSService->getMesCompetencia(), 2, '0', STR_PAD_LEFT);

        if ($this->isDecimoTerceiro) {
            $dadoFormatado->referencia .= '-2';
            $dadoFormatado->indApuracao = 2;
            $dadoFormatado->perApur = $this->remuneracaoRPPSService->getAnoCompetencia();
        } else {
            $dadoFormatado->indApuracao = 1;
            $dadoFormatado->referencia .= '-1';
            $dadoFormatado->perApur = $this->remuneracaoRPPSService->getAnoCompetencia() . "-"
            . $this->remuneracaoRPPSService->getMesCompetencia();
        }

        $dadoFormatado->inscricao_empregador = $this->inscricaoEmpregador;
        $remuneracoesRPPS = $this->remuneracaoRPPSService->buscarPorCGM(CgmRepository::getByCodigo($cgm));
        if (!empty($remuneracoesRPPS)) {
            /**
             * Caso exista servidores, o filtro envia somente a folha informada daquela matricula do cgm
             * ignorando outras matriculas daquele cgm
             */
            if (sizeof($this->servidores) > 0) {
                $remuneracoesRPPSTemp = [];
                foreach ($remuneracoesRPPS as $remuneracaoRPPS) {
                    foreach ($this->servidores as $servidor) {
                        if ($remuneracaoRPPS->getServidor()->getMatricula() == $servidor->getMatricula()) {
                            $remuneracoesRPPSTemp[] = $remuneracaoRPPS;
                            if ($servidor->temVinculoEmpregaticio()) {
                                $this->enviaDadosComplementares = false;
                            }
                        }
                    }
                }
                $remuneracoesRPPS = $remuneracoesRPPSTemp;
            }

            $indiceDmDev = 0;
            foreach ($remuneracoesRPPS as $index => $remuneracaoRPPS) {
                if (!$remuneracaoRPPS->getServidor()->isAtivo()) {
                    continue;
                }
                if (isset($remuneracaoRPPS->quantidadeServidor) && $remuneracaoRPPS->quantidadeServidor > 1) {
                    $this->possuiMatriculas = true;
                }
                $pagamentos = $remuneracaoRPPS->getPagamentos();
                // Na validacao de Rpps, caso nao seja rpps, efetuamos as seguintes validacoes
                if (!$remuneracaoRPPS->getServidor()->isRpps($anoCompetencia, $mesCompetencia)) {
                    // caso tenha o tipo de previdencia preenchido, ignoramos a matricula
                    if ($remuneracaoRPPS->getServidor()->getTipoPrevidencia($anoCompetencia, $mesCompetencia)) {
                        continue;
                    } else {
                        //Caso nao tenha tabela de previdencia, verificamos se o servidor é cedido
                        //Caso seja cedido, continuamos com o processamento, caso contrario (matricula vazia)
                        //ignoramos a matricula
                        $cedencia = new \Cedencia($remuneracaoRPPS->getServidor()->getMatricula());
                        if (empty($cedencia->getMatricula())) {
                            continue;
                        }
                    }
                }
                foreach ($pagamentos as $indexFolha => $tipoFolha) {
                    $this->remuneracaoRPPS = $remuneracaoRPPS;
                    $this->isRescisao = ($tipoFolha[0]->nomePagamento === 'RESCISAOPOSTERIOR');
                    $this->montarRemuneracaoPeriodoApuracao(
                        $dadoFormatado,
                        $dadoFormatado->indApuracao,
                        $indiceDmDev,
                        $tipoFolha[0]->nomePagamento,
                        $remuneracaoRPPS->getServidor()->getMatricula()
                    );
                    $this->organizarDadosServidor($dadoFormatado, $indexFolha, $indiceDmDev);
                    $this->organizarDadosTrabalhador($dadoFormatado);
                    $this->organizarDadosComplementares($dadoFormatado);
                    $this->organizarDadosPagamentos($dadoFormatado, $tipoFolha, $indexFolha, $indiceDmDev);

                    //SE remunOrgSuc for igual a 'S' remove a matricula conforme Layout
                    if (isset($dadoFormatado->dmDev[0]->infoPerAnt)
                        && $dadoFormatado->dmDev[0]->infoPerAnt->remunOrgSuc == 'S'
                    ) {
                        unset(
                            $dadoFormatado->dmDev[0]->infoPerAnt->idePeriodo[0]
                                ->ideEstab[0]->remunPerAnt[0]->matricula
                        );
                    }
                    //verifica se existem informacoes de pagamentos
                    $removeIndice = $this->validaRemocaoIndiceDmDev($dadoFormatado, $indiceDmDev);

                    if ($removeIndice) {
                        unset($dadoFormatado->dmDev[$indiceDmDev]);
                    } else {
                        $this->organizarDadosOutrosVinculos($dadoFormatado);

                        /**
                         * Validamos se existe remuneracao dentro do demonstrativo, caso contrario deletamos o
                         * demonstrativo
                         * ou se a geracao do arquivo ANUAL é diferente de 12
                         */
                        $removeIndice = null;
                        if ($removeIndice || ($this->isDecimoTerceiro && !$this->isUltimaParcelaDecimoTerceiro)
                        ) {
                            unset($dadoFormatado->dmDev[$indiceDmDev]);
                        }
                    }
                    $indiceDmDev += 1;
                }
            }
        }
        $dadoFormatado->dmDev = array_values($dadoFormatado->dmDev);
        //Validando se existe demonstrativo (dmDev), caso não exista ele não é enviado.
        if (empty($dadoFormatado->dmDev)) {
            $dadoFormatado = false;
        }
        return $dadoFormatado;
    }

    /**
     * @param  $dadoFormatado
     * @throws Exception
     */
    private function montarRemuneracaoPeriodoApuracao(
        &$dadoFormatado,
        $indApuracao = 1,
        $indice = 0,
        $tipoPagamento = '',
        $matricula = ''
    ) {
        $matriculaTemp = $matricula;

        if ($indApuracao == 1) {
            $indApuracao = "";
        }
        // pra nao gerar impactos em outros registros
        if (!$this->possuiVinculoNaoVinculo) {
            $matricula = '';
        }

        if ($this->possuiMatriculas) {
            $matricula = $matriculaTemp;
        }

        $adiantamento = false;
        $competenciaAno =  $this->remuneracaoRPPSService->getAnoCompetencia();
        // Montamos conforme o demonstrativo se e salario/adiantamento13/13
        if (empty($indApuracao)) {
            $sigla = 'SAL';
            switch ($tipoPagamento) {
                case RemuneracaoRPPSService::COMPLEMENTAR:
                    $sigla = 'COMP';
                    break;
                case RemuneracaoRPPSService::DECIMO:
                    $sigla = 'ADIANT13';
                    $adiantamento = true;
                    break;
                case RemuneracaoRPPSService::RESCISAOPOSTERIOR:
                    $sigla = 'RESPOS';
                    break;
                case RemuneracaoRPPSService::RESCISAOENVIADA:
                    $sigla = 'RESC';
                    break;
            }
            //Validamos se é salario ou adiantamento do decimo pela primeira rubrica da remuneracao atual
            $identificadorDemonstrativo = $matriculaTemp . $sigla . $competenciaAno . $this->remuneracaoRPPSService
                ->getMesCompetencia() . $indApuracao;
        } else {
            $identificadorDemonstrativo = $matriculaTemp . "DECIMO" . $competenciaAno . $this->remuneracaoRPPSService
                ->getMesCompetencia() . $indApuracao;
        }

        $dmDev = new stdClass();
        $dmDev->ideDmDev = $identificadorDemonstrativo;
        $dmDev->codCateg = $this->codigoCategoria = (int) $this->buscarCodigoCategoria();

        $processoJudicial = DadosRRAService::getDados(
            $this->remuneracaoRPPSService->getAnoCompetencia(),
            $this->remuneracaoRPPSService->getMesCompetencia(),
            $this->remuneracaoRPPS->getServidor()
        );

        if ($processoJudicial) {
            $dmDev->indRRA = 'S';
            $dmDev->infoRRA = $processoJudicial->infoRRA;
        }
        /**
         * Verificamos se o servidor possui 2 ou mais matriculas
         * sendo pelo menos 1 com vinculo e 1 sem vinculo ao mesmo tempo
         */

        // $this->organizarDadosComplementaresContrato($dmDev);

        if (count($this->remuneracaoRPPS->getPagamentos()) > 0) {
            $ideEstab = new stdClass();
            $ideEstab->tpInsc = 1;
            $ideEstab->nrInsc = $this->inscricaoEmpregador;
            if (!empty($this->remuneracaoRPPS->getServidor()->getLocalTrabalhoPrincial())
                && !empty($this->remuneracaoRPPS->getServidor()->getLocalTrabalhoPrincial()->getNumeroInscricao())
            ) {
                    $ideEstab->nrInsc = $this->remuneracaoRPPS
                        ->getServidor()
                        ->getLocalTrabalhoPrincial()
                        ->getNumeroInscricao();
            }
            if ($this->isRescisao) {
                $dmDev->infoPerAnt = new stdClass();
                $dmDev->infoPerAnt->remunOrgSuc = 'N';
                $dmDev->infoPerAnt->idePeriodo = [];
                $dmDev->infoPerAnt->idePeriodo[0] = [];
                $dmDev->infoPerAnt->idePeriodo[0]['ideEstab'] = [];
                $dataRecisao = (string) $this->remuneracaoRPPS->getServidor()->getDataRescisao()->format('Y-m-d');
                $competenciaRescisao = explode('-', $dataRecisao);
                $perRef = new stdClass();
                $perRef->perRef  = '';
                if (!empty($competenciaRescisao[1])) {
                    $perRef->perRef  = $competenciaRescisao[0] . '-' . $competenciaRescisao[1];
                }
                $dmDev->infoPerAnt->idePeriodo[0] = $perRef;
                $ideEstabInfoPerAnt = new stdClass();
                $ideEstabInfoPerAnt->tpInsc = 1;
                $ideEstabInfoPerAnt->nrInsc = $this->inscricaoEmpregador;
                $ideEstabInfoPerAnt->remunPerAnt = [];
                $dmDev->infoPerAnt->idePeriodo[0]->ideEstab[0] = $ideEstabInfoPerAnt;
            } else {
                $dmDev->infoPerApur = new stdClass();
                $dmDev->infoPerApur->ideEstab = [];
                $ideEstab->remunPerApur = [];
                $dmDev->infoPerApur->ideEstab[0] = $ideEstab;
            }
        }
        if (empty($dadoFormatado->dmDev[$indice])) {
            $dadoFormatado->dmDev[$indice] = $dmDev;
        }
    }

    private function montarOutrosVinculos(&$dadoFormatado)
    {
        $dadoFormatado->ideTrabalhador->infoMV = new stdClass();
        $dadoFormatado->ideTrabalhador->infoMV->indMV = null;
        $dadoFormatado->ideTrabalhador->infoMV->remunOutrEmpr = array();
    }

    /**
     * @param  $dadoFormatado
     * @param  $index
     * @throws \BusinessException
     */
    private function organizarDadosPagamentos(&$dadoFormatado, $folha, $index = 0, $indexDmDev = 0)
    {
        $itensRemun = [];
        $itensRemunDiferenca = [];

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
            $item->indApurIR = 0;
            $recisaoFalecimento = $this->remuneracaoRPPS->getServidor()->getDadosRescisao()->r59_motivoesocial;
            if ((int) $recisaoFalecimento == 10) {
                $item->indApurIR = 1;
            }

            if ($this->rubricaDiferenca == $codigoRubrica) {
                $itensRemunDiferenca[] = $item;
            } else {
                $itensRemun[] = $item;
            }
        }
        if ($this->isRescisao) {
            $dadoFormatado
                ->dmDev[$indexDmDev]
                ->infoPerAnt
                ->idePeriodo[0]
                ->ideEstab[0]
                ->remunPerAnt[$index]
                ->itensRemun = $itensRemun;
        } else {
            if (!empty($itensRemunDiferenca)) {
                if (!isset($dadoFormatado                    ->dmDev[$indexDmDev]                    ->infoPerAnt)
                ) {
                    $remunOrgSuc = 'N';
                    if ($this->remuneracaoRPPS->getServidor()->isRescindido()) {
                        $remunOrgSuc = 'S';
                    }
                    $idePeriodo = new stdClass();
                    $idePeriodo = $this->remuneracaoRPPSService->buscarPeriodoAnterior(
                        $this->remuneracaoRPPS->getServidor()->getMatricula()
                    );
                    $idePeriodo->ideEstab = [];
                    $dmDev = $dadoFormatado
                        ->dmDev[$indexDmDev];
                    $dmDev->infoPerAnt = new stdClass();
                    $dmDev->infoPerAnt->remunOrgSuc = $remunOrgSuc;
                    $dmDev->infoPerAnt->idePeriodo = [];
                    $dmDev->infoPerAnt->idePeriodo[0] = [];
                    $ideEstab = new stdClass();
                    $dmDev->infoPerAnt->idePeriodo[0] = $idePeriodo;
                    $dmDev->infoPerAnt->idePeriodo[0]->ideEstab[0] = '';
                    $ideEstabInfoPerAnt = new stdClass();
                    $ideEstabInfoPerAnt->tpInsc = 1;
                    $ideEstabInfoPerAnt->nrInsc = $this->inscricaoEmpregador;
                    $ideEstabInfoPerAnt->remunPerAnt = [];
                    $itensRemunValores = new stdClass();
                    if (!empty($this->matriculaAtual)) {
                        $itensRemunValores->matricula = $this->matriculaAtual;
                    }
                    $ideEstabInfoPerAnt->remunPerAnt[0] = $itensRemunValores;
                    $dmDev->infoPerAnt->idePeriodo[0]->ideEstab[0] = $ideEstabInfoPerAnt;
                    $dadoFormatado->dmDev[$indexDmDev] = $dmDev;
                }
                $dadoFormatado
                    ->dmDev[$indexDmDev]
                    ->infoPerAnt
                    ->idePeriodo[0]
                    ->ideEstab[0]
                    ->remunPerAnt[$index]
                    ->itensRemun = $itensRemunDiferenca;
            }
            $dadoFormatado
                ->dmDev[$indexDmDev]
                ->infoPerApur
                ->ideEstab[0]
                ->remunPerApur[$index]
                ->itensRemun = $itensRemun;
        }
    }

    private function organizarDadosServidor(&$dadoFormatado, $index, $indexDmDev = 0)
    {
        if ($this->isRescisao) {
            $remunPerAnt = new stdClass();

            if ($this->validaEnvioMatricula(
                $this->remuneracaoRPPS->getServidor()->getMatricula(),
                $this->getEmpregador()
            )
            ) {
                $remunPerAnt->matricula = $this->remuneracaoRPPS->getServidor()->getMatricula();
            }
            if ($this->remuneracaoRPPS->getServidor()->getMatriculaAnterior() != "") {
                $remunPerAnt->matricula = $this->remuneracaoRPPS->getServidor()->getMatriculaAnterior();
            }
            $remunPerAnt->itensRemun = [];
            $dadoFormatado->dmDev[$indexDmDev]->infoPerAnt->idePeriodo[0]->ideEstab[0]->remunPerAnt[$index] =
                $remunPerAnt;
        } else {
            $remunPerApur = new stdClass();
            if ($this->validaEnvioMatricula(
                $this->remuneracaoRPPS->getServidor()->getMatricula(),
                $this->getEmpregador()
            )
            ) {
                $remunPerApur->matricula = $this->remuneracaoRPPS->getServidor()->getMatricula();
            }
            if ($this->remuneracaoRPPS->getServidor()->getMatriculaAnterior() != "") {
                $remunPerApur->matricula = $this->remuneracaoRPPS->getServidor()->getMatriculaAnterior();
            }
            $this->matriculaAtual = $remunPerApur->matricula;
            $remunPerApur->itensRemun = [];
            $dadoFormatado->dmDev[$indexDmDev]->infoPerApur->ideEstab[0]->remunPerApur[$index] = $remunPerApur;
        }
    }

    private function organizarDadosOutrosVinculos(&$dadoFormatado)
    {

        foreach ($this->remuneracaoRPPS->getServidorOutrosVinculos() as $indice => $outroVinculo) {
            if ($indice == 0) {
                $this->montarOutrosVinculos($dadoFormatado);
            }

            $dadoFormatado->ideTrabalhador->infoMV->indMV = $outroVinculo->getTipoContribuicao();
            $remunOutrEmpr = new stdClass();
            $remunOutrEmpr->tpInsc = $outroVinculo->getTipoInscricao();
            $remunOutrEmpr->nrInsc = $outroVinculo->getNumeroInscricao();
            $remunOutrEmpr->codCateg = $outroVinculo->getCodigoCategoria();
            $remunOutrEmpr->vlrRemunOE = $outroVinculo->getValorRemuneracao();
            $dadoFormatado->ideTrabalhador->infoMV->remunOutrEmpr[] = $remunOutrEmpr;
        }
    }


    private function organizarDadosTrabalhador(&$dadoFormatado)
    {
        $dadoFormatado->ideTrabalhador = new stdClass();
        $dadoFormatado->ideTrabalhador->cpfTrab = $this->remuneracaoRPPS->getDadosTrabalhador()->cpf;
    }

    private function buscarCodigoCategoria()
    {
        return $this->remuneracaoRPPS->getServidor()->getVinculo()->getCodigoCategoria();
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
        //Se rescisão
        if ($this->isRescisao) {
            $dadoideEstab = $dadoFormatado->dmDev[$indiceDmDev]->infoPerAnt->idePeriodo[0]->ideEstab[0];
            //Caso nao exista ideEstab ou ideEstab = 0, remove o indice
            if (!isset($dadoideEstab)
                || (isset($dadoideEstab) && sizeof($dadoideEstab) == 0)
            ) {
                    $removeIndice = true;
            } else {
                // caso não exista remunPerApur ou remunPerApur = 0, remove o indice
                if (!isset($dadoideEstab->remunPerAnt)
                    || (isset($dadoideEstab->remunPerAnt) && sizeof($dadoideEstab->remunPerAnt) == 0)
                ) {
                    $removeIndice = true;
                } else {
                    // Caso não exista itensRemun ou itensRemun = 0, remove o indice
                    //Varremos o remunPerAnt
                    $remunPerAnts = $dadoideEstab->remunPerAnt;
                    foreach ($remunPerAnts as $remunPerAnt) {
                        if (!isset($remunPerAnt->itensRemun)
                            || isset($remunPerAnt->itensRemun) && (sizeof($remunPerAnt->itensRemun) == 0)
                        ) {
                                $removeIndice = true;
                        }
                    }
                }
            }
        } else {
            $dadoideEstab = $dadoFormatado->dmDev[$indiceDmDev]->infoPerApur->ideEstab[0];

            //Caso nao exista ideEstab ou ideEstab = 0, remove o indice
            if (!isset($dadoideEstab)
                || (isset($dadoideEstab) && (is_array($dadoideEstab) && sizeof($dadoideEstab) == 0))
            ) {
                    $removeIndice = true;
            } else {
                // caso não exista remunPerApur ou remunPerApur = 0, remove o indice
                if (!isset($dadoideEstab->remunPerApur)
                    || (isset($dadoideEstab->remunPerApur) && sizeof($dadoideEstab->remunPerApur) == 0)
                ) {
                    $removeIndice = true;
                } else {
                    // Caso não exista itensRemun ou itensRemun = 0, remove o indice
                    //Varremos o remunPerApur
                    $remunPerApurs = $dadoideEstab->remunPerApur;
                    foreach ($remunPerApurs as $remunPerApur) {
                        if (!isset($remunPerApur->itensRemun)
                            || isset($remunPerApur->itensRemun) && (sizeof($remunPerApur->itensRemun) == 0)
                        ) {
                            $removeIndice = true;
                        }
                    }
                }
            }
        }
        return $removeIndice;
    }

    /**
     * Validamos se o cgm não possui matriculas com vinculo
     * e no loop esta na matricula sem vinculo
     */
    private function organizarDadosComplementares(&$dadoFormatado)
    {
        /*
        * Regra MOS
        * 10.2.1. Tabela de categorias elegíveis para os eventos S-2190, S-2200 e S-2300
        * 11.1. Trabalhadores não incluídos no RET
        */
        $categoriaObrigatoriaS2300 = [201,
            202,
            304,
            305,
            308,
            401,
            410,
            721,
            722,
            723,
            731,
            734,
            738,
            761,
            771,
            901,
            902,
            906];

        /**
         * Validacao alterada, seguindo o manual do layout S1.2
         * Grupo obrigatorio quando servidor nao possuir cadastro no RET(eSocial)
         * Pela analise, é quando o servidor for desligado em competencias anteriores (pagamento Posterior)
         */
        if ($this->remuneracaoRPPS->getServidor()->isRescindido()) {
            $dataRecisao = $this->remuneracaoRPPS->getServidor()->getDadosRescisao()->rh05_datapagamento;
            if (!$dataRecisao) {
                $dataRecisao = $this->getDataPagamentoCompetencia();
            } else {
                $dataRecisao = new DBDate($dataRecisao);
            }
            $dataRecisao = new DBDate($dataRecisao);
            $ultimoDiaCompetencia = DBDate::getQuantidadeDiasMes($this->mesCompetencia, $this->anoCompetencia);
            $dataCompetencia = new DBDate("{$this->anoCompetencia}-{$this->mesCompetencia}-{$ultimoDiaCompetencia}");
            if ($dataRecisao <= $dataCompetencia) {
                if (($dataRecisao->getMes() == $dataCompetencia->getMes())
                    && ($dataRecisao->getAno() == $dataCompetencia->getAno())
                ) {
                    $this->dadosComplementares = false;
                } else {
                    $this->dadosComplementares = false;
                }
            } else {
                $this->dadosComplementares = false;
            }
        } else {
            $this->dadosComplementares = false;
        }

        if ($this->dadosComplementares) {
            $infoComplem = new stdClass();
            $infoComplem->nmTrab = $this->remuneracaoRPPS->getDadosTrabalhador()->nome;
            $infoComplem->dtNascto = $this->remuneracaoRPPS->getDadosTrabalhador()->nascimento;
            $dadoFormatado->ideTrabalhador->infoComplem = $infoComplem;
        }
    }

    /**
     * Validamos se o cgm possui + de 1 matricula sendo pelo menos 1 com vinculo e 1 sem vinculo simultaneamente
     * e no loop esta na matricula sem vinculo
     */
    private function organizarDadosComplementaresContrato(&$dmDev)
    {
        if ($this->possuiVinculoNaoVinculo && !$this->remuneracaoRPPS->getServidor()->temVinculoEmpregaticio()) {
            $infoComplCont = new stdClass;
            $infoComplCont->codCBO = $this->remuneracaoRPPS->getServidor()->getDadosCargo()->rh37_cbo;
            $infoComplCont->natAtividade = 1; // rural
            $infoComplCont->qtdDiasTrab = null;
            $dmDev->infoComplCont = $infoComplCont;
        }
    }

    /**
     * Validamos se o cgm possui + de 1 matricula sendo pelo menos 1 com vinculo e 1 sem vinculo simultaneamente
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

    private function getDataPagamentoCompetencia()
    {
        $pagamentoService = new DataPagamentoFolhaService();
        $parametros = new stdClass();
        $parametros->instituicao = $this->instituicao->getSequencial();
        $parametros->ano = $this->anoCompetencia;
        $parametros->mes = $this->mesCompetencia;
        $dataPagamento = $pagamentoService->buscarDataPagamentoInstituicaoCompetencia($parametros);
        return $dataPagamento[0]->getDataPagamento();
    }
}
