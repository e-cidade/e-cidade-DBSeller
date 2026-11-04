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

namespace ECidade\Tributario\Divida\Repository;

use \cl_divida;
use ECidade\Tributario\Divida\Divida as Entity;
use ECidade\Tributario\Divida\Interfaces\TermoRepositoryInterface;
use ECidade\Tributario\Divida\Procedencia as ProcedenciaEntity;
use ECidade\Tributario\Divida\Termo\Termo;
use Exception;

/**
 * Repository responsável por transações na tabela divida.
 *
 * @method static Divida getInstance()
 *
 * @author Leonardo Oliveira <leonardo.malia@dbseller.com.br>
 */
class Divida extends \BaseClassRepository implements TermoRepositoryInterface
{
    protected static $oInstance;

    protected $scopes = array();

    /**
     * Retorna uma dívida filtrando por código.
     *
     * @param integer $code
     *
     * @return Entity
     *
     * @throws Exception
     */
    public function getByCode($code)
    {
        $dao = new cl_divida;
        $sql = $dao->sql_query($code);

        $result = db_query($sql);

        if (!pg_num_rows($result)) {
            return null;
        }

        $entity = null;
        foreach (pg_fetch_all($result) as $item) {
            $entity = $this->make((object) $item);
            break;
        }

        return $entity;
    }

    /**
     * Retorna dividas filtrando por certidao.
     *
     * @param integer $code
     *
     * @return Entity[]
     *
     * @throws Exception
     */
    public function getByCertidao($code)
    {
        $dao = new \cl_certdiv;
        $sql = $dao->sql_query($code);

        $result = \db_query($sql);

        if (!pg_num_rows($result)) {
            throw new Exception('Nenhuma divida encontrada para certidao informada');
        }

        $data = array();
        foreach (pg_fetch_all($result) as $item) {
            $data[] = $this->make((object) $item);
        }

        return $data;
    }


    /**
     * Busca por registros que se enquadrem em todos os scopes
     *
     * @return Entity[]
     */
    public function get()
    {
        $dao = new cl_divida();
        $sql = $dao->sql_query_file(null, '*', null, implode(' AND ', $this->scopes));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar as dívidas.");
        }

        $dividas = array();

        if (pg_num_rows($rs) === 0) {
            return $dividas;
        }

        while ($divida = pg_fetch_array($rs)) {
            $dividas[] = Entity::fromState($divida);
        }

        return $dividas;
    }

    public function findAll($where = "")
    {
        $dao = new cl_divida();

        $sql = $dao->sql_query_file(null, "*", null, $where);

        $result = db_query($sql);

        while ($divida = pg_fetch_array($result)) {
            $dividas[] = Entity::fromState($divida);
        }

        return $dividas;
    }

    /**
     * Adiciona um scope na busca
     *
     * @param  string $id       Identificador desta regra
     * @param  string $campo    Lado esquerdo da operação
     * @param  string $operacao Operador
     * @param  string $valor    Lado direito da operação
     * @return Divida
     */
    private function scope($id, $campo, $operacao, $valor)
    {
        $this->scopes[$id] = "{$campo} {$operacao} {$valor}";
        return $this;
    }

    /**
     * Adiciona o scope comparando com o v01_numpre
     *
     * @param  string $valor
     * @param  string $operacao
     * @return Divida
     */
    public function scopeNumpre($valor, $operacao = '=')
    {
        return $this->scope('numpre', 'v01_numpre', $operacao, $valor);
    }

    /**
     * Persiste uma divida no banco de dados.
     *
     * @param Entity $divida
     *
     * @return Entity
     *
     * @throws Exception
     */
    public function persist(Entity $divida)
    {
        $dao = new cl_divida();

        $dao->v01_numcgm = $divida->getCgm();
        $dao->v01_exerc = $divida->getExercicio();
        $dao->v01_numpre = $divida->getNumpre();
        $dao->v01_numpar = $divida->getNumpar();
        $dao->v01_numtot = $divida->getNumtot();
        $dao->v01_vlrhis = $divida->getValorHistorico();
        $dao->v01_proced = $divida->getProcedencia()->getCodigo();
        $dao->v01_livro = $divida->getLivro();
        $dao->v01_folha = $divida->getFolha();

        $dataInscricao = $divida->getDataIncricao();
        if (!empty($dataInscricao)) {
            $dao->v01_dtinsc = $dataInscricao->format('Y-m-d');
        }

        $dataVencimento = $divida->getDataVencimento();
        if (!empty($dataVencimento)) {
            $dao->v01_dtvenc = $dataVencimento->format('Y-m-d');
        }

        $dataOperacao = $divida->getDataOperacao();
        if (!empty($dataOperacao)) {
            $dao->v01_dtoper = $dataOperacao->format('Y-m-d');
        }

        $dao->v01_valor = $divida->getValor();
        $dao->v01_obs = $divida->getObservacao();
        $dao->v01_numdig = $divida->getNumdig();
        $dao->v01_instit = $divida->getInstituicao();

        $dataInclusao = $divida->getDataInclusao();
        if (!empty($dataInclusao)) {
            $dao->v01_dtinclusao = $dataInclusao->format('Y-m-d');
        }

        $dao->v01_processo = $divida->getProcesso();

        $dataProcesso = $divida->getDataProcesso();
        if (!empty($dataProcesso)) {
            $dao->v01_dtprocesso = $dataProcesso->format('Y-m-d');
        }
        $dao->v01_titular = $divida->getTitular();

        $codigo = $divida->getCodigoDivida();

        if (!empty($codigo)) {
            $dao->v01_coddiv = $codigo;
            $dao->alterar($dao->v01_coddiv);
        } else {
            $dao->incluir(null);
            $divida->setCodigoDivida($dao->v01_coddiv);
        }

        if ($dao->erro_status == 0) {
            throw new Exception($dao->erro_msg);
        }

        return $divida;
    }

    /**
     * Formata a divida para entidade.
     *
     * @param \stdClass $item
     *
     * @return Entity
     */
    public function make($item)
    {
        $procedencia = new ProcedenciaEntity;

        if (property_exists($item, 'v03_codigo')) {
            $procedencia->setCodigo($item->v03_codigo);
        }

        if (property_exists($item, 'v03_descr')) {
            $procedencia->setDescricao($item->v03_descr);
        }

        if (property_exists($item, 'v03_procedtipo')) {
            $procedencia->setTipo($item->v03_procedtipo);
        }

        if (property_exists($item, 'v03_dcomp')) {
            $procedencia->setDescricaoCompleta($item->v03_dcomp);
        }

        $entity = new Entity;
        $entity
            ->setCodigoDivida($item->v01_coddiv)
            ->setCgm($item->v01_numcgm)
            ->setDataIncricao(new \DateTime($item->v01_dtinsc))
            ->setExercicio($item->v01_exerc)
            ->setNumpre($item->v01_numpre)
            ->setNumpar($item->v01_numpar)
            ->setNumtot($item->v01_numtot)
            ->setValorHistorico($item->v01_vlrhis)
            ->setProcedencia($procedencia)
            ->setLivro($item->v01_livro)
            ->setFolha($item->v01_folha)
            ->setDataVencimento(new \DateTime($item->v01_dtvenc))
            ->setDataOperacao(new \DateTime($item->v01_dtoper))
            ->setValor($item->v01_valor)
            ->setObservacao($item->v01_obs)
            ->setNumdig($item->v01_numdig)
            ->setInstituicao($item->v01_instit)
            ->setDataInclusao(new \DateTime($item->v01_dtinclusao))
            ->setProcesso($item->v01_processo)
            ->setDataProcesso(new \DateTime($item->v01_dtprocesso))
            ->setTitular($item->v01_titular);

        return $entity;
    }

    /**
     * @param array $numpres
     * @param Termo $parcelamento
     * @return bool
     * @throws Exception
     */
    public function atualizarObservacaoOrigemPorNumpreAoAnular(array $numpres, Termo $parcelamento)
    {
        $dividas = $this->scopeNumpre("(".implode(',', $numpres).")", 'in')
            ->get();

        $dataAtual = new \DateTime();

        $observacoes =  "Esta dívida fez parte do parcelamento {$parcelamento->getCodigo()},";
        $observacoes .= "anulado em {$dataAtual->format('d/m/Y')}";

        $dataUltimoPagamento = $parcelamento->getDataUltimoPagamento();
        if (!empty($dataUltimoPagamento)) {
            $observacoes .= ", com último pagamento em {$dataUltimoPagamento->format('d/m/Y')}.";
        } else {
            $observacoes .= '. Não houve parcelas pagas.';
        }

        foreach ($dividas as $divida) {
            $observacaoAtual = $divida->getObservacao();
            $divida->setObservacao("{$observacaoAtual}\n{$observacoes}");
            self::persist($divida);
        }

        return true;
    }
}
