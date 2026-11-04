<?php

class cl_decendiolancamento
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
    public $k231_codigo = 0;
    public $k231_decendio = 0;
    public $k231_lancamento = 0;
    public $k231_desdobramento = 0;
    public $created_at = null;
    public $updated_at = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
        k231_codigo = int4 = Código
        k231_decendio = int4 = Decêndio
        k231_lancamento = int4 = Lançamento
        k231_desdobramento = int4 = Desdobramento
        created_at = timestamp = Criado em
        updated_at = timestamp = Alterado em
    ";

    public function __construct()
    {
        $this->rotulo = new rotulo("decendiolancamento");
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
            $this->k231_codigo = ($this->k231_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["k231_codigo"] : $this->k231_codigo);
            $this->k231_decendio = ($this->k231_decendio == "" ? @$GLOBALS["HTTP_POST_VARS"]["k231_decendio"] : $this->k231_decendio);
            $this->k231_lancamento = ($this->k231_lancamento == "" ? @$GLOBALS["HTTP_POST_VARS"]["k231_lancamento"] : $this->k231_lancamento);
            $this->k231_desdobramento = ($this->k231_desdobramento == "" ? @$GLOBALS["HTTP_POST_VARS"]["k231_desdobramento"] : $this->k231_desdobramento);
            if (is_null($this->created_at)) {
                $this->created_at = date('Y-m-d H:i:s');
            }

            if (is_null($this->updated_at)) {
                $this->updated_at = date('Y-m-d H:i:s');
            }
        } else {
            $this->k231_codigo = ($this->k231_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["k231_codigo"] : $this->k231_codigo);
        }
    }

    public function incluir($k231_codigo)
    {
        $this->atualizacampos();
        if ($k231_codigo == "" || $k231_codigo == null) {
            $result = db_query("select nextval('decendiolancamento_k231_codigo_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: decendiolancamento_k231_codigo_seq do campo: k231_codigo";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->k231_codigo = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from decendiolancamento_k231_codigo_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $k231_codigo)) {
                $this->erro_sql = " Campo k231_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->k231_codigo = $k231_codigo;
            }
        }
        if (($this->k231_codigo == null) || ($this->k231_codigo == "")) {
            $this->erro_sql = " Campo k231_codigo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }

        $sql = "insert into decendiolancamento(
                                       k231_codigo
                                      ,k231_decendio
                                      ,k231_lancamento
                                      ,k231_desdobramento
                                      ,created_at
                                      ,updated_at
                       )
                values (
                                $this->k231_codigo
                               ,$this->k231_decendio
                               ,$this->k231_lancamento
                               ,$this->k231_desdobramento
                               ,'$this->created_at'
                               ,'$this->updated_at'
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Lancanentos presente no Decendio ($this->k231_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Lancanentos presente no Decendio já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Lancanentos presente no Decendio ($this->k231_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->k231_codigo;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);

        return true;
    }

    public function alterar($k231_codigo = null)
    {
        $this->atualizacampos();
        $sql = " update decendiolancamento set ";
        $virgula = "";
        if (trim($this->k231_codigo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k231_codigo"])) {
            $sql .= $virgula . " k231_codigo = $this->k231_codigo ";
            $virgula = ",";
            if (trim($this->k231_codigo) == null) {
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "k231_codigo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->k231_decendio) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k231_decendio"])) {
            if (trim($this->k231_decendio) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k231_decendio"])) {
                $this->k231_decendio = "0";
            }
            $sql .= $virgula . " k231_decendio = $this->k231_decendio ";
            $virgula = ",";
        }
        if (trim($this->k231_lancamento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k231_lancamento"])) {
            if (trim($this->k231_lancamento) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k231_lancamento"])) {
                $this->k231_lancamento = "0";
            }
            $sql .= $virgula . " k231_lancamento = $this->k231_lancamento ";
            $virgula = ",";
        }
        if (trim($this->k231_desdobramento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["k231_desdobramento"])) {
            if (trim($this->k231_desdobramento) == "" && isset($GLOBALS["HTTP_POST_VARS"]["k231_desdobramento"])) {
                $this->k231_desdobramento = "0";
            }
            $sql .= $virgula . " k231_desdobramento = $this->k231_desdobramento ";
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
        if ($k231_codigo != null) {
            $sql .= " k231_codigo = $this->k231_codigo";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Lancanentos presente no Decendio não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->k231_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lancanentos presente no Decendio não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->k231_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->k231_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($k231_codigo = null, $dbwhere = null)
    {
        $sql = " delete from decendiolancamento where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k231_codigo)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " k231_codigo = $k231_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Lancanentos presente no Decendio não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $k231_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lancanentos presente no Decendio não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $k231_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $k231_codigo;
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
            $this->erro_sql = "Record Vazio na Tabela:decendiolancamento";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($k231_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from decendiolancamento ";
        $sql .= "      left  join conlancam  on  conlancam.c70_codlan = decendiolancamento.k231_lancamento";
        $sql .= "      left  join decendio  on  decendio.k230_codigo = decendiolancamento.k231_decendio";
        $sql .= "      left  join tipodesdobramento  on  tipodesdobramento.k222_codigo = decendiolancamento.k231_desdobramento";
        $sql .= "      left  join slip  on  slip.k17_codigo = decendio.k230_slip";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k231_codigo)) {
                $sql2 .= " where decendiolancamento.k231_codigo = $k231_codigo ";
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

    public function sql_query_file($k231_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from decendiolancamento ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($k231_codigo)) {
                $sql2 .= " where decendiolancamento.k231_codigo = $k231_codigo ";
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
