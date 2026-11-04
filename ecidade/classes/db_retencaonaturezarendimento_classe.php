<?php

class cl_retencaonaturezarendimento
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
    public $e168_sequencial = 0;
    public $e168_retencaoreceitas = 0;
    public $e168_naturezarendimento = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 e168_sequencial = int4 = e168_sequencial
                 e168_retencaoreceitas = int4 = e168_retencaoreceitas
                 e168_naturezarendimento = int4 = e168_naturezarendimento
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("retencaonaturezarendimento");
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
            $this->e168_sequencial = ($this->e168_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["e168_sequencial"] : $this->e168_sequencial);
            $this->e168_retencaoreceitas = ($this->e168_retencaoreceitas == "" ? @$GLOBALS["HTTP_POST_VARS"]["e168_retencaoreceitas"] : $this->e168_retencaoreceitas);
            $this->e168_naturezarendimento = ($this->e168_naturezarendimento == "" ? @$GLOBALS["HTTP_POST_VARS"]["e168_naturezarendimento"] : $this->e168_naturezarendimento);
        } else {
            $this->e168_sequencial = ($this->e168_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["e168_sequencial"] : $this->e168_sequencial);
        }
    }

    public function incluir($e168_sequencial)
    {
        $this->atualizacampos();
        if ($this->e168_retencaoreceitas == null) {
            $this->erro_sql = " Campo e168_retencaoreceitas não informado.";
            $this->erro_campo = "e168_retencaoreceitas";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e168_naturezarendimento == null) {
            $this->erro_sql = " Campo e168_naturezarendimento não informado.";
            $this->erro_campo = "e168_naturezarendimento";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($e168_sequencial == "" || $e168_sequencial == null) {
            $result = db_query("select nextval('retencaonaturezarendimento_e168_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: retencaonaturezarendimento_e168_sequencial_seq do campo: e168_sequencial";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->e168_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from retencaonaturezarendimento_e168_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $e168_sequencial)) {
                $this->erro_sql = " Campo e168_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->e168_sequencial = $e168_sequencial;
            }
        }
        if (($this->e168_sequencial == null) || ($this->e168_sequencial == "")) {
            $this->erro_sql = " Campo e168_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into retencaonaturezarendimento(
                                       e168_sequencial
                                      ,e168_retencaoreceitas
                                      ,e168_naturezarendimento
                       )
                values (
                                $this->e168_sequencial
                               ,$this->e168_retencaoreceitas
                               ,$this->e168_naturezarendimento
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Tabela ($this->e168_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Tabela já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Tabela ($this->e168_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->e168_sequencial;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        return true;
    }

    public function alterar($e168_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update retencaonaturezarendimento set ";
        $virgula = "";
        if (trim($this->e168_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e168_sequencial"])) {
            $sql .= $virgula . " e168_sequencial = $this->e168_sequencial ";
            $virgula = ",";
            if (trim($this->e168_sequencial) == null) {
                $this->erro_sql = " Campo e168_sequencial não informado.";
                $this->erro_campo = "e168_sequencial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e168_retencaoreceitas) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e168_retencaoreceitas"])) {
            $sql .= $virgula . " e168_retencaoreceitas = $this->e168_retencaoreceitas ";
            $virgula = ",";
            if (trim($this->e168_retencaoreceitas) == null) {
                $this->erro_sql = " Campo e168_retencaoreceitas não informado.";
                $this->erro_campo = "e168_retencaoreceitas";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e168_naturezarendimento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e168_naturezarendimento"])) {
            $sql .= $virgula . " e168_naturezarendimento = $this->e168_naturezarendimento ";
            $virgula = ",";
            if (trim($this->e168_naturezarendimento) == null) {
                $this->erro_sql = " Campo e168_naturezarendimento não informado.";
                $this->erro_campo = "e168_naturezarendimento";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($e168_sequencial != null) {
            $sql .= " e168_sequencial = $this->e168_sequencial";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Tabela não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->e168_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Tabela não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->e168_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->e168_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($e168_sequencial = null, $dbwhere = null)
    {
        $sql = " delete from retencaonaturezarendimento
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e168_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " e168_sequencial = $e168_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Tabela não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $e168_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Tabela não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $e168_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $e168_sequencial;
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
            $this->erro_sql = "Record Vazio na Tabela:retencaonaturezarendimento";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($e168_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $sql .= "  from retencaonaturezarendimento ";
        $sql .= "      inner join retencaoreceitas  on  retencaoreceitas.e23_sequencial = retencaonaturezarendimento.e168_retencaoreceitas";
        $sql .= "      inner join naturezarendimento  on  naturezarendimento.e167_sequencial = retencaonaturezarendimento.e168_naturezarendimento";
        $sql .= "      inner join retencaotiporec  on  retencaotiporec.e21_sequencial = retencaoreceitas.e23_retencaotiporec";
        $sql .= "      inner join retencaopagordem  on  retencaopagordem.e20_sequencial = retencaoreceitas.e23_retencaopagordem";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e168_sequencial)) {
                $sql2 .= " where retencaonaturezarendimento.e168_sequencial = $e168_sequencial ";
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

    public function sql_query_file($e168_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from retencaonaturezarendimento ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e168_sequencial)) {
                $sql2 .= " where retencaonaturezarendimento.e168_sequencial = $e168_sequencial ";
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
