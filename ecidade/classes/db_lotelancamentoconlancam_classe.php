<?php

class cl_lotelancamentoconlancam
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
    public $c161_codigo = 0;
    public $c161_lotelancamento = 0;
    public $c161_conlancam = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 c161_codigo = int8 =
                 c161_lotelancamento = int4 =
                 c161_conlancam = int4 =
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("lotelancamentoconlancam");
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
            $this->c161_codigo = ($this->c161_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["c161_codigo"] : $this->c161_codigo);
            $this->c161_lotelancamento = ($this->c161_lotelancamento == "" ? @$GLOBALS["HTTP_POST_VARS"]["c161_lotelancamento"] : $this->c161_lotelancamento);
            $this->c161_conlancam = ($this->c161_conlancam == "" ? @$GLOBALS["HTTP_POST_VARS"]["c161_conlancam"] : $this->c161_conlancam);
        } else {
            $this->c161_codigo = ($this->c161_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["c161_codigo"] : $this->c161_codigo);
        }
    }

    public function incluir($c161_codigo)
    {
        $this->atualizacampos();
        if ($this->c161_lotelancamento == null) {
            $this->erro_sql = " Campo  não informado.";
            $this->erro_campo = "c161_lotelancamento";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->c161_conlancam == null) {
            $this->erro_sql = " Campo  não informado.";
            $this->erro_campo = "c161_conlancam";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($c161_codigo == "" || $c161_codigo == null) {
            $result = db_query("select nextval('lotelancamentoconlancam_c161_codigo_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: lotelancamentoconlancam_c161_codigo_seq do campo: c161_codigo";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->c161_codigo = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from lotelancamentoconlancam_c161_codigo_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $c161_codigo)) {
                $this->erro_sql = " Campo c161_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->c161_codigo = $c161_codigo;
            }
        }
        if (($this->c161_codigo == null) || ($this->c161_codigo == "")) {
            $this->erro_sql = " Campo c161_codigo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into lotelancamentoconlancam(
                                       c161_codigo
                                      ,c161_lotelancamento
                                      ,c161_conlancam
                       )
                values (
                                $this->c161_codigo
                               ,$this->c161_lotelancamento
                               ,$this->c161_conlancam
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Lan?amentos do lote ($this->c161_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Lan?amentos do lote já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Lan?amentos do lote ($this->c161_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->c161_codigo;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        return true;
    }

    public function alterar($c161_codigo = null)
    {
        $this->atualizacampos();
        $sql = " update lotelancamentoconlancam set ";
        $virgula = "";
        if (trim($this->c161_codigo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c161_codigo"])) {
            $sql .= $virgula . " c161_codigo = $this->c161_codigo ";
            $virgula = ",";
            if (trim($this->c161_codigo) == null) {
                $this->erro_sql = " Campo  não informado.";
                $this->erro_campo = "c161_codigo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c161_lotelancamento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c161_lotelancamento"])) {
            $sql .= $virgula . " c161_lotelancamento = $this->c161_lotelancamento ";
            $virgula = ",";
            if (trim($this->c161_lotelancamento) == null) {
                $this->erro_sql = " Campo  não informado.";
                $this->erro_campo = "c161_lotelancamento";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->c161_conlancam) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c161_conlancam"])) {
            $sql .= $virgula . " c161_conlancam = $this->c161_conlancam ";
            $virgula = ",";
            if (trim($this->c161_conlancam) == null) {
                $this->erro_sql = " Campo  não informado.";
                $this->erro_campo = "c161_conlancam";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($c161_codigo != null) {
            $sql .= " c161_codigo = $this->c161_codigo";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Lan?amentos do lote não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->c161_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lan?amentos do lote não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->c161_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->c161_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($c161_codigo = null, $dbwhere = null)
    {
        $sql = " delete from lotelancamentoconlancam where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c161_codigo)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " c161_codigo = $c161_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Lan?amentos do lote não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $c161_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lan?amentos do lote não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $c161_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $c161_codigo;
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
            $this->erro_sql = "Record Vazio na Tabela:lotelancamentoconlancam";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($c161_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from lotelancamentoconlancam ";
        $sql .= "      inner join conlancam  on  conlancam.c70_codlan = lotelancamentoconlancam.c161_conlancam";
        $sql .= "      inner join lotelancamentos  on  lotelancamentos.c160_codigo = lotelancamentoconlancam.c161_lotelancamento";
        $sql .= "      inner join db_config  on  db_config.codigo = lotelancamentos.c160_instituicao";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c161_codigo)) {
                $sql2 .= " where lotelancamentoconlancam.c161_codigo = $c161_codigo ";
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

    public function sql_query_file($c161_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from lotelancamentoconlancam ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c161_codigo)) {
                $sql2 .= " where lotelancamentoconlancam.c161_codigo = $c161_codigo ";
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
