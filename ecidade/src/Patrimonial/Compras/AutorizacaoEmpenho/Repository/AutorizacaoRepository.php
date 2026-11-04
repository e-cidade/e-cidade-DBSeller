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

namespace ECidade\Patrimonial\Compras\AutorizacaoEmpenho\Repository;

use ECidade\Patrimonial\Compras\AutorizacaoEmpenho\Model\Autorizacao;
use ECidade\Patrimonial\Compras\ProcessoAdministrativoEmpenho\Model\ProcessoAdministrativo;
use ECidade\Patrimonial\Compras\HistoricoEmpenho\Model\Historico;
use ECidade\Patrimonial\Compras\HistoricoEmpenho\Repository\HistoricoRepository;
use ECidade\Patrimonial\Compras\TipoPrestacaoEmpenho\Model\TipoPrestacao;
use ECidade\Patrimonial\Licitacao\Licitacon\Regra\Emissao\Licitacao;
use Exception;
use PhpOffice\PhpWord\Tests\Element\ObjectTest;
use UsuarioSistema;

/**
 * Class AutorizacaoEmpenhoRepository
 * @package ECidade\Patrimonial\Compras\Repository
 */
class AutorizacaoRepository
{
    /**
     * @var array
     */
    private $scopes = array();

    /**
     * @var Object
     */
    private $dao;

    /**
     * AutorizacaoRepository constructor.
     * @param $dao \cl_empautoriza
     */
    public function __construct($dao)
    {
        $this->dao = $dao;
    }


    /**
     * @param int $codigoAutorizacao
     * @return int
     */
    public function buscaUsuarioPorAutorizacao($codigoAutorizacao)
    {
        $dao = new \cl_empautoriza();
        $sql = $dao->sql_query_file($codigoAutorizacao, 'e54_login, e54_depto');

        return pg_fetch_array(db_query($sql));
    }


    public function buscaDepartamentoPorUsuarioEDepartamento(
        $codigoUsuario,
        $codigoDepartamento
    ) {
        $dao = new \cl_db_depusu;
        $sql = $dao->sql_query_file($codigoUsuario, $codigoDepartamento, 'coddepto as cod02');

        return pg_fetch_row(db_query($sql));
    }


    /**
     * @param $id
     * @param array $columns
     * @return bool|Autorizacao
     * @throws Exception
     */
    public function find($id, $columns = array('*'))
    {
        $sql = $this->dao->sql_query($id, implode(', ', $columns));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar a autorização.\nContate o suporte.");
        }

        if (pg_num_rows($rs) === 0) {
            return false;
        }

        $resultado = pg_fetch_array($rs);

        $autorizacao = Autorizacao::fromState($resultado);

        return $autorizacao
            ->withHistorico()
            ->withPrestacao()
            ->withProcessoAdministrativo()
            ->withDotacao()
            ->withItens();
    }


    /**
     * @param $codigoTipoCompra
     * @param $daoTipoCompra
     * @return array|bool
     * @throws Exception
     */
    public function buscarLicitacoesPorTipoCompra($codigoTipoCompra, $daoTipoCompra)
    {
        $rs = $daoTipoCompra->sql_record(
            $daoTipoCompra->sql_query_file(
                null,
                "l03_tipo,l03_descr",
                '',
                "l03_codcom= $codigoTipoCompra and l03_instit = " . db_getsession('DB_instit')
            )
        );

        if (!$rs) {
            return false;
        }

        return pg_fetch_all($rs);
    }


    /**
     * @param Autorizacao $autorizacao
     * @param $numeroProcesso
     * @param null $codigoAutorizacaoImportada
     * @return Autorizacao
     * @throws Exception
     */
    public function salvar(
        Autorizacao $autorizacao,
        $numeroProcesso,
        $codigoAutorizacaoImportada
    ) {
        $this->dao->e54_autori = $autorizacao->getCodigoAutorizacao();
        $this->dao->e54_numcgm = $autorizacao->getFornecedor()->getCgmFornecedor();
        $this->dao->e54_login = $autorizacao->getLogin();
        $this->dao->e54_codcom = $autorizacao->getCodigoTipoCompra();
        $this->dao->e54_destin = $autorizacao->getDestino();
        $this->dao->e54_valor = $autorizacao->getValor();
        $this->dao->e54_anousu = $autorizacao->getAnousu();
        $this->dao->e54_tipol = $autorizacao->getTipoLicitacao();
        $this->dao->e54_numerl = $autorizacao->getNumeroLicitacao();
        $this->dao->e54_praent = $autorizacao->getPraent();
        $this->dao->e54_entpar = $autorizacao->getEntpar();
        $this->dao->e54_conpag = $autorizacao->getConpag();
        $this->dao->e54_codout = $autorizacao->getCodout();
        $this->dao->e54_contat = $autorizacao->getContat();
        $this->dao->e54_telef = $autorizacao->getTelef();
        $this->dao->e54_numsol = $autorizacao->getNumsol();
        $this->dao->e54_emiss = !is_null($autorizacao->getEmiss())
            ? $autorizacao->getEmiss()->format('Y-m-d')
            : null;
        $this->dao->e54_resumo = $autorizacao->getResumo();
        $this->dao->e54_codtipo = $autorizacao->getCodigoTipoEmpenho();
        $this->dao->e54_instit = $autorizacao->getInstituicao()->getCodigo();
        $this->dao->e54_depto = $autorizacao->getDepartamento()->getCodigo();
        $this->dao->e54_concarpeculiar = $autorizacao->getCodigoCaracteristicaPeculiar();
        $this->dao->e54_institlic = $autorizacao->getInstituicaoLicitacao();
        if (!$autorizacao->getCodigoAutorizacao()) {
            $this->dao->e54_logincriador = db_getsession("DB_id_usuario");
            $this->dao->incluir(null);
        } else {
            $this->dao->alterar($autorizacao->getCodigoAutorizacao());
        }

        if ($this->dao->erro_status === '0') {
            throw new Exception("Não foi possível salvar as informações.\nContate o suporte.");
        }

        $autorizacao->setCodigoAutorizacao($this->dao->e54_autori);

        if ($autorizacao->getHistorico() !== null) {
            $this->associaHistorico(
                $autorizacao,
                new \cl_empauthist(),
                $autorizacao->getHistorico()
            );
        }

        if ($autorizacao->getTipoPrestacao() !== null) {
            $this->associaPrestacao(
                $autorizacao,
                new \cl_empautpresta(),
                $autorizacao->getTipoPrestacao()
            );
        }

        $processoAdministrativo = $this->associaProcessoAdministrativo(
            $autorizacao,
            new \cl_empautorizaprocesso(),
            $numeroProcesso
        );

        $autorizacao->setProcessoAdministrativo($processoAdministrativo);

        if ($autorizacao->getDotacao() !== null) {
            $this->associaDotacao(
                $autorizacao,
                new \cl_empautidot(),
                $autorizacao->getDotacao()
            );
        }

        if ($autorizacao->getItens()) {
            $daoAutorizacaoItem = new \cl_empautitem();
            $sqlBuscaItensVinculados = $daoAutorizacaoItem->sql_query_file($codigoAutorizacaoImportada);
            $result = db_query($sqlBuscaItensVinculados);
            $this->associaItens($autorizacao, pg_fetch_all($result), $daoAutorizacaoItem);
        }
        return $autorizacao;
    }


    /**
     * @param Autorizacao $autorizacao
     * @param \cl_empautidot $daoDotacaoAutorizacao
     * @param \Dotacao $dotacao
     * @throws Exception
     */
    public function associaDotacao(
        Autorizacao $autorizacao,
        \cl_empautidot $daoDotacaoAutorizacao,
        \Dotacao $dotacao
    ) {
        try {
            $daoDotacaoAutorizacao->e56_coddot = $dotacao->getCodigo();
            $daoDotacaoAutorizacao->e56_anousu = $dotacao->getAno();
            $daoDotacaoAutorizacao->incluir($autorizacao->getCodigoAutorizacao());
        } catch (Exception $e) {
            throw new Exception(
                "Não foi possivel vincular a Dotação a Autorização
                {$autorizacao->getCodigoAutorizacao()}"
            );
        }
    }


    public function associaItens(
        Autorizacao $autorizacao,
        array $autorizacaoItens,
        \cl_empautitem $daoAutorizacaoItens
    ) {
        try {
            $itensAutorizacao = $autorizacao->toArray();
            $dadosMateriais = [];

            foreach ($itensAutorizacao['itens'] as $item) {
                $dadosMateriais[$item['pc01_codmater']] = $item;
            }

            foreach ($autorizacaoItens as $autItem) {
                $codigoItem = $autItem['e55_item'];
                $daoAutorizacaoItens->e55_item = $codigoItem;
                $daoAutorizacaoItens->e55_quant = $autItem['e55_quant'];
                $daoAutorizacaoItens->e55_vltot = $autItem['e55_vltot'];
                $daoAutorizacaoItens->e55_codele = $autItem['e55_codele'];
                $daoAutorizacaoItens->e55_vlrun = $autItem['e55_vlrun'];
                $daoAutorizacaoItens->e55_matunid = $autItem['e55_matunid'];
                $daoAutorizacaoItens->e55_descr = $autItem['e55_descr'];

                $material = !empty($dadosMateriais[$codigoItem]) ? $dadosMateriais[$codigoItem] : [];
                if ($autItem['e55_quant'] < 2 && !empty($material['pc01_servico'])) {
                    $daoAutorizacaoItens->e55_servicoquantidade = 'f';
                } else {
                    $daoAutorizacaoItens->e55_servicoquantidade = 't';
                }

                $daoAutorizacaoItens->incluir($autorizacao->getCodigoAutorizacao(), $autItem['e55_sequen']);
            }
        } catch (Exception $e) {
            throw new Exception(
                "Não foi possivel vincular os Itens a Autorização
                {$autorizacao->getCodigoAutorizacao()}"
            );
        }
    }


    /**
     * @param Autorizacao $autorizacao
     * @param \cl_empauthist $daoHistorico
     * @param Historico $historico
     * @throws Exception
     */
    public function associaHistorico(
        Autorizacao $autorizacao,
        \cl_empauthist $daoHistorico,
        Historico $historico
    ) {
        try {
            $daoHistorico->excluir($autorizacao->getCodigoAutorizacao());
            $daoHistorico->e40_codhist = $historico->getCodigo();
            $daoHistorico->incluir($autorizacao->getCodigoAutorizacao());
        } catch (Exception $e) {
            throw new Exception(
                "Não foi possivel vincular o Histórico a Autorização
                {$autorizacao->getCodigoAutorizacao()}"
            );
        }
    }

    /**
     * @param Autorizacao $autorizacao
     * @param \cl_empautpresta $daoAutorizacaoPrestacao
     * @param TipoPrestacao $tipoPrestacao
     * @throws Exception
     */
    public function associaPrestacao(
        Autorizacao $autorizacao,
        \cl_empautpresta $daoAutorizacaoPrestacao,
        TipoPrestacao $tipoPrestacao
    ) {
        try {
            $daoAutorizacaoPrestacao->excluir($autorizacao->getCodigoAutorizacao());
            $daoAutorizacaoPrestacao->e58_tipo = $tipoPrestacao->getCodigoTipoPrestacao();
            $daoAutorizacaoPrestacao->e58_autori = $autorizacao->getCodigoAutorizacao();
            $daoAutorizacaoPrestacao->incluir();
        } catch (Exception $e) {
            throw new Exception(
                "Não foi possivel vincular a Prestação com a Autorização
                {$autorizacao->getCodigoAutorizacao()}"
            );
        }
    }


    /**
     * @param Autorizacao $autorizacao
     * @param \cl_empautorizaprocesso $daoProcessoAdministrativo
     * @param $numeroProcesso
     * @return ProcessoAdministrativo
     * @throws Exception
     */
    public function associaProcessoAdministrativo(
        Autorizacao $autorizacao,
        \cl_empautorizaprocesso $daoProcessoAdministrativo,
        $numeroProcesso
    ) {
        try {
            $daoProcessoAdministrativo->excluir('', "e150_empautoriza = {$autorizacao->getCodigoAutorizacao()}");
            $daoProcessoAdministrativo->e150_empautoriza = $autorizacao->getCodigoAutorizacao();
            $daoProcessoAdministrativo->e150_numeroprocesso = $numeroProcesso;
            $daoProcessoAdministrativo->incluir('');
        } catch (Exception $e) {
            throw new Exception("Erro ao tentar cadastrar um Processo Administrativo vinculado a Autorização
            {$autorizacao->getCodigoAutorizacao()}");
        }

        return ProcessoAdministrativo::fromState(array(
            'e150_empautoriza' => $autorizacao->getCodigoAutorizacao(),
            'e150_numeroprocesso' => $numeroProcesso
        ));
    }

    /**
     * @param $key
     * @return AutorizacaoRepository
     */
    public function removeScope($key)
    {
        if (array_key_exists($key, $this->scopes)) {
            unset($this->scopes[$key]);
        }

        return $this;
    }
}
