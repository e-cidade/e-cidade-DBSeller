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

namespace ECidade\RecursosHumanos\Pessoal\Repository;

use cl_rhdatapagamentofolha;
use ECidade\RecursosHumanos\Pessoal\Model\DataPagamentoFolha;
use Exception;
use Instituicao;
use DBDate;

/**
 * Class DataPagamentoFolhaRepository
 * @package ECidade\RecursosHumanos\Pessoal\Repository
 */
class DataPagamentoFolhaRepository
{
    /**
     * @var array
     */
    private $scopes = array();

    /**
     * @param array|int $ids
     * @return int
     * @throws Exception
     */
    public static function destroy($ids)
    {
        $count = 0;
        $ids = is_array($ids) ? $ids : func_get_args();

        $self = new self();

        foreach ($ids as $id) {
            $self->delete(self::find($id));
            $count++;
        }

        return $count;
    }

    /**
     * @param DataPagamentoFolha|null $dataPagamentoFolha
     * @throws Exception
     */
    public function delete(DataPagamentoFolha $dataPagamentoFolha = null)
    {
        $id = $dataPagamentoFolha instanceof DataPagamentoFolha ? $dataPagamentoFolha->getSequencial() : null;

        $dao = new cl_rhdatapagamentofolha();
        $dao->excluir($id, implode(' AND ', $this->scopes));

        if ($dao->erro_status === '0') {
            throw new Exception("Não foi possível excluir.\nContate o suporte.");
        }
    }

    /**
     * @param $id
     * @param array $columns
     * @return bool|DataPagamentoFolha
     * @throws Exception
     */
    public static function find($id, $columns = array('*'))
    {
        $dao = new cl_rhdatapagamentofolha();
        $sql = $dao->sql_query($id, implode(', ', $columns));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar a data de pagamento da folha.\nContate o suporte.");
        }

        if (pg_num_rows($rs) === 0) {
            return false;
        }

        $resultado = pg_fetch_array($rs);

        return DataPagamentoFolha::fromState($resultado);
    }

    /**
     * @param array $columns
     * @return DataPagamentoFolha[]
     * @throws Exception
     */
    public function all($columns = array('*'))
    {
        $dao = new cl_rhdatapagamentofolha();
        $sql = $dao->sql_query(null, implode(', ', $columns));
        $rs = db_query($sql);

        $datasPagamentoFolha = array();

        if (pg_num_rows($rs) === 0) {
            return $datasPagamentoFolha;
        }

        while ($dataPagamento = pg_fetch_array($rs)) {
            $datasPagamentoFolha[] = DataPagamentoFolha::fromState($dataPagamento);
        }

        return $datasPagamentoFolha;
    }

    /**
     * @return DataPagamentoFolha[]
     * @throws Exception
     */
    public function get()
    {
        $dao = new cl_rhdatapagamentofolha();
        $sql = $dao->sql_query(null, '*', null, implode(' AND ', $this->scopes));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar a(s) data(s) de pagamento(s).\nContate o suporte.");
        }

        $datasPagamentoFolha = array();

        if (pg_num_rows($rs) === 0) {
            return $datasPagamentoFolha;
        }

        while ($dataPagamento = pg_fetch_array($rs)) {
            $datasPagamentoFolha[] = DataPagamentoFolha::fromState($dataPagamento);
        }

        return $datasPagamentoFolha;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function count()
    {
        $dao = new cl_rhdatapagamentofolha();
        $sql = $dao->sql_query(null, 'count(*)', null, implode(' AND ', $this->scopes));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível o total de datas de pagamentos.\nContate o suporte.");
        }

        return (int)pg_fetch_result($rs, 0, 'count');
    }

    /**
     * @param DataPagamentoFolha $dataPagamentoFolha
     * @return DataPagamentoFolha
     * @throws Exception
     */
    public function save(DataPagamentoFolha $dataPagamentoFolha)
    {
        $dao = new cl_rhdatapagamentofolha();
        $dao->rh225_sequencial = $dataPagamentoFolha->getSequencial();
        $dao->rh225_instituicao = $dataPagamentoFolha->getInstituicao()->getCodigo();
        $dao->rh225_ano = $dataPagamentoFolha->getAno();
        $dao->rh225_mes = $dataPagamentoFolha->getMes();
        $dao->rh225_datapagamento = $dataPagamentoFolha->getDataPagamento()->getDate();

        $dao->rh225_sequencial ? $dao->alterar($dataPagamentoFolha->getSequencial()) : $dao->incluir(null);

        if ($dao->erro_status === '0') {
            throw new Exception("Não foi possível salvar a data de pagamento.\nContate o suporte.");
        }

        $dataPagamentoFolha->setSequencial($dao->rh225_sequencial);

        return $dataPagamentoFolha;
    }

    /**
     * @param $sequencial
     * @param string $operator
     * @return $this
     */
    public function scopeSequencial($sequencial, $operator = '=')
    {
        $this->scopes['sequencial'] = "rh225_sequencial {$operator} {$sequencial}";
        return $this;
    }

    /**
     * @param $instituicao
     * @param string $operator
     * @return $this
     */
    public function scopeInstituicao(Instituicao $instituicao, $operator = '=')
    {
        $this->scopes['instituicao'] = "rh225_instituicao {$operator} {$instituicao->getCodigo()}";
        return $this;
    }

    /**
     * @param $ano
     * @param string $operator
     * @return $this
     */
    public function scopeAno($ano, $operator = '=')
    {
        $this->scopes['ano'] = "rh225_ano {$operator} {$ano}";
        return $this;
    }

    /**
     * @param $mes
     * @param string $operator
     * @return $this
     */
    public function scopeMes($mes, $operator = '=')
    {
        $this->scopes['mes'] = "rh225_mes {$operator} {$mes}";
        return $this;
    }

    /**
     * @param $mes
     * @param $mes
     * @return $this
     */
    public function scopeCompetenciaCaixa($ano, $mes)
    {
        $this->scopes['dataPagamento'] = " extract(MONTH from rh225_datapagamento) = {$mes} "
            . "and extract (year from rh225_datapagamento) = {$ano} ";
        return $this;
    }

    /**
     * @param $dataPagamento
     * @param string $operator
     * @return $this
     */
    public function scopeDataPagamento(DBDate $dataPagamento, $operator = '=')
    {
        $this->scopes['dataPagamento'] = "rh225_datapagamento {$operator} '{$dataPagamento->getDate()}'";
        return $this;
    }


    /**
     * @return $this
     */
    public function resetScopes()
    {
        $this->scopes = array();
        return $this;
    }

    /**
     * @param $key
     * @return DataPagamentoFolhaRepository
     */
    public function removeScope($key)
    {
        if (array_key_exists($key, $this->scopes)) {
            unset($this->scopes[$key]);
        }

        return $this;
    }
}
