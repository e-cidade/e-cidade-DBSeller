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

/**
 * Classe repository para classe Recurso
 */
class RecursoRepository
{

    /**
     * @var Recurso[]
     */
    private $aRecurso = array();

    /**
     * Instancia da classe
     * @var RecursoRepository
     */
    private static $oInstance;

    private $scope = [];

    /**
     * Construtor privado para não ser possível instanciar a classe
     */
    private function __construct()
    {
    }

    public function resetScopes()
    {
        $this->scope = [];
        return $this;
    }

    private function __clone()
    {
    }

    /**
     * Retorno uma instancia do Recurso pelo Codigo
     * @todo não tipar o recurso retornado como use ECidade\Financeiro\Orcamento\Recurso\Recurso;
     *       pois vai quebrar metade do sistema
     *
     * @param integer $iCodigo
     * @return Recurso
     */
    public static function getRecursoPorCodigo($iCodigo)
    {

        if (!array_key_exists($iCodigo, RecursoRepository::getInstance()->aRecurso)) {
            RecursoRepository::getInstance()->aRecurso[$iCodigo] = new Recurso($iCodigo);
        }

        return RecursoRepository::getInstance()->aRecurso[$iCodigo];
    }

    /**
     * Retorna a instancia da classe
     *
     * @return RecursoRepository
     */
    public static function getInstance()
    {

        if (self::$oInstance == null) {
            self::$oInstance = new RecursoRepository();
        }

        return self::$oInstance;
    }

    /**
     * @param string $gestao
     * @param string $codigoRecurso
     * @param integer $complemento
     * @param integer $exercicio
     * @return Recurso
     * @throws Exception
     */
    public static function getRecursoPorCodigoRecursoAndComplemento($gestao, $codigoRecurso, $complemento, $exercicio)
    {
        $where = [
            "o15_complemento = {$complemento}",
            "o15_recurso = '{$codigoRecurso}'",
            "gestao = '$gestao'",
            "exercicio = $exercicio",
        ];
        $dao = new cl_orctiporec();
        $sql = $dao->sqlNovaFonteRecurso(null, 'orctiporec.*', null, implode(' and ', $where));
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar recurso.");
        }

        if (pg_num_rows($rs) === 0) {
            throw new Exception(sprintf(
                "Não foi encontrado o recurso com o Código %s e o Complemento %s.",
                $codigoRecurso,
                $complemento
            ));
        }

        return self::getRecursoPorCodigo(db_utils::fieldsMemory($rs, 0)->o15_codigo);
    }

    public function scopeFonteRecurso($recurso)
    {
        $this->scope[] = "o15_recurso = '{$recurso}'";
        return $this;
    }

    public function scopeComplemento($complemento)
    {
        $this->scope = "o15_complemento = {$complemento}";
        return $this;
    }

    public function get()
    {
        $dao = new cl_orctiporec();
        $sql = $dao->sql_query_file(null, '*', null, implode(' and ', $this->scope));

        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar recurso.");
        }

        if (pg_num_rows($rs) === 0) {
            throw new Exception("Não foi encontrado nenhum recurso com os filtros informados. ");
        }

        $recursos = [];
        while ($state = pg_fetch_array($rs)) {
            $recursos[] = self::getRecursoPorCodigo($state['o15_codigo']);
        }

        return $recursos;
    }
}
