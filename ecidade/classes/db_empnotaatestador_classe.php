<?php

class cl_empnotaatestador
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
    public $e169_sequencial = 0;
    public $e169_empnota = null;
    public $e169_numcgm = null;

    // cria propriedade com as variaveis do arquivo
    public $campos = "
        e169_sequencial = int4 = Código
        e169_empnota = int4 = Código da empnota
        e169_numcgm = int4 = Código do cgm
    ";

    public function __construct()
    {
        $this->rotulo = new rotulo("empnotaatestador");
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
            $this->e169_sequencial = ($this->e169_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["e169_sequencial"] : $this->e169_sequencial);
            $this->e169_empnota = ($this->e169_empnota == "" ? @$GLOBALS["HTTP_POST_VARS"]["e169_empnota"] : $this->e169_empnota);
            $this->e169_numcgm = ($this->e169_numcgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["e169_numcgm"] : $this->e169_numcgm);
        } else {
            $this->e169_sequencial = ($this->e169_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["e169_sequencial"] : $this->e169_sequencial);
        }
    }

    public function incluir($e169_sequencial)
    {
        $this->atualizacampos();

        if ($this->e169_empnota == null) {
            $this->erro_sql = " Campo Código da Nota não informado.";
            $this->erro_campo = "e169_empnota";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }

        if ($this->e169_numcgm == null) {
            $this->erro_sql = " Campo Código do cgm não informado.";
            $this->erro_campo = "e169_numcgm";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }

        if ($e169_sequencial == "" || $e169_sequencial == null) {
            $result = db_query("select nextval('empnotaatestador_e169_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: empnotaatestador_e169_sequencial_seq do campo: e169_sequencial";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->e169_sequencial = pg_fetch_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from empnotaatestador_e169_sequencial_seq");
            if (($result != false) && (pg_fetch_result($result, 0, 0) < $e169_sequencial)) {
                $this->erro_sql = " Campo e169_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->e169_sequencial = $e169_sequencial;
            }
        }
        if (($this->e169_sequencial == null) || ($this->e169_sequencial == "")) {
            $this->erro_sql = " Campo e169_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }

        $sql = "
        insert into empnotaatestador (
            e169_sequencial,
            e169_empnota,
            e169_numcgm)
        values (
            $this->e169_sequencial,
            $this->e169_empnota,
            $this->e169_numcgm
        )";

        $result = db_query($sql);

        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Tabela ($this->e169_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Tabela já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql   = "Tabela ($this->e169_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->e169_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);

        return true;
    }

    public function alterar($e169_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update empnotaatestador set ";
        $virgula = "";
        if (trim($this->e169_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e169_sequencial"])) {
            $sql  .= $virgula . " e169_sequencial = $this->e169_sequencial ";
            $virgula = ",";
            if (trim($this->e169_sequencial) == null) {
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "e169_sequencial";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e169_empnota) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e169_empnota"])) {
            $sql  .= $virgula . " e169_empnota = '$this->e169_empnota' ";
            $virgula = ",";
            if (trim($this->e169_empnota) == null) {
                $this->erro_sql = " Campo Código da Nota não informado.";
                $this->erro_campo = "e169_empnota";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e169_numcgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e169_numcgm"])) {
            $sql  .= $virgula . " e169_numcgm = '$this->e169_numcgm' ";
            $virgula = ",";
            if (trim($this->e169_numcgm) == null) {
                $this->erro_sql = " Campo Código do cgm não informado.";
                $this->erro_campo = "e169_numcgm";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        $sql .= " where ";
        if ($e169_sequencial != null) {
            $sql .= " e169_sequencial = $this->e169_sequencial";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Tabela não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->e169_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Tabela não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->e169_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->e169_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($e169_sequencial = null, $dbwhere = null)
    {
        $sql = " delete from empnotaatestador
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e169_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " e169_sequencial = $e169_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Tabela não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $e169_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Tabela não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $e169_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $e169_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
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
            $this->numrows    = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Erro ao selecionar os registros.";
            $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql   = "Record Vazio na Tabela:empnotaatestador";
            $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($e169_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from empnotaatestador ";
        $sql .= " inner join cgm on e169_numcgm = z01_numcgm ";
        $sql .= " inner join empnota on e169_empnota = e69_codnota ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e169_sequencial)) {
                $sql2 .= " where empnotaatestador.e169_sequencial = $e169_sequencial ";
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

    public function sql_query_file($e169_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from empnotaatestador ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e169_sequencial)) {
                $sql2 .= " where empnotaatestador.e169_sequencial = $e169_sequencial ";
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
