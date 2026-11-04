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

require_once modification("model/contabilidade/planoconta/ContaPlano.model.php");

/**
 * Model para persistir dados na tabela conplano
 * @author Matheus Felini
 * @package contabilidade
 */
class ContaPlanoPCASP extends ContaPlano
{

    /**
     *
     * Instancia de ContaCorrente
     * @var ContaCorrente
     */
    protected $oContaCorrente;

    /**
     * @var stdClass[]
     */
    protected $aContasReduzidasNoAno = array();

    /**
     * @var stdClass[]
     */
    protected $aContasReduzidas = array();

    /**
     * @var integer
     */

    protected $iSistemaContaCorrente;

    /**
     * Define se esta incluindo ou nao
     * @var bool
     */
    protected $lInclusao = false;

    /**
     * @var bool
     */
    protected $saldoContinuo = false;

    /**
     * ContaPlanoPCASP constructor.
     *
     * @param null $iCodigoConta
     * @param null $iAnoUsu
     * @param null $iReduz
     * @param null $iInstituicao
     */

    public function __construct($iCodigoConta = null, $iAnoUsu = null, $iReduz = null, $iInstituicao = null)
    {

        $this->setNomeDao("conplano");
        if ((!empty($iCodigoConta)) || (!empty($iReduz)) && !empty($iAnoUsu)) {
            parent::__construct($iCodigoConta, $iAnoUsu, $iReduz, $iInstituicao);
        }

        if ($this->getCodigoConta() != "" && $this->getAno() != "") {

            $dadosContaCorrente = $this->getDadosSistemaContaCorrente();
            if (!empty($dadosContaCorrente)) {
                $this->iSistemaContaCorrente = $dadosContaCorrente->codigo;
            }
        }
    }

    /**
     * vai buscar a conta da tesouraria vinculada ao reduzido
     * 
     * @return contaTesouraria
     */
    public function getContaTesouraria()
    {

        $daoSaltes = new cl_saltes;
        $sql = $daoSaltes->sql_query_file( null, "k13_conta", null, "k13_reduz = {$this->getReduzido()}");
        $rs = $daoSaltes->sql_record($sql);
        $conta = db_utils::fieldsMemory($rs, 0)->k13_conta;
        return $contaTesouraria = new contaTesouraria($conta);
    }

    /**
     * retorna o tipo de conta, pelo estrutural
     *
     * TIPO |    Descricao
     *   1  | Conta Corrente
     *   2  | Poupança
     *   3  | Conta Aplicação
     *
     * 1111119   ...   - conta corrente
     * 1111150   ...   - conta aplicacao
     * 1111106   ...   - RPPS -  Conta corrente
     *
     */
    public function getTipoConta()
    {
        $aTipoConta["1111119"] = "1";
        $aTipoConta["1111106"] = "1";
        $aTipoConta["1111150"] = "2, 3";
        $aTipoConta["1111151"] = "2, 3";
        $aTipoConta["1111153"] = "2, 3";
        $inicioEstrut = substr($this->getEstrutural(), 0, 7);
        return array_key_exists($inicioEstrut, $aTipoConta) ? $aTipoConta[$inicioEstrut] : null;
    }

    /**
     * necessário validar o estrutural para contas CAIXA e BANCO (c60_codsis)
     * uma conta  c60_codsis = 5 ou 6 so pode ter estruturais de inicio em: 
     * 
     * 5 - FINANCEIRO - CAIXA - somente conta caixa (1111101)
     * 6 - FINANCEIRO - BANCOS - somente contas bancárias (1111119) e de aplicação (1111150) e "1111102","1111106"
     */
    public function validaEstruturalContaCaixaBanco()
    {

        $estruturalContabilidade = substr($this->getEstrutural(), 0, 7);
        $contaCaixa = ["1111101"];
        $contaBanco = ["1111102", "1111106", "1111119", "1111150"];

        switch ($this->getSistemaConta()->getCodigoSistemaConta()) {
            case "5":
                if (!in_array($estruturalContabilidade, $contaCaixa)) {
                    throw new Exception("O estrutural {$this->getEstrutural()} não pode ser 5 - FINANCEIRO - CAIXA.");
                }
                break;
            case "6":
                if (!in_array($estruturalContabilidade, $contaBanco)) {
                    throw new Exception("O estrutural {$this->getEstrutural()} não pode ser 6 - FINANCEIRO - BANCOS.");
                }   
            }

    }

    /**
     * Salva os dados na tabela conplano/conplanoreduz
     * @return boolean
     * @throws Exception
     */
    public function salvar()
    {
        /*
         * Verifica se existe transação com o banco de dados ativa
         */
        if (!db_utils::inTransaction()) {
            throw new Exception("Transação com o banco de dados não encontrada.");
        }

        /*
         * Valida se o estrutural já esta cadastrado
         */
        if ($this->hasEstruturalCadastrado() && !isset($this->iCodigoConta)) {
            throw new Exception("O estrutural {$this->getEstrutural()} já está cadastrado.");
        }

        if (!$this->getSistemaConta() instanceof SistemaConta) {
            throw new Exception("Nao foi possivel instanciar o Sistema de Conta do plano de contas.");
        }

        $this->validaEstruturalContaCaixaBanco();

        $oDaoConPlano = new cl_conplano;
        $oDaoConPlano->c60_codcon = $this->getCodigoConta();
        $oDaoConPlano->c60_estrut = "{$this->getEstrutural()}";
        $oDaoConPlano->c60_descr = $this->getDescricao();
        $oDaoConPlano->c60_finali = $this->getFinalidade();
        $oDaoConPlano->c60_codsis = "{$this->getSistemaConta()->getCodigoSistemaConta()}";
        $oDaoConPlano->c60_codcla = "{$this->getClassificacaoConta()->getCodigoClasse()}";
        $oDaoConPlano->c60_consistemaconta = "{$this->getSubSistema()->getCodigo()}";
        $oDaoConPlano->c60_identificadorfinanceiro = $this->getIdentificadorFinanceiro();
        $oDaoConPlano->c60_naturezasaldo = $this->getNaturezaSaldo();
        $oDaoConPlano->c60_saldocontinuo = $this->isSaldoContinuo() ? 'true' : 'false';
        $oDaoConPlano->c60_funcao = $this->getFuncao();

        /*
         * Inclui/Altera os dados caso já estejam cadastrados.
         * Caso seja incluído um novo plano de conta, é setado o sequencial do código da conta.
         */

        $lInclusao = true;

        if (!empty($this->iCodigoConta)) {
            $lInclusao = false;
        }

        $iUltimoAno = $this->getUltimoAnoPlano();

        for ($iAno = $this->getAno(); $iAno <= $iUltimoAno; $iAno++) {
            $oDaoConPlano->c60_anousu = $iAno;

            if (!empty($this->iCodigoConta) && !$lInclusao) {
                $oDaoConPlano->alterar($this->getCodigoConta(), $this->getAno());
            } else {
                $oDaoConPlano->incluir($this->getCodigoConta(), $iAno);
                $this->setCodigoConta($oDaoConPlano->c60_codcon);
            }

            if ($oDaoConPlano->erro_status == "0") {
                $sMsgConPlano = "O plano de contas não pode ser inserido.\n\n";
                $sMsgConPlano .= $oDaoConPlano->erro_msg;
                throw new Exception(str_replace("\\n", "\n", $sMsgConPlano));
            }

            /**
             * Sempre exclui vinculo com conta corrente se houver
             */
            $oDaoPCASPContaCorrente = new cl_conplanocontacorrente;
            $sWhereExcluiVinculo = "     c18_codcon = " . $this->getCodigoConta();
            $sWhereExcluiVinculo .= " and c18_anousu = " . $iAno;
            $oDaoPCASPContaCorrente->excluir(null, $sWhereExcluiVinculo);

            if ($oDaoPCASPContaCorrente->erro_status == "0") {
                $sMsgPCASPContaCorrente = "O plano de contas não pode ser inserido.\n\n";
                $sMsgPCASPContaCorrente .= $oDaoPCASPContaCorrente->erro_msg;
                throw new Exception(str_replace("\\n", "\n", $sMsgPCASPContaCorrente));
            }
        }

        if ($this->getSistemaConta()->getCodigoSistemaConta() !== 6) {
            $buscaMaisReduzidos = "
              select c61_codcon,
                     c61_anousu,
                     c61_instit,
                     count(*) as total
                from conplanoreduz
               where c61_codcon = {$this->getCodigoConta()}
                 and c61_anousu = {$this->getAno()}
               group by c61_codcon, c61_anousu, c61_instit having count(*) > 1;";

            $buscaMaisReduzidos = db_query($buscaMaisReduzidos);

            if (!$buscaMaisReduzidos) {
                throw new DBException("Ocorreu um erro ao consultar os reduzidos para esta conta.");
            }

            $totalRegistros = pg_num_rows($buscaMaisReduzidos);

            for ($row = 0; $row < $totalRegistros; $row++) {
                $stdDados = db_utils::fieldsMemory($buscaMaisReduzidos, $row);

                if ($stdDados->total > 1) {
                    $mensagem = "Foram encontrados mais de um código reduzido para esta conta dentro do mesmo ano e instituição, ";
                    $mensagem .= "portanto não é possível alterar o detalhamento do sistema.";
                    throw new BusinessException($mensagem);
                }
            }

            $daoContaBancaria = new cl_conplanocontabancaria();
            $daoContaBancaria->excluir(null, "c56_codcon = {$this->getCodigoConta()}");

            if ($daoContaBancaria->erro_status === '0') {
                throw new DBException("Ocorreu um erro ao excluir os vínculos da conta bancária com a conta do plano.");
            }
        }

        return true;
    }


    /*
       retorna se um reduzido possui conciliacao bancaria aberta
    */
    public function hasConciliacaoVinculada()
    {

        $oContaBancaria = $this->getContaBancaria();
        $iContaBancaria = $oContaBancaria->getSequencialContaBancaria();
        $oDaoConcilia = new cl_concilia();
        $sSql = $oDaoConcilia->sql_query_file(null, "*", null, "k68_contabancaria = {$iContaBancaria} ");
        $oDaoConcilia->sql_record($sSql);
        if ($oDaoConcilia->numrows > 0) {
            return true;
        }
        return false;
    }

    /**
     * Faz o vinculo dos atributos com os conta correntes selecionados
     * @param $iContaSistemaContaCorrente
     * @return true
     * @throws Exception
     */
    public function vincularContaCorrente($iContaSistemaContaCorrente)
    {

        $daoConplanoAtributos = new cl_conplanoatributos();
        $where = "c120_conplano = " . $this->getCodigoConta() . " and c120_conplanosistema = {$iContaSistemaContaCorrente} ";
        $rsAtributos = db_query($daoConplanoAtributos->sql_query_file(null, "*", null, $where));
        if (pg_num_rows($rsAtributos) > 0) {
            throw new Exception("Conta corrente já vinculado a conta contábil");
        }

        $oDaoConplanoSistema = new cl_conplanosistemaatributos();
        $where = "c129_conplanosistema = {$iContaSistemaContaCorrente}";
        $sSqlAtributos = $oDaoConplanoSistema->sql_query_file(null, "*", "c129_ordem", $where);
        $rsAtributos = db_query($sSqlAtributos);
        $atributos = db_utils::getCollectionByRecord($rsAtributos);

        $ultimoAnoPlano = $this->getUltimoAnoPlano();
        for ($anoPercorrido = $this->iAno; $anoPercorrido <= $ultimoAnoPlano; $anoPercorrido++) {

            foreach ($atributos as $atributo) {

                $daoConplanoAtributos = new cl_conplanoatributos();
                $daoConplanoAtributos->c120_sequencial = null;
                $daoConplanoAtributos->c120_anousu = $anoPercorrido;
                $daoConplanoAtributos->c120_conplano = $this->getCodigoConta();
                $daoConplanoAtributos->c120_infocomplementar = $atributo->c129_conplanoinfocomplementar;
                $daoConplanoAtributos->c120_conplanosistema = $atributo->c129_conplanosistema;
                $daoConplanoAtributos->incluir(null);
                if ($daoConplanoAtributos->erro_status == 0) {
                    throw new \Exception("Erro ao vincular atributos a conta. Erro interno [{$daoConplanoAtributos->erro_msg}]");
                }
            }
        }
        return true;
    }


    /**
     *
     * verifica o parametro da contabilidade domicilio bancario
     *  - se estiver ativo poderemos ter varios reduzidos para mesma conta e mesma instituicao
     *  - se estiver desativado nao permitira ter mais de um reduzido para conta e instit
     * @return true
     *
     * @throws Exception
     */
    private function validaDamicilioBancario()
    {

        // parametro da contabilidade Domicilio Bancario
        $lUtilizaDomicilioBancario = UTILIZA_DOMICILIO_BANCARIO;
        $iConta = $this->getCodigoConta();
        $iInstituicaoProximoReduzido = $this->getInstituicao();

        // percorre os reduzidos da conta para verificar a instituicao que possui cadastro
        foreach ($this->getContasReduzidas() as $oReduzidos) {

            $iInstituicaoReduzidoExistente = $oReduzidos->c61_instit;
            // compara cada instit cadastrada com a que se esta tentando incluir
            if ($iInstituicaoProximoReduzido == $iInstituicaoReduzidoExistente && !$lUtilizaDomicilioBancario) {

                $sMensagem = "Já existem reduzidos para a conta: {$iConta}.\n";
                $sMensagem .= "Permitido somente ao utilizar Domicilio Bancário.";
                throw new Exception($sMensagem);
            }

        }
        return true;
    }

    /**
     * Persiste dados reduzidos de uma conta
     * @return true
     * @throws Exception
     */
    public function persistirReduzido()
    {

        if (!db_utils::inTransaction()) {
            throw new Exception("Transação com o banco de dados não encontrada.");
        }

        if (!$this->isContaAnalitica()) {
            throw new Exception("Não é possível incluir um reduzido para contas que possuem filhas.");
        }

        $oDaoReduzido = new cl_conplanoreduz();
        $oDaoConPlanoExe = new cl_conplanoexe();
        if ($this->getSistemaConta()->getCodigoSistemaConta() !== 6 &&
            $this->hasReduzidoAnoInstituicao() && $this->lInclusao &&
            $this->getRecurso() == $this->oDadosAnteriores->c61_codigo) {

            throw new Exception("Existe uma conta reduzida para o ano e instituição informados.");
        }

        if ($this->lInclusao) {
            $this->setReduzido(null);
        }


        /**
         * Insere os dados de acordo com o último ano cadastrado na tabela conplano.
         * Percorre um FOR inserindo na conplanoreduz utilizando o mesmo código reduzido (c61_reduz) sempre.
         */
        $iUltimoAno = $this->getUltimoAnoPlano();
        $lAlteraReduz = false;

        $oIntituicao = InstituicaoRepository::getInstituicaoSessao();
        $lPrefeitura = $oIntituicao->prefeitura();

        for ($iAno = $this->getAno(); $iAno <= $iUltimoAno; $iAno++) {

            $oDaoReduzido->c61_anousu = $iAno;
            $oDaoReduzido->c61_instit = $this->getInstituicao();
            $oDaoReduzido->c61_codigo = $this->getRecurso();
            $oDaoReduzido->c61_codcon = $this->getCodigoConta();
            $oDaoReduzido->c61_reduz = $this->getReduzido();
            $oDaoReduzido->c61_contrapartida = $this->getContraPartida();

            /*
             * Dados que serão inseridos na tabela conplanoexe
             */
            $oDaoConPlanoExe->c62_anousu = $iAno;
            $oDaoConPlanoExe->c62_codrec = $this->getRecurso();
            $oDaoConPlanoExe->c62_reduz = $this->getReduzido();

            if ($this->getReduzido() != "") {

                $sWhereReduzido = "    c61_reduz  = {$this->getReduzido()}";
                $sWhereReduzido .= "and c61_instit = {$this->getInstituicao()}";
                $sWhereReduzido .= "and c61_anousu = {$iAno}";
                $oDaoValidaReduz = new cl_conplanoreduz;
                $sSqlValidaReduz = $oDaoValidaReduz->sql_query_file(null, null, "*", null, $sWhereReduzido);
                $rsValidaReduz = $oDaoValidaReduz->sql_record($sSqlValidaReduz);
                if ($oDaoValidaReduz->numrows > 0) {
                    $lAlteraReduz = true;
                }

                $sWhereReduzido = " c61_reduz  = {$this->getReduzido()}";
                $sSqlVerificaReduzidoInstituicao = $oDaoReduzido->sql_query_file(null, null, "*", "1 limit 1",
                    $sWhereReduzido);
                $rsReduzidoInstituicao = $oDaoReduzido->sql_record($sSqlVerificaReduzidoInstituicao);
                if ($oDaoReduzido->numrows > 0) {

                    $oDadoReduzidoInstituicao = db_utils::fieldsMemory($rsReduzidoInstituicao, 0);
                    if (($oDadoReduzidoInstituicao->c61_instit != $this->getInstituicao()) && !$lPrefeitura) {

                        $oDaoReduzido->c61_reduz = null;
                    }
                }
            }

            if ($this->getReduzido() == "" && !$lAlteraReduz) {
                $this->validaDamicilioBancario();
                $oDaoReduzido->incluir($oDaoReduzido->c61_reduz, $iAno);
                $this->setReduzido($oDaoReduzido->c61_reduz);

                $oDaoConPlanoExe->c62_vlrcre = "0";
                $oDaoConPlanoExe->c62_vlrdeb = "0";
                $oDaoConPlanoExe->incluir($iAno, $this->getReduzido());
            } else if (!$lAlteraReduz) {
                $oDaoReduzido->incluir($oDaoReduzido->c61_reduz, $iAno);
                $this->setReduzido($oDaoReduzido->c61_reduz);
                $oDaoConPlanoExe->c62_vlrcre = "0";
                $oDaoConPlanoExe->c62_vlrdeb = "0";
                $oDaoConPlanoExe->incluir($iAno, $this->getReduzido());
            } else {
                $oDaoReduzido->alterar($oDaoReduzido->c61_reduz, $iAno);
                $oDaoConPlanoExe->alterar($iAno, $oDaoReduzido->c61_reduz);
            }

            $lAlteraReduz = false;

            if ($oDaoReduzido->erro_status == 0) {

                $sMsgReduzido = "Ocorreu um erro ao processar os reduzidos.\n\n";
                $sMsgReduzido .= $oDaoReduzido->erro_msg;
                throw new Exception($sMsgReduzido);
            }

            if ($oDaoConPlanoExe->erro_status == 0) {

                $sMsgConPlanoExe = "Ocorreu um erro ao processar os registros do exercício.\n\n";
                $sMsgConPlanoExe .= $oDaoConPlanoExe->erro_msg;
                throw new Exception($sMsgConPlanoExe);
            }

            if (!empty($this->oContaBancaria)) {
                /**
                 *
                 * Salva os dados de conta bancaria
                 */
                $daoConplanoContabancaria = new cl_conplanocontabancaria();
                $buscaSequenciais = $daoConplanoContabancaria->sql_query_file(
                    null,
                    "array_to_string(array_accum(c56_sequencial), ',') as sequenciais",
                    null,
                    "c56_reduz = {$this->getReduzido()} and c56_anousu = {$iAno}"
                );

                $rsBuscaSequenciais = db_query($buscaSequenciais);
                if (!$rsBuscaSequenciais) {

                    $sMensagem = "Ocorreu um erro ao buscar os sequenciais de vínculo entre a conta ";
                    $sMensagem .= "bancária e plano de contas.";
                    throw new DBException($sMensagem);
                }

                $sequenciaisContas = db_utils::fieldsMemory($rsBuscaSequenciais, 0)->sequenciais;

                if ($sequenciaisContas != '') {
                    $contas = explode(',', $sequenciaisContas);

                    foreach ($contas as $sequencial) {
                        $daoConplanoContabancaria->c56_sequencial = $sequencial;
                        $daoConplanoContabancaria->c56_contabancaria = $this->oContaBancaria->getSequencialContaBancaria();
                        $daoConplanoContabancaria->c56_reduz = $this->getReduzido();
                        $daoConplanoContabancaria->c56_anousu = $iAno;
                        $daoConplanoContabancaria->c56_codcon = $this->getCodigoConta();
                        $daoConplanoContabancaria->alterar($sequencial);
                    }
                } else if ($this->oContaBancaria->getSequencialContaBancaria() != '') {
                    $daoConplanoContabancaria->c56_anousu = $iAno;
                    $daoConplanoContabancaria->c56_codcon = $this->getCodigoConta();
                    $daoConplanoContabancaria->c56_contabancaria = $this->oContaBancaria->getSequencialContaBancaria();
                    $daoConplanoContabancaria->c56_reduz = $this->getReduzido();
                    $daoConplanoContabancaria->incluir(null);
                    if ($daoConplanoContabancaria->erro_status == 0) {

                        $sMensagem = "Ocorreu um erro vincular a conta bancária a conta contábil.\n";
                        $sMensagem .= $daoConplanoContabancaria->erro_msg;
                        throw new DBException($sMensagem);
                    }
                }
            }
        }

        $daoSaldoRecurso = new cl_conplanoexecontacorrente();
        if (!empty($this->oDadosAnteriores->c61_codigo) && $this->getRecurso() != $this->oDadosAnteriores->c61_codigo) {
            $sql = $daoSaldoRecurso->sql_saldo_recurso(
                $this->getReduzido(),
                $this->getAno(),
                $this->oDadosAnteriores->c61_codigo
            );

            $rs = db_query($sql);
            if ($rs && pg_num_rows($rs) !== 0) {
                $daoSaldoRecurso->excluir(db_utils::fieldsMemory($rs, 0)->id);
            }
        }

        $sql = $daoSaldoRecurso->sql_saldo_recurso($this->getReduzido(), $this->getAno(), $this->getRecurso());
        $rs = db_query($sql);

        if ($rs && pg_num_rows($rs) === 0) {
            $natureza = sinalContaBalanceteVerificacao(substr($this->getEstrutural(), 0, 1), 0);
            $daoSaldoRecurso->c143_conplanoreduz = $this->getReduzido();
            $daoSaldoRecurso->c143_exercicio = $this->getAno();
            $daoSaldoRecurso->c143_conplanosistema = 100;
            $daoSaldoRecurso->c143_saldo = 0;
            $daoSaldoRecurso->c143_natureza = $natureza;
            $daoSaldoRecurso->incluir(null);

            if ($daoSaldoRecurso->erro_status == 0) {
                throw new DBException('Erro ao incluir saldo inicial por recurso.');
            }

            $daoCA = new cl_conplanoexecontacorrenteatributo();

            $daoCA->c144_conplanoexecontacorrente = $daoSaldoRecurso->id;
            $daoCA->c144_conplanoinfocomplementar = 100;
            $daoCA->c144_valor = $this->getRecurso();
            $daoCA->incluir(null);
            if ($daoCA->erro_status == 0) {
                throw new DBException('Erro ao incluir recurso do saldo inicial.');
            }
        }


        $this->getSistemaConta()->integrarDados($this);
        return true;
    }

    /**
     * Exclui um plano de conta caso as validações ocorram com sucesso
     * @return boolean
     * @throws Exception
     */
    public function excluir()
    {

        if (!db_utils::inTransaction()) {
            throw new Exception("Transação com o banco de dados não encontrada.");
        }
        if ($this->getVinculoContaOrcamento()) {

            $sMsg = "Existem vinculos dessa conta com contas do plano orçamentário.\n";
            $sMsg .= "Para a exclusão dessa conta, reenvicule as contas do plano orçamentario com outra conta do PCASP.";
            throw new Exception($sMsg);
        }
        /*
         * Instancia as classes que serão utilizadas
         */
        $oDaoConPlano = new cl_conplano;
        $oDaoConPlanoReduz = new cl_conplanoreduz;
        $oDaoConlancamVal = new cl_conlancamval;
        $oDaoConPlanoExe = new cl_conplanoexe;
        $oDaoConPlanoContaBancaria = new cl_conplanocontabancaria;
        $oDaoPCASPContaCorrente = new cl_conplanocontacorrente;

        $aContasReduzidas = $this->getContasReduzidas();
        if ($aContasReduzidas) {

            foreach ($aContasReduzidas as $oContaReduzida) {

                $this->iReduzido = $oContaReduzida->c61_reduz;
                $this->removerReduzido($oContaReduzida->c61_reduz);
            }
        }

        /**
         * Exclui da tabela conplano
         */
        $iUltimoAno = $this->getUltimoAnoPlano();
        $lAlteraReduz = false;
        for ($iAno = $this->getAno(); $iAno <= $iUltimoAno; $iAno++) {

            /**
             * Exclui os Vinculos com a Conta Corrente
             */
            $sWhereExcluiVinculoContaCorrente = " c18_codcon = " . $this->getCodigoConta();
            $sWhereExcluiVinculoContaCorrente .= " and c18_anousu = " . $this->getAno();
            $rsExcluiVinculoContaCorrente = $oDaoPCASPContaCorrente->excluir(null, $sWhereExcluiVinculoContaCorrente);

            /**
             * Exclui os Dados da Conta Bancária
             */
            $this->removerDadosContaBancaria($iAno);


            $daoConplanoAtributos = new cl_conplanoatributos();
            $daoConplanoAtributos->excluir(null, "c120_anousu = $iAno and c120_conplano = " . $this->getCodigoConta());
            if ($daoConplanoAtributos->erro_status == 0) {

                $sMsgConPlano = "Não é possível excluir o plano de contas.\n\n";
                $sMsgConPlano .= $oDaoConPlano->erro_msg;
                throw new Exception($sMsgConPlano);
            }

            $rsExcluiConPlano = $oDaoConPlano->excluir($this->getCodigoConta(), $iAno);
            if ($oDaoConPlano->erro_status == 0) {

                $sMsgConPlano = "Não é possível excluir o plano de contas.\n\n";
                $sMsgConPlano .= $oDaoConPlano->erro_msg;
                throw new Exception($sMsgConPlano);
            }
        }
        return true;
    }

    /**
     * Função que remove o reduzido de uma conta plano sintética
     * @param integer $iCodigoReduzido - Código reduzido (conplanoreduz)
     * @return bool
     * @throws Exception
     */
    public function removerReduzido($iCodigoReduzido)
    {

        if (!db_utils::inTransaction()) {
            throw new Exception("Transação com o banco de dados não encontrada.");
        }

        $iUltimoAno = $this->getUltimoAnoPlano();
        $iAnoUso = db_getsession("DB_anousu");

        /**
         * Verifica se existe lançamento no <= $iAnoUso
         */
        if ($this->possuiLancamentoContabil($iCodigoReduzido, $iAnoUso)) {
            throw new Exception("Conta possui lançamentos, não pode ser excluída.");
        }

        /**
         * Valida se existe saldo lançado para a conta no ano e reduzido informado
         */
        if ($this->possuiMovimentacao($iCodigoReduzido, $iAnoUso)) {
            throw new Exception("Esta conta não pode ser excluída pois possui saldo inicial lançado.");
        }

        $this->getSistemaConta()->excluirDadosIntegrados($this);

        for ($iAnoFor = $iAnoUso; $iAnoFor <= $iUltimoAno; $iAnoFor++) {
            $daoSaldoRecurso = new cl_conplanoexecontacorrente();

            $where = "c143_exercicio >= $iAnoFor and c143_conplanoreduz = {$iCodigoReduzido}";
            $daoSaldoRecurso->excluir(null, $where);

            /**
             * Exclui da tabela conplanoexe
             */
            $oDaoConPlanoExe = new cl_conplanoexe;
            $sSqlConPlanoExe = $oDaoConPlanoExe->sql_query_file($iAnoFor, $iCodigoReduzido);
            $rsConPlanoExe = $oDaoConPlanoExe->sql_record($sSqlConPlanoExe);
            if ($oDaoConPlanoExe->numrows > 0) {

                $rsExcluiConPlanoExe = $oDaoConPlanoExe->excluir($iAnoFor, $iCodigoReduzido);
                if ($oDaoConPlanoExe->erro_status == 0) {

                    $sMsgConPlanoExe = "Não é possível excluir o plano de conta do exercício {$iAnoFor}.\n\n";
                    $sMsgConPlanoExe .= $oDaoConPlanoExe->erro_msg;
                    throw new Exception($sMsgConPlanoExe);
                }
            }

            /**
             * Exclui da tabela conplanoreduz
             */
            $oDaoConPlanoReduz = new cl_conplanoreduz;
            $sSqlConPlanoReduz = $oDaoConPlanoReduz->sql_query_file($iCodigoReduzido, $iAnoFor);
            $rsConPlanoReduz = $oDaoConPlanoReduz->sql_record($sSqlConPlanoReduz);

            if ($oDaoConPlanoReduz->numrows > 0) {

                $oDaoConPlanoReduz->excluir($iCodigoReduzido, $iAnoFor);

                if ($oDaoConPlanoReduz->erro_status == 0) {

                    $sMsgConPlanoReduz = "Não é possível excluir as contas reduzidas deste plano.\n\n";
                    $sMsgConPlanoReduz .= $oDaoConPlanoReduz->erro_msg;
                    throw new Exception($sMsgConPlanoReduz);
                }
            }

            $sWhereExistReduzido = " c61_codcon = " . $this->getCodigoConta();
            $sSqlExistReduzido = $oDaoConPlanoReduz->sql_query_file(null, null, "*", null, $sWhereExistReduzido);
            $rsExistReduzido = $oDaoConPlanoReduz->sql_record($sSqlExistReduzido);
            $iNumrowsSelect = $oDaoConPlanoReduz->numrows;

            /**
             * Exclui os Vinculos com a Conta Corrente caso não tenha mais reduzidos na conta do plano
             */
            if ($iNumrowsSelect == 0) {

                $oDaoPCASPContaCorrente = new cl_conplanocontacorrente;
                $sWhereExcluiVinculoContaCorrente = "     c18_codcon = " . $this->getCodigoConta();
                $sWhereExcluiVinculoContaCorrente .= " and c18_anousu >= " . $iAnoUso;
                $rsExcluiVinculoContaCorrente = $oDaoPCASPContaCorrente->excluir(null,
                    $sWhereExcluiVinculoContaCorrente);
            }

        }
        return true;
    }

    /**
     * Verifica se existem movimentação com o reduzido e ano. Retorna TRUE caso haja saldo.
     * @param integer $iReduzido
     * @param integer $iAno
     * @return boolean
     */
    public function possuiMovimentacao($iReduzido, $iAno)
    {

        $oDaoConPlanoExe = new cl_conplanoexe();
        $sWhereConPlanoExe = "     (c62_vlrcre > 0 or c62_vlrdeb > 0) ";
        $sWhereConPlanoExe .= " and c61_reduz   = {$iReduzido}         ";
        $sWhereConPlanoExe .= " and c61_anousu >= {$iAno}              ";
        $sSqlConPlanoExe = $oDaoConPlanoExe->sql_query_reduzido(null, null, "*", null, $sWhereConPlanoExe);
        $rsConPlanoExe = $oDaoConPlanoExe->sql_record($sSqlConPlanoExe);
        if ($oDaoConPlanoExe->numrows > 0) {
            return true;
        }
        return false;
    }

    /**
     * Valida se já existe um reduzido cadastrado para o Ano e Instituição
     * @return boolean
     */
    public function hasReduzidoAnoInstituicao()
    {

        $oDaoReduzido = new cl_conplanoreduz;
        $sWhereReduzido = "     c61_instit = {$this->getInstituicao()} ";
        $sWhereReduzido .= " and c61_codcon = {$this->getCodigoConta()} ";
        $sWhereReduzido .= " and c61_anousu = {$this->getAno()}         ";
        $sSqlReduzidoInstit = $oDaoReduzido->sql_query_file(null, null, "*", null, $sWhereReduzido);
        $rsReduzidoInstit = $oDaoReduzido->sql_record($sSqlReduzidoInstit);
        if ($oDaoReduzido->numrows > 0) {
            return true;
        }
        return false;
    }

    /**
     * Busca todos os reduzidos do plano de conta
     * @return bool|stdClass[]
     */
    public function getContasReduzidas()
    {

        if (empty($this->aContasReduzidas) && $this->getCodigoConta() != "") {

            $oDaoReduzido = new cl_conplanoreduz();
            $sWhereReduzido = "    conplanoreduz.c61_codcon = {$this->getCodigoConta()}";
            $sWhereReduzido .= "and conplanoreduz.c61_anousu >= " . db_getsession("DB_anousu");
            $sSqlBuscaReduzidos = $oDaoReduzido->sql_query_reduz_contacorrente(null, null, "*", null, $sWhereReduzido);
            $rsBuscaReduzidos = $oDaoReduzido->sql_record($sSqlBuscaReduzidos);

            if ($oDaoReduzido->numrows > 0) {
                $this->aContasReduzidas = db_utils::getCollectionByRecord($rsBuscaReduzidos, false, false, true);
            }
        }
        return $this->aContasReduzidas;
    }


    /**
     * Busca todos os reduzidos do plano de conta passado por parâmetro pelo ano atual
     * @return mixed
     */
    public function getContasReduzidasAno()
    {

        if (empty($this->aContasReduzidasNoAno) && $this->getCodigoConta() != "") {

            $oDaoReduzido = new cl_conplanoreduz();
            $sWhereReduzido = "    conplanoreduz.c61_codcon = {$this->getCodigoConta()}";
            $sWhereReduzido .= "and conplanoreduz.c61_anousu = " . db_getsession("DB_anousu");
            $sSqlBuscaReduzidos = $oDaoReduzido->sql_query_reduz_contacorrente(null, null, "*", null, $sWhereReduzido);
            $rsBuscaReduzidos = $oDaoReduzido->sql_record($sSqlBuscaReduzidos);

            if ($oDaoReduzido->numrows > 0) {
                $this->aContasReduzidasNoAno = db_utils::getCollectionByRecord($rsBuscaReduzidos, false, false, true);
            }
        }
        return $this->aContasReduzidasNoAno;
    }


    /**
     * Vincula um diversos planos de contas do plano orçamentário a uma conta do plano PCASP
     * @param integer $iCodigoPlanoOrcamento
     * @return bool
     * @throws Exception
     */
    public function vinculaPlanoContasOrcamento($iCodigoPlanoOrcamento)
    {

        if (!db_utils::inTransaction()) {
            throw new Exception("Transação com o banco de dados não encontrada.");
        }

        /*
         * Verifica se já existe o plano vinculado.
         */
        $oDaoVinculo = new cl_conplanoconplanoorcamento;
        $sWhereVinculo = "c72_conplanoorcamento = {$iCodigoPlanoOrcamento}";

        $sCampos = "conplano.c60_codcon as codigo, conplano.c60_estrut as estrutural,";
        $sCampos .= "conplano.c60_descr as descricao_conta";

        $sSqlBuscaVinculo = $oDaoVinculo->sql_query(null, $sCampos, null, $sWhereVinculo);
        $rsBuscaVinculo = $oDaoVinculo->sql_record($sSqlBuscaVinculo);

        if ($oDaoVinculo->numrows > 0) {

            $oDadosConta = db_utils::fieldsMemory($rsBuscaVinculo, 0);
            $sMsgVinculo = "Esta conta do plano orçamentário já está vínculada para a conta PCASP ";
            $sMsgVinculo .= "{$oDadosConta->codigo} - {$oDadosConta->estrutural} {$oDadosConta->descricao_conta}";
            throw new Exception($sMsgVinculo);
        }

        /*
         * Seta as propriedades que serão utilizadas na tabela e salva sempre para frente.
         */
        $oDaoVinculo->c72_conplano = $this->getCodigoConta();
        $oDaoVinculo->c72_conplanoorcamento = $iCodigoPlanoOrcamento;

        $iUltimoAno = $this->getUltimoAnoPlano("c60_codcon = {$this->getCodigoConta()}");

        for ($iAno = db_getsession("DB_anousu"); $iAno <= $iUltimoAno; $iAno++) {

            $oDaoVinculo->c72_anousu = $iAno;
            $oDaoVinculo->incluir(null);
            if ($oDaoVinculo->erro_status == "0") {

                $sMsgVinculo = "Não foi possível vincular a conta {$this->getCodigoConta()}.\n\n";
                $sMsgVinculo .= $oDaoVinculo->erro_msg;
            }
        }
        return true;
    }

    /**
     * Função que exclui o vínculo
     * Enter description here ...
     * @param $iCodigoContaOrcamento
     * @return bool
     * @throws Exception
     */
    public function excluiVinculoContaOrcamento($iCodigoContaOrcamento)
    {

        if (!db_utils::inTransaction()) {
            throw new Exception("Transação com o banco de dados não encontrada.");
        }

        $oDaoExcluiVinculo = new cl_conplanoconplanoorcamento;
        $sWhereExclui = "    c72_conplanoorcamento = {$iCodigoContaOrcamento}";
        $sWhereExclui .= "and c72_conplano = {$this->getCodigoConta()}";
        $rsExcluiVinculo = $oDaoExcluiVinculo->excluir(null, $sWhereExclui);

        if ($oDaoExcluiVinculo->erro_status == "0") {
            throw new Exception("Ocorreu um erro ao desvincular a conta orcamentária.\n\n{$this->erro_msg}");
        }
        return true;
    }

    /**
     * Busca as contas do plano orçamentário vinculadas a um plano de contas.
     * @return mixed
     */
    public function getVinculoContaOrcamento()
    {

        $oDaoVinculoContas = new cl_conplanoconplanoorcamento;
        $sCampoVinculo = "conplanoorcamento.c60_codcon, conplanoorcamento.c60_descr, conplanoorcamento.c60_estrut";
        $sWhereVinculo = "     conplano.c60_codcon = {$this->getCodigoConta()}          ";
        $sWhereVinculo .= " and conplanoconplanoorcamento.c72_anousu = {$this->getAno()} ";
        $sSqlVinculoConta = $oDaoVinculoContas->sql_query(null, $sCampoVinculo, null, $sWhereVinculo);
        $rsVinculoContas = $oDaoVinculoContas->sql_record($sSqlVinculoConta);

        if ($oDaoVinculoContas->numrows > 0) {
            return db_utils::getCollectionByRecord($rsVinculoContas);
        }
        return false;
    }

    /**
     * Remove os dados da conta bancaria
     * @param integer $iAno ano de cadastro da conta bancaria.
     * @throws Exception
     */
    protected function removerDadosContaBancaria($iAno)
    {

        $oDaoConPlanoContaBancaria = new cl_conplanocontabancaria;
        $sWhereContaBancaria = "c56_codcon = {$this->getCodigoConta()} and c56_anousu = {$iAno}";
        $sSqlContaBancaria = $oDaoConPlanoContaBancaria->sql_query_file(null, "*", null, $sWhereContaBancaria);
        $rsContaBancaria = $oDaoConPlanoContaBancaria->sql_record($sSqlContaBancaria);
        if ($oDaoConPlanoContaBancaria->numrows > 0) {

            $rsExcluiContaBancaria = $oDaoConPlanoContaBancaria->excluir(null, $sWhereContaBancaria);
            if ($oDaoConPlanoContaBancaria->erro_status == 0) {

                $sMsgContaBancaria = "Não é possível excluir as contas bancárias do exercício {$iAno}.\n\n";
                $sMsgContaBancaria .= str_replace("\\n", "\n", $oDaoConPlanoContaBancaria->erro_msg);
                throw new Exception($sMsgContaBancaria);
            }
        }
    }

    /**
     *
     * Seta a propriedade ContaCorrente
     */
    public function setContaCorrente(ContaCorrente $oContaCorrente)
    {

        $this->oContaCorrente = $oContaCorrente;
        return $this;
    }

    /**
     * Retorna uma instancia de Conta Corrente
     * @return ContaCorrente
     */
    public function getContaCorrente()
    {

        return $this->oContaCorrente;
    }


    /**
     * Verifica se o estrutural informado está apto a ser utilizado
     *
     * @param $sEstrutural
     * @return bool
     * @throws BusinessException
     * @internal param ContaPlanoPCASP $oConta
     */
    public function verificarEstrutural($sEstrutural)
    {

        $oInstituicao = InstituicaoRepository::getInstituicaoByCodigo($this->getInstituicao());

        /**
         * Verificamos se existe outra conta com o novo Estrutural
         */
        $oContaExistente = ContaPlanoPCASPRepository::getContaPorEstrutural($sEstrutural, $this->getAno());
        if (!empty($oContaExistente)) {

            if ($oContaExistente->getCodigoConta() !== $this->getCodigoConta()) {
                throw new BusinessException ("Estrutural '{$sEstrutural}' já informado para a conta {$oContaExistente->getCodigoConta()}");
            }
        }

        $sEstruturalFormatado = db_formatar($sEstrutural, 'receita');
        $iNivelConta = ContaPlano::getNivelEstrutura($sEstruturalFormatado);

        /**
         *  Verificamos se existe uma conta acima dela, e esta conta nao é uma conta analitica
         */
        $sEstruturalPai = ContaPlano::getCodigoEstruturalPai($sEstruturalFormatado);
        $oContaPaiExistente = ContaPlanoPCASPRepository::getContaPorEstrutural(str_replace(".", "", $sEstruturalPai),
            $this->getAno(),
            $oInstituicao);

        if (empty($oContaPaiExistente) || ($iNivelConta > $iNivelConta + 1)) {
            throw new BusinessException ("Estrutural '{$sEstrutural}' não possui uma conta sintética válida");
        }

        if ($oContaPaiExistente->hasReduzidoAnoInstituicao()) {
            throw new BusinessException (" A conta de nível acima da conta '{$sEstrutural}' é uma conta Analítica.");
        }

        /**
         * Verificamos se existe alguma conta analitica a baixo do estrutural original. Casoe exista, nao podemos
         * alterar o estrutural.
         */
        $oDaoConplano = new cl_conplano;
        $sEstruturalConta = db_formatar($this->getEstrutural(), 'receita');
        $iNivelConta = ContaPlano::getNivelEstrutura($sEstruturalConta);

        $sConta = str_replace(".", "", ContaPlano::getEstruturaAteNivel($sEstruturalConta, $iNivelConta));
        $sWhere = " c60_estrut like '{$sConta}%' ";
        $sWhere .= " and c61_reduz is not null ";
        $sWhere .= " and c60_anousu = {$this->getAno()} ";

        $sSqlContasAnaliticas = $oDaoConplano->sql_query_dados_plano(null, "c60_estrut, c61_reduz", "c60_estrut",
            $sWhere);
        $rsContasAnaliticas = $oDaoConplano->sql_record($sSqlContasAnaliticas);
        if ($rsContasAnaliticas && $oDaoConplano->numrows > 0) {

            $iTotalContas = 0;
            for ($iConta = 0; $iConta < $oDaoConplano->numrows++; $iConta++) {

                $oDadosConta = db_utils::fieldsMemory($rsContasAnaliticas, $iConta++);
                $sEstruturalAnalitica = db_formatar($oDadosConta->c60_estrut, 'receita');
                /**
                 * Verificamos se a conta está no nivel abaixo da conta
                 */
                if (ContaPlano::getNivelEstrutura($sEstruturalAnalitica) - 1 == $iNivelConta) {
                    $iTotalContas++;
                }
            }

            if ($iTotalContas > 0) {

                $sMensagem = "A conta {$sEstruturalConta} possui contas analíticas. ";
                $sMensagem .= "Não será possivel alterar seu estrutural.";
                throw new BusinessException($sMensagem);
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function validarEstrutural()
    {
        return $this->validaEstrutural();
    }

    /**
     * Remove todos as informações complementares da conta
     * @param integer $sistema codigo do sistema
     * @throws Exception
     */
    public function removerInformacoesComplementares($sistema = 1)
    {

        $daoConplanoAtributos = new cl_conplanoatributos();
        $where = array(
            "c120_anousu >= {$this->getAno()}",
            "c120_conplanosistema = $sistema",
            "c120_conplano = {$this->getCodigoConta()}"
        );
        $daoConplanoAtributos->excluir(null, implode(" and ", $where));
        if ($daoConplanoAtributos->erro_status == 0) {
            throw new Exception("Erro ao remover informações complementares da conta {$this->getEstruturalComMascara()} = {$this->getDescricao()}");
        }
    }

    /**
     * @param int $sistema
     * @param array $informacoesComplementares
     * @throws Exception
     */
    public function adicionarInformacoesComplementares($sistema = 1, array $informacoesComplementares)
    {

        $daoConplanoAtributos = new cl_conplanoatributos();
        $ultimoAno = $this->getUltimoAnoPlano("c60_estrut = '{$this->getEstrutural()}'");
        $anoAtual = $this->getAno();
        foreach ($informacoesComplementares as $informacaoComplementar) {

            for ($ano = $anoAtual; $ano <= $ultimoAno; $ano++) {

                $daoConplanoAtributos->c120_anousu = $ano;
                $daoConplanoAtributos->c120_sequencial = null;
                $daoConplanoAtributos->c120_conplano = $this->getCodigoConta();
                $daoConplanoAtributos->c120_conplanosistema = $sistema;
                $daoConplanoAtributos->c120_infocomplementar = $informacaoComplementar;
                $daoConplanoAtributos->incluir(null);
                if ($daoConplanoAtributos->erro_status == 0) {
                    throw new Exception("Erro ao salvar informacao complementar da conta {$this->getEstruturalComMascara()} = {$this->getDescricao()}");
                }
            }
        }
    }

    /**
     * @return int
     */
    public function getSistemaContaCorrente()
    {
        return $this->iSistemaContaCorrente;
    }

    /**
     * @param int $iSistemaContaCorrente
     */
    public function setSistemaContaCorrente($iSistemaContaCorrente)
    {
        $this->iSistemaContaCorrente = $iSistemaContaCorrente;
    }

    /**
     * Retorna o sistema de conta corrente da conta contábil.
     * @return _db_fields|null|stdClass
     */
    public function getDadosSistemaContaCorrente()
    {
        $daoConplanoAtributos = new cl_conplanoatributos();
        $campos = "distinct c120_conplanosistema as codigo, c122_descricao as descricao";
        $where = "c120_conplano = {$this->getCodigoConta()} and c120_anousu = {$this->getAno()} and c120_conplanosistema > 1";
        $sql = $daoConplanoAtributos->sql_query(null, $campos, null, $where);
        $rsDadosConta = db_query($sql);
        if (pg_num_rows($rsDadosConta) == 0) {
            return null;
        }
        return db_utils::fieldsMemory($rsDadosConta, 0);
    }


    /**
     * @param $lInclusao
     */
    public function setInclusao($lInclusao)
    {
        $this->lInclusao = $lInclusao;
    }

    /**
     * @param $iCodigoContaCorrente
     * @return bool
     * @throws DBException
     */
    public function excluirContaCorrente($iCodigoContaCorrente)
    {
        $daoConplanoAtributos = new cl_conplanoatributos();
        $daoConplanoAtributos->excluir(null, "c120_conplanosistema = {$iCodigoContaCorrente} and c120_conplano = {$this->getCodigoConta()}");
        if ($daoConplanoAtributos->erro_status === '0') {
            throw new DBException("Não foi possível excluir o vínculo da conta corrente com a conta contábil.");
        }
        return true;
    }

    public function toArray()
    {
        return array(
            'codigoConta' => $this->getCodigoConta(),
            'estrutural' => $this->getEstrutural()
        );
    }

    /**
     * @return bool
     */
    public function isSaldoContinuo()
    {
        return $this->saldoContinuo;
    }

    /**
     * @param bool $saldoContinuo
     */
    public function setSaldoContinuo($saldoContinuo)
    {
        $this->saldoContinuo = $saldoContinuo;
    }

}
