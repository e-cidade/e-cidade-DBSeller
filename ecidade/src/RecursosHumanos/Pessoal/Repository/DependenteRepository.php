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

use cl_rhdepend;
use Dependente;
use Exception;

/**
 * Class DependenteRepository
 * @package ECidade\RecursosHumanos\Pessoal\Repository
 */
class DependenteRepository
{
    /**
     * @var bool
     */
    protected $useJoin = false;

    /**
     * @var array
     */
    protected $scopes = array();

    /**
     * @var array
     */
    protected $order = array();

    /**
     * @param bool $useJoin
     * @return DependenteRepository
     */
    public function setUseJoin($useJoin)
    {
        $this->useJoin = (bool)$useJoin;
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
     * @return $this
     */
    public function removeScope($key)
    {
        if (array_key_exists($key, $this->scopes)) {
            unset($this->scopes[$key]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getScope($key)
    {
        return array_key_exists($key, $this->scopes) ? $this->scopes[$key] : null;
    }

    /**
     * @param int $matricula
     * @param string $operator
     * @return $this
     */
    public function scopeMatricula($matricula, $operator = '=')
    {
        $this->scopes['rh31_regist'] = "rh31_regist {$operator} {$matricula}";
        return $this;
    }

    /**
     * @param string $irrfComplementar
     * @param string $operator
     * @return $this
     */
    public function scopeIrrfComplemetar($irrfComplementar, $operator = '=')
    {
        $this->scopes['rh31_irfcomplementar'] = "rh31_irfcomplementar {$operator} {$irrfComplementar}";
        return $this;
    }

    /**
     * @param string $irf
     * @param string $operator
     * @return $this
     */
    public function scopeIrf($irrf, $operator = '=')
    {
        $this->scopes['rh31_irf'] = "rh31_irf {$operator} '{$irrf}'";
        return $this;
    }


    /**
     * @param $id
     * @param array $columns
     * @return bool|Dependente
     * @throws Exception
     */
    public static function find($id, $columns = array('*'))
    {
        $dao = new cl_rhdepend();
        $sql = $dao->sql_query($id, implode(', ', $columns));
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Não foi possível buscar o dependente.\nContate o suporte.");
        }

        if (pg_num_rows($rs) === 0) {
            return false;
        }

        $resultado = pg_fetch_array($rs);

        return Dependente::fromState($resultado);
    }

    /**
     * @param array $order Array com os nomes dos campos para ordenação
     * @return $this
     */
    public function orderBy(array $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @param array $columns
     * @return Dependente[]
     * @throws Exception
     */
    public function get($columns = array('*'))
    {
        $dao = new cl_rhdepend();

        if ($this->useJoin) {
            $dao->addJoin(
                'rhdependeplug',
                'dp01_rhdepend',
                '=',
                'rh31_codigo',
                true
            );
        }

        $sql = $dao->sql($columns, $this->scopes, $this->order);
        $resultado = db_query($sql);

        if (!$resultado) {
            throw new Exception("Não foi possível buscar os dependentes.\nContate o suporte.");
        }

        $registros = array();

        if (pg_num_rows($resultado) === 0) {
            return $registros;
        }

        while ($coluna = pg_fetch_array($resultado)) {
            $registros[] = Dependente::fromState($coluna);
        }

        return $registros;
    }

     /**
     * @param int $codigo
     * @return Dependente
     * @throws Exception
     */
    public function getDadosDependente($codigo = null)
    {
        $dependente = new Dependente();
        if (empty($codigo)) {
            throw new Exception("Não foi possível encontrar dados do dependente.\nFavor revisar.");
        }
        $sql = "select
                    rh31_codigo,
                    rh31_regist,
                    rh31_nome,
                    rh31_dtnasc,
                    rh31_gparen,
                    rh31_depend,
                    rh31_irf,
                    rh31_especi,
                    rh31_fins_previdenciarios,
                    rh31_tipoparentesco,
                    rh31_descricaoparentesco,
                    rh31_irfcomplementar,
                    dp01_codigo,
                    dp01_rhdepend,
                    dp01_regist,
                    dp01_processo,
                    dp01_instit,
                    dp01_cpf,
                    dp01_sexo
                from
                    pessoal.rhdepend
                left join pessoal.rhdependeplug on
                    dp01_rhdepend = rh31_codigo
                where
                    rh31_codigo = {$codigo}";

        $resultado = db_query($sql);

        if (!$resultado) {
            throw new Exception("Não foi possível dados encontrar do dependente.\nContate o suporte.");
        }

        if (pg_num_rows($resultado) === 0) {
            return $dependente;
        }

        $coluna = pg_fetch_array($resultado);
        $dependente = Dependente::fromState($coluna);

        return $dependente;
    }
}
