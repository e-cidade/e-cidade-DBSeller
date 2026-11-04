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

//MODULO: contabilidade
//CLASSE DA ENTIDADE conlancamemp
class cl_conlancamemp
{
    // cria variaveis de erro
    var $rotulo = null;
    var $query_sql = null;
    var $numrows = 0;
    var $numrows_incluir = 0;
    var $numrows_alterar = 0;
    var $numrows_excluir = 0;
    var $erro_status = null;
    var $erro_sql = null;
    var $erro_banco = null;
    var $erro_msg = null;
    var $erro_campo = null;
    var $pagina_retorno = null;
    // cria variaveis do arquivo
    var $c75_codlan = 0;
    var $c75_numemp = 0;
    var $c75_data_dia = null;
    var $c75_data_mes = null;
    var $c75_data_ano = null;
    var $c75_data = null;
    // cria propriedade com as variaveis do arquivo
    var $campos = "
                 c75_codlan = int4 = Código Lançamento
                 c75_numemp = int4 = Número Empenho
                 c75_data = date = Data
                 ";

    //funcao construtor da classe
    public function __construct()
    {
        //classes dos rotulos dos campos
        $this->rotulo = new rotulo("conlancamemp");
        $this->pagina_retorno = basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    }

    //funcao erro
    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert(\"" . $this->erro_msg . "\");</script>";
            if ($retorna == true) {
                echo "<script>location.href='" . $this->pagina_retorno . "'</script>";
            }
        }
    }

    // funcao para atualizar campos
    public function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->c75_codlan = ($this->c75_codlan == "" ? @$GLOBALS["HTTP_POST_VARS"]["c75_codlan"] : $this->c75_codlan);
            $this->c75_numemp = ($this->c75_numemp == "" ? @$GLOBALS["HTTP_POST_VARS"]["c75_numemp"] : $this->c75_numemp);
            if ($this->c75_data == "") {
                $this->c75_data_dia = ($this->c75_data_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["c75_data_dia"] : $this->c75_data_dia);
                $this->c75_data_mes = ($this->c75_data_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["c75_data_mes"] : $this->c75_data_mes);
                $this->c75_data_ano = ($this->c75_data_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["c75_data_ano"] : $this->c75_data_ano);
                if ($this->c75_data_dia != "") {
                    $this->c75_data = $this->c75_data_ano . "-" . $this->c75_data_mes . "-" . $this->c75_data_dia;
                }
            }
        } else {
            $this->c75_codlan = ($this->c75_codlan == "" ? @$GLOBALS["HTTP_POST_VARS"]["c75_codlan"] : $this->c75_codlan);
        }
    }

    // funcao para inclusao
    public function incluir($c75_codlan)
    {
        $this->atualizacampos();
        if ($this->c75_numemp == null) {
            $this->erro_sql = " Campo Número Empenho nao Informado.";
            $this->erro_campo = "c75_numemp";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c75_data == null) {
            $this->erro_sql = " Campo Data nao Informado.";
            $this->erro_campo = "c75_data_dia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->c75_codlan = $c75_codlan;
        if (($this->c75_codlan == null) || ($this->c75_codlan == "")) {
            $this->erro_sql = " Campo c75_codlan nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into conlancamemp(
                                       c75_codlan
                                      ,c75_numemp
                                      ,c75_data
                       )
                values (
                                $this->c75_codlan
                               ,$this->c75_numemp
                               ," . ($this->c75_data == "null" || $this->c75_data == "" ? "null" : "'" . $this->c75_data . "'") . "
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Empenho do Lançamento ($this->c75_codlan) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Empenho do Lançamento já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Empenho do Lançamento ($this->c75_codlan) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : " . $this->c75_codlan;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $resaco = $this->sql_record($this->sql_query_file($this->c75_codlan));
        if (($resaco != false) || ($this->numrows != 0)) {
            $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
            $acount = pg_result($resac, 0, 0);
            $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
            $resac = db_query("insert into db_acountkey values($acount,5207,'$this->c75_codlan','I')");
            $resac = db_query("insert into db_acount values($acount,766,5207,'','" . AddSlashes(pg_result($resaco, 0, 'c75_codlan')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,766,5208,'','" . AddSlashes(pg_result($resaco, 0, 'c75_numemp')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,766,5902,'','" . AddSlashes(pg_result($resaco, 0, 'c75_data')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        }
        return true;
    }

    // funcao para alteracao
    public function alterar($c75_codlan = null)
    {
        $this->atualizacampos();
        $sql = " update conlancamemp set ";
        $virgula = "";
        if (trim($this->c75_codlan) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c75_codlan"])) {
            $sql .= $virgula . " c75_codlan = $this->c75_codlan ";
            $virgula = ",";
            if (trim($this->c75_codlan) == null) {
                $this->erro_sql = " Campo Código Lançamento nao Informado.";
                $this->erro_campo = "c75_codlan";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c75_numemp) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c75_numemp"])) {
            $sql .= $virgula . " c75_numemp = $this->c75_numemp ";
            $virgula = ",";
            if (trim($this->c75_numemp) == null) {
                $this->erro_sql = " Campo Número Empenho nao Informado.";
                $this->erro_campo = "c75_numemp";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c75_data) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c75_data_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["c75_data_dia"] != "")) {
            $sql .= $virgula . " c75_data = '$this->c75_data' ";
            $virgula = ",";
            if (trim($this->c75_data) == null) {
                $this->erro_sql = " Campo Data nao Informado.";
                $this->erro_campo = "c75_data_dia";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["c75_data_dia"])) {
                $sql .= $virgula . " c75_data = null ";
                $virgula = ",";
                if (trim($this->c75_data) == null) {
                    $this->erro_sql = " Campo Data nao Informado.";
                    $this->erro_campo = "c75_data_dia";
                    $this->erro_banco = "";
                    $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                    $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        $sql .= " where ";
        if ($c75_codlan != null) {
            $sql .= " c75_codlan = $this->c75_codlan";
        }
        $resaco = $this->sql_record($this->sql_query_file($this->c75_codlan));
        if ($this->numrows > 0) {
            for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,5207,'$this->c75_codlan','A')");
                if (isset($GLOBALS["HTTP_POST_VARS"]["c75_codlan"]))
                    $resac = db_query("insert into db_acount values($acount,766,5207,'" . AddSlashes(pg_result($resaco, $conresaco, 'c75_codlan')) . "','$this->c75_codlan'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                if (isset($GLOBALS["HTTP_POST_VARS"]["c75_numemp"]))
                    $resac = db_query("insert into db_acount values($acount,766,5208,'" . AddSlashes(pg_result($resaco, $conresaco, 'c75_numemp')) . "','$this->c75_numemp'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                if (isset($GLOBALS["HTTP_POST_VARS"]["c75_data"]))
                    $resac = db_query("insert into db_acount values($acount,766,5902,'" . AddSlashes(pg_result($resaco, $conresaco, 'c75_data')) . "','$this->c75_data'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Empenho do Lançamento nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->c75_codlan;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Empenho do Lançamento nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->c75_codlan;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $this->c75_codlan;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    // funcao para exclusao
    public function excluir($c75_codlan = null, $dbwhere = null)
    {
        if ($dbwhere == null || $dbwhere == "") {
            $resaco = $this->sql_record($this->sql_query_file($c75_codlan));
        } else {
            $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
        }
        if (($resaco != false) || ($this->numrows != 0)) {
            for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,5207,'$c75_codlan','E')");
                $resac = db_query("insert into db_acount values($acount,766,5207,'','" . AddSlashes(pg_result($resaco, $iresaco, 'c75_codlan')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,766,5208,'','" . AddSlashes(pg_result($resaco, $iresaco, 'c75_numemp')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,766,5902,'','" . AddSlashes(pg_result($resaco, $iresaco, 'c75_data')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        $sql = " delete from conlancamemp
                    where ";
        $sql2 = "";
        if ($dbwhere == null || $dbwhere == "") {
            if ($c75_codlan != "") {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                }
                $sql2 .= " c75_codlan = $c75_codlan ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Empenho do Lançamento nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $c75_codlan;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Empenho do Lançamento nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $c75_codlan;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $c75_codlan;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = pg_affected_rows($result);
                return true;
            }
        }
    }

    // funcao do recordset
    public function sql_record($sql)
    {
        $result = db_query($sql);
        if ($result == false) {
            $this->numrows = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Erro ao selecionar os registros.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_numrows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:conlancamemp";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($c75_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from conlancamemp ";
        $sql .= "      inner join conlancam  on  conlancam.c70_codlan = conlancamemp.c75_codlan";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c75_codlan != null) {
                $sql2 .= " where conlancamemp.c75_codlan = $c75_codlan ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_file($c75_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from conlancamemp ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c75_codlan != null) {
                $sql2 .= " where conlancamemp.c75_codlan = $c75_codlan ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    /**
     *
     * Retorna os Pagamento de Empenho para arquivo txt do TCE (Sigfis)
     * @return string
     */
    public function sql_query_pagamentoEmpenho($c75_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from conlancamemp                                                               ";
        $sql .= " inner join conlancam 		 on conlancam.c70_codlan     = conlancamemp.c75_codlan  ";
        $sql .= " inner join conlancamord  on conlancamord.c80_codlan  = conlancam.c70_codlan     ";
        $sql .= " inner join pagordem      on pagordem.e50_codord			 = conlancamord.c80_codord  ";
        $sql .= " inner join empempenho    on empempenho.e60_numemp    = conlancamemp.c75_numemp  ";
        $sql .= " inner join cgm    on cgm.z01_numcgm    = empempenho.e60_numcgm                  ";
        $sql .= " inner join conlancamdoc  on conlancamdoc.c71_codlan  = conlancam.c70_codlan     ";
        $sql .= " inner join orcdotacao    on orcdotacao.o58_anousu    = empempenho.e60_anousu    ";
        $sql .= "                         and orcdotacao.o58_coddot    = empempenho.e60_coddot    ";
        $sql .= " inner join conhistdoc    on conhistdoc.c53_coddoc    = conlancamdoc.c71_coddoc  ";
        $sql .= " inner join conlancampag  on conlancampag.c82_codlan  = conlancam.c70_codlan     ";
        $sql .= " inner join conplanoreduz on conplanoreduz.c61_reduz  = conlancampag.c82_reduz   ";
        $sql .= "                         and conplanoreduz.c61_anousu = conlancampag.c82_anousu  ";
        $sql .= " inner join conplano      on conplano.c60_codcon      = conplanoreduz.c61_codcon ";
        $sql .= "                         and conplano.c60_anousu      = conplanoreduz.c61_anousu ";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($c75_codlan != null) {
                $sql2 .= " where conlancamemp.c75_codlan = $c75_codlan ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_pagamentoEmpenhoSemOP($c75_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from conlancamemp                                                               ";
        $sql .= " inner join conlancam 		 on conlancam.c70_codlan     = conlancamemp.c75_codlan  ";
        $sql .= " inner join empempenho    on empempenho.e60_numemp    = conlancamemp.c75_numemp  ";
        $sql .= " inner join conlancamdoc  on conlancamdoc.c71_codlan  = conlancam.c70_codlan     ";
        $sql .= " inner join orcdotacao    on orcdotacao.o58_anousu    = empempenho.e60_anousu    ";
        $sql .= "                         and orcdotacao.o58_coddot    = empempenho.e60_coddot    ";
        $sql .= " inner join conhistdoc    on conhistdoc.c53_coddoc    = conlancamdoc.c71_coddoc  ";
        $sql .= " inner join conlancampag  on conlancampag.c82_codlan  = conlancam.c70_codlan     ";
        $sql .= " inner join conplanoreduz on conplanoreduz.c61_reduz  = conlancampag.c82_reduz   ";
        $sql .= "                         and conplanoreduz.c61_anousu = conlancampag.c82_anousu  ";
        $sql .= " inner join conplano      on conplano.c60_codcon      = conplanoreduz.c61_codcon ";
        $sql .= "                         and conplano.c60_anousu      = conplanoreduz.c61_anousu ";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($c75_codlan != null) {
                $sql2 .= " where conlancamemp.c75_codlan = $c75_codlan ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_empenho_contrato($c75_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from conlancamemp ";
        $sql .= "      inner join conlancam          on conlancam.c70_codlan           = conlancamemp.c75_codlan";
        $sql .= "      inner join conlancamdoc       on conlancamdoc.c71_codlan        = conlancam.c70_codlan";
        $sql .= "      inner join empempenho         on empempenho.e60_numemp          = conlancamemp.c75_numemp";
        $sql .= "      inner join empempenhocontrato on empempenhocontrato.e100_numemp = empempenho.e60_numemp";
        $sql .= "      inner join acordo             on acordo.ac16_sequencial         = empempenhocontrato.e100_acordo";
        $sql .= "      inner join conhistdoc         on conhistdoc.c53_coddoc          = conlancamdoc.c71_coddoc";
        $sql .= "      left  join conlancamnota      on conlancamnota.c66_codlan       = conlancam.c70_codlan";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($c75_codlan != null) {
                $sql2 .= " where conlancamemp.c75_codlan = $c75_codlan ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_dadoslancamento($c75_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from conlancamemp ";
        $sql .= "      inner join conlancam  on  conlancam.c70_codlan = conlancamemp.c75_codlan";
        $sql .= "      left  join conlancamcompl 	on  conlancam.c70_codlan  = conlancamcompl.c72_codlan ";
        $sql .= "      inner join empempenho on conlancamemp.c75_numemp = empempenho.e60_numemp ";
        $sql .= "      inner join conlancamcgm on conlancam.c70_codlan = conlancamcgm.c76_codlan";
        $sql .= "      inner join conlancamele on conlancam.c70_codlan = conlancamele.c67_codlan";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c75_codlan != null) {
                $sql2 .= " where conlancamemp.c75_codlan = $c75_codlan ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }


    public function sql_query_verificaBensBaixados($c75_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select distinct ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }

        $sql .= " from conlancamemp ";
        $sql .= "      left  join conlancamnota          on conlancamemp.c75_codlan = conlancamnota.c66_codlan ";
        $sql .= "      left join conlancamdoc           on conlancamemp.c75_codlan = conlancamdoc.c71_codlan ";
        $sql .= "      left join conlancam              on conlancam.c70_codlan    = conlancamemp.c75_codlan";
        $sql .= "      left join empempenho             on conlancamemp.c75_numemp = empempenho.e60_numemp ";
        $sql .= "      left join empelemento            on empempenho.e60_numemp   = empelemento.e64_numemp ";
        $sql .= "      left join conplanoorcamentogrupo on empelemento.e64_codele  = conplanoorcamentogrupo.c21_codcon";
        $sql .= "      left join conplanoorcamento      on conplanoorcamentogrupo.c21_codcon = conplanoorcamento.c60_codcon ";
        $sql .= "                                       and conplanoorcamentogrupo.c21_anousu = conplanoorcamento.c60_anousu ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c75_codlan != null) {
                $sql2 .= " where conlancamemp.c75_codlan = $c75_codlan ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }


    public function sql_query_documentos($c75_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from conlancamemp ";
        $sql .= "      inner join conlancam    on  conlancam.c70_codlan    = conlancamemp.c75_codlan ";
        $sql .= "      inner join conlancamdoc on  conlancamdoc.c71_codlan = conlancam.c70_codlan ";
        $sql .= "      inner join conhistdoc   on  conhistdoc.c53_coddoc   = conlancamdoc.c71_coddoc ";
        $sql .= "      inner join conlancamordem on conlancamordem.c03_codlan = conlancam.c70_codlan ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c75_codlan != null) {
                $sql2 .= " where conlancamemp.c75_codlan = $c75_codlan ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_arquivo_lancamento($c75_codlan = null, $sCampos = "", $sOrdem = null, $sWhere = "")
    {

        if (empty($sCampos)) {
            $sCampos = "*";
        }

        $sSqlWhere = "";
        if (!empty($c75_codlan)) {
            $sSqlWhere = " where conlancamemp.c75_codlan = {$c75_codlan} ";
        }

        if (!empty($sWhere)) {
            $sSqlWhere = " where {$sWhere} ";
        }

        $sSqlOrdem = "";
        if (!empty($sOrdem)) {
            $sSqlOrdem = " order by {$sOrdem} ";
        }

        $sSql = " select {$sCampos} ";
        $sSql .= " from conlancamemp ";
        $sSql .= " inner join conlancam                 on conlancam.c70_codlan      = conlancamemp.c75_codlan ";
        $sSql .= " inner join conlancamdoc              on conlancamdoc.c71_codlan   = conlancam.c70_codlan ";
        $sSql .= " inner join conhistdoc                on conhistdoc.c53_coddoc     = conlancamdoc.c71_coddoc ";
        $sSql .= " inner join conlancamordem            on conlancamordem.c03_codlan = conlancam.c70_codlan ";
        $sSql .= " inner join conlancamcorgrupocorrente on conlancam.c70_codlan      = conlancamcorgrupocorrente.c23_conlancam ";
        $sSql .= " inner join corgrupocorrente          on corgrupocorrente.k105_sequencial = conlancamcorgrupocorrente.c23_corgrupocorrente ";
        $sSql .= " inner join corrente                  on (corgrupocorrente.k105_id, corgrupocorrente.k105_data, corgrupocorrente.k105_autent) = (corrente.k12_id, corrente.k12_data, corrente.k12_autent) ";
        $sSql .= " inner join corempagemov              on (corempagemov.k12_id, corempagemov.k12_data, corempagemov.k12_autent)                = (corrente.k12_id, corrente.k12_data, corrente.k12_autent) ";
        $sSql .= " inner join empagemov                 on empagemov.e81_codmov      = corempagemov.k12_codmov ";
        $sSql .= " inner join empageconfgera            on empageconfgera.e90_codmov = empagemov.e81_codmov ";
        $sSql .= " inner join empagegera                on empagegera.e87_codgera    = empageconfgera.e90_codgera";
        $sSql .= " {$sSqlWhere}  {$sSqlOrdem} ";

        return $sSql;
    }

    /**
     * Retorna todos dos dados de lancaento de empenhos.
     * @param $campos
     * @param $where
     * @param $order
     * @param $group
     * @param bool $resto
     * @param null $anoResto
     * @return string
     */
    public function sql_query_dados_empenho($campos, $where, $order, $group, $resto = false, $anoResto = null)
    {

        $sql = "select {$campos} ";
        $sql .= " from contabilidade.conlancam ";
        $sql .= "      inner join contabilidade.conlancamemp on conlancam.c70_codlan = conlancamemp.c75_codlan";
        $sql .= "      inner join contabilidade.conlancamdoc on conlancam.c70_codlan = conlancamdoc.c71_codlan";
        $sql .= "      inner join contabilidade.conhistdoc on conlancamdoc.c71_coddoc = conhistdoc.c53_coddoc";
        $sql .= "      inner join empenho.empempenho on c75_numemp = e60_numemp";
        $sql .= "      inner join protocolo.cgm on z01_numcgm = e60_numcgm";
        $sql .= "      inner join empenho.empempaut  on empempenho.e60_numemp = empempaut.e61_numemp";
        $sql .= "      inner join empenho.empautoriza on empempaut.e61_autori = empautoriza.e54_autori";
        $sql .= "      left join empenho.empautorizaprocesso on empautoriza.e54_autori = empautorizaprocesso.e150_empautoriza";
        $sql .= "      inner join orcamento.orcdotacao";
        $sql .= "                 on empempenho.e60_anousu = orcdotacao.o58_anousu and empempenho.e60_coddot = orcdotacao.o58_coddot";
        $sql .= "      inner join orcamento.orcorgao";
        $sql .= "                 on orcdotacao.o58_anousu = orcorgao.o40_anousu and orcdotacao.o58_orgao = orcorgao.o40_orgao";
        $sql .= "      inner join orcamento.orcunidade on orcdotacao.o58_anousu = orcunidade.o41_anousu";
        $sql .= "                                      and orcdotacao.o58_orgao = orcunidade.o41_orgao";
        $sql .= "                                      and orcdotacao.o58_unidade = orcunidade.o41_unidade";
        $sql .= "      inner join orcamento.orcprograma on orcdotacao.o58_anousu = orcprograma.o54_anousu and";
        $sql .= "                                          orcdotacao.o58_programa = orcprograma.o54_programa";
        $sql .= "      inner join orcamento.orcprojativ on orcdotacao.o58_anousu = orcprojativ.o55_anousu and";
        $sql .= "                                          orcdotacao.o58_projativ = orcprojativ.o55_projativ";
        $sql .= "      inner join orcamento.orcfuncao on orcdotacao.o58_funcao = orcfuncao.o52_funcao";
        $sql .= "      inner join orcamento.orcsubfuncao on orcdotacao.o58_subfuncao = orcsubfuncao.o53_subfuncao";
        $sql .= "      inner join orcamento.orcelemento";
        $sql .= "                 on orcdotacao.o58_codele = orcelemento.o56_codele and orcdotacao.o58_anousu = orcelemento.o56_anousu";
        $sql .= "      inner join orcamento.orctiporec on orcdotacao.o58_codigo = orctiporec.o15_codigo";
        $sql .= "      left join contabilidade.conlancampag on conlancam.c70_codlan = conlancampag.c82_codlan";
        $sql .= "      left join contabilidade.conplanoreduz on conlancampag.c82_reduz = conplanoreduz.c61_reduz";
        $sql .= "                                            and conlancampag.c82_anousu = conplanoreduz.c61_anousu";
        $sql .= "      left join contabilidade.conplanocontabancaria on conplanoreduz.c61_reduz = conplanocontabancaria.c56_reduz";
        $sql .= "                                            and conplanoreduz.c61_anousu  = conplanocontabancaria.c56_anousu";
        $sql .= "      left join configuracoes.contabancaria on conplanocontabancaria.c56_contabancaria = contabancaria.db83_sequencial";
        $sql .= "      left join configuracoes.bancoagencia on contabancaria.db83_bancoagencia = db89_sequencial";
        if ($resto) {
            $sql .= "      inner join empenho.empresto on empresto.e91_numemp = empempenho.e60_numemp ";
            $sql .= "                                 and empresto.e91_anousu = {$anoResto}";
        }



        if (!empty($where)) {
            $sql .= " where {$where}";
        }
        if (!empty($group)) {
            $sql .= " group by {$group}";
        }

        if (!empty($order)) {
            $sql .= " order by  {$order}";
        }

        return $sql;
    }
    /**
     * Retorna todos dos dados de lancaento de empenhos.
     * @param $campos
     * @param $where
     * @param $order
     * @param $group
     * @param bool $resto
     * @param null $anoResto
     * @return string
     */
    public function sql_query_despesa_desdobramento($ano, $dataInicial, $dataFinal, $where)
    {

        if (empty($where)) {
            $where = "true";
        }
        $sql  = "select ele.o56_descr as c60_descr, ";
        $sql .= "       ele.o56_elemento as c60_estrut, ";
        $sql .= "       ele.o56_elemento as o58_elemento, ";
        $sql .= "       ele.o56_descr, ";
        $sql .= "       sum(case ";
        $sql .= "           when c70_data between '{$dataInicial}' and '{$dataFinal}' ";
        $sql .= "             then ( case ";
        $sql .= "                      when c53_tipo = 10 then c70_valor ";
        $sql .= "                      else 0 ";
        $sql .= "                    end ) ";
        $sql .= "             else 0 ";
        $sql .= "          end) as empenhado, ";
        $sql .= "      sum(case ";
        $sql .= "           when c70_data between '{$dataInicial}' and '{$dataFinal}' ";
        $sql .= "             then ( case ";
        $sql .= "                      when c53_tipo = 11 then c70_valor ";
        $sql .= "                      else 0 ";
        $sql .= "                    end ) ";
        $sql .= "            else    0 ";
        $sql .= "          end) as anulado, ";
        $sql .= "      sum(case ";
        $sql .= "           when c70_data between '{$dataInicial}' and '{$dataFinal}' ";
        $sql .= "             then ( case ";
        $sql .= "                      when c53_tipo = 20 then c70_valor ";
        $sql .= "                      when c53_tipo = 21 then ( c70_valor * -1 ) ";
        $sql .= "                      else 0 ";
        $sql .= "                    end ) ";
        $sql .= "            else 0 ";
        $sql .= "          end) as liquidado, ";
        $sql .= "      sum(case ";
        $sql .= "           when c70_data between '{$dataInicial}' and '{$dataFinal}' ";
        $sql .= "              then (case ";
        $sql .= "                     when c53_tipo = 30 then c70_valor ";
        $sql .= "                     when c53_tipo = 31 then ( c70_valor * -1 ) ";
        $sql .= "                     else 0 ";
        $sql .= "                   end) ";
        $sql .= "            else 0 ";
        $sql .= "          end) as pago, ";
        $sql .= "       sum(case when c53_tipo = 10 then c70_valor else 0 end ) as empenhado_acumulado, ";
        $sql .= "       sum(case when c53_tipo = 11 then c70_valor else 0 end ) as anulado_acumulado, ";
        $sql .= "       sum(case when c53_tipo = 20 then c70_valor when c53_tipo = 21 then ( c70_valor * 1 ) else 0  end ) as liquidado_acumulado, ";
        $sql .= "       sum(case when c53_tipo = 30 then c70_valor when c53_tipo = 31 then ( c70_valor * -1 ) else 0 end ) as pago_acumulado, ";
        $sql .= "       0 as suplementado, ";
        $sql .= "       0 as reduzido, ";
        $sql .= "       0 as dot_ini, ";
        $sql .= "       0 as saldo_anterior, ";
        $sql .= "       0 as atual, ";
        $sql .= "       0 as reservado, ";
        $sql .= "       0 as atual_a_pagar, ";
        $sql .= "       0 as atual_menos_reservado, ";
        $sql .= "       0 as atual_a_pagar_liquidado, ";
        $sql .= "       0 as suplementado_acumulado, ";
        $sql .= "       0 as reduzido_acumulado, ";
        $sql .= "       0 as proj, ";
        $sql .= "       0 as ativ, ";
        $sql .= "       0 as oper, ";
        $sql .= "       0 as ordinario, ";
        $sql .= "       0 as vinculado, ";
        $sql .= "       0 as suplemen, ";
        $sql .= "       0 as suplemen_acumulado, ";
        $sql .= "       0 as especial, ";
        $sql .= "       0 as especial_acumulado ";
        $sql .= "  from conlancamele ";
        $sql .= "       inner join conlancam       on c67_codlan = c70_codlan ";
        $sql .= "       inner join conlancamemp    on c75_codlan = c70_codlan ";
        $sql .= "       inner join empempenho      on e60_numemp = c75_numemp ";
        $sql .= "                                 and e60_anousu = {$ano} ";
        $sql .= "       inner join orcdotacao      on o58_coddot = empempenho.e60_coddot ";
        $sql .= "                                 and o58_anousu = e60_anousu ";
        $sql .= "       inner join conlancamdoc    on c71_codlan = c70_codlan ";
        $sql .= "       inner join conhistdoc      on c71_coddoc = c53_coddoc ";
        $sql .= "       inner join orcelemento ele on ele.o56_codele = conlancamele.c67_codele ";
        $sql .= "    and ele.o56_anousu = o58_anousu ";
        $sql .= " where $where ";
        $sql .= "   and conlancam.c70_data between '{$ano}-01-01' and '{$ano}-12-31' ";
        $sql .= "   and conhistdoc.c53_tipo in (10,11,20,21,30,31) ";
        $sql .= " group by c60_estrut, ";
        $sql .= "          c60_descr, ";
        $sql .= "          o56_elemento, ";
        $sql .= "          o56_descr ";
        $sql .= " order by o56_elemento ";

        return $sql;
    }

    public function sql_query_dados($c75_codlan = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= " from conlancamemp ";
        $sql .= " join conlancam on conlancam.c70_codlan = conlancamemp.c75_codlan";
        $sql .= " join conlancaminstit on conlancaminstit.c02_codlan = conlancam.c70_codlan";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($c75_codlan != null) {
                $sql2 .= " where conlancamemp.c75_codlan = $c75_codlan ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function getSsqlBuscaPagamentos($iAnoUso, $dtDataInicial, $dtDataFinal, $instituicaoSessao)
    {


        $sql = <<< SQL
        select
            empempenho.e60_codemp,
            empempenho.e60_anousu,
            conlancam.c70_data,
            orcdotacao.o58_orgao,
            array_to_string(array_accum(distinct c60_estrut || '-' || c60_codcon),
            ',') as c60_estrut,
            array_to_string(array_accum(distinct c60_codcon),
            ',') as c60_codcon,
            array_to_string(array_accum(distinct c61_reduz || '-' || c60_codcon),
            ',') as c61_reduz,
            array_to_string(array_accum(distinct c70_codlan),
            ',') as c70_codlan,
            array_to_string(array_accum(distinct pagordem.e50_codord),
            ',') as codordem,
            to_char(max(conlancam.c70_data),
            'YYYYmm') as competencia,
            orcdotacao.o58_unidade,
            round(sum(case when c53_tipo = 30 and c53_coddoc not in (6002, 6003, 6004, 6005, 6008, 6009, 6010, 6011) then conlancam.c70_valor when c53_tipo = 31 and c53_coddoc not in (6002, 6003, 6004, 6005, 6008, 6009, 6010, 6011) then (conlancam.c70_valor * -1) else 0 end),
            2) as valor_pago
        from
            conlancamemp
        inner join conlancam on
            conlancam.c70_codlan = conlancamemp.c75_codlan
        inner join conlancamord on
            conlancamord.c80_codlan = conlancam.c70_codlan
        inner join pagordem on
            pagordem.e50_codord = conlancamord.c80_codord
        inner join empempenho on
            empempenho.e60_numemp = conlancamemp.c75_numemp
        inner join cgm on
            cgm.z01_numcgm = empempenho.e60_numcgm
        inner join conlancamdoc on
            conlancamdoc.c71_codlan = conlancam.c70_codlan
        inner join orcdotacao on
            orcdotacao.o58_anousu = empempenho.e60_anousu
            and orcdotacao.o58_coddot = empempenho.e60_coddot
        inner join conhistdoc on
            conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
        inner join conlancampag on
            conlancampag.c82_codlan = conlancam.c70_codlan
        inner join conplanoreduz on
            conplanoreduz.c61_reduz = conlancampag.c82_reduz
            and conplanoreduz.c61_anousu = conlancampag.c82_anousu
        inner join conplano on
            conplano.c60_codcon = conplanoreduz.c61_codcon
            and conplano.c60_anousu = conplanoreduz.c61_anousu
        where
            conlancam.c70_anousu = {$iAnoUso}
            and empempenho.e60_instit = {$instituicaoSessao}
            and empempenho.e60_anousu = {$iAnoUso}
            and conhistdoc.c53_tipo in (30, 31)
            and conlancam.c70_data between cast('{$dtDataInicial}' as date) and cast('{$dtDataFinal}' as date)
        group by
            empempenho.e60_codemp,
            empempenho.e60_anousu,
            c70_data,
            orcdotacao.o58_orgao,
            empempenho.e60_numemp,
            orcdotacao.o58_unidade
        order by
            empempenho.e60_codemp,
            conlancam.c70_data

SQL;

        return $sql;
    }
}
