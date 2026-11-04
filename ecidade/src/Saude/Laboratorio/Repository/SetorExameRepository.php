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

namespace ECidade\Saude\Laboratorio\Repository;

use ECidade\Saude\Laboratorio\Model\SetorExame;
use Exception;

/**
 * Class SetorExameRepository
 * @package ECidade\Saude\Laboratorio\Repository
 */
class SetorExameRepository
{
    /**
     * @var Object
     */
    private $dao;

    /**
     * SetorExameRepository constructor.
     * @param $dao \cl_lab_setorexame
     */
    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    public function buscar($campos, $where)
    {
        $sql = $this->dao->sql_query_setorexame(null, $campos, null, $where);

        $rs = db_query($sql);

        $dados = [];

        while ($linha = pg_fetch_array($rs)) {
            $dados[] = $linha;
        }

        return $dados;
    }

    public function buscarSetorDepartamento($campos, $where)
    {
        $sql = $this->dao->sql_query_setorexame_departamento(null, $campos, null, $where);

        $rs = db_query($sql);

        $dados = [];

        while ($linha = pg_fetch_array($rs)) {
            $dados[] = $linha;
        }

        return $dados;
    }
}
