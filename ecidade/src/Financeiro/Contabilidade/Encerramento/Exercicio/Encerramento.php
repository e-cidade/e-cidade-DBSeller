<?php

namespace ECidade\Financeiro\Contabilidade\Encerramento\Exercicio;

use cl_conencerramentolancam;
use db_utils;
use ECidade\Financeiro\Contabilidade\Encerramento\PeriodoContabil;
use ECidade\Financeiro\Contabilidade\ExercicioContabil\ExercicioContabil;
use \ECidade\Financeiro\Contabilidade\LancamentoContabil\Documento;
use ECidade\Tributario\Configuracao\Entity\Instituicao;
use ECidade\V3\Extension\Logger;
use EventoContabil;
use \lancamentoContabil;
use ECidade\Financeiro\Contabilidade\ExercicioContabil\Abertura;

class Encerramento extends ExercicioContabil
{
    /**
     * Encerramento das restos a pagar
     */
    const ENCERRAR_RESTOS_A_PAGAR = 1;

    /**
     * Encerramento das Variacoes patrimoniais
     */
    const ENCERRAR_VARIACOES_PATRIMONIAIS = 6;

    /**
     * Encerramento do sistema orcamentario e controle
     */
    const ENCERRAR_SISTEMA_ORCAMENTARIO_CONTROLE = 7;

    /**
     * Encerramento da Execucao Orcamentaria da Despesa
     */
    const ENCERRAR_EXECUCAO_ORCAMENTARIA_DESPESA = 8;

    const ENCERRAMENTO_TRANSFERENCIA_SALDO = 4;

    protected $sTipoEncerramento;

    /**
     * lista de encerramentos realizados
     * @var array
     */
    private $encerramentos = array();

    /**
     * Códigos de documentos que devem ser cancelados o encerramento
     * @var array
     */
    private $documentosParaCancelar = array();

    /**
     * @return mixed
     */
    public function getTipoEncerramento()
    {
        return $this->sTipoEncerramento;
    }

    /**
     * @param mixed $tipo de encerramento
     */
    public function setTipoEncerramento($sTipoEncerramento)
    {
        $this->sTipoEncerramento = $sTipoEncerramento;
    }

    /**
     * Encerramento constructor.
     */
    public function __construct($ano, \DBDate $data, \Instituicao $instituicao)
    {
        $dataArquivo = str_replace('/', '-', $data);
        $this->nomeLog = "encerramento_{$dataArquivo}_{$instituicao->getCodigo()}.log";
        parent::__construct($ano, $data, $instituicao);
    }

    /**
     * Documentos que devem ser Encerrados
     * @return array
     */
    public function getDocumentosParaProcessamento()
    {
        return array(


            Documento::ENCERRAMENTO_TRANSFERENCIA_SALDOS_RPNP_EX_ANT => self::ENCERRAMENTO_TRANSFERENCIA_SALDO,// 2030;
            Documento::ENCERRAMENTO_TRANSFERENCIA_SALDOS_RPP_EX_ANT => self::ENCERRAMENTO_TRANSFERENCIA_SALDO, // 2031;

            //const ABERTURA_TRANSFERENCIA_SALDOS_RPNP_EX_ANT = 2030;
            //const ABERTURA_TRANSFERENCIA_SALDOS_RPP_EX_ANT = 2031;


            Documento::ENCERRAMENTO_RPNP_LIQUIDAR_EXERCICIO => self::ENCERRAR_RESTOS_A_PAGAR, //1011
            Documento::ENCERRAMENTO_RPNP_LIQUIDACAO_EXERCICIO => self::ENCERRAR_RESTOS_A_PAGAR, //1012
            Documento::ENCERRAMENTO_INSCRICAO_RPP_EXERCICIO => self::ENCERRAR_RESTOS_A_PAGAR, //1013
            Documento::ENCERRAMENTO_INSCRICAO_RPNP_PAGO_EXERCICIO => self::ENCERRAR_RESTOS_A_PAGAR, // 1014
            Documento::ENCERRAMENTO_INSCRICAO_RPNP_CANCELADO_EXERCICIO => self::ENCERRAR_RESTOS_A_PAGAR, // 1015
            Documento::ENCERRAMENTO_INSCRICAO_RPP_PAGOS_EXERCICIO => self::ENCERRAR_RESTOS_A_PAGAR, // 1016
            Documento::ENCERRAMENTO_INSCRICAO_RPP_CANCELADO_EXERCICIO => self::ENCERRAR_RESTOS_A_PAGAR, // 1017
            Documento::ENCERRAMENTO_TRANSFERENCIA_RPNP_RP => self::ENCERRAR_RESTOS_A_PAGAR,  // 1018
            Documento::ENCERRAMENTO_VARIACOES_PATRIMONIAIS => self::ENCERRAR_VARIACOES_PATRIMONIAIS, //1009
            Documento::ENCERRAMENTO_NATUREZA_ORCAMENTARIA_CONTROLE_RECEITA =>
                self::ENCERRAR_SISTEMA_ORCAMENTARIO_CONTROLE,  // 1010
            Documento::ENCERRAMENTO_RECEITA_REALIZADA =>
                self::ENCERRAR_SISTEMA_ORCAMENTARIO_CONTROLE,  // 1020
            Documento::ENCERRAMENTO_RECEITA_BRUTA =>
                self::ENCERRAR_SISTEMA_ORCAMENTARIO_CONTROLE,  // 1021
            Documento::ENCERRAMENTO_NATUREZA_ORCAMENTARIA_CONTROLE_DESPESA =>
                self::ENCERRAR_SISTEMA_ORCAMENTARIO_CONTROLE,  // 1019
            Documento::ENCERRAMENTO_DDR_REALIZADA => // 1022
                self::ENCERRAR_SISTEMA_ORCAMENTARIO_CONTROLE,
            Documento::ENCERRAMENTO_CONTRATOS_CONVENIOS_EXECUTADOS => // 1022
                self::ENCERRAR_SISTEMA_ORCAMENTARIO_CONTROLE,
        );
    }

    public function getDocumentosParaProcessamentoEncerramentoOrcamentario()
    {
        return array(

            Documento::ENCERRAMENTO_EXERC_ORC_DESP_LIQUIDAR => self::ENCERRAR_EXECUCAO_ORCAMENTARIA_DESPESA,// 1024;
            Documento::ENCERRAMENTO_EXERC_ORC_DESP_LIQUIDACAO => self::ENCERRAR_EXECUCAO_ORCAMENTARIA_DESPESA, // 1025;
            Documento::ENCERRAMENTO_EXERC_ORC_DESP_LIQUIDADOS => self::ENCERRAR_EXECUCAO_ORCAMENTARIA_DESPESA, // 1026;

        );
    }


    /**
     * @param array $documentos
     */
    public function setDocumentosCancelar(array $documentos)
    {
        $this->documentosParaCancelar = $documentos;
    }

    /**
     * Retorna o lancamento auxiliar instanciado conforme a regra do lancamento
     * @param $documento
     *
     * @param $dados
     * @return \ILancamentoAuxiliar
     * @throws \BusinessException
     */
    private function getLancamentoAuxiliar($documento, $dados)
    {
        $lancamentoAuxiliar = new \LancamentoAuxiliarEncerramentoExercicio();
        $mensagem = "Lançamento automático de encerramento de exercício.";
        $lancamentoAuxiliar->setCodigoDocumento($documento);
        $lancamentoAuxiliar->setObservacaoHistorico($mensagem);
        $lancamentoAuxiliar->setValorTotal(abs($dados->valor));
        $lancamentoAuxiliar->setCodigoRecurso($dados->codigo_recurso);

        switch ($documento) {
            case Documento::ENCERRAMENTO_TRANSFERENCIA_SALDOS_RPNP_EX_ANT:
            case Documento::ENCERRAMENTO_TRANSFERENCIA_SALDOS_RPP_EX_ANT:
            case Documento::ENCERRAMENTO_EXERC_ORC_DESP_LIQUIDAR:
            case Documento::ENCERRAMENTO_EXERC_ORC_DESP_LIQUIDACAO:
            case Documento::ENCERRAMENTO_EXERC_ORC_DESP_LIQUIDADOS:
            case Documento::ENCERRAMENTO_RPNP_LIQUIDACAO_EXERCICIO:
            case Documento::ENCERRAMENTO_INSCRICAO_RPP_EXERCICIO:
            case Documento::ENCERRAMENTO_INSCRICAO_RPNP_PAGO_EXERCICIO:
            case Documento::ENCERRAMENTO_INSCRICAO_RPNP_CANCELADO_EXERCICIO:
            case Documento::ENCERRAMENTO_INSCRICAO_RPP_PAGOS_EXERCICIO:
            case Documento::ENCERRAMENTO_INSCRICAO_RPP_CANCELADO_EXERCICIO:
            case Documento::ENCERRAMENTO_TRANSFERENCIA_RPNP_RP:
            case Documento::ENCERRAMENTO_RPNP_LIQUIDAR_EXERCICIO:
                if ($dados->valor < 0) {
                    $lancamentoAuxiliar->setInversaoContas(true);
                }
                if (!empty($dados->empenho)) {
                    $oEmpenho = new \EmpenhoFinanceiro($dados->empenho);
                    $lancamentoAuxiliar->setCgm($oEmpenho->getCgm()->getCodigo());
                    $lancamentoAuxiliar->setEmpenho($oEmpenho);
                    $lancamentoAuxiliar->setCaracteristicaPeculiar($oEmpenho->getCaracteristicaPeculiar());
                    $lancamentoAuxiliar->setElemento($oEmpenho->getDesdobramentoEmpenho());
                    $lancamentoAuxiliar->setFavorecido($oEmpenho->getCgm()->getCodigo());
                    if ($oEmpenho->getAno() == $this->getAno()) {
                        $lancamentoAuxiliar->setDotacao($oEmpenho->getDotacao()->getCodigo());
                    }
                    $lancamentoAuxiliar->setNumeroEmpenho($oEmpenho->getNumero());
                }
                break;
            case Documento::ENCERRAMENTO_VARIACOES_PATRIMONIAIS:
                $lancamentoAuxiliar->setContaCredito($dados->reduzido_credito);
                $lancamentoAuxiliar->setContaDebito($dados->reduzido_debito);
                break;

            case Documento::ENCERRAMENTO_RECEITA_REALIZADA:
            case Documento::ENCERRAMENTO_RECEITA_BRUTA:
            case Documento::ENCERRAMENTO_NATUREZA_ORCAMENTARIA_CONTROLE_RECEITA:
                $lancamentoAuxiliar->setContaCredito($dados->conta_credito);
                $lancamentoAuxiliar->setContaDebito($dados->conta_debito);
                if (!empty($dados->receita)) {
                    $lancamentoAuxiliar->setReceitaContabil(new \ReceitaContabil($dados->receita, $dados->ano));
                }

                break;

            case Documento::ENCERRAMENTO_NATUREZA_ORCAMENTARIA_CONTROLE_DESPESA:
                if (!empty($dados->empenho)) {
                    $oEmpenho = new \EmpenhoFinanceiro($dados->empenho);
                    $lancamentoAuxiliar->setCgm($oEmpenho->getCgm()->getCodigo());
                    $lancamentoAuxiliar->setEmpenho($oEmpenho);
                    $lancamentoAuxiliar->setCaracteristicaPeculiar($oEmpenho->getCaracteristicaPeculiar());
                    $lancamentoAuxiliar->setElemento($oEmpenho->getDesdobramentoEmpenho());
                    $lancamentoAuxiliar->setFavorecido($oEmpenho->getCgm()->getCodigo());
                    $lancamentoAuxiliar->setNumeroEmpenho($oEmpenho->getNumero());
                    if ($oEmpenho->getDotacao()->getAno() == $this->ano) {
                        $lancamentoAuxiliar->setDotacao($oEmpenho->getDotacao()->getCodigo());
                    }
                }
                if (!empty($dados->dotacao)) {
                    $dotacao = \DotacaoRepository::getDotacaoPorCodigoAno($dados->dotacao, $this->ano);
                    if (!empty($dotacao)) {
                        $lancamentoAuxiliar->setDotacao($dados->dotacao);
                    }
                }
                $lancamentoAuxiliar->setContaCredito($dados->conta_credito);
                $lancamentoAuxiliar->setContaDebito($dados->conta_debito);
                if (empty($dados->empenho) && empty($dados->dotacao)) {
                    $msg = "[Documento 1019] - Encontrado {$dados->valor} despesa receita vinculada. ";
                    $msg .= "Lançado nas {$dados->conta_credito} (credito) e {$dados->conta_debito} (débito).";
                    $this->mensagensLog[] = $msg;
                }

                break;

            case Documento::ENCERRAMENTO_DDR_REALIZADA:
            case Documento::ENCERRAMENTO_CONTRATOS_CONVENIOS_EXECUTADOS:
                $lancamentoAuxiliar->setContaCredito($dados->conta_credito);
                $lancamentoAuxiliar->setContaDebito($dados->conta_debito);
                break;
        }
        return $lancamentoAuxiliar;
    }

    /**
     * @throws \BusinessException
     * @throws \DBException
     * @throws \ParameterException
     */
    public function encerrar($documentosEncerrar = array())
    {
        $sTipoEncerramento = $this->getTipoEncerramento();
        $aDocumentos = $this->getDocumentosParaProcessamento();
        if ($sTipoEncerramento == "ExecucaoOrcamentaria") {
            $aDocumentos = $this->getDocumentosParaProcessamentoEncerramentoOrcamentario();
        }

        foreach ($aDocumentos as $documento => $tipoEncerramento) {
            if (!in_array($documento, $documentosEncerrar)) {
                //dump("vai Pular encerramento: ", $documento, $documentosEncerrar);
                continue;
            }
            $this->encerrarDocumento($documento, $tipoEncerramento);
        }
    }

    /**
     * @throws \Exception
     */
    public function cancelar()
    {
        $this->abrirPeriodoContabil();
        $sTipoEncerramento = $this->getTipoEncerramento();
        $aDocumentosProcessamento = $this->getDocumentosParaProcessamento();
        if ($sTipoEncerramento == "ExecucaoOrcamentaria") {
            $aDocumentosProcessamento = $this->getDocumentosParaProcessamentoEncerramentoOrcamentario();
        }

        foreach ($aDocumentosProcessamento as $codigoDocumento => $tipo) {
            if (!in_array($codigoDocumento, $this->documentosParaCancelar)) {
                continue;
            }
            $this->cancelarDocumento($codigoDocumento);
        }
    }


    /**
     * @return bool
     * @throws \Exception
     */
    protected function abrirPeriodoContabil()
    {
        $usuario = \UsuarioSistemaRepository::getUsuarioSessao();
        $periodoContabil = new PeriodoContabil($this->getInstituicao(), $this->getData(), $usuario, $this->getAno());
        $periodoContabil->cancelarEncerramento();
        return true;
    }

    /**
     * realiza o encerramento de um documento especifico.
     * @param $documento
     * @param $tipoEncerramento
     *
     * @throws \BusinessException
     * @throws \Exception
     */
    private function encerrarDocumento($documento, $tipoEncerramento)
    {
        $sql = $this->getConsultaLancamentosDoDocumento($documento);
        $rsLancamentos = db_query($sql);

        if (!$rsLancamentos) {
            $mensgem = "Não foi possível executar regra para definição dos lançamentos do documento {$documento}";
            throw new \Exception($mensgem);
        }
        $this->incluirDadosEncerramentoDoTipo($tipoEncerramento, $documento);
        $instancia = $this;
        $eventoContabil = new EventoContabil($documento, $this->ano);
        \db_utils::makeCollectionFromRecord($rsLancamentos, function ($dados) use (
            $instancia,
            $documento,
            $eventoContabil,
            $tipoEncerramento
        ) {
            $instancia->mensagensLog = array();
            $lancamentoAuxiliar = $instancia->getLancamentoAuxiliar($documento, $dados);
            if (empty($lancamentoAuxiliar)) {
                throw new \Exception("Não foi possível executar identificar o o lancamento. ");
            }
            $opcoes = [
                'ignorar_conta_corrente' => true,
                'itens_ignorar_pos' => ['\ECidade\Financeiro\Contabilidade\LancamentoContabil\Validacao\Atributos']
            ];
            $codigoLancamento = $eventoContabil->executaLancamento(
                $lancamentoAuxiliar,
                $instancia->data->getDate(),
                $opcoes
            );
            if (!empty($instancia->mensagensLog)) {
                $instancia->logger->warning("Código do Lançamento: " . $codigoLancamento);
                $instancia->logger->write(implode("\n", $instancia->mensagensLog) . "\n");
            }
            /**
             * Gravar na conlancamencerramento
             */
            $instancia->vincularLancamentoAoEncerramento($codigoLancamento, $tipoEncerramento, $documento);
        });
    }


    /**
     * Realiza a inclusao de um tipo de encerramento.
     * @param $tipoEncerramento
     * @throws \BusinessException
     * @throws \Exception
     */
    private function incluirDadosEncerramentoDoTipo($tipoEncerramento, $documento)
    {

        if (!empty($this->encerramentos[$tipoEncerramento . $documento])) {
            return;
        }
        $oDaoConEncerramento = new \cl_conencerramento();
        $oDaoConEncerramento->c42_data = $this->data->getDate();
        $oDaoConEncerramento->c42_hora = db_hora();
        $oDaoConEncerramento->c42_anousu = $this->ano;
        $oDaoConEncerramento->c42_encerramentotipo = $tipoEncerramento;
        $oDaoConEncerramento->c42_instit = $this->getInstituicao()->getCodigo();
        $oDaoConEncerramento->c42_usuario = db_getsession("DB_id_usuario");
        $oDaoConEncerramento->c42_coddoc = $documento;



        $oDaoConEncerramento->incluir(null);
        if ($oDaoConEncerramento->erro_status == 0) {
            throw new \Exception($oDaoConEncerramento->erro_msg);
        }
       // dump($oDaoConEncerramento);
        $this->encerramentos[$tipoEncerramento . $documento] = $oDaoConEncerramento->c42_sequencial;
    }

    /**
     * Salvar o vinculo do lancamento contábil com o encerramento
     * @param $codigoLancamento
     * @param $tipoEncerramento
     * @throws \BusinessException
     */
    public function vincularLancamentoAoEncerramento($codigoLancamento, $tipoEncerramento, $documento)
    {

        $codigoEncerramento = $this->getCodigoEncerramentoDoTipo($tipoEncerramento, $documento);
        $oDaoConEncerramentoLancam = new \cl_conencerramentolancam();
        $oDaoConEncerramentoLancam->c44_conlancam = $codigoLancamento;
        $oDaoConEncerramentoLancam->c44_encerramento = $codigoEncerramento;
        $oDaoConEncerramentoLancam->incluir(null);
        if ($oDaoConEncerramentoLancam->erro_status == 0) {
            throw new \BusinessException($oDaoConEncerramentoLancam->erro_msg);
        }
    }

    /**
     * Retorna o codigo do encerramento realizado por tipo
     * @param $iTipoEncerramento
     * @return mixed
     */
    private function getCodigoEncerramentoDoTipo($iTipoEncerramento, $documento)
    {

        if (isset($this->encerramentos[$iTipoEncerramento . $documento])) {
            return $this->encerramentos[$iTipoEncerramento . $documento];
        }
        return false;
    }


    /**
     * Realiza o cancelametno dos dados do documento
     * @param $documento
     * @throws \Exception
     */
    protected function cancelarDocumento($documento)
    {
        /**
         * Deletemos todos os encerramentos do tipo
         */
        $where = "c71_coddoc = {$documento} and c70_anousu = {$this->ano} ";
        $where .= " and c02_instit = {$this->instituicao->getCodigo()}";
        $oDaoConEncerramentolancan = new cl_conencerramentolancam;
        $consultaCodigoEncerramento = $oDaoConEncerramentolancan->sql_query_documento(
            null,
            '*',
            'c71_coddoc',
            $where
        );
        $rsEncerrar = db_query($consultaCodigoEncerramento);

        //db_criatabela($rsEncerrar);


        $totalRegistros = pg_num_rows($rsEncerrar);

        $codigosEncerramento = array();
        for ($i = 0; $i < $totalRegistros; $i++) {
            $dados = \db_utils::fieldsMemory($rsEncerrar, $i);
            $oDaoConEncerramentolancan->excluir($dados->c44_sequencial);
            if ($oDaoConEncerramentolancan->erro_status == 0) {
                throw new \Exception('Não foi possível cancelar o encerramento do documento ' . $documento .
                    ' Erro técnico: ' . $oDaoConEncerramentolancan->erro_msg);
            }
            \lancamentoContabil::excluirLancamento($dados->c44_conlancam);
            if (!in_array($dados->c44_encerramento, $codigosEncerramento)) {
                $codigosEncerramento[] = $dados->c44_encerramento;
            }
        }

        $whereDocumento  = "c42_sequencial in(" . implode(",", $codigosEncerramento) . ")";
        if (empty($codigosEncerramento)) {
            $whereDocumento = "c42_coddoc = {$documento}";
            $whereDocumento .= " and c42_instit = {$this->instituicao->getCodigo()}";
            $whereDocumento .= " and c42_anousu = {$this->ano}";
        }
        /**
         * Remove os dados do encerramento
         */
        $oDaoConEncerramento = new \cl_conencerramento();
        $oDaoConEncerramento->excluir(
            null,
            $whereDocumento
        );
        if ($oDaoConEncerramento->erro_status == 0) {
            $mensagem = 'Erro ao remover dados do encerramento do exercício para o documento ';
            $mensagem .= "$documento\n{$oDaoConEncerramento->erro_msg}";

            throw new \Exception($mensagem);
        }
    }
}
