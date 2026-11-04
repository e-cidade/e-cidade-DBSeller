<?php
//MODULO: empenho
//CLASSE DA ENTIDADE empagemov
class cl_empagemov
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
    // cria variaveis do arquivo
    public $e81_codmov = 0;
    public $e81_codage = 0;
    public $e81_numemp = 0;
    public $e81_valor = 0;
    public $e81_cancelado_dia = null;
    public $e81_cancelado_mes = null;
    public $e81_cancelado_ano = null;
    public $e81_cancelado = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 e81_codmov = int4 = Movimento
                 e81_codage = int4 = Agenda
                 e81_numemp = int4 = Número
                 e81_valor = float8 = Valor
                 e81_cancelado = date = Data cancelado
                 ";

    //funcao construtor da classe
    public function __construct()
    {
        //classes dos rotulos dos campos
        $this->rotulo = new rotulo("empagemov");
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
            $this->e81_codmov = ($this->e81_codmov == "" ? @$GLOBALS["HTTP_POST_VARS"]["e81_codmov"] : $this->e81_codmov);
            $this->e81_codage = ($this->e81_codage == "" ? @$GLOBALS["HTTP_POST_VARS"]["e81_codage"] : $this->e81_codage);
            $this->e81_numemp = ($this->e81_numemp == "" ? @$GLOBALS["HTTP_POST_VARS"]["e81_numemp"] : $this->e81_numemp);
            $this->e81_valor = ($this->e81_valor === "" ? @$GLOBALS["HTTP_POST_VARS"]["e81_valor"] : $this->e81_valor);
            if ($this->e81_cancelado == "") {
                $this->e81_cancelado_dia = ($this->e81_cancelado_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["e81_cancelado_dia"] : $this->e81_cancelado_dia);
                $this->e81_cancelado_mes = ($this->e81_cancelado_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["e81_cancelado_mes"] : $this->e81_cancelado_mes);
                $this->e81_cancelado_ano = ($this->e81_cancelado_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["e81_cancelado_ano"] : $this->e81_cancelado_ano);
                if ($this->e81_cancelado_dia != "") {
                    $this->e81_cancelado = $this->e81_cancelado_ano . "-" . $this->e81_cancelado_mes . "-" . $this->e81_cancelado_dia;
                }
            }
        } else {
            $this->e81_codmov = ($this->e81_codmov == "" ? @$GLOBALS["HTTP_POST_VARS"]["e81_codmov"] : $this->e81_codmov);
        }
    }

    // funcao para inclusao
    public function incluir($e81_codmov)
    {
        $this->atualizacampos();
        if ($this->e81_codage == null) {
            $this->erro_sql = " Campo Agenda nao Informado.";
            $this->erro_campo = "e81_codage";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e81_numemp == null) {
            $this->erro_sql = " Campo Número nao Informado.";
            $this->erro_campo = "e81_numemp";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e81_valor === null) {
            $this->erro_sql = " Campo Valor nao Informado.";
            $this->erro_campo = "e81_valor";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e81_cancelado == null) {
            $this->e81_cancelado = "null";
        }
        if ($e81_codmov == "" || $e81_codmov == null) {
            $result = db_query("select nextval('empagemov_e81_codmov_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: empagemov_e81_codmov_seq do campo: e81_codmov";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->e81_codmov = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from empagemov_e81_codmov_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $e81_codmov)) {
                $this->erro_sql = " Campo e81_codmov maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->e81_codmov = $e81_codmov;
            }
        }
        if (($this->e81_codmov == null) || ($this->e81_codmov == "")) {
            $this->erro_sql = " Campo e81_codmov nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into empagemov(
                                       e81_codmov
                                      ,e81_codage
                                      ,e81_numemp
                                      ,e81_valor
                                      ,e81_cancelado
                       )
                values (
                                $this->e81_codmov
                               ,$this->e81_codage
                               ,$this->e81_numemp
                               ,$this->e81_valor
                               ," . ($this->e81_cancelado == "null" || $this->e81_cancelado == "" ? "null" : "'" . $this->e81_cancelado . "'") . "
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Movimentos agenda ($this->e81_codmov) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Movimentos agenda j? Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Movimentos agenda ($this->e81_codmov) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : " . $this->e81_codmov;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $resaco = $this->sql_record($this->sql_query_file($this->e81_codmov));
        if (($resaco != false) || ($this->numrows != 0)) {
            $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
            $acount = pg_result($resac, 0, 0);
            $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
            $resac = db_query("insert into db_acountkey values($acount,6172,'$this->e81_codmov','I')");
            $resac = db_query("insert into db_acount values($acount,995,6172,'','" . AddSlashes(pg_result($resaco, 0, 'e81_codmov')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,995,6173,'','" . AddSlashes(pg_result($resaco, 0, 'e81_codage')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,995,6174,'','" . AddSlashes(pg_result($resaco, 0, 'e81_numemp')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,995,6175,'','" . AddSlashes(pg_result($resaco, 0, 'e81_valor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,995,6176,'','" . AddSlashes(pg_result($resaco, 0, 'e81_cancelado')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        }
        return true;
    }

    // funcao para alteracao
    public function alterar($e81_codmov = null)
    {
        $this->atualizacampos();
        $sql = " update empagemov set ";
        $virgula = "";
        if (trim($this->e81_codmov) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e81_codmov"])) {
            $sql .= $virgula . " e81_codmov = $this->e81_codmov ";
            $virgula = ",";
            if (trim($this->e81_codmov) == null) {
                $this->erro_sql = " Campo Movimento nao Informado.";
                $this->erro_campo = "e81_codmov";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e81_codage) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e81_codage"])) {
            $sql .= $virgula . " e81_codage = $this->e81_codage ";
            $virgula = ",";
            if (trim($this->e81_codage) == null) {
                $this->erro_sql = " Campo Agenda nao Informado.";
                $this->erro_campo = "e81_codage";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e81_numemp) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e81_numemp"])) {
            $sql .= $virgula . " e81_numemp = $this->e81_numemp ";
            $virgula = ",";
            if (trim($this->e81_numemp) == null) {
                $this->erro_sql = " Campo Número nao Informado.";
                $this->erro_campo = "e81_numemp";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e81_valor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e81_valor"])) {
            $sql .= $virgula . " e81_valor = $this->e81_valor ";
            $virgula = ",";
            if (trim($this->e81_valor) == null) {
                $this->erro_sql = " Campo Valor nao Informado.";
                $this->erro_campo = "e81_valor";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e81_cancelado) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e81_cancelado_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["e81_cancelado_dia"] != "")) {
            $sql .= $virgula . " e81_cancelado = '$this->e81_cancelado' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["e81_cancelado_dia"])) {
                $sql .= $virgula . " e81_cancelado = null ";
                $virgula = ",";
            }
        }
        $sql .= " where ";
        if ($e81_codmov != null) {
            $sql .= " e81_codmov = $this->e81_codmov";
        }
        $resaco = $this->sql_record($this->sql_query_file($this->e81_codmov));
        if ($this->numrows > 0) {
            for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,6172,'$this->e81_codmov','A')");
                if (isset($GLOBALS["HTTP_POST_VARS"]["e81_codmov"]))
                    $resac = db_query("insert into db_acount values($acount,995,6172,'" . AddSlashes(pg_result($resaco, $conresaco, 'e81_codmov')) . "','$this->e81_codmov'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                if (isset($GLOBALS["HTTP_POST_VARS"]["e81_codage"]))
                    $resac = db_query("insert into db_acount values($acount,995,6173,'" . AddSlashes(pg_result($resaco, $conresaco, 'e81_codage')) . "','$this->e81_codage'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                if (isset($GLOBALS["HTTP_POST_VARS"]["e81_numemp"]))
                    $resac = db_query("insert into db_acount values($acount,995,6174,'" . AddSlashes(pg_result($resaco, $conresaco, 'e81_numemp')) . "','$this->e81_numemp'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                if (isset($GLOBALS["HTTP_POST_VARS"]["e81_valor"]))
                    $resac = db_query("insert into db_acount values($acount,995,6175,'" . AddSlashes(pg_result($resaco, $conresaco, 'e81_valor')) . "','$this->e81_valor'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                if (isset($GLOBALS["HTTP_POST_VARS"]["e81_cancelado"]))
                    $resac = db_query("insert into db_acount values($acount,995,6176,'" . AddSlashes(pg_result($resaco, $conresaco, 'e81_cancelado')) . "','$this->e81_cancelado'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Movimentos agenda nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->e81_codmov;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Movimentos agenda nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->e81_codmov;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Altera??o efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $this->e81_codmov;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    // funcao para exclusao
    public function excluir($e81_codmov = null, $dbwhere = null)
    {
        if ($dbwhere == null || $dbwhere == "") {
            $resaco = $this->sql_record($this->sql_query_file($e81_codmov));
        } else {
            $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
        }
        if (($resaco != false) || ($this->numrows != 0)) {
            for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,6172,'$e81_codmov','E')");
                $resac = db_query("insert into db_acount values($acount,995,6172,'','" . AddSlashes(pg_result($resaco, $iresaco, 'e81_codmov')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,995,6173,'','" . AddSlashes(pg_result($resaco, $iresaco, 'e81_codage')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,995,6174,'','" . AddSlashes(pg_result($resaco, $iresaco, 'e81_numemp')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,995,6175,'','" . AddSlashes(pg_result($resaco, $iresaco, 'e81_valor')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,995,6176,'','" . AddSlashes(pg_result($resaco, $iresaco, 'e81_cancelado')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        $sql = " delete from empagemov
                    where ";
        $sql2 = "";
        if ($dbwhere == null || $dbwhere == "") {
            if ($e81_codmov != "") {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                }
                $sql2 .= " e81_codmov = $e81_codmov ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Movimentos agenda nao Exclu?do. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $e81_codmov;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Movimentos agenda nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $e81_codmov;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $e81_codmov;
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
            $this->erro_sql = "Record Vazio na Tabela:empagemov";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
        }
        return $result;
    }

    public function alterardata($e81_codmov = null, $e81_cancelado = null)
    {
        if ($this->e81_codmov == null) {
            $this->erro_sql = " Campo Movimento nao Informado.";
            $this->erro_campo = "e81_codmov";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        } else {
            $sql = " update empagemov set e81_cancelado=";
            if ($this->e81_cancelado == null) {
                $sql .= " null ";
            } else {
                $sql .= " '" . $this->e81_cancelado . "' ";
            }
            $sql .= " where e81_codmov=" . $this->e81_codmov;
            $result = @db_query($sql);

            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Movimentos agenda nao Alterado. Alteracao Abortada.\\n";
                $this->erro_sql .= "Valores : " . $this->e81_codmov;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                $this->numrows_alterar = 0;
                return false;
            } else {
                if (pg_affected_rows($result) == 0) {
                    $this->erro_banco = "";
                    $this->erro_sql = "Movimentos agenda nao foi Alterado. Alteracao Executada.\\n";
                    $this->erro_sql .= "Valores : " . $this->e81_codmov;
                    $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                    $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                    $this->erro_status = "1";
                    $this->numrows_alterar = 0;
                    return true;
                } else {
                    $this->erro_banco = "";
                    $this->erro_sql = "Altera??o efetuada com Sucesso\\n";
                    $this->erro_sql .= "Valores : " . $this->e81_codmov;
                    $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                    $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                    $this->erro_status = "1";
                    $this->numrows_alterar = pg_affected_rows($result);
                    return true;
                }
            }
        }
    }

    public function sql_query_empenho_conferido($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";

        $sql .= " from empagemov ";
        $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
        $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
        $sql .= "      inner join db_config  on  db_config.codigo = empempenho.e60_instit";
        $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
        $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
        $sql .= "      inner join emptipo  on  emptipo.e41_codtipo = empempenho.e60_codtipo";
        $sql .= "      inner join emppresta on e81_codmov = e45_codmov";

        $sql2 = "";

        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }

        $sql2 .= ($sql2 != "" ? "" : " where db_config.codigo = " . db_getsession("DB_instit"));
        $sql .= $sql2;

        if ($ordem != null) {
            $sql .= " order by {$ordem}";
        }

        return $sql;
    }
    public function sql_query_empenho_conferidoPagamento($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";

        $sql .= " from empagemov ";
        $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
        $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
        $sql .= "      inner join db_config  on  db_config.codigo = empempenho.e60_instit";
        $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
        $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
        $sql .= "      inner join emptipo  on  emptipo.e41_codtipo = empempenho.e60_codtipo";
        $sql .= "      inner join emppresta on e81_codmov = e45_codmov";
        $sql .= "      inner join empord on e82_codmov = e45_codmov ";
        $sql .= "      inner join pagordemele  on  e53_codord = e82_codord ";
        $sql .= "      left  join empprestaitem on e46_emppresta = e45_sequencial";

        $sql2 = "";

        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }

        $sql2 .= ($sql2 != "" ? "" : " where db_config.codigo = " . db_getsession("DB_instit"));
        $sql .= $sql2;

        if ($ordem != null) {
            $sql .= " order by {$ordem}";
        }

        return $sql;
    }

    public function sql_query($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
        $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
        $sql .= "      inner join db_config  on  db_config.codigo = empempenho.e60_instit";
        $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
        $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
        $sql .= "      inner join emptipo  on  emptipo.e41_codtipo = empempenho.e60_codtipo";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql2 .= ($sql2 != "" ? "" : " where db_config.codigo = " . db_getsession("DB_instit"));
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

    public function sql_query_chequeforma($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
        $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
        $sql .= "      inner join empord  on  empord.e82_codmov = empagemov.e81_codmov";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
        $sql .= "      inner join empagepag  on  empagepag.e85_codmov = empagemov.e81_codmov";
        $sql .= "      inner join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
        $sql .= "      inner join empageconf on empageconf.e86_codmov = empagemov.e81_codmov ";
        $sql .= "      inner join empagemovforma on empagemovforma.e97_codmov = empagemov.e81_codmov ";
        $sql .= "      left  join empageconfgera on empageconfgera.e90_codmov = empagemov.e81_codmov ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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

    public function sql_query_conf($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
        $sql .= "      left join empagepag  on  empagepag.e85_codmov = empagemov.e81_codmov";
        $sql .= "      left join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
        $sql .= "      inner join empageconf  on empageconf.e86_codmov = empagemov.e81_codmov";
        $sql .= "      left join empageconfche  on  e91_codmov = empagemov.e81_codmov and e91_ativo is true ";
        $sql .= "      left join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
        $sql .= "      left  join empord  on  empord.e82_codmov = empagemov.e81_codmov";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
        $sql .= "      left join saltes  on  saltes.k13_conta = empagetipo.e83_conta";
        $sql .= "      left  join corconf  on  corconf.k12_codmov = empageconfche.e91_codcheque and corconf.k12_ativo is true ";
        $sql .= "      left  join corempagemov  on  corempagemov.k12_codmov = empagemov.e81_codmov ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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

    public function sql_query_consemp($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empage                      on empage.e80_codage = empagemov.e81_codage ";
        $sql .= "      inner join empord                      on empord.e82_codmov = empagemov.e81_codmov ";
        $sql .= "      inner join empempenho                  on empempenho.e60_numemp = empagemov.e81_numemp ";
        $sql .= "      left  join empagemovforma              on empagemovforma.e97_codmov = empagemov.e81_codmov ";
        $sql .= "      left  join empageforma                 on empageforma.e96_codigo = empagemovforma.e97_codforma ";
        $sql .= "      left  join empagepag                   on empagepag.e85_codmov = empagemov.e81_codmov ";
        $sql .= "      left  join empagetipo                  on empagetipo.e83_codtipo = empagepag.e85_codtipo ";
        $sql .= "      left  join empageconf                  on empageconf.e86_codmov = empagemov.e81_codmov ";
        $sql .= "      left  join empageconfgera              on empageconfgera.e90_codmov = empagemov.e81_codmov ";
        $sql .= "                                            and empageconfgera.e90_cancelado is false            ";
        $sql .= "      left  join saltes                      on saltes.k13_conta = empagetipo.e83_conta";
        $sql .= "      left  join empagedadosretmov           on empagedadosretmov.e76_codmov = empagemov.e81_codmov ";
        $sql .= "      left  join empagedadosret              on empagedadosret.e75_codret = empagedadosretmov.e76_codret ";
        $sql .= "                                            and empagedadosret.e75_ativo is true                     ";
        $sql .= "      left  join empagegera                  on empagegera.e87_codgera = empagedadosret.e75_codgera ";
        $sql .= "      left  join empagedadosretmovocorrencia on empagedadosretmovocorrencia.e02_empagedadosretmov = empagedadosretmov.e76_codmov ";
        $sql .= "                                            and empagedadosretmovocorrencia.e02_empagedadosret    = empagedadosretmov.e76_codret";
        $sql .= "      left  join errobanco                   on errobanco.e92_sequencia = empagedadosretmovocorrencia.e02_errobanco ";
        $sql .= "      left  join empageconfche               on empageconfche.e91_codmov = empagemov.e81_codmov";
        $sql .= "      left  join corconf                     on corconf.k12_codmov = empageconfche.e91_codcheque and corconf.k12_ativo is true";
        $sql .= "      left  join corempagemov                on corempagemov.k12_codmov = empagemov.e81_codmov";
        $sql .= "      left  join pagordemele                 on e53_codord = empord.e82_codord ";
        $sql .= "      left  join empagenotasordem            on e43_empagemov = e81_codmov";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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

    public function sql_query_emp($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
        $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
        $sql .= "      inner join empord  on  empord.e82_codmov = empagemov.e81_codmov";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
        $sql .= "      inner join empagepag  on  empagepag.e85_codmov = empagemov.e81_codmov";
        $sql .= "      inner join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
        $sql .= "      left  join empageconf on empageconf.e86_codmov = empagemov.e81_codmov ";
        $sql .= "      left  join pagordem       on e82_codord               = e50_codord";
        $sql .= "      left  join pagordemele    on e50_codord               = e53_codord";
        $sql .= "      left join saltes  on  saltes.k13_conta = empagetipo.e83_conta";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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

    public function sql_query_file($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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

    public function sql_query_gera($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empage  		on empage.e80_codage = empagemov.e81_codage";
        $sql .= "      inner join empagepag  	on empagepag.e85_codmov = empagemov.e81_codmov";
        $sql .= "      inner join empagetipo  	on empagetipo.e83_codtipo = empagepag.e85_codtipo";
        $sql .= "      inner join empageconf  on empageconf.e86_codmov = empagemov.e81_codmov ";
        $sql .= "      left  join empageconfgera on empageconfgera.e90_codmov = empagemov.e81_codmov ";
        $sql .= "      inner join empempenho  	on empempenho.e60_numemp = empagemov.e81_numemp";
        $sql .= "      left  join empord  		on empord.e82_codmov = empagemov.e81_codmov";
        $sql .= "      inner join cgm  		on cgm.z01_numcgm = empempenho.e60_numcgm";
        $sql .= "      inner join saltes  		on saltes.k13_conta = empagetipo.e83_conta";
        $sql .= "      inner join empagegera	on empagegera.e87_codgera = empageconfgera.e90_codgera";
        $sql .= "      inner join pagordemele 	on pagordemele.e53_codord = empord.e82_codord";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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

    public function sql_query_lay($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
        $sql .= "      inner join empagepag  on  empagepag.e85_codmov = empagemov.e81_codmov";
        $sql .= "      inner join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
        $sql .= "      inner join empageconf  on empageconf.e86_codmov = empagemov.e81_codmov";
        $sql .= "      left  join empageconfgera on empageconfgera.e90_codmov = empagemov.e81_codmov ";
        $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
        $sql .= "      left  join empord  on  empord.e82_codmov = empagemov.e81_codmov";
        $sql .= "      left  join pagordemele on pagordemele.e53_codord = empord.e82_codord ";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
        $sql .= "      inner join saltes  on  saltes.k13_conta = empagetipo.e83_conta";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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

    public function sql_query_ord($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empage					on  empage.e80_codage = empagemov.e81_codage";
        $sql .= "      inner join empord					on  empord.e82_codmov = empagemov.e81_codmov";
        $sql .= "      left  join empageconf     on empageconf.e86_codmov    = empagemov.e81_codmov";
        $sql .= "      left  join empageconfgera on empageconfgera.e90_codmov = empagemov.e81_codmov ";
        $sql .= "      left  join empageconfche  on empageconfche.e91_codmov = empagemov.e81_codmov";
        $sql .= "      left  join pagordem       on e82_codord               = e50_codord";
        $sql .= "      left  join pagordemele    on e50_codord               = e53_codord";
//     $sql .= "      left join empageconf    on empageconf.e86_codmov    = empageconfche.e91_codcheque";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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

    public function sql_query_slip($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage ";
        $sql .= "      inner join empageslip  on  empageslip.e89_codmov = empagemov.e81_codmov";
        $sql .= "      left  join empageconf on empageconf.e86_codmov = empagemov.e81_codmov ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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

    public function sql_query_tipo($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage ";
        $sql .= "      inner join empagepag  on  empagepag.e85_codmov = empagemov.e81_codmov";
        $sql .= "      inner join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
        $sql .= "      inner join db_config  on  db_config.codigo = empage.e80_instit";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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

    public function sql_query_txt($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empempenho      on empempenho.e60_numemp      = empagemov.e81_numemp ";
        $sql .= "      inner join empagemovforma  on empagemovforma.e97_codmov  = empagemov.e81_codmov ";
        $sql .= "      inner join empage          on empage.e80_codage          = empagemov.e81_codage ";
        $sql .= "      inner join empagepag       on empagepag.e85_codmov       = empagemov.e81_codmov ";
        $sql .= "      inner join empagetipo      on empagetipo.e83_codtipo     = empagepag.e85_codtipo ";
        $sql .= "      inner join conplanoreduz   on conplanoreduz.c61_reduz    = empagetipo.e83_conta   ";
        $sql .= "                                and conplanoreduz.c61_anousu   = " . db_getsession("DB_anousu");
        $sql .= "                                and conplanoreduz.c61_instit   = " . db_getsession("DB_instit");
        $sql .= "      inner join conplanoconta   on conplanoconta.c63_anousu   = conplanoreduz.c61_anousu
                                              and conplanoconta.c63_reduz    = conplanoreduz.c61_reduz ";
        $sql .= "      inner join conplanocontabancaria  on conplanocontabancaria.c56_anousu = conplanoconta.c63_anousu
                                                     and conplanocontabancaria.c56_reduz  = conplanoreduz.c61_reduz ";
        $sql .= "      inner join contabancaria   on contabancaria.db83_sequencial =  conplanocontabancaria.c56_contabancaria ";
        $sql .= "      inner join empageconf      on empageconf.e86_codmov      = empagemov.e81_codmov ";
        $sql .= "      inner join orcdotacao      on orcdotacao.o58_coddot      = empempenho.e60_coddot  ";
        $sql .= "                                and orcdotacao.o58_anousu      = empempenho.e60_anousu ";
        $sql .= "      inner join orctiporec      on orctiporec.o15_codigo      = orcdotacao.o58_codigo ";
        $sql .= "      inner join empord          on empord.e82_codmov          = empagemov.e81_codmov ";
        $sql .= "      inner join pagordem        on pagordem.e50_codord        = empord.e82_codord ";
        $sql .= "      inner join pagordemele     on pagordem.e50_codord        = pagordemele.e53_codord ";
        $sql .= "      left  join empageconfgera  on empageconfgera.e90_codmov  = empagemov.e81_codmov ";
        $sql .= "      left  join empagemovconta  on empagemovconta.e98_codmov  = empagemov.e81_codmov ";
        $sql .= "      left  join pcfornecon      on pcfornecon.pc63_contabanco = empagemovconta.e98_contabanco ";
        $sql .= "      left  join cgm             on cgm.z01_numcgm             = pcfornecon.pc63_numcgm ";
        $sql .= "      left  join saltes          on saltes.k13_conta           = empagetipo.e83_conta ";
        $sql .= "      left  join empagedadosretmov        on  empagedadosretmov.e76_codmov = empagemov.e81_codmov ";
        $sql .= "      left  join empagemovtipotransmissao on  e25_empagemov                = empagemov.e81_codmov ";
        $sql .= "      left  join corempagemov    on  k12_codmov                = empagemov.e81_codmov ";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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

    public function sql_query_txt_pagamento($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empagemovforma  on  empagemovforma.e97_codmov = empagemov.e81_codmov ";
        $sql .= "      inner join empage          on  empage.e80_codage         = empagemov.e81_codage ";
        $sql .= "      inner join empagepag       on  empagepag.e85_codmov      = empagemov.e81_codmov ";
        $sql .= "      inner join empagetipo      on  empagetipo.e83_codtipo    = empagepag.e85_codtipo ";
        $sql .= "      inner join conplanoreduz   on  conplanoreduz.c61_reduz   = empagetipo.e83_conta   ";
        $sql .= "                                and  conplanoreduz.c61_anousu  = " . db_getsession("DB_anousu");
        $sql .= "      inner join conplanoconta on conplanoconta.c63_anousu = conplanoreduz.c61_anousu
                                               and conplanoconta.c63_reduz = conplanoreduz.c61_reduz ";
        $sql .= "      inner join conplanocontabancaria  on conplanocontabancaria.c56_anousu = conplanoconta.c63_anousu
                                                     and conplanocontabancaria.c56_reduz = conplanoreduz.c61_reduz";
        $sql .= "      inner join contabancaria  on contabancaria.db83_sequencial =  conplanocontabancaria.c56_contabancaria ";
        $sql .= "      inner join empageconf     on empageconf.e86_codmov     = empagemov.e81_codmov ";
        $sql .= "      left  join empageconfgera on empageconfgera.e90_codmov = empagemov.e81_codmov ";
        $sql .= "      inner join empempenho     on empempenho.e60_numemp     = empagemov.e81_numemp ";
        $sql .= "      inner join orcdotacao     on orcdotacao.o58_coddot     = empempenho.e60_coddot  ";
        $sql .= "                               and orcdotacao.o58_anousu     = empempenho.e60_anousu ";
        $sql .= "      inner join orctiporec     on orctiporec.o15_codigo     = orcdotacao.o58_codigo ";
        $sql .= "      inner join empord         on empord.e82_codmov         = empagemov.e81_codmov ";
        $sql .= "      inner join pagordem       on pagordem.e50_codord       = empord.e82_codord ";
        $sql .= "      inner join pagordemele    on pagordem.e50_codord       = pagordemele.e53_codord ";
        $sql .= "      left  join empagemovconta on empagemovconta.e98_codmov = empagemov.e81_codmov ";
        $sql .= "      left  join pcfornecon     on pcfornecon.pc63_contabanco = empagemovconta.e98_contabanco ";
        $sql .= "      left  join cgm            on cgm.z01_numcgm             = pcfornecon.pc63_numcgm ";
        $sql .= "      left  join saltes         on saltes.k13_conta           = empagetipo.e83_conta ";
        $sql .= "      left  join empagedadosretmov        on  empagedadosretmov.e76_codmov = empagemov.e81_codmov ";
        $sql .= "      left  join empagemovtipotransmissao on  e25_empagemov                = empagemov.e81_codmov ";
        $sql .= "      left  join corempagemov   on  k12_codmov               = empagemov.e81_codmov ";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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

    public function sql_query_corgrupo($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
        $sql .= "      inner join empagepag  on  empagepag.e85_codmov = empagemov.e81_codmov";
        $sql .= "      inner join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
        $sql .= "      inner join empageconf  on empageconf.e86_codmov = empagemov.e81_codmov";
        $sql .= "      left join empageconfche  on  e91_codmov = empagemov.e81_codmov and e91_ativo is true";
        $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
        $sql .= "      left  join empord  on  empord.e82_codmov = empagemov.e81_codmov
                    left join corempagemovpagamento on k12_codmov = e82_codmov
     ";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
        $sql .= "      left join saltes  on  saltes.k13_conta = empagetipo.e83_conta";
        $sql .= "      left  join corconf  on  corconf.k12_codmov = empageconfche.e91_codcheque and k12_ativo is true";
        $sql .= "      left join corgrupocorrente on k105_data   = corempagemovpagamento.k12_data ";
        $sql .= "                                    and k105_id     = corempagemovpagamento.k12_id ";
        $sql .= "                                    and k105_autent = corempagemovpagamento.k12_autent ";
        $sql .= "       left join corrente        on k105_data   = corrente.k12_data ";
        $sql .= "                                and k105_id     = corrente.k12_id ";
        $sql .= "                                and k105_autent = corrente.k12_autent ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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


    public function sql_query_corgrupo_retencoes($e81_codmov = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from empagemov ";
        $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
        $sql .= "      left join empagepag  on  empagepag.e85_codmov = empagemov.e81_codmov";
        $sql .= "      left join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
        $sql .= "      inner join empageconf  on empageconf.e86_codmov = empagemov.e81_codmov";
        $sql .= "      left join empageconfche  on  e91_codmov = empagemov.e81_codmov and e91_ativo is true";
        $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
        $sql .= "      left  join empord  on  empord.e82_codmov = empagemov.e81_codmov";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
        $sql .= "      left join saltes  on  saltes.k13_conta = empagetipo.e83_conta";
        $sql .= "      left  join corconf  on  corconf.k12_codmov = empageconfche.e91_codcheque and k12_ativo is true";
        $sql .= "      left  join corempagemov  on  corempagemov.k12_codmov = empagemov.e81_codmov ";
        $sql .= "      left join corgrupocorrente on k105_data   = corempagemov.k12_data ";
        $sql .= "                                    and k105_id     = corempagemov.k12_id ";
        $sql .= "                                    and k105_autent = corempagemov.k12_autent ";
        $sql .= "      left join retencaocorgrupocorrente on k105_sequencial = e47_corgrupocorrente";
        $sql .= "      left join retencaoreceitas on e47_retencaoreceita = e23_sequencial";
        $sql .= "                                    and k105_id     = corempagemov.k12_id ";
        $sql .= "                                    and k105_autent = corempagemov.k12_autent ";
        $sql .= "       left join corrente        on k105_data   = corrente.k12_data ";
        $sql .= "                                and k105_id     = corrente.k12_id ";
        $sql .= "                                and k105_autent = corrente.k12_autent ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($e81_codmov != null) {
                $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
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

    public function sql_query_classificacaocredores($sCampos, $sWhere = null, $sOrder = null)
    {

        $sSql = "select {$sCampos} ";
        $sSql .= "from empagemov ";
        $sSql .= "     inner join empord                       on e82_codmov      = e81_codmov ";
        $sSql .= "     inner join pagordem                     on e82_codord      = e50_codord ";
        $sSql .= "     inner join pagordemnota                 on e50_codord      = e71_codord ";
        $sSql .= "     inner join empnota                      on e71_codnota     = e69_codnota ";
        $sSql .= "     inner join empempenho                   on e69_numemp      = e60_numemp ";
        $sSql .= "     left  join classificacaocredoresempenho on cc31_empempenho = e69_numemp ";
        $sSql .= "     left  join empagemovjustificativa       on e09_codmov      = e81_codmov ";

        if (!empty($sWhere)) {
            $sSql .= " where {$sWhere} ";
        }

        if (!empty($sOrder)) {
            $sSql .= " order by {$sOrder} ";
        }

        return $sSql;
    }

    public function sql_query_movimentos_atualizados_nao_pagos($sCampos = "*", $sWhere = null, $sOrder = null)
    {

        $sSql = " select {$sCampos} ";
        $sSql .= " from empagemov ";
        $sSql .= "      left  join corempagemov on e81_codmov = k12_codmov ";
        $sSql .= "      inner join empageconf   on e81_codmov = e86_codmov ";
        $sSql .= "      inner join empempenho   on e81_numemp = e60_numemp ";
        $sSql .= "      inner join orcdotacao   on (e60_anousu, e60_coddot) = (o58_anousu, o58_coddot) ";

        if (!empty($sWhere)) {
            $sSql .= " where {$sWhere} ";
        }

        if (!empty($sOrder)) {
            $sSql .= " order by {$sOrder} ";
        }

        return $sSql;
    }

    /**
     * @param string $sCampos
     * @param null $sWhere
     *
     * @return string
     */
    public function sql_query_movimentos_desconto($sCampos = "*", $sWhere = null)
    {

        $sSql = "select {$sCampos} ";
        $sSql .= "  from empagemov";
        $sSql .= "       inner join empord          on e82_codmov    = e81_codmov";
        $sSql .= "       left join empagepag        on e81_codmov    = e85_codmov";
        $sSql .= "       left join empagemovforma   on e97_codmov    = e81_codmov";
        $sSql .= "       left join empagenotasordem on e43_empagemov = e81_codmov";
        $sSql .= "       left join corempagemov     on k12_codmov    = e81_codmov";

        if (!empty($sWhere)) {
            $sSql .= " where {$sWhere} ";
        }
        return $sSql;
    }

    /**
     *    Metodo para retornar o recurso do movimento da agenda
     *
     * @param int $movimento codigo do movimento da agenda
     * @return string select para buscar o recurso
     *
     */
    public function sql_query_dados_recurso($movimento)
    {

        $ano = db_getsession('DB_anousu');
        $sql = " select case ";
        $sql .= "            when empresto.e91_numemp is not null then e91_recurso ";
        $sql .= "            when empempenho.e60_numemp is not null";
        $sql .= "              then o58_codigo";
        $sql .= "            else";
        $sql .= "              case";
        $sql .= "                 when sliptipooperacaovinculo.k153_slipoperacaotipo in (1, 2, 9, 10, 13, 14)";
        $sql .= "                   then ( select k181_recursodebito ";
        $sql .= "                         from sliprecursocontas     ";
        $sql .= "                        where k181_slip = k17_codigo limit 1)";
        $sql .= "                 when sliptipooperacaovinculo.k153_slipoperacaotipo in (3, 4, 7, 8, 11, 12)";
        $sql .= "                   then ( select k181_recursocredito
                                            from sliprecursocontas
                                           where k181_slip = k17_codigo limit 1)
                                 else 10000 ";
        $sql .= "              end";
        $sql .= "          end as recurso ";
        $sql .= "  from  empagemov ";
        $sql .= "        left join empempenho on e81_numemp  = e60_numemp ";
        $sql .= "        left join empresto   on e81_numemp  = e91_numemp ";
        $sql .= "                            and e91_anousu  = {$ano} ";
        $sql .= "        left join orcdotacao on e60_coddot  = o58_coddot ";
        $sql .= "                            and e60_anousu  = o58_anousu ";
        $sql .= "        left join empageslip on e81_codmov  = e89_codmov ";
        $sql .= "        left join slip       on e89_codigo  = k17_codigo ";
        $sql .= "        left join conplanoreduz reduzdebito on k17_debito = reduzdebito.c61_reduz ";
        $sql .= "                                           and reduzdebito.c61_anousu = {$ano}";
        $sql .= "        left join conplanoreduz reduzcredito on k17_credito  = reduzcredito.c61_reduz ";
        $sql .= "                                            and reduzcredito.c61_anousu = {$ano}";
        $sql .= "        left join sliptipooperacaovinculo on k153_slip = k17_codigo ";

        $sql .= " where e81_codmov = {$movimento} ";

        return $sql;
    }
}
