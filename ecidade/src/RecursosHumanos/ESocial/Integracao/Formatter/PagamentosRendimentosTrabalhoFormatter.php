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
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use DBCompetencia;
use ECidade\RecursosHumanos\ESocial\Entity\PagamentosRendimentosTrabalho as Pagamento;
use ECidade\RecursosHumanos\ESocial\Repository\ESocialRubricasRepository;
use ECidade\RecursosHumanos\ESocial\Repository\PagamentosRendimentosTrabalho as PagamentosRendTrabalhoRepository;
use ECidade\RecursosHumanos\ESocial\Service\PagamentosRendimentosTrabalhoService;
use stdClass;

class PagamentosRendimentosTrabalhoFormatter extends Formatter
{
    /**
     * @var PagamentosRendimentosTrabalhoService
     */
    private $pagamentosService;
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
    private $anoCompetenciaCaixa;
    /**
     * @var int
     */
    private $mesCompetenciaCaixa;
    /**
     * @var string
     */
    private $inscricaoEmpregador;
    /**
     * @var string
     */
    private $rubricaPensaoAlimenticia;
    /**
     *
     */
    private $rubricas;

    /**
     * @var string
     */
    private $perApur;

    private $isDecimoTerceiro = false;

    /**
     * @var [Servidor]
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
     * @var bool
     */
    private $processaMes12 = false;

    /**
     * @var bool
     */
    private $processa13 = false;

    /**
     * @param mixed $servidores
     */
    public function setServidores($servidores)
    {
        $this->servidores = $servidores;
    }

    private $cgmNaoIncluidos = [];

    /**
     * @param array $dados
     * @return array|stdClass[]
     * @throws \DBException
     */
    public function formatar($dados)
    {
        $dados = (object)$dados;
        $this->inscricaoEmpregador = $dados->inscricao_empregador;
        $this->anoCompetencia = $dados->anoCompetencia;
        $this->mesCompetencia = $dados->mesCompetencia;
        $this->anoCompetenciaCaixa = $dados->anoCompetencia;
        $this->mesCompetenciaCaixa = $dados->mesCompetencia;
        $this->rubricaPensaoAlimenticia = PagamentosRendTrabalhoRepository::buscarParametroRubricaPensaoAlimenticia(
            new DBCompetencia(
                $this->anoCompetencia,
                $this->mesCompetencia
            )
        );

        $rubricasEsocialRepository = new ESocialRubricasRepository();
        $this->rubricas = $rubricasEsocialRepository->validarRubricas('1210');
        $dadosFormatados = array();
        foreach ($dados->cgms as $dado) {
            if ($dado->cgm instanceof \CgmFisico) {
                $dadoFormatado = $this->dadosECidade($dado);
                if (!empty($dadoFormatado)) {
                    if (isset($dadoFormatado->ideBenef->infoPgto) && sizeof($dadoFormatado->ideBenef->infoPgto) > 0) {
                        $dadosFormatados[] = $dadoFormatado;
                    } else {
                        $this->cgmNaoIncluidos[] = ["motivo" => "Sem pagamento processado no evento", "cgm" => $dado];
                    }
                } else {
                    $this->cgmNaoIncluidos[] = ["motivo" => "Não Processado", "cgm" => $dado];
                }
            } else {
                $this->cgmNaoIncluidos[] = ["motivo" => "CGM Juridico", "cgm" => $dado];
            }
        }
        return $dadosFormatados;
    }

    /**
     * Busca os dados de pagamentos do CGM e retornamos formatados.
     * @param $cgm
     * @return stdClass|null
     * @throws \BusinessException
     * @throws \DBException
     * @throws \ParameterException
     */
    private function dadosECidade($dado)
    {
        $this->perApur = "{$this->anoCompetencia}-{$this->mesCompetencia}";
        $this->pagamentosService = new PagamentosRendimentosTrabalhoService();
        $this->pagamentosService->setRubricasValidas($this->rubricas);
        $this->pagamentosService->setProcessa13($this->processa13);
        $this->pagamentosService->setProcessaMes12($this->processaMes12);

        if ($this->isDecimoTerceiro) {
            $this->pagamentosService->setDecimoTerceiro();
        }
        $pagamentosRendimentosTrabalho = [];
        foreach ($dado->competencias as $competencia) {
            $this->pagamentosService->setAnoCompetencia($competencia->ano);
            $this->pagamentosService->setMesCompetencia($competencia->mes);
            $this->pagamentosService->setAnoCompetenciaCaixa($this->anoCompetenciaCaixa);
            $this->pagamentosService->setMesCompetenciaCaixa($this->mesCompetenciaCaixa);

            $pagamentosRendimentosTrabalho[] = $this->pagamentosService->buscarPorCGM(
                $dado->cgm,
                $this->servidores
            );
        }

        if (empty($pagamentosRendimentosTrabalho)) {
            return null;
        }

        $dadoFormatado = new stdClass();
        $dadoFormatado->referencia = "{$dado->cgm->getCodigo()}_"
            . "{$this->anoCompetencia}{$this->mesCompetencia}";
        $dadoFormatado->inscricao_empregador = $this->inscricaoEmpregador;
        $dadoFormatado->ideBenef = new stdClass();
        foreach ($pagamentosRendimentosTrabalho as $pagamento) {
            if (!empty($pagamento)) {
                $dadoFormatado->ideBenef->cpfBenef = $pagamento->getCPFBeneficiente();
                break;
            }
        }
        foreach ($pagamentosRendimentosTrabalho as $pagamento) {
            if ($pagamento instanceof Pagamento) {
                foreach ($pagamento->getPagamentos() as $valor) {
                    $dadoFormatado->ideBenef->infoPgto[] = $valor;
                }
                //Informações relacionadas à retenção na fonte, aos rendimentos tributáveis e não tributáveis,
                //deduções e/ou isenções, etc., de acordo com a legislação aplicada ao imposto de renda.
                if (!empty($pagamento->getDataLaudoMolestia())) {
                    $dataLaudoMolestia = $pagamento->getDataLaudoMolestia()->format('Y-m-d');
                    if (isset($dataLaudoMolestia) && !empty($dataLaudoMolestia)) {
                        if (!isset($dadoFormatado->ideBenef->infoIRComplem)) {
                            $dadoFormatado->ideBenef->infoIRComplem = new stdClass();
                        }
                        $dadoFormatado->ideBenef->infoIRComplem->dtLaudo = $dataLaudoMolestia;
                    }
                }
                //Plano de saúde coletivo.
                $planosSaude = $pagamento->getPlanoSaudeColetivo();
                if ($planosSaude && is_array($planosSaude)) {
                    foreach ($planosSaude as $indicePlanoSaude => $planoSaude) {
                        if ($planoSaude->vlrSaudeTit == 0) {
                            continue;
                        }
                        if (!isset($dadoFormatado->ideBenef->infoIRComplem)) {
                            $dadoFormatado->ideBenef->infoIRComplem = new stdClass();
                        }
                        $dadoFormatado
                            ->ideBenef
                            ->infoIRComplem
                            ->planSaude[$indicePlanoSaude] = new stdClass();
                        $dadoFormatado
                            ->ideBenef
                            ->infoIRComplem
                            ->planSaude[$indicePlanoSaude] = $planoSaude;
                    }
                }
                //Informações de dependentes não cadastrados pelo S-2200/S-2205/S-2300/S-2400/S-2405.
                $dependentesNaoCadastrado = $pagamento->getDependentesNaoCadastrado();
                if ($dependentesNaoCadastrado && is_array($dependentesNaoCadastrado)) {
                    foreach ($dependentesNaoCadastrado as $indiceDependente => $dependente) {
                        if (!isset($dadoFormatado->ideBenef->infoIRComplem)) {
                            $dadoFormatado->ideBenef->infoIRComplem = new stdClass();
                        }
                        $dadoFormatado->ideBenef->infoIRComplem->infoDep[$indiceDependente] = new stdClass();
                        $dadoFormatado->ideBenef->infoIRComplem->infoDep[$indiceDependente] = $dependente;
                    }
                }
                //buscamos dados de pensao para incluir conforme solicitado pela analista Lorenna
                $pensoesAlimenticia = $pagamento->getPensaoAlimenticia();
                //Pode ocorrer folha de férias e na folha de salário, portanto mesmo cpf
                $cpfPensao = [];
                if ($pensoesAlimenticia && is_array($pensoesAlimenticia)) {
                    foreach ($pensoesAlimenticia as $pensao) {
                        $incluir = true;
                        $dependentePensao = new stdClass();
                        /**
                         * varremos os dependentes processados no loop acima para
                         * não gerar dados em duplicidade
                         * os dados a serem mantidos é o do cadastro de dependentes
                         */
                        foreach ($dadoFormatado->ideBenef->infoIRComplem->infoDep as $dependente) {
                            // Caso o dependente ja esteja processado, nao adicionamos ele
                            if ($dependente->cpfDep == $pensao->cpfDep) {
                                $incluir = false;
                                break;
                            }
                        }

                        if ($incluir) {
                            $retorno = array_search($pensao->cpfDep, $cpfPensao);
                            if (!is_numeric($retorno)) {
                                $cmgPensao = \CgmFactory::getCgmByCnpjCpf($pensao->cpfDep);
                                if ($cmgPensao) {
                                    $dependentePensao->cpfDep = $pensao->cpfDep;
                                    if (!empty($cmgPensao->getDataNascimento())) {
                                        $dependentePensao->dtNascto = $cmgPensao->getDataNascimento();
                                    }
                                    $dependentePensao->nome = $cmgPensao->getNome();
                                    $dadoFormatado->ideBenef->infoIRComplem->infoDep[] = $dependentePensao;
                                    $cpfPensao[] = $pensao->cpfDep;
                                } else {
                                    $msg = "Matrícula {$pagamento->getServidor()->getMatricula()} possui pensão";
                                    $msg .= " com inconsistência de dados obrigatórios para a";
                                    $msg .= " pensâo cadastrada para {$pensao->nome}.";
                                    $msg .= "\r\nPor Favor verifique o cadastro da pensão e/ou CGM!";
                                    $msg .= "\r\nCaso o problema persista, entre em contato com o suporte.";
                                    throw new \Exception($msg);
                                }
                            }
                        }
                    }
                }
                //Informações de Imposto de Renda, por Código de Receita - CR.
                $codigosReceitas = $pagamento->getCodigoReceita();
                if ($codigosReceitas && is_array($codigosReceitas)) {
                    foreach ($codigosReceitas as $indiceCodigoReceita => $codigoReceita) {
                        if (!isset($dadoFormatado->ideBenef->infoIRComplem)) {
                            $dadoFormatado->ideBenef->infoIRComplem = new stdClass();
                        }
                        $dadoFormatado->ideBenef->infoIRComplem->infoIRCR[$indiceCodigoReceita] = new stdClass();
                        $dadoFormatado->ideBenef->infoIRComplem->infoIRCR[$indiceCodigoReceita] = $codigoReceita;
                        //Dedução do rendimento tributável relativa a dependentes.
                        $rendimentoTributavelDependente = $pagamento->getRendimentoTributavelDependente();
                        if ($rendimentoTributavelDependente && is_array($rendimentoTributavelDependente)) {
                            foreach ($rendimentoTributavelDependente as $indiceTributo => $dependenteTributo) {
                                $dadoFormatado
                                    ->ideBenef
                                    ->infoIRComplem
                                    ->infoIRCR[$indiceCodigoReceita]
                                    ->dedDepen[$indiceTributo] = new stdClass();
                                $dadoFormatado
                                    ->ideBenef
                                    ->infoIRComplem
                                    ->infoIRCR[$indiceCodigoReceita]
                                    ->dedDepen[$indiceTributo] = $dependenteTributo;
                            }
                        }
                        //Informação dos beneficiários da pensão alimentícia.
                        $pensoesAlimenticia = $pagamento->getPensaoAlimenticia();
                        if ($pensoesAlimenticia && is_array($pensoesAlimenticia)) {
                            foreach ($pensoesAlimenticia as $indicePensao => $pensao) {
                                $dadoFormatado
                                    ->ideBenef
                                    ->infoIRComplem
                                    ->infoIRCR[$indiceCodigoReceita]
                                    ->penAlim[$indicePensao] = new stdClass();
                                $dadoFormatado
                                    ->ideBenef
                                    ->infoIRComplem
                                    ->infoIRCR[$indiceCodigoReceita]
                                    ->penAlim[$indicePensao] = $pensao;
                            }
                        }
                        //Informações relativas a planos de previdência complementar.
                        $previdenciasComplementar = $pagamento->getPrevidenciaComplementar();
                        if ($previdenciasComplementar && is_array($previdenciasComplementar)) {
                            foreach ($previdenciasComplementar as $indicePrevidencia => $previdenciaComplementar) {
                                $dadoFormatado
                                    ->ideBenef
                                    ->infoIRComplem
                                    ->infoIRCR[$indiceCodigoReceita]
                                    ->previdCompl[$indicePrevidencia] = new stdClass();
                                $dadoFormatado
                                    ->ideBenef
                                    ->infoIRComplem
                                    ->infoIRCR[$indiceCodigoReceita]
                                    ->previdCompl[$indicePrevidencia] = $previdenciaComplementar;
                            }
                        }
                        //Informações de processos relacionados a não retenção de tributos ou a depósitos judiciais
                        $processosRetencaoTributos = $pagamento->getProcessoRetencaoJudicial();
                        if ($processosRetencaoTributos && is_array($processosRetencaoTributos)) {
                            foreach ($processosRetencaoTributos as $indiceProcesso => $processoRetencaoTributo) {
                                $dadoFormatado
                                    ->ideBenef
                                    ->infoIRComplem
                                    ->infoIRCR[$indiceCodigoReceita]
                                    ->infoProcRet[$indiceProcesso] = new stdClass();
                                $dadoFormatado
                                    ->ideBenef
                                    ->infoIRComplem
                                    ->infoIRCR[$indiceCodigoReceita]
                                    ->infoProcRet[$indiceProcesso] = $processoRetencaoTributo;
                            }
                        }
                    }
                }
            }
        }
        $dadoFormatado->perApur = $this->perApur;

        return $dadoFormatado;
    }

    public function setDecimoTerceiro()
    {
        $this->isDecimoTerceiro = true;
    }

    public function getCgmsNaoEnviados()
    {
        return $this->cgmNaoIncluidos;
    }

    /**
     * @param bool
     */
    public function setProcessaMes12($processaMes12)
    {
        $this->processaMes12 = $processaMes12;
    }

    /**
     * @param bool
     */
    public function setProcessa13($processa13)
    {
        $this->processa13 = $processa13;
    }
}
