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
 * Class cl_percposserural
 */
class cl_percposserural extends DAOBasica {

  public function __construct() {
    parent::__construct('cadastro.percposserural');
  }

  public function sql_outros_propri($matricula="*", $where) {
    $sql = "SELECT
                {$matricula}
            FROM
                percposserural
                INNER JOIN propri ON j166_matric = j42_matric
                AND j166_numcgm = j42_numcgm
                INNER JOIN cgm ON j166_numcgm = z01_numcgm
            WHERE
                {$where}
    ";
    return $sql;
  }
}