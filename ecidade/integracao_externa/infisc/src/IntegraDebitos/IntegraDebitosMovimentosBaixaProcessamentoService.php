<?php

namespace IntegracaoExterna\Infisc\IntegraDebitos;

use App\Domain\Tributario\Arrecadacao\Models\Disarq;
use App\Domain\Tributario\Arrecadacao\Models\Disbanco;
use App\Domain\Tributario\Arrecadacao\Repositories\DisarqRepository;
use App\Domain\Tributario\Arrecadacao\Repositories\DisbancoRepository;
use AutenticacaoBaixaBanco;
use ECidade\Lib\Session\DefaultSession;
use IntegracaoExterna\Infisc\Enums\TipoLancamentoEnum;

class IntegraDebitosMovimentosBaixaProcessamentoService extends IntegraDebitosBaseService
{
    const BANCO = "041";

    const AGENCIA = "0380";

    /**
     * @var \cancelamentoDebitos
     */
    private $cancelaDebitoModel;

    /**
     * @var \CancelamentoISSQNVariavel
     */
    private $cancelamentoIssqnVariavel;

    /**
     * @var \stdClass|null
     */
    private $dadosIntegraDebitos;

    /**
     * @param \cancelamentoDebitos $cancelaDebitoModel
     * @return $this
     */
    public function setCancelaDebitoModel($cancelaDebitoModel)
    {
        $this->cancelaDebitoModel = $cancelaDebitoModel;

        return $this;
    }

    /**
     * @param \CancelamentoISSQNVariavel $cancelamentoIssqnVariavel
     * @return $this
     */
    public function setCancelamentoIssqnVariavel($cancelamentoIssqnVariavel)
    {
        $this->cancelamentoIssqnVariavel = $cancelamentoIssqnVariavel;

        return $this;
    }

    public function processar()
    {
        $this->dadosIntegraDebitos = $this->getDadosIntegraDebitos();

        switch ($this->dados->tipo_lancamento) {
            case TipoLancamentoEnum::PAGAMENTO:
                $this->processarPagamento();
                break;
            case TipoLancamentoEnum::CANCELAMENTO:
                $this->processarCancelamento();
                break;
            case TipoLancamentoEnum::REABERTURA_COMPETENCIA:
                $this->processarReaberturaCompetencia();
                break;
            case TipoLancamentoEnum::CANCELAMENTO_SEM_MOVIMENTO:
                $this->processarCancelamentoSemMovimento();
                break;
        }
    }

    /**
     * @throws \Exception
     */
    private function processarPagamento()
    {
        $dadosNumpreNumpar = $this->buscarNumpreNumpar();

        if ($this->existePagamento($dadosNumpreNumpar->numpre, $dadosNumpreNumpar->numpar)) {
            throw new \Exception("Débito já está pago.");
        }

        $conta = $this->buscarContaBanco();
        $codRet = $this->salvarDisarq($conta);
        $this->salvarDisbanco($codRet, $dadosNumpreNumpar->numpre, $dadosNumpreNumpar->numpar);
        $this->executaBaixaBanco($codRet);
//        $this->autenticar($codRet);
    }

    /**
     * @throws \Exception
     */
    private function processarCancelamento()
    {
        $dadosNumpreNumpar = $this->buscarNumpreNumpar();

        if ($this->existePagamento($dadosNumpreNumpar->numpre, $dadosNumpreNumpar->numpar)) {
            throw new \Exception("Não é possível processar o cancelamento de um débito pago.");
        }

        $issVarInfo = $this->getIssVarInfo(
            null,
            null,
            null,
            null,
            null,
            $dadosNumpreNumpar->numpre,
            $dadosNumpreNumpar->numpar
        );

        if (!$issVarInfo) {
            throw new \Exception("Não foi identificado débito para cancelamento.");
        }

        try {
            $debito = [
                "Numpre" => $issVarInfo->q05_numpre,
                "Numpar" => $issVarInfo->q05_numpar,
                "Receita" => $issVarInfo->k00_receit
            ];

            $motivo = "Cancelamento gerado pela Integração com a INFISC. {$this->dados->motivo}";
            $this->cancelaDebitoModel->setArreHistTXT(substr($motivo, 0, 50));
            $this->cancelaDebitoModel->geraCancelamento([$debito]);
        } catch (\Exception $eException) {
            throw new \Exception("Cancelamento do débito: {$eException->getMessage()}");
        }
    }

    /**
     * @throws \Exception
     */
    private function processarReaberturaCompetencia()
    {
        $dadosNumpreNumpar = $this->buscarNumpreNumpar();

        if ($this->existePagamento($dadosNumpreNumpar->numpre, $dadosNumpreNumpar->numpar)) {
            throw new \Exception("Não é possível processar a reabertura de um débito pago.");
        }

        $cancdebitosregDados = $this->origemManager->query(
            "cancdebitosreg",
            "k21_receit",
            [
                ["k21_numpre", $dadosNumpreNumpar->numpre],
                ["k21_numpar", $dadosNumpreNumpar->numpar]
            ],
            1
        )->get();

        $zeraValoresDebito = true;

        $issVarInfo = $this->getIssVarInfo(
            null,
            null,
            null,
            null,
            null,
            $dadosNumpreNumpar->numpre,
            $dadosNumpreNumpar->numpar
        );

        if ($cancdebitosregDados) {
            $canceladoSemMovimento = $this->origemManager->query(
                "issvarsemmovreg",
                "1",
                [
                    ["q15_issvar", $issVarInfo->q05_codigo]
                ],
                1
            )->exists();

            if ($canceladoSemMovimento) {
                $this->cancelamentoIssqnVariavel->addDebito($dadosNumpreNumpar->numpre, $dadosNumpreNumpar->numpar);
                $this->cancelamentoIssqnVariavel->excluirCancelamento();
            } else {
                try {
                    $debito = [
                        "Numpre" => $issVarInfo->q05_numpre,
                        "Numpar" => $issVarInfo->q05_numpar,
                        "Receita" => $cancdebitosregDados->k21_receit,
                        "Receit" => $cancdebitosregDados->k21_receit
                    ];

                    $this->cancelaDebitoModel->excluiCancelamento([$debito]);
                } catch (Exception $eException) {
                    throw new \Exception("Cancelamento do débito: {$eException->getMessage()}");
                }
            }
        } else {
            $codigoTipoIssRetido = "33";

            if ($issVarInfo && $issVarInfo->k00_tipo && $issVarInfo->k00_tipo == $codigoTipoIssRetido) {
                $zeraValoresDebito = false;
            }
        }

        if ($zeraValoresDebito) {
            $this->zeraValorDebito($dadosNumpreNumpar->numpre, $dadosNumpreNumpar->numpar);
        }
    }

    /**
     * @throws \DBException
     * @throws \BusinessException
     * @throws \Exception
     */
    private function processarCancelamentoSemMovimento()
    {
        $dadosNumpreNumpar = $this->buscarNumpreNumpar();

        if ($this->existePagamento($dadosNumpreNumpar->numpre, $dadosNumpreNumpar->numpar)) {
            throw new \Exception("Não é possível processar a reabertura de um débito pago.");
        }

        $codigoInscricao = $this->buscarCodigoInscricao(
            $this->dadosIntegraDebitos->integra_empresas,
            $this->dadosIntegraDebitos->cnpj,
            $dadosNumpreNumpar->numpre
        );

        if (!$codigoInscricao) {
            throw new \Exception("Não foi possível identificar a inscrição.");
        }

        $sCaminhoScript = getcwd();
        chdir('../../');
            $empresa = new \Empresa($codigoInscricao);
        chdir($sCaminhoScript);


        $this->cancelamentoIssqnVariavel->setEmpresa($empresa);
        $this->cancelamentoIssqnVariavel->addDebito($dadosNumpreNumpar->numpre, $dadosNumpreNumpar->numpar);
        $this->cancelamentoIssqnVariavel->setObservacao("Cancelado a partir da integração com a INFISC.");
        $this->cancelamentoIssqnVariavel->incluirCancelamento(null);
    }

    /**
     * @throws \Exception
     */
    private function buscarNumpreNumpar()
    {
        if ($this->dados->numpre && $this->dados->numpar) {
            return (object) ["numpre" => $this->dados->numpre, "numpar" => $this->dados->numpar];
        }

        if ($this->dadosIntegraDebitos) {
            $dadosIssvar = $this->getIssVarInfo(
                null,
                null,
                null,
                null,
                $this->dadosIntegraDebitos->sequencial
            );

            if ($dadosIssvar) {
                return (object) ["numpre" => $dadosIssvar->q05_numpre, "numpar" => $dadosIssvar->q05_numpar];
            }
        }

        throw new \Exception("Não foi possível identificar o numpre e numpar.");
    }

    /**
     * @throws \Exception
     */
    private function buscarContaBanco()
    {
        $dadosBanco = $this->origemManager->query(
            "cadban",
            "k15_conta",
            [
                sprintf("k15_codbco = %d", intval(self::BANCO)),
                sprintf("k15_codage = '%s'", self::AGENCIA)
            ]
        )->get();

        if (!$dadosBanco || !$dadosBanco->k15_conta) {
            throw new \Exception("Conta não encontrada.");
        }

        return $dadosBanco->k15_conta;
    }

    /**
     * @throws \Exception
     */
    private function salvarDisarq($conta)
    {
        $disarqRepository = new DisarqRepository();
        $disarq = new Disarq();

        $disarq->setCodbco(self::BANCO);
        $disarq->setCodage(self::AGENCIA);
        $disarq->setArqret("BAIXA INTEGRAÇÃO INFISC");
        $disarq->setDtretorno($this->dados->data);
        $disarq->setDtarquivo($this->dados->data);
        $disarq->setConta($conta);
        $disarq->setAutent("true");
        $disarq->setIdUsuario(db_getsession(DefaultSession::DB_ID_USUARIO));
        $disarq->setInstit(db_getsession(DefaultSession::DB_INSTIT));

        return $disarqRepository->salvar($disarq);
    }

    /**
     * @throws \Exception
     */
    private function salvarDisbanco($codRet, $numpre, $numpar)
    {
        $disbancoRepository = new DisbancoRepository();
        $disbanco = new Disbanco();

        $disbanco->setCodbco(self::BANCO);
        $disbanco->setCodage(trim(self::AGENCIA));
        $disbanco->setCodret($codRet);
        $disbanco->setDtarq($this->dados->data);
        $disbanco->setDtpago($this->dados->data);
        $disbanco->setDtcredito($this->dados->data);

        $valorPago = floatval($this->dados->valor_principal) + floatval($this->dados->valor_correcao);
        $disbanco->setVlrpago($valorPago);
        $disbanco->setVlrtot($valorPago);
        $disbanco->setVlrjuros(floatval($this->dados->valor_juros));
        $disbanco->setVlrmulta(floatval($this->dados->valor_multa));
        $disbanco->setVlrcalc("0");

        $disbanco->setNumpre($numpre);
        $disbanco->setNumpar($numpar);
        $disbanco->setClassi("false");
        $disbanco->setInstit(db_getsession(DefaultSession::DB_INSTIT));
        $disbanco->setBancopagamento(trim(self::BANCO));
        $disbanco->setAgenciapagamento(trim(self::AGENCIA));

        $disbancoRepository->salvar($disbanco);
    }

    /**
     * @throws \Exception
     */
    private function executaBaixaBanco($codRet)
    {
        $oRetorno = fc_executa_baixa_banco($codRet, $this->dados->data);
        if ($oRetorno->processado == "f") {
            throw new \Exception("Erro na baixa de banco: {$oRetorno->descricao}");
        }
    }

    /**
     * @throws BusinessException
     * @throws \Exception
     */
    private function autenticar($codRet)
    {
        $disarqRepository = new DisarqRepository();
        $oDisarq = $disarqRepository->getCodclaByCodretAndInstit(
            $codRet,
            db_getsession(DefaultSession::DB_INSTIT),
            ["codcla"]
        );

        $sCaminhoScript = getcwd();
        chdir('../../');
            $autenticacaoBaixaBanco = new AutenticacaoBaixaBanco($oDisarq->codcla);
            $autenticacaoBaixaBanco->autenticar();
        chdir($sCaminhoScript);
    }

    private function existePagamento($numpre, $numpar)
    {
        $this->origemManager->query("arrepaga", "1", [["k00_numpre", $numpre], ["k00_numpar", $numpar]], 1);

        if ($this->origemManager->get()) {
            return true;
        }

        return false;
    }

    /**
     * @throws \Exception
     */
    private function zeraValorDebito($numpre, $numpar)
    {
        $issVarInfo = $this->getIssVarInfo(
            null,
            null,
            null,
            null,
            null,
            $numpre,
            $numpar
        );

        if (!$issVarInfo) {
            throw new \Exception("Não foi identificado débito reaberto.");
        }

        $daoIssVar = new \cl_issvar();
        $daoIssVar->q05_bruto = "0";
        $daoIssVar->q05_codigo = $issVarInfo->q05_codigo;
        $daoIssVar->alterar($issVarInfo->q05_codigo);

        if ($daoIssVar->erro_status == "0") {
            throw new \Exception($daoIssVar->erro_msg);
        }

        $daoArrecad = new \cl_arrecad();
        $daoArrecad->k00_valor = "0";
        $daoArrecad->alterar_arrecad(
            "k00_numpre = {$issVarInfo->q05_numpre} and k00_numpar = {$issVarInfo->q05_numpar}"
        );

        if ($daoArrecad->erro_status == '0') {
            throw new \Exception($daoArrecad->erro_msg);
        }
    }

    private function getDadosIntegraDebitos()
    {
        if (!$this->dados->integra_debitos) {
            return null;
        }

        return $this->destinoManager->query(
            "integra_infisc.integra_debitos",
            "*",
            ["sequencial = {$this->dados->integra_debitos}"]
        )->get();
    }
}
