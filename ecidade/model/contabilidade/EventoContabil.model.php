<?php
/*
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

use ECidade\Financeiro\Contabilidade\ContaCorrente\Services\Processamento;
use ECidade\Financeiro\Contabilidade\LancamentoContabil;
use ECidade\Financeiro\Contabilidade\LancamentoContabil\LancamentoRecurso;
use ECidade\Financeiro\Contabilidade\LancamentoContabil\Service\ComplementoLancamentoService;

define("URL_MENSAGEM_EVENTOCONTABIL", "financeiro.contabilidade.EventoContabil.");

/**
 * EventoContabil
 * Model para controle de transação
 * @author  matheus.felini@dbseller.com.br
 * @version $Revision: 1.46 $
 * @package contabilidade
 */
class EventoContabil
{

    /**
     * Código Sequencial da Transação
     * @var integer
     */
    protected $iSequencialTransacao;

    /**
     * Ano da Transação
     * @var integer
     */
    protected $iAnoUso;

    /**
     * Código do Documento
     * @var integer
     */
    protected $iCodigoDocumento;

    /**
     * Código da Instituição
     * @var integer
     */
    protected $iInstituicao;

    /**
     * Descricao do documento
     * @var string
     */
    protected $sDescricaoDocumento;

    /**
     * Coleção de Lançamentos de um EventoContabil
     * @var EventoContabilLancamento[]
     */
    protected $aEventoContabilLancamento = array();

    /**
     * Código do Lancamento Executado
     * @var integer
     */
    protected $iCodigoLancamento;

    /**
     * Evento contabil de inclusao
     * @var boolean
     */
    protected $lEventoInclusao;

    /**
     * Total de instancias
     * @var integer
     */
    static $iTotalInstancias = 0;

    /**
     * @var integer
     */
    private $iOrdemEvento = null;

    /**
     * Define a insituição em que ocorrera o lançamento
     * @var integer|null
     */
    private $iInstituicaoLancamento = null;

    /**
     * @var integer|null
     */
    private $documentoInverso = null;

    /**
     * @var integer
     */
    private $tipoDocumento;

    /**
     * @param null $iCodigoDocumento
     * @param null $iAno
     * @param null $iInstituicao
     * @throws Exception
     */
    public function __construct($iCodigoDocumento = null, $iAno = null, $iInstituicao = null, $executarLancamento = true)
    {
        EventoContabil::$iTotalInstancias++;
        $instituicaoPrefeitura = InstituicaoRepository::getInstituicaoPrefeitura();
        if (!empty($iCodigoDocumento) && !empty($iAno)) {
            $this->iInstituicao = $iInstituicao;
            $oDaoContrans = new cl_contrans;

            $sWhereContrans = "c45_coddoc = {$iCodigoDocumento} and c45_anousu = {$iAno} and c45_instit = {$this->getInstituicao()}";
            $sSqlBuscaContrans = $oDaoContrans->sql_query_vinculo(null, "*", null, $sWhereContrans);
            $rsBuscaContrans = $oDaoContrans->sql_record($sSqlBuscaContrans);

            if ($oDaoContrans->numrows == 0 && $executarLancamento === false) {
                throw new Exception("Documento {$iCodigoDocumento} não encontrado para o ano {$iAno}.");
            }

            if ($oDaoContrans->numrows == 0 && $instituicaoPrefeitura->getCodigo() === $this->getInstituicao()) {
                throw new Exception("Documento {$iCodigoDocumento} não encontrado para o ano {$iAno}.");
            }

            if ($oDaoContrans->numrows == 0 && $instituicaoPrefeitura->getCodigo() !== $this->getInstituicao()) {
                $this->iInstituicaoLancamento = $this->getInstituicao();
                $this->iInstituicao = $this->iInstituicaoLancamento;
                $sWhereContrans = "c45_coddoc = {$iCodigoDocumento} and c45_anousu = {$iAno} and c45_instit = {$instituicaoPrefeitura->getCodigo()}";
                $daoBuscaTransacaoPrefeitura = new cl_contrans();
                $sSqlBuscaContrans = $daoBuscaTransacaoPrefeitura->sql_query_vinculo(null, "*", null, $sWhereContrans);
                $rsBuscaContrans = $daoBuscaTransacaoPrefeitura->sql_record($sSqlBuscaContrans);
                if ($daoBuscaTransacaoPrefeitura->numrows === 0) {
                    throw new Exception("Documento {$iCodigoDocumento} não encontrado para o ano {$iAno} para instituição prefeitura.");
                }
            }

            /*
             * Setamos as propriedades da transação
             */
            $oDadoLancamento = db_utils::fieldsMemory($rsBuscaContrans, 0);

            /**
             * Evento de inclusao
             */
            if (!empty($oDadoLancamento->c115_conhistdocinclusao) && $oDadoLancamento->c115_conhistdocinclusao == $iCodigoDocumento) {
                $this->lEventoInclusao = true;
            }

            /**
             * Evento de estorno
             */
            if (!empty($oDadoLancamento->c115_conhistdocestorno) && $oDadoLancamento->c115_conhistdocestorno == $iCodigoDocumento) {
                $this->lEventoInclusao = false;
            }

            $this->setAnoUso($iAno);
            $this->setCodigoDocumento($iCodigoDocumento);
            $this->setInstituicao(empty($this->iInstituicao) ? $oDadoLancamento->c45_instit : $this->iInstituicao);
            $this->setSequencialTransacao($oDadoLancamento->c45_seqtrans);
            $this->setDescricaoDocumento($oDadoLancamento->c53_descr);
            $this->tipoDocumento = $oDadoLancamento->c53_tipo;
        }
    }

    /**
     * Inclui ou Altera uma transação
     * @return boolean true
     * @throws Exception
     */
    public function salvar()
    {
        if ($this->getCodigoDocumento() == "") {
            throw new Exception("É necessário informar o código do documento.");
        }
        if ($this->getInstituicao() == "") {
            throw new Exception("É necessário informar o código da instituição.");
        }
        if ($this->getAnoUso() == "") {
            throw new Exception("É necessário informar ano.");
        }

        $oDaoContrans = new cl_contrans();
        $oDaoContrans->c45_seqtrans = $this->getSequencialTransacao();
        $oDaoContrans->c45_anousu = $this->getAnoUso();
        $oDaoContrans->c45_coddoc = $this->getCodigoDocumento();
        $oDaoContrans->c45_instit = $this->getInstituicao();

        if ($this->getSequencialTransacao() == "") {
            $oDaoContransVerifica = new cl_contrans();
            $sWhere = "    c45_anousu = " . db_getsession("DB_anousu");
            $sWhere .= "and c45_coddoc = " . $this->getCodigoDocumento();
            $sWhere .= "and c45_instit = " . db_getsession("DB_instit");
            $sSQL = $oDaoContransVerifica->sql_query_file(null, "1", null, $sWhere);
            $oDaoContransVerifica->sql_record($sSQL);

            if ($oDaoContransVerifica->numrows > 0) {
                $sMensagem = "Não é possível cadastrar novo documento.\n";
                $sMensagem .= "Transação para o documento {$this->getCodigoDocumento()} já existe para o ano " .
                    db_getsession("DB_anousu");
                throw new Exception($sMensagem);
            }

            $oDaoContrans->incluir(null);
            $this->setSequencialTransacao($oDaoContrans->c45_seqtrans);
        } else {
            $oDaoContrans->alterar($this->getSequencialTransacao());
        }

        if ($oDaoContrans->erro_status == 0) {
            throw new Exception("Não foi possível salvar os dados da transação.\n\n{$oDaoContrans->erro_msg}");
        }
        return true;
    }

    /**
     * Exclui todos os dados de um evento contábil
     * @throws Exception
     */
    public function excluir()
    {
        $oDaoContrans = new cl_contrans();
        $oDaoContrans->excluir($this->getSequencialTransacao());

        if ($oDaoContrans->erro_status == 0) {
            throw new Exception("Não foi possível excluir a transação que mantinha o lançamento excluído.\n\n{$oDaoContrans->erro_msg}");
        }
        return true;
    }

    /**
     * Retorna o código do lançamento
     * @return integer
     */
    public function getCodigoLancamento()
    {
        return $this->iCodigoLancamento;
    }

    /**
     * Seta o código do documento
     * @param integer $iCodigoDocumento
     */
    public function setCodigoDocumento($iCodigoDocumento)
    {
        $this->iCodigoDocumento = $iCodigoDocumento;
    }

    /**
     * Retorna o código do documento
     * @return integer
     */
    public function getCodigoDocumento()
    {
        return $this->iCodigoDocumento;
    }

    /**
     * Seta valor para o ano uso
     * @param integer $iAnoUso
     */
    public function setAnoUso($iAnoUso)
    {
        $this->iAnoUso = $iAnoUso;
    }

    /**
     * Retorna o ano uso da transação
     * @return integer
     */
    public function getAnoUso()
    {
        return $this->iAnoUso;
    }

    /**
     * Seta valor para a instituição
     * @param integer $iInstituicao
     */
    public function setInstituicao($iInstituicao)
    {
        $this->iInstituicao = $iInstituicao;
    }

    /**
     * Retorna instituição da transação
     * @return integer
     */
    public function getInstituicao()
    {
        if (empty($this->iInstituicao)) {
            $this->iInstituicao = db_getsession("DB_instit");
        }
        return $this->iInstituicao;
    }

    /**
     * Retorna o sequencial da transação
     * @return integer
     */
    public function getSequencialTransacao()
    {
        return $this->iSequencialTransacao;
    }

    /**
     * Seta valor para o sequencial da transação
     * @param integer $iSequencialTransacao
     */
    public function setSequencialTransacao($iSequencialTransacao)
    {
        $this->iSequencialTransacao = $iSequencialTransacao;
    }

    /**
     * Retorna uma coleção de lançamentos contábeis
     * @return EventoContabilLancamento[]
     */
    public function getEventoContabilLancamento()
    {

        if (count($this->aEventoContabilLancamento) == 0) {
            /**
             *  Criamos um array contendo a coleção de lancamentos da transacao
             */
            $oDaoContranslan = new cl_contranslan();
            $sWhereLancamento = "c46_seqtrans = {$this->getSequencialTransacao()}";
            $sSqlBuscaLancamento = $oDaoContranslan->sql_query_file(null, "c46_seqtranslan", "c46_ordem", $sWhereLancamento);
            $rsBuscaLancamento = $oDaoContranslan->sql_record($sSqlBuscaLancamento);
            if ($oDaoContranslan->numrows > 0) {
                for ($iRowLancamento = 0; $iRowLancamento < $oDaoContranslan->numrows; $iRowLancamento++) {
                    $iCodigoLancamento = db_utils::fieldsMemory($rsBuscaLancamento, $iRowLancamento)->c46_seqtranslan;
                    $oEventoContabilLancamento = EventoContabilLancamentoRepository::getEventoByCodigo($iCodigoLancamento);
                    $this->adicionarEventoContabilLancamento($oEventoContabilLancamento);
                }
            }
        }

        return $this->aEventoContabilLancamento;
    }

    /**
     * Adiciona um Lançamento a coleção de lançamentos
     * @param EventoContabilLancamento $oLancamento
     */
    public function adicionarEventoContabilLancamento(EventoContabilLancamento $oLancamento)
    {
        $this->aEventoContabilLancamento[] = $oLancamento;
    }

    /**
     * Retorna um determinado lançamento de acordo com o código sequencial do lançamento passado via parâmetro
     * @param integer $iCodigoSequencialLancamento
     * @return EventoContabilLancamento|boolean
     */
    public function getEventoContabilLancamentoPorCodigo($iCodigoSequencialLancamento)
    {

        if (count($this->aEventoContabilLancamento) == 0) {
            $this->getEventoContabilLancamento();
        }

        foreach ($this->aEventoContabilLancamento as $oLancamentoEventoContabil) {
            if ($oLancamentoEventoContabil->getSequencialLancamento() == $iCodigoSequencialLancamento) {
                return $oLancamentoEventoContabil;
            }
        }
        return false;
    }

    /**
     * Reprocessa o Lancamento contábil do documento
     * @param $iCodLancamento
     * @param ILancamentoAuxiliar $oLancamentoAuxiliar
     * @param null $sDataLancamento
     * @return mixed
     * @throws BusinessException
     */
    public function reprocessaLancamentos(
        $iCodLancamento,
        ILancamentoAuxiliar $oLancamentoAuxiliar,
        $sDataLancamento = null
    ) {

        $dtDataUsu = date("Y-m-d", db_getsession('DB_datausu'));
        if (!empty($sDataLancamento)) {
            $dtDataUsu = $sDataLancamento;
        }

        //Seta o código do lançamento
        $this->iCodigoLancamento = $iCodLancamento;
        $this->excluirRegistrosAnteriores();

        /**
         * Efetua o lançamentos nas contas do Evento
         */
        foreach ($this->getEventoContabilLancamento() as $oLancamentoContabil) {
            $oLancamentoAuxiliar->setHistorico($oLancamentoContabil->getHistorico());

            $oLancamentoContabil->executa(
                $iCodLancamento,
                $this->iCodigoDocumento,
                $oLancamentoAuxiliar,
                $dtDataUsu,
                $this->iInstituicaoLancamento
            );
        }

        self::salvarLancamento($iCodLancamento);
        return $iCodLancamento;
    }


    private function excluirRegistrosAnteriores()
    {
        /**
         * Excluimos os lançamentos contábeis contacorrentedetalheconlancamval para então incluirmos nas contas corretas
         */
        $oDaoConlancamval = new cl_conlancamval();
        $sSqlConlancamval = $oDaoConlancamval->sql_query_file(
            null,
            "c69_sequen",
            null,
            "c69_codlan = $this->iCodigoLancamento"
        );
        $rsConlancamval = $oDaoConlancamval->sql_record($sSqlConlancamval);

        for ($iConlancamval = 0; $iConlancamval < $oDaoConlancamval->numrows; $iConlancamval++) {
            $c69_sequen = db_utils::fieldsMemory($rsConlancamval, $iConlancamval)->c69_sequen;
            $oDaoExcluirDetalheConlancamVal = new cl_contacorrentedetalheconlancamval();
            $oDaoExcluirDetalheConlancamVal->excluir(null, "c28_conlancamval = {$c69_sequen} ");

            if ($oDaoExcluirDetalheConlancamVal->erro_status == "0") {
                throw new Exception(
                    "Erro excluindo contacorrentedetalheconlancamval. \\n" . $oDaoExcluirDetalheConlancamVal->erro_msg
                );
            }

            $oDaoConlancamValExclusao = new cl_conlancamval();
            /**
             * Excluimos os lançamentos contábeis para então incluirmos nas contas corretas
             */
            $oDaoConlancamValExclusao->excluir(null, "c69_sequen = {$c69_sequen}");
            if ($oDaoConlancamValExclusao->erro_status == "0") {
                throw new Exception("Erro excluindo conlancamval. \\n" . $oDaoConlancamValExclusao->erro_msg);
            }
        }
    }

    /**
     * Executa o Lancamento contábil no documento
     * @param ILancamentoAuxiliar $oLancamentoAuxiliar
     * @param null $sDataLancamento
     * @return integer
     * @throws BusinessException|Exception
     */
    public function executaLancamento(
        ILancamentoAuxiliar $oLancamentoAuxiliar,
                            $sDataLancamento = null,
        array               $options = null
    ) {
        $dtDataUsu = date("Y-m-d", db_getsession('DB_datausu'));
        if (!empty($sDataLancamento)) {
            $dtDataUsu = $sDataLancamento;
        }

        $nValorTotal = $oLancamentoAuxiliar->getValorTotal();
        $iAnoUsu = db_getsession("DB_anousu");

        if ($nValorTotal <= 0) {
            throw new Exception("Não foi possível incluir o lançamento pois o valor esta zerado.");
        }

        /**
         * Começamos a efetuar os lançamentos contabeis
         */
        $oDaoLancamento = new cl_conlancam();
        $oDaoLancamento->c70_anousu = $iAnoUsu;
        $oDaoLancamento->c70_data = $dtDataUsu;
        $oDaoLancamento->c70_valor = $nValorTotal;
        $oDaoLancamento->c70_codlan = null;
        $oDaoLancamento->incluir(null);
        if ($oDaoLancamento->erro_status == 0) {
            $sErroMsg = "Não foi Possível incluir lancamento\nErro Técnico:{$oDaoLancamento->erro_msg}";
            throw new BusinessException($sErroMsg);
        }

        //Seta o código do lançamento
        $this->iCodigoLancamento = $oDaoLancamento->c70_codlan;

        /**
         * Incluimos o Documento do Lançamento
         */
        $oDaoConLancamDoc = new cl_conlancamdoc();
        $oDaoConLancamDoc->c71_codlan = $oDaoLancamento->c70_codlan;
        $oDaoConLancamDoc->c71_data = $oDaoLancamento->c70_data;
        $oDaoConLancamDoc->c71_coddoc = $this->iCodigoDocumento;

        $oDaoConLancamDoc->incluir($oDaoLancamento->c70_codlan);
        if ($oDaoConLancamDoc->erro_status == 0) {
            $sErroMsg = "Não foi possível salvar documento contabeis\n";
            $sErroMsg .= "Erro Técnico: {$oDaoConLancamDoc->erro_msg}\n{$oDaoConLancamDoc->erro_campo}";
            throw new BusinessException($sErroMsg);
        }

        /**
         * Efetua o lançamento nas contas do Evento
         */
        foreach ($this->getEventoContabilLancamento() as $oLancamentoContabil) {
            $oLancamentoContabil->executa(
                $oDaoLancamento->c70_codlan,
                $this->iCodigoDocumento,
                $oLancamentoAuxiliar,
                $dtDataUsu,
                $this->iInstituicaoLancamento
            );
        }

        /**
         * Executa vinculos adicionais
         */
        if (!self::vincularLancamentoNaInstituicao($this->iCodigoLancamento, $this->getInstituicao())) {
            throw new BusinessException("Não foi possível vincular o lançamento na instituição.");
        }

        if (!self::vincularOrdem($this->iCodigoLancamento, $this->iOrdemEvento)) {
            throw new BusinessException("Não foi possível vincular uma ordem ao lançamento contábil.");
        }


        $oLancamentoAuxiliar->executaLancamentoAuxiliar($oDaoLancamento->c70_codlan, $oDaoLancamento->c70_data);

        /**
         * Lancamentos os dados do recurso na tabela conlancamval, conforme valores
         */
        $this->lancarRecurso($oLancamentoAuxiliar);

        if (empty($options['ignorar_conta_corrente'])) {
            $instituicao = InstituicaoRepository::getInstituicaoByCodigo($this->getInstituicao());
            $data = new \DBDate($dtDataUsu);
            $competencia = new DBCompetencia($data->getAno(), $data->getMes());
            $oProcessamento = new Processamento($instituicao, $competencia);
            $oProcessamento->processar(array($oDaoLancamento->c70_codlan));
        }

        // transferencia de cobertura financeira
        $itensParaIgnorar = array();
        if (!empty($options['itens_ignorar_pos']) && is_array($options['itens_ignorar_pos'])) {
            $itensParaIgnorar = $options['itens_ignorar_pos'];
        }
        LancamentoContabil\PosProcessamento::processar($oDaoLancamento->c70_codlan, $itensParaIgnorar);

        ComplementoLancamentoService::createIfNotExists($oDaoLancamento->c70_codlan);

        $this->registrarLog($oDaoLancamento);

        return $oDaoLancamento->c70_codlan;
    }

    /**
     * falta de vinculo na lancamentoscontabeislog
     * verifica se existe antes de criar
     */
    public function registrarLog($oDaoLancamento){

        $usuario = db_getsession("DB_id_usuario");

        $sqlExiste = "select codlan from lancamentoscontabeislog where codlan = {$oDaoLancamento->c70_codlan}";
        $rsExiste = db_query($sqlExiste);
        
        if (pg_num_rows($rsExiste) <= 0) {

            $sqlIncluir = "
            
              insert into lancamentoscontabeislog 
                           select nextval('lancamentoscontabeislog_sequencial_seq'::regclass), 
                                  {$oDaoLancamento->c70_codlan}, 
                                  {$usuario}, 
                                  '".$oDaoLancamento->c70_data."', 
                                  1

            ";
            
            if (!db_query($sqlIncluir)) {
                throw new Exception("Erro ao vincular log do lançamento.");
            }
        }
    }


    /**
     * retornar descricao do documento
     * @return string
     */
    public function getDescricaoDocumento()
    {
        return $this->sDescricaoDocumento;
    }

    /**
     * setar a descricao do documento
     * @param string $sDescricaoDocumento
     */
    public function setDescricaoDocumento($sDescricaoDocumento)
    {
        $this->sDescricaoDocumento = $sDescricaoDocumento;
    }

    /**
     * @return bool|EventoContabil
     * @throws BusinessException
     */
    public function getEventoInverso()
    {

        $sWhere = "   (c115_conhistdocinclusao = {$this->iCodigoDocumento}";
        $sWhere .= " or c115_conhistdocestorno = {$this->iCodigoDocumento})";

        $oDaoVinculoeventoscontabeis = new cl_vinculoeventoscontabeis();
        $sSqlBuscaVinculo = $oDaoVinculoeventoscontabeis->sql_query(null, "*", null, $sWhere);
        $rsVinculoEventoContabil = $oDaoVinculoeventoscontabeis->sql_record($sSqlBuscaVinculo);

        if ($oDaoVinculoeventoscontabeis->erro_status == "0") {
            throw new BusinessException(_M(URL_MENSAGEM_EVENTOCONTABIL . "erro_vinculo_documento_inverso"));
        }

        $oStdVinculo = db_utils::fieldsMemory($rsVinculoEventoContabil, 0);

        if (empty($oStdVinculo->c115_conhistdocinclusao) || empty($oStdVinculo->c115_conhistdocestorno)) {
            return false;
        }

        $iEventoRetorno = $oStdVinculo->c115_conhistdocinclusao;
        if ($this->iCodigoDocumento == $oStdVinculo->c115_conhistdocinclusao) {
            $iEventoRetorno = $oStdVinculo->c115_conhistdocestorno;
        }
        $this->documentoInverso = $iEventoRetorno;

        try {
            return EventoContabilRepository::getEventoContabilByCodigo(
                $iEventoRetorno,
                $this->iAnoUso,
                $this->getInstituicao()
            );
        } catch (Exception $oErro) {
            return false;
        }
    }

    /**
     * Verifica se o evento contábil possui documento de estorno.
     * @return bool
     */
    public function possuiDocumentoEstorno()
    {
        $oDaoVinculoEventos = new cl_vinculoeventoscontabeis();
        $sSqlBuscaEvento = $oDaoVinculoEventos->sql_query_file(
            null,
            "*",
            null,
            "c115_conhistdocinclusao = {$this->iCodigoDocumento}"
        );
        $rsBuscaEvento = db_query($sSqlBuscaEvento);
        $oStdDocumento = db_utils::fieldsMemory($rsBuscaEvento, 0);
        if (empty($oStdDocumento->c115_conhistdocestorno)) {
            return false;
        }
        return true;
    }

    /**
     * @param $iCodigo
     * @return bool|EventoContabil
     * @throws Exception
     */
    public static function getInstanciaPorCodigo($iCodigo)
    {
        $oDaoConTrans = new cl_contrans();
        $sSqlContrans = $oDaoConTrans->sql_query_file($iCodigo);
        $rsContrans = $oDaoConTrans->sql_record($sSqlContrans);
        if ($oDaoConTrans->numrows > 0) {
            $oDadosContrans = db_utils::fieldsMemory($rsContrans, 0);
            return EventoContabilRepository::getEventoContabilByCodigo($oDadosContrans->c45_coddoc, $oDadosContrans->c45_anousu);
        }
        return false;
    }

    /**
     * @return int
     */
    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    /**
     * Retorna true caso documento for do tipo inclusao
     * @return boolean
     */
    public function isEventoInclusao()
    {
        return $this->lEventoInclusao;
    }

    /**
     * Retornar se o documento é de estorno
     *
     * @return boolean
     */
    public function estorno()
    {
        return !$this->lEventoInclusao;
    }

    /**
     * Retornar se o documento é de inclusao
     *
     * @return boolean
     */
    public function inclusao()
    {
        return $this->lEventoInclusao;
    }

    /**
     * @param $iOrdem
     */
    public function setOrdem($iOrdem)
    {
        $this->iOrdemEvento = $iOrdem;
    }

    /**
     * Vincula um lançamento contábil em uma instituição.
     * Criado método pois este é usado nos locais que não estão utilizando os programas novos para lançamento
     * contábil, desta forma as rotinas que não utilizam o programa novo, apenas irão chamar este método.
     *
     * @param $iCodigoLancamento - Código sequencial do lançamento
     * @param $iCodigoInstituicao - Código sequencial da instituição
     * @return boolean
     */
    public static function vincularLancamentoNaInstituicao($iCodigoLancamento, $iCodigoInstituicao)
    {

        $oDaoConlancamInstit = new cl_conlancaminstit();
        $oDaoConlancamInstit->c02_sequencial = null;
        $oDaoConlancamInstit->c02_codlan = $iCodigoLancamento;
        $oDaoConlancamInstit->c02_instit = $iCodigoInstituicao;
        $oDaoConlancamInstit->incluir(null);
        if ($oDaoConlancamInstit->erro_status == "0") {
            return false;
        }

        self::vincularDepartamento($iCodigoLancamento);
        return true;
    }

    /**
     * @param $iCodigoLancamento
     * @param $iOrdem
     * @return bool
     */
    public static function vincularOrdem($iCodigoLancamento, $iOrdem = null)
    {

        $oDaoConlancamOrdem = new cl_conlancamordem();
        $oDaoConlancamOrdem->c03_sequencial = null;
        $oDaoConlancamOrdem->c03_codlan = $iCodigoLancamento;
        $oDaoConlancamOrdem->c03_ordem = $iOrdem;
        $oDaoConlancamOrdem->incluir(null);
        if ($oDaoConlancamOrdem->erro_status == "0") {
            return false;
        }
        return true;
    }

    /**
     * Vincula o lancamento contabil com o departamento que originou o lancamento
     * @param $codigoLancamento
     * @param null $codigoDepartamento
     * @return bool
     */
    public static function vincularDepartamento($codigoLancamento, $codigoDepartamento = null)
    {
        if (empty($codigoDepartamento)) {
            $codigoDepartamento = db_getsession('DB_coddepto');
        }
        $daoConlancamDepartamento = new cl_conlancamdepartamento();
        $daoConlancamDepartamento->c128_sequencial = null;
        $daoConlancamDepartamento->c128_departamento = $codigoDepartamento;
        $daoConlancamDepartamento->c128_conlancam = $codigoLancamento;
        $daoConlancamDepartamento->incluir(null);
        if ($daoConlancamDepartamento->erro_status == "0") {
            return false;
        }
        return true;
    }

    /**
     * @param integer $lancamentoInclusao
     * @param integer $lancamentoEstorno
     * @param integer $lancamentoNovo
     *
     * @throws Exception
     */
    public static function vincularLancamentos($lancamentoInclusao, $lancamentoEstorno, $lancamentoNovo)
    {
        $dao = new cl_conlancamretificacao();
        $dao->c135_sequencial = null;
        $dao->c135_codlaninclusao = $lancamentoInclusao;
        $dao->c135_codlanestorno = $lancamentoEstorno;
        $dao->c135_codlannovo = $lancamentoNovo;
        $dao->incluir(null);
        if ($dao->erro_status == '0') {
            throw new Exception('Ocorreu um erro ao vincular os lançamentos contábeis.');
        }
    }

    /**
     * Utilizado para disparar a trigger na conlancam quando reprocessado um lançamento.
     * @param integer $codigoLancamento
     * @return boolean
     */
    public static function salvarLancamento($codigoLancamento)
    {
        $conlancam = new cl_conlancam();
        $conlancam->c70_codlan = $codigoLancamento;
        $conlancam->alterar($conlancam->c70_codlan);
        if ($conlancam->erro_status === "0") {
            return false;
        }
        return true;
    }

    /**
     * @param $lancamentoAuxiliar
     * @throws ReflectionException
     */
    protected function lancarRecurso($lancamentoAuxiliar)
    {
        $recurso = new LancamentoRecurso($this->iCodigoDocumento, $this->iCodigoLancamento, $this->iAnoUso);
        $recurso->processar($lancamentoAuxiliar);
    }

    /**
     * @return int|null
     */
    public function getDocumentoInverso()
    {
        return $this->documentoInverso;
    }
}
