<?php

class cl_apropriacaodecimoferias_lancamentos
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
    public $id = 0;
    public $c146_apropriacaodecimoferias_id = 0;
    public $c146_regimeprevidencia = 0;
    public $c146_conlancam = 0;
    public $c146_estornar = 'f';
    public $created_at = null;
    public $updated_at = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 id = int8 = id
                 c146_apropriacaodecimoferias_id = int4 = Apropriacao
                 c146_regimeprevidencia = int4 = Regime de Previdência
                 c146_conlancam = int4 = Lançamento
                 c146_estornar = bool = Estornar
                 created_at = varchar(30) = Criado em
                 updated_at = varchar(10) = Alterado em
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("apropriacaodecimoferias_lancamentos");
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
            $this->id = ($this->id == "" ? @$GLOBALS["_POST"]["id"] : $this->id);
            $this->c146_apropriacaodecimoferias_id = ($this->c146_apropriacaodecimoferias_id == "" ? @$GLOBALS["_POST"]["c146_apropriacaodecimoferias_id"] : $this->c146_apropriacaodecimoferias_id);
            $this->c146_regimeprevidencia = ($this->c146_regimeprevidencia == "" ? @$GLOBALS["_POST"]["c146_regimeprevidencia"] : $this->c146_regimeprevidencia);
            $this->c146_conlancam = ($this->c146_conlancam == "" ? @$GLOBALS["_POST"]["c146_conlancam"] : $this->c146_conlancam);
            $this->c146_estornar = ($this->c146_estornar == "f" && isset($GLOBALS["_POST"]["c146_estornar"])) ? $GLOBALS["_POST"]["c146_estornar"] : $this->c146_estornar;
            $this->created_at = ($this->created_at == "" ? @$GLOBALS["_POST"]["created_at"] : $this->created_at);
            $this->updated_at = ($this->updated_at == "" ? @$GLOBALS["_POST"]["updated_at"] : $this->updated_at);
        } else {
            $this->id = ($this->id == "" ? @$GLOBALS["HTTP_POST_VARS"]["id"] : $this->id);
        }
    }

    public function incluir($id)
    {
        $this->atualizacampos();
        if ($this->c146_apropriacaodecimoferias_id == null) {
            $this->erro_sql = " Campo Apropriacao não informado.";
            $this->erro_campo = "c146_apropriacaodecimoferias_id";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c146_regimeprevidencia == null) {
            $this->erro_sql = " Campo Regime de Previdência não informado.";
            $this->erro_campo = "c146_regimeprevidencia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c146_conlancam == null) {
            $this->erro_sql = " Campo Lançamento não informado.";
            $this->erro_campo = "c146_conlancam";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (is_null($this->c146_estornar)) {
            $this->c146_estornar = 't';
        }
        if ($this->created_at == null) {
            $this->erro_sql = " Campo Criado em não informado.";
            $this->erro_campo = "created_at";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->updated_at == null) {
            $this->erro_sql = " Campo Alterado em não informado.";
            $this->erro_campo = "updated_at";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($id == "" || $id == null) {
            $result = db_query("select nextval('apropriacaodecimoferias_lancamentos_id_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: apropiacaodecimoferias_lancamentos_id_seq do campo: id";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->id = pg_fetch_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from apropriacaodecimoferias_lancamentos_id_seq");
            if (($result != false) && (pg_fetch_result($result, 0, 0) < $id)) {
                $this->erro_sql = " Campo id maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->id = $id;
            }
        }
        if (($this->id == null) || ($this->id == "")) {
            $this->erro_sql = " Campo id não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into apropriacaodecimoferias_lancamentos(
                                       id
                                      ,c146_apropriacaodecimoferias_id
                                      ,c146_regimeprevidencia
                                      ,c146_conlancam
                                      ,c146_estornar
                                      ,created_at
                                      ,updated_at
                       )
                values (
                                $this->id
                               ,$this->c146_apropriacaodecimoferias_id
                               ,$this->c146_regimeprevidencia
                               ,$this->c146_conlancam
                               ,'$this->c146_estornar'
                               ,'$this->created_at'
                               ,'$this->updated_at'
                      )";

        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "apropriacaodecimoferias_lancamentos ($this->id) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "apropriacaodecimoferias_lancamentos já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "apropriacaodecimoferias_lancamentos ($this->id) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->id;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->id));
            if (($resaco != false) || ($this->numrows != 0)) {

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_fetch_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,1011345,'$this->id','I')");
                $resac = db_query("insert into db_acount values($acount,1011081,1011345,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'id')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011081,1015070,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'c146_apropriacaodecimoferias_id')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011081,1015071,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'c146_regimeprevidencia')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011081,1015072,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'c146_conlancam')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011081,1015073,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'c146_estornar')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011081,1012583,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'created_at')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011081,1012584,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'updated_at')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    public function alterar($id = null)
    {
        $this->atualizacampos();
        $sql = " update apropriacaodecimoferias_lancamentos set ";
        $virgula = "";
        if (trim($this->id) != "" || isset($GLOBALS["HTTP_POST_VARS"]["id"])) {
            $sql .= $virgula . " id = $this->id ";
            $virgula = ",";
            if (trim($this->id) == null) {
                $this->erro_sql = " Campo id não informado.";
                $this->erro_campo = "id";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c146_apropriacaodecimoferias_id) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c146_apropriacaodecimoferias_id"])) {
            $sql .= $virgula . " c146_apropriacaodecimoferias_id = $this->c146_apropriacaodecimoferias_id ";
            $virgula = ",";
            if (trim($this->c146_apropriacaodecimoferias_id) == null) {
                $this->erro_sql = " Campo Apropriacao não informado.";
                $this->erro_campo = "c146_apropriacaodecimoferias_id";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        $sql .= $virgula . " c146_regimeprevidencia = '$this->c146_estornar' ";

        if (trim($this->c146_regimeprevidencia) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c146_regimeprevidencia"])) {
            $sql .= $virgula . " c146_regimeprevidencia = $this->c146_regimeprevidencia ";
            $virgula = ",";
            if (trim($this->c146_regimeprevidencia) == null) {
                $this->erro_sql = " Campo Regime de Previdência não informado.";
                $this->erro_campo = "c146_regimeprevidencia";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c146_conlancam) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c146_conlancam"])) {
            $sql .= $virgula . " c146_conlancam = $this->c146_conlancam ";
            $virgula = ",";
            if (trim($this->c146_conlancam) == null) {
                $this->erro_sql = " Campo Lançamento não informado.";
                $this->erro_campo = "c146_conlancam";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if (trim($this->created_at) != "" || isset($GLOBALS["HTTP_POST_VARS"]["created_at"])) {
            $sql .= $virgula . " created_at = '$this->created_at' ";
            $virgula = ",";
            if (trim($this->created_at) == null) {
                $this->erro_sql = " Campo Criado em não informado.";
                $this->erro_campo = "created_at";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->updated_at) != "" || isset($GLOBALS["HTTP_POST_VARS"]["updated_at"])) {
            $sql .= $virgula . " updated_at = '$this->updated_at' ";
            $virgula = ",";
            if (trim($this->updated_at) == null) {
                $this->erro_sql = " Campo Alterado em não informado.";
                $this->erro_campo = "updated_at";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($id != null) {
            $sql .= " id = $this->id";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->id));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1011345,'$this->id','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["id"]) || $this->id != "")
                        $resac = db_query("insert into db_acount values($acount,1011081,1011345,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'id')) . "','$this->id'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c146_apropriacaodecimoferias_id"]) || $this->c146_apropriacaodecimoferias_id != "")
                        $resac = db_query("insert into db_acount values($acount,1011081,1015070,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'c146_apropriacaodecimoferias_id')) . "','$this->c146_apropriacaodecimoferias_id'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c146_regimeprevidencia"]) || $this->c146_regimeprevidencia != "")
                        $resac = db_query("insert into db_acount values($acount,1011081,1015071,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'c146_regimeprevidencia')) . "','$this->c146_regimeprevidencia'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c146_conlancam"]) || $this->c146_conlancam != "")
                        $resac = db_query("insert into db_acount values($acount,1011081,1015072,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'c146_conlancam')) . "','$this->c146_conlancam'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c146_estornar"]) || $this->c146_estornar != "")
                        $resac = db_query("insert into db_acount values($acount,1011081,1015073,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'c146_estornar')) . "','$this->c146_estornar'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["created_at"]) || $this->created_at != "")
                        $resac = db_query("insert into db_acount values($acount,1011081,1012583,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'created_at')) . "','$this->created_at'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["updated_at"]) || $this->updated_at != "")
                        $resac = db_query("insert into db_acount values($acount,1011081,1012584,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'updated_at')) . "','$this->updated_at'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "apropriacaodecimoferias_lancamentos não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->id;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "apropriacaodecimoferias_lancamentos não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->id;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->id;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($id = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($id));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1011345,'$id','E')");
                    $resac = db_query("insert into db_acount values($acount,1011081,1011345,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'id')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011081,1015070,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'c146_apropriacaodecimoferias_id')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011081,1015071,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'c146_regimeprevidencia')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011081,1015072,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'c146_conlancam')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011081,1015073,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'c146_estornar')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011081,1012583,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'created_at')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011081,1012584,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'updated_at')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from apropriacaodecimoferias_lancamentos
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($id)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " id = $id ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "apropriacaodecimoferias_lancamentos não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $id;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "apropriacaodecimoferias_lancamentos não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $id;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $id;
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
            $this->erro_sql = "Record Vazio na Tabela:apropriacaodecimoferias_lancamentos";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($id = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $sql .= "  from apropriacaodecimoferias_lancamentos ";
        $sql .= "      inner join conlancam  on  conlancam.c70_codlan = apropriacaodecimoferias_lancamentos.c146_conlancam";
        $sql .= "      inner join regimeprevidencia  on  regimeprevidencia.rh127_sequencial = apropriacaodecimoferias_lancamentos.c146_regimeprevidencia";
        $sql .= "      inner join apropriacaodecimoferias  on  apropriacaodecimoferias.id = apropriacaodecimoferias_lancamentos.c146_apropriacaodecimoferias_id";
        $sql .= "      inner join db_config  on  db_config.codigo = apropriacaodecimoferias.c145_instituicao";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($id)) {
                $sql2 .= " where apropriacaodecimoferias_lancamentos.id = $id ";
            }
        } else if (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

    public function sql_query_file($id = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from apropriacaodecimoferias_lancamentos ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($id)) {
                $sql2 .= " where apropriacaodecimoferias_lancamentos.id = $id ";
            }
        } else if (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

}
