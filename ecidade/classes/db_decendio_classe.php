<?php

class cl_decendio
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
    public $k230_codigo = 0;
    public $k230_slip = 0;
    public $created_at = null;
    public $updated_at = null;
    public $updated_at_hora = '00:00';
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 k230_codigo = int4 = Código
                 k230_slip = int4 = Slip
                 created_at = timestamp = Criado em
                 updated_at = timestamp = Alterado em
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("decendio");
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
            $this->k230_codigo = ($this->k230_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["k230_codigo"] : $this->k230_codigo);
            $this->k230_slip = ($this->k230_slip == "" ? @$GLOBALS["HTTP_POST_VARS"]["k230_slip"] : $this->k230_slip);

            if (is_null($this->created_at)) {
                $this->created_at = date('Y-m-d H:i:s');
            }

            if (is_null($this->updated_at)) {
                $this->updated_at = date('Y-m-d H:i:s');
            }
        } else {
            $this->k230_codigo = ($this->k230_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["k230_codigo"] : $this->k230_codigo);
        }
    }

    public function incluir($k230_codigo)
    {
        $this->atualizacampos();
        if ($k230_codigo == "" || $k230_codigo == null) {
            $result = db_query("select nextval('decendio_k230_codigo_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: decendio_k230_codigo_seq do campo: k230_codigo";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->k230_codigo = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from decendio_k230_codigo_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $k230_codigo)) {
                $this->erro_sql = " Campo k230_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->k230_codigo = $k230_codigo;
            }
        }
        if (($this->k230_codigo == null) || ($this->k230_codigo == "")) {
            $this->erro_sql = " Campo k230_codigo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }

        $sql = "insert into decendio(
                                       k230_codigo
                                      ,k230_slip
                                      ,created_at
                                      ,updated_at
                       )
                values (
                                $this->k230_codigo
                               ,$this->k230_slip
                               ,'$this->created_at'
                               ,'$this->updated_at'
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Decendio ($this->k230_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Decendio já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Decendio ($this->k230_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->k230_codigo;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);

        return true;
    }

    public function alterar($k230_codigo = null)
    {
        $this->atualizacampos();
        $sql = " update decendio set ";
        $virgula = "";
        if (trim($this->k230_codigo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k230_codigo"])) {
            $sql .= $virgula . " k230_codigo = $this->k230_codigo ";
            $virgula = ",";
            if (trim($this->k230_codigo) == null) {
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "k230_codigo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k230_slip) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k230_slip"])) {
            if (trim($this->k230_slip) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k230_slip"])) {
                $this->k230_slip = "0";
            }
            $sql .= $virgula . " k230_slip = $this->k230_slip ";
            $virgula = ",";
        }

        if (!is_null($this->updated_at)) {
            $sql .= $virgula . " updated_at = '$this->updated_at' ";
            $virgula = ",";
        } else {
            $sql .= $virgula . " updated_at = null ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($k230_codigo != null) {
            $sql .= " k230_codigo = $this->k230_codigo";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Decendio não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->k230_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Decendio não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->k230_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->k230_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($k230_codigo = null, $dbwhere = null)
    {
        $sql = " delete from decendio where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k230_codigo)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " k230_codigo = $k230_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Decendio não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $k230_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Decendio não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $k230_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $k230_codigo;
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
            $this->erro_sql = "Record Vazio na Tabela:decendio";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($k230_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from decendio ";
        $sql .= "      left  join slip  on  slip.k17_codigo = decendio.k230_slip";
        $sql .= "      inner join db_config  on  db_config.codigo = slip.k17_instit";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k230_codigo)) {
                $sql2 .= " where decendio.k230_codigo = $k230_codigo ";
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

    public function sql_query_file($k230_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from decendio ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k230_codigo)) {
                $sql2 .= " where decendio.k230_codigo = $k230_codigo ";
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
