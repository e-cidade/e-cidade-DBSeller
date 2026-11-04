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

use App\Domain\RecursosHumanos\Pessoal\Repository\PrevidenciaComplementarRepository;
use BusinessException;
use CalculoFolha;
use CgmFisico;
use DBCompetencia;
use DBDate;
use DBPessoal;
use ECidade\RecursosHumanos\ESocial\Entity\PagamentosRendimentosTrabalho;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\Recurso;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Repository\ProcessoJudicial\DeducaoSuspensaRepository;
use ECidade\RecursosHumanos\ESocial\Repository\ProcessoJudicial\RetencaoRepository;
use ECidade\RecursosHumanos\ESocial\Repository\ProcessoJudicial\ServidorRepository as ServidorProcessoRepository;
use ECidade\RecursosHumanos\ESocial\Repository\ProcessoJudicial\SuspensaoPensaoRepository;
use ECidade\RecursosHumanos\ESocial\Repository\ProcessoJudicial\TributoIRRFRepository;
use ECidade\RecursosHumanos\ESocial\Repository\ProcessoJudicial\ValorRetencaoRepository;
use ECidade\RecursosHumanos\Pessoal\Repository\DependenteRepository;
use ECidade\RecursosHumanos\Pessoal\Repository\PensaoAlimenticiaRepository;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorMovimentacaoRepository;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorOperadoraSaudeDependenteRepository;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorOperadoraSaudeRepository;
use ECidade\RecursosHumanos\Pessoal\Service\DataPagamentoFolhaService;
use ECidade\RecursosHumanos\Pessoal\Service\DecimoServidorService;
use ECidade\V3\Extension\Registry;
use FolhaPagamento;
use InstituicaoRepository;
use ServidorRepository;
use stdClass;

/**
 * Class PagamentosRendimentosTrabalhoService
 * @package ECidade\RecursosHumanos\ESocial\Service
 */
class PagamentosRendimentosTrabalhoService
{
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
     * @var Instituicao
     */
    private $instituicaoSessao;
    /**
     * @var DBCompetencia|null
     */
    private $competencia;
    /**
     * @var array
     */
    private $rubricasValidas;
    /**
     * @var string
     */
    private $rubricaPensaoAlimenticia;

    /**
     * @var array
     */
    private $rubricasPensao = [];

    /**
     * @var bool
     */
    private $processa13 = false;

    /**
     * @var bool
     */
    private $processaMes12 = false;

    private $qtdServidores;

    /**
     * @var array
     */
    private $deParaTipoPagamento = [
        Tipo::S1200_API => 1,
        Tipo::S2299_API => 2,
        Tipo::S2399_API => 3,
        Tipo::S1202_API => 4,
        Tipo::S1207_API => 5
    ];

    /**
     * @var bool
     */
    private $isDecimoTerceiro = false;

    /**
     * @param CgmFisico $cgm
     * @return PagamentosRendimentosTrabalho|null
     * @throws \DBException
     */
    public function buscarPorCGM(CgmFisico $cgm, $servidores = null)
    {
        if (empty($this->anoCompetencia) || empty($this->mesCompetencia)) {
            throw new \Exception("Competência não informada.");
        }

        $this->instituicaoSessao = InstituicaoRepository::getInstituicaoSessao();
        $this->competencia = new DBCompetencia($this->anoCompetencia, $this->mesCompetencia);
        if (empty($servidores)) {
            $servidores = ServidorRepository::getServidoresByCgm($cgm, $this->competencia);
        } else {
            foreach ($servidores as $servidor) {
                $matriculas[] = $servidor->getMatricula();
            }
            $servidores = ServidorRepository::getServidoresByMatriculas(
                $this->anoCompetencia,
                $this->mesCompetencia,
                $matriculas,
                $this->instituicaoSessao->getCodigo()
            );
        }
        
        $pagamentos = [];

        $servidoresPagamento = [];
        foreach ($servidores as $indice => $servidor) {
            $this->addPagamentosRendimentos(
                $servidor,
                $pagamentos,
                $servidor->getTipoPagamentoEsocialAPI($this->anoCompetencia, $this->mesCompetencia),
                $servidoresPagamento
            );
        }

        $pagamentosRendimentosTrabalho = null;

        if (!empty($pagamentos)) {
            $pagamentosRendimentosTrabalho = new PagamentosRendimentosTrabalho();
            $pagamentosRendimentosTrabalho->setCPFBeneficiente($cgm->getCPF());
            $pagamentosRendimentosTrabalho->setPagamentos($pagamentos);

            foreach ($servidoresPagamento as $servidorPagamento) {
                $pagamentosRendimentosTrabalho->setServidor($servidorPagamento);
                $servidorMovimentacaoRepository = new ServidorMovimentacaoRepository();
                $movimentacoes = $servidorMovimentacaoRepository
                    ->scopeAno($this->anoCompetencia)
                    ->scopeMes($this->mesCompetencia)
                    ->scopeMatricula($servidorPagamento->getMatricula())
                    ->get();
                foreach ($movimentacoes as $movimentacao) {
                    if (!empty($movimentacao)) {
                        $pagamentosRendimentosTrabalho->setDataLaudoMolestia($movimentacao->getDataLaudoMolestia());
                    }
                }

                $dadosCodigoReceita = [];
                $dadosCodigo = new stdClass;
                $codigoReceita = $servidorPagamento->getTipoIRCR($this->mesCompetencia, $this->anoCompetencia);
                if (!empty($codigoReceita)) {
                    $dadosCodigo->tpCR = $servidorPagamento->getTipoIRCR($this->mesCompetencia, $this->anoCompetencia);
                    $dadosCodigoReceita[] = $dadosCodigo;
                }

                if (!empty($dadosCodigoReceita)) {
                    $pagamentosRendimentosTrabalho->setCodigoReceita($dadosCodigoReceita);
                }


                $dependenteRepository = new DependenteRepository();
                $dependentesNaoCadastrado = $dependenteRepository
                    ->scopeMatricula($servidorPagamento->getMatricula())
                    ->scopeIrrfComplemetar('true')
                    ->orderBy(array('rh31_nome'))
                    ->setUseJoin(true)
                    ->get();
                if (!empty($dependentesNaoCadastrado)) {
                    $dadosDependentesNaoCadastrado = [];
                    foreach ($dependentesNaoCadastrado as $dependente) {
                        $dadoDependenteNao = new stdClass;
                        $dadoDependenteNao->cpfDep = $dependente->getCpf();
                        if (!empty($dependente->getDataNascimento()->getDate())) {
                            $dadoDependenteNao->dtNascto = $dependente->getDataNascimento()->getDate();
                        }
                        if (!empty($dependente->getNome())) {
                            $dadoDependenteNao->nome = $dependente->getNome();
                        }
                        $dadoDependenteNao->depIRRF = 'S';
                        if (!empty($dependente->getTipoParentesco())) {
                            $parentesco = str_pad((string) $dependente->getTipoParentesco(), 2, "0", STR_PAD_LEFT);
                            $dadoDependenteNao->tpDep = $parentesco;
                        }
                        if (!empty($dependente->getDescricaoParentesco()) &&
                            strlen($dependente->getDescricaoParentesco()) > 2) {
                            $dadoDependenteNao->descrDep = $dependente->getDescricaoParentesco();
                        }
                        if ((int) $dadoDependenteNao->tpDep == 99) {
                            $dadoDependenteNao->descrDep = (string) $dependente->getDescricaoParentesco();
                        }
                        $dadosDependentesNaoCadastrado[] = $dadoDependenteNao;
                    }
                    $pagamentosRendimentosTrabalho->setDependentesNaoCadastrado($dadosDependentesNaoCadastrado);
                }

                $calculoFinanceiroSalario = $servidorPagamento
                    ->getCalculoFinanceiro(CalculoFolha::CALCULO_SALARIO)
                    ->getEventosFinanceiros();
                $dadosRedimentoTributavel = [];

                /**
                 * Buscamos os dependentes que nao sao complementares
                 * e possuem configuracão de IRF
                 */
                $dependentes = $dependenteRepository
                    ->scopeMatricula($servidorPagamento->getMatricula())
                    ->scopeIrrfComplemetar('false')
                    ->scopeIrf('0', '!=')
                    ->orderBy(array('rh31_nome'))
                    ->setUseJoin(true)
                    ->get();
                /**
                 * Validacao criada para nao enviar para o esocial quando o
                 * depentente for do tipo (2 e 3) e possuir idade superior a 21 ou 24 anos
                 */
                if (is_array($dependentes) && sizeof($dependentes) > 0) {
                    $dependenteAux = [];
                    $dataPagamentoFolha = null;
                    if ($this->processa13) {
                        switch ($this->anoCompetencia) {
                            case 2020:
                                $dataPagamentoFolha= "{$this->anoCompetencia}-12-18";
                                break;
                            default:
                                $dataDecimo = DecimoServidorService::getDataDecimo(
                                    $servidor->getCgm()->getCodigo(),
                                    $this->anoCompetencia
                                );
                                if (!empty($dataDecimo)) {
                                    $dataPagamentoFolha = $dataDecimo;
                                } else {
                                    $dataPagamentoFolha = "{$this->anoCompetencia}-12-20";
                                }
                                break;
                        }
                    } else {
                        $dataPagamentoFolha = $this->buscarDataPagamentoFolha();
                    }
                    $dataPagamentoFolha = new DBDate($dataPagamentoFolha);
                    foreach ($dependentes as $dependente) {
                        $adiciona = true;
                        if (in_array($dependente->getTipo(), [2,3,4,5,7])) {
                            if (empty($dependente->getDataNascimento())) {
                                $msg = "Servidor {$servidorPagamento->getMatricula()} - "
                                    . "{$servidorPagamento->getCgm()->getNome()} possuí depentente(s) "
                                    . "sem data de nascimento informada no sistema.\n Favor acesse a rotina: "
                                    . "'DB:RECURSOSHUMANOS > Pessoal > Cadastros > Servidores > Alteração', "
                                    . "e verifique as informações cadastradas.";
                                throw new BusinessException($msg);
                            }
                            $idade = DBDate::getIntervaloEntreDatas(
                                $dataPagamentoFolha,
                                $dependente->getDataNascimento()
                            );
                            switch ($dependente->getTipo()) {
                                case 2: // Filho(a)/Enteado(a), até 21 anos de idade
                                case 4: // Irmão(ã), neto(a) ou bisneto(a),  até 21 anos
                                case 7: // Menor pobre até 21 anos, com a guarda judicial
                                    if ($idade->y > 21) {
                                        $adiciona = false;
                                        continue;
                                    }
                                    break;
                                case 3: // Filho(a) ou enteado(a),  24 anos de idade cursando ensino superior
                                case 5: // Irmão(ã), neto(a) ou bisneto(a), de 21 a 24 anos c/ensino superior
                                    if ($idade->y > 24) {
                                        $adiciona = false;
                                        continue;
                                    }
                                    break;
                            }
                        }
                        if ($adiciona) {
                            $dependenteAux[] = $dependente;
                        }
                    }
                    $dependentes = $dependenteAux;
                }
                if (is_array($dependentes) && sizeof($dependentes) > 0) {
                    $baseDescontoIRDependente = 0;
                    $sql = "
                        select
                            r07_valor
                        from
                            pessoal.pesdiver
                        where
                            r07_anousu = {$this->anoCompetencia}
                            and r07_mesusu = {$this->mesCompetencia}
                            and r07_instit = {$this->instituicaoSessao->getCodigo()}
                            and r07_codigo = 'D901'";
                    $rs = db_query($sql);

                    if (pg_num_rows($rs) == 1) {
                        $baseDescontoIRDependente = \db_utils::fieldsMemory($rs, 0)->r07_valor;
                    }
                    /**
                     * Caso tenha dependentes, verificamos se existe a rubrica R984
                     * caso exisa, utilizamos o valor do diverso D901 que é o valor individual de cada dependente
                     * para nao precisar efetuar nenhum tipo de calculo
                     * essa informacão foi passada pela analista Lorenna
                     */
                    foreach ($dependentes as $dependente) {
                        $rubricaFixa = 'R984'; //  R984-VLR REF DEPENDENTES P/ IRF
                        foreach ($calculoFinanceiroSalario as $salario) {
                            $rubricaSalario = $salario->getRubrica()->getCodigo();
                            if ($rubricaSalario === $rubricaFixa) {
                                $dadoRedimentoTrbutavel = new stdClass;
                                $dadoRedimentoTrbutavel->tpRend = 11;
                                $dadoRedimentoTrbutavel->cpfDep = $dependente->getCPF();
                                $dadoRedimentoTrbutavel->vlrDedDep = (float) $baseDescontoIRDependente;
                                $dadosRedimentoTributavel[] = $dadoRedimentoTrbutavel;
                            }
                        }
                        $calculoFinanceiroDecimoTerceiro = $servidorPagamento
                            ->getCalculoFinanceiro(CalculoFolha::CALCULO_13o)
                            ->getEventosFinanceiros();
                        foreach ($calculoFinanceiroDecimoTerceiro as $salario) {
                            $rubricaDecimoTerceiro = $salario->getRubrica()->getCodigo();
                            if ($rubricaDecimoTerceiro === $rubricaFixa) {
                                $dadoRedimentoTrbutavel = new stdClass;
                                $dadoRedimentoTrbutavel->tpRend = 12;
                                $dadoRedimentoTrbutavel->cpfDep = $dependente->getCPF();
                                $dadoRedimentoTrbutavel->vlrDedDep = (float) $baseDescontoIRDependente;
                                $dadosRedimentoTributavel[] = $dadoRedimentoTrbutavel;
                            }
                        }
                        $calculoFinanceiroFerias = $servidorPagamento
                            ->getCalculoFinanceiro(CalculoFolha::CALCULO_FERIAS)
                            ->getEventosFinanceiros();
                        foreach ($calculoFinanceiroFerias as $salario) {
                            $rubricaFerias = $salario->getRubrica()->getCodigo();
                            if ($rubricaFerias === $rubricaFixa) {
                                $dadoRedimentoTrbutavel = new stdClass;
                                $dadoRedimentoTrbutavel->tpRend = 13;
                                $dadoRedimentoTrbutavel->cpfDep = $dependente->getCPF();
                                $dadoRedimentoTrbutavel->vlrDedDep = (float) $baseDescontoIRDependente;
                                $dadosRedimentoTributavel[] = $dadoRedimentoTrbutavel;
                            }
                        }
                    }
                }

                if (!empty($dadosRedimentoTributavel)) {
                    $pagamentosRendimentosTrabalho->setRendimentoTributavelDependente($dadosRedimentoTributavel);
                }

                $dadosPensaoAlimenticia = [];

                $pensaoAlimenticiaRepository = new PensaoAlimenticiaRepository();
                $dadosPensao = $pensaoAlimenticiaRepository
                    ->scopeServidor($servidorPagamento)
                    ->scopeMes($this->mesCompetencia)
                    ->scopeAno($this->anoCompetencia)
                    ->get();
                foreach ($dadosPensao as $dadoPensao) {
                    if ($dadoPensao->getValor() > 0 || $dadoPensao->getValorComplementar() > 0) {
                        $dadoPensaoAlimenticia = new stdClass;
                        $dadoPensaoAlimenticia->tpRend = 11;
                        if (!$dadoPensao->getCgmPensionista() instanceof CgmFisico) {
                            $msgErro = "Matrícula: {$servidorPagamento->getMatricula()} com pensionista (CGM: ";
                            $msgErro .= "{$dadoPensao->getCgmPensionista()->getCodigo()} ";
                            $msgErro .= "Nome: {$dadoPensao->getCgmPensionista()->getNome()} ) com CPF inconsistente";
                            throw new \Exception($msgErro);
                        }
                        $dadoPensaoAlimenticia->cpfDep = $dadoPensao->getCgmPensionista()->getCpf();
                        $dadoPensaoAlimenticia->nome = $dadoPensao->getCgmPensionista()->getNome();
                        $dadoPensaoAlimenticia->vlrDedPenAlim = ((float) $dadoPensao->getValor())
                            + ((float) $dadoPensao->getValorComplementar());
                        $dadosPensaoAlimenticia[] = $dadoPensaoAlimenticia;
                    }

                    if ($dadoPensao->getValorDecimo() > 0) {
                        $dadoPensaoAlimenticia = new stdClass;
                        $dadoPensaoAlimenticia->tpRend = 12;
                        if (!$dadoPensao->getCgmPensionista() instanceof CgmFisico) {
                            $msgErro = "Matrícula: {$servidorPagamento->getMatricula()} com pensionista (CGM: ";
                            $msgErro .= "{$dadoPensao->getCgmPensionista()->getCodigo()} ";
                            $msgErro .= "Nome: {$dadoPensao->getCgmPensionista()->getNome()} ) com CPF inconsistente";
                            throw new \Exception($msgErro);
                        }
                        $dadoPensaoAlimenticia->cpfDep = $dadoPensao->getCgmPensionista()->getCpf();
                        $dadoPensaoAlimenticia->nome = $dadoPensao->getCgmPensionista()->getNome();
                        $dadoPensaoAlimenticia->vlrDedPenAlim = (float) $dadoPensao->getValorDecimo();
                        $dadosPensaoAlimenticia[] = $dadoPensaoAlimenticia;
                    }

                    if ($dadoPensao->getValorFerias() > 0) {
                        $dadoPensaoAlimenticia = new stdClass;
                        $dadoPensaoAlimenticia->tpRend = 13;
                        if (!$dadoPensao->getCgmPensionista() instanceof CgmFisico) {
                            $msgErro = "Matrícula: {$servidorPagamento->getMatricula()} com pensionista (CGM: ";
                            $msgErro .= "{$dadoPensao->getCgmPensionista()->getCodigo()} ";
                            $msgErro .= "Nome: {$dadoPensao->getCgmPensionista()->getNome()} ) com CPF inconsistente";
                            throw new \Exception($msgErro);
                        }
                        $dadoPensaoAlimenticia->cpfDep = $dadoPensao->getCgmPensionista()->getCpf();
                        $dadoPensaoAlimenticia->nome = $dadoPensao->getCgmPensionista()->getNome();
                        $dadoPensaoAlimenticia->vlrDedPenAlim = (float) $dadoPensao->getValorFerias();
                        $dadosPensaoAlimenticia[] = $dadoPensaoAlimenticia;
                    }
                }

                if (!empty($dadosPensaoAlimenticia)) {
                    $pagamentosRendimentosTrabalho->setPensaoAlimenticia($dadosPensaoAlimenticia);
                }

                $previdenciaComplementarRepository = new PrevidenciaComplementarRepository();
                $previdenciaComplementar = $previdenciaComplementarRepository
                    ->find($servidorPagamento->getMatricula())
                    ->toArray();
                $dadosPrevidencia = [];
                foreach ($previdenciaComplementar as $previ) {
                    $previdencia = (object) $previ;
                    $dadoPrevidencia = new stdClass;
                    $dadoPrevidencia->tpPrev = $previdencia->tipoPrevidencia;
                    $dadoPrevidencia->cnpjEntidPC = $previdencia->cnpj;
                    $dadoPrevidencia->vlrDedPC = (double) $previdencia->deducaoRelativa;
                    $dadoPrevidencia->vlrPatrocFunp = (double) $previdencia->contribuicaoPatrocinador;
                    $dadosPrevidencia[] = $dadoPrevidencia;
                }
                if (!empty($dadosPrevidencia)) {
                    $pagamentosRendimentosTrabalho->setPrevidenciaComplementar($dadosPrevidencia);
                }

                $servidorOperadoraSaudeRepository = new ServidorOperadoraSaudeRepository();
                $servidorOperadoraSaudeDiversos = $servidorOperadoraSaudeRepository
                    ->scopeServidor($servidorPagamento)
                    ->scopeAno($this->anoCompetencia)
                    ->scopeMes($this->mesCompetencia)
                    ->get();
                $cnpjOperadora = [];
                $dadoPlanoSaudeColetivo = [];
                $indiceOperadora = 0;
                $somado = false;
                foreach ($servidorOperadoraSaudeDiversos as $indiceServidor => $servidorOperadoraSaudeUnico) {
                    $operadora = $servidorOperadoraSaudeUnico->getOperadoraSaude();
                    $cnpjOper = $operadora->getCgm()->getCnpj();
                    if (!in_array($cnpjOper, $cnpjOperadora)) {
                        $dadosPlano = new stdClass;
                        $dadosPlano->cnpjOper = $operadora->getCgm()->getCnpj();
                        $regANS = (string)$operadora->getAns();
                        if (!empty($regANS)) {
                            $dadosPlano->regANS = (string)$operadora->getAns();
                        }
                        $dadosPlano->vlrSaudeTit = $this->processaDescontoSaude(
                            $calculoFinanceiroSalario,
                            $calculoFinanceiroDecimoTerceiro,
                            $calculoFinanceiroFerias,
                            $servidorOperadoraSaudeUnico->getRubrica()->getCodigo()
                        );
                        $somado = true;
                        $cnpjOperadora[] = $cnpjOper;
                    }
                    if (in_array($cnpjOper, $cnpjOperadora) && !$somado) {
                        $dadosPlano->vlrSaudeTit = $this->processaDescontoSaude(
                            $calculoFinanceiroSalario,
                            $calculoFinanceiroDecimoTerceiro,
                            $calculoFinanceiroFerias,
                            $servidorOperadoraSaudeUnico->getRubrica()->getCodigo(),
                            $dadosPlano->vlrSaudeTit
                        );
                    }
                    $servidorDependenteRepository = new ServidorOperadoraSaudeDependenteRepository();
                    $dependentesPlanoSaude = $servidorDependenteRepository
                        ->scopeServidorOperadoraSaude($servidorOperadoraSaudeUnico)
                        ->get();
                    foreach ($dependentesPlanoSaude as $indiceDependente => $dependentePlanoSaude) {
                        $codigoDependente = $dependentePlanoSaude->getDependente()->getCodigo();
                        $dependenteRepository = new DependenteRepository();
                        $dadoDependente = $dependenteRepository
                            ->getDadosDependente($codigoDependente);
                        if (!empty($dadoDependente)) {
                            $cpfDependente = $dadoDependente->getCpf();
                        }
                        $valorPlanoSaudeDependente = $dependentePlanoSaude->getValor();
                        if (empty($valorPlanoSaudeDependente)) {
                            $valorPlanoSaudeDependente = $this->processaDescontoSaude(
                                $calculoFinanceiroSalario,
                                $calculoFinanceiroDecimoTerceiro,
                                $calculoFinanceiroFerias,
                                $servidorOperadoraSaudeUnico->getRubrica()->getCodigo(),
                                $valorPlanoSaudeDependente
                            );
                        }
                        $dadosDependentePlano = new stdClass;
                        $dadosDependentePlano->cpfDep = $cpfDependente;
                        $dadosDependentePlano->vlrSaudeDep = (float) $valorPlanoSaudeDependente;
                        $dadosPlano->infoDepSau[$indiceDependente] = new stdClass;
                        $dadosPlano->infoDepSau[$indiceDependente] = $dadosDependentePlano;
                    }
                    $dadoPlanoSaudeColetivo[$indiceServidor] = $dadosPlano;
                    $somado = false;
                }
                if (!empty($dadoPlanoSaudeColetivo)) {
                    $pagamentosRendimentosTrabalho->setPlanoSaudeColetivo($dadoPlanoSaudeColetivo);
                }

                $servidorProcessoRepository = new ServidorProcessoRepository();
                $servidoresProcesso = $servidorProcessoRepository
                    ->scopeMatricula($servidorPagamento->getMatricula())
                    ->get();
                $dadosProcessoRetencaoJudicial = [];
                //PROCESSO JUDICIAL
                foreach ($servidoresProcesso as $isp => $servidoreProcesso) {
                    $processosDoServidor = $servidoreProcesso->getProcessoJudicial();
                    $sequencialProcesso = (int) $servidoreProcesso->getSequencialProcesso();
                    foreach ($processosDoServidor as $processoDoServidor) {
                        $sequencialDoProcesso = (int) $processoDoServidor->getSequencial();
                        if ($sequencialProcesso == $sequencialDoProcesso) {
                            $numeroProcesso = $processoDoServidor->getNumeroProcesso();
                        }
                    }
                    $dadoProcessoRetencaoJudicial = new stdClass;

                    $dadoProcessoRetencaoJudicial->tpProcRet = "2";
                    $dadoProcessoRetencaoJudicial->nrProcRet = $numeroProcesso;

                    $dadosProcessoRetencaoJudicial[$isp] = $dadoProcessoRetencaoJudicial;
                    $dadosProcessoRetencaoJudicial[$isp]->infoValores[0] = new stdClass;
                    $dadosProcessoRetencaoJudicial[$isp]->infoValores[0]->dedSusp[0] = new stdClass;
                    $dadosProcessoRetencaoJudicial[$isp]->infoValores[0]->dedSusp[0]->benefPen[0] = new stdClass;

                    $tributoIRRFRepository = new TributoIRRFRepository();
                    $tributosIRRF = $tributoIRRFRepository
                        ->scopeSequencialServidor($servidoreProcesso->getSequencial())
                        ->get();
                    foreach ($tributosIRRF as $indiceIR => $tributoIRRF) {
                        $retencaoRepository = new RetencaoRepository();
                        $retencoes = $retencaoRepository
                            ->scopeSequencialTributoIRRF($tributoIRRF->getSequencial())
                            ->get();
                        foreach ($retencoes as $retencao) {
                            $valorRetencaoRepository = new ValorRetencaoRepository();
                            $valoresRetencao = $valorRetencaoRepository
                                ->scopeSequencialRetencao($retencao->getSequencial())
                                ->get();
                            $grupoinfoValores = new stdClass;
                            foreach ($valoresRetencao as $iret => $valorRetencao) {
                                $dadosinfoValores = new stdClass;
                                $dadosinfoValores->indApuracao =
                                    $valorRetencao->getIndicativoApuracao();
                                $dadosinfoValores->vlrNRetido = (double)
                                $valorRetencao->getValorRetencao();
                                $dadosinfoValores->vlrDepJud = (double)
                                $valorRetencao->getValorDepositoJudicial();
                                $dadosinfoValores->vlrvlrCmpAnoCal = (double)
                                $valorRetencao->getValorCompensacaoAno();
                                $dadosinfoValores->vlrCmpAnoAnt = (double)
                                $valorRetencao->getValorCompensacaoAnoAnterior();
                                $dadosinfoValores->vlrRendSusp = (double)
                                $valorRetencao->getValorRendimentoSuspenso();
                                $dadoProcessoRetencaoJudicial->infoValores[$iret] = $dadosinfoValores;

                                $deducaoSuspensaRepository = new DeducaoSuspensaRepository();
                                $deducoesSuspensa = $deducaoSuspensaRepository
                                    ->scopeSequencialValorRetencao($valorRetencao->getSequencial())
                                    ->get();
                                $grupoDedSusp = new stdClass;
                                foreach ($deducoesSuspensa as $idedu => $deducoeSuspensa) {
                                    $dadosDedSusp = new stdClass;
                                    $dadosDedSusp->indTpDeducao =
                                        $deducoeSuspensa->getTipoDeducao();
                                    $dadosDedSusp->vlrDedSusp = (double)
                                    $deducoeSuspensa->getValorDeducao();
                                    $grupoDedSusp->dedSusp[$idedu] = $dadosDedSusp;
                                    $dadosProcessoRetencaoJudicial[$isp]
                                        ->infoValores[$iret]
                                        ->dedSusp[$idedu] = $dadosDedSusp;
                                    $suspensaoPensaoRepository = new SuspensaoPensaoRepository();
                                    $suspensoesPensao = $suspensaoPensaoRepository
                                        ->scopeSequencialDeducaoSuspensa($deducoeSuspensa->getSequencial())
                                        ->get();
                                    foreach ($suspensoesPensao as $isus => $suspensaoPensao) {
                                        $dadoPensao = new stdClass;
                                        $dadoPensao->cpfDep = $suspensaoPensao->getCpfDependente();
                                        $dadoPensao->vlrDepenSusp = (double) $suspensaoPensao->getValorDeducao();
                                        $dadosProcessoRetencaoJudicial[$isp]
                                            ->infoValores[$iret]
                                            ->dedSusp[$idedu]
                                            ->benefPen[$isus] = $dadoPensao;
                                    }
                                }
                            }
                        }
                    }
                    $cpfDependente = $dadosProcessoRetencaoJudicial[$isp]
                        ->infoValores[0]
                        ->dedSusp[0]
                        ->benefPen[0]
                        ->cpfDep;
                    if (empty($cpfDependente)) {
                        unset($dadosProcessoRetencaoJudicial[$isp]
                                ->infoValores[0]
                                ->dedSusp[0]
                                ->benefPen[0]);
                    }
                    $tipoDeducao = $dadosProcessoRetencaoJudicial[$isp]
                        ->infoValores[0]
                        ->dedSusp[0]->indTpDeducao;
                    if (empty($tipoDeducao)) {
                        unset($dadosProcessoRetencaoJudicial[$isp]
                                ->infoValores[0]
                                ->dedSusp[0]);
                    }
                    $indicativoApuracao = $dadosProcessoRetencaoJudicial[$isp]
                        ->infoValores[0]
                        ->indApuracao;
                    if (empty($indicativoApuracao)) {
                        unset($dadosProcessoRetencaoJudicial[$isp]
                                ->infoValores[0]);
                    }
                    if (empty($dadosProcessoRetencaoJudicial[$isp]->infoValores)) {
                        unset($dadosProcessoRetencaoJudicial[$isp]->infoValores);
                    }
                }

                if (!empty($dadosProcessoRetencaoJudicial)) {
                    $pagamentosRendimentosTrabalho->setProcessoRetencaoJudicial($dadosProcessoRetencaoJudicial);
                }
            }
        }
        return $pagamentosRendimentosTrabalho;
    }

    /**
     *Adiciona os rendimentos dos eventos S-2299 ou S-2399.
     * @param $servidor
     * @param $pagamentos
     */
    private function addPagamentosRendimentos($servidor, &$pagamentos, $tipoEvento, &$servidoresPagamento)
    {
        $pagamento = new stdClass();

        $tipoEvento = $servidor->getTipoPagamentoEsocialAPI($this->anoCompetencia, $this->mesCompetencia);
        if ($servidor->isPensionista() || !$servidor->isAtivo()) {
            $tipoEvento = TIPO::S1207_API;
        }
        $pagamento->tpPgto = null;
        $pagamento->dtPgto = null;

        // Busca o pagamento do DECIMO TERCEIRO
        if ($this->processa13) {
            $pagamentoDecimo = new stdClass();
            $pagamentoDecimo->tpPgto = $this->deParaTipoPagamento[$tipoEvento];
            if ($this->mesCompetencia == 12) {
                $dataDecimo = DecimoServidorService::getDataDecimo(
                    $servidor->getCgm()->getCodigo(),
                    $this->anoCompetencia
                );
                if (!empty($dataDecimo)) {
                    $pagamentoDecimo->dtPgto = $dataDecimo;
                } else {
                    $pagamentoDecimo->dtPgto = "{$this->anoCompetencia}-12-20";
                }
            } else {
                $pagamentoDecimo->dtPgto = $this->buscarDataPagamentoFolha();
            }
            $pagamento = $pagamentoDecimo;
            $this->buscarDetalhamentoPagamentos($servidor, $pagamentoDecimo, true, $servidoresPagamento);
            $this->buscarDetalhamentoPagamentos($servidor, $pagamento, false, $servidoresPagamento);

            if (!empty($pagamento)) {
                $dmDevAdiantamento = "{$servidor->getMatricula()}ADIANT13"
                    . "{$this->anoCompetencia}{$this->mesCompetencia}";
                if (!empty($pagamento[$dmDevAdiantamento])) {
                    unset($pagamento[$dmDevAdiantamento]);
                }
                foreach ($pagamentoDecimo as $key => $value) {
                    $pagamento[$key] = $pagamentoDecimo[$key];
                }
            } else {
                if (!empty($pagamentoDecimo)) {
                    $pagamento = $pagamentoDecimo;
                }
            }
        } else {
            $pagamento->tpPgto = $this->deParaTipoPagamento[$tipoEvento];
            $pagamento->dtPgto = $this->buscarDataPagamentoFolha();
            $this->buscarDetalhamentoPagamentos($servidor, $pagamento, false, $servidoresPagamento);
        }

        if (!empty($pagamentos)) {
            foreach ($pagamento as $key => $value) {
                $pagamentos[$key] = $pagamento[$key];
            }
        } else {
            if (!empty($pagamento)) {
                $pagamentos = $pagamento;
            }
        }
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

    /**
     * @return int
     */
    public function getAnoCompetencia()
    {
        return $this->anoCompetencia;
    }

    /**
     * @return int
     */
    public function getMesCompetencia()
    {
        return $this->mesCompetencia;
    }

    /**
     * Define quais são as rubricas válidas para este evento
     * @param array $rubricasValidas
     */
    public function setRubricasValidas($rubricasValidas)
    {
        $this->rubricasValidas = $rubricasValidas;
    }

    /**
     * Define qual tipo de data de pagamento
     * @param string $tipoDataPagamento
     */
    public function setTipoDataPagamento($tipoDataPagamento)
    {
        $this->tipoDataPagamento = $tipoDataPagamento;
    }

    /**
     * Retorna a data de rescisão do servidor
     * @param $servidor
     * @return string
     */
    private function buscarDataPagamentoRescisao($servidor)
    {
        $dataRescisao = $servidor->getDataPagamentoRescisao();
        if (!empty($dataRescisao)) {
            $dataRescisao = $dataRescisao->format('Y-m-d');
        }
        return $dataRescisao;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function buscarDataPagamentoFolha()
    {
        $dataPagamentoService = new DataPagamentoFolhaService();
        $dataPagamentoService->setAnoCompetencia($this->anoCompetencia);
        $dataPagamentoService->setMesCompetencia($this->mesCompetencia);
        $stdParametros = new stdClass();
        $stdParametros->instituicao = $this->instituicaoSessao->getCodigo();

        $dataPagamentos = $dataPagamentoService->buscarDataPagamentoInstituicaoCompetencia($stdParametros);
        if (empty($dataPagamentos)) {
            throw new BusinessException("Folha de pagamento encontra-se aberta, sem informação da data de pagamento.");
        }
        return $dataPagamentos[0]->getDataPagamento()->getDate();
    }

    /**
     * Busca e monta os dados do detalhamento dos pagamentos de acordo com o layout do evento.
     * @param $servidor
     * @param $pagamento
     */
    private function buscarDetalhamentoPagamentos($servidor, &$pagamento, $decimo = false, &$servidoresPagamento = null)
    {
        $identificadorRescisao = $servidor->getMatricula() . 'RESC' . $this->anoCompetencia . $this->mesCompetencia;

        $identificadorRGPS = $servidor->getCgm()->getCodigo() . $this->anoCompetencia . $this->mesCompetencia;
        $pagamentos = [];
        $matricula = $servidor->getMatricula();

        $tipoPagamento = [
            1 => ['recibo' => (object)['layout' => 'S-1200', 'referencia' => $identificadorRGPS],
                'identificador' => "{$matricula}SAL{$this->anoCompetencia}{$this->mesCompetencia}"],
            2 => ['recibo' => (object)['layout' => 'S-2299', 'referencia' => $identificadorRescisao],
                'identificador' => $identificadorRescisao],
            3 => ['recibo' => (object)['layout' => 'S-2399', 'referencia' => $identificadorRescisao],
                'identificador' => "RescTSVE_{$this->anoCompetencia}{$this->mesCompetencia}"],
            4 => ['recibo' => (object)['layout' => 'S-1200', 'referencia' => $identificadorRGPS],
                'identificador' => "{$matricula}SAL{$this->anoCompetencia}{$this->mesCompetencia}"],
            5 => ['recibo' => (object)['layout' => 'S-1200', 'referencia' => $identificadorRGPS],
                'identificador' => "{$matricula}SAL{$this->anoCompetencia}{$this->mesCompetencia}"]
        ];
        $pagamento->ideDmDev = $tipoPagamento[$pagamento->tpPgto]['identificador'];
        $calculoFinanceiroDecimo = $servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_13o);
        // alterada validacao de 0 para -0.01 pois as vezes no calculo o retorno vem em
        // notacao cienticia negativa em valor
        // menor que -0.01 devido aos valores calculados, sendo considerado apenas o valor 0 no final
        if ($calculoFinanceiroDecimo->getValorLiquido() >= -0.01 &&
            !empty($calculoFinanceiroDecimo->getMovimentacoes())) {
            if ($this->mesCompetencia != 12 || $decimo) {
                $pagamentoDecimo = clone $pagamento;
                $pagamentoDecimo->ideDmDev = "{$matricula}ADIANT13{$this->anoCompetencia}{$this->mesCompetencia}";
                $pagamentoDecimo->perRef = "{$this->anoCompetencia}-{$this->mesCompetencia}";
                //VERIFICA SE O PAGAMENTO E REFERENTE AO DECIMO
                if ($decimo) {
                    $pagamentoDecimo->perRef = "{$this->anoCompetencia}";
                    if ($this->mesCompetencia == 12) {
                        switch ($this->anoCompetencia) {
                            case 2020:
                                $pagamentoDecimo->dtPgto = "{$this->anoCompetencia}-12-18";
                                break;
                            default:
                                $dataDecimo = DecimoServidorService::getDataDecimo(
                                    $servidor->getCgm()->getCodigo(),
                                    $this->anoCompetencia
                                );
                                if (!empty($dataDecimo)) {
                                    $pagamentoDecimo->dtPgto = $dataDecimo;
                                } else {
                                    $pagamentoDecimo->dtPgto = "{$this->anoCompetencia}-12-20";
                                }
                                break;
                        }
                    }
                    $pagamentoDecimo->ideDmDev = "{$matricula}DECIMO{$this->anoCompetencia}{$this->mesCompetencia}";
                    $pagamentoDecimo->ideDmDev .= "2";
                }
                $pagamentoDecimo->vrLiq = $this->truncar($calculoFinanceiroDecimo->getValorLiquido());
                // adicionada validacao de decimo
                if ($decimo) {
                    // caso o processamento do 13 esteja habilitado
                    if ($this->processa13) {
                        $pagamentos[$pagamentoDecimo->ideDmDev] = $pagamentoDecimo;
                    }
                } else {
                    if ($this->validaDataPagamento($pagamentoDecimo)) {
                        $pagamentos[$pagamentoDecimo->ideDmDev] = $pagamentoDecimo;
                    }
                }
            }
        }

        $calculoFinanceiroSalario = $servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_SALARIO);
        if ($calculoFinanceiroSalario->getValorLiquido() >= -0.01 && !$decimo &&
            !empty($calculoFinanceiroSalario->getMovimentacoes())) {
            $pagamentoSalario = clone $pagamento;
            $pagamentoSalario->vrLiq = $this->truncar($calculoFinanceiroSalario->getValorLiquido());
            $pagamentoSalario->ideDmDev = "{$matricula}SAL{$this->anoCompetencia}{$this->mesCompetencia}";
            $pagamentoSalario->perRef = "{$this->anoCompetencia}-{$this->mesCompetencia}";

            // Caso seja o mes 12, validamos se o processamento de dezembro está habilitado
            if ($this->mesCompetencia == 12) {
                if ($this->processaMes12) {
                    $pagamentos[$pagamentoSalario->ideDmDev] = $pagamentoSalario;
                }
            } else {
                if ($this->validaDataPagamento($pagamentoSalario)) {
                    $pagamentos[$pagamentoSalario->ideDmDev] = $pagamentoSalario;
                }
            }
        }

        $calculoFinanceiroComplementar = $servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_COMPLEMENTAR);

        if (DBPessoal::verificarUtilizacaoEstruturaSuplementar()) {
            $calculoFinanceiroComplementar->getEventosFinanceirosHistorico(FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR);
            $valorLiquidoComplementar = $calculoFinanceiroComplementar->getValorLiquidoComplementar();
        } else {
            $valorLiquidoComplementar = $calculoFinanceiroComplementar->getValorLiquido();
        }

        if ($valorLiquidoComplementar >= -0.01 && !$decimo &&
            !empty($calculoFinanceiroComplementar->getMovimentacoes())) {
            $pagamentoComplementar = clone $pagamento;
            $pagamentoComplementar->ideDmDev = "{$matricula}COMP{$this->anoCompetencia}{$this->mesCompetencia}";
            $pagamentoComplementar->vrLiq = $this->truncar($valorLiquidoComplementar);
            $pagamentoComplementar->perRef = "{$this->anoCompetencia}-{$this->mesCompetencia}";

            // Caso seja o mes 12, validamos se o processamento de dezembro está habilitado
            if ($this->mesCompetencia == 12) {
                if ($this->processaMes12) {
                    $pagamentos[$pagamentoComplementar->ideDmDev] = $pagamentoComplementar;
                }
            } else {
                if ($this->validaDataPagamento($pagamentoComplementar)) {
                    $pagamentos[$pagamentoComplementar->ideDmDev] = $pagamentoComplementar;
                }
            }
        }


        $calculoFinanceiroRescisao = $servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_RESCISAO);
        if ($calculoFinanceiroRescisao->getValorLiquido() >= -0.01 && !$decimo &&
            !empty($calculoFinanceiroRescisao->getMovimentacoes())) {
            $dataPagamentoRescisao = null;
            if (empty($servidor->getDataPagamentoRescisao())) {
                $dataPagamentoRescisao = new DBDate($this->buscarDataPagamentoFolha());
            } else {
                $dataPagamentoRescisao = new DBDate($servidor->getDataPagamentoRescisao()->format('Y-m-d'));
            }

            $pagamentoRescisao = clone $pagamento;
            $pagamentoRescisao->vrLiq = $this->truncar($calculoFinanceiroRescisao->getValorLiquido());
            $pagamentoRescisao->perRef = "{$this->anoCompetencia}-{$this->mesCompetencia}";
            $pagamentoRescisao->dtPgto = $dataPagamentoRescisao->getDate(DBDate::DATA_EN);
            $tipoPagamento = $pagamentoRescisao->tpPgto;
            $pagamentoRescisao->ideDmDev = "{$matricula}RESC{$this->anoCompetencia}{$this->mesCompetencia}";

            $codigoCategoria = (int) $servidor->getVinculo()->getCodigoCategoria();
            // Códigos categorias conforme regra definida redmine 24836
            $codigoCategoriaS2399 = [721];
            $codigoCategoriaS2299 = [101,102,103,104,105,106,107,109,111,501];

            if (in_array($codigoCategoria, $codigoCategoriaS2399)) {
                $pagamentoRescisao->tpPgto = $this->deParaTipoPagamento[TIPO::S2399_API];
            }

            if (in_array($codigoCategoria, $codigoCategoriaS2299)) {
                $pagamentoRescisao->tpPgto = $this->deParaTipoPagamento[TIPO::S2299_API];
            }
            $dataRescisao = null;
            if (!empty($servidor->getDataRescisao())) {
                $dataRescisao = $servidor->getDataRescisao()->format('Y-m-d');
            }
            $matricula = null;
            $matricula = $servidor->getMatricula();
            $dadoCompetenciaRescisao = null;
            $dadoCompetenciaRescisao = $servidor->getCompetenciaPagamentoRecisao($matricula);
            if (!(((int) $dadoCompetenciaRescisao->anocompetencia == (int) $this->anoCompetencia) &&
                ((int) $dadoCompetenciaRescisao->mescompetencia == (int) $this->mesCompetencia))) {
                $pagamentoRescisao->ideDmDev =
                    "{$matricula}RESPOS{$this->anoCompetencia}{$this->mesCompetencia}";
                //No caso a data de pagamento sempre será o do fechamento da folha
                $dataPagamentoRescisao = new DBDate($this->buscarDataPagamentoFolha());
                $pagamentoRescisao->dtPgto =$dataPagamentoRescisao->getDate(DBDate::DATA_EN);
                $tipoPagamentoPos = $servidor->getTipoPagamentoEsocialAPI();
                if (TIPO::S1207_API == $tipoPagamentoPos) {
                    $pagamentoRescisao->tpPgto = 5;
                }
                if (TIPO::S1202_API == $tipoPagamentoPos) {
                    $pagamentoRescisao->tpPgto = 4;
                }
                if (TIPO::S1200_API == $tipoPagamentoPos) {
                    $pagamentoRescisao->tpPgto = 1;
                }
                $validacao = true;
            } else {
                $validacao = false;
                $anoPagamento = (int) substr($pagamentoRescisao->dtPgto, 0, 4);
                $mesPagamento = (int) substr($pagamentoRescisao->dtPgto, 5, 2);
                if ((int) $this->getMesCompetenciaCaixa() == $mesPagamento &&
                   (int) $this->getAnoCompetenciaCaixa() == $anoPagamento) {
                    $validacao = true;
                }
            }
            if ($validacao) {
                $pagamentos[$pagamentoRescisao->ideDmDev] = $pagamentoRescisao;
            }
        }
        if (!empty($pagamentos)) {
            $this->qtdServidores += 1;
            $pagamento = $pagamentos;
            $servidoresPagamento[] = $servidor;
        } else {
            $pagamento = [];
        }
    }

    /**
     * Busca na api o número do recibo para os eventos S-2299 e S-2399 de acordo com a referencia informada
     * @param string $evento
     * @param string $referencia
     * @return string|null
     */
    private function buscarRecibo($evento, $referencia)
    {

        $body = new stdClass();
        $body->idReferencia = $referencia;
        $body->idEvento = $evento;
        $body->inscricaoEmpregador = $this->instituicaoSessao->getCNPJ();

        $service = new ESocial(Registry::get('app.config'), Recurso::CONSULTA_RECIBO);
        $service->setDados($body);

        $dados = $service->request('GET');
        if ($dados) {
            $dados = array_pop($dados);
            $dadosUltimoRecibo = $dados->recibo[0];
            return $dadosUltimoRecibo->numero;
        }

        return null;
    }

    public function setDecimoTerceiro()
    {
        $this->isDecimoTerceiro = true;
    }

    public function truncar($valor)
    {
        $valor = abs(round($valor, 6));
        $novoValor = (string)$valor;
        $novoValor = explode(".", $novoValor);
        if (sizeof($novoValor) > 1) {
            if (strlen($novoValor[1]) > 2) {
                $novoValor[1] = substr($novoValor[1], 0, 2);
                $valor = (float)($novoValor[0] . "." . $novoValor[1]);
            }
        }
        return $valor;
    }

    public function getQuantidadeServidores()
    {
        return $this->qtdServidores;
    }

    /** Metodo devera ser refeito futuramente pois estamos processando as folhas 2 vezes */
    private function validaQuantidadeServidores($servidor)
    {
        if (!empty($servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_13o))
            || !empty($servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_RESCISAO))
            || !empty($servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_SALARIO))
            || !empty($servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_COMPLEMENTAR))
        ) {
            $this->qtdServidores += 1;
        }
    }

    private function retornaQuantidadeServidores($numeroCgm = null)
    {
        if (!empty($numeroCgm)) {
            $sql = "select count(*) as qtdServidores from pessoal.rhpessoal where rh01_numcgm = {$numeroCgm} ";
            $resultado = db_query($sql);
            if (pg_numrows($resultado) == 1) {
                return (int)\db_utils::fieldsMemory($resultado, 0)->qtdservidores;
            }
        }
        return 1;
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

    /**
     * @param int $anoCompetenciaCaixa
     */
    public function setAnoCompetenciaCaixa($anoCompetenciaCaixa)
    {
        $this->anoCompetenciaCaixa = $anoCompetenciaCaixa;
    }

    /**
     * @param int $mesCompetenciaCaixa
     */
    public function setMesCompetenciaCaixa($mesCompetenciaCaixa)
    {
        $this->mesCompetenciaCaixa = $mesCompetenciaCaixa;
    }

    /**
     * @return int
     */
    public function getAnoCompetenciaCaixa()
    {
        return $this->anoCompetenciaCaixa;
    }

    /**
     * @return int
     */
    public function getMesCompetenciaCaixa()
    {
        return $this->mesCompetenciaCaixa;
    }

    /**
     * Funcao criada com a finalidade de validar se a rubrica do calculo esta configurada na operadoda de saude
     * caso positivo, o valor é adicionado ou substituido pelo valor da rubrica (caso informado valor
     *  na variavel $valoDesconto).
     *
     * @var $calcSalario []
     * @var $calcDecimo []
     * @var $calcFerias []
     * @var $rubricaDesc string
     * @var $valorDesconto float
     */
    private function processaDescontoSaude($calcSalario, $calcDecimo, $calcFerias, $rubricaDesc, $valorDesconto = 0)
    {
        foreach ($calcSalario as $salario) {
            $rubricaSalario = $salario->getRubrica()->getCodigo();
            if ($rubricaSalario === $rubricaDesc) {
                $valorDesconto += (float) $salario->getValor();
            }
        }
        foreach ($calcDecimo as $salario) {
            $rubricaDecimoTerceiro = $salario->getRubrica()->getCodigo();
            if ($rubricaDecimoTerceiro === $rubricaDesc) {
                $valorDesconto += (float) $salario->getValor();
            }
        }
        foreach ($calcFerias as $salario) {
            $rubricaFerias = $salario->getRubrica()->getCodigo();
            if ($rubricaFerias === $rubricaDesc) {
                $valorDesconto += (float) $salario->getValor();
            }
        }
        return (double) $valorDesconto;
    }

    /**
     * Funcao valida se a folha de pagamento atual realmente foi paga
     * na competencia de caixa informada na tela
     */
    private function validaDataPagamento($pagamento)
    {
        $retorno = true;
        $dataPagamento = new DBDate($pagamento->dtPgto);
        if ($dataPagamento->getMes() != $this->mesCompetenciaCaixa
            || $dataPagamento->getAno() != $this->anoCompetenciaCaixa
        ) {
            $retorno = false;
        }
        return $retorno;
    }


    /**
     * @param $competencias
     * Funcao para validar se existe alguma rescisao na competencia atual
     */
    private function validaCompetenciasRescisao($competencias)
    {
        $ultimaCompetencia = array_keys($competencias, max($competencias));
        if ($competencias[$ultimaCompetencia[0]]->mesusu == $this->mesCompetencia &&
            $competencias[$ultimaCompetencia[0]]->anousu == $this->anoCompetencia) {
            return true;
        }
        return false;
    }
}
