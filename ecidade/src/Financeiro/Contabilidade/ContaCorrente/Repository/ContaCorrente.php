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
namespace ECidade\Financeiro\Contabilidade\ContaCorrente\Repository;

use ECidade\Financeiro\Contabilidade\ContaCorrente\Model\ContaCorrente as ContaCorrenteModel;

/**
 * Class ContaCorrente
 * @package ECidade\Financeiro\Contabilidade\ContaCorrente\Repository
 */
class ContaCorrente extends \BaseClassRepository
{


    /**
     * @var ContaCorrente
     */
    protected static $oInstance;


    /**
     * @param $codigo
     * @return bool|ContaCorrenteModel
     * @throws \DBException
     */
    public static function getByCodigo($codigo)
    {

        if (!empty(self::getInstance()->aColecao[$codigo])) {
            return self::getInstance()->aColecao[$codigo];
        }

        $daoContaCorrente = new \cl_conplanosistema();
        $dados = $daoContaCorrente->findBydId($codigo);
        if (empty($dados)) {
            return false;
        }

        $contaCorrente = self::getInstance()->make($dados);
        return $contaCorrente;
    }

    /**
     *
     * Cria a instacia do model conta corrente
     * @param $dados
     *
     * @return ContaCorrenteModel
     */
    protected function make($dados)
    {
        $contaCorrente = new ContaCorrenteModel();
        $contaCorrente->setCodigo($dados->c122_sequencial);
        $contaCorrente->setNome($dados->c122_descricao);
        $this->aColecao[$dados->c122_sequencial] = $contaCorrente;
        return $contaCorrente;
    }
}
