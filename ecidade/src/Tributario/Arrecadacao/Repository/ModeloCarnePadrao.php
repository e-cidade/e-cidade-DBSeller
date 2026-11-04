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

use \cl_modcarnepadrao;
use \Exception;

final class ModeloCarnePadrao
{
    protected static $oInstance;

    protected function __construct()
    {
        return;
    }

    protected function __clone()
    {
        return;
    }

    public static function getInstance()
    {
        if (empty(static::$oInstance)) {
            static::$oInstance = new static;
        }
    
        return static::$oInstance;
    }

    public function existeRegraModelos($tipoModelos)
    {
        $dao = new cl_modcarnepadrao();

        $where = " k48_cadtipomod in (".implode(",", $tipoModelos).") ";
        $where .= " and k48_datafim >= '".date("Y-m-d", db_getsession("DB_datausu"))."' ";
        
        $sql = $dao->sql_query_file(null, "*", null, $where);

        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao verificar se existem regras para emissão.");
        }

        return (pg_num_rows($rs) > 0);
    }
}
