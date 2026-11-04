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

require_once(modification('db_db_usuarios_classe.php'));

/**
* Módulo configurações
* Classe que estende a db_usuarios
*/
class cl_fis_db_usuarios_estendida extends cl_db_usuarios
{
    public function sqlQueryUsuarios($fields = "*", $where = "", $order = null, $group = null) {
        $sql  = "select {$fields} ";
        $sql .= " from db_usuarios ";
        $sql .= " inner join fiscalizacao.fis_cadfiscais on db_usuarios.id_usuario = fis_cadfiscais.id_usuario ";
        $sql .= " inner join db_depusu on fis_cadfiscais.id_usuario = db_depusu.id_usuario ";
        $sql .= " inner join db_depart on db_depusu.coddepto = db_depart.coddepto ";

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

    public function sqlQueryUsuariosParametrosAndamento($fields = "*", $where = "", $order = null, $group = null){
        $sql  = "select {$fields} ";
        $sql .= " from db_usuarios ";
        $sql .= " inner join fiscalizacao.fis_cadfiscais on db_usuarios.id_usuario = fis_cadfiscais.id_usuario ";
        $sql .= " inner join db_depusu  on fis_cadfiscais.id_usuario = db_depusu.id_usuario ";
        $sql .= " inner join db_depart  on db_depusu.coddepto = db_depart.coddepto ";
        $sql .= " inner join fiscalizacao.fis_usuariosparametrosandamento on db_usuarios.id_usuario = fis_usuariosparametrosandamento.db_usuarios ";
        $sql .= " inner join fiscalizacao.fis_parametrosandamento on fis_parametrosandamento.sequencial = fis_usuariosparametrosandamento.parametrosandamento ";

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

?>
