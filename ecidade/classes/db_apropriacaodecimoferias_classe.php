<?php

class cl_apropriacaodecimoferias
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
    public $c145_instituicao = 0;
    public $c145_exercicio = 0;
    public $c145_mes = 0;
    public $c145_processado = 'f';
    public $created_at = null;
    public $updated_at = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 id = int8 = id
                 c145_instituicao = int4 = Instituição
                 c145_exercicio = int4 = Exercício
                 c145_mes = int4 = Mês
                 c145_processado = bool = Processado
                 created_at = varchar(30) = Criado em
                 updated_at = varchar(10) = Alterado em
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("apropriacaodecimoferias");
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
            $this->id = ($this->id == "" ? @$GLOBALS["HTTP_POST_VARS"]["id"] : $this->id);
            $this->c145_instituicao = ($this->c145_instituicao == "" ? @$GLOBALS["HTTP_POST_VARS"]["c145_instituicao"] : $this->c145_instituicao);
            $this->c145_exercicio = ($this->c145_exercicio == "" ? @$GLOBALS["HTTP_POST_VARS"]["c145_exercicio"] : $this->c145_exercicio);
            $this->c145_mes = ($this->c145_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["c145_mes"] : $this->c145_mes);
            $this->c145_processado = ($this->c145_processado == "f" && isset($GLOBALS["HTTP_POST_VARS"]["c145_processado"]) ? $GLOBALS["HTTP_POST_VARS"]["c145_processado"] : $this->c145_processado);
            $this->created_at = ($this->created_at == "" ? @$GLOBALS["HTTP_POST_VARS"]["created_at"] : $this->created_at);
            $this->updated_at = ($this->updated_at == "" ? @$GLOBALS["HTTP_POST_VARS"]["updated_at"] : $this->updated_at);
        } else {
            $this->id = ($this->id == "" ? @$GLOBALS["HTTP_POST_VARS"]["id"] : $this->id);
        }
    }

    public function incluir($id)
    {
        $this->atualizacampos();
        if ($this->c145_instituicao == null) {
            $this->erro_sql = " Campo Instituição não informado.";
            $this->erro_campo = "c145_instituicao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c145_exercicio == null) {
            $this->erro_sql = " Campo Exercício não informado.";
            $this->erro_campo = "c145_exercicio";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c145_mes == null) {
            $this->erro_sql = " Campo Mês não informado.";
            $this->erro_campo = "c145_mes";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (is_null($this->c145_processado)) {
            $this->c145_processado = 't';
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
            $result = db_query("select nextval('apropriacaodecimoferias_id_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: apropiacaodecimoferias_id_seq do campo: id";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->id = pg_fetch_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from apropriacaodecimoferias_id_seq");
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
        $sql = "insert into apropriacaodecimoferias(
                                       id
                                      ,c145_instituicao
                                      ,c145_exercicio
                                      ,c145_mes
                                      ,c145_processado
                                      ,created_at
                                      ,updated_at
                       )
                values (
                                $this->id
                               ,$this->c145_instituicao
                               ,$this->c145_exercicio
                               ,$this->c145_mes
                               ,'$this->c145_processado'
                               ,'$this->created_at'
                               ,'$this->updated_at'
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "apropriacaodecimoferias ($this->id) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "apropriacaodecimoferias já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "apropriacaodecimoferias ($this->id) não Incluído. Inclusão Abortada.";
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
                $resac = db_query("insert into db_acount values($acount,1011080,1011345,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'id')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011080,1015066,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'c145_instituicao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011080,1015067,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'c145_exercicio')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011080,1015068,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'c145_mes')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011080,1015069,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'c145_processado')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011080,1012583,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'created_at')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1011080,1012584,'','" . AddSlashes(pg_fetch_result($resaco, 0, 'updated_at')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    public function alterar($id = null)
    {
        $this->atualizacampos();
        $sql = " update apropriacaodecimoferias set ";
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
        if (trim($this->c145_instituicao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c145_instituicao"])) {
            $sql .= $virgula . " c145_instituicao = $this->c145_instituicao ";
            $virgula = ",";
            if (trim($this->c145_instituicao) == null) {
                $this->erro_sql = " Campo Instituição não informado.";
                $this->erro_campo = "c145_instituicao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c145_exercicio) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c145_exercicio"])) {
            $sql .= $virgula . " c145_exercicio = $this->c145_exercicio ";
            $virgula = ",";
            if (trim($this->c145_exercicio) == null) {
                $this->erro_sql = " Campo Exercício não informado.";
                $this->erro_campo = "c145_exercicio";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c145_mes) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c145_mes"])) {
            $sql .= $virgula . " c145_mes = $this->c145_mes ";
            $virgula = ",";
            if (trim($this->c145_mes) == null) {
                $this->erro_sql = " Campo Mês não informado.";
                $this->erro_campo = "c145_mes";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        $sql .= $virgula . " c145_processado = '$this->c145_processado' ";
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
                        $resac = db_query("insert into db_acount values($acount,1011080,1011345,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'id')) . "','$this->id'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c145_instituicao"]) || $this->c145_instituicao != "")
                        $resac = db_query("insert into db_acount values($acount,1011080,1015066,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'c145_instituicao')) . "','$this->c145_instituicao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c145_exercicio"]) || $this->c145_exercicio != "")
                        $resac = db_query("insert into db_acount values($acount,1011080,1015067,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'c145_exercicio')) . "','$this->c145_exercicio'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c145_mes"]) || $this->c145_mes != "")
                        $resac = db_query("insert into db_acount values($acount,1011080,1015068,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'c145_mes')) . "','$this->c145_mes'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c145_processado"]) || $this->c145_processado != "")
                        $resac = db_query("insert into db_acount values($acount,1011080,1015069,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'c145_processado')) . "','$this->c145_processado'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["created_at"]) || $this->created_at != "")
                        $resac = db_query("insert into db_acount values($acount,1011080,1012583,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'created_at')) . "','$this->created_at'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["updated_at"]) || $this->updated_at != "")
                        $resac = db_query("insert into db_acount values($acount,1011080,1012584,'" . AddSlashes(pg_fetch_result($resaco, $conresaco, 'updated_at')) . "','$this->updated_at'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "apropriacaodecimoferias não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->id;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "apropriacaodecimoferias não foi Alterado. Alteração Executada.\\n";
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
                    $resac = db_query("insert into db_acount values($acount,1011080,1011345,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'id')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011080,1015066,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'c145_instituicao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011080,1015067,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'c145_exercicio')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011080,1015068,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'c145_mes')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011080,1015069,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'c145_processado')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011080,1012583,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'created_at')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1011080,1012584,'','" . AddSlashes(pg_fetch_result($resaco, $iresaco, 'updated_at')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from apropriacaodecimoferias
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
            $this->erro_sql = "apropriacaodecimoferias não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $id;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "apropriacaodecimoferias não Encontrado. Exclusão não Efetuada.\\n";
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
            $this->erro_sql = "Record Vazio na Tabela:apropriacaodecimoferias";
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
        $sql .= "  from apropriacaodecimoferias ";
        $sql .= "      inner join db_config  on  db_config.codigo = apropriacaodecimoferias.c145_instituicao";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= "      left  join db_depart  on  db_depart.coddepto = db_config.db21_departamento";
        $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($id)) {
                $sql2 .= " where apropriacaodecimoferias.id = $id ";
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
        $sql .= "  from apropriacaodecimoferias ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($id)) {
                $sql2 .= " where apropriacaodecimoferias.id = $id ";
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
