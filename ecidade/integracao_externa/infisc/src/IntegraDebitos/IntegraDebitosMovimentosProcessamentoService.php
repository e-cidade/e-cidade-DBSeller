<?php

namespace IntegracaoExterna\Infisc\IntegraDebitos;

use App\Domain\Tributario\ISSQN\Enums\Integracao\Infisc\IntegracaoInfiscStatusProcessamento;
use IntegracaoExterna\Infisc\Enums\TipoInscricaoEnum;

class IntegraDebitosMovimentosProcessamentoService
{
    const MAXIMO_ITENS_PROCESSAR = 1000;

    const CODIGO_IBGE_MUNICIPIO = 4318002;

    /**
     * @var IntegracaoExterna\Infisc\Managers\QueryManager
     */
    protected $origemManager;

    /**
     * @var IntegracaoExterna\Infisc\Manager\QueryManager
     */
    protected $destinoManager;

    /**
     * @var stdClass
     */
    private $dados;

    public function setDados($dados)
    {
        $this->dados = $dados;

        return $this;
    }

    /*
     * @param IntegracaoExterna\Infisc\Managers\QueryManager $origemManager
     * @param IntegracaoExterna\Infisc\Managers\QueryManager $destinoManager
     *
     **/

    //public function __construct(QueryManager $origemManager, QueryManager $destinoManager)
    public function __construct($origemManager, $destinoManager)
    {
        $this->origemManager = $origemManager;
        $this->destinoManager = $destinoManager;
    }

    /**
     * @throws Exception
     */
    public function processar()
    {
        $debitosOrigem = $this->buscarDebitosOrigem();

        if (!$debitosOrigem || count($debitosOrigem) == 0) {
            throw new Exception("Não foi encontrado debitos de origem.");
        }

        $dataLancamento = $this->buscarDataLancamento();

        foreach ($debitosOrigem as $debitoOrigem) {
            $codigoIssVar = $this->buscarCodigoIssVar($debitoOrigem->dv13_numpre, $debitoOrigem->dv13_numpar);
            $codigoIntegraDebitos = $codigoIssVar ? $this->buscarCodigoIntegraDebitos($codigoIssVar) : null;

            $this->inserirDados(
                $debitoOrigem->dv13_numpre,
                $debitoOrigem->dv13_numpar,
                $dataLancamento,
                $codigoIntegraDebitos
            );
        }
    }

    /**
     * @throws Exception
     */
    public function buscarItensPendentes()
    {
        $this->origemManager->query(
            "issqn.inscricao_debito_infisc_integra_debitos_mov",
            "*",
            [
                ["q197_status", IntegracaoInfiscStatusProcessamento::PENDENTE, "%s"]
            ],
            IntegraDebitosMovimentosProcessamentoService::MAXIMO_ITENS_PROCESSAR
        );

        if ($this->origemManager->hasError()) {
            throw new Exception("Erro ao buscar os itens para processar [{$this->origemManager->getError()}]");
        }

        return $this->origemManager->getCollection();
    }

    /**
     * @throws Exception
     */
    public function setarStatusProcessado()
    {
        $atualizado = $this->origemManager->update(
            "issqn.inscricao_debito_infisc_integra_debitos_mov",
            [sprintf("q197_status = '%s'", IntegracaoInfiscStatusProcessamento::PROCESSADO)],
            [["q197_sequencial", $this->dados->q197_sequencial]]
        );

        if (!$atualizado) {
            throw new Exception("Não foi possível setar o item como processado [{$this->origemManager->getError()}]");
        }
    }

    public function setarStatusErro()
    {
        return $this->origemManager->update(
            "issqn.inscricao_debito_infisc_integra_debitos_mov",
            [sprintf("q197_status = '%s'", IntegracaoInfiscStatusProcessamento::ERRO)],
            [["q197_sequencial", $this->dados->q197_sequencial]]
        );
    }

    /**
     * @throws Exception
     */
    private function inserirDados($numpre, $numpar, $dataLancamento, $codigoIntegraDebitos)
    {
        $dataCorrente = date("Y-m-d");
        $horaCorrente = date("H:i");

        $dadosSalvos = $this->destinoManager->insert(
            "integra_infisc.integra_debitos_movimentos",
            [
                ["munic_ibge", self::CODIGO_IBGE_MUNICIPIO],
                ["integra_debitos", ($codigoIntegraDebitos ?: "NULL")],
                ["numpre", $numpre],
                ["numpar", $numpar],
                ["tipo_inscricao", TipoInscricaoEnum::COBRANCA_ADMINISTRATIVA],
                ["data_lancamento", $dataLancamento, "s"],
                ["dataimp", $dataCorrente, "s"],
                ["horaimp", $horaCorrente, "s"],
                ["status_processamento", IntegracaoInfiscStatusProcessamento::PENDENTE, "s"]
            ]
        );

        if (!$dadosSalvos) {
            throw new Exception("Não foi possível incluir os dados {$this->destinoManager->getError()}");
        }
    }

    /**
     * @throws Exception
     */
    private function buscarDebitosOrigem()
    {
        $this->origemManager->query(
            "diversos.diverimportaold",
            "*",
            [
                ["dv13_diversos", $this->dados->q197_diversos]
            ]
        );

        if ($this->origemManager->hasError()) {
            throw new Exception("Erro ao buscar os debitos de origem [{$this->origemManager->getError()}]");
        }

        return $this->origemManager->getCollection();
    }

    /**
     * @throws Exception
     */
    private function buscarDataLancamento()
    {
        $this->origemManager->query(
            "diversos.diversos",
            "dv05_dtinsc",
            [
                ["dv05_coddiver", $this->dados->q197_diversos]
            ]
        );

        if ($this->origemManager->hasError()) {
            throw new Exception("Erro a data de lançammento [{$this->origemManager->getError()}]");
        }

        $dadosDiversos = $this->origemManager->get();
        return $dadosDiversos->dv05_dtinsc;
    }

    /**
     * @throws Exception
     */
    private function buscarCodigoIssVar($numpre, $numpar)
    {
        $this->origemManager->query("issqn.issvar", "q05_codigo", [["q05_numpre", $numpre], ["q05_numpar", $numpar]]);

        if ($this->origemManager->hasError()) {
            throw new Exception("Erro a buscar o código da ISSVAR [{$this->origemManager->getError()}]");
        }

        $dadosIssVar = $this->origemManager->get();
        return $dadosIssVar ? $dadosIssVar->q05_codigo : null;
    }

    /**
     * @throws Exception
     */
    private function buscarCodigoIntegraDebitos($codigoIssVar)
    {
        $this->origemManager->query(
            "issqn.issvar_infisc_integra_debitos",
            "q196_integra_debitos",
            [
                ["q196_issvar", $codigoIssVar]
            ],
            1,
            "q196_sequencial DESC"
        );

        if ($this->origemManager->hasError()) {
            throw new Exception("Erro a buscar o código da integra_debitos [{$this->origemManager->getError()}]");
        }

        $dadosIssvarInfiscIntegraDebitos = $this->origemManager->get();
        return $dadosIssvarInfiscIntegraDebitos ? $dadosIssvarInfiscIntegraDebitos->q196_integra_debitos : null;
    }
}
