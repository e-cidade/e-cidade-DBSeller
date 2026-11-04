<?php
/**
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
 * Class AtributoExameRepository
 */
class AtributoExameRepository
{
    /**
     * Instancia da classe
     * @var AtributoExameRepository
     */
    private static $oInstance;

    /**
     * Collection de AtributoExame
     * @var AtributoExame[]
     */
    private $aItens = array();

    private function __construct()
    {
    }

    /**
     * Retorna uma instancia do AtributoExame pelo Codigo
     * @param int $iCodigo
     * @return AtributoExame
     * @throws BusinessException
     */
    public static function getByCodigo($iCodigo)
    {
        if (!array_key_exists($iCodigo, AtributoExameRepository::getInstance()->aItens)) {
            AtributoExameRepository::getInstance()->aItens[$iCodigo] = new AtributoExame($iCodigo);
        }

        return AtributoExameRepository::getInstance()->aItens[$iCodigo];
    }

    /**
     * @param integer $codigoExame
     * @return array
     * @throws Exception
     */
    public static function getByCodigoExame($codigoExame)
    {
        $atributos = [];

        $dao = new cl_lab_atributo();
        $sql = $dao->sql_query_recursivo('', 'la25_i_codigo', 'la25_c_estrutural', "la42_i_exame = {$codigoExame}");
        $rs = $dao->sql_record($sql);

        if (!$rs) {
            throw new Exception('Erro ao buscar atributos do exame.');
        }

        foreach (\db_utils::getCollectionByRecord($rs) as $atributo) {
            if (!array_key_exists($atributo->la25_i_codigo, AtributoExameRepository::getInstance()->aItens)) {
                AtributoExameRepository::getInstance()->aItens[$atributo->la25_i_codigo] = new AtributoExame(
                    $atributo->la25_i_codigo
                );
            }

            $atributos[] = AtributoExameRepository::getInstance()->aItens[$atributo->la25_i_codigo];
        }

        return $atributos;
    }

    /**
     * Retorna a instancia da classe
     * @return AtributoExameRepository
     */
    protected static function getInstance()
    {
        if (self::$oInstance == null) {
            self::$oInstance = new AtributoExameRepository();
        }

        return self::$oInstance;
    }

    /**
     * Remove a instancia passada como parametro do repository
     * @param AtributoExame $oAtributoExame
     * @return boolean
     */
    public static function remover(AtributoExame $oAtributoExame)
    {
        if (array_key_exists($oAtributoExame->getCodigo(), AtributoExameRepository::getInstance()->aItens)) {
            unset(AtributoExameRepository::getInstance()->aItens[$oAtributoExame->getCodigo()]);
        }

        return true;
    }

    /**
     * Retorna o total de itens existentes no repositorio;
     * @return integer
     */
    public static function getTotalAtributoExame()
    {
        return count(AtributoExameRepository::getInstance()->aItens);
    }

    /**
     * @param string $sigla
     * @param Exame $exame
     * @return AtributoExame|null
     */
    public static function getBySiglaExame($sigla, Exame $exame)
    {
        foreach ($exame->getAtributos() as $atributoExame) {
            if ($atributoExame->getSigla() === $sigla) {
                self::getInstance()->adicionarAtributoExame($atributoExame);

                return $atributoExame;
            }
        }

        return null;
    }

    /**
     * Adiciona uma instancia de AtributoExame ao repositorio
     * @param AtributoExame $oAtributoExame Instancia de AtributoExame
     * @return boolean
     */
    public static function adicionarAtributoExame(AtributoExame $oAtributoExame)
    {
        if (!array_key_exists($oAtributoExame->getCodigo(), AtributoExameRepository::getInstance()->aItens)) {
            AtributoExameRepository::getInstance()->aItens[$oAtributoExame->getCodigo()] = $oAtributoExame;
        }

        return true;
    }

    private function __clone()
    {
    }

    /**
     * Retorna uma instancia do AtributoExame pelo Codigo
     * @param array $atributos
     * @return AtributoExame[]
     * @throws \Exception
     */
    public static function getVariosAtributos($atributos)
    {
        $arrayAtributos = [];
        $atributos = implode(',', $atributos);

        $dao = new cl_lab_atributo();
        $where = "la25_i_codigo in ({$atributos})";
        $order = 'la25_i_codigo';

        $sql = $dao->sql_query(null, '*', $order, $where);
        $resultadoSql = db_query($sql);

        if (!$resultadoSql) {
            throw new Exception('Erro ao buscar atributos!');
        }

        $atributos = db_utils::getCollectionByRecord($resultadoSql);

        foreach ($atributos as $atributo) {
            $arrayAtributos[$atributo->la25_i_codigo] = AtributoExame::fromState((array)$atributo);
        }

        return $arrayAtributos;
    }
}
