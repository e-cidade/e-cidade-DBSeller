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
namespace ECidade\Tributario\Arrecadacao\Repository;

use ECidade\Tributario\Arrecadacao\TermoTaxaParcela as TermoTaxaParcelaEntity;
use \cl_termotaxaparc;
use \Exception;

/**
 * Class TermoTaxaParcela
 *
 * @method static TermoTaxaParcela getInstance()
 */
final class TermoTaxaParcela extends \BaseClassRepository
{
    protected static $oInstance;

    public function persist(TermoTaxaParcelaEntity $oTermoTaxaParcela)
    {
        $oDao = new cl_termotaxaparc();

        $oDao->ar29_numpar     = $oTermoTaxaParcela->getNumpar();
        $oDao->ar29_taxa       = $oTermoTaxaParcela->getTaxa();
        $oDao->ar29_instit     = $oTermoTaxaParcela->getInstituicao();

        $iCodigo = $oTermoTaxaParcela->getCodigo();

        if (!empty($iCodigo)) {
            $oDao->ar29_sequencial = $iCodigo;
            $lResult = $oDao->alterar($iCodigo);
        } else {
            $lResult = $oDao->incluir(null);
        }

        if (!$lResult) {
            $sMensagem  = 'Ocorreu um erro ao ';
            $sMensagem .= (empty($iCodigo) ? 'incluir' : 'alterar');
            $sMensagem .= ' a configuração de parcela. ' . $oDao->erro_msg;
            throw new DBException($sMensagem);
        }

        return $oDao->ar29_sequencial;
    }

    /**
     * @param $dados
     * @return TermoTaxaParcelaEntity|null
     */
    protected function make($dados)
    {
        if (empty($dados)) {
            return null;
        }

        $oTermoTaxaParcelaEntity = new TermoTaxaParcelaEntity();
        $oTermoTaxaParcelaEntity->setCodigo($dados->ar29_sequencial);
        $oTermoTaxaParcelaEntity->setNumpar($dados->ar29_numpar);
        $oTermoTaxaParcelaEntity->setTaxa($dados->ar29_taxa);
        $oTermoTaxaParcelaEntity->setInstituicao($dados->ar29_instit);

        return $oTermoTaxaParcelaEntity;
    }

    /**
     * @param $rsResult
     * @return TermoTaxaParcelaEntity[]|array
     */
    private function makeCollection($rsResult)
    {
        $aCollection = array();

        if (!isset($rsResult) || !$rsResult) {
            return array();
        }

        $aResult = pg_fetch_all($rsResult);

        if (empty($aResult)) {
            return array();
        }

        foreach ($aResult as $oResult) {
            $aCollection[] = $this->make((object) $oResult);
        }

        return $aCollection;
    }

    /**
     * @return TermoTaxaParcelaEntity[]
     */
    public function getByInstituicao()
    {
        $iInstit = db_getsession("DB_instit");

        $oDao = new cl_termotaxaparc();
        $sSql = $oDao->sql_query(null, "*", null, "ar29_instit = {$iInstit}");
        $rsResult = $oDao->sql_record($sSql);

        return $this->makeCollection($rsResult);
    }
}
