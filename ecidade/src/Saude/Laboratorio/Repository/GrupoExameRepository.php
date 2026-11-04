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

use ECidade\Saude\Laboratorio\Model\GrupoExame;
use Exception;

/**
 * Class GrupoExameRepository
 * @package ECidade\Saude\Laboratorio\Repository
 */
class GrupoExameRepository
{
    /**
     * @var Object
     */
    private $dao;

    /**
     * GrupoExameRepository constructor.
     * @param $dao \cl_lab_grupoexame
     */
    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param GrupoExame $grupoExame
     * @return GrupoExame
     * @throws Exception
     */
    public function salvar(
        GrupoExame $grupoExame
    ) {
        $this->dao->la68_codigo = $grupoExame->getCodigo();
        $this->dao->la68_labgrupoexame = $grupoExame->getGrupoLaboratorio();
        $this->dao->la68_exame = $grupoExame->getExame();

        if (!$grupoExame->getCodigo()) {
            $this->dao->incluir(null);
        } else {
            $this->dao->alterar($grupoExame->getCodigo());
        }
        if ($this->dao->erro_status === '0') {
            throw new Exception($this->dao->erro_msg);
        }

        return $grupoExame;
    }


    public function buscar($where)
    {
        $sql = $this->dao->sql_query(
            null,
            "la68_codigo as codigo,
            la08_i_codigo as codigoexame,
            la08_c_descr as descricao,
            la02_i_codigo as codigolaboratorio,
            la02_c_descr as descricaolaboratorio,
            la08_i_dias as entrega,
            la08_obriga_peso as obriga_peso,
            la08_obriga_altura as obriga_altura,
            la08_obriga_volumeamostra as obriga_volume_amostra",
            null,
            $where
        );

        $rs = db_query($sql);

        $dados = [];

        while ($linha = pg_fetch_array($rs)) {
            $dados[] = $linha;
        }

        return $dados;
    }

    public function excluir(
        $codigo
    ) {
        $this->dao->excluir($codigo);

        if ($this->dao->erro_status === '0') {
            throw new Exception($this->dao->erro_msg);
        }
    }
}
