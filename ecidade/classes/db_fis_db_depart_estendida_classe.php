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

// MODULO: configuracoes
// Classe que estende a db_depart

require_once(modification('db_db_depart_classe.php'));

class cl_fis_db_depart_estendida extends cl_db_depart {

    public function sqlQueryDeptos($fields = "*", $where = "", $order = null, $group = null) {
        $sql  = "select {$fields} ";
        $sql .= " from db_depart ";
        $sql .= " inner join db_depusu on db_depart.coddepto = db_depusu.coddepto ";
        $sql .= " inner join db_usuarios on db_depusu.id_usuario = db_usuarios.id_usuario ";

        if ($where != '') {
            $sql .= " where {$where}";
        }

        if ($order != null) {
            $sql .= " order by {$order}";
        }

        if ($group != null) {
            $sql .= " group by {$group}";
        }

        return $sql;
    }

    public function sqlQueryDeptosParametrosAndamentos($fields = "*", $where = "", $order = null, $group = null){
        $sql  = "select {$fields} ";
        $sql .= " from db_depart ";
        $sql .= " inner join db_depusu on db_depart.coddepto = db_depusu.coddepto ";
        $sql .= " inner join db_usuarios on db_depusu.id_usuario = db_usuarios.id_usuario ";
        $sql .= " inner join fiscalizacao.fis_deptos_parametrosandamento on db_depart.coddepto = fis_deptos_parametrosandamento.db_depart ";
        $sql .= " inner join fiscalizacao.fis_parametrosandamento on fis_parametrosandamento.sequencial = fis_deptos_parametrosandamento.parametrosandamento ";

        if ($where != '') {
            $sql .= " where {$where}";
        }

        if ($order != null) {
            $sql .= " order by {$order}";
        }

        if ($group != null) {
            $sql .= " group by {$group}";
        }

        return $sql;
    }
}
