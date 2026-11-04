<?php


namespace ECidade\Financeiro\Contabilidade\ExercicioContabil;

use ECidade\Financeiro\Contabilidade\LancamentoContabil\Documento;
use EventoContabil;
use Exception;
use stdClass;

/**
 * Class Abertura
 * @package ECidade\Financeiro\Contabilidade\ExercicioContabil
 */
class Abertura extends ExercicioContabil
{

    /**
     * @var \AberturaExercicioOrcamento
     */
    protected $abertura;

    protected $dataAbertura;

    public function getDataAbertura()
    {
        return $this->dataAbertura;
    }

    public function setDataAbertura(\DBDate $data)
    {
        $this->dataAbertura = $data;
    }

    /**
     *
     * Abertura constructor.
     * @param $ano
     * @param \DBDate $data
     * @param \Instituicao $instituicao
     */
    public function __construct($ano, \DBDate $data, \Instituicao $instituicao)
    {
        $this->setDataAbertura($data);

        $this->nomeLog = sprintf(
            "abertura_%s_%s.log",
            (new \DateTime())->format('Y-m-d_H:i:s'),
            $instituicao->getCodigo()
        );
        parent::__construct($ano, $data, $instituicao);
    }

    /**
     * @return array
     */
    public function getDocumentosParaProcessamento()
    {
        return [
            Documento::ABERTURA_ORCAMENTO_RECEITA,
            Documento::ABERTURA_ORCAMENTO_DESPESA,
            Documento::ABERTURA_TRANSFERENCIA_SALDOS_RPNP_INSCRITOS_EX_ANT,
            Documento::ABERTURA_TRANSFERENCIA_SALDOS_RPP_INSCRITOS_EX_ANT,
            Documento::TRANSFERENCIA_SALDOS_RPNP_INSCRITOS_LIQUIDAR,
            Documento::TRANSFERENCIA_SALDOS_RPNP_INSCRITOS_EM_LIQUIDACAO,
        ];
    }

    protected function cancelarDocumento($codigoDocumento)
    {
        $where = "c71_coddoc = {$codigoDocumento} and c70_anousu = {$this->ano} ";
        $where .= " and c02_instit = {$this->instituicao->getCodigo()}";
        $daoAberturaExercicio = new \cl_conlancamaberturaexercicioorcamento();
        $consultaAbertura = $daoAberturaExercicio->sql_query_documento(
            null,
            '*',
            'c71_coddoc',
            $where
        );

        $rsAbertura = db_query($consultaAbertura);
        $totalRegistros = pg_num_rows($rsAbertura);
        for ($i = 0; $i < $totalRegistros; $i++) {
            $dados = \db_utils::fieldsMemory($rsAbertura, $i);
            $daoAberturaExercicio->excluir($dados->c105_aberturaexercicioorcamento);
            if ($daoAberturaExercicio->erro_status == 0) {
                $mensagem = 'Não foi possível cancelar a abertura do documento ' . $codigoDocumento;
                $mensagem .= " Erro técnico: {$daoAberturaExercicio->erro_msg}";
                throw new Exception($mensagem);
            }
            \lancamentoContabil::excluirLancamento($dados->c105_codlan);
        }
        return true;
    }

    /**
     * Realiza o cancelamento do exercicio contabil.
     * consiste em excluir os dados de abertura
     * @return boolean true
     * @throws Exception
     */
    public function cancelar(array $documentos)
    {
        foreach ($documentos as $codigoDocumento) {
            $this->cancelarDocumento($codigoDocumento);
        }
        $this->excluiAberturaDocumento($documentos);
        $this->excluiAbertura();
        return true;
    }

    /**
     * Processa os
     * @throws Exception
     */
    public function processar($documentos)
    {
        db_putsession('DB_desativar_account', true);
        $this->abertura = \AberturaExercicioOrcamento::getInstanciaPorAnoInstituicao(
            $this->ano,
            $this->instituicao->getCodigo()
        );
        $this->abertura->setCodigoUsuario(db_getsession("DB_id_usuario"));
        $this->abertura->setCodigoInstituicao(db_getsession("DB_instit"));
        $this->abertura->setAno($this->ano);
        $this->abertura->setDataProcessamento(new \DBDate(date('Y-m-d', db_getsession("DB_datausu"))));
        $this->abertura->setProcessado(true);
        $this->abertura->salvar();

        foreach ($this->getDocumentosParaProcessamento() as $documento) {
            if (!in_array($documento, $documentos)) {
                continue;
            }
            $this->processarDocumento($documento);
            $this->salvarAberturaDocumento($documento);
        }

        if (in_array(2021, $documentos)) {
            /**
             * aqui vai fazer os lancamentos dos saldos do ano anterior
             * para o estrutural 8111101
             */
            $this->processarFonteRecursoAnterior();
            $this->salvarAberturaDocumento(2021);
        }
    }

    public function processarFonteRecursoAnterior()
    {
        $data = $this->getDataAbertura()->getdate("Y-m-d");

        $iCodigoDocumento = 2021;
        $dados = $this->obterDadosConferenciaPorRecurso();

        foreach ($dados as $oDados) {
            $sObservacaoHistorico = "Pela transferência dos recursos da DDR líquida do exercício anterior, ";
            $sObservacaoHistorico .= "na fonte: {$oDados->fonte_recurso}";

            $nValor = $oDados->valor_disponibilidade;
            $lEstorno = true;

            if ($nValor < 0) {
                $nValor = $nValor * -1;
                $lEstorno = false;
            }

            $oRecurso = \RecursoRepository::getRecursoPorCodigo($oDados->o15_codigo);
            $oLancamentoAuxiliar = new \LancamentoAuxiliarRecursosExercicioAnteriorControles();
            $oLancamentoAuxiliar->setObservacaoHistorico($sObservacaoHistorico);
            $oLancamentoAuxiliar->setValorTotal($nValor);
            $oLancamentoAuxiliar->setHistorico(9601);
            $oLancamentoAuxiliar->setEstorno($lEstorno);
            $oLancamentoAuxiliar->setRecurso($oRecurso);
            $oLancamentoAuxiliar->setCodigoDocumento(2021);
            $oLancamentoAuxiliar->setCodigoAbertura($this->abertura->getCodigo());

            $oEventoContabil = new EventoContabil($iCodigoDocumento, $this->ano);
            $oEventoContabil->executaLancamento($oLancamentoAuxiliar, $data);
        }
    }


    public function obterDadosConferenciaPorRecurso(array $instituicoes = [])
    {
        $data = $this->ano - 1 . "-01-01";
        if (empty($instituicoes)) {
            $instituicoes = db_getsession("DB_instit");
        }

        $aDadosRelatorio = [];
        $sqlReduz = "
        (SELECT array_agg(c61_reduz)
           FROM (
            SELECT c61_reduz
              FROM contabilidade.conplano
              JOIN contabilidade.conplanoreduz ON (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
              WHERE c61_instit IN ({$instituicoes})
                AND c61_anousu = 2023
                AND (c60_estrut LIKE '8211101%')
           ) AS x)::int[]
       ";
        $pl = "contabilidade.balancete_verificacao_por_recurso ($this->ano, '$data', '$data', false, $sqlReduz)";

        $sql = "
            select x.*
              from {$pl} x

        ";

        $rs = db_query($sql);
        $linhas = pg_num_rows($rs);

        for ($i = 0; $i < $linhas; $i++) {
            $oDadosConsulta = \db_utils::fieldsMemory($rs, $i);

            if ($oDadosConsulta->saldo_anterior == 0) {
                continue;
            }

            $oDados = new stdClass();
            $oDados->fonte_recurso = sprintf(
                '%s - %s - %s',
                $oDadosConsulta->siconfi,
                $oDadosConsulta->subrecurso,
                $oDadosConsulta->complemento
            );
            $oDados->recurso = $oDadosConsulta->subrecurso;
            $oDados->o15_codigo = $oDadosConsulta->id_recurso;
            $oDados->o15_complemento = $oDadosConsulta->complemento;
            $oDados->valor_disponibilidade = $oDadosConsulta->saldo_anterior;

            $aDadosRelatorio[] = $oDados;
        }

        return $aDadosRelatorio;
    }


    /**
     * Processa os dados do lancamento Contábil
     * @param $documento
     * @throws Exception
     */
    protected function processarDocumento($documento)
    {
        /**
         *  1 - inserir na tabela de encerramento. Ok
         *  2 - buscar a query com os dados do documnento para serem lancados - Ok
         *  3 - Realizar os lancamentos - ok
         */
        $sql = $this->getConsultaLancamentosDoDocumento($documento);
        $rsLancamentos = db_query($sql);

        if (!$rsLancamentos) {
            $mensagem = "Não foi possível executar regra para definição dos lançamentos do ";
            $mensagem .= "documento {$documento} " . pg_last_error();
            throw new Exception($mensagem);
        }

        $instancia = $this;
        $eventoContabil = new EventoContabil($documento, $this->ano);

        \db_utils::makeCollectionFromRecord($rsLancamentos, function ($dados) use (
            $instancia,
            $documento,
            $eventoContabil
        ) {
            $instancia->mensagensLog = array();
            $lancamentoAuxiliar = $instancia->getLancamentoAuxiliar($documento, $dados);
            if (empty($lancamentoAuxiliar)) {
                throw new Exception("Não foi possível executar identificar o o lancamento. ");
            }
            
            $codigoLancamento = $eventoContabil->executaLancamento($lancamentoAuxiliar, $instancia->data->getDate());
            if (!empty($instancia->mensagensLog)) {
                $instancia->logger->warning("Código do Lançamento: " . $codigoLancamento);
                $instancia->logger->write(implode("\n", $instancia->mensagensLog) . "\n");
            }
        });
    }

    /**
     * Retorna o lancamento auxiliar
     * @param $documento
     * @param $dados
     * @return \LancamentoAuxiliarAberturaExercicioOrcamento
     */
    protected function getLancamentoAuxiliar($documento, $dados)
    {
        $lancamentoAuxiliar = new \LancamentoAuxiliarAberturaExercicioOrcamento();
        $lancamentoAuxiliar->setHistorico(9600);
        $lancamentoAuxiliar->setAberturaExercicioOrcamento($this->abertura->getCodigo());
        $lancamentoAuxiliar->setCodigoRecurso($dados->codigo_recurso);
        $lancamentoAuxiliar->setCodigoDocumento($documento);
        switch ($documento) {
            case Documento::ABERTURA_ORCAMENTO_RECEITA:
                $observacao = 'Lançamento automático de abertura do exercício de ' . $this->ano;
                $receita = \ReceitaContabilRepository::getReceitaByCodigo($dados->receita, $this->ano);

                $lancamentoAuxiliar->setObservacaoHistorico($observacao);
                $lancamentoAuxiliar->setValorTotal(abs($dados->valor));
                $lancamentoAuxiliar->setReceita($receita);
                return $lancamentoAuxiliar;

            case Documento::ABERTURA_ORCAMENTO_DESPESA:
                $dotacao = new \Dotacao(null, null);
                $dotacao->setValor($dados->valor);
                $dotacao->setCodigo($dados->dotacao);
                $dotacao->setAno($dados->ano);
                $observacao = 'Lançamento automático de abertura da despesa do exercício de ' . $this->ano;
                $lancamentoAuxiliar->setObservacaoHistorico($observacao);
                $lancamentoAuxiliar->setValorTotal(abs($dados->valor));
                $lancamentoAuxiliar->setDotacao($dotacao);
                return $lancamentoAuxiliar;

            case Documento::ABERTURA_TRANSFERENCIA_SALDOS_RPNP_INSCRITOS_EX_ANT:
            case Documento::ABERTURA_TRANSFERENCIA_SALDOS_RPP_INSCRITOS_EX_ANT:
            case Documento::TRANSFERENCIA_SALDOS_RPNP_INSCRITOS_LIQUIDAR:
            case Documento::TRANSFERENCIA_SALDOS_RPNP_INSCRITOS_EM_LIQUIDACAO:
                $observacao = 'Lançamento automático de transferência de saldos de RPNP do exercício de ' . $this->ano;
                $lancamentoAuxiliar->setObservacaoHistorico($observacao);
                $lancamentoAuxiliar->setValorTotal($dados->valor);

                if (!empty($dados->empenho)) {
                    $empenhoFinanceiro = \EmpenhoFinanceiroRepository::getEmpenhoFinanceiroPorNumero($dados->empenho);
                    $lancamentoAuxiliar->setCodigoElemento($empenhoFinanceiro->getDesdobramentoEmpenho());
                    $lancamentoAuxiliar->setFavorecido($empenhoFinanceiro->getCgm()->getCodigo());
                    $lancamentoAuxiliar->setNumeroEmpenho($dados->empenho);
                }
                return $lancamentoAuxiliar;
        }
        return null;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function possuiAberturaExercicioNoAnoInstituicao()
    {
        $oDaoAberturaExercicio = new \cl_aberturaexercicioorcamento();
        $where = "c104_ano = {$this->ano}
            and c104_instit = {$this->instituicao->getCodigo()}
            and c104_processado is true
        ";
        $sqlAberturaProcessada = $oDaoAberturaExercicio->sql_query_file(null, '*', null, $where);
        $buscaAbertura = db_query($sqlAberturaProcessada);
        if (!$buscaAbertura) {
            throw new Exception("Ocorreu um erro para consultar a existência de abertura processada.");
        }
        return pg_num_rows($buscaAbertura) > 0;
    }

    /**
     * @param array $documentos
     * @return array
     */
    public function validaDocumentosProcessados(array $documentos)
    {
        $where = [
            "c149_anousu = {$this->ano}",
            "c149_instit = {$this->instituicao->getCodigo()}",
        ];
        if (!empty($documentos)) {
            $where[] = "c149_documento in (" . implode(',', $documentos) . ")";
        }
        $dao = new \cl_aberturaexerciciodocumento();
        $sql = $dao->sql_query_file(null, 'c149_documento', null, implode(' and ', $where));
        $rs = db_query($sql);

        $documentosEncerrados = [];
        if ($rs && pg_num_rows($rs) > 0) {
            $documentosEncerrados = \db_utils::makeCollectionFromRecord($rs, function ($dado) {
                return $dado->c149_documento;
            });
        }

        return $documentosEncerrados;
    }

    private function salvarAberturaDocumento($documento)
    {
        $data = (new \DateTime())->format('Y-m-d H:i:s');

        $dao = new \cl_aberturaexerciciodocumento();
        $dao->c149_anousu = $this->ano;
        $dao->c149_documento = $documento;
        $dao->c149_instit = $this->instituicao->getCodigo();
        $dao->c149_usuario = db_getsession("DB_id_usuario");
        $dao->created_at = $data;
        $dao->updated_at = $data;
        $dao->incluir(null);

        if ($dao->erro_status == 0) {
            throw new Exception("Erro ao salvar a abertura do documento {$documento}.");
        }
        return true;
    }

    /**
     * @param array $documentos
     * @return void
     * @throws Exception
     */
    public function excluiAberturaDocumento(array $documentos)
    {
        $dao = new \cl_aberturaexerciciodocumento();
        $where = [
            "c149_anousu = {$this->ano}",
            "c149_instit = {$this->instituicao->getCodigo()}",
        ];
        if (!empty($documentos)) {
            $where[] = "c149_documento in (" . implode(',', $documentos) . ")";
        }
        $dao->excluir(null, implode(' and ', $where));
        if ($dao->erro_status == 0) {
            $msg = "Erro ao excluir documento da abertura: {$dao->erro_msg}";
            throw new Exception($msg);
        }
    }

    private function excluiAbertura()
    {
        $documentosEncerrados = $this->validaDocumentosProcessados([]);
        if (!empty($documentosEncerrados)) {
            return;
        }
        $oDaoAberturaExercicio = new \cl_aberturaexercicioorcamento();
        $where = "c104_ano = {$this->ano}  and c104_instit = {$this->instituicao->getCodigo()}";
        $oDaoAberturaExercicio->excluir(null, $where);
        if ($oDaoAberturaExercicio->erro_status == 0) {
            $msg = "Ocorreu algo inexperado ao excluir a abertura do ";
            $msg .= "exercício contabil. {$oDaoAberturaExercicio->erro_msg}";
            throw new Exception($msg);
        }
    }
}
