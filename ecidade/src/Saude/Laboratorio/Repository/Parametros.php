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

namespace ECidade\Saude\Laboratorio\Repository;

use cl_lab_parametros;
use db_utils;
use ECidade\Saude\Laboratorio\Model\Parametros as ParametrosModel;
use Exception;
use stdClass;

class Parametros
{
    /**
     * @var Parametros
     */
    private static $instancia;

    /**
     * @var ParametrosModel
     */
    private $parametros;

    /**
     * @return ParametrosModel
     * @throws Exception
     */
    public function buscar()
    {
        if (static::$instancia instanceof Parametros && static::$instancia->parametros instanceof ParametrosModel) {
            return static::$instancia->parametros;
        }

        $dao = new cl_lab_parametros();
        $sql = $dao->sql_query_file();
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception('Erro ao buscar os parâmetros do Laboratório.');
        }

        if (pg_num_rows($rs) === 0) {
            throw new Exception('Parâmetros não configurados. Acesse Laboratório > Procedimentos > Parâmetros.');
        }

        return $this->make(db_utils::fieldsMemory($rs, 0));
    }

    /**
     * @param stdClass $stdClass
     * @return ParametrosModel
     */
    private function make(stdClass $stdClass)
    {
        $parametrosModel = new ParametrosModel();
        $parametrosModel->setCodigo($stdClass->la49_i_codigo);
        $parametrosModel->setEstrutural($stdClass->la49_c_estrutural);
        $parametrosModel->setLiberaExamesDuplos($stdClass->la49_i_exameduplo === 1);
        $parametrosModel->setModeloColetaAmostra($stdClass->la49_modelocoletaamostra);
        $parametrosModel->setIntegracao($stdClass->la49_integracao);
        $parametrosModel->setNumeroControleInterno($stdClass->la49_numerocontroleinterno === 't');
        $parametrosModel->setModeloLaudo($stdClass->la49_modelolaudo);
        $parametrosModel->setExibeLiberacaoPorExame($stdClass->la49_exibeliberacaoporexame);
        $parametrosModel->setEspacoLinhas($stdClass->la49_espacolinhas);
        $parametrosModel->setTamanhoFonte($stdClass->la49_tamanhofonte);
        $parametrosModel->setIntegracaoInfinity($stdClass->la49_integracaoinfinity);

        static::getInstancia()->parametros = $parametrosModel;

        return static::getInstancia()->parametros;
    }

    /**
     * @return Parametros
     */
    public static function getInstancia()
    {
        if (static::$instancia === null) {
            static::$instancia = new Parametros();
        }

        return static::$instancia;
    }
}
