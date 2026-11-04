<?php

class cl_empcompetencialiquidacao
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
    public $e164_sequencial = 0;
    public $e164_codord = 0;
    public $e164_data_dia = null;
    public $e164_data_mes = null;
    public $e164_data_ano = null;
    public $e164_data = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 e164_sequencial = int4 = e164_sequencial
                 e164_codord = int4 = e164_codord
                 e164_data = date = Data da Competência
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("empcompetencialiquidacao");
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
            $this->e164_sequencial = ($this->e164_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["e164_sequencial"] : $this->e164_sequencial);
            $this->e164_codord = ($this->e164_codord == "" ? @$GLOBALS["HTTP_POST_VARS"]["e164_codord"] : $this->e164_codord);
            if ($this->e164_data == "") {
                $this->e164_data_dia = ($this->e164_data_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["e164_data_dia"] : $this->e164_data_dia);
                $this->e164_data_mes = ($this->e164_data_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["e164_data_mes"] : $this->e164_data_mes);
                $this->e164_data_ano = ($this->e164_data_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["e164_data_ano"] : $this->e164_data_ano);
                if ($this->e164_data_dia != "") {
                    $this->e164_data = $this->e164_data_ano . "-" . $this->e164_data_mes . "-" . $this->e164_data_dia;
                }
            }
        } else {
        }
    }

    public function incluir($e164_sequencial = null)
    {
        $this->atualizacampos();
        if ($this->e164_codord == null) {
            $this->erro_sql = " Campo e164_codord não informado.";
            $this->erro_campo = "e164_codord";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->e164_data == null) {
            $this->erro_sql = " Campo Data da Competência não informado.";
            $this->erro_campo = "e164_data_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($e164_sequencial == "" || $e164_sequencial == null) {
            $result = db_query("select nextval('empcompetencialiquidacao_e164_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: empcompetencialiquidacao_e164_sequencial_seq do campo: e164_sequencial";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->e164_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from empcompetencialiquidacao_e164_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $e164_sequencial)) {
                $this->erro_sql = " Campo e164_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->e164_sequencial = $e164_sequencial;
            }
        }
        $sql = "insert into empcompetencialiquidacao(
                                       e164_sequencial
                                      ,e164_codord
                                      ,e164_data
                       )
                values (
                                $this->e164_sequencial
                               ,$this->e164_codord
                               ," . ($this->e164_data == "null" || $this->e164_data == "" ? "null" : "'" . $this->e164_data . "'") . "
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Emp Competencia Liquidação () não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Emp Competencia Liquidação já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql   = "Emp Competencia Liquidação () não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
            && ($lSessaoDesativarAccount === false))) {
        }
        return true;
    }

    public function alterar($e164_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update empcompetencialiquidacao set ";
        $virgula = "";
        if (trim($this->e164_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e164_sequencial"])) {
            $sql  .= $virgula . " e164_sequencial = $this->e164_sequencial ";
            $virgula = ",";
            if (trim($this->e164_sequencial) == null) {
                $this->erro_sql = " Campo e164_sequencial não informado.";
                $this->erro_campo = "e164_sequencial";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e164_codord) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e164_codord"])) {
            $sql  .= $virgula . " e164_codord = $this->e164_codord ";
            $virgula = ",";
            if (trim($this->e164_codord) == null) {
                $this->erro_sql = " Campo e164_codord não informado.";
                $this->erro_campo = "e164_codord";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->e164_data) != "" || isset($GLOBALS["HTTP_POST_VARS"]["e164_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["e164_data_dia"] != "")) {
            $sql  .= $virgula . " e164_data = '$this->e164_data' ";
            $virgula = ",";
            if (trim($this->e164_data) == null) {
                $this->erro_sql = " Campo Data da Competência não informado.";
                $this->erro_campo = "e164_data_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["e164_data_dia"])) {
                $sql  .= $virgula . " e164_data = null ";
                $virgula = ",";
                if (trim($this->e164_data) == null) {
                    $this->erro_sql = " Campo Data da Competência não informado.";
                    $this->erro_campo = "e164_data_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        $sql .= " where ";
        $sql .= " e164_sequencial = $e164_sequencial";
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Emp Competencia Liquidação não Alterado. Alteração Abortada.\\n";
            $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Emp Competencia Liquidação não foi Alterado. Alteração Executada.\\n";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($e164_sequencial = null, $dbwhere = null)
    {
        $sql = " delete from empcompetencialiquidacao
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            $sql2 = "e164_sequencial = $e164_sequencial";
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Emp Competencia Liquidação não Excluído. Exclusão Abortada.\\n";
            $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Emp Competencia Liquidação não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
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
            $this->erro_sql   = "Record Vazio na Tabela:empcompetencialiquidacao";
            $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($e164_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql  = "select {$campos}";
        $sql .= "  from empcompetencialiquidacao ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($e164_sequencial)) {
                $sql2 = " where empcompetencialiquidacao.e164_sequencial = $e164_sequencial";
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

    public function sql_query_file($e164_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql  = "select {$campos} ";
        $sql .= "  from empcompetencialiquidacao ";
        $sql2 = "";
        if (!empty($e164_sequencial)) {
            $sql2 = "where e164_sequencial = {$e164_sequencial}";
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
