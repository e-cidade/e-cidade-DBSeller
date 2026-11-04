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

class cl_saltesextra
{
    // cria variaveis de erro
    public $rotulo = null;
    public $query_sql = null;
    public $numrows = 0;
    public $numrows_incluir = 0;
    public $numrows_alterar = 0;
    public $numrows_excluir = 0;
    public $erro_status = null;
    public $erro_sql = null;
    public $erro_banco = null;
    public $erro_msg = null;
    public $erro_campo = null;
    public $pagina_retorno = null;
    /* Variáveis do Arquivo */
    public $k109_sequencial = 0;
    public $k109_saltes = 0;
    public $k109_contaextra = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 k109_sequencial = int4 = Código Sequencial
                 k109_saltes = int4 = Código da conta Tesouraria
                 k109_contaextra = int4 = Conta Extra Orçamentaria
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("saltesextra");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert(\"" . $this->erro_msg . "\")</script>";
            if ($retorna == true) {
                echo "<script>location.href='" . $this->pagina_retorno . "'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->k109_sequencial = ($this->k109_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["k109_sequencial"] : $this->k109_sequencial);
            $this->k109_saltes = ($this->k109_saltes == "" ? @$GLOBALS["HTTP_POST_VARS"]["k109_saltes"] : $this->k109_saltes);
            $this->k109_contaextra = ($this->k109_contaextra == "" ? @$GLOBALS["HTTP_POST_VARS"]["k109_contaextra"] : $this->k109_contaextra);
        } else {
            $this->k109_sequencial = ($this->k109_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["k109_sequencial"] : $this->k109_sequencial);
        }
    }

    public function incluir($k109_sequencial)
    {
        $this->atualizacampos();
        if ($this->k109_saltes == null) {
            $this->erro_sql = " Campo Código da conta Tesouraria não informado.";
            $this->erro_campo = "k109_saltes";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->k109_contaextra == null) {
            $this->erro_sql = " Campo Conta Extra Orçamentaria não informado.";
            $this->erro_campo = "k109_contaextra";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($k109_sequencial == "" || $k109_sequencial == null) {
            $result = db_query("select nextval('saltesextra_k109_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: saltesextra_k109_sequencial_seq do campo: k109_sequencial";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->k109_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from saltesextra_k109_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $k109_sequencial)) {
                $this->erro_sql = " Campo k109_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->k109_sequencial = $k109_sequencial;
            }
        }
        if (($this->k109_sequencial == null) || ($this->k109_sequencial == "")) {
            $this->erro_sql = " Campo k109_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into saltesextra(
                                       k109_sequencial
                                      ,k109_saltes
                                      ,k109_contaextra
                       )
                values (
                                $this->k109_sequencial
                               ,$this->k109_saltes
                               ,$this->k109_contaextra
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Conta extra orcamentaria da saltes ($this->k109_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Conta extra orcamentaria da saltes já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Conta extra orcamentaria da saltes ($this->k109_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->k109_sequencial;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->k109_sequencial));
            if (($resaco != false) || ($this->numrows != 0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,12654,'$this->k109_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,2209,12654,'','" . AddSlashes(pg_result($resaco, 0, 'k109_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,2209,12655,'','" . AddSlashes(pg_result($resaco, 0, 'k109_saltes')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,2209,12656,'','" . AddSlashes(pg_result($resaco, 0, 'k109_contaextra')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    public function alterar($k109_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update saltesextra set ";
        $virgula = "";
        if (trim($this->k109_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k109_sequencial"])) {
            $sql .= $virgula . " k109_sequencial = $this->k109_sequencial ";
            $virgula = ",";
            if (trim($this->k109_sequencial) == null) {
                $this->erro_sql = " Campo Código Sequencial não informado.";
                $this->erro_campo = "k109_sequencial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k109_saltes) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k109_saltes"])) {
            $sql .= $virgula . " k109_saltes = $this->k109_saltes ";
            $virgula = ",";
            if (trim($this->k109_saltes) == null) {
                $this->erro_sql = " Campo Código da conta Tesouraria não informado.";
                $this->erro_campo = "k109_saltes";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k109_contaextra) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k109_contaextra"])) {
            $sql .= $virgula . " k109_contaextra = $this->k109_contaextra ";
            $virgula = ",";
            if (trim($this->k109_contaextra) == null) {
                $this->erro_sql = " Campo Conta Extra Orçamentaria não informado.";
                $this->erro_campo = "k109_contaextra";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($k109_sequencial != null) {
            $sql .= " k109_sequencial = $this->k109_sequencial";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->k109_sequencial));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,12654,'$this->k109_sequencial','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k109_sequencial"]) || $this->k109_sequencial != "") {
                        $resac = db_query("insert into db_acount values($acount,2209,12654,'" . AddSlashes(pg_result($resaco, $conresaco, 'k109_sequencial')) . "','$this->k109_sequencial'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k109_saltes"]) || $this->k109_saltes != "") {
                        $resac = db_query("insert into db_acount values($acount,2209,12655,'" . AddSlashes(pg_result($resaco, $conresaco, 'k109_saltes')) . "','$this->k109_saltes'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["k109_contaextra"]) || $this->k109_contaextra != "") {
                        $resac = db_query("insert into db_acount values($acount,2209,12656,'" . AddSlashes(pg_result($resaco, $conresaco, 'k109_contaextra')) . "','$this->k109_contaextra'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Conta extra orcamentaria da saltes não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->k109_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Conta extra orcamentaria da saltes não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->k109_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->k109_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($k109_sequencial = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {
            if (empty($dbwhere)) {
                $resaco = $this->sql_record($this->sql_query_file($k109_sequencial));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,12654,'$k109_sequencial','E')");
                    $resac = db_query("insert into db_acount values($acount,2209,12654,'','" . AddSlashes(pg_result($resaco, $iresaco, 'k109_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,2209,12655,'','" . AddSlashes(pg_result($resaco, $iresaco, 'k109_saltes')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,2209,12656,'','" . AddSlashes(pg_result($resaco, $iresaco, 'k109_contaextra')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from saltesextra
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k109_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " k109_sequencial = $k109_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Conta extra orcamentaria da saltes não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $k109_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Conta extra orcamentaria da saltes não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $k109_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $k109_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function sql_record($sql)
    {
        $result = db_query($sql);
        if (!$result) {
            $this->numrows = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Erro ao selecionar os registros.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:saltesextra";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($k109_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from saltesextra ";
        $sql .= "      inner join saltes  on  saltes.k13_conta = saltesextra.k109_saltes";

        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k109_sequencial)) {
                $sql2 .= " where saltesextra.k109_sequencial = $k109_sequencial ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

    public function sql_query_file($k109_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from saltesextra ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k109_sequencial)) {
                $sql2 .= " where saltesextra.k109_sequencial = $k109_sequencial ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

 public function sql_query_extra_bancaria ( $k109_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = explode("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from saltesextra ";
        $sql .= "      inner join saltes  on  saltes.k13_conta = saltesextra.k109_saltes";
        $sql .= "      inner join conplanocontabancaria on k13_conta = c56_reduz";
        $sql .= "         and c56_anousu = " . db_getsession("DB_anousu");

        $sql2 = "";
        if($dbwhere==""){
            if($k109_sequencial!=null ){
                $sql2 .= " where saltesextra.k109_sequencial = $k109_sequencial ";
            }
        }else if($dbwhere != ""){
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = explode("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    function sql_query_extra ( $k109_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = explode("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from saltesextra ";
        $sql .= "      inner join saltes  on  saltes.k13_conta = saltesextra.k109_contaextra";
        $sql2 = "";
        if($dbwhere==""){
            if($k109_sequencial!=null ){
                $sql2 .= " where saltesextra.k109_sequencial = $k109_sequencial ";
            }
        }else if($dbwhere != ""){
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = explode("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sqlContas($campos, $where = [], $order = null)
    {
        $anousu = db_getsession("DB_anousu");
        $sql = "
        select {$campos}
          from saltes
          join saltesextra on k109_saltes = saltes.k13_conta
          join conplanoreduz on conplanoreduz.c61_reduz = saltesextra.k109_contaextra
               and conplanoreduz.c61_anousu = {$anousu}
          join conplanoexe on conplanoexe.c62_reduz = conplanoreduz.c61_reduz
               and conplanoexe.c62_anousu = conplanoreduz.c61_anousu
          join conplano on conplano.c60_codcon = conplanoreduz.c61_codcon
               and conplano.c60_anousu = conplanoreduz.c61_anousu
          left  join conplanoconta on  conplanoconta.c63_codcon = conplanoreduz.c61_codcon
                     and conplanoconta.c63_anousu = conplanoreduz.c61_anousu
                     and conplanoconta.c63_reduz = conplanoreduz.c61_reduz
          join orctiporec on orctiporec.o15_codigo = conplanoreduz.c61_codigo
          join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
               and fonterecurso.exercicio = conplanoreduz.c61_anousu
        ";

        if (!empty($where)) {
            $sql .= " where " . implode(' and ', $where);
        }

        if (!empty($order)) {
            $sql .= " order by {$order}";
        }

        return $sql;
    }
}
