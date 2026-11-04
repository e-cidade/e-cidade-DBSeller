<?php

class cl_labrequisicao_prioridadetipo
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
    public $la70_sequencial = 0;
    public $la70_labrequisicao = 0;
    public $la70_prioridadetipo = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 la70_sequencial = int8 =
                 la70_labrequisicao = int8 = Campo identificador lab_requisicao.
                 la70_prioridadetipo = int4 = Campo identificador prioridadetipo.
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("labrequisicao_prioridadetipo");
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
            $this->la70_sequencial = ($this->la70_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["la70_sequencial"] : $this->la70_sequencial);
            $this->la70_labrequisicao = ($this->la70_labrequisicao == "" ? @$GLOBALS["HTTP_POST_VARS"]["la70_labrequisicao"] : $this->la70_labrequisicao);
            $this->la70_prioridadetipo = ($this->la70_prioridadetipo == "" ? @$GLOBALS["HTTP_POST_VARS"]["la70_prioridadetipo"] : $this->la70_prioridadetipo);
        } else {
            $this->la70_sequencial = ($this->la70_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["la70_sequencial"] : $this->la70_sequencial);
        }
    }

    public function incluir($la70_sequencial)
    {
        $this->atualizacampos();
        if ($this->la70_labrequisicao == null) {
            $this->erro_sql = " Campo Campo identificador lab_requisicao. não informado.";
            $this->erro_campo = "la70_labrequisicao";
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
        if ($this->la70_prioridadetipo == null) {
            $this->erro_sql = " Campo Campo identificador prioridadetipo. não informado.";
            $this->erro_campo = "la70_prioridadetipo";
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
        if ($la70_sequencial == "" || $la70_sequencial == null) {
            $result = db_query("select nextval('labrequisicao_prioridadetipo_la70_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: labrequisicao_prioridadetipo_la70_sequencial_seq do campo: la70_sequencial";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
            $this->la70_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from labrequisicao_prioridadetipo_la70_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $la70_sequencial)) {
                $this->erro_sql = " Campo la70_sequencial maior que último número da sequencia.";
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
                $this->la70_sequencial = $la70_sequencial;
            }
        }
        if (($this->la70_sequencial == null) || ($this->la70_sequencial == "")) {
            $this->erro_sql = " Campo la70_sequencial não declarado.";
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
        $sql = "insert into labrequisicao_prioridadetipo(
                                       la70_sequencial
                                      ,la70_labrequisicao
                                      ,la70_prioridadetipo
                       )
                values (
                                $this->la70_sequencial
                               ,$this->la70_labrequisicao
                               ,$this->la70_prioridadetipo
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Requisições com prioridade ($this->la70_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Requisições com prioridade já Cadastrado";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
            } else {
                $this->erro_sql = "Requisições com prioridade ($this->la70_sequencial) não Incluído. Inclusão Abortada.";
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
        $this->erro_sql .= "Valores : ".$this->la70_sequencial;
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

    public function alterar($la70_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update labrequisicao_prioridadetipo set ";
        $virgula = "";
        if (trim($this->la70_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la70_sequencial"])) {
            $sql .= $virgula." la70_sequencial = $this->la70_sequencial ";
            $virgula = ",";
            if (trim($this->la70_sequencial) == null) {
                $this->erro_sql = " Campo  não informado.";
                $this->erro_campo = "la70_sequencial";
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
        if (trim($this->la70_labrequisicao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la70_labrequisicao"])) {
            $sql .= $virgula." la70_labrequisicao = $this->la70_labrequisicao ";
            $virgula = ",";
            if (trim($this->la70_labrequisicao) == null) {
                $this->erro_sql = " Campo Campo identificador lab_requisicao. não informado.";
                $this->erro_campo = "la70_labrequisicao";
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
        if (trim($this->la70_prioridadetipo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la70_prioridadetipo"])) {
            $sql .= $virgula." la70_prioridadetipo = $this->la70_prioridadetipo ";
            $virgula = ",";
            if (trim($this->la70_prioridadetipo) == null) {
                $this->erro_sql = " Campo Campo identificador prioridadetipo. não informado.";
                $this->erro_campo = "la70_prioridadetipo";
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
        if ($la70_sequencial != null) {
            $sql .= " la70_sequencial = $this->la70_sequencial";
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Requisições com prioridade não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->la70_sequencial;
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
                $this->erro_sql = "Requisições com prioridade não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->la70_sequencial;
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
                $this->erro_sql .= "Valores : ".$this->la70_sequencial;
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

    public function excluir($la70_sequencial = null, $dbwhere = null)
    {
        $sql = " delete from labrequisicao_prioridadetipo
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la70_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " la70_sequencial = $la70_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Requisições com prioridade não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$la70_sequencial;
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
                $this->erro_sql = "Requisições com prioridade não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$la70_sequencial;
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
                $this->erro_sql .= "Valores : ".$la70_sequencial;
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
            $this->erro_sql = "Record Vazio na Tabela:labrequisicao_prioridadetipo";
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

    public function sql_query($la70_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from labrequisicao_prioridadetipo ";
        $sql .= "      inner join lab_requisicao  on  lab_requisicao.la22_i_codigo = labrequisicao_prioridadetipo.la70_labrequisicao";
        $sql .= "      inner join prioridadetipo  on  prioridadetipo.la69_sequencial = labrequisicao_prioridadetipo.la70_prioridadetipo";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = lab_requisicao.la22_i_usuario";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = lab_requisicao.la22_i_departamento";
        $sql .= "      inner join cgs_und  on  cgs_und.z01_i_cgsund = lab_requisicao.la22_i_cgs";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la70_sequencial)) {
                $sql2 .= " where labrequisicao_prioridadetipo.la70_sequencial = $la70_sequencial ";
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

    public function sql_query_file($la70_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from labrequisicao_prioridadetipo ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la70_sequencial)) {
                $sql2 .= " where labrequisicao_prioridadetipo.la70_sequencial = $la70_sequencial ";
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
