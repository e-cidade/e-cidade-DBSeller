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


class contaTesouraria
{


    /**
     * Codigo da conta = k109_saltesextra vinculada
     *
     * @var integer
     */
    protected $iCodigoContaExtra;


    /**
     * Codigo da conta = k13_Conta
     *
     * @var integer
     */
    protected $iCodigoConta;

    /**
     * Codigo reduzido = k13_reduz
     *
     * @var integer
     */
    protected $iCodigoReduzido;

    /**
     * Descricao do Saldo da Tesouraria = k13_descr
     *
     * @var string
     */
    protected $sDescricao;

    /**
     * Valor do saldo inicial = k13_saldo
     *
     * @var float
     */
    protected $nSaldoInicial;

    /**
     * Identificacao da conta = k13_ident
     *
     * @var string
     */
    protected $sIdentificacao;

    /**
     * Valor atualizado = k13_vlratu
     *
     * @var float
     */
    protected $nValorAtualizado;

    /**
     * Data atual da conta = k13_datvlr
     *
     * @var string
     */
    protected $dtDataAtualizacao;

    /**
     * Data limite = k13_limite
     *
     * @var string
     */
    protected $dtDataLimite;

    /**
     * Data implantacao = k13_dtimplantacao
     *
     * @var string
     */
    protected $dtDataImplantacao;

    /**
     * Outros dados = k13_outrosdados (jsonb)
     *
     * @var string
     */
    protected $outrosDados;

    protected $contrapartida;

    public function setContrapartida($contrapartida)
    {
        $this->contrapartida = $contrapartida;
    }

    public function getContrapartida()
    {
        $oDaosaltescontrapartida = new cl_saltescontrapartida;
        $where = "k103_saltes = {$this->getCodigoConta()}";
        //k103_contrapartida
        $sql = $oDaosaltescontrapartida->sql_query_contrapartida(null, "k103_contrapartida", null, $where);
        $rs = $oDaosaltescontrapartida->sql_record($sql);
        if ($oDaosaltescontrapartida->numrows <= 0) {
            throw new Exception("Nao foi possivel localizar a contrapartida da conta: {$this->getCodigoConta()}");
        }
        return db_utils::fieldsMemory($rs, 0)->k103_contrapartida;
    }



    function __construct($iConta = null)
    {

        if (!empty($iConta)) {
            $oDaoSaltes  = new cl_saltes;
            $sSqlSaltes  = $oDaoSaltes->sql_query_file(null, "*", null, "k13_conta = {$iConta}");
            $rsSqlSaltes = $oDaoSaltes->sql_record($sSqlSaltes);

            if ($oDaoSaltes->numrows > 0) {
                $oSaltes = db_utils::fieldsMemory($rsSqlSaltes, 0);
                $this->iCodigoConta      = $oSaltes->k13_conta;
                $this->iCodigoReduzido   = $oSaltes->k13_reduz;
                $this->sDescricao        = $oSaltes->k13_descr;
                $this->nSaldoInicial     = $oSaltes->k13_saldo;
                $this->sIdentificacao    = $oSaltes->k13_ident;
                $this->nValorAtualizado  = $oSaltes->k13_vlratu;
                $this->dtDataAtualizacao = $oSaltes->k13_datvlr;
                $this->dtDataLimite      = $oSaltes->k13_limite;
                $this->dtDataImplantacao = $oSaltes->k13_dtimplantacao;
                $this->outrosDados       = $oSaltes->k13_outrosdados;

                /**
                 * verifica se tem extra vinculado
                 */
                $oDaoSaltesExtra = new cl_saltesextra;
                $sql = $oDaoSaltesExtra->sql_query_extra(
                    null,
                    "k109_contaextra",
                    null,
                    "k109_saltes = {$oSaltes->k13_conta}"
                );
                $rsExtra = $oDaoSaltesExtra->sql_record($sql);
                if ($oDaoSaltesExtra->numrows > 0) {
                    $this->setContaExtra(db_utils::fieldsMemory($rsExtra, 0)->k109_contaextra);
                }
            }
        }

        return $this;
    }

    public static function getListaContasExtras($campos = "*" , $where ){

        $aContasExtras = [];

        $DaoSaltes = new cl_saltes;

        $sql = $DaoSaltes->sql_query_ListaContasExtras(null, $campos, null, $where);
        $rs = $DaoSaltes->sql_record($sql);
        if ($DaoSaltes->numrows > 0) {
            $aContasExtras = db_utils::getCollectionByRecord($rs);
        }
        return $aContasExtras ;
    }

    public function getContaExtra()
    {
        return $this->iCodigoContaExtra;
    }

    public function setContaExtra($iContaExtra)
    {
        $this->iCodigoContaExtra = $iContaExtra;
    }


    /**
     * Retorna data de atualizacao = k13_datvlr
     *
     * @return string
     */
    public function getDataAtualizacao()
    {

        return $this->dtDataAtualizacao;
    }

    /**
     * Retorna data de implantacao = k13_dtimplantacao
     *
     * @return string
     */
    public function getDataImplantacao()
    {

        return $this->dtDataImplantacao;
    }

    /**
     * Retorna data limite = k13_limite
     *
     * @return string
     */
    public function getDataLimite()
    {

        return $this->dtDataLimite;
    }

    /**
     * Retorna codigo da conta = k13_conta
     *
     * @return integer
     */
    public function getCodigoConta()
    {

        return $this->iCodigoConta;
    }

    /**
     * Retorna codigo reduzido = k13_reduz
     *
     * @return integer
     */
    public function getCodigoReduzido()
    {

        return $this->iCodigoReduzido;
    }

    /**
     * Retorna saldo inicial = k13_saldo
     *
     * @return float
     */
    public function getSaldoInicial()
    {

        return $this->nSaldoInicial;
    }

    /**
     * Retorna valor atualizado = k13_vlratu
     *
     * @return float
     */
    public function getValorAtualizado()
    {

        return $this->nValorAtualizado;
    }

    /**
     * Retorna a descricao da conta = k13_descr
     *
     * @return string
     */
    public function getDescricao()
    {

        return $this->sDescricao;
    }

    /**
     * Retorna a identificacao da conta = k13_ident
     *
     * @return string
     */
    public function getIdentificacao()
    {

        return $this->sIdentificacao;
    }

    /**
     * Retorna o json referente a outros dados= k13_outrosdados
     *
     * @return string
     */
    public function getOutrosDados()
    {

        return $this->outrosDados;
    }

    /**
     * Seta o valor da data de atualizacao = k13_datvlr
     *
     * @param string $dtDataAtualizacao
     */
    public function setDataAtualizacao($dtDataAtualizacao)
    {

        $this->dtDataAtualizacao = $dtDataAtualizacao;
    }

    /**
     * Seta o valor da data de implantacao = k13_dtimplantacao
     *
     * @param string $dtDataImplantacao
     */
    public function setDataImplantacao($dtDataImplantacao)
    {

        $this->dtDataImplantacao = $dtDataImplantacao;
    }

    /**
     * Seta o valor da data limite = k13_limite
     *
     * @param string $dtDataLimite
     */
    public function setDataLimite($dtDataLimite)
    {

        $this->dtDataLimite = $dtDataLimite;
    }

    /**
     * Seta o valor do saldo inicial = k13_saldo
     *
     * @param float $nSaldoInicial
     */
    public function setSaldoInicial($nSaldoInicial)
    {

        $this->nSaldoInicial = $nSaldoInicial;
    }

    /**
     * Seta o valor atualizado = k13_vlratu
     *
     * @param float $nValorAtualizado
     */
    public function setValorAtualizado($nValorAtualizado)
    {

        $this->nValorAtualizado = $nValorAtualizado;
    }

    /**
     * Seta o valor da descricao da conta = k13_descr
     *
     * @param string $sDescricao
     */
    public function setDescricao($sDescricao)
    {

        $this->sDescricao = $sDescricao;
    }

    /**
     * Seta o valor da identificacao da conta = k13_ident
     *
     * @param string $sIdentificacao
     */
    public function setIdentificacao($sIdentificacao)
    {

        $this->sIdentificacao = $sIdentificacao;
    }

    /**
     * Seta novo json para outros dados = k13_dados
     *
     * @param string $outrosDados
     */
    public function setOutrosDados($outrosDados)
    {
        $this->sIdentificacao = $outrosDados;
    }

    /**
     * Retorna as contas conforme filtro
     *
     * @return array $aContas
     */
    public static function getContasByFiltro($sWhere = null)
    {

        $sOrder     = "k13_conta,k13_descr,k13_datvlr,k13_saldo,k13_vlratu";
        $sCampos    = "k13_conta";
        $oDaoSaltes = db_utils::getDao("saltes");
        $sSqlSaltes = $oDaoSaltes->sql_query(null, $sCampos, $sOrder, $sWhere);
        $rsSaltes   = $oDaoSaltes->sql_record($sSqlSaltes);
        $iNumRows   = $oDaoSaltes->numrows;
        $aContas    = array();
        if ($iNumRows > 0) {
            for ($i = 0; $i < $iNumRows; $i++) {
                $iConta    = db_utils::fieldsMemory($rsSaltes, $i)->k13_conta;
                $aContas[] = new contaTesouraria($iConta);
            }
        }
        return $aContas;
    }

    /**
     * Funcao para atualizar implantacao saldo
     *
     * @param float $nDataAtualizar
     * @param float $nSaldoInicial
     * @return saltes
     */
    public function implantarSaldo($dtDataAtualizar, $nSaldoInicial, $lVerificaData = true)
    {

        $oDaoSaltes = db_utils::getDao("saltes");

        if (!db_utils::inTransaction()) {
            throw new Exception('Nao existe transação com o banco de dados ativa.');
        }

        $oDaoSaltes->k13_conta  = $this->iCodigoConta;
        $oDaoSaltes->k13_datvlr = $this->dtDataAtualizacao;
        $oDaoSaltes->k13_saldo  = "{$nSaldoInicial}";
        $oDaoSaltes->alterar($this->iCodigoConta);
        if ($oDaoSaltes->erro_status == 0) {
            throw new Exception($oDaoSaltes->erro_msg);
        }

        $dtDataAtualizar = implode("-", array_reverse(explode("/", $dtDataAtualizar)));

        /*
         * se o parametro $lverificaData == true
         * verifica se a data de processamento e maior que a data da implantacao
         * se for anterior a data de processo recebe a data de implantacao
         */
        if ($lVerificaData == true) {
            if ($dtDataAtualizar < $this->dtDataImplantacao) {
                $dtDataAtualizar = $this->dtDataImplantacao;
            }
        }

        $sSqlSaldoData   = "select substr(fc_saltessaldo($this->iCodigoConta,";
        $sSqlSaldoData  .= "                             '{$this->dtDataImplantacao}', ";
        $sSqlSaldoData  .= "                             '{$dtDataAtualizar}', ";
        $sSqlSaldoData  .= "                             null,".db_getsession("DB_instit")."), 41, 13) as saldo";
        $rsSaldoData     = db_query($sSqlSaldoData);
        $oSaldoSaltes    = db_utils::fieldsMemory($rsSaldoData, 0)->saldo;

        $oDaoSaltes->k13_conta  = $this->iCodigoConta;
        $oDaoSaltes->k13_datvlr = $dtDataAtualizar;
        $oDaoSaltes->k13_vlratu = "{$oSaldoSaltes}";
        $oDaoSaltes->alterar($this->iCodigoConta);
        if ($oDaoSaltes->erro_status == 0) {
            throw new Exception($oDaoSaltes->erro_msg);
        }
        $this->dtDataAtualizacao = $dtDataAtualizar;
        $this->nSaldoInicial     = $nSaldoInicial;
        $this->nValorAtualizado  = $oSaldoSaltes;

        return $this;
    }

    public static function isContaExtra($iReduz)
    {
        $oDaoSaltesExtra = new cl_saltesextra;
        $sql = $oDaoSaltesExtra->sql_query_extra(null, "k109_contaextra", null, "k109_contaextra = {$iReduz}");
        $oDaoSaltesExtra->sql_record($sql);
        if ($oDaoSaltesExtra->numrows > 0) {
            return true;
        }
        return false;
    }

    /**
     * Busca o saldo da conta da tesouraria.
     * @param integer $iConta       Código da conta para a qual será verificado o saldo.
     * @param DBDate  $oDataInicial Data inicial do período para verificação do saldo.
     * @param DBDate  $oDataFinal   Data final do período para verificação do saldo.
     * @param integer $iInstituicao Código da Intituição para a qual será feita a verificação do saldo.
     *
     * @return stdClass com atributos nSaldoAnterior, nDebitado, nCreditado e nSaldoFinal.
     * @throws Exception
     */
    public static function getSaldoTesouraria($iConta, DBDate $oDataInicial, DBDate $oDataFinal, $iInstituicao)
    {

        $sParametros = "{$iConta}, '{$oDataInicial->getDate()}', '{$oDataFinal->getDate()}', null, {$iInstituicao}";
        $sSql        = "select fc_saltessaldo({$sParametros}) as saldo";
        $rsSaldo     = db_query($sSql);

        if (pg_num_rows($rsSaldo) != 1) {
            throw new Exception("Ocorreu um erro ao calcular o saldo da conta.");
        }

        $oSaldo = db_utils::fieldsMemory($rsSaldo, 0);

        if ($oSaldo->saldo == '2') {
            throw new Exception("Não foi encontrado saldo na conta informada.");
        }

        if ($oSaldo->saldo == '3') {
            throw new Exception("Não foi possível verificar o saldo da conta informado. Verifique os parâmetros.");
        }

        $oSaldoTesouraria                 = new stdClass();
        $oSaldoTesouraria->nSaldoAnterior = trim(substr($oSaldo->saldo, 2, 13));
        $oSaldoTesouraria->nDebitado      = trim(substr($oSaldo->saldo, 15, 13));
        $oSaldoTesouraria->nCreditado     = trim(substr($oSaldo->saldo, 28, 13));
        $oSaldoTesouraria->nSaldoFinal    = trim(substr($oSaldo->saldo, 41, 13));

        return $oSaldoTesouraria;
    }
}
