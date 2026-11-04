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


require_once(modification('model/itemSolicitacao.model.php'));

final class itemCompilacao extends itemSolicitacao
{
    /**
     * Quantidade minima do item
     *
     * @var float
     */
    protected $nQuantidadeMinima = 0;

    /**
     * Quantidade máxima do item
     *
     * @var float
     */
    protected $nQuantidadeMaxima = 0;

    /**
     * Item está ativo
     *
     * @var boolean
     */
    protected $lAtivo = false;

    /**
     * Quantidade execedente para compra
     * @var float
     */
    protected $nQuantidadeExecedente = 0;
    /**
     * Código do item no registro de preço
     *
     * @var integer
     */
    private $iCodigoRegistro = null;

    /**
     * Codigo do item que originou o item
     *
     * @var integer
     */
    protected $iCodigoItemOrigem = null;

    protected $aItemEstimativas = array();

    /**
     * Codigo do item no orcamento
     *
     * @var integer
     */
    protected $iCodigoItemOrcamento = null;

    /**
     *
     * @param integer $iItemSolicitacao Codigo do item na solicitação de Compras
     * @param integer $iMaterial Codifgo do material (pcmater.pc01_codmater)
     * @return \itemCompilacao
     */
    function __construct($iItemSolicitacao = null, $iMaterial = null)
    {
        parent::__construct($iItemSolicitacao, $iMaterial);
        if (!empty($iItemSolicitacao)) {

            /**
             * Consultamos as informacoes do registro do preco
             */
            $oDaoSolicitemRegistro = db_utils::getDao("solicitemregistropreco");
            $sSqlRegistro = $oDaoSolicitemRegistro->sql_query_orcamento(null, "*",
              null,
              "pc57_solicitem={$this->iCodigoItemSolicitacao}"
            );

            $rsRegistro = $oDaoSolicitemRegistro->sql_record($sSqlRegistro);
            if ($oDaoSolicitemRegistro->numrows > 0) {

                $oItemRegistro = db_utils::fieldsMemory($rsRegistro, 0);
                $this->nQuantidadeMaxima = $oItemRegistro->pc57_quantmax;
                $this->nQuantidadeMinima = $oItemRegistro->pc57_quantmin;
                $this->iCodigoRegistro = $oItemRegistro->pc57_sequencial;
                $this->lAtivo = $oItemRegistro->pc57_ativo == 't' ? true : false;
                $this->iCodigoItemOrigem = $oItemRegistro->pc57_itemorigem;
                $this->iCodigoItemOrcamento = $oItemRegistro->pc22_orcamitem;
            }
            /**
             * Verifica qual o item do orcamento
             */
        }
    }

    /**
     * @return integer
     */
    public function getCodigoItemOrigem()
    {
        return $this->iCodigoItemOrigem;
    }

    /**
     * @param integer $iCodigoItemOrigem
     */
    public function setCodigoItemOrigem($iCodigoItemOrigem)
    {
        $this->iCodigoItemOrigem = $iCodigoItemOrigem;
    }

    /**
     * @return integer
     */
    public function getCodigoRegistro()
    {
        return $this->iCodigoRegistro;
    }

    /**
     * @return boolean
     */
    public function isAtivo()
    {
        return $this->lAtivo;
    }

    /**
     * @param boolean $lAtivo
     */
    public function setAtivo($lAtivo)
    {
        $this->lAtivo = $lAtivo;
    }

    /**
     * @return float
     */
    public function getQuantidadeMaxima()
    {
        return $this->nQuantidadeMaxima;
    }

    /**
     * @param float $nQuantidadeMaxima
     */
    public function setQuantidadeMaxima($nQuantidadeMaxima)
    {
        $this->nQuantidadeMaxima = $nQuantidadeMaxima;
    }

    /**
     * @return float
     */
    public function getQuantidadeMinima()
    {
        return $this->nQuantidadeMinima;
    }

    /**
     * @param float $nQuantidadeMininima
     */
    public function setQuantidadeMinima($nQuantidadeMininima)
    {
        $this->nQuantidadeMinima = $nQuantidadeMininima;
    }

    /**
     * Informa quais itens da estimativa fazer parte desse item de compilação
     */
    public function setItensEstimativas(array $aItensEstimativas)
    {
        $this->aItemEstimativas = $aItensEstimativas;
    }

    /**
     * Retorna o codigo do item no orcamento
     *
     * @return integer
     */
    function getCodigoItemOrcamento()
    {
        return $this->iCodigoItemOrcamento;
    }

    /**
     * Define a quantidade do item na estimativa
     * @param float $nQuantidade Quantidade do item
     * @return ItemEstimativa
     */
    public function setQuantidade($nQuantidade)
    {
        $nPercentual = ParametroRegistroPreco::getPercentualExecedente();
        $this->nQuantidade = $nQuantidade;
        $this->nQuantidadeExecedente = round((($this->nQuantidade * $nPercentual) / 100));
        return $this;
    }

    public function save($iSolicitacao = '')
    {
        if ($this->getQuantidadeMinima() <= 0) {

            $sMsgErro = "item {$this->getCodigoMaterial()}, possui quantidade minima inválida.\n";
            $sMsgErro .= "Deve ser maior que 0(zero).";
            throw new Exception($sMsgErro);

        }

        if ($this->getCodigoMaterial() == "") {
            throw new Exception("Informe o item!");
        }
        /**
         * Incluimos na tabela solicitem
         */
        $oDaoSolicitem = db_utils::getDao("solicitem");
        $oDaoSolicitem->pc11_just = addslashes(urldecode($this->getJustificativa()));
        $oDaoSolicitem->pc11_liberado = "true";
        $oDaoSolicitem->pc11_pgto = addslashes(urldecode($this->getPagamento()));
        $oDaoSolicitem->pc11_prazo = pg_escape_string(urldecode($this->getPrazos()));
        $oDaoSolicitem->pc11_quant = addslashes(urldecode($this->getQuantidade()));
        $oDaoSolicitem->pc11_vlrun = "{$this->getValorUnitario()}";
        $oDaoSolicitem->pc11_seq = "{$this->getOrdem()}";
        $oDaoSolicitem->pc11_resum = addslashes(urldecode($this->getResumo()));
        if (!empty($iSolicitacao)) {

            $oDaoSolicitem->pc11_numero = "{$iSolicitacao}";
            $this->iSolicitacao = $iSolicitacao;

        } else {
            if (!empty($this->iSolicitacao)) {
                $oDaoSolicitem->pc11_numero = "{$this->iSolicitacao}";
            } else {
                throw new Exception("Número da Solicitação não informado");
            }
        }

        if (!empty($this->iCodigoItemSolicitacao)) {

            $oDaoSolicitem->pc11_codigo = $this->getCodigoItemSolicitacao();
            $oDaoSolicitem->alterar($this->getCodigoItemSolicitacao());

        } else {

            $oDaoSolicitem->incluir(null);
            $this->iCodigoItemSolicitacao = $oDaoSolicitem->pc11_codigo;

        }

        if ($oDaoSolicitem->erro_status == 0) {
            throw new Exception("Erro ao salvar item {$this->getCodigoMaterial()}!\nErro Retornado:{$oDaoSolicitem->erro_msg}");
        }

        /**
         * Excluimos e incluimos novamente na tabela solicitempcmater
         */
        $oDaosolicitemPcMater = db_utils::getDao("solicitempcmater");
        $oDaosolicitemPcMater->excluir($this->getCodigoMaterial(), $this->iCodigoItemSolicitacao);
        $oDaosolicitemPcMater->incluir($this->getCodigoMaterial(), $this->iCodigoItemSolicitacao);
        if ($oDaosolicitemPcMater->erro_status == 0) {
            throw new Exception("Erro ao salvar item {$this->getCodigoMaterial()}!\nErro Retornado:{$oDaosolicitemPcMater->erro_msg}");
        }

        /**
         * Salvamos as informacoes da Unidade do material
         */
        $oDaosolicitemUnid = db_utils::getDao("solicitemunid");
        $oDaosolicitemUnid->excluir($this->iCodigoItemSolicitacao);
        $oDaosolicitemUnid->pc17_codigo = $this->iCodigoItemSolicitacao;
        $oDaosolicitemUnid->pc17_quant = "{$this->getQuantidadeUnidade()}";
        $oDaosolicitemUnid->pc17_unid = "{$this->getUnidade()}";

        $oDaosolicitemUnid->incluir($this->iCodigoItemSolicitacao);
        if ($oDaosolicitemUnid->erro_status == 0) {
            throw new Exception("Erro ao salvar item {$this->getCodigoMaterial()}!\nErro Retornado:{$oDaosolicitemUnid->erro_msg}");
        }
        /**
         * Salvamos a informacao do registro de compras
         */
        $oDaoSolicitemRegistro = db_utils::getDao("solicitemregistropreco");
        $oDaoSolicitemRegistro->pc57_ativo = $this->isAtivo() ? "true" : "false";
        $oDaoSolicitemRegistro->pc57_itemorigem = $this->getCodigoItemOrigem();
        $oDaoSolicitemRegistro->pc57_quantmax = "{$this->getQuantidadeMaxima()}";
        $oDaoSolicitemRegistro->pc57_quantmin = "{$this->getQuantidadeMinima()}";
        $oDaoSolicitemRegistro->pc57_solicitem = $this->getCodigoItemSolicitacao();
        $oDaoSolicitemRegistro->pc57_quantidadeexecedente = $this->getQuantidadeExecedente();
        if ($this->getCodigoRegistro() != "") {

            $oDaoSolicitemRegistro->pc57_sequencial = $this->getCodigoRegistro();
            $oDaoSolicitemRegistro->alterar($this->getCodigoRegistro());

        } else {

            $oDaoSolicitemRegistro->incluir(null);
            $this->iCodigoRegistro = $oDaoSolicitemRegistro->pc57_sequencial;
        }

        $oDaoSolicitemVinculo = new cl_solicitemvinculo();
        $rsExcluir = $oDaoSolicitemVinculo->excluir(null, "pc55_solicitemfilho = $this->iCodigoItemSolicitacao");

        if (empty($rsExcluir)) {
            throw new DBException("Erro ao excluir vínculos do item de solicitação");
        }

        if (empty($this->aItemEstimativas)) {
            $this->carregarItensEstimativa();
        }

        foreach ($this->aItemEstimativas as $iItemEstimativa) {

            $oDaoSolicitemVinculo->pc55_solicitemfilho = $this->iCodigoItemSolicitacao;
            $oDaoSolicitemVinculo->pc55_solicitempai = $iItemEstimativa;
            $oDaoSolicitemVinculo->incluir(null);
            if ($oDaoSolicitemVinculo->erro_status == 0) {

                $sErroMsg = "Erro ao salvar item {$this->getCodigoMaterial()}!\n";
                $sErroMsg .= "Erro Retornado:{$oDaoSolicitemVinculo->erro_msg}";
                throw new Exception($sErroMsg);

            }
        }
        if ($oDaoSolicitemRegistro->erro_status == 0) {

            $sErroMsg = "Erro ao salvar item {$this->getCodigoMaterial()}!\n";
            $sErroMsg .= "Erro Retornado:{$oDaoSolicitemRegistro->erro_msg}";
            throw new Exception($sErroMsg);

        }

    }

    public function remover()
    {
        /**
         * Excluimos a vinculacao com  solicitempcmater
         */
        if ($this->iCodigoItemSolicitacao != null) {

            $oDaoSolicitemRegistro = db_utils::getDao("solicitemregistropreco");
            $oDaoSolicitemRegistro->excluir(null, "pc57_solictem={$this->getCodigoItemSolicitacao()}");
            if ($oDaoSolicitemRegistro->erro_status == 0) {

                $sErroMsg = "Erro ao remover item {$this->getCodigoMaterial()}!\n";
                $sErroMsg .= "Erro Retornado:{$oDaoSolicitemRegistro->erro_msg}";
                throw new Exception($sErroMsg);

            }
        }
        parent::remover();
    }

    /**
     * retorna a quantidade execedente do item
     */
    public function getQuantidadeExecedente()
    {
        return $this->nQuantidadeExecedente;
    }

    /**
     * @return float|int
     * @throws DBException
     */
    public function getSaldo()
    {
        $quantidade = $this->getQuantidade();
        $quantidadeSolicitada = $this->getQuantidadeSolicitada();
        $quantidadeSolicitadaAnulada = $this->getQuantidadeSolicitadaAnulada();

        if ($quantidadeSolicitada === $quantidadeSolicitadaAnulada) {
            return $quantidade - $quantidadeSolicitada;
        }
        return $quantidade
            - $quantidadeSolicitada
            + $quantidadeSolicitadaAnulada;
    }

    /**
     * @return int
     * @throws DBException
     */
    public function getQuantidadeSolicitada()
    {
        $daoQuantidade = new cl_solicitem();
        $where = " pc67_solicita is null and pc55_solicitempai = {$this->iCodigoItemSolicitacao}";
        $sql = $daoQuantidade->sql_query_vinculo_filho(null, "sum(pc11_quant) as quant", null, $where);
        $rsSql = db_query($sql);

        if (!$rsSql) {
            throw new DBException("Erro ao buscar quantidade da solicitação filha.");
        }

        if (pg_num_rows($rsSql) === 0) {
            return 0;
        }

        $oItem = \db_utils::fieldsMemory($rsSql, 0);
        return $oItem->quant;
    }

    /**
     * @throws DBException
     */
    public function carregarItensEstimativa()
    {
        $daoSolicita = new cl_solicita();
        $sql = $daoSolicita->sql_query_estimativa_por_compilacao($this->iSolicitacao, $this->iOrdem,
          'item_esti.pc11_codigo');
        $rsSql = db_query($sql);

        if (empty($rsSql)) {
            throw new DBException("Erro ao buscar items de estimativa.");
        }

        $itens = db_utils::getCollectionByRecord($rsSql);

        foreach ($itens as $item) {
            $this->aItemEstimativas[] = $item->pc11_codigo;
        }
    }

    public function temReservaDeCota()
    {
        $itemReservaCota = new cl_solicitem();
        $campos = "itemreserva.l19_sequencial as itemreserva, itemprincipal.l19_sequencial as itemprincipal";
        $sql = $itemReservaCota->sql_query_reserva_cotas($this->iCodigoItemSolicitacao, $campos);
        $rsReserva = db_query($sql);

        if (!$rsReserva) {
            throw new DBException("Erro ao validar reserva de cotas");
        }

        if (pg_num_rows($rsReserva) == 0) {
            return false;
        }

        $itens = db_utils::getCollectionByRecord($rsReserva);

        foreach ($itens as $item) {
            if (!empty($item->itemprincipal) || !empty($item->itemreserva)) {
                return true;
            }
        }

        return false;
    }

    public function getQuantidadeSolicitadaAnulada()
    {
        $cl_solicitem = new cl_solicitem();
        $condicoes = " pc67_solicita is null and pc55_solicitempai = {$this->iCodigoItemSolicitacao}";
        $sqlBuscaQuantidadesAnuladas = $cl_solicitem->sql_query_quantidade_solicitada_anulada(
            null,
            "floor(sum(e37_vlranu/e55_vlrun)) quantidade_anulada",
            null,
            $condicoes
        );

        $resultadoItem = db_query($sqlBuscaQuantidadesAnuladas);
        if (!$resultadoItem) {
            throw new DBException("Erro ao buscar quantidade anulada da solicitação filha.");
        }

        $item = \db_utils::fieldsMemory($resultadoItem, 0);
        return $item->quantidade_anulada;
    }
}
?>
