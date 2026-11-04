<?php

class cl_prioridadetipo
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
    public $la69_sequencial = 0;
    public $la69_descricao = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 la69_sequencial = int4 = Código de identificação da prioridade.
                 la69_descricao = varchar(255) = Descrição da prioridade.
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("prioridadetipo");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert(\"".$this->erro_msg."\")</script>";
            if ($retorna == true) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->la69_sequencial = ($this->la69_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["la69_sequencial"] : $this->la69_sequencial);
            $this->la69_descricao = ($this->la69_descricao == "" ? @$GLOBALS["HTTP_POST_VARS"]["la69_descricao"] : $this->la69_descricao);
        } else {
            $this->la69_sequencial = ($this->la69_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["la69_sequencial"] : $this->la69_sequencial);
        }
    }

    public function incluir($la69_sequencial)
    {
        $this->atualizacampos();
        if ($this->la69_descricao == null) {
            $this->erro_sql = " Campo Descrição da prioridade. não informado.";
            $this->erro_campo = "la69_descricao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        if ($la69_sequencial == "" || $la69_sequencial == null) {
            $result = db_query("select nextval('prioridadetipo_la69_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: prioridadetipo_la69_sequencial_seq do campo: la69_sequencial";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
            $this->la69_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from prioridadetipo_la69_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $la69_sequencial)) {
                $this->erro_sql = " Campo la69_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            } else {
                $this->la69_sequencial = $la69_sequencial;
            }
        }
        if (($this->la69_sequencial == null) || ($this->la69_sequencial == "")) {
            $this->erro_sql = " Campo la69_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into prioridadetipo(
                                       la69_sequencial
                                      ,la69_descricao
                       )
                values (
                                $this->la69_sequencial
                               ,'$this->la69_descricao'
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Tabela de prioridades. ($this->la69_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Tabela de prioridades. já Cadastrado";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
            } else {
                $this->erro_sql = "Tabela de prioridades. ($this->la69_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : ".$this->la69_sequencial;
        $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg .= str_replace(
            '"',
            "",
            str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
        );
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        return true;
    }

    public function alterar($la69_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update prioridadetipo set ";
        $virgula = "";
        if (trim($this->la69_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la69_sequencial"])) {
            $sql .= $virgula." la69_sequencial = $this->la69_sequencial ";
            $virgula = ",";
            if (trim($this->la69_sequencial) == null) {
                $this->erro_sql = " Campo Código de identificação da prioridade. não informado.";
                $this->erro_campo = "la69_sequencial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->la69_descricao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la69_descricao"])) {
            $sql .= $virgula." la69_descricao = '$this->la69_descricao' ";
            $virgula = ",";
            if (trim($this->la69_descricao) == null) {
                $this->erro_sql = " Campo Descrição da prioridade. não informado.";
                $this->erro_campo = "la69_descricao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($la69_sequencial != null) {
            $sql .= " la69_sequencial = $this->la69_sequencial";
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Tabela de prioridades. não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->la69_sequencial;
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Tabela de prioridades. não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->la69_sequencial;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->la69_sequencial;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($la69_sequencial = null, $dbwhere = null)
    {
        $sql = " delete from prioridadetipo
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la69_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " la69_sequencial = $la69_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Tabela de prioridades. não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$la69_sequencial;
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Tabela de prioridades. não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$la69_sequencial;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$la69_sequencial;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
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
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:prioridadetipo";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($la69_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from prioridadetipo ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la69_sequencial)) {
                $sql2 .= " where prioridadetipo.la69_sequencial = $la69_sequencial ";
            }
        } else {
            if (!empty($dbwhere)) {
                $sql2 = " where $dbwhere";
            }
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

    public function sql_query_file($la69_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from prioridadetipo ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la69_sequencial)) {
                $sql2 .= " where prioridadetipo.la69_sequencial = $la69_sequencial ";
            }
        } else {
            if (!empty($dbwhere)) {
                $sql2 = " where $dbwhere";
            }
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

}
