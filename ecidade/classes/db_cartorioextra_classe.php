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

class cl_cartorioextra extends DAOBasica
{
    public function __construct()
    {
        parent::__construct('juridico.cartorioextra');
    }

    public function sql_all($sCampos = "", $sCondition = "")
    {
        $sWhere = (!empty($sCondition) ? "WHERE {$sCondition}" : "");

        $sSql = "SELECT {$sCampos}
                   FROM cartorioextra
                  INNER JOIN cgm
                     ON z01_numcgm = j167_numcgm
                  INNER JOIN cartorioextratipo
                     ON j168_cartorioextra = j167_sequencial
                  INNER JOIN tiposcartorioextra
                     ON j169_sequencial = j168_tiposcartorioextra
                  {$sWhere}";

        return $sSql;
    }
}
