<?php

class cl_processo_usuarios
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
    public $p119_codigo = 0;
    public $p119_protprocesso = 0;
    public $p119_id_usuario = 0;
    public $p119_atividadeexecucao = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 p119_codigo = int8 = Código
                 p119_protprocesso = int8 = Processo
                 p119_id_usuario = int8 = Usuário
                 p119_atividadeexecucao = int8 = Atividades de execução
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("processo_usuarios");
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
        if (!$exclusao) {
            $this->p119_codigo = ($this->p119_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["p119_codigo"] : $this->p119_codigo);
            $this->p119_protprocesso = ($this->p119_protprocesso == "" ? @$GLOBALS["HTTP_POST_VARS"]["p119_protprocesso"] : $this->p119_protprocesso);
            $this->p119_id_usuario = ($this->p119_id_usuario == "" ? @$GLOBALS["HTTP_POST_VARS"]["p119_id_usuario"] : $this->p119_id_usuario);
            $this->p119_atividadeexecucao = ($this->p119_atividadeexecucao == "" ? @$GLOBALS["HTTP_POST_VARS"]["p119_atividadeexecucao"] : $this->p119_atividadeexecucao);
        } else {
            $this->p119_codigo = ($this->p119_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["p119_codigo"] : $this->p119_codigo);
        }
    }

    public function incluir($p119_codigo)
    {
        $this->atualizacampos();
        if ($this->p119_protprocesso == null) {
            $this->erro_sql = " Campo Processo não informado.";
            $this->erro_campo = "p119_protprocesso";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->p119_id_usuario == null) {
            $this->erro_sql = " Campo Usuário não informado.";
            $this->erro_campo = "p119_id_usuario";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->p119_atividadeexecucao == null) {
            $this->erro_sql = " Campo Atividades de execução não informado.";
            $this->erro_campo = "p119_atividadeexecucao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($p119_codigo == "" || $p119_codigo == null) {
            $result = db_query("select nextval('processo_usuarios_p119_codigo_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: processo_usuarios_p119_codigo_seq do campo: p119_codigo";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->p119_codigo = pg_fetch_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from processo_usuarios_p119_codigo_seq");
            if ($result && (pg_fetch_result($result, 0, 0) < $p119_codigo)) {
                $this->erro_sql = " Campo p119_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->p119_codigo = $p119_codigo;
            }
        }
        if (($this->p119_codigo == null) || ($this->p119_codigo == "")) {
            $this->erro_sql = " Campo p119_codigo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into processo_usuarios(
                                       p119_codigo
                                      ,p119_protprocesso
                                      ,p119_id_usuario
                                      ,p119_atividadeexecucao
                       )
                values (
                                $this->p119_codigo
                               ,$this->p119_protprocesso
                               ,$this->p119_id_usuario
                               ,$this->p119_atividadeexecucao
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Usuários permitidos ($this->p119_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Usuários permitidos já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Usuários permitidos ($this->p119_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->p119_codigo;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        return true;
    }

    public function alterar($p119_codigo = null)
    {
        $this->atualizacampos();
        $sql = " update processo_usuarios set ";
        $virgula = "";
        if (trim($this->p119_codigo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["p119_codigo"])) {
            $sql .= $virgula . " p119_codigo = $this->p119_codigo ";
            $virgula = ",";
            if (trim($this->p119_codigo) == null) {
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "p119_codigo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->p119_protprocesso) != "" || isset($GLOBALS["HTTP_POST_VARS"]["p119_protprocesso"])) {
            $sql .= $virgula . " p119_protprocesso = $this->p119_protprocesso ";
            $virgula = ",";
            if (trim($this->p119_protprocesso) == null) {
                $this->erro_sql = " Campo Processo não informado.";
                $this->erro_campo = "p119_protprocesso";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->p119_id_usuario) != "" || isset($GLOBALS["HTTP_POST_VARS"]["p119_id_usuario"])) {
            $sql .= $virgula . " p119_id_usuario = $this->p119_id_usuario ";
            $virgula = ",";
            if (trim($this->p119_id_usuario) == null) {
                $this->erro_sql = " Campo Usuário não informado.";
                $this->erro_campo = "p119_id_usuario";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->p119_atividadeexecucao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["p119_atividadeexecucao"])) {
            $sql .= $virgula . " p119_atividadeexecucao = $this->p119_atividadeexecucao ";
            $virgula = ",";
            if (trim($this->p119_atividadeexecucao) == null) {
                $this->erro_sql = " Campo Atividades de execução não informado.";
                $this->erro_campo = "p119_atividadeexecucao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($p119_codigo != null) {
            $sql .= " p119_codigo = $this->p119_codigo";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Usuários permitidos não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->p119_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Usuários permitidos não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->p119_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->p119_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($p119_codigo = null, $dbwhere = null)
    {
        $sql = " delete from processo_usuarios
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($p119_codigo)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " p119_codigo = $p119_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Usuários permitidos não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $p119_codigo;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Usuários permitidos não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $p119_codigo;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $p119_codigo;
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
            $this->erro_sql = "Record Vazio na Tabela:processo_usuarios";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($p119_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}
          from processo_usuarios
              inner join db_usuarios
                  on  db_usuarios.id_usuario = processo_usuarios.p119_id_usuario
              inner join protprocesso
                  on  protprocesso.p58_codproc = processo_usuarios.p119_protprocesso
              inner join atividadesexecucao
                  on  atividadesexecucao.p114_codigo = processo_usuarios.p119_atividadeexecucao
              inner join cgm
                  on  cgm.z01_numcgm = protprocesso.p58_numcgm
              inner join db_config
                  on  db_config.codigo = protprocesso.p58_instit
              inner join db_usuarios
                  on  db_usuarios.id_usuario = protprocesso.p58_id_usuario
              inner join db_depart
                  on  db_depart.coddepto = protprocesso.p58_coddepto
              inner join tipoproc
                  on  tipoproc.p51_codigo = protprocesso.p58_codigo
              inner join tipoprocesso
                  on  tipoprocesso.p109_sequencial = protprocesso.p58_tipoprocesso
        ";

        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($p119_codigo)) {
                $sql2 .= " where processo_usuarios.p119_codigo = $p119_codigo ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

    public function sql_query_file($p119_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from processo_usuarios ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($p119_codigo)) {
                $sql2 .= " where processo_usuarios.p119_codigo = $p119_codigo ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }
}
